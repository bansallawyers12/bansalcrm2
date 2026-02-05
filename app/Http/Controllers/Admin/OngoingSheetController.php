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
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /** Session key for persisting ongoing sheet filters */
    const FILTER_SESSION_KEY = 'ongoing_sheet_filters';

    /**
     * Display the Ongoing Sheet - List view
     */
    public function index(Request $request)
    {
        // Clear stored filters when user explicitly requests it
        if ($request->has('clear_filters')) {
            session()->forget(self::FILTER_SESSION_KEY);
            return redirect()->route('clients.sheets.ongoing');
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

        // Build base query
        $query = $this->buildBaseQuery($request);

        // Apply filters
        $query = $this->applyFilters($query, $request);

        // Apply sorting
        $query = $this->applySorting($query, $request);

        // Get rows (paginate)
        $rows = $query->paginate($perPage)->appends($request->except('page'));

        // Dropdown data for filters
        $offices = Branch::orderBy('office_name')->get(['id', 'office_name']);
        $branches = Branch::orderBy('office_name')->get(['id', 'office_name']);
        $assignees = Admin::whereIn('id', Application::select('user_id')->whereNotNull('user_id')->distinct())
            ->orderBy('first_name')->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);
        // Ensure current user is in the list so they can select themselves by default
        $currentUser = Auth::user();
        if ($currentUser && $assignees->pluck('id')->doesntContain($currentUser->id)) {
            $assignees->push($currentUser);
            $assignees = $assignees->sortBy(fn ($a) => trim(($a->first_name ?? '') . ' ' . ($a->last_name ?? '')))->values();
        }
        $currentStages = Application::select('stage')
            ->whereNotIn('status', [2])
            ->whereRaw('LOWER(TRIM(stage)) NOT IN (?, ?, ?)', ['coe issued', 'enrolled', 'coe cancelled'])
            ->distinct()->orderBy('stage')->pluck('stage', 'stage');
        $activeFilterCount = $this->countActiveFilters($request);

        // Return view
        return view('Admin.sheets.ongoing', compact(
            'rows',
            'perPage',
            'activeFilterCount',
            'offices',
            'branches',
            'assignees',
            'currentStages'
        ));
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
        return session(self::FILTER_SESSION_KEY, []);
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
        session()->put(self::FILTER_SESSION_KEY, $payload);
    }

    /**
     * Build base query: one row per application (application-focused sheet).
     * Clients with multiple applications get multiple rows; rows for the same client stay together when sorting.
     */
    protected function buildBaseQuery(Request $request)
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
                // Institute for this application (from joined partner)
                'partners.partner_name',
                // Our office branch (client's assigned office)
                'branches.office_name as branch_name',
                // Assignee (user assigned to this application)
                'assignee.first_name as assignee_first_name',
                'assignee.last_name as assignee_last_name',
                // Ongoing reference data (per client)
                'ongoing.current_status',
                'ongoing.payment_display_note',
                'ongoing.institute_override',
                'ongoing.visa_category_override',
                // Payment sum (subquery, per client)
                DB::raw('(SELECT COALESCE(SUM(deposit_amount), 0) 
                         FROM account_client_receipts 
                         WHERE client_id = admins.id 
                         AND receipt_type = 1) as total_payment'),
                // Fallback institute from service_takens (per client)
                DB::raw('(SELECT edu_college 
                         FROM client_service_takens 
                         WHERE client_id = admins.id 
                         ORDER BY id DESC 
                         LIMIT 1) as service_college'),
                // Latest sheet comment (one per application, replaced on each new comment)
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
            ->whereNotIn('applications.status', [2]) // Exclude discontinued applications
            ->whereRaw('LOWER(TRIM(applications.stage)) NOT IN (?, ?, ?)', [
                'coe issued',
                'enrolled',
                'coe cancelled',
            ])
            ->where('admins.role', 7) // Clients only
            ->where('admins.is_archived', 0)
            ->whereNull('admins.is_deleted');

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
