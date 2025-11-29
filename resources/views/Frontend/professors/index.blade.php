@extends('layouts.dashboard_frontend')
@section('title', @$seoDetails->meta_title)
@section('meta_title', @$seoDetails->meta_title)
@section('meta_keyword', @$seoDetails->meta_keyword)
@section('meta_description', @$seoDetails->meta_desc)
@section('content')
<div class="row">
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
		<nav class="custom-breadcrumb" aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">
					<a href="{{URL::to('/')}}">Home</a>
				</li>
				<li class="breadcrumb-item active">
					<a href="{{URl::to('/professors')}}">Our Faculties</a>
				</li>
			</ol>
		</nav>
	</div>
</div>
<div class="row product-list-page">
	<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
		{{ Form::open(array('name'=>"search-form", 'method' => 'get', 'autocomplete'=>'off')) }}
			{{ Form::hidden('search_term', Request::get('search_term'), array('id'=>'search_term')) }}
			<div class="filters filter1">
				<div class="accordion-head">Faculties</div>
				<ul class="list-unstyled accordion-body">
					@if(count(@$professors) !== 0)
						@foreach (@$professors as $professor)	
							<li>
								<div class="checkbox">
									<label>
										<input type="checkbox" class="professor-search" value="pro_{{base64_encode(convert_uuencode(@$professor->id))}}">
										{{ @$professor->first_name == "" ? config('constants.empty') : @$professor->first_name.' '.@$professor->last_name }}
									</label>
								</div>
							</li>
						@endforeach		
					@else
						<li class="no_data">
							<label>No Professor Found.</label>
						</li>
					@endif		
				</ul>
			</div>
			<div class="filters filter1">
				<div class="accordion-head">Classes</div>
				<ul class="list-unstyled accordion-body">
					@if(count(@$subjects) !== 0)
						@foreach (@$subjects as $subject)	
							<li>
								<div class="checkbox">
									<label>
										<input type="checkbox" class="professor-search" value="cls_{{base64_encode(convert_uuencode(@$subject->id))}}">
											{{ @$subject->subject_name == "" ? config('constants.empty') : @$subject->subject_name }}
									</label>
								</div>
							</li>
						@endforeach		
					@else
						<li class="no_data">
							<label>No Class Found.</label>
						</li>
					@endif		
				</ul>
			</div>
		{{ Form::close() }}	
	</div>
	
	<div class="col-lg-9 col-sm-9 col-md-9 col-xs-12 col-7-products"> 
		<div class="main-product-area faculty">
			<div cellspacing="0" class="products-list-products" id="product-list-table">
				<ul class="product-list list-unstyled">
					
					@if(count(@$lists) !== 0)
						@foreach (@$lists as $list)
							<li class="product-bucket">
								<div class="text-center new-list">
									<div class="featured imgContainers">
										<a href="{{URL::to('/products/'.base64_encode(convert_uuencode(@$list->id)))}}" class="product-image" target="_blank">
											@if(@$list->organisation_role == 4)
												@if(@$list->organisationData->profile_img == '')
													<img class="img-responsive professor-image" src="{{URL::to('/public/img/Frontend/img/not_found.jpg')}}">
												@else
													<img class="img-responsive professor-image" src="{{URL::to('/public/img/profile_imgs/'.@$list->organisationData->profile_img)}}">
												@endif
											@else
												@if(@$list->org_professor_image == '')
													<img class="img-responsive professor-image" src="{{URL::to('/public/img/Frontend/img/not_found.jpg')}}">
												@else
													<img class="img-responsive professor-image" src="{{URL::to('/public/img/profile_imgs/'.@$list->org_professor_image)}}">
												@endif	
											@endif		
										</a>
									</div>
									<div class="fixedHeight">
										<div class="product-name relative">
											
											<span class="professor-name">
												{{ @$list->first_name == "" ? config('constants.empty') : @$list->first_name.' '.@$list->last_name }}
											</span>
											 
										</div>
									</div>
									<div class="actions">
										<div class="add-to-links">
											<div>
												<a href="{{URL::to('/products/'.base64_encode(convert_uuencode(@$list->id)))}}" class="btn button btn-cart btn-primary request-now">
													<i class="fa fa-eye-slash" aria-hidden="true"></i>
													View Classes
												</a>
											</div>
										</div>
									</div>
								</div>
							</li>
						@endforeach		
					@else
						<li class="product-bucket no_data">
							No Professor Found.
						</li>
					@endif
				</ul>
			</div>
		</div>
	</div>
</div>
@endsection