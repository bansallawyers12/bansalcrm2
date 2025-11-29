@extends('layouts.admin')
@section('title', 'Multi Factor Authentication')
@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">Multi Factor Authentication</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Multi Factor Authentication</li>
					</ol>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.container-fluid -->
	</div>
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
			</div>	
			<div class="row">
				<div class="col-sm-12">
					<div class="card card-primary multi_factor"> 
						<div class="card-header"> 
							<h3 class="card-title">MFA Modes</h3>
							<p>Use multi-factor authentication (MFA) to add an extra layer of security to your account.</p> 
							<div class="factor_label">
								<input value="1" type="checkbox" name="is_active" checked data-bootstrap-switch>
							</div>
						</div> 
						<div class="card-body">							
							<div class="row">  
								<div class="col-sm-6">
									<div class="mobile_based_otp custom_otp">
										<h4>Mobile-based OTP</h4> 
										<div class="mobile_field">
											<div class="mobile_icon">
												<i class="fa fa-phone"></i>
											</div>
											<div class="mobile_info">
												<div class="mobile_text">(+91) 90******65</div>
												<div class="mob_time" id="mob_time">5 years ago</div>
												<div class="mob_indicator">
													<div class="pri_indicator">PRIMARY</div>
												</div>
											</div>
										</div>
										<div class="add_view_btn">
											<a href="javascript:;"><i class="fa fa-eye"></i> View More</a>
											<a href="javascript:;"><i class="fa fa-plus"></i> Add Phone</a>
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