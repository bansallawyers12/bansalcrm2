<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\TestSeriesTransactionHistory;
use App\PurchasedSubject;
use App\Test;
use App\ScheduledTest;
use App\WebsiteSetting;
use App\Imports\ImportPartner;
use Auth;
use Config;
use Cookie;

class TestController extends Controller
{
	public function __construct()
    {
		$siteData = WebsiteSetting::where('id', '!=', '')->first();
		\View::share('siteData', $siteData);	
       // $this->middleware('auth:web');
	}
	
		public function import(Request $request){
	     $path = resource_path() . "/partner.xlsx"; 
        \Excel::import(new ImportPartner, $path);
	}
	
	public function index(Request $request)
	{
		$testSeriesTrans = TestSeriesTransactionHistory::select('id')->where('student_id', '=', Auth::user()->id)->where('status', '=', 0)->where('response', '=', 1)->where('session_finish', '=', 0)->get(); //not refund, payment success, session running
		
		if(@$testSeriesTrans->count() > 0)
		{
			$testSeriesTransIds = array();
				
			foreach($testSeriesTrans as $mainId)
			{
				array_push($testSeriesTransIds, $mainId->id);
			}
			
			$purchasedSubjects = PurchasedSubject::whereIn('test_series_transaction_id', @$testSeriesTransIds)->where('status', '=', 1)->get();

			if($purchasedSubjects->count() > 0)
			{
				$subjectArray = array();
				
				foreach($purchasedSubjects as $subjects)
				{
					array_push($subjectArray, $subjects->subject_id);
				}
				
				$query = Test::select('id', 'test_number', 'test_name', 'which_subject', 'from_date', 'to_date', 'estimated_time', 'marks')->whereIn('which_subject', $subjectArray)->where('status', '=', 1)->with(['subject'=>function($subQuery){
					$subQuery->select('id', 'which_course', 'which_test_series_type', 'which_group', 'subject_name');
					$subQuery->with(['course' => function($subSubQuery){
							$subSubQuery->select('id', 'course_name');
						}, 'test_series_type' => function($subSubQuery){
							$subSubQuery->select('id', 'test_series_type');
						}, 'group' => function($subSubQuery){
							$subSubQuery->select('id', 'group_name');
						}]);	
				}]);
				
				$totalData 	= $query->count();	//for all data
				
				$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
				return view('Frontend.tests.index',compact(['lists', 'totalData']));	
			}
			else
			{
				$totalData 	= 0 ;
				
				return view('Frontend.tests.index',compact(['lists', 'totalData']));
			}
		}
		else
		{
			$totalData 	= 0 ;
			
			return view('Frontend.tests.index',compact(['lists', 'totalData']));
		}			
	}
	
	public function viewTest(Request $request, $id)
	{
		if(isset($id) && !empty($id))
        {
			$id = $this->decodeString($id);

            if(Test::where('id', '=', $id)->where('status', '=', 1)->exists()) 
            {
				//Test Data start	
					$query      =   Test::where('id', '=', $id);
					
					$fetchedDataTest   =   $query->with(['subject'=>function($subQuery){
						$subQuery->select('id', 'which_course', 'which_test_series_type', 'which_group', 'subject_name');
						$subQuery->with(['course' => function($subSubQuery){
								$subSubQuery->select('id', 'course_name');
							}, 'test_series_type' => function($subSubQuery){
								$subSubQuery->select('id', 'test_series_type');
							}, 'group' => function($subSubQuery){
								$subSubQuery->select('id', 'group_name');
							}]);	
					}])->first();
				//Test Data End

				//Schedule Test Start
					$scheduleData = ScheduledTest::where('student_id', '=', Auth::user()->id)->where('test_id', '=', $id)->first();
				//Schedule Test End		
					
                return view('Frontend.tests.view_test', compact(['fetchedDataTest', 'scheduleData']));
            }
            else
            {
                return Redirect::to('/test')->with('error', 'Test does not exist.');
            }
        }
        else
        {
            return Redirect::to('/test')->with('error', Config::get('constants.unauthorized'));
        }
	}
	
	public function scheduleTestRequest(Request $request)
	{
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'test_id' => 'required',
										'scheduled_date' => 'required|date'
									  ]);
	
			$student_id = Auth::user()->id;
	
			$requestData 		= 	$request->all();
			
			$queryTestData =  Test::where('id', '=', @$requestData['test_id']);	
			$fetchedDataTest   =   $queryTestData->with(['subject'=>function($subQuery){
				$subQuery->select('id', 'which_course', 'which_test_series_type', 'which_group', 'subject_name');
				$subQuery->with(['course' => function($subSubQuery){
						$subSubQuery->select('id', 'course_name');
					}, 'test_series_type' => function($subSubQuery){
						$subSubQuery->select('id', 'test_series_type');
					}, 'group' => function($subSubQuery){
						$subSubQuery->select('id', 'group_name');
					}]);	
			}])->first();
			
			if(@$fetchedDataTest)
			{
				$getPurchasedSubjects = PurchasedSubject::where('student_id', '=', $student_id)->where('subject_id', '=', @$fetchedDataTest->which_subject)->where('status', '=', 1)->count();
				
				if($getPurchasedSubjects > 0)
				{
					$obj						= 	new ScheduledTest;
					$obj->student_id			=	@$student_id;
					$obj->test_id				=	@$requestData['test_id'];
					$obj->scheduled_date		=	@$requestData['scheduled_date'];
					$obj->test_data				=	json_encode(array(@$fetchedDataTest));
					
					$saved				=	$obj->save();
					
					if(!$saved)
					{
						return redirect()->back()->with('error', Config::get('constants.server_error'));
					}
					else
					{
						return Redirect::to('/view_test/'.base64_encode(convert_uuencode(@$requestData['test_id'])))->with('success', 'Your test has been schedule successfully, so please wait till given date.');
					}
				}
				else
				{
					return Redirect::to('/test')->with('error', 'You have not purchased subject for this Test, so you can not schedule your Test.');
				}		
			}
			else
			{
				return Redirect::to('/test')->with('error', 'Test does not exist.');
			}		
		}
	}
	
	public function uploadAnswer(Request $request)
	{
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'id' => 'required',
										'test_submitted_copy' => 'required'
									  ]);
	
			$student_id = Auth::user()->id;
	
			$requestData 		= 	$request->all();
			
			if(ScheduledTest::where('id', '=', @$requestData['id'])->where('student_id', '=', $student_id)->exists()) 
            {
				$scheduleData = ScheduledTest::select('id', 'test_id')->where('id', '=', @$requestData['id'])->where('student_id', '=', $student_id)->first();
				
				/* Test PDF Upload Function Start */						  
					if($request->hasfile('test_submitted_copy')) 
					{
						$test_submitted_copy = $this->uploadFile($request->file('test_submitted_copy'), Config::get('constants.test_submitted_copies'));
					}
					else
					{
						$test_submitted_copy = NULL;
					}		
				/* Test PDF Upload Function End */
				
					$obj							= 	ScheduledTest::find(@$requestData['id']);
					$obj->test_submitted			=	1;
					$obj->test_submitted_copy		=	@$test_submitted_copy;
					$obj->submit_date				=	date('Y-m-d H:i:s');
					
					$saved				=	$obj->save();
					
					if(!$saved)
					{
						return redirect()->back()->with('error', Config::get('constants.server_error'));
					}
					else
					{
						return Redirect::to('/view_test/'.base64_encode(convert_uuencode(@$scheduleData->test_id)))->with('success', 'Your test has been submitted successfully. Our Experts will review your test paper and let you know our feedbacks on the same. Also please find our suggested answers below.');
					}
			}	
			else
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}		
		}
	}
}