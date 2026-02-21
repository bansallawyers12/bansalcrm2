@extends('layouts.admin')
@section('title', 'New Manage Contacts')

@section('content')

<!-- Main Content --> 
<div class="main-content">
	<section class="section">
		<div class="section-body">
			{!! Form::open(array('url' => 'managecontact/store', 'name'=>"add-contacts", 'autocomplete'=>'off', "enctype"=>"multipart/form-data"))  !!}
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Add New Contacts</h4>
								<div class="card-header-action">
									<a href="{{route('managecontact.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>	
					<div class="col-12 col-md-6 col-lg-6">
						<div class="card">
							<div class="card-body">
								<div class="form-group"> 
									<label for="name" class="col-form-label">Name <span style="color:#ff0000;">*</span></label>
									{!! Form::text('name', old('name'), array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Full Name *' ))  !!}
									@if ($errors->has('name'))
										<span class="custom-error" role="alert">
											<strong>{{ $errors->first('name') }}</strong>
										</span> 
									@endif
								</div>	
								<div class="form-group"> 
									<label for="contact_email" class="col-form-label">Email <span style="color:#ff0000;">*</span></label>
									{!! Form::text('contact_email', old('contact_email'), array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Email *' ))  !!}
									@if ($errors->has('contact_email'))
										<span class="custom-error" role="alert">
											<strong>{{ $errors->first('contact_email') }}</strong>
										</span> 
									@endif
								</div>	
								<div class="form-group"> 
									<label for="contact_phone" class="col-form-label">Phone <span style="color:#ff0000;">*</span></label>
									{!! Form::text('contact_phone', old('contact_phone'), array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Phone *' ))  !!}
									@if ($errors->has('contact_phone'))
										<span class="custom-error" role="alert">
											<strong>{{ $errors->first('contact_phone') }}</strong>
										</span> 
									@endif
								</div>	
								<div class="form-group"> 
									<label for="department" class="col-form-label">Department</label>
									{!! Form::text('department', old('department'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Department' ))  !!}
								</div>	
								<div class="form-group float-end">
									{!! Form::submit('Save', ['class'=>'btn btn-primary' ])  !!}
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
