<?php

namespace App\Services;

use App\Models\StaffLoginLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

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
        [$start, $end] = $this->workloadService->dayBounds($day ?? now());
        $dateKey = $start->toDateString();

        $viewerId = Auth::guard('admin')->id();
        $isLiveToday = $viewerId !== null
            && (int) $viewerId === $staffId
            && $dateKey === now()->timezone((string) config('app.timezone'))->toDateString();

        if ($isLiveToday) {
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

        return $this->fromLoginLogs($staffId, $start, $end, $dateKey);
    }

    /**
     * @return array{label: string, minutes: int, seconds: int, source: string, date: string}
     */
    protected function fromLoginLogs(int $staffId, Carbon $start, Carbon $end, string $dateKey): array
    {
        $empty = [
            'label' => '—',
            'minutes' => 0,
            'seconds' => 0,
            'source' => 'none',
            'date' => $dateKey,
        ];

        if (! Schema::hasTable('staff_login_logs')) {
            return $empty;
        }

        $login = StaffLoginLog::query()
            ->where('user_id', $staffId)
            ->where('message', 'Logged in successfully')
            ->where('created_at', '<=', $end)
            ->orderByDesc('created_at')
            ->first();

        if ($login === null) {
            return $empty;
        }

        $loginAt = Carbon::parse($login->created_at);
        $until = now()->lt($end) ? now() : $end->copy();
        $from = $loginAt->greaterThan($start) ? $loginAt : $start->copy();
        if ($until->lessThan($from)) {
            return $empty;
        }

        $seconds = max(0, (int) abs($until->diffInSeconds($from)));

        return [
            'label' => $this->formatHoursAndMinutes($seconds),
            'minutes' => (int) floor($seconds / 60),
            'seconds' => $seconds,
            'source' => 'login_log',
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
