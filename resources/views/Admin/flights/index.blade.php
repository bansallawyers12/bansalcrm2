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
								<a href="{{route('admin.flights.create')}}" class="btn btn-primary"><i class="fa fa-plus"></i> New Flights</a> 
							</div> 
							<div class="card-tools card_tools">
								<div class="input-group input-group-sm" style="width: 150px;">
									<input type="text" name="table_search" class="form-control float-right" placeholder="Search">
									<div class="input-group-append">
										<button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
									</div>
								</div>
							</div>
						</div>  
						<div class="card-body table-responsive">
							<table id="hoteltable" class="table table-bordered table-hover text-nowrap">
							  <thead>
								<tr>
								  <th>Name</th>
								  <th>Code</th>
								  <th>Logo</th>
								  <th class="no-sort">Action</th>
								</tr> 
							  </thead>
							    <tbody class="tdata">	
							  
								@foreach (@$lists as $list)	
								<tr id="id_{{@$list->id}}">  
								  <td>{{ @$list->name == "" ? config('constants.empty') : str_limit(@$list->name, '50', '...') }}</td> 
								  <td>{{ @$list->code }}</td> 
								  <td><img width="30" src="{{URL::to('/public/img/airline')}}/{{@$list->code}}.gif"></td> 
				
								  <td>
									<div class="nav-item dropdown action_dropdown">
										<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
										<div class="dropdown-menu">
											<a href="{{URL::to('/admin/flights/edit/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="fa fa-edit"></i> Edit</a>
											
										</div>
									</div> 
								  </td>
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
@endsection