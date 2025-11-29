@extends('layouts.frontend')
@section('seoinfo')
	<title>{{@$blogdetailists->title}} | Bansal Immigration-Your Future, Our Priority</title>
	<meta name="description" content="| {{@$blogdetailists->title}}" />
	<link rel="canonical" href="<?php echo URL::to('/'); ?>/blogs/{{@$blogdetailists->slug}}" />
	<meta property="og:locale" content="en_US" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="{{@$blogdetailists->title}} | Bansal Immigration-Your Future, Our Priority" />
	<meta property="og:description" content="| {{@$blogdetailists->title}}" />
	<meta property="og:url" content="<?php echo URL::to('/'); ?>/blogs/{{@$blogdetailists->slug}}" />
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

<section class="custom_breadcrumb bg-img bg-overlay" style="background-image: url({!! asset('public/img/Frontend/bg-2.jpg') !!});">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="section-heading text-center mx-auto">
					<div class="section-header">
						<h3>{{@$blogdetailists->title}}</h3>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- ##### Blog Area Start ##### -->
<section class="cryptos-blog-area blog_detail">
	<div class="container">
		<div class="col-12">
			<div class="row"> 
				<div class="col-12">
					<div class="inner_blog">
						{{--@if($blogdetailists->image)--}}
						<!--<a href="<?php echo URL::to('/'); ?>/blogs/{{@$list->slug}}"><img src="https://bansalcrm.com/public/img/blog/{{@$blogdetailists->image}}"></img></a>--> 
						{{--@endif--}}
						
						<?php
                        if(isset($blogdetailists->image) && $blogdetailists->image != ""){
                            $extension = pathinfo($blogdetailists->image, PATHINFO_EXTENSION); //echo $extension;
                            if( strtolower($extension) == 'mp4' ){
                               $src = 'https://bansalcrm.com/public/img/blog/'.$blogdetailists->image.'?autoplay=1&mute=1';
                                ?>
                                <iframe width="420" height="315" src="<?php echo $src;?>"></iframe>
                            <?php
                            } else if(strtolower($extension) == 'pdf') {
                                $pdfUrl = 'https://bansalcrm.com/public/img/blog/'.$blogdetailists->image;
                                ?>
                                <a href="<?php echo $pdfUrl;?>" target="_blank" data-toggle="tooltip" data-placement="top" title="Click Here To View"><i class="fa fa-file-pdf-o" aria-hidden="true" style="font-size:48px;"></i></a>
                                <?php
                            } else { ?>
                                <a href="<?php echo URL::to('/'); ?>/blogs/{{@$list->slug}}"><img src="https://bansalcrm.com/public/img/blog/{{@$blogdetailists->image}}"></img></a>
                            <?php
                            }
                        } ?>
                        
						<h1>{{@$blogdetailists->title}}</h1> 
						<div class="blog_meta">
							<span>{{date('M d, Y',strtotime(@$blogdetailists->created_at))}} | {{@$blogdetailists->categorydetail->name}}</span>
						</div>
						{!! @$blogdetailists->description !!}
						
						 @if($blogdetailists->youtube_url)
                        <iframe class="youtube-video" src="<?php echo $blogdetailists->youtube_url ;?>" allowfullscreen></iframe>
						@endif
						
						 @if($blogdetailists->pdf_doc)
                        <br><a target="_blank" href="https://bansalcrm.com/public/img/blog/{{@$blogdetailists->pdf_doc}}">Click Here To Open PDF/Video</a> 
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- ##### Blog Area End ##### -->
@endsection 