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
				<li class="breadcrumb-item active">
					<a href="{{URl::to('/products/'.base64_encode(convert_uuencode(@$professor->id)))}}">Classes</a>
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
				<div class="accordion-head">Classes</div>
				<ul class="list-unstyled accordion-body">
					@if(count(@$subjects) !== 0)
						@foreach (@$subjects as $subject)	
							<li>
								<div class="checkbox">
									<label>
										<input type="checkbox" class="product-search" value="cls_{{base64_encode(convert_uuencode(@$subject->id))}}">
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
	
	<div class="col-lg-9 col-sm-9 col-md-9 col-xs-12 col-9-products">
		<div class="main-product-area">
			<div cellspacing="0" class="products-list-products" id="product-list-table">
				<ul class="product-list list-unstyled">
					@if(count(@$lists) !== 0)
						@foreach (@$lists as $list)
							<li class="product-bucket">
								@if(@$list->productOtherInfo[0]->discount != 0)
									<div class="discountWrapper">
										<div class="product-list-discount">
											Special Discount                                    
										</div>
									</div>
								@endif		
								<div class="text-center new-list">
									<div class="featured imgContainers">	
										<a href="{{URL::to('/view_product/'.base64_encode(convert_uuencode(@$list->id)))}}" class="product-image" target="_blank">
											@if(@$list->image == '')
												<img class="img-responsive productImage" src="{{URL::to('/public/img/Frontend/img/not_found.jpg')}}">
											@else
												<img class="img-responsive productImage" src="{{URL::to('/public/img/product_img/'.@$list->image)}}">
											@endif		
										</a>
									</div>
									<div class="fixedHeight">
										<div class="product-name relative">
											<span class="subject-name">
												{{ @$list->subject_name == "" ? config('constants.empty') : @$list->subject_name}}
											</span>	
											<br />
											<span class="professor-name">
												{{ @$professor->first_name == "" ? config('constants.empty') : @$professor->first_name.' '.@$professor->last_name }}
											</span>	
										</div>
									</div>
									<div class="price-wrapper">
										<div class="prdocuct-price">
											<ul class="list-inline">
												<li class="list-inline-item">
													<span class="price-lbl">Price :  </span>
												</li>
												@if(@$list->productOtherInfo[0]->discount != 0)
													<li class="list-inline-item">
														<span class="ol">
															<strike> 
																<i class="fa fa-inr" aria-hidden="true"></i> 
																{{ @$list->productOtherInfo[0]->price == "" ? config('constants.empty') : @$list->productOtherInfo[0]->price }}
															</strike>
														</span>
													</li>
												@endif		
												<li class="list-inline-item">
													<span> 
														<i class="fa fa-inr" aria-hidden="true"></i>  
														{{ @$list->productOtherInfo[0]->total_amount == "" ? config('constants.empty') : @$list->productOtherInfo[0]->total_amount }}
													</span>
												</li>
											</ul>
											@if(@$list->productOtherInfo[0]->discount != 0)
												<div>
													<ul class="list-inline">
														<li class="list-inline-item">
															<span style="margin: 0 10px;">Discount : </span>
														</li>
														<li class="list-inline-item">
															<span> {{ @$list->productOtherInfo[0]->discount == "" ? config('constants.empty') : @$list->productOtherInfo[0]->discount }}% </span>
														</li>
													</ul>
												</div>
											@endif
										</div>		
									</div>
									<div class="actions">
										<ul class="availability in-stock list-inline">
											<li class="list-inline-item">
												<span class="color-gray font12 text-center">Views : {{ @$list->productOtherInfo[0]->views == "" ? config('constants.empty') : @$list->productOtherInfo[0]->views }} </span>
											</li>
											<li class="list-inline-item">
												<span class="icon-bar">|</span>
											</li>
											<li class="list-inline-item">
												<span> {{ @$list->batch_type == "" ? config('constants.empty') : str_limit(@$list->batch_type, '25', '...') }}</span>
											</li>
										</ul>
										<div class="add-to-links">
											<div class="cart-form">
												{{ Form::open(array('url' => '/cart', 'name'=>"add_to_cart", 'autocomplete'=>'off')) }}
													{{ Form::hidden('user_id', @Auth::user()->id) }}
													{{ Form::hidden('product_other_info_id', @$list->productOtherInfo[0]->id) }}
													{{ Form::hidden('quantity', 1) }}
													{{ Form::hidden('product_id', @$list->productOtherInfo[0]->product_id) }}
													{{ Form::button('<i class="fa fa-cart-arrow-down" aria-hidden="true"></i>Add to Cart', ['class' => 'link-compare btn btn-info add-to-cart-list', 'type' => 'submit']) }}
												{{ Form::close() }}	
											</div>
											<div>
												<a href="{{URL::to('/view_product/'.base64_encode(convert_uuencode(@$list->id)))}}" class="link-compare btn btn-info">
													<i class="fa fa-eye-slash" aria-hidden="true"></i>
														View Details
												</a>
											</div>
										</div>
									</div>
								</div>
							</li>
						@endforeach	
					@else
						<li class="product-bucket no_data">
							No Class Found.
						</li>
					@endif
				</ul>
			</div>
		</div>
	</div>
</div>
@endsection