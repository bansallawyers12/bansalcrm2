@extends('layouts.dashboard_frontend')
@section('content')
<?php use App\Http\Controllers\PackageController; ?>
	<?php 
			$dest = json_decode($packagedetail);
			//echo '<pre>'; print_r($dest); die;
		?>

<section class="pack_details start_bg_zoom">
	<div class="wrapper">
		<div class="container-fluid"> 
			<div class="row"> 
				<div class="details_image">
					<img src="{{@$dest->data->image_gallery_path}}{{@$dest->data->package_detail->bamedia->images}}" class="img-fluid" alt=""/>
					<div class="opacity_banner"></div>
				</div> 
			</div>
		</div>
		<div class="container">
			<div class="pack_banner_title">
				<div class="inner_title">
					<span class="count_days">{{@$dest->data->package_detail->no_of_nights}} Nights / {{@$dest->data->package_detail->no_of_days}} Days</span> 
					<h1 class="fadeInUp animated mytitle"><span></span>{{@$dest->data->package_detail->package_name}}</h1>
					<p><i class="fa fa-map-marker"></i> {{@$dest->data->package_detail->details_day_night}}</p>
				</div>				
				<div class="pack_price"> 
					<span class="banner_code_span">Tour Code: <strong>{{@$dest->data->package_detail->tour_code}}</strong></span>	
				 @if(@$dest->data->package_detail->price_on_request == 1)
						<strong>Price On Request</strong>
					@else	 
						<?php
						$discount = (@$dest->data->package_detail->sales_price - @$dest->data->package_detail->offer_price) /100; 
						?>
						<span>Price:</span>
						<div class="price_val"><strike><strong><i class="fa fa-inr"></i> {{@$dest->data->package_detail->sales_price}}</strong></strike>
						<span class="actual_price"><i class="fa fa-inr"></i> {{$dest->data->package_detail->offer_price}}</span></div>
				@endif 
						
				</div>	
			</div>
		</div>  
	</div>
</section>     
  
<div class="details_main">
	<div class="bg_color_1 pack_details" style="transform: none;">
		<nav class="secondary_nav sticky_horizontal" style="">
			<div class="container">
				<ul class="pack_tabs clearfix">
					<li><a href="#description" class="active">Overview</a></li>
					<li><a href="#inclu_exclu">Inclusion & Exclusion</a></li>
					<li><a href="#itinerary">Itinerary</a></li>
					<li><a href="#hotels">Hotels</a></li>
					<li><a href="#price">Price</a></li>
					<li><a href="#terms">Terms & Info</a></li> 
				</ul>
			</div> 
		</nav>
		<div class="container pos_relt">
			<div class="download_btn">
				@if(@$dest->data->package_detail->pdf !="")
				<a href="{{@$dest->data->pdfs}}{{@$dest->data->package_detail->pdf}}" download="{{@$dest->data->package_detail->slug}}" class="download_icon"><i class="fa fa-download"></i> Download Itinerary</a>
			@endif
				<div class="dropdown">
					<button class="sharebtn" class="dropdown-toggle" type="button" data-toggle="dropdown" id="dropdownbtn" ><i class="fa fa-share-alt"></i></button>
					<ul class="mydropdown-menu dropdown-menu" aria-labelledby="dropdownbtn">  
						<li>    
							<a id="WhatsApp" href="" target="_blank">
								<img src="{!! asset('public/img/whatsapp.png') !!}" class="SocialLinkImage" alt="WhatsApp" title="WhatsApp" style="vertical-align: middle;">WhatsApp</a>
						</li>
						<li>  
							<a id="Facebook" href="" target="_blank">
								<img src="{!! asset('public/img/facebook.png') !!}" class="SocialLinkImage" alt="Facebook" title="Facebook" style="vertical-align: middle;">Facebook</a>
						</li>
						<li>   
							<a id="Twitter" href="" target="_blank">
								<img src="{!! asset('public/img/twitter_opt.png') !!}" class="SocialLinkImage" alt="Twitter" title="Twitter" style="vertical-align: middle;">Twitter</a>
						</li>
						<li>
							<a id="LinkedIn" href="" target="_blank">
								<img width="30" height="30" src="{!! asset('public/img/linkedIn_PNG36.png') !!}" class="SocialLinkImage" alt="Twitter" title="Twitter" style="vertical-align: middle;">LinkedIn</a>
						</li>
					</ul>
					<script> 
						$(window).load(function () {
							var Title = '<?php echo $dest->data->package_detail->package_name; ?>';

							var WhatsAppHref = "https://api.whatsapp.com/send?text=" + encodeURIComponent(Title.trim()) + ' ' + encodeURIComponent(document.URL).replace('#', '');
							var FacebookHref = "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(window.location.href).replace('#', '') + '&title=' + encodeURIComponent(Title.trim());
							var TwitterHref = "https://twitter.com/intent/tweet?text=" + encodeURIComponent(Title.trim()) + '- ' + encodeURIComponent(window.location.href).replace('#', '');
							var LinkedInHref = "http://www.linkedin.com/shareArticle?mini=true&url=" + encodeURIComponent(window.location.href).replace('#', '');
							
							

							$("#WhatsApp").attr('href', WhatsAppHref);        
							$("#Facebook").attr('href', FacebookHref);
							$("#Twitter").attr('href', TwitterHref);
							$("#LinkedIn").attr('href', LinkedInHref);
						   // $("#GooglePlus").attr('href', GooglePlusHref);

						});

					</script>
				</div>
			</div>
		</div>	
		<div class="container margin_60_35" style="transform: none;">
			<div id="row_scroll" class="row" style="transform: none;">
				<div class="col-lg-8">
					<section class="common_section">
						<div class="slidersection gallery_section">   					
							<div id="carousel-thumb" class="carousel slide carousel-fade carousel-thumbnails" data-ride="carousel">
								<div class="carousel-inner" role="listbox">
									<?php $if = 0; ?>
									@foreach(@$dest->data->package_detail->packigalleries as $gli)
									<div class="carousel-item <?php if($if == 0){ echo 'active'; }else{} ?>">
										<img class="d-block w-100" src="{{@$dest->data->image_gallery_path}}{{@$gli->galleriesmedia->images}}" width="100%" height="300" alt="{{$gli->package_gallery_image_alt}}">
									</div>
									<?php $if++; ?>
								@endforeach
								</div>
								<a class="carousel-control-prev" href="#carousel-thumb" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#carousel-thumb" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
								 <ol class="carousel-indicators">
								 <?php $ifd = 0; ?>
								 @foreach(@$dest->data->package_detail->packigalleries as $gli)
									<li data-target="#carousel-thumb" data-slide-to="{{@$ifd}}" class="<?php if($ifd == 0){ echo 'active'; }else{} ?>"> <img class="d-block w-100" src="{{@$dest->data->image_gallery_path}}{{@$gli->galleriesmedia->images}}"
										class="img-fluid"></li>
										<?php $ifd++; ?>
										@endforeach
								  </ol>
							</div>
						</div>		
					</section>
					<section id="description" class="common_section">
						<h2>Description</h2>
						<article data-readmore="" aria-expanded="false" id="rmjs-1" class="read-more-fade" style="">   
						<?php echo htmlspecialchars_decode(stripslashes(@$dest->data->package_detail->package_overview)); ?>
						</article>
						<hr>
						<?php if(@$dest->data->package_detail->package_topinclusions != ''){ ?>
						<div class="topinclusion">
						
							<h2>Top Inclusion</h2>
							<div class="row">
								<div class="col-lg-12">
									<ul class="bullets">
									<?php 
									$explodee = explode(',',@$dest->data->package_detail->package_topinclusions);
									if(!empty($explodee)){
										for($i=0; $i<count($explodee);$i++ ){
										$pdat = PackageController::topInclusion($explodee[$i]);
										$topinclusions = json_decode($pdat);
									?>
										<li><img width="20" height="20" src="{{@$dest->data->image_topinclusion_path}}{{@$topinclusions->data->image}}">{{@$topinclusions->data->name}}</li>
									<?php } } ?> 
									</ul> 
								</div>
							</div>
					
						</div>	
							<?php } ?>	
					</section>
					
					<section id="inclu_exclu" class="common_section">						
						<div class="row">
							<div class="col-lg-6 brder_rgt">
								<h2>Inclusion</h2>
								<ul class="bullets">
									<?php 
										$explodeei = explode(',',@$dest->data->package_detail->package_inclusions);
										for($j=0; $j<count($explodeei);$j++ ){
											$pdat = PackageController::Inclusion($explodeei[$j]);
											$inclusions = json_decode($pdat);
									?>
									<li>{{@$inclusions->data->name}}</li>
								<?php } ?>
								</ul>
							</div> 
							<div class="col-lg-6">
								<h2>Exclusion</h2>
								<ul class="bullets">
								<?php 
									$explodeee = explode(',',@$dest->data->package_detail->package_exclusions);
									for($c=0; $c<count($explodeee);$c++ ){
										$pdat = PackageController::Exclusion($explodeee[$c]);
										$exclusions = json_decode($pdat);
									?>
									<li>{{@$exclusions->data->name}}</li>
									<?php } ?>
								</ul>
							</div>
						</div>						
					</section>
					<section id="itinerary" class="common_section">
						<h2>Itinerary</h2>
						<ul class="cbp_tmtimeline">
						@php
						$cs = 1;
						@endphp
						@foreach(@$dest->data->package_detail->packitinerary as $li)
						<li>
							<time class="cbp_tmtime" datetime=""><span>Day</span>
							</time>
							<div class="cbp_tmicon">
							{{@$cs}}
							</div>
							<div class="cbp_tmlabel">
								<!--<div class="hidden-xs">
									<img src="img/tour_plan_1.jpg" alt="" class="rounded-circle thumb_visit">
								</div>-->
								<h4>{{@$li->title}}</h4>								
								<?php echo @htmlspecialchars_decode(stripslashes(@$li->details)); ?>
								<?php $foodt = explode(",", rtrim(@$li->foodtype,",")); 
									if(@$li->foodtype !=""){
								?>
								<div class="itinery_meals">
									<h6>Meals:</h6>
									
									<ul class="bullets">
										<li><i class="fa fa-cutlery"></i></li>
										<?php 
										for($ki = 0; $ki <count($foodt); $ki++){
										?>
										<li>{{ucfirst(@$foodt[$ki])}}</li>
										<?php } ?>
									</ul>	
								</div>
								<?php } ?>
							</div>
						</li>						
							@php
							$cs++;
							@endphp
							@endforeach						
						</ul>  
					</section>  				
					<script> var gallerydata = new Array(); </script>
					<!-- /section -->
					<section id="hotels" class="common_section">
						<h2>Hotels</h2>
						<?php 
				$igh =0; ?>
						@foreach(@$dest->data->package_detail->packhotel as $hli)
						<div class="row hotel_row">
							<div class="col-lg-12">
								<div class="row">
									<div class="col-sm-2 col-xs-3 padding5px">
										<div class="packagedethotelimg">
											<div class="zoomicon">
												<img class="lozad" src="{!! asset('public/img/zoom.png') !!}" alt="zoom" data-loaded="true">
											</div>
											<a href="javascript:;" ng-src="{{@$dest->data->image_hotel_path}}{{$hli->hotel->image}}" class="gallerypopup">
											<img  alt="" title="{{$hli->hotel->image_alt}}" class="ng-scope" src="{{@$dest->data->image_hotel_path}}{{$hli->hotel->image}}">
										</a>
										</div>
									</div>
									<div class="col-sm-10 col-xs-9 padding5px">
										<div class="row">
											<div class="col-sm-8 padding5px">
												<div class="inner_hotel_pack"> 
													<div class="packagelistboxheading">
														<h3><a href="javascript:void(0)"  datid="<?php echo $igh; ?>" class="ng-binding hotelcontent">{{$hli->hotel->name}}</a></h3>
													</div>
													<div class="starmargin ng-binding" ng-bind-html="hotel.Categoryimg | rawHtml">
													<?php for($ik =0; $ik < $hli->hotel->hotel_category; $ik++){ ?>
														<img src="{!! asset('public/img/star.png') !!}" alt="Star Rating" title="Star Rating">
													<?php } ?>
													</div>
													<div class="textblack13cont topmargin10px">
														<span class="textblack13bold ng-scope" ng-if="hotel.Locality!=''">Locality:</span><span ng-if="hotel.Locality!=''" class="textblue13 optionalCategory ng-binding ng-scope" ng-bind-html="hotel.Locality | rawHtml"> {{$hli->hotel->address}}</span><!-- end ngIf: hotel.Locality!='' -->
													</div>
												</div>
											</div>
											<div class="col-sm-4 padding5px includedtxt text-right ng-binding">Included in trip</div>
										</div>
									</div> 
								</div>
							
							</div>
						</div>
						<script>
						gallerydata[<?php echo $igh; ?>] = {
					"description":'<?php echo htmlspecialchars_decode(stripslashes(@$hli->hotel->description)); ?>',
					"hotelname":'<?php echo $hli->hotel->name; ?>',
					"address":'<?php echo $hli->hotel->address; ?>',
					"star":<?php echo $hli->hotel->hotel_category; ?>,
				}
					</script>
					<?php $igh++;  ?> 
						@endforeach						
					</section>
					<section id="price" class="common_section">
						<h2>Price</h2>
						<article data-readmore="" aria-expanded="false" id="rmjs-2" class="read-more-fade" style="">   
						<?php echo htmlspecialchars_decode(stripslashes(@$dest->data->package_detail->price_summary)); ?>
						</article>
					</section>
					<section id="terms" class="common_section">
						<h2>Tour Policy</h2>
						<article data-readmore="" aria-expanded="false" id="rmjs-3" class="read-more-fade" style="">   
						<?php echo htmlspecialchars_decode(stripslashes(@$dest->data->package_detail->package_tourpolicy)); ?>
						</article>
					</section>
					<!-- /section -->
				</div>
				<!-- /col -->
				
				<aside class="col-lg-4" id="sidebar" style="position: relative; overflow: visible; box-sizing: border-box; min-height: 1px;">
					<div class="pkgform-wrapper">
						<div class="cont-wth1">
							<div class="pkgform-headbx text-center">QUICK CONTACT <span class="title-arrow"></span></div>
							<div class="pkgform-box">
								{{ Form::open(array('url' => 'enquiry-contact', 'name'=>"add-query", 'autocomplete'=>'off','id'=>'enquiryco')) }}
								<span class="customerror"></span>
									<input type="text" data-valid = 'required' name="name" class="form-control" value="" placeholder="Name">                                
									<input type="text" data-valid = 'required' name="email" class="form-control" value="" placeholder="Email">
									<input type="text" data-valid = 'required' name="phone" class="form-control" value="" placeholder="Phone"> 
									<input type="text" data-valid = 'required' name="city" class="form-control" value="" placeholder="City">
									<div class="form-group" >
										<input type="text" data-valid = 'required' id="input_date" name="traveldate" class="form-control" value="" placeholder="Travel Date">	
									</div>
									<div class="row"> 
										<div class="col-sm-6 col-xs-6 codwh">
											<select class="form-control" name="adults">
												<option value="">Adults*</option>
												<?php
												for($ai=1;$ai<=10;$ai++){
													?>
													<option value="{{$ai}}">{{$ai}}</option>
													<?php
												}
												?>
											</select>
										</div>                                    
										<div class="col-sm-6 col-xs-6 leftpd">
											<select class="form-control" name="children">
												<option value="">Children (5-12 yr)</option>
												<?php
												for($ck=1;$ck<=10;$ck++){
													?>
													<option value="{{$ck}}">{{$ck}}</option>
													<?php
												}
												?>
											</select>
									   </div>
									</div>                                
									<textarea class="form-control" type="text" name="add_info" placeholder="Want to customize this package? Tell us more"></textarea>
									<div class="row">
										<div class="col-sm-7 col-xs-8 codwh">
											<input data-valid = 'required captcha' class="form-control" type="text" name="captcha" value="" placeholder="Enter Code" maxlength="4">
										</div>
										<div class="col-sm-5 col-xs-4 codwh-1"> 
									<?php $code=rand(1000,9999); ?>
									<input type="hidden" name="code" value="{{$code}}">
											<img src="{{route('sicaptcha')}}?code={{$code}}" class="img-responsive" alt="Captcha" width="65" height="25">
												
										</div>
									</div>                                
										<input type="hidden" name="package_id" value="{{$dest->data->package_detail->id}}">
									
									{{ Form::button('Submit', ['class'=>'submitbtt', 'onClick'=>'customValidate("add-query")' ]) }}
								 {{ Form::close() }}
							</div>  
						</div>
					</div>   
				</aside>
			</div> 
			<div class="row">	
				<div class="similar_packages">   
					<div class="main_title_2">
						<span><em></em></span>
						<h2>Similar Packages</h2>
					</div>
					<div id="reccomended" class="owl-carousel owl-theme">
						@foreach(@$dest->data->related_pack->data as $rplist)
						<div class="item">
							<div class="box_grid">
								<figure>
									<a href="#0" class="wish_bt"></a>
									<a href="{{URL::to('/destinations/'.$dslug.'/'.$rplist->slug)}}"><img src="{{@$dest->data->image_gallery_path}}{{@$rplist->media->images}}" class="img-fluid" alt="" width="800" height="533"><div class="read_more"><span>Read more</span></div></a>
									<small>Historic</small>
								</figure>
								<div class="wrapper">
									<h3><a href="{{URL::to('/destinations/'.$dslug.'/'.$rplist->slug)}}">{{$rplist->package_name}}</a></h3>
									<p>{{$rplist->details_day_night}}</p>
									@if(@$rplist->price_on_request == 1)
									<span class="price">Price on Request</span>
								@else
									<span class="price">From <strong><i class="fa fa-inr"></i>  {{$rplist->offer_price}}</strong> /per person</span>
									@endif
								</div>
								<ul>
									<li><i class="icon_clock_alt"></i> {{$rplist->no_of_nights}}N/{{$rplist->no_of_days}}D</li>
								</ul>
							</div> 
						</div>  
						<!-- /item -->  
						@endforeach
					</div>
					<!-- /carousel --> 
					<div class="container">
						<p class="btn_home_align"><a href="tours-grid-isotope.html" class="btn_1 rounded">View all Tours</a></p>
					</div>			
				</div>	
			</div> 
			<!-- /row -->
		</div> 
		<!-- /container -->
	</div>
</div>
@endsection