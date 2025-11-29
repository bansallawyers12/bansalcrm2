<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Admin;
use App\Application;
use App\ApplicationFeeOptionType;
use App\ApplicationFeeOption;
   use PDF; 
use Auth;
use Config;
use App\Partner;

class ApplicationsController extends Controller
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
	    $allstages = Application::select('stage')->where('status', '!=', 2)->groupBy('stage')->get();
		$query 		= Application::where('id', '!=', '')->where('status', '!=', 2)->with(['application_assignee']); 
		  
		$totalData 	= $query->count();	//for all data
        if ($request->has('partner')) 
		{
			$partner 		= 	$request->input('partner'); 
			if(trim($partner) != '')
			{
				$query->where('partner_id', '=', $partner);
			}
		}
		if ($request->has('assignee')) 
		{
			$assignee 		= 	$request->input('assignee'); 
			if(trim($assignee) != '')
			{
				$query->where('user_id', '=', $assignee);
			}
		}
		 if ($request->has('stage')) 
		{
			$stage 		= 	$request->input('stage'); 
			if(trim($stage) != '')
			{
				$query->where('stage', '=', $stage);
			}
		}
		      //  $lists = $query->orderBy('id', 'desc')->paginate(10);
		      $lists = $query->sortable(['id' => 'desc'])->paginate(10);
				
		return view('Admin.applications.index', compact(['lists', 'totalData','allstages'])); 	
				
		//return view('Admin.applications.index');	 
	}
	
	public function prospects(Request $request) 
	{
		
		//return view('Admin.prospects.index'); 	
 
	}
	
	public function create(Request $request)
	{
		//check authorization end
		//return view('Admin.users.create',compact(['usertype']));	
		
		//return view('Admin.clients.create');	
	}
	 
	 
	public function detail(){
		return view('Admin.applications.detail');
	}
	
	public function getapplicationdetail(Request $request){
		$fetchData = Application::find($request->id);
		return view('Admin.clients.applicationdetail', compact(['fetchData']));
	}
	
	public function completestage(Request $request){
		$fetchData = Application::find($request->id);
		$fetchData->status = 1;
		
		$saved = $fetchData->save();
		if($saved){
			$response['status'] 	= 	true;
			$response['stage']	=	$fetchData->stage;
			$response['width']	=	100;
			$response['message']	=	'Application has been successfully completed.';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		
		echo json_encode($response);
	}
	public function updatestage(Request $request){
		$fetchData = Application::find($request->id);
		$workflowstagecount = \App\WorkflowStage::where('w_id', $fetchData->workflow)->count();
		$widthcount = 0;
		if($workflowstagecount !== 0){
			$s = 100 / $workflowstagecount;
			$widthcount = round($s);
		}
		$workflowstage = \App\WorkflowStage::where('name', 'like', '%'.$fetchData->stage.'%')->where('w_id', $fetchData->workflow)->first();
		$nextid = \App\WorkflowStage::where('id', '>', @$workflowstage->id)->where('w_id', $fetchData->workflow)->orderBy('id','asc')->first();//dd($nextid);
		
		$fetchData->stage = $nextid->name;
		$comments = 'moved the stage from  <b>'.$workflowstage->name.'</b> to <b>'.$nextid->name.'</b>';
		
		$width = $fetchData->progresswidth + $widthcount;
		$fetchData->progresswidth = $width;
		$saved = $fetchData->save();
		if($saved){
			$obj = new \App\ApplicationActivitiesLog;
			$obj->stage = $workflowstage->name;
			$obj->comment = @$comments;
			$obj->app_id = $request->id;
			$obj->type = 'stage';
			$obj->user_id = Auth::user()->id;
			$saved = $obj->save();
			$displayback = false;
			$workflowstage = \App\WorkflowStage::where('w_id', $fetchData->workflow)->orderBy('id','desc')->first();
		
			if($workflowstage->name == $fetchData->stage){
				$displayback = true;
			}
			$response['status'] 	= 	true;
			$response['stage']	=	$fetchData->stage;
			$response['width']	=	$width;
			$response['displaycomplete']	=	$displayback;
			$response['message']	=	'Application has been successfully moved to next stage.';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
	
	public function updatebackstage(Request $request){
		$fetchData = Application::find($request->id);
		$workflowstage = \App\WorkflowStage::where('name', $fetchData->stage)->where('w_id', $fetchData->workflow)->first();
		$nextid = \App\WorkflowStage::where('id', '<', $workflowstage->id)->where('w_id', $fetchData->workflow)->orderBy('id','Desc')->first();
		if($nextid){
			$workflowstagecount = \App\WorkflowStage::where('w_id', $fetchData->workflow)->count();
			$widthcount = 0;
			if($workflowstagecount !== 0){
				$s = 100 / $workflowstagecount;
				$widthcount = round($s);
			}
			$fetchData->stage = $nextid->name;
			$comments = 'moved the stage from  <b>'.$workflowstage->name.'</b> to <b>'.$nextid->name.'</b>';
			$width = $fetchData->progresswidth - $widthcount;
			if($width <= 0){
				$width = 0;
			}	
			
			$fetchData->progresswidth = $width;
			
			$saved = $fetchData->save();
			if($saved){
				
				
				$obj = new \App\ApplicationActivitiesLog;
				$obj->stage = $workflowstage->stage;
				$obj->type = 'stage';
				$obj->comment = $comments;
				$obj->app_id = $request->id;
				$obj->user_id = Auth::user()->id;
				$saved = $obj->save();
				
				$displayback = false;
				$workflowstage = \App\WorkflowStage::where('w_id', $fetchData->workflow)->orderBy('id','desc')->first();
			
				if($workflowstage->name == $fetchData->stage){
					$displayback = true;
				}
				
				$response['status'] 	= 	true;
				$response['stage']	=	$fetchData->stage;
				$response['displaycomplete']	=	$displayback;
		
				$response['width']	=	$width;
				$response['message']	=	'Application has been successfully moved to previous stage.';
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
	   }else{
		   $response['status'] 	= 	false;
				$response['message']	=	'';
	   }
		echo json_encode($response);
	}
	
	public function getapplicationslogs(Request $request){
		//$clientid = @$request->clientid;
		$id = $request->id;
		$fetchData = Application::find($id);
		
		$stagesquery = \App\WorkflowStage::where('w_id', $fetchData->workflow)->get();
		foreach($stagesquery as $stages){
		$stage1 = '';
						
							$workflowstagess = \App\WorkflowStage::where('name', $fetchData->stage)->where('w_id', $fetchData->workflow)->first();
					
					$prevdata = \App\WorkflowStage::where('id', '<', $workflowstagess->id)->where('w_id', $fetchData->workflow)->orderBy('id','Desc')->get();
					$stagearray = array();
					foreach($prevdata as $pre){
						$stagearray[] = $pre->id;
					}
							
							if(in_array($stages->id, $stagearray)){
								$stage1 = 'app_green';
							}
							if($fetchData->status == 1){
								$stage1 = 'app_green';
							}
							$stagname = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $stages->name)));
							?>
				
						<div class="accordion cus_accrodian">
							
							<div class="accordion-header collapsed <?php echo $stage1; ?> <?php if($fetchData->stage == $stages->name && $fetchData->status != 1){ echo  'app_blue'; }  ?>"" role="button" data-toggle="collapse" data-target="#<?php echo $stagname; ?>_accor" aria-expanded="false">
								<h4><?php echo $stages->name; ?></h4>
								<div class="accord_hover">
									<a title="Add Note" class="openappnote" data-app-type="<?php echo $stages->name; ?>" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-file-alt"></i></a>
									<a title="Add Document" class="opendocnote" data-app-type="<?php echo $stagname; ?>" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-file-image"></i></a>
									<a data-app-type="<?php echo $stages->name; ?>" title="Add Appointments" class="openappappoint" data-id="<?php echo $fetchData->id; ?>" href="javascript:;"><i class="fa fa-calendar"></i></a>
									<a data-app-type="<?php echo $stages->name; ?>" title="Email" data-id="{{@$fetchData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" class="openclientemail" title="Compose Mail" href="javascript:;"><i class="fa fa-envelope"></i></a>
								</div>
							</div>
							<?php
							$applicationlists = \App\ApplicationActivitiesLog::where('app_id', $fetchData->id)->where('stage',$stages->name)->orderby('created_at', 'DESC')->get();
							
							?>
							<div class="accordion-body collapse" id="<?php echo $stagname; ?>_accor" data-parent="#accordion" style="">
								<div class="activity_list">
								<?php foreach($applicationlists as $applicationlist){ 
								$admin = \App\Admin::where('id',$applicationlist->user_id)->first();
								?>
									<div class="activity_col">
										<div class="activity_txt_time">
											<span class="span_txt"><b><?php echo $admin->first_name; ?></b> <?php echo $applicationlist->comment; ?></span>
											<span class="span_time"><?php echo date('d D, M Y h:i A', strtotime($applicationlist->created_at)); ?></span>
										</div>
										<?php if($applicationlist->title != ''){ ?>
										<div class="app_description"> 
											<div class="app_card">
												<div class="app_title"><?php echo $applicationlist->title; ?></div>
											</div>
											<?php if($applicationlist->description != ''){ ?>
											<div class="log_desc">
												<?php echo $applicationlist->description; ?>
											</div>
											<?php } ?>
										</div>	
										<?php } ?> 
									</div>
								<?php } ?>
								</div>
							</div>
						</div>
						<?php } ?>
		<?php
		}
	
	public function addNote(Request $request){
		$noteid =  $request->noteid;
		$type =  $request->type;
		
		$obj = new \App\ApplicationActivitiesLog;
			$obj->stage = $type;
			$obj->type = 'note';
			$obj->comment = 'added a note';
			$obj->title = $request->title;
			$obj->description = $request->description;
			$obj->app_id = $noteid;
			$obj->user_id = Auth::user()->id;
			$saved = $obj->save();
		$saved = $obj->save();
		if($saved){
			$response['status'] 	= 	true;
			$response['message']	=	'Note successfully added';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}	
	
	public function getapplicationnotes(Request $request){
		$noteid =  $request->id;

		
		$lists = \App\ApplicationActivitiesLog::where('type','note')->where('app_id',$noteid)->orderby('created_at', 'DESC')->get();
		
		ob_start();
			?>
			<div class="note_term_list"> 
				<?php
				foreach($lists as $list){
					$admin = \App\Admin::where('id', $list->user_id)->first();
				?>
					<div class="note_col" id="note_id_<?php echo $list->id; ?>"> 
						<div class="note_content">
							<h4><a class="viewapplicationnote" data-id="<?php echo $list->id; ?>" href="javascript:;"><?php echo @$list->title == "" ? config('constants.empty') : str_limit(@$list->title, '19', '...'); ?></a></h4>
							<p><?php echo @$list->description == "" ? config('constants.empty') : str_limit(@$list->description, '15', '...'); ?></p>
						</div>
						<div class="extra_content">
							<div class="left">
								<div class="author">
									<a href="#"><?php echo substr($admin->first_name, 0, 1); ?></a>
								</div>
								<div class="note_modify">
									<small>Last Modified <span><?php echo date('Y-m-d', strtotime($list->updated_at)); ?></span></small>
								</div>
							</div>  
							<div class="right">
								
							</div>
						</div>
					</div>
				<?php } ?>
				</div>
				<div class="clearfix"></div>
			<?php
			echo ob_get_clean();
		
	}
	
	public function applicationsendmail(Request $request){
		$requestData = $request->all();
		//echo '<pre>'; print_r($requestData); die;
		$user_id = @Auth::user()->id;
		$subject = $requestData['subject'];
		$message = $requestData['message'];
		$to = $requestData['to'];
		
	$client = \App\Admin::Where('email', $requestData['to'])->first();
			$subject = str_replace('{Client First Name}',$client->first_name, $subject);
			$message = str_replace('{Client First Name}',$client->first_name, $message);
			$message = str_replace('{Client Assignee Name}',$client->first_name, $message);
			$message = str_replace('{Company Name}',Auth::user()->company_name, $message);
			$array = array();
			$ccarray = array();
			if(isset($requestData['email_cc']) && !empty($requestData['email_cc'])){
				foreach($requestData['email_cc'] as $cc){
					$clientcc = \App\Admin::Where('id', $cc)->first();
					$ccarray[] = $clientcc;
				}
			}
				$sent = $this->send_compose_template($to, $subject, 'support@digitrex.live', $message, 'digitrex', $array,@$ccarray);
			if($sent){
				$objs = new \App\ApplicationActivitiesLog;
				$objs->stage = $request->type;
				$objs->type = 'appointment';
				$objs->comment = 'sent an email';
				$objs->title = '<b>Subject : '.$subject.'</b>';
				$objs->description = '<b>To: '.$to.'</b></br>'.$message;
				$objs->app_id = $request->noteid;
				$objs->user_id = Auth::user()->id;
				$saved = $objs->save();
				$response['status'] 	= 	true;
				$response['message']	=	'Email Sent Successfully';
			}else{
				$response['status'] 	= 	true;
				$response['message']	=	'Please try again';
			}
		
		echo json_encode($response);
	}
	
	public function updateintake(Request $request){
		$requestData = $request->all();
		//echo '<pre>'; print_r($requestData); die;
		$user_id = @Auth::user()->id;
		$obj = Application::find($request->appid);
		$obj->intakedate = $request->from;
		$saved = $obj->save();
			if($saved){
				
				$response['status'] 	= 	true;
				$response['message']	=	'Applied date successfully updated.';
			}else{
				$response['status'] 	= 	true;
				$response['message']	=	'Please try again';
			}
		
		echo json_encode($response);
	}
	
	public function updateexpectwin(Request $request){
		$requestData = $request->all();
		//echo '<pre>'; print_r($requestData); die;
		$user_id = @Auth::user()->id;
		$obj = Application::find($request->appid);
		$obj->expect_win_date = $request->from;
		$saved = $obj->save();
			if($saved){
				
				$response['status'] 	= 	true;
				$response['message']	=	'Date successfully updated.';
			}else{
				$response['status'] 	= 	true;
				$response['message']	=	'Please try again';
			}
		
		echo json_encode($response);
	}
	
	public function updatedates(Request $request){
		$requestData = $request->all();
		//echo '<pre>'; print_r($requestData); die;
		$user_id = @Auth::user()->id;
		$obj = Application::find($request->appid);
		if($request->datetype == 'start'){
			$obj->start_date = $request->from;
		}else{
			$obj->end_date = $request->from;
		}
		$saved = $obj->save();
			if($saved){
				
				$response['status'] 	= 	true;
				$response['message']	=	'Date successfully updated.';
				if($request->datetype == 'start'){
					$response['dates']	=	array(
						'date' => date('d',strtotime($obj->start_date)),
						'month' => date('M',strtotime($obj->start_date)),
						'year' => date('Y',strtotime($obj->start_date)),
					);
				}else{
					$response['dates']	=	array(
						'date' => date('d',strtotime($obj->end_date)),
						'month' => date('M',strtotime($obj->end_date)),
						'year' => date('Y',strtotime($obj->end_date)),
					);
				}
				
			}else{
				$response['status'] 	= 	true;
				$response['message']	=	'Please try again';
			}
		
		echo json_encode($response);
	}
	
	public function discontinue_application(Request $request){
		$requestData = $request->all();
		//echo '<pre>'; print_r($requestData); die;
		$user_id = @Auth::user()->id;
		$obj = Application::find($request->diapp_id);
		$obj->status = 2;
		$saved = $obj->save();
			if($saved){
				
				$response['status'] 	= 	true;
				$response['message']	=	'Application successfully discontinued.';
			}else{
				$response['status'] 	= 	true;
				$response['message']	=	'Please try again';
			}
		
		echo json_encode($response);
	}
	
	
	public function revert_application(Request $request){
		$requestData = $request->all();
		
		//echo '<pre>'; print_r($requestData); die;
		$user_id = @Auth::user()->id;
		$obj = Application::find($request->revapp_id);
		$obj->status = 0;
		$workflowstagecount = \App\WorkflowStage::where('w_id', $obj->workflow)->count();
			$widthcount = 0;
			if($workflowstagecount !== 0){
				$s = 100 / $workflowstagecount;
				$widthcount = round($s);
			}
		$progresswidth = $obj->progresswidth - $widthcount;
		$obj->progresswidth = $progresswidth;
		$saved = $obj->save();
			if($saved){
			$displayback = false;
				$workflowstage = \App\WorkflowStage::where('w_id', $obj->workflow)->orderBy('id','desc')->first();
			
				if($workflowstage->name == $obj->stage){
					$displayback = true;
				}	
				$response['status'] 	= 	true;
				$response['width'] 	= 	$progresswidth;
				$response['displaycomplete'] 	= 	$displayback;
				$response['message']	=	'Application successfully reverted.';
			}else{
				$response['status'] 	= 	true;
				$response['message']	=	'Please try again';
			}
		
		echo json_encode($response);
	}
	
	public function spagent_application(Request $request){
		$requestData = $request->all();
		$flag = true;
		/* if(Application::where('super_agent',$request->super_agent)->exists()){
			$flag = false;
			$response['message']	=	'Agent is already exists';
		}
		if(Application::where('sub_agent',$request->super_agent)->exists()){
			$flag = false;
			$response['message']	=	'Agent is already exists in sub admin';
		} */
		if($flag){
			$user_id = @Auth::user()->id;
			$obj = Application::find($request->siapp_id);
			$obj->super_agent = $request->super_agent;
			$saved = $obj->save();
			if($saved){
				$agent = \App\Agent::where('id',$request->super_agent)->first();
				$response['status'] 	= 	true;
				$response['message']	=	'Application successfully updated.';
				$response['data']	=	'<div class="client_info">
							<div class="cl_logo" style="display: inline-block;width: 30px;height: 30px; border-radius: 50%;background: #6777ef;text-align: center;color: #fff;font-size: 14px; line-height: 30px; vertical-align: top;">'.substr($agent->full_name, 0, 1).'</div>
							<div class="cl_name" style="display: inline-block;margin-left: 5px;width: calc(100% - 60px);">
								<span class="name">'.$agent->full_name.'</span>
								<span class="ui label zippyLabel alignMiddle yellow">
							  '.$agent->struture.'
							</span>
							</div>
							<div class="cl_del" style="display: inline-block;">
								<a href=""><i class="fa fa-times"></i></a>
							</div>
						</div>';
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
		}
		
		echo json_encode($response);
	}
	
	public function sbagent_application(Request $request){
		$requestData = $request->all();
		$flag = true;
		/* if(Application::where('super_agent',$request->sub_agent)->exists()){
			$flag = false;
			$response['message']	=	'Agent is already exists in super admin';
		}
		if(Application::where('sub_agent',$request->sub_agent)->exists()){
			$flag = false;
			$response['message']	=	'Agent is already exists';
		} */
		if($flag){
			$user_id = @Auth::user()->id;
			$obj = Application::find($request->sbapp_id);
			$obj->sub_agent = $request->sub_agent;
			$saved = $obj->save();
			if($saved){
				$agent = \App\Agent::where('id',$request->sub_agent)->first();
				$response['status'] 	= 	true;
				$response['message']	=	'Application successfully updated.';
				$response['data']	=	'<div class="client_info">
							<div class="cl_logo" style="display: inline-block;width: 30px;height: 30px; border-radius: 50%;background: #6777ef;text-align: center;color: #fff;font-size: 14px; line-height: 30px; vertical-align: top;">'.substr($agent->full_name, 0, 1).'</div>
							<div class="cl_name" style="display: inline-block;margin-left: 5px;width: calc(100% - 60px);">
								<span class="name">'.$agent->full_name.'</span>
								<span class="ui label zippyLabel alignMiddle yellow">
							  '.$agent->struture.'
							</span>
							</div>
							<div class="cl_del" style="display: inline-block;">
								<a href=""><i class="fa fa-times"></i></a>
							</div>
						</div>';
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
		}
		
		echo json_encode($response);
	}
	
	public function superagent(Request $request){
		$requestData = $request->all();
		
			$user_id = @Auth::user()->id;
			$obj = Application::find($request->note_id);
			$obj->super_agent = '';
			$saved = $obj->save();
			if($saved){

				$response['status'] 	= 	true;
				$response['message']	=	'Application successfully updated.';
				
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		
		echo json_encode($response);
	}
	
	public function subagent(Request $request){
		$requestData = $request->all();
		
			$user_id = @Auth::user()->id;
			$obj = Application::find($request->note_id);
			$obj->sub_agent = '';
			$saved = $obj->save();
			if($saved){

				$response['status'] 	= 	true;
				$response['message']	=	'Application successfully updated.';
				
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		
		echo json_encode($response);
	}
	
	public function application_ownership(Request $request){
		$requestData = $request->all();
		
			$user_id = @Auth::user()->id;
			$obj = Application::find($request->mapp_id);
			$obj->ratio = $request->ratio;
			$saved = $obj->save();
			if($saved){

				$response['status'] 	= 	true;
				$response['message']	=	'Application successfully updated.';
				$response['ratio']	=	$obj->ratio;
				
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		
		echo json_encode($response);
	}
	
	public function saleforcast(Request $request){
		$requestData = $request->all();
		
			$user_id = @Auth::user()->id;
			$obj = Application::find($request->fapp_id);
			$obj->client_revenue = $request->client_revenue;
			$obj->partner_revenue = $request->partner_revenue;
			$obj->discounts = $request->discounts;
			$saved = $obj->save();
			if($saved){

				$response['status'] 	= 	true;
				$response['message']	=	'Application successfully updated.';
				$response['client_revenue']	=	$obj->client_revenue;
				$response['partner_revenue']	=	$obj->partner_revenue;
				$response['discounts']	=	$obj->discounts;
				
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		
		echo json_encode($response);
	}
	
	public function getapplicationbycid(Request $request){
		$clientid = $request->clientid;
		//echo '<pre>'; print_r($requestData); die;
		$applications = Application::where('client_id', $clientid)->orderby('created_at', 'DESC')->get();
		ob_start();
		?>
		<option value="">Select Application</option>
		<?php
		foreach($applications as $application){
			$productdetail = \App\Product::where('id', $application->product_id)->first();
			$partnerdetail = \App\Partner::where('id', $application->partner_id)->first();		
			$clientdetail = \App\Admin::where('id', $application->client_id)->first();
			$PartnerBranch = \App\PartnerBranch::where('id', $application->branch)->first();
			?>
			<option value="<?php echo $application->id; ?>"><?php echo @$productdetail->name.'('.@$partnerdetail->partner_name; ?> <?php echo @$PartnerBranch->name; ?>)</option>
			<?php
		}
		return ob_get_clean();
	}
	
	
	
	public function showproductfee(Request $request){ //dd($request->all());
		$id = $request->id;
        $partnerid = $request->partnerid;
		ob_start();
		$appfeeoption = ApplicationFeeOption::where('app_id', $id)->first(); //dd($appfeeoption);
        $appInfo = Application::join('partners', 'applications.partner_id', '=', 'partners.id')
        ->join('products', 'applications.product_id', '=', 'products.id')
        ->select('applications.product_id','applications.partner_id','partners.commission_percentage','partners.partner_name','products.name as coursename')
        ->where('applications.id', $id)->first(); //dd($appInfo);
        ?>
		<form method="post" action="<?php echo \URL::to('/admin/applicationsavefee'); ?>" name="applicationfeeform" id="applicationfeeform" autocomplete="off" enctype="multipart/form-data">
				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
				<input type="hidden" name="id" value="<?php echo $id; ?>">
					<div class="row">
                        <div class="col-12 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="college_name">College Name <span class="span_req">*</span></label>
                              <input type="text" readonly name="college_name" class="form-control" value="<?php if( isset($appInfo->partner_name) && $appInfo->partner_name != "") {echo $appInfo->partner_name;} else { echo '';} ?>">
                                <span class="custom-error college_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

                        <div class="col-12 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="course_name">Course Name <span class="span_req">*</span></label>
								<input type="text" readonly name="course_name" class="form-control" value="<?php if( isset($appInfo->coursename) && $appInfo->coursename != "") {echo $appInfo->coursename;} else { echo '';} ?>">
                                <span class="custom-error course_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

                        <div class="col-12 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="installment">Installment <span class="span_req">*</span></label>
                                <select name="installment" class="form-control" data-valid="required">
                                    <option value="">Select Option</option>
                                    <option value="Yes" <?php if( isset($appfeeoption->installment) && $appfeeoption->installment == 'Yes' ) { echo ' selected="selected"'; } else { echo '';} ?> >Yes</option>
	                                <option value="No" <?php if( isset($appfeeoption->installment) && $appfeeoption->installment == 'No' ) { echo ' selected="selected"'; } else { echo '';} ?> >No</option>
                                </select>
								<span class="custom-error installment_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
                        <?php
                        if( isset($appInfo->commission_percentage)) {
                            if( $appInfo->commission_percentage !="" ) {
                                $commission_percentage = $appInfo->commission_percentage;
                            } else {
                                $commission_percentage = 0;
                            }
                        } else {
                            $commission_percentage = 0;
                        }?>
                        <input value="<?php echo $commission_percentage;?>" type="hidden" id="commission_percentage">
                        <div class="col-12 col-md-12 col-lg-12">
							<div class="table-responsive">
								<table class="table text_wrap" id="productitemview">
									<thead>
										<tr>
											<th>Fee Type</th>
											<th>Total Fee</th>
										</tr>
									</thead>
									<tbody class="tdata">
									<?php
									$totl = 0.00;
									$discount = 0.00;
									?>
                                        <tr class="add_fee_option cus_fee_option">
											<td>Total Course Fee
                                                <input value="1" type="hidden" name="fee_option_type[]">
                                                <input value="Total Course Fee" type="hidden" name="course_fee_type[]">
                                            </td>
											<td class="total_fee">
                                                <input value="<?php if( isset($appfeeoption->total_course_fee_amount) && $appfeeoption->total_course_fee_amount != "") { echo $appfeeoption->total_course_fee_amount;} ?>" data-valid="required" type="number" class="form-control total_fee_am" name="total_fee[]" id="total_course_fee_amount">
                                            </td>
										</tr>

                                        <tr class="add_fee_option cus_fee_option">
                                            <td>Enrolment Fee
                                            <input value="1" type="hidden" name="fee_option_type[]">
                                            <input value="Enrolment Fee" type="hidden" name="course_fee_type[]">
                                            </td>
											<td class="total_fee">
                                                <input value="<?php if( isset($appfeeoption->enrolment_fee_amount) && $appfeeoption->enrolment_fee_amount != "") { echo $appfeeoption->enrolment_fee_amount;} ?>" data-valid="required" type="number"  class="form-control total_fee_am" name="total_fee[]" id="enrolment_fee_amount">
                                            </td>
										</tr>

                                        <tr class="add_fee_option cus_fee_option">
                                            <td>Material fees
                                                <input value="1" type="hidden" name="fee_option_type[]">
                                                <input value="Material fees" type="hidden"  name="course_fee_type[]">
                                            </td>
											<td class="total_fee">
                                                <input value="<?php if( isset($appfeeoption->material_fees) && $appfeeoption->material_fees != "") { echo $appfeeoption->material_fees;} ?>" data-valid="required" type="number"  class="form-control total_fee_am" name="total_fee[]" id="material_fee_amount">
                                            </td>
										</tr>
                                    </tbody>
									<tfoot>
                                        <tr>
                                            <td>Tution Fee -</td>
											<td class="calculate_tution_fee"><?php if( isset($appfeeoption->tution_fees) && $appfeeoption->tution_fees != "") { echo $appfeeoption->tution_fees;} ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Commission %- <input type="text" name="commission_percentage" id="commission_percentage" value="<?php echo $commission_percentage;?>" style="width: 30px;"></td>
											<td class="calculate_total_commission"><?php if( isset($appfeeoption->tution_fees_commission) && $appfeeoption->tution_fees_commission != "") { echo $appfeeoption->tution_fees_commission;} ?></td>
                                            <td>Bonus - <input type="text" style="width:58px;" name="bonus" id="bonus" value="<?php if( isset($appfeeoption->bonus_amount) && $appfeeoption->bonus_amount != "") { echo $appfeeoption->bonus_amount;} ?>">

                                            <span style="margin-left:10px;">Bonus Paid-</span>
                                            <select name="bonus_paid" style="width: 141px;display: inline-block;" class="form-control" data-valid="required">
                                                <option value="">Select Option</option>
                                                <option value="Yes" <?php if( isset($appfeeoption->bonus_paid) && $appfeeoption->bonus_paid == 'Yes' ) { echo ' selected="selected"'; } else { echo '';} ?> >Yes</option>
                                                <option value="No" <?php if( isset($appfeeoption->bonus_paid) && $appfeeoption->bonus_paid == 'No' ) { echo ' selected="selected"'; } else { echo '';} ?> >No</option>
                                            </select>


                                        </td>
                                        </tr>
                                    </tfoot>
								</table>
							</div>
						</div>
                        <input type="hidden" name="tution_fees" id="tution_fees" value="<?php if( isset($appfeeoption->tution_fees) && $appfeeoption->tution_fees != "") { echo $appfeeoption->tution_fees;} ?>">
                        <input type="hidden" name="tution_fees_commission" id="tution_fees_commission" value="<?php if( isset($appfeeoption->tution_fees_commission) && $appfeeoption->tution_fees_commission != "") { echo $appfeeoption->tution_fees_commission;} ?>">
                        <input type="hidden" name="bonus_amount" id="bonus_amount" value="<?php if( isset($appfeeoption->bonus_amount) && $appfeeoption->bonus_amount != "") { echo $appfeeoption->bonus_amount;} ?>">
                        <input type="hidden" name="partnerid" id="partnerid" value="<?php echo $partnerid;?>">

                        <div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('applicationfeeform')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
		<?php
		return ob_get_clean();
	}


	public function applicationsavefee(Request $request){
		$requestData = $request->all(); //dd($requestData);
        //save commission percentage in partner table
        if(isset($request->partnerid) && $request->partnerid !="" ){
            $obj3 = Partner::find($request->partnerid);
            $obj3->commission_percentage = $request->commission_percentage;
            $saved = $obj3->save();
        }
        if(ApplicationFeeOption::where('app_id', $request->id)->exists()){
			$o = ApplicationFeeOption::where('app_id', $request->id)->first();
			$obj = ApplicationFeeOption::find($o->id);
			$obj->user_id = Auth::user()->id;
			$obj->app_id = $request->id;
			$obj->college_name = $requestData['college_name'];
			$obj->course_name = $requestData['course_name'];
			$obj->installment = $requestData['installment'];

            if( isset($requestData['total_fee'][0]) && $requestData['total_fee'][0] != ""){
                $total_course_fee_amount = $requestData['total_fee'][0];
            } else {
                $total_course_fee_amount = "0.00";
            }

            if( isset($requestData['total_fee'][1]) && $requestData['total_fee'][1] != ""){
                $enrolment_fee_amount = $requestData['total_fee'][1];
            } else {
                $enrolment_fee_amount = "0.00";
            }

            if( isset($requestData['total_fee'][2]) && $requestData['total_fee'][2] != ""){
                $material_fees = $requestData['total_fee'][2];
            } else {
                $material_fees = "0.00";
            }
            $obj->total_course_fee_amount = $total_course_fee_amount;
            $obj->enrolment_fee_amount = $enrolment_fee_amount;
            $obj->material_fees  = $material_fees;
            $obj->tution_fees = $requestData['tution_fees'];
            $obj->tution_fees_commission = $requestData['tution_fees_commission'];
            $obj->bonus_amount = $requestData['bonus_amount'];
            $obj->bonus_paid = $requestData['bonus_paid'];

            $saved = $obj->save();
			if($saved){
				ApplicationFeeOptionType::where('fee_id', $obj->id)->where('fee_option_type', 1)->delete();
				//$course_fee_type = $requestData['course_fee_type'];
				$totl = 0;
				for($i = 0; $i< count($requestData['course_fee_type']); $i++){
					$totl += $requestData['total_fee'][$i];
					$objs = new ApplicationFeeOptionType;
					$objs->fee_id = $obj->id;
                    $objs->fee_option_type = 1; //primary
					$objs->fee_type = $requestData['course_fee_type'][$i];
					$objs->total_fee = $requestData['total_fee'][$i];
                    $saved = $objs->save();
                }

                //If commision updated then update commission amount in 2nd popup Start Code
                if( ApplicationFeeOptionType::where('fee_id', $obj->id)->where('fee_option_type', 2)->exists() )
                {
                    $commission_percentage = $request->commission_percentage;
                    $app_fee_data_2nd_popup = ApplicationFeeOptionType::select('total_fee','id','claimed_or_not')->where('fee_id', $obj->id)->where('fee_option_type', 2)->get();
                    //dd($app_fee_data_2nd_popup);
                    if( !empty($app_fee_data_2nd_popup) && count($app_fee_data_2nd_popup) >0 )
                    {
                        $totl_commission = 0;
                        $sum_of_option_yes = 0;
                        $sum_of_option_no = 0;
                        foreach($app_fee_data_2nd_popup as $popVal){ //dd($popVal->total_fee);
                            if( isset($popVal->total_fee) && $popVal->total_fee != ""){
                                $total_fee_per_line = $popVal->total_fee;
                                $commission_per_line  = ($total_fee_per_line * $commission_percentage)/100;
                                $commission_per_line  = $commission_per_line;
                            } else {
                                $commission_per_line  = 0;
                            }
                            $obj5 = ApplicationFeeOptionType::find($popVal->id); //save updated commison
                            $obj5->commission = $commission_per_line;
                            $saved5 = $obj5->save();


                            $totl_commission +=  $commission_per_line;
                            if( $popVal->claimed_or_not == 'Yes') {
                                $sum_of_option_yes +=  $commission_per_line;
                            }
                            if( $popVal->claimed_or_not == 'No') {
                                $sum_of_option_no +=  $commission_per_line;
                            }
                        } //end foreach

                        //Update other entry as well
                        $obj6 = ApplicationFeeOption::find($obj->id);
                        if( isset($obj6->bonus_amount) && $obj6->bonus_amount != ""){
                            $commission_as_per_fee_reported = $totl_commission + $obj6->bonus_amount;
                        } else {
                            $commission_as_per_fee_reported = $totl_commission;
                        }
                        $obj6->commission_as_per_fee_reported = $commission_as_per_fee_reported; //Total commission +bonus
                        $obj6->commission_paid_as_per_fee_reported = $sum_of_option_yes; //sum of option Yes selected
                        $obj6->commission_pending = $sum_of_option_no; //sum of option No selected
                        $saved6 = $obj6->save();
                    }
                }
                //If commision updated then update commission amount in 2nd popup End Code

				$discount = 0.00;
				$response['status'] 	= 	true;
                $response['message']	=	'Fee Option added successfully';
                $response['totalfee']	=	$totl;
                $response['discount']	=	$discount;


                $response['total_course_fee_amount']	=	$total_course_fee_amount;
                $response['enrolment_fee_amount']	=	$enrolment_fee_amount;
                $response['material_fees']	=	$material_fees;
                $response['tution_fees']	=	$requestData['tution_fees'];
            }else{
				$response['status'] 	= 	false;
				$response['message']	=	'Record not found';
			}
		}else{
			$obj = new ApplicationFeeOption;
			$obj->user_id = Auth::user()->id;
			$obj->app_id = $request->id;
            $obj->college_name = $requestData['college_name'];
			$obj->course_name = $requestData['course_name'];
			$obj->installment = $requestData['installment'];

            if( isset($requestData['total_fee'][0]) && $requestData['total_fee'][0] != ""){
                $total_course_fee_amount = $requestData['total_fee'][0];
            } else {
                $total_course_fee_amount = "0.00";
            }

            if( isset($requestData['total_fee'][1]) && $requestData['total_fee'][1] != ""){
                $enrolment_fee_amount = $requestData['total_fee'][1];
            } else {
                $enrolment_fee_amount = "0.00";
            }

            if( isset($requestData['total_fee'][2]) && $requestData['total_fee'][2] != ""){
                $material_fees = $requestData['total_fee'][2];
            } else {
                $material_fees = "0.00";
            }
            $obj->total_course_fee_amount = $total_course_fee_amount;
            $obj->enrolment_fee_amount = $enrolment_fee_amount;
            $obj->material_fees  = $material_fees;
            $obj->tution_fees = $requestData['tution_fees'];
            $obj->tution_fees_commission = $requestData['tution_fees_commission'];
            $obj->bonus_amount = $requestData['bonus_amount'];
            $obj->bonus_paid = $requestData['bonus_paid'];
            $saved = $obj->save();
			if($saved){
				$course_fee_type = $requestData['course_fee_type'];
				$totl = 0;
				for($i = 0; $i< count($course_fee_type); $i++){
					$totl += $requestData['total_fee'][$i];
					$objs = new ApplicationFeeOptionType;
					$objs->fee_id = $obj->id;
                    $objs->fee_option_type = 1; //primary
					$objs->fee_type = $requestData['course_fee_type'][$i];
					$objs->total_fee = $requestData['total_fee'][$i];
                    $saved = $objs->save();
                }
				$discount = 0.00;

                //If commision updated then update commission amount in 2nd popup Start Code
                if( ApplicationFeeOptionType::where('fee_id', $obj->id)->where('fee_option_type', 2)->exists() )
                {
                    $commission_percentage = $request->commission_percentage;
                    $app_fee_data_2nd_popup = ApplicationFeeOptionType::select('total_fee','id','claimed_or_not')->where('fee_id', $obj->id)->where('fee_option_type', 2)->get();
                    //dd($app_fee_data_2nd_popup);
                    if( !empty($app_fee_data_2nd_popup) && count($app_fee_data_2nd_popup) >0 )
                    {
                        $totl_commission = 0;
                        $sum_of_option_yes = 0;
                        $sum_of_option_no = 0;
                        foreach($app_fee_data_2nd_popup as $popVal){ //dd($popVal->total_fee);
                            if( isset($popVal->total_fee) && $popVal->total_fee != ""){
                                $total_fee_per_line = $popVal->total_fee;
                                $commission_per_line  = ($total_fee_per_line * $commission_percentage)/100;
                                $commission_per_line  = $commission_per_line;
                            } else {
                                $commission_per_line  = 0;
                            }
                            $obj5 = ApplicationFeeOptionType::find($popVal->id); //save updated commison
                            $obj5->commission = $commission_per_line;
                            $saved5 = $obj5->save();


                            $totl_commission +=  $commission_per_line;
                            if( $popVal->claimed_or_not == 'Yes') {
                                $sum_of_option_yes +=  $commission_per_line;
                            }
                            if( $popVal->claimed_or_not == 'No') {
                                $sum_of_option_no +=  $commission_per_line;
                            }
                        } //end foreach

                        //Update other entry as well
                        $obj6 = ApplicationFeeOption::find($obj->id);
                        if( isset($obj6->bonus_amount) && $obj6->bonus_amount != ""){
                            $commission_as_per_fee_reported = $totl_commission + $obj6->bonus_amount;
                        } else {
                            $commission_as_per_fee_reported = $totl_commission;
                        }
                        $obj6->commission_as_per_fee_reported = $commission_as_per_fee_reported; //Total commission +bonus
                        $obj6->commission_paid_as_per_fee_reported = $sum_of_option_yes; //sum of option Yes selected
                        $obj6->commission_pending = $sum_of_option_no; //sum of option No selected
                        $saved6 = $obj6->save();
                    }
                }
                //If commision updated then update commission amount in 2nd popup End Code

				$response['status'] 	= 	true;
                $response['message']	=	'Fee Option added successfully';
                $response['totalfee']	=	$totl;
                $response['discount']	=	$discount;

                $response['total_course_fee_amount']	=	$total_course_fee_amount;
                $response['enrolment_fee_amount']	=	$enrolment_fee_amount;
                $response['material_fees']	=	$material_fees;
                $response['tution_fees']	=	$requestData['tution_fees'];
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Record not found';
			}
		}
		echo json_encode($response);
	}
	
	

	
	public function exportapplicationpdf(Request $request, $id){
		$applications = \App\Application::where('id', $id)->first();
		$partnerdetail = \App\Partner::where('id', @$applications->partner_id)->first();
		$productdetail = \App\Product::where('id', @$applications->product_id)->first();
		$cleintname = \App\Admin::where('role',7)->where('id',@$applications->client_id)->first();
		$PartnerBranch = \App\PartnerBranch::where('id', @$applications->branch)->first();
		$pdf = PDF::setOptions([
			'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
			'logOutputFile' => storage_path('logs/log.htm'),
			'tempDir' => storage_path('logs/')
			])->loadView('emails.application',compact(['cleintname','applications','productdetail','PartnerBranch','partnerdetail'])); 
			//
			return $pdf->stream('application.pdf');
	}
	
	public function addchecklists(Request $request){
		$requestData = $request->all();
		$client_id = $requestData['client_id'];
		$app_id = $requestData['app_id'];
		$type = $requestData['type'];
		$typename = $requestData['typename'];
		$obj = new \App\ApplicationDocumentList;
		$obj->type = $type;
		$obj->typename = $typename;
		$obj->client_id = $client_id;
		$obj->application_id = $app_id;
		$obj->document_type = @$request->document_type;
		$obj->description = $request->description;
		$obj->allow_client = $request->allow_upload_docu;
		$obj->make_mandatory = $request->proceed_next_stage;
		if($requestData['due_date'] == 1){
			$obj->date = $request->appoint_date;
			$obj->time = $request->appoint_time;
		}
		$obj->user_id = Auth::user()->id;
		
		$saved = $obj->save();
		if($saved){
			$applicationdocuments = \App\ApplicationDocumentList::where('application_id', $app_id)->where('client_id', $client_id)->where('type', $type)->get();
			$checklistdata = '<table class="table"><tbody>';
			foreach($applicationdocuments as $applicationdocument){
				$appcount = \App\ApplicationDocument::where('list_id', $applicationdocument->id)->count();
				$checklistdata .= '<tr>';
				if($appcount >0){
					$checklistdata .= '<td><span class="check"><i class="fa fa-check"></i></span></td>';
				}else{
					$checklistdata .= '<td><span class="round"></span></td>';
				}
					
					$checklistdata .= '<td>'.@$applicationdocument->document_type.'</td>';
					$checklistdata .= '<td><div class="circular-box cursor-pointer"><button class="transparent-button paddingNone">'.$appcount.'</button></div></td>';
					$checklistdata .= '<td><a data-aid="'.$app_id.'" data-type="'.$type.'" data-typename="'.$typename.'" data-id="'.$applicationdocument->id.'" class="openfileupload" href="javascript:;"><i class="fa fa-plus"></i></a></td>';
				$checklistdata .= '</tr>';
			}
			$checklistdata .= '</tbody></table>';
			$response['status'] 	= 	true;
			$response['message']	=	'CHecklist added successfully';
			$response['data']	=	$checklistdata;
			$countchecklist = \App\ApplicationDocumentList::where('application_id', $app_id)->count();
			$response['countchecklist']	=	$countchecklist;
		}else{
			$response['status'] 	= 	false;
				$response['message']	=	'Record not found';
		}
		echo json_encode($response);
	}
	
	public function checklistupload(Request $request){
		 $imageData = '';
		if (isset($_FILES['file']['name'][0])) {
		  foreach ($_FILES['file']['name'] as $keys => $values) {
			$fileName = $_FILES['file']['name'][$keys];
			if (move_uploaded_file($_FILES['file']['tmp_name'][$keys], Config::get('constants.documents').'/'. $fileName)) {
				$obj = new \App\ApplicationDocument;
				$obj->type = $request->type;
				$obj->typename = $request->typename;
				$obj->list_id = $request->id;
				$obj->file_name = $fileName;
				$obj->user_id = Auth::user()->id;
				$obj->application_id = $request->application_id;
				$save = $obj->save();
			  $imageData .= '<li><i class="fa fa-file"></i> '.$fileName.'</li>';
			}
		  }
		}
		
		$doclists = \App\ApplicationDocument::where('application_id',$request->application_id)->orderby('created_at','DESC')->get();
		$doclistdata = ''; 
		foreach($doclists as $doclist){
			$docdata = \App\ApplicationDocumentList::where('id', $doclist->list_id)->first();
			$doclistdata .= '<tr id="">';
				$doclistdata .= '<td><i class="fa fa-file"></i> '. $doclist->file_name.'<br>'.@$docdata->document_type.'</td>';
				$doclistdata .= '<td>';
					$doclistdata .=  $doclist->typename;
				$doclistdata .= '</td>';
				$admin = \App\Admin::where('id', @$doclist->user_id)->first();
				
			$doclistdata .= '<td><span style="    position: relative;background: rgb(3, 169, 244);font-size: .8rem;height: 24px;line-height: 24px;min-width: 24px;width: 24px;color: #fff;display: block;font-weight: 600;letter-spacing: 1px;text-align: center;border-radius: 50%;overflow: hidden;">'.substr(@$admin->first_name, 0, 1).'</span>'.@$admin->first_name.'</td>';
			$doclistdata .= '<td>'.date('Y-m-d',strtotime($doclist->created_at)).'</td>';
			$doclistdata .= '<td>';
			if($doclist->status == 1){
				$doclistdata .= '<span class="check"><i class="fa fa-eye"></i></span>';
			}
				$doclistdata .= '<div class="dropdown d-inline">
					<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
					<div class="dropdown-menu">
						<a target="_blank" class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Preview</a>
						<a data-id="'.$doclist->id.'" class="dropdown-item deletenote" data-href="deleteapplicationdocs" href="javascript:;">Delete</a>
						<a download class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Download</a>';
						if($doclist->status == 0){
							$doclistdata .= '<a data-id="'.$doclist->id.'" class="dropdown-item publishdoc" href="javascript:;">Publish Document</a>';
						}else{
							$doclistdata .= '<a data-id="'.$doclist->id.'"  class="dropdown-item unpublishdoc" href="javascript:;">Unpublish Document</a>';
						}
						
					$doclistdata .= '</div>
				</div>								  
			</td>';
			$doclistdata .= '</tr>';
		}
		$application_id = $request->application_id;
		$applicationuploadcount = DB::select("SELECT COUNT(DISTINCT list_id) AS cnt FROM application_documents where application_id = '$application_id'");
		$response['status'] 	= 	true;
		$response['imagedata']	=	$imageData;
		$response['doclistdata']	=	$doclistdata;
		$response['applicationuploadcount']	=	@$applicationuploadcount[0]->cnt;
		
		
		$applicationdocuments = \App\ApplicationDocumentList::where('application_id', $application_id)->where('type', $request->type)->get();
			$checklistdata = '<table class="table"><tbody>';
			foreach($applicationdocuments as $applicationdocument){
				$appcount = \App\ApplicationDocument::where('list_id', $applicationdocument->id)->count();
				$checklistdata .= '<tr>';
				if($appcount >0){
					$checklistdata .= '<td><span class="check"><i class="fa fa-check"></i></span></td>';
				}else{
					$checklistdata .= '<td><span class="round"></span></td>';
				}
					
					$checklistdata .= '<td>'.@$applicationdocument->document_type.'</td>';
					$checklistdata .= '<td><div class="circular-box cursor-pointer"><button class="transparent-button paddingNone">'.$appcount.'</button></div></td>';
					$checklistdata .= '<td><a data-aid="'.$application_id.'" data-type="'.$request->type.'" data-id="'.$applicationdocument->id.'" class="openfileupload" href="javascript:;"><i class="fa fa-plus"></i></a></td>';
				$checklistdata .= '</tr>';
			}
			$checklistdata .= '</tbody></table>';
		$response['checklistdata']	=	$checklistdata;
		$response['type']	=	$request->type;
		echo json_encode($response);
	}
	
	public function deleteapplicationdocs(Request $request){
		if(\App\ApplicationDocument::where('id', $request->note_id)->exists()){
			$appdoc = \App\ApplicationDocument::where('id', $request->note_id)->first();
			$res = \App\ApplicationDocument::where('id', $request->note_id)->delete();
			if($res){
				$response['status'] 	= 	true;
				$response['message'] 	= 	'Record removed successfully';
				
				
				
				$doclists = \App\ApplicationDocument::where('application_id',$appdoc->application_id)->orderby('created_at','DESC')->get();
		$doclistdata = ''; 
		foreach($doclists as $doclist){
			$docdata = \App\ApplicationDocumentList::where('id', $doclist->list_id)->first();
			$doclistdata .= '<tr id="">';
				$doclistdata .= '<td><i class="fa fa-file"></i> '. $doclist->file_name.'<br>'.@$docdata->document_type.'</td>';
				$doclistdata .= '<td>';
				if($doclist->type == 'application'){ $doclistdata .= 'Application'; }else if($doclist->type == 'acceptance'){ $doclistdata .=  'Acceptance'; }else if($doclist->type == 'payment'){ $doclistdata .=  'Payment'; }else if($doclist->type == 'formi20'){ $doclistdata .=  'Form I 20'; }else if($doclist->type == 'visaapplication'){ $doclistdata .=  'Visa Application'; }else if($doclist->type == 'interview'){ $doclistdata .=  'Interview'; }else if($doclist->type == 'enrolment'){ $doclistdata .=  'Enrolment'; }else if($doclist->type == 'courseongoing'){ $doclistdata .=  'Course Ongoing'; }
				$doclistdata .= '</td>';
				$admin = \App\Admin::where('id', $doclist->user_id)->first();
				
			$doclistdata .= '<td><span style="    position: relative;background: rgb(3, 169, 244);font-size: .8rem;height: 24px;line-height: 24px;min-width: 24px;width: 24px;color: #fff;display: block;font-weight: 600;letter-spacing: 1px;text-align: center;border-radius: 50%;overflow: hidden;">'.substr($admin->first_name, 0, 1).'</span>'.$admin->first_name.'</td>';
			$doclistdata .= '<td>'.date('Y-m-d',strtotime($doclist->created_at)).'</td>';
			$doclistdata .= '<td>';
			if($doclist->status == 1){
				$doclistdata .= '<span class="check"><i class="fa fa-eye"></i></span>';
			}
				$doclistdata .= '<div class="dropdown d-inline">
					<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
					<div class="dropdown-menu">
						<a target="_blank" class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Preview</a>
						<a data-id="'.$doclist->id.'" class="dropdown-item deletenote" data-href="deleteapplicationdocs" href="javascript:;">Delete</a>
						<a download class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Download</a>';
						if($doclist->status == 0){
							$doclistdata .= '<a data-id="'.$doclist->id.'" class="dropdown-item publishdoc" href="javascript:;">Publish Document</a>';
						}else{
							$doclistdata .= '<a data-id="'.$doclist->id.'"  class="dropdown-item unpublishdoc" href="javascript:;">Unpublish Document</a>';
						}
						
					$doclistdata .= '</div>
				</div>								  
			</td>';
			$doclistdata .= '</tr>';
		}
		$application_id = $appdoc->application_id;
		$applicationuploadcount = DB::select("SELECT COUNT(DISTINCT list_id) AS cnt FROM application_documents where application_id = '$application_id'");
		$response['status'] 	= 	true;

		$response['doclistdata']	=	$doclistdata;
		$response['applicationuploadcount']	=	@$applicationuploadcount[0]->cnt;
		
		
		$applicationdocuments = \App\ApplicationDocumentList::where('application_id', $application_id)->where('type', $appdoc->type)->get();
			$checklistdata = '<table class="table"><tbody>';
			foreach($applicationdocuments as $applicationdocument){
				$appcount = \App\ApplicationDocument::where('list_id', $applicationdocument->id)->count();
				$checklistdata .= '<tr>';
				if($appcount >0){
					$checklistdata .= '<td><span class="check"><i class="fa fa-check"></i></span></td>';
				}else{
					$checklistdata .= '<td><span class="round"></span></td>';
				}
					
					$checklistdata .= '<td>'.@$applicationdocument->document_type.'</td>';
					$checklistdata .= '<td><div class="circular-box cursor-pointer"><button class="transparent-button paddingNone">'.$appcount.'</button></div></td>';
					$checklistdata .= '<td><a data-aid="'.$application_id.'" data-type="'.$appdoc->type.'"data-typename="'.$appdoc->typename.'" data-id="'.$applicationdocument->id.'" class="openfileupload" href="javascript:;"><i class="fa fa-plus"></i></a></td>';
				$checklistdata .= '</tr>';
			}
			$checklistdata .= '</tbody></table>';
		$response['checklistdata']	=	$checklistdata;
		$response['type']	=	$appdoc->type;
			}else{
				$response['status'] 	= 	false;
				$response['message'] 	= 	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message'] 	= 	'No Record found';
		}
		echo json_encode($response);
	}
	
	
	public function publishdoc(Request $request){
		if(\App\ApplicationDocument::where('id', $request->appid)->exists()){
			$appdoc = \App\ApplicationDocument::where('id', $request->appid)->first();
			$obj = \App\ApplicationDocument::find($request->appid);
			$obj->status = $request->status;
			$saved = $obj->save();
			if($saved){
				$response['status'] 	= 	true;
				$response['message'] 	= 	'Record updated successfully';
				$doclists = \App\ApplicationDocument::where('application_id',$appdoc->application_id)->orderby('created_at','DESC')->get();
		$doclistdata = ''; 
		foreach($doclists as $doclist){
			$docdata = \App\ApplicationDocumentList::where('id', $doclist->list_id)->first();
			$doclistdata .= '<tr id="">';
				$doclistdata .= '<td><i class="fa fa-file"></i> '. $doclist->file_name.'<br>'.@$docdata->document_type.'</td>';
				$doclistdata .= '<td>';
				if($doclist->type == 'application'){ $doclistdata .= 'Application'; }else if($doclist->type == 'acceptance'){ $doclistdata .=  'Acceptance'; }else if($doclist->type == 'payment'){ $doclistdata .=  'Payment'; }else if($doclist->type == 'formi20'){ $doclistdata .=  'Form I 20'; }else if($doclist->type == 'visaapplication'){ $doclistdata .=  'Visa Application'; }else if($doclist->type == 'interview'){ $doclistdata .=  'Interview'; }else if($doclist->type == 'enrolment'){ $doclistdata .=  'Enrolment'; }else if($doclist->type == 'courseongoing'){ $doclistdata .=  'Course Ongoing'; }
				$doclistdata .= '</td>';
				$admin = \App\Admin::where('id', $doclist->user_id)->first();
				
			$doclistdata .= '<td><span style="    position: relative;background: rgb(3, 169, 244);font-size: .8rem;height: 24px;line-height: 24px;min-width: 24px;width: 24px;color: #fff;display: block;font-weight: 600;letter-spacing: 1px;text-align: center;border-radius: 50%;overflow: hidden;">'.substr($admin->first_name, 0, 1).'</span>'.$admin->first_name.'</td>';
			$doclistdata .= '<td>'.date('Y-m-d',strtotime($doclist->created_at)).'</td>';
			$doclistdata .= '<td>';
			if($doclist->status == 1){
				$doclistdata .= '<span class="check"><i class="fa fa-eye"></i></span>';
			}
				$doclistdata .= '<div class="dropdown d-inline">
					<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
					<div class="dropdown-menu">
						<a target="_blank" class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Preview</a>
						<a data-id="'.$doclist->id.'" class="dropdown-item deletenote" data-href="deleteapplicationdocs" href="javascript:;">Delete</a>
						<a download class="dropdown-item" href="'.\URL::to('/public/img/documents').'/'.$doclist->file_name.'">Download</a>';
						if($doclist->status == 0){
							$doclistdata .= '<a data-id="'.$doclist->id.'" class="dropdown-item publishdoc" href="javascript:;">Publish Document</a>';
						}else{
							$doclistdata .= '<a data-id="'.$doclist->id.'"  class="dropdown-item unpublishdoc" href="javascript:;">Unpublish Document</a>';
						}
						
					$doclistdata .= '</div>
				</div>								  
			</td>';
			$doclistdata .= '</tr>';
		}
		
		$response['status'] 	= 	true;

		$response['doclistdata']	=	$doclistdata;
		
			}else{
				$response['status'] 	= 	false;
				$response['message'] 	= 	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message'] 	= 	'No Record found';
		}
		echo json_encode($response);
	}
	
	public function getapplications(Request $request){
		$client_id = $request->client_id;
		$applications = Application::where('client_id', '=', $client_id)->get(); 
		ob_start();
		?>
		<option value="">Choose Application</option>
		<?php
		foreach($applications as $application){
			$Products = \App\Product::where('id', '=', @$application->product_id)->first(); 
			$Partners = \App\Partner::where('id', '=', @$application->partner_id)->first(); 
			?>
		<option value="<?php echo $application->id; ?>">(#<?php echo $application->id; ?>) <?php echo @$Products->name; ?>  (<?php echo @$Partners->partner_name; ?>)</option>
			<?php
		}
		return ob_get_clean();
	}
	
	public function migrationindex(Request $request)
	{
		//check authorization start	
			
			/* if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			} */	
		//check authorization end
	    $allstages = Application::select('stage')->where('workflow', '=', 5)->groupBy('stage')->get();
		$query 		= Application::where('id', '!=', '')->where('workflow', 5)->with(['application_assignee']); 
		  
		$totalData 	= $query->count();	//for all data
        if ($request->has('partner')) 
		{
			$partner 		= 	$request->input('partner'); 
			if(trim($partner) != '')
			{
				$query->where('partner_id', '=', $partner);
			}
		}
		if ($request->has('assignee')) 
		{
			$assignee 		= 	$request->input('assignee'); 
			if(trim($assignee) != '')
			{
				$query->where('user_id', '=', $assignee);
			}
		}
		 if ($request->has('stage')) 
		{
			$stage 		= 	$request->input('stage'); 
			if(trim($stage) != '')
			{
				$query->where('stage', '=', $stage);
			}
		}
		$lists		= $query->sortable(['id' => 'desc'])->paginate(10);
				
		return view('Admin.applications.migrationindex', compact(['lists', 'totalData','allstages'])); 	
				
		//return view('Admin.applications.index');	 
	}

	public function import(Request $request){
		$the_file = $request->file('uploaded_file');
		try{
			$spreadsheet = IOFactory::load($the_file->getRealPath());
			$sheet        = $spreadsheet->getActiveSheet();
			$row_limit    = $sheet->getHighestDataRow();
			$column_limit = $sheet->getHighestDataColumn();
			$row_range    = range( 2, $row_limit );
			$column_range = range( 'Z', $column_limit );
			$startcount = 2;
			$data = array();
			
			foreach ( $row_range as $row ) {
				$data[] = [
											   'user_id'=>$sheet->getCell( 'B' . $row )->getValue(),
											   'workflow'=>$sheet->getCell( 'C' . $row )->getValue(),
											   'partner_id'=>$sheet->getCell( 'D' . $row )->getValue(),
											   'product_id'=>$sheet->getCell( 'E' . $row )->getValue(),
											   'status'=>$sheet->getCell( 'F' . $row )->getValue(),
											   'stage'=>$sheet->getCell( 'G' . $row )->getValue(),
											   'sale_forcast'=>$sheet->getCell( 'H' . $row )->getValue(),
											   'created_at'=>$sheet->getCell( 'I' . $row )->getValue(),
											   'updated_at'=>$sheet->getCell( 'J' . $row )->getValue(),
											   'client_id'=>$sheet->getCell( 'K' . $row )->getValue(),
											   'branch'=>$sheet->getCell( 'L' . $row )->getValue(),
											   'intakedate'=>$sheet->getCell( 'M' . $row )->getValue(),
											   'start_date'=>$sheet->getCell( 'N' . $row )->getValue(),
											   'end_date'=>$sheet->getCell( 'O' . $row )->getValue(),
											   'expect_win_date'=>$sheet->getCell( 'P' . $row )->getValue(),
											   'super_agent'=>$sheet->getCell( 'Q' . $row )->getValue(),
											   'sub_agent'=>$sheet->getCell( 'R' . $row )->getValue(),
											   'ratio'=>$sheet->getCell( 'S' . $row )->getValue(),
											   'client_revenue'=>$sheet->getCell( 'T' . $row )->getValue(),
											   'partner_revenue'=>$sheet->getCell( 'U' . $row )->getValue(),
											   'discounts'=>$sheet->getCell( 'V' . $row )->getValue(),
											   'progresswidth'=>$sheet->getCell( 'W' . $row )->getValue()
				];
				$startcount++;
			}
			DB::connection()->getPdo()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
			DB::table('check_applications')->insert($data);
			DB::connection()->getPdo()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
		} catch (Exception $e) {
			$error_code = $e->errorInfo[1];
			return back()->withErrors('There was a problem uploading the data!');
		} 
		return back()->withSuccess('Great! Data has been successfully uploaded.');
	}
  
    //Update Student id
    public function updateStudentId(Request $request){
		$requestData = $request->all(); //dd($requestData);
        $obj = Application::find($request->application_id);
        $obj->student_id = $request->student_id;
        $saved = $obj->save();
        if($saved){
            $response['status'] 	= 	true;
            $response['message']	=	'Application student id successfully updated.';
            $response['student_id']	=	$obj->student_id;
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
        }
        echo json_encode($response);
	}
  
    
	//Show Product Fee latest
    public function showproductfeelatest(Request $request){ //dd($request->all());
		$id = $request->id;
		ob_start();
		$appfeeoption = ApplicationFeeOption::where('app_id', $id)->first(); //dd($appfeeoption);
        $appInfo = Application::join('partners', 'applications.partner_id', '=', 'partners.id')
        ->join('products', 'applications.product_id', '=', 'products.id')
        ->select('applications.product_id','applications.partner_id','partners.commission_percentage','partners.partner_name','products.name as coursename','applications.partner_id')
        ->where('applications.id', $id)->first();
        //dd($appInfo);
        ?>
		<form method="post" action="<?php echo \URL::to('/admin/applicationsavefeelatest'); ?>" name="applicationfeeformlatest" id="applicationfeeformlatest" autocomplete="off" enctype="multipart/form-data">
				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
				<input type="hidden" name="id" value="<?php echo $id; ?>">

                <?php
                if( isset($appInfo->commission_percentage)) {
                    if( $appInfo->commission_percentage !="" ) {
                        $commission_percentage = $appInfo->commission_percentage;
                    } else {
                        $commission_percentage = 0;
                    }
                } else {
                    $commission_percentage = 0;
                }?>
                <input value="<?php echo $commission_percentage;?>" type="hidden" id="commission_percentage">
					<div class="row">
                        <div class="col-12 col-md-12 col-lg-12">
							<div class="table-responsive">
                               <div style="float:right;margin-bottom:10px;">
                                    <span style="margin-left:10px;">Bonus Paid-</span>
                                    <select name="bonus_paid" style="width: 141px;display: inline-block;" class="form-control" disabled>
                                        <option value="">Select Option</option>
                                        <option value="Yes" <?php if( isset($appfeeoption->bonus_paid) && $appfeeoption->bonus_paid == 'Yes' ) { echo ' selected="selected"'; } else { echo '';} ?> >Yes</option>
                                        <option value="No" <?php if( isset($appfeeoption->bonus_paid) && $appfeeoption->bonus_paid == 'No' ) { echo ' selected="selected"'; } else { echo '';} ?> >No</option>
                                    </select>
                                </div>
								<table class="table text_wrap" id="productitemviewlatest">
									<thead>
										<tr>
											<th>Date paid</th>
											<th>Fees paid</th>
											<th>Commission earned -<?php echo $commission_percentage;?>%</th>
											<th>Claimed or not claimed</th>
                                            <th>Source</th>
										</tr>
									</thead>
									<tbody class="tdata">
									<?php
									$totl = 0.00;
                                    $totl_commission = 0.00;
									$discount = 0.00;
                                    $sum_of_paid_commission = 0;
                                    $sum_of_pending_commission = 0;
									if($appfeeoption){
										$appfeeoptiontype = \App\ApplicationFeeOptionType::where('fee_id', $appfeeoption->id)->where('fee_option_type', 2)->get();
										foreach($appfeeoptiontype as $fee){
											$totl += $fee->total_fee;

                                            //Total Commission
                                            if( isset($fee->total_fee) && $fee->total_fee != ""){
                                                $total_fee_per_line = $fee->total_fee;
                                                $commission_per_line  = ($total_fee_per_line * $commission_percentage)/100;
										    } else {
                                                $commission_per_line  = 0;
                                            }
                                            $totl_commission += $commission_per_line;

                                            //Paid Commission
                                            if( $fee->claimed_or_not == 'Yes') {
                                                $sum_of_paid_commission +=  $fee->commission;
                                            }
                                            //Pending Commission
                                            if( $fee->claimed_or_not == 'No' ) {
                                                $sum_of_pending_commission +=  $fee->commission;
                                            }
                                            ?>

                                        <tr class="add_fee_option cus_fee_option">
                                            <td>
                                                <input type="hidden" value="2"  name="fee_option_type[]">
												<input type="text" data-valid="required" value="<?php echo $fee->date_paid;?>" class="form-control date_paid" name="date_paid[]">
											</td>
											<td>
												<input type="number" data-valid="required" value="<?php echo $fee->total_fee;?>" class="form-control total_fee_am_2nd" name="total_fee[]">
											</td>
											<td>
												<input type="number" data-valid="required" value="<?php echo $commission_per_line;?>" class="form-control commission_cal" name="commission[]">
											</td>
											<td>
                                                <select class="form-control" data-valid="required"  name="claimed_or_not[]" >
                                                    <option value="">Select</option>
                                                    <option value="Yes" <?php if( $fee->claimed_or_not == 'Yes' ) { echo ' selected="selected"'; } else { echo '';} ?> >Yes</option>
                                                    <option value="No" <?php if( $fee->claimed_or_not == 'No' ) { echo ' selected="selected"'; } else { echo '';} ?> >No</option>
                                                </select>
                                            </td>

                                            <td>
                                                <select class="form-control" data-valid="required"  name="source[]" >
                                                    <option value="">Select</option>
                                                    <option value="Prededuct" <?php if( $fee->source == 'Prededuct' ) { echo ' selected="selected"'; } else { echo '';} ?> >Prededuct</option>
                                                    <option value="Reported by college" <?php if( $fee->source == 'Reported by college' ) { echo ' selected="selected"'; } else { echo '';} ?> >Reported by college</option>
                                                    <option value="Calculated by us" <?php if( $fee->source == 'Calculated by us' ) { echo ' selected="selected"'; } else { echo '';} ?> >Calculated by us</option>
                                                    <option value="Told by student" <?php if( $fee->source == 'Told by student' ) { echo ' selected="selected"'; } else { echo '';} ?> >Told by student</option>
                                                </select>
                                            </td>
                                        </tr>
										<?php
										} //end foreach
									}
                                    else
                                    {
									?>
										<tr class="add_fee_option cus_fee_option">
                                            <td>
                                                <input type="hidden" value="2"  name="fee_option_type[]">
                                                <input type="text" data-valid="required" value="" class="form-control date_paid" name="date_paid[]">
											</td>
											<td>
												<input type="number" data-valid="required" value="" class="form-control total_fee_am_2nd" name="total_fee[]">
											</td>
											<td>
												<input type="number" data-valid="required" value="" class="form-control commission_cal" name="commission[]">
											</td>
											<td>
                                                <select class="form-control" data-valid="required"  name="claimed_or_not[]" >
                                                    <option value="">Select</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control" data-valid="required"  name="source[]" >
                                                    <option value="">Select</option>
                                                    <option value="Prededuct">Prededuct</option>
                                                    <option value="Reported by college">Reported by college</option>
                                                    <option value="Calculated by us">Calculated by us</option>
                                                    <option value="Told by student">Told by student</option>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    $net = $totl -  $discount;
                                    ?>
									</tbody>
									<tfoot>
                                        <tr>
											<td style="text-align: right;"><b>Total</b></td>
											<td class="net_totl text-info total_fees_paid"><?php if( isset($totl) && $totl != "" ){ echo $totl;}?></td>
											<td colspan="2" class="net_totl text-info total_commission_earned"><?php if( isset($totl_commission) && $totl_commission != "" ){ echo $totl_commission;}?></td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12" style="margin-top: 10px;">

                            <div style="float:left;">
                                <div class="fee_option_addbtn_latest" style="display: inline-block;">
                                    <a href="#" class="btn btn-primary"><i class="fas fa-plus"></i> Add Fee</a>
                                </div>

                                <?php
                                if( isset($appfeeoption->bonus_paid ) && $appfeeoption->bonus_paid  != ""){
                                    if( $appfeeoption->bonus_paid  == "Yes"){
                                        $sum_of_paid_commission = $sum_of_paid_commission + $appfeeoption->bonus_amount;
                                        $sum_of_pending_commission = $sum_of_pending_commission;
                                    }
                                    if( $appfeeoption->bonus_paid  == "No"){
                                        $sum_of_paid_commission = $sum_of_paid_commission;
                                        $sum_of_pending_commission = $sum_of_pending_commission + $appfeeoption->bonus_amount;
                                    }
                                }
                                else {
                                    $sum_of_paid_commission = $sum_of_paid_commission;
                                    $sum_of_pending_commission = $sum_of_pending_commission;
                                }
                                ?>
                                <div style="display: inline-block;margin-left:95px;">
                                    <span><b>Paid Commission - </b></span>
                                    <span id="paid_commission"> <?php if( isset($sum_of_paid_commission) && $sum_of_paid_commission != "" ){ echo $sum_of_paid_commission;}?></span>
                                </div>

                                <div style="display: inline-block;margin-left:95px;">
                                    <span><b>Pending Commission - </b></span>
                                    <span id="pending_commission"> <?php if( isset($sum_of_pending_commission) && $sum_of_pending_commission != "" ){ echo $sum_of_pending_commission;}?></span>
                                </div>
                            </div>
                            <div style="float:right;">
                                <button onclick="customValidate('applicationfeeformlatest')" type="button" class="btn btn-primary">Save</button>
							    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
						</div>
					</div>
				</form>
		<?php
		return ob_get_clean();
	}

   //Save application Fee latest
	public function applicationsavefeelatest(Request $request){
		$requestData = $request->all(); //dd($requestData);
		if(ApplicationFeeOption::where('app_id', $request->id)->exists()){ //dd('iff');
			$o = ApplicationFeeOption::where('app_id', $request->id)->first();
			$obj = ApplicationFeeOption::find($o->id);
			$obj->user_id = Auth::user()->id;
			$obj->app_id = $request->id;
            $saved = $obj->save();
			if($saved){
				ApplicationFeeOptionType::where('fee_id', $obj->id)->where('fee_option_type', 2)->delete();
				$totl = 0;
                $totl_commission = 0;
                $sum_of_option_yes = 0;
                $sum_of_option_no = 0;
				for($i = 0; $i< count($requestData['date_paid']); $i++){
					$totl += $requestData['total_fee'][$i];
                    $totl_commission +=  $requestData['commission'][$i];
                    if($requestData['claimed_or_not'][$i] == 'Yes') {
                        $sum_of_option_yes +=  $requestData['commission'][$i];
                    }
                    if($requestData['claimed_or_not'][$i] == 'No') {
                        $sum_of_option_no +=  $requestData['commission'][$i];
                    }

					$objs = new ApplicationFeeOptionType;
					$objs->fee_id = $obj->id;
					$objs->fee_option_type = 2; //other fee
                    $objs->date_paid = $requestData['date_paid'][$i];
                    $objs->total_fee = $requestData['total_fee'][$i];
					$objs->commission = $requestData['commission'][$i];
                    $objs->claimed_or_not = $requestData['claimed_or_not'][$i];
                    $objs->source = $requestData['source'][$i];
                    $saved = $objs->save();
                }
                //Update commision related col in table
                $obj3 = ApplicationFeeOption::find($obj->id);
                $obj3->fee_reported_by_college = $totl;  //Sum Of Fees paid in Commision Popup

                $app_fee_info = ApplicationFeeOption::where('id', $obj->id)->first(); //dd($app_fee_info);
                if(isset($app_fee_info['bonus_amount']) && $app_fee_info['bonus_amount'] !=""){
                    $bonus_amount = $app_fee_info['bonus_amount'];
                } else {
                    $bonus_amount = "0.00";
                }
                $commission_as_per_fee_reported = $totl_commission + $bonus_amount;
                $obj3->commission_as_per_fee_reported = $commission_as_per_fee_reported; //Total commission earned + bonus amount

                //$obj3->commission_paid_as_per_fee_reported = $sum_of_option_yes; //Sum Of Option Yes Selected
                //$obj3->commission_pending = $sum_of_option_no; //Sum Of Option No Selected

                if( isset($app_fee_info['bonus_paid']) && $app_fee_info['bonus_paid']  != ""){
                    if( $app_fee_info['bonus_paid']  == "Yes"){
                        $obj3->commission_paid_as_per_fee_reported = $sum_of_option_yes + $app_fee_info['bonus_amount'];
                        $obj3->commission_pending = $sum_of_option_no;
                    }
                    if( $app_fee_info['bonus_paid'] == "No"){
                        $obj3->commission_paid_as_per_fee_reported = $sum_of_option_yes;
                        $obj3->commission_pending = $sum_of_option_no + $app_fee_info['bonus_amount'];
                    }
                }
                else {
                    $obj3->commission_paid_as_per_fee_reported = $sum_of_option_yes;
                    $obj3->commission_pending = $sum_of_option_no;
                }
                $saved3 = $obj3->save();

				$discount = 0.00;
				$response['status'] 	= 	true;
                $response['message']	=	'Other Fee Option added successfully';
                $response['totalfee']	=	$totl;
                $response['discount']	=	$discount;
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Record not found';
			}
		}else{ //dd('elsee');
			$obj = new ApplicationFeeOption;
			$obj->user_id = Auth::user()->id;
			$obj->app_id = $request->id;
            $saved = $obj->save();
			if($saved){
				$date_paid = $requestData['date_paid'];
				$totl = 0;
                $totl_commission = 0;
                $sum_of_option_yes = 0;
                $sum_of_option_no = 0;
				for($i = 0; $i< count($date_paid); $i++){
					$totl += $requestData['total_fee'][$i];
                    $totl_commission +=  $requestData['commission'][$i];
                    if($requestData['claimed_or_not'][$i] == 'Yes') {
                        $sum_of_option_yes +=  $requestData['commission'][$i];
                    }
                    if($requestData['claimed_or_not'][$i] == 'No') {
                        $sum_of_option_no +=  $requestData['commission'][$i];
                    }
					$objs = new ApplicationFeeOptionType;
					$objs->fee_id = $obj->id;
					$objs->fee_option_type = 2; //other fee
                    $objs->date_paid = $requestData['date_paid'][$i];
                    $objs->total_fee = $requestData['total_fee'][$i];
					$objs->commission = $requestData['commission'][$i];
                    $objs->claimed_or_not = $requestData['claimed_or_not'][$i];
                    $objs->source = $requestData['source'][$i];
                    $saved = $objs->save();
                }

              	//Update commision related col in table
                $obj3 = ApplicationFeeOption::find($obj->id);
                $obj3->fee_reported_by_college = $totl;  //Sum Of Fees paid in Commision Popup

                $app_fee_info = ApplicationFeeOption::where('id', $obj->id)->first(); //dd($app_fee_info);
                if(isset($app_fee_info['bonus_amount']) && $app_fee_info['bonus_amount'] !=""){
                    $bonus_amount = $app_fee_info['bonus_amount'];
                } else {
                    $bonus_amount = "0.00";
                }
                $commission_as_per_fee_reported = $totl_commission + $bonus_amount;
                $obj3->commission_as_per_fee_reported = $commission_as_per_fee_reported; //Total commission earned + bonus amount

                //$obj3->commission_paid_as_per_fee_reported = $sum_of_option_yes; //Sum Of Option Yes Selected
                //$obj3->commission_pending = $sum_of_option_no; //Sum Of Option No Selected

                if( isset($app_fee_info['bonus_paid']) && $app_fee_info['bonus_paid']  != ""){
                    if( $app_fee_info['bonus_paid']  == "Yes"){
                        $obj3->commission_paid_as_per_fee_reported = $sum_of_option_yes + $app_fee_info['bonus_amount'];
                        $obj3->commission_pending = $sum_of_option_no;
                    }
                    if( $app_fee_info['bonus_paid'] == "No"){
                        $obj3->commission_paid_as_per_fee_reported = $sum_of_option_yes;
                        $obj3->commission_pending = $sum_of_option_no + $app_fee_info['bonus_amount'];
                    }
                }
                else {
                    $obj3->commission_paid_as_per_fee_reported = $sum_of_option_yes;
                    $obj3->commission_pending = $sum_of_option_no;
                }
                $saved3 = $obj3->save();

                $discount = 0.00;
				$response['status'] 	= 	true;
				$response['message']	=	'Other Fee Option added successfully';
				$response['totalfee']	=	$totl;
				$response['discount']	=	$discount;
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Record not found';
			}
		}
		echo json_encode($response);
	}
   

    //Update commission amount on the basis of partner id
    /*public function updateCommissionPerAmount(Request $request){
		$obj = Partner::find($request->partnerid);
        $obj->commission_percentage = $request->commission_percentage;
        $saved = $obj->save();
        if($saved){
            $response['status'] 	= 	true;
            $response['message']	=	'Commission amount id successfully updated.';
            $response['commission_percentage']	=	$obj->commission_percentage;
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
        }
        echo json_encode($response);
	}*/

    
}
