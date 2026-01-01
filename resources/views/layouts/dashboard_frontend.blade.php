<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bansal CRM</title>
	
	<!-- Load jQuery synchronously before any other scripts to ensure availability -->
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	
	<!-- Bootstrap CSS -->
    <link href="{{asset('css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/style.css')}}" rel="stylesheet">
    <link href="{{asset('css/components.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/flatpickr.min.css')}}">
</head>
<body>
	<!--Content-->
	@yield('content') 
			
	<!-- COMMON SCRIPTS -->
	<script type="text/javascript">
		var site_url = "<?php echo URL::to('/'); ?>";
		//var redirecturl = "<?php echo URL::to('/thanks'); ?>";
	</script>
	
	<!-- Load jQuery FIRST as separate entry (synchronous) -->
	@vite(['resources/js/jquery-init.js'])
	
	<!-- Then load main app with Bootstrap, etc (async) -->
	@vite(['resources/js/app.js'])
	
	<!-- jQuery should now be available immediately -->
		 
	<!-- Option 1: Bootstrap Bundle with Popper -->
	<script src="{{asset('js/flatpickr.min.js')}}"></script> 

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