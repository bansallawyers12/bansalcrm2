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
						<h3>{{@$servicesdetailists->title}}</h3>
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
						<h1>{{@$servicesdetailists->title}}</h1>
						{!! @$servicesdetailists->description !!}
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- ##### Blog Area End ##### -->
<script>
jQuery(document).ready(function($){
	
});
</script>
@endsection 