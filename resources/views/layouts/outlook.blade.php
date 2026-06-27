<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>Bansal CRM | @yield('title')</title>
	<link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
	<link rel="dns-prefetch" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800" rel="stylesheet">
	@include('Elements.font-awesome-styles')
	<!-- jQuery 3.7.1 — single source (Phase 2a) -->
	<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
	@vite(['resources/sass/app.scss'])
	<link rel="stylesheet" href="{{ asset('css/style.css') }}">
	<link rel="stylesheet" href="{{ asset('css/components.css') }}">
	<link rel="stylesheet" href="{{ asset('css/custom.css') }}?v={{ (config('app.asset_version') ? config('app.asset_version').'-' : '') . filemtime(public_path('css/custom.css')) }}">
	@stack('styles')
</head>
<body>
	<div class="loader"></div>
	<div id="app">
		@yield('content')
	</div>
	@vite(['resources/js/app.js'])
	@vite(['resources/js/minimal-layout-scripts.js'])
	<script>
		var site_url = '{{ URL::to('/') }}';
	</script>
	@stack('scripts')
</body>
</html>
