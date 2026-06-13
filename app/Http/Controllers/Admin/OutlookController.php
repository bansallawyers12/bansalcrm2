<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Models\OutlookDraftEmail;
use App\Services\SesSenderService;
use App\Support\EducationEliteMail;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer as LaravelMailer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class OutlookController extends Controller
{
    public function __construct(
        private SesSenderService $sesSenderService
    ) {
        $this->middleware('auth:admin');
    }

    /**
     * Return verified senders as JSON for AJAX (e.g. frontend refresh on page load).
     * Unified list: @bansaleducation.com.au + @educationelite.com.au (+ admission@bansalimmigration.com.au).
     * Managed in Admin Console → Emails; ?elite=1 only changes the default From preference.
     * GET /admin/outlook/senders
     */
    public function senders(Request $request)
    {
        $list = $this->sesSenderService->getComposeSenders(auth('admin')->id());
        $emails = array_column($list, 'email');

        if ($request->boolean('elite')) {
            $fromEmail = (string) config('services.ses_elite.from_email', '');
            if ($fromEmail === '' || ! $this->sesSenderService->isAllowedSenderDomain($fromEmail)) {
                $fromEmail = 'info@'.EducationEliteMail::apexDomain();
            }
        } else {
            $fromEmail = (string) config('services.ses_crm.from_email', '');
            if ($fromEmail === '') {
                $fromEmail = optional(auth('admin')->user())->email ?? config('mail.from.address', '');
            }
        }

        if (! empty($emails) && ! $this->emailInList($fromEmail, $emails)) {
            $fromEmail = $emails[0];
        }

        return response()->json([
            'senders' => $list,
            'default_from' => $fromEmail,
        ]);
    }

    /**
     * Mailer for Outlook send — chosen from the From address domain.
     */
    private function mailerNameForAddress(string $from): string
    {
        if (! $this->sesSenderService->isConfigured()) {
            Log::error('Outlook: SES credentials missing for compose');

            throw new \RuntimeException('AWS SES is not configured.');
        }

        return $this->sesSenderService->mailerForAddress($from);
    }

    /**
     * Display name for From, matching verified sender list.
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

        if ($parseHost === $apexDomain || str_ends_with($parseHost, '.'.$apexDomain)) {
            return null;
        }

        $local = trim((string) config('crm.education_elite_inbound_reply_local', 'inbound'));
        if ($local === '' || ! preg_match('/^[a-z0-9._%+\-]+$/i', $local)) {
            $local = 'inbound';
        }

        $addr = $local.'@'.$parseHost;

        return filter_var($addr, FILTER_VALIDATE_EMAIL) ? $addr : null;
    }

    /**
     * Send email via AWS SES (CRM and Education Elite compose).
     * Form fields: from, to, cc, subject, body; supports attachments.
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|email',
            'to' => 'required|string|max:2000',
            'cc' => 'nullable|string|max:2000',
            'subject' => 'required|string|max:500',
            'body' => 'nullable|string',
        ]);

        $from = $validated['from'];
        $toList = $this->parseEmailList($validated['to'], 'to', true);
        $ccList = $this->parseEmailList($request->input('cc'), 'cc', false);
        $toDisplay = implode(', ', $toList);
        $subject = $validated['subject'];
        $body = $validated['body'] ?? '';

        if (! $this->sesSenderService->isAllowedSenderDomain($from)) {
            $message = 'From address must be @bansaleducation.com.au, @'.EducationEliteMail::apexDomain().', or admission@bansalimmigration.com.au.';
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'message' => $message], 422);
            }

            return redirect()->back()->with('error', $message)->withInput();
        }

        $allowedFrom = array_column($this->sesSenderService->getComposeSenders(auth('admin')->id()), 'email');
        if ($allowedFrom !== [] && ! $this->emailInList($from, $allowedFrom)) {
            $message = 'From address is not in the allowed sender list. Add it in Admin Console → Emails.';
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'message' => $message], 422);
            }

            return redirect()->back()->with('error', $message)->withInput();
        }

        $eliteReplyTo = null;
        if ($request->boolean('_elite_compose') && EducationEliteMail::isEliteOwnedAddress($from)) {
            $eliteReplyTo = $this->resolveEliteComposeReplyTo();
        }

        $verifiedSenders = $this->sesSenderService->getComposeSenders(auth('admin')->id());
        $fromDisplayName = $this->displayNameForFrom($from, $verifiedSenders);
        $htmlBody = $body !== '' ? $body : '<p> </p>';
        $plainBody = $this->htmlToPlainTextForMail($htmlBody);
        $mailerName = $this->mailerNameForAddress($from);

        try {
            /** @var LaravelMailer $mailer */
            $mailer = Mail::mailer($mailerName);
            $sent = $mailer->html($htmlBody, function ($message) use ($from, $fromDisplayName, $toList, $ccList, $subject, $plainBody, $eliteReplyTo) {
                $message->to($toList)->from($from, $fromDisplayName)->subject($subject);
                $message->text($plainBody);
                if ($eliteReplyTo !== null) {
                    $message->replyTo($eliteReplyTo);
                }
                if (count($ccList) > 0) {
                    $message->cc($ccList);
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
                    'to' => $toDisplay,
                    'mailer' => $mailerName,
                ]);
                throw new \RuntimeException('Email could not be sent (message was blocked or not transmitted).');
            }

            try {
                Email::create([
                    'user_id' => auth('admin')->id(),
                    'from_mail' => $from,
                    'to_mail' => $toDisplay,
                    'cc' => count($ccList) > 0 ? implode(', ', $ccList) : null,
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
            Log::error('Email sending error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            if ($request->boolean('_elite_compose') || $request->wantsJson()) {
                return response()->json(['ok' => false, 'message' => 'Failed to send email: '.$e->getMessage()], 500);
            }

            return redirect()->route('dashboard')
                ->with('error', 'Failed to send email: '.$e->getMessage())
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
            'to' => 'nullable|string|max:2000',
            'cc' => 'nullable|string|max:2000',
            'subject' => 'nullable|string|max:500',
            'body' => 'nullable|string',
        ]);

        if ($request->filled('to')) {
            $this->parseEmailList($validated['to'], 'to', false);
        }
        if ($request->filled('cc')) {
            $this->parseEmailList($validated['cc'], 'cc', false);
        }

        OutlookDraftEmail::create([
            'from_email' => $validated['from'],
            'to_email' => $request->filled('to') ? trim($validated['to']) : null,
            'cc' => $request->filled('cc') ? trim($validated['cc']) : null,
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

    /**
     * Parse comma/semicolon-separated recipient lists.
     *
     * @return list<string>
     */
    private function parseEmailList(?string $value, string $field, bool $required = false): array
    {
        if ($value === null || trim($value) === '') {
            if ($required) {
                throw ValidationException::withMessages([
                    $field => ['At least one recipient is required.'],
                ]);
            }

            return [];
        }

        $parts = preg_split('/[,;]+/', $value) ?: [];
        $emails = [];
        foreach ($parts as $part) {
            $email = $this->normalizeRecipientAddress($part);
            if ($email === '') {
                continue;
            }
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw ValidationException::withMessages([
                    $field => ['Invalid email address: '.$email],
                ]);
            }
            $emails[] = $email;
        }

        if ($required && $emails === []) {
            throw ValidationException::withMessages([
                $field => ['At least one recipient is required.'],
            ]);
        }

        return $emails;
    }

    private function normalizeRecipientAddress(string $part): string
    {
        $part = trim($part);
        if ($part === '') {
            return '';
        }
        if (preg_match('/<([^>]+)>/', $part, $matches)) {
            return trim($matches[1]);
        }

        return $part;
    }
}
