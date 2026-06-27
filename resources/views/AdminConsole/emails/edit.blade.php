@extends('layouts.adminconsole')
@section('title', 'Edit Email')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			{!! Form::open(array('url' => 'adminconsole/emails/edit', 'name'=>"add-emails", 'autocomplete'=>'off', "enctype"=>"multipart/form-data"))  !!} 
			{!! Form::hidden('id', @$fetchedData->id)  !!}
				<div class="row">   
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Edit Email</h4>
								<div class="card-header-action">
									<a href="{{route('adminconsole.emails.index')}}" class="btn btn-primary">@icon('arrow-left') Back</a>
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
												@include('AdminConsole.emails.partials.email-name-domain-fields', ['storedEmail' => (string) ($fetchedData->email ?? '')])
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group"> 
														<label for="status">Status</label><br>
														<label ><input <?php if(@$fetchedData->status == 1){ echo 'checked'; } ?> type="checkbox" name="status" value="1"> Enable This Feature</label>
													</div>
												</div>
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group"> 
														<label for="display_name">Display Name</label>
														{!! Form::text('display_name', @$fetchedData->display_name, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' ))  !!}
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
														{!! Form::password('password', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'new-password', 'placeholder'=>'Leave blank to keep current password' ))  !!}
														@if ($errors->has('password'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('password') }}</strong>
															</span>
														@endif
														<small class="form-text text-muted">Not used for sending — CRM uses AWS SES. Leave blank to keep the existing value.</small>
													</div>
												</div>
                                              
                                              
												<div class="col-12 col-md-12 col-lg-12">
													<h4>Staff Sharing</h4>
													<div class="form-group"> 
														<label for="display_name">Select Staff</label>
														<select id="email_edit_users" data-valid="required" multiple class="form-control tomselect {{ $errors->has('users') ? 'is-invalid' : '' }}" name="users[]">
															<?php
															$userids = json_decode($fetchedData->user_id ?? '[]') ?? [];
																$users = \App\Models\Staff::Where('status', '=', 1)->get();
																foreach($users as $user){
																	?>
																	<option <?php if(in_array($user->id, $userids)){ echo 'selected'; } ?> value="{{$user->id}}">{{$user->first_name}} {{$user->last_name}}</option>
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
									{!! Form::button('Update', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-emails")' ])  !!}
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    waitForTomSelect().then(function () {
        initTomSelect('#email_edit_users', {
            width: '100%',
            placeholder: 'Select Staff',
            plugins: ['remove_button']
        });
    });
});
</script>
@endsection