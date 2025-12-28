
<?php $__env->startSection('title', 'Notifications'); ?>

<?php $__env->startSection('content'); ?>

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Notifications</h4>
							<div class="card-header-action">
								<?php if(isset($unreadCount) && $unreadCount > 0): ?>
								<button type="button" class="btn btn-sm btn-primary" id="markAllReadBtn">
									<i class="fas fa-check-double"></i> Mark All as Read
								</button>
								<?php endif; ?>
							</div>
						</div>
						<div class="card-body">
							<!-- Filter Tabs -->
							<ul class="nav nav-tabs mb-3" id="notificationTabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link <?php echo e(!request('filter') || request('filter') == 'all' ? 'active' : ''); ?>" 
									   href="<?php echo e(route('admin.notifications.index', ['filter' => 'all', 'search' => request('search')])); ?>">
										All <span class="badge badge-secondary"><?php echo e(isset($totalCount) ? $totalCount : 0); ?></span>
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link <?php echo e(request('filter') == 'unread' ? 'active' : ''); ?>" 
									   href="<?php echo e(route('admin.notifications.index', ['filter' => 'unread', 'search' => request('search')])); ?>">
										Unread <span class="badge badge-danger"><?php echo e(isset($unreadCount) ? $unreadCount : 0); ?></span>
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link <?php echo e(request('filter') == 'read' ? 'active' : ''); ?>" 
									   href="<?php echo e(route('admin.notifications.index', ['filter' => 'read', 'search' => request('search')])); ?>">
										Read <span class="badge badge-success"><?php echo e(isset($readCount) ? $readCount : 0); ?></span>
									</a>
								</li>
							</ul>

							<!-- Search Bar -->
							<form method="GET" action="<?php echo e(route('admin.notifications.index')); ?>" class="mb-3">
								<div class="input-group">
									<input type="text" name="search" class="form-control" 
										   placeholder="Search notifications..." 
										   value="<?php echo e(request('search')); ?>">
									<?php if(request('filter')): ?>
										<input type="hidden" name="filter" value="<?php echo e(request('filter')); ?>">
									<?php endif; ?>
									<div class="input-group-append">
										<button class="btn btn-primary" type="submit">
											<i class="fas fa-search"></i>
										</button>
										<?php if(request('search')): ?>
										<a href="<?php echo e(route('admin.notifications.index', ['filter' => request('filter')])); ?>" 
										   class="btn btn-secondary">
											<i class="fas fa-times"></i>
										</a>
										<?php endif; ?>
									</div>
								</div>
							</form>

							<!-- Notifications Table -->
							<?php if(count($lists) > 0): ?>
							<div class="table-responsive">
								<table class="table table-hover">
									<thead>
										<tr>
											<th width="50">Status</th>
											<th>Notification</th>
											<th width="180">Date & Time</th>
											<th width="100">Action</th>
										</tr>
									</thead>
									<tbody>
										<?php $__currentLoopData = $lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr id="notification_<?php echo e(@$list->id); ?>" 
											class="<?php echo e($list->receiver_status == 0 ? 'notification-unread' : 'notification-read'); ?>">
											<td>
												<?php if($list->receiver_status == 1): ?>
													<span class="badge badge-success" title="Read">
														<i class="fas fa-check-circle"></i>
													</span>
												<?php else: ?>
													<span class="badge badge-danger" title="Unread">
														<i class="fas fa-circle"></i>
													</span>
												<?php endif; ?>
											</td>
											<td>
												<div class="notification-content">
													<?php
														$iconClass = 'fas fa-bell';
														$iconColor = 'text-primary';
														if(strpos(strtolower($list->message), 'client') !== false) {
															$iconClass = 'fas fa-user';
															$iconColor = 'text-info';
														} elseif(strpos(strtolower($list->message), 'office visit') !== false || strpos(strtolower($list->message), 'visit') !== false) {
															$iconClass = 'fas fa-building';
															$iconColor = 'text-warning';
														} elseif(strpos(strtolower($list->message), 'followup') !== false || strpos(strtolower($list->message), 'follow up') !== false) {
															$iconClass = 'fas fa-calendar-check';
															$iconColor = 'text-success';
														}
													?>
													<i class="<?php echo e($iconClass); ?> <?php echo e($iconColor); ?> mr-2"></i>
													<?php if($list->url): ?>
														<a href="<?php echo e($list->url); ?>?t=<?php echo e($list->id); ?>" 
														   class="notification-link <?php echo e($list->receiver_status == 0 ? 'font-weight-bold' : ''); ?>">
															<?php echo e($list->message); ?>

														</a>
													<?php else: ?>
														<span class="<?php echo e($list->receiver_status == 0 ? 'font-weight-bold' : ''); ?>">
															<?php echo e($list->message); ?>

														</span>
													<?php endif; ?>
												</div>
											</td>
											<td>
												<div class="notification-date">
													<?php
														try {
															$createdAt = \Carbon\Carbon::parse($list->created_at);
															$now = \Carbon\Carbon::now();
															$diffInHours = $createdAt->diffInHours($now);
														} catch(\Exception $e) {
															$diffInHours = 999;
														}
													?>
													<?php if($diffInHours < 24 && isset($createdAt)): ?>
														<small class="text-muted"><?php echo e($createdAt->diffForHumans()); ?></small>
													<?php else: ?>
														<small class="text-muted"><?php echo e(date('d/m/Y', strtotime($list->created_at))); ?></small>
													<?php endif; ?>
													<br>
													<small class="text-muted"><?php echo e(date('h:i A', strtotime($list->created_at))); ?></small>
												</div>
											</td>
											<td>
												<?php if($list->receiver_status == 0): ?>
												<button type="button" 
														class="btn btn-sm btn-outline-primary mark-read-btn" 
														data-id="<?php echo e($list->id); ?>"
														title="Mark as read">
													<i class="fas fa-check"></i>
												</button>
												<?php else: ?>
												<span class="text-muted">
													<i class="fas fa-check text-success"></i>
												</span>
												<?php endif; ?>
											</td>
										</tr>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</tbody>
								</table>
							</div>
							<?php else: ?>
							<div class="text-center py-5">
								<i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
								<p class="text-muted">No notifications found.</p>
								<?php if(request('search') || request('filter')): ?>
								<a href="<?php echo e(route('admin.notifications.index')); ?>" class="btn btn-sm btn-primary">
									View All Notifications
								</a>
								<?php endif; ?>
							</div>
							<?php endif; ?>
						</div>
						<?php if(count($lists) > 0): ?>
						<div class="card-footer">
							<?php echo $lists->appends(\Request::except('page'))->render(); ?>

						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<style>
	.notification-unread {
		background-color: #f8f9fa;
	}
	.notification-unread:hover {
		background-color: #e9ecef;
	}
	.notification-read {
		opacity: 0.85;
	}
	.notification-content {
		display: flex;
		align-items: center;
	}
	.notification-link {
		color: #495057;
		text-decoration: none;
	}
	.notification-link:hover {
		color: #007bff;
		text-decoration: underline;
	}
	.notification-date {
		font-size: 0.875rem;
	}
	.mark-read-btn {
		padding: 0.25rem 0.5rem;
	}
	.nav-tabs .badge {
		margin-left: 5px;
	}
</style>

<script>
$(document).ready(function(){
	// Mark single notification as read
	$(document).on('click', '.mark-read-btn', function(){
		var notificationId = $(this).data('id');
		var btn = $(this);
		
		$.ajax({
			url: "<?php echo e(route('admin.notifications.mark-read')); ?>",
			method: 'POST',
			data: {
				id: notificationId,
				_token: '<?php echo e(csrf_token()); ?>'
			},
			success: function(response){
				if(response.success){
					// Update the row
					var row = $('#notification_' + notificationId);
					row.removeClass('notification-unread').addClass('notification-read');
					row.find('.badge-danger').removeClass('badge-danger').addClass('badge-success')
						.html('<i class="fas fa-check-circle"></i>');
					row.find('.notification-link, .notification-content span').removeClass('font-weight-bold');
					btn.replaceWith('<span class="text-muted"><i class="fas fa-check text-success"></i></span>');
					
					// Update unread count in tab
					var unreadBadge = $('.nav-link:contains("Unread")').find('.badge');
					var currentCount = parseInt(unreadBadge.text());
					if(currentCount > 0){
						unreadBadge.text(currentCount - 1);
					}
					
					// Update all count
					var allBadge = $('.nav-link:contains("All")').find('.badge');
					var allCount = parseInt(allBadge.text());
					allBadge.text(allCount);
					
					// Update read count
					var readBadge = $('.nav-link:contains("Read")').find('.badge');
					var readCount = parseInt(readBadge.text());
					readBadge.text(readCount + 1);
					
					// Show success message
					iziToast.success({
						title: 'Success',
						message: response.message,
						position: 'topRight'
					});
				}
			},
			error: function(){
				iziToast.error({
					title: 'Error',
					message: 'Failed to mark notification as read',
					position: 'topRight'
				});
			}
		});
	});
	
	// Mark all notifications as read
	$('#markAllReadBtn').on('click', function(){
		if(!confirm('Are you sure you want to mark all notifications as read?')){
			return;
		}
		
		var btn = $(this);
		btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
		
		$.ajax({
			url: "<?php echo e(route('admin.notifications.mark-all-read')); ?>",
			method: 'POST',
			data: {
				_token: '<?php echo e(csrf_token()); ?>'
			},
			success: function(response){
				if(response.success){
					// Reload the page to reflect changes
					window.location.reload();
				}
			},
			error: function(){
				btn.prop('disabled', false).html('<i class="fas fa-check-double"></i> Mark All as Read');
				iziToast.error({
					title: 'Error',
					message: 'Failed to mark all notifications as read',
					position: 'topRight'
				});
			}
		});
	});
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\notifications.blade.php ENDPATH**/ ?>