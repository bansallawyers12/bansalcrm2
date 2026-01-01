<!DOCTYPE html>
<html lang="en">
	<head>
		<base href="./">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="">
		<meta name="keyword" content="E-Weblink CRM">
		<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="shortcut icon" type="image/png" href="{{ asset('img/favicon.png') }}"/>
	<title>CRM Digitrex | @yield('title')</title>
	
	<!-- Load jQuery synchronously before any other scripts to ensure availability -->
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	  
	<!-- Font Awesome -->
	  <link rel="stylesheet" href="{{URL::asset('icons/font-awesome/css/all.min.css')}}">
	  <!-- Ionicons -->
	  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
	  <!-- Datatable -->
	  
	  <!-- Select2 -->
	  <link rel="stylesheet" href="{{URL::asset('css/select2.min.css')}}">
	  <link rel="stylesheet" href="{{URL::asset('css/select2-bootstrap4.min.css')}}">
	  <!-- Theme style -->
	  <link rel="stylesheet" href="{{URL::asset('css/admintheme.min.css')}}">
	  <!-- overlayScrollbars -->
	 
	  <link rel="stylesheet" type="text/css" href="{{URL::asset('css/bootstrap-select.min.css')}}" >
	  <!-- TinyMCE -->
	  <!-- No CSS needed for TinyMCE (uses inline styles) --> 
	  <!-- style --> 
	  <link rel="stylesheet" href="{{URL::asset('css/style.css')}}">
	  <link rel="stylesheet" href="{{URL::asset('css/font-awesome.min.css')}}">
	  <link rel="stylesheet" href="{{URL::asset('css/flatpickr.min.css')}}">
	 
	  <!--<link rel="stylesheet" href="{{URL::asset('css/niceCountryInput.css')}}">-->
	  <!-- Google Font: Nunito (standardized across CRM) -->
	  <link rel="dns-prefetch" href="https://fonts.gstatic.com">
	  <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800" rel="stylesheet">
	  
		<script>var billingdata = new Array();</script>	 
		
	<style>
        .upic > img {
            width: 32px;
            height: auto;
            float: left;
        }
        .margin-r-10{
            margin-right:10px
            }
        .margin-r-20{
            margin-right:20px
        }

        .ps_btn {
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            color: #666666;
            padding: 5px 8px;
        }

	.dnone{display:none}
	.f18{font-size:18px;}
	.mt{margin-top:5px;}
	.btn-arrow-right {
	position: relative;
	padding-left: 18px;
	padding-right: 18px;}
	.btn-arrow-right {padding-left: 36px;}
	.btn-arrow-right:before,
	.btn-arrow-right:after{
		content:"";
		position: absolute;
		top: 5px;
		width: 22px;
		height: 22px;
		background: inherit;
		border: inherit;
		border-left-color: transparent;
		border-bottom-color: transparent;
		border-radius: 0px 4px 0px 0px;
		-webkit-border-radius: 0px 4px 0px 0px;
		-moz-border-radius: 0px 4px 0px 0px;}
	.btn-arrow-right:before,
	.btn-arrow-right:after {
		transform: rotate(45deg);
		-webkit-transform: rotate(45deg);
		-moz-transform: rotate(45deg);
		-o-transform: rotate(45deg);
		-ms-transform: rotate(45deg);}
	.btn-arrow-right:before {left: -11px;}
	.btn-arrow-right:after {right: -11px;}
	.btn-arrow-right:after { z-index: 1;}
	.btn-arrow-right:before{ background-color: white;}
	.text-ellipsis{white-space: nowrap; text-overflow: ellipsis; overflow: hidden;}
	

	.dispM{display:block}
	.ww40{width:40%}
	.logo{display:block}
	.sear01{width:60%}
	.lh24{line-height:24px}.lh28{line-height:28px}.lin_drp a{color:#333}
	
	@media only screen and (max-width:479px)
        {
            .dispM{display:none}
            .ww40{width:100%}
			.logo{display:none!important}
			.sear01{width:100%; border:none;-webkit-box-shadow:none}
		}
		
        blockquote {
            font-size : 14px; 
        }
        .popover {max-width:700px;}
        .selec_reg{background-color:#f4f4f4; border:1px solid #ddd; color:#444; border-radius: 3px; font-size:12px}
        .selec_reg option{background-color:#fff; color:#444; padding:5px; cursor:pointer;}
        .f13{font-size:13px}
        .attch_downl a{width:270px; display:block; float:left; margin-bottom:8px; margin-right:20px}
        @font-face {
            font-family: 'Material Icons';
            font-style: normal;
            font-weight: 400;
            src: local('Material Icons'), local('MaterialIcons-Regular'), url(https://fonts.gstatic.com/s/materialicons/v21/2fcrYFNaTjcS6g4U3t-Y5ZjZjT5FdEJ140U2DJYC3mY.woff2) format('woff2');
        }

        .material-icons {
            font-family: 'Material Icons';
            -moz-font-feature-settings: 'liga';
            -moz-osx-font-smoothing: grayscale;
        }
        .qr_btn{padding:2px 10px 3px; border-radius:15px; cursor:pointer}
        
        /* Override AdminLTE Source Sans Pro with Nunito for consistency */
        :root {
            --font-family-sans-serif: "Nunito", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }
        body, .wrapper, html {
            font-family: "Nunito", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        }
    
    </style>	
		   
	</head>
	<body class="hold-transition sidebar-mini layout-fixed loderover">
	
		<div class="wrapper">
		<div id="loader">
			<div class="overlay">
              <i class="fa fa-refresh fa-spin"></i>
            </div> 
        </div>
			<!--Header-->
			@include('../Elements/Admin/header')
		
			<!--Content-->
			<div class="app-body">
				<!--Left Side Bar-->
				@include('../Elements/Admin/left-side-bar')
				
				@yield('content')
				
			</div>
			<!--Footer-->
			@include('../Elements/Admin/footer')
		</div>	
		<div class="modal fade" id="leadsearch_modal">
			<div class="modal-dialog modal-lg">
			  <div class="modal-content">
				<div class="modal-header">
				  <h4 class="modal-title">Lead Search</h4>
				  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
				</div>
				<form action="{{route('admin.leads.index')}}" method="get">
				<div class="modal-body"> 
					
						<div class="row">
							<div class="col-md-6">
								<div class="form-group row">
									<label for="lead_id" class="col-sm-2 col-form-label">Lead ID</label>
									<div class="col-sm-10">
										{!! Form::text('lead_id', Request::get('lead_id'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Lead ID', 'id' => 'lead_id' ))  !!}	 						
										@if ($errors->has('lead_id'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('lead_id') }}</strong>
											</span> 
										@endif
								   </div>	
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group row">
									<label for="name" class="col-sm-2 col-form-label">Name</label>
									<div class="col-sm-10">
										{!! Form::text('name', Request::get('name'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Name', 'id' => 'name' ))  !!}	 						
										@if ($errors->has('name'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('name') }}</strong>
											</span> 
										@endif
								   </div>	
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group row">
									<label for="email" class="col-sm-2 col-form-label">Email</label>
									<div class="col-sm-10">
										{!! Form::text('email', Request::get('email'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Email', 'id' => 'email' ))  !!}	 						
										@if ($errors->has('email'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('email') }}</strong>
											</span> 
										@endif
								   </div>	
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group row">
									<label for="phone" class="col-sm-2 col-form-label">Phone</label>
									<div class="col-sm-10">
										{!! Form::text('phone', Request::get('phone'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Phone', 'id' => 'phone' ))  !!}	 						
										@if ($errors->has('phone'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('phone') }}</strong>
											</span> 
										@endif
								   </div>	
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group row">
									<label for="followupdate" class="col-sm-2 col-form-label">Followup Date</label>
									<div class="col-sm-10">
										{!! Form::text('followupdate', Request::get('followupdate'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Followup Date', 'id' => 'followupdate' ))  !!}	 						
										@if ($errors->has('followupdate'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('followupdate') }}</strong>
											</span> 
										@endif
								   </div>	
								</div>
							</div>
							
							
						</div>
					
				</div>
				<div class="modal-footer justify-content-between">
				  <a href="{{route('admin.leads.index')}}" class="btn btn-default" >Reset</a>
				  <button type="submit" id="" class="btn btn-primary">Search</button>
				</div>
				</form>	
			  </div>
			  <!-- /.modal-content -->
			</div>
		<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->	
		<div class="customer_support">
			<a href="javascript:;" data-bs-toggle="modal" data-bs-target="#contactsupport_modal" class="btn btn-primary"><i class="fa fa-envelope"></i> Contact Support</a>
		</div>
		<div class="modal fade" id="contactsupport_modal">
			<div class="modal-dialog modal-lg">
			  <div class="modal-content">
				<div class="modal-header">
				  <h4 class="modal-title">At Your Service</h4>
				  <p>Responses to this email will be sent to info@eweblink.net</p>
				  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
				</div>
				<div class="modal-body">
					<form action="" method="post">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="subject" class="col-form-label">Subject</label>
									{!! Form::text('subject', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Subject', 'id' => 'subject' ))  !!}	 						
									@if ($errors->has('subject'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('subject') }}</strong>
										</span> 
									@endif
								</div>
								<div class="form-group">
									<label for="how_help_you" class="col-form-label">How can we help you today?</label>
									{!! Form::text('how_help_you', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'How can we help you today?', 'id' => 'how_help_you' ))  !!}	 						
									@if ($errors->has('how_help_you'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('how_help_you') }}</strong>
										</span> 
									@endif
								</div>
								<div class="form-group">
									<label for="attach_file" class="col-form-label">Attachments <i class="fa fa-explanation"></i></label>
									<input type="file" name="attach_file" class="" autocomplete="off" data-valid="" style="display:block;" />
									@if ($errors->has('attach_file'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('attach_file') }}</strong>
										</span> 
									@endif
								</div>
								<div class="form-group">
									<label for="contact_no" class="col-form-label">Contact Number</label>
									{!! Form::text('contact_no', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Contact Number', 'id' => 'contact_no' ))  !!}	 						
									@if ($errors->has('contact_no'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('contact_no') }}</strong>
										</span> 
									@endif
								</div>
								<div class="form-group">
									<label for="critical_request" class="col-form-label">How critical is your request?</label>
									<select name="critical_request" data-valid="required" id="critical_request" class="form-control" autocomplete="new-password">
										<option value="None">None</option>
										<option value="Just FYI">Just FYI</option>
										<option value="Nothing urgent, can wait">Nothing urgent, can wait</option>
										<option value="I'm stuck, need assistance">I'm stuck, need assistance</option>
									</select>	 						
									@if ($errors->has('critical_request'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('critical_request') }}</strong>
										</span> 
									@endif
								</div>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer justify-content-between">
				  <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
				  <button type="button" id="support_save" class="btn btn-primary">Save</button>
				</div>
			  </div>
			  <!-- /.modal-content -->
			</div>
		<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->
		
		<!-- Load jQuery FIRST as separate entry (synchronous) -->
		@vite(['resources/js/jquery-init.js'])
		
		<!-- Then load main app with Bootstrap, etc (async) -->
		@vite(['resources/js/app.js'])
		
		<!-- jQuery should now be available immediately -->
		
		<!-- Flatpickr -->
		<script src="{{URL::asset('js/flatpickr.min.js')}}"></script> 
		<!-- Bootstrap is already loaded via Vite (app.js -> bootstrap.js), no need for duplicate bundle -->	
		<!-- Datatable  -->
		
		<!-- Select2 -->		
		<!--<script src="{{URL::asset('js/select2.full.min.js')}}"></script>	-->
		<!-- Select2 -->		
		<script src="{{URL::asset('js/select2.min.js')}}"></script>			
		<!-- daterangepicker -->
		
		<!-- TinyMCE scripts loaded conditionally via @push('tinymce-scripts') on pages that need it -->
		@stack('tinymce-scripts')
		
		<!-- Admin Theme App -->
		<script src="{{URL::asset('js/admintheme.min.js')}}"></script>
		
		<!-- Admin Theme dashboard demo (This is only for demo purposes) -->
	
		
		<script src="{{URL::asset('js/custom-form-validation.js')}}"></script>
		  
		
	<script type="text/javascript">
		var site_url = "<?php echo URL::to('/'); ?>"; 
		var followuplist = "<?php echo URL::to('/'); ?>";
		var followupstore = "<?php echo URL::to('/admin/followup/store'); ?>";
	</script>
		<!--<script async src="https://app.appzi.io/bootstrap/bundle.js?token=unZ6A"></script><div id="zbwid-3c79022e"></div>-->
		
		

		<script>
	
        
    $(function () {
		if (typeof flatpickr !== 'undefined') {
			// Helper functions for duration calculation
			function append(dtTxt, ddTxt) {
				$('input[name="duration"]').val(dtTxt);
			}
			function retappend(dtTxt, ddTxt) {
				$('input[name="ret_duration"]').val(dtTxt);
			}
			function retcalduration(d1,d2){
				if(d2 != ''){
					var msec = d2 - d1;
					var mins = Math.floor(msec / 60000);
					var hrs = Math.floor(mins / 60);
					var days = Math.floor(hrs / 24);
					var yrs = Math.floor(days / 365);
					mins = mins % 60;
					retappend(hrs + "h " + mins + "m");
				}
			}
			function calduration(d1,d2){
				if(d2 != ''){
					var msec = d2 - d1;
					var mins = Math.floor(msec / 60000);
					var hrs = Math.floor(mins / 60);
					var days = Math.floor(hrs / 24);
					var yrs = Math.floor(days / 365);
					mins = mins % 60;
					append(hrs + "h " + mins + "m");
				}
			}

			// DateTime pickers with 15-minute increments
			var datetimeOptions = {
				enableTime: true,
				dateFormat: "Y-m-d H:i",
				minDate: "today",
				minuteIncrement: 15,
				time_24hr: false,
				allowInput: true
			};

			// Departure time
			if ($('#deptime').length) {
				flatpickr('#deptime', $.extend({}, datetimeOptions, {
					onChange: function(selectedDates, dateStr, instance) {
						var date1 = new Date($('#deptime').val());
						var date2 = new Date($('#artime').val());
						calduration(date1, date2);
					}
				}));
			}

			// Arrival time
			if ($('#artime').length) {
				flatpickr('#artime', $.extend({}, datetimeOptions, {
					onChange: function(selectedDates, dateStr, instance) {
						var date1 = new Date($('#deptime').val());
						var date2 = new Date($('#artime').val());
						calduration(date1, date2);
					}
				}));
			}

			// Return departure time
			if ($('#retdeptime').length) {
				flatpickr('#retdeptime', $.extend({}, datetimeOptions, {
					onChange: function(selectedDates, dateStr, instance) {
						var date1 = new Date($('#retdeptime').val());
						var date2 = new Date($('#retartime').val());
						retcalduration(date1, date2);
					}
				}));
			}

			// Return arrival time
			if ($('#retartime').length) {
				flatpickr('#retartime', $.extend({}, datetimeOptions, {
					onChange: function(selectedDates, dateStr, instance) {
						var date1 = new Date($('#retdeptime').val());
						var date2 = new Date($('#retartime').val());
						retcalduration(date1, date2);
					}
				}));
			}

			// Date only picker
			if ($('#ardate').length) {
				flatpickr('#ardate', {
					dateFormat: "Y-m-d",
					minDate: "today",
					allowInput: true
				});
			}
		}
	 //Initialize Select2 Elements
		$('.select2_name, .select2_source, .select2_destination').select2({
		  theme: 'bootstrap4'
		}); 
		// TinyMCE initialization is handled in tinymce-init.js
		// Summernote has been replaced with TinyMCE
			
    }); 

		</script>   
									
	</body>
</html> 