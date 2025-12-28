
<?php $__env->startSection('title', 'Change Password'); ?>
<?php $__env->startSection('content'); ?>      
<div class="row dashboard">
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading dashboard-main-heading">
				<h3 class="panel-title text-center">
					YOUR DASHBOARD
				</h3>
			</div>
			<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 no-padding">
				<!-- Emergency Note Start-->
					<?php echo $__env->make('../Elements/emergency', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
				<!-- Emergency Note End-->
				
				<!-- Flash Message Start -->
				<div class="server-error">
					<?php echo $__env->make('../Elements/flash-message', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>	
				</div>
				<!-- Flash Message End -->
			
				<div class="panel-body">
					<div class="col-lg-12 col-sm-12 col-md-12 no-padding">
						<div class="tab" role="tabpanel">				
							<!-- Content Start for the Menu Bar Dashboard -->
								<?php echo $__env->make('../Elements/Frontend/navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
							<!-- Content End for the Menu Bar Dashboard -->	
						</div>
					</div>
				</div>
				
				<h3 class="order-summary"><strong>CHANGE PASSWORD</strong></h3>
				<div class="clearfix"></div>	
				<div class="panel-body">
					<div class="col-lg-6 col-sm-12 col-md-6 no-padding">
						<div class="tab" role="tabpanel">
							<div class="tab-content tabs">
								<div role="tabpanel" class="fade in active" id="Section0">		
									<div class="table-responsive">
										<div id="orderSummary_wrapper" class="dataTables_wrapper no-footer">
											<?php echo Form::open(array('url' => 'change_password', 'name'=>"change-password")); ?>

												<?php echo Form::hidden('user_id', @Auth::user()->id); ?>

												<div>
													<div class="form-group">
														<label for="old_password">Old Password<em>*</em></label>
														<?php echo Form::password('old_password', array('class' => 'form-control', 'data-valid'=>'required')); ?>

													
														<?php if($errors->has('old_password')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e($errors->first('old_password')); ?></strong>
															</span>
														<?php endif; ?>
													</div>
													<div class="form-group">
														<label for="password">New Password<em>*</em></label>
														<?php echo Form::password('password', array('class' => 'form-control', 'data-valid'=>'required')); ?>

													
														<?php if($errors->has('password')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e($errors->first('password')); ?></strong>
															</span>
														<?php endif; ?>
													</div>
													<div class="form-group">
														<label for="password_confirmation">Confirm Password<em>*</em></label>
														<?php echo Form::password('password_confirmation', array('class' => 'form-control', 'data-valid'=>'required')); ?>

													
														<?php if($errors->has('password_confirmation')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e($errors->first('password_confirmation')); ?></strong>
															</span>
														<?php endif; ?>
													</div>
													<div class="form-group">
														<?php echo Form::button('Change', ['class'=>'btn btn-primary px-4', 'onClick'=>'customValidate("change-password")']); ?>

													</div>
												</div>
											<?php echo Form::close(); ?>	
											
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard_frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\change_password.blade.php ENDPATH**/ ?>