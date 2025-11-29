@extends('layouts.dashboard_frontend')
@section('title', 'Dashboard | Your Current Tests')
@section('content')
<div class="row">
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
		<!-- Flash Message Start -->
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
		<!-- Flash Message End -->
	</div>
</div>      
<div class="row dashboard">
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading dashboard-main-heading">
				<h3 class="panel-title text-center">
					YOUR DASHBOARD
				</h3>
			</div>
			<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 no-padding">
				<!-- Emergency Note Start-->
					@include('../Elements/emergency')
				<!-- Emergency Note End-->
				
				<!-- Flash Message Start -->
				<div class="server-error">
					@include('../Elements/flash-message')	
				</div>
				<!-- Flash Message End -->
			
				<div class="panel-body">
					<div class="col-lg-12 col-sm-12 col-md-12 no-padding">
						<div class="tab" role="tabpanel">				
							<!-- Content Start for the Menu Bar Dashboard -->
								@include('../Elements/Frontend/navigation')
							<!-- Content End for the Menu Bar Dashboard -->	
						</div>
					</div>
				</div>
				
				<h3 class="order-summary"><strong>CURRENT TESTS ({{@$totalData}})</strong></h3>
				<div class="clearfix"></div>	
				<div class="panel-body">
					<div class="col-lg-12 col-sm-12 col-md-12 no-padding">
						<div class="tab" role="tabpanel">
							<div class="tab-content tabs">
								<div role="tabpanel" class="fade in active" id="Section0">		
									<div class="table-responsive">
										<div id="orderSummary_wrapper" class="dataTables_wrapper no-footer">
											<table id="orderSummary" class="table table-striped dataTable no-footer" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>Test Number</th>
														<th>Test Name</th>
														<th>Course Name</th>
														<th>Subject Name</th>
														<th>Test Series Type</th>
														<th>Group</th>
														<th>Test Start From</th>
														<th>Test End To</th>
														<!--<th>Submitted ?</th>
														<th>Reviewed ?</th>-->
														<th>Marks</th>
														<th>Time</th>
														<th>Action</th>
													</tr>
												</thead>
												@if(@$totalData !== 0)
												<tbody class="tdata">
													@foreach (@$lists as $list)
														<tr id="id_{{@$list->id}}">
															<td>
																{{ @$list->test_number == "" ? config('constants.empty') : @$list->test_number }}
															</td>
															<td>
																{{ @$list->test_name == "" ? config('constants.empty') : @$list->test_name }}
															</td>
															<td>
																{{ @$list->subject->which_course == "" ? config('constants.empty') : @$list->subject->course->course_name }}
															</td>
															<td>
																{{ @$list->subject->subject_name == "" ? config('constants.empty') : @$list->subject->subject_name }}
															</td>
															<td>
																{{ @$list->subject->which_test_series_type == "" ? config('constants.empty') : @$list->subject->test_series_type->test_series_type }}
															</td>
															<td>
																{{ @$list->subject->which_group == "" ? config('constants.empty') : @$list->subject->group->group_name }}
															</td>
															<td>
																{{ @$list->from_date == "" ? config('constants.empty') : Carbon\Carbon::parse(@$list->from_date)->toFormattedDateString() }}
															</td>
															<td>
																{{ @$list->to_date == "" ? config('constants.empty') : Carbon\Carbon::parse(@$list->to_date)->toFormattedDateString() }}
															</td>
															<!--<td>
																<?php
																	/* if(@$list->scheduledTest)	
																	{
																		if(@$list->scheduledTest->test_submitted == 0)
																		{
																			echo '<span class="btn btn-success">No</span>';
																		}
																		else
																		{
																			echo '<span class="btn btn-danger">No</span>';
																		}	
																	}
																	else
																	{
																		echo '<span class="btn btn-danger">No</span>';
																	} */		
																?>
															</td>	
															<td>
																<?php
																	/* if(@$list->scheduledTest)
																	{
																		if(@$list->scheduledTest->test_reviewed == 0)
																		{
																			echo '<span class="btn btn-success">No</span>';
																		}
																		else
																		{
																			echo '<span class="btn btn-danger">No</span>';
																		}	
																	}
																	else
																	{
																		echo '<span class="btn btn-danger">No</span>';
																	} */		
																?>
															</td>-->
															<td>
																{{ @$list->marks == "" ? config('constants.empty') : @$list->marks }}
															</td>
															<td>
																{{ @$list->estimated_time == "" ? config('constants.empty') : @$list->estimated_time }}
															</td>
															<td>
																<a class="btn btn-success" href="{{URL::to('/view_test/'.base64_encode(convert_uuencode(@$list->id)))}}" data-toggle="tooltip" title="View Test Summary">
																	<i class="fa fa-eye-slash"></i>
																</a>
															</td>
														</tr>
													@endforeach
												</tbody>
											@else
												<tbody>
													<tr>
														<td class="no_data" colspan="12" align="center">
															{{config('constants.no_data')}}
														</td>
													</tr>
												</tbody>
											@endif		
											</table>
											<div class="float-right">	
												@if(@$totalData !== 0)	
													{!! @$lists->appends(\Request::except('page'))->render() !!}
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
		</div>
	</div>
</div>
@endsection