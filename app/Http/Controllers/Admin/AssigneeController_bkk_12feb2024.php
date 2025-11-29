<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Contracts\Database\Eloquent\Builder;

use App\Appointment;
use App\Note;
use App\AppointmentLog;
use App\Notification;
use Carbon\Carbon;
use App\Admin;
use App\ActivitiesLog;
use Auth;
use Illuminate\Support\Facades\DB;
use DataTables;

class AssigneeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct()
     {
         $this->middleware('auth:admin');
     }

    //All task lists except completed = Closed
    public function index(Request $request)
    {
        if(\Auth::user()->role == 1){
            $assignees = \App\Note::sortable()
            ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status','<>','1')->orderBy('created_at', 'desc')->latest()->paginate(20);//where('status','not like','Closed')
        }else{
            $assignees = \App\Note::sortable()
            ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])->where('assigned_to',\Auth::user()->id)->where('type','client')->where('folloup',1)->where('status','<>','1')->orderBy('created_at', 'desc')->latest()->paginate(20);
        } //dd($assignees);
        return view('Admin.assignee.index',compact('assignees'))
         ->with('i', (request()->input('page', 1) - 1) * 20);
    }

    //All completed task lists
    public function completed(Request $request)
    {
        if(\Auth::user()->role == 1){
            $assignees = \App\Note::sortable()
            ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status','1')->orderBy('created_at', 'desc')->latest()->paginate(20); //where('status','like','Closed')
        }else{
            $assignees = \App\Note::sortable()
            ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])->where('assigned_to',\Auth::user()->id)->where('type','client')->where('folloup',1)->where('status','1')->orderBy('created_at', 'desc')->latest()->paginate(20);
        }  //dd( $assignees);
        return view('Admin.assignee.completed',compact('assignees'))
         ->with('i', (request()->input('page', 1) - 1) * 20);
    }

    //Update task to be complete
    public function updatetaskcompleted(Request $request,Note $note)
    {
        $data = $request->all(); //dd($data['id']);
        $note = Note::where('id',$data['id'])->update(['status'=>'1']);
        //$note = 1;
        if($note){
            $note_data = Note::where('id',$data['id'])->first(); //dd($note_data);
            if($note_data){
                $admin_data = Admin::where('id',$note_data['assigned_to'])->first(); //dd($admin_data);
                if($admin_data){
                    $assignee_name = $admin_data['first_name']." ".$admin_data['last_name'];
                } else {
                    $assignee_name = 'N/A';
                }
                $objs = new ActivitiesLog;
                $objs->client_id = $note_data['client_id'];
                $objs->created_by = Auth::user()->id;
                $objs->subject = 'assigned task for '.@$assignee_name;
                $objs->description = '<p>'.@$note_data['description'].'</p>';
                if(Auth::user()->id != @$note_data['assigned_to']){
                    $objs->use_for = @$note_data['assigned_to'];
                } else {
                    $objs->use_for = "";
                }

                $objs->followup_date = @$note_data['updated_at'];
                $objs->task_group = @$note_data['task_group'];
                $objs->task_status = 1; //maked completed
                $objs->save();
            }



            $response['status'] 	= 	true;
            $response['message']	=	'Task updated successfully';
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
        }
        echo json_encode($response);
    }

    //Update task to be not complete
    public function updatetasknotcompleted(Request $request,Note $note)
    {
        $data = $request->all(); //dd($data['id']);
        $note = Note::where('id',$data['id'])->update(['status'=>'0']);
        if($note){
            $response['status'] 	= 	true;
            $response['message']	=	'Task updated successfully';
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
        }
        echo json_encode($response);
    }


    //All assigned by me task list which r incomplete
    public function assigned_by_me(Request $request)
    {  //dd(Auth::user()->id);
         if(\Auth::user()->role == 1){
             $assignees_notCompleted = \App\Note::sortable()
             ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])
             ->where('status','<>','1')
             ->where('type','client')
             ->whereNotNull('client_id')
             ->where('folloup',1)
             ->where('is_delete', '0')
             ->orderBy('created_at', 'desc')
             ->latest()
             ->paginate(20);
         } else {
             $assignees_notCompleted = \App\Note::sortable()
             ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])
             ->where('status','<>','1')
             ->where('user_id',\Auth::user()->id)
             ->where('type','client')
             ->where('folloup',1)
             ->where('is_delete', '0')
             ->orderBy('created_at', 'desc')
             ->latest()->paginate(20);
         }
         #dd($assignees_notCompleted);
         return view('Admin.assignee.assign_by_me',compact('assignees_notCompleted'))
          ->with('i', (request()->input('page', 1) - 1) * 20);
    }


    //All assigned to me task list
    public function assigned_to_me(Request $request)
    {
        if(\Auth::user()->role == 1){
            $assignees_notCompleted = \App\Note::sortable()
            ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])->where('status','<>','1')->where('assigned_to',\Auth::user()->id)->where('type','client')->whereNotNull('client_id')->where('folloup',1)->orderBy('created_at', 'desc')->latest()->paginate(20);//where('status','not like','Closed')

            $assignees_completed = \App\Note::sortable()
            ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])->where('status','1')->where('assigned_to',\Auth::user()->id)->where('type','client')->whereNotNull('client_id')->where('folloup',1)->orderBy('created_at', 'desc')->latest()->paginate(20);
        }else{
            $assignees_notCompleted = \App\Note::sortable()
            ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])->where('status','<>','1')->where('assigned_to',\Auth::user()->id)->where('type','client')->where('folloup',1)->orderBy('created_at', 'desc')->latest()->paginate(20);

            $assignees_completed = \App\Note::sortable()
            ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])->where('status','1')->where('assigned_to',\Auth::user()->id)->where('type','client')->where('folloup',1)->orderBy('created_at', 'desc')->latest()->paginate(20);
        }
        //dd($assignees_notCompleted);
        //dd($assignees_completed);
        return view('Admin.assignee.assign_to_me',compact('assignees_notCompleted','assignees_completed'))
         ->with('i', (request()->input('page', 1) - 1) * 20);
    }



   //All incomplete activities list
    /*public function activities(Request $request)
    {   //dd($request->all());
        $req_data = $request->all();
        if( isset($req_data['group_type'])  && $req_data['group_type'] != ""){
            $task_group = $req_data['group_type'];
        } else {
            $task_group = 'All';
        }

        if( isset($req_data['search_by'])  && $req_data['search_by'] != ""){
            $search_by = $req_data['search_by'];
        } else {
            $search_by = "";
        }
        //dd($task_group.'==='.$search_by); dd(Auth::user()->id);
        if($task_group == 'All')
        {  //if no task group is present

            if(\Auth::user()->role == 1)
            { //admin role
                if($search_by) { //if search string is present
                    $assignees_notCompleted = \App\Note::sortable()
                    ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])
                    ->where('status','<>','1')
                    ->where('type','client')
                    ->where('folloup',1)
                    ->where('is_delete', '0')
                    ->where(function($subQuery) use ($search_by)
                    {   
                        $subQuery->whereHas('noteUser', function ( $query ) use ($search_by) {
                            $query->where('first_name', 'LIKE', '%'.$search_by.'%');
                            $query->orWhere('last_name', 'LIKE', '%'.$search_by.'%');
                        })
                        ->orWhereHas('noteClient', function ( $query ) use ($search_by)  {
                            $query->where('first_name', 'LIKE', '%'.$search_by.'%');
                            $query->orWhere('last_name', 'LIKE', '%'.$search_by.'%');
                        })
                        ->orWhereHas('noteClient', function ( $query ) use ($search_by)  {
                            $query->where('phone', 'LIKE', '%'.$search_by.'%');
                        })
                        ->orWhereHas('noteClient', function ( $query ) use ($search_by)  {
                             $query->where('client_id', 'LIKE', '%'.$search_by.'%');
                        });
                    })->orderBy('created_at', 'desc')
                    ->latest()
                    ->paginate(20);
                } else { //if no searching
                    $assignees_notCompleted = \App\Note::sortable()
                    ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])
                    ->where('status','<>','1')
                    ->where('type','client')
                    ->whereNotNull('client_id')
                    ->where('folloup',1)
                    ->where('is_delete', '0')
                    ->orderBy('created_at', 'desc')
                    ->latest()->paginate(20);
                }
                //dd($assignees_notCompleted);
            }
            else
            { //role is not admin
                if($search_by) { //if search string is present
                    //dd('ifff'.Auth::user()->id);
                    $assignees_notCompleted = \App\Note::sortable()
                    ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])
                    ->where('status','<>','1')
                    ->where('assigned_to',\Auth::user()->id)
                    ->where('type','client')
                    ->where('folloup',1)
                    ->where('is_delete', '0')
                    ->where(function($subQuery) use ($search_by)
                    {   
                        $subQuery->whereHas('noteUser', function ( $query ) use ($search_by) {
                            $query->where('first_name', 'LIKE', '%'.$search_by.'%');
                            $query->orWhere('last_name', 'LIKE', '%'.$search_by.'%');
                        })
                        ->orWhereHas('noteClient', function ( $query ) use ($search_by)  {
                            $query->where('first_name', 'LIKE', '%'.$search_by.'%');
                            $query->orWhere('last_name', 'LIKE', '%'.$search_by.'%');
                        })
                        ->orWhereHas('noteClient', function ( $query ) use ($search_by)  {
                            $query->where('phone', 'LIKE', '%'.$search_by.'%');
                        })
                        ->orWhereHas('noteClient', function ( $query ) use ($search_by)  {
                             $query->where('client_id', 'LIKE', '%'.$search_by.'%');
                        });
                    })->orderBy('created_at', 'desc')
                    ->latest()
                    ->paginate(20);
                } else { //if no searching
                //dd('elsee');
                    $assignees_notCompleted = \App\Note::sortable()
                    ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])
                    ->where('status','<>','1')
                    ->where('assigned_to',\Auth::user()->id)
                    ->where('type','client')
                    ->where('folloup',1)
                    ->where('is_delete', '0')
                    ->orderBy('created_at', 'desc')
                    ->latest()
                    ->paginate(20);
                }
                //dd($assignees_notCompleted);
            }
        }
        else
        { //if search by task group is present
            if(\Auth::user()->role == 1)
            {  //admin role
                if($search_by) { //if search string is present
                    $assignees_notCompleted = \App\Note::sortable()
                    ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])
                    ->where('task_group','like',$task_group)
                    ->where('status','<>','1')
                    ->where('type','client')
                    ->whereNotNull('client_id')
                    ->where('folloup',1)
                    ->where('is_delete', '0')
                    ->where(function($subQuery) use ($search_by)
                    {   
                        $subQuery->whereHas('noteUser', function ( $query ) use ($search_by) {
                            $query->where('first_name', 'LIKE', '%'.$search_by.'%');
                            $query->orWhere('last_name', 'LIKE', '%'.$search_by.'%');
                        })
                        ->orWhereHas('noteClient', function ( $query ) use ($search_by)  {
                            $query->where('first_name', 'LIKE', '%'.$search_by.'%');
                            $query->orWhere('last_name', 'LIKE', '%'.$search_by.'%');
                        })
                        ->orWhereHas('noteClient', function ( $query ) use ($search_by)  {
                            $query->where('phone', 'LIKE', '%'.$search_by.'%');
                        })
                        ->orWhereHas('noteClient', function ( $query ) use ($search_by)  {
                             $query->where('client_id', 'LIKE', '%'.$search_by.'%');
                        });
                    })->orderBy('created_at', 'desc')
                    ->latest()
                    ->paginate(20);
                } else { //if no searching
                    $assignees_notCompleted = \App\Note::sortable()
                    ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])
                    ->where('task_group','like',$task_group)
                    ->where('status','<>','1')
                    ->where('type','client')
                    ->whereNotNull('client_id')
                    ->where('folloup',1)
                    ->where('is_delete', '0')
                    ->orderBy('created_at', 'desc')
                    ->latest()
                    ->paginate(20);
                }
                //dd($assignees_notCompleted);
            }
            else
            { //role is not admin
                if($search_by) { //if search string is present
                    $assignees_notCompleted = \App\Note::sortable()
                    ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])
                    ->where('task_group','like',$task_group)
                    ->where('status','<>','1')
                    ->where('assigned_to',\Auth::user()->id)
                    ->where('type','client')
                    ->where('folloup',1)
                    ->where('is_delete', '0')
                   ->where(function($subQuery) use ($search_by)
                    {   
                        $subQuery->whereHas('noteUser', function ( $query ) use ($search_by) {
                            $query->where('first_name', 'LIKE', '%'.$search_by.'%');
                            $query->orWhere('last_name', 'LIKE', '%'.$search_by.'%');
                        })
                        ->orWhereHas('noteClient', function ( $query ) use ($search_by)  {
                            $query->where('first_name', 'LIKE', '%'.$search_by.'%');
                            $query->orWhere('last_name', 'LIKE', '%'.$search_by.'%');
                        })
                        ->orWhereHas('noteClient', function ( $query ) use ($search_by)  {
                            $query->where('phone', 'LIKE', '%'.$search_by.'%');
                        })
                        ->orWhereHas('noteClient', function ( $query ) use ($search_by)  {
                             $query->where('client_id', 'LIKE', '%'.$search_by.'%');
                        });
                    })->orderBy('created_at', 'desc')
                    ->latest()
                    ->paginate(20);
                } else { //if no searching
                    $assignees_notCompleted = \App\Note::sortable()
                    ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])
                    ->where('task_group','like',$task_group)
                    ->where('status','<>','1')
                    ->where('assigned_to',\Auth::user()->id)
                    ->where('type','client')
                    ->where('folloup',1)
                    ->where('is_delete', '0')
                    ->orderBy('created_at', 'desc')
                    ->latest()
                    ->paginate(20);
                }
                //dd($assignees_notCompleted);
            }
        }
        //dd($assignees_notCompleted);
        return view('Admin.assignee.activities',compact('assignees_notCompleted','task_group'))
         ->with('i', (request()->input('page', 1) - 1) * 20);
    }*/

    //All completed activities list
    public function activities_completed(Request $request)
    {   //dd($request->all());
        $req_data = $request->all();
        if( isset($req_data['group_type'])  && $req_data['group_type'] != ""){
            $task_group = $req_data['group_type'];
        } else {
            $task_group = 'All';
        }
        //dd($task_group);
        if($task_group == 'All') {
            if(\Auth::user()->role == 1){
                $assignees_completed = \App\Note::sortable()
                ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])
                ->where('status','1')
                ->where('type','client')
                ->whereNotNull('client_id')
                ->where('folloup',1)
                ->where('is_delete', '0')
                ->orderBy('created_at', 'desc')
                ->latest()->paginate(20);
            } else {
                $assignees_completed = \App\Note::sortable()
                ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])
                ->where('status','1')
                ->where('assigned_to',\Auth::user()->id)
                ->where('type','client')
                ->where('folloup',1)
                ->where('is_delete', '0')
                ->orderBy('created_at', 'desc')
                ->latest()->paginate(20);
            }
        } else {
            if(\Auth::user()->role == 1){
                $assignees_completed = \App\Note::sortable()
                ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])
                ->where('task_group','like',$task_group)
                ->where('status','1')
                ->where('type','client')
                ->whereNotNull('client_id')
                ->where('folloup',1)
                ->where('is_delete', '0')
                ->orderBy('created_at', 'desc')
                ->latest()->paginate(20);
            } else {
                $assignees_completed = \App\Note::sortable()
                ->with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])
                ->where('task_group','like',$task_group)
                ->where('status','1')
                ->where('assigned_to',\Auth::user()->id)
                ->where('type','client')
                ->where('folloup',1)
                ->where('is_delete', '0')
                ->orderBy('created_at', 'desc')
                ->latest()->paginate(20);
            }
        }
        #dd($assignees_completed);
        return view('Admin.assignee.activities_completed',compact('assignees_completed','task_group'))
         ->with('i', (request()->input('page', 1) - 1) * 20);
    }
    
    
    
    public function activities() {
        return view('Admin.assignee.activities');
    }

    public function getActivities(Request $request) {
        if ($request->ajax()) {
           if(\Auth::user()->role == 1)
            { //admin role
            	$data = \App\Note::with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])
            	->where('status','<>','1')
            	->where('type','client')
            	->where('folloup',1)
            	->where('is_delete', '0')
            	->orderBy('created_at', 'desc')
            	->latest()->get();
            }
            else
            { //role is not admin
            	$data = \App\Note::with(['noteUser','noteClient','lead.natureOfEnquiry','lead.service','assigned_user'])
            	->where('status','<>','1')
            	->where('assigned_to',\Auth::user()->id)
            	->where('type','client')
            	->where('folloup',1)
            	->where('is_delete', '0')
            	->orderBy('created_at', 'desc')
            	->latest()->get();
            }
            //dd($data);
            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('done_task', function($data) {
                $done_task = '<input type="radio" class="complete_task" data-toggle="tooltip" title="Mark Complete!" data-id="'.$data->id.'">';
                return $done_task;
            })
            ->addColumn('assigner_name', function($data) {
                if($data->noteUser){
                    $full_name = $data->noteUser->first_name.' '.$data->noteUser->last_name;
                } else {
                    $full_name = 'N/P';
                }
                return $full_name;
            })
            ->addColumn('client_reference', function($data) {
                if($data->noteClient){
                    $user_name = $data->noteClient->first_name.' '.$data->noteClient->last_name;
                    $user_name .= "<br>";
                    $user_name .= "\n";

                    $client_encoded_id = base64_encode(convert_uuencode(@$data->client_id)) ;
                    $user_name .= '<a href="'.url('/admin/clients/detail/'.$client_encoded_id).'" target="_blank" >'.$data->noteClient->client_id.'</a>';
                } else {
                    $user_name = 'N/P';
                }
                return $user_name;
            })

            ->addColumn('assign_date', function($data) {
                if($data->followup_date){
                    $assign_date =  date('d/m/Y',strtotime($data->followup_date)) ;
                } else {
                    $assign_date = 'N/P';
                }
                return $assign_date;
            })
            ->addColumn('task_group', function($data) {
                if($data->task_group){
                    $task_group =   $data->task_group ;
                } else {
                    $task_group = 'N/P';
                }
                return $task_group;
            })
            ->addColumn('note_description', function($data) {
                if( isset($data->description) && $data->description != "" ){
                    if (strlen($data->description) > 190) {
                        $full_description = $data->description;
                        $final_desc = substr($data->description, 0, 190);
                        $final_desc .= '<button type="button" class="btn btn-link btn_readmore" data-toggle="popover" title="" data-content="'.$full_description.'">Read more</button>';
                    } else {
                        $final_desc =  $data->description;
                    }
                } else {
                    $final_desc =  "N/P";
                }  //echo "\n";
                return $final_desc;
            })

            ->addColumn('action', function($list){

                if($list->task_group != 'Personal Task')
                {
                    if($list->followup_date != ""){
                        $current_date1 = $list->followup_date;
                    } else{
                        $current_date1 = date('Y-m-d');
                    }

                    $content1 =
                    '<div id=&quot;popover-content&quot;>
                        <h4 class=&quot;text-center&quot;>Update Task</h4>
                        <div class=&quot;clearfix&quot;></div>

                        <div class=&quot;box-header with-border&quot;>
                            <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
                                <label for=&quot;inputSub3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Select Assignee</label>
                                <div class=&quot;col-sm-9&quot;>
                                    <select class=&quot;assigneeselect2 form-control selec_reg&quot; id=&quot;rem_cat&quot; name=&quot;rem_cat&quot; onchange=&quot;&quot;>
                                        <option value=&quot;&quot;>Select</option>';
                                        


                    $content1 .= '</select></div>
                                <div class=&quot;clearfix&quot;></div>
                            </div>
                        </div>

                        <div class=&quot;box-header with-border&quot;>
                            <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
                                <label for=&quot;inputEmail3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Note</label>
                                <div class=&quot;col-sm-9&quot;>
                                    <textarea id=&quot;assignnote&quot; class=&quot;form-control summernote-simple f13&quot; placeholder=&quot;Enter an note....&quot; type=&quot;text&quot;></textarea>
                                </div>
                                <div class=&quot;clearfix&quot;></div>
                            </div>
                        </div>

                        <div class=&quot;box-header with-border&quot;>
                            <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
                                <label for=&quot;inputEmail3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>DateTime</label>
                                <div class=&quot;col-sm-9&quot;>
                                    <input type=&quot;date&quot; class=&quot;form-control f13&quot; placeholder=&quot;yyyy-mm-dd&quot; id=&quot;popoverdatetime&quot; value=&quot;'.$current_date1.'&quot; name=&quot;popoverdate&quot;>
                                </div>
                                <div class=&quot;clearfix&quot;></div>
                            </div>
                        </div>

                        <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
                            <label for=&quot;inputSub3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Group</label>
                            <div class=&quot;col-sm-9&quot;>
                                <select class=&quot;assigneeselect2 form-control selec_reg&quot; id=&quot;task_group&quot; name=&quot;task_group&quot;>
                                    <option value=&quot;&quot;>Select</option>
                                    <option value=&quot;Call&quot;>Call</option>
                                    <option value=&quot;Checklist&quot;>Checklist</option>
                                    <option value=&quot;Review&quot;>Review</option>
                                    <option value=&quot;Query&quot;>Query</option>
                                    <option value=&quot;Urgent&quot;>Urgent</option>
                                </select>
                            </div>
                            <div class=&quot;clearfix&quot;></div>
                        </div>

                        <input id=&quot;assign_note_id&quot;  type=&quot;hidden&quot; value=&quot;&quot;>

                        <input id=&quot;assign_client_id&quot;  type=&quot;hidden&quot; value=&quot;'.base64_encode(convert_uuencode(@$list->client_id)).'&quot;>

                        <div class=&quot;box-footer&quot; style=&quot;padding:10px 0&quot;>
                            <div class=&quot;row&quot;>
                                <input type=&quot;hidden&quot; value=&quot;&quot; id=&quot;popoverrealdate&quot; name=&quot;popoverrealdate&quot; />
                            </div>
                            <div class=&quot;row text-center&quot;>
                                <div class=&quot;col-md-12 text-center&quot;>
                                <button  class=&quot;btn btn-info&quot; id=&quot;updateTask&quot;>Update Task</button>
                                </div>
                            </div>
                        </div>
                    </div>';

                    $actionBtn = '<button type="button"  data-assignedto="'.$list->assigned_to.'" data-noteid="'.$list->description.'" data-taskid="'.$list->id.'" data-taskgroupid="'.$list->task_group.'"  data-followupdate="'.$list->followup_date.'"  class="btn btn-primary btn-block update_task" data-toggle="popover" data-role="popover" title=""  data-placement="left"   data-content="'.$content1.'" style="width: 40px;display: inline;"><i class="fa fa-edit" aria-hidden="true"></i></button>';
                } else {
                    $actionBtn = '';
                }

                $actionBtn .= ' <button class="btn btn-danger deleteNote" data-remote="/admin/destroy_activity/'. $list->id.'"><i class="fa fa-trash" aria-hidden="true"></i></button>';



                if($list->task_group != 'Personal Task')
                {
                    $content2 =
                    '<div id=&quot;popover-content&quot;>
                        <h4 class=&quot;text-center&quot;>Re-Assign User</h4>
                        <div class=&quot;clearfix&quot;></div>
                        <div class=&quot;box-header with-border&quot;>
                            <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
                                <label for=&quot;inputSub3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Select Assignee</label>
                                <div class=&quot;col-sm-9&quot;>
                                    <select class=&quot;assigneeselect2 form-control selec_reg&quot; id=&quot;rem_cat&quot; name=&quot;rem_cat&quot; onchange=&quot;&quot;>
                                        <option value=&quot;&quot; >Select</option>';
                            $content2 .= '</select>
                                </div>
                                <div class=&quot;clearfix&quot;></div>
                            </div>
                        </div>


                        <div class=&quot;box-header with-border&quot;>
                            <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
                                <label for=&quot;inputEmail3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Note</label>
                                <div class=&quot;col-sm-9&quot;>
                                    <textarea id=&quot;assignnote&quot; class=&quot;form-control summernote-simple f13&quot; placeholder=&quot;Enter an note....&quot; type=&quot;text&quot;></textarea>
                                </div>
                                <div class=&quot;clearfix&quot;></div>
                            </div>
                        </div>

                        <div class=&quot;box-header with-border&quot;>
                            <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
                                <label for=&quot;inputEmail3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>DateTime</label>
                                <div class=&quot;col-sm-9&quot;>
                                    <input type=&quot;date&quot; class=&quot;form-control f13&quot; placeholder=&quot;yyyy-mm-dd&quot; id=&quot;popoverdatetime&quot; value=&quot;'.$current_date1.'&quot;name=&quot;popoverdate&quot;>
                                </div>
                                <div class=&quot;clearfix&quot;></div>
                            </div>
                        </div>

                        <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
                            <label for=&quot;inputSub3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Group</label>
                            <div class=&quot;col-sm-9&quot;>
                                <select class=&quot;assigneeselect2 form-control selec_reg&quot; id=&quot;task_group&quot; name=&quot;task_group&quot;>
                                    <option value=&quot;&quot;>Select</option>
                                    <option value=&quot;Call&quot;>Call</option>
                                    <option value=&quot;Checklist&quot;>Checklist</option>
                                    <option value=&quot;Review&quot;>Review</option>
                                    <option value=&quot;Query&quot;>Query</option>
                                    <option value=&quot;Urgent&quot;>Urgent</option>
                                </select>
                            </div>
                            <div class=&quot;clearfix&quot;></div>
                        </div>

                        <input id=&quot;assign_note_id&quot;  type=&quot;hidden&quot; value=&quot;&quot;>

                        <input id=&quot;assign_client_id&quot;  type=&quot;hidden&quot; value=&quot;'.base64_encode(convert_uuencode(@$list->client_id)).'&quot;>

                        <div class=&quot;box-footer&quot; style=&quot;padding:10px 0&quot;>
                            <div class=&quot;row&quot;>
                                <input type=&quot;hidden&quot; value=&quot;&quot; id=&quot;popoverrealdate&quot; name=&quot;popoverrealdate&quot; />
                            </div>
                            <div class=&quot;row text-center&quot;>
                                <div class=&quot;col-md-12 text-center&quot;>
                                <button  class=&quot;btn btn-info&quot; id=&quot;assignUser&quot;>Assign User</button>
                                </div>
                            </div>
                        </div>
                    </div>';

                    $actionBtn .= ' <button type="button" data-assignedto="'.$list->assigned_to.'" data-noteid="'.$list->description.'" data-taskid="'.$list->id.'" data-taskgroupid="'.$list->task_group.'"  data-followupdate="'.$list->followup_date.'" data-toggle="popover" title="" class="btn btn-primary btn-block reassign_task" data-container="body" data-role="popover" data-placement="auto" data-html="true" data-content="'.$content2.'" data-original-title="" title="" style="width: 40px;display: inline;"><i class="fa fa-tasks" aria-hidden="true"></i></button>';
                }
                return $actionBtn;
            })
            ->rawColumns(['done_task','client_reference','note_description','action'])
            ->make(true);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('appointment.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'detail' => 'required',
        ]);

        Product::create($request->all());
        return redirect()->route('appointment.index')
                        ->with('success','Product created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function show(Appointment $appointment)
    {
        $appointment=Appointment::with(['user','clients','service','natureOfEnquiry'])->where('id',$appointment->id)->first();
        return view('Admin.appointments.show',compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Appointment $appointment)
    {
        $appointment=Appointment::with(['user','clients','service','natureOfEnquiry'])->where('id',$appointment->id)->first();
        return view('Admin.appointments.edit',compact('appointment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            // 'user_id' => 'required|exists:admins,id',
            'client_id' => 'required|exists:admins,id',
            'date' => 'required',
            'time' => 'required',
            'title' => 'required',
            'description' => 'required',
            'invites' => 'required',
            'status' => 'required',
        ]);

        $data=$request->all();
        $data['time']= Carbon::parse($request->time)->format('H:i:s');
        $appointment->update($data);

        return redirect()->route('appointments.index')
                        ->with('success','Appointment updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id,Note $Note)
    {   // dd($id);
        $appointment =Note::find($id);
        $appointment->folloup = 0;
        $appointment->save();

        return redirect()->route('assignee.index')
        ->with('success','Assingee deleted successfully');
    }

    public function destroy_by_me( $id,Note $Note)
    {
        $appointment =Note::find($id);
        $appointment->folloup = 0;
        if( $appointment->save() ){
            $objs = new ActivitiesLog;
            $objs->client_id = $appointment->client_id;
            $objs->created_by = Auth::user()->id;

            $assign_user = Admin::find($appointment->assigned_to);
            if($assign_user){
                $assign_full_name = $assign_user->first_name." ".$assign_user->last_name;
                $objs->subject = 'deleted activity for '.@$assign_full_name;
            } else {
                $objs->subject = 'deleted activity ';
            }

            $objs->description = '<p>'.$appointment->description.'</p>';
            if(Auth::user()->id != @$appointment->assigned_to){
                $objs->use_for = @$appointment->assigned_to;
            } else {
                $objs->use_for = "";
            }
            $objs->followup_date = @$appointment->followup_datetime;
            $objs->task_group = @$appointment->task_group;
            $objs->save();
            //echo json_encode(array('success' => true, 'message' => 'Activity deleted successfully', 'clientID' => $appointment->client_id));
            //exit;
            return redirect()->route('assignee.assigned_by_me')->with('success','Activity deleted successfully');
        }
    }


    public function destroy_to_me( $id,Note $Note)
    {
        $appointment =Note::find($id);
        $appointment->folloup = 0;
        $appointment->save();

        return redirect()->route('assignee.assigned_to_me')
        ->with('success','Assingee deleted successfully');
    }


   //incomplete activity remove
    public function destroy_activity( $id,Note $Note)
    {
        $appointment = Note::find($id);//dd($appointment);
        $appointment->folloup = 0;
        if( $appointment->save() ){
            $objs = new ActivitiesLog;
            $objs->client_id = $appointment->client_id;
            $objs->created_by = Auth::user()->id;

            $assign_user = Admin::find($appointment->assigned_to);
            if($assign_user){
                $assign_full_name = $assign_user->first_name." ".$assign_user->last_name;
                $objs->subject = 'deleted activity for '.@$assign_full_name;
            } else {
                $objs->subject = 'deleted activity ';
            }

            $objs->description = '<p>'.$appointment->description.'</p>';
            if(Auth::user()->id != @$appointment->assigned_to){
                $objs->use_for = @$appointment->assigned_to;
            } else {
                $objs->use_for = "";
            }
            $objs->followup_date = @$appointment->followup_datetime;
            $objs->task_group = @$appointment->task_group;
            $objs->save();
            //echo json_encode(array('success' => true, 'message' => 'Activity deleted successfully', 'clientID' => $appointment->client_id));
            //exit;
            return redirect()->route('assignee.activities')->with('success','Activity deleted successfully');
        }
    }

    //complete activity remove
    public function destroy_complete_activity( $id,Note $Note)
    {
        $appointment = Note::find($id);
        $appointment->folloup = 0;
        if( $appointment->save() ){
            $objs = new ActivitiesLog;
            $objs->client_id = $appointment->client_id;
            $objs->created_by = Auth::user()->id;

            $assign_user = Admin::find($appointment->assigned_to);
            if($assign_user){
                $assign_full_name = $assign_user->first_name." ".$assign_user->last_name;
                $objs->subject = 'deleted completed activity for '.@$assign_full_name;
            } else {
                $objs->subject = 'deleted completed activity ';
            }

            $objs->description = '<p>'.$appointment->description.'</p>';
            if(Auth::user()->id != @$appointment->assigned_to){
                $objs->use_for = @$appointment->assigned_to;
            } else {
                $objs->use_for = "";
            }
            $objs->followup_date = @$appointment->followup_datetime;
            $objs->task_group = @$appointment->task_group;
            $objs->save();
            //echo json_encode(array('success' => true, 'message' => 'Activity deleted successfully', 'clientID' => $appointment->client_id));
            //exit;
            return redirect()->route('assignee.activities')->with('success','Activity deleted successfully');
        }
        //return redirect()->route('assignee.activities_completed')->with('success','Activity deleted successfully');
    }


    public function assignedetail(Request $request){
        $appointmentdetail = Appointment::with(['user','clients','service','assignee_user','natureOfEnquiry'])->where('id',$request->id)->first();
        // dd($appointmentdetail->assignee_user->id);
    // $admin = \App\Admin::where('id', $notedetail->assignee)->first();
    // $noe = \App\NatureOfEnquiry::where('id', @$appointmentdetail->noeid)->first();
    // $addedby = \App\Admin::where('id', $appointmentdetail->user_id)->first();
    // $client = \App\Admin::where('id', $appointmentdetail->client_id)->first();
    // ?>
    <div class="modal-header">
            <h5 class="modal-title" id="taskModalLabel"><i class="fa fa-bag"></i> <?php echo $appointmentdetail->title ?? $appointmentdetail->service->title; ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-12 col-md-6 col-lg-6">
                <div class="form-group">
                    <label for="title">Status:</label>
                    <?php

                    if($appointmentdetail->status == 0){
                        $status = '<span style="color: rgb(255, 173, 0);" class="">Pending</span>';
                    }else if($appointmentdetail->status == 2){
                        $status = '<span style="color: rgb(255, 173, 0); " class="">Completed</span>';
                    }else if($appointmentdetail->status == 3){
                        $status = '<span style="color: rgb(156, 156, 156);" class="">Rejected</span>';
                    }else if($appointmentdetail->status == 1){
                        $status = '<span style="color: rgb(113, 204, 83);" class="">Approved</span>';
                    }else{
                        $status = '<span style="color: rgb(113, 204, 83);" class="">N/P</span>';
                    }
                    ?>


                    <ul class="navbar-nav navbar-right">
                        <li class="dropdown dropdown-list-toggle">
                            <a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle updatedstatus"><?php echo $status ?? 'Pending'; ?> <i class="fa fa-angle-down"></i></a>
                            <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
                                <a data-status="0" data-id="<?php echo $appointmentdetail->id; ?>" data-status-name="Pending" href="javascript:;" class="dropdown-item changestatus">
                                    Pending
                                </a>
                                <a data-status="2" data-status-name="Completed" data-id="<?php echo $appointmentdetail->id; ?>" href="javascript:;" class="dropdown-item changestatus">
                                    Completed
                                </a>
                                <a data-status="3" data-status-name="Rejected" data-id="<?php echo $appointmentdetail->id; ?>" href="javascript:;" class="dropdown-item changestatus">
                                    Rejected
                                </a>
                                <a data-status="1" data-status-name="Approved" data-id="<?php echo $appointmentdetail->id; ?>" href="javascript:;" class="dropdown-item changestatus">
                                    Approved
                                </a>
                                <a data-status="4" data-status-name="N/P" data-id="<?php echo $appointmentdetail->id; ?>" href="javascript:;" class="dropdown-item changestatus">
                                     N/P
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6">
                <div class="form-group">
                    <label for="title">Priority:</label>
                    <ul class="navbar-nav navbar-right">
                        <li class="dropdown dropdown-list-toggle">
                            <a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle updatedpriority"><?php echo $appointmentdetail->priority ?? 'Low'; ?><i class="fa fa-angle-down"></i></a>
                             <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
                                <a data-status="Low" data-id="<?php echo $appointmentdetail->id; ?>" href="javascript:;" class="dropdown-item changepriority">
                                    Low
                                </a>
                                <a data-status="Normal" data-id="<?php echo $appointmentdetail->id; ?>" href="javascript:;" class="dropdown-item changepriority">
                                    Normal
                                </a>
                                <a data-status="High" data-id="<?php echo $appointmentdetail->id; ?>" href="javascript:;" class="dropdown-item changepriority">
                                    High
                                </a>
                                <a data-status="Urgent" data-id="<?php echo $appointmentdetail->id; ?>" href="javascript:;" class="dropdown-item changepriority">
                                    Urgent
                                </a>
                             </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6">
                <div class="form-group">
                    <label for="title">Assignee: <a class="openassignee"  href="javascript:;">Change</a></label>
                    <br>
                    <?php if($appointmentdetail){ ?>
                        <div style="display: flex;">
                            <span class="author-avtar" style="margin-left: unset;margin-right: unset;font-size: .8rem;height: 24px;line-height: 24px;width: 24px;min-width: 24px;background: rgb(3, 169, 244);"><?php echo substr($appointmentdetail->user->first_name, 0, 1); ?></span>
                            <span style="margin-left:5px;"><?php echo $appointmentdetail->assignee_user->first_name ?? ''; ?></span>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6">
                <div class="form-group">
                    <label for="title">Added By:</label>
                    <br>
                    <?php if($appointmentdetail){ ?>
                        <div style="display: flex;">
                            <span class="author-avtar" style="margin-left: unset;margin-right: unset;font-size: .8rem;height: 24px;line-height: 24px;width: 24px;min-width: 24px;background: rgb(3, 169, 244);"><?php echo substr($appointmentdetail->user->first_name, 0, 1); ?></span>
                            <span style="margin-left:5px;"><?php echo $appointmentdetail->user->first_name; ?></span>
                        </div>
                    <?php } ?>
                </div>
            </div>
                <div class="assignee" style="display:none;">
                <div class="row">
                    <div class="col-md-8">
                        <select class="form-control select2" id="changeassignee" name="changeassignee">
                            <?php
                                foreach(\App\Admin::where('role','!=',7)->orderby('first_name','ASC')->get() as $admin){
                                    $branchname = \App\Branch::where('id',$admin->office_id)->first();
                            ?>
                                    <option value="<?php echo $admin->id; ?>"><?php echo $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')'; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <a class="saveassignee btn btn-success" data-id="<?php echo $appointmentdetail->id; ?>" href="javascript:;">Save</a>
                    </div>
                    <div class="col-md-2">
                        <a class="closeassignee" href="javascript:;"><i class="fa fa-times"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-12">
                <div class="form-group">
                    <label for="title">Description:</label>
                    <br>
                    <?php if($appointmentdetail->description != ''){ echo '<span class="desc_click">'.$appointmentdetail->description.'</span>'; }else{ ?><textarea data-id="<?php echo $appointmentdetail->id; ?>" class="form-control tasknewdesc"  placeholder="Enter Description"><?php echo $appointmentdetail->description; ?></textarea><?php } ?>
                    <textarea data-id="<?php echo $appointmentdetail->id; ?>" class="form-control taskdesc" style="display:none;"  placeholder="Enter Description"><?php echo $appointmentdetail->description; ?></textarea>
                </div>
                <p><strong>Note:</strong> <span class="badge badge-warning">Please,click on the above description text to enable the input field.</span></p>
            </div>
            <div class="col-12 col-md-12 col-lg-12">
                <div class="form-group">
                    <label for="title">Comments:</label>
                    <textarea class="form-control taskcomment" name="comment" placeholder="Enter comment here"></textarea>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-12">
                <div class="form-group">
                    <button data-id="<?php echo $appointmentdetail->id; ?>" class="btn btn-primary savecomment" >Save</button>
                </div>
            </div>

            <div class="col-md-12">
                    <h4>Application Logs</h4>
                    <div class="logsdata">

  <?php
                    $logslist = AppointmentLog::where('appointment_id',$appointmentdetail->id)->orderby('created_at', 'DESC')->get();
                    foreach($logslist as $llist){
                       $admin = \App\Admin::where('id', $llist->created_by)->first();
                    ?>
                        <div class="logsitem">
                            <div class="row">
                                <div class="col-md-7">
                                    <span class="ag-avatar"><?php echo substr($admin->first_name, 0, 1); ?></span>
                                    <span class="text_info"><span><?php echo $admin->first_name; ?></span><?php echo $llist->title; ?></span>
                                </div>
                                <div class="col-md-5">
                                    <span class="logs_date"><?php echo date('d M Y h:i A', strtotime($llist->created_at)); ?></span>
                                </div>
                                <?php if($llist->message != ''){ ?>
                                <div class="col-md-12 logs_comment">
                                    <p><?php echo $llist->message; ?></p>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                </div>
        </div>
    </div>
    <?php
}

public function update_appointment_status(Request $request){

    $objs = Appointment::find($request->id);

    if($objs->status == 0){
        $status = 'Pending';
    }else if($objs->status == 2){
        $status = 'Completed';
    }else if($objs->status == 3){
        $status = 'Rejected';
    }else if($objs->status == 1){
        $status = 'Approved';
    }else if($objs->status == 4){
        $status = 'N/P';
    }
    $objs->status = $request->status;
    $saved = $objs->save();
    if($saved){
        $objs = new AppointmentLog;
        $objs->title = 'changed status from '.$status.' to '.$request->statusname;
        $objs->created_by = \Auth::user()->id;
        $objs->appointment_id = $request->id;

        $saved = $objs->save();
        $alist = Appointment::find($request->id);
        $status = '';
        if($alist->status == 1 ){
                $status = '<span style="color: rgb(113, 204, 83); width: 84px;">Approved</span>';
            }else if($alist->status == 0){
                $status = '<span style="color: rgb(255, 173, 0); width: 84px;">Pending</span>';
            }else if($alist->status == 2){
                $status = '<span style="color: rgb(255, 173, 0); width: 84px;">Completed</span>';
            }else if($alist->status == 3){
                $status = '<span style="color: rgb(156, 156, 156); width: 84px;">Rejected</span>';
            }else if($alist->status == 4){
                $status = '<span style="color: rgb(156, 156, 156); width: 84px;">N/P</span>';
            }else {
                $status = '<span style="color: rgb(255, 173, 0); width: 84px;">N/P</span>';
            }
        $response['status'] 	= 	true;
        $response['viewstatus'] 	= 	$status;
        $response['message']	=	'saved successfully';
    }else{
        $response['status'] 	= 	false;
        $response['message']	=	'Please try again';
    }
    echo json_encode($response);
}

public function update_appointment_priority(Request $request){
    $objs = Appointment::findOrFail($request->id);
    $status = $objs->priority;
    if($request->status == 'Low'){
        $objs->priority_no = 1;
    }else if($request->status == 'Normal'){
        $objs->priority_no = 2;
    }if($request->status == 'High'){
        $objs->priority_no = 3;
    }if($request->status == 'Urgent'){
        $objs->priority_no = 4;
    }
    $objs->priority = $request->status;
    $saved = $objs->save();

    if($saved){
        $objs = new AppointmentLog;
        $objs->title = 'changed priority from '.$status.' to '.$request->status;
        $objs->created_by = \Auth::user()->id;
        $objs->appointment_id = $request->id;

        $saved = $objs->save();
        $response['status'] 	= 	true;
        $response['message']	=	'saved successfully';
    }else{
        $response['status'] 	= 	false;
        $response['message']	=	'Please try again';
    }
    echo json_encode($response);
}

public function change_assignee(Request $request){
    $objs = Appointment::find($request->id);

    $objs->assignee = $request->assinee;

    $saved = $objs->save();
    if($saved){
        $o = new \App\Notification;
        $o->sender_id = \Auth::user()->id;
        $o->receiver_id = $request->assinee;
        $o->module_id = $request->id;
        $o->url = \URL::to('/admin/appointments');
        $o->notification_type = 'appointment';
        $o->message = $objs->title.' Appointments Assigned by '.\Auth::user()->first_name.' '.\Auth::user()->last_name;
        $o->save();
        $response['status'] 	= 	true;
        $response['message']	=	'Updated successfully';
    }else{
        $response['status'] 	= 	false;
        $response['message']	=	'Please try again';
    }
    echo json_encode($response);
}

public function update_apppointment_comment(Request $request){
    $objs = new AppointmentLog;
    $objs->title = 'has commented';
    $objs->created_by = \Auth::user()->id;
    $objs->appointment_id = $request->id;
    $objs->message = $request->visit_comment;
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

public function update_apppointment_description(Request $request){
    $objs = Appointment::find($request->id);
    $objs->description = $request->visit_purpose;
    $saved = $objs->save();
    if($saved){
        $objs = new AppointmentLog;
        $objs->title = 'changed description';
        $objs->created_by = \Auth::user()->id;
        $objs->appointment_id = $request->id;
        $objs->message = $request->visit_purpose;
        $saved = $objs->save();
        $response['status'] 	= 	true;
        $response['message']	=	'saved successfully';
    }else{
        $response['status'] 	= 	false;
        $response['message']	=	'Please try again';
    }
    echo json_encode($response);
}

    //Get All assignee list dropdown
    public function get_assignee_list(Request $request){
        $assignedto = $request->assignedto;
        
        $content1 = array();
        foreach(\App\Admin::where('role','!=',7)->where('status',1)->orderby('first_name','ASC')->get() as $admin)
        {
            $branchname = \App\Branch::where('id',$admin->office_id)->first();
            $option_value =  $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')';

            if($admin->id == $assignedto){
                $content1[] = '<option value="'.$admin->id.'" selected>'.$option_value.'</option>';
            } else {
                $content1[] = '<option value="'.$admin->id.'">'.$option_value.'</option>';
            }
        }
        //if($saved){
            $response['status'] 	= 	true;
            $response['message']	=	$content1;
        /*}else{
            $response['status'] 	= 	false;
            $response['message']	=	array();
        }*/
        echo json_encode($response);
    }

}


