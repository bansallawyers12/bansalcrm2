@extends('layouts.adminconsole')
@section('title', 'Add Source')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<form action="{{ url('adminconsole/source/store') }}" method="POST" name="add-source" autocomplete="off" enctype="multipart/form-data">
			@csrf
				<div class="row">   
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Add Source</h4>
								<div class="card-header-action">
									<a href="{{route('adminconsole.source.index')}}" class="btn btn-primary">@icon('arrow-left') Back</a>
								</div>
							</div>
						</div>
					</div>
				<div class="col-12">
						<div class="card">
							<div class="card-body">
								<div id="accordion"> 
									<div class="accordion">
										<div class="accordion-header" role="button" data-bs-toggle="collapse" data-bs-target="#primary_info" aria-expanded="true">
											<h4>Primary Information</h4>
										</div>
										<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
											<div class="row"> 						
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="name">Name <span class="span_req">*</span></label>
														<input type="text" name="name" value="" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Name" spellcheck="false">
														@if ($errors->has('name'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('name') }}</strong>
															</span> 
														@endif
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group float-end">
									<button type="submit" class="btn btn-primary">Save Source</button>
								</div> 
							</div>
						</div>	
					</div>
				</div>
			</form>
		</div>
	</section>
</div>

@endsection
