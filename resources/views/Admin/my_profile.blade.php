@extends('layouts.admin')
@section('title', 'My Profile')
@section('content')

<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="custom-error-msg"></div>

			{!! Form::open(array('url' => 'my_profile', 'name'=>"my-profile", 'enctype'=>'multipart/form-data'))  !!}
			{!! Form::hidden('id', $fetchedData->id)  !!}

			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>My Profile</h4>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-12 col-md-6 col-lg-6">
									<div class="form-group">
										<label for="first_name">First Name <span style="color:#ff0000;">*</span></label>
										{!! Form::text('first_name', @$fetchedData->first_name, array('class' => 'form-control', 'data-valid'=>'required'))  !!}
										@if ($errors->has('first_name'))
											<span class="custom-error" role="alert"><strong>{{ $errors->first('first_name') }}</strong></span>
										@endif
									</div>
									<div class="form-group">
										<label for="last_name">Last Name</label>
										{!! Form::text('last_name', @$fetchedData->last_name, array('class' => 'form-control'))  !!}
										@if ($errors->has('last_name'))
											<span class="custom-error" role="alert"><strong>{{ $errors->first('last_name') }}</strong></span>
										@endif
									</div>
									<div class="form-group">
										<label for="email">Email <span style="color:#ff0000;">*</span></label>
										{!! Form::text('email', @$fetchedData->email, array('class' => 'form-control', 'data-valid'=>'required email', 'autocomplete'=>'off','placeholder'=>'Enter email address'))  !!}
										@if ($errors->has('email'))
											<span class="custom-error" role="alert"><strong>{{ $errors->first('email') }}</strong></span>
										@endif
									</div>
									<div class="form-group">
										<label for="phone">Phone <span style="color:#ff0000;">*</span></label>
										{!! Form::text('phone', @$fetchedData->phone, array('class' => 'form-control mask', 'data-valid'=>'required', 'placeholder'=>'000-000-0000'))  !!}
										@if ($errors->has('phone'))
											<span class="custom-error" role="alert"><strong>{{ $errors->first('phone') }}</strong></span>
										@endif
									</div>
									<div class="form-group">
										<label for="country_code">Country Code</label>
										{!! Form::text('country_code', @$fetchedData->country_code, array('class' => 'form-control', 'placeholder'=>'e.g. +61'))  !!}
									</div>
									<div class="form-group">
										{!! Form::button('@icon('edit') Update', ['class'=>'btn btn-primary px-4', 'onClick'=>'customValidate("my-profile")'])  !!}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			{!! Form::close()  !!}
		</div>
	</section>
</div>
@endsection
