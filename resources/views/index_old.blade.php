@extends('layouts.dashboard_frontend')
@section('title', @$seoDetails->meta_title)
@section('meta_title', @$seoDetails->meta_title)
@section('meta_keyword', @$seoDetails->meta_keyword)
@section('meta_description', @$seoDetails->meta_desc)
@section('content')


<div class="search_product_wrapper">
	<div class="container">
		<div class="inner_search">
			<h3>Search for products & find <b>verified sellers</b> near you</h3>
			<div class="search_area disflex_center">
				<div class="search_dropdown">
					<button class="dropbtn" id="searchPlacebtn"><i class="fa fa-map-marker"></i><span id="usercity">All India</span><i class="fa fa-angle-down pull-right"></i></button>
					<div id="searchPlaces" class="dropdown-content">
						 <input type="text" placeholder="Search your city" id="searchPlaceInput" class="search_field" autocomplete="off" />
					</div>
				</div>
				<input type="text" id="search-input" placeholder="Enter product/service name" name="search_s" class="ui-autocomplete-input" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true"/>
				<button id="searchBtn"><i class="fa fa-search"></i> Search</button>
			</div>
		</div>
	</div>
</div>

<div class="service_sec ptb-40">
	<div class="container">
		<div class="row">
			<div class="col-md-8 offset-md-2 text-center">
				<div class="cus_title" data-aos="fade-up" data-aos-duration="1500">
					<h2>Our Services</h2>
				</div>
				<div class="para_txt" data-aos="fade-up" data-aos-duration="1800">
					<p>Consectetur adipisicing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua enim aden a minim veniam quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat</p>
				</div>
			</div>
		</div>
		<div class="service_list">
			<div class="row">
				<div class="col-md-3 serv_col" data-aos="fade-up" data-aos-duration="500">
					<div class="serv_item serv1">
						<div class="serv_icon">
							<img src="{{URL::asset('public/images/services/graphic-design.png')}}" alt="Grahic & Design" />
						</div>
						<div class="serv_title">
							<h5>Grahic & Design</h5>
						</div>
						<a href="#"><i class="ti-arrow-right"></i></a>
					</div>
				</div>
				<div class="col-md-3 serv_col" data-aos="fade-up" data-aos-duration="1000">
					<div class="serv_item serv2">
						<div class="serv_icon">
							<img src="{{URL::asset('public/images/services/programming-tech.png')}}" alt="Programming & Tech" />
						</div>
						<div class="serv_title">
							<h5>Programming & Tech</h5>
						</div>
						<a href="#"><i class="ti-arrow-right"></i></a>
					</div>
				</div>
				<div class="col-md-3 serv_col" data-aos="fade-up" data-aos-duration="1500">
					<div class="serv_item serv3">
						<div class="serv_icon">
							<img src="{{URL::asset('public/images/services/digital-marketing.png')}}" alt="Digital Marketing" />
						</div>
						<div class="serv_title">
							<h5>Digital Marketing</h5>
						</div>
						<a href="#"><i class="ti-arrow-right"></i></a>
					</div>
				</div>
				<div class="col-md-3 serv_col" data-aos="fade-up" data-aos-duration="2000">
					<div class="serv_item serv4">
						<div class="serv_icon">
							<img src="{{URL::asset('public/images/services/IT-training.png')}}" alt="IT Training" />
						</div>
						<div class="serv_title">
							<h5>IT Training</h5>
						</div>
						<a href="#"><i class="ti-arrow-right"></i></a>
					</div> 
				</div>
				<div class="col-md-3 serv_col" data-aos="fade-up" data-aos-duration="500">
					<div class="serv_item serv5">
						<div class="serv_icon">
							<img src="{{URL::asset('public/images/services/carpenter.png')}}" alt="Carpenter" />
						</div>
						<div class="serv_title">
							<h5>Carpenter</h5>
						</div>
						<a href="#"><i class="ti-arrow-right"></i></a>
					</div>
				</div>
				<div class="col-md-3 serv_col" data-aos="fade-up" data-aos-duration="1000">
					<div class="serv_item serv6">
						<div class="serv_icon">
							<img src="{{URL::asset('public/images/services/plumber.png')}}" alt="Plumber" />
						</div>
						<div class="serv_title">
							<h5>Plumber</h5>
						</div>
						<a href="#"><i class="ti-arrow-right"></i></a>
					</div>
				</div>
				<div class="col-md-3 serv_col" data-aos="fade-up" data-aos-duration="1500">
					<div class="serv_item serv7">
						<div class="serv_icon">
							<img src="{{URL::asset('public/images/services/electrician.png')}}" alt="Electrician" />
						</div>
						<div class="serv_title">
							<h5>Electrician</h5>
						</div>
						<a href="#"><i class="ti-arrow-right"></i></a>
					</div>
				</div>
				<div class="col-md-3 serv_col" data-aos="fade-up" data-aos-duration="2000">
					<div class="serv_item serv8">
						<div class="serv_icon">
							<img src="{{URL::asset('public/images/services/mason.png')}}" alt="Mason" />
						</div>
						<div class="serv_title">
							<h5>Mason</h5>
						</div>
						<a href="#"><i class="ti-arrow-right"></i></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="provider_join">
	<div class="join_opacity">
		<div class="container">
			<div class="row cont_justify">
				<div class="col-md-10 col_10">
					<div class="provider_content">
						<div class="provider_descirption">
							<h3>Join Our Service Provider</h3>
							<p>Lorem ipsum dolor sit amet, consecte adipiscing elit sed do eiusmod tempor</p>
						</div>
						<div class="provider_btn">
							<a class="cus_btn btn_medium brder_rad50 btn_translate" href="#">Register Now</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="repair_maintance ptb-40">
	<div class="container">
		<div class="row">
			<div class="col-md-8 offset-md-2 text-center">
				<div class="cus_title" data-aos="fade-up" data-aos-duration="1500">
					<h2>Home Repairs & Maintenance</h2>
				</div>
				<div class="para_txt" data-aos="fade-up" data-aos-duration="1800">
					<p>Consectetur adipisicing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua enim aden a minim veniam quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat</p>
				</div> 
			</div>
		</div>
		<div class="maintance_list">
			<div class="row disflex_center">
				<div class="col-md-4 custom_col3" data-aos="fade-up" data-aos-duration="500">
					<div class="box_item">
						<div class="rp_icon style_gradient">
							<i aria-hidden="true" class="jki jki-brickwall-light"></i>
						</div>
						<div class="box_body">
							<h4>Mason</h4>
							<p>Lorem ipsum dolor sit amet, consecte adipiscing elit sed do eiusmod tempor</p>
						</div>
						<div class="hover_watermark">
							<i aria-hidden="true" class="jki jki-brickwall-light"></i>
						</div>
						<div class="hover_link">
							<a href="javascript:;" data-bs-toggle="modal" data-bs-target="#searchModal"><i aria-hidden="true" class="jki jki-arrow-right-solid"></i></a>
						</div>
					</div>
				</div>
				<div class="col-md-4 custom_col3" data-aos="fade-up" data-aos-duration="1000">
					<div class="box_item">
						<div class="rp_icon style_gradient">
							<i aria-hidden="true" class="jki jki-bolt-solid"></i>
						</div>
						<div class="box_body">
							<h4>Electrician</h4>
							<p>Lorem ipsum dolor sit amet, consecte adipiscing elit sed do eiusmod tempor</p>
						</div>
						<div class="hover_watermark">
							<i aria-hidden="true" class="jki jki-bolt-solid"></i>
						</div>
						<div class="hover_link">
							<a href="#"><i aria-hidden="true" class="jki jki-arrow-right-solid"></i></a>
						</div>
					</div>
				</div>
				<div class="col-md-4 custom_col3" data-aos="fade-up" data-aos-duration="1500">
					<div class="box_item">
						<div class="rp_icon style_gradient">
							<i aria-hidden="true" class="jki jki-wrench-solid"></i>
						</div>
						<div class="box_body">
							<h4>Plumber</h4>
							<p>Lorem ipsum dolor sit amet, consecte adipiscing elit sed do eiusmod tempor</p>
						</div>
						<div class="hover_watermark">
							<i aria-hidden="true" class="jki jki-wrench-solid"></i>
						</div>
						<div class="hover_link">
							<a href="#"><i aria-hidden="true" class="jki jki-arrow-right-solid"></i></a>
						</div>
					</div>
				</div>
				<div class="col-md-4 custom_col3" data-aos="fade-up" data-aos-duration="2000">
					<div class="box_item">
						<div class="rp_icon style_gradient">
							<i aria-hidden="true" class="jki jki-hammer-solid"></i>
						</div>
						<div class="box_body">
							<h4>Carpenter</h4>
							<p>Lorem ipsum dolor sit amet, consecte adipiscing elit sed do eiusmod tempor</p>
						</div>
						<div class="hover_watermark">
							<i aria-hidden="true" class="jki jki-hammer-solid"></i>
						</div>
						<div class="hover_link">
							<a href="#"><i aria-hidden="true" class="jki jki-arrow-right-solid"></i></a>
						</div>
					</div>
				</div>
				<div class="col-md-4 custom_col3" data-aos="fade-up" data-aos-duration="2500">
					<div class="box_item">
						<div class="rp_icon style_gradient">
							<i aria-hidden="true" class="jki jki-paint-roller-solid"></i>
						</div>
						<div class="box_body">
							<h4>Painter</h4>
							<p>Lorem ipsum dolor sit amet, consecte adipiscing elit sed do eiusmod tempor</p>
						</div>
						<div class="hover_watermark">
							<i aria-hidden="true" class="jki jki-paint-roller-solid"></i>
						</div>
						<div class="hover_link">
							<a href="#"><i aria-hidden="true" class="jki jki-arrow-right-solid"></i></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="contact_support">
	<div class="support_opacity"></div>
	<div class="container">
		<div class="row cont_justify">
			<div class="col-md-10 col_10">
				<div class="support_content">
					<div class="support_descirption">
						<h3>Recommend a <b>Service</b> in <b>Your City</b></h3>
						<p>Lorem ipsum dolor sit amet, consecte adipiscing elit sed do eiusmod tempor</p>
						<a class="cus_btn btn_medium brder_rad50 btn_translate" href="#">Get Inquiry</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection