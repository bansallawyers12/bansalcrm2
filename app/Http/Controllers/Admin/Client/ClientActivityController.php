<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Services\SmsService;

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
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->middleware('auth:admin');
        $this->smsService = $smsService;
    }

    /**
     * Not picked call button click
     */
    public function notpickedcall(Request $request){
        $data = $request->all();
        //Get user Phone no and send message via cellcast
        $userInfo = Admin::select('id','country_code','phone')->where('id', $data['id'])->first();
        if ( $userInfo) {
            $message = $data['message'];
            $userPhone = $userInfo->country_code."".$userInfo->phone;
            $this->smsService->sendSms($userPhone,$message);
        }
        $recExist = Admin::where('id', $data['id'])->update(['not_picked_call' => $data['not_picked_call']]);
        if($recExist){
            if($data['not_picked_call'] == 1){ //if checked true
                $objs = new ActivitiesLog;
                $objs->client_id = $data['id'];
                $objs->created_by = Auth::user()->id;
                $objs->description = '<span class="text-semi-bold">Call not picked.SMS sent successfully!</span>';
                $objs->task_status = 0;
                $objs->pin = 0;
                $objs->save();

                $response['status'] 	= 	true;
                $response['message']	=	'Call not picked.SMS sent successfully!';
                $response['not_picked_call'] 	= 	$data['not_picked_call'];
            }
            else if($data['not_picked_call'] == 0){
                $response['status'] 	= 	true;
                $response['message']	=	'You have updated call not picked bit. Please try again';
                $response['not_picked_call'] 	= 	$data['not_picked_call'];
            }
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
            $response['not_picked_call'] 	= 	$data['not_picked_call'];
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

    // TODO: Move 'activities' method here from ClientsController
}
