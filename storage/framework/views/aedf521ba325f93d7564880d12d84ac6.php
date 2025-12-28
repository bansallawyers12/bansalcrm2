
<?php $__env->startSection('title', 'New Manage Contacts'); ?>

<?php $__env->startSection('content'); ?>

<!-- Main Content --> 
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<?php echo Form::open(array('url' => 'admin/managecontact/store', 'name'=>"add-contacts", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")); ?>

				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Add New Contacts</h4>
								<div class="card-header-action">
									<a href="<?php echo e(route('admin.managecontact.index')); ?>" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>	
					<div class="col-12 col-md-6 col-lg-6">
						<div class="card">
							<div class="card-body">
								<div class="form-group"> 
									<label for="srname" class="col-form-label">Primary Name <span style="color:#ff0000;">*</span></label>
									<div class="row">		
										<div class="col-sm-2">
											<select style="padding: 0px 5px;" name="srname" id="srname" class="form-control" autocomplete="new-password">
												<option value="Mr">Mr</option>
												<option value="Mrs">Mrs</option>
												<option value="Ms">Ms</option>
												<option value="Miss">Miss</option>
												<option value="Dr">Dr</option>
											</select>
										</div>
										<div class="col-sm-4">
										<?php echo Form::text('first_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'First Name *' )); ?>

										<?php if($errors->has('first_name')): ?>
											<span class="custom-error" role="alert">
												<strong><?php echo e(@$errors->first('first_name')); ?></strong>
											</span> 
										<?php endif; ?>
										</div>									
										<div class="col-sm-3">
										<?php echo Form::text('middle_name', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Middle Name' )); ?>

										<?php if($errors->has('middle_name')): ?>
											<span class="custom-error" role="alert">
												<strong><?php echo e(@$errors->first('middle_name')); ?></strong>
											</span> 
										<?php endif; ?>
										</div>
										<div class="col-sm-3">
										<?php echo Form::text('last_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Last Name *' )); ?>

										<?php if($errors->has('last_name')): ?>
											<span class="custom-error" role="alert">
												<strong><?php echo e(@$errors->first('last_name')); ?></strong>
											</span> 
										<?php endif; ?>
										</div>
									</div>
								</div>	
								<div class="form-group float-right">
									<?php echo Form::submit('Save', ['class'=>'btn btn-primary' ]); ?>

								</div>
							</div>
						</div>
					</div>
				</div>
			<?php echo Form::close(); ?>

		</div>
	</section>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\managecontact\create.blade.php ENDPATH**/ ?>