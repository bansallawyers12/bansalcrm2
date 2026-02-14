@extends('layouts.admin')
@section('title', 'Assigned by me')

@section('content')
<style>
.fc-event-container .fc-h-event{cursor:pointer;}
#openassigneview .modal-body ul.navbar-nav li .dropdown-menu{transform: none!important; top:40px!important;}
.sort_col a { color: #212529 !important; font-weight: 700 !important;}
.group_type_section a.active {color:black;}
.select2-container{z-index:100000;width:315px !important;}
.countAction {background: #1f1655;padding: 0px 5px;border-radius: 50%;color: #fff;margin-left: 5px;}
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
							<h4>Assigned by me</h4>
							<div class="card-header-action">
							</div>

                            <ul class="nav nav-pills" id="client_tabs" role="tablist">
                                <li class="nav-item is_checked_clientn12">
									<a class="nav-link" href="{{URL::to('/action')}}">Incomplete</a>
								</li>

                                <li class="nav-item is_checked_clientn11">
									<a class="nav-link" id="archived-tab"  href="{{URL::to('/action/completed')}}">Completed</a>
								</li>
                            </ul>
						</div>
						<div class="card-body">
							<div class="tab-content" id="quotationContent">
                                <form action="{{ route('action.assigned_by_me') }}" method="get">
                                    <div class="row">
                                        <div class="col-md-12 group_type_section"><?php //echo $task_group;?>


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
                                                <th width="140px">Assignee Name</th>
                                                <th width="140px">Client Reference</th>
                                                <th width="120px" class="sort_col">@sortablelink('followup_date','Action Date')</th>
                                                <th width="100px" class="sort_col">@sortablelink('task_group','Type')</th>
                                                <th>Note</th>
                                                <th width="140px">Action</th>
                                            </tr>
                                            <?php
                                            if(count($assignees_notCompleted)>0){
                                            ?>
                                            @foreach ($assignees_notCompleted as $list)
                                            <?php //echo "<pre>list==";print_r($list);
                                                $admin = \App\Models\Admin::where('id', $list->assigned_to)->first();//dd($admin);
                                                if($admin){
                                                    $first_name = $admin->first_name ?? 'N/A';
                                                    $last_name = $admin->last_name ?? 'N/A';
                                                    $full_name = $first_name.' '.$last_name;
                                                } else {
                                                    $full_name = 'N/P';
                                                }
                                            ?>
                                            <tr>
                                                <?php
                                                if($list->noteClient){
                                                    $user_name=$list->noteClient->first_name.' '.$list->noteClient->last_name;
                                                }else{
                                                    $user_name='N/P';
                                                } ?>
                                                <td style="text-align: center;">{{ ++$i }}</td>
                                                <td style="text-align: center;"><input type="radio" class="complete_task" data-bs-toggle="tooltip" title="Mark Complete!" data-id="{{ $list->id }}"></td>
                                                <td>{{ $full_name??'N/P' }}</td>
                                                <td>
                                                    {{ $user_name }}
                                                    <br>
                                                    <?php
                                                    if($list->noteClient)
                                                    { ?>
                                                        <a href="{{URL::to('/clients/detail/'.base64_encode(convert_uuencode(@$list->client_id)))}}" target="_blank" >{{ $list->noteClient->client_id }}</a>
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
                                                            $new_string = substr($list->description, 0, 190) . ' <button type="button" class="btn btn-link" data-bs-toggle="popover" title="" data-content="'.$full_description.'">Read more</button>';
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
                                                    <form action="{{ route('action.destroy_by_me',$list->id) }}" method="POST">

                                                        {{-- <a class="btn btn-info" href="{{ route('assignees.show',$list->id) }}">Show</a> --}}

                                                        {{--<a class="btn btn-primary" href="{{ url('/clients/edit/'.base64_encode(convert_uuencode(@$list->client_id)).'') }}">Edit</a>--}}

                                                        <?php if($list->task_group != 'Personal Task'){?>
                                                            <button type="button" data-assignedto="{{ $list->assigned_to }}" data-noteid="{{ $list->description }}" data-taskid="{{ $list->id }}" data-taskgroupid="{{ $list->task_group }}" data-followupdate="{{ $list->followup_date }}" class="btn btn-primary btn-block update_task" data-container="body" data-role="popover" data-placement="bottom" data-html="true" data-content="<div id=&quot;popover-content&quot;>
                                                                <h4 class=&quot;text-center&quot;>Update Task</h4>
                                                                <div class=&quot;clearfix&quot;></div>
                                                            <div class=&quot;box-header with-border&quot;>
                                                                <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
                                                                    <label for=&quot;inputSub3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Select Assignee</label>
                                                                    <div class=&quot;col-sm-9&quot;>
                                                                        <select class=&quot;assigneeselect2 form-control selec_reg&quot; id=&quot;rem_cat&quot; name=&quot;rem_cat&quot; onchange=&quot;&quot;>
                                                                            <option value=&quot;&quot; >Select</option>
                                                                            {{--  @foreach(\App\Models\Admin::where('role','!=',7)->orderby('first_name','ASC')->get() as $admin) --}}
                                                                            @foreach(\App\Models\Admin::where('role','!=',7)->where('status',1)->orderby('first_name','ASC')->get() as $admin)
                                                                            <?php
                                                                            $branchname = \App\Models\Branch::where('id',$admin->office_id)->first();
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
                                                                        <textarea id=&quot;assignnote&quot; class=&quot;form-control tinymce-simple f13&quot; placeholder=&quot;Enter an note....&quot; type=&quot;text&quot;></textarea>
                                                                    </div>
                                                                    <div class=&quot;clearfix&quot;></div>
                                                                </div>
                                                            </div>
                                                            <div class=&quot;box-header with-border&quot;>
                                                                <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
                                                                    <label for=&quot;inputEmail3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>DateTime</label>
                                                                    <div class=&quot;col-sm-9&quot;>
                                                                        <input type=&quot;text&quot; class=&quot;form-control f13 flatpickr-date&quot; placeholder=&quot;yyyy-mm-dd&quot; id=&quot;popoverdatetime&quot; value=&quot;<?php echo date('Y-m-d');?>&quot; name=&quot;popoverdate&quot; autocomplete=&quot;off&quot;>
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
                                                    <?php } ?>

                                                        <?php if($list->task_group != 'Personal Task'){?>
                                                        <button type="button" data-assignedto="{{ $list->assigned_to }}" data-noteid="{{ $list->description }}" data-taskid="{{ $list->id }}" data-taskgroupid="{{ $list->task_group }}" data-followupdate="{{ $list->followup_date }}" class="btn btn-primary btn-block reassign_task" data-container="body" data-role="popover" data-placement="bottom" data-html="true" title="Reassign" data-content="<div id=&quot;popover-content&quot;>
                                                            <h4 class=&quot;text-center&quot;>Re-Assign User</h4>
                                                            <div class=&quot;clearfix&quot;></div>
                                                        <div class=&quot;box-header with-border&quot;>
                                                            <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
                                                                <label for=&quot;inputSub3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Select Assignee</label>
                                                                <div class=&quot;col-sm-9&quot;>
                                                                    <select class=&quot;assigneeselect2 form-control selec_reg&quot; id=&quot;rem_cat&quot; name=&quot;rem_cat&quot; onchange=&quot;&quot;>
                                                                        <option value=&quot;&quot; >Select</option>
                                                                        @foreach(\App\Models\Admin::where('role','!=',7)->where('status',1)->orderby('first_name','ASC')->get() as $admin)
                                                                        <?php
                                                                        $branchname = \App\Models\Branch::where('id',$admin->office_id)->first();
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
                                                                    <textarea id=&quot;assignnote&quot; class=&quot;form-control tinymce-simple f13&quot; placeholder=&quot;Enter an note....&quot; type=&quot;text&quot;></textarea>
                                                                </div>
                                                                <div class=&quot;clearfix&quot;></div>
                                                            </div>
                                                        </div>
                                                        <div class=&quot;box-header with-border&quot;>
                                                            <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
                                                                <label for=&quot;inputEmail3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>DateTime</label>
                                                                <div class=&quot;col-sm-9&quot;>
                                                                    <input type=&quot;text&quot; class=&quot;form-control f13 flatpickr-date&quot; placeholder=&quot;yyyy-mm-dd&quot; id=&quot;popoverdatetime&quot; value=&quot;<?php echo date('Y-m-d');?>&quot; name=&quot;popoverdate&quot; autocomplete=&quot;off&quot;>
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
                                                            <button  class=&quot;btn btn-info&quot; id=&quot;assignUser&quot;>Assign User</button>
                                                            </div>
                                                        </div>
                                                </div>" data-original-title="" title="" style="width: 40px;display: inline;"><i class="fa fa-tasks" aria-hidden="true"></i></button>
                                                        <?php } ?>

                                                        @csrf
                                                        @method('DELETE')

                                                        <!--<button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure want to delete?');"><i class="fa fa-trash" aria-hidden="true"></i></button>-->



                                                    </form>
                                                    {{-- @endif --}}
                                                </td>
                                            </tr>
										    @endforeach
                                            <?php
                                            } else {
                                            ?>
                                            <tr>
                                                <td colspan="8"><b>There is no activity assigned by me.</b></td>
                                            </tr>
                                            <?php
                                            }
                                            ?>
									    </table>
										{{-- {!! $assignees->appends(\Request::except('page'))->render() !!} --}}
   										{!! $assignees_notCompleted->appends($_GET)->links() !!}
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

<!-- Complete Action Modal -->
<div class="modal fade" id="completeActionModal" tabindex="-1" role="dialog" aria-labelledby="completeActionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completeActionModalLabel">Complete Action</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Client:</label>
                    <p id="complete-action-client"><strong><span></span></strong></p>
                </div>
                <div class="form-group">
                    <label for="completion_message">Completion Message: <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="completion_message" name="completion_message" rows="4" placeholder="Enter completion notes..." required></textarea>
                    <small class="form-text text-muted">Please describe what was done to complete this action.</small>
                </div>
                <input type="hidden" id="complete_action_id" name="complete_action_id" value="">
                <input type="hidden" id="complete_client_id" name="complete_client_id" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="submitCompleteAction">Complete Action</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="{{asset('js/popover.js')}}"></script>
<script>
	jQuery(document).ready(function($){
     $(document).delegate('.openassignee', 'click', function(){
        $('.assignee').show();
    });
	$(document).delegate('.closeassignee', 'click', function(){
        $('.assignee').hide();
    });


    //reassign task
    $(document).delegate('.reassign_task', 'click', function(e){
        e.preventDefault();
        e.stopPropagation();
        
        var $btn = $(this);
        var assignedto = $btn.attr('data-assignedto');
        var note_id = $btn.attr('data-noteid');
        var task_id = $btn.attr('data-taskid');
        var taskgroup_id = $btn.attr('data-taskgroupid');
        var followupdate_id = $btn.attr('data-followupdate');
        var folowDateArr = (followupdate_id || '').split(" ");
        var finalDate = folowDateArr[0] || '';
        
        // Popover is already initialized by popover.js on page load - do NOT re-initialize
        // (Re-initializing causes "Bootstrap doesn't allow more than one instance per element" error)
        $btn.popover('show');
        
        // Wait for popover to be shown, then set form values
        var popoverShown = false;
        var setFormValues = function() {
            if (popoverShown) return;
            popoverShown = true;
            
            // Find the visible popover element (Bootstrap 5 creates .popover elements)
            var $popover = $('.popover:visible').last();
            if ($popover.length) {
                // Load assignee list via AJAX and set in popover
                $.ajax({
                    type:'post',
                    url:"{{URL::to('/')}}/action/assignee-list",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {assignedto:assignedto},
                    success: function(response){
                        var obj = $.parseJSON(response);
                        $popover.find('#rem_cat').html(obj.message);
                    }
                });
                
                // Set form values within the visible popover
                $popover.find('#assignnote').val(note_id);
                $popover.find('#assign_note_id').val(task_id);
                $popover.find('#task_group').val(taskgroup_id);
                $popover.find('#popoverdatetime').val(finalDate);
            } else {
                // Fallback: set values globally (for compatibility)
                $('#assignnote').val(note_id);
                $('#assign_note_id').val(task_id);
                $('#task_group').val(taskgroup_id);
                $('#popoverdatetime').val(finalDate);
                
                // Load assignee list
                $.ajax({
                    type:'post',
                    url:"{{URL::to('/')}}/action/assignee-list",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {assignedto:assignedto},
                    success: function(response){
                        var obj = $.parseJSON(response);
                        $('#rem_cat').html(obj.message);
                    }
                });
            }
        };
        
        // Listen for Bootstrap 5 popover shown event
        $btn.one('shown.bs.popover', setFormValues);
        
        // Fallback timeout in case event doesn't fire
        setTimeout(setFormValues, 200);
    });

    //update task
    $(document).delegate('.update_task', 'click', function(e){
        e.preventDefault();
        e.stopPropagation();
        
        var $btn = $(this);
        var assignedto = $btn.attr('data-assignedto');
        var note_id = $btn.attr('data-noteid');
        var task_id = $btn.attr('data-taskid');
        var taskgroup_id = $btn.attr('data-taskgroupid');
        var followupdate_id = $btn.attr('data-followupdate');
        var folowDateArr = (followupdate_id || '').split(" ");
        var finalDate = folowDateArr[0] || '';
        
        // Popover is already initialized by popover.js on page load - do NOT re-initialize
        // (Re-initializing causes "Bootstrap doesn't allow more than one instance per element" error)
        $btn.popover('show');
        
        // Wait for popover to be shown, then set form values
        var popoverShown = false;
        var setFormValues = function() {
            if (popoverShown) return;
            popoverShown = true;
            
            // Find the visible popover element (Bootstrap 5 creates .popover elements)
            var $popover = $('.popover:visible').last();
            if ($popover.length) {
                // Set form values within the visible popover
                $popover.find('#rem_cat').each(function() {
                    var $select = $(this);
                    // Load assignee list via AJAX
                    $.ajax({
                        type:'post',
                        url:"{{URL::to('/')}}/action/assignee-list",
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: {assignedto:assignedto},
                        success: function(response){
                            var obj = $.parseJSON(response);
                            $select.html(obj.message);
                        }
                    });
                });
                
                $popover.find('#assignnote').val(note_id);
                $popover.find('#assign_note_id').val(task_id);
                $popover.find('#task_group').val(taskgroup_id);
                $popover.find('#popoverdatetime').val(finalDate);
            } else {
                // Fallback: set values globally (for compatibility)
                $('#rem_cat').html(''); // Will be set by AJAX
                $('#assignnote').val(note_id);
                $('#assign_note_id').val(task_id);
                $('#task_group').val(taskgroup_id);
                $('#popoverdatetime').val(finalDate);
                
                // Load assignee list
                $.ajax({
                    type:'post',
                    url:"{{URL::to('/')}}/action/assignee-list",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {assignedto:assignedto},
                    success: function(response){
                        var obj = $.parseJSON(response);
                        $('#rem_cat').html(obj.message);
                    }
                });
            }
        };
        
        // Listen for Bootstrap 5 popover shown event
        $btn.one('shown.bs.popover', setFormValues);
        
        // Fallback timeout in case event doesn't fire
        setTimeout(setFormValues, 200);
    });

    //Function is used for not complete the task
	$(document).delegate('.not_complete_task', 'click', function(){
		var row_id = $(this).attr('data-id');
        if(row_id !=""){
            $.ajax({
				type:'post',
                url:"{{URL::to('/')}}/action/task-incomplete",
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
	$(document).delegate('.complete_task', 'click', function(e){
		e.preventDefault();
		var row_id = $(this).attr('data-id');
        if(row_id !=""){
            // Get client name from the row
            var $row = $(this).closest('tr');
            var clientName = 'N/A';
            var clientId = '';
            
            // Extract client name from the Client Reference column (4th column)
            var $clientCell = $row.find('td:eq(3)');
            if ($clientCell.length) {
                var cellText = $clientCell.text().trim();
                var lines = cellText.split('\n');
                if (lines.length > 0) {
                    clientName = lines[0].trim() || 'N/A';
                }
            }
            
            // Get client_id from the note data if available
            $.ajax({
                type: 'GET',
                url: "{{URL::to('/')}}/action/get-note-data",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {id: row_id},
                success: function(noteData){
                    // Handle response structure
                    if(noteData && noteData.status && noteData.client_id){
                        clientId = noteData.client_id;
                        if(noteData.client_name){
                            clientName = noteData.client_name;
                        }
                    } else if(noteData && noteData.client_id){
                        // Fallback for different response structure
                        clientId = noteData.client_id;
                        if(noteData.client_name){
                            clientName = noteData.client_name;
                        }
                    }
                    
                    // Set form values
                    $('#complete_action_id').val(row_id);
                    $('#complete_client_id').val(clientId);
                    $('#complete-action-client span').text(clientName || 'N/A');
                    $('#completion_message').val('');
                    
                    // Show modal
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        var modalElement = document.getElementById('completeActionModal');
                        var modal = new bootstrap.Modal(modalElement);
                        modal.show();
                    } else {
                        $('#completeActionModal').modal('show');
                    }
                },
                error: function(xhr){
                    // Fallback if note data fetch fails - try to get from note directly
                    console.warn('Failed to fetch note data, using fallback');
                    
                    // Set form values with available data
                    $('#complete_action_id').val(row_id);
                    $('#complete_client_id').val(''); // Will be fetched from note on backend
                    $('#complete-action-client span').text(clientName || 'N/A');
                    $('#completion_message').val('');
                    
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        var modalElement = document.getElementById('completeActionModal');
                        var modal = new bootstrap.Modal(modalElement);
                        modal.show();
                    } else {
                        $('#completeActionModal').modal('show');
                    }
                }
            });
        }
	});
    
    // Handle complete action form submission
    $('#submitCompleteAction').on('click', function() {
        var actionId = $('#complete_action_id').val();
        var clientId = $('#complete_client_id').val();
        var message = $('#completion_message').val().trim();
        
        if (!message) {
            alert('Please enter a completion message.');
            return;
        }
        
        // Disable button during submission
        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Completing...');
        
        $.ajax({
            url: "{{URL::to('/')}}/action/task-complete",
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: actionId,
                client_id: clientId,
                completion_message: message
            },
            success: function(response) {
                if (response.status) {
                    // Hide modal
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        var modalElement = document.getElementById('completeActionModal');
                        var modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) modal.hide();
                    } else {
                        $('#completeActionModal').modal('hide');
                    }
                    
                    // Show success message
                    alert(response.message || 'Action completed successfully!');
                    
                    // Reload page to reflect changes
                    location.reload();
                } else {
                    alert(response.message || 'Failed to complete action. Please try again.');
                    $('#submitCompleteAction').prop('disabled', false).html('Complete Action');
                }
            },
            error: function(xhr) {
                var errorMsg = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
                $('#submitCompleteAction').prop('disabled', false).html('Complete Action');
            }
        });
    });


    //re-assign task or update task
    $(document).delegate('#assignUser','click', function(){
		$(".popuploader").show();
		var flag = true;
		var error ="";
		$(".custom-error").remove();
		
		// Find the visible popover to scope our selectors
		var $popover = $('.popover:visible').last();
		var $form = $popover.length ? $popover : $(document); // Fallback to document if popover not found
		
		if($form.find('#rem_cat').val() == ''){
			$('.popuploader').hide();
			error="Assignee field is required.";
			$form.find('#rem_cat').after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
		if($form.find('#assignnote').val() == ''){
			$('.popuploader').hide();
			error="Note field is required.";
			$form.find('#assignnote').after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
        if($form.find('#task_group').val() == ''){
			$('.popuploader').hide();
			error="Group field is required.";
			$form.find('#task_group').after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
		if(flag){
			$.ajax({
				type:'post',
                url:"{{URL::to('/')}}/clients/reassignaction/store",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
					note_id: $form.find('#assign_note_id').val(),
					note_type: 'action',
					description: $form.find('#assignnote').val(),
					client_id: $form.find('#assign_client_id').val(),
					followup_datetime: $form.find('#popoverdatetime').val(),
					assignee_name: $form.find('#rem_cat :selected').text(),
					rem_cat: $form.find('#rem_cat option:selected').val(),
					task_group: $form.find('#task_group option:selected').val()
				},
                success: function(response){
                    console.log(response);
                    $('.popuploader').hide();
                    var obj = $.parseJSON(response);
                    if(obj.success){
                        $("[data-role=popover]").each(function(){
                            (($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false  // fix for BS 3.3.6
                        });
                        location.reload();
                    } else{
                        alert(obj.message);
                        location.reload();
                    }
                }
			});
		}else{
			$("#loader").hide();
		}
	});


    //update task
    $(document).delegate('#updateTask','click', function(){
		$(".popuploader").show();
		var flag = true;
		var error ="";
		$(".custom-error").remove();

		// Find the visible popover to scope our selectors
		var $popover = $('.popover:visible').last();
		var $form = $popover.length ? $popover : $(document); // Fallback to document if popover not found

		if($form.find('#rem_cat').val() == ''){
			$('.popuploader').hide();
			error="Assignee field is required.";
			$form.find('#rem_cat').after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
		if($form.find('#assignnote').val() == ''){
			$('.popuploader').hide();
			error="Note field is required.";
			$form.find('#assignnote').after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
        if($form.find('#task_group').val() == ''){
			$('.popuploader').hide();
			error="Group field is required.";
			$form.find('#task_group').after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
		if(flag){
			$.ajax({
				type:'post',
                url:"{{URL::to('/')}}/clients/updateaction/store",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
					note_id: $form.find('#assign_note_id').val(),
					note_type: 'action',
					description: $form.find('#assignnote').val(),
					client_id: $form.find('#assign_client_id').val(),
					followup_datetime: $form.find('#popoverdatetime').val(),
					assignee_name: $form.find('#rem_cat :selected').text(),
					rem_cat: $form.find('#rem_cat option:selected').val(),
					task_group: $form.find('#task_group option:selected').val()
				},
                success: function(response){
                    console.log(response);
                    $('.popuploader').hide();
                    var obj = $.parseJSON(response);
                    if(obj.success){
                        $("[data-role=popover]").each(function(){
                            (($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false  // fix for BS 3.3.6
                        });
                        location.reload();
                    } else{
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
			url: site_url+'/change_assignee',
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
			url: site_url+'/update_apppointment_comment',
			type:'POST',
			data:{"_token":$('meta[name="csrf-token"]').attr('content'),id: appliid,visit_comment:visitcomment},
			success: function(responses){
				// $('.popuploader').hide();
				$('.taskcomment').val('');
				$.ajax({
					url: site_url+'/get-assigne-detail',
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
			url: site_url+'/get-assigne-detail',
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
			url: site_url+'/update_list_status',
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
					url: site_url+'/get-assigne-detail',
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
			url: site_url+'/update_list_priority',
			type:'POST',
			data:{"_token":$('meta[name="csrf-token"]').attr('content'),id: appliid,status:status},
			success: function(responses){
				$('.popuploader').hide();

				$.ajax({
					url: site_url+'/get-assigne-detail',
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
			url: site_url+'/update_apppointment_description',
			type:'POST',
			data:{"_token":$('meta[name="csrf-token"]').attr('content'),id: appliid,visit_purpose:visitpurpose},
			success: function(responses){
				$.ajax({
					url: site_url+'/get-assigne-detail',
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
			url: site_url+'/update_apppointment_description',
			type:'POST',
			data:{"_token":$('meta[name="csrf-token"]').attr('content'),id: appliid,visit_purpose:visitpurpose},
			success: function(responses){
				 $.ajax({
					url: site_url+'/get-assigne-detail',
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

@push('tinymce-scripts')
@include('partials.tinymce')
@endpush

@endsection
