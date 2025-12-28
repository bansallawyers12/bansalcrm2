
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
									<a class="nav-link" id="payables-tab"  href="<?php echo e(URL::to('/admin/income-sharing/payables/unpaid')); ?>" >Payables</a>
								</li> 
								<li class="nav-item is_checked_clientn">
									<a class="nav-link active" id="receivables-tab"  href="<?php echo e(URL::to('/admin/income-sharing/receivables/unpaid')); ?>" >Receivables</a>
								</li> 	
							</ul> 
							<div class="tab-content" id="payableContent">
								<div class="tab-pane fade show active" id="payables" role="tabpanel" aria-labelledby="payables-tab">
									<ul class="nav nav-pills" id="paypaid_tabs" role="tablist">
										<li class="nav-item is_checked_clientn">
											<a class="nav-link" id="unpaid-tab"  href="<?php echo e(URL::to('/admin/income-sharing/receivables/unpaid')); ?>" >Unpaid</a>
										</li> 
										<li class="nav-item is_checked_clientn">
											<a class="nav-link active" id="paid-tab"  href="<?php echo e(URL::to('/admin/income-sharing/receivables/paid')); ?>" >Received</a>
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
															<th>Paid By</th>
															<th>Received On</th>
															<th>Action</th>
														</tr> 
													</thead> 
													
													<tbody class="tdata">	
														
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

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>

<script>
jQuery(document).ready(function($){ 
	
});	
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\account\receivablepaid.blade.php ENDPATH**/ ?>