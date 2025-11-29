@extends('layouts.dashboard_frontend')
@section('title', @$seoDetails->meta_title)
@section('meta_title', @$seoDetails->meta_title)
@section('meta_keyword', @$seoDetails->meta_keyword)
@section('meta_description', @$seoDetails->meta_desc)
@section('content')
<div class="row">
	<!-- {{dd('jijiijij')}} -->
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
		<!-- Flash Message Start -->
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
		<!-- Flash Message End -->
	
		<!-- Login Start -->
			<div class="col-lg-5 col-sm-5 col-md-5 col-xs-12 no-padding">
				<div class="form-box login-form-box col-lg-12 col-sm-12 col-md-12 col-xs-12 no-padding">
					<div class="form-top  col-lg-12 col-sm-12 col-md-12 col-xs-12">
						<div class="form-top-left ">
							<h3>Sign-in</h3>
							<p>Enter Mobile/Email and password to log on</p>
						</div>
						<div class="form-top-right">
							<i class="fa fa-lock"></i>
						</div>
					</div>
					<div class="form-bottom  col-lg-12 col-sm-12 col-md-12 col-xs-12">
						{{ Form::open(array('url' => '/login', 'name'=>"login", 'autocomplete'=>'off', 'class'=>'login-form')) }}
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								<input type="text" placeholder="Email / Mobile*" class="form-mobile form-control" name="email" value="{{ (Cookie::get('email') !='' && !old('email')) ? Cookie::get('email') : old('email')  }}" autocomplete="new-password" data-valid="required" />

								@if ($errors->has('email'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								<input type="password" placeholder="Password" class="form-control" name="password" value="{{ (Cookie::get('password') !='' && !old('password')) ? Cookie::get('password') : old('password')  }}" autocomplete="new-password" data-valid="required" />
								
								@if ($errors->has('password'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('password') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12">	
								<input class="ml-1" type="checkbox" name="remember" id="remember" @if(Cookie::get('email') != '' && Cookie::get('password') != '') checked  @endif>
								<span class="text-muted ml-1 remember-me-text">{{ __('Remember Me') }}</span>
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12">	
								<a href="{{URL::to('/forgot_password')}}" class="text-muted ml-1 remember-me-text"><strong>Forgot Password ?</strong></a>
							</div>
							
							
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{{ Form::button('Sign in!', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("login")']) }}
							</div>
							
							<!--<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								<a class="common-btn btn-facebook" href="{{url('/auth/facebook')}}">
									<i class="fa fa-facebook"></i> Sign Up with Facebook
								</a>
								<a class="common-btn btn-google" href="{{url('/auth/google')}}">
									<i class="fa fa-google-plus"></i> Sign Up with Google Plus
								</a>
							</div>-->
						{{ Form::close() }}	
					</div>
				</div>
			</div>
		<!-- Login End -->
		
		<div class="col-sm-1 middle-border"></div>
		<div class="col-sm-1"></div>
		
		<!-- Signup Start -->
			<div class="col-lg-5 col-sm-5 col-md-5 col-xs-12 no-padding">
				<div class="form-box col-lg-12 col-sm-12 col-md-12 col-xs-12 no-padding">
					<div class="form-top  col-lg-12 col-sm-12 col-md-12 col-xs-12 ">
						<div class="form-top-left">
							<h3>Sign up now</h3>
							<p>Fill in the form below to get instant access.</p>
						</div>
						<div class="form-top-right">
							<i class="fa fa-pencil"></i>
						</div>
					</div>
					<div class="form-bottom  col-lg-12 col-sm-12 col-md-12 col-xs-12">
						{{ Form::open(array('url' => '/register', 'name'=>"register", 'autocomplete'=>'off', 'class'=>'registration-form')) }}
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{{ Form::text('first_name', '', array('class' => 'form-name form-control', 'data-valid'=>'required', 'placeholder'=>'First Name*', 'autocomplete'=>'new-password')) }}
							
								@if ($errors->has('first_name'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('first_name') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{{ Form::text('last_name', '', array('class' => 'form-name form-control', 'data-valid'=>'required', 'placeholder'=>'Last Name*', 'autocomplete'=>'new-password')) }}
							
								@if ($errors->has('last_name'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('last_name') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{{ Form::text('email_register', '', array('class' => 'form-name form-control', 'data-valid'=>'required email', 'placeholder'=>'Email*', 'autocomplete'=>'new-password')) }}
							
								@if ($errors->has('email_register'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_register') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{{ Form::password('password_register', array('class' => 'form-name form-control', 'data-valid'=>'required min-6 max-12', 'placeholder'=>'Password*', 'autocomplete'=>'new-password')) }}
							
								@if ($errors->has('password_register'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('password_register') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-6 col-sm-6 col-md-6 col-xs-12">
								{{ Form::text('phone_register', '', array('class' => 'form-name form-control mask', 'data-valid'=>'required equal-10', 'placeholder'=>'Mobile*', 'autocomplete'=>'new-password')) }}
							
								@if ($errors->has('phone_register'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('phone_register') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-6 col-sm-6 col-md-6 col-xs-12">
								{{ Form::text('course_level', '', array('class' => 'form-name form-control', 'placeholder'=>'Course Level', 'autocomplete'=>'new-password')) }}
							
								@if ($errors->has('course_level'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('course_level') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12">
								<select name="country" class="form-name form-control" data-valid="required">
									<option value="{{ @$country->id }}">{{ @$country->name }}</option>		
								</select>
								
								@if ($errors->has('country'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('country') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12">	
								<select name="state" class="form-control" data-valid="required">
									<option value="">Choose State</option>
									@if(count(@$states) !== 0)
										@foreach (@$states as $state)
											<option value="{{ $state->id }}">{{ $state->name }}</option>
										@endforeach
									@endif
								</select>
							
								@if ($errors->has('state'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('state') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{{ Form::text('city', '', array('class' => 'form-name form-control', 'placeholder'=>'City', 'data-valid'=>'required', 'autocomplete'=>'new-password')) }}
							
								@if ($errors->has('city'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('city') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12">
								{{ Form::textarea('address', '', array('class' => 'form-control textarea', 'placeholder'=>'Please write Your Address...', 'data-valid'=>'required', 'autocomplete'=>'new-password')) }}
							
								@if ($errors->has('address'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('address') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{{ Form::text('zip', '', array('class' => 'form-name form-control', 'placeholder'=>'Zip Code', 'data-valid'=>'required', 'autocomplete'=>'new-password')) }}
							
								@if ($errors->has('zip'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('zip') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{{ Form::button('Sign me up!', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("register")']) }}
							</div>
							<!--<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								<a class="common-btn btn-facebook" href="{{url('/auth/facebook')}}">
									<i class="fa fa-facebook"></i> Sign Up with Facebook
								</a>
								<a class="common-btn btn-google" href="{{url('/auth/google')}}">
									<i class="fa fa-google-plus"></i> Sign Up with Google Plus
								</a>
							</div>-->
						{{ Form::close() }}	
					</div>
				</div>
			</div>
		<!-- Signup End -->	
	</div>
</div>
@endsection