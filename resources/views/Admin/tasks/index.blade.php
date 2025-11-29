@extends('layouts.admin')
@section('title', 'Tasks')

@section('content')
<style>
.card .card-body table tbody.taskdata tr td span.check{background: #71cc53;color: #fff; border-radius: 50%;font-size: 10px;line-height: 14px;padding: 3px 4px;width: 18px;height: 18px;
display: inline-block;}
.card .card-body table tbody.taskdata tr td span.round{background: #fff;border:1px solid #000; border-radius: 50%;font-size: 10px;line-height: 14px;padding: 2px 5px;width: 16px;height: 16px; display: inline-block;}
#opentaskview .modal-body ul.navbar-nav li .dropdown-menu{transform: none!important; top:40px!important;}
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
							<h4>All Tasks</h4>
							<div class="card-header-action">
								<div class="drop_table_data" style="display: none;margin-right: 10px;"> 
									<button type="button" class="btn btn-primary dropdown-toggle"><?php if(Request::get('status') == 'All'){ echo 'All'; }else if(Request::get('status') == 'assigned'){ echo 'Assigned to me'; }else if(Request::get('status') == 'mytodo'){ echo 'My Todo'; }else if(Request::get('status') == 'completed'){ echo 'My Completed'; }else if(Request::get('status') == 'following'){ echo 'Following'; }else if(Request::get('status') == 'archived'){ echo 'All Archived'; }else{ echo 'My Todo'; } ?></button>
									<div class="dropdown_list client_dropdown_list">
										<a class="dropdown-option <?php if(Request::get('status') == 'All'){ echo 'active';} ?>" href="{{URL::to('/admin/tasks')}}?status=All">All</a><br>
									<?php /*	<a class="dropdown-option <?php if(Request::get('status') == 'assigned'){ echo 'active';} ?>" href="{{URL::to('/admin/tasks')}}?status=assigned">Assigned to me</a><br> */ ?>
										<a class="dropdown-option <?php if(Request::get('status') == 'mytodo'){ echo 'active';} ?>" href="{{URL::to('/admin/tasks')}}?status=mytodo">My Todo</a><br>
										<a class="dropdown-option <?php if(Request::get('status') == 'completed'){ echo 'active';} ?>" href="{{URL::to('/admin/tasks')}}?status=completed">My Completed</a><br>
										<a class="dropdown-option <?php if(Request::get('status') == 'following'){ echo 'active';} ?>" href="{{URL::to('/admin/tasks')}}?status=following">Following</a><br>
										<a class="dropdown-option <?php if(Request::get('status') == 'archived'){ echo 'active';} ?>" href="{{URL::to('/admin/tasks')}}?status=archived">All Archived</a><br>
										 
									</div>
								</div>
								<a href="javascript:;" id="create_task" class="btn btn-primary">Create Task</a>
								<a href="javascript:;" id="create_group" class="btn btn-primary">Create Group</a>
								
								  <a href="javascript:;" id="delete_group" style="" class="btn btn-primary"><i class="fa fa-trash" aria-hidden="true"></i> Delete Group</a>
								 <a style="" href="javascript:;" id="invite_user" class="btn btn-primary"><i class="fa fa-user-plus" aria-hidden="true"></i> Invite User</a>
								
							</div>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-8">
								        
								</div>
								<div class="col-md-2">
									
										<label class="">Sort By</label>
										<div class="">
											<select class="form-control" id="sort">
												<option <?php if(Request::get('sort') == 'due_date'){ echo 'selected';} ?> value="due_date">Due Date</option>
												<option <?php if(Request::get('sort') == 'created_at'){ echo 'selected';} ?> value="created_at">Created Date</option>
												<option <?php if(Request::get('sort') == 'status'){ echo 'selected';} ?> value="status">Status</option>
												<option <?php if(Request::get('sort') == 'priority_no'){ echo 'selected';} ?> value="priority_no">Priority</option>
											</select>
										</div>
									
								</div>
								<div class="col-md-2">
									<label class="">Sort</label>
									<select class="form-control" id="sorttype">
										<option <?php if(Request::get('sorttype') == 'desc'){ echo 'selected';} ?> value="desc">Desc</option>
										<option <?php if(Request::get('sorttype') == 'asc'){ echo 'selected';} ?> value="asc">Asc</option>
									</select>
								</div>
							</div>
						
						</div>
						<div class="card-body">
							
							<?php
						$totalgroupcount = 	\App\ToDoGroup::where('user_id', Auth::user()->id)->count();
						$class = 'col-md-9';
						
						$groups = \App\ToDoGroup::where('user_id', Auth::user()->id)->orderby('name', 'ASC')->get();
							?>
							
							<div class="row">
							   
							    <div class="col-md-3">
							        <div class="custom_nav_setting">
									<?php
								$countasstodo 		= \App\Task::where('status',0)->where('assignee', '=', Auth::user()->id)->count(); 
								$countasstodoall 		= \App\Task::where('status',0)->count(); 
								?>
									<ul>
										<li class=""><a class="nav-link" href="{{URL::to('/admin/tasks/')}}"><span class="groupname" style="float: left;">All</span> <span class="countgroups" style="background-color: rgba(0,0,0,0.04);padding: 0px 5px;border-radius: 50%;color: #666; float: right;">{{$countasstodoall}}</span><div class="clearfix"></div></a></li> 
										<li class=""><a class="nav-link" href="{{URL::to('/admin/tasks/?gid=me')}}"><span class="groupname" style="float: left;">Assigned To Me</span> <span class="countgroups" style="background-color: rgba(0,0,0,0.04);padding: 0px 5px;border-radius: 50%;color: #666; float: right;">{{$countasstodo}}</span><div class="clearfix"></div></a></li> 
									</ul>
							                 <ul>
							                     @foreach($groups as $grouplists)
							                     <?php
							                     if(Auth::user()->role == 1){
	        $counttodo 		= \App\Task::where('status',0)->where('group_id', '=', $grouplists->id)->count(); 
	    }else{
	       
	        $counttodo 		= \App\Task::where('status',0)->where('group_id', '=', $grouplists->id)
	        ->where(function($query){
                            $query->where('assignee', Auth::user()->id)
                                  ->orWhere('followers', Auth::user()->id);
                        })->count();
	    }
							                     
							                     ?>
							                    <li class=""><a class="nav-link" href="{{URL::to('/admin/tasks/?gid='.$grouplists->id)}}"><span class="groupname" style="float: left;">{{$grouplists->name}}</span> <span class="countgroups" style="background-color: rgba(0,0,0,0.04);
    padding: 0px 5px;
    border-radius: 50%;
    color: #666; float: right;">{{$counttodo}}</span><div class="clearfix"></div></a></li> 
							                    @endforeach
							                </ul>
							          </div>
							     </div>
							  
							     <div class="{{$class}}">
								
								 <div class="clearfix"></div>
							            	<div class="table-responsive" style=""> 
								<table id="my-datatable" class="table-2 table text_wrap">
									<thead>
										<tr>
									       <th></th> 
											<th>Title</th>
											<th>Priority</th>
											<th>Date</th>
											<th>Client ID</th>
											<th>Assignee</th>
											<th>Status</th>
										</tr>
									</thead>
									@if(count($lists) >0)
									<tbody class="taskdata ">
									<?php
									foreach($lists as $alist){
										$admin = \App\Admin::where('id', $alist->user_id)->first();
											$assignee = \App\Admin::where('id', $alist->assignee)->first();
											$clint = \App\Admin::where('id', $alist->client_id)->first();
										if($alist->status == 0){
											$status = 'To Do';
										}else if($alist->status == 2){
											$status = '>In Progress';
										}else if($alist->status == 3){
											$status = 'On Review<';
										}else if($alist->status == 1){
											$status = 'Completed';
										}
										$d = 1;
										if($alist->status == 1){
											$d = 0;
										}
										$ds = 'Completed';
										if($alist->status == 1){
											$ds = 'To Do';
										}
										?>
										<tr  style="cursor:pointer;" >
											<td data-status-name="{{$ds}}" data-status="{{$d}}" data-id="{{$alist->id}}" class="changestatuscompleted"><?php if($alist->status == 1){ echo "<span class='check'><i class='fa fa-check'></i></span>"; } else{ echo "<span class='round'></span>"; } ?></td> 
											<td class="opentaskview" id="{{$alist->id}}"><b>{{$alist->category}}</b>: {{$alist->title}}</td>
											
											<td class="opentaskview" id="{{$alist->id}}">{{$alist->priority}}</td> 
											<td class="opentaskview" id="{{$alist->id}}"><i class="fa fa-clock"></i> {{date('d/m/Y h:i A', strtotime($alist->due_date.' '.$alist->due_time))}}</td>
												<td class="opentaskview" id="{{$alist->id}}"> {{@$clint->client_id}}</td>
											<td class="opentaskview" id="{{$alist->id}}">@if($assignee) <span class="author-avtar" style="font-size: .8rem;height: 24px;line-height: 24px;width: 24px;min-width: 24px;background: rgb(3, 169, 244);display: inline-block;
    vertical-align: middle;"><?php echo substr($assignee->first_name, 0, 1); ?></span> <span><?php echo $assignee->first_name; ?></span> @endif</td> 
											<td class="opentaskview updatestatusview{{$alist->id}}" id="{{$alist->id}}"><?php
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
									}
									?>											
										<?php
									foreach($listscom as $alist){
										$admin = \App\Admin::where('id', $alist->user_id)->first();
											$assignee = \App\Admin::where('id', $alist->assignee)->first();
											$clint = \App\Admin::where('id', $alist->client_id)->first();
										if($alist->status == 0){
											$status = 'To Do';
										}else if($alist->status == 2){
											$status = '>In Progress';
										}else if($alist->status == 3){
											$status = 'On Review<';
										}else if($alist->status == 1){
											$status = 'Completed';
										}
										$d = 1;
										if($alist->status == 1){
											$d = 0;
										}
										$ds = 'Completed';
										if($alist->status == 1){
											$ds = 'To Do';
										}
										?>
										<tr  style="cursor:pointer;" >
											<td data-status-name="{{$ds}}" data-status="{{$d}}" data-id="{{$alist->id}}" class="changestatuscompleted"><?php if($alist->status == 1){ echo "<span class='check'><i class='fa fa-check'></i></span>"; } else{ echo "<span class='round'></span>"; } ?></td> 
											<td class="opentaskview" id="{{$alist->id}}"><b>{{$alist->category}}</b>: {{$alist->title}}</td>
											
											<td class="opentaskview" id="{{$alist->id}}">{{$alist->priority}}</td> 
											<td class="opentaskview" id="{{$alist->id}}"><i class="fa fa-clock"></i> {{date('d/m/Y h:i A', strtotime($alist->due_date.' '.$alist->due_time))}}</td>
												<td class="opentaskview" id="{{$alist->id}}"> {{@$clint->client_id}}</td>
											<td class="opentaskview" id="{{$alist->id}}">@if($assignee) <span class="author-avtar" style="font-size: .8rem;height: 24px;line-height: 24px;width: 24px;min-width: 24px;background: rgb(3, 169, 244);display: inline-block;
    vertical-align: middle;"><?php echo substr($assignee->first_name, 0, 1); ?></span> <span><?php echo $assignee->first_name; ?></span> @endif</td> 
											<td class="opentaskview updatestatusview{{$alist->id}}" id="{{$alist->id}}"><?php
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
									}
									?>
									</tbody>
									@else
									<tbody>
										<tr>
											<td style="text-align:center;" colspan="12">
												No Record found
											</td>
										</tr>
									</tbody>
									@endif
								</table> 
								
							</div>
							     </div>
							 </div>
						
						</div>
						<div class="card-footer">
							{!! $lists->appends(\Request::except('page'))->render() !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
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
								<label for="group_id">Group <span class="span_req">*</span></label> 
								<select data-valid="" class="form-control groupsselect2 " name="group_id">
									<option value="">Choose Group</option>
									@foreach($groups as $glist)
									<option value="{{$glist->id}}">{{$glist->name}}</option>
									@endforeach
								</select> 
								<span class="custom-error category_error" role="alert">
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
								$assignee = \App\Admin::where('role','!=',7)->get();
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
									{{ Form::text('due_time', '', array('class' => 'form-control timepicker', 'data-valid'=>'', 'autocomplete'=>'off', 'placeholder'=>'Select Time', 'readonly' )) }} 
								</div>
								<span class="custom-error due_time_error" role="alert">
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
								<select data-valid="" class="js-data-example-ajax-checktask" name="client_id"></select>
								
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
						
					{{--	<div class="col-12 col-md-6 col-lg-6 is_application">
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
						</div>--}}
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
								<label for="description">Description</label> 
								<textarea class="form-control" name="description"></textarea>
								<span class="custom-error description_error" role="alert">
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


<div class="modal fade custom_modal" id="create_group_modal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="taskModalLabel">Create Group</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/tasks/groupstore/')}}" name="newgroupform" autocomplete="off" id="grouptermform" enctype="multipart/form-data">
				@csrf 
				<input type="hidden" name="is_ajax" value="0">
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
						
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('newgroupform')" type="button" class="btn btn-primary">Create</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form> 
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="invite_user_modal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="taskModalLabel">Invite Users</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/tasks/inviteform/')}}" name="inviteform" autocomplete="off" id="inviteform" enctype="multipart/form-data">
				@csrf 
				<input type="hidden" name="is_ajax" value="0">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<ul style="list-style:none;height: 200px;overflow: auto;">
							<?php
							$followers = \App\Admin::where('role', '!=', '7')->get();
							foreach($followers as $follower){
							?>
								<li><label><input type="checkbox" name="invite_users[]" value="{{$follower->id}}"> {{$follower->first_name}} ({{$follower->email}})</label></li>
							<?php } ?>
							</ul>
						</div>
						
						<div class="col-12 col-md-12 col-lg-12">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
							<button onclick="customValidate('inviteform')" type="button" class="btn btn-primary">Invite</button>
						</div>
					</div>
				</form> 
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="delete_group_modal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="taskModalLabel">Delete Group</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/tasks/deletegroup/')}}" name="inviteform" autocomplete="off" id="inviteform" enctype="multipart/form-data">
				@csrf 
				<input type="hidden" name="is_ajax" value="0">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<ul style="list-style:none;height: 200px;overflow: auto;">
							<?php
							
							foreach($groups as $group){
							?>
								<li><label><input type="checkbox" name="delete_group[]" value="{{$group->id}}"> {{$group->name}}</label></li>
							<?php } ?>
							</ul>
						</div>
						
						<div class="col-12 col-md-12 col-lg-12">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
							<button onclick="customValidate('inviteform')" type="button" class="btn btn-primary">Submit</button>
						</div>
					</div>
				</form> 
			</div>
		</div>
	</div>
</div>
<!-- Task Modal -->
<div class="modal fade custom_modal" id="opentaskview" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content taskview">
			 
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script>
jQuery(document).ready(function($){
     $(document).delegate('.openassignee', 'click', function(){
        $('.assignee').show();
    });
     $(document).delegate('.closeassignee', 'click', function(){
        $('.assignee').hide();
    });
     $(document).delegate('.saveassignee', 'click', function(){
        var appliid = $(this).attr('data-id');
		$('.popuploader').show();
		$.ajax({
			url: site_url+'/admin/task/change_assignee',
			type:'GET',
			data:{id: appliid,assinee: $('#changeassignee').val()},
			success: function(response){
				
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
	$(document).delegate('#sort', 'change', function(){
		var v = $('#sort option:selected').val();
		var vs = $('#sorttype option:selected').val();
		if(v != ''){
			window.location.href = '{{URL::to('/admin/tasks/?status=')}}{{Request::get('status')}}&sort='+v+'&sorttype='+vs;
		}
	});
	$(document).delegate('#sorttype', 'change', function(){
		var v = $('#sort option:selected').val();
		var vs = $('#sorttype option:selected').val();
		if(v != ''){
			window.location.href = '{{URL::to('/admin/tasks/?status=')}}{{Request::get('status')}}&sort='+v+'&sorttype='+vs;
		}
	});
	$(document).delegate('.desc_click', 'click', function(){
		$(this).hide();
		$('.taskdesc').show();
		$('.taskdesc').focus();
	});
	
	$(document).delegate('#create_task', 'click', function(){
		$('#create_task_modal').modal('show');
		$('.cleintselect2').select2({
			dropdownParent: $('#create_task_modal .modal-content'),
		});
	});
	$(document).delegate('#create_group', 'click', function(){
		$('#create_group_modal').modal('show');
	
	});
	$(document).delegate('#invite_user', 'click', function(){
		$('#invite_user_modal').modal('show');
	
	});
	
	$(document).delegate('#delete_group', 'click', function(){
		$('#delete_group_modal').modal('show');
	
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
			url: site_url+'/admin/update_task_description',
			type:'POST',
			data:{id: appliid,visit_purpose:visitpurpose},
			success: function(responses){
				$.ajax({
					url: site_url+'/admin/get-task-detail',
					type:'GET',
					data:{task_id:appliid},
					success: function(responses){
						$('.popuploader').hide();
						$('.taskview').html(responses);
						updatedatetimepicker(appliid);
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
			url: site_url+'/admin/update_task_description',
			type:'POST',
			data:{id: appliid,visit_purpose:visitpurpose},
			success: function(responses){
				 $.ajax({
					url: site_url+'/admin/get-task-detail',
					type:'GET',
					data:{task_id:appliid},
					success: function(responses){
						$('.popuploader').hide();
						$('.taskview').html(responses);
						updatedatetimepicker(appliid);
					}
				});
				
			}
		});
	});
	
	$(document).delegate('.savecomment', 'click', function(){
		var visitcomment = $('.taskcomment').val();
		var appliid = $(this).attr('data-id');
		$('.popuploader').show();
		$.ajax({
			url: site_url+'/admin/update_task_comment',
			type:'POST',
			data:{id: appliid,visit_comment:visitcomment},
			success: function(responses){
				// $('.popuploader').hide();
				$('.taskcomment').val('');
				$.ajax({
					url: site_url+'/admin/get-task-detail',
					type:'GET',
					data:{task_id:appliid},
					success: function(responses){
						$('.popuploader').hide();
						$('.taskview').html(responses);
						updatedatetimepicker(appliid);
					}
				});
			}
		});
	});
	
	$(document).delegate('.changestatus', 'click', function(){
		var appliid = $(this).attr('data-id');
		var status = $(this).attr('data-status');
		var statusame = $(this).attr('data-status-name');
		$('.popuploader').show();
		
		$.ajax({
			url: site_url+'/admin/update_task_status',
			type:'POST',
			data:{id: appliid,statusname:statusame,status:status},
			success: function(responses){
				// $('.popuploader').hide();
				var obj = JSON.parse(responses);
				if(obj.status){
				    
				    $('.updatestatusview'+appliid).html(obj.viewstatus);
				}
				$.ajax({
					url: site_url+'/admin/get-task-detail',
					type:'GET',
					data:{task_id:appliid},
					success: function(responses){
						$('.popuploader').hide();
						$('.taskview').html(responses);
						updatedatetimepicker(appliid);
					}
				});
			}
		});
	});
	
	$(document).delegate('.changestatuscompleted', 'click', function(){
		var appliid = $(this).attr('data-id');
		var status = $(this).attr('data-status');
		var statusame = $(this).attr('data-status-name');
		$('.popuploader').show();
		$.ajax({
			url: site_url+'/admin/update_task_status',
			type:'POST',
			data:{id: appliid,statusname:statusame,status:status},
			success: function(responses){
			
				location.reload();
			}
		});
	});
	
	$(document).delegate('.changepriority', 'click', function(){
		var appliid = $(this).attr('data-id');
		var status = $(this).attr('data-status');
		$('.popuploader').show();
		
		$.ajax({
			url: site_url+'/admin/update_task_priority',
			type:'POST',
			data:{id: appliid,status:status},
			success: function(responses){
				// $('.popuploader').hide();
				
				$.ajax({
					url: site_url+'/admin/get-task-detail',
					type:'GET',
					data:{task_id:appliid},
					success: function(responses){
						$('.popuploader').hide();
						$('.taskview').html(responses);
						updatedatetimepicker(appliid);
						
					}
				});
			}
		});
	});
	$(document).delegate('.opentaskview', 'click', function(){
		$('.popuploader').show();
		$('#opentaskview').modal('show');
		var v = $(this).attr('id');
		$.ajax({
			url: site_url+'/admin/get-task-detail',
			type:'GET',
			data:{task_id:v},
			success: function(responses){
				$('.popuploader').hide();
				$('.taskview').html(responses);
				updatedatetimepicker(v);
			}
		});
	});
	
	$(document).delegate('#getapplications', 'change', function(){
		$('.popuploader').show();
		
		var v = $('#getapplications').val();
		$.ajax({
			url: site_url+'/admin/get-applications',
			type:'GET',
			data:{client_id:v},
			success: function(responses){
				$('.popuploader').hide();
				$('#allaplication').html(responses);
			
			}
		});
	});
	
	function updatedatetimepicker(taskid){
		$(".timepicker").timepicker({
				  icons: {
					up: "fas fa-chevron-up",
					down: "fas fa-chevron-down"
				  }
				});
				 $(".datepicker").daterangepicker({
					locale: { cancelLabel: 'Clear',format: "YYYY-MM-DD" },
					singleDatePicker: true,
					showDropdowns: true
				}, function(start, end, label) {
					$('.popuploader').show();
					$.ajax({
						url:"{{URL::to('/admin/updateduedate')}}",
						method: "POST", // or POST
						
						data: {from: start.format('YYYY-MM-DD'), id: taskid},
						success:function(result) {
							
							$.ajax({
								url: site_url+'/admin/get-task-detail',
								type:'GET',
								data:{task_id:taskid},
								success: function(responses){
									$('.popuploader').hide();
									$('.taskview').html(responses);
									updatedatetimepicker(taskid);
								}
							});
						}
					});
				});
		
	}
	
	
	$('.js-data-example-ajax-checktask').on("select2:select", function(e) { 
  var data = e.params.data;
  console.log(data);
  // $('#contact_name').val(data.status);
});	
			
			$('.js-data-example-ajax-checktask').select2({
		 multiple: false,
		 closeOnSelect: true,
		dropdownParent: $('#create_task_modal'),
		  ajax: {
			url: '{{URL::to('/admin/clients/get-recipients')}}',
			dataType: 'json',
			processResults: function (data) {
			  // Transforms the top-level key of the response object from 'items' to 'results'
			  return {
				results: data.items
			  };
			  
			},
			 cache: true
			
		  },
	templateResult: formatRepochecks,
	templateSelection: formatRepoSelectionchecks
});
function formatRepochecks (repo) {
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

function formatRepoSelectionchecks (repo) {
  return repo.name || repo.text;
}
});
</script>
@endsection	