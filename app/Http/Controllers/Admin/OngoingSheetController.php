<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
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
        $activeFilterCount = $this->countActiveFilters($request);

        // Return view
        return view('Admin.sheets.ongoing', compact(
            'rows',
            'perPage',
            'activeFilterCount',
            'offices'
        ));
    }

    /**
     * Get filters from session when request has no filter params (so back/return preserves filters).
     */
    protected function getFiltersFromSession(Request $request): array
    {
        $filterParams = ['office', 'visa_expiry_from', 'visa_expiry_to', 'search', 'per_page'];
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
     * Build base query: clients with optional LEFT JOIN to ongoing references
     */
    protected function buildBaseQuery(Request $request)
    {
        $query = Admin::query()
            ->select([
                'admins.id as client_id',
                'admins.client_id as crm_ref',
                'admins.first_name',
                'admins.last_name',
                'admins.dob',
                'admins.visaexpiry',
                'admins.visa_type',
                'admins.visa_opt',
                'admins.office_id',
                // Ongoing reference data (LEFT JOIN)
                'ongoing.current_status',
                'ongoing.payment_display_note',
                'ongoing.institute_override',
                'ongoing.visa_category_override',
                // Payment sum (subquery)
                DB::raw('(SELECT COALESCE(SUM(deposit_amount), 0) 
                         FROM account_client_receipts 
                         WHERE client_id = admins.id 
                         AND receipt_type = 1) as total_payment'),
                // Institute from latest application (subquery)
                DB::raw('(SELECT partners.partner_name 
                         FROM applications 
                         LEFT JOIN partners ON applications.partner_id = partners.id 
                         WHERE applications.client_id = admins.id 
                         ORDER BY applications.id DESC 
                         LIMIT 1) as partner_name'),
                // Fallback institute from service_takens
                DB::raw('(SELECT edu_college 
                         FROM client_service_takens 
                         WHERE client_id = admins.id 
                         ORDER BY id DESC 
                         LIMIT 1) as service_college')
            ])
            ->leftJoin('client_ongoing_references as ongoing', 'ongoing.client_id', '=', 'admins.id')
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
                    ->orWhereRaw('LOWER(ongoing.current_status) LIKE ?', [$search]);
            });
        }

        return $query;
    }

    /**
     * Apply sorting to query
     */
    protected function applySorting($query, Request $request)
    {
        $sortField = $request->get('sort', 'client_id');
        $sortDirection = $request->get('direction', 'asc');

        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $sortableFields = [
            'client_id' => 'admins.client_id',
            'name' => 'admins.first_name',
            'dob' => 'admins.dob',
            'visa_expiry' => 'admins.visaexpiry',
        ];

        $actualSortField = $sortableFields[$sortField] ?? 'admins.client_id';
        $query->orderBy($actualSortField, $sortDirection);

        return $query;
    }

    /**
     * Count active filters
     */
    protected function countActiveFilters(Request $request)
    {
        $filters = ['office', 'visa_expiry_from', 'visa_expiry_to', 'search'];
        $count = 0;
        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $count++;
            }
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
}
