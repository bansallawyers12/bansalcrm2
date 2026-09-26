@php
	$clientRecord = $clientRecord ?? null;
	$preferredApplicationAssigneeId = $clientRecord
		? \App\Support\LeadCreateAssignees::preferredApplicationAssigneeIdFromClientAssignee($clientRecord->assignee ?? null)
		: null;
@endphp
<div class="col-12 col-md-12 col-lg-12">
	<div class="form-group">
		<label for="application_assignee">Assignee <span class="span_req">*</span></label>
		<select
			data-valid="required"
			class="form-control application_assignee tomselect"
			id="application_assignee"
			name="assignee"
			@if($preferredApplicationAssigneeId) data-default-assignee="{{ $preferredApplicationAssigneeId }}" @endif
		>
			<option value="">Please Select Assignee</option>
			@foreach(\App\Support\LeadCreateAssignees::assignableStaff() as $staff)
				<option value="{{ $staff->id }}" {{ (int) $preferredApplicationAssigneeId === (int) $staff->id ? 'selected' : '' }}>
					{{ trim($staff->first_name.' '.$staff->last_name) }}@if($staff->office) ({{ $staff->office->office_name }})@endif
				</option>
			@endforeach
		</select>
		<span class="custom-error assignee_error" role="alert">
			<strong></strong>
		</span>
	</div>
</div>
