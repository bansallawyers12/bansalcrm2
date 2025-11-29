<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

use App\Product;
use App\Professor;
use App\ModeProduct;
use App\ProductOtherInformation;
use App\ProductDemoVideo;
use App\ProductReview;

use Config;

class ProductController extends BaseController
{
    /**
     * All products list api
     *
     * @return \Illuminate\Http\Response
     */
    public function productsList(Request $request)
    {
		$subjects	= Product::select('id', 'subject_name')->where('id', '!=', '')->where('status', '=', 1)->get();
		
		$success['product_list'] =  @$subjects;
		return $this->sendResponse($success, 'Subjects has been fetched suceessfully.');
	}
	
	public function products(Request $request)
	{	
		$requestData = $request->all();
	
		if(isset($requestData['professor_id']) && !empty($requestData['professor_id']))
		{
			if(Professor::where('id', '=', @$requestData['professor_id'])->where('status', '=', 1)->exists()) 
			{
				//Professor Information Start	
					$professor	= Professor::select('id', 'which_organisation', 'first_name', 'last_name')->where('id', '=', @$requestData['professor_id'])->with(['organisationData'=>function($subQuery){
								$subQuery->select('id', 'profile_img');
					}])->first();
				//Professor Information End

				$query 		= 	Product::select('id', 'subject_name', 'batch_type', 'stock_out', 'image')->where('professor_id', '=', @$requestData['professor_id'])->where('status', '=', 1);
				$query->with(['productOtherInfo'=>function($subQuery){	
					$subQuery->orderBy('product_other_informations.total_amount', 'asc');
				}]);
		
				$data		= $query->sortable(['order_number'=>'asc'])->get();
				
				$success['professor_detail'] 	=  @$professor;
				$success['product_list'] 				=  @$data;
				$success['image_base_path'] 	=  \URL::to('/public/mg/product_img').'/';
				
				return $this->sendResponse($success, 'Products have been fetched suceessfully.');
			}
			else
			{
				return $this->sendError('Error', array('product_id'=>array('Professor'.Config::get('constants.not_exist'))));	
			}
		}
		else
		{
			return $this->sendError('Error', array('id'=>array('Please send Professor ID.'))); 
		}
	}

	public function viewProduct(Request $request)
	{
		$requestData = $request->all();
		
		if(isset($requestData['product_id']) && !empty($requestData['product_id']))
		{
			if(Product::where('id', '=', $requestData['product_id'])->exists()) 
			{
				//product individual details start	
					$fetchedData 		= 	Product::where('id', '=', $requestData['product_id'])->with(['professor'=>function($query){
						$query->select('id', 'first_name', 'last_name', 'about_faculty');
					}])->first();

					$productOtherInfo = ProductOtherInformation::where('product_id', '=', $requestData['product_id'])->with(['mode_product'=>function($query){
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
					
					$productDemoInfo = ProductDemoVideo::where('product_id', '=', $requestData['product_id'])->get();
					
					$productReviewInfo = ProductReview::where('product_id', '=', $requestData['product_id'])->where('status', '=', 1)->with(['studentData'])->get();
				//product individual details end	
		
				
				//minimum price product detail start
					$minProductQuery 		= 	Product::select('id')->where('id', '=', $requestData['product_id'])->where('status', '=', 1);
					$minProductQuery->with(['productOtherInfo'=>function($subQuery){	
						$subQuery->orderBy('product_other_informations.total_amount', 'asc');
					}]);
					$minProduct = $minProductQuery->first();
				//minimum price product detail end
		
				$success['product_detail'] 		=  @$fetchedData;
				$success['product_other_info'] 	=  @$productOtherInfo;
				$success['product_demo_info'] 	=  @$productDemoInfo;
				$success['product_review_info'] =  @$productReviewInfo;
				$success['min_product_info'] 	=  @$minProduct;
				$success['mode_of_product'] 	=  @$modeOfProduct;
				$success['image_base_path'] 	=  \URL::to('/public/mg/product_img').'/';
				
				return $this->sendResponse($success, 'Product has been fetched suceessfully.');
			}
			else
			{
				return $this->sendError('Error', array('product_id'=>array('Product'.Config::get('constants.not_exist')))); 	
			}
		}
		else
		{
			return $this->sendError('Error', array('product_id'=>array('Please send Product ID.'))); 
		}
	}
}