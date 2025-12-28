
<?php $__env->startSection('title', 'Audit Logs'); ?>

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
							<h4>Audit Logs</h4>
						</div>
						<div class="card-body">
							<div class="table-responsive"> 
								<table class="table text_wrap">
									<thead>
										<tr>
											<th>ID</th>
											<th>Level</th>
											<th>Date</th>
											<th>User</th>
											<th>IP Address</th>
											<th>User Agent</th>
											<th>Message</th>
										</tr> 
									</thead>
									<tbody class="tdata">	
									<?php $__currentLoopData = $lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr>
											<td><?php echo e($list->id); ?></td> 
											<td><?php if($list->level == 'info'): ?><span class="ag-label--circular" style="color: #008000;">Info</span><?php elseif($list->level == 'critical'): ?>
												<span class="ag-label--circular" style="color: #e46363;">Critical</span><?php elseif($list->level == 'warning'): ?><span class="ag-label--circular" style="color: #ffbd72;">Warning</span><?php endif; ?></td> 
											<td><?php echo e(date('d/m/Y', strtotime($list->created_at))); ?></td> 
											<td>
											<?php
											if($list->user_id != ''){
												$user = \App\Models\Admin::where('id', $list->user_id)->first();
												if($user){
												?>
												<a href="#"><?php echo e($user->first_name); ?></a>
												<?php
												}
											}
											?>
											</td> 
											<td><a target="_blank" href="https://whatismyipaddress.com/ip/<?php echo e($list->ip_address); ?>"><?php echo e($list->ip_address); ?></a></td> 
											<td></td> 
											<td><?php echo e($list->message); ?></td> 
										</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										
									</tbody>  
								</table> 
							</div>
							
						</div>
						<div class="card-footer"><?php echo $lists->appends(\Request::except('page'))->render(); ?></div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\auditlogs\index.blade.php ENDPATH**/ ?>