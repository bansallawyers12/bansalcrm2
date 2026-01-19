<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivitiesLog;
use Illuminate\Support\Facades\DB;
use Auth;

/**
 * Client notes management
 * 
 * Methods to move from ClientsController:
 * - createnote
 * - getnotedetail
 * - viewnotedetail
 * - viewapplicationnote
 * - getnotes
 * - deletenote
 * - pinnote
 */
class ClientNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
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
			$response['message']	=	'You\'ve successfully updated Note';
			}else{
				$response['message']	=	'You\'ve successfully added Note';
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
}
