<?php

namespace App\Console\Commands;

use App\Models\StaffDaySummary;
use App\Services\StaffDaySummaryService;
use App\Services\StaffFileSessionService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SnapshotStaffDaySummaries extends Command
{
    protected $signature = 'my-day:snapshot-summaries {--date= : Melbourne Y-m-d to snapshot (default: today)}';

    protected $description = 'Save each active staff member\'s My Day copy-summary for a Melbourne date';

    public function handle(
        StaffFileSessionService $sessions,
        StaffDaySummaryService $summaries,
    ): int {
        $dateOption = $this->option('date');
        $day = null;
        if (is_string($dateOption) && $dateOption !== '') {
            $tz = (string) config('app.timezone');
            try {
                $parsed = Carbon::createFromFormat('Y-m-d', $dateOption, $tz);
            } catch (\Throwable) {
                $parsed = false;
            }
            if ($parsed === false || $parsed->format('Y-m-d') !== $dateOption) {
                $this->error('Invalid --date. Use Y-m-d in the app timezone.');

                return self::FAILURE;
            }
            $day = $parsed->startOfDay();
        }

        $sessions->closeStale(now());
        $count = $summaries->snapshotActiveStaff(StaffDaySummary::SOURCE_SCHEDULE, $day);
        $this->info("Saved {$count} staff day summary snapshot(s).");

        return self::SUCCESS;
    }
}
