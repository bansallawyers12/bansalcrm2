
<?php $__env->startSection('title', 'User'); ?>

<?php $__env->startSection('content'); ?>

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<?php echo Form::open(array('url' => 'admin/services/store', 'name'=>"add-service", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")); ?>

				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Add Services</h4>
								<div class="card-header-action">
									<a href="<?php echo e(route('admin.services.index')); ?>" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-6 col-lg-6">
						<div class="card">
							<div class="card-body">
								<div class="form-group"> 
									<label for="title">Title</label>
									<?php echo Form::text('title', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Service Name' )); ?>

									<?php if($errors->has('title')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('title')); ?></strong>
										</span> 
									<?php endif; ?>
								</div>
								<div class="form-group">
									<label for="description">Description</label>
									<textarea name="description" class="form-control" placeholder="Enter Description"></textarea>
									<?php if($errors->has('description')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('description')); ?></strong>
										</span> 
									<?php endif; ?>
								</div>
								<div class="form-group">
									<label for="parent">Parent</label>
									<select class="form-control" name="parent">
										<option value="0">None</option>
											<?php
												echo \App\Models\Service::printTree($tree);
											?>
									</select>
									<?php if($errors->has('parent')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('parent')); ?></strong>
										</span> 
									<?php endif; ?>
								</div>
								<div class="form-group"> 
									<label for="services_icon">Service Icon</label>
									<?php echo Form::text('services_icon', '', array('class' => 'form-control', 'autocomplete'=>'off','placeholder'=>'Enter Service Icon' )); ?>

									<?php if($errors->has('services_icon')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('services_icon')); ?></strong>
										</span> 
									<?php endif; ?>
								</div>
								<div class="form-group">
									<label>Service Image</label>
									<div class="custom-file">
										<input type="file" id="services_image" name="services_image" class="form-control" autocomplete="off" />
										<label class="custom-file-label" for="services_image">Choose file</label>
									</div>	
									<?php if($errors->has('services_image')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('services_image')); ?></strong>
										</span> 
									<?php endif; ?>
								</div>
								<div class="form-group float-right">
									<?php echo Form::submit('Save Service', ['class'=>'btn btn-primary' ]); ?>

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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\services\create.blade.php ENDPATH**/ ?>