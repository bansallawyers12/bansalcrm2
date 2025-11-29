@extends('layouts.admin')
@section('title', 'Show Appointment')

@section('content')
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="col-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="card-body">
                    <div class="col-12 col-md-12 col-lg-12">
							<div class="card-header">
								<h4>Show Appointment</h4>
								<div class="card-header-action">
									<a href="{{route('appointments.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
					</div>
						<div id="accordion">
							<div class="accordion">

								<div class="accordion-body collapse show" id="contact_details" data-parent="#accordion">
									<div class="row">

                                        <div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="client">Client name</label>
												<div class="cus_field_input">
													{{ Form::text('client_id', @$appointment->clients->first_name.' '.@$appointment->clients->last_name, array('class' => 'form-control','autocomplete'=>'off','readonly')) }}
												</div>
											</div>
										</div>

										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="user">Added By</label>
												{{--@if($appointment->user)--}}
												{{--Form::text('user_id', @$appointment->user->first_name.' '.@$appointment->user->last_name, array('class' => 'form-control', 'autocomplete'=>'off','placeholder'=>'Enter User Name','readonly' ))--}}
												{{--@else--}}
												{{--Form::text('user_id', 'N/A', array('class' => 'form-control', 'autocomplete'=>'off','placeholder'=>'Enter User Name','readonly' ))--}}
												{{--@endif--}}

                                                @if($appointment->user)
                                                {{ Form::text('user_id', @$appointment->user->first_name.' '.$appointment->user->last_name, array('class' => 'form-control', 'autocomplete'=>'off','placeholder'=>'Enter User Name','readonly' ))}}
												@else
                                                {{ Form::text('user_id', 'N/A', array('class' => 'form-control', 'autocomplete'=>'off','placeholder'=>'Enter User Name','readonly' )) }}
												@endif
											</div>
										</div>

									</div>

                                    <div class="row">
										<!--<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="timezone">Timezone </label>
												{{--Form::text('timezone', @$appointment->timezone, array('class' => 'form-control', 'autocomplete'=>'off','readonly'))--}}
											</div>
										</div>-->
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="date">Date</label>
												<div class="cus_field_input">
													{{ Form::text('date', date('d/m/Y', strtotime(@$appointment->date)) , array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','readonly' )) }}
												</div>
											</div>
										</div>

                                        <div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="time">Time </label>
												{{ Form::text('time', @$appointment->timeslot_full, array('class' => 'form-control', 'autocomplete'=>'off','readonly' )) }}
											</div>
										</div>
									</div>

                                    <!--<div class="row">
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="time">Time </label>
												{{--Form::text('time', @$appointment->time, array('class' => 'form-control', 'autocomplete'=>'off','readonly' ))--}}
											</div>
										</div>
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="title">Title</label>
												<div class="cus_field_input">
													{{--Form::text('title', @$appointment->title, array('class' => 'form-control', 'autocomplete'=>'off','readonly' ))--}}
												</div>
											</div>
										</div>
									</div>-->

									<!--<div class="row">
										<div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="invites">Full name</label>
                                                <div class="cus_field_input">
                                                    {{--Form::text('full_name', @$appointment->full_name, array('class' => 'form-control', 'autocomplete'=>'off','readonly'))--}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="status">Email </label>
                                                {{--Form::text('email', @$appointment->email, array('class' => 'form-control', 'autocomplete'=>'off','readonly'))--}}
                                            </div>
                                        </div>
									</div>-->

									<div class="row">
										<div class="col-12 col-md-6 col-lg-6">
												<div class="form-group">
													<label for="invites">Nature of Enquiry</label>
													<div class="cus_field_input">
														{{ Form::text('nature_of_enquiry', @$appointment->natureOfEnquiry->title, array('class' => 'form-control', 'autocomplete'=>'off','readonly')) }}
													</div>
												</div>
											</div>
											<div class="col-12 col-md-6 col-lg-6">
												<div class="form-group">
													<label for="status">Service </label>
													{{ Form::text('service', @$appointment->service->title, array('class' => 'form-control', 'autocomplete'=>'off','readonly')) }}
												</div>
											</div>
										</div>
									</div>

                                    <div class="row">
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="description">Description </label>
												{{ Form::textarea('description', @$appointment->description, array('class' => 'form-control', 'autocomplete'=>'off','readonly' )) }}
											</div>
										</div>

                                        <div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
												<label for="status">Status <span class="span_req">*</span></label>
                                                <select class="form-control" name="status" data-valid="required" disabled>
                                                    <option value="0" <?php echo ($appointment->status == '0') ? 'selected' : ''; ?>>Pending/Not confirmed</option>
                                                    <option value="2" <?php echo ($appointment->status == '2') ? 'selected' : ''; ?>>Completed</option>
                                                    
                                                    <option value="4" <?php echo ($appointment->status == '4') ? 'selected' : ''; ?>>N/P</option>
                                                   
                                                    <option value="6" <?php echo ($appointment->status == '6') ? 'selected' : ''; ?>>Did Not Come</option>
                                                    <option value="7" <?php echo ($appointment->status == '7') ? 'selected' : ''; ?>>Cancelled</option>
                                                    <option value="8" <?php echo ($appointment->status == '8') ? 'selected' : ''; ?>>Missed</option>
                                                    <option value="9" <?php echo ($appointment->status == '9') ? 'selected' : ''; ?>>Payment Pending</option>
                                                    <option value="10" <?php echo ($appointment->status == '10') ? 'selected' : ''; ?>>Payment Success</option>
                                                    <option value="11" <?php echo ($appointment->status == '11') ? 'selected' : ''; ?>>Payment Failed</option>
                                                </select>
                                            </div>
										</div>
									</div>

                                    <div class="row">
                                      	<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group">
                                                <label for="appointment_details">Appointment details <span class="span_req">*</span></label>
                                                <select data-valid="required" class="form-control" name="appointment_details" disabled>
                                                    <option value="">Select</option>
                                                    <option value="phone" <?php echo ($appointment->appointment_details == 'phone') ? 'selected' : ''; ?>> Phone</option>
                                                    <option value="in_person" <?php echo ($appointment->appointment_details == 'in_person') ? 'selected' : ''; ?>>In person</option>
                                                  <option value="zoom_google_meeting" <?php echo ($appointment->appointment_details == 'zoom_google_meeting') ? 'selected' : ''; ?>>Zoom / Google Meeting</option>
                                                </select>

                                                @if ($errors->has('appointment_details'))
                                                    <span class="custom-error" role="alert">
                                                        <strong>{{ @$errors->first('appointment_details') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
										<!--<div class="col-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="invites">Invites</label>
                                                <div class="cus_field_input">
                                                    {{--Form::text('invites', @$appointment->invites, array('class' => 'form-control', 'autocomplete'=>'off','readonly'))--}}
                                                </div>
                                            </div>
                                        </div>-->


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
