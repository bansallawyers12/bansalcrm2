@extends('layouts.frontend')
@section('seoinfo')
	<title>{{@$pagedata->meta_title}}</title>
	<meta name="description" content="{{@$pagedata->meta_description}}" />
	<link rel="canonical" href="<?php echo URL::to('/'); ?>/{{@$pagedata->slug}}" />
	<meta property="og:locale" content="en_US" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="{{@$pagedata->meta_title}}" />
	<meta property="og:description" content="{{@$pagedata->meta_description}}" />
	<meta property="og:url" content="<?php echo URL::to('/'); ?>/{{@$pagedata->slug}}" />
	<meta property="og:site_name" content="<?php echo @\App\ThemeOption::where('meta_key','site_name')->first()->meta_value; ?>" />
	<meta property="article:publisher" content="https://www.facebook.com/BANSALImmigration/" />
	<meta property="article:modified_time" content="2023-04-04T21:06:24+00:00" />
	<meta property="og:image" content="<?php echo URL::to('/'); ?>/public/img/bansal-immigration-icon.jpg" />
	<meta property="og:image:width" content="200" />
	<meta property="og:image:height" content="200" />
	<meta property="og:image:type" content="image/jpeg" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:title" content="{{@$pagedata->meta_title}}" />
	<meta name="twitter:description" content="{{@$pagedata->meta_description}}" />
	<meta name="twitter:image" content="<?php echo URL::to('/'); ?>/public/img/bansal-immigration-icon.jpg" />
	<meta name="twitter:site" content="@Bansalimmi" />
	<meta name="twitter:label1" content="Est. reading time" />
	<meta name="twitter:data1" content="6 minutes" />
@endsection
@section('content')
<style>
  .page_image{border-radius: 20px;margin-right: 20px;margin-bottom: 20px;width: 400px;float: left;}
 /* ul li, ol li { list-style:none !important;}*/
  ul { 
    list-style-type: disc !important;
     }
  h1{
    color:#1C174B;
  }
  p strong{
    color:#1C174B;
  }
  a{ font-weight: bold;}
  
  li{
    margin-left:35px;  }
  nav ul li {
    margin-left:0px;
  }
  p::first-letter {
    text-transform: capitalize;
}
  li::first-letter {
    text-transform: capitalize; /* or uppercase */
}
 
</style>

<section class="custom_breadcrumb bg-img bg-overlay" style="background-image: url({{ asset('public/img/Frontend/bg-2.jpg') }});">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="section-heading text-center mx-auto">
					<div class="section-header">
						<h3><?php echo $pagedata->title; ?></h3>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="custom_inner_page" section-padding-5-0="" style="background-color:#F7F7F7;">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-12 col-md-12">
				<div class="cus_inner_content">
					<?php if($pagedata->image != ''){ ?>
					<img src="{{ asset('public/img/cmspage/') }}/<?php echo $pagedata->image; ?>" alt="" class="page_image" />
					<?php } ?>
                   
                   
                 <?php echo $pagedata->content; ?>
                  
					
				</div>
			</div>
		</div>
	</div>
</section>
@endsection