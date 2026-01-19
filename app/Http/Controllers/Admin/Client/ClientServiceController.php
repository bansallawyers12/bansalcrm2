<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Models\Tag;

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
 */
class ClientServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
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
