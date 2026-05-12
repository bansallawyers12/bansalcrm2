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
						<div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
							<h4 class="mb-0">Followup listing</h4>
							<a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">Dashboard</a>
						</div>
						<div class="card-body p-0">
							<div class="table-responsive">
								<table class="table table-striped table-hover table-md mb-0">
									<thead>
										<tr>
											<th>Date &amp; time</th>
											<th>Client ref</th>
											<th>Client</th>
											<th>Consultant</th>
											<th>Assigned to</th>
											<th class="text-end">Action</th>
										</tr>
									</thead>
									<tbody>
										@forelse($followups as $row)
											@php
												$client = $row->noteClient;
												$detailUrl = $client ? url('/clients/detail/'.base64_encode(convert_uuencode($client->id))) : '#';
												$consultant = $row->title ? preg_replace('/^Followup\s+[—\-]\s*/u', '', $row->title) : '—';
											@endphp
											<tr>
												<td>{{ $row->action_assign_date ? \Carbon\Carbon::parse($row->action_assign_date)->format('d/m/Y H:i') : '—' }}</td>
												<td>{{ $client->client_id ?? '—' }}</td>
												<td>{{ $client ? trim($client->first_name.' '.$client->last_name) : '—' }}</td>
												<td>{{ $consultant }}</td>
												<td>{{ $row->assigned_user ? trim($row->assigned_user->first_name.' '.$row->assigned_user->last_name) : '—' }}</td>
												<td class="text-end">
													@if($client)
														<a href="{{ $detailUrl }}" class="btn btn-sm btn-primary" target="_blank" rel="noopener">Client</a>
													@else
														—
													@endif
												</td>
											</tr>
										@empty
											<tr>
												<td colspan="6" class="text-center text-muted py-4">No follow-ups found.</td>
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
