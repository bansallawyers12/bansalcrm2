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
use App\Product;
use App\ProductOtherInformation;
use App\MyCart;
use App\ProductOrder;
use App\ProductTransactionHistory;
use App\WebsiteSetting;



use Cookie;
use Config;
use Auth;

class PaymentController extends Controller
{
	public function __construct(Request $request)
    {	
		$siteData = WebsiteSetting::where('id', '!=', '')->first();
		\View::share('siteData', $siteData);
        $this->middleware('auth:web');
	}
	/**
     * All Payment Process.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		$fetchedData = MyCart::where('id', '!=', '')->where('user_id', '=', @Auth::user()->id)->where('is_expired', '=', 0)->with(['cartItem'=>function($query){
			$query->with(['productData'=>function($subQuery){
				$subQuery->select('id', 'batch_type', 'subject_name', 'image');
			}, 'productOtherInfo'=>function($subSubQuery){
				$subSubQuery->with(['mode_product']);
			}]);
		}])->first();
			
		if(!empty(@$fetchedData))
		{
			if(count(@$fetchedData['cartItem']) > 0)
			{
				return view('Frontend.payment.index', compact(['fetchedData']));	
			}
			else
			{
				return Redirect::to('/cart')->with('error', 'There are no product added into your cart, so you can not proceed further.');
			}		
		}
		else
		{
			return Redirect::to('/cart')->with('error', 'There are no product added into your cart, so you can not proceed further.');
		}	
	} 

	public function checkout(Request $request)
	{
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();

			$fetchedData = MyCart::where('id', '!=', '')->where('user_id', '=', @Auth::user()->id)->where('is_expired', '=', 0)->with(['cartItem'=>function($query){
				$query->with(['productData'=>function($subQuery){
					$subQuery->select('id', 'batch_type', 'subject_name', 'image');
				}, 'productOtherInfo'=>function($subSubQuery){
					$subSubQuery->with(['mode_product']);
				}]);
			}])->first();
			
			if(!empty(@$fetchedData))
			{
				if(count(@$fetchedData['cartItem']) > 0)
				{
					$cartId = @$fetchedData->id;
									
					$obj				=	new ProductOrder; //main product start
					$obj->student_id	=	@Auth::user()->id;
					
					$saved = $obj->save();
					
					if($saved)
					{
						$mainOrderId = @$obj->id; //main order
						
						$dataSaved = 0;	
						$arrayProduct = array();
						$grandTotal = 0;
						$transactionHistoryIds = array();
	
						foreach(@$fetchedData['cartItem'] as $data)
						{
							$objTran				=	new ProductTransactionHistory;
						
							$objTran->student_id	=	@Auth::user()->id;
							$objTran->order_id		=	@$mainOrderId;
							$objTran->product_id	=	@$data->product_id;
							
							$productInfos = Product::select('id', 'professor_id', 'subject_name')->where('id', '=', @$data->product_id)->with(['professor'=>function($query){
								$query->select('id', 'first_name', 'last_name');
							}])->first();
							
							$objTran->professor_name =	@$productInfos->professor->first_name.' '.@$productInfos->professor->last_name;
							$objTran->subject_name	=	@$productInfos->subject_name;

							$otherInformation 	=	ProductOtherInformation::where('id', '=', @$data->product_other_info_id)->select('id', 'mode_of_product', 'duration', 'validity', 'price', 'discount', 'views', 'total_amount')->first();  
							
							if(!empty(@$otherInformation))
							{	
								$objTran->mode_product	=	@$otherInformation->mode_of_product;
								$objTran->duration		=	@$otherInformation->duration;
								$objTran->validity		=	@$otherInformation->validity;
								$objTran->price			=	@$otherInformation->price;
								$objTran->discount		=	@$otherInformation->discount;
								$objTran->views			=	@$otherInformation->views;
								$objTran->quantity		=	@$data->quantity;
								$objTran->total_amount	=	@$otherInformation->total_amount;
								$objTran->pay_amount	=	@$data->quantity * @$otherInformation->total_amount;
							}
							$savedTran = $objTran->save();
							
							if($savedTran)
							{
								array_push($transactionHistoryIds, @$objTran->id);
								
								array_push($arrayProduct, @$data->product_id);
								if(!empty(@$otherInformation))
								{
									$grandTotal = $grandTotal + $objTran->pay_amount;
								}
								
								$dataSaved = 1; //at least success in one item
							}
						}

						if($dataSaved == 1)
						{
							$productIds = array_unique($arrayProduct);
							
							$productName = '';
							foreach($productIds as $pid)
							{
								$productInfo = Product::select('id', 'subject_name')->where('id', '=', $pid)->first();
								
								if($productName == '')
								{	
									$productName .= @$productInfo->subject_name;
								}
								else
								{
									$productName .= ', '.@$productInfo->subject_name;
								}	
							}	
							
							$stateData = State::select('id', 'name')->where('id', '=', @Auth::user()->state)->first();
							$countryData = Country::select('id', 'name')->where('id', '=', @Auth::user()->country)->first();
							
							if(!empty($transactionHistoryIds))
							{
								$transaction_history_ids = implode(",", $transactionHistoryIds);	
							}
							
							$attributes = [
											'txnid' => substr(hash('sha256', mt_rand() . microtime()), 0, 20),
											'amount' => $grandTotal,
											'productinfo' => $productName,
											'firstname' => @Auth::user()->first_name,
											'lastname' => @Auth::user()->last_name,
											'email' => @Auth::user()->email,
											'phone' => @Auth::user()->phone,
											'address1' => @Auth::user()->address,
											'city' => @Auth::user()->city,
											'state' => @$stateData->name,
											'country' => @$countryData->name,
											'zipcode' => @Auth::user()->zip,
											'udf1'=>$transaction_history_ids,
											'udf2'=>$mainOrderId,
											'udf3'=>$cartId	
										];
													
							return Payment::make($attributes, function ($then) {
								$then->redirectRoute('payment.status');
							});
						}
						else
						{
							return redirect()->back()->with('error', Config::get('constants.server_error'));
						}
					}
					else
					{
						return redirect()->back()->with('error', Config::get('constants.server_error'));
					}		
				}
				else
				{
					return Redirect::to('/cart')->with('error', 'There are no product added into your cart, so you can not proceed further.');
				}		
			}
			else
			{
				return Redirect::to('/cart')->with('error', 'There are no product added into your cart, so you can not proceed further.');
			}		
		}
		else
		{
			return redirect()->back()->with('error', Config::get('constants.post_method'));
		}	
	}
	
	public function status(Request $request)
	{
		$payment = Payment::capture();
		
		if($payment)
		{			
			// Get the payment status.
			$payment->isCaptured(); # Returns boolean - true / false

			$jsonData = json_decode($payment->data, true);
			
			$transactionHistoryIds = $jsonData['udf1'];
			$explodeTransactionIds = explode(",", $transactionHistoryIds);
			
			if(isset($jsonData['udf2']) && !empty($jsonData['udf2']))
			{
				$objOrder	=	ProductOrder::find(@$jsonData['udf2']);
				if($payment->status == 'Failed')	
				{	
					$objOrder->status			=	0; //error
				}
				else
				{	
					$objOrder->status			=	1; //success
				}
				
				$savedOrder = $objOrder->save();
			}
			
			if(!empty($explodeTransactionIds))
			{
				foreach($explodeTransactionIds as $id)
				{
					$obj					=	ProductTransactionHistory::find(@$id);
					$obj->transaction_id	=	$jsonData['txnid'];
					if($payment->status == 'Failed')	
					{	
						$obj->response			=	0; //error
					}
					else
					{	
						$obj->response			=	1; //success
					}
					$obj->whole_response	=	$payment->data;	
					$saved = $obj->save();
				}
			}
			
			if(@$payment->status == 'Failed')
			{	
				if($jsonData['unmappedstatus'] == 'userCancelled')
				{
					return Redirect::to('/cart')->with('error', 'You have cancelled your last transaction, so your payment does not proceed.');
				}
				else
					{
						$errorMessage = 'Payment has been decline due to : '.@$payment->error_Message;
						return Redirect::to('/cart')->with('error', $errorMessage);
					}	
			}
			else
			{	
				$cartId = $jsonData['udf3'];
			
				$objCart				=	MyCart::find(@$cartId);
				$objCart->is_expired 	= 	1; //expired
				
				$savedCart = $objCart->save();	
				
				if($savedCart)
				{
					//product info start
						$professorsEmails = array();
						$productInfo = '';
						if(!empty($explodeTransactionIds))
						{
							foreach($explodeTransactionIds as $id)
							{
								$dataTransaction = ProductTransactionHistory::where('id', '=', $id)->with(['product'=>function($subQuery){
									$subQuery->select('id', 'professor_id', 'subject_name');
									$subQuery->with(['professor'=>function($subSubQuery){
										$subSubQuery->select('id', 'first_name', 'last_name', 'which_organisation');
										$subSubQuery->with(['organisationData'=>function($subSubSubQuery){
											$subSubSubQuery->select('id', 'email');
										}]);	
									}]);	
								}, 'mode_product_data'=>function($subQuery){
									$subQuery->select('id', 'mode_product');
								}])->first();
								
								array_push($professorsEmails, $dataTransaction->product->professor->organisationData->email);
								
								$productInfo .= '<tr style="border:1px solid #011224;">';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->product->professor->first_name.' '.@$dataTransaction->product->professor->last_name.'</td>';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->product->subject_name.'</td>';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->mode_product_data->mode_product.'</td>';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->views.'</td>';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->duration.'</td>';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->validity.'</td>';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">₹ '.@$dataTransaction->price.'</td>';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->discount.'%</td>';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->quantity.'</td>';
								$productInfo .= '<td style="border:1px solid #011224; text-align:center;">₹ '.@$dataTransaction->total_amount.'</td>';
								$productInfo .= '</tr>';	
							}
						}
					//product info end
					
					//shipping info start
						$stateData = State::select('id', 'name')->where('id', '=', @Auth::user()->state)->first();
						$countryData = Country::select('id', 'name')->where('id', '=', @Auth::user()->country)->first();
					
						$shipping_info = '';
						$shipping_info .= '<p style="font-size: 14px; margin: 2px;">'.@Auth::user()->first_name.' '.@Auth::user()->last_name.'</p>';
						$shipping_info .= '<p style="font-size: 14px; margin: 2px;">'.@Auth::user()->email.'</p>';		
						$shipping_info .= '<p style="font-size: 14px; margin: 2px;">'.@Auth::user()->address.'</p>';		
						$shipping_info .= '<p style="font-size: 14px; margin: 2px;">'.@Auth::user()->city.'</p>';	
						$shipping_info .= '<p style="font-size: 14px; margin: 2px;">'.@$stateData->name.', '.@$countryData->name.'</p>';	
						$shipping_info .= '<p style="font-size: 14px; margin: 2px;">'.@Auth::user()->zip.'</p>';	
						$shipping_info .= '<p style="font-size: 14px; margin: 2px;"><b>Mobile No :</b> '.@Auth::user()->phone.'</p>';
					//shipping info end

					//email goes to student start
						$replace = array('{logo}', '{first_name}', '{last_name}', '{year}', '{order_id}', '{productInfo}', '{total}', '{shipping_info}');					
						$replace_with = array(\URL::to('/').Config::get('constants.logoImg'), @Auth::user()->first_name, @Auth::user()->last_name, date('Y'), $jsonData['udf2'], $productInfo, $jsonData['amount'], $shipping_info);
				
						$this->send_email_template($replace, $replace_with, 'order_student', @$jsonData['email']);
					//email goes to student end	
					
					//email goes to super admin start
						$replace = array('{logo}', '{first_name}', '{last_name}', '{year}', '{order_id}', '{productInfo}', '{total}', '{shipping_info}');					
						$replace_with = array(\URL::to('/').Config::get('constants.logoImg'), @Auth::user()->first_name, @Auth::user()->last_name, date('Y'), $jsonData['udf2'], $productInfo, $jsonData['amount'], $shipping_info);
			
						$this->send_email_template($replace, $replace_with, 'order_superadmin', 'apnamentor.com@gmail.com');
					//email goes to super admin end
							
					//email goes to vendor start
						array_unique($professorsEmails);
						$professorsEmails = implode(";", $professorsEmails);
						
						$replace = array('{logo}', '{first_name}', '{last_name}', '{year}', '{order_id}', '{productInfo}', '{total}', '{shipping_info}');					
						$replace_with = array(\URL::to('/').Config::get('constants.logoImg'), @Auth::user()->first_name, @Auth::user()->last_name, date('Y'), $jsonData['udf2'], $productInfo, $jsonData['amount'], $shipping_info);

						$this->send_email_template($replace, $replace_with, 'order_professors', @$professorsEmails);
					//email goes to vendor end
					
					return Redirect::to('/thankyou');
				}	
			}
		}
		else
		{
			return Redirect::to('/payment')->with('error', 'Payment has been declined due to some error, so please try again later or contact to our support.');
		}		
	}
	
	public function thankyou(Request $request)
	{
		return view('Frontend.payment.thankyou');
	}
}