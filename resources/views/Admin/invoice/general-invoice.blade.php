@extends('layouts.admin')
@section('title', 'Create General Invoice')

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
.thumb-image {
    float:left;
    width:100px;
    position:relative;
    padding:5px;
}

</style>
<!-- Main Content -->
<div class="main-content">
	
	<section class="section">
	<div class="section-body">
	<form method="POST" action="{{ url('invoice/general-store') }}" name="invoiceform" autocomplete="off" enctype="multipart/form-data">
	@csrf
	<input type="hidden" name="client_id" value="{{$clientid}}">
	<input type="hidden" name="applicationid" value="{{$applicationid}}">
	<input type="hidden" name="type" value="{{$type}}">
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Create General Invoice</h4>
								<div class="card-header-action">
									<a href="{{URL::to('invoice/unpaid')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
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
								<div class="invoice_info">
									<ul>
										<li><span>Name:</span> {{$clientdata->first_name}} {{$clientdata->last_name}}</li>
										<li><span>Client ID:</span> {{$clientdata->client_id}}</li>
										<li><span>DOB:</span> {{$clientdata->dob}}</li>
										<li><span>Assignee:</span> {{Auth::user()->first_name}}</li>
										<li><span>Service:</span> {{@$workflowdaa->name}}</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-4 col-lg-4 offset-4">
						<div class="card">							
							<div class="card-body">
								<div class="form-group"> 
									<label for="invoice_date">Invoice Date:</label>
									{!! Form::date('invoice_date', date('Y-m-d'), array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Select Date' ))  !!}
									{{-- <span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span> --}}
									@if ($errors->has('invoice_date'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('invoice_date') }}</strong>
										</span> 
									@endif
								</div>
								<div class="form-group"> 
									<label for="invoice_due_date">Invoice Due Date:</label>
									{!! Form::date('invoice_due_date', date('Y-m-d'), array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Select Date' ))  !!}
									{{-- <span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span> --}}
									@if ($errors->has('invoice_due_date'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('invoice_due_date') }}</strong>
										</span> 
									@endif
								</div>
							<div class="form-group"> 
								<label for="currency_display">Currency <span class="span_req">*</span></label>
								<input type="hidden" name="currency" value="AUD" data-valid="required">
								<input class="form-control" id="currency_display" type="text" value="Australian dollar (AUD)" readonly>
								@if ($errors->has('currency'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('currency') }}</strong>
									</span> 
								@endif
							</div>
								<div class="form-group"> 
									<label for="invoice_due_date">Select Profile:</label>
									<select class="form-control" name="profile">
										@foreach(\App\Models\Profile::all() as $profiles)
											<option value="{{$profiles->id}}">{{$profiles->company_name}}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-body">
								<div class="table-responsive">
									<table class="table text_wrap table-striped table-hover table-md vertical_align invoice-line-items">
										<thead> 
											<tr>
												<th></th>
												<th>Description</th>
												<th>Income Type</th>
												<th>Amount ($)</th>
												<th>Tax (%)</th>
												<th>GST Amount ($)</th>
												<th>Total Amount ($)</th>
											</tr>
										</thead>
										<tbody class="productitem">
											<tr class="clonedrow">
												<td><a href="#"><i class="fa fa-drag"><i></a></td>
												<td><input class="form-control" type="text" name="description[]" value="Tuition Fee" data-valid="required"/></td>
												<td>
													<select name="income_type[]" class="form-control income_type" data-valid="required">
														<option value="">Select</option>
														<option value="Income">Income</option>
														<option value="Payables">Payables</option>
													</select>
												</td>
												<td>
													<input class="form-control amount"  name="amount[]" type="number" step="0.01" data-valid="required" />
												</td>
												<td>
													<select name="tax_code[]" class="form-control tax_per">
														<option value="0">No GST</option>
														<option value="10">GST 10%</option>
													</select>
												</td>
												<td>
													<input class="form-control tax_amt" name="tax_amt[]" type="text" readonly />
												</td>
												<td>
													<input class="form-control totlamt" name="total_amt[]" type="text" readonly />
												</td>
												<td>
												<a class="removeitems" href="javascript:;"><i class="fa fa-times"></i></a>
											</td>
											</tr>
										</tbody>										
									</table>
								</div>
								<div class="add_new">
									<a href="javascript:;" class="openproductrinfo"><i class="fa fa-plus"></i> Add New Line</a>	
								</div>
								<div class="row bottom_aligned">
									<div class="col-md-7 cus_col_7">
										<div class="invoiceNetResult"> 
											<span>Total Payables</span>
											<p id="totalFee" class="invoiceNetAmount"><span>$</span>0.00</p>
											<input type="hidden" id="invoice_net_amount" name="invoice_net_amount" placeholder="" class="" />
										</div>
										<div class="invoiceNetResult">
											<span>Total Income</span>
											<p class="invoiceNetAmount_2"><span>$</span>0.00</p>
											<input type="hidden" id="invoice_net_income" name="invoice_net_income" placeholder="" class="" />
										</div>
									</div>
									<div class="col-md-5 cus_col_5">
										<div class="invoiceInformationDiv">
											<div class="inline_field">
												<label>Total Amount:</label>
												<span>$</span>
												<div class="label_input">
													<input type="hidden" name="total" value="0">
													<input type="text" id="commissionClaimed" readonly="readonly">
													<div class="basic_label">AUD</div>
												</div> 
											</div>
											<div class="inline_field">
												
												<label>GST:</label>
												<span>$</span>
												<div class="label_input">
													<input type="hidden" name="total_tax" value="0">
													<input type="text" id="gst" readonly="readonly"> <div class="basic_label">AUD</div>
												</div>
											</div>
											<div class="inline_field">
												<label>Total Amount (incl GST):<span>$</span></label>
												<div class="label_input">
													<input type="hidden" name="invoice_amount" value="0">
													<input type="text" id="netFeePaid" readonly="readonly">
													<input type="hidden" name="total" value="0">
													<div class="basic_label">AUD</div>
												</div>
											</div>
											<div class="inline_field">
												<label>Total Paid</label>
												<span>$</span>
												<div class="label_input">
													<input type="text" id="totalpaid" readonly="readonly">
													<div class="basic_label">AUD</div>
												</div>
											</div>
											<div class="inline_field">
												<label>Total Due:</label>
												<span>$</span>
												<div class="label_input">
													<input type="text" id="totaldue" readonly="readonly">
													<div class="basic_label">AUD</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-7"></div>
									<div class="col-lg-5 cus_col_5"> 
										<div class="payment_detail">
											<label>Select Payment Option</label>
											<select name="paymentoption" class="form-control">
												<option value="">Select Income Type</option>
												<option value="Income">Income</option>
												<option value="Payables">Payables</option>
											</select>
										</div>
									</div>
								</div>
								<div class="divider"></div>
								<div class="row">
									<div class="col-lg-6">
										<div class="payment_check">
											<h4>Payments Received 
											<span class="float-end">
												<span class="pay_checkbox">
													<input type="checkbox" id="payment_done" name="payment_done" tabindex="0" class="hidden">
													<label>Mark this invoice as paid</label>
												</span>
											</span></h4>
											<div class="payment_field">
												<div class="payment_field_row">
													<div class="payment_field_col payment_first_step">
														<div class="field_col">
															<div class="label_input">
															<span>$</span>
																<input type="number" name="payment_amount[]" placeholder="" class="paymentAmount" />
																<div class="basic_label">AUD</div>
															</div>
														</div>
														<div class="field_col">
															<div class="input-group">
																<div class="input-group-prepend">
																	<div class="input-group-text">
																		<i class="fas fa-clock"></i>
																	</div>
																</div>	
																<input type="date" name="payment_date[]" placeholder="Date" class=" form-control"value="{{date('Y-m-d')}}"/>
															</div>
															{{-- <span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span> --}}
														</div>
														<div class="field_col">
															<select name="payment_mode[]" class="form-control">
																<option value="Cheque">Cheque</option>
																<option value="Cash">Cash</option>
																<option value="Credit Card">Credit Card</option>
																<option value="Bank Transfers">Bank Transfers</option>
															</select>
														</div>
														<div class="field_remove_col">
															<a href="javascript:;" class="remove_col"><i class="fa fa-times"></i></a>
														</div>
													</div>
												</div>
												<div class="add_payment_field">
													<a href="javascript:;"><i class="fa fa-plus"></i> Add New Line</a>
												</div>
												<div class="clearfix"></div>
											</div>
										</div> 
									</div>
									<div class="col-lg-3">
										<div class="add_notes">
											<h4>Add Notes</h4>
											<textarea class="form-control" name="notes"></textarea>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="attachment">
											<h4>Attachments</h4>
											<div class="invoice_attach_file">
												<span>Click to upload</span>
												<input name="attachfile[]" class="attachfile" type="file" multiple><br/>
												<div id="image-holder"></div>
											</div>
										</div>
									</div>
								</div>
								<div class="divider"></div>
								<div class="income_sharing">
									<div class="row">
										<div class="col-lg-12">
											<h4>Income Sharing</h4>
										</div>
										<div class="col-lg-8">
											<div class="income_field">
												<div class="income_col">
													<select class="form-control" id="share_user" name="share_user">
													<option value="no">Select a receiver</option>
													<?php
													$branches = \App\Models\Branch::where('id','!=', '1')->get();
													foreach($branches as $branch){
													?>
														<option value="{{$branch->id}}">{{$branch->office_name}}</option>
													<?php } ?>
														<option value="no">None</option>
													</select>
												</div>
												<div class="income_col">	
													<div class="label_input">
														<span>$</span>
														<input disabled type="number" name="incomeshare_amount" placeholder="Amount" class="incomeAmount" />
														<div class="basic_label">AUD</div>
													</div>
												</div>
												<div class="tax_col">
													<div class="form-check form-check-inline">
														<input disabled class="form-check-input" type="checkbox" id="taxval" value="1" name="taxval">
														<label class="form-check-label" for="taxval">GST</label>
													</div>
													{{--<div class="input_field">
														<input readonly class="form-control" type="text" />
													</div>--}}
												</div>
											</div>
										</div>
										<div class="col-lg-4">
											<div class="tax_amount_field">
												<div class="tax_amount">
													<span>GST Amount: </span>
													<span>$</span>
													<span>0.00</span>
												</div>
												<div class="tax_amount">
													<span>Total Including GST: </span>
													<span>$</span>
													<span>0.00</span>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="divider"></div>
								<div class="row">
									<div class="col-md-12">
										<div class="invoice_btns text-end">
										<input type="hidden" name="btn" value="save">
											<button type="button" class="btn btn-success" onclick="customValidate('invoiceform','savepreview')">Save & Preview</button>
											<button class="btn btn-primary" onclick="customValidate('invoiceform','save')" type="button">Save</button>
											<!--<button class="btn btn-primary">Save & Send</button>
											<button class="btn btn-secondary">Cancel</button>-->
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

@endsection
@section('scripts')
<script>
jQuery(document).ready(function($){
	$(document).delegate('#share_user','change', function(){
		if($('#share_user option:checked').val() != ''){
			$('.incomeAmount').prop('disabled',false);
			$('#taxval').prop('disabled',false);
		}else{
			$('.incomeAmount').prop('disabled',true);
			$('#taxval').prop('disabled',true);
		}	
	});
	$(document).delegate('.amount','keyup', function(){
		grandtotal();
	});
	$(document).delegate('.income_type','change', function(){
			grandtotal();		
		});
	$(document).delegate('.tax_per','change', function(){
		grandtotal();
	});
		
		$(document).delegate('#payment_done','change', function(){
			if($('#payment_done').is(':checked')){
				$('.paymentAmount').prop('readonly', true);
				$('.payment_field_clone').remove();
			}else{
				$('.paymentAmount').val(0);
					$('.paymentAmount').prop('readonly', false);
			}
			
			grandtotal();	
		});
		
		
		$(document).delegate('.paymentAmount','keyup', function(){
			grandtotal();	
		});
	function grandtotal(){
		
		var paymentAmount = 0;
		var pric = 0;
		var tot_amt = 0;
		var taxTotal = 0;
		$('.productitem tr').each(function(){
			var amountVal = 0;
			if($(this).find('.amount').val() != ''){
				amountVal = parseFloat($(this).find('.amount').val());
			}
			if(isNaN(amountVal)){
				amountVal = 0;
			}
			var taxRate = parseFloat($(this).find('.tax_per option:selected').val());
			if(isNaN(taxRate)){
				taxRate = 0;
			}
			var rowTax = (amountVal * taxRate) / 100;
			var rowTotal = amountVal + rowTax;
			
			if($(this).find('.income_type option:selected').val() == 'Income'){
				var ssss = amountVal;
			}else{
				var ssss = 0;
			}
			pric += parseFloat(ssss);
			if($(this).find('.income_type option:selected').val() == 'Payables'){
				var ss = amountVal;
			}else{
				var ss = 0;
			}
			
			paymentAmount += parseFloat(ss);
		
	
			$(this).find('.tax_amt').val(rowTax.toFixed(2));
			$(this).find('.totlamt').val(rowTotal.toFixed(2));
			tot_amt += rowTotal;
			taxTotal += rowTax;
		});
		var p =0;
		if($('#payment_done').is(':checked')){
			
			$('.paymentAmount').val(tot_amt.toFixed(2));
			p = tot_amt;
		}else{
			
				
			$('.paymentAmount').each(function(){
				if($(this).val() != ''){
					p += parseFloat($(this).val());
				}
			});
		}
		$('.invoiceNetAmount').html(paymentAmount.toFixed(2));
		$('#invoice_net_amount').val(paymentAmount.toFixed(2));
		$('.invoiceNetAmount_2').html(pric.toFixed(2));
		$('#invoice_net_income').val(pric.toFixed(2));
		$('#commissionClaimed').val(tot_amt.toFixed(2));
		$('#netFeePaid').val(tot_amt.toFixed(2));
		$('#totalpaid').val(p.toFixed(2));
		
		var totaldue = parseFloat(tot_amt) - parseFloat(p);
		$('#totaldue').val(totaldue.toFixed(2));
		$('#gst').val(taxTotal.toFixed(2));
	}
	$('.add_payment_field a').on('click', function(){
		if($('#payment_done').is(':checked')){
			alert('Received amount exceeded total fee.');
		}else{
		var clonedval = $('.payment_field .payment_field_row .payment_first_step').html();
		$('.payment_field .payment_field_row').append('<div class="payment_field_col payment_field_clone">'+clonedval+'</div>');
		}
	}); 
	$(document).delegate('.payment_field_col .field_remove_col a.remove_col', 'click', function(){ 
		var $tr    = $(this).closest('.payment_field_clone');
		var trclone = $('.payment_field_clone').length;		
		if(trclone > 0){
			$tr.remove();
		} 
	});
	
	$(document).delegate('.openproductrinfo', 'click', function(){
		
			var clonedval = $('.clonedrow').html();
			$('.productitem').append('<tr class="product_field_clone">'+clonedval+'</tr>');
		
	}); 
	$(document).delegate('.removeitems', 'click', function(){ 
		var $tr    = $(this).closest('.product_field_clone');
		var trclone = $('.product_field_clone').length;		
		if(trclone > 0){
			$tr.remove();
			grandtotal();
		} 
	});
	
	$('.attachfile').on('change',function(){
         //Get count of selected files
		var countFiles = $(this)[0].files.length;

var imgPath = $(this)[0].value;
var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
var image_holder = $("#image-holder");
image_holder.empty();

if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg" || extn == "pdf" || extn =="'docx'") {
	if (typeof (FileReader) != "undefined") {

		//loop for each file selected for uploaded.
		for (var i = 0; i < countFiles; i++) {

			var reader = new FileReader();
			reader.onload = function (e) {
						if(extn == "pdf" || extn == "docx"){
							$("<iframe />", {
							"src": e.target.result,
								"class": "thumb-image"
							}).appendTo(image_holder);
						}else{
							$("<img />", {
							"src": e.target.result,
								"class": "thumb-image"
						}).appendTo(image_holder);
						}
						
					}

			image_holder.show();
			reader.readAsDataURL($(this)[0].files[i]);
		}

	} else {
		alert("This browser does not support FileReader.");
	}
} else {
	alert("Pls select only images");
}
    });
	
}); // End document.ready

// Force hide loaders after DOM is fully loaded
$(document).ready(function() {
	$(".loader").fadeOut("slow");
	$(".popuploader").fadeOut("slow");
});
</script>
@endsection