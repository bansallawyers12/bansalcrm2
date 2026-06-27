@php
	$crmIconCssVersion = (config('app.asset_version') ? config('app.asset_version') . '-' : '')
		. filemtime(public_path('css/crm-icons.css'));
@endphp
<link rel="stylesheet" href="{{ asset('css/crm-icons.css') }}?v={{ $crmIconCssVersion }}">
