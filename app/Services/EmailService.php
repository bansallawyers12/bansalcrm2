<?php

namespace App\Services;

use App\Models\FromEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Message;

class EmailService
{
    /** Default mailer for all CRM emails - AWS SES */
    protected const DEFAULT_MAILER = 'ses';

    /**
     * Get the first active email (default for system emails).
     *
     * @return \App\Models\FromEmail|null
     */
    public function getDefaultEmail(): ?FromEmail
    {
        return FromEmail::where('status', true)->orderBy('id')->first();
    }

    /**
     * Resolve email config for From address (email + display_name).
     * Uses from_emails table when address provided, else .env or first active email.
     * AWS SES uses IAM credentials from .env — no per-email SMTP credentials.
     *
     * @param string|null $emailAddress Email address to use (must exist in from_emails table when provided)
     * @return \App\Models\FromEmail|object|null The email config (email, display_name), or null
     */
    public function configureMailerForEmail(?string $emailAddress = null): ?object
    {
        $emailConfig = null;

        // Explicit From email provided: use from_emails table
        if ($emailAddress && trim($emailAddress) !== '') {
            $trimmed = trim($emailAddress);
            $emailConfig = FromEmail::where('status', true)
                ->whereRaw('LOWER(TRIM(email)) = ?', [strtolower($trimmed)])
                ->first();
        }

        // No explicit From: use .env or first active email from DB
        if (!$emailConfig) {
            $envFrom = env('MAIL_FROM_ADDRESS');
            if ($envFrom) {
                $emailConfig = (object) [
                    'email' => $envFrom,
                    'display_name' => env('MAIL_FROM_NAME', $envFrom),
                ];
                return $emailConfig;
            }
            $emailConfig = $this->getDefaultEmail();
        }

        return $emailConfig;
    }

    /**
     * Get all active email configurations.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllActiveEmails()
    {
        return FromEmail::where('status', true)
            ->select('id', 'email', 'display_name')
            ->get();
    }

    /**
     * Send an email via AWS SES.
     *
     * @param string $view
     * @param array $data
     * @param string $to
     * @param string $subject
     * @param string $fromEmailAddress From email (must exist in from_emails table)
     * @param array $attachments
     * @param array $cc
     * @return bool
     * @throws \Exception
     */
    public function sendEmail($view, $data, $to, $subject, $fromEmailAddress, $attachments = [], $cc = [])
    {
        try {
            $trimmed = trim((string) $fromEmailAddress);
            if ($trimmed === '') {
                throw new \Exception('From email address is required.');
            }
            $emailConfig = FromEmail::where('status', true)
                ->whereRaw('LOWER(TRIM(email)) = ?', [strtolower($trimmed)])
                ->first();
            if (!$emailConfig) {
                // Allow SES verified senders (From dropdown is populated from SES API / SES_SENDERS)
                if (! filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception("Invalid From email address: {$trimmed}");
                }
                $emailConfig = (object) [
                    'email' => $trimmed,
                    'display_name' => $trimmed,
                ];
            }

            Log::info('EmailService - Sending Email via AWS SES', [
                'from_email' => $emailConfig->email,
                'to' => $to,
                'subject' => $subject,
            ]);

            Mail::mailer(self::DEFAULT_MAILER)->send($view, $data, function (Message $message) use ($to, $subject, $emailConfig, $attachments, $cc) {
                $message->to($to)
                    ->subject($subject)
                    ->from($emailConfig->email, $emailConfig->display_name ?? $emailConfig->email);

                if (!empty($cc)) {
                    $message->cc($cc);
                }

                if (!empty($attachments)) {
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

            return true;
        } catch (\Exception $e) {
            Log::error('EmailService - Send Failed', [
                'error' => $e->getMessage(),
                'from_email' => $fromEmailAddress ?? 'unknown',
                'to' => $to ?? 'unknown',
            ]);
            throw new \Exception('Email could not be sent: ' . $e->getMessage());
        }
    }
}
