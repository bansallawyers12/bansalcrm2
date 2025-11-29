@extends('layouts.admin')
@section('title', 'Income Sharing')

@section('content')
<style>
.dropdown a.dropdown-toggle:after{display:none;}
.dropdown a.dropdown-toggle{color:#000;}	
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
							<h4>Income Sharing</h4> 
						</div> 
						<div class="card-body">							
							<ul class="nav nav-pills" id="payable_tabs" role="tablist">
								<li class="nav-item is_checked_clientn">
									<a class="nav-link" id="payables-tab"  href="{{URL::to('/admin/income-sharing/payables/unpaid')}}" >Payables</a>
								</li> 
								<li class="nav-item is_checked_clientn">
									<a class="nav-link active" id="receivables-tab"  href="{{URL::to('/admin/income-sharing/receivables/unpaid')}}" >Receivables</a>
								</li> 	
							</ul> 
							<div class="tab-content" id="payableContent">
								<div class="tab-pane fade show active" id="payables" role="tabpanel" aria-labelledby="payables-tab">
									<ul class="nav nav-pills" id="paypaid_tabs" role="tablist">
										<li class="nav-item is_checked_clientn">
											<a class="nav-link active" id="unpaid-tab"  href="{{URL::to('/admin/income-sharing/receivables/unpaid')}}" >Unpaid</a>
										</li> 
										<li class="nav-item is_checked_clientn">
											<a class="nav-link" id="paid-tab"  href="{{URL::to('/admin/income-sharing/receivables/paid')}}" >Received</a>
										</li> 	
									</ul>
									<div class="tab-content" id="payableContent">
										<div class="tab-pane fade show active" id="unpaid" role="tabpanel" aria-labelledby="unpaid-tab">
											<div class="table-responsive common_table"> 
												<table class="table text_wrap">
													<thead>
														<tr> 
															<th>Client Name</th>
															<th>DOB</th>
															<th>Partner Name</th>
															<th>Product Name</th>
															<th>Amount</th> 
															<th>Tax Amount</th>
															<th>Action</th>
														</tr> 
													</thead> 
													
													<tbody class="tdata">	
														{{-- <tr>
															<td>Arun</td>
															<td>19-04-1991</td>
															<td>Santosh</td>
															<td>Education</td>
															<td>$ 200.00</td>
															<td>$ 15.00</td>
															<td style="text-align:right;">
																<div class="dropdown d-inline">
																	<a class="dropdown-toggle" href="javascript:;" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
																	<div class="dropdown-menu">
																		<a class="dropdown-item" href="#">Make Payment</a>
																		<a class="dropdown-item" href="#">Delete</a>
																	</div>
																</div>	
															</td>
														</tr> --}}
														<tr>
															<td style="text-align:center;" colspan="12">
																No Record found
															</td>
														</tr>
													</tbody>											
												</table>
											</div>
										</div>	 
									</div>	 
								</div>	 
							</div> 
						</div>
						<div class="card-footer">
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

@endsection
@section('scripts')

<script>
jQuery(document).ready(function($){ 
	
});	
</script>
@endsection