
<?php $__env->startSection('title', 'Edit Branch'); ?>

<?php $__env->startSection('content'); ?>
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<?php echo Form::open(array('url' => 'admin/branch/edit', 'name'=>"edit-branch", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")); ?>

			<?php echo Form::hidden('id', @$fetchedData->id); ?>

				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Edit Branch</h4>
								<div class="card-header-action">
									<a href="<?php echo e(route('admin.branch.index')); ?>" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-body">
								<div id="accordion"> 
									<div class="accordion">
										<div class="accordion-header" role="button" data-toggle="collapse" data-target="#primary_info" aria-expanded="true">
											<h4>Primary Information</h4>
										</div>
										<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
											<div class="row"> 						
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="office_name">Office Name <span class="span_req">*</span></label>
														<?php echo Form::text('office_name', @$fetchedData->office_name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Office Name' )); ?>

														<?php if($errors->has('office_name')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('office_name')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="accordion">
										<div class="accordion-header" role="button" data-toggle="collapse" data-target="#address">
											<h4>Address</h4>
										</div>
										<div class="accordion-body collapse" id="address" data-parent="#accordion">
											<div class="row">
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="address">Address</label>
														<?php echo Form::text('address', @$fetchedData->address, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Address' )); ?>

														<?php if($errors->has('address')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('address')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="city">City</label>
														<?php echo Form::text('city', @$fetchedData->city, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter City' )); ?>

														<?php if($errors->has('city')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('city')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="state">State</label>
														<?php echo Form::text('state', @$fetchedData->state, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter State' )); ?>

														<?php if($errors->has('state')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('state')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="zip">Zip / Post Code</label>
														<?php echo Form::text('zip', @$fetchedData->zip, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Zip / Post Code' )); ?>

														<?php if($errors->has('zip')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('zip')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="country">Country</label>
														<select class="form-control  select2" name="country" >
														<?php
															foreach(\App\Models\Country::all() as $list){
																?>
																<option value="<?php echo e(@$list->sortname); ?>" <?php if($fetchedData->country == @$list->sortname){ echo 'selected'; } ?>><?php echo e(@$list->name); ?></option>
																<?php
															}
															?>
														</select>
														<?php if($errors->has('country')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('country')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
											</div>  
										</div>
									</div>
									<div class="accordion">
										<div class="accordion-header" role="button" data-toggle="collapse" data-target="#contact_details">
											<h4>Contact Details</h4>
										</div>
										<div class="accordion-body collapse" id="contact_details" data-parent="#accordion">
											<div class="row">
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="email">Email <span class="span_req">*</span></label>
														<?php echo Form::text('email', @$fetchedData->email, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Email' )); ?>

														<?php if($errors->has('email')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('email')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="phone">Phone Number</label>	
														<?php echo Form::text('phone', @$fetchedData->phone, array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Phone' )); ?>

														<?php if($errors->has('phone')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('phone')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="mobile">Mobile</label>
														<?php echo Form::text('mobile', @$fetchedData->mobile, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Mobile' )); ?>

														<?php if($errors->has('mobile')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('mobile')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="contact_person">Contact Person</label>
														<?php echo Form::text('contact_person', @$fetchedData->contact_person, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Contact Person' )); ?>

														<?php if($errors->has('contact_person')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('contact_person')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>	
											</div>
										</div>
									</div>
									<div class="accordion">
										<div class="accordion-header" role="button" data-toggle="collapse" data-target="#other_info">
											<h4>Other Information</h4>
										</div>
										<div class="accordion-body collapse" id="other_info" data-parent="#accordion">
											<div class="row">
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="choose_admin">Choose Admin</label>
														<select class="form-control select2" name="choose_admin">
															<option>-- Choose Admin --</option>
														</select>
														<?php if($errors->has('choose_admin')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('choose_admin')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group float-right">
									<?php echo Form::button('Update Branch', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("edit-branch")']); ?> 
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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\branch\edit.blade.php ENDPATH**/ ?>