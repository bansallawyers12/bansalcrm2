

<?php $__env->startSection('title', 'Admin Login'); ?>

<?php $__env->startSection('content'); ?>
	
	<section class="section">
		<div class="container mt-5">
			<div class="row">
				<div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
					<div class="card card-primary">
						<div class="card-header">
							<h4>Login</h4>
						</div>
						<div class="card-body">
							<div class="server-error"> 
								<?php echo $__env->make('../Elements/flash-message', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
							</div>
							
							<form action="<?php echo e(URL::to('admin/login')); ?>" method="post" name="admin_login">
							<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
								<div class="form-group">
									<label for="email">Email</label>
									<input id="email" placeholder="Email" type="email" class="form-control" name="email" tabindex="1" value="<?php echo e((Cookie::get('email') !='' && !old('email')) ? Cookie::get('email') : old('email')); ?>" required autofocus>
									<?php if($errors->has('email')): ?>
									<div style="color: #dc3545;">
									 <?php echo e($errors->first('email')); ?>

									</div>
									<?php endif; ?>
								</div>
								<div class="form-group">
									<div class="d-block">
										<label for="password" class="control-label">Password</label>
										<div class="float-right">
											<!-- <a href="#" class="text-small">Forgot Password?</a> -->
										</div>
									</div>
									<input id="password" type="password" class="form-control" name="password" tabindex="2" placeholder="Password" value="<?php echo e((Cookie::get('password') !='' && !old('password')) ? Cookie::get('password') : old('password')); ?>" required>
									<div class="invalid-feedback">
									  please fill in your password
									</div>
								</div>
								
								<!-- Google Recaptcha -->
                                <div class="g-recaptcha mt-4" data-sitekey=<?php echo e(config('services.recaptcha.key')); ?>></div>

                                <?php if($errors->has('g-recaptcha-response')): ?>
									<div style="color: #dc3545;">Captcha field is required.</div>
								<?php endif; ?>
								
								<div class="form-group">
									<div class="custom-control custom-checkbox">
										<input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me" <?php if(Cookie::get('email') != '' && Cookie::get('password') != ''): ?> checked  <?php endif; ?>>
										<label class="custom-control-label" for="remember-me">Remember Me</label>
									</div>
								</div>
								<div class="form-group">
									<button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">Login</button>
								</div>
							<?php echo Form::close(); ?>

						</div>
					</div>
					<div class="mt-5 text-muted text-center">
						Don't have an account? <a href="<?php echo e(URL::to('/register')); ?>">Create One</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin-login', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\auth\admin-login.blade.php ENDPATH**/ ?>