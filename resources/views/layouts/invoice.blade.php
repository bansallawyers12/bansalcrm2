<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Best tour planner is leading travel agency in delhi offer best holiday packages services">
    <meta name="author" content="Ansonika">
    <title>@yield('title') </title>
	<!--<title>@yield('title')</title>-->
	<!-- Favicons-->
    <link rel="shortcut icon" href="{!! asset('public/img/Frontend/img/favicon.png') !!}" type="image/x-icon">
    <link rel="apple-touch-icon" type="image/x-icon" href="{!! asset('public/img/Frontend/img/apple-touch-icon-57x57-precomposed.png') !!}">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="{!! asset('public/img/Frontend/img/apple-touch-icon-72x72-precomposed.png') !!}">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="114x114" href="{!! asset('public/img/Frontend/img/apple-touch-icon-114x114-precomposed.png') !!}">

 <!-- GOOGLE WEB FONT -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800" rel="stylesheet">
    <!-- BASE CSS -->
    <link href="{{URL::asset('public/css/DashboardFrontend/bootstrap.min.css')}}" rel="stylesheet">
   
	<!-- ALTERNATIVE COLORS CSS -->
    <link href="#" id="colors" rel="stylesheet">
    <!-- YOUR CUSTOM CSS -->
    <link href="{{URL::asset('public/css/DashboardFrontend/invoice.css')}}" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="{{URL::asset('public/js/DashboardFrontend/jquery-2.2.4.min.js')}}"></script>
	</style>
	<!--<script src="{{URL::asset('public/js/DashboardFrontend/jquery-min.js')}}"></script>	-->
	</head>
	<body>
		<div id="page">		
			<!--Header-->
				
			<main>
			<!--Content-->
				@yield('content') 
			</main>
			<!-- /main -->
			<!--Footer-->
				
		</div>
		<script src="{{URL::asset('public/js/DashboardFrontend/bootstrap.min.js')}}"></script>
		 <!-- page -->	
		<script>
		var site_url = "<?php echo URL::to('/'); ?>"; 
		function print() {
        var objFra = document.getElementById('myFrame');
        objFra.contentWindow.focus();
        objFra.contentWindow.print();
    }
	
	$(document).delegate('.print_invoice', "click", function () {
			var val = $(this).attr('dataid');
			$('#pdfmodel').modal('show');
		
					 $("#pdfmodel .modal-body iframe").attr('src', site_url+'/invoice/print/'+val) // create an iframe
         
		});
		</script>
	</body>
</html>