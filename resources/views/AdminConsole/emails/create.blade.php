@extends('layouts.adminconsole')
@section('title', 'Add Email')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			{!! Form::open(array('url' => 'adminconsole/emails/store', 'name'=>"add-emails", 'autocomplete'=>'off', "enctype"=>"multipart/form-data"))  !!} 
				<div class="row">   
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Add Email</h4>
								<div class="card-header-action">
									<a href="{{route('adminconsole.emails.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
				<div class="col-12">
						<div class="card">
							<div class="card-body">
								<div id="accordion"> 
									<div class="accordion">
										<div class="accordion-header" role="button" data-bs-toggle="collapse" data-bs-target="#primary_info" aria-expanded="true">
											<h4>Primary Information</h4>
										</div>
										<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
											<div class="row"> 						
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group"> 
														<label for="email">Email address <span class="span_req">*</span></label>
														{!! Form::text('email', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'e.g. apply@bansaleducation.com.au or info@educationelite.com.au' ))  !!}
														<small class="form-text text-muted">@bansaleducation.com.au and @educationelite.com.au addresses appear in all compose From dropdowns once active. Domains must be verified in AWS SES.</small>
														@if ($errors->has('email'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('email') }}</strong>
															</span> 
														@endif
													</div>
												</div>
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group"> 
														<label for="status">Status</label><br>
														<label ><input type="checkbox" name="status" value="1"> Enable This Feature</label>
													</div>
												</div>
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group"> 
														<label for="display_name">Display Name</label>
														{!! Form::text('display_name', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
														@if ($errors->has('display_name'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('display_name') }}</strong>
															</span> 
														@endif
													</div>
												</div>
                                              
                                                <div class="col-12 col-md-12 col-lg-12">
													<div class="form-group">
														<label for="password">Password</label>
														{!! Form::password('password', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'new-password', 'placeholder'=>'Optional (SES uses AWS credentials from .env)' ))  !!}
														@if ($errors->has('password'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('password') }}</strong>
															</span>
														@endif
														<small class="form-text text-muted">Not used for sending — CRM uses AWS SES. Leave blank or enter any placeholder (e.g. ses).</small>
													</div>
												</div>
                                              
												<div class="col-12 col-md-12 col-lg-12">
													<h4>Staff Sharing</h4>
													<div class="form-group"> 
														<label for="display_name">Select Staff</label>
														<select data-valid="required" multiple class="form-control select2 {{ $errors->has('users') ? 'is-invalid' : '' }}" name="users[]">
															<option value="">Select Staff</option>
															<?php
																$users = \App\Models\Staff::Where('status', '=', 1)->get();
																foreach($users as $user){
																	?>
																	<option value="{{$user->id}}">{{$user->first_name}} {{$user->last_name}}</option>
																	<?php
																}
															?>
														</select>
														@if ($errors->has('users'))
															<span class="custom-error" role="alert">
																<strong>{{ $errors->first('users') }}</strong>
															</span>
														@endif
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group float-end">
									{!! Form::button('Save', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-emails")' ])  !!}
								</div> 
							</div>
						</div>	
					</div>
				</div>
			 {!! Form::close()  !!}	
		</div>
	</section>
</div>

@endsection