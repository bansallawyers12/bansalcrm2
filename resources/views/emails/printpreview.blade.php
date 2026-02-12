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
        </style>
	</head>
	<body>
        <div class="invoice_table" style="padding: 10px;">
			<table width="100%" border="0">
				<tbody>
					<tr>
						<td style="text-align: left;">
                            <img width="80" style="height:auto;display:block;" src="{{URL::to('public/img/')}}/logo.png" alt="Logo"/>
                            
							<p style="font-size: 15px;line-height: 21px;color: #333;font-weight: normal;">
                                BANSAL IMMIGRATION CONSULTANTS<br/>
                              	Level 8<br/>
                                278 Collins Street<br/>
                                Melbourne VIC 3000
								<!--E-mail:invoice@bansaleducation.com.au<br/>
								Phone: 0460420720-->
                           </p>
						</td>

						<td style="text-align: right;">
							<h2 style="font-size:18px;line-height:21px;color:#000;">CLIENT ACCOUNT RECEIPT</h2>
                            <!--<h3 style="font-size:15px;line-height:21px;color:#000;">ABN 30 904 532 916</h3>-->
                            <p style="font-size: 15px;line-height: 21px;color: #333;font-weight: normal;margin: 10px 0px 0px;">
                                Transaction Date: {{$record_get[0]->trans_date}} <br/>
                                Date Entered:    {{$record_get[0]->entry_date}} <br/>
                                Payment Method:  {{ $record_get[0]->payment_method}} <br/>
                                Made Out By:     {{ Auth::user()->first_name }}<br/>
							</p>

                            <p style="font-size: 15px;line-height: 21px;color: #333;font-weight: normal;margin: 10px 0px 0px;">
                                RECEIPT NO: {{ $record_get[0]->receipt_id}}<br/>
                                Trans NO: {{ $record_get[0]->trans_no}}<br/>
                            </p>
						</td>
					</tr>
                  
                  	<tr>
                        <td colspan="3">
                          <h5><u>Received From:</u>
                            <br/>
                            <?php if($clientname->dob == "" || $clientname->dob == '0000-00-00' ){?>
                            <span style="font-size: 15px;line-height: 18px;color: #333;font-weight: normal;">{{ $clientname->first_name}}</span>
                      	  	<?php } else {?>
                             <span style="font-size: 15px;line-height: 18px;color: #333;font-weight: normal;">{{ $clientname->first_name}} ({{ $clientname->dob}})</span>
                            
                             <?php }?>
                          </h5>
                        </td>
                    </tr>
                  
                    
                  
					<tr>
						<td colspan="3"></td>
					</tr>
                    <tr>
						<td colspan="3"></td>
					</tr>
                    <tr>
						<td colspan="3"></td>
					</tr>
                    <tr>
						<td colspan="3"></td>
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
                                                        <th>Matter Description</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>{{$record_get[0]->description}}</td>
                                                        <td>${{$record_get[0]->deposit_amount}}</td>
                                                    </tr>
                                                  
                                                    <tr>
                                                        <td></td>
                                                      	<td style="text-align: right;">Receipt Total:</td>
                                                        <td style="border-top: 1px solid #000;">${{$record_get[0]->deposit_amount}}</td>
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
                        <td colspan="3" style="text-align: center;margin: 30px 0px 0px;">
                          <h6>RECEIVED WITH THANKS</h6>
                        </td>
                    </tr>
                  
                  
                     <tr>
                        <td colspan="3" style="font-size: 12px;line-height: 15px;color: #333;font-weight: normal;margin: 10px 0px 0px;">
                          Please contact this office if you have any queries.
                        </td>
                    </tr>
                  
                </tbody>
			</table>
		</div>
	</body>
</html>
