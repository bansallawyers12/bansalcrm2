@extends(request()->routeIs('adminconsole.followups.*') ? 'layouts.adminconsole' : 'layouts.admin')
@section('title', 'Calendar Setting — Free consultation')

@section('content')
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="row justify-content-center">
				<div class="col-12">
					<div class="card">
						<div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
							<div>
								<h4 class="mb-0">Calendar timing — free consultation</h4>
								<p class="text-muted small mb-0 mt-1">One schedule per consultant (linked to <code>followup_calendar_settings</code>).</p>
							</div>
							<div class="d-flex flex-wrap gap-2">
								<a href="{{ route('adminconsole.producttype.index') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-columns me-1"></i> Admin Console</a>
								<a href="{{ route('followups.index') }}" class="btn btn-outline-secondary btn-sm">Followup listing</a>
							</div>
						</div>
						<div class="card-body">
							<div class="row g-3">
								@php
									$dayLabels = [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun'];
								@endphp
								@forelse($settings as $row)
									@php
										$c = $row->consultant;
										$start = \Carbon\Carbon::parse($row->start_time);
										$end = \Carbon\Carbon::parse($row->end_time);
										$days = $row->available_days ?? [];
										if ($days === []) {
											$daysLabel = 'All days';
										} else {
											$daysLabel = collect($days)->map(fn ($n) => $dayLabels[(int) $n] ?? $n)->implode(', ');
										}
									@endphp
									<div class="col-md-6 col-xl-3">
										<div class="card h-100 border shadow-sm">
											<div class="card-header py-2 d-flex justify-content-between align-items-center bg-light">
												<span class="fw-semibold small">{{ $c->name ?? 'Consultant' }}</span>
												<a href="{{ followups_console_route('calendar-settings.edit', $row) }}" class="btn btn-sm btn-outline-warning" title="Edit">
													<i class="fas fa-pencil-alt"></i>
												</a>
											</div>
											<div class="card-body small">
												<div class="mb-2">
													@if($row->is_active)
														<span class="badge bg-success"><span class="me-1">●</span>Active</span>
													@else
														<span class="badge bg-secondary">Inactive</span>
													@endif
												</div>
												<div class="mb-1"><strong>Time:</strong> {{ $start->format('g:i A') }} – {{ $end->format('g:i A') }}</div>
												<div class="mb-1"><strong>Slot:</strong> {{ $row->slot_duration_minutes }} min</div>
												<div class="mb-0 text-muted"><strong>Days:</strong> {{ $daysLabel }}</div>
												@if($row->notes)
													<div class="mt-2 pt-2 border-top text-muted fst-italic">{{ \Illuminate\Support\Str::limit($row->notes, 120) }}</div>
												@endif
											</div>
										</div>
									</div>
								@empty
									<div class="col-12 text-center text-muted py-4">
										No consultants found. Add rows to <code>followup_consultants</code> and run migrations.
									</div>
								@endforelse
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
