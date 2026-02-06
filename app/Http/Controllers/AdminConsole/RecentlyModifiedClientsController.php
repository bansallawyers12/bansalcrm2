<?php
namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Models\Application;
use App\Models\Document;
use Carbon\Carbon;

use Auth; 
use Config;

class RecentlyModifiedClientsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
	
	/**
     * Display recently modified clients based on activities log.
     *
     * @return \Illuminate\Http\Response 
     */
	public function index(Request $request)
	{
		// Get filter parameters from request
		$fromDate = $request->input('from_date');
		$toDate = $request->input('to_date');
		$sortOrder = $request->input('sort_order', 'desc'); // Default to descending (newest first)
		$search = $request->input('search', '');
		$activityType = $request->input('activity_type', '');
		$perPage = $request->input('per_page', config('constants.limit', 20));
		$sortColumn = $request->input('sort_column', 'activity_date');
		$hasApplications = $request->input('has_applications', ''); // '' = all, '0' = no applications
		$lastActivityYears = $request->input('last_activity_years', ''); // 1, 2, 3, 4, 5 = X+ years ago
		$documentCount = $request->input('document_count', ''); // '', '0', '1', ... '9', '10+' = documents count filter
		$noPhone = $request->input('no_phone', ''); // '' = all, '1' = only clients with no phone number
		$noEmail = $request->input('no_email', ''); // '' = all, '1' = only clients with no email address
		
		// Get the most recent activity for each client
		// Filter out NULL client_id to avoid orphaned activities
		$subQuery = ActivitiesLog::select('client_id', DB::raw('MAX(created_at) as last_activity'))
			->whereNotNull('client_id')
			->groupBy('client_id');
		
		// Clients live in admins table (role = 7). Join admins twice: client info + creator info.
		$query = ActivitiesLog::select(
				'activities_logs.id as activity_id',
				'activities_logs.client_id',
				'activities_logs.created_by',
				'activities_logs.subject',
				'activities_logs.description',
				'activities_logs.created_at as activity_date',
				'client_admins.first_name as client_firstname',
				'client_admins.last_name as client_lastname',
				'client_admins.email as client_email',
				'client_admins.phone as client_phone',
				'admins.first_name as admin_firstname',
				'admins.last_name as admin_lastname'
			)
			->joinSub($subQuery, 'latest_activities', function($join) {
				$join->on('activities_logs.client_id', '=', 'latest_activities.client_id')
					 ->on('activities_logs.created_at', '=', 'latest_activities.last_activity');
			})
			->leftJoin('admins as client_admins', function($join) {
				$join->on('activities_logs.client_id', '=', 'client_admins.id')
					 ->where('client_admins.role', '=', '7')
					 ->where('client_admins.is_archived', '=', '0');
			})
			->leftJoin('admins', 'activities_logs.created_by', '=', 'admins.id');
		
		// Subquery: document count per client (non-archived only) for document count filter
		$docCountSubQuery = Document::select('client_id', DB::raw('COUNT(*) as doc_count'))
			->whereNull('archived_at')
			->groupBy('client_id');
		$query->leftJoinSub($docCountSubQuery, 'doc_counts', 'activities_logs.client_id', '=', 'doc_counts.client_id');
		
		// Apply search filter (name, email, phone)
		if (!empty($search)) {
			$query->where(function($q) use ($search) {
				$q->where(DB::raw("CONCAT(client_admins.first_name, ' ', client_admins.last_name)"), 'LIKE', "%{$search}%")
				  ->orWhere('client_admins.email', 'LIKE', "%{$search}%")
				  ->orWhere('client_admins.phone', 'LIKE', "%{$search}%");
			});
		}
		
		// Apply activity type filter
		if (!empty($activityType)) {
			$query->where(function($q) use ($activityType) {
				$q->where('activities_logs.subject', 'LIKE', "%{$activityType}%")
				  ->orWhere('activities_logs.description', 'LIKE', "%{$activityType}%");
			});
		}
		
		// Apply date filters to main query if provided
		// This filters clients whose most recent activity falls within the date range
		if ($fromDate) {
			$query->whereDate('activities_logs.created_at', '>=', $fromDate);
		}
		if ($toDate) {
			$query->whereDate('activities_logs.created_at', '<=', $toDate);
		}
		
		// Filter: clients that have no applications created
		if ($hasApplications === '0') {
			$query->whereNotIn('activities_logs.client_id', Application::select('client_id'));
		}
		
		// Filter: last activity X+ years ago (1 to 5 years on yearly basis)
		if ($lastActivityYears !== '' && in_array((int) $lastActivityYears, [1, 2, 3, 4, 5], true)) {
			$query->where('activities_logs.created_at', '<=', Carbon::now()->subYears((int) $lastActivityYears));
		}
		
		// Filter: document count (0, 1, 2, ... 9, 10+)
		if ($documentCount !== '') {
			if ($documentCount === '0') {
				$query->where(function ($q) {
					$q->whereNull('doc_counts.doc_count')->orWhere('doc_counts.doc_count', 0);
				});
			} elseif ($documentCount === '10+') {
				$query->whereNotNull('doc_counts.doc_count')->where('doc_counts.doc_count', '>=', 10);
			} elseif (in_array($documentCount, ['1', '2', '3', '4', '5', '6', '7', '8', '9'], true)) {
				$query->where('doc_counts.doc_count', '=', (int) $documentCount);
			}
		}
		
		// Filter: no phone number (only clients with missing/empty phone)
		if ($noPhone === '1') {
			$query->where(function ($q) {
				$q->whereNull('client_admins.phone')
				  ->orWhere(DB::raw("TRIM(COALESCE(client_admins.phone, ''))"), '=', '');
			});
		}
		
		// Filter: no email address (only clients with missing/empty email)
		if ($noEmail === '1') {
			$query->where(function ($q) {
				$q->whereNull('client_admins.email')
				  ->orWhere(DB::raw("TRIM(COALESCE(client_admins.email, ''))"), '=', '');
			});
		}
		
		// Apply column sorting
		$allowedSortColumns = [
			'client_name' => DB::raw("CONCAT(client_admins.first_name, ' ', client_admins.last_name)"),
			'client_email' => 'client_admins.email',
			'client_phone' => 'client_admins.phone',
			'activity_date' => 'activities_logs.created_at',
			'modified_by' => DB::raw("CONCAT(admins.first_name, ' ', admins.last_name)"),
		];
		
		if (isset($allowedSortColumns[$sortColumn])) {
			$query->orderBy($allowedSortColumns[$sortColumn], $sortOrder);
		} else {
			$query->orderBy('activities_logs.created_at', $sortOrder);
		}
		
		$totalData = $query->count();
		
		// Paginate the results - preserve query parameters
		$lists = $query->paginate($perPage)
			->appends($request->query());
		
		return view('AdminConsole.recent_clients.index', compact([
			'lists', 
			'totalData', 
			'fromDate', 
			'toDate', 
			'sortOrder', 
			'search', 
			'activityType', 
			'perPage', 
			'sortColumn',
			'hasApplications',
			'lastActivityYears',
			'documentCount',
			'noPhone',
			'noEmail'
		])); 	
	}
	
	/**
     * Get client details for expandable row (AJAX)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
	public function getClientDetails(Request $request)
	{
		$clientId = $request->input('client_id');
		
		if (!$clientId) {
			return response()->json([
				'success' => false,
				'message' => 'Client ID is required'
			], 400);
		}
		
		// Get client info
		$client = Admin::where('id', $clientId)
			->where('role', '7')
			->first();
		
		if (!$client) {
			return response()->json([
				'success' => false,
				'message' => 'Client not found'
			], 404);
		}
		
		// Get last activity with creator info
		$lastActivity = ActivitiesLog::where('client_id', $clientId)
			->with('createdBy')
			->orderBy('created_at', 'desc')
			->first();
		
		// Get document count
		$documentCount = Document::where('client_id', $clientId)
			->whereNull('archived_at') // Only count non-archived documents
			->count();
		
		// Check if client is archived
		$isArchived = $client->is_archived == 1;
		
		return response()->json([
			'success' => true,
			'data' => [
				'client_id' => $clientId,
				'last_activity' => $lastActivity ? [
					'subject' => $lastActivity->subject,
					'description' => $lastActivity->description,
					'date' => $lastActivity->created_at->format('d/m/Y h:i A'),
					'created_by' => $lastActivity->createdBy ? 
						($lastActivity->createdBy->first_name . ' ' . $lastActivity->createdBy->last_name) : 
						'N/A'
				] : null,
				'document_count' => $documentCount,
				'is_archived' => $isArchived
			]
		]);
	}
	
	/**
     * Archive or unarchive a client
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
	public function toggleArchive(Request $request)
	{
		$clientId = $request->input('client_id');
		$action = $request->input('action'); // 'archive' or 'unarchive'
		
		if (!$clientId) {
			return response()->json([
				'success' => false,
				'message' => 'Client ID is required'
			], 400);
		}
		
		// Get client info
		$client = Admin::where('id', $clientId)
			->where('role', '7')
			->first();
		
		if (!$client) {
			return response()->json([
				'success' => false,
				'message' => 'Client not found'
			], 404);
		}
		
		// Determine archive status based on action
		if ($action === 'archive') {
			$isArchived = 1;
			$updateData = [
				'is_archived' => $isArchived,
				'archived_on' => date('Y-m-d'),
				'archived_by' => Auth::user()->id
			];
			$message = 'Client has been archived successfully.';
		} else if ($action === 'unarchive') {
			$isArchived = 0;
			$updateData = [
				'is_archived' => $isArchived,
				'archived_on' => null,
				'archived_by' => null
			];
			$message = 'Client has been unarchived successfully.';
		} else {
			return response()->json([
				'success' => false,
				'message' => 'Invalid action. Use "archive" or "unarchive".'
			], 400);
		}
		
		// Update the client
		$updated = DB::table('admins')->where('id', $clientId)->update($updateData);
		
		if ($updated) {
			// Log the activity
			$subject = $action === 'archive' ? 'Client has been archived' : 'Client has been unarchived';
			$activity = new ActivitiesLog();
			$activity->client_id = $clientId;
			$activity->created_by = Auth::user()->id;
			$activity->subject = $subject;
			$activity->description = $subject . ' by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name;
			$activity->task_status = 0;
			$activity->pin = 0;
			$activity->save();
			
			return response()->json([
				'success' => true,
				'message' => $message,
				'is_archived' => $isArchived
			]);
		} else {
			return response()->json([
				'success' => false,
				'message' => 'Failed to update client. Please try again.'
			], 500);
		}
	}
	
	/**
	 * Bulk archive selected clients
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function bulkArchive(Request $request)
	{
		$clientIds = $request->input('client_ids', []);
		
		if (empty($clientIds) || !is_array($clientIds)) {
			return response()->json([
				'success' => false,
				'message' => 'Please select at least one client to archive.'
			], 400);
		}
		
		$clientIds = array_map('intval', array_filter($clientIds));
		
		$clients = Admin::whereIn('id', $clientIds)
			->where('role', '7')
			->where('is_archived', '0')
			->get();
		
		$archived = 0;
		$updateData = [
			'is_archived' => 1,
			'archived_on' => date('Y-m-d'),
			'archived_by' => Auth::user()->id
		];
		
		foreach ($clients as $client) {
			$updated = DB::table('admins')->where('id', $client->id)->update($updateData);
			if ($updated) {
				$archived++;
				$subject = 'Client has been archived';
				$activity = new ActivitiesLog();
				$activity->client_id = $client->id;
				$activity->created_by = Auth::user()->id;
				$activity->subject = $subject;
				$activity->description = $subject . ' by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name;
				$activity->task_status = 0;
				$activity->pin = 0;
				$activity->save();
			}
		}
		
		$message = $archived === 1
			? '1 client has been archived successfully.'
			: $archived . ' clients have been archived successfully.';
		
		return response()->json([
			'success' => true,
			'message' => $message,
			'archived_count' => $archived
		]);
	}
}
