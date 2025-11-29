@extends('layouts.dashboard_frontend')
@section('title', 'Reset Password')
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
							<h3>Reset Password</h3>
							<p>Please enter the below fields to change the password.</p>
						</div>
						<div class="form-top-right">
							<i class="fa fa-lock"></i>
						</div>
					</div>
					<div class="form-bottom  col-lg-12 col-sm-12 col-md-12 col-xs-12">
						{{ Form::open(array('url' => '/reset_link', 'name'=>"reset_link", 'autocomplete'=>'off', 'class'=>'reset-link-form')) }}
							{{ Form::hidden('id', @$data->id) }}
							{{ Form::hidden('email', @$data->email) }}
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								<input type="password" placeholder="New Password*" class="form-mobile form-control" name="password" autocomplete="new-password" data-valid="required" />

								@if ($errors->has('password'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('password') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								<input type="password" placeholder="Confirm Password*" class="form-mobile form-control" name="password_confirmation" autocomplete="new-password" data-valid="required" />

								@if ($errors->has('password_confirmation'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('password_confirmation') }}</strong>
									</span>
								@endif
							</div>

							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{{ Form::button('Reset', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("reset_link")']) }}
							</div>
						{{ Form::close() }}	
					</div>
				</div>
			</div>
		<!-- Login End -->
	</div>
</div>
@endsection