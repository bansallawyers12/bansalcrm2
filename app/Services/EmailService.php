<?php

namespace App\Services;

use App\Models\FromEmail;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /** Default mailer for all CRM emails - AWS SES */
    protected const DEFAULT_MAILER = 'ses';

    /**
     * Get the first active email (default for system emails).
     */
    public function getDefaultEmail(): ?FromEmail
    {
        return FromEmail::where('status', true)->orderBy('id')->first();
    }

    /**
     * Resolve email config for From address (email + display_name).
     */
    public function configureMailerForEmail(?string $emailAddress = null): ?object
    {
        $emailConfig = null;

        if ($emailAddress && trim($emailAddress) !== '') {
            $trimmed = trim($emailAddress);
            $emailConfig = FromEmail::where('status', true)
                ->whereRaw('LOWER(TRIM(email)) = ?', [strtolower($trimmed)])
                ->first();
        }

        if (! $emailConfig) {
            $envFrom = env('MAIL_FROM_ADDRESS');
            if ($envFrom) {
                return (object) [
                    'email' => $envFrom,
                    'display_name' => env('MAIL_FROM_NAME', $envFrom),
                ];
            }

            return $this->getDefaultEmail();
        }

        return $emailConfig;
    }

    public function getAllActiveEmails()
    {
        return FromEmail::where('status', true)
            ->select('id', 'email', 'display_name')
            ->get();
    }

    /**
     * Send an email via AWS SES.
     *
     * @throws \Exception
     */
    public function sendEmail(
        $view,
        $data,
        $to,
        $subject,
        $fromEmailAddress,
        $attachments = [],
        $cc = []
    ): void {
        try {
            $trimmed = trim((string) $fromEmailAddress);
            if ($trimmed === '') {
                throw new \Exception('From email address is required.');
            }
            $emailConfig = FromEmail::where('status', true)
                ->whereRaw('LOWER(TRIM(email)) = ?', [strtolower($trimmed)])
                ->first();
            if (! $emailConfig) {
                if (! filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception("Invalid From email address: {$trimmed}");
                }
                $emailConfig = (object) [
                    'email' => $trimmed,
                    'display_name' => $trimmed,
                ];
            }

            Log::info('EmailService - Sending Email via SES', [
                'from_email' => $emailConfig->email,
                'to' => $to,
                'subject' => $subject,
            ]);

            $mailer = app(SesSenderService::class)->mailerForAddress($emailConfig->email);

            Mail::mailer($mailer)->send($view, $data, function (Message $message) use ($to, $subject, $emailConfig, $attachments, $cc) {
                $message->to($to)
                    ->subject($subject)
                    ->from($emailConfig->email, $emailConfig->display_name ?? $emailConfig->email);

                if (! empty($cc)) {
                    $message->cc($cc);
                }

                if (! empty($attachments)) {
                    foreach ($attachments as $attachment) {
                        if (is_string($attachment) && file_exists($attachment)) {
                            $message->attach($attachment);
                        }
                    }
                }
            });

            Log::info('EmailService - Email Sent Successfully', [
                'from' => $emailConfig->email,
                'to' => $to,
            ]);
        } catch (\Exception $e) {
            Log::error('EmailService - Send Failed', [
                'error' => $e->getMessage(),
                'from_email' => $fromEmailAddress ?? 'unknown',
                'to' => $to ?? 'unknown',
            ]);
            throw new \Exception('Email could not be sent: '.$e->getMessage());
        }
    }
}
