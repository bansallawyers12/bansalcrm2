<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Report;
use App\Models\Application;
use App\Models\CheckinLog;
use App\Models\Invoice;
// use App\Models\Task; // Task system removed - December 2025
 
use Auth; 
use Config;

class ReportController extends Controller
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
     * All Vendors.
     *
     * @return \Illuminate\Http\Response
     */
	public function client(Request $request)  
	{		
		$query 		= Admin::where('is_archived', '=', '0')->where('role', '=', '7'); 		  
		$totalData 	= $query->count();	//for all data
		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		
		return view('Admin.reports.client', compact(['lists', 'totalData'])); 	
		//return view('Admin.reports.client');
	}
	public function application(Request $request)  
	{		
		$query 		= Application::query(); 		  
		$totalData 	= $query->count();	//for all data
		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		
		return view('Admin.reports.application', compact(['lists', 'totalData'])); 
		//return view('Admin.reports.application');
	}
	public function invoice(Request $request)  
	{	
		$query 		= Invoice::query(); 		  
		$totalData 	= $query->count();	//for all data
		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		
		return view('Admin.reports.invoice', compact(['lists', 'totalData'])); 
		//return view('Admin.reports.invoice');
	}
	public function office_visit(Request $request)  
	{		
		$query 		= CheckinLog::query();  	  
		$totalData 	= $query->count();	//for all data
		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		
		return view('Admin.reports.office-task-report', compact(['lists', 'totalData']));
		// return view('Admin.reports.office-visit', compact(['lists', 'totalData']));
		//return view('Admin.reports.office-visit');
	}
	public function saleforecast_application(Request $request)  
	{	
		$query 		= Application::query(); 		  
		$totalData 	= $query->count();	//for all data
		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		
		return view('Admin.reports.saleforecast-application', compact(['lists', 'totalData']));
		//return view('Admin.reports.sale-forecast');
	}
	// Interested services report removed - applications are created directly
	// Task system removed - December 2025 (inactive for 16+ months)
	// Database tables preserved: tasks, task_logs, to_do_groups
	/*
	public function personal_task(Request $request)  
	{	
		$query 		= Task::query();  	  
		$totalData 	= $query->count();	//for all data
		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		
		//return view('Admin.reports.tasks');
	}
	public function office_task(Request $request)  
	{	
		$query 		= Task::query();  	  
		$totalData 	= $query->count();	//for all data
		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		
		return view('Admin.reports.office-task-report', compact(['lists', 'totalData']));
		//return view('Admin.reports.tasks');
	}
	*/
	
	public function visaexpires(Request $request)  
	{	
		return view('Admin.reports.visaexpires');
	}
	public function actionCalendar(Request $request)  
	{	
		return view('Admin.reports.action_calendar');
	}
	public function agreementexpires(Request $request)  
	{	
		return view('Admin.reports.agreementexpires');
	}
	
	//Daily no of person office visit
    public function noofpersonofficevisit(Request $request)
	{
		//SELECT date, count(id) as personCount FROM `checkin_logs` group by date order by date desc;
         $lists = DB::table('checkin_logs')
        ->join('branches', 'branches.id', '=', 'checkin_logs.office')
        ->select(DB::raw('checkin_logs.date,branches.office_name,count(checkin_logs.id) as person_count'))
        ->groupBy(['checkin_logs.date', 'checkin_logs.office', 'branches.office_name'])
        ->orderByRaw('checkin_logs.date DESC NULLS LAST')
        ->paginate(5);

        if(!empty($lists)){
            $totalData = count($lists);
        } else {
            $totalData = 0;
        }
        //dd($totalData);
		return view('Admin.reports.noofpersonofficevisit',compact(['lists', 'totalData']))
        ->with('i', (request()->input('page', 1) - 1) * 20);
	}
	
}
