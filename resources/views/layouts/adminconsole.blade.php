<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
	<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="keyword" content="Bansal CRM">
	<meta name="csrf-token" content="{{ csrf_token() }}"> 
	<meta http-equiv="Content-Security-Policy" content="script-src 'self' 'unsafe-inline' 'unsafe-eval' https: http://localhost:5173 http://127.0.0.1:5173 http://localhost:5174 http://127.0.0.1:5174 ws://localhost:5173 ws://127.0.0.1:5173 ws://localhost:5174 ws://127.0.0.1:5174 https://cdn.jsdelivr.net; script-src-attr 'unsafe-inline' 'unsafe-hashes'; script-src-elem 'self' 'unsafe-inline' 'unsafe-eval' https: http://localhost:5173 http://127.0.0.1:5173 http://localhost:5174 http://127.0.0.1:5174 ws://localhost:5173 ws://127.0.0.1:5173 ws://localhost:5174 ws://127.0.0.1:5174 https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https: http://localhost:5173 http://127.0.0.1:5173 http://localhost:5174 http://127.0.0.1:5174 https://cdn.jsdelivr.net; connect-src 'self' ws://localhost:5173 ws://127.0.0.1:5173 ws://localhost:5174 ws://127.0.0.1:5174 http://localhost:5173 http://127.0.0.1:5173 http://localhost:5174 http://127.0.0.1:5174 https://cdn.jsdelivr.net https://maps.googleapis.com;">
	<!-- Note: IPv6 literals [::1] are NOT supported by CSP spec. Use 'localhost' which resolves to both IPv4 and IPv6. -->
	<title>Bansal CRM | Admin Console | @yield('title')</title>
	
	<!-- jQuery 3.7.1 — single source (Phase 2a: sync in head; do not also load via Vite) -->
	<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
	
	<!-- DataTables: bundled via Vite (vendor-libs.js, Phase 2c) -->

	<link rel="icon" type="image/png" href="{{asset('img/favicon.png')}}">
 	<!-- CSS for libraries now loaded via Vite (vendor-libs.js): iziToast, flatpickr, Tom Select, DataTables -->
	<!-- FullCalendar v6 CSS is now loaded automatically via JavaScript -->
	<!-- TinyMCE - No CSS needed -->
	<!-- Template CSS -->
  
	<!-- Vendor CSS now loaded via Vite (vendor-libs.js) -->
  
	<!-- Google Font: Nunito (standardized across CRM) -->
	<link rel="dns-prefetch" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800" rel="stylesheet">
	<!-- Font Awesome 6 -->
	@include('Elements.font-awesome-styles')

	<!-- Bootstrap CSS in head (prevents header FOUC); Bootstrap JS still loaded via app.js -->
	@vite(['resources/sass/app.scss'])
	<!-- Vendor CSS + JS (flatpickr, iziToast, Tom Select, DataTables) — CSS in head prevents FOUC -->
	@vite(['resources/js/vendor-libs.js'])
  
	<link rel="stylesheet" href="{{asset('css/style.css')}}">
  
	<link rel="stylesheet" href="{{asset('css/components.css')}}">
	<!-- Custom style CSS -->
	<link rel="stylesheet" href="{{ asset('css/custom.css') }}?v={{ (config('app.asset_version') ? config('app.asset_version').'-' : '') . filemtime(public_path('css/custom.css')) }}">
	<link rel="stylesheet" href="{{ asset('css/tomselect-bridge.css') }}?v={{ (config('app.asset_version') ? config('app.asset_version').'-' : '') . filemtime(public_path('css/tomselect-bridge.css')) }}">
	<!-- Modern Search CSS -->
	<link rel="stylesheet" href="{{ asset('css/modern-search.css') }}?v={{ (config('app.asset_version') ? config('app.asset_version').'-' : '') . filemtime(public_path('css/modern-search.css')) }}">
	
    <link rel="stylesheet" href="{{asset('css/dataTables_min_latest.css')}}">
    
    @stack('styles')
    
    @include('../Elements/AdminConsole/styles')
    
<style>
/* Admin Console Layout - Fixed Left Sidebar */
.main-wrapper.adminconsole-wrapper {
	margin-left: 0 !important;
}

.main-wrapper.adminconsole-wrapper .main-content {
	margin-left: 250px !important;
	width: calc(100% - 250px) !important;
	padding: 0 !important;
	margin-top: 0 !important;
	padding-top: 0 !important;
	position: relative;
	top: 0;
}

/* Ensure main-content starts immediately after header with no gap */
.main-wrapper.adminconsole-wrapper {
	padding-top: 0 !important;
	margin-top: 0 !important;
}

.main-wrapper.adminconsole-wrapper .adminconsole-header {
	margin-bottom: 0 !important;
	padding-bottom: 0 !important;
}

/* Fixed Left Sidebar */
.adminconsole-sidebar {
	position: fixed;
	top: 80px;
	left: 0;
	width: 250px;
	height: calc(100vh - 80px);
	background: #fff;
	border-right: 1px solid #e3e6f0;
	overflow-y: auto;
	z-index: 999;
	padding: 20px 0;
}

.adminconsole-sidebar .custom_nav_setting {
	padding: 0 15px;
}

.adminconsole-sidebar .custom_nav_setting ul {
	list-style: none;
	padding: 0;
	margin: 0;
}

.adminconsole-sidebar .custom_nav_setting ul li {
	margin-bottom: 5px;
}

.adminconsole-sidebar .custom_nav_setting ul li a {
	display: block;
	padding: 12px 15px;
	color: #6c757d;
	text-decoration: none;
	border-radius: 5px;
	transition: all 0.3s ease;
	font-size: 14px;
}

.adminconsole-sidebar .custom_nav_setting ul li a:hover {
	background: #f8f9fa;
	color: #495057;
}

.adminconsole-sidebar .custom_nav_setting ul li.active a {
	background: #6777ef;
	color: #fff;
	font-weight: 500;
}

/* Exit Button Styles */
.adminconsole-exit-btn {
	margin-right: 15px;
}

.adminconsole-header {
	background: #fff;
	padding: 15px 30px;
	border-bottom: 1px solid #e3e6f0;
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.adminconsole-header .logo-section {
	display: flex;
	align-items: center;
	gap: 15px;
}

.adminconsole-header .logo-section img {
	height: 40px;
}

.adminconsole-header .exit-section {
	display: flex;
	align-items: center;
	gap: 10px;
}

.adminconsole-header .user-img-radious-style {
	width: 45px;
	height: 45px;
	border-radius: 50%;
	object-fit: cover;
}

/* Improved Color Contrast for Icons and Text - Better Readability */
.navbar .nav-link,
.navbar .nav-link-lg,
.navbar .nav-link i,
.navbar .nav-link-lg i {
    color: #2d3748 !important;
}

.navbar .nav-link:hover,
.navbar .nav-link-lg:hover,
.navbar .nav-link:hover i,
.navbar .nav-link-lg:hover i {
    color: #1a202c !important;
}

/* Form Controls Text */
.form-control,
.form-control::placeholder {
    color: #1a202c !important;
}

.form-control::placeholder {
    color: #718096 !important;
    opacity: 1 !important;
}

/* Button Text */
.btn:not(.btn-primary):not(.btn-success):not(.btn-danger):not(.btn-warning):not(.btn-info) {
    color: #2d3748 !important;
}

/* General Text Improvements */
.text-muted {
    color: #4a5568 !important;
}
</style>
</head>
<body>
	<div class="loader"></div>
	<div class="popuploader" style="display: none;"></div>
	<div id="app">
		<div class="main-wrapper main-wrapper-1 adminconsole-wrapper">
			<div class="navbar-bg"></div>
			
				<!-- Admin Console Header with Exit Button -->
			<div class="adminconsole-header">
				<div class="logo-section">
					<img alt="Bansal CRM" src="{{ asset('img/logo.png') }}" />
					<h4 style="margin: 0; color: #2d3748;">Admin Console</h4>
				</div>
				<div class="exit-section">
					<a href="{{route('dashboard')}}" class="btn btn-primary adminconsole-exit-btn">
						<i class="fas fa-arrow-left"></i> Exit to Main Dashboard
					</a>
					@if(Auth::user())
						<div class="dropdown">
							<a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
								@if(@Auth::user()->profile_img == '')
									<img alt="user image" src="{{ asset('img/user.png') }}" class="user-img-radious-style">
								@else
									<img alt="{{str_limit(Auth::user()->first_name.' '.Auth::user()->last_name, 150, '...')}}" src="{{asset('img/profile_imgs')}}/{{@Auth::user()->profile_img}}" class="user-img-radious-style"/>
								@endif	
								<span class="d-sm-none d-lg-inline-block"></span>
							</a>
							<div class="dropdown-menu dropdown-menu-right pullDown">
								<div class="dropdown-title">{{str_limit(Auth::user()->first_name.' '.Auth::user()->last_name, 150, '...')}}</div>
								<a href="{{route('my_profile')}}" class="dropdown-item has-icon">
									<i class="far fa-user"></i> Profile
								</a>
								<div class="dropdown-divider"></div>
								<a href="{{route('admin.logout')}}" class="dropdown-item has-icon text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> 
									<i class="fas fa-sign-out-alt"></i> Logout
								</a>
							</div>
						</div>
					@endif
				</div>
			</div>

			<!-- Fixed Left Sidebar -->
			<div class="adminconsole-sidebar">
				@include('../Elements/AdminConsole/setting')
			</div>

			@yield('content')
				
			@include('../Elements/AdminConsole/footer')
		</div>
	</div>

	<?php if(@Settings::sitedata('date_format') != 'none'){
		   $date_format = @Settings::sitedata('date_format');
		 if($date_format == 'd/m/Y'){
		     $dataformat =  'DD/MM/YYYY';
		 }else if($date_format == 'm/d/Y'){
		     $dataformat =  'MM/DD/YYYY';
		 }else if($date_format == 'Y-m-d'){
	    	 $dataformat = 'YYYY-MM-DD';
		 }else{
		    $dataformat = 'YYYY-MM-DD';
		 }
		}else{
		  $dataformat = 'YYYY-MM-DD';
		}
		?>
			<script>
			    var site_url = '{{URL::to('/')}}';
			     var dataformat = '{{$dataformat}}';
			    </script>

	<!-- Load FullCalendar v6 -->
	@vite(['resources/js/fullcalendar-init.js'])

	<!-- Bootstrap / app JS (vendor-libs loaded in <head>) -->
	@vite(['resources/js/app.js'])
	 
	<!-- TinyMCE scripts loaded conditionally via @push('tinymce-scripts') on pages that need it -->
	@stack('tinymce-scripts')
	@if(request()->is('agent', 'agent/*'))
	@vite(['resources/js/agent-adminconsole-layout-scripts.js'])
	@else
	@vite(['resources/js/adminconsole-layout-scripts.js'])
	@endif

	<!-- Legacy initialization now loaded via Vite (legacy-init.js) -->
	@vite(['resources/js/legacy-init.js'])
	
	<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
		@csrf
		<input type="hidden" name="id" value="{{Auth::user()->id}}">
	</form>

	@include('partials.email-from-ses-script')

	@stack('scripts')
	@yield('scripts')	
</body>
</html>
