
<?php $__env->startSection('title', 'Reset Password'); ?>
<?php $__env->startSection('content'); ?>
<div class="row">
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
		<!-- Flash Message Start -->
			<div class="server-error">
				<?php echo $__env->make('../Elements/flash-message', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
			</div>
		<!-- Flash Message End -->
	
		<!-- Login Start -->
			<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
			</div>	
			<div class="col-lg-5 col-sm-5 col-md-5 col-xs-12 no-padding">
				<div class="form-box login-form-box col-lg-12 col-sm-12 col-md-12 col-xs-12 no-padding">
					<div class="form-top  col-lg-12 col-sm-12 col-md-12 col-xs-12">
						<div class="form-top-left ">
							<h3>Reset Password</h3>
							<p>Please enter the below fields to change the password.</p>
						</div>
						<div class="form-top-right">
							<i class="fa fa-lock"></i>
						</div>
					</div>
					<div class="form-bottom  col-lg-12 col-sm-12 col-md-12 col-xs-12">
						<?php echo Form::open(array('url' => '/reset_link', 'name'=>"reset_link", 'autocomplete'=>'off', 'class'=>'reset-link-form')); ?>

							<?php echo Form::hidden('id', @$data->id); ?>

							<?php echo Form::hidden('email', @$data->email); ?>

							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								<input type="password" placeholder="New Password*" class="form-mobile form-control" name="password" autocomplete="new-password" data-valid="required" />

								<?php if($errors->has('password')): ?>
									<span class="custom-error" role="alert">
										<strong><?php echo e(@$errors->first('password')); ?></strong>
									</span>
								<?php endif; ?>
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								<input type="password" placeholder="Confirm Password*" class="form-mobile form-control" name="password_confirmation" autocomplete="new-password" data-valid="required" />

								<?php if($errors->has('password_confirmation')): ?>
									<span class="custom-error" role="alert">
										<strong><?php echo e(@$errors->first('password_confirmation')); ?></strong>
									</span>
								<?php endif; ?>
							</div>

							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								<?php echo Form::button('Reset', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("reset_link")']); ?>

							</div>
						<?php echo Form::close(); ?>	
					</div>
				</div>
			</div>
		<!-- Login End -->
	</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard_frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\reset_link.blade.php ENDPATH**/ ?>