@extends('layouts.admin')
@section('title', 'Office Check-In Report')

@section('content')
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('Elements.flash-message')
			</div>
			<div class="custom-error-msg"></div>
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Office Check-In Report</h4>
						</div>
						<div class="card-body">
							<div class="table-responsive common_table">
								<table class="table text_wrap">
									<thead>
										<tr>
											<th>ID</th>
											<th>Date</th>
											<th>Contact</th>
											<th>Contact Type</th>
											<th>Visit Purpose</th>
											<th>Office</th>
											<th>Assignee</th>
											<th>Status</th>
										</tr>
									</thead>
									@if($lists->count() > 0)
									<tbody class="tdata">
										@foreach ($lists as $list)
										@php
											$client = $list->client;
											$assignee = $list->assignee;
											$office = $list->office;
											$statusLabel = match ((int) $list->status) {
												0 => 'Waiting',
												1 => 'Completed',
												2 => 'Attending',
												default => '-',
											};
										@endphp
										<tr id="id_{{ $list->id }}">
											<td>#{{ $list->id }}</td>
											<td>{{ $list->date ? date('d/m/Y', strtotime($list->date)) : ($list->created_at ? date('d/m/Y', strtotime($list->created_at)) : '-') }}</td>
											<td>
												@if($client)
													<a target="_blank" href="{{ URL::to('/clients/detail/'.base64_encode(convert_uuencode($client->id))) }}">{{ $client->first_name }} {{ $client->last_name }}</a>
												@else
													-
												@endif
											</td>
											<td>{{ $list->contact_type ?: '-' }}</td>
											<td>{{ $list->visit_purpose ?: '-' }}</td>
											<td>{{ $office?->office_name ?: '-' }}</td>
											<td>
												@if($assignee)
													{{ trim(($assignee->first_name ?? '').' '.($assignee->last_name ?? '')) ?: '-' }}
												@else
													-
												@endif
											</td>
											<td>{{ $statusLabel }}</td>
										</tr>
										@endforeach
									</tbody>
									@else
									<tbody>
										<tr>
											<td style="text-align:center;" colspan="8">No Record found</td>
										</tr>
									</tbody>
									@endif
								</table>
							</div>
						</div>
						@if($lists->hasPages())
						<div class="card-footer">
							{!! $lists->appends(request()->except('page'))->render() !!}
						</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
