@extends('layouts.admin')
@section('title', 'Activities')

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
                            <h4>Activities</h4>
                            <div class="card-header-action">
                            </div>
                            <ul class="nav nav-pills" id="client_tabs" role="tablist">
                                <li class="nav-item is_checked_clientn12">
									<a class="nav-link" id="assigned_by_me"  href="{{URL::to('/admin/assigned_by_me')}}">Assigned by me</a>
								</li>

                                <li class="nav-item is_checked_clientn11">
									<a class="nav-link active" id="archived-tab"  href="{{URL::to('/admin/activities_completed')}}">Completed</a>
								</li>
                            </ul>
                        </div>

						<div class="card-body">
							<div class="tab-content" id="quotationContent">

                                <div class="row">
                                    <div class="col-md-12">
                                        <?php
                                        if(\Auth::user()->role == 1){
                                            $assigneesCount_All_type = \App\Note::where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_call_type = \App\Note::where('task_group','like','Call')
                                            ->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Checklist_type = \App\Note::where('task_group','like','Checklist')
                                            ->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Review_type = \App\Note::where('task_group','like','Review')
                                            ->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Query_type = \App\Note::where('task_group','like','Query')
                                            ->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Urgent_type = \App\Note::where('task_group','like','Urgent')
                                            ->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Personal_Task_type = \App\Note::where('task_group','like','Personal Task')
                                            ->where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                        } else {
                                            $assigneesCount_All_type = \App\Note::where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_call_type = \App\Note::where('task_group','like','Call')
                                            ->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Checklist_type = \App\Note::where('task_group','like','Checklist')
                                            ->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Review_type = \App\Note::where('task_group','like','Review')
                                            ->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Query_type = \App\Note::where('task_group','like','Query')
                                            ->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Urgent_type = \App\Note::where('task_group','like','Urgent')
                                            ->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();

                                            $assigneesCount_Personal_Task_type = \App\Note::where('task_group','like','Personal Task')
                                            ->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->orderBy('created_at', 'desc')->count();
                                        } ?>
                                        <div class="filter-wrapper">
                                            <input type="checkbox" class="filter-checkbox" value="Call"/> <div class="btn btn-light"><i class="fa fa-phone" aria-hidden="true"></i> Call <span class="countAction">{{ $assigneesCount_call_type }}</span></div>
                                            <input type="checkbox" class="filter-checkbox" value="Checklist"/> <div class="btn btn-light"> <i class="fa fa-bars" aria-hidden="true"></i> Checklist <span class="countAction">{{ $assigneesCount_Checklist_type }}</span></div>
                                            <input type="checkbox" class="filter-checkbox" value="Review"/> <div class="btn btn-light"> <i class="fa fa-check" aria-hidden="true"></i> Review <span class="countAction">{{ $assigneesCount_Review_type }}</span></div>
                                            <input type="checkbox" class="filter-checkbox" value="Query"/>  <div class="btn btn-light"><i class="fa fa-question" aria-hidden="true"></i> Query <span class="countAction">{{ $assigneesCount_Query_type }}</span></div>
                                            <input type="checkbox" class="filter-checkbox" value="Urgent"/> <div class="btn btn-light"> <i class="fa fa-flag" aria-hidden="true"></i> Urgent <span class="countAction">{{ $assigneesCount_Urgent_type }}</span></div>
                                            <input type="checkbox" class="filter-checkbox" value="Personal Task"/> <div class="btn btn-light"> <i class="fa fa-tasks" aria-hidden="true"></i> Personal Task <span class="countAction">{{ $assigneesCount_Personal_Task_type }}</span></div>

                                            <button type="button" class="btn btn-primary btn-block add_my_task" data-container="body" data-role="popover" data-placement="bottom" data-html="true" data-content="<div id=&quot;popover-content11&quot;>
                                                <h4 class=&quot;text-center&quot;>Add My Task</h4>
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
                                                <button  class=&quot;btn btn-info&quot; id=&quot;add_my_task&quot;>Add My Task</button>
                                                </div>
                                            </div>
                                    </div>" data-original-title="" title="" style="width: 105px;display: inline;margin-left: 10px">Add My Task</button>


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
                                                    <th>Client Reference</th>
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
@endsection
@section('scripts')


<script src="{{URL::to('/')}}/js/popover.js"></script>
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>-->
<script type="text/javascript">
$(function () {

    var table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('activities1.list') }}",
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
            $('[data-toggle="popover"]').popover({
                html: true,
                sanitize: false,
                outsideClick:true
            });
           // $('[data-toggle="tooltip"]').tooltip();
        },

        /*"fnDrawCallback": function () {
            $('.update_task').popover({
                "html": true,
                trigger: 'manual',
                placement: 'left',
                "content": function () {
                    return "<div>Popover content</div>";
                }
            })
        },*/

        "bAutoWidth": false
    });

    $('.filter-checkbox').on('change', function(e){
        var searchTerms = []
        $.each($('.filter-checkbox'), function(i,elem){
            if($(elem).prop('checked')){
                searchTerms.push("^" + $(this).val() + "$")
            }
        })
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

    //$('[data-toggle="tooltip"]').tooltip();

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

    /*$('.update_task').popover({
        trigger: 'manual',
        placement: 'top',
        html: true,
        title: function() {
            return 'User Info: ' + $(this).data('title') + ' <a href="#" class="close" data-dismiss="alert">×</a>'
        },
        content: function() {
            return '<div class="media"><a href="#" class="pull-left"><img src=".." class="media-object" alt="Sample Image"></a><div class="media-body"><h4 class="media-heading">' + $(this).data('name') + '</h4><p>Excellent Bootstrap popover! I really love it.</p></div></div>'
        }
    });*/


    //update task
    $(document).delegate('.update_task', 'click', function(){
        var note_id = $(this).attr('data-noteid'); //alert(note_id);
        $('#assignnote').val(note_id);

        var task_id = $(this).attr('data-taskid'); //alert(task_id);
        $('#assign_note_id').val(task_id);

        var taskgroup_id = $(this).attr('data-taskgroupid'); //alert(taskgroup_id);
        $('#task_group').val(taskgroup_id);

        var followupdate_id = $(this).attr('data-followupdate'); //alert(followupdate_id);
        var folowDateArr = followupdate_id.split(" ");
        var newDate = folowDateArr[0].split("-");
        var finalDate = newDate[1]+"/"+newDate[2]+"/"+newDate[0]; //alert(finalDate);
        //2024-02-16 00:00:00
        $('#popoverdatetime').val(finalDate);
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
                    //var obj = $.parseJSON(response);
                    //location.reload();
                    $('.yajra-datatable').DataTable().draw(false);
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
                    //var obj = $.parseJSON(response);
                    //location.reload();
                    $('.yajra-datatable').DataTable().draw(false);
                }
			});
        }
	});

    //re-assign task or update task
    $(document).delegate('#assignUser','click', function(){
		$(".popuploader").show();
		var flag = true;
		var error = "";
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
                url:"{{URL::to('/')}}/admin/clients/reassignfollowup/store",
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
                        location.reload();
                        getallactivities();
                        getallnotes();
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
                url:"{{URL::to('/')}}/admin/clients/updatefollowup/store",
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
                        location.reload();
                        getallactivities();
                        getallnotes();
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
			$.ajax({
				type:'post',
                url:"{{URL::to('/')}}/admin/clients/personalfollowup/store",
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
