@extends('layouts.admin')
@section('title', 'Edit Agent')

@section('content')
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			{{ Form::open(array('url' => 'admin/agents/edit', 'name'=>"edit-agents", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
			{{ Form::hidden('id', @$fetchedData->id) }} 
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Edit Agent</h4>
								<div class="card-header-action">
									<a href="{{route('admin.agents.active')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
			<div class="col-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="card-body">
						<div id="accordion">
							<div class="accordion">
								<div class="accordion-header" role="button" data-toggle="collapse" data-target="#agenttype" aria-expanded="true">
									<h4>Agent Type</h4>
								</div>
								<div class="accordion-body collapse show" id="agenttype" data-parent="#accordion">
									<div class="row">  
										<div class="col-12 col-md-12 col-lg-12">
											<div class="form-group">
												<div class="form-check form-check-inline">
												<?php
												$exp = explode(',', $fetchedData->agent_type);
												?>
													<input <?php if(in_array('Super Agent', $exp)){ echo 'checked'; } ?> class="form-check-input" type="checkbox" id="super_agent" value="Super Agent" name="agent_type[]">
													<label class="form-check-label" for="super_agent">Super Agent</label>
												</div>
												<div class="form-check form-check-inline">
													<input <?php if(in_array('Sub Agent', $exp)){ echo 'checked'; } ?> class="form-check-input" type="checkbox" id="sub_agent" value="Sub Agent" name="agent_type[]">
													<label class="form-check-label" for="sub_agent">Sub Agent</label>
												</div>
												@if ($errors->has('agent_type'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('agent_type') }}</strong>
													</span> 
												@endif
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="accordion">
								<div class="accordion-header" role="button" data-toggle="collapse" data-target="#agentstructure" aria-expanded="true">
									<h4>Agent Structure</h4>
								</div>
								<div class="accordion-body collapse show" id="agentstructure" data-parent="#accordion">
									<div class="row">  
										<div class="col-12 col-md-12 col-lg-12">
											<div class="form-group">
												<div class="form-check form-check-inline">
													<input <?php if($fetchedData->struture == 'Individual'){ echo 'checked'; } ?> class="form-check-input" type="radio" id="individual" value="Individual" name="struture" checked>
													<label class="form-check-label" for="individual">Individual</label>
												</div>
												<div class="form-check form-check-inline">
													<input <?php if($fetchedData->struture == 'Business'){ echo 'checked'; } ?> class="form-check-input" type="radio" id="business" value="Business" name="struture">
													<label class="form-check-label" for="business">Business</label>
												</div>
												@if ($errors->has('struture'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('struture') }}</strong>
													</span> 
												@endif
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="accordion">
								<div class="accordion-header" role="button" data-toggle="collapse" data-target="#personal_details" aria-expanded="true">
									<h4>Personal Details</h4>
								</div>
								<div class="accordion-body collapse show" id="personal_details" data-parent="#accordion">
									<div class="row"> 
										<div class="col-12 col-md-3 col-lg-3">
											<div class="form-group">
												<input type="hidden" id="old_profile_img" name="old_profile_img" value="{{@$fetchedData->profile_img}}" />
												<div class="profile_upload">
													<div class="upload_content">
														
														<img id="output"/>
															@if(@$fetchedData->profile_img != '')
																<img  src="{{URL::to('/public/img/profile_imgs')}}/{{@$fetchedData->profile_img}}" class="img-avatar"/>
															@else
																<i class="fa fa-camera"></i>
																<span>Upload Profile Image</span>
															@endif
														
													<input type="file" onchange="loadFile(event)" id="profile_img" name="profile_img" class="form-control" autocomplete="off" />
												</div>	
												
												@if ($errors->has('profile_img'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('profile_img') }}</strong>
													</span> 
												@endif
											</div>
										</div>
										</div>
										<div class="col-12 col-md-9 col-lg-9">
											<div class="row">
												<div class="col-12 col-md-6 col-lg-6 is_individual">
													<div class="form-group"> 
														<label for="full_name">Full Name <span class="span_req">*</span></label>
														{{ Form::text('full_name', @$fetchedData->full_name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Full Name' )) }}
														@if ($errors->has('full_name'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('full_name') }}</strong>
															</span> 
														@endif
													</div>
												</div>
												<div class="col-12 col-md-6 col-lg-6 is_business" >
													<div class="form-group"> 
														<label for="business_name">Business Name <span class="span_req">*</span></label>
														{{ Form::text('business_name', @$fetchedData->business_name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Business Name' )) }}
														@if ($errors->has('business_name'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('business_name') }}</strong>
															</span> 
														@endif
													</div>
												</div>
												<div class="col-12 col-md-6 col-lg-6 is_business <?php if($fetchedData->struture == 'Individual'){ echo ''; } ?>">
													<div class="form-group"> 
														<label for="c_name">Primary Contact Name <span class="span_req">*</span></label>
														{{ Form::text('c_name', @$fetchedData->full_name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Primary Contact Name' )) }}
														@if ($errors->has('c_name'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('c_name') }}</strong>
															</span> 
														@endif
													</div>
												</div>
												<div class="col-12 col-md-6 col-lg-6 is_business <?php if($fetchedData->struture == 'Individual'){ echo ''; } ?>">
													<div class="form-group"> 
														<label for="tax_number">Tax Number</label>
														{{ Form::text('tax_number', @$fetchedData->tax_number, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Tax Number' )) }}
														@if ($errors->has('tax_number'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('tax_number') }}</strong>
															</span> 
														@endif
													</div>
												</div>
												<div class="col-12 col-md-6 col-lg-6 is_business <?php if($fetchedData->struture == 'Individual'){ echo ''; } ?>">
													<div class="form-group"> 
														<label for="contract_expiry_date">Contract Expiry Date</label>
														<div class="input-group">
															<div class="input-group-prepend">
																<div class="input-group-text">
																	<i class="fas fa-calendar-alt"></i>
																</div>
															</div>
															{{ Form::text('contract_expiry_date', @$fetchedData->contract_expiry_date, array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )) }}
														</div>
														<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
														@if ($errors->has('contract_expiry_date'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('contract_expiry_date') }}</strong>
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
								<div class="accordion-header" role="button" data-toggle="collapse" data-target="#contact_details" aria-expanded="true">
									<h4>Contact Details</h4>
								</div>
								<div class="accordion-body collapse show" id="contact_details" data-parent="#accordion">
									<div class="row">
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group"> 
												<label for="email">Email <span class="span_req">*</span></label>
												{{ Form::text('email', @$fetchedData->email, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Email' )) }}
												@if ($errors->has('email'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('email') }}</strong>
													</span> 
												@endif
											</div>
										</div> 
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group"> 
												<label for="phone">Phone</label>
												<div class="cus_field_input">
													<div class="country_code"> 
														<input class="telephone" id="telephone" type="tel" name="country_code" readonly >
													</div>	
													{{ Form::text('phone', @$fetchedData->phone, array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Phone' )) }}
													@if ($errors->has('phone'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('phone') }}</strong>
														</span> 
													@endif
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="accordion">
								<div class="accordion-header" role="button" data-toggle="collapse" data-target="#address" aria-expanded="true">
									<h4>Address</h4>
								</div>
								<div class="accordion-body collapse show" id="address" data-parent="#accordion">
									<div class="row">
										<div class="col-12 col-md-4 col-lg-4">
											<div class="form-group"> 
												<label for="address">Address</label>
												{{ Form::text('address', @$fetchedData->address, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Address' )) }}
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
												{{ Form::text('city', @$fetchedData->city, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter City' )) }}
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
												{{ Form::text('state', @$fetchedData->state, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter State' )) }}
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
												{{ Form::text('zip', @$fetchedData->zip, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Zip / Post Code' )) }}
												@if ($errors->has('zip'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('zip') }}</strong>
													</span> 
												@endif
											</div>
										</div>
										<div class="col-12 col-md-4 col-lg-4">
											<div class="form-group"> 
												<label for="country">Country</label>
												
												<select class="form-control  select2" name="country" >
												<?php
													foreach(\App\Country::all() as $list){
														?>
														<option value="{{@$list->sortname}}" <?php if($fetchedData->country == @$list->sortname){ echo 'selected'; } ?>>{{@$list->name}}</option>
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
								<div class="accordion-header" role="button" data-toggle="collapse" data-target="#office_income_share" aria-expanded="true">
									<h4>Office and Income Sharing Details</h4>
								</div>
								<div class="accordion-body collapse show" id="office_income_share" data-parent="#accordion">
									<div class="row">
										<div class="col-12 col-md-4 col-lg-4">
											<div class="form-group"> 
												<label for="related_office">Related Office <span class="span_req">*</span></label>
												<select class="form-control select2" name="related_office">
													<?php
													$branches = \App\Branch::all();
													foreach($branches as $branch){
													?>
														<option <?php if($fetchedData->related_office == $branch->id){ echo 'selected'; } ?> value="{{$branch->id}}">{{$branch->office_name}}</option>
													<?php } ?>
												</select>
												@if ($errors->has('related_office'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('related_office') }}</strong>
													</span> 
												@endif
											</div>
										</div>
										<div class="col-12 col-md-4 col-lg-4 is_sub_agent">
											<div class="form-group"> 
												<label for="income_sharing">Income Sharing Percentage</label>
												{{ Form::number('income_sharing', @$fetchedData->income_sharing, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Income Sharing Percentage', 'step' => '0.01', 'min' => '0', 'max'=> '100' )) }}
												@if ($errors->has('income_sharing'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('income_sharing') }}</strong>
													</span> 
												@endif
												<span class="span_note">This will be proportion of the income that is shared with your sub-agents when creating any invoice related to the referred application</span>
											</div>
										</div>
										<div class="col-12 col-md-4 col-lg-4 is_super_agent">
											<div class="form-group"> 
												<label for="claim_revenue">Claim Revenue Percentage</label>
												{{ Form::number('claim_revenue', @$fetchedData->claim_revenue, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Claim Revenue Percentage', 'step' => '0.01', 'min' => '0', 'max'=> '100' )) }}
												@if ($errors->has('claim_revenue'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('claim_revenue') }}</strong>
													</span> 
												@endif
												<span class="span_note">This is the proportion of commission that you will be receiving from your super agent</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="form-group float-right">
							{{ Form::submit('Update Agent', ['class'=>'btn btn-primary' ]) }}
						</div>
					</div>
				</div>
			</div>	
		</div>	
		{{ Form::close() }}
		</div>
	</section>
</div>
@endsection
@section('scripts')
<script>
  var loadFile = function(event) {
    var output = document.getElementById('output');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
      URL.revokeObjectURL(output.src) // free memory
    }
  };
  $(document).ready(function () {
	var id = $('#agentstructure input[name="struture"]:checked').val();
	
	if(id == 'Individual'){
		$('#personal_details .is_business').hide();
		$('#personal_details .is_individual').show();
		$('#personal_details .is_business input').attr('data-valid', '');
		$('#personal_details .is_individual input').attr('data-valid', 'required');
	} 
	else{
		$('#personal_details .is_individual').hide();
		$('#personal_details .is_business').show(); 
		$('#personal_details .is_business input').attr('data-valid', 'required');
		$('#personal_details .is_individual input').attr('data-valid', '');
	}
  });
</script>
@endsection