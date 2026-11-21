<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="description" content="">
	<meta name="author" content="Bansal CRM">
	<meta http-equiv="Content-Security-Policy" content="script-src 'self' 'unsafe-inline' 'unsafe-eval' https: http://localhost:5173 http://127.0.0.1:5173 http://localhost:5174 http://127.0.0.1:5174 ws://localhost:5173 ws://127.0.0.1:5173 ws://localhost:5174 ws://127.0.0.1:5174 https://www.google.com https://www.gstatic.com; script-src-attr 'unsafe-inline' 'unsafe-hashes'; script-src-elem 'self' 'unsafe-inline' 'unsafe-eval' https: http://localhost:5173 http://127.0.0.1:5173 http://localhost:5174 http://127.0.0.1:5174 ws://localhost:5173 ws://127.0.0.1:5173 ws://localhost:5174 ws://127.0.0.1:5174 https://www.google.com https://www.gstatic.com; style-src 'self' 'unsafe-inline' https: http://localhost:5173 http://127.0.0.1:5173 http://localhost:5174 http://127.0.0.1:5174 https://www.google.com https://www.gstatic.com; connect-src 'self' ws://localhost:5173 ws://127.0.0.1:5173 ws://localhost:5174 ws://127.0.0.1:5174 http://localhost:5173 http://127.0.0.1:5173 http://localhost:5174 http://127.0.0.1:5174 https://maps.googleapis.com https://www.google.com https://www.gstatic.com; worker-src 'self' https://www.google.com https://www.gstatic.com; frame-src 'self' https://www.google.com https://www.gstatic.com;">
	<!-- Note: IPv6 literals [::1] are NOT supported by CSP spec. Use 'localhost' which resolves to both IPv4 and IPv6. -->
	<link rel="shortcut icon" type="image/png" href="{{asset('img/favicon.png')}}"/>
	<title>Bansal CRM | @yield('title')</title>
	
	<!-- jQuery 3.7.1 — single source (Phase 2a: sync in head; do not also load via Vite) -->
	<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
	
	<!-- Favicons-->
	<link rel="shortcut icon" href="{{asset('img/favicon.png')}}" type="image/x-icon">
			 
	 <!-- BASE CSS -->
	@vite(['resources/sass/app.scss'])
	<link href="{{asset('css/bootstrap-social.css')}}" rel="stylesheet">	
	<link href="{{asset('css/style.css')}}" rel="stylesheet">	
	<link href="{{asset('css/components.css')}}" rel="stylesheet">	
	<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
	<link href="{{ asset('css/custom.css') }}?v={{ (config('app.asset_version') ? config('app.asset_version').'-' : '') . filemtime(public_path('css/custom.css')) }}" rel="stylesheet">
	<link href="{{ asset('css/crm-theme.css') }}?v={{ (config('app.asset_version') ? config('app.asset_version').'-' : '') . filemtime(public_path('css/crm-theme.css')) }}" rel="stylesheet">
	
	<script async src="https://www.google.com/recaptcha/api.js"></script> <!-- Add recaptcha script -->
</head>
<style>
html, body.bg {
    height: 100%;
}
.bg {
    margin: 0;
    min-height: 100vh;
    background-color: #e7eef3;
    background-repeat: no-repeat;
    background-size: cover;
    /* Keep her face (right side of the photo) in frame as the viewport crops. */
    background-position: 78% center;
}
.login-shell {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    box-sizing: border-box;
    padding-top: clamp(1.25rem, 4vh, 2.5rem);
    padding-bottom: clamp(1.25rem, 4vh, 2.5rem);
    padding-right: clamp(1rem, 4vw, 2.5rem);
    padding-left: clamp(1.5rem, 7vw, 6.5rem);
}
.login-panel {
    width: min(420px, 100%);
}
.login-panel .card {
    border: 0;
    border-radius: 14px;
    box-shadow: 0 16px 40px rgba(16, 32, 48, 0.22);
}
.login-footnote {
    margin-top: 0.85rem;
    padding: 0.55rem 0.75rem;
    text-align: center;
    color: #243140;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 10px;
}
@media (max-width: 767.98px) {
    .bg {
        /* On a tall screen, park her face above the form. */
        background-position: 82% 18%;
    }
    .login-shell {
        align-items: flex-end;
        padding: 1rem 1rem 1.25rem;
    }
}
</style>
<body class="bg">
	<div class="loader"></div>
	<div id="app">
		@yield('content')
	</div>
	<!-- COMMON SCRIPTS -->
	<script type="text/javascript">
		var site_url = "<?php echo URL::to('/'); ?>";
		// Try to load background image, but don't fail if it doesn't exist
		(function() {
			var img = new Image();
			img.onload = function() {
				document.body.style.backgroundImage = "url('{{ asset('img/login-background.jpg') }}')";
			};
			img.src = '{{ asset('img/login-background.jpg') }}';
		})();
	</script>
	
	<!-- Then load main app with Bootstrap, etc (async) -->
	@vite(['resources/js/app.js'])
	@vite(['resources/js/minimal-layout-scripts.js'])
</body>
</html>