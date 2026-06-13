<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FromEmail;

/**
 * Debug email configuration for AWS SES troubleshooting.
 * Run: php artisan email:debug info@example.com
 */
class DebugEmailCredentials extends Command
{
    protected $signature = 'email:debug {email? : Email address to debug (e.g. info@example.com)}';
    protected $description = 'Debug email configuration in DB (From addresses) for AWS SES';

    public function handle()
    {
        $email = $this->argument('email');

        $this->info('=== Email Configuration Debug (AWS SES) ===');
        $this->newLine();

        // List all active emails if none specified
        if (!$email) {
            $emails = FromEmail::where('status', true)->orderBy('id')->get();
            $this->info('Active From addresses in DB:');
            foreach ($emails as $e) {
                $this->line("  - {$e->email} | Display: " . ($e->display_name ?? '(empty)'));
            }
            $this->newLine();
            $this->info('Note: AWS SES uses AWS_ACCESS_KEY_ID / AWS_SECRET_ACCESS_KEY from .env. Password column in DB is not used.');
            $this->info('Run with: php artisan email:debug <email>');
            $this->info('Run: php artisan ses:test');
            return 0;
        }

        // Case-insensitive lookup (trim too)
        $record = FromEmail::whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($email))])->first();

        if (!$record) {
            $this->error("No record found for: {$email}");
            $this->line('Trying exact match...');
            $record = FromEmail::where('email', $email)->first();
            if (!$record) {
                $this->error('Still not found. Add this email in Admin Console → Emails.');
                return 1;
            }
        }

        $this->info("Found record for: {$record->email}");
        $this->line("  ID: {$record->id}");
        $this->line("  Display name: " . ($record->display_name ?? '(empty)'));
        $this->line("  Status: {$record->status}");
        $this->newLine();
        $this->info('AWS SES uses IAM credentials from .env (AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, SES_REGION).');
        $this->info('Ensure this From address or its domain is verified in AWS SES.');
        $this->newLine();

        return 0;
    }
}
