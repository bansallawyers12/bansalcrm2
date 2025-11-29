@extends('layouts.dashboard_frontend')
@section('title', 'Thankyou')
@section('content')
<div class="row">
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
		<section class="border">
			<div class="wizard">
				<div class="tab-content">
					<div class="tab-pane thankyou" role="tabpanel">
						<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
							<div class="success-msg">
								<span class="h4">Dear {{@Auth::user()->first_name}} {{@Auth::user()->last_name}}, Your Transaction is successfully done.</span>
								<div class="clearfix"></div>
								<a href="{{URL::to('/professors')}}" class="btn btn-warning">
									<i class="fa fa-angle-left"></i> 
									Continue Shopping
								</a>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</section>
	</div>
</div>
@endsection