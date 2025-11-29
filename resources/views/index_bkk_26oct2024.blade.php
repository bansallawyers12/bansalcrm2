@extends('layouts.frontend')
@section('seoinfo')
	<title><?php echo @\App\HomeContent::where('meta_key','meta_title')->first()->meta_value; ?></title>
	<meta name="description" content="<?php echo @\App\HomeContent::where('meta_key','meta_description')->first()->meta_value; ?>" />
	<link rel="canonical" href="<?php echo URL::to('/'); ?>" />
	<meta property="og:locale" content="en_US" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="<?php echo @\App\HomeContent::where('meta_key','meta_title')->first()->meta_value; ?>" />
	<meta property="og:description" content="<?php echo @\App\HomeContent::where('meta_key','meta_description')->first()->meta_value; ?>" />
	<meta property="og:url" content="<?php echo URL::to('/'); ?>" />
	<meta property="og:site_name" content="<?php echo @\App\ThemeOption::where('meta_key','site_name')->first()->meta_value; ?>" />
	<meta property="article:publisher" content="https://www.facebook.com/BANSALImmigration/" />
	<meta property="article:modified_time" content="2023-04-04T21:06:24+00:00" />
	<meta property="og:image" content="{{asset('public/img/bansal-immigration-icon.jpg')}}" />
	<meta property="og:image:width" content="200" />
	<meta property="og:image:height" content="200" />
	<meta property="og:image:type" content="image/jpeg" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:title" content="<?php echo @\App\HomeContent::where('meta_key','meta_title')->first()->meta_value; ?>" />
	<meta name="twitter:description" content="<?php echo @\App\HomeContent::where('meta_key','meta_description')->first()->meta_value; ?>" />
	<meta name="twitter:image" content="{{asset('public/img/bansal-immigration-icon.jpg')}}" />
	<meta name="twitter:site" content="@Bansalimmi" />
	<meta name="twitter:label1" content="Est. reading time" />
	<meta name="twitter:data1" content="6 minutes" />
@endsection
@section('content')

<?php $sliderstat = @\App\HomeContent::where('meta_key','sliderstatus')->first()->meta_value;
// dd($sliderstat);
if(@$sliderstat == 1){
 ?>
<section class="hero-area">
	<div class="hero-slides owl-carousel ">
		@foreach (@$sliderlists as $list)
		<div class="single-hero-slide" style="background-image:url({{asset('public/img/slider')}}/{{@$list->image}})">
			<div class="container h-100">
				<div class="row h-100 align-items-center"> 
					<div class="col-12 col-md-12">
						<div class="hero-slides-content text-center">
							<h1>Bansal Immigration Consultants</h1> 
							<h2 data-animation="fadeInUp" data-delay="400ms" style="text-shadow: 4px 4px 4px #000000;">{{@$list->title}}</h2>
							<h4 data-animation="fadeInUp" data-delay="400ms" style="text-shadow: 4px 4px 4px #000000;">{{@$list->subtitle}}</h4>
							<div class="slider_search">
								<form class="search_form" action="{{URL::to('/search_result')}}" method="get">
									<div class="search_field">
										<input type="text" class="form-control" placeholder="Search" name="search" />
										<input type="submit" class="search_btn" value="Search"/>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div> 
		</div>
	  @endforeach	
	</div>
</section>
<!-- ##### Hero Area End ##### -->
<?php } ?>
<!-- ##### About Area Start ##### -->
<section class="cryptos-about-area" style="background-color:#F7F7F7;display:none;">
	<div class="container">
		<div class="row align-items-center" style="padding-top:60px;">
			<div class="col-12 col-md-6">
				<div class="about-thumbnail mb-100">
					<img src="{{asset('public/img/Frontend/about.jpg') }}" style="border-radius:20px" alt="" />
				</div>
			</div>
			<div class="col-12 col-md-6">
				<div class="about-content mb-100"> 
					<div class="section-heading">
						<h3><span><?php echo @\App\HomeContent::where('meta_key','about_title')->first()->meta_value; ?></span></h3>
						<h6 style="line-height:26px"><?php echo @\App\HomeContent::where('meta_key','about_description')->first()->meta_value; ?></h6>
						<a href="<?php echo @\App\HomeContent::where('meta_key','about_link')->first()->meta_value; ?>" class="btn cryptos-btn mt-30">Read More</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- ##### About Area End ##### -->
<?php $whychoosestat = @\App\HomeContent::where('meta_key','whychoosestatus')->first()->meta_value;
if(@$whychoosestat == 1){
 ?>
 <!-- ##### Course Area Start ##### -->
<div class="cryptos-feature-area why_immigration_sec">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="section-heading text-center mx-auto">
					<h3><span>Why BANSAL Immigration?</span></h3>
				</div>
			</div>
		</div>
		<div class="row">
			<!-- Single Course Area -->
			@foreach (@$whychoosequerylists as $list)		
			<div class="col-12 col-md-6 col-xl-4 why_col3">
				<div class="why_box">	
					<div class="why_icon">
						<i class="fa fa-{{ @$list->icon }}"></i>
					</div>
					<div class="why_content">					
						<h4>{{@$list->title}}</h4>
						<p>{{@$list->description}}</p>
					</div>
				</div>
			</div>
			@endforeach
		</div>
	</div>
</div>
<!-- ##### Course Area End ##### -->
<?php } ?>
<?php $servicestat = @\App\HomeContent::where('meta_key','servicestatus')->first()->meta_value;
if(@$servicestat == 1){
 ?>
<!--================Project Area =================-->
<section class="services_area" style="padding-top:40px; padding-bottom:30px; background-image:url({{ asset('public/img/Frontend/projectback.jpg')}})">
	<div class="container">
		<h2 class="text-center"><span style="color:#fff">SERVICES WE OFFER</span></h2>
		<p class="text-center" style="color:#fff">We provide the following services at  <a style="color:#ffaf02" href="<?php echo URL::to('/'); ?>">Bansal Immigration Consultants</a></p>
		<div class="inner_service"> 
			<div class="service_row"> 
				@foreach (@$servicelists as $list)				
				<div class="service_col">
					<div class="service_item">  
						<div class="service_icon"><i class="fa fa-{{ @$list->serv_icon }}"></i></div>
						<h4>{{@$list->title}}</h4>
						<p>{{@$list->short_description}}</p> 
						<a class="readmore" href="<?php echo URL::to('/'); ?>/ourservices/{{@$list->slug}}">Read More</a>
					</div>
				</div>
				<!--<div class="service_col service_empty_col"></div>-->
				@endforeach
			</div> 
			<div class="text-center">
				<a href="<?php echo URL::to('/'); ?>/ourservices" class="btn cryptos-btn mt-30">View All</a>
			</div>
		</div>
	</div>
</section>
<!--================End Project Area =================-->
<?php } ?>

<!--==========================
      Testimonials Section
    ============================-->
<section id="testimonials" class="wow fadeInUp" style="background-image:url({{asset('public/img/Frontend/testback.jpg')}})">
	<div class="container">
		<div class="row">
			<?php $testmioalstat = @\App\HomeContent::where('meta_key','testimonialstatus')->first()->meta_value;
			if(@$testmioalstat == 1){
			 ?>
			<div class="col-lg-4 col-md-4">
				<div class="section-header">
					<h3><span>TESTIMONIALS</span></h3>
				</div>
				<div class="owl-carousel testimonials-carousel">
					@foreach (@$testimoniallists as $list)
					<div class="testimonial-item" style="background-color:#FFFFFF">
						<img src="{{asset('public/img/Frontend/quote-sign-left.png')}}" class="quote-sign-left" alt="">
						<p>{{@$list->description}}</p>
						<img src="{{asset('public/img/Frontend/quote-sign-right.png')}}" class="quote-sign-right" alt="">
						<img src="{{asset('public/img/testimonial_img')}}/{{@$list->image}}" class="testimonial-img" alt="{{@$list->name}}">
						<p><b>{{@$list->name}}</b></p>
					</div>
					@endforeach
				</div>
				<div class="text-center">
					<a href="<?php echo URL::to('/'); ?>/testimonials" class="btn cryptos-btn view_all_btn">View All</a>
				</div>
			</div>
			<?php } ?>

			<div class="col-lg-1 col-md-1"></div>

			<div class="col-lg-7 col-md-7">
				<div class="section-header">
					<h3><span>VIDEOS</span></h3>
				</div>
				<table id="Table1" cellspacing="0" align="Justify" style="font-family:Arial;font-size:11pt;font-weight:normal;width:100%;border-collapse:collapse;">
					<tr>
						<td style="border-color:#A9834B;border-width:0px;border-style:Outset;">
							<table id="DataList25" cellspacing="0" align="Justify" style="font-family:Arial;font-size:11pt;font-weight:normal;width:100%;border-collapse:collapse;">
								<tr>
									<td style="border-color:#A9834B;border-width:0px;border-style:Outset;">
										<iframe width="100%" height="430" src="<?php echo @\App\HomeContent::where('meta_key','video_link')->first()->meta_value; ?>" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</section>

<!-- ##### Currency Area Start ##### -->
<section class="currency-calculator-area section-padding-50 bg-img bg-overlay" style="background-image: url({{ asset('public/img/Frontend/bg-2.jpg') }});">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-heading text-center white mx-auto">
                    <h3><strong>REQUEST A CALL BACK</strong></h3>
                    <h5 class="mb-2">Get the right guidance about complex immigration situations from Registered Migration Agents</h5>
                </div>
            </div>
        </div>

        <div class="form_area cus_form_area"> 
            <form class="contact_form" action="{{ url('/contact') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fullname">Name</label>
                            <input type="text" class="form-control" placeholder="Name" name="fullname" required/>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" placeholder="Email" name="email" required/>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" class="form-control" placeholder="Phone" name="phone" required/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="subject">Your Query</label>
                            <input type="text" class="form-control" placeholder="Write Query" name="subject" required/>
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea class="form-control" placeholder="Message" name="message" required></textarea>
                        </div>
                        <div class="capcha_code" style="margin-bottom:20px;">
                            <label>Verify Code:</label>
                            <div class="code_verify">
                                <div class="image">
                                    <?php echo $captcha; ?>
                                </div>
                            </div>
                            <div class="code_refresh">
                                @if ($errors->has('captcha'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('captcha') }}</strong>
                                    </span>
                                @endif
                                <input type="text" name="captcha" id="captcha" required="required" class="form-control cap1" placeholder="Enter Code Here" min="6" maxlength="6" autocomplete="off"/>
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
</section>

<!-- Success Modal -->
<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalMessage">
                Your request has been submitted successfully!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


 
<!-- ##### Currency Area End ##### -->

<?php $blogstat = @\App\HomeContent::where('meta_key','blogstatus')->first()->meta_value;
if(@$blogstat == 1){
 ?>
<!-- ##### Blog Area Start ##### -->
<section class="cryptos-blog-area blog_sec">
	<div class="container">
		<h3 class="text-center"><span>RECENT BLOGS</span></h3>
		<div class="col-12">
			<div class="row"> 
			<!-- Single Course Area -->
				@foreach (@$bloglists as $list)
				<div class="col-12 col-sm-6 col-lg-4">
					<div class="single-feature-area style-2 mb-4">
						@if($list->image)
							<a href="<?php echo URL::to('/'); ?>/{{@$list->slug}}"><img src="https://bansalcrm.com/public/img/blog/{{@$list->image}}"></img></a> 
						@endif
						<h4>{{@$list->title}}</h4>
						<p>{{@$list->short_description}}</p>
						<a href="<?php echo URL::to('/'); ?>/{{@$list->slug}}">Read More</a>
					</div>
				</div>
				@endforeach
			</div>
		</div>
		<div class="text-center">
			<a href="<?php echo URL::to('/'); ?>/blogs" class="btn cryptos-btn view_all_btn">View All</a>
		</div>
	</div>
</section>
<!-- ##### Blog Area End ##### -->
<?php } ?>

<section class="meet_us">
	<div class="container">
		<div class="row align-items-center">
			<!--<div class="col-12 col-md-4">
				<div class="about-thumbnail">
					<img src="{{--asset('public/img/media_gallery/')--}}/{{--@\App\HomeContent::where('meta_key','meet_image_1')->first()->meta_value--}}" style="border-radius:20px" alt="" />
					<h3><?php //echo @\App\HomeContent::where('meta_key','meet_name_1')->first()->meta_value; ?></h3>
					<h5><?php //echo @\App\HomeContent::where('meta_key','meet_profession_1')->first()->meta_value; ?></h5>
				</div>
			</div>-->
          
            <div class="col-12 col-md-4">
				<div class="about-thumbnail" style="margin-bottom: 145px;">
					<img src="{{asset('public/img/media_gallery/')}}/{{@\App\HomeContent::where('meta_key','meet_image_2')->first()->meta_value}}" style="border-radius:20px" alt="" />
					<h3><?php echo @\App\HomeContent::where('meta_key','meet_name_2')->first()->meta_value; ?></h3>
					<h5><?php echo @\App\HomeContent::where('meta_key','meet_profession_2')->first()->meta_value; ?></h5>
				</div>
			</div>
          
			<div class="col-12 col-md-8">
				<div class="about-content"> 
					<div class="section-heading">
						<h3><span><?php echo @\App\HomeContent::where('meta_key','meet_title')->first()->meta_value; ?></span></h3>
						
                      <!--<p><?php //echo @\App\HomeContent::where('meta_key','meet_description')->first()->meta_value; ?></p>-->
                      
                        <p style="padding-bottom: 1em;box-sizing: border-box;font-size: 15px;">
                            <em>
                                “Guiding you through migration is more than just paperwork - it's about building a future you can thrive in.”
                                <strong>Arun Bansal</strong>
                            </em>
                            <!--<p></p>-->
                            <p style="padding-bottom: 1em;box-sizing: border-box;font-size: 15px;">At <strong>Bansal Immigration Consultants</strong>, we are committed to shaping your dreams of living and prospering in Australia. Led by <strong>Arun Bansal</strong> (MARN: 2418466), our mission goes beyond just visa approvals - we aim to empower individuals by providing them with the best pathway to success.</p>
                            <p style="padding-bottom: 1em;box-sizing: border-box;font-size: 15px;">With <strong>over a decade of experience</strong>, our expert MARN Agent Mr. Arun Bansal has helped countless clients transform their Australian dreams into reality. Whether you're seeking general skilled migration, education consultation, or other visa solutions, our team is committed to delivering tailored services with unmatched professionalism and care.</p>
                            <p style="box-sizing: border-box;font-size: 15px;">As<strong> Melbourne's trusted experts</strong>, located on Collins Street, we offer you guidance, clarity, and unwavering support every step of the way. Trust us to navigate the complexities of migration and education in Australia with you, ensuring your success, just as we have for so many others.</p>
                            <!--<p style="line-height: 150%;">
                                <span style="color: var( --e-global-color-text ); font-family: var( --e-global-typography-text-font-family ), Sans-serif; font-size: 14px; font-weight: var( --e-global-typography-text-font-weight );"></span>
                            </p>-->
                        </p>
                      
						<a href="<?php echo URL::to('/'); ?>/<?php echo @\App\HomeContent::where('meta_key','meet_link')->first()->meta_value; ?>" class="btn cryptos-btn contact_btn">Contact Us</a>
					</div>
				</div> 
			</div>
			
		</div>
	</div>
</section>
@endsection 

@section('scripts')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
    @if ($message = Session::get('success'))
        $(document).ready(function() {
            $('#modalMessage').text("{{ $message }}"); // Set the success message
            $('#successModal').modal('show'); // Show the modal
        });
    @endif
</script>
@endsection



