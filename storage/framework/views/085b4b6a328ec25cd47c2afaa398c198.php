
<?php $__env->startSection('title', 'Create Client'); ?>

<?php $__env->startSection('content'); ?>

<!-- Main Content -->
<div class="main-content">
	<section class="section">
	     <div class="server-error">
				<?php echo $__env->make('../Elements/flash-message', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
			</div>
		<div class="section-body">
			<?php echo Form::open(array('url' => 'admin/clients/store', 'name'=>"add-clients", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")); ?> 
			<input type="hidden" name="type" value="client">
				<div class="row">   
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Create Client</h4>
								<div class="card-header-action">
									<a href="<?php echo e(route('admin.clients.index')); ?>" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-body">
								<div class="row">
									<div class="col-3 col-md-3 col-lg-3">
								    	<div class="form-group profile_img_field">
											<div class="profile_upload">
												<div class="upload_content">
													<img id="output"/> 
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
									<div class="col-9 col-md-9 col-lg-9">
										<div class="row">
											<div class="col-4 col-md-4 col-lg-4">
												<div class="form-group"> 
													<label for="first_name">First Name <span class="span_req">*</span></label>
													<?php echo Form::text('first_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )); ?>

													<?php if($errors->has('first_name')): ?>
														<span class="custom-error" role="alert">
															<strong><?php echo e(@$errors->first('first_name')); ?></strong>
														</span> 
													<?php endif; ?>
												</div>
											</div>
											<div class="col-4 col-md-4 col-lg-4">
												<div class="form-group"> 
													<label for="last_name">Last Name <span class="span_req">*</span></label>
													<?php echo Form::text('last_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )); ?>

													<?php if($errors->has('last_name')): ?>
														<span class="custom-error" role="alert">
															<strong><?php echo e(@$errors->first('last_name')); ?></strong>
														</span> 
													<?php endif; ?>
												</div>
											</div>
											<div class="col-4 col-md-4 col-lg-4">
												<?php
													$oldgender = old('gender');
												?>
												<div class="form-group"> 
													<label style="display:block;" for="gender">Gender <span class="span_req">*</span></label>
													<div class="form-check form-check-inline">
														<input <?php if($oldgender == 'Male'){ echo 'checked'; } ?> class="form-check-input" type="radio" id="male" value="Male" name="gender" checked>
														<label class="form-check-label" for="male">Male</label>
													</div>
													<div class="form-check form-check-inline">
														<input class="form-check-input" <?php if($oldgender == 'Female'){ echo 'checked'; } ?> type="radio" id="female" value="Female" name="gender">
														<label class="form-check-label" for="female">Female</label>
													</div>
													<div class="form-check form-check-inline">
														<input class="form-check-input" <?php if($oldgender == 'Other'){ echo 'checked'; } ?> type="radio" id="other" value="Other" name="gender">
														<label class="form-check-label" for="other">Other</label>
													</div>
													<?php if($errors->has('gender')): ?>
														<span class="custom-error" role="alert">
															<strong><?php echo e(@$errors->first('gender')); ?></strong>
														</span> 
													<?php endif; ?>
												</div>
											</div>
											<div class="col-4 col-md-4 col-lg-4">
												<div class="form-group">
													<label for="dob">
													Date of Birth</label>
													<div class="input-group">
														<div class="input-group-prepend">
															<div class="input-group-text">
																<i class="fas fa-calendar-alt"></i>
															</div>
														</div>
														<?php echo Form::text('dob', '', array('class' => 'form-control dobdatepickers', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?> 
														<?php if($errors->has('dob')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('dob')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
											</div>
											<div class="col-4 col-md-4 col-lg-4">
												<div class="form-group"> 
													<label for="age">Age</label>
													<div class="input-group">
														<div class="input-group-prepend">
															<div class="input-group-text">
																<i class="fas fa-calendar-alt"></i>
															</div>
														</div>
														<?php echo Form::text('age', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?>

														<?php if($errors->has('age')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('age')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
											</div>
											<div class="col-4 col-md-4 col-lg-4">
															<div class="form-group"> 
																<label for="client_id">Client ID</label>
																<?php echo Form::text('client_id', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?>

																<?php if($errors->has('client_id')): ?>
																	<span class="custom-error" role="alert">
																		<strong><?php echo e(@$errors->first('client_id')); ?></strong>
																	</span> 
																<?php endif; ?>
															</div>
														</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="martial_status">
													Marital Status</label>
											<select style="padding: 0px 5px;" name="martial_status" id="martial_status" class="form-control">
														<option value="">Select Marital Status</option>
														<option value="Married" <?php if(old('martial_status') == 'Married'){ echo 'selected'; } ?>>Married</option>
														<option <?php if(old('martial_status') == 'Never Married'){ echo 'selected'; } ?> value="Never Married">Never Married</option>
														<option <?php if(old('martial_status') == 'Engaged'){ echo 'selected'; } ?> value="Engaged">Engaged</option>
														<option <?php if(old('martial_status') == 'Divorced'){ echo 'selected'; } ?> value="Divorced">Divorced</option>
														<option <?php if(old('martial_status') == 'Separated'){ echo 'selected'; } ?> value="Separated">Separated</option>
														<option <?php if(old('martial_status') == 'De facto'){ echo 'selected'; } ?> value="De facto">De facto</option>
														<option <?php if(old('martial_status') == 'Widowed'){ echo 'selected'; } ?> value="Widowed">Widowed</option>
														<option <?php if(old('martial_status') == 'Others'){ echo 'selected'; } ?> value="Others">Others</option>
													</select>
													<?php if($errors->has('martial_status')): ?>
														<span class="custom-error" role="alert">
															<strong><?php echo e(@$errors->first('martial_status')); ?></strong>
														</span> 
													<?php endif; ?>
												</div>
											</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="contact_type">
											Contact Type <span style="color:#ff0000;">*</span></label>
											<select style="padding: 0px 5px;" name="contact_type" id="contact_type" class="form-control" data-valid="required">
												<option value="Personal" <?php if(old('contact_type') == 'Personal'){ echo 'selected'; } ?>> Personal</option>
												<option <?php if(old('contact_type') == 'Office'){ echo 'selected'; } ?> value="Office">Office</option>
											</select>
											<?php if($errors->has('contact_type')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('contact_type')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="phone">Contact No.<span style="color:#ff0000;">*</span></label>
											<div class="cus_field_input">
												<div class="country_code"> 
													<input class="telephone" id="telephone" type="tel" name="country_code" readonly >
												</div>	
												<?php echo Form::text('phone', '', array('class' => 'form-control tel_input', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'', 'id' => 'checkphone' )); ?>

												<?php if($errors->has('phone')): ?>
													<span class="custom-error" role="alert">
														<strong><?php echo e(@$errors->first('phone')); ?></strong>
													</span> 
												<?php endif; ?>
											</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="email_type">
											Email Type <span style="color:#ff0000;">*</span></label>
											<select style="padding: 0px 5px;" name="email_type" id="email_type" class="form-control" data-valid="required">	
												<option value="Personal" <?php if(old('email_type') == 'Personal'){ echo 'selected'; } ?>> Personal</option>
												<option value="Business" <?php if(old('email_type') == 'Business'){ echo 'selected'; } ?>>Business</option>
											</select>
											<?php if($errors->has('email_type')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('email_type')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="email">Email <span style="color:#ff0000;">*</span></label>
											<?php echo Form::text('email', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'', 'id' => 'checkemail' )); ?>

											<?php if($errors->has('email')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('email')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div>													
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="att_email">Email </label>
											<?php echo Form::text('att_email', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?>

											<?php if($errors->has('att_email')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('att_email')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div> 
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="att_phone">Phone</label>
											<div class="cus_field_input">
												<div class="country_code"> 
													<input class="telephone" id="telephone" type="tel" name="att_country_code" readonly >
												</div>	
												<?php echo Form::text('att_phone', '', array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?>

												<?php if($errors->has('att_phone')): ?>
													<span class="custom-error" role="alert">
														<strong><?php echo e(@$errors->first('att_phone')); ?></strong>
													</span> 
												<?php endif; ?>
											</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="visa_type">Visa Type</label>
											<select class="form-control select2" name="visa_type">
											<option value="">- Select Visa Type -</option>
											<?php $__currentLoopData = \App\Models\VisaType::orderby('name', 'ASC')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $visalist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($visalist->name); ?>"><?php echo e($visalist->name); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
											<?php if($errors->has('visa_type')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('visa_type')); ?></strong>
												</span> 
											<?php endif; ?>
									<div style="margin-top:10px;">		
    								<?php echo Form::text('visa_opt', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Visa' )); ?>

    								</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="visaExpiry">Visa Expiry Date</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fas fa-calendar-alt"></i>
													</div>
												</div>
												<?php echo Form::text('visaExpiry', '', array('class' => 'form-control dobdatepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?>

												<?php if($errors->has('visaExpiry')): ?>
													<span class="custom-error" role="alert">
														<strong><?php echo e(@$errors->first('visaExpiry')); ?></strong>
													</span> 
												<?php endif; ?>
											</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="preferredIntake">Preferred Intake</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fas fa-calendar-alt"></i>
													</div>
												</div>
												<?php echo Form::text('preferredIntake', '', array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?>

												<?php if($errors->has('preferredIntake')): ?>
													<span class="custom-error" role="alert">
														<strong><?php echo e(@$errors->first('preferredIntake')); ?></strong>
													</span> 
												<?php endif; ?>
											</div>
										</div> 
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="country_passport">Country of Passport</label>
											<select class="form-control  select2" name="country_passport" >
											<?php
												foreach(\App\Models\Country::all() as $list){
													?>
													<option <?php if(@$list->sortname == 'IN'){ echo 'selected'; } ?> value="<?php echo e(@$list->sortname); ?>" ><?php echo e(@$list->name); ?></option>
													<?php
												}
												?>
											</select>
											
											<?php if($errors->has('country_passport')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('country_passport')); ?></strong>
												</span> 
											<?php endif; ?> 
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="passport_number">Passport Number</label>
											<?php echo Form::text('passport_number', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?>

											<?php if($errors->has('passport_number')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('passport_number')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="address">Address</label>
											<?php echo Form::text('address', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?>

											<?php if($errors->has('address')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('address')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="city">City</label>
											<?php echo Form::text('city', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?>

											<?php if($errors->has('city')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('city')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="state">State</label>
											<select class="form-control" name="state">
												<option value="">- Select State -</option>
												<option value="Australian Capital Territory">Australian Capital Territory</option>
												<option value="New South Wales">New South Wales</option>
												<option value="Northern Territory">Northern Territory</option>
												<option value="Queensland">Queensland</option>
												<option value="South Australia">South Australia</option>
												<option value="Tasmania">Tasmania</option>
												<option value="Victoria">Victoria</option>
												<option value="Western Australia">Western Australia</option>
											</select>
											<?php if($errors->has('state')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('state')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="zip">Post Code</label>
											<?php echo Form::text('zip', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?>

											<?php if($errors->has('zip')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('zip')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div>
								</div>
								<hr style="border-color: #000;"/>
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group"> 
											<label for="country">Country</label>
											<select class="form-control select2" name="country" >
											<?php
												foreach(\App\Models\Country::all() as $list){
													?>
													<option <?php if(@$list->sortname == 'AU'){ echo 'selected'; } ?> value="<?php echo e(@$list->sortname); ?>" ><?php echo e(@$list->name); ?></option>
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
									<div class="col-sm-8">
										<div class="form-group"> 
											<label for="related_files">Similar related files</label>
											<select class="form-control js-data-example-ajaxcc" name="related_files[]">
												
											</select>
											<?php if($errors->has('related_files')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('related_files')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="nomi_occupation">Nominated Occupation</label>
											<?php echo Form::text('nomi_occupation', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?>

											
											<?php if($errors->has('nomi_occupation')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('nomi_occupation')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="skill_assessment">Skill Assessment</label>
											<select class="form-control" name="skill_assessment">
									<option value="">Select</option>
									<option value="Yes">Yes</option>
									<option value="No">No</option>
											</select>
											
											
											<?php if($errors->has('skill_assessment')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('skill_assessment')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="high_quali_aus">Highest Qualification in Australia</label>
											<?php echo Form::text('high_quali_aus', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?>

											
											<?php if($errors->has('high_quali_aus')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('high_quali_aus')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="high_quali_overseas">Highest Qualification Overseas</label>
											<?php echo Form::text('high_quali_overseas', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?>

											
											<?php if($errors->has('high_quali_overseas')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('high_quali_overseas')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group"> 
											<label for="relevant_work_exp_aus">Relevant work experience in Australia</label>
											<?php echo Form::text('relevant_work_exp_aus', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?>

											
											<?php if($errors->has('relevant_work_exp_aus')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('relevant_work_exp_aus')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group"> 
											<label for="relevant_work_exp_over">Relevant work experience in Overseas</label>
											<?php echo Form::text('relevant_work_exp_over', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?>

												
											<?php if($errors->has('relevant_work_exp_over')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('relevant_work_exp_over')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div>									
									<div class="col-sm-4">
										<div class="form-group"> 
											<label for="married_partner">Overall English score</label>
											<?php echo Form::text('married_partner', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?>

												
											<?php if($errors->has('married_partner')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('married_partner')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											
												<div class="form-group"> 
													<label style="display:block;" for="naati_py">Naati/PY </label>
													<div class="form-check form-check-inline">
														<input  class="form-check-input" type="checkbox" id="Naati" value="Naati" name="naati_py[]">
														<label class="form-check-label" for="Naati">Naati</label>
													</div>
													<div class="form-check form-check-inline">
														<input class="form-check-input"  type="checkbox" id="py" value="PY" name="naati_py[]">
														<label class="form-check-label" for="py">PY</label>
													</div>
													<div class="form-check form-check-inline">
													
													</div>
												
												</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="total_points">Total Points</label>
											<?php echo Form::text('total_points', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )); ?>

												
											<?php if($errors->has('total_points')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('total_points')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group"> 
											<label for="start_process">When You want to start Process</label>
												<select class="form-control" name="start_process">
													<option value="">Select</option>
													<option value="As soon As Possible">As soon As Possible</option>
													<option value="In Next 3 Months">In Next 3 Months</option>
													<option value="In Next 6 Months">In Next 6 Months</option>
													<option value="Advise Only">Advise Only</option>
											</select>
											<?php if($errors->has('start_process')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('start_process')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div>
								</div>
								<hr style="border-color: #000;"/>
								<div class="row " id="internal">
									<div class="col-sm-3">
										<div class="form-group">
											<label for="service">Service <span style="color:#ff0000;">*</span></label>
											<select class="form-control select2" name="service" data-valid="required">
											<option value="">- Select Lead Service -</option>
											<?php $__currentLoopData = \App\Models\LeadService::orderby('name', 'ASC')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leadservlist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option <?php if(old('service') == $leadservlist->name){ echo 'selected'; } ?> value="<?php echo e($leadservlist->name); ?>"><?php echo e($leadservlist->name); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
											<?php if($errors->has('service')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('service')); ?></strong>
												</span> 
											<?php endif; ?> 
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="assign_to">Assign To <span style="color:#ff0000;">*</span></label>
											<select style="padding: 0px 5px;" name="assign_to" id="assign_to" class="form-control select2" data-valid="required">
											<?php
												$admins = \App\Models\Admin::where('role','!=',7)->orderby('first_name','ASC')->get();
												foreach($admins as $admin){
													$branchname = \App\Models\Branch::where('id',$admin->office_id)->first();
												?>
												<option <?php if(old('assign_to') == $admin->id){ echo 'selected'; } ?> value="<?php echo $admin->id; ?>"><?php echo $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')'; ?> </option>
												<?php } ?>
											</select>
											<?php if($errors->has('assign_to')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('assign_to')); ?></strong>
												</span> 
											<?php endif; ?> 
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="status">Status</label>
											<select style="padding: 0px 5px;" name="status" id="status" class="form-control" data-valid="">
												<option value="">Select Status</option>
												<option <?php if(old('status') == 'Unassigned'){ echo 'selected'; } ?> value="Unassigned">Unassigned</option>
												<option <?php if(old('status') == 'Assigned'){ echo 'selected'; } ?> value="Assigned">Assigned</option>
												<option <?php if(old('status') == 'In-Progress'){ echo 'selected'; } ?> value="In-Progress">In-Progress</option>
												<option <?php if(old('status') == 'Closed'){ echo 'selected'; } ?> value="Closed">Closed</option>
											</select>
											<?php if($errors->has('status')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('status')); ?></strong>
												</span> 
											<?php endif; ?> 
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="lead_quality">Quality <span style="color:#ff0000;">*</span></label>
											<select style="padding: 0px 5px;" name="lead_quality" id="lead_quality" class="form-control" data-valid="required">
												<option <?php if(old('lead_quality') == '1'){ echo 'selected'; } ?> value="1">1</option>
												<option <?php if(old('lead_quality') == '2'){ echo 'selected'; } ?> value="2">2</option>
												<option <?php if(old('lead_quality') == '3'){ echo 'selected'; } ?> value="3">3</option>
												<option <?php if(old('lead_quality') == '4'){ echo 'selected'; } ?> value="4">4</option>
												<option <?php if(old('lead_quality') == '5'){ echo 'selected'; } ?> value="5">5</option>
											</select>
											<?php if($errors->has('lead_quality')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('lead_quality')); ?></strong>
												</span> 
											<?php endif; ?> 
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="lead_source">Source <span style="color:#ff0000;">*</span></label>
											<select style="padding: 0px 5px;" name="source" id="lead_source" class="form-control select2" data-valid="">
										<option value="">- Source -</option>
									    <option value="Sub Agent">Sub Agent</option>
											<?php $__currentLoopData = \App\Models\Source::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sources): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<option <?php if(old('lead_source') == $sources->name){ echo 'selected'; } ?> value="<?php echo e($sources->name); ?>"><?php echo e($sources->name); ?></option>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
											<?php if($errors->has('lead_source')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('lead_source')); ?></strong>
												</span> 
											<?php endif; ?> 
										</div>
									</div>
									<div class="col-sm-3 is_subagent" style="display:none;">
													<div class="form-group"> 
														<label for="subagent">Sub Agent <span class="span_req">*</span></label>
														<select class="form-control select2" name="subagent">  
															<option>-- Choose a sub agent --</option>
															<?php $__currentLoopData = \App\Models\Agent::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agentlist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($agentlist->id); ?>"><?php echo e($agentlist->full_name); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
														<?php if($errors->has('subagent')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('subagent')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="tags_label">Tags/Label </label>
										<select multiple class="form-control select2" name="tagname[]">
															<option value="">-- Search & Select tag --</option>
														<?php
														foreach(\App\Models\Tag::all() as $tags){
															?>
															<option value="<?php echo e($tags->id); ?>"><?php echo e($tags->name); ?></option>
															<?php
														}
														?>	 
														</select>
										
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<label for="comments_note">Comments / Note</label>
											<textarea class="form-control" name="comments_note" placeholder="" data-valid=""><?php echo e(old('comments_note')); ?></textarea>
											<?php if($errors->has('comments_note')): ?> 
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('comments_note')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div>  
									<div class="col-sm-12">
										<div class="form-group float-right">
											<?php echo Form::button('Save', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-clients")' ]); ?>

										</div>
									</div>
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

<?php $__env->startSection('scripts'); ?>
<script>
jQuery(document).ready(function($){
     $('#checkphone').on('blur', function(){
        var v = $(this).val();
        if(v != ''){
            $.ajax({
                url: '<?php echo e(URL::to('admin/checkclientexist')); ?>',
                type:'GET',
                data:{vl:v,type:'phone'},
                success:function(res){
                    if(res == 1){
                        alert('Phone number is already exist in our record.');
                    }
                }
            });
        }
    });
      $('#checkemail').on('blur', function(){
        var v = $(this).val();
        if(v != ''){
           $. ajax({
                url: '<?php echo e(URL::to('admin/checkclientexist')); ?>',
                type:'GET',
                data:{vl:v,type:'email'},
                success:function(res){
                    if(res == 1){
                        alert('Email is already exist in our record.');
                    }
                }
            });
        }
    });
$('.js-data-example-ajaxcc').select2({
		 multiple: true,
		 closeOnSelect: false,
	
		  ajax: {
			url: '<?php echo e(URL::to('/admin/clients/get-recipients')); ?>',
			dataType: 'json',
			processResults: function (data) {
			  // Transforms the top-level key of the response object from 'items' to 'results'
			  return {
				results: data.items
			  };
			  
			},
			 cache: true
			
		  },
	templateResult: formatRepo,
	templateSelection: formatRepoSelection
});
function formatRepo (repo) {
  if (repo.loading) {
    return repo.text;
  }

  var $container = $(
    "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

      "<div  class='ag-flex ag-align-start'>" +
        "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
        "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small ></div>" +
      
      "</div>" +
      "</div>" +
	   "<div class='ag-flex ag-flex-column ag-align-end'>" +
        
        "<span class='ui label yellow select2-result-repository__statistics'>" +
          
        "</span>" +
      "</div>" +
    "</div>"
  );

  $container.find(".select2-result-repository__title").text(repo.name);
  $container.find(".select2-result-repository__description").text(repo.email);
  $container.find(".select2-result-repository__statistics").append(repo.status);
 
  return $container;
}

function formatRepoSelection (repo) {
  return repo.name || repo.text;
}
});

  var loadFile = function(event) {
    var output = document.getElementById('output');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
      URL.revokeObjectURL(output.src); // free memory
	  $('.if_image').hide();
	  $('#output').css({'width':"100px",'height':"100px"});
    }
  };
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\clients\create.blade.php ENDPATH**/ ?>