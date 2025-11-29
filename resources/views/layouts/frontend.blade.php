<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Google tag (gtag.js) --> <script async src="https://www.googletagmanager.com/gtag/js?id=G-HTPCSH8PY5"></script> <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'G-HTPCSH8PY5'); </script>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-WDJK53X');</script>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   @yield('seoinfo') 
    
	<!--<title>@yield('title')</title>-->
	<!-- Favicons-->
    <link rel="shortcut icon" href="{{asset('public//img/logo_img')}}/<?php echo @\App\ThemeOption::where('meta_key','fav_icon')->first()->meta_value; ?>" type="image/x-icon">
   <!-- <link rel="apple-touch-icon" type="image/x-icon" href="{{asset('public/img/Frontend/img/apple-touch-icon-57x57-precomposed.png')}}">-->

 <!-- GOOGLE WEB FONT -->
    
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- BASE CSS -->
    <link href="{{asset('public/css/Frontend/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/css/Frontend/owl.carousel.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/css/Frontend/animate.css')}}" rel="stylesheet">
    <link href="{{asset('public/css/Frontend/magnific-popup.css')}}" rel="stylesheet">
    <link href="{{asset('public/css/Frontend/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/css/Frontend/ETmodules.css')}}" rel="stylesheet">
	<!--<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.standalone.min.css" rel="stylesheet"/>-->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <!--<link href="{{asset('public/css/Frontend/jquery-ui.css')}}" rel="stylesheet">-->
    <link href="{{asset('public/css/Frontend/custom-icon.css')}}" rel="stylesheet">
    <link href="{{asset('public/css/Frontend/classy-nav.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/css/Frontend/style.css')}}" rel="stylesheet">
    <link href="{{asset('public/css/Frontend/custom.css')}}" rel="stylesheet"> 
    <link href="{{asset('public/css/Frontend/responsive.css')}}" rel="stylesheet"> 
    <meta name="msvalidate.01" content="9E8B3D290F7B32155E3FF82B7BB9636E" />
	
	<!--<script src="{{asset('public/js/Frontend/jquery-min.js')}}"></script>-->	
	</head>
	<body>
      <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WDJK53X"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<div class="main_wrapper"> 
			<!--<div id="preloader">
				<i class="circle-preloader"></i>
			</div>-->
			<!--Header-->
				@include('../Elements/Frontend/header')
			<main>
			<!--Content-->
				@yield('content') 
			</main>
			<!-- /main -->
			<!--Footer-->
				@include('../Elements/Frontend/footer')
		 <!-- page -->	
		</div> 
		
		<!-- COMMON SCRIPTS -->
		<script type="text/javascript">
			var site_url = "<?php echo URL::to('/'); ?>";
			var redirecturl = "<?php echo URL::to('/thanks'); ?>";
		</script>
		 
		<script src="{{asset('public/js/Frontend/jquery-2.2.4.min.js')}}"></script>
		<script src="{{asset('public/js/Frontend/popper.min.js')}}"></script> 
		<script src="{{asset('public/js/Frontend/bootstrap.min.js')}}"></script> 
		<script src="{{asset('public/js/Frontend/plugins.js')}}"></script> 
		<script src="{{asset('public/js/Frontend/active.js')}}"></script> 
		<script src="{{asset('public/js/Frontend/jquery.min.js')}}"></script>
		<script src="{{asset('public/js/Frontend/jquery-migrate.min.js')}}"></script>
		<script src="{{asset('public/js/Frontend/bootstrap.bundle.min.js')}}"></script>
		<script src="{{asset('public/js/moment.min.js')}}"></script>
		<!--<script src="{{asset('public/js/Frontend/jquery-ui.js')}}"></script>
		<script src="{{asset('public/js/bootstrap-datepicker.js')}}"></script>-->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
		<script src="{{asset('public/js/Frontend/easing.min.js')}}"></script>
		<script src="{{asset('public/js/Frontend/hoverIntent.js')}}"></script>
		<script src="{{asset('public/js/Frontend/superfish.min.js')}}"></script>
		<script src="{{asset('public/js/Frontend/wow.min.js')}}"></script>
		<script src="{{asset('public/js/Frontend/owl.carousel.min.js')}}"></script>
		<script src="{{asset('public/js/Frontend/magnific-popup.min.js')}}"></script>
		<script src="{{asset('public/js/Frontend/sticky.js')}}"></script>
		<script src="{{asset('public/js/Frontend/main.js')}}"></script>
      
		<!--<script src='https://kit.fontawesome.com/d482948106.js' crossorigin='anonymous'></script>-->
        <script src="{{asset('public/js/Frontend/font_awesome5.js')}}"></script>
      
		<script>
		jQuery(document).ready(function($){
			$('.refresh').on('click', function(){
				$.ajax({
					url: '<?php echo URL::to('/'); ?>/refresh-captcha',
					type: 'GET',
					success: function(html){
						$('.code_verify .image').html(html);
					}
				});
			});
			/* $('.commodate').datepicker({
			   autoclose: true, 
			   inline: true,
			   format: 'yyyy-mm-dd',
			   startDate: "now"
			}); */
			 /* $( "#datepicker" ).datepicker({
				  inline: true,
				showOtherMonths: true,
				selectOtherMonths: true
			}); */
			
		});
		</script>
		@yield('scripts')	
	</body>
</html>