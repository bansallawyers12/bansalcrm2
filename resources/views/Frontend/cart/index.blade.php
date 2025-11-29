@extends('layouts.dashboard_frontend')
@section('title', 'Your Cart')
@section('content')

<div class="row">
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12 text-center">
		<div class="fancy">
			<span class="h4">My Cart</span>
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
						<th class="text-center" width="8%">Product image</th>
						<th class="text-center" width="15%">Product Details</th>
						<th class="text-center" width="10%">Price</th>
						<th class="text-center" width="5%">Discount</th>
						<th class="text-center" width="5%">Quantity</th>
						<th class="text-center" width="10%">Total</th>
						<th class="text-center" width="10%">Action</th>
					</tr>
				</thead>
				<tbody class="tdata">
					@if(null !== @$fetchedData['cartItem'])
						<span class="count hide">{{count(@$fetchedData['cartItem'])}}</span>
					@endif
					
					<?php $grandTotal = 0; ?>	
					@if(null !== @$fetchedData['cartItem'])
						@if(count(@$fetchedData['cartItem']) !== 0)
							@foreach (@$fetchedData['cartItem'] as $data)
								<?php
									$grandTotal = $grandTotal + (@$data->productOtherInfo->total_amount * @$data->quantity);
								?>
								<tr id="id_{{@$data->id}}" data-total="{{@$data->productOtherInfo->total_amount}}">
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
										<input type="text" class="form-control text-center number quantity quantity_{{@$data->id}}" value="{{@$data->quantity}}" />
									</td>
									<td class="text-center total_amount_{{@$data->id}}">	
										<strong>
											<i class="fa fa-inr" aria-hidden="true"></i>
											{{ @$data->productOtherInfo->total_amount == "" ? config('constants.empty') : @$data->productOtherInfo->total_amount * @$data->quantity  }}
										</strong>
									</td>
									<td class="actions" data-th="Action">
										<a href="javascript:void(0)" class="btn btn-info btn-sm update_cart" data-id="{{@$data->id}}">
											<i class="fa fa-refresh"></i>
										</a>
										<a href="javascript:void(0)" class="btn btn-danger btn-sm" onClick="deleteAction({{@$data->id}}, 'cart_items')">
											<i class="fa fa-trash-o"></i>
										</a>		
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
							<a href="{{URL::to('/professors')}}" class="btn btn-info">
								<i class="fa fa-angle-left"></i> 
								Continue Shopping
							</a>
						</td>
						<td colspan="4" class="text-right"><b>Grand Total</b></td>
						<td class="text-center grandTotal" data-grand-total="{{@$grandTotal}}">
							<strong>
								<i class="fa fa-inr" aria-hidden="true"></i>
								 {{@$grandTotal}}
							</strong>
						</td>
						<td>
							@if(null !== @$fetchedData['cartItem'])
								@if(count(@$fetchedData['cartItem']) !== 0)
									<a href="{{URL::to('/address')}}" class="btn btn-success btn-block procced">
										Proceed <i class="fa fa-angle-right"></i>
									</a>
								@endif
							@endif		
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>
@endsection