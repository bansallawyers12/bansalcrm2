@extends('layouts.dashboard_frontend')
@section('content')
<?php use App\Http\Controllers\PackageController; ?>
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
				<?php 
			$dest = json_decode($destinationdetail);
			$filterlist = json_decode($filterlist);
		?>
					<h2>{{@$dest->data->destination_detail->myloc->name}} Tour Packages</h2>
					<article data-readmore="" aria-expanded="false" id="rmjs-1" class="read-more-fade" style="">  
					<?php echo htmlspecialchars_decode(stripslashes(@$dest->data->destination_detail->description)); ?>
					</article>
					
				</div>
				<div class="col-md-3">
					<div class="custom-search-input-2 inner-2">
					<form id="searchform" action="{{URL::to('/search/')}}" method="get">
						<div class="form-group">
							<input class="form-control search-query" name="term" type="text" placeholder="Type Destination">
							
							<i class="icon_search"></i>
							
						</div>  
						<input type="submit" class="btn_search" value="Search">
						</form>
					</div>
					<div class="filterpanel" id="filters_col">
						<h5><span aria-hidden="true" class="icon_adjust-horiz"></span> Refine Search</h5> 
						<button type="button" class="link button clear_filter">CLEAR</button>
						<input id="mslug" type="hidden" value="{{$slug}}">
						<div class="applied_filter">
							<ul>
								
							</ul>
						</div>
						<!-- /custom-search-input-2 -->
						<div class="filter_type departure_city">
							<h6>Departure City</h6>
							<select id="dcity" class="form-control">
								<option>- Select Departure City -</option>
								@foreach($filterlist->data->cities as $cities)
								<option value="{{@$cities->id}}">{{@$cities->name}}</option>
								@endforeach
							</select>
						</div>
						<div class="filter_type budge_filter">
							<h6>Budget Per Person</h6>
							<input type="text" id="budgetrange" name="budgetrange" value="">   
						</div>
						<div class="filter_type duration_filter">
							<h6>Duration</h6>
							<input type="text" id="durationrange" name="durationrange" value="">   
						</div>	
						<div class="filter_type themes_filter">
							<h6>Themes</h6>
							<div class="custom-search-input-2 inner-2">
								<div class="form-group">
									<input class="form-control" id="myInput" type="text" onkeyup="myFunction()" placeholder="Search For More">
									<i class="icon_search"></i>
								</div>
							</div>			  					
							<ul id="myUL">
							@foreach($filterlist->data->holidaytypes as $holidaytypes)		
									<?php
										$countdocname = explode(',',$holidaytypes->docname);
									?>
								<li>
									<label>
										<input dataname="{{@$holidaytypes->name}}" value="{{@$holidaytypes->id}}" name="themes" type="checkbox" class="icheck myListicheck">{{@$holidaytypes->name}} <small>({{@count(@$countdocname)}})</small>
									</label>
								</li>
							@endforeach	
							</ul> 
							<a href="javascript:;" id="loadMore">View more</a>
						</div>
					</div> 
				</div> 
				<div class="col-md-9">
				<div class="row">
						<div class="col-lg-8">
							<h2>{{@$dest->data->destination_detail->myloc->name}} Tour Packages</h2>
						</div>                
						<div class="col-lg-4" style="padding-top:20px; text-align:right;">
							<div class="row">
							</div>
						</div>
					</div>  
					<div id="mloader" style="position: fixed;left: 45%;padding: 20px;top: 40%;opacity: 0.5;border: 1px solid rgb(102, 102, 102);z-index: 100;background-color: rgb(255, 255, 255);display: none;"><img src="{!! asset('public/img/loader.gif') !!}" alt="Loading"></div>
					<div id="ajaxResultContainer">
						<div class="row">
							<div class="col-lg-12">
								<div class="tourpack-pagtbox">
									<div class="row">
										<!--<div class="col-lg-4 col-md-4 col-sm-12 pagtextbx pagt-tetbx">Showing : 1-20 out of 62</div>                        
										<div class="col-lg-5 col-md-5 col-sm-12 pagtwrap">
											<ul class="pagination">
												<li class="disabled"><a href="#">Prev</a></li>
												<li class="active"><a href="#">1</a></li>
												<li><a href="#" onclick="showPage(1); return false;">2</a></li>
												<li><a href="#" onclick="showPage(2); return false;">3</a></li>
												<li><a href="#" onclick="showPage(3); return false;">4</a></li>
												<li><a href="#" onclick="showPage(1); return false;">Next</a></li>
											</ul>                           
										</div>                        
										<div class="col-lg-3 col-md-3 col-sm-12 pagtboxsel sorting-box">
											<div class="custom-select selct-bg">
												<select class="form-control" name="sortBy" id="SortBy">
													<option value="order_id asc" selected="selected">Sort by</option>
													<option value="days asc">Duration Short</option>
													<option value="days desc">Duration Long</option>
													<option value="price_inr asc">Price Lowest First</option>
													<option value="price_inr desc">Price Highest First</option>
												</select>
											</div>
										</div>-->
									</div>
								</div>
							</div>
						</div>   
						<?php 
						//echo '<pre>'; print_r($dest->data->packages);
							foreach($dest->data->packages->data as $plist){
						?>
						<div class="row">
							<div class="col-lg-12">
								<div class="row pkgwrapper">
									<div class="col-sm-3 pkgimg-box">
										<a href="{{URL::to('/destinations/'.$dest->data->destination_detail->myloc->slug.'/'.$plist->slug)}}" class="pkg-imgbx">
											<img data-original="{{@$dest->data->image_base_path}}{{@$plist->media->images}}" width="250" class="img-fluid lazy" alt="{{@$plist->package_image_alt}}" title="" src="{{@$dest->data->image_base_path}}{{@$plist->media->images}}" style="display: block;">
										</a>
									</div>                                
									<div class="col-sm-9">
										<div class="row">
											<div class="col-sm-9 pkgtext-box">
												<span>{{@$plist->no_of_nights}} Nights / {{@$plist->no_of_days}} Days</span>
												@if(@$plist->tour_code != '')
												<span class="code_span">Tour Code: <strong>{{@$plist->tour_code}}</strong></span>
												@endif
												<a href="{{URL::to('/destinations/'.$dest->data->destination_detail->myloc->slug.'/'.$plist->slug)}}">{{@$plist->package_name}}</a>
												<p>{{@$plist->details_day_night}}</p>
												<?php if(@$plist->package_topinclusions != ''){ ?>
												<i>Top Inclusion</i>
												<ul>
												<?php 
												$explodee = explode(',',@$plist->package_topinclusions);
												if(!empty($explodee)){
												for($i=0; $i<count($explodee);$i++ ){
													$pdat = PackageController::topInclusion($explodee[$i]);
													$topinclusions = json_decode($pdat);
													
												?>
												<li>
												@if(!empty($typet->topinclusion))
													@if(@$typet->topinclusion->image != '')
														<img width="20" height="20" src="{{@$dest->data->image_topinclusion_path}}{{@$topinclusions->data->topinclusion->image}}">
													@else
														<img width="20" height="20" src="{{@$dest->data->image_topinclusion_path}}{{@$topinclusions->data->image}}">
													@endif
												@else
													<img width="20" height="20" src="{{@$dest->data->image_topinclusion_path}}{{@$topinclusions->data->image}}">
												@endif
												{{@$topinclusions->data->name}}</li>
												<?php } } ?>
												
												</ul>  
												<?php } ?>
												<div class="pack_category">
													<ul>
														<li>Honeymoon</li>
														<li>Family</li>
														<li>Adventure</li>
														<li>Popular</li>  
														<li>Honeymoon</li>
													</ul>
												</div>
											</div>			 				
											<div class="col-sm-3 txt-cntr">
												<span>
												@if($plist->price_on_request == 1)
													<div class="pkg-pricebx price_request">
														<strong>Price On Request</strong>
													</div>
												@else
													
												<?php
												$discount = (($plist->sales_price - $plist->offer_price) /$plist->sales_price ) * 100; 
												?>
													<div class="pkg-pricebx" style="font-size: 15px;">
														<p class="appendBottom10"><span class="font12 redText appendRight5">Save <i class="fa fa-inr"></i> <?php echo $plist->sales_price - $plist->offer_price; ?></span><span class="holidaySprite discountTag"></span><span class="discount_box font11 latoBold whiteText">{{@$discount}}%</span></p>
														<strike><strong style="color:#aba5a5"><i class="fa fa-inr"></i> {{@$plist->sales_price}}</strong></strike>
													</div>
													<div class="pkg-pricebx" style="font-size: 15px;">
														<strong><i class="fa fa-inr"></i> {{@$plist->offer_price}}</strong>
													</div>
												@endif
												</span> 
												<a href="{{URL::to('/destinations/'.$dest->data->destination_detail->myloc->slug.'/'.$plist->slug)}}" class="pkglinks-view text-center">View Details</a>
												<a href="javascript:;" datapacid="{{$plist->id}}" data-toggle="modal"  data-target="#inquirymodal" onclick="" class="pkglinks-enquire text-center myqueryli">Enquire Now</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php } ?>	 
						<!--<nav aria-label="..." class="custom_pagination">
							<ul class="pagination pagination-sm">
								<li class="page-item disabled">
									<a class="page-link" href="#" tabindex="-1">Previous</a>
								</li>
								<li class="page-item"><a class="page-link" href="#">1</a></li>
								<li class="page-item"><a class="page-link" href="#">2</a></li>
								<li class="page-item"><a class="page-link" href="#">3</a></li>
								<li class="page-item">
									<a class="page-link" href="#">Next</a>
								</li>
							</ul>
						</nav>-->
						<!-- /pagination -->	
					</div>
				</div>
			</div>  
		</div>
	</div>
</div>
<script>
var miprice = '{{@$filterlist->data->min_price}}';
var maxprice = '{{@$filterlist->data->max_price}}';
var min_nigt = '{{@$filterlist->data->min_nigt}}';
var max_day = '{{@$filterlist->data->max_day}}';
</script>
@endsection