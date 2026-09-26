@extends('layouts.admin')
@section('title', 'Assigned by me')

@section('content')
<style>
.fc-event-container .fc-h-event{cursor:pointer;}
.sort_col a { color: #212529 !important; font-weight: 700 !important;}
.group_type_section a.active {color:black;}
.countAction {background: #1f1655;padding: 0px 5px;border-radius: 50%;color: #fff;margin-left: 5px;}
.popover .popover-body { overflow: visible !important; }
.popover .ts-wrapper { z-index: 100001 !important; width: 100% !important; }
.popover .ts-dropdown { z-index: 100001 !important; }
.action-btns { display: flex; gap: 4px; flex-wrap: nowrap; align-items: center; }
.action-btns .btn { flex-shrink: 0; }
.table td { vertical-align: middle; }
</style>
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('Elements.flash-message')
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
                                <form action="{{ route('action.assigned_by_me') }}" method="get" id="assignedByMeFilters">
                                    <div class="row mb-2">
                                        <div class="col-md-12">
                                            <label class="form-check form-check-inline" title="Future-dated Followup actions are hidden by default until their assign date">
                                                <input type="checkbox" class="form-check-input" name="include_scheduled_followups" value="1"
                                                    {{ !empty($includeScheduledFollowups) ? 'checked' : '' }}
                                                    onchange="document.getElementById('assignedByMeFilters').submit();">
                                                Include scheduled follow-ups
                                            </label>
                                            <small class="text-muted d-block">Default list shows Followups only on/after their assign date.</small>
                                        </div>
                                    </div>
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
                                                <th width="120px" class="sort_col">@sortablelink('action_assign_date','Action Date')</th>
                                                <th width="100px" class="sort_col">@sortablelink('task_group','Type')</th>
                                                <th>Note</th>
                                                <th width="140px">Action</th>
                                            </tr>
                                            @php
                                                $assignableStaff = $assignableStaff ?? collect();
                                            @endphp
                                            <?php
                                            if(count($assignees_notCompleted)>0){
                                            ?>
                                            @foreach ($assignees_notCompleted as $list)
                                            @php
                                                $assignee = $list->assigned_user;
                                                $full_name = $assignee
                                                    ? trim(($assignee->first_name ?? 'N/A').' '.($assignee->last_name ?? 'N/A'))
                                                    : 'N/P';
                                            @endphp
                                            <tr>
                                                @php
                                                    if ($list->noteClient) {
                                                        $user_name = $list->noteClient->first_name.' '.$list->noteClient->last_name;
                                                    } else {
                                                        $user_name = 'N/P';
                                                    }
                                                @endphp
                                                <td style="text-align: center;">{{ ++$i }}</td>
                                                <td style="text-align: center;"><input type="radio" class="complete_task" data-bs-toggle="tooltip" title="Mark Complete!" data-id="{{ $list->id }}"></td>
                                                <td>{{ $full_name }}</td>
                                                <td>
                                                    {{ $user_name }}
                                                    <br>
                                                    @if($list->noteClient)
                                                        @php
                                                            $encodedRefId = base64_encode(convert_uuencode(@$list->client_id));
                                                            $isLeadType = strtolower((string) ($list->noteClient->type ?? '')) === 'lead';
                                                            $detailUrl = $isLeadType
                                                                ? route('leads.detail', $encodedRefId)
                                                                : route('clients.detail', $encodedRefId);
                                                        @endphp
                                                        <a href="{{ $detailUrl }}" target="_blank">{{ $list->noteClient->client_id }}</a>
                                                    @endif
                                                </td>

                                                <td>
                                                    @if(!empty($list->action_assign_date))
                                                        {{ date('d/m/Y', strtotime($list->action_assign_date)) }}
                                                        @php
                                                            $isFutureFollowup = strcasecmp((string) ($list->task_group ?? ''), 'Followup') === 0
                                                                && \Carbon\Carbon::parse($list->action_assign_date)->timezone(config('app.timezone'))->startOfDay()
                                                                    ->gt(\Carbon\Carbon::today(config('app.timezone')));
                                                        @endphp
                                                        @if($isFutureFollowup)
                                                            <span class="badge bg-info text-dark">Scheduled</span>
                                                        @endif
                                                    @else
                                                        N/P
                                                    @endif
                                                </td>
                                                <td>{{ $list->task_group ?? 'N/P' }}</td>
                                                <td>
                                                    @php
                                                        $plainDescription = trim(strip_tags((string) ($list->description ?? '')));
                                                    @endphp
                                                    @if ($plainDescription !== '')
                                                        @php
                                                            $safeHtml = \App\Support\Utf8Helper::sanitizeForHtml($plainDescription);
                                                        @endphp
                                                        @if (mb_strlen($plainDescription) > 190)
                                                            @php
                                                                $preview = \App\Support\Utf8Helper::sanitizeForHtml(mb_substr($plainDescription, 0, 190));
                                                                $safeAttr = \App\Support\Utf8Helper::sanitizeForHtmlAttribute($plainDescription);
                                                            @endphp
                                                            {!! $preview !!} <button type="button" class="btn btn-link" data-bs-toggle="popover" data-bs-html="false" title="" data-bs-content="{{ $safeAttr }}">Read more</button>
                                                        @else
                                                            {!! $safeHtml !!}
                                                        @endif
                                                    @else
                                                        N/P
                                                    @endif
                                                </td>

                                                <td>
                                                    <form action="{{ route('action.destroy_by_me',$list->id) }}" method="POST" class="d-inline">
                                                        <div class="action-btns">
                                                        @if($list->task_group != 'Personal Task')
                                                        <div id="popover-update-{{ $list->id }}" class="d-none">
                                                            <h4 class="text-center">Update Task</h4>
                                                            <div class="clearfix"></div>
                                                            <div class="box-header with-border">
                                                                <div class="form-group row" style="margin-bottom:12px">
                                                                    <label class="col-sm-3 control-label c6 f13" style="margin-top:8px">Select Assignee</label>
                                                                    <div class="col-sm-9">
                                                                        <select class="assignee-tomselect tomselect form-control selec_reg rem_cat" name="rem_cat">
                                                                            <option value="">Select</option>
                                                                            @foreach($assignableStaff as $admin)
                                                                            <option value="{{ $admin->id }}" {{ $admin->id == $list->assigned_to ? 'selected' : '' }}>{{ $admin->first_name.' '.$admin->last_name.' ('.($admin->office->office_name ?? '').')' }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="box-header with-border">
                                                                <div class="form-group row" style="margin-bottom:12px">
                                                                    <label class="col-sm-3 control-label c6 f13" style="margin-top:8px">Note</label>
                                                                    <div class="col-sm-9">
                                                                        <textarea class="form-control assignnote tinymce-simple js-staff-mentions f13" placeholder="Enter a note... (type @ to tag staff)" rows="3"></textarea>
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
                                                                    <select class="assignee-tomselect tomselect form-control task_group" name="task_group">
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
                                                        <button type="button" data-popover-target="popover-update-{{ $list->id }}" data-description="{{ $list->description }}" data-taskid="{{ $list->id }}" data-taskgroupid="{{ $list->task_group }}" data-followupdate="{{ $list->action_assign_date }}" data-assignedto="{{ $list->assigned_to }}" class="btn btn-primary btn-sm update_task" data-bs-toggle="tooltip" title="Update Task">@icon('edit')</button>
                                                        @endif

                                                        @if($list->task_group != 'Personal Task')
                                                        <div id="popover-assign-{{ $list->id }}" class="d-none">
                                                            <h4 class="text-center">Re-Assign Staff</h4>
                                                            <div class="clearfix"></div>
                                                            <div class="box-header with-border">
                                                                <div class="form-group row" style="margin-bottom:12px">
                                                                    <label class="col-sm-3 control-label c6 f13" style="margin-top:8px">Select Assignee</label>
                                                                    <div class="col-sm-9">
                                                                        <select class="assignee-tomselect tomselect form-control selec_reg rem_cat" name="rem_cat">
                                                                            <option value="">Select</option>
                                                                            @foreach($assignableStaff as $admin)
                                                                            <option value="{{ $admin->id }}">{{ $admin->first_name.' '.$admin->last_name.' ('.($admin->office->office_name ?? '').')' }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="box-header with-border">
                                                                <div class="form-group row" style="margin-bottom:12px">
                                                                    <label class="col-sm-3 control-label c6 f13" style="margin-top:8px">Note</label>
                                                                    <div class="col-sm-9">
                                                                        <textarea class="form-control assignnote tinymce-simple js-staff-mentions f13" placeholder="Enter a note... (type @ to tag staff)" rows="3"></textarea>
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
                                                                    <select class="assignee-tomselect tomselect form-control task_group" name="task_group">
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
                                                                        <button type="button" class="btn btn-info assignUser">Assign Staff</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <button type="button" data-popover-target="popover-assign-{{ $list->id }}" data-description="{{ $list->description }}" data-taskid="{{ $list->id }}" data-taskgroupid="{{ $list->task_group }}" data-followupdate="{{ $list->action_assign_date }}" data-assignedto="{{ $list->assigned_to }}" class="btn btn-primary btn-sm reassign_task" data-bs-toggle="tooltip" title="Assign Staff">@icon('tasks')</button>
                                                        @endif

                                                        @csrf
                                                        @method('DELETE')
                                                        </div>
                                                    </form>
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
<!-- Assign Modal (legacy appointment detail — removed) -->

<!-- Update Task / Assign Staff Modal (populated from template) -->
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

<!-- Complete Action Modal -->
<div class="modal fade" id="completeActionModal" tabindex="-1" role="dialog" aria-labelledby="completeActionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completeActionModalLabel">Complete Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="submitCompleteAction">Complete Action</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')

@push('scripts')
	@vite(['resources/js/pages/admin/popover-entry.js'])
@endpush

<script>
jQuery(document).ready(function($){
    $('[data-bs-toggle="tooltip"]').tooltip();

    $(document).delegate('.openassignee', 'click', function(){
        $('.assignee').show();
    });

	$(document).delegate('.closeassignee', 'click', function(){
        $('.assignee').hide();
    });

    function showActionPopoverModal(title, $clone, assignedTo) {
        $('#actionPopoverModalLabel').text(title);
        $('#actionPopoverModalBody').html($clone);

        if (assignedTo) {
            $.ajax({
                type: 'post',
                url: "{{URL::to('/')}}/action/assignee-list",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: { assignedto: assignedTo },
                success: function(response) {
                    var obj = $.parseJSON(response);
                    if (!obj.message) {
                        return;
                    }
                    var html = Array.isArray(obj.message) ? obj.message.join('') : obj.message;
                    var $sel = $('#actionPopoverModalBody .rem_cat').first();
                    if (window.ActionPopoverTomSelect) {
                        ActionPopoverTomSelect.refreshAssigneeSelect($sel[0], html, $('#actionPopoverModalBody')[0]);
                    } else {
                        $sel.html(html);
                    }
                }
            });
        }

        var modalEl = document.getElementById('actionPopoverModal');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        } else {
            $(modalEl).modal('show');
        }
        if (typeof initModalFlatpickrDates === 'function') {
            initModalFlatpickrDates('#actionPopoverModal');
        } else if (typeof flatpickr !== 'undefined') {
            document.querySelectorAll('#actionPopoverModal .flatpickr-date').forEach(function (el) {
                if (el._flatpickr) {
                    el._flatpickr.destroy();
                }
                flatpickr(el, { dateFormat: 'Y-m-d', allowInput: true });
            });
        }
    }

    $(document).delegate('.update_task', 'click', function(e){
        e.preventDefault();
        var $btn = $(this);
        var targetId = $btn.data('popover-target');
        var $template = $('#' + targetId);
        if (!$template.length) {
            return;
        }

        var noteDescription = $btn.attr('data-description');
        if (noteDescription === undefined) {
            noteDescription = $btn.attr('data-noteid') || '';
        }
        var taskId = $btn.data('taskid');
        var taskgroupId = $btn.data('taskgroupid');
        var followupdate = ($btn.data('followupdate') || '').toString().split(' ')[0] || '{{ date("Y-m-d") }}';

        var $clone = $template.clone().removeClass('d-none');
        $clone.find('.assign_note_id').val(taskId);
        $clone.find('.assignnote').val(noteDescription);
        if (typeof setEnhancedSelectValue === 'function') {
            setEnhancedSelectValue($clone.find('.task_group')[0], taskgroupId);
        } else {
            $clone.find('.task_group').val(taskgroupId);
        }
        $clone.find('.popoverdatetime').val(followupdate);

        showActionPopoverModal('Update Task', $clone, $btn.data('assignedto'));
    });

    $(document).delegate('.reassign_task', 'click', function(e){
        e.preventDefault();
        var $btn = $(this);
        var targetId = $btn.data('popover-target');
        var $template = $('#' + targetId);
        if (!$template.length) {
            return;
        }

        var noteDescription = $btn.attr('data-description');
        if (noteDescription === undefined) {
            noteDescription = $btn.attr('data-noteid') || '';
        }
        var taskId = $btn.data('taskid');
        var taskgroupId = $btn.data('taskgroupid');
        var followupdate = ($btn.data('followupdate') || '').toString().split(' ')[0] || '{{ date("Y-m-d") }}';
        var assignedTo = $btn.data('assignedto');

        var $clone = $template.clone().removeClass('d-none');
        $clone.find('.assign_note_id').val(taskId);
        $clone.find('.assignnote').val(noteDescription);
        if (typeof setEnhancedSelectValue === 'function') {
            setEnhancedSelectValue($clone.find('.task_group')[0], taskgroupId);
        } else {
            $clone.find('.task_group').val(taskgroupId);
        }
        $clone.find('.popoverdatetime').val(followupdate);

        showActionPopoverModal('Re-Assign Staff', $clone, assignedTo);
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
                    // Handle string (legacy) or object (application/json) without breaking reload
                    var obj = (typeof response === 'string') ? $.parseJSON(response) : response;
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
            showToast('Please enter a completion message.', 'warning');
            return;
        }
        
        // Disable button during submission
        $(this).prop('disabled', true).html(crmIconSpinner('Completing...'));
        
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
                    showToast(response.message || 'Action completed successfully!', 'success');
                    
                    // Reload page to reflect changes
                    location.reload();
                } else {
                    showToast(response.message || 'Failed to complete action. Please try again.', 'error');
                    $('#submitCompleteAction').prop('disabled', false).html('Complete Action');
                }
            },
            error: function(xhr) {
                var errorMsg = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showToast(errorMsg, 'error');
                $('#submitCompleteAction').prop('disabled', false).html('Complete Action');
            }
        });
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
		if(typeof actionPopoverSelectVal === 'function' ? actionPopoverSelectVal($remCat) === '' : $remCat.val() == ''){
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
		if(typeof actionPopoverSelectVal === 'function' ? actionPopoverSelectVal($taskGroup) === '' : $taskGroup.val() == ''){
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
					rem_cat: typeof actionPopoverSelectVal === 'function' ? actionPopoverSelectVal($remCat) : $remCat.val(),
					task_group: typeof actionPopoverSelectVal === 'function' ? actionPopoverSelectVal($taskGroup) : $taskGroup.val()
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
						showToast(obj.message, 'error');
					}
				}
			});
		}else{
			$('.popuploader').hide();
		}
	});

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
		if(typeof actionPopoverSelectVal === 'function' ? actionPopoverSelectVal($remCat) === '' : $remCat.val() == ''){
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
		if(typeof actionPopoverSelectVal === 'function' ? actionPopoverSelectVal($taskGroup) === '' : $taskGroup.val() == ''){
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
					rem_cat: typeof actionPopoverSelectVal === 'function' ? actionPopoverSelectVal($remCat) : $remCat.val(),
					task_group: typeof actionPopoverSelectVal === 'function' ? actionPopoverSelectVal($taskGroup) : $taskGroup.val()
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
						showToast(obj.message || 'Update failed', 'error');
					}
				}
			});
		}else{
			$('.popuploader').hide();
		}
	});
});
</script>

@push('tinymce-scripts')
@include('partials.tinymce')
@endpush

@endsection
