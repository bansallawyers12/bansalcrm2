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
					<a href="{{URl::to('/test-series')}}">Test Series</a>
				</li>
			</ol>
		</nav>
		<!-- Flash Message Start -->
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
		<!-- Flash Message End -->
	</div>
</div>
<div class="row test-series">
	<h2 class="main-cms-title">Test Series</h2>
	<div class="cms-desc test-series-detail col-lg-12 col-sm-12 col-md-12 col-xs-12">
		{!! @$data->content !!}
	</div>
</div>

@if(count(@$courses) !== 0)
	<div class="row h-100 row align-items-center">
		<div class="container">
			<div class="col-md-12 col-sm-6 text-center">
				<div class="form-group">
					<img class="select_test_series" src="{!! asset('public/img/Frontend/img/select.png') !!}">
					<label>SELECT COURSE</label>
				</div>
			</div>
			<div class="col-md-12 col-sm-12">
				<div class="col-md-4 col-sm-6">
				</div>
				<div class="col-md-4 col-sm-6">
					{{ Form::open(array('name'=>"search-form", 'method' => 'get', 'autocomplete'=>'off')) }}
						<div class="form-group">	
							<div class="text-center">
								<select name="search_term" class="form-control test_series_course">
									<option value="">Select Course</option>
									@foreach (@$courses as $course)
										<option value="{{ @$course->id }}" @if(@Request::get('search_term') == @$course->id) selected  @endif>
											{{ @$course->course_name }}
										</option>
									@endforeach
								</select>
							</div>
						</div>
					{{ Form::close() }}		
				</div>
				<div class="col-md-4 col-sm-6">
				</div>	
			</div>		
		</div>
	</div>
	<?php	if(!empty(@$fetchedData)) 
				{ 
	?>
					<div class="row test-series">	
						<div class="container">	
							<?php foreach($fetchedData as $key=>$data) 
									{ 
										if($key == 0){
											$color = 'orange';
										} else if($key == 1){
											$color = 'green';
										}  else if($key == 2){
											$color = 'yellow';
										}
							?>
										<div class="col-md-3 col-sm-6">
										</div>	
										<div class="col-md-6 col-sm-6">
											<div class="pricingTable <?php echo $color; ?>">
												<div class="pricingTable-header">
													<span class="heading">
														<h3><?php echo @$data['which_test_series_type']; ?></h3>
													</span>
													<span class="price-value">
														<span class="currency">
															<i class="fa fa-inr" aria-hidden="true"></i>
														</span>
														<span class="indi_test_series_type_price{{@$data['id']}}">	
															0
														</span>	
														<span class="mo"> Total</span>
													</span>
												</div>
												<?php foreach($data['which_group'] as $data1){ ?>	
													<div class="pricingContent">
														<h4 class="text-left"><?php echo @$data1['group_name']; ?></h4>
														<table class="table text-left">
															<tr>
																<th>Subject</th>
																<th>Fees</th>
															</tr>	
															<?php
																if(!empty($data1['data'])){
																	foreach($data1['data'] as $data2){ ?>
																		<tr>
																			<td>
																				<input type="checkbox" class="test_series_subject" data-subject-id="<?php echo @$data2['id']; ?>" data-price="<?php echo @$data2['price']; ?>"   data-test-series-id="<?php echo @$data['id']; ?>" /> 
																				<?php echo @$data2['subject_name']; ?>
																			</td>
																			<td>
																				<button type="button" class="btn price-test-series-subject">
																					<i class="fa fa-inr" aria-hidden="true"></i> 
																					<?php echo @$data2['price']; ?>
																				</button>
																			</td>
																		</tr>		
															<?php 	}
																}
																else
																	{
															?>	
																		<tr>
																			<td colspan="6" class="no_data">
																				No Subject found in this Group.
																			</td>	
																		</tr>	
															<?php	
																	}	
															?>			
														</table>
													</div>
												<?php } ?>		
											</div>
										</div>
										<div class="col-md-3 col-sm-6">
										</div>	
							<?php	} ?>
						</div>		
					</div>
					<div class="row test-series">	
						<div class="container">
							<div class="col-md-4 col-sm-6">
								<div class="pricingTable-sign-up">
									<input type="hidden" class="count" value="0" />
									<input type="hidden" class="discount_val" value="0" />
									<input type="hidden" class="discount" value='<?php echo json_encode($discount); ?>' />
									<input type="hidden" class="total" value="0" />	
									{{ Form::button('DISCOUNT <i class="fa fa-inr" aria-hidden="true"></i> <span class="discount_text">0</span>', ['class' => 'btn-discount', 'type' => 'button']) }}
								</div>	
							</div>
							<div class="col-md-4 col-sm-6 text-center">
								<a href="{{URL::to('/public/img/plans/'.@$courseDetails->plans)}}" target="_blank">
									<img src="{{ asset('public/img/pdf.ico') }}" class="pdf-icon" style="width:25%;"/>
								</a>
								<p class="pdf-name">
									@if(@$courseDetails->id == 1)
										Test series schedule old course
									@else
										Test series schedule new course
									@endif	
								</p>
							</div>
							<div class="col-md-4 col-sm-6 total-price">	
								<div class="pricingTable-sign-up">
									{{ Form::open(array('url' => '/test_series_checkout', 'name'=>"test_series_checkout", 'autocomplete'=>'off')) }}
										{{ Form::hidden('user_id', @Auth::user()->id) }}
										{{ Form::hidden('subject_ids', '', array('class'=>'subject_ids')) }}
										
										{{ Form::button('PAY <i class="fa fa-inr" aria-hidden="true"></i> <span class="grand_total_test">0</span>', ['class' => 'btn-block main-test-series-button', 'type' => 'submit', 'disabled'=>'disabled']) }}
										
									{{ Form::close() }}	
								</div>
							</div>
								
						</div>
					</div>	
	<?php 	} 
			else 
			{ 	
	?>
				<div class="row test-series">	
					<div class="container no_data">	
						Please select Course to get the related Subjects.
					</div>
				</div>			
	<?php 	} ?>	
@endif		
@endsection