@extends('layouts.admin')
@section('title', 'Quotations')

@section('content')
<style>
.ui.label {
    display: inline-block;
    line-height: 1;
    vertical-align: baseline;
    margin: 0 0.14285714em;
    background-color: #e8e8e8;
    background-image: none;
    padding: 0.5833em 0.833em;
    color: rgba(0,0,0,.6);
    text-transform: none;
    font-weight: 700;
    border: 0 solid transparent;
    border-radius: 0.28571429rem;
    -webkit-transition: background .1s ease;
    transition: background .1s ease;
}
.ui.yellow.label, .ui.yellow.labels .label {
    background-color: #fbbd08!important;
    border-color: #fbbd08!important;
    color: #fff!important;
}
.ui.red.label, .ui.red.labels .label {
    background-color: #db2828!important;
    border-color: #db2828!important;
    color: #fff!important;
}
.ui.green.label, .ui.green.labels .label {
    background-color: #21ba45!important;
    border-color: #21ba45!important;
    color: #fff!important;
}.ui.blue.label, .ui.blue.labels .label {
    background-color: #2185d0!important;
    border-color: #2185d0!important;
    color: #fff!important;
}
</style>
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="custom-error-msg">
			</div>
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>All Quotations</h4>
							<div class="card-header-action">
								<a style="display:none;" class="btn btn-primary emailmodal is_checked_client" id=""  href="javascript:;"  >Send Mail</a>
								<a style="display:none;" class="btn btn-primary changestatus is_checked_client" id=""  href="javascript:;"  >Mark as Sent</a>
							
								<div class="drop_table_data is_checked_clientn" style="display: inline-block;margin-right: 10px;">
									<button type="button" class="btn btn-primary dropdown-toggle"><i class="fas fa-columns"></i></button>
									<div class="dropdown_list">
										<label class="dropdown-option all"><input type="checkbox" value="all" checked /> Display All</label>
										<label class="dropdown-option"><input type="checkbox" value="4" checked /> Products</label>
										<label class="dropdown-option"><input type="checkbox" value="5" checked /> Total Fee</label>
										<label class="dropdown-option"><input type="checkbox" value="6" checked /> Status</label>
										<label class="dropdown-option"><input type="checkbox" value="7" checked /> Due Date</label>
										<label class="dropdown-option"><input type="checkbox" value="8" checked /> Created On</label>
										<label class="dropdown-option"><input type="checkbox" value="9" checked /> Created By</label>
										
									</div>
								</div>
								
								<a href="{{URL::to('admin/quotations/template/create')}}"  class="btn btn-primary is_checked_clientn">Create Template</a>
								<a href="javascript:;" data-toggle="modal" data-target=".create_quotation" class="btn btn-primary is_checked_clientn">Create Quotations</a>
							</div>
						</div>
						<div class="card-body">
							<ul class="nav nav-pills" id="quotation_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link" id="quotation_template-tab"  href="{{URL::to('/admin/quotations/template')}}" >Quotation Template</a>
								</li>
								<li class="nav-item">
									<a class="nav-link active" id="active_quotation-tab"  href="{{URL::to('/admin/quotations')}}" >Active Quotations</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="archived-tab"  href="{{URL::to('/admin/quotations/archived')}}" >Archived Quotations</a>
								</li>
							</ul>
							<div class="tab-content" id="quotationContent">								
								<div class="tab-pane fade show active" id="active_quotation" role="tabpanel" aria-labelledby="active_quotation-tab">
									<div class="table-responsive common_table"> 
										<table class="table text_wrap ">
										<thead>
											<tr>
												<th class="text-center" style="width:30px;">
														<div class="custom-checkbox custom-checkbox-table custom-control">
															<input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad" class="custom-control-input" id="checkbox-all">
															<label for="checkbox-all" class="custom-control-label">&nbsp;</label>
														</div>
												</th>
												<th>No</th>
												<th>Client Name</th>
												<th>Products</th>
												<th>Total Fee</th>
												<th>Status</th>
												<th>Due Date</th>
												<th>Created On</th>
												<th>Created By</th>
												<th></th>
											</tr> 
										</thead>
										@if(@$totalData !== 0)
											<?php 	$r = 0; ?>
										@foreach (@$lists as $list)
									<?php
								
									$client = \App\Models\Admin::where('id',$list->client_id)->where('role', 7)->first();
									$createdby = \App\Models\Admin::where('id',$list->user_id)->first();
									$countqou = \App\Models\QuotationInfo::where('quotation_id',$list->id)->count();
									$getq = \App\Models\QuotationInfo::where('quotation_id',$list->id)->get();
									$totfare = 0;
									foreach($getq as $q){
										$servicefee = $q->service_fee;
										$discount = $q->discount;
										$exg_rate = $q->exg_rate;
										
										$netfare = $servicefee - $discount;
										$exgrw = $netfare * $exg_rate;
										$totfare += $exgrw;
									}
									?>
										<tbody class="tdata">	
											<tr id="id_{{@$list->id}}">
												<td class="text-center">
													<div class="custom-checkbox custom-control">
														<input data-q-id="{{@$list->id}}" data-id="{{@$client->id}}" data-email="{{@$client->email}}" data-name="{{@$client->first_name}} {{@$client->last_name}}" type="checkbox" data-checkboxes="mygroup" class="cb-element custom-control-input" id="checkbox-{{$r}}">
														<label for="checkbox-{{$r}}" class="custom-control-label">&nbsp;</label>
													</div>
												</td>
												<td><a href="{{URL::to('/admin/quotation/detail')}}/{{base64_encode(convert_uuencode(@$list->id))}}">{{@$list->id}}</a></td>
												<td>{{ $client->first_name}} {{$client->last_name}}<br/>{{ @$client->email == "" ? config('constants.empty') : str_limit(@$client->email, '50', '...') }}</td> 
												<td>{{$countqou}}</td>
												<td>{{number_format($totfare,2,'.','')}} {{$list->currency}}</td>
												<td class="statusupdate"><?php if($list->status == 0){ ?>
												<span title="draft" class="ui label uppercase">draft</span>
												<?php }else if($list->status == 3){
													?>
													<span title="draft" class="ui label uppercase yellow">Sent</span>
													<?php
												}else if($list->status == 2){
													?>
													<span title="draft" class="ui label uppercase red">Declined</span>
													<?php
												}else if($list->status == 1){
													?>
													<span title="draft" class="ui label uppercase green">Approved</span>
													<?php
												}else if($list->status == 4){
													?>
													<span title="draft" class="ui label uppercase blue">Processed</span>
													<?php
												}													?></td> 	
												<td>{{date('d/m/Y', strtotime($list->due_date))}}</td>
												<td>{{date('d/m/Y', strtotime($list->created_at))}}</td>
												<td>{{$createdby->first_name}}</td>
												<td>
													<div class="dropdown d-inline">
														<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
														<div class="dropdown-menu">
														<a class="dropdown-item has-icon" target="_blank" href="{{URL::to('/admin/quotation/preview')}}/{{base64_encode(convert_uuencode(@$list->id))}}">Preview</a>
														<?php if($list->status != 2 && $list->status != 4){?>
															<a class="is_declined dropdown-item has-icon" href="{{URL::to('/admin/quotations/edit/'.base64_encode(convert_uuencode(@$list->id)))}}">Edit</a>
														<?php } ?>
															<a class="dropdown-item has-icon clientemail" data-q-id="{{@$list->id}}" data-id="{{@$client->id}}" data-email="{{@$client->email}}" data-name="{{@$client->first_name}} {{@$client->last_name}}" href="javascript:;" >Send Email</a>
															<?php if($list->status == 0 || $list->status == 3){ ?>
																<a class="is_declined dropdown-item has-icon" href="javascript:;" onClick="declineAction({{@$list->id}}, 'quotations')"><i class="far fa-mail"></i> Decline</a>
																
															<?php } ?>
															@if($list->status == 3)
																<a class="is_declined dropdown-item has-icon" href="javascript:;" onClick="approveAction({{@$list->id}}, 'quotations')"><i class="far fa-mail"></i> Approve</a>
															@endif
															@if($list->status == 1)
															<a class="processquo dropdown-item has-icon" href="javascript:;" onClick="processedAction({{@$list->id}}, 'quotations')"><i class="far fa-mail"></i> Process</a>
															@endif
															@if($list->status == 4)@else
															<a class="is_archived dropdown-item has-icon" href="javascript:;" onClick="deleteAction({{@$list->id}}, 'quotations')">Archive</a>
															@endif
														</div>
													</div>								  
												</td>
											</tr>	
											<?php $r++; ?>
										@endforeach	
										</tbody>
										@else
										<tbody>
											<tr>
												<td style="text-align:center;" colspan="10">
													No Record found
												</td>
											</tr>
										</tbody>
										@endif
									</table> 
								</div>
								<div class="card-footer">
									
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>


<div class="modal fade create_quotation custom_modal" tabindex="-1" role="dialog" aria-labelledby="quotationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="quotationModalLabel">Create Quotation</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="get" action="{{URL::to('/admin/quotations/client/')}}" name="quotationform" autocomplete="off" enctype="multipart/form-data">
			
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="client_name">Choose Client <span class="span_req">*</span></label> 
								<select data-valid="required" class="form-control client_name select2" name="client_name">
									<option value="">Select</option>
									@foreach(\App\Models\Admin::where('role',7)->get() as $list)
									<option value="{{$list->id}}">{{$list->first_name}} {{$list->last_name}}</option>
									@endforeach
								</select> 
								<span class="custom-error client_name_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('quotationform')" type="button" class="btn btn-primary">Create</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="emailmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">Compose Email</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" name="sendmail" action="{{URL::to('/admin/quotations/sendmail')}}" autocomplete="off" enctype="multipart/form-data">
				@csrf
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">From <span class="span_req">*</span></label>
								<select class="form-control" name="email_from">
									<?php
									$emails = \App\Models\Email::select('email')->where('status', 1)->get();
									foreach($emails as $nemail){
										?>
											<option value="<?php echo $nemail->email; ?>"><?php echo $nemail->email; ?></option>
										<?php
									}
									
									?>
								</select>
								@if ($errors->has('email_from'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_from') }}</strong>
									</span> 
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_to">To </label>
								<input type="hidden" name="to" class="mailto">
								<input type="hidden" name="quoto" class="quoto">
								<div class="tomail"></div>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_cc">CC </label>
								<select data-valid="" class="js-data-example-ajaxcc" name="email_cc[]"></select>
								
								@if ($errors->has('email_cc'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_cc') }}</strong>
									</span> 
								@endif
							</div>
						</div>
						
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="template">Templates </label>
								<select data-valid="" class="form-control select2 selecttemplate" name="template">
									<option value="">Select</option>
									@foreach(\App\Models\CrmEmailTemplate::all() as $list)
										<option value="{{$list->id}}">{{$list->name}}</option>
									@endforeach
								</select>
								
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="subject">Subject <span class="span_req">*</span></label>
								{{ Form::text('subject', '', array('class' => 'form-control selectedsubject', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Subject' )) }}
								@if ($errors->has('subject'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('subject') }}</strong>
									</span> 
								@endif
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Message <span class="span_req">*</span></label>
								<textarea class="summernote-simple selectedmessage" name="message"></textarea>
								@if ($errors->has('message'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('message') }}</strong>
									</span>  
								@endif
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('sendmail')" type="button" class="btn btn-primary">Send</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script>
jQuery(document).ready(function($){
	$("[data-checkboxes]").each(function () {
  var me = $(this),
    group = me.data('checkboxes'),
    role = me.data('checkbox-role');

  me.change(function () {
    var all = $('[data-checkboxes="' + group + '"]:not([data-checkbox-role="dad"])'),
      checked = $('[data-checkboxes="' + group + '"]:not([data-checkbox-role="dad"]):checked'),
      dad = $('[data-checkboxes="' + group + '"][data-checkbox-role="dad"]'),
      total = all.length,
      checked_length = checked.length;

    if (role == 'dad') {
      if (me.is(':checked')) {
        all.prop('checked', true);
		$('.is_checked_client').show();
		$('.is_checked_clientn').hide();
      } else {
        all.prop('checked', false);
		$('.is_checked_client').hide();
		$('.is_checked_clientn').show();
      }
    } else {
      if (checked_length >= total) {
        dad.prop('checked', true);
			$('.is_checked_client').show();
		$('.is_checked_clientn').hide();
      } else {
        dad.prop('checked', false);
		$('.is_checked_client').hide();
		$('.is_checked_clientn').show();
      }
    }
  });
});
$('.cb-element').change(function () {
	
 if ($('.cb-element:checked').length == $('.cb-element').length){
  $('#checkbox-all').prop('checked',true);
 }
 else {
  $('#checkbox-all').prop('checked',false);
 }

 if ($('.cb-element:checked').length > 0){
		$('.is_checked_client').show();
		$('.is_checked_clientn').hide();
	}else{
		$('.is_checked_client').hide();
		$('.is_checked_clientn').show();
	}
});
	$(document).delegate('.clientemail', 'click', function(){
$('.tomail').html('');
		$('#emailmodal').modal('show');
		var id = $(this).attr('data-id');
		var qid = $(this).attr('data-q-id');
		var name = $(this).attr('data-name');
		$('.mailto').val(id);
		$('.quoto').val(qid);
		$('.tomail').html('<div style="position: relative; font-weight: 700; padding: 1px 6px; background-color: rgb(235, 235, 235); border-radius: 3px; box-shadow: rgba(51, 51, 51, 0.15) 0px 0px 0px 1px inset; color: rgb(91, 91, 91); display: inline-flex; align-items: center; margin: 3px 0px 0px 3px;"><span  style="text-align: initial; margin-right: 14px;"><span >'+name+'</span></span></div>');
	});
	
	
	$(document).delegate('.emailmodal', 'click', function(){

		$('#emailmodal').modal('show');
		$('.tomail').html('');
	
		var array = [];
		var arrayq = [];
		$('.cb-element:checked').each(function(){
			var id = $(this).attr('data-id');
			var qid = $(this).attr('data-q-id');
			var name = $(this).attr('data-name');
			 array.push(id);
			 arrayq.push(qid);
			 $('.tomail').append('<div style="position: relative; font-weight: 700; padding: 1px 6px; background-color: rgb(235, 235, 235); border-radius: 3px; box-shadow: rgba(51, 51, 51, 0.15) 0px 0px 0px 1px inset; color: rgb(91, 91, 91); display: inline-flex; align-items: center; margin: 3px 0px 0px 3px;"><span  style="text-align: initial; margin-right: 14px;"><span >'+name+'</span></span></div>');
		});
		$('.mailto').val(array);
		$('.quoto').val(arrayq);
		
	});
	
	$(document).delegate('.selecttemplate', 'change', function(){
	var v = $(this).val();
	$.ajax({
		url: '{{URL::to('/admin/get-templates')}}',
		type:'GET',
		datatype:'json',
		data:{id:v},
		success: function(response){
			var res = JSON.parse(response);
			$('.selectedsubject').val(res.subject);
			 $(".summernote-simple").summernote('reset');  
                    $(".summernote-simple").summernote('code', res.description);  
					$(".summernote-simple").val(res.description); 
			
		}
	});
});

$(document).delegate('.changestatus', 'click', function(){
	$('.popuploader').show();
	var array = [];
		$('.cb-element:checked').each(function(){
			var id = $(this).attr('data-q-id');
			 array.push(id);
		});
	$.ajax({
		url: '{{URL::to('/admin/quotations/changestatus')}}',
		type:'GET',
		datatype:'json',
		data:{id:array},
		success: function(response){
			$('.popuploader').hide();
			var res = JSON.parse(response);
			if(res.success){
				$('.custom-error-msg').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close">☓</button> <strong>Status Marked as sent successfully</strong></div>');
				//location.reload()
			}else{
				$('.custom-error-msg').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close">☓</button> <strong>Unable to change quotation status!</strong></div>');
			}
			
		}
	});
});

$('.js-data-example-ajaxcc').select2({
		 multiple: true,
		 closeOnSelect: false,
		dropdownParent: $('#emailmodal'),
		  ajax: {
			url: '{{URL::to('/admin/clients/get-recipients')}}',
			dataType: 'json',
			processResults: function (data) {
			  // Transforms the top-level key of the response object from 'items' to 'results'
			  return {
				results: data.items
			  };
			  
			},
			 cache: true
			
		  },
	templateResult: formatRepo,
	templateSelection: formatRepoSelection
});
function formatRepo (repo) {
  if (repo.loading) {
    return repo.text;
  }

  var $container = $(
    "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

      "<div  class='ag-flex ag-align-start'>" +
        "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
        "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small ></div>" +
      
      "</div>" +
      "</div>" +
	   "<div class='ag-flex ag-flex-column ag-align-end'>" +
        
        "<span class='ui label yellow select2-result-repository__statistics'>" +
          
        "</span>" +
      "</div>" +
    "</div>"
  );

  $container.find(".select2-result-repository__title").text(repo.name);
  $container.find(".select2-result-repository__description").text(repo.email);
  $container.find(".select2-result-repository__statistics").append(repo.status);
 
  return $container;
}

function formatRepoSelection (repo) {
  return repo.name || repo.text;
}
});
</script>
@endsection