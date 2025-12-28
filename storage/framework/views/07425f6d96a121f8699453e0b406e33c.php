
<?php $__env->startSection('title', 'Quotations'); ?>

<?php $__env->startSection('content'); ?>
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
		<div class="row">
			<div class="col-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="card-header">
						<h4>Quotation #<?php echo e(@$fetchedData->id); ?></h4>
						<div class="card-header-action">
							<a href="<?php echo e(route('admin.quotations.index')); ?>" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-12 col-md-4 col-lg-4">
				<div class="card">
					<div class="card-header">
						<h4>Client Details</h4>
					</div>
					<div class="card-body">
						<p><?php echo e($fetchedData->client->first_name); ?> <?php echo e($fetchedData->client->last_name); ?><br><?php echo e($fetchedData->client->email); ?><br><?php echo e($fetchedData->client->address); ?><br><?php echo e($fetchedData->client->email); ?></p>
					</div>
				</div>
			</div>
			<div class="col-12 col-md-8 col-lg-8 ">
				<div class="form-group text-right"> 
					<span><b>Due Date:</b> <?php echo e($fetchedData->due_date); ?></span><br>
					<span><b>Quotation Currency:</b> <?php echo e($fetchedData->currency); ?></span>
				</div>
			</div>
			<div class="col-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table class="table text_wrap table-striped table-hover table-md vertical_align">
								<thead> 
									<tr>
										<th>S.N.</th>
										<th>Product Info</th>
										<th>Description</th>
										<th colspan="2">Service Fee</th>
										<th>Discount</th>
										<th>Net Fee</th>
										<th>Exg. Rate</th>
										<th >Total Amt. (in None)</th>
									</tr>
								</thead>
								<tbody class="productitem">
								<?php
								$i=1;
								$l=0;
								$getq = \App\Models\QuotationInfo::where('quotation_id',$fetchedData->id)->get();
								$totfare = 0;
								foreach($getq as $q){
									$servicefee = $q->service_fee;
									$discount = $q->discount;
									$exg_rate = $q->exg_rate;
									
									$netfare = $servicefee - $discount;
									$exgrw = $netfare * $exg_rate;
									$totfare += $exgrw;
								$workflowdata = \App\Models\Workflow::where('id',$q->workflow)->first();	
								$Productdata = \App\Models\Product::where('id',$q->product)->first();	
								$Partnerdata = \App\Models\Partner::where('id',$q->partner)->first();	
									?>
									<tr >
										<td class="sortsn"><?php echo e($i); ?></td>
										<td class="show_<?php echo e($l); ?>"><div class="productinfo"><div class="productdet"><b><?php echo e(@$Productdata->name); ?></b></div><?php echo e(@$Partnerdata->partner_name); ?><div class="prodescription">(<?php echo e(@$workflowdata->name); ?>)</div></div></td>
										<td><?php echo e(@$q->description); ?></td>
										<td>AUD</td>
										<td><?php echo e(number_format($servicefee,2,'.','')); ?></td>
										<td><?php echo e(number_format($discount,2,'.','')); ?></td>
										
										<td class="netfare"><?php echo e(number_format($netfare,2,'.','')); ?></td>
										<td><?php echo e(number_format($exg_rate,2,'.','')); ?></td>
										<td><?php echo e(number_format($exgrw,2,'.','')); ?></td>
										
									</tr>
									<?php
									$i++;
									$l++;
								}
								?>
							</tbody>
							
							</table>
						</div>
					</div>
				</div>
			</div>
			
			</div>
			<div class="row">
				<div class="col-lg-6">
				</div>
				<div class="col-lg-6 text-right">
					<span>(Service Fee - Discount = NetFee) x Exg. Rate = Total Amount</span>
					<div class="invoice-detail-item">
						<div class="invoice-detail-name">Grand Total Fees (in None)</div>
						<div class="invoice-detail-value invoice-detail-value-lg">$<span  class="grandtotal"><?php echo e(number_format($totfare, 2, '.','')); ?></span></div>
					</div>
					
				</div>
			</div>
		</div>
	</section>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\quotations\detail.blade.php ENDPATH**/ ?>