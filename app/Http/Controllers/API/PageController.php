<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
//use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Response;

use App\CmsPage;
use App\Admin;

use Config;
class PageController extends BaseController
{
	public function __construct(Request $request)
    {	
		//$siteData = WebsiteSetting::where('id', '!=', '')->first();
		//\View::share('siteData', $siteData);
	}
	
	public function Index(Request $request){
		$client_id = $request->client_id;
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
			$pagedetail = CmsPage::where('user_id',$users->id)->where('slug',$request->slug)->first();
			if($pagedetail){
				$success['pagedetail'] 	=  $pagedetail;
				$success['image_base_path'] 	=  \URL::to('/public/img/cmspage').'/';
			return $this->sendResponse($success, '');
		}else{
			return $this->sendError('Error', array('id'=>array('id not found'))); 
		}
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}	
	}
}
?>