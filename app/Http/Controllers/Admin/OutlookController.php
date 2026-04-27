<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Models\OutlookDraftEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer as LaravelMailer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OutlookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the Outlook compose page.
     * Fetches verified senders from SendGrid API so the From dropdown updates automatically.
     */
    public function index()
    {
        $verifiedSenders = $this->getVerifiedSenders();
        $senderEmails = array_column($verifiedSenders, 'email');
        $fromEmail = config('services.sendgrid.from_email')
            ?: optional(auth('admin')->user())->email
            ?: config('mail.from.address', 'noreply@example.com');

        if (count($senderEmails) > 0 && ! $this->emailInList($fromEmail, $senderEmails)) {
            $fromEmail = $senderEmails[0];
        }

        return view('Admin.outlook.index', compact('fromEmail', 'senderEmails', 'verifiedSenders'));
    }

    /**
     * Return verified senders as JSON for AJAX (e.g. frontend refresh on page load).
     * GET /admin/outlook/senders
     */
    public function senders(Request $request)
    {
        $list = $this->getVerifiedSenders();
        $fromEmail = config('services.sendgrid.from_email', '');
        if (empty($fromEmail)) {
            $fromEmail = optional(auth('admin')->user())->email ?? config('mail.from.address', '');
        }
        $emails = array_column($list, 'email');
        if (! empty($emails) && ! $this->emailInList($fromEmail, $emails)) {
            $fromEmail = $emails[0];
        }
        return response()->json([
            'senders' => $list,
            'default_from' => $fromEmail,
        ]);
    }

    /**
     * Fetch verified senders from SendGrid API.
     * Automatically shows new emails added in SendGrid when you refresh the page.
     */
    private function getVerifiedSenders(): array
    {
        $apiKey = config('services.sendgrid.api_key');
        $baseUrl = rtrim(config('services.sendgrid.base_url', 'https://api.sendgrid.com'), '/');

        $senders = [];

        if (empty($apiKey)) {
            Log::warning('Outlook: SENDGRID_API_KEY not set in .env');
            return $this->getFallbackSendersFromEnv();
        }

        try {
            // Method 1: Try verified_senders endpoint
            /** @var Response $response */
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->timeout(10)->get($baseUrl . '/v3/verified_senders');

            Log::info('SendGrid API Response Status: ' . $response->status());
            Log::info('SendGrid API Response Body: ' . $response->body());

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['results'])) {
                    foreach ($data['results'] as $sender) {
                        if (! empty($sender['from_email']) && (isset($sender['verified']) ? $sender['verified'] : true)) {
                            $senders[] = [
                                'email' => $sender['from_email'],
                                'name' => $sender['from_name'] ?? $sender['nickname'] ?? $sender['from_email'],
                                'nickname' => $sender['nickname'] ?? '',
                            ];
                        }
                    }
                }
            }

            // Method 2: If no results, try senders endpoint
            if (empty($senders)) {
                /** @var Response $response2 */
                $response2 = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                ])->timeout(10)->get($baseUrl . '/v3/senders');

                if ($response2->successful()) {
                    $data2 = $response2->json();
                    $result = $data2['result'] ?? (is_array($data2) ? $data2 : []);

                    foreach (is_array($result) ? $result : [] as $sender) {
                        $email = $sender['from']['email'] ?? $sender['email'] ?? null;
                        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $senders[] = [
                                'email' => $email,
                                'name' => $sender['from']['name'] ?? $sender['nickname'] ?? $email,
                                'nickname' => $sender['nickname'] ?? '',
                            ];
                        }
                    }
                }
            }

            // Method 3: Try EU endpoint if still empty
            if (empty($senders) && strpos($baseUrl, 'api.sendgrid.com') !== false) {
                /** @var Response $responseEu */
                $responseEu = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                ])->timeout(10)->get('https://api.eu.sendgrid.com/v3/verified_senders');

                if ($responseEu->successful()) {
                    $data = $responseEu->json();
                    if (isset($data['results'])) {
                        foreach ($data['results'] as $sender) {
                            if (! empty($sender['from_email']) && (isset($sender['verified']) ? $sender['verified'] : true)) {
                                $senders[] = [
                                    'email' => $sender['from_email'],
                                    'name' => $sender['from_name'] ?? $sender['nickname'] ?? $sender['from_email'],
                                    'nickname' => $sender['nickname'] ?? '',
                                ];
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('SendGrid API Error: ' . $e->getMessage());
        }

        // Remove duplicates by email
        $senders = collect($senders)->unique('email')->values()->toArray();

        // If still empty, use fallback from .env
        if (empty($senders)) {
            Log::warning('No verified senders found from API, using fallback');
            return $this->getFallbackSendersFromEnv();
        }

        return $senders;
    }

    /**
     * Fallback: use SENDGRID_SENDERS from .env (comma-separated emails).
     */
    private function getFallbackSendersFromEnv(): array
    {
        $fallbackSenders = config('services.sendgrid.senders');
        if (empty($fallbackSenders) || ! is_string($fallbackSenders)) {
            return [];
        }
        $emails = array_filter(array_map('trim', explode(',', $fallbackSenders)));
        $list = [];
        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $list[] = [
                    'email' => $email,
                    'name' => $email,
                    'nickname' => '',
                ];
            }
        }
        return $list;
    }

    /**
     * Mailer for Outlook send: Education Elite UI uses a dedicated subuser when configured.
     */
    private function mailerNameForOutlookSend(Request $request): string
    {
        if ($request->boolean('_elite_compose') && ! empty(config('services.sendgrid_elite.api_key'))) {
            return 'sendgrid_elite';
        }

        return 'sendgrid_outlook';
    }

    /**
     * Display name for From, matching SendGrid verified sender (better DMARC alignment / reputation).
     */
    private function displayNameForFrom(string $fromEmail, array $verifiedSenders): string
    {
        $fromLower = strtolower($fromEmail);
        foreach ($verifiedSenders as $sender) {
            if (! empty($sender['email']) && strtolower((string) $sender['email']) === $fromLower) {
                $name = $sender['name'] ?? $sender['nickname'] ?? $fromEmail;

                return (string) $name !== '' ? (string) $name : $fromEmail;
            }
        }

        return $fromEmail;
    }

    /**
     * Plain-text part for multipart/alternative (many receivers score HTML-only as bulk).
     */
    private function htmlToPlainTextForMail(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return ' ';
        }
        $t = strip_tags($html);
        $t = html_entity_decode($t, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $t = preg_replace('/[ \t]+/u', ' ', $t) ?? $t;
        $t = preg_replace('/\r\n|\r|\n/u', "\n", $t) ?? $t;
        $t = trim($t);

        return $t !== '' ? $t : ' ';
    }

    /**
     * Reply-To for Elite "New Message" so customer replies go to SendGrid Inbound Parse → POST /elite/emails.
     * Uses EDUCATION_ELITE_INBOUND_REPLY_TO, or {EDUCATION_ELITE_INBOUND_REPLY_LOCAL}@{EDUCATION_ELITE_INBOUND_PARSE_HOST}.
     */
    private function resolveEliteComposeReplyTo(): ?string
    {
        if (! config('crm.education_elite_inbound_set_reply_to', true)) {
            return null;
        }
        $explicit = trim((string) config('crm.education_elite_inbound_reply_to', ''));
        if ($explicit !== '' && filter_var($explicit, FILTER_VALIDATE_EMAIL)) {
            return strtolower($explicit);
        }
        $parseHost = trim((string) config('crm.education_elite_inbound_parse_host', ''));
        if ($parseHost === '') {
            return null;
        }
        $local = trim((string) config('crm.education_elite_inbound_reply_local', 'inbound'));
        if ($local === '' || ! preg_match('/^[a-z0-9._%+\-]+$/i', $local)) {
            $local = 'inbound';
        }
        $parseHost = strtolower(ltrim($parseHost, '@'));
        $addr = $local.'@'.$parseHost;

        if (! filter_var($addr, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return $addr;
    }

    /**
     * Debug SendGrid API - visit /admin/outlook/debug to see what SendGrid returns.
     */
    public function debug(Request $request)
    {
        $apiKey = config('services.sendgrid.api_key');
        $baseUrl = rtrim(config('services.sendgrid.base_url', 'https://api.sendgrid.com'), '/');
        $debug = [
            'api_key_set' => ! empty($apiKey),
            'api_key_prefix' => $apiKey ? substr($apiKey, 0, 10) . '...' : null,
            'base_url' => $baseUrl,
            'verified_senders' => ['status' => null, 'count' => 0],
            'senders' => ['status' => null, 'count' => 0],
            'emails' => [],
            'errors' => [],
        ];

        if (! $apiKey) {
            $debug['errors'][] = 'SENDGRID_API_KEY not found in .env. Add it and run: php artisan config:clear';
            return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
        }

        $client = Http::withToken($apiKey)->timeout(10);
        $emails = [];

        /** @var Response $res1 */
        $res1 = $client->get("{$baseUrl}/v3/verified_senders");
        $debug['verified_senders']['status'] = $res1->status();
        if ($res1->successful()) {
            $results = $res1->json('results', []);
            $debug['verified_senders']['count'] = count($results);
            foreach ($results as $s) {
                if (! empty($s['from_email'])) {
                    $emails[$s['from_email']] = true;
                }
            }
        } else {
            $debug['errors'][] = "verified_senders: HTTP {$res1->status()} - " . ($res1->json('errors.0.message') ?? $res1->body());
        }

        /** @var Response $res2 */
        $res2 = $client->get("{$baseUrl}/v3/senders");
        $debug['senders']['status'] = $res2->status();
        if ($res2->successful()) {
            $result = $res2->json('result', []);
            $debug['senders']['count'] = count($result);
            foreach (is_array($result) ? $result : [] as $s) {
                $email = $s['from']['email'] ?? null;
                if ($email) {
                    $emails[$email] = true;
                }
            }
        } else {
            $debug['errors'][] = "senders: HTTP {$res2->status()} - " . ($res2->json('errors.0.message') ?? $res2->body());
        }

        $debug['emails'] = array_keys($emails);
        return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Fetch emails by folder (inbox, sent, drafts, trash).
     * Sent folder returns all sent emails from emails table (CRM + Outlook).
     * Supports filters: search, date_from, date_to, sort, filter_from, filter_to, has_attachments.
     */
    public function inbox(Request $request)
    {
        if (! $request->expectsJson()) {
            return redirect()->route('admin.outlook.index');
        }
        $folder = $request->get('folder', 'inbox');
        $search = trim($request->get('search', ''));
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        $sort = $request->get('sort', 'newest');
        $filterFrom = trim($request->get('filter_from', ''));
        $filterTo = trim($request->get('filter_to', ''));
        $hasAttachments = $request->boolean('has_attachments');

        $messages = [
            'inbox' => 'No emails yet. Configure SendGrid API and Inbound Parse to receive emails.',
            'sent' => 'No sent messages found. Emails you send via this page are stored here automatically.',
            'drafts' => 'No drafts saved.',
            'trash' => 'Trash is empty.',
        ];

        $emails = [];
        $sent_groups = [];
        $filterOptions = ['from_list' => [], 'to_list' => []];

        if ($folder === 'drafts') {
            $query = OutlookDraftEmail::orderBy('updated_at', 'desc')->where('admin_id', auth('admin')->id());
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('from_email', 'like', '%' . $search . '%')
                        ->orWhere('to_email', 'like', '%' . $search . '%')
                        ->orWhere('subject', 'like', '%' . $search . '%');
                });
            }
            foreach ($query->get() as $draft) {
                $emails[] = [
                    'id' => $draft->id,
                    'from' => $draft->from_email,
                    'to' => $draft->to_email,
                    'subject' => $draft->subject ?: '(No subject)',
                    'date' => $draft->updated_at->format('d/m/Y g:i A'),
                ];
            }
        } elseif ($folder === 'sent') {
            // Sent: all mail_type=1 rows; prefer verified-sender rows but fall back to all if API is unavailable.
            $verifiedSenders    = $this->getVerifiedSenders();
            $verifiedForDropdown = $this->uniqueSenderEmailsForUi($verifiedSenders);
            $verifiedLower      = $this->normalizedVerifiedSenderEmails($verifiedSenders);

            $query = Email::where('mail_type', 1);

            // Only apply verified-sender filter when the API actually returned addresses.
            // When the API is down / unconfigured, fall back to showing all sent mail so
            // users can always see what was sent.
            if (! empty($verifiedLower)) {
                $this->scopeSentFromVerifiedSenders($query, $verifiedLower);
            }

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('from_mail', 'like', '%' . $search . '%')
                        ->orWhere('to_mail', 'like', '%' . $search . '%')
                        ->orWhere('subject', 'like', '%' . $search . '%');
                });
            }
            if ($dateFrom !== '') {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo !== '') {
                $query->whereDate('created_at', '<=', $dateTo);
            }
            if ($filterFrom !== '') {
                $query->whereRaw('LOWER(TRIM(COALESCE(from_mail, \'\'))) = ?', [strtolower(trim($filterFrom))]);
            }
            if ($filterTo !== '') {
                $query->where('to_mail', $filterTo);
            }
            if ($hasAttachments) {
                $query->whereHas('attachments');
            }

            $sortDir = ($sort === 'oldest') ? 'asc' : 'desc';
            $query->orderBy('created_at', $sortDir);

            $list = $query->get();

            // Build per-sender unique list for the "From" filter dropdown.
            // If API returned verified senders, use those; otherwise derive from the DB rows.
            if (! empty($verifiedForDropdown)) {
                $filterOptions['from_list'] = $verifiedForDropdown;
            } else {
                $filterOptions['from_list'] = $list->pluck('from_mail')
                    ->filter()->unique()->values()->toArray();
            }
            $filterOptions['to_list'] = $list->pluck('to_mail')
                ->filter()->unique()->values()->toArray();

            foreach ($list as $sent) {
                $emails[] = [
                    'id'         => $sent->id,
                    'from'       => $sent->from_mail,
                    'to'         => $sent->to_mail,
                    'cc'         => $sent->cc,
                    'subject'    => $sent->subject ?: '(No subject)',
                    'body'       => $sent->message,
                    'date'       => $sent->created_at->format('d/m/Y g:i A'),
                    'date_short' => $sent->created_at->format('g:i A'),
                ];
            }

            // Group by from_mail (kept for compatibility – frontend may still use it).
            $byFrom = [];
            foreach ($list as $sent) {
                $from = $sent->from_mail ?? '(unknown)';
                if (! isset($byFrom[$from])) {
                    $byFrom[$from] = ['from_email' => $from, 'emails' => []];
                }
                $byFrom[$from]['emails'][] = [
                    'id'         => $sent->id,
                    'to'         => $sent->to_mail,
                    'cc'         => $sent->cc,
                    'subject'    => $sent->subject ?: '(No subject)',
                    'body'       => $sent->message,
                    'date'       => $sent->created_at->format('d/m/Y g:i A'),
                    'date_short' => $sent->created_at->format('g:i A'),
                ];
            }
            $sent_groups = array_values($byFrom);
        } elseif ($folder === 'inbox') {
            // Inbox: same filter logic when data source exists (e.g. mail_type 0)
            $query = Email::where('mail_type', 0);
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('from_mail', 'like', '%' . $search . '%')
                        ->orWhere('to_mail', 'like', '%' . $search . '%')
                        ->orWhere('subject', 'like', '%' . $search . '%');
                });
            }
            if ($dateFrom !== '') {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo !== '') {
                $query->whereDate('created_at', '<=', $dateTo);
            }
            if ($hasAttachments) {
                $query->whereHas('attachments');
            }
            $sortDir = ($sort === 'oldest') ? 'asc' : 'desc';
            $query->orderBy('created_at', $sortDir);
            $list = $query->get();
            foreach ($list as $item) {
                $emails[] = [
                    'id' => $item->id,
                    'from' => $item->from_mail,
                    'to' => $item->to_mail,
                    'subject' => $item->subject,
                    'body' => $item->message,
                    'date' => $item->created_at->format('d/m/Y g:i A'),
                ];
            }
        }

        return response()->json([
            'emails' => $emails,
            'sent_groups' => $sent_groups,
            'filter_options' => $filterOptions,
            'message' => $messages[$folder] ?? $messages['inbox'],
        ]);
    }

    /**
     * Send email via SendGrid (uses selected From address from dropdown).
     * Form fields: from, to, cc, subject, body; supports attachments.
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|email',
            'to' => 'required|email',
            'cc' => 'nullable|email',
            'subject' => 'required|string|max:500',
            'body' => 'nullable|string',
        ]);

        $from = $validated['from'];
        $to = $validated['to'];
        $cc = $request->filled('cc') ? [$validated['cc']] : [];
        $subject = $validated['subject'];
        $body = $validated['body'] ?? '';

        $eliteReplyTo = null;
        if ($request->boolean('_elite_compose')) {
            $eliteReplyTo = $this->resolveEliteComposeReplyTo();
        }

        $verifiedSenders = $this->getVerifiedSenders();
        $fromDisplayName = $this->displayNameForFrom($from, $verifiedSenders);
        $htmlBody = $body !== '' ? $body : '<p> </p>';
        $plainBody = $this->htmlToPlainTextForMail($htmlBody);
        $mailerName = $this->mailerNameForOutlookSend($request);

        try {
            /** @var LaravelMailer $mailer */
            $mailer = Mail::mailer($mailerName);
            $sent = $mailer->html($htmlBody, function ($message) use ($from, $fromDisplayName, $to, $cc, $subject, $plainBody, $eliteReplyTo) {
                $message->to($to)->from($from, $fromDisplayName)->subject($subject);
                $message->text($plainBody);
                if ($eliteReplyTo !== null) {
                    $message->replyTo($eliteReplyTo);
                }
                if (count($cc) > 0) {
                    $message->cc($cc);
                }
                $files = request()->file('attachments', []);
                if (is_array($files)) {
                    foreach ($files as $file) {
                        if ($file && $file->isValid()) {
                            $message->attach($file->getRealPath(), [
                                'as' => $file->getClientOriginalName(),
                                'mime' => $file->getMimeType(),
                            ]);
                        }
                    }
                }
            });

            if ($sent === null) {
                Log::error('Outlook: mail transport returned no sent confirmation', [
                    'from' => $from,
                    'to' => $to,
                    'mailer' => $mailerName,
                ]);
                throw new \RuntimeException('Email could not be sent (message was blocked or not transmitted).');
            }

            // Record sent email in emails table (same as CRM) so Sent folder shows all emails
            try {
                Email::create([
                    'user_id' => auth('admin')->id(),
                    'from_mail' => $from,
                    'to_mail' => $to,
                    'cc' => count($cc) > 0 ? implode(', ', $cc) : null,
                    'subject' => $subject,
                    'message' => $body,
                    'type' => 'outlook',
                    'client_id' => null,
                    'mail_type' => 1,
                ]);
            } catch (\Throwable $createEx) {
                Log::error('Outlook: failed to record sent email', ['error' => $createEx->getMessage(), 'trace' => $createEx->getTraceAsString()]);
            }

            if ($request->boolean('_elite_compose') || $request->wantsJson()) {
                return response()->json(['ok' => true, 'message' => 'Email sent successfully.']);
            }

            return redirect()->route('admin.outlook.index')
                ->with('success', 'Email sent successfully.')
                ->with('refresh_sent', true);
        } catch (\Throwable $e) {
            Log::error('Email sending error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            if ($request->boolean('_elite_compose') || $request->wantsJson()) {
                return response()->json(['ok' => false, 'message' => 'Failed to send email: ' . $e->getMessage()], 500);
            }

            return redirect()->route('admin.outlook.index')
                ->with('error', 'Failed to send email: ' . $e->getMessage())
                ->withInput($request->only('from', 'to', 'cc', 'subject', 'body'));
        }
    }

    /**
     * Save current compose as draft (no email sent). To/Subject can be empty.
     */
    public function saveDraft(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|email',
            'to' => 'nullable|email',
            'cc' => 'nullable|email',
            'subject' => 'nullable|string|max:500',
            'body' => 'nullable|string',
        ]);

        OutlookDraftEmail::create([
            'from_email' => $validated['from'],
            'to_email' => $request->filled('to') ? $validated['to'] : null,
            'cc' => $request->filled('cc') ? $validated['cc'] : null,
            'subject' => $request->filled('subject') ? $validated['subject'] : null,
            'body' => $validated['body'] ?? null,
            'admin_id' => auth('admin')->id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Draft saved.']);
        }

        return redirect()->route('admin.outlook.index')
            ->with('success', 'Draft saved.');
    }

    /**
     * Case-insensitive match of $email against a list of address strings.
     */
    private function emailInList(string $email, array $list): bool
    {
        $want = strtolower(trim($email));
        if ($want === '' || $list === []) {
            return false;
        }
        foreach ($list as $e) {
            if (strtolower(trim((string) $e)) === $want) {
                return true;
            }
        }

        return false;
    }

    /**
     * SendGrid API + env fallback — normalized unique emails (lowercase) for SQL filters.
     *
     * @param  array<int, array<string, mixed>>  $verifiedSenders
     * @return list<string>
     */
    private function normalizedVerifiedSenderEmails(array $verifiedSenders): array
    {
        $out = [];
        foreach ($verifiedSenders as $s) {
            $e = isset($s['email']) ? strtolower(trim((string) $s['email'])) : '';
            if ($e !== '' && filter_var($e, FILTER_VALIDATE_EMAIL)) {
                $out[$e] = $e;
            }
        }

        return array_values($out);
    }

    /**
     * One entry per logical sender, sorted, for filter dropdowns (preserve readable casing when possible).
     *
     * @param  array<int, array<string, mixed>>  $verifiedSenders
     * @return list<string>
     */
    private function uniqueSenderEmailsForUi(array $verifiedSenders): array
    {
        $byKey = [];
        foreach ($verifiedSenders as $s) {
            if (empty($s['email'])) {
                continue;
            }
            $raw = trim((string) $s['email']);
            $key = strtolower($raw);
            if ($key === '' || ! filter_var($key, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            if (! array_key_exists($key, $byKey)) {
                $byKey[$key] = $raw;
            }
        }
        $list = array_values($byKey);
        sort($list);

        return $list;
    }

    /**
     * Sent folder: from_mail must match a SendGrid-verified address (lowercase compare).
     *
     * @param  Builder  $query
     * @param  list<string>  $verifiedLower
     */
    private function scopeSentFromVerifiedSenders(Builder $query, array $verifiedLower): void
    {
        if ($verifiedLower === []) {
            $query->whereRaw('1 = 0');

            return;
        }
        $placeholders = implode(',', array_fill(0, count($verifiedLower), '?'));
        $query->whereRaw(
            'LOWER(TRIM(COALESCE(from_mail, \'\'))) IN ('.$placeholders.')',
            $verifiedLower
        );
    }
}
