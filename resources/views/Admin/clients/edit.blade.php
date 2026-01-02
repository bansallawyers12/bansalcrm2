@extends('layouts.admin')
@section('title', 'Edit Client')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
	     <div class="server-error">
				@include('../Elements/flash-message')
			</div>
		<div class="section-body">
			<form action="{{ url('admin/clients/edit') }}" method="POST" name="edit-clients" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="id" value="{{ @$fetchedData->id }}">
				<input type="hidden" name="type" value="{{ @$fetchedData->type }}"> 
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
								    <a href="{{route('admin.clients.index')}}" class="btn btn-outline-secondary me-2">
								    	<i class="fa fa-arrow-left"></i> Back
								    </a>
								    <button type="submit" class="btn btn-primary" onclick="customValidate('edit-clients')">
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
							<div class="card-header bg-light">
								<h5 class="mb-0">
									<i class="fas fa-user text-primary"></i> Basic Information
								</h5>
							</div>
							<div class="card-body">
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
											{!! Form::text('first_name', @$fetchedData->first_name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter first name' ))  !!}
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
											{!! Form::text('last_name', @$fetchedData->last_name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter last name' ))  !!}
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
												<option value="Male" @if(@$fetchedData->gender == "Male") selected @endif>Male</option>
												<option value="Female" @if(@$fetchedData->gender == "Female") selected @endif>Female</option>
												<option value="Other" @if(@$fetchedData->gender == "Other") selected @endif>Other</option>
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
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fas fa-calendar-alt"></i>
													</div>
												</div>
												<?php
													if($fetchedData->dob != ''){
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
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fas fa-calendar-alt"></i>
													</div>
												</div>
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
												<option value="Married" @if(@$fetchedData->martial_status == "Married") selected @endif>Married</option>
												<option value="Never Married" @if(@$fetchedData->martial_status == "Never Married") selected @endif>Never Married</option>
												<option value="Engaged" @if(@$fetchedData->martial_status == "Engaged") selected @endif>Engaged</option>
												<option value="Divorced" @if(@$fetchedData->martial_status == "Divorced") selected @endif>Divorced</option>
												<option value="Separated" @if(@$fetchedData->martial_status == "Separated") selected @endif>Separated</option>
												<option value="De facto" @if(@$fetchedData->martial_status == "De facto") selected @endif>De facto</option>
												<option value="Widowed" @if(@$fetchedData->martial_status == "Widowed") selected @endif>Widowed</option>
												<option value="Others" @if(@$fetchedData->martial_status == "Others") selected @endif>Others</option>
											</select>
											@if ($errors->has('martial_status'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('martial_status') }}</strong>
												</span> 
											@endif
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Section 2: Contact Information -->
				<div class="row mt-3">
					<div class="col-12">
						<div class="card section-card">
							<div class="card-header bg-light">
								<h5 class="mb-0">
									<i class="fas fa-address-book text-primary"></i> Contact Information
								</h5>
							</div>
							<div class="card-body compact-contact-section">
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

											<script>
											var clientphonedata = new Array();
											</script>
											
											<div class="clientphonedata compact-contact-list">
												<?php
												$clientphones = \App\Models\ClientPhone::where('client_id', $fetchedData->id)->get();
												$iii=0;
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
															<?php if( isset($clientphone->contact_type) && $clientphone->contact_type == "Personal" ) {
																$check_verified_phoneno = $clientphone->client_country_code."".$clientphone->client_phone;
																$verifiedNumber = \App\Models\VerifiedNumber::where('phone_number',$check_verified_phoneno)->where('is_verified', true)->first();
																if ($verifiedNumber) {
																	echo '<span class="verified-badge"><i class="fas fa-check-circle"></i></span>';
																} else {
																	echo '<button type="button" class="btn-verify phone_verified" data-fname="'.$fetchedData->first_name.'" data-phone="'.$check_verified_phoneno.'" data-clientid="'.$fetchedData->id.'"><i class="fas fa-check"></i></button>';
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

											<script>
											var clientemaildata = new Array();
											</script>
											
											<div class="clientemaildata compact-contact-list">
												<?php
												// Main email
												if(isset($fetchedData->email) && $fetchedData->email != "") {
													$email_type = isset($fetchedData->email_type) ? $fetchedData->email_type : 'Personal';
													$email_verified = (isset($fetchedData->manual_email_phone_verified) && $fetchedData->manual_email_phone_verified == '1');
												?>
												<div class="compact-contact-item" id="email_main">
													<span class="contact-type-tag">{{ $email_type }}</span>
													<span class="contact-email">{{ $fetchedData->email }}</span>
													<div class="contact-actions">
														<?php if($email_verified) { ?>
															<span class="verified-badge"><i class="fas fa-check-circle"></i></span>
														<?php } else { ?>
															<button type="button" class="btn-verify manual_email_phone_verified" data-fname="<?php echo $fetchedData->first_name;?>" data-email="<?php echo $fetchedData->email;?>" data-clientid="<?php echo $fetchedData->id;?>">
																<i class="fas fa-check"></i>
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
														<a href="javascript:;" class="deleteemail btn-delete" data-email="email_additional">
															<i class="fa fa-trash"></i>
														</a>
													</div>
													<!-- Hidden field -->
													<input type="hidden" name="att_email" value="{{ $fetchedData->att_email }}">
												</div>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Section 3: Visa & Passport Information -->
					<div class="row mt-3">
						<div class="col-12">
							<div class="card section-card">
								<div class="card-header bg-light">
									<h5 class="mb-0">
										<i class="fas fa-passport text-primary"></i> Visa & Passport Information
									</h5>
								</div>
								<div class="card-body">
									<div class="row">
										<div class="col-md-4 col-sm-12">
											<div class="form-group"> 
												<label for="visa_type">Visa Type</label>
												<select class="form-control select2" name="visa_type">
													<option value="">- Select Visa Type -</option>
													@foreach(\App\Models\VisaType::orderby('name', 'ASC')->get() as $visalist)
														<option @if($fetchedData->visa_type == $visalist->name) selected @endif value="{{$visalist->name}}">{{$visalist->name}}</option>
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
												{!! Form::text('visa_opt', $fetchedData->visa_opt, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Additional visa information' ))  !!}
											</div>
										</div>
										
										<?php
											$visa_expiry_date = '';
											if($fetchedData->visaExpiry != '' && $fetchedData->visaExpiry != '0000-00-00'){
												$visa_expiry_date = date('d/m/Y', strtotime($fetchedData->visaExpiry));
											}
										?>	
										
										@if($fetchedData->visa_type!="Citizen" && $fetchedData->visa_type!="PR")
											<div class="col-md-4 col-sm-12">
												<div class="form-group"> 
													<label for="visaExpiry">Visa Expiry Date</label>
													<div class="input-group">
														<div class="input-group-prepend">
															<div class="input-group-text">
																<i class="fas fa-calendar-alt"></i>
															</div>
														</div>
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
														<div class="input-group-prepend">
															<div class="input-group-text">
																<i class="fas fa-calendar-alt"></i>
															</div>
														</div>
														{!! Form::text('preferredIntake', @$fetchedData->preferredIntake, array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select intake date' ))  !!}
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
															<option <?php if(@$fetchedData->country_passport == $list->sortname){ echo 'selected'; } ?> value="{{@$list->sortname}}">{{@$list->name}}</option>
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
													{!! Form::text('passport_number', @$fetchedData->passport_number, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter passport number' ))  !!}
													@if ($errors->has('passport_number'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('passport_number') }}</strong>
														</span> 
													@endif
												</div>
											</div>
										@endif
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Section 4: Address Information -->
					<div class="row mt-3">
						<div class="col-12">
							<div class="card section-card">
								<div class="card-header bg-light">
									<h5 class="mb-0">
										<i class="fas fa-map-marker-alt text-primary"></i> Address Information
									</h5>
								</div>
								<div class="card-body">
									<div class="row">
										<div class="col-md-6 col-sm-12">
											<div class="form-group"> 
												<label for="address">Address</label>
												{!! Form::text('address', @$fetchedData->address, array('placeholder'=>"Search address" , 'id'=>"pac-input" , 'class' => 'form-control controls', 'data-valid'=>'', 'autocomplete'=>'off' ))  !!}
												@if ($errors->has('address'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('address') }}</strong>
													</span> 
												@endif
												<small class="form-text text-muted">Start typing to search address</small>
											</div>
										</div>
										
										<div id="map" style="display:none;"></div>
										
										<div class="col-md-3 col-sm-12">
											<div class="form-group"> 
												<label for="city">City</label>
												{!! Form::text('city', @$fetchedData->city, array('id' => 'locality', 'class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter city' ))  !!}
												@if ($errors->has('city'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('city') }}</strong>
													</span> 
												@endif
											</div>
										</div>
										
										<div class="col-md-3 col-sm-12">
											<div class="form-group"> 
												<label for="zip">Post Code</label>
												{!! Form::text('zip', @$fetchedData->zip, array('id' => 'postal_code', 'class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter postcode' ))  !!}
												@if ($errors->has('zip'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('zip') }}</strong>
													</span> 
												@endif
											</div>
										</div>
										
										<div class="col-md-6 col-sm-12">
											<div class="form-group"> 
												<label for="state">State</label>
												<select class="form-control" name="state">
													<option value="">- Select State -</option>	
													<option value="Australian Capital Territory" @if(@$fetchedData->state == "Australian Capital Territory") selected @endif>Australian Capital Territory</option>
													<option value="New South Wales" @if(@$fetchedData->state == "New South Wales") selected @endif>New South Wales</option>
													<option value="Northern Territory" @if(@$fetchedData->state == "Northern Territory") selected @endif>Northern Territory</option>
													<option value="Queensland" @if(@$fetchedData->state == "Queensland") selected @endif>Queensland</option>
													<option value="South Australia" @if(@$fetchedData->state == "South Australia") selected @endif>South Australia</option>
													<option value="Tasmania" @if(@$fetchedData->state == "Tasmania") selected @endif>Tasmania</option>
													<option value="Victoria" @if(@$fetchedData->state == "Victoria") selected @endif>Victoria</option>
													<option value="Western Australia" @if(@$fetchedData->state == "Western Australia") selected @endif>Western Australia</option>
												</select>
												@if ($errors->has('state'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('state') }}</strong>
													</span> 
												@endif
											</div>
										</div>
										
										<div class="col-md-6 col-sm-12">
											<div class="form-group"> 
												<label for="country">Country</label>
												<select class="form-control select2" id="country_select" name="country">
												<?php
													foreach(\App\Models\Country::all() as $list){
														?>
														<option <?php if(@$fetchedData->country == $list->sortname){ echo 'selected'; } ?> value="{{@$list->sortname}}">{{@$list->name}}</option>
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
							</div>
						</div>
					</div>

					<!-- Additional Contact (Collapsible) -->
					<div class="row mt-3">
						<div class="col-12">
							<div class="card section-card additional-contact-section" <?php if(
								( isset($fetchedData->att_email) && $fetchedData->att_email != "")
								||
								( isset($fetchedData->att_phone) && $fetchedData->att_phone != "")
							) { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php }?>>
								<div class="card-header bg-light">
									<h5 class="mb-0">
										<i class="fas fa-plus-circle text-primary"></i> Additional Contact
									</h5>
								</div>
								<div class="card-body">
									<div class="row">
										<div class="col-md-6 col-sm-12">
											<div class="form-group"> 
												<label for="att_email">Additional Email</label>
												{!! Form::text('att_email', @$fetchedData->att_email, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter additional email' ))  !!}
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
														<input class="telephone" id="telephone" type="tel" name="att_country_code" readonly>
													</div>	
													{!! Form::text('att_phone', @$fetchedData->att_phone, array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter additional phone' ))  !!}
													@if ($errors->has('att_phone'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('att_phone') }}</strong>
														</span> 
													@endif
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Section 5: Related Files & Country -->
					<div class="row mt-3">
						<div class="col-12">
							<div class="card section-card">
								<div class="card-header bg-light">
									<h5 class="mb-0">
										<i class="fas fa-link text-primary"></i> Related Files & Country
									</h5>
								</div>
								<div class="card-body">
									<div class="row">
										<div class="col-md-9 col-sm-12">
											<div class="form-group"> 
												<label for="related_files">Similar Related Files</label>
												<select multiple class="form-control js-data-example-ajaxcc" name="related_files[]">
												</select>
												@if ($errors->has('related_files'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('related_files') }}</strong>
													</span> 
												@endif
												<small class="form-text text-muted">Search and select related client files</small>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Professional Details Section (for non-Citizen/PR visa types) -->
					@if($fetchedData->visa_type!="Citizen" && $fetchedData->visa_type!="PR")
					<div class="row mt-3">
						<div class="col-12">
							<div class="card section-card">
								<div class="card-header bg-light">
									<h5 class="mb-0">
										<i class="fas fa-briefcase text-primary"></i> Professional Details
									</h5>
								</div>
								<div class="card-body">
									<div class="row">
										<div class="col-md-4 col-sm-12">
											<div class="form-group"> 
												<label for="nomi_occupation">Nominated Occupation</label>
												{!! Form::text('nomi_occupation', @$fetchedData->nomi_occupation, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter occupation' ))  !!}
												@if ($errors->has('nomi_occupation'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('nomi_occupation') }}</strong>
													</span> 
												@endif
											</div>
										</div>
										
										<div class="col-md-4 col-sm-12">
											<div class="form-group"> 
												<label for="skill_assessment">Skill Assessment</label>
												<select class="form-control" name="skill_assessment">
													<option value="">Select</option>
													<option @if($fetchedData->skill_assessment == 'Yes') selected @endif value="Yes">Yes</option>
													<option @if($fetchedData->skill_assessment == 'No') selected @endif value="No">No</option>
												</select>											
												@if ($errors->has('skill_assessment'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('skill_assessment') }}</strong>
													</span> 
												@endif
											</div>
										</div>
										
										<div class="col-md-4 col-sm-12">
											<div class="form-group"> 
												<label for="high_quali_aus">Highest Qualification (Australia)</label>
												{!! Form::text('high_quali_aus', @$fetchedData->high_quali_aus, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter qualification' ))  !!}
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
											{!! Form::text('high_quali_overseas', @$fetchedData->high_quali_overseas, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
											
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
											{!! Form::text('relevant_work_exp_aus', @$fetchedData->relevant_work_exp_aus, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
											
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
											{!! Form::text('relevant_work_exp_over', @$fetchedData->relevant_work_exp_over, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
												
											@if ($errors->has('relevant_work_exp_over'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('relevant_work_exp_over') }}</strong>
												</span> 
											@endif
										</div>
									</div>									
									<div class="col-sm-12">
										<div class="form-group">
											<label>English Test Scores</label>
											<?php
												$testscores = \App\Models\TestScore::where('client_id', $fetchedData->id)->where('type', 'client')->first();
											?>
											<div class="row">
												<div class="col-sm-2">
													<div class="form-group">
														<label for="test_type">Test Type</label>
														<select class="form-control" name="test_type" id="test_type" onchange="loadTestScoresEditPage()">
															<option value="toefl">TOEFL</option>
															<option value="ilets">IELTS</option>
															<option value="pte">PTE</option>
														</select>
													</div>
												</div>
												<div class="col-sm-2">
													<div class="form-group">
														<label for="listening_edit">L (Listening)</label>
														<input type="number" class="form-control" name="listening" id="listening_edit" step="0.01" placeholder="Listening"/>
													</div>
												</div>
												<div class="col-sm-2">
													<div class="form-group">
														<label for="reading_edit">R (Reading)</label>
														<input type="number" class="form-control" name="reading" id="reading_edit" step="0.01" placeholder="Reading"/>
													</div>
												</div>
												<div class="col-sm-2">
													<div class="form-group">
														<label for="writing_edit">W (Writing)</label>
														<input type="number" class="form-control" name="writing" id="writing_edit" step="0.01" placeholder="Writing"/>
													</div>
												</div>
												<div class="col-sm-2">
													<div class="form-group">
														<label for="speaking_edit">S (Speaking)</label>
														<input type="number" class="form-control" name="speaking" id="speaking_edit" step="0.01" placeholder="Speaking"/>
													</div>
												</div>
												<div class="col-sm-2">
													<div class="form-group">
														<label for="overall_edit">O (Overall)</label>
														<input type="number" class="form-control" name="overall" id="overall_edit" step="0.01" placeholder="Overall"/>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-3">
													<div class="form-group">
														<label for="test_date_edit">Test Date</label>
														<input type="text" class="form-control datepicker" name="test_date" id="test_date_edit" placeholder="Date"/>
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
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<?php
											$explodeenaati_py = array();
											if($fetchedData->naati_py != ''){
												$explodeenaati_py = explode(',', $fetchedData->naati_py);
											} 
											?>
											<label style="display:block; margin-bottom: 8px;" for="naati_py">Naati/PY</label>
											<div class="d-flex align-items-center" style="gap: 15px;">
												<div class="form-check">
													<input <?php if(in_array('Naati', $explodeenaati_py)){ echo 'checked'; } ?> class="form-check-input" type="checkbox" id="Naati" value="Naati" name="naati_py[]">
													<label class="form-check-label" for="Naati">Naati</label>
												</div>
												<div class="form-check">
													<input <?php if(in_array('PY', $explodeenaati_py)){ echo 'checked'; } ?> class="form-check-input" type="checkbox" id="py" value="PY" name="naati_py[]">
													<label class="form-check-label" for="py">PY</label>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="total_points">Total Points</label>
											{!! Form::text('total_points', @$fetchedData->total_points, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
												
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
													<option @if($fetchedData->start_process == 'As soon As Possible') selected @endif value="As soon As Possible">As soon As Possible</option>
													<option @if($fetchedData->start_process == 'In Next 3 Months') selected @endif value="In Next 3 Months">In Next 3 Months</option>
													<option @if($fetchedData->start_process == 'In Next 6 Months') selected @endif value="In Next 6 Months">In Next 6 Months</option>
													<option @if($fetchedData->start_process == 'Advise Only') selected @endif value="Advise Only">Advise Only</option>
											</select>
											@if ($errors->has('start_process'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('start_process') }}</strong>
												</span> 
											@endif
										</div>
									</div>
								</div>
								<hr style="border-color: #000;"/>
								<div class="row " id="internal">
									<div class="col-sm-3">
										<div class="form-group">
											<label for="service">Service <span style="color:#ff0000;">*</span></label>
											<select class="form-control select2" name="service" data-valid="required">
												<option value="">- Select Lead Service -</option>
												@foreach(\App\Models\LeadService::orderby('name', 'ASC')->get() as $leadservlist)
												<option @if($fetchedData->service == $leadservlist->name) selected @endif value="{{$leadservlist->name}}">{{$leadservlist->name}}</option>
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
												<option value="Unassigned" @if(@$fetchedData->status == "Unassigned") selected @endif>Unassigned</option>
												<option value="Assigned" @if(@$fetchedData->status == "Assigned") selected @endif>Assigned</option>
												<option value="In-Progress" @if(@$fetchedData->status == "In-Progress") selected @endif>In-Progress</option>
												<option value="Closed" @if(@$fetchedData->status == "Closed") selected @endif>Closed</option>
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
												<option value="1" @if(@$fetchedData->lead_quality == "1") selected @endif>1</option>
												<option value="2" @if(@$fetchedData->lead_quality == "2") selected @endif>2</option>
												<option value="3" @if(@$fetchedData->lead_quality == "3") selected @endif>3</option>
												<option value="4" @if(@$fetchedData->lead_quality == "4") selected @endif>4</option>
												<option value="5" @if(@$fetchedData->lead_quality == "5") selected @endif>5</option>
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
												<option value="Sub Agent" @if(@$fetchedData->source == 'Sub Agent') selected @endif>Sub Agent</option>
												@foreach(\App\Models\Source::all() as $sources)
													<option value="{{$sources->name}}" @if(@$fetchedData->source == $sources->name) selected @endif>{{$sources->name}}</option>
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
													<option <?php if(@$fetchedData->agent_id == $agentlist->id){ echo 'selected'; } ?> value="{{$agentlist->id}}">{{$agentlist->full_name}}</option>
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
											<?php
											/*$explodee = array();
                                            if($fetchedData->tagname != ''){
                                                $explodee = explode(',', $fetchedData->tagname);
                                            } */
											?>
											<!--<select multiple class="form-control select2" name="tagname[]">
												<option value="">-- Search & Select tag --</option>-->
												<?php
												//foreach(\App\Models\Tag::all() as $tags){
                                                 //foreach(\App\Models\Tag::select('id', 'name')->paginate(50) as $tags){
												?>
										<!--<option <?php //if(in_array($tags->id, $explodee)){ echo 'selected'; } ?>  value="{{--$tags->id--}}">{{--$tags->name--}}</option>-->
												<?php
												//}
												?>	 
											<!--</select>-->
                                          
                                            <select multiple class="form-control select2"  id="tag"  name="tagname[]">

											</select>
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
									@endif
									<div class="col-sm-12">
										<div class="form-group float-end">
                                             <div class="removesids_contact"></div>
											{!! Form::button('Save', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("edit-clients")' ])  !!}
										</div>
									</div>
								</div> 
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
       $tagWord = \App\Models\Tag::where('id', $tag1)->first();
   ?>
<input type="hidden" class="relatedtag" data-name="<?php echo $tagWord->name; ?>" data-id="<?php echo $tagWord->id; ?>">
<?php
   }
} ?>

<div class="modal fade custom_modal" id="serviceTaken" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel">Service Taken</h5>

				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
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
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" id="clientphoneform" autocomplete="off" enctype="multipart/form-data">
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
										<input class="telephone" id="telephone" type="tel" name="client_country_code" >
									</div>
									{!! Form::text('client_phone', '', array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Phone' ))  !!}
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
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" id="clientemailform" autocomplete="off" enctype="multipart/form-data">
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
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
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

<!--
Using async loading strategy as recommended by Google Maps API.
See https://developers.google.com/maps/documentation/javascript/load-maps-js-api
for more information.
-->
<script>
(function() {
  function loadGoogleMaps() {
    // Only load if required elements exist
    if (!document.getElementById("map") || !document.getElementById("pac-input")) {
      console.warn("Google Maps: Required elements (map or pac-input) not found. Maps functionality disabled.");
      return;
    }
    
    const script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key=<?php echo env('GOOGLE_MAPS_API_KEY');?>&callback=initAutocomplete&libraries=places&v=weekly&loading=async';
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
  }
  
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadGoogleMaps);
  } else {
    loadGoogleMaps();
  }
})();
</script>

<script>
/**
 * @license
 * Copyright 2019 Google LLC. All Rights Reserved.
 * SPDX-License-Identifier: Apache-2.0
 */
// @ts-nocheck TODO remove when fixed
// This example uses Google Places Autocomplete with a map.
// Users can enter geographical searches and see results on the map.
// This example requires the Places library. Include the libraries=places
// parameter when you first load the API. For example:
// <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
// Migrated from deprecated SearchBox to Autocomplete API (March 2025)

function initAutocomplete() {
  // Check if required elements exist before initializing
  const mapElement = document.getElementById("map");
  const input = document.getElementById("pac-input");
  
  if (!mapElement || !input) {
    console.warn("Google Maps: Required elements (map or pac-input) not found. Maps functionality disabled.");
    return;
  }
  
  // Check if Google Maps API is loaded
  if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
    console.error("Google Maps API not loaded properly.");
    return;
  }
  
  try {
    const map = new google.maps.Map(mapElement, {
      center: { lat: -33.8688, lng: 151.2195 },
      zoom: 13,
      mapTypeId: "roadmap",
    });
    
    // Show the map once initialized
    mapElement.style.display = 'block';
    
    // Create Autocomplete with Australian bias and required fields
    const autocomplete = new google.maps.places.Autocomplete(input, {
      componentRestrictions: { country: 'au' },
      fields: ['address_components', 'formatted_address', 'geometry', 'name', 'icon'],
      types: ['address']
    });

    // Position the input control on the map
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    
    // Bias Autocomplete results towards current map's viewport
    map.addListener("bounds_changed", () => {
      if (map.getBounds()) {
        autocomplete.setBounds(map.getBounds());
      }
    });

    let marker = null;

    // Listen for the event fired when the user selects a prediction
    autocomplete.addListener("place_changed", () => {
      const place = autocomplete.getPlace();

      if (!place.geometry || !place.geometry.location) {
        console.log("Returned place contains no geometry");
        return;
      }

      // Clear out the old marker
      if (marker) {
        marker.setMap(null);
        marker = null;
      }
      
      // Parse address components directly from Google Places API response
      if (place.address_components) {
          let postalCode = '';
          let locality = '';
          let state = '';
          let streetNumber = '';
          let route = '';
          
          // Extract address components
          place.address_components.forEach((component) => {
              if (component.types.includes('postal_code')) {
                  postalCode = component.long_name;
              }
              if (component.types.includes('locality')) {
                  locality = component.long_name;
              }
              // Also check for postal_town if locality is not found
              if (!locality && component.types.includes('postal_town')) {
                  locality = component.long_name;
              }
              // Extract state/administrative area
              if (component.types.includes('administrative_area_level_1')) {
                  state = component.long_name;
              }
              // Extract street number
              if (component.types.includes('street_number')) {
                  streetNumber = component.long_name;
              }
              // Extract route/street name
              if (component.types.includes('route')) {
                  route = component.long_name;
              }
          });
          
          // State abbreviation to full name mapping for Australian states
          const stateMapping = {
              'NSW': 'New South Wales',
              'VIC': 'Victoria',
              'QLD': 'Queensland',
              'SA': 'South Australia',
              'WA': 'Western Australia',
              'TAS': 'Tasmania',
              'NT': 'Northern Territory',
              'ACT': 'Australian Capital Territory'
          };
          
          // Populate the form fields
          if (postalCode) {
              $('#postal_code').val(postalCode);
          }
          if (locality) {
              $('#locality').val(locality);
          }
          if (state) {
              // Check if state is an abbreviation and convert to full name
              const fullStateName = stateMapping[state] || state;
              $('select[name="state"]').val(fullStateName);
          }
      }

      const icon = {
        url: place.icon,
        size: new google.maps.Size(71, 71),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(17, 34),
        scaledSize: new google.maps.Size(25, 25),
      };

      // Create a marker for the selected place
      marker = new google.maps.Marker({
        map,
        icon,
        title: place.name,
        position: place.geometry.location,
      });

      // Adjust map viewport to show the selected place
      const bounds = new google.maps.LatLngBounds();
      if (place.geometry.viewport) {
        bounds.union(place.geometry.viewport);
      } else {
        bounds.extend(place.geometry.location);
      }
      map.fitBounds(bounds);
    });
  } catch (error) {
    console.error("Error initializing Google Maps:", error);
  }
}

window.initAutocomplete = initAutocomplete;
</script>

@if($showAlert)
    <script>
        alert("Have u updated the following details - email address,current address,current visa,visa expiry,other fields? Pls update these details before forwarding this to anyone?");
    </script>
@endif

<script>
jQuery(document).ready(function($){
  
    ////////////////////////////////////////
	////////////////////////////////////////
	//// start add more client phone //////
	////////////////////////////////////////
	////////////////////////////////////////

	var itag_phone = $('.clientphonedata .row').length;

    //Add client phone
    $(document).delegate('.openclientphonenew','click', function(){
        $('#clientPhoneModalLabel').html('Add New Client Phone');
        $('.saveclientphone').show();
        $('#update_clientphone').hide();
        $('#clientphoneform')[0].reset();
        $('.addclientphone').modal('show');
        $(".telephone").intlTelInput();
    });

    $('.addclientphone').on('shown.bs.modal', function () {
        $(".telephone").intlTelInput();
    });
  
  
    //Save client phone
    $(document).delegate('.saveclientphone','click', function() {
        var client_phone = $('input[name="client_phone"]').val();
        $('.client_phone_error').html('');
        $('input[name="client_phone"]').parent().removeClass('error');
        if ($('table#metatag_table').find('#metatag2_'+itag_phone).length > 0) {
        }
        else {
            var flag = false;
            if(client_phone == ''){
                $('.client_phone_error').html('The Phone field is required.');
                $('input[name="client_phone"]').parent().addClass('error');
                flag = true;
            }


            if(!flag){
                var str = $( "#clientphoneform" ).serializeArray();
                console.log(str);
                clientphonedata[itag_phone] = {"contact_type":str[0].value, "country_code":str[1].value ,"phone":str[2].value}
                console.log(clientphonedata);

                // New compact design HTML
                var html = '<div class="compact-contact-item" id="metatag2_'+itag_phone+'">';
                html += '<span class="contact-type-tag">'+str[0].value+'</span>';
                html += '<span class="contact-phone">'+str[1].value+' '+str[2].value+'</span>';
                html += '<div class="contact-actions">';
                
                if(str[0].value != 'Personal') {
                    html += '<a href="javascript:;" dataid="'+itag_phone+'" class="deletecontact btn-delete"><i class="fa fa-trash"></i></a>';
                }
                
                html += '</div>';
                
                // Hidden fields
                html += '<input type="hidden" name="contact_type[]" value="'+str[0].value+'">';
                html += '<input type="hidden" name="client_country_code[]" value="'+str[1].value+'">';
                html += '<input type="hidden" name="client_phone[]" value="'+str[2].value+'">';
                html += '<input type="hidden" name="clientphoneid[]" value="">';
                html += '</div>';

                $('.clientphonedata').append(html);
                $('#clientphoneform')[0].reset();
                $('.addclientphone').modal('hide');
                itag_phone++;
            }
        }
    });

     $(document).delegate('.deletecontact','click', function(){
		var v = $(this).attr('dataid');
		var contactid = $(this).attr('contactid');
         // Show confirmation message
        if (confirm('Are you sure you want to delete this contact?')) {
        // If user clicks Yes
            $('#metatag2_'+v).remove();
            if (typeof contactid !== 'undefined' && contactid !== false) {
                $('.removesids_contact').append('<input type="hidden" name="rem_phone[]" value="'+contactid+'">');
            }
        }
	});

	// Email Management
	var itag_email = 0;
	
	$(document).delegate('.openclientemailnew','click', function(){
		$('#clientEmailModalLabel').html('Add New Email');
		$('.saveclientemail').show();
		$('#clientemailform')[0].reset();
		$('.addclientemail').modal('show');
	});

	// Save client email
	$(document).delegate('.saveclientemail','click', function(){
		var client_email = $('input[name="client_email"]').val();
		var email_type = $('select[name="email_type_modal"]').val();
		
		$('.client_email_error').html('');
		$('input[name="client_email"]').parent().removeClass('error');
		
		var flag = false;
		if(client_email == ''){
			$('.client_email_error').html('The Email field is required.');
			$('input[name="client_email"]').parent().addClass('error');
			flag = true;
		}
		if(email_type == ''){
			alert('Please select email type.');
			flag = true;
		}

		if(!flag){
			// Check if this is main email or additional
			var isMainEmail = (email_type == 'Personal' || email_type == 'Business');
			var emailId = isMainEmail ? 'email_main' : 'email_additional_' + itag_email;
			var hiddenName = isMainEmail ? 'email' : 'att_email';
			var hiddenTypeName = isMainEmail ? 'email_type' : '';
			
			// Remove existing main email if adding new main email
			if(isMainEmail) {
				$('#email_main').remove();
			}
			
			var html = '<div class="compact-contact-item" id="'+emailId+'">';
			html += '<span class="contact-type-tag">'+email_type+'</span>';
			html += '<span class="contact-email">'+client_email+'</span>';
			html += '<div class="contact-actions">';
			
			if(isMainEmail) {
				html += '<button type="button" class="btn-verify manual_email_phone_verified" data-fname="{{ $fetchedData->first_name }}" data-email="'+client_email+'" data-clientid="{{ $fetchedData->id }}">';
				html += '<i class="fas fa-check"></i>';
				html += '</button>';
			} else {
				html += '<a href="javascript:;" class="deleteemail btn-delete" data-email="'+emailId+'">';
				html += '<i class="fa fa-trash"></i>';
				html += '</a>';
			}
			
			html += '</div>';
			
			// Hidden fields
			html += '<input type="hidden" name="'+hiddenName+'" value="'+client_email+'">';
			if(hiddenTypeName) {
				html += '<input type="hidden" name="'+hiddenTypeName+'" value="'+email_type+'">';
			}
			html += '</div>';

			$('.clientemaildata').append(html);
			$('#clientemailform')[0].reset();
			$('.addclientemail').modal('hide');
			itag_email++;
		}
	});

	// Delete email
	$(document).delegate('.deleteemail','click', function(){
		var emailId = $(this).attr('data-email');
		if (confirm('Are you sure you want to delete this email?')) {
			$('#'+emailId).remove();
		}
	});

    ////////////////////////////////////////
    ////////////////////////////////////////
    //// end add more client phone //////
    ////////////////////////////////////////
    ////////////////////////////////////////


  
    /////////////////////////////////////////////////
    ////// tag  code start ///
    ////////////////////////////////////////////////
  
     <?php if($fetchedData->tagname != '')
    { ?>
    	var array1 = [];
	    var data1 = [];
        $('.relatedtag').each(function(){
            var id1 = $(this).attr('data-id');
			array1.push(id1);
			var name1 = $(this).attr('data-name');
            data1.push({
				id: id1,
                text: name1,
            });
	    });

        $("#tag").select2({
            data: data1,
            escapeMarkup: function(markup) {
                return markup;
            },
            templateResult: function(data1) {
                return data1.html;
            },
            templateSelection: function(data1) {
                return data1.text;
            }
        });

	    $('#tag').val(array1);
		$('#tag').trigger('change');
    <?php
    } ?>

    $('#tag').select2({
        ajax: {
            url: '{{URL::to('/admin/gettagdata')}}',
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.items.map(item => ({
                        id: item.id,
                        text: item.text
                    })),
                    pagination: {
                        more: (params.page * data.per_page) < data.total_count
                    }
                };
            },
            cache: true
        },
        placeholder: 'Search & Select tag',
        minimumInputLength: 1,
        templateResult: formatItem, // Custom function to format the result
        templateSelection: formatItemSelection // Custom function to format the selection
    });

    function formatItem(item) {
        if (item.loading) {
            return item.text;
        }
        //return `<div class='select2-result-item'>${item.text}</div>`;
        return item.text;
    }

    function formatItemSelection(item) {
        return item.text || item.id;
    }
  
    /////////////////////////////////////////////////
    ////// tag  code end ///
    ////////////////////////////////////////////////
  
    var source_val = '<?php echo $fetchedData->source;?>';
    if( source_val != '' ) {
        if( source_val == 'Sub Agent') {
            $('.is_subagent').css('display','inline-block');
        } else {
            $('.is_subagent').css('display','none');
        }
    } else {
        $('.is_subagent').css('display','none');
    }
  
  	$('.filter_btn').on('click', function(){
		$('.filter_panel').slideToggle();
	});

	/////////////////////////////////////////////////
    ////// service taken related code start ///
    ////////////////////////////////////////////////

    //add button popup
    $(document).delegate('.serviceTaken','click', function(){
        $('#entity_type').val("add");

        $('#mig_ref_no').val("");
        $('#mig_service').val("");
        $('#mig_notes').val("");

		$('#edu_course').val("");
		$('#edu_college').val("");
		$('#edu_service_start_date').val("");
		$('#edu_notes').val("");

		$('#serviceTaken').modal('show');
        $('#createservicetaken_btn').text("Save");
	});

    //edit button click and form submit
	$(document).delegate('.service_taken_edit','click', function(){
        $('#createservicetaken_btn').text("Update");
		var sel_service_taken_id = $(this).attr('id');
        $('#entity_id').val(sel_service_taken_id);
        $.ajax({
			url: '{{URL::to('/admin/client/getservicetaken')}}',
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			type:'POST',
			datatype:'json',
			data:{sel_service_taken_id:sel_service_taken_id},
			success: function(response){
				var obj = $.parseJSON(response);
				if(obj.status){
					console.log(obj.user_rec.service_type);
                    $('#entity_type').val("edit");
                    if(obj.user_rec.service_type == 'Migration') {
                        $('#Migration_inv').prop('checked', true);
                        $('#Eductaion_inv').prop('checked', false);
                        $('#Migration_inv').trigger('change');

                        $('#mig_ref_no').val(obj.user_rec.mig_ref_no);
                        $('#mig_service').val(obj.user_rec.mig_service);
                        $('#mig_notes').val(obj.user_rec.mig_notes);

                        $('#edu_course').val("");
                        $('#edu_college').val("");
                        $('#edu_service_start_date').val("");
                        $('#edu_notes').val("");

                    } else if(obj.user_rec.service_type == 'Education') {
                        $('#Eductaion_inv').prop('checked', true);
                        $('#Migration_inv').prop('checked', false);
                        $('#Eductaion_inv').trigger('change');

                        $('#edu_course').val(obj.user_rec.edu_course);
                        $('#edu_college').val(obj.user_rec.edu_college);
                        $('#edu_service_start_date').val(obj.user_rec.edu_service_start_date);
                        $('#edu_notes').val(obj.user_rec.edu_notes);

                        $('#mig_ref_no').val("");
                        $('#mig_service').val("");
                        $('#mig_notes').val("");
                    }
				} else {
					alert(obj.message);
				}
			}
		});
        $('#serviceTaken').modal('show');
	});

    if (typeof flatpickr !== 'undefined') {
        flatpickr('#edu_service_start_date', {
            dateFormat: 'd/m/Y',
            allowInput: true
        });
    }

    //Service type on change div
    $('.modal-body form#createservicetaken input[name="service_type"]').on('change', function(){
        var invid = $(this).attr('id');
        if(invid == 'Migration_inv'){
            $('.modal-body form#createservicetaken .is_Migration_inv').show();
            $('.modal-body form#createservicetaken .is_Migration_inv input').attr('data-valid', 'required');
            $('.modal-body form#createservicetaken .is_Eductaion_inv').hide();
            $('.modal-body form#createservicetaken .is_Eductaion_inv input').attr('data-valid', '');
        }
        else {
            $('.modal-body form#createservicetaken .is_Eductaion_inv').show();
            $('.modal-body form#createservicetaken .is_Eductaion_inv input').attr('data-valid', 'required');
            $('.modal-body form#createservicetaken .is_Migration_inv').hide();
            $('.modal-body form#createservicetaken .is_Migration_inv input').attr('data-valid', '');
        }
    });

    //add and edit button service taken form submit
    $('#createservicetaken').submit(function(event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: "{{URL::to('/admin/client/createservicetaken')}}",
            data: formData,
            dataType: 'json',
            success: function(response) {
                var res = response.user_rec;
                console.log(res);
                $('#serviceTaken').modal('hide');
                $(".popuploader").hide();

                // Clear and rebuild services grid
                $('.services-taken-grid').html('');
                
                $.each(res, function(index, value) {
                    var serviceClass = value.service_type.toLowerCase();
                    var badgeClass = serviceClass == 'migration' ? 'primary' : 'info';
                    
                    var cardHtml = '<div class="service-card service-card-' + serviceClass + '" id="service-card-' + value.id + '">';
                    cardHtml += '<div class="service-card-header">';
                    cardHtml += '<span class="service-type-badge badge badge-' + badgeClass + '">' + value.service_type + '</span>';
                    cardHtml += '<div class="service-actions">';
                    cardHtml += '<a href="javascript:;" class="service_taken_edit text-primary" id="' + value.id + '" title="Edit"><i class="fa fa-edit"></i></a>';
                    cardHtml += '<a href="javascript:;" class="service_taken_trash text-danger ms-2" id="' + value.id + '" title="Delete"><i class="fa fa-trash"></i></a>';
                    cardHtml += '</div></div>';
                    cardHtml += '<div class="service-card-body">';
                    
                    if(value.service_type == 'Migration') {
                        cardHtml += '<div class="service-detail"><span class="detail-label">Reference No:</span><span class="detail-value">' + value.mig_ref_no + '</span></div>';
                        cardHtml += '<div class="service-detail"><span class="detail-label">Service:</span><span class="detail-value">' + value.mig_service + '</span></div>';
                        cardHtml += '<div class="service-detail"><span class="detail-label">Notes:</span><span class="detail-value">' + value.mig_notes + '</span></div>';
                    } else if(value.service_type == 'Education') {
                        cardHtml += '<div class="service-detail"><span class="detail-label">Course:</span><span class="detail-value">' + value.edu_course + '</span></div>';
                        cardHtml += '<div class="service-detail"><span class="detail-label">College:</span><span class="detail-value">' + value.edu_college + '</span></div>';
                        cardHtml += '<div class="service-detail"><span class="detail-label">Start Date:</span><span class="detail-value">' + value.edu_service_start_date + '</span></div>';
                        cardHtml += '<div class="service-detail"><span class="detail-label">Notes:</span><span class="detail-value">' + value.edu_notes + '</span></div>';
                    }
                    
                    cardHtml += '</div></div>';
                    $('.services-taken-grid').append(cardHtml);
                });
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText); // Handle error response
            }
        });
    });

    //delete
	$(document).delegate('.service_taken_trash', 'click', function(e){
        var conf = confirm('Are you sure you want to delete this service?');
	    if(conf){
            var sel_service_taken_id = $(this).attr('id');
            $.ajax({
                url: '{{URL::to('/admin/client/removeservicetaken')}}',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type:'POST',
                datatype:'json',
                data:{sel_service_taken_id:sel_service_taken_id},
                success: function(response){
                    var obj = $.parseJSON(response);
                    if(obj.status){
                        // Remove the service card with animation
                        $('#service-card-' + obj.record_id).fadeOut(300, function(){
                            $(this).remove();
                            
                            // Check if no services left, show empty message
                            if($('.services-taken-grid .service-card').length === 0) {
                                var emptyHtml = '<div class="no-services-message">';
                                emptyHtml += '<i class="fas fa-inbox fa-3x text-muted mb-3"></i>';
                                emptyHtml += '<p class="text-muted">No services have been added yet.</p>';
                                emptyHtml += '<p class="text-muted"><small>Click "Add Service" to create a new service record.</small></p>';
                                emptyHtml += '</div>';
                                $('.services-taken-grid').html(emptyHtml);
                            }
                        });
                        
                        // Show success message
                        if (typeof iziToast !== 'undefined') {
                            iziToast.success({
                                title: 'Success',
                                message: obj.message,
                                position: 'topRight'
                            });
                        } else {
                            alert(obj.message);
                        }
                    } else {
                        alert(obj.message);
                    }
                }
            });
        } else {
            return false;
        }
    });
    /////////////////////////////////////////////////
    ////// service taken related code end ///
    ////////////////////////////////////////////////

    $("#country_select").select2({ width: '200px' });
    
    $(document).delegate('.add_other_email_phone', 'click', function(){
		const section = $('.additional-contact-section');
		if (section.css('display') == 'none') {
			section.slideDown(300);
			$(this).html('<i class="fa fa-minus" aria-hidden="true"></i> Hide');
		} else {
			section.slideUp(300);
			$(this).html('<i class="fa fa-plus" aria-hidden="true"></i> Add More');
		}
	});
    
   $('.manual_email_phone_verified').on('click', function(){
        var client_email = $(this).attr('data-email');
        var client_id = $(this).attr('data-clientid');
        var client_fname = $(this).attr('data-fname');
        if(client_email != '' && client_id != ""){
            $.ajax({
                url: '{{URL::to('email-verify')}}',
                type:'POST',
                data:{client_email:client_email,client_id:client_id,client_fname:client_fname},
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success:function(response){
                    var obj = $.parseJSON(response);
                    alert(obj.message);
                    /*if(obj.status){
                        alert(obj.message);
                    }*/
                }
            });
        }
    });
  
  
     //Verify Phone
    $(document).delegate('.phone_verified', 'click', function(){
        $('#verifyphonemodal').modal('show');
        var client_id = $(this).attr('data-clientid');
        $('#verifyphone_client_id').val(client_id);

        var phone = $(this).attr('data-phone');
        $('#verify_phone_number').val(phone);
    });
  
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#sendCodeBtn').click(function() {
        const phoneNumber = $('#verify_phone_number').val();
        if (!phoneNumber) return;

        $.post('{{ route("verify.send-code") }}', {
            phone_number: phoneNumber
        })
        .done(function(response) {
            alert(response.message);
            $('#verificationCodeSection').show();
        })
        .fail(function(xhr) {
            alert('Failed to send verification code');
        });
    });

    $('#verifyCodeBtn').click(function() {
        const phoneNumber = $('#verify_phone_number').val();
        const code = $('#verification_code').val();
        if (!phoneNumber || !code) return;

        $.post('{{ route("verify.check-code") }}', {
            phone_number: phoneNumber,
            verification_code: code
        })
        .done(function(response) {
            alert(response.message);
            $('#verifyphonemodal').modal('hide');
            location.reload(); // Reload to show updated verified numbers list
        })
        .fail(function(xhr) {
            alert(xhr.responseJSON?.message || 'Verification failed');
        });
    });

    
     $('#checkclientid').on('blur', function(){
        var v = $(this).val();
        if(v != ''){
            $.ajax({
                url: '{{URL::to('admin/checkclientexist')}}',
                type:'GET',
                data:{vl:v,type:'clientid'},
                success:function(res){
                    if(res == 1){
                        alert('Client Id is already exist in our record.');
                    }
                }
            });
        }
    });
    <?php if($fetchedData->related_files != ''){
      
        ?>
    	var array = [];
	var data = [];
    $('.relatedfile').each(function(){
		
			var id = $(this).attr('data-id');
			 array.push(id);
			var email = $(this).attr('data-email');
			var name = $(this).attr('data-name');
			var status = 'Client';
			
			data.push({
				id: id,
				name: name,
				email: email,
				status: status,
  text: name,
  html:  "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

      "<div  class='ag-flex ag-align-start'>" +
        "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'>"+name+"</span>&nbsp;</div>" +
        "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'>"+email+"</small ></div>" +
      
      "</div>" +
      "</div>" +
	   "<div class='ag-flex ag-flex-column ag-align-end'>" +
        
        "<span class='ui label yellow select2-result-repository__statistics'>"+ status +
          
        "</span>" +
      "</div>" +
    "</div>",
  title: name
				});
	});
	<?php } ?>
	
$('.js-data-example-ajaxcc').select2({
		 multiple: true,
		 closeOnSelect: false,
		 minimumInputLength: 1,
		 <?php if($fetchedData->related_files != ''){ ?>
		 data: data,
		 <?php } ?>
		  ajax: {
			url: '{{URL::to('/admin/clients/get-recipients')}}',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					q: params.term, // search term
					page: params.page || 1
				};
			},
			processResults: function (data) {
			  // Transforms the top-level key of the response object from 'items' to 'results'
			  return {
				results: data.items
			  };
			  
			},
			 cache: true
			
		  },
	templateResult: formatRepo,
	templateSelection: formatRepoSelection
});
<?php if($fetchedData->related_files != ''){ ?>
	$('.js-data-example-ajaxcc').val(array);
	$('.js-data-example-ajaxcc').trigger('change');
<?php } ?>
function formatRepo (repo) {
  if (repo.loading) {
    return repo.text;
  }

  var $container = $(
    "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

      "<div  class='ag-flex ag-align-start'>" +
        "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
        "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small ></div>" +
      
      "</div>" +
      "</div>" +
	   "<div class='ag-flex ag-flex-column ag-align-end'>" +
        
        "<span class='ui label yellow select2-result-repository__statistics'>" +
          
        "</span>" +
      "</div>" +
    "</div>"
  );

  $container.find(".select2-result-repository__title").text(repo.name);
  $container.find(".select2-result-repository__description").text(repo.email);
  $container.find(".select2-result-repository__statistics").append(repo.status);
 
  return $container;
}

function formatRepoSelection (repo) {
  return repo.name || repo.text;
}
});

  var loadFile = function(event) {
    var output = document.getElementById('output');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
      URL.revokeObjectURL(output.src); // free memory
	  $('.if_image').hide();
	  $('#output').css({'width':"100px",'height':"100px"});
    }
  };
</script>
@endsection