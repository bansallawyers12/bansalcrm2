<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Client followup management
 * 
 * Methods to move from ClientsController:
 * - followupstore
 * - followupstore_application
 * - reassignfollowupstore
 * - updatefollowup
 * - retagfollowup
 * - personalfollowup
 * - updatefollowupschedule
 */
class ClientFollowupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

       //Asssign application stage and save
	public function followupstore_application(Request $request){
	    $requestData = $request->all(); //echo '<pre>'; print_r($requestData); die;
        //echo "client_id==".$requestData['client_id'];
        //echo $client_decode_id = base64_encode(convert_uuencode($requestData['client_id'])); die;
        $client_decode_id = base64_encode(convert_uuencode($requestData['client_id']));

        $followup 				    = new \App\Models\Note;
        $followup->client_id		= @$requestData['client_id'];
		$followup->user_id			= Auth::user()->id;

        //Get Description
        $description =  'Application '.$requestData['course'].' for this college '.$requestData['school'].' assigned for '.$requestData['stage_name'].' stage';
		$followup->description		= $description;


        //Get assigner name
        $assignee_info = \App\Models\Admin::select('id','first_name','last_name')->where('id', $requestData['rem_cat11'])->first();
        if($assignee_info){
            $assignee_name = $assignee_info->first_name;
        } else {
            $assignee_name = 'N/A';
        }
        $title = 'Application assign to '.$assignee_name;
		$followup->title		    = $title;
        $followup->folloup	        = 1;
        $followup->task_group       = 'stage';
		$followup->assigned_to	    = @$requestData['rem_cat11'];
		$followup->followup_date	=  date('Y-m-d H:i:s');
        $followup->application_id	= $requestData['application_id'];
		$followup->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
		$followup->status = 0; // Required NOT NULL field (0 = active/open, 1 = closed/completed)
		$followup->type = 'client'; // Required field - mark as client type for Action page filtering
        $saved				        =  $followup->save();
        if(!$saved) {
			echo json_encode(array('success' => false, 'message' => 'Please try again', 'clientID' => $client_decode_id));
		} else {
			/*if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
                $Lead = Admin::find($this->decodeString($requestData['client_id']));
                $Lead->followup_date = date('Y-m-d H:i:s');
                $Lead->save();
			}*/

			$o = new \App\Models\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = @$requestData['rem_cat11'];
	    	$o->module_id = $requestData['client_id'];

	    	$o->url = route('clients.detail', $client_decode_id);
	    	$o->notification_type = 'client';
	    	$o->message = 'Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name.' '.date('d/M/Y h:i A');
	    	$o->seen = 0; // Set seen to 0 (unseen) for new notifications
	    	$o->save();

			/*$objs = new ActivitiesLog;
            $objs->client_id = @$requestData['client_id'];
            $objs->created_by = Auth::user()->id;
            $objs->subject = 'set action for '.@$assignee_name;
            $objs->description = '<span class="text-semi-bold">'.@$title.'</span><p>'.$description.'</p>';
            if(Auth::user()->id != @$requestData['rem_cat11']){
                $objs->use_for = @$requestData['rem_cat11'];
            } else {
                $objs->use_for = "";
            }
            $objs->followup_date = date('Y-m-d H:i:s');
            $objs->task_group = 'stage';
            $objs->save();*/



            $obj1 = new \App\Models\ApplicationActivitiesLog;
            $obj1->stage = $requestData['stage_name'];
            $obj1->type = 'task';
            $obj1->comment = 'assigned a task';
            $obj1->title =  'set action for '.@$assignee_name;
            $obj1->description =  '<span class="text-semi-bold">'.@$title.'</span><p>'.$description.'</p>';
            $obj1->app_id =  $requestData['application_id'];
            $obj1->user_id = Auth::user()->id;
            $obj1->save();

			echo json_encode(array('success' => true, 'message' => 'Applcation successfully assigned', 'clientID' => $client_decode_id,'application_id' => $requestData['application_id']));
			exit;
		}
	}
}
