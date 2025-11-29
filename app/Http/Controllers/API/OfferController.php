<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
//use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Response;

use App\Admin;
use DB;

use App\Offer;

use Config;

class OfferController extends BaseController
{
	public function __construct(Request $request)
    {	
		//$siteData = WebsiteSetting::where('id', '!=', '')->first();
		//\View::share('siteData', $siteData);
	}
	
	public function offers(Request $request)
    {
		$skillData = array();
		$client_id = $request->client_id;
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
			$query = Offer::where('user_id',$users->id);
			if(@$request->type != 'all'){
				$query->where('type',$request->type);
			}
			$lists		= $query->orderby('id','ASC')->get();
			$data = array();
			$dateStart= date('Y-m-d');
			foreach($lists as $list){
				if(strtotime($dateStart)<=strtotime($list->expiry_date)){
				$data[] = array(
					'id' => $list->id,
					'name' => $list->name,
					'subtitle' => $list->subtitle,
					'description' => $list->description,
					'expiry_date' => $list->expiry_date,
					'image' => $list->image,
					);
				}
			}
			$success['offers'] =  @$data;
			$success['image_gallery_path'] 	=  \URL::to('/public/img/gallery_img').'/';
			return $this->sendResponse($success, '');
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}
    } 

}
?>