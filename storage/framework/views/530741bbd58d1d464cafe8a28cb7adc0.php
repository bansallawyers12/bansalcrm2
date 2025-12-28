
<?php $__env->startSection('title', 'Paid Invoices'); ?>

<?php $__env->startSection('content'); ?>
<style>
.ag-space-between {
    justify-content: space-between;
}
.ag-align-center {
    align-items: center;
}
.ag-flex {
    display: flex;
}
.ag-align-start {
    align-items: flex-start;
}
.ag-flex-column {
    flex-direction: column;
}
.col-hr-1 {
    margin-right: 5px!important;
}
.text-semi-bold {
    font-weight: 600!important;
}
.small, small {
    font-size: 85%;
}
.ag-align-end {
    align-items: flex-end;
}

.ui.yellow.label, .ui.yellow.labels .label {
    background-color: #fbbd08!important;
    border-color: #fbbd08!important;
    color: #fff!important;
}
.ui.label:last-child {
    margin-right: 0;
}
.ui.label:first-child {
    margin-left: 0;
}
.field .ui.label {
    padding-left: 0.78571429em;
    padding-right: 0.78571429em;
}

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
							<h4>Paid Invoices</h4>
							<div class="card-header-action">
								
								
							</div>
						</div>
						<div class="card-body">
							
							<ul class="nav nav-pills" id="client_tabs" role="tablist">
								
								<li class="nav-item is_checked_clientn">
									<a class="nav-link" id="prospects-tab"  href="<?php echo e(URL::to('/admin/invoice/unpaid')); ?>" >Unpaid Invoices</a>
								</li>
								<li class="nav-item is_checked_clientn">
									<a class="nav-link active" id="clients-tab"  href="<?php echo e(URL::to('/admin/invoice/paid')); ?>" >Paid Invoices</a>
								</li>
							
							</ul> 
							<div class="tab-content" id="clientContent">								
								<div class="tab-pane fade show active" id="clients" role="tabpanel" aria-labelledby="clients-tab">
									<div class="table-responsive common_table"> 
										<table class="table text_wrap">
											<thead>
												<tr> 
													
													<th>No</th>
													<th>Issue Date</th>
													<th>Client Name</th>
													<th>Created By</th>
													<th>Partner Name</th>
													<th>Workflow</th>
													<th>Product</th>
													<th>Amount</th>			
													
													<th>Action</th>
													
												</tr> 
											</thead>
											<?php if(count($lists) >0): ?>
											<tbody class="tdata">	
												<?php
												foreach($lists as $invoicelist){
																				$clientdata = \App\Models\Admin::where('id', $invoicelist->client_id)->first();
																				$admindata = \App\Models\Admin::where('id', $invoicelist->user_id)->first();
																											if($invoicelist->type == 3){
																		$workflowdaa = \App\Models\Workflow::where('id', $invoicelist->application_id)->first();
																	}else{
																		$applicationdata = \App\Models\Application::where('id', @$invoicelist->application_id)->first();
																		$workflowdaa = \App\Models\Workflow::where('id', @$invoicelist->application_id)->first();
																		$partnerdata = \App\Models\Partner::where('id', @$applicationdata->partner_id)->first();
																	}
																	$invoiceitemdetails = \App\Models\InvoiceDetail::where('invoice_id', $invoicelist->id)->orderby('id','ASC')->get();
																	$netamount = 0;
																	$coom_amt = 0;
																	$total_fee = 0;
																	foreach($invoiceitemdetails as $invoiceitemdetail){
																		$netamount += $invoiceitemdetail->netamount;
																		$coom_amt += $invoiceitemdetail->comm_amt;
																		$total_fee += $invoiceitemdetail->total_fee;
																	}
																	
																	$paymentdetails = \App\Models\InvoicePayment::where('invoice_id', $invoicelist->id)->orderby('created_at', 'DESC')->get();
																	$amount_rec = 0;
																	foreach($paymentdetails as $paymentdetail){
																		$amount_rec += $paymentdetail->amount_rec;
																	} 
																	if($invoicelist->type == 1){
																		$totaldue = $total_fee - $coom_amt;
																	} if($invoicelist->type == 2){
																		$totaldue = $netamount - $amount_rec;
																	}else{
																		$totaldue = $netamount - $amount_rec;
																	}
																	?>
												<tr id="id_<?php echo $invoicelist->id; ?>">
													<td><?php echo $invoicelist->id; ?></td>
													<td style="white-space: initial;"><a href="<?php echo e(URL::to('admin/invoice/view/')); ?>/<?php echo $invoicelist->id; ?>"><?php echo e(date('d/m/Y', strtotime($invoicelist->invoice_date))); ?> <?php //echo $invoicelist->invoice_date; ?></a></td>
													<td style="white-space: initial;"><a href="<?php echo e(URL::to('admin/clients/detail/')); ?>/<?php echo e(base64_encode(convert_uuencode(@$clientdata->id))); ?>"><?php echo e($clientdata->first_name); ?> <?php echo e($clientdata->last_name); ?></a></td>
													<td style="white-space: initial;"><a href=""><?php echo e($admindata->first_name); ?></a></td>
													<td style="white-space: initial;"><?php echo e(@$partnerdata->partner_name); ?></td>
													<td style="white-space: initial;"><?php echo e(@$workflowdaa->name); ?></td>
													<td></td>
													<td>AUD <?php echo $invoicelist->net_fee_rec; ?></td>
													
													<td>
													<a href="<?php echo e(URL::to('admin/invoice/view/')); ?>/<?php echo $invoicelist->id; ?>"><i class="fa fa-eye"></i></a>
												<!--	<a href=""><i class="fa fa-envelope"></i></a>-->
													
													<!--<a href=""><i class="fa fa-dollor"></i></a>-->
													<!--<a href=""><i class="fa fa-trash"></i></a>-->
													</td>
												</tr>
												<?php
												}
												?>
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
									</div>
								</div>
								
							</div> 
						</div>
						<div class="card-footer">
							<?php echo $lists->appends(\Request::except('page'))->render(); ?>

						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<div id="emailmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">Compose Email</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" name="sendmail" action="<?php echo e(URL::to('/admin/sendmail')); ?>" autocomplete="off" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">From <span class="span_req">*</span></label>
								<?php echo Form::text('email_from', 'support@digitrex.live', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter From' )); ?>

								<?php if($errors->has('email_from')): ?>
									<span class="custom-error" role="alert">
										<strong><?php echo e(@$errors->first('email_from')); ?></strong>
									</span> 
								<?php endif; ?>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_to">To <span class="span_req">*</span></label>
								<select data-valid="required" class="js-data-example-ajax" name="email_to[]"></select>
								
								<?php if($errors->has('email_to')): ?>
									<span class="custom-error" role="alert">
										<strong><?php echo e(@$errors->first('email_to')); ?></strong>
									</span> 
								<?php endif; ?>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_cc">CC </label>
								<select data-valid="" class="js-data-example-ajaxcc" name="email_cc[]"></select>
								
								<?php if($errors->has('email_cc')): ?>
									<span class="custom-error" role="alert">
										<strong><?php echo e(@$errors->first('email_cc')); ?></strong>
									</span> 
								<?php endif; ?>
							</div>
						</div>
						
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="template">Templates </label>
								<select data-valid="" class="form-control select2 selecttemplate" name="template">
									<option value="">Select</option>
									<?php $__currentLoopData = \App\Models\CrmEmailTemplate::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($list->id); ?>"><?php echo e($list->name); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
								
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="subject">Subject <span class="span_req">*</span></label>
								<?php echo Form::text('subject', '', array('class' => 'form-control selectedsubject', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Subject' )); ?>

								<?php if($errors->has('subject')): ?>
									<span class="custom-error" role="alert">
										<strong><?php echo e(@$errors->first('subject')); ?></strong>
									</span> 
								<?php endif; ?>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Message <span class="span_req">*</span></label>
								<textarea class="summernote-simple selectedmessage" name="message"></textarea>
								<?php if($errors->has('message')): ?>
									<span class="custom-error" role="alert">
										<strong><?php echo e(@$errors->first('message')); ?></strong>
									</span>  
								<?php endif; ?>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('sendmail')" type="button" class="btn btn-primary">Send</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\invoice\paid.blade.php ENDPATH**/ ?>