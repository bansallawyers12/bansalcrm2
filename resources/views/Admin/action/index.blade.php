@extends('layouts.admin')
@section('title', 'Action')

@section('content')
<style>
.fc-event-container .fc-h-event{cursor:pointer;}
#openassigneview .modal-body ul.navbar-nav li .dropdown-menu{transform: none!important; top:40px!important;}
.sort_col a { color: #212529 !important; font-weight: 700 !important;}
.group_type_section a.active {color:black;}
.select2-container{z-index:100000;width:315px !important;}
.countAction {background: #1f1655;padding: 0px 5px;border-radius: 50%;color: #fff;margin-left: 5px;}
.table:not(.table-sm) thead th {background-color:#fff !important;height: 60px;vertical-align: middle;padding: 0 10px !important;color: #212529;font-size: 15px;}
.card .card-body table.table thead tr th {padding: 0px 10px!important;}
.uniqueClassName {text-align: center;}
.filter-checkbox{/*margin-left: 30px;*/}
.filter-checkbox:first-child{margin-left:0}
/*.table-responsive {width:98% !important; overflow-x: hidden !important;}*/
.card .card-body table.table tbody tr td {padding: 8px 5px!important;}
.table-responsive { overflow: hidden;}
.dataTables_wrapper .dataTables_filter{float: left !important;margin-left: 310px !important;}
.popover .popover-body {width: 500px !important;}
.filter-wrapper div.active {color:blue !important;}
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
                            <h4>Action</h4>
                            <div class="card-header-action">
                            </div>
                            <ul class="nav nav-pills" id="client_tabs" role="tablist">
                                <li class="nav-item is_checked_clientn12">
									<a class="nav-link" id="assigned_by_me"  href="{{URL::to('/action/assigned-by-me')}}">Assigned by me</a>
								</li>

                                <li class="nav-item is_checked_clientn11">
									<a class="nav-link active" id="archived-tab"  href="{{URL::to('/action/completed')}}">Completed</a>
								</li>
                            </ul>
                        </div>

						<div class="card-body">
							<div class="tab-content" id="quotationContent">

                                <div class="row">
                                    <div class="col-md-12">
                                        <?php
                                        if(\Auth::user()->role == 1){
                                            $assigneesCount_All_type = \App\Models\Note::where('type','client')
                                            ->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_call_type = \App\Models\Note::where('task_group','like','Call')
                                            ->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Checklist_type = \App\Models\Note::where('task_group','like','Checklist')
                                            ->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Review_type = \App\Models\Note::where('task_group','like','Review')
                                            ->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Query_type = \App\Models\Note::where('task_group','like','Query')
                                            ->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Urgent_type = \App\Models\Note::where('task_group','like','Urgent')
                                            ->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Personal_Task_type = \App\Models\Note::where('task_group','like','Personal Task')
                                            ->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();
                                          
                                           $assigneesCount_Stage_type = \App\Models\Note::where('task_group','like','stage')
                                            ->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                          $assigneesCount_partner_type = \App\Models\Note::where('task_group','like','partner')
                                            ->where('type','partner')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();


                                        } else {
                                            $assigneesCount_All_type = \App\Models\Note::where('assigned_to',Auth::user()->id)
                                            ->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_call_type = \App\Models\Note::where('task_group','like','Call')
                                            ->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Checklist_type = \App\Models\Note::where('task_group','like','Checklist')
                                            ->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Review_type = \App\Models\Note::where('task_group','like','Review')
                                            ->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Query_type = \App\Models\Note::where('task_group','like','Query')
                                            ->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Urgent_type = \App\Models\Note::where('task_group','like','Urgent')
                                            ->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Personal_Task_type = \App\Models\Note::where('task_group','like','Personal Task')
                                            ->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();
                                        
                                            $assigneesCount_Stage_type = \App\Models\Note::where('task_group','like','stage')
                                            ->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();
                                        
                                          
                                          $assigneesCount_partner_type = \App\Models\Note::where('task_group','like','partner')
                                            ->where('assigned_to',Auth::user()->id)->where('type','partner')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                        } ?>
                                        <div class="filter-wrapper">
                                            <div class="btn btn-light filter-checkbox active" data-val="All"> All <span class="countAction">{{ $assigneesCount_All_type }}</span></div>
                                            <div class="btn btn-light filter-checkbox" data-val="Call"><i class="fa fa-phone" aria-hidden="true"></i> Call <span class="countAction">{{ $assigneesCount_call_type }}</span></div>
                                            <div class="btn btn-light filter-checkbox" data-val="Checklist"> <i class="fa fa-bars" aria-hidden="true"></i> Checklist <span class="countAction">{{ $assigneesCount_Checklist_type }}</span></div>
                                            <div class="btn btn-light filter-checkbox" data-val="Review"> <i class="fa fa-check" aria-hidden="true"></i> Review <span class="countAction">{{ $assigneesCount_Review_type }}</span></div>
                                            <div class="btn btn-light filter-checkbox" data-val="Query"><i class="fa fa-question" aria-hidden="true"></i> Query <span class="countAction">{{ $assigneesCount_Query_type }}</span></div>
                                            <div class="btn btn-light filter-checkbox" data-val="Urgent"> <i class="fa fa-flag" aria-hidden="true"></i> Urgent <span class="countAction">{{ $assigneesCount_Urgent_type }}</span></div>
                                            <div class="btn btn-light filter-checkbox" data-val="Personal Task"> <i class="fa fa-tasks" aria-hidden="true"></i> Personal Task <span class="countAction">{{ $assigneesCount_Personal_Task_type }}</span></div>
                                          
                                          <div class="btn btn-light filter-checkbox" data-val="stage"> <i class="fa fa-flag" aria-hidden="true"></i> Stage <span class="countAction">{{ $assigneesCount_Stage_type }}</span></div>
                                          
                                          <div class="btn btn-light filter-checkbox" style="margin-top:5px;" data-val="partner"> <i class="fa fa-flag" aria-hidden="true"></i> Partner <span class="countAction">{{ $assigneesCount_partner_type }}</span></div>



                                            <button type="button" class="btn btn-primary btn-block add_my_task" data-container="body" data-role="popover" data-placement="bottom" data-html="true" data-content="<div id=&quot;popover-content11&quot;>
                                                <h4 class=&quot;text-center&quot;>Add Action</h4>
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
                                                        <input type=&quot;date&quot; class=&quot;form-control f13&quot; placeholder=&quot;yyyy-mm-dd&quot; id=&quot;popoverdatetime&quot; value=&quot;<?php echo date('Y-m-d');?>&quot;name=&quot;popoverdate&quot;>
                                                    </div>
                                                    <div class=&quot;clearfix&quot;></div>
                                                </div>
                                            </div>

                                            <input id=&quot;task_group&quot;  name=&quot;task_group&quot;  type=&quot;hidden&quot; value=&quot;Personal Task&quot;>


                                            <form class=&quot;form-inline mr-auto&quot;>
                                                <label for=&quot;inputSub3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Select Client</label>

                                                <div class=&quot;search-element&quot; style=&quot;margin-left: 5px;width:70%;&quot;>
                                                    <select id=&quot;assign_client_id&quot;  class=&quot;form-control js-data-example-ajaxccsearch__addmytask&quot; type=&quot;search&quot; placeholder=&quot;Search&quot; aria-label=&quot;Search&quot; data-width=&quot;200&quot; style=&quot;width:200px&quot;></select>
                                                    <button class=&quot;btn&quot; type=&quot;submit&quot;><i class=&quot;fas fa-search&quot;></i></button>
                                                </div>
                                            </form>

                                            <div class=&quot;box-footer&quot; style=&quot;padding:10px 0&quot;>
                                            <div class=&quot;row&quot;>
                                                <input type=&quot;hidden&quot; value=&quot;&quot; id=&quot;popoverrealdate&quot; name=&quot;popoverrealdate&quot; />
                                            </div>
                                            <div class=&quot;row text-center&quot;>
                                                <div class=&quot;col-md-12 text-center&quot;>
                                                <button  class=&quot;btn btn-info&quot; id=&quot;add_my_task&quot;>Add Action</button>
                                                </div>
                                            </div>
                                    </div>" data-original-title="" title="" style="width: 105px;display: inline;margin-left: 10px;margin-top:5px;">Add Action</button>


                                        </div>


                                    </div>
                                </div>

                                <div class="tab-pane fade show active" id="active_quotation" role="tabpanel" aria-labelledby="active_quotation-tab">
									<div class="table-responsive common_table">
									    <!-- @if ($message = Session::get('success'))
										<div class="alert alert-success">
											<p>{{ $message }}</p>
										</div>
									    @endif   -->

                                        <table class="table table-bordered yajra-datatable">
                                            <thead>
                                                <tr>
                                                    <th>Sno</th>
                                                    <th>Done</th>
                                                    <th>Assigner Name</th>
                                                    <th>Client/Partner Reference</th>
                                                    <th>Assign Date</th>
                                                    <th>Type</th>
                                                    <th>Note</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>


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
            <div class="modal-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff;">
                <h5 class="modal-title" id="completeActionModalLabel">
                    <i class="fa fa-check-circle"></i> Complete Action
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="completeActionForm">
                    <input type="hidden" id="complete_action_id" name="action_id">
                    <input type="hidden" id="complete_client_id" name="client_id">
                    
                    <div class="mb-3">
                        <label class="text-muted small">Client/Partner</label>
                        <p id="complete-action-client" style="color: #4b5563; margin: 0; font-weight: 500;">
                            <i class="fa fa-user"></i> <span></span>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="completion_message" class="form-label">
                            Completion Message <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="completion_message" name="completion_message" rows="4" 
                                  placeholder="Enter completion message..." required></textarea>
                        <small class="text-muted">This message will be recorded in the activities section.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="submitCompleteAction">
                    <i class="fa fa-check"></i> Complete Action
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')


<script src="{{asset('js/popover.js')}}"></script>

<script type="text/javascript">
$(function () {

    var table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('action.list') }}",
        columns: [
            {sWidth: '40px',className: "uniqueClassName", data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {sWidth: '50px',className: "uniqueClassName", data: 'done_task', name: 'done_task',orderable: false,searchable: false},
            {sWidth: '120px',data: 'assigner_name', name: 'assigner_name'},
            {sWidth: '130px',data: 'client_reference', name: 'client_reference'},
            {sWidth: '100px',data: 'assign_date', name: 'assign_date'},
            {sWidth: '80px',data: 'task_group', name: 'task_group'},
            {data: 'note_description', name: 'note_description'},
            {sWidth: '120px',data: 'action',name: 'action',orderable: false,searchable: false},
        ],
       /* "fnDrawCallback": function (oSettings) {
            $('.yajra-datatable tbody tr').each(function () {
                var sTitle;
                var nTds = $('td', this);
                var s0 = $(nTds[6]).text();

                sTitle = s0;

                this.setAttribute('rel', 'tooltip');
                this.setAttribute('title', sTitle);
                //console.log(this);
                //console.log($(this));
                $(this).tooltip({
                    html: true
                });
            });
        },*/

        "fnDrawCallback": function() {
            // Only initialize popovers that aren't already initialized
            // Support both data-toggle (legacy) and data-bs-toggle (Bootstrap 5)
            $('[data-role="popover"], [data-toggle="popover"], [data-bs-toggle="popover"]').each(function() {
                var $el = $(this);
                // Check if Bootstrap 5 instance exists or jQuery data exists
                var bsInstance = window.bootstrap && window.bootstrap.Popover ? window.bootstrap.Popover.getInstance(this) : null;
                var jqData = $el.data('bs.popover');
                
                if (!bsInstance && !jqData) {
                    try {
                        // Initialize with jQuery bridge (which creates Bootstrap 5 instance)
                        $el.popover({
                            html: true,
                            sanitize: false,
                            placement: $el.attr('data-placement') || $el.attr('data-bs-placement') || 'auto',
                            container: $el.attr('data-container') || $el.attr('data-bs-container') || false
                        });
                    } catch(e) {
                        console.warn('Popover initialization error:', e);
                    }
                }
            });
           // $('[data-bs-toggle="tooltip"]').tooltip();
        },
        "bAutoWidth": false
    });

    //filter record on bais of task group
    /*$('.filter-checkbox').on('change', function(e){
        var searchTerms = []
        $.each($('.filter-checkbox'), function(i,elem){
            if($(elem).prop('checked')){
                searchTerms.push("^" + $(this).val() + "$")
            }
        })
        table.column(5).search(searchTerms.join('|'), true, false, true).draw();
    });*/
    
    //filter record on bais of task group
    $('.filter-checkbox').on('click', function(e){
        var searchTerms = []
        if($(this).attr('data-val') == 'All'){
            searchTerms = ["Call", "Checklist","Review","Query","Urgent","Personal Task","stage","partner"];
        } else {
            searchTerms.push("^" + $(this).attr('data-val') + "$")
        }
        $(".filter-checkbox").removeClass("active");
        $(this).addClass("active");
        //console.log(searchTerms);
        table.column(5).search(searchTerms.join('|'), true, false, true).draw();
    });


    // Delete record
    $('.yajra-datatable').on('click','.deleteNote',function(e){
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var url = $(this).data('remote');

        var deleteConfirm = confirm("Are you sure?");
        if (deleteConfirm == true) {
            $.ajax({
                url: url,
                type: 'DELETE',
                dataType: 'json',
                data: {method: '_DELETE', submit: true}
            }).always(function (data) {
                $('.yajra-datatable').DataTable().draw(false);
            });
        }
    });
});

</script>

<script>
jQuery(document).ready(function($){

    //$('[data-bs-toggle="tooltip"]').tooltip();

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
        var folowDateArr = followupdate_id.split(" ");
        var finalDate = folowDateArr[0];
        
        // Check if popover is already initialized (Bootstrap 5 or jQuery bridge)
        var bsInstance = window.bootstrap && window.bootstrap.Popover ? window.bootstrap.Popover.getInstance(this) : null;
        var jqData = $btn.data('bs.popover');
        
        if (!bsInstance && !jqData) {
            // Initialize popover with jQuery bridge
            $btn.popover({
                html: true,
                sanitize: false,
                placement: $btn.attr('data-placement') || $btn.attr('data-bs-placement') || 'auto',
                container: $btn.attr('data-container') || $btn.attr('data-bs-container') || 'body'
            });
        }
        
        // Show the popover using jQuery bridge method
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
        var folowDateArr = followupdate_id.split(" ");
        var finalDate = folowDateArr[0];
        
        // Check if popover is already initialized (Bootstrap 5 or jQuery bridge)
        var bsInstance = window.bootstrap && window.bootstrap.Popover ? window.bootstrap.Popover.getInstance(this) : null;
        var jqData = $btn.data('bs.popover');
        
        if (!bsInstance && !jqData) {
            // Initialize popover with jQuery bridge
            $btn.popover({
                html: true,
                sanitize: false,
                placement: $btn.attr('data-placement') || $btn.attr('data-bs-placement') || 'left',
                container: $btn.attr('data-container') || $btn.attr('data-bs-container') || 'body'
            });
        }
        
        // Show the popover using jQuery bridge method
        $btn.popover('show');
        
        // Wait for popover to be shown, then set form values
        // Use Bootstrap 5 event or jQuery event
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
                    //var obj = $.parseJSON(response);
                    //location.reload();
                    $('.yajra-datatable').DataTable().draw(false);
                }
			});
        }
	});

    //Function is used for complete the task
	$(document).delegate('.complete_task', 'click', function(e){
		e.preventDefault();
		var row_id = $(this).attr('data-id');
        if(row_id !=""){
            // Get row data from DataTable
            var table = $('.yajra-datatable').DataTable();
            var rowData = table.row($(this).closest('tr')).data();
            
            // Get client ID and name from the row
            var clientId = '';
            var clientName = 'N/A';
            
            // Try to extract client info from the row
            if (rowData && rowData.client_reference) {
                // Extract client name from the HTML (first line before <br>)
                var tempDiv = $('<div>').html(rowData.client_reference);
                clientName = tempDiv.text().split('\n')[0].trim() || 'N/A';
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
                _token: '{{ csrf_token() }}',
                id: actionId,
                client_id: clientId,
                completion_message: message
            },
            success: function(response) {
                // Re-enable button
                $('#submitCompleteAction').prop('disabled', false).html('<i class="fa fa-check"></i> Complete Action');
                
                // Check response status
                if (response && response.status) {
                    // Close modal
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        var modalElement = document.getElementById('completeActionModal');
                        var modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                        }
                    } else {
                        $('#completeActionModal').modal('hide');
                    }
                    
                    // Refresh DataTable
                    $('.yajra-datatable').DataTable().draw(false);
                    
                    // Show success message
                    if (typeof iziToast !== 'undefined') {
                        iziToast.success({
                            title: 'Success',
                            message: response.message || 'Action completed successfully!'
                        });
                    } else {
                        alert(response.message || 'Action completed successfully!');
                    }
                } else {
                    // Handle error response
                    alert(response.message || 'Failed to complete action. Please try again.');
                }
            },
            error: function(xhr) {
                // Re-enable button
                $('#submitCompleteAction').prop('disabled', false).html('<i class="fa fa-check"></i> Complete Action');
                
                var errorMsg = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            }
        });
    });

    //re-assign task or update task
    $(document).delegate('#assignUser','click', function(){
		$(".popuploader").show();
		var flag = true;
		var error = "";
		$(".custom-error").remove();
		
		// Find the visible popover and get values from within it
		var $popover = $('.popover:visible').last();
		var $remCat, $assignNote, $taskGroup, $assignNoteId, $assignClientId, $popoverDateTime;
		
		if ($popover.length) {
			// Get form elements from within the popover
			$remCat = $popover.find('#rem_cat');
			$assignNote = $popover.find('#assignnote');
			$taskGroup = $popover.find('#task_group');
			$assignNoteId = $popover.find('#assign_note_id');
			$assignClientId = $popover.find('#assign_client_id');
			$popoverDateTime = $popover.find('#popoverdatetime');
		} else {
			// Fallback to global selectors
			$remCat = $('#rem_cat');
			$assignNote = $('#assignnote');
			$taskGroup = $('#task_group');
			$assignNoteId = $('#assign_note_id');
			$assignClientId = $('#assign_client_id');
			$popoverDateTime = $('#popoverdatetime');
		}
		
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
			// Get values from the correct form elements
			var noteId = $assignNoteId.val();
			var noteDescription = $assignNote.val();
			var clientId = $assignClientId.val();
			var followupDateTime = $popoverDateTime.val();
			var assigneeName = $remCat.find(':selected').text();
			var remCat = $remCat.val();
			var taskGroup = $taskGroup.val();
			
			if (!noteId) {
				$('.popuploader').hide();
				alert('Note ID is missing. Please try again.');
				return;
			}
			
			$.ajax({
				type:'post',
                url:"{{URL::to('/')}}/clients/reassignfollowup/store",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
					note_id: noteId,
					note_type: 'follow_up',
					description: noteDescription,
					client_id: clientId,
					followup_datetime: followupDateTime,
					assignee_name: assigneeName,
					rem_cat: remCat,
					task_group: taskGroup
				},
                success: function(response){
                    console.log(response);
                    $('.popuploader').hide();
                    var obj = $.parseJSON(response);
                    if(obj.success){
                        // Hide all popovers
                        $("[data-role=popover], [data-toggle=popover], [data-bs-toggle=popover]").each(function(){
                            $(this).popover('hide');
                        });
                        //location.reload();
                        $('.yajra-datatable').DataTable().draw(false);
                        if(typeof getallactivities === 'function') getallactivities();
                        if(typeof getallnotes === 'function') getallnotes();
                    } else{
                        alert(obj.message || 'An error occurred');
                        //location.reload();
                        $('.yajra-datatable').DataTable().draw(false);
                    }
                },
				error: function(xhr, status, error) {
					$('.popuploader').hide();
					console.error('AJAX Error:', error);
					alert('Error: ' + (xhr.responseJSON?.message || error || 'Failed to reassign task'));
				}
			});
		}else{
			$(".popuploader").hide();
		}
	});

    //update task
    $(document).delegate('#updateTask','click', function(){
		$(".popuploader").show();
		var flag = true;
		var error ="";
		$(".custom-error").remove();

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
                url:"{{URL::to('/')}}/clients/updatefollowup/store",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {note_id:$('#assign_note_id').val(),note_type:'follow_up',description:$('#assignnote').val(),client_id:$('#assign_client_id').val(),followup_datetime:$('#popoverdatetime').val(),assignee_name:$('#rem_cat :selected').text(),rem_cat:$('#rem_cat option:selected').val(),task_group:$('#task_group option:selected').val()},
                success: function(response){
                    console.log(response);
                    $('.popuploader').hide();
                    var obj = $.parseJSON(response);
                    if(obj.success){
                        $("[data-role=popover]").each(function(){
                            (($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false  // fix for BS 3.3.6
                        });
                        //location.reload();
                        $('.yajra-datatable').DataTable().draw(false);
                        getallactivities();
                        getallnotes();
                    } else{
                        alert(obj.message);
                        $('.yajra-datatable').DataTable().draw(false);
                        //location.reload();
                    }
                }
			});
		}else{
			$("#loader").hide();
		}
	});

    //Add Personal Task
    $(document).delegate('#add_my_task','click', function(){
		$(".popuploader").show();
		var flag = true;
		var error ="";
		$(".custom-error").remove();
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
        if(flag){
			// Debug: Log the client_id value before sending
			console.log('Client ID value:', $('#assign_client_id').val());
			
			$.ajax({
				type:'post',
                url:"{{URL::to('/')}}/clients/personalfollowup/store",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {note_type:'follow_up',description:$('#assignnote').val(),client_id:$('#assign_client_id').val(),followup_datetime:$('#popoverdatetime').val(),assignee_name:$('#rem_cat :selected').text(),rem_cat:$('#rem_cat option:selected').val(),task_group:$('#task_group').val()},
                success: function(response){
                    //console.log(response);
                    $('.popuploader').hide();
                    var obj = $.parseJSON(response);
                    if(obj.success){
                        $("[data-role=popover]").each(function(){
                            (($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false  // fix for BS 3.3.6
                        });
                        //location.reload();
                        $('.yajra-datatable').DataTable().draw(false);
                        getallactivities();
                        getallnotes();
                    } else{
                        alert(obj.message);
                        $('.yajra-datatable').DataTable().draw(false);
                        //location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    $('.popuploader').hide();
                    console.error('AJAX Error:', error);
                    console.error('Response:', xhr.responseText);
                    var errorMessage = 'An error occurred. Please try again.';
                    try {
                        var errorObj = $.parseJSON(xhr.responseText);
                        if(errorObj.message) {
                            errorMessage = errorObj.message;
                        }
                    } catch(e) {
                        errorMessage = xhr.responseText || error;
                    }
                    alert(errorMessage);
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

	// Stub functions to prevent errors - these functions are called but not needed on this page
	function getallactivities(){
		// Function stub - activities are not displayed on the action index page
		// This function is defined in detail pages but called here after task operations
	}

	function getallnotes(){
		// Function stub - notes are not displayed on the action index page
		// This function is defined in detail pages but called here after task operations
	}
});
</script>

@push('tinymce-scripts')
@include('partials.tinymce')
@endpush

@endsection
