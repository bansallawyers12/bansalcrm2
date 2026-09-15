<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\StaffDaySummary;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Throwable;

class StaffDaySummaryService
{
    public function __construct(
        protected StaffWorkloadService $workloadService,
        protected StaffFileTimeService $fileTime,
        protected StaffDayCrmEventsService $crmEvents,
        protected StaffDayHoursService $hours,
        protected StaffFileSessionService $fileSessions,
    ) {}

    public function find(int $staffId, ?Carbon $day = null): ?StaffDaySummary
    {
        if (! Schema::hasTable('staff_day_summaries')) {
            return null;
        }

        [$start] = $this->workloadService->dayBounds($day ?? now());

        return StaffDaySummary::query()
            ->where('staff_id', $staffId)
            ->whereDate('summary_date', $start->toDateString())
            ->first();
    }

    /**
     * @param  list<int>  $staffIds
     * @return Collection<int, true>
     */
    public function savedStaffIdsForDay(array $staffIds, ?Carbon $day = null): Collection
    {
        if ($staffIds === [] || ! Schema::hasTable('staff_day_summaries')) {
            return collect();
        }

        [$start] = $this->workloadService->dayBounds($day ?? now());

        return StaffDaySummary::query()
            ->whereDate('summary_date', $start->toDateString())
            ->whereIn('staff_id', $staffIds)
            ->pluck('staff_id')
            ->mapWithKeys(fn ($id) => [(int) $id => true]);
    }

    /**
     * @param  array{text?: string}  $summary
     */
    public function upsert(int $staffId, array $summary, string $source, ?Carbon $day = null): StaffDaySummary
    {
        [$start] = $this->workloadService->dayBounds($day ?? now());
        $date = $start->toDateString();
        $body = (string) ($summary['text'] ?? '');

        $row = $this->find($staffId, $day);
        if ($row === null) {
            $row = new StaffDaySummary;
            $row->staff_id = $staffId;
            $row->summary_date = $date;
        }

        $row->body = $body;
        $row->source = $source;
        $row->saved_at = now();

        try {
            $row->save();
        } catch (UniqueConstraintViolationException $e) {
            $existing = $this->find($staffId, $day);
            if ($existing === null) {
                throw $e;
            }
            $existing->body = $body;
            $existing->source = $source;
            $existing->saved_at = now();
            $existing->save();

            return $existing->fresh() ?? $existing;
        }

        return $row->fresh() ?? $row;
    }

    /**
     * @return array{text: string, stored: bool, source: string|null, saved_at: string|null}
     */
    public function payload(?StaffDaySummary $row): array
    {
        if ($row === null) {
            return [
                'text' => '',
                'stored' => false,
                'source' => null,
                'saved_at' => null,
            ];
        }

        $tz = (string) config('app.timezone');

        return [
            'text' => (string) $row->body,
            'stored' => true,
            'source' => (string) $row->source,
            'saved_at' => $row->saved_at?->timezone($tz)->toIso8601String(),
        ];
    }

    public function snapshotStaff(int $staffId, string $source, ?Carbon $day = null): StaffDaySummary
    {
        $summary = $this->fileTime->copySummary(
            $staffId,
            $this->crmEvents,
            $this->hours,
            $this->fileSessions,
            $day,
        );

        return $this->upsert($staffId, $summary, $source, $day);
    }

    public function snapshotActiveStaff(string $source = StaffDaySummary::SOURCE_SCHEDULE, ?Carbon $day = null): int
    {
        if (! Schema::hasTable('staff_day_summaries')) {
            return 0;
        }

        $count = 0;
        Staff::query()->active()->orderBy('id')->each(function (Staff $staff) use ($source, $day, &$count): void {
            try {
                $this->snapshotStaff((int) $staff->id, $source, $day);
                $count++;
            } catch (Throwable $e) {
                report($e);
            }
        });

        return $count;
    }
}
