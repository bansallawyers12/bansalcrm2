<?php

namespace App\Http\Controllers\Elite;

use App\Http\Controllers\Controller;
use App\Models\EliteEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EliteEmailController extends Controller
{
    private const ELITE_DOMAIN = '@educationelite.com.au';

    public function index()
    {
        $emails = EliteEmail::query()
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        return view('elite.emails-inbox', compact('emails'));
    }

    /**
     * JSON list for the inbox UI (same shape as admin Outlook inbox folder).
     */
    public function inbox(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        $sort = $request->get('sort', 'newest');

        $query = EliteEmail::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('from_address', 'like', '%' . $search . '%')
                    ->orWhere('to_address', 'like', '%' . $search . '%')
                    ->orWhere('subject', 'like', '%' . $search . '%')
                    ->orWhere('body_text', 'like', '%' . $search . '%');
            });
        }
        if ($dateFrom !== '') {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo !== '') {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $sortDir = ($sort === 'oldest') ? 'asc' : 'desc';
        $query->orderBy('created_at', $sortDir);

        $list = $query->limit(500)->get();
        $emails = [];
        foreach ($list as $item) {
            $emails[] = [
                'id' => $item->id,
                'from' => $item->from_address,
                'to' => $item->to_address,
                'subject' => $item->subject,
                'body' => $item->body_html ?: $item->body_text,
                'date' => $item->created_at->format('d/m/Y g:i A'),
            ];
        }

        return response()->json([
            'emails' => $emails,
            'sent_groups' => [],
            'filter_options' => ['from_list' => [], 'to_list' => []],
            'message' => 'No messages from @educationelite.com.au yet. POST inbound mail to this URL or use “Simulate inbound” below.',
        ]);
    }

    /**
     * Inbound parse / webhook: only accepts senders @educationelite.com.au.
     */
    public function store(Request $request)
    {
        $fromRaw = $request->input('from')
            ?? $request->input('sender')
            ?? $request->input('from_email')
            ?? $request->input('From');

        if ($fromRaw === null && is_array($request->input('envelope'))) {
            $fromRaw = data_get($request->input('envelope'), 'from');
        }

        $fromAddress = $this->parseEmailAddress((string) $fromRaw);
        if (! $fromAddress || ! $this->isEliteSender($fromAddress)) {
            Log::warning('elite.emails.rejected', ['from' => $fromRaw, 'ip' => $request->ip()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'error' => 'Sender must be an @educationelite.com.au address.',
                ], 422);
            }

            return back()->with('error', 'Sender must be an @educationelite.com.au address.');
        }

        $subject = $request->input('subject') ?? $request->input('Subject');
        $to = $request->input('to') ?? $request->input('recipient') ?? $request->input('To');
        $text = $request->input('text') ?? $request->input('body_text') ?? $request->input('plain');
        $html = $request->input('html') ?? $request->input('body_html') ?? $request->input('body');

        if (is_string($html) && strip_tags($html) === $html && $text === null) {
            $text = $html;
            $html = null;
        }

        $payload = $request->except(['_token']);
        if (count($payload) > 80) {
            $payload = array_slice($payload, 0, 80, true);
        }

        $record = EliteEmail::create([
            'from_address' => $fromAddress,
            'to_address' => is_string($to) ? substr($to, 0, 255) : null,
            'subject' => is_string($subject) ? substr($subject, 0, 998) : null,
            'body_text' => is_string($text) ? $text : null,
            'body_html' => is_string($html) ? $html : null,
            'payload' => $payload,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'id' => $record->id]);
        }

        return back()->with('success', 'Email recorded.');
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
        return str_ends_with(strtolower($email), strtolower(self::ELITE_DOMAIN));
    }
}
