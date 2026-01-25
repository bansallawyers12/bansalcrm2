<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Traits\ClientHelpers;
use App\Traits\ClientQueries;
use App\Traits\ClientAuthorization;
use App\Services\SearchService;
use App\Services\ClientExportService;
use App\Services\ClientImportService;
use App\Models\CheckinLog;
use App\Models\ClientPhone;
use App\Helpers\PhoneHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Auth;
use Config;

/**
 * Core client CRUD and listing operations
 * 
 * Methods to move from ClientsController:
 * - index
 * - archived
 * - create
 * - store
 * - edit
 * - clientdetail
 * - leaddetail
 * - updateclientstatus
 * - changetype
 * - change_assignee
 * - removetag
 * - save_tag
 * - getallclients
 * - getrecipients
 * - getonlyclientrecipients
 * - address_auto_populate
 * - updatesessioncompleted
 */
class ClientController extends Controller
{
    use ClientHelpers, ClientQueries, ClientAuthorization;

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

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
		
		// Apply search and filters
		$query = $this->applyArchivedFilters($query, $request);
		
		$totalData = $query->count();
		$lists = $query->sortable(['id' => 'desc'])->paginate(20)->appends($request->except('page'));
		
		// Assignees for filter dropdown (admins except clients)
		$assignees = \App\Models\Admin::select('id', 'first_name', 'last_name')
			->where('role', '!=', 7)
			->where('status', 1)
			->orderBy('first_name')
			->get();
		
		// Users who have archived at least one client (for "Archived by" filter)
		$archivedByUsers = \App\Models\Admin::select('admins.id', 'admins.first_name', 'admins.last_name')
			->whereIn('admins.id', function ($q) {
				$q->select('archived_by')->from('admins')->where('is_archived', 1)->whereNotNull('archived_by');
			})
			->orderBy('first_name')
			->get();
		
		return view($this->getClientViewPath('archived.index'), compact(['lists', 'totalData', 'assignees', 'archivedByUsers']));
	}

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
			$obj->att_country_code	=	PhoneHelper::normalizeCountryCode(@$requestData['att_country_code']);
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
			if(isset($requestData['tagname'])){
				$obj->tagname = $this->normalizeTags($requestData['tagname']);
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
                $client_country_code = array_map(function($code) {
                    return PhoneHelper::normalizeCountryCode($code);
                }, (array)$requestData['client_country_code']);
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
					return redirect()->route('clients.index')->with('error', 'Clients Not Exist');
				}
			}
			else
			{
				return redirect()->route('clients.index')->with('error', Config::get('constants.unauthorized'));
			}
		}

	}

    //Client detail page
    public function clientdetail(Request $request, $id = NULL, $tab = NULL){ 
        $showAlert = false;
        $applicationId = $request->route('applicationId');
        if (!empty($applicationId) && empty($tab)) {
            $tab = 'application';
        }
        $forcedTab = $tab;
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
                
                return view(
                    $this->getClientViewPath('clients.detail'),
                    compact(['fetchedData','encodeId','showAlert','applicationId','forcedTab'])
                );
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
    public function leaddetail(Request $request, $id = NULL, $tab = NULL){ 
        $showAlert = false;
        $applicationId = $request->route('applicationId');
        if (!empty($applicationId) && empty($tab)) {
            $tab = 'application';
        }
        $forcedTab = $tab;
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
                return redirect()->route('leads.index')->with('error', 'Invalid Lead ID');
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
                    // Set required NOT NULL fields for PostgreSQL
                    $obj->password = Hash::make('LEAD_PLACEHOLDER'); // Required NOT NULL - placeholder for leads
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
                return view(
                    $this->getClientViewPath('clients.detail'),
                    compact(['fetchedData','encodeId','showAlert','applicationId','forcedTab'])
                );
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
					$subject = 'has changed Client\'s rating from '.$client->rating.' to '.$request->rating;
				}
				$objs = new ActivitiesLog;
				$objs->client_id = $request->id;
				$objs->created_by = Auth::user()->id;
				$objs->subject = $subject;
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
				$objs->save();
				$response['status'] 	= 	true;
				$response['message']	=	'You\'ve successfully updated your client\'s information.';
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

	public function getrecipients(Request $request){
		$squery = $request->q ?? '';
		if($squery != ''){
			try {
				$operator = DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
				$clients = Admin::where('is_archived', '=', 0)
					->where('role', '=', 7)
					->where(function($query) use ($squery, $operator) {
						$query->where('email', $operator, '%'.$squery.'%')
							->orWhere('first_name', $operator, '%'.$squery.'%')
							->orWhere('last_name', $operator, '%'.$squery.'%')
							->orWhere('client_id', $operator, '%'.$squery.'%')
							->orWhere('phone', $operator, '%'.$squery.'%')
							->orWhere(DB::raw("COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')"), $operator, '%'.$squery.'%');
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
			$operator = DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
			$clients = Admin::where('is_archived', '=', 0)
				->where('role', '=', 7)
				->where(function($query) use ($squery, $operator) {
					return $query
						->where('email', $operator, '%'.$squery.'%')
						->orwhere('first_name', $operator, '%'.$squery.'%')
						->orwhere('last_name', $operator, '%'.$squery.'%')
						->orwhere('client_id', $operator, '%'.$squery.'%')
						->orwhere('phone', $operator, '%'.$squery.'%')
						->orWhere(DB::raw("COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')"), $operator, "%".$squery."%");
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

	public function save_tag(Request $request){
		$id = $request->client_id;

		if(Admin::where('id',$id)->exists()){
			$rawTags = $request->input('tagname', '');
			$obj = Admin::find($id);
			$obj->tagname = $this->normalizeTags($rawTags);
			$saved = $obj->save();
			if($saved){
				return redirect()->route('clients.detail', base64_encode(convert_uuencode(@$id)))->with('success', 'Tags addes successfully');
			}else{
				return redirect()->route('clients.detail', base64_encode(convert_uuencode(@$id)))->with('error', 'Please try again');
			}
		}else{
			return redirect()->route('clients.index')->with('error', Config::get('constants.unauthorized'));
		}
	}

	public function change_assignee(Request $request){
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
		$saved = $objs->save();
		if($saved){
			if ( is_array($request->assinee) && count($request->assinee) >=1) {
				$assigneeArr = $request->assinee;
				foreach($assigneeArr as $key=>$val) {
					$o = new \App\Models\Notification;
					$o->sender_id = Auth::user()->id;
					$o->receiver_id = $val;
					$o->module_id = $request->id;
					$o->url = route('clients.detail', base64_encode(convert_uuencode(@$request->id)));
					$o->notification_type = 'client';
					$o->message = 'Client Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name;
					$o->seen = 0;
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
		return redirect()->route('clients.detail', ['id' => base64_encode(convert_uuencode(@$objs->id))])->with('success', 'Record Updated successfully');
	}

    /**
     * Auto-populate address using geocoding (currently disabled)
     */
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

    /**
     * Check if client exists (AJAX validation)
     * Used for form validation to prevent duplicate clients
     * 
     * @param Request $request
     * @return int Returns 1 if exists, 0 if not
     */
    public function checkclientexist(Request $request){
        if($request->type == 'email'){
            $clientexists = Admin::where('email', $request->vl)->where('role', 7)->exists();
            echo $clientexists ? 1 : 0;
        } elseif($request->type == 'clientid'){
            $clientexists = Admin::where('client_id', $request->vl)->where('role', 7)->exists();
            echo $clientexists ? 1 : 0;
        } else {
            $clientexists = Admin::where('phone', $request->vl)->where('role', 7)->exists();
            echo $clientexists ? 1 : 0;
        }
    }

    /**
     * Change client type (client/lead)
     */
    public function changetype(Request $request,$id = Null, $slug = Null){
        if(isset($id) && !empty($id))
        {
            $id = $this->decodeString($id);
            if(Admin::where('id', '=', $id)->where('role', '=', '7')->exists())
            {
                $obj = Admin::find($id);
                $obj->type = $slug;
                $saved = $obj->save();

                return redirect()->route('clients.detail', ['id' => base64_encode(convert_uuencode(@$id))])->with('success', 'Record Updated successfully');
            }
            else
            {
                return redirect()->route('clients.index')->with('error', 'Clients Not Exist');
            }
        }
        else
        {
            return redirect()->route('clients.index')->with('error', Config::get('constants.unauthorized'));
        }
    }

    /**
     * Export client data to JSON file
     * 
     * @param int $id Client ID
     * @return \Illuminate\Http\Response
     */
    public function export($id)
    {
        try {
            $client = Admin::where('id', $id)
                ->where('role', 7)
                ->first();

            if (!$client) {
                return redirect()->route('clients.index')
                    ->with('error', 'Client not found.');
            }

            $exportService = app(ClientExportService::class);
            $exportData = $exportService->exportClient($id);

            $filename = 'client_export_' . ($client->client_id ?? $id) . '_' . date('Y-m-d_His') . '.json';

            return response()->json($exportData, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                ->header('Content-Type', 'application/json')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            \Log::error('Client export error: ' . $e->getMessage(), [
                'client_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('clients.index')
                ->with('error', 'Failed to export client data: ' . $e->getMessage());
        }
    }

    /**
     * Import client data from JSON file
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        try {
            $request->validate([
                'import_file' => 'required|file|mimes:json|max:10240',
            ]);

            $file = $request->file('import_file');
            $jsonContent = file_get_contents($file->getRealPath());
            $importData = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()
                    ->withErrors(['import_file' => 'Invalid JSON file: ' . json_last_error_msg()])
                    ->withInput();
            }

            if (!isset($importData['client'])) {
                return redirect()->back()
                    ->withErrors(['import_file' => 'Invalid import file format: missing client data'])
                    ->withInput();
            }

            if (empty($importData['client']['email'])) {
                return redirect()->back()
                    ->withErrors(['import_file' => 'Client email is required and cannot be empty'])
                    ->withInput();
            }

            if (empty($importData['client']['first_name'])) {
                return redirect()->back()
                    ->withErrors(['import_file' => 'Client first name is required'])
                    ->withInput();
            }

            $skipDuplicates = $request->has('skip_duplicates');
            $importService = app(ClientImportService::class);
            $result = $importService->importClient($importData, $skipDuplicates);

            if ($result['success']) {
                return redirect()->route('clients.index')
                    ->with('success', $result['message']);
            } else {
                return redirect()->back()
                    ->withErrors(['import_file' => $result['message']])
                    ->withInput();
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Get the first validation error message for better UX
            $firstError = $e->validator->errors()->first();
            return redirect()->back()
                ->withErrors(['import_file' => $firstError])
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Client import error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $request->file('import_file') ? $request->file('import_file')->getClientOriginalName() : 'unknown'
            ]);

            // Provide more user-friendly error messages
            $errorMessage = $e->getMessage();
            
            // Check for file-related errors
            if (strpos($errorMessage, 'file_get_contents') !== false || strpos($errorMessage, 'failed to open stream') !== false) {
                $errorMessage = 'File error: Could not read the uploaded file. Please ensure the file is not corrupted and try again.';
            } elseif (strpos($errorMessage, 'json_decode') !== false) {
                $errorMessage = 'JSON error: The file is not a valid JSON file. Please check the file format.';
            } elseif (strpos($errorMessage, 'mimes') !== false || strpos($errorMessage, 'mime type') !== false) {
                $errorMessage = 'File type error: Please upload a valid JSON file (.json extension).';
            } elseif (strpos($errorMessage, 'max:') !== false) {
                $errorMessage = 'File size error: The file is too large. Maximum file size is 10MB.';
            }

            return redirect()->back()
                ->withErrors(['import_file' => $errorMessage])
                ->withInput();
        }
    }
}
