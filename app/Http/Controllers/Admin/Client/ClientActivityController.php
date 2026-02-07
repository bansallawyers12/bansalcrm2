<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Services\Sms\UnifiedSmsManager;

/**
 * Client activity log operations
 *
 * Methods moved from ClientsController:
 * - activities (TODO - still in ClientsController)
 * - deleteactivitylog
 * - pinactivitylog
 * - notpickedcall
 */
class ClientActivityController extends Controller
{
    protected $smsManager;

    public function __construct(UnifiedSmsManager $smsManager)
    {
        $this->middleware('auth:admin');
        $this->smsManager = $smsManager;
    }

    /**
     * Not picked call button click - send SMS via UnifiedSmsManager
     */
    public function notpickedcall(Request $request){
        $data = $request->all();
        $userInfo = Admin::select('id','country_code','phone')->where('id', $data['id'])->first();
        $smsSent = false;
        if ($userInfo && !empty($data['message'])) {
            $userPhone = trim(($userInfo->country_code ?? '') . '' . ($userInfo->phone ?? ''));
            if ($userPhone) {
                $result = $this->smsManager->sendSms($userPhone, $data['message'], 'notification', ['client_id' => $data['id']]);
                $smsSent = !empty($result['success']);
            }
        }
        $recExist = Admin::where('id', $data['id'])->update(['not_picked_call' => $data['not_picked_call']]);
        if ($recExist) {
            if ($data['not_picked_call'] == 1) {
                $response['status'] = true;
                $response['message'] = $smsSent ? 'Call not picked. SMS sent successfully!' : 'Call not picked. SMS failed to send.';
                $response['not_picked_call'] = $data['not_picked_call'];
            } else {
                $response['status'] = true;
                $response['message'] = 'You have updated call not picked bit. Please try again';
                $response['not_picked_call'] = $data['not_picked_call'];
            }
        } else {
            $response['status'] = false;
            $response['message'] = 'Please try again';
            $response['not_picked_call'] = $data['not_picked_call'];
        }
        echo json_encode($response);
    }

    /**
     * Delete activity log
     */
    public function deleteactivitylog(Request $request){
		$activitylogid = $request->activitylogid;
		if(ActivitiesLog::where('id',$activitylogid)->exists()){
			$data = ActivitiesLog::select('client_id','subject','description')->where('id',$activitylogid)->first();
			$res = DB::table('activities_logs')->where('id', @$activitylogid)->delete();
			if($res){
				
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

    /**
     * Pin activity log
     */
    public function pinactivitylog(Request $request){
		$requestData = $request->all();
        if(ActivitiesLog::where('id',$requestData['activity_id'])->exists()){
			$activity = ActivitiesLog::where('id',$requestData['activity_id'])->first();
			if($activity->pin == 0){
				$obj = ActivitiesLog::find($activity->id);
				$obj->pin = 1;
				$saved = $obj->save();
			}else{
				$obj = ActivitiesLog::find($activity->id);
				$obj->pin = 0;
				$saved = $obj->save();
			}
			$response['status'] 	= 	true;
			$response['message']	=	'Pin Option added successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Record not found';
		}
		echo json_encode($response);
	}

    /**
     * Get activity log for a client
     */
    public function activities(Request $request){
		if(Admin::where('role', '=', '7')->where('id', $request->id)->exists()){
			$activities = ActivitiesLog::where('client_id', $request->id)->orderby('created_at', 'DESC')->get();
			$data = array();
			foreach($activities as $activit){
				$admin = Admin::where('id', $activit->created_by)->first();

				$data[] = array(
                    'activity_id' => $activit->id,
					'subject' => $activit->subject,
					'createdname' => substr($admin->first_name, 0, 1),
					'name' => $admin->first_name,
					'message' => $activit->description,
					'date' => date('d M Y, H:i A', strtotime($activit->created_at)),
                   'followup_date' => $activit->followup_date,
                   'task_group' => $activit->task_group,
                   'pin' => $activit->pin
				);
			}

			$response['status'] 	= 	true;
			$response['data']	=	$data;
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

}
