@extends('layouts.admin')
@section('title', 'Tax Setting')

@section('content')
 
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">Tax Setting</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Tax Setting</li>
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
				<div class="col-md-4">	
					<div class="card">
						<div class="card-body p-0" style="display: block;">
							<ul class="nav nav-pills flex-column"> <!---->
								<li class="nav-item"> <a href="{{route('admin.taxrates')}}" id="ember5167" class="nav-link  active ember-view"> Tax Rates </a> </li><!----><!----><!----><!----><li class="nav-item"> <a href="{{route('admin.returnsetting')}}" id="ember5168" class="nav-link ember-view"> GST Settings </a> </li> <!----><!----> </ul>
						</div>
					</div>
				</div>
				<div class="col-md-8">
					<div class="card card-primary">
					  <div class="card-header">
						<h3 class="card-title">Tax Rate</h3>
					  </div> 
					  <!-- /.card-header -->
					  <!-- form start -->
						<div class="card-body">
							<div class="card-header">
							<div class="float-right">
								<a href="{{route('admin.taxrates.create')}}" class="btn btn-primary"><i class="fa fa-plus"></i> New Tax</a>
							</div>
						</div>
							<div class="card-body table-responsive">
							<table id="departurecity_table" class="table table-bordered table-hover text-nowrap"> 
							  <thead>
								<tr>
					
								  <th>Name</th>
								  <th>Rate</th>
								  <th class="no-sort">Action</th>
								</tr> 
							  </thead>
							  <tbody class="tdata">	
								@foreach (@$lists as $list)	
								<tr id="id_{{@$list->id}}">
									
								  <td>{{ @$list->name == "" ? config('constants.empty') : str_limit(@$list->name, '50', '...') }}</td>
								  <td>{{@$list->rate}}</td>
								  <td>
									<div class="nav-item dropdown action_dropdown">
										<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
										<div class="dropdown-menu">
											<a href="{{URL::to('/admin/settings/taxes/taxrates/edit/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="fa fa-edit"></i> Edit</a>
											<a href="javascript:;" onClick="deleteAction({{@$list->id}}, 'tax_rates')"><i class="fa fa-trash"></i> Delete</a>
										</div>
									</div>
								  </td>
								</tr>	 
								@endforeach						
							  </tbody>
							</table>
							<div class="card-footer hide">							
							 </div>
						  </div>
						</div> 
					
					</div>	
				</div>	
			</div>
		</div>
	</section>
</div>
<script>
jQuery(document).ready(function($){
	$('input[name="is_business_gst"]').on('change', function(){
		var val = $('input[name="is_business_gst"]:checked').val();
		if(val == 'yes'){
			$('.is_gst_yes').show();
			$('input[name="gstin"]').attr('data-valid','required min-15 max-15');
		}else{
			$('.is_gst_yes').hide();
			$('input[name="gstin"]').attr('data-valid','');
		}
	});
});
</script>
@endsection