<?php

namespace App\Http\Controllers\Elite;

use App\Http\Controllers\Controller;
use App\Models\EliteEmail;
use App\Services\EducationEliteInboxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EliteEmailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->only(['index', 'inbox']);
    }

    public function index()
    {
        $service = EducationEliteInboxService::make();
        $items = $service->getInbox('', '', '', 'newest', 200, 'inbox', null);

        $webhookUrl = $this->inboundWebhookUrl();

        return view('elite.emails-inbox', [
            'eliteInboxItems' => $items,
            'eliteInitialFolder' => 'inbox',
            'eliteInitialAccount' => 'all',
            'eliteMailboxes' => $service->listMailboxes(),
            'webhookUrl' => $webhookUrl,
        ]);
    }

    /**
     * JSON list for the Elite inbox (SendGrid Inbound Parse / elite_emails only).
     * folder is always treated as 'inbox'; 'sent' returns [] immediately in the service.
     */
    public function inbox(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        $sort = $request->get('sort', 'newest');
        if (! in_array($sort, ['newest', 'oldest'], true)) {
            $sort = 'newest';
        }
        $accountRaw = (string) $request->get('account', 'all');
        $account = ($accountRaw === 'all' || $accountRaw === '') ? null : $accountRaw;

        $service = EducationEliteInboxService::make();
        $emails = $service->getInbox($search, $dateFrom, $dateTo, $sort, 500, 'inbox', $account);
        $normalized = $service->normalizeAccountFilter($account);

        return response()->json([
            'emails' => $emails,
            'folder' => 'inbox',
            'account' => $normalized ?? 'all',
            'accounts' => $service->listMailboxes(),
            'message' => $service->emptyListMessage('inbox'),
        ]);
    }

    /**
     * SendGrid Inbound Parse (and other providers): POST multipart, no CSRF.
     */
    public function store(Request $request): JsonResponse
    {
        $this->assertInboundSecret($request);

        return $this->persistInbound($request);
    }

    private function assertInboundSecret(Request $request): void
    {
        $secret = (string) config('crm.education_elite_inbound_secret', '');
        if ($secret === '') {
            return;
        }

        $given = (string) ($request->query('secret', $request->header('X-Elite-Webhook-Secret', '')));

        if (! hash_equals($secret, $given)) {
            Log::warning('elite.emails.forbidden', ['ip' => $request->ip()]);
            abort(403, 'Invalid inbound secret');
        }
    }

    private function persistInbound(Request $request): JsonResponse
    {
        $fromAddress = $this->resolveFromAddress($request);
        $fromRaw = $request->input('from')
            ?? $request->input('sender')
            ?? $request->input('envelope');

        if (! $fromAddress || ! $this->isEliteSender($fromAddress)) {
            Log::warning('elite.emails.rejected', ['from' => $fromRaw, 'ip' => $request->ip()]);

            $msg = 'Sender must be an @'.config('crm.education_elite_sender_domain', 'educationelite.com.au').' address.';

            return response()->json(['ok' => false, 'error' => $msg], 422);
        }

        $subject = $request->input('subject') ?? $request->input('Subject');
        $to = $request->input('to') ?? $request->input('recipient') ?? $request->input('To');
        $text = $request->input('text') ?? $request->input('body_text') ?? $request->input('plain');
        $html = $request->input('html') ?? $request->input('body_html') ?? $request->input('body');

        if (is_string($html) && $this->looksLikePlainText($html) && $text === null) {
            $text = $html;
            $html = null;
        }

        $payload = $this->compactPayload($request);

        $record = EliteEmail::create([
            'from_address' => $fromAddress,
            'to_address' => is_string($to) ? substr($to, 0, 255) : null,
            'subject' => is_string($subject) ? substr($subject, 0, 998) : null,
            'body_text' => is_string($text) ? $text : null,
            'body_html' => is_string($html) ? $html : null,
            'payload' => $payload,
        ]);

        return response()->json(['ok' => true, 'id' => $record->id]);
    }

    /**
     * SendGrid sends `envelope` as a JSON string: {"to":["…"],"from":"…"}.
     */
    private function resolveFromAddress(Request $request): ?string
    {
        $headerCandidates = [
            $request->input('from'),
            $request->input('sender'),
            $request->input('from_email'),
            $request->input('From'),
        ];

        foreach ($headerCandidates as $c) {
            if ($c !== null && $c !== '') {
                $parsed = $this->parseEmailAddress((string) $c);
                if ($parsed) {
                    return $parsed;
                }
            }
        }

        $envelope = $request->input('envelope');
        if (is_string($envelope)) {
            $decoded = json_decode($envelope, true);
            $envelope = is_array($decoded) ? $decoded : null;
        }
        if (is_array($envelope) && ! empty($envelope['from'])) {
            return $this->parseEmailAddress((string) $envelope['from']);
        }

        return null;
    }

    /**
     * Drop huge SendGrid fields (raw MIME) from stored JSON; keep metadata only.
     */
    private function compactPayload(Request $request): ?array
    {
        $payload = $request->except(['_token']);
        foreach (['email', 'html', 'text', 'content-id_map'] as $key) {
            unset($payload[$key]);
        }

        if (count($payload) > 60) {
            $payload = array_slice($payload, 0, 60, true);
        }

        return $payload === [] ? null : $payload;
    }

    private function looksLikePlainText(string $html): bool
    {
        return ! preg_match('/<[a-z][\s\S]*>/i', $html);
    }

    private function inboundWebhookUrl(): string
    {
        $url = url('/elite/emails');
        $secret = (string) config('crm.education_elite_inbound_secret', '');
        if ($secret !== '') {
            $url .= '?secret='.urlencode($secret);
        }

        return $url;
    }

    private function parseEmailAddress(string $raw): ?string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }
        if (preg_match('/<([^>]+@[^>]+)>/', $raw, $m)) {
            $raw = trim($m[1]);
        }
        $raw = strtolower($raw);
        if (! filter_var($raw, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return $raw;
    }

    private function isEliteSender(string $email): bool
    {
        $domain = strtolower((string) config('crm.education_elite_sender_domain', 'educationelite.com.au'));
        $domain = ltrim($domain, '@');

        return str_ends_with(strtolower($email), '@'.$domain);
    }
}
