@extends('layouts.admin')
@section('title', 'Api Key')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">Api Key</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Api Key</li>
					</ol>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.container-fluid -->
	</div>
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
				<div class="col-md-6">
					<div class="card card-primary">
						<div class="card-header">
						<h3 class="card-title">Api Key</h3>
					  </div>
					  @if(@Auth::user()->client_id == '')
					  {{ Form::open(array('url' => 'admin/api-key', 'name'=>"add-key", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
						<div class="card-body">	
						  <div class="form-group">
							{{ Form::submit('Genreate Key', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-key")' ]) }}
						  </div>
						   
						</div>
					  {{ Form::close() }}
					  @else
						  <div class="card-body">	
						 
						   <div class="form-group">
							<b>{{ @Auth::user()->client_id }}</b>
						  </div>
						</div>
					  @endif
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection