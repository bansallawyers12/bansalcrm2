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
					<a href="{{URl::to('/free-resource')}}">Free Resources</a>
				</li>
			</ol>
		</nav>
	</div>
</div>
<div class="row product-list-page">
	{{ Form::open(array('name'=>"search-form", 'method' => 'get', 'autocomplete'=>'off')) }}
	{{ Form::hidden('search_term', Request::get('search_term'), array('id'=>'search_term')) }}
	{{ Form::hidden('search_term_resource', Request::get('search_term_resource'), array('id'=>'search_term_resource')) }}
		<div class="col-lg-2 col-sm-12 col-md-2 col-xs-12">
			<div class="filters filter1">
				<a href="{{URL::to('/free-resource')}}" class="faculty-filter-btn btn btn-info filled">Filter <span aria-hidden="true" class="float-right">×</span></a>
				<a href="javascript:void(0);" class="faculty-filter-btn btn btn-info free-search" data-val="3"> 
					<i class="fa fa-file-pdf-o" aria-hidden="true"></i> 
					PDF
				</a>
				<a href="javascript:void(0);" class="faculty-filter-btn btn btn-info filled free-search" data-val="1"> 
					<i class="fa fa-file-audio-o" aria-hidden="true"></i> 
					Audio
				</a>
				<a href="javascript:void(0);" class="faculty-filter-btn btn btn-info free-search" data-val="2"> 
					<i class="fa fa-file-o" aria-hidden="true"></i> 
					Video
				</a>
			</div>
		</div>
		<div class="col-lg-10 col-sm-12 col-md-10 col-xs-12 col-7-products">
			<div class="main-product-area">
				<h3>Free Resources</h3>
				<div class="faculty-tab">
					<a href="javascript:void(0);" class="btn btn-info free-search-resource" data-val="1"> CA Final</a>
					<a href="javascript:void(0);" class="btn btn-info free-search-resource" data-val="2">CA Inter</a>
				</div>	
	{{ Form::close() }}					
				
				<div cellspacing="0" class="products-list-products" id="product-list-table">
					<!-- Flash Message Start -->
						<div class="server-error">
							@include('../Elements/flash-message')
						</div>
						<div class="custom-error-msg">
						</div>	
					<!-- Flash Message End -->
				
					<ul class="product-list faculty-list list-unstyled">
						@if(count(@$lists) !== 0)
							@foreach (@$lists as $list)
								<?php 
									$breakExtension = @$list->content; 
									$explode = explode(".", $breakExtension);	
								?>
								<li class="product-bucket" id="res_{{@$list->id}}">
									<div class="text-center new-list">
										<div class="featured imgContainers">
											<a href="javascript:void(0);" class="product-image">
												@if(@$list->free_img == '')
													<img class="img-responsive faculty-img" src="{{URL::to('/public/img/Frontend/img/not_found.jpg')}}">
												@else
													<img class="img-responsive faculty-img" src="{{URL::to('/public/img/free_downloads/free_imgs')}}/{{@$list->free_img}}">
												@endif		
											</a>
										</div>
										<div class="fixedHeight">
											<div class="faculty-name relative"> 
												<a href="javascript:void(0);" class="name">
													<strong>Subject</strong> : {{ @$list->subject == "" ? config('constants.empty') : @$list->subject}}
												</a>
												<a href="javascript:void(0);" class="name">
													<strong>Faculty</strong> : {{ @$list->professor_name == "" ? config('constants.empty') : @$list->professor_name}}
												</a>
												<!--<a href="javascript:void(0);" class="name">
													@if(@$list->resource == 1)
														CA Final
													@elseif(@$list->type == 2)
														CA Intern
													@endif
												</a>-->
											</div>
										</div>
										<div class="actions">
											<div class="add-to-links">
												<div class="faculty-action">
													@if(@$list->type == 1)
														<a href="javascript:void(0);" class="faculty-action-btn btn btn-info" title="This is Audio."> 
															<i class="fa fa-file-audio-o" aria-hidden="true"></i> 
														</a>
														@if(!@Auth::check())	
															<a href="javascript:void(0);" class="faculty-action-btn btn btn-info to-get-free-resource" data-val="{{@$list->id}}" data-query-string="<?php echo @$_SERVER['QUERY_STRING'];?>" data-type="view"> 
																<i class="fa fa-play" aria-hidden="true"></i>
															</a>
															<a href="javascript:void(0);" class="faculty-action-btn download-btn btn btn-info to-get-free-resource" data-val="{{@$list->id}}" data-query-string="<?php echo @$_SERVER['QUERY_STRING'];?>" data-type="download"> 
																<i class="fa fa-download" aria-hidden="true"></i>
															</a>	
														@else
															<a href="javascript:void(0);" id="res_view_{{@$list->id}}" class="faculty-action-btn btn btn-info view-free-resource" data-type="audio" data-src="{{URL::to('/public/img/free_downloads/audio/'.@$list->content)}}" data-extension="{{@$explode[1]}}" data-subject-name="{{@$list->subject}}" data-faculty-name="{{@$list->professor_name}}"> 
																<i class="fa fa-play" aria-hidden="true"></i>
															</a>
															<a href="{{URL::to('/download/'.base64_encode(convert_uuencode(@$list->id))).'/audio'}}" id="res_download_{{@$list->id}}" class="faculty-action-btn download-btn btn btn-info"> 
																<i class="fa fa-download" aria-hidden="true"></i>
															</a>	
														@endif			
													@elseif(@$list->type == 2)
														<a href="javascript:void(0);" class="faculty-action-btn btn btn-info" title="This is Video."> 
															<i class="fa fa-file-video-o" aria-hidden="true"></i> 
														</a>
														@if(!@Auth::check())
															<a href="javascript:void(0);" class="faculty-action-btn btn btn-info to-get-free-resource" data-val="{{@$list->id}}" data-query-string="<?php echo @$_SERVER['QUERY_STRING'];?>" data-type="view"> 
																<i class="fa fa-play" aria-hidden="true"></i>	
															</a>
															<a href="javascript:void(0);" class="faculty-action-btn download-btn btn btn-info to-get-free-resource" data-val="{{@$list->id}}" data-query-string="<?php echo @$_SERVER['QUERY_STRING'];?>" data-type="download"> 
																<i class="fa fa-download" aria-hidden="true"></i>
															</a>	
														@else	
															<a href="javascript:void(0);" id="res_view_{{@$list->id}}" class="faculty-action-btn btn btn-info view-free-resource" data-type="video" data-src="{{URL::to('/public/img/free_downloads/video/'.@$list->content)}}" data-extension="{{@$explode[1]}}" data-subject-name="{{@$list->subject}}" data-faculty-name="{{@$list->professor_name}}"> 
																<i class="fa fa-play" aria-hidden="true"></i>
															</a>
															<a href="{{URL::to('/download/'.base64_encode(convert_uuencode(@$list->id))).'/video'}}" id="res_download_{{@$list->id}}" class="faculty-action-btn download-btn btn btn-info"> 
																<i class="fa fa-download" aria-hidden="true"></i>
															</a>
														@endif		
													@elseif(@$list->type == 3)
														<a href="javascript:void(0);" class="faculty-action-btn btn btn-info" title="This is PDF."> 
															<i class="fa fa-file-pdf-o" aria-hidden="true"></i> 
														</a>
														@if(!@Auth::check())
															<a href="javascript:void(0);" class="faculty-action-btn btn btn-info to-get-free-resource" data-val="{{@$list->id}}" data-query-string="<?php echo @$_SERVER['QUERY_STRING'];?>" data-type="view_pdf"> 
																<i class="fa fa-file-o" aria-hidden="true"></i>
															</a>
															<a href="javascript:void(0);" class="faculty-action-btn download-btn btn btn-info to-get-free-resource" data-val="{{@$list->id}}" data-query-string="<?php echo @$_SERVER['QUERY_STRING'];?>"  data-type="download"> 
																<i class="fa fa-download" aria-hidden="true"></i>
															</a>	
														@else
															<a href="{{URL::to('/public/img/free_downloads/pdf/'.@$list->content)}}" id="res_view_pdf_{{@$list->id}}" class="faculty-action-btn btn btn-info" target="_blank"> 
																<i class="fa fa-file-o" aria-hidden="true"></i>
															</a>
															<a href="{{URL::to('/download/'.base64_encode(convert_uuencode(@$list->id))).'/pdf'}}" id="res_download_{{@$list->id}}" class="faculty-action-btn download-btn btn btn-info"> 
																<i class="fa fa-download" aria-hidden="true"></i>
															</a>
														@endif		
													@endif	
													
													@if(@$list->type == 1 || @$list->type == 2)
														<span class="faculty-time"> 
															<i class="fa fa-clock-o" aria-hidden="true"></i> 
															{{@$list->duration}}
														</span>
													@endif	
												</div>
											</div>
										</div>
									</div>
								</li>
							@endforeach	
						@else
							<li class="no_data">
								No Free Resource Found.
							</li>
						@endif		
					</ul>
				</div>
			</div>
		</div>
</div>
<!-- Audio & Video Modal Start -->	
<div class="modal fade" id="viewDetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-primary" role="document">
		<div class="modal-content">
				<div class="modal-header">
					FREE CONTENT FOR YOU!
					<button class="close pause-track" type="button" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="subject-free-resource">
						<strong>Subject : </strong><span class="subjectNameModal"></span>
					</div>	
					<div class="faculty-free-resource">
						<strong>Faculty : </strong><span class="facultyNameModal"></span>
					</div>
					<div class="getFreeResourceData">
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-secondary pause-track" type="button" data-dismiss="modal" style="background-color:#009999;">Close</button>
				</div>	
		</div>
	</div>
</div>
<!-- Audio & Video Modal End -->	
@endsection