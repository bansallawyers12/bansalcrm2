
<?php $__env->startSection('title', 'User Type'); ?>

<?php $__env->startSection('content'); ?>

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body"> 
			<?php echo Form::open(array('url' => 'admin/usertype/edit', 'name'=>"edit-usertype", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")); ?>

					  <?php echo Form::hidden('id', @$fetchedData->id); ?>

				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Edit User Type</h4>
								<div class="card-header-action">
									<a href="<?php echo e(route('admin.usertype.index')); ?>" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>	
					<div class="col-12 col-md-6 col-lg-6">
						<div class="card">
							<div class="card-body">
								<div class="form-group"> 
									<label for="name">User Type Name</label>
									<?php echo Form::text('name', @$fetchedData->name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter User Type' )); ?>

									<?php if($errors->has('name')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('name')); ?></strong>
										</span> 
									<?php endif; ?>
								</div>	
								<div class="form-group float-right">
									<?php echo Form::submit('Update', ['class'=>'btn btn-primary' ]); ?>

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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\usertype\edit.blade.php ENDPATH**/ ?>