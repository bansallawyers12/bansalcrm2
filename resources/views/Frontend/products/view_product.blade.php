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
					<a href="{{URl::to('/products/'.base64_encode(convert_uuencode(@$fetchedData->professor->id)))}}">Classes</a>
				</li>
			</ol>
		</nav>
	</div>
</div>
<div class="row">
	<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
		@if(@$fetchedData->sampleImage != '')
			<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12 no-padding">
				<section class="vertical-center-4 slider">
						<?php
							@$images = @$fetchedData->sampleImage;
							@$arrayImages = explode(',', $images);
						?>
						@foreach(@$arrayImages as $image)	
							<img src="{{URL::to('/public/img/product_sample_img')}}/{{@$image}}"  width="90px" height="90px">
						@endforeach
				</section>
			</div>
		@endif
		@if(@$fetchedData->sampleImage != '')	
			<div class="col-lg-9 col-sm-9 col-md-9 col-xs-12">
		@else
			<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
		@endif	
			@if(@$fetchedData->image != '')
				<img src="{{URL::to('/public/img/product_img')}}/{{@$fetchedData->image}}" alt="Snow" class="img-responsive prod-img" width="410px" height="410px" />
			@else
				<img src="{{URL::to('/public/img/Frontend/img/not_found.jpg')}}" alt="Snow" class="img-responsive prod-img" width="410px" height="410px" />	
			@endif		
		</div>
	</div>
	<div class="col-lg-6  col-sm-6  col-md-6 col-xs-12">
			<ul class="list-group">
				<li class="list-group-item">
					<span class="h3">
						{{ @$fetchedData->subject_name == "" ? config('constants.empty') : @$fetchedData->subject_name }}					
					</span>
				</li>
				<li class="list-group-item">
					<ul class="product-page-details list-inline">
						<!--By Default 5 Star Start-->
							<li>
								<i class="fa fa-star fa-star-blue" aria-hidden="true"></i>
								<i class="fa fa-star fa-star-blue" aria-hidden="true"></i>
								<i class="fa fa-star fa-star-blue" aria-hidden="true"></i>
								<i class="fa fa-star fa-star-blue" aria-hidden="true"></i>
								<i class="fa fa-star fa-star-blue" aria-hidden="true"></i>
							</li>
						<!--By Default 5 Star End-->		
						<li>
							<span class="icon-bar">|</span>
						</li>
						<li>
							<span class="h6">
								@if(count(@$productReviewInfo) > 0)
									{{count(@$productReviewInfo).' reviews'}}
								@else
									0 reviews	
								@endif		
							</span>
						</li>
						@if(@Auth::check())	
							<li>
								<span class="icon-bar">|</span>
							</li>
							<li>
								<span class="h6" data-toggle="modal" data-target="#reviewModal"> 
									<i class="fa fa-edit"></i> 
									Write a Review		
								</span>
							</li>
						@endif		
					</ul>
				</li>
				<li class="list-group-item">
					<span class="h4">Price : </span>
					<strong class="realprice">
						<strike>
							@if(@$minProduct->productOtherInfo[0]->discount != "0")
								<i class="fa fa-inr" aria-hidden="true"></i> 
								{{ @$minProduct->productOtherInfo[0]->price == "" ? '0' : @$minProduct->productOtherInfo[0]->price }}
							@endif
						</strike>
					</strong>
					<strong>
						<i class="fa fa-inr fa-2x" aria-hidden="true"></i> 
						<span class="mainprice" data-mainprice="{{ @$minProduct->productOtherInfo[0]->total_amount == "" ? '0' : @$minProduct->productOtherInfo[0]->total_amount }}">
							{{ @$minProduct->productOtherInfo[0]->total_amount == "" ? '0' : @$minProduct->productOtherInfo[0]->total_amount }}
						</span>
					</strong>
				</li>
				{{ Form::open(array('url' => '/cart', 'name'=>"add_to_cart", 'autocomplete'=>'off')) }}
					<li class="list-group-item">
						<div class="form-group row">
							<label class="col-sm-3 col-form-label col-xs-6">Delivery Method</label>
							<div class="col-sm-offset-0 col-sm-8 col-sm-offset-4 col-xs-6">
								<select class="form-control select_mode">
									@if(count(@$modeOfProduct) !== 0)
										@foreach (@$modeOfProduct as $mode)
											<option value="{{ @$mode->id }}" <?php echo @$minProduct->productOtherInfo[0]->mode_of_product == @$mode->id ? 'selected' : ''; ?>>{{ str_limit(@$mode->mode_product, '50', '...') }}</option>
										@endforeach
									@endif		
								</select>
								{{ Form::hidden('store_mode_id', @$minProduct->productOtherInfo[0]->mode_of_product, array('id'=>'store_mode_id')) }}
							</div>
						</div>
						<div class="clear clearfix"></div>	
						<div class="form-group row">
							<label for="inputPassword3" class="col-sm-3 col-form-label col-xs-6">Views</label>
							<div class="col-sm-offset-0 col-sm-8 col-sm-offset-4 col-xs-6">
								<select class="form-control views">
								</select>
								{{ Form::hidden('store_view_id', @$minProduct->productOtherInfo[0]->id, array('id'=>'store_view_id')) }}
							</div>
						</div>
						<div class="clear clearfix"></div>	
						<div class="form-group row">
							<label for="inputPassword3" class="col-sm-3 col-form-label col-xs-6">Duration</label>
							<div class="col-sm-offset-0 col-sm-8 col-sm-offset-4 col-xs-6 duration">	
								{{ @$minProduct->productOtherInfo[0]->duration == "" ? 'No Item selected' : @$minProduct->productOtherInfo[0]->duration }}
								
							</div>
						</div>
						<div class="clear clearfix"></div>
						<div class="form-group row">
							<label for="inputPassword3" class="col-sm-3 col-form-label col-xs-6">Validity</label>
							<div class="col-sm-offset-0 col-sm-8 col-sm-offset-4 col-xs-6 validity">
								{{ @$minProduct->productOtherInfo[0]->validity == "" ? 'No Item selected' : @$minProduct->productOtherInfo[0]->validity }}
							</div>
						</div>
						<div class="clear clearfix"></div>
						<div class="form-group row hide">
							<label for="inputPassword3" class="col-sm-3 col-form-label col-xs-6">Price</label>
							<div class="col-sm-offset-0 col-sm-8 col-sm-offset-4 col-xs-6 price" data-price="{{ @$minProduct->productOtherInfo[0]->price == "" ? '0' : @$minProduct->productOtherInfo[0]->price }}">	
								{{ @$minProduct->productOtherInfo[0]->price == "" ? 'No Item selected' : '₹ '. @$minProduct->productOtherInfo[0]->price }}
							</div>
						</div>
						<div class="clear clearfix"></div>					
						<div class="form-group row hide">
							<label for="inputPassword3" class="col-sm-3 col-form-label col-xs-6">Discount</label>
							<div class="col-sm-offset-0 col-sm-8 col-sm-offset-4 col-xs-6 discount" data-discount="{{ @$minProduct->productOtherInfo[0]->discount == "" ? '0' : @$minProduct->productOtherInfo[0]->discount }}">	
								{{ @$minProduct->productOtherInfo[0]->discount == "" ? 'No Item selected' : @$minProduct->productOtherInfo[0]->discount.'%' }}
							</div>
						</div>
						<div class="clear clearfix"></div>
						<div class="form-group row">
							<label for="inputPassword3" class="col-sm-3 col-form-label col-xs-6">Quantity</label>
							<div class="col-sm-offset-0 col-sm-8 col-sm-offset-4 col-xs-6">
								<div class="quantity buttons_added">
									<input type="button" value="-" class="quantity_insert custom-btn minus" />
									<input type="text" step="1" min="1" max="" name="quantity" value="1" title="Qty" class="input-text qty text quantity_input" size="4" pattern="" inputmode="" readonly="readonly" />
									<input type="button" value="+" class="quantity_insert custom-btn plus" />
								</div>
							</div>
						</div>
					</li>
					<li class="list-group-item">
						<ul class="list-inline text-center">
							<li class="list-inline-item">
								{{ Form::hidden('user_id', @Auth::user()->id) }}
								{{ Form::hidden('product_other_info_id', @$minProduct->productOtherInfo[0]->id, array('class'=>'product_other_info')) }}
								{{ Form::hidden('product_id', @$fetchedData->id, array('class'=>'product_id')) }}
								
								{{ Form::button('<i class="fa fa-cart-arrow-down" aria-hidden="true"></i>Add to Cart', ['class' => 'btn btn-info cart_submit', 'type' => 'submit', 'disabled'=>'disabled']) }}
							</li>
						</ul>
					</li>
				{{ Form::close() }}	
				
			</ul>
	</div>
</div>





<div class="row all-detail">
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
		<div class="card-tab">
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active">
					<a href="#description" aria-controls="description" role="tab" data-toggle="tab">
						<i class="fa fa-home"></i>  
						<span>Description</span>
					</a>
				</li>
				<li role="presentation">
					<a href="#features" aria-controls="features" role="tab" data-toggle="tab">
						<i class="fa fa-user"></i>  
						<span>Features</span>
					</a>
				</li>
				<li role="presentation">
					<a href="#demo_videos" aria-controls="demo_videos" role="tab" data-toggle="tab">
						<i class="fa fa-video-camera" aria-hidden="true"></i>  
						<span>Demo Videos</span>
					</a>
				</li>
				<li role="presentation">
					<a href="#review" aria-controls="review" role="tab" data-toggle="tab">
						<i class="fa fa-cog"></i>  
						<span>Reviews</span>
					</a>
				</li>
				<li role="presentation">
					<a href="#about_professsor" aria-controls="about_professsor" role="tab" data-toggle="tab">
						<i class="fa fa-plus-square-o"></i>  
						<span>About the Professor</span>
					</a>
				</li>
			</ul>
			
			
			<div class="tab-content">
				<!--All Product Information Start -->
					<div role="tabpanel" class="tab-pane active" id="description">
						<div class="table-responsive">
							<table class="table table-bordered product-info">
								<tr>
									<th>Professor Name</th>
									<td>
										{{ @$fetchedData->professor->first_name == "" ? config('constants.empty') : @$fetchedData->professor->first_name.' '.@$fetchedData->professor->last_name }}
									</td>
								</tr>
								<tr>
									<th>Subject Name</th>
									<td>
										{{ @$fetchedData->subject_name == "" ? config('constants.empty') : @$fetchedData->subject_name }}
									</td>
								</tr>
								<tr>
									<th>Course Level</th>
									<td>
										{{ @$fetchedData->course_level == "" ? config('constants.empty') : @$fetchedData->course_level }}
									</td>
								</tr>
								<tr>
									<th>Attempt Info</th>
									<td>
										{{ @$fetchedData->attempt_info == "" ? config('constants.empty') : @$fetchedData->attempt_info }}
									</td>
								</tr>
								<tr>
									<th>Package</th>
									<td>
										{{ @$fetchedData->package == "" ? config('constants.empty') : @$fetchedData->package }}
									</td>
								</tr>
								<tr>
									<th>Video Language</th>
									<td>
										{{ @$fetchedData->video_language == "" ? config('constants.empty') : @$fetchedData->video_language }}
									</td>
								</tr>
								<tr>
									<th>Study Material Language</th>
									<td>
										{{ @$fetchedData->study_material_language == "" ? config('constants.empty') : @$fetchedData->study_material_language }}
									</td>
								</tr>
								<tr>
									<th>Dispatched by</th>
									<td>
										{{ @$fetchedData->dispatched_by == "" ? config('constants.empty') : @$fetchedData->dispatched_by }}
									</td>
								</tr>
								<tr>
									<th>Delivery Period</th>
									<td>
										{{ @$fetchedData->delivery_period == "" ? config('constants.empty') : @$fetchedData->delivery_period }}
									</td>
								</tr>
								<tr>
									<th>System Requirement</th>
									<td>
										{{ @$fetchedData->system_requirement == "" ? config('constants.empty') : @$fetchedData->system_requirement }}
									</td>
								</tr>
								<tr>
									<th>Runs On</th>
									<td>
										{{ @$fetchedData->runs_on == "" ? config('constants.empty') : @$fetchedData->runs_on }}								
									</td>
								</tr>
								<tr>
									<th>Batch Type</th>
									<td>
										{{ @$fetchedData->batch_type == "" ? config('constants.empty') : @$fetchedData->batch_type }}
									</td>
								</tr>
								<tr>
									<th>Number of Lecture</th>
									<td>
										{{ @$fetchedData->no_lecture == "" ? config('constants.empty') : @$fetchedData->no_lecture }}
									</td>
								</tr>
								<tr>
									<th>Syllabus Coverage</th>
									<td>
										{{ @$fetchedData->syllabus_coverage == "" ? config('constants.empty') : @$fetchedData->syllabus_coverage }}
									</td>
								</tr>
								<tr>
									<th>Amendment</th>
									<td>
										{{ @$fetchedData->amendment == "" ? config('constants.empty') : @$fetchedData->amendment }}
									</td>
								</tr>
								<tr>
									<th>Faculty Support</th>
									<td>
										{{ @$fetchedData->faculty_support == "" ? config('constants.empty') : @$fetchedData->faculty_support }}
									</td>
								</tr>
								<tr>
									<th>Lecture Recorded</th>
									<td>
										{{ @$fetchedData->lecture_recorded == "" ? config('constants.empty') : @$fetchedData->lecture_recorded }}
									</td>
								</tr>
								<tr>
									<th>Fast Forward Option</th>
									<td>
										{{ @$fetchedData->fast_forward == "" ? config('constants.empty') : @$fetchedData->fast_forward }}
									</td>
								</tr>
								<tr>
									<th>Books Provided</th>
									<td>
										{{ @$fetchedData->books_provided == "" ? config('constants.empty') : @$fetchedData->books_provided }}
									</td>
								</tr>
								<tr>
									<th>Index</th>
									<td>
										{!! @$fetchedData->index_order !!}
									</td>
								</tr>
								<tr>
									<th>Other Information</th>
									<td>
										{{ @$fetchedData->other_info == "" ? config('constants.empty') : @$fetchedData->other_info }}
									</td>
								</tr>
								<tr>
									<th>Validity Starts From</th>
									<td>
										{{ @$fetchedData->validity_start_from == "" ? config('constants.empty') : @$fetchedData->validity_start_from }}
									</td>
								</tr>
								<tr>
									<th>Validity Extension</th>
									<td>
										{{ @$fetchedData->validity_extension == "" ? config('constants.empty') : @$fetchedData->validity_extension }}
									</td>
								</tr>
								<tr>
									<th>Views Extension</th>
									<td>
										{{ @$fetchedData->views_extension == "" ? config('constants.empty') : @$fetchedData->views_extension }}
									</td>
								</tr>
								<tr>
									<th>Internet Connectivity</th>
									<td>
										{{ @$fetchedData->internet_connectivity == "" ? config('constants.empty') : @$fetchedData->internet_connectivity }}
									</td>
								</tr>
								<tr>
									<th>Dispatch Time</th>
									<td> 
										{{ @$fetchedData->dispatch_time == "" ? config('constants.empty') : @$fetchedData->dispatch_time }}
									</td>
								</tr>
							</table>
						</div>
					</div>
				<!--All Product Information End -->	
				
				<!-- Features Panel Start -->		
					<div role="tabpanel" class="tab-pane feature-info" id="features">
						@if(@$fetchedData->features == '')
							<div class="no_data">	
								No Feature Description Available!
							</div>
						@else
							{!! @$fetchedData->features !!}
						@endif	
					</div>
				<!-- Features Panel End -->
				
				<!-- Demo Video Start -->
					<div role="tabpanel" class="tab-pane" id="demo_videos">
						<div class="row">
							<?php
								// Function to convert the Youtube URL to Embed code.
								/* function convertYoutube($string) {
									return preg_replace(
										"/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
										"<iframe src=\"https://www.youtube.com/embed/$2\" allowfullscreen></iframe>",
										$string
									);
								} */	
							?>
							@if(count(@$productDemoInfo) > 0) 
								@foreach(@$productDemoInfo as $key=>$demo) 
									@if( @$demo->demo_videos != "")
										<div class="col-sm-4">	
											<iframe src="{{@$demo->demo_videos}}" class="demo-videos"></iframe>	
										</div>	
									@endif
								@endforeach	
							@else
								<div class="no_data">	
									No Demo Video Available yet.	
								</div>
							@endif
						</div>		
					</div>
				<!-- Demo Video End -->	

				<!-- Review Panel Start -->
					<div role="tabpanel" class="tab-pane" id="review">
						@if(count(@$productReviewInfo) > 0)
							@foreach(@$productReviewInfo as $key=>$review)
								<b>{{@$review->studentData->first_name}} {{@$review->studentData->last_name}} </b> : {{@$review->review}}
								<br /><br />	
							@endforeach	
						@else
							<div class="no_data">	
								No Review Available yet.
							</div>		
						@endif
					</div>
				<!-- Review Panel End -->
				
				<!-- About Professor Start -->	
					<div role="tabpanel" class="tab-pane" id="about_professsor">
						@if(@$fetchedData->professor->about_faculty == '')
							<div class="no_data">
								No Information about the Professor yet.
							</div>
						@else
							<div>
								{!! @$fetchedData->professor->about_faculty !!}
							</div>
						@endif
					</div>
				<!-- About Professor End -->			
			</div>
		</div>
	</div>
</div>


<!-- Other Classes (I) Start -->
	<div class='row'>
		<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
			<div class="fancy">
				<span class="h3">Other Classes</span>
			</div>
			@if(count(@$fetchedAllOtherClasses) !== 0)
				<div cellspacing="0" class="bestsell-list" id="product-list-table">
					<ul id="owl-demo" class="product-list list-unstyled owl-carousel owl-theme">
						@foreach (@$fetchedAllOtherClasses as $list)
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
										<a href="javascript:void(0);" class="product-image" target="_blank">
											@if(@$list->image == '')
												<img classs="img-responsive"src="{{URL::to('/public/img/Frontend/img/not_found.jpg')}}">
											@else
												<img classs="img-responsive"src="{{URL::to('/public/img/product_img/'.@$list->image)}}">
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
												{{ @$list->professor->first_name == "" ? config('constants.empty') : str_limit(@$list->professor->first_name, '25', '...').' '.str_limit(@$list->professor->last_name, '25', '...') }}
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
											<div>
												<a href="{{URL::to('/view_product/'.base64_encode(convert_uuencode(@$list->id)))}}" class="link-compare btn btn-info"> 
													<i class="fa fa-eye-slash" aria-hidden="true"></i>
														View Detail
												</a>
											</div>
										</div>
									</div>
								</div>
							</li>
						@endforeach	
					</ul>
				</div>
			@else
				<div cellspacing="0" class="bestsell-list no_data" id="product-list-table">	
					There are no related another class yet. 
				</div>
			@endif		
		</div>
	</div>
<!-- Other Classes (I) End -->
		
<!-- Other Classes (II) Start -->	
	<div class='row'>
		<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
			<div class="fancy">
				<span class="h3">Other Classes</span>
			</div>
			@if(count(@$fetchedAllOtherClassesOther) !== 0)
				<div cellspacing="0" class="bestsell-list" id="product-list-table1">
					<ul id="owl-demo1" class="product-list list-unstyled owl-carousel owl-theme">
						@foreach (@$fetchedAllOtherClassesOther as $list)
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
										<a href="javascript:void(0);" class="product-image" target="_blank">
											@if(@$list->image == '')
												<img classs="img-responsive"src="{{URL::to('/public/img/Frontend/img/not_found.jpg')}}">
											@else
												<img classs="img-responsive"src="{{URL::to('/public/img/product_img/'.@$list->image)}}">
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
												{{ @$list->professor->first_name == "" ? config('constants.empty') : str_limit(@$list->professor->first_name, '25', '...').' '.str_limit(@$list->professor->last_name, '25', '...') }}
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
											<div>
												<a href="{{URL::to('/view_product/'.base64_encode(convert_uuencode(@$list->id)))}}" class="link-compare btn btn-info"> 
													<i class="fa fa-eye-slash" aria-hidden="true"></i>
														View Detail
												</a>
											</div>
										</div>
									</div>
								</div>
							</li>
						@endforeach	
					</ul>
				</div>
			@else
				<div cellspacing="0" class="bestsell-list no_data" id="product-list-table1">	
					There are no related another class yet. 
				</div>
			@endif		
		</div>
	</div>
<!-- Other Classes (II) End -->
	
@if(@Auth::check())	
	<!-- Review Start -->	
	<div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
		<div class="modal-dialog modal-primary" role="document">
			<div class="modal-content">
				{{ Form::open(array('url' => '/add_review', 'name'=>"add-review")) }}
					<div class="modal-header">
						<h4 class="modal-title">Write your review</h4>
						<button class="close" type="button" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="card-body">
						<input type="hidden" name="user_id" value="{{@Auth::user()->id}}"/><!--Who gave Review -->
						<input type="hidden" name="product_id" value="{{@$fetchedData->id}}" /><!--Which Product Review -->
							<div class="form-group">
								<label class="popup-label" for="review">Your Review<em>*</em></label>
									{{ Form::textarea('review', '', array('class' => 'form-control review-textarea', 'placeholder'=>'Please write Review...', 'autocomplete'=>'new-password', 'data-valid'=>'required')) }}
									
								@if (@$errors->has('review'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('review') }}</strong>
									</span>
								@endif
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
						{{ Form::button('Submit', ['class'=>'btn btn-primary px-4', 'onClick'=>'customValidate("add-review")']) }}
					</div>
				{{ Form::close() }}		
			</div>
		</div>
	</div>
	<!-- Review End -->	
@endif

@endsection