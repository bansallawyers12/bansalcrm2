{{-- Notes list for client/lead detail and AJAX refresh --}}
@foreach($notelist as $list)
	@php
		$staff = \App\Models\Staff::select('id', 'first_name', 'last_name', 'email', 'team')->find($list->user_id);
		$color = $staff?->team ? \App\Models\Team::select('color')->where('id', $staff->team)->first() : null;
	@endphp
	@include('Admin.partials.note-item', ['list' => $list, 'staff' => $staff, 'color' => $color])
@endforeach
