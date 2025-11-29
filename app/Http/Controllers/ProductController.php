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
use App\ProductReview;
use App\ModeProduct;
use App\WebsiteSetting;
use App\SeoPage;

use Config;
use Auth;
use Cookie;

class ProductController extends Controller
{
	public function __construct(Request $request)
    {
		//remove all cokies start
			\Cookie::queue(\Cookie::forget('product_id'));
			\Cookie::queue(\Cookie::forget('product_other_info_id'));
			\Cookie::queue(\Cookie::forget('quantity'));
			\Cookie::queue(\Cookie::forget('subject_ids'));
		//remove all cokies end	
		
		$siteData = WebsiteSetting::where('id', '!=', '')->first();
		\View::share('siteData', $siteData);
	}
	
	/**
     * All Products.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request, $id = NULL)
	{
		if(isset($id) && !empty($id))
		{
			$id = $this->decodeString($id);
			if(Professor::where('id', '=', $id)->where('status', '=', 1)->exists()) 
			{
				//Professor Information Start	
					$professor	= Professor::select('id', 'which_organisation', 'first_name', 'last_name')->where('id', '=', $id)->with(['organisationData'=>function($subQuery){
								$subQuery->select('id', 'profile_img');
					}])->first();
				//Professor Information End

				$query 		= 	Product::select('id', 'subject_name', 'batch_type', 'stock_out', 'image')->where('professor_id', '=', $id)->where('status', '=', 1);
				$subjects = $query->get();
				
				$query->with(['productOtherInfo'=>function($subQuery){	
					$subQuery->orderBy('product_other_informations.total_amount', 'asc');
				}]);

				$totalData 	= $query->count();	//for all data
				
				if ($request->has('search_term')) 
				{
					$search_term 		= 	$request->input('search_term');
					if(trim($search_term) != '')
					{	
						$searchTermArray = explode(',', $search_term);
						
						$productIds = array();
					
						foreach($searchTermArray as $search)
						{
							$breakDownArray	= explode('_', $search);	
							$value = $this->decodeString($breakDownArray[1]);
	
							array_push($productIds, $value);		
						}
						
						if(!empty($productIds))
						{
								$query->whereIn('id', $productIds);
						}
					}
					$totalData 	= $query->count();
				}
		
				$lists		= $query->sortable(['order_number'=>'asc'])->paginate(config('constants.unlimited'));
				
				$seoDetails = SeoPage::where('page_slug', '=', 'our_products')->first();
				
				return view('Frontend.products.index',compact(['professor', 'subjects', 'lists', 'totalData', 'seoDetails']));
			}
			else
			{
				return Redirect::to('/professors')->with('error', 'For this Professor product '.Config::get('constants.not_exist'));
			}
		}
		else
		{
			return Redirect::to('/professors')->with('error', 'For this Professor product '.Config::get('constants.not_exist'));
		}
	}
	
	public function viewProduct(Request $request, $id)
	{
		/* $replace = array('{logo}', '{first_name}', '{last_name}');					
		$replace_with = array(Config::get('constants.logo'), 'Test', 'Test');
			
		$this->send_email_template($replace, $replace_with, 'signup', 'raghavgarg.jpiet@gmail.com');
			
		die; */
		if(isset($id) && !empty($id))
		{
			$id = $this->decodeString($id);
			if(Product::where('id', '=', $id)->exists()) 
			{
				//product individual details start	
					$fetchedData 		= 	Product::where('id', '=', $id)->with(['professor'=>function($query){
						$query->select('id', 'first_name', 'last_name', 'about_faculty');
					}])->first();

					$productOtherInfo = ProductOtherInformation::where('product_id', '=', $id)->with(['mode_product'=>function($query){
						$query->select('id', 'mode_product');
					}])->get();
					
					//mode of product start	
						$modeofProductIds = array();	
						foreach($productOtherInfo as $info)
						{
							array_push($modeofProductIds, $info->mode_of_product);
						}
						$modeofProductIds = array_unique($modeofProductIds);
						
						$modeOfProduct = ModeProduct::whereIn('id', $modeofProductIds)->get();
					//mode of product end
					
					$productDemoInfo = ProductDemoVideo::where('product_id', '=', $id)->get();
					
					$productReviewInfo = ProductReview::where('product_id', '=', $id)->where('status', '=', 1)->with(['studentData'])->get();
				//product individual details end	

				//for other classes I start
					$fetchedAllOtherClasses = 	Product::select('id', 'professor_id', 'subject_name', 'batch_type', 'stock_out', 'image')->where('id', '!=', $id)->where('status', '=', 1)->where('professor_id', '=', $fetchedData->professor_id)->with(['professor'=>function($subQuery){
						$subQuery->select('id', 'first_name', 'last_name');
					}, 'productOtherInfo'=>function($subQuery){	
						$subQuery->orderBy('product_other_informations.total_amount', 'asc');
					}])->get();
				//for other classes	I end	
				
				//for other classes	II start
					$fetchedAllOtherClassesOther = 	Product::select('id', 'professor_id', 'subject_name', 'batch_type', 'stock_out', 'image')->where('status', '=', 1)->where('professor_id', '!=', $fetchedData->professor_id)->with(['professor'=>function($subQuery){
						$subQuery->select('id', 'first_name', 'last_name');
					}, 'productOtherInfo'=>function($subQuery){	
						$subQuery->orderBy('product_other_informations.total_amount', 'asc');
					}])->get();
				//for other classes	II end	
				
				
				//minimum price product detail
					$minProductQuery 		= 	Product::select('id')->where('id', '=', $id)->where('status', '=', 1);
					$minProductQuery->with(['productOtherInfo'=>function($subQuery){	
						$subQuery->orderBy('product_other_informations.total_amount', 'asc');
					}]);
					$minProduct = $minProductQuery->first();
		
				$seoDetails = SeoPage::where('page_slug', '=', 'view_product')->first();
		
				return view('Frontend.products.view_product', compact(['fetchedData', 'productOtherInfo', 'productDemoInfo', 'productReviewInfo', 'fetchedAllOtherClasses', 'fetchedAllOtherClassesOther', 'modeOfProduct', 'minProduct', 'seoDetails']));
			}
			else
			{
				return Redirect::to('/professors')->with('error', 'Product '.Config::get('constants.not_exist'));
			}
		}
		else
		{
			return Redirect::to('/professors')->with('error', 'Product '.Config::get('constants.not_exist'));
		}
	}
}