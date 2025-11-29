@extends('layouts.frontend')
@section('seoinfo')
	<title>Book An  Appointment | Bansal Immigration-Your Future, Our Priority</title>
	<meta name="description" content="| Book An  Appointment" />
	<link rel="canonical" href="<?php echo URL::to('/'); ?>/book-an-appointment/" />
	<meta property="og:locale" content="en_US" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="Book An  Appointment | Bansal Immigration-Your Future, Our Priority" />
	<meta property="og:description" content="| Book An  Appointment" />
	<meta property="og:url" content="<?php echo URL::to('/'); ?>/book-an-appointment/" />
	<meta property="og:site_name" content="Bansal Immigration - Your Future, Our Priority" />
	<meta property="article:publisher" content="https://www.facebook.com/BANSALImmigration/" />
	<meta property="article:modified_time" content="2021-08-30T09:05:04+00:00" />
	<meta property="og:image" content="{{asset('public/img/bansal-immigration-icon.jpg')}}" />
	<meta property="og:image:width" content="200" />
	<meta property="og:image:height" content="200" />
	<meta property="og:image:type" content="image/jpeg" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:site" content="@Bansalimmi" />
	<meta name="twitter:label1" content="Est. reading time" />
	<meta name="twitter:data1" content="3 minutes" />
@endsection
@section('content')
<style>
.timeslots .timeslot_col.active{border:1px solid #0062cc;background-color:#fff;margin: 0px 10px 8px 0px;}
#preloaderbook {
	display:none;
    background: #0d104d;
    background: -webkit-linear-gradient(to right, #0d104d, #28408b);
    background: linear-gradient(to right, #0d104d, #28408b);
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    z-index: 5000;
}
#preloaderbook .circle-preloader {
    display: block;
    width: 60px;
    height: 60px;
    border: 2px solid rgba(255, 255, 255, 0.5);
    border-bottom-color: #ffffff;
    border-radius: 50%;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    margin: auto;
    animation: spin 2s infinite linear;
}
</style>
<section class="custom_breadcrumb bg-img bg-overlay" style="background-image: url({{asset('public/img/Frontend/bg-2.jpg')}}); padding-top:40px">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="section-heading text-center mx-auto">
					<div class="section-header">
						<h3>Book An  Appointment</h3>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="custom_inner_page appointment_page" section-padding-5-0="" style="background-color:#F7F7F7;">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-12 col-md-12">
				<div class="cus_inner_content">
					<h1><strong>Important information regarding booking online appointment:</strong></h1>
					<ol>
						<li><strong>15 mins free consultation: for in person meetings to discuss your immigration matters. We encourage, if your inquiry is related to Australian PR, please select the PR appointment option and provide required information. </strong></li>
						<li><strong>10 mins free phone consultations: We are also offering 10 mins free phone consultation. It can be related to any immigration matter, however, due to time constraint and the information required to give advice, we do not provide PR advice over the phone within this option.</strong></li>
						<li><strong>PR appointment: if you need information about your Australian PR, then kindly choose this option and provide details. At this stage, we are not charging for PR appointments. This type of option is only available for in person meetings only. If you want over the phone, kindly choose the last Migration advice option and pay the required charges.</strong></li>
						<li><strong>Migration Advice: our charges are $110 for most type of migration advice. Please select this option, if your matter is complicated and will require more than 15 mins or you want PR advice over the phone.</strong></li>
					</ol>
					<p><strong>Disclaimer 1: If you <span style="text-decoration: underline; color: #000000;">miss the scheduled appointment or phone call</span>, you will lose your opportunity of free consultation.</strong></p>
					<p><strong>Disclaimer 2: Please note appointment service is only for clients who are in Australia, if you are</strong><span style="text-decoration: underline;"><strong> <span style="color: #000000; text-decoration: underline;">outside Australia</span> </strong></span><strong>or</strong><span style="text-decoration: underline;"><strong> <span style="color: #000000; text-decoration: underline;">inquiring about someone outside Australia</span></strong></span><strong>, kindly email your details and we will get in touch with you.</strong></p>
					<p><strong>If you are unsure, you can give our friendly staff a call on <span style="text-decoration: underline; color: #000000;">03 9602 1330</span> and they can book the appointment for you or assist you to choose the right option.</strong></p>
				</div>

				<div class="appointment_form_tabs">
					<form class="contact_form" id="appintment_form" action="<?php echo URL::to('/'); ?>/book-an-appointment/store" method="post" enctype="multipart/form-data">

                        <div class="cus_tab_form">
							<ul class="nav nav-tabs" id="myTab" role="tablist">
								<li class="tab_logo">
									<a href="#">
										<img src="{{asset('public/img/logo_img/bansal-imm-logo-11_vrUFM77pu7.png')}}" alt="" />
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link active" id="nature_of_enquiry-tab" data-toggle="tab" href="#nature_of_enquiry" role="tab" aria-controls="nature_of_enquiry" aria-selected="true">Nature of Enquiry</a>
								</li>
								<li class="nav-item">
									<a class="nav-link disabled" id="services-tab" data-toggle="tab" href="#services" role="tab" aria-controls="services" aria-selected="true">Services</a>
								</li>
								<li class="nav-item">
									<a class="nav-link disabled" id="appointment_details-tab" data-toggle="tab" href="#appointment_details" role="tab" aria-controls="appointment_details" aria-selected="true">Appointment Details</a>
								</li>
								<li class="nav-item">
									<a class="nav-link disabled" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="true">Information</a>
								</li>
								<!--<li class="nav-item">
									<a class="nav-link disabled" id="datetime-tab" data-toggle="tab" href="#datetime" role="tab" aria-controls="datetime" aria-selected="false">Date & Time</a>
								</li>-->
								<li class="nav-item">
									<a class="nav-link disabled" id="confirm-tab" data-toggle="tab" href="#confirm" role="tab" aria-controls="confirm" aria-selected="false">Confirmation</a>
								</li>
							</ul>


                            <div class="col-12 col-md-6 col-lg-6" style="margin-left: 10px;">

                                <div class="row">
                                    <div class="form-group">
                                        <label for="noe_id">Nature of Enquiry </label>
                                        <select data-valid="" class="form-control enquiry_item" name="noe_id">
                                            <option value="">Select</option>
                                            @foreach(\App\NatureOfEnquiry::where('status',1)->get() as $enquiry)
                                                <option value="{{$enquiry->id}}">{{$enquiry->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row services_row" style="display: none;">
                                    <div class="form-group">
                                         <label for="service_id">Services</label>
                                         <select data-valid="" class="form-control services_item" name="service_id">
                                             <option value="">Select</option>
                                             @foreach(\App\BookService::where('status',1)->get() as $bookservices)
                                                 <option value="{{$bookservices->id}}">{{$bookservices->title}} {{$bookservices->duration}} minutes {{$bookservices->price}}</option>
                                             @endforeach
                                         </select>
                                     </div>
                                </div>

                                <div class="row appointment_row" style="display: none;">
                                    <div class="form-group">
                                        <label for="appointment_details">Appointment details</label>
                                        <select data-valid="" class="form-control appointment_item" name="appointment_details">
                                            <option value="">Select</option>
                                            <option value="phone"> Phone</option>
                                            <option value="in_person">In person</option>
                                        </select>
                                     </div>
                                </div>

                                <div class="row info_row" style="display: none;">
                                    <div class="tab_header">
										<h4>Fill Information</h4>
									</div>
									<form id="fromtopupvalues">
                                        <div class="tab_body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="fullname">Full Name</label>
                                                        <input type="text" class="form-control fullname" placeholder="Enter Name" name="fullname" />
                                                        <input type="hidden" class="form-control " placeholder="" name="noe_id" />
                                                        <input type="hidden" class="form-control " placeholder="" name="service_id" />
                                                        <input type="hidden" class="form-control " placeholder="" name="date" />
                                                        <input type="hidden" class="form-control " placeholder="" name="time" />
                                                        <input type="hidden" class="form-control " placeholder="" name="appointment_details" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="email">Email</label>
                                                        <input id="email" type="email" class="form-control email" placeholder="Enter Email" name="email" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="phone">Phone</label>
                                                        <input id="phone" type="text" class="form-control phone" placeholder="Enter Phone" name="phone" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="title">Title</label>
                                                        <input type="text" class="form-control title" placeholder="Enter Title" name="title" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="description">Description</label>
                                                        <textarea class="form-control description" placeholder="Enter Description" name="description"></textarea>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="description">Date & Time</label>

                                                        <div style="width:150%;height:205px;">
                                                            <div style="width:30%">
                                                                <div id='datetimepicker' style="height: 210px;margin-left: 15px;"></div>
                                                            </div>
                                                            <div style="width:65%;margin-left: 265px;margin-top: -212px;">
                                                                <div class="showselecteddate" style="font-size: 14px;text-align: center; padding: 5px 0 3px;border-bottom: 1px solid #E3EAF3;color: #0d0d0f !important;font-weight: bold;"></div>
                                                                <div class="timeslots" style="overflow:scroll !important;height:185px;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-btn text-right">
                                                        <input type="button" class="btn btn-primary  nextbtn" style="margin-bottom:5px;" data-steps="confirm" name="submit" value="Confirm" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
									</form>
                                </div>


                                <div class="row confirm_row" style="display: none;">
                                    <div class="tab_header">
										<h4>Confirm Details</h4>
									</div>
									<div class="tab_body">
                                        <div class="row">
											<div class="col-md-12">
												<div class="table-responsive" style="width: 830px !important;">
													<table class="table table-bordered table-striped">
														<thead>
															<tr>
																<th>Full Name</th>
																<th>Email</th>
																<th>Phone</th>
																<th>Title</th>
																<th>Description</th>
																<th>Date</th>
																<th>Time</th>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td class="full_name"></td>
																<td class="email"></td>
																<td class="phone"></td>
																<td class="title"></td>
																<td class="description"></td>
																<td class="date"></td>
																<td class="time"></td>
															</tr>
														</tbody>
													</table>
												</div>
												<ul class="errors">
												</ul>
												<!--<div class="form-btn text-left">
													<input type="button" class="btn prevbtn" name="submit" value="Back" data-steps="datetime" />
												</div>-->
												<div class="form-btn text-center">
													<input type="button" class="btn btn-primary  submitappointment" style="margin-bottom:5px;" name="submit" value="Submit" />
												</div>
											</div>
										</div>
									</div>
                                </div>







                            <div class="tab-content" style="position:relative" id="appointTabContent">
								<div id="preloaderbook">
									<i class="circle-preloader"></i>
								</div>

								<!--<div class="tab-pane fade show active" id="nature_of_enquiry" role="tabpanel" aria-labelledby="nature_of_enquiry-tab">
									<div class="tab_header">
										<h4>Nature of Enquiry</h4>
									</div>
									<div class="tab_body">
										<div class="service_row">
											<div class="service_lists">
                                                @foreach(\App\NatureOfEnquiry::where('status',1)->get() as $enquiry)
                                                    <div class="enquiry_item" style="cursor: pointer;" data-id="{{$enquiry->id}}" data-is-recurring="0" data-has-extras="false" style="">
                                                        <div class="services_item_header">
                                                            <div class="services_item_title">
                                                                <span class="services_item_title_span">{{$enquiry->title}}</span>
                                                                <span class="services_item_duration"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
										</div>
									</div>
								</div>-->

								<!--<div class="tab-pane fade" id="services" role="tabpanel" aria-labelledby="services-tab">
									<div class="tab_header">
										<h4>Services</h4>
									</div>
									<div class="tab_body">
										<div class="service_row">
											<div class="service_lists">
											@foreach(\App\BookService::where('status',1)->get() as $bookservices)
												<div class="services_item" style="cursor: pointer;" data-id="{{$bookservices->id}}" data-is-recurring="0" data-has-extras="false" style="">
													<div class="services_item_header">
														<div class="services_item_img" style="width: 80px;height: 80px;">
															<img class="" src="{{asset('public/img/service_img')}}/{{$bookservices->image}}">
														</div>
														<div class="services_item_title">
															<span class="services_item_title_span">{{$bookservices->title}}</span>
															<span class="services_item_duration">{{$bookservices->duration}} minutes</span>

														</div>

														<div class="services_item_price" data-price="{{$bookservices->price}}">
															{{$bookservices->price}}
                                                        </div>

														<div class="services_item_title">
														 	<span class="services_item_duration">{{$bookservices->description}}</span>
														</div>
                                                    </div>
												</div>
											@endforeach
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-btn text-left">
													<input type="button" class="btn prevbtn" name="submit" data-steps="nature_of_enquiry" value="Back" />
												</div>
											</div>
                                        </div>
									</div>
								</div>-->

								<!--<div class="tab-pane fade" id="appointment_details" role="tabpanel" aria-labelledby="appointment_details-tab">
									<div class="tab_header">
										<h4>Appointment details</h4>
									</div>
									<div class="tab_body">
										<div class="service_row">
											<div class="service_lists">

												<div class="appointment_item" style="cursor: pointer;" data-id="phone" data-is-recurring="0" data-has-extras="false" style="">
													<div class="services_item_header">
														<div class="services_item_img" style="width: 80px;height: 80px;">
															<img class="" src="{{asset('public/img/service_img/phone.png')}}">
														</div>
														<div class="services_item_price">
															<span class="services_item_title_span"><b>Phone</b></span>
														</div>
													</div>
												</div>
												<div class="appointment_item" style="cursor: pointer;" data-id="in_person" data-is-recurring="0" data-has-extras="false" style="">
													<div class="services_item_header">
														<div class="services_item_img" style="width: 80px;height: 80px;">
															<img class="" src="{{asset('public/img/service_img/person.jpeg')}}">
														</div>
														<div class="services_item_price">
															<span class="services_item_title_span"><b>In person</b></span>
														</div>
													</div>
												</div>

												<div class="row">
													<div class="col-md-6">
														<div class="form-btn text-left">
															<input type="button" class="btn prevbtn" name="submit" value="Back" data-steps="services" />
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-btn text-right">
															<input type="button" class="btn nextbtn" data-steps="info" name="submit" value="Next" />
														</div>
													</div>
												</div>

											</div>
										</div>
									</div>
								</div>-->

								<!--<div class="tab-pane fade" id="info" role="tabpanel" aria-labelledby="info-tab">
									<div class="tab_header">
										<h4>Fill Information</h4>
									</div>
									<form id="fromtopupvalues">
									<div class="tab_body">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="fullname">Full Name</label>
													<input type="text" class="form-control fullname" placeholder="Enter Name" name="fullname" />
													<input type="hidden" class="form-control " placeholder="" name="noe_id" />
													<input type="hidden" class="form-control " placeholder="" name="service_id" />
													<input type="hidden" class="form-control " placeholder="" name="date" />
													<input type="hidden" class="form-control " placeholder="" name="time" />
													<input type="hidden" class="form-control " placeholder="" name="appointment_details" />
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="email">Email</label>
													<input id="email" type="email" class="form-control email" placeholder="Enter Email" name="email" />
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="phone">Phone</label>
													<input id="phone" type="text" class="form-control phone" placeholder="Enter Phone" name="phone" />
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="title">Title</label>
													<input type="text" class="form-control title" placeholder="Enter Title" name="title" />
												</div>
											</div>
											<div class="col-md-12">
												<div class="form-group">
													<label for="description">Description</label>
													<textarea class="form-control description" placeholder="Enter Description" name="description"></textarea>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-btn text-left">
													<input type="button" class="btn prevbtn" name="submit" value="Back" data-steps="appointment_details" />
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-btn text-right">
													<input type="button" class="btn nextbtn" data-steps="datetime" name="submit" value="Next" />
												</div>
											</div>
										</div>
									</div>
									</form>
								</div>-->

								<!--<div class="tab-pane fade" id="datetime" role="tabpanel" aria-labelledby="datetime-tab">
									<div class="tab_header">
										<h4>Date & Time</h4>
									</div>
									<div class="tab_body">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<div id='datetimepicker' style="height: 310px;"></div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group" style="width: 305px;height: 312px;border-radius: 2px;background-color: #d3d4ec;box-shadow: 0 0 30px 0 rgba(0,0,0,0.05);padding-top: 0;padding-bottom: 10px;">
													<div class="showselecteddate" style="font-size: 14px;text-align: center; padding: 15px 0 13px;border-bottom: 1px solid #E3EAF3;color: #0d0d0f !important;font-weight: bold;"></div>
													<div class="timeslots" style="overflow: auto !important;height: calc(100% - 50px);">
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-btn text-left">
													<input type="button" class="btn prevbtn" name="submit" data-steps="info" value="Back" />
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-btn text-right">
													<input type="button" class="btn nextbtn" data-steps="confirm" name="submit" value="Next" />
												</div>
											</div>
										</div>
									</div>
								</div>-->

								<!--<div class="tab-pane fade" id="confirm" role="tabpanel" aria-labelledby="confirm-tab">
                                    <div class="tab_header">
										<h4>Confirm Details</h4>
									</div>
									<div class="tab_body">
                                        <div class="row">
											<div class="col-md-12">
												<div class="table-responsive">
													<table class="table table-bordered table-striped">
														<thead>
															<tr>
																<th>Full Name</th>
																<th>Email</th>
																<th>Phone</th>
																<th>Title</th>
																<th>Description</th>
																<th>Date</th>
																<th>Time</th>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td class="full_name"></td>
																<td class="email"></td>
																<td class="phone"></td>
																<td class="title"></td>
																<td class="description"></td>
																<td class="date"></td>
																<td class="time"></td>
															</tr>
														</tbody>
													</table>
												</div>
												<ul class="errors">
												</ul>
												<div class="form-btn text-left">
													<input type="button" class="btn prevbtn" name="submit" value="Back" data-steps="datetime" />
												</div>
												<div class="form-btn text-center">
													<input type="button" class="btn btn-primary  submitappointment" name="submit" value="Submit" />
												</div>
											</div>
										</div>
									</div>
								</div>-->

							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div id="querysuccess_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" class="modal fade custom_modal thankyoupack_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div class="text-center padding64">
					<div class="query_icons">
						<i class="fa-light fa-paper-plane"></i>
					</div>
					<div class="query_heading">
						<h4>Thank You!</h4>
					</div>
					<div class="query_info">
						<p>Your request is submitted successfully!<br/> Our Expert will get in tough with you at the earliest.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script>
jQuery(document).ready(function($){
	$( "#myTab" ).tab( { disabled: [1, 2,3,4] } );
	var duration = 30;
	var starttime = '';
	var endtime = '';
	var daysOfWeek = '';

	var disabledtimeslotes = new Array();


	$(document).delegate('.enquiry_item', 'change', function(){ //alert('change'+$(this).val());
		var v = 'services';
		//var id = $(this).attr('data-id');
        var id = $(this).val();
        if(id != ""){
            $('.services_row').show();
        } else {
            $('.services_row').hide();
        }
		$('#myTab .nav-item #nature_of_enquiry-tab').addClass('disabled');
		$('#myTab .nav-item #services-tab').removeClass('disabled');
		$('#myTab a[href="#'+v+'"]').trigger('click');
		$('input[name="noe_id"]').val(id);
	});



	$(document).delegate('.appointment_item', 'change', function(){
		var v = 'info';
		//var id = $(this).attr('data-id');
        var id = $(this).val();
        if(id != ""){
            $('.info_row').show();
        } else {
            $('.info_row').hide();
        }

		$('#myTab .nav-item #appointment_details-tab').addClass('disabled');
		$('#myTab .nav-item #info-tab').removeClass('disabled');
		$('#myTab a[href="#'+v+'"]').trigger('click');
		$('input[name="appointment_details"]').val(id);
	});


	$(document).delegate('.services_item', 'change', function(){
		var v = 'appointment_details';
		//var id = $(this).attr('data-id');
        var id = $(this).val();
        if(id != ""){
            $('.appointment_row').show();
        } else {
            $('.appointment_row').hide();
        }
		$('.timeslots').html('');
		$('.showselecteddate').html('');
		$.ajax({
			url:'{{URL::to('/getdatetime')}}',
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			type:'POST',
			data:{id:id},
			datatype:'json',
			success:function(res){
				var obj = JSON.parse(res);
				if(obj.success){
                    duration = obj.duration;
					daysOfWeek =  obj.weeks;
					starttime =  obj.start_time;
					endtime =  obj.end_time;
					disabledtimeslotes = obj.disabledtimeslotes;
					var datesForDisable = obj.disabledatesarray;
					$('#datetimepicker').datepicker({
						inline: true,
						startDate: new Date(),
						datesDisabled: datesForDisable,
						daysOfWeekDisabled: daysOfWeek,
						format: 'dd/mm/yyyy'
					}).on('changeDate', function(e) {
                        var date = e.format();
                        var checked_date=e.date.toLocaleDateString('en-US');

                        $('.showselecteddate').html(date);
                        $('input[name="date"]').val(date);

                        $('.timeslots').html('');
					    var start_time = parseTime(starttime),
				        end_time = parseTime(endtime),
			            interval = parseInt(duration);

			            var service_id = $('input[name="service_id"]').val();
                        $.ajax({
                            url:'{{URL::to('/getdisableddatetime')}}',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            type:'POST',
                            data:{service_id:service_id,sel_date:date},
                            datatype:'json',
                            success:function(res){
                                var obj = JSON.parse(res);
                                 if(obj.success){
                                    var objdisable = obj.disabledtimeslotes;
                                    var start_timer = start_time;
                                    for(var i = start_time; i<end_time; i = i+interval){
                                        var timeString = start_timer + interval;

                                        // Prepend any date. Use your birthday.
                                        const timeString12hr = new Date('1970-01-01T' + convertHours(start_timer) + 'Z')
                                        .toLocaleTimeString('en-US',
                                            {timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}
                                        );
                                        const timetoString12hr = new Date('1970-01-01T' + convertHours(timeString) + 'Z')
                                        .toLocaleTimeString('en-US',
                                            {timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}
                                        );

                                        var today_date = new Date();
                                        today_date = today_date.toLocaleDateString('en-US');

                                        // current time
                                        var now = new Date();
                                        var nowTime = new Date('1/1/1900 ' + now.toLocaleTimeString(navigator.language, {
                                            hour: '2-digit',
                                            minute: '2-digit',
                                            hour12: true
									    }));

                                        var current_time=nowTime.toLocaleTimeString('en-US');
                                        if(objdisable.length > 0){
                                            if(jQuery.inArray(timeString12hr, objdisable) != -1  || jQuery.inArray(timetoString12hr, objdisable) != -1) {
                                            } else if ((checked_date == today_date) && (current_time > timeString12hr || current_time > timetoString12hr)){
                                            } else{
                                                $('.timeslots').append('<div data-fromtime="'+timeString12hr+'" data-totime="'+timetoString12hr+'" style="cursor: pointer;" class="timeslot_col"><span>'+timeString12hr+'</span><span>'+timetoString12hr+'</span></div>');
                                            }
                                        } else{
                                            if((checked_date == today_date) && (current_time > timeString12hr || current_time > timetoString12hr)){
                                            } else {
                                                $('.timeslots').append('<div data-fromtime="'+timeString12hr+'" data-totime="'+timetoString12hr+'" style="cursor: pointer;" class="timeslot_col"><span>'+timeString12hr+'</span><span>'+timetoString12hr+'</span></div>');
                                            }
                                            // $('.timeslots').append('<div data-fromtime="'+timeString12hr+'" data-totime="'+timetoString12hr+'" style="cursor: pointer;" class="timeslot_col"><span>'+timeString12hr+'</span><span>'+timetoString12hr+'</span></div>');
                                        }
						                start_timer = timeString;
					                }
                                }else{

                                }
                            }
                        });
                        //	var times_ara = calculate_time_slot( start_time, end_time, interval );
                    });

					$('#myTab .nav-item .nav-link').addClass('disabled');
					$('#myTab .nav-item #appointment_details-tab').removeClass('disabled');
					$('#myTab a[href="#'+v+'"]').trigger('click');
					$('input[name="service_id"]').val(id);
				} else {
                    $('input[name="service_id"]').val('');
					alert('There is a problem in our system. please try again');
				}
			}
		})
	});

	/* $('#datetimepicker').datepicker({
		inline: true,
		 startDate: new Date(),

			format: 'dd/mm/yyyy'
	  }) *//* .on('changeDate', function(e) {
			var date = e.format();
			$('.showselecteddate').html(date);
			$('input[name="date"]').val(date);

			$('.timeslots').html('');
					var start_time = parseTime(starttime),
				end_time = parseTime(endtime),
			interval = parseInt(duration);

			var service_id = $('input[name="service_id"]').val();
			$.ajax({
				url:'{{URL::to('/getdisableddatetime')}}',
				type:'POST',
				data:{service_id:service_id,sel_date:date},
				datatype:'json',
				success:function(res){
					var obj = JSON.parse(res);
				if(obj.success){
					var objdisable = obj.disabledtimeslotes;
					console.log(objdisable);
					var start_timer = start_time;
					for(var i = start_time; i<end_time; i = i+interval){

						var timeString = start_timer + interval;

						// Prepend any date. Use your birthday.
						 const timeString12hr = new Date('1970-01-01T' + convertHours(start_timer) + 'Z')
						  .toLocaleTimeString('en-US',
							{timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}
						  );
						  const timetoString12hr = new Date('1970-01-01T' + convertHours(timeString) + 'Z')
						  .toLocaleTimeString('en-US',
							{timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}
						  );
						if(objdisable.length > 0){
							 if(jQuery.inArray(timeString12hr+"-"+timetoString12hr, objdisable) != -1) {
						 }else{
							$('.timeslots').append('<div data-fromtime="'+timeString12hr+'" data-totime="'+timetoString12hr+'" style="cursor: pointer;" class="timeslot_col"><span>'+timeString12hr+'</span><span>'+timetoString12hr+'</span></div>');
						 }
						}else{
						$('.timeslots').append('<div data-fromtime="'+timeString12hr+'" data-totime="'+timetoString12hr+'" style="cursor: pointer;" class="timeslot_col"><span>'+timeString12hr+'</span><span>'+timetoString12hr+'</span></div>');
						}
						start_timer = timeString;
					}
				}else{


				}
			}
		});

	//	var times_ara = calculate_time_slot( start_time, end_time, interval );

	}); */

	$(document).delegate('.nextbtn', 'click', function(){
		var v = $(this).attr('data-steps'); //alert(v);
		$(".custom-error").remove();
		var flag = 1;

		if(v == 'confirm'){ //datetime
			var fullname = $('.fullname').val();
			var email = $('.email').val();
			var title = $('.title').val();
			var phone = $('.phone').val();
			var description = $('.description').val();

			if( !$.trim(fullname) ){
				flag = 0;
				$('.fullname').after('<span class="custom-error" role="alert">Fullname is required</span>');
			}
			if( !ValidateEmail(email) ){
				flag = 0;
				if(!$.trim(email)){
					$('.email').after('<span class="custom-error" role="alert">Email is required.</span>');
				}else{
					$('.email').after('<span class="custom-error" role="alert">You have entered an invalid email address!</span>');
				}
			}

            if( !$.trim(phone) ){
				flag = 0;
				$('.phone').after('<span class="custom-error" role="alert">Phone number is required</span>');
			}

			if( !$.trim(title) ){
				flag = 0;
				$('.title').after('<span class="custom-error" role="alert">Title is required</span>');
			}
			if( !$.trim(description) ){
				flag = 0;
				$('.description').after('<span class="custom-error" role="alert">Description is required</span>');
			}
		}/*else if(v == 'confirm'){

		}*/
        //alert('flag=='+flag+'---v=='+v);
		if(flag == 1 && v == 'confirm'){
            $('.confirm_row').show();
            $('#myTab .nav-item .nav-link').addClass('disabled');
		    $('#myTab .nav-item #'+v+'-tab').removeClass('disabled');
			$('#myTab a[href="#'+v+'"]').trigger('click');
		} else {
            $('.confirm_row').hide();
        }

		function ValidateEmail(inputText)
		{
			var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
			if(inputText.match(mailformat))
			{
			return true;
			}
			else
			{
			// alert("You have entered an invalid email address!");
			return false;
			}
		}

	});

	$(document).delegate('.prevbtn', 'click', function(){
		var v = $(this).attr('data-steps');

		$('#myTab .nav-item .nav-link').addClass('disabled');
		$('#myTab .nav-item #'+v+'-tab').removeClass('disabled');
		$('#myTab a[href="#'+v+'"]').trigger('click');
	});


	$(document).delegate('.timeslot_col', 'click', function(){
		$('.timeslot_col').removeClass('active');
		$(this).addClass('active');
		var fromtime = $(this).attr('data-fromtime');
		var totime = $(this).attr('data-totime');
		$('input[name="time"]').val(fromtime+'-'+totime);

        $('.full_name').text($('.fullname').val());
        $('.email').text($('.email').val());
        $('.title').text($('.title').val());
        $('.phone').text($('.phone').val());
        $('.description').text($('.description').val());
        $('.date').text($('input[name="date"]').val());
        $('.time').text($('input[name="time"]').val());
	});


    function parseTime(s) {
        var c = s.split(':');
        return parseInt(c[0]) * 60 + parseInt(c[1]);
    }

    function convertHours(mins){
        var hour = Math.floor(mins/60);
        var mins = mins%60;
        var converted = pad(hour, 2)+':'+pad(mins, 2);
        return converted;
    }

    function pad (str, max) {
        str = str.toString();
        return str.length < max ? pad("0" + str, max) : str;
    }

    function calculate_time_slot(start_time, end_time, interval = "30"){
        var i, formatted_time;
        var time_slots = new Array();
        for(var i=start_time; i<=end_time; i = i+interval){
            formatted_time = convertHours(i);
            const timeString = formatted_time;

            time_slots.push(timeString);
        }
        return time_slots;
    }

    $(document).delegate('.submitappointment','click',function (e) {
		var flag = 1;
		$('.errors').html('');
		var fullname = $('.fullname').val();
		var email = $('.email').val();
		var title = $('.title').val();
		var phone = $('.phone').val();
		var date = $('input[name="date"]').val();
		var time = $('input[name="time"]').val();
		var service_id = $('input[name="service_id"]').val();

		var description = $('.description').val();
		if( !$.trim(date) ){
			flag = 0;
			$('.errors').append('<li><span class="custom-error" role="alert">Date is required</span></li>');
		}if( !$.trim(time) ){
			flag = 0;
			$('.errors').append('<li><span class="custom-error" role="alert">Time is required</span></li>');
		}if( !$.trim(service_id) ){
			flag = 0;
			$('.errors').append('<li><span class="custom-error" role="alert">Service is required</span></li>');
		}
		if( !$.trim(fullname) ){
			flag = 0;
				$('.errors').append('<li><span class="custom-error" role="alert">Name is required</span></li>');
		}
		if( !$.trim(email) ){
			flag = 0;
			$('.errors').append('<li><span class="custom-error" role="alert">Email is required</span></li>');
		}
		if( !$.trim(phone) ){
			flag = 0;
			$('.errors').append('<li><span class="custom-error" role="alert">Phone is required</span></li>');
		}
		if( !$.trim(title) ){
			flag = 0;
			$('.errors').append('<li><span class="custom-error" role="alert">Title is required</span></li>');
		}
		if( !$.trim(description) ){
			flag = 0;
			$('.errors').append('<li><span class="custom-error" role="alert">Description is required</span></li>');
		}
		if(flag == 1){
			$('#preloaderbook').show();
			$.ajax({
			    type:'POST',
			    data: $('#appintment_form').serialize(),
			    url:'{{URL::to('/book-an-appointment/store')}}',
			    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			    success:function(data){
				    $('#preloaderbook').hide();
				    var obj = JSON.parse(data);
				    if(obj.success){
					    $('#confirm').html('<div class="tab_header"><h4></h4></div><div class="tab_body"><h4 style="text-align: center;padding: 20px;">'+obj.message+'</h4></div>');
					    setTimeout(function(){ window.location.reload(); }, 5000);
				    }else{
					    alert('Please try again. There is a issue in our system');
				    }
			    }
		    });
		}
	});
});
</script>

@endsection
