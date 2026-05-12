@extends(request()->routeIs('adminconsole.followups.*') ? 'layouts.adminconsole' : 'layouts.admin')
@section('title', 'Edit calendar setting')

@section('content')
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="row justify-content-center">
				<div class="col-12 col-lg-8">
					<div class="mb-3">
						<a href="{{ followups_console_route('calendar-settings.index') }}" class="btn btn-link ps-0">&larr; Back to calendar settings</a>
					</div>
					<div class="card">
						<div class="card-header">
							<h4 class="mb-0">Edit calendar setting</h4>
							<p class="text-muted small mb-0 mt-1">
								<strong>Free consultation</strong> — {{ $setting->consultant->name ?? 'Consultant' }}
							</p>
						</div>
						<div class="card-body">
							@if ($errors->any())
								<div class="alert alert-danger">
									<ul class="mb-0 ps-3">
										@foreach ($errors->all() as $err)
											<li>{{ $err }}</li>
										@endforeach
									</ul>
								</div>
							@endif
							@php
								$startRaw = (string) $setting->start_time;
								$endRaw = (string) $setting->end_time;
								$startVal = strlen($startRaw) >= 5 ? substr($startRaw, 0, 5) : '10:00';
								$endVal = strlen($endRaw) >= 5 ? substr($endRaw, 0, 5) : '17:00';
								$storedDays = $setting->available_days ?? [];
								$dayLabels = [
									1 => 'Monday',
									2 => 'Tuesday',
									3 => 'Wednesday',
									4 => 'Thursday',
									5 => 'Friday',
									6 => 'Saturday',
									7 => 'Sunday',
								];
							@endphp
							<form method="post" action="{{ followups_console_route('calendar-settings.update', $setting) }}">
								@csrf
								@method('PUT')
								<div class="row">
									<div class="col-md-6 mb-3">
										<label class="form-label">Start time <span class="text-danger">*</span></label>
										<input type="time" name="start_time" class="form-control" value="{{ old('start_time', $startVal) }}" required>
									</div>
									<div class="col-md-6 mb-3">
										<label class="form-label">End time <span class="text-danger">*</span></label>
										<input type="time" name="end_time" class="form-control" value="{{ old('end_time', $endVal) }}" required>
									</div>
								</div>
								<div class="mb-3">
									<label class="form-label">Slot duration (minutes) <span class="text-danger">*</span></label>
									<select name="slot_duration_minutes" class="form-control" required>
										@foreach ([5, 10, 15, 20, 30, 45, 60] as $m)
											<option value="{{ $m }}" @selected((int) old('slot_duration_minutes', $setting->slot_duration_minutes) === $m)>{{ $m }} minutes</option>
										@endforeach
									</select>
									<div class="form-text">Time between each follow-up slot.</div>
								</div>
								<div class="mb-3">
									<span class="form-label d-block">Available days</span>
									@php
										$oldDays = old('days');
										if ($oldDays !== null) {
											$checkedDayNums = array_map('intval', (array) $oldDays);
										} else {
											$checkedDayNums = $storedDays;
										}
									@endphp
									<div class="d-flex flex-wrap gap-3">
										@foreach ($dayLabels as $num => $label)
											<label class="form-check mb-0">
												<input type="checkbox" name="days[]" value="{{ $num }}" class="form-check-input"
													{{ in_array($num, $checkedDayNums, true) ? 'checked' : '' }}>
												<span class="form-check-label">{{ $label }}</span>
											</label>
										@endforeach
									</div>
									<div class="form-text">Leave all unchecked to allow <strong>all</strong> days of the week.</div>
								</div>
								<div class="mb-3">
									<input type="hidden" name="is_active" value="0">
									<label class="form-check mb-0" for="is_active">
										<input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
											{{ old('is_active', $setting->is_active ? '1' : '0') === '1' ? 'checked' : '' }}>
										<span class="form-check-label">Active (enable this calendar setting)</span>
									</label>
								</div>
								<div class="mb-3">
									<label class="form-label">Notes (optional)</label>
									<textarea name="notes" class="form-control" rows="3" maxlength="2000" placeholder="e.g. office / calendar note">{{ old('notes', $setting->notes) }}</textarea>
								</div>
								<button type="submit" class="btn btn-primary">Save</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
