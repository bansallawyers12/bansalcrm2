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
	<meta name="current-user-id" content="{{ optional(auth('admin')->user())->id }}"> 
	<meta http-equiv="Content-Security-Policy" content="script-src 'self' 'unsafe-inline' 'unsafe-eval' https: http://localhost:5173 http://127.0.0.1:5173 http://localhost:5174 http://127.0.0.1:5174 ws://localhost:5173 ws://127.0.0.1:5173 ws://localhost:5174 ws://127.0.0.1:5174 https://cdn.jsdelivr.net; script-src-attr 'unsafe-inline' 'unsafe-hashes'; script-src-elem 'self' 'unsafe-inline' 'unsafe-eval' https: http://localhost:5173 http://127.0.0.1:5173 http://localhost:5174 http://127.0.0.1:5174 ws://localhost:5173 ws://127.0.0.1:5173 ws://localhost:5174 ws://127.0.0.1:5174 https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https: http://localhost:5173 http://127.0.0.1:5173 http://localhost:5174 http://127.0.0.1:5174 https://cdn.jsdelivr.net; connect-src 'self' ws://localhost:5173 ws://127.0.0.1:5173 ws://localhost:5174 ws://127.0.0.1:5174 http://localhost:5173 http://127.0.0.1:5173 http://localhost:5174 http://127.0.0.1:5174 https://maps.googleapis.com;">
	<!-- Note: IPv6 literals [::1] are NOT supported by CSP spec. Use 'localhost' which resolves to both IPv4 and IPv6. -->
	<title>Bansal CRM | @yield('title')</title>
	
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
	<!--<link rel="stylesheet" href="{{--asset('css/niceCountryInput.css')--}}">-->
	<!--<link rel="stylesheet" href="{{--asset('css/flagstrap.css')--}}">-->
  
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
	
    <!--<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">-->
    
    <link rel="stylesheet" href="{{asset('css/dataTables_min_latest.css')}}">
    
    @stack('styles')
    

<!-- <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"> -->
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>-->
<!-- jQuery now loaded in <head> before Vite for compatibility with legacy scripts -->   

<style>
.dropbtn {
  background-color: transparent;
 border:0;
}
.ui.yellow.label, .ui.yellow.labels .label, .select2resultrepositorystatistics .yellow {background-color: #fbbd08!important;border-color: #fbbd08!important;color: #fff!important;}
.dropbtn:hover, .dropbtn:focus {
  background-color: transparent;
   border:0;
}

.mydropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #fff;
  min-width: 160px;
  overflow: auto;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}

.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

.mydropdown a:hover {background-color: #ddd;}

.show {display: block;}

/* Improved Color Contrast for Icons and Text - Better Readability */
/* Navbar Icons - Header Navigation */
.navbar .nav-link,
.navbar .nav-link-lg,
.navbar .nav-link i,
.navbar .nav-link-lg i {
    color: #2d3748 !important; /* Dark gray for better contrast */
}

.navbar .nav-link:hover,
.navbar .nav-link-lg:hover,
.navbar .nav-link:hover i,
.navbar .nav-link-lg:hover i {
    color: #1a202c !important; /* Even darker on hover */
}

/* Specific icon improvements */
.navbar .collapse-btn i,
.navbar .fullscreen-btn i,
.navbar .message-toggle i,
.navbar .notification-toggle i,
.navbar .opencheckin i {
    color: #2d3748 !important;
    opacity: 1 !important;
}

.navbar .collapse-btn:hover i,
.navbar .fullscreen-btn:hover i,
.navbar .message-toggle:hover i,
.navbar .notification-toggle:hover i,
.navbar .opencheckin:hover i {
    color: #1a202c !important;
}

/* Search Element - Improved Contrast */
.search-element .btn,
.search-element .btn i {
    color: #2d3748 !important;
}

.search-element .btn:hover,
.search-element .btn:hover i {
    color: #1a202c !important;
}

.search-element .form-control,
.search-element .select2-container .select2-selection__rendered {
    color: #1a202c !important;
}

/* Dropdown Elements */
.dropdown-toggle,
.dropdown-toggle i {
    color: #2d3748 !important;
}

.dropdown-toggle:hover,
.dropdown-toggle:hover i {
    color: #1a202c !important;
}

/* Sidebar Menu Items */
.sidebar-menu .nav-link,
.sidebar-menu .nav-link i,
.sidebar-menu .nav-link span {
    color: #2d3748 !important;
}

.sidebar-menu .nav-link:hover,
.sidebar-menu .nav-link:hover i,
.sidebar-menu .nav-link:hover span {
    color: #1a202c !important;
}

.sidebar-menu .active .nav-link,
.sidebar-menu .active .nav-link i,
.sidebar-menu .active .nav-link span {
    color: #1a202c !important;
    font-weight: 600 !important;
}

/* Menu Header Text */
.sidebar-menu .menu-header {
    color: #4a5568 !important;
    font-weight: 700 !important;
}

/* Dropdown Menu Items */
.dropdown-menu a,
.dropdown-menu .dropdown-item {
    color: #2d3748 !important;
}

.dropdown-menu a:hover,
.dropdown-menu .dropdown-item:hover {
    color: #1a202c !important;
    background-color: #f7fafc !important;
}

/* User Profile Dropdown */
.nav-link-user,
.nav-link-user span {
    color: #2d3748 !important;
}

/* Bell Icon with Badge */
.bell,
.bell i {
    color: #2d3748 !important;
}

/* Select2 Dropdown Text */
.select2-container .select2-selection__rendered {
    color: #1a202c !important;
}

.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #4a5568 !important;
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

/* Ensure all Font Awesome icons have good contrast */
.fas,
.far,
.fa,
[class^="fa-"],
[class*=" fa-"] {
    color: inherit;
}

/* Override any light gray icon colors */
i[style*="color: #"],
i[style*="color:#"],
i[style*="color: rgb"],
i[style*="color:rgba"] {
    color: #2d3748 !important;
}
</style>
</head>
<body >
	<div class="loader"></div>
		<div class="popuploader" style="display: none;"></div>
	<div id="app">
		<div class="main-wrapper main-wrapper-1">
			<div class="navbar-bg"></div>
			<!--Header-->
			@include('../Elements/Admin/header')
			<!--Left Side Bar-->
			@include('../Elements/Admin/left-side-bar')

			@yield('content')
				
			@include('../Elements/AdminConsole/footer')
		</div>
	</div>

   

		<?php if(@Settings::sitedata('date_format') != 'none'){
			   $date_format = @Settings::sitedata('date_format');
			 //  $time_format = @Settings::sitedata('time_format');
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
	 
	<!--<script src="{{--asset('js/niceCountryInput.js')--}}"></script> -->  
	<!-- Bootstrap is already loaded via Vite (app.js -> bootstrap.js), no need for duplicate bundle -->
	<!-- Feather Icons and jQuery NiceScroll now loaded via Vite (ui-libs.js) -->
	<!-- FullCalendar v6 now loaded via Vite (fullcalendar-init.js) -->
 	<!-- flatpickr, iziToast now loaded via Vite (vendor-libs.js) -->
 	<!-- Select2 and DataTables are loaded from CDN in <head> section -->
  
	<!--<script src="{{--asset('js/chart.min.js')--}}"></script>-->
  
	 <!-- JS Libraies -->
	<!--<script src="{{--asset('js/apexcharts.min.js')--}}"></script>--> 
	<!-- Page Specific JS File -->	
	<!--<script src="{{asset('js/index.js')}}"></script> -->  
	<!-- TinyMCE scripts loaded conditionally via @push('tinymce-scripts') on pages that need it -->
	@stack('tinymce-scripts')
	<script src="{{asset('js/bootstrap-timepicker.min.js')}}" defer></script> 
	
	<!--<script src="{{--asset('js/jquery.flagstrap.js')--}}"></script>--> 
	<script src="{{asset('js/bootstrap-formhelpers.min.js')}}" defer></script> 
	<script src="{{asset('js/custom-form-validation.js')}}" defer></script> 
	<script src="{{asset('js/scripts.js')}}" defer></script>   

	<!-- Custom JS File -->	
	<script src="{{asset('js/custom.js')}}" defer>
	</script>
	
	<!-- Legacy initialization now loaded via Vite (legacy-init.js) -->
	@vite(['resources/js/legacy-init.js'])
	
	<!-- Modern Search - Replaces inline search initialization to prevent conflicts -->
	<script src="{{asset('js/modern-search.js')}}" defer></script>
	
	<div id="checkinmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">Create In Person Client</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" name="checkinmodalsave" id="checkinmodalsave" action="{{URL::to('/checkin')}}" autocomplete="off" enctype="multipart/form-data">
				@csrf
			
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">Search Contact <span class="span_req">*</span></label>
								<select data-valid="required" class="js-data-example-ajax-check" name="contact"></select>
								@if ($errors->has('email_from'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_from') }}</strong>
									</span> 
								@endif
							</div> 
						</div>
						<input type="hidden" id="utype" name="utype" value="">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">Office <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control" name="office">
									<option value="">Select</option>
									@foreach(\App\Models\Branch::all() as $of)
										<option value="{{$of->id}}">{{$of->office_name}}</option>
									@endforeach
								</select>
								
							</div>
						</div>
						
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Visit Purpose <span class="span_req">*</span></label>
								<textarea class="form-control" name="message"></textarea>
								@if ($errors->has('message'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('message') }}</strong>
									</span>  
								@endif
							</div>
						</div>
						
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Select In Person Assignee <span class="span_req">*</span></label>
								<?php
								$assignee = \App\Models\Admin::where('role','!=', '7')->get();
								?>
								<select class="form-control assineeselect2" name="assignee">
								@foreach($assignee as $assigne)
									<option value="{{$assigne->id}}">{{$assigne->first_name}} ({{$assigne->email}})</option>
								@endforeach
								</select>
								@if ($errors->has('message'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('message') }}</strong>
									</span>  
								@endif
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('checkinmodalsave')" type="button" class="btn btn-primary">Send</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="checkindetailmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">In Person Details</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body showchecindetail">
				
			</div>
		</div>
	</div>
</div>
	<!-- Teams-style office visit notification popups -->
	@auth('admin')
	<div class="teams-notification-container" id="teamsNotificationContainer" aria-label="Office visit notifications"></div>
	<style>
		.teams-notification-container { position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column-reverse; gap: 12px; max-width: 360px; }
		.teams-notification-card { background: #fff; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,.15); border-left: 4px solid #1f1655; overflow: hidden; animation: teamsNotificationIn 0.25s ease; }
		@keyframes teamsNotificationIn { from { opacity: 0; transform: translateX(100%); } to { opacity: 1; transform: translateX(0); } }
		.teams-notification-card.minimized { max-height: 48px; }
		.teams-notification-card.minimized .teams-notification-body { display: none; }
		.teams-notification-header { padding: 10px 12px; background: #f8f9fa; display: flex; justify-content: space-between; align-items: center; cursor: pointer; }
		.teams-notification-header h6 { margin: 0; font-size: 14px; font-weight: 600; color: #1f1655; }
		.teams-notification-actions { display: flex; gap: 4px; }
		.teams-notification-actions button { padding: 2px 8px; font-size: 12px; border: none; border-radius: 4px; cursor: pointer; }
		.teams-notification-body { padding: 12px; font-size: 13px; }
		.teams-notification-body p { margin: 0 0 6px; }
		.teams-notification-body .btn-group { margin-top: 10px; display: flex; gap: 8px; flex-wrap: wrap; }
		.teams-notification-body .btn-group .btn { font-size: 12px; padding: 6px 12px; }
	</style>
	<script>
	(function() {
		var container = document.getElementById('teamsNotificationContainer');
		if (!container) return;
		var currentUserId = document.querySelector('meta[name="current-user-id"]');
		currentUserId = currentUserId ? (currentUserId.getAttribute('content') || '').trim() : '';
		var baseUrl = document.querySelector('script[data-site-url]') ? document.querySelector('script[data-site-url]').getAttribute('data-site-url') : (typeof site_url !== 'undefined' ? site_url : '');
		var fetchUrl = '{{ url("/fetch-office-visit-notifications") }}';
		var markSeenUrl = '{{ url("/mark-notification-seen") }}';
		var updateStatusUrl = '{{ url("/update-checkin-status") }}';
		var csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

		function showTeamsNotification(notification) {
			if (!notification || !notification.id) return;
			var id = 'ov-notif-' + notification.id;
			if (document.getElementById(id)) return;
			var card = document.createElement('div');
			card.className = 'teams-notification-card';
			card.id = id;
			card.setAttribute('data-notification-id', notification.id);
			card.setAttribute('data-checkin-id', notification.checkin_id || '');
			card.innerHTML =
				'<div class="teams-notification-header">' +
					'<h6>Office Visit Assignment</h6>' +
					'<div class="teams-notification-actions">' +
						'<button type="button" class="btn btn-sm btn-outline-secondary teams-notification-minimize" aria-label="Minimize">−</button>' +
						'<button type="button" class="btn btn-sm btn-outline-secondary teams-notification-close" aria-label="Close">×</button>' +
					'</div>' +
				'</div>' +
				'<div class="teams-notification-body">' +
					'<p><strong>' + (notification.sender_name || '') + '</strong></p>' +
					'<p>Client: ' + (notification.client_name || '') + '</p>' +
					'<p>Purpose: ' + (notification.visit_purpose || '') + '</p>' +
					'<p>Time: ' + (notification.created_at || '') + '</p>' +
					'<div class="btn-group">' +
						'<button type="button" class="btn btn-success teams-notification-plssend">Pls Send The Client</button>' +
						'<a href="' + (notification.url || baseUrl + '/office-visits/waiting') + '" class="btn btn-outline-primary">View Details</a>' +
					'</div>' +
				'</div>';
			container.appendChild(card);

			card.querySelector('.teams-notification-close').addEventListener('click', function() { closeNotification(notification.id); });
			card.querySelector('.teams-notification-minimize').addEventListener('click', function() { card.classList.toggle('minimized'); });
			card.querySelector('.teams-notification-plssend').addEventListener('click', function() {
				attendSession(notification.checkin_id, notification.id, card);
			});
		}

		function closeNotification(notificationId) {
			var el = document.getElementById('ov-notif-' + notificationId);
			if (el) el.remove();
			if (notificationId && csrfToken) {
				fetch(markSeenUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify({ notification_id: notificationId }) });
			}
		}

		function attendSession(checkinId, notificationId, cardEl) {
			if (!checkinId) return;
			if (cardEl) cardEl.remove();
			if (csrfToken && updateStatusUrl) {
				fetch(updateStatusUrl, {
					method: 'POST',
					headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
					body: JSON.stringify({ checkin_id: checkinId, notification_id: notificationId, status: 0, wait_type: 1 })
				}).then(function(r) { return r.json(); }).then(function() {
					if (notificationId && markSeenUrl) {
						fetch(markSeenUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ notification_id: notificationId }) });
					}
				});
			}
		}

		function loadOfficeVisitNotifications() {
			if (!fetchUrl) return;
			fetch(fetchUrl, { headers: { 'Accept': 'application/json' } })
				.then(function(r) { return r.json(); })
				.then(function(data) {
					if (data.notifications && data.notifications.length) {
						data.notifications.forEach(function(n) { showTeamsNotification(n); });
					}
				})
				.catch(function() {});
		}

		function setupOfficeVisitRealtimeNotifications() {
			if (typeof window.Echo === 'undefined' || !currentUserId) return;
			window.Echo.private('user.' + currentUserId)
				.listen('.OfficeVisitNotificationCreated', function(e) {
					if (e && e.notification) showTeamsNotification(e.notification);
				});
		}

		loadOfficeVisitNotifications();
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', setupOfficeVisitRealtimeNotifications);
		} else {
			setupOfficeVisitRealtimeNotifications();
		}
	})();
	</script>
	@endauth

	<!-- Auto-logout after 15 minutes of inactivity -->
	<script src="{{ asset('js/inactivity-logout.js') }}" defer></script>

@stack('scripts')
@yield('scripts')	
	<!--<script src="{{--asset('js/custom-chart.js')--}}"></script>-->  
</body>
</html>