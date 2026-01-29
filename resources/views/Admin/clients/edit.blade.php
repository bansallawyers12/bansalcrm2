@extends('layouts.admin')
@section('title', 'Edit Client')

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
			<form action="{{ url('clients/edit') }}" method="POST" name="edit-clients" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="id" value="{{ @$fetchedData->id }}">
				<input type="hidden" name="type" value="{{ @$fetchedData->type }}"> 
				
				<!-- Validation Errors Summary -->
				@if ($errors->any())
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<strong><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</strong>
						<ul class="mb-0 mt-2">
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
				@endif
				
				<!-- Page Header -->
				<div class="row">   
					<div class="col-12">
						<div class="card client-edit-header">
							<div class="card-header d-flex justify-content-between align-items-center">
								<div class="header-title-section">
									<h4 class="mb-1">
										<i class="fas fa-user-edit text-primary"></i> 
										Edit Client: {{ @$fetchedData->first_name }} {{ @$fetchedData->last_name }}
									</h4>
									<small class="text-muted">Client ID: <span class="badge badge-secondary">{{ @$fetchedData->client_id }}</span></small>
								</div>
								<div class="card-header-action">
								    <a href="{{route('clients.index')}}" class="btn btn-outline-secondary me-2">
								    	<i class="fa fa-arrow-left"></i> Back
								    </a>
								    <button type="button" class="btn btn-primary" onclick="customValidate('edit-clients')">
								    	<i class="fas fa-save"></i> Save Changes
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
											{!! Form::text('first_name', old('first_name', @$fetchedData->first_name), array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter first name' ))  !!}
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
											{!! Form::text('last_name', old('last_name', @$fetchedData->last_name), array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter last name' ))  !!}
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
												<option value="Male" @if(old('gender', @$fetchedData->gender) == "Male") selected @endif>Male</option>
												<option value="Female" @if(old('gender', @$fetchedData->gender) == "Female") selected @endif>Female</option>
												<option value="Other" @if(old('gender', @$fetchedData->gender) == "Other") selected @endif>Other</option>
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
												<?php
													$dob = old('dob');
													if (!$dob && $fetchedData->dob != ''){
														$dob = date('d/m/Y', strtotime($fetchedData->dob));
													}
												?>
												{!! Form::text('dob', @$dob, array('class' => 'form-control dobdatepickers', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'DD/MM/YYYY' ))  !!} 
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
												{!! Form::text('age', @$fetchedData->age, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Age' ))  !!}
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
											{!! Form::text('client_id', @$fetchedData->client_id, array('class' => 'form-control bg-light', 'data-valid'=>'', 'autocomplete'=>'off', 'id' => 'checkclientid', 'placeholder'=>'Auto-generated' ,'readonly' => 'readonly' ))  !!}
											@if ($errors->has('client_id'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('client_id') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									
									<div class="col-md-3 col-sm-12">
										<div class="form-group">
											<label for="martial_status">Marital Status</label>
											<select name="martial_status" id="martial_status" class="form-control">
												<option value="">Select Marital Status</option>
												<option value="Married" @if(old('martial_status', @$fetchedData->martial_status) == "Married") selected @endif>Married</option>
												<option value="Never Married" @if(old('martial_status', @$fetchedData->martial_status) == "Never Married") selected @endif>Never Married</option>
												<option value="Engaged" @if(old('martial_status', @$fetchedData->martial_status) == "Engaged") selected @endif>Engaged</option>
												<option value="Divorced" @if(old('martial_status', @$fetchedData->martial_status) == "Divorced") selected @endif>Divorced</option>
												<option value="Separated" @if(old('martial_status', @$fetchedData->martial_status) == "Separated") selected @endif>Separated</option>
												<option value="De facto" @if(old('martial_status', @$fetchedData->martial_status) == "De facto") selected @endif>De facto</option>
												<option value="Widowed" @if(old('martial_status', @$fetchedData->martial_status) == "Widowed") selected @endif>Widowed</option>
												<option value="Others" @if(old('martial_status', @$fetchedData->martial_status) == "Others") selected @endif>Others</option>
											</select>
											@if ($errors->has('martial_status'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('martial_status') }}</strong>
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
									<!-- Phone Numbers - Left Side -->
									<div class="col-md-6 col-sm-12">
										<div class="contact-subsection">
											<div class="d-flex justify-content-between align-items-center mb-2">
												<label class="section-label mb-0"><i class="fas fa-phone-alt"></i> Phone Numbers</label>
												<a href="javascript:;" class="btn btn-xs btn-primary openclientphonenew">
													<i class="fa fa-plus"></i> Add
												</a>
											</div>
											@if ($errors->has('contact_type') || $errors->has('client_phone'))
												<div class="mb-2">
													@if ($errors->has('contact_type'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('contact_type') }}</strong>
														</span>
													@endif
													@if ($errors->has('client_phone'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('client_phone') }}</strong>
														</span>
													@endif
												</div>
											@endif

											<script>
											var clientphonedata = new Array();
											</script>
											
											<div class="clientphonedata compact-contact-list">
												<?php
												// Check if old input exists (validation error occurred)
												$oldContactTypes = old('contact_type');
												$oldCountryCodes = old('client_country_code');
												$oldPhones = old('client_phone');
												$oldPhoneIds = old('clientphoneid');
												
												if (is_array($oldContactTypes) && count($oldContactTypes) > 0) {
													// Display old input from failed validation
													for ($iii = 0; $iii < count($oldContactTypes); $iii++) {
														$contactType = $oldContactTypes[$iii] ?? '';
														$countryCode = $oldCountryCodes[$iii] ?? '';
														$phone = $oldPhones[$iii] ?? '';
														$phoneId = $oldPhoneIds[$iii] ?? '';
													?>
													<script>
													clientphonedata[<?php echo $iii; ?>] = { "contact_type" :'<?php echo addslashes($contactType); ?>',"country_code" :'<?php echo addslashes($countryCode); ?>',"phone" :'<?php echo addslashes($phone); ?>'}
													</script>
													<div class="compact-contact-item" id="metatag2_<?php echo $iii; ?>">
														<span class="contact-type-tag"><?php echo htmlspecialchars($contactType); ?></span>
														<span class="contact-phone"><?php echo htmlspecialchars($countryCode . ' ' . $phone); ?></span>
														<div class="contact-actions">
															<a href="javascript:;" 
																class="editclientphone btn-edit" 
																data-index="<?php echo $iii; ?>" 
																data-id="<?php echo htmlspecialchars($phoneId); ?>"
																data-type="<?php echo htmlspecialchars($contactType); ?>"
																data-country="<?php echo htmlspecialchars($countryCode); ?>"
																data-phone="<?php echo htmlspecialchars($phone); ?>"
																title="Edit">
																<i class="fa fa-edit"></i>
															</a>
															<?php if($contactType == "Personal") {
																$check_verified_phoneno = $countryCode.$phone;
																$verifiedNumber = \App\Models\VerifiedNumber::where('phone_number',$check_verified_phoneno)->where('is_verified', true)->first();
																if ($verifiedNumber) {
																	echo '<span class="verified-badge"><i class="fas fa-check-circle"></i></span>';
																} else {
																	echo '<button type="button" class="btn-verify phone_verified" data-fname="'.$fetchedData->first_name.'" data-phone="'.$check_verified_phoneno.'" data-clientid="'.$fetchedData->id.'"><i class="fas fa-paper-plane"></i></button>';
																}
															} ?>
															<?php if($contactType != "Personal") { ?>
																<a href="javascript:;" dataid="<?php echo $iii; ?>" contactid="<?php echo htmlspecialchars($phoneId); ?>" class="deletecontact btn-delete">
																	<i class="fa fa-trash"></i>
																</a>
															<?php } ?>
														</div>
														<!-- Hidden fields -->
														<input type="hidden" name="contact_type[]" value="<?php echo htmlspecialchars($contactType); ?>">
														<input type="hidden" name="client_country_code[]" value="<?php echo htmlspecialchars($countryCode); ?>">
														<input type="hidden" name="client_phone[]" value="<?php echo htmlspecialchars($phone); ?>">
														<input type="hidden" name="clientphoneid[]" value="<?php echo htmlspecialchars($phoneId); ?>">
													</div>
													<?php
													}
												} else {
													// No validation errors - display from database
													$clientphones = \App\Models\ClientPhone::where('client_id', $fetchedData->id)->get();
													$iii = 0;
													$clientphonedata = array();

													if(count($clientphones)>0) {
														foreach($clientphones as $clientphone){
														?>
														<script>
														clientphonedata[<?php echo $iii; ?>] = { "contact_type" :'<?php echo $clientphone->contact_type; ?>',"country_code" :'<?php echo $clientphone->client_country_code; ?>',"phone" :'<?php echo $clientphone->client_phone; ?>'}
														</script>
														<div class="compact-contact-item" id="metatag2_{{$iii}}">
															<span class="contact-type-tag">{{ $clientphone->contact_type }}</span>
															<span class="contact-phone">{{$clientphone->client_country_code}} {{$clientphone->client_phone}}</span>
															<div class="contact-actions">
																<a href="javascript:;" 
																	class="editclientphone btn-edit" 
																	data-index="{{$iii}}" 
																	data-id="{{$clientphone->id}}"
																	data-type="{{$clientphone->contact_type}}"
																	data-country="{{$clientphone->client_country_code}}"
																	data-phone="{{$clientphone->client_phone}}"
																	title="Edit">
																	<i class="fa fa-edit"></i>
																</a>
																<?php if( isset($clientphone->contact_type) && $clientphone->contact_type == "Personal" ) {
																	$check_verified_phoneno = $clientphone->client_country_code."".$clientphone->client_phone;
																	$verifiedNumber = \App\Models\VerifiedNumber::where('phone_number',$check_verified_phoneno)->where('is_verified', true)->first();
																	if ($verifiedNumber) {
																		echo '<span class="verified-badge"><i class="fas fa-check-circle"></i></span>';
																	} else {
																		echo '<button type="button" class="btn-verify phone_verified" data-fname="'.$fetchedData->first_name.'" data-phone="'.$check_verified_phoneno.'" data-clientid="'.$fetchedData->id.'"><i class="fas fa-paper-plane"></i></button>';
																	}
																} ?>
																<?php if( isset($clientphone->contact_type) && $clientphone->contact_type != "Personal" ) { ?>
																	<a href="javascript:;" dataid="{{$iii}}" contactid="{{$clientphone->id}}" class="deletecontact btn-delete">
																		<i class="fa fa-trash"></i>
																	</a>
																<?php } ?>
															</div>
															<!-- Hidden fields -->
															<input type="hidden" name="contact_type[]" value="{{$clientphone->contact_type}}">
															<input type="hidden" name="client_country_code[]" value="{{$clientphone->client_country_code}}">
															<input type="hidden" name="client_phone[]" value="{{$clientphone->client_phone}}">
															<input type="hidden" name="clientphoneid[]" value="{{$clientphone->id}}">
														</div>
														<?php
														$iii++;
														}
													}
												} ?>
											</div>
										</div>
									</div>

									<!-- Email Section - Right Side -->
									<div class="col-md-6 col-sm-12">
										<div class="contact-subsection">
											<div class="d-flex justify-content-between align-items-center mb-2">
												<label class="section-label mb-0"><i class="fas fa-envelope"></i> Email Addresses</label>
												<a href="javascript:;" class="btn btn-xs btn-primary openclientemailnew">
													<i class="fa fa-plus"></i> Add
												</a>
											</div>
											@if ($errors->has('email'))
												<div class="mb-2">
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('email') }}</strong>
													</span>
												</div>
											@endif

											<script>
											var clientemaildata = new Array();
											</script>
											
											<div class="clientemaildata compact-contact-list">
												<?php
												// Check if old input exists (validation error occurred)
												$oldEmail = old('email');
												$oldEmailType = old('email_type');
												$oldAttEmail = old('att_email');
												
												if ($oldEmail !== null || $oldAttEmail !== null) {
													// Display old input from failed validation
													if($oldEmail) {
														$email_type = $oldEmailType ?: 'Personal';
														$email_verified = (isset($fetchedData->manual_email_phone_verified) && $fetchedData->manual_email_phone_verified == '1');
													?>
													<div class="compact-contact-item" id="email_main">
														<span class="contact-type-tag"><?php echo htmlspecialchars($email_type); ?></span>
														<span class="contact-email"><?php echo htmlspecialchars($oldEmail); ?></span>
														<div class="contact-actions">
															<a href="javascript:;" 
																class="editclientemail btn-edit" 
																data-email-id="main"
																data-type="<?php echo htmlspecialchars($email_type); ?>"
																data-email="<?php echo htmlspecialchars($oldEmail); ?>"
																title="Edit">
																<i class="fa fa-edit"></i>
															</a>
															<?php if($email_verified) { ?>
																<span class="verified-badge"><i class="fas fa-check-circle"></i></span>
															<?php } else { ?>
																<button type="button" class="btn-verify manual_email_phone_verified" data-fname="<?php echo $fetchedData->first_name;?>" data-email="<?php echo htmlspecialchars($oldEmail);?>" data-clientid="<?php echo $fetchedData->id;?>">
																	<i class="fas fa-paper-plane"></i>
																</button>
															<?php } ?>
														</div>
														<!-- Hidden fields -->
														<input type="hidden" name="email" value="<?php echo htmlspecialchars($oldEmail); ?>">
														<input type="hidden" name="email_type" value="<?php echo htmlspecialchars($email_type); ?>">
													</div>
													<?php } ?>
													
													<?php
													// Additional email from old input
													if($oldAttEmail) {
													?>
													<div class="compact-contact-item" id="email_additional">
														<span class="contact-type-tag">Additional</span>
														<span class="contact-email"><?php echo htmlspecialchars($oldAttEmail); ?></span>
														<div class="contact-actions">
															<a href="javascript:;" 
																class="editclientemail btn-edit" 
																data-email-id="additional"
																data-type="Additional"
																data-email="<?php echo htmlspecialchars($oldAttEmail); ?>"
																title="Edit">
																<i class="fa fa-edit"></i>
															</a>
															<a href="javascript:;" class="deleteemail btn-delete" data-email="email_additional">
																<i class="fa fa-trash"></i>
															</a>
														</div>
														<!-- Hidden field -->
														<input type="hidden" name="att_email" value="<?php echo htmlspecialchars($oldAttEmail); ?>">
													</div>
													<?php } ?>
												<?php } else {
													// No validation errors - display from database
													// Main email
													if(isset($fetchedData->email) && $fetchedData->email != "") {
														$email_type = isset($fetchedData->email_type) ? $fetchedData->email_type : 'Personal';
														$email_verified = (isset($fetchedData->manual_email_phone_verified) && $fetchedData->manual_email_phone_verified == '1');
													?>
													<div class="compact-contact-item" id="email_main">
														<span class="contact-type-tag">{{ $email_type }}</span>
														<span class="contact-email">{{ $fetchedData->email }}</span>
														<div class="contact-actions">
															<a href="javascript:;" 
																class="editclientemail btn-edit" 
																data-email-id="main"
																data-type="{{ $email_type }}"
																data-email="{{ $fetchedData->email }}"
																title="Edit">
																<i class="fa fa-edit"></i>
															</a>
															<?php if($email_verified) { ?>
																<span class="verified-badge"><i class="fas fa-check-circle"></i></span>
															<?php } else { ?>
																<button type="button" class="btn-verify manual_email_phone_verified" data-fname="<?php echo $fetchedData->first_name;?>" data-email="<?php echo $fetchedData->email;?>" data-clientid="<?php echo $fetchedData->id;?>">
																	<i class="fas fa-paper-plane"></i>
																</button>
															<?php } ?>
														</div>
														<!-- Hidden fields -->
														<input type="hidden" name="email" value="{{ $fetchedData->email }}">
														<input type="hidden" name="email_type" value="{{ $email_type }}">
													</div>
													<?php } ?>
													
													<?php
													// Additional email
													if(isset($fetchedData->att_email) && $fetchedData->att_email != "") {
													?>
													<div class="compact-contact-item" id="email_additional">
														<span class="contact-type-tag">Additional</span>
														<span class="contact-email">{{ $fetchedData->att_email }}</span>
														<div class="contact-actions">
															<a href="javascript:;" 
																class="editclientemail btn-edit" 
																data-email-id="additional"
																data-type="Additional"
																data-email="{{ $fetchedData->att_email }}"
																title="Edit">
																<i class="fa fa-edit"></i>
															</a>
															<a href="javascript:;" class="deleteemail btn-delete" data-email="email_additional">
																<i class="fa fa-trash"></i>
															</a>
														</div>
														<!-- Hidden field -->
														<input type="hidden" name="att_email" value="{{ $fetchedData->att_email }}">
													</div>
													<?php } ?>
												<?php } ?>
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
												<select class="form-control select2" name="visa_type">
													<option value="">- Select Visa Type -</option>
													@foreach(\App\Models\VisaType::orderby('name', 'ASC')->get() as $visalist)
														<option @if(old('visa_type', $fetchedData->visa_type) == $visalist->name) selected @endif value="{{$visalist->name}}">{{$visalist->name}}</option>
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
												{!! Form::text('visa_opt', old('visa_opt', $fetchedData->visa_opt), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Additional visa information' ))  !!}
											</div>
										</div>
										
										<?php
											$visa_expiry_date = old('visaExpiry');
											if (!$visa_expiry_date && $fetchedData->visaExpiry != '' && $fetchedData->visaExpiry != '0000-00-00'){
												$visa_expiry_date = date('d/m/Y', strtotime($fetchedData->visaExpiry));
											}
										?>	
										
										@if($fetchedData->visa_type!="Citizen" && $fetchedData->visa_type!="PR")
											<div class="col-md-4 col-sm-12">
												<div class="form-group"> 
													<label for="visaExpiry">Visa Expiry Date</label>
													<div class="input-group">
														<span class="input-group-text">
															<i class="fas fa-calendar-alt"></i>
														</span>
														{!! Form::text('visaExpiry', $visa_expiry_date, array('class' => 'form-control dobdatepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'DD/MM/YYYY' ))  !!}
														@if ($errors->has('visaExpiry'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('visaExpiry') }}</strong>
															</span> 
														@endif
													</div>
												</div>
											</div>
											
											<div class="col-md-4 col-sm-12">
												<div class="form-group"> 
													<label for="preferredIntake">Preferred Intake</label>
													<div class="input-group">
														<span class="input-group-text">
															<i class="fas fa-calendar-alt"></i>
														</span>
														{!! Form::text('preferredIntake', old('preferredIntake', @$fetchedData->preferredIntake), array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select intake date' ))  !!}
														@if ($errors->has('preferredIntake'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('preferredIntake') }}</strong>
															</span> 
														@endif
													</div>
												</div> 
											</div>
											
											<div class="col-md-4 col-sm-12">
												<div class="form-group"> 
													<label for="country_passport">Country of Passport</label>
													<select class="form-control select2" name="country_passport">
													<?php
														foreach(\App\Models\Country::all() as $list){
															?>
															<option <?php if(old('country_passport', @$fetchedData->country_passport) == $list->sortname){ echo 'selected'; } ?> value="{{@$list->sortname}}">{{@$list->name}}</option>
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
											
											<div class="col-md-4 col-sm-12">
												<div class="form-group"> 
													<label for="passport_number">Passport Number</label>
													{!! Form::text('passport_number', old('passport_number', @$fetchedData->passport_number), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter passport number' ))  !!}
													@if ($errors->has('passport_number'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('passport_number') }}</strong>
														</span> 
													@endif
												</div>
											</div>
										@endif
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
											{!! Form::text('address', old('address', @$fetchedData->address), array('placeholder'=>"Search address" , 'class' => 'form-control address-search-input', 'data-valid'=>'', 'autocomplete'=>'off' ))  !!}
											@if ($errors->has('address'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('address') }}</strong>
												</span>
											@endif
											<small class="form-text text-muted">Start typing to search address</small>
										</div>
										
										<div class="form-group">
											<label for="city">City</label>
											{!! Form::text('city', old('city', @$fetchedData->city), array('id' => 'locality', 'class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter city' ))  !!}
											@if ($errors->has('city'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('city') }}</strong>
												</span>
											@endif
										</div>
										
										<div class="form-group">
											<label for="zip">Post Code</label>
											{!! Form::text('zip', old('zip', @$fetchedData->zip), array('id' => 'postal_code', 'class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter postcode' ))  !!}
											@if ($errors->has('zip'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('zip') }}</strong>
												</span>
											@endif
										</div>
										
										<div class="form-group">
											<label for="state">State</label>
											<select class="form-control" name="state">
												<option value="">- Select State -</option>	
												<option value="Australian Capital Territory" @if(old('state', @$fetchedData->state) == "Australian Capital Territory") selected @endif>Australian Capital Territory</option>
												<option value="New South Wales" @if(old('state', @$fetchedData->state) == "New South Wales") selected @endif>New South Wales</option>
												<option value="Northern Territory" @if(old('state', @$fetchedData->state) == "Northern Territory") selected @endif>Northern Territory</option>
												<option value="Queensland" @if(old('state', @$fetchedData->state) == "Queensland") selected @endif>Queensland</option>
												<option value="South Australia" @if(old('state', @$fetchedData->state) == "South Australia") selected @endif>South Australia</option>
												<option value="Tasmania" @if(old('state', @$fetchedData->state) == "Tasmania") selected @endif>Tasmania</option>
												<option value="Victoria" @if(old('state', @$fetchedData->state) == "Victoria") selected @endif>Victoria</option>
												<option value="Western Australia" @if(old('state', @$fetchedData->state) == "Western Australia") selected @endif>Western Australia</option>
											</select>
											@if ($errors->has('state'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('state') }}</strong>
												</span>
											@endif
										</div>
										
										<div class="form-group">
											<label for="country">Country</label>
											<select class="form-control select2" id="country_select" name="country">
											<?php
												foreach(\App\Models\Country::all() as $list){
													?>
													<option <?php if(old('country', @$fetchedData->country) == $list->sortname){ echo 'selected'; } ?> value="{{@$list->sortname}}">{{@$list->name}}</option>
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
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('related_files') }}</strong>
										</span>
									@endif
								</div>
							</div>
						</section>
					</div>

					<!-- Additional Contact (Collapsible) -->
					<div class="row mt-3">
						<div class="col-12">
							<div class="card section-card additional-contact-section" <?php if(
								( isset($fetchedData->att_email) && $fetchedData->att_email != "")
								||
								( isset($fetchedData->att_phone) && $fetchedData->att_phone != "")
							) { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php }?>>
								<div class="card-body">
									<section class="form-section">
										<h3><i class="fas fa-plus-circle"></i> Additional Contact</h3>
									<div class="row">
										<div class="col-md-6 col-sm-12">
											<div class="form-group"> 
												<label for="att_email">Additional Email</label>
												{!! Form::text('att_email', old('att_email', @$fetchedData->att_email), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter additional email' ))  !!}
												@if ($errors->has('att_email'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('att_email') }}</strong>
													</span> 
												@endif
											</div>
										</div>
										
										<div class="col-md-6 col-sm-12">
											<div class="form-group"> 
												<label for="att_phone">Additional Phone</label>
												<div class="cus_field_input">
													<div class="country_code"> 
														@include('partials.country-code-select', [
															'name' => 'att_country_code',
															'selected' => old('att_country_code', $fetchedData->att_country_code ?? \App\Helpers\PhoneHelper::getDefaultCountryCode())
														])
													</div>	
													{!! Form::text('att_phone', old('att_phone', @$fetchedData->att_phone), array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter additional phone' ))  !!}
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

					<!-- Professional Details Section (for non-Citizen/PR visa types) -->
					@if($fetchedData->visa_type!="Citizen" && $fetchedData->visa_type!="PR")
					<div class="form-content-section">
						<section class="form-section">
							<h3><i class="fas fa-briefcase"></i> Professional Details</h3>
							<div class="content-grid">
								<div class="form-group">
									<label for="nomi_occupation">Nominated Occupation</label>
									{!! Form::text('nomi_occupation', old('nomi_occupation', @$fetchedData->nomi_occupation), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter occupation' ))  !!}
									@if ($errors->has('nomi_occupation'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('nomi_occupation') }}</strong>
										</span>
									@endif
								</div>
								<div class="form-group">
									<label for="skill_assessment">Skill Assessment</label>
									<select class="form-control" name="skill_assessment">
										<option value="">Select</option>
										<option @if(old('skill_assessment', $fetchedData->skill_assessment) == 'Yes') selected @endif value="Yes">Yes</option>
										<option @if(old('skill_assessment', $fetchedData->skill_assessment) == 'No') selected @endif value="No">No</option>
									</select>
									@if ($errors->has('skill_assessment'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('skill_assessment') }}</strong>
										</span>
									@endif
								</div>
								<div class="form-group">
									<label for="high_quali_aus">Highest Qualification in Australia</label>
									{!! Form::text('high_quali_aus', old('high_quali_aus', @$fetchedData->high_quali_aus), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter qualification' ))  !!}
									@if ($errors->has('high_quali_aus'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('high_quali_aus') }}</strong>
										</span>
									@endif
								</div>
								<div class="form-group">
									<label for="high_quali_overseas">Highest Qualification Overseas</label>
									{!! Form::text('high_quali_overseas', old('high_quali_overseas', @$fetchedData->high_quali_overseas), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter qualification' ))  !!}
									@if ($errors->has('high_quali_overseas'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('high_quali_overseas') }}</strong>
										</span>
									@endif
								</div>
								<div class="form-group">
									<label for="relevant_work_exp_aus">Relevant work experience in Australia</label>
									{!! Form::text('relevant_work_exp_aus', old('relevant_work_exp_aus', @$fetchedData->relevant_work_exp_aus), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'e.g., 2 years' ))  !!}
									@if ($errors->has('relevant_work_exp_aus'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('relevant_work_exp_aus') }}</strong>
										</span>
									@endif
								</div>
								<div class="form-group">
									<label for="relevant_work_exp_over">Relevant work experience in Overseas</label>
									{!! Form::text('relevant_work_exp_over', old('relevant_work_exp_over', @$fetchedData->relevant_work_exp_over), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'e.g., 5 years' ))  !!}
									@if ($errors->has('relevant_work_exp_over'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('relevant_work_exp_over') }}</strong>
										</span>
									@endif
								</div>
							</div>
						</section>
									
									<!-- English Test Scores & Additional Information Section -->
									<section class="form-section">
										<h3><i class="fas fa-language"></i> English Test Scores & Additional Information</h3>
										<?php
											$testscores = \App\Models\TestScore::where('client_id', $fetchedData->id)->where('type', 'client')->first();
										?>
									<div class="english-test-wrapper">
										<?php
										// Determine which test type has data
										$activeTestType = 'toefl'; // default
										if ($testscores) {
											if (!empty($testscores->ilets_Listening) || !empty($testscores->ilets_Reading) || 
												!empty($testscores->ilets_Writing) || !empty($testscores->ilets_Speaking)) {
												$activeTestType = 'ilets';
											} elseif (!empty($testscores->pte_Listening) || !empty($testscores->pte_Reading) || 
													  !empty($testscores->pte_Writing) || !empty($testscores->pte_Speaking)) {
												$activeTestType = 'pte';
											}
										}
										$activeTestType = old('test_type', $activeTestType); // Use old input if available
										?>
										<div class="row g-3 mb-3">
											<div class="col-md-3 col-sm-6">
												<div class="form-group">
													<label for="test_type">Test Type</label>
													<select class="form-control" name="test_type" id="test_type" onchange="loadTestScoresEditPage()">
														<option value="toefl" @if($activeTestType == 'toefl') selected @endif>TOEFL</option>
														<option value="ilets" @if($activeTestType == 'ilets') selected @endif>IELTS</option>
														<option value="pte" @if($activeTestType == 'pte') selected @endif>PTE</option>
													</select>
												</div>
											</div>
											<div class="col-md-auto col-sm-4 col-6">
												<div class="form-group">
													<label for="listening_edit">L</label>
													<input type="number" class="form-control" name="listening" id="listening_edit" value="{{ old('listening') }}" step="0.01" placeholder="0.00" min="0" style="width: 80px;"/>
												</div>
											</div>
											<div class="col-md-auto col-sm-4 col-6">
												<div class="form-group">
													<label for="reading_edit">R</label>
													<input type="number" class="form-control" name="reading" id="reading_edit" value="{{ old('reading') }}" step="0.01" placeholder="0.00" min="0" style="width: 80px;"/>
												</div>
											</div>
											<div class="col-md-auto col-sm-4 col-6">
												<div class="form-group">
													<label for="writing_edit">W</label>
													<input type="number" class="form-control" name="writing" id="writing_edit" value="{{ old('writing') }}" step="0.01" placeholder="0.00" min="0" style="width: 80px;"/>
												</div>
											</div>
											<div class="col-md-auto col-sm-4 col-6">
												<div class="form-group">
													<label for="speaking_edit">S</label>
													<input type="number" class="form-control" name="speaking" id="speaking_edit" value="{{ old('speaking') }}" step="0.01" placeholder="0.00" min="0" style="width: 80px;"/>
												</div>
											</div>
											<div class="col-md-auto col-sm-4 col-6">
												<div class="form-group">
													<label for="overall_edit">O</label>
													<input type="number" class="form-control" name="overall" id="overall_edit" value="{{ old('overall') }}" step="0.01" placeholder="0.00" min="0" style="width: 80px;"/>
												</div>
											</div>
											</div>
										<div class="row g-3">
											<div class="col-md-3 col-sm-6">
												<div class="form-group">
													<label for="test_date_edit">Test Date</label>
													<input type="text" class="form-control datepicker" name="test_date" id="test_date_edit" value="{{ old('test_date') }}" placeholder="Select date"/>
												</div>
											</div>
										</div>
										</div>
										<input type="hidden" name="test_score_client_id" value="{{$fetchedData->id}}">
										<input type="hidden" name="test_score_type" value="client">
										<script>
										function loadTestScoresEditPage() {
											var testType = document.getElementById('test_type').value;
											var testscores = @json($testscores);
											
											if (!testscores) {
												document.getElementById('listening_edit').value = '';
												document.getElementById('reading_edit').value = '';
												document.getElementById('writing_edit').value = '';
												document.getElementById('speaking_edit').value = '';
												document.getElementById('overall_edit').value = '';
												document.getElementById('test_date_edit').value = '';
												return;
											}
											
											if (testType === 'toefl') {
												document.getElementById('listening_edit').value = testscores.toefl_Listening || '';
												document.getElementById('reading_edit').value = testscores.toefl_Reading || '';
												document.getElementById('writing_edit').value = testscores.toefl_Writing || '';
												document.getElementById('speaking_edit').value = testscores.toefl_Speaking || '';
												document.getElementById('overall_edit').value = testscores.score_1 || '';
												document.getElementById('test_date_edit').value = testscores.toefl_Date || '';
											} else if (testType === 'ilets') {
												document.getElementById('listening_edit').value = testscores.ilets_Listening || '';
												document.getElementById('reading_edit').value = testscores.ilets_Reading || '';
												document.getElementById('writing_edit').value = testscores.ilets_Writing || '';
												document.getElementById('speaking_edit').value = testscores.ilets_Speaking || '';
												document.getElementById('overall_edit').value = testscores.score_2 || '';
												document.getElementById('test_date_edit').value = testscores.ilets_Date || '';
											} else if (testType === 'pte') {
												document.getElementById('listening_edit').value = testscores.pte_Listening || '';
												document.getElementById('reading_edit').value = testscores.pte_Reading || '';
												document.getElementById('writing_edit').value = testscores.pte_Writing || '';
												document.getElementById('speaking_edit').value = testscores.pte_Speaking || '';
												document.getElementById('overall_edit').value = testscores.score_3 || '';
												document.getElementById('test_date_edit').value = testscores.pte_Date || '';
											}
										}
										// Load initial data on page load
										document.addEventListener('DOMContentLoaded', function() {
											loadTestScoresEditPage();
										});
										</script>
										
										<div class="content-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; margin-bottom: 12px; margin-top: 20px;">
											<div class="form-group">
												<?php
												$explodeenaati_py = array();
												if($fetchedData->naati_py != ''){
													$explodeenaati_py = explode(',', $fetchedData->naati_py);
												} 
												?>
												<label for="naati_py">Naati/PY</label>
												<div class="naati-checkbox-wrapper">
													<label class="naati-checkbox-item" for="Naati">
														<input type="checkbox" id="Naati" value="Naati" name="naati_py[]" <?php if(in_array('Naati', $explodeenaati_py)){ echo 'checked'; } ?>>
														<span class="naati-checkbox-label">Naati</span>
													</label>
													<label class="naati-checkbox-item" for="py">
														<input type="checkbox" id="py" value="PY" name="naati_py[]" <?php if(in_array('PY', $explodeenaati_py)){ echo 'checked'; } ?>>
														<span class="naati-checkbox-label">PY</span>
													</label>
												</div>
											</div>
											<div class="form-group">
												<label for="total_points">Total Points</label>
												{!! Form::text('total_points', old('total_points', @$fetchedData->total_points), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter points' ))  !!}
												@if ($errors->has('total_points'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('total_points') }}</strong>
													</span>
												@endif
											</div>
											<div class="form-group">
												<label for="start_process">When You want to start Process</label>
												<select class="form-control" name="start_process">
													<option value="">Select</option>
													<option @if(old('start_process', $fetchedData->start_process) == 'As soon As Possible') selected @endif value="As soon As Possible">As soon As Possible</option>
													<option @if(old('start_process', $fetchedData->start_process) == 'In Next 3 Months') selected @endif value="In Next 3 Months">In Next 3 Months</option>
													<option @if(old('start_process', $fetchedData->start_process) == 'In Next 6 Months') selected @endif value="In Next 6 Months">In Next 6 Months</option>
													<option @if(old('start_process', $fetchedData->start_process) == 'Advise Only') selected @endif value="Advise Only">Advise Only</option>
												</select>
												@if ($errors->has('start_process'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('start_process') }}</strong>
													</span>
												@endif
											</div>
										</div>
									</section>
								</div>
								@endif

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
												<option @if(old('service', $fetchedData->service) == $leadservlist->name) selected @endif value="{{$leadservlist->name}}">{{$leadservlist->name}}</option>
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
                                            if( !empty($fetchedData->assignee) )
                                            {
                                                if ( str_contains($fetchedData->assignee, ',') ) {
                                                    $assigneeArr = explode(",",$fetchedData->assignee);
                                                } else {
                                                    $assigneeArr = array($fetchedData->assignee);
                                                }

                                                $admins = \App\Models\Admin::where('role','!=',7)->orderby('first_name','ASC')->get();
                                                foreach($admins as $admin)
                                                {
                                                    $branchname = \App\Models\Branch::where('id',$admin->office_id)->first();
                                                    foreach($assigneeArr as $assigneeKey=>$assigneeVal ) {
                                            ?>
                                                <option @if($assigneeVal == $admin->id) selected @endif value="<?php echo $admin->id; ?>"><?php echo $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')'; ?></option>
                                            <?php
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                $assigneeArr = array();
                                                $admins = \App\Models\Admin::where('role','!=',7)->orderby('first_name','ASC')->get();
                                                foreach($admins as $admin){
                                                    $branchname = \App\Models\Branch::where('id',$admin->office_id)->first();
                                                ?>
                                                <option @if($fetchedData->assignee == $admin->id) selected @endif value="<?php echo $admin->id; ?>"><?php echo $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')'; ?></option>
                                            <?php
                                                }
                                            } ?>

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
												<option value="Unassigned" @if(old('status', @$fetchedData->status) == "Unassigned") selected @endif>Unassigned</option>
												<option value="Assigned" @if(old('status', @$fetchedData->status) == "Assigned") selected @endif>Assigned</option>
												<option value="In-Progress" @if(old('status', @$fetchedData->status) == "In-Progress") selected @endif>In-Progress</option>
												<option value="Closed" @if(old('status', @$fetchedData->status) == "Closed") selected @endif>Closed</option>
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
												<option value="1" @if(old('lead_quality', @$fetchedData->lead_quality) == "1") selected @endif>1</option>
												<option value="2" @if(old('lead_quality', @$fetchedData->lead_quality) == "2") selected @endif>2</option>
												<option value="3" @if(old('lead_quality', @$fetchedData->lead_quality) == "3") selected @endif>3</option>
												<option value="4" @if(old('lead_quality', @$fetchedData->lead_quality) == "4") selected @endif>4</option>
												<option value="5" @if(old('lead_quality', @$fetchedData->lead_quality) == "5") selected @endif>5</option>
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
												<option value="Sub Agent" @if(old('source', @$fetchedData->source) == 'Sub Agent') selected @endif>Sub Agent</option>
												@foreach(\App\Models\Source::all() as $sources)
													<option value="{{$sources->name}}" @if(old('source', @$fetchedData->source) == $sources->name) selected @endif>{{$sources->name}}</option>
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
													<option <?php if(old('subagent', @$fetchedData->agent_id) == $agentlist->id){ echo 'selected'; } ?> value="{{$agentlist->id}}">{{$agentlist->full_name}}</option>
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
											<input
												type="text"
												id="tagname_input"
												name="tagname"
												class="form-control"
												placeholder="e.g. VIP, Follow up, IELTS"
												value="{{ trim($fetchedData->tagname ?? '') }}"
											/>
											<small class="form-text text-muted">Separate tags with commas.</small>
										</div>
									</div>
                                  
									<!-- Services Taken Section -->
									<div class="col-12 mt-4">
										<div class="services-taken-header d-flex justify-content-between align-items-center mb-3">
											<h6 class="mb-0"><i class="fas fa-briefcase"></i> Services Taken</h6>
											<a href="javascript:;" data-id="{{$fetchedData->id}}" class="btn btn-sm btn-primary serviceTaken">
												<i class="fa fa-plus"></i> Add Service
											</a>
										</div>
                                       
									   <div id="service_taken_complete" style="display:none;"></div>

										<div class="services-taken-grid">
											<?php
											$serviceTakenArr = \App\Models\clientServiceTaken::where('client_id', $fetchedData->id )->orderBy('created_at', 'desc')->get();
											if( !empty($serviceTakenArr) && count($serviceTakenArr) > 0 ){
												foreach ($serviceTakenArr as $tokenkey => $tokenval) {
													$serviceClass = strtolower($tokenval['service_type']);
													?>
													<div class="service-card service-card-<?php echo $serviceClass; ?>" id="service-card-<?php echo $tokenval['id']; ?>">
														<div class="service-card-header">
															<span class="service-type-badge badge badge-<?php echo $serviceClass == 'migration' ? 'primary' : 'info'; ?>">
																<?php echo $tokenval['service_type']; ?>
															</span>
															<div class="service-actions">
																<a href="javascript:;" class="service_taken_edit text-primary" id="<?php echo $tokenval['id']; ?>" title="Edit">
																	<i class="fa fa-edit"></i>
																</a>
																<a href="javascript:;" class="service_taken_trash text-danger ms-2" id="<?php echo $tokenval['id']; ?>" title="Delete">
																	<i class="fa fa-trash"></i>
																</a>
															</div>
														</div>
														<div class="service-card-body">
															<?php if($tokenval['service_type'] == "Migration") { ?>
																<div class="service-detail">
																	<span class="detail-label">Reference No:</span>
																	<span class="detail-value"><?php echo htmlspecialchars($tokenval['mig_ref_no']); ?></span>
																</div>
																<div class="service-detail">
																	<span class="detail-label">Service:</span>
																	<span class="detail-value"><?php echo htmlspecialchars($tokenval['mig_service']); ?></span>
																</div>
																<div class="service-detail">
																	<span class="detail-label">Notes:</span>
																	<span class="detail-value"><?php echo htmlspecialchars($tokenval['mig_notes']); ?></span>
																</div>
															<?php } else if($tokenval['service_type'] == "Education") { ?>
																<div class="service-detail">
																	<span class="detail-label">Course:</span>
																	<span class="detail-value"><?php echo htmlspecialchars($tokenval['edu_course']); ?></span>
																</div>
																<div class="service-detail">
																	<span class="detail-label">College:</span>
																	<span class="detail-value"><?php echo htmlspecialchars($tokenval['edu_college']); ?></span>
																</div>
																<div class="service-detail">
																	<span class="detail-label">Start Date:</span>
																	<span class="detail-value"><?php echo htmlspecialchars($tokenval['edu_service_start_date']); ?></span>
																</div>
																<div class="service-detail">
																	<span class="detail-label">Notes:</span>
																	<span class="detail-value"><?php echo htmlspecialchars($tokenval['edu_notes']); ?></span>
																</div>
															<?php } ?>
														</div>
													</div>
													<?php
												}
											} else {
												echo '<div class="no-services-message">';
												echo '<i class="fas fa-inbox fa-3x text-muted mb-3"></i>';
												echo '<p class="text-muted">No services have been added yet.</p>';
												echo '<p class="text-muted"><small>Click "Add Service" to create a new service record.</small></p>';
												echo '</div>';
											}
											?>
										</div>
									</div>
                                  
									<div class="col-sm-12">
										<div class="form-group">
											<label for="comments_note">Comments / Note</label>
											<textarea class="form-control" name="comments_note" placeholder="" data-valid="">{{@$fetchedData->comments_note}}</textarea>
											@if ($errors->has('comments_note')) 
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('comments_note') }}</strong>
												</span> 
											@endif
										</div>
									</div>  
									<div class="col-sm-12">
										<div class="form-group float-end">
                                             <div class="removesids_contact"></div>
											{!! Form::button('Save', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("edit-clients")' ])  !!}
										</div>
									</div>
								</div>
								</section> 
							</div>
					</div>	
				</div>
			</div>  
			</form>
		</div>
	</section>
</div>
 <?php if($fetchedData->related_files != ''){
     $exploderel = explode(',', $fetchedData->related_files);
     foreach($exploderel AS $EXP){ 
         // PostgreSQL doesn't accept empty strings for integer columns - filter empty values
         if(!empty(trim($EXP)) && trim($EXP) !== '') {
             $relatedclients = \App\Models\Admin::where('id', trim($EXP))->first();	
             if($relatedclients) {
    ?>
 <input type="hidden" class="relatedfile" data-email="<?php echo $relatedclients->email; ?>" data-name="<?php echo $relatedclients->first_name.' '.$relatedclients->last_name; ?>" data-id="<?php echo $relatedclients->id; ?>">
    <?php
            }
        }
     }
 } ?>

<?php
if($fetchedData->tagname != ''){
   $tagnameArr = explode(',', $fetchedData->tagname);
   foreach($tagnameArr AS $tag1){
       $tagWord = \App\Models\Tag::where('name', trim($tag1))->first();
       if($tagWord){
   ?>
<input type="hidden" class="relatedtag" data-name="<?php echo $tagWord->name; ?>" data-id="<?php echo $tagWord->id; ?>">
<?php
       }
   }
} ?>

<div class="modal fade custom_modal" id="serviceTaken" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel">Service Taken</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
                <form name="createservicetaken" id="createservicetaken" autocomplete="off">
				@csrf
                    <input id="logged_client_id" name="logged_client_id"  type="hidden" value="<?php echo $fetchedData->id;?>">
					<input type="hidden" name="entity_type" id="entity_type" value="add">
                    <input type="hidden" name="entity_id" id="entity_id" value="">
                    <div class="row">
						<div class="col-12 col-md-12 col-lg-12">

							<div class="form-group">
								<label style="display:block;" for="service_type">Select Service Type:</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="Migration_inv" value="Migration" name="service_type" checked>
									<label class="form-check-label" for="Migration_inv">Migration</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="Eductaion_inv" value="Education" name="service_type">
									<label class="form-check-label" for="Eductaion_inv">Education</label>
								</div>
								<span class="custom-error service_type_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12 is_Migration_inv">
                            <div class="form-group">
								<label for="mig_ref_no">Reference No: <span class="span_req">*</span></label>
                                <input type="text" name="mig_ref_no" id="mig_ref_no" value="" class="form-control" data-valid="required">
                            </div>

                            <div class="form-group">
								<label for="mig_service">Service: <span class="span_req">*</span></label>
                                <input type="text" name="mig_service" id="mig_service" value="" class="form-control" data-valid="required">
                            </div>

                            <div class="form-group">
								<label for="mig_notes">Notes: <span class="span_req">*</span></label>
                                <input type="text" name="mig_notes" id="mig_notes" value="" class="form-control" data-valid="required">
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12 is_Eductaion_inv" style="display:none;">
                            <div class="form-group">
								<label for="edu_course">Course: <span class="span_req">*</span></label>
                                <input type="text" name="edu_course" id="edu_course" value="" class="form-control">
                            </div>

                            <div class="form-group">
								<label for="edu_college">College: <span class="span_req">*</span></label>
                                <input type="text" name="edu_college" id="edu_college" value="" class="form-control">
                            </div>

                            <div class="form-group">
								<label for="edu_service_start_date">Service Start Date: <span class="span_req">*</span></label>
                                <input type="text" name="edu_service_start_date" id="edu_service_start_date" value="" class="form-control">
                            </div>

                            <div class="form-group">
								<label for="edu_notes">Notes: <span class="span_req">*</span></label>
                                <input type="text" name="edu_notes" id="edu_notes" value="" class="form-control">
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('createservicetaken')" type="button" class="btn btn-primary" id="createservicetaken_btn">Save</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<div class="modal fade addclientphone custom_modal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="clientPhoneModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientPhoneModalLabel">Add New Phone</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form method="post" id="clientphoneform" autocomplete="off" enctype="multipart/form-data">
					<input type="hidden" id="edit_phone_mode" value="0">
					<input type="hidden" id="edit_phone_id" value="">
					<input type="hidden" id="edit_phone_index" value="">
					<div class="row">
                        <div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="contact_type">Contact Type <span style="color:#ff0000;">*</span></label>
								<select name="contact_type[]" id="contact_type" class="form-control">
                                    <option value="">Select</option>
                                    <option value="Personal">Personal</option>
                                    <option value="Business">Business</option>
                                    <option value="Secondary">Secondary</option>
                                    <option value="Father">Father</option>
                                    <option value="Mother">Mother</option>
                                    <option value="Brother">Brother</option>
                                    <option value="Sister">Sister</option>
                                    <option value="Uncle">Uncle</option>
                                    <option value="Aunt">Aunt</option>
                                    <option value="Cousin">Cousin</option>
                                    <option value="Others">Others</option>
                                    <option value="Partner">Partner</option>
                                    <option value="Not In Use">Not In Use</option>
                                </select>
								<span class="custom-error contact_type_error" role="alert">
									<strong></strong>
								</span>
								@if ($errors->has('contact_type'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('contact_type') }}</strong>
									</span>
								@endif
							</div>
						</div>

                        <div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="client_phone">Phone Number </label>
								<div class="cus_field_input">
									<div class="country_code">
										@include('partials.country-code-select', [
											'name' => 'client_country_code',
											'selected' => null
										])
									</div>
									{!! Form::text('client_phone', '', array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Phone' ))  !!}
									<span class="custom-error client_phone_error" role="alert">
										<strong></strong>
									</span>
									@if ($errors->has('client_phone'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('client_phone') }}</strong>
										</span>
									@endif
								</div>
							</div>
						</div>

                        <div class="col-12 col-md-12 col-lg-12">
							<button type="button" class="btn btn-primary saveclientphone">Save</button>
							<button type="button" id="update_clientphone" style="display:none" class="btn btn-primary">Update</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<!-- Add Client Email Modal -->
<div class="modal fade addclientemail custom_modal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="clientEmailModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientEmailModalLabel">Add New Email</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form method="post" id="clientemailform" autocomplete="off" enctype="multipart/form-data">
					<input type="hidden" id="edit_email_mode" value="0">
					<input type="hidden" id="edit_email_id" value="">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_type_modal">Email Type <span style="color:#ff0000;">*</span></label>
								<select name="email_type_modal" id="email_type_modal" class="form-control">
									<option value="">Select</option>
									<option value="Personal">Personal</option>
									<option value="Business">Business</option>
									<option value="Secondary">Secondary</option>
									<option value="Additional">Additional</option>
								</select>
								@if ($errors->has('email_type_modal'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_type_modal') }}</strong>
									</span>
								@endif
							</div>
						</div>

						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="client_email">Email Address <span style="color:#ff0000;">*</span></label>
								{!! Form::text('client_email', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter email address' ))  !!}
								@if ($errors->has('client_email'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('client_email') }}</strong>
									</span>
								@endif
							</div>
						</div>

					<div class="col-12 col-md-12 col-lg-12">
						<button type="button" class="btn btn-primary saveclientemail">Save</button>
						<button type="button" id="update_clientemail" style="display:none" class="btn btn-primary">Update</button>
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Verify Phone-->
<div id="verifyphonemodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
                <div class="mb-4" id="verificationSection">
                    <h5>Verify Phone Number</h5>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="verify_phone_number" placeholder="" value="">
                        <button class="btn btn-outline-secondary" type="button" id="sendCodeBtn">Send Code</button>
                    </div>

                    <div id="verificationCodeSection" style="display: none;">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="verification_code" placeholder="Enter verification code">
                            <button class="btn btn-outline-secondary" type="button" id="verifyCodeBtn">Verify Code</button>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>

@endsection

@section('scripts')

@if($showAlert)
    <script>
        alert("Have u updated the following details - email address,current address,current visa,visa expiry,other fields? Pls update these details before forwarding this to anyone?");
    </script>
@endif

<!-- Configuration for Page-Specific JavaScript -->
<script>
    window.AppConfig = window.AppConfig || {};
    window.PageConfig = window.PageConfig || {};
    
    // CSRF Token
    AppConfig.csrf = '{{ csrf_token() }}';
    
    // API URLs
    AppConfig.urls = {
        siteUrl: '{{ url("/") }}',
        verifyEmail: '{{ route("verify.send-code") }}',
        checkCode: '{{ route("verify.check-code") }}',
        getRecipients: '{{ url("/clients/get-recipients") }}',
        checkClientExist: '{{ url("/checkclientexist") }}',
        getServiceTaken: '{{ url("/client/getservicetaken") }}',
        createServiceTaken: '{{ url("/client/createservicetaken") }}',
        removeServiceTaken: '{{ url("/client/removeservicetaken") }}',
        emailVerify: '{{ url("/email-verify") }}',
        verifySendCode: '{{ route("verify.send-code") }}',
        verifyCheckCode: '{{ route("verify.check-code") }}'
    };
    
    // Page-specific data
    PageConfig.clientId = {{ $fetchedData->id }};
    PageConfig.clientFirstName = '{{ $fetchedData->first_name }}';
    PageConfig.source = '{{ $fetchedData->source ?? "" }}';
    
    @if(isset($relatedfiles) && !empty($relatedfiles))
    PageConfig.relatedFilesData = {!! json_encode($relatedfiles) !!};
    @else
    PageConfig.relatedFilesData = [];
    @endif
</script>

{{-- Common JavaScript Files (load first) --}}
<script src="{{ asset('js/common/config.js') }}"></script>
<script src="{{ asset('js/common/ajax-helpers.js') }}"></script>
<script src="{{ asset('js/common/utilities.js') }}"></script>
<script src="{{ asset('js/common/ui-components.js') }}"></script>
<script src="{{ asset('js/common/google-maps.js') }}"></script>

{{-- Page-Specific JavaScript (load last) --}}
<script src="{{ asset('js/pages/admin/client-edit.js') }}"></script>

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
    // Use 'change' event only - label's 'for' attribute handles clicks automatically
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