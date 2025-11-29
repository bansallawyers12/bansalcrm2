@extends('layouts.dashboard_frontend')
@section('title', 'Change Password')
@section('content')      
<div class="row dashboard">
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading dashboard-main-heading">
				<h3 class="panel-title text-center">
					YOUR DASHBOARD
				</h3>
			</div>
			<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 no-padding">
				<!-- Emergency Note Start-->
					@include('../Elements/emergency')
				<!-- Emergency Note End-->
				
				<!-- Flash Message Start -->
				<div class="server-error">
					@include('../Elements/flash-message')	
				</div>
				<!-- Flash Message End -->
			
				<div class="panel-body">
					<div class="col-lg-12 col-sm-12 col-md-12 no-padding">
						<div class="tab" role="tabpanel">				
							<!-- Content Start for the Menu Bar Dashboard -->
								@include('../Elements/Frontend/navigation')
							<!-- Content End for the Menu Bar Dashboard -->	
						</div>
					</div>
				</div>
				
				<h3 class="order-summary"><strong>CHANGE PASSWORD</strong></h3>
				<div class="clearfix"></div>	
				<div class="panel-body">
					<div class="col-lg-6 col-sm-12 col-md-6 no-padding">
						<div class="tab" role="tabpanel">
							<div class="tab-content tabs">
								<div role="tabpanel" class="fade in active" id="Section0">		
									<div class="table-responsive">
										<div id="orderSummary_wrapper" class="dataTables_wrapper no-footer">
											{{ Form::open(array('url' => 'change_password', 'name'=>"change-password")) }}
												{{ Form::hidden('user_id', @Auth::user()->id) }}
												<div>
													<div class="form-group">
														<label for="old_password">Old Password<em>*</em></label>
														{{ Form::password('old_password', array('class' => 'form-control', 'data-valid'=>'required')) }}
													
														@if ($errors->has('old_password'))
															<span class="custom-error" role="alert">
																<strong>{{ $errors->first('old_password') }}</strong>
															</span>
														@endif
													</div>
													<div class="form-group">
														<label for="password">New Password<em>*</em></label>
														{{ Form::password('password', array('class' => 'form-control', 'data-valid'=>'required')) }}
													
														@if ($errors->has('password'))
															<span class="custom-error" role="alert">
																<strong>{{ $errors->first('password') }}</strong>
															</span>
														@endif
													</div>
													<div class="form-group">
														<label for="password_confirmation">Confirm Password<em>*</em></label>
														{{ Form::password('password_confirmation', array('class' => 'form-control', 'data-valid'=>'required')) }}
													
														@if ($errors->has('password_confirmation'))
															<span class="custom-error" role="alert">
																<strong>{{ $errors->first('password_confirmation') }}</strong>
															</span>
														@endif
													</div>
													<div class="form-group">
														{{ Form::button('Change', ['class'=>'btn btn-primary px-4', 'onClick'=>'customValidate("change-password")']) }}
													</div>
												</div>
											{{ Form::close() }}	
											
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
@endsection