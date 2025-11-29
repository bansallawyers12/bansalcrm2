@extends('layouts.frontend')
@section('title', @$seoDetails->meta_title)
@section('meta_title', @$seoDetails->meta_title)
@section('meta_keyword', @$seoDetails->meta_keyword)
@section('meta_description', @$seoDetails->meta_desc)
@section('content')

<section class="currency-calculator-area section-padding-50 bg-img bg-overlay" style="background-image: url({!! asset('public/img/Frontend/bg-2.jpg') !!}); padding-top:40px">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="section-heading text-center white mx-auto" style="margin-bottom:40px">
					<div class="section-header">
						<h3><span id="ContentPlaceHolder1_Label7">Mission and Vision</span></h3>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="cryptos-about-area" section-padding-5-0 style="background-color:#F7F7F7; padding-top:40px; padding-bottom:40px">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-12 col-md-12">
				<div class="about-content mb-50">
					<div>
						<p style="line-height:16px; color:black">
							<img id="ContentPlaceHolder2_Image1" align="left" src="{!! asset('public/img/Frontend/73202063430employer.jpg') !!}" style="border-radius:20px; margin-right:20px; margin-bottom:20px; width:400px" />
						
							<span id="ContentPlaceHolder2_Label1" style="font-size: 12pt; font-family:Helvetica; color: #000000; text-align: left;"><p><span style="font-size: medium;"><span style="font-size: 13pt; text-align: justify; font-family: Helvetica; color: #000000;"><span style="font-size: large;"><span style="font-size: large;"><strong>MISSION</strong></span><br /><span style="font-size: medium;">We  believe in providing honest and reliable services to our customers.  Clients generally demand simple ways to get a permanent visa, so that  they do not have to face the long process duration. They later on  struggle and have a tough time in the asylum without any identity.  Instead of facing this tough time for getting a permanent residence, our  consultants advise as to what is best for them. We explain the clients  about the specific requirements of a particular visa. Even though it  takes a long time it would help them to get a legal and a valid PR.</span></span></span>To become the leading education and migration firm in Australia, we  learn and discover the skills and qualities of students to help students  and their parents select the courses and providers in accordance with  their academic background and financial capacity. We thrive to provide  our clients excellent services with clear communication, fast and  simplified application process and successful outcome.<br /><br /><span style="font-size: large;"><strong>VISION</strong></span></span><span style="font-size: medium;"><span style="font-size: 13pt; text-align: justify; font-family: Helvetica; color: #000000;"><span style="font-size: large;"><br /><span style="font-size: medium;">Our immigration consultants are the pillars of our organization.</span> </span></span>We are committed to deliver the highest quality of study experience  to overseas students by developing different branch offices throughout  different countries as well as improving our service quality, customer  experience and reachability. We will inspire overseas students and  new immigrants and participate in social activities to create a bright  and harmonious future for the society, thereby developing a strong  community.</span></p><p>&nbsp;</p></span>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

@endsection 