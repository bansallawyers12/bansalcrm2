<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Admin;
use App\ActivitiesLog;
use App\ServiceFeeOption;
use App\ServiceFeeOptionType;
use App\OnlineForm;
use Auth;
use Config;
use PDF;
use App\CheckinLog;
use App\Note;
use App\clientServiceTaken;
use App\AccountClientReceipt;

use Illuminate\Support\Facades\Storage;

use App\Application;
use DataTables;

class ClientsController extends Controller
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
	    $roles = \App\UserRole::find(Auth::user()->role);
		$newarray = json_decode($roles->module_access);
		$module_access = (array) $newarray;
		if(array_key_exists('20',  $module_access)) {
		    $query 		= Admin::where('is_archived', '=', '0')->where('role', '=', '7')->whereNull('is_deleted');

		$totalData 	= $query->count();	//for all data
		if ($request->has('client_id'))
		{
			$client_id 		= 	$request->input('client_id');
			if(trim($client_id) != '')
			{
				$query->where('client_id', '=', $client_id);
			}
		}
			if ($request->has('type'))
		{
			$type 		= 	$request->input('type');
			if(trim($type) != '')
			{
				$query->where('type', 'LIKE', $type);

			}
		}

		if ($request->has('name'))
		{
			$name 		= 	$request->input('name');
			if(trim($name) != '')
			{
				$query->where('first_name', 'LIKE', '%'.$name.'%');

			}
		}

		if ($request->has('email'))
		{
			$email 		= 	$request->input('email');
			if(trim($email) != '')
			{
				//$query->where('email', $email);
				$query->where('email', 'LIKE','%'.$email.'%')->orwhere('att_email', 'LIKE','%'.$email.'%');
            }
		}

		if ($request->has('phone'))
		{
			$phone 		= 	$request->input('phone');
			if(trim($phone) != '')
			{
				//$query->where('phone', $phone);
                $query->where('phone', 'LIKE','%'.$phone.'%')->orwhere('att_phone', 'LIKE','%'.$phone.'%');
            }
		}
		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);

		}else{
		    $query 		= Admin::where('id', '=', '')->where('role', '=', '7');
		    $lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		    $totalData = 0;
		}
		return view('Admin.clients.index', compact(['lists', 'totalData']));

		//return view('Admin.clients.index');
	}

	public function archived(Request $request)
	{
		$query 		= Admin::where('is_archived', '=', '1')->where('role', '=', '7')->whereNull('is_deleted');
        $totalData 	= $query->count();	//for all data
        $lists		= $query->sortable(['id' => 'desc'])->paginate(20);
        return view('Admin.archived.index', compact(['lists', 'totalData']));
    }

	public function prospects(Request $request)
	{

		return view('Admin.prospects.index');

	}

	public function create(Request $request)
	{
		//check authorization end
		//return view('Admin.users.create',compact(['usertype']));

		return view('Admin.clients.create');
	}

	public function store(Request $request)
	{
		//check authorization end
		if ($request->isMethod('post'))
		{
			$this->validate($request, [
										'first_name' => 'required|max:255',
										'last_name' => 'required|max:255',
										'email' => 'required|max:255|unique:admins,email',
										'phone' => 'required|max:255|unique:admins,phone',
									//	'client_id' => 'required|max:255|unique:admins,client_id'
									  ]);

			$requestData 		= 	$request->all();
			$related_files = '';
	        if(isset($requestData['related_files'])){
	            for($i=0; $i<count($requestData['related_files']); $i++){
	                $related_files .= $requestData['related_files'][$i].',';
	            }

	        }
	        $dob = '';
	        if($requestData['dob'] != ''){
	           $dobs = explode('/', $requestData['dob']);
	          $dob = $dobs[2].'-'.$dobs[1].'-'. $dobs[0];
	        }
	        $visaExpiry = '';
	        if($requestData['visaExpiry'] != ''){
	           $visaExpirys = explode('/', $requestData['visaExpiry']);
	          $visaExpiry = $visaExpirys[2].'-'.$visaExpirys[1].'-'. $visaExpirys[0];
	        }
			$first_name = substr(@$requestData['first_name'], 0, 4);
			$obj				= 	new Admin;
			$obj->first_name	=	@$requestData['first_name'];
			$obj->last_name	=	@$requestData['last_name'];
			$obj->age	=	@$requestData['age'];
			$obj->gender	=	@$requestData['gender'];
			$obj->martial_status	=	@$requestData['martial_status'];
			$obj->contact_type	=	@$requestData['contact_type'];
			$obj->email_type	=	@$requestData['email_type'];
			$obj->service	=	@$requestData['service'];
			$obj->dob	=	@$dob;
			$obj->related_files	=	rtrim($related_files,',');
			$obj->email	=	@$requestData['email'];
			$obj->phone	=	@$requestData['phone'];
			$obj->address	=	@$requestData['address'];
			
			if( isset($requestData['address']) && $requestData['address'] != ""){
                //$address = "16-18, Argyle Street, Camden, London, WC1H 8EG, United Kingdom";
                $address = @$requestData['address'];
                $result = app('geocoder')->geocode($address)->get();
                $coordinates = $result[0]->getCoordinates();
                $lat = $coordinates->getLatitude();
                $long = $coordinates->getLongitude();
                //echo "lat==".$lat;
                //echo "long==".$long; die();

                $obj->latitude	=	$lat;
                $obj->longitude	=	$long;
            }
			
			$obj->city	=	@$requestData['city'];
			$obj->visa_opt = @$requestData['visa_opt'];
			$obj->state	=	@$requestData['state'];
			$obj->zip	=	@$requestData['zip'];
			$obj->country	=	@$requestData['country'];
			$obj->preferredIntake	=	@$requestData['preferredIntake'];
			$obj->country_passport			=	@$requestData['country_passport'];
			$obj->passport_number			=	@$requestData['passport_number'];
			$obj->visa_type			=		@$requestData['visa_type'];
			$obj->visaExpiry			=	@$visaExpiry;
			$obj->applications	=	@$requestData['applications'];
			$obj->assignee	=	@$requestData['assign_to'];
			$obj->status	=	$requestData['status'] ?? 1;
			$obj->lead_quality	=	@$requestData['lead_quality'];
			$obj->att_phone	=	@$requestData['att_phone'];
			$obj->att_country_code	=	@$requestData['att_country_code'];
			$obj->att_email	=	@$requestData['att_email'];
			$obj->nomi_occupation	=	@$requestData['nomi_occupation'];
			$obj->skill_assessment	=	@$requestData['skill_assessment'];
			$obj->high_quali_aus	=	@$requestData['high_quali_aus'];
			$obj->high_quali_overseas	=	@$requestData['high_quali_overseas'];
			$obj->relevant_work_exp_aus	=	@$requestData['relevant_work_exp_aus'];
			$obj->relevant_work_exp_over	=	@$requestData['relevant_work_exp_over'];

			$obj->married_partner	=	@$requestData['married_partner'];
			$obj->total_points	=	@$requestData['total_points'];
			$obj->start_process	=	@$requestData['start_process'];
			$obj->comments_note	=	@$requestData['comments_note'];
			$obj->type	=	@$requestData['type'];
			$followers = '';
			if(isset($requestData['followers']) && !empty($requestData['followers'])){
				foreach($requestData['followers'] as $follows){
					$followers .= $follows.',';
				}
			}
			$obj->followers	=	rtrim($followers,',');
			$obj->source	=	@$requestData['source'];
			if(isset($requestData['tagname']) && !empty($requestData['tagname'])){
			$obj->tagname	=	implode(',',@$requestData['tagname']);
			}
			if(isset($requestData['naati_py']) && !empty($requestData['naati_py'])){
			$obj->naati_py	=	implode(',',@$requestData['naati_py']);
			}
			if(@$requestData['source'] == 'Sub Agent' ){
				$obj->agent_id	=	@$requestData['subagent'];
			}
			else{
				$obj->agent_id	=	'';
			}
			$obj->role	=	7;

			/* Profile Image Upload Function Start */
			if($request->hasfile('profile_img'))
			{
				$profile_img = $this->uploadFile($request->file('profile_img'), Config::get('constants.profile_imgs'));
			}
			else
			{
				$profile_img = NULL;
			}
		    /* Profile Image Upload Function End */
			$obj->profile_img			=	@$profile_img;
			if(@$requestData['client_id'] != ''){
			    $obj->client_id	=	@$requestData['client_id'];
			}
			$saved				=	$obj->save();
			if($requestData['client_id'] == ''){
		    	$objs							= 	Admin::find($obj->id);
		    	$objs->client_id	=	strtoupper($first_name).date('ym').$objs->id;
		    	$saveds				=	$objs->save();
			}
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				//return Redirect::to('/admin/clients')->with('success', 'Clients Added Successfully');
				return Redirect::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$obj->id)))->with('success', 'Clients Added Successfully');
			}
		}

		return view('Admin.clients.create');
	}

	public function downloadpdf(Request $request, $id = NULL){
	    	$fetchd = \App\Document::where('id',$id)->first();
	    	$data = ['title' => 'Welcome to codeplaners.com','image' => $fetchd->myfile];
     $pdf = PDF::loadView('myPDF', $data);

     return $pdf->stream('codeplaners.pdf');
	}
	public function edit(Request $request, $id = NULL)
	{
		//check authorization end

		if ($request->isMethod('post'))
		{
			$requestData 		= 	$request->all();
			//echo '<pre>'; print_r($requestData); die;
          
            //Get Db values of related files
			$db_arr = Admin::select('related_files')->where('id', $requestData['id'])->get();
          
			$this->validate($request, [
										'first_name' => 'required|max:255',
										'last_name' => 'required|max:255',
										'email' => 'required|max:255|unique:admins,email,'.$requestData['id'],
										'phone' => 'required|max:255|unique:admins,phone,'.$requestData['id'],
										//'client_id' => 'required|max:255|unique:admins,client_id,'.$requestData['id']

									  ]);
				$related_files = '';
	        if(isset($requestData['related_files'])){
	            for($i=0; $i<count($requestData['related_files']); $i++){
	                $related_files .= $requestData['related_files'][$i].',';
	            }

	        }
	         $dob = '';
	        if(array_key_exists("dob",$requestData) && $requestData['dob'] != ''){
	           $dobs = explode('/', $requestData['dob']);
	          $dob = $dobs[2].'-'.$dobs[1].'-'. $dobs[0];
	        }
	         $visaExpiry = '';
	        if(array_key_exists("visaExpiry",$requestData) && $requestData['visaExpiry'] != '' ){
	           $visaExpirys = explode('/', $requestData['visaExpiry']);
	          $visaExpiry = $visaExpirys[2].'-'.$visaExpirys[1].'-'. $visaExpirys[0];
	        }
			$obj		= 	Admin::find(@$requestData['id']);
			$first_name = substr(@$requestData['first_name'], 0, 4);
				$obj->first_name	=	@$requestData['first_name'];
			$obj->last_name	=	@$requestData['last_name'];
			$obj->age	=	@$requestData['age'];
			$obj->gender	=	@$requestData['gender'];
			$obj->martial_status	=	@$requestData['martial_status'];
			$obj->contact_type	=	@$requestData['contact_type'];
			$obj->email_type	=	@$requestData['email_type'];
			$obj->service	=	@$requestData['service'];
			$obj->dob	=	@$dob;
			$obj->related_files	=	rtrim($related_files,',');
			$obj->email	=	@$requestData['email'];
			$obj->phone	=	@$requestData['phone'];
			$obj->address	=	@$requestData['address'];
			
			if( isset($requestData['address']) && $requestData['address'] != ""){
                //$address = "16-18, Argyle Street, Camden, London, WC1H 8EG, United Kingdom";
                $address = @$requestData['address'];
                $result = app('geocoder')->geocode($address)->get();
                $coordinates = $result[0]->getCoordinates();
                $lat = $coordinates->getLatitude();
                $long = $coordinates->getLongitude();
                //echo "lat==".$lat;
                //echo "long==".$long; die();

                $obj->latitude	=	$lat;
                $obj->longitude	=	$long;
            }

			$obj->city	=	@$requestData['city'];
			$obj->state	=	@$requestData['state'];
			$obj->zip	=	@$requestData['zip'];
			$obj->country	=	@$requestData['country'];
			$obj->visa_opt = @$requestData['visa_opt'];
			$obj->preferredIntake	=	@$requestData['preferredIntake'];
			$obj->country_passport			=	@$requestData['country_passport'];
			$obj->passport_number			=	@$requestData['passport_number'];
			$obj->visa_type			=		@$requestData['visa_type'];
			$obj->visaExpiry			=	@$visaExpiry;
			$obj->applications	=	@$requestData['applications'];
          
			//$obj->assignee	=	@$requestData['assign_to'];
            if( isset($requestData['assign_to']) && is_array($requestData['assign_to']) ){
                if( count($requestData['assign_to']) >1 ) {
                    $obj->assignee	=  implode(",", $requestData['assign_to']);
                } else if( count($requestData['assign_to']) == 1 ) {
                    $obj->assignee	=  $requestData['assign_to'][0];
                } else {
                    $obj->assignee	= "";
                }
            }
          
			$obj->status	=	@$requestData['status'];
			$obj->lead_quality	=	@$requestData['lead_quality'];
			$obj->att_phone	=	@$requestData['att_phone'];
			$obj->att_country_code	=	@$requestData['att_country_code'];
			$obj->att_email	=	@$requestData['att_email'];
			$obj->nomi_occupation	=	@$requestData['nomi_occupation'];
			$obj->skill_assessment	=	@$requestData['skill_assessment'];
			$obj->high_quali_aus	=	@$requestData['high_quali_aus'];
			$obj->high_quali_overseas	=	@$requestData['high_quali_overseas'];
			$obj->relevant_work_exp_aus	=	@$requestData['relevant_work_exp_aus'];
			$obj->relevant_work_exp_over	=	@$requestData['relevant_work_exp_over'];

			$obj->married_partner	=	@$requestData['married_partner'];
			$obj->total_points	=	@$requestData['total_points'];
			$obj->start_process	=	@$requestData['start_process'];
			$obj->comments_note	=	@$requestData['comments_note'];
			$obj->type	=	@$requestData['type'];
			$followers = '';
			if(isset($requestData['followers']) && !empty($requestData['followers'])){
				foreach($requestData['followers'] as $follows){
					$followers .= $follows.',';
				}
			}
			$obj->followers	=	rtrim($followers,',');
			$obj->source	=	@$requestData['source'];
			if(isset($requestData['tagname']) && !empty($requestData['tagname'])){
			$obj->tagname	=	implode(',',@$requestData['tagname']);
			}

				if(isset($requestData['naati_py']) && !empty($requestData['naati_py'])){
			$obj->naati_py	=	implode(',',@$requestData['naati_py']);
			}else{
			   	$obj->naati_py	=	'';
			}
			if(@$requestData['source'] == 'Sub Agent' ){
				$obj->agent_id	=	@$requestData['subagent'];
			}
			else{
				$obj->agent_id	=	'';
			}

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
			
			 $obj->manual_email_phone_verified	=	@$requestData['manual_email_phone_verified'];
			 
			$saved							=	$obj->save();
			if($requestData['client_id'] == ''){
		    	$objs							= 	Admin::find($obj->id);
		    	$objs->client_id	=	strtoupper($first_name).date('ym').$objs->id;
		    	$saveds				=	$objs->save();
			}else{
			    $objs							= 	Admin::find($obj->id);
		    	$objs->client_id	=	$requestData['client_id'];
		    	$saveds				=	$objs->save();
			}
			$route=$request->route;
			if(strpos($request->route,'?')){
				$position=strpos($request->route,'?');
				if ($position !== false) {
					$route = substr($request->route, 0, $position);
				}
			}

			// dd($route);
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
             else  if($route==url('/admin/assignee')){
				$subject = 'Lead status has changed to '.@$requestData['status'].' from '. \Auth::user()->first_name;
				$objs = new ActivitiesLog;
				$objs->client_id = $request->id;
				$objs->created_by = \Auth::user()->id;
				$objs->subject = $subject;
				$objs->save();

				return redirect()->route('assignee.index')->with('success','Assignee updated successfully');
			}

			else
			{
              
              //Code for addition of simiar related files in added users account  
                    if(isset($requestData['related_files']))
                    {
                        for($j=0; $j<count($requestData['related_files']); $j++){
                            if(Admin::where('id', '=', $requestData['related_files'][$j])->exists())
                            {
                                $objsY = Admin::select('id', 'related_files')->where('id', $requestData['related_files'][$j])->get();
                                if(!empty($objsY)){
                                    if($objsY[0]->related_files != ""){
                                        $related_files_string = $objsY[0]->related_files;
                                        $commaPosition = strpos($related_files_string, ',');
                                        if ($commaPosition !== false) { //If comma is exist
                                            $related_files_string_Arr = explode(",",$related_files_string);
                                            array_push($related_files_string_Arr, $requestData['id']);
                                            // Remove duplicate elements
                                            $uniqueArray = array_unique($related_files_string_Arr);

                                            // Reindex the array
                                            $uniqueArray = array_values($uniqueArray);

                                            $related_files_latest = implode(",",$uniqueArray);
                                        } else { //If comma is not exist
                                            $related_files_string_Arr = array($objsY[0]->related_files);
                                            array_push($related_files_string_Arr, $requestData['id']);
                                            // Remove duplicate elements
                                             $uniqueArray = array_unique($related_files_string_Arr);

                                             // Reindex the array
                                             $uniqueArray = array_values($uniqueArray);

                                            $related_files_latest = implode(",",$uniqueArray);
                                        }
                                    } else {
                                        $related_files_latest = $requestData['id'];
                                    }
                                    Admin::where('id', $requestData['related_files'][$j])->update(['related_files' => $related_files_latest]);
                                }
                            }
                        } //end foreach
                    }

              //Code for removal of simiar related files in added users account                  
              if( isset($requestData['related_files'])  || !isset($requestData['related_files']) )
              {

                  if( isset($requestData['related_files']) ) {
                    $req_arr11 = $requestData['related_files'];
                  } else {
                    $req_arr11 = array();
                  }

                  if( !empty($db_arr)  ){
                      $commaPosition11 = strpos($db_arr[0]->related_files, ',');
                      if ($commaPosition11 !== false) { //If comma is exist
                        $db_arr11 = explode(",",$db_arr[0]->related_files);
                      } else { //If comma is not exist
                        $db_arr11 = array($db_arr[0]->related_files);
                      }

                      //echo "<pre>db_arr11=";print_r($db_arr11);
                      //echo "<pre>req_arr11=";print_r($req_arr11);
                      $diff_arr = array_diff( $db_arr11,$req_arr11 );
                      //echo "<pre>diff_arr=";print_r($diff_arr);
                      $diff_arr = array_values($diff_arr);
                      //echo "<pre>diff_arr=";print_r($diff_arr);die;
                  }


                  if( isset($diff_arr) && !empty($diff_arr))
                  {
                      for($k=0; $k<count($diff_arr); $k++)
                      {
                          if(Admin::where('id', '=', $diff_arr[$k])->exists())
                          {
                              $rel_data_arr = Admin::select('related_files')->where('id', $diff_arr[$k])->get();
                              if( !empty($rel_data_arr) ){
                                  $commaPosition1 = strpos($rel_data_arr[0]->related_files, ',');
                                  if ($commaPosition1 !== false) { //If comma is exist
                                      $rel_data_exploded_arr = explode(",",$rel_data_arr[0]->related_files);
                                      $key_search = array_search($requestData['id'], $rel_data_exploded_arr);
                                      if ($key_search !== false) {
                                          unset($rel_data_exploded_arr[$key_search]);
                                      }
                                      $rel_data_exploded_arr = array_values($rel_data_exploded_arr);
                                      //print_r($rel_data_exploded_arr);
                                      $related_files_updated = implode(",",$rel_data_exploded_arr);

                                      Admin::where('id', $diff_arr[$k])->update(['related_files' => $related_files_updated]);

                                  } else { //If comma is not exist
                                      if ($rel_data_arr[0]->related_files == $requestData['id']) {
                                          $related_files_updated = "";
                                          Admin::where('id', $diff_arr[$k])->update(['related_files' => $related_files_updated]);
                                      }
                                  }
                              }
                          }
                      }
                  }
              }
              
              return Redirect::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$requestData['id'])))->with('success', 'Clients Edited Successfully');
			}
		}

		else
		{
			if(isset($id) && !empty($id))
			{

				$id = $this->decodeString($id);
				if(Admin::where('id', '=', $id)->where('role', '=', '7')->exists())
				{
					$fetchedData = Admin::find($id);
					return view('Admin.clients.edit', compact(['fetchedData']));
				}
				else
				{
					return Redirect::to('/admin/clients')->with('error', 'Clients Not Exist');
				}
			}
			else
			{
				return Redirect::to('/admin/clients')->with('error', Config::get('constants.unauthorized'));
			}
		}

	}

	public function detail(Request $request, $id = NULL){ 

	     if(isset($request->t)){
    	    if(\App\Notification::where('id', $request->t)->exists()){
    	       $ovv =  \App\Notification::find($request->t);
    	       $ovv->receiver_status = 1;
    	       $ovv->save();
    	    }
	    }
		if(isset($id) && !empty($id))
			{
				$encodeId = $id;
				$id = $this->decodeString($id); //dd($id);
				if(Admin::where('id', '=', $id)->where('role', '=', '7')->exists())
				{
					$fetchedData = Admin::find($id);
					return view('Admin.clients.detail', compact(['fetchedData','encodeId']));
				}
				else
				{
					return Redirect::to('/admin/clients')->with('error', 'Clients Not Exist');
				}
			}
			else
			{
				return Redirect::to('/admin/clients')->with('error', Config::get('constants.unauthorized'));
			}
	}
	
	//Update session to be complete
    public function updatesessioncompleted(Request $request,CheckinLog $checkinLog)
    {
        $data = $request->all(); //dd($data['client_id']);
        $sessionExist = CheckinLog::where('client_id', $data['client_id'])
        ->where('status', 2)
        ->update(['status' => 1]);
        if($sessionExist){
            $response['status'] 	= 	true;
            $response['message']	=	'Session completed successfully';
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
        }
        echo json_encode($response);
    }

	public function getrecipients(Request $request){
		$squery = $request->q; //dd($squery);
		if($squery != ''){
				$d = '';
			 $clients = \App\Admin::where('is_archived', '=', 0)
       ->where('role', '=', 7)
       ->where(
           function($query) use ($squery) {
             return $query
                    ->where('email', 'LIKE', '%'.$squery.'%')
                    ->orwhere('first_name', 'LIKE','%'.$squery.'%')->orwhere('last_name', 'LIKE','%'.$squery.'%')->orwhere('client_id', 'LIKE','%'.$squery.'%')->orwhere('phone', 'LIKE','%'.$squery.'%')  ->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$squery."%");
            })
            ->get();

            	/* $leads = \App\Lead::where('converted', '=', 0)

       ->where(
           function($query) use ($squery,$d) {
             return $query
                    ->where('email', 'LIKE', '%'.$squery.'%')
                    ->orwhere('first_name', 'LIKE','%'.$squery.'%')->orwhere('last_name', 'LIKE','%'.$squery.'%')->orwhere('phone', 'LIKE','%'.$squery.'%')  ->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$squery."%");

            })
            ->get();*/

			$items = array();
			foreach($clients as $clint){
				$items[] = array('name' => $clint->first_name.' '.$clint->last_name,'email'=>$clint->email,'status'=>$clint->type,'id'=>$clint->id,'cid'=>base64_encode(convert_uuencode(@$clint->id)));
			}

				$litems = array();
		/*	foreach($leads as $lead){
				$litems[] = array('name' => $lead->first_name.' '.$lead->last_name,'email'=>$lead->email,'status'=>'Lead','id'=>$lead->id,'cid'=>base64_encode(convert_uuencode(@$lead->id)));
			}*/
				$m = array_merge($items, $litems);
			echo json_encode(array('items'=>$m));
		}
	}

	public function getonlyclientrecipients(Request $request){
		$squery = $request->q;
		if($squery != ''){
				$d = '';
			 $clients = \App\Admin::where('is_archived', '=', 0)
       ->where('role', '=', 7)
       ->where(
           function($query) use ($squery) {
             return $query
                    ->where('email', 'LIKE', '%'.$squery.'%')
                    ->orwhere('first_name', 'LIKE','%'.$squery.'%')->orwhere('last_name', 'LIKE','%'.$squery.'%')->orwhere('client_id', 'LIKE','%'.$squery.'%')->orwhere('phone', 'LIKE','%'.$squery.'%')  ->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$squery."%");
            })
            ->get();



			$items = array();
			foreach($clients as $clint){
				$items[] = array('name' => $clint->first_name.' '.$clint->last_name,'email'=>$clint->email,'status'=>$clint->type,'id'=>$clint->id,'cid'=>base64_encode(convert_uuencode(@$clint->id)));
			}

				$litems = array();

				$m = array_merge($items, $litems);
			echo json_encode(array('items'=>$m));
		}
	}


	public function getallclients(Request $request){
		$squery = $request->q; //dd($squery);
		if($squery != ''){
			$d = '';
			if(strstr($squery, '/')){
				$dob = explode('/', $squery);
				if(!empty($dob) && is_array($dob)){
					$d = $dob[2].'/'.$dob[1].'/'.$dob[0];
				}
			}
			//dd($d);
            if( $d != "") {
                $clients = \App\Admin::where('role', '=', 7)->whereNull('is_deleted')
                ->where(
                    function($query) use ($squery,$d) {
                    return $query
                        ->orwhere('email', 'LIKE', '%'.$squery.'%')
                        //->where('att_email', '!=', '')
                        //->where('att_phone', '!=', '')
                        ->orwhere('first_name', 'LIKE','%'.$squery.'%')
                        ->orwhere('last_name', 'LIKE','%'.$squery.'%')
                        ->orwhere('client_id', 'LIKE','%'.$squery.'%')
                        ->orwhere('att_email', 'LIKE','%'.$squery.'%')
                        ->orwhere('att_phone', 'LIKE','%'.$squery.'%')
                        ->orwhere('phone', 'LIKE','%'.$squery.'%')
                        ->orwhere('dob', '=',$d)
                        ->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$squery."%");
                    })
                ->get();
            } else {
                $clients = \App\Admin::where('role', '=', 7)->whereNull('is_deleted')
                ->where(
                    function($query) use ($squery) {
                    return $query
                        ->orwhere('email', 'LIKE', '%'.$squery.'%')
                        //->where('att_email', '!=', '')
                        //->where('att_phone', '!=', '')
                        ->orwhere('first_name', 'LIKE','%'.$squery.'%')
                        ->orwhere('last_name', 'LIKE','%'.$squery.'%')
                        ->orwhere('client_id', 'LIKE','%'.$squery.'%')
                        ->orwhere('att_email', 'LIKE','%'.$squery.'%')
                        ->orwhere('att_phone', 'LIKE','%'.$squery.'%')
                        ->orwhere('phone', 'LIKE','%'.$squery.'%')
                        ->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$squery."%");
                    })
                ->get();
            }
             //dd($clients);
			/*	 $leads = \App\Lead::where('converted', '=', 0)

       ->where(
           function($query) use ($squery,$d) {
             return $query
                    ->where('email', 'LIKE', '%'.$squery.'%')
                    ->orwhere('first_name', 'LIKE','%'.$squery.'%')->orwhere('last_name', 'LIKE','%'.$squery.'%')->orwhere('phone', 'LIKE','%'.$squery.'%')->orwhere('dob', '=',$d)  ->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$squery."%");

            })
            ->get();*/

				$litems = array();
			/*foreach($leads as $lead){
				$litems[] = array('name' => $lead->first_name.' '.$lead->last_name,'email'=>$lead->email,'status'=>'Lead','id'=>base64_encode(convert_uuencode(@$lead->id)).'/Lead');
			}*/

			$items = array();
			foreach($clients as $clint){
				if($clint->is_archived == 1){
					$type = 'Archived';
				}else{
					$type = $clint->type;
				}
				$items[] = array('name' => $clint->first_name.' '.$clint->last_name,'email'=>$clint->email,'status'=>$type,'id'=>base64_encode(convert_uuencode(@$clint->id)).'/Client');
			}
			$m = array_merge($items, $litems);
			echo json_encode(array('items'=>$m));
		}
	}


	public function activities(Request $request){
		if(Admin::where('role', '=', '7')->where('id', $request->id)->exists()){
			$activities = ActivitiesLog::where('client_id', $request->id)->orderby('created_at', 'DESC')->get(); //->where('subject', '<>','added a note')
			$data = array();
			foreach($activities as $activit){
				$admin = Admin::where('id', $activit->created_by)->first();
                /*if($activit->use_for != ""){
                    $receiver = \App\Admin::where('id', $activit->use_for)->first();
                    if($receiver->first_name){
                        $reciver_name = $receiver->first_name;
                    } else {
                        $reciver_name = "";
                    }
                } else
                    $reciver_name = "";
                }*/


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
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
  
	public function updateclientstatus(Request $request){
		if(Admin::where('role', '=', '7')->where('id', $request->id)->exists()){
			$client = Admin::where('role', '=', '7')->where('id', $request->id)->first();

			$obj = Admin::find($request->id);
			$obj->rating = $request->rating;
			$saved = $obj->save();
			if($saved){
				if($client->rating == ''){
					$subject = 'has rated Client as '.$request->rating;
				}else{
					$subject = 'has changed Client’s rating from '.$client->rating.' to '.$request->rating;
				}
				$objs = new ActivitiesLog;
				$objs->client_id = $request->id;
				$objs->created_by = Auth::user()->id;
				$objs->subject = $subject;
				$objs->save();
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully updated your client’s information.';
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

	public function saveapplication(Request $request){
		if(Admin::where('role', '=', '7')->where('id', $request->client_id)->exists()){
			$workflow = $request->workflow;
			$explode = explode('_', $request->partner_branch);
			$partner = $explode[1];
			$branch = $explode[0];
			$product = $request->product;
			$client_id = $request->client_id;
			$status = 0;
			$workflowstage = \App\WorkflowStage::where('w_id', $workflow)->orderby('id','asc')->first();
			$stage = $workflowstage->name;
			$sale_forcast = 0.00;
			$obj = new \App\Application;
			$obj->user_id = Auth::user()->id;
			$obj->workflow = $workflow;
			$obj->partner_id = $partner;
			$obj->branch = $branch;
			$obj->product_id = $product;
			$obj->status = $status;
			$obj->stage = $stage;
			$obj->sale_forcast = $sale_forcast;
			$obj->client_id = $client_id;
			$saved = $obj->save();
			if($saved){
				$productdetail = \App\Product::where('id', $product)->first();
				$partnerdetail = \App\Partner::where('id', $partner)->first();
				$PartnerBranch = \App\PartnerBranch::where('id', $branch)->first();
				$subject = 'has started an application';
				$objs = new ActivitiesLog;
				$objs->client_id = $request->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '<span class="text-semi-bold">'.@$productdetail->name.'</span><p>'.@$partnerdetail->partner_name.' ('.@$PartnerBranch->name.')</p>';
				$objs->subject = $subject;
				$objs->save();
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully updated your client’s information.';
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

	public function getapplicationlists(Request $request){
		if(Admin::where('role', '=', '7')->where('id', $request->id)->exists()){
			$applications = \App\Application::where('client_id', $request->id)->orderby('created_at', 'DESC')->get();
			$data = array();
			ob_start();
			foreach($applications as $alist){
				$productdetail = \App\Product::where('id', $alist->product_id)->first();
				$partnerdetail = \App\Partner::where('id', $alist->partner_id)->first();
				$PartnerBranch = \App\PartnerBranch::where('id', $alist->branch)->first();
				$workflow = \App\Workflow::where('id', $alist->workflow)->first();
				?>
				<tr id="id_<?php echo $alist->id; ?>">
				<td><a class="openapplicationdetail" data-id="<?php echo $alist->id; ?>" href="javascript:;" style="display:block;"><?php echo @$productdetail->name; ?></a> <small><?php echo @$partnerdetail->partner_name; ?>(<?php echo @$PartnerBranch->name; ?>)</small></td>
				<td><?php echo @$workflow->name; ?></td>
				<td><?php echo @$alist->stage; ?></td>
				<td>
				<?php if($alist->status == 0){ ?>
				<span class="ag-label--circular" style="color: #6777ef" >In Progress</span>
				<?php }else if($alist->status == 1){ ?>
					<span class="ag-label--circular" style="color: #6777ef" >Completed</span>
				<?php } else if($alist->status == 2){
				?>
				<span class="ag-label--circular" style="color: red;" >Discontinued</span>
				<?php
				} ?>
			</td>

				<td><?php if(@$alist->start_date != ''){ echo date('d/m/Y', strtotime($alist->start_date)); } ?></td>
				<td><?php if(@$alist->end_date != ''){ echo date('d/m/Y', strtotime($alist->end_date)); } ?></td>
				<td>
					<div class="dropdown d-inline">
						<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
						<div class="dropdown-menu">
							<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction(<?php echo @$alist->id; ?>, 'applications')"><i class="fas fa-trash"></i> Delete</a>
						</div>
					</div>
				</td>
			</tr>
				<?php
			}

			return ob_get_clean();
		}else{

		}

	}

	public function createnote(Request $request){

			if(isset($request->noteid) && $request->noteid != ''){
				$obj = \App\Note::find($request->noteid);
			}else{
				$obj = new \App\Note;
			}

			$obj->client_id = $request->client_id;
			$obj->user_id = Auth::user()->id;
			$obj->title = $request->title;
			$obj->description = $request->description;
			$obj->mail_id = $request->mailid;
			$obj->type = $request->vtype;
			$saved = $obj->save();
			if($saved){
				if($request->vtype == 'client'){
					$subject = 'added a note';
					if(isset($request->noteid) && $request->noteid != ''){
					$subject = 'updated a note';
					}
					$objs = new ActivitiesLog;
					$objs->client_id = $request->client_id;
					$objs->created_by = Auth::user()->id;
					$objs->description = '<span class="text-semi-bold">'.$request->title.'</span><p>'.$request->description.'</p>';
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

	public function getnotedetail(Request $request){
		$note_id = $request->note_id;
		if(\App\Note::where('id',$note_id)->exists()){
			$data = \App\Note::select('title','description')->where('id',$note_id)->first();
			$response['status'] 	= 	true;
			$response['data']	=	$data;
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function viewnotedetail(Request $request){
		$note_id = $request->note_id;
		if(\App\Note::where('id',$note_id)->exists()){
			$data = \App\Note::select('title','description','user_id','updated_at')->where('id',$note_id)->first();
			$admin = \App\Admin::where('id', $data->user_id)->first();
			$s = substr(@$admin->first_name, 0, 1);
			$data->admin = $s;
			$response['status'] 	= 	true;
			$response['data']	=	$data;
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function viewapplicationnote(Request $request){
		$note_id = $request->note_id;
		if(\App\ApplicationActivitiesLog::where('type','note')->where('id',$note_id)->exists()){
			$data = \App\ApplicationActivitiesLog::select('title','description','user_id','updated_at')->where('type','note')->where('id',$note_id)->first();
			$admin = \App\Admin::where('id', $data->user_id)->first();
			$s = substr(@$admin->first_name, 0, 1);
			$data->admin = $s;
			$response['status'] 	= 	true;
			$response['data']	=	$data;
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function getnotes(Request $request){
		$client_id = $request->clientid;
		$type = $request->type;

		$notelist = \App\Note::where('client_id',$client_id)->whereNull('assigned_to')->whereNull('task_group')->where('type',$type)->orderby('pin', 'DESC')->orderBy('created_at', 'DESC')->get();
		ob_start();
		foreach($notelist as $list){
			$admin = \App\Admin::where('id', $list->user_id)->first();
			?>
			<div class="note_col" id="note_id_<?php echo $list->id; ?>">
				<div class="note_content">
					<h4><a class="viewnote" data-id="<?php echo $list->id; ?>" href="javascript:;"><?php echo @$list->title == "" ? config('constants.empty') : str_limit(@$list->title, '19', '...'); ?> </a></h4>
					<?php if($list->pin == 1){
									?><div class="pined_note"><i class="fa fa-thumbtack"></i></i></div><?php } ?>
				</div>
				<div class="extra_content">
				    <p><?php echo @$list->description; ?></p>
					<div class="left">
						<div class="author">
							<a href="<?php echo \URL::to('/admin/users/view/'.$admin->id); ?>"><?php echo substr($admin->first_name, 0, 1); ?></a>
						</div>
						<div class="note_modify">
							<small>Last Modified <span><?php echo date('Y-m-d h:i A', strtotime($list->updated_at)); ?></span></small>
							<?php echo $admin->first_name.' '.$admin->last_name; ?>
						</div>
					</div>
					<div class="right">
						<div class="dropdown d-inline dropdown_ellipsis_icon">
							<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
							<div class="dropdown-menu">
								<a class="dropdown-item opennoteform" data-id="<?php echo $list->id; ?>" href="javascript:;">Edit</a>
								<a data-id="<?php echo $list->id; ?>" data-href="deletenote" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
								<?php if($list->pin == 1){
									?>
									<a data-id="<?php echo $list->id; ?>"  class="dropdown-item pinnote" href="javascript:;" >UnPin</a>
									<?php
								}else{ ?>
									<a data-id="<?php echo $list->id; ?>"  class="dropdown-item pinnote" href="javascript:;" >Pin</a>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		return ob_get_clean();
	}

	public function deletenote(Request $request){
		$note_id = $request->note_id;
		if(\App\Note::where('id',$note_id)->exists()){
			$data = \App\Note::select('client_id','title','description')->where('id',$note_id)->first();
			$res = DB::table('notes')->where('id', @$note_id)->delete();
			if($res){
				if($data == 'client'){
				$subject = 'deleted a note';

				$objs = new ActivitiesLog;
				$objs->client_id = $data->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '<span class="text-semi-bold">'.$data->title.'</span><p>'.$data->description.'</p>';
				$objs->subject = $subject;
				$objs->save();
				}
			$response['status'] 	= 	true;
			$response['data']	=	$data;
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

	public function interestedService(Request $request){
		if(Admin::where('role', '=', '7')->where('id', $request->client_id)->exists()){
			if(\App\InterestedService::where('client_id', $request->client_id)->where('partner', $request->partner)->where('product', $request->product)->exists()){
				$response['status'] 	= 	false;
				$response['message']	=	'This interested service already exists.';
			}else{
				$obj = new \App\InterestedService;
				$obj->client_id = $request->client_id;
				$obj->user_id = Auth::user()->id;
				$obj->workflow = $request->workflow;
				$obj->partner = $request->partner;
				$obj->product = $request->product;
				$obj->branch = $request->branch;
				$obj->start_date = $request->expect_start_date;
				$obj->exp_date = $request->expect_win_date;
				$obj->status = 0;
				$saved = $obj->save();
				if($saved){
					$subject = 'added an interested service';

					$partnerdetail = \App\Partner::where('id', $request->partner)->first();
					$PartnerBranch = \App\PartnerBranch::where('id', $request->branch)->first();
					$objs = new ActivitiesLog;
					$objs->client_id = $request->client_id;
					$objs->created_by = Auth::user()->id;
					$objs->description = '<span class="text-semi-bold">'.$PartnerBranch->name.'</span><p>'.$partnerdetail->name.'</p>';
					$objs->subject = $subject;
					$objs->save();
					$response['status'] 	= 	true;
					$response['message']	=	'You’ve successfully added interested service';
				}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
				}
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}



	public function getServices(Request $request){
		$client_id = $request->clientid;
		$inteservices = \App\InterestedService::where('client_id',$client_id)->orderby('created_at', 'DESC')->get();
		foreach($inteservices as $inteservice){
			$workflowdetail = \App\Workflow::where('id', $inteservice->workflow)->first();
			 $productdetail = \App\Product::where('id', $inteservice->product)->first();
			$partnerdetail = \App\Partner::where('id', $inteservice->partner)->first();
			$PartnerBranch = \App\PartnerBranch::where('id', $inteservice->branch)->first();
			$admin = \App\Admin::where('id', $inteservice->user_id)->first();
			ob_start();
			?>
			<div class="interest_column">
			<?php
				if($inteservice->status == 1){
					?>
					<div class="interest_serv_status status_active">
						<span>Converted</span>
					</div>
					<?php
				}else{
					?>
					<div class="interest_serv_status status_default">
						<span>Draft</span>
					</div>
					<?php
				}
				?>
			<div class="interest_serv_info">
				<h4><?php echo @$workflowdetail->name; ?></h4>
				<h6><?php echo @$productdetail->name; ?></h6>
				<p><?php echo @$partnerdetail->partner_name; ?></p>
				<p><?php echo @$PartnerBranch->name; ?></p>
			</div>
			<?php
			$client_revenue = '0.00';
			if($inteservice->client_revenue != ''){
				$client_revenue = $inteservice->client_revenue;
			}
			$partner_revenue = '0.00';
			if($inteservice->partner_revenue != ''){
				$partner_revenue = $inteservice->partner_revenue;
			}
			$discounts = '0.00';
			if($inteservice->discounts != ''){
				$discounts = $inteservice->discounts;
			}
			$nettotal = $client_revenue + $partner_revenue - $discounts;

			$appfeeoption = \App\ServiceFeeOption::where('app_id', $inteservice->id)->first();
			$totl = 0.00;
			$net = 0.00;
			$discount = 0.00;
			if($appfeeoption){
				$appfeeoptiontype = \App\ServiceFeeOptionType::where('fee_id', $appfeeoption->id)->get();
				foreach($appfeeoptiontype as $fee){
					$totl += $fee->total_fee;
				}
			}

			if(@$appfeeoption->total_discount != ''){
				$discount = @$appfeeoption->total_discount;
			}
			$net = $totl -  $discount;
			?>
			<div class="interest_serv_fees">
				<div class="fees_col cus_col">
					<span class="cus_label">Product Fees</span>
					<span class="cus_value">AUD: <?php echo number_format($net,2,'.',''); ?></span>
				</div>
				<div class="fees_col cus_col">
					<span class="cus_label">Sales Forecast</span>
					<span class="cus_value">AUD: <?php echo number_format($nettotal,2,'.',''); ?></span>
				</div>
			</div>
			<div class="interest_serv_date">
				<div class="date_col cus_col">
					<span class="cus_label">Expected Start Date</span>
					<span class="cus_value"><?php echo $inteservice->start_date; ?></span>
				</div>
				<div class="fees_col cus_col">
					<span class="cus_label">Expected Win Date</span>
					<span class="cus_value"><?php echo $inteservice->exp_date; ?></span>
				</div>
			</div>
			<div class="interest_serv_row">
				<div class="serv_user_data">
					<div class="serv_user_img"><?php echo substr($admin->first_name, 0, 1); ?></div>
					<div class="serv_user_info">
						<span class="serv_name"><?php echo $admin->first_name; ?></span>
						<span class="serv_create"><?php echo date('Y-m-d', strtotime($inteservice->exp_date)); ?></span>
					</div>
				</div>
				<div class="serv_user_action">
					<a href="javascript:;" data-id="<?php echo $inteservice->id; ?>" class="btn btn-primary interest_service_view">View</a>
					<div class="dropdown d-inline dropdown_ellipsis_icon" style="margin-left:10px;">
						<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
						<div class="dropdown-menu">
						<?php if($inteservice->status == 0){ ?>
							<a class="dropdown-item converttoapplication" data-id="<?php echo $inteservice->id; ?>" href="javascript:;">Create Appliation</a>
						<?php } ?>
							<a class="dropdown-item" href="javascript:;">Delete</a>
						</div>
					</div>
				</div>
			</div>
		</div>
			<?php

		}
		return ob_get_clean();
	}


	public function uploaddocument(Request $request){ //dd($request->all());

		$id = $request->clientid;
        $doctype = isset($request->doctype)? $request->doctype : '';

		 if ($request->hasfile('document_upload')) {

			if(!is_array($request->file('document_upload'))){
				$files[] = $request->file('document_upload');
			}else{
				$files = $request->file('document_upload');
			}
		      foreach ($files as $file) {

		           $size = $file->getSize();
				   $fileName = $file->getClientOriginalName();
				    $explodeFileName = explode('.', $fileName);
		        	$document_upload = $this->uploadrenameFile($file, Config::get('constants.documents'));
		        	$exploadename = explode('.', $document_upload);
		        	$obj = new \App\Document;
        		    $obj->file_name = $explodeFileName[0];
        			$obj->filetype = $exploadename[1];
        			$obj->user_id = Auth::user()->id;
        			$obj->myfile = $document_upload;
        			$obj->client_id = $id;
        			$obj->type = $request->type;
        			$obj->file_size = $size;
        			$obj->doc_type = $doctype;
        			$saved = $obj->save();

		      }

			if($saved){
				if($request->type == 'client'){
				$subject = 'added 1 document';
				$objs = new ActivitiesLog;
				$objs->client_id = $id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '';
				$objs->subject = $subject;
				$objs->save();

				}
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully uploaded your document';
				$fetchd = \App\Document::where('client_id',$id)->where('doc_type',$doctype)->where('type',$request->type)->orderby('created_at', 'DESC')->get();
				ob_start();
				foreach($fetchd as $fetch){
					$admin = \App\Admin::where('id', $fetch->user_id)->first();
					?>
					<tr class="drow" id="id_<?php echo $fetch->id; ?>">
						<td><div data-id="<?php echo $fetch->id; ?>" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
							<i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name; ?><?php echo '.'.$fetch->filetype; ?></span>
						</div></td>
						<td><?php echo $admin->first_name; ?></td>

						<td><?php echo date('Y-m-d', strtotime($fetch->created_at)); ?></td>
						<td>
							<div class="dropdown d-inline">
								<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
								<div class="dropdown-menu">
									<a class="dropdown-item renamedoc" href="javascript:;">Rename</a>
									<a target="_blank" class="dropdown-item" href="<?php echo \URL::to('/public/img/documents'); ?>/<?php echo $fetch->myfile; ?>">Preview</a>
									<?php
																$explodeimg = explode('.',$fetch->myfile);
										if($explodeimg[1] == 'jpg'|| $explodeimg[1] == 'png'|| $explodeimg[1] == 'jpeg'){
																?>
																	<a target="_blank" class="dropdown-item" href="<?php echo \URL::to('/admin/document/download/pdf'); ?>/<?php echo $fetch->id; ?>">PDF</a>
																	<?php } ?>
									<a download class="dropdown-item" href="<?php echo \URL::to('/public/img/documents'); ?>/<?php echo $fetch->myfile; ?>">Download</a>

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
					$admin = \App\Admin::where('id', $fetch->user_id)->first();
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
										<a class="dropdown-item" href="<?php echo \URL::to('/public/img/documents'); ?>/<?php echo $fetch->myfile; ?>">Preview</a>
										<a download class="dropdown-item" href="<?php echo \URL::to('/public/img/documents'); ?>/<?php echo $fetch->myfile; ?>">Download</a>
										<a data-id="<?php echo $fetch->id; ?>" class="dropdown-item deletenote" data-href="deletedocs" href="javascript:;" >Delete</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
				$griddata = ob_get_clean();
				$response['data']	=$data;
				$response['griddata']	=$griddata;
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
	public function convertapplication(Request $request){
		$id = $request->cat_id;
		$clientid = $request->clientid;

		if(\App\InterestedService::where('client_id',$clientid)->where('id',$id)->exists()){
			$app = \App\InterestedService::where('client_id',$clientid)->where('id',$id)->first();
			$workflow = $app->workflow;
			$workflowstage = \App\WorkflowStage::where('w_id', $workflow)->orderby('id','ASC')->first();
			$partner = $app->partner;
			$branch = $app->branch;
			$product = $app->product;
			$client_id = $request->client_id;
			$status = 0;
			$stage = $workflowstage->name;
			$sale_forcast = 0.00;
			$obj = new \App\Application;
			$obj->user_id = Auth::user()->id;
			$obj->workflow = $workflow;
			$obj->partner_id = $partner;
			$obj->branch = $branch;
			$obj->product_id = $product;
			$obj->status = $status;
			$obj->stage = $stage;
			$obj->client_id = $clientid;
			$obj->client_revenue = @$app->client_revenue;
			$obj->partner_revenue = @$app->partner_revenue;
			$obj->discounts = @$app->discounts;

			$saved = $obj->save();

			if(\App\ServiceFeeOption::where('app_id', $app->id)->exists()){
				$servicedata = \App\ServiceFeeOption::where('app_id', $app->id)->first();

				$aobj = new \App\ApplicationFeeOption;
				$aobj->user_id = Auth::user()->id;
				$aobj->app_id = $obj->id;
				$aobj->name = $servicedata->name;
				$aobj->country = $servicedata->country;
				$aobj->installment_type = $servicedata->installment_type;
				$aobj->discount_amount = $servicedata->discount_amount;
				$aobj->discount_sem = $servicedata->discount_sem;
				$aobj->total_discount = $servicedata->total_discount;
				$aobj->save();
				if(\App\ServiceFeeOptionType::where('fee_id', $servicedata->id)->exists()){
					$totl = 0.00;
					$appfeeoptiontype = \App\ServiceFeeOptionType::where('fee_id', $servicedata->id)->get();
					foreach($appfeeoptiontype as $fee){
						$totl += $fee->total_fee;
						$aobjs = new \App\ApplicationFeeOptionType;
						$aobjs->fee_id = $aobj->id;
						$aobjs->fee_type = $fee->fee_type;
						$aobjs->inst_amt = $fee->inst_amt;
						$aobjs->installment = $fee->installment;
						$aobjs->total_fee = $fee->total_fee;
						$aobjs->claim_term = $fee->claim_term;
						$aobjs->commission = $fee->commission;
						$saved = $aobjs->save();
					}
				}
			}

			$app = \App\InterestedService::find($id);
			$app->status = 1;
			$saved = $app->save();
			if($saved){
				$productdetail = \App\Product::where('id', $product)->first();
				$partnerdetail = \App\Partner::where('id', $partner)->first();
				$PartnerBranch = \App\PartnerBranch::where('id', $branch)->first();
				$subject = 'has started an application';
				$objs = new ActivitiesLog;
				$objs->client_id = $request->clientid;
				$objs->created_by = Auth::user()->id;
				$objs->description = '<span class="text-semi-bold">'.@$productdetail->name.'</span><p>'.@$partnerdetail->partner_name.' ('.@$PartnerBranch->name.')</p>';
				$objs->subject = $subject;
				$objs->save();
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully updated your client’s information.';
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

	public function deleteservices(Request $request){
		$note_id = $request->note_id;
		if(\App\InterestedService::where('id',$note_id)->exists()){
			$data = \App\InterestedService::where('id',$note_id)->first();
			$res = DB::table('interested_services')->where('id', @$note_id)->delete();
			if($res){
				$productdetail = \App\Product::where('id', $data->product)->first();
				$partnerdetail = \App\Partner::where('id', $data->partner)->first();
				$PartnerBranch = \App\PartnerBranch::where('id', $data->branch)->first();
				$subject = 'deleted an interested service';

				$objs = new ActivitiesLog;
				$objs->client_id = $data->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '<span class="text-semi-bold">'.@$productdetail->name.'</span><p>'.@$partnerdetail->partner_name.' ('.@$PartnerBranch->name.')</p>';
				$objs->subject = $subject;
				$objs->save();
			$response['status'] 	= 	true;
			$response['data']	=	$data;
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



	public function renamedoc(Request $request){
		$id = $request->id;
		$filename = $request->filename;
		if(\App\Document::where('id',$id)->exists()){
			$doc = \App\Document::where('id',$id)->first();
			$res = DB::table('documents')->where('id', @$id)->update(['file_name' => $filename]);
			if($res){
				$response['status'] 	= 	true;
				$response['data']	=	'Document saved successfully';
				$response['Id']	=	$id;
				$response['filename']	=	$filename;
				$response['filetype']	=	$doc->filetype;
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

	public function save_tag(Request $request){
		 $id = $request->client_id;

		if(\App\Admin::where('id',$id)->exists()){
		    $tagg = $request->tag;
		    $tag = array();
		    foreach($tagg as $tg){
		        $stagd = \App\Tag::where('name','=',$tg)->first();
		        if($stagd){

		        }else{
		            $stagds = \App\Tag::where('id','=',$tg)->first();
		            if($stagds){
		                $tag[] = $stagds->id;
		            }else{
		                $o = new \App\Tag;
		                $o->name = $tg;
		                $o->save();
		                $tag[] = $o->id;
		            }

		        }
		    }
			$obj = \App\Admin::find($id);
			$obj->tagname = implode(',', $tag);
			$saved = $obj->save();
			if($saved){
				return Redirect::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$id)))->with('success', 'Tags addes successfully');
			}else{
				return Redirect::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$id)))->with('error', 'Please try again');
			}
		}else{
			return Redirect::to('/admin/clients')->with('error', Config::get('constants.unauthorized'));
		}

	}

	public function deletedocs(Request $request){
		$note_id = $request->note_id;

		if(\App\Document::where('id',$note_id)->exists()){

			$data = DB::table('documents')->where('id', @$note_id)->first();
			$res = DB::table('documents')->where('id', @$note_id)->delete();

			if($res){

				$subject = 'deleted a document';

				$objs = new ActivitiesLog;
				$objs->client_id = $data->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '';
				$objs->subject = $subject;
				$objs->save();
				$response['status'] 	= 	true;
				$response['data']	=	'Document removed successfully';
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
	
	public function addAppointmentBook(Request $request){
		$requestData = $request->all(); //dd($requestData);
        $obj = new \App\Appointment;
		$obj->user_id = @Auth::user()->id;
		$obj->client_id = @$request->client_id;
		$obj->timezone = @$request->timezone;
        $obj->service_id = @$request->service_id;
        $obj->noe_id = @$request->noe_id;
        $obj->appointment_details = @$request->appointment_details;

        if( isset($request->appoint_date) && $request->appoint_date != "") {
            $obj->client_unique_id = @$request->client_unique_id;
        }

        //$obj->full_name = @$request->fullname;
        //$obj->email = @$request->email;
        //$obj->phone = @$request->phone;
        //$obj->date = @$request->appoint_date;
		//$obj->time = @$request->appoint_time;
        if( isset($request->appoint_date) && $request->appoint_date != "") {
            $date = explode('/', $request->appoint_date);
            $obj->date = $date[2].'-'.$date[1].'-'.$date[0];
        }

        $obj->timeslot_full = @$request->appoint_time;
        if( isset($request->appoint_time) && $request->appoint_time != "" ){
			$time = explode('-', $request->appoint_time);
			//echo "@@".date("H:i", strtotime($time[0])); die;
			$obj->time = date("H:i", strtotime($time[0]));
		}

        if( isset($request->slot_overwrite_hidden) && $request->slot_overwrite_hidden != "" ){
			$obj->slot_overwrite_hidden = $request->slot_overwrite_hidden;
		}

        //$obj->title = @$request->title;
		$obj->description = @$request->description;
        //$obj->invites = @$request->invites;
        if( isset($request->promocode_used) && $request->promocode_used != "" ){
			$obj->promocode_used = $request->promocode_used;
        }

        if( isset($request->service_id) && $request->service_id == 1 ){ //1=>Paid,2=>Free
            $obj->status = 9; //9=>Pending Appointment With Payment Pending
        } else if( isset($request->service_id) && $request->service_id == 2 ){
            $obj->status = 0; //0=>Pending Appointment With Free Type
        }

        $obj->related_to = 'client';
		$saved = $obj->save();
		if($saved){
            if( isset($request->promocode_used) && $request->promocode_used != "" ){
                DB::table('promocode_uses')->insert([
                    'client_id' => $request->client_id,
                    'promocode_id' => $request->promocode_id,
                    'promocode' => $request->promocode_used
                ]);
            }

			if(isset($request->type) && $request->atype == 'application'){
				$objs = new \App\ApplicationActivitiesLog;
				$objs->stage = $request->type;
				$objs->type = 'appointment';
				$objs->comment = 'created appointment '.@$request->appoint_date;
				$objs->title = '';
				$objs->description = '';
				$objs->app_id = $request->noteid;
				$objs->user_id = Auth::user()->id;
				$saved = $objs->save();
            } else {
                $objs = new ActivitiesLog;
                $objs->client_id = $request->client_id;
                $objs->created_by = Auth::user()->id;

                $appoint_date_val = explode('/', $request->appoint_date);
                $appoint_date_val_formated = $appoint_date_val[0].'/'.$appoint_date_val[1].'/'.$appoint_date_val[2];
                if( isset($request->service_id) && $request->service_id == 1 ){ //1=>Paid
                    $objs->description = '<p><span class="text-semi-bold">scheduled an paid appointment without payment on '.$appoint_date_val_formated.' at '.$request->appoint_time.'</span></p>';
                } else if( isset($request->service_id) && $request->service_id == 2 ){ //2=>Free
                    $objs->description = '<p><span class="text-semi-bold">scheduled an appointment on '.$appoint_date_val_formated.' at '.$request->appoint_time.'</span></p>';
                }

                if( isset($request->service_id) && $request->service_id == 1 ){ //1=>Paid
                    $subject = 'scheduled an paid appointment without payment';
                } else if( isset($request->service_id) && $request->service_id == 2 ){ //2=>Free
                    $subject = 'scheduled an appointment';
                }
                $objs->subject = $subject;

			    $objs->save();
			}

            $response['status'] = 	true;
			$response['data']	=	'Appointment saved successfully';
            if(isset($requestData['is_ajax']) && $requestData['is_ajax'] == 1){
                $response['reloadpage'] = true;
            }else{
                $response['reloadpage'] = true; //false;
            }
		} else {
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
        echo json_encode($response);
    }
    

	public function addAppointment(Request $request){
		$requestData = $request->all();

		$obj = new \App\Appointment;
		$obj->user_id = @Auth::user()->id;
		$obj->client_id = @$request->client_id;
		$obj->timezone = @$request->timezone;
		$obj->date = @$request->appoint_date;
		$obj->time = @$request->appoint_time;
		$obj->title = @$request->title;
		$obj->description = @$request->description;
		$obj->invites = @$request->invites;

		$obj->status = 0;
		$obj->related_to = 'client';
		$saved = $obj->save();
		if($saved){

			if(isset($request->type) && $request->atype == 'application'){
				$objs = new \App\ApplicationActivitiesLog;
				$objs->stage = $request->type;
				$objs->type = 'appointment';
				$objs->comment = 'created appointment '.@$request->appoint_date;
				$objs->title = '';
				$objs->description = '';
				$objs->app_id = $request->noteid;
				$objs->user_id = Auth::user()->id;
				$saved = $objs->save();

			}else{
				$subject = 'scheduled an appointment';
			$objs = new ActivitiesLog;
			$objs->client_id = $request->client_id;
			$objs->created_by = Auth::user()->id;
			$objs->description = '<div  style="margin-right: 1rem;float:left;">
						<span style="height: 60px; width: 60px; border: 1px solid rgb(3, 169, 244); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px;overflow: hidden;">
							<span  style="flex: 1 1 0%; width: 100%; text-align: center; background: rgb(237, 237, 237); border-top-left-radius: 120px; border-top-right-radius: 120px; font-size: 12px;line-height: 24px;">
							  '.date('d M', strtotime($obj->date)).'
							</span>
							<span style="background: rgb(84, 178, 75); color: rgb(255, 255, 255); flex: 1 1 0%; width: 100%; border-bottom-left-radius: 120px; border-bottom-right-radius: 120px; text-align: center;font-size: 12px; line-height: 21px;">
							   '.date('Y', strtotime($obj->date)).'
							</span>
						</span>
					</div>
					<div style="float:right;"><span  class="text-semi-bold">'.$obj->title.'</span> <p  class="text-semi-light-grey col-v-1">
				@ '.date('H:i A', strtotime($obj->time)).'
				</p></div>';
			$objs->subject = $subject;
			$objs->save();
			}


			$response['status'] 	= 	true;
			$response['data']	=	'Appointment saved successfully';
				if(isset($requestData['is_ajax']) && $requestData['is_ajax'] == 1){
		            $response['reloadpage'] 	= 	true;
	        	}else{
		        $response['reloadpage'] 	= 	false;
	        	}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}

		 echo json_encode($response);

	}


	public function editappointment(Request $request){
		$requestData = $request->all();

		$obj = \App\Appointment::find($requestData['id']);
		$obj->user_id = @Auth::user()->id;
		$obj->timezone = @$request->timezone;
		$obj->date = @$request->appoint_date;
		$obj->time = @$request->appoint_time;
		$obj->title = @$request->title;
		$obj->description = @$request->description;
		$obj->invites = @$request->invites;
		$obj->status = 0;
		$saved = $obj->save();
		if($saved){
			$subject = 'rescheduled an appointment';
			$objs = new ActivitiesLog;
			$objs->client_id = $request->client_id;
			$objs->created_by = Auth::user()->id;
			$objs->description = '<div  style="margin-right: 1rem;float:left;">
						<span style="height: 60px; width: 60px; border: 1px solid rgb(3, 169, 244); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px;overflow: hidden;">
							<span  style="flex: 1 1 0%; width: 100%; text-align: center; background: rgb(237, 237, 237); border-top-left-radius: 120px; border-top-right-radius: 120px; font-size: 12px;line-height: 24px;">
							  '.date('d M', strtotime($obj->date)).'
							</span>
							<span style="background: rgb(84, 178, 75); color: rgb(255, 255, 255); flex: 1 1 0%; width: 100%; border-bottom-left-radius: 120px; border-bottom-right-radius: 120px; text-align: center;font-size: 12px; line-height: 21px;">
							   '.date('Y', strtotime($obj->date)).'
							</span>
						</span>
					</div>
					<div style="float:right;"><span  class="text-semi-bold">'.$obj->title.'</span> <p  class="text-semi-light-grey col-v-1">
				@ '.date('H:i A', strtotime($obj->time)).'
				</p></div>';
			$objs->subject = $subject;
			$objs->save();
			$response['status'] 	= 	true;
			$response['data']	=	'Appointment updated successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
  
  	
	

	public function updateappointmentstatus(Request $request, $status = Null, $id = Null){
		if(isset($id) && !empty($id))
		{
			$requestData = $request->all();
			if(\App\Appointment::where('id', '=', $id)->exists())
			{
				$obj = \App\Appointment::find($id);
				$obj->status = @$status;
				$saved = $obj->save();

				//$subject = 'Appointment Completed';
                if( $status == 0){
                    $subject = 'Appointment is pending';
                } else if( $status == 1){
                    $subject = 'Appointment is approved';
                } else if( $status == 2){
                    $subject = 'Appointment is completed';
                } else if( $status == 3){
                    $subject = 'Appointment is rejected';
                } else if( $status == 4){
                    $subject = 'Appointment is N/P';
                } else if( $status == 5){
                    $subject = 'Appointment is inrogress';
                } else if( $status == 6){
                    $subject = 'Appointment is pending due to did not come';
                } else if( $status == 7){
                    $subject = 'Appointment is cancelled';
                } else if( $status == 8){
                    $subject = 'Appointment is missed';
                } else if( $status == 9){
                    $subject = 'Appointment is pending with payment pending';
                } else if( $status == 10){
                    $subject = 'Appointment is pending with payment success';
                } else if( $status == 11){
                    $subject = 'Appointment is pending with payment failed';
                }
                $objs = new ActivitiesLog;
                $objs->client_id = $obj->client_id;
                $objs->created_by = Auth::user()->id;
                $objs->description = '<div  style="margin-right: 1rem;float:left;">
						<span style="height: 60px; width: 60px; border: 1px solid rgb(3, 169, 244); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px;overflow: hidden;">
							<span  style="flex: 1 1 0%; width: 100%; text-align: center; background: rgb(237, 237, 237); border-top-left-radius: 120px; border-top-right-radius: 120px; font-size: 12px;line-height: 24px;">
							  '.date('d M', strtotime($obj->date)).'
							</span>
							<span style="background: rgb(84, 178, 75); color: rgb(255, 255, 255); flex: 1 1 0%; width: 100%; border-bottom-left-radius: 120px; border-bottom-right-radius: 120px; text-align: center;font-size: 12px; line-height: 21px;">
							   '.date('Y', strtotime($obj->date)).'
							</span>
						</span>
					</div>
					<div style="float:right;"><span  class="text-semi-bold">'.$obj->title.'</span> <p  class="text-semi-light-grey col-v-1">
				@ '.date('H:i A', strtotime($obj->time)).'
				</p></div>';
				$objs->subject = $subject;
				$objs->save();
				//return Redirect::to('/admin/appointments-cal')->with('success', 'Appointment updated successfully.');
                return redirect()->back()->withInput()->with('success', 'Appointment updated successfully.');
			}else{
				return redirect()->back()->with('error', 'Record Not Found');
			}
		}else{
			return redirect()->back()->with('error', 'Record Not Found');
		}
	}
	public function updatefollowupschedule(Request $request){
		$requestData = $request->all();

		$obj = \App\Appointment::find($requestData['appointment_id']);
		$obj->user_id = @Auth::user()->id;
		//$obj->timezone = @$request->timezone;
		$obj->date = @$request->followup_date;
		$obj->time = @$request->followup_time;
		//$obj->title = @$request->title;
		$obj->description = @$request->edit_description;
		//$obj->invites = @$request->invites
		$saved = $obj->save();
		if($saved){
			$subject = 'updated an appointment';
			$objs = new ActivitiesLog;
			$objs->client_id = $obj->client_id;
			$objs->created_by = Auth::user()->id;
			$objs->description = '<div  style="margin-right: 1rem;float:left;">
						<span style="height: 60px; width: 60px; border: 1px solid rgb(3, 169, 244); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px;overflow: hidden;">
							<span  style="flex: 1 1 0%; width: 100%; text-align: center; background: rgb(237, 237, 237); border-top-left-radius: 120px; border-top-right-radius: 120px; font-size: 12px;line-height: 24px;">
							  '.date('d M', strtotime($obj->date)).'
							</span>
							<span style="background: rgb(84, 178, 75); color: rgb(255, 255, 255); flex: 1 1 0%; width: 100%; border-bottom-left-radius: 120px; border-bottom-right-radius: 120px; text-align: center;font-size: 12px; line-height: 21px;">
							   '.date('Y', strtotime($obj->date)).'
							</span>
						</span>
					</div>
					<div style="float:right;"><span  class="text-semi-bold">'.$obj->title.'</span> <p  class="text-semi-light-grey col-v-1">
				@ '.date('H:i A', strtotime($obj->time)).'
				</p></div>';
			$objs->subject = $subject;
			$objs->save();
			return Redirect::to('/admin/appointments-cal')->with('success', 'Appointment updated successfully.');
		}else{
			return redirect()->back()->with('error', Config::get('constants.server_error'));
		}

	}

	public function getAppointments(Request $request){
		ob_start();
		?>
		<div class="row">
			<div class="col-md-5 appointment_grid_list">
				<?php
				$rr=0;
				$appointmentdata = array();
				$appointmentlists = \App\Appointment::where('client_id', $request->clientid)->where('related_to', 'client')->orderby('created_at', 'DESC')->get();
				$appointmentlistslast = \App\Appointment::where('client_id', $request->clientid)->where('related_to', 'client')->orderby('created_at', 'DESC')->first();
				foreach($appointmentlists as $appointmentlist){
					$admin = \App\Admin::where('id', $appointmentlist->user_id)->first();
					$datetime = $appointmentlist->created_at;
					$timeago = Controller::time_elapsed_string($datetime);

					$appointmentdata[$appointmentlist->id] = array(
						'title' => $appointmentlist->title,
						'time' => date('H:i A', strtotime($appointmentlist->time)),
						'date' => date('d D, M Y', strtotime($appointmentlist->date)),
						'description' => $appointmentlist->description,
						'createdby' => substr($admin->first_name, 0, 1),
						'createdname' => $admin->first_name,
						'createdemail' => $admin->email,
					);
				?>
				<div class="appointmentdata <?php if($rr == 0){ echo 'active'; } ?>" data-id="<?php echo $appointmentlist->id; ?>">
					<div class="appointment_col">
						<div class="appointdate">
							<h5><?php echo date('d D', strtotime($appointmentlist->date)); ?></h5>
							<p><?php echo date('H:i A', strtotime($appointmentlist->time)); ?><br>
							<i><small><?php echo $timeago ?></small></i></p>
						</div>
						<div class="title_desc">
							<h5><?php echo $appointmentlist->title; ?></h5>
							<p><?php echo $appointmentlist->description; ?></p>
						</div>
						<div class="appoint_created">
							<span class="span_label">Created By:
							<span><?php echo substr($admin->first_name, 0, 1); ?></span></span>
							<div class="dropdown d-inline dropdown_ellipsis_icon">
								<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
								<div class="dropdown-menu">
									<a class="dropdown-item edit_appointment" data-id="<?php echo $appointmentlist->id; ?>" href="javascript:;">Edit</a>
									<a data-id="<?php echo $appointmentlist->id; ?>" data-href="deleteappointment" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php $rr++; } ?>
			</div>
			<div class="col-md-7">
				<div class="editappointment">
					<a class="edit_link edit_appointment" href="javascript:;" data-id="<?php echo $appointmentlistslast->id; ?>"><i class="fa fa-edit"></i></a>
					<?php
					$adminfirst = \App\Admin::where('id', $appointmentlistslast->user_id)->first();
					?>
					<div class="content">
						<h4 class="appointmentname"><?php echo $appointmentlistslast->title; ?></h4>
						<div class="appitem">
							<i class="fa fa-clock"></i>
							<span class="appcontent appointmenttime"><?php echo date('H:i A', strtotime($appointmentlistslast->time)); ?></span>
						</div>
						<div class="appitem">
							<i class="fa fa-calendar"></i>
							<span class="appcontent appointmentdate"><?php echo date('d D, M Y', strtotime($appointmentlistslast->date)); ?></span>
						</div>
						<div class="description appointmentdescription">
							<p><?php echo $appointmentlistslast->description; ?></p>
						</div>
						<div class="created_by">
							<span class="label">Created By:</span>
							<div class="createdby">
								<span class="appointmentcreatedby"><?php echo substr($adminfirst->first_name, 0, 1); ?></span>
							</div>
							<div class="createdinfo">
								<a href="" class="appointmentcreatedname"><?php echo $adminfirst->first_name ?></a>
								<p class="appointmentcreatedemail"><?php echo $adminfirst->primary_email; ?></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		echo ob_get_clean();
		die;
	}


	public function getAppointmentdetail(Request $request){
		$obj = \App\Appointment::find($request->id);
		if($obj){
			?>
			<form method="post" action="<?php echo \URL::to('/admin/editappointment'); ?>" name="editappointment" id="editappointment" autocomplete="off" enctype="multipart/form-data">

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
								<input type="text" name="client_name" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Client Name" readonly value="<?php echo $obj->clients->first_name.' '.@$obj->clients->last_name; ?>">

							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="timezone">Timezone <span class="span_req">*</span></label>
								<select class="form-control timezoneselects2" name="timezone" data-valid="required">
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
									<input type="time" name="appoint_time" class="form-control" data-valid="required" autocomplete="off" placeholder="Select Date" value="<?php echo $obj->time; ?>">

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
								<label for="invites">Invitees</label>
								<select class="form-control invitesselects2" name="invites">
									<option value="">Select Invitees</option>
								 <?php
										$headoffice = \App\Admin::where('role','!=',7)->get();
									foreach($headoffice as $holist){
										?>
										<option value="<?php echo $holist->id; ?>" <?php if($obj->invites == $holist->id){ echo 'selected'; } ?>><?php echo $holist->first_name.' '. $holist->last_name.' ('.$holist->email.')'; ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('editappointment')" type="button" class="btn btn-primary">Save</button>
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

	public function deleteappointment(Request $request){
		$note_id = $request->note_id;
		if(\App\Appointment::where('id',$note_id)->exists()){
			$data = \App\Appointment::where('id',$note_id)->first();
			$res = DB::table('appointments')->where('id', @$note_id)->delete();
			if($res){

				$subject = 'deleted an appointment';

				$objs = new ActivitiesLog;
				$objs->client_id = $data->client_id;
				$objs->created_by = Auth::user()->id;
			$objs->description = '<div  style="margin-right: 1rem;float:left;">
						<span style="height: 60px; width: 60px; border: 1px solid rgb(3, 169, 244); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2px;overflow: hidden;">
							<span  style="flex: 1 1 0%; width: 100%; text-align: center; background: rgb(237, 237, 237); border-top-left-radius: 120px; border-top-right-radius: 120px; font-size: 12px;line-height: 24px;">
							  '.date('d M', strtotime($data->date)).'
							</span>
							<span style="background: rgb(84, 178, 75); color: rgb(255, 255, 255); flex: 1 1 0%; width: 100%; border-bottom-left-radius: 120px; border-bottom-right-radius: 120px; text-align: center;font-size: 12px; line-height: 21px;">
							   '.date('Y', strtotime($data->date)).'
							</span>
						</span>
					</div>
					<div style="float:right;"><span  class="text-semi-bold">'.$data->title.'</span> <p  class="text-semi-light-grey col-v-1">
				@ '.date('H:i A', strtotime($data->time)).'
				</p></div>';
				$objs->subject = $subject;
				$objs->save();
			$response['status'] 	= 	true;
			$response['data']	=	$data;
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

	public function editinterestedService(Request $request){
		if(Admin::where('role', '=', '7')->where('id', $request->client_id)->exists()){

			$obj = \App\InterestedService::find($request->id);
			$obj->workflow = $request->workflow;
			$obj->partner = $request->partner;
			$obj->product = $request->product;
			$obj->branch = $request->branch;
			$obj->start_date = $request->expect_start_date;
			$obj->exp_date = $request->expect_win_date;
			$obj->status = 0;
			$saved = $obj->save();
			if($saved){
				$subject = 'updated an interested service';

				$partnerdetail = \App\Partner::where('id', $request->partner)->first();
				$PartnerBranch = \App\PartnerBranch::where('id', $request->branch)->first();
				$objs = new ActivitiesLog;
				$objs->client_id = $request->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '<span class="text-semi-bold">'.$PartnerBranch->name.'</span><p>'.$partnerdetail->name.'</p>';
				$objs->subject = $subject;
				$objs->save();
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully updated interested service';
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

	public function getintrestedserviceedit(Request $request){
		$obj = \App\InterestedService::find($request->id);
		if($obj){
			?>
			<form method="post" action="<?php echo \URL::to('/admin/edit-interested-service'); ?>" name="editinter_servform" autocomplete="off" id="editinter_servform" enctype="multipart/form-data">
				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
				<input type="hidden" name="client_id" value="<?php echo $obj->client_id; ?>">
				<input type="hidden" name="id" value="<?php echo $obj->id; ?>">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="edit_intrested_workflow">Select Workflow <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control workflowselect2" id="edit_intrested_workflow" name="workflow">
									<option value="">Please Select a Workflow</option>
									<?php foreach(\App\Workflow::all() as $wlist){
										?>
										<option <?php if($obj->workflow == $wlist->id){ echo 'selected'; } ?> value="<?php echo $wlist->id; ?>"><?php echo $wlist->name; ?></option>
									<?php } ?>
								</select>
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="edit_intrested_partner">Select Partner</label>
								<select data-valid="required" class="form-control partnerselect2" id="edit_intrested_partner" name="partner">
									<option value="">Please Select a Partner</option>
									<?php foreach(\App\Partner::where('service_workflow', $obj->workflow)->orderby('created_at', 'DESC')->get() as $plist){
										?>
										<option <?php if($obj->partner == $plist->id){ echo 'selected'; } ?> value="<?php echo $plist->id; ?>"><?php echo @$plist->partner_name; ?></option>
									<?php } ?>
								</select>
								<span class="custom-error partner_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="edit_intrested_product">Select Product</label>
								<select data-valid="required" class="form-control productselect2" id="edit_intrested_product" name="product">
									<option value="">Please Select a Product</option>
									<?php foreach(\App\Product::where('partner', $obj->partner)->orderby('created_at', 'DESC')->get() as $pplist){
										?>
										<option <?php if($obj->product == $pplist->id){ echo 'selected'; } ?> value="<?php echo $pplist->id; ?>"><?php echo $pplist->name; ?></option>
									<?php } ?>
								</select>
								<span class="custom-error product_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch">Select Branch</label>
								<select data-valid="required" class="form-control getintrestedserviceedit" id="edit_intrested_branch" name="branch">
									<option value="">Please Select a Branch</option>
									<?php
								$catid = $obj->product;
		$pro = \App\Product::where('id', $catid)->first();
		if($pro){
		$user_array = explode(',',$pro->branches);
		$lists = \App\PartnerBranch::WhereIn('id',$user_array)->Where('partner_id',$pro->partner)->orderby('name','ASC')->get();

									foreach($lists as $list){
										?>
										<option  <?php if($obj->branch == $list->id){ echo 'selected'; } ?> value="<?php echo $list->id; ?>"><?php echo $list->name; ?></option>
									<?php } ?>
								</select>
								<span class="custom-error branch_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="expect_start_date">Expected Start Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<input type="text" name="expect_start_date" class="form-control datepicker" data-valid="required" autocomplete="off" placeholder="Select Date" value="<?php echo $obj->start_date; ?>">

								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error expect_start_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="expect_win_date">Expected Win Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<input type="text" name="expect_win_date" class="form-control datepicker" data-valid="required" autocomplete="off" placeholder="Select Date" value="<?php echo $obj->exp_date; ?>">

								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error expect_win_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('editinter_servform')" type="button" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			<?php
		}else{
			?>
			Not Found
			<?php
		}
	}
	}
	public function getintrestedservice(Request $request){
		$obj = \App\InterestedService::find($request->id);
		if($obj){
			$workflowdetail = \App\Workflow::where('id', $obj->workflow)->first();
			 $productdetail = \App\Product::where('id', $obj->product)->first();
			$partnerdetail = \App\Partner::where('id', $obj->partner)->first();
			$PartnerBranch = \App\PartnerBranch::where('id', $obj->branch)->first();
			$admin = \App\Admin::where('id', $obj->user_id)->first();
			?>
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel"><?php echo $workflowdetail->name; ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body ">
				<div class="interest_serv_detail">
					<div class="serv_status_added">
						<p>Status <?php if($obj->status == 1){ ?><span style="color:#6777ef;">Converted</span><?php }else{ ?><span style="">Draft</span><?php } ?></p>
						<p>Added On: <span class="text-muted"><?php echo date('Y-m-d', strtotime($obj->created_at)); ?></span></p>
						<p>Added By:<span class="text-muted"><span class="name"><?php echo substr($admin->first_name, 0, 1); ?></span><?php echo $admin->first_name; ?></span></p>
					</div>
					<div class="serv_detail">
						<h6>Service Details</h6>
						<?php if($obj->status == 0){ ?><a href="javascript:;" data-id="<?php echo $obj->id; ?>" class="openeditservices"><i class="fa fa-edit"></i></a><?php } ?>
						<div class="clearfix"></div>
						<div class="service_list">
							<ul>
								<li>Workflow <span><?php echo @$workflowdetail->name; ?></span></li>
								<li>Partner <span><?php echo @$partnerdetail->partner_name; ?></span></li>
								<li>Branch <span><?php echo @$PartnerBranch->name; ?></span></li>
								<li>Product <span><?php echo @$productdetail->name; ?></span></li>
								<li>Expected Start Date <span><?php echo $obj->start_date; ?></span></li>
								<li>Expected Win Date <span><?php echo $obj->exp_date; ?></span></li>
							</ul>
							<div class="clearfix"></div>
						</div>
					</div>
					<div class="divider"></div>
					<div class="prod_fees_sec productfeedata">
						<div class="cus_prod_fees">
							<h5>Product Fees <span>AUD</span></h5>
							<?php if($obj->status == 0){ ?><a href="javascript:;" data-id="<?php echo $obj->id; ?>" class="openpaymentfeeserv"><i class="fa fa-edit"></i></a><?php } ?>
							<div class="clearfix"></div>
						</div>
						<?php
						$totl = 0.00;
						$discount = 0.00;
						$appfeeoption = \App\ServiceFeeOption::where('app_id', $obj->id)->first();
						if($appfeeoption){
							?>
							<div class="prod_type">Installment Type: <span class="installtype"><?php echo $appfeeoption->installment_type; ?></span></div>
						<div class="feedata">
						<?php
						$appfeeoptiontype = \App\ServiceFeeOptionType::where('fee_id', $appfeeoption->id)->get();
						foreach($appfeeoptiontype as $fee){
							$totl += $fee->total_fee;
						?>
						<p class="clearfix">
							<span class="float-left">Tuition Fee <span class="note">(<?php echo $fee->installment; ?> installments at <span class="classfee"><?php echo $fee->inst_amt; ?></span> each)</span></span>
							<span class="float-right text-muted feetotl"><?php echo $fee->inst_amt * $fee->installment; ?></span>
						</p>
						<?php }

						if(@$appfeeoption->total_discount != ''){
								$discount = @$appfeeoption->total_discount;
							}
							$net = $totl -  $discount;
						?>
						</div>
						<p class="clearfix" style="color:#ff0000;">
							<span class="float-left">Client Discounts</span>
							<span class="float-right text-muted client_dicounts"><?php echo $discount; ?></span>
						</p>
						<p class="clearfix" style="color:#6777ef;">
							<span class="float-left">Total</span>
							<span class="float-right text-muted client_totl"><?php echo $net; ?></span>
						</p>
							<?php
						}else{
							?>
							<div class="prod_type">Installment Type: <span class="installtype">Per Semester</span></div>
						<div class="feedata">
						<p class="clearfix">
							<span class="float-left">Tuition Fee <span class="note">(1 installments at <span class="classfee">0.00</span> each)</span></span>
							<span class="float-right text-muted feetotl">0.00</span>
						</p>
						</div>
						<p class="clearfix" style="color:#ff0000;">
							<span class="float-left">Client Discounts</span>
							<span class="float-right text-muted client_dicounts">0.00</span>
						</p>
						<p class="clearfix" style="color:#6777ef;">
							<span class="float-left">Total</span>
							<span class="float-right text-muted client_totl">0.00</span>
						</p>
							<?php
						}
						?>

					</div>
					<div class="divider"></div>
					<div class="prod_fees_sec">
						<div class="cus_prod_fees">
						<?php
			$client_revenue = '0.00';
			if($obj->client_revenue != ''){
				$client_revenue = $obj->client_revenue;
			}
			$partner_revenue = '0.00';
			if($obj->partner_revenue != ''){
				$partner_revenue = $obj->partner_revenue;
			}
			$discounts = '0.00';
			if($obj->discounts != ''){
				$discounts = $obj->discounts;
			}
			$nettotal = $client_revenue + $partner_revenue - $discounts;
			?>
							<h5>Sales Forecast <span>AUD</span></h5>
							<?php if($obj->status == 0){ ?><a href="javascript:;" data-id="<?php echo $obj->id; ?>" data-client_revenue="<?php echo $client_revenue; ?>" data-partner_revenue="<?php echo $partner_revenue; ?>" data-discounts="<?php echo $discounts; ?>" class="opensaleforcastservice"><i class="fa fa-edit"></i></a><?php } ?>
							<div class="clearfix"></div>
						</div>
						<p class="clearfix appsaleforcastserv">
							<span class="float-left">Partner Revenue</span></span>
							<span class="float-right text-muted partner_revenue"><?php echo $partner_revenue; ?></span>
						</p>
						<p class="clearfix appsaleforcastserv">
							<span class="float-left">Client Revenue</span></span>
							<span class="float-right text-muted client_revenue"><?php echo $client_revenue; ?></span>
						</p>
						<p class="clearfix appsaleforcastserv" style="color:#ff0000;">
							<span class="float-left">Client Discounts</span>
							<span class="float-right text-muted discounts"><?php echo $discounts; ?></span>
						</p>
						<p class="clearfix appsaleforcastserv" style="color:#6777ef;">
							<span class="float-left">Total</span>
							<span class="float-right text-muted netrevenue"><?php echo number_format($nettotal,2,'.',''); ?></span>
						</p>
					</div>
					<!--<div class="prod_comment">
						<h6>Comment</h6>
						<div class="form-group">
							<textarea class="form-control" name="comment" placeholder="Enter comment here"></textarea>
						</div>
						<div class="form-btn">
							<button type="button" class="btn btn-primary">Save</button>
						</div>
					</div>
					<div class="serv_logs">
						<h6>Logs</h6>
						<div class="logs_list">
							<div class=""></div>
						</div>
					</div>-->
				</div>
			</div>
			<?php
		}else{
			?>
			Record Not Found
			<?php
		}
	}

	public function saleforcastservice(Request $request){
		$requestData = $request->all();

			$user_id = @Auth::user()->id;
			$obj = \App\InterestedService::find($request->fapp_id);
			$obj->client_revenue = $request->client_revenue;
			$obj->partner_revenue = $request->partner_revenue;
			$obj->discounts = $request->discounts;
			$saved = $obj->save();
			if($saved){

				$response['status'] 	= 	true;
				$response['message']	=	'Sales Forecast successfully updated.';
				$response['client_revenue']	=	$obj->client_revenue;
				$response['partner_revenue']	=	$obj->partner_revenue;
				$response['discounts']	=	$obj->discounts;
				$response['client_id']	=	$obj->client_id;

			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}

		echo json_encode($response);
	}


	public function savetoapplication(Request $request){
		if(Admin::where('role', '=', '7')->where('id', $request->contact)->exists()){
			$workflow = $request->workflow;

			$partner = $request->partner_id;
			$branch = $request->branch;
			$product = $request->product_id;
			$client_id = $request->contact;
			$status = 0;
			$stage = 'Application';
			$sale_forcast = 0.00;
			if(\App\Application::where('client_id', $client_id)->where('product_id', $product)->where('partner_id', $partner)->exists()){
				$response['status'] 	= 	false;
				$response['message']	=	'Application to the product already exists for this client.';
			}else{
				$obj = new \App\Application;
				$obj->user_id = Auth::user()->id;
				$obj->workflow = $workflow;
				$obj->partner_id = $partner;
				$obj->branch = $branch;
				$obj->product_id = $product;
				$obj->status = $status;
				$obj->stage = $stage;
				$obj->sale_forcast = $sale_forcast;
				$obj->client_id = $client_id;
				$saved = $obj->save();
				if($saved){
					$productdetail = \App\Product::where('id', $product)->first();
					$partnerdetail = \App\Partner::where('id', $partner)->first();
					$PartnerBranch = \App\PartnerBranch::where('id', $branch)->first();
					$subject = 'has started an application';
					$objs = new ActivitiesLog;
					$objs->client_id = $request->client_id;
					$objs->created_by = Auth::user()->id;
					$objs->description = '<span class="text-semi-bold">'.@$productdetail->name.'</span><p>'.@$partnerdetail->partner_name.' ('.@$PartnerBranch->name.')</p>';
					$objs->subject = $subject;
					$objs->save();
					$response['status'] 	= 	true;
					$response['message']	=	'You’ve successfully updated your client’s information.';
				}else{
					$response['status'] 	= 	false;
					$response['message']	=	'Please try again';
				}
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}


	public function showproductfeeserv(Request $request){
		$id = $request->id;
		ob_start();
		$appfeeoption = \App\ServiceFeeOption::where('app_id', $id)->first();

		?>
		<form method="post" action="<?php echo \URL::to('/admin/servicesavefee'); ?>" name="servicefeeform" id="servicefeeform" autocomplete="off" enctype="multipart/form-data">
				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
				<input type="hidden" name="id" value="<?php echo $id; ?>">
					<div class="row">
						<div class="col-12 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="fee_option_name">Fee Option Name <span class="span_req">*</span></label>
								<input type="text" readonly name="fee_option_name" class="form-control" value="Default Fee">

								<span class="custom-error feeoption_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="country_residency">Country of Residency <span class="span_req">*</span></label>
								<input type="text" readonly name="country_residency" class="form-control" value="All Countries">
								<span class="custom-error country_residency_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="degree_level">Installment Type <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control degree_level installment_type select2" name="degree_level">
									<option value="">Select Type</option>
									<option value="Full Fee" <?php if(@$appfeeoption->installment_type == 'Full Fee'){ echo 'selected'; }?>>Full Fee</option>
									<option value="Per Year" <?php if(@$appfeeoption->installment_type == 'Per Year'){ echo 'selected'; }?>>Per Year</option>
									<option value="Per Month" <?php if(@$appfeeoption->installment_type == 'Per Month'){ echo 'selected'; }?>>Per Month</option>
									<option value="Per Term" <?php if(@$appfeeoption->installment_type == 'Per Term'){ echo 'selected'; }?>>Per Term</option>
									<option value="Per Trimester" <?php if(@$appfeeoption->installment_type == 'Per Trimester'){ echo 'selected'; }?>>Per Trimester</option>
									<option value="Per Semester" <?php if(@$appfeeoption->installment_type == 'Per Semester'){ echo 'selected'; }?>>Per Semester</option>
									<option value="Per Week" <?php if(@$appfeeoption->installment_type == 'Per Week'){ echo 'selected'; }?>>Per Week</option>
									<option value="Installment" <?php if(@$appfeeoption->installment_type == 'Installment'){ echo 'selected'; }?>>Installment</option>
								</select>
								<span class="custom-error degree_level_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="table-responsive">
								<table class="table text_wrap" id="productitemview">
									<thead>
										<tr>
											<th>Fee Type <span class="span_req">*</span></th>
											<th>Per Semester Amount <span class="span_req">*</span></th>
											<th>No. of Semester <span class="span_req">*</span></th>
											<th>Total Fee</th>
											<th>Claimable Semester</th>
											<th>Commission %</th>


										</tr>
									</thead>
									<tbody class="tdata">
									<?php
									$totl = 0.00;
									$discount = 0.00;
									if($appfeeoption){
										$appfeeoptiontype = \App\ServiceFeeOptionType::where('fee_id', $appfeeoption->id)->get();
										foreach($appfeeoptiontype as $fee){
											$totl += $fee->total_fee;
										?>
										<tr class="add_fee_option cus_fee_option">
											<td>
												<select data-valid="required" class="form-control course_fee_type " name="course_fee_type[]">
													<option value="">Select Type</option>
													<option value="Accommodation Fee" <?php if(@$fee->fee_type == 'Accommodation Fee'){ echo 'selected'; }?>>Accommodation Fee</option>
													<option value="Administration Fee" <?php if(@$fee->fee_type == 'Administration Fee'){ echo 'selected'; }?>>Administration Fee</option>
													<option value="Airline Ticket" <?php if(@$fee->fee_type == 'Airline Ticket'){ echo 'selected'; }?>>Airline Ticket</option>
													<option value="Airport Transfer Fee" <?php if(@$fee->fee_type == 'Airport Transfer Fee'){ echo 'selected'; }?>>Airport Transfer Fee</option>
													<option value="Application Fee" <?php if(@$fee->fee_type == 'Application Fee'){ echo 'selected'; }?>>Application Fee</option>
													<option value="Bond" <?php if(@$fee->fee_type == 'Bond'){ echo 'selected'; }?>>Bond</option>
												</select>
											</td>
											<td>
												<input type="number" value="<?php echo @$fee->inst_amt; ?>" class="form-control semester_amount" name="semester_amount[]">
											</td>
											<td>
												<input type="number" value="<?php echo @$fee->installment; ?>" class="form-control no_semester" name="no_semester[]">
											</td>
											<td class="total_fee"><span><?php echo @$fee->total_fee; ?></span><input value="<?php echo @$fee->total_fee; ?>" type="hidden"  class="form-control total_fee_am" name="total_fee[]"></td>
											<td>
												<input type="number" value="<?php echo @$fee->claim_term; ?>" class="form-control claimable_terms" name="claimable_semester[]">
											</td>
											<td>
												<input type="number" value="<?php echo @$fee->commission; ?>" class="form-control commission" name="commission[]">
											</td>

										</tr>
										<?php
										}
									}else{
									?>
										<tr class="add_fee_option cus_fee_option">
											<td>
												<select data-valid="required" class="form-control course_fee_type " name="course_fee_type[]">
													<option value="">Select Type</option>
													<option value="Accommodation Fee">Accommodation Fee</option>
													<option value="Administration Fee">Administration Fee</option>
													<option value="Airline Ticket">Airline Ticket</option>
													<option value="Airport Transfer Fee">Airport Transfer Fee</option>
													<option value="Application Fee">Application Fee</option>
													<option value="Bond">Bond</option>
												</select>
											</td>
											<td>
												<input type="number" value="0" class="form-control semester_amount" name="semester_amount[]">
											</td>
											<td>
												<input type="number" value="1" class="form-control no_semester" name="no_semester[]">
											</td>
											<td class="total_fee"><span>0.00</span><input value="0" type="hidden"  class="form-control total_fee_am" name="total_fee[]"></td>
											<td>
												<input type="number" value="1" class="form-control claimable_terms" name="claimable_semester[]">
											</td>
											<td>
												<input type="number" class="form-control commission" name="commission[]">
											</td>

										</tr>
	<?php }

	$net = $totl -  $discount;
	?>
									</tbody>
									<tfoot>
										<tr>
											<td><input type="text" readonly value="Discounts" name="discount" class="form-control"></td>
											<td><input type="number"  value="<?php echo @$appfeeoption->discount_amount; ?>" name="discount_amount" class="form-control discount_amount"></td>
											<td><input type="number"  value="<?php if(@$appfeeoption->discount_sem != ''){ echo @$appfeeoption->discount_sem; }else{ echo 0.00; } ?>" name="discount_sem" class="form-control discount_sem"></td>
											<td class="totaldis" style="color:#ff0000;"><span><?php if(@$appfeeoption->total_discount != ''){ echo @$appfeeoption->total_discount; }else{ echo 0.00; } ?></span><input value="<?php if(@$appfeeoption->total_discount != ''){ echo @$appfeeoption->total_discount; }else{ echo 0.00; } ?>" type="hidden" class="form-control total_dis_am" name="total_discount"></td>
											<td><input type="text"  readonly value="" name="" class="form-control"></td>
											<td><input type="text"  readonly value="" name="" class="form-control"></td>
										</tr>
										<tr>
											<?php
											$dis = 0.00;
											if(@$appfeeoption->total_discount != ''){
												$dis = @$appfeeoption->total_discount;
											}
											$duductamt = $net - $dis;
											?>
											<td colspan="3" style="text-align: right;"><b>Net Total</b></td>
											<td class="net_totl text-info"><?php echo $duductamt; ?></td>
											<td colspan="3"></td>
										</tr>
									</tfoot>
								</table>
							</div>
							<div class="fee_option_addbtn">
								<a href="#" class="btn btn-primary"><i class="fas fa-plus"></i> Add Fee</a>
							</div>

						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('servicefeeform')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
		<?php
		return ob_get_clean();
	}



	public function servicesavefee(Request $request){
		$requestData = $request->all();
		$InterestedService = \App\InterestedService::find($request->id);
		if(ServiceFeeOption::where('app_id', $request->id)->exists()){
			$o = ServiceFeeOption::where('app_id', $request->id)->first();
			$obj = ServiceFeeOption::find($o->id);
			$obj->user_id = Auth::user()->id;
			$obj->app_id = $request->id;
			$obj->name = $requestData['fee_option_name'];
			$obj->country = $requestData['country_residency'];
			$obj->installment_type = $requestData['degree_level'];
			$obj->discount_amount = $requestData['discount_amount'];
			$obj->discount_sem = $requestData['discount_sem'];
			$obj->total_discount = $requestData['total_discount'];
			$saved = $obj->save();
			if($saved){
				ServiceFeeOptionType::where('fee_id', $obj->id)->delete();
				$course_fee_type = $requestData['course_fee_type'];
				$totl = 0;
				for($i = 0; $i< count($course_fee_type); $i++){
					$totl += $requestData['total_fee'][$i];
					$objs = new ServiceFeeOptionType;
					$objs->fee_id = $obj->id;
					$objs->fee_type = $requestData['course_fee_type'][$i];
					$objs->inst_amt = $requestData['semester_amount'][$i];
					$objs->installment = $requestData['no_semester'][$i];
					$objs->total_fee = $requestData['total_fee'][$i];
					$objs->claim_term = $requestData['claimable_semester'][$i];
					$objs->commission = $requestData['commission'][$i];

					$saved = $objs->save();
					$p = '<p class="clearfix">
							<span class="float-left">'.$requestData['course_fee_type'][$i].' <span class="note">('.$requestData['no_semester'][$i].' installments at <span class="classfee">'.$requestData['total_fee'][$i].'</span> each)</span></span>
							<span class="float-right text-muted feetotl">'.$requestData['total_fee'][$i].'</span>
						</p>';
				}
				$discount = 0.00;
				if(@$obj->total_discount != ''){
				$discount = @$obj->total_discount;
				}
				$response['status'] 	= 	true;
					$response['message']	=	'Fee Option added successfully';
					$response['installment_type']	=	$obj->installment_type;
				$response['feedata']			=	$p;
				$response['totalfee']			=	$totl;
				$response['discount']			=	$discount;
				$response['client_id']			=	$InterestedService->client_id;
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Record not found';
			}
		}else{
			$obj = new ServiceFeeOption;
			$obj->user_id = Auth::user()->id;
			$obj->app_id = $request->id;
			$obj->name = $requestData['fee_option_name'];
			$obj->country = $requestData['country_residency'];
			$obj->installment_type = $requestData['degree_level'];
			$obj->discount_amount = $requestData['discount_amount'];
			$obj->discount_sem = $requestData['discount_sem'];
			$obj->total_discount = $requestData['total_discount'];
			$saved = $obj->save();
			if($saved){
				$course_fee_type = $requestData['course_fee_type'];
				$totl = 0;
				$p = '';
				for($i = 0; $i< count($course_fee_type); $i++){
					$totl += $requestData['total_fee'][$i];
					$objs = new ServiceFeeOptionType;
					$objs->fee_id = $obj->id;
					$objs->fee_type = $requestData['course_fee_type'][$i];
					$objs->inst_amt = $requestData['semester_amount'][$i];
					$objs->installment = $requestData['no_semester'][$i];
					$objs->total_fee = $requestData['total_fee'][$i];
					$objs->claim_term = $requestData['claimable_semester'][$i];
					$objs->commission = $requestData['commission'][$i];

					$saved = $objs->save();

				}
				$discount = 0.00;
				if(@$obj->total_discount != ''){
					$discount = @$obj->total_discount;
				}
				$appfeeoption = \App\ServiceFeeOption::where('app_id', $obj->id)->first();
				$totl = 0.00;

				if($appfeeoption){
					$appfeeoptiontype = \App\ServiceFeeOptionType::where('fee_id', $appfeeoption->id)->get();
					foreach($appfeeoptiontype as $fee){
						$totl += $fee->total_fee;
						$p = '<p class="clearfix">
							<span class="float-left">'.$fee->installment.' <span class="note">('.$fee->installment.' installments at <span class="classfee">'.$fee->inst_amt.'</span> each)</span></span>
							<span class="float-right text-muted feetotl">'.$fee->inst_amt * $fee->installment.'</span>
						</p>';
					}
				}
				$response['status'] 				= 	true;
				$response['message']			=	'Fee Option added successfully';
				$response['installment_type']	=	$obj->installment_type;
				$response['feedata']			=	$p;
				$response['totalfee']			=	$totl;
				$response['discount']			=	$discount;
				$response['client_id']			=	$InterestedService->client_id;
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Record not found';
			}
		}
		echo json_encode($response);
	}

	public function pinnote(Request $request){
		$requestData = $request->all();

		if(\App\Note::where('id',$requestData['note_id'])->exists()){
			$note = \App\Note::where('id',$requestData['note_id'])->first();
			if($note->pin == 0){
				$obj = \App\Note::find($note->id);
				$obj->pin = 1;
				$saved = $obj->save();
			}else{
				$obj = \App\Note::find($note->id);
				$obj->pin = 0;
				$saved = $obj->save();
			}
			$response['status'] 				= 	true;
			$response['message']			=	'Fee Option added successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Record not found';
		}
		echo json_encode($response);
	}

	public function changetype(Request $request,$id = Null, $slug = Null){
	    if(isset($id) && !empty($id))
			{

				$id = $this->decodeString($id);
				if(Admin::where('id', '=', $id)->where('role', '=', '7')->exists())
				{
					$obj = Admin::find($id);
					$obj->type = $slug;
					$saved = $obj->save();

					return Redirect::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$id)))->with('success', 'Record Updated successfully');
				}
				else
				{
					return Redirect::to('/admin/clients')->with('error', 'Clients Not Exist');
				}
			}
			else
			{
				return Redirect::to('/admin/clients')->with('error', Config::get('constants.unauthorized'));
			}
	}

   //asssign followup and save
	public function followupstore(Request $request){
	    $requestData 		= 	$request->all();
        //echo '<pre>'; print_r($requestData); die;
        /*if(\App\Note::where('client_id',$requestData['client_id'])->where('assigned_to',$requestData['rem_cat'])->exists())
        {
            // return redirect()->back()->with('error', 'Lead already assigned');
            // return Redirect::to('/admin/assignee')->with('error', 'Lead already assigned');
            echo json_encode(array('success' => false, 'message' => 'Lead already assigned to '.@$requestData['assignee_name'], 'clientID' => $requestData['client_id']));
            exit;
        }*/

        $followup 				= new \App\Note;
        $followup->client_id		= $this->decodeString(@$requestData['client_id']);
		$followup->user_id			= Auth::user()->id;
		$followup->description		= @$requestData['description'];
		$followup->title		    = @$requestData['remindersubject'] ?? 'Lead assign to '.@$requestData['assignee_name'];
		$followup->folloup	= 1;
        $followup->task_group = @$requestData['task_group'];
		$followup->assigned_to	= @$requestData['rem_cat'];
		if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
		    //	$followup->followup_date	= @$requestData['followup_date'].date('H:i', strtotime($requestData['followup_time']));
			$followup->followup_date	=  @$requestData['followup_datetime'];
		}
        $saved				=	$followup->save();
        if(!$saved)
		{
			echo json_encode(array('success' => false, 'message' => 'Please try again', 'clientID' => $requestData['client_id']));
		}
		else
		{
			if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
                $Lead = Admin::find($this->decodeString($requestData['client_id']));
                $Lead->followup_date = @$requestData['followup_datetime'];
                $Lead->save();
			}

			$o = new \App\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = @$requestData['rem_cat'];
	    	$o->module_id = $this->decodeString(@$requestData['client_id']);
	    	$o->url = \URL::to('/admin/clients/detail/'.@$requestData['client_id']);
	    	$o->notification_type = 'client';
	    	$o->message = 'Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name.' '.date('d/M/Y h:i A',strtotime($Lead->followup_date));
	    	$o->save();

			$objs = new ActivitiesLog;
            $objs->client_id = $this->decodeString(@$requestData['client_id']);
            $objs->created_by = Auth::user()->id;
            //$objs->subject = 'Followup set for '.date('d/M/Y h:i A',strtotime($Lead->followup_date));
            $objs->subject = 'set action for '.@$requestData['assignee_name'];
            $objs->description = '<span class="text-semi-bold">'.@$requestData['remindersubject'].'</span><p>'.@$requestData['description'].'</p>';
            if(Auth::user()->id != @$requestData['rem_cat']){
                $objs->use_for = @$requestData['rem_cat'];
            } else {
                $objs->use_for = "";
            }
            $objs->followup_date = @$requestData['followup_datetime'];
            $objs->task_group = @$requestData['task_group'];
            $objs->save();
			echo json_encode(array('success' => true, 'message' => 'successfully saved', 'clientID' => $requestData['client_id']));
			exit;
		}
	}
	
	//task reassign and update exist followup
	public function reassignfollowupstore(Request $request){
	    $requestData 		= 	$request->all();
        //echo '<pre>'; print_r($requestData); die;
        /*if(\App\Note::where('client_id',$requestData['client_id'])->where('assigned_to',$requestData['rem_cat'])->exists())
        {
            // return redirect()->back()->with('error', 'Lead already assigned');
            // return Redirect::to('/admin/assignee')->with('error', 'Lead already assigned');
            echo json_encode(array('success' => false, 'message' => 'Lead already assigned to '.@$requestData['assignee_name'], 'clientID' => $requestData['client_id']));
            exit;
        }*/

        $followup = \App\Note::where('id', '=', $requestData['note_id'])->first();
        $followup->id               = $followup ->id;
		$followup->client_id		= $this->decodeString(@$requestData['client_id']);
		$followup->user_id			= Auth::user()->id;
		$followup->description		= @$requestData['description'];
		$followup->title		    = @$requestData['remindersubject'] ?? 'Lead assign to '.@$requestData['assignee_name'];
		$followup->folloup	= 1;
        $followup->task_group = @$requestData['task_group'];
		$followup->assigned_to	= @$requestData['rem_cat'];
		if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
		    //	$followup->followup_date	= @$requestData['followup_date'].date('H:i', strtotime($requestData['followup_time']));
			$followup->followup_date	=  @$requestData['followup_datetime'];
		}
        $saved				=	$followup->save();
        if(!$saved)
		{
			echo json_encode(array('success' => false, 'message' => 'Please try again', 'clientID' => $requestData['client_id']));
		}
		else
		{
			if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
                $Lead = Admin::find($this->decodeString($requestData['client_id']));
                $Lead->followup_date = @$requestData['followup_datetime'];
                $Lead->save();
			}

			$o = new \App\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = @$requestData['rem_cat'];
	    	$o->module_id = $this->decodeString(@$requestData['client_id']);
	    	$o->url = \URL::to('/admin/clients/detail/'.@$requestData['client_id']);
	    	$o->notification_type = 'client';
	    	$o->message = 'Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name.' '.date('d/M/Y h:i A',strtotime($Lead->followup_date));
	    	$o->save();

			$objs = new ActivitiesLog;
            $objs->client_id = $this->decodeString(@$requestData['client_id']);
            $objs->created_by = Auth::user()->id;
            //$objs->subject = 'Followup set for '.date('d/M/Y h:i A',strtotime($Lead->followup_date));
            $objs->subject = 'set action for '.@$requestData['assignee_name'];
            $objs->description = '<span class="text-semi-bold">'.@$requestData['remindersubject'].'</span><p>'.@$requestData['description'].'</p>';
            if(Auth::user()->id != @$requestData['rem_cat']){
                $objs->use_for = @$requestData['rem_cat'];
            } else {
                $objs->use_for = "";
            }
            $objs->followup_date = @$requestData['followup_datetime'];
            $objs->task_group = @$requestData['task_group'];
            $objs->save();
			echo json_encode(array('success' => true, 'message' => 'successfully saved', 'clientID' => $requestData['client_id']));
			exit;
		}
	}

    //update task follow up and save
	public function updatefollowup(Request $request){
	    $requestData 		= 	$request->all();

        //echo '<pre>'; print_r($requestData); die;
        /*if(\App\Note::where('client_id',$requestData['client_id'])->where('assigned_to',$requestData['rem_cat'])->exists())
        {
            echo json_encode(array('success' => false, 'message' => 'Lead already assigned to '.@$requestData['assignee_name'], 'clientID' => $requestData['client_id']));
            exit;
        }*/

        $followup = \App\Note::where('id', '=', $requestData['note_id'])->first();
        //$followup 				= new \App\Note;
        $followup->id               = $followup ->id;
		$followup->client_id		= $this->decodeString(@$requestData['client_id']);
		$followup->user_id			= Auth::user()->id;
		$followup->description		= @$requestData['description'];
		$followup->title		    = @$requestData['remindersubject'] ?? 'Update Task and lead assign to '.@$requestData['assignee_name'];
		$followup->folloup	= 1;
        $followup->task_group = @$requestData['task_group'];
		$followup->assigned_to	= @$requestData['rem_cat'];
		if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
		    //	$followup->followup_date	= @$requestData['followup_date'].date('H:i', strtotime($requestData['followup_time']));
			$followup->followup_date	=  @$requestData['followup_datetime'];
		}
        $saved	=	$followup->save();

		if(!$saved)
		{
			echo json_encode(array('success' => false, 'message' => 'Please try again', 'clientID' => $requestData['client_id']));
		}
		else
		{
			if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
                $Lead = Admin::find($this->decodeString($requestData['client_id']));
                $Lead->followup_date = @$requestData['followup_datetime'];
                $Lead->save();
			}

			$o = new \App\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = @$requestData['rem_cat'];
	    	$o->module_id = $this->decodeString(@$requestData['client_id']);
	    	$o->url = \URL::to('/admin/clients/detail/'.@$requestData['client_id']);
	    	$o->notification_type = 'client';
	    	$o->message = 'Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name.' '.date('d/M/Y h:i A',strtotime($Lead->followup_date));
	    	$o->save();

			$objs = new ActivitiesLog;
            $objs->client_id = $this->decodeString(@$requestData['client_id']);
            $objs->created_by = Auth::user()->id;
            //$objs->subject = 'Followup set for '.date('d/M/Y h:i A',strtotime($Lead->followup_date));
            $objs->subject = 'Update task for '.@$requestData['assignee_name'];
            $objs->description = '<span class="text-semi-bold">'.@$requestData['remindersubject'].'</span><p>'.@$requestData['description'].'</p>';
            if(Auth::user()->id != @$requestData['rem_cat']){
                $objs->use_for = @$requestData['rem_cat'];
            } else {
                $objs->use_for = "";
            }

            $objs->followup_date = @$requestData['followup_datetime'];
            $objs->task_group = @$requestData['task_group'];
            $objs->save();
			echo json_encode(array('success' => true, 'message' => 'successfully saved', 'clientID' => $requestData['client_id']));
			exit;
		}
	}


    //personal followup
    public function personalfollowup(Request $request){
	    $requestData 		= 	$request->all();
        //echo '<pre>'; print_r($requestData); die;

        if( isset($requestData['client_id']) && strpos($requestData['client_id'],"/")){
            $req_client_arr = explode("/",$requestData['client_id']);
            if(!empty($req_client_arr)){
                $req_clientID = $req_client_arr[0];
                $client_id = $this->decodeString($req_clientID );
                //echo $req_clientID."====".$client_id;die;
            }
        } else {
            $req_clientID = "";
            $client_id = "";
        }
        //echo "####".$this->decodeString(@$requestData['client_id']);die;

        /*if(\App\Note::where('client_id',$requestData['client_id'])->where('assigned_to',$requestData['rem_cat'])->exists())
        {
            echo json_encode(array('success' => false, 'message' => 'Lead already assigned to '.@$requestData['assignee_name'], 'clientID' => $req_clientID));
            exit;
        }*/
		$followup 					= new \App\Note;
		$followup->client_id		= $client_id;//$this->decodeString(@$requestData['client_id']);
		$followup->user_id			= Auth::user()->id;
		$followup->description		= @$requestData['description'];
		$followup->title		    = @$requestData['remindersubject'] ?? 'Personal Task assign to '.@$requestData['assignee_name'];
		$followup->folloup	= 1;
        $followup->task_group = @$requestData['task_group'];
		$followup->assigned_to	= @$requestData['rem_cat'];
		if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
		    $followup->followup_date	=  @$requestData['followup_datetime'];
		}
        $saved	=	$followup->save();
        if(!$saved)
		{
			echo json_encode(array('success' => false, 'message' => 'Please try again', 'clientID' => $client_id)); //$requestData['client_id']
		}
		else
		{
			$o = new \App\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = @$requestData['rem_cat'];
	    	$o->module_id = '';//$this->decodeString(@$requestData['client_id']);
	    	$o->url = '';//\URL::to('/admin/clients/detail/'.@$requestData['client_id']);
	    	$o->notification_type = 'client';
	    	$o->message = 'Personal Task Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name.' '.date('d/M/Y h:i A',strtotime($requestData['followup_datetime']));
	    	$o->save();

			$objs = new ActivitiesLog;
            $objs->client_id = $client_id;//$this->decodeString(@$requestData['client_id']);
            $objs->created_by = Auth::user()->id;
            $objs->subject = 'set action for '.@$requestData['assignee_name'];
            $objs->description = '<span class="text-semi-bold">'.@$requestData['remindersubject'].'</span><p>'.@$requestData['description'].'</p>';
            if(Auth::user()->id != @$requestData['rem_cat']){
                $objs->use_for = @$requestData['rem_cat'];
            } else {
                $objs->use_for = "";
            }
            $objs->followup_date = @$requestData['followup_datetime'];
            $objs->task_group = @$requestData['task_group'];
            $objs->save();
			echo json_encode(array('success' => true, 'message' => 'successfully saved', 'clientID' => $client_id)); //$requestData['client_id']
			exit;
		}
	}
	
	
	public function retagfollowup(Request $request){
	    $requestData 		= 	$request->all();

        //	echo '<pre>'; print_r($requestData); die;
		$followup 					= new \App\Note;
		$followup->client_id			= @$requestData['client_id'];
		$followup->user_id			= Auth::user()->id;
		$followup->description		= @$requestData['message'];
		$followup->title			= '';
		$followup->folloup	        = 1;
		$followup->assigned_to	    = @$requestData['changeassignee'];
		if(isset($requestData['followup_date']) && $requestData['followup_date'] != ''){

				$followup->followup_date	=  $requestData['followup_date'].' '.date('H:i', strtotime($requestData['followup_time']));
		}

		$saved				=	$followup->save();

		if(!$saved)
		{
		return Redirect::to('/admin/followup-dates')->with('error', 'Please try again');
		}
		else
		{
		   /*$objnote =  \App\Note::find();
		   $objnote->status = 1;
		   $objnote->save();*/
		    $newassignee = Admin::find($requestData['changeassignee']);
			$o = new \App\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = @$requestData['changeassignee'];
	    	$o->module_id = @$requestData['client_id'];
	    	$o->url = \URL::to('/admin/clients/detail/'.@$requestData['client_id']);
	    	$o->notification_type = 'client';
	    	$o->message = 'Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name;
	    	$o->save();

			$objs = new ActivitiesLog;
				$objs->client_id = @$requestData['client_id'];
				$objs->created_by = Auth::user()->id;
				$objs->subject = Auth::user()->first_name.' '.Auth::user()->last_name.' tags work to '.$newassignee->first_name.' '.$newassignee->last_name;
				$objs->description = @$requestData['message'];
				$objs->save();
		return Redirect::to('/admin/followup-dates')->with('success', 'Record Updated successfully');
		}
	}

		/*public function change_assignee(Request $request){
    		$objs = Admin::find($request->id);
    		$objs->assignee = $request->assinee;

    		$saved = $objs->save();

    		if($saved){
    		    $o = new \App\Notification;
    	    	$o->sender_id = Auth::user()->id;
    	    	$o->receiver_id = $request->assinee;
    	    	$o->module_id = $request->id;
    	    	$o->url = \URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$request->id)));
    	    	$o->notification_type = 'client';
    	    	$o->message = 'Client Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name;
    	    	$o->save();
    			$response['status'] 	= 	true;
    			$response['message']	=	'Updated successfully';
    		}else{
    			$response['status'] 	= 	false;
    			$response['message']	=	'Please try again';
    		}
    		echo json_encode($response);
    	}*/
  
       public function change_assignee(Request $request){
            //dd( $request->all() );
    		$objs = Admin::find($request->id);
            if ( is_array($request->assinee) ) {
                if( count($request->assinee) < 1){
                    $objs->assignee = "";
                } else if( count($request->assinee) == 1){
                    $objs->assignee = $request->assinee[0];
                } else if( count($request->assinee) > 1){
                    $objs->assignee = implode(",",$request->assinee);
                }
            }
    		//$objs->assignee = $request->assinee;
            $saved = $objs->save();
            if($saved){
                if ( is_array($request->assinee) && count($request->assinee) >=1) {
                    $assigneeArr = $request->assinee;
                    foreach($assigneeArr as $key=>$val) {
                        $o = new \App\Notification;
                        $o->sender_id = Auth::user()->id;
                        $o->receiver_id = $val; //$request->assinee;
                        $o->module_id = $request->id;
                        $o->url = \URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$request->id)));
                        $o->notification_type = 'client';
                        $o->message = 'Client Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name;
                        $o->save();
                    }
                }
                $response['status'] 	= 	true;
    			$response['message']	=	'Updated successfully';
    		}else{
    			$response['status'] 	= 	false;
    			$response['message']	=	'Please try again';
    		}
    		echo json_encode($response);
    	}

    	public function saveprevvisa(Request $request){
    	    $requestData 		= 	$request->all();
    	     $obj = Admin::find($requestData['client_id']);
    	    $pr = array();
    	    $i = 0;
    	  $start_date =  $requestData['prev_visa']['start_date'];
    	   $end_date =  $requestData['prev_visa']['end_date'];
    	    $place =  $requestData['prev_visa']['place'];
    	     $person =  $requestData['prev_visa']['person'];

    	    foreach($requestData['prev_visa']['name'] as  $prev_visa){

    	       $pr[] = array(
    	                'name' => $prev_visa,
    	                'start_date' => $start_date[$i],
    	                'end_date' =>$end_date[$i],
    	                'place' =>$place[$i],
    	                'person' =>$person[$i],
    	            );
    	            $i++;
    	    }

    	     $obj->prev_visa = json_encode($pr);

    	     $save = $obj->save();
    	     if($save){
    	         return Redirect::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$requestData['client_id'])))->with('success', 'Previous Visa Updated Successfully');
    	     }else{
    	         return redirect()->back()->with('error', Config::get('constants.server_error'));
    	     }
    	}

    	public function removetag(Request $request){
    	    $objs = Admin::find($request->c);
    	    $itag = $request->rem_id;

    	    	if($objs->tagname != ''){
					$rs = explode(',', $objs->tagname);
					unset($rs[$itag]);

			    $objs->tagname = 	implode(',',@$rs);
			    $objs->save();
    	    	}
    	   return Redirect::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$objs->id)))->with('success', 'Record Updated successfully');
    	}

    	public function saveonlineform(Request $request){
    	   $requestData 		= 	$request->all();
    	   if(OnlineForm::where('client_id', $requestData['client_id'])->where('type', $requestData['type'])->exists()){
    	     $OnlineForm =  OnlineForm::where('client_id', $requestData['client_id'])->where('type', $requestData['type'])->first();
    	     $obj = OnlineForm::find($OnlineForm->id);
    	   }else{
    	       $obj = New OnlineForm;
    	   }

		   $parent_dob = '';
	        if($requestData['parent_dob'] != ''){
	           $dobs = explode('/', $requestData['parent_dob']);
	          $parent_dob = $dobs[2].'-'.$dobs[1].'-'. $dobs[0];
	        }

			 $parent_dob_2 = '';
	        if($requestData['parent_dob_2'] != ''){
	           $dobs = explode('/', $requestData['parent_dob_2']);
	          $parent_dob_2 = $dobs[2].'-'.$dobs[1].'-'. $dobs[0];
	        }
			$sibling_dob = '';
	        if($requestData['sibling_dob'] != ''){
	           $dobs = explode('/', $requestData['sibling_dob']);
	          $sibling_dob = $dobs[2].'-'.$dobs[1].'-'. $dobs[0];
	        }
			$sibling_dob_2 = '';
	        if($requestData['sibling_dob_2'] != ''){
	           $dobs = explode('/', $requestData['sibling_dob_2']);
	          $sibling_dob_2 = $dobs[2].'-'.$dobs[1].'-'. $dobs[0];
	        }

                $obj->client_id = $requestData['client_id'];
                $obj->type = $requestData['type'];
                $obj->info_name = $requestData['info_name'];
                $obj->main_lang = implode(',', $requestData['main_lang']);
                $obj->marital_status = $requestData['marital_status'];
                $obj->mobile = $requestData['mobile'];
                $obj->curr_address = $requestData['curr_address'];
                $obj->email = $requestData['email'];
                $obj->parent_name = $requestData['parent_name'];
                $obj->parent_dob = $parent_dob;
                $obj->parent_occ = $requestData['parent_occ'];
                $obj->parent_country = $requestData['parent_country'];
                $obj->parent_name_2 = $requestData['parent_name_2'];
                $obj->parent_dob_2 = $parent_dob_2;
                $obj->parent_occ_2 = $requestData['parent_occ_2'];
                $obj->parent_country_2 = $requestData['parent_country_2'];
                $obj->sibling_name = $requestData['sibling_name'];
                $obj->sibling_dob = $sibling_dob;
                $obj->sibling_occ = $requestData['sibling_occ'];
                $obj->sibling_gender = $requestData['sibling_gender'];
                $obj->sibling_country = $requestData['sibling_country'];
                $obj->sibling_marital = $requestData['sibling_marital'];
                $obj->sibling_name_2 = $requestData['sibling_name_2'];
                $obj->sibling_dob_2 = $sibling_dob_2;
                $obj->sibling_occ_2 = $requestData['sibling_occ_2'];
                $obj->sibling_gender_2 = $requestData['sibling_gender_2'];
                $obj->sibling_country_2 = $requestData['sibling_country_2'];
                $obj->sibling_marital_2 = $requestData['sibling_marital_2'];
                $obj->held_visa = $requestData['held_visa'];
                $obj->visa_refused = $requestData['visa_refused'];
                $obj->traveled = $requestData['traveled'];

    	     $save = $obj->save();
    	     if($save){
    	         return Redirect::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$requestData['client_id'])))->with('success', 'Record Updated Successfully');
    	     }else{
    	         return redirect()->back()->with('error', Config::get('constants.server_error'));
    	     }
    	}

	public function uploadmail(Request $request){
		$requestData 		= 	$request->all();
		//$obj		= 	Admin::find(@$requestData['id']);
		$obj				= 	new \App\MailReport;
		$obj->user_id		=	Auth::user()->id;
		$obj->from_mail 	=  $requestData['from'];
		$obj->to_mail 		=  $requestData['to'];
		$obj->subject		=  $requestData['subject'];
		$obj->message		=  $requestData['message'];
		$obj->mail_type		=  1;
		$obj->client_id		=  @$requestData['client_id'];
		$saved				=	$obj->save();
		if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}

			else
			{
				return redirect()->back()->with('success', 'Email uploaded Successfully');

			}
	}
	
	
	/*public function merge_records(Request $request){
        if(isset($request->merge_record_ids) && $request->merge_record_ids != ""){
            if( strpos($request->merge_record_ids, ',') !== false ) {
                $merge_record_ids_arr = explode(",",$request->merge_record_ids);
                //echo "<pre>";print_r($merge_record_ids_arr);die;

                //check 1st and 2nd record
                $first_record = Admin::where('id', $merge_record_ids_arr[0])->select('id','phone','att_phone','email','att_email')->first();
                //echo "<pre>";print_r($first_record);
                if(!empty($first_record)){
                    $first_phone = $first_record['phone'];
                    $first_att_phone = $first_record['att_phone'];
                    $first_email = $first_record['email'];
                    $first_att_email = $first_record['att_email'];
                }

                $second_record = Admin::where('id', $merge_record_ids_arr[1])->select('id','phone','att_phone','email','att_email')->first();
                //echo "<pre>";print_r($second_record);
                if(!empty($second_record)){
                    $second_phone = $second_record['phone'];
                    $second_att_phone = $second_record['att_phone'];
                    $second_email = $second_record['email'];
                    $second_att_email = $second_record['att_email'];
                }

               DB::table('admins')
                ->where('id', $merge_record_ids_arr[0])
                ->update(['att_phone' => $second_phone,'att_email' => $second_email]);

                DB::table('admins')
                ->where('id', $merge_record_ids_arr[1])
                ->update(['att_phone' => $first_phone,'att_email' => $first_email]);

                $notelist1 = Note::where('client_id', $merge_record_ids_arr[0])->whereNull('assigned_to')->where('type', 'client')->orderby('pin', 'DESC')->orderBy('created_at', 'DESC')->get();
                //dd($notelist1);

                $notelist2 = Note::where('client_id', $merge_record_ids_arr[1])->whereNull('assigned_to')->where('type', 'client')->orderby('pin', 'DESC')->orderBy('created_at', 'DESC')->get();
                //dd($notelist2);

                if(!empty($notelist2)){
                    foreach($notelist2 as $key2=>$list2){
                        $obj1 = new \App\Note;
                        $obj1->user_id = $list2->user_id;
                        $obj1->client_id = $merge_record_ids_arr[0];
                        $obj1->lead_id = $list2->lead_id;
                        $obj1->title = $list2->title;
                        $obj1->description = $list2->description;
                        $obj1->mail_id = $list2->mail_id;
                        $obj1->type = $list2->type;
                        $obj1->pin = $list2->pin;
                        $obj1->followup_date = $list2->followup_date;
                        $obj1->folloup = $list2->folloup;
                        $obj1->assigned_to = $list2->assigned_to;
                        $obj1->status = $list2->status;
                        $obj1->task_group = $list2->task_group;
                        $saved1 = $obj1->save();
                    }
                }

                if(!empty($notelist1)){
                    foreach($notelist1 as $key1=>$list1){
                        $obj2 = new \App\Note;
                        $obj2->user_id = $list1->user_id;
                        $obj2->client_id = $merge_record_ids_arr[1];
                        $obj2->lead_id = $list1->lead_id;
                        $obj2->title = $list1->title;
                        $obj2->description = $list1->description;
                        $obj2->mail_id = $list1->mail_id;
                        $obj2->type = $list1->type;
                        $obj2->pin = $list1->pin;
                        $obj2->followup_date = $list1->followup_date;
                        $obj2->folloup = $list1->folloup;
                        $obj2->assigned_to = $list1->assigned_to;
                        $obj2->status = $list1->status;
                        $obj2->task_group = $list1->task_group;
                        $saved2 = $obj2->save();
                    }
                }

                if($saved2){
                    $response['status'] 	= 	true;
				    $response['message']	=	'You have successfully merged records.';
                }else{
                    $response['status'] 	= 	false;
                    $response['message']	=	'Please try again';
                }
                echo json_encode($response);
            }
        }
    }*/
    
    /*public function merge_records(Request $request){
        $response = array();
        if(
            ( isset($request->merge_from) && $request->merge_from != "" )
            && ( isset($request->merge_into) && $request->merge_into != "" )
        ){
            //Update merge_from to be deleted
            DB::table('admins')->where('id',$request->merge_from)->update( array('is_deleted'=>1) );

            //activities_logs
            $activitiesLogs = DB::table('activities_logs')->where('client_id', $request->merge_from)->get(); //dd($activitiesLogs);
            if(!empty($activitiesLogs)){
                foreach($activitiesLogs as $actkey=>$actval){
                    DB::table('activities_logs')->insert(
                        [
                            'client_id' => $request->merge_into,
                            'created_by' => $actval->created_by,
                            'description' => $actval->description,
                            'created_at' => $actval->created_at,
                            'updated_at' => $actval->updated_at,
                            'subject' => $actval->subject,
                            'use_for' => $actval->use_for,
                            'followup_date' => $actval->followup_date,
                            'task_group' => $actval->task_group,
                            'task_status' => $actval->task_status
                        ]
                    );
                }
            }

            //notes
            $notes = DB::table('notes')->where('client_id', $request->merge_from)->get(); //dd($notes);
            if(!empty($notes)){
                foreach($notes as $notekey=>$noteval){
                    DB::table('notes')->insert(
                        [
                            'user_id'=> $noteval->user_id,
                            'client_id' => $request->merge_into,
                            'lead_id' => $noteval->lead_id,
                            'title' => $noteval->title,
                            'description' => $noteval->description,
                            'created_at' => $noteval->created_at,
                            'updated_at' => $noteval->updated_at,
                            'mail_id' => $noteval->mail_id,
                            'type' => $noteval->type,
                            'pin' => $noteval->pin,
                            'followup_date' => $noteval->followup_date,
                            'folloup' => $noteval->folloup,
                            'assigned_to' => $noteval->assigned_to,
                            'status' => $noteval->status,
                            'task_group' => $noteval->task_group,
                        ]
                    );
                }
            }


            //applications
            $applications = DB::table('applications')->where('client_id', $request->merge_from)->get(); //dd($applications);
            if(!empty($applications)){
                foreach($applications as $appkey=>$appval){
                    DB::table('applications')->insert(
                        [
                            'user_id'=> $appval->user_id,
                            'workflow' => $appval->workflow,
                            'partner_id' => $appval->partner_id,
                            'product_id' => $appval->product_id,
                            'status' => $appval->status,
                            'stage' => $appval->stage,
                            'sale_forcast' => $appval->sale_forcast,
                            'created_at' => $appval->created_at,
                            'updated_at' => $appval->updated_at,
                            'client_id' => $request->merge_into,
                            'branch' => $appval->branch,
                            'intakedate' => $appval->intakedate,
                            'start_date' => $appval->start_date,
                            'end_date' => $appval->end_date,
                            'expect_win_date' => $appval->expect_win_date,
                            'super_agent' => $appval->super_agent,
                            'sub_agent' => $appval->sub_agent,
                            'ratio' => $appval->ratio,
                            'client_revenue' => $appval->client_revenue,
                            'partner_revenue' => $appval->partner_revenue,
                            'discounts' => $appval->discounts,
                            'progresswidth' => $appval->progresswidth
                        ]
                    );
                }
            }


            //interested_services
            $interested_services = DB::table('interested_services')->where('client_id', $request->merge_from)->get(); //dd($interested_services);
            if(!empty($interested_services)){
                foreach($interested_services as $intkey=>$intval){
                    DB::table('interested_services')->insert(
                        [
                            'user_id'=> $intval->user_id,
                            'client_id' => $request->merge_into,
                            'workflow' => $intval->workflow,
                            'partner' => $intval->partner,
                            'product' => $intval->product,
                            'branch' => $intval->branch,
                            'start_date' => $intval->start_date,
                            'exp_date' => $intval->exp_date,
                            'status' => $intval->status,
                            'created_at' => $intval->created_at,
                            'updated_at' => $intval->updated_at,
                            'client_revenue' => $intval->client_revenue,
                            'partner_revenue' => $intval->partner_revenue,
                            'discounts' => $intval->discounts
                        ]
                    );
                }
            }


            //education documents and migration documents
            $documents = DB::table('documents')->where('client_id', $request->merge_from)->get(); //dd($documents);
            if(!empty($documents)){
                foreach($documents as $dockey=>$docval){
                    DB::table('documents')->insert(
                        [
                            'document'=> $docval->document,
                            'filetype' => $docval->filetype,
                            'myfile' => $docval->myfile,
                            'user_id' => $docval->user_id,
                            'client_id' => $request->merge_into,
                            'file_size' => $docval->file_size,
                            'type' => $docval->type,
                            'doc_type' => $docval->doc_type,
                            'created_at' => $docval->created_at,
                            'updated_at' => $docval->updated_at
                        ]
                    );
                }
            }

            //appointments
            $appointments = DB::table('appointments')->where('client_id', $request->merge_from)->get(); //dd($appointments);
            if(!empty($appointments)){
                foreach($appointments as $appkey=>$appval){
                    DB::table('appointments')->insert(
                        [
                            'user_id'=> $appval->user_id,
                            'client_id' => $request->merge_into,
                            'service_id' => $appval->service_id,
                            'noe_id' => $appval->noe_id,
                            'full_name' => $appval->full_name,
                            'email' => $appval->email,
                            'phone' => $appval->phone,
                            'timezone' => $appval->timezone,
                            'date' => $appval->date,
                            'time' => $appval->time,
                            'timeslot_full' => $appval->timeslot_full,
                            'title' => $appval->title,
                            'description' => $appval->description,
                            'invites' => $appval->invites,
                            'appointment_details' => $appval->appointment_details,
                            'status' => $appval->status,
                            'assignee' => $appval->assignee,
                            'priority' => $appval->priority,
                            'priority_no' => $appval->priority_no,
                            'created_at' => $appval->created_at,
                            'updated_at' => $appval->updated_at,
                            'related_to' => $appval->related_to,
                            'order_hash' => $appval->order_hash
                        ]
                    );
                }
            }


            //quotations
            $quotations = DB::table('quotations')->where('client_id', $request->merge_from)->get(); //dd($quotations);
            if(!empty($quotations)){
                foreach($quotations as $quotekey=>$quoteval){
                    DB::table('quotations')->insert(
                        [
                            'client_id' => $request->merge_into,
                            'user_id'=> $quoteval->user_id,
                            'total_fee' => $quoteval->total_fee,
                            'status' => $quoteval->status,
                            'due_date' => $quoteval->due_date,
                            'created_by' => $quoteval->created_by,
                            'created_at' => $quoteval->created_at,
                            'updated_at' => $quoteval->updated_at,
                            'currency' => $quoteval->currency,
                            'is_archive' => $quoteval->is_archive
                        ]
                    );
                }
            }

            //accounts
            $accounts = DB::table('invoices')->where('client_id', $request->merge_from)->get(); //dd($accounts);
            if(!empty($accounts)){
                foreach($accounts as $acckey=>$accval){
                    DB::table('invoices')->insert(
                        [
                            'invoice_no'=> $accval->invoice_no,
                            'user_id' => $accval->user_id,
                            'client_id' => $request->merge_into,
                            'application_id' => $accval->application_id,
                            'type' => $accval->type,
                            'invoice_date' => $accval->invoice_date,
                            'due_date' => $accval->due_date,
                            'discount' => $accval->discount,
                            'discount_date' => $accval->discount_date,
                            'net_fee_rec' => $accval->net_fee_rec,
                            'notes' => $accval->notes,
                            'payment_option' => $accval->payment_option,
                            'attachments' => $accval->attachments,
                            'status' => $accval->status,
                            'currency' => $accval->currency,
                            'created_at' => $accval->created_at,
                            'updated_at' => $accval->updated_at,
                            'profile' => $accval->profile
                        ]
                    );
                }
            }

            //Conversations
            $conversations = DB::table('mail_reports')->where('client_id', $request->merge_from)->get(); //dd($conversations);
            if(!empty($conversations)){
                foreach($conversations as $mailkey=>$mailval){
                    DB::table('mail_reports')->insert(
                        [
                            'user_id' => $mailval->user_id,
                            'from_mail' => $mailval->from_mail,
                            'to_mail' => $mailval->to_mail,
                            'cc' => $mailval->cc,
                            'template_id' => $mailval->template_id,
                            'subject' => $mailval->subject,
                            'message' => $mailval->message,
                            'created_at' => $mailval->created_at,
                            'updated_at' => $mailval->updated_at,
                            'type' => $mailval->type,
                            'reciept_id' => $mailval->reciept_id,
                            'attachments' => $mailval->attachments,
                            'mail_type' => $mailval->mail_type,
                            'client_id' => $request->merge_into
                        ]
                    );
                }
            }

            //Tasks
            $tasks = DB::table('tasks')->where('client_id', $request->merge_from)->get(); //dd($tasks);
            if(!empty($tasks)){
                foreach($tasks as $taskkey=>$taskval){
                    DB::table('tasks')->insert(
                        [
                            'title' => $taskval->user_id,
                            'category' => $taskval->from_mail,
                            'assignee' => $taskval->to_mail,
                            'priority' => $taskval->cc,
                            'due_date' => $taskval->template_id,
                            'due_time' => $taskval->subject,
                            'description' => $taskval->message,
                            'related_to' => $taskval->created_at,
                            'contact_name' => $taskval->updated_at,
                            'partner_name' => $taskval->type,
                            'client_name' => $taskval->reciept_id,
                            'application' => $taskval->attachments,
                            'stage' => $taskval->mail_type,
                            'followers' => $taskval->mail_type,
                            'attachments' => $taskval->mail_type,
                            'created_at' => $taskval->mail_type,
                            'updated_at' => $taskval->mail_type,
                            'mailid' => $taskval->mail_type,
                            'user_id' => $taskval->mail_type,
                            'client_id' => $request->merge_into,
                            'status' => $taskval->mail_type,
                            'type' => $taskval->mail_type,
                            'priority_no' => $taskval->mail_type,
                            'is_archived' => $taskval->mail_type,
                            'group_id' => $taskval->mail_type
                        ]
                    );
                }
            }

            //Education
            $educations = DB::table('education')->where('client_id', $request->merge_from)->get(); //dd($educations);
            if(!empty($educations)){
                foreach($educations as $edukey=>$eduval){
                    DB::table('education')->insert(
                        [
                             'user_id' => $eduval->user_id,
                             'client_id' => $request->merge_into,
                             'degree_title' => $eduval->degree_title,
                             'degree_level' => $eduval->degree_level,
                             'institution' => $eduval->institution,
                             'course_start' => $eduval->course_start,
                             'course_end' => $eduval->course_end,
                             'subject_area' => $eduval->subject_area,
                             'subject' => $eduval->subject,
                             'ac_score' => $eduval->ac_score,
                             'score' => $eduval->score,
                             'created_at' => $eduval->created_at,
                             'updated_at' => $eduval->updated_at
                        ]
                    );
                }
            }

            //CheckinLogs
            $checkinLogs = DB::table('checkin_logs')->where('client_id', $request->merge_from)->get(); //dd($checkinLogs);
            if(!empty($checkinLogs)){
                foreach($checkinLogs as $checkkey=>$checkval){
                    DB::table('checkin_logs')->insert(
                        [
                             'client_id' => $request->merge_into,
                             'contact_type' => $checkval->contact_type,
                             'user_id' => $checkval->user_id,
                             'visit_purpose' => $checkval->visit_purpose,
                             'status' => $checkval->status,
                             'date' => $checkval->date,
                             'sesion_start' => $checkval->sesion_start,
                             'sesion_end' => $checkval->sesion_end,
                             'created_at' => $checkval->created_at,
                             'updated_at' => $checkval->updated_at,
                             'wait_time' => $checkval->wait_time,
                             'attend_time' => $checkval->attend_time,
                             'is_archived' => $checkval->is_archived,
                             'office' => $checkval->office,
                             'wait_type' => $checkval->wait_type
                        ]
                    );
                }
            }


            //Previous History
            $prevHis = DB::table('admins')->where('id', $request->merge_from)->select('id','prev_visa')->get(); //dd($prevHis);
            if(!empty($prevHis)){
               DB::table('admins')->where('id',$request->merge_into)->update( array('prev_visa'=>$prevHis[0]->prev_visa) );
            }

            //Client Info Form
            $clientInfo = DB::table('online_forms')->where('client_id', $request->merge_from)->get(); //dd($clientInfo);
            if(!empty($clientInfo)){
                foreach($clientInfo as $clientkey=>$clientval){
                    DB::table('online_forms')->insert(
                        [
                             'client_id' => $request->merge_into,
                             'type' => $clientval->type,
                             'info_name' => $clientval->info_name,
                             'main_lang' => $clientval->main_lang,
                             'marital_status' => $clientval->marital_status,
                             'mobile' => $clientval->mobile,
                             'curr_address' => $clientval->curr_address,
                             'email' => $clientval->email,
                             'parent_name' => $clientval->parent_name,
                             'parent_dob' => $clientval->parent_dob,
                             'parent_occ' => $clientval->parent_occ,
                             'parent_country' => $clientval->parent_country,
                             'parent_name_2' => $clientval->parent_name_2,
                             'parent_dob_2' => $clientval->parent_dob_2,
                             'parent_occ_2' => $clientval->parent_occ_2,
                             'parent_country_2' => $clientval->parent_country_2,
                             'sibling_name' => $clientval->sibling_name,
                             'sibling_dob' => $clientval->sibling_dob,
                             'sibling_occ' => $clientval->sibling_occ,
                             'sibling_gender' => $clientval->sibling_gender,
                             'sibling_country' => $clientval->sibling_country,
                             'sibling_marital' => $clientval->sibling_marital,
                             'sibling_name_2' => $clientval->sibling_name_2,
                             'sibling_dob_2' => $clientval->sibling_dob_2,
                             'sibling_occ_2' => $clientval->sibling_occ_2,
                             'sibling_gender_2' => $clientval->sibling_gender_2,
                             'sibling_country_2' => $clientval->sibling_country_2,
                             'sibling_marital_2' => $clientval->sibling_marital_2,
                             'held_visa' => $clientval->held_visa,
                             'visa_refused' => $clientval->visa_refused,
                             'traveled' => $clientval->traveled,
                             'created_at' => $clientval->created_at,
                             'updated_at' => $clientval->updated_at
                        ]
                    );
                }
            }
        }
        $response['status'] 	= 	true;
        $response['message']	=	'You have successfully merged records.';
        echo json_encode($response);
    }*/
  
    public function merge_records(Request $request){
        $response = array();
        if(
            ( isset($request->merge_from) && $request->merge_from != "" )
            && ( isset($request->merge_into) && $request->merge_into != "" )
        ){
            //Update merge_from to be deleted
            DB::table('admins')->where('id',$request->merge_from)->update( array('is_deleted'=>1) );

            //activities_logs
            $activitiesLogs = DB::table('activities_logs')->where('client_id', $request->merge_from)->get(); //dd($activitiesLogs);
            if(!empty($activitiesLogs)){
                foreach($activitiesLogs as $actkey=>$actval){
                    DB::table('activities_logs')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }

            //notes
            $notes = DB::table('notes')->where('client_id', $request->merge_from)->get(); //dd($notes);
            if(!empty($notes)){
                foreach($notes as $notekey=>$noteval){
                    DB::table('notes')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }


            //applications
            $applications = DB::table('applications')->where('client_id', $request->merge_from)->get(); //dd($applications);
            if(!empty($applications)){
                foreach($applications as $appkey=>$appval){
                    DB::table('applications')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }


            //interested_services
            $interested_services = DB::table('interested_services')->where('client_id', $request->merge_from)->get(); //dd($interested_services);
            if(!empty($interested_services)){
                foreach($interested_services as $intkey=>$intval){
                    DB::table('interested_services')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }


            //education documents and migration documents
            $documents = DB::table('documents')->where('client_id', $request->merge_from)->get(); //dd($documents);
            if(!empty($documents)){
                foreach($documents as $dockey=>$docval){
                    DB::table('documents')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }

            //appointments
            $appointments = DB::table('appointments')->where('client_id', $request->merge_from)->get(); //dd($appointments);
            if(!empty($appointments)){
                foreach($appointments as $appkey=>$appval){
                    DB::table('appointments')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }


            //quotations
            $quotations = DB::table('quotations')->where('client_id', $request->merge_from)->get(); //dd($quotations);
            if(!empty($quotations)){
                foreach($quotations as $quotekey=>$quoteval){
                    DB::table('quotations')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }

            //accounts
            $accounts = DB::table('invoices')->where('client_id', $request->merge_from)->get(); //dd($accounts);
            if(!empty($accounts)){
                foreach($accounts as $acckey=>$accval){
                    DB::table('invoices')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }

            //Conversations
            $conversations = DB::table('mail_reports')->where('client_id', $request->merge_from)->get(); //dd($conversations);
            if(!empty($conversations)){
                foreach($conversations as $mailkey=>$mailval){
                    DB::table('mail_reports')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }

            //Tasks
            $tasks = DB::table('tasks')->where('client_id', $request->merge_from)->get(); //dd($tasks);
            if(!empty($tasks)){
                foreach($tasks as $taskkey=>$taskval){
                    DB::table('tasks')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }

            //Education
            $educations = DB::table('education')->where('client_id', $request->merge_from)->get(); //dd($educations);
            if(!empty($educations)){
                foreach($educations as $edukey=>$eduval){
                    DB::table('education')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }

            //CheckinLogs
            $checkinLogs = DB::table('checkin_logs')->where('client_id', $request->merge_from)->get(); //dd($checkinLogs);
            if(!empty($checkinLogs)){
                foreach($checkinLogs as $checkkey=>$checkval){
                    DB::table('checkin_logs')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }


            //Previous History
            $prevHis = DB::table('admins')->where('id', $request->merge_from)->select('id','prev_visa')->get(); //dd($prevHis);
            if(!empty($prevHis)){
                $prevHis_exist = DB::table('admins')->where('id', $request->merge_into)->select('id','prev_visa')->first();
                if( empty($prevHis_exist) ){
                    DB::table('admins')->where('id',$request->merge_into)->update( array('prev_visa'=>$prevHis[0]->prev_visa) );
                }
            }

            //Client Info Form
            $clientInfo = DB::table('online_forms')->where('client_id', $request->merge_from)->get(); //dd($clientInfo);
            if(!empty($clientInfo)){
                foreach($clientInfo as $clientkey=>$clientval){
                    DB::table('online_forms')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }
        }
        $response['status'] 	= 	true;
        $response['message']	=	'You have successfully merged records from '.$request->merge_from.' to '.$request->merge_into.' .';
        echo json_encode($response);
    }
    
    //Update email to be verified wrt client id
    public function updateemailverified(Request $request)
    {
        $data = $request->all(); //dd($data);
        $recExist = Admin::where('id', $data['client_id'])
        ->update(['manual_email_phone_verified' => $data['manual_email_phone_verified']]);
         if($recExist){
             $response['status'] 	= 	true;
             $response['message']	=	'Record updated successfully';
         } else {
             $response['status'] 	= 	false;
             $response['message']	=	'Please try again';
         }
         echo json_encode($response);
    }
    
    
    //address_auto_populate
    public function address_auto_populate(Request $request){
        $address = $request->address;
        if( isset($address) && $address != ""){
            $result = app('geocoder')->geocode($address)->get(); //dd($result[0]);
            $postalCode = $result[0]->getPostalCode();
            $locality = $result[0]->getLocality();
            if( !empty($result) ){
                $response['status'] 	= 	1;
                $response['postal_code'] = 	$postalCode;
                $response['locality'] 	= 	$locality;
                $response['message']	=	"address is success.";
            } else {
                $response['status'] 	= 	0;
                $response['postal_code'] = 	"";
                $response['locality']    = 	"";
                $response['message']	=	"address is wrong.";
            }
            echo json_encode($response);
        }
    }
  
  
    //not picked call button click
    public function notpickedcall(Request $request){
        $data = $request->all(); //dd($data);
        $recExist = Admin::where('id', $data['id'])
        ->update(['not_picked_call' => $data['not_picked_call']]);
        if($recExist){
            if($data['not_picked_call'] == 1){ //if checked true
                $objs = new ActivitiesLog;
                $objs->client_id = $data['id'];
                $objs->created_by = Auth::user()->id;
                $objs->description = '<span class="text-semi-bold">Call not picked.Text sent</span>';
                //$objs->subject = "Call not picked";
                $objs->save();

                $response['status'] 	= 	true;
                $response['message']	=	'Call not picked.Text sent';
                $response['not_picked_call'] 	= 	$data['not_picked_call'];
            }
            else if($data['not_picked_call'] == 0){
                $response['status'] 	= 	true;
                $response['message']	=	'You have updated call not picked bit. Please try again';
                $response['not_picked_call'] 	= 	$data['not_picked_call'];
            }
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
            $response['not_picked_call'] 	= 	$data['not_picked_call'];
        }
        echo json_encode($response);
    }


    
     public function deleteactivitylog(Request $request){
		$activitylogid = $request->activitylogid; //dd($activitylogid);
		if(\App\ActivitiesLog::where('id',$activitylogid)->exists()){
			$data = \App\ActivitiesLog::select('client_id','subject','description')->where('id',$activitylogid)->first();
			$res = DB::table('activities_logs')->where('id', @$activitylogid)->delete();
			if($res){
				
			    $response['status'] 	= 	true;
			    $response['data']	=	$data;
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

    public function pinactivitylog(Request $request){
		$requestData = $request->all();
        if(\App\ActivitiesLog::where('id',$requestData['activity_id'])->exists()){
			$activity = \App\ActivitiesLog::where('id',$requestData['activity_id'])->first();
			if($activity->pin == 0){
				$obj = \App\ActivitiesLog::find($activity->id);
				$obj->pin = 1;
				$saved = $obj->save();
			}else{
				$obj = \App\ActivitiesLog::find($activity->id);
				$obj->pin = 0;
				$saved = $obj->save();
			}
			$response['status'] 	= 	true;
			$response['message']	=	'Pin Option added successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Record not found';
		}
		echo json_encode($response);
	}
  
  
    public function createservicetaken(Request $request){ //dd( $request->all() );
        $id = $request->logged_client_id;
        if( \App\Admin::where('id',$id)->exists() ) {
            $entity_type = $request->entity_type;
            if($entity_type == 'add') {
                $obj	= 	new clientServiceTaken;
                $obj->client_id = $id;
                $obj->service_type = $request->service_type;
                $obj->mig_ref_no = $request->mig_ref_no;
                $obj->mig_service = $request->mig_service;
                $obj->mig_notes = $request->mig_notes;
                $obj->edu_course = $request->edu_course;
                $obj->edu_college = $request->edu_college;
                $obj->edu_service_start_date = $request->edu_service_start_date;
                $obj->edu_notes = $request->edu_notes;
                $saved = $obj->save();
            }
            else if($entity_type == 'edit') {
                $saved = DB::table('client_service_takens')
                ->where('id', $request->entity_id)
                ->update([
                    'service_type' => $request->service_type,

                    'mig_ref_no' => $request->mig_ref_no,
                    'mig_service' => $request->mig_service,
                    'mig_notes' => $request->mig_notes,

                    'edu_course' => $request->edu_course,
                    'edu_college' => $request->edu_college,
                    'edu_service_start_date' => $request->edu_service_start_date,
                    'edu_notes' => $request->edu_notes
                ]);
            }
            if($saved){
               $response['status'] 	= 	true;
               $response['message']	=	'success';
               $user_rec = DB::table('client_service_takens')->where('client_id', $id)->orderBy('id', 'desc')->get();
               $response['user_rec'] = 	$user_rec;
            } else {
                $response['status'] 	= 	true;
                $response['message']	=	'success';
                $response['user_rec'] = 	array();
            }
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'fail';
            $response['result_str'] = 	array();
        }
        echo json_encode($response);
    }

    public function removeservicetaken(Request $request){ //dd( $request->all() );
        $sel_service_taken_id = $request->sel_service_taken_id;
		if( DB::table('client_service_takens')->where('id', $sel_service_taken_id)->exists() ){
			$res = DB::table('client_service_takens')->where('id', @$sel_service_taken_id)->delete();
			if($res){
				$response['status'] 	= 	true;
			    $response['record_id']	=	$sel_service_taken_id;
                $response['message']	=	'Service removed successfully';
			} else {
				$response['status'] 	= 	false;
			    $response['record_id']	=	$sel_service_taken_id;
                $response['message']	=	'Service not removed';
			}
		}else{
			$response['status'] 	= 	false;
            $response['record_id']	=	$sel_service_taken_id;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
    }

    public function getservicetaken(Request $request){ //dd( $request->all() );
        $sel_service_taken_id = $request->sel_service_taken_id;
        if( DB::table('client_service_takens')->where('id', $sel_service_taken_id)->exists() ){
			$res = DB::table('client_service_takens')->where('id', @$sel_service_taken_id)->first();//dd($res);
            if($res){
               $response['status'] 	= 	true;
               $response['message']	=	'success';
               $response['user_rec'] = 	$res;
            } else {
                $response['status'] 	= 	true;
                $response['message']	=   'success';
                $response['user_rec']   = 	array();
            }
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'fail';
            $response['user_rec'] = 	array();
        }
        echo json_encode($response);
    }
  
  
    public function gettagdata(Request $request){ //dd( $request->all() );
        $squery = $request->q;
        if($squery != ''){
            $tags_total = \App\Tag::select('id','name')->where('name', 'LIKE', '%'.$squery.'%')->count();
            $tags = \App\Tag::select('id','name')->where('name', 'LIKE', '%'.$squery.'%')->paginate(20);

            $items = array();
            //$total_count = count($tags);
            /*if(count($tags) >=20){
                $per_page = 20;
            } else {
                $per_page = count($tags);
            }*/
            $per_page = 20;
            foreach($tags as $tag){
                $items[] = array('id'=>$tag->id,'text' => $tag->name);
            }
            echo json_encode(array('items'=>$items,'per_page'=>$per_page,'total_count'=>$tags_total));
        }
    }
  
    
     //Save client account reports
    /*public function saveaccountreport(Request $request, $id = NULL)
	{
		$requestData = $request->all();
        //echo '<pre>'; print_r($requestData); die;
        if ($request->hasfile('document_upload'))
        {
            if(!is_array($request->file('document_upload'))){
                $files[] = $request->file('document_upload');
            }else{
                $files = $request->file('document_upload');
            }

            $client_info = \App\Admin::select('client_id')->where('id', $requestData['client_id'])->first(); //dd($admin);
            if(!empty($client_info)){
                $client_unique_id = $client_info->client_id;
            } else {
                $client_unique_id = "";
            }

            $doctype = isset($request->doctype)? $request->doctype : '';

            /*foreach ($files as $file) {
                $size = $file->getSize();
                $fileName = $file->getClientOriginalName();
                $explodeFileName = explode('.', $fileName);
                $name = time() . $file->getClientOriginalName();
                $filePath = $client_unique_id.'/'.$doctype.'/'. $name;
                Storage::disk('s3')->put($filePath, file_get_contents($file));
                $exploadename = explode('.', $name);

                $obj = new \App\Document;
                $obj->file_name = $explodeFileName[0];
                $obj->filetype = $exploadename[1];
                $obj->user_id = Auth::user()->id;
                $obj->myfile = $name;

                $obj->client_id = $requestData['client_id'];
                $obj->type = $request->type;
                $obj->file_size = $size;
                $obj->doc_type = $doctype;
                $doc_saved = $obj->save();

                $insertedDocId = $obj->id;
            } */ //end foreach

            /*foreach ($files as $file) {
                $size = $file->getSize();
                $fileName = $file->getClientOriginalName();
                $explodeFileName = explode('.', $fileName);
                $document_upload = $this->uploadrenameFile($file, Config::get('constants.client_receipts'));
                $exploadename = explode('.', $document_upload);

                $obj = new \App\Document;
                $obj->file_name = $explodeFileName[0];
                $obj->filetype = $exploadename[1];
                $obj->user_id = Auth::user()->id;
                $obj->myfile = $document_upload;
                $obj->client_id = $requestData['client_id'];
                $obj->type = $request->type;
                $obj->file_size = $size;
                $obj->doc_type = $doctype;
                $doc_saved = $obj->save();

                $insertedDocId = $obj->id;
            }

        } else {
            $insertedDocId = "";
            $doc_saved = "";
        }

        if(isset($requestData['trans_date'])){
            //Generate unique receipt id
            $is_record_exist = DB::table('account_client_receipts')->select('receipt_id')->where('receipt_type',1)->orderBy('receipt_id', 'desc')->first();
            //dd($is_record_exist);
            if(!$is_record_exist){
                $receipt_id = 1;
            } else {
                $receipt_id = $is_record_exist->receipt_id;
                $receipt_id = $receipt_id +1;
            }  //dd($receipt_id);
            $finalArr = array();
            for($i=0; $i<count($requestData['trans_date']); $i++){
                $finalArr[$i]['trans_date'] = $requestData['trans_date'][$i];
                $finalArr[$i]['entry_date'] = $requestData['entry_date'][$i];
                $finalArr[$i]['trans_no'] = $requestData['trans_no'][$i];
                $finalArr[$i]['payment_method'] = $requestData['payment_method'][$i];
                $finalArr[$i]['description'] = $requestData['description'][$i];
                $finalArr[$i]['deposit_amount'] = $requestData['deposit_amount'][$i];

                $saved	= DB::table('account_client_receipts')->insert([
                    'user_id' => $requestData['loggedin_userid'],
                    'client_id' =>  $requestData['client_id'],
                    //'agent_id' =>  $requestData['agent_id'],
                    'receipt_id'=>  $receipt_id,
                    'receipt_type' => $requestData['receipt_type'],
                    'trans_date' => $requestData['trans_date'][$i],
                    'entry_date' => $requestData['entry_date'][$i],
                    'trans_no' => $requestData['trans_no'][$i],
                    'payment_method' => $requestData['payment_method'][$i],
                    'description' => $requestData['description'][$i],
                    'deposit_amount' => $requestData['deposit_amount'][$i],
                    'uploaded_doc_id'=> $insertedDocId
                ]);
            }
        }
        //echo '<pre>'; print_r($finalArr); die;
        if($saved) {
            $response['status'] = true;
            $response['requestData'] = $finalArr;

            //Get total deposit amount
            $db_total_deposit_amount = DB::table('account_client_receipts')->where('client_id',$requestData['client_id'])->where('receipt_type',1)->sum('deposit_amount');
            $response['db_total_deposit_amount'] = $db_total_deposit_amount; //dd($db_total_deposit_amount );

            if($doc_saved){
                //Get AWS Url link
                //$url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                //$awsUrl = $url.$client_unique_id.'/'.$doctype.'/'.$name; //dd($awsUrl);
                //$awsUrl ="http://127.0.0.1:8000/".'img/client_receipts/'.$document_upload;
                $awsUrl = \URL::to('public/img/client_receipts').'/'.$document_upload;
                $response['awsUrl'] = $awsUrl;
                $response['message'] = 'Client receipt with document added successfully';

                $subject = 'added client receipt with Receipt No-'.$receipt_id.' and document' ;
            } else {
                $response['message'] = 'Client receipt added successfully';
                $response['awsUrl'] =  "";
                $subject = 'added client receipt with Receipt No-'.$receipt_id;
            }

            $printUrl = \URL::to('/admin/clients/printpreview').'/'.$receipt_id;
            $response['printUrl'] = $printUrl;

            if($request->type == 'client'){
                //$subject = 'added client receipt with document';
                $objs = new ActivitiesLog;
                $objs->client_id = $requestData['client_id'];
                $objs->created_by = Auth::user()->id;
                $objs->description = '';
                $objs->subject = $subject;
                $objs->save();
            }

        } else {
            $response['awsUrl'] =  "";
            $response['requestData'] = "";
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
        }
        echo json_encode($response);
    }*/
  
  
  
     //Save client account reports
    public function saveaccountreport(Request $request, $id = NULL)
	{
		$requestData = $request->all();
        //echo '<pre>'; print_r($requestData); die;
        if( $requestData['function_type'] == 'add')
        {
            if ($request->hasfile('document_upload'))
            {
                if(!is_array($request->file('document_upload'))){
                    $files[] = $request->file('document_upload');
                }else{
                    $files = $request->file('document_upload');
                }

                $client_info = \App\Admin::select('client_id')->where('id', $requestData['client_id'])->first(); //dd($admin);
                if(!empty($client_info)){
                    $client_unique_id = $client_info->client_id;
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

                    $obj = new \App\Document;
                    $obj->file_name = $explodeFileName[0];
                    $obj->filetype = $exploadename[1];
                    $obj->user_id = Auth::user()->id;
                    $obj->myfile = $name;

                    $obj->client_id = $requestData['client_id'];
                    $obj->type = $request->type;
                    $obj->file_size = $size;
                    $obj->doc_type = $doctype;
                    $doc_saved = $obj->save();

                    $insertedDocId = $obj->id;
                } //end foreach

                /*foreach ($files as $file) {
                    $size = $file->getSize();
                    $fileName = $file->getClientOriginalName();
                    $explodeFileName = explode('.', $fileName);
                    $document_upload = $this->uploadrenameFile($file, Config::get('constants.client_receipts'));
                    $exploadename = explode('.', $document_upload);

                    $obj = new \App\Document;
                    $obj->file_name = $explodeFileName[0];
                    $obj->filetype = $exploadename[1];
                    $obj->user_id = Auth::user()->id;
                    $obj->myfile = $document_upload;
                    $obj->client_id = $requestData['client_id'];
                    $obj->type = $request->type;
                    $obj->file_size = $size;
                    $obj->doc_type = $doctype;
                    $doc_saved = $obj->save();

                    $insertedDocId = $obj->id;
                }*/

            } else {
                $insertedDocId = "";
                $doc_saved = "";
            }

            if(isset($requestData['trans_date'])){
                //Generate unique receipt id
                $is_record_exist = DB::table('account_client_receipts')->select('receipt_id')->where('receipt_type',1)->orderBy('receipt_id', 'desc')->first();
                //dd($is_record_exist);
                if(!$is_record_exist){
                    $receipt_id = 1;
                } else {
                    $receipt_id = $is_record_exist->receipt_id;
                    $receipt_id = $receipt_id +1;
                }  //dd($receipt_id);
                $finalArr = array();
                for($i=0; $i<count($requestData['trans_date']); $i++){
                    $finalArr[$i]['trans_date'] = $requestData['trans_date'][$i];
                    $finalArr[$i]['entry_date'] = $requestData['entry_date'][$i];
                    $finalArr[$i]['trans_no'] = $requestData['trans_no'][$i];
                    $finalArr[$i]['payment_method'] = $requestData['payment_method'][$i];
                    $finalArr[$i]['description'] = $requestData['description'][$i];
                    $finalArr[$i]['deposit_amount'] = $requestData['deposit_amount'][$i];

                    $saved	= DB::table('account_client_receipts')->insertGetId([
                        'user_id' => $requestData['loggedin_userid'],
                        'client_id' =>  $requestData['client_id'],
                        'receipt_id'=>  $receipt_id,
                        'receipt_type' => $requestData['receipt_type'],
                        'trans_date' => $requestData['trans_date'][$i],
                        'entry_date' => $requestData['entry_date'][$i],
                        'trans_no' => $requestData['trans_no'][$i],
                        'payment_method' => $requestData['payment_method'][$i],
                        'description' => $requestData['description'][$i],
                        'deposit_amount' => $requestData['deposit_amount'][$i],
                        'uploaded_doc_id'=> $insertedDocId
                    ]);
                }
            }
            //echo '<pre>'; print_r($finalArr); die;
            if($saved) {
                $response['status'] = true;
                $response['requestData'] = $finalArr;
                $response['lastInsertedId'] = $saved;
                $response['function_type'] = $requestData['function_type'];

                //Get total deposit amount
                $db_total_deposit_amount = DB::table('account_client_receipts')->where('client_id',$requestData['client_id'])->where('receipt_type',1)->sum('deposit_amount');
                $response['db_total_deposit_amount'] = $db_total_deposit_amount; //dd($db_total_deposit_amount );

                if($doc_saved){
                    //Get AWS Url link
                    $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                    $awsUrl = $url.$client_unique_id.'/'.$doctype.'/'.$name; //dd($awsUrl);
                    
                    //$awsUrl = \URL::to('/img/client_receipts').'/'.$document_upload;
                    $response['awsUrl'] = $awsUrl;
                    $response['message'] = 'Client receipt with document added successfully';

                    $subject = 'added client receipt with Receipt No-'.$receipt_id.' and document' ;
                } else {
                    $response['message'] = 'Client receipt added successfully';
                    $response['awsUrl'] =  "";
                    $subject = 'added client receipt with Receipt No-'.$receipt_id;
                }

                $printUrl = \URL::to('/admin/clients/printpreview').'/'.$receipt_id;
                $response['printUrl'] = $printUrl;

                if($request->type == 'client'){
                    $objs = new ActivitiesLog;
                    $objs->client_id = $requestData['client_id'];
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->save();
                }
            } else {
                $response['lastInsertedId'] = "";
                $response['awsUrl'] =  "";
                $response['requestData'] = "";
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['function_type'] = $requestData['function_type'];
            }
        }
        else if( $requestData['function_type'] == 'edit'){
            $finalArr = array();
            for($j=0; $j<count($requestData['trans_date']); $j++){
                $finalArr[$j]['trans_date'] = $requestData['trans_date'][$j];
                $finalArr[$j]['entry_date'] = $requestData['entry_date'][$j];
                $finalArr[$j]['payment_method'] = $requestData['payment_method'][$j];
                $finalArr[$j]['description'] = $requestData['description'][$j];
                $finalArr[$j]['deposit_amount'] = $requestData['deposit_amount'][$j];
                $finalArr[$j]['trans_no'] = $requestData['trans_no'][$j];

                $finalArr[$j]['id'] = $requestData['id'][$j];

                $savedDB = DB::table('account_client_receipts')
                    ->where('id',$requestData['id'][$j])
                    ->update([
                        'user_id' => $requestData['loggedin_userid'],
                        'client_id' =>  $requestData['client_id'],
                        'trans_date' => $requestData['trans_date'][$j],
                        'entry_date' => $requestData['entry_date'][$j],
                        'payment_method' => $requestData['payment_method'][$j],
                        //'trans_no' => $requestData['trans_no'][$j],
                        'description' => $requestData['description'][$j],
                        'deposit_amount' => $requestData['deposit_amount'][$j]
                    ]);
            }
            if($savedDB >=0) {
                $response['function_type'] = $requestData['function_type'];
                $response['requestData'] 	= $finalArr;
                $db_total_deposit_amount = DB::table('account_client_receipts')->where('client_id',$requestData['client_id'])->where('receipt_type',1)->sum('deposit_amount');
                $response['db_total_deposit_amount'] = $db_total_deposit_amount;
                $response['status'] 	= 	true;

                $response['message'] = 'Client receipt updated successfully';
                $subject = 'updated client receipt with Receipt No-'.$requestData['id'][0];
                $response['lastInsertedId'] = $requestData['id'][0];

                if($request->type == 'client'){
                    $objs = new ActivitiesLog;
                    $objs->client_id = $requestData['client_id'];
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->save();
                }
            } else {
                $response['requestData'] = "";
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['function_type'] = $requestData['function_type'];
                $response['lastInsertedId'] = "";
            }
        }
        echo json_encode($response);
    }



    public function getTopReceiptValInDB(Request $request)
	{
        $requestData = 	$request->all();
        $receipt_type = $requestData['type'];
        $record_count = DB::table('account_client_receipts')->where('receipt_type',$receipt_type)->max('id');
        //dd($record_count);
        if($record_count) {
            if($receipt_type == 3){ //type = invoice
                $max_receipt_id = DB::table('account_client_receipts')->where('receipt_type',$receipt_type)->max('receipt_id');
                $response['max_receipt_id'] 	= $max_receipt_id;
            } else {
                $response['max_receipt_id'] 	= "";
            }
            $response['receipt_type'] 	= $receipt_type;
            $response['record_count'] 	= $record_count;
            $response['status'] 	= 	true;
            $response['message']	=	'Record is exist';
        }else{
            $response['receipt_type'] 	= $receipt_type;
            $response['record_count'] 	= $record_count;
            $response['max_receipt_id'] 	= "";
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
        }
        echo json_encode($response);
    }


    /*public function clientreceiptlist(Request $request)
	{
		$query 	= AccountClientReceipt::select('id','receipt_id','client_id','user_id','trans_date','entry_date','trans_no', 'payment_method','validate_receipt','voided_or_validated_by', DB::raw('sum(deposit_amount) as total_deposit_amount'))->where('receipt_type',1)->groupBy('receipt_id');
        $totalData 	= $query->count(); 
        $lists = $query->sortable(['id' => 'desc'])->paginate(20);
		return view('Admin.clients.clientreceiptlist', compact(['lists', 'totalData']));
    }*/
  
    public function clientreceiptlist(Request $request)
	{

        $query = DB::table('account_client_receipts as acr')
        ->join('admins as ad', 'acr.client_id', '=', 'ad.id')
        ->select('acr.id','acr.receipt_id',
        'acr.client_id','acr.user_id','acr.trans_date','acr.entry_date','acr.trans_no', 'acr.payment_method',
        'acr.validate_receipt','acr.voided_or_validated_by','acr.deposit_amount as total_deposit_amount',
        'ad.first_name','ad.last_name','ad.client_id as client_decode_id')
        ->where('acr.receipt_type',1);

        if ($request->has('client_id')) {
			$client_id 	= $request->input('client_id');
			if(trim($client_id) != ''){
				$query->where('ad.client_id', '=', $client_id);
			}
		}

		if ($request->has('name')) {
			$name =  $request->input('name');
			if(trim($name) != '') {
				$query->where('ad.first_name', 'LIKE', '%'.$name.'%');
			}
		}
        $query->orderBy('acr.id', 'desc');
        $totalData 	= $query->count();
        $lists = $query->paginate(20);
		return view('Admin.clients.clientreceiptlist', compact(['lists', 'totalData']));
    }

    public function validate_receipt(Request $request){
        $response = array(); //dd($request->all());
        if( isset($request->clickedReceiptIds) && !empty($request->clickedReceiptIds) ){
            //Update all selected receipt bit to be 1
            $affectedRows = DB::table('account_client_receipts')
            ->where('receipt_type', $request->receipt_type)
            ->whereIn('receipt_id', $request->clickedReceiptIds)
            ->update(['validate_receipt' => 1,'voided_or_validated_by' => Auth::user()->id]);
            if ($affectedRows > 0) {

                foreach($request->clickedReceiptIds as $ReceiptVal){
                    $receipt_info = AccountClientReceipt::select('user_id','client_id')->where('receipt_id', $ReceiptVal)->first();
                    $client_info = \App\Admin::select('client_id')->where('id', $receipt_info->client_id)->first();

                    if($request->receipt_type == 1){
                        $subject = 'validated client receipt no -'.$ReceiptVal.' of client-'.$client_info->client_id;
                    } 
                    $objs = new ActivitiesLog;
                    $objs->client_id = $receipt_info->client_id;
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->save();
                }

                //Get record validate_receipt =1
                $record_data = DB::table('account_client_receipts')
                ->leftJoin('admins', 'admins.id', '=', 'account_client_receipts.voided_or_validated_by')
                ->select('account_client_receipts.id','account_client_receipts.voided_or_validated_by','admins.first_name','admins.last_name')
                ->where('account_client_receipts.receipt_type', $request->receipt_type)
                ->whereIn('account_client_receipts.receipt_id', $request->clickedReceiptIds)
                ->where('account_client_receipts.validate_receipt', 1)
                ->get();
                $response['record_data'] = 	$record_data;
                $response['status'] 	= 	true;
                $response['message']	=	'Record updated successfully.';
            } else {
                $response['status'] 	= 	true;
                $response['message']	=	'No record was updated.';
                $response['clickedIds'] = 	array();
            }
        }
        echo json_encode($response);
    }
  
   public function printpreview(Request $request, $id){
        //phpinfo();
        $record_get = DB::table('account_client_receipts')->where('receipt_type',1)->where('id',$id)->get(); //dd($record_get);
        if(!empty($record_get)){
            $clientname = DB::table('admins')->select('first_name','last_name','address','state','city','zip','country','dob')->where('id',$record_get[0]->client_id)->first();
            $admin = DB::table('admins')->select('company_name','address','state','city','zip','email','phone','profile_img','dob')->where('id',$record_get[0]->user_id)->first();
        }
        //dd($admin);
        set_time_limit(3000);
        $pdf = PDF::setOptions([
			'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
			'logOutputFile' => storage_path('logs/log.htm'),
			'tempDir' => storage_path('logs/')
		])->loadView('emails.printpreview',compact(['record_get','clientname','admin']));
		return $pdf->stream('ClientReceipt.pdf');
	}
  
  
  public function getClientReceiptInfoById(Request $request)
	{
        $requestData = 	$request->all();
        $id = $requestData['id'];
        $record_get = DB::table('account_client_receipts')->where('receipt_type',1)->where('id',$id)->get();
        //dd($record_get);
        if(!empty($record_get)) {
            $response['record_get'] = $record_get;
            $response['status'] 	= 	true;
            $response['message']	=	'Record is exist';
            $last_record_id = DB::table('account_client_receipts')->where('receipt_type',1)->max('id');
            //dd($last_record_id);
            $response['last_record_id'] = $last_record_id;
        }else{
            $response['record_get'] = array();
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
            $response['last_record_id'] = 0;
        }
        echo json_encode($response);
    }

    //Commission Report
    public function commissionreport() {
        return view('Admin.clients.commissionreport');
    }

    public function getcommissionreport(Request $request) {
        if ($request->ajax()) {
			$data = \App\Application::join('admins', 'applications.client_id', '=', 'admins.id')
            ->leftJoin('partners', 'applications.partner_id', '=', 'partners.id')
            ->leftJoin('products', 'applications.product_id', '=', 'products.id')
            ->leftJoin('application_fee_options', 'applications.id', '=', 'application_fee_options.app_id')
            ->select('applications.*','admins.client_id as client_reference', 'admins.first_name','admins.last_name','admins.dob','partners.partner_name','products.name as coursename','application_fee_options.total_course_fee_amount','application_fee_options.enrolment_fee_amount','application_fee_options.material_fees','application_fee_options.tution_fees','application_fee_options.total_anticipated_fee','application_fee_options.fee_reported_by_college','application_fee_options.bonus_amount','application_fee_options.bonus_paid','application_fee_options.commission_as_per_anticipated_fee','application_fee_options.commission_as_per_fee_reported','application_fee_options.commission_payable_as_per_anticipated_fee','application_fee_options.commission_paid_as_per_fee_reported','application_fee_options.commission_pending')
            ->where('applications.stage','Coe issued')
            ->orWhere('applications.stage','Enrolled')
            ->orWhere('applications.stage','Coe Cancelled')
            ->latest()->get();  //dd($data);
            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('client_reference', function($data) {
                if($data->client_reference){
                    $client_encoded_id = base64_encode(convert_uuencode(@$data->client_id)) ;
                    $client_reference = '<a href="'.url('/admin/clients/detail/'.$client_encoded_id).'" target="_blank" >'.$data->client_reference.'</a>';
                } else {
                    $client_reference = 'N/P';
                }
                return $client_reference;
            })
            ->addColumn('student_name', function($data) {
                if($data->first_name != ""){
                    $full_name = $data->first_name.' '.$data->last_name;
                } else {
                    $full_name = 'N/P';
                }
                return $full_name;
            })
            ->addColumn('dob', function($data) {
                if($data->dob != ""){ //1992-02-19 Y-m-d
                    $dobArr = explode("-",$data->dob);
                    $dob = $dobArr[2]."/".$dobArr[1]."/".$dobArr[0];
                } else {
                    $dob = 'N/P';
                }
                return $dob;
            })
            ->addColumn('student_id', function($data) {
                if($data->student_id != ""){
                    $student_id = $data->student_id;
                } else {
                    $student_id = 'N/P';
                }
                return $student_id;
            })
            ->addColumn('college_name', function($data) {
                if($data->partner_name != ""){
                    $partner_name = $data->partner_name;
                } else {
                    $partner_name = 'N/P';
                }
                return $partner_name;
            })
            ->addColumn('course_name', function($data) {
                if($data->coursename != ""){
                    $coursename = $data->coursename;
                } else {
                    $coursename = 'N/P';
                }
                return $coursename;
            })
            ->addColumn('start_date', function($data) {
                if($data->start_date != ""){
                    $start_date = date('d/m/Y',strtotime($data->start_date));
                } else {
                    $start_date = 'N/P';
                }
                return $start_date;
            })
            ->addColumn('end_date', function($data) {
                if($data->end_date != ""){
                    $end_date = date('d/m/Y',strtotime($data->end_date));
                } else {
                    $end_date = 'N/P';
                }
                return $end_date;
            })

            ->addColumn('total_course_fee_amount', function($data) {
                if($data->total_course_fee_amount != ""){
                    $total_course_fee_amount = $data->total_course_fee_amount;
                } else {
                    $total_course_fee_amount = 'N/P';
                }
                return $total_course_fee_amount;
            })
            ->addColumn('enrolment_fee_amount', function($data) {
                if($data->enrolment_fee_amount != ""){
                    $enrolment_fee_amount = $data->enrolment_fee_amount;
                } else {
                    $enrolment_fee_amount = 'N/P';
                }
                return $enrolment_fee_amount;
            })
            ->addColumn('material_fees', function($data) {
                if($data->material_fees != ""){
                    $material_fees = $data->material_fees;
                } else {
                    $material_fees = 'N/P';
                }
                return $material_fees;
            })
            ->addColumn('tution_fees', function($data) {
                if($data->tution_fees != ""){
                    $tution_fees = $data->tution_fees;
                } else {
                    $tution_fees = 'N/P';
                }
                return $tution_fees;
            })
            ->addColumn('total_anticipated_fee', function($data) {
                if($data->total_anticipated_fee != ""){
                    $total_anticipated_fee = $data->total_anticipated_fee;
                } else {
                    $total_anticipated_fee = 'N/P';
                }
                return $total_anticipated_fee;
            })
            ->addColumn('fee_reported_by_college', function($data) {
                if($data->fee_reported_by_college != ""){
                    $fee_reported_by_college = $data->fee_reported_by_college;
                } else {
                    $fee_reported_by_college = 'N/P';
                }
                return $fee_reported_by_college;
            })
            ->addColumn('bonus_amount', function($data) {
                if($data->bonus_amount != ""){
                    $bonus_amount = $data->bonus_amount;
                } else {
                    $bonus_amount = 'N/P';
                }
                return $bonus_amount;
            })
            ->addColumn('bonus_paid', function($data) {
                if($data->bonus_paid != ""){
                    $bonus_paid = $data->bonus_paid;
                } else {
                    $bonus_paid = 'N/P';
                }
                return $bonus_paid;
            })
            ->addColumn('commission_as_per_anticipated_fee', function($data) {
                if($data->commission_as_per_anticipated_fee != ""){
                    $commission_as_per_anticipated_fee = $data->commission_as_per_anticipated_fee;
                } else {
                    $commission_as_per_anticipated_fee = 'N/P';
                }
                return $commission_as_per_anticipated_fee;
            })
            ->addColumn('commission_as_per_fee_reported', function($data) {
                if($data->commission_as_per_fee_reported != ""){
                    $commission_as_per_fee_reported = $data->commission_as_per_fee_reported;
                } else {
                    $commission_as_per_fee_reported = 'N/P';
                }
                return $commission_as_per_fee_reported;
            })
            ->addColumn('commission_payable_as_per_anticipated_fee', function($data) {
                if($data->commission_payable_as_per_anticipated_fee != ""){
                    $commission_payable_as_per_anticipated_fee = $data->commission_payable_as_per_anticipated_fee;
                } else {
                    $commission_payable_as_per_anticipated_fee = 'N/P';
                }
                return $commission_payable_as_per_anticipated_fee;
            })
            ->addColumn('commission_paid_as_per_fee_reported', function($data) {
                if($data->commission_paid_as_per_fee_reported != ""){
                    $commission_paid_as_per_fee_reported = $data->commission_paid_as_per_fee_reported;
                } else {
                    $commission_paid_as_per_fee_reported = 'N/P';
                }
                return $commission_paid_as_per_fee_reported;
            })
            ->addColumn('commission_pending', function($data) {
                if($data->commission_pending != ""){
                    $commission_pending = $data->commission_pending;
                } else {
                    $commission_pending = 'N/P';
                }
                return $commission_pending;
            })
            ->addColumn('student_status', function($data) {
                if($data->status == 0){
                    $student_status = "In Progress";
                } else if($data->status == 1){
                    $student_status = "Completed";
                } else if($data->status == 2){
                    $student_status = "Discontinued";
                }
                return $student_status;
            })
            ->rawColumns(['client_reference'])
            ->make(true);
        }
    }
  
    //Add All Doc checklist
    public function addalldocchecklist(Request $request){ //dd($request->all());
        $clientid = $request->clientid;
        $admin_info1 = \App\Admin::select('client_id')->where('id', $clientid)->first(); //dd($admin);
        if(!empty($admin_info1)){
            $client_unique_id = $admin_info1->client_id;
        } else {
            $client_unique_id = "";
        }  //dd($client_unique_id);
        $doctype = isset($request->doctype)? $request->doctype : '';

        if ($request->has('checklist'))
        {
            $checklistArray = $request->input('checklist'); //dd($checklistArray);
            if (is_array($checklistArray))
            {
                foreach ($checklistArray as $item)
                {
                    $obj = new \App\Document;
                    $obj->user_id = Auth::user()->id;
                    $obj->client_id = $clientid;
                    $obj->type = $request->type;
                    $obj->doc_type = $doctype;
                    $obj->checklist = $item;
                    $saved = $obj->save();
                } //end foreach

                if($saved)
                {
                    if($request->type == 'client'){
                        $subject = 'added document checklist';
                        $objs = new ActivitiesLog;
                        $objs->client_id = $clientid;
                        $objs->created_by = Auth::user()->id;
                        $objs->description = '';
                        $objs->subject = $subject;
                        $objs->save();
                    }

                    $response['status'] 	= 	true;
                    $response['message']	=	'You have successfully added your document checklist';

                    $fetchd = \App\Document::where('client_id',$clientid)->whereNull('not_used_doc')->where('doc_type',$doctype)->where('type',$request->type)->orderby('updated_at', 'DESC')->get();
                    ob_start();
                    foreach($fetchd as $docKey=>$fetch)
                    {
                        $admin = \App\Admin::where('id', $fetch->user_id)->first();
                        //Checklist verified by
                        if( isset($fetch->checklist_verified_by) && $fetch->checklist_verified_by != "") {
                            $checklist_verified_Info = \App\Admin::select('first_name')->where('id', $fetch->checklist_verified_by)->first();
                            $checklist_verified_by = $checklist_verified_Info->first_name;
                        } else {
                            $checklist_verified_by = 'N/A';
                        }

                        if( isset($fetch->checklist_verified_at) && $fetch->checklist_verified_at != "") {
                            $checklist_verified_at = date('d/m/Y', strtotime($fetch->checklist_verified_at));
                        } else {
                            $checklist_verified_at = 'N/A';
                        }
                        ?>
                        <tr class="drow" id="id_<?php echo $fetch->id; ?>">
                            <td><?php echo $docKey+1;?></td>
                            <td>
                                <div data-id="<?php echo $fetch->id;?>" data-personalchecklistname="<?php echo $fetch->checklist; ?>" class="personalchecklist-row">
                                    <span><?php echo $fetch->checklist; ?></span>
                                </div>
                            </td>
                            <td>
                                <?php
                                echo $admin->first_name. "<br>";
                                echo date('d/m/Y', strtotime($fetch->created_at));
                                ?>
                            </td>
                            <td>
                                <?php
                                if( isset($fetch->file_name) && $fetch->file_name !=""){ ?>
                                    <div data-id="<?php echo $fetch->id; ?>" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
                                        <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name; ?><?php echo '.'.$fetch->filetype; ?></span>
                                    </div>
                                <?php
                                }
                                else
                                {?>
                                    <div class="allupload_document" style="display:inline-block;">
                                        <form method="POST" enctype="multipart/form-data" id="upload_form_<?php echo $fetch->id;?>">
                                            <input type="hidden" name="_token" value="<?php echo csrf_token();?>" />
                                            <input type="hidden" name="clientid" value="<?php echo $clientid;?>">
                                            <input type="hidden" name="fileid" value="<?php echo $fetch->id;?>">
                                            <input type="hidden" name="type" value="client">
                                            <input type="hidden" name="doctype" value="documents">
                                            <a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>
                                            <input class="alldocupload" data-fileid="<?php echo $fetch->id;?>" type="file" name="document_upload"/>
                                        </form>
                                    </div>
                                <?php
                                }?>
                            </td>
                            <td id="docverifiedby_<?php echo $fetch->id;?>">
                                <?php
                                echo $checklist_verified_by. "<br>";
                                echo $checklist_verified_at;
                                ?>
                            </td>

                            <td>
                                <?php
                                if( isset($fetch->file_name) && $fetch->file_name !="")
                                { ?>
                                    <div class="dropdown d-inline">
                                        <button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item renamechecklist" href="javascript:;">Rename Checklist</a>
                                            <a class="dropdown-item renamealldoc" href="javascript:;">Rename File Name</a>
                                            <?php
                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                            ?>
                                            <a target="_blank" class="dropdown-item" href="<?php echo $url.$client_unique_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Preview</a>
                                            <?php
                                            $explodeimg = explode('.',$fetch->myfile);
                                            if($explodeimg[1] == 'jpg'|| $explodeimg[1] == 'png'|| $explodeimg[1] == 'jpeg'){
                                            ?>
                                                <a target="_blank" class="dropdown-item" href="<?php echo \URL::to('/admin/document/download/pdf'); ?>/<?php echo $fetch->id; ?>">PDF</a>
                                            <?php } ?>
                                            <a download class="dropdown-item" href="<?php echo $url.$client_unique_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Download</a>
                                            <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item deletenote" data-href="deletealldocs" href="javascript:;" >Delete</a>
                                            <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item verifydoc" data-doctype="documents" data-href="verifydoc" href="javascript:;">Verify</a>
                                            <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item notuseddoc" data-doctype="documents" data-href="notuseddoc" href="javascript:;">Not Used</a>
                                        </div>
                                    </div>
                                <?php
                                }?>
                            </td>
                        </tr>
			        <?php
			        } //end foreach

                    $data = ob_get_clean();
                    ob_start();
                    foreach($fetchd as $fetch)
                    {
                        $admin = \App\Admin::where('id', $fetch->user_id)->first();
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
                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                            ?>
                                            <?php if( isset($fetch->myfile) && $fetch->myfile != ""){?>
                                            <a class="dropdown-item" href="<?php echo $url.$client_unique_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Preview</a>
                                            <a download class="dropdown-item" href="<?php echo $url.$client_unique_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Download</a>
                                            <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item deletenote" data-href="deletealldocs" href="javascript:;" >Delete</a>
                                            <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item verifydoc" data-doctype="documents" data-href="verifydoc" href="javascript:;">Verify</a>
                                            <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item notuseddoc" data-doctype="documents" data-href="notuseddoc" href="javascript:;">Not Used</a>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    } //end foreach
                    $griddata = ob_get_clean();
                    $response['data']	= $data;
                    $response['griddata'] = $griddata;
                } //end if
                else
                {
                    $response['status'] = false;
                    $response['message'] = 'Please try again';
                } //end else
            } //end if
            else
            {
                $response['status'] = false;
                $response['message'] = 'Please try again';
            } //end else
        }
        else
        {
            $response['status'] = false;
            $response['message'] = 'Please try again';
        } //end else
        echo json_encode($response);
	}

    //Update all Document upload
	public function uploadalldocument(Request $request){ //dd($request->all());
        if ($request->hasfile('document_upload'))
        {
            $clientid = $request->clientid;
            $admin_info1 = \App\Admin::select('client_id')->where('id', $clientid)->first(); //dd($admin);
            if(!empty($admin_info1)){
                $client_unique_id = $admin_info1->client_id;
            } else {
                $client_unique_id = "";
            }  //dd($client_unique_id);

            $doctype = isset($request->doctype)? $request->doctype : '';
            $files = $request->file('document_upload');
            $size = $files->getSize();
            $fileName = $files->getClientOriginalName();
            $explodeFileName = explode('.', $fileName);
            $name = time() . $files->getClientOriginalName();
            $filePath = $client_unique_id.'/'.$doctype.'/'. $name;
            Storage::disk('s3')->put($filePath, file_get_contents($files));
            $exploadename = explode('.', $name);

            $req_file_id = $request->fileid;
            $obj = \App\Document::find($req_file_id);
            $obj->file_name = $explodeFileName[0];
            $obj->filetype = $exploadename[1];
            $obj->user_id = Auth::user()->id;
            $obj->myfile = $name;
            $obj->type = $request->type;
            $obj->file_size = $size;
            $obj->doc_type = $doctype;
            $saved = $obj->save();

			if($saved){
				if($request->type == 'client'){
                    $subject = 'uploaded document';
                    $objs = new ActivitiesLog;
                    $objs->client_id = $clientid;
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->save();
                }
				$response['status'] 	= 	true;
				$response['message']	=	'You have successfully uploaded your document';
				$fetchd = \App\Document::where('client_id',$clientid)->whereNull('not_used_doc')->where('doc_type',$doctype)->where('type',$request->type)->orderby('updated_at', 'DESC')->get();
				ob_start();
				foreach($fetchd as  $docKey=>$fetch){
					$admin = \App\Admin::where('id', $fetch->user_id)->first();
                    //Checklist verified by
                    if( isset($fetch->checklist_verified_by) && $fetch->checklist_verified_by != "") {
                        $checklist_verified_Info = \App\Admin::select('first_name')->where('id', $fetch->checklist_verified_by)->first();
                        $checklist_verified_by = $checklist_verified_Info->first_name;
                    } else {
                        $checklist_verified_by = 'N/A';
                    }

                    if( isset($fetch->checklist_verified_at) && $fetch->checklist_verified_at != "") {
                        $checklist_verified_at = date('d/m/Y', strtotime($fetch->checklist_verified_at));
                    } else {
                        $checklist_verified_at = 'N/A';
                    }
					?>
					<tr class="drow" id="id_<?php echo $fetch->id; ?>">
                        <td><?php echo $docKey+1;?></td>
                        <td>
                            <div data-id="<?php echo $fetch->id;?>" data-personalchecklistname="<?php echo $fetch->checklist; ?>" class="personalchecklist-row">
                                <span><?php echo $fetch->checklist; ?></span>
                            </div>
                        </td>
                        <td>
                            <?php
                            echo $admin->first_name. "<br>";
                            echo date('d/m/Y', strtotime($fetch->created_at));
                            ?>
                        </td>
                        <td>
                            <?php
                            if( isset($fetch->file_name) && $fetch->file_name !=""){ ?>
                                <div data-id="<?php echo $fetch->id; ?>" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
                                    <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name; ?><?php echo '.'.$fetch->filetype; ?></span>
                                </div>
                            <?php
                            }
                            else
                            {?>
                                <div class="allupload_document" style="display:inline-block;">
                                    <form method="POST" enctype="multipart/form-data" id="upload_form_<?php echo $fetch->id;?>">
                                        <input type="hidden" name="_token" value="<?php echo csrf_token();?>" />
                                        <input type="hidden" name="clientid" value="<?php echo $fetch->client_id;?>">
                                        <input type="hidden" name="fileid" value="<?php echo $fetch->id;?>">
                                        <input type="hidden" name="type" value="client">
                                        <input type="hidden" name="doctype" value="documents">
                                        <a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>
                                        <input class="alldocupload" data-fileid="<?php echo $fetch->id;?>" type="file" name="document_upload"/>
                                    </form>
                                </div>
                            <?php
                            }?>
                        </td>
                        <td id="docverifiedby_<?php echo $fetch->id;?>">
                            <?php
                            echo $checklist_verified_by. "<br>";
                            echo $checklist_verified_at;
                            ?>
                        </td>
                        <td>
                            <?php
                            if( isset($fetch->file_name) && $fetch->file_name !="")
                            { ?>
                                <div class="dropdown d-inline">
                                    <button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item renamechecklist" href="javascript:;">Rename Checklist</a>
                                        <a class="dropdown-item renamealldoc" href="javascript:;">Rename File Name</a>
                                        <?php
                                        $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                        ?>
                                        <a target="_blank" class="dropdown-item" href="<?php echo $url.$client_unique_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Preview</a>
                                        <?php
                                        $explodeimg = explode('.',$fetch->myfile);
                                        if($explodeimg[1] == 'jpg'|| $explodeimg[1] == 'png'|| $explodeimg[1] == 'jpeg'){
                                        ?>
                                            <a target="_blank" class="dropdown-item" href="<?php echo \URL::to('/admin/document/download/pdf'); ?>/<?php echo $fetch->id; ?>">PDF</a>
                                        <?php } ?>
                                        <a download class="dropdown-item" href="<?php echo $url.$client_unique_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Download</a>
                                        <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item deletenote" data-href="deletealldocs" href="javascript:;" >Delete</a>
                                        <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item verifydoc" data-doctype="documents" data-href="verifydoc" href="javascript:;">Verify</a>
                                        <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item notuseddoc" data-doctype="documents" data-href="notuseddoc" href="javascript:;">Not Used</a>
                                    </div>
                                </div>
                            <?php
                            }?>
						</td>
					</tr>
					<?php
				}
				$data = ob_get_clean();
				ob_start();
				foreach($fetchd as $fetch){
					$admin = \App\Admin::where('id', $fetch->user_id)->first();
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
                                        $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                        ?>
										<a class="dropdown-item" href="<?php echo $url.$client_unique_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Preview</a>
										<a download class="dropdown-item" href="<?php echo $url.$client_unique_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Download</a>
                                        <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item deletenote" data-href="deletealldocs" href="javascript:;" >Delete</a>
                                        <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item verifydoc" data-doctype="documents" data-href="verifydoc" href="javascript:;">Verify</a>
                                        <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item notuseddoc" data-doctype="documents" data-href="notuseddoc" href="javascript:;">Not Used</a>
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
				$response['status'] = false;
				$response['message'] = 'Please try again';
			}
		} else {
			 $response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

    //verify document
    public function verifydoc(Request $request){ //dd($request->all());
		$doc_id = $request->doc_id;
        $doc_type = $request->doc_type;
        if(\App\Document::where('id',$doc_id)->exists()){
            $upd = DB::table('documents')->where('id', $doc_id)->update(array(
                'checklist_verified_by' => Auth::user()->id,
                'checklist_verified_at' => date('Y-m-d H:i:s')
            ));
            if($upd){
                $docInfo = \App\Document::select('client_id')->where('id',$doc_id)->first();
                $subject = 'verified '.$doc_type.' document';
                $objs = new ActivitiesLog;
				$objs->client_id = $docInfo->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '';
				$objs->subject = $subject;
				$objs->save();
                //Get verified at and verified by
                $admin_info = DB::table('admins')->select('first_name')->where('id', '=',Auth::user()->id)->first();
                if($admin_info){
                    $response['verified_by'] = 	$admin_info->first_name;
                    $response['verified_at'] = 	date('d/m/Y');
                } else {
                    $response['verified_by'] = "";
                    $response['verified_at'] = "";
                }
                $response['doc_type'] = $doc_type;
				$response['status'] = 	true;
				$response['data']	=	$doc_type.' Document verified successfully';
			} else {
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
                $response['verified_by'] = "";
                $response['verified_at'] = "";
                $response['doc_type'] = "";
			}
		} else {
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
            $response['verified_by'] = "";
            $response['verified_at'] = "";
            $response['doc_type'] = "";
		}
		echo json_encode($response);
	}

    //Not Used Document
    public function notuseddoc(Request $request){ //dd($request->all());
		$doc_id = $request->doc_id;
        $doc_type = $request->doc_type;
        if(\App\Document::where('id',$doc_id)->exists()){
            $upd = DB::table('documents')->where('id', $doc_id)->update(array('not_used_doc' => 1));
            if($upd){
                $docInfo = \App\Document::where('id',$doc_id)->first();
                $subject = $doc_type.' document moved to Not Used Tab';
                $objs = new ActivitiesLog;
				$objs->client_id = $docInfo->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '';
				$objs->subject = $subject;
				$objs->save();

                if($docInfo){
                    if( isset($docInfo->user_id) && $docInfo->user_id!= "" ){
                        $adminInfo = \App\Admin::select('first_name')->where('id',$docInfo->user_id)->first();
                        $response['Added_By'] = $adminInfo->first_name;
                        $response['Added_date'] = date('d/m/Y',strtotime($docInfo->created_at));
                    } else {
                        $response['Added_By'] = "N/A";
                        $response['Added_date'] = "N/A";
                    }


                    if( isset($docInfo->checklist_verified_by) && $docInfo->checklist_verified_by!= "" ){
                        $verifyInfo = \App\Admin::select('first_name')->where('id',$docInfo->checklist_verified_by)->first();
                        $response['Verified_By'] = $verifyInfo->first_name;
                        $response['Verified_At'] = date('d/m/Y',strtotime($docInfo->checklist_verified_at));
                    } else {
                        $response['Verified_By'] = "N/A";
                        $response['Verified_At'] = "N/A";
                    }

                }

                $response['docInfo'] = $docInfo;
                $response['doc_type'] = $doc_type;
                $response['doc_id'] = $doc_id;
				$response['status'] = 	true;
				$response['data']	=	$doc_type.' document moved to Not Used Tab';
			} else {
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
                $response['doc_type'] = "";
                $response['doc_id'] = "";
                $response['docInfo'] = "";

                $response['Added_By'] = "";
                $response['Added_date'] = "";
                $response['Verified_By'] = "";
                $response['Verified_At'] = "";
			}
		} else {
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
            $response['doc_type'] = "";
            $response['doc_id'] = "";
            $response['docInfo'] = "";

            $response['Added_By'] = "";
            $response['Added_date'] = "";
            $response['Verified_By'] = "";
            $response['Verified_At'] = "";
		}
		echo json_encode($response);
	}

    //Rename checklist in Document
    public function renamechecklistdoc(Request $request){
		$id = $request->id;
		$checklist = $request->checklist;
		if(\App\Document::where('id',$id)->exists()){
			$doc = \App\Document::where('id',$id)->first();
			$res = DB::table('documents')->where('id', @$id)->update(['checklist' => $checklist]);
			if($res){
				$response['status'] 	= 	true;
				$response['data']	=	'Checklist saved successfully';
				$response['Id']	=	$id;
				$response['checklist']	=	$checklist;
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

    //Delete all document
    public function deletealldocs(Request $request){
		$note_id = $request->note_id;
        if(\App\Document::where('id',$note_id)->exists()){
            $data = DB::table('documents')->where('id', @$note_id)->first();
			$res = DB::table('documents')->where('id', @$note_id)->delete();
            if($res){
                $subject = 'deleted a document';
                $objs = new ActivitiesLog;
				$objs->client_id = $data->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '';
				$objs->subject = $subject;
				$objs->save();
				$response['status'] 	= 	true;
				$response['data']	=	'Document removed successfully';
			} else {
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		} else {
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

    //Rename all document
    public function renamealldoc(Request $request){
		$id = $request->id;
		$filename = $request->filename;
		if(\App\Document::where('id',$id)->exists()){
			$doc = \App\Document::where('id',$id)->first();
			$res = DB::table('documents')->where('id', @$id)->update(['file_name' => $filename]);
			if($res){
				$response['status'] 	= 	true;
				$response['data']	=	'Document saved successfully';
				$response['Id']	=	$id;
				$response['filename']	=	$filename;
				$response['filetype']	=	$doc->filetype;
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
