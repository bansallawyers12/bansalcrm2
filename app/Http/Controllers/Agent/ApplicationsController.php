<?php
namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Admin;
use App\Application;
use App\ApplicationFeeOptionType;
use App\ApplicationFeeOption;
   use PDF; 
use Auth;
use Config;

class ApplicationsController extends Controller
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
	    $allstages = Application::select('stage')->groupBy('stage')->get();
		$query 		= Application::where('id', '!=', '')->with(['application_assignee']); 
		  
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
				
		return view('Agent.applications.index', compact(['lists', 'totalData','allstages'])); 	
				
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
		return view('Agent.applications.detail');
	}
	
	public function getapplicationdetail(Request $request){
		$fetchData = Application::find($request->id);
		return view('Agent.clients.applicationdetail', compact(['fetchData']));
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
	
	
	
	public function spagent_application(Request $request){
		$requestData = $request->all();
		$flag = true;
		if(Application::where('super_agent',$request->super_agent)->exists()){
			$flag = false;
			$response['message']	=	'Agent is already exists';
		}
		if(Application::where('sub_agent',$request->super_agent)->exists()){
			$flag = false;
			$response['message']	=	'Agent is already exists in sub admin';
		}
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
		if(Application::where('super_agent',$request->sub_agent)->exists()){
			$flag = false;
			$response['message']	=	'Agent is already exists in super admin';
		}
		if(Application::where('sub_agent',$request->sub_agent)->exists()){
			$flag = false;
			$response['message']	=	'Agent is already exists';
		}
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
	

	
	
	public function showproductfee(Request $request){
		$id = $request->id;
		ob_start();
		$appfeeoption = ApplicationFeeOption::where('app_id', $id)->first();
	
		?>
		<form method="post" action="<?php echo \URL::to('/admin/applicationsavefee'); ?>" name="applicationfeeform" id="applicationfeeform" autocomplete="off" enctype="multipart/form-data">
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
										$appfeeoptiontype = \App\ApplicationFeeOptionType::where('fee_id', $appfeeoption->id)->get();
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
							<button onclick="customValidate('applicationfeeform')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
		<?php
		return ob_get_clean();
	}
	
	
	public function applicationsavefee(Request $request){
		$requestData = $request->all();
		if(ApplicationFeeOption::where('app_id', $request->id)->exists()){
			$o = ApplicationFeeOption::where('app_id', $request->id)->first();
			$obj = ApplicationFeeOption::find($o->id);
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
				ApplicationFeeOptionType::where('fee_id', $obj->id)->delete();
				$course_fee_type = $requestData['course_fee_type'];
				$totl = 0;
				for($i = 0; $i< count($course_fee_type); $i++){
					$totl += $requestData['total_fee'][$i];
					$objs = new ApplicationFeeOptionType;
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
				$response['status'] 	= 	true;
					$response['message']	=	'Fee Option added successfully';
					$response['totalfee']	=	$totl;
					$response['discount']	=	$discount;
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Record not found';
			}
		}else{
			$obj = new ApplicationFeeOption;
			$obj->user_id = Auth::user()->id;
			$obj->app_id = $request->id;
			$obj->name = $requestData['fee_option_name'];
			$obj->country = $requestData['country_residency'];
			$obj->installment_type = $requestData['degree_level'];
			$saved = $obj->save();
			if($saved){
				$course_fee_type = $requestData['course_fee_type'];
				$totl = 0;
				for($i = 0; $i< count($course_fee_type); $i++){
					$totl += $requestData['total_fee'][$i];
					$objs = new ApplicationFeeOptionType;
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
				$response['status'] 	= 	true;
					$response['message']	=	'Fee Option added successfully';
					$response['totalfee']	=	$totl;
					$response['discount']	=	$discount;
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
}
