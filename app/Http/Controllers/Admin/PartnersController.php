<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Models\Admin;
use App\Models\Partner;
use App\Models\Contact;
use App\Models\PartnerBranch;
use App\Models\Task;
use App\Models\TaskLog;
//use App\Models\ActivitiesLog;
 
use Auth; 
use Config;
use Illuminate\Support\Facades\Storage;
use PDF;

use Hfig\MAPI;
use Hfig\MAPI\OLE\Pear;
use Hfig\MAPI\Message\Msg;
use Hfig\MAPI\MapiMessageFactory;

use DateTime;
use DateTimeZone;
use App\Models\ActivitiesLog;
use App\Models\PartnerEmail;
use App\Models\PartnerPhone;

class PartnersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
	/**
     * All Vendors.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		//check authorization start	
			
			/* if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			} */	
		//check authorization end 
	
		 $query 		= Partner::where('id', '!=', '')->where('status',0); 
		 
		$totalData 	= $query->count();	//for all data
		if ($request->has('name')) 
		{
			$name 		= 	$request->input('name'); 
			if(trim($name) != '')
			{
				$query->where('partner_name', 'LIKE', '%'.$name.'%');
			}
		}
	
		 if ($request->has('email')) 
		{
			$email 		= 	$request->input('email'); 
			if(trim($email) != '')
			{
				$query->where('email', '=', $email);
			}
		}
		 if ($request->has('reginal_code')) 
		{
			$reginal_code 		= 	$request->input('reginal_code'); 
			if(trim($reginal_code) != '')
			{
				$query->where('reginal_code', '=', $reginal_code);
			}
		}

		if ($request->has('level')) 
		{
			$level 		= 	$request->input('level'); 
			if(trim($level) != '')
			{
				$query->where('level', 'LIKE', '%'.$level.'%');
			}
		}
	
		$lists = $query->withCount(['applications as student_count' => function ($query) {
            $query->whereIn('stage', ['Coe issued', 'Enrolled', 'Coe Cancelled']);
        }]);

        // Apply sorting based on request parameters
        if ($request->has('sort') && $request->has('direction')) {
            $sort = $request->input('sort');
            $direction = $request->input('direction');
            $lists = $lists->orderBy($sort, $direction);
        } else {
            // Default sorting by student_count descending
            $lists = $lists->orderByDesc('student_count')->sortable(['id' => 'desc']);
        }

        // Paginate the results
        $lists = $lists->paginate(config('constants.limit'));
        return view('Admin.partners.index',compact(['lists', 'totalData']));
	}
  
    public function inactivePartnerList(Request $request)
	{
		$query 	= Partner::where('id', '!=', '')->where('status',1);
        $totalData 	= $query->count();	//for all data
		if ($request->has('name'))
		{
			$name 		= 	$request->input('name');
			if(trim($name) != '')
			{
				$query->where('partner_name', 'LIKE', '%'.$name.'%');
			}
		}

		 if ($request->has('email'))
		{
			$email 		= 	$request->input('email');
			if(trim($email) != '')
			{
				$query->where('email', '=', $email);
			}
		}
		 if ($request->has('reginal_code'))
		{
			$reginal_code 		= 	$request->input('reginal_code');
			if(trim($reginal_code) != '')
			{
				$query->where('reginal_code', '=', $reginal_code);
			}
		}

		if ($request->has('level'))
		{
			$level 		= 	$request->input('level');
			if(trim($level) != '')
			{
				$query->where('level', 'LIKE', '%'.$level.'%');
			}
		}
      
        $lists = $query->withCount(['applications as student_count' => function ($query) {
            $query->whereIn('stage', ['Coe issued', 'Enrolled', 'Coe Cancelled']);
        }]);

        // Apply sorting based on request parameters
        if ($request->has('sort') && $request->has('direction')) {
            $sort = $request->input('sort');
            $direction = $request->input('direction');
            $lists = $lists->orderBy($sort, $direction);
        } else {
            // Default sorting by student_count descending
            $lists = $lists->orderByDesc('student_count')->sortable(['id' => 'desc']);
        }

        // Paginate the results
        $lists = $lists->paginate(config('constants.limit'));
        return view('Admin.partners.inactive',compact(['lists', 'totalData']));
    }
	
	public function create(Request $request)
	{
		//check authorization end
		//return view('Admin.users.create',compact(['usertype']));	
		
		return view('Admin.partners.create');	
	}
	
	
	
	public function store(Request $request)
	{
        //check authorization end
		if ($request->isMethod('post'))
		{
			$this->validate($request, [
                //'master_category' => 'required|max:255',
                //'partner_type' => 'required|max:255',
                'partner_name' => 'required|max:255',
                //'service_workflow' => 'required|max:255',
                //'currency' => 'required|max:255',
                //'email' => 'required|max:255'
                'partner_email' => 'required|array', // Validate that partner_email is an array
                'partner_email.*' => 'required|email|max:255' // Validate each email within the array
            ]);

			$requestData 		= 	$request->all();
			//echo '<pre>'; print_r($requestData);die;
            //echo $lastEmail = end($requestData['partner_email']); die;
			$obj				= 	new Partner;
			$obj->master_category	=	@$requestData['master_category'];
			$obj->partner_type	=	@$requestData['partner_type'];
			$obj->partner_name	=	@$requestData['partner_name'];
            $obj->legal_name	=	@$requestData['legal_name'];
			$obj->business_reg_no	=	@$requestData['business_reg_no'];
			$obj->service_workflow	=	@$requestData['service_workflow'];
			$obj->currency	=	@$requestData['currency'];
			$obj->address	=	@$requestData['address'];
			$obj->city	=	@$requestData['city'];
			$obj->state	=	@$requestData['state'];
			$obj->zip	=	@$requestData['zip'];
			$obj->country	=	@$requestData['country'];
			//$obj->country_code	=	@$requestData['country_code'];
			$obj->is_regional	=	@$requestData['is_regional'];
			//$obj->phone	=	@$requestData['phone'];
			//$obj->email	=	@$requestData['email'];
			$obj->fax	=	@$requestData['fax'];
			$obj->level = @$requestData['level'];
			$obj->website	=	@$requestData['website'];

			/* Profile Image Upload Function Start */
            if($request->hasfile('profile_img')) {
                $profile_img = $this->uploadFile($request->file('profile_img'), Config::get('constants.profile_imgs'));
            } else {
                $profile_img = NULL;
            }
			/* Profile Image Upload Function End */
			$obj->profile_img	=	@$profile_img;
			$saved				=	$obj->save();

            
			//Save to partner email table
            if(isset($requestData['partner_email']) && $requestData['partner_email'] != ''){
                $partner_email =  $requestData['partner_email'];
                $partner_email_type =  $requestData['partner_email_type'];
                for($ii=0; $ii< count($partner_email); $ii++){
                    $oe = new \App\Models\PartnerEmail;
                    $oe->user_id = @Auth::user()->id;
                    $oe->partner_id = @$obj->id;
                    $oe->partner_email_type = @$partner_email_type[$ii];
                    $oe->partner_email = @$partner_email[$ii];
                    $oe->created_at = date('Y-m-d H:i:s');
                    $oe->updated_at = date('Y-m-d H:i:s');
                    $oe->save();

                    if( isset($partner_email_type[$ii]) && $partner_email_type[$ii] == 'Personal'){
                        //Update partner  table
                        $partnerInfo = Partner::find($obj->id); // Retrieve the record by ID
                        //$lastEmail = end($requestData['partner_email']);
                        $lastEmail = $partner_email[$ii];;
                        $partnerInfo->email =  $lastEmail;
                        $partnerInfo->save(); // Save the changes
                    }
                }
            }


            //Save to partner phone table
            if(isset($requestData['partner_phone']) && $requestData['partner_phone'] != ''){
                $partner_phone_type =  $requestData['partner_phone_type'];
                $partner_phone =  $requestData['partner_phone'];
                $partner_country_code =  $requestData['partner_country_code'];
                for($iii=0; $iii< count($partner_phone); $iii++){
                    $oe1 = new \App\Models\PartnerPhone;
                    $oe1->user_id = @Auth::user()->id;
                    $oe1->partner_id = @$obj->id;
                    $oe1->partner_phone_type = @$partner_phone_type[$iii];
                    $oe1->partner_country_code = @$partner_country_code[$iii];
                    $oe1->partner_phone = @$partner_phone[$iii];
                    $oe1->created_at = date('Y-m-d H:i:s');
                    $oe1->updated_at = date('Y-m-d H:i:s');
                    $oe1->save();

                    if( isset($partner_phone_type[$iii]) && $partner_phone_type[$iii] == 'Personal'){
                        //Update partner  table
                        $partnerInfo1 = Partner::find($obj->id); // Retrieve the record by ID

                        //$lastPhone = end($requestData['partner_phone']);
                        //$lastPhoneCountryCode = end($requestData['partner_country_code']);

                        $lastPhone = $partner_phone[$iii];;
                        $lastPhoneCountryCode =  $partner_country_code[$iii];

                        $partnerInfo1->phone =  $lastPhone;
                        $partnerInfo1->country_code =  $lastPhoneCountryCode;
                        $partnerInfo1->save(); // Save the changes
                    }
                }
            }

           

			if(isset($requestData['branchname']) && $requestData['branchname'] != ''){
                $branchname =  $requestData['branchname'];
                $branchemail =  $requestData['branchemail'];
                $branchcountry =  $requestData['branchcountry'];
                $branchcity =  $requestData['branchcity'];
                $branchstate =  $requestData['branchstate'];
                $branchaddress =  $requestData['branchaddress'];
                $branchzip =  $requestData['branchzip'];
                $branchreg =  $requestData['branchreg'];
                $branchcountry_code =  $requestData['branchcountry_code'];
                $branchphone =  $requestData['branchphone'];
                for($i=0; $i< count($branchname); $i++){
                    $is_headoffice = 0;
                    if($i==0){
                        $is_headoffice = 1;
                    }
                    $o = new \App\Models\PartnerBranch;
                    $o->user_id = @Auth::user()->id;
                    $o->partner_id = @$obj->id;
                    $o->name = @$branchname[$i];
                    $o->email = @$branchemail[$i];
                    $o->country = @$branchcountry[$i];
                    $o->city = @$branchcity[$i];
                    $o->state = @$branchstate[$i];
                    $o->street = @$branchaddress[$i];
                    $o->zip = @$branchzip[$i];
                    $o->country_code = @$branchcountry_code[$i];
                    $o->phone = @$branchphone[$i];
                    $o->is_regional = @$branchreg[$i];
                    $o->is_headoffice = $is_headoffice;
                    $o->save();
                }
		    } else {
		        $is_headoffice = 1;
		        $o = new \App\Models\PartnerBranch;
				$o->user_id = @Auth::user()->id;
				$o->partner_id = @$obj->id;
				$o->name = 'Head Office';
				$o->email = @$requestData['email'];
				$o->country = @$requestData['country'];
				$o->city = @$requestData['city'];
				$o->state = @$requestData['state'];
				$o->street = @$requestData['address'];
				$o->zip = @$requestData['zip'];
				$o->country_code = @$requestData['country_code'];
				$o->phone =@$requestData['phone'];
				$o->is_regional =@$requestData['is_regional'];
				$o->is_headoffice = $is_headoffice;
				$o->save();
		    }
			if(!$saved) {
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			} else {
                return Redirect::to('/admin/partners')->with('success', 'Partners Added Successfully');
			}
		}
        return view('Admin.partners.create');
	}

	public function edit(Request $request, $id = NULL)
	{
        //check authorization end
        if ($request->isMethod('post'))
		{ //dd('ifff');
			$requestData 		= 	$request->all();
		    //echo '<pre>'; print_r($requestData); die;
			$this->validate($request, [
                //'master_category' => 'required|max:255',
                //'partner_type' => 'required|max:255',
                'partner_name' => 'required|max:255',
                //'service_workflow' => 'required|max:255',
                //'currency' => 'required|max:255',
                //'email' => 'required|max:255'
                'partner_email' => 'required|array', // Validate that partner_email is an array
                'partner_email.*' => 'required|email|max:255' // Validate each email within the array
            ]);

			$obj			= 	Partner::find(@$requestData['id']);

			//$obj->master_category	=	@$requestData['master_category'];
			$obj->partner_type	=	@$requestData['partner_type'];
			$obj->partner_name	=	@$requestData['partner_name'];
            $obj->legal_name	=	@$requestData['legal_name'];
			$obj->business_reg_no	=	@$requestData['business_reg_no'];
			$obj->service_workflow	=	@$requestData['service_workflow'];
			$obj->currency	=	@$requestData['currency'];
			$obj->address	=	@$requestData['address'];
			$obj->city	=	@$requestData['city'];
			$obj->state	=	@$requestData['state'];
			$obj->is_regional	=	@$requestData['is_regional'];
			$obj->zip	=	@$requestData['zip'];
			$obj->country	=	@$requestData['country'];
			//$obj->country_code	=	@$requestData['country_code'];
			//$obj->phone	=	@$requestData['phone'];
			//$obj->email	=	@$requestData['email'];
			$obj->fax	=	@$requestData['fax'];
			$obj->level = @$requestData['level'];
			$obj->website	=	@$requestData['website'];

			/* Profile Image Upload Function Start */
			if($request->hasfile('profile_img'))
			{
				/* Unlink File Function Start */
                if($requestData['profile_img'] != '')
                {
                    $this->unlinkFile($requestData['old_profile_img'], Config::get('constants.profile_imgs'));
                }
				/* Unlink File Function End */
                $profile_img = $this->uploadFile($request->file('profile_img'), Config::get('constants.profile_imgs'));
			}
			else
			{
				$profile_img = @$requestData['old_profile_img'];
			}
		    /* Profile Image Upload Function End */
			$obj->profile_img			=	@$profile_img;
			$saved							=	$obj->save();

            
            //////////////////////////////////////////////////////
            //////////Code Start For partner email////////////////
            //////////////////////////////////////////////////////
            //////////////////////////////////////////////////////
            //Update partner email table
            if(isset($requestData['rem_email'])){
                $rem_email =  @$requestData['rem_email'];
                for($irem_email=0; $irem_email< count($rem_email); $irem_email++){
                    if(\App\Models\PartnerEmail::where('id', $rem_email[$irem_email])->exists()){
                        \App\Models\PartnerEmail::where('id', $rem_email[$irem_email])->delete();
                    }
                }
            }

            if(isset($requestData['partner_email_type'])){
                $partner_email_type =  $requestData['partner_email_type'];
            } else {
                $partner_email_type = array();
            }

            if(isset($requestData['partner_email'])){
                $partner_email =  $requestData['partner_email'];
            } else {
                $partner_email = array();
            }

            if(count($partner_email) >0){
                for($ii=0; $ii< count($partner_email); $ii++){

                    if(\App\Models\PartnerEmail::where('id', $requestData['partneremailid'][$ii])->exists()){
                        $os = \App\Models\PartnerEmail::find($requestData['partneremailid'][$ii]);
                        $os->user_id = @Auth::user()->id;
                        $os->partner_id = @$obj->id;
                        $os->partner_email_type = @$partner_email_type[$ii];
                        $os->partner_email = @$partner_email[$ii];
                        $os->updated_at = date('Y-m-d H:i:s');
                        $os->save();
                    }else{
                        $oe = new \App\Models\PartnerEmail;
                        $oe->user_id = @Auth::user()->id;
                        $oe->partner_id = @$obj->id;
                        $oe->partner_email_type = @$partner_email_type[$ii];
                        $oe->partner_email = @$partner_email[$ii];
                        $oe->created_at = date('Y-m-d H:i:s');
                        $oe->updated_at = date('Y-m-d H:i:s');
                        $oe->save();
                    }

                    if( isset($partner_email_type[$ii]) && $partner_email_type[$ii] == 'Personal'){
                        //Update partner  table
                        $partnerInfo = Partner::find($obj->id); // Retrieve the record by ID
                        //$lastEmail = end($requestData['partner_email']);
                        $lastEmail = $partner_email[$ii];;
                        $partnerInfo->email =  $lastEmail;
                        $partnerInfo->save(); // Save the changes
                    }
                }
            }

            //////////////////////////////////////////////////////
            //////////Code End For partner email////////////////
            //////////////////////////////////////////////////////
            //////////////////////////////////////////////////////


            //////////////////////////////////////////////////////
            //////////Code Start For partner phone////////////////
            //////////////////////////////////////////////////////
            //////////////////////////////////////////////////////
            //Update partner phone table
            if(isset($requestData['rem_phone'])){
                $rem_phone =  @$requestData['rem_phone'];
                for($irem_phone=0; $irem_phone< count($rem_phone); $irem_phone++){
                    if(\App\Models\PartnerPhone::where('id', $rem_phone[$irem_phone])->exists()){
                        \App\Models\PartnerPhone::where('id', $rem_phone[$irem_phone])->delete();
                    }
                }
            }

            if(isset($requestData['partner_phone_type'])){
                $partner_phone_type =  $requestData['partner_phone_type'];
            } else {
                $partner_phone_type = array();
            }

            if(isset($requestData['partner_phone'])){
                $partner_phone =  $requestData['partner_phone'];
            } else {
                $partner_phone = array();
            }

            if(isset($requestData['partner_country_code'])){
                $partner_country_code =  $requestData['partner_country_code'];
            } else {
                $partner_country_code = array();
            }

            if(count($partner_phone) >0){
                for($iii=0; $iii< count($partner_phone); $iii++){

                    if(\App\Models\PartnerPhone::where('id', $requestData['partnerphoneid'][$iii])->exists()){
                        $os1 = \App\Models\PartnerPhone::find($requestData['partnerphoneid'][$iii]);
                        $os1->user_id = @Auth::user()->id;
                        $os1->partner_id = @$obj->id;
                        $os1->partner_phone_type = @$partner_phone_type[$iii];
                        $os1->partner_country_code = @$partner_country_code[$iii];
                        $os1->partner_phone = @$partner_phone[$iii];
                        $os1->updated_at = date('Y-m-d H:i:s');
                        $os1->save();
                    }else{
                        $oe1 = new \App\Models\PartnerPhone;
                        $oe1->user_id = @Auth::user()->id;
                        $oe1->partner_id = @$obj->id;
                        $oe1->partner_phone_type = @$partner_phone_type[$iii];
                        $oe1->partner_country_code = @$partner_country_code[$iii];
                        $oe1->partner_phone = @$partner_phone[$iii];
                        $oe1->created_at = date('Y-m-d H:i:s');
                        $oe1->updated_at = date('Y-m-d H:i:s');
                        $oe1->save();
                    }

                    if( isset($partner_phone_type[$iii]) && $partner_phone_type[$iii] == 'Personal'){
                        //Update partner  table
                        $partnerInfo1 = Partner::find($obj->id); // Retrieve the record by ID

                        //$lastPhone = end($requestData['partner_phone']);
                        //$lastPhoneCountryCode = end($requestData['partner_country_code']);

                        $lastPhone = $partner_phone[$iii];;
                        $lastPhoneCountryCode =  $partner_country_code[$iii];

                        $partnerInfo1->phone =  $lastPhone;
                        $partnerInfo1->country_code =  $lastPhoneCountryCode;
                        $partnerInfo1->save(); // Save the changes
                    }
                }
            }

            //////////////////////////////////////////////////////
            //////////Code End For partner phone////////////////
            //////////////////////////////////////////////////////
            //////////////////////////////////////////////////////

           

            if(isset($requestData['rem'])){
                $rem =  @$requestData['rem'];
                for($irem=0; $irem< count($rem); $irem++){
                    if(\App\Models\PartnerBranch::where('id', $rem[$irem])->exists()){
                        \App\Models\PartnerBranch::where('id', $rem[$irem])->delete();
                    }
                }
            }
            if(isset($requestData['branchname'])){
                $branchname =  $requestData['branchname'];
            } else {
                $branchname = array();
            }

            if(isset($requestData['branchemail'])){
                $branchemail =  $requestData['branchemail'];
            }

            if(isset($requestData['branchcountry'])){
                $branchcountry =  $requestData['branchcountry'];
            }

            if(isset($requestData['branchcity'])){
                $branchcity =  $requestData['branchcity'];
            }

			if(isset($requestData['branchstate'])){
                $branchstate =  $requestData['branchstate'];
            }

            if(isset($requestData['branchaddress'])){
                $branchaddress =  $requestData['branchaddress'];
            }

            if(isset($requestData['branchzip'])){
                $branchzip =  $requestData['branchzip'];
            }

            if(isset($requestData['branchreg'])){
                $branchreg =  $requestData['branchreg'];
            }

            if(isset($requestData['branchcountry_code'])){
                $branchcountry_code =  $requestData['branchcountry_code'];
            }

            if(isset($requestData['branchphone'])){
                $branchphone =  $requestData['branchphone'];
            }

            if(count($branchname) >0){
                for($i=0; $i< count($branchname); $i++){
                    $is_headoffice = 0;
                    if($i==0){
                        $is_headoffice = 1;
                    }
                    if(\App\Models\PartnerBranch::where('id', $requestData['branchid'][$i])->exists()){
                        $os = \App\Models\PartnerBranch::find($requestData['branchid'][$i]);

                        $os->name = @$branchname[$i];
                        $os->email = @$branchemail[$i];
                        $os->country = @$branchcountry[$i];
                        $os->city = @$branchcity[$i];
                        $os->state = @$branchstate[$i];
                        $os->street = @$branchaddress[$i];
                        $os->zip = @$branchzip[$i];
                        $os->country_code = @$branchcountry_code[$i];
                        $os->phone = @$branchphone[$i];
                        $os->is_regional = @$branchreg[$i];

                        $os->save();
                    }else{
                        $o = new \App\Models\PartnerBranch;
                        $o->user_id = @Auth::user()->id;
                        $o->partner_id = @$obj->id;
                        $o->name = @$branchname[$i];
                        $o->email = @$branchemail[$i];
                        $o->country = @$branchcountry[$i];
                        $o->city = @$branchcity[$i];
                        $o->state = @$branchstate[$i];
                        $o->street = @$branchaddress[$i];
                        $o->zip = @$branchzip[$i];
                        $o->country_code = @$branchcountry_code[$i];
                        $o->phone = @$branchphone[$i];
                        $o->is_regional = @$branchreg[$i];
                        $o->is_headoffice = $is_headoffice;
                        $o->save();
                    }
                }
            }

			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}

			else
			{
				return Redirect::to('/admin/partners')->with('success', 'Partners Edited Successfully');
			}
		}

		else
		{ //dd('elseee');
			if(isset($id) && !empty($id))
			{
                $id = $this->decodeString($id);
				if(Partner::where('id', '=', $id)->exists())
				{
					$fetchedData = Partner::find($id); //dd($fetchedData);

                    //Check email record is exist in partner email table
                    if( \App\Models\PartnerEmail::where('partner_id', $id)->doesntExist()  && $fetchedData->email != ""){
                        $oef = new \App\Models\PartnerEmail;
                        $oef->user_id = @Auth::user()->id;
                        $oef->partner_id = $id;
                        $oef->partner_email = $fetchedData->email;
                        $oef->created_at = date('Y-m-d H:i:s');
                        $oef->updated_at = date('Y-m-d H:i:s');
                        $oef->save();
                    }

                    //Check phone record is exist in partner phone table
                    if( \App\Models\PartnerPhone::where('partner_id', $id)->doesntExist()  && $fetchedData->phone != ""){
                        $oef1 = new \App\Models\PartnerPhone;
                        $oef1->user_id = @Auth::user()->id;
                        $oef1->partner_id = $id;
                        $oef1->partner_country_code = $fetchedData->country_code;
                        $oef1->partner_phone = $fetchedData->phone;
                        $oef1->created_at = date('Y-m-d H:i:s');
                        $oef1->updated_at = date('Y-m-d H:i:s');
                        $oef1->save();
                    }
					return view('Admin.partners.edit', compact(['fetchedData']));
				}
				else
				{
					return Redirect::to('/admin/partners')->with('error', 'Partners Not Exist');
				}
			}
			else
			{
				return Redirect::to('/admin/partners')->with('error', Config::get('constants.unauthorized'));
			}
		}

	}
	
	
	
	public function getpaymenttype(Request $request){
		$catid = $request->cat_id;
		$lists = \App\Models\PartnerType::where('category_id', $catid)->orderby('name','ASC')->get();
		ob_start();
		?>
		<option value="">Select a Partner Type</option>
		<?php
		foreach($lists as $list){
			?>
			<option value="<?php echo $list->id; ?>"><?php echo $list->name; ?></option>
			<?php
		}
		echo ob_get_clean();
	}
	
	public function detail(Request $request, $id = NULL){
		if(isset($id) && !empty($id))  
			{				
				$id = $this->decodeString($id);	
				if(Partner::where('id', '=', $id)->exists()) 
				{ 
					$fetchedData = Partner::find($id);
                  
                    //Check email record is exist in partner email table
                    if( \App\Models\PartnerEmail::where('partner_id', $id)->doesntExist()  && $fetchedData->email != ""){
                        $oef = new \App\Models\PartnerEmail;
                        $oef->user_id = @Auth::user()->id;
                        $oef->partner_id = $id;
                        $oef->partner_email = $fetchedData->email;
                        $oef->created_at = date('Y-m-d H:i:s');
                        $oef->updated_at = date('Y-m-d H:i:s');
                        $oef->save();
                    }

                    //Check phone record is exist in partner phone table
                    if( \App\Models\PartnerPhone::where('partner_id', $id)->doesntExist()  && $fetchedData->phone != ""){
                        $oef1 = new \App\Models\PartnerPhone;
                        $oef1->user_id = @Auth::user()->id;
                        $oef1->partner_id = $id;
                        $oef1->partner_country_code = $fetchedData->country_code;
                        $oef1->partner_phone = $fetchedData->phone;
                        $oef1->created_at = date('Y-m-d H:i:s');
                        $oef1->updated_at = date('Y-m-d H:i:s');
                        $oef1->save();
                    }
                  
					return view('Admin.partners.detail', compact(['fetchedData']));
				}
				else 
				{  
					return Redirect::to('/admin/partners')->with('error', 'Partners Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/partners')->with('error', Config::get('constants.unauthorized'));
			}
	}
	
	public function getrecipients(Request $request){
		$squery = $request->q;
		if($squery != ''){
			
			 $partners = \App\Models\Partner::where('id', '!=', '')
      
       ->where(
           function($query) use ($squery) {
             return $query
                    ->where('email', 'LIKE', '%'.$squery.'%')
                    ->orwhere('partner_name', 'LIKE','%'.$squery.'%');
            })
            ->get();
			
			$items = array();
			foreach($partners as $partner){
				$items[] = array('name' => $partner->partner_name,'email'=>$partner->email,'status'=>'Partner','id'=>$partner->id,'cid'=>base64_encode(convert_uuencode(@$partner->id)));
			}
			
			echo json_encode(array('items'=>$items));
		}
	}
	
	
	public function getallpartners(Request $request){
		$squery = $request->q;
		if($squery != ''){
			
			 $partners = \App\Models\Partner::where('id', '!=', '')
       
       ->where( 
           function($query) use ($squery) {
             return $query
                    ->where('email', 'LIKE', '%'.$squery.'%')
                    ->orwhere('partner_name', 'LIKE','%'.$squery.'%');
            })
            ->get();
			
			$items = array();
			foreach($partners as $partner){ 
				$items[] = array('name' => $partner->partner_name,'email'=>$partner->email,'status'=>'Partner','id'=>$partner->id,'cid'=>base64_encode(convert_uuencode(@$partner->id)));
			}
			
			echo json_encode(array('items'=>$items));
		}
	}
	
	public function saveagreement(Request $request){ //dd($request->all());
		if(Partner::where('id', '=', $request->partner_id)->exists()) 
		{
			$obj = Partner::find($request->partner_id);
            $obj->contract_start = $request->contract_start;
			$obj->contract_expiry = $request->contract_expiry;
			if(isset($request->represent_region) && !empty($request->represent_region)){
				$obj->represent_region = implode(',',$request->represent_region);
			}
			if(isset($request->gst)){
				$obj->gst = 1;
			}else{
				$obj->gst = 0;
			}
			$obj->commission_percentage = $request->commission_percentage;
			$obj->default_super_agent = $request->default_super_agent;
          
            if ($request->hasfile('file_upload')) {
                if(!is_array($request->file('file_upload'))){
                    $files[] = $request->file('file_upload');
                } else {
                    $files = $request->file('file_upload');
                }
                foreach ($files as $file) {
                    $size = $file->getSize();
                    $fileName = $file->getClientOriginalName();
                    $explodeFileName = explode('.', $fileName);
                    $document_upload = $this->uploadrenameFile($file, Config::get('constants.documents'));
                    $exploadename = explode('.', $document_upload);
                    $obj->file_upload = $document_upload; //$explodeFileName[0];
                }
            }
          
			$saved = $obj->save();
			if($saved){
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully saved your partner agreement’s information.';
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Record Not Found';
		}
		
		 echo json_encode($response);
	}
	
	public function createcontact(Request $request){
		if(isset($request->contact_id) && $request->contact_id != ''){
			$obj = Contact::find($request->contact_id);
		}else{
			$obj = new Contact;
		}
		$obj->name 				= $request->name;
		$obj->contact_email 	= $request->email;
		$obj->contact_phone 	= $request->phone;
		$obj->department 		= $request->department;
		$obj->branch 			= $request->branch;
		$obj->fax 				= $request->fax;
		$obj->position 			= $request->position;
		$obj->primary_contact 	= $request->primary_contact;
		$obj->user_id 			= $request->client_id;
		$obj->countrycode 		= $request->country_code;
		$saved = $obj->save();
		
		if($saved){
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully saved your contact.';
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
			
			echo json_encode($response);
	}
	
	public function getcontacts(Request $request){
		$querycontactlist = \App\Models\Contact::where('user_id', $request->clientid)->orderby('created_at', 'DESC');
		$contactlistcount = $querycontactlist->count();
		$contactlist = $querycontactlist->get();
		if($contactlistcount !== 0){
			foreach($contactlist as $clist){
				$branch = \App\Models\PartnerBranch::where('id', $clist->branch)->first();
				?>
				<div class="note_col" id="contact_<?php echo $clist->id; ?>" style="width:33.33333333%"> 
					<div class="note_content">
						<h4><?php echo $clist->name; ?></h4>
						<p><span class="text-semi-bold"><?php if($clist->position != ''){ echo $clist->position; }else{ echo '-'; } ?></span> In <span class="text-semi-bold"><?php if($clist->department != ''){ echo $clist->department; }else{ echo '-'; } ?></span></p>
						<div class="" style="margin-top: 15px!important;">
							<p><i class="fa fa-phone"></i> <?php if($clist->contact_phone != ''){ echo $clist->contact_phone; }else{ echo '-'; } ?></p>
							<p style="margin-top: 5px!important;"><i class="fa fa-fax"></i> <?php if($clist->fax != ''){ echo $clist->fax; }else{ echo '-'; } ?></p>
							<p style="margin-top: 5px!important;"><i class="fa fa-mail"></i> <?php if($clist->contact_email != ''){ echo $clist->contact_email; }else{ echo '-'; } ?></p>
						</div>
					</div>
					<div class="extra_content">
						<div class="left">
							<i class="fa fa-map-marker" style="margin-right: 20px!important;"></i>
							<?php echo $branch->city; ?>, <?php echo $branch->country; ?>
						</div>  
						<div class="right">
							<div class="dropdown d-inline dropdown_ellipsis_icon">
								<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
								<div class="dropdown-menu">
									<a class="dropdown-item opencontactform" data-id="<?php echo $clist->id; ?>" href="javascript:;">Edit</a>
									<a data-id="<?php echo $clist->id; ?>" data-href="deletecontact" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
									
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
		}else{
			
		}
	}
	
	
	public function deletecontact(Request $request){
		$note_id = $request->note_id;
		if(\App\Models\Contact::where('id',$note_id)->exists()){
			$res = DB::table('contacts')->where('id', @$note_id)->delete();
			if($res){
				
			$response['status'] 	= 	true;
			}else{
				$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
	
	public function getcontactdetail(Request $request){
		$note_id = $request->note_id;
		if(\App\Models\Contact::where('id',$note_id)->exists()){
			$data = \App\Models\Contact::select('name','contact_email','contact_phone','department','branch','fax','position','primary_contact','countrycode')->where('id',$note_id)->first();
			$response['status'] 	= 	true;
			$response['data']	=	$data;
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
	
	
	public function createbranch(Request $request){
		if(isset($request->branch_id) && $request->branch_id != ''){
			$obj = PartnerBranch::find($request->branch_id);
		}else{
			$obj = new PartnerBranch;
		}
		$obj->user_id 			= Auth::user()->id;
		$obj->partner_id 				= $request->client_id;
		$obj->name 	= $request->name;
		$obj->email 	= $request->email;
		$obj->country 		= $request->country;
		$obj->city 			= $request->city;
		$obj->state 				= $request->state;
		$obj->street 			= $request->street;
		$obj->country_code 	= $request->country_code;
		$obj->phone 			= $request->phone;
		$obj->is_headoffice 		= $request->head_office;
		$obj->zip 		= $request->zip_code;
		$saved = $obj->save();
		
		if($saved){
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully saved your contact.';
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
			
			echo json_encode($response);
	}
	
	public function getbranch(Request $request){
		$branchesquery = PartnerBranch::where('partner_id', $request->clientid)->orderby('created_at', 'DESC');
		$branchescount = $branchesquery->count();
		$branches = $branchesquery->get();
		if($branchescount !== 0){
		
			foreach($branches as $branch){
		
				?>
				<div class="branch_col" id="contact_"> 
					<div class="branch_content">
						<h4><?php echo $branch->name; ?></h4>
						<div class="" style="margin-top: 15px!important;">
							<p><i class="fa fa-map-marker-alt" style="margin-right: 10px!important;"></i> <?php echo $branch->city; ?>, <?php echo $branch->a; ?></p>
						</div>
					</div>
					<div class="extra_content">
						<div class="left">
							<p><i class="fa fa-phone" style="margin-right: 20px!important;"></i> <?php if($branch->phone != ''){ echo $branch->phone; }else{ echo '-'; } ?></p>
							<p><i class="fa fa-envelope-o" style="margin-right: 20px!important;"></i> <?php if($branch->email != ''){ echo $branch->email; }else{ echo '-'; } ?></p>
						</div>  
						<div class="right">
							<div class="dropdown d-inline dropdown_ellipsis_icon">
								<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
								<div class="dropdown-menu">
									<a class="dropdown-item openbranchform" data-id="{{$branch->id}}" href="javascript:;">Edit</a>
									<a data-id="{{$branch->id}}" data-href="deletebranch" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
		}else{
			
		}
	}
	
	public function getbranchdetail(Request $request){
		$note_id = $request->note_id;
		if(\App\Models\PartnerBranch::where('id',$note_id)->exists()){
			$data = \App\Models\PartnerBranch::where('id',$note_id)->first();
			$response['status'] 	= 	true;
			$response['data']	=	$data;
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
	
	public function deletebranch(Request $request){
		$note_id = $request->note_id;
		if(\App\Models\PartnerBranch::where('id',$note_id)->exists()){
			$res = DB::table('partner_branches')->where('id', @$note_id)->delete();
			if($res){
				
			$response['status'] 	= 	true;
			}else{
				$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
	
	public function addtask(Request $request){
		$obj = \App\Models\Appointment::find($request->id);
		if($obj){
			?>
			<form method="post" action="<?php echo \URL::to('/admin/editappointment'); ?>" name="editpartnerappointment" id="editpartnerappointment" autocomplete="off" enctype="multipart/form-data">
				 
				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
				<input type="hidden" name="client_id" value="<?php echo $obj->client_id; ?>">
				<input type="hidden" name="id" value="<?php echo $obj->id; ?>">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label style="display:block;" for="related_to">Related to:</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="client" value="Client" name="related_to" checked>
									<label class="form-check-label" for="client">Client</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="partner" value="Partner" name="related_to">
									<label class="form-check-label" for="partner">Partner</label>
								</div>
								<span class="custom-error related_to_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label style="display:block;" for="related_to">Added by:</label>
								<span><?php echo @Auth::user()->first_name; ?></span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="client_name">Client Name <span class="span_req">*</span></label>
								<input type="text" name="client_name" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Client Name" readonly value="<?php echo $obj->partners->partner_name; ?>">
								
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="timezone">Timezone <span class="span_req">*</span></label>
								<select class="form-control select2" name="timezone" data-valid="required">
									<option value="">Select Timezone</option>
									<?php
									$timelist = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
									foreach($timelist as $tlist){
										?>
										<option value="<?php echo $tlist; ?>" <?php if($obj->timezone == $tlist){ echo 'selected'; } ?>><?php echo $tlist; ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-7 col-lg-7">
							<div class="form-group"> 
								<label for="appoint_date">Date</label>
								<div class="input-group">
									<div class="input-group-prepend"> 
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<input type="text" name="appoint_date" class="form-control datepicker" data-valid="required" autocomplete="off" placeholder="Select Date" readonly value="<?php echo $obj->date; ?>">
								
								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error appoint_date_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-5 col-lg-5">
							<div class="form-group"> 
								<label for="appoint_time">Time</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-clock"></i>
										</div>
									</div>
									<input type="text" name="appoint_time" class="form-control timepicker" data-valid="required" autocomplete="off" placeholder="Select Date" readonly value="<?php echo $obj->time; ?>">
									
								</div>
								<span class="custom-error appoint_time_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="title">Title <span class="span_req">*</span></label>
								<input type="text" name="title" class="form-control " data-valid="required" autocomplete="off" placeholder="Enter Title"  value="<?php echo $obj->title; ?>">
								
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="description">Description</label>
								<textarea class="form-control" name="description" placeholder="Description"><?php echo $obj->description; ?></textarea>
								<span class="custom-error description_error" role="alert">
									<strong></strong>
								</span> 
							</div>	
						</div>	
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="invitees">Invitees</label>
								<select class="form-control select2" name="invitees">
									<option>Select Invitees</option>
									<option>Option 1</option>
									<option>Option 2</option>
									<option>Option 3</option> 
								</select>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('editpartnerappointment')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form> 
			<?php
		}else{
			?>
			Record Not Found
			<?php
		}
	}
	
	public function addtask(Request $request){
		$requestData 		= 	$request->all();
			 if($request->hasfile('attachments')) 
			{	
				$attachfile = $this->uploadFile($request->file('attachments'), Config::get('constants.invoice'));
			}
			else
			{
				$attachfile = Null;
			}
			$obj				= 	new Task; 
			$obj->title			=	@$requestData['title'];
			$obj->category		=	@$requestData['category'];
			$obj->assignee		=	@$requestData['assignee'];
			$obj->priority		=	@$requestData['priority'];
			$obj->due_date		=	@$requestData['due_date']; 
			$obj->due_time		=	@$requestData['due_time']; 
			$obj->description	=	@$requestData['description'];
			$obj->related_to	=	@$requestData['related_to'];
			if(isset($requestData['contact_name']) && !empty($requestData['contact_name'])){
			$obj->contact_name	=	implode(',',@$requestData['contact_name']);
			}
			$obj->partner_name	=	@$requestData['partner_name'];
			$obj->client_name	=	@$requestData['client_name'];
			$obj->application	=	@$requestData['application'];
			$obj->stage			=	@$requestData['stage'];
			$obj->followers		=	@$requestData['followers'];
			$obj->attachments	=	@$requestData['attachfile'];
			$obj->user_id		=	Auth::user()->id;
			$obj->type			=	'partner';
			$obj->client_id	=	@$requestData['partnerid'];
			$saved				=	$obj->save();  
			
			
			if(!$saved)
			{
				$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
			}
			else
			{
				$objtask = new TaskLog;
				$objtask->task_id 		= $obj->id;
				$objtask->created_by 	= Auth::user()->id;
				$objtask->title = 'created a task';
				$saved				=	$objtask->save();  
				
				$response['status'] 	= 	true;
				$response['message']	=	'Task Created Successfully';
			}	
			echo json_encode($response);
	}
	
	public function gettasks(Request $request){
		$client_id = $request->clientid;
		
		$notelist = \App\Models\Task::where('client_id',$client_id)->where('type','partner')->orderby('created_at', 'DESC')->get();
		ob_start();
		foreach($notelist as $alist){
			$admin = \App\Models\Admin::where('id', $alist->user_id)->first();
			?>
			<tr class="opentaskview" style="cursor:pointer;" id="<?php echo $alist->id; ?>">
				<td></td> 
				<td><b><?php echo $alist->category; ?></b>: <?php echo $alist->title; ?></td>
				<td><span class="author-avtar" style="font-size: .8rem;height: 24px;line-height: 24px;width: 24px;min-width: 24px;background: rgb(3, 169, 244);"><?php echo substr($admin->first_name, 0, 1); ?></span></td>
				<td><?php echo $alist->priority; ?></td> 
				<td><i class="fa fa-clock"></i> <?php echo $alist->due_date; ?> <?php echo $alist->due_time; ?></td>
				<td><?php
				if($alist->status == 3){
					echo '<span style="color: rgb(113, 204, 83); width: 84px;">Completed</span>';
				}else if($alist->status == 1){
					echo '<span style="color: rgb(3, 169, 244); width: 84px;">In Progress</span>';
				}else if($alist->status == 2){
					echo '<span style="color: rgb(156, 156, 156); width: 84px;">On Review</span>';
				}else{
					echo '<span style="color: rgb(255, 173, 0); width: 84px;">Todo</span>';
				}
				?></td> 
				
			</tr>
			<?php
		}
		return ob_get_clean();
	}
	
	public function taskdetail(Request $request){
		$notedetail = \App\Models\Task::where('id',$request->task_id)->where('type','partner')->first();
		?>
		<div class="modal-header">
				<h5 class="modal-title" id="taskModalLabel"><i class="fa fa-bag"></i> <?php echo $notedetail->title; ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Status:</label>
						<?php
						if($notedetail->status == 0){
					$comment = 'Todo';
				}else if($notedetail->status == 1){
					$comment = 'In Progress';
				}else if($notedetail->status == 2){
					$comment = 'On Review';
				}else if($notedetail->status == 3){
					$comment = 'Completed';
				}
						?>
						<ul class="navbar-nav navbar-right">
							<li class="dropdown dropdown-list-toggle">
								<a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle taskstatus"><?php echo $comment; ?> <i class="fa fa-angle-down"></i></a>
								 <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
									<a href="javascript:;" data-statusname="To Do" data-status="0" data-id="<?php echo $notedetail->id; ?>" class="dropdown-item changetaskstatus">
										To Do
									</a>
									<a href="javascript:;" data-statusname="In Progress" data-status="1" data-id="<?php echo $notedetail->id; ?>" class="dropdown-item changetaskstatus">
										In Progress
									</a>
									<a href="javascript:;" data-statusname="On Review" data-status="2"  data-id="<?php echo $notedetail->id; ?>" class="dropdown-item changetaskstatus">
										On Review
									</a>
									<a href="javascript:;" data-statusname="Completed" data-status="3"  data-id="<?php echo $notedetail->id; ?>" class="dropdown-item changetaskstatus">
										Completed
									</a>
								 </div>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Priority:</label>
						
						<ul class="navbar-nav navbar-right">
							<li class="dropdown dropdown-list-toggle">
								<a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle prioritystatus"><?php echo $notedetail->priority; ?> <i class="fa fa-angle-down"></i></a>
								 <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
									<a href="javascript:;" data-statusname="Low" data-id="<?php echo $notedetail->id; ?>" class="dropdown-item changeprioritystatus">
										Low
									</a>
									<a href="javascript:;" data-statusname="Normal" data-id="<?php echo $notedetail->id; ?>" class="dropdown-item changeprioritystatus">
										Normal
									</a>
									<a href="javascript:;" data-statusname="High" data-id="<?php echo $notedetail->id; ?>" class="dropdown-item changeprioritystatus">
										High
									</a>
									<a href="javascript:;" data-statusname="Urgent" data-id="<?php echo $notedetail->id; ?>" class="dropdown-item changeprioritystatus">
										Urgent
									</a>
								 </div>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Category:</label>
						<br>
						<span><?php echo $notedetail->category; ?></span>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Due Date:</label>
						<br>
						<span><?php echo $notedetail->due_date; ?></span>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Due Time:</label>
						<br>
						<span><?php echo date('h:i A', strtotime($notedetail->due_time)); ?></span>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
					<?php
					$admindetail = \App\Models\Admin::where('id',$notedetail->assignee)->first();
					?>
						<label for="title">Assignee:</label>
						<br>
						<div style="display: flex;">
						<span  title="Arun " class="col-hr-1 ag-avatar ag-avatar--xs" style="position: relative; background: rgb(3, 169, 244);color: #fff;display: block;
    font-weight: 600;
    letter-spacing: 1px;
    text-align: center;
    border-radius: 50%;
    overflow: hidden;
    font-size: .8rem;
    height: 24px;
    line-height: 24px;
    min-width: 24px;
    width: 24px;
}"><b > <?php echo substr($admindetail->first_name, 0, 1); ?></b></span>
						<span><?php echo $admindetail->first_name; ?></span>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Followers:</label>
						<br>
						<?php
					$admindetailfollowers = \App\Models\Admin::where('id',$notedetail->followers)->first();
					if($admindetailfollowers){
					?>
						<div style="display: flex;">
						<span  title="Arun " class="col-hr-1 ag-avatar ag-avatar--xs" style="position: relative; background: rgb(3, 169, 244);color: #fff;display: block;
    font-weight: 600;
    letter-spacing: 1px;
    text-align: center;
    border-radius: 50%;
    overflow: hidden;
    font-size: .8rem;
    height: 24px;
    line-height: 24px;
    min-width: 24px;
    width: 24px;
}"><b > <?php echo substr($admindetailfollowers->first_name, 0, 1); ?></b></span>
						<span><?php echo $admindetailfollowers->first_name; ?></span>
						</div>
<?php } ?>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Added By:</label>
						<br>
						<?php
					$admindetailadded = \App\Models\Admin::where('id',$notedetail->user_id)->first();
					?>
						<div style="display: flex;">
						<span  title="Arun " class="col-hr-1 ag-avatar ag-avatar--xs" style="position: relative; background: rgb(3, 169, 244);color: #fff;display: block;
    font-weight: 600;
    letter-spacing: 1px;
    text-align: center;
    border-radius: 50%;
    overflow: hidden;
    font-size: .8rem;
    height: 24px;
    line-height: 24px;
    min-width: 24px;
    width: 24px;
}"><b > <?php echo substr($admindetailadded->first_name, 0, 1); ?></b></span>
						<span><?php echo $admindetailadded->first_name; ?></span>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Description:</label>
						<br>
						<span><?php echo $notedetail->description; ?></span>
					</div>
				</div>
				<div class="col-12 col-md-12 col-lg-12">
					<div class="form-group">
						<label for="title">Related to:</label>
						<br>
						<span><?php echo $notedetail->related_to; ?></span>
					</div>
				</div>
				
				<div class="col-12 col-md-12 col-lg-12">
					<div class="form-group">
						<label for="title">Comments:</label>
						<textarea id="comment" class="form-control" name="comment" placeholder="Enter comment here"></textarea>
						<span class="comment-error" style="color:#9f3a38"></span>
					</div>
				</div>
				<div class="col-12 col-md-12 col-lg-12">
					<div class="form-group">
					<input type="hidden" id="taskid" value="<?php echo $notedetail->id; ?>">
						<button class="btn btn-primary savecomment" >Save</button>
					</div>
				</div>
				
				<div class="col-12 col-md-12 col-lg-12" style="background-color: #f2f2f2;padding: 1rem 2rem 2rem;">
					<h4>Logs</h4>
					<div class="ag-flex ag-flex-column tasklogs">
						<?php
						$datas = TaskLog::where('task_id', $notedetail->id)->orderby('created_at','DESC')->get();
							foreach($datas as $data){
								$admindetailcreated_by = \App\Models\Admin::where('id',$data->created_by)->first();
								?>
							
								<div  class="task-log-item text-semi-light-grey col-v-1" style="margin-top: 5px!important;background-color: #fff;
    border-radius: 4px;
    box-shadow: 0 1px 1px 0 rgb(0 0 0 / 10%);
    padding: 7px 15px;">
									<div class="ag-flex">
										<span  title="Arun " class="col-hr-2 ag-avatar ag-avatar--xs" style="position: relative; background: rgb(3, 169, 244);font-size: .8rem;
    height: 24px;
    line-height: 24px;
    min-width: 24px;
    width: 24px;    color: #fff;
    display: block;
    font-weight: 600;
    letter-spacing: 1px;
    text-align: center;
    border-radius: 50%;
    overflow: hidden;    margin-right: 10px!important;"><b ><?php echo substr($admindetailcreated_by->first_name, 0, 1); ?></b></span> 
										<div style="flex: 1;" class="ag-flex ag-flex-column ag-flex-1 task-activity-content">
											<div  class="ag-flex ag-space-between ag-align-start">
												<div class="ag-flex ag-align-start">
													<strong class="text-info col-hr-1 ag-flex-shrink-0"> <?php echo $admindetailcreated_by->first_name; ?>  </strong> <?php echo $data->title; ?>
												</div> 
												<span class="text-semi-bold ag-flex-shrink-0"> <?php echo date('Y-m-d h:i A', strtotime($data->created_at)); ?></span>
											</div> 
											<?php if($data->message != ''){ ?>
											<p class="col-v-2 text-semi-light-grey task-activity-block"><?php echo $data->message; ?></p>
											<?php } ?>
										</div>
									</div>
								</div>
								
								<?php
							}
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	
	public function savecomment(Request $request){
		$obj = new TaskLog;
		$obj->title = 'commented';
		$obj->message = $request->comment;
		$obj->task_id = $request->taskid;
		$obj->created_by = Auth::user()->id;
		$saved = $obj->save();
		if(!$saved)
			{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
			else
			{
				$response['status'] 	= 	true;
				
			}	
			$datas = TaskLog::where('task_id', $request->taskid)->orderby('created_at','DESC')->get();
			ob_start();
			foreach($datas as $data){
				$admindetailcreated_by = \App\Models\Admin::where('id',$data->created_by)->first();
				?>
				<div  class="task-log-item text-semi-light-grey col-v-1" style="margin-top: 5px!important;background-color: #fff;border-radius: 4px;box-shadow: 0 1px 1px 0 rgb(0 0 0 / 10%);padding: 7px 15px;">
			<div class="ag-flex">
								<span  title="Arun " class="col-hr-2 ag-avatar ag-avatar--xs" style="position: relative; background: rgb(3, 169, 244);font-size: .8rem;height: 24px;line-height: 24px;min-width: 24px;width: 24px;    color: #fff;display: block;font-weight: 600;letter-spacing: 1px;text-align: center;border-radius: 50%;overflow: hidden;    margin-right: 10px!important;"><b ><?php echo substr($admindetailcreated_by->first_name, 0, 1); ?></b></span> 
			<div style="flex: 1;" class="ag-flex ag-flex-column ag-flex-1 task-activity-content">
				<div  class="ag-flex ag-space-between ag-align-start">
					<div class="ag-flex ag-align-start">
						<strong class="text-info col-hr-1 ag-flex-shrink-0"> <?php echo $admindetailcreated_by->first_name; ?>  </strong> <?php echo $data->title; ?>
					</div> 
					<span class="text-semi-bold ag-flex-shrink-0"> <?php echo date('Y-m-d h:i A', strtotime($data->created_at)); ?></span>
				</div> 
				<?php if($data->message != ''){ ?>
				<p class="col-v-2 text-semi-light-grey task-activity-block"><?php echo $data->message; ?></p>
				<?php } ?>
			</div>
		</div>
	</div>
				<?php
			}
			$dat = ob_get_clean();
			$response['data']	=	$dat;
			echo json_encode($response);
	}
	
	public function changetaskstatus(Request $request){
		if(Task::where('id', $request->id)->exists()){
			
			$obj = Task::find($request->id);
			if($obj->status == 0){
					$precomment = 'Todo';
				}else if($obj->status == 1){
					$precomment = 'In Progress';
				}else if($obj->status == 2){
					$precomment = 'On Review';
				}else if($obj->status == 3){
					$precomment = 'Completed';
				}
			$obj->status = $request->status;
			$saved = $obj->save(); 
			if(!$saved)
			{
				$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
			}
			else
			{
				if($request->status == 0){
					$comment = 'Todo';
				}else if($request->status == 1){
					$comment = 'In Progress';
				}else if($request->status == 2){
					$comment = 'On Review';
				}else if($request->status == 3){
					$comment = 'Completed';
				}
				if($comment != $precomment){
				$obj = new TaskLog;
				$obj->title = 'changed status from '.$precomment.' to '.$comment;
				$obj->message = '';
				$obj->task_id = $request->id;
				$obj->created_by = Auth::user()->id;
				$saved = $obj->save();
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully updated a Task.';
				}
			}				
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Record not found';
		}
		$datas = TaskLog::where('task_id', $request->id)->orderby('created_at','DESC')->get();
			ob_start();
			foreach($datas as $data){
				$admindetailcreated_by = \App\Models\Admin::where('id',$data->created_by)->first();
				?>
				<div  class="task-log-item text-semi-light-grey col-v-1" style="margin-top: 5px!important;background-color: #fff;border-radius: 4px;box-shadow: 0 1px 1px 0 rgb(0 0 0 / 10%);padding: 7px 15px;">
			<div class="ag-flex">
								<span  title="Arun " class="col-hr-2 ag-avatar ag-avatar--xs" style="position: relative; background: rgb(3, 169, 244);font-size: .8rem;height: 24px;line-height: 24px;min-width: 24px;width: 24px;    color: #fff;display: block;font-weight: 600;letter-spacing: 1px;text-align: center;border-radius: 50%;overflow: hidden;    margin-right: 10px!important;"><b ><?php echo substr($admindetailcreated_by->first_name, 0, 1); ?></b></span> 
			<div style="flex: 1;" class="ag-flex ag-flex-column ag-flex-1 task-activity-content">
				<div  class="ag-flex ag-space-between ag-align-start">
					<div class="ag-flex ag-align-start">
						<strong class="text-info col-hr-1 ag-flex-shrink-0"> <?php echo $admindetailcreated_by->first_name; ?>  </strong> <?php echo $data->title; ?>
					</div> 
					<span class="text-semi-bold ag-flex-shrink-0"> <?php echo date('Y-m-d h:i A', strtotime($data->created_at)); ?></span>
				</div> 
				<?php if($data->message != ''){ ?>
				<p class="col-v-2 text-semi-light-grey task-activity-block"><?php echo $data->message; ?></p>
				<?php } ?>
			</div>
		</div>
	</div>
				<?php
			}
			$dat = ob_get_clean();
			$response['data']	=	$dat;
		echo json_encode($response);	
	}
	
	public function changetaskpriority(Request $request){
		if(Task::where('id', $request->id)->exists()){
			
			$obj = Task::find($request->id);
			$precomment = $obj->priority;
			$obj->priority = $request->status;
			$saved = $obj->save(); 
			if(!$saved)
			{
				$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
			}
			else
			{
				$comment = $request->status;
				if($comment != $precomment){
				$obj = new TaskLog;
				$obj->title = 'changed priority from '.$precomment.' to '.$comment;
				$obj->message = '';
				$obj->task_id = $request->id;
				$obj->created_by = Auth::user()->id;
				$saved = $obj->save();
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully updated a Task.';
				}
			}				
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Record not found';
		}
		$datas = TaskLog::where('task_id', $request->id)->orderby('created_at','DESC')->get();
			ob_start();
			foreach($datas as $data){
				$admindetailcreated_by = \App\Models\Admin::where('id',$data->created_by)->first();
				?>
				<div  class="task-log-item text-semi-light-grey col-v-1" style="margin-top: 5px!important;background-color: #fff;border-radius: 4px;box-shadow: 0 1px 1px 0 rgb(0 0 0 / 10%);padding: 7px 15px;">
			<div class="ag-flex">
								<span  title="Arun " class="col-hr-2 ag-avatar ag-avatar--xs" style="position: relative; background: rgb(3, 169, 244);font-size: .8rem;height: 24px;line-height: 24px;min-width: 24px;width: 24px;    color: #fff;display: block;font-weight: 600;letter-spacing: 1px;text-align: center;border-radius: 50%;overflow: hidden;    margin-right: 10px!important;"><b ><?php echo substr($admindetailcreated_by->first_name, 0, 1); ?></b></span> 
			<div style="flex: 1;" class="ag-flex ag-flex-column ag-flex-1 task-activity-content">
				<div  class="ag-flex ag-space-between ag-align-start">
					<div class="ag-flex ag-align-start">
						<strong class="text-info col-hr-1 ag-flex-shrink-0"> <?php echo $admindetailcreated_by->first_name; ?>  </strong> <?php echo $data->title; ?>
					</div> 
					<span class="text-semi-bold ag-flex-shrink-0"> <?php echo date('Y-m-d h:i A', strtotime($data->created_at)); ?></span>
				</div> 
				<?php if($data->message != ''){ ?>
				<p class="col-v-2 text-semi-light-grey task-activity-block"><?php echo $data->message; ?></p>
				<?php } ?>
			</div>
		</div>
	</div>
				<?php
			}
			$dat = ob_get_clean();
			$response['data']	=	$dat;
		echo json_encode($response);	
	}

	public function import(Request $request){
		$the_file = $request->file('uploaded_file');
		try{
			$spreadsheet = IOFactory::load($the_file->getRealPath());
			$sheet        = $spreadsheet->getActiveSheet();
			$row_limit    = $sheet->getHighestDataRow();
			$column_limit = $sheet->getHighestDataColumn();
			$row_range    = range( 2, $row_limit );
			$column_range = range( 'S', $column_limit ); //AE
			$startcount = 2;
			$data = array();
			
			foreach ( $row_range as $row ) {
				/*$data[] = [
				   'partner_name'=>$sheet->getCell( 'B' . $row )->getValue(),
				   'master_category'=>$sheet->getCell( 'C' . $row )->getValue(),
				   'partner_type'=>$sheet->getCell( 'D' . $row )->getValue(),
				   'business_reg_no'=>$sheet->getCell( 'E' . $row )->getValue(),
				   'service_workflow'=>$sheet->getCell( 'F' . $row )->getValue(),
				   'currency'=>$sheet->getCell( 'G' . $row )->getValue(),
				   'email'=>$sheet->getCell( 'H' . $row )->getValue(),
				   'gender'=>$sheet->getCell( 'I' . $row )->getValue(),
				   'country_code'=>$sheet->getCell( 'J' . $row )->getValue(),
				   'phone'=>$sheet->getCell( 'K' . $row )->getValue(),
				   'state'=>$sheet->getCell( 'L' . $row )->getValue(),
				   'country'=>$sheet->getCell( 'M' . $row )->getValue(),
				   'zip'=>$sheet->getCell( 'N' . $row )->getValue(),
				   'password'=>$sheet->getCell( 'O' . $row )->getValue(),
				   'profile_img'=>$sheet->getCell( 'P' . $row )->getValue(),
				   'created_at'=>$sheet->getCell( 'Q' . $row )->getValue(),
				   'updated_at'=>$sheet->getCell( 'R' . $row )->getValue(),
				   'city'=>$sheet->getCell( 'S' . $row )->getValue(),
				   'address'=>$sheet->getCell( 'T' . $row )->getValue(),
				   'fax'=>$sheet->getCell( 'U' . $row )->getValue(),
				   'website'=>$sheet->getCell( 'V' . $row )->getValue(),
				   'status'=>$sheet->getCell( 'W' . $row )->getValue(),
				   'contract_expiry'=>$sheet->getCell( 'X' . $row )->getValue(),
				   'represent_region'=>$sheet->getCell( 'Y' . $row )->getValue(),
				   'commission_percentage'=>$sheet->getCell( 'Z' . $row )->getValue(),
				   'default_super_agent'=>$sheet->getCell( 'AA' . $row )->getValue(),
				   'gst'=>$sheet->getCell( 'AB' . $row )->getValue(),
				   'is_regional'=>$sheet->getCell( 'AC' . $row )->getValue(),
				   'is_archived'=>$sheet->getCell( 'AD' . $row )->getValue(),
				   'level'=>$sheet->getCell( 'AE' . $row )->getValue(),
				];*/
				
				 //Get master_category id
                $master_category_val = trim($sheet->getCell( 'C' . $row )->getValue());
                if($master_category_val){
                    $cat_data = DB::table('categories')->select('categories.id')->where('category_name', 'like', '%'.$master_category_val.'%')->get();
                    if(!empty($cat_data)){
                        $master_category_id = $cat_data[0]->id;
                    } else {
                        $master_category_id = "";
                    }
                } else {
                    $master_category_id = "";
                }
                //dd($master_category_id);

                //Get partner type id
                $partner_type_val = trim($sheet->getCell( 'D' . $row )->getValue()); //dd($partner_type_val);
                if($partner_type_val){
                    $partner_type_data = DB::table('partner_types')->select('partner_types.id')->where('category_id', $master_category_id)->where('name', 'like', '%'.$partner_type_val.'%')->get();
                    if(!empty($partner_type_data)){
                        $partner_type_id = $partner_type_data[0]->id;
                    } else {
                        $partner_type_id = "";
                    }
                } else {
                    $partner_type_id = "";
                }
                //dd($partner_type_id);

                //Get service_workflow id
                $service_workflow_val = trim($sheet->getCell( 'F' . $row )->getValue()); //dd($service_workflow_val);
                if($service_workflow_val){
                    $workflow_data = DB::table('workflows')->select('workflows.id')->where('name', 'like', '%'.$service_workflow_val.'%')->get();
                    //dd($workflow_data);
                    if(!empty($workflow_data)){
                        $workflow_data_id = $workflow_data[0]->id;
                    } else {
                        $workflow_data_id = "";
                    }
                } else {
                    $workflow_data_id = "";
                }
                //dd($workflow_data_id);

                //Get is_regional value
                $is_regional_val = trim($sheet->getCell( 'R' . $row )->getValue());
                if($is_regional_val == 'Regional'){
                    $is_regional_val_bit = 1;
                } else {
                    $is_regional_val_bit = 0;
                }
                
                 $data[] = [
                    'partner_name'=>$sheet->getCell( 'B' . $row )->getValue(),
                    'master_category'=>$master_category_id,
                    'partner_type'=>$partner_type_id,
                    'business_reg_no'=>$sheet->getCell( 'E' . $row )->getValue(),
                    'service_workflow'=>$workflow_data_id,
                    'currency'=>$sheet->getCell( 'G' . $row )->getValue(),
                    'email'=>$sheet->getCell( 'H' . $row )->getValue(),
                    'country_code'=>$sheet->getCell( 'I' . $row )->getValue(),
                    'phone'=>$sheet->getCell( 'J' . $row )->getValue(),
                    'state'=>$sheet->getCell( 'K' . $row )->getValue(),
                    'country'=>$sheet->getCell( 'L' . $row )->getValue(),
                    'zip'=>$sheet->getCell( 'M' . $row )->getValue(),
                    'city'=>$sheet->getCell( 'N' . $row )->getValue(),
                    'address'=>$sheet->getCell( 'O' . $row )->getValue(),
                    'fax'=>$sheet->getCell( 'P' . $row )->getValue(),
                    'website'=>$sheet->getCell( 'Q' . $row )->getValue(),
                    'is_regional'=>$is_regional_val_bit,
                    'level'=>$sheet->getCell( 'S' . $row )->getValue(),
				];
				$startcount++;
			}
			//dd($data);
			DB::table('partners')->insert($data);
            DB::table('check_partners')->insert($data);
		} catch (Exception $e) {
			$error_code = $e->errorInfo[1];
			return back()->withErrors('There was a problem uploading the data!');
		} 
		return back()->withSuccess('Great! Data has been successfully uploaded.');
	}
  
    
     //Save Partner Student Invoice
    public function savepartnerstudentinvoice(Request $request, $id = NULL)
	{
		$requestData = $request->all(); //echo '<pre>'; print_r($requestData); die;
        if( $requestData['function_type'] == 'add')
        {
            if ($request->hasfile('document_upload'))
            {
                if(!is_array($request->file('document_upload'))){
                    $files[] = $request->file('document_upload');
                } else {
                    $files = $request->file('document_upload');
                }

                $partner_info = \App\Models\Partner::select('id','partner_name','email')->where('id', $requestData['partner_id'])->first(); //dd($admin);
                if(!empty($partner_info)){
                    $client_unique_id = $partner_info->email;
                } else {
                    $client_unique_id = "";
                }
                $doctype = isset($request->doctype)? $request->doctype : '';
                foreach ($files as $file) {
                    $size = $file->getSize();
                    $fileName = $file->getClientOriginalName();
                    $explodeFileName = explode('.', $fileName);
                    $name = time() . $file->getClientOriginalName();
                    $filePath = $client_unique_id.'/'.$doctype.'/'. $name;
                    Storage::disk('s3')->put($filePath, file_get_contents($file));
                    $exploadename = explode('.', $name);
                    $obj = new \App\Models\Document;
                    $obj->file_name = $explodeFileName[0];
                    $obj->filetype = $exploadename[1];
                    $obj->user_id = Auth::user()->id;
                    //Get the full URL of the uploaded file
                    $fileUrl = Storage::disk('s3')->url($filePath);
                    $obj->myfile = $fileUrl;
                    $obj->myfile_key = $name;

                    $obj->client_id = $requestData['partner_id'];
                    $obj->type = $request->type;
                    $obj->file_size = $size;
                    $obj->doc_type = $doctype;
                    $doc_saved = $obj->save();
                    $insertedDocId = $obj->id;
                }  //end foreach
            } else {
                $insertedDocId = "";
                $doc_saved = "";
            }

            if(isset($requestData['invoice_date'])){
                //Generate unique invoice_id
                $is_record_exist = DB::table('partner_student_invoices')->select('invoice_id')->where('invoice_type',1)->orderBy('invoice_id', 'desc')->first();
                //dd($is_record_exist);
                if(!$is_record_exist){
                    $invoice_id = 1;
                } else {
                    $invoice_id = $is_record_exist->invoice_id;
                    $invoice_id = $invoice_id +1;
                }  //dd($invoice_id);
                $finalArr = array();
                $finalArr['invoice_date'] = $requestData['invoice_date'];
                $finalArr['invoice_no'] = $requestData['invoice_no'];
                $finalArr['save_type'] = $requestData['save_type'];
                for($i=0; $i<count($requestData['description']); $i++){
                    $saved	= DB::table('partner_student_invoices')->insertGetId([
                        'user_id' => $requestData['loggedin_userid'],
                        'partner_id' =>  $requestData['partner_id'],
                        'invoice_id'=>  $invoice_id,
                        'invoice_type' => $requestData['invoice_type'],
                        'invoice_date' => $requestData['invoice_date'],
                        'invoice_no' => $requestData['invoice_no'],
                        'student_id' => $requestData['student_id'][$i],
                        'student_dob' => $requestData['student_dob'][$i],
                        'student_name' => $requestData['student_name'][$i],
                        'student_ref_no' => $requestData['student_ref_no'][$i],
                        'course_name' => $requestData['course_name'][$i],
                        'student_info_id' => $requestData['student_info_id'][$i],
                        'description' => $requestData['description'][$i],
                        'amount_aud' => $requestData['amount_aud'][$i],
                        'uploaded_doc_id'=> $insertedDocId,
                        'save_type'=> $requestData['save_type'],
                        'created_at'=> date('Y-m-d H:i:s'),
                        'updated_at'=> date('Y-m-d H:i:s')
                    ]);
                }
            }
            //echo '<pre>'; print_r($finalArr); die;
            if($saved) {
                $response['status'] = true;
                $response['requestData'] = $finalArr;
                $response['lastInsertedId'] = $saved;
                $response['function_type'] = $requestData['function_type'];
                $response['last_invoice_id'] = $invoice_id;
                $response['partnerid'] = $requestData['partner_id'];


                //Get Total Enrolled Student for any specific invoice no
                $db_total_enrolled_student = DB::table('partner_student_invoices')->where('partner_id',$requestData['partner_id'])->where('invoice_type',1)->where('invoice_id',$invoice_id)->count('invoice_id');
                $response['db_total_enrolled_student'] = $db_total_enrolled_student; //dd($db_total_enrolled_student );

                //Get Total amount for any specific invoice no
                $db_total_amount = DB::table('partner_student_invoices')->where('partner_id',$requestData['partner_id'])->where('invoice_type',1)->where('invoice_id',$invoice_id)->sum('amount_aud');
                $response['db_total_amount'] = $db_total_amount; //dd($db_total_amount);

                //Get total amount for all invoices
                $db_total_deposit_amount = DB::table('partner_student_invoices')->where('partner_id',$requestData['partner_id'])->where('invoice_type',1)->sum('amount_aud');
                $response['db_total_deposit_amount'] = $db_total_deposit_amount; //dd($db_total_deposit_amount);

                if($doc_saved){
                    //Get AWS Url link
                    $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                    $awsUrl = $url.$client_unique_id.'/'.$doctype.'/'.$name; //dd($awsUrl);
                    $response['awsUrl'] = $awsUrl;
                    $response['message'] = 'Student Invoice with document added successfully';
                    $subject = 'added student invoice with invoice No-'.$requestData['invoice_no'].' and document' ;
                } else {
                    $response['message'] = 'Student Invoice added successfully';
                    $response['awsUrl'] =  "";
                    $subject = 'added student invoice with invoice No-'.$requestData['invoice_no'];
                }
                $printUrl = \URL::to('/admin/partners/printpreviewcreateinvoice').'/'.$invoice_id;
                $response['printUrl'] = $printUrl;
            } else {
                $response['lastInsertedId'] = "";
                $response['awsUrl'] =  "";
                $response['requestData'] = "";
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['function_type'] = $requestData['function_type'];
                $response['last_invoice_id'] = "";
                $response['partnerid'] = "";
            }
        }
        else if( $requestData['function_type'] == 'edit')
        {
            if ($request->hasfile('document_upload'))
            {
                if(!is_array($request->file('document_upload'))){
                    $files[] = $request->file('document_upload');
                } else {
                    $files = $request->file('document_upload');
                }

                $partner_info = \App\Models\Partner::select('id','partner_name','email')->where('id', $requestData['partner_id'])->first(); //dd($admin);
                if(!empty($partner_info)){
                    $client_unique_id = $partner_info->email;
                } else {
                    $client_unique_id = "";
                }
                $doctype = isset($request->doctype)? $request->doctype : '';
                foreach ($files as $file) {
                    $size = $file->getSize();
                    $fileName = $file->getClientOriginalName();
                    $explodeFileName = explode('.', $fileName);
                    $name = time() . $file->getClientOriginalName();
                    $filePath = $client_unique_id.'/'.$doctype.'/'. $name;
                    Storage::disk('s3')->put($filePath, file_get_contents($file));
                    $exploadename = explode('.', $name);
                    $obj2 = new \App\Models\Document;
                    $obj2->file_name = $explodeFileName[0];
                    $obj2->filetype = $exploadename[1];
                    $obj2->user_id = Auth::user()->id;
                    //Get the full URL of the uploaded file
                    $fileUrl = Storage::disk('s3')->url($filePath);
                    $obj2->myfile = $fileUrl;
                    $obj2->myfile_key = $name;

                    $obj2->client_id = $requestData['partner_id'];
                    $obj2->type = $request->type;
                    $obj2->file_size = $size;
                    $obj2->doc_type = $doctype;
                    $doc_saved2 = $obj2->save();
                    $insertedDocId2 = $obj2->id;
                }  //end foreach
            } else {
                $insertedDocId2 = "";
                $doc_saved2 = "";
            }

            //Check any entry is deleted
            $all_record_get = DB::table('partner_student_invoices')->select('id')->where('invoice_type',1)->where('invoice_id',$requestData['invoice_id'])->get();
            //dd($all_record_get);
            if(!empty($all_record_get) ){
                $db_arr = array();
                foreach($all_record_get as $dbkey=>$dbval){
                    $db_arr[] = $dbval->id;
                }
                //dd($db_arr);
                $req_id_arr = array();
                foreach($requestData['id'] as $reqkey=>$reqval){
                    $req_id_arr[] = $reqval;
                }

                $result_final = array_diff($db_arr, $req_id_arr);
                $result_final = array_values($result_final); //dd($result_final);
                if(!empty($result_final)){
                    $response['requestDeleteData'] 	= $result_final;
                    $response['requestDeleteDataType'] 	= "delete";
                    foreach($result_final as $final_key=>$final_val){
                        DB::table('partner_student_invoices')->where('id', $final_val)->delete();
                    }
                }
            }

            if(isset($requestData['description'])){
                $finalArr = array();
                $finalAddArr = array();

                $finalAddArr['invoice_date'] = $requestData['invoice_date'];
                $finalAddArr['invoice_id'] = $requestData['invoice_id'];

                $finalArr['invoice_date'] = $requestData['invoice_date'];
                $finalArr['invoice_id'] = $requestData['invoice_id'];

                for($j=0; $j<count($requestData['description']); $j++){
                    if( empty($requestData['id'][$j] ) ){ //add new entry
                        $lastInsertId11	= DB::table('partner_student_invoices')->insertGetId([
                            'user_id' => $requestData['loggedin_userid'],
                            'partner_id' =>  $requestData['partner_id'],
                            'invoice_id'=>  $requestData['invoice_id'],
                            'invoice_type' => $requestData['invoice_type'],
                            'invoice_date' => $requestData['invoice_date'],
                            'invoice_no'=>  $requestData['invoice_no'],
                            'student_id' => $requestData['student_id'][$j],
                            'student_dob' => $requestData['student_dob'][$j],
                            'student_name' => $requestData['student_name'][$j],
                            'student_ref_no' => $requestData['student_ref_no'][$j],
                            'course_name' => $requestData['course_name'][$j],
                            'student_info_id' => $requestData['student_info_id'][$j],
                            'description' => $requestData['description'][$j],
                            'amount_aud' => $requestData['amount_aud'][$j],
                            'save_type' => $requestData['save_type']
                        ]);
                        $finalAddArr[$j]['id'] = $lastInsertId11;
                        //$finalAddArr['id'] = $lastInsertId11;
                        $response['requestAddData'] 	= $finalAddArr;
                        $response['requestAddDataType'] = "add";
                    }
                    else { //edit case
                        $saved	= DB::table('partner_student_invoices')
                        ->where('id',$requestData['id'][$j])
                        ->update([
                            'user_id' => $requestData['loggedin_userid'],
                            'partner_id' =>  $requestData['partner_id'],
                            'invoice_id' =>  $requestData['invoice_id'],
                            'invoice_type' => $requestData['invoice_type'],
                            'invoice_date' => $requestData['invoice_date'],
                            'invoice_no' => $requestData['invoice_no'],
                            'student_id' => $requestData['student_id'][$j],
                            'student_dob' => $requestData['student_dob'][$j],
                            'student_name' => $requestData['student_name'][$j],
                            'student_ref_no' => $requestData['student_ref_no'][$j],
                            'course_name' => $requestData['course_name'][$j],
                            'student_info_id' => $requestData['student_info_id'][$j],
                            'description' => $requestData['description'][$j],
                            'amount_aud' => $requestData['amount_aud'][$j],
                            'save_type' => $requestData['save_type']
                        ]);
                    }
                }
            }
            //echo '<pre>'; print_r($saved); die;
            if($saved>=0) {
                $response['requestData'] = $finalArr;
                $response['partnerid'] = $requestData['partner_id'];

                //Get Total Enrolled Student for any specific invoice no
                $db_total_enrolled_student2 = DB::table('partner_student_invoices')->where('partner_id',$requestData['partner_id'])->where('invoice_type',1)->where('invoice_id',$requestData['invoice_id'])->count('invoice_id');
                $response['db_total_enrolled_student2'] = $db_total_enrolled_student2; //dd($db_total_enrolled_student2 );

                //Get Total amount for any specific invoice no
                $db_total_amount2 = DB::table('partner_student_invoices')->where('partner_id',$requestData['partner_id'])->where('invoice_type',1)->where('invoice_id',$requestData['invoice_id'])->sum('amount_aud');
                $response['db_total_amount2'] = $db_total_amount2; //dd($db_total_amount2 );

                //Get total amount for all invoices
                $db_total_deposit_amount2 = DB::table('partner_student_invoices')->where('partner_id',$requestData['partner_id'])->where('invoice_type',1)->sum('amount_aud');
                $response['db_total_deposit_amount2'] = $db_total_deposit_amount2;

                if($doc_saved2){
                    //Update document id
                    DB::table('partner_student_invoices')
                    ->where('partner_id',$requestData['partner_id'])
                    ->where('invoice_id',$requestData['invoice_id'])
                    ->where('invoice_type',1)
                    ->update(['uploaded_doc_id'=> $insertedDocId2]);

                    //Get AWS Url link
                    $url2 = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                    $awsUrl2 = $url2.$client_unique_id.'/'.$doctype.'/'.$name; //dd($awsUrl);
                    $response['awsUrl2'] = $awsUrl2;
                    $response['message'] = 'Student Invoice with document added successfully';
                    $subject = 'added student invoice with invoice No-'.$requestData['invoice_no'].' and document' ;
                } else {
                    $response['message'] = 'Student Invoice added successfully';
                    $response['awsUrl2'] =  "";
                    $subject = 'added student invoice with invoice No-'.$requestData['invoice_no'];
                }
                $printUrl2 = \URL::to('/admin/partners/printpreviewcreateinvoice').'/'.$requestData['invoice_id'];
                $response['printUrl2'] = $printUrl2;

                $response['status'] 	= 	true;
                $response['message']	=	'Invoice updated successfully';
                $response['function_type'] = $requestData['function_type'];
            } else {
                $response['requestData'] = "";
                $response['db_total_deposit_amount2'] = "";
                $response['db_total_amount2'] = "";
                $response['db_total_enrolled_student2']= "";
                $response['awsUrl2'] = "";
                $response['printUrl2'] = "";
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['function_type'] = $requestData['function_type'];
                $response['partnerid'] = "";
            }
        }
       echo json_encode($response);
    }
  
    //Get Top Invoice Value From Db
    public function getTopReceiptValInDB(Request $request)
	{
        $requestData = 	$request->all();
        $invoice_type = $requestData['type'];
        $record_count = DB::table('partner_student_invoices')->where('invoice_type',$invoice_type)->max('id');
        //dd($record_count);
        if($record_count) {
            /*if($invoice_type == 3){ //type = record payment
                $max_invoice_id = DB::table('partner_student_invoices')->where('invoice_type',$invoice_type)->max('invoice_id');
                $response['max_invoice_id'] 	= $max_invoice_id;
            } else {
                $response['max_invoice_id'] 	= "";
            }*/
            $response['invoice_type'] 	= $invoice_type;
            $response['record_count'] 	= $record_count;
            $response['status'] 	= 	true;
            $response['message']	=	'Record is exist';
        }else{
            $response['invoice_type'] 	= $invoice_type;
            $response['record_count'] 	= $record_count;
            //$response['max_invoice_id'] = "";
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
        }
        echo json_encode($response);
    }

    //Get Enrolled Student List
    public function getEnrolledStudentList(Request $request)
	{
        //dd($request->partnerid);
        $record_get = \App\Models\Application::join('admins', 'applications.client_id', '=', 'admins.id')
        ->leftJoin('partners', 'applications.partner_id', '=', 'partners.id')
        ->select('admins.client_id as client_reference','admins.first_name','admins.last_name','admins.id')
        ->where('applications.partner_id', $request->partnerid)
        ->where(function ($query) {
            $query->where('applications.stage', 'Coe issued')
            ->orWhere('applications.stage', 'Enrolled')
            ->orWhere('applications.stage', 'Coe Cancelled');
        })
        ->groupBy('admins.client_id') // Grouping by client_id and other selected columns
        ->orderBy('admins.first_name', 'ASC')
        ->get();
        //dd($record_get);
        if(!empty($record_get)) {
            $str = "<option value=''>Select</option>";
            foreach($record_get as $key=>$val) {
                $student_name = $val->first_name.' '.$val->last_name.'('.$val->client_reference.')';
                $str .=  '<option value="'.$val->id.'">'.$student_name.'</option>';
            }
            $response['record_get'] = $str;
            $response['status'] 	= 	true;
            $response['message']	=	'Record is exist';
        }else{
            $response['record_get'] 	= array();
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
        }
        echo json_encode($response);
    }

    //Save Partner Record Invoice
    public function savepartnerrecordinvoice(Request $request, $id = NULL)
	{
		$requestData = $request->all(); //echo '<pre>'; print_r($requestData); die;
        if( $requestData['function_type'] == 'add')
        {
            if ($request->hasfile('document_upload'))
            {
                if(!is_array($request->file('document_upload'))){
                    $files[] = $request->file('document_upload');
                }else{
                    $files = $request->file('document_upload');
                }

                $partner_info = \App\Models\Partner::select('id','partner_name','email')->where('id', $requestData['partner_id'])->first(); //dd($admin);
                if(!empty($partner_info)){
                    $client_unique_id = $partner_info->email;
                } else {
                    $client_unique_id = "";
                }

                $doctype = isset($request->doctype)? $request->doctype : '';

                foreach ($files as $file) {
                    $size = $file->getSize();
                    $fileName = $file->getClientOriginalName();
                    $explodeFileName = explode('.', $fileName);
                    $name = time() . $file->getClientOriginalName();
                    $filePath = $client_unique_id.'/'.$doctype.'/'. $name;
                    Storage::disk('s3')->put($filePath, file_get_contents($file));
                    $exploadename = explode('.', $name);

                    $obj = new \App\Models\Document;
                    $obj->file_name = $explodeFileName[0];
                    $obj->filetype = $exploadename[1];
                    $obj->user_id = Auth::user()->id;
                    //$obj->myfile = $name;

                    // Get the full URL of the uploaded file
                    $fileUrl = Storage::disk('s3')->url($filePath);
                    $obj->myfile = $fileUrl;
                    $obj->myfile_key = $name;

                    $obj->client_id = $requestData['partner_id'];
                    $obj->type = $request->type;
                    $obj->file_size = $size;
                    $obj->doc_type = $doctype;
                    $doc_saved = $obj->save();

                    $insertedDocId = $obj->id;
                }  //end foreach
            } else {
                $insertedDocId = "";
                $doc_saved = "";
            }

            if(isset($requestData['invoice_date'])){
                //Generate unique invoice_id
                $is_record_exist = DB::table('partner_student_invoices')->select('invoice_id')->where('invoice_type',2)->orderBy('invoice_id', 'desc')->first();
                //dd($is_record_exist);
                if(!$is_record_exist){
                    $invoice_id = 1;
                } else {
                    $invoice_id = $is_record_exist->invoice_id;
                    $invoice_id = $invoice_id +1;
                }  //dd($invoice_id);
                $finalArr = array();
                for($i=0; $i<count($requestData['invoice_date']); $i++){
                    $finalArr[$i]['invoice_date'] = $requestData['invoice_date'][$i];
                    $finalArr[$i]['sent_date'] = $requestData['sent_date'][$i];
                    $finalArr[$i]['invoice_no'] = $requestData['invoice_no'][$i];
                    $finalArr[$i]['amount_aud'] = $requestData['amount_aud'][$i];
                    $finalArr[$i]['partnerid'] = $requestData['partner_id'];

                    $saved	= DB::table('partner_student_invoices')->insertGetId([
                        'user_id' => $requestData['loggedin_userid'],
                        'partner_id' =>  $requestData['partner_id'],
                        'invoice_id'=>  $invoice_id,
                        'invoice_type' => $requestData['invoice_type'],
                        'invoice_date' => $requestData['invoice_date'][$i],
                        'invoice_no' => $requestData['invoice_no'][$i],
                        'sent_date' => $requestData['sent_date'][$i],
                        'amount_aud' => $requestData['amount_aud'][$i],
                        'uploaded_doc_id'=> $insertedDocId
                    ]);
                    $finalArr[$i]['id'] = $saved;
                }
            }
            //echo '<pre>'; print_r($finalArr); die;
            if($saved) {
                $response['status'] = true;
                $response['requestData'] = $finalArr;
                $response['lastInsertedId'] = $saved;
                $response['function_type'] = $requestData['function_type'];

                //Get total amount
                $db_total_deposit_amount = DB::table('partner_student_invoices')->where('partner_id',$requestData['partner_id'])->where('invoice_type',2)->sum('amount_aud');
                $response['db_total_deposit_amount'] = $db_total_deposit_amount; //dd($db_total_deposit_amount );

                if($doc_saved){
                    //Get AWS Url link
                    $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                    $awsUrl = $url.$client_unique_id.'/'.$doctype.'/'.$name; //dd($awsUrl);
                    $response['awsUrl'] = $awsUrl;
                    $response['message'] = 'Record Invoice with document added successfully';

                    $subject = 'added record invoice with Receipt No-'.$invoice_id.' and document' ;
                } else {
                    $response['message'] = 'Record Invoice added successfully';
                    $response['awsUrl'] =  "";
                    $subject = 'added record invoice with Receipt No-'.$invoice_id;
                }

                //$printUrl = \URL::to('/admin/partners/printpreviewrecordinvoice').'/'.$invoice_id;
                //$response['printUrl'] = $printUrl;
            } else {
                $response['lastInsertedId'] = "";
                $response['awsUrl'] =  "";
                $response['requestData'] = "";
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['function_type'] = $requestData['function_type'];
            }
        }
        echo json_encode($response);
    }


    //Get Recorded Invoice List
    public function getRecordedInvoiceList(Request $request)
	{
        //dd($request->partnerid);
        $record_get = DB::table('partner_student_invoices')->select('invoice_no')->where('partner_id',$request->partnerid)->whereIn('invoice_type', [1, 2])->groupBy('invoice_no')->get();
        //dd($record_get);
        if(!empty($record_get)) {
            $str = "<option value=''>Select</option>";
            foreach($record_get as $key=>$val) {
                $str .=  '<option value="'.$val->invoice_no.'">'.$val->invoice_no.'</option>';
            }
            $response['record_get'] = $str;
            $response['status'] 	= 	true;
            $response['message']	=	'Record is exist';
        }else{
            $response['record_get'] 	= array();
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
        }
        echo json_encode($response);
    }


    //Save Partner Record Payment
    public function savepartnerrecordpayment(Request $request, $id = NULL)
	{
		$requestData = $request->all(); //echo '<pre>'; print_r($requestData); die;
        if( $requestData['function_type'] == 'add')
        {
            if ($request->hasfile('document_upload'))
            {
                if(!is_array($request->file('document_upload'))){
                    $files[] = $request->file('document_upload');
                }else{
                    $files = $request->file('document_upload');
                }

                $partner_info = \App\Models\Partner::select('id','partner_name','email')->where('id', $requestData['partner_id'])->first(); //dd($admin);
                if(!empty($partner_info)){
                    $client_unique_id = $partner_info->email;
                } else {
                    $client_unique_id = "";
                }

                $doctype = isset($request->doctype)? $request->doctype : '';

                foreach ($files as $file) {
                    $size = $file->getSize();
                    $fileName = $file->getClientOriginalName();
                    $explodeFileName = explode('.', $fileName);
                    $name = time() . $file->getClientOriginalName();
                    $filePath = $client_unique_id.'/'.$doctype.'/'. $name;
                    Storage::disk('s3')->put($filePath, file_get_contents($file));
                    $exploadename = explode('.', $name);

                    $obj = new \App\Models\Document;
                    $obj->file_name = $explodeFileName[0];
                    $obj->filetype = $exploadename[1];
                    $obj->user_id = Auth::user()->id;
                    //$obj->myfile = $name;

                    // Get the full URL of the uploaded file
                    $fileUrl = Storage::disk('s3')->url($filePath);
                    $obj->myfile = $fileUrl;
                    $obj->myfile_key = $name;

                    $obj->client_id = $requestData['partner_id'];
                    $obj->type = $request->type;
                    $obj->file_size = $size;
                    $obj->doc_type = $doctype;
                    $doc_saved = $obj->save();

                    $insertedDocId = $obj->id;
                }  //end foreach
            } else {
                $insertedDocId = "";
                $doc_saved = "";
            }

            if(isset($requestData['verified_date'])){
                //Generate unique invoice_id
                $is_record_exist = DB::table('partner_student_invoices')->select('invoice_id')->where('invoice_type',3)->orderBy('invoice_id', 'desc')->first();
                //dd($is_record_exist);
                if(!$is_record_exist){
                    $invoice_id = 1;
                } else {
                    $invoice_id = $is_record_exist->invoice_id;
                    $invoice_id = $invoice_id +1;
                }  //dd($invoice_id);
                $finalArr = array();
                for($i=0; $i<count($requestData['verified_date']); $i++){
                    $finalArr[$i]['invoice_no'] = $requestData['invoice_no'][$i];
                    $finalArr[$i]['method_received'] = $requestData['method_received'][$i];
                    $finalArr[$i]['verified_by'] = $requestData['verified_by'][$i];
                    $finalArr[$i]['verified_date'] = $requestData['verified_date'][$i];
                    $finalArr[$i]['amount_aud'] = $requestData['amount_aud'][$i];
                    $finalArr[$i]['partnerid'] = $requestData['partner_id'];

                    $saved	= DB::table('partner_student_invoices')->insertGetId([
                        'user_id' => $requestData['loggedin_userid'],
                        'partner_id' =>  $requestData['partner_id'],
                        'invoice_id'=>  $invoice_id,
                        'invoice_type' => $requestData['invoice_type'],
                        'invoice_no' => $requestData['invoice_no'][$i],
                        'method_received' => $requestData['method_received'][$i],
                        'verified_by' => $requestData['verified_by'][$i],
                        'verified_date' => $requestData['verified_date'][$i],
                        'amount_aud' => $requestData['amount_aud'][$i],
                        'uploaded_doc_id'=> $insertedDocId
                    ]);
                    $finalArr[$i]['id'] = $saved;
                }
            }
            //echo '<pre>'; print_r($finalArr); die;
            if($saved) {
                $response['status'] = true;
                $response['requestData'] = $finalArr;
                $response['lastInsertedId'] = $saved;
                $response['function_type'] = $requestData['function_type'];

                //Get total amount
                $db_total_deposit_amount = DB::table('partner_student_invoices')->where('partner_id',$requestData['partner_id'])->where('invoice_type',3)->sum('amount_aud');
                $response['db_total_deposit_amount'] = $db_total_deposit_amount; //dd($db_total_deposit_amount );

                if($doc_saved){
                    //Get AWS Url link
                    $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                    $awsUrl = $url.$client_unique_id.'/'.$doctype.'/'.$name; //dd($awsUrl);

                    //$awsUrl = \URL::to('/img/client_receipts').'/'.$document_upload;
                    $response['awsUrl'] = $awsUrl;
                    $response['message'] = 'Record Payment with document added successfully';

                    $subject = 'added record payment with Receipt No-'.$invoice_id.' and document' ;
                } else {
                    $response['message'] = 'Record payment added successfully';
                    $response['awsUrl'] =  "";
                    $subject = 'added record payment with Receipt No-'.$invoice_id;
                }

                $printUrl = \URL::to('/admin/partners/printpreview').'/'.$invoice_id;
                $response['printUrl'] = $printUrl;
            } else {
                $response['lastInsertedId'] = "";
                $response['awsUrl'] =  "";
                $response['requestData'] = "";
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['function_type'] = $requestData['function_type'];
            }
        }
        echo json_encode($response);
    }

    //Update student status
    public function updateStudentStatus(Request $request)
	{
        //dd($request->all());
        $updatedRows = DB::table('applications')->where('id', $request->student_id)->update(['status' => $request->new_status]);
        // Check if the update was successful
        if ($updatedRows > 0) {
            $response['status'] 	= 	true;
            $response['message']	=	'Student status updated successfully.';
            $response['studentId']	=   $request->student_id;


            if($request->new_status == 0){
                $student_status = "In Progress";
            } else if($request->new_status == 1){
                $student_status = "Completed";
            } else if($request->new_status == 2){
                $student_status = "Discontinued";
            } else if($request->new_status == 3){
                $student_status = "Cancelled";
            } else if($request->new_status == 4){
                $student_status = "Withdrawn";
            } else if($request->new_status == 5){
                $student_status = "Deferred";
            } else if($request->new_status == 6){
                $student_status = "Future";
            } else if($request->new_status == 7){
                $student_status = "VOE";
            } else if($request->new_status == 8){
                $student_status = "Refund";
            }
            $response['newStatus']	= $student_status;
            $response['newStatus_id']	= $request->new_status;
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'No changes made or student not found.Please try again';
            $response['studentId']	=  "";
            $response['newStatus']	= "";
            $response['newStatus_id']	= "";
        }
        echo json_encode($response);
    }


    //Get Student Info
    public function getStudentInfo(Request $request)
	{
        //dd($request->sel_student_id);
        $record_get = \App\Models\Admin::select('first_name','last_name','id','dob','client_id')
        ->where('id', $request->sel_student_id)->first(); //dd($record_get);
        if($record_get) {
            if( $record_get->dob != ""){
                $dobArr = explode("-",$record_get->dob);
                $dobFinal = $dobArr[2]."/".$dobArr[1]."/".$dobArr[0];
                $response['student_db'] = $dobFinal;
            } else {
                $response['student_db'] = "";
            }
            $response['student_name'] = $record_get->first_name.' '.$record_get->last_name;
            $response['student_ref_no'] = $record_get->client_id;
            $response['status'] 	= 	true;
            $response['message']	=	'Record is exist';
        } else {
            $response['student_db'] =  "";
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
        }
        echo json_encode($response);
    }

    //Get Student Course info and student id
    public function getStudentCourseInfo(Request $request)
	{
        //dd($request->sel_student_id);
        $studentCourseInfo = \App\Models\Application::join('partners', 'applications.partner_id', '=', 'partners.id')
        ->leftJoin('products', 'applications.product_id', '=', 'products.id')
        ->leftJoin('application_fee_options', 'applications.id', '=', 'application_fee_options.app_id')
        ->select('applications.student_id','products.name as coursename','application_fee_options.commission_pending')
        ->where('applications.partner_id', $request->partner_id)
        ->where('applications.client_id', $request->sel_student_id)
        ->where(function ($query) {
            $query->where('applications.stage', 'Coe issued')
                ->orWhere('applications.stage', 'Enrolled')
                ->orWhere('applications.stage', 'Coe Cancelled');
        })->first(); //dd($studentCourseInfo);
        if($studentCourseInfo) {
            if($studentCourseInfo->commission_pending != ""){
                $studentCourseInfo->commission_pending = $studentCourseInfo->commission_pending;
            } else {
                $studentCourseInfo->commission_pending = 0;
            }
            $response['student_course_info'] = $studentCourseInfo;
            $response['status'] 	= 	true;
            $response['message']	=	'Record is exist';
        } else {
            $response['student_course_info'] =  array();
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
        }
        echo json_encode($response);
    }


    public function generateIncrementInvoiceNumber($lastInvoiceNumber = null) {
        // Prefix with the current year
        $yearPrefix = date('y'); // e.g., "24" for the year 2024
        $prefix = "INV-" . $yearPrefix;

        // If there is no previous invoice, start with the base number
        if (!$lastInvoiceNumber) {
            return $prefix . "0001";
        }

        // Extract the numeric part of the last invoice number
        $numericPart = (int)substr($lastInvoiceNumber, strlen($prefix));

        // Increment the numeric part
        $newNumericPart = $numericPart + 1;

        // Combine prefix and the new numeric part
        return $prefix . str_pad($newNumericPart, 4, "0", STR_PAD_LEFT);
    }



    //Get Top Invoice Value From Db
    public function getTopInvoiceValInDB(Request $request)
	{
        $requestData = 	$request->all();
        $invoice_type = $requestData['type'];
        $get_invoice_info = DB::table('partner_student_invoices')->select('invoice_no')->where('invoice_type', $invoice_type)->orderBy('id', 'desc')->first();
        //dd($get_invoice_info);
        if($get_invoice_info) {
            $get_max_invoice_id = $get_invoice_info->invoice_no;
            if($invoice_type == 1){
                $newInvoice = $this->generateIncrementInvoiceNumber($get_max_invoice_id);
            }

            $response['invoice_type'] 	= $invoice_type;
            $response['max_invoice_id'] = $newInvoice;
            $response['status'] 	= 	true;
            $response['message']	=	'Record is exist';
        } else {
            $response['invoice_type'] 	= $invoice_type;
            if($invoice_type == 1){
                $response['max_invoice_id'] = "INV-".date('y')."0001";
            }
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
        }
        echo json_encode($response);
    }


    public function printpreviewcreateinvoice(Request $request, $id){
        //phpinfo();
        $record_get = DB::table('partner_student_invoices')->where('invoice_type',1)->where('invoice_id',$id)->get();
        //dd($record_get);
        if(!empty($record_get)){
            $partnerInfo = DB::table('partners')->where('id',$record_get[0]->partner_id)->first(); //dd($partnerInfo);
            $admin = DB::table('admins')->select('profile_img')->where('id',1)->first(); //dd($admin);
        }
        set_time_limit(3000);
        $pdf = PDF::setOptions([
			'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
			'logOutputFile' => storage_path('logs/log.htm'),
			'tempDir' => storage_path('logs/')
		])->loadView('emails.studentinvoice',compact(['record_get','partnerInfo','admin']));
		return $pdf->stream('StudentInvoice.pdf');
	}


    //update Invoice Sent Option To Yes
    public function updateInvoiceSentOptionToYes(Request $request)
	{
        $requestData = 	$request->all();
        $sel_invoice_id = $requestData['sel_invoice_id'];
        $sent_date = $requestData['sentDate'];
        /*$today_date = date('Y-m-d');
        $today_date_arr = explode("-", $today_date);
        $sent_date = $today_date_arr[2]."/".$today_date_arr[1]."/".$today_date_arr[0];*/
        $upd = DB::table('partner_student_invoices')->where('invoice_type', 1)->where('invoice_id', $sel_invoice_id)->update(['sent_option' => 'Yes','sent_date' => $sent_date,'save_type' => 'final']);
        //dd($upd);
        if($upd) {
            $response['status'] 	= 	true;
            $response['message']	=	'Invoice is updated successfully';
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Invoice is not exist.Please try again';
        }
        echo json_encode($response);
    }

    //Get Info By InvoiceId For draft invoice
    public function getInfoByInvoiceId(Request $request)
	{
        $requestData = 	$request->all();
        $invoiceid = $requestData['invoiceid'];
        $record_get = DB::table('partner_student_invoices')->where('invoice_type',1)->where('invoice_id',$invoiceid)->get();
        //dd($record_get);
        if(!empty($record_get)) {
            $response['record_get'] = $record_get;
            $response['status'] 	= 	true;
            $response['message']	=	'Record is exist';
            $last_record_id = DB::table('partner_student_invoices')->where('invoice_type',1)->max('id');
            //dd($last_record_id);
            $response['last_record_id'] = $last_record_id;
            $response['invoiceid'] = $invoiceid;
        }else{
            $response['record_get'] =   array();
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
            $response['last_record_id'] = 0;
            $response['invoiceid'] = $invoiceid;
        }
        echo json_encode($response);
    }


    //Get Enrolled Student List in Edit mode
    public function getEnrolledStudentListInEditMode(Request $request)
    {
        //dd($request->partnerid);
        $record_get = \App\Models\Application::join('admins', 'applications.client_id', '=', 'admins.id')
        ->leftJoin('partners', 'applications.partner_id', '=', 'partners.id')
        ->select('admins.client_id as client_reference','admins.first_name','admins.last_name','admins.id')
        ->where('applications.partner_id', $request->partnerid)
        ->where(function ($query) {
            $query->where('applications.stage', 'Coe issued')
            ->orWhere('applications.stage', 'Enrolled')
            ->orWhere('applications.stage', 'Coe Cancelled');
        })
        ->groupBy('admins.client_id') // Grouping by client_id and other selected columns
        ->orderBy('admins.first_name', 'ASC')
        ->get(); //dd($record_get);

        $studentRecordInfo = DB::table('partner_student_invoices')->select('student_id')->where('id',$request->uniqueRowId)->first();
        if(!empty($record_get)) {
            $str = "<option value=''>Select</option>";
            foreach($record_get as $key=>$val) {
                $student_name = $val->first_name.' '.$val->last_name.'('.$val->client_reference.')';
                // Check if the current student's ID matches
                if($studentRecordInfo->student_id){
                    $selected = ($studentRecordInfo->student_id == $val->id) ? 'selected' : '';
                    $str .= '<option value="' . $val->id . '" ' . $selected . '>' . $student_name . '</option>';
                } else {
                    $str .=  '<option value="'.$val->id.'" >'.$student_name.'</option>';
                }
            }
            $response['record_get'] =  $str;
            $response['status'] 	=  true;
            $response['message']	=  'Record is exist';
        } else {
            $response['record_get'] =  array();
            $response['status'] 	=  false;
            $response['message']	=  'Record is not exist.Please try again';
        }
        echo json_encode($response);
    }


    //Partner upload inbox email
    public function uploadpartnerfetchmail(Request $request){ //dd($request->all());
        $request->validate([
            'email_file' => 'required|array', // Ensure the field is an array
            'email_file.*' => 'required|mimes:msg|max:20480', // Validate each file (20MB max per file)
        ]);
        $partner_info = \App\Models\Partner::select('id','partner_name','email')->where('id', $request->partner_id)->first(); //dd($partner_info);
        if(!empty($partner_info)){
            $partner_unique_id = $partner_info->email;
        } else {
            $partner_unique_id = "";
        }
        $doc_type = 'partner_email_fetch';
        if ($request->hasfile('email_file')) {
            if(!is_array($request->file('email_file'))){
                $files[] = $request->file('email_file');
            }else{
                $files = $request->file('email_file');
            } //dd(print_r($files));
            foreach ($files as $file) {
                $size = $file->getSize();
              
                $fileName = $file->getClientOriginalName();
                //$fileName = time().'_'.str_replace(' ', '_', $file->getClientOriginalName());
                //$explodeFileName = explode('.', $fileName);
              
                $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
                $fileExtension = $file->getClientOriginalExtension();
              
                $name = time() . $file->getClientOriginalName();
                //$name = time().'_'.str_replace(' ', '_', $file->getClientOriginalName());
              
                $filePath = $partner_unique_id.'/'.$doc_type.'/inbox/'.$name;
                Storage::disk('s3')->put($filePath, file_get_contents($file));
                //$exploadename = explode('.', $name);

                //save uploaded image at aws server
                $obj = new \App\Models\Document;
                $obj->file_name = $fileName;
                $obj->filetype = $fileExtension;
                $obj->user_id = Auth::user()->id;
                //$obj->myfile = $name;

                // Get the full URL of the uploaded file
                $fileUrl = Storage::disk('s3')->url($filePath);
                $obj->myfile = $fileUrl;
                $obj->myfile_key = $name;

                $obj->client_id = $request->partner_id;
                $obj->type = $request->type;
                $obj->mail_type = "inbox";
                $obj->file_size = $size;
                $obj->doc_type = $doc_type;
                $saved = $obj->save();
                if($saved) {
                    $lastInsertedDocId = $obj->id;
                    //Fetch Email content and save it to mail report
                    $fileUploadedPath = $file->getPathname();
                    $messageFactory = new MAPI\MapiMessageFactory();
                    $documentFactory = new Pear\DocumentFactory();
                    $ole = $documentFactory->createFromFile($fileUploadedPath);
                    $message = $messageFactory->parseMessage($ole);  //dd($message);

                    $mail_subject = $message->properties['subject'];
                    $mail_sender = $message->getSender();
                    $mail_body = $message->getBody();
                    $mail_to = array();
                    foreach ($message->getRecipients() as $recipient) {
                        $mail_to[] = (string)$recipient;
                    }
                    $mail_to_arr = implode(",",$mail_to);

                    //Get mail Sent time
                    /*$sentTime = $message->getSendTime(); //dd($sentTime);
                    if ($sentTime instanceof DateTime) {
                        //$sentTime->modify('+5 hours 30 minutes');
                        $sentTime->modify('+11 hours');
                        $formattedSentTime = $sentTime->format('d/m/Y h:i a');
                        if($formattedSentTime){
                            $sentTimeFinal = $formattedSentTime;
                        } else {
                            $sentTimeFinal = "";
                        }
                    } else {
                        $sentTimeFinal = "";
                    }*/
                  
                    // Get mail Sent time
                    $sentTime = $message->getSendTime();
                    if ($sentTime instanceof DateTime) {
                        // Set the source timezone (assuming the email's sent time is in UTC)
                        $sentTime->setTimezone(new DateTimeZone('UTC'));
                        
                        // Convert to Australian Eastern Time (AEST/AEDT)
                        $australiaTimezone = new DateTimeZone('Australia/Sydney');
                        $sentTime->setTimezone($australiaTimezone);
                        
                        // Format the time
                        $formattedSentTime = $sentTime->format('d/m/Y h:i a');
                        $sentTimeFinal = $formattedSentTime;
                    } else {
                        $sentTimeFinal = "";
                    }

                    $obj1				=  new \App\Models\MailReport;
                    $obj1->user_id		=  Auth::user()->id;
                    $obj1->from_mail 	=  $mail_sender;
                    $obj1->to_mail 		=  $mail_to_arr;
                    $obj1->subject		=  $mail_subject;
                    $obj1->message		=  $mail_body;
                    $obj1->mail_type	=  1;
                    $obj1->client_id	=  $request->partner_id;
                    $obj1->conversion_type = $doc_type;
                    $obj1->mail_body_type	=  "inbox";
                    $obj1->uploaded_doc_id =  $lastInsertedDocId;
                    $obj1->fetch_mail_sent_time = $sentTimeFinal;
                    $obj1->type = $request->type;
                    $saved1 = $obj1->save();
                    if ($saved1) {
                        $response['status'] 	= 	true;
                        $response['message']	=	'Partner inbox email uploaded successfully.';
                    } else {
                        $response['status'] 	= 	false;
                        $response['message']	=	'No inbox email uploaded.Please try again';
                    }
                } else {
                    $response['status'] 	= 	false;
                    $response['message']	=	'No inbox email uploaded.Please try again';
                }
            } //end foreach
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'No inbox email uploaded.Please try again';
        }
        echo json_encode($response);
    }

    //Partner upload sent email
    public function uploadpartnersentfetchmail(Request $request){ //dd($request->all());
        $request->validate([
            'email_file' => 'required|array', // Ensure the field is an array
            'email_file.*' => 'required|mimes:msg|max:20480', // Validate each file (20MB max per file)
        ]);
        $partner_info = \App\Models\Partner::select('id','partner_name','email')->where('id', $request->partner_id)->first(); //dd($partner_info);
        if(!empty($partner_info)){
            $partner_unique_id = $partner_info->email;
        } else {
            $partner_unique_id = "";
        }
        $doc_type = 'partner_email_fetch';
        if ($request->hasfile('email_file')) {
            if(!is_array($request->file('email_file'))){
                $files[] = $request->file('email_file');
            }else{
                $files = $request->file('email_file');
            } //dd(print_r($files));
            foreach ($files as $file) {
                //$file = $request->file('email_file');
                $size = $file->getSize();
              
                $fileName = $file->getClientOriginalName();
                //$fileName = time().'_'.str_replace(' ', '_', $file->getClientOriginalName());
              
                $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
                $fileExtension = $file->getClientOriginalExtension();
              
                //$fileName = $file->getClientOriginalName();
                //$explodeFileName = explode('.', $fileName);
                $name = time() . $file->getClientOriginalName();
                $filePath = $partner_unique_id.'/'.$doc_type.'/sent/'.$name;
                Storage::disk('s3')->put($filePath, file_get_contents($file));
                //$exploadename = explode('.', $name);

                //save uploaded image at aws server
                $obj = new \App\Models\Document;
                $obj->file_name = $fileName;
                $obj->filetype = $fileExtension;
                $obj->user_id = Auth::user()->id;
                //$obj->myfile = $name;
                // Get the full URL of the uploaded file
                $fileUrl = Storage::disk('s3')->url($filePath);
                $obj->myfile = $fileUrl;
                $obj->myfile_key = $name;

                $obj->client_id = $request->partner_id;
                $obj->type = $request->type;
                $obj->mail_type = "sent";
                $obj->file_size = $size;
                $obj->doc_type = $doc_type;
                $saved = $obj->save();
                if($saved){
                    $lastInsertedDocId = $obj->id;
                    //Fetch Email content and save it to mail report
                    $fileUploadedPath = $file->getPathname();
                    $messageFactory = new MAPI\MapiMessageFactory();
                    $documentFactory = new Pear\DocumentFactory();
                    $ole = $documentFactory->createFromFile($fileUploadedPath);
                    $message = $messageFactory->parseMessage($ole);

                    $mail_subject = $message->properties['subject'];
                    $mail_sender = $message->getSender();
                    $mail_body = $message->getBody();
                    $mail_to = array();
                    foreach ($message->getRecipients() as $recipient) {
                        $mail_to[] = (string)$recipient;
                    }
                    $mail_to_arr = implode(",",$mail_to);

                    //Get mail Sent time
                    /*$sentTime = $message->getSendTime();
                    if ($sentTime instanceof DateTime) {
                        //$sentTime->modify('+5 hours 30 minutes');
                        $sentTime->modify('+11 hours');
                        $formattedSentTime = $sentTime->format('d/m/Y h:i a');
                        if($formattedSentTime){
                            $sentTimeFinal = $formattedSentTime;
                        } else {
                            $sentTimeFinal = "";
                        }
                    } else {
                        $sentTimeFinal = "";
                    }*/
                  
                    // Get mail Sent time
                    $sentTime = $message->getSendTime();
                    if ($sentTime instanceof DateTime) {
                        // Set the source timezone (assuming the email's sent time is in UTC)
                        $sentTime->setTimezone(new DateTimeZone('UTC'));
                        
                        // Convert to Australian Eastern Time (AEST/AEDT)
                        $australiaTimezone = new DateTimeZone('Australia/Sydney');
                        $sentTime->setTimezone($australiaTimezone);
                        
                        // Format the time
                        $formattedSentTime = $sentTime->format('d/m/Y h:i a');
                        $sentTimeFinal = $formattedSentTime;
                    } else {
                        $sentTimeFinal = "";
                    }

                    $obj1				=  new \App\Models\MailReport;
                    $obj1->user_id		=  Auth::user()->id;
                    $obj1->from_mail 	=  $mail_sender;
                    $obj1->to_mail 		=  $mail_to_arr;
                    $obj1->subject		=  $mail_subject;
                    $obj1->message		=  $mail_body;
                    $obj1->mail_type	=  1;
                    $obj1->client_id	=  $request->partner_id;
                    $obj1->conversion_type = $doc_type;
                    $obj1->mail_body_type	=  "sent";
                    $obj1->uploaded_doc_id =  $lastInsertedDocId;
                    $obj1->fetch_mail_sent_time = $sentTimeFinal;
                    $obj1->type = $request->type;
                    $saved1	=	$obj1->save();
                    if ($saved1) {
                        $response['status'] 	= 	true;
                        $response['message']	=	'Partner sent email uploaded successfully.';
                    } else {
                        $response['status'] 	= 	false;
                        $response['message']	=	'No sent email uploaded.Please try again';
                    }
                    //return redirect()->back()->with('success', 'Sent email uploaded successfully');
                } else {
                    $response['status'] 	= 	false;
                    $response['message']	=	'No sent email uploaded.Please try again';
                    //return redirect()->back()->with('error', Config::get('constants.server_error'));
                }
            } //end foreach
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'No sent email uploaded.Please try again';
        }
        echo json_encode($response);
    }
   
    


    //Delete Student Invoice
    public function deleteStudentRecordByInvoiceId(Request $request)
	{
        $requestData = 	$request->all();
        $invoiceid = $requestData['invoiceid'];
        $invoicetype = $requestData['invoicetype'];
        $partnerid = $requestData['partnerid'];
        $record_get = DB::table('partner_student_invoices')->where('invoice_type',$invoicetype)->where('invoice_id',$invoiceid)->where('partner_id',$partnerid)->delete();
        //dd($record_get);
        //$record_get = 1;
        if($record_get) {
            $response['status'] 	= 	true;
            $response['message']	=	'Record is deleted';
            $sum = DB::table('partner_student_invoices')->where('invoice_type',$invoicetype)->where('partner_id',$partnerid)->sum('amount_aud');
            //dd($sum);
            $response['sum'] = $sum;
            $response['invoiceid']  = $invoiceid;
            $response['invoicetype'] = $invoicetype;
            $response['partnerid']  = $partnerid;
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
            $response['sum']  = 0;
            $response['invoiceid']  = "";
            $response['invoicetype'] = "";
            $response['partnerid']  = "";
        }
        echo json_encode($response);
    }

    //Delete Student Record Invoice
    public function deleteStudentRecordInvoiceByInvoiceId(Request $request)
	{
        $requestData = 	$request->all();
        $id = $requestData['id'];
        $invoicetype = $requestData['invoicetype'];
        $partnerid = $requestData['partnerid'];
        $record_get = DB::table('partner_student_invoices')->where('id',$id)->delete();
        //dd($record_get);
        if($record_get) {
            $response['status'] 	= 	true;
            $response['message']	=	'Record is deleted';
            $sum = DB::table('partner_student_invoices')->where('invoice_type',$invoicetype)->where('partner_id',$partnerid)->sum('amount_aud');
            //dd($sum);
            $response['sum'] = $sum;
            $response['id']  = $id;
            $response['invoicetype'] = $invoicetype;
            $response['partnerid']  = $partnerid;
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
            $response['sum']  = 0;
            $response['id']  = "";
            $response['invoicetype'] = "";
            $response['partnerid']  = "";
        }
        echo json_encode($response);
    }

    //Delete Student Payment Invoice
    public function deleteStudentPaymentInvoiceByInvoiceId(Request $request)
	{
        $requestData = 	$request->all();
        $id = $requestData['id'];
        $invoicetype = $requestData['invoicetype'];
        $partnerid = $requestData['partnerid'];
        $record_get = DB::table('partner_student_invoices')->where('id',$id)->delete();
        //dd($record_get);
        if($record_get) {
            $response['status'] 	= 	true;
            $response['message']	=	'Record is deleted';
            $sum = DB::table('partner_student_invoices')->where('invoice_type',$invoicetype)->where('partner_id',$partnerid)->sum('amount_aud');
            //dd($sum);
            $response['sum'] = $sum;
            $response['id']  = $id;
            $response['invoicetype'] = $invoicetype;
            $response['partnerid']  = $partnerid;
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
            $response['sum']  = 0;
            $response['id']  = "";
            $response['invoicetype'] = "";
            $response['partnerid']  = "";
        }
        echo json_encode($response);
    }
  
  
    //Asssign partner action and save
	public function followupstore_partner(Request $request){
	    $requestData = $request->all(); //echo '<pre>'; print_r($requestData); die;
        $partner_decode_id = base64_encode(convert_uuencode($requestData['partner_id'])); //dd($partner_decode_id);
        $followup 				    = new \App\Models\Note;
        $followup->client_id		= @$requestData['partner_id'];
		$followup->user_id			= Auth::user()->id;
        $followup->description		= $requestData['assignnote'];

        //Get assigner name
        $assignee_info = \App\Models\Admin::select('id','first_name','last_name')->where('id', $requestData['rem_cat123'])->first();
        if($assignee_info){
            $assignee_name = $assignee_info->first_name;
        } else {
            $assignee_name = 'N/A';
        }

        if(isset($requestData['note_deadline']) && $requestData['note_deadline'] != ''){
            $title = 'Partner assigned action with deadline '.$requestData['note_deadline'].' to '.$assignee_name;
            $recurring_type = $requestData['recurring_type'];
        } else {
            $title = 'Partner assigned action to '.$assignee_name;
            $recurring_type = "";
        }

		$followup->title		    = $title;
        $followup->folloup	        = 1;
        $followup->task_group       =  $requestData['task_group'];
		$followup->assigned_to	    =  @$requestData['rem_cat123'];
        if( isset($requestData['popoverdate']) && $requestData['popoverdate'] != "" ){
            $popoverdateArr = explode("/",$requestData['popoverdate']);
            $popoverdateFormated = $popoverdateArr[2]."-".$popoverdateArr[1]."-".$popoverdateArr[0];
        } else {
            $popoverdateFormated = "";
        }
		$followup->followup_date	=  $popoverdateFormated;
        $followup->type	            =  $requestData['type'];

        //add note deadline
        if(isset($requestData['note_deadline_checkbox']) && $requestData['note_deadline_checkbox'] != ''){
            if($requestData['note_deadline_checkbox'] == 1){
                $note_deadlineArr = explode("/",$requestData['note_deadline']);
                $note_deadlineArrFormated = $note_deadlineArr[2]."-".$note_deadlineArr[1]."-".$note_deadlineArr[0];
                $followup->note_deadline = $note_deadlineArrFormated;
                $followup->deadline_recurring_type = $recurring_type;
            } else {
                $followup->note_deadline = NULL;
                $followup->deadline_recurring_type = NULL;
            }
        } else {
            $followup->note_deadline = NULL;
            $followup->deadline_recurring_type = NULL;
        }

        $saved	=  $followup->save();
        if(!$saved) {
			echo json_encode(array('success' => false, 'message' => 'Please try again', 'clientID' => $partner_decode_id));
		} else {
			$o = new \App\Models\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = @$requestData['rem_cat123'];
	    	$o->module_id = $requestData['partner_id'];
            $o->url = \URL::to('/admin/partners/detail/'.$partner_decode_id);
	    	$o->notification_type = 'partner';
	    	$o->message = 'Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name.' '.date('d/M/Y h:i A');
	    	$o->save();

            //save in activity log
			$objs = new ActivitiesLog;
            $objs->client_id = @$requestData['partner_id'];
            $objs->created_by = Auth::user()->id;

            if(isset($requestData['note_deadline']) && $requestData['note_deadline'] != ''){
                $subject = 'Partner assigned action in group '.$requestData['task_group'].' with deadline '.$requestData['note_deadline'].' to '.$assignee_name;
            } else {
                $subject = 'Partner assigned action in group '.$requestData['task_group'].' to '.@$assignee_name;
            }
            $objs->subject = $subject;
            $objs->description = '<span class="text-semi-bold">'.@$title.'</span><p>'.$requestData['assignnote'].'</p>';
            if(Auth::user()->id != @$requestData['rem_cat123']){
                $objs->use_for = @$requestData['rem_cat123'];
            } else {
                $objs->use_for = "";
            }
            $objs->followup_date = $popoverdateFormated;
            $objs->task_group = 'partner'; //$requestData['task_group'];
            $objs->save();

            echo json_encode(array('success' => true, 'message' => 'Partner successfully assigned action', 'clientID' => $partner_decode_id,'partner_id'=>$requestData['partner_id']));
			exit;
		}
	}
  
    //Get partner all actions
    /*public function partnerActivities(Request $request){ //dd($request->all());
		if(Partner::where('id', $request->id)->exists()){
			$activities = ActivitiesLog::where('client_id', $request->id)->where('task_group', 'partner')->orderby('created_at', 'DESC')->get();
			$data = array();
			foreach($activities as $activit){
				$admin = Admin::where('id', $activit->created_by)->first();
                $data[] = array(
                    'activity_id' => $activit->id,
					'subject' => $activit->subject,
					'createdname' => substr($admin->first_name, 0, 1),
					'name' => $admin->first_name,
					'message' => $activit->description,
					'date' => date('d M Y, H:i A', strtotime($activit->created_at)),
                   'followup_date' => date('d/m/Y',strtotime($activit->followup_date)),
                   'task_group' => $activit->task_group,
                   'pin' => $activit->pin
                );
			}
            $response['status'] 	= 	true;
			$response['data']	=	$data;
		} else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}*/
  
  
    //Fetch all contact list of any partner at create note popup
     public function fetchPartnerContactNo(Request $request){ //dd($request->all());
        if( \App\Models\PartnerPhone::where('partner_id', $request->partner_id)->exists())
        {
            //Fetch All partner contacts
            $partnerContacts = \App\Models\PartnerPhone::select('partner_phone','partner_country_code')->where('partner_id', $request->partner_id)->get();
            //dd($partnerContacts);
            if( !empty($partnerContacts) && count($partnerContacts)>0 ){
                $response['status'] 	= 	true;
                $response['message']	=	'Partner contact is successfully fetched.';
                $response['partnerContacts']	=	$partnerContacts;
            } else {
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['partnerContacts']	=	array();
            }
        }
        else
        {
            if( \App\Models\Partner::where('id', $request->partner_id)->exists()){
                //Fetch All partner contacts
                $partnerContacts = \App\Models\Partner::select('phone as partner_phone','country_code as partner_country_code')->where('id', $request->partner_id)->get();
                //dd($partnerContacts);
                if( !empty($partnerContacts) && count($partnerContacts)>0 ){
                    $response['status'] 	= 	true;
                    $response['message']	=	'Partner contact is successfully fetched.';
                    $response['partnerContacts']	=	$partnerContacts;
                } else {
                    $response['status'] 	= 	false;
                    $response['message']	=	'Please try again';
                    $response['partnerContacts']	=	array();
                }
            }
            else {
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['partnerContacts']	=	array();
            }
        }
        echo json_encode($response);
	}
  
  
  
    //Update student application overall status
    public function updateStudentApplicationOverallStatus(Request $request)
	{
        //dd($request->all());
        if( isset($request->application_overall_status) && $request->application_overall_status == 0){
            $application_overall_status = 1;
        } else if( isset($request->application_overall_status) && $request->application_overall_status == 1){
            $application_overall_status = 0;
        }
        $updatedRows = DB::table('applications')->where('id', $request->application_student_id)->update(['overall_status' => $application_overall_status]);
        // Check if the update was successful
        if ($updatedRows > 0) {
            $response['status'] 	= 	true;
            $response['message']	=	'Student application overall status updated successfully.';
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'No changes made or student application overall not found.Please try again';
        }
        echo json_encode($response);
    }
  
  
    //Add Note To Student
    public function addstudentnote(Request $request){ //dd($request->all());
        //In Student Note
        if(isset($request->noteid) && $request->noteid != ''){
            $obj = \App\Models\Note::find($request->noteid);
        } else {
            $obj = new \App\Models\Note;
        }

        $obj->client_id = $request->student_id; // In student note
        $obj->user_id = Auth::user()->id;
        $obj->title = $request->title;
        $obj->mail_id = $request->mailid;
        $obj->type = 'client';

        if( isset($request->mobileNumber) && $request->mobileNumber != ""){
            $obj->mobile_number = $request->mobileNumber; // Add this line
        }

        //$obj->description = $request->description;
        $partner_encoded_id = base64_encode(convert_uuencode(@$request->partner_id)) ;
        $partner_reference = '<a href="'.url('/admin/partners/detail/'.$partner_encoded_id).'" target="_blank" >'.$request->college_name.'</a>';

        $title = 'Added a note by partner '.$partner_reference;
        if(isset($request->noteid) && $request->noteid != ''){
            $title = 'Updated a note by partner '.$partner_reference;
        }
        if( isset($request->mobileNumber) && $request->mobileNumber != ""){
            //$obj->description = '<span class="text-semi-bold">'.$title.'</span><p>'.$request->description.'</p><p>'.$request->mobileNumber.'</p>';
            $obj->description = '<span class="text-semi-bold">'.$title.'</span><p>'.$request->description.'</p>';
        } else {
            $obj->description = '<span class="text-semi-bold">'.$title.'</span><p>'.$request->description.'</p>';
        }
        $saved = $obj->save();
		if($saved){
            //In Partner activity log
            if($request->vtype == 'partner'){
                $client_encoded_id = base64_encode(convert_uuencode(@$request->student_id)) ;
                $client_reference = '<a href="'.url('/admin/clients/detail/'.$client_encoded_id).'" target="_blank" >'.$request->student_ref_no.'</a>';

                $subject = 'added a note for '.$client_reference;
                if(isset($request->noteid) && $request->noteid != ''){
					$subject = 'updated a note for '.$client_reference;
                }

                $objs = new ActivitiesLog;
                $objs->client_id = $request->partner_id;
				$objs->task_group = $request->vtype; //partner
                $objs->created_by = Auth::user()->id;
                if( isset($request->mobileNumber) && $request->mobileNumber != ""){
                    $objs->description = '<span class="text-semi-bold">'.$request->title.'</span><p>'.$request->description.'</p><p>'.$request->mobileNumber.'</p>';
                } else {
                    $objs->description = '<span class="text-semi-bold">'.$request->title.'</span><p>'.$request->description.'</p>';
                }
                $objs->subject = $subject;
                $objs->save();
            }
            $response['status'] 	= 	true;
            if(isset($request->noteid) && $request->noteid != ''){
                $response['message']	=	'You’ve successfully updated Note';
            }else{
                $response['message']	=	'You’ve successfully added Note';
            }
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
        echo json_encode($response);
	}


    //Get Partner all activity logs
    public function activities(Request $request){
		if(Partner::where('id', $request->partner_id)->exists()){
			$activities = ActivitiesLog::where('client_id', $request->partner_id)->where('task_group', 'partner')->orderby('created_at', 'DESC')->get();
			$data = array();
			foreach($activities as $activit){
				$admin = Admin::where('id', $activit->created_by)->first();
                $data[] = array(
                    'activity_id' => $activit->id,
					'subject' => $activit->subject,
					'createdname' => substr($admin->first_name, 0, 1),
					'name' => $admin->first_name,
					'message' => $activit->description,
					'date' => date('d M Y, H:i A', strtotime($activit->created_at)),
                   'followup_date' => $activit->followup_date,
                   'task_group' => $activit->task_group,
                   'pin' => $activit->pin
                );
			}

			$response['status'] 	= 	true;
			$response['data']	=	$data;
		} else {
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

  
  
    //Update student application commission percentage
    public function updatecommissionpercentage($partner_id)
	{
        //dd($partner_id);
        //get partner commission
        $partnerInfo = DB::table('partners')->select('commission_percentage')->where('id', $partner_id)->first();
        //dd($partnerInfo);
        if($partnerInfo){
            $commission_percentage = $partnerInfo->commission_percentage; //dd($commission_percentage);

            //get all student applications of that partner
            $allApplicationWRTPartnerInfo = DB::table('applications')
            ->where('partner_id', $partner_id)
            ->where(function ($query) {
                $query->where('stage', 'Coe issued')
                    ->orWhere('stage', 'Enrolled')
                    ->orWhere('stage', 'Coe Cancelled');
            })->get(); //dd($allApplicationWRTPartnerInfo);
            if( !empty($allApplicationWRTPartnerInfo) && count($allApplicationWRTPartnerInfo) >0 ){
                foreach($allApplicationWRTPartnerInfo as $appkey=>$appval){
                    $appFeeInfo = DB::table('application_fee_options')->where('app_id', $appval->id)->first();
                    //dd($appFeeInfo);
                    if($appFeeInfo){
                        $appFeeOptionTypeInfo = DB::table('application_fee_option_types')->where('fee_id', $appFeeInfo->id)->where('fee_option_type', 2)->get();
                        //dd($appFeeOptionTypeInfo);
                        if( !empty($appFeeOptionTypeInfo) && count($appFeeOptionTypeInfo) >0 ){
                            foreach($appFeeOptionTypeInfo as $optkey=>$optval){
                                //Update commission_percentage
                                if( $optval->commission_percentage == '0.00' ){
                                    $updatedRows = DB::table('application_fee_option_types')
                                    ->where('id', $optval->id)
                                    ->update(['commission_percentage' => $commission_percentage]);
                                    
                                   if ($updatedRows > 0) {
                                        echo "<br/>Success Fee Option Type Id=".$optval->id;
                                    } else {
                                        echo "<br/>Already Updated  Fee Option Type Id=".$optval->id;
                                    }
                                } else {
                                    echo "<br/>Commission percentage is not zero or already updated";
                                }
                                
                            } //end foreach
                        }
                    }
                } //end foreach
            }
        }
    }



    //Update student application commission claimed and other
    public function updatecommissionclaimed($partner_id)
	{
        //dd($partner_id);
        //get partner commission
        $partnerInfo = DB::table('partners')->select('commission_percentage')->where('id', $partner_id)->first();
        //dd($partnerInfo);
        if($partnerInfo){
            $commission_percentage = $partnerInfo->commission_percentage; //dd($commission_percentage);

            //get all student applications of that partner
            $allApplicationWRTPartnerInfo = DB::table('applications')
            ->where('partner_id', $partner_id)
            ->where(function ($query) {
                $query->where('stage', 'Coe issued')
                    ->orWhere('stage', 'Enrolled')
                    ->orWhere('stage', 'Coe Cancelled');
            })->get(); //dd($allApplicationWRTPartnerInfo);
            if( !empty($allApplicationWRTPartnerInfo) && count($allApplicationWRTPartnerInfo) >0 ){
                foreach($allApplicationWRTPartnerInfo as $appkey=>$appval){
                    $appFeeInfo = DB::table('application_fee_options')->where('app_id', $appval->id)->first();
                    //dd($appFeeInfo);
                    if($appFeeInfo){
                        $appFeeOptionTypeInfo = DB::table('application_fee_option_types')->where('fee_id', $appFeeInfo->id)->where('fee_option_type', 2)->get();
                        //dd($appFeeOptionTypeInfo);
                        if( !empty($appFeeOptionTypeInfo) && count($appFeeOptionTypeInfo) >0 ){
                            $totl_commission_claimed = 0;
                            $sum_of_option_yes = 0;
                            $sum_of_option_no = 0;
                            foreach($appFeeOptionTypeInfo as $optkey=>$optval){
                                //Update commission_claimed = commission + adjustment_discount_entry
                                if( $optval->commission_claimed == '0.00' ){
                                    $commission_claimed = $optval->commission +  $optval->adjustment_discount_entry;

                                    $updatedRows = DB::table('application_fee_option_types')
                                    ->where('id', $optval->id)
                                    ->update(['commission_claimed' => $commission_claimed]);
                                    if ($updatedRows > 0) {
                                        echo "<br/>Success Fee Option Type Id=".$optval->id;
                                    } else {
                                        echo "<br/>Already Updated  Fee Option Type Id=".$optval->id;
                                    }

                                    $totl_commission_claimed +=  $commission_claimed;
                                    if($optval->claimed_or_not == 'Yes') {
                                        $sum_of_option_yes +=  $commission_claimed;
                                    }
                                    if($optval->claimed_or_not == 'No') {
                                        $sum_of_option_no +=  $commission_claimed;
                                    }
                                } else {
                                    $totl_commission_claimed +=  $optval->commission_claimed;
                                    if($optval->claimed_or_not == 'Yes') {
                                        $sum_of_option_yes +=  $optval->commission_claimed;
                                    }
                                    if($optval->claimed_or_not == 'No') {
                                        $sum_of_option_no +=  $optval->commission_claimed;
                                    }
                                }
                            } //end foreach

                            $updatedRows1 = DB::table('application_fee_options')
                            ->where('id',  $appFeeInfo->id)
                            ->update([
                                'commission_as_per_fee_reported' => $totl_commission_claimed,
                                'commission_paid_as_per_fee_reported' => $sum_of_option_yes,
                                'commission_pending' => $sum_of_option_no
                            ]);
                          
                            if ($updatedRows1 > 0) {
                                echo "<br/>Success Full Fee Option Id=".$appFeeInfo->id;
                            } else {
                                echo "<br/>Already Updated Full Fee Option Id=".$appFeeInfo->id;
                            }
                        }
                    }
                } //end foreach
            }
        }
    } 
  
  
  
   //save student note
    public function saveStudentNote(Request $request)
	{
        //dd($request->all());
        $updatedRows = DB::table('applications')->where('id', $request->rowId)->update(['student_add_notes' => $request->note]);
        // Check if the update was successful
        if ($updatedRows > 0) {
            $response['status'] 	= 	true;
            $response['message']	=	'Student note added successfully.';
            $response['studentId']	=   $request->rowId;
            $response['studentNote']	= $request->note;
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'No changes made or student not found.Please try again';
            $response['studentId']	=  "";
            $response['studentNote']	= "";
        }
        echo json_encode($response);
    }
  
    
    //Get partner notes
    public function getPartnerNotes(Request $request){
		$client_id = $request->clientid;
		$type = $request->type;

		$notelist = \App\Models\Note::where('client_id',$client_id)->whereNull('assigned_to')->whereNull('task_group')->where('type',$type)->orderby('pin', 'DESC')->orderBy('created_at', 'DESC')->get();
		ob_start();
		foreach($notelist as $list){
			$admin = \App\Models\Admin::where('id', $list->user_id)->first();
			?>
			<div class="note_col" id="note_id_<?php echo $list->id; ?>">
                <div class="note-icon bg-primary text-white" style="width: 50px;height: 50px;line-height: 50px;font-size: 20px;margin-right: 20px;border-radius: 50%;text-align: center;">
                    <span><?php echo substr($admin->first_name, 0, 1);?></span>
                </div>
				<div class="note_content">
					<!--<h4><a class="viewnote" data-id="<?php //echo $list->id; ?>" href="javascript:;"><?php //echo @$list->title == "" ? config('constants.empty') : str_limit(@$list->title, '19', '...'); ?> </a></h4>-->
					<div class="note-title" style="display: inline-block;margin-right: 60px;">
                        <p><b><?php echo $admin->first_name;?></b>  Added Note with Title <b><?php echo @$list->title; ?></b></p>
                    </div>

                    <div class="note-date" style="display: inline-block;">
                        <span class="text-job"><?php echo date('d M Y, H:i A', strtotime($list->updated_at));?></span>
                    </div>

                    <div class="right" style="float: right;width: 15px;">
						<div class="dropdown d-inline dropdown_ellipsis_icon">
							<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
							<div class="dropdown-menu">
								<a class="dropdown-item opennoteform" data-id="<?php echo $list->id; ?>" href="javascript:;">Edit</a>
                                <?php if(Auth::user()->role == 1){ ?>
								<a data-id="<?php echo $list->id; ?>" data-href="deletenote" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
								<?php }?>
                                <?php if($list->pin == 1){ ?>
									<a data-id="<?php echo $list->id; ?>"  class="dropdown-item pinnote" href="javascript:;" >UnPin</a>
								<?php }else{ ?>
									<a data-id="<?php echo $list->id; ?>"  class="dropdown-item pinnote" href="javascript:;" >Pin</a>
								<?php } ?>
							</div>
						</div>
					</div>

                    <?php if($list->pin == 1){ ?>
                        <div class="pined_note"><i class="fa fa-thumbtack"></i></i></div>
                    <?php } ?>
				</div>
				<div class="extra_content">
				    <p><?php echo @$list->description; ?></p>

                    <?php if( isset($list->mobile_number) && $list->mobile_number != ""){ ?>
                        <p><?php echo @$list->mobile_number; ?></p>
                    <?php } ?>

					<!--<div class="left">
						<div class="author">
							<a href="<?php //echo \URL::to('/admin/users/view/'.$admin->id); ?>"><?php //echo substr($admin->first_name, 0, 1); ?></a>
						</div>
						<div class="note_modify">
							<small>Last Modified <span><?php //echo date('d/m/Y h:i A', strtotime($list->updated_at)); ?></span></small>
							<?php //echo $admin->first_name.' '.$admin->last_name; ?>
						</div>
					</div>-->

				</div>
			</div>
			<?php
		}
		return ob_get_clean();
	}
  
  
   //Partner upload document
    public function uploadpartnerdocumentupload(Request $request){ //dd($request->all());
        $id = $request->clientid;
        //get partner info
        $partner_info = \App\Models\Partner::select('email')->where('id', $id)->first(); //dd($partner_info);
        if(!empty($partner_info)){
            $partner_unique_email = $partner_info->email;
        } else {
            $partner_unique_email = "";
        }
        if ($request->hasfile('document_upload')) {
            if(!is_array($request->file('document_upload'))){
				$files[] = $request->file('document_upload');
			}else{
				$files = $request->file('document_upload');
			}

		    foreach ($files as $file) {
                $size = $file->getSize();
                $fileName = $file->getClientOriginalName();
                $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
                $fileExtension = $file->getClientOriginalExtension();
                //echo $nameWithoutExtension."===".$fileExtension;
                //$document_upload = $this->uploadrenameFile($file, Config::get('constants.documents'));

                $name = time() . $file->getClientOriginalName();
                $filePath = $partner_unique_email.'/partner_document/'. $name;
                Storage::disk('s3')->put($filePath, file_get_contents($file));

                $obj = new \App\Models\Document;
                $obj->file_name = $nameWithoutExtension;
                $obj->filetype = $fileExtension;
                $obj->user_id = Auth::user()->id;
                // Get the full URL of the uploaded file
                $fileUrl = Storage::disk('s3')->url($filePath);
                $obj->myfile = $fileUrl;
                $obj->myfile_key = $name;

                $obj->client_id = $id;
                $obj->type = $request->type;
                $obj->file_size = $size;
                $obj->doc_type = '';
                $saved = $obj->save();
            }

			if($saved){
				if($request->type == 'partner'){
                    $subject = 'added 1 partner document';
                    $objs = new ActivitiesLog;
                    $objs->client_id = $id;
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->save();
                }
				$response['status'] 	= 	true;
				$response['message']	=	'You have successfully uploaded your partner document';
				$fetchd = \App\Models\Document::where('client_id',$id)
                        ->where(function ($query) {
                            $query->whereNull('doc_type')
                                ->orWhere('doc_type', '');
                        })->where('type','partner')->orderby('created_at', 'DESC')->get();
				ob_start();
				foreach($fetchd as $fetch){
					$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                    ?>
					<tr class="drow" id="id_<?php echo $fetch->id; ?>">
                        <td style="white-space: initial;">
                            <div data-id="{{$fetch->id}}" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
                                <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name; ?><?php echo '.'.$fetch->filetype; ?></span>
                            </div>
                        </td>
						<td style="white-space: initial;"><?php echo $admin->first_name; ?></td>
                        <td style="white-space: initial;"><?php echo date('d/m/Y', strtotime($fetch->created_at)); ?></td>
						<td>
							<div class="dropdown d-inline">
								<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
								<div class="dropdown-menu">
									<a class="dropdown-item renamedoc" href="javascript:;">Rename</a>
									<?php
                                    if( isset($fetch->myfile_key) && $fetch->myfile_key !="")
                                    { ?>
                                        <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                        <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">PDF</a>
                                        <a download class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Download</a>
                                    <?php
                                    }
                                    else
                                    {
                                        if (filter_var($fetch->myfile, FILTER_VALIDATE_URL)) { //String is a valid URL
                                        ?>
                                            <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                            <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">PDF</a>
                                            <a download class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Download</a>
                                        <?php
                                        }
                                        else
                                        { //String is not a valid URL
                                        ?>
                                            <a target="_blank" class="dropdown-item" href="<?php echo asset('img/documents'); ?>/<?php echo $fetch->myfile; ?>">Preview</a>
                                            <?php
                                            $explodeimg = explode('.',$fetch->myfile);
                                            if($explodeimg[1] == 'jpg'|| $explodeimg[1] == 'png'|| $explodeimg[1] == 'jpeg'){ ?>
                                                <a target="_blank" class="dropdown-item" href="<?php echo \URL::to('/admin/document/download/pdf'); ?>/<?php echo $fetch->id; ?>">PDF</a>
                                            <?php } ?>
                                            <a download class="dropdown-item" href="<?php echo asset('img/documents'); ?>/<?php echo $fetch->myfile; ?>">Download</a>
                                        <?php
                                        }
                                    }
                                    ?>

									<a data-id="<?php echo $fetch->id; ?>" class="dropdown-item deletenote" data-href="deletedocs" href="javascript:;" >Delete</a>
								</div>
							</div>
						</td>
					</tr>
					<?php
				}
				$data = ob_get_clean();
				ob_start();
				foreach($fetchd as $fetch){
					$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
					?>
					<div class="grid_list">
						<div class="grid_col">
							<div class="grid_icon">
								<i class="fas fa-file-image"></i>
							</div>
							<div class="grid_content">
								<span id="grid_<?php echo $fetch->id; ?>" class="gridfilename"><?php echo $fetch->file_name; ?></span>
								<div class="dropdown d-inline dropdown_ellipsis_icon">
									<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
									<div class="dropdown-menu">
										<?php
                                        if( isset($fetch->myfile_key) && $fetch->myfile_key !="")
                                        { ?>
                                            <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                            <a download class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Download</a>
                                        <?php
                                        }
                                        else
                                        {
                                            if (filter_var($fetch->myfile, FILTER_VALIDATE_URL)) { //String is a valid URL
                                            ?>
                                                <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                                <a download class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Download</a>
                                            <?php
                                            }
                                            else
                                            { //String is not a valid URL
                                            ?>
                                                <a target="_blank" class="dropdown-item" href="<?php echo asset('img/documents'); ?>/<?php echo $fetch->myfile; ?>">Preview</a>
                                                <a download class="dropdown-item" href="<?php echo asset('img/documents'); ?>/<?php echo $fetch->myfile; ?>">Download</a>
                                            <?php
                                            }
                                        }
                                        ?>
										<a data-id="<?php echo $fetch->id; ?>" class="dropdown-item deletenote" data-href="deletedocs" href="javascript:;" >Delete</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
				$griddata = ob_get_clean();
				$response['data']	= $data;
				$response['griddata'] = $griddata;
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		 }else{
			 $response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		 }
		 echo json_encode($response);
	}
    
}
