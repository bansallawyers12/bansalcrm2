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
													{{ Form::text('client_id', @$fetchedData->client_id, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off', 'id' => 'checkclientid', 'placeholder'=>'' )) }}
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
											{{ Form::text('address', @$fetchedData->address, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
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
											{{ Form::text('city', @$fetchedData->city, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
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
											{{ Form::text('zip', @$fetchedData->zip, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
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
											<select style="padding: 0px 5px;" name="assign_to" id="assign_to" class="form-control select2" data-valid="required">
											<?php
												$admins = \App\Admin::where('role','!=',7)->orderby('first_name','ASC')->get();
												foreach($admins as $admin){
													 $branchname = \App\Branch::where('id',$admin->office_id)->first();
												?>
												<option @if(@$fetchedData->assign_to == $admin->id) selected @endif value="<?php echo $admin->id; ?>"><?php echo $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')'; ?></option>
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
												<option value="Sub Agent">Sub Agent</option>
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
										$explodee = array();
	if($fetchedData->tagname != ''){
		$explodee = explode(',', $fetchedData->tagname);
	} 
											?>
											<select multiple class="form-control select2" name="tagname[]">
												<option value="">-- Search & Select tag --</option>
												<?php
												foreach(\App\Tag::all() as $tags){
													?>
													<option <?php if(in_array($tags->id, $explodee)){ echo 'selected'; } ?>  value="{{$tags->id}}">{{$tags->name}}</option>
													<?php
												}
												?>	 
											</select>
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
@endsection

@section('scripts')
<script>
jQuery(document).ready(function($){
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