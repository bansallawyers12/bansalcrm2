{{-- Notes list for client/lead detail and AJAX refresh --}}
@php
	if (!isset($staffMap) && isset($notelist)) {
		$staffIds = $notelist->pluck('user_id')->unique()->filter();
		$staffMap = \App\Models\Staff::whereIn('id', $staffIds)->get()->keyBy('id');
	}
@endphp
@foreach($notelist as $list)
	@php
		$staff = isset($staffMap) ? ($staffMap[$list->user_id] ?? null) : \App\Models\Staff::select('id', 'first_name', 'last_name', 'email', 'team')->find($list->user_id);
	@endphp
	@include('Admin.partials.note-item', ['list' => $list, 'staff' => $staff])
@endforeach
