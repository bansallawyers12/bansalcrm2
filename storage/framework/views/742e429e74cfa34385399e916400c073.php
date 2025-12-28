
<?php $__env->startSection('title', 'Add Crm Email Template'); ?>

<?php $__env->startSection('content'); ?>

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<?php echo Form::open(array('url' => 'admin/crm_email_template/store', 'name'=>"add-crmemailtemplate", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")); ?> 
				<div class="row">   
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Add Crm Email Template</h4>
								<div class="card-header-action">
									<a href="<?php echo e(route('admin.crmemailtemplate.index')); ?>" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					 <div class="col-3 col-md-3 col-lg-3">
			        	<?php echo $__env->make('../Elements/Admin/setting', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    		        </div>       
    				<div class="col-9 col-md-9 col-lg-9">
						<div class="card">
							<div class="card-body">
								<div id="accordion"> 
									<div class="accordion">
										<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
											<div class="row"> 						
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group"> 
														<label for="name">Name <span class="span_req">*</span></label>
														<?php echo Form::text('name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )); ?>

														<?php if($errors->has('name')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('name')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group"> 
														<label for="subject">Subject</label>
														<?php echo Form::text('subject', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?>

														<?php if($errors->has('subject')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('subject')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group"> 
														<label for="description">Description</label>
														<textarea class="form-control summernote-simple" name="description"></textarea>
													</div>
												</div> 
											</div>
										</div>
									</div>
								</div>
								<div class="form-group float-right">
									<?php echo Form::button('Save', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-crmemailtemplate")' ]); ?>

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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\feature\crmemailtemplate\create.blade.php ENDPATH**/ ?>