@extends('layouts.admin')
@section('title', 'Create Quotation Template')

@section('content')
<style>
.addbranch .error label{
	color: #9f3a38;
}
.addbranch .error input{
	background: #fff6f6;
    border-color: #e0b4b4;
    color: #9f3a38;
    border-radius: "";
    box-shadow: none;
}

</style>
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			{{ Form::open(array('url' => 'admin/quotations/template/edit', 'name'=>"add-quotations", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
			{{ Form::hidden('id', @$fetchedData->id) }} 
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Edit Quotations Template</h4>
								<div class="card-header-action">
									<a href="{{route('admin.quotations.template.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">							
							<div class="card-body">
								<div class="row">
									<div class="col-12 col-md-4 col-lg-4">
										<div class="form-group"> 
											<label for="template_name">Template Name <span class="span_req">*</span></label>
											{{ Form::text('template_name', @$fetchedData->name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Template Name' )) }}
											@if ($errors->has('template_name'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('template_name') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-12 col-md-4 col-lg-4">
										<div class="form-group"> 
											<label for="office">Office <span class="span_req">*</span></label>
											<select class="form-control select2" name="office">
												<option value="">Select</option>
												<?php
												$offices = \App\Branch::all();
												foreach($offices as $office){
													?>
													<option <?php if($fetchedData->office == $office->id){ echo 'selected'; } ?> value="{{$office->id}}">{{$office->office_name}}</option>
													<?php
												}
												?>
											</select>
											@if ($errors->has('office'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('office') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-12 col-md-4 col-lg-4">
										<div class="form-group"> 
											<label for="quotation_currency">Quotation Currency <span class="span_req">*</span></label>
											<div class="bfh-selectbox bfh-currencies" data-currency="{{@$fetchedData->currency}}" data-flags="true" data-name="quotation_currency"></div>
											@if ($errors->has('quotation_currency'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('quotation_currency') }}</strong>
												</span> 
											@endif
										</div>
									</div>
								</div>
							</div>
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
												<th colspan="2">Total Amt. (in None)</th>
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
													<td class="show_{{$l}}"><div class="productinfo"><a href="javascript:;" dataid="{{$l}}" class="edit_productinfo"><i class="fa fa-edit"></i></a><div class="productdet"><b>{{@$Productdata->name}}</b></div>{{@$Partnerdata->partner_name}}<div class="prodescription">({{@$workflowdata->name}})</div></div><input type="hidden" class="workflowid" name="workflowid[]" value="{{@$q->workflow}}"><input class="partnerid" type="hidden" name="partnerid[]" value="{{@$q->partner}}"><input class="productid" type="hidden" name="productid[]" value="{{@$q->product}}"><input type="hidden" name="branchid[]" class="branchid" value="{{@$q->branch}}"></td>
													<td><textarea placeholder="Description" rows="1" cols="30" class="form-control" name="description[]">{{@$q->description}}</textarea></td>
													<td>AUD</td>
													<td><input type="tel" value="{{number_format($servicefee,2,'.','')}}" placeholder="0.00" class="form-control servicefee" name="service_fee[]"></td>
													<td><input type="tel" value="{{number_format($discount,2,'.','')}}" placeholder="0.00" class="form-control discount" name="discount[]"></td>
													
													<td class="netfare">{{number_format($netfare,2,'.','')}}</td>
													<td><input type="tel" value="{{number_format($exg_rate,2,'.','')}}" class="form-control excrate" name="exg_rate[]"></td>
													<td dataprice="{{number_format($exgrw,2,'.','')}}" class="totalamt">{{number_format($exgrw,2,'.','')}}</td>
													<td class="last_td"><a href="javascript:;" class="rem_product"><i class="fa fa-times"></i></a></td>
												</tr>
												<?php
												$i++;
												$l++;
											}
											?>
										</tbody>
										
									</table>
								</div>
								<div class="add_new">
									<a href="javascript:;" class="openitems"><i class="fa fa-plus"></i> Add New Line</a>	
								</div>
								<div class="row">
									<div class="col-lg-8">
									</div>
									<div class="col-lg-4 text-right">
										<div class="invoice-detail-item">
											<div class="invoice-detail-name">Grand Total Fees (in None)</div>
											<div class="invoice-detail-value invoice-detail-value-lg">$<span  class="grandtotal">{{number_format($totfare, 2, '.','')}}</span></div>
										</div>
										<div class="float-lg-right" style="margin-top:30px;">
											<button class="btn btn-primary">Save</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="modal fade custom_modal" data-keyboard="false" data-backdrop="static" id="addproduct" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog">
	
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">Add Product</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				
			
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="workflow">Workflow <span class="span_req">*</span></label> 
								<select data-valid="required" class="form-control workflow select2" id="workflow" name="workflow">
									<option value="">Please Select Workflow</option>
									@foreach(\App\Workflow::all() as $wlist)
										<option value="{{$wlist->id}}">{{$wlist->name}}</option>
									@endforeach
								</select> 
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="partner">Partner <span class="span_req">*</span></label> 
								<select data-valid="required" class="form-control select2" id="partner" name="partner">
									<option value="">Please Select Partner</option>
									
								</select> 
								<span class="custom-error partner_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="product">Product <span class="span_req">*</span></label> 
								<select data-valid="required" class="form-control select2" id="product" name="product">
									<option value="">Please Select Product</option>
									
								</select> 
								<span class="custom-error product_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="branch">Branch <span class="span_req">*</span></label> 
								<select data-valid="required" class="form-control select2" id="branch" name="branch">
									<option value="">Please Select Branch</option>
									
								</select> 
								<span class="custom-error branch_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button  type="button" class="btn btn-primary addproduct">Add</button>
							<button style="display:none;" type="button" class="btn btn-primary editproduct">Update</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script>
jQuery(document).ready(function($){
	$(document).delegate('.openitems', 'click', function(){
		$('#addproduct #clientModalLabel').html('Add Product');
		$('#addproduct .editproduct').hide();
		$('#addproduct .addproduct').show();
		$('#addproduct').modal('show');
		$("#workflow").val('').trigger('change');
			$("#partner").val('').trigger('change');
			$("#product").val('').trigger('change');
			$("#branch").val('').trigger('change');
			
			$('.custom-error').html('');
			$('.form-group').parent().removeClass('error');
	});
	
	$(document).delegate('#workflow', 'change', function(){
	
				var v = $('#workflow option:selected').val();
				if(v != ''){
						$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getpartner')}}',
			type:'GET',
			data:{cat_id:v},
			success:function(response){
				$('.popuploader').hide();
				$('#partner').html(response);
				
				$("#partner").val('').trigger('change');
			$("#product").val('').trigger('change');
			$("#branch").val('').trigger('change');
			}
		});
				}
	});
	
	$(document).delegate('#partner','change', function(){
		
				var v = $('#partner option:selected').val();
				if(v != ''){
					$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getproduct')}}',
			type:'GET',
			data:{cat_id:v},
			success:function(response){
				$('.popuploader').hide();
				$('#product').html(response);
				$("#product").val('').trigger('change');
			$("#branch").val('').trigger('change');
			}
		});
				}
	});
	
	$(document).delegate('#product','change', function(){
		
				var v = $('#product option:selected').val();
				if(v != ''){
					$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getbranch')}}',
			type:'GET',
			data:{cat_id:v},
			success:function(response){
				$('.popuploader').hide();
				$('#branch').html(response);
				$("#branch").val('').trigger('change');
			}
		});
		}
	});
	var itag = $('.productitem tr').length;
	
	$(document).delegate('.addproduct','click', function(){
		itag++;
		var flag = false;
		var workflow = $('#workflow option:selected').val();
		var partner = $('#partner option:selected').val();
		var product = $('#product option:selected').val();
		var branch = $('#branch option:selected').val();
		
		var workflowtext = $('#workflow option:selected').text();
		var partnertext = $('#partner option:selected').text();
		var producttext = $('#product option:selected').text();
		var branchtext = $('#branch option:selected').text();
			if(workflow == ''){
				$('.workflow_error').html('The field is required.');
				$('#workflow').parent().addClass('error');
				flag = true;
			}
			if(partner == ''){
				$('.partner_error').html('The field is required.');
				$('#partner').parent().addClass('error');
				flag = true;
			}
			if(product == ''){
				$('.product_error').html('The field is required.');
				$('.product').parent().addClass('error');
				flag = true;
			}
			if(branch == ''){
				$('.branch_error').html('The field is required.');
				$('.branch').parent().addClass('error');
				flag = true;
			}
			if(!flag){
			var html = '<tr >';
				html += '<td class="sortsn"></td>';
				html += '<td class="show_'+itag+'"><div class="productinfo"><a href="javascript:;" dataid="'+itag+'" class="edit_productinfo"><i class="fa fa-edit"></i></a><div class="productdet"><b>'+producttext+'</b></div>'+partnertext+'<div class="prodescription">('+workflowtext+')</div></div><input type="hidden" class="workflowid" name="workflowid[]" value="'+workflow+'"><input class="partnerid" type="hidden" name="partnerid[]" value="'+partner+'"><input class="productid" type="hidden" name="productid[]" value="'+product+'"><input type="hidden" name="branchid[]" class="branchid" value="'+branch+'"></td>';
				html += '<td><textarea placeholder="Description" rows="1" cols="30" class="form-control" name="description[]"></textarea></td>';
				html += '<td>AUD</td>';
				html += '<td><input type="tel" placeholder="0.00" class="form-control servicefee" name="service_fee[]"></td>';
				html += '<td><input type="tel" placeholder="0.00" class="form-control discount" name="discount[]"></td>';
				html += '<td class="netfare">0.00</td>';
				html += '<td><input type="tel" value="1.00" class="form-control excrate" name="exg_rate[]"></td>';
				html += '<td dataprice="0.00" class="totalamt">0.00</td>';
				html += '<td class="last_td"><a href="javascript:;" class="rem_product"><i class="fa fa-times"></i></a></td>';
			 html += '</tr>';
			$('.productitem').append(html);
			$('#addproduct').modal('hide'); 
			sortrow(); 
			$("#workflow").val('').trigger('change');
			$("#partner").val('').trigger('change');
			$("#product").val('').trigger('change');
			$("#branch").val('').trigger('change');
			}
			
	});
	var editid = 0;
	$(document).delegate('.edit_productinfo','click', function(){
		var r = $(this).attr('dataid');
		editid = r;
		$('.custom-error').html('');
			$('.form-group').parent().removeClass('error');
			$('#addproduct #clientModalLabel').html('Edit Product');
			$('#addproduct .editproduct').show();
		$('#addproduct .addproduct').hide();
		$('#addproduct').modal('show');
		var w = $('.show_'+r+' .workflowid').val();
		var pr = $('.show_'+r+' .partnerid').val();
		var p = $('.show_'+r+' .productid').val();
		var b = $('.show_'+r+' .branchid').val();
		$("#workflow").select2().select2("val", w);
		//$("#workflow").val(w);
	// $("#partner").select2().select2("val", pr);
		if(w != ''){
			$('.popuploader').show();
			$.ajax({
				url: '{{URL::to('/admin/getpartner')}}',
				type:'GET',
				data:{cat_id:w},
				success:function(response){
					$('.popuploader').hide();
					$('#partner').html(response);
					$("#partner").val(pr);
					
				}
			});
		}
		
		//$("#partner").val(pr).trigger('change');
		if(pr != ''){
			$('.popuploader').show();
			$.ajax({
				url: '{{URL::to('/admin/getproduct')}}',
				type:'GET',
				data:{cat_id:pr},
				success:function(response){
					$('.popuploader').hide();
					$('#product').html(response);
					  $("#product").val(p);
				}
			});
		}
		
		if(p != ''){
			$('.popuploader').show();
			$.ajax({
				url: '{{URL::to('/admin/getbranch')}}',
				type:'GET',
				data:{cat_id:p},
				success:function(response){
					$('.popuploader').hide();
					$('#branch').html(response);
					 $("#branch").val(b);
				}
			});
		}
	 
		
	});
	
	
	$(document).delegate('.editproduct','click', function(){
		
		var flag = false;
		var workflow = $('#workflow option:selected').val();
		var partner = $('#partner option:selected').val();
		var product = $('#product option:selected').val();
		var branch = $('#branch option:selected').val();
		
		var workflowtext = $('#workflow option:selected').text();
		var partnertext = $('#partner option:selected').text();
		var producttext = $('#product option:selected').text();
		var branchtext = $('#branch option:selected').text();
			if(workflow == ''){
				$('.workflow_error').html('The field is required.');
				$('#workflow').parent().addClass('error');
				flag = true;
			}
			if(partner == ''){
				$('.partner_error').html('The field is required.');
				$('#partner').parent().addClass('error');
				flag = true;
			}
			if(product == ''){
				$('.product_error').html('The field is required.');
				$('.product').parent().addClass('error');
				flag = true;
			}
			if(branch == ''){
				$('.branch_error').html('The field is required.');
				$('.branch').parent().addClass('error');
				flag = true;
			}
			if(!flag){
			var html = '<div class="productinfo"><a href="javascript:;" dataid="'+editid+'" class="edit_productinfo"><i class="fa fa-edit"></i></a><div class="productdet"><b>'+producttext+'</b></div>'+partnertext+'<div class="prodescription">('+workflowtext+')</div></div><input type="hidden" class="workflowid" name="workflowid[]" value="'+workflow+'"><input class="partnerid" type="hidden" name="partnerid[]" value="'+partner+'"><input class="productid" type="hidden" name="productid[]" value="'+product+'"><input type="hidden" name="branchid[]" class="branchid" value="'+branch+'">';
				
			$('.productitem .show_'+editid).html(html);
			$('#addproduct').modal('hide');
			sortrow();
			grandtotal();
			$("#workflow").val('').trigger('change');
			$("#partner").val('').trigger('change');
			$("#product").val('').trigger('change');
			$("#branch").val('').trigger('change');
			}
			
	});
	$(document).delegate('.rem_product','click', function(){
		$(this).parent().parent().remove();
		
		sortrow();
		grandtotal();
	});
	
	function sortrow(){
		var it = 1;
		$('.productitem tr').each(function() {
			$(this).find('.sortsn').html(it);
			it++;
		});
	}
	
	
	$(document).delegate('.servicefee','keyup', function(){
		var servicefee = $(this).val();
		var cserv = 0.00;
		if(servicefee != ''){
			cserv = servicefee;
		}
		var discount = $(this).parent().parent().find('.discount').val();
		var cdis = 0.00;
		if(discount != ''){
			cdis = discount;
		}
		var excrate = $(this).parent().parent().find('.excrate').val();
		var cex = 0.00;
		if(excrate != ''){
			cex = excrate;
		}
		var netfare = parseFloat(cserv) - parseFloat(cdis);
		var totalamount = parseFloat(netfare) * parseFloat(cex);
		$(this).parent().parent().find('.netfare').html(netfare.toFixed(2));
		$(this).parent().parent().find('.totalamt').html(totalamount.toFixed(2));
		$(this).parent().parent().find('.totalamt').attr('dataprice',totalamount.toFixed(2));
		grandtotal();
	});
	
	$(document).delegate('.discount','keyup', function(){
		var servicefee = $(this).parent().parent().find('.servicefee').val();
		var cserv = 0.00;
		if(servicefee != ''){
			cserv = servicefee;
		}
		var discount = $(this).val();
		var cdis = 0.00;
		if(discount != ''){
			cdis = discount;
		}
		var excrate = $(this).parent().parent().find('.excrate').val();
		var cex = 0.00;
		if(excrate != ''){
			cex = excrate;
		}
		var netfare = parseFloat(cserv) - parseFloat(cdis);
		var totalamount = parseFloat(netfare) * parseFloat(cex);
		$(this).parent().parent().find('.netfare').html(netfare.toFixed(2));
		$(this).parent().parent().find('.totalamt').html(totalamount.toFixed(2));
		$(this).parent().parent().find('.totalamt').attr('dataprice',totalamount.toFixed(2));
		grandtotal();
	});
	
	$(document).delegate('.excrate','keyup', function(){
		var servicefee = $(this).parent().parent().find('.servicefee').val();
		var cserv = 0.00;
		if(servicefee != ''){
			cserv = servicefee;
		}
		var discount = $(this).parent().parent().find('.discount').val();
		var cdis = 0.00;
		if(discount != ''){
			cdis = discount;
		}
		var excrate = $(this).val();
		var cex = 0.00;
		if(excrate != ''){
			cex = excrate;
		}
		var netfare = parseFloat(cserv) - parseFloat(cdis);
		var totalamount = parseFloat(netfare) * parseFloat(cex);
		$(this).parent().parent().find('.netfare').html(netfare.toFixed(2));
		$(this).parent().parent().find('.totalamt').html(totalamount.toFixed(2));
		$(this).parent().parent().find('.totalamt').attr('dataprice',totalamount.toFixed(2));
		grandtotal();
	});
	
	function grandtotal(){
		var pric = 0;
		$('.productitem tr').each(function(){
			pric += parseFloat($(this).find('.totalamt').attr('dataprice'));
		});
		
		$('.grandtotal').html(pric);
	}
});
</script>
@endsection