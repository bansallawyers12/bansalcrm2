<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Models\Application;
use App\Models\ApplicationActivitiesLog;
use App\Models\ClientOngoingReference;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OngoingSheetController extends Controller
{
    public const SHEET_TYPES = ['ongoing', 'coe_enrolled', 'discontinue'];

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /** Session key for persisting ongoing sheet filters (legacy) */
    const FILTER_SESSION_KEY = 'ongoing_sheet_filters';

    /** @var string|null Session key for current request (set in index) */
    protected $currentFilterSessionKey;

    /**
     * Config for each sheet type: title, route name, session key.
     */
    public static function getSheetConfig(string $sheetType): array
    {
        $configs = [
            'ongoing'       => ['title' => 'Ongoing Sheet', 'route' => 'clients.sheets.ongoing', 'session_key' => 'ongoing_sheet_filters'],
            'coe_enrolled' => ['title' => 'COE Issued & Enrolled', 'route' => 'clients.sheets.coe-enrolled', 'session_key' => 'coe_enrolled_sheet_filters'],
            'discontinue'   => ['title' => 'Discontinue', 'route' => 'clients.sheets.discontinue', 'session_key' => 'discontinue_sheet_filters'],
        ];
        return $configs[$sheetType] ?? $configs['ongoing'];
    }

    /**
     * Display a sheet (Ongoing, COE Issued & Enrolled, or Discontinue).
     */
    public function index(Request $request, $sheetType = null)
    {
        $sheetType = $sheetType ?? $request->route('sheetType', 'ongoing');
        if (!in_array($sheetType, self::SHEET_TYPES, true)) {
            $sheetType = 'ongoing';
        }
        $config = self::getSheetConfig($sheetType);
        $this->currentFilterSessionKey = $config['session_key'];

        // Clear stored filters when user explicitly requests it
        if ($request->has('clear_filters')) {
            session()->forget($this->currentFilterSessionKey);
            return redirect()->route($config['route']);
        }

        // Merge request with session-stored filters (session as fallback when no query params)
        $request->merge($this->getFiltersFromSession($request));

        // Default assignee to logged-in user when not set (first visit); "all" = show all assignees
        if (!$request->has('assignee') || $request->input('assignee') === '') {
            $request->merge(['assignee' => Auth::id()]);
        }

        // Pagination
        $perPage = (int) $request->get('per_page', 50);
        $allowedPerPage = [10, 25, 50, 100, 200];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 50;
        }

        // Persist current filters to session when filters are applied (has query params)
        $this->persistFiltersToSession($request);

        // Build base query (depends on sheet type)
        $query = $this->buildBaseQuery($request, $sheetType);

        // Apply filters
        $query = $this->applyFilters($query, $request);

        // Apply sorting
        $query = $this->applySorting($query, $request);

        // Get rows (paginate)
        $rows = $query->paginate($perPage)->appends($request->except('page'));

        // Dropdown data for filters (staff who have at least one application + current user)
        $offices = Branch::orderBy('office_name')->get(['id', 'office_name']);
        $branches = Branch::orderBy('office_name')->get(['id', 'office_name']);
        $assignees = Admin::where('status', 1)
            ->whereIn('id', Application::select('user_id')->whereNotNull('user_id')->distinct())
            ->orderBy('first_name')->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);
        $currentUser = Auth::user();
        if ($currentUser && $assignees->pluck('id')->doesntContain($currentUser->id)) {
            $assignees->push($currentUser);
            $assignees = $assignees->sortBy(fn ($a) => trim(($a->first_name ?? '') . ' ' . ($a->last_name ?? '')))->values();
        }
        // Full staff list for Change assignee modal (active only, same as Application tab)
        $assigneesForChangeModal = Admin::where('role', '!=', 7)
            ->where('status', 1)
            ->orderBy('first_name')->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);
        $currentStages = $this->getCurrentStagesForSheet($sheetType);
        $activeFilterCount = $this->countActiveFilters($request);

        return view('Admin.sheets.ongoing', compact(
            'rows',
            'perPage',
            'activeFilterCount',
            'offices',
            'branches',
            'assignees',
            'assigneesForChangeModal',
            'currentStages',
            'sheetType'
        ) + [
            'sheetTitle' => $config['title'],
            'sheetRoute' => $config['route'],
        ]);
    }

    /**
     * Get filters from session when request has no filter params (so back/return preserves filters).
     */
    protected function getFiltersFromSession(Request $request): array
    {
        $filterParams = ['office', 'assignee', 'branch', 'current_stage', 'visa_expiry_from', 'visa_expiry_to', 'search', 'per_page'];
        $hasAnyParam = false;
        foreach ($filterParams as $key) {
            if ($request->has($key) && $request->input($key) !== null && $request->input($key) !== '') {
                $hasAnyParam = true;
                break;
            }
        }
        if ($hasAnyParam) {
            return [];
        }
        $key = $this->currentFilterSessionKey ?? self::FILTER_SESSION_KEY;
        return session($key, []);
    }

    /**
     * Persist current filter values to session for next visit.
     */
    protected function persistFiltersToSession(Request $request): void
    {
        $payload = [
            'office' => $request->input('office'),
            'assignee' => $request->input('assignee'),
            'branch' => $request->input('branch'),
            'current_stage' => $request->input('current_stage'),
            'visa_expiry_from' => $request->input('visa_expiry_from'),
            'visa_expiry_to' => $request->input('visa_expiry_to'),
            'search' => $request->input('search'),
            'per_page' => $request->input('per_page'),
        ];
        $payload = array_filter($payload, function ($v) {
            if (is_array($v)) {
                return !empty($v);
            }
            return $v !== null && $v !== '';
        });
        $key = $this->currentFilterSessionKey ?? self::FILTER_SESSION_KEY;
        session()->put($key, $payload);
    }

    /**
     * Stage dropdown options for the current sheet type.
     */
    protected function getCurrentStagesForSheet(string $sheetType): \Illuminate\Support\Collection
    {
        if ($sheetType === 'coe_enrolled') {
            return Application::select('stage')
                ->whereNotIn('status', [2])
                ->whereRaw('LOWER(TRIM(stage)) IN (?, ?)', ['coe issued', 'enrolled'])
                ->distinct()->orderBy('stage')->pluck('stage', 'stage');
        }
        if ($sheetType === 'discontinue') {
            return Application::where('status', 2)
                ->select('stage')
                ->distinct()->orderBy('stage')->pluck('stage', 'stage');
        }
        return Application::select('stage')
            ->whereNotIn('status', [2])
            ->whereRaw('LOWER(TRIM(stage)) NOT IN (?, ?, ?)', ['coe issued', 'enrolled', 'coe cancelled'])
            ->distinct()->orderBy('stage')->pluck('stage', 'stage');
    }

    /**
     * Build base query: one row per application (application-focused sheet).
     * Criteria depend on sheet type: ongoing, coe_enrolled, or discontinue.
     */
    protected function buildBaseQuery(Request $request, string $sheetType = 'ongoing')
    {
        $query = Application::query()
            ->select([
                'applications.id as application_id',
                'applications.stage as application_stage',
                'products.name as course_name',
                'admins.id as client_id',
                'admins.client_id as crm_ref',
                'admins.first_name',
                'admins.last_name',
                'admins.dob',
                'admins.visaexpiry',
                'admins.visa_type',
                'admins.visa_opt',
                'admins.office_id',
                'partners.partner_name',
                'branches.office_name as branch_name',
                'applications.user_id as assignee_id',
                'assignee.first_name as assignee_first_name',
                'assignee.last_name as assignee_last_name',
                'ongoing.current_status',
                'ongoing.payment_display_note',
                'ongoing.institute_override',
                'ongoing.visa_category_override',
                DB::raw('(SELECT COALESCE(SUM(deposit_amount), 0) 
                         FROM account_client_receipts 
                         WHERE client_id = admins.id 
                         AND receipt_type = 1) as total_payment'),
                DB::raw('(SELECT edu_college 
                         FROM client_service_takens 
                         WHERE client_id = admins.id 
                         ORDER BY id DESC 
                         LIMIT 1) as service_college'),
                DB::raw("(SELECT aal.comment FROM application_activities_logs aal 
                         WHERE aal.app_id = applications.id AND aal.type = 'sheet_comment' 
                         ORDER BY aal.updated_at DESC LIMIT 1) as sheet_comment_text")
            ])
            ->join('admins', 'applications.client_id', '=', 'admins.id')
            ->leftJoin('products', 'applications.product_id', '=', 'products.id')
            ->leftJoin('partners', 'applications.partner_id', '=', 'partners.id')
            ->leftJoin('branches', 'admins.office_id', '=', 'branches.id')
            ->leftJoin('admins as assignee', 'applications.user_id', '=', 'assignee.id')
            ->leftJoin('client_ongoing_references as ongoing', 'ongoing.client_id', '=', 'admins.id')
            ->where('admins.role', 7)
            ->where('admins.is_archived', 0)
            ->whereNull('admins.is_deleted');

        if ($sheetType === 'discontinue') {
            $query->where('applications.status', 2);
        } else {
            $query->whereNotIn('applications.status', [2]);
            if ($sheetType === 'coe_enrolled') {
                $query->whereRaw('LOWER(TRIM(applications.stage)) IN (?, ?)', ['coe issued', 'enrolled']);
            } else {
                $query->whereRaw('LOWER(TRIM(applications.stage)) NOT IN (?, ?, ?)', [
                    'coe issued',
                    'enrolled',
                    'coe cancelled',
                ]);
            }
        }

        return $query;
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters($query, Request $request)
    {
        // Office filter
        if ($request->filled('office')) {
            $offices = is_array($request->input('office'))
                ? $request->input('office')
                : [$request->input('office')];
            $query->whereIn('admins.office_id', $offices);
        }

        // Assignee filter ("all" = no filter)
        if ($request->filled('assignee') && $request->input('assignee') !== 'all') {
            $query->where('applications.user_id', $request->input('assignee'));
        }

        // Branch filter (client's office/branch)
        if ($request->filled('branch')) {
            $query->where('admins.office_id', $request->input('branch'));
        }

        // Current stage filter
        if ($request->filled('current_stage')) {
            $query->where('applications.stage', $request->input('current_stage'));
        }

        // Visa expiry date range
        if ($request->filled('visa_expiry_from')) {
            try {
                $fromDate = Carbon::createFromFormat('d/m/Y', $request->input('visa_expiry_from'))->startOfDay();
                $query->whereDate('admins.visaexpiry', '>=', $fromDate);
            } catch (\Exception $e) {
                // Ignore invalid date format
            }
        }

        if ($request->filled('visa_expiry_to')) {
            try {
                $toDate = Carbon::createFromFormat('d/m/Y', $request->input('visa_expiry_to'))->endOfDay();
                $query->whereDate('admins.visaexpiry', '<=', $toDate);
            } catch (\Exception $e) {
                // Ignore invalid date format
            }
        }

        // Search (name, CRM ref, current status)
        if ($request->filled('search')) {
            $search = '%' . strtolower($request->input('search')) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(admins.first_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(admins.last_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(admins.client_id) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(ongoing.current_status) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(applications.stage) LIKE ?', [$search]);
            });
        }

        return $query;
    }

    /**
     * Apply sorting to query. Rows for the same client always stay together:
     * order by sort column (client-level), then client id, then application id.
     */
    protected function applySorting($query, Request $request)
    {
        $sortField = $request->get('sort', 'client_id');
        $sortDirection = $request->get('direction', 'asc');

        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $sortableFields = [
            'application_id' => 'applications.id',
            'client_id' => 'admins.client_id',
            'name' => 'admins.first_name',
            'dob' => 'admins.dob',
            'visa_expiry' => 'admins.visaexpiry',
        ];

        $actualSortField = $sortableFields[$sortField] ?? 'admins.client_id';
        // Keep all applications of the same client together: sort by chosen field, then client, then application
        $query->orderBy($actualSortField, $sortDirection)
              ->orderBy('admins.id', 'asc')
              ->orderBy('applications.id', 'asc');

        return $query;
    }

    /**
     * Count active filters
     */
    protected function countActiveFilters(Request $request)
    {
        $count = 0;
        if ($request->filled('office')) {
            $count++;
        }
        if ($request->filled('assignee') && $request->input('assignee') !== 'all') {
            $count++;
        }
        if ($request->filled('branch')) {
            $count++;
        }
        if ($request->filled('current_stage')) {
            $count++;
        }
        if ($request->filled('visa_expiry_from')) {
            $count++;
        }
        if ($request->filled('visa_expiry_to')) {
            $count++;
        }
        if ($request->filled('search')) {
            $count++;
        }
        return $count;
    }

    /**
     * Display insights view (optional - can be implemented later)
     */
    public function insights(Request $request)
    {
        $baseQuery = $this->buildBaseQuery($request);
        $baseQuery = $this->applyFilters($baseQuery, $request);
        $allRecords = $baseQuery->get();

        $insights = [
            'total_clients' => $allRecords->count(),
            'total_payments' => $allRecords->sum('total_payment'),
            'avg_payment' => $allRecords->avg('total_payment'),
            'clients_with_visa_expiry' => $allRecords->whereNotNull('visaexpiry')->count(),
        ];

        $activeFilterCount = $this->countActiveFilters($request);

        return view('Admin.sheets.ongoing-insights', compact('insights', 'activeFilterCount'));
    }

    /**
     * Update ongoing reference for a client (optional - for future use)
     */
    public function updateReference(Request $request, $clientId)
    {
        $request->validate([
            'current_status' => 'nullable|string',
            'payment_display_note' => 'nullable|string|max:100',
            'institute_override' => 'nullable|string|max:255',
            'visa_category_override' => 'nullable|string|max:50',
        ]);

        $ongoingRef = ClientOngoingReference::updateOrCreate(
            ['client_id' => $clientId],
            [
                'current_status' => $request->input('current_status'),
                'payment_display_note' => $request->input('payment_display_note'),
                'institute_override' => $request->input('institute_override'),
                'visa_category_override' => $request->input('visa_category_override'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Ongoing reference updated successfully',
            'data' => $ongoingRef
        ]);
    }

    /**
     * Store or replace sheet comment for an application (Option A: one current comment per app).
     * Appears in Notes & Activity with course and college name; filter by sheet_comment.
     */
    public function storeSheetComment(Request $request)
    {
        $request->validate([
            'application_id' => 'required|integer|exists:applications,id',
            'comment' => 'required|string|max:65535',
        ]);

        $app = Application::with(['product', 'partner'])->findOrFail($request->application_id);
        $courseName = $app->product ? $app->product->name : '—';
        $collegeName = $app->partner ? $app->partner->partner_name : '—';
        $title = "Course: {$courseName}, College: {$collegeName}";

        $log = ApplicationActivitiesLog::updateOrCreate(
            [
                'app_id' => $request->application_id,
                'type' => 'sheet_comment',
            ],
            [
                'stage' => 'Sheet comment',
                'comment' => $request->comment,
                'title' => $title,
                'description' => '',
                'user_id' => Auth::id(),
            ]
        );

        // Also log to client Activities feed so it appears on the client page
        if ($app->client_id) {
            ActivitiesLog::create([
                'client_id' => $app->client_id,
                'created_by' => Auth::id(),
                'subject' => "added a sheet comment",
                'description' => "<strong>Sheet comment</strong> ({$title}): " . e($request->comment),
                'task_status' => 0,
                'pin' => 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Comment saved. Previous comment replaced.',
            'data' => ['comment' => $log->comment, 'title' => $log->title],
        ]);
    }
}
