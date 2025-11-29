@extends('layouts.frontend')
@section('title', @$seoDetails->meta_title)
@section('meta_title', @$seoDetails->meta_title)
@section('meta_keyword', @$seoDetails->meta_keyword)
@section('meta_description', @$seoDetails->meta_desc)
@section('content')

<section class="custom_breadcrumb bg-img bg-overlay" style="background-image: url({!! asset('public/img/Frontend/bg-2.jpg') !!}); padding-top:40px">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="section-heading text-center mx-auto">
					<div class="section-header">
						<h3>Our Services</h3>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ##### Blog Area Start ##### -->
<?php $servicestat = @\App\HomeContent::where('meta_key','servicestatus')->first()->meta_value;
if(@$servicestat == 1){
 ?>
<!--================Project Area =================-->
<section class="services_area">
	<div class="container">
		<div class="inner_service"> 
			<div class="service_row"> 
				@foreach (@$servicelists as $list)
                <?php
                if( isset($list) ){
                    if( $list->id == 7 || $list->id == 8 || $list->id == 10 || $list->id == 11 ){
                        $addCls = 'style="margin-top:42px;"';
                    } else if ( $list->id == 9 ){
                        $addCls = 'style="margin-top:21px;"';
                    } else {
                        $addCls = '';
                    }
                } else {
                    $addCls = '';
                } ?>
				<div class="service_col" style="margin-top:5px;border: 1px solid #FFFFFF;">
					<div class="service_item">  
						<div class="service_icon"><i class="fa fa-{{ @$list->serv_icon }}"></i></div>
						<h4>{{@$list->title}}</h4>
						<p>{{@$list->short_description}}</p>
						<a <?php echo $addCls;?> class="readmore" href="<?php echo URL::to('/'); ?>/ourservices/{{@$list->slug}}">Read More</a>
					</div>
				</div>
				@endforeach
			</div>
		</div>
	</div>
</section>
<?php } ?>
@endsection 