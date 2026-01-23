@extends('layouts.adminconsole')
@section('title', 'Checklists')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			{!! Form::open(array('url' => 'adminconsole/checklist/store', 'name'=>"add-visatype", 'autocomplete'=>'off', "enctype"=>"multipart/form-data"))  !!}
			<div class="row">   
				<div class="col-12">
					<div class="card">
						<div class="card-header">
							<h4>Checklists</h4>
							<div class="card-header-action">
								<a href="{{route('adminconsole.checklist.index')}}" class="btn btn-primary" style="margin-right: 10px;"><i class="fa fa-arrow-left"></i> Back</a>
								{!! Form::submit('Save', ['class'=>'btn btn-primary'])  !!}
							</div>
						</div>
						<div class="card-body" style="padding: 0;">
							<div id="accordion"> 
								<div class="accordion">
									<div class="accordion-header" role="button" data-bs-toggle="collapse" data-bs-target="#primary_info" aria-expanded="true" style="margin-bottom: 0;">
										<h4>Primary Information</h4>
									</div>
									<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion" style="padding: 15px 20px 15px 20px;">
										<div class="row" style="margin-bottom: 0;"> 						
											<div class="col-12 col-md-4 col-lg-4">
												<div class="form-group" style="margin-bottom: 10px;"> 
													<label for="name">Name <span class="span_req">*</span></label>
													{!! Form::text('name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Name' ))  !!}
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
						</div>
					</div>
				</div>
			</div>
			{!! Form::close()  !!}
		</div>
	</section>
</div>

@endsection