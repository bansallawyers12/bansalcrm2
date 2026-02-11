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
								@if(isset($totalData) && $totalData !== null)
									<span class="badge badge-primary">Total: {{ $totalData }}</span>
								@else
									<span class="badge badge-secondary" title="Total not shown for faster loading">Total: —</span>
								@endif
							</div>
						</div>
						<div class="card-body">
							<!-- Filter Panel -->
							<div class="filter-panel mb-4" style="background: #f7f7f7; padding: 20px; border: 1px solid #eee; border-radius: 5px;">
								<form method="GET" action="{{ route('adminconsole.recentclients.index') }}" id="filterForm">
									<!-- Search Box -->
									<div class="row mb-3">
										<div class="col-md-12">
											<div class="form-group">
												<label for="search"><strong><i class="fas fa-search"></i> Search Clients</strong></label>
												<input type="text" 
													   name="search" 
													   id="search" 
													   class="form-control" 
													   value="{{ @$search }}" 
													   placeholder="Search by name, email, phone, or client ID (e.g. TEST105453)...">
											</div>
										</div>
									</div>
									
									<!-- Date Filters and Quick Buttons -->
									<div class="row mb-3">
										<div class="col-md-3">
											<div class="form-group">
												<label for="from_date"><strong><i class="fas fa-calendar-alt"></i> From Date</strong></label>
												<input type="text" 
													   name="from_date" 
													   id="from_date" 
													   class="form-control filterdatepicker" 
													   style="font-size: 14px; padding: 10px; border: 2px solid #007bff;"
													   value="{{ @$fromDate }}" 
													   placeholder="Select start date">
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label for="to_date"><strong><i class="fas fa-calendar-alt"></i> To Date</strong></label>
												<input type="text" 
													   name="to_date" 
													   id="to_date" 
													   class="form-control filterdatepicker" 
													   style="font-size: 14px; padding: 10px; border: 2px solid #007bff;"
													   value="{{ @$toDate }}" 
													   placeholder="Select end date">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label><strong><i class="fas fa-clock"></i> Quick Filters</strong></label>
												<div class="btn-group" role="group">
													<button type="button" class="btn btn-outline-primary quick-filter-btn" data-days="0">Today</button>
													<button type="button" class="btn btn-outline-primary quick-filter-btn" data-days="7">This Week</button>
													<button type="button" class="btn btn-outline-primary quick-filter-btn" data-days="30">This Month</button>
												</div>
											</div>
										</div>
									</div>
									
									<!-- Document count, Document storage, Phone, Email filters -->
									<div class="row mb-3">
										<div class="col-md-3">
											<div class="form-group">
												<label for="document_count"><strong><i class="fas fa-file"></i> Documents</strong></label>
												<select name="document_count" id="document_count" class="form-control">
													<option value="">All</option>
													<option value="0" {{ (string)@$documentCount === '0' ? 'selected' : '' }}>0</option>
													<option value="1" {{ (string)@$documentCount === '1' ? 'selected' : '' }}>1</option>
													<option value="2" {{ (string)@$documentCount === '2' ? 'selected' : '' }}>2</option>
													<option value="3" {{ (string)@$documentCount === '3' ? 'selected' : '' }}>3</option>
													<option value="4" {{ (string)@$documentCount === '4' ? 'selected' : '' }}>4</option>
													<option value="5" {{ (string)@$documentCount === '5' ? 'selected' : '' }}>5</option>
													<option value="6" {{ (string)@$documentCount === '6' ? 'selected' : '' }}>6</option>
													<option value="7" {{ (string)@$documentCount === '7' ? 'selected' : '' }}>7</option>
													<option value="8" {{ (string)@$documentCount === '8' ? 'selected' : '' }}>8</option>
													<option value="9" {{ (string)@$documentCount === '9' ? 'selected' : '' }}>9</option>
													<option value="10+" {{ (string)@$documentCount === '10+' ? 'selected' : '' }}>10+</option>
												</select>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label for="doc_storage"><strong><i class="fas fa-cloud"></i> Doc Storage</strong></label>
												<select name="doc_storage" id="doc_storage" class="form-control">
													<option value="">All</option>
													<option value="local" {{ @$docStorage === 'local' ? 'selected' : '' }}>Local only</option>
													<option value="aws" {{ @$docStorage === 'aws' ? 'selected' : '' }}>AWS only</option>
													<option value="both" {{ @$docStorage === 'both' ? 'selected' : '' }}>Both</option>
													<option value="none" {{ @$docStorage === 'none' ? 'selected' : '' }}>No documents</option>
												</select>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label for="no_phone"><strong><i class="fas fa-phone"></i> Phone</strong></label>
												<select name="no_phone" id="no_phone" class="form-control">
													<option value="">All</option>
													<option value="1" {{ @$noPhone === '1' ? 'selected' : '' }}>No phone number</option>
												</select>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label for="no_email"><strong><i class="fas fa-envelope"></i> Email</strong></label>
												<select name="no_email" id="no_email" class="form-control">
													<option value="">All</option>
													<option value="1" {{ @$noEmail === '1' ? 'selected' : '' }}>No email address</option>
												</select>
											</div>
										</div>
									</div>
									
									<!-- Applications, Last Activity, Activity Type and Sort Options -->
									<div class="row mb-3">
										<div class="col-md-2">
											<div class="form-group">
												<label for="has_applications"><strong><i class="fas fa-file-alt"></i> Applications</strong></label>
												<select name="has_applications" id="has_applications" class="form-control">
													<option value="">All Clients</option>
													<option value="0" {{ @$hasApplications === '0' ? 'selected' : '' }}>No applications created</option>
												</select>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label for="last_activity_years"><strong><i class="fas fa-clock"></i> Last Activity</strong></label>
												<select name="last_activity_years" id="last_activity_years" class="form-control">
													<option value="">All</option>
													<option value="1" {{ @$lastActivityYears === '1' || @$lastActivityYears === 1 ? 'selected' : '' }}>1+ years ago</option>
													<option value="2" {{ @$lastActivityYears === '2' || @$lastActivityYears === 2 ? 'selected' : '' }}>2+ years ago</option>
													<option value="3" {{ @$lastActivityYears === '3' || @$lastActivityYears === 3 ? 'selected' : '' }}>3+ years ago</option>
													<option value="4" {{ @$lastActivityYears === '4' || @$lastActivityYears === 4 ? 'selected' : '' }}>4+ years ago</option>
													<option value="5" {{ @$lastActivityYears === '5' || @$lastActivityYears === 5 ? 'selected' : '' }}>5+ years ago</option>
												</select>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label for="activity_type"><strong><i class="fas fa-filter"></i> Activity Type</strong></label>
												<select name="activity_type" id="activity_type" class="form-control">
													<option value="">All Activities</option>
													<option value="note" {{ @$activityType == 'note' ? 'selected' : '' }}>Notes</option>
													<option value="call" {{ @$activityType == 'call' ? 'selected' : '' }}>Calls</option>
													<option value="email" {{ @$activityType == 'email' ? 'selected' : '' }}>Emails</option>
													<option value="meeting" {{ @$activityType == 'meeting' ? 'selected' : '' }}>Meetings</option>
													<option value="task" {{ @$activityType == 'task' ? 'selected' : '' }}>Tasks</option>
												</select>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label for="sort_order"><strong><i class="fas fa-sort"></i> Sort Order</strong></label>
												<select name="sort_order" id="sort_order" class="form-control">
													<option value="desc" {{ @$sortOrder == 'desc' ? 'selected' : '' }}>Newest First</option>
													<option value="asc" {{ @$sortOrder == 'asc' ? 'selected' : '' }}>Oldest First</option>
												</select>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label for="per_page"><strong><i class="fas fa-list"></i> Per Page</strong></label>
												<select name="per_page" id="per_page" class="form-control">
													<option value="10" {{ @$perPage == 10 ? 'selected' : '' }}>10</option>
													<option value="25" {{ @$perPage == 25 ? 'selected' : '' }}>25</option>
													<option value="50" {{ @$perPage == 50 ? 'selected' : '' }}>50</option>
													<option value="100" {{ @$perPage == 100 ? 'selected' : '' }}>100</option>
												</select>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label>&nbsp;</label>
												<div>
													<button type="submit" class="btn btn-primary">
														<i class="fas fa-filter"></i> Apply Filters
													</button>
													<button type="button" class="btn btn-info" id="refreshBtn" title="Refresh Data">
														<i class="fas fa-sync-alt"></i> Refresh
													</button>
													<a href="{{ route('adminconsole.recentclients.index') }}" class="btn btn-secondary">
														<i class="fas fa-times"></i> Clear
													</a>
												</div>
											</div>
										</div>
									</div>
									
									<!-- Hidden field for sort column -->
									<input type="hidden" name="sort_column" id="sort_column" value="{{ @$sortColumn }}">
								</form>
							</div>
							@if($lists->count() > 0)
							<div class="mb-3 d-flex align-items-center flex-wrap">
								<button type="button" class="btn btn-danger mr-2" id="bulkArchiveBtn" disabled title="Select one or more clients to archive">
									<i class="fas fa-archive"></i> Bulk Archive
								</button>
								<span class="text-muted small" id="selectedCountText">0 selected</span>
							</div>
							@endif
							<div class="table-responsive common_table"> 
								<table class="table text_wrap table-striped recent-clients-table">
								<thead>
									<tr>
										<th style="width: 40px;">
											<label class="mb-0 d-flex align-items-center">
												<input type="checkbox" id="selectAllClients" class="client-select-all" title="Select all on this page">
												<span class="ml-1 small">All</span>
											</label>
										</th>
										<th class="sortable-header" data-sort-column="client_name" style="cursor: pointer;">
											Client Name
											@if(@$sortColumn == 'client_name')
												@if(@$sortOrder == 'desc')
													<i class="fas fa-sort-down ml-1"></i>
												@else
													<i class="fas fa-sort-up ml-1"></i>
												@endif
											@else
												<i class="fas fa-sort ml-1 text-muted"></i>
											@endif
										</th>
										<th class="sortable-header" data-sort-column="client_email" style="cursor: pointer;">
											Email
											@if(@$sortColumn == 'client_email')
												@if(@$sortOrder == 'desc')
													<i class="fas fa-sort-down ml-1"></i>
												@else
													<i class="fas fa-sort-up ml-1"></i>
												@endif
											@else
												<i class="fas fa-sort ml-1 text-muted"></i>
											@endif
										</th>
										<th class="sortable-header" data-sort-column="client_phone" style="cursor: pointer;">
											Phone
											@if(@$sortColumn == 'client_phone')
												@if(@$sortOrder == 'desc')
													<i class="fas fa-sort-down ml-1"></i>
												@else
													<i class="fas fa-sort-up ml-1"></i>
												@endif
											@else
												<i class="fas fa-sort ml-1 text-muted"></i>
											@endif
										</th>
										<th>Last Activity</th>
										<th class="sortable-header" data-sort-column="activity_date" style="cursor: pointer;">
											Activity Date
											@if(@$sortColumn == 'activity_date')
												@if(@$sortOrder == 'desc')
													<i class="fas fa-sort-down ml-1"></i>
												@else
													<i class="fas fa-sort-up ml-1"></i>
												@endif
											@else
												<i class="fas fa-sort ml-1 text-muted"></i>
											@endif
										</th>
										<th class="sortable-header" data-sort-column="modified_by" style="cursor: pointer;">
											Modified By
											@if(@$sortColumn == 'modified_by')
												@if(@$sortOrder == 'desc')
													<i class="fas fa-sort-down ml-1"></i>
												@else
													<i class="fas fa-sort-up ml-1"></i>
												@endif
											@else
												<i class="fas fa-sort ml-1 text-muted"></i>
											@endif
										</th>
										<th>Storage</th>
										<th>Action</th>
									</tr>
								</thead>
								@if($lists->count() > 0)
								<tbody class="tdata">	
								@foreach (@$lists as $list)
									<tr id="id_{{@$list->activity_id}}" class="client-row">
										<td>
											@if(@$list->client_id)
												<input type="checkbox" class="client-checkbox" name="client_ids[]" value="{{ @$list->client_id }}" data-client-id="{{ @$list->client_id }}">
											@else
												<span class="text-muted">—</span>
											@endif
										</td>
										<td class="cell-wrap">
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
												@if(!empty(@$list->client_unique_id))
													<div class="mt-1"><a href="{{ URL::to('/clients/detail/'.base64_encode(convert_uuencode(@$list->client_id))) }}" target="_blank" rel="noopener noreferrer" class="client-unique-id-link" title="Open client in new tab">{{ @$list->client_unique_id }}</a></div>
												@endif
											@else
												@if(@$list->client_firstname || @$list->client_lastname)
													{{ @$list->client_firstname }} {{ @$list->client_lastname }}
												@else
													<span class="text-muted">{{ config('constants.empty') }}</span>
												@endif
												@if(!empty(@$list->client_unique_id))
													<div class="mt-1"><a href="{{ URL::to('/clients/detail/'.base64_encode(convert_uuencode(@$list->client_id))) }}" target="_blank" rel="noopener noreferrer" class="client-unique-id-link" title="Open client in new tab">{{ @$list->client_unique_id }}</a></div>
												@endif
											@endif
										</td>
										<td>{{ empty(@$list->client_email) ? config('constants.empty') : \Illuminate\Support\Str::limit(@$list->client_email, 40, '...') }}</td> 	
										<td>{{ @$list->client_phone == "" ? config('constants.empty') : @$list->client_phone }}</td> 	
										<td class="cell-wrap cell-wrap-activity">
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
											@php $ds = @$list->doc_storage ?? 'none'; @endphp
											@if($ds === 'both')
												<span class="badge badge-info" title="Documents in local and AWS"><i class="fas fa-folder"></i> <i class="fas fa-cloud"></i> Both</span>
											@elseif($ds === 'local')
												<span class="badge badge-secondary" title="Documents in local/public folder"><i class="fas fa-folder"></i> Local</span>
											@elseif($ds === 'aws')
												<span class="badge badge-primary" title="Documents in AWS S3"><i class="fas fa-cloud"></i> AWS</span>
											@else
												<span class="text-muted">—</span>
											@endif
										</td>
										<td>
											@if(@$list->client_id)
												<a href="{{ URL::to('/clients/detail/'.base64_encode(convert_uuencode(@$list->client_id))) }}" class="btn btn-sm btn-primary" title="View Client">
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
										<td colspan="9" style="background-color: #f8f9fa; padding: 20px;">
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
										<td style="text-align:center;" colspan="9">
											No records found
										</td>
									</tr>
								</tbody>
								@endif
							</table> 
						</div>
						@if($lists->count() > 0)
							<div class="card-footer">
								{{ $lists->links() }}
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<!-- Modal: Documents by category (Application / Education / Migration) -->
<div class="modal fade" id="clientDocumentsModal" tabindex="-1" role="dialog" aria-labelledby="clientDocumentsModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientDocumentsModalLabel"><i class="fas fa-file"></i> <span id="clientDocumentsModalTitle">Documents</span></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="clientDocumentsModalBody">
					<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

@push('styles')
<style>
	/* Allow long text to wrap in Client Name and Last Activity columns (no overlap) */
	table.recent-clients-table td.cell-wrap {
		white-space: normal;
		word-break: break-word;
		overflow-wrap: break-word;
		min-width: 0;
	}
	table.recent-clients-table td.cell-wrap-activity {
		max-width: 320px;
	}
	.client-unique-id-link {
		color: #007bff;
		text-decoration: none;
		font-size: 0.9em;
	}
	.client-unique-id-link:hover {
		color: #0056b3;
		text-decoration: underline;
	}
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
	
	/* Enhanced date picker styling */
	.filterdatepicker {
		font-weight: 500;
		transition: all 0.3s ease;
	}
	.filterdatepicker:focus {
		border-color: #0056b3 !important;
		box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
	}
	
	/* Quick filter buttons */
	.quick-filter-btn {
		margin-right: 5px;
		margin-bottom: 5px;
	}
	.quick-filter-btn:hover {
		transform: translateY(-1px);
		box-shadow: 0 2px 4px rgba(0,0,0,0.1);
	}
	
	/* Sortable header styling */
	.sortable-header {
		user-select: none;
		position: relative;
	}
	.sortable-header:hover {
		background-color: #f8f9fa;
	}
	.sortable-header i {
		opacity: 0.5;
		transition: opacity 0.2s;
	}
	.sortable-header:hover i {
		opacity: 1;
	}
	
	/* Search box styling */
	#search {
		font-size: 15px;
		padding: 12px;
	}
	#search:focus {
		border-color: #007bff;
		box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
	}
	
	/* Refresh button animation */
	#refreshBtn i.fa-spin {
		animation: fa-spin 1s infinite linear;
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
	
	// Quick filter buttons (Today, This Week, This Month)
	$('.quick-filter-btn').on('click', function() {
		var days = $(this).data('days');
		var today = new Date();
		var fromDate = new Date();
		
		if (days === 0) {
			// Today
			fromDate.setHours(0, 0, 0, 0);
		} else {
			// This Week or This Month
			fromDate.setDate(today.getDate() - days);
			fromDate.setHours(0, 0, 0, 0);
		}
		
		var toDate = new Date(today);
		toDate.setHours(23, 59, 59, 999);
		
		// Format dates as YYYY-MM-DD
		var formatDate = function(date) {
			var year = date.getFullYear();
			var month = String(date.getMonth() + 1).padStart(2, '0');
			var day = String(date.getDate()).padStart(2, '0');
			return year + '-' + month + '-' + day;
		};
		
		$('#from_date').val(formatDate(fromDate));
		$('#to_date').val(formatDate(toDate));
		
		// Update flatpickr if it's initialized
		if (typeof flatpickr !== 'undefined') {
			var fromPicker = $('#from_date')[0]._flatpickr;
			var toPicker = $('#to_date')[0]._flatpickr;
			if (fromPicker) {
				fromPicker.setDate(fromDate, false);
			}
			if (toPicker) {
				toPicker.setDate(toDate, false);
			}
		}
	});
	
	// Column header sorting
	$('.sortable-header').on('click', function() {
		var sortColumn = $(this).data('sort-column');
		var currentSortColumn = $('#sort_column').val();
		var currentSort = $('#sort_order').val() || 'desc';
		
		// If clicking the same column, toggle sort order; otherwise, set to desc
		if (sortColumn === currentSortColumn) {
			var newSort = currentSort === 'desc' ? 'asc' : 'desc';
			$('#sort_order').val(newSort);
		} else {
			$('#sort_column').val(sortColumn);
			$('#sort_order').val('desc');
		}
		
		$('#filterForm').submit();
	});
	
	// Refresh button
	$('#refreshBtn').on('click', function() {
		var $btn = $(this);
		var $icon = $btn.find('i');
		
		// Add spinning animation
		$icon.addClass('fa-spin');
		$btn.prop('disabled', true);
		
		// Reload the page with current filters
		window.location.reload();
	});
	
	// Auto-submit on per_page change
	$('#per_page').on('change', function() {
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
					
					// Document Count & Storage Section
					html += '<div class="col-md-6 mb-3">';
					html += '<h6><i class="fas fa-file"></i> Documents</h6>';
					html += '<div class="card p-3" style="background: white;">';
					html += '<div class="d-flex align-items-center">';
					html += '<span class="badge badge-primary" style="font-size: 1.2em; padding: 8px 12px;">' + data.document_count + '</span>';
					html += '<span class="ml-2">' + (data.document_count === 1 ? 'file' : 'files') + ' found</span>';
					html += '</div>';
					var storageLabel = (data.document_storage === 'both') ? 'Local & AWS' : ((data.document_storage === 'local') ? 'Local only' : ((data.document_storage === 'aws') ? 'AWS only' : '—'));
					if (data.document_count > 0 && data.document_storage) {
						html += '<div class="mt-2"><small class="text-muted">Storage: </small><strong>' + storageLabel + '</strong></div>';
					}
					// Category doc counts (local/public folder only) - clickable to show documents in popup
					html += '<div class="mt-3 pt-2 border-top">';
					html += '<small class="text-muted d-block mb-1">In public folder (not S3):</small>';
					html += '<div class="d-flex flex-wrap gap-2">';
					html += '<span class="badge badge-secondary doc-category-badge" data-client-id="' + clientId + '" data-category="application" data-count="' + (data.application_doc_count_local != null ? data.application_doc_count_local : 0) + '" style="cursor: pointer;" title="Click to view documents">Application: ' + (data.application_doc_count_local != null ? data.application_doc_count_local : 0) + '</span>';
					html += '<span class="badge badge-secondary doc-category-badge" data-client-id="' + clientId + '" data-category="education" data-count="' + (data.education_doc_count_local != null ? data.education_doc_count_local : 0) + '" style="cursor: pointer;" title="Click to view documents">Education: ' + (data.education_doc_count_local != null ? data.education_doc_count_local : 0) + '</span>';
					html += '<span class="badge badge-secondary doc-category-badge" data-client-id="' + clientId + '" data-category="migration" data-count="' + (data.migration_doc_count_local != null ? data.migration_doc_count_local : 0) + '" style="cursor: pointer;" title="Click to view documents">Migration: ' + (data.migration_doc_count_local != null ? data.migration_doc_count_local : 0) + '</span>';
					html += '</div>';
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
	
	// Click on Application / Education / Migration badge: show documents in modal
	$(document).on('click', '.doc-category-badge', function() {
		var clientId = $(this).data('client-id');
		var category = $(this).data('category');
		var count = $(this).data('count');
		if (!clientId || !category) return;
		var categoryLabel = category.charAt(0).toUpperCase() + category.slice(1);
		$('#clientDocumentsModalTitle').text(categoryLabel + ' documents');
		$('#clientDocumentsModalBody').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
		$('#clientDocumentsModal').modal('show');
		$.ajax({
			url: '{{ route("adminconsole.recentclients.documentsbycategory") }}',
			type: 'POST',
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
			data: { client_id: clientId, category: category },
			success: function(response) {
				if (response.success) {
					var docs = response.documents || [];
					var label = response.category_label || categoryLabel;
					if (docs.length === 0) {
						$('#clientDocumentsModalBody').html('<p class="text-muted mb-0">No documents in public folder for this category.</p>');
					} else {
						var html = '<p class="text-muted small mb-2">' + docs.length + ' document(s) in public folder (not S3)</p>';
						html += '<ul class="list-group list-group-flush">';
						for (var i = 0; i < docs.length; i++) {
							var d = docs[i];
							html += '<li class="list-group-item d-flex justify-content-between align-items-center">';
							html += '<span class="text-break">' + (d.file_name || 'Document #' + d.id) + '</span>';
							html += '<span class="d-flex align-items-center">';
							if (d.created_at) html += '<small class="text-muted mr-2">' + d.created_at + '</small>';
							if (d.preview_url) {
								html += '<a href="' + d.preview_url + '" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary"><i class="fas fa-external-link-alt"></i> View</a>';
							}
							html += '</span></li>';
						}
						html += '</ul>';
						$('#clientDocumentsModalBody').html(html);
					}
				} else {
					$('#clientDocumentsModalBody').html('<div class="alert alert-danger">' + (response.message || 'Failed to load documents') + '</div>');
				}
			},
			error: function(xhr) {
				var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to load documents';
				$('#clientDocumentsModalBody').html('<div class="alert alert-danger">' + msg + '</div>');
			}
		});
	});
	
	// Bulk selection: select all on page
	$('#selectAllClients').on('change', function() {
		var checked = $(this).prop('checked');
		$('.client-checkbox').prop('checked', checked);
		updateBulkArchiveState();
	});
	
	$('.client-checkbox').on('change', function() {
		updateBulkArchiveState();
	});
	
	function updateBulkArchiveState() {
		var count = $('.client-checkbox:checked').length;
		$('#selectedCountText').text(count + ' selected');
		$('#bulkArchiveBtn').prop('disabled', count === 0);
		$('#selectAllClients').prop('checked', count > 0 && count === $('.client-checkbox').length);
	}
	
	// Bulk archive
	$('#bulkArchiveBtn').on('click', function() {
		var ids = [];
		$('.client-checkbox:checked').each(function() {
			var id = $(this).val();
			if (id) ids.push(id);
		});
		// DEBUG: Log collected IDs
		console.log('[BulkArchive] Checkboxes checked:', $('.client-checkbox:checked').length);
		console.log('[BulkArchive] IDs collected:', ids);
		console.log('[BulkArchive] IDs type:', typeof ids, 'isArray:', Array.isArray(ids));
		
		if (ids.length === 0) {
			alert('Please select at least one client to archive.');
			return;
		}
		if (!confirm('Are you sure you want to archive ' + ids.length + ' client(s)? They will be moved to archived clients.')) {
			return;
		}
		var $btn = $('#bulkArchiveBtn');
		$btn.prop('disabled', true);
		var originalHtml = $btn.html();
		$btn.html('<i class="fas fa-spinner fa-spin"></i> Archiving...');
		
		var postData = { client_ids: ids };
		console.log('[BulkArchive] Sending payload:', JSON.stringify(postData));
		
		$.ajax({
			url: '{{ route("adminconsole.recentclients.bulkarchive") }}',
			type: 'POST',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: postData,
			success: function(response) {
				console.log('[BulkArchive] Response:', response);
				if (response.debug) {
					console.log('[BulkArchive] Debug info:', response.debug);
				}
				if (response.success) {
					var msg = response.message;
					if (response.debug) {
						msg += '\n\n[DEBUG] ' + JSON.stringify(response.debug, null, 2);
					}
					alert(msg);
					window.location.reload();
				} else {
					alert('Error: ' + (response.message || 'Failed to archive clients'));
					$btn.prop('disabled', false);
					$btn.html(originalHtml);
				}
			},
			error: function(xhr) {
				console.log('[BulkArchive] Error:', xhr.status, xhr.responseText);
				var errorMsg = 'Failed to archive clients';
				if (xhr.responseJSON && xhr.responseJSON.message) {
					errorMsg = xhr.responseJSON.message;
				}
				alert('Error: ' + errorMsg);
				$btn.prop('disabled', false);
				$btn.html(originalHtml);
			}
		});
	});
	
	// Handle archive button click
	$(document).on('click', '.btn-archive-client', function() {
		var $btn = $(this);
		var clientId = $btn.data('client-id');
		var action = $btn.data('action');
		var actionText = action === 'archive' ? 'archive' : 'unarchive';
		var $detailsContent = $('#client-details-content-' + clientId);
		
		if (confirm('Are you sure you want to ' + actionText + ' this client?')) {
			// Disable button and show loading
			$btn.prop('disabled', true);
			var originalHtml = $btn.html();
			$btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
			
			$.ajax({
				url: '{{ route("adminconsole.recentclients.togglearchive") }}',
				type: 'POST',
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: {
					client_id: clientId,
					action: action
				},
				success: function(response) {
					if (response.success) {
						// Show success message
						alert(response.message);
						
						// Reload the client details to reflect the new archive status
						if ($detailsContent.length) {
							loadClientDetails(clientId, $detailsContent);
						}
						
						// Optionally reload the page to refresh the list
						// window.location.reload();
					} else {
						alert('Error: ' + (response.message || 'Failed to ' + actionText + ' client'));
						$btn.prop('disabled', false);
						$btn.html(originalHtml);
					}
				},
				error: function(xhr) {
					var errorMsg = 'Failed to ' + actionText + ' client';
					if (xhr.responseJSON && xhr.responseJSON.message) {
						errorMsg = xhr.responseJSON.message;
					}
					alert('Error: ' + errorMsg);
					$btn.prop('disabled', false);
					$btn.html(originalHtml);
				}
			});
		}
	});
});
</script>
@endpush
 
@endsection
