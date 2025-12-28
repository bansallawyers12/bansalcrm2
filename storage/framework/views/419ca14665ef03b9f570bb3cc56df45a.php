
<?php $__env->startSection('title', 'Staffs'); ?>

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
				<div class="col-md-12">
					<div class="card">
						<div class="card-header">
							<div class="float-right">
								<a href="<?php echo e(route('admin.staff.create')); ?>" class="btn btn-primary">Create Staff</a>
							</div>
						</div>
						<div class="card-body table-responsive p-0">
							<table class="table table-hover text-nowrap">
							  <thead>
								<tr>
								  <th>First Name</th>
								  <th>Last Name</th>
								  <th>Email</th>
								 
								  <th>Is Active</th>
								  <th>Action</th>
								</tr> 
							  </thead>
							  <tbody class="tdata">	
								<?php if(@$totalData !== 0): ?>
								<?php $__currentLoopData = @$lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>	
								<tr id="id_<?php echo e(@$list->id); ?>"> 
								  <td><?php echo e(@$list->first_name == "" ? config('constants.empty') : str_limit(@$list->first_name, '50', '...')); ?></td> 
								  <td><?php echo e(@$list->last_name == "" ? config('constants.empty') : str_limit(@$list->last_name, '50', '...')); ?></td> 
								  <td><?php echo e(@$list->email == "" ? config('constants.empty') : str_limit(@$list->email, '50', '...')); ?></td>  
								  <td><input data-id="<?php echo e(@$list->id); ?>"  data-status="<?php echo e(@$list->status); ?>" data-col="status" data-table="admins" class="change-status" value="1" type="checkbox" name="is_active" <?php echo e((@$list->status == 1 ? 'checked' : '')); ?> data-bootstrap-switch></td> 	
								  <td><a href="<?php echo e(URL::to('/admin/staff/edit/'.base64_encode(convert_uuencode(@$list->id)))); ?>"><i class="fa fa-edit"></i> Edit</a> / <a href="javascript:;" onClick="deleteAction(<?php echo e(@$list->id); ?>, 'admins')"><i class="fa fa-trash"></i> Delete</a>
								  </td>
								</tr>	
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>						
							  </tbody>
							  <?php else: ?>
							  <tbody>
									<tr>
										<td colspan="2">
											
										</td>
									</tr>
								</tbody>
							<?php endif; ?>
							</table>
							<div class="card-footer">
							 <?php echo $lists->appends(\Request::except('page'))->render(); ?>

							 </div>
						  </div>
					</div>	
				</div>	
			</div>
		</div>
	</section>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\staff\index.blade.php ENDPATH**/ ?>