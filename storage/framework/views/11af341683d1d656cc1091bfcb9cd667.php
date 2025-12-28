
<?php $__env->startSection('title', 'Users'); ?>

<?php $__env->startSection('content'); ?>

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="row">
			     <div class="col-12 col-md-12 col-lg-12"><div class="custom-error-msg"></div></div>
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Users</h4>
							<div class="card-header-action">
								<a href="javascript:;" class="btn btn-primary">Invite User</a>
							</div>
						</div>
						<div class="card-body">
							<ul class="nav nav-pills" id="user_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link" id="active-tab"  href="<?php echo e(URL::to('/admin/users/active')); ?>" >Active</a>
								</li>
								<li class="nav-item">
									<a class="nav-link active" id="inactive-tab"  href="<?php echo e(URL::to('/admin/users/inactive')); ?>" >Inactive</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="invited-tab"  href="<?php echo e(URL::to('/admin/users/invited')); ?>" >Invited</a>
								</li>								
							</ul>
							<div class="tab-content" id="checkinContent">
								<div class="tab-pane fade show active" id="inactive" role="tabpanel" aria-labelledby="inactive-tab">
									<div class="table-responsive common_table"> 
										<table class="table"> 
											<thead>
												<tr>
												  <th>Name</th>
												  <th>Position</th>
												  <th>Office</th> 
												  <th>Role</th>
												  <th>Status</th>
												</tr> 
											</thead>
											<?php if(@$totalData !== 0): ?>
											<?php $__currentLoopData = @$lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<?php
										$b = \App\Models\Branch::where('id', $list->office_id)->first();
										?>
											<tbody class="tdata">	
												<tr id="id_<?php echo e(@$list->id); ?>"> 
													<td><a href="<?php echo e(URL::to('/admin/users/view')); ?>/<?php echo e(@$list->id); ?>"><?php echo e(@$list->first_name); ?></a><br><?php echo e(@$list->email); ?></td> 
													<td><?php echo e(@$list->position); ?></td>
													<td><a href="<?php echo e(URL::to('/admin/branch/view/')); ?>/<?php echo e(@$b->id); ?>"><?php echo e(@$b->office_name); ?></a></td> 
													
													
													<td><?php echo e(@$list->usertype->name == "" ? config('constants.empty') : str_limit(@$list->usertype->name, '50', '...')); ?></td>  
													<td>
													    <div class="custom-switches">
									<label class="">
										<input value="1" data-id="<?php echo e(@$list->id); ?>"  data-status="<?php echo e(@$list->status); ?>" data-col="status" data-table="admins" type="checkbox" name="custom-switch-checkbox" class="change-status custom-switch-input" <?php echo e((@$list->status == 1 ? 'checked' : '')); ?>>
										<span class="custom-switch-indicator"></span>
									</label>
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
								</div>
							</div>
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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\users\inactive.blade.php ENDPATH**/ ?>