<?php

namespace App\Services;

use App\Models\ActivitiesLog;
use App\Models\Admin;
use App\Models\Application;
use App\Models\Partner;
use App\Models\StaffFileSession;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class StaffFileSessionService
{
    public const HEARTBEAT_STALE_SECONDS = 180;

    public const REVIEWED_ONLY_SECONDS = 120;

    public function __construct(
        protected StaffWorkloadService $workloadService,
        protected StaffDayCrmEventsService $crmEvents,
    ) {}

    public function heartbeat(
        int $staffId,
        string $recordType,
        int $recordId,
        ?int $applicationId,
        int $focusedSeconds,
    ): StaffFileSession {
        $session = $this->findOrCreateToday($staffId, $recordType, $recordId, $applicationId);
        $this->applyFocusedSeconds($session, $focusedSeconds);
        $session->last_heartbeat_at = now();
        $this->reopenIfClosed($session);
        $session->save();

        return $this->promoteIfWritten($session->fresh());
    }

    public function blur(
        int $staffId,
        string $recordType,
        int $recordId,
        ?int $applicationId,
        int $focusedSeconds,
    ): StaffFileSession {
        $session = $this->findOrCreateToday($staffId, $recordType, $recordId, $applicationId);
        $this->applyFocusedSeconds($session, $focusedSeconds);
        $session->last_heartbeat_at = now();
        $this->reopenIfClosed($session);
        $session->save();

        return $session->fresh();
    }

    public function idleCut(int $staffId, StaffFileSession $session, Carbon $idleStartedAt): StaffFileSession
    {
        $this->assertOwned($staffId, $session);

        if ($session->last_heartbeat_at === null) {
            return $session;
        }

        $lastBeat = $session->last_heartbeat_at;
        if ($idleStartedAt->greaterThan($lastBeat)) {
            return $session;
        }

        $removed = max(0, $lastBeat->getTimestamp() - $idleStartedAt->getTimestamp());
        if ($removed > 0) {
            $session->focused_seconds = max(0, (int) $session->focused_seconds - $removed);
            $session->idle_cut_seconds = (int) $session->idle_cut_seconds + $removed;
            $session->save();
        }

        return $session->fresh();
    }

    public function promoteIfWritten(StaffFileSession $session): StaffFileSession
    {
        $events = $this->eventsForSession($session);

        if ($session->status === StaffFileSession::STATUS_ACCESSED) {
            if ($events->isNotEmpty()) {
                $session->status = StaffFileSession::STATUS_RECORDED;
                $session->is_reviewed_only = false;
                $session->save();

                return $session->fresh();
            }

            if ((int) $session->focused_seconds >= self::REVIEWED_ONLY_SECONDS) {
                $session->status = StaffFileSession::STATUS_RECORDED;
                $session->is_reviewed_only = true;
                $session->save();
            }

            return $session->fresh();
        }

        // A later CRM write should clear reviewed-only even if already recorded.
        if ($session->status === StaffFileSession::STATUS_RECORDED
            && $session->is_reviewed_only
            && $events->isNotEmpty()) {
            $session->is_reviewed_only = false;
            $session->save();

            return $session->fresh();
        }

        return $session;
    }

    public function closeStale(Carbon $now): int
    {
        if (! Schema::hasTable('staff_file_sessions')) {
            return 0;
        }

        $cutoff = $now->copy()->subSeconds(self::HEARTBEAT_STALE_SECONDS);
        $sessions = StaffFileSession::query()
            ->where('status', '!=', StaffFileSession::STATUS_CLOSED)
            ->where('last_heartbeat_at', '<', $cutoff)
            ->get();

        $closed = 0;
        foreach ($sessions as $session) {
            $this->closeSession($session, $session->last_heartbeat_at ?? $now);
            $closed++;
        }

        return $closed;
    }

    public function updateMinutes(int $staffId, StaffFileSession $session, int $minutes): StaffFileSession
    {
        $this->assertOwned($staffId, $session);

        if ($minutes < 1 || $minutes > 480) {
            throw ValidationException::withMessages(['confirmed_minutes' => 'Minutes must be between 1 and 480.']);
        }

        $session->confirmed_minutes = $minutes;
        $session->save();

        if ($session->activities_log_id && $session->isRecordedForBoard()) {
            $this->postToFeed($session->fresh());
        }

        return $session->fresh();
    }

    public function delete(int $staffId, StaffFileSession $session): void
    {
        $this->assertOwned($staffId, $session);

        if ($session->activities_log_id !== null) {
            throw ValidationException::withMessages(['session' => 'Posted sessions cannot be deleted.']);
        }

        $session->delete();
    }

    /**
     * @return array{
     *     auto: list<array<string, mixed>>,
     *     opened: list<array<string, mixed>>,
     *     event_minutes: array<string, int>
     * }
     */
    public function sessionsForBoard(int $staffId, ?Carbon $day = null): array
    {
        if (! Schema::hasTable('staff_file_sessions')) {
            return ['auto' => [], 'opened' => [], 'event_minutes' => []];
        }

        [$start] = $this->workloadService->dayBounds($day ?? now());
        $sessionDate = $start->toDateString();

        $sessions = StaffFileSession::query()
            ->with('application')
            ->where('staff_id', $staffId)
            ->whereDate('session_date', $sessionDate)
            ->orderByDesc('started_at')
            ->get();

        $auto = [];
        $opened = [];
        $eventMinutes = [];

        foreach ($sessions as $session) {
            $ref = $this->recordRef($session);

            if ($session->status === StaffFileSession::STATUS_ACCESSED) {
                $opened[] = [
                    'id' => $session->id,
                    'ref' => $ref,
                    'record_type' => $session->record_type,
                    'record_id' => $session->record_id,
                    'application_id' => $session->application_id,
                    'focused_seconds' => (int) $session->focused_seconds,
                    'minutes' => max(0, (int) round(((int) $session->focused_seconds) / 60)),
                ];

                continue;
            }

            if (! $session->isRecordedForBoard()) {
                continue;
            }

            $minutes = (int) ($session->confirmed_minutes ?? max(1, (int) round(((int) $session->focused_seconds) / 60)));
            $events = $this->eventsForSession($session);
            $splitEvents = $this->splitMinutesAcrossEvents($events, $minutes);

            foreach ($splitEvents as $event) {
                if (isset($event['key'], $event['minutes'])) {
                    $eventMinutes[(string) $event['key']] = (int) $event['minutes'];
                }
            }

            $auto[] = [
                'id' => $session->id,
                'ref' => $ref,
                'record_type' => $session->record_type,
                'record_id' => $session->record_id,
                'application_id' => $session->application_id,
                'status' => $session->status,
                'confirmed_minutes' => $minutes,
                'event_count' => (int) ($session->event_count ?? $events->count()),
                'is_reviewed_only' => (bool) $session->is_reviewed_only,
                'posted' => $session->activities_log_id !== null,
                'activities_log_id' => $session->activities_log_id,
                'events' => $splitEvents,
            ];
        }

        return [
            'auto' => $auto,
            'opened' => $opened,
            'event_minutes' => $eventMinutes,
        ];
    }

    public function postToFeed(StaffFileSession $session): ActivitiesLog
    {
        $ref = $this->recordRef($session);
        $minutes = (int) ($session->confirmed_minutes ?? max(1, (int) round(((int) $session->focused_seconds) / 60)));
        $events = $this->eventsForSession($session);
        $eventCount = (int) ($session->event_count ?? $events->count());

        if ($session->is_reviewed_only && $eventCount < 1) {
            $subject = "logged {$minutes}m on {$ref} · reviewed file";
        } else {
            $subject = "logged {$minutes}m on {$ref} · {$eventCount} activities";
        }

        $payload = [
            'client_id' => $session->record_id,
            'created_by' => $session->staff_id,
            'subject' => $subject,
            'description' => $subject,
            'activity_type' => StaffFileSession::ACTIVITY_TYPE,
            'task_status' => 0,
            'pin' => 0,
            'use_for' => null,
            'task_group' => $session->isPartner() ? ActivitiesLog::TASK_GROUP_PARTNER : null,
        ];

        if ($session->activities_log_id) {
            $existing = ActivitiesLog::query()->find($session->activities_log_id);
            if ($existing) {
                $existing->fill($payload);
                $existing->save();

                return $existing;
            }
        }

        $log = ActivitiesLog::query()->create($payload);
        $session->activities_log_id = $log->id;
        $session->save();

        return $log;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function eventsForSession(StaffFileSession $session): Collection
    {
        $start = $session->started_at ?? Carbon::parse($session->session_date)->startOfDay();
        $end = $session->last_heartbeat_at ?? now();

        return $this->crmEvents->forStaffOnRecord(
            (int) $session->staff_id,
            (string) $session->record_type,
            (int) $session->record_id,
            $session->application_id !== null ? (int) $session->application_id : null,
            $start,
            $end,
        );
    }

    protected function closeSession(StaffFileSession $session, Carbon $endedAt): void
    {
        $session = $this->promoteIfWritten($session->fresh());
        $wasRecorded = $session->status === StaffFileSession::STATUS_RECORDED;

        $events = $this->eventsForSession($session);
        $session->ended_at = $endedAt;
        $session->status = StaffFileSession::STATUS_CLOSED;

        if ($session->confirmed_minutes === null) {
            $session->confirmed_minutes = max(0, (int) round(((int) $session->focused_seconds) / 60));
        }

        if ($wasRecorded) {
            $session->event_count = $session->is_reviewed_only
                ? max(1, (int) ($session->event_count ?? 1))
                : max($events->count(), 1);
        } else {
            $session->event_count = 0;
        }

        $session->save();

        if ($wasRecorded && (int) $session->confirmed_minutes >= 1) {
            $this->postToFeed($session->fresh());
        }
    }

    protected function findOrCreateToday(
        int $staffId,
        string $recordType,
        int $recordId,
        ?int $applicationId,
    ): StaffFileSession {
        [$start] = $this->workloadService->dayBounds(now());
        $sessionDate = $start->toDateString();
        $applicationKey = (int) ($applicationId ?? 0);

        try {
            return DB::transaction(function () use ($staffId, $recordType, $recordId, $applicationId, $sessionDate, $applicationKey): StaffFileSession {
                $existing = $this->findTodaySession(
                    $staffId,
                    $recordType,
                    $recordId,
                    $sessionDate,
                    $applicationKey,
                    forUpdate: true,
                );

                if ($existing) {
                    return $existing;
                }

                return $this->createTodaySession(
                    $staffId,
                    $recordType,
                    $recordId,
                    $applicationId,
                    $sessionDate,
                    $applicationKey,
                );
            });
        } catch (UniqueConstraintViolationException $exception) {
            // PostgreSQL aborts the whole transaction after a duplicate insert; retry in a new transaction.
            return DB::transaction(function () use ($staffId, $recordType, $recordId, $sessionDate, $applicationKey, $exception): StaffFileSession {
                $existing = $this->findTodaySession(
                    $staffId,
                    $recordType,
                    $recordId,
                    $sessionDate,
                    $applicationKey,
                    forUpdate: true,
                );

                if ($existing) {
                    return $existing;
                }

                throw $exception;
            });
        }
    }

    protected function createTodaySession(
        int $staffId,
        string $recordType,
        int $recordId,
        ?int $applicationId,
        string $sessionDate,
        int $applicationKey,
    ): StaffFileSession {
        $now = now();

        return StaffFileSession::query()->create([
            'staff_id' => $staffId,
            'record_type' => $recordType,
            'record_id' => $recordId,
            'application_id' => $applicationId,
            'application_key' => $applicationKey,
            'session_date' => $sessionDate,
            'status' => StaffFileSession::STATUS_ACCESSED,
            'focused_seconds' => 0,
            'idle_cut_seconds' => 0,
            'started_at' => $now,
            'last_heartbeat_at' => $now,
        ]);
    }

    protected function findTodaySession(
        int $staffId,
        string $recordType,
        int $recordId,
        string $sessionDate,
        int $applicationKey,
        bool $forUpdate = false,
    ): ?StaffFileSession {
        $query = StaffFileSession::query()
            ->where('staff_id', $staffId)
            ->where('record_type', $recordType)
            ->where('record_id', $recordId)
            ->whereDate('session_date', $sessionDate)
            ->where('application_key', $applicationKey);

        if ($forUpdate) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    protected function applyFocusedSeconds(StaffFileSession $session, int $focusedSeconds): void
    {
        $focusedSeconds = max(0, min(86400, $focusedSeconds));
        $session->focused_seconds = max((int) $session->focused_seconds, $focusedSeconds);
    }

    protected function reopenIfClosed(StaffFileSession $session): void
    {
        if ($session->status !== StaffFileSession::STATUS_CLOSED) {
            return;
        }

        $session->status = $session->isRecordedForBoard()
            ? StaffFileSession::STATUS_RECORDED
            : StaffFileSession::STATUS_ACCESSED;
        $session->ended_at = null;
    }

    protected function assertOwned(int $staffId, StaffFileSession $session): void
    {
        if ((int) $session->staff_id !== $staffId) {
            abort(404);
        }
    }

    protected function recordRef(StaffFileSession $session): string
    {
        if ($session->application_id) {
            $app = $session->relationLoaded('application')
                ? $session->application
                : Application::query()->find($session->application_id);

            if ($app) {
                $partnerName = null;
                if ($app->partner_id && Schema::hasTable('partners')) {
                    $partnerName = Partner::query()->where('id', $app->partner_id)->value('partner_name');
                }
                if ($partnerName) {
                    return trim((string) $partnerName).' · #'.$app->id;
                }

                return 'Application #'.$app->id;
            }
        }

        if ($session->isPartner()) {
            $name = Partner::query()->where('id', $session->record_id)->value('partner_name');

            return $name ? (string) $name : 'College #'.$session->record_id;
        }

        $client = Admin::query()->find($session->record_id, ['id', 'client_id', 'first_name', 'last_name']);
        if ($client) {
            $code = trim((string) ($client->client_id ?? ''));
            if ($code !== '') {
                return $code;
            }
            $name = trim(($client->first_name ?? '').' '.($client->last_name ?? ''));
            if ($name !== '') {
                return $name;
            }
        }

        return 'Record #'.$session->record_id;
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $events
     * @return list<array<string, mixed>>
     */
    protected function splitMinutesAcrossEvents(Collection $events, int $minutes): array
    {
        if ($minutes < 1) {
            return [];
        }

        $sorted = $events->sortBy([
            ['sort_at', 'asc'],
            ['key', 'asc'],
        ])->values();

        if ($sorted->isEmpty()) {
            return [[
                'key' => 'reviewed:0',
                'kind' => 'Reviewed file',
                'title' => 'Reviewed file',
                'minutes' => $minutes,
            ]];
        }

        $count = $sorted->count();
        $base = intdiv($minutes, $count);
        $remainder = $minutes % $count;
        $result = [];

        foreach ($sorted as $index => $event) {
            $eventMinutes = $base + ($index === 0 ? $remainder : 0);
            $row = $event;
            // Keep record_type/record_id/application_id for diary deep links in the activities popup.
            unset($row['sort_at']);
            $row['minutes'] = $eventMinutes;
            $result[] = $row;
        }

        return $result;
    }
}
