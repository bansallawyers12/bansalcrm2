<?php
namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Models\Document;
  
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
			'sortColumn'
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
}
