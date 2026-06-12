<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Models\OutlookDraftEmail;
use App\Support\EducationEliteMail;
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
     * Return verified senders as JSON for AJAX (e.g. frontend refresh on page load).
     * CRM compose: @bansaleducation.com.au + admission@bansalimmigration.com.au (SendGrid).
     * Elite compose (?elite=1): @educationelite.com.au addresses (AWS SES).
     * GET /admin/outlook/senders
     */
    public function senders(Request $request)
    {
        if ($request->boolean('elite')) {
            $list = $this->getEliteSenders();
            $fromEmail = (string) config('services.ses_elite.from_email', '');
            if ($fromEmail === '' || ! EducationEliteMail::isEliteOwnedAddress($fromEmail)) {
                $fromEmail = (string) config('mail.from.address', '');
            }
            if ($fromEmail === '' || ! EducationEliteMail::isEliteOwnedAddress($fromEmail)) {
                $fromEmail = $list[0]['email'] ?? '';
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

        $list = $this->filterComposeSenders($this->getVerifiedSenders());
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
     * Limit Compose Email From dropdown to education addresses plus one immigration admission inbox.
     */
    private function filterComposeSenders(array $senders): array
    {
        $filtered = array_values(array_filter($senders, function (array $sender) {
            $email = strtolower(trim((string) ($sender['email'] ?? '')));
            if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return false;
            }

            return str_ends_with($email, '@bansaleducation.com.au')
                || $email === 'admission@bansalimmigration.com.au';
        }));

        usort($filtered, function (array $a, array $b) {
            $emailA = strtolower((string) ($a['email'] ?? ''));
            $emailB = strtolower((string) ($b['email'] ?? ''));

            if ($emailA === 'admission@bansalimmigration.com.au') {
                return -1;
            }
            if ($emailB === 'admission@bansalimmigration.com.au') {
                return 1;
            }

            return strcmp($emailA, $emailB);
        });

        return $filtered;
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
     * Verified @educationelite.com.au From addresses for Elite compose (AWS SES).
     *
     * @return list<array{email: string, name: string, nickname: string}>
     */
    private function getEliteSenders(): array
    {
        $raw = (string) config('services.ses_elite.senders', '');
        if (trim($raw) === '') {
            $raw = (string) config('services.ses_elite.from_email', 'info@'.EducationEliteMail::apexDomain());
        }
        $displayName = (string) config('crm.education_elite_from_name', config('mail.from.name', 'Education Elite'));
        $emails = array_filter(array_map('trim', explode(',', $raw)));
        $list = [];

        foreach ($emails as $email) {
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            if (! EducationEliteMail::isEliteOwnedAddress($email)) {
                continue;
            }
            $list[] = [
                'email' => strtolower($email),
                'name' => $displayName !== '' ? $displayName : $email,
                'nickname' => '',
            ];
        }

        return collect($list)->unique('email')->values()->toArray();
    }

    private function isSesEliteConfigured(): bool
    {
        $key = config('services.ses.key');
        $secret = config('services.ses.secret');

        return ! empty($key) && ! empty($secret);
    }

    /**
     * Mailer for Outlook send: Elite compose uses AWS SES when configured.
     */
    private function mailerNameForOutlookSend(Request $request): string
    {
        if (! $request->boolean('_elite_compose')) {
            return 'sendgrid_outlook';
        }

        $preferred = (string) config('crm.education_elite_mailer', 'ses_elite');

        if ($preferred === 'sendgrid_elite' && ! empty(config('services.sendgrid_elite.api_key'))) {
            return 'sendgrid_elite';
        }

        if ($this->isSesEliteConfigured()) {
            return 'ses_elite';
        }

        if (! empty(config('services.sendgrid_elite.api_key'))) {
            Log::warning('Outlook: SES credentials missing for Elite compose; falling back to sendgrid_elite');

            return 'sendgrid_elite';
        }

        Log::warning('Outlook: neither SES nor sendgrid_elite configured for Elite compose; using sendgrid_outlook');

        return 'sendgrid_outlook';
    }

    /**
     * @return list<string>
     */
    private function allowedEliteFromEmails(): array
    {
        return array_column($this->getEliteSenders(), 'email');
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
     * Reply-To for Elite "New Message".
     *
     * Since the full educationelite.com.au domain MX points to SendGrid Inbound Parse,
     * replies to the From address (info@educationelite.com.au) are captured directly.
     * No Reply-To subdomain is needed. Returns null unless EDUCATION_ELITE_INBOUND_PARSE_HOST
     * is explicitly set AND is NOT a subdomain of the apex domain (future-proof for
     * multi-domain setups). Ignores EDUCATION_ELITE_INBOUND_REPLY_TO to prevent
     * broken subdomain addresses from being injected via env.
     */
    private function resolveEliteComposeReplyTo(): ?string
    {
        if (! config('crm.education_elite_inbound_set_reply_to', false)) {
            return null;
        }

        $parseHost = strtolower(trim(ltrim((string) config('crm.education_elite_inbound_parse_host', ''), '@')));
        if ($parseHost === '') {
            return null;
        }

        $apexDomain = strtolower(trim(ltrim((string) config('crm.education_elite_sender_domain', 'educationelite.com.au'), '@')));

        // If parse host is the apex or a subdomain of it, MX already covers it — no Reply-To needed.
        if ($parseHost === $apexDomain || str_ends_with($parseHost, '.' . $apexDomain)) {
            return null;
        }

        $local = trim((string) config('crm.education_elite_inbound_reply_local', 'inbound'));
        if ($local === '' || ! preg_match('/^[a-z0-9._%+\-]+$/i', $local)) {
            $local = 'inbound';
        }

        $addr = $local . '@' . $parseHost;

        return filter_var($addr, FILTER_VALIDATE_EMAIL) ? $addr : null;
    }

    /**
     * Send email via SendGrid (CRM/Outlook) or AWS SES (Education Elite compose).
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

        if ($request->boolean('_elite_compose')) {
            if (! EducationEliteMail::isEliteOwnedAddress($from)) {
                $message = 'From address must be an @'.EducationEliteMail::apexDomain().' mailbox.';
                if ($request->wantsJson()) {
                    return response()->json(['ok' => false, 'message' => $message], 422);
                }

                return redirect()->back()->with('error', $message)->withInput();
            }
            $allowedFrom = $this->allowedEliteFromEmails();
            if ($allowedFrom !== [] && ! $this->emailInList($from, $allowedFrom)) {
                $message = 'From address is not an allowed Education Elite sender.';
                if ($request->wantsJson()) {
                    return response()->json(['ok' => false, 'message' => $message], 422);
                }

                return redirect()->back()->with('error', $message)->withInput();
            }
        }

        $eliteReplyTo = null;
        if ($request->boolean('_elite_compose')) {
            $eliteReplyTo = $this->resolveEliteComposeReplyTo();
        }

        $verifiedSenders = $request->boolean('_elite_compose')
            ? $this->getEliteSenders()
            : $this->getVerifiedSenders();
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

            return redirect()->route('dashboard')
                ->with('success', 'Email sent successfully.');
        } catch (\Throwable $e) {
            Log::error('Email sending error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            if ($request->boolean('_elite_compose') || $request->wantsJson()) {
                return response()->json(['ok' => false, 'message' => 'Failed to send email: ' . $e->getMessage()], 500);
            }

            return redirect()->route('dashboard')
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

        return redirect()->route('dashboard')
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
}
