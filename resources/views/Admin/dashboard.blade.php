@extends('layouts.admin')
@section('title', 'Admin Dashboard')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		


        <div class="row">

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-4">
				<div class="card dash_card">
				    <?php

					if(Auth::user()->role == 1){
						$countfollowup = \App\Note::select('id')->whereDate('followup_date', date('Y-m-d'))->count();
					}else{
						$countfollowup = \App\Note::whereDate('followup_date', date('Y-m-d'))->where('assigned_to', Auth::user()->id)->count();
					}
                    
                    ?>
					<div class="card-statistic-4">
						<div class="align-items-center justify-content-between">
							<div class="row ">
								<div class="col-lg-12 col-md-12">
										<div class="card-content">
										<h5 class="font-14">Today Followup</h5>
										<h2 class="mb-3 font-18">{{$countfollowup}}</h2>
										<p class="mb-0"><span class="col-green">{{$countfollowup}}</span> <a href="{{URL::to('/admin/followup-dates/')}}">click here</a></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>


            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-4">
                <div class="card dash_card">
                    <div class="card-statistic-4">
                        <div class="card-content cus_card_content">
                            <div class="card_header">
                                <h5 class="font-14">My Appointment</h5>
                                <!--<a href="javascript:;" data-toggle="modal" data-target=".add_appiontment" class="btn btn-outline-primary btn-sm add_btn"><i class="fa fa-plus"></i> Add</a>-->
                            </div>
                            <div class="card_body">
                                <?php
                                //$atotalData = \App\Appointment::whereDate('date', date('Y-m-d'))->count(); dd($atotalData );
                                $atotalData = \App\Appointment::whereDate('date', date('Y-m-d'))->select('date','time','client_id','title')->orderby('created_at','Desc')->get();
                                //echo "$$$".count($atotalData);die;
                                ?>
                                @if(@count($atotalData) !== 0)
                                <div class="appli_remind">
                                <?php
                                //foreach(\App\Appointment::whereDate('date', date('Y-m-d'))->orderby('created_at','Desc')->get() as $alist)
                                foreach($atotalData as $alist)
                                {
                                    $day = date('d', strtotime($alist->date));
                                    $time = date('h:i A', strtotime($alist->time));
                                    $week = date('D', strtotime($alist->date));
                                    $month = date('M', strtotime($alist->date));
                                    $year = date('Y', strtotime($alist->date));
                                    $admin = \App\Admin::where('id', $alist->client_id)->select('id','first_name')->first();
                                    ?>
                                    <div class="appli_column">
                                        <div class="date">{{$day}}<span>{{$week}}</span>
                                        </div>
                                        <div class="appli_content">
                                            <a href="{{URL::to('admin/clients/detail/')}}/{{base64_encode(convert_uuencode(@$admin->id))}}">{{@$admin->first_name}}</a>
                                            <div class="event_end"><span></span> - {{@$alist->title}}</div>
                                            <span class="end_date">{{$month}} {{$year}} {{$time}}</span>
                                        </div>
                                    </div>
                                    </tr>
                                <?php
                                }
                                ?>
                                </div>
                                @else
                                <p class="text-muted">All Clear! No appointments.</p>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-4">
                <div class="card dash_card">
                    <div class="card-statistic-4">
                        <div class="card-content cus_card_content">
                            <div class="card_header">
                                <h5 class="font-14">My Tasks for </h5>
                                <!--<a href="javascript:;" id="create_task" class="btn btn-outline-primary btn-sm add_btn"><i class="fa fa-plus"></i> Add</a>-->
                            </div>
                            <div class="card_body">
                                <?php
                                if(Auth::user()->role == 1){
                                    //echo date('Y-m-d');
                                    $liststodo = \App\Task::whereDate('due_date', date('Y-m-d'))->select('id','user_id','status','due_date','due_time')->orderby('created_at','Desc')->get();
                                }else{
                                    $liststodo = \App\Task::whereDate('due_date', date('Y-m-d'))
                                    ->where(function($query){
                                        $query->where('assignee', Auth::user()->id)
                                              ->orWhere('followers', Auth::user()->id);
                                    })->select('id','user_id','status','due_date','due_time')->orderby('created_at','Desc')->get();
                                } //dd($liststodo);
                                ?>
                                @if(@$totalData !== 0)
                                <div class="taskdata_list">
                                    <div class="table-responsive">
                                        <table id="my-datatable" class="table-2 table text_wrap">
                                            <tbody class="taskdata">
                                            <?php
                                            foreach($liststodo as $alist)
                                            { //dd($alist);
                                                $admin = \App\Admin::where('id', $alist->user_id)->select('last_name','first_name')->first();//dd($admin);
                                                if($admin){
                                                    $first_name = $admin->first_name ?? 'N/A';
                                                    $last_name = $admin->last_name ?? 'N/A';
                                                    $full_name = $first_name.' '.$last_name;
                                                } else {
                                                    $full_name = 'N/A';
                                                } ?>
                                                <tr class="opentaskview" style="cursor:pointer;" id="{{$alist->id}}">
                                                    <td><?php if($alist->status == 1 || $alist->status == 2){ echo "<span class='check'><i class='fa fa-check'></i></span>"; } else{ echo "<span class='round'></span>"; } ?></td>
                                                    <td>{{$full_name}}<br><i class="fa fa-clock"></i>{{date('d/m/Y h:i A', strtotime($alist->due_date.' '.$alist->due_time))}} </td>
                                                    <td>
                                                    <?php
                                                    if($alist->status == 1){
                                                        echo '<span style="color: rgb(113, 204, 83); width: 84px;">Completed</span>';
                                                    }else if($alist->status == 2){
                                                        echo '<span style="color: rgb(255, 173, 0); width: 84px;">In Progress</span>';
                                                    }else if($alist->status == 3){
                                                        echo '<span style="color: rgb(156, 156, 156); width: 84px;">On Review</span>';
                                                    }else{
                                                        echo '<span style="color: rgb(255, 173, 0); width: 84px;">Todo</span>';
                                                    }
                                                    ?></td>
                                                </tr>
                                                <?php
                                            } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @else
                                <p class="text-muted">No tasks at the moment.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-4">
                <div class="card dash_card">
                    <div class="card-statistic-4">
                        <?php
                        $checkins 		= \App\CheckinLog::where('id', '!=', '')->where('status', '=', '0')->select('id','client_id','created_at');
                        $checkinstotalData 	= $checkins->count();
                        $checkinslists		= 		$checkins->get()
                        ?>
                        <div class="card-content cus_card_content">
                            <div class="card_header">
                                <h5 class="font-14">Check-In Queue</h5>
                            </div>
                            <div class="card_body">
                            @if($checkinstotalData !== 0)
                                <table>
                                    <tbody>
                                    @foreach($checkinslists as $checkinslist)
                                        <?php
                                        $client = \App\Admin::where('role', '=', '7')->where('id', '=', $checkinslist->client_id)->select('last_name','first_name')->first();
                                        ?>
                                        <tr>
                                            <td><a id="{{@$checkinslist->id}}" class="opencheckindetail" href="javascript:;">{{@$client->first_name}} {{@$client->last_name}} </a>
                                            <br>
                                            <span>Waiting since {{date('h:i A', strtotime($checkinslist->created_at))}}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted">No office check-in at the moment.</p>
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
                                    <?php
                                    $note_client = \App\Partner::select('id','partner_name')->where('id', $note->client_id)->first();
                                    ?>
                                    <tr>
                                        <td><a href="{{URL::to('/admin/partners/detail/'.base64_encode(convert_uuencode(@$note_client->id)) )}}">{{ @$note_client->partner_name == "" ? config('constants.empty') : str_limit(@$note_client->partner_name, '50', '...') }}</a></td>
                                        <td><?php echo preg_replace('/<\/?p>/', '', $note->description ); ?></td>
                                        <td>{{ date('d/m/Y',strtotime($note->note_deadline)) }}</td>
                                        <td>{{ date('d/m/Y',strtotime($note->created_at)) }}</td>
                                        <td style="white-space: initial;">
                                            <div class="dropdown d-inline">
                                                <button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item has-icon" href="javascript:;" onclick="closeNotesDeadlineAction({{$note->id}})">Close</a>
                                                    <a class="dropdown-item has-icon btn-extend_note_deadline"  data-noteid="{{$note->id}}" data-assignnote="{{$note->description}}" data-deadlinedate="<?php echo date('d/m/Y',strtotime($note->note_deadline));?>" href="javascript:;">Extend</a>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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

<link rel="stylesheet" href="{{asset('css/bootstrap-datepicker.min.css')}}">
@endsection

@section('scripts')
<script src="{{asset('js/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('js/popover.js')}}"></script>
<script>
$(document).ready(function() {
    $('#note_deadline').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

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

