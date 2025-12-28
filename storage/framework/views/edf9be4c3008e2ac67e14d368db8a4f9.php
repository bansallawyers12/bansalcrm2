
<?php $__env->startSection('title', 'Income Sharing'); ?>

<?php $__env->startSection('content'); ?>
<style>
.dropdown a.dropdown-toggle:after{display:none;}
.dropdown a.dropdown-toggle{color:#000;}	
</style>
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				<?php echo $__env->make('../Elements/flash-message', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
									<a class="nav-link active" id="payables-tab"  href="<?php echo e(URL::to('/admin/income-sharing/payables/unpaid')); ?>" >Payables</a>
								</li> 
								<li class="nav-item is_checked_clientn">
									<a class="nav-link" id="receivables-tab"  href="<?php echo e(URL::to('/admin/income-sharing/receivables/unpaid')); ?>" >Receivables</a>
								</li> 	
							</ul> 
							<div class="tab-content" id="payableContent">
								<div class="tab-pane fade show active" id="payables" role="tabpanel" aria-labelledby="payables-tab">
									<ul class="nav nav-pills" id="paypaid_tabs" role="tablist">
										<li class="nav-item is_checked_clientn">
											<a class="nav-link active" id="unpaid-tab"  href="<?php echo e(URL::to('/admin/income-sharing/payables/unpaid')); ?>" >Unpaid</a>
										</li> 
										<li class="nav-item is_checked_clientn">
											<a class="nav-link" id="paid-tab"  href="<?php echo e(URL::to('/admin/income-sharing/payables/paid')); ?>" >Paid</a>
										</li> 	
									</ul>
									<div class="tab-content" id="payableContent">
										<div class="tab-pane fade show active" id="unpaid" role="tabpanel" aria-labelledby="unpaid-tab">
											<div class="table-responsive common_table"> 
												<table class="table text_wrap">
													<thead>
														<tr> 
															<th>Invoice No</th>
															<th>Office Name</th>
															<th>Sub Agent</th>
															<th>Client Name</th>
															<th>DOB</th>
															<th>Partner Name</th>
															<th>Product Name</th>
															<th>Amount</th> 
															<th>Tax Amount</th>
															<th>Status</th>	
															<th>Action</th>
														</tr> 
													</thead> 
													<?php //dd($lists); ?>
													<?php if(count($lists) >0): ?>
													<tbody class="tdata">	
														<?php $__currentLoopData = $lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<?php
														    if( isset($list->invoice) && $list->invoice != ""){
																$applicationdata = \App\Models\Application::where('id', $list->invoice->application_id)->first();//dd($applicationdata);
																$partnerdata = \App\Models\Partner::where('id', @$applicationdata->partner_id)->first();
																$productdata = \App\Models\Product::where('id', @$applicationdata->product_id)->first();
																
																$customer_id = $list->invoice->customer->id;
														    }
															?>
																<tr>
																	<td style="white-space: initial;"><a href="<?php echo e(URL::to('/admin/invoice/view/')); ?>/<?php echo e($list->invoice_id); ?>"><?php echo e(@$list->invoice_id); ?></a></td>
																	<td style="white-space: initial;"><a href="#"><?php echo e(@$list->branch->office_name); ?></a></td>
																	<td>-</td>
																	<td style="white-space: initial;"><a href="<?php echo e(URL::to('/admin/clients/detail/')); ?>/<?php echo e(base64_encode(convert_uuencode($list->invoice->customer->id ?? 'N/A'))); ?>"><?php echo e(@$list->invoice->customer->first_name ?? 'N/A'); ?> <?php echo e(@$list->invoice->customer->last_name ?? 'N/A'); ?></a></td>
																	<td style="white-space: initial;"><?php echo e(date('d/m/Y', strtotime(@$list->invoice->customer->dob))); ?></td>
																	<td style="white-space: initial;"><?php echo e(@$partnerdata->partner_name ?? 'N/A'); ?></td>
																	<td style="white-space: initial;"><?php echo e(@$productdata->name ?? 'N/A'); ?></td>
																	<td style="white-space: initial;">$ <?php echo e($list->amount); ?></td>
																	<td>$ 0.00</td>
																	
																	<td>
																	    <?php
																	    if(isset($list->invoice) && $list->invoice->status == 1){
																	        echo "Active";
																	    } else {
																	        echo "Inactive";
																	    }?>
																	    
																	   </td>
																	<td style="text-align:right;">
																		<div class="dropdown d-inline">
																			<a class="dropdown-toggle" href="javascript:;" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
																			<div class="dropdown-menu">
																			<?php
																			if(isset($list->invoice) && $list->invoice->status == 1){
																			?>
																				<a  data-netamount="<?php echo e($list->amount); ?>"  data-invoiceid="<?php echo e($list->id); ?>" class="dropdown-item openpaymentform" href="javascript:;">Make Payment</a>
																				<?php } ?>
																				<a class="dropdown-item" href="javascript:;" onClick="deleteAction(<?php echo e(@$list->id); ?>, ' income_sharings')">Delete</a>
																			</div>
																		</div>	
																	</td> 
																</tr>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													</tbody>	
													<?php else: ?>
													<tbody>
														<tr>
															<td style="text-align:center;" colspan="12">
																No Record found
															</td>
														</tr>
													</tbody>
													<?php endif; ?>										
												</table>
												
												<div class="card-footer">
							<?php echo $lists->appends(\Request::except('page'))->render(); ?>

						</div>
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
<div id="addpaymentmodal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
	<?php echo Form::open(array('url' => 'admin/income-payment-store', 'name'=>"incomepaymentform", 'autocomplete'=>'off', "enctype"=>"multipart/form-data", "id"=>"incomepaymentform")); ?>

	<input type="hidden" value="" name="invoice_id" id="invoice_id">
	<input type="hidden" value="false" name="is_ajax" id="">
	<input data-valid="required" type="hidden" name="payment_amount" placeholder="" class="paymentAmount" />
		<div class="modal-content ">
			<div class="modal-header">
				<h4 class="modal-title">Payment Details</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
			
				<div class="payment_field">
					<div class="payment_field_row">
						<div class="payment_field_col payment_first_step">
							
							<div class="field_col">
								<select data-valid="required" name="payment_mode" class="form-control">
									<option value="Cheque">Cheque</option>
									<option value="Cash">Cash</option>
									<option value="Credit Card">Credit Card</option>
									<option value="Bank Transfers">Bank Transfers</option>
								</select>
							</div>
							<div class="field_col">
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-clock"></i>
										</div>
									</div>	
									<input data-valid="required"  type="text" name="payment_date" placeholder="" class="datepicker form-control" />
								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
							</div>
							
						</div>
					</div>
					
					<div class="clearfix"></div>
					<div class="invoiceamount">
						<table class="table">
							<tr>
								<td><b>Amount:</b></td>
								<td class="invoicenetamount"></td>
								<td><b>Tax Amount:</b></td>
								<td class="totldueamount" data-totaldue=""></td>
							</tr>
						
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" onclick="customValidate('incomepaymentform')" class="btn btn-primary" >Save & Close</button>
				
			  </div>
		</div>
		</form>
	</div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>

<script>
jQuery(document).ready(function($){ 
	$(document).delegate('.openpaymentform','click', function(){
		var v = $(this).attr('data-invoiceid');
		var netamount = $(this).attr('data-netamount');
		$('#invoice_id').val(v);
		$('.invoicenetamount').html(netamount+' AUD');
		$('.totldueamount').html('0 AUD');
		$('#addpaymentmodal').modal('show');
		$('.paymentAmount').val(netamount);
	});	
});	
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\account\payableunpaid.blade.php ENDPATH**/ ?>