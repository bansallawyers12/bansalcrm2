<?php

namespace App\Services;

use App\Models\FromEmail;
use Aws\SesV2\SesV2Client;
use Illuminate\Support\Facades\Log;

/**
 * Resolve verified From addresses for CRM compose (AWS SES).
 */
class SesSenderService
{
    /**
     * @return list<array{email: string, name: string, nickname: string}>
     */
    public function listVerifiedSenders(): array
    {
        $senders = $this->fetchFromSesApi();

        if ($senders === []) {
            $senders = $this->sendersFromEnv();
        }

        $senders = $this->mergeFromDatabase($senders);

        return collect($senders)
            ->filter(fn (array $sender) => ! empty($sender['email']) && filter_var($sender['email'], FILTER_VALIDATE_EMAIL))
            ->unique('email')
            ->values()
            ->all();
    }

    /**
     * @return list<array{email: string, name: string, nickname: string}>
     */
    private function fetchFromSesApi(): array
    {
        $key = config('services.ses.key');
        $secret = config('services.ses.secret');
        $region = config('services.ses.region', 'ap-southeast-2');

        if (empty($key) || empty($secret)) {
            Log::warning('SES: AWS credentials not set in .env');

            return [];
        }

        try {
            $client = new SesV2Client([
                'version' => 'latest',
                'region' => $region,
                'credentials' => [
                    'key' => $key,
                    'secret' => $secret,
                ],
            ]);

            $senders = [];
            $nextToken = null;

            do {
                $params = ['PageSize' => 50];
                if ($nextToken !== null) {
                    $params['NextToken'] = $nextToken;
                }

                $result = $client->listEmailIdentities($params);

                foreach ($result->get('EmailIdentities') ?? [] as $identity) {
                    $identityName = (string) ($identity['IdentityName'] ?? '');
                    $identityType = (string) ($identity['IdentityType'] ?? '');

                    if ($identityType !== 'EMAIL_ADDRESS') {
                        continue;
                    }

                    if (! filter_var($identityName, FILTER_VALIDATE_EMAIL)) {
                        continue;
                    }

                    $normalized = strtolower(trim($identityName));
                    $senders[$normalized] = [
                        'email' => $normalized,
                        'name' => $identityName,
                        'nickname' => '',
                    ];
                }

                $nextToken = $result->get('NextToken');
            } while ($nextToken !== null);

            return array_values($senders);
        } catch (\Throwable $e) {
            Log::error('SES listEmailIdentities failed: '.$e->getMessage());

            return [];
        }
    }

    /**
     * @return list<array{email: string, name: string, nickname: string}>
     */
    private function sendersFromEnv(): array
    {
        $fallbackSenders = config('services.ses.senders');
        if (empty($fallbackSenders) || ! is_string($fallbackSenders)) {
            return [];
        }

        $list = [];
        foreach (array_filter(array_map('trim', explode(',', $fallbackSenders))) as $email) {
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            $normalized = strtolower($email);
            $list[] = [
                'email' => $normalized,
                'name' => $email,
                'nickname' => '',
            ];
        }

        return $list;
    }

    /**
     * @param  list<array{email: string, name: string, nickname: string}>  $senders
     * @return list<array{email: string, name: string, nickname: string}>
     */
    private function mergeFromDatabase(array $senders): array
    {
        $byEmail = [];
        foreach ($senders as $sender) {
            $byEmail[strtolower($sender['email'])] = $sender;
        }

        try {
            $rows = FromEmail::where('status', true)->get(['email', 'display_name']);
            foreach ($rows as $row) {
                $email = strtolower(trim((string) $row->email));
                if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }

                $displayName = trim((string) ($row->display_name ?? ''));
                if (isset($byEmail[$email])) {
                    if ($displayName !== '') {
                        $byEmail[$email]['name'] = $displayName;
                    }
                    continue;
                }

                $byEmail[$email] = [
                    'email' => $email,
                    'name' => $displayName !== '' ? $displayName : $email,
                    'nickname' => '',
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('SES: could not merge from_emails table: '.$e->getMessage());
        }

        return array_values($byEmail);
    }
}
