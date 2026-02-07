<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Lead;
use App\Models\FollowupType;
use App\Models\Followup;
 
use Auth; 
use Config;
use Carbon\Carbon;
use App\Helpers\PhoneHelper;

class LeadController extends Controller
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
			/* $check = $this->checkAuthorizationAction('lead_management', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}*/	
		//check authorization end
		 $not_contacted = Lead::where('assign_to', '=', Auth::user()->id)->where('status', '=', 0)->count();
			$create_porposal = Lead::where('assign_to', '=', Auth::user()->id)->where('status', '=', 1)->count();
			$followup = Lead::where('assign_to', '=', Auth::user()->id)->where('status', '=', 15)->count();
			$undecided = Lead::where('assign_to', '=', Auth::user()->id)->where('status', '=', 11)->count();
			$lost = Lead::where('assign_to', '=', Auth::user()->id)->where('status', '=', 12)->count();
			$won = Lead::where('assign_to', '=', Auth::user()->id)->where('status', '=', 13)->count();
			$ready_to_pay = Lead::where('assign_to', '=', Auth::user()->id)->where('status', '=', 14)->count();
			$todaycall = Lead::where('assign_to', '=', Auth::user()->id)->where('status', '=', 15)->whereHas('followupload', function ($q) {
					$q->whereDate('followup_date',Carbon::today());
						})->count();
		$query 		= Lead::whereNotNull('user_id')->where('converted', '=', 0)->with(['staffuser']); 
		
		  
		$totalData 	= $query->count();	//for all data
		if ($request->has('type')) 
		{	
			 $type 		= 	$request->input('type'); 
			if(trim($type) != '')
			{
				if($type != 'not_contacted' && $type != 'today'){
					$FollowupType = FollowupType::where('type', '=', $type)->first();
					
					$query->where('status', '=', @$FollowupType->id);
				}else if($type == 'today'){
					
					$query->whereHas('followupload', function ($q) {
					$q->whereDate('followup_date',Carbon::today());
						});
				}else{
					$query->where('status', '=', 0);
				}
				
			}	
		}
		if ($request->has('id')) 
		{
			$lead_id 		= 	$request->input('id'); 
			if(trim($lead_id) != '')
			{
				$query->where('id', '=', @$lead_id);
			}
		}
		if ($request->has('email')) 
		{
			$email 		= 	$request->input('email'); 
			if(trim($email) != '')
			{
				$query->where('email', '=', @$email);
			}
		}if ($request->has('name')) 
		{
			$name 		= 	$request->input('name'); 
			if(trim($name) != '')
			{
			$query	->where(DB::raw("COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')"), 'ilike', "%".$name."%");
			}
		}if ($request->has('phone')) 
		{
			$phone 		= 	$request->input('phone'); 
			if(trim($phone) != '')
			{
				$query->where('phone', '=', @$phone);
			}
		}if ($request->has('status')) 
		{
			$status 		= 	$request->input('status'); 
			if(trim($status) != '')
			{
				$query->where('status', '=', @$status);
			}
		}
		if ($request->has('from')) 
		{
			$from 		= 	$request->input('from'); 
			if(trim($from) != '')
			{
				$query->whereDate('created_at', '>=', @$from);
			}

		}if ($request->has('to')) 
		{
			$to 		= 	$request->input('to'); 
			if(trim($to) != '')
			{
				$query->whereDate('created_at', '<=', @$to);
			}

		}
		if ($request->has('followupdate')) 
		{
			$followupdate 		= 	$request->input('followupdate'); 
			if(trim($followupdate) != '')
			{
			   
				$query->whereHas('likes', function ($q) use($followupdate){
					$q->whereDate('followup_date',$followupdate)->whereNotNull('followup_date');
				});
			}
		}
		if ($request->has('priority')) 
		{
			$priority 		= 	$request->input('priority'); 
			if(trim($priority) != '')
			{
				$query->where('priority', '=', @$priority);
			}
		}
	if ($request->has('type') || $request->has('lead_id') || $request->has('email')|| $request->has('name') || $request->has('phone') || $request->has('status')|| $request->has('followupdate') || $request->has('priority')) 
		{
			$totalData 	= $query->count();//after search
		}
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit')); 
		$cur_url = $request->fullUrl();
		return view('Admin.leads.index',compact(['lists', 'totalData', 'not_contacted', 'create_porposal', 'followup', 'undecided', 'lost', 'won', 'ready_to_pay', 'cur_url', 'todaycall'])); 

	}   
	
	public function create(Request $request) 
	{
		//check authorization start	
			/* $check = $this->checkAuthorizationAction('add_lead', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}*/	 
		//check authorization end
		
		return view('Admin.leads.create');
	}
	
	public function assign(Request $request) {
		$requestData 		= 	$request->all();
		$id = $this->decodeString($requestData['mlead_id']);	 
		if(Lead::where('id', '=', $id)->where('user_id', '=', Auth::user()->id)->exists()) 
		{
			$leads = Lead::where('id', '=', $id)->where('user_id', '=', Auth::user()->id)->first();
			if($leads->assign_to != ''){
				if($leads->assign_to == $requestData['assignto']){
					return redirect()->back()->with('error', 'Already Assigned to this user');
				}else{
					$assignfrom = Admin::where('id',$leads->assign_to)->first();
					$assignto = Admin::where('id',$requestData['assignto'])->first();
					$ld = Lead::find($id);
					$ld->assign_to = $requestData['assignto'];
					$ld->save();
					$followup 					= new Followup;
					$followup->lead_id			= @$id;
					$followup->user_id			= Auth::user()->id;
					$followup->note				= 'changed from '.$assignfrom->first_name.' '.$assignfrom->last_name.' to '.$assignto->first_name.' '.$assignto->last_name;
					$followup->followup_type	= 'assigned_to';
					$saved				=	$followup->save();  
					if(!$saved) 
					{
						return redirect()->back()->with('error', 'Please try again');
					}else{
						return redirect()->back()->with('success', 'Lead transfer successfully');
					}
				}
			}else{
				$ld = Lead::find($id);
				$ld->assign_to = $requestData['assignto'];
				$saved		= $ld->save();
				if(!$saved) 
					{
						return redirect()->back()->with('error', 'Please try again');
					}else{
						return redirect()->back()->with('success', 'Lead Assigned successfully');
					}
			}
		}else{
			return redirect()->back()->with('error', 'Not Found');
		}
	}
	
	 public function store(Request $request)
	{
		//check authorization start	
			  $check = $this->checkAuthorizationAction('add_lead', $request->route()->getActionMethod(), Auth::user()->role);
			if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			}	 
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'first_name' => 'required|max:255',
										'last_name' => 'required|max:255',
										'gender' => 'required|in:Male,Female,Other',
										'contact_type' => 'required|in:Personal,Office,Work,Mobile,Business,Secondary,Father,Mother,Brother,Sister,Uncle,Aunt,Cousin,Others,Partner,Not In Use',
									'phone' => 'required|max:255|unique:admins,phone|unique:leads,phone',
										'email_type' => 'required|in:Personal,Work,Business,Secondary,Additional,Sister,Brother,Father,Mother,Uncle,Auntie',
										'email' => 'required|max:255|unique:admins,email|unique:leads,email',
										'service' => 'required',
										'assign_to' => 'required|array|min:1',
										'assign_to.*' => 'required|integer',
										'lead_quality' => 'required',
										'source' => 'required',
									
									  ]);
			
			$requestData 		= 	$request->all();
		
			$related_files = '';
	        if(isset($requestData['related_files'])){
	            for($i=0; $i<count($requestData['related_files']); $i++){
	                $related_files .= $requestData['related_files'][$i].',';
	            }
	            
	        }
			  $dob = '';
	        if(isset($requestData['dob']) && $requestData['dob'] != ''){
	           $dobs = explode('/', $requestData['dob']);
	          $dob = $dobs[2].'-'.$dobs[1].'-'. $dobs[0]; 
	        }
	         $visa_expiry_date = '';
	        if(isset($requestData['visa_expiry_date']) && $requestData['visa_expiry_date'] != ''){
	           $visa_expiry_dates = explode('/', $requestData['visa_expiry_date']);
	          $visa_expiry_date = $visa_expiry_dates[2].'-'.$visa_expiry_dates[1].'-'. $visa_expiry_dates[0]; 
	        }
			$obj				= 	new Lead; 
			$obj->user_id	=	Auth::user()->id;   
			$obj->first_name		=	@$requestData['first_name'];
			$obj->last_name		=	@$requestData['last_name'];
			$obj->gender		=	@$requestData['gender'];
			$obj->dob		=	($dob != '') ? $dob : null;
			// Extract numeric value from age field (handles cases like "13 years" -> 13)
			$age = isset($requestData['age']) && $requestData['age'] != '' ? preg_replace('/[^0-9]/', '', $requestData['age']) : null;
			$obj->age		=	($age != '' && is_numeric($age)) ? (int)$age : null;
			$obj->marital_status		=	@$requestData['marital_status'];
			$obj->passport_no		=	@$requestData['passport_no'];
			$obj->visa_type			=	@$requestData['visa_type'];
			$obj->visa_expiry_date		=	($visa_expiry_date != '') ? $visa_expiry_date : null;
			// Handle tags_label - convert array to comma-separated string
			if(isset($requestData['tagname']) && !empty($requestData['tagname']) && is_array($requestData['tagname'])){
				$obj->tags_label = implode(',', $requestData['tagname']);
			} else {
				$obj->tags_label = '';
			}
			$obj->contact_type		=	@$requestData['contact_type'];
			$obj->country_code		=	PhoneHelper::normalizeCountryCode(@$requestData['country_code']);
			$obj->phone		=	@$requestData['phone'];
			$obj->email_type		=	@$requestData['email_type'];
			$obj->email		=	@$requestData['email'];			
		    //$obj->social_type		=	@$requestData['social_type'];			
			//$obj->social_link		=	@$requestData['social_link'];			
			$obj->service		=	@$requestData['service'];			
			// Handle assign_to - convert array to single value (take first selected admin)
			if(isset($requestData['assign_to']) && is_array($requestData['assign_to'])){
				$obj->assign_to = $requestData['assign_to'][0]; // Take first value
			} else {
				$obj->assign_to = @$requestData['assign_to'];
			}
			$obj->status		=	@$requestData['status'];				 
			$obj->converted		=	0; // New leads are not converted yet
			$obj->lead_quality		=	@$requestData['lead_quality'];		
			$obj->att_country_code		=	PhoneHelper::normalizeCountryCode(@$requestData['att_country_code']);
			$obj->att_phone		=	@$requestData['att_phone'];
				$obj->att_email		=	@$requestData['att_email'];
			$obj->lead_source		=	@$requestData['source'];	
			$obj->related_files	=	rtrim($related_files,',');
		//	$obj->advertisements_name		=	@$requestData['advertisements_name'];
			$obj->comments_note		=	@$requestData['comments_note'];				 
    		/* Profile Image Upload Function Start */						  
    			if($request->hasfile('profile_img')) 
    			{	
    				$profile_img = $this->uploadFile($request->file('profile_img'), Config::get('constants.profile_imgs'));
    			}
    			else
    			{
    				$profile_img = NULL;
    			}	
    		$obj->profile_img			=	@$profile_img;
    		$obj->preferredIntake			=	@$requestData['preferredIntake'];
    		$obj->country_passport			=	@$requestData['country_passport'];
    		$obj->address			=	@$requestData['address'];
    		$obj->city			=	@$requestData['city'];
    		$obj->state			=	@$requestData['state'];
    		$obj->zip			=	@$requestData['zip'];
    		$obj->country			=	@$requestData['country'];
    		$obj->nomi_occupation			=	@$requestData['nomi_occupation'];
    		$obj->skill_assessment			=	@$requestData['skill_assessment'];
    		$obj->high_quali_aus			=	@$requestData['high_quali_aus'];
    		$obj->high_quali_overseas			=	@$requestData['high_quali_overseas'];
			$obj->relevant_work_exp_aus			=	@$requestData['relevant_work_exp_aus'];
			$obj->relevant_work_exp_over			=	@$requestData['relevant_work_exp_over'];
			// Handle naati_py - convert array to comma-separated string
			if(isset($requestData['naati_py']) && !empty($requestData['naati_py']) && is_array($requestData['naati_py'])){
				$obj->naati_py = implode(',', $requestData['naati_py']);
			} else {
				$obj->naati_py = '';
			}
			$obj->married_partner			=	@$requestData['married_partner'];
    		$obj->total_points			=	@$requestData['total_points'];
    		/* Profile Image Upload Function End */	
			$saved				=	$obj->save();  
			
			if(!$saved) 
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{ 
				return redirect()->route('leads.index')->with('success', 'Lead added Successfully');
			} 				
		}	
	} 
	
	/* REMOVED: Broken edit method - Leads now use the detail page (ClientsController@detail) for viewing and editing
	 * The detail page provides a much richer interface with tabs for notes, activities, documents, etc.
	 * This old edit method had issues:
	 * - Missing CSRF token handling
	 * - No proper method specification in form
	 * - Route mismatch between GET and POST
	 * - Form submission failures (405 errors)
	 * 
	 * To edit a lead, users should now click on the lead to open the detail page.
	 */ 
	
	public function leadPin(Request $request, $id)
	{
	    if(Followup::where('id', $id)->exists()){
	        $a = Followup::find($id);
	        if($a->pin == 1){
	           $a->pin =  0;
	        }else{
	           $a->pin =  1;  
	        }
	        $save = $a->save();
	        if($save){
	            return redirect()->route('leads.index')->with('success', 'Record Updated successfully');
	        }else{
	            return redirect()->route('leads.index')->with('error', 'Please try again');
	        }
	    }
	}
	public function convertoClient(Request $request)
	{ 
		$requestData 		= 	$request->all();
		$enqdatas = Lead::query()->paginate(500);
	//	if(Lead::where('id', $id)->exists()){
		foreach($enqdatas as $lead){
		    $id = $lead->id;
			$enqdata = Admin::where('lead_id', $id)->first();
			if($enqdata){
			$obj = Admin::find($enqdata->id);
			$obj->created_at = $lead->created_at;
			$obj->updated_at = $lead->updated_at;
			$obj->save();
			}
			/*if(!Admin::where('email', $enqdata->email)->exists()){
				$first_name = substr(@$enqdata->first_name, 0, 4);
				$obj				= 	new Admin;
				$obj->role	=	7;
					$obj->lead_id	=	$id;
			$obj->first_name	=	@$enqdata->first_name;
			$obj->last_name	=	@$enqdata->last_name;
			$obj->age	=	@$enqdata->first_name;
			$obj->dob	=		@$enqdata->dob;
			$obj->gender = @$enqdata->gender;
			$obj->marital_status	=	@$enqdata->marital_status;
			$obj->contact_type	=	@$enqdata->contact_type;
			$obj->email_type	=	@$enqdata->email_type;
			$obj->service	=	@$enqdata->service;
			$obj->related_files	=	@$enqdata->related_files;
			$obj->email	=	@$enqdata->email;
			$obj->phone	=	@$enqdata->phone;
			$obj->address	=	@$enqdata->address;
			$obj->city	=	@$enqdata->city;
			$obj->state	=	@$enqdata->state;
			$obj->zip	=	@$enqdata->zip;
			$obj->country	=	@$enqdata->country;
			$obj->preferredIntake	=	@$enqdata->preferredIntake;
			$obj->country_passport			=	@$enqdata->country_passport;
			$obj->passport_number			=	@$enqdata->passport_no;
			$obj->visa_type			=	@$enqdata->visa_type;
			$obj->visaExpiry			=	@$enqdata->visa_expiry_date;
			//$obj->applications	=@$enqdata->first_name;
			$obj->assignee	=	@$enqdata->assign_to;
			
			$obj->att_phone	=	@$enqdata->att_phone;
			$obj->att_country_code	=@$enqdata->att_country_code;
			$obj->att_email	=	@$enqdata->att_email;
			$obj->nomi_occupation	=@$enqdata->nomi_occupation;
			$obj->skill_assessment	=@$enqdata->skill_assessment;
			$obj->high_quali_aus	=@$enqdata->high_quali_aus;
			$obj->high_quali_overseas	=	@$enqdata->high_quali_overseas;
			$obj->relevant_work_exp_aus	=	@$enqdata->relevant_work_exp_aus;
			$obj->relevant_work_exp_over	=	@$enqdata->relevant_work_exp_over;
			$obj->naati_py	=	@$enqdata->naati_py;
			$obj->married_partner	=@$enqdata->married_partner;
			$obj->total_points	=@$enqdata->total_points;
			$obj->source	=	@$enqdata->lead_source;
			$obj->comments_note	=	@$enqdata->comments_note;
			$obj->type	=	'lead';
			$obj->profile_img			=@$enqdata->profile_img;
			
				$saved				=	$obj->save(); 
			$objs							= 	Admin::find($obj->id);
		    	$objs->client_id	=	strtoupper($first_name).date('ym').$obj->id;
		    	$saveds				=	$objs->save();  	
				
				if(!$saved)
				{
					$response['status'] 	= 	false;
					$response['message']	=	'Please try again';
					return redirect()->route('leads.index')->with('error', 'Please try again');
				}
				else
				{
				    $o = Lead::find($id);
				    $o->converted = 1;
				    $o->converted_date = date('Y-m-d');
				    $o->save();
				    $Followups = Followup::where('lead_id', $id)->get();
				    foreach($Followups as $Followup){
	                	$Followupstype = FollowupType::where('type', $Followup->followup_type)->first();
	                	$r = '';
	                	if(@$Followup->subject != ''){
	                	    $r .= @$Followup->subject.'<br>';
	                	}
	                	if(@$Followup->followup_date != ''){
	                	    $r .= @$Followup->followup_date.'<br>';
	                	}
	                	if(@$Followup->note != ''){
	                	    $r .= @$Followup->note;
	                	}
				        $objn = new \App\Models\Note;
				        $objn->client_id = $obj->id;
		            	$objn->user_id = Auth::user()->id;
		        	    $objn->title = @$Followupstype->name;
		        	    $objn->description = $r;
		        	    $objn->mail_id = 0;
		        	    $objn->type = 'client';
		        	    // Set required NOT NULL fields for PostgreSQL
		        	    $objn->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
		        	    $objn->folloup = 0; // Required NOT NULL field (0 = not a followup, 1 = followup)
		        	    $objn->status = 0; // Required NOT NULL field (0 = active/open, 1 = closed/completed)
		        	    $objn->save();
				    }
			
    				$enq = new Followup;
    				$enq->lead_id = $id;
    				$enq->user_id = @Auth::user()->id;
    				$enq->note = 'Lead converted to client';
    				$enq->followup_type = 'converted';
    				$enq->save(); 
					$response['status'] 	= 	true;
					$response['message']	=	'Client saved successfully';
				//	return Redirect::to('/admin/leads')->with('success', 'Client saved successfully');
				}
			}*/
			echo $id.'<br>';
		}
		//	echo json_encode($response);
		//}
	}
	
	public function leaddeleteNotes(Request $request, $id = Null){
	    if(isset($id) && !empty($id)) 
			{
		 
				if(Followup::where('id', '=', $id)->exists()) 
				{
			    $leadid = Followup::where('id', '=', $id)->first()->lead_id;
			    $res = Followup::where('id', '=', $id)->delete();
				if($res){
				    return redirect()->route('leads.index')->with('success', 'Record deleted successfully');
				}else{
				    return redirect()->route('leads.index')->with('error', 'Lead Not Exist');
				}
				}
				else
				{
					return redirect()->route('leads.index')->with('error', 'Lead Not Exist');
				}	
			}
			else
			{
				return redirect()->route('leads.index')->with('error', Config::get('constants.unauthorized'));
			}
	}
	
	public function getnotedetail(Request $request){
	    $id = $request->id;
	    if(Followup::where('id', '=', $id)->exists()) 
		{
		    $fetchedData = Followup::where('id', '=', $id)->first();
		    	return view('Admin.leads.editnotemodal', compact(['fetchedData']));
		}else{
		    echo 'No Found';
		}
	}
	
	//Check Email is unique or not
    public function is_email_unique(Request $request){
        $email = $request->email;
        $email_count_admin = \App\Models\Admin::where('email',$email)->count();
        $email_count_lead = \App\Models\Lead::where('email',$email)->count();
        $email_count = $email_count_admin + $email_count_lead;
        if($email_count >0){
            $response['status'] 	= 	1;
            $response['message']	=	"The email has already been taken.";
        }else{
            $response['status'] 	= 	0;
            $response['message']	=	"";
        }
        echo json_encode($response);
    }

    //Check Contact no is unique or not
    public function is_contactno_unique(Request $request){
        $contact = $request->contact;
        $phone_count_admin = \App\Models\Admin::where('phone',$contact)->count();
        $phone_count_lead = \App\Models\Lead::where('phone',$contact)->count();
        $phone_count = $phone_count_admin + $phone_count_lead;
        if($phone_count >0){
            $response['status'] 	= 	1;
            $response['message']	=	"The phone has already been taken.";
        }else{
            $response['status'] 	= 	0;
            $response['message']	=	"";
        }
        echo json_encode($response);
    }
}
