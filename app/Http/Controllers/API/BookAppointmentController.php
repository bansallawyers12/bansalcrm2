<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\FlightDetail;
use App\Admin;
use DB;
class FlightController extends BaseController
{
	public function __construct(Request $request)
    {	
		//$siteData = WebsiteSetting::where('id', '!=', '')->first();
		//\View::share('siteData', $siteData);
	}
	
	public function bookappointment(Request $request){
		
		
	}
}