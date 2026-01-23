@extends('layouts.adminconsole')
@section('title', 'Edit Email Label')

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
						<div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
							<h4 style="color: white; margin: 0;">Edit Email Label</h4>
							<div class="card-header-action">
								<a href="{{route('adminconsole.emaillabels.index')}}" class="btn btn-light">Back</a>
							</div>
						</div>
						<div class="card-body">
							<form method="POST" action="{{ route('adminconsole.emaillabels.update', $label->id) }}">
								@csrf
								
								<div class="alert alert-info">
									<strong>Primary Information</strong>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="form-group mb-4">
											<label>Label Name <span class="text-danger">*</span></label>
											<input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $label->name) }}" placeholder="Enter Label Name" required {{ $label->type === 'system' ? 'readonly' : '' }}>
											@error('name')
												<div class="invalid-feedback">{{ $message }}</div>
											@enderror
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group mb-4">
											<label>Type <span class="text-danger">*</span></label>
											<select name="type" class="form-control" disabled>
												<option value="custom" {{ $label->type == 'custom' ? 'selected' : '' }}>Custom</option>
												<option value="system" {{ $label->type == 'system' ? 'selected' : '' }}>System</option>
											</select>
											<small class="form-text text-muted">Label type cannot be changed after creation.</small>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="form-group mb-4">
											<label>Color <span class="text-danger">*</span></label>
											<div class="input-group">
												<input type="color" name="color" id="colorPicker" class="form-control form-control-color @error('color') is-invalid @enderror" value="{{ old('color', $label->color) }}" required style="max-width: 80px; height: 45px;">
												<input type="text" id="colorHex" class="form-control" value="{{ old('color', $label->color) }}" readonly style="background: #f8f9fa;">
											</div>
											@error('color')
												<div class="invalid-feedback d-block">{{ $message }}</div>
											@enderror
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group mb-4">
											<label>Icon</label>
											<input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon', $label->icon) }}" placeholder="fas fa-tag">
											@error('icon')
												<div class="invalid-feedback">{{ $message }}</div>
											@enderror
											<small class="form-text text-muted">Enter FontAwesome icon class (e.g., fas fa-star, fas fa-flag)</small>
										</div>
									</div>
								</div>

								<div class="form-group mb-4">
									<label>Description</label>
									<textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Enter description (optional)">{{ old('description', $label->description) }}</textarea>
									@error('description')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>

								<div class="form-group mb-4">
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $label->is_active) == '1' ? 'checked' : '' }}>
										<label class="form-check-label" for="is_active">
											Active
										</label>
									</div>
								</div>

								@if($label->type === 'system')
									<div class="alert alert-warning">
										<i class="fas fa-info-circle"></i> <strong>Note:</strong> System labels have restricted editing. Only color, icon, description, and status can be modified.
									</div>
								@endif

								<div class="form-group row mb-4">
									<div class="col-sm-12">
										<button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">Update Email Label</button>
										<a href="{{route('adminconsole.emaillabels.index')}}" class="btn btn-secondary">Cancel</a>
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

@section('scripts')
<script>
$(document).ready(function() {
	// Sync color picker with hex input
	$('#colorPicker').on('input change', function() {
		$('#colorHex').val($(this).val().toUpperCase());
	});
	
	// Update color picker if hex input changes
	$('#colorHex').on('input', function() {
		var color = $(this).val();
		if(/^#[0-9A-F]{6}$/i.test(color)) {
			$('#colorPicker').val(color);
		}
	});
});
</script>
@endsection
