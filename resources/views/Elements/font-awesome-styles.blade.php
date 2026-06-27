@php
	$faAllVersion = (config('app.asset_version') ? config('app.asset_version') . '-' : '')
		. filemtime(public_path('icons/font-awesome/css/all.min.css'));
	$faShimVersion = (config('app.asset_version') ? config('app.asset_version') . '-' : '')
		. filemtime(public_path('icons/font-awesome/css/v4-shims.min.css'));
@endphp
<link rel="stylesheet" href="{{ asset('icons/font-awesome/css/all.min.css') }}?v={{ $faAllVersion }}">
<link rel="stylesheet" href="{{ asset('icons/font-awesome/css/v4-shims.min.css') }}?v={{ $faShimVersion }}">
