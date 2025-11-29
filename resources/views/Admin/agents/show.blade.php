@extends('layouts.admin')
@section('title', 'Agent')

@section('content')
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">Agents</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Agent</li>
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
				<div class="col-md-12">
					<div class="card card-primary">
						 <div class="card-header">
							<h3 class="card-title">Personal Information</h3>
						</div>
						<div class="card-body">	
							<div class="row"> 
								<div class="col-sm-6"> 
									<div class="form-group"> 
										<b for="first_name" class="col-sm-3">Wallet Amount</b>
										<h5 class="col-sm-6">{{@$fetchedData->wallet}}</h5>
									</div>
								</div>
								<div class="col-sm-6"> 
									<div class="form-group"> 
										<b for="first_name" class="col-sm-3">Name</b>
										<h5 class="col-sm-6">{{@$fetchedData->name}}</h5>
									</div>
								</div>
								
								<div class="col-sm-6"> 
									<div class="form-group"> 
										
										<b for="first_name">Email</b>
										<h5>{{@$fetchedData->email}}</h5>
									</div>
								</div>
								
								<div class="col-sm-6"> 
									<div class="form-group"> 
										<b for="first_name">Phone</b>
										<h5>{{@$fetchedData->phone}}</h5>
									</div>
								</div>
								<div class="col-sm-6"> 
									<div class="form-group"> 
										<b for="first_name">Address</b>
										<h5>{{@$fetchedData->address}}</h5>
									</div>
								</div>
								<div class="col-sm-6"> 
									<div class="form-group"> 
										<b for="first_name">City</b>
										<h5>{{@$fetchedData->city}}</h5>
									</div>
								</div>
								<div class="col-sm-6"> 
									<div class="form-group"> 
										<b for="first_name">State</b>
										<h5>{{@$fetchedData->state}}</h5>
									</div>
								</div>
								<div class="col-sm-6"> 
									<div class="form-group"> 
										<b for="first_name">Country</b>
										<h5>{{@$fetchedData->country}}</h5>
									</div>
								</div>
								<div class="col-sm-6"> 
									<div class="form-group"> 
										<b for="first_name">Zip</b>
										<h5>{{@$fetchedData->zip}}</h5>
									</div>
								</div>
								<div class="col-sm-6"> 
									<div class="form-group"> 
										<b for="first_name">Gender</b>
										<h5>@if(@$fetchedData->gender == 1) Male @elseif(@$fetchedData->gender == 2) Female @else Transgender @endif</h5>
									</div>
								</div>
								<div class="col-sm-6"> 
									<div class="form-group"> 
										<b for="first_name">Marital Status</b>
										<h5>{{@$fetchedData->marital_status}}</h5>
									</div>
								</div>
								<div class="col-sm-6"> 
									<div class="form-group"> 
										<b for="first_name">Date of Birth</b>
										<h5>{{date('d/m/Y', strtotime(@$fetchedData->dob))}}</h5>
									</div>
								</div>
								
							</div>
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</section>
</div>
@endsection