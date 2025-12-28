
<?php $__env->startSection('title', 'Edit General Invoice'); ?>

<?php $__env->startSection('content'); ?>
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
	<form method="POST" action="<?php echo e(url('admin/invoice/general-edit')); ?>" name="invoiceform" autocomplete="off" enctype="multipart/form-data">
	<?php echo csrf_field(); ?>
	<input type="hidden" name="id" value="<?php echo e($invoicedetail->id); ?>">
	<input type="hidden" name="client_id" value="<?php echo e($invoicedetail->client_id); ?>">
	<input type="hidden" name="applicationid" value="<?php echo e($invoicedetail->application_id); ?>">
	<input type="hidden" name="type" value="<?php echo e($invoicedetail->type); ?>">
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Create General Invoice</h4>
								<div class="card-header-action">
									<a href="<?php echo e(URL::to('admin/invoice/unpaid')); ?>" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
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
										<li><span>Name:</span> <?php echo e($clientdata->first_name); ?> <?php echo e($clientdata->last_name); ?></li>
										<li><span>DOB:</span> <?php echo e($clientdata->dob); ?></li>
										<li><span>Assignee:</span> <?php echo e(Auth::user()->first_name); ?></li>
										<li><span>Service:</span> <?php echo e(@$workflowdaa->name); ?></li>
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
									<?php echo Form::text('invoice_date', $invoicedetail->invoice_date, array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )); ?>

									<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
									<?php if($errors->has('invoice_date')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('invoice_date')); ?></strong>
										</span> 
									<?php endif; ?>
								</div>
								<div class="form-group"> 
									<label for="invoice_due_date">Invoice Due Date:</label>
									<?php echo Form::text('invoice_due_date', $invoicedetail->due_date, array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )); ?>

									<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
									<?php if($errors->has('invoice_due_date')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('invoice_due_date')); ?></strong>
										</span> 
									<?php endif; ?>
								</div>
								<div class="form-group"> 
									<label for="currency">Currency <span class="span_req">*</span></label>
									<div class="bfh-selectbox bfh-currencies" data-currency="<?php echo e($invoicedetail->currency); ?>" data-flags="true" data-name="currency"></div>
									<?php if($errors->has('currency')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('currency')); ?></strong>
										</span> 
									<?php endif; ?>
								</div>
								<div class="form-group"> 
									<?php
								$profile = json_decode($invoicedetail->profile);
								?>
									<label for="invoice_due_date">Select Profile:</label>
									<select class="form-control" name="profile">
										<?php $__currentLoopData = \App\Models\Profile::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profiles): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<option <?php if(@$profile->id == $profiles->id): ?>  selected <?php endif; ?> value="<?php echo e($profiles->id); ?>"><?php echo e($profiles->company_name); ?></option>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-body">
								<div class="table-responsive">
									<table class="table text_wrap table-striped table-hover table-md vertical_align" id="productitemview">
									<?php
										$invoiceitemdetails = \App\Models\InvoiceDetail::where('invoice_id', $invoicedetail->id)->orderby('id','ASC')->get();
										$coom_amt = 0;
										$total_fee = 0;
										$netamount = 0;
										?>
										<thead> 
											<tr>
												<th></th>
												<th>Description</th>
												<th>Income Type</th>
												<th>Amount</th>
												<th>Tax</th>
												<th>Tax Amount</th>
												<th>Total Amount</th>
											</tr>
										</thead>
										<tbody class="productitem">
										<?php
										$ir = 0;
										foreach($invoiceitemdetails as $invoiceitemdetail){
											$coom_amt += $invoiceitemdetail->comm_amt;
											$total_fee += $invoiceitemdetail->total_fee;
											$netamount += $invoiceitemdetail->netamount;
										?>
											<tr class="<?php if($ir == 0){ ?>clonedrow<?php }else{?>product_field_clone<?php } ?>">
												<td><a href="#"><i class="fa fa-drag"><i></a></td>
												<td><input class="form-control" type="text" value="<?php echo e($invoiceitemdetail->description); ?>" name="description[]" /></td>
												<td>
													<select name="income_type[]" class="form-control income_type">
														<option value="">Select Income Type</option>
														<option <?php if($invoiceitemdetail->income_type == 'Income'){ echo 'selected'; } ?> value="Income">Income</option>
														<option <?php if($invoiceitemdetail->income_type == 'Payables'){ echo 'selected'; } ?> value="Payables">Payables</option>
													</select>
												</td>
												<td>
													<input class="form-control amount" value="<?php echo e($invoiceitemdetail->total_fee); ?>" name="amount[]" type="number" step="0.01" />
												</td>
												<td>
													<select name="tax_code[]" class="form-control">
														<option value="">Select A Tax Code</option>
													</select>
												</td>
												<td>
													<input class="form-control tax_amt" value="<?php echo e($invoiceitemdetail->tax_amount); ?>"  name="tax_amt[]" type="text" readonly />
												</td>
												<td class="last_td">
													<input class="form-control totlamt" name="total_amt[]" type="text" value="<?php echo e($invoiceitemdetail->netamount); ?>" readonly />
													<?php if($ir != 0){ ?>
													<a class="removeitems" href="javascript:;"><i class="fa fa-times"></i></a>
													<?php } ?>
												</td>
											</tr>
										<?php $ir++; } ?>
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
											<p id="totalFee" class="invoiceNetAmount"><?php echo e($invoicedetail->net_fee_rec); ?></p>
											<input type="hidden" id="invoice_net_amount" name="invoice_net_amount" value="<?php echo e($invoicedetail->net_fee_rec); ?>" placeholder="" class="" />
										</div>
										<div class="invoiceNetResult">
											<span>Total Income</span>
											<p class="invoiceNetAmount_2"><?php echo e($invoicedetail->net_incone); ?></p>
											<input type="hidden" id="invoice_net_income" name="invoice_net_income" value="<?php echo e($invoicedetail->net_incone); ?>" placeholder="" class="" />
										</div>
									</div>
									<?php
									$totaldue =0;
									$paymentdetails = \App\Models\InvoicePayment::where('invoice_id', $invoicedetail->id)->orderby('created_at', 'DESC')->get();
											$amount_rec = 0;
											foreach($paymentdetails as $paymentdetail){
												$amount_rec += $paymentdetail->amount_rec;
											}
									?>
									<div class="col-md-5 cus_col_5">
										<div class="invoiceInformationDiv">
											<div class="inline_field">
												<label>Total Amount:</label>
												<div class="label_input">
													<input type="hidden" name="total" value="<?php echo e($netamount); ?>">
													<input type="text" id="commissionClaimed" value="<?php echo e($netamount); ?>" readonly="readonly">
													<div class="basic_label">AUD</div>
												</div> 
											</div>
											<div class="inline_field">
												<label>Tax:</label>
												<div class="label_input">
													<input type="hidden" name="total_tax" value="0">
													<input type="text" id="gst" readonly="readonly"> <div class="basic_label">AUD</div>
												</div>
											</div>
											<div class="inline_field">
												<label>Total Amount (incl Tax):</label>
												<div class="label_input">
													<input type="hidden" name="invoice_amount" value="<?php echo e($netamount); ?>">
													<input type="text" id="netFeePaid" value="<?php echo e($netamount); ?>" readonly="readonly">
													<input type="hidden" name="total" value="<?php echo e($netamount); ?>">
													<div class="basic_label">AUD</div>
												</div>
											</div>
											<div class="inline_field">
												<label>Total Paid</label>
												<div class="label_input">
													<input type="text" id="totalpaid" value="<?php echo e($amount_rec); ?>" readonly="readonly">
													<div class="basic_label">AUD</div>
												</div>
											</div>
											<?php
											$totaldue = $netamount - $amount_rec;
											?>
											<div class="inline_field">
												<label>Total Due:</label>
												<div class="label_input">
													<input type="text" id="totaldue" value="<?php echo e($totaldue); ?>" readonly="readonly">
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
												<option <?php if($invoicedetail->payment_option == 'Income'){ echo 'selected'; } ?> value="Income">Income</option>
												<option <?php if($invoicedetail->payment_option == 'Payables'){ echo 'selected'; } ?> value="Payables">Payables</option>
											</select>
										</div>
									</div>
								</div>
								<div class="divider"></div>
								<div class="row">
									<div class="col-lg-6">
										<div class="payment_check">
											<h4>Payments Received 
											<span class="float-right">
												<span class="pay_checkbox">
													<input type="checkbox" id="payment_done" name="payment_done" tabindex="0" class="hidden">
													<label>Mark this invoice as paid</label>
												</span>
											</span></h4>
											<div class="payment_field">
												<div class="payment_field_row">
												<?php
												$paymentdetails = \App\Models\InvoicePayment::where('invoice_id', $invoicedetail->id)->orderby('created_at', 'DESC')->get();
												$totlacount = \App\Models\InvoicePayment::where('invoice_id', $invoicedetail->id)->orderby('created_at', 'DESC')->count();
												$ir = 0;
												if($totlacount !== 0){
													foreach($paymentdetails as $paymentdetail){
												?>
													<div class="payment_field_col <?php if($ir == 0){ ?>payment_first_step<?php }else{?>payment_field_clone<?php } ?>">
														<div class="field_col">
															<div class="label_input">
																<input type="number" name="payment_amount[]" value="<?php echo e($paymentdetail->amount_rec); ?>" placeholder="" class="paymentAmount" />
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
																<input type="text" name="payment_date[]" value="<?php echo e($paymentdetail->payment_date); ?>" placeholder="Received Amount" class="datepicker form-control" />
															</div>
															<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
														</div>
														<div class="field_col">
															<select name="payment_mode[]" class="form-control">
																<option <?php if($paymentdetail->payment_mode == 'Cheque'){ echo 'selected'; } ?> value="Cheque">Cheque</option>
																<option <?php if($paymentdetail->payment_mode == 'Cash'){ echo 'selected'; } ?> value="Cash">Cash</option>
																<option <?php if($paymentdetail->payment_mode == 'Credit Card'){ echo 'selected'; } ?> value="Credit Card">Credit Card</option>
																<option <?php if($paymentdetail->payment_mode == 'Bank Transfers'){ echo 'selected'; } ?> value="Bank Transfers">Bank Transfers</option>
															</select>
														</div>
														<div class="field_remove_col">
															<a href="javascript:;" class="remove_col"><i class="fa fa-times"></i></a>
														</div>
													</div>
													<?php $ir++; } ?>
													<?php }else{
														?>
														<div class="payment_field_col payment_first_step">
														<div class="field_col">
															<div class="label_input">
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
																<input type="text" name="payment_date[]" placeholder="Received Amount" class="datepicker form-control" />
															</div>
															<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
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
														<?php
													} ?>
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
											<textarea name="notes" class="form-control"><?php echo e($invoicedetail->notes); ?></textarea>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="attachment">
											<h4>Attachments</h4>
											<input type="hidden" id="old_attachments" name="old_attachments" value="<?php echo e(@$invoicedetail->attachments); ?>" />
											<div class="invoice_attach_file">
												<span>Click to upload</span>
												<input name="attachfile" class="attachfile" type="file">
												<p class="showfilename"><?php echo e(@$invoicedetail->attachments); ?></p>
												
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
										<?php
										$IncomeSharing = \App\Models\IncomeSharing::where('invoice_id',$invoicedetail->id)->first();
										?>
			<div class="col-lg-8">
				<div class="income_field">
					<div class="income_col">
						<select class="form-control" id="share_user" name="share_user">
						<option <?php if($IncomeSharing && $IncomeSharing == $branch->id){ echo 'selected'; } ?> value="no">Select a receiver</option>
						<?php
						$branches = \App\Models\Branch::where('id','!=', '1')->get();
						foreach($branches as $branch){
						?>
							<option <?php if($IncomeSharing && $IncomeSharing->rec_id == $branch->id){ echo 'selected'; } ?> value="<?php echo e($branch->id); ?>"><?php echo e($branch->office_name); ?></option>
						<?php } ?>
							<option value="no">None</option>
						</select>
					</div>
					<div class="income_col">
						<div class="label_input">
							<input disabled type="number" name="incomeshare_amount" placeholder="Amount" class="incomeAmount" />
							<div class="basic_label">AUD</div>
						</div>
					</div>
					<div class="tax_col">
						<div class="form-check form-check-inline">
							<input disabled class="form-check-input" type="checkbox" id="taxval" value="1" name="taxval">
							<label class="form-check-label" for="taxval">Tax</label>
						</div>
						
					</div>
				</div>
			</div>
										<div class="col-lg-4">
											<div class="tax_amount_field">
												<div class="tax_amount">
													<span>Tax Amount: </span>
													<span>0.00</span>
												</div>
												<div class="tax_amount">
													<span>Total Including Tax: </span>
													<span>0.00</span>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="divider"></div>
								<div class="row">
									<div class="col-md-12">
										<div class="invoice_btns text-right">
										<input type="hidden" name="btn" value="save">
											<button class="btn btn-success" onclick="customValidate('invoiceform','savepreview')" type="button">Save & Preview</button>
											<button class="btn btn-primary" onclick="customValidate('invoiceform','save')" type="button">Update</button>
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

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
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
		var total_fee = $(this).val(); 
		
		$(this).parent().parent().find('.totlamt').val(total_fee);
			grandtotal();
		
	});
	$(document).delegate('.income_type','change', function(){
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
		$('.productitem tr').each(function(){
		
			if($(this).find('.income_type option:selected').val() == 'Income'){
				if($(this).find('.amount').val() != ''){
					var ssss = $(this).find('.amount').val();
				}else{
					var ssss = 0;
				}
			}else{
				var ssss = 0;
			}
			pric += parseFloat(ssss);
			if($(this).find('.income_type option:selected').val() == 'Payables'){
				if($(this).find('.amount').val() != ''){
					var ss = $(this).find('.amount').val();
				}else{
					var ss = 0;
				}
			}else{
				var ss = 0;
			}
			
			paymentAmount += parseFloat(ss);
		
	
			if($(this).find('.totlamt').val() != ''){
					var ssq = $(this).find('.totlamt').val();
				}else{
					var ssq = 0;
				}
				
					tot_amt += parseFloat(ssq);
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
		$('.tax_amt').val(0.00);
		$('#gst').val(0.00);
	}
	$('.add_payment_field a').on('click', function(){
		if($('#payment_done').is(':checked')){
			alert('Received amount exceeded total fee.');
		}else{
				var $tableBody = $('.payment_field_row'),
$trLast = $tableBody.find(".payment_field_col:last"),
$trNew = $trLast.clone();
$trNew.find('input').val('');
$trNew.find('select').val('');
$trLast.after($trNew);
		/* var clonedval = $('.payment_field .payment_field_row .payment_first_step').html();
		$('.payment_field .payment_field_row').append('<div class="payment_field_col payment_field_clone">'+clonedval+'</div>'); */
		}
	}); 
	$(document).delegate('.payment_field_col .field_remove_col a.remove_col', 'click', function(){ 
		$(this).parent().parent().remove();
		
			grandtotal();
	});
	
	$(document).delegate('.openproductrinfo', 'click', function(){
		var $tableBody = $('#productitemview').find("tbody"),
$trLast = $tableBody.find("tr:last"),
$trNew = $trLast.clone();
$trNew.find('input').val('');
$trNew.find('select').val('');
$trLast.after($trNew);
			/* var clonedval = $('.clonedrow').html();
			
			 */
		
	}); 
	$(document).delegate('.removeitems', 'click', function(){ 
		$(this).parent().parent().remove();
		
			grandtotal();
	
	});
	$('.attachfile').on('change',function(){
       // output raw value of file input
      $('.showfilename').html(''); 

        // or, manipulate it further with regex etc.
        var filename = $(this).val().replace(/.*(\/|\\)/, '');
        // .. do your magic

       $('.showfilename').html(filename);
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\invoice\edit-gen.blade.php ENDPATH**/ ?>