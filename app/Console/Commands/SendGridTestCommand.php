<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SendGridTestCommand extends Command
{
    protected $signature = 'sendgrid:test';

    protected $description = 'Test SendGrid API and list verified sender emails';

    public function handle(): int
    {
        $apiKey = config('services.sendgrid.api_key');
        $baseUrl = rtrim(config('services.sendgrid.base_url', 'https://api.sendgrid.com'), '/');

        if (! $apiKey) {
            $this->error('SENDGRID_API_KEY not set in .env');
            $this->info('Add: SENDGRID_API_KEY=your_key');
            $this->info('Then run: php artisan config:clear');
            return 1;
        }

        $baseUrls = [$baseUrl, 'https://api.eu.sendgrid.com'];
        $baseUrls = array_unique($baseUrls);
        $this->info('Testing SendGrid API...');
        $this->newLine();

        $client = Http::withToken($apiKey)->timeout(15);
        $emails = [];

        foreach ($baseUrls as $testUrl) {
            $this->line("  Base URL: {$testUrl}");
            foreach (['verified_senders', 'senders', 'marketing/senders'] as $endpoint) {
                $url = "{$testUrl}/v3/{$endpoint}";
                $res = $client->get($url);
                $status = $res->status();

                if ($res->successful()) {
                    $key = ($endpoint === 'senders') ? 'result' : 'results';
                    $items = $res->json($key, []);
                    $count = count($items);

                    foreach ($items as $s) {
                        $email = $s['from_email'] ?? ($s['from']['email'] ?? null);
                        if ($email) {
                            $emails[$email] = true;
                        }
                    }

                    $this->info("    ✓ {$endpoint}: HTTP {$status}, found {$count} senders");
                } else {
                    $err = $res->json('errors.0.message') ?? $res->body();
                    $this->error("    ✗ {$endpoint}: HTTP {$status}");
                    $this->line("      {$err}");
                }
            }
            if (count($emails) > 0) {
                break;
            }
        }

        $this->newLine();
        $emails = array_keys($emails);

        if (count($emails) > 0) {
            $this->info('Emails for From dropdown:');
            foreach ($emails as $e) {
                $this->line("  • {$e}");
            }
        } else {
            $this->warn('No senders found. Try:');
            $this->line('  1. SENDGRID_BASE_URL=https://api.eu.sendgrid.com (if your account is EU)');
            $this->line('  2. Add Single Senders in SendGrid: Settings → Sender Authentication');
            $this->line('  3. Ensure API key has Full Access or Sender read permission');
        }

        return 0;
    }
}
