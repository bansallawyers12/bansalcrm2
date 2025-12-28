
<?php $__env->startSection('title', 'Email Template'); ?>

<?php $__env->startSection('content'); ?>
 
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">Email Template</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Email Template</li>
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
				<div class="col-md-12">
					<div class="card card-primary">
					  <div class="card-header">
						<h3 class="card-title">Create Email Template</h3>
					  </div> 
					  <!-- /.card-header -->
					  <!-- form start -->
					  <?php echo Form::open(array('url' => 'admin/email_templates/store', 'name'=>"add-template", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")); ?>

					   
						<div class="card-body">
							<div class="form-group" style="text-align:right;">
								<a style="margin-right:5px;" href="<?php echo e(route('admin.email.index')); ?>" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>  
								<?php echo Form::button('<i class="fa fa-save"></i> Save Template', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-template")' ]); ?>

							</div>
							<div class="form-group row"> 
								<label for="title" class="col-sm-2 col-form-label">Name <span style="color:#ff0000;">*</span></label>
								<div class="col-sm-10">
								<?php echo Form::text('title', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Name' )); ?>

								<?php if($errors->has('title')): ?>
									<span class="custom-error" role="alert">
										<strong><?php echo e(@$errors->first('title')); ?></strong>
									</span> 
								<?php endif; ?>
								</div>
						  </div>
						  <div class="form-group row"> 
								<label for="subject" class="col-sm-2 col-form-label">Subject <span style="color:#ff0000;">*</span></label>
								<div class="col-sm-10">
								<?php echo Form::text('subject', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Subject' )); ?>

								<?php if($errors->has('subject')): ?>
									<span class="custom-error" role="alert">
										<strong><?php echo e(@$errors->first('subject')); ?></strong>
									</span> 
								<?php endif; ?>
								</div>
						  </div>
						 
						  <div class="form-group row">
								<label for="description" class="col-sm-2 col-form-label">Description <span style="color:#ff0000;">*</span></label>
								<div class="col-sm-10">
									<textarea name="description" data-valid="required" value="" class="textarea" placeholder="Please Add Description Here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
									<?php if($errors->has('description')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('description')); ?></strong>
										</span> 
									<?php endif; ?>
								</div>
						  </div>
						  
						  <div class="form-group float-right">
							<?php echo Form::button('<i class="fa fa-save"></i> Save Template', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-template")' ]); ?>

						  </div> 
						</div> 
					  <?php echo Form::close(); ?>

					</div>	   
				</div>	
			</div>
		</div>
	</section>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\email_template\create.blade.php ENDPATH**/ ?>