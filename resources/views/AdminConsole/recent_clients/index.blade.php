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
									<tr id="id_{{@$list->activity_id}}">
										<td>
											@if(@$list->client_firstname || @$list->client_lastname)
												{{ @$list->client_firstname }} {{ @$list->client_lastname }}
											@else
												<span class="text-muted">{{ config('constants.empty') }}</span>
											@endif
										</td> 	
										<td>{{ @$list->client_email == "" ? config('constants.empty') : str_limit(@$list->client_email, '40', '...') }}</td> 	
										<td>{{ @$list->client_phone == "" ? config('constants.empty') : @$list->client_phone }}</td> 	
										<td>
											<div style="max-width: 300px;">
												<strong>{{ @$list->subject }}</strong>
												@if(@$list->description)
													<div class="text-muted small mt-1">
														{!! str_limit(strip_tags(@$list->description), 60, '...') !!}
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
});
</script>
@endpush
 
@endsection
