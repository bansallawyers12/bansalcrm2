<!doctype html>
<html>
	<head>
		<style>
			body{font-family: 'Open Sans', sans-serif;margin:0px;}
			.inv_table{vertical-align: top;}
			.invoice_table table{border-collapse: collapse;}
			.inv_table table thead{background:#eee;}
			.inv_table table thead tr th{padding:10px;text-align:left;}
			.inv_table table tbody tr td{padding:8px;}
			.inv_table table thead tr th, .inv_table table tbody tr td{font-size:14px;line-height: 18px;vertical-align: top;}
			.inv_table table tbody tr.total_val td{background:#ccc;padding: 20px 10px;}
			
			.small-font {
              font-size: 60%; 
            }
		</style>
	</head>
	<body>
	<?php
	$admin = \App\Models\Admin::where('role',1)->where('id',$invoicedetail->user_id)->first();
	?>
	<?php
	$paymentdetails = \App\Models\InvoicePayment::where('invoice_id', $invoicedetail->id)->orderby('created_at', 'DESC')->get();
	$totlacount = \App\Models\InvoicePayment::where('invoice_id', $invoicedetail->id)->orderby('created_at', 'DESC')->count();
	$amount_rec = 0;
	if($totlacount !== 0){
		foreach($paymentdetails as $paymentdetail){
			$amount_rec +=$paymentdetail->amount_rec;
		}
	}
	$name = @$admin->company_name;
	$address = @$admin->address;
	$email = @$admin->primary_email;
	$phone = @$admin->phone;
	$other_phone = '';
	$website = '';
	$abn = '';
	$note = '';
	$logo = @$admin->profile_img;
	if($invoicedetail->profile != ''){
		$profile = json_decode($invoicedetail->profile);
		$name = $profile->name;
		$address = $profile->address;
		$phone = $profile->phone;
		$other_phone = ', '.$profile->other_phone;
		$email = $profile->email;
		$website = $profile->website;
		$logo = $profile->logo;
		$abn = @$profile->abn;
		$profiledetail = \App\Models\Profile::where('id', @$profile->id)->first();
		$note = $profiledetail->note;
	}

		?>
		<div class="invoice_table" style="padding: 10px;">
			<table width="100%" border="0">
				<tbody>
					<tr>
						<td>
							<img style="width:150px;" src="<?php echo e(asset('img/profile_imgs')); ?>/<?php echo e($logo); ?>" alt="Company Logo"/>
						</td>
						<td style="text-align: right;">
							<span style="font-size:21px;line-height:24px;color:#000;"><b><?php echo e($name); ?></b></span>
							
							<p style="font-size: 15px;line-height: 21px;color: #333;font-weight: normal;margin: 10px 0px 0px;"><?php echo e($abn); ?><br><?php echo e($address); ?><br/>
							<?php echo e($phone); ?><?php echo e($other_phone); ?>

							<?php echo e($email); ?><br/>
							<?php echo e($website); ?><br/>
							</p>
						</td>
					</tr>
					<tr>
						<td colspan="2"><hr style="border-color:#000;"/></td>
					</tr>
					<tr>
						<td>
							<span style="font-size: 18px;line-height: 21px;color: #000;margin: 0px 0px 20px;display:block;"><b>TAX INVOICE</b></span>
							<p style="font-size: 13px;line-height: 16px;color: #000;font-weight: normal;margin: 10px 0px 5px;"><b>Invoice To:</b></p>
							<p style="font-size: 14px;line-height: 21px;color: #000;font-weight: bold;margin: 0px;"><?php echo e(@$partnerdata->partner_name); ?></p>
							<p style="font-size: 13px;line-height: 21px;color: #333;font-weight: normal;margin: 0px;"><?php echo e(@$partnerdata->address); ?><br/> <?php echo e(@$partnerdata->city); ?> , <?php echo e(@$partnerdata->state); ?><br/> <?php echo e(@$partnerdata->country); ?></p>
						</td>
						<td>
							<p style="font-size: 14px;line-height: 18px;color: #666;margin: 0px 0px 5px;"><b>Invoice No:</b> <span style="float:right;"><?php echo e($invoicedetail->invoice_no); ?></span></p>
							<p style="font-size: 14px;line-height: 18px;color: #666;margin: 0px 0px 5px;"><b>Invoice Date:</b> <span style="float:right;"><?php echo e(date('d-m-Y', strtotime($invoicedetail->invoice_date))); ?></span></p>
							<div style="border:1px solid #ccc;">
								<!--<p style="background:#eee;padding:10px;font-size: 14px;line-height: 18px;color: #666;margin:0px;"><b>Total Due</b> (in AUD) <span style="font-size: 16px;line-height: 21px;color: #333;float: right;"><b><span class="percentageinput">$</span></b></span></p>-->
								<p style="background:#eee;padding:10px;font-size: 14px;line-height: 18px;color: #666;margin:0px;"><b>Total Due</b> (in AUD) <span style="font-size: 16px;line-height: 21px;color: #333;float: right;"><b><span class="percentageinput">$</span><?php echo e(0); ?></b></span></p>
								<p style="padding:10px;font-size: 14px;line-height: 18px;color: #666;margin:0px;"><b>Due Date</b> <span style="font-size: 16px;line-height: 21px;margin:0px;color:#333;float: right;"><b><?php echo e(date('d-m-Y', strtotime($invoicedetail->due_date))); ?></b></span></p>
							</div>
						</td>
					</tr>
					<td colspan="2" style="padding:10px;"></td>
					<tr>
						<td class="inv_table" colspan="2">
							<table width="100%" border="1">
								<tbody>
									<tr>
										<td style="padding:0px;">
											<table width="100%" border="0">
												<thead>
													<tr>
														<th>Client Details</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>
															<span style="font-size:16px;line-height:21px;color:#000;display:block;"><b>Name:<?php echo e($clientdata->first_name); ?> <?php echo e($clientdata->last_name); ?></b></span>
															<span style="font-size:16px;line-height:21px;color:#000;display:block;"><b>Client ID:</b> <?php echo e($clientdata->client_id); ?></span>
															<span style="font-size:16px;line-height:21px;color:#000;display:block;"><b>DOB:</b> <?php echo e(date('d-m-Y', strtotime($clientdata->dob))); ?></span>
															<?php if($productdata): ?>
															<span style="font-size:16px;line-height:21px;color:#000;display:block;"><b>Product:</b> <?php echo e($productdata->name); ?></span>
															<?php endif; ?>
															<p style="font-size:14px;line-height:21px;color:#000;margin:0px;">id: #<?php echo e($clientdata->id); ?><br/><?php echo e($clientdata->address); ?><br/><?php echo e(@$partnerdata->partner_name); ?><br/><?php echo e(@$branchdata->name); ?></p>
														</td>
													</tr>
												</tbody> 
											</table>
										</td>
										<td style="padding:0px;">
											<table width="100%" border="0">
												<thead>
													<tr>
														<th>Description</th>
														<th>Fee</th>
														<th>Commission<span class="small-font">(Incl. GST)</span></th>
														<th>Bonus<span class="small-font">(Incl. GST)</span></th>
														<!--<th>Tax</th>-->
														<th >Total Commission<span class="small-font">(Incl. GST)</span></th>
														<th >Payable to College</th>
													</tr>
												</thead>
												<tbody>
												<?php
										$invoiceitemdetails = \App\Models\InvoiceDetail::where('invoice_id', $invoicedetail->id)->orderby('id','ASC')->get();
										$coom_amt = 0;
										$total_fee = 0;
										$netamount = 0;
										$tax_amount = 0;
										$bonus_amount = 0;
										
										$total_commission_plus_tax_amount = 0;
										$total_commission_all = 0;
										
										foreach($invoiceitemdetails as $invoiceitemdetail){
										    $coom_amt += $invoiceitemdetail->comm_amt;
											$total_fee += $invoiceitemdetail->total_fee;
											$tax_amount += $invoiceitemdetail->tax_amount;
											$netamount += $invoiceitemdetail->netamount;
										    $bonus_amount += $invoiceitemdetail->bonus_amount;
										    
										     $commission_plus_tax_amount = $invoiceitemdetail->comm_amt + $invoiceitemdetail->tax_amount;
										    $total_commission_plus_tax_amount += $commission_plus_tax_amount;
										    
										    $total_commission = $invoiceitemdetail->comm_amt + $invoiceitemdetail->tax_amount + $invoiceitemdetail->bonus_amount;
										    $total_commission_all += $total_commission;
												?>
													<tr>
														<td><?php echo e($invoiceitemdetail->description); ?></td>
														<td><span class="percentageinput11">$<?php echo e($invoiceitemdetail->total_fee); ?></span></td>
														<td><span class="percentageinput11">$<?php echo e($commission_plus_tax_amount); ?></span></td>
														<td><span class="percentageinput11">$<?php echo e($invoiceitemdetail->bonus_amount); ?></span></td>
														<!--<td><span class="percentageinput11">$<?php echo e($invoiceitemdetail->tax_amount); ?></span></td>-->
														<td><span class="percentageinput11">$<?php echo e($total_commission); ?></span></td>
														<td><span class="percentageinput11">$<?php echo e($invoiceitemdetail->netamount); ?></span></td>
													</tr>
										<?php } ?>
													<tr class="total_val">
														<td><b>Total</b> (in AUD):</td>
														<td><b><span class="percentageinput11">$<?php echo e($total_fee); ?></span></b></td>
														<td><b><span class="percentageinput11">$<?php echo e($total_commission_plus_tax_amount); ?></span></b></td>
														<td><b><span class="percentageinput11">$<?php echo e($bonus_amount); ?></span></b></td>
														<!--<td><b><span class="percentageinput11">$<?php echo e($tax_amount); ?></span></b></td>-->
														<td><b><span class="percentageinput11">$<?php echo e($total_commission_all); ?></span></b></td>
														<td><b><span class="percentageinput11">$<?php echo e($netamount); ?></span></b></td>
													</tr>
												</tbody>
											</table>
										</td>										
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<td colspan="2" style="padding:10px;"></td>
					<tr>
						<td class="inv_table" style="padding-right: 15px;">
							<table width="100%" border="1">
								<thead>
									<tr>
										<th>Payment Details</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>No payment details found.</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td class="inv_table" style="padding-left: 15px;">
							<table width="100%" border="1">
								<thead>
									<tr>
										<th colspan="2">Payment Received</th>
									</tr>
								</thead>
								<tbody>
								<?php
								
								$amount_rec = 0;
								if($totlacount !== 0){
								foreach($paymentdetails as $paymentdetail){
									$amount_rec +=$paymentdetail->amount_rec;
									?>
									<tr>
										<td><?php echo e(date('d-m-Y', strtotime($paymentdetail->payment_date))); ?></td>
										<td style="text-align: right;"><b><span class="percentageinput">$</span><?php echo e($paymentdetail->amount_rec); ?></b></td>
									</tr>
									<?php } ?>
									<tr>
										<td><b>Total in (AUD)</b></td>
										<td style="text-align: right;"><b><span class="percentageinput">$</span><?php echo e($amount_rec); ?></b></td>
									</tr>
									<?php
										}else{
											?>
											<tr>
												<td>There is no payment done for this invoice</td>
											</tr>
											<?php
										}
										?>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<table style="padding-top:50px;">
				<tr>
					<td><?php echo e($note); ?></td>
				</tr>
			</table>
		</div>
	</body>
</html>
<?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\emails\invoice.blade.php ENDPATH**/ ?>