@extends('layouts.admin')
@section('title', 'Add Partner')

@section('content')
<style>
.addbranch .error label{
	color: #9f3a38;
}
.addbranch .error input{
	background: #fff6f6;
    border-color: #e0b4b4;
    color: #9f3a38;
    border-radius: "";
    box-shadow: none;
}
</style>
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			{!! Form::open(array('route' => 'partners.store', 'method' => 'post', 'name'=>"add-partner", 'autocomplete'=>'off', "enctype"=>"multipart/form-data"))  !!}
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Add Partners</h4>
								<div class="card-header-action">
									<a href="{{route('partners.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-body">
								<div id="accordion">
									<div class="accordion">
										<div class="accordion-header" role="button" data-bs-toggle="collapse" data-bs-target="#primary_info" aria-expanded="true">
											<h4>Primary Information</h4>
										</div>
										<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
											<div class="row">
												<div class="col-12 col-md-3 col-lg-3">
													<div class="form-group">
														<div class="profile_upload">
															<div class="upload_content">
																<img style="width:100px;height:100px;" id="output"/>
																<i class="fa fa-camera if_image"></i>
																<span class="if_image">Upload Profile Image</span>
															</div>
															<input type="file" onchange="loadFile(event)" id="profile_img" name="profile_img" class="form-control" autocomplete="off" />
														</div>
														@if ($errors->has('profile_img'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('profile_img') }}</strong>
															</span>
														@endif
													</div>
												</div>
												<div class="col-12 col-md-9 col-lg-9">
													<div class="row">
														<div class="col-12 col-md-6 col-lg-6">
															<div class="form-group">
																<label for="master_category">Master Category <span class="span_req">*</span></label>
																<select data-valid="required" id="getpartnertype" class="form-control addressselect2" name="master_category">
																	<option value="">Select a Master Category</option>
																	@foreach(\App\Models\Category::all() as $clist)
																	<option value="{{$clist->id}}">{{$clist->category_name}}</option>
																@endforeach
																</select>
																@if ($errors->has('master_category'))
																	<span class="custom-error" role="alert">
																		<strong>{{ @$errors->first('master_category') }}</strong>
																	</span>
																@endif
															</div>
														</div>
														<div class="col-12 col-md-6 col-lg-6">
															<div class="form-group">
																<label for="partner_type">Partner Type <span class="span_req">*</span></label>
																<select data-valid="required" id="partner_type" class="form-control addressselect2 " name="partner_type">
																	<option value="">Select a Partner Type</option>
																</select>
																@if ($errors->has('partner_type'))
																	<span class="custom-error" role="alert">
																		<strong>{{ @$errors->first('partner_type') }}</strong>
																	</span>
																@endif
															</div>
														</div>
														<div class="col-12 col-md-6 col-lg-6">
															<div class="form-group">
																<label for="partner_name">Trading Name <span class="span_req">*</span></label>
																{!! Form::text('partner_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Trading Name' ))  !!}
																@if ($errors->has('partner_name'))
																	<span class="custom-error" role="alert">
																		<strong>{{ @$errors->first('partner_name') }}</strong>
																	</span>
																@endif
															</div>
														</div>
														<div class="col-12 col-md-6 col-lg-6">
															<div class="form-group">
																<label for="business_reg_no">ABN</label>
																{!! Form::text('business_reg_no', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter ABN' ))  !!}
																@if ($errors->has('business_reg_no'))
																	<span class="custom-error" role="alert">
																		<strong>{{ @$errors->first('business_reg_no') }}</strong>
																	</span>
																@endif
															</div>
														</div>
														<div class="col-12 col-md-6 col-lg-6">
															<div class="form-group">
																<label for="service_workflow">Service Workflow <span class="span_req">*</span></label>
																<select data-valid="required" class="form-control addressselect2 " name="service_workflow">
																	<option value="">Choose Service workflow</option>
																	@foreach(\App\Models\Workflow::all() as $wlist)
																		<option value="{{$wlist->id}}">{{$wlist->name}}</option>
																	@endforeach

																</select>
																@if ($errors->has('service_workflow'))
																	<span class="custom-error" role="alert">
																		<strong>{{ @$errors->first('service_workflow') }}</strong>
																	</span>
																@endif
															</div>
														</div>
														<div class="col-12 col-md-6 col-lg-6">
															<div class="form-group">
																<label for="currency">Currency <span class="span_req">*</span></label>
																<div class="bfh-selectbox bfh-currencies" data-currency="AUD" data-flags="true" data-name="currency"></div>
																@if ($errors->has('currency'))
																	<span class="custom-error" role="alert">
																		<strong>{{ @$errors->first('currency') }}</strong>
																	</span>
																@endif
															</div>
														</div>

                                                        <div class="col-12 col-md-6 col-lg-6">
															<div class="form-group">
																<label for="legal_name">Legal Name</label>
																{!! Form::text('legal_name', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Legal Name' ))  !!}
																@if ($errors->has('legal_name'))
																	<span class="custom-error" role="alert">
																		<strong>{{ @$errors->first('legal_name') }}</strong>
																	</span>
																@endif
															</div>
														</div>

													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="accordion">
										<div class="accordion-header" role="button" data-bs-toggle="collapse" data-bs-target="#address" aria-expanded="true">
											<h4>Address</h4>
										</div>
										<div class="accordion-body collapse show" id="address" data-parent="#accordion">
											<div class="row">
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group">
														<label for="address">Address</label>
														{!! Form::text('address', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Address' ))  !!}
														@if ($errors->has('address'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('address') }}</strong>
															</span>
														@endif
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group">
														<label for="city">City</label>
														{!! Form::text('city', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter City' ))  !!}
														@if ($errors->has('city'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('city') }}</strong>
															</span>
														@endif
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group">
														<label for="state">State</label>
														{!! Form::text('state', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter State' ))  !!}
														@if ($errors->has('state'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('state') }}</strong>
															</span>
														@endif
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group">
														<label for="zip">Zip / Post Code</label>
														{!! Form::text('zip', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Zip / Post Code' ))  !!}
														@if ($errors->has('zip'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('zip') }}</strong>
															</span>
														@endif
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group">
														<label for="zip" style="visibility:hidden;">Zip / Post Code</label>
														<br>
														<label for=""><input checked  type="radio" value="1" name="is_regional"> Regional</label>
																<label for=""><input type="radio" value="0" name="is_regional"> Non Regional</label>
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group">
														<label for="country">Country</label>
														<select class="form-control addressselect2" name="country" >
														<?php
															foreach(\App\Models\Country::all() as $list){
																?>
																<option @if(@$list->name == 'Australia') selected @endif value="{{@$list->name}}">{{@$list->name}}</option>
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
									<div class="accordion">
										<div class="accordion-header" role="button" data-bs-toggle="collapse" data-bs-target="#contact_details" aria-expanded="true">
											<h4>Contact Details</h4>
										</div>
										<div class="accordion-body collapse show" id="contact_details" data-parent="#accordion">
											
											<div class="col-12 col-md-12 col-lg-12">
                                                <div class="row">
                                                    <div class="col-6 col-md-6 col-lg-6">
                                                       <a href="javascript:;" class="btn btn-outline-primary openpartnerphonenew" style="margin-bottom: 5px;margin-left: -15px;"><i class="fa fa-plus"></i> Add Contact</a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-12 col-lg-12">
                                                <div class="row">
                                                    <div class="col-3 col-md-3 col-lg-3">
                                                        <div class="form-group">
                                                            <label for="partner_phone_type">Contact Type <span style="color:#ff0000;">*</span></label>
                                                            <select name="partner_phone_type[]" id="partner_phone_type" class="form-control">
                                                                <option value="">Select</option>
                                                                <option value="Personal">Personal</option>
                                                                <option value="Secondary">Secondary</option>
                                                                <option value="Not In Use">Not In Use</option>
                                                            </select>
                                                            @if ($errors->has('partner_phone_type'))
                                                                <span class="custom-error" role="alert">
                                                                    <strong>{{ @$errors->first('partner_phone_type') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="col-4 col-md-4 col-lg-4">
                                                        <div class="form-group">
                                                            <label for="phone">Phone Number</label>
                                                            <div class="cus_field_input">
                                                                <div class="country_code">
                                                                    <input class="telephone" id="telephone" type="tel" name="partner_country_code[]"  >
                                                                </div>
                                                                {!! Form::text('partner_phone[]', '', array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Phone' ))  !!}
                                                                @if ($errors->has('partner_phone'))
                                                                    <span class="custom-error" role="alert">
                                                                        <strong>{{ @$errors->first('partner_phone') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="partnerphonedata">
                                            </div>

                                            <div class="col-12 col-md-12 col-lg-12">
                                                <div class="row">
                                                    <div class="col-6 col-md-6 col-lg-6">
                                                    <a href="javascript:;" class="btn btn-outline-primary openpartneremailnew" style="margin-bottom: 5px;margin-left: -15px;"><i class="fa fa-plus"></i> Add Email</a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-3 col-md-3 col-lg-3">
                                                    <div class="form-group">
                                                        <label for="partner_email_type">Email Type <span style="color:#ff0000;">*</span></label>
                                                        <select name="partner_email_type[]" id="partner_email_type" class="form-control">
                                                            <option value="">Select</option>
                                                            <option value="Personal">Personal</option>
                                                            <option value="Secondary">Secondary</option>
                                                            <option value="Not In Use">Not In Use</option>
                                                        </select>
                                                        @if ($errors->has('partner_email_type'))
                                                            <span class="custom-error" role="alert">
                                                                <strong>{{ @$errors->first('partner_email_type') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
												<div class="col-4 col-md-4 col-lg-4">
                                                    <div class="form-group">
                                                        <label for="partner_email">Email <span class="span_req">*</span></label>
                                                        {!! Form::text('partner_email[]', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Email' ))  !!}
														@if ($errors->has('partner_email'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('partner_email') }}</strong>
															</span>
														@endif
													</div>
												</div>
                                            </div>

                                            <div class="partneremaildata">
                                            </div>
                                           

                                            
                                            


                                            <div class="row">
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group">
														<label for="fax">Fax</label>
														{!! Form::text('fax', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Fax' ))  !!}
														@if ($errors->has('fax'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('fax') }}</strong>
															</span>
														@endif
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group">
														<label for="website">Website</label>
														{!! Form::text('website', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Website' ))  !!}
														@if ($errors->has('website'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('website') }}</strong>
															</span>
														@endif
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group">
														<label for="level">Partner Level</label>
														{!! Form::text('level', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter level' ))  !!}
														@if ($errors->has('level'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('level') }}</strong>
															</span>
														@endif
													</div>
												</div>
											</div>
										</div>
									</div>
                                  
                                  
									<div class="accordion">
										<div class="accordion-header" role="button" data-bs-toggle="collapse" data-bs-target="#branch" aria-expanded="true">
											<h4>Branch</h4>
										</div>
										<div class="accordion-body collapse show" id="branch" data-parent="#accordion">
											<div class="row">
												<div class="col-12 col-md-12 col-lg-4">
													<a href="javascript:;" class="btn btn-outline-primary openbranchnew" ><i class="fa fa-plus"></i> Add Branch</a>
												</div>
											</div>

											<div class="branchdata">

											</div>
										</div>
									</div>
								</div>
								<div class="form-group float-end">
									{!! Form::button('Save Partner', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-partner")' ])  !!}
								</div>
							</div>
						</div>
					</div>
				</div>
			 {!! Form::close()  !!}
		</div>
	</section>
</div>

<div class="modal fade addbranch custom_modal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">Add New Branch</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" id="branchform" autocomplete="off" enctype="multipart/form-data">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch_name">Name <span class="span_req">*</span></label>
								{!! Form::text('branch_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Name' ))  !!}
								<span class="custom-error branch_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch_email">Email <span class="span_req">*</span></label>
								{!! Form::text('branch_email', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Email' ))  !!}
									<span class="custom-error branch_email_error" role="alert">
										<strong></strong>
									</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch_country">Country</label>
								<select class="form-control branch_country select2" name="branch_country" >
									<option value="">Select</option>
									<?php
									foreach(\App\Models\Country::all() as $list){
										?>
										<option @if(@$list->name == 'Australia') selected @endif value="{{@$list->name}}">{{@$list->name}}</option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch_city">City</label>
								{!! Form::text('branch_city', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter City' ))  !!}
								@if ($errors->has('branch_city'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('branch_city') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch_state">State</label>
								{!! Form::text('branch_state', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter State' ))  !!}
								@if ($errors->has('branch_state'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('branch_state') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch_address">Street</label>
								{!! Form::text('branch_address', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Street' ))  !!}
								@if ($errors->has('branch_address'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('branch_address') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch_zip">Zip Code</label>
								{!! Form::text('branch_zip', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Zip / Post Code' ))  !!}
								@if ($errors->has('branch_zip'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('branch_zip') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<div class="form-group">
									<label style="visibility:hidden;" for="zip">Zip / Post Code</label>
									<br>
									<label for=""><input class="branchregional" checked type="radio" value="1" name="branch_regional"> Regional</label>
									<label for=""><input class="branchnonregional" type="radio" value="0" name="branch_regional"> Non Regional</label>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch_phone">Phone</label>
								<div class="cus_field_input">
									<div class="country_code">
										<input class="telephone" id="telephone" type="tel" value="{{ config('phone.default_country_code', '+61') }}" name="brnch_country_code" readonly >
									</div>
									{!! Form::text('branch_phone', '', array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Phone' ))  !!}
									@if ($errors->has('branch_phone'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('branch_phone') }}</strong>
										</span>
									@endif
								</div>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button type="button" class="btn btn-primary savebranch">Save</button>
							<button type="button" id="update_branch" style="display:none" class="btn btn-primary">Update</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>



<div class="modal fade addpartneremail custom_modal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="clientEmailModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientEmailModalLabel">Add New Partner Email</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" id="partneremailform" autocomplete="off" enctype="multipart/form-data">
					<div class="row">
                        <div class="col-3 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="partner_email_type">Email Type <span style="color:#ff0000;">*</span></label>
								<select name="partner_email_type[]" id="partner_email_type" class="form-control">
                                    <option value="">Select</option>
                                    <option value="Personal">Personal</option>
                                    <option value="Secondary">Secondary</option>
                                    <option value="Not In Use">Not In Use</option>
                                </select>
								@if ($errors->has('partner_email_type'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('partner_email_type') }}</strong>
									</span>
								@endif
							</div>
						</div>
                        <div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="partner_email">Email <span class="span_req">*</span></label>
								{!! Form::text('partner_email', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Email' ))  !!}
									<span class="custom-error partner_email_error" role="alert">
										<strong></strong>
									</span>
							</div>
						</div>

                        <div class="col-12 col-md-12 col-lg-12">
							<button type="button" class="btn btn-primary savepartneremail">Save</button>
							<button type="button" id="update_partneremail" style="display:none" class="btn btn-primary">Update</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<div class="modal fade addpartnerphone custom_modal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="clientPhoneModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientPhoneModalLabel">Add New Partner Phone Number</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" id="partnerphoneform" autocomplete="off" enctype="multipart/form-data">
					<div class="row">
                        <div class="col-3 col-md-3 col-lg-3">
							<div class="form-group">
								<label for="partner_phone_type">Contact Type <span style="color:#ff0000;">*</span></label>
                                <select name="partner_phone_type" id="partner_phone_type" class="form-control">
                                    <option value="">Select</option>
                                    <option value="Personal">Personal</option>
                                    <option value="Secondary">Secondary</option>
                                    <option value="Not In Use">Not In Use</option>
                                </select>
                                @if ($errors->has('partner_phone_type'))
                                    <span class="custom-error" role="alert">
                                        <strong>{{ @$errors->first('partner_phone_type') }}</strong>
                                    </span>
                                @endif
							</div>
						</div>

                        <div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="partner_phone">Phone Number </label>
								<div class="cus_field_input">
									<div class="country_code">
										<input class="telephone" id="telephone" type="tel" name="partner_country_code" readonly >
									</div>
									{!! Form::text('partner_phone', '', array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Phone' ))  !!}
									@if ($errors->has('partner_phone'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('partner_phone') }}</strong>
										</span>
									@endif
								</div>
							</div>
						</div>

                        <div class="col-12 col-md-12 col-lg-12">
							<button type="button" class="btn btn-primary savepartnerphone">Save</button>
							<button type="button" id="update_partnerphone" style="display:none" class="btn btn-primary">Update</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>



@section('scripts') 
<script>
// Cache buster: <?php echo time(); ?>

jQuery(document).ready(function($){

	function validateEmail(sEmail) {
        var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
        if (filter.test(sEmail)) {
            return true;
        } else {
            return false;
        }
    }
	var branchdata = new Array();
    var itag = $('.branchdata .row').length;

	$(document).delegate('.openbranchnew','click', function(){
		$('#clientModalLabel').html('Add New Branch');
		$('.savebranch').show();
		$('#update_branch').hide();
		$('#branchform')[0].reset();
		$(".branch_country").val('Australia').trigger('change') ;
	    $('.addbranch').modal('show');
	});

    $(document).delegate('#getpartnertype','change', function(){
		$('.popuploader').show();
		var v = $('#getpartnertype option:selected').val();
		$.ajax({
			url: '{{URL::to('/getpaymenttype')}}',
			type:'GET',
			data:{cat_id:v},
			success:function(response){
				$('.popuploader').hide();
				$('#partner_type').html(response);
				
				// Re-initialize Partner Type dropdown after AJAX load
				console.log('Partner Type: Re-initializing after AJAX with', $('#partner_type option').length, 'options');
				$('#partner_type').select2('destroy');
				$('#partner_type').select2({
					minimumResultsForSearch: Infinity,
					width: '100%'
				});
				console.log('Partner Type: Re-initialization complete');
			}
		});
	});

    $(document).delegate('.savebranch','click', function() {
		var branch_name = $('input[name="branch_name"]').val();
		var branch_email = $('input[name="branch_email"]').val();
		$('.branch_name_error').html('');
		$('.branch_email_error').html('');
		$('input[name="branch_name"]').parent().removeClass('error');
		$('input[name="branch_email"]').parent().removeClass('error');
		if ($('table#metatag_table').find('#metatag_'+itag).length > 0) {
		} else {
			var flag = false;
			if(branch_name == ''){
				$('.branch_name_error').html('The Name field is required.');
				$('input[name="branch_name"]').parent().addClass('error');
				flag = true;
			}
			if(branch_email == ''){
				$('.branch_email_error').html('The Name field is required.');
				$('input[name="branch_email"]').parent().addClass('error');
				flag = true;
			}
            else if(!validateEmail($.trim(branch_email)))
			{
				$('.branch_email_error').html('Email is invalid.');
				$('input[name="branch_email"]').parent().addClass('error');
				flag = true;
			}

            if(!flag){
				var str = $( "#branchform" ).serializeArray();
	            console.log(str);
				branchdata[itag] = {
					"name":str[0].value,
					"email":str[1].value,
					"country":str[2].value,
					"city":str[3].value,
					"state":str[4].value,
					"street":str[5].value,
					"zip":str[6].value,
					"rgcode":str[7].value,
					"ccode":str[8].value,
					"phone":str[9].value,
                }
				console.log(branchdata);
				var html = '<div class="row" id="metatag_'+itag+'"><div class="col-12 col-md-3 col-lg-3">';
					html += '<div class="form-group">';
					html += '<label for="bname">Name</label>';
					html += '<input class="form-control" readonly autocomplete="off" placeholder="" name="branchname[]" type="text" value="'+str[0].value+'">';
					html += '</div>';
				    html += '</div>';
				    html += '<div class="col-12 col-md-3 col-lg-3">';
					html += '<div class="form-group">';
					html += '<label for="bemail">Email</label>';
					html += '<input class="form-control" readonly autocomplete="off" placeholder="" name="branchemail[]" type="text" value="'+str[1].value+'">';
					html += '</div>';
				    html += '</div>';
				    html += '<div class="col-12 col-md-3 col-lg-3">';
					html += '<div class="form-group">';
					html += '<label for="bcountry">Country</label>';
				    html += '<input class="form-control" readonly autocomplete="off" placeholder="" name="branchcountry[]" type="text" value="'+str[2].value+'">';
					html += '</div>';
				    html += '</div>';
				    html += '<div class="col-12 col-md-2 col-lg-2">';
					html += '<div class="form-group">';
					html += '<label for="bcity">City</label>';
					html += '<input class="form-control" readonly autocomplete="off" placeholder="" name="branchcity[]" type="text" value="'+str[3].value+'">';
					html += '</div>';
				    html += '<input autocomplete="off" placeholder="" name="branchstate[]" type="hidden" value="'+str[4].value+'"><input autocomplete="off" placeholder="" name="branchaddress[]" type="hidden" value="'+str[5].value+'"><input autocomplete="off" placeholder="" name="branchzip[]" type="hidden" value="'+str[6].value+'"><input autocomplete="off" placeholder="" name="branchreg[]" type="hidden" value="'+str[7].value+'"><input autocomplete="off" placeholder="" name="branchcountry_code[]" type="hidden" value="'+str[8].value+'"><input autocomplete="off" placeholder="" name="branchphone[]" type="hidden" value="'+str[9].value+'"></div>';
				    html += '<div class="col-12 col-md-1 col-lg-1">';
					html +=  '<a href="javascript:;" dataid="'+itag+'" class="editbranch"><i class="fa fa-edit"></i></a>';
					html +=  '<a href="javascript:;" dataid="'+itag+'" class="deletebranch"><i class="fa fa-times"></i></a>';
					html += '</div>';
				    html += '</div></div>';
				$('.branchdata').append(html);
				$('#branchform')[0].reset();
				$('.addbranch').modal('hide');
                $(".branch_country").val('').trigger('change') ;
				itag++;
			}
		}
    });

	$(document).delegate('#update_branch','click', function(){
		var branch_name = $('input[name="branch_name"]').val();
		var branch_email = $('input[name="branch_email"]').val();
		$('.branch_name_error').html('');
		$('.branch_email_error').html('');
		$('input[name="branch_name"]').parent().removeClass('error');
		$('input[name="branch_email"]').parent().removeClass('error');

        var flag = false;
        if(branch_name == ''){
            $('.branch_name_error').html('The Name field is required.');
            $('input[name="branch_name"]').parent().addClass('error');
            flag = true;
        }
        if(branch_email == ''){
            $('.branch_email_error').html('The Name field is required.');
            $('input[name="branch_email"]').parent().addClass('error');
            flag = true;
        }

        if(!flag){
            var str = $( "#branchform" ).serializeArray();

            branchdata[mtval] = {
                "name":str[0].value,
                "email":str[1].value,
                "country":str[2].value,
                "city":str[3].value,
                "state":str[4].value,
                "street":str[5].value,
                "zip":str[6].value,
                "rgcode":str[7].value,
                "ccode":str[8].value,
                "phone":str[9].value,
            }
            console.log(branchdata);
            var html = '<div class="col-12 col-md-3 col-lg-3">';
                html += '<div class="form-group">';
                html += '<label for="bname">Name</label>';
                html += '<input class="form-control" readonly autocomplete="off" placeholder="" name="branchname[]" type="text" value="'+str[0].value+'">';
                html += '</div>';
				html += '</div>';
				html += '<div class="col-12 col-md-3 col-lg-3">';
                html += '<div class="form-group">';
                html += '<label for="bemail">Email</label>';
                html += '<input class="form-control" readonly autocomplete="off" placeholder="" name="branchemail[]" type="text" value="'+str[1].value+'">';
                html += '</div>';
				html += '</div>';
				html += '<div class="col-12 col-md-3 col-lg-3">';
				html += '<div class="form-group">';
				html += '<label for="bcountry">Country</label>';
				html += '<input class="form-control" readonly autocomplete="off" placeholder="" name="branchcountry[]" type="text" value="'+str[2].value+'">';
				html += '</div>';
				html += '</div>';
				html += '<div class="col-12 col-md-2 col-lg-2">';
				html += '<div class="form-group">';
                html += '<label for="bcity">City</label>';
                html += '<input class="form-control" readonly autocomplete="off" placeholder="" name="branchcity[]" type="text" value="'+str[3].value+'">';
				html += '</div>';
				html += '<input autocomplete="off" placeholder="" name="branchstate[]" type="hidden" value="'+str[4].value+'"><input autocomplete="off" placeholder="" name="branchaddress[]" type="hidden" value="'+str[5].value+'"><input autocomplete="off" placeholder="" name="branchzip[]" type="hidden" value="'+str[6].value+'"><input autocomplete="off" placeholder="" name="branchreg[]" type="hidden" value="'+str[7].value+'"><input autocomplete="off" placeholder="" name="branchcountry_code[]" type="hidden" value="'+str[8].value+'"><input autocomplete="off" placeholder="" name="branchphone[]" type="hidden" value="'+str[9].value+'"></div>';
				html += '<div class="col-12 col-md-1 col-lg-1">';
				html +=  '<a href="javascript:;" dataid="'+mtval+'" class="editbranch"><i class="fa fa-edit"></i></a>';
				html +=  '<a href="javascript:;" dataid="'+mtval+'" class="deletebranch"><i class="fa fa-times"></i></a>';
				html += '</div>';
				html += '</div>';
            $('#metatag_'+mtval).html(html);

            $('#branchform')[0].reset();
            $('.addbranch').modal('hide');
        }
    });

	var mtval = 0;
	$(document).delegate('.editbranch','click', function(){
		var v = $(this).attr('dataid');
		$('.addbranch').modal('show');
		console.log(branchdata);
		var c = branchdata[v];
		mtval = v;
		$('input[name="branch_name"]').val(c.name);
		$('input[name="branch_email"]').val(c.email);
		$('input[name="branch_city"]').val(c.city);
		$('input[name="branch_state"]').val(c.state);
		$('input[name="branch_address"]').val(c.street);
		$('input[name="branch_zip"]').val(c.zip);
		if(c.rgcode == 1){
			$('.branchregional').prop('checked', true);
		}else{
			$('.branchnonregional').prop('checked', true);
		}

		$('input[name="branch_phone"]').val(c.phone);
		$(".branch_country").val(c.country).trigger('change') ;
		//alert(c.ccode);
		$('#telephone').val(c.ccode);
		$('#clientModalLabel').html('Edit Branch');
		$('.savebranch').hide();
		$('#update_branch').show();
    });

    $(document).delegate('.deletebranch','click', function(){
		var v = $(this).attr('dataid');
		$('#metatag_'+v).remove();
	});

	console.log('Partner Create: Initializing Select2 v2');
	
	// Destroy existing Select2 instances if they exist (check each element individually)
	$(".addressselect2").each(function() {
		if ($(this).hasClass("select2-hidden-accessible")) {
			$(this).select2('destroy');
			console.log('Destroyed old Select2 instance:', $(this).attr('id') || $(this).attr('name'));
		}
	});
	
	// Initialize .select2 elements in modal (only if modal exists)
	if ($(".addbranch .modal-content").length > 0) {
		$(".select2").select2({ dropdownParent: $(".addbranch .modal-content") });
	}
    
    // Initialize addressselect2 elements without search
    $(".addressselect2").each(function() {
        var $element = $(this);
        try {
            $element.select2({
                minimumResultsForSearch: Infinity,  // Disable search
                width: '100%'
            });
            console.log('Initialized Select2 on:', $element.attr('id') || $element.attr('name'), 'with', $element.find('option').length, 'options');
        } catch (error) {
            console.error('Failed to initialize Select2 on:', $element.attr('id') || $element.attr('name'), error);
        }
    });
	
	console.log('Partner Create: Select2 initialization complete');

	// Force re-initialize after a short delay to override any other scripts
	setTimeout(function() {
		console.log('Partner Create: Force re-initializing Master Category dropdown');
		$('#getpartnertype').select2('destroy');
		$('#getpartnertype').select2({
			minimumResultsForSearch: Infinity,
			width: '100%',
			data: $('#getpartnertype option').map(function() {
				return {
					id: $(this).val(),
					text: $(this).text()
				};
			}).get()
		});
		console.log('Partner Create: Force re-initialization complete with', $('#getpartnertype option').length, 'options');
		
		// Also force re-initialize Partner Type dropdown
		if ($('#partner_type').length > 0) {
			console.log('Partner Type: Force re-initializing');
			$('#partner_type').select2('destroy');
			$('#partner_type').select2({
				minimumResultsForSearch: Infinity,
				width: '100%'
			});
			console.log('Partner Type: Force re-initialization complete with', $('#partner_type option').length, 'options');
		}
		
		// Force re-initialize all other addressselect2 dropdowns
		$('.addressselect2').not('#getpartnertype').not('#partner_type').each(function() {
			var $elem = $(this);
			var elemId = $elem.attr('id') || $elem.attr('name');
			console.log('Force re-initializing:', elemId, 'with', $elem.find('option').length, 'options');
			$elem.select2('destroy');
			$elem.select2({
				minimumResultsForSearch: Infinity,
				width: '100%'
			});
		});
	}, 500);

    
    ////////////////////////////////////////
    ////////////////////////////////////////
    //// start add more partner email //////
    ////////////////////////////////////////
    ////////////////////////////////////////
    var partneremaildata = new Array();
    var itag_email = $('.partneremaildata .row').length;

    //Add partner email
    $(document).delegate('.openpartneremailnew','click', function(){
        $('#clientEmailModalLabel').html('Add New Partner Email');
        $('.savepartneremail').show();
        $('#update_partneremail').hide();
        $('#partneremailform')[0].reset();
        $('.addpartneremail').modal('show');
    });

    //Save partner email
    $(document).delegate('.savepartneremail','click', function() {
        var partner_email_type = $('input[name="partner_email_type"]').val();
        $('.partner_email_type_error').html('');
        $('input[name="partner_email_type"]').parent().removeClass('error');

        var partner_email = $('input[name="partner_email"]').val();
        $('.partner_email_error').html('');
        $('input[name="partner_email"]').parent().removeClass('error');

        if ($('table#metatag_table').find('#metatag_'+itag_email).length > 0) {
        }
        else {
            var flag = false;
            if(partner_email_type == ''){
                $('.partner_email_type_error').html('The Email Type field is required.');
                $('input[name="partner_email_type"]').parent().addClass('error');
                flag = true;
            }
            if(partner_email == ''){
                $('.partner_email_error').html('The Email field is required.');
                $('input[name="partner_email"]').parent().addClass('error');
                flag = true;
            }
            else if(!validateEmail($.trim(partner_email))) {
                $('.partner_email_error').html('Email is invalid.');
                $('input[name="partner_email"]').parent().addClass('error');
                flag = true;
            }

            if(!flag){
                var str = $( "#partneremailform" ).serializeArray();
                console.log(str);
                partneremaildata[itag_email] = {"email_type":str[0].value,"email":str[1].value}
                console.log(partneremaildata);

                var html = '<div class="col-12 col-md-12 col-lg-12">';
                    html += '<div class="row" id="metatag_'+itag_email+'">';

                    html += '<div class="col-3 col-md-3 col-lg-3">';
                    html += '<div class="form-group">';
                    html += '<label for="partner_email_type">Email Type</label>';
                    html += `<select name="partner_email_type[]" id="partner_email_type" class="form-control">
                        <option value="" ${str[0].value === '' ? 'selected' : ''}>Select</option>
                        <option value="Personal" ${str[0].value === 'Personal' ? 'selected' : ''}>Personal</option>
                        <option value="Secondary" ${str[0].value === 'Secondary' ? 'selected' : ''}>Secondary</option>
                        <option value="Not In Use" ${str[0].value === 'Not In Use' ? 'selected' : ''}>Not In Use</option>
                    </select>`;
                    html += '</div>';
                    html += '</div>';

                    html += '<div class="col-4 col-md-4 col-lg-4">';
                    html += '<div class="form-group">';
                    html += '<label for="partner_email">Email</label>';
                    html += '<input class="form-control"  autocomplete="off" placeholder="" name="partner_email[]" type="text" value="'+str[1].value+'">';
                    html += '</div>';
                    html += '</div>';

                    //html += '<div class="col-12 col-md-1 col-lg-1">';
                    //html +=  '<a href="javascript:;" dataid="'+itag_email+'" class="editpartneremail"><i class="fa fa-edit"></i></a>';
                    //html +=  '<a href="javascript:;" dataid="'+itag_email+'" class="deletepartneremail"><i class="fa fa-times"></i></a>';
                    //html += '</div>';
                    html += '</div></div>';
                $('.partneremaildata').append(html);
                $('#partneremailform')[0].reset();
                $('.addpartneremail').modal('hide');
                itag_email++;
            }
        }
    });

    $(document).delegate('#update_partneremail','click', function(){
        var partner_email = $('input[name="partner_email"]').val();
        $('.partner_email_error').html('');
        $('input[name="partner_email"]').parent().removeClass('error');

        var flag = false;
        if(partner_email == ''){
            $('.partner_email_error').html('The Name field is required.');
            $('input[name="partner_email"]').parent().addClass('error');
            flag = true;
        }

        if(!flag){
            var str = $( "#partneremailform" ).serializeArray();
            partneremaildata[mtval_email] = {"email":str[0].value}
            console.log(partneremaildata);
            var html = '<div class="col-12 col-md-3 col-lg-3">';
                html += '<div class="form-group">';
                html += '<label for="partner_email">Email</label>';
                html += '<input class="form-control" readonly autocomplete="off" placeholder="" name="partner_email[]" type="text" value="'+str[0].value+'">';
                html += '</div>';
                html += '</div>';

                html += '<div class="col-12 col-md-1 col-lg-1">';
                html +=  '<a href="javascript:;" dataid="'+mtval_email+'" class="editpartneremail"><i class="fa fa-edit"></i></a>';
                html +=  '<a href="javascript:;" dataid="'+mtval_email+'" class="deletepartneremail"><i class="fa fa-times"></i></a>';
                html += '</div>';
                html += '</div>';
            $('#metatag_'+mtval_email).html(html);

            $('#partneremailform')[0].reset();
            $('.addpartneremail').modal('hide');
        }
    });

    var mtval_email = 0;
    $(document).delegate('.editpartneremail','click', function(){
        var v = $(this).attr('dataid');
        $('.addpartneremail').modal('show');
        console.log(partneremaildata);
        var c = partneremaildata[v];
        mtval_email = v;
        $('input[name="partner_email"]').val(c.email);

        $('#clientEmailModalLabel').html('Edit Partner Email');
        $('.savepartneremail').hide();
        $('#update_partneremail').show();
    });

    $(document).delegate('.deletepartneremail','click', function(){
        var v = $(this).attr('dataid');
        $('#metatag_'+v).remove();
    });
    ////////////////////////////////////////
    ////////////////////////////////////////
    //// end add more partner email //////
    ////////////////////////////////////////
    ////////////////////////////////////////



     ////////////////////////////////////////
    ////////////////////////////////////////
    //// start add more partner phone //////
    ////////////////////////////////////////
    ////////////////////////////////////////
    var partnerphonedata = new Array();
    var itag_phone = $('.partnerphonedata .row').length;

    //Add partner phone
    $(document).delegate('.openpartnerphonenew','click', function(){
        $('#clientPhoneModalLabel').html('Add New Partner Phone Number');
        $('.savepartnerphone').show();
        $('#update_partnerphone').hide();
        $('#partnerphoneform')[0].reset();
        $('.addpartnerphone').modal('show');
        $(".telephone").intlTelInput();
    });

    $('.addpartnerphone').on('shown.bs.modal', function () {
        $(".telephone").intlTelInput();
    });

    //Save partner phone
    $(document).delegate('.savepartnerphone','click', function() {
        var partner_phone_type = $('input[name="partner_phone_type"]').val();
        $('.partner_phone_type_error').html('');
        $('input[name="partner_phone_type"]').parent().removeClass('error');

        var partner_phone = $('input[name="partner_phone"]').val();
        $('.partner_phone_error').html('');
        $('input[name="partner_phone"]').parent().removeClass('error');
        if ($('table#metatag_table').find('#metatag_'+itag_phone).length > 0) {
        }
        else {
            var flag = false;
            if(partner_phone_type == ''){
                $('.partner_phone_type_error').html('The Phone field is required.');
                $('input[name="partner_phone_type"]').parent().addClass('error');
                flag = true;
            }

            if(partner_phone == ''){
                $('.partner_phone_error').html('The Phone field is required.');
                $('input[name="partner_phone"]').parent().addClass('error');
                flag = true;
            }

            if(!flag){
                var str = $( "#partnerphoneform" ).serializeArray();
                partnerphonedata[itag_phone] = {"phone_type":str[0].value,"country_code":str[1].value ,"phone":str[2].value}
                //console.log(partnerphonedata);
                var html = '<div class="col-12 col-md-12 col-lg-12">';
                    html += '<div class="row" id="metatag_'+itag_phone+'">';

                        html += '<div class="col-3 col-md-3 col-lg-3">';
                        html += '<div class="form-group">';
                        html += '<label for="partner_phone_type">Contact Type <span style="color:#ff0000;">*</span></label>';

                        html += `<select name="partner_phone_type[]" id="partner_phone_type" class="form-control">
                            <option value="" ${str[0].value === '' ? 'selected' : ''}>Select</option>
                            <option value="Personal" ${str[0].value === 'Personal' ? 'selected' : ''}>Personal</option>
                            <option value="Secondary" ${str[0].value === 'Secondary' ? 'selected' : ''}>Secondary</option>
                            <option value="Not In Use" ${str[0].value === 'Not In Use' ? 'selected' : ''}>Not In Use</option>
                        </select>`;

                        html += '</div>';
                        html += '</div>';

                        html += '<div class="col-4 col-md-4 col-lg-4">';
                            html += '<div class="form-group">';
                                html += '<label for="partner_phone">Phone Number</label>';

                                html += '<div class="cus_field_input">';
                                    html += '<div class="country_code">';
                                        html += '<input class="telephone" id="telephone" type="tel" name="partner_country_code[]" value="'+str[1].value+'"  >';
                                    html += '</div>';
                                    html += '<input class="form-control tel_input"  autocomplete="off" placeholder="Enter Phone" name="partner_phone[]" type="text" value="'+str[2].value+'">';
                                html += '</div>';

                            html += '</div>';
                        html += '</div>';

                        //html += '<div class="col-12 col-md-1 col-lg-1">';
                        //html +=  '<a href="javascript:;" dataid="'+itag_phone+'" class="editpartnerphone"><i class="fa fa-edit"></i></a>';
                        //html +=  '<a href="javascript:;" dataid="'+itag_phone+'" class="deletepartnerphone"><i class="fa fa-times"></i></a>';
                        //html += '</div>';

                    html += '</div></div>';
                $('.partnerphonedata').append(html);
                $(".telephone").intlTelInput();
                $('#partnerphoneform')[0].reset();
                $('.addpartnerphone').modal('hide');
                itag_phone++;
            }
        }
    });


    ////////////////////////////////////////
    ////////////////////////////////////////
    //// end add more partner phone //////
    ////////////////////////////////////////
    ////////////////////////////////////////


    

});

var loadFile = function(event) {
    var output = document.getElementById('output');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
      URL.revokeObjectURL(output.src) // free memory
	  $('.if_image').hide();
    }
};
</script>
@endsection
