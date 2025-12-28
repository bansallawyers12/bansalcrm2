
<?php $__env->startSection('title', 'Invoice Report'); ?>

<?php $__env->startSection('content'); ?>

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				<?php echo $__env->make('../Elements/flash-message', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
			</div>
			<div class="custom-error-msg">
			</div>
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Invoice Report</h4>
							<div class="card-header-action">
								<div class="drop_table_data" style="display: inline-block;margin-right: 10px;"> 
									<button type="button" class="btn btn-primary dropdown-toggle"><i class="fas fa-columns"></i></button>
									<div class="dropdown_list invoice_report_list">  
										<label class="dropdown-option all"><input type="checkbox" value="all" checked /> Display All</label>
										<label class="dropdown-option"><input type="checkbox" value="3" checked /> Created By</label>
										<label class="dropdown-option"><input type="checkbox" value="5" checked /> Partner</label>
										<label class="dropdown-option"><input type="checkbox" value="6" checked /> Product</label>
										<label class="dropdown-option"><input type="checkbox" value="7" checked /> Workflow</label>
										<label class="dropdown-option"><input type="checkbox" value="8" checked /> Currency</label>
										<label class="dropdown-option"><input type="checkbox" value="9" checked /> Invoice Type</label>
										<label class="dropdown-option"><input type="checkbox" value="10" checked /> Invoice Due Date</label>
										<label class="dropdown-option"><input type="checkbox" value="11" checked /> Invoice No</label>
										<label class="dropdown-option"><input type="checkbox" value="12" checked /> Task Added By</label>
										<label class="dropdown-option"><input type="checkbox" value="13" checked /> Created At</label>
										<label class="dropdown-option"><input type="checkbox" value="14" checked /> Status</label>
										<label class="dropdown-option"><input type="checkbox" value="15" checked /> Total Fee</label>
										<label class="dropdown-option"><input type="checkbox" value="16" checked /> Commission Amount</label>
										<label class="dropdown-option"><input type="checkbox" value="17" checked /> Income Amount</label>
										<label class="dropdown-option"><input type="checkbox" value="18" checked /> Discount Amount</label>
										<label class="dropdown-option"><input type="checkbox" value="19" checked /> Income Sharing</label>
										<label class="dropdown-option"><input type="checkbox" value="20" checked /> Other Payables</label>
										<label class="dropdown-option"><input type="checkbox" value="21" checked /> Net Income</label>
										<label class="dropdown-option"><input type="checkbox" value="22" checked /> Tax Received</label>
										<label class="dropdown-option"><input type="checkbox" value="23" checked /> Tax Paid</label>
										<label class="dropdown-option"><input type="checkbox" value="24" checked /> Paid Amount</label>
										<label class="dropdown-option"><input type="checkbox" value="25" checked /> Due Amount</label>
										<label class="dropdown-option"><input type="checkbox" value="26" checked /> Last Payment Date</label>
									</div>
								</div>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive common_table invoice_report_data"> 
								<table class="table text_wrap">
									<thead> 
										<tr>
											<th>Client ID</th>
											<th>Client</th>
											<th>Created By</th>
											<th>Assignee</th>
											<th>Partner</th>
											<th>Product</th>
											<th>Workflow</th>
											<th>Currency</th>
											<th>Invoice Type</th>
											<th>Invoice Due Date</th>
											<th>Invoice No</th>
											<th>Task Added By</th>
											<th>Created At</th>
											<th>Status</th>
											<th>Total Fee</th>
											<th>Commission Amount</th>
											<th>Income Amount</th>
											<th>Discount Amount</th>
											<th>Income Sharing</th>
											<th>Other Payables</th>
											<th>Net Income</th>
											<th>Tax Received</th>
											<th>Tax Paid</th>
											<th>Paid Amount</th>
											<th>Due Amount</th>
											<th>Last Payment Date</th>
										</tr> 
									</thead>
									<?php if(count($lists) >0): ?>
									<tbody class="tdata">	
										<?php if(@$totalData !== 0): ?>
										<?php $__currentLoopData = @$lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<?php
											$client = \App\Models\Admin::where('role', '=', '7')->where('id', '=', $list->client_id)->first();
											$productdetail = \App\Models\Product::where('id', $list->product_id)->first();
											$partnerdetail = \App\Models\Partner::where('id', $list->partner_id)->first();	
											$partnerdetail = \App\Models\Partner::where('id', $list->partner_id)->first();	
											$Appldetail = \App\Models\Application::where('id', $list->branch)->first();
											$invoicedetail = \App\Models\InvoiceDetail::where('id', $list->branch)->first();
										?>
										<tr id="id_<?php echo e(@$list->id); ?>">
											<td><?php echo e(@$client->id); ?></td>
											<td>
												<a href="<?php echo e(URL::to('/admin/clients/detail'.base64_encode(convert_uuencode(@$client->id)))); ?>"><?php echo e(@$client->first_name); ?> <?php echo e(@$client->last_name); ?></a>
											</td>
											<td>-</td>
											<td><?php echo e(@$client->assignee); ?></td>
											<td><?php echo e(@$partnerdetail->partner_name); ?></td> 
											<td><?php echo e(@$productdetail->name); ?></td> 
											<td><?php echo e(@$Appldetail->workflow); ?></td>
											<td><?php echo e(@$list->currency == "" ? config('constants.empty') : str_limit(@$list->currency, '50', '...')); ?></td>
											<td><?php echo e(@$list->type == "" ? config('constants.empty') : str_limit(@$list->type, '50', '...')); ?></td>
											<td><?php echo e(@$list->due_date == "" ? config('constants.empty') : date('d/m/Y',strtotime(@$list->due_date))); ?></td>
											<td>-</td>
											<td><?php echo e(@$list->created_at == "" ? config('constants.empty') : date('d/m/Y',strtotime(@$list->created_at))); ?></td>
											<td>
												<?php
												if($list->status == 1){
													echo '<span style="color: rgb(113, 204, 83); width: 84px;">Paid</span>';
												}else{
													echo '<span style="color: rgb(255, 173, 0); width: 84px;">Unpaid</span>';
												}
												?>
											</td>
											<td><?php echo e(@$invoicedetail->total_fee); ?></td>
											<td><?php echo e(@$invoicedetail->comm_amt); ?></td>
											<td>-</td>
											<td>-</td>
											<td>-</td>
											<td>-</td>
											<td>-</td>
											<td>-</td>
											<td><?php echo e(@$list->net_incone == "" ? config('constants.empty') : str_limit(@$list->net_incone, '50', '...')); ?></td>
											<td>-</td>
											<td>-</td>
											<td>-</td>
											<td>-</td>
										</tr>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
										<?php endif; ?>										
									</tbody>  
									<?php else: ?>
										<tbody>
											<tr>
												<td style="text-align:center;" colspan="12">
													No Record found
												</td>
											</tr>
										</tbody>
									<?php endif; ?>			
								</table> 
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\reports\invoice.blade.php ENDPATH**/ ?>