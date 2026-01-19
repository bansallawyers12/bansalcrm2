<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\ActivitiesLog;
use Illuminate\Support\Facades\DB;
use Auth;

/**
 * Client application lifecycle
 * 
 * Methods to move from ClientsController:
 * - saveapplication
 * - getapplicationlists
 * - convertapplication
 * - savetoapplication
 * - deleteservices
 * - saleforcastservice
 */
class ClientApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
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

	public function convertapplication(Request $request){
		$id = $request->cat_id;
		$clientid = $request->clientid;

		if(\App\Models\InterestedService::where('client_id',$clientid)->where('id',$id)->exists()){
			$app = \App\Models\InterestedService::where('client_id',$clientid)->where('id',$id)->first();
			$workflow = $app->workflow;
			$workflowstage = \App\Models\WorkflowStage::where('w_id', $workflow)->orderby('id','ASC')->first();
			if(!$workflowstage){
				return response()->json([
					'status' => false,
					'message' => 'Workflow stage not found. Please try again.'
				]);
			}
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
			if(!$saved){
				return response()->json([
					'status' => false,
					'message' => 'Please try again'
				]);
			}

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
				$productName = $productdetail ? $productdetail->name : 'Unknown product';
				$partnerName = $partnerdetail ? $partnerdetail->partner_name : 'Unknown partner';
				$branchName = $PartnerBranch ? $PartnerBranch->name : 'Unknown branch';
				$objs->description = '<span class="text-semi-bold">'.$productName.'</span><p>'.$partnerName.' ('.$branchName.')</p>';
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
		return response()->json($response);
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
}
