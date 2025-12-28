
<?php $__env->startSection('title', 'Company Profile'); ?>
<?php $__env->startSection('content'); ?>

<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				<?php echo $__env->make('../Elements/flash-message', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
			</div>
			<div class="custom-error-msg"></div>
			
			<?php echo Form::open(array('url' => 'admin/my_profile', 'name'=>"my-profile", 'enctype'=>'multipart/form-data')); ?>

			<?php echo Form::hidden('id', $fetchedData->id); ?>

			
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Company Profile</h4>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-6 col-md-6 col-lg-6">
									<div class="form-group"> 
										<label for="test_pdf">Company Logo</label>
										<input type="hidden" id="old_profile_img" name="old_profile_img" value="<?php echo e(@$fetchedData->profile_img); ?>" />
										<div class="profile_upload">
											<div class="upload_content">
											<?php if(@$fetchedData->profile_img != ''): ?>
												<img src="<?php echo e(asset('img/profile_imgs')); ?>/<?php echo e(@$fetchedData->profile_img); ?>" style="width:100px;height:100px;" id="output"/> 
											<?php else: ?>
												<img id="output" src="<?php echo e(asset('img/user.png')); ?>"/> 
											<?php endif; ?>
												<i <?php if(@$fetchedData->profile_img != ''){ echo 'style="display:none;"'; } ?> class="fa fa-camera if_image"></i>
												<span <?php if(@$fetchedData->profile_img != ''){ echo 'style="display:none;"'; } ?> class="if_image">Upload Company Logo</span>
											</div>
											<input onchange="loadFile(event)" type="file" id="profile_img" name="profile_img" class="form-control" autocomplete="off" />
										</div>	
									</div> 
										<div class="form-group">
											<?php if(Auth::user()->role == 3): ?>
												<label for="first_name">Organization Name <span style="color:#ff0000;">*</span></label>
											<?php else: ?>
												<label for="first_name">First Name <span style="color:#ff0000;">*</span></label>
											<?php endif; ?>	
											
												<?php echo Form::text('first_name', @$fetchedData->first_name, array('class' => 'form-control', 'data-valid'=>'required')); ?>

										
											<?php if($errors->has('first_name')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e($errors->first('first_name')); ?></strong>
												</span>
											<?php endif; ?>
										</div>
									<?php if(Auth::user()->role != 3): ?>
										<div class="form-group">
											<label for="last_name">Last Name <span style="color:#ff0000;">*</span></label>
												<?php echo Form::text('last_name', @$fetchedData->last_name, array('class' => 'form-control', 'data-valid'=>'required')); ?>

										
											<?php if($errors->has('last_name')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e($errors->first('last_name')); ?></strong>
												</span>
											<?php endif; ?>
										</div>
									<?php endif; ?>
										<div class="form-group">
											<label for="email">Company Email <span style="color:#ff0000;">*</span></label>
												<?php echo Form::text('email', @$fetchedData->email, array('class' => 'form-control', 'data-valid'=>'required email', 'disabled'=>'disabled')); ?>

										
											<?php if($errors->has('email')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e($errors->first('email')); ?></strong>
												</span>
											<?php endif; ?>
										</div>
										<div class="form-group">
											<label for="phone">Company Phone <span style="color:#ff0000;">*</span></label>
												<?php echo Form::text('phone', @$fetchedData->phone, array('class' => 'form-control mask', 'data-valid'=>'required', 'placeholder'=>'000-000-0000')); ?>

										
											<?php if($errors->has('phone')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e($errors->first('phone')); ?></strong>
												</span>
											<?php endif; ?>
										</div>
										<div class="form-group">
											<label for="company_name">Company Name <span style="color:#ff0000;">*</span></label>
												<?php echo Form::text('company_name', @$fetchedData->company_name, array('class' => 'form-control mask', 'data-valid'=>'required', 'placeholder'=>'Company Name')); ?>

										
											<?php if($errors->has('company_name')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e($errors->first('company_name')); ?></strong>
												</span>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-6 col-md-6 col-lg-6">
										<div class="form-group">
											<label for="company_website">Company Website</label>
												<?php echo Form::text('company_website', @$fetchedData->company_website, array('class' => 'form-control mask', 'data-valid'=>'', 'placeholder'=>'Company Website')); ?>

										
											<?php if($errors->has('company_website')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e($errors->first('company_website')); ?></strong>
												</span>
											<?php endif; ?>
										</div>
										<div class="form-group">
											<label for="company_fax">Company Fax</label>
												<?php echo Form::text('company_fax', @$fetchedData->company_fax, array('class' => 'form-control', 'data-valid'=>'', 'placeholder'=>'Company Fax')); ?>

										
											<?php if($errors->has('company_fax')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e($errors->first('company_fax')); ?></strong>
												</span>
											<?php endif; ?>
										</div>
										<div class="form-group">
											<label for="country">Country <span style="color:#ff0000;">*</span></label>
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
													<strong><?php echo e($errors->first('country')); ?></strong>
												</span>
											<?php endif; ?>
										</div>
										<!--<div class="form-group">
											<label for="state">Primary Email </label>
												<?php echo Form::text('primary_email', @$fetchedData->primary_email, array('class' => 'form-control', 'data-valid'=>'email')); ?>

										
											<?php if($errors->has('primary_email')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e($errors->first('primary_email')); ?></strong>
												</span>
											<?php endif; ?>
										</div>	-->
										<div class="form-group">
											<label for="state">State <span style="color:#ff0000;">*</span></label>
												<?php echo Form::text('state', @$fetchedData->state, array('class' => 'form-control', 'data-valid'=>'required')); ?>

										
											<?php if($errors->has('state')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e($errors->first('state')); ?></strong>
												</span>
											<?php endif; ?>
										</div>	
										<div class="form-group">
											<label for="city">City <span style="color:#ff0000;">*</span></label>
												<?php echo Form::text('city', @$fetchedData->city, array('class' => 'form-control', 'data-valid'=>'required')); ?>

										
											<?php if($errors->has('city')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e($errors->first('city')); ?></strong>
												</span>
											<?php endif; ?>
										</div>	
										<div class="form-group">
											<label for="zip">Zip Code <span style="color:#ff0000;">*</span></label>
												<?php echo Form::text('zip', @$fetchedData->zip, array('class' => 'form-control', 'data-valid'=>'required')); ?>

										
											<?php if($errors->has('zip')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e($errors->first('zip')); ?></strong>
												</span>
											<?php endif; ?>
										</div> 	
										<!--<div class="form-group">
											<label for="gst_no">GST No. <span style="color:#ff0000;">*</span></label>
												<?php echo Form::text('gst_no', @$fetchedData->gst_no, array('class' => 'form-control', 'data-valid'=>'required')); ?>

										
											<?php if($errors->has('gst_no')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e($errors->first('gst_no')); ?></strong>
												</span>
											<?php endif; ?>
										</div>-->	
										<div class="form-group">
											<label for="address">Address <span style="color:#ff0000;">*</span></label>
												<?php echo Form::text('address', @$fetchedData->address, array('class' => 'form-control', 'placeholder'=>'Please write Address...', 'data-valid'=>'required')); ?>

										
											<?php if($errors->has('address')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e($errors->first('address')); ?></strong>
												</span>
											<?php endif; ?>
										</div>
									</div>
								</div>																
								<div class="form-group">
									<?php echo Form::button('<i class="fa fa-edit"></i> Update', ['class'=>'btn btn-primary px-4', 'onClick'=>'customValidate("my-profile")']); ?>

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
	$('#select_country').attr('data-selected-country','<?php echo @$fetchedData->country; ?>');
		$('#select_country').flagStrap();
});
</script>
<script>
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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\my_profile.blade.php ENDPATH**/ ?>