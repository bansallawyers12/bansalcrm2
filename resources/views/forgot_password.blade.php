@extends('layouts.dashboard_frontend')
@section('title', 'Forgot Password')
@section('content')
<div class="row">
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
		<!-- Flash Message Start -->
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
		<!-- Flash Message End -->
	
		<!-- Login Start -->
			<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
			</div>	
			<div class="col-lg-5 col-sm-5 col-md-5 col-xs-12 no-padding">
				<div class="form-box login-form-box col-lg-12 col-sm-12 col-md-12 col-xs-12 no-padding">
					<div class="form-top  col-lg-12 col-sm-12 col-md-12 col-xs-12">
						<div class="form-top-left ">
							<h3>Forgot Password</h3>
							<p>Enter Email to get reset password link.</p>
						</div>
						<div class="form-top-right">
							<i class="fa fa-lock"></i>
						</div>
					</div>
					<div class="form-bottom  col-lg-12 col-sm-12 col-md-12 col-xs-12">
						{{ Form::open(array('url' => '/forgot_password', 'name'=>"forgot_password", 'autocomplete'=>'off', 'class'=>'forgot-password-form')) }}
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								<input type="text" placeholder="Email*" class="form-mobile form-control" name="email" autocomplete="new-password" data-valid="required email" />

								@if ($errors->has('email'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email') }}</strong>
									</span>
								@endif
							</div>

							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-right">	
								<a href="{{URL::to('/login')}}" class="text-muted ml-1 remember-me-text"><strong>>> Back to Login</strong></a>
							</div>
							
							
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{{ Form::button('Send', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("forgot_password")']) }}
							</div>
						{{ Form::close() }}	
					</div>
				</div>
			</div>
		<!-- Login End -->
	</div>
</div>
@endsection