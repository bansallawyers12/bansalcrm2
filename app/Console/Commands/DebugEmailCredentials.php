<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FromEmail;

/**
 * Debug email configuration for SendGrid troubleshooting.
 * Run: php artisan email:debug info@example.com
 */
class DebugEmailCredentials extends Command
{
    protected $signature = 'email:debug {email? : Email address to debug (e.g. info@example.com)}';
    protected $description = 'Debug email configuration in DB (From addresses) for SendGrid';

    public function handle()
    {
        $email = $this->argument('email');

        $this->info('=== Email Configuration Debug (SendGrid) ===');
        $this->newLine();

        // List all active emails if none specified
        if (!$email) {
            $emails = FromEmail::where('status', true)->orderBy('id')->get();
            $this->info('Active From addresses in DB:');
            foreach ($emails as $e) {
                $this->line("  - {$e->email} | Display: " . ($e->display_name ?? '(empty)'));
            }
            $this->newLine();
            $this->info('Note: SendGrid uses SENDGRID_API_KEY from .env. Password column in DB is not used.');
            $this->info('Run with: php artisan email:debug <email>');
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
        $this->info('SendGrid authentication uses SENDGRID_API_KEY from .env.');
        $this->info('Ensure this From address is verified in SendGrid: Settings → Sender Authentication');
        $this->newLine();

        return 0;
    }
}
