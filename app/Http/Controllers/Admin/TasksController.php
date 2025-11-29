<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Admin;
use App\Task;
use App\TaskLog;
use App\ToDoGroup;
 
use Auth;
use Config;

class TasksController extends Controller
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
	     if(isset($request->t)){
    	    if(\App\Notification::where('id', $request->t)->exists()){
    	       $ovv =  \App\Notification::find($request->t);
    	       $ovv->receiver_status = 1;
    	       $ovv->save();
    	    }
	    }
	    if(Auth::user()->role == 1){
	        $query 		= Task::where('id', '!=', ''); 
	    }else{
	       
	        $query 		= Task::where('id', '!=', '')
	        ->where(function($query){
                            $query->where('assignee', Auth::user()->id)
                                  ->orWhere('followers', Auth::user()->id);
                        });
	    }
		
		 
		$totalData 	= $query->count();	//for all data
		if($request->has('status')){
			$status = $request->status;
			if(trim($status) != ''){
				if($status == 'All'){
					$query->where('is_archived', 0);
				}else if($status == 'mytodo'){
					$query->where('status', 0);
				}else if($status == 'assigned'){
					$query->where('assignee', Auth::user()->id);
				}else if($status == 'completed'){
					
					$query->where('status', 1);
				}else if($status == 'following'){
					
					$query->where('followers', '!=', '');
				}else if($status == 'archived'){
					
					$query->where('is_archived', 1);
				}
				
			}else{
			$query->where('status', 0);
		}
		}else{
		//	$query->where('status', 0);
		}
		$query->where('status', 0);
		if($request->has('gid')){
		    	$gid = $request->gid;
		    	if(trim($gid) != '' ){
		    	    if($gid == 'me' ){
		     $query->where('assignee', Auth::user()->id);
		    	    }else{
		    	        $query->where('group_id', $gid); 
		    	    }
		    	}
		}
		if($request->has('sort')){
			 $sort = $request->sort;
			 $sorttype = $request->sorttype; 
			$query->sortable([$sort => $sorttype]);
		}else{
			$query->sortable(['due_date' => 'desc']);
		}
		$lists		= $query->paginate(config('constants.limit'));
		
		
		/*completed*/
		 if(Auth::user()->role == 1){
	        $querycom 		= Task::where('id', '!=', ''); 
	    }else{
	       
	        $querycom 		= Task::where('id', '!=', '')
	        ->where(function($query){
                            $query->where('assignee', Auth::user()->id)
                                  ->orWhere('followers', Auth::user()->id);
                        });
	    }
		
		 
		$totalDatacom 	= $querycom->count();	//for all data
		if($request->has('status')){
			$status = $request->status;
			if(trim($status) != ''){
				if($status == 'All'){
					$querycom->where('is_archived', 0);
				}else if($status == 'mytodo'){
					$querycom->where('status', 0);
				}else if($status == 'assigned'){
					$querycom->where('assignee', Auth::user()->id);
				}else if($status == 'completed'){
					
					$querycom->where('status', 1);
				}else if($status == 'following'){
					
					$querycom->where('followers', '!=', '');
				}else if($status == 'archived'){
					
					$querycom->where('is_archived', 1);
				}
				
			}else{
			$querycom->where('status', 0);
		}
		}else{
			$querycom->where('status', 1);
		}
			$querycom->where('status', 1);
		if($request->has('gid')){
		    	$gid = $request->gid;
		    	if(trim($gid) != '' ){
		    	    if($gid == 'me' ){
		     $querycom->where('assignee', Auth::user()->id);
		    	    }else{
		    	        $querycom->where('group_id', $gid); 
		    	    }
		    	}
		}
		if($request->has('sort')){
			 $sort = $request->sort;
			 $sorttype = $request->sorttype; 
			$querycom->sortable([$sort => $sorttype]);
		}else{
			$querycom->sortable(['due_date' => 'desc']);
		}
		$listscom		= $querycom->paginate(config('constants.limit'));
		/*completed*/
		
		return view('Admin.tasks.index',compact(['lists', 'totalData','listscom','totalDatacom']));  
	} 
	
	/* public function create(Request $request){
		return view('Admin.tasks.create');	
	} */
	
	public function groupstore(Request $request)
	{
	    $requestData 		= 	$request->all();
	    $obj				= 	new ToDoGroup; 
	    $obj->name			=	@$requestData['title'];
	    $obj->user_id			=	Auth::user()->id;
	    $saved				=	$obj->save(); 
	    if(!$saved)
			{
				return Redirect::to('/admin/tasks')->with('error', 'Please try again');
			}
			else
			{
			    	return Redirect::to('/admin/tasks')->with('success', 'Record Updated successfully');
			}
	}
	
	public function deletegroup(Request $request)
	{
	    $requestData 		= 	$request->all();
		if($request->has('delete_group') && count($requestData['delete_group'])>0){
			foreach($requestData['delete_group'] as $list){
				ToDoGroup::where('id',$list)->delete();
			}
			return Redirect::to('/admin/tasks')->with('success', 'Group Deleted');
		}
		return Redirect::to('/admin/tasks')->with('warning', 'Group not available');
			
	}
	
	public function store(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post'))  
		{
			$this->validate($request, [							
										'title' => 'required',
										'category' => 'required'
									  ]);
			
			$requestData 		= 	$request->all();
			//echo '<pre>'; print_r($requestData); die;
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
			$obj->group_id		=	@$requestData['group_id'];
			$obj->category		=	@$requestData['category'];
			$obj->assignee		=	@$requestData['assignee'];
			if(@$requestData['priority'] == 'Low'){
			$obj->priority_no = 1;
			}else if(@$requestData['priority'] == 'Normal'){
				$obj->priority_no = 2;
			}if(@$requestData['priority'] == 'High'){
				$obj->priority_no = 3;
			}if(@$requestData['priority'] == 'Urgent'){
				$obj->priority_no = 4;
			}
			$obj->priority		=	@$requestData['priority'];
			$obj->due_date		=	@$requestData['due_date']; 
			$obj->due_time		=	@$requestData['due_time']; 
			$obj->description	=	@$requestData['description'];
			$obj->related_to	=	@$requestData['related_to'];
			if(isset($requestData['contact_name'])){
			$obj->contact_name	=	@$requestData['contact_name'];
			}
			$obj->partner_name	=	@$requestData['partner_name'];
			$obj->client_name	=	@$requestData['client_name'];
			$obj->application	=	@$requestData['application'];
			$obj->stage			=	@$requestData['stage'];
			$obj->followers		=	@$requestData['followers'];
			$obj->attachments	=	@$requestData['attachfile'];
			$obj->user_id		=	Auth::user()->id;
			$obj->type			=	'client';
			$obj->client_id	=	@$requestData['client_id'];
			$saved				=	$obj->save();  
			
			
			if(!$saved)
			{
				if(isset($request->is_ajax) && $request->is_ajax == 0){
					return Redirect::to('/admin/tasks')->with('success', 'Please try again');
				}else{
					$response['status'] 	= 	false;
					$response['message']	=	'Please try again';
					echo json_encode($response);	
				}
				
			}
			else
			{
				$objtask = new TaskLog;
				$objtask->task_id 		= $obj->id;
				$objtask->created_by 	= Auth::user()->id;
				$objtask->title = 'created a task';
				$saved				=	$objtask->save();  
				
				if(isset($request->is_ajax) && $request->is_ajax == 0){
					if(isset($request->is_dashboard) && $request->is_dashboard == 0){
							return Redirect::to('/admin/dashboard')->with('success', 'Task Created Successfully');
					}else{
						return Redirect::to('/admin/tasks')->with('success', 'Task Created Successfully');
					}
				}else{
					$response['status'] 	= 	true;
					$response['message']	=	'Task Created Successfully';
					echo json_encode($response);	
				}
				
			}	
			
				
		}	

		//return view('Admin.tasks.index');	 
	}
	
	public function edit(Request $request, $id = NULL)
	{
	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [										
										'email' => 'required|email',
										'related_office' => 'required'
									  ]);
								  					  
			$obj				= 	Agent::find(@$requestData['id']);
						
			$obj->agent_type	=	@$requestData['agent_type'];
			$obj->struture	=	@$requestData['struture'];
			$obj->full_name	=	@$requestData['full_name'];
			$obj->business_name	=	@$requestData['business_name'];
			$obj->tax_number	=	@$requestData['tax_number']; 
			$obj->contract_expiry_date	=	@$requestData['contract_expiry_date'];
			$obj->email	=	@$requestData['email'];
			$obj->country_code	=	@$requestData['country_code'];
			$obj->phone	=	@$requestData['phone'];
			$obj->address	=	@$requestData['address'];
			$obj->city	=	@$requestData['city'];
			$obj->state	=	@$requestData['state'];
			$obj->zip	=	@$requestData['zip'];
			$obj->country	=	@$requestData['country'];
			$obj->related_office	=	@$requestData['related_office'];
			$obj->income_sharing	=	@$requestData['income_sharing'];
			$obj->claim_revenue	=	@$requestData['claim_revenue'];
			$obj->user_id	=	Auth::user()->id;
			$obj->client_id	=	@$requestData['client_id'];
			
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
		
			$saved							=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/agents')->with('success', 'Agents Edited Successfully');
			}				
		}

		else
		{		
			if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(Agent::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Agent::find($id);
					return view('Admin.agents.edit', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/admin/agents')->with('error', 'Agents Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/agents')->with('error', Config::get('constants.unauthorized'));
			}		
		} 	
		
	}
	
	
	public function gettasks(Request $request){
		$client_id = $request->clientid;
		
		$notelist = \App\Task::where('client_id',$client_id)->where('type','client')->orderby('created_at', 'DESC')->get();
		ob_start();
		foreach($notelist as $alist){
			$admin = \App\Admin::where('id', $alist->user_id)->first();
			?>
			<tr class="opentaskview" style="cursor:pointer;" id="<?php echo $alist->id; ?>">
				<td></td> 
				<td><b><?php echo $alist->category; ?></b>: <?php echo $alist->title; ?></td>
				<td><span class="author-avtar" style="font-size: .8rem;height: 24px;line-height: 24px;width: 24px;min-width: 24px;background: rgb(3, 169, 244);"><?php echo substr($admin->first_name, 0, 1); ?></span></td>
				<td><?php echo $alist->priority; ?></td> 
				<td><i class="fa fa-clock"></i> <?php echo $alist->due_date; ?> <?php echo $alist->due_time; ?></td>
				<td><?php
				if($alist->status == 1){
					echo '<span style="color: rgb(113, 204, 83); width: 84px;">Completed</span>';
				}else if($alist->status == 2){
					echo '<span style="color: rgb(255, 173, 0); width: 84px;">In Progress</span>';
				}else if($alist->status == 3){
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
	   
			$notedetail = \App\Task::where('id',$request->task_id)->where('type','client')->first();
		$admin = \App\Admin::where('id', $notedetail->assignee)->first();
		$followers = \App\Admin::where('id', @$notedetail->followers)->first();
		$addedby = \App\Admin::where('id', $notedetail->user_id)->first();
		$client = \App\Admin::where('id', $notedetail->client_id)->first();
		?>
		<div class="modal-header"> 
		
				<h5 class="modal-title" id="taskModalLabel"><i class="fa fa-bag"></i> <?php echo $notedetail->title; ?></h5>
				<?php if($notedetail->is_archived == 0){ ?> <a style="padding-left: 10px;" href="<?php echo \URL::to('/admin/tasks/archive/'.$notedetail->id); ?>"><i class="fa fa-archive"></i></a><?php } ?>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				
			</div>
		<div class="modal-body">
			<?php if($notedetail->is_archived == 1){ ?>
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="form-group">
						<p class="alert alert-danger" style="color: #5b5b5b;background-color: #fddbdb;">This task has been archived</p>
					</div>
				</div>
			</div>
			<?php } ?>
			<div class="row">
				
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Status:</label>
						<?php
						
						if($notedetail->status == 0){
							$status = '<span style="color: rgb(255, 173, 0);" class="">To Do</span>';
						}else if($notedetail->status == 2){
							$status = '<span style="color: rgb(255, 173, 0); " class="">In Progress</span>';
						}else if($notedetail->status == 3){
							$status = '<span style="color: rgb(156, 156, 156);" class="">On Review</span>';
						}else if($notedetail->status == 1){
							$status = '<span style="color: rgb(113, 204, 83);" class="">Completed</span>';
						}
						?>
						<ul class="navbar-nav navbar-right">
							<li class="dropdown dropdown-list-toggle">
								<a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle updatedstatus"><?php echo $status; ?> <i class="fa fa-angle-down"></i></a>
								 <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
									<a data-status="0" data-id="<?php echo $notedetail->id; ?>" data-status-name="To DO" href="javascript:;" class="dropdown-item changestatus">
										To Do
									</a>
									<a data-status="2" data-status-name="In Progress" data-id="<?php echo $notedetail->id; ?>" href="javascript:;" class="dropdown-item changestatus">
										In Progress
									</a>
									<a data-status="3" data-status-name="On Review" data-id="<?php echo $notedetail->id; ?>" href="javascript:;" class="dropdown-item changestatus">
										On Review
									</a>
									<a data-status="1" data-status-name="Completed" data-id="<?php echo $notedetail->id; ?>" href="javascript:;" class="dropdown-item changestatus">
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
								<a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle updatedpriority"><?php echo $notedetail->priority; ?> <i class="fa fa-angle-down"></i></a>
								 <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
									<a data-status="Low" data-id="<?php echo $notedetail->id; ?>" href="javascript:;" class="dropdown-item changepriority">
										Low
									</a>
									<a data-status="Normal" data-id="<?php echo $notedetail->id; ?>" href="javascript:;" class="dropdown-item changepriority">
										Normal
									</a>
									<a data-status="High" data-id="<?php echo $notedetail->id; ?>" href="javascript:;" class="dropdown-item changepriority">
										High
									</a>
									<a data-status="Urgent" data-id="<?php echo $notedetail->id; ?>" href="javascript:;" class="dropdown-item changepriority">
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
						<span><input type="text" class="form-control datepicker" name="" value="<?php echo $notedetail->due_date; ?>"></span>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Due Time:</label>
						<br>
						<span><input type="text" class="form-control timepicker" name="" value="<?php echo $notedetail->due_time; ?>"></span>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Assignee: <a class="openassignee"  href="javascript:;">Change</a></label>
						<br>
						<?php if($admin){ ?>
							<div style="display: flex;">
								<span class="author-avtar" style="margin-left: unset;margin-right: unset;font-size: .8rem;height: 24px;line-height: 24px;width: 24px;min-width: 24px;background: rgb(3, 169, 244);"><?php echo substr($admin->first_name, 0, 1); ?></span>
								<span style="margin-left:5px;"><?php echo $admin->first_name; ?></span>
							</div>
						<?php } ?>
					</div>
				
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Followers:</label>
						<br>
						<?php if($followers){ ?>
							<div style="display: flex;">
								<span class="author-avtar" style="margin-left: unset;margin-right: unset;font-size: .8rem;height: 24px;line-height: 24px;width: 24px;min-width: 24px;background: rgb(3, 169, 244);"><?php echo substr($followers->first_name, 0, 1); ?></span>
								<span style="margin-left:5px;"><?php echo $followers->first_name; ?></span>
							</div>
						<?php } ?>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Added By:</label>
						<br>
						<?php if($addedby){ ?>
							<div style="display: flex;">
								<span class="author-avtar" style="margin-left: unset;margin-right: unset;font-size: .8rem;height: 24px;line-height: 24px;width: 24px;min-width: 24px;background: rgb(3, 169, 244);"><?php echo substr($addedby->first_name, 0, 1); ?></span>
								<span style="margin-left:5px;"><?php echo $addedby->first_name; ?></span>
							</div>
						<?php } ?>
					</div>
				</div>
					<div class="assignee" style="display:none;">
	<div class="row">
		<div class="col-md-8">
			<select class="form-control select2" id="changeassignee" name="changeassignee">
				 <?php 
					foreach(\App\Admin::where('role','!=',7)->orderby('first_name','ASC')->get() as $admin){
						$branchname = \App\Branch::where('id',$admin->office_id)->first();
				?>
						<option value="<?php echo $admin->id; ?>"><?php echo $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')'; ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="col-md-2">
			<a class="saveassignee btn btn-success" data-id="<?php echo $notedetail->id; ?>" href="javascript:;">Save</a>
		</div>
		<div class="col-md-2">
			<a class="closeassignee" href="javascript:;"><i class="fa fa-times"></i></a>
		</div>
	</div>
</div>
				<div class="col-12 col-md-12 col-lg-12">
					<div class="form-group">
						<label for="title">Description:</label>
						<br>
						<?php if($notedetail->description != ''){ echo '<span class="desc_click">'.$notedetail->description.'</span>'; }else{ ?><textarea data-id="<?php echo $notedetail->id; ?>" class="form-control tasknewdesc"  placeholder="Enter Description"><?php echo $notedetail->description; ?></textarea><?php } ?>
						<textarea data-id="<?php echo $notedetail->id; ?>" class="form-control taskdesc" style="display:none;"  placeholder="Enter Description"><?php echo $notedetail->description; ?></textarea>
					</div>
				</div>
				<div class="col-12 col-md-12 col-lg-12">
					<div class="form-group">
						<label for="title">Related to: <span class="text-info">Client</span></label>
						<br>
						<?php if($client){ ?>
							<div class="client_task">
								<a target="_blank" href="<?php echo \URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$client->id))); ?>"><span class="author-avtar" style="margin-left: unset;margin-right: unset;font-size: .8rem;height: 24px;line-height: 24px;width: 24px;min-width: 24px;background: rgb(113, 204, 83);"><?php echo substr($client->first_name, 0, 1); ?><?php echo substr($client->last_name, 0, 1); ?></span>
								<span style="margin-left:5px;"><?php echo $client->first_name.' '.$client->last_name; ?></span></a>
							</div>
						<?php } ?>
					</div>
				</div>
			
				
				<div class="col-12 col-md-12 col-lg-12">
					<div class="form-group">
						<label for="title">Comments:</label>
						<textarea class="form-control taskcomment" name="comment" placeholder="Enter comment here"></textarea>
					</div>
				</div>
				<div class="col-12 col-md-12 col-lg-12">
					<div class="form-group">
						<button data-id="<?php echo $notedetail->id; ?>" class="btn btn-primary savecomment" >Save</button>
					</div>
				</div>
				
				<div class="col-md-12">
						<h4>Logs</h4>
						<div class="logsdata">
						<?php
						$logslist = TaskLog::where('task_id',$notedetail->id)->orderby('created_at', 'DESC')->get();						
						foreach($logslist as $llist){
							$admin = \App\Admin::where('id', $llist->created_by)->first();
						?>
							<div class="logsitem">
								<div class="row">
									<div class="col-md-7">
										<span class="ag-avatar"><?php echo substr($admin->first_name, 0, 1); ?></span>
										<span class="text_info"><span><?php echo $admin->first_name; ?></span><?php echo $llist->title; ?></span>
									</div>
									<div class="col-md-5">
										<span class="logs_date"><?php echo date('d M Y h:i A', strtotime($llist->created_at)); ?></span>
									</div>
									<?php if($llist->message != ''){ ?>
									<div class="col-md-12 logs_comment">
										<p><?php echo $llist->message; ?></p>
									</div>
									<?php } ?>
								</div>
							</div>
						<?php } ?> 
						</div>
					</div>
			</div>
		</div>
		<?php
	}
	
	public function update_task_comment(Request $request){
		$objs = new TaskLog;
		$objs->title = 'has commented';
		$objs->created_by = Auth::user()->id;
		$objs->task_id = $request->id;
		$objs->message = $request->visit_comment;
		$saved = $objs->save();
		if($saved){
			$response['status'] 	= 	true;
			$response['message']	=	'saved successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
	
	public function update_task_description(Request $request){
		$objs = Task::find($request->id);
		
		
		$objs->description = $request->visit_purpose;
		$saved = $objs->save();
		if($saved){
			$objs = new TaskLog;
			$objs->title = 'changed description';
			$objs->created_by = Auth::user()->id;
			$objs->task_id = $request->id;
			$objs->message = $request->visit_purpose;
			$saved = $objs->save();
			$response['status'] 	= 	true;
			$response['message']	=	'saved successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
	
	public function update_task_status(Request $request){
		$objs = Task::find($request->id);
		if($objs->status == 0){
			$status = 'To Do';
		}else if($objs->status == 2){
			$status = '>In Progress';
		}else if($objs->status == 3){
			$status = 'On Review<';
		}else if($objs->status == 1){
			$status = 'Completed';
		}
		$objs->status = $request->status;
		$saved = $objs->save();
		if($saved){
			$objs = new TaskLog;
			$objs->title = 'changed status from '.$status.' to '.$request->statusname;
			$objs->created_by = Auth::user()->id;
			$objs->task_id = $request->id;
			
			$saved = $objs->save();
			$alist = Task::find($request->id);
			$status = '';
			if($alist->status == 1){
					$status = '<span style="color: rgb(113, 204, 83); width: 84px;">Completed</span>';
				}else if($alist->status == 2){
					$status = '<span style="color: rgb(255, 173, 0); width: 84px;">In Progress</span>';
				}else if($alist->status == 3){
					$status = '<span style="color: rgb(156, 156, 156); width: 84px;">On Review</span>';
				}else{
					$status = '<span style="color: rgb(255, 173, 0); width: 84px;">Todo</span>';
				}
			$response['status'] 	= 	true;
			$response['viewstatus'] 	= 	$status;
			$response['message']	=	'saved successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
	
	public function update_task_priority(Request $request){
		$objs = Task::find($request->id);
		$status = $objs->priority;
		if($request->status == 'Low'){
			$objs->priority_no = 1;
		}else if($request->status == 'Normal'){
			$objs->priority_no = 2;
		}if($request->status == 'High'){
			$objs->priority_no = 3;
		}if($request->status == 'Urgent'){
			$objs->priority_no = 4;
		}
		$objs->priority = $request->status;
		$saved = $objs->save();
		if($saved){
			$objs = new TaskLog;
			$objs->title = 'changed priority from '.$status.' to '.$request->status;
			$objs->created_by = Auth::user()->id;
			$objs->task_id = $request->id;
			
			$saved = $objs->save();
			$response['status'] 	= 	true;
			$response['message']	=	'saved successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
	
	public function updateduedate(Request $request){
		$objs = Task::find($request->id);
		$due_date = $objs->due_date;
		$objs->due_date = $request->from;
		$saved = $objs->save();
		if($saved){
			$objs = new TaskLog;
			$objs->title = 'changed due date from '.$due_date.' to '.$request->from;
			$objs->created_by = Auth::user()->id;
			$objs->task_id = $request->id;
			
			$saved = $objs->save();
			$response['status'] 	= 	true;
			$response['message']	=	'saved successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
	
	public function taskArchive(Request $request, $id){
		$objs = Task::find($id);
		
		$objs->is_archived = 1;
		$saved = $objs->save();
		if($saved){
			$objs = new TaskLog;
			$objs->title = 'archived the task';
			$objs->created_by = Auth::user()->id;
			$objs->task_id = $request->id;
			
			$saved = $objs->save();
			return Redirect::to('/admin/tasks')->with('success', 'Task archived Successfully');
		}else{
			return Redirect::to('/admin/tasks')->with('error', 'Please try again');
		}
		
	}
	
	public function change_assignee(Request $request){
		$objs = Task::find($request->id);
		$objs->assignee = $request->assinee;
	
		$saved = $objs->save();
		if($saved){
	    	$o = new \App\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = $request->assinee;
	    	$o->module_id = $request->id;
	    	$o->url = \URL::to('/admin/tasks');
	    	$o->notification_type = 'task';
	    	$o->message = $objs->title.' Task Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name;
	    	$o->save();
			$response['status'] 	= 	true;
			$response['message']	=	'Updated successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
	
}
