
<?php $__env->startSection('title', 'Branches'); ?>

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
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>All Branches</h4>
							<div class="card-header-action">
								<a href="<?php echo e(route('admin.branch.create')); ?>" class="btn btn-primary">Create Branch</a>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive"> 
								<table class="table text_wrap">
									<thead>
										<tr>
											<th>Name</th>
											<th>City</th>
											<th>Country</th>
											<th>Mobile</th>
											<th>Phone</th>
											<th>Contact Person</th>
											<th></th>
										</tr> 
									</thead>
									<?php if(@$totalData !== 0): ?>
									<?php $__currentLoopData = @$lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tbody class="tdata">	
										<tr id="id_<?php echo e(@$list->id); ?>">
											<td><a href="<?php echo e(URL::to('/admin/branch/view')); ?>/<?php echo e($list->id); ?>"><?php echo e(@$list->office_name == "" ? config('constants.empty') : str_limit(@$list->office_name, '50', '...')); ?></a></td> 
											<td><?php echo e(@$list->city == "" ? config('constants.empty') : str_limit(@$list->city, '50', '...')); ?></td> 
											<td><?php echo e(@$list->country == "" ? config('constants.empty') : str_limit(@$list->country, '50', '...')); ?></td> 
											<td><?php echo e(@$list->mobile == "" ? config('constants.empty') : str_limit(@$list->mobile, '50', '...')); ?></td> 
											<td><?php echo e(@$list->phone == "" ? config('constants.empty') : str_limit(@$list->phone, '50', '...')); ?></td> 
											<td><?php echo e(@$list->contact_person == "" ? config('constants.empty') : str_limit(@$list->contact_person, '50', '...')); ?></td> 	
											<td>
												<div class="dropdown d-inline">
													<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
													<div class="dropdown-menu">
														<a class="dropdown-item has-icon" href="<?php echo e(URL::to('/admin/branch/edit/'.base64_encode(convert_uuencode(@$list->id)))); ?>"><i class="far fa-edit"></i> Edit</a>
														<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction(<?php echo e(@$list->id); ?>, 'branches')"><i class="fas fa-trash"></i> Delete</a>
													</div>
												</div>								  
											</td>
										</tr>	
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
									</tbody>
									<?php else: ?>
									<tbody>
										<tr>
											<td style="text-align:center;" colspan="7">
												No Record found
											</td>
										</tr>
									</tbody>
									<?php endif; ?>
								</table> 
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\branch\index.blade.php ENDPATH**/ ?>