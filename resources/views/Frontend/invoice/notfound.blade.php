@extends('layouts.invoice')
@section('title', @$shareinvoice->company->company_name)
@section('content')
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
			<div id="ember241" class="secure top-container ember-view"><div class="pmt-error">
				<div class="pmt-top">
				</div>
				<div class="pmt-body">
				<div>This link has expired. Kindly contact your vendor for further clarification.</div>
			  </div>
			</div>
		</div>
	</div>
</div>