
<?php $__env->startSection('title', 'Client List'); ?>

<?php $__env->startSection('content'); ?>

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Client List</h4>
							<div class="card-header-action">
								<a href="<?php echo e(route('admin.users.createclient')); ?>" class="btn btn-primary">Create Client</a>
							</div>
						</div>
						<div class="card-body">
							<table class="table">
								<thead>
									<tr>
										<th>Company Name</th>
										<th>Owner Name</th>
										<th>Email</th>
										<th>Phone</th>
										<th>Is Active</th>
										<th></th>
									</tr> 
								</thead>
								<tbody class="tdata">	
									<?php if(@$totalData !== 0): ?>
									<?php $__currentLoopData = @$lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>	
									<tr id="id_<?php echo e(@$list->id); ?>"> 
										<td><?php echo e(@$list->company_name == "" ? config('constants.empty') : str_limit(@$list->company_name, '50', '...')); ?></td> 
										<td><?php echo e(@$list->first_name == "" ? config('constants.empty') : str_limit(@$list->first_name, '50', '...')); ?> <?php echo e(@$list->last_name == "" ? config('constants.empty') : str_limit(@$list->last_name, '50', '...')); ?></td> 
										<td><?php echo e(@$list->email == "" ? config('constants.empty') : str_limit(@$list->email, '50', '...')); ?></td> 
										<td><?php echo e(@$list->phone == "" ? config('constants.empty') : str_limit(@$list->phone, '50', '...')); ?></td>
										<td>
											<label class="custom-switch">
												<input type="checkbox" name="is_active" class="custom-switch-input" data-id="<?php echo e(@$list->id); ?>"  data-status="<?php echo e(@$list->status); ?>" data-col="status" data-table="admins" name="option" <?php echo e((@$list->status == 1 ? 'checked' : '')); ?>>
												<span class="custom-switch-indicator"></span>
											</label>
										</td>
										<td>
											<div class="dropdown d-inline">
												<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
												<div class="dropdown-menu">
													<a class="dropdown-item has-icon" href="<?php echo e(URL::to('/admin/users/editclient/'.base64_encode(convert_uuencode(@$list->id)))); ?>"><i class="far fa-edit"></i> Edit</a>
													<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction(<?php echo e(@$list->id); ?>, 'admins')"><i class="fas fa-trash"></i> Delete</a>
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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\users\clientlist.blade.php ENDPATH**/ ?>