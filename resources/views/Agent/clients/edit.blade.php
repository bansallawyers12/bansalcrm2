@extends('layouts.agent')
@section('title', 'Edit Client')

@section('content')
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			{{ Form::open(array('url' => 'agent/clients/edit', 'name'=>"edit-clients", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
			{{ Form::hidden('id', @$fetchedData->id) }} 
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Edit Clients</h4>
								<div class="card-header-action">
									<a href="{{route('agent.clients.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-body">
								<div id="accordion">
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
												</div>
												<div class="col-12 col-md-9 col-lg-9">
													<div class="row">
														<div class="col-12 col-md-6 col-lg-6">
															<div class="form-group"> 
																<label for="first_name">First Name <span class="span_req">*</span></label>
																{{ Form::text('first_name', @$fetchedData->first_name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter First Name' )) }}
																@if ($errors->has('first_name'))
																	<span class="custom-error" role="alert">
																		<strong>{{ @$errors->first('first_name') }}</strong>
																	</span> 
																@endif
															</div>
														</div>
														<div class="col-12 col-md-6 col-lg-6">
															<div class="form-group"> 
																<label for="last_name">Last Name <span class="span_req">*</span></label>
																{{ Form::text('last_name', @$fetchedData->last_name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Last Name' )) }}
																@if ($errors->has('last_name'))
																	<span class="custom-error" role="alert">
																		<strong>{{ @$errors->first('last_name') }}</strong>
																	</span> 
																@endif
															</div>
														</div>
														<div class="col-12 col-md-6 col-lg-6">
															<div class="form-group"> 
																<label for="dob">D.O.B</label>
																<div class="input-group">
																	<div class="input-group-prepend">
																		<div class="input-group-text">
																			<i class="fas fa-calendar-alt"></i>
																		</div>
																	</div>
																	{{ Form::text('dob', @$fetchedData->dob, array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )) }}
																	@if ($errors->has('dob'))
																		<span class="custom-error" role="alert">
																			<strong>{{ @$errors->first('dob') }}</strong>
																		</span> 
																	@endif
																</div>
															</div>
														</div>
														<div class="col-12 col-md-6 col-lg-6">
															<div class="form-group"> 
																<label for="client_id">Client ID</label>
																{{ Form::text('client_id', @$fetchedData->client_id, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Client ID' )) }}
																@if ($errors->has('client_id'))
																	<span class="custom-error" role="alert">
																		<strong>{{ @$errors->first('client_id') }}</strong>
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
										<div class="accordion-header" aria-expanded="true" role="button" data-toggle="collapse" data-target="#contact_details">
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
															{{ Form::text('phone', @$fetchedData->phone, array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Phone' )) }}									@if ($errors->has('phone'))
																<span class="custom-error" role="alert">
																	<strong>{{ @$errors->first('phone') }}</strong>
																</span> 
															@endif
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-12 col-md-6 col-lg-6">
													<div class="form-group"> 
														<label for="att_email">Email </label>
														{{ Form::text('att_email', @$fetchedData->att_email, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter email' )) }}
														@if ($errors->has('att_email'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('att_email') }}</strong>
															</span> 
														@endif
													</div>
												</div> 
												<div class="col-12 col-md-6 col-lg-6">
													<div class="form-group"> 
														<label for="att_phone">Phone</label>
														<div class="cus_field_input">
															<div class="country_code"> 
																<input class="telephone" id="telephone" type="tel" name="att_country_code" value="{{@$fetchedData->att_country_code}}" readonly >
															</div>	
															{{ Form::text('att_phone', @$fetchedData->att_phone, array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Phone' )) }}
															@if ($errors->has('att_phone'))
																<span class="custom-error" role="alert">
																	<strong>{{ @$errors->first('att_') }}</strong>
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
										<div class="accordion-header" role="button" aria-expanded="true" data-toggle="collapse" data-target="#current_visa_info">
											<h4>Current Visa Information</h4>
										</div>
										<div class="accordion-body collapse show" id="current_visa_info" data-parent="#accordion">
											<div class="row">
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="preferredIntake">Preferred Intake</label>
														<div class="input-group">
															<div class="input-group-prepend">
																<div class="input-group-text">
																	<i class="fas fa-calendar-alt"></i>
																</div>
															</div>
															{{ Form::text('preferredIntake', @$fetchedData->preferredIntake, array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )) }}
															@if ($errors->has('preferredIntake'))
																<span class="custom-error" role="alert">
																	<strong>{{ @$errors->first('preferredIntake') }}</strong>
																</span> 
															@endif
														</div>
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="country_passport">Country of Passport</label>
														<select class="form-control  select2" name="country_passport" >
														<?php
															foreach(\App\Country::all() as $list){
																?>
																<option value="{{@$list->sortname}}" <?php if($fetchedData->country_passport == @$list->sortname){ echo 'selected'; } ?>>{{@$list->name}}</option>
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
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="passport_number">Passport Number</label>
														{{ Form::text('passport_number', @$fetchedData->passport_number, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Passport Number' )) }}
														@if ($errors->has('passport_number'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('passport_number') }}</strong>
															</span> 
														@endif
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="visa_type">Visa Type</label>
														<select class="form-control select2" name="visa_type">
														<option value=""></option>
														@foreach(\App\VisaType::all() as $visalist)
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
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="visaExpiry">Visa Expiry Date</label>
														<div class="input-group">
															<div class="input-group-prepend">
																<div class="input-group-text">
																	<i class="fas fa-calendar-alt"></i>
																</div>
															</div>
															{{ Form::text('visaExpiry', @$fetchedData->visaExpiry, array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )) }}
															@if ($errors->has('visaExpiry'))
																<span class="custom-error" role="alert">
																	<strong>{{ @$errors->first('visaExpiry') }}</strong>
																</span> 
															@endif
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="accordion">
										<div class="accordion-header" role="button" aria-expanded="true" data-toggle="collapse" data-target="#internal">
											<h4>Internal</h4>
										</div>
										<div class="accordion-body collapse show" id="internal" data-parent="#accordion">
											<div class="row">
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="assignee">Assignee</label>
														<select class="form-control select2" name="assignee">
															<option value="">-- Assignee --	</option>
															<?php
															$admins = \App\Admin::where('role','!=',7)->get();
															foreach($admins as $admin){
															?>
															<option <?php if($fetchedData->assignee == $admin->id){  echo "selected"; } ?> value="<?php echo $admin->id; ?>"><?php echo $admin->first_name.' '.$admin->last_name; ?></option>
															<?php } ?>
														</select>
														@if ($errors->has('assignee'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('assignee') }}</strong>
															</span> 
														@endif
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="followers">Followers</label>
														<?php
														$explode = explode(',', $fetchedData->followers);
														?>
														<select class="form-control select2" multiple name="followers[]">
															<option value="">-- Followers --</option>
															<?php
															$admins = \App\Admin::where('role','!=',7)->get();
															foreach($admins as $admin){
															?>
															<option <?php if(in_array($admin->id, $explode)){  echo "selected"; } ?> value="<?php echo $admin->id; ?>"><?php echo $admin->first_name.' '.$admin->last_name; ?></option>
															<?php } ?>
														</select>
														@if ($errors->has('followers'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('followers') }}</strong>
															</span> 
														@endif
													</div>
												</div>
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="source">Choose a source</label>
														<select class="form-control select2" name="source">
															<option>-- Choose a source --</option>
															
															@foreach(\App\Source::all() as $sourcelist)
																<option @if($fetchedData->source == $sourcelist->id) selected @endif value="{{$sourcelist->id}}">{{$sourcelist->name}}</option>
															@endforeach
														</select>
														@if ($errors->has('source'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('source') }}</strong>
															</span> 
														@endif
													</div>
												</div>
												
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="tagname">Tag Name</label>
														<select multiple class="form-control select2" name="tagname[]">
															<option value="">-- Search & Select tag --</option>
														<?php
														$explodee = array();
														if($fetchedData->tagname != ''){
															$explodee = explode(',', $fetchedData->tagname);
														} 
														foreach(\App\Tag::all() as $tags){
															?>
															<option <?php if(in_array($tags->id, $explodee)){ echo 'selected'; } ?> value="{{$tags->id}}">{{$tags->name}}</option>
															<?php
														}
														?>	
														</select>
														@if ($errors->has('tagname'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('tagname') }}</strong>
															</span> 
														@endif
													</div>
												</div> 
											</div> 
										</div>
									</div>
								</div>
								
								<div class="form-group float-right">
									{{ Form::button('Update Clients', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("edit-clients")']) }}
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
<?php
if($fetchedData->related_files != ''){
    $exploder = explode(',', $fetchedData->related_files);
       foreach($exploder AS $EXP){ 
			$relatedclients = \App\Admin::where('id', $EXP)->first();	
			?>
			<input type="hidden" class="relatedfile" data-id="{{$relatedclients->id}}" data-email="{{$relatedclients->email}}" data-name="{{$relatedclients->first_name}} {{$relatedclients->last_name}}">
			<?php
								
}
}
?>
<script>
jQuery(document).ready(function($){
    <?php if($fetchedData->related_files != ''){ ?>
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