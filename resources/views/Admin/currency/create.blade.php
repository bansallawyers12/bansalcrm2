@extends('layouts.admin')
@section('title', 'New Currency')

@section('content')
 <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">New Currency</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">New Currency</li>
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
				<div class="col-md-12">
					<div class="card card-primary">
					  <div class="card-header">
						<h3 class="card-title">New Currency</h3>
					  </div> 
					  <!-- /.card-header -->
					  <!-- form start -->
					  {{ Form::open(array('url' => 'admin/settings/currencies/store', 'name'=>"add-city", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
						<div class="card-body">
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group" style="text-align:right;">
										<a style="margin-right:5px;" href="{{route('admin.currency.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
										{{ Form::button('<i class="fa fa-save"></i> Save', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-city")' ]) }}
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group"> 
										<label for="currency_code" class="col-form-label">Currency Code <span style="color:#ff0000;">*</span></label>
										<select id="currency_code" name="currency_code" data-valid="required" class="form-control select2bs4" style="width: 100%;">
												<option value="">Select</option>
										</select>
										@if ($errors->has('currency_code'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('currency_code') }}</strong>
											</span> 
										@endif
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group"> 
										<label for="currency_symbol" class="col-form-label">Currency Symbol <span style="color:#ff0000;">*</span></label>
										{{ Form::text('currency_symbol', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'','id'=>'currency_symbol' )) }}
										@if ($errors->has('currency_symbol'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('currency_symbol') }}</strong>
											</span> 
										@endif
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group"> 
										<label for="name" class="col-form-label">Currency Name <span style="color:#ff0000;">*</span></label>
										{{ Form::text('name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'','id'=>'currency_name' )) }}
										@if ($errors->has('name'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('name') }}</strong>
											</span> 
										@endif
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group"> 
										<label for="decimal" class="col-form-label">Decimal Places</label>
										<select id="decimal" name="decimal" data-valid="" class="form-control select2bs4" style="width: 100%;">
											<option value="">Select</option>
											<option value="0">0</option>
											<option value="2">2</option>
											<option value="3">3</option>
										</select>
									
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group"> 
										<label for="format" class="col-form-label">Format <span style="color:#ff0000;">*</span></label>
										<select id="format" name="format" data-valid="" class="form-control" style="width: 100%;">
											<option value="">Select</option>
											<option value="1">1,234,567.89</option>
											<option value="2">1.234.567.89</option>
											<option value="3">1 234 567.89</option>
										</select>
										
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group float-right">
										{{ Form::button('<i class="fa fa-save"></i> Save', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-city")' ]) }}
									</div> 
								</div> 
							</div> 
						</div> 
					  {{ Form::close() }}
					</div>	
				</div>	
			</div> 
		</div>
	</section>
</div>
<script>
var r = {"code":0,"message":"success","results":{"supported_currencies":{"AED":{"currency_name":"UAE Dirham","currency_symbol":"AED","currency_format":"1,234,567.89","price_precision":2},"AFN":{"currency_name":"Afghan Afghani","currency_symbol":"AFN","currency_format":"1,234,567.89","price_precision":2},"ALL":{"currency_name":"Albanian Lek","currency_symbol":"Lek","currency_format":"1,234,567.89","price_precision":2},"AMD":{"currency_name":"Armenian Dram","currency_symbol":"AMD","currency_format":"1,234,567.89","price_precision":2},"ANG":{"currency_name":"Netherlands Antillian Guilder","currency_symbol":"ƒ","currency_format":"1,234,567.89","price_precision":2},"AOA":{"currency_name":"Angolan Kwanza","currency_symbol":"AOA","currency_format":"1,234,567.89","price_precision":2},"ARS":{"currency_name":"Argentine Peso","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"AUD":{"currency_name":"Australian Dollar","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"AWG":{"currency_name":"Aruban Guilder","currency_symbol":"ƒ","currency_format":"1,234,567.89","price_precision":2},"AZN":{"currency_name":"Azerbaijanian Manat","currency_symbol":"AZN","currency_format":"1,234,567.89","price_precision":2},"BAM":{"currency_name":"Bosnia and Herzegovina Convertible Marks","currency_symbol":"KM","currency_format":"1,234,567.89","price_precision":2},"BBD":{"currency_name":"Barbadian Dollar","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"BDT":{"currency_name":"Bangladeshi Taka","currency_symbol":"BDT","currency_format":"1,234,567.89","price_precision":2},"BGN":{"currency_name":"Bulgarian Lev","currency_symbol":"BGN","currency_format":"1,234,567.89","price_precision":2},"BHD":{"currency_name":"Bahraini Dinar","currency_symbol":"BHD","currency_format":"1,234,567.899","price_precision":3},"BIF":{"currency_name":"Burundian Franc","currency_symbol":"BIF","currency_format":"1,234,567","price_precision":0},"BMD":{"currency_name":"Bermudian Dollar (Bermuda Dollar)","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"BND":{"currency_name":"Brunei Dollar","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"BOB":{"currency_name":"Bolivian Boliviano","currency_symbol":"$b","currency_format":"1,234,567.89","price_precision":2},"BOV":{"currency_name":"Mvdol","currency_symbol":"BOV","currency_format":"1,234,567.89","price_precision":2},"BRL":{"currency_name":"Brazilian Real","currency_symbol":"R$","currency_format":"1,234,567.89","price_precision":2},"BSD":{"currency_name":"Bahamian Dollar","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"BTN":{"currency_name":"Bhutanese Ngultrum","currency_symbol":"BTN","currency_format":"1,234,567.89","price_precision":2},"BWP":{"currency_name":"Botswana Pula","currency_symbol":"P","currency_format":"1,234,567.89","price_precision":2},"BYN":{"currency_name":"Belarussian Ruble","currency_symbol":"p.","currency_format":"1,234,567","price_precision":0},"BZD":{"currency_name":"Belize Dollar","currency_symbol":"BZ$","currency_format":"1,234,567.89","price_precision":2},"CAD":{"currency_name":"Canadian Dollar","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"CDF":{"currency_name":"Congolese franc","currency_symbol":"CDF","currency_format":"1,234,567.89","price_precision":2},"CHE":{"currency_name":"WIR Euro","currency_symbol":"CHE","currency_format":"1,234,567.89","price_precision":2},"CHF":{"currency_name":"Swiss Franc","currency_symbol":"CHF","currency_format":"1,234,567.89","price_precision":2},"CHW":{"currency_name":"WIR Franc","currency_symbol":"CHW","currency_format":"1,234,567.89","price_precision":2},"CLF":{"currency_name":"Chilean Unidades de formento","currency_symbol":"CLF","currency_format":"1,234,567","price_precision":0},"CLP":{"currency_name":"Chilean Peso","currency_symbol":"$","currency_format":"1,234,567","price_precision":0},"CNY":{"currency_name":"Yuan Renminbi","currency_symbol":"CNY","currency_format":"1,234,567.89","price_precision":2},"COP":{"currency_name":"Colombian Peso","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"COU":{"currency_name":"Unidad de Valor Real","currency_symbol":"COU","currency_format":"1,234,567.89","price_precision":2},"CRC":{"currency_name":"Costa Rican Colon","currency_symbol":"CRC","currency_format":"1,234,567.89","price_precision":2},"CUC":{"currency_name":"Cuban Convertible Peso","currency_symbol":"CUC$","currency_format":"1,234,567.89","price_precision":2},"CUP":{"currency_name":"Cuban Peso","currency_symbol":"CUP","currency_format":"1,234,567.89","price_precision":2},"CVE":{"currency_name":"Cape Verdean Escudo","currency_symbol":"CVE","currency_format":"1,234,567.89","price_precision":2},"CZK":{"currency_name":"Czech Koruna","currency_symbol":"CZK","currency_format":"1,234,567.89","price_precision":2},"DJF":{"currency_name":"Djiboutian Franc","currency_symbol":"DJF","currency_format":"1,234,567","price_precision":0},"DKK":{"currency_name":"Danish Krone","currency_symbol":"kr","currency_format":"1,234,567.89","price_precision":2},"DOP":{"currency_name":"Dominican Peso","currency_symbol":"RD$","currency_format":"1,234,567.89","price_precision":2},"DZD":{"currency_name":"Algerian Dinar","currency_symbol":"DZD","currency_format":"1,234,567.89","price_precision":2},"EGP":{"currency_name":"Egyptian Pound","currency_symbol":"£","currency_format":"1,234,567.89","price_precision":2},"ERN":{"currency_name":"Eritrean Nakfa","currency_symbol":"ERN","currency_format":"1,234,567.89","price_precision":2},"ETB":{"currency_name":"Ethiopian Birr","currency_symbol":"ETB","currency_format":"1,234,567.89","price_precision":2},"EUR":{"currency_name":"Euro","currency_symbol":"€","currency_format":"1,234,567.89","price_precision":2},"FJD":{"currency_name":"Fijian Dollar","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"FKP":{"currency_name":"Falkland Islands Pound","currency_symbol":"£","currency_format":"1,234,567.89","price_precision":2},"GBP":{"currency_name":"Pound Sterling","currency_symbol":"£","currency_format":"1,234,567.89","price_precision":2},"GEL":{"currency_name":"Georgian Lari","currency_symbol":"GEL","currency_format":"1,234,567.89","price_precision":2},"GGP":{"currency_name":"Guernsey Pound","currency_symbol":"£","currency_format":"1,234,567.89","price_precision":2},"GHS":{"currency_name":"Ghanaian Cedi","currency_symbol":"¢","currency_format":"1,234,567.89","price_precision":2},"GIP":{"currency_name":"Gibraltar Pound","currency_symbol":"£","currency_format":"1,234,567.89","price_precision":2},"GMD":{"currency_name":"Gambian Dalasi","currency_symbol":"GMD","currency_format":"1,234,567.89","price_precision":2},"GNF":{"currency_name":"Guinean Franc","currency_symbol":"GNF","currency_format":"1,234,567","price_precision":0},"GTQ":{"currency_name":"Guatemalan Quetzal","currency_symbol":"Q","currency_format":"1,234,567.89","price_precision":2},"GYD":{"currency_name":"Guyanese Dollar","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"HKD":{"currency_name":"Hong Kong Dollar","currency_symbol":"元","currency_format":"1,234,567.89","price_precision":2},"HNL":{"currency_name":"Honduran Lempira","currency_symbol":"L","currency_format":"1,234,567.89","price_precision":2},"HRK":{"currency_name":"Croatian Kuna","currency_symbol":"kn","currency_format":"1,234,567.89","price_precision":2},"HTG":{"currency_name":"Haitian Gourde","currency_symbol":"HTG","currency_format":"1,234,567.89","price_precision":2},"HUF":{"currency_name":"Hungarian Forint","currency_symbol":"Ft","currency_format":"1,234,567.89","price_precision":2},"IDR":{"currency_name":"Indonesian Rupiah","currency_symbol":"Rp","currency_format":"1,234,567.89","price_precision":2},"ILS":{"currency_name":"Israeli new shekel","currency_symbol":"ILS","currency_format":"1,234,567.89","price_precision":2},"IMP":{"currency_name":"Manx Pound","currency_symbol":"£","currency_format":"1,234,567.89","price_precision":2},"INR":{"currency_name":"Indian Rupee","currency_symbol":"Rs.","currency_format":"1,234,567.89","price_precision":2},"IQD":{"currency_name":"Iraqi Dinar","currency_symbol":"IQD","currency_format":"1,234,567.899","price_precision":3},"IRR":{"currency_name":"Iranian Rial","currency_symbol":"IRR","currency_format":"1,234,567.89","price_precision":2},"ISK":{"currency_name":"Icelandic Krona","currency_symbol":"kr","currency_format":"1,234,567.89","price_precision":2},"JEP":{"currency_name":"Jersey Pound","currency_symbol":"£","currency_format":"1,234,567.89","price_precision":2},"JMD":{"currency_name":"Jamaican Dollar","currency_symbol":"J$","currency_format":"1,234,567.89","price_precision":2},"JOD":{"currency_name":"Jordanian Dinar","currency_symbol":"JOD","currency_format":"1,234,567.899","price_precision":3},"JPY":{"currency_name":"Japanese Yen","currency_symbol":"¥","currency_format":"1,234,567","price_precision":0},"KES":{"currency_name":"Kenyan Shilling","currency_symbol":"KES","currency_format":"1,234,567.89","price_precision":2},"KGS":{"currency_name":"Kyrgyzstani Som","currency_symbol":"KGS","currency_format":"1,234,567.89","price_precision":2},"KHR":{"currency_name":"Cambodian Riel","currency_symbol":"KHR","currency_format":"1,234,567.89","price_precision":2},"KMF":{"currency_name":"Comorian Franc","currency_symbol":"KMF","currency_format":"1,234,567","price_precision":0},"KPW":{"currency_name":"North Korean Won","currency_symbol":"₩","currency_format":"1,234,567.89","price_precision":2},"KRW":{"currency_name":"South Korean Won","currency_symbol":"₩","currency_format":"1,234,567","price_precision":0},"KWD":{"currency_name":"Kuwaiti Dinar","currency_symbol":"KWD","currency_format":"1,234,567.899","price_precision":3},"KYD":{"currency_name":"Cayman Islands Dollar","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"KZT":{"currency_name":"Kazakhstani Tenge","currency_symbol":"KZT","currency_format":"1,234,567.89","price_precision":2},"LAK":{"currency_name":"Lao Kip","currency_symbol":"LAK","currency_format":"1,234,567.89","price_precision":2},"LBP":{"currency_name":"Lebanese Pound","currency_symbol":"£","currency_format":"1,234,567.89","price_precision":2},"LKR":{"currency_name":"Sri Lankan Rupee","currency_symbol":"Rs","currency_format":"1,234,567.89","price_precision":2},"LRD":{"currency_name":"Liberian Dollar","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"LSL":{"currency_name":"Lesotho Loti","currency_symbol":"LSL","currency_format":"1,234,567.89","price_precision":2},"LYD":{"currency_name":"Libyan Dinar","currency_symbol":"LYD","currency_format":"1,234,567.899","price_precision":3},"MAD":{"currency_name":"Moroccan Dirham","currency_symbol":"MAD","currency_format":"1,234,567.89","price_precision":2},"MDL":{"currency_name":"Moldovan Leu","currency_symbol":"MDL","currency_format":"1,234,567.89","price_precision":2},"MGA":{"currency_name":"Malagascy Ariary","currency_symbol":"MGA","currency_format":"1,234,567.89","price_precision":2},"MKD":{"currency_name":"Macedonian Denar","currency_symbol":"MKD","currency_format":"1,234,567.89","price_precision":2},"MMK":{"currency_name":"Burmese Kyat","currency_symbol":"MMK","currency_format":"1,234,567.89","price_precision":2},"MNT":{"currency_name":"Mongolian Tugrik","currency_symbol":"MNT","currency_format":"1,234,567.89","price_precision":2},"MOP":{"currency_name":"Macanese Pataca","currency_symbol":"MOP","currency_format":"1,234,567.89","price_precision":2},"MRO":{"currency_name":"Ouguiya","currency_symbol":"MRO","currency_format":"1,234,567.89","price_precision":2},"MRU":{"currency_name":"Ouguiya","currency_symbol":"MRU","currency_format":"1,234,567.89","price_precision":2},"MUR":{"currency_name":"Mauritian Rupee","currency_symbol":"Rp","currency_format":"1,234,567.89","price_precision":2},"MVR":{"currency_name":"Maldivian Rufiyaa","currency_symbol":"MVR","currency_format":"1,234,567.89","price_precision":2},"MWK":{"currency_name":"Malawian Kwacha","currency_symbol":"MWK","currency_format":"1,234,567.89","price_precision":2},"MXN":{"currency_name":"Mexican Peso","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"MXV":{"currency_name":"Mexican Unidad de Inversion (UID)","currency_symbol":"MXV","currency_format":"1,234,567.89","price_precision":2},"MYR":{"currency_name":"Malaysian Ringgit","currency_symbol":"RM","currency_format":"1,234,567.89","price_precision":2},"MZN":{"currency_name":"Mozambican Metical","currency_symbol":"MT","currency_format":"1,234,567.89","price_precision":2},"NAD":{"currency_name":"Namibian Dollar","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"NGN":{"currency_name":"Nigerian Naira","currency_symbol":"NGN","currency_format":"1,234,567.89","price_precision":2},"NIO":{"currency_name":"Nicaraguan Cordoba Oro","currency_symbol":"C$","currency_format":"1,234,567.89","price_precision":2},"NOK":{"currency_name":"Norwegian Krone","currency_symbol":"kr","currency_format":"1,234,567.89","price_precision":2},"NPR":{"currency_name":"Nepalese Rupee","currency_symbol":"Rp","currency_format":"1,234,567.89","price_precision":2},"NZD":{"currency_name":"New Zealand Dollar","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"OMR":{"currency_name":"Omani rial","currency_symbol":"OMR","currency_format":"1,234,567.899","price_precision":3},"PAB":{"currency_name":"Panamanian Balboa","currency_symbol":"B/.","currency_format":"1,234,567.89","price_precision":2},"PEN":{"currency_name":"Peruvian Nuevo Sol","currency_symbol":"S/.","currency_format":"1,234,567.89","price_precision":2},"PGK":{"currency_name":"Papua New Guinean Kina","currency_symbol":"PGK","currency_format":"1,234,567.89","price_precision":2},"PHP":{"currency_name":"Philippine Peso","currency_symbol":"Php","currency_format":"1,234,567.89","price_precision":2},"PKR":{"currency_name":"Pakistani Rupee","currency_symbol":"Rs","currency_format":"1,234,567.89","price_precision":2},"PLN":{"currency_name":"Polish Zloty","currency_symbol":"PLN","currency_format":"1,234,567.89","price_precision":2},"PYG":{"currency_name":"Paraguayan Guarani","currency_symbol":"Gs","currency_format":"1,234,567","price_precision":0},"QAR":{"currency_name":"Qatari Rial","currency_symbol":"QAR","currency_format":"1,234,567.89","price_precision":2},"RON":{"currency_name":"Romanian Leu","currency_symbol":"lei","currency_format":"1,234,567.89","price_precision":2},"RSD":{"currency_name":"Serbian Dinar","currency_symbol":"RSD","currency_format":"1,234,567.89","price_precision":2},"RUB":{"currency_name":"Russian Ruble","currency_symbol":"RUB","currency_format":"1,234,567.89","price_precision":2},"RWF":{"currency_name":"Rwandan Franc","currency_symbol":"RWF","currency_format":"1,234,567","price_precision":0},"SAR":{"currency_name":"Saudi Riyal","currency_symbol":"SAR","currency_format":"1,234,567.89","price_precision":2},"SBD":{"currency_name":"Solomon Islands Dollar","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"SCR":{"currency_name":"Seychellois Rupee","currency_symbol":"Rp","currency_format":"1,234,567.89","price_precision":2},"SDG":{"currency_name":"Sudanese Pound","currency_symbol":"SDG","currency_format":"1,234,567.89","price_precision":2},"SEK":{"currency_name":"Swedish Krona","currency_symbol":"kr","currency_format":"1,234,567.89","price_precision":2},"SGD":{"currency_name":"Singapore Dollar","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"SHP":{"currency_name":"Saint Helena Pound","currency_symbol":"£","currency_format":"1,234,567.89","price_precision":2},"SLL":{"currency_name":"Sierra Leonean Leone","currency_symbol":"SLL","currency_format":"1,234,567.89","price_precision":2},"SOS":{"currency_name":"Somali Shilling","currency_symbol":"S","currency_format":"1,234,567.89","price_precision":2},"SRD":{"currency_name":"Surinamese Dollar","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"SSP":{"currency_name":"South Sudanese Pound","currency_symbol":"SSP","currency_format":"1,234,567.89","price_precision":2},"STD":{"currency_name":" Sao Tomean Dobra","currency_symbol":"STD","currency_format":"1,234,567.89","price_precision":2},"STN":{"currency_name":"Sao Tome and Principe Dobra","currency_symbol":"STN","currency_format":"1,234,567.89","price_precision":2},"SVC":{"currency_name":"El Salvador Colon","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"SYP":{"currency_name":"Syrian Pound","currency_symbol":"£","currency_format":"1,234,567.89","price_precision":2},"SZL":{"currency_name":"Swazi Lilangeni","currency_symbol":"SZL","currency_format":"1,234,567.89","price_precision":2},"THB":{"currency_name":"Thai Baht","currency_symbol":"THB","currency_format":"1,234,567.89","price_precision":2},"TJS":{"currency_name":"Tajikistani Somoni","currency_symbol":"TJS","currency_format":"1,234,567.89","price_precision":2},"TMT":{"currency_name":"Turkmenistan Manat","currency_symbol":"TMT","currency_format":"1,234,567.89","price_precision":2},"TND":{"currency_name":"Tunisian Dinar","currency_symbol":"TND","currency_format":"1,234,567.899","price_precision":3},"TOP":{"currency_name":"Tongan Paanga","currency_symbol":"TOP","currency_format":"1,234,567.89","price_precision":2},"TRY":{"currency_name":"Turkish Lira","currency_symbol":"YTL","currency_format":"1,234,567.89","price_precision":2},"TTD":{"currency_name":"Trinidad and Tobago Dollar","currency_symbol":"TT$","currency_format":"1,234,567.89","price_precision":2},"TVD":{"currency_name":"Tuvaluan Dollar","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"TWD":{"currency_name":"New Taiwan Dollar","currency_symbol":"NT$","currency_format":"1,234,567.89","price_precision":2},"TZS":{"currency_name":"Tanzanian Shilling","currency_symbol":"TZS","currency_format":"1,234,567.89","price_precision":2},"UAH":{"currency_name":"Ukrainian Hryvnia","currency_symbol":"UAH","currency_format":"1,234,567.89","price_precision":2},"UGX":{"currency_name":"Ugandan Shilling","currency_symbol":"UGX","currency_format":"1,234,567.89","price_precision":2},"USD":{"currency_name":"United States Dollar","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"UYI":{"currency_name":"Uruguay Peso en Unidades Indexadas","currency_symbol":"UYI","currency_format":"1,234,567.89","price_precision":2},"UYU":{"currency_name":"Uruguayan peso","currency_symbol":"$U","currency_format":"1,234,567.89","price_precision":2},"UZS":{"currency_name":"Uzbekistani Sum","currency_symbol":"UZS","currency_format":"1,234,567.89","price_precision":2},"VEF":{"currency_name":"Venezuelan Bolivar Fuerte","currency_symbol":"VEF","currency_format":"1.234.567,89","price_precision":2},"VES":{"currency_name":"Venezuelan Bolivar Soberano","currency_symbol":"VES","currency_format":"1.234.567,89","price_precision":2},"VND":{"currency_name":"Vietnamese Dong","currency_symbol":"VND","currency_format":"1,234,567.89","price_precision":2},"VUV":{"currency_name":"Vanuatu Vatu","currency_symbol":"VUV","currency_format":"1,234,567","price_precision":0},"WST":{"currency_name":"Samoan Tala","currency_symbol":"WST","currency_format":"1,234,567.89","price_precision":2},"XAF":{"currency_name":"Central African CFA Franc","currency_symbol":"XAF","currency_format":"1,234,567","price_precision":0},"XCD":{"currency_name":"Eastern Caribbean Dollar","currency_symbol":"$","currency_format":"1,234,567.89","price_precision":2},"XDR":{"currency_name":"SDR","currency_symbol":"XDR","currency_format":"1,234,567.89","price_precision":2},"XOF":{"currency_name":"CFA Franc BCEAO","currency_symbol":"XOF","currency_format":"1,234,567","price_precision":0},"XPF":{"currency_name":"CFP Franc","currency_symbol":"XPF","currency_format":"1,234,567","price_precision":0},"YER":{"currency_name":"Yemeni Rial","currency_symbol":"YER","currency_format":"1,234,567.89","price_precision":2},"ZAR":{"currency_name":"South African Rand","currency_symbol":"R","currency_format":"1,234,567.89","price_precision":2},"ZMW":{"currency_name":"Zambian Kwacha","currency_symbol":"ZMW","currency_format":"1,234,567.89","price_precision":2},"ZWL":{"currency_name":"Zimbabwe Dollar","currency_symbol":"Z$","currency_format":"1,234,567.89","price_precision":2}}}}
 $.each( r.results.supported_currencies , function( key, value ) { 
		var newOption = new Option(key+'-'+value.currency_name, key, true, true);
							// Append it to the select
							$('#currency_code').append(newOption);
							$('#currency_code').val('null').trigger('change');
 });
 
  $('#currency_code').on('change', function(){
	 var val = $('#currency_code option:selected').val();
	
	 $('#currency_symbol').val(r.results.supported_currencies[val].currency_symbol);
	 $('#currency_name').val(r.results.supported_currencies[val].currency_name);
 }); 
</script> 
@endsection