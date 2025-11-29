@extends('layouts.frontend')
@section('seoinfo')
	<title>Blogs | Bansal Immigration-Your Future, Our Priority</title>
	<meta name="description" content="| Blogs" />
	<link rel="canonical" href="<?php echo URL::to('/'); ?>/blogs/" />
	<meta property="og:locale" content="en_US" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="Blogs | Bansal Immigration-Your Future, Our Priority" />
	<meta property="og:description" content="| Blogs" />
	<meta property="og:url" content="<?php echo URL::to('/'); ?>/blogs/" />
	<meta property="og:site_name" content="Bansal Immigration - Your Future, Our Priority" />
	<meta property="article:publisher" content="https://www.facebook.com/BANSALImmigration/" />
	<meta property="article:modified_time" content="2021-08-30T09:05:04+00:00" />
	<meta property="og:image" content="<?php echo URL::to('/'); ?>/public/img/bansal-immigration-icon.jpg" />
	<meta property="og:image:width" content="200" />
	<meta property="og:image:height" content="200" />
	<meta property="og:image:type" content="image/jpeg" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:site" content="@Bansalimmi" />
	<meta name="twitter:label1" content="Est. reading time" />
	<meta name="twitter:data1" content="3 minutes" />
@endsection
@section('content')

<section class="custom_breadcrumb bg-img bg-overlay" style="background-image: url({!! asset('public/img/Frontend/bg-2.jpg') !!}); padding-top:40px">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="section-heading text-center mx-auto">
					<div class="section-header">
						<h3>RECENT BLOGS</h3>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- ##### Blog Area Start ##### -->
<section class="cryptos-blog-area blog_sec">
	<div class="container">
		<div class="col-12">
			<div class="row"> 
			<!-- Single Course Area -->
				@foreach (@$bloglists as $list)
				<div class="col-12 col-sm-6 col-lg-4">
					<div class="single-feature-area style-2 mb-4">
						{{--@if($list->image)--}}
						<!--<a href="<?php echo URL::to('/'); ?>/blogs/{{@$list->slug}}"><img src="https://bansalcrm.com/public/img/blog/{{@$list->image}}"></img></a>--> 
						{{--@endif--}}
						
						<?php
                        if(isset($list->image) && $list->image != ""){
                            $extension = pathinfo($list->image, PATHINFO_EXTENSION); //echo $extension;
                            if( strtolower($extension) == 'mp4' ){
                               $src = 'https://bansalcrm.com/public/img/blog/'.$list->image.'?autoplay=1&mute=1';
                                ?>
                                <iframe width="200" height="90" src="<?php echo $src;?>"></iframe>
                            <?php
                            } else if(strtolower($extension) == 'pdf') {
                                $pdfUrl = 'https://bansalcrm.com/public/img/blog/'.$list->image;
                                ?>
                                <a href="<?php echo $pdfUrl;?>" target="_blank" data-toggle="tooltip" data-placement="top" title="Click Here To View"><i class="fa fa-file-pdf-o" aria-hidden="true" style="font-size:48px;"></i></a>
                                <?php
                            } else { ?>
                                <a href="<?php echo URL::to('/'); ?>/{{@$list->slug}}"><img src="https://bansalcrm.com/public/img/blog/{{@$list->image}}" alt="{{@$list->image}}"></img></a>
                            <?php
                            }
                        } ?>
                        
						<h4>{{@$list->title}}</h4>
						<p>{{@$list->short_description}}</p> 
						<a href="<?php echo URL::to('/'); ?>/{{@$list->slug}}">Read More</a>
					</div>
				</div>
				@endforeach
			</div>
		</div>
	</div>
</section>
<!-- ##### Blog Area End ##### -->
@endsection 