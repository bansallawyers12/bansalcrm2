@extends('layouts.admin')
@section('title', 'Company Profile')
@section('content')

<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="custom-error-msg"></div>
			
			{{ Form::open(array('url' => 'admin/my_profile', 'name'=>"my-profile", 'enctype'=>'multipart/form-data')) }}
			{{ Form::hidden('id', $fetchedData->id) }}
			
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Company Profile</h4>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-6 col-md-6 col-lg-6">
									<div class="form-group"> 
										<label for="test_pdf">Company Logo</label>
										<input type="hidden" id="old_profile_img" name="old_profile_img" value="{{@$fetchedData->profile_img}}" />
										<div class="profile_upload">
											<div class="upload_content">
											@if(@$fetchedData->profile_img != '')
												<img src="{{asset('public/img/profile_imgs')}}/{{@$fetchedData->profile_img}}" style="width:100px;height:100px;" id="output"/> 
											@else
												<img id="output" src="{{asset('public/images/no_image.jpg')}}"/> 
											@endif
												<i <?php if(@$fetchedData->profile_img != ''){ echo 'style="display:none;"'; } ?> class="fa fa-camera if_image"></i>
												<span <?php if(@$fetchedData->profile_img != ''){ echo 'style="display:none;"'; } ?> class="if_image">Upload Company Logo</span>
											</div>
											<input onchange="loadFile(event)" type="file" id="profile_img" name="profile_img" class="form-control" autocomplete="off" />
										</div>	
									</div> 
										<div class="form-group">
											@if(Auth::user()->role == 3)
												<label for="first_name">Organization Name <span style="color:#ff0000;">*</span></label>
											@else
												<label for="first_name">First Name <span style="color:#ff0000;">*</span></label>
											@endif	
											
												{{ Form::text('first_name', @$fetchedData->first_name, array('class' => 'form-control', 'data-valid'=>'required')) }}
										
											@if ($errors->has('first_name'))
												<span class="custom-error" role="alert">
													<strong>{{ $errors->first('first_name') }}</strong>
												</span>
											@endif
										</div>
									@if(Auth::user()->role != 3)
										<div class="form-group">
											<label for="last_name">Last Name <span style="color:#ff0000;">*</span></label>
												{{ Form::text('last_name', @$fetchedData->last_name, array('class' => 'form-control', 'data-valid'=>'required')) }}
										
											@if ($errors->has('last_name'))
												<span class="custom-error" role="alert">
													<strong>{{ $errors->first('last_name') }}</strong>
												</span>
											@endif
										</div>
									@endif
										<div class="form-group">
											<label for="email">Company Email <span style="color:#ff0000;">*</span></label>
												{{ Form::text('email', @$fetchedData->email, array('class' => 'form-control', 'data-valid'=>'required email', 'disabled'=>'disabled')) }}
										
											@if ($errors->has('email'))
												<span class="custom-error" role="alert">
													<strong>{{ $errors->first('email') }}</strong>
												</span>
											@endif
										</div>
										<div class="form-group">
											<label for="phone">Company Phone <span style="color:#ff0000;">*</span></label>
												{{ Form::text('phone', @$fetchedData->phone, array('class' => 'form-control mask', 'data-valid'=>'required', 'placeholder'=>'000-000-0000')) }}
										
											@if ($errors->has('phone'))
												<span class="custom-error" role="alert">
													<strong>{{ $errors->first('phone') }}</strong>
												</span>
											@endif
										</div>
										<div class="form-group">
											<label for="company_name">Company Name <span style="color:#ff0000;">*</span></label>
												{{ Form::text('company_name', @$fetchedData->company_name, array('class' => 'form-control mask', 'data-valid'=>'required', 'placeholder'=>'Company Name')) }}
										
											@if ($errors->has('company_name'))
												<span class="custom-error" role="alert">
													<strong>{{ $errors->first('company_name') }}</strong>
												</span>
											@endif
										</div>
									</div>
									<div class="col-6 col-md-6 col-lg-6">
										<div class="form-group">
											<label for="company_website">Company Website</label>
												{{ Form::text('company_website', @$fetchedData->company_website, array('class' => 'form-control mask', 'data-valid'=>'', 'placeholder'=>'Company Website')) }}
										
											@if ($errors->has('company_website'))
												<span class="custom-error" role="alert">
													<strong>{{ $errors->first('company_website') }}</strong>
												</span>
											@endif
										</div>
										<div class="form-group">
											<label for="company_fax">Company Fax</label>
												{{ Form::text('company_fax', @$fetchedData->company_fax, array('class' => 'form-control', 'data-valid'=>'', 'placeholder'=>'Company Fax')) }}
										
											@if ($errors->has('company_fax'))
												<span class="custom-error" role="alert">
													<strong>{{ $errors->first('company_fax') }}</strong>
												</span>
											@endif
										</div>
										<div class="form-group">
											<label for="country">Country <span style="color:#ff0000;">*</span></label>
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
													<strong>{{ $errors->first('country') }}</strong>
												</span>
											@endif
										</div>
										<!--<div class="form-group">
											<label for="state">Primary Email </label>
												{{ Form::text('primary_email', @$fetchedData->primary_email, array('class' => 'form-control', 'data-valid'=>'email')) }}
										
											@if ($errors->has('primary_email'))
												<span class="custom-error" role="alert">
													<strong>{{ $errors->first('primary_email') }}</strong>
												</span>
											@endif
										</div>	-->
										<div class="form-group">
											<label for="state">State <span style="color:#ff0000;">*</span></label>
												{{ Form::text('state', @$fetchedData->state, array('class' => 'form-control', 'data-valid'=>'required')) }}
										
											@if ($errors->has('state'))
												<span class="custom-error" role="alert">
													<strong>{{ $errors->first('state') }}</strong>
												</span>
											@endif
										</div>	
										<div class="form-group">
											<label for="city">City <span style="color:#ff0000;">*</span></label>
												{{ Form::text('city', @$fetchedData->city, array('class' => 'form-control', 'data-valid'=>'required')) }}
										
											@if ($errors->has('city'))
												<span class="custom-error" role="alert">
													<strong>{{ $errors->first('city') }}</strong>
												</span>
											@endif
										</div>	
										<div class="form-group">
											<label for="zip">Zip Code <span style="color:#ff0000;">*</span></label>
												{{ Form::text('zip', @$fetchedData->zip, array('class' => 'form-control', 'data-valid'=>'required')) }}
										
											@if ($errors->has('zip'))
												<span class="custom-error" role="alert">
													<strong>{{ $errors->first('zip') }}</strong>
												</span>
											@endif
										</div> 	
										<!--<div class="form-group">
											<label for="gst_no">GST No. <span style="color:#ff0000;">*</span></label>
												{{ Form::text('gst_no', @$fetchedData->gst_no, array('class' => 'form-control', 'data-valid'=>'required')) }}
										
											@if ($errors->has('gst_no'))
												<span class="custom-error" role="alert">
													<strong>{{ $errors->first('gst_no') }}</strong>
												</span>
											@endif
										</div>-->	
										<div class="form-group">
											<label for="address">Address <span style="color:#ff0000;">*</span></label>
												{{ Form::text('address', @$fetchedData->address, array('class' => 'form-control', 'placeholder'=>'Please write Address...', 'data-valid'=>'required')) }}
										
											@if ($errors->has('address'))
												<span class="custom-error" role="alert">
													<strong>{{ $errors->first('address') }}</strong>
												</span>
											@endif
										</div>
									</div>
								</div>																
								<div class="form-group">
									{{ Form::button('<i class="fa fa-edit"></i> Update', ['class'=>'btn btn-primary px-4', 'onClick'=>'customValidate("my-profile")']) }}
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
jQuery(document).ready(function($){
	$('#select_country').attr('data-selected-country','<?php echo @$fetchedData->country; ?>');
		$('#select_country').flagStrap();
});
</script>
<script>
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