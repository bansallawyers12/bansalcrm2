<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

use Config;
use App\Admin;
use App\Wallet;
use App\WalletHistory;
//use Log;

class WalletController extends BaseController
{
	public function __construct(Request $request)
    {	
		//$siteData = WebsiteSetting::where('id', '!=', '')->first();
		//\View::share('siteData', $siteData);
	}
	
	public function Crdrhistory(Request $request){
		$client_id = $request->client_id;
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
			$walet = WalletHistory::where('user_id',$users->id)->where('customer_id',$request->user_id)->orderby('id','DESC')->get();
			$success['wallet'] =  @$walet;
			return $this->sendResponse($success, '');
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}
	}
	public function Wallethistory(Request $request){
		$client_id = $request->client_id;
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
			$walet = Wallet::where('user_id',$users->id)->where('customer_id',$request->user_id)->orderby('id','DESC')->get();
			$success['wallet'] =  @$walet;
			return $this->sendResponse($success, '');
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}
	}
	public function rechargeWallet(Request $request)
    {
		 $requestdata = $request->all();
		 
		$client_id = @$requestdata['user_id'];
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
			$wallet = new Wallet;
			$wallet->user_id = $users->id;
			$wallet->customer_id = @$requestdata['customer_id'];
			$wallet->pay_mode = @$requestdata['pay_mode'];
			$wallet->amount = @$requestdata['amount'];
			$wallet->cheque_no = @$requestdata['cheque_no'];
			$wallet->pay_date = @$requestdata['pay_date'];
			$wallet->bank_name = @$requestdata['bank_name'];
			$wallet->bank_branch = @$requestdata['bank_branch'];
			$wallet->bank_transaction_id = @$requestdata['bank_transaction_id'];
			$wallet->remarks = @$requestdata['remarks'];
			$saved = $wallet->save();
			if(!$saved)
			{
			return $this->sendError('Error', array('message'=>array(Config::get('constants.server_error'))));
			}else{
				return $this->sendResponse($wallet, '');
			}
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		} 
	}  
}