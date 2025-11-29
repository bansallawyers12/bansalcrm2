@extends('layouts.frontend-editclient')
@section('title', 'Verify DOB')

@section('content')
<!-- Main Content -->
<div class="main-content">
	<section class="section">
	    <div class="server-error">
			@include('../Elements/flash-message')
		</div>

		<div class="section-body">
            <form action="{{ url('/verify-dob') }}" method="POST">
            @csrf
            <input type="hidden" name="client_id" value="{{ $client_id }}">

            <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Verify Your Date of Birth</h4>
                        </div>
                    </div>
                </div>
				<div class="col-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-12 col-lg-12">
                                    <div class="row">
                                        <div class="col-3 col-md-3 col-lg-3">
                                            <div class="form-group" style="width: 90%;">
                                                <label for="dob">Date of Birth</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fas fa-calendar-alt"></i>
                                                        </div>
                                                    </div>

													{{ Form::text('dob','', array('class' => 'form-control dobdatepickers', 'data-valid'=>'required')) }}
                                                    @if ($errors->has('dob'))
                                                        <span class="custom-error" role="alert">
                                                            <strong>{{ @$errors->first('dob') }}</strong>
                                                        </span>
                                                    @endif
												</div>
											</div>
                                        </div>
									</div>
                                </div>

								<div class="row">
                                    <div class="col-sm-12">
										<div class="form-group float-right">
											<input type="submit" class="btn btn-primary" value="Verify">
										</div>
									</div>
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

