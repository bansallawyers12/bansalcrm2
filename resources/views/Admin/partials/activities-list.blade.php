{{-- Activity log list for AJAX refresh (matches client detail page markup) --}}
@php
	$staffMap = $staffMap ?? null;
	$adminMap = $adminMap ?? null;
@endphp
@foreach($activities as $activit)
	@php
		if ($staffMap !== null || $adminMap !== null) {
			$admin = ($staffMap[$activit->created_by] ?? null) ?? ($adminMap[$activit->created_by] ?? null);
		} else {
			$admin = \App\Models\Staff::find($activit->created_by) ?? \App\Models\Admin::find($activit->created_by);
		}
	@endphp
	@if($admin)
		@include('Admin.partials.activity-item', ['activit' => $activit, 'admin' => $admin])
	@endif
@endforeach
