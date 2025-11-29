@extends('layouts.admin')
@section('title', 'Edit Appointment')


@section('content')
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
		<form action="{{ route('appointments.update',$appointment->id) }}" method="POST">
        @csrf
        @method('PUT')	
		{{ Form::hidden('id', @$appointment->id) }} 
				<!-- <div class="row"> -->
			<div class="col-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="card-body">
                    <div class="col-12 col-md-12 col-lg-12">
						<!-- <div class="card"> -->
							<div class="card-header">
								<h4>Edit Appointment</h4>
								<div class="card-header-action">
									<a href="{{route('appointments.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						<!-- </div> -->
					</div>
						<div id="accordion">
							<div class="accordion">
								<div class="accordion-body collapse show" id="contact_details" data-parent="#accordion">
									<div class="row">
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group"> 
												<input type="hidden" name="route" value="{{url()->previous()}}">
												<label for="user">User name </label>
												{{-- Form::text('user_id', @$appointment->user->first_name.' '.@$appointment->user->last_name, array('class' => 'form-control', 'data-valid'=>'required','readonly', 'autocomplete'=>'off','placeholder'=>'Enter User Name' ))--}}
												{{ Form::text('user_id', @$appointment->full_name, array('class' => 'form-control', 'data-valid'=>'required','readonly', 'autocomplete'=>'off','placeholder'=>'Enter User Name' )) }}
                                                <!-- @if ($errors->has('user_id'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('user_id') }}</strong>
													</span> 
												@endif -->
											</div>
										</div> 
										<input class="form-control" id="user_id" type="hidden" name="user_id" value="{{$appointment->user_id}}" >
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group"> 
												<label for="client">Client name</label>
												<div class="cus_field_input">
													{{ Form::text('client_id', @$appointment->clients->first_name.' '.@$appointment->clients->last_name, array('class' => 'form-control', 'data-valid'=>'','readonly', 'autocomplete'=>'off','placeholder'=>'Enter CLient Name' )) }}
													@if ($errors->has('client_id'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('client_id') }}</strong>
														</span> 
													@endif
													<input class="form-control" id="client_id" type="hidden" name="client_id" value="{{$appointment->client_id}}" >
												</div>
											</div>
										</div>
									</div>
									
                                    <div class="row">
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group"> 
												<label for="timezone">Timezone </label>
												{{ Form::text('timezone', @$appointment->timezone, array('class' => 'form-control','readonly', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Select timezone' )) }}
												@if ($errors->has('timezone'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('timezone') }}</strong>
													</span> 
												@endif
											</div>
										</div> 
									<!-- dd('dfsdfg'); -->
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group"> 
												<label for="date">Date <span class="span_req">*</span></label>
												<div class="cus_field_input">
													<div class="country_code"> 
														<!-- <input class="date" id="date" type="date" name="date"  readonly> -->
													</div>	
													{{ Form::date('date', @$appointment->date, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select date' )) }}
													@if ($errors->has('date'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('date') }}</strong>
														</span> 
													@endif
												</div>
											</div>
										</div>
									</div>
									
                                    <div class="row">
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group"> 
												<label for="time">Time <span class="span_req">*</span></label>
												{{ Form::time('time', @$appointment->time, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Select time' )) }}
												@if ($errors->has('time'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('time') }}</strong>
													</span> 
												@endif
											</div>
										</div> 
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group"> 
												<label for="title">Title <span class="span_req">*</span></label>
												<div class="cus_field_input">
													<div class="title"> 
														<!-- <input class="title" id="title" type="text" name="title" readonly> -->
													</div>	
													{{ Form::text('title', @$appointment->title, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter title' )) }}
													@if ($errors->has('title'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('title') }}</strong>
														</span> 
													@endif
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group"> 
												<label for="time">Full name <span class="span_req">*</span></label>
												{{ Form::text('full_name', @$appointment->full_name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter full name' )) }}
												@if ($errors->has('full_name'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('full_name') }}</strong>
													</span> 
												@endif
											</div>
										</div> 
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group"> 
												<label for="title">Email <span class="span_req">*</span></label>
												<div class="cus_field_input">
													<div class="title"> 
														<!-- <input class="title" id="title" type="text" name="title" readonly> -->
													</div>	
													{{ Form::email('email', @$appointment->email, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter email' )) }}
													@if ($errors->has('email'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('email') }}</strong>
														</span> 
													@endif
												</div>
											</div>
										</div>
									</div>
								
                                    <div class="row">
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group"> 
												<label for="description">Description <span class="span_req">*</span></label>
												{{ Form::text('description', @$appointment->description, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter description' )) }}
												@if ($errors->has('description'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('description') }}</strong>
													</span> 
												@endif
											</div>
										</div> 
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group"> 
												<label for="invites">Invites <span class="span_req">*</span></label>
												<div class="cus_field_input">
													<div class="invites"> 
														<!-- <input class="invites" id="invites" type="text" name="invites" readonly > -->
													</div>	
													{{ Form::number('invites', @$appointment->invites, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter invites' )) }}
													@if ($errors->has('invites'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('invites') }}</strong>
														</span> 
													@endif
												</div>
											</div>
										</div>
									</div>
                                    <div class="row">
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group"> 
											<label for="noe_id">Nature of Enquiry<span class="span_req">*</span></label>
											<select class="form-control  select2" name="noe_id" >
												<option value="" >Select Nature of Enquiry</option>
											<?php
												foreach(\App\NatureOfEnquiry::all() as $list){
													?>
													<option <?php if(@$list->id == $appointment->noe_id){ echo 'selected'; } ?> value="{{@$list->id}}" >{{@$list->title}}</option>
													<?php
												}
												?>
											</select>
											
											@if ($errors->has('noe_id'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('noe_id') }}</strong>
												</span> 
											@endif 
										</div>
										</div> 
										<div class="col-12 col-md-6 col-lg-6">
											<div class="form-group"> 
												<label for="status">Status <span class="span_req">*</span></label>
												@if(@$appointment->status =='0')
												{!! Form::select('status', ['Pending','Approved','Completed','Rejected','N/P'], 'Pending', ['class' => 'form-control']) !!}
												@elseif(@$appointment->status =='1')
												{!! Form::select('status', ['Approved','Pending','Completed','Rejected','N/P'], 'Approved', ['class' => 'form-control']) !!}
												@elseif(@$appointment->status =='2')
												{!! Form::select('status', ['Completed','Pending','Approved','Rejected','N/P'], 'Completed', ['class' => 'form-control']) !!}
												@elseif(@$appointment->status =='3')
												{!! Form::select('status', ['Rejected','Approved','Completed','Pending','N/P'], 'Rejected', ['class' => 'form-control']) !!}
												@elseif(@$appointment->status =='4')
												{!! Form::select('status', ['N/P','Pending','Approved','Completed','Rejected'], 'N/P', ['class' => 'form-control']) !!}
												@else
												{!! Form::select('status', ['Pending','Approved','Completed','Rejected','N/P'], ['class' => 'form-control']) !!}
												@endif
												@if ($errors->has('status'))
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('status') }}</strong>
													</span> 
												@endif
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group float-right">
							{{ Form::submit('Update', ['class'=>'btn btn-primary' ]) }}
						</div>
					</div>
				</div>
			</div>	
		</div>	
		{{ Form::close() }}
		</div>
	</section>
</div>

@endsection