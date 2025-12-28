
<?php $__env->startSection('title', 'Add Email'); ?>

<?php $__env->startSection('content'); ?>

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<?php echo Form::open(array('url' => 'admin/emails/store', 'name'=>"add-emails", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")); ?> 
				<div class="row">   
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Add Email</h4>
								<div class="card-header-action">
									<a href="<?php echo e(route('admin.emails.index')); ?>" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
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
										<div class="accordion-header" role="button" data-toggle="collapse" data-target="#primary_info" aria-expanded="true">
											<h4>Primary Information</h4>
										</div>
										<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
											<div class="row"> 						
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group"> 
														<label for="email">Email Id <span class="span_req">*</span></label>
														<?php echo Form::text('email', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )); ?>

														<?php if($errors->has('email')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('email')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group"> 
														<label for="status">Status</label><br>
														<label ><input type="checkbox" name="status" value="1"> Enable This Feature</label>
													</div>
												</div>
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group"> 
														<label for="display_name">Display Name</label>
														<?php echo Form::text('display_name', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?>

														<?php if($errors->has('display_name')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('display_name')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
                                              
                                                <div class="col-12 col-md-12 col-lg-12">
													<div class="form-group">
														<label for="password">Password</label>
														<?php echo Form::text('password', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?>

														<?php if($errors->has('password')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('password')); ?></strong>
															</span>
														<?php endif; ?>
													</div>
												</div>
                                              
												<div class="col-12 col-md-12 col-lg-12">
													<h4>User Sharing</h4>
													<div class="form-group"> 
														<label for="display_name">Select Users</label>
														<select data-valid="required" multiple class="form-control select2" name="users[]">
															<option value="">Select User</option>
															<?php
																$users = \App\Models\Admin::Where('role', '!=', '7')->Where('status', '=', 1)->get();
																foreach($users as $user){
																	?>
																	<option value="<?php echo e($user->id); ?>"><?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?></option>
																	<?php
																}
															?>
														</select>
														
													</div>
												</div>
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group"> 
														<label for="status">Company Email Signature</label><br>
														<textarea class="form-control summernote-simple" name="email_signature"></textarea>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group float-right">
									<?php echo Form::button('Save', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-emails")' ]); ?>

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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\feature\emails\create.blade.php ENDPATH**/ ?>