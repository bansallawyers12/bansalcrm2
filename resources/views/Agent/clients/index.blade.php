@extends('layouts.agent')
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
										<label class="dropdown-option"><input type="checkbox" value="4" checked /> Tag(s)</label>
										<label class="dropdown-option"><input type="checkbox" value="5" checked /> Rating</label>
										<label class="dropdown-option"><input type="checkbox" value="6" checked /> Client Id</label>
										<label class="dropdown-option"><input type="checkbox" value="7" checked /> Phone</label>
										<label class="dropdown-option"><input type="checkbox" value="8" checked /> </label>
										<label class="dropdown-option"><input type="checkbox" value="9" checked /> Current City</label>
										<label class="dropdown-option"><input type="checkbox" value="10" checked /> Assignee</label>
										<label class="dropdown-option"><input type="checkbox" value="11" checked /> Followers</label>
										<label class="dropdown-option"><input type="checkbox" value="12" checked /> Status</label>
										<label class="dropdown-option"><input type="checkbox" value="13" checked /> Applications</label>
										<label class="dropdown-option"><input type="checkbox" value="14" checked /> Last Updated</label>
										<label class="dropdown-option"><input type="checkbox" value="15" checked /> Preferred Intake</label>
										
									</div>
								</div>
								<a href="{{route('agent.clients.create')}}" class="btn btn-primary">Create Client</a>
								<a href="javascript:;" class="btn btn-theme btn-theme-sm filter_btn"><i class="fas fa-filter"></i> Filter</a>
							</div>
						</div>
						<div class="card-body">
							
							<ul class="nav nav-pills" id="client_tabs" role="tablist">
							
								<li class="nav-item is_checked_clientn">
									<a class="nav-link active" id="clients-tab"  href="{{URL::to('/agent/clients')}}" >Clients</a>
								</li>
								<li class="nav-item is_checked_clientn">
									<a class="nav-link" id="archived-tab"  href="{{URL::to('/agent/archived')}}" >Archived</a>
								</li>
							</ul> 
							<div class="tab-content" id="clientContent">	
							<div class="filter_panel">
								<h4>Search By Details</h4>								
								<form action="{{URL::to('/agent/clients')}}" method="get">
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label for="pnr" class="col-form-label">Client ID</label>
												{{ Form::text('client_id', Request::get('client_id'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Client ID', 'id' => 'client_id' )) }}
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="company_name" class="col-form-label">Name</label>
												{{ Form::text('name', Request::get('name'), array('class' => 'form-control agent_company_name', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Name', 'id' => 'name' )) }}
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="email" class="col-form-label">Email</label>
												{{ Form::text('email', Request::get('email'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Email', 'id' => 'email' )) }}
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="phone" class="col-form-label">Phone</label>
												{{ Form::text('phone', Request::get('phone'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Phone', 'id' => 'phone' )) }}
											</div>
										</div>
										
										
									</div>
									<div class="row">
										<div class="col-md-12 text-center">
									
											{{ Form::submit('Search', ['class'=>'btn btn-primary btn-theme-lg' ]) }}
											<a class="btn btn-info" href="{{URL::to('/agent/clients')}}">Reset</a>
										</div>
									</div>
								</form>
							</div>							
								<div class="tab-pane fade show active" id="clients" role="tabpanel" aria-labelledby="clients-tab">
									<div class="table-responsive common_table client_table_data"> 
										<table class="table text_wrap">
											<thead> 
												<tr> 
													
													<th>Name</th>
													
													<th>Tag(s)</th>
													<th>Rating</th>
													<th>Client ID</th>
													<th>Phone</th>
													<th>Current City</th>
													<th>Assignee</th>
													<th>Followers</th>
													<th>Status</th>
													<th>Applications</th>						<th>Last Updated</th>
													<th>Preferred Intake</th>
													<th></th>
												</tr> 
											</thead>
											
											<tbody class="tdata">	
												@if(@$totalData !== 0)
													<?php $i=0; ?>
												@foreach (@$lists as $list)
												<tr id="id_{{@$list->id}}"> 
													
													<td><a href="{{URL::to('/agent/clients/detail/'.base64_encode(convert_uuencode(@$list->id)))}}">{{ @$list->first_name == "" ? config('constants.empty') : str_limit(@$list->first_name, '50', '...') }} {{ @$list->last_name == "" ? config('constants.empty') : str_limit(@$list->last_name, '50', '...') }}</a><br/>{{ @$list->email == "" ? config('constants.empty') : str_limit(@$list->email, '50', '...') }}</td> 
												
												
													<td>
													<?php if($list->tagname != ''){ 
													$rs = explode(',', $list->tagname);
													$counttag = count($rs);
													if($counttag > 1){
														$tag = '';
														foreach($rs as $r){
															$stagds = \App\Tag::where('id','=',$r)->first();
															$tag .= '<li>'.@$stagds->name.'</li>';
														}
														$stagd = \App\Tag::where('id','=',$rs[0])->first();
														?>
														
														<div tabindex="0" data-html="true" data-toggle="popover" data-trigger="hover focus" title="Tags" data-content="<ul><?php echo @$tag; ?></ul>" class="ag-flex ag-align-center">
															<span  title="ff" class="col-hr-1 truncate">{{@$stagd->name}}</span> 
															<span class="ui label counter">+ {{@$counttag - 1}}</span>
														</div>
														<?php
													}else{
														$stagd = \App\Tag::where('id','=',$rs)->first();
														?>
														<div class="ag-flex ag-align-center">
															<span  title="ff" class="col-hr-1 truncate">{{@$stagd->name}}</span> 
															
														</div>
														<?php
													}
													?>
													
													<?php }else{ echo '-'; } ?>
													</td>
													<td><?php echo @$list->rating; ?></td>
													
													<td>{{ @$list->client_id == "" ? config('constants.empty') : str_limit(@$list->client_id, '50', '...') }}</td> 
													<td>{{ @$list->phone == "" ? config('constants.empty') : str_limit(@$list->phone, '50', '...') }}</td> 
													
													<td>{{ @$list->city == "" ? config('constants.empty') : str_limit(@$list->city, '50', '...') }}</td>
													<?php
													$assignee = \App\Admin::where('id',@$list->assignee)->first();
													$explode = explode(',', $list->followers);
													$followerss = '';
													foreach($explode as $exp){
														$followers = \App\Admin::where('id',@$exp)->first();
														$followerss .= @$followers->first_name.', ';
													}
													?>
													<td>{{ @$assignee->first_name == "" ? config('constants.empty') : str_limit(@$assignee->first_name, '50', '...') }}</td> 
													<td>{{ rtrim(@$followerss,', ') }}</td> 
													<td><span class="ag-label--circular" style="color: #6777ef" >
														In Progress
													</span></td>
													<td> - </td>
													<td>{{date('Y-m-d', strtotime($list->created_at))}}</td>
													<td>{{ @$list->preferredIntake == "" ? config('constants.empty') : str_limit(@$list->preferredIntake, '50', '...') }}</td>  	
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">
																
																<a class="dropdown-item has-icon" href="{{URL::to('/agent/clients/edit/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="far fa-edit"></i> Edit</a>
																
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
													<td style="text-align:center;" colspan="17">
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
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" name="sendmail" action="{{URL::to('/agent/sendmail')}}" autocomplete="off" enctype="multipart/form-data">
				@csrf
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">From <span class="span_req">*</span></label>
								<select class="form-control" name="email_from">
									<?php
									$emails = \App\Email::select('email')->where('status', 1)->get();
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
									@foreach(\App\CrmEmailTemplate::all() as $list)
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
		url: '{{URL::to('/agent/get-templates')}}',
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
			url: '{{URL::to('/agent/clients/get-recipients')}}',
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
			url: '{{URL::to('/agent/clients/get-recipients')}}',
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