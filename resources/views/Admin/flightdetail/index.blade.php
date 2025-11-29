@extends('layouts.admin')
@section('title', 'Flights')

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">Flights</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Flights</li>
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
					<div class="card"> 
						<div class="card-header">   
							<div class="card-title">
								<a href="{{route('admin.flightdetail.create')}}" class="btn btn-primary"><i class="fa fa-plus"></i> New Flights Detail</a> 
							</div> 
							<div class="card-tools card_tools">
								<div class="row">
									<div class="col-md-4">
										<a href="javascript:;" data-toggle="modal" data-target="#flight_detail_modal" class="btn btn-primary"><i class="fas fa-search"></i></a>
									</div>
								</div>
							</div>
						</div>   
						<div class="card-body table-responsive">
							<table id="flight_details" class="table-responsive table table-bordered table-hover text-nowrap">
							  <thead>
								<tr>
								  <th class="no-sort">Action</th>
								  <th>Flight Name</th>
								  <th>Flight Type</th>
								  <th>Agent</th>
								  <th>Flight Code</th>
								  <th>Departure Time</th>
								  <th>Arrival Time</th>
								  <th>Source</th>
								  <th>Destination</th>
								  <th>B2C Total</th>
								  <th>B2B Total</th>
								</tr> 
							  </thead>
							    <tbody class="tdata">	
							  <?php
							// echo '<pre>';  print_r($lists);
							  ?>
								@foreach (@$lists as $list)	
								<tr id="id_{{@$list->id}}">  
								  <td>
									<div class="nav-item dropdown action_dropdown">
										<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
										<div class="dropdown-menu">
											<a href="{{URL::to('/admin/flight-detail/edit/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="fa fa-edit"></i> Edit</a>
											<a href="{{URL::to('/admin/flight-detail/clone/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="fa fa-copy"></i> Clone</a>
											
										</div>
									</div> 
								  </td>
								  <td>{{ @$list->flight->name == "" ? config('constants.empty') : str_limit(@$list->flight->name, '50', '...') }}</td> 
								   <td>@if(@$list->flighttype == 1)Oneway Trip @else Round Trip @endif</td> <td>{{ @$list->agentdetail->company_name }}</td>
								  <td>{{ @$list->flight->code }}</td> 
								  
								  <td>{{ @$list->dep_time }}</td>  
								  <td>{{ @$list->arival_time }}</td> 
								  <td>{{ @$list->flightsource->airport_code }} ({{ @$list->flightsource->city_name }})</td> 
								  <td>{{ @$list->flightdest->airport_code }} ({{ @$list->flightdest->city_name }})</td> 
								  <td>{{ @$list->bc_total }}</td> 
								  <td>{{ @$list->bb_total }}</td>   
								</tr>	
							  @endforeach	
								
							  </tbody> 
							 
							</table>
							<div class="card-footer hide">
							{{-- {!! $lists->appends(\Request::except('page'))->render() !!} --}}
							 </div>
						  </div>
					</div>	
				</div>	
			</div>
		</div>
	</section>
</div>

<div class="modal fade" id="flight_detail_modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				  <h4 class="modal-title">Flight Detail</h4>
				  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
			</div>
			<form action="{{route('admin.flightdetail.index')}}" method="get">
				<div class="modal-body"> 
					<div class="row">
					
					<div class="col-md-6">
						<div class="form-group row">
							<label for="flight_name" class="col-sm-3 col-form-label">Flight Name</label>
							<div class="col-sm-9">
								{{ Form::text('flight_name', Request::get('flight_name'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Flight Name', 'id' => '' )) }}
							</div>
						</div>
					</div>
					<div class="col-md-6">
							<div class="form-group row">
								<label for="flight_type" class="col-sm-3 col-form-label">Flight Type</label>
								<div class="col-sm-9">
									<select name="type" class="form-control">
										<option value="1" @if(Request::get('flight_name') == '1') selected @endif>Oneway Trip</option>
										<option value="2" @if(Request::get('flight_name') == '2') selected @endif>Round Trip</option>
									</select>
								</div>
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group row">
								<label for="flight_code" class="col-sm-3 col-form-label">Flight Code</label>
								<div class="col-sm-9">
									{{ Form::text('flight_code', Request::get('flight_code'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Flight Code', 'id' => '' )) }}
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group row">
								<label for="depature_time" class="col-sm-3 col-form-label">Departure Time</label>
								<div class="col-sm-9">
									{{ Form::text('depature_time', Request::get('depature_time'), array('class' => 'form-control commodate', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Departure Time', 'id' => '' )) }}
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group row">
								<label for="arrival_time" class="col-sm-3 col-form-label">Arrival Time</label>
								<div class="col-sm-9">
									{{ Form::text('arrival_time', Request::get('arrival_time'), array('class' => 'form-control commodate', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Arrival Time', 'id' => '' )) }}
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer justify-content-between">
				  <a href="{{route('admin.flightdetail.index')}}" class="btn btn-default" >Reset</a>
				  <button type="submit" id="" class="btn btn-primary">Search</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection