@extends('layouts.admin')
@section('title', 'Admin Dashboard')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="row">

			<!--<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-4">
				<div class="card dash_card">
					<div class="card-statistic-4">
						<div class="align-items-center justify-content-between">
							<div class="row ">
								<div class="col-lg-12 col-md-12">
									<div class="card-content">

										<h5 class="font-14">Total Enquiries</h5>
										<h2 class="mb-3 font-18">{{-- $countenquiries --}}</h2>
										<p class="mb-0"><span class="col-green">{{-- $countenquiries --}} New</span> added this month</p>
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
						<div class="align-items-center justify-content-between">
							<div class="row ">
								<div class="col-lg-12 col-md-12">
									<div class="card-content">
                                        <?php
                                        //$countleads = \App\Admin::where('is_archived',0)->where('role', '=', '7')->where('type','lead')->whereMonth('created_at', \Carbon\Carbon::now()->month)->count();
                                        //$countallleads = \App\Admin::where('is_archived',0)->where('role', '=', '7')->where('type','lead')->count();
                                        ?>
										<h5 class="font-14">Total Leads</h5>
										<?php
										/*$roles = \App\UserRole::find(Auth::user()->role);
                                        $newarray = json_decode($roles->module_access);
                                        $module_access = (array) $newarray; //dd($module_access);*/
                                        //if(array_key_exists('20',  $module_access)) {
                                        ?>
										<h2 class="mb-3 font-18">{{-- $countallleads --}}</h2>
										<?php //} ?>
										<p class="mb-0"><span class="col-green">{{-- $countleads --}} New</span> added this month</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>-->

			<!--<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-4">
				<div class="card dash_card">-->
				    <?php
					/*if(Auth::user()->role == 1){
						$countfollowup = \App\Note::whereDate('followup_date', date('Y-m-d'))->count();
					}else{
						$countfollowup = \App\Note::whereDate('followup_date', date('Y-m-d'))->where('assigned_to', Auth::user()->id)->count();
					}*/
                    ?>
					<!--<div class="card-statistic-4">
						<div class="align-items-center justify-content-between">
							<div class="row ">
								<div class="col-lg-12 col-md-12">
										<div class="card-content">
										<h5 class="font-14">Today Followup</h5>
										<h2 class="mb-3 font-18">{{-- $countfollowup --}}</h2>
										<p class="mb-0"><span class="col-green">{{-- $countfollowup --}}</span> <a href="{{URL::to('/admin/followup-dates/')}}">click here</a></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>-->

			<!--<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-4">
				<div class="card dash_card">
					<div class="card-statistic-4">
						<div class="align-items-center justify-content-between">
							<div class="row ">
								<div class="col-lg-12 col-md-12">
									<?php
                                    //$client = \App\Admin::where('is_archived',0)->where('type','client')->where('role', '=', '7')->count();
									//dd($client);
                                    ?>
									<div class="card-content">
										<h5 class="font-14">Total Clients</h5>
										<?php
										/*$roles = \App\UserRole::find(Auth::user()->role);
                                        $newarray = json_decode($roles->module_access);
                                        $module_access = (array) $newarray;*/
                                        //if(array_key_exists('20',  $module_access)) {
										?>
										<h2 class="mb-3 font-18"><?php //echo $countclient; ?></h2>
										<?php //} ?>

										<p class="mb-0"><span class="col-green"><?php //echo $countclient; ?> clients</span> ongoing</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>-->
        </div>


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
                                <a href="javascript:;" data-toggle="modal" data-target=".add_appiontment" class="btn btn-outline-primary btn-sm add_btn"><i class="fa fa-plus"></i> Add</a>
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
                                <a href="javascript:;" id="create_task" class="btn btn-outline-primary btn-sm add_btn"><i class="fa fa-plus"></i> Add</a>
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

            <!--<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-4">
                <div class="card dash_card">
                    <div class="card-statistic-4">
                        <div class="card-content cus_card_content">
                            <div class="card_header">
                                <h5 class="font-14">Application Reminders</h5>
                            </div>
                            <?php
                            //$applications = \App\Application::where('end_date','!=','')->whereDate('end_date','>=',date('Y-m-d'))->orderby('end_date','desc')->get();
                            ?>
                            <div class="card_body">
                                <div class="appli_remind">
                                {{--@foreach($applications as $appli)--}}
                                <?php
                                   /* $productdetail = \App\Product::where('id', $appli->product_id)->first();
                                    $partnerdetail = \App\Partner::where('id', $appli->partner_id)->first();
                                    $clientdetail = \App\Admin::where('id', $appli->client_id)->first();
                                    $PartnerBranch = \App\PartnerBranch::where('id', $appli->branch)->first();
                                    */?>
                                    <?php
                                    /*$day = date('d', strtotime($appli->end_date));
                                    $week = date('D', strtotime($appli->end_date));
                                    $month = date('M', strtotime($appli->end_date));
                                    $year = date('Y', strtotime($appli->end_date));*/
                                    ?>
                                    <div class="appli_column">
                                        <div class="date">{{-- $day --}}<span>{{-- $week --}}</span>
                                        </div>
                                        <div class="appli_content">
                                            <a href="{{URL::to('admin/clients/detail/')}}/{{base64_encode(convert_uuencode(@$clientdetail->id))}}?tab=application&appid={{@$appli->id}}">{{@$partnerdetail->partner_name}}</a>
                                            <div class="event_end"><span>End</span> - {{-- @$productdetail->name --}}</div>
                                            <span class="end_date">{{-- $month --}} {{-- $year --}}</span>
                                        </div>
                                    </div>
                                    {{--@endforeach--}}

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>-->
        </div>


        <div class="row">
            <!--<div class="col-lg-6 col-md-6 col-sm-6 col_inline_flex">
                <div class="card card dash_chart card_column">
                    <div class="card-header">
                        <h4>Clients by Users</h4>
                    </div>
                    <div class="card-body">
                        <div class="progress_list">
                        <?php
                        /*$staffs = \App\Admin::where('role','!=',7)->where('show_dashboard_per',1)->orderby('first_name', 'ASC')->get();
                        $totalclients = \App\Admin::where('role', '=', '7')->count();
                        foreach($staffs as $staff)
                        {
                            $countclients = \App\Admin::where('assignee', $staff->id)->where('role', 7)->count();
                            $perclients=0;
                            if($totalclients>0){
                                $perclients = ($countclients / $totalclients) * 100;
                            }*/
                            ?>
                            <div class="progress_column">
                                <div class="progress_title"><a target="_blank" href="{{-- URL::to('/admin/users/view/'.$staff->id) --}}">{{-- $staff->first_name --}} {{-- $staff->last_name --}}</a></div>
                                <div class="progress_bar">
                                    <div class="progress" data-height="4">
                                      <div class="progress-bar" role="progressbar" data-width="{{-- round($perclients) --}}%" aria-valuenow={{-- "-$totalclients"ar --}}ia-valuemin="0" aria-valuemax="{{-- $totalclients --}}"></div>
                                    </div>
                                </div>
                                <div class="progress_count txt_rgt">{{-- $countclients --}}</div>
                            </div>
                        <?php
                        //} ?>
                        </div>
                    </div>
                </div>
            </div>-->

            <!--<div class="col-lg-6 col-md-6 col-sm-6 col_inline_flex">
                <div class="card card_column">
                    <div class="card-header">
                        <h4>Progress of Leads</h4>
                          <div class="card-header-action">
                                <div class="drop_table_data" style="display: inline-block;margin-right: 10px;">
                                    <button type="button" class="btn btn-primary dropdown-toggle">Staff</button>
                                    <div class="dropdown_list client_dropdown_list">
                                            <?php /*$staffs = \App\Admin::where('role','!=',7)->orderby('first_name', 'ASC')->get();
                                                foreach($staffs as $staff){*/
                                            ?>

                                        <a class="dropdown-item has-icon" href={{-- "-URL::to('/admin/users/vi --}}'.$staff->id)}}?tab=progress">{{-- $staff->first_name --}} {{-- $staff->last_name --}}</a>
                                            <?php //} ?>
                                    </div>
                                </div>

                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="application_workflow"></canvas>
                    </div>
                </div>
            </div>-->
        </div>

        <div class="row">
            <!--<div class="col-lg-6 col-md-6 col-sm-6 col_inline_flex">
                <div class="card card_column">
                    <div class="card-header">
                        <h4>Clients Application by Status</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="client_application"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col_inline_flex">
                <div class="card dash_chart card_column">
                    <div class="card-header">
                        <h4>Applications by Workflow Stages</h4>
                    </div>
                    <?php
                        //$allstages = \App\Application::select('stage')->groupBy('stage')->get();
                    ?>
                    <div class="card-body" style="max-height: 300px;    overflow-y: scroll;">
                        <div class="progress_list">
                            <?php
                        /*foreach($allstages as $allstage){

                            $countstage = \App\Application::where('stage',$allstage->stage)->count();
                            $totcountstage = \App\Application::count();
                            $perstage = ($countstage / $totcountstage) * 100;*/
                               ?>
                            <div class="progress_column">
                                <div class="progress_title">{{-- $allstage->stage --}}</div>
                                <div class="progress_bar">
                                    <div class="progress" data-height="4">
                                      <div class="progress-bar" role="progressbar" data-width="{{-- $perstage --}}%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="progress_count txt_rgt">{{-- $countstage --}}</div>
                            </div>
                            <?php //} ?>


                        </div>
                    </div>
                </div>
            </div>-->
        </div>




	</section>
</div>

<!-- Appointment Modal -->
<div class="modal fade add_appiontment custom_modal" id="create_appoint" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel">Add Appointment</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/add-appointment')}}" name="appointform" id="appointform" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="is_ajax" value="1">

					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
						<?php
							$timelist = \DateTimeZone::listIdentifiers(DateTimeZone::ALL);
						?>
							<div class="form-group">
								<label style="display:block;" for="related_to">Related to:</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="aclient" value="Client" name="related_to" checked>
									<label class="form-check-label" for="client">Client</label>
								</div>

								<span class="custom-error related_to_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label style="display:block;" for="related_to">Added by:</label>
								<span>{{@Auth::user()->first_name}}</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="client_id">Client Name <span class="span_req">*</span></label>
									<select data-valid="" class="form-control js-data-example-ajaxccl" name="client_id"></select>

							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="timezone">Timezone <span class="span_req">*</span></label>
								<select class="form-control timezoneselect2" name="timezone" data-valid="required">
									<option value="">Select Timezone</option>
									<?php
									foreach($timelist as $tlist){
										?>
										<option value="<?php echo $tlist; ?>" <?php if($tlist == 'Australia/Melbourne'){ echo "selected"; } ?>><?php echo $tlist; ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-7 col-lg-7">
							<div class="form-group">
								<label for="appoint_date">Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									{{ Form::text('appoint_date', '', array('class' => 'form-control datepicker', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Select Date' )) }}
								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error appoint_date_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-5 col-lg-5">
							<div class="form-group">
								<label for="appoint_time">Time</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-clock"></i>
										</div>
									</div>
									{{ Form::time('appoint_time', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off',  'placeholder'=>'Select Time')) }}
								</div>
								<span class="custom-error appoint_time_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="title">Title <span class="span_req">*</span></label>
								{{ Form::text('title', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Title' )) }}
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="description">Description</label>
								<textarea class="form-control" name="description" placeholder="Description"></textarea>
								<span class="custom-error description_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="invitees">Invitees</label>
									<select class="form-control invitesselects2" name="invites">
									<option value="">Select Invitees</option>
								    <?php
										$headoffice = \App\Admin::where('role','!=',7)->get();
									foreach($headoffice as $holist){
										?>
										<option value="{{$holist->id}}">{{$holist->first_name}} {{$holist->last_name}} ({{$holist->email}})</option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('appointform')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="create_task_modal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="taskModalLabel">Create New Task</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/tasks/store/')}}" name="newtaskform" autocomplete="off" id="tasktermform" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="is_ajax" value="0">
				<input type="hidden" name="is_dashboard" value="true">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="title">Title <span class="span_req">*</span></label>
								{{ Form::text('title', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Title' )) }}
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="category">Category <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control cleintselect2 select2" name="category">
									<option value="">Choose Category</option>
									<option value="Reminder">Reminder</option>
									<option value="Call">Call</option>
									<option value="Follow Up">Follow Up</option>
									<option value="Email">Email</option>
									<option value="Meeting">Meeting</option>
									<option value="Support">Support</option>
									<option value="Others">Others</option>
								</select>
								<span class="custom-error category_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
							<?php
								$assignee = \App\Admin::where('role','!=',1)->get();
								?>
								<label for="assignee">Assignee</label>
								<select data-valid="" class="form-control cleintselect2 select2" name="assignee">
									<option value="">Select</option>
									@foreach($assignee as $assigne)
										<option value="{{$assigne->id}}">{{$assigne->first_name}} ({{$assigne->email}})</option>
									@endforeach
								</select>
								<span class="custom-error assignee_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="priority">Priority</label>
								<select data-valid="" class="form-control cleintselect2 select2" name="priority">
									<option value="">Choose Priority</option>
									<option value="Low">Low</option>
									<option value="Normal">Normal</option>
									<option value="High">High</option>
									<option value="Urgent">Urgent</option>
								</select>
								<span class="custom-error priority_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="due_date">Due Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									{{ Form::text('due_date', '', array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )) }}
								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error due_date_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="due_time">Due Time</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-clock"></i>
										</div>
									</div>
									{{ Form::time('due_time', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off', 'placeholder'=>'Select Time' )) }}
								</div>
								<span class="custom-error due_time_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Description</label>
								<textarea class="form-control" name="description"></textarea>
								<span class="custom-error description_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label class="d-block" for="related_to">Related To</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="contact" value="Contact" name="related_to" checked>
									<label class="form-check-label" for="contact">Contact</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="partner" value="Partner" name="related_to">
									<label class="form-check-label" for="partner">Partner</label>
								</div>
								{{--<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="application" value="Application" name="related_to">
									<label class="form-check-label" for="application">Application</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="internal" value="Internal" name="related_to">
									<label class="form-check-label" for="internal">Internal</label>
								</div>--}}
								@if ($errors->has('related_to'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('related_to') }}</strong>
									</span>
								@endif
							</div>
						</div>

						<div class="col-12 col-md-6 col-lg-6 is_contact">
							<div class="form-group">
								<label for="contact_name">Contact Name <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control cleintselect2 select2" name="contact_name[]">
									<option value="">Choose Contact</option>
									<?php
									$clients = \App\Admin::where('is_archived', '=', '0')->where('role', '=', '7')->get();
									foreach($clients as $client){
									?>
									<option value="{{$client->id}} ">{{$client->first_name}} ({{$client->email}})</option>
									<?php } ?>
								</select>
								<span class="custom-error contact_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-6 col-lg-6 is_partner">
							<div class="form-group">
								<label for="partner_name">Partner Name <span class="span_req">*</span></label>
								<select data-valid="" class="form-control cleintselect2 select2" name="partner_name">
									<option value="">Choose Partner</option>
									<?php
									$Partners = \App\Partner::where('id', '!=', '')->get();
									foreach($Partners as $Partner){
									?>
									<option value="{{$Partner->id}} ">{{$Partner->first_name}} ({{$Partner->email}})</option>
									<?php } ?>
								</select>
								<span class="custom-error partner_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-6 col-lg-6 is_application">
							<div class="form-group">
								<label for="client_name">Client Name <span class="span_req">*</span></label>
								<select data-valid="" id="getapplications" class="form-control client_name cleintselect2" name="client_name">
									<option value="">Choose Client</option>
									<?php
								//$clientsss = \App\Admin::where('is_archived', '0')->where('role', '7')->get();
								/*	foreach($clientsss as $clientsssss){
									?>
									<option value="{{@$clientsssss->id}}">{{@$clientsssss->first_name}} ({{@$clientsssss->email}})</option>
									<?php }*/ ?>
								</select>
								<span class="custom-error client_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-6 col-lg-6 is_application">
							<div class="form-group">
								<label for="application">Application <span class="span_req">*</span></label>
								<select data-valid="" id="allaplication" class="form-control cleintselect2 select2" name="application">
									<option value="">Choose Application</option>

								</select>
								<span class="custom-error application_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6 is_application">
							<div class="form-group">
								<label for="stage">Stage <span class="span_req">*</span></label>
								<select data-valid="" class="form-control cleintselect2 select2" name="stage">
									<option value="">Choose Stage</option>
									<option value="Application">Application</option>
									<option value="Acceptance">Acceptance</option>
									<option value="Payment">Payment</option>
									<option value="Form | 20">Form | 20</option>
									<option value="Visa Application">Visa Application</option>
									<option value="Interview">Interview</option>
									<option value="Enrolment">Enrolment</option>
									<option value="Course Ongoing">Course Ongoing</option>

								</select>
								<span class="custom-error stage_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="followers">Followers <span class="span_req">*</span></label>
								<select data-valid="" class="form-control cleintselect2 select2" name="followers">
									<option value="">Choose Followers</option>
									<?php
									$followers = \App\Admin::where('role', '!=', '7')->get();
									foreach($followers as $follower){
									?>
									<option value="{{$follower->id}} ">{{$follower->first_name}} ({{$follower->email}})</option>
									<?php } ?>
								</select>
								<span class="custom-error followers_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="attachments">Attachments</label>
								<div class="custom-file">
									<input type="file" class="form-control" name="attachments">

								</div>
								<span class="custom-error attachments_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('newtaskform')" type="button" class="btn btn-primary">Create</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<?php
/*$leadsconverted = \App\Lead::where('converted', 1)->count();
$leadsprogress = \App\Lead::where('converted', 0)->count();
$leadstotal = \App\Lead::where('id', '!=','')->count();
$data = array($leadstotal, $leadsconverted, $leadsprogress);

$applicationinprogress = \App\Application::where('status',0)->count();
$applicationcompleted = \App\Application::where('status',1)->count();
$applicationdicontinued = \App\Application::where('status',2)->count();
$dataapplication = array($applicationinprogress, $applicationcompleted, $applicationdicontinued);*/

$data = array();
$dataapplication = array();
//dd($data);
?>
<script>
var data = {{json_encode($data)}};
var dataapplication = {{json_encode($dataapplication)}};
jQuery(document).ready(function($){
     $(".timezoneselect2").select2({
    dropdownParent: $("#create_appoint")
  });
   $(".invitesselects2").select2({
    dropdownParent: $("#create_appoint")
  });
	$(document).delegate('#create_task', 'click', function(){
		$('#create_task_modal').modal('show');
		$('.cleintselect2').select2({
			dropdownParent: $('#create_task_modal .modal-content'),
		});
	});


	$(document).delegate('.opentaskview', 'click', function(){
		$('#opentaskview').modal('show');
		var v = $(this).attr('id');
		$.ajax({
			url: site_url+'/admin/get-task-detail',
			type:'GET',
			data:{task_id:v},
			success: function(responses){

				$('.taskview').html(responses);
			}
		});
	});



	$('.js-data-example-ajaxccl').select2({
		 multiple: false,
		 closeOnSelect: true,
		dropdownParent: $('#create_appoint'),
		  ajax: {
			url: '{{URL::to('/admin/clients/get-onlyclientrecipients')}}',
			dataType: 'json',
			processResults: function (data) {
			  // Transforms the top-level key of the response object from 'items' to 'results'
			  return {
				results: data.items
			  };

			},
			 cache: true

		  },
	templateResult: formatRepol,
	templateSelection: formatRepoSelectionl
});
function formatRepol (repo) {
  if (repo.loading) {
    return repo.text;
  }

  var $container = $(
    "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

      "<div  class='ag-flex ag-align-start'>" +
        "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
        "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small ></div>" +

      "</div>" +
      "</div>" +
	   "<div class='ag-flex ag-flex-column ag-align-end'>" +

        "<span class='ui label yellow select2-result-repository__statistics'>" +

        "</span>" +
      "</div>" +
    "</div>"
  );

  $container.find(".select2-result-repository__title").text(repo.name);
  $container.find(".select2-result-repository__description").text(repo.email);
  $container.find(".select2-result-repository__statistics").append(repo.status);

  return $container;
}

function formatRepoSelectionl (repo) {
  return repo.name || repo.text;
}
});
</script>
@endsection
