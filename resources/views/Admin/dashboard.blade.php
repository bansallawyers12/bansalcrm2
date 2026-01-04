@extends('layouts.admin')
@section('title', 'Admin Dashboard')

@section('content')
<style>
    /* ============================================
       MODERN DASHBOARD DESIGN
       ============================================ */
    
    /* Dashboard Section */
    .main-content .section {
        padding: 2rem 0;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: calc(100vh - 100px);
    }
    
    /* Modern Card Design */
    .dash_card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        background: #ffffff;
        position: relative;
        height: 100%;
    }
    
    .dash_card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #6777ef 0%, #764ba2 100%);
    }
    
    .dash_card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }
    
    /* Card Variants with Gradient Backgrounds */
    .dash_card.stat-card-login::before {
        background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%);
    }
    
    .dash_card.stat-card-tasks::before {
        background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .dash_card.stat-card-checkin::before {
        background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
    }
    
    .card-statistic-4 {
        padding: 2rem;
        position: relative;
    }
    
    /* Card Content Styling */
    .card-content h5 {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .card-content h5 i {
        font-size: 1rem;
        color: #6777ef;
    }
    
    .card-content h2 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0.5rem 0;
        line-height: 1.2;
    }
    
    .card-content p {
        margin: 0;
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .card-content a {
        color: #6777ef;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }
    
    .card-content a:hover {
        color: #5568d3;
        text-decoration: underline;
    }
    
    /* Card Header */
    .card_header {
        margin-bottom: 1.25rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f3f4f6;
    }
    
    .card_header h5 {
        margin: 0;
        font-weight: 600;
        color: #1f2937;
        font-size: 0.9375rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .card_header h5 i {
        color: #6777ef;
        margin-right: 0.5rem;
    }
    
    /* Card Body */
    .card_body {
        min-height: 120px;
    }
    
    .card_body .text-center {
        padding: 2.5rem 1rem;
    }
    
    .card_body .text-muted {
        color: #9ca3af;
        font-size: 0.875rem;
    }
    
    /* Task Filter Dropdown */
    #task_filter {
        font-size: 0.75rem;
        height: 28px;
        padding: 4px 10px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        background-color: #ffffff;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 500;
    }
    
    #task_filter:hover {
        border-color: #6777ef;
    }
    
    #task_filter:focus {
        outline: none;
        border-color: #6777ef;
        box-shadow: 0 0 0 3px rgba(103, 119, 239, 0.1);
    }
    
    /* Empty State Styling */
    .card_body .text-center p {
        margin-bottom: 0.5rem;
        color: #6b7280;
        font-weight: 500;
    }
    
    .card_body .text-center small {
        font-size: 0.8125rem;
        color: #9ca3af;
    }
    
    /* Table Improvements */
    .card_body table {
        width: 100%;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .card_body table tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background-color 0.2s;
    }
    
    .card_body table tbody tr:last-child {
        border-bottom: none;
    }
    
    .card_body table tbody tr:hover {
        background-color: #f9fafb;
    }
    
    .card_body table tbody tr td {
        padding: 0.75rem 0.5rem;
        font-size: 0.8125rem;
    }
    
    /* Login Statistics Styling */
    .login-stats {
        font-size: 0.875rem;
    }
    
    .login-stats .stat-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
        transition: padding-left 0.2s;
    }
    
    .login-stats .stat-item:hover {
        padding-left: 0.5rem;
    }
    
    .login-stats .stat-item:last-child {
        border-bottom: none;
    }
    
    .login-stats .stat-item small {
        color: #6b7280;
        font-weight: 500;
        font-size: 0.75rem;
        display: block;
        margin-bottom: 0.25rem;
    }
    
    .login-stats .stat-item .font-weight-bold {
        color: #1f2937;
        font-size: 0.9375rem;
        font-weight: 600;
    }
    
    .login-stats .badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-weight: 600;
    }
    
    .login-stats .badge-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
    }
    
    .login-stats .badge-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #ffffff;
    }
    
    /* Notes Table Card */
    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
        padding: 1.25rem 1.5rem;
        border-bottom: none;
    }
    
    .card-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.125rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .card-body table {
        border-radius: 8px;
        overflow: hidden;
    }
    
    .card-body table thead {
        background: #f9fafb;
    }
    
    .card-body table thead th {
        font-weight: 600;
        color: #374151;
        font-size: 0.8125rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .card-body table tbody tr {
        border-bottom: 1px solid #f3f4f6;
    }
    
    .card-body table tbody tr:hover {
        background-color: #f9fafb;
    }
    
    .card-body table tbody td {
        padding: 1rem;
        color: #4b5563;
        font-size: 0.875rem;
    }
    
    .card-body table tbody td a {
        color: #6777ef;
        font-weight: 500;
        text-decoration: none;
    }
    
    .card-body table tbody td a:hover {
        text-decoration: underline;
    }
    
    .card-footer {
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
        padding: 1rem 1.5rem;
    }
    
    /* Button Improvements */
    .btn-primary {
        background: linear-gradient(135deg, #6777ef 0%, #5568d3 100%);
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1.25rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(103, 119, 239, 0.4);
    }
    
    /* Check-in Queue Styling */
    .card_body table tbody tr td a {
        color: #6777ef;
        font-weight: 500;
        text-decoration: none;
        transition: color 0.2s;
    }
    
    .card_body table tbody tr td a:hover {
        color: #5568d3;
        text-decoration: underline;
    }
    
    /* Task Status Colors */
    .task-status-completed {
        color: #10b981 !important;
        font-weight: 600;
    }
    
    .task-status-progress {
        color: #f59e0b !important;
        font-weight: 600;
    }
    
    .task-status-review {
        color: #6b7280 !important;
        font-weight: 600;
    }
    
    .task-status-todo {
        color: #3b82f6 !important;
        font-weight: 600;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .main-content .section {
            padding: 1rem 0;
        }
        
        .dash_card {
            margin-bottom: 1.5rem;
        }
        
        .card-statistic-4 {
            padding: 1.5rem;
        }
        
        .card-content h2 {
            font-size: 2rem;
        }
        
        .login-stats {
            font-size: 0.8125rem;
        }
        
        .card-header {
            padding: 1rem;
        }
        
        .card-body {
            padding: 1rem;
        }
    }
    
    /* Animation for numbers */
    @keyframes countUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .card-content h2 {
        animation: countUp 0.6s ease-out;
    }
    
    /* Icon Styling */
    .card-content h5 i,
    .card_header h5 i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: rgba(103, 119, 239, 0.1);
        color: #6777ef;
    }
</style>

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<!-- Dashboard Header -->
		<div class="row mb-4">
			<div class="col-12">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<h2 class="mb-1" style="color: #1f2937; font-weight: 700; font-size: 1.875rem;">
							Welcome back, {{ Auth::user()->first_name ?? 'User' }}!
						</h2>
						<p class="text-muted mb-0" style="font-size: 0.9375rem;">
							Here's what's happening with your CRM today
						</p>
					</div>
					<div>
						<span class="badge badge-primary" style="background: linear-gradient(135deg, #6777ef 0%, #5568d3 100%); padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem;">
							<i class="fa fa-calendar"></i> {{ date('l, F j, Y') }}
						</span>
					</div>
				</div>
			</div>
		</div>

        <div class="row">

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-4">
				<div class="card dash_card stat-card-login">
					<div class="card-statistic-4">
						<div class="align-items-center justify-content-between">
							<div class="row ">
								<div class="col-lg-12 col-md-12">
									<div class="card-content">
										<h5 class="font-14">
											<i class="fa fa-user-clock"></i> Login Statistics
										</h5>
										<div class="login-stats mt-3">
											<div class="stat-item mb-2">
												<small class="text-muted d-block">Last Login:</small>
												<span class="font-weight-bold">{{$loginStats['last_login_formatted']}}</span>
												@if($loginStats['time_since_last_login_formatted'] != 'Never')
													<small class="text-muted d-block mt-1">
														({{$loginStats['time_since_last_login_formatted']}})
													</small>
												@endif
											</div>
											<div class="stat-item mb-2">
												<small class="text-muted d-block">Current Session:</small>
												<span class="font-weight-bold" id="current_session_duration">{{$loginStats['current_session_duration_formatted']}}</span>
												<small class="text-muted d-block mt-1">
													Since: {{$loginStats['current_login_formatted']}}
												</small>
											</div>
											<div class="stat-item">
												<small class="text-muted d-block">Status:</small>
												@if($loginStats['is_active'])
													<span class="badge badge-success">Active</span>
												@else
													<span class="badge badge-warning">Inactive</span>
													<small class="text-muted d-block mt-1">
														({{$loginStats['inactivity_formatted']}})
													</small>
												@endif
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>



            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-4">
                <div class="card dash_card stat-card-tasks">
                    <div class="card-statistic-4">
                        <div class="card-content cus_card_content">
                            <div class="card_header">
                                <h5 class="font-14">
                                    <i class="fa fa-tasks"></i> My Actions 
                                    <select id="task_filter" class="form-control form-control-sm d-inline-block" style="width: auto; display: inline-block; margin-left: 5px; padding: 2px 5px;">
                                        <option value="today" {{$dateFilter == 'today' ? 'selected' : ''}}>Today</option>
                                        <option value="week" {{$dateFilter == 'week' ? 'selected' : ''}}>This Week</option>
                                        <option value="month" {{$dateFilter == 'month' ? 'selected' : ''}}>This Month</option>
                                    </select>
                                </h5>
                            </div>
                            <div class="card_body">
                                @if($todayTasks->count() > 0)
                                <div class="taskdata_list">
                                    <div class="table-responsive">
                                        <table id="my-datatable" class="table-2 table text_wrap">
                                            <tbody class="taskdata">
                                            @foreach($todayTasks as $alist)
                                                @php
                                                    // Get action title or description snippet
                                                    $actionTitle = $alist->title ?? 'Action';
                                                    if(empty($actionTitle) && !empty($alist->description)) {
                                                        $actionTitle = \Illuminate\Support\Str::limit(strip_tags($alist->description), 30);
                                                    }
                                                    // Client/partner name is now available directly from the service
                                                    $clientName = $alist->client_name ?? 'N/A';
                                                @endphp
                                                <tr style="cursor:pointer;" id="{{$alist->id}}">
                                                    <td>
                                                        <span class='round'></span>
                                                    </td>
                                                    <td>
                                                        <strong>{{$actionTitle}}</strong><br>
                                                        <small class="text-muted">{{$clientName}}</small><br>
                                                        <i class="fa fa-clock"></i> {{$alist->formatted_due_date ?? 'N/A'}}
                                                    </td>
                                                    <td>
                                                        <span class="task-status-todo">Active</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @else
                                <div class="text-center py-3">
                                    <p class="text-muted mb-0">No actions for {{$dateFilter == 'today' ? 'today' : ($dateFilter == 'week' ? 'this week' : 'this month')}}.</p>
                                    <small class="text-muted">All caught up!</small>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-4">
                <div class="card dash_card stat-card-checkin">
                    <div class="card-statistic-4">
                        <div class="card-content cus_card_content">
                            <div class="card_header">
                                <h5 class="font-14">
                                    <i class="fa fa-users"></i> Check-In Queue
                                </h5>
                            </div>
                            <div class="card_body">
                            @if($checkInQueue['total'] > 0)
                                <table>
                                    <tbody>
                                    @foreach($checkInQueue['items'] as $checkinslist)
                                        <tr>
                                            <td>
                                                <a id="{{@$checkinslist->id}}" class="opencheckindetail" href="javascript:;">
                                                    {{@$checkinslist->client->first_name ?? 'N/A'}} {{@$checkinslist->client->last_name ?? ''}}
                                                </a>
                                                <br>
                                                <span>Waiting since {{$checkinslist->formatted_waiting_time ?? 'N/A'}}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center py-3">
                                    <p class="text-muted mb-0">No office check-in at the moment.</p>
                                </div>
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
          </div>
      	</div>  
          
          <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Note Lists With Deadline</h4>
                        <div class="card-header-action">
                            <!-- Additional header actions can be added here -->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Partner Name</th>
                                        <th>Description</th>
                                        <th>Deadline</th>
                                        <th>Created at</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     @if($notesData->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center">No record found</td>
                                    </tr>
                                    @else
                                    @foreach($notesData as $note)
                                    <tr>
                                        <td><a href="{{URL::to('/partners/detail/'.base64_encode(convert_uuencode(@$note->client_id)) )}}">{{ @$note->partner_name == "" ? config('constants.empty') : str_limit(@$note->partner_name, '50', '...') }}</a></td>
                                        <td><?php echo preg_replace('/<\/?p>/', '', $note->description ); ?></td>
                                        <td>{{ $note->formatted_deadline ?? 'N/A' }}</td>
                                        <td>{{ $note->formatted_created_at ?? 'N/A' }}</td>
                                        <td style="white-space: initial;">
                                            <div class="dropdown d-inline">
                                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item has-icon" href="javascript:;" onclick="closeNotesDeadlineAction({{$note->id}})">Close</a>
                                                    <a class="dropdown-item has-icon btn-extend_note_deadline"  data-noteid="{{$note->id}}" data-assignnote="{{$note->description}}" data-deadlinedate="{{$note->formatted_deadline ?? ''}}" href="javascript:;">Extend</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                     @endforeach
                                     @endif
                                    </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <!-- Footer content can be added here -->
                        {!! $notesData->appends(\Request::except('page'))->render() !!}
                    </div>
                </div>
            </div>
        </div>

	</section>
</div>

<!-- Action Popup Modal -->
<div class="modal fade custom_modal" id="extend_note_popup" tabindex="-1" role="dialog" aria-labelledby="create_action_popupLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="padding: 20px;">
            <div class="modal-header" style="padding-bottom: 11px;">
                <h5 class="modal-title assignnn" id="create_action_popupLabel" style="margin: 0 -24px;">Extend Notes Deadline</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <input id="note_id" type="hidden" value="">
            <div id="popover-content">
                <div class="box-header with-border">
                    <div class="form-group row" style="margin-bottom:12px;">
                        <label for="inputEmail3" class="col-sm-3 control-label c6 f13" style="margin-top:8px;">Note</label>
                        <div class="col-sm-9">
                            <textarea id="assignnote" class="form-control" placeholder="Enter a note..."></textarea>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <div class="form-group row note_deadline">
                    <label for="inputSub3" class="col-sm-3 control-label c6 f13" style="margin-top:8px;">
                        Note Deadline
                    </label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control f13" placeholder="dd/mm/yyyy" id="note_deadline" value="<?php echo date('d/m/Y');?>" name="note_deadline">
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="box-footer" style="padding:10px 0;">
                    <div class="row text-center">
                        <div class="col-md-12 text-center">
                            <button class="btn btn-danger" id="extend_deadline">Extend Deadline</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{asset('js/popover.js')}}"></script>
<script>
$(document).ready(function() {
    // Task filter change handler
    $('#task_filter').on('change', function() {
        var filter = $(this).val();
        window.location.href = '{{URL::to('/dashboard')}}?task_filter=' + filter;
    });

    // Update current session duration in real-time
    var sessionStartSeconds = {{$loginStats['current_session_duration'] ?? 0}};
    var sessionStartTime = new Date().getTime() - (sessionStartSeconds * 1000);
    
    function updateSessionDuration() {
        var now = new Date().getTime();
        var elapsed = Math.floor((now - sessionStartTime) / 1000);
        
        var hours = Math.floor(elapsed / 3600);
        var minutes = Math.floor((elapsed % 3600) / 60);
        var seconds = elapsed % 60;
        
        var durationText = '';
        if (hours > 0) {
            durationText = hours + ' hour' + (hours != 1 ? 's' : '');
            if (minutes > 0) {
                durationText += ' ' + minutes + ' minute' + (minutes != 1 ? 's' : '');
            }
        } else if (minutes > 0) {
            durationText = minutes + ' minute' + (minutes != 1 ? 's' : '');
        } else {
            durationText = seconds + ' second' + (seconds != 1 ? 's' : '');
        }
        
        $('#current_session_duration').text(durationText);
    }
    
    // Update every minute
    updateSessionDuration();
    setInterval(updateSessionDuration, 60000);

    if (typeof flatpickr !== 'undefined') {
      flatpickr('#note_deadline', {
        dateFormat: 'd/m/Y',
        defaultDate: 'today',
        allowInput: true
      });
    }

    $(document).on('click', '#extend_deadline', function() {
        $(".popuploader").show();
        let flag = true;
        let error = "";
        $(".custom-error").remove();

        if ($('#assignnote').val() === '') {
            $('.popuploader').hide();
            error = "Note field is required.";
            $('#assignnote').after("<span class='custom-error' role='alert'>" + error + "</span>");
            flag = false;
        }
        if ($('#note_deadline').val() === '') {
            $('.popuploader').hide();
            error = "Note Deadline is required.";
            $('#task_group').after("<span class='custom-error' role='alert'>" + error + "</span>");
            flag = false;
        }

        if (flag) {
            $.ajax({
                type: 'POST',
                url: "{{URL::to('/')}}/admin/extenddeadlinedate",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    note_id: $('#note_id').val(),
                    description: $('#assignnote').val(),
                    note_deadline: $('#note_deadline').val()
                },
                success: function(response) {
                    $('.popuploader').hide();
                    $('#extend_note_popup').modal('hide');
                    location.reload();
                    //var obj = $.parseJSON(response);
                }
            });
        } else {
            $('.popuploader').hide();
        }
    });

    // Handle click event on the action button
    $(document).delegate('.btn-extend_note_deadline', 'click', function(){
        var noteid = $(this).attr("data-noteid");
        var assignnote = $(this).attr("data-assignnote");
        var deadlinedate = $(this).attr("data-deadlinedate");
        $('#note_id').val(noteid);
        $('#assignnote').val(assignnote);
        $('#note_deadline').val(deadlinedate);
        $('#extend_note_popup').modal('show');
    });
});

//close Notes Deadline Action
function closeNotesDeadlineAction( noteid) {
    var conf = confirm('Are you sure, you want to close this note deadline.');
    if(conf){
        if(noteid == '') {
            alert('Please select note to close the deadline.');
            return false;
        } else {
            $('.popuploader').show();
            $.ajax({
                type:'post',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url:"{{URL::to('/')}}/admin/update-note-deadline-completed",
                data:{'id': noteid},
                success:function(resp) {
                    $('.popuploader').hide();
                    location.reload();
                }
            });
        }
    } else{
        $('.popuploader').hide();
    }
}
</script>
@endsection
