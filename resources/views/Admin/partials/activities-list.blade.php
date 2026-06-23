{{-- Activity log list for AJAX refresh (matches client detail page markup) --}}
@foreach($activities as $activit)
	@php
		$admin = \App\Models\Staff::find($activit->created_by) ?? \App\Models\Admin::find($activit->created_by);
	@endphp
	@if($admin)
		@include('Admin.partials.activity-item', ['activit' => $activit, 'admin' => $admin])
	@endif
@endforeach
