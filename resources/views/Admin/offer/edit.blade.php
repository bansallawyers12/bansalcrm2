@extends('layouts.adminnew')
@section('title', 'Edit Offer')
 
@section('content')
 
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">Edit Offer</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Edit Offer</li>
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
						<h3 class="card-title">Edit Offer</h3>
					  </div> 
					  <!-- /.card-header -->
					  <!-- form start -->
					  {{ Form::open(array('url' => 'admin/offer/edit', 'name'=>"add-flights", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
					     {{ Form::hidden('id', @$fetchedData->id) }}
						<div class="card-body">
							<div class="form-group" style="text-align:right;">
								<a style="margin-right:5px;" href="{{route('admin.offer.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>  
								{{ Form::button('<i class="fa fa-save"></i> Edit Offer', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-flights")' ]) }}
							</div>
							<div class="form-group row">
								<label for="name" class="col-sm-2 col-form-label">Offer Type</label>
								<div class="col-sm-10">
									<select class="form-control" name="type">
										<option value="flights" @if(@$fetchedData->type == 'flights') selected @endif>Flights</option>
										<option value="hotels" @if(@$fetchedData->type == 'hotels') selected @endif>Hotels</option>
									</select>
								</div>
							</div>
							<div class="form-group row"> 
								<label for="name" class="col-sm-2 col-form-label">Name</label>
								<div class="col-sm-10">
								{{ Form::text('name', @$fetchedData->name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Name' )) }}
								@if ($errors->has('name'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('name') }}</strong>
									</span> 
								@endif
								</div>  
							</div>
							<div class="form-group row"> 
								<label for="subtitle" class="col-sm-2 col-form-label">Subtitle</label>
								<div class="col-sm-10">
								
								{{ Form::text('subtitle', @$fetchedData->subtitle, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Subtitle', 'id' =>'' )) }}
								
								@if ($errors->has('subtitle'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('subtitle') }}</strong>
									</span> 
								@endif
								</div>  
							</div>
							<div class="form-group row"> 
								<label for="subtitle" class="col-sm-2 col-form-label">Image</label>
								<div class="col-sm-10">
								<input type="hidden" id="old_image" name="old_image" value="{{@$fetchedData->image}}" />
								<input type="file" name="image" class="form-control">
								@if(@$fetchedData->image != '')
									<img width="70" src="{{URL::to('/public/img/gallery_img')}}/{{@$fetchedData->image}}" class="img-avatar"/>
								@endif
								@if ($errors->has('image'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('image') }}</strong>
									</span> 
								@endif
								</div>  
							</div>
							<div class="form-group row"> 
								<label for="arival_time" class="col-sm-2 col-form-label">Expire Date</label>
								<div class="col-sm-10">
								{{ Form::text('expire_date', @$fetchedData->expiry_date, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Expire Date', 'id'=>'ardate' )) }}
								@if ($errors->has('expire_date'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('expire_date') }}</strong>
									</span> 
								@endif
								</div>  
							</div>
							
							<div class="form-group row"> 
								<label for="description" class="col-sm-2 col-form-label">Description</label>
								<div class="col-sm-10">
								<textarea name="description" data-valid ="required" class="textarea" placeholder="Please Add Description Here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{@$fetchedData->description}}</textarea>
								@if ($errors->has('description'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('description') }}</strong>
									</span> 
								@endif
								</div>  
							</div>
							<div class="form-group float-right">
								{{ Form::button('<i class="fa fa-save"></i> Save Offer', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-flights")' ]) }}
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