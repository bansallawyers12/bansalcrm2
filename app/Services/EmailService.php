<?php

namespace App\Services;

use App\Models\Email;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Message;

class EmailService
{
    /** Zoho SMTP settings - both domains are hosted on Zoho */
    protected const SMTP_HOST = 'smtp.zoho.com.au';
    protected const SMTP_PORT = 587;
    protected const SMTP_ENCRYPTION = 'tls';

    /**
     * Get the first active email (default for system emails).
     *
     * @return \App\Models\Email|null
     */
    public function getDefaultEmail(): ?Email
    {
        return Email::where('status', true)->orderBy('id')->first();
    }

    /**
     * Configure the mailer for sending emails.
     * - When From email is explicitly provided and exists in emails table: use that email's credentials from DB.
     * - When no From email is provided: use .env (MAIL_*) credentials.
     *
     * @param string|null $emailAddress Email address to use (must exist in emails table when provided)
     * @return \App\Models\Email|object|null The email config (Email model or object with email, display_name), or null
     */
    public function configureMailerForEmail(?string $emailAddress = null): ?object
    {
        $emailConfig = null;

        // Explicit From email provided: use credentials from emails table
        if ($emailAddress && trim($emailAddress) !== '') {
            $trimmed = trim($emailAddress);
            $emailConfig = Email::where('status', true)
                ->whereRaw('LOWER(TRIM(email)) = ?', [strtolower($trimmed)])
                ->first();
        }

        // No explicit From: use .env credentials (default)
        if (!$emailConfig) {
            $envUser = env('MAIL_USERNAME');
            $envPass = env('MAIL_PASSWORD');
            if ($envUser && $envPass !== null) {
                $emailConfig = (object) [
                    'email' => env('MAIL_FROM_ADDRESS', $envUser),
                    'display_name' => env('MAIL_FROM_NAME', $envUser),
                ];
                $host = env('MAIL_HOST', self::SMTP_HOST);
                $port = (int) (env('MAIL_PORT') ?: self::SMTP_PORT);
                $encryption = env('MAIL_ENCRYPTION', self::SMTP_ENCRYPTION);

                Config::set('mail.default', 'smtp');
                Config::set('mail.mailers.smtp', [
                    'transport' => 'smtp',
                    'host' => $host,
                    'port' => $port,
                    'encryption' => $encryption,
                    'username' => $envUser,
                    'password' => trim((string) $envPass),
                ]);
                Config::set('mail.from.address', $emailConfig->email);
                Config::set('mail.from.name', $emailConfig->display_name);

                app()->forgetInstance('mailer');
                app()->forgetInstance('mail.manager');

                return $emailConfig;
            }

            // Fallback: first active email from emails table (when .env not configured)
            $emailConfig = $this->getDefaultEmail();
        }

        if (!$emailConfig) {
            return null;
        }

        // Use credentials from emails table
        $password = is_string($emailConfig->password) ? trim($emailConfig->password) : '';

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp', [
            'transport' => 'smtp',
            'host' => self::SMTP_HOST,
            'port' => self::SMTP_PORT,
            'encryption' => self::SMTP_ENCRYPTION,
            'username' => trim($emailConfig->email),
            'password' => $password,
        ]);
        Config::set('mail.from.address', $emailConfig->email);
        Config::set('mail.from.name', $emailConfig->display_name ?? $emailConfig->email);

        app()->forgetInstance('mailer');
        app()->forgetInstance('mail.manager');

        return $emailConfig;
    }

    /**
     * Get all active email configurations.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllActiveEmails()
    {
        return Email::where('status', true)
            ->select('id', 'email', 'display_name')
            ->get();
    }

    /**
     * Send an email using the specified email configuration.
     *
     * @param string $view
     * @param array $data
     * @param string $to
     * @param string $subject
     * @param int $fromEmailId
     * @return bool
     * @throws \Exception
     */
    public function sendEmail($view, $data, $to, $subject, $fromEmailId, $attachments = [], $cc = [])
    {
        try {
            $trimmed = trim($fromEmailId);
            $emailConfig = Email::where('status', true)
                ->whereRaw('LOWER(TRIM(email)) = ?', [strtolower($trimmed)])
                ->first();
            if (!$emailConfig) {
                throw new \Exception("Email '{$fromEmailId}' not found in emails table. Add it in Admin Console → Emails.");
            }

            // Log email config from DB
            Log::info('EmailService - Sending Email', [
                'from_email' => $emailConfig->email,
                'password_length' => strlen($emailConfig->password ?? ''),
                'to' => $to,
                'subject' => $subject,
            ]);

            // Configure mailer from emails table (not .env)
            $this->configureMailerForEmail($emailConfig->email);

            // Log SMTP config after setting
            Log::info('EmailService - SMTP Config', [
                'host' => Config::get('mail.mailers.smtp.host'),
                'port' => Config::get('mail.mailers.smtp.port'),
                'username' => Config::get('mail.mailers.smtp.username'),
                'encryption' => Config::get('mail.mailers.smtp.encryption'),
            ]);

            // Send the email
            Mail::send($view, $data, function (Message $message) use ($to, $subject, $emailConfig, $attachments, $cc) {
                $message->to($to)
                    ->subject($subject)
                    ->from($emailConfig->email, $emailConfig->display_name);

                if (!empty($cc)) {
                    $message->cc($cc);
                }

                if (!empty($attachments)) {
                    foreach ($attachments as $attachment) {
                        if (file_exists($attachment)) {
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
                'from_email_id' => $fromEmailId ?? 'unknown',
                'to' => $to ?? 'unknown',
            ]);
            throw new \Exception('Email could not be sent: ' . $e->getMessage());
        }
    }
}
