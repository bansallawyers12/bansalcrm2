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
    protected const SMTP_HOST = 'smtp.zoho.com';
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
     * Configure the mailer to use credentials from the emails table.
     * Uses the given email address if found in DB, otherwise uses first active email.
     *
     * @param string|null $emailAddress Email address to use (must exist in emails table)
     * @return \App\Models\Email|null The Email model used, or null if no config available
     */
    public function configureMailerForEmail(?string $emailAddress = null): ?Email
    {
        $emailConfig = null;
        if ($emailAddress) {
            $trimmed = trim($emailAddress);
            $emailConfig = Email::where('status', true)
                ->whereRaw('LOWER(TRIM(email)) = ?', [strtolower($trimmed)])
                ->first();
        }

        if (!$emailConfig) {
            $emailConfig = $this->getDefaultEmail();
        }

        if (!$emailConfig) {
            return null;
        }

        // Trim password - leading/trailing whitespace causes 535 auth failure
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
