<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailReport;
use Illuminate\Http\Request;
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

        if (count($senderEmails) > 0 && ! in_array($fromEmail, $senderEmails)) {
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
        if (! empty($emails) && ! in_array($fromEmail, $emails)) {
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
     * Sent folder returns all sent emails from mail_reports (CRM + Outlook).
     */
    public function inbox(Request $request)
    {
        if (! $request->expectsJson()) {
            return redirect()->route('admin.outlook.index');
        }
        $folder = $request->get('folder', 'inbox');
        $search = $request->get('search', '');
        $messages = [
            'inbox' => 'No emails yet. Configure SendGrid API and Inbound Parse to receive emails.',
            'sent' => 'No sent messages.',
            'drafts' => 'No drafts saved.',
            'trash' => 'Trash is empty.',
        ];

        $emails = [];
        $sent_groups = [];
        if ($folder === 'sent') {
            // Use mail_reports (same table as CRM) - mail_type 1 = sent/composed emails
            $query = MailReport::where('mail_type', 1)->orderBy('created_at', 'desc');
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('from_mail', 'like', '%' . $search . '%')
                        ->orWhere('to_mail', 'like', '%' . $search . '%')
                        ->orWhere('subject', 'like', '%' . $search . '%');
                });
            }
            $list = $query->get();
            foreach ($list as $sent) {
                $emails[] = [
                    'id' => $sent->id,
                    'from' => $sent->from_mail,
                    'to' => $sent->to_mail,
                    'cc' => $sent->cc,
                    'subject' => $sent->subject,
                    'body' => $sent->message,
                    'date' => $sent->created_at->format('d/m/Y g:i A'),
                    'date_short' => $sent->created_at->format('g:i A'),
                ];
            }
            // Group by from_mail (different section per sender, like Outlook accounts)
            $byFrom = [];
            foreach ($list as $sent) {
                $from = $sent->from_mail;
                if (! isset($byFrom[$from])) {
                    $byFrom[$from] = [
                        'from_email' => $from,
                        'emails' => [],
                    ];
                }
                $byFrom[$from]['emails'][] = [
                    'id' => $sent->id,
                    'to' => $sent->to_mail,
                    'cc' => $sent->cc,
                    'subject' => $sent->subject,
                    'body' => $sent->message,
                    'date' => $sent->created_at->format('d/m/Y g:i A'),
                    'date_short' => $sent->created_at->format('g:i A'),
                ];
            }
            $sent_groups = array_values($byFrom);
        }

        return response()->json([
            'emails' => $emails,
            'sent_groups' => $sent_groups,
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

        try {
            Mail::mailer('sendgrid_outlook')->html($body ?: '<p> </p>', function ($message) use ($from, $to, $cc, $subject) {
                $message->to($to)->from($from)->subject($subject);
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

            // Record sent email in mail_reports (same table as CRM) so Sent folder shows all emails
            try {
                MailReport::create([
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

            return redirect()->route('admin.outlook.index')
                ->with('success', 'Email sent successfully.')
                ->with('refresh_sent', true);
        } catch (\Throwable $e) {
            Log::error('Email sending error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('admin.outlook.index')
                ->with('error', 'Failed to send email: ' . $e->getMessage())
                ->withInput($request->only('from', 'to', 'cc', 'subject', 'body'));
        }
    }
}
