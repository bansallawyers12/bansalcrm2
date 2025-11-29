@extends('layouts.admin')
@section('title', 'Profile')

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">Profile</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Profile</li>
					</ol>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.container-fluid --> 
	</div>
	<!-- /.content-header -->	
	<!-- Breadcrumb start-->
	<!--<ol class="breadcrumb">
		<li class="breadcrumb-item active">
			Home / <b>Dashboard</b>
		</li>
		@include('../Elements/Admin/breadcrumb')
	</ol>-->
	<!-- Breadcrumb end-->
	
	<!-- Main content --> 
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<!-- Flash Message Start -->
					<div class="server-error">
						@include('../Elements/flash-message')
					</div>
					<!-- Flash Message End -->
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="card card-primary">
						<div class="card-header">
							<h3 class="card-title">Profile</h3>
						</div>
					  <!-- /.card-header -->
						<div class="card-body">  
							<!-- form start --> 
							{{ Form::open(array('url' => 'profile/store', 'name'=>"add-profile", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
								<div class="form-group" style="text-align:right;">
									<a style="margin-right:5px;" href="{{route('profile')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a> 
									{{ Form::button('<i class="fa fa-save"></i> Save Profile', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-profile")' ]) }}
								</div> 	 
								<div class="row">  
									<div class="col-sm-8">      
										<div class="form-group row">  
											<label for="customer_name" class="col-sm-3 col-form-label">Customer Name <span style="color:#ff0000;">*</span></label>
											<div class="col-sm-9">
											<select name="customer_name" data-valid="required" class="form-control select2bs4" style="width: 100%;">
												<option selected="selected">Hampitrip.com</option>
												<option>Kishkinda Heritage Resort</option>
												<option>Heritage Resort</option>
												<option>Mr Ritesh</option>
												<option>Pankaj</option>
												<option>Suren</option>
											</select>
											@if ($errors->has('customer_name'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('customer_name') }}</strong>
												</span> 
											@endif
											</div>  
										</div>
									</div>
									<div class="col-sm-4">
										
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group row"> 
											<label for="invoice" class="col-sm-3 col-form-label">Invoice# <span style="color:#ff0000;">*</span></label>
											<div class="col-sm-9">
											{{ Form::text('invoice', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'INV - 00855*' )) }}
											@if ($errors->has('invoice'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('invoice') }}</strong>
												</span> 
											@endif
											</div>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group row"> 
											<label for="order_no" class="col-sm-3 col-form-label">Order Number</label>
											<div class="col-sm-9">
											{{ Form::text('order_no', '', array('class' => 'form-control', 'autocomplete'=>'off','placeholder'=>'Order Number' )) }}
											@if ($errors->has('order_no'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('order_no') }}</strong>
												</span> 
											@endif
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">										
										<div class="form-group row">
											<label for="invoice_date" class="col-sm-3 col-form-label">Invoice Date</label>
											<div class="col-sm-9">
											{{ Form::text('invoice_date', '', array('class' => 'form-control commodate', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Invoice Date' )) }}
											@if ($errors->has('invoice_date'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('invoice_date') }}</strong>
												</span> 
											@endif
											</div>
										</div>
									</div>
									<div class="col-sm-6">										
										<div class="form-group row">
											<label for="terms" class="col-sm-3 col-form-label">Terms</label>
											<div class="col-sm-9">
											<select name="terms" class="form-control select2bs4" style="width: 100%;">
												<option selected="selected">Net 15</option>
												<option>Net 30</option>
												<option>Net 45</option>
												<option>Net 60</option>
												<option>Due end of the month</option>
												<option>Due end of next month</option>
												<option>Due on Receipt</option>
												<option>Custom</option>
											</select>
											@if ($errors->has('terms'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('terms') }}</strong>
												</span> 
											@endif
											</div>
										</div>
									</div>
								</div> 
								<div class="row">
									<div class="col-sm-6">										
										<div class="form-group row">
											<label for="due_date" class="col-sm-3 col-form-label">Due Date</label>
											<div class="col-sm-9">
											{{ Form::text('due_date', '', array('class' => 'form-control commodate', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Invoice Date' )) }}
											@if ($errors->has('due_date'))
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('due_date') }}</strong>
												</span> 
											@endif
											</div>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group row">
											<label for="salesperson" class="col-sm-3 col-form-label">Salesperson</label>
											<div class="col-sm-9">
												<select name="salesperson" class="form-control select2bs4" style="width: 100%;">
													<option>Select or Add Salesperson</option>
													<option selected="selected">Suren</option>
													<option>Ritesh</option>
												</select>
												@if ($errors->has('salesperson')) 
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('salesperson') }}</strong>
												</span> 
											@endif
											</div>   
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12">
										<div class="table-responsive invoice_item_table">
											<table class="table table-bordered text-nowrap">
												<thead>
													<tr>
														<th>Item Details</th>
														<th>Quantity</th>
														<th>Rate</th>
														<th>Amount</th>
													</tr> 
												</thead>
												<tbody>
													<tr class="item_row">
														<td>
															<div class="form-group">
																<textarea class="form-control" placeholder="Type or click to select an item." name="item_detail"></textarea>
															</div>
														</td>
														<td>
															<div class="form-group">
																<input type="text" class="form-control" placeholder="1 or 2" name="quantity" />
															</div>
														</td>
														<td>
															<div class="form-group">
																<input type="text" class="form-control" placeholder="Rate" name="rate" />
															</div>
														</td>
														<td class="last_item_col">
															<div class="form-group">
																<input type="text" class="form-control" placeholder="Amount" name="amount" disabled />
															</div>
															<div class="item_action">
																<a class="item_clone" href="javascript:;"><i class="fa fa-copy"></i></a>
																<a class="item_remove" href="javascript:;"><i class="fa fa-close"></i></a>
															</div>
														</td>
													</tr>
												</tbody>
											</table>
										</div> 
										<div class="add_new_row">
											<button class="btn btn-outline-primary btn-sm" id="add_new_row">Add New Row</button>											
										</div>
									</div>
								</div>
								<div class="row" style="align-items: flex-end;">
									<div class="col-sm-6">
										<div class="form_group">
											<label for="customer_note" class="col-form-label">Customer Notes</label>
											<textarea class="form-control" name="customer_note" placeholder="Thanks for your business."></textarea>
											@if ($errors->has('customer_note')) 
												<span class="custom-error" role="alert">
													<strong>{{ @$errors->first('customer_note') }}</strong>
												</span> 
											@endif
										</div>
									</div>
									<div class="col-sm-6">
										<div class="sub_total">
											<div class="form-group row">
												<label for="" class="col-sm-3 col-form-label">Sub Total</label>
												<div class="col-sm-9 text-right">
													<span>2000</span>
												</div> 
											</div>
											<div class="form-group row">
												<label for="" class="col-sm-3 col-form-label">Discount</label>
												<div class="col-sm-4">
													<div class="discount_field">
														<input type="text" />
														<button class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">₹ <span class="caret"></span></button>
														<div class="dropdown-menu" x-placement="top-start">
															<a class="dropdown-item" tabindex="-1" href="#">%</a>
															<a class="dropdown-item" tabindex="-1" href="#">₹</a>
														</div>
													</div>
												</div>
												<div class="col-sm-5 text-right">
													<span>200</span>
												</div>
											</div>
											<div class="form-group row total_amount">
												<label for="" class="col-sm-3 col-form-label">Total (₹)</label>
												<div class="col-sm-9 text-right">
													<span>1800</span>
												</div> 
											</div>
										</div>  
									</div> 
								</div>
								<div class="term_condition_sec">
									<div class="row">
										<div class="col-sm-8">
											<div class="form-group">
												<label for="term_condition" class="col-form-label">Terms &  Conditions</label>
												<textarea class="form-control" name="term_condition" placeholder="Enter the terms and conditions of your business to be displayed in your transaction"></textarea>	
												@if ($errors->has('term_condition')) 
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('term_condition') }}</strong>
													</span> 
												@endif
											</div>
										</div>
										<div class="col-sm-4">	
										{{-- <div class="form-group upload_invoice">
												<label for="attach_file" class="col-form-label">Attach File(s) to Invoice</label>
												<div class="upload_btn">
													<input type="file" name="attach_file"/>
													<i class="fa fa-upload"></i>
												</div>
												<button class="btn dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Upload File <span class="caret"></span></button>
													<div class="dropdown-menu" x-placement="top-start">
														<a class="dropdown-item" tabindex="-1" href="#">Attach from Desktop</a>
														<a class="dropdown-item" tabindex="-1" href="#">Attach from Cloud</a>
														<a class="dropdown-item" tabindex="-1" href="#">Attach from Documents</a>
													</div>
												<span class="file_note">You can upload a maximum of 10 files, 5MB each</span>
												@if ($errors->has('attach_file')) 
													<span class="custom-error" role="alert">
														<strong>{{ @$errors->first('attach_file') }}</strong>
													</span> 
												@endif
											</div> --}}
										</div>
									</div>
								</div>								
								<div style="margin-bottom:0px;" class="form-group float-right invoice_save_btn">
									{{ Form::button('<i class="fa fa-save"></i> Save and Send', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-invoice")' ]) }}
									<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"></a>
									<div class="dropdown-menu" x-placement="top-start">
										<a class="dropdown-item" tabindex="-1" href="#"><i class="fa fa-print"></i> Save & Print</a>
										<a class="dropdown-item" tabindex="-1" href="#"><i class="fa fa-envelope"></i> Save & Send</a>
										<a class="dropdown-item" tabindex="-1" href="#"><i class="fa fa-share"></i> Save & Share</a>
										<a class="dropdown-item" tabindex="-1" href="#"><i class="fa fa-times"></i> Save & Send Later</a>
									</div>
									<button type="button" class="btn btn-default cancel_btn">Cancel</button>
									<div class="clearfix"></div>
								</div> 
							{{ Form::close() }}   
						</div> 
					</div>	
				</div>
			</div>
		</div>
	</section>
</div>
@endsection