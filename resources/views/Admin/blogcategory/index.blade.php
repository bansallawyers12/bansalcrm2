@extends('layouts.admin')
@section('title', 'Blog Category')

@section('content')
<style>
.fc-event-container .fc-h-event{cursor:pointer;}
</style>
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="custom-error-msg">
			</div>
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Blog Category</h4>
							<div class="card-header-action">	
								<!-- <div class="card-title"> -->
									<a href="{{route('admin.blogcategory.create')}}" class="btn btn-primary"><i class="fa fa-plus"></i>New Blog Category</a> 
								<!-- </div> 							 -->
									<!-- <a href="{{URL::to('admin/quotations/template/create')}}"  class="btn btn-primary is_checked_clientn">Create Template</a> -->
							</div>
						</div>
						<div class="card-body">
							<div class="tab-content" id="quotationContent">
								<div class="card-tools card_tools">
									<!-- <div class="input-group input-group-sm" style="width: 150px;">
										<input type="text" name="table_search" class="form-control float-right" placeholder="Search">
										<div class="input-group-append">
											<button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
										</div>
									</div>
								</div> -->
								<div class="tab-pane fade show active" id="active_quotation" role="tabpanel" aria-labelledby="active_quotation-tab">
									<div class="table-responsive common_table"> 
									<table id="hoteltable" class="table table-bordered table-hover text-nowrap">
							  <thead>
								<tr>
								  <th>Id</th>
								  <th>Name</th>
								  <th>Slug</th>
								  <th>Category</th>
								  <th>Status</th>
								  <th class="no-sort">Action</th>
								</tr> 
							  </thead>
							  <tbody class="tdata">	
								@foreach (@$lists as $list)	
								<tr id="id_{{@$list->id}}"> 
								  <td>{{@$list->id}}</td> 
								  <td style="white-space: initial;">{{ @$list->name == "" ? config('constants.empty') : str_limit(@$list->name, '50', '...') }}</td> 
								  <td style="white-space: initial;">{{ @$list->slug }}</td> 
								  <td style="white-space: initial;">{{ @$list->parent->name ?? "N/A"}}</td> 
								  <td style="white-space: initial;"><input data-id="{{@$list->id}}"  data-status="{{@$list->status}}" data-col="status" data-table="blog_categories" class="change-status" value="1" type="checkbox" name="status" {{ (@$list->status == 1 ? 'checked' : '')}} data-bootstrap-switch></td> 
								  <td>
										<a class="btn btn-info" href="{{URL::to('/admin/blogcategories/edit/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="fa fa-edit"></i> Edit</a>
										<a class="btn btn-danger" href="javascript:;" onClick="deleteAction({{@$list->id}}, 'blog_categories')"><i class="fa fa-trash"></i> Delete</a>
								  </td>
								</tr>	
							  @endforeach		
							  </tbody>	 
							</table>
								</div>
								<div class="card-footer hide">
							{{-- {!! $lists->appends(\Request::except('page'))->render() !!} --}}
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