@extends('layouts.dashboard_frontend')
@section('title', 'Payment')
@section('content')

<div class="row">
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
		<div class="fancy">
			<span class="h4">All Added Products</span>
		</div>
	</div>
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
		<!-- Flash Message Start -->
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="custom-error-msg">
			</div>	
		<!-- Flash Message End -->
		
		<div class="table-responsive">
			<table id="cart" class="table table-bordered text-center cart-table">
				<thead>
					<tr>
						<th class="text-center" width="20%">Product image</th>
						<th class="text-center" width="25%">Product Details</th>
						<th class="text-center" width="15%">Price</th>
						<th class="text-center" width="15%">Discount</th>
						<th class="text-center" width="15%">Quantity</th>
						<th class="text-center" width="15%">Total</th>
					</tr>
				</thead>
				<tbody class="tdata">
					<?php $grandTotal = 0; ?>	
					@if(null !== @$fetchedData['cartItem'])
						@if(count(@$fetchedData['cartItem']) !== 0)
							@foreach (@$fetchedData['cartItem'] as $data)
								<?php
									$grandTotal = $grandTotal + (@$data->productOtherInfo->total_amount * @$data->quantity);
								?>
								<tr>
									<td class="text-center">
										@if(@$data->productData->image != '')
											<img src="{{URL::to('/public/img/product_img')}}/{{@$data->productData->image}}" class="img-responsive">
										@else
											{{config('constants.empty')}}
										@endif
									</td>
									
									<td class="cart-prod-details">   
										<p>
											{{ @$data->productData->subject_name == "" ? config('constants.empty') : @$data->productData->subject_name  }}
										</p>
										<p>
											<b>Delivery Method :</b> {{ @$data->productOtherInfo->mode_of_product == "" ? config('constants.empty') : @$data->productOtherInfo->mode_product->mode_product  }}
										</p>
										<p>
											<b>Views :</b> {{ @$data->productOtherInfo->views == "" ? config('constants.empty') : @$data->productOtherInfo->views  }}
										</p>
										<p>
											<b>Validity :</b> {{ @$data->productOtherInfo->validity == "" ? config('constants.empty') : @$data->productOtherInfo->validity  }}
										</p>									
									</td>
									
									<td>
										<i class="fa fa-inr" aria-hidden="true"></i> 
										{{ @$data->productOtherInfo->price == "" ? config('constants.empty') : @$data->productOtherInfo->price  }}
									</td>
									<td>
										{{ @$data->productOtherInfo->discount == "" ? config('constants.empty') : @$data->productOtherInfo->discount  }}%
									</td>
									<td>
										{{ @$data->quantity == "" ? config('constants.empty') : @$data->quantity }}
									</td>
									<td class="text-center">	
										<strong>
											<i class="fa fa-inr" aria-hidden="true"></i>
											{{ @$data->productOtherInfo->total_amount == "" ? config('constants.empty') : @$data->productOtherInfo->total_amount * @$data->quantity  }}
										</strong>
									</td>
								</tr>	
							@endforeach	
						@else
							<tr>
								<td colspan="8">
									<strong>
										No Item added into your cart yet. So Please choose atleast one Item.
									</strong>
								</td>
							</tr>
						@endif
					@else
							<tr>
								<td colspan="8">
									<strong>
										No Item added into your cart yet. So Please choose atleast one Item.
									</strong>
								</td>
							</tr>
					@endif		
				</tbody>
				<tfoot>
					<tr>
						<td>
							<a href="{{URL::to('/address')}}" class="btn btn-warning">
								<i class="fa fa-angle-left"></i> 
								Back
							</a>
						</td>
						<td colspan="4" class="text-right"><b>Grand Total</b></td>
						<td class="text-center">
							<strong>
								<i class="fa fa-inr" aria-hidden="true"></i>
								 {{@$grandTotal}}
							</strong>
							{{ Form::open(array('url' => '/checkout', 'name'=>"checkout", 'autocomplete'=>'off', 'class'=>'checkout-form')) }}		
								{{ Form::submit('Checkout >', ['class'=>'btn btn-success btn-block']) }}	
							{{ Form::close() }}	
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>
@endsection