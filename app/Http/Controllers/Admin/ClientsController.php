<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\ActivitiesLog;
use Auth;
use Config;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Models\CheckinLog;
use App\Models\Note;
use App\Models\clientServiceTaken;
use App\Models\AccountClientReceipt;

use Illuminate\Support\Facades\Storage;

use App\Models\Application;
use DataTables;
use Mail;

use App\Models\ClientPhone;
use Illuminate\Validation\Rule;

use DateTime;
use Carbon\Carbon;

//use App\Services\TwilioService;
use App\Services\SmsService;
use App\Services\SearchService;
use App\Models\VerifiedNumber;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

use App\Traits\ClientQueries;
use App\Traits\ClientAuthorization;
use App\Traits\ClientHelpers;

class ClientsController extends Controller
{
    use ClientQueries, ClientAuthorization, ClientHelpers;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
  
   // protected $twilioService;
    protected $smsService;
    protected $openAiClient;
  
    public function __construct(SmsService $smsService)
    {
        $this->middleware('auth:admin');
        //$this->twilioService = $twilioService;
        $this->smsService = $smsService;
      
        $this->openAiClient = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }
	/**
     * All Vendors.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		// Check authorization using trait
		if (!$this->hasModuleAccess('20')) {
			// Return empty result set for users without module access
			$lists = $this->getEmptyClientQuery()->paginate(20);
			$totalData = 0;
			return view($this->getClientViewPath('clients.index'), compact(['lists', 'totalData']));
		}
		
		// Get base query with automatic agent filtering
		$query = $this->getBaseClientQuery();
		$totalData = $query->count();
		
		// Apply filters using trait method
		$query = $this->applyClientFilters($query, $request);
		
		// Paginate results
		$lists = $query->sortable(['id' => 'desc'])->paginate(20);
		
		// Return appropriate view based on context
		return view($this->getClientViewPath('clients.index'), compact(['lists', 'totalData']));
	}

	public function archived(Request $request)
	{
		// Get archived clients query with automatic agent filtering
		$query = $this->getArchivedClientQuery();
		$totalData = $query->count();
		
		// Paginate results
		$lists = $query->sortable(['id' => 'desc'])->paginate(20);
		
		// Return appropriate view based on context
		return view($this->getClientViewPath('archived.index'), compact(['lists', 'totalData']));
	}

	public function create(Request $request)
	{
		// Return appropriate view based on context
		return view($this->getClientViewPath('clients.create'));
	}

	public function store(Request $request)
	{
		if ($request->isMethod('post'))
		{
			// Use trait method for validation rules
			$this->validate($request, $this->getClientValidationRules($request));

			$requestData = $request->all();
			
			// Date formatting using trait helpers
			$dob = $this->formatDateForDatabase(@$requestData['dob']);
			$visaExpiry = $this->formatDateForDatabase(@$requestData['visaExpiry']);
			
			$first_name = substr(@$requestData['first_name'], 0, 4);
			$obj = new Admin;
			
			// Basic info
			$obj->first_name = @$requestData['first_name'];
			$obj->last_name = @$requestData['last_name'];
			$obj->age = @$requestData['age'];
			$obj->gender = @$requestData['gender'];
			$obj->martial_status = @$requestData['martial_status'];
			$obj->contact_type = @$requestData['contact_type'];
			$obj->email_type = @$requestData['email_type'];
			$obj->service = @$requestData['service'];
			$obj->dob = $dob;
			$obj->email = @$requestData['email'];
			$obj->phone = @$requestData['phone'];
			$obj->address = @$requestData['address'];
			
			// Geocoding for address
			if (isset($requestData['address']) && $requestData['address'] != "") {
				$address = @$requestData['address'];
				$result = app('geocoder')->geocode($address)->get();
				$coordinates = $result[0]->getCoordinates();
				$obj->latitude = $coordinates->getLatitude();
				$obj->longitude = $coordinates->getLongitude();
			}
			
			$obj->city = @$requestData['city'];
			$obj->visa_opt = @$requestData['visa_opt'];
			$obj->state = @$requestData['state'];
			$obj->zip = @$requestData['zip'];
			$obj->country = @$requestData['country'];
			$obj->preferredIntake = @$requestData['preferredIntake'];
			$obj->country_passport = @$requestData['country_passport'];
			$obj->passport_number = @$requestData['passport_number'];
			$obj->visa_type = @$requestData['visa_type'];
			$obj->visaExpiry = $visaExpiry;
			$obj->applications = @$requestData['applications'];
			$obj->assignee = @$requestData['assign_to'];
			$obj->status = $requestData['status'] ?? 1;
			$obj->lead_quality = @$requestData['lead_quality'];
			$obj->att_phone = @$requestData['att_phone'];
			$obj->att_country_code = @$requestData['att_country_code'];
			$obj->att_email = @$requestData['att_email'];
			$obj->nomi_occupation = @$requestData['nomi_occupation'];
			$obj->skill_assessment = @$requestData['skill_assessment'];
			$obj->high_quali_aus = @$requestData['high_quali_aus'];
			$obj->high_quali_overseas = @$requestData['high_quali_overseas'];
			$obj->relevant_work_exp_aus = @$requestData['relevant_work_exp_aus'];
			$obj->relevant_work_exp_over = @$requestData['relevant_work_exp_over'];
			$obj->married_partner = @$requestData['married_partner'];
			$obj->total_points = @$requestData['total_points'];
			$obj->start_process = @$requestData['start_process'];
			$obj->comments_note = @$requestData['comments_note'];
			$obj->type = @$requestData['type'];
			
			// Use trait helpers for array processing
			$obj->related_files = $this->processRelatedFiles($request);
			$obj->followers = $this->processFollowers($request);
			$obj->tagname = $this->processTags($request);
			
			// NAATI
			if (isset($requestData['naati_py']) && !empty($requestData['naati_py'])) {
				$obj->naati_py = implode(',', @$requestData['naati_py']);
			}
			
			// Source and agent assignment
			$obj->source = @$requestData['source'];
			if (@$requestData['source'] == 'Sub Agent') {
				$obj->agent_id = @$requestData['subagent'];
			} else {
				$obj->agent_id = '';
			}
			
			$obj->role = 7; // Client role
			$obj->country_code = @$requestData['country_code'];
			
			// Profile image upload using trait helper
			if ($request->hasfile('profile_img')) {
				$obj->profile_img = $this->uploadClientFile(
					$request->file('profile_img'), 
					'constants.profile_imgs'
				);
			} else {
				$obj->profile_img = NULL;
			}
			
			// Client ID handling
			if (!empty($requestData['client_id'])) {
				$obj->client_id = @$requestData['client_id'];
			}
			
			$saved = $obj->save();
			
			// Generate client_id if not provided
			if (empty($requestData['client_id'])) {
				$objs = Admin::find($obj->id);
				$objs->client_id = $this->generateClientId($requestData['first_name'], $obj->id);
				$objs->save();
			}
			
			if (!$saved) {
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			// Redirect to client detail page
			return redirect($this->getClientRedirectUrl('detail', $obj->id))
				->with('success', 'Clients Added Successfully');
		}

		return view($this->getClientViewPath('clients.create'));
	}

	public function downloadpdf(Request $request, $id = NULL){
	    	$fetchd = \App\Models\Document::where('id',$id)->first();
	    	$data = ['title' => 'Welcome to codeplaners.com','image' => $fetchd->myfile];
     $pdf = PDF::loadView('myPDF', $data);

     return $pdf->stream('codeplaners.pdf');
	}
  
	/*public function edit(Request $request, $id = NULL)
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
	            $relatedFilesCount = count($requestData['related_files']);
	            for($i=0; $i<$relatedFilesCount; $i++){
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
			$obj->dob	=	($dob != '') ? $dob : null;
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
			$obj->visaExpiry			=	($visaExpiry != '') ? $visaExpiry : null;
			$obj->applications	=	@$requestData['applications'];
          
			//$obj->assignee	=	@$requestData['assign_to'];
            if( isset($requestData['assign_to']) && is_array($requestData['assign_to']) ){
                $assignToCount = count($requestData['assign_to']);
                if( $assignToCount >1 ) {
                    $obj->assignee	=  implode(",", $requestData['assign_to']);
                } else if( $assignToCount == 1 ) {
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

			
			if($request->hasfile('profile_img'))
			{
				
					if($requestData['profile_img'] != '')
						{
							$this->unlinkFile($requestData['old_profile_img'], Config::get('constants.profile_imgs'));
						}
				

				$profile_img = $this->uploadFile($request->file('profile_img'), Config::get('constants.profile_imgs'));
			}
			else
			{
				$profile_img = @$requestData['old_profile_img'];
			}
		
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
             else  if($route==url('/action')){
				$subject = 'Lead status has changed to '.@$requestData['status'].' from '. \Auth::user()->first_name;
				$objs = new ActivitiesLog;
				$objs->client_id = $request->id;
				$objs->created_by = \Auth::user()->id;
				$objs->subject = $subject;
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
				$objs->save();

				return redirect()->route('action.index')->with('success','Action updated successfully');
			}

			else
			{
              
              //Code for addition of simiar related files in added users account  
                    if(isset($requestData['related_files']))
                    {
                        $relatedFilesCount = count($requestData['related_files']);
                        for($j=0; $j<$relatedFilesCount; $j++){
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
                      $diffArrCount = count($diff_arr);
                      for($k=0; $k<$diffArrCount; $k++)
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
              
              // Save test scores if provided
              if(isset($requestData['test_type']) && isset($requestData['test_score_client_id'])) {
                  $testType = $requestData['test_type'];
                  $clientId = $requestData['test_score_client_id'];
                  $testScoreType = isset($requestData['test_score_type']) ? $requestData['test_score_type'] : 'client';
                  
                  if(\App\Models\TestScore::where('client_id', $clientId)->where('type', $testScoreType)->exists()){
                      $testscores = \App\Models\TestScore::where('client_id', $clientId)->where('type', $testScoreType)->first();
                      $objTest = \App\Models\TestScore::find($testscores->id);
                  }else{
                      $objTest = new \App\Models\TestScore;
                      $objTest->user_id = @Auth::user()->id;
                      $objTest->client_id = $clientId;
                      $objTest->type = $testScoreType;
                  }
                  
                  // Update fields based on test type
                  if($testType == 'toefl'){
                      $objTest->toefl_Listening = $requestData['listening'] ?? null;
                      $objTest->toefl_Reading = $requestData['reading'] ?? null;
                      $objTest->toefl_Writing = $requestData['writing'] ?? null;
                      $objTest->toefl_Speaking = $requestData['speaking'] ?? null;
                      $objTest->score_1 = $requestData['overall'] ?? null;
                      $objTest->toefl_Date = $requestData['test_date'] ?? null;
                  }elseif($testType == 'ilets'){
                      $objTest->ilets_Listening = $requestData['listening'] ?? null;
                      $objTest->ilets_Reading = $requestData['reading'] ?? null;
                      $objTest->ilets_Writing = $requestData['writing'] ?? null;
                      $objTest->ilets_Speaking = $requestData['speaking'] ?? null;
                      $objTest->score_2 = $requestData['overall'] ?? null;
                      $objTest->ilets_Date = $requestData['test_date'] ?? null;
                  }elseif($testType == 'pte'){
                      $objTest->pte_Listening = $requestData['listening'] ?? null;
                      $objTest->pte_Reading = $requestData['reading'] ?? null;
                      $objTest->pte_Writing = $requestData['writing'] ?? null;
                      $objTest->pte_Speaking = $requestData['speaking'] ?? null;
                      $objTest->score_3 = $requestData['overall'] ?? null;
                      $objTest->pte_Date = $requestData['test_date'] ?? null;
                  }
                  
                  $objTest->save();
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

	}*/
  
  
  
    public function edit(Request $request, $id = NULL)
	{
		 
        $showAlert = false;
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
              //'phone' => 'required|max:255|unique:admins,phone,'.$requestData['id'],
              //'client_id' => 'required|max:255|unique:admins,client_id,'.$requestData['id']

              'contact_type' => 'required|array',
              'contact_type.*' => 'required|max:255',

              'client_phone' => 'required|array',
              'client_phone.*' => 'required|max:255',

            ]);
          
             if ( isset($requestData['contact_type']) && count(array_keys($requestData['contact_type'] , "Personal")) > 1) {
                //echo "Error: 'Personal' contact type can only be used once.";
                return redirect()->back()->with('error', "Error: 'Personal' contact type can only be used once.");
            }
          
			$related_files = '';
	        if(isset($requestData['related_files'])){
	            $relatedFilesCount = count($requestData['related_files']);
	            for($i=0; $i<$relatedFilesCount; $i++){
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
			//$obj->age	=	@$requestData['age'];
			$obj->gender	=	@$requestData['gender'];
			$obj->martial_status	=	@$requestData['martial_status'];
			
			$obj->email_type	=	@$requestData['email_type'];
			$obj->service	=	@$requestData['service'];
          
			$obj->dob	=	($dob != '') ? $dob : null;
            if(isset($dob) && $dob != ""){
                $calculate_age  = $this->calculateAge($dob); //dd($age);
                $obj->age	=	$calculate_age;
            }
          
			$obj->related_files	=	rtrim($related_files,',');
			$obj->email	=	@$requestData['email'];
          
			//$obj->contact_type	=	@$requestData['contact_type'];
            //$obj->country_code	=	@$requestData['country_code'];
			//$obj->phone	=	@$requestData['phone'];
          
		$obj->address	=	@$requestData['address'];
		
		// Geocoding disabled - GPS coordinates not needed
		/*
		if( isset($requestData['address']) && $requestData['address'] != ""){
            //$address = "16-18, Argyle Street, Camden, London, WC1H 8EG, United Kingdom";
            $address = @$requestData['address'];
            $result = app('geocoder')->geocode($address)->get(); //dd($result);
          
            if(isset($result[0]) && $result[0] != "" ){
                $coordinates = $result[0]->getCoordinates();
                $lat = $coordinates->getLatitude();
                $long = $coordinates->getLongitude();
            } else {
                $lat = "";
                $long = "";
            }
          
            //echo "lat==".$lat;
            //echo "long==".$long; die();
			$obj->latitude	=	$lat;
            $obj->longitude	=	$long;
        }
		*/

			$obj->city	=	@$requestData['city'];
			$obj->state	=	@$requestData['state'];
			$obj->zip	=	@$requestData['zip'];
			$obj->country	=	@$requestData['country'];
			$obj->visa_opt = @$requestData['visa_opt'];
			$obj->preferredIntake	=	@$requestData['preferredIntake'];
			$obj->country_passport			=	@$requestData['country_passport'];
			$obj->passport_number			=	@$requestData['passport_number'];
			$obj->visa_type			=		@$requestData['visa_type'];
			$obj->visaExpiry			=	($visaExpiry != '') ? $visaExpiry : null;
			$obj->applications	=	@$requestData['applications'];
          
			//$obj->assignee	=	@$requestData['assign_to'];
            if( isset($requestData['assign_to']) && is_array($requestData['assign_to']) ){
                $assignToCount = count($requestData['assign_to']);
                if( $assignToCount >1 ) {
                    $obj->assignee	=  implode(",", $requestData['assign_to']);
                } else if( $assignToCount == 1 ) {
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

			
			if($request->hasfile('profile_img'))
			{
				
					if($requestData['profile_img'] != '')
						{
							$this->unlinkFile($requestData['old_profile_img'], Config::get('constants.profile_imgs'));
						}
				

				$profile_img = $this->uploadFile($request->file('profile_img'), Config::get('constants.profile_imgs'));
			}
			else
			{
				$profile_img = @$requestData['old_profile_img'];
			}
		
			$obj->profile_img			=	@$profile_img;
			
			 //$obj->manual_email_phone_verified	=	@$requestData['manual_email_phone_verified'];
			 
			$saved							=	$obj->save();
          
            //////////////////////////////////////////////////////
            //////////Code Start For client phone////////////////
            //////////////////////////////////////////////////////
            //////////////////////////////////////////////////////
          
          
            //Update partner phone table
            if(isset($requestData['rem_phone'])){
                $rem_phone =  @$requestData['rem_phone'];
                $remPhoneCount = count($rem_phone);
                for($irem_phone=0; $irem_phone< $remPhoneCount; $irem_phone++){
                    if(\App\Models\ClientPhone::where('id', $rem_phone[$irem_phone])->exists()){
                        \App\Models\ClientPhone::where('id', $rem_phone[$irem_phone])->delete();
                    }
                }
            }

            if(isset($requestData['contact_type'])){
                $contact_type =  $requestData['contact_type'];
            } else {
                $contact_type = array();
            }

            if(isset($requestData['client_country_code'])){
                $client_country_code =  $requestData['client_country_code'];
            } else {
                $client_country_code = array();
            }

            if(isset($requestData['client_phone'])){
                $client_phone =  $requestData['client_phone'];
            } else {
                $client_phone = array();
            }

            $clientPhoneCount = count($client_phone);
            if($clientPhoneCount >0){
                for($iii=0; $iii< $clientPhoneCount; $iii++){
                    if(\App\Models\ClientPhone::where('id', $requestData['clientphoneid'][$iii])->exists()){
                        $os1 = \App\Models\ClientPhone::find($requestData['clientphoneid'][$iii]);
                        $os1->user_id = @Auth::user()->id;
                        $os1->client_id = @$obj->id;
                        $os1->contact_type = @$contact_type[$iii];
                        $os1->client_country_code = @$client_country_code[$iii];
                        $os1->client_phone = @$client_phone[$iii];
                        $os1->updated_at = date('Y-m-d H:i:s');
                        $os1->save();
                    } else {
                        $oe1 = new \App\Models\ClientPhone;
                        $oe1->user_id = @Auth::user()->id;
                        $oe1->client_id = @$obj->id;
                        $oe1->contact_type = @$contact_type[$iii];
                        $oe1->client_country_code = @$client_country_code[$iii];
                        $oe1->client_phone = @$client_phone[$iii];
                        $oe1->created_at = date('Y-m-d H:i:s');
                        $oe1->updated_at = date('Y-m-d H:i:s');
                        $oe1->save();
                    }

                    if( isset($contact_type[$iii]) && $contact_type[$iii] == 'Personal'){
                        //Update admin  table
                        $adminInfo1 = Admin::find($obj->id); // Retrieve the record by ID
                        $lastContactType = $contact_type[$iii];
                        $lastPhoneCountryCode = $client_country_code[$iii];
                        $lastPhone = $client_phone[$iii];
                        $adminInfo1->contact_type =  $lastContactType;
                        $adminInfo1->country_code =  $lastPhoneCountryCode;
                        $adminInfo1->phone =  $lastPhone;
                        $adminInfo1->save(); // Save the changes
                    }
                } //end for loop
            }
            //////////////////////////////////////////////////////
            //////////Code End For client phone////////////////
            //////////////////////////////////////////////////////
            //////////////////////////////////////////////////////
          
          
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
             else  if($route==url('/action')){
				$subject = 'Lead status has changed to '.@$requestData['status'].' from '. \Auth::user()->first_name;
				$objs = new ActivitiesLog;
				$objs->client_id = $request->id;
				$objs->created_by = \Auth::user()->id;
				$objs->subject = $subject;
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
				$objs->save();

				return redirect()->route('action.index')->with('success','Action updated successfully');
			}

			else
			{
              
              //Code for addition of simiar related files in added users account  
                    if(isset($requestData['related_files']))
                    {
                        $relatedFilesCount = count($requestData['related_files']);
                        for($j=0; $j<$relatedFilesCount; $j++){
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
                      $diffArrCount = count($diff_arr);
                      for($k=0; $k<$diffArrCount; $k++)
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
              
              return redirect()->route('clients.detail', $this->encodeString(@$requestData['id']))->with('success', 'Clients Edited Successfully');
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
                  
                    if(!empty($fetchedData) && $fetchedData->dob != ""){
                        $calculate_age  = $this->calculateAge($fetchedData->dob); //dd($age);
                        $fetchedData->age = $calculate_age;  // Update age in the database
                        $fetchedData->save();
                    }
                  
                     //Check phone record is exist in client phone table
                    if( \App\Models\ClientPhone::where('client_id', $id)->doesntExist() ){
                        if( $fetchedData->phone != "" ) {
                            $oef1 = new \App\Models\ClientPhone;
                            $oef1->user_id = @Auth::user()->id;
                            $oef1->client_id = $id;
                            $oef1->contact_type = $fetchedData->contact_type;
                            $oef1->client_country_code = $fetchedData->country_code;
                            $oef1->client_phone = $fetchedData->phone;
                            $oef1->created_at = date('Y-m-d H:i:s');
                            $oef1->updated_at = date('Y-m-d H:i:s');
                            $oef1->save();
                        }

                        if( $fetchedData->att_phone != "" ) {
                            $oef1 = new \App\Models\ClientPhone;
                            $oef1->user_id = @Auth::user()->id;
                            $oef1->client_id = $id;
                            $oef1->contact_type = '';
                            $oef1->client_country_code = $fetchedData->att_country_code;
                            $oef1->client_phone = $fetchedData->att_phone;
                            $oef1->created_at = date('Y-m-d H:i:s');
                            $oef1->updated_at = date('Y-m-d H:i:s');
                            $oef1->save();
                        }
                    }
                  
                  
                     //Show alert box is entry is updated before 1 month ago
                    if ($fetchedData && $fetchedData->updated_at) {
                        $updatedAt = Carbon::parse($fetchedData->updated_at);
                        $fourWeeksAgo = Carbon::now()->subWeeks(4);
                        if ($updatedAt->lt($fourWeeksAgo)) {
                            $showAlert = true;
                        }
                    }
                  
					return view('Admin.clients.edit', compact(['fetchedData','showAlert']));
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
  
    
	/*public function detail(Request $request, $id = NULL){ 
		 $showAlert = false;
	     if(isset($request->t)){
    	    if(\App\Models\Notification::where('id', $request->t)->exists()){
    	       $ovv =  \App\Models\Notification::find($request->t);
    	       $ovv->receiver_status = 1;
    	       $ovv->save();
    	    }
	    }
		if(isset($id) && !empty($id))
			{
				$encodeId = $id;
				$originalId = $id;
				$id = $this->decodeString($id); //dd($id);
				
				// Check if decodeString returned false (invalid encoded string)
				if($id === false || empty($id))
				{
					return Redirect::to('/admin/clients')->with('error', 'Invalid Client ID');
				}
				
				// Check if this is a lead from the separate leads table by checking the original encoded ID
				// If the ID was generated from the leads table, check there first
				$isFromLeadsTable = false;
				if(\App\Models\Lead::where('id', '=', $id)->exists())
				{
					// Check if this same ID exists in admins table with different data
					// If both exist, we need to determine which one the user clicked
					if(Admin::where('id', '=', $id)->where('role', '=', '7')->exists())
					{
						// Both exist with same ID - need to check which table the search result came from
						// For now, prefer leads table for new searches (you can adjust this logic)
						$lead = \App\Models\Lead::find($id);
						$admin = Admin::find($id);
						
						// Compare data to see which matches better (this is a fallback)
						// In the future, you might want to use different ID schemes
						$isFromLeadsTable = true; // Default to leads for now
					}
					else
					{
						$isFromLeadsTable = true;
					}
				}
				//dd($lead->id); die;
				// Check leads table first if determined to be from there
				if($isFromLeadsTable)
				{
					$lead = \App\Models\Lead::with('staffuser')->find($id); 
					
					// Create a temporary Admin object with lead data for the view
					$fetchedData = new Admin(); dd($fetchedData); die;
					$fetchedData->exists = true; // Mark as existing record
					$fetchedData->id = $lead->id;
					$fetchedData->first_name = $lead->first_name;
					$fetchedData->last_name = $lead->last_name;
					$fetchedData->email = $lead->email;
					$fetchedData->phone = $lead->phone;
					$fetchedData->country_code = $lead->country_code;
					$fetchedData->gender = $lead->gender;
					$fetchedData->dob = $lead->dob;
					$fetchedData->visa_type = $lead->visa_type ?? null;
					$fetchedData->visa_expiry_date = $lead->visa_expiry_date;
					$fetchedData->type = 'lead'; // Mark as lead type
					$fetchedData->profile_img = $lead->profile_img;
					$fetchedData->created_at = $lead->created_at;
					$fetchedData->updated_at = $lead->updated_at;
					$fetchedData->client_id = 'LEAD-' . str_pad($lead->id, 4, '0', STR_PAD_LEFT);
					$fetchedData->role = 7; // Set role to 7 like other clients
					$fetchedData->is_archived = 0;
					$fetchedData->office_id = $lead->staffuser->office_id ?? null;
					$fetchedData->att_email = $lead->att_email ?? null;
					$fetchedData->att_phone = $lead->att_phone ?? null;
					$fetchedData->martial_status = $lead->martial_status ?? null;
					$fetchedData->passport_no = $lead->passport_no ?? null;
					$fetchedData->address = $lead->address ?? null;
					$fetchedData->city = $lead->city ?? null;
					$fetchedData->state = $lead->state ?? null;
					$fetchedData->zip = $lead->zip ?? null;
					$fetchedData->country = $lead->country ?? null;
					$fetchedData->nomi_occupation = $lead->nomi_occupation ?? null;
					
					// Add relationship for assigned user
					if($lead->assign_to) {
						$fetchedData->setRelation('staffuser', $lead->staffuser);
					}
					
					// Calculate age if DOB exists
					if(!empty($fetchedData->dob)){
						$fetchedData->age = $this->calculateAge($fetchedData->dob);
					}
					
					return view('Admin.clients.detail', compact(['fetchedData','encodeId','showAlert']));
				}
				// Otherwise check admins table (old clients/leads)
				elseif(Admin::where('id', '=', $id)->where('role', '=', '7')->exists())
				{
					$fetchedData = Admin::find($id);
					
					// Double check that fetchedData exists
					if(empty($fetchedData))
					{
						return Redirect::to('/admin/clients')->with('error', 'Client data not found');
					}
                  
                    if(!empty($fetchedData) && $fetchedData->dob != ""){
                        $calculate_age  = $this->calculateAge($fetchedData->dob); //dd($age);
                        $fetchedData->age = $calculate_age;  // Update age in the database
                        $fetchedData->save();
                    }
                  
                  
                    //Show alert box is entry is updated before 1 month ago
                    if ($fetchedData && $fetchedData->updated_at) {
                        $updatedAt = Carbon::parse($fetchedData->updated_at);
                        $fourWeeksAgo = Carbon::now()->subWeeks(4);
                        if ($updatedAt->lt($fourWeeksAgo)) {
                            $showAlert = true;
                        }
                    }
                  
					return view('Admin.clients.detail', compact(['fetchedData','encodeId','showAlert']));
				}
				// If not in admins table, check if it's a new lead in the leads table
				elseif(\App\Models\Lead::where('id', '=', $id)->exists())
				{
					$lead = \App\Models\Lead::with('staffuser')->find($id);
					
					// Create a temporary Admin object with lead data for the view
					$fetchedData = new Admin();
					$fetchedData->exists = true; // Mark as existing record
					$fetchedData->id = $lead->id;
					$fetchedData->first_name = $lead->first_name;
					$fetchedData->last_name = $lead->last_name;
					$fetchedData->email = $lead->email;
					$fetchedData->phone = $lead->phone;
					$fetchedData->country_code = $lead->country_code;
					$fetchedData->gender = $lead->gender;
					$fetchedData->dob = $lead->dob;
					$fetchedData->visa_type = $lead->visa_type ?? null;
					$fetchedData->visa_expiry_date = $lead->visa_expiry_date;
					$fetchedData->type = 'lead'; // Mark as lead type
					$fetchedData->profile_img = $lead->profile_img;
					$fetchedData->created_at = $lead->created_at;
					$fetchedData->updated_at = $lead->updated_at;
					$fetchedData->client_id = 'LEAD-' . str_pad($lead->id, 4, '0', STR_PAD_LEFT);
					$fetchedData->role = 7; // Set role to 7 like other clients
					$fetchedData->is_archived = 0;
					$fetchedData->office_id = $lead->staffuser->office_id ?? null;
					$fetchedData->att_email = $lead->att_email ?? null;
					$fetchedData->att_phone = $lead->att_phone ?? null;
					$fetchedData->martial_status = $lead->martial_status ?? null;
					$fetchedData->passport_no = $lead->passport_no ?? null;
					$fetchedData->address = $lead->address ?? null;
					$fetchedData->city = $lead->city ?? null;
					$fetchedData->state = $lead->state ?? null;
					$fetchedData->zip = $lead->zip ?? null;
					$fetchedData->country = $lead->country ?? null;
					$fetchedData->nomi_occupation = $lead->nomi_occupation ?? null;
					
					// Add relationship for assigned user
					if($lead->assign_to) {
						$fetchedData->setRelation('staffuser', $lead->staffuser);
					}
					
					// Calculate age if DOB exists
					if(!empty($fetchedData->dob)){
						$fetchedData->age = $this->calculateAge($fetchedData->dob);
					}
					
					return view('Admin.clients.detail', compact(['fetchedData','encodeId','showAlert']));
				}
				else
				{
					return Redirect::to('/admin/clients')->with('error', 'Client or Lead Not Found');
				}
			}
			else
			{
				return Redirect::to('/admin/clients')->with('error', Config::get('constants.unauthorized'));
			}
	}*/

    //Client detail page
    public function clientdetail(Request $request, $id = NULL){ 
        $showAlert = false;
        if(isset($request->t)){
           if(\App\Models\Notification::where('id', $request->t)->exists()){
              $ovv =  \App\Models\Notification::find($request->t);
              $ovv->receiver_status = 1;
              $ovv->save();
           }
       }
       if(isset($id) && !empty($id))
        {
            $encodeId = $id;
            $originalId = $id;
            $id = $this->decodeString($id); //dd($id);
            
            // Check if decodeString returned false (invalid encoded string)
            if($id === false || empty($id))
            {
                return Redirect::to($this->getClientRedirectUrl('index'))->with('error', 'Invalid Client ID');
            }
            // Otherwise check admins table (old clients/leads)
            if(Admin::where('id', '=', $id)->where('role', '=', '7')->exists())
            {
                $fetchedData = Admin::find($id);
                
                // Double check that fetchedData exists
                if(empty($fetchedData))
                {
                    return Redirect::to($this->getClientRedirectUrl('index'))->with('error', 'Client data not found');
                }
                
                if(!empty($fetchedData) && $fetchedData->dob != ""){
                    $calculate_age  = $this->calculateAge($fetchedData->dob); //dd($age);
                    $fetchedData->age = $calculate_age;  // Update age in the database
                    $fetchedData->save();
                }
                
                
                //Show alert box is entry is updated before 1 month ago
                if ($fetchedData && $fetchedData->updated_at) {
                    $updatedAt = Carbon::parse($fetchedData->updated_at);
                    $fourWeeksAgo = Carbon::now()->subWeeks(4);
                    if ($updatedAt->lt($fourWeeksAgo)) {
                        $showAlert = true;
                    }
                }
                
                return view($this->getClientViewPath('clients.detail'), compact(['fetchedData','encodeId','showAlert']));
            }
            else
            {
                return Redirect::to($this->getClientRedirectUrl('index'))->with('error', 'Client or Lead Not Found');
            }
        }
        else
        {
            return Redirect::to($this->getClientRedirectUrl('index'))->with('error', Config::get('constants.unauthorized'));
        }
    }

    //Lead detail page
    public function leaddetail(Request $request, $id = NULL){ 
        $showAlert = false;
        if(isset($request->t)){
           if(\App\Models\Notification::where('id', $request->t)->exists()){
              $ovv =  \App\Models\Notification::find($request->t);
              $ovv->receiver_status = 1;
              $ovv->save();
           }
        }
        if(isset($id) && !empty($id))
        {
            $encodeId = $id;
            $originalId = $id;
            $id = $this->decodeString($id);
            
            // Check if decodeString returned false (invalid encoded string)
            if($id === false || empty($id))
            {
                return Redirect::to('/admin/leads')->with('error', 'Invalid Lead ID');
            }
            
            // If not in admins table, check if it's a new lead in the leads table
            if(\App\Models\Lead::where('id', '=', $id)->exists())
            {
                $lead = \App\Models\Lead::with('staffuser')->find($id);//dd($lead); die;
                //Check Lead is alreay exist in admins table or not 
                $enqdata = Admin::where('lead_id', $id)->first();
                if($enqdata){
                    $fetchedData  = Admin::find($enqdata->id);
                } else {
                    //Insert new lead in admins table
                    $obj = new Admin();
                    $obj->lead_id = $lead->id;
                    $obj->first_name = $lead->first_name;
                    $obj->last_name = $lead->last_name;
                    $obj->email = $lead->email;
                    $obj->phone = $lead->phone;
                    $obj->country_code = $lead->country_code;
                    $obj->gender = $lead->gender;
                    $obj->dob = $lead->dob;
                    $obj->visa_type = $lead->visa_type ?? null;
                    //$obj->visa_expiry_date = $lead->visa_expiry_date;
                    $obj->type = 'lead'; // Mark as lead type
                    $obj->profile_img = $lead->profile_img;
                    $obj->created_at = $lead->created_at;
                    $obj->updated_at = $lead->updated_at;
                    
                    $obj->role = 7; // Set role to 7 like other clients
                    $obj->is_archived = 0;
                    $obj->verified = 0; // New leads/clients are not verified yet
                    $obj->show_dashboard_per = 0; // Leads/clients don't have dashboard access
                    $obj->office_id = $lead->staffuser->office_id ?? null;
                    $obj->att_email = $lead->att_email ?? null;
                    $obj->att_phone = $lead->att_phone ?? null;
                    $obj->martial_status = $lead->martial_status ?? null;
                    //$obj->passport_no = $lead->passport_no ?? null;
                    $obj->address = $lead->address ?? null;
                    $obj->city = $lead->city ?? null;
                    $obj->state = $lead->state ?? null;
                    $obj->zip = $lead->zip ?? null;
                    $obj->country = $lead->country ?? null;
                    $obj->nomi_occupation = $lead->nomi_occupation ?? null;
                    
                    // Add relationship for assigned user
                    if($lead->assign_to) {
                        $obj->setRelation('staffuser', $lead->staffuser);
                    }
                    
                    // Calculate age if DOB exists
                    if(!empty($lead->dob)){
                        $obj->age = $this->calculateAge($lead->dob);
                    }
                    $obj->save();

                    $fetchedData = Admin::find($obj->id);
                    if($fetchedData->client_id == '' && $fetchedData->role == 7){
                        $objs	= 	Admin::find($obj->id);

                        $first_name = substr(@$lead->first_name, 0, 4);
                        $objs->client_id	=	strtoupper($first_name).date('ym').$objs->id;
                        $saveds				=	$objs->save();
                        
                        // Refresh $fetchedData after client_id is saved to ensure it's available on first load
                        $fetchedData = Admin::find($obj->id);
                    }
                }
                 //Show alert box is entry is updated before 1 month ago
                 if ($fetchedData && $fetchedData->updated_at) {
                    $updatedAt = Carbon::parse($fetchedData->updated_at);
                    $fourWeeksAgo = Carbon::now()->subWeeks(4);
                    if ($updatedAt->lt($fourWeeksAgo)) {
                        $showAlert = true;
                    }
                }
                return view($this->getClientViewPath('clients.detail'), compact(['fetchedData','encodeId','showAlert']));
            }
            else
            {
                return Redirect::to($this->getClientRedirectUrl('index'))->with('error', 'Client or Lead Not Found');
            }
        }
        else
        {
            return Redirect::to($this->getClientRedirectUrl('index'))->with('error', Config::get('constants.unauthorized'));
        }
    }
  
    //Calculate age
    function calculateAge($dob) {
        // Convert the DOB string to a DateTime object
        $birthDate = new \DateTime($dob);
        // Get the current date
        $today = new \DateTime();
        // Calculate the difference between the current date and the birth date
        $diff = $today->diff($birthDate);

        // Get the years and months from the difference
        $ageYears = $diff->y;
        $ageMonths = $diff->m;

        return "$ageYears years and $ageMonths months";
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
		$squery = $request->q ?? '';
		if($squery != ''){
			try {
				$clients = \App\Models\Admin::where('is_archived', '=', 0)
					->where('role', '=', 7)
					->where(function($query) use ($squery) {
						$query->where('email', 'LIKE', '%'.$squery.'%')
							->orWhere('first_name', 'LIKE', '%'.$squery.'%')
							->orWhere('last_name', 'LIKE', '%'.$squery.'%')
							->orWhere('client_id', 'LIKE', '%'.$squery.'%')
							->orWhere('phone', 'LIKE', '%'.$squery.'%')
							->orWhere(DB::raw("COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')"), 'LIKE', '%'.$squery.'%');
					})
					->get();

				$items = array();
				foreach($clients as $clint){
					$fullName = trim(($clint->first_name ?? '') . ' ' . ($clint->last_name ?? ''));
					$items[] = array(
						'id' => $clint->id,
						'text' => $fullName, // Required by Select2
						'name' => $fullName,
						'email' => $clint->email ?? '',
						'status' => $clint->type ?? 'Client',
						'cid' => base64_encode(convert_uuencode($clint->id))
					);
				}

				return response()->json(array('items'=>$items));
			} catch (\Exception $e) {
				\Log::error('getrecipients error: ' . $e->getMessage());
				return response()->json(array('items'=>array(), 'error' => $e->getMessage()));
			}
		} else {
			return response()->json(array('items'=>array()));
		}
	}

	public function getonlyclientrecipients(Request $request){
		$squery = $request->q;
		if($squery != ''){
				$d = '';
			 $clients = \App\Models\Admin::where('is_archived', '=', 0)
       ->where('role', '=', 7)
       ->where(
           function($query) use ($squery) {
             return $query
                    ->where('email', 'LIKE', '%'.$squery.'%')
                    ->orwhere('first_name', 'LIKE','%'.$squery.'%')->orwhere('last_name', 'LIKE','%'.$squery.'%')->orwhere('client_id', 'LIKE','%'.$squery.'%')->orwhere('phone', 'LIKE','%'.$squery.'%')  ->orWhere(DB::raw("COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')"), 'LIKE', "%".$squery."%");
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


	/**
	 * Modern search endpoint with validation, limits, and caching
	 * Searches across: Clients, Leads, Partners, Products, Applications
	 */
	public function getallclients(Request $request){
		// Validate input
		$validated = $request->validate([
			'q' => 'required|string|min:2|max:100',
		]);

		$query = $validated['q'];

		// Use SearchService for optimized search
		$searchService = new SearchService($query, 50, true);
		$results = $searchService->search();

		return response()->json($results);
	}


	public function activities(Request $request){
		if(Admin::where('role', '=', '7')->where('id', $request->id)->exists()){
			$activities = ActivitiesLog::where('client_id', $request->id)->orderby('created_at', 'DESC')->get(); //->where('subject', '<>','added a note')
			$data = array();
			foreach($activities as $activit){
				$admin = Admin::where('id', $activit->created_by)->first();
                /*if($activit->use_for != ""){
                    $receiver = \App\Models\Admin::where('id', $activit->use_for)->first();
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
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
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
			$workflowstage = \App\Models\WorkflowStage::where('w_id', $workflow)->orderby('id','asc')->first();
			$stage = $workflowstage->name;
			$sale_forcast = 0.00;
			$obj = new \App\Models\Application;
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
				$productdetail = \App\Models\Product::where('id', $product)->first();
				$partnerdetail = \App\Models\Partner::where('id', $partner)->first();
				$PartnerBranch = \App\Models\PartnerBranch::where('id', $branch)->first();
				$subject = 'has started an application';
				$objs = new ActivitiesLog;
				$objs->client_id = $request->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '<span class="text-semi-bold">'.@$productdetail->name.'</span><p>'.@$partnerdetail->partner_name.' ('.@$PartnerBranch->name.')</p>';
				$objs->subject = $subject;
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
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
			$applications = \App\Models\Application::where('client_id', $request->id)->orderby('created_at', 'DESC')->get();
			$data = array();
			ob_start();
			foreach($applications as $alist){
				$productdetail = \App\Models\Product::where('id', $alist->product_id)->first();
				$partnerdetail = \App\Models\Partner::where('id', $alist->partner_id)->first();
				$PartnerBranch = \App\Models\PartnerBranch::where('id', $alist->branch)->first();
				$workflow = \App\Models\Workflow::where('id', $alist->workflow)->first();
              
                $application_assign_count = \App\Models\Note::where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->where('application_id',$alist->id)->where('client_id',$request->id)->count();
                //dd($application_assign_count);
				?>
				<tr id="id_<?php echo $alist->id; ?>">
				<td>
                  <a class="openapplicationdetail" data-id="<?php echo $alist->id; ?>" href="javascript:;" style="display:block;">
                  <?php echo @$productdetail->name; ?>
                  <?php  if( $application_assign_count > 0 ) { ?>
                            <span class="countTotalActivityAction" style="background: #1f1655;padding: 0px 5px;border-radius: 50%;color: #fff;margin-left: 5px;"><?php echo $application_assign_count;?></span>
                  <?php } ?>
                  </a> 
                  <small><?php echo @$partnerdetail->partner_name; ?>(<?php echo @$PartnerBranch->name; ?>)</small>
                </td>
				<td><?php echo @$workflow->name; ?></td>
				<td><?php echo @$alist->stage; ?></td>
				<td>
                    <?php if($alist->status == 0){ ?>
                        <span class="ag-label--circular" style="color: #6777ef" >In Progress</span>
                    <?php } else if($alist->status == 1){ ?>
                        <span class="ag-label--circular" style="color: #6777ef" >Completed</span>
                    <?php } else if($alist->status == 2){ ?>
                        <span class="ag-label--circular" style="color: red;" >Discontinued</span>
                    <?php } else if($alist->status == 3){ ?>
                        <span class="ag-label--circular" style="color: red;" >Cancelled</span>
                    <?php } else if($alist->status == 4){ ?>
                        <span class="ag-label--circular" style="color: red;" >Withdrawn</span>
                    <?php } else if($alist->status == 5){ ?>
                        <span class="ag-label--circular" style="color: red;" >Deferred</span>
                    <?php } else if($alist->status == 6){ ?>
                        <span class="ag-label--circular" style="color: red;" >Future</span>
                    <?php } else if($alist->status == 7){ ?>
                        <span class="ag-label--circular" style="color: red;" >VOE</span>
                    <?php } else if($alist->status == 8){ ?>
                            <span class="ag-label--circular" style="color: red;" >Refund</span>
                    <?php } ?>
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
				$obj = \App\Models\Note::find($request->noteid);
			}else{
				$obj = new \App\Models\Note;
			}

			$obj->client_id = $request->client_id;
			$obj->user_id = Auth::user()->id;
			$obj->title = $request->title;
			$obj->description = $request->description;
			$obj->mail_id = $request->mailid;
			$obj->type = $request->vtype;
          	
      		if( isset($request->mobileNumber) && $request->mobileNumber != ""){
            	$obj->mobile_number = $request->mobileNumber; // Add this line
        	}
      
			$obj->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
			$obj->folloup = 0; // Required NOT NULL field (0 = not a followup, 1 = followup)
			$obj->status = 0; // Required NOT NULL field (0 = active/open, 1 = closed/completed)
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
                  
					if( isset($request->mobileNumber) && $request->mobileNumber != ""){
                        $objs->description = '<span class="text-semi-bold">'.$request->title.'</span><p>'.$request->description.'</p><p>'.$request->mobileNumber.'</p>';
                    } else {
                        $objs->description = '<span class="text-semi-bold">'.$request->title.'</span><p>'.$request->description.'</p>';
                    }
                  
					$objs->subject = $subject;
					$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
					$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
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
		if(\App\Models\Note::where('id',$note_id)->exists()){
			$data = \App\Models\Note::select('title','description')->where('id',$note_id)->first();
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
		if(\App\Models\Note::where('id',$note_id)->exists()){
			$data = \App\Models\Note::select('title','description','user_id','updated_at')->where('id',$note_id)->first();
			$admin = \App\Models\Admin::where('id', $data->user_id)->first();
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
		if(\App\Models\ApplicationActivitiesLog::where('type','note')->where('id',$note_id)->exists()){
			$data = \App\Models\ApplicationActivitiesLog::select('title','description','user_id','updated_at')->where('type','note')->where('id',$note_id)->first();
			$admin = \App\Models\Admin::where('id', $data->user_id)->first();
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

		$notelist = \App\Models\Note::where('client_id',$client_id)->whereNull('assigned_to')->whereNull('task_group')->where('type',$type)->orderby('pin', 'DESC')->orderByRaw('created_at DESC NULLS LAST')->get();
		ob_start();
		foreach($notelist as $list){
			$admin = \App\Models\Admin::where('id', $list->user_id)->first();
			?>
			<div class="note_col" id="note_id_<?php echo $list->id; ?>">
				<div class="note_content">
					<h4><a class="viewnote" data-id="<?php echo $list->id; ?>" href="javascript:;"><?php echo @$list->title == "" ? config('constants.empty') : str_limit(@$list->title, '19', '...'); ?> </a></h4>
					<?php if($list->pin == 1){
									?><div class="pined_note"><i class="fa fa-thumbtack"></i></i></div><?php } ?>
				</div>
				<div class="extra_content">
				    <p><?php echo @$list->description; ?></p>
                  
                    <?php if( isset($list->mobile_number) && $list->mobile_number != ""){ ?>
                        <p><?php echo @$list->mobile_number; ?></p>
                    <?php } ?>
                  
					<div class="left">
						<div class="author">
							<a href="<?php echo \URL::to('/users/view/'.$admin->id); ?>"><?php echo substr($admin->first_name, 0, 1); ?></a>
						</div>
						<div class="note_modify">
							<small>Last Modified <span><?php echo date('d/m/Y h:i A', strtotime($list->updated_at)); ?></span></small>
							<?php echo $admin->first_name.' '.$admin->last_name; ?>
						</div>
					</div>
					<div class="right">
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
				</div>
			</div>
			<?php
		}
		return ob_get_clean();
	}

	public function deletenote(Request $request){
		$note_id = $request->note_id;
		if(\App\Models\Note::where('id',$note_id)->exists()){
			$data = \App\Models\Note::select('client_id','title','description')->where('id',$note_id)->first();
			$res = DB::table('notes')->where('id', @$note_id)->delete();
			if($res){
				if($data == 'client'){
				$subject = 'deleted a note';

				$objs = new ActivitiesLog;
				$objs->client_id = $data->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '<span class="text-semi-bold">'.$data->title.'</span><p>'.$data->description.'</p>';
				$objs->subject = $subject;
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
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
			if(\App\Models\InterestedService::where('client_id', $request->client_id)->where('partner', $request->partner)->where('product', $request->product)->exists()){
				$response['status'] 	= 	false;
				$response['message']	=	'This interested service already exists.';
			}else{
				$obj = new \App\Models\InterestedService;
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

					$partnerdetail = \App\Models\Partner::where('id', $request->partner)->first();
					$PartnerBranch = \App\Models\PartnerBranch::where('id', $request->branch)->first();
					$objs = new ActivitiesLog;
					$objs->client_id = $request->client_id;
					$objs->created_by = Auth::user()->id;
					$objs->description = '<span class="text-semi-bold">'.$PartnerBranch->name.'</span><p>'.$partnerdetail->name.'</p>';
					$objs->subject = $subject;
					$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
					$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
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
		$inteservices = \App\Models\InterestedService::where('client_id',$client_id)->orderby('created_at', 'DESC')->get();
		foreach($inteservices as $inteservice){
			$workflowdetail = \App\Models\Workflow::where('id', $inteservice->workflow)->first();
			 $productdetail = \App\Models\Product::where('id', $inteservice->product)->first();
			$partnerdetail = \App\Models\Partner::where('id', $inteservice->partner)->first();
			$PartnerBranch = \App\Models\PartnerBranch::where('id', $inteservice->branch)->first();
			$admin = \App\Models\Admin::where('id', $inteservice->user_id)->first();
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

			$totl = 0.00;
			$net = 0.00;
			$discount = 0.00;
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
                   $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
                   $fileExtension = $file->getClientOriginalExtension();
                   //echo $nameWithoutExtension."===".$fileExtension;
				    $explodeFileName = explode('.', $fileName);
		        	$document_upload = $this->uploadrenameFile($file, Config::get('constants.documents'));
		        	$exploadename = explode('.', $document_upload);
		        	$obj = new \App\Models\Document;
        		    $obj->file_name = $nameWithoutExtension; //$explodeFileName[0];
                	$obj->filetype = $fileExtension; //$exploadename[1];
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
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
				$objs->save();

				}
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully uploaded your document';
				$fetchd = \App\Models\Document::where('client_id',$id)->where('doc_type',$doctype)->where('type',$request->type)->orderby('created_at', 'DESC')->get();
				ob_start();
				foreach($fetchd as $fetch){
					$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                  
                    if( isset($doctype) && $doctype == 'migration'){
                        $preview_container_type = 'preview-container-migrationdocumentlist';
                    } else if( isset($doctype) && $doctype == 'education'){
                        $preview_container_type = 'preview-container-documentlist';
                    }
					?>
					<tr class="drow" id="id_<?php echo $fetch->id; ?>">
						<td style="white-space: initial;">
                            <div data-id="<?php echo $fetch->id; ?>" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
								<a style="white-space: initial;" href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset('img/documents/'.$fetch->myfile); ?>','<?php echo $preview_container_type;?>')">
                                    <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                </a>
							</div>
                        </td>
						<td style="white-space: initial;"><?php echo $admin->first_name; ?></td>

						<td style="white-space: initial;"><?php echo date('d/m/Y', strtotime($fetch->created_at)); ?></td>
						<td>
							<div class="dropdown d-inline">
								<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
								<div class="dropdown-menu">
									<a class="dropdown-item renamedoc" href="javascript:;">Rename</a>
									<a target="_blank" class="dropdown-item" href="<?php echo asset('img/documents'); ?>/<?php echo $fetch->myfile; ?>">Preview</a>
									<?php
																$explodeimg = explode('.',$fetch->myfile);
										if($explodeimg[1] == 'jpg'|| $explodeimg[1] == 'png'|| $explodeimg[1] == 'jpeg'){
																?>
																	<a target="_blank" class="dropdown-item" href="<?php echo \URL::to('/document/download/pdf'); ?>/<?php echo $fetch->id; ?>">PDF</a>
																	<?php } ?>
									<a download class="dropdown-item" href="<?php echo asset('img/documents'); ?>/<?php echo $fetch->myfile; ?>">Download</a>

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
										<a class="dropdown-item" href="<?php echo asset('img/documents'); ?>/<?php echo $fetch->myfile; ?>">Preview</a>
										<a download class="dropdown-item" href="<?php echo asset('img/documents'); ?>/<?php echo $fetch->myfile; ?>">Download</a>
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

		if(\App\Models\InterestedService::where('client_id',$clientid)->where('id',$id)->exists()){
			$app = \App\Models\InterestedService::where('client_id',$clientid)->where('id',$id)->first();
			$workflow = $app->workflow;
			$workflowstage = \App\Models\WorkflowStage::where('w_id', $workflow)->orderby('id','ASC')->first();
			$partner = $app->partner;
			$branch = $app->branch;
			$product = $app->product;
			$client_id = $request->client_id;
			$status = 0;
			$stage = $workflowstage->name;
			$sale_forcast = 0.00;
			$obj = new \App\Models\Application;
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


			$app = \App\Models\InterestedService::find($id);
			$app->status = 1;
			$saved = $app->save();
			if($saved){
				$productdetail = \App\Models\Product::where('id', $product)->first();
				$partnerdetail = \App\Models\Partner::where('id', $partner)->first();
				$PartnerBranch = \App\Models\PartnerBranch::where('id', $branch)->first();
				$subject = 'has started an application';
				$objs = new ActivitiesLog;
				$objs->client_id = $request->clientid;
				$objs->created_by = Auth::user()->id;
				$objs->description = '<span class="text-semi-bold">'.@$productdetail->name.'</span><p>'.@$partnerdetail->partner_name.' ('.@$PartnerBranch->name.')</p>';
				$objs->subject = $subject;
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
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
		if(\App\Models\InterestedService::where('id',$note_id)->exists()){
			$data = \App\Models\InterestedService::where('id',$note_id)->first();
			$res = DB::table('interested_services')->where('id', @$note_id)->delete();
			if($res){
				$productdetail = \App\Models\Product::where('id', $data->product)->first();
				$partnerdetail = \App\Models\Partner::where('id', $data->partner)->first();
				$PartnerBranch = \App\Models\PartnerBranch::where('id', $data->branch)->first();
				$subject = 'deleted an interested service';

				$objs = new ActivitiesLog;
				$objs->client_id = $data->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '<span class="text-semi-bold">'.@$productdetail->name.'</span><p>'.@$partnerdetail->partner_name.' ('.@$PartnerBranch->name.')</p>';
				$objs->subject = $subject;
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
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
		if(\App\Models\Document::where('id',$id)->exists()){
			$doc = \App\Models\Document::where('id',$id)->first();
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

		if(\App\Models\Admin::where('id',$id)->exists()){
		    $tagg = $request->tag;
		    $tag = array();
		    foreach($tagg as $tg){
		        $stagd = \App\Models\Tag::where('name','=',$tg)->first();
		        if($stagd){

		        }else{
		            $stagds = \App\Models\Tag::where('id','=',$tg)->first();
		            if($stagds){
		                $tag[] = $stagds->id;
		            }else{
		                $o = new \App\Models\Tag;
		                $o->name = $tg;
		                $o->save();
		                $tag[] = $o->id;
		            }

		        }
		    }
			$obj = \App\Models\Admin::find($id);
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

		if(\App\Models\Document::where('id',$note_id)->exists()){

			$data = DB::table('documents')->where('id', @$note_id)->first();
			$res = DB::table('documents')->where('id', @$note_id)->delete();

			if($res){

				$subject = 'deleted a document';

				$objs = new ActivitiesLog;
				$objs->client_id = $data->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '';
				$objs->subject = $subject;
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
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

	// Appointment functionality removed - tables dropped in migration
	public function addAppointment(Request $request){
		return response()->json(['status' => false, 'message' => 'Appointment functionality has been removed']);
    }


	// Appointment functionality removed - tables dropped in migration
	public function editappointment(Request $request){
		return response()->json(['status' => false, 'message' => 'Appointment functionality has been removed']);
	}
  
  	// Appointment functionality removed - tables dropped in migration
	public function updateappointmentstatus(Request $request, $status = Null, $id = Null){
		return redirect()->back()->with('error', 'Appointment functionality has been removed');
	}
	
  
    // Appointment functionality removed - tables dropped in migration
    public function updatefollowupschedule(Request $request){
        return redirect()->back()->with('error', 'Appointment functionality has been removed');
    }
  	
	// Appointment functionality removed - tables dropped in migration
	public function getAppointments(Request $request){
		return response('<div class="row"><div class="col-md-12"><p class="text-muted">Appointment functionality has been removed.</p></div></div>', 200);
	}

	// Appointment functionality removed - tables dropped in migration
	public function getAppointmentdetail(Request $request){
		return response('Appointment functionality has been removed', 404);
	}


	// Appointment functionality removed - tables dropped in migration
	public function deleteappointment(Request $request){
		return response()->json(['status' => false, 'message' => 'Appointment functionality has been removed']);
	}


	public function editinterestedService(Request $request){
		if(Admin::where('role', '=', '7')->where('id', $request->client_id)->exists()){

			$obj = \App\Models\InterestedService::find($request->id);
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

				$partnerdetail = \App\Models\Partner::where('id', $request->partner)->first();
				$PartnerBranch = \App\Models\PartnerBranch::where('id', $request->branch)->first();
				$objs = new ActivitiesLog;
				$objs->client_id = $request->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '<span class="text-semi-bold">'.$PartnerBranch->name.'</span><p>'.$partnerdetail->name.'</p>';
				$objs->subject = $subject;
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
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
		$obj = \App\Models\InterestedService::find($request->id);
		if($obj){
			?>
			<form method="post" action="<?php echo \URL::to('/edit-interested-service'); ?>" name="editinter_servform" autocomplete="off" id="editinter_servform" enctype="multipart/form-data">
				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
				<input type="hidden" name="client_id" value="<?php echo $obj->client_id; ?>">
				<input type="hidden" name="id" value="<?php echo $obj->id; ?>">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="edit_intrested_workflow">Select Workflow <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control workflowselect2" id="edit_intrested_workflow" name="workflow">
									<option value="">Please Select a Workflow</option>
									<?php foreach(\App\Models\Workflow::all() as $wlist){
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
									<?php foreach(\App\Models\Partner::where('service_workflow', $obj->workflow)->orderby('created_at', 'DESC')->get() as $plist){
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
									<?php foreach(\App\Models\Product::where('partner', $obj->partner)->orderby('created_at', 'DESC')->get() as $pplist){
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
		$pro = \App\Models\Product::where('id', $catid)->first();
		if($pro){
		$user_array = explode(',',$pro->branches);
		$lists = \App\Models\PartnerBranch::WhereIn('id',$user_array)->Where('partner_id',$pro->partner)->orderby('name','ASC')->get();

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
		$obj = \App\Models\InterestedService::find($request->id);
		if($obj){
			$workflowdetail = \App\Models\Workflow::where('id', $obj->workflow)->first();
			 $productdetail = \App\Models\Product::where('id', $obj->product)->first();
			$partnerdetail = \App\Models\Partner::where('id', $obj->partner)->first();
			$PartnerBranch = \App\Models\PartnerBranch::where('id', $obj->branch)->first();
			$admin = \App\Models\Admin::where('id', $obj->user_id)->first();
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
							<div class="clearfix"></div>
						</div>
						<?php
						$totl = 0.00;
						$discount = 0.00;
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
			$obj = \App\Models\InterestedService::find($request->fapp_id);
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
			if(\App\Models\Application::where('client_id', $client_id)->where('product_id', $product)->where('partner_id', $partner)->exists()){
				$response['status'] 	= 	false;
				$response['message']	=	'Application to the product already exists for this client.';
			}else{
				$obj = new \App\Models\Application;
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
					$productdetail = \App\Models\Product::where('id', $product)->first();
					$partnerdetail = \App\Models\Partner::where('id', $partner)->first();
					$PartnerBranch = \App\Models\PartnerBranch::where('id', $branch)->first();
					$subject = 'has started an application';
					$objs = new ActivitiesLog;
					$objs->client_id = $request->client_id;
					$objs->created_by = Auth::user()->id;
					$objs->description = '<span class="text-semi-bold">'.@$productdetail->name.'</span><p>'.@$partnerdetail->partner_name.' ('.@$PartnerBranch->name.')</p>';
					$objs->subject = $subject;
					$objs->task_status = 0; // Required NOT NULL field for PostgreSQL (0 = activity, 1 = task)
					$objs->pin = 0; // Required NOT NULL field for PostgreSQL (0 = not pinned, 1 = pinned)
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



	public function pinnote(Request $request){
		$requestData = $request->all();

		if(\App\Models\Note::where('id',$requestData['note_id'])->exists()){
			$note = \App\Models\Note::where('id',$requestData['note_id'])->first();
			if($note->pin == 0){
				$obj = \App\Models\Note::find($note->id);
				$obj->pin = 1;
				$saved = $obj->save();
			}else{
				$obj = \App\Models\Note::find($note->id);
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
        /*if(\App\Models\Note::where('client_id',$requestData['client_id'])->where('assigned_to',$requestData['rem_cat'])->exists())
        {
            // return redirect()->back()->with('error', 'Lead already assigned');
            // return Redirect::to('/admin/assignee')->with('error', 'Lead already assigned');
            echo json_encode(array('success' => false, 'message' => 'Lead already assigned to '.@$requestData['assignee_name'], 'clientID' => $requestData['client_id']));
            exit;
        }*/

        $followup 				= new \App\Models\Note;
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

			$o = new \App\Models\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = @$requestData['rem_cat'];
	    	$o->module_id = $this->decodeString(@$requestData['client_id']);
	    	$o->url = route('clients.detail', @$requestData['client_id']);
	    	$o->notification_type = 'client';
	    	$o->message = 'Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name.' '.date('d/M/Y h:i A',strtotime($Lead->followup_date));
	    	$o->seen = 0; // Set seen to 0 (unseen) for new notifications
	    	$o->save();

			$objs = new ActivitiesLog;
            $objs->client_id = $this->decodeString(@$requestData['client_id']);
            $objs->created_by = Auth::user()->id;
            //$objs->subject = 'Followup set for '.date('d/M/Y h:i A',strtotime($Lead->followup_date));
            $objs->subject = 'set action for '.@$requestData['assignee_name'];
            $objs->description = '<span class="text-semi-bold">'.@$requestData['remindersubject'].'</span><p>'.@$requestData['description'].'</p>';
            $objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
            $objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
            if(Auth::user()->id != @$requestData['rem_cat']){
                $objs->use_for = @$requestData['rem_cat'];
            } else {
                $objs->use_for = null; // Use null instead of empty string for PostgreSQL
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
        /*if(\App\Models\Note::where('client_id',$requestData['client_id'])->where('assigned_to',$requestData['rem_cat'])->exists())
        {
            // return redirect()->back()->with('error', 'Lead already assigned');
            // return Redirect::to('/admin/assignee')->with('error', 'Lead already assigned');
            echo json_encode(array('success' => false, 'message' => 'Lead already assigned to '.@$requestData['assignee_name'], 'clientID' => $requestData['client_id']));
            exit;
        }*/

        $followup = \App\Models\Note::where('id', '=', $requestData['note_id'])->first();
        
        if(!$followup) {
            echo json_encode(array('success' => false, 'message' => 'Note not found', 'clientID' => $requestData['client_id']));
            exit;
        }
        
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
			$Lead = null;
			if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
                $Lead = Admin::find($this->decodeString($requestData['client_id']));
                if($Lead){
                    $Lead->followup_date = @$requestData['followup_datetime'];
                    $Lead->save();
                }
			}

			$o = new \App\Models\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = @$requestData['rem_cat'];
	    	$o->module_id = $this->decodeString(@$requestData['client_id']);
	    	$o->url = route('clients.detail', @$requestData['client_id']);
	    	$o->notification_type = 'client';
	    	$followupDateText = ($Lead && $Lead->followup_date) ? date('d/M/Y h:i A', strtotime($Lead->followup_date)) : (isset($requestData['followup_datetime']) ? date('d/M/Y h:i A', strtotime($requestData['followup_datetime'])) : '');
	    	$o->message = 'Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name.($followupDateText ? ' '.$followupDateText : '');
	    	$o->seen = 0; // Set seen to 0 (unseen) for new notifications
	    	$o->save();

			$objs = new ActivitiesLog;
            $objs->client_id = $this->decodeString(@$requestData['client_id']);
            $objs->created_by = Auth::user()->id;
            //$objs->subject = 'Followup set for '.date('d/M/Y h:i A',strtotime($Lead->followup_date));
            $objs->subject = 'set action for '.@$requestData['assignee_name'];
            $objs->description = '<span class="text-semi-bold">'.@$requestData['remindersubject'].'</span><p>'.@$requestData['description'].'</p>';
            $objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
            $objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
            if(Auth::user()->id != @$requestData['rem_cat']){
                $objs->use_for = @$requestData['rem_cat'];
            } else {
                $objs->use_for = null; // Use null instead of empty string for PostgreSQL
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
        /*if(\App\Models\Note::where('client_id',$requestData['client_id'])->where('assigned_to',$requestData['rem_cat'])->exists())
        {
            echo json_encode(array('success' => false, 'message' => 'Lead already assigned to '.@$requestData['assignee_name'], 'clientID' => $requestData['client_id']));
            exit;
        }*/

        $followup = \App\Models\Note::where('id', '=', $requestData['note_id'])->first();
        
        if(!$followup) {
            echo json_encode(array('success' => false, 'message' => 'Note not found', 'clientID' => $requestData['client_id']));
            exit;
        }
        
        //$followup 				= new \App\Models\Note;
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
			$Lead = null;
			if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
                $Lead = Admin::find($this->decodeString($requestData['client_id']));
                if($Lead) {
                    $Lead->followup_date = @$requestData['followup_datetime'];
                    $Lead->save();
                }
			}

			$o = new \App\Models\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = @$requestData['rem_cat'];
	    	$o->module_id = $this->decodeString(@$requestData['client_id']);
	    	$o->url = route('clients.detail', @$requestData['client_id']);
	    	$o->notification_type = 'client';
	    	$followupDateText = ($Lead && $Lead->followup_date) ? date('d/M/Y h:i A', strtotime($Lead->followup_date)) : (isset($requestData['followup_datetime']) ? date('d/M/Y h:i A', strtotime($requestData['followup_datetime'])) : '');
	    	$o->message = 'Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name.($followupDateText ? ' '.$followupDateText : '');
	    	$o->seen = 0; // Set seen to 0 (unseen) for new notifications
	    	$o->save();

			$objs = new ActivitiesLog;
            $objs->client_id = $this->decodeString(@$requestData['client_id']);
            $objs->created_by = Auth::user()->id;
            //$objs->subject = 'Followup set for '.date('d/M/Y h:i A',strtotime($Lead->followup_date));
            $objs->subject = 'Update task for '.@$requestData['assignee_name'];
            $objs->description = '<span class="text-semi-bold">'.@$requestData['remindersubject'].'</span><p>'.@$requestData['description'].'</p>';
            $objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
            $objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
            if(Auth::user()->id != @$requestData['rem_cat']){
                $objs->use_for = @$requestData['rem_cat'];
            } else {
                $objs->use_for = null; // Use null instead of empty string for PostgreSQL
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
        
        // Debug logging (remove in production)
        \Log::info('personalfollowup request data: ', $requestData);

        $client_id = null;
        $req_clientID = "";
        
        if( isset($requestData['client_id']) && $requestData['client_id'] != ''){
            // Check if client_id contains "/" (encoded format)
            if(strpos($requestData['client_id'],"/") !== false){
                $req_client_arr = explode("/",$requestData['client_id']);
                if(!empty($req_client_arr)){
                    $req_clientID = $req_client_arr[0];
                    $client_id = $this->decodeString($req_clientID);
                    if($client_id === false){
                        $client_id = null;
                    }
                }
            } 
            // Check if client_id is a raw integer (from Select2)
            elseif(is_numeric($requestData['client_id'])){
                $client_id = (int)$requestData['client_id'];
                $req_clientID = $requestData['client_id'];
            }
            // Try to decode if it's an encoded string without "/"
            else {
                $decoded = $this->decodeString($requestData['client_id']);
                if($decoded !== false){
                    $client_id = $decoded;
                    $req_clientID = $requestData['client_id'];
                }
            }
        }
        
        \Log::info('personalfollowup parsed client_id: ' . $client_id);

        // Validate that client_id was successfully parsed
        if($client_id === null || $client_id === ''){
            \Log::error('personalfollowup: Invalid client_id. Request: ' . json_encode($requestData));
            echo json_encode(array('success' => false, 'message' => 'Invalid client ID. Please select a valid client.', 'clientID' => $req_clientID));
            exit;
        }

        /*if(\App\Models\Note::where('client_id',$requestData['client_id'])->where('assigned_to',$requestData['rem_cat'])->exists())
        {
            echo json_encode(array('success' => false, 'message' => 'Lead already assigned to '.@$requestData['assignee_name'], 'clientID' => $req_clientID));
            exit;
        }*/
		$followup 					= new \App\Models\Note;
		$followup->client_id		= $client_id;//$this->decodeString(@$requestData['client_id']);
		$followup->user_id			= Auth::user()->id;
		$followup->description		= @$requestData['description'];
		$followup->title		    = @$requestData['remindersubject'] ?? 'Personal Task assign to '.@$requestData['assignee_name'];
		$followup->folloup	= 1;
        $followup->task_group = @$requestData['task_group'];
		$followup->assigned_to	= @$requestData['rem_cat'];
		$followup->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
		$followup->status = 0; // Required NOT NULL field (0 = active/open, 1 = closed/completed)
		$followup->type = 'client'; // Required field - mark as client type
		if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
		    $followup->followup_date	=  @$requestData['followup_datetime'];
		}
        try {
            $saved	=	$followup->save();
        } catch (\Exception $e) {
            \Log::error('Error saving followup in personalfollowup: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());
            echo json_encode(array('success' => false, 'message' => 'Error saving action: ' . $e->getMessage(), 'clientID' => $client_id));
            exit;
        }
        
        if(!$saved)
		{
			echo json_encode(array('success' => false, 'message' => 'Please try again', 'clientID' => $client_id)); //$requestData['client_id']
		}
		else
		{
			// Validate receiver_id before creating notification
			if(isset($requestData['rem_cat']) && $requestData['rem_cat'] != ''){
				$o = new \App\Models\Notification;
				$o->sender_id = Auth::user()->id;
				$o->receiver_id = $requestData['rem_cat'];
				$o->module_id = $client_id; // Use the parsed client_id (integer)
				$o->url = route('clients.detail', base64_encode(convert_uuencode($client_id)));
				$o->notification_type = 'client';
				// Safely format date
				$followupDateText = '';
				if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
					$timestamp = strtotime($requestData['followup_datetime']);
					if($timestamp !== false){
						$followupDateText = ' '.date('d/M/Y h:i A', $timestamp);
					}
				}
				$o->message = 'Personal Task Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name.$followupDateText;
				$o->seen = 0; // Set seen to 0 (unseen) for new notifications
				$o->save();
			}

			$objs = new ActivitiesLog;
            $objs->client_id = $client_id;//$this->decodeString(@$requestData['client_id']);
            $objs->created_by = Auth::user()->id;
            $objs->subject = 'set action for '.@$requestData['assignee_name'];
            $objs->description = '<span class="text-semi-bold">'.@$requestData['remindersubject'].'</span><p>'.@$requestData['description'].'</p>';
            if(Auth::user()->id != @$requestData['rem_cat']){
                $objs->use_for = @$requestData['rem_cat'];
            } else {
                $objs->use_for = null; // Use null instead of empty string for PostgreSQL
            }
            $objs->followup_date = @$requestData['followup_datetime'];
            $objs->task_group = @$requestData['task_group'];
            $objs->task_status = 0; // Required NOT NULL field for PostgreSQL (0 = activity, 1 = task)
            $objs->pin = 0; // Required NOT NULL field for PostgreSQL (0 = not pinned, 1 = pinned)
            $objs->save();
			echo json_encode(array('success' => true, 'message' => 'successfully saved', 'clientID' => $client_id)); //$requestData['client_id']
			exit;
		}
	}
	
	
	public function retagfollowup(Request $request){
	    $requestData 		= 	$request->all();

        //	echo '<pre>'; print_r($requestData); die;
		$followup 					= new \App\Models\Note;
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
		   /*$objnote =  \App\Models\Note::find();
		   $objnote->status = 1;
		   $objnote->save();*/
		    $newassignee = Admin::find($requestData['changeassignee']);
			$o = new \App\Models\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = @$requestData['changeassignee'];
	    	$o->module_id = @$requestData['client_id'];
	    	$o->url = route('clients.detail', @$requestData['client_id']);
	    	$o->notification_type = 'client';
	    	$o->message = 'Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name;
	    	$o->seen = 0; // Set seen to 0 (unseen) for new notifications
	    	$o->save();

			$objs = new ActivitiesLog;
				$objs->client_id = @$requestData['client_id'];
				$objs->created_by = Auth::user()->id;
				$objs->subject = Auth::user()->first_name.' '.Auth::user()->last_name.' tags work to '.$newassignee->first_name.' '.$newassignee->last_name;
				$objs->description = @$requestData['message'];
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
				$objs->save();
		return Redirect::to('/admin/followup-dates')->with('success', 'Record Updated successfully');
		}
	}

		/*public function change_assignee(Request $request){
    		$objs = Admin::find($request->id);
    		$objs->assignee = $request->assinee;

    		$saved = $objs->save();

    		if($saved){
    		    $o = new \App\Models\Notification;
    	    	$o->sender_id = Auth::user()->id;
    	    	$o->receiver_id = $request->assinee;
    	    	$o->module_id = $request->id;
    	    	$o->url = route('clients.detail', base64_encode(convert_uuencode(@$request->id)));
    	    	$o->notification_type = 'client';
    	    	$o->message = 'Client Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name;
    	    	$o->seen = 0; // Set seen to 0 (unseen) for new notifications
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
                $assineeCount = count($request->assinee);
                if( $assineeCount < 1){
                    $objs->assignee = "";
                } else if( $assineeCount == 1){
                    $objs->assignee = $request->assinee[0];
                } else if( $assineeCount > 1){
                    $objs->assignee = implode(",",$request->assinee);
                }
            }
    		//$objs->assignee = $request->assinee;
            $saved = $objs->save();
            if($saved){
                if ( is_array($request->assinee) && count($request->assinee) >=1) {
                    $assigneeArr = $request->assinee;
                    foreach($assigneeArr as $key=>$val) {
                        $o = new \App\Models\Notification;
                        $o->sender_id = Auth::user()->id;
                        $o->receiver_id = $val; //$request->assinee;
                        $o->module_id = $request->id;
                        $o->url = route('clients.detail', base64_encode(convert_uuencode(@$request->id)));
                        $o->notification_type = 'client';
                        $o->message = 'Client Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name;
                        $o->seen = 0; // Set seen to 0 (unseen) for new notifications
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


	public function uploadmail(Request $request){
		$requestData 		= 	$request->all();
		//$obj		= 	Admin::find(@$requestData['id']);
		$obj				= 	new \App\Models\MailReport;
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

                $notelist1 = Note::where('client_id', $merge_record_ids_arr[0])->whereNull('assigned_to')->where('type', 'client')->orderby('pin', 'DESC')->orderByRaw('created_at DESC NULLS LAST')->get();
                //dd($notelist1);

                $notelist2 = Note::where('client_id', $merge_record_ids_arr[1])->whereNull('assigned_to')->where('type', 'client')->orderby('pin', 'DESC')->orderByRaw('created_at DESC NULLS LAST')->get();
                //dd($notelist2);

                if(!empty($notelist2)){
                    foreach($notelist2 as $key2=>$list2){
                        $obj1 = new \App\Models\Note;
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
                        $obj2 = new \App\Models\Note;
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

            //appointments - REMOVED: appointments table deleted
            // Appointment merge functionality disabled - appointments table no longer exists
            /*
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
            */
  
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

            //appointments - REMOVED: appointments table deleted
            // Appointment merge functionality disabled - appointments table no longer exists
            /*
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
            */


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
        
        // Geocoding disabled - returning empty response
        $response['status'] 	= 	0;
        $response['postal_code'] = 	"";
        $response['locality']    = 	"";
        $response['message']	=	"Geocoding feature disabled.";
        echo json_encode($response);
        
        /*
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
        */
    }
  
  
    //not picked call button click
    public function notpickedcall(Request $request){
        $data = $request->all(); //dd($data);
        //Get user Phone no and send message via cellcast
        $userInfo = Admin::select('id','country_code','phone')->where('id', $data['id'])->first();//dd($userInfo);
        if ( $userInfo) {
            //$message = 'Call not picked.SMS sent successfully!';
            $message = $data['message'];
            $userPhone = $userInfo->country_code."".$userInfo->phone;
            $this->smsService->sendSms($userPhone,$message);
        }
        $recExist = Admin::where('id', $data['id'])->update(['not_picked_call' => $data['not_picked_call']]);
        if($recExist){
            if($data['not_picked_call'] == 1){ //if checked true
                $objs = new ActivitiesLog;
                $objs->client_id = $data['id'];
                $objs->created_by = Auth::user()->id;
                $objs->description = '<span class="text-semi-bold">Call not picked.SMS sent successfully!</span>';
                //$objs->subject = "Call not picked";
                $objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
                $objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
                $objs->save();

                $response['status'] 	= 	true;
                $response['message']	=	'Call not picked.SMS sent successfully!';
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
		if(\App\Models\ActivitiesLog::where('id',$activitylogid)->exists()){
			$data = \App\Models\ActivitiesLog::select('client_id','subject','description')->where('id',$activitylogid)->first();
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
        if(\App\Models\ActivitiesLog::where('id',$requestData['activity_id'])->exists()){
			$activity = \App\Models\ActivitiesLog::where('id',$requestData['activity_id'])->first();
			if($activity->pin == 0){
				$obj = \App\Models\ActivitiesLog::find($activity->id);
				$obj->pin = 1;
				$saved = $obj->save();
			}else{
				$obj = \App\Models\ActivitiesLog::find($activity->id);
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
        if( \App\Models\Admin::where('id',$id)->exists() ) {
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
            $tags_total = \App\Models\Tag::select('id','name')->where('name', 'LIKE', '%'.$squery.'%')->count();
            $tags = \App\Models\Tag::select('id','name')->where('name', 'LIKE', '%'.$squery.'%')->paginate(20);

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

                $client_info = \App\Models\Admin::select('client_id')->where('id', $requestData['client_id'])->first(); //dd($admin);
                if(!empty($client_info)){
                    $client_unique_id = $client_info->client_id;
                } else {
                    $client_unique_id = "";
                }

                $doctype = isset($request->doctype)? $request->doctype : '';

                foreach ($files as $file) {
                    $size = $file->getSize();
                    $fileName = $file->getClientOriginalName();
                    $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
                    $fileExtension = $file->getClientOriginalExtension();
                    //echo $nameWithoutExtension."===".$fileExtension;
                    //$explodeFileName = explode('.', $fileName);
                  
                    $name = time() . $file->getClientOriginalName();
                  
                    $filePath = $client_unique_id.'/'.$doctype.'/'. $name;
                    Storage::disk('s3')->put($filePath, file_get_contents($file));
                  
                    //$exploadename = explode('.', $name);

                    $obj = new \App\Models\Document;
                    $obj->file_name = $nameWithoutExtension; //$explodeFileName[0];
                    $obj->filetype = $fileExtension; //$exploadename[1];
                    $obj->user_id = Auth::user()->id;
                  
                     // Get the full URL of the uploaded file
                    $fileUrl = Storage::disk('s3')->url($filePath);
                    $obj->myfile = $fileUrl;
                    $obj->myfile_key = $name;

                    $obj->client_id = $requestData['client_id'];
                    $obj->type = $request->type;
                    $obj->file_size = $size;
                    $obj->doc_type = $doctype;
                    $doc_saved = $obj->save();

                    $insertedDocId = $obj->id;
                } //end foreach

               
            } else {
                $insertedDocId = null;
                $doc_saved = "";
            }

            if(isset($requestData['trans_date'])){
                //Generate unique receipt id
                /*$is_record_exist = DB::table('account_client_receipts')->select('receipt_id')->where('receipt_type',1)->orderBy('receipt_id', 'desc')->first();
                //dd($is_record_exist);
                if(!$is_record_exist){
                    $receipt_id = 1;
                } else {
                    $receipt_id = $is_record_exist->receipt_id;
                    $receipt_id = $receipt_id +1;
                } */ //dd($receipt_id);
              
                $finalArr = array();
                for($i=0; $i<count($requestData['trans_date']); $i++){
                    $finalArr[$i]['trans_date'] = $requestData['trans_date'][$i];
                    $finalArr[$i]['entry_date'] = $requestData['entry_date'][$i];
                    //$finalArr[$i]['trans_no'] = $requestData['trans_no'][$i];
                    $finalArr[$i]['payment_method'] = $requestData['payment_method'][$i];
                    $finalArr[$i]['description'] = $requestData['description'][$i];
                    $finalArr[$i]['deposit_amount'] = $requestData['deposit_amount'][$i];

                    $saved	= DB::table('account_client_receipts')->insertGetId([
                        'user_id' => $requestData['loggedin_userid'],
                        'client_id' =>  $requestData['client_id'],
                        //'receipt_id'=>  $receipt_id,
                        'receipt_type' => $requestData['receipt_type'],
                        'trans_date' => $requestData['trans_date'][$i],
                        'entry_date' => $requestData['entry_date'][$i],
                        //'trans_no' => $requestData['trans_no'][$i],
                        'payment_method' => $requestData['payment_method'][$i],
                        'description' => $requestData['description'][$i],
                        'deposit_amount' => $requestData['deposit_amount'][$i],
                        'uploaded_doc_id'=> $insertedDocId
                    ]);
                }
            }
            //echo '<pre>'; print_r($finalArr); die;
            if($saved) {
                //Update transaction no
                $requestData['trans_no'][0] = "Rec".$saved;
                $finalArr[0]['trans_no'] = "Rec".$saved;
                $receipt_id = $saved;
                DB::table('account_client_receipts')->where('id',$saved)->update(['trans_no' => $requestData['trans_no'][0],'receipt_id'=>$receipt_id]);
                $response['status'] = true;
                $response['requestData'] = $finalArr;
                $response['lastInsertedId'] = $saved;
                $response['function_type'] = $requestData['function_type'];

                //Get total deposit amount
                $db_total_deposit_amount = DB::table('account_client_receipts')->where('client_id',$requestData['client_id'])->where('receipt_type',1)->sum('deposit_amount');
                $response['db_total_deposit_amount'] = $db_total_deposit_amount; //dd($db_total_deposit_amount );
              
                $validate_receipt_info = DB::table('account_client_receipts')->select('validate_receipt')->where('id',$saved)->first();
                $response['validate_receipt'] = $validate_receipt_info->validate_receipt;

                if($doc_saved){
                    //Get AWS Url link
                    //$url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                    //$awsUrl = $url.$client_unique_id.'/'.$doctype.'/'.$name; //dd($awsUrl);
                    $awsUrl = $fileUrl;
                    
                    $response['awsUrl'] = $awsUrl;
                    $response['message'] = 'Client receipt with document added successfully';
					$subject = 'added client receipt with Receipt Id-'.$receipt_id.' and Trans. No	-'.$requestData['trans_no'][0].' and document' ;
                } else {
                    $response['message'] = 'Client receipt added successfully';
                    $response['awsUrl'] =  "";
                    $subject = 'added client receipt with Receipt Id-'.$receipt_id.' and Trans. No	-'.$requestData['trans_no'][0];
                }

                $printUrl = \URL::to('/clients/printpreview').'/'.$receipt_id;
                $response['printUrl'] = $printUrl;

                if($request->type == 'client'){
                    $objs = new ActivitiesLog;
                    $objs->client_id = $requestData['client_id'];
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
                    $objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
                    $objs->save();
                }
            } else {
                $response['lastInsertedId'] = "";
                $response['awsUrl'] =  "";
                $response['requestData'] = "";
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['function_type'] = $requestData['function_type'];
              	$response['validate_receipt'] = "";
            }
        }
        else if( $requestData['function_type'] == 'edit'){
             if ($request->hasfile('document_upload'))
            {
                if(!is_array($request->file('document_upload'))){
                    $files[] = $request->file('document_upload');
                }else{
                    $files = $request->file('document_upload');
                }

                $client_info = \App\Models\Admin::select('client_id')->where('id', $requestData['client_id'])->first(); //dd($admin);
                if(!empty($client_info)){
                    $client_unique_id = $client_info->client_id;
                } else {
                    $client_unique_id = "";
                }

                $doctype = isset($request->doctype)? $request->doctype : '';

                foreach ($files as $file) {
                    $size = $file->getSize();
                    $fileName = $file->getClientOriginalName();
                    $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
                    $fileExtension = $file->getClientOriginalExtension();
                    //echo $nameWithoutExtension."===".$fileExtension;
                    //$explodeFileName = explode('.', $fileName);
                  
                    $name = time() . $file->getClientOriginalName();
                  
                    $filePath = $client_unique_id.'/'.$doctype.'/'. $name;
                    Storage::disk('s3')->put($filePath, file_get_contents($file));
                    //$exploadename = explode('.', $name);

                    $obj = new \App\Models\Document;
                    $obj->file_name = $nameWithoutExtension; //$explodeFileName[0];
                    $obj->filetype = $fileExtension; //$exploadename[1];
                    $obj->user_id = Auth::user()->id;
                  
                     // Get the full URL of the uploaded file
                    $fileUrl = Storage::disk('s3')->url($filePath);
                    $obj->myfile = $fileUrl;
                    $obj->myfile_key = $name;
                  
                    $obj->client_id = $requestData['client_id'];
                    $obj->type = $request->type;
                    $obj->file_size = $size;
                    $obj->doc_type = $doctype;
                    $doc_savedL = $obj->save();

                    $insertedDocIdL = $obj->id;
                }  //end foreach
            } else {
                $uploaded_doc_Info1 = DB::table('account_client_receipts')->select('uploaded_doc_id')->where('id',$requestData['id'][0])->first();
                if($uploaded_doc_Info1 && $uploaded_doc_Info1->uploaded_doc_id){
                    $insertedDocIdL = $uploaded_doc_Info1->uploaded_doc_id;
                } else {
                    $insertedDocIdL = null;
                }
                $doc_savedL = "";
            }

            $finalArr = array();
            for($j=0; $j<count($requestData['trans_date']); $j++){
                $finalArr[$j]['trans_date'] = $requestData['trans_date'][$j];
                $finalArr[$j]['entry_date'] = $requestData['entry_date'][$j];
                $finalArr[$j]['payment_method'] = $requestData['payment_method'][$j];
                $finalArr[$j]['description'] = $requestData['description'][$j];
                $finalArr[$j]['deposit_amount'] = $requestData['deposit_amount'][$j];
                //$finalArr[$j]['trans_no'] = $requestData['trans_no'][$j];
				//$finalArr[$j]['trans_no'] = "Rec".$requestData['id'][$j];
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
                        'deposit_amount' => $requestData['deposit_amount'][$j],
                        'uploaded_doc_id'=> $insertedDocIdL
                    ]);
            }
            if($savedDB >=0) {
                $requestData['trans_no'][0] = "Rec".$requestData['id'][0];
                $finalArr[0]['trans_no'] = "Rec".$requestData['id'][0];
                $response['function_type'] = $requestData['function_type'];
                $response['requestData'] 	= $finalArr;
                $db_total_deposit_amount = DB::table('account_client_receipts')->where('client_id',$requestData['client_id'])->where('receipt_type',1)->sum('deposit_amount');
                $response['db_total_deposit_amount'] = $db_total_deposit_amount;
                $response['status'] 	= 	true;

                $response['message'] = 'Client receipt updated successfully';
                $subject = 'updated client receipt with Receipt Id-'.$requestData['id'][0].' and Trans. No-'.$requestData['trans_no'][0];
                $response['lastInsertedId'] = $requestData['id'][0];
              
                $validate_receipt_info = DB::table('account_client_receipts')->select('validate_receipt')->where('id',$requestData['id'][0])->first();
                $response['validate_receipt'] = $validate_receipt_info->validate_receipt;
              
                if($doc_savedL){
                    //Get AWS Url link
                    //$url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                    //$awsUrl = $url.$client_unique_id.'/'.$doctype.'/'.$name; //dd($awsUrl);
                    
                  	$awsUrl = $fileUrl;
                    $response['awsUrl'] = $awsUrl;
                } else {
                    //Check aws url is exist or not
                    $uploaded_doc_Info = DB::table('account_client_receipts')->select('uploaded_doc_id')->where('id',$requestData['id'][0])->first();
                    if($uploaded_doc_Info){
                        $document_info = DB::table('documents')->select('myfile')->where('id',$uploaded_doc_Info->uploaded_doc_id)->first();
                        if($document_info){
                            $client_info = \App\Models\Admin::select('client_id')->where('id', $requestData['client_id'])->first(); //dd($admin);
                            if(!empty($client_info)){
                                $client_unique_id = $client_info->client_id;
                            } else {
                                $client_unique_id = "";
                            }
                            $doctype = 'client_receipt';
                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                            $awsUrl = $url.$client_unique_id.'/'.$doctype.'/'.$document_info->myfile; //dd($awsUrl);
                            $response['awsUrl'] =  $awsUrl;
                        } else {
                            $response['awsUrl'] =  "";
                        }
                    } else {
                        $response['awsUrl'] =  "";
                    }
                }

                $printUrl = \URL::to('/clients/printpreview').'/'.$requestData['id'][0];
                $response['printUrl'] = $printUrl;

                if($request->type == 'client'){
                    $objs = new ActivitiesLog;
                    $objs->client_id = $requestData['client_id'];
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
                    $objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
                    $objs->save();
                }
            } else {
                $response['requestData'] = "";
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['function_type'] = $requestData['function_type'];
                $response['lastInsertedId'] = "";
                $response['validate_receipt'] = "";
                $response['awsUrl'] =  "";
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
				$query->where('ad.client_id', 'LIKE', '%'.$client_id.'%');
			}
		}


		if ($request->has('name')) {
			$name =  $request->input('name');
			if(trim($name) != '') {
				$query->where(function ($q) use ($name) {
                    $q->where(DB::raw("COALESCE(ad.first_name, '') || ' ' || COALESCE(ad.last_name, '')"), 'LIKE', "%{$name}%")
                      ->orWhere('ad.first_name', 'LIKE', "%{$name}%")
                      ->orWhere('ad.last_name', 'LIKE', "%{$name}%");
                });
			}
		}
      
        if ($request->has('trans_date')) {
			$trans_date =  $request->input('trans_date');
			if(trim($trans_date) != '') {
				$query->where('acr.trans_date', 'LIKE', '%'.$trans_date.'%');
			}
		}

        if ($request->has('deposit_amount')) {
			$deposit_amount =  $request->input('deposit_amount');
			if(trim($deposit_amount) != '') {
				$query->where('acr.deposit_amount', '=', $deposit_amount);
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
                    $client_info = \App\Models\Admin::select('client_id')->where('id', $receipt_info->client_id)->first();

                    if($request->receipt_type == 1){
                        $subject = 'validated client receipt no -'.$ReceiptVal.' of client-'.$client_info->client_id;
                    } 
                    $objs = new ActivitiesLog;
                    $objs->client_id = $receipt_info->client_id;
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
                    $objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
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
			$data = \App\Models\Application::join('admins', 'applications.client_id', '=', 'admins.id')
            ->leftJoin('partners', 'applications.partner_id', '=', 'partners.id')
            ->leftJoin('products', 'applications.product_id', '=', 'products.id')
            ->leftJoin('application_fee_options', 'applications.id', '=', 'application_fee_options.app_id')
            ->select('applications.*','admins.client_id as client_reference', 'admins.first_name','admins.last_name','admins.dob','partners.partner_name','products.name as coursename','application_fee_options.total_course_fee_amount','application_fee_options.enrolment_fee_amount','application_fee_options.material_fees','application_fee_options.tution_fees','application_fee_options.total_anticipated_fee','application_fee_options.fee_reported_by_college','application_fee_options.bonus_amount','application_fee_options.bonus_pending_amount','application_fee_options.bonus_paid','application_fee_options.commission_as_per_anticipated_fee','application_fee_options.commission_as_per_fee_reported','application_fee_options.commission_payable_as_per_anticipated_fee','application_fee_options.commission_paid_as_per_fee_reported','application_fee_options.commission_pending')
            ->where('applications.stage','Coe issued')
            ->orWhere('applications.stage','Enrolled')
            ->orWhere('applications.stage','Coe Cancelled')
            ->latest()->get();  //dd($data);
            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('client_reference', function($data) {
                if($data->client_reference){
                    $client_encoded_id = base64_encode(convert_uuencode(@$data->client_id)) ;
                    $client_reference = '<a href="'.route('clients.detail', $client_encoded_id).'" target="_blank" >'.$data->client_reference.'</a>';
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
            /*->addColumn('total_anticipated_fee', function($data) {
                if($data->total_anticipated_fee != ""){
                    $total_anticipated_fee = $data->total_anticipated_fee;
                } else {
                    $total_anticipated_fee = 'N/P';
                }
                return $total_anticipated_fee;
            })*/
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
           ->addColumn('bonus_pending_amount', function($data) {
                if($data->bonus_pending_amount != ""){
                    $bonus_pending_amount = $data->bonus_pending_amount;
                } else {
                    $bonus_pending_amount = 'N/P';
                }
                return $bonus_pending_amount;
            })
            ->addColumn('bonus_paid', function($data) {
                if($data->bonus_paid != ""){
                    $bonus_paid = $data->bonus_paid;
                } else {
                    $bonus_paid = 'N/P';
                }
                return $bonus_paid;
            })
            /*->addColumn('commission_as_per_anticipated_fee', function($data) {
                if($data->commission_as_per_anticipated_fee != ""){
                    $commission_as_per_anticipated_fee = $data->commission_as_per_anticipated_fee;
                } else {
                    $commission_as_per_anticipated_fee = 'N/P';
                }
                return $commission_as_per_anticipated_fee;
            })*/
            ->addColumn('commission_as_per_fee_reported', function($data) {
                if($data->commission_as_per_fee_reported != ""){
                    $commission_as_per_fee_reported = $data->commission_as_per_fee_reported;
                } else {
                    $commission_as_per_fee_reported = 'N/P';
                }
                return $commission_as_per_fee_reported;
            })
            /*->addColumn('commission_payable_as_per_anticipated_fee', function($data) {
                if($data->commission_payable_as_per_anticipated_fee != ""){
                    $commission_payable_as_per_anticipated_fee = $data->commission_payable_as_per_anticipated_fee;
                } else {
                    $commission_payable_as_per_anticipated_fee = 'N/P';
                }
                return $commission_payable_as_per_anticipated_fee;
            })*/
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
                } else if($data->status == 3){
                    $student_status = "Cancelled";
                }
                return $student_status;
            })
            ->rawColumns(['client_reference'])
            ->make(true);
        }
    }
  
    
	//Add All Doc checklist
    public function addalldocchecklist(Request $request){ //dd($request->all());
        try {
            $response = ['status' => false, 'message' => 'Please try again'];
            $clientid = $request->clientid;
            
            if(empty($clientid)) {
                $response['message'] = 'Client ID is required';
                echo json_encode($response);
                return;
            }
            
            $admin_info1 = \App\Models\Admin::select('client_id')->where('id', $clientid)->first(); //dd($admin);
            if(!empty($admin_info1)){
                $client_unique_id = $admin_info1->client_id;
            } else {
                $client_unique_id = "";
            }  //dd($client_unique_id);
            $doctype = isset($request->doctype)? $request->doctype : '';

            if ($request->has('checklist'))
        {
            $checklistArray = $request->input('checklist'); //dd($checklistArray);
            if (is_array($checklistArray) && !empty($checklistArray))
            {
                $saved = false;
                foreach ($checklistArray as $item)
                {
                    if(empty($item)) continue; // Skip empty items
                    $obj = new \App\Models\Document;
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
                        $objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
                        $objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
                        $objs->save();
                    }

                    $response['status'] 	= 	true;
                    $response['message']	=	'You have successfully added your document checklist';

                    $fetchd = \App\Models\Document::where('client_id',$clientid)->whereNull('not_used_doc')->where('doc_type',$doctype)->where('type',$request->type)->orderBy('updated_at', 'DESC')->get();
                    ob_start();
                    foreach($fetchd as $docKey=>$fetch)
                    {
                        $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                        //Checklist verified by
                        /*if( isset($fetch->checklist_verified_by) && $fetch->checklist_verified_by != "") {
                            $checklist_verified_Info = \App\Models\Admin::select('first_name')->where('id', $fetch->checklist_verified_by)->first();
                            $checklist_verified_by = $checklist_verified_Info->first_name;
                        } else {
                            $checklist_verified_by = 'N/A';
                        }

                        if( isset($fetch->checklist_verified_at) && $fetch->checklist_verified_at != "") {
                            $checklist_verified_at = date('d/m/Y', strtotime($fetch->checklist_verified_at));
                        } else {
                            $checklist_verified_at = 'N/A';
                        }*/
                        ?>
                        <tr class="drow" id="id_<?php echo $fetch->id; ?>">
                            <td><?php echo $docKey+1;?></td>
                            <td style="white-space: initial;">
                                <div data-id="<?php echo $fetch->id;?>" data-personalchecklistname="<?php echo $fetch->checklist; ?>" class="personalchecklist-row">
                                    <span><?php echo $fetch->checklist; ?></span>
                                </div>
                            </td>
                            <td style="white-space: initial;">
                                <?php
                                if($admin) {
                                    echo $admin->first_name. "<br>";
                                } else {
                                    echo "N/A<br>";
                                }
                                echo date('d/m/Y', strtotime($fetch->created_at));
                                ?>
                            </td>
                            <td style="white-space: initial;">
                                <?php
                                if( isset($fetch->file_name) && $fetch->file_name !=""){ ?>
                                    <div data-id="<?php echo $fetch->id; ?>" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
                                        <!--<a target="_blank" class="dropdown-item" href="<?php //echo $fetch->myfile; ?>" style="white-space: initial;">
                                            <i class="fas fa-file-image"></i> <span><?php //echo $fetch->file_name; ?><?php //echo '.'.$fetch->filetype; ?></span>
                                        </a>-->
                                      
                                        <a style="white-space: initial;" href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-alldocumentlist')">
                                            <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                        </a>
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
                            <!--<td id="docverifiedby_<?php //echo $fetch->id;?>">
                                <?php
                                //echo $checklist_verified_by. "<br>";
                                //echo $checklist_verified_at;
                                ?>
                            </td>-->

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
                                            //$url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                            ?>
                                            <!--<a target="_blank" class="dropdown-item" href="<?php //echo $url.$client_unique_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Preview</a>-->
                                            <!--<a target="_blank" class="dropdown-item" href="<?php //echo $fetch->myfile; ?>">Preview</a>-->
                                          
                                             <a class="dropdown-item" href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-alldocumentlist')">Preview</a>

                                            <?php
                                            $explodeimg = explode('.',$fetch->myfile);
                                            if(strtolower($explodeimg[1]) == 'jpg'|| strtolower($explodeimg[1]) == 'png'|| strtolower($explodeimg[1]) == 'jpeg'){
                                            ?>
                                                <a target="_blank" class="dropdown-item" href="<?php echo \URL::to('/document/download/pdf'); ?>/<?php echo $fetch->id; ?>">PDF</a>
                                            <?php } ?>

                                            <!--<a download class="dropdown-item" href="<?php //echo $url.$client_unique_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Download</a>-->
                                            <!--<a download class="dropdown-item" href="<?php //echo $fetch->myfile; ?>">Download</a>-->
                                            <a href="#" class="dropdown-item download-file" data-filelink="<?= $fetch->myfile ?>" data-filename="<?= $fetch->myfile_key ?>">Download</a>


                                            <?php if( Auth::user()->role == 1 ){ //echo Auth::user()->role;//super admin ?>
                                            <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item deletenote" data-href="deletealldocs" href="javascript:;" >Delete</a>
                                            <?php } ?>
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
                                            //$url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                            ?>
                                            <?php if( isset($fetch->myfile) && $fetch->myfile != ""){?>
                                            <!--<a class="dropdown-item" href="<?php //echo $url.$client_unique_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Preview</a>
                                            <a download class="dropdown-item" href="<?php //echo $url.$client_unique_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Download</a>-->

                                            <!--<a class="dropdown-item" href="<?php //echo $fetch->myfile; ?>">Preview</a>
                                            <a download class="dropdown-item" href="<?php //echo $fetch->myfile; ?>">Download</a>-->
                                          
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-alldocumentlist')">Preview</a>
                                            <a href="#" class="dropdown-item download-file" data-filelink="<?= $fetch->myfile ?>" data-filename="<?= $fetch->myfile_key ?>">Download</a>


                                            <?php if( Auth::user()->role == 1 ){ //echo Auth::user()->role;//super admin ?>
                                                <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item deletenote" data-href="deletealldocs" href="javascript:;" >Delete</a>
                                            <?php }?>
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
        } catch (\Exception $e) {
            \Log::error('Error in addalldocchecklist: ' . $e->getMessage() . ' at line ' . $e->getLine());
            $response = ['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
            echo json_encode($response);
        }
    }

    //Update all Document upload
	public function uploadalldocument(Request $request){ //dd($request->all());
        if ($request->hasfile('document_upload'))
        {
            $clientid = $request->clientid;
            $admin_info1 = \App\Models\Admin::select('client_id')->where('id', $clientid)->first(); //dd($admin);
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
            $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
            $fileExtension = $files->getClientOriginalExtension();
            //echo $nameWithoutExtension."===".$fileExtension;
            $name = time() . $files->getClientOriginalName();
            $filePath = $client_unique_id.'/'.$doctype.'/'. $name;
            Storage::disk('s3')->put($filePath, file_get_contents($files));
            $exploadename = explode('.', $name);

            $req_file_id = $request->fileid;
            $obj = \App\Models\Document::find($req_file_id);
            $obj->file_name = $nameWithoutExtension; //$explodeFileName[0];
            $obj->filetype = $fileExtension;//$exploadename[1];
            $obj->user_id = Auth::user()->id;
            //$obj->myfile = $name;
            // Get the full URL of the uploaded file
            $fileUrl = Storage::disk('s3')->url($filePath);
            $obj->myfile = $fileUrl;
            $obj->myfile_key = $name;

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
                    $objs->task_status = 0; // Required NOT NULL field for PostgreSQL (0 = activity, 1 = task)
                    $objs->pin = 0; // Required NOT NULL field for PostgreSQL (0 = not pinned, 1 = pinned)
                    $objs->save();
                }
				$response['status'] 	= 	true;
				$response['message']	=	'You have successfully uploaded your document';
			$fetchd = \App\Models\Document::where('client_id',$clientid)->whereNull('not_used_doc')->where('doc_type',$doctype)->where('type',$request->type)->orderByRaw('updated_at DESC NULLS LAST')->get();
			ob_start();
			foreach($fetchd as  $docKey=>$fetch){
				$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
					$addedByInfo = $admin->first_name . ' on ' . date('d/m/Y', strtotime($fetch->created_at));
					?>
					<tr class="drow document-row" id="id_<?php echo $fetch->id; ?>" 
						data-doc-id="<?php echo $fetch->id;?>"
						data-checklist-name="<?php echo htmlspecialchars($fetch->checklist, ENT_QUOTES, 'UTF-8'); ?>"
						data-file-name="<?php echo htmlspecialchars($fetch->file_name, ENT_QUOTES, 'UTF-8'); ?>"
						data-file-type="<?php echo htmlspecialchars($fetch->filetype, ENT_QUOTES, 'UTF-8'); ?>"
						data-myfile="<?php echo htmlspecialchars($fetch->myfile, ENT_QUOTES, 'UTF-8'); ?>"
						data-myfile-key="<?php echo isset($fetch->myfile_key) ? htmlspecialchars($fetch->myfile_key, ENT_QUOTES, 'UTF-8') : ''; ?>"
						data-doc-type="<?php echo htmlspecialchars($fetch->doc_type, ENT_QUOTES, 'UTF-8'); ?>"
						data-user-role="<?php echo Auth::user()->role; ?>"
						title="Added by: <?php echo htmlspecialchars($addedByInfo, ENT_QUOTES, 'UTF-8'); ?>"
						style="cursor: context-menu;">
						<td style="white-space: initial;">
							<div data-id="<?php echo $fetch->id;?>" data-personalchecklistname="<?php echo $fetch->checklist; ?>" class="personalchecklist-row">
								<span><?php echo $fetch->checklist; ?></span>
							</div>
						</td>
						<td style="white-space: initial;">
							<?php
							if( isset($fetch->file_name) && $fetch->file_name !=""){ ?>
								<div data-id="<?php echo $fetch->id; ?>" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
									<?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
										<a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-alldocumentlist')">
											<i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
										</a>
									<?php } else {  //For old file upload
										$url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
										$myawsfile = $url.$client_unique_id.'/'.$fetch->doc_type.'/'.$fetch->myfile;
										?>
										<a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($myawsfile); ?>','preview-container-alldocumentlist')">
											<i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
										</a>
									<?php } ?>
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
                                        //$url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                        ?>
										<!--<a class="dropdown-item" href="<?php //echo $url.$client_unique_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Preview</a>
										<a download class="dropdown-item" href="<?php //echo $url.$client_unique_id.'/'.$doctype.'/'.$fetch->myfile; ?>">Download</a>-->

                                        <!--<a class="dropdown-item" href="<?php //echo $fetch->myfile; ?>">Preview</a>
										<a download class="dropdown-item" href="<?php //echo $fetch->myfile; ?>">Download</a>-->
                                      
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-alldocumentlist')">Preview</a>

                                        <a href="#" class="dropdown-item download-file" data-filelink="<?= $fetch->myfile ?>" data-filename="<?= $fetch->myfile_key ?>">Download</a>



                                        <?php if( Auth::user()->role == 1 ){ //echo Auth::user()->role;//super admin ?>
                                        <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item deletenote" data-href="deletealldocs" href="javascript:;" >Delete</a>
                                        <?php } ?>
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
        if(\App\Models\Document::where('id',$doc_id)->exists()){
            $upd = DB::table('documents')->where('id', $doc_id)->update(array(
                'checklist_verified_by' => Auth::user()->id,
                'checklist_verified_at' => date('Y-m-d H:i:s')
            ));
            if($upd){
                $docInfo = \App\Models\Document::select('client_id')->where('id',$doc_id)->first();
                $subject = 'verified '.$doc_type.' document';
                $objs = new ActivitiesLog;
				$objs->client_id = $docInfo->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '';
				$objs->subject = $subject;
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
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
        if(\App\Models\Document::where('id',$doc_id)->exists()){
            $upd = DB::table('documents')->where('id', $doc_id)->update(array('not_used_doc' => 1));
            if($upd){
                $docInfo = \App\Models\Document::where('id',$doc_id)->first();
                $subject = $doc_type.' document moved to Not Used Tab';
                $objs = new ActivitiesLog;
				$objs->client_id = $docInfo->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '';
				$objs->subject = $subject;
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
				$objs->save();

                if($docInfo){
                    if( isset($docInfo->user_id) && $docInfo->user_id!= "" ){
                        $adminInfo = \App\Models\Admin::select('first_name')->where('id',$docInfo->user_id)->first();
                        $response['Added_By'] = $adminInfo->first_name;
                        $response['Added_date'] = date('d/m/Y',strtotime($docInfo->created_at));
                    } else {
                        $response['Added_By'] = "N/A";
                        $response['Added_date'] = "N/A";
                    }


                    if( isset($docInfo->checklist_verified_by) && $docInfo->checklist_verified_by!= "" ){
                        $verifyInfo = \App\Models\Admin::select('first_name')->where('id',$docInfo->checklist_verified_by)->first();
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
		if(\App\Models\Document::where('id',$id)->exists()){
			$doc = \App\Models\Document::where('id',$id)->first();
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
        if(\App\Models\Document::where('id',$note_id)->exists()){
            $data = DB::table('documents')->where('id', @$note_id)->first();
            /*if(
                ( isset($data->myfile) && $data->myfile != '' )
                &&
                ( isset($data->myfile_key) && $data->myfile_key != '' )
            ){*/
            if( isset($data->myfile_key) && $data->myfile_key != '' ){
                // Extract the file path from the URL
                $parsedUrl = parse_url($data->myfile);
                $filePath = ltrim($parsedUrl['path'], '/'); //dd($filePath);

                // Find the position of the keyword
                $position = strpos($filePath, '/');
                if ($position !== false) {
                    $filePathArr = explode('/',$filePath);//dd($filePathArr);
                    if(!empty($filePathArr)){
                        $fileExistPath = $filePathArr[0]."/".$filePathArr[1]."/".$data->myfile_key;
                        if (Storage::disk('s3')->exists($fileExistPath)) {
                            // To delete the uploaded file, use the delete method
                            Storage::disk('s3')->delete($fileExistPath);
                        }
                    }
                }
            }

            $res = DB::table('documents')->where('id', @$note_id)->delete();
            if($res){
                $subject = 'deleted a document';
                $objs = new ActivitiesLog;
				$objs->client_id = $data->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '';
				$objs->subject = $subject;
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
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
		if(\App\Models\Document::where('id',$id)->exists()){
			$doc = \App\Models\Document::where('id',$id)->first();
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
  
  
    //Back To Document List From Not Used Tab
    public function backtodoc(Request $request){ //dd($request->all());
		$doc_id = $request->doc_id;
        $doc_type = $request->doc_type;
        if(\App\Models\Document::where('id',$doc_id)->exists()){
            $upd = DB::table('documents')->where('id', $doc_id)->update(array('not_used_doc' => null));
            if($upd){
                $docInfo = \App\Models\Document::where('id',$doc_id)->first();
                $subject = $doc_type.' document moved to document tab';
                $objs = new ActivitiesLog;
				$objs->client_id = $docInfo->client_id;
				$objs->created_by = Auth::user()->id;
				$objs->description = '';
				$objs->subject = $subject;
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
				$objs->save();

                if($docInfo){
                    if( isset($docInfo->user_id) && $docInfo->user_id!= "" ){
                        $adminInfo = \App\Models\Admin::select('first_name')->where('id',$docInfo->user_id)->first();
                        $response['Added_By'] = $adminInfo->first_name;
                        $response['Added_date'] = date('d/m/Y',strtotime($docInfo->created_at));
                    } else {
                        $response['Added_By'] = "N/A";
                        $response['Added_date'] = "N/A";
                    }


                    if( isset($docInfo->checklist_verified_by) && $docInfo->checklist_verified_by!= "" ){
                        $verifyInfo = \App\Models\Admin::select('first_name')->where('id',$docInfo->checklist_verified_by)->first();
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
				$response['data']	=	$doc_type.' document moved to document tab';
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
  
  
    //Asssign application stage and save
	public function followupstore_application(Request $request){
	    $requestData = $request->all(); //echo '<pre>'; print_r($requestData); die;
        //echo "client_id==".$requestData['client_id'];
        //echo $client_decode_id = base64_encode(convert_uuencode($requestData['client_id'])); die;
        $client_decode_id = base64_encode(convert_uuencode($requestData['client_id']));

        $followup 				    = new \App\Models\Note;
        $followup->client_id		= @$requestData['client_id'];
		$followup->user_id			= Auth::user()->id;

        //Get Description
        $description =  'Application '.$requestData['course'].' for this college '.$requestData['school'].' assigned for '.$requestData['stage_name'].' stage';
		$followup->description		= $description;


        //Get assigner name
        $assignee_info = \App\Models\Admin::select('id','first_name','last_name')->where('id', $requestData['rem_cat11'])->first();
        if($assignee_info){
            $assignee_name = $assignee_info->first_name;
        } else {
            $assignee_name = 'N/A';
        }
        $title = 'Application assign to '.$assignee_name;
		$followup->title		    = $title;
        $followup->folloup	        = 1;
        $followup->task_group       = 'stage';
		$followup->assigned_to	    = @$requestData['rem_cat11'];
		$followup->followup_date	=  date('Y-m-d H:i:s');
        $followup->application_id	= $requestData['application_id'];
        $saved				        =  $followup->save();
        if(!$saved) {
			echo json_encode(array('success' => false, 'message' => 'Please try again', 'clientID' => $client_decode_id));
		} else {
			/*if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
                $Lead = Admin::find($this->decodeString($requestData['client_id']));
                $Lead->followup_date = date('Y-m-d H:i:s');
                $Lead->save();
			}*/

			$o = new \App\Models\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = @$requestData['rem_cat11'];
	    	$o->module_id = $requestData['client_id'];

	    	$o->url = route('clients.detail', $client_decode_id);
	    	$o->notification_type = 'client';
	    	$o->message = 'Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name.' '.date('d/M/Y h:i A');
	    	$o->seen = 0; // Set seen to 0 (unseen) for new notifications
	    	$o->save();

			/*$objs = new ActivitiesLog;
            $objs->client_id = @$requestData['client_id'];
            $objs->created_by = Auth::user()->id;
            $objs->subject = 'set action for '.@$assignee_name;
            $objs->description = '<span class="text-semi-bold">'.@$title.'</span><p>'.$description.'</p>';
            if(Auth::user()->id != @$requestData['rem_cat11']){
                $objs->use_for = @$requestData['rem_cat11'];
            } else {
                $objs->use_for = "";
            }
            $objs->followup_date = date('Y-m-d H:i:s');
            $objs->task_group = 'stage';
            $objs->save();*/



            $obj1 = new \App\Models\ApplicationActivitiesLog;
            $obj1->stage = $requestData['stage_name'];
            $obj1->type = 'task';
            $obj1->comment = 'assigned a task';
            $obj1->title =  'set action for '.@$assignee_name;
            $obj1->description =  '<span class="text-semi-bold">'.@$title.'</span><p>'.$description.'</p>';
            $obj1->app_id =  $requestData['application_id'];
            $obj1->user_id = Auth::user()->id;
            $obj1->save();

			echo json_encode(array('success' => true, 'message' => 'Applcation successfully assigned', 'clientID' => $client_decode_id,'application_id' => $requestData['application_id']));
			exit;
		}
	}
  
  
  
    //Fetch all contact list of any client at create note popup
    public function fetchClientContactNo(Request $request){ //dd($request->all());
        if( \App\Models\ClientPhone::where('client_id', $request->client_id)->exists())
        {
            //Fetch All client contacts
            $clientContacts = \App\Models\ClientPhone::select('client_phone','client_country_code','contact_type')->where('client_id', $request->client_id)->where('contact_type', '!=', 'Not In Use')->get();
            //dd($clientContacts);
            if( !empty($clientContacts) && count($clientContacts)>0 ){
                $response['status'] 	= 	true;
                $response['message']	=	'Client contact is successfully fetched.';
                $response['clientContacts']	=	$clientContacts;
            } else {
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['clientContacts']	=	array();
            }
        }
        else
        {
            if( \App\Models\Admin::where('id', $request->client_id)->exists()){
                //Fetch All client contacts
                $clientContacts = \App\Models\Admin::select('phone as client_phone','country_code as client_country_code','contact_type')->where('id', $request->client_id)->get();
                //dd($clientContacts);
                if( !empty($clientContacts) && count($clientContacts)>0 ){
                    $response['status'] 	= 	true;
                    $response['message']	=	'Client contact is successfully fetched.';
                    $response['clientContacts']	=	$clientContacts;
                } else {
                    $response['status'] 	= 	false;
                    $response['message']	=	'Please try again';
                    $response['clientContacts']	=	array();
                }
            }
            else {
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['clientContacts']	=	array();
            }
        }
        echo json_encode($response);
	}
  
  
  
    //Send message
    public function sendmsg(Request $request){ //dd($request->all());
        $obj = new \App\Models\Note;
        $obj->client_id = $request->client_id;
        $obj->user_id = Auth::user()->id;
        $subject = 'sent a message';
        $obj->title =  $subject;
        $obj->description = $request->message;
        $obj->type = $request->vtype;
        $saved = $obj->save();
		if($saved){
            if($request->vtype == 'client'){
                $objs = new ActivitiesLog;
                $objs->client_id = $request->client_id;
                $objs->created_by = Auth::user()->id;
                $objs->description = '<span class="text-semi-bold">'.$subject.'</span><p>'.$request->message.'</p>';
                $objs->subject = $subject;
                $objs->task_status = 0; // Required NOT NULL field for PostgreSQL (0 = activity, 1 = task)
                $objs->pin = 0; // Required NOT NULL field for PostgreSQL (0 = not pinned, 1 = pinned)
                $objs->save();
            }
            $response['status'] 	= 	true;
            $response['message']	=	'You have successfully sent message';
        }else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
        echo json_encode($response);
	}
  
    
    //Google review email sent
    public function isgreviewmailsent(Request $request){
        $data = $request->all(); //dd($data);
        if($data['is_greview_mail_sent'] != 1){
            $userInfo = Admin::select('first_name','email')->where('id', $data['id'])->first();
            if($userInfo){
                //Google review email link To Customer
                $details = [
                    'title' => 'Invitation For Google Review At Bansal Immigration',
                    'body' => 'This is for testing email using smtp',
                    'fullname' => $userInfo->first_name,
                    'email'=> $userInfo->email,
                    'reviewLink'=> env('GOOGLE_REVIEW_LINK')
                ];

                if( \Mail::to($userInfo->email)->send(new \App\Models\Mail\GoogleReviewMail($details)) ) { //mail sent = success
                    $recExist = Admin::where('id', $data['id'])->update(['is_greview_mail_sent' => 1]);
                    if($recExist){
                        $objs = new ActivitiesLog;
                        $objs->client_id = $data['id'];
                        $objs->created_by = Auth::user()->id;
                        $objs->description = '<span class="text-semi-bold">Google review inviatation sent successfully</span>';
                        $objs->subject = "Google review inviatation";
                        $objs->task_status = 0; // Required NOT NULL field for PostgreSQL (0 = activity, 1 = task)
                        $objs->pin = 0; // Required NOT NULL field for PostgreSQL (0 = not pinned, 1 = pinned)
                        $objs->save();

                        $response['status'] 	= 	true;
                        $response['message']	=	'Google review inviatation sent successfully';
                        $response['is_greview_mail_sent'] 	= 	$data['is_greview_mail_sent'];
                    }
                } else { //mail sent = failed
                    $response['status'] 	= 	false;
                    $response['message']	=	'Please try again';
                    $response['is_greview_mail_sent'] 	= 	$data['is_greview_mail_sent'];
                }
            } else { //User not found
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['is_greview_mail_sent'] 	= 	$data['is_greview_mail_sent'];
            }
        }
        echo json_encode($response);
    }
   
  	//Chatgpt enhance message
    public function enhanceMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        try {
            $response = $this->openAiClient->post('chat/completions', [
                'json' => [
                    'model' => 'gpt-3.5-turbo', // or 'gpt-4' if you have access
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a professional email writer. Rewrite the following content in a more professional and polished manner:'
                        ],
                        [
                            'role' => 'user',
                            'content' => $request->message
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 500,
                ],
            ]);

            $result = json_decode($response->getBody(), true);
            $enhancedMessage = $result['choices'][0]['message']['content'];

            return response()->json(['enhanced_message' => $enhancedMessage]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to enhance message: ' . $e->getMessage()], 500);
        }
    }
  
  
    //Download Document
    /*public function download_document(Request $request)
    {
        $fileUrl = $request->input('filelink');
        $filename = $request->input('filename', 'downloaded.pdf');

        if (!$fileUrl) {
            return abort(400, 'Missing file URL');
        }

        $response = Http::get($fileUrl);

        if (!$response->successful()) {
            return abort(404, 'File not found');
        }

        return response($response->body())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }*/
  
    public function download_document(Request $request)
    {
        $fileUrl = $request->input('filelink');
        $filename = $request->input('filename', 'downloaded.pdf');

        if (!$fileUrl) {
            return abort(400, 'Missing file URL');
        }

        try {
            // Extract S3 key from the URL
            $parsed = parse_url($fileUrl);
            if (!isset($parsed['path'])) {
                return abort(400, 'Invalid S3 URL format');
            }
            
            $s3Key = ltrim(urldecode($parsed['path']), '/');
            
            // Check if file exists in S3
            if (!Storage::disk('s3')->exists($s3Key)) {
                return abort(404, 'File not found in S3');
            }
            
            // Generate temporary URL with proper headers
            $tempUrl = Storage::disk('s3')->temporaryUrl(
                $s3Key,
                now()->addMinutes(5), // 5 minutes expiration
                [
                    'ResponseContentDisposition' => 'attachment; filename="' . $filename . '"',
                    'ResponseContentType' => 'application/pdf'
                ]
            );
            
            // Redirect to S3 temporary URL
            return redirect($tempUrl);
            
        } catch (\Exception $e) {
            \Log::error('S3 download error: ' . $e->getMessage());
            return abort(500, 'Error generating download link');
        }
    }
    
}
