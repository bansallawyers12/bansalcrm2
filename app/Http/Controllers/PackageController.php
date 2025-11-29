<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; 
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;

use App\Destination;
use App\Package;
use App\WebsiteSetting;
use Illuminate\Support\Facades\Session;

use Config;
use Cookie;

class PackageController extends Controller
{
	public function __construct()
    {	
		$siteData = WebsiteSetting::where('id', '!=', '')->first();
		\View::share('siteData', $siteData);
	}
	public function index(Request $request, $slug = NULL)
    {
		if(isset($slug) && !empty($slug))
		{
			$client_id = env('TRAVEL_CLIENT_ID', '');
			$iurl = env('TRAVEL_API_URL', '')."destination-detail?slug=".$slug."&order=DESC&limit=10&client_id=".$client_id; 
			$destinationdetail = $this->curlRequest($iurl,'GET','');
			
			 $furl = env('TRAVEL_API_URL', '')."filterdata?slug=".$slug."&client_id=".$client_id; 
			$filterlist = $this->curlRequest($furl,'GET','');
			return view('packagelist', compact(['destinationdetail', 'slug', 'filterlist']));
		}
		
    }
	
	 public function theme(Request $request, $slug = NULL)
    {
		if(isset($slug) && !empty($slug))
		{
			$client_id = env('TRAVEL_CLIENT_ID', '');
			 $iurl = env('TRAVEL_API_URL', '')."theme-detail?slug=".$slug."&order=DESC&limit=10&client_id=".$client_id; 
			$destinationdetail = $this->curlRequest($iurl,'GET','');
			
			$furl = env('TRAVEL_API_URL', '')."filterdata?themeslug=".$slug."&client_id=".$client_id; 
			$filterlist = $this->curlRequest($furl,'GET','');
			return view('themelist', compact(['destinationdetail', 'slug', 'filterlist']));
		}
		
    } 
	
	public function Search(Request $request)
    {
		$client_id = env('TRAVEL_CLIENT_ID', '');
		
		$ilocation = env('TRAVEL_API_URL', '')."location?name=".$request->term; 
		$locationdetail = $this->curlRequest($ilocation,'GET','');
		 $locations = json_decode($locationdetail);
		
		$iurl = env('TRAVEL_API_URL', '')."destination-detail?slug=".$locations->slug."&order=DESC&limit=10&client_id=".$client_id; 
		$destinationdetail = $this->curlRequest($iurl,'GET','');
		
		$furl = env('TRAVEL_API_URL', '')."filterdata?slug=".$locations->slug."&client_id=".$client_id; 
		$slug = $locations->slug;
		$filterlist = $this->curlRequest($furl,'GET','');
		return view('packagelist', compact(['destinationdetail', 'slug', 'filterlist']));
		
    }
	   
	public function searchpackagedetails(Request $request)
    {
		if(isset($request->tslug) && !empty($request->tslug))
		{
			$slug = "?tslug=".$request->tslug;
		}else{
			$slug = "?slug=".$request->slug;
		}
		$ptype = json_encode($request->ptype);	
		$client_id = env('TRAVEL_CLIENT_ID', '');
			 $iurl = env('TRAVEL_API_URL', '')."search-destination-detail".$slug."&price=".$request->price."&duration=".$request->duration."&city=".$request->city."&ptype=".$ptype."&client_id=".$client_id; 
			$destinationdetail = $this->curlRequest($iurl,'GET','');
			return view('searchlist', compact(['destinationdetail']));
		//} 
		
    }
	
	public function packdetails(Request $request,$dslug= NULL ,$slug = NULL)
    {
		if(isset($slug) && !empty($slug))
		{
			$client_id = env('TRAVEL_CLIENT_ID', '');
			$iurl = env('TRAVEL_API_URL', '')."package-detail?slug=".$slug."&client_id=".$client_id; 
			$packagedetail = $this->curlRequest($iurl,'GET','');
			return view('packdetails', compact(['packagedetail','dslug']));
		}
		
    }
	
	public function thanks(Request $request)
    {
		return view('thanks');
	}	
	public function enquiryContact(Request $request)
    {
		$requestdata =  $request->all();
		if($requestdata['captcha'] != $requestdata['code']){
			return json_encode(array('success'=> false,'message'=> 'Captcha Invalid'));
		}
		
		 $client_id = env('TRAVEL_CLIENT_ID', '');
		 $requestdata['client_id'] = env('TRAVEL_CLIENT_ID', '');
		$data_string = json_encode($requestdata);
			 $iurl = env('TRAVEL_API_URL', '')."enquiry-contact";
			$packagedetail = $this->curlRequest($iurl,'POST',$data_string);
			
			return $packagedetail;
		
    }
	
	public static function topInclusion($iInclusionid){
		$client_id = env('TRAVEL_CLIENT_ID', '');
		$iurl = env('TRAVEL_API_URL', '')."top-inclusion/".$iInclusionid."&client_id=".$client_id; 
		return (new static)->curlRequest($iurl,'GET','');
	}
	
	public static function themepachages($typeid){
		$client_id = env('TRAVEL_CLIENT_ID', '');
		$iurl = env('TRAVEL_API_URL', '')."type-by-package/".$typeid."?client_id=".$client_id; 
		return (new static)->curlRequest($iurl,'GET','');
	}
	
	public static function Inclusion($iInclusionid){
		$client_id = env('TRAVEL_CLIENT_ID', '');
		$iurl = env('TRAVEL_API_URL', '')."inclusion/".$iInclusionid."&client_id=".$client_id; 
		return (new static)->curlRequest($iurl,'GET','');
	}
	
	public static function Exclusion($iInclusionid){
		$client_id = env('TRAVEL_CLIENT_ID', '');
		$iurl = env('TRAVEL_API_URL', '')."exclusion/".$iInclusionid."&client_id=".$client_id; 
		return (new static)->curlRequest($iurl,'GET','');
	}
	
	
}
?>