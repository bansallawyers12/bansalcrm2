@extends('layouts.dashboard_frontend')
@section('title', 'Your Shipping Address')
@section('content')
<div class="row">
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
		<!-- Flash Message Start -->
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
		<!-- Flash Message End -->
	
		<!-- Show Address Start -->
			<div class="col-lg-5 col-sm-5 col-md-5 col-xs-12 no-padding">
				<div class="form-box login-form-box col-lg-12 col-sm-12 col-md-12 col-xs-12 no-padding">
					<div class="form-top  col-lg-12 col-sm-12 col-md-12 col-xs-12">
						<div class="form-top-left ">
							<h3>Your Shipping Address</h3>
						</div>
					</div>
					<div class="form-top col-lg-12 col-sm-12 col-md-12 col-xs-12 show-address">
						<div class="float-left">
							<p>
								{{ @$fetchedData->first_name == "" ? config('constants.empty') : @$fetchedData->first_name }}  {{ @$fetchedData->last_name == "" ? config('constants.empty') : @$fetchedData->last_name }}
							<br />
							{{ @$fetchedData->address == "" ? config('constants.empty') : @$fetchedData->address }}
							<br />
							{{ @$fetchedData->city == "" ? config('constants.empty') : @$fetchedData->city }}, {{ @$fetchedData->zip == "" ? config('constants.empty') : @$fetchedData->zip }}
							<br />
							{{ @$fetchedData->state == "" ? config('constants.empty') : @$fetchedData->stateData->name }},  {{ @$fetchedData->country == "" ? config('constants.empty') : @$fetchedData->countryData->name }}
							<br />
							<p><strong>Mobile No :</strong> {{ @$fetchedData->phone == "" ? config('constants.empty') : @$fetchedData->phone }}</p>	
						</div>
					</div>
				</div>
			</div>
		<!-- Show Address End -->
		
		<div class="col-sm-1 middle-border"></div>
		<div class="col-sm-1"></div>
		
		<!-- Address Start -->
			<div class="col-lg-5 col-sm-5 col-md-5 col-xs-12 no-padding">
				<div class="form-box col-lg-12 col-sm-12 col-md-12 col-xs-12 no-padding">
					<div class="form-top  col-lg-12 col-sm-12 col-md-12 col-xs-12 ">
						<div class="form-top-left">
							<h3>Address</h3>
							<p>Fill in the form below to change your address.</p>
						</div>
						<div class="form-top-right">
							<i class="fa fa-pencil"></i>
						</div>
					</div>
					<div class="form-bottom  col-lg-12 col-sm-12 col-md-12 col-xs-12">
						{{ Form::open(array('url' => '/address', 'name'=>"update-address", 'autocomplete'=>'off', 'class'=>'address-form')) }}
						{{ Form::hidden('id', @$fetchedData->id) }}
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{{ Form::text('first_name', @$fetchedData->first_name, array('class' => 'form-name form-control', 'data-valid'=>'required', 'placeholder'=>'First Name*', 'autocomplete'=>'new-password')) }}
							
								@if ($errors->has('first_name'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('first_name') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{{ Form::text('last_name', @$fetchedData->last_name, array('class' => 'form-name form-control', 'data-valid'=>'required', 'placeholder'=>'Last Name*', 'autocomplete'=>'new-password')) }}
							
								@if ($errors->has('last_name'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('last_name') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12">
								<select name="country" id="getCountryStates" class="form-name form-control" data-valid="required">
									<option value="">Choose Country*</option>
									@if(count(@$countries) !== 0)
										@foreach (@$countries as $country)
											<option value="{{ @$country->id }}" @if(@$fetchedData->country == @$country->id) selected  @endif>{{ @$country->name }}</option>
										@endforeach
									@endif		
								</select>
								
								@if ($errors->has('country'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('country') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12">
								<input type="hidden" id="storeStateId" value="{{@$fetchedData->state}}" />	
								<select name="state" id="getStateCities" class="form-control" data-valid="required">
									<option value="">Choose State*</option>		
								</select>
							
								@if ($errors->has('state'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('state') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{{ Form::text('city', @$fetchedData->city, array('class' => 'form-name form-control', 'placeholder'=>'City*', 'autocomplete'=>'new-password', 'data-valid'=>'required')) }}
							
								@if ($errors->has('city'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('city') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12">
								{{ Form::textarea('address', @$fetchedData->address, array('class' => 'form-control textarea', 'placeholder'=>'Please write Your Address...', 'autocomplete'=>'new-password', 'data-valid'=>'required')) }}
							
								@if ($errors->has('address'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('address') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								{{ Form::text('zip', @$fetchedData->zip, array('class' => 'form-name form-control', 'placeholder'=>'Zip Code*', 'autocomplete'=>'new-password', 'data-valid'=>'required')) }}
							
								@if ($errors->has('zip'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('zip') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								<div class="form-group col-lg-4 col-sm-12 col-md-4 col-xs-12 text-center">
								</div>
								<div class="form-group col-lg-4 col-sm-12 col-md-4 col-xs-12 text-center">
									{{ Form::button('Update', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("update-address")']) }}
								</div>
								<div class="form-group col-lg-4 col-sm-12 col-md-4 col-xs-12 text-center">
								</div>
							</div>
						{{ Form::close() }}
							<div class="form-group col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
								<div class="form-group col-lg-3 col-sm-12 col-md-3 col-xs-12 text-center">
									<a href="{{URL::to('/cart')}}" class="btn btn-warning">< Back</a>
								</div>
								<div class="form-group col-lg-2 col-sm-12 col-md-2 col-xs-12 text-center">
								</div>	
								<div class="form-group col-lg-4 col-sm-12 col-md-4 col-xs-12 text-center">
									<a href="{{URL::to('/payment')}}" class="btn btn-success">Proceed <i class="fa fa-angle-right"></i></a>
								</div>
							</div>
					</div>
				</div>
			</div>
		<!-- Address End -->	
	</div>
</div>
@endsection