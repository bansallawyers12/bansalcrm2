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
             tr.separator td { padding:5px 0px 5px 0px; border-top: 1px solid #000; /* Adjust the color and thickness as needed */ }
             tr.separator1 td { padding:5px 0px 50px 0px; border-top: 1px solid #000; /* Adjust the color and thickness as needed */ }
        </style>
	</head>
	<body>
        <div class="invoice_table" style="padding: 10px;">
			<table width="100%" border="0">
				<tbody>
                    <tr>
                        <td style="text-align: left;">
							<h2 style="font-size:18px;line-height:21px;color:#000;margin: 50px 0px 0px;">TAX INVOICE</h2>
                            <p style="font-size: 15px;line-height: 21px;color: #333;font-weight: normal;margin: 55px 0px 0px;">
                                <?php if( !empty($record_get) ) { ?>
                                    Invoice Date: <?php echo e($record_get[0]->invoice_date); ?>

                                <?php } ?>
                            </p>
                        </td>

                        <td style="text-align: right;">
                            <img width="120" height="120" style="display:block;border:1px solid grey;" src="<?php echo e(URL::to('public/img/profile_imgs')); ?>/<?php echo e($admin->profile_img); ?>" alt="Logo"/>
                            <p style="font-size: 15px;line-height: 21px;color: #333;font-weight: normal;margin: 10px 0px 0px;">
                               <?php if( !empty($record_get) ) { ?>
                            		Invoice Number: <?php echo e($record_get[0]->invoice_no); ?>

                               <?php } ?>
                            </p>
                            
                        </td>
                    </tr>

                    <tr class="separator">
                        <td colspan="2"></td>
                    </tr>

					<tr>
                        <td style="text-align: left;">
							<p style="font-size: 15px;line-height: 21px;color: #333;font-weight: normal;margin: 10px 0px 0px;">
                                <?php if( isset($partnerInfo->legal_name) && $partnerInfo->legal_name != ""){ ?>
                                <?php echo e($partnerInfo->legal_name); ?> <br/>
                                <?php } ?>

                                <?php if( isset($partnerInfo->partner_name) && $partnerInfo->partner_name != ""){ ?>
                                Trading AS: <br> <?php echo e($partnerInfo->partner_name); ?> <br/>
                                <?php } ?>

                                <?php if( isset($partnerInfo->business_reg_no) && $partnerInfo->business_reg_no != ""){ ?>
                                ABN: <?php echo e($partnerInfo->business_reg_no); ?> <br/>
                                <?php } ?>

                                <?php if( isset($partnerInfo->address) && $partnerInfo->address != ""){ ?>
                                <?php echo e($partnerInfo->address); ?> <br/>
                                <?php } ?>

                                <?php if(
                                    ( isset($partnerInfo->city) && $partnerInfo->city != "")
                                    ||
                                    ( isset($partnerInfo->state) && $partnerInfo->state != "")
                                ){ ?>
                                <?php echo e($partnerInfo->city.' '.$partnerInfo->state); ?> <br/>
                                <?php } ?>

                                <?php if( isset($partnerInfo->country) && $partnerInfo->country != ""){ ?>
                                <?php echo e($partnerInfo->country); ?>

                                <?php } ?>
                            </p>
                        </td>

                        <td style="text-align: right;">
                            <p style="font-size: 15px;line-height: 21px;color: #333;font-weight: normal;">
                                BANSAL EDUCATION GROUP<br/>
                              	Level 8 278 Collins Street<br/>
                                Melbourne VIC 3000<br/>
								E-mail:invoice@bansaleducation.com.au<br/>
								Phone: 0460420720 <br/>
                                ABN: 30904532916
                           </p>
						</td>
                    </tr>

                    <tr class="separator1">
                        <td colspan="2"></td>
                    </tr>

                   
                   

                    <tr>
                        <td class="inv_table" colspan="3">
                            <table width="100%" border="1">
                                <tbody>
                                    <tr>
                                        <td style="padding:0px;">
                                            <table width="100%" border="0">
                                                <thead>
                                                    <tr>
                                                        <th>SNo.</th>
                                                        <th>Student Name and Student ID</th>
                                                        <th>Amount(Incl GST)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $total_amount_aud = 0.00;
                                                    if( !empty($record_get) )
                                                    {
                                                        foreach ($record_get as $record_key => $record_value)
                                                        {
                                                        ?>
                                                            <tr>
                                                                <td><?php echo e(($record_key+1)); ?></td>
                                                                <td><?php echo e(strtoupper($record_value->student_name).' ('.$record_value->student_ref_no.')  '.$record_value->student_info_id); ?></td>
                                                                <td>$<?php echo e($record_value->amount_aud); ?></td>
                                                            </tr>
                                                        <?php
                                                        $total_amount_aud += $record_value->amount_aud;
                                                        } //end  foreach
                                                    }
                                                    else
                                                    { ?>
                                                        <tr>
                                                            <td colspan="3">No Record Found</td>
                                                        </tr>
                                                    <?php
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td></td>
                                                      	<td style="text-align: right;">TOTAL AUD:</td>
                                                        <td style="border-top: 1px solid #000;">$<?php echo e($total_amount_aud); ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>


                    <tr style="margin: 30px 0px 0px;">
                        <td colspan="2" style="text-align: center;margin: 30px 0px 0px;">
                          <h6>RECEIVED WITH THANKS</h6>
                        </td>
                    </tr>


                     <tr>
                        <td colspan="2" style="font-size: 12px;line-height: 15px;color: #333;font-weight: normal;margin: 10px 0px 0px;">
                            Due Date: <?php echo date('d M Y');?> <br/>
                            You can either pay by credit card, or alternatively can transfer the funds to the following account:<br/>
                            Name: Bansal Education<br/>
                            BSB : 083-419<br/>
                            ACC : 735397520<br/>
                            <?php if( !empty($record_get) ) { ?>
                            Ref: <?php echo e($record_get[0]->invoice_no); ?>

                            <?php } ?>
                        </td>
                    </tr>

                </tbody>
			</table>
		</div>
	</body>
</html>
<?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\emails\studentinvoice.blade.php ENDPATH**/ ?>