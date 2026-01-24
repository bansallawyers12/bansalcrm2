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
							<div class="table-responsive common_table"> 
								<table class="table text_wrap table-striped">
								<thead>
									<tr>
										<th>Client Name</th>
										<th>Email</th>
										<th>Phone</th>
										<th>Last Activity</th>
										<th>Activity Date</th>
										<th>Modified By</th>
										<th>Action</th>
									</tr> 
								</thead>
								@if(@$totalData !== 0)
								<tbody class="tdata">	
								@foreach (@$lists as $list)
									<tr id="id_{{@$list->activity_id}}">
										<td>
											@if(@$list->firstname || @$list->lastname)
												{{ @$list->firstname }} {{ @$list->lastname }}
											@else
												<span class="text-muted">{{ config('constants.empty') }}</span>
											@endif
										</td> 	
										<td>{{ @$list->email == "" ? config('constants.empty') : str_limit(@$list->email, '40', '...') }}</td> 	
										<td>{{ @$list->phone == "" ? config('constants.empty') : @$list->phone }}</td> 	
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
 
@endsection
