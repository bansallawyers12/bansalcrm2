@extends('layouts.adminconsole')
@section('title', 'Recently Modified Clients')
 
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
							<h4>Recently Modified Clients</h4>
							<div class="card-header-action">
								<span class="badge badge-primary">Total: {{ @$totalData }}</span>
							</div>
						</div>
						<div class="card-body">
							<!-- Filter Panel -->
							<div class="filter-panel mb-4" style="background: #f7f7f7; padding: 20px; border: 1px solid #eee; border-radius: 5px;">
								<form method="GET" action="{{ route('adminconsole.recentclients.index') }}" id="filterForm">
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label for="from_date"><strong>From Date</strong></label>
												<input type="text" 
													   name="from_date" 
													   id="from_date" 
													   class="form-control filterdatepicker" 
													   value="{{ @$fromDate }}" 
													   placeholder="Select start date">
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label for="to_date"><strong>To Date</strong></label>
												<input type="text" 
													   name="to_date" 
													   id="to_date" 
													   class="form-control filterdatepicker" 
													   value="{{ @$toDate }}" 
													   placeholder="Select end date">
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label for="sort_order"><strong>Sort Order</strong></label>
												<select name="sort_order" id="sort_order" class="form-control">
													<option value="desc" {{ @$sortOrder == 'desc' ? 'selected' : '' }}>Newest First</option>
													<option value="asc" {{ @$sortOrder == 'asc' ? 'selected' : '' }}>Oldest First</option>
												</select>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label>&nbsp;</label>
												<div>
													<button type="submit" class="btn btn-primary">
														<i class="fas fa-filter"></i> Apply Filters
													</button>
													<a href="{{ route('adminconsole.recentclients.index') }}" class="btn btn-secondary">
														<i class="fas fa-times"></i> Clear
													</a>
												</div>
											</div>
										</div>
									</div>
								</form>
							</div>
							<div class="table-responsive common_table"> 
								<table class="table text_wrap table-striped">
								<thead>
									<tr>
										<th>Client Name</th>
										<th>Email</th>
										<th>Phone</th>
										<th>Last Activity</th>
										<th class="sortable-header" data-sort-column="activity_date" style="cursor: pointer;">
											Activity Date
											@if(@$sortOrder == 'desc')
												<i class="fas fa-sort-down ml-1" title="Sorted: Newest First"></i>
											@else
												<i class="fas fa-sort-up ml-1" title="Sorted: Oldest First"></i>
											@endif
										</th>
										<th>Modified By</th>
										<th>Action</th>
									</tr> 
								</thead>
								@if(@$totalData !== 0)
								<tbody class="tdata">	
								@foreach (@$lists as $list)
									<tr id="id_{{@$list->activity_id}}" class="client-row">
										<td>
											@if(@$list->client_id)
												<a href="javascript:void(0);" 
												   class="client-name-toggle" 
												   data-client-id="{{ @$list->client_id }}"
												   data-activity-id="{{ @$list->activity_id }}"
												   style="cursor: pointer; text-decoration: none; color: inherit;">
													<i class="fas fa-chevron-right toggle-icon" style="margin-right: 5px; transition: transform 0.3s;"></i>
													@if(@$list->client_firstname || @$list->client_lastname)
														{{ @$list->client_firstname }} {{ @$list->client_lastname }}
													@else
														<span class="text-muted">{{ config('constants.empty') }}</span>
													@endif
												</a>
											@else
												@if(@$list->client_firstname || @$list->client_lastname)
													{{ @$list->client_firstname }} {{ @$list->client_lastname }}
												@else
													<span class="text-muted">{{ config('constants.empty') }}</span>
												@endif
											@endif
										</td> 	
										<td>{{ empty(@$list->client_email) ? config('constants.empty') : \Illuminate\Support\Str::limit(@$list->client_email, 40, '...') }}</td> 	
										<td>{{ @$list->client_phone == "" ? config('constants.empty') : @$list->client_phone }}</td> 	
										<td>
											<div style="max-width: 300px;">
												<strong>{{ @$list->subject }}</strong>
												@if(@$list->description)
													<div class="text-muted small mt-1">
														{!! \Illuminate\Support\Str::limit(strip_tags(@$list->description), 60, '...') !!}
													</div>
												@endif
											</div>
										</td>
										<td>
											@if(@$list->activity_date)
												<div>{{ \Carbon\Carbon::parse(@$list->activity_date)->format('d/m/Y') }}</div>
												<small class="text-muted">{{ \Carbon\Carbon::parse(@$list->activity_date)->format('h:i A') }}</small>
												<div class="text-muted small">{{ \Carbon\Carbon::parse(@$list->activity_date)->diffForHumans() }}</div>
											@else
												{{ config('constants.empty') }}
											@endif
										</td>
										<td>
											@if(@$list->admin_firstname || @$list->admin_lastname)
												{{ @$list->admin_firstname }} {{ @$list->admin_lastname }}
											@else
												<span class="text-muted">{{ config('constants.empty') }}</span>
											@endif
										</td>
										<td>
											@if(@$list->client_id)
												<a href="{{ route('clients.detail', @$list->client_id) }}" class="btn btn-sm btn-primary" title="View Client">
													<i class="far fa-eye"></i> View
												</a>
											@else
												<span class="text-muted">N/A</span>
											@endif
										</td>
									</tr>
									<!-- Expandable row for client details -->
									@if(@$list->client_id)
									<tr id="client-details-{{ @$list->client_id }}" class="client-details-row" style="display: none;">
										<td colspan="7" style="background-color: #f8f9fa; padding: 20px;">
											<div class="client-details-content" id="client-details-content-{{ @$list->client_id }}">
												<div class="text-center">
													<i class="fas fa-spinner fa-spin"></i> Loading...
												</div>
											</div>
										</td>
									</tr>
									@endif
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
						@if(@$totalData !== 0)
							<div class="card-footer">
								{{ @$lists->links() }}
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

@push('styles')
<style>
	.client-name-toggle {
		display: inline-flex;
		align-items: center;
		transition: color 0.2s;
	}
	.client-name-toggle:hover {
		color: #007bff !important;
	}
	.client-name-toggle .toggle-icon {
		font-size: 0.8em;
	}
	.client-details-row td {
		border-top: 2px solid #dee2e6;
	}
	.client-details-content h6 {
		color: #495057;
		margin-bottom: 10px;
		font-weight: 600;
	}
	.client-details-content .card {
		border: 1px solid #e9ecef;
		border-radius: 5px;
	}
	.btn-archive-client {
		/* Small button - no min-width needed */
	}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
	// Wait for flatpickr to be available (it's loaded via Vite)
	function initDatePickers() {
		if (typeof flatpickr !== 'undefined' && $(".filterdatepicker").length) {
			$(".filterdatepicker").each(function() {
				flatpickr(this, {
					dateFormat: "Y-m-d",
					allowInput: true
				});
			});
		} else if ($(".filterdatepicker").length) {
			// Retry after a short delay if flatpickr isn't loaded yet
			setTimeout(initDatePickers, 100);
		}
	}
	
	// Initialize date pickers
	initDatePickers();
	
	// Make Activity Date column header clickable for sorting
	$('.sortable-header').on('click', function() {
		var currentSort = '{{ @$sortOrder }}' || 'desc';
		var newSort = currentSort === 'desc' ? 'asc' : 'desc';
		
		// Update the sort order in the form and submit
		$('#sort_order').val(newSort);
		$('#filterForm').submit();
	});
	
	// Handle client name click to expand/collapse details
	$('.client-name-toggle').on('click', function(e) {
		e.preventDefault();
		var $toggle = $(this);
		var clientId = $toggle.data('client-id');
		var activityId = $toggle.data('activity-id');
		var $detailsRow = $('#client-details-' + clientId);
		var $icon = $toggle.find('.toggle-icon');
		var $detailsContent = $('#client-details-content-' + clientId);
		
		// Toggle the row visibility
		if ($detailsRow.is(':visible')) {
			// Collapse
			$detailsRow.slideUp(300);
			$icon.css('transform', 'rotate(0deg)');
		} else {
			// Expand
			$detailsRow.slideDown(300);
			$icon.css('transform', 'rotate(90deg)');
			
			// Load details if not already loaded
			if ($detailsContent.find('.client-details-loaded').length === 0) {
				loadClientDetails(clientId, $detailsContent);
			}
		}
	});
	
	// Helper function to strip HTML tags and get plain text
	function stripHtml(html) {
		if (!html) return '';
		var tmp = document.createElement('DIV');
		tmp.innerHTML = html;
		return tmp.textContent || tmp.innerText || '';
	}
	
	// Function to load client details via AJAX
	function loadClientDetails(clientId, $container) {
		$container.html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
		
		var detailsUrl = '{{ route("adminconsole.recentclients.getdetails") }}';
		
		$.ajax({
			url: detailsUrl,
			type: 'POST',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {
				client_id: clientId
			},
			success: function(response) {
				if (response.success) {
					var data = response.data;
					var html = '<div class="client-details-loaded">';
					html += '<div class="row">';
					
					// Last Activity Section
					html += '<div class="col-md-6 mb-3">';
					html += '<h6><i class="fas fa-history"></i> Last Activity</h6>';
					if (data.last_activity) {
						html += '<div class="card p-3" style="background: white;">';
						html += '<strong>' + (data.last_activity.subject || 'N/A') + '</strong>';
						if (data.last_activity.description) {
							// Show full description without truncation, strip HTML tags for clean display
							var descriptionText = stripHtml(data.last_activity.description);
							html += '<div class="mb-2 text-muted small" style="white-space: pre-wrap; word-wrap: break-word; max-height: 300px; overflow-y: auto;">' + 
								descriptionText + 
								'</div>';
						}
						html += '<div class="text-muted small">';
						html += '<i class="far fa-calendar"></i> ' + data.last_activity.date;
						html += ' | <i class="far fa-user"></i> ' + data.last_activity.created_by;
						html += '</div>';
						html += '</div>';
					} else {
						html += '<p class="text-muted">No activity found</p>';
					}
					html += '</div>';
					
					// Document Count Section
					html += '<div class="col-md-6 mb-3">';
					html += '<h6><i class="fas fa-file"></i> Documents</h6>';
					html += '<div class="card p-3" style="background: white;">';
					html += '<div class="d-flex align-items-center">';
					html += '<span class="badge badge-primary" style="font-size: 1.2em; padding: 8px 12px;">' + data.document_count + '</span>';
					html += '<span class="ml-2">' + (data.document_count === 1 ? 'file' : 'files') + ' found</span>';
					html += '</div>';
					html += '</div>';
					html += '</div>';
					
					// Archive Button Section
					html += '<div class="col-md-12 mt-3">';
					html += '<div class="card p-3" style="background: white;">';
					html += '<h6><i class="fas fa-archive"></i> Actions</h6>';
					if (data.is_archived) {
						html += '<button type="button" class="btn btn-sm btn-warning btn-archive-client" data-client-id="' + clientId + '" data-action="unarchive">';
						html += '<i class="fas fa-undo"></i> Unarchive Client';
						html += '</button>';
						html += '<span class="ml-2 text-muted"><i class="fas fa-info-circle"></i> This client is currently archived</span>';
					} else {
						html += '<button type="button" class="btn btn-sm btn-danger btn-archive-client" data-client-id="' + clientId + '" data-action="archive">';
						html += '<i class="fas fa-archive"></i> Archive Client';
						html += '</button>';
						html += '<span class="ml-2 text-muted"><i class="fas fa-info-circle"></i> Archive this client to move it to archived clients</span>';
					}
					html += '</div>';
					html += '</div>';
					
					html += '</div>'; // End row
					html += '</div>'; // End client-details-loaded
					
					$container.html(html);
				} else {
					$container.html('<div class="alert alert-danger">Error: ' + (response.message || 'Failed to load client details') + '</div>');
				}
			},
			error: function(xhr) {
				var errorMsg = 'Failed to load client details';
				if (xhr.responseJSON && xhr.responseJSON.message) {
					errorMsg = xhr.responseJSON.message;
				}
				$container.html('<div class="alert alert-danger">' + errorMsg + '</div>');
			}
		});
	}
	
	// Handle archive button click (placeholder - will be implemented in next step)
	$(document).on('click', '.btn-archive-client', function() {
		var clientId = $(this).data('client-id');
		var action = $(this).data('action');
		var actionText = action === 'archive' ? 'archive' : 'unarchive';
		
		if (confirm('Are you sure you want to ' + actionText + ' this client?')) {
			// This will be implemented in the next step
			alert('Archive functionality will be implemented in the next step. Client ID: ' + clientId);
		}
	});
});
</script>
@endpush
 
@endsection
