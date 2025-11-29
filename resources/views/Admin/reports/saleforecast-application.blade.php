@extends('layouts.admin')
@section('title', 'Sales Forecast Report')

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
							<h4>Sales Forecast Report</h4>
							<div class="card-header-action">
								<div class="drop_table_data" style="display: inline-block;margin-right: 10px;"> 
									<button type="button" class="btn btn-primary dropdown-toggle"><i class="fas fa-columns"></i></button>
									<div class="dropdown_list saleforecast_applic_report_list">
										<label class="dropdown-option all"><input type="checkbox" value="all" checked /> Display All</label>
										<label class="dropdown-option"><input type="checkbox" value="3" checked /> Contact Email</label>
										<label class="dropdown-option"><input type="checkbox" value="4" checked /> Rating</label>
										<label class="dropdown-option"><input type="checkbox" value="5" checked /> Workflow</label>
										<label class="dropdown-option"><input type="checkbox" value="6" checked /> Partner</label>
										<label class="dropdown-option"><input type="checkbox" value="7" checked /> Product</label>
										<label class="dropdown-option"><input type="checkbox" value="8" checked /> Status</label>
										<label class="dropdown-option"><input type="checkbox" value="9" checked /> Stage</label>
										<label class="dropdown-option"><input type="checkbox" value="10" checked /> Added Date</label>
										<label class="dropdown-option"><input type="checkbox" value="12" checked /> Added By Office</label>
										<label class="dropdown-option"><input type="checkbox" value="13" checked /> Assignee</label>
										<label class="dropdown-option"><input type="checkbox" value="14" checked /> Client Revenue</label>
										<label class="dropdown-option"><input type="checkbox" value="15" checked /> Partner Revenue</label>
										<label class="dropdown-option"><input type="checkbox" value="16" checked /> Discount</label>
										<label class="dropdown-option"><input type="checkbox" value="18" checked /> Won Value</label>
										<label class="dropdown-option"><input type="checkbox" value="19" checked /> Won At</label>
										<label class="dropdown-option"><input type="checkbox" value="20" checked /> Expected Win Date</label>
									</div>
								</div>
							</div>
						</div>
						<div class="card-body">
							<ul class="nav nav-pills" id="checkin_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" id="forecast_application-tab"  href="{{URL::to('/admin/report/sale-forecast/application')}}" >Application</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="interested_service-tab"  href="{{URL::to('/admin/report/sale-forecast/interested-service')}}" >Interested Service</a>
								</li>								
							</ul>  
							<div class="tab-content" id="checkinContent">
								<div class="tab-pane fade show active" id="forecast_application" role="tabpanel" aria-labelledby="forecast_application-tab">
									<div class="table-responsive common_table saleforecast_application_report_data">  
										<table class="table text_wrap">
											<thead> 
												<tr>
													<th>Application ID</th>
													<th>Contact Name</th>
													<th>Contact Email</th>
													<th>Rating</th>
													<th>Workflow</th>
													<th>Partner</th>
													<th>Product</th>
													<th>Status</th>
													<th>Stage</th>
													<th>Added Date</th>
													<th>Added By</th>
													<th>Added By Office</th>
													<th>Assignee</th>
													<th>Client Revenue</th>
													<th>Partner Revenue</th>
													<th>Discount</th>
													<th>Total Forecast</th>
													<th>Won Value</th>
													<th>Won At</th>
													<th>Expected Win Date</th>
												</tr> 
											</thead>
											@if(count($lists) >0)
											<tbody class="tdata">	
												@if(@$totalData !== 0)
												<?php $i=0; ?>
												@foreach (@$lists as $list)
												<?php 
													$productdetail = \App\Product::where('id', $list->product_id)->first();
													$partnerdetail = \App\Partner::where('id', $list->partner_id)->first();		
													$clientdetail = \App\Admin::where('id', $list->client_id)->first();
													$PartnerBranch = \App\PartnerBranch::where('id', $list->branch)->first();
												?>
												<tr id="id_{{@$list->id}}"> 
													<td class="text-center">
														<div class="custom-checkbox custom-control">
															<input data-id="{{@$list->id}}" data-email="{{@$list->email}}" data-name="{{@$list->first_name}} {{@$list->last_name}}" type="checkbox" data-checkboxes="mygroup" class="cb-element custom-control-input" id="checkbox-{{$i}}">
															<label for="checkbox-{{$i}}" class="custom-control-label">&nbsp;</label>
														</div>
													</td>
													<td><a href="{{URL::to('admin/clients/detail/')}}/{{base64_encode(convert_uuencode(@$clientdetail->id))}}?tab=application&appid={{@$list->id}}">{{ @$list->id == "" ? config('constants.empty') : str_limit(@$list->id, '50', '...') }}</a></td> 
													<td><a href="{{URL::to('admin/clients/detail/')}}/{{base64_encode(convert_uuencode(@$clientdetail->id))}}">{{@$clientdetail->first_name}} {{@$clientdetail->last_name}}</a></td> 
													<td>{{ @$list->intakedate == "" ? config('constants.empty') : str_limit(@$list->intakedate, '50', '...') }} </td>
													<td><a data-id="{{@$list->id}}" data-email="{{@$clientdetail->email}}" data-name="{{@$clientdetail->first_name}} {{@$clientdetail->last_name}}" href="javascript:;" class="clientemail">{{ @$clientdetail->email == "" ? config('constants.empty') : str_limit(@$clientdetail->email, '50', '...') }}</a></td>
													<td>-</td>
													<td>{{@$clientdetail->client_id}} </td>
													<td>{{date('d/m/Y',strtotime($clientdetail->dob))}}</td>
													<td>{{@$clientdetail->phone}} </td>
													<td>{{@$clientdetail->followers}} </td>
													<td>-</td>
													<td>{{ @$list->workflow == "" ? config('constants.empty') : str_limit(@$list->workflow, '50', '...') }} </td>
													<td>{{@$partnerdetail->partner_name}}</td> 
													<td>{{@$productdetail->name}}</td> 
													<td>{{$PartnerBranch->name}}</td>
													<td>{{@$productdetail->duration}}</td> 
													<td>-</td>
													<td>-</td>
													<td>-</td>
													<td>-</td>
													<td>{{ @$list->status == "" ? config('constants.empty') : str_limit(@$list->status, '50', '...') }} </td>
													<td>-</td>
													<td>-</td>
													<td>-</td>
													<td>{{ @$list->stage == "" ? config('constants.empty') : str_limit(@$list->stage, '50', '...') }} </td>
													<td>{{@$clientdetail->assignee}} </td>
													<td>{{ @$list->created_at == "" ? config('constants.empty') : date('d/m/Y',strtotime($list->created_at)) }}</td>
													<td>{{$PartnerBranch->name}}</td>
													<td>{{@$clientdetail->source}} </td>
													<td>{{ @$list->sub_agent == "" ? config('constants.empty') : str_limit(@$list->sub_agent, '50', '...') }}</td>
													<td>{{ @$list->super_agent == "" ? config('constants.empty') : str_limit(@$list->super_agent, '50', '...') }}</td>
													<td>{{date('d/m/Y',strtotime($clientdetail->visaExpiry))}}</td>
													<td>{{ @$list->created_at == "" ? config('constants.empty') : date('d/m/Y',strtotime($list->created_at)) }}</td>
													<td>{{ @$list->start_date == "" ? config('constants.empty') : date('d/m/Y',strtotime($list->start_date)) }}</td>
													<td>{{ @$list->end_date == "" ? config('constants.empty') : date('d/m/Y',strtotime($list->end_date)) }}</td>
													<td>{{ @$list->updated_at == "" ? config('constants.empty') : date('d/m/Y',strtotime($list->updated_at)) }}</td>
												</tr>
												<?php $i++; ?>
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