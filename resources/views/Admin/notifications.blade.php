@extends('layouts.admin')
@section('title', 'Notifications')

@section('content')

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
								@if(isset($unreadCount) && $unreadCount > 0)
								<button type="button" class="btn btn-sm btn-primary" id="markAllReadBtn">
									<i class="fas fa-check-double"></i> Mark All as Read
								</button>
								@endif
							</div>
						</div>
						<div class="card-body">
							<!-- Filter Tabs -->
							<ul class="nav nav-tabs mb-3" id="notificationTabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link {{!request('filter') || request('filter') == 'all' ? 'active' : ''}}" 
									   href="{{route('admin.notifications.index', ['filter' => 'all', 'search' => request('search')])}}">
										All <span class="badge badge-secondary">{{isset($totalCount) ? $totalCount : 0}}</span>
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link {{request('filter') == 'unread' ? 'active' : ''}}" 
									   href="{{route('admin.notifications.index', ['filter' => 'unread', 'search' => request('search')])}}">
										Unread <span class="badge badge-danger">{{isset($unreadCount) ? $unreadCount : 0}}</span>
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link {{request('filter') == 'read' ? 'active' : ''}}" 
									   href="{{route('admin.notifications.index', ['filter' => 'read', 'search' => request('search')])}}">
										Read <span class="badge badge-success">{{isset($readCount) ? $readCount : 0}}</span>
									</a>
								</li>
							</ul>

							<!-- Search Bar -->
							<form method="GET" action="{{route('admin.notifications.index')}}" class="mb-3">
								<div class="input-group">
									<input type="text" name="search" class="form-control" 
										   placeholder="Search notifications..." 
										   value="{{request('search')}}">
									@if(request('filter'))
										<input type="hidden" name="filter" value="{{request('filter')}}">
									@endif
									<div class="input-group-append">
										<button class="btn btn-primary" type="submit">
											<i class="fas fa-search"></i>
										</button>
										@if(request('search'))
										<a href="{{route('admin.notifications.index', ['filter' => request('filter')])}}" 
										   class="btn btn-secondary">
											<i class="fas fa-times"></i>
										</a>
										@endif
									</div>
								</div>
							</form>

							<!-- Notifications Table -->
							@if(count($lists) > 0)
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
										@foreach($lists as $list)
										<tr id="notification_{{@$list->id}}" 
											class="{{$list->receiver_status == 0 ? 'notification-unread' : 'notification-read'}}">
											<td>
												@if($list->receiver_status == 1)
													<span class="badge badge-success" title="Read">
														<i class="fas fa-check-circle"></i>
													</span>
												@else
													<span class="badge badge-danger" title="Unread">
														<i class="fas fa-circle"></i>
													</span>
												@endif
											</td>
											<td>
												<div class="notification-content">
													@php
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
													@endphp
													<i class="{{$iconClass}} {{$iconColor}} me-2"></i>
													@if($list->url)
														<a href="{{$list->url}}?t={{$list->id}}" 
														   class="notification-link {{$list->receiver_status == 0 ? 'font-weight-bold' : ''}}">
															{{$list->message}}
														</a>
													@else
														<span class="{{$list->receiver_status == 0 ? 'font-weight-bold' : ''}}">
															{{$list->message}}
														</span>
													@endif
												</div>
											</td>
											<td>
												<div class="notification-date">
													@php
														try {
															$createdAt = \Carbon\Carbon::parse($list->created_at);
															$now = \Carbon\Carbon::now();
															$diffInHours = $createdAt->diffInHours($now);
														} catch(\Exception $e) {
															$diffInHours = 999;
														}
													@endphp
													@if($diffInHours < 24 && isset($createdAt))
														<small class="text-muted">{{$createdAt->diffForHumans()}}</small>
													@else
														<small class="text-muted">{{date('d/m/Y', strtotime($list->created_at))}}</small>
													@endif
													<br>
													<small class="text-muted">{{date('h:i A', strtotime($list->created_at))}}</small>
												</div>
											</td>
											<td>
												@if($list->receiver_status == 0)
												<button type="button" 
														class="btn btn-sm btn-outline-primary mark-read-btn" 
														data-id="{{$list->id}}"
														title="Mark as read">
													<i class="fas fa-check"></i>
												</button>
												@else
												<span class="text-muted">
													<i class="fas fa-check text-success"></i>
												</span>
												@endif
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							@else
							<div class="text-center py-5">
								<i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
								<p class="text-muted">No notifications found.</p>
								@if(request('search') || request('filter'))
								<a href="{{route('admin.notifications.index')}}" class="btn btn-sm btn-primary">
									View All Notifications
								</a>
								@endif
							</div>
							@endif
						</div>
						@if(count($lists) > 0)
						<div class="card-footer">
							{!! $lists->appends(\Request::except('page'))->render() !!}
						</div>
						@endif
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
			url: "{{route('admin.notifications.mark-read')}}",
			method: 'POST',
			data: {
				id: notificationId,
				_token: '{{csrf_token()}}'
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
			url: "{{route('admin.notifications.mark-all-read')}}",
			method: 'POST',
			data: {
				_token: '{{csrf_token()}}'
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

@endsection
