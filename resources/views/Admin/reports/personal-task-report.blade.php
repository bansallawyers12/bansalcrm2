@extends('layouts.admin')
@section('title', 'Task Report')

@section('content')

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
							<h4>Task Report</h4>
						</div>
						<div class="card-body">
							<ul class="nav nav-pills" id="checkin_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" id="personal-tab"  href="{{URL::to('/admin/report/task/personal-task-report')}}" >Personal Task Report</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="office-tab"  href="{{URL::to('/admin/report/task/office-task-report')}}" >Office Task Report</a>
								</li>								
							</ul> 
							<div class="tab-content" id="checkinContent">
								<div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
									<div class="table-responsive common_table"> 
										<table class="table text_wrap">
											<thead> 
												<tr>
													<th>Category</th>
													<th>Task Title</th>
													<th>Contact</th>
													<th>Partner</th>
													<th>Description</th>
													<th>Priority</th>
													<th>Status</th>
													<th>Task Added By</th>
													<th>Added Date</th>
													<th>Current Assignee</th>
													<th>Due Date</th>
													<th>Due Time</th>
													<th>Followers</th>
													<th>Related To</th>
													<th>Completed Date</th>
													<th>Completed In (Days)</th>
													<th>Time Exceeded (Days)</th>
												</tr> 
											</thead>
											@if(count($lists) >0)
											<tbody class="tdata">	
												@if(@$totalData !== 0)
												@foreach (@$lists as $list)
												<?php
													$client = \App\Admin::where('role', '=', '7')->where('id', '=', $list->client_id)->first();
													$productdetail = \App\Product::where('id', $list->product_id)->first();
													$partnerdetail = \App\Partner::where('id', $list->partner_id)->first();	
												?>
												<tr id="id_{{@$list->id}}">
													<td>{{ @$list->category == "" ? config('constants.empty') : str_limit(@$list->category, '50', '...') }}</td>
													<td style="white-space: normal;">{{ @$list->title == "" ? config('constants.empty') : str_limit(@$list->title, '50', '...') }}</td>
													<td style="white-space: initial;">
														<a href="{{URL::to('/admin/clients/detail'.base64_encode(convert_uuencode(@$client->id)))}}">{{@$client->first_name}} {{@$client->last_name}}</a>
													</td>
													<td style="white-space: initial;">{{ @$list->partner_name == "" ? config('constants.empty') : str_limit(@$list->partner_name, '50', '...') }}</td>
													<td style="white-space: initial;">{{ @$list->description == "" ? config('constants.empty') : str_limit(@$list->description, '50', '...') }}</td>
													<td style="white-space: initial;">{{ @$list->priority == "" ? config('constants.empty') : str_limit(@$list->priority, '50', '...') }}</td>
													<td>
														<?php
														if($list->status == 1){
															echo '<span style="color: rgb(113, 204, 83); width: 84px;">Completed</span>';
														}else if($list->status == 2){
															echo '<span style="color: rgb(255, 173, 0); width: 84px;">In Progress</span>';
														}else if($list->status == 3){
															echo '<span style="color: rgb(156, 156, 156); width: 84px;">On Review</span>';
														}else{
															echo '<span style="color: rgb(255, 173, 0); width: 84px;">Todo</span>';
														}
														?>
													</td>
													<td>-</td>
													<td>{{date('d/m/Y',strtotime($list->created_at))}}</td>
													<td>-</td>
													<td style="white-space: initial;">{{ @$list->due_date == "" ? config('constants.empty') : date('d/m/Y',strtotime($list->due_date)) }}</td>
													<td style="white-space: initial;">{{ @$list->due_time == "" ? config('constants.empty') : str_limit(@$list->due_time, '50', '...') }}</td>
													<td style="white-space: initial;">{{ @$list->followers == "" ? config('constants.empty') : str_limit(@$list->followers, '50', '...') }}</td>
													<td style="white-space: initial;">{{ @$list->related_to == "" ? config('constants.empty') : str_limit(@$list->related_to, '50', '...') }}</td>
													<td>-</td>
													<td>-</td>
													<td>-</td>
												</tr>
												@endforeach	
												@endif										
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
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

@endsection