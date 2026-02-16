<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Email;

/**
 * Debug email credentials for SMTP troubleshooting.
 * Run: php artisan email:debug info@bansaleducation.com.au
 */
class DebugEmailCredentials extends Command
{
    protected $signature = 'email:debug {email? : Email address to debug (e.g. info@bansaleducation.com.au)}';
    protected $description = 'Debug email credentials stored in DB for SMTP auth troubleshooting';

    public function handle()
    {
        $email = $this->argument('email');

        $this->info('=== Email Credentials Debug ===');
        $this->newLine();

        // List all active emails if none specified
        if (!$email) {
            $emails = Email::where('status', true)->orderBy('id')->get();
            $this->info('Active emails in DB:');
            foreach ($emails as $e) {
                $pwdLen = strlen($e->password ?? '');
                $pwdStatus = $pwdLen === 0 ? 'EMPTY' : ($pwdLen < 6 ? 'SHORT?' : "OK ({$pwdLen} chars)");
                $this->line("  - {$e->email} | password: {$pwdStatus}");
            }
            $this->newLine();
            $this->info('Run with: php artisan email:debug <email>');
            return 0;
        }

        // Case-insensitive lookup (trim too)
        $record = Email::whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($email))])->first();

        if (!$record) {
            $this->error("No record found for: {$email}");
            $this->line('Trying exact match...');
            $record = Email::where('email', $email)->first();
            if (!$record) {
                $this->error('Still not found. Check the exact email in Admin Console → Emails.');
                return 1;
            }
        }

        $this->info("Found record for: {$record->email}");
        $this->line("  ID: {$record->id}");
        $this->line("  Display name: " . ($record->display_name ?? '(empty)'));
        $this->line("  Status: {$record->status}");

        $pwd = $record->password ?? '';
        $pwdLen = strlen($pwd);

        $this->newLine();
        $this->info('Password check:');
        $this->line("  Length: {$pwdLen} characters");

        if ($pwdLen === 0) {
            $this->error('  ISSUE: Password is EMPTY. Edit this email and enter the correct Zoho password.');
            return 1;
        }

        if ($pwdLen < 8) {
            $this->warn('  WARNING: Password is very short. Zoho App Passwords are typically 16 chars.');
        }

        // Check for common issues
        $trimmed = trim($pwd);
        if ($trimmed !== $pwd) {
            $this->warn('  WARNING: Password has leading/trailing whitespace - may cause auth failure.');
        }

        $this->newLine();
        $this->info('SMTP will use: smtp.zoho.com:587 (TLS)');
        $this->line("  Username: {$record->email}");
        $this->line("  Password: [REDACTED - {$pwdLen} chars]");
        $this->newLine();

        $this->info('Common causes of 535 Authentication Failed:');
        $this->line('  1. Wrong password - verify in Zoho Mail settings');
        $this->line('  2. 2FA enabled - use Zoho App Password instead of account password');
        $this->line('  3. Domain not verified in Zoho');
        $this->line('  4. Account locked or SMTP disabled');
        $this->newLine();

        return 0;
    }
}
