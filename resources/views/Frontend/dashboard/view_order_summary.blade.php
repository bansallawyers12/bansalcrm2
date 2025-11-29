@extends('layouts.dashboard_frontend')
@section('title', 'Dashboard | View Order Summary')
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
										<strong>Order Details</strong>
										<div class="float-right">
											<input action="action" class="btn btn-primary" onclick="window.history.go(-1); return false;" type="button" value="Back" />		
										</div>	
									</h3>
									<div class="form-group">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Order Number</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											{{ @$orderDetail->id == "" ? config('constants.empty') : '#'.@$orderDetail->id }}
										</div>
									</div>
									<div class="clear clearfix"></div>
									
									<div class="form-group">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Order Transaction ID</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											{{ @$fetchedDatas[0]->transaction_id == "" ? config('constants.empty') : @$fetchedDatas[0]->transaction_id }}
										</div>
									</div>
									<div class="clear clearfix"></div>
						
									<div class="form-group">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Order Status</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											{!! @$orderDetail->status == 0 ? '<span class="btn btn-danger">Fail</span>' : '<span class="btn btn-success">Success</span>' !!}
										</div>
									</div>
									<div class="clear clearfix"></div>	
									
									@if(@$orderDetail->status == 0)
										<div class="form-group reason">
											<div class="col-md-4 col-sm-12 float-left">
												<strong>Reason (for Failure)</strong>
											</div>
											<div class="col-md-8 col-sm-12 float-right failure">	
												<?php
													if(@$fetchedDatas[0]->response == 0)
														{
															if(!empty(@$fetchedDatas[0]->whole_response))
																{
																	$jsonDecode = json_decode(@$fetchedDatas[0]->whole_response);
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
																	echo 'There was some problem in the system.';
																}
														}
													else
														{
															echo "Payment has been successfully done.";
														}	
													?>
											</div>			
										</div>
										<div class="clear clearfix"></div>	
									@endif
										
									<div class="form-group">
										<div class="col-md-4 col-sm-12 float-left">
											<strong>Procced On</strong>
										</div>
										<div class="col-md-8 col-sm-12 float-right">	
											{{ @$orderDetail->created_at == "" ? config('constants.empty') : Carbon\Carbon::parse(@$orderDetail->created_at)->toFormattedDateString() }}
										</div>
									</div>
								</div>
								
								<div class="col-sm-12">
									<?php
										$grandTotal = 0;
									?>	
									@if(count(@$fetchedDatas) > 0)
										@foreach(@$fetchedDatas as $key=>$fetchedData)		
											<div class="col-sm-12 order-summary-box">
												<h3>
													<strong>Product Details</strong>
												</h3>
												<div class="form-group">
													<div class="col-md-4 col-sm-12 float-left">
														<strong>Professor Name</strong>
													</div>
													<div class="col-md-8 col-sm-12 float-right">	
														{{ @$fetchedData->professor_name != "" ? @$fetchedData->professor_name : @$fetchedData->product->professor->first_name.' '.@$fetchedData->product->professor->last_name }}
													</div>
												</div>
												<div class="clear clearfix"></div>
													
												<div class="form-group">
													<div class="col-md-4 col-sm-12 float-left">
														<strong>Subject Name</strong>
													</div>
													<div class="col-md-8 col-sm-12 float-right">	
														{{ @$fetchedData->subject_name != "" ? @$fetchedData->subject_name : @$fetchedData->product->subject_name }}
													</div>
												</div>
												<div class="clear clearfix"></div>
													
												<div class="form-group">
													<div class="col-md-4 col-sm-12 float-left">
														<strong>Delivery Method</strong>
													</div>
													<div class="col-md-8 col-sm-12 float-right">	
														{{ @$fetchedData->product_id == "" ? config('constants.empty') : @$fetchedData->mode_product_data->mode_product }}
													</div>
												</div>
												<div class="clear clearfix"></div>
												
												<div class="form-group">
													<div class="col-md-4 col-sm-12 float-left">
														<strong>Duration</strong>
													</div>
													<div class="col-md-8 col-sm-12 float-right">	
														{{ @$fetchedData->duration == "" ? config('constants.empty') : @$fetchedData->duration }}
													</div>
												</div>
												<div class="clear clearfix"></div>
												
												<div class="form-group">
													<div class="col-md-4 col-sm-12 float-left">
														<strong>Validity</strong>
													</div>
													<div class="col-md-8 col-sm-12 float-right">	
														{{ @$fetchedData->validity == "" ? config('constants.empty') : @$fetchedData->validity }}
													</div>
												</div>
												<div class="clear clearfix"></div>
												
												<div class="form-group">
													<div class="col-md-4 col-sm-12 float-left">
														<strong>Views</strong>
													</div>
													<div class="col-md-8 col-sm-12 float-right">	
														{{ @$fetchedData->views == "" ? config('constants.empty') : @$fetchedData->views }}
													</div>
												</div>
												<div class="clear clearfix"></div>
												
												<div class="form-group">
													<div class="col-md-4 col-sm-12 float-left">
														<strong>Price</strong>
													</div>
													<div class="col-md-8 col-sm-12 float-right">	
														{{ @$fetchedData->price == "" ? config('constants.empty') : '₹ '.@$fetchedData->price }}
													</div>
												</div>
												<div class="clear clearfix"></div>
												
												<div class="form-group">
													<div class="col-md-4 col-sm-12 float-left">
														<strong>Discount</strong>
													</div>
													<div class="col-md-8 col-sm-12 float-right">	
														{{ @$fetchedData->discount == "" ? config('constants.empty') : @$fetchedData->discount.'%' }}
													</div>
												</div>
												<div class="clear clearfix"></div>
												
												<div class="form-group">
													<div class="col-md-4 col-sm-12 float-left">
														<strong>Price After Discount (for each)</strong>
													</div>
													<div class="col-md-8 col-sm-12 float-right">	
														{{ @$fetchedData->total_amount == "" ? config('constants.empty') : '₹ '.@$fetchedData->total_amount }}
													</div>
												</div>
												<div class="clear clearfix"></div>
												
												<div class="form-group">
													<div class="col-md-4 col-sm-12 float-left">
														<strong>Quantity</strong>
													</div>
													<div class="col-md-8 col-sm-12 float-right">	
														{{ @$fetchedData->quantity == "" ? config('constants.empty') : @$fetchedData->quantity }}
													</div>
												</div>
												<div class="clear clearfix"></div>
												
												<div class="form-group grand_total">
													<div class="col-md-4 col-sm-12 float-left">
														<strong>Total Price</strong>
													</div>
													<div class="col-md-8 col-sm-12 float-right">	
														{{ @$fetchedData->pay_amount == "" ? config('constants.empty') : '₹ '.@$fetchedData->pay_amount }}
														
														<?php $grandTotal = $grandTotal + @$fetchedData->pay_amount; ?>	
													</div>
												</div>
												
												<div class="col-sm-12 order-summary-box">
													<h3>
														<strong>Dispatched Details</strong>
													</h3>
													<div class="form-group">
														<div class="col-md-4 col-sm-12 float-left">
															<strong>Dispatched ?</strong>
														</div>
														<div class="col-md-8 col-sm-12 float-right">	
															{!! @$fetchedData->dispatched == 0 ? '<span class="btn btn-danger">No</span>' : '<span class="btn btn-success">Yes</span>' !!}
														</div>
													</div>
													<div class="clear clearfix"></div>
													
													<div class="form-group reason">
														<div class="col-md-4 col-sm-12 float-left">
															<strong>Dispatched Date</strong>
														</div>
														<div class="col-md-8 col-sm-12 float-right">	
															{{ @$fetchedData->dispatched_date == "" ? config('constants.empty') : Carbon\Carbon::parse(@$fetchedData->dispatched_date)->toFormattedDateString() }}
														</div>
													</div>
													<div class="clear clearfix"></div>
													
													<div class="form-group">
														<div class="col-md-4 col-sm-12 float-left">
															<strong>Serial Number</strong>
														</div>
														<div class="col-md-8 col-sm-12 float-right">	
															{{ @$fetchedData->serial_number == "" ? config('constants.empty') : @$fetchedData->serial_number }}
														</div>
													</div>
													<div class="clear clearfix"></div>
													
													<div class="form-group">
														<div class="col-md-4 col-sm-12 float-left">
															<strong>Tracking Number</strong>
														</div>
														<div class="col-md-8 col-sm-12 float-right">	
															{{ @$fetchedData->tracking_number == "" ? config('constants.empty') : @$fetchedData->tracking_number }}
														</div>
													</div>
													<div class="clear clearfix"></div>
													
													<div class="form-group">
														<div class="col-md-4 col-sm-12 float-left">
															<strong>Courier Company Name</strong>
														</div>
														<div class="col-md-8 col-sm-12 float-right">	
															{{ @$fetchedData->courier_company_name == "" ? config('constants.empty') : @$fetchedData->courier_company_name }}
														</div>
													</div>	
												</div>
												
											</div>
										@endforeach
									@else
										<div class="col-sm-12 no_data">
											<strong>No Subject Purchased.</strong>		
										</div>	
									@endif	
									
									<div class="col-sm-12 order-summary-box">
										<div class="form-group">
											<div class="col-md-4 col-sm-12 float-left">
												<strong>Grand Total of this Order</strong>
											</div>
											<div class="col-md-8 col-sm-12 float-right">	
												{{ '₹ '.@$grandTotal }}
											</div>
										</div>
										<div class="clear clearfix"></div>
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