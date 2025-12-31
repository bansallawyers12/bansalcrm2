<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bansal CRM</title>
	<!-- Bootstrap CSS -->
    <link href="{{asset('css/font-awesome.min.css')}}" rel="stylesheet">
    @vite(['resources/js/app.js'])
    <link href="{{asset('css/style.css')}}" rel="stylesheet">
    <link href="{{asset('css/components.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/daterangepicker.css')}}">
</head>
<body>
	<!--Content-->
	@yield('content') 
			
	<!-- COMMON SCRIPTS -->
	<script type="text/javascript">
		var site_url = "<?php echo URL::to('/'); ?>";
		//var redirecturl = "<?php echo URL::to('/thanks'); ?>";
	</script>
		 
	<!-- Option 1: Bootstrap Bundle with Popper -->
	<script src="{{asset('js/jquery_min_latest.js')}}"></script>
	<script src="{{asset('js/moment.min.js')}}"></script>
	<script src="{{asset('js/daterangepicker.js')}}"></script> 

	<script>
		$(document).ready(function() {
			$(".dobdatepicker").daterangepicker({
        locale: { cancelLabel: 'Clear',format: "DD/MM/YYYY" },
        singleDatePicker: true,
		autoUpdateInput: false,
        showDropdowns: true
      }).on("apply.daterangepicker", function (e, picker) {
        picker.element.val(picker.startDate.format(picker.locale.format));
    });
		});		
	</script>
</body>
</html>