@extends('layouts.admin')
@section('title', 'Create Lead')

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
			{!! Form::open(array('url' => route('leads.store'), 'name'=>"add-leads", 'id' => 'create-lead-form', 'autocomplete'=>'off', "enctype"=>"multipart/form-data"))  !!}
				<input type="hidden" name="type" value="lead"> 
				<!-- Page Header -->
				<div class="row">   
					<div class="col-12">
						<div class="card client-edit-header">
							<div class="card-header d-flex justify-content-between align-items-center">
								<div class="header-title-section">
									<h4 class="mb-1">
										<i class="fas fa-user-plus text-primary"></i> 
										Create Lead
									</h4>
								</div>
								<div class="card-header-action">
								    <a href="{{route('leads.index')}}" class="btn btn-outline-secondary me-2">
								    	<i class="fa fa-arrow-left"></i> Back
								    </a>
								    <button type="submit" class="btn btn-primary" onclick="customValidate('add-leads')">
								    	<i class="fas fa-save"></i> Create Lead
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
												<option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
												<option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
												<option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
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
											<label for="marital_status">Marital Status</label>
											<select name="marital_status" id="marital_status" class="form-control">
												<option value="">Select Marital Status</option>
												<option value="Never Married" {{ old('marital_status') == 'Never Married' ? 'selected' : '' }}>Never Married</option>
												<option value="Engaged" {{ old('marital_status') == 'Engaged' ? 'selected' : '' }}>Engaged</option>
												<option value="Married" {{ old('marital_status') == 'Married' ? 'selected' : '' }}>Married</option>
												<option value="De Facto" {{ old('marital_status') == 'De Facto' ? 'selected' : '' }}>De Facto</option>
												<option value="Separated" {{ old('marital_status') == 'Separated' ? 'selected' : '' }}>Separated</option>
												<option value="Divorced" {{ old('marital_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
												<option value="Widowed" {{ old('marital_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
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
									<!-- Contact Type -->
									<div class="col-md-3 col-sm-12">
										<div class="form-group">
											<label for="contact_type">Contact Type <span class="span_req">*</span></label>
											<select name="contact_type" id="contact_type" class="form-control" data-valid="required">
												<option value="">Select Contact Type</option>
												<option value="Personal" {{ old('contact_type') == 'Personal' ? 'selected' : '' }}>Personal</option>
												<option value="Office" {{ old('contact_type') == 'Office' ? 'selected' : '' }}>Office</option>
												<option value="Work" {{ old('contact_type') == 'Work' ? 'selected' : '' }}>Work</option>
												<option value="Mobile" {{ old('contact_type') == 'Mobile' ? 'selected' : '' }}>Mobile</option>
											</select>
											@if ($errors->has('contact_type'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('contact_type') }}</strong>
												</span> 
											@endif
										</div>
									</div>

									<!-- Phone Number -->
									<div class="col-md-3 col-sm-12">
										<div class="form-group">
											<label for="phone">Phone Number <span class="span_req">*</span></label>
											<div class="cus_field_input">
												<div class="country_code">
													@include('partials.country-code-select', [
														'name' => 'country_code',
														'selected' => old('country_code', \App\Helpers\PhoneHelper::getDefaultCountryCode())
													])
												</div>
												{!! Form::text('phone', old('phone'), array('class' => 'form-control tel_input contactno_unique', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter phone number', 'id'=>'checkphone' ))  !!}
												@if ($errors->has('phone'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('phone') }}</strong>
													</span> 
												@endif
											</div>
										</div>
									</div>

									<!-- Email Type -->
									<div class="col-md-3 col-sm-12">
										<div class="form-group">
											<label for="email_type">Email Type <span class="span_req">*</span></label>
											<select name="email_type" id="email_type" class="form-control" data-valid="required">
												<option value="">Select Email Type</option>
												<option value="Personal" {{ old('email_type') == 'Personal' ? 'selected' : '' }}>Personal</option>
												<option value="Work" {{ old('email_type') == 'Work' ? 'selected' : '' }}>Work</option>
												<option value="Business" {{ old('email_type') == 'Business' ? 'selected' : '' }}>Business</option>
											</select>
											@if ($errors->has('email_type'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('email_type') }}</strong>
												</span> 
											@endif
										</div>
									</div>

									<!-- Email Address -->
									<div class="col-md-3 col-sm-12">
										<div class="form-group">
											<label for="email">Email Address <span class="span_req">*</span></label>
											{!! Form::text('email', old('email'), array('class' => 'form-control email_unique', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter email address', 'id'=>'checkemail' ))  !!}
											@if ($errors->has('email'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('email') }}</strong>
												</span> 
											@endif
										</div>
									</div>
								</div>
								
								<!-- Additional Contact Fields -->
								<div class="row mt-2">
									<div class="col-md-12">
										<div class="form-group" style="margin-top: 10px;">
											<label for="add_other_email_phone"></label>
											<a href="javascript:void(0)" class="add_other_email_phone" data-bs-toggle="tooltip" data-placement="bottom" title="Show/Hide another email and contact no">
												<i class="fa fa-plus" aria-hidden="true"></i> Add Another Email & Phone
											</a>
										</div>
									</div>
								</div>
								
								<div class="row other_email_div" style="display:none;">
									<div class="col-md-6 col-sm-12">
										<div class="form-group">
											<label for="att_email">Additional Email</label>
											{!! Form::text('att_email', old('att_email'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter additional email' ))  !!}
											@if ($errors->has('att_email'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('att_email') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-md-6 col-sm-12 other_phone_div" style="display:none;">
										<div class="form-group">
											<label for="att_phone">Additional Phone</label>
											<div class="cus_field_input">
												<div class="country_code">
													@include('partials.country-code-select', [
														'name' => 'att_country_code',
														'selected' => old('att_country_code', \App\Helpers\PhoneHelper::getDefaultCountryCode())
													])
												</div>
												{!! Form::text('att_phone', old('att_phone'), array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter additional phone' ))  !!}
												@if ($errors->has('att_phone'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('att_phone') }}</strong>
													</span> 
												@endif
											</div>
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
														<option value="{{$visalist->name}}" {{ old('visa_type') == $visalist->name ? 'selected' : '' }}>{{$visalist->name}}</option>
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
												<label for="visa_expiry_date">Visa Expiry Date</label>
												<div class="input-group">
													<span class="input-group-text">
														<i class="fas fa-calendar-alt"></i>
													</span>
													{!! Form::text('visa_expiry_date', old('visa_expiry_date'), array('class' => 'form-control dobdatepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'DD/MM/YYYY' ))  !!}
													@if ($errors->has('visa_expiry_date'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('visa_expiry_date') }}</strong>
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
														$selected = old('country_passport') == $list->name ? 'selected' : '';
														?>
														<option value="{{ @$list->name }}" <?php echo $selected; ?>>{{ @$list->name }}</option>
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
												<label for="passport_no">Passport Number</label>
												{!! Form::text('passport_no', old('passport_no'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter passport number' ))  !!}
												@if ($errors->has('passport_no'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('passport_no') }}</strong>
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
												<option value="Australian Capital Territory" {{ old('state') == 'Australian Capital Territory' ? 'selected' : '' }}>Australian Capital Territory</option>
												<option value="New South Wales" {{ old('state') == 'New South Wales' ? 'selected' : '' }}>New South Wales</option>
												<option value="Northern Territory" {{ old('state') == 'Northern Territory' ? 'selected' : '' }}>Northern Territory</option>
												<option value="Queensland" {{ old('state') == 'Queensland' ? 'selected' : '' }}>Queensland</option>
												<option value="South Australia" {{ old('state') == 'South Australia' ? 'selected' : '' }}>South Australia</option>
												<option value="Tasmania" {{ old('state') == 'Tasmania' ? 'selected' : '' }}>Tasmania</option>
												<option value="Victoria" {{ old('state') == 'Victoria' ? 'selected' : '' }}>Victoria</option>
												<option value="Western Australia" {{ old('state') == 'Western Australia' ? 'selected' : '' }}>Western Australia</option>
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
													$selected = old('country') == $list->sortname ? 'selected' : '';
													?>
													<option value="{{@$list->sortname}}" <?php echo $selected; ?>>{{@$list->name}}</option>
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
									<select class="form-control js-data-example-ajaxcc" name="related_files[]" multiple>
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
										<option value="Yes" {{ old('skill_assessment') == 'Yes' ? 'selected' : '' }}>Yes</option>
										<option value="No" {{ old('skill_assessment') == 'No' ? 'selected' : '' }}>No</option>
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
															<option value="{{ $value }}" {{ old('test_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
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
										<input type="hidden" name="test_score_type" value="lead">
										
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
												<option value="{{$leadservlist->name}}" {{ old('service') == $leadservlist->name ? 'selected' : '' }}>{{$leadservlist->name}}</option>
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
                                                $oldAssignTo = old('assign_to', []);
                                                foreach($admins as $admin){
                                                    $branchname = \App\Models\Branch::where('id',$admin->office_id)->first();
                                                    $selected = in_array($admin->id, (array)$oldAssignTo) ? 'selected' : '';
                                                ?>
                                                <option value="<?php echo $admin->id; ?>" <?php echo $selected; ?>><?php echo $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')'; ?></option>
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
												<option value="Unassigned" {{ old('status') == 'Unassigned' ? 'selected' : '' }}>Unassigned</option>
												<option value="Assigned" {{ old('status') == 'Assigned' ? 'selected' : '' }}>Assigned</option>
												<option value="In-Progress" {{ old('status') == 'In-Progress' ? 'selected' : '' }}>In-Progress</option>
												<option value="Closed" {{ old('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
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
												<option value="1" {{ old('lead_quality') == '1' ? 'selected' : '' }}>1</option>
												<option value="2" {{ old('lead_quality') == '2' ? 'selected' : '' }}>2</option>
												<option value="3" {{ old('lead_quality') == '3' ? 'selected' : '' }}>3</option>
												<option value="4" {{ old('lead_quality') == '4' ? 'selected' : '' }}>4</option>
												<option value="5" {{ old('lead_quality') == '5' ? 'selected' : '' }}>5</option>
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
												<option value="Sub Agent" {{ old('source') == 'Sub Agent' ? 'selected' : '' }}>Sub Agent</option>
												@foreach(\App\Models\Source::all() as $sources)
													<option value="{{$sources->name}}" {{ old('source') == $sources->name ? 'selected' : '' }}>{{$sources->name}}</option>
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
												<option value="">-- Choose a sub agent --</option>
												@foreach(\App\Models\Agent::all() as $agentlist)
													<option value="{{$agentlist->id}}" {{ old('subagent') == $agentlist->id ? 'selected' : '' }}>{{$agentlist->full_name}}</option>
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
											{!! Form::button('Create Lead', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-leads")' ])  !!}
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
        checkClientExist: '{{ url("/checkclientexist") }}',
        checkEmailUnique: '{{ url("/is_email_unique") }}',
        checkContactUnique: '{{ url("/is_contactno_unique") }}'
    };
    
    // Page-specific data for create
    PageConfig.isCreatePage = true;
</script>

{{-- Load Client Create JavaScript for Shared Functionality --}}
<script src="{{ asset('js/pages/admin/client-create.js') }}"></script>

{{-- Page-Specific JavaScript --}}
<script>
// Handle additional email/phone toggle
$(document).ready(function($){
    $('.add_other_email_phone').on('click', function(){
        if ($('.other_email_div').css('display') == 'none') {
            $('.other_email_div').css('display','block');
            $('.other_phone_div').css('display','block');
            $('.add_other_email_phone').html('<i class="fa fa-minus" aria-hidden="true"></i> Hide Additional Email & Phone');
        } else {
            $('.other_email_div').css('display','none');
            $('.other_phone_div').css('display','none');
            $('.add_other_email_phone').html('<i class="fa fa-plus" aria-hidden="true"></i> Add Another Email & Phone');
        }
    });
    
    // Check email uniqueness (blur only - not while typing)
    $(document).on('blur', '.email_unique', function(){
        var $input = $(this);
        var email = $input.val().trim();
        $input.nextAll('.custom-error.js-validation-error').remove();
        if(email != '') {
            $.ajax({
                url: AppConfig.urls.checkEmailUnique,
                type: 'POST',
                data: {
                    _token: AppConfig.csrf,
                    email: email
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if(data.status == 1) {
                        $input.after('<span class="custom-error js-validation-error">' + data.message + '</span>');
                    }
                }
            });
        }
    });
    
    // Check contact uniqueness (blur only - not while typing)
    $(document).on('blur', '.contactno_unique', function(){
        var $input = $(this);
        var $target = $input.closest('.cus_field_input').length ? $input.closest('.cus_field_input') : $input;
        var contact = $input.val().trim();
        $target.nextAll('.custom-error.js-validation-error').remove();
        if(contact != '') {
            $.ajax({
                url: AppConfig.urls.checkContactUnique,
                type: 'POST',
                data: {
                    _token: AppConfig.csrf,
                    contact: contact
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if(data.status == 1) {
                        $target.after('<span class="custom-error js-validation-error">' + data.message + '</span>');
                    }
                }
            });
        }
    });
    
    // Sub agent toggle
    $('#lead_source').on('change', function(){
        if($(this).val() == 'Sub Agent') {
            $('.is_subagent').show();
        } else {
            $('.is_subagent').hide();
        }
    });
    
    // Initialize Related Files Select2 with AJAX - Must run after all other scripts
    setTimeout(function() {
        var $relatedFiles = $('.js-data-example-ajaxcc');
        
        console.log('Found elements:', $relatedFiles.length);
        
        // Destroy any existing Select2 instance and remove ONLY its container
        if ($relatedFiles.data('select2')) {
            $relatedFiles.select2('destroy');
        }
        
        // Remove only the orphaned container for THIS specific element
        $relatedFiles.next('.select2-container').remove();
        
        // Make sure the original select is visible
        $relatedFiles.show();
        
        // Initialize with AJAX configuration
        $relatedFiles.select2({
            multiple: true,
            closeOnSelect: false,
            minimumInputLength: 1,
            placeholder: 'Search for clients...',
            ajax: {
                url: '{{ url("/clients/get-recipients") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    console.log('Searching for:', params.term);
                    return {
                        q: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    console.log('Raw response:', data);
                    console.log('Items:', data.items);
                    console.log('Items length:', data.items ? data.items.length : 0);
                    
                    // Make sure each item has id and text properties
                    var results = [];
                    if (data.items && data.items.length > 0) {
                        results = data.items.map(function(item) {
                            return {
                                id: item.id,
                                text: item.text || item.name || 'Unknown',
                                name: item.name,
                                email: item.email,
                                status: item.status
                            };
                        });
                    }
                    
                    console.log('Processed results:', results);
                    
                    return {
                        results: results,
                        pagination: {
                            more: false
                        }
                    };
                },
                cache: true
            },
            templateResult: function(repo) {
                if (repo.loading) {
                    return 'Searching...';
                }
                
                var $container = $('<div class="select2-result-repository">');
                $container.append($('<div>').text(repo.name || repo.text));
                if (repo.email) {
                    $container.append($('<small style="display:block;color:#666;">').text(repo.email));
                }
                
                return $container;
            },
            templateSelection: function(repo) {
                return repo.name || repo.text;
            }
        });
        
        console.log('Related Files Select2 initialized with AJAX');
    }, 500);
});
</script>

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