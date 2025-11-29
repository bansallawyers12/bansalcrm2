@extends('layouts.frontend')
@section('seoinfo')
	<title>Contact Us (03) 9602 1330 For Education Visa &amp; Migration Visa Services</title>
	<meta name="description" content="Contact us on (03) 9602 1330 for all your education visa &amp; migration visa services Or visit our Collins Street Education &amp; Migration Hub Office today." />
	<link rel="canonical" href="<?php echo URL::to('/'); ?>/contact/" />
	<meta property="og:locale" content="en_US" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="Contact |" />
	<meta property="og:description" content="| Contact" />
	<meta property="og:url" content="<?php echo URL::to('/'); ?>/contact/" />
	<meta property="og:site_name" content="Bansal Immigration - Your Future, Our Priority" />
	<meta property="article:publisher" content="https://www.facebook.com/BANSALImmigration/" />
	<meta property="article:modified_time" content="2021-08-30T09:05:04+00:00" />
	<meta property="og:image" content="<?php echo URL::to('/'); ?>/public/img/bansal-immigration-icon.jpg" />
	<meta property="og:image:width" content="200" />
	<meta property="og:image:height" content="200" />
	<meta property="og:image:type" content="image/jpeg" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:site" content="@Bansalimmi" />
	<meta name="twitter:label1" content="Est. reading time" />
	<meta name="twitter:data1" content="3 minutes" />
@endsection
@section('content')

<section class="custom_breadcrumb bg-img bg-overlay" style="background-image: url({!! asset('public/img/Frontend/bg-2.jpg') !!}); padding-top:40px">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="section-heading text-center mx-auto">
					<div class="section-header">
						<h3>CONTACT US</h3>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<div class="container" style="padding-top:40px; padding-bottom:40px">
	<div class="row">
		<div class="col-sm-4">
			<div class="section-header">
				<h3><span>CONTACT US</span></h3>
			</div>
			<div class="contact_info">
				<h5>Bansal Immigration Consultants</h5>
				<p><i class="fa fa-map-marker"></i> Bansal Immigration, Next to flight Center, Level 8/278 Collins St, Melbourne VIC 3000, Australia</p>
				<!--<p><i class="fa fa-map-marker"></i> 1A Bald Hill Road, Pakenham VIC 3810</p>-->
				<!--<p><i class="fa fa-map-marker"></i> Bansal Immigration 701/343 Little Collins St, Melbourne, VIC 3000</p>-->
				<!-- <p><i class="fa fa-map-marker"></i> 44D Partap Nagar, Near 24 Number Phatak, Patiala Punjab India 147001</p> -->
				<!--<p><i class="fa fa-phone"></i> <a href="tel:0429898915">0429898915</a></p>-->
                <p><i class="fa fa-phone"></i> <a style="font-size: 30px;" href="tel:0406660960">0406660960</a></p>
				<p><i class="fa fa-envelope"></i> <a style="font-size: 20px;" href="mailto:info@bansalimmigration.com.au">info@bansalimmigration.com.au</a></p>
			</div>
		</div>
		<div class="col-sm-8" style="width:100%">
			<div class="section-header"> 
				<h3><span>SEND ENQUIRY</span></h3>
			</div>
				@if ($message = Session::get('success'))
					<div class="alert alert-success">
						<p>{{ $message }}</p>
					</div>
				@endif  
			<div class="contact_form_area cus_form_area">
				<form class="contact_form" action="<?php echo URL::to('/'); ?>/contact" method="post" enctype="multipart/form-data">
				@csrf
				<div class="row"> 
						<div class="col-md-6">
							<div class="form-group">
								<label for="fullname">Name</label>
								<input type="text" class="form-control" placeholder="Name" name="fullname" required="required" />
							</div>
							<div class="form-group">
								<label for="email">Email</label>
								<input type="email" class="form-control" placeholder="Email" name="email" required="required"/>
							</div>
							<div class="form-group">
								<label for="phone">Phone</label>
								<input type="text" class="form-control" placeholder="Phone" name="phone" required="required"/>
							</div>
							<div class="form-group">
								<label for="subject">Your Query</label>
								<input type="text" class="form-control" placeholder="Write Query" name="subject" required="required"/>
							</div>
						</div>
						<div class="col-md-6">
							<!--<div class="form-group">
								<label for="attachment">Attachment</label>
								<input type="file" class="form-control" name="attachment" />
							</div>-->
							<div class="form-group">
								<label for="message">Message</label>
								<textarea class="form-control" placeholder="Message" name="message" required="required"></textarea>
							</div>
							<div class="capcha_code" style="margin-bottom:20px;">
								<label>Verify Code:</label>
								<div class="code_verify">
									<div class="image ">
										<?php echo $captcha; ?>
									</div> 
								</div>
								<div class="code_refresh">
									@if ($errors->has('captcha'))
										<span class="invalid-feedback" role="alert">
											<strong>{{ $errors->first('captcha') }}</strong>
										</span>
									@endif
									<input type="text" name="captcha" id="captcha" required="required" class="form-control cap1" placeholder="Enter Code Here" value="" onkeyup="IsChkNumc(this);" onchange="IsChkNumc(this);" type="text" min="6" maxlength="6" autocomplete="off"/>
									<a class="refresh" href="javascript:;"><i class="fa fa-refresh"></i></a>											
								</div>
								<div class="clearfix"></div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-btn">
								<input type="submit" class="btn" name="submit" value="Submit" />
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div> 
@endsection 