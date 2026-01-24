<?php
namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Client;
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
		// Get the most recent activity for each client
		$subQuery = ActivitiesLog::select('client_id', DB::raw('MAX(created_at) as last_activity'))
			->groupBy('client_id');
		
		// Join with activities_logs to get the full activity details
		$query = ActivitiesLog::select(
				'activities_logs.id as activity_id',
				'activities_logs.client_id',
				'activities_logs.created_by',
				'activities_logs.subject',
				'activities_logs.description',
				'activities_logs.created_at as activity_date',
				'clients.firstname',
				'clients.lastname',
				'clients.email',
				'clients.phone',
				'admins.first_name as admin_firstname',
				'admins.last_name as admin_lastname'
			)
			->joinSub($subQuery, 'latest_activities', function($join) {
				$join->on('activities_logs.client_id', '=', 'latest_activities.client_id')
					 ->on('activities_logs.created_at', '=', 'latest_activities.last_activity');
			})
			->leftJoin('clients', 'activities_logs.client_id', '=', 'clients.id')
			->leftJoin('admins', 'activities_logs.created_by', '=', 'admins.id')
			->orderBy('activities_logs.created_at', 'desc');
		
		$totalData = $query->count();
		
		// Paginate the results
		$lists = $query->paginate(config('constants.limit', 20));
		
		return view('AdminConsole.recent_clients.index', compact(['lists', 'totalData'])); 	
	}
}
