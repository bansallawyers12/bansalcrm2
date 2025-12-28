
<?php $__env->startSection('title', 'Tax Setting'); ?>

<?php $__env->startSection('content'); ?>
 
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">New Tax</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Tax Setting</li>
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
				<div class="col-md-4">	
					<div class="card">
						<div class="card-body p-0" style="display: block;">
							<ul class="nav nav-pills flex-column"> <!---->
								<li class="nav-item"> <a href="<?php echo e(route('admin.taxrates')); ?>" id="ember5167" class="nav-link active ember-view"> Tax Rates </a> </li><!----><!----><!----><!----><li class="nav-item"> <a href="<?php echo e(route('admin.returnsetting')); ?>" id="ember5168" class="nav-link ember-view"> GST Settings </a> </li> <!----><!----> </ul>
						</div>
					</div>
				</div>
				<div class="col-md-8">
					<div class="card card-primary">
					  <div class="card-header">
						<h3 class="card-title">New Tax</h3>
					  </div> 
					  <!-- /.card-header -->
					  <!-- form start -->
					  <?php echo Form::open(array('url' => 'admin/settings/taxes/taxrates/store', 'name'=>"add-city", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")); ?>

						<div class="card-body">
							<div class="row">
								<div class="col-sm-12 is_gst_yes">
									<div class="form-group"> 
										<label for="name" class="col-form-label">Tax Name <span style="color:#ff0000;">*</span></label>
										<?php echo Form::text('name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )); ?>

						
										<?php if($errors->has('name')): ?>
											<span class="custom-error" role="alert">
												<strong><?php echo e(@$errors->first('name')); ?></strong>
											</span> 
										<?php endif; ?>
									</div>
								</div>
								<div class="col-sm-12 is_gst_yes ">
									<div class="form-group"> 
										<label for="rate" class="col-form-label">Rate % <span style="color:#ff0000;">*</span></label>
									
										<input type="text" name="rate" onkeyup="this.value=this.value.replace(/[^0-9\.]/g,'')" autocomplete="off" class="form-control" data-valid="required">
										<?php if($errors->has('rate')): ?>
											<span class="custom-error" role="alert">
												<strong><?php echo e(@$errors->first('rate')); ?></strong>
											</span> 
										<?php endif; ?>
									</div>
								</div>
								<div class="col-sm-12" >
									<div class="form-group float-right">
										<?php echo Form::button('<i class="fa fa-save"></i> Save', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-city")' ]); ?>

									</div> 
								</div> 
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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\settings\create.blade.php ENDPATH**/ ?>