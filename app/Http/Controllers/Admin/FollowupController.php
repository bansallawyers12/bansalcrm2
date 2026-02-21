<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Followup;
use App\Models\FollowupType;
// NOTE: Attachment model/table has been removed
// use App\Models\Attachment;
 
use Auth;
use Config;
class FollowupController extends Controller
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
	
	public function index(Request $request)
	{
		$id = $this->decodeString($request->leadid);
		// Admin model: resolve by admins.id or lead_id (leads.id for migrated)
		$admin = Admin::where('id', '=', $id)->where('type', 'lead')->first()
			?? Admin::where('lead_id', '=', $id)->where('type', 'lead')->first();
		if ($admin) 
		{
			$query = Followup::query()->with(['staff']);
			if ($admin->lead_id) {
				$query->where('lead_id', '=', $admin->lead_id);
			} else {
				$query->where('client_id', '=', $admin->id);
			}
			$totalData = $query->count();
			$lists = $query->orderby('pin', 'DESC')->orderby('created_at', 'DESC')->paginate(config('constants.limit')); 
			return view('Admin.leads.list', compact(['lists', 'totalData'])); 
		}
	} 
	
	public function compose(Request $request){
		$requestData 		= 	$request->all();
		$ledID = $this->decodeString(@$requestData['lead_id']);
		$admin = Admin::where('id', $ledID)->where('type', 'lead')->first()
			?? Admin::where('lead_id', $ledID)->where('type', 'lead')->first();
		if (!$admin) {
			return redirect()->back()->with('error', 'Lead not found');
		}
		$assi = \App\Models\Staff::find($admin->assignee);
		$message = @$requestData['message'];
		$subject = @$requestData['subject'];
		$subject = str_replace('{Client First Name}', $admin->first_name ?? '', $subject);
		$message = str_replace('{Client First Name}', $admin->first_name ?? '', $message);	
		$message = str_replace('{Company Name}', 'Bansal Education', $message);
		$message = str_replace('{Client Assignee Name}', $assi ? $assi->first_name : '', $message);
		$followup = new Followup;
		// For admin-only use client_id; for migrated use lead_id
		if ($admin->lead_id) {
			$followup->lead_id = $admin->lead_id;
		} else {
			$followup->client_id = $admin->id;
		}
		$followup->user_id			= Auth::user()->id;
		
		$followup->note				= $message;
		$followup->subject			= @$subject;
		$followup->followup_type	= 'mail_compose';
		//$followup->rem_cat	= @$requestData['rem_cat'];
		$saved				=	$followup->save();  
			
		if(!$saved) 
		{
			return redirect()->back()->with('error', Config::get('constants.server_error'));
		}
		else
		{ 
		   
			    	$ccarray = array();
			    		$array = array();
			    			
			
		$issuccess = $this->send_compose_template($message, 'bansalcrm', $requestData['email_to'],$subject,'noreply@bansalcrm.com', $array,@$ccarray);
	
		return Redirect::to('/admin/leads')->with('success', 'Mail sent successpully Successfully');
		}
	}
	public function store(Request $request)
	{ 
		$requestData = $request->all();
		$decodedId = $this->decodeString(@$requestData['lead_id']);
		$admin = Admin::where('id', $decodedId)->where('type', 'lead')->first()
			?? Admin::where('lead_id', $decodedId)->where('type', 'lead')->first();
		if (!$admin) {
			echo json_encode(array('success' => false, 'message' => 'Lead not found', 'leadid' => $requestData['lead_id']));
			return;
		}
		$followup = new Followup;
		if ($admin->lead_id) {
			$followup->lead_id = $admin->lead_id;
		} else {
			$followup->client_id = $admin->id;
		}
		$followup->user_id = Auth::user()->id;
		$followup->note = @$requestData['description'];
		$followup->subject = @$requestData['remindersubject'];
		$followup->followup_type = @$requestData['note_type'];
		$followup->rem_cat = @$requestData['rem_cat'];
		if(isset($requestData['followup_date']) && $requestData['followup_date'] != ''){
			$followup->followup_date = @$requestData['followup_date'].' '.date('H:i', strtotime($requestData['followup_time']));
		}
		$saved = $followup->save();  
		if(!$saved) 
		{
			echo json_encode(array('success' => false, 'message' => 'Please try again', 'leadid' => $requestData['lead_id']));
		}
		else
		{ 
			$note_type = $this->followuptype($requestData['note_type'], 'id');
			Admin::where('id', $admin->id)->update(['status' => $note_type]);
			echo json_encode(array('success' => true, 'message' => 'successfully saved', 'leadid' => $requestData['lead_id']));
		}
	}
	public function followupupdate(Request $request)
	{ 
		$requestData = $request->all();
		$followupId = $this->decodeString(@$requestData['lead_id']);
		$followup = Followup::find($followupId);
		if (!$followup) {
			echo json_encode(array('success' => false, 'message' => 'Followup not found', 'leadid' => $requestData['lead_id']));
			return;
		}
		$followup->note = @$requestData['description'];
		$followup->subject = @$requestData['remindersubject'];
		$followup->followup_type = @$requestData['note_type'];
		$followup->rem_cat = @$requestData['rem_cat'];
		if(isset($requestData['followup_date']) && $requestData['followup_date'] != ''){
			$followup->followup_date = @$requestData['followup_date'].' '.date('H:i', strtotime($requestData['followup_time']));
		}
		$saved = $followup->save();  
		if(!$saved) 
		{
			echo json_encode(array('success' => false, 'message' => 'Please try again', 'leadid' => $requestData['lead_id']));
		}
		else
		{ 
			// Resolve admin by followup.lead_id or followup.client_id
			$admin = null;
			if ($followup->lead_id) {
				$admin = Admin::where('lead_id', $followup->lead_id)->where('type', 'lead')->first();
			}
			if (!$admin && $followup->client_id) {
				$admin = Admin::where('id', $followup->client_id)->where('type', 'lead')->first();
			}
			if ($admin) {
				$note_type = $this->followuptype($requestData['note_type'], 'id');
				Admin::where('id', $admin->id)->update(['status' => $note_type]);
			}
			$leadidEncoded = $followup->lead_id
				? base64_encode(convert_uuencode($followup->lead_id))
				: base64_encode(convert_uuencode($followup->client_id));
			echo json_encode(array('success' => true, 'message' => 'successfully saved', 'leadid' => $leadidEncoded));
		}
	}
	
		public static function followuptype($type, $field) { 
			$FollowupType = FollowupType::where('type','=',	$type)->first();
			
			return @$FollowupType->$field;
		}
		public static function time_Ago($time) { 
			$diff     = time() - $time; 
			// Time difference in seconds 
			$sec     = $diff; 
			// Convert time difference in minutes 
			$min     = round($diff / 60 ); 
			// Convert time difference in hours 
			$hrs     = round($diff / 3600); 
		  
		// Convert time difference in days 
			$days     = round($diff / 86400 ); 
		  
		// Convert time difference in weeks 
			$weeks     = round($diff / 604800); 
		  
		// Convert time difference in months 
		$mnths     = round($diff / 2600640 ); 
		  
		// Convert time difference in years 
		$yrs     = round($diff / 31207680 ); 
		  
		// Check for seconds 
		if($sec <= 60) { 
			echo "$sec seconds ago"; 
		} 
		  
		// Check for minutes 
		else if($min <= 60) { 
			if($min==1) { 
				echo "one minute ago"; 
			} 
			else { 
				echo "$min minutes ago"; 
			} 
		} 
		  
		// Check for hours 
		else if($hrs <= 24) { 
			if($hrs == 1) {  
				echo " an hour ago"; 
			} 
			else { 
				echo "$hrs hours ago"; 
			} 
		}else{
			echo date("d M, Y", $time);
		} 
	} 
}
?>