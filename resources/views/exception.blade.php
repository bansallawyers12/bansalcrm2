<!DOCTYPE html>
<html lang="en">
	<head>
		<base href="./">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
		<meta name="description" content="ApnaMentor for higher education">
		<meta name="author" content="Raghav Garg">
		<meta name="keyword" content="Bootstrap,Admin,Template,Open,Source,jQuery,CSS,HTML,RWD,Dashboard">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<title>Tour Planner | Exception</title>
		
		<!-- Load jQuery synchronously before any other scripts to ensure availability -->
		<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    
		<!-- Icons-->
			<!-- Removed broken references: @coreui/icons, flag-icon-css, simple-line-icons (not installed) -->
			<link rel="stylesheet" type="text/css" href="{{asset('icons/font-awesome/css/all.min.css')}}" />
		
	<!-- Main styles for this application-->
		<link rel="stylesheet" type="text/css" href="{{asset('css/style.css')}}" />
		<link rel="stylesheet" type="text/css" href="{{asset('css/pace.min.css')}}" />
		<link rel="stylesheet" type="text/css" href="{{asset('css/admin.css')}}" />
</head>
	<body class="app flex-row align-items-center">
		<div id="loader">
			<div class="loading_image">
				<div class="valid">
					<img src="{{asset('img/loading.gif') }}">
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-8">
					@include('Elements/flash-message')
					<div class="card-group">
						<div class="card p-4">
							{!! Form::open(array('url' => '/exception', 'name'=>'exception'))  !!}
								<div class="card-body">
									<h1>Exception</h1>
									<div class="input-group mb-3">
										<textarea class="form-control" name="comment" placeholder="Please write comment, what did you face." data-valid="required"></textarea>	
									</div>
									<div class="row">
										<div class="col-6">
											{!! Form::button('Post', ['class'=>'btn btn-primary px-4', 'onClick'=>'customValidate("exception")'])  !!}	
										</div>
									</div>
								</div>
							{!! Form::close()  !!}
						</div>
						<div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%">
							<div class="card-body text-center">
								<div>
									<p>Please write your comment, if you are seeing this page.</p>
									<p>This page occur because you are facing any issue, so please write your comment what your are exactly facing, so we can resolve as soon as possible</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	
	<!-- Load jQuery FIRST as separate entry (synchronous) -->
	@vite(['resources/js/jquery-init.js'])
	
	<!-- Then load main app with Bootstrap, etc (async) -->
	@vite(['resources/js/app.js'])
	
	<!-- jQuery should now be available immediately -->
	<!-- Bootstrap is already loaded via Vite (app.js -> bootstrap.js), no need for duplicate bundle -->
	<!-- Popper.js is already included in Bootstrap 5 bundle via Vite -->
	
	<!-- Apnamentor necessary plugins-->
		<script src="{{asset('js/pace-progress/js/pace.min.js')}}"></script>
		<script src="{{asset('js/perfect-scrollbar/js/perfect-scrollbar.min.js')}}"></script>
		<!-- Removed broken reference: icons/@coreui/coreui-pro/js/coreui.min.js (not installed) -->
		<script src="{{asset('js/custom-form-validation.js')}}"></script>
	</body>
</html>