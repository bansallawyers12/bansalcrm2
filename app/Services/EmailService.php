<?php

namespace App\Services;

use App\Models\Email;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class EmailService
{
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
            //dd($view, $data, $to, $subject, $fromEmailId);
            $emailConfig = Email::where('email', $fromEmailId)->firstOrFail();//dd($emailConfig);

            // Configure mail settings for this specific email
            // Zoho: smtp.zoho.com = personal (@zoho.com); smtppro.zoho.com = business/custom domain (@yourdomain.com)
            $isCustomDomain = strpos($emailConfig->email, '@zoho.') === false;
            $smtpHost = $isCustomDomain ? 'smtppro.zoho.com' : 'smtp.zoho.com';
            config([
                'mail.mailers.smtp.host' => $smtpHost,
                'mail.mailers.smtp.port' => 587,
                'mail.mailers.smtp.encryption' => 'tls',
                'mail.mailers.smtp.username' => trim($emailConfig->email),
                'mail.mailers.smtp.password' => trim((string) $emailConfig->password),
                'mail.from.address' => trim($emailConfig->email),
                'mail.from.name' => trim((string) $emailConfig->display_name),
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

            return true;
        } catch (\Exception $e) {
            throw new \Exception('Email could not be sent: ' . $e->getMessage());
        }
    }
}
