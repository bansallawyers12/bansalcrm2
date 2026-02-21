<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

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
     * All leads from admins table (type='lead').
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

		$baseQuery = Admin::where('type', 'lead')->where('converted', 0);

		$not_contacted = (clone $baseQuery)->where('assignee', Auth::user()->id)->where('status', 0)->count();
		$create_porposal = (clone $baseQuery)->where('assignee', Auth::user()->id)->where('status', 1)->count();
		$followup = (clone $baseQuery)->where('assignee', Auth::user()->id)->where('status', 15)->count();
		$undecided = (clone $baseQuery)->where('assignee', Auth::user()->id)->where('status', 11)->count();
		$lost = (clone $baseQuery)->where('assignee', Auth::user()->id)->where('status', 12)->count();
		$won = (clone $baseQuery)->where('assignee', Auth::user()->id)->where('status', 13)->count();
		$ready_to_pay = (clone $baseQuery)->where('assignee', Auth::user()->id)->where('status', 14)->count();
		$todaycall = (clone $baseQuery)->where('assignee', Auth::user()->id)->where('status', 15)
			->whereExists(function ($q) {
				$q->select(DB::raw(1))->from('followups')
					->where(function ($q2) {
						$q2->whereColumn('followups.lead_id', 'admins.lead_id')
							->orWhereColumn('followups.client_id', 'admins.id');
					})
					->whereDate('followups.followup_date', Carbon::today());
			})->count();

		$query = clone $baseQuery;

		$totalData = $query->count();
		if ($request->has('type')) 
		{	
			 $type 		= 	$request->input('type'); 
			if(trim($type) != '')
			{
				if($type != 'not_contacted' && $type != 'today'){
					$FollowupType = FollowupType::where('type', '=', $type)->first();
					
					$query->where('status', '=', @$FollowupType->id);
				}else if($type == 'today'){
					
					$query->whereExists(function ($q) {
						$q->select(DB::raw(1))->from('followups')
							->where(function ($q2) {
								$q2->whereColumn('followups.lead_id', 'admins.lead_id')
									->orWhereColumn('followups.client_id', 'admins.id');
							})
							->whereDate('followups.followup_date', Carbon::today());
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
				$query->where(function ($q) use ($lead_id) {
					$q->where('lead_id', '=', $lead_id)->orWhere('id', '=', $lead_id);
				});
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
			   
				$query->whereExists(function ($q) use ($followupdate) {
					$q->select(DB::raw(1))->from('followups')
						->where(function ($q2) {
							$q2->whereColumn('followups.lead_id', 'admins.lead_id')
								->orWhereColumn('followups.client_id', 'admins.id');
						})
						->whereDate('followups.followup_date', $followupdate)
						->whereNotNull('followups.followup_date');
				});
			}
		}
	if ($request->has('type') || $request->has('lead_id') || $request->has('email')|| $request->has('name') || $request->has('phone') || $request->has('status')|| $request->has('followupdate')) 
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
		// Admin model only: support migrated (lead_id) and admin-only (admins.id)
		$admin = Admin::where('lead_id', '=', $id)->where('type', 'lead')->first()
			?? Admin::where('id', '=', $id)->where('type', 'lead')->first();
		if ($admin) 
		{
			$currentAssignee = $admin->assignee;
			if($currentAssignee != '' && $currentAssignee != null){
				if($currentAssignee == $requestData['assignto']){
					return redirect()->back()->with('error', 'Already Assigned to this user');
				}
				$assignfrom = \App\Models\Staff::find($currentAssignee);
				$assignto = \App\Models\Staff::find($requestData['assignto']);
				if (!$assignfrom || !$assignto) {
					return redirect()->back()->with('error', 'Invalid assignee');
				}
			}
			Admin::where('id', $admin->id)->update(['assignee' => $requestData['assignto']]);
			$saved = true;
			// Create followup only when changing from one assignee to another (original behavior)
			if (isset($assignfrom) && $assignfrom) {
				$assignto = \App\Models\Staff::find($requestData['assignto']);
				$followup = new Followup;
				// For admin-only leads use client_id; for migrated use lead_id
				if ($admin->lead_id === null) {
					$followup->client_id = $admin->id;
				} else {
					$followup->lead_id = $admin->lead_id;
				}
				$followup->user_id = Auth::user()->id;
				$followup->note = $assignto ? 'changed from '.$assignfrom->first_name.' '.$assignfrom->last_name.' to '.$assignto->first_name.' '.$assignto->last_name : 'Assigned';
				$followup->followup_type = 'assigned_to';
				$followup->save();
			}
			if(!$saved) {
				return redirect()->back()->with('error', 'Please try again');
			}
			return redirect()->back()->with('success', 'Lead Assigned successfully');
			} else {
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
									'phone' => 'required|max:255|unique:admins,phone',
										'email_type' => 'required|in:Personal,Work,Business,Secondary,Additional,Sister,Brother,Father,Mother,Uncle,Auntie',
										'email' => 'required|max:255|unique:admins,email',
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

			// Save to admins table only (type=lead), not to leads table
			$adminId = $this->createAdminFromRequestData($requestData, $dob, $visa_expiry_date, $related_files);
			if (!$adminId) {
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}

			return redirect()->route('leads.detail', base64_encode(convert_uuencode($adminId)))->with('success', 'Lead added Successfully'); 				
		}	
	} 
	
	/**
	 * Create Admin row from request data (saves to admins only, lead_id=null for new leads).
	 * @return int|null Admin id or null on failure
	 */
	protected function createAdminFromRequestData(array $requestData, string $dob, string $visa_expiry_date, string $related_files): ?int
	{
		if (!Schema::hasTable('admins')) {
			return null;
		}

		$assignee = null;
		if (isset($requestData['assign_to']) && is_array($requestData['assign_to'])) {
			$assignee = $requestData['assign_to'][0];
		} else {
			$assignee = $requestData['assign_to'] ?? null;
		}

		$officeId = null;
		if ($assignee) {
			$staff = \App\Models\Staff::find($assignee);
			$officeId = $staff ? $staff->office_id : null;
		}

		$age = isset($requestData['age']) && $requestData['age'] != '' ? preg_replace('/[^0-9]/', '', $requestData['age']) : null;
		$tags = '';
		if (isset($requestData['tagname']) && !empty($requestData['tagname']) && is_array($requestData['tagname'])) {
			$tags = implode(',', $requestData['tagname']);
		}
		$naatiPy = '';
		if (isset($requestData['naati_py']) && !empty($requestData['naati_py']) && is_array($requestData['naati_py'])) {
			$naatiPy = implode(',', $requestData['naati_py']);
		}

		$adminCols = Schema::getColumnListing('admins');
		$now = now();
		$data = [
			'type' => 'lead',
			'remember_token' => null,
			'lead_id' => null,
			'first_name' => $requestData['first_name'] ?? null,
			'last_name' => $requestData['last_name'] ?? null,
			'email' => trim((string) ($requestData['email'] ?? '')),
			'password' => bcrypt(Str::random(32)),
			'phone' => $requestData['phone'] ?? null,
			'country_code' => PhoneHelper::normalizeCountryCode($requestData['country_code'] ?? null),
			'gender' => $requestData['gender'] ?? null,
			'dob' => ($dob != '') ? $dob : null,
			'marital_status' => $requestData['marital_status'] ?? null,
			'address' => $requestData['address'] ?? null,
			'city' => $requestData['city'] ?? null,
			'state' => $requestData['state'] ?? null,
			'zip' => $requestData['zip'] ?? null,
			'country' => $requestData['country'] ?? null,
			'user_id' => Auth::user()->id,
			'assignee' => $assignee,
			'office_id' => $officeId,
			'source' => $requestData['source'] ?? null,
			'tags' => $tags,
			'passport_number' => $requestData['passport_no'] ?? null,
			'visaexpiry' => ($visa_expiry_date != '') ? $visa_expiry_date : null,
			'visa_type' => $requestData['visa_type'] ?? null,
			'nomi_occupation' => $requestData['nomi_occupation'] ?? null,
			'skill_assessment' => $requestData['skill_assessment'] ?? null,
			'high_quali_aus' => $requestData['high_quali_aus'] ?? null,
			'high_quali_overseas' => $requestData['high_quali_overseas'] ?? null,
			'relevant_work_exp_aus' => $requestData['relevant_work_exp_aus'] ?? null,
			'relevant_work_exp_over' => $requestData['relevant_work_exp_over'] ?? null,
			'naati_py' => $naatiPy,
			'married_partner' => $requestData['married_partner'] ?? null,
			'total_points' => $requestData['total_points'] ?? null,
			'comments_note' => $requestData['comments_note'] ?? null,
			'service' => $requestData['service'] ?? null,
			'lead_quality' => $requestData['lead_quality'] ?? null,
			'country_passport' => $requestData['country_passport'] ?? null,
			'contact_type' => $requestData['contact_type'] ?? null,
			'email_type' => $requestData['email_type'] ?? null,
			'related_files' => rtrim($related_files, ','),
			'status' => $requestData['status'] ?? 0,
			'verified' => 0,
			'is_archived' => 0,
			'show_dashboard_per' => 0,
			'created_at' => $now,
			'updated_at' => $now,
			'converted' => 0,
			'is_verified' => 0,
			'verified_at' => null,
			'verified_by' => null,
		];

		$data = array_intersect_key($data, array_flip($adminCols));
		$adminId = DB::table('admins')->insertGetId($data);

		$firstName = substr((string) ($requestData['first_name'] ?? ''), 0, 4);
		$clientId = strtoupper(preg_replace('/[^A-Za-z]/', '', $firstName) ?: 'LEAD') . date('ym') . $adminId;
		if (in_array('client_id', $adminCols, true)) {
			DB::table('admins')->where('id', $adminId)->update(['client_id' => $clientId]);
		}

		return (int) $adminId;
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
	public function convertoClient(Request $request, $id = null)
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
			// preferredIntake column removed
			$obj->country_passport			=	@$enqdata->country_passport;
			$obj->passport_number			=	@$enqdata->passport_no;
			$obj->visa_type			=	@$enqdata->visa_type;
			$obj->visaExpiry			=	@$enqdata->visa_expiry_date;
			//$obj->applications	=@$enqdata->first_name;
			$obj->assignee	=	@$enqdata->assign_to;
			
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
			// profile_img column removed
			
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
		        	    $objn->is_action = 0; // Required NOT NULL field (0 = not a followup, 1 = followup)
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
        // Admins table holds both clients and leads - single count
        $email_count = \App\Models\Admin::where('email',$email)->count();
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
        // Admins table holds both clients and leads - single count
        $phone_count = \App\Models\Admin::where('phone',$contact)->count();
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
