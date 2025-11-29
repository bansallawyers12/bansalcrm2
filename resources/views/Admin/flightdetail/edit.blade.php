@extends('layouts.adminnew')
@section('title', 'Edit Flight Detail')

@section('content')
 
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">Edit Flight Detail</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Edit Flight Detail</li>
					</ol>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.container-fluid -->
	</div>
	<!-- /.content-header -->	
	<!-- Breadcrumb start-->
	<!--<ol class="breadcrumb">
		<li class="breadcrumb-item active">
			Home / <b>Dashboard</b>
		</li>
		@include('../Elements/Admin/breadcrumb')
	</ol>-->
	<!-- Breadcrumb end-->
	
	<!-- Main content --> 
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<!-- Flash Message Start -->
					<div class="server-error">
						@include('../Elements/flash-message')
					</div>
					<!-- Flash Message End -->
				</div> 
				<div class="col-md-12">
					<div class="card card-primary">
					  <div class="card-header">
						<h3 class="card-title">Edit Flight</h3>
					  </div> 
					  <!-- /.card-header -->
					  <!-- form start -->
					  {{ Form::open(array('url' => 'admin/flight-detail/edit', 'name'=>"add-flights", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
					    {{ Form::hidden('id', @$fetchedData->id) }}
						<div class="card-body">
							<div class="form-group" style="text-align:right;">
								<a style="margin-right:5px;" href="{{route('admin.flightdetail.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>  
								{{ Form::button('<i class="fa fa-save"></i> Save Flight', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-flights")' ]) }}
							</div>
							<div class="form-group row"> 
								<label for="agent" class="col-sm-2 col-form-label">Flight Type <span style="color:#ff0000;">*</span></label>
								<div class="col-sm-10">
									<label ><input @if(@$fetchedData->flighttype == 1) checked @endif class="flighttyp" type="radio" name="flighttype" value="1">One Way</label>
									<label ><input class="flighttyp" type="radio"  @if(@$fetchedData->flighttype == 2) checked @endif name="flighttype" value="2">Two Way</label>
								</div>
							</div>
							<div class="form-group row"> 
								<label for="agent" class="col-sm-2 col-form-label">Agent <span style="color:#ff0000;">*</span></label>
								<div class="col-sm-10">
								<select id="agent" name="agent" data-valid="required" class="form-control select2_name" style="width: 100%;">
									<option value="">Select Agent</option>
									@foreach($users as $user) 
										<option value="{{@$user->id}}" @if(@$fetchedData->agent == @$user->id) selected @endif>{{@$user->company_name}}</option>
									@endforeach
								</select>
								</div>   
							</div>
							<div class="form-group row"> 
								<label for="name" class="col-sm-2 col-form-label">Flight <span style="color:#ff0000;">*</span></label>
								<div class="col-sm-10">
								<select id="flight_name" name="flight_name" data-valid="required" class="form-control select2_name" style="width: 100%;">
									<option value="">Select Flight</option>
									@foreach($flights as $clist)
										<option value="{{@$clist->id}}" @if(@$fetchedData->flight_id == @$clist->id) selected @endif>{{@$clist->name}} ({{@$clist->code}})</option>
									@endforeach
								</select>
								</div> 
							</div>
							<div class="form-group row"> 
								<label for="flight_source" class="col-sm-2 col-form-label">Source <span style="color:#ff0000;">*</span></label>
								<div class="col-sm-10">
								<select id="flight_source" name="flight_source" data-valid="required" class="form-control select2_source" style="width: 100%;">
									<option value="">Select Source</option>
									@foreach(\App\Airport::all() as $slist)
										<option value="{{$slist->id}}" @if(@$fetchedData->flight_source == @$slist->id) selected @endif>{{$slist->city_name}} ({{$slist->airport_code}})</option>
									@endforeach
								</select>
								</div>  
							</div>
							<div class="form-group row">  
								<label for="flight_destination" class="col-sm-2 col-form-label">Destination <span style="color:#ff0000;">*</span></label>
								<div class="col-sm-10">
								<select id="flight_destination" name="flight_destination" data-valid="required" class="form-control select2_destination" style="width: 100%;">
									<option value="">Select Destination</option>
									@foreach(\App\Airport::all() as $dlist)
										<option value="{{$dlist->id}}" @if(@$fetchedData->flight_destination == @$dlist->id) selected @endif>{{$dlist->city_name}} ({{$dlist->airport_code}})</option>
									@endforeach
								</select>
								</div> 
							</div>
							<div class="form-group row"> 
								<label for="flight_number" class="col-sm-2 col-form-label">Flight Number</label>
								<div class="col-sm-10">
								{{ Form::text('flight_number', @$fetchedData->flight_number, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Flight Number' )) }}
								@if ($errors->has('flight_number'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('flight_number') }}</strong>
									</span> 
								@endif
								</div>  
							</div>
							<div class="form-group row"> 
								<label for="dep_time" class="col-sm-2 col-form-label">Departure Time</label>
								<div class="col-sm-10">
								
								{{ Form::text('dep_time', @$fetchedData->dep_time, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Departure Time', 'id' =>'deptime' )) }}
								
								@if ($errors->has('dep_time'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('dep_time') }}</strong>
									</span> 
								@endif
								</div>  
							</div>
							<div class="form-group row"> 
								<label for="arival_time" class="col-sm-2 col-form-label">Arrival Time</label>
								<div class="col-sm-10">
								{{ Form::text('arival_time', @$fetchedData->arival_time, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Arrival Time', 'id'=>'artime' )) }}
								@if ($errors->has('arival_time'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('arival_time') }}</strong>
									</span> 
								@endif
								</div>  
							</div>
							<div class="form-group row"> 
								<label for="duration" class="col-sm-2 col-form-label">Duration</label>
								<div class="col-sm-10">
								{{ Form::text('duration', @$fetchedData->duration, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Duration' )) }}
								@if ($errors->has('duration'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('flight_number') }}</strong>
									</span> 
								@endif
								</div>  
							</div>
							
							<div class="form-group row"> 
								<label for="flight_number" class="col-sm-2 col-form-label">Stop</label>
								<div class="col-sm-10">
									<select data-valid="required" class="form-control" name="stop">
										<option value="">----</option>
										<option value="non-stop" @if(@$fetchedData->stop == 'non-stop') selected @endif>Non Stop</option>
										<option value="one-stop" @if(@$fetchedData->stop == 'one-stop') selected @endif>1 Stop</option>
										<option value="two-stop" @if(@$fetchedData->stop == 'two-stop') selected @endif>2 Stop</option>
										<option value="three-stop" @if(@$fetchedData->stop == 'three-stop') selected @endif>3 Stop</option>
									</select>
								</div>  
							</div>
							<?php
							$required = '';
							if(@$fetchedData->flighttype ==2){
								$required = 'required';
							}
							?>
							<div @if(@$fetchedData->flighttype ==2)@else style="display:none;" @endif class="form-group row is_return"></hr> 
							</div>
							<div @if(@$fetchedData->flighttype ==2)@else style="display:none;" @endif class="form-group row is_return"> 
								<label for="ret_flight_id" class="col-sm-2 col-form-label">Return Flight <span style="color:#ff0000;">*</span></label>
								<div class="col-sm-10">
								<select id="ret_flight_id" name="ret_flight_id" data-valid="{{$required}}" class="form-control select2_name" style="width: 100%;">
									<option value="">Select Flight</option>
									@foreach($flights as $clist) 
										<option value="{{@$clist->id}}" @if(@$fetchedData->ret_flight_id == @$clist->id) selected @endif>{{@$clist->name}} ({{@$clist->code}})</option>
									@endforeach
								</select>
								</div>   
							</div>
								<div @if(@$fetchedData->flighttype ==2)@else style="display:none;" @endif class="form-group row is_return"> 
								<label for="ret_flight_number" class="col-sm-2 col-form-label">Return Flight Number</label>
								<div class="col-sm-10">
								{{ Form::text('ret_flight_number', @$fetchedData->ret_flight_number, array('class' => 'form-control', 'data-valid'=>$required, 'autocomplete'=>'off','placeholder'=>'Enter Flight Number' )) }}
								@if ($errors->has('ret_flight_number'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('ret_flight_number') }}</strong>
									</span> 
								@endif
								</div>  
							</div>
							<div @if(@$fetchedData->flighttype ==2)@else style="display:none;" @endif class="form-group row is_return"> 
								<label for="ret_dep_time" class="col-sm-2 col-form-label">Return Departure Time</label>
								<div class="col-sm-10">
								
								{{ Form::text('ret_dep_time', @$fetchedData->ret_dep_time, array('class' => 'form-control ', 'data-valid'=>$required, 'autocomplete'=>'off','placeholder'=>'Enter Return Departure Time', 'id' =>'retdeptime' )) }}
								
								@if ($errors->has('ret_dep_time'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('ret_dep_time') }}</strong>
									</span> 
								@endif
								</div>  
							</div>
							<div @if(@$fetchedData->flighttype ==2)@else style="display:none;" @endif class="form-group row is_return"> 
								<label for="ret_arv_time" class="col-sm-2 col-form-label">Return Arrival Time</label>
								<div class="col-sm-10">
								{{ Form::text('ret_arv_time', @$fetchedData->ret_arv_time, array('class' => 'form-control', 'data-valid'=>$required, 'autocomplete'=>'off','placeholder'=>'Enter Return Arrival Time', 'id'=>'retartime' )) }}
								@if ($errors->has('ret_arv_time'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('ret_arv_time') }}</strong>
									</span> 
								@endif
								</div>  
							</div>
							<div @if(@$fetchedData->flighttype ==2)@else style="display:none;" @endif class="form-group row is_return"> 
								<label for="duration" class="col-sm-2 col-form-label">Return Duration</label>
								<div class="col-sm-10">
								{{ Form::text('ret_duration', @$fetchedData->ret_duration, array('class' => 'form-control', 'data-valid'=>$required, 'autocomplete'=>'off','placeholder'=>'Enter Return Duration' )) }}
								@if ($errors->has('ret_duration'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('ret_duration') }}</strong>
									</span> 
								@endif
								</div>  
							</div>
							
							<div @if(@$fetchedData->flighttype ==2)@else style="display:none;" @endif  class="form-group row is_return"> 
								<label for="ret_stop" class="col-sm-2 col-form-label">Return Stop</label>
								<div class="col-sm-10">
									<select data-valid="" class="form-control" name="ret_stop">
										<option value="">----</option>
										<option value="non-stop" @if(@$fetchedData->ret_stop == 'non-stop') selected @endif>Non Stop</option>
										<option value="one-stop" @if(@$fetchedData->ret_stop == 'one-stop') selected @endif>1 Stop</option>
										<option value="two-stop" @if(@$fetchedData->ret_stop == 'two-stop') selected @endif>2 Stop</option>
										<option value="three-stop" @if(@$fetchedData->ret_stop == 'three-stop') selected @endif>3 Stop</option>
									</select>
								</div>  
							</div>
							<div class="form-group row"> 
								<label for="bc_total" class="col-sm-2 col-form-label">B2C Total Fare</label>
								<div class="col-sm-10">
								{{ Form::text('bc_total', @$fetchedData->bc_total, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter B2C Total Fare' )) }}
								@if ($errors->has('bc_total'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('bc_total') }}</strong>
									</span> 
								@endif
								</div>
							</div>
							<div class="form-group row"> 
								<label for="bb_total" class="col-sm-2 col-form-label">B2B Total Fare</label>
								<div class="col-sm-10">
								{{ Form::text('bb_total', @$fetchedData->bb_total, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter B2B Total Fare' )) }}
								@if ($errors->has('bb_total'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('bb_total') }}</strong>
									</span> 
								@endif
								</div>  
							</div>
							<div class="form-group row"> 
								<label for="fare_detail" class="col-sm-2 col-form-label">Fare Detail</label>
								<div class="col-sm-10">
								<textarea name="fare_detail" data-valid ="required" class="textarea" placeholder="Please Add Description Here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{@$fetchedData->fare_detail}}</textarea>
								@if ($errors->has('fare_detail'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('fare_detail') }}</strong>
									</span> 
								@endif
								</div>  
							</div>
							<div class="form-group row"> 
								<label for="check_in_baggage" class="col-sm-2 col-form-label">Check in baggage</label>
								<div class="col-sm-10">
								<select class="form-control" name="check_in_baggage">
									<option value="20kg" @if(@$fetchedData->check_in_baggage == "20kg") selected @endif>20kg</option>
									<option value="25kg" @if(@$fetchedData->check_in_baggage == "25kg") selected @endif>25kg</option>
									<option value="30kg" @if(@$fetchedData->check_in_baggage == "30kg") selected @endif>30kg</option>
									<option value="1 piece" @if(@$fetchedData->check_in_baggage == "1 piece") selected @endif>1 piece</option>
									<option value="2 piece" @if(@$fetchedData->check_in_baggage == "2 piece") selected @endif>2 piece</option>
								</select>
								@if ($errors->has('check_in_baggage'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('check_in_baggage') }}</strong>
									</span> 
								@endif
								</div>  
							</div>
							<div class="form-group row"> 
								<label for="cabbin_baggage" class="col-sm-2 col-form-label">Cabin baggage</label>
								<div class="col-sm-10">
								<select class="form-control" name="cabbin_baggage">
									<option value="7kg" @if(@$fetchedData->cabbin_baggage == "7kg") selected @endif>7kg</option>
									
								</select>
								@if ($errors->has('cabbin_baggage'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('cabbin_baggage') }}</strong>
									</span> 
								@endif
								</div>  
							</div>
							<div class="form-group row"> 
								<label for="cancellation_policy" class="col-sm-2 col-form-label">Cancellation Policy</label>
								<div class="col-sm-10">
								<textarea name="cancellation_policy" data-valid ="required" class="textarea" placeholder="Please Add Description Here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{@$fetchedData->cancellation_policy}}</textarea>
								@if ($errors->has('cancellation_policy'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('cancellation_policy') }}</strong>
									</span> 
								@endif
								</div>  
							</div>
							<div class="form-group float-right">
								{{ Form::button('<i class="fa fa-save"></i> Save Flight', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-flights")' ]) }}
							</div> 
						</div>  
					  {{ Form::close() }}
					</div>	   
				</div>	
			</div>
		</div>
	</section>
</div>
<script type="text/javascript">
            $(function () {
				$('.flighttyp').on('click', function(){
					var val = $('input[name="flighttype"]:checked').val();
					if(val == '2'){
						$('.is_return').show();
						$('#ret_flight_id').attr('data-valid','required');
						$('input[name="ret_flight_number"]').attr('data-valid','required');
						$('input[name="ret_dep_time"]').attr('data-valid','required');
						$('input[name="ret_arv_time"]').attr('data-valid','required');
						
					}else{
							$('.is_return').hide();
							$('#ret_flight_id').attr('data-valid','');
						$('input[name="ret_flight_number"]').attr('data-valid','');
						$('input[name="ret_dep_time"]').attr('data-valid','');
						$('input[name="ret_arv_time"]').attr('data-valid','');
					}
				});
                $('#datetimepicker1').datetimepicker();
            });
        </script>
@endsection