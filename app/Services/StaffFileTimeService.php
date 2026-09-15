<?php

namespace App\Services;

use App\Models\ActivitiesLog;
use App\Models\Admin;
use App\Models\Application;
use App\Models\Partner;
use App\Models\Staff;
use App\Models\StaffFileSession;
use App\Models\StaffFileTimeEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class StaffFileTimeService
{
    public function __construct(
        protected StaffWorkloadService $workloadService,
    ) {}

    /**
     * @return array{entries: list<array<string, mixed>>, date: string, timezone: string}
     */
    public function boardForStaff(int $staffId, ?Carbon $day = null): array
    {
        if (! Schema::hasTable('staff_file_time_entries')) {
            return [
                'entries' => [],
                'date' => ($day ?? now())->toDateString(),
                'timezone' => (string) config('app.timezone'),
            ];
        }

        [$start, $end] = $this->workloadService->dayBounds($day ?? now());
        $entries = StaffFileTimeEntry::query()
            ->where('staff_id', $staffId)
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('updated_at')
            ->get();

        return [
            'entries' => $entries->map(fn (StaffFileTimeEntry $entry): array => $this->serialize($entry))->all(),
            'date' => $start->toDateString(),
            'timezone' => (string) config('app.timezone'),
        ];
    }

    /**
     * One-shot manual log (no live timer).
     *
     * @param  array{
     *     kind: string,
     *     title: string,
     *     confirmed_minutes: int,
     *     record_type?: string|null,
     *     record_id?: int|null,
     *     application_id?: int|null,
     *     admin?: bool
     * }  $data
     */
    public function logCompleted(int $staffId, array $data): StaffFileTimeEntry
    {
        $confirmedMinutes = (int) ($data['confirmed_minutes'] ?? 0);
        if ($confirmedMinutes < 1 || $confirmedMinutes > 480) {
            throw ValidationException::withMessages([
                'confirmed_minutes' => 'Confirmed minutes must be between 1 and 480.',
            ]);
        }

        if (! in_array((string) ($data['kind'] ?? ''), StaffFileTimeEntry::kinds(), true)) {
            throw ValidationException::withMessages(['kind' => 'Invalid kind.']);
        }

        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '') {
            throw ValidationException::withMessages(['title' => 'Title is required.']);
        }

        $isAdmin = filter_var($data['admin'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $recordType = $data['record_type'] ?? null;
        $recordId = isset($data['record_id']) ? (int) $data['record_id'] : null;
        $applicationId = isset($data['application_id']) ? (int) $data['application_id'] : null;
        $applicationId = $applicationId && $applicationId > 0 ? $applicationId : null;

        if ($isAdmin) {
            $recordType = null;
            $recordId = null;
            $applicationId = null;
        } else {
            if (! in_array($recordType, [StaffFileSession::RECORD_TYPE_STUDENT, StaffFileSession::RECORD_TYPE_PARTNER], true)
                || ! $recordId) {
                throw ValidationException::withMessages([
                    'record_id' => 'Choose a student, application, college, or Admin / no file.',
                ]);
            }

            if ($recordType === StaffFileSession::RECORD_TYPE_STUDENT) {
                if (! Admin::query()->where('id', $recordId)->exists()) {
                    throw ValidationException::withMessages(['record_id' => 'Student not found.']);
                }
                if ($applicationId !== null) {
                    $belongs = Application::query()
                        ->where('id', $applicationId)
                        ->where('client_id', $recordId)
                        ->exists();
                    if (! $belongs) {
                        throw ValidationException::withMessages([
                            'application_id' => 'Application does not belong to this student.',
                        ]);
                    }
                }
            } else {
                if (! Partner::query()->where('id', $recordId)->exists()) {
                    throw ValidationException::withMessages(['record_id' => 'College not found.']);
                }
                $applicationId = null;
            }
        }

        return DB::transaction(function () use ($staffId, $data, $title, $confirmedMinutes, $recordType, $recordId, $applicationId) {
            $now = now();
            $entry = StaffFileTimeEntry::query()->create([
                'staff_id' => $staffId,
                'record_type' => $recordType,
                'record_id' => $recordId,
                'application_id' => $applicationId,
                'kind' => $data['kind'],
                'title' => $title,
                'status' => StaffFileTimeEntry::STATUS_DONE,
                'is_running' => false,
                'clock_seconds' => $confirmedMinutes * 60,
                'confirmed_minutes' => $confirmedMinutes,
                'started_at' => $now,
                'completed_at' => $now,
            ]);

            if (! $entry->isAdmin()) {
                $log = $this->postToFeed($entry, $staffId);
                $entry->activities_log_id = $log->id;
                $entry->save();
            }

            return $entry->fresh();
        });
    }

    /**
     * @return array{
     *     hours_label: string,
     *     crm_events: list<array<string, mixed>>,
     *     overlay: list<array<string, mixed>>,
     *     admin: list<array<string, mixed>>,
     *     auto: list<string>,
     *     opened: list<string>,
     *     still_open: list<array<string, mixed>>,
     *     text: string
     * }
     */
    public function copySummary(
        int $staffId,
        StaffDayCrmEventsService $crmEvents,
        StaffDayHoursService $hours,
        ?StaffFileSessionService $fileSessions = null,
        ?Carbon $day = null,
    ): array {
        $staff = Staff::query()->find($staffId);
        $name = $staff
            ? trim(($staff->first_name ?? '').' '.($staff->last_name ?? ''))
            : 'Staff';
        [$start] = $this->workloadService->dayBounds($day ?? now());
        $dateLabel = $start->format('l, j M Y');

        $hoursPayload = $hours->forStaff($staffId, $day);
        $events = $crmEvents->forStaff($staffId, $day);
        $board = $this->boardForStaff($staffId, $day);

        $sessionsPayload = ['auto' => [], 'opened' => [], 'event_minutes' => []];
        if ($fileSessions !== null) {
            $sessionsPayload = $fileSessions->sessionsForBoard($staffId, $day);
        }

        $overlayDone = [];
        $adminDone = [];
        foreach ($board['entries'] as $entry) {
            $line = [
                'ref' => $entry['ref'] ?? 'Admin',
                'kind' => $entry['kind'],
                'kind_label' => $entry['kind_label'] ?? $entry['kind'],
                'title' => $entry['title'],
                'minutes' => $entry['confirmed_minutes'],
                'record_type' => $entry['record_type'] ?? null,
                'record_id' => $entry['record_id'] ?? null,
                'application_id' => $entry['application_id'] ?? null,
            ];
            if ($entry['is_admin']) {
                $adminDone[] = $line;
            } else {
                $overlayDone[] = $line;
            }
        }

        $crmMinutes = $this->resolveCrmEventMinutes(
            $events['items'] ?? [],
            $sessionsPayload,
            $overlayDone,
        );

        $lines = [
            "{$name} — {$dateLabel}",
            'Hours in CRM: '.($hoursPayload['label'] ?? '—'),
            '',
            '— Already in CRM —',
        ];

        if ($events['items'] === []) {
            $lines[] = '(none)';
        } else {
            foreach ($events['items'] as $item) {
                $ref = $item['ref'] ?: '—';
                $line = "{$ref} · {$item['kind']} · {$item['title']} · {$item['time']}";
                $mins = $crmMinutes[(string) ($item['key'] ?? '')] ?? null;
                if ($mins !== null && $mins > 0) {
                    $line .= " · {$mins}m";
                }
                $lines[] = $line;
            }
            if (($events['more'] ?? 0) > 0) {
                $lines[] = '… and '.$events['more'].' more';
            }
        }

        $lines[] = '';
        $lines[] = '— Manual logs —';
        if ($overlayDone === []) {
            $lines[] = '(none)';
        } else {
            foreach ($overlayDone as $item) {
                $kind = $item['kind_label'] ?? $item['kind'];
                $lines[] = "{$item['ref']} · {$kind} · {$item['title']} · {$item['minutes']}m";
            }
        }

        $lines[] = '';
        $lines[] = '— Admin / no file —';
        if ($adminDone === []) {
            $lines[] = '(none)';
        } else {
            foreach ($adminDone as $item) {
                $kind = $item['kind_label'] ?? $item['kind'];
                $lines[] = "{$item['ref']} · {$kind} · {$item['title']} · {$item['minutes']}m";
            }
        }

        $lines[] = '';
        $lines[] = '— Time on files (auto) —';
        $autoLines = [];
        $openedRefs = [];
        foreach ($sessionsPayload['auto'] as $row) {
            $eventLabel = ! empty($row['is_reviewed_only'])
                ? 'reviewed file'
                : (($row['event_count'] ?? 0).' activities');
            $autoLines[] = "{$row['ref']} · {$row['confirmed_minutes']}m · {$eventLabel}";
        }
        foreach ($sessionsPayload['opened'] as $row) {
            $mins = (int) ($row['minutes'] ?? max(0, (int) round(((int) ($row['focused_seconds'] ?? 0)) / 60)));
            $openedRefs[] = $mins > 0
                ? ((string) ($row['ref'] ?? '—'))." · {$mins}m (open)"
                : (string) ($row['ref'] ?? '—');
        }
        if ($autoLines === []) {
            $lines[] = '(none)';
        } else {
            array_push($lines, ...$autoLines);
        }

        $lines[] = '';
        $lines[] = '— Files opened —';
        if ($openedRefs === []) {
            $lines[] = '(none)';
        } else {
            array_push($lines, ...$openedRefs);
        }

        $lines[] = '';
        $lines[] = '— Still open —';
        $lines[] = '(none)';

        return [
            'hours_label' => $hoursPayload['label'] ?? '—',
            'crm_events' => $events['items'],
            'overlay' => $overlayDone,
            'admin' => $adminDone,
            'auto' => $autoLines,
            'opened' => $openedRefs,
            'still_open' => [],
            'text' => implode("\n", $lines),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function serialize(StaffFileTimeEntry $entry): array
    {
        return [
            'id' => $entry->id,
            'kind' => $entry->kind,
            'kind_label' => $entry->kindLabel(),
            'title' => $entry->title,
            'status' => $entry->status,
            'confirmed_minutes' => $entry->confirmed_minutes,
            'record_type' => $entry->record_type,
            'record_id' => $entry->record_id,
            'application_id' => $entry->application_id,
            'ref' => $this->entryRef($entry),
            'is_admin' => $entry->isAdmin(),
            'posted' => $entry->status === StaffFileTimeEntry::STATUS_DONE && $entry->isPosted(),
            'activities_log_id' => $entry->activities_log_id,
            'completed_at' => optional($entry->completed_at)?->toIso8601String(),
        ];
    }

    protected function entryRef(StaffFileTimeEntry $entry): string
    {
        if ($entry->isAdmin()) {
            return 'Admin';
        }

        if ($entry->application_id) {
            $partnerName = Application::query()
                ->where('applications.id', $entry->application_id)
                ->leftJoin('partners', 'partners.id', '=', 'applications.partner_id')
                ->value('partners.partner_name');

            return $partnerName
                ? trim((string) $partnerName).' · #'.$entry->application_id
                : 'Application #'.$entry->application_id;
        }

        if ($entry->record_type === StaffFileSession::RECORD_TYPE_PARTNER) {
            $name = Partner::query()->where('id', $entry->record_id)->value('partner_name');

            return $name ? (string) $name : 'College #'.$entry->record_id;
        }

        $admin = Admin::query()->find($entry->record_id, ['id', 'client_id', 'first_name', 'last_name']);
        if ($admin) {
            $code = trim((string) ($admin->client_id ?? ''));
            if ($code !== '') {
                return $code;
            }
            $name = trim(($admin->first_name ?? '').' '.($admin->last_name ?? ''));
            if ($name !== '') {
                return $name;
            }
        }

        return 'Record #'.$entry->record_id;
    }

    protected function postToFeed(StaffFileTimeEntry $entry, int $staffId): ActivitiesLog
    {
        $ref = $this->entryRef($entry);
        $minutes = (int) $entry->confirmed_minutes;
        $kindLabel = $entry->kindLabel();
        $payload = [
            'client_id' => $entry->record_id,
            'created_by' => $staffId,
            'subject' => "logged {$minutes}m {$kindLabel} on {$ref}",
            'description' => trim($entry->title.' · '.$kindLabel.' · '.$minutes.'m'),
            'activity_type' => StaffFileTimeEntry::ACTIVITY_TYPE,
            'use_for' => null,
            'task_status' => 0,
            'pin' => 0,
            'task_group' => $entry->record_type === StaffFileSession::RECORD_TYPE_PARTNER
                ? ActivitiesLog::TASK_GROUP_PARTNER
                : null,
        ];

        if ($entry->activities_log_id) {
            $existing = ActivitiesLog::query()->find($entry->activities_log_id);
            if ($existing) {
                $existing->fill($payload);
                $existing->save();

                return $existing;
            }
        }

        return ActivitiesLog::query()->create($payload);
    }

    /**
     * @param  list<array<string, mixed>>  $items
     * @param  array{auto?: list<array<string, mixed>>, event_minutes?: array<string, int>}  $sessionsPayload
     * @param  list<array<string, mixed>>  $overlayDone
     * @return array<string, int>
     */
    protected function resolveCrmEventMinutes(array $items, array $sessionsPayload, array $overlayDone): array
    {
        $resolved = [];
        foreach ($sessionsPayload['event_minutes'] ?? [] as $key => $mins) {
            $mins = (int) $mins;
            if ($mins > 0 && is_string($key) && $key !== '') {
                $resolved[$key] = $mins;
            }
        }

        $recordTotals = [];
        foreach ($sessionsPayload['auto'] ?? [] as $row) {
            $recordKey = $this->crmRecordKey(
                $row['record_type'] ?? null,
                isset($row['record_id']) ? (int) $row['record_id'] : null,
                isset($row['application_id']) ? (int) $row['application_id'] : null,
            );
            if ($recordKey === null) {
                continue;
            }
            $recordTotals[$recordKey] = ($recordTotals[$recordKey] ?? 0) + (int) ($row['confirmed_minutes'] ?? 0);
        }

        foreach ($overlayDone as $row) {
            $recordKey = $this->crmRecordKey(
                $row['record_type'] ?? null,
                isset($row['record_id']) ? (int) $row['record_id'] : null,
                isset($row['application_id']) ? (int) $row['application_id'] : null,
            );
            if ($recordKey === null || ($recordTotals[$recordKey] ?? 0) > 0) {
                continue;
            }
            $recordTotals[$recordKey] = (int) ($row['minutes'] ?? 0);
        }

        $unassignedByRecord = [];
        foreach ($items as $item) {
            $eventKey = (string) ($item['key'] ?? '');
            if ($eventKey === '' || isset($resolved[$eventKey])) {
                continue;
            }
            $recordKey = $this->crmRecordKey(
                $item['record_type'] ?? null,
                isset($item['record_id']) ? (int) $item['record_id'] : null,
                isset($item['application_id']) ? (int) $item['application_id'] : null,
            );
            if ($recordKey === null) {
                continue;
            }
            $unassignedByRecord[$recordKey][] = $eventKey;
        }

        foreach ($unassignedByRecord as $recordKey => $eventKeys) {
            $remaining = max(0, $recordTotals[$recordKey] ?? 0);
            $count = count($eventKeys);
            if ($remaining < 1 || $count < 1) {
                continue;
            }
            $base = intdiv($remaining, $count);
            $remainder = $remaining % $count;
            foreach ($eventKeys as $index => $eventKey) {
                $resolved[$eventKey] = $base + ($index === 0 ? $remainder : 0);
            }
        }

        return $resolved;
    }

    protected function crmRecordKey(?string $recordType, ?int $recordId, ?int $applicationId): ?string
    {
        if ($applicationId !== null && $applicationId > 0) {
            return 'application:'.$applicationId;
        }
        if ($recordType && $recordId !== null && $recordId > 0) {
            return $recordType.':'.$recordId;
        }

        return null;
    }
}
