@extends('layouts.admin')
@section('title', 'Create Client')

@push('styles')
<style>
/* Form Section Subheading Styles - Matching Edit Page */
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

/* Ensure all section cards have consistent sizing and padding */
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

/* Naati/PY Checkbox Styling */
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
					<div class="col-12 mt-3">
						<div class="card section-card">
							<div class="card-body">
								<section class="form-section">
									<h3><i class="fas fa-id-card"></i> Basic Information</h3>
								<div class="row">
											<div class="col-4 col-md-4 col-lg-4">
												<div class="form-group"> 
													<label for="first_name">First Name <span class="span_req">*</span></label>
													{!! Form::text('first_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
													@if ($errors->has('first_name'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('first_name') }}</strong>
														</span> 
													@endif
												</div>
											</div>
											<div class="col-4 col-md-4 col-lg-4">
												<div class="form-group"> 
													<label for="last_name">Last Name <span class="span_req">*</span></label>
													{!! Form::text('last_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
													@if ($errors->has('last_name'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('last_name') }}</strong>
														</span> 
													@endif
												</div>
											</div>
											<div class="col-4 col-md-4 col-lg-4">
												<?php
													$oldgender = old('gender');
												?>
												<div class="form-group"> 
													<label style="display:block;" for="gender">Gender <span class="span_req">*</span></label>
													<div class="gender-radio-group" style="display: flex !important; gap: 0.8rem; align-items: center; flex-wrap: wrap; margin-top: 0.5rem;">
														<div class="form-check form-check-inline" style="display: inline-flex !important; align-items: center; margin-right: 0.5rem; margin-bottom: 0;">
															<input class="form-check-input" type="radio" id="male" value="Male" name="gender" <?php if($oldgender == 'Male' || $oldgender == ''){ echo 'checked'; } ?> style="width: 18px; height: 18px; margin-right: 6px; cursor: pointer; flex-shrink: 0;">
															<label class="form-check-label" for="male" style="cursor: pointer; margin-bottom: 0; white-space: nowrap;">Male</label>
														</div>
														<div class="form-check form-check-inline" style="display: inline-flex !important; align-items: center; margin-right: 0.5rem; margin-bottom: 0;">
															<input class="form-check-input" type="radio" id="female" value="Female" name="gender" <?php if($oldgender == 'Female'){ echo 'checked'; } ?> style="width: 18px; height: 18px; margin-right: 6px; cursor: pointer; flex-shrink: 0;">
															<label class="form-check-label" for="female" style="cursor: pointer; margin-bottom: 0; white-space: nowrap;">Female</label>
														</div>
														<div class="form-check form-check-inline" style="display: inline-flex !important; align-items: center; margin-right: 0; margin-bottom: 0;">
															<input class="form-check-input" type="radio" id="other" value="Other" name="gender" <?php if($oldgender == 'Other'){ echo 'checked'; } ?> style="width: 18px; height: 18px; margin-right: 6px; cursor: pointer; flex-shrink: 0;">
															<label class="form-check-label" for="other" style="cursor: pointer; margin-bottom: 0; white-space: nowrap;">Other</label>
														</div>
													</div>
													@if ($errors->has('gender'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('gender') }}</strong>
														</span> 
													@endif
												</div>
											</div>
											<div class="col-4 col-md-4 col-lg-4">
												<div class="form-group">
													<label for="dob">
													Date of Birth</label>
													<div class="input-group">
														<span class="input-group-text">
															<i class="fas fa-calendar-alt"></i>
														</span>
														{!! Form::text('dob', '', array('class' => 'form-control dobdatepickers', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!} 
														@if ($errors->has('dob'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('dob') }}</strong>
															</span> 
														@endif
													</div>
												</div>
											</div>
											<div class="col-4 col-md-4 col-lg-4">
												<div class="form-group"> 
													<label for="age">Age</label>
													<div class="input-group">
														<span class="input-group-text">
															<i class="fas fa-calendar-alt"></i>
														</span>
														{!! Form::text('age', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
														@if ($errors->has('age'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('age') }}</strong>
															</span> 
														@endif
													</div>
												</div>
											</div>
											<div class="col-4 col-md-4 col-lg-4">
															<div class="form-group"> 
																<label for="client_id">Client ID</label>
																{!! Form::text('client_id', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
																@if ($errors->has('client_id'))
																	<span class="custom-error" role="alert">
																		<strong>{{ @$errors->first('client_id') }}</strong>
																	</span> 
																@endif
															</div>
														</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="martial_status">
													Marital Status</label>
											<select style="padding: 0px 5px;" name="martial_status" id="martial_status" class="form-control">
														<option value="">Select Marital Status</option>
														<option value="Married" <?php if(old('martial_status') == 'Married'){ echo 'selected'; } ?>>Married</option>
														<option <?php if(old('martial_status') == 'Never Married'){ echo 'selected'; } ?> value="Never Married">Never Married</option>
														<option <?php if(old('martial_status') == 'Engaged'){ echo 'selected'; } ?> value="Engaged">Engaged</option>
														<option <?php if(old('martial_status') == 'Divorced'){ echo 'selected'; } ?> value="Divorced">Divorced</option>
														<option <?php if(old('martial_status') == 'Separated'){ echo 'selected'; } ?> value="Separated">Separated</option>
														<option <?php if(old('martial_status') == 'De facto'){ echo 'selected'; } ?> value="De facto">De facto</option>
														<option <?php if(old('martial_status') == 'Widowed'){ echo 'selected'; } ?> value="Widowed">Widowed</option>
														<option <?php if(old('martial_status') == 'Others'){ echo 'selected'; } ?> value="Others">Others</option>
													</select>
													@if ($errors->has('martial_status'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('martial_status') }}</strong>
														</span> 
													@endif
												</div>
											</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="contact_type">
											Contact Type <span style="color:#ff0000;">*</span></label>
											<select style="padding: 0px 5px;" name="contact_type" id="contact_type" class="form-control" data-valid="required">
												<option value="Personal" <?php if(old('contact_type') == 'Personal'){ echo 'selected'; } ?>> Personal</option>
												<option <?php if(old('contact_type') == 'Office'){ echo 'selected'; } ?> value="Office">Office</option>
											</select>
											@if ($errors->has('contact_type'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('contact_type') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="phone">Contact No.<span style="color:#ff0000;">*</span></label>
											<div class="cus_field_input">
												<div class="country_code"> 
													<input class="telephone" id="telephone" type="tel" name="country_code" readonly >
												</div>	
												{!! Form::text('phone', '', array('class' => 'form-control tel_input', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'', 'id' => 'checkphone' ))  !!}
												@if ($errors->has('phone'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('phone') }}</strong>
													</span> 
												@endif
											</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label for="email_type">
											Email Type <span style="color:#ff0000;">*</span></label>
											<select style="padding: 0px 5px;" name="email_type" id="email_type" class="form-control" data-valid="required">	
												<option value="Personal" <?php if(old('email_type') == 'Personal'){ echo 'selected'; } ?>> Personal</option>
												<option value="Business" <?php if(old('email_type') == 'Business'){ echo 'selected'; } ?>>Business</option>
											</select>
											@if ($errors->has('email_type'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('email_type') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="email">Email <span style="color:#ff0000;">*</span></label>
											{!! Form::text('email', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'', 'id' => 'checkemail' ))  !!}
											@if ($errors->has('email'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('email') }}</strong>
												</span> 
											@endif
										</div>
									</div>													
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="att_email">Email </label>
											{!! Form::text('att_email', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
											@if ($errors->has('att_email'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('att_email') }}</strong>
												</span> 
											@endif
										</div>
									</div> 
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="att_phone">Phone</label>
											<div class="cus_field_input">
												<div class="country_code"> 
													<input class="telephone" id="telephone" type="tel" name="att_country_code" readonly >
												</div>	
												{!! Form::text('att_phone', '', array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
												@if ($errors->has('att_phone'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('att_phone') }}</strong>
													</span> 
												@endif
											</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="visa_type">Visa Type</label>
											<select class="form-control select2" name="visa_type">
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
									<div style="margin-top:10px;">		
    								{!! Form::text('visa_opt', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Visa' ))  !!}
    								</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="visaExpiry">Visa Expiry Date</label>
											<div class="input-group">
												<span class="input-group-text">
													<i class="fas fa-calendar-alt"></i>
												</span>
												{!! Form::text('visaExpiry', '', array('class' => 'form-control dobdatepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
												@if ($errors->has('visaExpiry'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('visaExpiry') }}</strong>
													</span> 
												@endif
											</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="preferredIntake">Preferred Intake</label>
											<div class="input-group">
												<span class="input-group-text">
													<i class="fas fa-calendar-alt"></i>
												</span>
												{!! Form::text('preferredIntake', '', array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
												@if ($errors->has('preferredIntake'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('preferredIntake') }}</strong>
													</span> 
												@endif
											</div>
										</div> 
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="country_passport">Country of Passport</label>
											<select class="form-control  select2" name="country_passport" >
											<?php
												foreach(\App\Models\Country::all() as $list){
													?>
													<option <?php if(@$list->sortname == 'IN'){ echo 'selected'; } ?> value="{{@$list->sortname}}" >{{@$list->name}}</option>
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
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="passport_number">Passport Number</label>
											{!! Form::text('passport_number', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
											@if ($errors->has('passport_number'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('passport_number') }}</strong>
												</span> 
											@endif
										</div>
									</div>
								</div>
								</section>
								<section class="form-section">
									<h3><i class="fas fa-map-marker-alt"></i> Address Information</h3>
								<div class="row">
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="address">Address</label>
											{!! Form::text('address', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
											@if ($errors->has('address'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('address') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="city">City</label>
											{!! Form::text('city', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
											@if ($errors->has('city'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('city') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
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
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('state') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="zip">Post Code</label>
											{!! Form::text('zip', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
											@if ($errors->has('zip'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('zip') }}</strong>
												</span> 
											@endif
										</div>
									</div>
								</div>
								<hr style="border-color: #000;"/>
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group"> 
											<label for="country">Country</label>
											<select class="form-control select2" name="country" >
											<?php
												foreach(\App\Models\Country::all() as $list){
													?>
													<option <?php if(@$list->sortname == 'AU'){ echo 'selected'; } ?> value="{{@$list->sortname}}" >{{@$list->name}}</option>
													<?php
												}
												?>
											</select>
											@if ($errors->has('country'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('country') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-8">
										<div class="form-group"> 
											<label for="related_files">Similar related files</label>
											<select class="form-control js-data-example-ajaxcc" name="related_files[]">
												
											</select>
											@if ($errors->has('related_files'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('related_files') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="nomi_occupation">Nominated Occupation</label>
											{!! Form::text('nomi_occupation', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
											
											@if ($errors->has('nomi_occupation'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('nomi_occupation') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="skill_assessment">Skill Assessment</label>
											<select class="form-control" name="skill_assessment">
									<option value="">Select</option>
									<option value="Yes">Yes</option>
									<option value="No">No</option>
											</select>
											
											
											@if ($errors->has('skill_assessment'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('skill_assessment') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="high_quali_aus">Highest Qualification in Australia</label>
											{!! Form::text('high_quali_aus', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
											
											@if ($errors->has('high_quali_aus'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('high_quali_aus') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="high_quali_overseas">Highest Qualification Overseas</label>
											{!! Form::text('high_quali_overseas', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
											
											@if ($errors->has('high_quali_overseas'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('high_quali_overseas') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group"> 
											<label for="relevant_work_exp_aus">Relevant work experience in Australia</label>
											{!! Form::text('relevant_work_exp_aus', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
											
											@if ($errors->has('relevant_work_exp_aus'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('relevant_work_exp_aus') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group"> 
											<label for="relevant_work_exp_over">Relevant work experience in Overseas</label>
											{!! Form::text('relevant_work_exp_over', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
												
											@if ($errors->has('relevant_work_exp_over'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('relevant_work_exp_over') }}</strong>
												</span> 
											@endif
										</div>
									</div>									
									<div class="col-sm-4">
										<div class="form-group"> 
											<label for="married_partner">Overall English score</label>
											{!! Form::text('married_partner', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
												
											@if ($errors->has('married_partner'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('married_partner') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label style="display:block;" for="naati_py">Naati/PY</label>
											<div style="white-space: nowrap;">
												<div class="form-check form-check-inline">
													<input class="form-check-input" type="checkbox" id="Naati" value="Naati" name="naati_py[]">
													<label class="form-check-label" for="Naati">Naati</label>
												</div>
												<div class="form-check form-check-inline">
													<input class="form-check-input" type="checkbox" id="py" value="PY" name="naati_py[]">
													<label class="form-check-label" for="py">PY</label>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="total_points">Total Points</label>
											{!! Form::text('total_points', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
												
											@if ($errors->has('total_points'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('total_points') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group"> 
											<label for="start_process">When You want to start Process</label>
												<select class="form-control" name="start_process">
													<option value="">Select</option>
													<option value="As soon As Possible">As soon As Possible</option>
													<option value="In Next 3 Months">In Next 3 Months</option>
													<option value="In Next 6 Months">In Next 6 Months</option>
													<option value="Advise Only">Advise Only</option>
											</select>
											@if ($errors->has('start_process'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('start_process') }}</strong>
												</span> 
											@endif
										</div>
									</div>
								</div>
								</section>
								<section class="form-section">
									<h3><i class="fas fa-briefcase"></i> Professional Details</h3>
								<div class="row">
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="nomi_occupation">Nominated Occupation</label>
											{!! Form::text('nomi_occupation', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
											
											@if ($errors->has('nomi_occupation'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('nomi_occupation') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="skill_assessment">Skill Assessment</label>
											<select class="form-control" name="skill_assessment">
									<option value="">Select</option>
									<option value="Yes">Yes</option>
									<option value="No">No</option>
											</select>
											
											
											@if ($errors->has('skill_assessment'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('skill_assessment') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="high_quali_aus">Highest Qualification in Australia</label>
											{!! Form::text('high_quali_aus', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
											
											@if ($errors->has('high_quali_aus'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('high_quali_aus') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="high_quali_overseas">Highest Qualification Overseas</label>
											{!! Form::text('high_quali_overseas', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
											
											@if ($errors->has('high_quali_overseas'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('high_quali_overseas') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group"> 
											<label for="relevant_work_exp_aus">Relevant work experience in Australia</label>
											{!! Form::text('relevant_work_exp_aus', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
											
											@if ($errors->has('relevant_work_exp_aus'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('relevant_work_exp_aus') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group"> 
											<label for="relevant_work_exp_over">Relevant work experience in Overseas</label>
											{!! Form::text('relevant_work_exp_over', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
												
											@if ($errors->has('relevant_work_exp_over'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('relevant_work_exp_over') }}</strong>
												</span> 
											@endif
										</div>
									</div>									
									<div class="col-sm-4">
										<div class="form-group"> 
											<label for="married_partner">Overall English score</label>
											{!! Form::text('married_partner', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
												
											@if ($errors->has('married_partner'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('married_partner') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label style="display:block;" for="naati_py">Naati/PY</label>
											<div style="white-space: nowrap;">
												<div class="form-check form-check-inline">
													<input class="form-check-input" type="checkbox" id="Naati" value="Naati" name="naati_py[]">
													<label class="form-check-label" for="Naati">Naati</label>
												</div>
												<div class="form-check form-check-inline">
													<input class="form-check-input" type="checkbox" id="py" value="PY" name="naati_py[]">
													<label class="form-check-label" for="py">PY</label>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="total_points">Total Points</label>
											{!! Form::text('total_points', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
												
											@if ($errors->has('total_points'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('total_points') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group"> 
											<label for="start_process">When You want to start Process</label>
												<select class="form-control" name="start_process">
													<option value="">Select</option>
													<option value="As soon As Possible">As soon As Possible</option>
													<option value="In Next 3 Months">In Next 3 Months</option>
													<option value="In Next 6 Months">In Next 6 Months</option>
													<option value="Advise Only">Advise Only</option>
											</select>
											@if ($errors->has('start_process'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('start_process') }}</strong>
												</span> 
											@endif
										</div>
									</div>
								</div>
								</section>
								<section class="form-section">
									<h3><i class="fas fa-cogs"></i> Internal Information</h3>
								<div class="row " id="internal">
									<div class="col-sm-3">
										<div class="form-group">
											<label for="service">Service <span style="color:#ff0000;">*</span></label>
											<select class="form-control select2" name="service" data-valid="required">
											<option value="">- Select Lead Service -</option>
											@foreach(\App\Models\LeadService::orderby('name', 'ASC')->get() as $leadservlist)
												<option <?php if(old('service') == $leadservlist->name){ echo 'selected'; } ?> value="{{$leadservlist->name}}">{{$leadservlist->name}}</option>
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
											<select style="padding: 0px 5px;" name="assign_to" id="assign_to" class="form-control select2" data-valid="required">
											<?php
												$admins = \App\Models\Admin::where('role','!=',7)->orderby('first_name','ASC')->get();
												foreach($admins as $admin){
													$branchname = \App\Models\Branch::where('id',$admin->office_id)->first();
												?>
												<option <?php if(old('assign_to') == $admin->id){ echo 'selected'; } ?> value="<?php echo $admin->id; ?>"><?php echo $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')'; ?> </option>
												<?php } ?>
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
												<option <?php if(old('status') == 'Unassigned'){ echo 'selected'; } ?> value="Unassigned">Unassigned</option>
												<option <?php if(old('status') == 'Assigned'){ echo 'selected'; } ?> value="Assigned">Assigned</option>
												<option <?php if(old('status') == 'In-Progress'){ echo 'selected'; } ?> value="In-Progress">In-Progress</option>
												<option <?php if(old('status') == 'Closed'){ echo 'selected'; } ?> value="Closed">Closed</option>
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
												<option <?php if(old('lead_quality') == '1'){ echo 'selected'; } ?> value="1">1</option>
												<option <?php if(old('lead_quality') == '2'){ echo 'selected'; } ?> value="2">2</option>
												<option <?php if(old('lead_quality') == '3'){ echo 'selected'; } ?> value="3">3</option>
												<option <?php if(old('lead_quality') == '4'){ echo 'selected'; } ?> value="4">4</option>
												<option <?php if(old('lead_quality') == '5'){ echo 'selected'; } ?> value="5">5</option>
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
											<option <?php if(old('lead_source') == $sources->name){ echo 'selected'; } ?> value="{{$sources->name}}">{{$sources->name}}</option>
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
										<select multiple class="form-control select2" name="tagname[]">
															<option value="">-- Search & Select tag --</option>
														<?php
														foreach(\App\Models\Tag::all() as $tags){
															?>
															<option value="{{$tags->id}}">{{$tags->name}}</option>
															<?php
														}
														?>	 
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
<script src="{{ asset('js/pages/admin/client-create.js') }}"></script>
@endsection