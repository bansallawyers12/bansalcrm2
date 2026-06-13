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
												@include('AdminConsole.emails.partials.email-name-domain-fields', ['storedEmail' => ''])
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
														<label for="password">Password <span class="span_req">*</span></label>
														{!! Form::password('password', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'new-password', 'placeholder'=>'Enter password (e.g. ses)' ))  !!}
														@if ($errors->has('password'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('password') }}</strong>
															</span>
														@endif
														<small class="form-text text-muted">Required on create. Not used for sending — CRM uses AWS SES. You may use a placeholder value (e.g. ses).</small>
													</div>
												</div>
                                              
												<div class="col-12 col-md-12 col-lg-12">
													<h4>Staff Sharing</h4>
													<div class="form-group"> 
														<label for="users">Select Staff <span class="span_req">*</span></label>
														<select data-valid="required" multiple class="form-control select2 {{ $errors->has('users') ? 'is-invalid' : '' }}" name="users[]">
															<option value="">Select Staff</option>
															<?php
																$roleIds = config('crm_access.exempt_role_ids', [1, 12]);
																$superAdminRoleId = (int) ($roleIds[0] ?? 1);
																$adminRoleId = (int) ($roleIds[1] ?? 12);
																$selectedUsers = old('users', []);
																$users = \App\Models\Staff::Where('status', '=', 1)->get();
																foreach($users as $user){
																	$roleLabel = '';
																	if ((int) $user->role === $superAdminRoleId) {
																		$roleLabel = ' — Super Admin';
																	} elseif ((int) $user->role === $adminRoleId) {
																		$roleLabel = ' — Admin';
																	}
																	$selected = is_array($selectedUsers) && in_array((string) $user->id, array_map('strval', $selectedUsers), true);
																	?>
																	<option value="{{$user->id}}"{{ $selected ? ' selected' : '' }}>{{$user->first_name}} {{$user->last_name}}{{ $roleLabel }}</option>
																	<?php
																}
															?>
														</select>
														<small class="form-text text-muted">Required on create: select at least <strong>2 Super Admin</strong> and <strong>2 Admin</strong> staff.</small>
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