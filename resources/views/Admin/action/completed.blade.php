@extends('layouts.admin')
@section('title', 'Completed Activities')

@section('content')
<style>
.fc-event-container .fc-h-event{cursor:pointer;}
#openassigneview .modal-body ul.navbar-nav li .dropdown-menu{transform: none!important; top:40px!important;}
.sort_col a { color: #212529 !important; font-weight: 700 !important;}
.group_type_section a.active {color:black;}
.select2-container{z-index:100000;width:315px !important;}
.countAction {background: #1f1655;padding: 0px 5px;border-radius: 50%;color: #fff;margin-left: 5px;}
.action-btns { display: flex; gap: 4px; flex-wrap: nowrap; align-items: center; }
.action-btns .btn { flex-shrink: 0; }
.table td { vertical-align: middle; }
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
									<a class="nav-link active" id="archived-tab"  href="{{URL::to('/action')}}">Incomplete</a>
								</li>
                            </ul>
						</div>
						<div class="card-body">
							<div class="tab-content" id="quotationContent">
                                <form action="{{ route('action.completed') }}" method="get">
                                    <div class="row">
                                        <div class="col-md-12 group_type_section">
                                            <?php
                                            if(\Auth::user()->role == 1){
                                                $assigneesCount_All_type = \App\Models\Note::whereIn('type',['client','partner'])->whereNotNull('client_id')->where('folloup',1)->where('status',1)->orderBy('created_at', 'desc')->count();

                                                $assigneesCount_call_type = \App\Models\Note::where('task_group','like','Call')
                                                ->whereIn('type',['client','partner'])->whereNotNull('client_id')->where('folloup',1)->where('status',1)->orderBy('created_at', 'desc')->count();

                                                $assigneesCount_Checklist_type = \App\Models\Note::where('task_group','like','Checklist')
                                                ->whereIn('type',['client','partner'])->whereNotNull('client_id')->where('folloup',1)->where('status',1)->orderBy('created_at', 'desc')->count();

                                                $assigneesCount_Review_type = \App\Models\Note::where('task_group','like','Review')
                                                ->whereIn('type',['client','partner'])->whereNotNull('client_id')->where('folloup',1)->where('status',1)->orderBy('created_at', 'desc')->count();

                                                $assigneesCount_Query_type = \App\Models\Note::where('task_group','like','Query')
                                                ->whereIn('type',['client','partner'])->whereNotNull('client_id')->where('folloup',1)->where('status',1)->orderBy('created_at', 'desc')->count();

                                                $assigneesCount_Urgent_type = \App\Models\Note::where('task_group','like','Urgent')
                                                ->whereIn('type',['client','partner'])->whereNotNull('client_id')->where('folloup',1)->where('status',1)->orderBy('created_at', 'desc')->count();

                                                $assigneesCount_Personal_Task_type = \App\Models\Note::where('task_group','like','Personal Task')
                                                ->whereIn('type',['client','partner'])->whereNotNull('client_id')->where('folloup',1)->where('status',1)->orderBy('created_at', 'desc')->count();

                                            } else {
                                                $assigneesCount_All_type = \App\Models\Note::where('assigned_to',Auth::user()->id)->whereIn('type',['client','partner'])->where('folloup',1)->where('status',1)->orderBy('created_at', 'desc')->count();

                                                $assigneesCount_call_type = \App\Models\Note::where('task_group','like','Call')
                                                ->where('assigned_to',Auth::user()->id)->whereIn('type',['client','partner'])->where('folloup',1)->where('status',1)->orderBy('created_at', 'desc')->count();

                                                $assigneesCount_Checklist_type = \App\Models\Note::where('task_group','like','Checklist')
                                                ->where('assigned_to',Auth::user()->id)->whereIn('type',['client','partner'])->where('folloup',1)->where('status',1)->orderBy('created_at', 'desc')->count();

                                                $assigneesCount_Review_type = \App\Models\Note::where('task_group','like','Review')
                                                ->where('assigned_to',Auth::user()->id)->whereIn('type',['client','partner'])->where('folloup',1)->where('status',1)->orderBy('created_at', 'desc')->count();

                                                $assigneesCount_Query_type = \App\Models\Note::where('task_group','like','Query')
                                                ->where('assigned_to',Auth::user()->id)->whereIn('type',['client','partner'])->where('folloup',1)->where('status',1)->orderBy('created_at', 'desc')->count();

                                                $assigneesCount_Urgent_type = \App\Models\Note::where('task_group','like','Urgent')
                                                ->where('assigned_to',Auth::user()->id)->whereIn('type',['client','partner'])->where('folloup',1)->where('status',1)->orderBy('created_at', 'desc')->count();

                                                $assigneesCount_Personal_Task_type = \App\Models\Note::where('task_group','like','Personal Task')
                                                ->where('assigned_to',Auth::user()->id)->whereIn('type',['client','partner'])->where('folloup',1)->where('status',1)->orderBy('created_at', 'desc')->count();
                                            } ?>


                                            <?php //echo $task_group;?>
                                            <a href="{{URL::to('/action/completed?group_type=All')}}" id="All" class="group_type <?php if($task_group == 'All') { echo 'active';}?>">All <span class="countAction">{{ $assigneesCount_All_type }}</span></a> | &nbsp;

                                            <button type="button" class="btn btn-light">
                                                <a href="{{URL::to('/action/completed?group_type=Call')}}" id="Call" class="group_type <?php if($task_group == 'Call') { echo 'active';}?>"> <i class="fa fa-phone" aria-hidden="true"></i> Call <span class="countAction">{{ $assigneesCount_call_type }}</span></a> &nbsp;
                                            </button>

                                            <button type="button" class="btn btn-light">
                                                <a href="{{URL::to('/action/completed?group_type=Checklist')}}" id="Checklist" class="group_type <?php if($task_group == 'Checklist') { echo 'active';}?>"><i class="fa fa-bars" aria-hidden="true"></i> Checklist <span class="countAction">{{ $assigneesCount_Checklist_type }}</span></a> &nbsp;
                                            </button>

                                            <button type="button" class="btn btn-light">
                                                <a href="{{URL::to('/action/completed?group_type=Review')}}" id="Review" class="group_type <?php if($task_group == 'Review') { echo 'active';}?>"> <i class="fa fa-check" aria-hidden="true"></i> Review <span class="countAction">{{ $assigneesCount_Review_type }}</span></a> &nbsp;
                                            </button>

                                            <button type="button" class="btn btn-light">
                                                <a href="{{URL::to('/action/completed?group_type=Query')}}" id="Query" class="group_type <?php if($task_group == 'Query') { echo 'active';}?>"><i class="fa fa-question" aria-hidden="true"></i> Query <span class="countAction">{{ $assigneesCount_Query_type }}</span></a> &nbsp;
                                            </button>

                                            <button type="button" class="btn btn-light">
                                                <a href="{{URL::to('/action/completed?group_type=Urgent')}}" id="Urgent" class="group_type <?php if($task_group == 'Urgent') { echo 'active';}?>"> <i class="fa fa-flag" aria-hidden="true"></i> Urgent <span class="countAction">{{ $assigneesCount_Urgent_type }}</span></a> &nbsp;
                                            </button>

                                            <button type="button" class="btn btn-light">
                                                <a href="{{URL::to('/action/completed?group_type=Personal Task')}}" id="Personal Task" class="group_type <?php if($task_group == 'Personal Task') { echo 'active';}?>"> <i class="fa fa-tasks" aria-hidden="true"></i> Personal Task <span class="countAction">{{ $assigneesCount_Personal_Task_type }}</span></a> &nbsp;
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
                                                <th width="120px" class="sort_col">@sortablelink('followup_date','Action Date')</th>
                                                <th width="100px" class="sort_col">@sortablelink('task_group','Type')</th>
                                                <th>Note</th>
                                                <th width="140px">Action</th>
                                            </tr>
                                            <?php
                                            if(count($assignees_completed)>0){
                                            ?>
                                            @foreach ($assignees_completed as $list)
                                            <?php //echo "<pre>list==";print_r($list);
                                                $admin = \App\Models\Admin::where('id', $list->user_id)->first();//dd($admin);
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
                                                // Handle both client and partner types
                                                if($list->type == 'partner'){
                                                    $partnerInfo = \App\Models\Partner::select('partner_name')->where('id',$list->client_id)->first();
                                                    if($partnerInfo){
                                                        $user_name = $partnerInfo->partner_name;
                                                        $reference_link = '<a href="'.route('partners.detail', base64_encode(convert_uuencode(@$list->client_id))).'" target="_blank" >'.$partnerInfo->partner_name.'</a>';
                                                    } else {
                                                        $user_name = 'N/P';
                                                        $reference_link = 'N/P';
                                                    }
                                                } else {
                                                    // Client type
                                                    if($list->noteClient){
                                                        $user_name = $list->noteClient->first_name.' '.$list->noteClient->last_name;
                                                        $reference_link = '<a href="'.URL::to('/clients/detail/'.base64_encode(convert_uuencode(@$list->client_id))).'" target="_blank" >'.$list->noteClient->client_id.'</a>';
                                                    } else {
                                                        $user_name = 'N/P';
                                                        $reference_link = 'N/P';
                                                    }
                                                }
                                                ?>
                                                <td style="text-align: center;">{{ ++$i }}</td>
                                                <td style="text-align: center;"><input type="radio" class="not_complete_task" data-bs-toggle="tooltip" title="Mark Incomplete!" data-id="{{ $list->id }}"></td>
                                                <td>{{ $full_name??'N/P' }}</td>
                                                <td>
                                                    {{ $user_name }}
                                                    <br>
                                                    {!! $reference_link !!}
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
                                                    <form action="{{ route('action.destroy_completed',$list->id) }}" method="POST" class="d-inline">
                                                        <div class="action-btns">
                                                         @if($list->task_group != 'Personal Task')
                                                         {{-- Update Task: use template div to avoid HTML-in-attribute rendering issues --}}
                                                         <div id="popover-update-{{ $list->id }}" class="d-none">
                                                            <h4 class="text-center">Update Task</h4>
                                                            <div class="clearfix"></div>
                                                            <div class="box-header with-border">
                                                                <div class="form-group row" style="margin-bottom:12px">
                                                                    <label class="col-sm-3 control-label c6 f13" style="margin-top:8px">Select Assignee</label>
                                                                    <div class="col-sm-9">
                                                                        <select class="assigneeselect2 form-control selec_reg rem_cat" name="rem_cat">
                                                                            <option value="">Select</option>
                                                                            @foreach(\App\Models\Admin::where('role','!=',7)->where('status',1)->orderby('first_name','ASC')->get() as $admin)
                                                                            <?php $branchname = \App\Models\Branch::where('id',$admin->office_id)->first(); ?>
                                                                            <option value="{{ $admin->id }}" {{ $admin->id == $list->assigned_to ? 'selected' : '' }}>{{ $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')' }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="box-header with-border">
                                                                <div class="form-group row" style="margin-bottom:12px">
                                                                    <label class="col-sm-3 control-label c6 f13" style="margin-top:8px">Note</label>
                                                                    <div class="col-sm-9">
                                                                        <textarea class="form-control assignnote tinymce-simple f13" placeholder="Enter an note...." rows="3"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="box-header with-border">
                                                                <div class="form-group row" style="margin-bottom:12px">
                                                                    <label class="col-sm-3 control-label c6 f13" style="margin-top:8px">DateTime</label>
                                                                    <div class="col-sm-9">
                                                                        <input type="text" class="form-control f13 flatpickr-date popoverdatetime" placeholder="yyyy-mm-dd" value="{{ date('Y-m-d') }}" name="popoverdate" autocomplete="off">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row" style="margin-bottom:12px">
                                                                <label class="col-sm-3 control-label c6 f13" style="margin-top:8px">Group</label>
                                                                <div class="col-sm-9">
                                                                    <select class="assigneeselect2 form-control task_group" name="task_group">
                                                                        <option value="">Select</option>
                                                                        <option value="Call" {{ $list->task_group == 'Call' ? 'selected' : '' }}>Call</option>
                                                                        <option value="Checklist" {{ $list->task_group == 'Checklist' ? 'selected' : '' }}>Checklist</option>
                                                                        <option value="Review" {{ $list->task_group == 'Review' ? 'selected' : '' }}>Review</option>
                                                                        <option value="Query" {{ $list->task_group == 'Query' ? 'selected' : '' }}>Query</option>
                                                                        <option value="Urgent" {{ $list->task_group == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" class="assign_note_id" value="">
                                                            <input type="hidden" class="assign_client_id" value="{{ base64_encode(convert_uuencode(@$list->client_id)) }}">
                                                            <div class="box-footer" style="padding:10px 0">
                                                                <div class="row text-center">
                                                                    <div class="col-md-12">
                                                                        <button type="button" class="btn btn-info updateTask">Update Task</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                         </div>
                                                         <button type="button" data-popover-target="popover-update-{{ $list->id }}" data-noteid="{{ $list->description }}" data-taskid="{{ $list->id }}" data-taskgroupid="{{ $list->task_group }}" data-followupdate="{{ $list->followup_date }}" data-assignedto="{{ $list->assigned_to }}" class="btn btn-primary btn-sm update_task" data-bs-toggle="tooltip" title="Update Task"><i class="fa fa-edit" aria-hidden="true"></i></button>
                                                         @endif

                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure want to delete?');"><i class="fa fa-trash" aria-hidden="true"></i></button>

                                                        @if($list->task_group != 'Personal Task')
                                                        {{-- Assign User: use template div --}}
                                                        <div id="popover-assign-{{ $list->id }}" class="d-none">
                                                            <h4 class="text-center">Re-Assign User</h4>
                                                            <div class="clearfix"></div>
                                                            <div class="box-header with-border">
                                                                <div class="form-group row" style="margin-bottom:12px">
                                                                    <label class="col-sm-3 control-label c6 f13" style="margin-top:8px">Select Assignee</label>
                                                                    <div class="col-sm-9">
                                                                        <select class="assigneeselect2 form-control selec_reg rem_cat" name="rem_cat">
                                                                            <option value="">Select</option>
                                                                            @foreach(\App\Models\Admin::where('role','!=',7)->where('status',1)->orderby('first_name','ASC')->get() as $admin)
                                                                            <?php $branchname = \App\Models\Branch::where('id',$admin->office_id)->first(); ?>
                                                                            <option value="{{ $admin->id }}">{{ $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')' }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="box-header with-border">
                                                                <div class="form-group row" style="margin-bottom:12px">
                                                                    <label class="col-sm-3 control-label c6 f13" style="margin-top:8px">Note</label>
                                                                    <div class="col-sm-9">
                                                                        <textarea class="form-control assignnote tinymce-simple f13" placeholder="Enter an note...." rows="3"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="box-header with-border">
                                                                <div class="form-group row" style="margin-bottom:12px">
                                                                    <label class="col-sm-3 control-label c6 f13" style="margin-top:8px">DateTime</label>
                                                                    <div class="col-sm-9">
                                                                        <input type="text" class="form-control f13 flatpickr-date popoverdatetime" placeholder="yyyy-mm-dd" value="{{ date('Y-m-d') }}" name="popoverdate" autocomplete="off">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row" style="margin-bottom:12px">
                                                                <label class="col-sm-3 control-label c6 f13" style="margin-top:8px">Group</label>
                                                                <div class="col-sm-9">
                                                                    <select class="assigneeselect2 form-control task_group" name="task_group">
                                                                        <option value="">Select</option>
                                                                        <option value="Call">Call</option>
                                                                        <option value="Checklist">Checklist</option>
                                                                        <option value="Review">Review</option>
                                                                        <option value="Query">Query</option>
                                                                        <option value="Urgent">Urgent</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" class="assign_note_id" value="">
                                                            <input type="hidden" class="assign_client_id" value="{{ base64_encode(convert_uuencode(@$list->client_id)) }}">
                                                            <div class="box-footer" style="padding:10px 0">
                                                                <div class="row text-center">
                                                                    <div class="col-md-12">
                                                                        <button type="button" class="btn btn-info assignUser">Assign User</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <button type="button" data-popover-target="popover-assign-{{ $list->id }}" data-noteid="{{ $list->description }}" data-taskid="{{ $list->id }}" data-taskgroupid="{{ $list->task_group }}" data-followupdate="{{ $list->followup_date }}" data-assignedto="{{ $list->assigned_to }}" class="btn btn-primary btn-sm reassign_task" data-bs-toggle="tooltip" title="Assign User"><i class="fa fa-tasks" aria-hidden="true"></i></button>
                                                        @endif
                                                        </div>
                                                    </form>
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
		<div class="modal-content taskview"></div>
	</div>
</div>

<!-- Update Task / Assign User Modal (populated from template) -->
<div class="modal fade" id="actionPopoverModal" tabindex="-1" aria-labelledby="actionPopoverModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="actionPopoverModalLabel"></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="actionPopoverModalBody"></div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script src="{{asset('js/popover.js')}}"></script>
<script>
jQuery(document).ready(function($){
    $('[data-bs-toggle="tooltip"]').tooltip();

    $(document).delegate('.openassignee', 'click', function(){
        $('.assignee').show();
    });

	$(document).delegate('.closeassignee', 'click', function(){
        $('.assignee').hide();
    });

    // Update task - show modal with form from template
    $(document).delegate('.update_task', 'click', function(e){
        e.preventDefault();
        var $btn = $(this);
        var targetId = $btn.data('popover-target');
        var $template = $('#' + targetId);
        if (!$template.length) return;

        var noteId = $btn.data('noteid');
        var taskId = $btn.data('taskid');
        var taskgroupId = $btn.data('taskgroupid');
        var followupdate = ($btn.data('followupdate') || '').toString().split(' ')[0] || '{{ date("Y-m-d") }}';

        var $clone = $template.clone().removeClass('d-none');
        $clone.find('.assign_note_id').val(taskId);
        $clone.find('.assignnote').val(noteId);
        $clone.find('.task_group').val(taskgroupId);
        $clone.find('.popoverdatetime').val(followupdate);

        $('#actionPopoverModalLabel').text('Update Task');
        $('#actionPopoverModalBody').html($clone);
        var modalEl = document.getElementById('actionPopoverModal');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            new bootstrap.Modal(modalEl).show();
        } else {
            $(modalEl).modal('show');
        }
        if ($.fn.flatpickr) { $('#actionPopoverModal .flatpickr-date').flatpickr({ dateFormat: 'Y-m-d' }); }
    });

    // Reassign task - show modal with form from template
    $(document).delegate('.reassign_task', 'click', function(e){
        e.preventDefault();
        var $btn = $(this);
        var targetId = $btn.data('popover-target');
        var $template = $('#' + targetId);
        if (!$template.length) return;

        var noteId = $btn.data('noteid');
        var taskId = $btn.data('taskid');
        var taskgroupId = $btn.data('taskgroupid');
        var followupdate = ($btn.data('followupdate') || '').toString().split(' ')[0] || '{{ date("Y-m-d") }}';
        var assignedTo = $btn.data('assignedto');

        var $clone = $template.clone().removeClass('d-none');
        $clone.find('.assign_note_id').val(taskId);
        $clone.find('.assignnote').val(noteId);
        $clone.find('.task_group').val(taskgroupId);
        $clone.find('.popoverdatetime').val(followupdate);

        $('#actionPopoverModalLabel').text('Re-Assign User');
        $('#actionPopoverModalBody').html($clone);

        if (assignedTo) {
            $.ajax({ type:'post', url:"{{URL::to('/')}}/action/assignee-list", headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, data: {assignedto: assignedTo},
                success: function(r){ var obj = $.parseJSON(r); if(obj.message) { var html = Array.isArray(obj.message) ? obj.message.join('') : obj.message; $('#actionPopoverModalBody .rem_cat').first().html(html); } }
            });
        }

        var modalEl = document.getElementById('actionPopoverModal');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            new bootstrap.Modal(modalEl).show();
        } else {
            $(modalEl).modal('show');
        }
        if ($.fn.flatpickr) { $('#actionPopoverModal .flatpickr-date').flatpickr({ dateFormat: 'Y-m-d' }); }
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
	$(document).delegate('.complete_task', 'click', function(){
		var row_id = $(this).attr('data-id'); //alert(row_id);
        if(row_id !=""){ //&& confirm('Are you sure want to complete the task?')
            $.ajax({
				type:'post',
                url:"{{URL::to('/')}}/action/task-complete",
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


    $(document).delegate('#actionPopoverModalBody .assignUser','click', function(){
		$(".popuploader").show();
		var $modal = $('#actionPopoverModalBody');
		var flag = true;
		var error = "";
		$modal.find(".custom-error").remove();
		var $remCat = $modal.find('.rem_cat');
		var $assignNote = $modal.find('.assignnote');
		var $taskGroup = $modal.find('.task_group');
		var $assignNoteId = $modal.find('.assign_note_id');
		var $assignClientId = $modal.find('.assign_client_id');
		var $popoverDateTime = $modal.find('.popoverdatetime');
		if($remCat.val() == ''){
			$('.popuploader').hide();
			error="Assignee field is required.";
			$remCat.after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
		if($assignNote.val() == ''){
			$('.popuploader').hide();
			error="Note field is required.";
			$assignNote.after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
		if($taskGroup.val() == ''){
			$('.popuploader').hide();
			error="Group field is required.";
			$taskGroup.after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
		if(flag){
			$.ajax({
				type:'post',
				url:"{{URL::to('/')}}/clients/reassignaction/store",
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				data: {
					note_id: $assignNoteId.val(),
					note_type:'action',
					description:$assignNote.val(),
					client_id:$assignClientId.val(),
					followup_datetime:$popoverDateTime.val(),
					assignee_name:$remCat.find(':selected').text(),
					rem_cat:$remCat.val(),
					task_group:$taskGroup.val()
				},
				success: function(response){
					$('.popuploader').hide();
					var obj = $.parseJSON(response);
					if(obj.success){
						$('#actionPopoverModal').modal('hide');
						if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
							var m = bootstrap.Modal.getInstance(document.getElementById('actionPopoverModal'));
							if (m) m.hide();
						}
						location.reload();
					}else{
						alert(obj.message);
					}
				}
			});
		}
	});

	// Update task - called from modal
	$(document).delegate('#actionPopoverModalBody .updateTask','click', function(){
		$(".popuploader").show();
		var $modal = $('#actionPopoverModalBody');
		var flag = true;
		var error = "";
		$modal.find(".custom-error").remove();
		var $remCat = $modal.find('.rem_cat');
		var $assignNote = $modal.find('.assignnote');
		var $taskGroup = $modal.find('.task_group');
		var $assignNoteId = $modal.find('.assign_note_id');
		var $assignClientId = $modal.find('.assign_client_id');
		var $popoverDateTime = $modal.find('.popoverdatetime');
		if($remCat.val() == ''){
			$('.popuploader').hide();
			error="Assignee field is required.";
			$remCat.after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
		if($assignNote.val() == ''){
			$('.popuploader').hide();
			error="Note field is required.";
			$assignNote.after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
		if($taskGroup.val() == ''){
			$('.popuploader').hide();
			error="Group field is required.";
			$taskGroup.after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
		if(flag){
			$.ajax({
				type:'post',
				url:"{{URL::to('/')}}/clients/updateaction/store",
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				data: {
					note_id: $assignNoteId.val(),
					note_type:'action',
					description:$assignNote.val(),
					client_id:$assignClientId.val(),
					followup_datetime:$popoverDateTime.val(),
					assignee_name:$remCat.find(':selected').text(),
					rem_cat:$remCat.val(),
					task_group:$taskGroup.val()
				},
				success: function(response){
					$('.popuploader').hide();
					var obj = $.parseJSON(response);
					if(obj.success){
						var modalEl = document.getElementById('actionPopoverModal');
						if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
							var m = bootstrap.Modal.getInstance(modalEl);
							if (m) m.hide();
						} else {
							$('#actionPopoverModal').modal('hide');
						}
						location.reload();
					}else{
						alert(obj.message || 'Update failed');
					}
				}
			});
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
