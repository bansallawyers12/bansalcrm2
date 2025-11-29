<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; 
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;

use App\WebsiteSetting;
use App\SeoPage;
use App\Enquiry;

use Config;



class HomeController extends Controller
{
	public function __construct(Request $request)
    {	
		$siteData = WebsiteSetting::where('id', '!=', '')->first();
		\View::share('siteData', $siteData);
	}
	
    public function coming_soon()
    {
        return view('coming_soon');
    }
		
	public function index(Request $request)
    {		
        return view('index');
    }

	public function enquiry(Request $request)
    {		
        return view('enquiry');
    }
	public function store(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'first_name' => 'required|max:255',
										'last_name' => 'required|max:255',
										'email' => 'required|max:255',
										'phone' => 'required|max:255',
										'message' => 'required|max:255'
									  ]);
			$visaExpiry = '';
				$requestData 		= 	$request->all();
	        if($requestData['visa_expiry'] != ''){
	           $visaExpirys = explode('/', $requestData['visa_expiry']);
	          $visaExpiry = $visaExpirys[2].'-'.$visaExpirys[1].'-'. $visaExpirys[0]; 
	        }
		
			$obj				= 	new Enquiry;
			$obj->first_name	=	@$requestData['first_name'];
			$obj->last_name	    =	@$requestData['last_name'];			
			$obj->email	        =	@$requestData['email'];
			$obj->phone	        =	@$requestData['phone'];
			$obj->country	    =	@$requestData['country'];
			$obj->city	        =	@$requestData['city'];
			$obj->address	    =	@$requestData['address'];
			$obj->message	    =	@$requestData['message'];
			$obj->source	    =	@$requestData['source'];
			$obj->visa_expiry	=	$visaExpiry;
			$obj->cur_visa	    =	@$requestData['cur_visa'];
			$saved				=	$obj->save();  
			
			$objs				= 	Enquiry::find($obj->id);
			$saveds				=	$objs->save();  
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/enquiry')->with('success', 'Enquiry Added Successfully');
			}				
		}	

		return view('enquiry');	 
	}	
}
