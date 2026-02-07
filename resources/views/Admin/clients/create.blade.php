@extends('layouts.admin')
@section('title', 'Create Client')

@push('styles')
<style>
/* Form Section Subheading Styles - Matching Create New Page */
.form-section {
    margin-bottom: 16px;
    position: relative;
}

.form-section:last-child {
    margin-bottom: 0;
}

.form-section h3 {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #1e293b !important;
    margin-bottom: 12px !important;
    padding-bottom: 8px !important;
    border-bottom: 2px solid #f1f5f9 !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.3px !important;
    position: relative !important;
}

.form-section h3::after {
    content: '' !important;
    position: absolute !important;
    bottom: -2px !important;
    left: 0 !important;
    width: 50px !important;
    height: 2px !important;
    background: linear-gradient(90deg, #6366f1, #8b5cf6) !important;
    border-radius: 2px !important;
}

.form-section h3 i {
    color: #6366f1 !important;
    font-size: 14px !important;
    background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;
}

/* Compact spacing adjustments */
.form-section:not(:last-child) {
    padding-bottom: 16px;
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: 16px;
}

/* Override any card-header styles that might interfere */
.card-body .form-section h3,
.section-card .form-section h3 {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #1e293b !important;
    margin-bottom: 12px !important;
    padding-bottom: 8px !important;
    border-bottom: 2px solid #f1f5f9 !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.3px !important;
    position: relative !important;
}

.card-body .form-section h3::after,
.section-card .form-section h3::after {
    content: '' !important;
    position: absolute !important;
    bottom: -2px !important;
    left: 0 !important;
    width: 50px !important;
    height: 2px !important;
    background: linear-gradient(90deg, #6366f1, #8b5cf6) !important;
    border-radius: 2px !important;
}

.card-body .form-section h3 i,
.section-card .form-section h3 i {
    color: #6366f1 !important;
    font-size: 14px !important;
    background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;
}

/* Ensure all section cards have consistent sizing and padding - matching create-new page */
.form-content-section {
    background: #ffffff !important;
    padding: 16px !important;
    border-radius: 8px !important;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03) !important;
    border: 1px solid #e2e8f0 !important;
    margin-bottom: 16px !important;
    display: block !important;
    width: 100% !important;
    transition: all 0.2s ease !important;
    position: relative !important;
}

.form-content-section:hover {
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04) !important;
    border-color: #cbd5e1 !important;
}

.form-content-section:last-child {
    margin-bottom: 0 !important;
}

/* Legacy section-card support */
.section-card {
    background: #ffffff !important;
    border-radius: 8px !important;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03) !important;
    border: 1px solid #e2e8f0 !important;
    margin-bottom: 16px !important;
    transition: all 0.2s ease !important;
}

.section-card:hover {
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04) !important;
    border-color: #cbd5e1 !important;
}

.section-card .card-body {
    padding: 16px !important;
}

/* Remove default card styling that might interfere */
.section-card .card-header {
    display: none !important;
}

/* Make content-grid consistent across all sections */
.content-grid {
    display: grid !important;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)) !important;
    gap: 12px !important;
    margin-bottom: 12px !important;
}

/* Naati/PY Checkbox Styling - Matching Create New Page */
.naati-checkbox-wrapper {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    padding: 8px 0;
}

.naati-checkbox-item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    background: #ffffff;
    transition: all 0.2s ease;
    cursor: pointer;
    user-select: none;
    margin: 0;
    position: relative;
}

.naati-checkbox-item:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}

.naati-checkbox-item input[type="checkbox"] {
    width: 18px;
    height: 18px;
    margin: 0;
    cursor: pointer;
    accent-color: #6366f1;
    flex-shrink: 0;
    position: relative;
    z-index: 2;
    pointer-events: auto;
}

.naati-checkbox-label {
    margin: 0;
    cursor: pointer;
    font-size: 13px;
    color: #475569;
    font-weight: 500;
    user-select: none;
    pointer-events: none;
    position: relative;
    z-index: 1;
}

.naati-checkbox-item input[type="checkbox"]:checked ~ .naati-checkbox-label {
    color: #6366f1 !important;
    font-weight: 600 !important;
}

.naati-checkbox-item:has(input[type="checkbox"]:checked),
.naati-checkbox-item.checked {
    border-color: #6366f1 !important;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(139, 92, 246, 0.05)) !important;
}

.naati-checkbox-item.checked .naati-checkbox-label {
    color: #6366f1 !important;
    font-weight: 600 !important;
}
</style>
@endpush

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
	     <div class="server-error">
				@include('../Elements/flash-message')
			</div>
		<div class="section-body">
			{!! Form::open(array('url' => 'clients/store', 'name'=>"add-clients", 'id' => 'create-client-form', 'autocomplete'=>'off', "enctype"=>"multipart/form-data", 'data-check-url' => URL::to('checkclientexist'), 'data-recipients-url' => URL::to('/clients/get-recipients')))  !!}
				<input type="hidden" name="type" value="client"> 
				<!-- Page Header -->
				<div class="row">   
					<div class="col-12">
						<div class="card client-edit-header">
							<div class="card-header d-flex justify-content-between align-items-center">
								<div class="header-title-section">
									<h4 class="mb-1">
										<i class="fas fa-user-plus text-primary"></i> 
										Create Client
									</h4>
								</div>
								<div class="card-header-action">
								    <a href="{{route('clients.index')}}" class="btn btn-outline-secondary me-2">
								    	<i class="fa fa-arrow-left"></i> Back
								    </a>
								    <button type="submit" class="btn btn-primary" onclick="customValidate('add-clients')">
								    	<i class="fas fa-save"></i> Create Client
								    </button>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Section 1: Basic Information -->
				<div class="row mt-3">
					<div class="col-12">
						<div class="card section-card">
							<div class="card-body">
								<section class="form-section">
									<h3><i class="fas fa-id-card"></i> Basic Information</h3>
								<div class="row">
									<!--<div class="col-3 col-md-3 col-lg-3">
								    	<div class="form-group profile_img_field">	
											<input type="hidden" id="old_profile_img" name="old_profile_img" value="{{@$fetchedData->profile_img}}" />
											<div class="profile_upload">
												<div class="upload_content">
													@if(@$fetchedData->profile_img != '')
														<img src="{{asset('img/profile_imgs')}}/{{@$fetchedData->profile_img}}" style="width:100px;height:100px;" id="output"/> 
													@else
														<img id="output"/> 
													@endif
														<i <?php if(@$fetchedData->profile_img != ''){ echo 'style="display:none;"'; } ?> class="fa fa-camera if_image"></i>
														<span <?php if(@$fetchedData->profile_img != ''){ echo 'style="display:none;"'; } ?> class="if_image">Upload Profile Image</span>
													</div>
													<input onchange="loadFile(event)" type="file" id="profile_img" name="profile_img" class="form-control" autocomplete="off" />
											</div>	
											@if ($errors->has('profile_img'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('profile_img') }}</strong>
												</span> 
											@endif
										</div>
									</div>-->
									<input type="hidden" name="route" value="{{url()->previous()}}">
									
									<!-- Name and Gender Row -->
									<div class="col-md-4 col-sm-12">
										<div class="form-group"> 
											<label for="first_name">First Name <span class="span_req">*</span></label>
											{!! Form::text('first_name', old('first_name'), array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter first name' ))  !!}
											@if ($errors->has('first_name'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('first_name') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									
									<div class="col-md-4 col-sm-12">
										<div class="form-group"> 
											<label for="last_name">Last Name <span class="span_req">*</span></label>
											{!! Form::text('last_name', old('last_name'), array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter last name' ))  !!}
											@if ($errors->has('last_name'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('last_name') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									
									<div class="col-md-4 col-sm-12">
										<div class="form-group"> 
											<label for="gender">Gender <span class="span_req">*</span></label>
											<select name="gender" id="gender" class="form-control" data-valid="required">
												<option value="">Select Gender</option>
												<option value="Male">Male</option>
												<option value="Female">Female</option>
												<option value="Other">Other</option>
											</select>
											@if ($errors->has('gender'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('gender') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									
									<!-- DOB, Age, Client ID Row -->
									<div class="col-md-3 col-sm-12">
										<div class="form-group">
										<label for="dob">Date of Birth</label>
										<div class="input-group">
											<span class="input-group-text">
												<i class="fas fa-calendar-alt"></i>
											</span>
												{!! Form::text('dob', old('dob'), array('class' => 'form-control dobdatepickers', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'DD/MM/YYYY' ))  !!} 
												@if ($errors->has('dob'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('dob') }}</strong>
													</span> 
												@endif
											</div>
										</div>
									</div>
									
									<div class="col-md-3 col-sm-12">
										<div class="form-group"> 
										<label for="age">Age</label>
										<div class="input-group">
											<span class="input-group-text">
												<i class="fas fa-calendar-alt"></i>
											</span>
												{!! Form::text('age', old('age'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Age' ))  !!}
												@if ($errors->has('age'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('age') }}</strong>
													</span> 
												@endif
											</div>
										</div>
									</div>
									
									<div class="col-md-3 col-sm-12">
										<div class="form-group"> 
											<label for="client_id">Client ID</label>
											{!! Form::text('client_id', '', array('class' => 'form-control bg-light', 'data-valid'=>'', 'autocomplete'=>'off', 'id' => 'checkclientid', 'placeholder'=>'Auto-generated' ,'readonly' => 'readonly' ))  !!}
											@if ($errors->has('client_id'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('client_id') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									
									<div class="col-md-3 col-sm-12">
										<div class="form-group">
											<label for="marital_status">Marital Status</label>
											<select name="marital_status" id="marital_status" class="form-control">
												<option value="">Select Marital Status</option>
												<option value="Never Married">Never Married</option>
												<option value="Engaged">Engaged</option>
												<option value="Married">Married</option>
												<option value="De Facto">De Facto</option>
												<option value="Separated">Separated</option>
												<option value="Divorced">Divorced</option>
												<option value="Widowed">Widowed</option>
											</select>
											@if ($errors->has('marital_status'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('marital_status') }}</strong>
												</span> 
											@endif
										</div>
									</div>
								</div>
								</section>
							</div>
						</div>
					</div>
				</div>

				<!-- Section 2: Contact Information -->
				<div class="row mt-3">
					<div class="col-12">
						<div class="card section-card">
							<div class="card-body compact-contact-section">
								<section class="form-section" style="margin-bottom: 0;">
									<h3><i class="fas fa-user"></i> Contact Information</h3>
								<div class="row">
									<!-- Phone Number -->
									<div class="col-md-6 col-sm-12">
										<div class="form-group">
											<label for="client_phone">Phone Number <span class="span_req">*</span></label>
											<div class="cus_field_input">
												<div class="country_code">
													@include('partials.country-code-select', [
														'name' => 'client_country_code',
														'selected' => old('client_country_code', \App\Helpers\PhoneHelper::getDefaultCountryCode())
													])
												</div>
												{!! Form::text('client_phone', old('client_phone'), array('class' => 'form-control tel_input', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter phone number', 'id'=>'checkphone' ))  !!}
												@if ($errors->has('client_phone'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('client_phone') }}</strong>
													</span> 
												@endif
											</div>
										</div>
									</div>

									<!-- Email Address -->
									<div class="col-md-6 col-sm-12">
										<div class="form-group">
											<label for="email">Email Address <span class="span_req">*</span></label>
											{!! Form::text('email', old('email'), array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter email address', 'id'=>'checkemail' ))  !!}
											@if ($errors->has('email'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('email') }}</strong>
												</span> 
											@endif
										</div>
									</div>
								</div>
								</section>
							</div>
						</div>
					</div>

					<!-- Section 3: Visa & Passport Information -->
					<div class="row mt-3">
						<div class="col-12">
							<div class="card section-card">
								<div class="card-body">
									<section class="form-section">
										<h3><i class="fas fa-file-contract"></i> Visa Details</h3>
									<div class="row">
										<div class="col-md-4 col-sm-12">
											<div class="form-group"> 
												<label for="visa_type">Visa Type</label>
												<select class="form-control select2" name="visa_type" id="visa_type">
													<option value="">- Select Visa Type -</option>
													@foreach(\App\Models\VisaType::orderby('name', 'ASC')->get() as $visalist)
														<option value="{{$visalist->name}}">{{$visalist->name}}</option>
													@endforeach
												</select>
												@if ($errors->has('visa_type'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('visa_type') }}</strong>
													</span> 
												@endif
											</div>
										</div>
										
										<div class="col-md-4 col-sm-12">
											<div class="form-group"> 
												<label for="visa_opt">Visa Details</label>
												{!! Form::text('visa_opt', old('visa_opt'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Additional visa information' ))  !!}
											</div>
										</div>
										
										<div class="col-md-4 col-sm-12 visa-expiry-field">
											<div class="form-group"> 
												<label for="visaExpiry">Visa Expiry Date</label>
												<div class="input-group">
													<span class="input-group-text">
														<i class="fas fa-calendar-alt"></i>
													</span>
													{!! Form::text('visaExpiry', old('visaExpiry'), array('class' => 'form-control dobdatepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'DD/MM/YYYY' ))  !!}
													@if ($errors->has('visaExpiry'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('visaExpiry') }}</strong>
														</span> 
													@endif
												</div>
											</div>
										</div>
										
										<div class="col-md-4 col-sm-12 visa-intake-field">
											<div class="form-group"> 
												<label for="preferredIntake">Preferred Intake</label>
												<div class="input-group">
													<span class="input-group-text">
														<i class="fas fa-calendar-alt"></i>
													</span>
													{!! Form::text('preferredIntake', old('preferredIntake'), array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select intake date' ))  !!}
													@if ($errors->has('preferredIntake'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('preferredIntake') }}</strong>
														</span> 
													@endif
												</div>
											</div> 
										</div>
										
										<div class="col-md-4 col-sm-12 visa-passport-field">
											<div class="form-group"> 
												<label for="country_passport">Country of Passport</label>
												<select class="form-control select2" name="country_passport">
													<option value="">- Select Country -</option>
												<?php
													foreach(\App\Models\Country::all() as $list){
														?>
														<option value="{{ @$list->name }}">{{ @$list->name }}</option>
														<?php
													}
												?>
												</select>
												@if ($errors->has('country_passport'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('country_passport') }}</strong>
													</span> 
												@endif 
											</div>
										</div>
										
										<div class="col-md-4 col-sm-12 visa-passport-field">
											<div class="form-group"> 
												<label for="passport_number">Passport Number</label>
												{!! Form::text('passport_number', old('passport_number'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter passport number' ))  !!}
												@if ($errors->has('passport_number'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('passport_number') }}</strong>
													</span> 
												@endif
											</div>
										</div>
									</div>
								</section>
							</div>
						</div>
					</div>

					<!-- Address Information & Related Files -->
					<div class="form-content-section">
						<section class="form-section">
							<h3><i class="fas fa-map-marker-alt"></i> Address Information</h3>
							{{-- Address Autocomplete Component --}}
							<div id="addressAutocomplete" 
								 data-search-route="{{ route('address.search') }}"
								 data-details-route="{{ route('address.details') }}"
								 data-csrf-token="{{ csrf_token() }}">
								
								<div class="address-wrapper">
									<div class="content-grid">
										<div class="form-group address-search-container" style="grid-column: span 2;">
											<label for="address">Address</label>
											{!! Form::text('address', old('address'), array('placeholder'=>"Search address" , 'class' => 'form-control address-search-input', 'data-valid'=>'', 'autocomplete'=>'off' ))  !!}
											@if ($errors->has('address'))
												<span class="text-danger">{{ @$errors->first('address') }}</span>
											@endif
											<small class="form-text text-muted">Start typing to search address</small>
										</div>
										
										<div class="form-group">
											<label for="city">City</label>
											{!! Form::text('city', old('city'), array('id' => 'locality', 'class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter city' ))  !!}
											@if ($errors->has('city'))
												<span class="text-danger">{{ @$errors->first('city') }}</span>
											@endif
										</div>
										
										<div class="form-group">
											<label for="zip">Post Code</label>
											{!! Form::text('zip', old('zip'), array('id' => 'postal_code', 'class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter postcode' ))  !!}
											@if ($errors->has('zip'))
												<span class="text-danger">{{ @$errors->first('zip') }}</span>
											@endif
										</div>
										
										<div class="form-group">
											<label for="state">State</label>
											<select class="form-control" name="state">
												<option value="">- Select State -</option>	
												<option value="Australian Capital Territory">Australian Capital Territory</option>
												<option value="New South Wales">New South Wales</option>
												<option value="Northern Territory">Northern Territory</option>
												<option value="Queensland">Queensland</option>
												<option value="South Australia">South Australia</option>
												<option value="Tasmania">Tasmania</option>
												<option value="Victoria">Victoria</option>
												<option value="Western Australia">Western Australia</option>
											</select>
											@if ($errors->has('state'))
												<span class="text-danger">{{ @$errors->first('state') }}</span>
											@endif
										</div>
										
										<div class="form-group">
											<label for="country">Country</label>
											<select class="form-control select2" id="country_select" name="country">
												<option value="">- Select Country -</option>
											<?php
												foreach(\App\Models\Country::all() as $list){
													?>
													<option value="{{@$list->sortname}}">{{@$list->name}}</option>
													<?php
												}
											?>
											</select>
											@if ($errors->has('country'))
												<span class="text-danger">{{ @$errors->first('country') }}</span>
											@endif
										</div>
									</div>
								</div>
							</div>
						</section>

						<section class="form-section">
							<h3><i class="fas fa-link"></i> Related Files</h3>
							<div class="content-grid">
								<div class="form-group" style="grid-column: span 2;">
									<label for="related_files">Similar Related Files</label>
									<select class="form-control js-data-example-ajaxcc select2" name="related_files[]" multiple>
									</select>
									@if ($errors->has('related_files'))
										<span class="text-danger">{{ @$errors->first('related_files') }}</span>
									@endif
								</div>
							</div>
						</section>
					</div>

					<!-- Professional Details Section -->
					<div class="form-content-section">
						<section class="form-section">
							<h3><i class="fas fa-briefcase"></i> Professional Details</h3>
							<div class="content-grid">
								<div class="form-group">
									<label for="nomi_occupation">Nominated Occupation</label>
									{!! Form::text('nomi_occupation', old('nomi_occupation'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter occupation' ))  !!}
									@if ($errors->has('nomi_occupation'))
										<span class="text-danger">{{ @$errors->first('nomi_occupation') }}</span>
									@endif
								</div>
								<div class="form-group">
									<label for="skill_assessment">Skill Assessment</label>
									<select class="form-control" name="skill_assessment">
										<option value="">Select</option>
										<option value="Yes">Yes</option>
										<option value="No">No</option>
									</select>
									@if ($errors->has('skill_assessment'))
										<span class="text-danger">{{ @$errors->first('skill_assessment') }}</span>
									@endif
								</div>
								<div class="form-group">
									<label for="high_quali_aus">Highest Qualification in Australia</label>
									{!! Form::text('high_quali_aus', old('high_quali_aus'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter qualification' ))  !!}
									@if ($errors->has('high_quali_aus'))
										<span class="text-danger">{{ @$errors->first('high_quali_aus') }}</span>
									@endif
								</div>
								<div class="form-group">
									<label for="high_quali_overseas">Highest Qualification Overseas</label>
									{!! Form::text('high_quali_overseas', old('high_quali_overseas'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter qualification' ))  !!}
									@if ($errors->has('high_quali_overseas'))
										<span class="text-danger">{{ @$errors->first('high_quali_overseas') }}</span>
									@endif
								</div>
								<div class="form-group">
									<label for="relevant_work_exp_aus">Relevant work experience in Australia</label>
									{!! Form::text('relevant_work_exp_aus', old('relevant_work_exp_aus'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'e.g., 2 years' ))  !!}
									@if ($errors->has('relevant_work_exp_aus'))
										<span class="text-danger">{{ @$errors->first('relevant_work_exp_aus') }}</span>
									@endif
								</div>
								<div class="form-group">
									<label for="relevant_work_exp_over">Relevant work experience in Overseas</label>
									{!! Form::text('relevant_work_exp_over', old('relevant_work_exp_over'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'e.g., 5 years' ))  !!}
									@if ($errors->has('relevant_work_exp_over'))
										<span class="text-danger">{{ @$errors->first('relevant_work_exp_over') }}</span>
									@endif
								</div>
							</div>
						</section>
									
									<!-- English Test Scores & Additional Information Section -->
									<section class="form-section">
										<h3><i class="fas fa-language"></i> English Test Scores & Additional Information</h3>
										<div class="english-test-wrapper">
											<div class="row g-3 mb-3">
												<div class="col-md-3 col-sm-6">
													<div class="form-group">
														<label for="test_type">Test Type</label>
														<select class="form-control" name="test_type" id="test_type">
															<option value="">Select Test Type</option>
															@foreach(\App\Models\ClientTestScore::TEST_TYPES as $value => $label)
															<option value="{{ $value }}">{{ $label }}</option>
															@endforeach
														</select>
													</div>
												</div>
												<div class="col-md-auto col-sm-4 col-6">
													<div class="form-group">
														<label for="listening">L</label>
														<input type="number" class="form-control" name="listening" id="listening" step="0.01" placeholder="0.00" min="0" style="width: 80px;"/>
													</div>
												</div>
												<div class="col-md-auto col-sm-4 col-6">
													<div class="form-group">
														<label for="reading">R</label>
														<input type="number" class="form-control" name="reading" id="reading" step="0.01" placeholder="0.00" min="0" style="width: 80px;"/>
													</div>
												</div>
												<div class="col-md-auto col-sm-4 col-6">
													<div class="form-group">
														<label for="writing">W</label>
														<input type="number" class="form-control" name="writing" id="writing" step="0.01" placeholder="0.00" min="0" style="width: 80px;"/>
													</div>
												</div>
												<div class="col-md-auto col-sm-4 col-6">
													<div class="form-group">
														<label for="speaking">S</label>
														<input type="number" class="form-control" name="speaking" id="speaking" step="0.01" placeholder="0.00" min="0" style="width: 80px;"/>
													</div>
												</div>
												<div class="col-md-auto col-sm-4 col-6">
													<div class="form-group">
														<label for="overall">O</label>
														<input type="number" class="form-control" name="overall" id="overall" step="0.01" placeholder="0.00" min="0" style="width: 80px;"/>
													</div>
												</div>
											</div>
											<div class="row g-3">
												<div class="col-md-3 col-sm-6">
													<div class="form-group">
														<label for="test_date">Test Date</label>
														<input type="text" class="form-control datepicker" name="test_date" id="test_date" placeholder="Select date"/>
													</div>
												</div>
											</div>
										</div>
										<input type="hidden" name="test_score_type" value="client">
										
										<div class="content-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; margin-bottom: 12px; margin-top: 20px;">
											<div class="form-group">
												<label for="naati_py">Naati/PY</label>
												<div class="naati-checkbox-wrapper">
													<label class="naati-checkbox-item" for="Naati">
														<input type="checkbox" id="Naati" value="Naati" name="naati_py[]">
														<span class="naati-checkbox-label">Naati</span>
													</label>
													<label class="naati-checkbox-item" for="py">
														<input type="checkbox" id="py" value="PY" name="naati_py[]">
														<span class="naati-checkbox-label">PY</span>
													</label>
												</div>
											</div>
											<div class="form-group">
												<label for="total_points">Total Points</label>
												{!! Form::text('total_points', old('total_points'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter points' ))  !!}
												@if ($errors->has('total_points'))
													<span class="text-danger">{{ @$errors->first('total_points') }}</span>
												@endif
											</div>
										</div>
									</section>
								</div>

								<!-- Internal Information Section -->
								<div class="row mt-3">
									<div class="col-12">
										<div class="card section-card">
											<div class="card-body">
												<section class="form-section">
													<h3><i class="fas fa-cogs"></i> Internal Information</h3>
												<div class="row" id="internal">
													<div class="col-sm-3">
										<div class="form-group">
											<label for="service">Service <span style="color:#ff0000;">*</span></label>
											<select class="form-control select2" name="service" data-valid="required">
												<option value="">- Select Lead Service -</option>
												@foreach(\App\Models\LeadService::orderby('name', 'ASC')->get() as $leadservlist)
												<option value="{{$leadservlist->name}}">{{$leadservlist->name}}</option>
												@endforeach
											</select>
											@if ($errors->has('service'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('service') }}</strong>
												</span> 
											@endif 
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="assign_to">Assign To <span style="color:#ff0000;">*</span></label>
                                          
											<select style="padding: 0px 5px;" name="assign_to[]" id="assign_to" class="form-control select2" data-valid="required" multiple="multiple">
											<?php
                                                $admins = \App\Models\Admin::where('role','!=',7)->orderby('first_name','ASC')->get();
                                                foreach($admins as $admin){
                                                    $branchname = \App\Models\Branch::where('id',$admin->office_id)->first();
                                                ?>
                                                <option value="<?php echo $admin->id; ?>"><?php echo $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')'; ?></option>
                                            <?php
                                                }
                                            ?>
                                            </select>
                                          
											@if ($errors->has('assign_to'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('assign_to') }}</strong>
												</span> 
											@endif 
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="status">Status</label>
											<select style="padding: 0px 5px;" name="status" id="status" class="form-control" data-valid="">
												<option value="">Select Status</option>
												<option value="Unassigned">Unassigned</option>
												<option value="Assigned">Assigned</option>
												<option value="In-Progress">In-Progress</option>
												<option value="Closed">Closed</option>
											</select>
											@if ($errors->has('status'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('status') }}</strong>
												</span> 
											@endif 
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="lead_quality">Quality <span style="color:#ff0000;">*</span></label>
											<select style="padding: 0px 5px;" name="lead_quality" id="lead_quality" class="form-control" data-valid="required">
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
												<option value="4">4</option>
												<option value="5">5</option>
											</select>
											@if ($errors->has('lead_quality'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('lead_quality') }}</strong>
												</span> 
											@endif 
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="lead_source">Source <span style="color:#ff0000;">*</span></label>
											<select style="padding: 0px 5px;" name="source" id="lead_source" class="form-control select2" data-valid="">
												<option value="">- Source -</option>
												<option value="Sub Agent">Sub Agent</option>
												@foreach(\App\Models\Source::all() as $sources)
													<option value="{{$sources->name}}">{{$sources->name}}</option>
												@endforeach
											</select>
											@if ($errors->has('lead_source'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('lead_source') }}</strong>
												</span> 
											@endif 
										</div>
									</div>
									<div class="col-sm-3 is_subagent" style="display:none;">
										<div class="form-group"> 
											<label for="subagent">Sub Agent <span class="span_req">*</span></label>
											<select class="form-control select2" name="subagent">  
												<option>-- Choose a sub agent --</option>
												@foreach(\App\Models\Agent::all() as $agentlist)
													<option value="{{$agentlist->id}}">{{$agentlist->full_name}}</option>
												@endforeach
											</select>
											@if ($errors->has('subagent'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('subagent') }}</strong>
												</span> 
											@endif
										</div>
									</div>
                                  
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="tags_label">Tags/Label </label>
                                          
                                            <select multiple class="form-control select2"  id="tag"  name="tagname[]">

											</select>
										</div>
									</div>
                                  
									<div class="col-sm-12">
										<div class="form-group">
											<label for="comments_note">Comments / Note</label>
											<textarea class="form-control" name="comments_note" placeholder="" data-valid="">{{old('comments_note')}}</textarea>
											@if ($errors->has('comments_note')) 
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('comments_note') }}</strong>
												</span> 
											@endif
										</div>
									</div>  
									<div class="col-sm-12">
										<div class="form-group float-end">
											{!! Form::button('Create Client', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-clients")' ])  !!}
										</div>
									</div>
								</div>
								</section> 
							</div>
					</div>	
				</div>
			</div>  
			{!! Form::close()  !!}
		</div>
	</section>
</div>

@endsection

@section('scripts')

<!-- Configuration for Page-Specific JavaScript -->
<script>
    window.AppConfig = window.AppConfig || {};
    window.PageConfig = window.PageConfig || {};
    
    // CSRF Token
    AppConfig.csrf = '{{ csrf_token() }}';
    
    // API URLs
    AppConfig.urls = {
        siteUrl: '{{ url("/") }}',
        getTagData: '{{ url("/gettagdata") }}',
        getRecipients: '{{ url("/clients/get-recipients") }}',
        checkClientExist: '{{ url("/checkclientexist") }}'
    };
    
    // Page-specific data for create
    PageConfig.isCreatePage = true;
</script>

{{-- Page-Specific JavaScript --}}
<script src="{{ asset('js/pages/admin/client-create.js') }}"></script>

{{-- Address Autocomplete Styles --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/address-autocomplete.css') }}">
@endpush

{{-- Address Autocomplete Scripts --}}
@push('scripts')
    <script src="{{ asset('js/address-autocomplete.js') }}"></script>
@endpush

<!-- Naati/PY Checkbox Handling -->
<script>
jQuery(document).ready(function($){
    // Handle Naati/PY checkbox state changes
    $('.naati-checkbox-item input[type="checkbox"]').on('change', function() {
        updateCheckboxState(this);
    });
    
    // Initialize checked state on page load
    $('.naati-checkbox-item input[type="checkbox"]').each(function() {
        updateCheckboxState(this);
    });
    
    function updateCheckboxState(checkbox) {
        var $item = $(checkbox).closest('.naati-checkbox-item');
        var $label = $item.find('.naati-checkbox-label');
        
        if ($(checkbox).is(':checked')) {
            $item.addClass('checked');
            $label.css({
                'color': '#6366f1',
                'font-weight': '600'
            });
        } else {
            $item.removeClass('checked');
            $label.css({
                'color': '#475569',
                'font-weight': '500'
            });
        }
    }
});
</script>

@endsection