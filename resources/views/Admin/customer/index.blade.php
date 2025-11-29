@extends('layouts.admin')
@section('title', 'Registered Users')

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">Registered Users</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Registered Users</li>
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
							<div class="float-left">
								<form enctype="multipart/form-data" action="{{URL::to('admin/customer/upload')}}" method="post">
								@csrf
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<input required type="file" class="form-control" name="file">
											</div>
										</div>
									  <div class="col-md-3">
										  <div class="form-group">
											<input type="submit" class="btn btn-primary" name="submit">
										  </div>
									  </div>
									  
									  <div class="col-md-3">
										  <div class="form-group">
											<a href="{{URL::to('/public/usersample.csv')}}" download><i class="fa fa-download"></i> Sample</a>
										  </div>
									  </div>
									 </div>
								</form>
							</div>
							<div class="float-right">
								<a href="{{route('admin.customer.create')}}" class="btn btn-primary">Create User</a>
							</div>
						</div>
						<div class="card-body table-responsive p-0">
							<table class="table table-hover text-nowrap">
							  <thead>
								<tr>
								  <th>Name</th>
								  <th>Email</th>
								  <th>Phone</th>
								  <th>City</th>
								  <th>DOB</th>
								  <th>Action</th>
								</tr> 
							  </thead>
							  <tbody class="tdata">	
								@if(@$totalData !== 0)
								@foreach (@$lists as $list)	
								<tr id="id_{{@$list->id}}"> 
								  <td>{{ @$list->name == "" ? config('constants.empty') : str_limit(@$list->name, '50', '...') }}</td> 
								
								  <td>{{ @$list->email == "" ? config('constants.empty') : str_limit(@$list->email, '50', '...') }}</td>  
								  <td>{{ @$list->phone }}</td>  
								  <td>{{ @$list->city }}</td>  
								  <td>{{ @$list->dob }}</td>  
								 
								  <td><a href="{{URL::to('/admin/customer/edit/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="fa fa-edit"></i> Edit</a> / <a href="javascript:;" onClick="deleteAction({{@$list->id}}, 'users')"><i class="fa fa-trash"></i> Delete</a>
								  </td>
								</tr>	
								@endforeach						
							  </tbody>
							  @else
							  <tbody>
									<tr>
										<td colspan="2">
											
										</td>
									</tr>
								</tbody>
							@endif
							</table>
							<div class="card-footer">
							 {!! $lists->appends(\Request::except('page'))->render() !!}
							 </div>
						  </div>
					</div>	
				</div>	
			</div>
		</div>
	</section>
</div>
@endsection