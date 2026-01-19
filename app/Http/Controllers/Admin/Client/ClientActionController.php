<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Traits\ClientHelpers;
use Auth;

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
    use ClientHelpers;

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    //Asssign followup and save
	public function followupstore(Request $request){
	    $requestData 		= 	$request->all();
        //echo '<pre>'; print_r($requestData); die;
        /*if(\App\Models\Note::where('client_id',$requestData['client_id'])->where('assigned_to',$requestData['rem_cat'])->exists())
        {
            // return redirect()->back()->with('error', 'Lead already assigned');
            // return Redirect::to('/admin/assignee')->with('error', 'Lead already assigned');
            echo json_encode(array('success' => false, 'message' => 'Lead already assigned to '.@$requestData['assignee_name'], 'clientID' => $requestData['client_id']));
            exit;
        }*/

        $followup 				= new \App\Models\Note;
        $followup->client_id		= $this->decodeString(@$requestData['client_id']);
		$followup->user_id			= Auth::user()->id;
		$followup->description		= @$requestData['description'];
		$followup->title		    = @$requestData['remindersubject'] ?? 'Lead assign to '.@$requestData['assignee_name'];
		$followup->folloup	= 1;
        $followup->task_group = @$requestData['task_group'];
		$followup->assigned_to	= @$requestData['rem_cat'];
		$followup->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
		$followup->status = 0; // Required NOT NULL field (0 = active/open, 1 = closed/completed)
		$followup->type = 'client'; // Required field - mark as client type for Action page filtering
		if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
		    //	$followup->followup_date	= @$requestData['followup_date'].date('H:i', strtotime($requestData['followup_time']));
			$followup->followup_date	=  @$requestData['followup_datetime'];
		}
        $saved				=	$followup->save();
        if(!$saved)
		{
			echo json_encode(array('success' => false, 'message' => 'Please try again', 'clientID' => $requestData['client_id']));
		}
		else
		{
			if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
                $Lead = Admin::find($this->decodeString($requestData['client_id']));
                $Lead->followup_date = @$requestData['followup_datetime'];
                $Lead->save();
			}

			$o = new \App\Models\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = @$requestData['rem_cat'];
	    	$o->module_id = $this->decodeString(@$requestData['client_id']);
	    	$o->url = route('clients.detail', @$requestData['client_id']);
	    	$o->notification_type = 'client';
	    	$o->message = 'Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name.' '.date('d/M/Y h:i A',strtotime($Lead->followup_date));
	    	$o->seen = 0; // Set seen to 0 (unseen) for new notifications
	    	$o->save();

			$objs = new ActivitiesLog;
            $objs->client_id = $this->decodeString(@$requestData['client_id']);
            $objs->created_by = Auth::user()->id;
            //$objs->subject = 'Followup set for '.date('d/M/Y h:i A',strtotime($Lead->followup_date));
            $objs->subject = 'set action for '.@$requestData['assignee_name'];
            $objs->description = '<span class="text-semi-bold">'.@$requestData['remindersubject'].'</span><p>'.@$requestData['description'].'</p>';
            $objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
            $objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
            if(Auth::user()->id != @$requestData['rem_cat']){
                $objs->use_for = @$requestData['rem_cat'];
            } else {
                $objs->use_for = null; // Use null instead of empty string for PostgreSQL
            }
            $objs->followup_date = @$requestData['followup_datetime'];
            $objs->task_group = @$requestData['task_group'];
            $objs->save();
			echo json_encode(array('success' => true, 'message' => 'successfully saved', 'clientID' => $requestData['client_id']));
			exit;
		}
	}
	
	//Task reassign and update exist followup
	public function reassignfollowupstore(Request $request){
	    $requestData 		= 	$request->all();
        //echo '<pre>'; print_r($requestData); die;
        /*if(\App\Models\Note::where('client_id',$requestData['client_id'])->where('assigned_to',$requestData['rem_cat'])->exists())
        {
            // return redirect()->back()->with('error', 'Lead already assigned');
            // return Redirect::to('/admin/assignee')->with('error', 'Lead already assigned');
            echo json_encode(array('success' => false, 'message' => 'Lead already assigned to '.@$requestData['assignee_name'], 'clientID' => $requestData['client_id']));
            exit;
        }*/

        $followup = \App\Models\Note::where('id', '=', $requestData['note_id'])->first();
        
        if(!$followup) {
            echo json_encode(array('success' => false, 'message' => 'Note not found', 'clientID' => $requestData['client_id']));
            exit;
        }
        
        $followup->id               = $followup ->id;
		$followup->client_id		= $this->decodeString(@$requestData['client_id']);
		$followup->user_id			= Auth::user()->id;
		$followup->description		= @$requestData['description'];
		$followup->title		    = @$requestData['remindersubject'] ?? 'Lead assign to '.@$requestData['assignee_name'];
		$followup->folloup	= 1;
        $followup->task_group = @$requestData['task_group'];
		$followup->assigned_to	= @$requestData['rem_cat'];
		if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
		    //	$followup->followup_date	= @$requestData['followup_date'].date('H:i', strtotime($requestData['followup_time']));
			$followup->followup_date	=  @$requestData['followup_datetime'];
		}
        $saved				=	$followup->save();
        if(!$saved)
		{
			echo json_encode(array('success' => false, 'message' => 'Please try again', 'clientID' => $requestData['client_id']));
		}
		else
		{
			$Lead = null;
			if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
                $Lead = Admin::find($this->decodeString($requestData['client_id']));
                if($Lead){
                    $Lead->followup_date = @$requestData['followup_datetime'];
                    $Lead->save();
                }
			}

			$o = new \App\Models\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = @$requestData['rem_cat'];
	    	$o->module_id = $this->decodeString(@$requestData['client_id']);
	    	$o->url = route('clients.detail', @$requestData['client_id']);
	    	$o->notification_type = 'client';
	    	$followupDateText = ($Lead && $Lead->followup_date) ? date('d/M/Y h:i A', strtotime($Lead->followup_date)) : (isset($requestData['followup_datetime']) ? date('d/M/Y h:i A', strtotime($requestData['followup_datetime'])) : '');
	    	$o->message = 'Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name.($followupDateText ? ' '.$followupDateText : '');
	    	$o->seen = 0; // Set seen to 0 (unseen) for new notifications
	    	$o->save();

			$objs = new ActivitiesLog;
            $objs->client_id = $this->decodeString(@$requestData['client_id']);
            $objs->created_by = Auth::user()->id;
            //$objs->subject = 'Followup set for '.date('d/M/Y h:i A',strtotime($Lead->followup_date));
            $objs->subject = 'set action for '.@$requestData['assignee_name'];
            $objs->description = '<span class="text-semi-bold">'.@$requestData['remindersubject'].'</span><p>'.@$requestData['description'].'</p>';
            $objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
            $objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
            if(Auth::user()->id != @$requestData['rem_cat']){
                $objs->use_for = @$requestData['rem_cat'];
            } else {
                $objs->use_for = null; // Use null instead of empty string for PostgreSQL
            }
            $objs->followup_date = @$requestData['followup_datetime'];
            $objs->task_group = @$requestData['task_group'];
            $objs->save();
			echo json_encode(array('success' => true, 'message' => 'successfully saved', 'clientID' => $requestData['client_id']));
			exit;
		}
	}

    //Update task follow up and save
	public function updatefollowup(Request $request){
	    $requestData 		= 	$request->all();

        //echo '<pre>'; print_r($requestData); die;
        /*if(\App\Models\Note::where('client_id',$requestData['client_id'])->where('assigned_to',$requestData['rem_cat'])->exists())
        {
            echo json_encode(array('success' => false, 'message' => 'Lead already assigned to '.@$requestData['assignee_name'], 'clientID' => $requestData['client_id']));
            exit;
        }*/

        $followup = \App\Models\Note::where('id', '=', $requestData['note_id'])->first();
        
        if(!$followup) {
            echo json_encode(array('success' => false, 'message' => 'Note not found', 'clientID' => $requestData['client_id']));
            exit;
        }
        
        //$followup 				= new \App\Models\Note;
        $followup->id               = $followup ->id;
		$followup->client_id		= $this->decodeString(@$requestData['client_id']);
		$followup->user_id			= Auth::user()->id;
		$followup->description		= @$requestData['description'];
		$followup->title		    = @$requestData['remindersubject'] ?? 'Update Task and lead assign to '.@$requestData['assignee_name'];
		$followup->folloup	= 1;
        $followup->task_group = @$requestData['task_group'];
		$followup->assigned_to	= @$requestData['rem_cat'];
		if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
		    //	$followup->followup_date	= @$requestData['followup_date'].date('H:i', strtotime($requestData['followup_time']));
			$followup->followup_date	=  @$requestData['followup_datetime'];
		}
        $saved	=	$followup->save();

		if(!$saved)
		{
			echo json_encode(array('success' => false, 'message' => 'Please try again', 'clientID' => $requestData['client_id']));
		}
		else
		{
			$Lead = null;
			if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
                $Lead = Admin::find($this->decodeString($requestData['client_id']));
                if($Lead) {
                    $Lead->followup_date = @$requestData['followup_datetime'];
                    $Lead->save();
                }
			}

			$o = new \App\Models\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = @$requestData['rem_cat'];
	    	$o->module_id = $this->decodeString(@$requestData['client_id']);
	    	$o->url = route('clients.detail', @$requestData['client_id']);
	    	$o->notification_type = 'client';
	    	$followupDateText = ($Lead && $Lead->followup_date) ? date('d/M/Y h:i A', strtotime($Lead->followup_date)) : (isset($requestData['followup_datetime']) ? date('d/M/Y h:i A', strtotime($requestData['followup_datetime'])) : '');
	    	$o->message = 'Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name.($followupDateText ? ' '.$followupDateText : '');
	    	$o->seen = 0; // Set seen to 0 (unseen) for new notifications
	    	$o->save();

			$objs = new ActivitiesLog;
            $objs->client_id = $this->decodeString(@$requestData['client_id']);
            $objs->created_by = Auth::user()->id;
            //$objs->subject = 'Followup set for '.date('d/M/Y h:i A',strtotime($Lead->followup_date));
            $objs->subject = 'Update task for '.@$requestData['assignee_name'];
            $objs->description = '<span class="text-semi-bold">'.@$requestData['remindersubject'].'</span><p>'.@$requestData['description'].'</p>';
            $objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
            $objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
            if(Auth::user()->id != @$requestData['rem_cat']){
                $objs->use_for = @$requestData['rem_cat'];
            } else {
                $objs->use_for = null; // Use null instead of empty string for PostgreSQL
            }

            $objs->followup_date = @$requestData['followup_datetime'];
            $objs->task_group = @$requestData['task_group'];
            $objs->save();
			echo json_encode(array('success' => true, 'message' => 'successfully saved', 'clientID' => $requestData['client_id']));
			exit;
		}
	}


    //Personal followup
    public function personalfollowup(Request $request){
	    $requestData 		= 	$request->all();
        
        // Debug logging (remove in production)
        \Log::info('personalfollowup request data: ', $requestData);

        $client_id = null;
        $req_clientID = "";
        
        if( isset($requestData['client_id']) && $requestData['client_id'] != ''){
            // Check if client_id contains "/" (encoded format)
            if(strpos($requestData['client_id'],"/") !== false){
                $req_client_arr = explode("/",$requestData['client_id']);
                if(!empty($req_client_arr)){
                    $req_clientID = $req_client_arr[0];
                    $client_id = $this->decodeString($req_clientID);
                    if($client_id === false){
                        $client_id = null;
                    }
                }
            } 
            // Check if client_id is a raw integer (from Select2)
            elseif(is_numeric($requestData['client_id'])){
                $client_id = (int)$requestData['client_id'];
                $req_clientID = $requestData['client_id'];
            }
            // Try to decode if it's an encoded string without "/"
            else {
                $decoded = $this->decodeString($requestData['client_id']);
                if($decoded !== false){
                    $client_id = $decoded;
                    $req_clientID = $requestData['client_id'];
                }
            }
        }
        
        \Log::info('personalfollowup parsed client_id: ' . $client_id);

        // Validate that client_id was successfully parsed
        if($client_id === null || $client_id === ''){
            \Log::error('personalfollowup: Invalid client_id. Request: ' . json_encode($requestData));
            echo json_encode(array('success' => false, 'message' => 'Invalid client ID. Please select a valid client.', 'clientID' => $req_clientID));
            exit;
        }

        /*if(\App\Models\Note::where('client_id',$requestData['client_id'])->where('assigned_to',$requestData['rem_cat'])->exists())
        {
            echo json_encode(array('success' => false, 'message' => 'Lead already assigned to '.@$requestData['assignee_name'], 'clientID' => $req_clientID));
            exit;
        }*/
		$followup 					= new \App\Models\Note;
		$followup->client_id		= $client_id;//$this->decodeString(@$requestData['client_id']);
		$followup->user_id			= Auth::user()->id;
		$followup->description		= @$requestData['description'];
		$followup->title		    = @$requestData['remindersubject'] ?? 'Personal Task assign to '.@$requestData['assignee_name'];
		$followup->folloup	= 1;
        $followup->task_group = @$requestData['task_group'];
		$followup->assigned_to	= @$requestData['rem_cat'];
		$followup->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
		$followup->status = 0; // Required NOT NULL field (0 = active/open, 1 = closed/completed)
		$followup->type = 'client'; // Required field - mark as client type
		if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
		    $followup->followup_date	=  @$requestData['followup_datetime'];
		}
        try {
            $saved	=	$followup->save();
        } catch (\Exception $e) {
            \Log::error('Error saving followup in personalfollowup: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());
            echo json_encode(array('success' => false, 'message' => 'Error saving action: ' . $e->getMessage(), 'clientID' => $client_id));
            exit;
        }
        
        if(!$saved)
		{
			echo json_encode(array('success' => false, 'message' => 'Please try again', 'clientID' => $client_id)); //$requestData['client_id']
		}
		else
		{
			// Validate receiver_id before creating notification
			if(isset($requestData['rem_cat']) && $requestData['rem_cat'] != ''){
				$o = new \App\Models\Notification;
				$o->sender_id = Auth::user()->id;
				$o->receiver_id = $requestData['rem_cat'];
				$o->module_id = $client_id; // Use the parsed client_id (integer)
				$o->url = route('clients.detail', base64_encode(convert_uuencode($client_id)));
				$o->notification_type = 'client';
				// Safely format date
				$followupDateText = '';
				if(isset($requestData['followup_datetime']) && $requestData['followup_datetime'] != ''){
					$timestamp = strtotime($requestData['followup_datetime']);
					if($timestamp !== false){
						$followupDateText = ' '.date('d/M/Y h:i A', $timestamp);
					}
				}
				$o->message = 'Personal Task Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name.$followupDateText;
				$o->seen = 0; // Set seen to 0 (unseen) for new notifications
				$o->save();
			}

			$objs = new ActivitiesLog;
            $objs->client_id = $client_id;//$this->decodeString(@$requestData['client_id']);
            $objs->created_by = Auth::user()->id;
            $objs->subject = 'set action for '.@$requestData['assignee_name'];
            $objs->description = '<span class="text-semi-bold">'.@$requestData['remindersubject'].'</span><p>'.@$requestData['description'].'</p>';
            if(Auth::user()->id != @$requestData['rem_cat']){
                $objs->use_for = @$requestData['rem_cat'];
            } else {
                $objs->use_for = null; // Use null instead of empty string for PostgreSQL
            }
            $objs->followup_date = @$requestData['followup_datetime'];
            $objs->task_group = @$requestData['task_group'];
            $objs->task_status = 0; // Required NOT NULL field for PostgreSQL (0 = activity, 1 = task)
            $objs->pin = 0; // Required NOT NULL field for PostgreSQL (0 = not pinned, 1 = pinned)
            $objs->save();
			echo json_encode(array('success' => true, 'message' => 'successfully saved', 'clientID' => $client_id)); //$requestData['client_id']
			exit;
		}
	}
	
	
	public function retagfollowup(Request $request){
	    $requestData 		= 	$request->all();

        //	echo '<pre>'; print_r($requestData); die;
		$followup 					= new \App\Models\Note;
		$followup->client_id			= @$requestData['client_id'];
		$followup->user_id			= Auth::user()->id;
		$followup->description		= @$requestData['message'];
		$followup->title			= '';
		$followup->folloup	        = 1;
		$followup->assigned_to	    = @$requestData['changeassignee'];
		$followup->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
		$followup->status = 0; // Required NOT NULL field (0 = active/open, 1 = closed/completed)
		$followup->type = 'client'; // Required field - mark as client type for Action page filtering
		if(isset($requestData['followup_date']) && $requestData['followup_date'] != ''){

				$followup->followup_date	=  $requestData['followup_date'].' '.date('H:i', strtotime($requestData['followup_time']));
		}

		$saved				=	$followup->save();

		if(!$saved)
		{
		return redirect()->route('followup.index')->with('error', 'Please try again');
		}
		else
		{
		   /*$objnote =  \App\Models\Note::find();
		   $objnote->status = 1;
		   $objnote->save();*/
		    $newassignee = Admin::find($requestData['changeassignee']);
			$o = new \App\Models\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = @$requestData['changeassignee'];
	    	$o->module_id = @$requestData['client_id'];
	    	$o->url = route('clients.detail', @$requestData['client_id']);
	    	$o->notification_type = 'client';
	    	$o->message = 'Followup Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name;
	    	$o->seen = 0; // Set seen to 0 (unseen) for new notifications
	    	$o->save();

			$objs = new ActivitiesLog;
				$objs->client_id = @$requestData['client_id'];
				$objs->created_by = Auth::user()->id;
				$objs->subject = Auth::user()->first_name.' '.Auth::user()->last_name.' tags work to '.$newassignee->first_name.' '.$newassignee->last_name;
				$objs->description = @$requestData['message'];
				$objs->task_status = 0; // Required NOT NULL field (0 = activity, 1 = task)
				$objs->pin = 0; // Required NOT NULL field (0 = not pinned, 1 = pinned)
				$objs->save();
		return redirect()->route('followup.index')->with('success', 'Record Updated successfully');
		}
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
