@extends('layouts.admin')
@section('title', 'Leads')

@section('content')
<style>
.mytooltip{display: inline;position: relative;z-index: 999;}
.mytooltip .tooltip-item {background: rgba(0, 0, 0, 0.1);cursor: pointer;display: inline-block; font-weight: 500; padding: 0 10px;}
.mytooltip .tooltip-content {position: absolute;z-index: 9999;width: 360px;left: 50%;margin: 0 0 20px -180px;bottom: 100%;text-align: left;font-size: 14px;line-height: 30px; -webkit-box-shadow: -5px -5px 15px rgba(48, 54, 61, 0.2);box-shadow: -5px -5px 15px rgba(48, 54, 61, 0.2);background: #2b2b2b;opacity: 0;cursor: default;pointer-events: none;}
.mytooltip .tooltip-content::after {content: '';top: 100%;left: 50%;border: solid transparent; 
height: 0;width: 0;position: absolute;pointer-events: none;border-color: #2a3035 transparent transparent;border-width: 10px;margin-left: -10px;}
.mytooltip .tooltip-content img {position: relative;height: 140px;display: block;float: left; margin-right: 1em;}
.mytooltip .tooltip-item::after {content: '';position: absolute;width: 360px;height: 20px;
bottom: 100%;left: 50%;pointer-events: none;-webkit-transform: translateX(-50%);transform: translateX(-50%);}
.mytooltip:hover .tooltip-item::after {pointer-events: auto;}
.mytooltip:hover .tooltip-content {pointer-events: auto;opacity: 1;-webkit-transform: translate3d(0, 0, 0) rotate3d(0, 0, 0, 0deg);transform: translate3d(0, 0, 0) rotate3d(0, 0, 0, 0deg);}
.mytooltip:hover .tooltip-content2 {opacity: 1;font-size: 18px;}
.mytooltip .tooltip-text {font-size: 14px;line-height: 24px;display: block;padding: 1.31em 1.21em 1.21em 0;color: #fff;}
.filter_panel {background: #f7f7f7;margin-bottom: 10px;border: 1pxsolid #eee;display: none;}
.card .card-body .filter_panel { padding: 20px;}
</style>
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
							<h4>Leads</h4>
							<div class="card-header-action">
								<a href="{{route('admin.leads.create')}}" class="btn btn-primary">Create Leads</a>
								<a href="javascript:;" class="btn btn-theme btn-theme-sm filter_btn"><i class="fas fa-filter"></i> Filter</a>
							</div>
						</div>
						<div class="card-body">
						    <div class="filter_panel">
								<h4>Search By Details</h4>								
								<form action="{{URL::to('/admin/leads')}}" method="get">
									<div class="row">
									<div class="col-md-4">
											<div class="form-group">
												<label for="did" class="col-form-label" style="visibility:hidden;">Lead ID</label>
											<div class="row">
											    <div class="col-md-3">
											       <b>Lead -</b>
											        </div>
											    	<div class="col-md-7">		
											 {{ Form::text('id', Request::get('id'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Lead ID', 'id' => 'did' )) }}
											</div>	</div></div>
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
										
										<div class="col-md-4">
											<div class="form-group">
												<label for="from" class="col-form-label">From</label>
												{{ Form::text('from', Request::get('from'), array('class' => 'form-control filterdatepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'From', 'id' => '' )) }}
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="to" class="col-form-label">To</label>
												{{ Form::text('to', Request::get('to'), array('class' => 'form-control filterdatepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'To', 'id' => '' )) }}
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12 text-center">
									
											{{ Form::submit('Search', ['class'=>'btn btn-primary btn-theme-lg' ]) }}
											<a class="btn btn-info" href="{{URL::to('/admin/leads')}}">Reset</a>
										</div>
									</div>
								</form>
							</div>
							<div class="table-responsive common_table lead_table_data"> 
								<table class="table text_wrap">
									<thead>
										<tr>
											<th>Lead</th>
											<th>Client</th>
											<th>Services</th>
											<th>Level & Status</th>
											<th>Followup</th>
											<th>Action</th>
										</tr> 
									</thead>
									<tbody class="tdata">	
										@if(@$totalData !== 0)
										@foreach (@$lists as $list)	
										<?php $followpe = \App\Models\Followup::where('lead_id','=',$list->id)->where('followup_type','!=','assigned_to')->orderby('id','DESC')->with(['followutype'])->first(); 
										$followp = \App\Models\Followup::where('lead_id','=',$list->id)->where('followup_type','=','follow_up')->orderby('id','DESC')->with(['followutype'])->first();

										?> 
										<tr id="id_{{@$list->id}}">
											<td><i class="fa fa-ticket-alt"></i> <a class="" href="{{URL::to('/admin/leads/history/'.base64_encode(convert_uuencode(@$list->id)))}}">Lead - {{str_pad($list->id, 3, '0', STR_PAD_LEFT)}}</a> <br/><i class="fa fa-calendar-alt"></i> 
										
											{{@$list->created_at}}
											<?php
											$assigneduser = \App\Models\Admin::where('id', $list->assign_to)->first();
											if($assigneduser){
											    ?>
											    <br>
											   Assigned: <a target="_blank" href="{{URL::to('/admin/users/view')}}/{{$assigneduser->id}}">{{$assigneduser->first_name}} {{$assigneduser->first_name}}</a> 
											    <?php
											}else{ echo '-'; }
											?>
											</td>
											<td><i class="fa fa-user"></i>  {{@$list->first_name}} {{@$list->last_name}} <br/> <i class="fa fa-mobile"></i> {{@$list->phone}} <br/> <i class="fa fa-envelope"></i> {{@$list->email}}</td>
											<td>{{@$list->service}} <br/> {{@$list->created_at}}</td>
											<td><div class="lead_stars"><i class="fa fa-star"></i><span>{{@$list->lead_quality}}</span> {{@$followpe->followutype->name}}</div></td>
											@if($followp)
											@if(@$followp->followutype->type == 'follow_up')
											<td>{{$followp->followutype->name}}<br> {{date('d-m-Y h:i A', strtotime($followp->followup_date))}}</td>
											@else
											<td>{{@$followp->followutype->name}}</td>
											@endif
											@else
											<td>Not Contacted</td>
											@endif
											{{-- @if($followp)
											<td>{{ @$followp->note == "" ? config('constants.empty') : str_limit(@$followp->note, '20', '...') }}</td>
											@else
											<td>{{ @$list->latest_comment == "" ? config('constants.empty') : str_limit(@$list->latest_comment, '20', '...') }}</td>
											@endif --}}
											<td>
												<div class="dropdown action_toggle">
													<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i data-feather="more-vertical"></i></a>
													<div class="dropdown-menu">
														<a class="dropdown-item has-icon" href="{{URL::to('/admin/leads/edit/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="fa fa-edit"></i> Edit</a>
														<a class="dropdown-item has-icon" href="{{URL::to('/admin/leads/history/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="fa fa-history"></i> History</a>
														<a class="dropdown-item has-icon assignlead_modal" href="javascript:;" mleadid="{{base64_encode(convert_uuencode(@$list->id))}}"><i class="fa fa-edit"></i> Assign To</a>
										@if($list->converted == 0)
											<a class="dropdown-item has-icon" href="{{URL::to('/admin/leads/convert/'.@$list->id)}}" onclick="return confirm('Are you sure?')"><i class="fa fa-user"></i> Convert To Client</a>	
											@endif
													</div>
												</div>	
											</td>
										</tr>
										@endforeach 

										@else
										<tr>
											<td style="text-align:center;" colspan="10">
											No Record found
											</td>
										</tr>										
										@endif  
									</tbody>
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

<div class="modal fade" id="assignlead_modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				  <h4 class="modal-title">Assign Lead</h4>
				  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
			</div>
			{{ Form::open(array('url' => 'admin/leads/assign', 'name'=>"add-assign", 'autocomplete'=>'off', "enctype"=>"multipart/form-data", 'id'=>"addnoteform")) }}
			<div class="modal-body">
				<div class="form-group row">
					<div class="col-sm-12">
						<input id="mlead_id" name="mlead_id" type="hidden" value="">
						<select name="assignto" class="form-control select2 " style="width: 100%;" data-select2-id="1" tabindex="-1" aria-hidden="true">
							<option value="">Select</option>
							@foreach(\App\Models\Admin::Where('role', '!=', '7')->get() as $ulist)
							<option value="{{@$ulist->id}}">{{@$ulist->first_name}} {{@$ulist->last_name}}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				{{ Form::button('<i class="fa fa-save"></i> Assign Lead', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-assign")' ]) }}
			</div>
			 {{ Form::close() }}
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
         $('.assignlead_modal').on('click', function(){
			  var val = $(this).attr('mleadid');
			  $('#assignlead_modal #mlead_id').val(val);
			  $('#assignlead_modal').modal('show');
		  });
    });
</script>
@endsection