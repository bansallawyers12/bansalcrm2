<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Admin;
use App\Report;
use App\Application;
use App\CheckinLog;
use App\Invoice;
use App\Task;
 
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
		$query 		= Application::where('id', '!=', ''); 		  
		$totalData 	= $query->count();	//for all data
		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		
		return view('Admin.reports.application', compact(['lists', 'totalData'])); 
		//return view('Admin.reports.application');
	}
	public function invoice(Request $request)  
	{	
		$query 		= Invoice::where('id', '!=', ''); 		  
		$totalData 	= $query->count();	//for all data
		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		
		return view('Admin.reports.invoice', compact(['lists', 'totalData'])); 
		//return view('Admin.reports.invoice');
	}
	public function office_visit(Request $request)  
	{		
		$query 		= CheckinLog::where('id', '!=', '');  	  
		$totalData 	= $query->count();	//for all data
		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		
		return view('Admin.reports.office-task-report', compact(['lists', 'totalData']));
		// return view('Admin.reports.office-visit', compact(['lists', 'totalData']));
		//return view('Admin.reports.office-visit');
	}
	public function saleforecast_application(Request $request)  
	{	
		$query 		= Application::where('id', '!=', ''); 		  
		$totalData 	= $query->count();	//for all data
		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		
		return view('Admin.reports.saleforecast-application', compact(['lists', 'totalData']));
		//return view('Admin.reports.sale-forecast');
	}
	public function interested_service(Request $request)  
	{		
		$query 		= Application::where('id', '!=', ''); 		  
		$totalData 	= $query->count();	//for all data
		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		
		return view('Admin.reports.interested-service', compact(['lists', 'totalData']));
		//return view('Admin.reports.sale-forecast');
	}
	public function personal_task(Request $request)  
	{	
		$query 		= Task::where('id', '!=', '');  	  
		$totalData 	= $query->count();	//for all data
		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		
		return view('Admin.reports.personal-task-report', compact(['lists', 'totalData']));
		//return view('Admin.reports.tasks');
	}
	public function office_task(Request $request)  
	{	
		$query 		= Task::where('id', '!=', '');  	  
		$totalData 	= $query->count();	//for all data
		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		
		return view('Admin.reports.office-task-report', compact(['lists', 'totalData']));
		//return view('Admin.reports.tasks');
	}
	
	public function visaexpires(Request $request)  
	{	
		return view('Admin.reports.visaexpires');
	}
	public function followupdates(Request $request)  
	{	
		return view('Admin.reports.followup');
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
        ->select(DB::raw('checkin_logs.date,branches.office_name,count(checkin_logs.id) as personCount'))
        ->groupBy(['checkin_logs.date', 'checkin_logs.office'])
        ->orderBy('checkin_logs.date','DESC')
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
	
	//Client Select Monthly Report
    public function clientrandomlyselectmonthly(Request $request)
	{
        $lists = DB::table('client_monthly_rewards')
        ->select('reward_date','first_name','last_name','phone','client_id')
        ->orderBy('reward_date','DESC')
        ->paginate(20);

        if(!empty($lists)){
            $totalData = count($lists);
        } else {
            $totalData = 0;
        }
        //dd($lists);
		return view('Admin.reports.clientrandomlyselectmonthly',compact(['lists', 'totalData']))
        ->with('i', (request()->input('page', 1) - 1) * 20);
	}
  
  
    //Save Client Select Monthly Report
    public function saveclientrandomlyselectmonthly(Request $request)
	{
        $query = DB::table('admins')->select('id', 'role', 'first_name','last_name','email','phone')->where('role', 7)->inRandomOrder()->limit(1)->get();
        //dd($query);
        if(count($query) >0){
            $current_date = date('Y-m-d');
            $current_month = date('m');

            $current_month_record_cnt = DB::table('client_monthly_rewards')->select('id')->whereMonth('reward_date', $current_month)->count();
            //dd($current_month_record_cnt);
            if($current_month_record_cnt >0){
                $current_month_record_id = DB::table('client_monthly_rewards')->select('id')->whereMonth('reward_date', $current_month)->first();
                //dd($current_month_record_id->id);
                if( $current_month_record_id->id != ""){
                    DB::table('client_monthly_rewards')->where('id', $current_month_record_id->id)->delete();
                }
            }
            $ins = DB::table('client_monthly_rewards')->insert(
                [
                    'reward_date' => $current_date,
                    'client_id' => $query[0]->id,
                    'role' => $query[0]->role,
                    'first_name' => $query[0]->first_name,
                    'last_name' => $query[0]->last_name,
                    'email' => $query[0]->email,
                    'phone' => $query[0]->phone,
                ]
            );
            if($ins){
                $response['status'] 	= 	true;
				$response['message']	=	'Successfully inserted';
            } else {
                $response['status'] 	= 	false;
				$response['message']	=	'Please try again';
            }
        }
		echo json_encode($response);
    }
	
}
