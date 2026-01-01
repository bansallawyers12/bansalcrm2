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
	<link rel="shortcut icon" type="image/png" href="{{asset('img/favicon.png')}}"/>
	<title>Bansal CRM | @yield('title')</title>
	
	<!-- Load jQuery synchronously before any other scripts to ensure availability -->
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	
	<!-- Favicons-->
	<link rel="shortcut icon" href="{{asset('img/favicon.png')}}" type="image/x-icon">
			 
	 <!-- BASE CSS -->
	<link href="{{asset('css/bootstrap-social.css')}}" rel="stylesheet">	
	<link href="{{asset('css/style.css')}}" rel="stylesheet">	
	<link href="{{asset('css/components.css')}}" rel="stylesheet">	
	<link href="{{asset('css/custom.css')}}" rel="stylesheet">
	
	<script async src="https://www.google.com/recaptcha/api.js"></script> <!-- Add recaptcha script -->
</head>
<style>
.bg{
    background-color: #f8f9fa; /* Fallback background color */
    height: 100%;
    margin: 0;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
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
				document.body.style.backgroundImage = "url('{{ asset('img/bansal_crm_background_image.jpg') }}')";
			};
			img.src = '{{ asset('img/bansal_crm_background_image.jpg') }}';
		})();
	</script>
	
	<!-- Load jQuery FIRST as separate entry (synchronous) -->
	@vite(['resources/js/jquery-init.js'])
	
	<!-- Then load main app with Bootstrap, etc (async) -->
	@vite(['resources/js/app.js'])
	
	<!-- jQuery should now be available immediately -->
	
	<!-- Load legacy scripts that depend on jQuery -->
	<script src="{{asset('js/scripts.js')}}"></script>
	<script src="{{asset('js/custom.js')}}"></script>
</body>
</html>