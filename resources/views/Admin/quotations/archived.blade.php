@extends('layouts.admin')
@section('title', 'Quotations')

@section('content')
<style>
.ui.label {
    display: inline-block;
    line-height: 1;
    vertical-align: baseline;
    margin: 0 0.14285714em;
    background-color: #e8e8e8;
    background-image: none;
    padding: 0.5833em 0.833em;
    color: rgba(0,0,0,.6);
    text-transform: none;
    font-weight: 700;
    border: 0 solid transparent;
    border-radius: 0.28571429rem;
    -webkit-transition: background .1s ease;
    transition: background .1s ease;
}
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
							<h4>All Quotations</h4>
							<div class="card-header-action">
								<a href="javascript:;" data-toggle="modal" data-target=".create_quotation" class="btn btn-primary">Create Quotations</a>
							</div>
						</div>
						<div class="card-body">
							<ul class="nav nav-pills" id="quotation_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link" id="quotation_template-tab"  href="{{URL::to('/admin/quotations/template')}}" >Quotation Template</a>
								</li>
								<li class="nav-item">
									<a class="nav-link " id="active_quotation-tab"  href="{{URL::to('/admin/quotations')}}" >Active Quotations</a>
								</li>
								<li class="nav-item">
									<a class="nav-link active" id="archived-tab"  href="{{URL::to('/admin/quotations/archived')}}" >Archived Quotations</a>
								</li>
							</ul>
							<div class="tab-content" id="quotationContent">								
								<div class="tab-pane fade show active" id="active_quotation" role="tabpanel" aria-labelledby="active_quotation-tab">
									<div class=""> 
										<table class="table text_wrap">
										<thead>
											<tr>
												
												<th>No</th>
												<th>Client Name</th>
												
												<th>Total Fee</th>
											
												<th>Due Date</th>
												
												<th></th>
											</tr> 
										</thead>
										@if(@$totalData !== 0)
										@foreach (@$lists as $list)
									<?php
									$client = \App\Admin::where('id',$list->client_id)->where('role', 7)->first();
									$createdby = \App\Admin::where('id',$list->user_id)->first();
									$countqou = \App\QuotationInfo::where('quotation_id',$list->id)->count();
									$getq = \App\QuotationInfo::where('quotation_id',$list->id)->get();
									$totfare = 0;
									foreach($getq as $q){
										$servicefee = $q->service_fee;
										$discount = $q->discount;
										$exg_rate = $q->exg_rate;
										
										$netfare = $servicefee - $discount;
										$exgrw = $netfare * $exg_rate;
										$totfare += $exgrw;
									}
									?>
										<tbody class="tdata">	
											<tr id="id_{{@$list->id}}">
												
												<td>{{@$list->id}}</td>
												<td>{{ $client->first_name}} {{$client->last_name}}<br/>{{ @$client->email == "" ? config('constants.empty') : str_limit(@$client->email, '50', '...') }}</td> 
												
												<td>{{number_format($totfare,2,'.','')}} {{$list->currency}}</td>
													
												<td>{{date('d/m/Y', strtotime($list->due_date))}}</td>
												
												<td>
													<div class="dropdown d-inline">
														<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
														<div class="dropdown-menu">
															
															
															<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction({{@$list->id}}, 'quotations')">Delete</a>
														</div>
													</div>								  
												</td>
											</tr>	
										@endforeach	
										</tbody>
										@else
										<tbody>
											<tr>
												<td style="text-align:center;" colspan="10">
													No Record found
												</td>
											</tr>
										</tbody>
										@endif
									</table> 
								</div>
								<div class="card-footer">
									
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>


<div class="modal fade create_quotation custom_modal" tabindex="-1" role="dialog" aria-labelledby="quotationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="quotationModalLabel">Create Quotation</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="get" action="{{URL::to('/admin/quotations/client/')}}" name="quotationform" autocomplete="off" enctype="multipart/form-data">
			
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="client_name">Choose Client <span class="span_req">*</span></label> 
								<select data-valid="required" class="form-control client_name select2" name="client_name">
									<option value="">Select</option>
									@foreach(\App\Admin::where('role',7)->get() as $list)
									<option value="{{$list->id}}">{{$list->first_name}} {{$list->last_name}}</option>
									@endforeach
								</select> 
								<span class="custom-error client_name_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('quotationform')" type="button" class="btn btn-primary">Create</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


@endsection