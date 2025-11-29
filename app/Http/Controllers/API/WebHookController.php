<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use Tzsk\Payu\Facade\Payment;

use App\TestSeriesTransactionHistory;
use App\PurchasedSubject;
use App\ProductOrder;
use App\ProductTransactionHistory;
use App\MyCart;

use Cookie;
use Config;
use Auth;

class WebHookController extends Controller
{
	public function successWebhook(Request $request, $token = NULL)
	{	
		if(isset($token) && !empty($token))
		{
			$token = trim($token);
				
			if($token == Config::get('constants.payumoney_unique_token'))
			{		
				$response = file_get_contents('php://input');
				
				if(isset($response) && !empty($response))
				{
					$json_decode = json_decode($response);

					if(isset($json_decode->udf1) && !empty($json_decode->udf1))
					{
						if(isset($json_decode->udf2) && !empty($json_decode->udf2))
						{
							if($json_decode->udf2 == 'test_series') //Test Series Webhook
							{
								$transactionHistoryId = @$json_decode->udf1; 
								
								$getData = TestSeriesTransactionHistory::where('id', '=', $transactionHistoryId)->first();
								
								if(!empty($getData))
								{
									if($getData->response == 0) //when user pays money but system did not update
									{
										$obj	=	TestSeriesTransactionHistory::find(@$transactionHistoryId);
										
										$obj->pay_amount		=	$json_decode->amount;
										$obj->response			=	1; //for getting success
										
										if($getData->whole_response == '')
										{
											$obj->whole_response 	= 	$response;	
										}		
										$obj->webhook_response 	= 	$response;	
										
										$saved				=	$obj->save();
										if(!$saved)
										{
											mail("raghavgarg.jpiet@gmail.com", "PayUMoney Success Response not saved into TestSeriesTransactionHistory table.",$response);	
										}
										else
										{
											PurchasedSubject::where('test_series_transaction_id', '=', @$transactionHistoryId)->update(array('status' => 1));
										}		
									}
								}		
							}
							else //product
							{
								$orderId = @$json_decode->udf2; 
								
								$getData = ProductOrder::where('id', '=', @$orderId)->first();
								
								if(!empty($getData))
								{
									if($getData->status == 0) //when user pays money but system did not update
									{	
										$objOrder			=	ProductOrder::find(@$orderId);
										$objOrder->status	=	1; //success
										
										$saved = $objOrder->save();
										if(!$saved)
										{
											mail("raghavgarg.jpiet@gmail.com", "PayUMoney Success Response not saved into ProductTransactionHistory table.",$response);	
										}
										else
										{
											//All attached Order start	
												$transactionHistoryIds = @$json_decode->udf1;
												$explodeTransactionIds = explode(",", $transactionHistoryIds);
												
												if(!empty($explodeTransactionIds))
												{
													foreach($explodeTransactionIds as $id)
													{
														$obj					=	ProductTransactionHistory::find(@$id);
														$obj->transaction_id	=	@$json_decode->merchantTransactionId;
														$obj->response			=	1; //success	
														$obj->webhook_response	=	$response;
														
														$savedObj = $obj->save();
													}
												}
											//All attached Order end
											
											//cart will expire start
												$cartId = @$json_decode->udf3;
				
												$objCart				=	MyCart::find(@$cartId);
												$objCart->is_expired 	= 	1; //expired
												$savedCart = $objCart->save();
											//cart will expire end
										}
									}		
								}			
							}		
						}
					}		
					mail("raghavgarg.jpiet@gmail.com","PayuMoney",$response);
				}
				else
				{
					echo json_encode(array('status'=>'error', 'message'=>'Response is NULL.'));
				}
			}
			else
			{
				echo json_encode(array('status'=>'error', 'message'=>'Token is missmatch.'));
			}		
		}
		else
		{
			echo json_encode(array('status'=>'error', 'message'=>'Token is missing.'));
		}		
	} 
}