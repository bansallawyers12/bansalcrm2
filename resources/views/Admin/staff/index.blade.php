@extends('layouts.admin')
@section('title', 'Staffs')

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">Staffs</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Staffs</li>
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
							<div class="float-right">
								<a href="{{route('admin.staff.create')}}" class="btn btn-primary">Create Staff</a>
							</div>
						</div>
						<div class="card-body table-responsive p-0">
							<table class="table table-hover text-nowrap">
							  <thead>
								<tr>
								  <th>First Name</th>
								  <th>Last Name</th>
								  <th>Email</th>
								 
								  <th>Is Active</th>
								  <th>Action</th>
								</tr> 
							  </thead>
							  <tbody class="tdata">	
								@if(@$totalData !== 0)
								@foreach (@$lists as $list)	
								<tr id="id_{{@$list->id}}"> 
								  <td>{{ @$list->first_name == "" ? config('constants.empty') : str_limit(@$list->first_name, '50', '...') }}</td> 
								  <td>{{ @$list->last_name == "" ? config('constants.empty') : str_limit(@$list->last_name, '50', '...') }}</td> 
								  <td>{{ @$list->email == "" ? config('constants.empty') : str_limit(@$list->email, '50', '...') }}</td>  
								  <td><input data-id="{{@$list->id}}"  data-status="{{@$list->status}}" data-col="status" data-table="admins" class="change-status" value="1" type="checkbox" name="is_active" {{ (@$list->status == 1 ? 'checked' : '')}} data-bootstrap-switch></td> 	
								  <td><a href="{{URL::to('/admin/staff/edit/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="fa fa-edit"></i> Edit</a> / <a href="javascript:;" onClick="deleteAction({{@$list->id}}, 'admins')"><i class="fa fa-trash"></i> Delete</a>
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