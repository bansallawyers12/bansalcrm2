
<?php $__env->startSection('title', 'Add New Agent'); ?>

<?php $__env->startSection('content'); ?>

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<form action="<?php echo e(url('admin/agents/store')); ?>" method="POST" name="add-agents" autocomplete="off" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Add New Agent</h4>
								<div class="card-header-action">
									<a href="<?php echo e(route('admin.agents.active')); ?>" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-body">
								<div id="accordion">
									<div class="accordion">
										<div class="accordion-header" role="button" data-toggle="collapse" data-target="#agenttype" aria-expanded="true">
											<h4>Agent Type</h4>
										</div>
										<div class="accordion-body collapse show" id="agenttype" data-parent="#accordion">
											<div class="row">  
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group">
														<div class="form-check form-check-inline">
															<input class="form-check-input" type="checkbox" id="super_agent" value="Super Agent" name="agent_type[]">
															<label class="form-check-label" for="super_agent">Super Agent</label>
														</div>
														<div class="form-check form-check-inline">
															<input class="form-check-input" type="checkbox" id="sub_agent" value="Sub Agent" name="agent_type[]">
															<label class="form-check-label" for="sub_agent">Sub Agent</label>
														</div>
														<?php if($errors->has('agent_type')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('agent_type')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="accordion">
										<div class="accordion-header" role="button" data-toggle="collapse" data-target="#agentstructure" aria-expanded="true">
											<h4>Agent Structure</h4>
										</div>
										<div class="accordion-body collapse show" id="agentstructure" data-parent="#accordion">
											<div class="row">  
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group">
														<div class="form-check form-check-inline">
															<input class="form-check-input" type="radio" id="individual" value="Individual" name="struture" checked>
															<label class="form-check-label" for="individual">Individual</label>
														</div>
														<div class="form-check form-check-inline">
															<input class="form-check-input" type="radio" id="business" value="Business" name="struture">
															<label class="form-check-label" for="business">Business</label>
														</div>
														<?php if($errors->has('struture')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('struture')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="accordion">
										<div class="accordion-header" role="button" data-toggle="collapse" data-target="#personal_details" aria-expanded="true">
											<h4>Personal Details</h4>
										</div>
										<div class="accordion-body collapse show" id="personal_details" data-parent="#accordion">
											<div class="row"> 
												<div class="col-12 col-md-3 col-lg-3">
													<div class="form-group">
														<div class="profile_upload">
															<div class="upload_content">
															<img style="width:100px;height:100px;" id="output"/> 
																<i class="fa fa-camera if_image"></i>
																<span class="if_image">Upload Profile Image</span>
															</div>
															<input onchange="loadFile(event)" type="file" id="profile_img" name="profile_img" class="form-control" autocomplete="off" />
														</div>	
														<?php if($errors->has('profile_img')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('profile_img')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
												<div class="col-12 col-md-9 col-lg-9">
													<div class="row">
														<div class="col-12 col-md-6 col-lg-6 is_individual">
															<div class="form-group"> 
																<label for="full_name">Full Name <span class="span_req">*</span></label>
																<?php echo Form::text('full_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Full Name' )); ?>

																<?php if($errors->has('full_name')): ?>
																	<span class="custom-error" role="alert">
																		<strong><?php echo e(@$errors->first('full_name')); ?></strong>
																	</span> 
																<?php endif; ?>
															</div>
														</div>
														<div class="col-12 col-md-6 col-lg-6 is_business">
															<div class="form-group"> 
																<label for="business_name">Business Name <span class="span_req">*</span></label>
																<?php echo Form::text('business_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Business Name' )); ?>

																<?php if($errors->has('business_name')): ?>
																	<span class="custom-error" role="alert">
																		<strong><?php echo e(@$errors->first('business_name')); ?></strong>
																	</span> 
																<?php endif; ?>
															</div>
														</div>
														<div class="col-12 col-md-6 col-lg-6 is_business">
															<div class="form-group"> 
																<label for="c_name">Primary Contact Name <span class="span_req">*</span></label>
																<?php echo Form::text('c_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Primary Contact Name' )); ?>

																<?php if($errors->has('c_name')): ?>
																	<span class="custom-error" role="alert">
																		<strong><?php echo e(@$errors->first('c_name')); ?></strong>
																	</span> 
																<?php endif; ?>
															</div>
														</div>
														<div class="col-12 col-md-6 col-lg-6 is_business">
															<div class="form-group"> 
																<label for="tax_number">Tax Number</label>
																<?php echo Form::text('tax_number', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Tax Number' )); ?>

																<?php if($errors->has('tax_number')): ?>
																	<span class="custom-error" role="alert">
																		<strong><?php echo e(@$errors->first('tax_number')); ?></strong>
																	</span> 
																<?php endif; ?>
															</div>
														</div>
														<div class="col-12 col-md-6 col-lg-6 is_business">
															<div class="form-group"> 
																<label for="contract_expiry_date">Contract Expiry Date</label>
																<div class="input-group">
																	<div class="input-group-prepend">
																		<div class="input-group-text">
																			<i class="fas fa-calendar-alt"></i>
																		</div>
																	</div>
																	<?php echo Form::text('contract_expiry_date', '', array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )); ?>

																</div>
																<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
																<?php if($errors->has('contract_expiry_date')): ?>
																	<span class="custom-error" role="alert">
																		<strong><?php echo e(@$errors->first('contract_expiry_date')); ?></strong>
																	</span> 
																<?php endif; ?>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="accordion">
										<div class="accordion-header" role="button" data-toggle="collapse" data-target="#contact_details" aria-expanded="true">
											<h4>Contact Details</h4>
										</div>
										<div class="accordion-body collapse show" id="contact_details" data-parent="#accordion">
											<div class="row">
												<div class="col-12 col-md-6 col-lg-6">
													<div class="form-group"> 
														<label for="email">Email <span class="span_req">*</span></label>
														<?php echo Form::text('email', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Email' )); ?>

														<?php if($errors->has('email')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('email')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div> 
												<div class="col-12 col-md-6 col-lg-6">
													<div class="form-group"> 
														<label for="phone">Phone</label>
														<div class="cus_field_input">
															<div class="country_code"> 
																<input class="telephone" id="telephone" type="tel" name="country_code" readonly >
															</div>	
															<?php echo Form::text('phone', '', array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Phone' )); ?>

															<?php if($errors->has('phone')): ?>
																<span class="custom-error" role="alert">
																	<strong><?php echo e(@$errors->first('phone')); ?></strong>
																</span> 
															<?php endif; ?>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="accordion">
										<div class="accordion-header" role="button" data-toggle="collapse" data-target="#address" aria-expanded="true">
											<h4>Address</h4>
										</div>
										<div class="accordion-body collapse show" id="address" data-parent="#accordion">
											<div class="row">
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="address">Address</label>
														<?php echo Form::text('address', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Address' )); ?>

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
														<?php echo Form::text('city', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter City' )); ?>

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
														<?php echo Form::text('state', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter State' )); ?>

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
														<?php echo Form::text('zip', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Zip / Post Code' )); ?>

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
																<option <?php if($list->sortname == 'AU'): ?> selected <?php endif; ?> value="<?php echo e(@$list->sortname); ?>" ><?php echo e(@$list->name); ?></option>
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
										<div class="accordion-header" role="button" data-toggle="collapse" data-target="#office_income_share" aria-expanded="true">
											<h4>Office and Income Sharing Details</h4>
										</div>
										<div class="accordion-body collapse show" id="office_income_share" data-parent="#accordion">
											<div class="row">
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="related_office">Related Office <span class="span_req">*</span></label>
														<select class="form-control select2" name="related_office">
															<?php
													$branches = \App\Models\Branch::all();
													foreach($branches as $branch){
													?>
														<option  value="<?php echo e($branch->id); ?>"><?php echo e($branch->office_name); ?></option>
													<?php } ?>
														</select>
														<?php if($errors->has('related_office')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('related_office')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4 is_sub_agent">
													<div class="form-group"> 
														<label for="income_sharing">Income Sharing Percentage</label>
														<?php echo Form::number('income_sharing', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Income Sharing Percentage', 'step' => '0.01', 'min' => '0', 'max'=> '100' )); ?>

														<?php if($errors->has('income_sharing')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('income_sharing')); ?></strong>
															</span> 
														<?php endif; ?>
														<span class="span_note">This will be proportion of the income that is shared with your sub-agents when creating any invoice related to the referred application</span>
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4 is_super_agent">
													<div class="form-group"> 
														<label for="claim_revenue">Claim Revenue Percentage</label>
														<?php echo Form::number('claim_revenue', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Claim Revenue Percentage', 'step' => '0.01', 'min' => '0', 'max'=> '100' )); ?>

														<?php if($errors->has('claim_revenue')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('claim_revenue')); ?></strong>
															</span> 
														<?php endif; ?>
														<span class="span_note">This is the proportion of commission that you will be receiving from your super agent</span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								
								<div class="form-group float-right">
									<?php echo Form::submit('Save Agent', ['class'=>'btn btn-primary' ]); ?>

								</div>
							</div>
						</div>
					</div> 
				</div>
			</form>	
		</div>
	</section>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script>
  var loadFile = function(event) {
    var output = document.getElementById('output');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
      URL.revokeObjectURL(output.src) // free memory
	  $('.if_image').hide();
    }
  };
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\agents\create.blade.php ENDPATH**/ ?>