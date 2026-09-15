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
            $seconds = (int) ($stats['current_session_duration'] ?? 0);
            $label = (string) ($stats['current_session_duration_formatted'] ?? '—');

            return [
                'label' => $label !== '' ? $label : '—',
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
}
