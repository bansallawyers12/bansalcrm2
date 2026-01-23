@extends('layouts.adminconsole')
@section('title', 'Edit Document Category')

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
				<div class="col-12">
					<div class="card">
						<div class="card-header">
							<h4>Edit Document Category</h4>
							<div class="card-header-action">
								<a href="{{route('adminconsole.documentcategory.index')}}" class="btn btn-primary">Back</a>
							</div>
						</div>
						<div class="card-body">
							<form method="POST" action="{{ route('adminconsole.documentcategory.update', $category->id) }}">
								@csrf
								
								<div class="form-group row mb-4">
									<label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Category Name <span class="text-danger">*</span></label>
									<div class="col-sm-12 col-md-7">
										<input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required>
										@error('name')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>

								<div class="form-group row mb-4">
									<label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Category Type</label>
									<div class="col-sm-12 col-md-7">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="is_default" id="default" value="1" {{ $category->is_default ? 'checked' : '' }} disabled>
											<label class="form-check-label" for="default">
												Default (Visible to all clients)
											</label>
										</div>
										<div class="form-check">
											<input class="form-check-input" type="radio" name="is_default" id="custom" value="0" {{ !$category->is_default ? 'checked' : '' }} disabled>
											<label class="form-check-label" for="custom">
												Custom (User-created categories)
											</label>
										</div>
										<small class="form-text text-muted">Category type cannot be changed after creation.</small>
									</div>
								</div>

								@if($category->client)
								<div class="form-group row mb-4">
									<label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Client</label>
									<div class="col-sm-12 col-md-7">
										<input type="text" class="form-control" value="{{ $category->client->first_name }} {{ $category->client->last_name }}" disabled>
										<small class="form-text text-muted">Category is specific to this client.</small>
									</div>
								</div>
								@endif

								@if($category->user)
								<div class="form-group row mb-4">
									<label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Created By</label>
									<div class="col-sm-12 col-md-7">
										<input type="text" class="form-control" value="{{ $category->user->first_name }} {{ $category->user->last_name }}" disabled>
									</div>
								</div>
								@endif

								<div class="form-group row mb-4">
									<label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Status <span class="text-danger">*</span></label>
									<div class="col-sm-12 col-md-7">
										<select name="status" class="form-control @error('status') is-invalid @enderror" required>
											<option value="1" {{ old('status', $category->status) == '1' ? 'selected' : '' }}>Active</option>
											<option value="0" {{ old('status', $category->status) == '0' ? 'selected' : '' }}>Inactive</option>
										</select>
										@error('status')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>

								<div class="form-group row mb-4">
									<label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
									<div class="col-sm-12 col-md-7">
										<button type="submit" class="btn btn-primary">Update Category</button>
										<a href="{{route('adminconsole.documentcategory.index')}}" class="btn btn-secondary">Cancel</a>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

@endsection
