@extends('layouts.dashboard_frontend')
@section('title', 'Dashboard | View Test')
@section('content')
        
<div class="row dashboard">
	<div class="panel panel-default">
		<div class="panel-heading dashboard-main-heading">
			<h3 class="panel-title text-center">
				YOUR DASHBOARD
			</h3>
		</div>
		<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
			<!-- Emergency Note Start-->
				@include('../Elements/emergency')
			<!-- Emergency Note End-->
			
			<!-- Flash Message Start -->
				<div class="server-error">
					@include('../Elements/flash-message')	
				</div>
			<!-- Flash Message End -->
			
			<div class="panel-body">
				<div class="col-lg-12 col-sm-12 col-md-12">
				    <div class="tab" role="tabpanel">
				  	 	@include('../Elements/Frontend/navigation')		
			      	</div>
					
			      <div class="row">
						<div class="animated fadeIn">
							<div class="row">
								<div class="col-sm-12 order-summary-box">
									<h3>
										<strong>Test Details</strong>
										<div class="float-right">
											<a href="{{URL::to('/test')}}" class="btn btn-primary">Back</a>	
										</div>	
									</h3>
									
									<div class="form-group">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Course Name</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											{{ @$fetchedDataTest->subject->which_course == "" ? config('constants.empty') : @$fetchedDataTest->subject->course->course_name }}
										</div>
									</div>
									<div class="clear clearfix"></div>
									
									<div class="form-group">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Test Series Type</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											{{ @$fetchedDataTest->subject->which_test_series_type == "" ? config('constants.empty') : @$fetchedDataTest->subject->test_series_type->test_series_type }}
										</div>
									</div>
									<div class="clear clearfix"></div>
									
									<div class="form-group">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Group</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											{{ @$fetchedDataTest->subject->which_group == "" ? config('constants.empty') : @$fetchedDataTest->subject->group->group_name }}
										</div>
									</div>
									<div class="clear clearfix"></div>
									
									<div class="form-group">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Subject Name</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											{{ @$fetchedDataTest->which_subject == "" ? config('constants.empty') : @$fetchedDataTest->subject->subject_name }}
										</div>
									</div>
									<div class="clear clearfix"></div>
									
									<div class="form-group">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Test Number</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											{{ @$fetchedDataTest->test_number == "" ? config('constants.empty') : @$fetchedDataTest->test_number }}
										</div>
									</div>
									<div class="clear clearfix"></div>
									<div class="form-group">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Chapter Covered</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											{{ @$fetchedDataTest->test_name == "" ? config('constants.empty') : @$fetchedDataTest->test_name }}
										</div>
									</div>
									<div class="clear clearfix"></div>
									
									<div class="form-group">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Test Start</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											{{ @$fetchedDataTest->from_date == "" ? config('constants.empty') : Carbon\Carbon::parse(@$fetchedDataTest->from_date)->toFormattedDateString() }}
										</div>
									</div>
									<div class="clear clearfix"></div>	
									
									<div class="form-group">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Test End</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											{{ @$fetchedDataTest->to_date == "" ? config('constants.empty') : Carbon\Carbon::parse(@$fetchedDataTest->to_date)->toFormattedDateString() }}
										</div>
									</div>
									<div class="clear clearfix"></div>	
									
									<div class="form-group">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Time</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											{{ @$fetchedDataTest->estimated_time == "" ? config('constants.empty') : @$fetchedDataTest->estimated_time }}
										</div>
									</div>
									<div class="clear clearfix"></div>
									
									<div class="form-group">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Marks</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											{{ @$fetchedDataTest->marks == "" ? config('constants.empty') : @$fetchedDataTest->marks }}
										</div>
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="col-sm-12 order-summary-box">
									<h3>
										Some Instructions
									</h3>
									<div class="form-group cms-desc test-series-detail">
										<p>
											<b>1.</b> Please mention Name, Test No.,Subject,Old/New course,mobile No on first page of answersheet.
										</p>
										<p>	
											<b>2.</b> Please mention page number on every page.
										</p>	
										<p>	
											<b>3.</b> Please write  Question No. Clearly and correctly.
										</p>
										<p>	
											<b>4.</b> Scan or take picture of answer sheet properly and convert it into PDF.
										</p>
										<p>	
											<b>5.</b> Once student submit their sheet, it can not be modified or edited.
										</p>
										<br/>	
										<p>
											<b>Disclaimer -</b> If any of above conditions not satisfied by student,sheet will not be evaluated....
										</p>	
									<div class="clear clearfix"></div>	
								</div>
							</div>		
							<?php 
								$todayDate = strtotime(date('Y-m-d')); ;
								$testEndDate = strtotime(@$fetchedDataTest->to_date);
								
								if($todayDate <= @$testEndDate)
								{		
							?>
									@if(@$scheduleData)	
										<div class="row">
											<div class="col-sm-12 order-summary-box">
												<h3>
													<strong>Scheduled / Submitted Test Details</strong>	
												</h3>
												<div class="form-group">
													<div class="col-md-4 col-sm-12 float-left">
														<strong>Test Schedule</strong>
													</div>
													<div class="col-md-8 col-sm-12 float-right">	
														{{ @$scheduleData->scheduled_date == "" ? config('constants.empty') : Carbon\Carbon::parse(@$scheduleData->scheduled_date)->toFormattedDateString() }}
													</div>
												</div>
												<div class="clear clearfix"></div>
												
												@if(@$scheduleData->test_submitted == 1)
													<div class="form-group">
														<div class="col-md-4 col-sm-12 float-left">
															<strong>Download Your Question Paper</strong>
														</div>
														<div class="col-md-8 col-sm-12 float-right">	
															<a href="{{URL::to('/public/img/test_pdfs/'.@$fetchedDataTest->test_pdf)}}" target="_blank">
																<img src="{{ asset('public/img/pdf.ico') }}" class="pdf-icon" style=	"width:10%;"/>
															</a>
														</div>
													</div>
													<div class="clear clearfix"></div>

													<div class="form-group">
														<div class="col-md-4 col-sm-12 float-left">
															<strong>Your Uploaded Answer Sheet</strong>
														</div>
														<div class="col-md-8 col-sm-12 float-right">	
															<a href="{{URL::to('/public/img/test_submitted_copies/'.@$scheduleData->test_submitted_copy)}}" target="_blank">
																<img src="{{ asset('public/img/pdf.ico') }}" class="pdf-icon" style=	"width:10%;"/>
															</a>
														</div>
													</div>
													<div class="clear clearfix"></div>
													
													<div class="form-group">
														<div class="col-md-4 col-sm-12 float-left">
															<strong>Suggested Answer Sheet</strong>
														</div>
														<div class="col-md-8 col-sm-12 float-right">	
															<a href="{{URL::to('/public/img/test_suggestion_pdfs/'.@$fetchedDataTest->test_suggestion_pdf)}}" target="_blank">
																<img src="{{ asset('public/img/pdf.ico') }}" class="pdf-icon" style=	"width:10%;"/>
															</a>
														</div>
													</div>
													@if(@$scheduleData->test_reviewed == 0)
														<div class="form-group text-center total_amount_view">
															<span class="btn btn-success">Our Experts are reviewing your submitted Test Paper and will let you know feedbacks as soon as possible.</span>
														</div>	
													@endif
												@elseif(@$scheduleData->test_submitted == 0)
													<?php
														if(strtotime(@$scheduleData->scheduled_date) <= $todayDate)
														{		
													?>
															<div class="form-group">
																<div class="col-md-4 col-sm-12 float-left">
																	<strong>Download your Question Paper</strong>
																</div>
																<div class="col-md-8 col-sm-12 float-right">	
																	<a href="{{URL::to('/public/img/test_pdfs/'.@$fetchedDataTest->test_pdf)}}" target="_blank">
																		<img src="{{ asset('public/img/pdf.ico') }}" class="pdf-icon" style=	"width:10%;"/>
																	</a>
																</div>
															</div>
															<div class="clear clearfix"></div>
															
															{{ Form::open(array('url' => '/upload_answer', 'name'=>"upload-answer", 'enctype'=>'multipart/form-data')) }}
																<div class="form-group total_amount_view">
																	<div class="col-md-4 col-sm-12 float-left">
																		<strong>Upload Your Answer Sheet<strong>
																	</div>
																	<div class="col-md-3 col-sm-6 float-right">	
																		{{ Form::hidden('id', @$scheduleData->id) }}
																		<input type="file" name="test_submitted_copy" class="form-control uploadFile" data-valid="required" /> 
																	</div>
																</div>
																<div class="clear clearfix"></div>
					
																<div class="form-group total_amount_view">
																	<div class="col-md-4 col-sm-12 float-left">
																	</div>
																	<div class="col-md-3 col-sm-6 float-right">	
																		{{ Form::button('Submit', ['class'=>'btn btn-primary px-4', 'onClick'=>'customValidate("upload-answer")']) }}
																	</div>
																</div>
															{{ Form::close() }}	
												<?php 	} 	
													else
														{
												?>
															<div class="clear clearfix"></div>
															<div class="form-group text-center schedule_test">
																<span class="btn btn-success">Test Paper will be shown on Scheduled Date.</span>
															</div>
												<?php	
														}
												?>
												@endif	
											</div>		
										</div>
									@else
										<div class="row">
											<div class="col-sm-12 order-summary-box">
												<div class="form-group text-center schedule_test">
													<span class="btn btn-success" type="button" data-toggle="modal" data-target="#scheduleTestModal">Would you like to schedule your Test ?</span>
												</div>
											</div>		
										</div>	
									@endif
						<?php	}
							else
								{
						?>
									<div class="row">
										<div class="col-sm-12 order-summary-box">
											<div class="form-group text-center schedule_test">
												<span class="btn btn-danger">Test End Date has been passed, so now you can't perform any operation for the same test.</span>
											</div>
										</div>		
									</div>
						<?php	
								}	
						?>	
						
							@if(@$scheduleData)	
								@if(@$scheduleData->test_reviewed == 1)	
									<div class="row">
										<div class="col-sm-12 order-summary-box">
											<h3>
												<strong>Review Your Answer Sheet</strong>
											</h3>
											
											<div class="form-group">
												<div class="col-md-4 col-sm-12 float-left">
													<strong>Reviewed Date</strong>
												</div>
												<div class="col-md-8 col-sm-12 float-right">	
													{{ @$scheduleData->reviewed_date == "" ? config('constants.empty') : Carbon\Carbon::parse(@$scheduleData->reviewed_date)->toFormattedDateString() }}
												</div>
											</div>
											<div class="clear clearfix"></div>
											
											<div class="form-group">
												<div class="col-md-4 col-sm-12 float-left">
													<strong>Review Your Answer Sheet</strong>
												</div>
												<div class="col-md-8 col-sm-12 float-right">	
													<a href="{{URL::to('/public/img/test_reviewed_copies/'.@$scheduleData->test_reviewed_copy)}}" target="_blank">
														<img src="{{ asset('public/img/pdf.ico') }}" class="pdf-icon" style=	"width:10%;"/>
													</a>
												</div>
											</div>
											<div class="clear clearfix"></div>
											
											<div class="form-group total_amount_view">
												<div class="col-md-4 col-sm-12 float-left">
													<strong>Get Marks</strong>
												</div>
												<div class="col-md-8 col-sm-12 float-right">	
													{{ @$scheduleData->marks == "" ? config('constants.empty') : @$scheduleData->marks }}
												</div>
											</div>
											<div class="clear clearfix"></div>
											
											<div class="form-group">
												<div class="col-md-4 col-sm-12 float-left">
													<strong>Additional Comment</strong>
												</div>
												<div class="col-md-8 col-sm-12 float-right">	
													{{ @$scheduleData->additional_remarks == "" ? config('constants.empty') : @$scheduleData->additional_remarks }}
												</div>
											</div>
										</div>
									</div>
								@endif		
							@endif	
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Schedule Your Test Start-->
<div class="modal fade" id="scheduleTestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-primary schedule_test_modal" role="document">
		<div class="modal-content">
			{{ Form::open(array('url' => '/schedule_test_request', 'name'=>"schedule-request")) }}	
				<div class="modal-body">
					<div class="card-body">
						<div class="form-group">
							<label for="scheduled_date">SCHEDULE DATE<em>*</em></label>
								{{ Form::hidden('test_id', @$fetchedDataTest->id) }}
								{{ Form::text('scheduled_date', '', array('id'=>'scheduled_date', 'class' => 'form-control', 'data-valid'=>'required', 'readonly'=>'readonly')) }}
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
					{{ Form::button('Set', ['class'=>'btn btn-primary px-4', 'onClick'=>'customValidate("schedule-request")']) }}
				</div>
			{{ Form::close() }}		
		</div>
	</div>
</div>
<!-- Schedule Your Test End-->

<script type="text/javascript">
	$(document).ready(function(){
		//Scheduled Date
			$('#scheduled_date').fdatepicker({
				initialDate: '<?php echo @$fetchedDataTest->from_date; ?>',
				startDate: '<?php echo @$fetchedDataTest->from_date; ?>',
				endDate: '<?php echo @$fetchedDataTest->to_date; ?>',
				format: 'yyyy-mm-dd',
				disableDblClickSelection: true,
				leftArrow:'<<',
				rightArrow:'>>'
			});	
	});
</script>	
@endsection