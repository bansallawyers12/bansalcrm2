<?php
namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Admin;
use App\ActivitiesLog;
use App\ServiceFeeOption;
use App\ServiceFeeOptionType;
   
use Auth;
use Config;

class ClientsController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:agents');
    }
    
    public function index(Request $request)
	{
	
	
		$query 		= Admin::where('is_archived', '=', '0')->where('role', '=', '7')->where('agent_id', '=', Auth::user()->id); 
		  
		$totalData 	= $query->count();	//for all data
		if ($request->has('client_id')) 
		{
			$client_id 		= 	$request->input('client_id'); 
			if(trim($client_id) != '')
			{
				$query->where('client_id', '=', $client_id);
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
				$query->where('email', $email);
					
			}
		}
		
		if ($request->has('phone')) 
		{
			$phone 		= 	$request->input('phone'); 
			if(trim($phone) != '')
			{
				$query->where('phone', $phone);
					
			}
		}
		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		
		
		return view('Agent.clients.index', compact(['lists', 'totalData'])); 	
				
		//return view('Admin.clients.index');	 
	}
	
	public function archived(Request $request)
	{
		 $query 		= Admin::where('is_archived', '=', '1')->where('role', '=', '7')->where('agent_id', '=', Auth::user()->id); 
		  
		$totalData 	= $query->count();	//for all data

		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		
		
		
		return view('Agent.archived.index', compact(['lists', 'totalData'])); 	
 
	}
	
	public function prospects(Request $request)
	{
		
		return view('Agent.prospects.index'); 	
 
	}
	
		public function create(Request $request)
	{
		//check authorization end
		//return view('Admin.users.create',compact(['usertype']));	
		
		return view('Agent.clients.create');	
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
										'client_id' => 'required|max:255|unique:admins,client_id'
									  ]);
			
			$requestData 		= 	$request->all();
			$related_files = '';
	        if(isset($requestData['related_files'])){
	            for($i=0; $i<count($requestData['related_files']); $i++){
	                $related_files .= $requestData['related_files'][$i].',';
	            }
	            
	        }
			$first_name = substr(@$requestData['first_name'], 0, 4);
			$obj				= 	new Admin;
			$obj->first_name	=	@$requestData['first_name'];
			$obj->last_name	=	@$requestData['last_name'];
			$obj->dob	=	@$requestData['dob'];
			$obj->related_files	=	rtrim($related_files,',');
			$obj->email	=	@$requestData['email'];
			$obj->phone	=	@$requestData['phone'];
			$obj->address	=	@$requestData['address'];
			$obj->city	=	@$requestData['city'];
			$obj->state	=	@$requestData['state'];
			$obj->zip	=	@$requestData['zip'];
			$obj->country	=	@$requestData['country'];
			$obj->preferredIntake	=	@$requestData['preferredIntake'];
			$obj->country_passport			=	@$requestData['country_passport'];
			$obj->passport_number			=	@$requestData['passport_number'];
			$obj->visa_type			=	@$requestData['visa_type'];
			$obj->visaExpiry			=	@$requestData['visaExpiry'];
			$obj->applications	=	@$requestData['applications'];
			$obj->assignee	=	@$requestData['assignee'];
			
			$obj->att_phone	=	@$requestData['att_phone'];
			$obj->att_country_code	=	@$requestData['att_country_code'];
			$obj->att_email	=	@$requestData['att_email'];
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
			
				$obj->agent_id	=	Auth::user()->id;
			
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
				return Redirect::to('/agent/clients')->with('success', 'Clients Added Successfully');
			}				
		}	

		return view('Agent.clients.create');	 
	}
	
	public function edit(Request $request, $id = NULL)
	{
	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			//echo '<pre>'; print_r($requestData); die;
			$this->validate($request, [										
										'first_name' => 'required|max:255',
										'last_name' => 'required|max:255',
										'email' => 'required|max:255|unique:admins,email,'.$requestData['id'],
										'phone' => 'required|max:255|unique:admins,phone,'.$requestData['id'],
										'client_id' => 'required|max:255|unique:admins,client_id,'.$requestData['id']
										
									  ]);
				$related_files = '';
	        if(isset($requestData['related_files'])){
	            for($i=0; $i<count($requestData['related_files']); $i++){
	                $related_files .= $requestData['related_files'][$i].',';
	            }
	            
	        }					  					  
			$obj		= 	Admin::find(@$requestData['id']);
			$first_name = substr(@$requestData['first_name'], 0, 4);			
			$obj->first_name	=	@$requestData['first_name'];
			$obj->last_name	=	@$requestData['last_name'];
			$obj->dob	=	@$requestData['dob'];
			$obj->client_id	=	@$requestData['client_id'];
			$obj->email	=	@$requestData['email'];
			$obj->phone	=	@$requestData['phone'];
			$obj->address	=	@$requestData['address'];
			$obj->city	=	@$requestData['city'];
			$obj->state	=	@$requestData['state'];
			$obj->zip	=	@$requestData['zip'];
			$obj->country	=	@$requestData['country'];
			$obj->preferredIntake	=	@$requestData['preferredIntake'];
			$obj->country_passport			=	@$requestData['country_passport'];
			$obj->passport_number			=	@$requestData['passport_number'];
			$obj->visa_type			=	@$requestData['visa_type'];
			$obj->related_files	=	rtrim($related_files,',');
			$obj->visaExpiry			=	@$requestData['visaExpiry'];
			$obj->applications	=	@$requestData['applications'];
			$obj->assignee	=	@$requestData['assignee'];
			$obj->att_phone	=	@$requestData['att_phone'];
			$obj->att_country_code	=	@$requestData['att_country_code'];
			$obj->att_email	=	@$requestData['att_email'];
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
				return Redirect::to('/agent/clients')->with('success', 'Clients Edited Successfully');
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
					return view('Agent.clients.edit', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/agent/clients')->with('error', 'Clients Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/agent/clients')->with('error', Config::get('constants.unauthorized'));
			}		
		} 	
		
	}
	
	public function detail(Request $request, $id = NULL){
		if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(Admin::where('id', '=', $id)->where('role', '=', '7')->where('agent_id', '=', Auth::user()->id)->exists()) 
				{
					$fetchedData = Admin::find($id);
					return view('Agent.clients.detail', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/agent/clients')->with('error', 'Clients Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/agent/clients')->with('error', Config::get('constants.unauthorized'));
			}
	}
	
	public function getrecipients(Request $request){
		$squery = $request->q;
		if($squery != ''){
			
			 $clients = \App\Admin::where('is_archived', '=', 0)
       ->where('role', '=', 7)
       ->where(
           function($query) use ($squery) {
             return $query
                    ->where('email', 'LIKE', '%'.$squery.'%')
                    ->orwhere('first_name', 'LIKE','%'.$squery.'%')->orwhere('last_name', 'LIKE','%'.$squery.'%')->orwhere('client_id', 'LIKE','%'.$squery.'%')->orwhere('phone', 'LIKE','%'.$squery.'%');
            })
            ->get();
			
			$items = array();
			foreach($clients as $clint){
				$items[] = array('name' => $clint->first_name.' '.$clint->last_name,'email'=>$clint->email,'status'=>'Client','id'=>$clint->id,'cid'=>base64_encode(convert_uuencode(@$clint->id)));
			}
			
			echo json_encode(array('items'=>$items));
		}
	}
	
	
	public function getallclients(Request $request){
		$squery = $request->q;
		if($squery != ''){
			$d = '';
			if(strstr($squery, '/')){
				$dob = explode('/', $squery);
				if(!empty($dob) && is_array($dob)){
					$d = $dob[2].'/'.$dob[1].'/'.$dob[0];
				}
			}
			 $clients = \App\Admin::where('is_archived', '=', 0)
       ->where('role', '=', 7)
       ->where(
           function($query) use ($squery,$d) {
             return $query
                    ->where('email', 'LIKE', '%'.$squery.'%')
                    ->orwhere('first_name', 'LIKE','%'.$squery.'%')->orwhere('last_name', 'LIKE','%'.$squery.'%')->orwhere('client_id', 'LIKE','%'.$squery.'%')->orwhere('phone', 'LIKE','%'.$squery.'%')->orwhere('dob', '=',$d)->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$squery."%");
					
            })
            ->get();
			
				 $leads = \App\Lead::where('converted', '=', 0)
      
       ->where(
           function($query) use ($squery,$d) {
             return $query
                    ->where('email', 'LIKE', '%'.$squery.'%')
                    ->orwhere('first_name', 'LIKE','%'.$squery.'%')->orwhere('last_name', 'LIKE','%'.$squery.'%')->orwhere('phone', 'LIKE','%'.$squery.'%')->orwhere('dob', '=',$d)  ->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$squery."%");
					
            })
            ->get();
			
				$litems = array();
			foreach($leads as $lead){
				$litems[] = array('name' => $lead->first_name.' '.$lead->last_name,'email'=>$lead->email,'status'=>'Lead','id'=>base64_encode(convert_uuencode(@$lead->id)).'/Lead');
			}
			
			$items = array();
			foreach($clients as $clint){
				$items[] = array('name' => $clint->first_name.' '.$clint->last_name,'email'=>$clint->email,'status'=>'Client','id'=>base64_encode(convert_uuencode(@$clint->id)).'/Client');
			}
			$m = array_merge($items, $litems);
			echo json_encode(array('items'=>$m));
		}
	}
	
	
	public function activities(Request $request){
		if(Admin::where('role', '=', '7')->where('id', $request->id)->exists()){
			$activities = ActivitiesLog::where('client_id', $request->id)->orderby('created_at', 'DESC')->get();
			$data = array();
			foreach($activities as $activit){
				$admin = Admin::where('id', $activit->created_by)->first();
				$data[] = array(
					'subject' => $activit->subject,
					'createdname' => substr($admin->first_name, 0, 1),
					'name' => $admin->first_name,
					'message' => $activit->description,
					'date' => date('d M Y, H:i A', strtotime($activit->created_at))
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
				$objs->description = '<span class="text-semi-bold">'.$productdetail->name.'</span><p>'.$partnerdetail->partner_name.' ('.$PartnerBranch->name.')</p>';
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
				<td><?php echo $alist->sale_forcast; ?></td>
				<td><?php echo date('Y-m-d', strtotime($alist->created_at)); ?></td> 
				<td><?php echo date('Y-m-d', strtotime($alist->updated_at)); ?></td> 
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
		
		$notelist = \App\Note::where('client_id',$client_id)->where('type',$type)->orderby('pin', 'DESC')->get();
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
							<a href="#"><?php echo substr($admin->first_name, 0, 1); ?></a>
						</div>
						<div class="note_modify">
							<small>Last Modified <span><?php echo date('Y-m-d', strtotime($list->updated_at)); ?></span></small>
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
						
						</div>
					</div>
				</div>
			</div>
		</div>
			<?php
			
		}
		return ob_get_clean();
	}
	
	
	public function uploaddocument(Request $request){
		$id = $request->clientid;

	
		 if ($request->hasfile('document_upload')) {
		     $files = $request->file('document_upload');
		      foreach ($files as $file) {
		           $size = $file->getSize();
		        	 $document_upload = $this->uploadFile($file, Config::get('constants.documents'));
		        	 $exploadename = explode('.', $document_upload);
		        	$obj = new \App\Document;
        		    	$obj->file_name = $exploadename[0];
        			$obj->filetype = $exploadename[1];
        			$obj->user_id = Auth::user()->id;
        			$obj->myfile = $document_upload;
        			$obj->client_id = $id;
        			$obj->type = $request->type;
        			$obj->file_size = $size;
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
				$fetchd = \App\Document::where('client_id',$id)->where('type',$request->type)->orderby('created_at', 'DESC')->get();
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
				$objs->description = '<span class="text-semi-bold">'.$productdetail->name.'</span><p>'.$partnerdetail->partner_name.' ('.$PartnerBranch->name.')</p>';
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
				$objs->description = '<span class="text-semi-bold">'.$productdetail->name.'</span><p>'.$partnerdetail->partner_name.' ('.$PartnerBranch->name.')</p>';
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
			$obj = \App\Admin::find($id);
			$obj->tagname = implode(',', $request->tag);
			$saved = $obj->save();
			if($saved){
				return Redirect::to('/agent/clients/detail/'.base64_encode(convert_uuencode(@$id)))->with('success', 'Tags addes successfully');
			}else{
				return Redirect::to('/agent/clients/detail/'.base64_encode(convert_uuencode(@$id)))->with('error', 'Please try again');
			}
		}else{
			return Redirect::to('/agent/clients')->with('error', Config::get('constants.unauthorized'));
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
			<form method="post" action="<?php echo \URL::to('/agent/editappointment'); ?>" name="editappointment" id="editappointment" autocomplete="off" enctype="multipart/form-data">
				 
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
			<form method="post" action="<?php echo \URL::to('/agent/edit-interested-service'); ?>" name="editinter_servform" autocomplete="off" id="editinter_servform" enctype="multipart/form-data">
				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
				<input type="hidden" name="client_id" value="<?php echo $obj->client_id; ?>">
				<input type="hidden" name="id" value="<?php echo $obj->id; ?>">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="intrested_workflow">Select Workflow <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control select2" id="intrested_workflow" name="workflow">
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
								<label for="intrested_partner">Select Partner</label> 
								<select data-valid="required" class="form-control select2" id="intrested_partner" name="partner">
									<option value="">Please Select a Partner</option>
									<?php foreach(\App\Partner::where('service_workflow', $obj->workflow)->orderby('created_at', 'DESC')->get() as $plist){
										?>
										<option <?php if($obj->partner == $plist->id){ echo 'selected'; } ?> value="<?php echo $plist->id; ?>"><?php echo $plist->partner_name; ?></option>
									<?php } ?>
								</select> 
								<span class="custom-error partner_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="intrested_product">Select Product</label> 
								<select data-valid="required" class="form-control select2" id="intrested_product" name="product">
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
								<select data-valid="required" class="form-control select2" id="intrested_branch" name="branch">
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
					$objs->description = '<span class="text-semi-bold">'.$productdetail->name.'</span><p>'.$partnerdetail->partner_name.' ('.$PartnerBranch->name.')</p>';
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
		<form method="post" action="<?php echo \URL::to('/agent/servicesavefee'); ?>" name="servicefeeform" id="servicefeeform" autocomplete="off" enctype="multipart/form-data">
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
	
}
?>