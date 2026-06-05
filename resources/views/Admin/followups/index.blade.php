@extends('layouts.admin')
@section('title', 'Followups')

@section('content')
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-header">
							<h4 class="mb-0">Followup listing</h4>
						</div>
						<div class="card-body p-0">
							<div class="table-responsive">
								<table class="table table-striped table-hover table-md mb-0">
									<thead>
										<tr>
											<th class="text-center" style="width:4rem;">SNO</th>
											<th>Date &amp; time</th>
											<th>Client ref</th>
											<th>Client</th>
											<th>Consultant</th>
											<th>Status</th>
											<th class="text-end">Action</th>
										</tr>
									</thead>
									<tbody>
										@forelse($followups as $row)
											@php
												$client = $row->noteClient;
												$detailUrl = $client ? url('/clients/detail/'.base64_encode(convert_uuencode($client->id))) : '#';
												$consultant = $row->title
													? preg_replace(['/^Followup\s+[—\-]\s*/u', '/\s+Followups$/u'], ['', ''], $row->title)
													: '—';
												$statusLabel = \App\Http\Controllers\Admin\FollowupController::followupListingStatusLabel($row);
												$statusBadgeClass = \App\Http\Controllers\Admin\FollowupController::followupListingStatusBadgeClass($statusLabel);
											@endphp
											<tr>
												<td class="text-center text-muted">{{ $followups->firstItem() + $loop->index }}</td>
												<td>{{ $row->action_assign_date ? \Carbon\Carbon::parse($row->action_assign_date)->format('d/m/Y H:i') : '—' }}</td>
												<td>
													@if($client && filled($client->client_id ?? null))
														<a href="{{ $detailUrl }}" target="_blank" rel="noopener noreferrer">{{ $client->client_id }}</a>
													@else
														—
													@endif
												</td>
												<td>{{ $client ? trim($client->first_name.' '.$client->last_name) : '—' }}</td>
												<td>{{ $consultant }}</td>
												<td><span class="badge {{ $statusBadgeClass }}">{{ $statusLabel }}</span></td>
												<td class="text-end">
													@if($client)
														<a href="{{ route('followups.view', $row) }}" class="btn btn-sm btn-outline-primary">View</a>
													@else
														—
													@endif
												</td>
											</tr>
										@empty
											<tr>
												<td colspan="7" class="text-center text-muted py-4">No follow-ups found.</td>
											</tr>
										@endforelse
									</tbody>
								</table>
							</div>
						</div>
						@if($followups->hasPages())
							<div class="card-footer">{{ $followups->links() }}</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
