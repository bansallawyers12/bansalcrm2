@extends('layouts.dashboard_frontend')
@section('title', 'Dashboard | View Order History')
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
						<!-- Content Start for the Menu Bar Dashboard -->
							@include('../Elements/Frontend/navigation')
						<!-- Content End for the Menu Bar Dashboard -->	
					</div>
					<div class="row">
						<div class="animated fadeIn">
							<div class="row">
								<div class="col-sm-12 order-summary-box">
									<h3>
										<strong>Order Details</strong>
										<div class="float-right">
											<input action="action" class="btn btn-primary" onclick="window.history.go(-1); return false;" type="button" value="Back" />		
										</div>	
									</h3>
									<div class="form-group">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Order Transaction ID</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											{{ @$fetchedData->transaction_id == "" ? config('constants.empty') : @$fetchedData->transaction_id }}
										</div>
									</div>
									<div class="clear clearfix"></div>
						
									<div class="form-group">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Order Status</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											{!! @$fetchedData->response == 0 ? '<span class="btn btn-danger">Fail</span>' : '<span class="btn btn-success">Success</span>' !!}
										</div>
									</div>
									<div class="clear clearfix"></div>

									<div class="form-group total_amount_view">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Refund</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											@if(@$fetchedData->response == 0)
												{{config('constants.empty')}}
											@else												
												{!! @$fetchedData->status == 1 ? '<span class="btn btn-danger">Yes</span>' : '<span class="btn btn-success">No</span>' !!}
											@endif	
										</div>
									</div>
									<div class="clear clearfix"></div>

									@if(@$fetchedData->response == 0)
										<div class="form-group reason">
											<div class="col-md-4 col-sm-12 float-left">
												<strong>Reason (for Failure)</strong>
											</div>
											<div class="col-md-8 col-sm-12 float-right failure">	
												<?php
													if(!empty(@$fetchedData->whole_response))
														{
															$jsonDecode = json_decode(@$fetchedData->whole_response);
															if(@$jsonDecode->unmappedstatus == 'userCancelled')
																{
																	echo "<b>Transaction Cancelled By You.</b>";
																}
															else
																{
																	if(@$jsonDecode->error_Message)
																	{	
																		echo @$jsonDecode->error_Message;
																	}
																	else
																	{
																		echo @$jsonDecode->field9;
																	}	
																}
																	
														}
													else
														{
															echo 'Payment has been decline due to leave the payment screen.';
														}
													?>
											</div>			
										</div>
										<div class="clear clearfix"></div>
									@endif
									
									<div class="clear clearfix"></div>
									<div class="form-group total_amount_view">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Total Amount</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											{{ @$fetchedData->total_amount == "" ? config('constants.empty') : '₹ '.@$fetchedData->total_amount }}
										</div>
									</div>
									<div class="clear clearfix"></div>
									
									<div class="form-group">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Discount</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											{{ @$fetchedData->discount == "" ? config('constants.empty') : '₹ '.@$fetchedData->discount }}
										</div>
									</div>
									<div class="clear clearfix"></div>
									
									<div class="form-group grand_total">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Pay After Discount</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											{{ @$fetchedData->pay_amount == "" ? config('constants.empty') : '₹ '.@$fetchedData->pay_amount }}
										</div>
									</div>	
									<div class="clear clearfix"></div>
									
									<div class="form-group">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Procced On</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											{{ @$fetchedData->created_at == "" ? config('constants.empty') : Carbon\Carbon::parse(@$fetchedData->created_at)->toFormattedDateString() }}
										</div>
									</div>
								</div>
								<!-- purchased subjects start -->
								<div class="col-sm-12 order-summary-box">	
									<h3>					
										<strong>Purchased Subjects</strong>
									</h3>
									<div class="row product-list-page">
										@if(@$fetchedData->response != 0 && @$fetchedData->status == 0)
											@if(count($fetchedData->purchased_subject) > 0)
												<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 col-7-products"> 
													<div class="main-product-area faculty">
														<div cellspacing="0" class="products-list-products" id="product-list-table">
															<ul class="product-list list-unstyled">
																@foreach($fetchedData->purchased_subject as $key=>$data)
																	@if(@$data->subject_data == '')
																		<li class="product-bucket test-series-subject">
																			<div class="text-center new-list">
																				<div>
																					<div class="form-group purchased-subjects">
																						<label class="first-test-series">
																							{{ @$data->subject->which_course == "0" ? config('constants.empty') : @$data->subject->course->course_name }}
																						</label>	
																					</div>
																					<div class="clear clearfix"></div>
																					<div class="form-group purchased-subjects">
																						<label class="second-test-series">
																							{{ @$data->subject->which_test_series_type == "0" ? config('constants.empty') : @$data->subject->test_series_type->test_series_type }}
																						</label>	
																					</div>
																					<div class="clear clearfix"></div>	
																					<div class="form-group purchased-subjects">
																						<label class="third-test-series">
																							{{ @$data->subject->which_group == "" ? config('constants.empty') : @$data->subject->group->group_name }}
																						</label>	
																					</div>
																					<div class="clear clearfix"></div>	
																					<div class="form-group purchased-subjects">
																						<label class="fourth-test-series">
																							{{ @$data->subject->subject_name == "" ? config('constants.empty') : @$data->subject->subject_name }}
																						</label>	
																					</div>
																					<div class="form-group purchased-subjects">
																						<label class="fifth-test-series">
																							{{ @$data->subject->price == "" ? config('constants.empty') : '₹ '.@$data->subject->price }}
																						</label>	
																					</div>
																				</div>
																			</div>
																		</li>
																	@else
																		<?php 
																			$real_data = json_decode(@$data->subject_data); 	
																		?>	
																		<li class="product-bucket test-series-subject">
																			<div class="text-center new-list">
																				<div>
																					<div class="form-group purchased-subjects">
																						<label class="first-test-series">
																							{{ @$real_data->course == "" ? config('constants.empty') : @$real_data->course }}
																						</label>	
																					</div>
																					<div class="clear clearfix"></div>
																					<div class="form-group purchased-subjects">
																						<label class="second-test-series">
																							{{ @$real_data->test_series_type == "" ? config('constants.empty') : @$real_data->test_series_type }}
																						</label>	
																					</div>
																					<div class="clear clearfix"></div>	
																					<div class="form-group purchased-subjects">
																						<label class="third-test-series">
																							{{ @$real_data->group == "" ? config('constants.empty') : @$real_data->group }}
																						</label>	
																					</div>
																					<div class="clear clearfix"></div>	
																					<div class="form-group purchased-subjects">
																						<label class="fourth-test-series">
																							{{ @$real_data->subject_name == "" ? config('constants.empty') : @$real_data->subject_name }}
																						</label>	
																					</div>
																					<div class="form-group purchased-subjects">
																						<label class="fifth-test-series">
																							{{ @$real_data->price == "" ? config('constants.empty') : '₹ '.@$real_data->price }}
																						</label>	
																					</div>
																				</div>
																			</div>
																		</li>	
																	@endif		
																@endforeach		
															</ul>
														</div>
													</div>
												</div>
											@else
												<div class="col-sm-12 no_data">
													<strong>No Subject Purchased.</strong>		
												</div>	
											@endif		
										@else
											<div class="col-sm-12 no_data">
												<strong>No Subject Purchased.</strong>		
											</div>	
										@endif
									</div>
								</div>		
								<!-- purchased subjects end -->	
										
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection