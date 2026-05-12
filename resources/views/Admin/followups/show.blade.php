@extends('layouts.admin')
@section('title', 'Follow-up detail')

@section('content')
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
							<h4 class="mb-0">Follow-up detail</h4>
							<a href="{{ route('followups.index') }}" class="btn btn-outline-secondary btn-sm">Back to listing</a>
						</div>
						<div class="card-body">
							<dl class="row mb-4">
								<dt class="col-sm-3">Date &amp; time</dt>
								<dd class="col-sm-9">{{ $assignPretty }}</dd>
								<dt class="col-sm-3">Client ref</dt>
								<dd class="col-sm-9">
									@if($client && ($client->client_id ?? null))
										<a href="{{ $clientDetailUrl }}" target="_blank" rel="noopener noreferrer">{{ $client->client_id }}</a>
									@else
										—
									@endif
								</dd>
								<dt class="col-sm-3">Client</dt>
								<dd class="col-sm-9">{{ $client ? trim($client->first_name.' '.$client->last_name) : '—' }}</dd>
								<dt class="col-sm-3">Consultant</dt>
								<dd class="col-sm-9">{{ $consultantDisplay }}</dd>
								<dt class="col-sm-3">Assigned to</dt>
								<dd class="col-sm-9">{{ $note->assigned_user ? trim($note->assigned_user->first_name.' '.$note->assigned_user->last_name) : '—' }}</dd>
								@if($followupOutcome)
									<dt class="col-sm-3">Outcome</dt>
									<dd class="col-sm-9"><span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $followupOutcome)) }}</span></dd>
								@endif
							</dl>
							<h6 class="text-muted text-uppercase small fw-bold mb-2">Scheduled content</h6>
							<div class="border rounded bg-light p-3 followup-note-html">
								{!! $descriptionHtml !!}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
