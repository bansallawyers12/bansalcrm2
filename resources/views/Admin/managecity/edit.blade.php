@extends('layouts.admin')
@section('title', 'Edit Manage Departure/X-City')

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">Manage Departure/X-City</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Manage Departure/X-City</li>
					</ol>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.container-fluid -->
	</div>
	<!-- /.content-header -->	
	<!-- Breadcrumb start-->
	<!--<ol class="breadcrumb">
		<li class="breadcrumb-item active">
			Home / <b>Dashboard</b>
		</li>
		@include('../Elements/Admin/breadcrumb')
	</ol>-->
	<!-- Breadcrumb end-->
	
	<!-- Main content --> 
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<!-- Flash Message Start -->
					<div class="server-error">
						@include('../Elements/flash-message')
					</div>
					<!-- Flash Message End -->
				</div> 
				<div class="col-md-12">
					<div class="card card-primary">
					  <div class="card-header">
						<h3 class="card-title">Edit Manage Departure/X-City</h3>
					  </div> 
					  <!-- /.card-header -->
					  <!-- form start -->
					  {{ Form::open(array('url' => 'admin/cities/edit', 'name'=>"edit-city", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
					  {{ Form::hidden('id', @$fetchedData->id) }}
						<div class="card-body">
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group" style="text-align:right;">
										<a style="margin-right:5px;" href="{{route('admin.cities.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
										{{ Form::button('<i class="fa fa-edit"></i> Update Departure/X-City', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("edit-city")' ]) }}
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group"> 
										<label for="name" class="col-form-label">Departure/X-City <span style="color:#ff0000;">*</span></label>
										{{ Form::text('name', @$fetchedData->name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter City Name' )) }}
										@if ($errors->has('name'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('name') }}</strong>
											</span> 
										@endif 
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group float-right">
										{{ Form::button('<i class="fa fa-edit"></i> Update Departure/X-City', ['class'=>'btn 	btn-primary', 'onClick'=>'customValidate("edit-city")' ]) }}
									</div> 
								</div> 
							</div> 
						</div> 
					  {{ Form::close() }}
					</div>	
				</div>	
			</div>
		</div>
	</section>
</div>
@endsection