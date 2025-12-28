
<?php $__env->startSection('title', 'Create Invoice'); ?>

<?php $__env->startSection('content'); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">Create Invoice</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Create Invoice</li>
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
		<?php echo $__env->make('../Elements/Admin/breadcrumb', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
	</ol>-->
	<!-- Breadcrumb end-->
	
	<!-- Main content --> 
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<!-- Flash Message Start -->
					<div class="server-error">
						<?php echo $__env->make('../Elements/flash-message', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
					</div>
					<!-- Flash Message End -->
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="card card-primary">
						<div class="card-header">
							<h3 class="card-title">New Invoice</h3>
						</div>
					  <!-- /.card-header -->
						<div class="card-body">
							<!-- form start -->
							<?php echo Form::open(array('url' => 'admin/invoice/store', 'name'=>"add-invoice", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")); ?>

								<div class="form-group" style="text-align:right;">
									<a style="margin-right:5px;" href="<?php echo e(route('admin.invoice.index')); ?>" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a> 
									<?php echo Form::button('<i class="fa fa-save"></i> Save Invoice', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-invoice")' ]); ?>

								</div> 	 
								<div class="row"> 
									<?php if(@Auth::user()->is_business_gst == 'yes'): ?>
										<?php
										$que = \App\Models\TaxRate::where('user_id',Auth::user()->id);
										$tocoun = $que->count();
										$taxresult = $que->get();
										
									?>
									<div class="col-sm-12">
										<div class="form-group row">
											<label for="tax" class="col-sm-3 col-form-label">Select Tax</label>
											<label for="taxno" class="col-sm-3 col-form-label"><input checked id="taxno" class="taxrate" type="radio" name="tax" value="0">No Tax</label>
											<?php if($tocoun !== 0): ?>
											<?php $__currentLoopData = $taxresult; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tlist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<label for="tax<?php echo e($tlist->id); ?>" class="col-sm-3 col-form-label"><input id="tax<?php echo e($tlist->id); ?>" type="radio" ratename="<?php echo e($tlist->name); ?>" class="taxrates" ratetax="<?php echo e($tlist->rate); ?>" name="tax" value="<?php echo e($tlist->id); ?>"><?php echo e($tlist->name); ?></label>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
										</div>
									</div>
									<?php else: ?>
									<input style="display:none;" checked id="taxno" class="taxrate" type="radio" name="tax" value="0">
								
									<?php endif; ?>
									<div class="col-sm-12">    
										<div class="form-group row">  
											<label for="customer_name" class="col-sm-3 col-form-label">Customer Name <span style="color:#ff0000;">*</span></label>
											<div class="col-sm-9">
											<select id="customer_name" name="customer_name" data-valid="required" class="form-control select2bs4" style="width: 100%;">
												<option value="">Select Customer</option>
												<?php $__currentLoopData = \App\Models\Contact::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e(@$clist->id); ?>"><?php echo e(@$clist->first_name); ?> <?php echo e(@$clist->last_name); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
											<span class="currencydata"></span>
											<div class="wrapper" id="wrpdata" style="display: none;">
												<a href="javascript:;" class="font-weight-300 addCustomermodel" >+ Add New Customer</a>
											</div>
											</div>
											<?php if($errors->has('customer_name')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('customer_name')); ?></strong>
												</span> 
											<?php endif; ?>
											</div>  
										</div>
									</div> 
								
								<div class="row" style="margin-top: 25px;">
									<div class="col-sm-12">
										<div class="addresslist" style="display:none;">
											
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group row"> 
										
											<label for="invoice" class="col-sm-3 col-form-label">Invoice# <span style="color:#ff0000;">*</span></label>
											<div class="col-sm-9">
											<?php echo Form::text('invoice', @$invoicenumber, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'INV - 00855*' )); ?>

											<?php if($errors->has('invoice')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('invoice')); ?></strong>
												</span> 
											<?php endif; ?>
											</div>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group row"> 
											<label for="order_no" class="col-sm-3 col-form-label">Order Number</label>
											<div class="col-sm-9">
											<?php echo Form::text('order_no', '', array('class' => 'form-control', 'autocomplete'=>'off','placeholder'=>'Order Number' )); ?>

											<?php if($errors->has('order_no')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('order_no')); ?></strong>
												</span> 
											<?php endif; ?>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">										
										<div class="form-group row">
											<label for="invoice_date" class="col-sm-3 col-form-label">Invoice Date</label>
											<div class="col-sm-9">
											<?php echo Form::text('invoice_date', '', array('class' => 'form-control commodate', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Invoice Date' )); ?>

											<?php if($errors->has('invoice_date')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('invoice_date')); ?></strong>
												</span> 
											<?php endif; ?>
											</div>
										</div>
									</div>
									<div class="col-sm-6">										
										<div class="form-group row">
											<label for="terms" class="col-sm-3 col-form-label">Terms</label>
											<div class="col-sm-9">
											<select name="terms" class="form-control select2bs4" style="width: 100%;">
												<option value="Net 15" selected="selected">Net 15</option>
												<option value="Net 30">Net 30</option>
												<option value="Net 45">Net 45</option>
												<option value="Net 60">Net 60</option>
												<option value="Due end of the month">Due end of the month</option>
												<option value="Due end of next month">Due end of next month</option>
												<option value="Due on Receipt">Due on Receipt</option>
												<option value="Custom">Custom</option>
											</select>
											<?php if($errors->has('terms')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('terms')); ?></strong>
												</span> 
											<?php endif; ?>
											</div>
										</div>
									</div>
								</div> 
								<div class="row">
									<div class="col-sm-6">										
										<div class="form-group row">
											<label for="due_date" class="col-sm-3 col-form-label">Due Date</label>
											<div class="col-sm-9">
											<?php echo Form::text('due_date', '', array('class' => 'form-control commodate', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Invoice Date' )); ?>

											<?php if($errors->has('due_date')): ?>
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('due_date')); ?></strong>
												</span> 
											<?php endif; ?>
											</div>
										</div>
									</div>
									
								</div>
								<div class="row">
									<div class="col-sm-12">
										<div class="table-responsive invoice_item_table">
											<table id="itemsdetail" class="table table-bordered text-nowrap">
												<thead>
													<tr>
														<th>Item Details</th>
														<th>Quantity</th>
														<th>Rate</th>
														<th>Amount</th>
													</tr> 
												</thead>
												<tbody>
												<tr class="item_row" style="display:none;">
													<td>
														<div class="form-group">
															<!--<textarea class="form-control" placeholder="Type or click to select an item." name="item_detail"></textarea>-->
															<select id="" name="item_detail[]" class="select2dat" style="width: 100%;">
																<option value="">Type or click to select an item.</option>
																<?php $__currentLoopData = \App\Models\Item::where('user_id', @Auth::user()->id)->orderby('name','ASC')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ilist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																	<option value="<?php echo e($ilist->name); ?>"><?php echo e($ilist->name); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															</select>
														</div>
													</td>
													<td>
														<div class="form-group">
															<input type="text"  class="form-control qty" value="1.00" placeholder="" name="quantity[]" />
														</div>
													</td>
													<td>
														<div class="form-group">
															<input type="text"  class="form-control rate" value="0" placeholder="Rate" name="rate[]" />
														</div>
													</td>
													<td class="last_item_col">
														<div class="form-group">
															<input type="text" value="0.00" class="form-control amount" placeholder="Amount" name="amount" disabled />
														</div>
														<div class="item_action">
															<!--<a class="item_clone" href="javascript:;"><i class="fa fa-copy"></i></a>-->
															<a class="item_remove" href="javascript:;"><i class="fa fa-close"></i></a>
														</div>
													</td>
													</tr>

													<tr class="tr_clone">
														<td>
															<div class="form-group">
																<!--<textarea class="form-control" placeholder="Type or click to select an item." name="item_detail"></textarea>-->
																<select id="" name="item_detail[]" class="select2dat"  style="width: 100%;">
																	<option value="">Type or click to select an item.</option>
																	<?php $__currentLoopData = \App\Models\Item::where('user_id', @Auth::user()->id)->orderby('name','ASC')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ilist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																		<option value="<?php echo e($ilist->name); ?>"><?php echo e($ilist->name); ?></option>
																	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																</select>
															</div>
														</td>
														<td>
															<div class="form-group">
																<input type="text" class="form-control qty" value="1.00" name="quantity[]" />
															</div>
														</td>
														<td>
															<div class="form-group">
																<input type="text" class="form-control rate" value="0" name="rate[]" />
															</div>
														</td>
														<td class="last_item_col">
															<div class="form-group">
																<input type="text" class="form-control amount" value="0.00" name="amount" disabled />
															</div>
															<div class="item_action">
																<!--<a class="item_clone" href="javascript:;"><i class="fa fa-copy"></i></a>-->
																<a class="item_remove" href="javascript:;"><i class="fa fa-close"></i></a>
															</div>
														</td>
													</tr>
												</tbody>
											</table>
										</div> 
										<div class="add_new_row">
											<button type="button" class="btn btn-outline-primary btn-sm" id="add_new_row">Add New Row</button>											
										</div>
									</div>
								</div>
								<div class="row" style="align-items: flex-end;">
									<div class="col-sm-6">
										<div class="form_group">
											<label for="customer_note" class="col-form-label">Customer Notes</label>
											<textarea class="form-control" name="customer_note" placeholder="Thanks for your business."></textarea>
											<?php if($errors->has('customer_note')): ?> 
												<span class="custom-error" role="alert">
													<strong><?php echo e(@$errors->first('customer_note')); ?></strong>
												</span> 
											<?php endif; ?>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="sub_total">
											<div class="form-group row">
												<label for="" class="col-sm-3 col-form-label">Sub Total</label>
												<div class="col-sm-9 text-right">
													<span class="subtotal">0.00</span>
												</div> 
											</div>
											
											<div class="form-group row">
												<label for="" class="col-sm-3 col-form-label">Discount</label>
												<div class="col-sm-4">
													<div class="discount_field">
														<input type="text" class="discount" name="discount" value="0"/>
														<button class="btn btn-default dropdown-toggle currecysign showdiscounttype" data-toggle="dropdown" aria-expanded="false">₹ <span class="caret"></span></button>
														<div class="dropdown-menu" x-placement="top-start">
															<a class="dropdown-item selectdiscount" dataid="percentage" tabindex="-1" href="javascript:;">%</a>
															<a class="dropdown-item currecysign selectdiscount" dataid="fixed" tabindex="-1" href="javascript:;">₹</a>
															<input type="hidden" value="fixed" name="disc_type" class="disc_type">
														</div>
													</div>
												</div>
												<div class="col-sm-5 text-right">
													<span class="discountsho">0</span>
												</div>
											</div>
											<div class="form-group row taxdetail" style="display:none;">
												<label for="" class="col-sm-3 col-form-label taxname"></label>
												<div class="col-sm-9 text-right">
													<span class="taxprice">0.00</span>
												</div> 
											</div>
											<div class="form-group row total_amount">
												<label for="" class="col-sm-3 col-form-label">Total (<span class="currecysign">₹</span>)</label>
												<div class="col-sm-9 text-right">
													<span class="finaltotal">0.00</span>
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
												<?php if($errors->has('term_condition')): ?> 
													<span class="custom-error" role="alert">
														<strong><?php echo e(@$errors->first('term_condition')); ?></strong>
													</span> 
												<?php endif; ?>
											</div>
										</div>
										<div class="col-sm-4">	
										
										</div>
									</div>
								</div>				
								<input id="save_type" name="save_type" type="hidden" value="save_send">
								<div style="margin-bottom:0px;" class="form-group float-right invoice_save_btn">
									<?php echo Form::button('<i class="fa fa-save"></i> Save and Send', ['class'=>'btn btn-primary', 'onClick'=>'customInvoiceValidate("add-invoice", "save_send")' ]); ?>

									<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"></a>
									<div class="dropdown-menu" x-placement="top-start">
										<a savetype="save_print" class="dropdown-item" tabindex="-1" href="javascript:;" onClick='customInvoiceValidate("add-invoice", "save_print")'><i class="fa fa-print"></i> Save & Print</a>
										
										<a savetype="save_send" class="dropdown-item" tabindex="-1" href="javascript:;" onClick='customInvoiceValidate("add-invoice", "save_send")'><i class="fa fa-envelope"></i> Save & Send</a>
										
										<a savetype="save_share" class="dropdown-item" tabindex="-1" href="javascript:;" onClick='customInvoiceValidate("add-invoice", "save_share")'><i class="fa fa-share"></i> Save & Share</a>
										
										<a savetype="save_send_later" class="dropdown-item" tabindex="-1" href="javascript:;" onClick='customInvoiceValidate("add-invoice", "save_send_later")'><i class="fa fa-times"></i> Save & Send Later</a>
									</div>
									<button type="button" class="btn btn-default cancel_btn">Cancel</button>
									<div class="clearfix"></div>
								</div> 
							<?php echo Form::close(); ?>   
						</div> 
					</div>	
				</div>
			</div>
		</div>
	</section>
</div>
<div class="modal fade" id="addCustomermodel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Add New Customer</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
			</div>
			<?php echo Form::open(array('url' => 'admin/contact/add', 'name'=>"add-customer", 'autocomplete'=>'off', "enctype"=>"multipart/form-data", 'id'=>'addcustomer')); ?>

			<div class="modal-body">
				<div class="customerror"></div>
				<div class="form-group row">  
					<label for="srname" class="col-sm-2 col-form-label">Primary Name <span style="color:#ff0000;">*</span></label>
					<div class="col-sm-1">
						<select style="padding: 0px 5px;" name="srname" id="srname" class="form-control" autocomplete="new-password">
							<option value="Mr">Mr</option>
							<option value="Mrs">Mrs</option>
							<option value="Ms">Ms</option>
							<option value="Miss">Miss</option>
							<option value="Dr">Dr</option>
						</select>
						<span class=""></span>
					</div>
					<div class="col-sm-3">
					<?php echo Form::text('first_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'First Name *' )); ?>

					</div>
					<div class="col-sm-3">
						<?php echo Form::text('middle_name', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Middle Name' )); ?>

					</div>
					<div class="col-sm-3">
					<?php echo Form::text('last_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Last Name *' )); ?>

					</div>  
				</div>	
				<div class="form-group row">
					<label for="company_name" class="col-sm-2 col-form-label">Company Name <span style="color:#ff0000;">*</span></label>
					<div class="col-sm-10">
						<?php echo Form::text('company_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Company Name *' )); ?>

					</div>
				</div>
				<div class="form-group row"> 
					<label for="contact_display_name" class="col-sm-2 col-form-label">Contact Display Name <span style="color:#ff0000;">*</span></label>
					<div class="col-sm-10">
					<?php echo Form::text('contact_display_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Contact Display Name *' )); ?>

					</div>
				</div>
				<div class="form-group row"> 
					<label for="contact_email" class="col-sm-2 col-form-label">Contact Email <span style="color:#ff0000;">*</span></label>
					<div class="col-sm-10">
					<?php echo Form::text('contact_email', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Contact Email *' )); ?>

					</div>
				</div>
				<div class="form-group row"> 
					<label for="contact_phone" class="col-sm-2 col-form-label">Contact Phone <span style="color:#ff0000;">*</span></label>
					<div class="col-sm-10">
					<?php echo Form::text('contact_phone', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Contact Phone *' )); ?>

					</div>
				</div>
				<div class="form-group row"> 
					<label for="currency" class="col-sm-2 col-form-label">Currency</label>
					<div class="col-sm-10">
					<select name="currency" data-valid="required" class="form-control">
							<?php $__currentLoopData = \App\Models\Currency::where('is_base','=','1' )->orwhere('user_id',Auth::user()->id)->orderby('currency_code','ASC')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cclist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<option value="<?php echo e($cclist->id); ?>" <?php if($cclist->is_base == 1): ?> selected <?php endif; ?>><?php echo e($cclist->currency_code); ?>-<?php echo e($cclist->name); ?></option>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</select>
					</div>
			</div> 
			</div>
			<div class="modal-footer justify-content-between">
			  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			  <button type="button" id="customer_save" class="btn btn-primary">Save</button>
			</div>
			<?php echo Form::close(); ?>

		</div>
	</div>
</div>
<div class="modal fade" id="billing_modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Billing Address</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
			</div>
			<?php echo Form::open(array('url' => 'admin/contact/storeaddress', 'name'=>"add-billingdetail", 'autocomplete'=>'off', "enctype"=>"multipart/form-data", 'id'=>'updatebillingdetail')); ?>

			<div class="modal-body">
				
					<input type="hidden" value="" name="customer_id" id="customer_id">
					<div class="customerror"></div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="subject" class="col-form-label">Country</label>
								<div name="country" class="country_input" id="select_country" data-input-name="country"></div>
							</div>
							<div class="form-group">
								<label for="address" class="col-form-label">Address</label>
								<textarea name="address" id="address" class="form-control" placeholder="Address" style="width: 100%; height:80px;padding: 10px;margin-bottom:10px;"></textarea>
							</div>
							<div class="form-group">
								<label for="city" class="col-form-label">City</label>
								<?php echo Form::text('city', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'City Name','id'=>'city' )); ?>

							</div>
							<div class="form-group">
								<label for="zipcode" class="col-form-label">ZipCode</label>
								<?php echo Form::text('zipcode', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Zipcode','id'=>'zipcode' )); ?>

							</div>
							<div class="form-group">
								<label for="phone" class="col-form-label">Phone</label>
								<?php echo Form::text('phone', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Phone','id'=>'phone' )); ?>

							</div>
							
						</div>
					</div>.
				
			</div>
			<div class="modal-footer justify-content-between">
				  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				  <button type="button" id="billing_save" class="btn btn-primary">Save</button>
				</div>
				<?php echo Form::close(); ?>

		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\invoice\create.blade.php ENDPATH**/ ?>