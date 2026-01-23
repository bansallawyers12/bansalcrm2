@extends('layouts.adminconsole')
@section('title', 'Personal Document Category')

@section('content')

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
			    <div class="col-3 col-md-3 col-lg-3">
			        	@include('../Elements/Admin/setting')
		        </div>
				<div class="col-9 col-md-9 col-lg-9">
					<div class="card">
						<div class="card-header">
							<h4>Personal Document Category</h4>
							<div class="card-header-action">
								<a href="{{route('adminconsole.documentcategory.create')}}" class="btn btn-primary">Add New</a>
							</div>
						</div>
						<div class="card-body">
							<!-- Search and Filter -->
							<form method="GET" action="{{ route('adminconsole.documentcategory.index') }}" class="mb-3">
								<div class="row">
									<div class="col-md-4">
										<input type="text" name="search" class="form-control" placeholder="Search by category name..." value="{{ request('search') }}">
									</div>
									<div class="col-md-3">
										<select name="type" class="form-control">
											<option value="">All Types</option>
											<option value="default" {{ request('type') == 'default' ? 'selected' : '' }}>Default Categories</option>
											<option value="custom" {{ request('type') == 'custom' ? 'selected' : '' }}>Custom Categories</option>
										</select>
									</div>
									<div class="col-md-3">
										<select name="status" class="form-control">
											<option value="">All Status</option>
											<option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
											<option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
										</select>
									</div>
									<div class="col-md-2">
										<button type="submit" class="btn btn-primary w-100">Filter</button>
									</div>
								</div>
							</form>

							<div class="table-responsive common_table">
								<table class="table text_wrap">
								<thead>
									<tr>
										<th>@sortablelink('name', 'Category Name')</th>
										<th>Type</th>
										<th>Client Name</th>
										<th>Status</th>
										<th>@sortablelink('created_at', 'Created At')</th>
										<th>Action</th>
									</tr>
								</thead>
								@if($categories->count() > 0)
								<tbody class="tdata">
								@foreach ($categories as $category)
									<tr id="id_{{$category->id}}">
										<td>{{ $category->name }}</td>
										<td>
											@if($category->is_default)
												<span class="badge badge-success">Default</span>
											@else
												<span class="badge badge-info">Custom</span>
											@endif
										</td>
										<td>
											@if($category->client)
												{{ $category->client->first_name }} {{ $category->client->last_name }}
											@else
												<span class="text-muted">All Clients</span>
											@endif
										</td>
										<td>
											@if($category->status)
												<span class="badge badge-success">Active</span>
											@else
												<span class="badge badge-danger">Inactive</span>
											@endif
										</td>
										<td>{{ $category->created_at->format('d/m/Y') }}</td>
										<td>
											<div class="dropdown d-inline">
												<button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
												<div class="dropdown-menu">
													<a class="dropdown-item has-icon" href="{{route('adminconsole.documentcategory.edit', $category->id)}}"><i class="far fa-edit"></i> Edit</a>
													@if(!$category->is_default || $category->name !== 'General')
														<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction({{$category->id}}, 'document_categories')"><i class="fas fa-trash"></i> Delete</a>
													@endif
												</div>
											</div>
										</td>
									</tr>
								@endforeach
								</tbody>
								@else
								<tbody>
									<tr>
										<td style="text-align:center;" colspan="6">
											No Record found
										</td>
									</tr>
								</tbody>
								@endif
							</table>
						</div>

                        <div class="card-footer">
							{!! $categories->appends(\Request::except('page'))->render() !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

@endsection
