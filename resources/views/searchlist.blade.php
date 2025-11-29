<?php 
use App\Http\Controllers\PackageController;
$dest = json_decode($destinationdetail);
//print_r($dest); die;
if(!empty($dest->data->packages->data)){
		foreach($dest->data->packages->data as $plist){
	?>
<div class="row">
	<div class="col-lg-12">
		<div class="row pkgwrapper">
			<div class="col-sm-3 pkgimg-box">
				<a href="{{URL::to('/destinations/'.$plist->packloc->slug.'/'.$plist->slug)}}" class="pkg-imgbx">
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
						<a href="{{URL::to('/destinations/'.$plist->packloc->slug.'/'.$plist->slug)}}">{{@$plist->package_name}}</a>
						<p>{{@$plist->details_day_night}}</p>
						<i>Top Inclusion</i>
						<ul>
						<?php 
						$explodee = explode(',',@$plist->package_topinclusions);
						for($i=0; $i<count($explodee);$i++ ){
							$pdat = PackageController::topInclusion($explodee[$i]);
							$topinclusions = json_decode($pdat);
							
						?>
						<li><img width="20" height="20" src="{{@$dest->data->image_topinclusion_path}}{{@$topinclusions->data->image}}">{{@$topinclusions->data->name}}</li>
						<?php } ?>
						
						</ul>
					</div>							
					<div class="col-sm-3 txt-cntr">
						<span>
						@if($plist->price_on_request == 1)
							<div class="pkg-pricebx price_request">
								<strong>Price On Request</strong>
							</div>
						@else
							
						<?php
						$discount = ($plist->sales_price - $plist->offer_price) /100; 
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
						<a href="{{URL::to('/destinations/'.$plist->packloc->slug.'/'.$plist->slug)}}" class="pkglinks-view text-center">View Details</a>
						<a href="javascript:;" data-toggle="modal" data-target="#inquirymodal" onclick="" class="pkglinks-enquire text-center">Enquire Now</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } }else{
	?>
	<div class="row">
		<div class="col-lg-12">
			<div class="row pkgwrapper">
				<h2>Packages not found.</h2>
			</div>
		</div>
	</div>	
	<?php
} ?>	