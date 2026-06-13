<?php

namespace App\Console\Commands;

use App\Models\FromEmail;
use App\Services\SesSenderService;
use Illuminate\Console\Command;

/**
 * Debug email configuration for AWS SES troubleshooting.
 * Run: php artisan email:debug info@example.com
 */
class DebugEmailCredentials extends Command
{
    protected $signature = 'email:debug {email? : Email address to debug (e.g. info@example.com)}';

    protected $description = 'Debug email configuration in DB (From addresses) for AWS SES';

    public function handle(SesSenderService $sesSenderService): int
    {
        $email = $this->argument('email');

        $this->info('=== Email Configuration Debug (AWS SES) ===');
        $this->newLine();

        if (! $sesSenderService->isConfigured()) {
            $this->warn('AWS SES credentials not set (AWS_ACCESS_KEY_ID / AWS_SECRET_ACCESS_KEY).');
        } else {
            $this->info('AWS SES credentials: configured');
        }
        $this->newLine();

        if (! $email) {
            $emails = FromEmail::where('status', true)->orderBy('id')->get();
            $this->info('Active From addresses in DB:');
            foreach ($emails as $e) {
                $this->line("  - {$e->email} | Display: ".($e->display_name ?? '(empty)'));
            }
            $this->newLine();
            $this->info('Compose From dropdown (Admin Console → Emails, active only):');
            foreach ($sesSenderService->getComposeSenders() as $sender) {
                $this->line('  - '.($sender['email'] ?? ''));
            }
            $this->newLine();
            $this->info('Note: SES uses AWS credentials from .env. Password column in DB is not used.');
            $this->info('Run with: php artisan email:debug <email>');

            return 0;
        }

        $record = FromEmail::whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($email))])->first();

        if (! $record) {
            $this->error("No record found for: {$email}");
            $this->line('Trying exact match...');
            $record = FromEmail::where('email', $email)->first();
            if (! $record) {
                $this->error('Still not found. Add this email in Admin Console → Emails, or SES_SENDERS in .env.');
                return 1;
            }
        }

        $this->info("Found record for: {$record->email}");
        $this->line("  ID: {$record->id}");
        $this->line('  Display name: '.($record->display_name ?? '(empty)'));
        $this->line("  Status: {$record->status}");
        $this->newLine();
        $this->info('Ensure this From address is verified in AWS SES (identity or domain).');
        $this->newLine();

        return 0;
    }
}
