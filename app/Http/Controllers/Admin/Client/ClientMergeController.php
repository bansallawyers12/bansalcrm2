<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Client record merging
 * 
 * Methods moved from ClientsController:
 * - merge_records
 */
class ClientMergeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Merge two client records into one
     */
    public function merge_records(Request $request){
        $response = array();
        if(
            ( isset($request->merge_from) && $request->merge_from != "" )
            && ( isset($request->merge_into) && $request->merge_into != "" )
        ){
            //Update merge_from to be deleted
            DB::table('admins')->where('id',$request->merge_from)->update( array('is_deleted'=>1) );

            //activities_logs
            $activitiesLogs = DB::table('activities_logs')->where('client_id', $request->merge_from)->get();
            if(!empty($activitiesLogs)){
                foreach($activitiesLogs as $actkey=>$actval){
                    DB::table('activities_logs')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }

            //notes
            $notes = DB::table('notes')->where('client_id', $request->merge_from)->get();
            if(!empty($notes)){
                foreach($notes as $notekey=>$noteval){
                    DB::table('notes')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }


            //applications
            $applications = DB::table('applications')->where('client_id', $request->merge_from)->get();
            if(!empty($applications)){
                foreach($applications as $appkey=>$appval){
                    DB::table('applications')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }


            //interested_services
            $interested_services = DB::table('interested_services')->where('client_id', $request->merge_from)->get();
            if(!empty($interested_services)){
                foreach($interested_services as $intkey=>$intval){
                    DB::table('interested_services')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }


            //education documents and migration documents
            $documents = DB::table('documents')->where('client_id', $request->merge_from)->get();
            if(!empty($documents)){
                foreach($documents as $dockey=>$docval){
                    DB::table('documents')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }

            //appointments - REMOVED: appointments table deleted
            // Appointment merge functionality disabled - appointments table no longer exists
            /*
            $appointments = DB::table('appointments')->where('client_id', $request->merge_from)->get();
            if(!empty($appointments)){
                foreach($appointments as $appkey=>$appval){
                    DB::table('appointments')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }
            */


            //quotations
            $quotations = DB::table('quotations')->where('client_id', $request->merge_from)->get();
            if(!empty($quotations)){
                foreach($quotations as $quotekey=>$quoteval){
                    DB::table('quotations')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }

            //accounts
            $accounts = DB::table('invoices')->where('client_id', $request->merge_from)->get();
            if(!empty($accounts)){
                foreach($accounts as $acckey=>$accval){
                    DB::table('invoices')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }

            //Conversations
            $conversations = DB::table('mail_reports')->where('client_id', $request->merge_from)->get();
            if(!empty($conversations)){
                foreach($conversations as $mailkey=>$mailval){
                    DB::table('mail_reports')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }

            //Tasks
            $tasks = DB::table('tasks')->where('client_id', $request->merge_from)->get();
            if(!empty($tasks)){
                foreach($tasks as $taskkey=>$taskval){
                    DB::table('tasks')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }

            //CheckinLogs
            $checkinLogs = DB::table('checkin_logs')->where('client_id', $request->merge_from)->get();
            if(!empty($checkinLogs)){
                foreach($checkinLogs as $checkkey=>$checkval){
                    DB::table('checkin_logs')
                    ->where('client_id', $request->merge_from)
                    ->update([
                        'client_id' => $request->merge_into,
                        'updated_at' => now()
                    ]);
                }
            }

        }
        $response['status'] 	= 	true;
        $response['message']	=	'You have successfully merged records from '.$request->merge_from.' to '.$request->merge_into.' .';
        echo json_encode($response);
    }
}
