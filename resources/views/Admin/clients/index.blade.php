@extends('layouts.admin')
@section('title', 'Clients')

@section('content')
<style>
.ag-space-between {
    justify-content: space-between;
}
.ag-align-center {
    align-items: center;
}
.ag-flex {
    display: flex;
}
.ag-align-start {
    align-items: flex-start;
}
.ag-flex-column {
    flex-direction: column;
}
.col-hr-1 {
    margin-right: 5px!important;
}
.text-semi-bold {
    font-weight: 600!important;
}
.small, small {
    font-size: 85%;
}
.ag-align-end {
    align-items: flex-end;
}

.ui.yellow.label, .ui.yellow.labels .label {
    background-color: #fbbd08!important;
    border-color: #fbbd08!important;
    color: #fff!important;
}
.ui.label:last-child {
    margin-right: 0;
}
.ui.label:first-child {
    margin-left: 0;
}
.field .ui.label {
    padding-left: 0.78571429em;
    padding-right: 0.78571429em;
}

.ag-list__title{background-color: #fcfcfc;border: 1px solid #f2f2f2;padding: 0.8rem 1.2rem;}
.ag-list__item{font-size: 12px;margin: 0;padding: 0.8rem 2.6rem;}
.filter_panel {background: #f7f7f7;margin-bottom: 10px;border: 1pxsolid #eee;display: none;}
.card .card-body .filter_panel { padding: 20px;}
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
							<h4>All Clients</h4>
							<div class="card-header-action">
								<div class="drop_table_data" style="display: inline-block;margin-right: 10px;"> 
									<button type="button" class="btn btn-primary dropdown-toggle"><i class="fas fa-columns"></i></button>
									<div class="dropdown_list client_dropdown_list">
										<label class="dropdown-option all"><input type="checkbox" value="all" checked /> Display All</label>
										<label class="dropdown-option"><input type="checkbox" value="3" checked /> Agent</label>
										<label class="dropdown-option"><input type="checkbox" value="4" checked /> Client Id</label>
										<label class="dropdown-option"><input type="checkbox" value="5" checked /> Phone</label>
										<label class="dropdown-option"><input type="checkbox" value="6" checked /> City/Postcode/State</label>
										<label class="dropdown-option"><input type="checkbox" value="7" checked /> Assignee</label>
										<label class="dropdown-option"><input type="checkbox" value="8" checked /> Status</label>
										<label class="dropdown-option"><input type="checkbox" value="9" checked /> Applications</label>
										<label class="dropdown-option"><input type="checkbox" value="10" checked /> Last Updated</label>
										<label class="dropdown-option"><input type="checkbox" value="11" checked /> Preferred Intake</label>
										
									</div>
								</div>
								<a href="javascript:;" class="btn btn-theme btn-theme-sm" data-bs-toggle="modal" data-bs-target="#importClientModal" title="Import Client">
									<i class="fas fa-upload"></i> Import Client
								</a>
								<a href="javascript:;" class="btn btn-theme btn-theme-sm filter_btn"><i class="fas fa-filter"></i> Filter</a>
							</div>
						</div>
						<div class="card-body">
							
							<ul class="nav nav-pills" id="client_tabs" role="tablist">
								<li class="nav-item is_checked_client" style="display:none;">
									<a class="btn btn-primary emailmodal" href="javascript:;"  >Send Mail</a>
								</li>
								<li class="nav-item is_checked_client" style="display:none;">
									<a class="btn btn-primary " href="javascript:;"  >Change Assignee</a>
								</li>
								
								<li class="nav-item is_checked_client_merge" style="display:none;">
									<a class="btn btn-primary " href="javascript:;"  >Merge</a>
								</li>
								
								<li class="nav-item is_checked_clientn">
									<a class="nav-link active" id="clients-tab"  href="{{URL::to('/clients')}}" >Clients</a>
								</li>
								<li class="nav-item is_checked_clientn">
									<a class="nav-link" id="archived-tab"  href="{{URL::to('/archived')}}" >Archived</a>
								</li>
							</ul> 
							<div class="tab-content" id="clientContent">	
							<div class="filter_panel">
								<h4>Search By Details</h4>								
								<form action="{{URL::to('/clients')}}" method="get">
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label for="pnr" class="col-form-label">Client ID</label>
												{!! Form::text('client_id', Request::get('client_id'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Client ID', 'id' => 'client_id' ))  !!}
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="company_name" class="col-form-label">Name</label>
												{!! Form::text('name', Request::get('name'), array('class' => 'form-control agent_company_name', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Name', 'id' => 'name' ))  !!}
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="email" class="col-form-label">Email</label>
												{!! Form::text('email', Request::get('email'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Email', 'id' => 'email' ))  !!}
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="phone" class="col-form-label">Phone</label>
												{!! Form::text('phone', Request::get('phone'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Phone', 'id' => 'phone' ))  !!}
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="type" class="col-form-label">Type</label>
												<select class="form-control" name="type">
												<option value="">Select</option>
												<option value="client">Client</option>
												<option value="lead">Lead</option>
												</select>
											</div>
										</div>
										
									</div>
									<div class="row">
										<div class="col-md-12 text-center">
									
											{!! Form::submit('Search', ['class'=>'btn btn-primary btn-theme-lg' ])  !!}
											<a class="btn btn-info" href="{{URL::to('/clients')}}">Reset</a>
										</div>
									</div>
								</form>
							</div>							
								<div class="tab-pane fade show active" id="clients" role="tabpanel" aria-labelledby="clients-tab">
									<div class="table-responsive common_table client_table_data"> 
										<table class="table text_wrap">
											<thead> 
												<tr> 
													<th class="text-center" style="width:30px;">
														<div class="custom-checkbox custom-checkbox-table custom-control">
															<input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad" class="custom-control-input" id="checkbox-all">
															<label for="checkbox-all" class="custom-control-label">&nbsp;</label>
														</div>
													</th>	
													<th>Name</th>
													<th>Agent</th>
													<th>Client ID</th>
													<th>City/Postcode/State</th>
													<th>Assignee</th>
													<th>Status</th>
													<th>Applications</th>
													<th>Last Updated</th>
													<th>Preferred Intake</th>
													<th></th>
												</tr> 
											</thead>
											
											<tbody class="tdata">	
												@if(@$totalData !== 0)
													<?php $i=0; ?>
												@foreach (@$lists as $list)
												<tr id="id_{{@$list->id}}"> 
													<td style="white-space: initial;" class="text-center">
														<div class="custom-checkbox custom-control">
															<input data-id="{{@$list->id}}" data-email="{{@$list->email}}" data-name="{{@$list->first_name}} {{@$list->last_name}}" data-clientid="{{@$list->client_id}}" type="checkbox" data-checkboxes="mygroup" class="cb-element custom-control-input your-checkbox" id="checkbox-{{$i}}">
															<label for="checkbox-{{$i}}" class="custom-control-label">&nbsp;</label>
														</div>
													</td>
													<td style="white-space: initial;"><a href="{{URL::to('/clients/detail/'.base64_encode(convert_uuencode(@$list->id)))}}">{{ @$list->first_name == "" ? config('constants.empty') : str_limit(@$list->first_name, '50', '...') }} {{ @$list->last_name == "" ? config('constants.empty') : str_limit(@$list->last_name, '50', '...') }} </a><span class="badge btn-warning"><?php echo $list->type; ?></span><br/>{{--<a data-id="{{@$list->id}}" data-email="{{@$list->email}}" data-name="{{@$list->first_name}} {{@$list->last_name}}" href="javascript:;" class="clientemail">{{ @$list->email == "" ? config('constants.empty') : str_limit(@$list->email, '50', '...') }}</a>--}}</td> 
													<?php
													$agent = \App\Models\Agent::where('id', $list->agent_id)->first();
													?>
													<td style="white-space: initial;">@if($agent) <a target="_blank" href="{{URL::to('/agent/detail/'.base64_encode(convert_uuencode(@$agent->id)))}}">{{@$agent->full_name}}<a/>@else - @endif</td>
													<td style="white-space: initial;">{{ @$list->client_id == "" ? config('constants.empty') : str_limit(@$list->client_id, '50', '...') }}</td> 
													{{--<td>{{ @$list->phone == "" ? config('constants.empty') : str_limit(@$list->phone, '50', '...') }}</td> --}}
													
													<?php
													$stateAbbrev = [
														'Australian Capital Territory' => 'ACT',
														'New South Wales' => 'NSW',
														'Northern Territory' => 'NT',
														'Queensland' => 'QLD',
														'South Australia' => 'SA',
														'Tasmania' => 'TAS',
														'Victoria' => 'VIC',
														'Western Australia' => 'WA',
													];
													$cityPart = trim(@$list->city ?? '') !== '' ? str_limit($list->city, 30, '...') : '-';
													$zipPart = trim(@$list->zip ?? '') !== '' ? $list->zip : '-';
													$stateVal = trim(@$list->state ?? '');
													$statePart = $stateVal !== '' ? ($stateAbbrev[$stateVal] ?? $stateVal) : '-';
													$locationDisplay = $cityPart . '/' . $zipPart . '/' . $statePart;
													?>
													<td style="white-space: initial;" title="{{ @$list->city }} {{ @$list->zip }} {{ @$list->state }}">{{ $locationDisplay }}</td>
													<?php
													// PostgreSQL doesn't accept empty strings for integer columns - check before querying
													$assignee = null;
													if(!empty(@$list->assignee) && @$list->assignee !== '') {
														$assignee = \App\Models\Admin::where('id', @$list->assignee)->first();
													}
													?>
													<td style="white-space: initial;">{{ @$assignee->first_name == "" ? config('constants.empty') : str_limit(@$assignee->first_name, '50', '...') }}</td> 
													<td ><span class="ag-label--circular" style="color: #6777ef" >
														In Progress
													</span></td>
													<td style="white-space: initial;"> - </td>
													<td style="white-space: initial;">{{date('d/m/Y', strtotime($list->updated_at))}}</td>
													<td style="white-space: initial;">{{ @$list->preferredIntake == "" ? config('constants.empty') : str_limit(@$list->preferredIntake, '50', '...') }}</td>  	
													<td style="white-space: initial;">
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">
																<a class="dropdown-item has-icon clientemail" data-id="{{@$list->id}}" data-email="{{@$list->email}}" data-name="{{@$list->first_name}} {{@$list->last_name}}" href="javascript:;" ><i class="far fa-envelope"></i> Email</a>
																<a class="dropdown-item has-icon" href="{{URL::to('/clients/edit/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="far fa-edit"></i> Edit</a>
																<a class="dropdown-item has-icon" href="{{URL::to('/clients/export/'.$list->id)}}" title="Export Client Data"><i class="fas fa-download"></i> Export</a>
																<a class="dropdown-item has-icon" href="javascript:;" onclick="deleteAction({{$list->id}}, 'admins')"><i class="fas fa-trash"></i> Archived</a>
															</div>
														</div>								  
													</td>
												</tr>	
												<?php $i++; ?>
												@endforeach	
											</tbody>
											@else
											<tbody>
												<tr>
													<td style="text-align:center;" colspan="11">
														No Record found
													</td>
												</tr>
											</tbody>
											@endif
										</table>
									</div>
								</div>
								 
							</div> 
						</div>
						<div class="card-footer">
							{!! $lists->appends(\Request::except('page'))->render() !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
 
<div id="emailmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">Compose Email</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" name="sendmail" action="{{URL::to('/sendmail')}}" autocomplete="off" enctype="multipart/form-data">
				@csrf
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">From <span class="span_req">*</span></label>
								<select class="form-control" name="email_from">
									<?php
									$emails = \App\Models\Email::select('email')->where('status', 1)->get();
									foreach($emails as $email){
										?>
											<option value="<?php echo $email->email; ?>"><?php echo $email->email; ?></option>
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
								<label for="email_to">To <span class="span_req">*</span></label>
								<select data-valid="required" class="js-data-example-ajax" name="email_to[]"></select>
								
								@if ($errors->has('email_to'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_to') }}</strong>
									</span> 
								@endif
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
								{!! Form::text('subject', '', array('class' => 'form-control selectedsubject', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Subject' ))  !!}
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
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Import Client Modal -->
<div id="importClientModal" data-bs-backdrop="static" data-bs-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload"></i> Import Client from File
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" name="importClientForm" action="{{URL::to('/clients/import')}}" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Instructions:</strong> Upload a JSON file exported from migrationmanager2 or bansalcrm2 to import client data.
                    </div>
                    
                    <div class="form-group">
                        <label for="import_file">Select JSON File <span class="span_req">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="import_file" name="import_file" accept=".json" required>
                            <label class="custom-file-label" for="import_file">Choose file...</label>
                        </div>
                        <small class="form-text text-muted">Only JSON files exported from CRM systems are supported.</small>
                        @if ($errors->has('import_file'))
                            <span class="custom-error" role="alert">
                                <strong>{{ @$errors->first('import_file') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="skip_duplicates" name="skip_duplicates" value="1" checked>
                            <label class="form-check-label" for="skip_duplicates">
                                Skip if client with same email or phone number already exists
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Import Client
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
	$('.filter_btn').on('click', function(){
		$('.filter_panel').slideToggle();
	});
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
    
    //alert(checked_length);
    if(checked_length == 2){
        $('.is_checked_client_merge').show();
    } else {
        $('.is_checked_client_merge').hide();
    }
  });
});


    //merge task
    /*$(document).delegate('.is_checked_client_merge', 'click', function(){
        if (confirm("Are you sure want to merge these records?")) {
            var array = [];
            $('.cb-element:checked').each(function(){
                var id = $(this).attr('data-id');
                array.push(id);
            });
            //alert(array.join(","));
            var merge_record_ids = array.join(",");
            $.ajax({
                type:'post',
                url:"{{URL::to('/')}}/merge_records",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {merge_record_ids:merge_record_ids},
                success: function(response){
                    var obj = $.parseJSON(response);
                    //console.log(obj.message);
                    location.reload(true);
                }
            });
        }
        return false;
    });*/
    
    var clickedOrder = [];
    var clickedIds = [];
    $(document).delegate('.your-checkbox', 'click', function(){
        var clicked_id = $(this).data('id');
        var nameStr = $(this).attr('data-name');
        var clientidStr = $(this).attr('data-clientid');
        var finalStr = nameStr+'('+clientidStr+')'; //console.log('finalStr='+finalStr);
        if ($(this).is(':checked')) {
            clickedOrder.push(finalStr);
            clickedIds.push(clicked_id);
        } else {
            var index = clickedOrder.indexOf(finalStr);
            if (index !== -1) {
                clickedOrder.splice(index, 1);
            }
            var index1 = clickedIds.indexOf(clicked_id);
            if (index1 !== -1) {
                clickedIds.splice(index1, 1);
            }
        }
    });

    //merge task
    $(document).delegate('.is_checked_client_merge', 'click', function(){
        if ( clickedOrder.length > 0 && clickedOrder.length == 2 )
        {
            var mergeStr = "Are you sure want to merge "+clickedOrder[0]+" record into this "+clickedOrder[1]+" record?";
            if (confirm(mergeStr)) {
                $.ajax({
                    type:'post',
                    url:"{{URL::to('/')}}/merge_records",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {merge_from:clickedIds[0],merge_into:clickedIds[1]},
                    success: function(response){
                        var obj = $.parseJSON(response);
                        //console.log(obj.message);
                        location.reload(true);
                    }
                });
                //return false;
            }
        }
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
$(document).delegate('.emailmodal', 'click', function(){

	$('#emailmodal').modal('show');
	var array = [];
	var data = [];
	$('.cb-element:checked').each(function(){
		
			var id = $(this).attr('data-id');
			 array.push(id);
			var email = $(this).attr('data-email');
			var name = $(this).attr('data-name');
			var status = 'Client';
			
			data.push({
				id: id,
  text: name,
  html:  "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

      "<div  class='ag-flex ag-align-start'>" +
        "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'>"+name+"</span>&nbsp;</div>" +
        "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'>"+email+"</small ></div>" +
      
      "</div>" +
      "</div>" +
	   "<div class='ag-flex ag-flex-column ag-align-end'>" +
        
        "<span class='ui label yellow select2-result-repository__statistics'>"+ status +
          
        "</span>" +
      "</div>" +
    "</div>",
  title: name
				});
		


	});
	
	$(".js-data-example-ajax").select2({
  data: data,
  escapeMarkup: function(markup) {
    return markup;
  },
  templateResult: function(data) {
    return data.html;
  },
  templateSelection: function(data) {
    return data.text;
  }
})
	$('.js-data-example-ajax').val(array);
		$('.js-data-example-ajax').trigger('change');
	
});

$(document).delegate('.clientemail', 'click', function(){
	$('#emailmodal').modal('show');
	var array = [];
	var data = [];

		
			var id = $(this).attr('data-id');
			 array.push(id);
			var email = $(this).attr('data-email');
			var name = $(this).attr('data-name');
			var status = 'Client';
			
			data.push({
				id: id,
  text: name,
  html:  "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

      "<div  class='ag-flex ag-align-start'>" +
        "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'>"+name+"</span>&nbsp;</div>" +
        "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'>"+email+"</small ></div>" +
      
      "</div>" +
      "</div>" +
	   "<div class='ag-flex ag-flex-column ag-align-end'>" +
        
        "<span class='ui label yellow select2-result-repository__statistics'>"+ status +
          
        "</span>" +
      "</div>" +
    "</div>",
  title: name
				});
	
	$(".js-data-example-ajax").select2({
  data: data,
  escapeMarkup: function(markup) {
    return markup;
  },
  templateResult: function(data) {
    return data.html;
  },
  templateSelection: function(data) {
    return data.text;
  }
})
	$('.js-data-example-ajax').val(array);
		$('.js-data-example-ajax').trigger('change');
	
});
$(document).delegate('.selecttemplate', 'change', function(){
	var v = $(this).val();
	$.ajax({
		url: '{{URL::to('/get-templates')}}',
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
	$('.js-data-example-ajax').select2({
		 multiple: true,
		 closeOnSelect: false,
		dropdownParent: $('#emailmodal'),
		  ajax: {
			url: '{{URL::to('/clients/get-recipients')}}',
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

$('.js-data-example-ajaxcc').select2({
		 multiple: true,
		 closeOnSelect: false,
		dropdownParent: $('#emailmodal'),
		  ajax: {
			url: '{{URL::to('/clients/get-recipients')}}',
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

// File input label update for import modal
$('#import_file').on('change', function() {
    var fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').html(fileName || 'Choose file...');
});

// Show error toast notification when import fails
@if ($errors->has('import_file'))
    $(document).ready(function() {
        // Keep modal open if there's an error
        var modalElement = document.getElementById('importClientModal');
        if (modalElement) {
            var modal = bootstrap.Modal.getInstance(modalElement);
            if (!modal) {
                modal = new bootstrap.Modal(modalElement);
            }
            modal.show();
        }
        
        // Show toast notification with specific error
        var errorMessage = {!! json_encode($errors->first('import_file')) !!};
        if (typeof iziToast !== 'undefined') {
            iziToast.error({
                title: 'Import Failed',
                message: errorMessage,
                position: 'topRight',
                timeout: 8000,
                closeOnClick: true,
                pauseOnHover: true,
                displayMode: 'replace'
            });
        } else {
            // Fallback to alert if iziToast is not available
            alert('Import Error:\n\n' + errorMessage);
        }
    });
@endif

// Show success toast notification when import succeeds
@if (Session::has('success'))
    $(document).ready(function() {
        var successMessage = {!! json_encode(Session::get('success')) !!};
        if (typeof iziToast !== 'undefined') {
            iziToast.success({
                title: 'Import Successful',
                message: successMessage,
                position: 'topRight',
                timeout: 5000,
                closeOnClick: true,
                pauseOnHover: true
            });
        }
    });
@endif
</script>
@endsection