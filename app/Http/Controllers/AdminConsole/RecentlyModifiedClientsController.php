<?php
namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\ActivitiesLog;
  
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
		
		// Get the most recent activity for each client
		$subQuery = ActivitiesLog::select('client_id', DB::raw('MAX(created_at) as last_activity'))
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
					 ->where('client_admins.role', '=', '7');
			})
			->leftJoin('admins', 'activities_logs.created_by', '=', 'admins.id');
		
		// Apply date filters to main query if provided
		// This filters clients whose most recent activity falls within the date range
		if ($fromDate) {
			$query->whereDate('activities_logs.created_at', '>=', $fromDate);
		}
		if ($toDate) {
			$query->whereDate('activities_logs.created_at', '<=', $toDate);
		}
		
		// Apply sorting order
		$query->orderBy('activities_logs.created_at', $sortOrder);
		
		$totalData = $query->count();
		
		// Paginate the results - preserve query parameters
		$lists = $query->paginate(config('constants.limit', 20))
			->appends($request->query());
		
		return view('AdminConsole.recent_clients.index', compact(['lists', 'totalData', 'fromDate', 'toDate', 'sortOrder'])); 	
	}
}
