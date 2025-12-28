
<?php $__env->startSection('title', 'Staff'); ?>

<?php $__env->startSection('content'); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">Staffs</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Staffs</li>
					</ol>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.container-fluid -->
	</div>
	<!-- /.content-header -->	
	<!-- Breadcrumb start-->
	<!--<ol class="breadcrumb">
		<li class="breadcrumb-item active">
			Home / <b>Dashboard</b>
		</li>
		<?php echo $__env->make('../Elements/Admin/breadcrumb', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
	</ol>-->
	<!-- Breadcrumb end-->
	
	<!-- Main content --> 
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<!-- Flash Message Start -->
					<div class="server-error">
						<?php echo $__env->make('../Elements/flash-message', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
					</div>
					<!-- Flash Message End -->
				</div>
				<div class="col-md-6">
					<div class="card card-primary">
					  <div class="card-header">
						<h3 class="card-title">Add Staff</h3>
				  </div>
				  <!-- /.card-header -->
				  <!-- form start -->
				  <form action="<?php echo e(url('admin/staff/store')); ?>" method="POST" name="add-staff" autocomplete="off" enctype="multipart/form-data">
					<?php echo csrf_field(); ?>
						<div class="card-body">	
						  <div class="form-group"> 
							<label for="first_name">User First Name</label>
							<?php echo Form::text('first_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter User First Name' )); ?>

							<?php if($errors->has('first_name')): ?>
								<span class="custom-error" role="alert">
									<strong><?php echo e(@$errors->first('first_name')); ?></strong>
								</span> 
							<?php endif; ?>
						  </div>
						  <div class="form-group">
							<label for="last_name">User Last Name</label>
							<?php echo Form::text('last_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter User Last Name' )); ?>

							<?php if($errors->has('last_name')): ?>
								<span class="custom-error" role="alert">
									<strong><?php echo e(@$errors->first('last_name')); ?></strong>
								</span> 
							<?php endif; ?>
						  </div>
						   <div class="form-group"> 
							<label for="staff_id">Staff Code</label>
							<?php echo Form::text('staff_id', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter User Last Name' )); ?>

							<?php if($errors->has('staff_id')): ?>
								<span class="custom-error" role="alert">
									<strong><?php echo e(@$errors->first('staff_id')); ?></strong>
								</span> 
							<?php endif; ?>
						  </div>
						  <div class="form-group">
							<label for="name">User Email</label>
							<?php echo Form::text('email', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter User Email' )); ?>

							<?php if($errors->has('email')): ?>
								<span class="custom-error" role="alert">
									<strong><?php echo e(@$errors->first('email')); ?></strong>
								</span> 
							<?php endif; ?>
						  </div>
						  <div class="form-group">
							<label for="name">User Password</label>
							<input type="password" name="password" class="form-control" autocomplete="off" placeholder="Enter User Password" data-valid="required" />							
							<?php if($errors->has('password')): ?>
								<span class="custom-error" role="alert">
									<strong><?php echo e(@$errors->first('password')); ?></strong>
								</span> 
							<?php endif; ?>
						  </div>
						  <div class="form-group">
							<label for="name">User Phone</label>
							<?php echo Form::text('phone', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter User Phone' )); ?>

							<?php if($errors->has('phone')): ?>
								<span class="custom-error" role="alert">
									<strong><?php echo e(@$errors->first('phone')); ?></strong>
								</span> 
							<?php endif; ?>
						  </div>
						  
						  <div class="form-group">
							<label for="profile_img">User Profile Image</label>
							<input type="file" name="profile_img" class="form-control" autocomplete="off" data-valid="required" />							
							<?php if($errors->has('profile_img')): ?>
								<span class="custom-error" role="alert">
									<strong><?php echo e(@$errors->first('profile_img')); ?></strong>
								</span> 
							<?php endif; ?>
						  </div>
						  <div class="form-group">
						<button type="submit" class="btn btn-primary">Save</button>
					  </div> 
					</div> 
				  </form>
					</div>	
				</div>	
			</div>
		</div>
	</section>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\staff\create.blade.php ENDPATH**/ ?>