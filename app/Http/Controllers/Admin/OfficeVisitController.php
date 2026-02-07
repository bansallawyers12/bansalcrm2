<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Lead;
use App\Models\CheckinLog;
use App\Models\CheckinHistory;
use App\Models\ActivitiesLog;
use App\Events\OfficeVisitNotificationCreated;
 
use Auth;
use Config;

class OfficeVisitController extends Controller
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
	 
	public function checkin(Request $request){
		try {
			//dd($request->all());
			// Handle Select2 multiple select - get first value if array
			$contactValue = $request->input('contact');
			if (is_array($contactValue)) {
				$contactValue = !empty($contactValue) ? $contactValue[0] : null;
			}
			
			// Validate required fields
			$rules = [
				'assignee' => 'required|integer',
				'message' => 'required|string',
				'office' => 'required|integer',
				'utype' => 'required|string',
			];
			
			$messages = [
				'assignee.required' => 'Please select an assignee.',
				'assignee.integer' => 'Invalid assignee selected.',
				'message.required' => 'Visit purpose is required.',
				'office.required' => 'Please select an office.',
				'office.integer' => 'Invalid office selected.',
				'utype.required' => 'Contact type is required. Please select a contact.',
			];
			
			// Validate contact separately
			if (empty($contactValue)) {
				return redirect()->back()
					->withErrors(['contact' => 'Please select a contact.'])
					->withInput();
			}
			
			$contactId = (int) $contactValue;
			if ($contactId <= 0) {
				return redirect()->back()
					->withErrors(['contact' => 'Please select a valid contact.'])
					->withInput();
			}
			
			// Validate other fields
			$validated = $request->validate($rules, $messages);

			// Get validated data
			$assigneeId = (int) $validated['assignee'];
			$officeId = (int) $validated['office'];
			$visitPurpose = trim($validated['message'] ?? '');
			
			// Normalize contact type
			$utypeRaw = strtolower(trim($validated['utype'] ?? ''));
			if ($utypeRaw === 'lead') {
				$contactType = 'Lead';
			} elseif ($utypeRaw === 'client') {
				$contactType = 'Client';
			} else {
				$contactType = 'Client';
			}
			// Verify contact exists based on type (Lead or Client)
			$contactExists = Admin::where('role', '7')->where('id', $contactId)->exists();
			
			if (!$contactExists) {
				return redirect()->back()->with('error', 'Selected contact does not exist.');
			}

			// Verify assignee exists
			$assigneeExists = Admin::where('role', '!=', '7')->where('id', $assigneeId)->exists();
			if (!$assigneeExists) {
				return redirect()->back()->with('error', 'Selected assignee does not exist.');
			}

			// Verify office exists
			$officeExists = \App\Models\Branch::where('id', $officeId)->exists();
			if (!$officeExists) {
				return redirect()->back()->with('error', 'Selected office does not exist.');
			}

			// Wrap all database operations in a transaction
			DB::beginTransaction();

			try {
				// Create CheckinLog
				$obj = new \App\Models\CheckinLog;
				$obj->client_id = $contactId;
				$obj->user_id = $assigneeId;
				$obj->visit_purpose = $visitPurpose;
				$obj->office = $officeId;
				$obj->contact_type = $contactType;
				$obj->status = 0;
				$obj->date = date('Y-m-d');
				
				if (!$obj->save()) {
					throw new \Exception('Failed to save check-in log.');
				}

				// Create Notification
				$notification = new \App\Models\Notification;
				$notification->sender_id = Auth::user()->id;
				$notification->receiver_id = $assigneeId;
				$notification->module_id = $obj->id;
				$notification->url = \URL::to('/office-visits/waiting');
				$notification->notification_type = 'officevisit';
				$notification->message = 'Office visit Assigned by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name;
				$notification->seen = 0;
				$notification->receiver_status = 0;
				$notification->sender_status = 1;
				
				if (!$notification->save()) {
					throw new \Exception('Failed to save notification.');
				}

				// Get contact name for broadcasting (Lead or Client)
				$contact = Admin::where('role', '7')->find($contactId);
				$clientName = $contact ? $contact->first_name . ' ' . $contact->last_name : 'Unknown Client';
				

				// Broadcast real-time notification (optional - wrap in try-catch)
				try {
					broadcast(new OfficeVisitNotificationCreated(
						$notification->id,
						$notification->receiver_id,
						[
							'id' => $notification->id,
							'checkin_id' => $obj->id,
							'message' => $notification->message,
							'sender_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
							'client_name' => $clientName,
							'visit_purpose' => $obj->visit_purpose,
							'created_at' => $notification->created_at ? $notification->created_at->format('d/m/Y h:i A') : now()->format('d/m/Y h:i A'),
							'url' => $notification->url
						]
					));
				} catch (\Exception $broadcastException) {
					// Log broadcast error but don't fail the entire operation
					\Log::warning('Failed to broadcast office visit notification', [
						'notification_id' => $notification->id,
						'error' => $broadcastException->getMessage()
					]);
				}

				// Create CheckinHistory
				$checkinHistory = new CheckinHistory;
				$checkinHistory->subject = 'has created check-in';
				$checkinHistory->created_by = Auth::user()->id;
				$checkinHistory->checkin_id = $obj->id;
				
				if (!$checkinHistory->save()) {
					throw new \Exception('Failed to save check-in history.');
				}

				// OPTIONAL: Keep ActivitiesLog for backward compatibility
				// Create activity log for clients (not leads)
				if($contactType == 'Client') {
					$activityLog = new ActivitiesLog;
					$activityLog->client_id = $contactId;
					$activityLog->created_by = Auth::user()->id;
					$activityLog->subject = 'has created check-in';
					$activityLog->description = !empty($visitPurpose) ? 'Visit Purpose: ' . $visitPurpose : '';
					$activityLog->task_status = 0;
					$activityLog->pin = 0;
					$activityLog->save();
				}

				// Commit transaction
				DB::commit();

				return redirect()->back()->with('success', 'Checkin updated successfully');

			} catch (\Exception $e) {
				// Rollback transaction on any error
				DB::rollBack();
				
				// Log the error for debugging
				\Log::error('Checkin creation failed', [
					'error' => $e->getMessage(),
					'trace' => $e->getTraceAsString(),
					'request_data' => $request->except(['_token'])
				]);

				return redirect()->back()->with('error', 'Failed to create check-in. Please try again.');
			}

		} catch (\Illuminate\Validation\ValidationException $e) {
			// Return validation errors
			return redirect()->back()
				->withErrors($e->errors())
				->withInput();
		} catch (\Exception $e) {
			// Catch any unexpected errors
			\Log::error('Unexpected error in checkin method', [
				'error' => $e->getMessage(),
				'trace' => $e->getTraceAsString()
			]);
			
			return redirect()->back()->with('error', Config::get('constants.server_error'));
		}
	}
	
	public function getcheckin(Request $request)
	{
	    
		$CheckinLog = CheckinLog::with('office')->where('id', '=', $request->id)->first(); 
		if($CheckinLog){
			ob_start();
			// Resolve contact by type: Lead or Client
			$contact = Admin::where('role', '7')->where('id', $CheckinLog->client_id)->first();
			$contactDetailUrl = $contact ? route('clients.detail', base64_encode(convert_uuencode($contact->id))) : '#';
			$contactName = $contact ? $contact->first_name . ' ' . $contact->last_name : '—';
			$contactEmail = $contact ? $contact->email : '';
			
			?>
			<div class="row">
				<div class="col-md-12">
					<?php
					if($CheckinLog->status == 0){
						?>
						<h5 class="text-warning">Waiting</h5>
						<?php
					}else if($CheckinLog->status == 2){
						?>
						<h5 class="text-info">Attending</h5>
						<?php
					}else if($CheckinLog->status == 1){
						?>
						<h5 class="text-success">Completed</h5>
						<?php
					}
					?>
				</div>
			</div>
			<div class="row">
					<div class="col-md-6">
						<b>Contact</b>
						<div class="clientinfo">
							<a href="<?php echo e($contactDetailUrl); ?>"><?php echo e($contactName); ?></a>
							<?php if ($contactEmail !== '') { ?>
							<br>
							<?php echo e($contactEmail); ?>
							<?php } ?>
						</div>
					</div>
					<div class="col-md-6">
						<b><?php echo $CheckinLog->contact_type; ?></b>
						<br>
						<?php
						$branch = is_object($CheckinLog->office) ? $CheckinLog->office : \App\Models\Branch::find($CheckinLog->getAttribute('office'));
						if ($branch) {
							echo '<a target="_blank" href="'.\URL::to('/branch/view/'.$branch->id).'">'.$branch->office_name.'</a>';
						}
						?>
						
					</div>
					
					<div class="col-md-12">
						<div class="form-group">
							<label>Visit Purpose</label>
								<textarea class="form-control visitpurpose" data-id="<?php echo $CheckinLog->id; ?>" ><?php echo $CheckinLog->visit_purpose; ?></textarea>
						</div>
					</div>
					
					<div class="col-md-7">
						<table class="table">
						<thead>
								<tr>
									<th>In Person Date</th>
									<th>Session Start</th>
									<th>Session End</th>
								</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php echo date('Y-m-d',strtotime($CheckinLog->created_at)); ?></td>
								<td><?php if($CheckinLog->sesion_start != '') { echo date('h:i A',strtotime($CheckinLog->sesion_start)); }else{ echo '-'; } ?></td>
								<td><?php if($CheckinLog->sesion_end != '') { echo date('h:i A',strtotime($CheckinLog->sesion_end)); }else{ echo '-'; } ?></td>
							</tr>
							
							</tbody>
						</table>
					</div>
					<div class="col-md-5">
						<div style="padding: 6px 8px; border-radius: 4px; background-color: rgb(84, 178, 75); margin-top: 14px;">
						<div class="row">
						<div class="col-md-6">
							<div class="ag-flex col-hr-3" style="flex-direction: column;"><p class="marginNone text-semi-bold text-white">Wait Time</p> <p class="marginNone small  text-white"><?php if($CheckinLog->status == 0){ ?><span id="waitcount"> 00h 0m 0s </span><?php }else if($CheckinLog->status == 2){ echo '<span>'.$CheckinLog->wait_time.'</span>'; }else if($CheckinLog->status == 1){ echo '<span>'.$CheckinLog->wait_time.'</span>'; }else{ echo '<span >-</span>'; } ?></p></div></div>
							<div class="col-md-6">
							<div class="ag-flex" style="flex-direction: column;"><p class="marginNone text-semi-bold  text-white">Attend Time</p> <p class="marginNone small  text-white"><?php if($CheckinLog->status == 2){ ?><span id="attendtime"> 00h 0m 0s </span><?php }else if($CheckinLog->status == 1){ echo '<span>'.$CheckinLog->attend_time.'</span>'; }else{ echo '<span >-</span>'; } ?>
							
							</p></div></div>
							</div>
						</div>
					</div>
					<div class="col-md-7">
						<b>In Person Assignee </b> <a class="openassignee" href="javascript:;"><i class="fa fa-edit"></i></a>
						<br>
						<?php
						$admin = \App\Models\Admin::where('role', '!=', '7')->where('id', '=', $CheckinLog->user_id)->first();
						?>
						<a href=""><?php echo @$admin->first_name.' '.@$admin->last_name; ?></a>
						<br>
						<span><?php echo @$admin->email; ?></span>
					</div>
						<div class="assignee" style="display:none;">
						    <div class="row">
						        <div class="col-md-8">
						            <select class="form-control select2" id="changeassignee" name="changeassignee">
						                 <?php 
											foreach(\App\Models\Admin::with('office')->where('role','!=',7)->orderby('first_name','ASC')->get() as $admin){
												$officeName = $admin->office ? $admin->office->office_name : '';
										?>
												<option value="<?php echo $admin->id; ?>"><?php echo $admin->first_name.' '.$admin->last_name.' ('.$officeName.')'; ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="col-md-2">
									<a class="saveassignee btn btn-success" data-id="<?php echo $CheckinLog->id; ?>" href="javascript:;">Save</a>
								</div>
								<div class="col-md-2">
									<a class="closeassignee" href="javascript:;"><i class="fa fa-times"></i></a>
								</div>
							</div>
						</div>
				
					<div class="col-md-5">
					<?php
					if($CheckinLog->status == 0){
					?>
						<a data-id="<?php echo $CheckinLog->id; ?>" href="javascript:;" class="btn btn-success attendsession">Attend Session</a>
					<?php }else if($CheckinLog->status == 2){ ?>
						<a data-id="<?php echo $CheckinLog->id; ?>" href="javascript:;" class="btn btn-success completesession">Complete Session</a>
					<?php } ?>
					</div>
					<input type="hidden" value="" id="waitcountdata">
					<input type="hidden" value="" id="attendcountdata">
					<div class="col-md-12">
						<div class="form-group">
							<label>Comment</label>
							<textarea class="form-control visit_comment" name="comment"></textarea>
						</div>
						<div class="form-group">
							<button data-id="<?php echo $CheckinLog->id; ?>" type="button" class="savevisitcomment btn btn-primary">Save</button>
						</div>
					</div>
					
					<div class="col-md-12">
						<h4>Logs</h4>
						<div class="logsdata">
						<?php
						$logslist = CheckinHistory::where('checkin_id',$CheckinLog->id)->orderby('created_at', 'DESC')->get();						
						foreach($logslist as $llist){
							$admin = \App\Models\Admin::where('id', $llist->created_by)->first();
						?>
							<div class="logsitem">
								<div class="row">
									<div class="col-md-7">
										<span class="ag-avatar"><?php echo substr($admin->first_name, 0, 1); ?></span>
										<span class="text_info"><span><?php echo $admin->first_name; ?></span><?php echo $llist->subject; ?></span>
									</div>
									<div class="col-md-5">
										<span class="logs_date"><?php echo date('d M Y h:i A', strtotime($llist->created_at)); ?></span>
									</div>
									<?php if($llist->description != ''){ ?>
									<div class="col-md-12 logs_comment">
										<p><?php echo $llist->description; ?></p>
									</div>
									<?php } ?>
								</div>
							</div>
						<?php } ?> 
						</div>
					</div>
				</div>
				<script>
				function pretty_time_stringd(num) {
					return ( num < 10 ? "0" : "" ) + num;
				}
				var start = new Date('<?php echo date('Y-m-d H:i:s',strtotime($CheckinLog->created_at)); ?>');
				setInterval(function() {
				  var total_seconds = (new Date - start) / 1000;   

				  var hours = Math.floor(total_seconds / 3600);
				  total_seconds = total_seconds % 3600;

				  var minutes = Math.floor(total_seconds / 60);
				  total_seconds = total_seconds % 60;

				  var seconds = Math.floor(total_seconds);

				  hours = pretty_time_stringd(hours);
				  minutes = pretty_time_stringd(minutes);
				  seconds = pretty_time_stringd(seconds);

				  var currentTimeString = hours + "h:" + minutes + "m:" + seconds+'s';

				  $('#waitcount').text(currentTimeString);
				  $('#waitcountdata').val(currentTimeString);
				}, 1000);
				<?php
				if($CheckinLog->status == 2){
					?>
					var start = new Date('<?php echo date('Y-m-d H:i:s',strtotime($CheckinLog->sesion_start)); ?>');
				setInterval(function() {
				  var total_seconds = (new Date - start) / 1000;   

				  var hours = Math.floor(total_seconds / 3600);
				  total_seconds = total_seconds % 3600;

				  var minutes = Math.floor(total_seconds / 60);
				  total_seconds = total_seconds % 60;

				  var seconds = Math.floor(total_seconds);

				  hours = pretty_time_stringd(hours);
				  minutes = pretty_time_stringd(minutes);
				  seconds = pretty_time_stringd(seconds);

				  var currentTimeString = hours + "h:" + minutes + "m:" + seconds+'s';

				  $('#attendtime').text(currentTimeString);
				  $('#attendcountdata').val(currentTimeString);
				}, 1000);
					<?php
				}
				?>
				</script>
			<?php
			return ob_get_clean();
		}
	}
	
	
	public function update_visit_purpose(Request $request){
		$obj = CheckinLog::find($request->id);
		$obj->visit_purpose = $request->visit_purpose;
		$saved = $obj->save();
		if($saved){
			$response['status'] 	= 	true;
			$response['message']	=	'saved successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
	
	public function update_visit_comment(Request $request){
		$objs = new CheckinHistory;
		$objs->subject = 'has commented';
		$objs->created_by = Auth::user()->id;
		$objs->checkin_id = $request->id;
		$objs->description = $request->visit_comment;
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
	
	public function change_assignee(Request $request){
		$objs = CheckinLog::find($request->id);
		$objs->user_id = $request->assinee;
	
		$saved = $objs->save();
		if($objs->status == 2){
		    $t = 'attending';
		}else if($objs->status == 1){
		    $t = 'completed';
		}else if($objs->status == 0){
		    $t = 'waiting';
		}
		if($saved){
		    $o = new \App\Models\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$o->receiver_id = $request->assinee;
	    	$o->module_id = $request->id;
	    	$o->url = \URL::to('/office-visits/'.$t);
	    	$o->notification_type = 'officevisit';
	    	$o->message = 'Office Visit Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name;
	    	$o->seen = 0;
	    	$o->receiver_status = 0;
	    	$o->sender_status = 1;
	    	$o->save();
	    	
	    	// Broadcast notification (optional)
	    	try {
	    	    $client = Admin::where('role', '7')->find($objs->client_id);
	    	    
	    	    broadcast(new OfficeVisitNotificationCreated(
	    	        $o->id,
	    	        $o->receiver_id,
	    	        [
	    	            'id' => $o->id,
	    	            'checkin_id' => $objs->id,
	    	            'message' => $o->message,
	    	            'sender_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
	    	            'client_name' => $client ? $client->first_name . ' ' . $client->last_name : 'Unknown Client',
	    	            'visit_purpose' => $objs->visit_purpose,
	    	            'created_at' => $o->created_at ? $o->created_at->format('d/m/Y h:i A') : now()->format('d/m/Y h:i A'),
	    	            'url' => $o->url
	    	        ]
	    	    ));
	    	} catch (\Exception $e) {
	    	    \Log::warning('Failed to broadcast change assignee notification', [
	    	        'error' => $e->getMessage()
	    	    ]);
	    	}
	    	
			$response['status'] 	= 	true;
			$response['message']	=	'Updated successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
	
	/*public function attend_session(Request $request){
		$obj = CheckinLog::find($request->id);
		$obj->sesion_start = date('Y-m-d H:i');
		$obj->wait_time = $request->waitcountdata;
		$obj->status = 2;
		$saved = $obj->save();
		
		$objs = new CheckinHistory;
		$objs->subject = 'has started session';
		$objs->created_by = Auth::user()->id;
		$objs->checkin_id = $request->id;
		$saved = $objs->save();
		if($saved){
			$response['status'] 	= 	true;
			$response['message']	=	'saved successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}*/
	
	public function attend_session(Request $request){
		$obj = CheckinLog::find($request->id);
		$obj->sesion_start = date('Y-m-d H:i');
		$obj->wait_time = $request->waitcountdata;

        if($request->waitingtype == 1){ // Pls send (reception sent client / assignee confirmed) → start attending
            $obj->status = 2; //attending session
            $t = 'attending';
        } else {  // Assignee clicked "Waiting" → notify reception to send the client
            $obj->status = 0; // keep waiting
            $obj->wait_type = 1; // Pls send
            $t = 'waiting';
        }

        $saved = $obj->save();

        // When assignee clicks "Waiting" (waitingtype != 1): notify reception to send the client. When "Pls send" (waitingtype == 1): no popup.
        if ($saved && $request->waitingtype != 1) {
		    $o = new \App\Models\Notification;
	    	$o->sender_id = Auth::user()->id;
	    	$receptionId = config('constants.reception_user_id');
	    	$o->receiver_id = $receptionId ? (int) $receptionId : $obj->user_id;
	    	$o->module_id = $request->id;
	    	$o->url = \URL::to('/office-visits/waiting');
	    	$o->notification_type = 'officevisit';
	    	$o->message = 'Office Visit Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name;
	    	$o->seen = 0;
	    	$o->receiver_status = 0;
	    	$o->sender_status = 1;
	    	$o->save();
	    	
	    	try {
	    	    $client = Admin::where('role', '7')->find($obj->client_id);
	    	    broadcast(new OfficeVisitNotificationCreated(
	    	        $o->id,
	    	        $o->receiver_id,
	    	        [
	    	            'id' => $o->id,
	    	            'checkin_id' => $obj->id,
	    	            'message' => $o->message,
	    	            'sender_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
	    	            'client_name' => $client ? $client->first_name . ' ' . $client->last_name : 'Unknown Client',
	    	            'visit_purpose' => $obj->visit_purpose,
	    	            'created_at' => $o->created_at ? $o->created_at->format('d/m/Y h:i A') : now()->format('d/m/Y h:i A'),
	    	            'url' => $o->url
	    	        ]
	    	    ));
	    	} catch (\Exception $e) {
	    	    \Log::warning('Failed to broadcast attend session notification', ['error' => $e->getMessage()]);
	    	}
		}

		// Create CheckinHistory
		$objs = new CheckinHistory;
		$objs->subject = 'has started session';
		$objs->created_by = Auth::user()->id;
		$objs->checkin_id = $request->id;
		$saved = $objs->save();
		
		// Keep ActivitiesLog for backward compatibility
		if($obj->contact_type == 'Client') {
			$activityLog = new ActivitiesLog;
			$activityLog->client_id = $obj->client_id;
			$activityLog->created_by = Auth::user()->id;
			$activityLog->subject = 'has started check-in session';
			$activityLog->description = !empty($obj->visit_purpose) ? 'Visit Purpose: ' . $obj->visit_purpose : '';
			$activityLog->task_status = 0;
			$activityLog->pin = 0;
			$activityLog->save();
		}
		
		if($saved){
			$response['status'] 	= 	true;
			$response['message']	=	'saved successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
	
	public function complete_session(Request $request){
		$obj = CheckinLog::find($request->id);
		$obj->sesion_end = date('Y-m-d H:i');
		$obj->attend_time = $request->attendcountdata;
		$obj->status = 1;
		$saved = $obj->save();
		
		// Create CheckinHistory
		$objs = new CheckinHistory;
		$objs->subject = 'has completed session';
		$objs->created_by = Auth::user()->id;
		$objs->checkin_id = $request->id;
		$saved = $objs->save();
		
		// Keep ActivitiesLog for backward compatibility
		if($obj->contact_type == 'Client') {
			$activityLog = new ActivitiesLog;
			$activityLog->client_id = $obj->client_id;
			$activityLog->created_by = Auth::user()->id;
			$activityLog->subject = 'has completed check-in session';
			$description = !empty($obj->visit_purpose) ? 'Visit Purpose: ' . $obj->visit_purpose : '';
			if(!empty($obj->attend_time)) {
				$description .= ($description ? ' | ' : '') . 'Session Duration: ' . $obj->attend_time;
			}
			$activityLog->description = $description;
			$activityLog->task_status = 0;
			$activityLog->pin = 0;
			$activityLog->save();
		}
		
		if($saved){
			$response['status'] 	= 	true;
			$response['message']	=	'saved successfully';
			// Return updated counts so office-visits tab badges can refresh without full page reload
			$response['waiting']   = CheckinLog::where('status', 0)->count();
			$response['attending'] = CheckinLog::where('status', 2)->count();
			$response['completed'] = CheckinLog::where('status', 1)->count();
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}
	/**
	 * GET: Fetch office visit notifications for current user (unread, checkin still waiting).
	 * Used by Teams-style popup on load and for badge count.
	 */
	public function fetchOfficeVisitNotifications(Request $request)
	{
		$userId = Auth::user()->id;
		$notifications = \App\Models\Notification::where('receiver_id', $userId)
			->where('notification_type', 'officevisit')
			->where('receiver_status', 0)
			->whereHas('checkinLog', function ($q) {
				$q->where('status', 0);
			})
			->orderBy('created_at', 'desc')
			->get()
			->map(function ($n) {
				$log = $n->checkinLog;
				$client = Admin::where('role', '7')->find($log->client_id);
				$sender = Admin::find($n->sender_id);
				return [
					'id' => $n->id,
					'checkin_id' => $log->id,
					'message' => $n->message,
					'sender_name' => $sender ? $sender->first_name . ' ' . $sender->last_name : '',
					'client_name' => $client ? $client->first_name . ' ' . $client->last_name : 'Unknown Client',
					'visit_purpose' => $log->visit_purpose,
					'created_at' => $n->created_at ? $n->created_at->format('d/m/Y h:i A') : '',
					'url' => $n->url,
				];
			});
		return response()->json(['notifications' => $notifications, 'count' => $notifications->count()]);
	}

	/**
	 * POST: Mark an office visit notification as seen (receiver_status = 1).
	 */
	public function markNotificationSeen(Request $request)
	{
		$n = \App\Models\Notification::where('id', $request->input('notification_id'))
			->where('receiver_id', Auth::user()->id)
			->where('notification_type', 'officevisit')
			->first();
		if ($n) {
			$n->receiver_status = 1;
			$n->save();
		}
		return response()->json(['status' => true]);
	}

	/**
	 * POST: Update check-in status (e.g. reception "Pls Send The Client": status=0, wait_type=1).
	 * Optionally pass notification_id to mark that notification seen.
	 */
	public function updateCheckinStatus(Request $request)
	{
		$checkinId = (int) $request->input('checkin_id');
		$obj = CheckinLog::find($checkinId);
		if (!$obj) {
			return response()->json(['status' => false, 'message' => 'Check-in not found.'], 404);
		}
		if ($request->has('status')) {
			$obj->status = (int) $request->input('status');
		}
		if ($request->has('wait_type')) {
			$obj->wait_type = (int) $request->input('wait_type');
		}
		$obj->save();

		$notificationId = $request->input('notification_id');
		if ($notificationId) {
			$n = \App\Models\Notification::where('id', $notificationId)
				->where('receiver_id', Auth::user()->id)
				->first();
			if ($n) {
				$n->receiver_status = 1;
				$n->save();
			}
		}
		return response()->json(['status' => true, 'message' => 'saved successfully']);
	}

	public function waiting(Request $request)
	{
	      if(isset($request->t)){
    	    if(\App\Models\Notification::where('id', $request->t)->exists()){
    	       $ovv =  \App\Models\Notification::find($request->t);
    	       $ovv->receiver_status = 1;
    	       $ovv->save();
    	    }
	    }
		$query 		= CheckinLog::with('office')->where('status', '=', 0); 
		 
		$totalData 	= $query->count();	//for all data
		if($request->has('office')){
			$office 		= 	$request->input('office'); 
			if(trim($office) != '')
			{
				$query->where('office', '=', $office);
			}
		}
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		$activeTab = 'waiting';
		return view('Admin.officevisits.index', compact('lists', 'totalData', 'activeTab'));  
	}
	public function attending(Request $request)
	{
	      if(isset($request->t)){
    	    if(\App\Models\Notification::where('id', $request->t)->exists()){
    	       $ovv =  \App\Models\Notification::find($request->t);
    	       $ovv->receiver_status = 1;
    	       $ovv->save();
    	    }
	    }
		$query 		= CheckinLog::with('office')->where('status', '=', '2'); 
		 
		$totalData 	= $query->count();	//for all data
		if($request->has('office')){
			$office 		= 	$request->input('office'); 
			if(trim($office) != '')
			{
				$query->where('office', '=', $office);
			}
		}
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));//dd($lists);
		
		$activeTab = 'attending';
		return view('Admin.officevisits.index', compact('lists', 'totalData', 'activeTab'));  
	}
	public function completed(Request $request)
	{
	      if(isset($request->t)){
    	    if(\App\Models\Notification::where('id', $request->t)->exists()){
    	       $ovv =  \App\Models\Notification::find($request->t);
    	       $ovv->receiver_status = 1;
    	       $ovv->save();
    	    }
	    }
		$query 		= CheckinLog::with('office')->where('status', '=', '1'); 
		 
		$totalData 	= $query->count();	//for all data
		if($request->has('office')){
			$office 		= 	$request->input('office'); 
			if(trim($office) != '')
			{
				$query->where('office', '=', $office);
			}
		}
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		$activeTab = 'completed';
		return view('Admin.officevisits.index', compact('lists', 'totalData', 'activeTab'));  
	}
	public function create(Request $request){
		return view('Admin.officevisits.create');	
	} 

}
