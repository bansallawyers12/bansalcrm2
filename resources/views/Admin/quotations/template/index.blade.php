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
.ui.yellow.label, .ui.yellow.labels .label {
    background-color: #fbbd08!important;
    border-color: #fbbd08!important;
    color: #fff!important;
}
.ui.red.label, .ui.red.labels .label {
    background-color: #db2828!important;
    border-color: #db2828!important;
    color: #fff!important;
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
							<h4>All Templates</h4>
							<div class="card-header-action">
							
								<a style="display:none;" class="btn btn-primary changestatus is_checked_client" id=""  href="javascript:;"  >Delete</a>
							
								<div class="drop_table_data is_checked_clientn" style="display: inline-block;margin-right: 10px;">
									<button type="button" class="btn btn-primary dropdown-toggle"><i class="fas fa-columns"></i></button>
									<div class="dropdown_list">
										<label class="dropdown-option all"><input type="checkbox" value="all" checked /> Display All</label>
										<label class="dropdown-option"><input type="checkbox" value="1" checked /> Products</label>
										<label class="dropdown-option"><input type="checkbox" value="2" checked /> Total Fee</label>
										
										<label class="dropdown-option"><input type="checkbox" value="3" checked /> Created On</label>
										<label class="dropdown-option"><input type="checkbox" value="4" checked /> Created By</label>
										<label class="dropdown-option"><input type="checkbox" value="5" checked /> Office</label>
										
									</div>
								</div>
								
								<a href="{{URL::to('admin/quotations/template/create')}}"  class="btn btn-primary is_checked_clientn">Create Template</a>
								<a href="javascript:;" data-toggle="modal" data-target=".create_quotation" class="btn btn-primary is_checked_clientn">Create Quotations</a>
							</div>
						</div>
						<div class="card-body">
							<ul class="nav nav-pills" id="quotation_tabs" role="tablist">
								<li class="nav-item ">
									<a class="nav-link active" id="quotation_template-tab"  href="{{URL::to('/admin/quotations/template')}}" >Quotation Template</a>
								</li>
								<li class="nav-item">
									<a class="nav-link " id="active_quotation-tab"  href="{{URL::to('/admin/quotations')}}" >Active Quotations</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="archived-tab"  href="{{URL::to('/admin/quotations/archived')}}" >Archived Quotations</a>
								</li>
							</ul>
							<div class="tab-content" id="quotationContent">								
								<div class="tab-pane fade show active" id="active_quotation" role="tabpanel" aria-labelledby="active_quotation-tab">
									<div class="table-responsive common_table"> 
										<table class="table text_wrap ">
										<thead>
											<tr>
												<th class="text-center" style="width:30px;">
														<div class="custom-checkbox custom-checkbox-table custom-control">
															<input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad" class="custom-control-input" id="checkbox-all">
															<label for="checkbox-all" class="custom-control-label">&nbsp;</label>
														</div>
												</th>
											
												<th>Template Name</th>
												<th>Products</th>
												<th>Total Fee</th>
												<th>Created By</th>
												<th>Office</th>
												
												<th>Created On</th>
												<th></th>
											</tr> 
										</thead>
										@if(@$totalData !== 0)
											<?php 	$r = 0; ?>
										@foreach (@$lists as $list)
									<?php
								
									$client = \App\Admin::where('id',$list->client_id)->where('role', 7)->first();
									$office = \App\Branch::where('id',$list->office)->first();
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
												<td class="text-center">
													<div class="custom-checkbox custom-control">
														<input data-q-id="{{@$list->id}}" data-id="{{@$client->id}}" data-email="{{@$client->email}}" data-name="{{@$client->first_name}} {{@$client->last_name}}" type="checkbox" data-checkboxes="mygroup" class="cb-element custom-control-input" id="checkbox-{{$r}}">
														<label for="checkbox-{{$r}}" class="custom-control-label">&nbsp;</label>
													</div>
												</td>
												
												<td>{{ $list->name}} </td> 
												<td>{{$countqou}}</td>
												<td>{{number_format($totfare,2,'.','')}} {{$list->currency}}</td>
												<td>{{$createdby->first_name}}</td>
												<td>{{@$office->office_name}}</td>
													
												
												<td>{{date('d/m/Y', strtotime($list->created_at))}}</td>
												
												<td>
												<a data-id="{{$list->id}}" href="javascript:;"  class="btn btn-primary openclientpopup">Use</a>
													<div class="dropdown d-inline">
														<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
														<div class="dropdown-menu">
														
															<a class="is_declined dropdown-item has-icon" href="{{URL::to('/admin/quotations/template/edit/'.base64_encode(convert_uuencode(@$list->id)))}}">Edit</a>
														
															<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction({{@$list->id}}, 'templates')">Delete</a>
														</div>
													</div>								  
												</td>
											</tr>	
											<?php $r++; ?>
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
					<input type="hidden" name="template_id" id="template_id" value="">
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
@section('scripts')
<script>
jQuery(document).ready(function($){
	$("[data-checkboxes]").each(function () {
  var me = $(this),
    group = me.data('checkboxes'),
    role = me.data('checkbox-role');

  me.change(function () {
    var all = $('[data-checkboxes="' + group + '"]:not([data-checkbox-role="dad"])'),
      checked = $('[data-checkboxes="' + group + '"]:not([data-checkbox-role="dad"]):checked'),
      dad = $('[data-checkboxes="' + group + '"][data-checkbox-role="dad"]'),
      total = all.length,
      checked_length = checked.length;

    if (role == 'dad') {
      if (me.is(':checked')) {
        all.prop('checked', true);
		$('.is_checked_client').show();
		$('.is_checked_clientn').hide();
      } else {
        all.prop('checked', false);
		$('.is_checked_client').hide();
		$('.is_checked_clientn').show();
      }
    } else {
      if (checked_length >= total) {
        dad.prop('checked', true);
			$('.is_checked_client').show();
		$('.is_checked_clientn').hide();
      } else {
        dad.prop('checked', false);
		$('.is_checked_client').hide();
		$('.is_checked_clientn').show();
      }
    }
  });
});
$('.cb-element').change(function () {
	
 if ($('.cb-element:checked').length == $('.cb-element').length){
  $('#checkbox-all').prop('checked',true);
 }
 else {
  $('#checkbox-all').prop('checked',false);
 }

 if ($('.cb-element:checked').length > 0){
		$('.is_checked_client').show();
		$('.is_checked_clientn').hide();
	}else{
		$('.is_checked_client').hide();
		$('.is_checked_clientn').show();
	}
});
	$(document).delegate('.openclientpopup', 'click', function(){
		var v = $(this).attr('data-id');
		$('.create_quotation').modal('show');
		$('#template_id').val(v);
	});
	


});
</script>
@endsection