
<?php $__env->startSection('title', 'User'); ?>

<?php $__env->startSection('content'); ?>
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
		<div class="server-error">
				<?php echo $__env->make('../Elements/flash-message', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
			</div>
			<div class="custom-error-msg">
			</div>
			<?php echo Form::open(array('url' => 'admin/users/store', 'name'=>"edit-user", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")); ?>

			
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Create Users</h4>
								<div class="card-header-action">
									<a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-6 col-lg-6">
						<div class="card">
							<div class="card-body">
								<h4>PERSONAL DETAILS</h4>
								<div class="form-group"> 
									<label for="first_name">First Name</label>
									<?php echo Form::text('first_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter User First Name' )); ?>

									<?php if($errors->has('first_name')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('first_name')); ?></strong>
										</span> 
									<?php endif; ?>
								</div>
								<div class="form-group">
									<label for="last_name">Last Name</label>
									<?php echo Form::text('last_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter User Last Name' )); ?>

									<?php if($errors->has('last_name')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('last_name')); ?></strong>
										</span> 
									<?php endif; ?>
								</div>
								<div class="form-group">
									<label for="email">Email</label>
									<?php echo Form::text('email', '', array('class' => 'form-control', 'data-valid'=>'required email', 'autocomplete'=>'off','placeholder'=>'Enter Email' )); ?>

									<?php if($errors->has('email')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('email')); ?></strong>
										</span> 
									<?php endif; ?>
								</div>
								
								<div class="form-group">
									<label for="name">Password</label>
									<input type="password" value="" name="password" class="form-control" autocomplete="off" placeholder="Enter User Password" data-valid="required" />							
									<?php if($errors->has('password')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('password')); ?></strong>
										</span> 
									<?php endif; ?>
								</div>
								<div class="form-group">
									<label for="name">Password Confirmation</label>
									<input type="password" value="" name="password_confirmation" class="form-control" autocomplete="off" placeholder="Enter User Password" data-valid="required" />							
									<?php if($errors->has('password')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('password')); ?></strong>
										</span> 
									<?php endif; ?>
								</div>
								<div class="form-group">
									<label for="name">Phone Number</label>
									<div class="cus_field_input">
									<div class="country_code"> 
										<input class="telephone" id="telephone" type="tel" name="country_code" readonly value="<?php echo e(@$fetchedData->telephone); ?>" >
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
					<div class="col-12 col-md-6 col-lg-6">
						<div class="card">
							<div class="card-body">
								<h4>Office DETAILS</h4>
								<div class="form-group">
									<label for="position">Position Title</label>
									<?php echo Form::text('position', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Position Title' )); ?>

									<?php if($errors->has('position')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('position')); ?></strong>
										</span> 
									<?php endif; ?>
								</div>
								
								<div class="form-group">
									<label for="role">User Role (Type)</label>
									<select name="role" id="role" class="form-control" data-valid="required" autocomplete="new-password">
										<option value="">Choose One...</option>
										<?php if(count(@$usertype) !== 0): ?>
											<?php $__currentLoopData = @$usertype; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ut): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e(@$ut->id); ?>"><?php echo e(@$ut->name); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
									</select>
									<?php if($errors->has('role')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('role')); ?></strong>
										</span>
									<?php endif; ?>
								</div>

								<div class="form-group">
                                    <?php
                                    $branchx = \App\Models\Branch::all();
                                    ?>
									<label for="office">Office</label>
									<select class="form-control" data-valid="required" name="office">
										<option value="">Select</option>
										<?php $__currentLoopData = $branchx; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<option value="<?php echo e($branch->id); ?>"><?php echo e($branch->office_name); ?></option>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</select>
									<?php if($errors->has('office')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('office')); ?></strong>
										</span>
									<?php endif; ?>
								</div>



								<div class="form-group">
									<label for="role">Department (Team)</label>
									<select name="team" id="team" class="form-control" data-valid="" autocomplete="new-password">
										<option value="">Choose One...</option>

											<?php $__currentLoopData = \App\Models\Team::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e(@$tm->id); ?>"><?php echo e(@$tm->name); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

									</select>
                                </div>
                                
                                 <div class="form-group">
                                    <label for="role">Permission</label>
							    	<br><b>Notes</b>  &nbsp;&nbsp;&nbsp;&nbsp;
                                    <input value="1" type="checkbox" name="permission[]" class="show_dashboard_per">&nbsp; View &nbsp;
                                    <input value="2" type="checkbox" name="permission[]" class="show_dashboard_per">&nbsp; Add/Edit &nbsp;
                                    <input value="3" type="checkbox" name="permission[]" class="show_dashboard_per">&nbsp; Delete &nbsp;

                                    <br><b>Documents</b>
                                    <input value="4" type="checkbox" name="permission[]" class="show_dashboard_per">&nbsp; View &nbsp;
                                    <input value="5" type="checkbox" name="permission[]" class="show_dashboard_per">&nbsp; Add/Edit &nbsp;
                                    <input value="6" type="checkbox" name="permission[]" class="show_dashboard_per">&nbsp; Delete &nbsp;
                                </div>
                                
								<div class="form-group">
							    	<label><input value="1" type="checkbox" name="show_dashboard_per" class="show_dashboard_per"> Can view on dasboard</label>
								</div>
								<div class="form-group float-right">
									<?php echo Form::submit('Save User', ['class'=>'btn btn-primary' ]); ?>

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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\users\create.blade.php ENDPATH**/ ?>