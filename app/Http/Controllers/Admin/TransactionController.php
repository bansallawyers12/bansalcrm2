<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;


use App\TestSeriesTransactionHistory;
use App\ProductTransactionHistory;
use App\PurchasedSubject;
use App\User;

use Auth;

class TransactionController extends Controller
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
     * All Test Series Transactions.
     *
     * @return \Illuminate\Http\Response
     */	
	public function test_series_transactions(Request $request)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('Transaction', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		
		/* $query 				= 	PurchasedSubject::where('id', '!=', '')->with(['subject'=>function($sub){
			$sub->select('id', 'which_course', 'which_test_series_type', 'which_group', 'subject_name', 'price');
			$sub->with(['course', 'test_series_type', 'group']);	
		}]);
		$res				=	$query->get();
		
		foreach($res as $re)
		{
			$subject_data = '';
			
			$subject_data = json_encode(array('course'=>@$re->subject->course->course_name, 'test_series_type'=>@$re->subject->test_series_type->test_series_type, 'group'=>@$re->subject->group->group_name, 'subject_name'=>@$re->subject->subject_name, 'price'=>@$re->subject->price));
			
			DB::table('purchased_subjects')->where('id', '=', $re->id)->update(['subject_data' => @$subject_data]);
		} */	
		
	
		
		
		/* foreach($res as $re)
		{
			if(@$re->professor_name == '')
			{
				DB::table('product_transaction_histories')->where('id', '=', $re->id)->update(['professor_name' => @$re->product->professor->first_name.' '.@$re->product->professor->last_name]);
				//DB::table('product_transaction_histories')->where('id', '=', $re->id)->update(['subject_name' => @$re->product->subject_name]);
			}
		}
		 */
		
		
		$query 				= TestSeriesTransactionHistory::where('id', '!=', '')->where('session_finish', '=', 0);
	
		$totalData 	= $query->count();	//for all data
		
		if ($request->has('search_term_first_name')) 
		{
			$search_term_first_name 		= 	$request->input('search_term_first_name');	
			if(trim($search_term_first_name) != '')
			{
				$query->whereHas('student', function ($q) use($search_term_first_name){
					$q->where('first_name',$search_term_first_name);
				});
			}		
		}
		if ($request->has('search_term_last_name')) 
		{	
			$search_term_last_name 		= 	$request->input('search_term_last_name');	
			if(trim($search_term_last_name) != '')
			{
				$query->whereHas('student', function ($q) use($search_term_last_name){
					$q->where('last_name',$search_term_last_name);
				});
			}		
		}
		if ($request->has('search_term_email')) 
		{
			$search_term_email 		= 	$request->input('search_term_email');	
			if(trim($search_term_email) != '')
			{
				$query->whereHas('student', function ($q) use($search_term_email){
					$q->where('email',$search_term_email);
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
		
		$query->with(['student'=>function($q){
			$q->select('id', 'first_name', 'last_name', 'email', 'phone');
		}]);
		
		if ($request->has('search_term_first_name') || $request->has('search_term_last_name') || $request->has('search_term_email') || $request->has('search_term_from') || $request->has('search_term_to')) 
		{
			$totalData 	= $query->count();
		}
		
		$lists = $query->sortable(['id'=>'desc'])->paginate(config('constants.limit'));
	
		return view('Admin.transaction.test_series_transactions',compact(['lists', 'totalData']));	
	}
	
	public function viewTestSeriesTransaction(Request $request, $id)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('Transaction', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		
		if(isset($id) && !empty($id))
		{
			$id = $this->decodeString($id);
			if(TestSeriesTransactionHistory::where('id', '=', $id)->exists()) 
			{
				$query 		= 	TestSeriesTransactionHistory::where('id', '=', $id);
				
				$fetchedData 		= 	$query->with(['student'=>function($query){
					$query->select('id', 'first_name', 'last_name', 'email', 'phone', 'country', 'state', 'city', 'address', 'zip');
					$query->with(['countryData', 'stateData']);
				}])->first();
				
				return view('Admin.transaction.view_test_series_transaction', compact(['fetchedData']));
			}
			else
			{
				return Redirect::to('/admin/test_series_transactions')->with('error', 'Transaction Id does not exist.');
			}
		}
		else
		{
			return Redirect::to('/admin/test_series_transactions')->with('error', Config::get('constants.unauthorized'));
		}
	}

	public function studentTestSeriesTransactions(Request $request, $id = NULL)
	{	
		//check authorization start	
			$check = $this->checkAuthorizationAction('User', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end

		if(isset($id) && !empty($id))
		{
			$id = $this->decodeString($id);

			if(User::where('id', '=', $id)->exists()) 
			{
				$studentData = User::select('id', 'first_name', 'last_name', 'email')->where('id', '=', $id)->first();
				
				$query 				= TestSeriesTransactionHistory::where('student_id', '=', $id);	
				
				$lists 		= 	$query->with(['student'=>function($query){
					$query->select('id', 'first_name', 'last_name', 'email', 'phone');
				}])->sortable()->paginate(config('constants.limit'));
				
				$totalData 	= TestSeriesTransactionHistory::where('student_id', '=', $id)->count();

				return view('Admin.transaction.student_test_series_transactions',compact(['lists', 'totalData', 'studentData']));	
			}
			else
			{
				return Redirect::to('/admin/students')->with('error', 'Student Id does not exist.');
			}
		}
		else
		{
			return Redirect::to('/admin/students')->with('error', Config::get('constants.unauthorized'));
		}
	}
	
	public function product_transactions(Request $request)
	{	
		//check authorization start	
			$check = $this->checkAuthorizationAction('Transaction', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		
		$query 				= ProductTransactionHistory::where('id', '!=', '');
		
		/* $query 				= 	ProductTransactionHistory::where('id', '!=', '')->with(['product'=>function($sub){
			$sub->select('id', 'professor_id', 'subject_name');
			$sub->with(['professor'=>function($sa){
				$sa->select('id', 'first_name', 'last_name');
			}]);	
		}]);
		$res				=	$query->get();
		
		foreach($res as $re)
		{
			if(@$re->professor_name == '')
			{
				DB::table('product_transaction_histories')->where('id', '=', $re->id)->update(['professor_name' => @$re->product->professor->first_name.' '.@$re->product->professor->last_name]);
				//DB::table('product_transaction_histories')->where('id', '=', $re->id)->update(['subject_name' => @$re->product->subject_name]);
			}
		}*/	
	
		$totalData 	= $query->count();	//for all data
		
		if ($request->has('search_term_first_name')) 
		{
			$search_term_first_name 		= 	$request->input('search_term_first_name');	
			if(trim($search_term_first_name) != '')
			{
				$query->whereHas('student', function ($q) use($search_term_first_name){
					$q->where('first_name',$search_term_first_name);
				});
			}		
		}
		if ($request->has('search_term_last_name')) 
		{	
			$search_term_last_name 		= 	$request->input('search_term_last_name');	
			if(trim($search_term_last_name) != '')
			{
				$query->whereHas('student', function ($q) use($search_term_last_name){
					$q->where('last_name',$search_term_last_name);
				});
			}		
		}
		if ($request->has('search_term_email')) 
		{
			$search_term_email 		= 	$request->input('search_term_email');	
			if(trim($search_term_email) != '')
			{
				$query->whereHas('student', function ($q) use($search_term_email){
					$q->where('email',$search_term_email);
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
		
		if ($request->has('search_term_order')) 
		{
			$search_term_order 		= 	$request->input('search_term_order');	
			if(trim($search_term_order) != '')
			{
				$query->whereHas('order', function ($q) use($search_term_order){
					$q->where('id',$search_term_order);
				});
			}		
		}
		
		$query->with(['student'=>function($q){
			$q->select('id', 'first_name', 'last_name', 'email', 'phone');
		}, 'product'=>function($q1){
			$q1->select('id', 'subject_name', 'professor_id');
			$q1->with(['professor'=>function($q2){
				$q2->select('id', 'first_name', 'last_name');
			}]);
		}]);
		
		if ($request->has('search_term_first_name') || $request->has('search_term_last_name') || $request->has('search_term_email') || $request->has('search_term_from') || $request->has('search_term_to') || $request->has('search_term_order')) 
		{
			$totalData 	= $query->count();
		}
		
		$lists = $query->sortable(['id'=>'desc'])->paginate(config('constants.limit'));
	
		return view('Admin.transaction.product_transactions',compact(['lists', 'totalData']));	
	}
	
	public function viewProductTransaction(Request $request, $id)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('Transaction', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		
		if(isset($id) && !empty($id))
		{
			$id = $this->decodeString($id);

			if(ProductTransactionHistory::where('id', '=', $id)->exists()) 
			{
				$query 		= 	ProductTransactionHistory::where('id', '=', $id);
				
				$fetchedData 		= 	$query->with(['student'=>function($query){
					$query->select('id', 'first_name', 'last_name', 'email', 'phone', 'country', 'state', 'city', 'address', 'zip');
					$query->with(['countryData', 'stateData']);
				}, 'product'=>function($query1){
					$query1->select('id', 'professor_id', 'subject_name');
					$query1->with(['professor'=>function($subQuery){
						$subQuery->select('id', 'first_name', 'last_name');
					}]);
				}, 'mode_product_data'=>function($query2){
					$query2->select('id', 'mode_product');
				}])->first();

				return view('Admin.transaction.view_product_transaction', compact(['fetchedData']));
			}
			else
			{
				return Redirect::to('/admin/product_transactions')->with('error', 'Transaction'.Config::get('constants.not_exist'));
			}
		}
		else
		{
			return Redirect::to('/admin/product_transactions')->with('error', Config::get('constants.unauthorized'));
		}
	}
	
	public function exportTestSeries(Request $request)
	{
		$headers = array(
							"Content-type" => "text/csv",
							"Content-Disposition" => "attachment; filename=Test Series Report.csv",
							"Pragma" => "no-cache",
							"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
							"Expires" => "0"
						);
						
		$query 	 = TestSeriesTransactionHistory::select('id', 'student_id', 'pay_amount', 'status', 'response')->where('id', '!=', '')->where('session_finish', '=', 0);
		$query->with(['student'=>function($q){
			$q->select('id', 'first_name', 'last_name', 'email', 'phone');
		}, 'purchased_subject'=>function($q1){
			$q1	->select('id', 'test_series_transaction_id', 'subject_data');
		}]);

		$reports = $query->get();

		$columns = array('Name', 'Email', 'Phone', 'Payment Status', 'Amount', 'Refund', 'Subject');	
		
		$callback = function() use ($reports, $columns)
			{
				$file = fopen('php://output', 'w');
				fputcsv($file, $columns);

				foreach($reports as $reportData) 
				{
					if(@$reportData->response == 1)
					{
						$paymentStatus = 'Success';
					}
					else
					{
						$paymentStatus = 'Error';
					}

					if(@$reportData->status == 1)
					{
						$refundStatus = 'Yes';
					}
					else
					{
						$refundStatus = 'No';
					}	
					
					$subjects = $reportData->purchased_subject;
					
					$subjectName = array();
					foreach($subjects as $subject)
					{
						$data = json_decode($subject->subject_data);
						array_push($subjectName, $data->subject_name);	
					}	
					
					$stringSubjects = implode(',', $subjectName);
					
					fputcsv($file, array(@$reportData->student->first_name.' '.@$reportData->student->last_name, @$reportData->student->email, @$reportData->student->phone, $paymentStatus, @$reportData->pay_amount, $refundStatus, @$stringSubjects));
				}
				fclose($file);
			};
		return Response::stream($callback, 200, $headers);									
	}
	
	public function closeSession(Request $request)
	{
		//check authorization start	
			$check = $this->checkAuthorizationAction('Transaction', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$changed =  DB::table('test_series_transaction_histories')->update(['session_finish' => 1]);
			
			if(!$changed)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/test_series_transactions')->with('success', 'Test Series Session has been expired, now you can continue with new Test Series session.');
			}				
		}
	}
}
