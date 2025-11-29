<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Professor;
use App\Product;
use App\ProductOtherInformation;
use App\ProductDemoVideo;
use App\MyCart;
use App\CartItem;
use App\WebsiteSetting;


use Cookie;
use Config;
use Auth;

class CartController extends Controller
{
	public function __construct(Request $request)
    {
		$siteData = WebsiteSetting::where('id', '!=', '')->first();
		\View::share('siteData', $siteData);
		
		//remove all cokies start
			\Cookie::queue(\Cookie::forget('subject_ids'));
		//remove all cokies end	
		
		if ($request->isMethod('post')) 
		{	
			$requestData 		= 	@$request->all();
			if(trim(@$requestData['user_id']) == '')
			{
				\Cookie::queue(\Cookie::make('product_id', @$requestData['product_id'], 3600));
				\Cookie::queue(\Cookie::make('product_other_info_id', @$requestData['product_other_info_id'], 3600));
				\Cookie::queue(\Cookie::make('quantity', @$requestData['quantity'], 3600));
			}	
		}		
        $this->middleware('auth:web');
	}
	/**
     * All Cart Items.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		if ($request->isMethod('post')) 
		{
			//remove all cokies start
				\Cookie::queue(\Cookie::forget('product_id'));
				\Cookie::queue(\Cookie::forget('product_other_info_id'));
				\Cookie::queue(\Cookie::forget('quantity'));
			//remove all cokies end	
			
			$requestData 		= 	@$request->all();
			
			$cartDetail = MyCart::where('id', '!=', '')->where('user_id', '=', @$requestData['user_id'])->where('is_expired', '=', 0)->first();
			
			if(empty($cartDetail))
			{
				$objCart = new MyCart;
			}		
			else
			{
				$objCart = MyCart::find(@$cartDetail->id);
			}
			
			$objCart->user_id 		= @Auth::user()->id;
			
			$savedCart				=	$objCart->save();
			
			if(!$savedCart)
			{ //error
				return Redirect::to('/cart')->with('error', Config::get('constants.server_error'));
			}	
			else
			{ //success
				if(empty($cartDetail))
				{
					$cart_id =  @$objCart->id; //last inserted id
				}		
				else
				{
					$cart_id = @$cartDetail->id;
				}
				
				$cartItem = CartItem::select('id', 'quantity')->where('id', '!=', '')->where('user_id', '=', @$requestData['user_id'])->where('product_id', '=', @$requestData['product_id'])->where('product_other_info_id', '=', @$requestData['product_other_info_id'])->where('cart_id', '=', $cart_id)->first();
				
				if(!empty($cartItem))
				{
					$objCartItem = CartItem::find(@$cartItem->id);
					$objCartItem->quantity 	= @$cartItem->quantity + @$requestData['quantity'];
				}
				else
				{
					$objCartItem = new CartItem;
					$objCartItem->quantity 	= @$requestData['quantity'];
				}
				
				$objCartItem->cart_id 					= @$cart_id;
				$objCartItem->user_id 					= @$requestData['user_id'];
				$objCartItem->product_id 				= @$requestData['product_id'];
				$objCartItem->product_other_info_id 	= @$requestData['product_other_info_id'];

				$savedCartItem					=	$objCartItem->save();
				
				if(!$savedCartItem)
				{ //error	
					return Redirect::to('/cart')->with('error', Config::get('constants.server_error'));
				}	
				else
				{ //success
					return Redirect::to('/cart')->with('success', 'Product has been added successfully into your cart.');
				} 
			}		
		}
		else
		{
			$product_id = @Cookie::get('product_id');
			if(isset($product_id) && !empty($product_id))
			{
				$cartDetail = MyCart::where('id', '!=', '')->where('user_id', '=', @Auth::user()->id)->where('is_expired', '=', 0)->first();
				
				if(empty($cartDetail))
				{
					$objCart = new MyCart;
				}		
				else
				{
					$objCart = MyCart::find(@$cartDetail->id);
				}		

				$objCart->user_id 		= @Auth::user()->id;
				
				$savedCart				=	$objCart->save();
				
				if(!$savedCart)
				{ //error
					return Redirect::to('/cart')->with('error', Config::get('constants.server_error'));
				}	
				else
				{ //success
					if(empty($cartDetail))
					{
						$cart_id =  @$objCart->id; //last inserted id
					}		
					else
					{
						$cart_id = @$cartDetail->id;
					}
					
					$cartItem = CartItem::select('id', 'quantity')->where('id', '!=', '')->where('user_id', '=', @Auth::user()->id)->where('product_id', '=', @Cookie::get('product_id'))->where('product_other_info_id', '=', @Cookie::get('product_other_info_id'))->where('cart_id', '=', $cart_id)->first();
				
					if(!empty($cartItem))
					{
						$objCartItem = CartItem::find(@$cartItem->id);
						$objCartItem->quantity 	= @$cartItem->quantity + @Cookie::get('quantity');
					}
					else
					{
						$objCartItem = new CartItem;
						$objCartItem->quantity 	= @Cookie::get('quantity');
					}
					
					$objCartItem->cart_id 					= @$cart_id;
					$objCartItem->user_id 					= @Auth::user()->id;
					$objCartItem->product_id 				= @Cookie::get('product_id');
					$objCartItem->product_other_info_id 	= @Cookie::get('product_other_info_id');

					$savedCartItem					=	$objCartItem->save();
					
					if(!$savedCartItem)
					{ //error
						//remove all cokies start
							\Cookie::queue(\Cookie::forget('product_id'));
							\Cookie::queue(\Cookie::forget('product_other_info_id'));
							\Cookie::queue(\Cookie::forget('quantity'));
						//remove all cokies end	
							
						return Redirect::to('/cart')->with('error', Config::get('constants.server_error'));
					}	
					else
					{ //success
						//remove all cokies start
							\Cookie::queue(\Cookie::forget('product_id'));
							\Cookie::queue(\Cookie::forget('product_other_info_id'));
							\Cookie::queue(\Cookie::forget('quantity'));
						//remove all cokies end

						return Redirect::to('/cart')->with('success', 'Product has been added successfully into your cart.');
					} 
				}		
			}
			else
			{
				$fetchedData = MyCart::where('id', '!=', '')->where('user_id', '=', @Auth::user()->id)->where('is_expired', '=', 0)->with(['cartItem'=>function($query){
					$query->with(['productData'=>function($subQuery){
						$subQuery->select('id', 'batch_type', 'subject_name', 'image');
					}, 'productOtherInfo'=>function($subSubQuery){
						$subSubQuery->with(['mode_product']);
					}]);
				}])->first();	
			}
		}
		return view('Frontend.cart.index', compact(['fetchedData']));	
	}
	
	public function updateCart(Request $request)
	{	
		$status 			= 	0;
		$method 			= 	$request->method();	
		if ($request->isMethod('post')) 
		{
			$requestData 	= 	$request->all();

			$requestData['id'] = trim(@$requestData['id']);
			$requestData['quantity'] = trim(@$requestData['quantity']);
			
			if(isset($requestData['id']) && !empty($requestData['id']) && isset($requestData['quantity']) && !empty($requestData['quantity'])) 
			{
				$objCartItem  					= CartItem::find(@$requestData['id']);
				$objCartItem->quantity 			= @$requestData['quantity'];
					
				$savedCartItem				=	$objCartItem->save();
				
				if(!$savedCartItem)
				{ //error
					$message = Config::get('constants.server_error');
				}	
				else
				{ //success
					$status = 1;
					$message = 'Cart has been updated successfully.';
				}	
			} 
			else 
			{
				$message = 'Id OR Quantity does not exist, please check it once again.';		
			}		
		} 
		else 
		{
			$message = Config::get('constants.post_method');
		}
		echo json_encode(array('status'=>$status, 'message'=>$message));
		die;
	}
	
}