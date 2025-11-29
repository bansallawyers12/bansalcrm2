@extends('layouts.invoice')
@section('title', @$shareinvoice->company->company_name)
@section('content')
<?php use App\Http\Controllers\Controller; ?>
<div id="ember210" class="ember-view">  
	<div class="content-body transitview">
		<div class="top-container transitview  ">
			<div class="org-container">
				<div class="center-container clearfix">
					<div class="float-left">
						<h4 title="hap techno ltd" class="over-flow mt-1">
							<b>{{@$shareinvoice->company->company_name}}</b>
						 </h4>
					</div>
				</div>
			</div>
		</div>
		<div class="zb-container">
			<div class="top-container secure">
<!---->    		<div class="secure-band ">
					  <div class="center-container actions">
						  
						<div class="clearfix">
							<div class="seperator-col spaced float-left d-none d-md-block">
								<div class="column">
								  <div class="text-muted">Invoice #:</div>
								  <div>{{$invoicedetail->invoice}}</div>
								</div>
								<div class="column">
								  <div class="text-muted">Due Date:</div>
								  <div>{{date('d/m/Y',strtotime($invoicedetail->due_date))}}</div>
								</div>
							</div>
						  <div class="float-right">
				<!---->            <button dataid="{{base64_encode(convert_uuencode(@$shareinvoice->invoice_link))}}" type="button" class="btn btn-outline-secondary print_invoice" data-ember-action="" data-ember-action-249="249">
							  <svg class="icon text-bottom" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M143.5.5h230v220h-230z"></path><path d="M419.5 174.5v87h-322v-87h-98v291h98v-87h322v87h92v-291z"></path><path d="M127.5 424.5h256v88h-256z"></path></svg>
							</button>
							<a href="{{URL::to('/invoice/download/'.base64_encode(convert_uuencode(@$shareinvoice->invoice_link)))}}" class="btn btn-outline-secondary" data-ember-action="" data-ember-action-250="250">
							  <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M396.2 364l-49.7 51h-176l-52.3-51H-.5v148h512V364z"></path><path d="M396.2 235h-89.7V51h-101v184h-87.3L256 376.6z"></path></svg>
							</a>
				<!---->          </div>
						</div>
					  </div>
				</div>
			  </div>
			  <?php $currencydata = \App\Currency::where('id',$invoicedetail->currency_id)->first(); ?>
			<div class=" secure-details-container ">
			<div class="row d-md-none d-block">
			  <hr class="row">
			</div>
		<div class="pdf-container center-container d-none d-md-block">
        <div class="ribbon">
		<?php 
				$today = date('Y-m-d');
				if(strtotime($today) > strtotime($invoicedetail->due_date)  && $invoicedetail->status != 1){
					$stattyp = 'Overdue';
					$classty = 'overdue';
				}else{	
					if($invoicedetail->status == 1){
						$stattyp = 'Paid';
						$classty = 'paid';
					}else if($invoicedetail->status == 2){
						$stattyp = 'Sent';
						$classty = 'open';
					}else if($invoicedetail->status == 3){
						$stattyp = 'Partially Paid';
						$classty = 'partially_paid';
					}else{
						$stattyp = 'Draft';
						$classty = 'overdue';
					}
				} 
			?>	
          <div class="ribbon-inner ribbon-<?php echo $classty; ?>"><?php echo $stattyp; ?></div>
        </div>
<div class="invoice_template">
	<style media="all">
		body{font-family:Arial;}
		.invoice_template .inv-template-bodysection {border: 1px solid #9e9e9e;}
		.invoice_template .inv-template {padding: 30px 20px;}
		.invoice_template .inv-template .inv-entity-title{font-size: 28px;color: #000;line-height: 32px;}
		table.invoice-detailstable tr th, table.invoice-detailstable tr td {padding: 2px 5px;}
		table.invoice-detailstable tr td, .invoice_template table.inv-itemtable tr td {font-size: 14px; line-height: 16px;}
		table.inv-addresstable tr td{font-size:14px;line-height:21px;color:#000;}
		.invoice_template table.inv-itemtable tr th, .invoice_template table.inv-itemtable tr td {
		padding: 7px;}
		.invoice_template table.inv-totaltable tr td{font-size:16px;line-height:24px;padding-bottom: 5px;padding-left: 10px;}
	</style>
	<div class="inv-template"> 
		<div class="inv-template-body"> 
			<div class="inv-template-bodysection" style="margin-top:30px;">
				<table style="width: 100%;border-bottom:1px solid #000;" class="invoice-detailstable">
					<tbody>
						  <tr> 
							<td style="width:50%;padding: 2px 10px;vertical-align: middle;">
							  <div>
								<span style="font-weight: bold;" class="inv-orgname">{{@$invoicedetail->company->company_name}}<br></span>
								<span style="white-space: pre-wrap;font-size: 14px;line-height:21px;" id="tmp_org_address">{{@$invoicedetail->company->address}} <br>{{@$invoicedetail->company->city}} {{@$invoicedetail->company->state}} {{@$invoicedetail->company->zip}} <br/>{{@$invoicedetail->company->country}}</span>
							  </div>
							</td>
							<td style="width:40%;padding: 5px;vertical-align: bottom;" align="right">
								<div class="inv-entity-title">TAX INVOICE</div>
							</td>
						</tr>
					</tbody>
				</table>

				<div style="width: 100%;border-bottom:1px solid #000;">
					<table cellspacing="0" cellpadding="0" border="0" style="width: 100%;table-layout: fixed;word-wrap: break-word;font-family:Arial;" class="invoice-detailstable">
						<thead>
							<tr>
								<th style="width: 50%"></th>
								<th style="width: 50%"></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="border-right: 1px solid #9e9e9e;padding-bottom: 10px;width: 50%">
									<table cellspacing="5" cellpadding="0" border="0" style="width: 100%">
										<tbody>
											<tr>
												<td style="font-weight: 600;">#</td>
												<td style="font-weight: 600;">: {{$invoicedetail->invoice}}</td>
											</tr>
											<tr>
												<td style="">Invoice Date</td>
												<td style="font-weight: 600;">: {{date('d/m/Y',strtotime($invoicedetail->invoice_date))}}</td>
											</tr>
											<tr>
												<td style="">Terms</td>
												<td style="font-weight: 600;">: {{$invoicedetail->terms}}</td>
											</tr>
											<tr>
												<td style="">Due Date</td>
												<td style="font-weight: 600;">: {{date('d/m/Y',strtotime($invoicedetail->due_date))}}</td>
											</tr>
										</tbody>
									</table>
								</td>    
								<td style="padding-bottom: 10px;width: 50%">
								</td>
							</tr> 
						</tbody>
					</table>
				</div>
				<div style="clear:both;"></div>
				<div style="background:#f3f3f3;padding:10px 5px;">
					<table style="width: 100%;" class="inv-addresstable" border="0" cellspacing="0" cellpadding="0">
						<thead style="text-align: left;">
							  <tr>
									<th style=""><label style="margin-bottom: 10px;display: block;" id="tmp_billing_address_label" class="inv-label"><b>Bill To:</b></label></th>
							  </tr>
						</thead>
						<tbody> 
							<tr>
								<td style="" valign="top">
									<span style="white-space: pre-wrap;line-height: 15px;color:#0080ec;" id="tmp_billing_address"><strong><span class="inv-customer-name" id="zb-pdf-customer-detail"><a style="color:#0080ec;" href="#">{{@$invoicedetail->customer->company_name}}</a></span></strong></span>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<span id="tmp_billing_address"><span class="inv-customer-name" id="zb-pdf-customer-detail">{{@$invoicedetail->customer->first_name}} {{@$invoicedetail->customer->last_name}}</span></span>
								</td>
							</tr><tr>
								<td valign="top">
									<span id="tmp_billing_address"><span class="inv-customer-name" id="zb-pdf-customer-detail">
									<span style="display:block;">{{@$invoicedetail->customer->address}}</span>
									<span style="display:block;">{{@$invoicedetail->customer->city}}</span>
									<span style="display:block;">{{@$invoicedetail->customer->zip}}</span>
									<span style="display:block;">{{@$invoicedetail->customer->country}}</span></span></span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div style="clear:both;"></div> 
				<?php $currencydata = \App\Currency::where('id',$invoicedetail->currency_id)->first(); ?>
				<table style="width: 100%;table-layout:fixed;clear: both;" class="inv-itemtable" id="itemTable" cellspacing="0" cellpadding="0" border="1">
					<thead>
						<tr>
							<th style="text-align: center;" valign="bottom" id="" class="inv-itemtable-header inv-itemtable-breakword">#</th>
							<th style="text-align: left;" valign="bottom" id="" class="inv-itemtable-header inv-itemtable-breakword"><b>Item &amp; Description</b></th>
							<th style="text-align: right;" valign="bottom" id="" class="inv-itemtable-header inv-itemtable-breakword"><b>Qty</b></th>
							<th style="text-align: right;" valign="bottom" id="" class="inv-itemtable-header inv-itemtable-breakword"><b>Rate</b></th>
							<th style="text-align: right;" valign="bottom" id="" class="inv-itemtable-header inv-itemtable-breakword"><b>Amount</b></th>
						</tr>
					</thead>
					<tbody class="itemBody">
						<?php $ist = 1; $subtotal = 0; ?>
						@foreach($invoicedetail->invoicedetail as $lst)
						<?php $ntotal = $lst->quantity * $lst->rate; ?>
						<tr class="breakrow-inside breakrow-after">
							<td valign="top" style="text-align: center;" class="inv-item-row">{{$ist}}</td>
							<td valign="top" class="inv-item-row" id="tmp_item_name">
								<div><span style="white-space: pre-wrap;word-wrap: break-word;" class="inv-item-desc" id="tmp_item_description">{{$lst->item_name}}</span><br></div>
							</td>
							<td valign="top" style="text-align: right;" class="inv-item-row" id="tmp_item_qty">{{number_format($lst->quantity,$currencydata->decimal)}} </td>
							<td valign="top" style="text-align: right;" class="inv-item-row" id="tmp_item_rate">{{number_format($lst->rate,$currencydata->decimal)}}</td>
							<td valign="top" style="text-align: right;" class="inv-item-row" id="tmp_item_amount">{{number_format($ntotal,$currencydata->decimal)}}</td>
						</tr>
						<?php 
						$subtotal += $ntotal;
						$ist++; ?>
						@endforeach
					</tbody> 
				</table>
				<?php 
				if($invoicedetail->discount_type == 'fixed'){ 
					$discoun = $invoicedetail->discount;
					$finaltotal = $subtotal - $invoicedetail->discount;
				}else{
				 $discoun = ($subtotal * $invoicedetail->discount) / 100; 
				 $finaltotal = $subtotal - $discoun;
				} 
				 if(@$invoicedetail->tax != 0)
				{
					$cure = \App\TaxRate::where('id',@$invoicedetail->tax)->first(); 
					$taxcal = ($finaltotal * $cure->rate) / 100;
					$finaltotal = $finaltotal + $taxcal;
				}
				$amount_rec = \App\InvoicePayment::where('invoice_id',$invoicedetail->id)->get()->sum("amount_rec");
				$ispaymentexist = \App\InvoicePayment::where('invoice_id',$invoicedetail->id)->exists();
				?>
				<div style="width: 100%;">
					<div style="width: 50%;padding: 4px 4px 3px 7px;float: left;">
						<div style="margin:10px 0 5px">
							<div style="padding-right: 10px;margin-bottom:10px;">Total In Words</div>
							<span><b><i>Rupees <?php echo Controller::convert_number_to_words($finaltotal); ?> Only</i></b></span>
						</div>
						<div style="padding-top: 10px;">
							<p style="white-space: pre-wrap;word-wrap: break-word;margin:0px;" class="inv-notes">Thanks for your business.</p>
						</div>
					</div>
					<div style="width: 43.6%;float:right;padding:10px 0px;border-left: 1px solid #9e9e9e;" class="inv-totals">
						<table class="inv-totaltable" id="itemTable" cellspacing="0" cellspacing="0" border="0" width="100%">
							<tbody>
								<tr>
									<td valign="middle">Sub Total</td>
									<td id="tmp_subtotal" valign="middle" style="width:110px;">{{number_format($subtotal,$currencydata->decimal)}}</td>
								</tr>  
							<?php if($invoicedetail->discount != 0){ ?>
								<tr>
									<td valign="middle">Discount(<?php if($invoicedetail->discount_type == 'fixed'){ echo $currencydata->currency_symbol; } ?>{{$invoicedetail->discount}} <?php if($invoicedetail->discount_type == 'percentage'){ echo '%'; } ?>)</td>
									<td id="tmp_total" valign="middle" style="width:110px;">(-) <?php echo $discoun; ?></td>
								</tr>
								<?php } ?>
								@if(@$invoicedetail->tax != 0)
								<?php
									
									$isex = \App\TaxRate::where('id',@$invoicedetail->tax)->exists(); 
									if($isex){
								?>
								<tr>
									<td valign="middle"><b>{{@$cure->name}} [{{@$cure->rate}}%]</b></td>
									<td id="tmp_total" valign="middle" style="width:110px;"><b>{{number_format($taxcal,$currencydata->decimal)}}</b></td>
								</tr>
								<?php } ?>
							@endif
								<tr>
									<td valign="middle"><b>Total</b></td>
									<td id="tmp_total" valign="middle" style="width:110px;"><b>{{$currencydata->currency_symbol}}{{number_format($finaltotal,$currencydata->decimal)}}</b></td>
								</tr>
					 
								@if($ispaymentexist)
									<?php $baldue = $finaltotal - $amount_rec; ?>
								<tr style="height:10px;">
									<td valign="middle">Payment Made</td>
									<td valign="middle" style="width:110px;color: red;">(-) {{number_format($amount_rec, $currencydata->decimal)}}</td>
								</tr>
								<tr style="height:10px;" class="inv-balance">
									<td valign="middle"><b>Balance Due</b></td>
									<td id="tmp_balance_due" valign="middle" style="width:110px;;"><strong>{{$currencydata->currency_symbol}}{{number_format($baldue, $currencydata->decimal)}}</strong></td>
								</tr>
								@endif
							</tbody>
						</table>	
						<table width="100%" style="border-top: 1px solid #9e9e9e;">	
							<tbody>
								<tr>
									<td style="text-align: center;padding-top: 5px;" colspan="2">
										<div style="height: 75px;">
										</div>
									</td>
								</tr>
								<tr>
									<td style="text-align: center;" colspan="2">
										<label style="margin-bottom: 0px;" class="inv-totals">Authorized Signature</label>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div style="clear: both;"></div>
				</div>
			</div>
		</div> 
	</div>
</div>

      </div>
  </div>
		</div>
	</div>
</div>

<div class="modal fade" id="pdfmodel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
			  <h4 class="modal-title">Print Invoice</h4>
			   <button type="button" onclick="print()" class="btn btn-primary" >
				<span aria-hidden="true">Print</span>
			  </button>
			  <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">Close</span>
			  </button>
			</div>

			<div class="modal-body">
				<iframe frameborder="0" src="" style="width:100%;height:80vh;" id="myFrame" name="printframe"></iframe>
			</div>
		</div>
	</div>
</div>
@endsection