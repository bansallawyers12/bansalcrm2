<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Models\Tag;
use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Models\clientServiceTaken;

/**
 * Client interested services and service taken
 * 
 * Methods to move from ClientsController:
 * - interestedService
 * - editinterestedService
 * - getServices
 * - getintrestedservice
 * - getintrestedserviceedit
 * - createservicetaken
 * - removeservicetaken
 * - getservicetaken
 * - gettagdata
 * - saleforcastservice
 * - savetoapplication
 */
class ClientServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
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
					$response['message']	=	'You\'ve successfully added interested service';
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
				$response['message']	=	'You\'ve successfully updated interested service';
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
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
					$response['message']	=	'You\'ve successfully updated your client\'s information.';
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
        
        // Initialize default response
        $items = array();
        $per_page = 20;
        $tags_total = 0;
        
        // Only search if query is provided and not empty
        if($squery != '' && trim($squery) != ''){
            $tags_total = \App\Models\Tag::select('id','name')->where('name', 'ilike', '%'.$squery.'%')->count();
            $tags = \App\Models\Tag::select('id','name')->where('name', 'ilike', '%'.$squery.'%')->paginate(20);

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
        }
        
        // Always return a proper JSON response
        return response()->json(array('items'=>$items,'per_page'=>$per_page,'total_count'=>$tags_total));
    }  // Move methods here
}
