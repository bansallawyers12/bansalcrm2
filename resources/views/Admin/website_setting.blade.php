@extends('layouts.admin')
@section('title', 'Website Setting')
@section('content')
<main class="main">
	<!-- Breadcrumb start-->
	<ol class="breadcrumb">
		<li class="breadcrumb-item active">
			Home / <a href="{{URL::to('/admin/dashboard')}}">Dashboard</a> / <b>Website Setting</b>
		</li>
		@include('../Elements/Admin/breadcrumb')
	</ol>
	<!-- Breadcrumb end-->
	
	<div class="container-fluid">
		<div class="animated fadeIn">
			<!-- Flash Message Start -->
				<div class="server-error">
					@include('../Elements/flash-message')
				</div>
			<!-- Flash Message End -->
			
			<div class="row">
				<div class="col-sm-6">
					<div class="card">
						<div class="card-header">
							<strong>Website Setting</strong>
						</div>
						{{ Form::open(array('url' => 'admin/website_setting', 'name'=>"website-setting", 'enctype'=>'multipart/form-data', 'autocomplete'=>'off')) }}
							{{ Form::hidden('id', @$fetchedData->id) }}
						
							<div class="card-body">
								<div class="form-group">
									<label for="phone">Website Phone<em>*</em></label>
										{{ Form::text('phone', @$fetchedData->phone, array('class' => 'form-control mask', 'data-valid'=>'required')) }}
								
									@if ($errors->has('phone'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('phone') }}</strong>
										</span>
									@endif
								</div>
								<div class="form-group">
									<label for="ofc_timing">Office Timing</label>
										{{ Form::text('ofc_timing', @$fetchedData->ofc_timing, array('class' => 'form-control')) }}
								
									@if ($errors->has('ofc_timing'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('ofc_timing') }}</strong>
										</span>
									@endif
								</div>
								<div class="form-group">
									<label for="email">Website Email<em>*</em></label>
										{{ Form::text('email', @$fetchedData->email, array('class' => 'form-control', 'data-valid'=>'required email')) }}
								
									@if ($errors->has('email'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('email') }}</strong>
										</span>
									@endif
								</div>	
								<div class="form-group">
									<label for="logo">Logo Upload</label>
										<input type="hidden" id="old_logo" name="old_logo" value="{{@$fetchedData->logo}}" />
										
										<div class="show-uploded-img float-right">	
											@if(@$fetchedData->logo == '')
												<img src="{{ asset('/public/img/logo.png') }}" class="img-avatar" />
											@else
												<img src="{{URL::to('/public/img/logo')}}/{{@$fetchedData->logo}}" class="img-avatar"/>
											@endif
										</div>
										<input type="file" name="logo" class="form-control uploadImageFile" />		
								</div>
								
								<div class="form-group">
									{{ Form::button('Save', ['class'=>'btn btn-primary px-4', 'onClick'=>'customValidate("website-setting")']) }}
								</div>
							</div>
						{{ Form::close() }}	
					</div>
				</div>
				<div class="col-sm-6">
					<div class="card">
						<div class="card-header">
							<strong>Instructions</strong>	
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label for="name"><em>*</em> Fields are mandatory.</label>
									</div>
								</div>
							</div>
						</div>
					</div>	
				</div>
			</div>
		</div>
	</div>
</main>
@endsection