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
	<meta http-equiv="Content-Security-Policy" content="script-src 'self' 'unsafe-inline' 'unsafe-eval' https: http://localhost:5173 http://127.0.0.1:5173 ws://localhost:5173 ws://127.0.0.1:5173 https://cdn.jsdelivr.net; script-src-attr 'unsafe-inline' 'unsafe-hashes'; script-src-elem 'self' 'unsafe-inline' 'unsafe-eval' https: http://localhost:5173 http://127.0.0.1:5173 ws://localhost:5173 ws://127.0.0.1:5173 https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https: http://localhost:5173 http://127.0.0.1:5173 https://cdn.jsdelivr.net; connect-src 'self' ws://localhost:5173 ws://127.0.0.1:5173 http://localhost:5173 http://127.0.0.1:5173 https://maps.googleapis.com;">
	<!-- Note: IPv6 literals [::1] are NOT supported by CSP spec. Use 'localhost' which resolves to both IPv4 and IPv6. -->
	<title>Bansal CRM | @yield('title')</title>
	
	<!-- Load jQuery synchronously before any other scripts to ensure availability -->
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	<link rel="icon" type="image/png" href="{{asset('img/favicon.png')}}">
	<!-- CSS for libraries now loaded via Vite (vendor-libs.js): iziToast, flatpickr, select2, intlTelInput -->
	<!-- FullCalendar v6 CSS is now loaded automatically via JavaScript -->
	<!-- TinyMCE - No CSS needed -->
	<link rel="stylesheet" href="{{asset('css/bootstrap-timepicker.min.css')}}">
	<!-- Template CSS -->
	<!--<link rel="stylesheet" href="{{--asset('css/niceCountryInput.css')--}}">-->
	<!--<link rel="stylesheet" href="{{--asset('css/flagstrap.css')--}}">-->
  
	<link rel="stylesheet" href="{{asset('css/bootstrap-formhelpers.min.css')}}">
	<!-- intlTelInput CSS now loaded via Vite (vendor-libs.js) -->
  
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
				
			@include('../Elements/Admin/footer')
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
	
	<!-- Load vendor libraries (flatpickr, select2, datatables, izitoast, intl-tel-input) -->
	@vite(['resources/js/vendor-libs.js'])
	
	<!-- Load UI libraries (feather-icons, jquery.nicescroll) -->
	@vite(['resources/js/ui-libs.js'])
	
	<!-- Then load main app with Bootstrap, etc -->
	@vite(['resources/js/app.js'])
	 
	<!--<script src="{{--asset('js/niceCountryInput.js')--}}"></script> -->  
	<!-- Bootstrap is already loaded via Vite (app.js -> bootstrap.js), no need for duplicate bundle -->
	<!-- Feather Icons and jQuery NiceScroll now loaded via Vite (ui-libs.js) -->
	<!-- FullCalendar v6 now loaded via Vite (fullcalendar-init.js) -->
	<!-- DataTables, flatpickr, select2, iziToast, intlTelInput now loaded via Vite (vendor-libs.js) -->
  
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
	<!-- Modern Search JS -->
	<script src="{{asset('js/modern-search.js')}}" defer></script> 
	
	<!-- Legacy initialization now loaded via Vite (legacy-init.js) -->
	@vite(['resources/js/legacy-init.js'])
	
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
				<form method="post" name="checkinmodalsave" id="checkinmodalsave" action="{{URL::to('/admin/checkin')}}" autocomplete="off" enctype="multipart/form-data">
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
				<a style="margin-left:10px;" href="javascript:;"><i class="fa fa-trash"></i> Archive</a>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showchecindetail">
				
			</div>
		</div>
	</div>
</div>
@yield('scripts')	
	<!--<script src="{{--asset('js/custom-chart.js')--}}"></script>-->  
</body>
</html>