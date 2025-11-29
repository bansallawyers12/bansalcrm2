<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bansal CRM</title>
	<!-- Bootstrap CSS -->
    <link href="{{asset('public/css/DashboardFrontend/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/css/DashboardFrontend/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/css/DashboardFrontend/style.css')}}" rel="stylesheet">
    <link href="{{asset('public/css/DashboardFrontend/responsive.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('public/css/daterangepicker.css')}}">
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
	<script src="{{asset('public/js/DashboardFrontend/jquery-3.3.1.min.js')}}"></script>
	<script src="{{asset('public/js/DashboardFrontend/bootstrap.bundle.min.js')}}"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
	<script src="{{asset('public/js/daterangepicker.js')}}"></script> 

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