@extends('layouts.admin')
@section('title', 'Edit Manage Contacts')

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">Manage Contacts</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Manage Contacts</li>
					</ol>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.container-fluid -->
	</div>
	<!-- /.content-header -->	

	<!-- Main content --> 
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="server-error">
						@include('../Elements/flash-message')
					</div>
				</div>
			</div>
			{!! Form::open(array('url' => 'contact/edit', 'name'=>"edit-contacts", 'autocomplete'=>'off', "enctype"=>"multipart/form-data"))  !!}
			{!! Form::hidden('id', @$fetchedData->id)  !!}
				<div class="row">
					<div class="col-md-12">
						<div class="card card-primary">
							<div class="card-header">
								<h3 class="card-title">Edit Contacts</h3>
							</div>
							<div class="card-body">
								<div class="form-group" style="text-align:right;">
									<a style="margin-right:5px;" href="{{route('managecontact.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a> 
									{!! Form::button('<i class="fa fa-edit"></i> Update Contact', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("edit-contacts")' ])  !!}
								</div> 	 
								<div class="form-group row"> 
									<label for="name" class="col-sm-2 col-form-label">Name <span style="color:#ff0000;">*</span></label>
									<div class="col-sm-10">
									{!! Form::text('name', @$fetchedData->name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Full Name *' ))  !!}
									@if ($errors->has('name'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('name') }}</strong>
										</span> 
									@endif
									</div>
								</div>
								<div class="form-group row"> 
									<label for="contact_email" class="col-sm-2 col-form-label">Email <span style="color:#ff0000;">*</span></label>
									<div class="col-sm-10">
									{!! Form::text('contact_email', @$fetchedData->contact_email, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Email *' ))  !!}
									@if ($errors->has('contact_email'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('contact_email') }}</strong>
										</span> 
									@endif
									</div>
								</div>
								<div class="form-group row"> 
									<label for="contact_phone" class="col-sm-2 col-form-label">Phone <span style="color:#ff0000;">*</span></label>
									<div class="col-sm-10">
									{!! Form::text('contact_phone', @$fetchedData->contact_phone, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Phone *' ))  !!}
									@if ($errors->has('contact_phone'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('contact_phone') }}</strong>
										</span> 
									@endif
									</div>
								</div>
								<div class="form-group row">
									<label for="department" class="col-sm-2 col-form-label">Department</label>
									<div class="col-sm-10">
									{!! Form::text('department', @$fetchedData->department, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Department' ))  !!}
									@if ($errors->has('department'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('department') }}</strong>
										</span> 
									@endif
									</div> 
								</div> 
								<div class="form-group">
									{!! Form::button('<i class="fa fa-edit"></i> Update Contact', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("edit-contacts")' ])  !!}
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
