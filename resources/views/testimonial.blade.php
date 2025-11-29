@extends('layouts.frontend')
@section('title', @$seoDetails->meta_title)
@section('meta_title', @$seoDetails->meta_title)
@section('meta_keyword', @$seoDetails->meta_keyword)
@section('meta_description', @$seoDetails->meta_desc)
@section('content')

<!--==========================
      Testimonials Section
    ============================-->

<section class="custom_breadcrumb bg-img bg-overlay" style="background-image: url({!! asset('public/img/Frontend/bg-2.jpg') !!}); padding-top:40px">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="section-heading text-center mx-auto">
					<div class="section-header">
						<h3>Testimonials</h3>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
	
<div class="container" style="padding-top:30px; padding-bottom:30px">
	<div class="row align-items-center">
		<div class="col-12 col-md-12">
			<div class="guideline-content">
				<table id="ContentPlaceHolder2_DataList1" style="font-family:Arial;font-size:14pt;font-weight:normal;width:100%;border-collapse:collapse;" cellspacing="0" align="Center">
					<tbody>
						@foreach (@$testimoniallists as $list)
						<tr>
							<td style="border-color:#A9834B;border-width:0px;border-style:Outset;">
								<div class="single-step d-flex">
									<div class="col-md-12">
										<div class="step-content">
											<img src="{{URL::to('/public/img/testimonial_img')}}/{{@$list->image}}" style="margin-right:15px; margin-bottom:15px; width:120px" alt="{{@$list->name}}" align="left" />
											<h6>{{@$list->name}}</h6>
											<p>{{@$list->description}}</p>
										</div>
									</div>
								</div>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection 