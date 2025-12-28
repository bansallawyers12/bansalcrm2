
<?php $__env->startSection('title', 'User Type'); ?>

<?php $__env->startSection('content'); ?>

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>User Type</h4>
							<div class="card-header-action">
								<a href="<?php echo e(route('admin.usertype.create')); ?>" class="btn btn-primary">Create User Type</a>
							</div>
						</div>
						<div class="card-body">
							<table class="table">
								<thead>
									<tr>
									 <th>User Type</th>
									  <th></th>
									</tr> 
								</thead>
								<tbody class="tdata">	
								<?php if(@$totalData !== 0): ?>
								<?php $__currentLoopData = @$lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr id="id_<?php echo e(@$list->id); ?>"> 
										<td><?php echo e(@$list->name == "" ? config('constants.empty') : str_limit(@$list->name, '50', '...')); ?></td> 	
										<td>
											<div class="dropdown d-inline">
												<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
												<div class="dropdown-menu">
													<a class="dropdown-item has-icon" href="<?php echo e(URL::to('/admin/usertype/edit/'.base64_encode(convert_uuencode(@$list->id)))); ?>"><i class="far fa-edit"></i> Edit</a>
													<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction(<?php echo e(@$list->id); ?>, 'user_types')"><i class="fas fa-trash"></i> Delete</a>
												</div>
											</div>								  
										</td>
									</tr>	
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
								</tbody>
								<?php else: ?>
								<tbody>
									<tr>
										<td style="text-align:center;" colspan="6">
											No Record found
										</td>
									</tr>
								</tbody>
								<?php endif; ?>
							</table> 
						</div>
						<div class="card-footer">
							<?php echo $lists->appends(\Request::except('page'))->render(); ?>

						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\usertype\index.blade.php ENDPATH**/ ?>