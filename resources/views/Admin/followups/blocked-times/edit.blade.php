@extends(request()->routeIs('adminconsole.followups.*') ? 'layouts.adminconsole' : 'layouts.admin')
@section('title', 'Edit blocked time')

@section('content')
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="row justify-content-center">
				<div class="col-12 col-lg-8">
					<div class="d-flex justify-content-between align-items-center mb-3">
						<h4 class="mb-0">Edit blocked time</h4>
						<a href="{{ followups_console_route('blocked-times.index') }}" class="btn btn-outline-secondary btn-sm">Back to list</a>
					</div>
					<div class="card">
						<div class="card-body">
							<form method="post" action="{{ followups_console_route('blocked-times.update', $block) }}">
								@csrf
								@method('PUT')
								@include('Admin.followups.blocked-times._form', ['block' => $block])
								<button type="submit" class="btn btn-primary">Update</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
