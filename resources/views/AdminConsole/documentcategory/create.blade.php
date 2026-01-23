@extends('layouts.adminconsole')
@section('title', 'Create Document Category')

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
							<h4>Create Document Category</h4>
							<div class="card-header-action">
								<a href="{{route('adminconsole.documentcategory.index')}}" class="btn btn-primary">Back</a>
							</div>
						</div>
						<div class="card-body">
							<form method="POST" action="{{ route('adminconsole.documentcategory.store') }}">
								@csrf
								
								<div class="form-group row mb-4">
									<label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Category Name <span class="text-danger">*</span></label>
									<div class="col-sm-12 col-md-7">
										<input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
										@error('name')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>

								<div class="form-group row mb-4">
									<label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Category Type <span class="text-danger">*</span></label>
									<div class="col-sm-12 col-md-7">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="is_default" id="default" value="1" {{ old('is_default', '0') == '1' ? 'checked' : '' }}>
											<label class="form-check-label" for="default">
												Default (Visible to all clients)
											</label>
										</div>
										<div class="form-check">
											<input class="form-check-input" type="radio" name="is_default" id="custom" value="0" {{ old('is_default', '0') == '0' ? 'checked' : '' }}>
											<label class="form-check-label" for="custom">
												Custom (User-created categories)
											</label>
										</div>
										@error('is_default')
											<div class="text-danger">{{ $message }}</div>
										@enderror
										<small class="form-text text-muted">Default categories will be visible to all clients. Custom categories are created by users for specific clients.</small>
									</div>
								</div>

								<div class="form-group row mb-4">
									<label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Status <span class="text-danger">*</span></label>
									<div class="col-sm-12 col-md-7">
										<select name="status" class="form-control @error('status') is-invalid @enderror" required>
											<option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
											<option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
										</select>
										@error('status')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>

								<div class="form-group row mb-4">
									<label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
									<div class="col-sm-12 col-md-7">
										<button type="submit" class="btn btn-primary">Create Category</button>
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
