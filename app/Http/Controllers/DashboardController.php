<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Routing\Route;

use Auth;
use Config;
use Cookie;

use App\Country;
use App\User;
use App\ProductReview;
use App\ProductOrder;
use App\ProductTransactionHistory;
use App\TestSeriesTransactionHistory;
use App\PurchasedSubject;
use App\WebsiteSetting;
use App\SeoPage;
use App\FreeDownload;

use App\Course;
use App\TestSeriesType;
use App\Group;
use App\Subject;
use App\DiscountTestSeries;
use App\CmsPage;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$siteData = WebsiteSetting::where('id', '!=', '')->first();
		\View::share('siteData', $siteData);	
        $this->middleware('auth:web');
	
		//remove all cokies start
			\Cookie::queue(\Cookie::forget('product_id'));
			\Cookie::queue(\Cookie::forget('product_other_info_id'));
			\Cookie::queue(\Cookie::forget('quantity'));
			\Cookie::queue(\Cookie::forget('subject_ids'));
		//remove all cokies end	
	}
	
	protected function guard()
	{
        return Auth::guard('web');
    }
	
	public function index(Request $request) 
    {
		
		//for first time message start
			$forFirstTime = User::select('id', 'first_time_msg')->where('id', '=', @Auth::user()->id)->first();
			$lastFirstMsgStatus = $forFirstTime->first_time_msg;

			if($lastFirstMsgStatus == 0)
			{
				$obj 					= User::find(@$forFirstTime->id);
				$obj->first_time_msg 	= 1;
				$saved					=	$obj->save();	
			}	
		//for first time message end
		
		//Product Order Summary start
			$query = ProductOrder::where('student_id', '=', @Auth::user()->id);		
			$totalData 	        = $query->count();	//for all data
			$lists              = $query->sortable(['id'=>'desc'])->paginate(config('constants.unlimited'));
		//Product Order Summary end

		//Test series summary start	
			$query1 	= TestSeriesTransactionHistory::where('student_id', '=', Auth::user()->id);
			$totalData1	= $query1->count();	//for all data
			$lists1		= $query1->sortable(['id' => 'desc'])->paginate(config('constants.unlimited'));
			
		//Test series summary end	
		
		return view('Frontend.dashboard.index', compact(['lists', 'totalData', 'lists1', 'totalData1', 'lastFirstMsgStatus']) ); 
    }

	public function viewOrderSummary(Request $request, $id)
    {
        if(isset($id) && !empty($id))
        {
            $id = $this->decodeString($id);
			
            if(ProductOrder::where('id', '=', $id)->exists()) 
            {
                $query      =   ProductTransactionHistory::where('order_id', '=', $id);
                
                $fetchedDatas        =   $query->with(['student'=>function($query){
                    $query->select('id', 'first_name', 'last_name', 'email', 'phone');
                }, 'product'=>function($query1){
                    $query1->select('id', 'professor_id', 'subject_name');
                    $query1->with(['professor'=>function($subQuery){
                        $subQuery->select('id', 'first_name', 'last_name');
                    }]);
                }, 'mode_product_data'=>function($query2){
                    $query2->select('id', 'mode_product');
                }])->get();
				
				$orderDetail = ProductOrder::where('id', '=', @$id)->first();
				
                return view('Frontend.dashboard.view_order_summary', compact(['fetchedDatas', 'orderDetail']));
            }
            else
            {
                return Redirect::to('/dashboard')->with('error', 'Order does not exist.');
            }
        }
        else
        {
            return Redirect::to('/dashboard')->with('error', Config::get('constants.unauthorized'));
        }
    }
	
	public function viewTestSeriesOrder(Request $request, $id)
	{
		if(isset($id) && !empty($id))
		{
			$id = $this->decodeString($id);

			if(TestSeriesTransactionHistory::where('id', '=', $id)->exists()) 
			{
				$fetchedData = TestSeriesTransactionHistory::where('id', '=', $id)->with(['purchased_subject'=>function($query){
					$query->select('id', 'test_series_transaction_id', 'subject_id', 'subject_data');
					$query->with(['subject'=>function($subQuery){
						$subQuery->select('id', 'which_course', 'which_test_series_type', 'which_group', 'subject_name');
						$subQuery->with(['course' => function($subSubQuery){
							$subSubQuery->select('id', 'course_name');
						}, 'test_series_type' => function($subSubQuery){
							$subSubQuery->select('id', 'test_series_type');
						}, 'group' => function($subSubQuery){
							$subSubQuery->select('id', 'group_name');
						}]);
					}]);	
				}])->first();	
				
				return view('Frontend.dashboard.view_test_series_order', compact(['fetchedData']));
			}
			else
			{
				return Redirect::to('/dashboard')->with('error', 'Order Id does not exist.');
			}
		}
		else
		{
			return Redirect::to('/dashboard')->with('error', Config::get('constants.unauthorized'));
		}
	}
	
	public function address(Request $request)
	{
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [
										'first_name' => 'required|max:255',
										'last_name' => 'required|max:255',
										'country' => 'required',
										'state' => 'required',
										'city' => 'required|max:255',
										'address' => 'required',
										'zip' => 'required|max:40',
									  ]);
									  
			$obj					= 	User::find($requestData['id']);
			$obj->first_name		=	@$requestData['first_name'];
			$obj->last_name			=	@$requestData['last_name'];
			$obj->country			=	@$requestData['country'];
			$obj->state				=	@$requestData['state'];
			$obj->city				=	@$requestData['city'];
			$obj->address			=	@$requestData['address'];
			$obj->zip				=	@$requestData['zip'];
			
			$saved				=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return redirect()->back()->with('success', 'Address has been updated successfully.');
			}				
		}

		/* Get all Select Data */	
			$countries = Country::select('id', 'name')->where('status', '=', 1)->get();	
			$fetchedData = User::select('id', 'first_name', 'last_name', 'email', 'phone', 'country', 'state', 'city', 'address', 'zip')->where('id', '=', @Auth::user()->id)->with('countryData', 'stateData')->first();
		/* Get all Select Data */	
		
		return view('Frontend.dashboard.address', compact(['countries', 'fetchedData']));
	}

	public function addReview(Request $request)
	{
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
		
			$this->validate($request, [
										'user_id' => 'required',
										'product_id' => 'required',
										'review' => 'required'
									  ]);
									  
			$obj					= 	new ProductReview;
			$obj->user_id			=	@$requestData['user_id'];
			$obj->product_id		=	@$requestData['product_id'];
			$obj->review			=	@$requestData['review'];
			
			$saved				=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return redirect()->back()->with('success', 'Your Review has been send to our Admin, after approval your review would be show into the Review Section.');
			}				
		}
	}
	
	public function changePassword(Request $request)
	{
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'old_password' => 'required|min:6',
										'password' => 'required|confirmed|min:6',
										'password_confirmation' => 'required|min:6'
									  ]);
			
			
			$requestData 	= 	$request->all();
				
			
			$loginId = Auth::user()->id;
			
			$fetchedData = User::where('id', '=', @$loginId)->first();
			if(!empty(@$fetchedData))
				{
					if(@$loginId == trim(@$requestData['user_id']))
						{
							 if (!(Hash::check($request->get('old_password'), Auth::user()->password))) 
								{
									return redirect()->back()->with("error","Your current password does not match with the password you provided. Please try again.");
								}
							else
								{
									$user = User::find(@$requestData['user_id']);
									$user->password = Hash::make(@$requestData['password']);
									if($user->save())
										{
											return redirect('/change_password')->with('success', 'Your Password has been changed successfully.');
										}
									else
										{
											return redirect()->back()->with('error', Config::get('constants.server_error'));
										}
								}	
						}
					else
						{
							return redirect()->back()->with('error', 'You can only change password of your account.');
						}	
				}	
			else
				{
					return redirect()->back()->with('error', 'User does not exist, so you can not change the password.');
				}	
		}
		return view('change_password');		
	}
	
	public function editProfile(Request $request)
	{
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [
										'first_name' => 'required|max:255',
										'last_name' => 'required|max:255',
										'country' => 'required',
										'state' => 'required',
										'city' => 'required|max:255',
										'address' => 'required',
										'zip' => 'required|max:40',
									  ]);	
									 					  
			$obj					= 	User::find($requestData['id']);
			$obj->first_name		=	@$requestData['first_name'];
			$obj->last_name			=	@$requestData['last_name'];
			$obj->country			=	@$requestData['country'];
			$obj->state				=	@$requestData['state'];
			$obj->city				=	@$requestData['city'];
			$obj->address			=	@$requestData['address'];
			$obj->zip				=	@$requestData['zip'];
			
			$saved				=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return redirect()->back()->with('success', 'Your Profile has been updated successfully.');
			}				
		}

		/* Get all Select Data */	
			$countries = Country::select('id', 'name')->where('status', '=', 1)->get();	
			$fetchedData = User::select('id', 'first_name', 'last_name', 'email', 'phone', 'country', 'state', 'city', 'address', 'zip')->where('id', '=', @Auth::user()->id)->with('countryData', 'stateData')->first();
		/* Get all Select Data */	
		
		return view('Frontend.dashboard.edit_profile', compact(['countries', 'fetchedData']));
	}
	
	public function modifiedTestSeries(Request $request)
	{
		/* Get all Select Data */	
			$courses = Course::select('id', 'course_name')->where('status', '=', 1)->get();
			$groups = Group::select('id', 'group_name')->get();
			$discounts = DiscountTestSeries::select('id', 'subject_numbers', 'discount')->get();
			$data =	CmsPage::where('alias', '=', 'test_series')->first();
		/* Get all Select Data */
		
		/*Discount Start */
			$discount = array();
			if($discounts->count() > 0)
			{
				foreach($discounts as $d)
				{
					$discount[$d->subject_numbers] = $d->discount;
				}
			}
		/*Discount End */
		
		/* Purchased Subjects Start */
			$purchaseSubjectArray = array();
			
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
					foreach($purchasedSubjects as $subjects)
					{
						array_push($purchaseSubjectArray, $subjects->subject_id);
					}
				}
			}
		/* Purchased Subjects End */
		
			if ($request->has('search_term')) 
			{
				$courseId 		= 	trim($request->input('search_term'));
				$courseDetails = Course::select('id', 'plans')->where('id', '=', $courseId)->where('status', '=', 1)->first();	
			}
			else
			{
				//if no course selected then first course selected 
					//$courseDetails = Course::select('id', 'plans')->where('status', '=', 1)->first();
					$courseId = '';	
			}	
			
			if($courseId)	
			{		
				$subjects = Subject::select('id', 'which_course', 'which_test_series_type', 'which_group', 'subject_name', 'price')->where('which_course', '=', @$courseId)->where('status', '=', 1)->get();
				
				if($subjects->count() > 0)
				{		
					$testSeriesTypes = array();	
					foreach($subjects as $subject)
					{
						array_push($testSeriesTypes, @$subject->which_test_series_type);
					}
					
					$testSeriesTypes  = array_unique($testSeriesTypes); //all test series types
					
					$fetchedData = array();
					foreach($testSeriesTypes as $testSeriesType)
					{
						$testSeriesTypeDetail =  TestSeriesType::select('id', 'test_series_type')->where('id', '=', @$testSeriesType)->where('status', '=', 1)->first();
						
						if(!empty($testSeriesTypeDetail))
						{
							$mainArray = array();
							$mainArray['id'] = @$testSeriesTypeDetail->id;
							$mainArray['which_test_series_type'] = @$testSeriesTypeDetail->test_series_type;
							
							$mainArray['which_group'] = array();	
							foreach($groups as $group)
							{
								$innerArray = array();
								$innerArray['group_name'] = $group->group_name;
								
								$subjects = Subject::select('id', 'which_course', 'which_test_series_type', 'which_group', 'subject_name', 'price')->where('which_course', '=', $courseId)->where('which_test_series_type', '=', $testSeriesType)->where('which_group', '=', $group->id)->where('status', '=', 1)->get();
								
								if($subjects)
								{
									$innerArray['data'] = array(); 	
									
									foreach($subjects as $key=>$subject)
									{
										$newInnerArray = array();
										$newInnerArray['id'] = @$subject->id;
										$newInnerArray['subject_name'] = @$subject->subject_name;
										$newInnerArray['price'] = @$subject->price;

										array_push($innerArray['data'], $newInnerArray);
									}	
								}	
								array_push($mainArray['which_group'], $innerArray);	
							}

							array_push($fetchedData, $mainArray); 	
						}
					}
				}		
			}
			else
			{
				$fetchedData = array();
			}	

		return view('Frontend.dashboard.modified_test_series', compact(['data', 'courses', 'discount', 'courseDetails', 'fetchedData', 'purchaseSubjectArray']));
	}
	
	public function freeResource(Request $request)
	{
		$seoDetails = SeoPage::where('page_slug', '=', 'free_resource')->first();
	
		$query =	FreeDownload::where('id', '!=', '');
		if ($request->has('search_term')) 
		{
			$search_term 		= 	$request->input('search_term');
			
			if(!empty($search_term))
			{	
				$searchTermArray = explode(',', $search_term);
			
				$query->whereIn('type', @$searchTermArray);
			}
		}
		if ($request->has('search_term_resource')) 
		{
			$search_term_resource 		= 	$request->input('search_term_resource');
			
			if(!empty($search_term_resource))
			{	
				$searchTermArray = explode(',', $search_term_resource);
			
				$query->whereIn('resource', @$searchTermArray);
			}
		}
		$lists		= $query->sortable(['id'=>'asc'])->paginate(config('constants.unlimited'));
		
		return view('free_resource',compact(['lists', 'seoDetails']));
	}
	
	public function logout(Request $request)
    {
		Auth::guard('web')->logout();
        $request->session()->flush();
        $request->session()->regenerate();
		return redirect('/login');
    }
}
