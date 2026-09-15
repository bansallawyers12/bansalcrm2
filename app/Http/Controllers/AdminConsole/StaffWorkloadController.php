<?php

namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Services\StaffDaySummaryService;
use App\Services\StaffWorkloadService;
use App\Support\ArrayPaginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StaffWorkloadController extends Controller
{
    public const TEAM_OVERVIEW_PER_PAGE = 20;

    public function __construct(
        private StaffWorkloadService $staffWorkloadService,
        private StaffDaySummaryService $staffDaySummaryService,
    ) {
        $this->middleware('auth:admin');
    }

    /**
     * Super Admin only (same gate as Recently Modified Clients).
     */
    private function ensureSuperAdminAccess(): void
    {
        if ((Auth::user()->role ?? null) != 1) {
            abort(403, 'Unauthorized.');
        }
    }

    public function index(Request $request)
    {
        $this->ensureSuperAdminAccess();

        try {
            $teamOverview = $this->paginateTeamOverview($this->attachSummarySavedFlags(
                $this->staffWorkloadService->getTeamOverview()
            ));
        } catch (\Throwable $e) {
            Log::error('Staff workload team overview failed: '.$e->getMessage());

            return view('AdminConsole.staff_workload.index', [
                'teamOverview' => $this->paginateTeamOverview([
                    'day_label' => now()->timezone(config('app.timezone'))->format('l, j F Y'),
                    'rows' => [],
                ]),
            ])->with('error', 'Could not load staff workload data. Please try again.');
        }

        return view('AdminConsole.staff_workload.index', compact('teamOverview'));
    }

    public function show(Request $request, Staff $staff)
    {
        $this->ensureSuperAdminAccess();

        if ((int) ($staff->status ?? 0) !== 1) {
            abort(404);
        }

        try {
            $summary = $this->staffWorkloadService->getDaySummary((int) $staff->id);
        } catch (\Throwable $e) {
            Log::error('Staff workload detail failed: '.$e->getMessage(), ['staff_id' => $staff->id]);

            return redirect()
                ->route('adminconsole.staff-workload.index')
                ->with('error', 'Could not load workload for this staff member.');
        }

        $daySummary = $this->staffDaySummaryService->payload(
            $this->staffDaySummaryService->find((int) $staff->id)
        );

        return view('AdminConsole.staff_workload.show', [
            'staff' => $staff,
            'summary' => $summary,
            'daySummary' => $daySummary,
        ]);
    }

    /**
     * @param  array{day_label?: string, rows?: list<array<string, mixed>>}  $teamOverview
     * @return array{day_label?: string, rows: list<array<string, mixed>>}
     */
    private function attachSummarySavedFlags(array $teamOverview): array
    {
        $rows = $teamOverview['rows'] ?? [];
        $ids = array_values(array_filter(array_map(
            fn ($row) => (int) ($row['staff_id'] ?? 0),
            $rows
        )));
        $saved = $this->staffDaySummaryService->savedStaffIdsForDay($ids);

        foreach ($rows as $index => $row) {
            $rows[$index]['summary_saved'] = $saved->has((int) ($row['staff_id'] ?? 0));
        }

        $teamOverview['rows'] = $rows;

        return $teamOverview;
    }

    /**
     * @param  array{day_label?: string, rows?: list<array<string, mixed>>}  $teamOverview
     * @return array{day_label?: string, rows: LengthAwarePaginator}
     */
    private function paginateTeamOverview(array $teamOverview): array
    {
        $teamOverview['rows'] = ArrayPaginator::make(
            $teamOverview['rows'] ?? [],
            'page',
            self::TEAM_OVERVIEW_PER_PAGE,
        );

        return $teamOverview;
    }
}
