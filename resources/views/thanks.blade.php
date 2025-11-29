@extends('layouts.dashboard_frontend')
@section('content')
<div class="single_package">
	<div class="inner_single_package">
		<div class="container-fluid">	
			<div class="row"> 
				<div class="list_image">
					<img src="{!! asset('public/img/Frontend/img/rajastan_img.jpg') !!}" class="img-fluid" alt=""/>
					<div class="opacity_banner"></div> 
				</div>
			</div>
			<div class="row"> 
				<div class="col-md-12"> 
					<h2>Thank You !!</h2>
					<p>Congratulations, your query has been submitted successfully. You'll soon be contacted by our representative after we have processed your details. This normally takes 1-4 working days.</p>
					<p>
						<strong>Important:</strong>
						</p>
						<p>
						In case you do not receive our reply or acknowledgement with in 24 hours, it means we have not received your communication and we request you to resend your query at following e-mail id : inquiry@test.com
					</p>
				</div>
			</div>
		</div>
	</div>
</div>
