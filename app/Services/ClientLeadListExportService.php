<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Application;
use App\Models\ClientEmail;
use App\Models\ClientPhone;
use App\Models\ClientTestScore;
use App\Models\Staff;
use App\Support\StaffAssigneeResolver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class ClientLeadListExportService
{
    public const EXPORT_LIMIT = 10000;

    public const CHUNK_SIZE = 500;

    public const FLUSH_INTERVAL = 50;

    /** @var array<string, bool>|null */
    protected ?array $schemaFlags = null;

    /**
     * Export list as CSV, or as a ZIP of batch CSV files when over the per-file limit.
     */
    public function export(Builder $query, string $recordType, string $filenamePrefix): StreamedResponse
    {
        $totalMatching = $this->countMatching($query);
        $batchCount = $this->calculateBatchCount($totalMatching);

        if ($batchCount <= 1) {
            return $this->streamCsv($query, $recordType, $filenamePrefix, $totalMatching);
        }

        return $this->streamZipBatches($query, $recordType, $filenamePrefix, $totalMatching, $batchCount);
    }

    public function calculateBatchCount(int $totalMatching): int
    {
        if ($totalMatching <= 0) {
            return 0;
        }

        return (int) ceil($totalMatching / self::EXPORT_LIMIT);
    }

    /**
     * @return list<string>
     */
    public function getHeaders(string $recordType): array
    {
        $headers = [
            'Client ID',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Country Code',
            'Type',
            'Status',
            'Lead Status',
            'Follow-up Date',
            'Source',
            'Tag Name',
            'DOB',
            'Gender',
            'Marital Status',
            'Address',
            'City',
            'State',
            'Country',
            'Zip',
            'Passport Number',
            'Passport Country',
            'Passport Issue Date',
            'Passport Expiry',
            'Visa Type',
            'Visa Description',
            'Visa Expiry',
            'Is Company',
            'Company Name',
            'Company Website',
            'Assigned Staff',
            'Agent ID',
            'Additional Addresses',
            'Additional Contacts',
            'Additional Emails',
            'Travel History',
            'Visa History',
            'Character Records',
            'Test Scores',
        ];

        if ($recordType === 'client') {
            $headers[] = 'Active Matters Count';
        }

        $headers[] = 'Created At';
        $headers[] = 'Updated At';

        return $headers;
    }

    public function streamCsv(Builder $query, string $recordType, string $filenamePrefix, ?int $totalMatching = null): StreamedResponse
    {
        $headers = $this->getHeaders($recordType);
        $filename = $filenamePrefix . '_' . date('Y-m-d_His') . '.csv';
        $totalMatching = $totalMatching ?? $this->countMatching($query);

        return response()->streamDownload(function () use ($query, $headers, $recordType, $totalMatching) {
            @set_time_limit(0);

            $out = fopen('php://output', 'w');
            $this->writeCsvBatch($out, $query, $headers, $recordType, $totalMatching, 1, 1, 0, $totalMatching, true);
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'X-Export-Total-Matching' => (string) $totalMatching,
            'X-Export-Expected-Count' => (string) $totalMatching,
            'X-Export-Batch-Count' => '1',
            'X-Export-Limit' => (string) self::EXPORT_LIMIT,
            'X-Export-Capped' => '0',
        ]);
    }

    protected function streamZipBatches(
        Builder $query,
        string $recordType,
        string $filenamePrefix,
        int $totalMatching,
        int $batchCount
    ): StreamedResponse {
        $filename = $filenamePrefix . '_' . date('Y-m-d_His') . '_batches.zip';
        $headers = $this->getHeaders($recordType);

        return response()->streamDownload(function () use ($query, $recordType, $filenamePrefix, $totalMatching, $batchCount, $headers) {
            @set_time_limit(0);

            $zipPath = tempnam(sys_get_temp_dir(), 'crm_export_zip_');
            if ($zipPath === false) {
                throw new \RuntimeException('Unable to create temporary export file.');
            }

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                @unlink($zipPath);
                throw new \RuntimeException('Unable to create export ZIP archive.');
            }

            for ($batchNumber = 1; $batchNumber <= $batchCount; $batchNumber++) {
                $offset = ($batchNumber - 1) * self::EXPORT_LIMIT;
                $batchLimit = min(self::EXPORT_LIMIT, $totalMatching - $offset);
                $csvContent = $this->buildCsvBatchString(
                    $query,
                    $headers,
                    $recordType,
                    $totalMatching,
                    $batchNumber,
                    $batchCount,
                    $offset,
                    $batchLimit
                );

                $zip->addFromString(
                    sprintf('%s_batch_%d_of_%d.csv', $filenamePrefix, $batchNumber, $batchCount),
                    $csvContent
                );
            }

            $zip->close();

            $handle = fopen($zipPath, 'rb');
            if ($handle !== false) {
                while (! feof($handle)) {
                    echo fread($handle, 8192);
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                }
                fclose($handle);
            }

            @unlink($zipPath);
        }, $filename, [
            'Content-Type' => 'application/zip',
            'X-Export-Total-Matching' => (string) $totalMatching,
            'X-Export-Expected-Count' => (string) $totalMatching,
            'X-Export-Batch-Count' => (string) $batchCount,
            'X-Export-Limit' => (string) self::EXPORT_LIMIT,
            'X-Export-Capped' => '0',
        ]);
    }

    /**
     * @param  resource  $out
     */
    protected function writeCsvBatch(
        $out,
        Builder $query,
        array $headers,
        string $recordType,
        int $totalMatching,
        int $batchNumber,
        int $batchCount,
        int $offset,
        int $batchLimit,
        bool $flushStream = false
    ): int {
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, $headers);

        $rowsSinceFlush = 0;
        $exportedCount = $this->chunkRecords(
            $this->buildBatchQuery($query, $offset),
            $recordType,
            function (Admin $admin, array $batch) use ($out, $recordType, $flushStream, &$rowsSinceFlush) {
                fputcsv($out, $this->buildRow($admin, $recordType, $batch));

                if ($flushStream) {
                    $rowsSinceFlush++;
                    if ($rowsSinceFlush >= self::FLUSH_INTERVAL) {
                        $this->flushOutputStream();
                        $rowsSinceFlush = 0;
                    }
                }
            },
            $batchLimit
        );

        if ($flushStream && $rowsSinceFlush > 0) {
            $this->flushOutputStream();
        }

        $this->writeExportSummary($out, $totalMatching, $exportedCount, $batchNumber, $batchCount);

        return $exportedCount;
    }

    protected function buildCsvBatchString(
        Builder $query,
        array $headers,
        string $recordType,
        int $totalMatching,
        int $batchNumber,
        int $batchCount,
        int $offset,
        int $batchLimit
    ): string {
        $handle = fopen('php://temp', 'r+');
        if ($handle === false) {
            throw new \RuntimeException('Unable to create temporary CSV buffer.');
        }

        $this->writeCsvBatch($handle, $query, $headers, $recordType, $totalMatching, $batchNumber, $batchCount, $offset, $batchLimit);
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $content === false ? '' : $content;
    }

    protected function buildBatchQuery(Builder $query, int $offset): Builder
    {
        $batchQuery = (clone $query)->orderBy('id');

        if ($offset <= 0) {
            return $batchQuery;
        }

        $startId = (clone $query)->orderBy('id')->offset($offset)->limit(1)->value('id');
        if ($startId !== null) {
            $batchQuery->where('id', '>=', $startId);
        }

        return $batchQuery;
    }

    public function countMatching(Builder $query): int
    {
        return (int) (clone $query)->count();
    }

    protected function writeExportSummary(
        $out,
        int $totalMatching,
        int $exportedCount,
        ?int $batchNumber = null,
        ?int $batchTotal = null
    ): void {
        fputcsv($out, []);
        fputcsv($out, ['Export Summary']);
        if ($batchNumber !== null && $batchTotal !== null) {
            fputcsv($out, ['Export batch', $batchNumber . ' of ' . $batchTotal]);
            fputcsv($out, ['Records in this file', $exportedCount]);
        }
        fputcsv($out, ['Total matching records', $totalMatching]);
        fputcsv($out, ['Total exported in this file', $exportedCount]);
        fputcsv($out, [
            'Export complete for this file',
            $exportedCount > 0 ? 'Yes' : 'No',
        ]);
        if ($batchNumber !== null && $batchTotal !== null && $batchTotal > 1) {
            fputcsv($out, [
                'All batches required',
                'Yes (' . $batchTotal . ' files in ZIP download)',
            ]);
        }
        fputcsv($out, ['Exported at', now()->format('Y-m-d H:i:s')]);
    }

    /**
     * @return int Number of rows exported
     */
    protected function chunkRecords(Builder $query, string $recordType, callable $callback, ?int $maxRecords = null): int
    {
        $count = 0;
        $staffNames = [];
        $limit = $maxRecords ?? self::EXPORT_LIMIT;

        (clone $query)
            ->with(['visaType'])
            ->orderBy('id')
            ->chunkById(self::CHUNK_SIZE, function ($chunk) use ($callback, $recordType, &$count, &$staffNames, $limit) {
                $clientIds = $chunk->pluck('id')->map(fn ($id) => (int) $id)->all();

                $assigneeIds = $chunk->pluck('assignee')
                    ->map(fn ($value) => $this->firstAssigneeStaffId($value))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                $missingIds = array_diff($assigneeIds, array_keys($staffNames));

                if ($missingIds !== []) {
                    Staff::query()
                        ->whereIn('id', $missingIds)
                        ->get(['id', 'first_name', 'last_name'])
                        ->each(function (Staff $staff) use (&$staffNames) {
                            $staffNames[$staff->id] = trim($staff->first_name . ' ' . $staff->last_name);
                        });
                }

                $batch = $this->loadBatchContext($clientIds, $recordType);

                foreach ($chunk as $admin) {
                    if ($count >= $limit) {
                        return false;
                    }

                    $assigneeId = $this->firstAssigneeStaffId($admin->assignee);
                    $admin->assignedStaffName = $assigneeId
                        ? ($staffNames[$assigneeId] ?? '')
                        : '';

                    $callback($admin, $batch);
                    $count++;
                }
            });

        return $count;
    }

    protected function firstAssigneeStaffId(mixed $value): ?int
    {
        $staff = StaffAssigneeResolver::firstStaffFromAssigneeValue($value);

        return $staff?->id;
    }

    /**
     * Preload related records for a chunk of client/lead IDs.
     *
     * @param  list<int>  $clientIds
     * @return array<string, mixed>
     */
    /**
     * Resolve table/column availability once per export request.
     *
     * @return array<string, bool>
     */
    protected function schemaFlags(): array
    {
        if ($this->schemaFlags !== null) {
            return $this->schemaFlags;
        }

        $this->schemaFlags = [
            'client_phones' => Schema::hasTable('client_phones'),
            'client_emails' => Schema::hasTable('client_emails'),
            'client_testscore' => Schema::hasTable('client_testscore'),
            'applications' => Schema::hasTable('applications'),
            'lead_status' => Schema::hasColumn('admins', 'lead_status'),
            'followup_date' => Schema::hasColumn('admins', 'followup_date'),
            'is_company' => Schema::hasColumn('admins', 'is_company'),
        ];

        return $this->schemaFlags;
    }

    protected function flushOutputStream(): void
    {
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
    }

    protected function loadBatchContext(array $clientIds, string $recordType): array
    {
        if ($clientIds === []) {
            return [
                'contacts' => collect(),
                'emails' => collect(),
                'test_scores' => collect(),
                'matter_counts' => collect(),
            ];
        }

        $schema = $this->schemaFlags();

        $contacts = collect();
        if ($schema['client_phones']) {
            $contacts = ClientPhone::query()
                ->whereIn('client_id', $clientIds)
                ->get()
                ->groupBy('client_id');
        }

        $emails = collect();
        if ($schema['client_emails']) {
            $emails = ClientEmail::query()
                ->whereIn('client_id', $clientIds)
                ->get()
                ->groupBy('client_id');
        }

        $testScores = collect();
        if ($schema['client_testscore']) {
            $testScores = ClientTestScore::query()
                ->whereIn('client_id', $clientIds)
                ->orderByDesc('updated_at')
                ->get()
                ->groupBy('client_id');
        }

        $matterCounts = collect();
        if ($recordType === 'client' && $schema['applications']) {
            $matterCounts = Application::query()
                ->selectRaw('client_id, COUNT(*) as aggregate')
                ->whereIn('client_id', $clientIds)
                ->where('status', 0)
                ->groupBy('client_id')
                ->pluck('aggregate', 'client_id');
        }

        return [
            'contacts' => $contacts,
            'emails' => $emails,
            'test_scores' => $testScores,
            'matter_counts' => $matterCounts,
        ];
    }

    /**
     * @param  array<string, mixed>|null  $batch
     * @return list<scalar|null>
     */
    public function buildRow(Admin $admin, string $recordType, ?array $batch = null): array
    {
        if ($batch === null) {
            $batch = $this->loadBatchContext([(int) $admin->id], $recordType);
        }

        $schema = $this->schemaFlags();
        $clientId = (int) $admin->id;
        $visaType = $admin->visaType?->name ?? ($admin->visa_type ?? null);
        $visaDescription = $admin->visa_opt ?? null;
        $visaExpiry = $this->formatDateValue($admin->visaExpiry ?? null);

        $row = [
            $admin->client_id,
            $admin->first_name,
            $admin->last_name,
            $admin->email,
            $admin->phone,
            $admin->country_code,
            $admin->type,
            $this->formatStatus($admin->status),
            $schema['lead_status'] ? ($admin->lead_status ?? null) : null,
            $schema['followup_date'] ? $this->formatDateTime($admin->followup_date ?? null) : null,
            $admin->source ?? null,
            $admin->tagname ?? null,
            $admin->dob ?? null,
            $admin->gender ?? null,
            $admin->marital_status ?? null,
            $admin->address ?? null,
            $admin->city ?? null,
            $admin->state ?? null,
            $admin->country ?? null,
            $admin->zip ?? null,
            $admin->passport_number ?? null,
            $admin->country_passport ?? null,
            null,
            null,
            $visaType,
            $visaDescription,
            $visaExpiry,
            ($schema['is_company'] && $admin->is_company) ? 'Yes' : 'No',
            null,
            null,
            $admin->assignedStaffName ?? '',
            $admin->agent_id ?? null,
            '',
            $this->formatContacts($this->mapContacts($batch['contacts'][$clientId] ?? collect())),
            $this->formatEmails($this->mapEmails($batch['emails'][$clientId] ?? collect(), $admin)),
            '',
            $this->formatVisaHistory($visaType, $visaDescription, $visaExpiry, $admin->visa_grant_date ?? null),
            '',
            $this->formatTestScores($this->mapTestScores($batch['test_scores'][$clientId] ?? collect())),
        ];

        if ($recordType === 'client') {
            $row[] = (int) ($batch['matter_counts'][$clientId] ?? 0);
        }

        $row[] = $this->formatDateTime($admin->created_at);
        $row[] = $this->formatDateTime($admin->updated_at);

        return $row;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function mapContacts(Collection $items): array
    {
        return $items->map(fn (ClientPhone $contact) => [
            'contact_type' => $contact->contact_type,
            'country_code' => $contact->client_country_code,
            'phone' => $contact->client_phone,
        ])->values()->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function mapEmails(Collection $items, Admin $admin): array
    {
        $emails = $items->map(fn (ClientEmail $email) => [
            'email_type' => $email->email_type,
            'email' => $email->client_email,
        ])->values()->all();

        if ($emails === [] && ! empty($admin->email)) {
            $emails[] = [
                'email_type' => $admin->email_type ?? 'Personal',
                'email' => $admin->email,
            ];
        }

        return $emails;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function mapTestScores(Collection $items): array
    {
        return $items->map(fn (ClientTestScore $score) => [
            'test_type' => $score->test_type,
            'listening' => $score->listening,
            'reading' => $score->reading,
            'writing' => $score->writing,
            'speaking' => $score->speaking,
            'overall_score' => $score->overall_score,
            'test_date' => $this->formatDateValue($score->test_date),
        ])->values()->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $contacts
     */
    protected function formatContacts(array $contacts): string
    {
        return collect($contacts)->map(function (array $contact) {
            $phone = trim(($contact['country_code'] ?? '') . ' ' . ($contact['phone'] ?? ''));

            return trim(($contact['contact_type'] ?? 'Phone') . ': ' . $phone);
        })->filter()->implode(' | ');
    }

    /**
     * @param  array<int, array<string, mixed>>  $emails
     */
    protected function formatEmails(array $emails): string
    {
        return collect($emails)->map(function (array $email) {
            return trim(($email['email_type'] ?? 'Email') . ': ' . ($email['email'] ?? ''));
        })->filter()->implode(' | ');
    }

    protected function formatVisaHistory(?string $visaType, ?string $visaDescription, ?string $visaExpiry, mixed $visaGrantDate): string
    {
        $parts = array_filter([
            $visaType,
            $visaDescription,
            $visaGrantDate ? 'Grant: ' . $this->formatDateValue($visaGrantDate) : null,
            $visaExpiry ? 'Expiry: ' . $visaExpiry : null,
        ]);

        return implode(', ', $parts);
    }

    /**
     * @param  array<int, array<string, mixed>>  $scores
     */
    protected function formatTestScores(array $scores): string
    {
        return collect($scores)->map(function (array $score) {
            $parts = array_filter([
                $score['test_type'] ?? null,
                isset($score['overall_score']) ? 'Overall: ' . $score['overall_score'] : null,
                isset($score['listening']) ? 'L:' . $score['listening'] : null,
                isset($score['reading']) ? 'R:' . $score['reading'] : null,
                isset($score['writing']) ? 'W:' . $score['writing'] : null,
                isset($score['speaking']) ? 'S:' . $score['speaking'] : null,
                $score['test_date'] ?? null,
            ]);

            return implode(', ', $parts);
        })->filter()->implode(' | ');
    }

    protected function formatStatus(mixed $status): string
    {
        if ($status === null || $status === '') {
            return '';
        }

        return (string) $status === '1' ? 'Active' : 'Inactive';
    }

    protected function formatDateTime(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        return (string) $value;
    }

    protected function formatDateValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_string($value) && $value !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return is_scalar($value) ? (string) $value : null;
        }
    }
}
