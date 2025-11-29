@extends('layouts.admin')
@section('title', 'Edit Client')

@section('content')
<link rel="stylesheet" href="{{URL::asset('public/css/bootstrap-datepicker.min.css')}}">
<!-- Main Content -->
<div class="main-content">
	<section class="section">
	     <div class="server-error">
				@include('../Elements/flash-message')
			</div>
		<div class="section-body">
			{{ Form::open(array('url' => 'admin/clients/edit', 'name'=>"edit-clients", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
			{{ Form::hidden('id', @$fetchedData->id) }}  
				{{ Form::hidden('type', @$fetchedData->type) }} 
				<div class="row">   
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Edit Client</h4>
								<div class="card-header-action">
								    <a href="{{route('admin.clients.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-body">
								<div class="row">
									<!--<div class="col-3 col-md-3 col-lg-3">
								    	<div class="form-group profile_img_field">	
											<input type="hidden" id="old_profile_img" name="old_profile_img" value="{{@$fetchedData->profile_img}}" />
											<div class="profile_upload">
												<div class="upload_content">
													@if(@$fetchedData->profile_img != '')
														<img src="{{URL::to('/public/img/profile_imgs')}}/{{@$fetchedData->profile_img}}" style="width:100px;height:100px;" id="output"/> 
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
									<div class="col-12 col-md-12 col-lg-12">
										<div class="row">
											<div class="col-4 col-md-4 col-lg-4">
												<div class="form-group"> 
													<label for="first_name">First Name <span class="span_req">*</span></label>
													{{ Form::text('first_name', @$fetchedData->first_name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )) }}
													@if ($errors->has('first_name'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('first_name') }}</strong>
														</span> 
													@endif
												</div>
											</div>
											<input type="hidden" name="route" value="{{url()->previous()}}">
											<div class="col-4 col-md-4 col-lg-4">
												<div class="form-group"> 
													<label for="last_name">Last Name <span class="span_req">*</span></label>
													{{ Form::text('last_name', @$fetchedData->last_name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )) }}
													@if ($errors->has('last_name'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('last_name') }}</strong>
														</span> 
													@endif
												</div>
											</div>
											<div class="col-4 col-md-4 col-lg-4">
												<div class="form-group"> 
													<label style="display:block;" for="gender">Gender <span class="span_req">*</span></label>
													<div class="form-check form-check-inline">
														<input class="form-check-input" type="radio" id="male" value="Male" name="gender" @if(@$fetchedData->gender == "Male") checked @endif>
														<label class="form-check-label" for="male">Male</label>
													</div>
													<div class="form-check form-check-inline">
														<input class="form-check-input" type="radio" id="female" value="Female" name="gender" @if(@$fetchedData->gender == "Female") checked @endif>
														<label class="form-check-label" for="female">Female</label>
													</div>
													<div class="form-check form-check-inline">
														<input class="form-check-input" type="radio" id="other" value="Other" name="gender" @if(@$fetchedData->gender == "Other") checked @endif>
														<label class="form-check-label" for="other">Other</label>
													</div>
													@if ($errors->has('gender'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('gender') }}</strong>
														</span> 
													@endif
												</div>
											</div>
											
											<div class="col-3 col-md-3 col-lg-3">
												<div class="form-group" style="width: 90%;">
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
														{{ Form::text('dob', @$dob, array('class' => 'form-control dobdatepickers', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }} 
														@if ($errors->has('dob'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('dob') }}</strong>
															</span> 
														@endif
													</div>
												</div>
											</div>
											<div class="col-3 col-md-3 col-lg-3">
												<div class="form-group" style="width: 90%;"> 
													<label for="age">Age</label>
													<div class="input-group">
														<div class="input-group-prepend">
															<div class="input-group-text">
																<i class="fas fa-calendar-alt"></i>
															</div>
														</div>
														{{ Form::text('age', @$fetchedData->age, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
														@if ($errors->has('age'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('age') }}</strong>
															</span> 
														@endif
													</div>
												</div>
											</div>
											<div class="col-3 col-md-3 col-lg-3">
												<div class="form-group"> 
													<label for="client_id">Client ID</label>
													{{ Form::text('client_id', @$fetchedData->client_id, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off', 'id' => 'checkclientid', 'placeholder'=>'' ,'readonly' => 'readonly' )) }}
													@if ($errors->has('client_id'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('client_id') }}</strong>
														</span> 
													@endif
												</div>
											</div>
											
											<div class="col-3 col-md-3 col-lg-3">
        										<div class="form-group">
        											<label for="martial_status">Marital Status</label>
        											<select style="padding: 0px 5px;width: 165px;" name="martial_status" id="martial_status" class="form-control">
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
									
									<div class="col-sm-2">
										<div class="form-group">
											<label for="contact_type">Contact Type <span style="color:#ff0000;">*</span></label>
											<select style="padding: 0px 5px;" name="contact_type" id="contact_type" class="form-control" data-valid="required">
												<option value="Personal" @if(@$fetchedData->contact_type == "Personal") selected @endif> Personal</option>
												<option value="Office" @if(@$fetchedData->contact_type == "Office") selected @endif>Office</option>
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
												{{ Form::text('phone', @$fetchedData->phone, array('class' => 'form-control tel_input', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )) }}
												@if ($errors->has('phone'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('phone') }}</strong>
													</span> 
												@endif
											</div>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group">
											<label for="email_type">Email Type <span style="color:#ff0000;">*</span></label>
											<select style="padding: 0px 5px;" name="email_type" id="email_type" class="form-control" data-valid="required">	
												<option value="Personal" @if(@$fetchedData->email_type == "Personal") selected @endif> Personal</option>
												<option value="Business" @if(@$fetchedData->email_type == "Business") selected @endif>Business</option>
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
											{{ Form::text('email', @$fetchedData->email, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )) }}
											@if ($errors->has('email'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('email') }}</strong>
												</span> 
											@endif
										</div>
									</div>	
									
									 <div class="col-sm-1">
                                        <div class="form-group">
											<label for="manual_email_phone_verified">Email verify</label>
                                            <input type="checkbox" class="manual_email_phone_verified" name="manual_email_phone_verified" value="<?php echo $fetchedData->manual_email_phone_verified;?>" <?php if( isset($fetchedData->manual_email_phone_verified) && $fetchedData->manual_email_phone_verified == '1' ) { echo 'checked';}?>>
                                        </div>
                                    </div>


                                    <div class="col-sm-1">
                                        <div class="form-group" style="margin-top: 40px;">
											<label for="Add another email and contact no"> </label>
                                            <?php
                                            //echo $fetchedData->att_email."===".$fetchedData->att_phone;die;
                                            if(
                                                ( isset($fetchedData->att_email) && $fetchedData->att_email != "")
                                                ||
                                                ( isset($fetchedData->att_phone) && $fetchedData->att_phone != "")
                                            ){ ?>
                                                <a href="javascript:void(0)" class="add_other_email_phone" data-toggle="tooltip" data-placement="bottom" title="Show/Hide another email and contact no"><i class="fa fa-minus" aria-hidden="true"></i></a>
                                            <?php } else { ?>
                                                <a href="javascript:void(0)" class="add_other_email_phone" data-toggle="tooltip" data-placement="bottom" title="Show/Hide another email and contact no"><i class="fa fa-plus" aria-hidden="true"></i></a>

                                            <?php } ?>
										    </div>
                                    </div>
                                    
									<div class="col-sm-3 other_email_div" <?php if(
											( isset($fetchedData->att_email) && $fetchedData->att_email != "")
                                            ||
                                            ( isset($fetchedData->att_phone) && $fetchedData->att_phone != "") ) { ?> style="display:inline-block;" <?php } else { ?> style="display:none;"  <?php }?>>
										<div class="form-group"> 
											<label for="att_email">Email </label>
											{{ Form::text('att_email', @$fetchedData->att_email, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
											@if ($errors->has('att_email'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('att_email') }}</strong>
												</span> 
											@endif
										</div>
									</div> 
									<div class="col-sm-3 other_phone_div" <?php if(
											( isset($fetchedData->att_email) && $fetchedData->att_email != "")
                                            ||
                                            ( isset($fetchedData->att_phone) && $fetchedData->att_phone != "") ) { ?> style="display:inline-block;" <?php } else { ?> style="display:none;"  <?php }?>>
										<div class="form-group"> 
											<label for="att_phone">Phone</label>
											<div class="cus_field_input">
												<div class="country_code"> 
													<input class="telephone" id="telephone" type="tel" name="att_country_code" readonly >
												</div>	
												{{ Form::text('att_phone', @$fetchedData->att_phone, array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
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
											@foreach(\App\VisaType::orderby('name', 'ASC')->get() as $visalist)
												<option @if($fetchedData->visa_type == $visalist->name) selected @endif value="{{$visalist->name}}">{{$visalist->name}}</option>
											@endforeach
											</select>
											@if ($errors->has('visa_type'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('visa_type') }}</strong>
												</span> 
											@endif
												<div style="margin-top:10px;">	
    								{{ Form::text('visa_opt', $fetchedData->visa_opt, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Visa' )) }}
    								</div>
										</div>
									</div>
									<?php
										$visa_expiry_date = '';
										if($fetchedData->visaExpiry != '' && $fetchedData->visaExpiry != '0000-00-00'){
											$visa_expiry_date = date('d/m/Y', strtotime($fetchedData->visaExpiry));
										}
									?>	
								@if($fetchedData->visa_type!="Citizen" && $fetchedData->visa_type!="PR")
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="visaExpiry">Visa Expiry Date</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fas fa-calendar-alt"></i>
													</div>
												</div>
												{{ Form::text('visaExpiry', $visa_expiry_date, array('class' => 'form-control dobdatepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
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
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fas fa-calendar-alt"></i>
													</div>
												</div>
												{{ Form::text('preferredIntake', @$fetchedData->preferredIntake, array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
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
												foreach(\App\Country::all() as $list){
													?>
													<option <?php if(@$fetchedData->country_passport == $list->sortname){ echo 'selected'; } ?> value="{{@$list->sortname}}" >{{@$list->name}}</option>
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
											{{ Form::text('passport_number', @$fetchedData->passport_number, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
											@if ($errors->has('passport_number'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('passport_number') }}</strong>
												</span> 
											@endif
										</div>
									</div>
								@endif
								</div>
								<div class="row">
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="address">Address</label>
											{{ Form::text('address', @$fetchedData->address, array('placeholder'=>"Search Box" , 'id'=>"pac-input" , 'class' => 'form-control controls', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
											@if ($errors->has('address'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('address') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									
									<div id="map"></div>
									
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="city">City</label>
											{{ Form::text('city', @$fetchedData->city, array('id' => 'locality', 'class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
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
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="zip">Post Code</label>
											{{ Form::text('zip', @$fetchedData->zip, array('id' => 'postal_code', 'class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
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
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="country">Country</label>
											<select class="form-control select2" id="country_select" name="country" >
											<?php
												foreach(\App\Country::all() as $list){
													?>
													<option <?php if(@$fetchedData->country == $list->sortname){ echo 'selected'; } ?> value="{{@$list->sortname}}" >{{@$list->name}}</option>
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
									@if($fetchedData->visa_type!="Citizen" && $fetchedData->visa_type!="PR")
									<div class="col-sm-9">
										<div class="form-group"> 
											<label for="related_files">Similar related files</label>
											<select multiple class="form-control js-data-example-ajaxcc" name="related_files[]">
												
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
											{{ Form::text('nomi_occupation', @$fetchedData->nomi_occupation, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
											
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
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="high_quali_aus">Highest Qualification in Australia</label>
											{{ Form::text('high_quali_aus', @$fetchedData->high_quali_aus, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
											
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
											{{ Form::text('high_quali_overseas', @$fetchedData->high_quali_overseas, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
											
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
											{{ Form::text('relevant_work_exp_aus', @$fetchedData->relevant_work_exp_aus, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
											
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
											{{ Form::text('relevant_work_exp_over', @$fetchedData->relevant_work_exp_over, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
												
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
											{{ Form::text('married_partner', @$fetchedData->married_partner, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
												
											@if ($errors->has('married_partner'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('married_partner') }}</strong>
												</span> 
											@endif
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
											<div class="form-group"> 
													<label style="display:block;" for="naati_py">Naati/PY </label>
													<div class="form-check form-check-inline">
														<input  <?php if(in_array('Naati', $explodeenaati_py)){ echo 'checked'; } ?> class="form-check-input" type="checkbox" id="Naati" value="Naati" name="naati_py[]">
														<label class="form-check-label" for="Naati">Naati</label>
													</div>
													<div class="form-check form-check-inline">
														<input <?php if(in_array('PY', $explodeenaati_py)){ echo 'checked'; } ?> class="form-check-input"  type="checkbox" id="py" value="PY" name="naati_py[]">
														<label class="form-check-label" for="py">PY</label>
													</div>
													<div class="form-check form-check-inline">
													
													</div>
												
												</div>
											
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group"> 
											<label for="total_points">Total Points</label>
											{{ Form::text('total_points', @$fetchedData->total_points, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
												
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
												@foreach(\App\LeadService::orderby('name', 'ASC')->get() as $leadservlist)
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

                                                $admins = \App\Admin::where('role','!=',7)->orderby('first_name','ASC')->get();
                                                foreach($admins as $admin)
                                                {
                                                    $branchname = \App\Branch::where('id',$admin->office_id)->first();
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
                                                $admins = \App\Admin::where('role','!=',7)->orderby('first_name','ASC')->get();
                                                foreach($admins as $admin){
                                                    $branchname = \App\Branch::where('id',$admin->office_id)->first();
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
												@foreach(\App\Source::all() as $sources)
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
												@foreach(\App\Agent::all() as $agentlist)
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
												//foreach(\App\Tag::all() as $tags){
                                                 //foreach(\App\Tag::select('id', 'name')->paginate(50) as $tags){
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
                                  
                                     <div class="col-sm-6">
										<div class="form-group">
											<label for="tags_label">Services Taken </label>
                                            <span class="text-muted">
                                                <a href="javascript:;" data-id="{{$fetchedData->id}}" class="btn btn-primary serviceTaken btn-sm"><i class="fa fa-plus"></i> Add</a>
                                            </span>
                                        </div>
                                       
                                       <div id="service_taken_complete" style="display:none;">
                                        </div>

                                        <div class="client_info">
                                            <ul style="margin-left: -40px;">
                                                <?php
                                                $serviceTakenArr = \App\clientServiceTaken::where('client_id', $fetchedData->id )->orderBy('created_at', 'desc')->get();
                                                //dd($serviceTakenArr);
                                                if( !empty($serviceTakenArr) && count($serviceTakenArr) >0 ){
                                                    foreach ($serviceTakenArr as $tokenkey => $tokenval) {
                                                        //echo "+++".htmlspecialchars($tokenval['mig_ref_no'])."<br/>";
                                                        if($tokenval['service_type']  == "Migration") {
                                                            $service_str = $tokenval['service_type']."-".htmlspecialchars($tokenval['mig_ref_no'])."-".htmlspecialchars($tokenval['mig_service'])."-".htmlspecialchars($tokenval['mig_notes']);
                                                        } else if($tokenval['service_type']  == "Education") {
                                                            $service_str = $tokenval['service_type']."-".htmlspecialchars($tokenval['edu_course'])."-".htmlspecialchars($tokenval['edu_college'])."-".htmlspecialchars($tokenval['edu_service_start_date'])."-".htmlspecialchars($tokenval['edu_notes']);
                                                        }
                                                        echo '<span id="'.$tokenval['id'].'">'.$service_str.' </span><i class="fa fa-edit service_taken_edit" style="cursor: pointer;color: #6777ef;" id="'.$tokenval['id'].'"></i><i class="fa fa-trash service_taken_trash" style="cursor: pointer;color: #6777ef;" id="'.$tokenval['id'].'"></i><br>';
                                                    
                                                    }
                                                } ?>
                                            </ul>
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
										<div class="form-group float-right">
											{{ Form::button('Save', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("edit-clients")' ]) }}
										</div>
									</div>
								</div> 
							</div>
						</div>	
					</div>
				</div>  
			 {{ Form::close() }}	
		</div>
	</section>
</div>
 <?php if($fetchedData->related_files != ''){
     $exploderel = explode(',', $fetchedData->related_files);
     foreach($exploderel AS $EXP){ 
         $relatedclients = \App\Admin::where('id', $EXP)->first();	
    ?>
 <input type="hidden" class="relatedfile" data-email="<?php echo $relatedclients->email; ?>" data-name="<?php echo $relatedclients->first_name.' '.$relatedclients->last_name; ?>" data-id="<?php echo $relatedclients->id; ?>">
 <?php
     }
 }
 ?>

<?php
if($fetchedData->tagname != ''){
   $tagnameArr = explode(',', $fetchedData->tagname);
   foreach($tagnameArr AS $tag1){
       $tagWord = \App\Tag::where('id', $tag1)->first();
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

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')

<!--
The `defer` attribute causes the callback to execute after the full HTML
document has been parsed. For non-blocking uses, avoiding race conditions,
and consistent behavior across browsers, consider loading using Promises.
See https://developers.google.com/maps/documentation/javascript/load-maps-js-api
for more information.
-->

<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo env('GOOGLE_MAPS_API_KEY');?>&callback=initAutocomplete&libraries=places&v=weekly" defer></script>

<script>
/**
 * @license
 * Copyright 2019 Google LLC. All Rights Reserved.
 * SPDX-License-Identifier: Apache-2.0
 */
// @ts-nocheck TODO remove when fixed
// This example adds a search box to a map, using the Google Place Autocomplete
// feature. People can enter geographical searches. The search box will return a
// pick list containing a mix of places and predicted search terms.
// This example requires the Places library. Include the libraries=places
// parameter when you first load the API. For example:
// <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

function initAutocomplete() {
  const map = new google.maps.Map(document.getElementById("map"), {
    center: { lat: -33.8688, lng: 151.2195 },
    zoom: 13,
    mapTypeId: "roadmap",
  });
  // Create the search box and link it to the UI element.
  const input = document.getElementById("pac-input");
  const searchBox = new google.maps.places.SearchBox(input);

  map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
  // Bias the SearchBox results towards current map's viewport.
  map.addListener("bounds_changed", () => {
    searchBox.setBounds(map.getBounds());
  });

  let markers = [];

  // Listen for the event fired when the user selects a prediction and retrieve
  // more details for that place.
  searchBox.addListener("places_changed", () => {
    const places = searchBox.getPlaces();

    if (places.length == 0) {
      return;
    }

    // Clear out the old markers.
    markers.forEach((marker) => {
      marker.setMap(null);
    });
    markers = [];

    // For each place, get the icon, name and location.
    const bounds = new google.maps.LatLngBounds();

    places.forEach((place) => {
      if (!place.geometry || !place.geometry.location) {
        console.log("Returned place contains no geometry");
        return;
      }
      
        if(place.formatted_address != "") {
            var address = place.formatted_address;
            $.ajax({
                type:'post',
                url:"{{URL::to('/')}}/admin/address_auto_populate",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {address:address},
                success: function(response){
                    var obj = $.parseJSON(response);
                    if(obj.status == 1){
                        $('#postal_code').val(obj.postal_code);
                        $('#locality').val(obj.locality);
                    } else {
                        $('#postal_code').val("");
                        $('#locality').val("");
                    }
                }
            });
        }

      const icon = {
        url: place.icon,
        size: new google.maps.Size(71, 71),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(17, 34),
        scaledSize: new google.maps.Size(25, 25),
      };

      // Create a marker for each place.
      markers.push(
        new google.maps.Marker({
          map,
          icon,
          title: place.name,
          position: place.geometry.location,
        }),
      );
      if (place.geometry.viewport) {
        // Only geocodes have viewport.
        bounds.union(place.geometry.viewport);
      } else {
        bounds.extend(place.geometry.location);
      }
    });
    map.fitBounds(bounds);
  });
}

window.initAutocomplete = initAutocomplete;
</script>
<script src="{{URL::asset('public/js/bootstrap-datepicker.js')}}"></script>
<script>
jQuery(document).ready(function($){
  
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

    $('#edu_service_start_date').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true
    });

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

                $('#service_taken_complete').html("");
                $('.client_info').css('display','none');
                $('#service_taken_complete').css('display','block');
                $.each(res, function(index, value) {
                    if(value.service_type == 'Migration') {
                        var html =  value.service_type+'-'+value.mig_ref_no+'-'+value.mig_service+'-'+value.mig_notes+' ' ;
                    } else if(value.service_type == 'Education') {
                        var html =  value.service_type+'-'+value.edu_course+'-'+value.edu_college+'-'+value.edu_service_start_date+'-'+value.edu_notes+' ';
                    }
                    const newItem = $('<span id="'+value.id+'"></span>').text(html);
                    $('#service_taken_complete').append(newItem);  //Append the item to the container

					var edit_icon = $('<i class="fa fa-edit service_taken_edit" style="cursor: pointer;color: #6777ef;" id="'+value.id+'"></i>');
                    $('#service_taken_complete').append(edit_icon);

                    var del_icon = $('<i class="fa fa-trash service_taken_trash" style="cursor: pointer;color: #6777ef;" id="'+value.id+'"></i>');
                    $('#service_taken_complete').append(del_icon);
                    $('#service_taken_complete').append('<br>'); //Append a line break after each item
                });
                //$('#service_taken_complete').html(html);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText); // Handle error response
            }
        });
    });

    //delete
	$(document).delegate('.service_taken_trash', 'click', function(e){
        var conf = confirm('Are you sure want to delete this?');
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
                        alert(obj.message);
						if ( $('.client_info').css('display') === 'none' ) {
							$('#service_taken_complete span#'+obj.record_id).remove();
						}
						else if ( $('#service_taken_complete').css('display') === 'none' ) {
							$('.client_info span#'+obj.record_id).remove();
						}


                        var editSpan = $('.service_taken_edit#'+obj.record_id);
                        $('.service_taken_edit#'+obj.record_id).remove();

                        var targetSpan = $('.service_taken_trash#'+obj.record_id);

                        // Find the <br> element that follows the <span>
                        var brElement = targetSpan.next('br');

                        $('.service_taken_trash#'+obj.record_id).remove();
                        // Remove the <br> element
                        brElement.remove();

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
		if ($('.other_email_div').css('display') == 'none') {
			$('.other_email_div').css('display','inline-block');
			$('.other_phone_div').css('display','inline-block');
			$('.add_other_email_phone').html('<i class="fa fa-minus" aria-hidden="true"></i>');
		} else {
			$('.other_email_div').css('display','none');
			$('.other_phone_div').css('display','none');
			$('.add_other_email_phone').html('<i class="fa fa-plus" aria-hidden="true"></i>');
		}
	});
    
    $('.manual_email_phone_verified').on('change', function(){ 
        if( $(this).is(":checked") ) {
            $('.manual_email_phone_verified').val(1);
        } else { 
            $('.manual_email_phone_verified').val(0);
        }
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
	$(".js-data-example-ajaxcc").select2({
  data: data,
  escapeMarkup: function(markup) {
    return markup;
  },
  templateResult: function(data) {
    return data.html;
  },
  templateSelection: function(data) {
    return data.text;
  }
});
	$('.js-data-example-ajaxcc').val(array);
		$('.js-data-example-ajaxcc').trigger('change');
	
	
	<?php } ?>
	
$('.js-data-example-ajaxcc').select2({
		 multiple: true,
		 closeOnSelect: false,
	
		  ajax: {
			url: '{{URL::to('/admin/clients/get-recipients')}}',
			dataType: 'json',
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