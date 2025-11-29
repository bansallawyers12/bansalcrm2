@extends('layouts.dashboard_frontend')
@section('title', 'Dashboard')
@section('content')      
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
					
					@if (@$lastFirstMsgStatus == 0)
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">☓</button>
							<strong>Congrats! You have successfully register into our system.</strong>
					</div>
					@endif	
				</div>
				<!-- Flash Message End -->
			
				<div class = "panel-body">
					<div class="col-lg-12 col-sm-12 col-md-12 no-padding">
						<div class="tab" role="tabpanel">
							<!-- Content Start for the Menu Bar Dashboard -->
								@include('../Elements/Frontend/navigation')
							<!-- Content End for the Menu Bar Dashboard -->	
							
							<!-- Product Order Summary Start -->
								<div class="tab-content tabs">
									<div role="tabpanel" class="tab-pane fade in active">
										<h3 class="order-summary"><strong>PRODUCT ORDER SUMMARY</strong></h3>
										<div class="table-responsive">
											<div id="orderSummary_wrapper" class="dataTables_wrapper no-footer">
												<table id="orderSummary" class="table table-striped dataTable no-footer" cellspacing="0" width="100%">
													<thead>
														<tr>
															<th>Order Number</th>
															<th>Order Status</th>
															<th>Order Date</th>
															<th>Action</th>
														</tr>
													</thead>
													@if(@$totalData !== 0)
													<tbody class="tdata">
														@foreach (@$lists as $list)
															<tr id="id_{{@$list->id}}">
																<td>
																	{{ @$list->id == "" ? config('constants.empty') : '#'.@$list->id }}
																</td>
																<td>
																	{!! @$list->status == 0 ? '<span class="btn btn-danger">Fail</span>' : '<span class="btn btn-success">Success</span>' !!}	
																</td>
																<td>
																	{{ @$list->created_at == "" ? config('constants.empty') : Carbon\Carbon::parse(@$list->created_at)->toFormattedDateString() }}
																</td>
																<td>
																	<a class="btn btn-success" href="{{URL::to('/dashboard/view_order_summary/'.base64_encode(convert_uuencode(@$list->id)))}}" data-toggle="tooltip" title="View Order Summary">
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
												 {!! @$lists->appends(\Request::except('page'))->render() !!}
											</div>
										</div>
									</div>
								</div>
							<!-- Product Order Summary End -->	
							
								<div class="clearfix"></div>
							
							<!--Test Series Order Summary Start -->
								<div class="tab-content tabs">
									<div role="tabpanel" class="fade in active">			
										<h3 class="order-summary"><strong>TEST SERIES ORDER SUMMARY</strong></h3>
										<div class="table-responsive">
											<div class="dataTables_wrapper no-footer">
												<table id="orderSummary1" class="table table-striped dataTable no-footer" cellspacing="0" width="100%">
													<thead>
														<tr>
															<th>Transaction ID</th>
															<th>Total Amount</th>
															<th>Discount</th>
															<th>Pay Amount</th>
															<th>Order Status</th>
															<th>Refund</th>
															<th>Order Date</th>
															<th>Action</th>
														</tr>
													</thead>
													@if(@$totalData1 !== 0)
													<tbody class="tdata">
														@foreach (@$lists1 as $list)
															<tr>
																<td>
																	{{ @$list->transaction_id == "" ? config('constants.empty') : @$list->transaction_id }}
																</td>
																<td>
																	{{ @$list->total_amount == "" ? config('constants.empty') : '₹ '.@$list->total_amount }}
																</td>
																<td>
																	{{ @$list->discount == "" ? config('constants.empty') : '₹ '.@$list->discount }}
																</td>
																<td>
																	{{ @$list->pay_amount == "" ? config('constants.empty') : '₹ '.@$list->pay_amount }}
																</td>
																<td>
																	{!! @$list->response == 0 ? '<span class="btn btn-danger">Fail</span>' : '<span class="btn btn-success">Success</span>' !!}	
																</td>
																<td>
																	@if(@$list->response == 0)
																		{{config('constants.empty')}}
																	@else												
																		{!! @$list->status == 1 ? '<span class="btn btn-danger">Yes</span>' : '<span class="btn btn-success">No</span>' !!}
																	@endif		
																</td>	
																<td>
																	{{ @$list->created_at == "" ? config('constants.empty') : Carbon\Carbon::parse(@$list->created_at)->toFormattedDateString() }}
																</td>
																<td>
																	<a class="btn btn-success" href="{{URL::to('/view_test_series_order/'.base64_encode(convert_uuencode(@$list->id)))}}" data-toggle="tooltip" title="View Order History">
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
												@if(@$totalData1 !== 0)	
													{!! @$lists1->appends(\Request::except('page'))->render() !!}
												@endif
											</div>
										</div>
									</div>
								</div>	
							<!--Test Series Order Summary End -->		
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection