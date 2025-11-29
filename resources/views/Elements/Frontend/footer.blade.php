<?php	
	$officequery 		= \App\OurOffice::where('id', '!=', '')->where('status', '=', 1);		
	$officeData 		= $officequery->count();	//for all data
	$officelists		=  $officequery->orderby('id','DESC')->paginate(4);
?>
<style>
@media screen and (min-width: 769px) {
    .phoneCls { padding-top: 18px;}
}
</style>
<footer class="footer-area">
	<!-- Main Footer Area -->
	<div class="main-footer-area">
		<div class="container-fluid">
			<div class="row">
				<!-- Footer Widget Area -->
				<div class="col-12 col-sm-12 col-md-3 col-lg-3">
					<div class="footer-widget">
						<div class="footer_logo">
							<a class="logo_link" href="<?php echo URL::to('/'); ?>">
								<img src="{{asset('public/img/logo_img')}}/<?php echo @\App\ThemeOption::where('meta_key','logo')->first()->meta_value; ?>" alt="">
							</a>
						</div>
						<div class="widget-title">
							<h5 style="color:#FFFFFF">Bansal Immigration Consultants</h5>
							<p>MARN: 2418466<br/> <!--<a href="https://www.mara.gov.au/tools-for-agents-subsite/Files/code-of-conduct.pdf" target="_blank" rel="noopener">Code of Conduct</a>--><a href="https://bansalcrm.com/public/code-of-conduct-march-2022.pdf" target="_blank" rel="noopener">Code of Conduct</a></p>
						</div>
					</div>
				</div>
				<div class="col-12 col-sm-12 col-md-9 col-lg-9">
					<div class="row office_row">
						@foreach (@$officelists as $key=>$list)	
						<div class="col-12 col-sm-6 col-md-6 col-lg-4 office_info_col">
							<div class="footer-widget">
								<h4>{{@$list->name}}</h4>
								<ul class="office_list">
									<?php if(@$list->address != '') { ?>
									<li><i class="fa fa-map-marker"></i> {{@$list->address}}</li>
									<?php 
                                    } ?>
                                  
									<?php		
									if(@$list->phone != '') {
                                        $str = '<i class="fa fa-phone"></i> <a href="tel:'.$list->phone.'">'.$list->phone.'</a>&nbsp;&nbsp;';
                                    } if(@$list->phone2 != '') {
                                        $str .= '<i class="fa fa-phone"></i> <a href="tel:'.$list->phone2.'">'.$list->phone2.'</a>';
                                    } ?>
                                  
                                    <?php if($key <3){ ?>
                                        <li class="phoneCls"><?php echo $str;?></li>
                                    <?php } else { ?>
                                        <li><?php echo $str;?></li>
                                    <?php } ?>

                                    <?php if(@$list->email != '') {	?>
									<li><i class="fa fa-envelope"></i> <a href="tel:{{@$list->email}}">{{@$list->email}}</a></li>
									<?php } ?>
								</ul>
								<div class="footer_social_info">
									<ul class="social_link">										
										<?php $fbicon = @\App\ThemeOption::where('meta_key','facebook')->first()->meta_value; ?>
										<?php if($fbicon !=  ''){  ?>
										<li class="fb_icon"><a href="<?php echo $fbicon; ?>"><i class="fa fa-facebook"></i></a></li>
										<?php } ?>
										<?php $twicon = @\App\ThemeOption::where('meta_key','twitter')->first()->meta_value; ?>
										<?php if($twicon !=  ''){  ?>
										<li class="tw_icon"><a href="<?php echo $twicon; ?>"><i class="fa-brands fa-square-x-twitter"></i></a></li>
										<?php } ?>
										<?php $lkedicon = @\App\ThemeOption::where('meta_key','linkedin')->first()->meta_value; ?>
										<?php if($lkedicon !=  ''){  ?>
										<li class="lked_icon"><a href="<?php echo $lkedicon; ?>"><i class="fa fa-linkedin"></i></a></li>
										<?php } ?>
										<?php $instaicon = @\App\ThemeOption::where('meta_key','instagram')->first()->meta_value; ?>
										<?php if($instaicon !=  ''){  ?>
										<li class="ins_icon"><a href="<?php echo $instaicon; ?>"><i class="fa fa-instagram"></i></a></li>
										<?php } ?>
										<?php $yutubeicon = @\App\ThemeOption::where('meta_key','youtube')->first()->meta_value; ?>
										<?php if($yutubeicon !=  ''){  ?>
										<li class="ytube_icon"><a href="<?php echo $yutubeicon; ?>"><i class="fa fa-youtube-play"></i></a></li>
										<?php } ?>
									</ul>
								</div>
							</div>
						</div>
						@endforeach	
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Bottom Footer Area --> 
	<div class="bottom-footer-area">
		<div class="container h-100">
			<div class="row h-100 align-items-center justify-content-center">
				<div class="col-12">
					<p><?php echo @\App\ThemeOption::where('meta_key','copyright_txt')->first()->meta_value; ?></p>
				</div>
			</div>
		</div>
	</div>
</footer>
