<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StaffDayHoursService
{
    public function __construct(
        protected StaffWorkloadService $workloadService,
        protected DashboardService $dashboardService,
    ) {}

    /**
     * Hours-in-CRM label for the diary header.
     *
     * @return array{label: string, minutes: int, seconds: int, source: string, date: string}
     */
    public function forStaff(int $staffId, ?Carbon $day = null): array
    {
        [$start] = $this->workloadService->dayBounds($day ?? now());
        $dateKey = $start->toDateString();

        $viewerId = Auth::guard('admin')->id();
        if ($viewerId !== null && (int) $viewerId === $staffId) {
            $stats = $this->dashboardService->getLoginStatistics();
            $seconds = max(0, (int) abs((float) ($stats['current_session_duration'] ?? 0)));

            return [
                'label' => $this->formatHoursAndMinutes($seconds),
                'minutes' => (int) floor($seconds / 60),
                'seconds' => $seconds,
                'source' => 'login_session',
                'date' => $dateKey,
            ];
        }

        return [
            'label' => '—',
            'minutes' => 0,
            'seconds' => 0,
            'source' => 'none',
            'date' => $dateKey,
        ];
    }

    /**
     * Diary-facing label: prefer hours + minutes (never raw seconds).
     */
    protected function formatHoursAndMinutes(int $seconds): string
    {
        $seconds = max(0, $seconds);
        $hours = (int) floor($seconds / 3600);
        $minutes = (int) floor(($seconds % 3600) / 60);

        if ($hours > 0 && $minutes > 0) {
            return $hours.'h '.$minutes.'m';
        }

        if ($hours > 0) {
            return $hours.'h';
        }

        return $minutes.'m';
    }
}
