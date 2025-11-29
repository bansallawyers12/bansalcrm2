@extends('layouts.admin')
@section('title', 'Completed Activities')

@section('content')
<style>
.fc-event-container .fc-h-event{cursor:pointer;}
#openassigneview .modal-body ul.navbar-nav li .dropdown-menu{transform: none!important; top:40px!important;}
.sort_col a { color: #212529 !important; font-weight: 700 !important;}
.group_type_section a.active {color:black;}
</style>
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="custom-error-msg">
			</div>
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
                            <h4>Completed Activities</h4>

                            <div class="card-header-action">
							</div>

                            <ul class="nav nav-pills" id="client_tabs" role="tablist">
                                <li class="nav-item is_checked_clientn11">
									<a class="nav-link active" id="archived-tab"  href="{{URL::to('/admin/activities')}}">Incomplete</a>
								</li>
                            </ul>
						</div>
						<div class="card-body">
							<div class="tab-content" id="quotationContent">
                                <form action="{{ route('assignee.activities_completed') }}" method="get">
                                    <div class="row">
                                        <div class="col-md-9 group_type_section">
                                            <?php //echo $task_group;?>
                                            <a href="{{URL::to('/admin/activities_completed?group_type=All')}}" id="All" class="group_type <?php if($task_group == 'All') { echo 'active';}?>">All</a> | &nbsp;

                                            <button type="button" class="btn btn-light">
                                                <a href="{{URL::to('/admin/activities_completed?group_type=Call')}}" id="Call" class="group_type <?php if($task_group == 'Call') { echo 'active';}?>"> <i class="fa fa-phone" aria-hidden="true"></i> Call</a> &nbsp;
                                            </button>

                                            <button type="button" class="btn btn-light">
                                                <a href="{{URL::to('/admin/activities_completed?group_type=Checklist')}}" id="Checklist" class="group_type <?php if($task_group == 'Checklist') { echo 'active';}?>"><i class="fa fa-bars" aria-hidden="true"></i> Checklist</a> &nbsp;
                                            </button>

                                            <button type="button" class="btn btn-light">
                                                <a href="{{URL::to('/admin/activities_completed?group_type=Review')}}" id="Review" class="group_type <?php if($task_group == 'Review') { echo 'active';}?>"> <i class="fa fa-check" aria-hidden="true"></i> Review</a> &nbsp;
                                            </button>

                                            <button type="button" class="btn btn-light">
                                                <a href="{{URL::to('/admin/activities_completed?group_type=Query')}}" id="Query" class="group_type <?php if($task_group == 'Query') { echo 'active';}?>"><i class="fa fa-question" aria-hidden="true"></i> Query</a> &nbsp;
                                            </button>

                                            <button type="button" class="btn btn-light">
                                                <a href="{{URL::to('/admin/activities_completed?group_type=Urgent')}}" id="Urgent" class="group_type <?php if($task_group == 'Urgent') { echo 'active';}?>"> <i class="fa fa-flag" aria-hidden="true"></i> Urgent</a> &nbsp;
                                            </button>

                                            <button type="button" class="btn btn-light">
                                                <a href="{{URL::to('/admin/activities?group_type=Personal Task')}}" id="Personal Task" class="group_type <?php if($task_group == 'Personal Task') { echo 'active';}?>"> <i class="fa fa-tasks" aria-hidden="true"></i> Personal Task</a> &nbsp;
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <div class="tab-pane fade show active" id="active_quotation" role="tabpanel" aria-labelledby="active_quotation-tab">
									<div class="table-responsive common_table">
									    <!-- @if ($message = Session::get('success'))
										<div class="alert alert-success">
											<p>{{ $message }}</p>
										</div>
									    @endif   -->

                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="20px" style="text-align: center;">Sno</th>
                                                <th width="25px" style="text-align: center;">Done</th>
                                                <th width="120px">Assigner Name</th>
                                                <th width="140px">Client Reference</th>
                                                <th width="120px" class="sort_col">@sortablelink('followup_date','Assign Date')</th>
                                                <th width="100px" class="sort_col">@sortablelink('task_group','Type')</th>
                                                <th>Note</th>
                                                <th width="140px">Action</th>
                                            </tr>
                                            <?php
                                            if(count($assignees_completed)>0){
                                            ?>
                                            @foreach ($assignees_completed as $list)
                                            <?php //echo "<pre>list==";print_r($list);
                                                $admin = \App\Admin::where('id', $list->user_id)->first();//dd($admin);
                                                if($admin){
                                                    $first_name = $admin->first_name ?? 'N/A';
                                                    $last_name = $admin->last_name ?? 'N/A';
                                                    $full_name = $first_name.' '.$last_name;
                                                } else {
                                                    $full_name = 'N/A';
                                                }
                                            ?>
                                            <tr>
                                                <?php
                                                if($list->noteClient){
                                                    $user_name=$list->noteClient->first_name.' '.$list->noteClient->last_name;
                                                }else{
                                                    $user_name='N/P';
                                                }
                                                ?>
                                                <td style="text-align: center;">{{ ++$i }}</td>
                                                <td style="text-align: center;"><input type="radio" class="not_complete_task" data-toggle="tooltip" title="Mark Incomplete!" data-id="{{ $list->id }}"></td>
                                                <td>{{ $full_name??'N/P' }}</td>
                                                <td>
                                                    {{ $user_name }}
                                                    <br>
                                                    <?php
                                                    if($list->noteClient)
                                                    { ?>
                                                        <a href="{{URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$list->client_id)))}}" target="_blank" >{{ $list->noteClient->client_id }}</a>
                                                    <?php
                                                    } ?>
                                                </td>
                                                <td>{{ date('d/m/Y',strtotime($list->followup_date)) ?? 'N/P'}} </td>
                                                <td>{{ $list->task_group??'N/P' }}</td>
                                                <td>
                                                    <?php
                                                    if( isset($list->description) && $list->description != "" ){
                                                        if (strlen($list->description) > 190) {
                                                            $full_description = $list->description;
                                                            $new_string = substr($list->description, 0, 190) . ' <button type="button" class="btn btn-link" data-toggle="popover" title="" data-content="'.$full_description.'">Read more</button>';
                                                            echo $new_string;
                                                        } else {
                                                            echo $list->description;
                                                        }
                                                    } else {
                                                        echo "N/P";
                                                    }  echo "\n";?>
                                                </td>

                                                <td>
                                                    {{-- @if($list->noteClient) --}}
                                                    <form action="{{ route('assignee.destroy_complete_activity',$list->id) }}" method="POST">

                                                        {{-- <a class="btn btn-info" href="{{ route('assignees.show',$list->id) }}">Show</a> --}}

                                                         <!--<a class="btn btn-primary" data-toggle="tooltip" title="" href="{{ url('/admin/clients/edit/'.base64_encode(convert_uuencode(@$list->client_id)).'') }}"> <i class="fa fa-edit" aria-hidden="true"></i> </a>-->

                                                         <?php if($list->task_group != 'Personal Task'){?>
                                                         <button type="button" data-noteid="{{ $list->description }}" data-taskid="{{ $list->id }}" data-taskgroupid={{ $list->task_group }}  data-followupdate={{ $list->followup_date  }} data-toggle="tooltip" title="" class="btn btn-primary btn-block update_task" data-container="body" data-role="popover" data-placement="bottom" data-html="true" data-content="<div id=&quot;popover-content&quot;>
                                                            <h4 class=&quot;text-center&quot;>Update Task</h4>
                                                            <div class=&quot;clearfix&quot;></div>
                                                        <div class=&quot;box-header with-border&quot;>
                                                            <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
                                                                <label for=&quot;inputSub3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Select Assignee</label>
                                                                <div class=&quot;col-sm-9&quot;>
                                                                    <select class=&quot;assigneeselect2 form-control selec_reg&quot; id=&quot;rem_cat&quot; name=&quot;rem_cat&quot; onchange=&quot;&quot;>
                                                                        <option value=&quot;&quot; >Select</option>
                                                                        {{--  @foreach(\App\Admin::where('role','!=',7)->orderby('first_name','ASC')->get() as $admin) --}}
                                                                        @foreach(\App\Admin::where('role','!=',7)->where('status',1)->orderby('first_name','ASC')->get() as $admin)
                                                                        <?php
                                                                        $branchname = \App\Branch::where('id',$admin->office_id)->first();
                                                                        ?>
                                                                        <option value=&quot;<?php echo $admin->id; ?>&quot; <?php if($admin->id == $list->assigned_to){ echo "selected";} ?>><?php echo $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')'; ?></option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class=&quot;clearfix&quot;></div>
                                                            </div>
                                                        </div><div id=&quot;popover-content&quot;>
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
                                                                    <input type=&quot;date&quot; class=&quot;form-control f13&quot; placeholder=&quot;yyyy-mm-dd&quot; id=&quot;popoverdatetime&quot; value=&quot;<?php echo date('Y-m-d');?>&quot;name=&quot;popoverdate&quot;>
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

                                                        <input id=&quot;assign_client_id&quot;  type=&quot;hidden&quot; value=&quot;{{base64_encode(convert_uuencode(@$list->client_id))}}&quot;>
                                                        <div class=&quot;box-footer&quot; style=&quot;padding:10px 0&quot;>
                                                        <div class=&quot;row&quot;>
                                                            <input type=&quot;hidden&quot; value=&quot;&quot; id=&quot;popoverrealdate&quot; name=&quot;popoverrealdate&quot; />
                                                        </div>
                                                        <div class=&quot;row text-center&quot;>
                                                            <div class=&quot;col-md-12 text-center&quot;>
                                                            <button  class=&quot;btn btn-info&quot; id=&quot;updateTask&quot;>Update Task</button>
                                                            </div>
                                                        </div>
                                                </div>" data-original-title="" title="" style="width: 40px;display: inline;"><i class="fa fa-edit" aria-hidden="true"></i></button>
                                                        <?php }?>

                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" data-toggle="tooltip" title="" onclick="return confirm('Are you sure want to delete?');"><i class="fa fa-trash" aria-hidden="true"></i></button>


                                                        <?php if($list->task_group != 'Personal Task'){?>
                                                        <button type="button" data-noteid="{{ $list->description }}" data-toggle="tooltip" title="" class="btn btn-primary btn-block reassign_task" data-container="body" data-role="popover" data-placement="bottom" data-html="true" data-content="<div id=&quot;popover-content&quot;>
                                                            <h4 class=&quot;text-center&quot;>Re-Assign User</h4>
                                                            <div class=&quot;clearfix&quot;></div>
                                                        <div class=&quot;box-header with-border&quot;>
                                                            <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
                                                                <label for=&quot;inputSub3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Select Assignee</label>
                                                                <div class=&quot;col-sm-9&quot;>
                                                                    <select class=&quot;assigneeselect2 form-control selec_reg&quot; id=&quot;rem_cat&quot; name=&quot;rem_cat&quot; onchange=&quot;&quot;>
                                                                        <option value=&quot;&quot; >Select</option>
                                                                        {{--  @foreach(\App\Admin::where('role','!=',7)->orderby('first_name','ASC')->get() as $admin) --}}
                                                                        @foreach(\App\Admin::where('role','!=',7)->where('status',1)->orderby('first_name','ASC')->get() as $admin)
                                                                        <?php
                                                                        $branchname = \App\Branch::where('id',$admin->office_id)->first();
                                                                        ?>
                                                                        <option value=&quot;<?php echo $admin->id; ?>&quot;><?php echo $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')'; ?></option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class=&quot;clearfix&quot;></div>
                                                            </div>
                                                        </div><div id=&quot;popover-content&quot;>
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
                                                                    <input type=&quot;date&quot; class=&quot;form-control f13&quot; placeholder=&quot;yyyy-mm-dd,h:i:s&quot; id=&quot;popoverdatetime&quot; value=&quot;<?php echo date('Y-m-d h:i:s');?>&quot;name=&quot;popoverdate&quot;>
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

                                                        <input id=&quot;assign_client_id&quot;  type=&quot;hidden&quot; value=&quot;{{base64_encode(convert_uuencode(@$list->client_id))}}&quot;>
                                                        <div class=&quot;box-footer&quot; style=&quot;padding:10px 0&quot;>
                                                        <div class=&quot;row&quot;>
                                                            <input type=&quot;hidden&quot; value=&quot;&quot; id=&quot;popoverrealdate&quot; name=&quot;popoverrealdate&quot; />
                                                        </div>
                                                        <div class=&quot;row text-center&quot;>
                                                            <div class=&quot;col-md-12 text-center&quot;>
                                                            <button  class=&quot;btn btn-info&quot; id=&quot;assignUser&quot;>Assign User</button>
                                                            </div>
                                                        </div>
                                                </div>" data-original-title="" title="" style="width: 40px;display: inline;"><i class="fa fa-tasks" aria-hidden="true"></i></button>
                                                {{-- <a class="btn btn-primary openassigneview" id="{{$list->id}}" href="#">Reassign</a> --}}
                                                <?php } ?>
                                                    </form>
                                                    {{-- @endif --}}
                                                </td>
                                            </tr>
										    @endforeach
                                            <?php
                                            } else {
                                            ?>
                                            <tr>
                                                <td colspan="8"><b>There is no completed activity exist.</b></td>
                                            </tr>
                                            <?php
                                            }
                                            ?>
									    </table>
										{{-- {!! $assignees->appends(\Request::except('page'))->render() !!} --}}
   										{!! $assignees_completed->appends($_GET)->links() !!}
								    </div>
								    <div class="card-footer">

								    </div>
							    </div>

                        </div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<!-- Assign Modal -->

<div class="modal fade custom_modal" id="openassigneview" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content taskview">

		</div>
	</div>
</div>
@endsection
@section('scripts')
<script src="{{URL::to('/')}}/public/js/popover.js"></script>
<script>
jQuery(document).ready(function($){
    $('[data-toggle="tooltip"]').tooltip();

    $(document).delegate('.openassignee', 'click', function(){
        $('.assignee').show();
    });

	$(document).delegate('.closeassignee', 'click', function(){
        $('.assignee').hide();
    });

    //reassign task
    $(document).delegate('.reassign_task', 'click', function(){
        var note_id = $(this).attr('data-noteid'); //alert(note_id);
        $('#assignnote').val(note_id);

        var task_id = $(this).attr('data-taskid'); //alert(task_id);
        $('#assign_note_id').val(task_id);
    });

    //update task
    $(document).delegate('.update_task', 'click', function(){
        var note_id = $(this).attr('data-noteid'); //alert(note_id);
        $('#assignnote').val(note_id);

        var task_id = $(this).attr('data-taskid'); //alert(task_id);
        $('#assign_note_id').val(task_id);

        var taskgroup_id = $(this).attr('data-taskgroupid'); //alert(taskgroup_id);
        $('#task_group').val(taskgroup_id);

        var followupdate_id = $(this).attr('data-followupdate'); //alert(followupdate_id);
        $('#popoverdatetime').val(followupdate_id);
    });



    //Function is used for not complete the task
	$(document).delegate('.not_complete_task', 'click', function(){
		var row_id = $(this).attr('data-id');
        if(row_id !=""){
            $.ajax({
				type:'post',
                url:"{{URL::to('/')}}/admin/update-task-not-completed",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {id:row_id },
                success: function(response){
                    //console.log(response);
                    var obj = $.parseJSON(response);
                    location.reload();
                }
			});
        }
	});

    //Function is used for complete the task
	$(document).delegate('.complete_task', 'click', function(){
		var row_id = $(this).attr('data-id'); //alert(row_id);
        if(row_id !=""){ //&& confirm('Are you sure want to complete the task?')
            $.ajax({
				type:'post',
                url:"{{URL::to('/')}}/admin/update-task-completed",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {id:row_id },
                success: function(response){
                    //console.log(response);
                    var obj = $.parseJSON(response);
                    location.reload();
                }
			});
        }
	});


    $(document).delegate('#assignUser','click', function(){
		$(".popuploader").show();
		var flag = true;
		var error ="";
		$(".custom-error").remove();
		// if($('#lead_id').val() == ''){
		// 	$('.popuploader').hide();
		// 	error="Lead field is required.";
		// 	$('#lead_id').after("<span class='custom-error' role='alert'>"+error+"</span>");
		// 	flag = false;
		// }
		if($('#rem_cat').val() == ''){
			$('.popuploader').hide();
			error="Assignee field is required.";
			$('#rem_cat').after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
		if($('#assignnote').val() == ''){
			$('.popuploader').hide();
			error="Note field is required.";
			$('#assignnote').after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
        if($('#task_group').val() == ''){
			$('.popuploader').hide();
			error="Group field is required.";
			$('#task_group').after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
		if(flag){
			$.ajax({
				type:'post',
					url:"{{URL::to('/')}}/admin/clients/reassignfollowup/store",
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

					data: {note_type:'follow_up',description:$('#assignnote').val(),client_id:$('#assign_client_id').val(),followup_datetime:$('#popoverdatetime').val(),assignee_name:$('#rem_cat :selected').text(),rem_cat:$('#rem_cat option:selected').val(),task_group:$('#task_group option:selected').val()},
					success: function(response){
						console.log(response);
						$('.popuploader').hide();
						var obj = $.parseJSON(response);
						if(obj.success){
							$("[data-role=popover]").each(function(){
									(($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false  // fix for BS 3.3.6
							});
							location.reload();
							getallactivities();
							getallnotes();

						}else{
							alert(obj.message);
							location.reload();

						}
					}
			});
		}else{
			$("#loader").hide();
		}
	});

	$(document).delegate('.saveassignee', 'click', function(){
        var appliid = $(this).attr('data-id');

		var assinee= $('#changeassignee').val();
		$('.popuploader').show();
		// console.log($('#changeassignee').val());
		$.ajax({
			url: site_url+'/admin/change_assignee',
			type:'GET',
			data:{id: appliid,assinee: assinee},
			success: function(response){
				// console.log(response);
				 var obj = $.parseJSON(response);
				if(obj.status){
				    alert(obj.message);
				location.reload();

				}else{
					alert(obj.message);
				}
			}
		});
    });

	$(document).delegate('.savecomment', 'click', function(){
		var visitcomment = $('.taskcomment').val();
		var appliid = $(this).attr('data-id');
		$('.popuploader').show();
		$.ajax({
			url: site_url+'/admin/update_apppointment_comment',
			type:'POST',
			data:{"_token":$('meta[name="csrf-token"]').attr('content'),id: appliid,visit_comment:visitcomment},
			success: function(responses){
				// $('.popuploader').hide();
				$('.taskcomment').val('');
				$.ajax({
					url: site_url+'/admin/get-assigne-detail',
					type:'GET',
					data:{id:appliid},
					success: function(responses){
						$('.popuploader').hide();
						$('.taskview').html(responses);
					}
				});
			}
		});
	});
	$(document).delegate('.openassigneview', 'click', function(){
	// $('.popuploader').hide();
	$('#openassigneview').modal('show');
	var v = $(this).attr('id');
		$.ajax({
			url: site_url+'/admin/get-assigne-detail',
			type:'GET',
			data:{id:v},
			success: function(responses){
				$('.popuploader').hide();
				$('.taskview').html(responses);
			}
		});
	});

	$(document).delegate('.changestatus', 'click', function(){
		var appliid = $(this).attr('data-id');
		var status = $(this).attr('data-status');
		var statusame = $(this).attr('data-status-name');
		$('.popuploader').show();

		$.ajax({
			url: site_url+'/admin/update_list_status',
			type:'POST',
			data:{"_token":$('meta[name="csrf-token"]').attr('content'),id: appliid,statusname:statusame,status:status},
			success: function(responses){
				$('.popuploader').hide();
				var obj = JSON.parse(responses);
				if(obj.status){
				    console.log(obj.status);
				    $('.updatestatusview'+appliid).html(obj.viewstatus);
				}
				$.ajax({
					url: site_url+'/admin/get-assigne-detail',
					type:'GET',
					data:{id:appliid},
					success: function(responses){
						$('.popuploader').hide();
						$('.taskview').html(responses);
					}
				});
			}
		});
	});


	$(document).delegate('.changepriority', 'click', function(){
		var appliid = $(this).attr('data-id');
		var status = $(this).attr('data-status');
		$('.popuploader').show();

		$.ajax({
			url: site_url+'/admin/update_list_priority',
			type:'POST',
			data:{"_token":$('meta[name="csrf-token"]').attr('content'),id: appliid,status:status},
			success: function(responses){
				$('.popuploader').hide();

				$.ajax({
					url: site_url+'/admin/get-assigne-detail',
					type:'GET',
					data:{id:appliid},
					success: function(responses){
						$('.popuploader').hide();
						console.log(responses);
						$('.taskview').html(responses);

					}
				});
			}
		});
	});

	$(document).delegate('.desc_click', 'click', function(){
		$(this).hide();
		$('.taskdesc').show();
		$('.taskdesc').focus();
	});
	$(document).delegate('.taskdesc', 'blur', function(){
		$(this).hide();
		$('.desc_click').show();
	});

	$(document).delegate('.tasknewdesc', 'blur', function(){
		var visitpurpose = $(this).val();
		var appliid = $(this).attr('data-id');
		$('.popuploader').show();
		$.ajax({
			url: site_url+'/admin/update_apppointment_description',
			type:'POST',
			data:{"_token":$('meta[name="csrf-token"]').attr('content'),id: appliid,visit_purpose:visitpurpose},
			success: function(responses){
				$.ajax({
					url: site_url+'/admin/get-assigne-detail',
					type:'GET',
					data:{id:appliid},
					success: function(responses){
						$('.popuploader').hide();
						$('.taskview').html(responses);
					}
				});

			}
		});
	});

	$(document).delegate('.taskdesc', 'blur', function(){
		var visitpurpose = $(this).val();
		var appliid = $(this).attr('data-id');
		$('.popuploader').show();
		$.ajax({
			url: site_url+'/admin/update_apppointment_description',
			type:'POST',
			data:{"_token":$('meta[name="csrf-token"]').attr('content'),id: appliid,visit_purpose:visitpurpose},
			success: function(responses){
				 $.ajax({
					url: site_url+'/admin/get-assigne-detail',
					type:'GET',
					data:{id:appliid},
					success: function(responses){
						$('.popuploader').hide();
						$('.taskview').html(responses);
					}
				});

			}
		});
	});
});
</script>
@endsection
