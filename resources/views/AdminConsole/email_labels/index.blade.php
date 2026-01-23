@extends('layouts.adminconsole')
@section('title', 'Email Labels')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">@include('../Elements/flash-message')</div>
			<div class="custom-error-msg"></div>
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-header">
							<h4>Email Labels</h4>
							<div class="card-header-action">
								<a href="{{route('adminconsole.emaillabels.create')}}" class="btn btn-primary">Create Email Label</a>
							</div>
						</div>
						<div class="card-body">
							<!-- Search and Filter -->
							<form method="GET" action="{{ route('adminconsole.emaillabels.index') }}" class="mb-3">
								<div class="row">
									<div class="col-md-4">
										<input type="text" name="search" class="form-control" placeholder="Search by label name..." value="{{ request('search') }}">
									</div>
									<div class="col-md-3">
										<select name="type" class="form-control">
											<option value="">All Types</option>
											<option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>System Labels</option>
											<option value="custom" {{ request('type') == 'custom' ? 'selected' : '' }}>Custom Labels</option>
										</select>
									</div>
									<div class="col-md-3">
										<select name="status" class="form-control">
											<option value="">All Status</option>
											<option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
											<option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
										</select>
									</div>
									<div class="col-md-2">
										<button type="submit" class="btn btn-primary w-100">Filter</button>
									</div>
								</div>
							</form>

							<div class="table-responsive common_table">
								<table class="table text_wrap">
								<thead>
									<tr>
										<th>Label</th>
										<th>Name</th>
										<th>Type</th>
										<th>Created By</th>
										<th>Status</th>
										<th>Last Updated</th>
										<th>Action</th>
									</tr>
								</thead>
								@if($labels->count() > 0)
								<tbody class="tdata">
								@foreach ($labels as $label)
									<tr id="id_{{$label->id}}">
										<td>
											<span class="badge" style="background-color: {{ $label->color }}; color: white; font-size: 13px;">
												<i class="{{ $label->display_icon }}"></i> {{ $label->name }}
											</span>
										</td>
										<td>{{ $label->name }}</td>
										<td>
											@if($label->type === 'system')
												<span class="badge badge-info">System</span>
											@else
												<span class="badge badge-secondary">Custom</span>
											@endif
										</td>
										<td>
											@if($label->type === 'system')
												<span class="text-muted">System</span>
											@elseif($label->user)
												{{ $label->user->name ?? 'Unknown' }}
											@else
												<span class="text-muted">Super Admin1</span>
											@endif
										</td>
										<td>
											@if($label->is_active)
												<span class="badge badge-success">Active</span>
											@else
												<span class="badge badge-danger">Inactive</span>
											@endif
										</td>
										<td>{{ $label->updated_at->format('Y-m-d H:i') }}</td>
										<td>
											<div class="dropdown d-inline">
												<button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
												<div class="dropdown-menu">
													@if($label->type === 'custom')
														<a class="dropdown-item has-icon" href="{{route('adminconsole.emaillabels.edit', $label->id)}}"><i class="far fa-edit"></i> Edit</a>
													@endif
													<a class="dropdown-item has-icon toggle-status-btn" href="javascript:;" data-id="{{$label->id}}">
														<i class="fas fa-toggle-on"></i> Toggle Status
													</a>
													@if($label->type === 'custom')
														<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction({{$label->id}}, 'email_labels')"><i class="fas fa-trash"></i> Delete</a>
													@endif
												</div>
											</div>
										</td>
									</tr>
								@endforeach
								</tbody>
								@else
								<tbody>
									<tr>
										<td style="text-align:center;" colspan="7">
											No Record found
										</td>
									</tr>
								</tbody>
								@endif
							</table>
						</div>

                        <div class="card-footer">
							{!! $labels->appends(\Request::except('page'))->render() !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
	// Toggle status functionality
	$('.toggle-status-btn').on('click', function(e) {
		e.preventDefault();
		var labelId = $(this).data('id');
		var $row = $('#id_' + labelId);
		
		$.ajax({
			url: '/adminconsole/email-labels/toggle-status/' + labelId,
			type: 'POST',
			data: {
				_token: '{{ csrf_token() }}'
			},
			success: function(response) {
				if(response.status) {
					// Update the status badge
					var statusBadge = $row.find('td:nth-child(5)');
					if(response.new_status) {
						statusBadge.html('<span class="badge badge-success">Active</span>');
					} else {
						statusBadge.html('<span class="badge badge-danger">Inactive</span>');
					}
					
					// Show success message
					showToast('success', response.message);
				}
			},
			error: function() {
				showToast('error', 'Error updating status');
			}
		});
	});
});

function showToast(type, message) {
	// If you have a toast/notification system, use it here
	// Otherwise, use alert
	if (typeof iziToast !== 'undefined') {
		iziToast[type]({
			title: type.charAt(0).toUpperCase() + type.slice(1),
			message: message,
			position: 'topRight'
		});
	} else {
		alert(message);
	}
}
</script>
@endsection
