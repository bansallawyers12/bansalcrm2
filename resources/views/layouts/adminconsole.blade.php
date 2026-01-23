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
	<meta http-equiv="Content-Security-Policy" content="script-src 'self' 'unsafe-inline' 'unsafe-eval' https: http://localhost:5173 http://127.0.0.1:5173 http://localhost:5174 http://127.0.0.1:5174 ws://localhost:5173 ws://127.0.0.1:5173 ws://localhost:5174 ws://127.0.0.1:5174 https://cdn.jsdelivr.net; script-src-attr 'unsafe-inline' 'unsafe-hashes'; script-src-elem 'self' 'unsafe-inline' 'unsafe-eval' https: http://localhost:5173 http://127.0.0.1:5173 http://localhost:5174 http://127.0.0.1:5174 ws://localhost:5173 ws://127.0.0.1:5173 ws://localhost:5174 ws://127.0.0.1:5174 https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https: http://localhost:5173 http://127.0.0.1:5173 http://localhost:5174 http://127.0.0.1:5174 https://cdn.jsdelivr.net; connect-src 'self' ws://localhost:5173 ws://127.0.0.1:5173 ws://localhost:5174 ws://127.0.0.1:5174 http://localhost:5173 http://127.0.0.1:5173 http://localhost:5174 http://127.0.0.1:5174 https://maps.googleapis.com;">
	<!-- Note: IPv6 literals [::1] are NOT supported by CSP spec. Use 'localhost' which resolves to both IPv4 and IPv6. -->
	<title>Bansal CRM | Admin Console | @yield('title')</title>
	
	<!-- Load jQuery synchronously before any other scripts to ensure availability -->
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	
	<!-- Load Select2 from CDN (after jQuery, before other scripts) -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css">
	<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
	
	<!-- Load DataTables from CDN (after jQuery, before other scripts) -->
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
	<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
	
	<link rel="icon" type="image/png" href="{{asset('img/favicon.png')}}">
 	<!-- CSS for libraries now loaded via Vite (vendor-libs.js): iziToast, flatpickr -->
 	<!-- Note: select2 and DataTables are loaded from CDN above to avoid ES module issues -->
	<!-- FullCalendar v6 CSS is now loaded automatically via JavaScript -->
	<!-- TinyMCE - No CSS needed -->
	<link rel="stylesheet" href="{{asset('css/bootstrap-timepicker.min.css')}}">
	<!-- Template CSS -->
  
	<link rel="stylesheet" href="{{asset('css/bootstrap-formhelpers.min.css')}}">
	<!-- Vendor CSS now loaded via Vite (vendor-libs.js) -->
  
	<!-- Google Font: Nunito (standardized across CRM) -->
	<link rel="dns-prefetch" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800" rel="stylesheet">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="{{asset('icons/font-awesome/css/all.min.css')}}">
  
	<link rel="stylesheet" href="{{asset('css/style.css')}}">
  
	<link rel="stylesheet" href="{{asset('css/components.css')}}">
	<!-- Custom style CSS -->
	<link rel="stylesheet" href="{{asset('css/custom.css')}}">
	<!-- Modern Search CSS -->
	<link rel="stylesheet" href="{{asset('css/modern-search.css')}}">
	
    <link rel="stylesheet" href="{{asset('css/dataTables_min_latest.css')}}">
    
    @stack('styles')
    
<style>
/* Admin Console Layout - Fixed Left Sidebar */
.main-wrapper.adminconsole-wrapper {
	margin-left: 0 !important;
}

.main-wrapper.adminconsole-wrapper .main-content {
	margin-left: 250px !important;
	width: calc(100% - 250px) !important;
	padding-top: 20px;
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
				@include('../Elements/Admin/setting')
			</div>

			@yield('content')
				
			@include('../Elements/Admin/footer')
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

	<!-- Load jQuery FIRST as separate entry -->
	@vite(['resources/js/jquery-init.js'])
	
	<!-- Load FullCalendar v6 -->
	@vite(['resources/js/fullcalendar-init.js'])
	
 	<!-- Load vendor libraries (flatpickr, izitoast, intl-tel-input) -->
 	<!-- Note: select2 and DataTables are loaded from CDN in <head> section above -->
	@vite(['resources/js/vendor-libs.js'])
	
	<!-- Load UI libraries (feather-icons, jquery.nicescroll) -->
	@vite(['resources/js/ui-libs.js'])
	
	<!-- Then load main app with Bootstrap, etc -->
	@vite(['resources/js/app.js'])
	 
	<!-- TinyMCE scripts loaded conditionally via @push('tinymce-scripts') on pages that need it -->
	@stack('tinymce-scripts')
	<script src="{{asset('js/bootstrap-timepicker.min.js')}}" defer></script> 
	
	<script src="{{asset('js/bootstrap-formhelpers.min.js')}}" defer></script> 
	<script src="{{asset('js/custom-form-validation.js')}}" defer></script> 
	<script src="{{asset('js/scripts.js')}}" defer></script>   

	<!-- Custom JS File -->	
	<script src="{{asset('js/custom.js')}}" defer></script>
	
	<!-- Legacy initialization now loaded via Vite (legacy-init.js) -->
	@vite(['resources/js/legacy-init.js'])
	
	<!-- Modern Search - Replaces inline search initialization to prevent conflicts -->
	<script src="{{asset('js/modern-search.js')}}" defer></script>
	
	<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
		@csrf
		<input type="hidden" name="id" value="{{Auth::user()->id}}">
	</form>

	@stack('scripts')
	@yield('scripts')	
</body>
</html>
