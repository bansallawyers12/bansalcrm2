@extends('layouts.admin')
@section('title', 'Create Blog Category')

@section('content')

<!-- Main Content -->
<div class="main-content">
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
						<h3 class="card-title">Create Blog Category</h3>
					  </div> 
					  <!-- /.card-header -->
					  <!-- form start -->
					  {{ Form::open(array('url' => 'admin/blogcategories/store', 'name'=>"add-blogcategory", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
					   
					  <div class="card-body">
							<div class="form-group" style="text-align:right;">
								<a style="margin-right:5px;" href="{{route('admin.blogcategory.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>  
								{{ Form::button('<i class="fa fa-save"></i> Save Blog Category', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-blogcategory")' ]) }}
							</div>
							<div class="form-group row"> 
								<label for="name" class="col-sm-2 col-form-label">Name <span style="color:#ff0000;">*</span></label>
								<div class="col-sm-10">
								{{ Form::text('name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Name' )) }}
								@if ($errors->has('name'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('name') }}</strong>
									</span> 
								@endif
								</div>
							</div>
							<div class="form-group row"> 
								<label for="slug" class="col-sm-2 col-form-label">Slug <span style="color:#ff0000;">*</span></label>
								<div class="col-sm-10">
									{{ Form::text('slug', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Slug' )) }}
									@if ($errors->has('slug'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('slug') }}</strong>
										</span> 
									@endif
								</div>
							</div>
							<div class="form-group row"> 
								<label for="parent_id" class="col-sm-2 col-form-label">Select Parent Category</label>
								<div class="col-sm-10">
									<select class="form-control" name="parent_id" data-valid="">
										<option value="">- Select Parent Category -</option>
										@if($categories)
											@foreach($categories as $category)
												<?php $dash=''; ?>
												<option value="{{$category->id}}">{{$category->name}}</option>
												<!-- @if(count($category->subcategory))	@include('/Admin/blogcategory/subCategoryList-option',['subcategories' => $category->subcategory])
												@endif -->
											@endforeach
										@endif
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label for="status" class="col-sm-2 col-form-label">Is Active</label>
								<div class="col-sm-10">
									<input value="1" type="checkbox" name="status" data-bootstrap-switch>
								</div>
							</div> 
							<div class="form-group float-right">
								{{ Form::button('<i class="fa fa-save"></i> Save Blog Category', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-blogcategory")' ]) }}
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