@extends('layouts.admin')
@section('title', 'Partners')

@section('content')
<style>
    .filter_panel {background: #f7f7f7;margin-bottom: 10px;border: 1pxsolid #eee;display: none;}
.card .card-body .filter_panel { padding: 20px;}
  a.dropdown-item {padding-top: 5px !important;}
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
							<h4 class="is_checked_clientn">All Active Partners</h4>
							<div class="card-header-action is_checked_clientn">
								<a href="{{route('partners.create')}}" class="btn btn-primary">Create Partner</a>
							</div>
							<div class="card-header-action is_checked_clientn">
								<a href="#" class="btn btn-primary importmodal"> Import csv</a>
							</div>
							<div class="card-header-action is_checked_client" style="display:none;">
								<a class="btn btn-primary emailmodal" href="javascript:;"  >Send Mail</a>
							</div>
							<a href="javascript:;" class="btn btn-theme btn-theme-sm filter_btn"><i class="fas fa-filter"></i> Filter</a>
						</div>
						<div class="card-body">
                            <ul class="nav nav-pills" id="partner_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link" id="partners-tab"  href="{{URL::to('/partners')}}" >Active</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="partners-inactive-tab"  href="{{URL::to('/partners-inactive')}}" >Inactive</a>
								</li>
							</ul>
                          
						    <div class="filter_panel">
								<h4>Search By Details</h4>								
								<form action="{{URL::to('/partners')}}" method="get">
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label for="ass_id" class="col-form-label">Name</label>
												{!! Form::text('name', Request::get('name'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Name', 'id' => 'name' ))  !!}
											</div>
										</div>				
										<div class="col-md-3">
											<div class="form-group">
												<label for="" class="col-form-label">Email</label>
												{!! Form::text('email', Request::get('email'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Email', 'id' => 'email' ))  !!}
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label for="" class="col-form-label">Reginal code</label>
												{!! Form::text('reginal_code', Request::get('reginal_code'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Reginal code', 'id' => 'reginal_code' ))  !!}
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label for="level" class="col-form-label">Level</label>
												{!! Form::text('level', Request::get('level'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Level', 'id' => 'level' ))  !!}
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
							<div class="table-responsive"> 
								<table class="table text_wrap">
									<thead>
										<tr>
											<th class="text-center" style="width:30px;">
												<div class="custom-checkbox custom-checkbox-table custom-control">
													<input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad" class="custom-control-input" id="checkbox-all">
													<label for="checkbox-all" class="custom-control-label">&nbsp;</label>
												</div>
											</th>
											<th>Sno.</th>
											<!--<th>ID</th>-->
											<th>@sortablelink('partner_name','Name')</th>
                                            <th>No Of Students</th>
											<th>Note</th>
											<th>Level</th>
											<!--<th>Workflow</th>
											<th>Partner Type</th>-->
											<th>City</th>
											<th>Products</th>
											
											<!--<th>Is Progress</th>-->
											<th></th>
										</tr> 
									</thead>
									<tbody class="tdata">	
										@if(@$totalData !== 0)
										<?php $i=0; ?>
										@foreach (@$lists as $list)
										<?php 
											$partnertype = \App\Models\PartnerType::where('id', $list->partner_type)->first();	
											$workflow = \App\Models\Workflow::where('id', $list->service_workflow)->first();
											$product = \App\Models\Product::where('partner', $list->id)->count();

											//Get partner latest notes
                                            $latestnote = \App\Models\Note::where('client_id',$list->id)->whereNull('assigned_to')->whereNull('task_group')->where('type','partner')->orderby('pin', 'DESC')->orderBy('created_at', 'DESC')->first();


										?>	
										<tr id="id_{{@$list->id}}">
											<td style="white-space: initial;" class="text-center">
												<div class="custom-checkbox custom-control">
													<input data-id="{{@$list->id}}" data-email="{{@$list->email}}" data-name="{{@$list->partner_name}}" type="checkbox" data-checkboxes="mygroup" class="cb-element custom-control-input" id="checkbox-{{$i}}">
													<label for="checkbox-{{$i}}" class="custom-control-label">&nbsp;</label>
												</div>
											</td>
											<td style="white-space: initial;">{{@$i+1}}</td>
											<!--<td style="white-space: initial;">{{--@$list->id--}}</td>-->
											<td style="white-space: initial;">
                                              <a href="{{URL::to('/partners/detail/'.base64_encode(convert_uuencode(@$list->id)))}}">{{ @$list->partner_name == "" ? config('constants.empty') : str_limit(@$list->partner_name, '50', '...') }}</a>
                                              <!--<br/>-->
                                              <!--<a data-id="{{--@$list->id--}}" data-email="{{--@$list->email--}}" data-name="{{--@$list->partner_name--}}" href="javascript:;" class="partneremail">{{--@$list->email == "" ? config('constants.empty') : str_limit(@$list->email, '50', '...')--}}</a>-->
                                          </td> 
											
                                          <td style="white-space: initial;">{{ $list->student_count }}</td> <!-- Display student count -->
                                            
                                          <!--<td style="white-space: initial;">
											<?php
											/*$branchesquery = \App\Models\PartnerBranch::where('partner_id', $list->id)->orderby('created_at', 'DESC')->get();
											$branches = '';
											foreach($branchesquery as $branch){
												$branches .= $branch->name.', ';
											}
											echo rtrim($branches,', ');*/
											?>
											</td>-->
                                            <td style="white-space: initial;">
                                                <?php
                                                if($latestnote){
                                                   //echo $latestnote->title;
                                                   ?>

                                                    <div class="note_col" id="note_id_{{$latestnote->id}}" style="width: 100%;float: left;margin-bottom: 20px;border-radius: 4px;box-shadow: 0 3px 8px 0 rgba(0, 0, 0, .08), 0 1px 2px 0 rgba(0, 0, 0, .1);">
                                                        <div class="note_content" style="background: #f4f4f4;padding: 10px;">
                                                            <h4 style="font-size: 14px;line-height: 18px;color: #000;margin: 0px 0px 5px;"><a class="viewnote" data-id="{{$latestnote->id}}" href="javascript:;">{{ @$latestnote->title == "" ? config('constants.empty') : str_limit(@$latestnote->title, '19', '...') }}</a></h4>
                                                        </div>
                                                        <div class="extra_content" style="background: #fcfcfc;padding: 10px 5px;border-top: 1px solid #ccc;float: left;width: 100%;">
                                                            <p><?php echo @$latestnote->description == "" ? config('constants.empty') : str_limit(@$latestnote->description, '15', '...'); ?></p>

                                                            <!--<div class="left">
                                                                <?php
                                                                //$adminInfo = \App\Models\Admin::select('first_name')->where('id', $latestnote->user_id)->first();
                                                                ?>
                                                                <div class="author">
                                                                    <a href="#">{{--substr($adminInfo->first_name, 0, 1)--}}</a>
                                                                </div>
                                                                <div class="note_modify">
                                                                    <small>Last Modified <span>{{--date('Y-m-d', strtotime($latestnote->updated_at))--}}</span></small>
                                                                </div>
                                                            </div>
                                                            <div class="right">
                                                                <div class="dropdown d-inline dropdown_ellipsis_icon">
                                                                    <a class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                                                    <div class="dropdown-menu">
                                                                        <a class="dropdown-item opennoteform" data-id="{{--$latestnote->id--}}" href="javascript:;">Edit</a>
                                                                        <a data-id="{{--$latestnote->id--}}" data-href="deletenote" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
                                                                        <?php //if($latestnote->pin == 1){
                                                                            ?>
                                                                            <a data-id="<?php //echo $latestnote->id; ?>"  class="dropdown-item pinnote" href="javascript:;" >UnPin</a>
                                                                            <?php
                                                                        //}else{ ?>
                                                                            <a data-id="<?php //echo $latestnote->id; ?>"  class="dropdown-item pinnote" href="javascript:;" >Pin</a>
                                                                        <?php //} ?>
                                                                    </div>
                                                                </div>
                                                            </div>-->
                                                        </div>
                                                    </div>
                                                <?php
                                                   //echo $latestnote->description;
                                                } else {
                                                    echo "N/A";
                                                }
                                                ?>
                                            </td>
											<td style="white-space: initial;">@if($list->level===0)
											<span class="badge badge-danger">{{@$list->level}}</span>
											@elseif($list->level===1)
											<span class="badge badge-success">{{@$list->level}}</span>
											@else
											<span class="badge badge-info">{{@$list->level}}</span>
											@endif
											</td>
											<!--<td style="white-space: initial;">{{--@$workflow->name--}}</td>
											<td style="white-space: initial;">{{--@$partnertype->name--}}</td>-->
											
											<td style="white-space: initial;">{{ @$list->city == "" ? config('constants.empty') : str_limit(@$list->city, '50', '...') }}<br/>{{ @$list->country == "" ? config('constants.empty') : str_limit(@$list->country, '50', '...') }}</td> 
											<td style="white-space: initial;">{{$product}}</td> 
											
											<!--<td><span class="ag-label--circular" style="color: #6777ef" >In Progress</span></td>-->	
											<td>
												<div class="dropdown d-inline">
													<button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
													<div class="dropdown-menu"> 
														<a class="dropdown-item has-icon partneremail" data-id="{{@$list->id}}" data-email="{{@$list->email}}" data-name="{{@$list->partner_name}}" href="javascript:;" ><i class="far fa-envelope"></i> Email</a>
														<a class="dropdown-item has-icon" href="{{URL::to('/partners/edit/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="far fa-edit"></i> Edit</a>
														<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction({{@$list->id}}, 'partners')"><i class="fas fa-trash"></i> Delete</a>
                                                      <a class="dropdown-item has-icon" href="javascript:;" onclick="partnerchangetoinactive({{$list->id}}, 'partners')"><i class="fas fa-trash"></i> Inactive</a>
													
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
											<td style="text-align:center;" colspan="10">
												No Record found
											</td>
										</tr>
									</tbody>
									@endif
								</table> 
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
<div id="importmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="importmodalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="importmodalLabel">Import CSV</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{URL::to('/partners-import')}}"  enctype="multipart/form-data">
				@csrf
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="import">Select Import File<span class="span_req">*</span></label>
								<input type="file" required class="form-control" name="uploaded_file" id="uploaded_file">
								<small class="warning text-muted">Please upload only CSV file</small>
							</div>
						</div>
						
						<div class="col-12 col-md-12 col-lg-12">
							<button  type="submit" class="btn btn-primary">Import</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="emailmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="partnerModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="partnerModalLabel">Compose Email</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" name="sendmail" action="{{URL::to('/sendmail')}}" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type ="hidden" value="partner" name="type">
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




@endsection
@section('scripts')
<script>
 function partnerchangetoinactive( id, table ) {
    var conf = confirm('Do you want to change status to inactive?');
    if(conf){
        if(id == '') {
            alert('Please select ID to update the record.');
            return false;
        } else {
            $(".server-error").html(''); //remove server error.
            $(".custom-error-msg").html(''); //remove custom error.
            $.ajax({
                type:'post',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url:site_url+'/partner_change_to_inactive',
                data:{'id': id, 'table' : table},
                success:function(resp) {
                    var obj = $.parseJSON(resp);
                    if(obj.status == 1) {
                        $("#quid_"+id+' .statusupdate').html(obj.astatus);
                        //show success msg
                        var html = successMessage(obj.message);
                        $(".custom-error-msg").html(html);
                        //show count
                    } else{
                        var html = errorMessage(obj.message);
                        $(".custom-error-msg").html(html);
                    }
                    location.reload();
                    $("#loader").hide();
                },
                beforeSend: function() {
                    $("#loader").show();
                }
            });
            $('html, body').animate({scrollTop:0}, 'slow');
        }
    } else{
        $("#loader").hide();
    }
}
  
jQuery(document).ready(function($){
    let currentUrl = window.location.href; console.log(currentUrl);
    var currentUrlArr = currentUrl.split("/"); //console.log(currentUrlArr[4]);
    if( currentUrlArr.length >0 ){
        if(currentUrlArr[4] == 'partners'){
            $('a#partners-tab').addClass('active');
        } else if(currentUrlArr[4] == 'partners-inactive'){
            $('a#partners-inactive-tab').addClass('active');
        }
    }
  
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

$(document).delegate('.importmodal', 'click', function(){

$('#importmodal').modal('show');
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
			var status = 'Partner';
			
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

$(document).delegate('.partneremail', 'click', function(){

	$('#emailmodal').modal('show');
	var array = [];
	var data = [];

		
			var id = $(this).attr('data-id');
			 array.push(id);
			var email = $(this).attr('data-email');
			var name = $(this).attr('data-name');
			var status = 'Partner';
			
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
			url: '{{URL::to('/partners/get-recipients')}}',
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
			url: '{{URL::to('/partners/get-recipients')}}',
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
$('.filter_btn').on('click', function(){
		$('.filter_panel').slideToggle();
	});
});
</script>
@endsection