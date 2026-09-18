<?php

namespace App\Services;

use App\Models\ActivitiesLog;
use App\Models\Admin;
use App\Models\Application;
use App\Models\ApplicationActivitiesLog;
use App\Models\Document;
use App\Models\Email;
use App\Models\Note;
use App\Models\Partner;
use App\Models\SmsLog;
use App\Models\StaffFileSession;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class StaffDayCrmEventsService
{
    public const LIST_CAP = 50;

    /** @var array<string, string|null> */
    protected array $labelCache = [];

    public function __construct(
        protected StaffWorkloadService $workloadService,
    ) {}

    /**
     * Read-only union of today's CRM writes by this staff. Does not write.
     *
     * @return array{items: list<array<string, mixed>>, total: int, more: int, date: string}
     */
    public function forStaff(int $staffId, ?Carbon $day = null, int $limit = self::LIST_CAP): array
    {
        [$start, $end] = $this->workloadService->dayBounds($day ?? now());
        $items = $this->collectEvents($staffId, $start, $end)
            ->sortByDesc(fn (array $row) => $row['sort_at'])
            ->values();

        $total = $items->count();
        $sliced = $items->take($limit)->map(function (array $row): array {
            unset($row['sort_at']);

            return $row;
        })->all();

        return [
            'items' => $sliced,
            'total' => $total,
            'more' => max(0, $total - count($sliced)),
            'date' => $start->toDateString(),
        ];
    }

    /**
     * CRM writes by this staff on one record within a time window (auto session promotion).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function forStaffOnRecord(
        int $staffId,
        string $recordType,
        int $recordId,
        ?int $applicationId,
        Carbon $start,
        Carbon $end,
    ): Collection {
        return $this->collectEvents($staffId, $start, $end)
            ->filter(function (array $row) use ($recordType, $recordId, $applicationId): bool {
                if (($row['record_type'] ?? null) !== $recordType) {
                    return false;
                }

                if ((int) ($row['record_id'] ?? 0) !== $recordId) {
                    return false;
                }

                if ($applicationId === null) {
                    return true;
                }

                $rowApp = $row['application_id'] ?? null;
                if ($rowApp === null || $rowApp === '') {
                    return true;
                }

                return (int) $rowApp === $applicationId;
            })
            ->sortBy(fn (array $row) => $row['sort_at'])
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function collectEvents(int $staffId, Carbon $start, Carbon $end): Collection
    {
        return collect()
            ->merge($this->emailEvents($staffId, $start, $end))
            ->merge($this->documentEvents($staffId, $start, $end))
            ->merge($this->smsEvents($staffId, $start, $end))
            ->merge($this->contactNoteEvents($staffId, $start, $end))
            ->merge($this->stageEvents($staffId, $start, $end))
            ->merge($this->feedEvents($staffId, $start, $end));
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function emailEvents(int $staffId, Carbon $start, Carbon $end): Collection
    {
        if (! Schema::hasTable('emails')) {
            return collect();
        }

        return Email::query()
            ->where('user_id', $staffId)
            ->whereBetween('created_at', [$start, $end])
            ->where(function ($q) {
                $q->whereNull('conversion_type')
                    ->orWhere('conversion_type', '!=', 'system_generated');
            })
            // Include client/lead uploaded emails (conversion_email_fetch), not only sent ones.
            ->orderByDesc('created_at')
            ->limit(80)
            ->get(['id', 'subject', 'mail_body_type', 'conversion_type', 'type', 'client_id', 'created_at', 'user_id'])
            ->pipe(function (Collection $emails): Collection {
                $activityIds = $this->activityLogIdsForEmails($emails);

                return $emails->map(function (Email $log) use ($activityIds): ?array {
                    $clientId = $log->client_id !== null ? (int) $log->client_id : null;
                    if ($clientId === null) {
                        return null;
                    }

                    [$recordType, $recordId] = $this->resolveRecordFromType((string) ($log->type ?? ''), $clientId);
                    if ($recordType === null) {
                        return null;
                    }

                    $isSent = ($log->mail_body_type ?? '') === 'sent';
                    $kind = $isSent ? 'Email out' : 'Email';

                    $row = $this->row(
                        $kind,
                        (string) ($log->subject ?: 'Email'),
                        $log->created_at,
                        $this->recordRef($recordType, $recordId),
                        'email:'.$log->id,
                        $recordType,
                        $recordId,
                        null,
                    );

                    $activityId = $activityIds[(int) $log->id] ?? null;
                    if ($activityId !== null) {
                        $row['activities_log_id'] = $activityId;
                    }

                    return $row;
                });
            })
            ->filter()
            ->values();
    }

    /**
     * Map email ids to matching client activity-feed rows for diary deep links.
     *
     * @param  Collection<int, Email>  $emails
     * @return array<int, int>
     */
    protected function activityLogIdsForEmails(Collection $emails): array
    {
        if ($emails->isEmpty() || ! Schema::hasTable('activities_logs')) {
            return [];
        }

        $clientIds = $emails->pluck('client_id')
            ->filter(fn ($id): bool => $id !== null && (int) $id > 0)
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();

        if ($clientIds === []) {
            return [];
        }

        $createdAts = $emails->pluck('created_at')->filter();
        if ($createdAts->isEmpty()) {
            return [];
        }

        $start = Carbon::parse($createdAts->min())->subMinutes(3);
        $end = Carbon::parse($createdAts->max())->addMinutes(3);

        $userIds = $emails->pluck('user_id')
            ->filter(fn ($id): bool => $id !== null && (int) $id > 0)
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();

        $logsQuery = ActivitiesLog::query()
            ->whereIn('client_id', $clientIds)
            ->whereBetween('created_at', [$start, $end])
            ->where(function ($q) {
                $q->where('subject', 'like', 'uploaded Email:%')
                    ->orWhere('subject', 'like', '%sent an email%')
                    ->orWhere('subject', 'like', 'Email%');
            })
            ->orderBy('id');

        if ($userIds !== []) {
            $logsQuery->whereIn('created_by', $userIds);
        }

        $logs = $logsQuery->get(['id', 'client_id', 'created_by', 'subject', 'created_at']);
        if ($logs->isEmpty()) {
            return [];
        }

        $map = [];
        $used = [];

        foreach ($emails as $email) {
            $emailId = (int) $email->id;
            $emailClientId = $email->client_id !== null ? (int) $email->client_id : 0;
            $emailUserId = $email->user_id !== null ? (int) $email->user_id : 0;
            if ($emailClientId < 1) {
                continue;
            }

            $emailAt = Carbon::parse($email->created_at)->getTimestamp();
            $subject = trim((string) ($email->subject ?? ''));
            $bestId = null;
            $bestScore = PHP_INT_MAX;

            foreach ($logs as $log) {
                $logId = (int) $log->id;
                if (isset($used[$logId])) {
                    continue;
                }
                if ((int) $log->client_id !== $emailClientId) {
                    continue;
                }
                if ($emailUserId > 0 && (int) $log->created_by !== $emailUserId) {
                    continue;
                }

                $delta = abs(Carbon::parse($log->created_at)->getTimestamp() - $emailAt);
                if ($delta > 180) {
                    continue;
                }

                $logSubject = (string) ($log->subject ?? '');
                $hasSubjectOverlap = $subject !== '' && stripos($logSubject, $subject) !== false;
                $isUploadEmailRow = str_starts_with($logSubject, 'uploaded Email:');
                if (! $hasSubjectOverlap && ! $isUploadEmailRow) {
                    continue;
                }

                // Prefer subject overlap; then closer timestamps.
                $score = $delta + ($hasSubjectOverlap ? 0 : 1000);
                if ($score < $bestScore) {
                    $bestScore = $score;
                    $bestId = $logId;
                }
            }

            if ($bestId !== null) {
                $map[$emailId] = $bestId;
                $used[$bestId] = true;
            }
        }

        return $map;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function documentEvents(int $staffId, Carbon $start, Carbon $end): Collection
    {
        if (! Schema::hasTable('documents')) {
            return collect();
        }

        $hasDocType = Schema::hasColumn('documents', 'doc_type');

        $columns = array_values(array_filter([
            'id',
            Schema::hasColumn('documents', 'file_name') ? 'file_name' : null,
            Schema::hasColumn('documents', 'name') ? 'name' : null,
            Schema::hasColumn('documents', 'doc_name') ? 'doc_name' : null,
            'type',
            'client_id',
            $hasDocType ? 'doc_type' : null,
            Schema::hasColumn('documents', 'application_id') ? 'application_id' : null,
            'created_at',
        ]));

        $query = Document::query()
            ->where(function ($q) use ($staffId) {
                $q->where('created_by', $staffId)->orWhere('user_id', $staffId);
            })
            ->whereBetween('created_at', [$start, $end]);

        // Hide client email-upload storage/PDF rows from diary Document list (shown as Email instead).
        // Partner email docs (partner_email_fetch) stay listed as Document.
        if ($hasDocType) {
            $query->where(function ($q) {
                $q->whereNull('doc_type')
                    ->orWhere('doc_type', '!=', 'conversion_email_fetch');
            });
        }

        return $query
            ->orderByDesc('created_at')
            ->limit(80)
            ->get($columns)
            ->map(function (Document $doc): ?array {
                $clientId = $doc->client_id !== null ? (int) $doc->client_id : null;
                if ($clientId === null) {
                    return null;
                }

                [$recordType, $recordId] = $this->resolveRecordFromType((string) ($doc->type ?? ''), $clientId);
                if ($recordType === null) {
                    return null;
                }

                $title = (string) ($doc->file_name ?? $doc->name ?? $doc->doc_name ?? 'Document');
                $appId = Schema::hasColumn('documents', 'application_id')
                    && $doc->application_id !== null
                    && is_numeric($doc->application_id)
                    ? (int) $doc->application_id
                    : null;

                return $this->row(
                    'Document',
                    $title,
                    $doc->created_at,
                    $this->recordRef($recordType, $recordId, $appId),
                    'document:'.$doc->id,
                    $recordType,
                    $recordId,
                    $appId,
                );
            })
            ->filter()
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function smsEvents(int $staffId, Carbon $start, Carbon $end): Collection
    {
        if (! Schema::hasTable('sms_logs')) {
            return collect();
        }

        $timeCol = Schema::hasColumn('sms_logs', 'sent_at') ? 'sent_at' : 'created_at';
        $bodyCol = Schema::hasColumn('sms_logs', 'message_content')
            ? 'message_content'
            : (Schema::hasColumn('sms_logs', 'message') ? 'message' : null);

        $columns = array_values(array_filter(['id', $bodyCol, $timeCol, 'client_id']));

        return SmsLog::query()
            ->where('sender_id', $staffId)
            ->whereBetween($timeCol, [$start, $end])
            ->orderByDesc($timeCol)
            ->limit(40)
            ->get($columns)
            ->map(function (SmsLog $sms) use ($timeCol, $bodyCol): ?array {
                $clientId = Schema::hasColumn('sms_logs', 'client_id') && $sms->client_id !== null
                    ? (int) $sms->client_id
                    : null;
                if ($clientId === null) {
                    return null;
                }

                $at = $sms->{$timeCol} ?? $sms->created_at ?? null;
                $body = $bodyCol !== null ? ($sms->{$bodyCol} ?? null) : null;
                $title = (string) (Str::limit((string) ($body ?? 'SMS'), 80));

                return $this->row(
                    'SMS',
                    $title,
                    $at,
                    $this->recordRef(StaffFileSession::RECORD_TYPE_STUDENT, $clientId),
                    'sms:'.$sms->id,
                    StaffFileSession::RECORD_TYPE_STUDENT,
                    $clientId,
                    null,
                );
            })
            ->filter()
            ->values();
    }

    /**
     * Notes this staff posted today (client/lead/partner file notes, not assigned actions).
     * Intentionally broader than StaffWorkloadService::CONTACT_TITLES so diary lists all
     * posted notes; Call/In-Person-only filtering stays on workload contact metrics.
     *
     * @return Collection<int, array<string, mixed>>
     */
    protected function contactNoteEvents(int $staffId, Carbon $start, Carbon $end): Collection
    {
        if (! Schema::hasTable('notes')) {
            return collect();
        }

        return Note::query()
            ->where('user_id', $staffId)
            ->where('is_action', 0)
            ->whereNull('assigned_to')
            ->whereIn('type', ['client', 'lead', 'partner'])
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->limit(80)
            ->get(array_values(array_filter([
                'id',
                'title',
                'type',
                'client_id',
                'created_at',
                Schema::hasColumn('notes', 'description') ? 'description' : null,
            ])))
            ->map(function (Note $note): ?array {
                $clientId = $note->client_id !== null ? (int) $note->client_id : null;
                if ($clientId === null) {
                    return null;
                }

                [$recordType, $recordId] = $this->resolveRecordFromType((string) ($note->type ?? ''), $clientId);
                if ($recordType === null) {
                    return null;
                }

                $title = (string) ($note->title ?: 'Note');
                $kind = $this->noteKindLabel($title);

                $row = $this->row(
                    $kind,
                    $title,
                    $note->created_at,
                    $this->recordRef($recordType, $recordId),
                    'note:'.$note->id,
                    $recordType,
                    $recordId,
                    null,
                );

                $body = $this->plainTextForDiary((string) ($note->description ?? ''));
                if ($body !== '') {
                    $row['body'] = $body;
                }

                return $row;
            })
            ->filter()
            ->values();
    }

    protected function noteKindLabel(string $title): string
    {
        $normalized = strtolower(trim($title));

        if (str_contains($normalized, 'person')) {
            return 'In-person note';
        }

        if ($normalized === 'call') {
            return 'Call note';
        }

        return 'Note';
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function stageEvents(int $staffId, Carbon $start, Carbon $end): Collection
    {
        if (! Schema::hasTable('application_activities_logs') || ! Schema::hasTable('applications')) {
            return collect();
        }

        return ApplicationActivitiesLog::query()
            ->where('user_id', $staffId)
            ->where('type', 'stage')
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->limit(80)
            ->get(['id', 'app_id', 'stage', 'title', 'comment', 'created_at'])
            ->map(function (ApplicationActivitiesLog $log): ?array {
                $appId = $log->app_id !== null ? (int) $log->app_id : null;
                if ($appId === null) {
                    return null;
                }

                $application = Application::query()->find($appId, ['id', 'client_id', 'stage', 'partner_id']);
                if (! $application || $application->client_id === null) {
                    return null;
                }

                $recordId = (int) $application->client_id;
                $rawTitle = (string) ($log->title ?: $log->comment ?: ('Stage → '.($log->stage ?: $application->stage)));
                $title = $this->plainTextForDiary($rawTitle);

                // Diary list should show the student client ref (e.g. TEST…), not college · #application.
                // Keep application_id on the row so detail links can still open the application.
                return $this->row(
                    'Stage',
                    $title,
                    $log->created_at,
                    $this->recordRef(StaffFileSession::RECORD_TYPE_STUDENT, $recordId),
                    'stage:'.$log->id,
                    StaffFileSession::RECORD_TYPE_STUDENT,
                    $recordId,
                    $appId,
                );
            })
            ->filter()
            ->values();
    }

    /**
     * Non-note feed rows (exclude file_time and note-audit duplicates).
     *
     * @return Collection<int, array<string, mixed>>
     */
    protected function feedEvents(int $staffId, Carbon $start, Carbon $end): Collection
    {
        if (! Schema::hasTable('activities_logs')) {
            return collect();
        }

        return ActivitiesLog::query()
            ->where('created_by', $staffId)
            ->whereBetween('created_at', [$start, $end])
            ->where(function ($q) {
                $q->whereNull('activity_type')
                    ->orWhere('activity_type', '!=', StaffFileSession::ACTIVITY_TYPE);
            })
            ->where(function ($q) {
                $q->whereNull('subject')
                    ->orWhere(function ($sub) {
                        $sub->whereRaw('LOWER(TRIM(subject)) NOT IN (?, ?, ?)', [
                            'added a note',
                            'updated a note',
                            'deleted a note',
                        ]);
                    });
            })
            ->where(function ($q) {
                $q->where('activity_type', 'stage')
                    ->orWhere('subject', 'like', 'completed action for%')
                    ->orWhere('subject', 'like', 'Updated action for%')
                    ->orWhere('subject', 'like', 'Completed action%')
                    ->orWhere('subject', 'like', '%started an application%');
            })
            ->orderByDesc('created_at')
            ->limit(80)
            ->get(['id', 'subject', 'activity_type', 'task_group', 'client_id', 'created_at'])
            ->map(function (ActivitiesLog $log): ?array {
                $clientId = $log->client_id !== null ? (int) $log->client_id : null;
                if ($clientId === null) {
                    return null;
                }

                $isPartner = ($log->task_group ?? null) === ActivitiesLog::TASK_GROUP_PARTNER;
                $recordType = $isPartner
                    ? StaffFileSession::RECORD_TYPE_PARTNER
                    : StaffFileSession::RECORD_TYPE_STUDENT;

                $subject = (string) ($log->subject ?? '');
                $type = (string) ($log->activity_type ?? '');
                if (str_starts_with(strtolower($subject), 'completed action')) {
                    $kind = 'Action completed';
                } elseif (str_starts_with(strtolower($subject), 'updated action')) {
                    $kind = 'Action updated';
                } elseif ($type === 'stage') {
                    $kind = 'Stage';
                } else {
                    $kind = 'Activity';
                }

                return $this->row(
                    $kind,
                    $subject !== '' ? $subject : $kind,
                    $log->created_at,
                    $this->recordRef($recordType, $clientId),
                    'feed:'.$log->id,
                    $recordType,
                    $clientId,
                    null,
                );
            })
            ->filter()
            ->values();
    }

    /**
     * @return array{0: ?string, 1: ?int}
     */
    protected function resolveRecordFromType(string $type, int $clientId): array
    {
        if ($type === 'partner') {
            return [StaffFileSession::RECORD_TYPE_PARTNER, $clientId];
        }

        if (in_array($type, ['client', 'lead'], true)) {
            return [StaffFileSession::RECORD_TYPE_STUDENT, $clientId];
        }

        // Fallback: prefer student when admins row exists, else partner.
        if (Schema::hasTable('admins') && Admin::query()->where('id', $clientId)->exists()) {
            return [StaffFileSession::RECORD_TYPE_STUDENT, $clientId];
        }

        if (Schema::hasTable('partners') && Partner::query()->where('id', $clientId)->exists()) {
            return [StaffFileSession::RECORD_TYPE_PARTNER, $clientId];
        }

        return [null, null];
    }

    /**
     * @return array<string, mixed>
     */
    protected function row(
        string $kind,
        string $title,
        mixed $at,
        ?string $ref,
        string $key,
        string $recordType,
        int $recordId,
        ?int $applicationId = null,
    ): array {
        $carbon = $at ? Carbon::parse($at)->timezone((string) config('app.timezone')) : now();

        return [
            'key' => $key,
            'kind' => $kind,
            'title' => $title,
            'ref' => $ref,
            'time' => $carbon->format('g:i a'),
            'sort_at' => $carbon->timestamp,
            'record_type' => $recordType,
            'record_id' => $recordId,
            'application_id' => $applicationId,
        ];
    }

    /**
     * Diary UI escapes HTML, so strip tags from stored stage comments (e.g. <b>Stage</b>).
     */
    protected function plainTextForDiary(string $value): string
    {
        $plain = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $collapsed = preg_replace('/\s+/u', ' ', $plain);

        return trim(is_string($collapsed) ? $collapsed : $plain);
    }

    protected function recordRef(string $recordType, int $recordId, ?int $applicationId = null): ?string
    {
        $cacheKey = $recordType.':'.$recordId.':'.($applicationId ?? 0);
        if (array_key_exists($cacheKey, $this->labelCache)) {
            return $this->labelCache[$cacheKey];
        }

        if ($applicationId !== null && $applicationId > 0 && Schema::hasTable('applications')) {
            $app = Application::query()->with(['partner:id,partner_name', 'client:id,first_name,last_name,client_id'])
                ->find($applicationId, ['id', 'client_id', 'partner_id', 'stage', 'product_id']);
            if ($app) {
                $partnerName = $app->partner?->partner_name;
                $label = $partnerName
                    ? trim($partnerName.' · #'.$app->id)
                    : ('Application #'.$app->id);
                $this->labelCache[$cacheKey] = $label;

                return $label;
            }
        }

        if ($recordType === StaffFileSession::RECORD_TYPE_PARTNER && Schema::hasTable('partners')) {
            $name = Partner::query()->where('id', $recordId)->value('partner_name');
            $this->labelCache[$cacheKey] = $name ? (string) $name : ('College #'.$recordId);

            return $this->labelCache[$cacheKey];
        }

        if (Schema::hasTable('admins')) {
            $admin = Admin::query()->find($recordId, ['id', 'client_id', 'first_name', 'last_name']);
            if ($admin) {
                $code = trim((string) ($admin->client_id ?? ''));
                if ($code !== '') {
                    $this->labelCache[$cacheKey] = $code;

                    return $code;
                }
                $name = trim((string) ($admin->first_name ?? '').' '.($admin->last_name ?? ''));
                $this->labelCache[$cacheKey] = $name !== '' ? $name : ('Record #'.$recordId);

                return $this->labelCache[$cacheKey];
            }
        }

        $this->labelCache[$cacheKey] = 'Record #'.$recordId;

        return $this->labelCache[$cacheKey];
    }
}
