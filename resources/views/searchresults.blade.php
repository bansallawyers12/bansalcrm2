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
	<meta property="og:image" content="<?php echo URL::to('/'); ?>/img/bansal-immigration-icon.jpg" />
	<meta property="og:image:width" content="200" />
	<meta property="og:image:height" content="200" />
	<meta property="og:image:type" content="image/jpeg" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:site" content="@Bansalimmi" />
	<meta name="twitter:label1" content="Est. reading time" />
	<meta name="twitter:data1" content="3 minutes" />
@endsection
@section('content')

<section class="custom_breadcrumb bg-img bg-overlay" style="background-image: url({!! asset('img/Frontend/bg-2.jpg') !!}); padding-top:40px">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="section-heading text-center mx-auto">
					<div class="section-header">
						<h3>Search Result</h3>
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
                <?php if( count($lists) >0 ) { ?>
                    @foreach (@$lists as $list)
                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="single-feature-area style-2 mb-4">
                                <h4>{{@$list->title}}</h4>
                                <a href="<?php echo URL::to('/'); ?>/{{@$list->slug}}">Read More</a>
                            </div>
                        </div>
                    @endforeach
                <?php } else { ?>
                    <div class="col-12 col-sm-6 col-lg-4">No Record Found</div>
                <?php } ?>
			</div>
		</div>
	</div>
</section>
<!-- ##### Blog Area End ##### -->
@endsection
