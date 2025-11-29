<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

use App\VendorSubject;
use App\Test;
use App\ScheduledTest;
use App\User;

use Auth;
use Config;

class ReviewTestController extends Controller
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
     * All Linked Subjects.
     *
     * @return \Illuminate\Http\Response
     */
	public function linkedSubjects(Request $request)
	{
		$query 	= VendorSubject::where('vendor_id', '=', Auth::user()->id)->with(['subject'=>function($query){
							$query->select('id', 'which_course', 'which_test_series_type', 'which_group', 'subject_name');
							$query->with(['course' => function($subQuery){
								$subQuery->select('id', 'course_name');
							}, 'test_series_type' => function($subQuery){
								$subQuery->select('id', 'test_series_type');
							}, 'group' => function($subQuery){
								$subQuery->select('id', 'group_name');
							}]);
						}]);
						
		$totalData 	= $query->count();	//for all data			
		
		//searching start	
			if ($request->has('search_term')) 
			{
				$search_term 		= 	$request->input('search_term');
				
				if(trim($search_term) != '')
				{
					$query->whereHas('subject', function ($q) use($search_term){
						$q->where('subject_name', 'LIKE', '%'.$search_term.'%');
					});
				}
			}
			if ($request->has('search_term_from')) 
			{
				$search_term_from 		= 	$request->input('search_term_from');
				if(trim($search_term_from) != '')
				{
					$query->whereDate('created_at', '>=', $search_term_from);
				}
			}
			if ($request->has('search_term_to')) 
			{	
				$search_term_to 		= 	$request->input('search_term_to');
				
				if(trim($search_term_to) != '')
				{
					$query->whereDate('created_at', '<=', $search_term_to);
				}	
			}
			
			if ($request->has('search_term') || $request->has('search_term_from') || $request->has('search_term_to')) 
			{
				$totalData 	= $query->count();//after search
			}
		//searching end		
		
		$lists		= $query->sortable(['id'=>'desc'])->paginate(config('constants.limit'));	
		
		return view('Admin.review_test.linked_subjects',compact(['lists', 'totalData']));	
	}
	/**
     * All Assigned Tests.
     *
     * @return \Illuminate\Http\Response
     */
	public function assignedTests(Request $request, $id = NULL)
	{
		$query 	= Test::where('which_vendor', '=', Auth::user()->id)->with(['subject'=>function($query){
							$query->select('id', 'which_course', 'which_test_series_type', 'which_group', 'subject_name');
							$query->with(['course' => function($subQuery){
								$subQuery->select('id', 'course_name');
							}, 'test_series_type' => function($subQuery){
								$subQuery->select('id', 'test_series_type');
							}, 'group' => function($subQuery){
								$subQuery->select('id', 'group_name');
							}]);
						}]);

		if(isset($id) && !empty($id))
		{
			$id = $this->decodeString($id);	
			$query->where('which_subject', '=', $id);
		}
		
		$totalData 	= $query->count();	//for all data	
		
		//searching start						
			if ($request->has('search_term_test_name')) 
			{
				$search_term_test_name 		= 	$request->input('search_term_test_name');
				if(trim($search_term_test_name) != '')
				{	
					$query->where('test_name', 'LIKE', '%'.$search_term_test_name.'%');
				}
			}
			if ($request->has('search_term_subject_name')) 
			{
				$search_term_subject_name 		= 	$request->input('search_term_subject_name');
				if(trim($search_term_subject_name) != '')
				{	
					$query->whereHas('subject', function ($q) use($search_term_subject_name){
						$q->where('subject_name', 'LIKE', '%'.$search_term_subject_name.'%');
					});
				}
			}
			if ($request->has('search_term_from')) 
			{
				$search_term_from 		= 	$request->input('search_term_from');
				if(trim($search_term_from) != '')
				{
					$query->whereDate('created_at', '>=', $search_term_from);
				}
			}
			if ($request->has('search_term_to')) 
			{	
				$search_term_to 		= 	$request->input('search_term_to');
				
				if(trim($search_term_to) != '')
				{
					$query->whereDate('created_at', '<=', $search_term_to);
				}	
			}
			if ($request->has('search_term_test_name') || $request->has('search_term_subject_name') || $request->has('search_term_from') || $request->has('search_term_to')) 
			{
				$totalData 	= $query->count();//after search
			}
		//searching end
		
		$lists		= $query->sortable(['id'=>'desc'])->paginate(config('constants.limit'));	
		
		return view('Admin.review_test.assigned_tests',compact(['lists', 'totalData']));	
	}
	
	/**
     * All Review Tests.
     *
     * @return \Illuminate\Http\Response
     */
	public function reviewTests(Request $request, $id = NULL)
	{	
		$getAssignTestIds	= 	Test::select('id')->where('which_vendor', '=', Auth::user()->id)->get();
		
		if(count($getAssignTestIds) > 0)
		{
			$arrayOfAssignedTest = $getAssignTestIds->toArray();
			
			$ids = array();
			if(isset($id) && !empty($id))
			{
				$id = $this->decodeString($id);	
				array_push($ids, $id);	
			}
			else	
			{	
				foreach($arrayOfAssignedTest as $value)
				{
					array_push($ids, $value['id']);
				}
			}	
			
			$query	=	ScheduledTest::whereIn('test_id', $ids)->where('test_submitted', '=', 1)->with(['test' => function($query)
							{
								$query->select('id', 'test_name', 'which_subject', 'test_number');
								$query->with(['subject' => function($subQuery){
									$subQuery->select('id', 'which_course', 'which_test_series_type', 'which_group', 'subject_name');
									$subQuery->with(['course' => function($subQuery1){
										$subQuery1->select('id', 'course_name');
									}, 'test_series_type' => function($subQuery1){
										$subQuery1->select('id', 'test_series_type');
									}, 'group' => function($subQuery1){
										$subQuery1->select('id', 'group_name');
									}]);
								}]);
							}, 'student'=>function($query){
								$query->select('id', 'first_name', 'last_name', 'email');
							}]);
				
			$totalData 	= $query->count();	//for all data
				
			//searching start				
				if ($request->has('search_term_first')) 
				{
					$search_term_first 		= 	$request->input('search_term_first');
					if(trim($search_term_first) != '')
					{
						$query->whereHas('student', function ($q) use($search_term_first){
							$q->where('first_name', 'LIKE', '%'.$search_term_first.'%');
						});
					}
				}
				if ($request->has('search_term_last')) 
				{
					$search_term_last 		= 	$request->input('search_term_last');
					if(trim($search_term_last) != '')
					{
						$query->whereHas('student', function ($q) use($search_term_last){
							$q->where('last_name', 'LIKE', '%'.$search_term_last.'%');
						});
					}
				}
				if ($request->has('search_term_email')) 
				{
					$search_term_email 		= 	$request->input('search_term_email');
					if(trim($search_term_email) != '')
					{
						$query->whereHas('student', function ($q) use($search_term_email){
							$q->where('email', 'LIKE', '%'.$search_term_email.'%');
						});
					}
				}
				if ($request->has('search_term_test_name')) 
				{
					$search_term_test_name 		= 	$request->input('search_term_test_name');
					if(trim($search_term_test_name) != '')
					{
						$query->whereHas('test', function ($q) use($search_term_test_name){
							$q->where('test_name', 'LIKE', '%'.$search_term_test_name.'%');
						});
					}
				}
				if ($request->has('search_term_subject_name')) 
				{
					$search_term_subject_name 		= 	$request->input('search_term_subject_name');
					if(trim($search_term_subject_name) != '')
					{
						$query->whereHas('subject', function ($q) use($search_term_subject_name){
							$q->where('subject_name', 'LIKE', '%'.$search_term_subject_name.'%');
						});
					}
				}
				if ($request->has('search_term_from')) 
				{
					$search_term_from 		= 	$request->input('search_term_from');
					if(trim($search_term_from) != '')
					{
						$query->whereDate('scheduled_date', '>=', $search_term_from);
					}
				}
				if ($request->has('search_term_to')) 
				{	
					$search_term_to 		= 	$request->input('search_term_to');
					if(trim($search_term_to) != '')
					{
						$query->whereDate('scheduled_date', '<=', $search_term_to);
					}	
				}
				if ($request->has('search_term_first') || $request->has('search_term_last') || $request->has('search_term_email') || $request->has('search_term_test_name') || $request->has('search_term_subject_name') || $request->has('search_term_from') || $request->has('search_term_to')) 
				{
					$totalData 	= $query->count();//after search
				}	
			//searching end				

			$lists		= $query->sortable(['id'=>'desc'])->paginate(config('constants.limit'));
			
			return view('Admin.review_test.review_tests',compact(['lists', 'totalData']));
		}
		else
		{
			$ids = array();	
			$query	=	ScheduledTest::whereIn('test_id', $ids);
			$lists		= $query->sortable(['id'=>'desc'])->paginate(config('constants.limit'));

			$totalData = 0;
			
			return view('Admin.review_test.review_tests',compact(['lists', 'totalData']));
		}		
	}
	
	public function viewReviewTest(Request $request, $id)
	{
		if(isset($id) && !empty($id))
		{
			$id = $this->decodeString($id);
			
			if(ScheduledTest::where('id', '=', $id)->exists()) 
			{
				$fetchedData 		= 	ScheduledTest::where('id', '=', $id)->with(['test' => function($query)
					{
						$query->select('id', 'test_number', 'test_name', 'which_subject', 'from_date', 'to_date', 'estimated_time', 'marks', 'test_pdf', 'test_suggestion_pdf', 'which_vendor');
						$query->with(['subject' => function($subQuery){
							$subQuery->select('id', 'which_course', 'which_test_series_type', 'which_group', 'subject_name', 'price');
							$subQuery->with(['course' => function($subQuery1){
								$subQuery1->select('id', 'course_name');
							}, 'test_series_type' => function($subQuery1){
								$subQuery1->select('id', 'test_series_type');
							}, 'group' => function($subQuery1){
								$subQuery1->select('id', 'group_name');
							}]);
						}]);
					}, 'student'=>function($query){
						$query->select('id', 'first_name', 'last_name', 'email', 'phone', 'country', 'state', 'city', 'address', 'zip');
					}])->first();

				return view('Admin.review_test.view_review_test', compact(['fetchedData']));
			}
			else
			{
				return Redirect::to('/admin/review_tests')->with('error', 'Test does not exist.');
			}
		}
		else
		{
			return Redirect::to('/admin/review_tests')->with('error', Config::get('constants.unauthorized'));
		}
	}
	
	public function submitReview(Request $request)
	{
		if ($request->isMethod('post')) 
		{
			$requestData 					= 	$request->all();
			
			$this->validate($request, [
										'marks' => 'required|numeric',
										'test_reviewed_copy' => 'required'
									  ]);
			if(@$requestData['marks'] > @$requestData['test_main_marks'])
			{
				return redirect()->back()->with('error', 'Given Marks should be less than Test Total Marks.');
			}		
			
			/* Test PDF Upload Function Start */						  
				if($request->hasfile('test_reviewed_copy')) 
				{
					$test_reviewed_copy = $this->uploadFile($request->file('test_reviewed_copy'), Config::get('constants.test_reviewed_copies'));
				}
			/* Test PDF Upload Function End */			

			$obj							= 	ScheduledTest::find($requestData['id']);
			$obj->test_reviewed				=	1;
			$obj->test_reviewed_copy		=	$test_reviewed_copy;
			$obj->reviewed_date				=	date('Y-m-d H:i:s');
			$obj->marks						=	@$requestData['marks'];
			$obj->additional_remarks		=	@$requestData['additional_remarks'];
			
			$saved							=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				//test info start
					$scheduledTestDetail = ScheduledTest::select('id', 'student_id', 'test_id')->where('id', '=', @$requestData['id'])->first(); 
				
					if(!empty($scheduledTestDetail))
					{
						$studentId = @$scheduledTestDetail->student_id;
						$testId = @$scheduledTestDetail->test_id;
							
						$userDetail = User::select('id', 'first_name', 'last_name', 'email')->where('id', '=', @$studentId)->first();
						$testDetail = Test::select('id', 'test_number', 'test_name', 'which_subject', 'from_date', 'to_date', 'estimated_time', 'marks')->where('id', '=', @$testId)->with(['subject'=>function($subQuery){
						$subQuery->select('id', 'which_course', 'which_test_series_type', 'which_group', 'subject_name');
						$subQuery->with(['course' => function($subSubQuery){
								$subSubQuery->select('id', 'course_name');
							}, 'test_series_type' => function($subSubQuery){
								$subSubQuery->select('id', 'test_series_type');
							}, 'group' => function($subSubQuery){
								$subSubQuery->select('id', 'group_name');
							}]);	
						}])->first();
						
						$testInfo = '';
						$testInfo .= '<tr style="border:1px solid #011224;">';
						$testInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$testDetail->subject->course->course_name.'</td>';
						$testInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$testDetail->subject->test_series_type->test_series_type.'</td>';
						$testInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$testDetail->subject->group->group_name.'</td>';
						$testInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$testDetail->subject->subject_name.'</td>';
						$testInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$testDetail->test_number.'</td>';
						$testInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$testDetail->test_name.'</td>';
						
						$fromDate =  strtotime(@$testDetail->from_date);	
						$fromDate =  date('d M, Y', $fromDate); 

						$toDate =  strtotime(@$testDetail->to_date);	
						$toDate =  date('d M, Y', $toDate); 

						$testInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$fromDate.'</td>';
						$testInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$toDate.'</td>';
						$testInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$testDetail->estimated_time.'</td>';
						$testInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$testDetail->marks.'</td>';
						$testInfo .= '</tr>';
					}
				//test info end
					
				//email goes to student start
					$replace = array('{logo}', '{first_name}', '{last_name}', '{year}', '{testInfo}');					
					$replace_with = array(\URL::to('/').Config::get('constants.logoImg'), @$userDetail->first_name, @$userDetail->last_name, date('Y'), $testInfo);
			
					$this->send_email_template($replace, $replace_with, 'review_info_student', @$userDetail->email);
				//email goes to student end
				
				return Redirect::to('/admin/view_review_test/'.$this->encodeString($requestData['id']))->with('success', 'This Test has been reviewed successfully.');
			}
		}	
	}
	
	public function deleteStudentCopy(Request $request)
	{
		if ($request->isMethod('post')) 
		{
			$requestData 					= 	$request->all();
			
			$this->validate($request, [
										'id' => 'required'
									  ]);
									  
			if(ScheduledTest::where('id', '=', @$requestData['id'])->exists()) 
			{	
				$fetchedData = ScheduledTest::where('id', '=', @$requestData['id'])->first();
				
				/* Unlink Student File Function Start */ 
					if(@$fetchedData->test_submitted_copy != '')
						{
							$this->unlinkFile(@$fetchedData->test_submitted_copy, Config::get('constants.test_submitted_copies'));
						}
				/* Unlink Student File Function End */
				
				/* Unlink Vendor File Function Start */ 
					if(@$fetchedData->test_reviewed_copy != '')
						{
							$this->unlinkFile(@$fetchedData->test_reviewed_copy, Config::get('constants.test_reviewed_copies'));
						}
				/* Unlink Vendor File Function End */
				
				$obj							= 	ScheduledTest::find(@$requestData['id']);
				$obj->test_submitted			=	0;
				$obj->test_submitted_copy		=	NULL;
				$obj->submit_date				=	NULL;
				$obj->test_reviewed				=	0;
				$obj->test_reviewed_copy		=	NULL;
				$obj->reviewed_date				=	NULL;
				$obj->marks						=	NULL;
				$obj->additional_remarks		=	NULL;
				
				$saved							=	$obj->save();
				
				if(!$saved)
				{
					return redirect()->back()->with('error', Config::get('constants.server_error'));
				}
				else
				{
					return Redirect::to('/admin/view_review_test/'.$this->encodeString($requestData['id']))->with('success', 'Student Copy has been deleted successfully. Now Student will submit copy.');
				}
			}
			else
			{
				return Redirect::to('/admin/review_tests')->with('error', 'Schedule Test does not exist.');
			}		
		}	
	}
	
	public function deleteReviewCopy(Request $request)
	{
		if ($request->isMethod('post')) 
		{
			$requestData 					= 	$request->all();
			
			$this->validate($request, [
										'id' => 'required'
									  ]);
									  
			if(ScheduledTest::where('id', '=', @$requestData['id'])->exists()) 
			{	
				$fetchedData = ScheduledTest::where('id', '=', @$requestData['id'])->first();
				
				/* Unlink Vendor File Function Start */ 
					if(@$fetchedData->test_reviewed_copy != '')
						{
							$this->unlinkFile(@$fetchedData->test_reviewed_copy, Config::get('constants.test_reviewed_copies'));
						}
				/* Unlink Vendor File Function End */
				
				$obj							= 	ScheduledTest::find(@$requestData['id']);
				$obj->test_reviewed				=	0;
				$obj->test_reviewed_copy		=	NULL;
				$obj->reviewed_date				=	NULL;
				$obj->marks						=	NULL;
				$obj->additional_remarks		=	NULL;
				
				$saved							=	$obj->save();
				
				if(!$saved)
				{
					return redirect()->back()->with('error', Config::get('constants.server_error'));
				}
				else
				{
					return Redirect::to('/admin/view_review_test/'.$this->encodeString($requestData['id']))->with('success', 'Review Copy has been deleted successfully. Now you can review again.');
				}
			}
			else
			{
				return Redirect::to('/admin/review_tests')->with('error', 'Schedule Test does not exist.');
			}		
		}	
	}
}
