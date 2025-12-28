
<?php $__env->startSection('title', 'Emails'); ?>
 
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
				 <div class="col-3 col-md-3 col-lg-3">
			        	<?php echo $__env->make('../Elements/Admin/setting', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
		        </div>       
				<div class="col-9 col-md-9 col-lg-9">
					<div class="card">
						<div class="card-header">
							<h4>All Emails</h4>
							<div class="card-header-action">
								<a href="<?php echo e(route('admin.emails.create')); ?>" class="btn btn-primary">Create Emails</a>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive common_table"> 
								<table class="table text_wrap">
								<thead>
									<tr>
										
										<th>Name</th>
										<th>User Sharing</th>
										<th>Status</th>
										<th></th>
									</tr> 
								</thead>
								<?php if(@$totalData !== 0): ?>
								<?php $i=0; ?>
								<tbody class="tdata">	
								<?php $__currentLoopData = @$lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php
                                    if( isset($list->user_id) && $list->user_id != ''){
                                        $userids = json_decode($list->user_id);
										$username = '';
										foreach($userids as $userid){
											$users = \App\Models\Admin::where('id', $userid)->first();
											$username .= $users->first_name.', ';
										}
                                    } ?>
									<tr id="id_<?php echo e(@$list->id); ?>">
										
										<td><?php echo e(@$list->email == "" ? config('constants.empty') : str_limit(@$list->email, '50', '...')); ?></td> 	
										<td><?php echo e(@$username == "" ? config('constants.empty') : str_limit(rtrim(@$username,', '), '50', '...')); ?></td> 	
										<td>
										<?php
										if($list->status == 1){ echo '<span class=" text-success">Active</span>'; }else{
											echo '<span class=" text-danger">Inactive</span>';
										}
										?>
										</td> 	
										<td>
											<div class="dropdown d-inline">
												<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
												<div class="dropdown-menu">
													<a class="dropdown-item has-icon" href="<?php echo e(URL::to('/admin/emails/edit/'.base64_encode(convert_uuencode(@$list->id)))); ?>"><i class="far fa-edit"></i> Edit</a>
													<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction(<?php echo e(@$list->id); ?>, 'emails')"><i class="fas fa-trash"></i> Delete</a>
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
	</section>
</div>
 
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\feature\emails\index.blade.php ENDPATH**/ ?>