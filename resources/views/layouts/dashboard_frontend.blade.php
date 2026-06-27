<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bansal CRM</title>
	
	<!-- jQuery 3.7.1 — single source (Phase 2a: sync in head; do not also load via Vite) -->
	<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
	
	<!-- Bootstrap CSS in head (prevents FOUC); Bootstrap JS still loaded via app.js -->
	@vite(['resources/sass/app.scss'])
	<!-- Vendor CSS + JS (flatpickr, iziToast, Tom Select) — CSS in head prevents FOUC (Phase 2b) -->
	@vite(['resources/js/vendor-libs.js'])
    @include('Elements.font-awesome-styles')
    <link href="{{asset('css/style.css')}}" rel="stylesheet">
    <link href="{{asset('css/components.css')}}" rel="stylesheet">
    <!-- flatpickr CSS now loaded via Vite (vendor-libs.js) -->
</head>
<body>
	<!--Content-->
	@yield('content') 
			
	<!-- COMMON SCRIPTS -->
	<script type="text/javascript">
		var site_url = "<?php echo URL::to('/'); ?>";
		//var redirecturl = "<?php echo URL::to('/thanks'); ?>";
	</script>
	
	<!-- Bootstrap / app JS (vendor-libs loaded in <head>) -->
	@vite(['resources/js/app.js'])

	<script>
		$(document).ready(function() {
			if (typeof flatpickr !== 'undefined' && $(".dobdatepicker").length) {
				$(".dobdatepicker").each(function() {
					flatpickr(this, {
						dateFormat: "d/m/Y",
						allowInput: true
					});
				});
			}
		});		
	</script>
</body>
</html>