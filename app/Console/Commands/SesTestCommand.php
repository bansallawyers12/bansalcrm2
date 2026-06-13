<?php

namespace App\Console\Commands;

use App\Services\SesSenderService;
use App\Support\EducationEliteMail;
use Aws\SesV2\SesV2Client;
use Illuminate\Console\Command;

class SesTestCommand extends Command
{
    protected $signature = 'ses:test';

    protected $description = 'Test AWS SES credentials and list verified identities';

    public function handle(SesSenderService $sesSenderService): int
    {
        $key = config('services.ses.key');
        $secret = config('services.ses.secret');
        $region = config('services.ses.region', 'ap-southeast-2');

        if (empty($key) || empty($secret)) {
            $this->error('SES credentials not set. Add AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY to .env');
            $this->info('Then run: php artisan config:clear');

            return 1;
        }

        $this->info("Testing AWS SES (region: {$region})...");
        $this->newLine();

        $apex = EducationEliteMail::apexDomain();
        $credentialsOk = false;

        try {
            $client = new SesV2Client([
                'version' => 'latest',
                'region' => $region,
                'credentials' => [
                    'key' => $key,
                    'secret' => $secret,
                ],
            ]);

            $result = $client->listEmailIdentities(['PageSize' => 50]);
            $credentialsOk = true;
            $identities = $result->get('EmailIdentities') ?? [];

            if ($identities === []) {
                $this->warn('No email identities found in SES. Verify domains/addresses in the SES console.');
            } else {
                $this->info('SES identities:');
                foreach ($identities as $identity) {
                    $name = $identity['IdentityName'] ?? '(unknown)';
                    $type = $identity['IdentityType'] ?? '';
                    $this->line("  • {$name}".($type !== '' ? " ({$type})" : ''));
                }
            }
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'AccessDenied')) {
                $this->warn('SES credentials accepted but ListEmailIdentities is not allowed for this IAM user.');
                $this->line('  Sending only needs ses:SendEmail and ses:SendRawEmail.');
                $credentialsOk = true;
            } else {
                $this->error('SES API error: '.$e->getMessage());

                return 1;
            }
        }

        $this->newLine();
        $this->info('Compose From dropdown (Admin Console → Emails, active + staff sharing when run as a user):');
        foreach ($sesSenderService->getComposeSenders() as $sender) {
            $this->line('  • '.($sender['email'] ?? ''));
        }

        $this->newLine();
        $this->info('Elite mailer: '.config('crm.education_elite_mailer', 'ses_elite'));
        $this->line("Elite domain: @{$apex}");
        if ($credentialsOk) {
            $this->info('SES credentials: OK');
        }

        return 0;
    }
}
