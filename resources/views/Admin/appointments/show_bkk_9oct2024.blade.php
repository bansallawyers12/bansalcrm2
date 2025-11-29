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
												<label for="user">User name</label>
												<!--@if($appointment->user)
												{{--Form::text('user_id', @$appointment->user->first_name.' '.@$appointment->user->last_name, array('class' => 'form-control', 'autocomplete'=>'off','placeholder'=>'Enter User Name','readonly' ))--}}
												@else
												{{--Form::text('user_id', 'N/A', array('class' => 'form-control', 'autocomplete'=>'off','placeholder'=>'Enter User Name','readonly' ))--}}
												@endif-->
												
												 @if($appointment->full_name)
												{{ Form::text('user_id', @$appointment->full_name, array('class' => 'form-control', 'autocomplete'=>'off','placeholder'=>'Enter User Name','readonly' ))}}
												@else
												{{ Form::text('user_id', 'N/A', array('class' => 'form-control', 'autocomplete'=>'off','placeholder'=>'Enter User Name','readonly' )) }}
												@endif
											</div>
										</div> 
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group"> 
												<label for="client">Client name</label>
												<div class="cus_field_input">
													{{ Form::text('client_id', @$appointment->clients->first_name.' '.@$appointment->clients->last_name, array('class' => 'form-control','autocomplete'=>'off','readonly')) }}
												</div>
											</div>
										</div>
									</div>
									
                                    <div class="row">
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group"> 
												<label for="timezone">Timezone </label>
												{{ Form::text('timezone', @$appointment->timezone, array('class' => 'form-control', 'autocomplete'=>'off','readonly')) }}
											</div>
										</div> 
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group"> 
												<label for="date">Date</label>
												<div class="cus_field_input">
													{{ Form::text('date', date('d/m/Y', strtotime(@$appointment->date)), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','readonly' )) }}
												</div>
											</div>
										</div>
									</div>
									
                                    <div class="row">
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group"> 
												<label for="time">Time </label>
												{{ Form::text('time', @$appointment->time, array('class' => 'form-control', 'autocomplete'=>'off','readonly' )) }}
											</div>
										</div> 
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group"> 
												<label for="title">Title</label>
												<div class="cus_field_input">	
													{{ Form::text('title', @$appointment->title, array('class' => 'form-control', 'autocomplete'=>'off','readonly' )) }}
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-12 col-md-6 col-lg-6">
												<div class="form-group"> 
													<label for="invites">Full name</label>
													<div class="cus_field_input">
														{{ Form::text('full_name', @$appointment->full_name, array('class' => 'form-control', 'autocomplete'=>'off','readonly')) }}
													</div>
												</div>
											</div>
											<div class="col-12 col-md-6 col-lg-6">
												<div class="form-group"> 
													<label for="status">Email </label>									
													{{ Form::text('email', @$appointment->email, array('class' => 'form-control', 'autocomplete'=>'off','readonly')) }}
												</div>
											</div>
										</div>
									</div>
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
										<div class="col-12 col-md-12 col-lg-12">
											<div class="form-group"> 
												<label for="description">Description </label>
												{{ Form::textarea('description', @$appointment->description, array('class' => 'form-control', 'autocomplete'=>'off','readonly' )) }}
											</div>
										</div>
									</div>
                                    <div class="row">
										<div class="col-12 col-md-6 col-lg-6">
												<div class="form-group"> 
													<label for="invites">Invites</label>
													<div class="cus_field_input">
														{{ Form::text('invites', @$appointment->invites, array('class' => 'form-control', 'autocomplete'=>'off','readonly')) }}
													</div>
												</div>
											</div>
											<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group"> 
												<label for="status">Status <span class="span_req">*</span></label>
												@if(@$appointment->status =='0')
												{!! Form::select('status', ['Pending','Approved','Completed','Rejected','N/P'], 'Pending', ['class' => 'form-control','disabled']) !!}
												@elseif(@$appointment->status =='1')
												{!! Form::select('status', ['Approved','Pending','Completed','Rejected','N/P'], 'Approved', ['class' => 'form-control','disabled']) !!}
												@elseif(@$appointment->status =='2')
												{!! Form::select('status', ['Completed','Pending','Approved','Rejected','N/P'], 'Completed', ['class' => 'form-control','disabled']) !!}
												@elseif(@$appointment->status =='3')
												{!! Form::select('status', ['Rejected','Approved','Completed','Pending','N/P'], 'Rejected', ['class' => 'form-control','disabled']) !!}
												@elseif(@$appointment->status =='4')
												{!! Form::select('status', ['N/P','Pending','Approved','Completed','Rejected'], 'N/P', ['class' => 'form-control','disabled']) !!}
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
		</div>
	</section>
</div>
@endsection