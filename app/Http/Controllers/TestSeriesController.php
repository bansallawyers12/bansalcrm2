<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use Tzsk\Payu\Facade\Payment;

use App\State;
use App\Country;
use App\CmsPage;
use App\Course;
use App\TestSeriesType;
use App\Group;
use App\Subject;
use App\TestSeriesTransactionHistory;
use App\PurchasedSubject;
use App\VendorSubject;
use App\DiscountTestSeries;
use App\WebsiteSetting;
use App\SeoPage;

use Auth;
use Config;
use Cookie;

class TestSeriesController extends Controller
{
	public function __construct(Request $request)
    {
		//remove all cokies start
			\Cookie::queue(\Cookie::forget('product_id'));
			\Cookie::queue(\Cookie::forget('product_other_info_id'));
			\Cookie::queue(\Cookie::forget('quantity'));
		//remove all cokies end	
		
		$siteData = WebsiteSetting::where('id', '!=', '')->first();
		\View::share('siteData', $siteData);
		
		if ($request->isMethod('post')) 
		{	
			$requestData 		= 	@$request->all();
			if(trim(@$requestData['user_id']) == '')
			{
				\Cookie::queue(\Cookie::make('subject_ids', @$requestData['subject_ids'], 3600));
				$this->middleware('auth:web');
			}	
		}		
	}

	/**
     * All Test Series Panel.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		//remove all cokies start
			\Cookie::queue(\Cookie::forget('subject_ids'));
		//remove all cokies end

		//check student login & take already test series panel start
			/* $studentId = @Auth::user()->id;
			if(!empty($studentId))
			{
				$count = TestSeriesTransactionHistory::where('student_id', '=', $studentId)->where('response', '=', '1')->where('status', '=', 0)->count();
	
				if($count > 0)
				{
					return Redirect::to('/modified_test_series');
				}	
			} */	
		//check student login & take already test series panel end

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

		$seoDetails = SeoPage::where('page_slug', '=', 'test_series')->first();	
				
		return view('Frontend.test_series.index', compact(['data', 'courses', 'discount', 'courseDetails', 'fetchedData', 'seoDetails']));
	}
	
	public function testSeriesCheckout(Request $request)
	{
		//All Discounts	
			$discounts = DiscountTestSeries::select('id', 'subject_numbers', 'discount')->get();
		
		if ($request->isMethod('post')) 
		{
			//remove all cokies start
				\Cookie::queue(\Cookie::forget('subject_ids'));
			//remove all cokies end
			
			$requestData 		= 	@$request->all();
			
			$subject_ids = @$requestData['subject_ids'];
			
			if(isset($subject_ids) && !empty(trim($subject_ids)))
			{
				$subject_ids = trim($subject_ids);
		
				$explodeSubjectIds = explode(",", $subject_ids);
				
				$newSubjectIds = array();	
				foreach($explodeSubjectIds as $subjectId)
				{
					if(Subject::where('id', '=', $subjectId)->exists())
					{
						array_push($newSubjectIds, $subjectId);
					}	
				}
				
				if(!empty($newSubjectIds))
				{
					$obj = new TestSeriesTransactionHistory;
					$obj->student_id 	= @Auth::user()->id;
					
					$saved				=	$obj->save();
					if(!$saved)
						{ //error
							return Redirect::to('/test-series')->with('error', Config::get('constants.server_error'));
						}	
						else
						{
							$lastId  = $obj->id; //main id

							$realTotal = 0;
							$subjectName = '';
							
							foreach($newSubjectIds as $subject_id)
							{
								$subjectData = Subject::select('id', 'which_course', 'which_test_series_type', 'which_group', 'subject_name', 'price')->where('id', '=', $subject_id)->with(['course', 'test_series_type', 'group'])->first();
								
								$subject_data = '';
								$subject_data = json_encode(array('course'=>@$subjectData->course->course_name, 'test_series_type'=>@$subjectData->test_series_type->test_series_type, 'group'=>@$subjectData->group->group_name, 'subject_name'=>@$subjectData->subject_name, 'price'=>@$subjectData->price));
								
								$realTotal = $realTotal + $subjectData->price;
								
								if($subjectName == '')
								{	
									$subjectName .= @$subjectData->subject_name;
								}
								else
								{
									$subjectName .= ', '.@$subjectData->subject_name;
								}
								
								$objPurchaseSubject = new PurchasedSubject;
								$objPurchaseSubject->student_id 					= @Auth::user()->id;
								$objPurchaseSubject->subject_id 					= @$subject_id;
								$objPurchaseSubject->subject_data 					= @$subject_data;
								$objPurchaseSubject->test_series_transaction_id 	= @$lastId;
							
								$objPurchaseSubject->save();
							}

							//discount start
								$subjectCount = count($newSubjectIds);
								
								if($discounts->count() > 0)
								{	
									$discount = array();
									$discountKey = array();	
									if($discounts->count() > 0)
									{
										foreach($discounts as $d)
										{
											$discount[$d->subject_numbers] = $d->discount;
											array_push($discountKey, $d->subject_numbers);
										}
									}

									sort($discountKey);
									
									$newDiscountKey = array();
									foreach($discountKey as $ke)
									{
										if($subjectCount >= $ke)
											{
												array_push($newDiscountKey, $ke);	
											}
									}
									
									if (count($newDiscountKey) == 0) 
										{
											$discount_val = 0;
										} 
									else 
										{
											$discountMainKay = $newDiscountKey[count($newDiscountKey) - 1];
											$discount_val = $discount[$discountMainKay];
										}
								}
								else
								{
									$discount_val = 0;
								}		
							//discount end
							
							//After Discount Total start	
								$grandTotal = $realTotal - $discount_val;
							//After Discount Total end
							
							//Save into test transaction history table start 
								$txnid  = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
								
								$updateObj					=	TestSeriesTransactionHistory::find(@$lastId);
								$updateObj->transaction_id	=	$txnid;
								$updateObj->total_amount 	=	@$realTotal;
								$updateObj->discount		=	@$discount_val;
								
								$updateObj->save();
							//Save into test transaction history table end 
							
							$stateData = State::select('id', 'name')->where('id', '=', @Auth::user()->state)->first();
							$countryData = Country::select('id', 'name')->where('id', '=', @Auth::user()->country)->first();

							$attributes = [
											'txnid' => $txnid,
											'amount' => $grandTotal,
											'productinfo' => $subjectName,
											'firstname' => @Auth::user()->first_name,
											'lastname' => @Auth::user()->last_name,
											'email' => @Auth::user()->email,
											'phone' => @Auth::user()->phone,
											'address1' => @Auth::user()->address,
											'city' => @Auth::user()->city,
											'state' => @$stateData->name,
											'country' => @$countryData->name,
											'zipcode' => @Auth::user()->zip,
											'udf1'=>$lastId,
											'udf2'=>'test_series'	
										];
														
							return Payment::make($attributes, function ($then) {
								$then->redirectRoute('test_series.status');
							});
						}
				}		
				else
				{
					return Redirect::to('/test-series')->with('error', 'Please select atleast one subject to proceed further.');
				}
			}
			else
			{
				return Redirect::to('/test-series')->with('error', 'Please select atleast one subject to proceed further.');
			}	
		}
		else
		{
			$subject_ids = @Cookie::get('subject_ids');
			if(isset($subject_ids) && !empty($subject_ids))
			{
				$explodeSubjectIds = explode(",", $subject_ids);	
				
				$newSubjectIds = array();	
				foreach($explodeSubjectIds as $subjectId)
				{
					if(Subject::where('id', '=', $subjectId)->exists())
					{
						array_push($newSubjectIds, $subjectId);
					}	
				}
				
				if(!empty($newSubjectIds))
				{
					$obj = new TestSeriesTransactionHistory;
					$obj->student_id 	= @Auth::user()->id;
					
					$saved				=	$obj->save();
					if(!$saved)
						{ //error
							return Redirect::to('/test-series')->with('error', Config::get('constants.server_error'));
						}	
						else
						{
							$lastId  = $obj->id; //main id
							
							$realTotal = 0;
							$subjectName = '';
							
							foreach($newSubjectIds as $subject_id)
							{
								$subjectData = Subject::select('id', 'which_course', 'which_test_series_type', 'which_group', 'subject_name', 'price')->where('id', '=', $subject_id)->with(['course', 'test_series_type', 'group'])->first();
								
								$subject_data = '';
								$subject_data = json_encode(array('course'=>@$subjectData->course->course_name, 'test_series_type'=>@$subjectData->test_series_type->test_series_type, 'group'=>@$subjectData->group->group_name, 'subject_name'=>@$subjectData->subject_name, 'price'=>@$subjectData->price));
								
								$realTotal = $realTotal + $subjectData->price;
								
								if($subjectName == '')
								{	
									$subjectName .= @$subjectData->subject_name;
								}
								else
								{
									$subjectName .= ', '.@$subjectData->subject_name;
								}
								
								$objPurchaseSubject = new PurchasedSubject;
								$objPurchaseSubject->student_id 					= @Auth::user()->id;
								$objPurchaseSubject->subject_id 					= @$subject_id;
								$objPurchaseSubject->subject_data 					= @$subject_data;
								$objPurchaseSubject->test_series_transaction_id 	= @$lastId;
							
								$objPurchaseSubject->save();
							}

							//discount start
								$subjectCount = count($newSubjectIds);
								
								if($discounts->count() > 0)
								{	
									$discount = array();
									$discountKey = array();	
									if($discounts->count() > 0)
									{
										foreach($discounts as $d)
										{
											$discount[$d->subject_numbers] = $d->discount;
											array_push($discountKey, $d->subject_numbers);
										}
									}

									sort($discountKey);
									
									$newDiscountKey = array();
									foreach($discountKey as $ke)
									{
										if($subjectCount >= $ke)
											{
												array_push($newDiscountKey, $ke);	
											}
									}
									
									if (count($newDiscountKey) == 0) 
										{
											$discount_val = 0;
										} 
									else 
										{
											$discountMainKay = $newDiscountKey[count($newDiscountKey) - 1];
											$discount_val = $discount[$discountMainKay];
										}
								}
								else
								{
									$discount_val = 0;
								}		
							//discount end
							
							//After Discount Total start	
								$grandTotal = $realTotal - $discount_val;
							//After Discount Total end
							
							//Save into test transaction history table start 
								$txnid  = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
								
								$updateObj					=	TestSeriesTransactionHistory::find(@$lastId);
								$updateObj->transaction_id 	=	$txnid;
								$updateObj->total_amount	=	@$realTotal;
								$updateObj->discount		=	@$discount_val;
								
								$updateObj->save();
							//Save into test transaction history table end
							
							
							$stateData = State::select('id', 'name')->where('id', '=', @Auth::user()->state)->first();
							$countryData = Country::select('id', 'name')->where('id', '=', @Auth::user()->country)->first();
							
							$attributes = [
											'txnid' => $txnid,
											'amount' => $grandTotal,
											'productinfo' => $subjectName,
											'firstname' => @Auth::user()->first_name,
											'lastname' => @Auth::user()->last_name,
											'email' => @Auth::user()->email,
											'phone' => @Auth::user()->phone,
											'address1' => @Auth::user()->address,
											'city' => @Auth::user()->city,
											'state' => @$stateData->name,
											'country' => @$countryData->name,
											'zipcode' => @Auth::user()->zip,
											'udf1'=>$lastId,
											'udf2'=>'test_series'	
										];
														
							return Payment::make($attributes, function ($then) {
								$then->redirectRoute('test_series.status');
							});
						}
				}		
				else
				{
					return Redirect::to('/test-series')->with('error', 'Please select atleast one subject to proceed further.');
				}
			}
			else
			{
				return Redirect::to('/test-series')->with('error', 'Please select atleast one subject to proceed further.');
			}	
		}		
	}
	
	public function modifiedTestSeriesCheckout(Request $request)
	{
		//All Discounts	
			$discounts = DiscountTestSeries::select('id', 'subject_numbers', 'discount')->get();
		
		if ($request->isMethod('post')) 
		{
			//remove all cokies start
				\Cookie::queue(\Cookie::forget('subject_ids'));
			//remove all cokies end
			
			$requestData 		= 	@$request->all();
			
			$subject_ids = @$requestData['subject_ids'];
			
			if(isset($subject_ids) && !empty(trim($subject_ids)))
			{
				$subject_ids = trim($subject_ids);
		
				$explodeSubjectIds = explode(",", $subject_ids);
				
				$newSubjectIds = array();	
				foreach($explodeSubjectIds as $subjectId)
				{
					if(Subject::where('id', '=', $subjectId)->exists())
					{
						array_push($newSubjectIds, $subjectId);
					}	
				}
				
				if(!empty($newSubjectIds))
				{
					$obj = new TestSeriesTransactionHistory;
					$obj->student_id 	= @Auth::user()->id;
					
					$saved				=	$obj->save();
					if(!$saved)
						{ //error
							return Redirect::to('/modified_test_series')->with('error', Config::get('constants.server_error'));
						}	
						else
						{
							$lastId  = $obj->id; //main id

							$realTotal = 0;
							$subjectName = '';
							
							foreach($newSubjectIds as $subject_id)
							{
								$subjectData = Subject::select('id', 'which_course', 'which_test_series_type', 'which_group', 'subject_name', 'price')->where('id', '=', $subject_id)->with(['course', 'test_series_type', 'group'])->first();
								
								$subject_data = '';
								$subject_data = json_encode(array('course'=>@$subjectData->course->course_name, 'test_series_type'=>@$subjectData->test_series_type->test_series_type, 'group'=>@$subjectData->group->group_name, 'subject_name'=>@$subjectData->subject_name, 'price'=>@$subjectData->price));
								
								$realTotal = $realTotal + $subjectData->price;
								
								if($subjectName == '')
								{	
									$subjectName .= @$subjectData->subject_name;
								}
								else
								{
									$subjectName .= ', '.@$subjectData->subject_name;
								}
								
								$objPurchaseSubject = new PurchasedSubject;
								$objPurchaseSubject->student_id 					= @Auth::user()->id;
								$objPurchaseSubject->subject_id 					= @$subject_id;
								$objPurchaseSubject->subject_data 					= @$subject_data;
								$objPurchaseSubject->test_series_transaction_id 	= @$lastId;
							
								$objPurchaseSubject->save();
							}

							//discount start
								$subjectCount = count($newSubjectIds);
								
								if($discounts->count() > 0)
								{	
									$discount = array();
									$discountKey = array();	
									if($discounts->count() > 0)
									{
										foreach($discounts as $d)
										{
											$discount[$d->subject_numbers] = $d->discount;
											array_push($discountKey, $d->subject_numbers);
										}
									}

									sort($discountKey);
									
									$newDiscountKey = array();
									foreach($discountKey as $ke)
									{
										if($subjectCount >= $ke)
											{
												array_push($newDiscountKey, $ke);	
											}
									}
									
									if (count($newDiscountKey) == 0) 
										{
											$discount_val = 0;
										} 
									else 
										{
											$discountMainKay = $newDiscountKey[count($newDiscountKey) - 1];
											$discount_val = $discount[$discountMainKay];
										}
								}
								else
								{
									$discount_val = 0;
								}		
							//discount end
							
							//After Discount Total start	
								$grandTotal = $realTotal - $discount_val;
							//After Discount Total end
							
							//Save into test transaction history table start 
								$txnid  = str(hash('sha256', mt_rand() . microtime()), 0, 20);
							
								$updateObj	=	TestSeriesTransactionHistory::find(@$lastId);
								
								$updateObj->transaction_id 	=	$txnid;
								$updateObj->total_amount	=	@$realTotal;
								$updateObj->discount		=	@$discount_val;
								$updateObj->save();
							//Save into test transaction history table end 
							
							$stateData = State::select('id', 'name')->where('id', '=', @Auth::user()->state)->first();
							$countryData = Country::select('id', 'name')->where('id', '=', @Auth::user()->country)->first();

							$attributes = [
											'txnid' => $txnid,
											'amount' => $grandTotal,
											'productinfo' => $subjectName,
											'firstname' => @Auth::user()->first_name,
											'lastname' => @Auth::user()->last_name,
											'email' => @Auth::user()->email,
											'phone' => @Auth::user()->phone,
											'address1' => @Auth::user()->address,
											'city' => @Auth::user()->city,
											'state' => @$stateData->name,
											'country' => @$countryData->name,
											'zipcode' => @Auth::user()->zip,
											'udf1'=>$lastId,
											'udf2'=>'test_series'	
										];
														
							return Payment::make($attributes, function ($then) {
								$then->redirectRoute('test_series.modified_status');
							});
						}
				}		
				else
				{
					return Redirect::to('/modified_test_series')->with('error', 'Please select atleast one subject to proceed further.');
				}
			}
			else
			{
				return Redirect::to('/modified_test_series')->with('error', 'Please select atleast one subject to proceed further.');
			}	
		}		
	}
	
	public function status(Request $request)
	{
		$payment = Payment::capture();
		
		if($payment)
		{			
			// Get the payment status.
			$payment->isCaptured(); # Returns boolean - true / false

			$jsonData = json_decode(@$payment->data, true);
			
			if(isset($jsonData['udf1']) && !empty($jsonData['udf1']))
			{
				$obj					=	TestSeriesTransactionHistory::find(@$jsonData['udf1']);
				$obj->transaction_id	=	@$jsonData['txnid'];
				$obj->pay_amount		=	@$jsonData['amount'];
				if($payment->status == 'Failed')	
				{	
					$obj->response			=	0; //error
					$status = 0;	
				}
				else
				{	
					$obj->response			=	1; //success
					$status = 1;
				}
				$obj->whole_response	=	@$payment->data;	

				$saved = $obj->save();
				
				PurchasedSubject::where('test_series_transaction_id', '=', @$jsonData['udf1'])->update(array('status' => $status));
			}
			
			if(@$payment->status == 'Failed')
			{
				if($jsonData['unmappedstatus'] == 'userCancelled')
				{
					return Redirect::to('/test-series')->with('error', 'You have cancelled your last transaction, so your payment does not proceed.');
				}
				else
					{
						$errorMessage = 'Payment has been decline due to : '.@$payment->error_Message;
						return Redirect::to('/test-series')->with('error', $errorMessage);
					}	
			}
			else
			{
				if(isset($jsonData['udf1']) && !empty($jsonData['udf1']))
				{
					$testSeriesDetails = TestSeriesTransactionHistory::where('id', '=', trim(@$jsonData['udf1']))->with(['purchased_subject'=>function($subQuery){
						$subQuery->select('id', 'subject_id', 'test_series_transaction_id');
						$subQuery->with(['subject'=>function($subSubQuery){
							$subSubQuery->with(['course', 'test_series_type', 'group']);	
						}]);
					}])->first();
					
					if($testSeriesDetails)
					{
						//product info start	
							$productInfo = '';	
							foreach($testSeriesDetails->purchased_subject as $subjectData)
							{
								$productInfo .= '<tr style="border:1px solid #011224;">';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$subjectData->subject->course->course_name.'</td>';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$subjectData->subject->test_series_type->test_series_type.'</td>';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$subjectData->subject->group->group_name.'</td>';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$subjectData->subject->subject_name.'</td>';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">₹ '.@$subjectData->subject->price.'</td>';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">₹ '.@$subjectData->subject->price.'</td>';
								$productInfo .= '</tr>';
							}
						//product info end

						//email goes to student start
							$replace = array('{logo}', '{first_name}', '{last_name}', '{year}', '{productInfo}', '{total}');					
							$replace_with = array(\URL::to('/').Config::get('constants.logoImg'), @Auth::user()->first_name, @Auth::user()->last_name, date('Y'), $productInfo, $jsonData['amount']);
					
							$this->send_email_template($replace, $replace_with, 'order_test_series_student', @$jsonData['email']);
						//email goes to student end	
				
						//email goes to super admin start
							$replace = array('{logo}', '{first_name}', '{last_name}', '{email}', '{phone}', '{year}', '{productInfo}', '{total}');					
							$replace_with = array(\URL::to('/').Config::get('constants.logoImg'), @Auth::user()->first_name, @Auth::user()->last_name, @Auth::user()->email, @Auth::user()->phone, date('Y'), $productInfo, $jsonData['amount']);
				
							$this->send_email_template($replace, $replace_with, 'order_test_series_superadmin', 'apnamentor.com@gmail.com');
						//email goes to super admin end
				
						return Redirect::to('/thankyou');	
					}
					else
					{	
						return Redirect::to('/test-series')->with('error', 'There are something wrong. Please contact to Our Support for immediate basis.');
					}		
				}
				else
				{	
					return Redirect::to('/test-series')->with('error', 'There are something wrong. Please contact to Our Support for immediate basis.');
				}
			}
		}
		else
		{
			return Redirect::to('/test-series')->with('error', 'Payment has been declined due to some error, so please try again later or contact to our support.');
		}		
	}
	
	public function modified_status(Request $request)
	{
		$payment = Payment::capture();
		
		if($payment)
		{			
			// Get the payment status.
			$payment->isCaptured(); # Returns boolean - true / false

			$jsonData = json_decode(@$payment->data, true);
			
			if(isset($jsonData['udf1']) && !empty($jsonData['udf1']))
			{
				$obj					=	TestSeriesTransactionHistory::find(@$jsonData['udf1']);
				$obj->transaction_id	=	@$jsonData['txnid'];
				$obj->pay_amount		=	@$jsonData['amount'];
				if($payment->status == 'Failed')	
				{	
					$obj->response			=	0; //error
					$status = 0;	
				}
				else
				{	
					$obj->response			=	1; //success
					$status = 1;
				}
				$obj->whole_response	=	@$payment->data;	

				$saved = $obj->save();
				
				PurchasedSubject::where('test_series_transaction_id', '=', @$jsonData['udf1'])->update(array('status' => $status));
			}
			
			if(@$payment->status == 'Failed')
			{
				if($jsonData['unmappedstatus'] == 'userCancelled')
				{
					return Redirect::to('/modified_test_series')->with('error', 'You have cancelled your last transaction, so your payment does not proceed.');
				}
				else
					{
						$errorMessage = 'Payment has been decline due to : '.@$payment->error_Message;
						return Redirect::to('/modified_test_series')->with('error', $errorMessage);
					}	
			}
			else
			{
				if(isset($jsonData['udf1']) && !empty($jsonData['udf1']))
				{
					$testSeriesDetails = TestSeriesTransactionHistory::where('id', '=', trim(@$jsonData['udf1']))->with(['purchased_subject'=>function($subQuery){
						$subQuery->select('id', 'subject_id', 'test_series_transaction_id');
						$subQuery->with(['subject'=>function($subSubQuery){
							$subSubQuery->with(['course', 'test_series_type', 'group']);	
						}]);
					}])->first();
					
					if($testSeriesDetails)
					{
						//product info start	
							$productInfo = '';	
							foreach($testSeriesDetails->purchased_subject as $subjectData)
							{
								$productInfo .= '<tr style="border:1px solid #011224;">';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$subjectData->subject->course->course_name.'</td>';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$subjectData->subject->test_series_type->test_series_type.'</td>';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$subjectData->subject->group->group_name.'</td>';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$subjectData->subject->subject_name.'</td>';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">₹ '.@$subjectData->subject->price.'</td>';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">₹ '.@$subjectData->subject->price.'</td>';
								$productInfo .= '</tr>';
							}
						//product info end

						//email goes to student start
							$replace = array('{logo}', '{first_name}', '{last_name}', '{year}', '{productInfo}', '{total}');					
							$replace_with = array(\URL::to('/').Config::get('constants.logoImg'), @Auth::user()->first_name, @Auth::user()->last_name, date('Y'), $productInfo, $jsonData['amount']);
					
							$this->send_email_template($replace, $replace_with, 'order_test_series_student', @$jsonData['email']);
						//email goes to student end	
				
						//email goes to super admin start
							$replace = array('{logo}', '{first_name}', '{last_name}', '{email}', '{phone}', '{year}', '{productInfo}', '{total}');					
							$replace_with = array(\URL::to('/').Config::get('constants.logoImg'), @Auth::user()->first_name, @Auth::user()->last_name, @Auth::user()->email, @Auth::user()->phone, date('Y'), $productInfo, $jsonData['amount']);
				
							$this->send_email_template($replace, $replace_with, 'order_test_series_superadmin', 'apnamentor.com@gmail.com');
						//email goes to super admin end
				
						return Redirect::to('/thankyou');	
					}
					else
					{	
						return Redirect::to('/modified_test_series')->with('error', 'There are something wrong. Please contact to Our Support for immediate basis.');
					}		
				}
				else
				{	
					return Redirect::to('/modified_test_series')->with('error', 'There are something wrong. Please contact to Our Support for immediate basis.');
				}
			}
		}
		else
		{
			return Redirect::to('/modified_test_series')->with('error', 'Payment has been declined due to some error, so please try again later or contact to our support.');
		}		
	}
}