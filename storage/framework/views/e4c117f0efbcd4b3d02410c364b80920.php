
<?php $__env->startSection('title', 'Create Group Invoice'); ?>

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
			<?php echo Form::open(array('url' => 'admin/invoice/general-store', 'name'=>"invoiceform", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")); ?>

			<input type="hidden" name="type" value="">
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
								<h4>Partner Detail</h4>
							</div>
							<div class="card-body">
								<div class="invoice_info">
									<p><b>BUPA</b><br/>228 Park Road, Hurstville, NSW Australia 61134135</p>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-4 col-lg-4 offset-4">
						<div class="card">	
							<div class="card-header">
								<h4>Payment Detail</h4>
							</div>
							<div class="card-body">
								<div class="form-group"> 
									<label for="payment_option">Select Payment Option</label>
									<select name="payment_option" class="form-control payment_option">
										<option value="">Select Payment Detail</option>
										<option value="Income">Income</option>
										<option value="Payables">Payables</option>
									</select>
									<?php if($errors->has('payment_option')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('payment_option')); ?></strong>
										</span> 
									<?php endif; ?>
								</div> 
							</div>
						</div> 
					</div>	
					<div class="col-12 col-md-4 col-lg-4">
						<div class="card">							
							<div class="card-body">
								<div class="form-group">  
									<label for="invoice_due_date">Due Date:</label>
									<?php echo Form::text('invoice_due_date', '', array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )); ?>

									<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
									<?php if($errors->has('invoice_due_date')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('invoice_due_date')); ?></strong>
										</span> 
									<?php endif; ?>
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
	
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\invoice\creategroupinvoice.blade.php ENDPATH**/ ?>