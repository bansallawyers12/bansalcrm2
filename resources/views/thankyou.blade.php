@extends('layouts.frontend')
@section('content')
<section class="custom_breadcrumb bg-img bg-overlay" style="background-image: url({{ asset('img/Frontend/bg-2.jpg') }});">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="section-heading text-center mx-auto">
					<div class="section-header">
                        <h2 style="color:#FFF;">Thank You !!</h2>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="custom_inner_page" section-padding-5-0="" style="background-color:#F7F7F7;">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-12 col-md-12">
				<div class="cus_inner_content">
                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Error Message -->
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
			</div>
        </div>
	</div>
</section>
@endsection

