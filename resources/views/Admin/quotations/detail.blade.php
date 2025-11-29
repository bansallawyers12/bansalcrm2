@extends('layouts.admin')
@section('title', 'Quotations')

@section('content')
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
		<div class="row">
			<div class="col-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="card-header">
						<h4>Quotation #{{@$fetchedData->id}}</h4>
						<div class="card-header-action">
							<a href="{{route('admin.quotations.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-12 col-md-4 col-lg-4">
				<div class="card">
					<div class="card-header">
						<h4>Client Details</h4>
					</div>
					<div class="card-body">
						<p>{{$fetchedData->client->first_name}} {{$fetchedData->client->last_name}}<br>{{$fetchedData->client->email}}<br>{{$fetchedData->client->address}}<br>{{$fetchedData->client->email}}</p>
					</div>
				</div>
			</div>
			<div class="col-12 col-md-8 col-lg-8 ">
				<div class="form-group text-right"> 
					<span><b>Due Date:</b> {{$fetchedData->due_date}}</span><br>
					<span><b>Quotation Currency:</b> {{$fetchedData->currency}}</span>
				</div>
			</div>
			<div class="col-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table class="table text_wrap table-striped table-hover table-md vertical_align">
								<thead> 
									<tr>
										<th>S.N.</th>
										<th>Product Info</th>
										<th>Description</th>
										<th colspan="2">Service Fee</th>
										<th>Discount</th>
										<th>Net Fee</th>
										<th>Exg. Rate</th>
										<th >Total Amt. (in None)</th>
									</tr>
								</thead>
								<tbody class="productitem">
								<?php
								$i=1;
								$l=0;
								$getq = \App\QuotationInfo::where('quotation_id',$fetchedData->id)->get();
								$totfare = 0;
								foreach($getq as $q){
									$servicefee = $q->service_fee;
									$discount = $q->discount;
									$exg_rate = $q->exg_rate;
									
									$netfare = $servicefee - $discount;
									$exgrw = $netfare * $exg_rate;
									$totfare += $exgrw;
								$workflowdata = \App\Workflow::where('id',$q->workflow)->first();	
								$Productdata = \App\Product::where('id',$q->product)->first();	
								$Partnerdata = \App\Partner::where('id',$q->partner)->first();	
									?>
									<tr >
										<td class="sortsn">{{$i}}</td>
										<td class="show_{{$l}}"><div class="productinfo"><div class="productdet"><b>{{@$Productdata->name}}</b></div>{{@$Partnerdata->partner_name}}<div class="prodescription">({{@$workflowdata->name}})</div></div></td>
										<td>{{@$q->description}}</td>
										<td>AUD</td>
										<td>{{number_format($servicefee,2,'.','')}}</td>
										<td>{{number_format($discount,2,'.','')}}</td>
										
										<td class="netfare">{{number_format($netfare,2,'.','')}}</td>
										<td>{{number_format($exg_rate,2,'.','')}}</td>
										<td>{{number_format($exgrw,2,'.','')}}</td>
										
									</tr>
									<?php
									$i++;
									$l++;
								}
								?>
							</tbody>
							
							</table>
						</div>
					</div>
				</div>
			</div>
			
			</div>
			<div class="row">
				<div class="col-lg-6">
				</div>
				<div class="col-lg-6 text-right">
					<span>(Service Fee - Discount = NetFee) x Exg. Rate = Total Amount</span>
					<div class="invoice-detail-item">
						<div class="invoice-detail-name">Grand Total Fees (in None)</div>
						<div class="invoice-detail-value invoice-detail-value-lg">$<span  class="grandtotal">{{number_format($totfare, 2, '.','')}}</span></div>
					</div>
					
				</div>
			</div>
		</div>
	</section>
</div>
@endsection