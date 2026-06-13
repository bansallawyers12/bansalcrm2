<?php

namespace App\Services;

use App\Models\FromEmail;
use App\Support\EducationEliteMail;
use Aws\SesV2\SesV2Client;
use Illuminate\Support\Facades\Log;

class SesSenderService
{
    /**
     * All verified From addresses for compose dropdowns (both @bansaleducation.com.au and @educationelite.com.au).
     *
     * Sources: Admin Console → Emails (from_emails), SES API, SES_SENDERS + SES_ELITE_SENDERS in .env.
     *
     * @return list<array{email: string, name: string, nickname: string}>
     */
    public function getComposeSenders(?int $adminId = null): array
    {
        $fromEnvCrm = $this->parseSenderEmails((string) config('services.ses_crm.senders', ''));
        $fromEnvElite = $this->parseSenderEmails((string) config('services.ses_elite.senders', ''));
        $fromApi = $this->listVerifiedEmailIdentitiesFromApi();
        $fromDb = FromEmail::where('status', true)
            ->orderBy('id')
            ->get()
            ->filter(fn (FromEmail $row) => $this->fromEmailVisibleToAdmin($row, $adminId))
            ->pluck('email')
            ->map(fn ($email) => strtolower(trim((string) $email)))
            ->filter()
            ->values()
            ->all();

        $defaultFromCrm = strtolower(trim((string) config('services.ses_crm.from_email', '')));
        if ($defaultFromCrm !== '' && filter_var($defaultFromCrm, FILTER_VALIDATE_EMAIL)) {
            array_unshift($fromEnvCrm, $defaultFromCrm);
        }

        $defaultFromElite = strtolower(trim((string) config('services.ses_elite.from_email', '')));
        if ($defaultFromElite !== '' && filter_var($defaultFromElite, FILTER_VALIDATE_EMAIL)) {
            array_unshift($fromEnvElite, $defaultFromElite);
        }

        $emails = array_values(array_unique(array_merge($fromEnvCrm, $fromEnvElite, $fromApi, $fromDb)));
        $list = [];

        foreach ($emails as $email) {
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            $normalized = strtolower($email);
            $list[$normalized] = [
                'email' => $normalized,
                'name' => $normalized,
                'nickname' => '',
            ];
        }

        $filtered = $this->filterComposeSenders(array_values($list));

        foreach ($filtered as &$sender) {
            $db = FromEmail::where('status', true)
                ->whereRaw('LOWER(TRIM(email)) = ?', [$sender['email']])
                ->first();
            if ($db && $db->display_name) {
                $sender['name'] = $db->display_name;
            }
        }
        unset($sender);

        return $filtered;
    }

    /**
     * @return list<array{email: string, name: string, nickname: string}>
     */
    public function getCrmSenders(?int $adminId = null): array
    {
        return $this->getComposeSenders($adminId);
    }

    public function isAllowedSenderDomain(string $email): bool
    {
        $email = strtolower(trim($email));
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return str_ends_with($email, '@bansaleducation.com.au')
            || EducationEliteMail::isEliteOwnedAddress($email)
            || $email === 'admission@bansalimmigration.com.au';
    }

    /**
     * Pick Laravel mailer based on From domain (both use AWS SES credentials).
     */
    public function mailerForAddress(string $email): string
    {
        return EducationEliteMail::isEliteOwnedAddress($email) ? 'ses_elite' : 'ses';
    }

    /**
     * @return list<string>
     */
    public function listVerifiedEmailIdentitiesFromApi(): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        try {
            $client = $this->client();
            $emails = [];
            $nextToken = null;

            do {
                $params = ['PageSize' => 50];
                if ($nextToken !== null) {
                    $params['NextToken'] = $nextToken;
                }
                $result = $client->listEmailIdentities($params);
                $identities = $result->get('EmailIdentities') ?? [];

                foreach ($identities as $identity) {
                    $name = strtolower(trim((string) ($identity['IdentityName'] ?? '')));
                    $type = (string) ($identity['IdentityType'] ?? '');

                    if ($type === 'EMAIL_ADDRESS' && filter_var($name, FILTER_VALIDATE_EMAIL)) {
                        $emails[] = $name;
                    }
                }

                $nextToken = $result->get('NextToken');
            } while ($nextToken !== null);

            return array_values(array_unique($emails));
        } catch (\Throwable $e) {
            Log::warning('SES listEmailIdentities failed: '.$e->getMessage());

            return [];
        }
    }

    public function isConfigured(): bool
    {
        $key = config('services.ses.key');
        $secret = config('services.ses.secret');

        return ! empty($key) && ! empty($secret);
    }

    /**
     * Limit Compose Email From dropdown to allowed sender domains.
     *
     * @param  list<array{email: string, name: string, nickname: string}>  $senders
     * @return list<array{email: string, name: string, nickname: string}>
     */
    public function filterComposeSenders(array $senders): array
    {
        $filtered = array_values(array_filter($senders, function (array $sender) {
            $email = strtolower(trim((string) ($sender['email'] ?? '')));

            return $this->isAllowedSenderDomain($email);
        }));

        usort($filtered, function (array $a, array $b) {
            $emailA = strtolower((string) ($a['email'] ?? ''));
            $emailB = strtolower((string) ($b['email'] ?? ''));

            if ($emailA === 'admission@bansalimmigration.com.au') {
                return -1;
            }
            if ($emailB === 'admission@bansalimmigration.com.au') {
                return 1;
            }

            return strcmp($emailA, $emailB);
        });

        return $filtered;
    }

    /**
     * Admin Console stores allowed staff on from_emails.user_id (JSON array of staff ids).
     */
    private function fromEmailVisibleToAdmin(FromEmail $row, ?int $adminId): bool
    {
        if ($adminId === null) {
            return true;
        }

        $raw = $row->user_id;
        if ($raw === null || $raw === '' || $raw === '[]' || $raw === 'null') {
            return true;
        }

        $ids = is_array($raw) ? $raw : json_decode((string) $raw, true);
        if (! is_array($ids) || $ids === []) {
            return true;
        }

        $allowed = array_map(static fn ($id) => (string) $id, $ids);

        return in_array((string) $adminId, $allowed, true);
    }

    /**
     * @return list<string>
     */
    private function parseSenderEmails(string $raw): array
    {
        $emails = [];
        foreach (array_filter(array_map('trim', explode(',', $raw))) as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emails[] = strtolower($email);
            }
        }

        return $emails;
    }

    private function client(): SesV2Client
    {
        return new SesV2Client([
            'version' => 'latest',
            'region' => config('services.ses.region', 'ap-southeast-2'),
            'credentials' => [
                'key' => config('services.ses.key'),
                'secret' => config('services.ses.secret'),
            ],
        ]);
    }
}
