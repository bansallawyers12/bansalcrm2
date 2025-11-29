@extends('layouts.admin')
@section('title', 'Agent Detail')

@section('content')
<style>
.ag-space-between {justify-content: space-between;} 
.ag-align-center {align-items: center;}
.ag-flex {display: flex;}
.ag-align-start {align-items: flex-start;}
.ag-flex-column {flex-direction: column;}  
.col-hr-1 {margin-right: 5px!important;}
.text-semi-bold {font-weight: 600!important;}
.small, small {font-size: 85%;}
.ag-align-end { align-items: flex-end;}

.ui.yellow.label, .ui.yellow.labels .label {background-color: #fbbd08!important;border-color: #fbbd08!important;color: #fff!important;}
.ui.label:last-child {margin-right: 0;}
.ui.label:first-child { margin-left: 0;}
.field .ui.label {padding-left: 0.78571429em; padding-right: 0.78571429em;}
.ag-appointment-list__title{padding-left: 1rem; text-transform: uppercase;}
.zippyLabel{background-color: #e8e8e8; line-height: 1;display: inline-block;color: rgba(0,0,0,.6);font-weight: 700; border: 0 solid transparent; font-size: 10px;padding: 3px; }
.accordion .accordion-header.app_green{background-color: #54b24b;color: #fff;}
.accordion .accordion-header.app_green .accord_hover a{color: #fff!important;}
.accordion .accordion-header.app_blue{background-color: rgba(3,169,244,.1);color: #03a9f4;}
.card .card-body table tbody.taskdata tr td span.check{background: #71cc53;color: #fff; border-radius: 50%;font-size: 10px;line-height: 14px;padding: 3px 4px;width: 18px;height: 18px;
display: inline-block;}
.card .card-body table tbody.taskdata tr td span.round{background: #fff;border:1px solid #000; border-radius: 50%;font-size: 10px;line-height: 14px;padding: 2px 5px;width: 16px;height: 16px; display: inline-block;}
#opentaskview .modal-body ul.navbar-nav li .dropdown-menu{transform: none!important; top:40px!important;} 
</style>
<?php
use App\Http\Controllers\Controller;
?>
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
							<h4>Agent Detail</h4>
							<div class="card-header-action">
								<a href="{{route('admin.agents.active')}}" class="btn btn-primary">Agent List</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-3 col-md-3 col-lg-3">
					<div class="card author-box">
						<div class="card-body">
							<div class="author-box-center">
								<span class="author-avtar" style="background: rgb(68, 182, 174);"><b>{{substr($fetchedData->full_name, 0, 1)}}</b></span>
								<div class="clearfix"></div>
								<div class="author-tag">
									<span>{{$fetchedData->struture}}</span>
								</div>
								<div class="author-box-name">
									<a href="#">{{$fetchedData->full_name}}</a>
								</div>
								<div class="author-mail_sms">
									<a href="#" title="Compose SMS"><i class="fas fa-comment-alt"></i></a>
									<a href="javascript:;" data-id="{{@$fetchedData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->full_name}}" class="clientemail" title="Compose Mail"><i class="fa fa-envelope"></i></a>  
									<a href="{{URL::to('/admin/agents/edit/'.base64_encode(convert_uuencode(@$fetchedData->id)))}}" title="Edit"><i class="fa fa-edit"></i></a>
								</div>
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header">
							<h4>General Information</h4>
						</div>
						<div class="card-body">
							<p class="clearfix"> 
								<span class="float-left">Phone No:</span>
								<span class="float-right text-muted">{{$fetchedData->country_code}}{{$fetchedData->phone}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Email:</span>
								<span class="float-right text-muted">{{$fetchedData->email}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Address:</span>
								<span class="float-right text-muted">{{$fetchedData->address}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Agent Type:</span>
								<span class="float-right text-muted"><a href="javascript:;" class="btn btn-sm btn-outline-primary">{{$fetchedData->agent_type}}</a> </span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Claim Revenue Percentage:</span>
								<span class="float-right text-muted">{{$fetchedData->claim_revenue}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Income Sharing Percentage:</span>
								<span class="float-right text-muted">{{$fetchedData->income_sharing}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Associated Offices:</span>
								<?php 
									$branches = \App\Branch::where('id', $fetchedData->related_office)->first();
								?>	
								<span class="float-right text-muted"><?php echo $branches->office_name;?></span>
							</p>							
							<p class="clearfix"> 
								<span class="float-left">Tax Number:</span>
								<span class="float-right text-muted">{{$fetchedData->tax_number}}</span>
							</p>
						</div>
					</div>
				</div>
				<div class="col-9 col-md-9 col-lg-9">
					<div class="card">
						<div class="card-body">
							<ul class="nav nav-pills" id="agents_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" data-toggle="tab" id="noteterm-tab" href="#noteterm" role="tab" aria-controls="noteterm" aria-selected="true">Notes & Terms</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="reffered_client-tab" href="#reffered_client" role="tab" aria-controls="reffered_client" aria-selected="false">Referred Clients</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="application-tab" href="#application" role="tab" aria-controls="application" aria-selected="false">Applications</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="accounts-tab" href="#accounts" role="tab" aria-controls="accounts" aria-selected="false">Accounts</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="represent_partner-tab" href="#represent_partner" role="tab" aria-controls="represent_partner" aria-selected="false">Representing Partners</a>
								</li>
							</ul> 
							<div class="tab-content" id="agentContent" style="padding-top:15px;">
								<div class="tab-pane fade show active" id="noteterm" role="tabpanel" aria-labelledby="noteterm-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<a href="javascript:;" datatype="note" class="create_note btn btn-primary"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="note_term_list"> 									
									<?php									
									$notelist = \App\Note::where('client_id', $fetchedData->id)->where('type', 'agent')->orderby('created_at', 'DESC')->get();
									foreach($notelist as $list){
										$admin = \App\Admin::where('id', $list->user_id)->first();
									?>
										<div class="note_col" id="note_id_{{$list->id}}"> 
											<div class="note_content">
												<h4><a class="viewnote" data-id="{{$list->id}}" href="javascript:;">{{ @$list->title == "" ? config('constants.empty') : str_limit(@$list->title, '19', '...') }}</a></h4>
												<p><?php //echo @$list->description == "" ? config('constants.empty') : str_limit(@$list->description, '15', '...'); ?></p>
											</div>
											<div class="extra_content">
												<div class="left">
													<div class="author">
														<a href="#">{{substr($admin->first_name, 0, 1)}}</a>
													</div>
													<div class="note_modify">
														<small>Last Modified <span>{{date('Y-m-d', strtotime($list->updated_at))}}</span></small>
													</div>
												</div>  
												<div class="right">
													<div class="dropdown d-inline dropdown_ellipsis_icon">
														<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">
															<a class="dropdown-item opennoteform" data-id="{{$list->id}}" href="javascript:;">Edit</a>
															<a data-id="{{$list->id}}" data-href="deletenote" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="tab-pane fade" id="reffered_client" role="tabpanel" aria-labelledby="reffered_client-tab">
									<div class="table-responsive"> 
										<table class="table text_wrap table-2">
											<thead>
												<tr>
													<th>Name</th>
													<th>Tag</th>
													<th>City / Country</th>
													<th>Status</th>
													<th>Assignee</th>
													<th>Added Date</th>
												</tr> 
											</thead>
											<tbody class="referredclienttdata">
												<?php
													foreach(\App\Admin::where('agent_id', $fetchedData->id)->orderby('created_at','Desc')->get() as $reflist){
													//$productdetail = \App\Product::where('id', $alist->product_id)->first();
													//$partnerdetail = \App\Partner::where('id', $alist->partner_id)->first();
													//$PartnerBranch = \App\PartnerBranch::where('id', $alist->branch)->first();
													//$workflow = \App\Workflow::where('id', $alist->workflow)->first();
												?>
												<tr id="id_{{$reflist->id}}">
													<td>{{$reflist->first_name}} {{$reflist->last_name}}</td>
													<td>-</td>
													<td>{{$reflist->city}}<br/>{{$reflist->country}}</td>
													<td><span class="ag-label--circular" style="color: #6777ef" >In Progress</span></td>
													<td>{{$reflist->assignee}}</td>
													<td>{{date('Y-m-d', strtotime($reflist->created_at))}}</td>
												</tr>
												<?php
													} 
												?>
											</tbody>
											<tbody>
												<tr>
													<td style="text-align:center;" colspan="10">
														No Record found
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="tab-pane fade <?php if(isset($_GET['tab']) && $_GET['tab'] == 'application'){ echo 'show active'; } ?>" id="application" role="tabpanel" aria-labelledby="application-tab">
									<div class="card-header-action text-right if_applicationdetail" style="padding-bottom:15px;">
										
									</div>									
									<div class="table-responsive if_applicationdetail"> 
										<table class="table text_wrap table-2">
											<thead>
												<tr>
													<th>Contact</th>
													<th>Name</th>
													<th>Current Stage</th>
													<th>Status</th>
													
													<th>Started</th>
													<th>Last Updated</th>
													
												</tr> 
											</thead>
											<tbody class="applicationtdata">
											<?php
											foreach(\App\Application::where('sub_agent', $fetchedData->id)->orwhere('super_agent', $fetchedData->id)->orderby('created_at','Desc')->get() as $alist){
												$productdetail = \App\Product::where('id', $alist->product_id)->first();
				$partnerdetail = \App\Partner::where('id', $alist->partner_id)->first();
				$PartnerBranch = \App\PartnerBranch::where('id', $alist->branch)->first();
				$workflow = \App\Workflow::where('id', $alist->workflow)->first();
				$clientdetail = \App\Admin::where('id', $alist->client_id)->first();
												?>
												<tr id="id_{{$alist->id}}">
													<td><a href="{{URL::to('admin/clients/detail/')}}/{{base64_encode(convert_uuencode(@$clientdetail->id))}}?tab=application">{{@$clientdetail->first_name}} {{@$clientdetail->last_name}}</a><br/>{{@$clientdetail->email}}</td> 
													<td><a href="{{URL::to('admin/clients/detail/')}}/{{base64_encode(convert_uuencode(@$clientdetail->id))}}?tab=application">{{$productdetail->name}}</a> <br><small>{{$partnerdetail->partner_name}} ({{$PartnerBranch->name}})</small></td> 
													<td>{{$alist->stage}}<br>{{$workflow->name}}</td>
												
													<td>
													@if($alist->status == 0)
													<span class="ag-label--circular" style="color: #6777ef" >In Progress</span>
													@elseif($alist->status == 1)
														<span class="ag-label--circular" style="color: #6777ef" >Completed</span>
													@endif
												</td> 
												
													<td>{{date('Y-m-d', strtotime($alist->created_at))}}</td> 
													<td>{{date('Y-m-d', strtotime($alist->updated_at))}}</td> 
													
												</tr>
												<?php
											} 
											?>											
												
											</tbody>
											<!--<tbody>
												<tr>
													<td style="text-align:center;" colspan="10">
														No Record found
													</td>
												</tr>
											</tbody>-->
										</table> 
									</div>
									
								</div>
								<div class="tab-pane fade" id="accounts" role="tabpanel" aria-labelledby="accounts-tab">
									<div class="row">
										<div class="col-md-12">
											<ul class="nav nav-pills" id="agents_tabs" role="tablist">
												<li class="nav-item">
													<a class="nav-link active" data-toggle="tab" id="incomesharingterm-tab" href="#incomesharing" role="tab" aria-controls="incomesharingterm" aria-selected="true">Income Sharing</a>
												</li>
												<li class="nav-item">
													<a class="nav-link" data-toggle="tab" id="invoices-tab" href="#invoices" role="tab" aria-controls="invoices" aria-selected="false">Invoices</a>
												</li>
											</ul> 
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="tab-content" id="agentinvoiceContent" style="padding-top:15px;">
										<div class="tab-pane fade show active" id="incomesharing" role="tabpanel" aria-labelledby="incomesharing-tab">
											<div class="table-responsive"> 
												<table class="table invoicetable text_wrap">
													<thead>
														<tr>
															<th>Invoice No.</th>
															<th>Associated Office</th>
															<th>Client</th>
															<th>Application</th>
															<th>Amount</th>
															<th>Tax Amount</th>
															<th>Paid Date</th>
															<th>Paid By</th>
															<th>Status</th>
														</tr> 
													</thead>
													
												</table>
											</div>
										</div>
										<div class="tab-pane fade show active" id="invoices" role="tabpanel" aria-labelledby="invoices-tab">
											<table class="table invoicetable text_wrap">
													<thead>
														<tr>
															<th>Invoice No.</th>
															<th>Issue Date</th>
															<th>Invoice Type</th>
															<th>Client Name</th>
															<th>Partner Name</th>
															<th>Product Name</th>
															<th>Workflow</th>
															<th>Invoice Amount</th>
															<th>Amount Due</th>
															<th>Status</th>
															<th>Actions</th>
														</tr> 
													</thead>
													
												</table>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="represent_partner" role="tabpanel" aria-labelledby="represent_partner-tab">
									<div class="card-header-action text-right if_applicationdetail" style="padding-bottom:15px;">
										<a href="javascript:;" data-toggle="modal" data-target=".add_represent_partner" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
									</div>	
									<div class="table-responsive if_partnerdetail"> 
										<table class="table text_wrap table-2">
											<thead>
												<tr>
													<th>Partner Name</th>
													<th>Email</th>
													<th>Branch</th>
													<th>Workflow</th>
													<th>Action</th>
												</tr> 
											</thead>
											<tbody class="partnerdata">
												<?php 
													$representpartnerlists = \App\RepresentingPartner::where('agent_id', $fetchedData->id)->orderby('created_at', 'DESC')->with(['partners'])->get();
													foreach($representpartnerlists as $partnertlist){
													$PartnerBranch = \App\PartnerBranch::select('name')->where('partner_id', $partnertlist->partner_id)->get();
													$branch = '';
													foreach($PartnerBranch as $pb){
														$branch .= $pb->name.',';
													}
												?> 
												<tr id="id_{{$partnertlist->id}}">
													<td><?php echo $partnertlist->partners->partner_name; ?></td>
													<td><?php echo $partnertlist->partners->email; ?></td>
													<td><?php echo rtrim($branch, ','); ?></td>  
													<td><?php echo $partnertlist->partners->workflow->name; ?></td>
													<td><a onclick="deleteAction({{$partnertlist->id}}, 'representing_partners')" href="javascript:;"><i class="fa fa-link"></i></a></td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>	
								</div>	
							</div> 
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div> 

@include('Admin/agents/addagentmodal')  
@include('Admin/agents/editagentmodal')  

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
				<form method="post" name="sendmail" action="{{URL::to('/admin/sendmail')}}" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="type" value="agent">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">From <span class="span_req">*</span></label>
								<select class="form-control" name="email_from">
									<?php
									$emails = \App\Email::select('email')->where('status', 1)->get();
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


<div class="modal fade  custom_modal " id="interest_service_view" tabindex="-1" role="dialog" aria-labelledby="interest_serviceModalLabel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content showinterestedservice">
			
		</div>
	</div>
</div>

<div id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to delete this note?</h4> 
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Delete</button> 
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmEducationModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to delete this note?</h4> 
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accepteducation">Delete</button> 
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>
<div id="confirmcompleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to complete the Application?</h4> 
				<button  data-id="" type="submit" style="margin-top: 40px;" class="button btn btn-danger acceptapplication">Complete</button> 
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script>
jQuery(document).ready(function($){
	$(document).delegate('.opentaskview', 'click', function(){
		$('#opentaskview').modal('show');
		var v = $(this).attr('id');
		$.ajax({
			url: site_url+'/admin/get-task-detail',
			type:'GET',
			data:{task_id:v},
			success: function(responses){
				
				$('.taskview').html(responses);
			}
		});
	});
	 function getallnotes(){
		$.ajax({
			url: site_url+'/admin/get-notes',
			type:'GET',
			data:{clientid:'{{$fetchedData->id}}',type:'client'},
			success: function(responses){
				
				$('.note_term_list').html(responses);
			}
		});
	} 

	var notid = '';
	var delhref = '';
	$(document).delegate('.deletenote', 'click', function(){
		$('#confirmModal').modal('show');
		notid = $(this).attr('data-id');
		delhref = $(this).attr('data-href');
	});
	$(document).delegate('#confirmModal .accept', 'click', function(){
	
		$('.popuploader').show(); 
		$.ajax({
			url: '{{URL::to('/admin/')}}/'+delhref,
			type:'GET',
			datatype:'json',
			data:{note_id:notid},
			success:function(response){
				$('.popuploader').hide(); 
				var res = JSON.parse(response);
				$('#confirmModal').modal('hide');
				if(res.status){
					$('#note_id_'+notid).remove();
					if(delhref == 'deletedocs'){
						$('.documnetlist #id_'+notid).remove();
					} 
					if(delhref == 'deleteservices'){
						$.ajax({
						url: site_url+'/admin/get-services', 
						type:'GET',
						data:{clientid:'{{$fetchedData->id}}'},
						success: function(responses){
							
							$('.interest_serv_list').html(responses);
						}
					});
					}if(delhref == 'deleteappointment'){
						$.ajax({
						url: site_url+'/admin/get-appointments',
						type:'GET',
						data:{clientid:'{{$fetchedData->id}}'},
						success: function(responses){
							
							$('.appointmentlist').html(responses);
						}
					});
					}else{
						getallnotes();
					}
					
					getallactivities();
				}
			}
		});
	}); 
	
	
	$(document).delegate('.create_note', 'click', function(){
		$('#create_note').modal('show');
		$('#create_note input[name="mailid"]').val(0);
		$('#create_note input[name="title"]').val('');
		$('#create_note #appliationModalLabel').html('Create Note');
		$('#create_note input[name="title"]').val('');
					$("#create_note .summernote-simple").val('');
					$('#create_note input[name="noteid"]').val('');                    
				$("#create_note .summernote-simple").summernote('code','');
		if($(this).attr('datatype') == 'note'){
			$('.is_not_note').hide();
		}else{ 
		var datasubject = $(this).attr('datasubject');
		var datamailid = $(this).attr('datamailid');
			$('#create_note input[name="title"]').val(datasubject);
			$('#create_note input[name="mailid"]').val(datamailid);
			$('.is_not_note').show();
		}
	});
	
	$(document).delegate('.opentaskmodal', 'click', function(){
		$('#opentaskmodal').modal('show');
		$('#opentaskmodal input[name="mailid"]').val(0);
		$('#opentaskmodal input[name="title"]').val('');
		$('#opentaskmodal #appliationModalLabel').html('Create Note');
		$('#opentaskmodal input[name="attachments"]').val('');
		$('#opentaskmodal input[name="title"]').val('');
		$('#opentaskmodal .showattachment').val('Choose file');
              
		var datasubject = $(this).attr('datasubject');
		var datamailid = $(this).attr('datamailid');
			$('#opentaskmodal input[name="title"]').val(datasubject);
			$('#opentaskmodal input[name="mailid"]').val(datamailid);
	});
	$('.js-data-example-ajaxcc').select2({
		 multiple: true,
		 closeOnSelect: false,
		dropdownParent: $('#create_note'),
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
$('.js-data-example-ajaxcontact').select2({
		 multiple: true,
		 closeOnSelect: false,
		dropdownParent: $('#opentaskmodal'),
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
	$(document).delegate('.opennoteform', 'click', function(){
		$('#create_note').modal('show');
		$('#create_note #appliationModalLabel').html('Edit Note');
		var v = $(this).attr('data-id');
		$('#create_note input[name="noteid"]').val(v);
			$('.popuploader').show(); 
		$.ajax({
			url: '{{URL::to('/admin/getnotedetail')}}',
			type:'GET',
			datatype:'json',
			data:{note_id:v},
			success:function(response){
				$('.popuploader').hide(); 
				var res = JSON.parse(response);
				
				if(res.status){
					$('#create_note input[name="title"]').val(res.data.title);
					$("#create_note .summernote-simple").val(res.data.description);                    
				$("#create_note .summernote-simple").summernote('code',res.data.description);
				}
			}
		});
	});
	$(document).delegate('.viewnote', 'click', function(){
		$('#view_note').modal('show');
		var v = $(this).attr('data-id');
		$('#view_note input[name="noteid"]').val(v);
			$('.popuploader').show(); 
		$.ajax({
			url: '{{URL::to('/admin/viewnotedetail')}}',
			type:'GET',
			datatype:'json',
			data:{note_id:v},
			success:function(response){
				$('.popuploader').hide(); 
				var res = JSON.parse(response);
				
				if(res.status){
					$('#view_note .modal-body .note_content h5').html(res.data.title);
					$("#view_note .modal-body .note_content p").html(res.data.description);                    
					
				} 
			}
		});
	});
	$(document).delegate('.add_appliation #workflow', 'change', function(){
	
				var v = $('.add_appliation #workflow option:selected').val();
				if(v != ''){
						$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getpartnerbranch')}}',
			type:'GET',
			data:{cat_id:v},
			success:function(response){
				$('.popuploader').hide();
				$('.add_appliation #partner').html(response);
				
				$(".add_appliation #partner").val('').trigger('change');
			$(".add_appliation #product").val('').trigger('change');
			$(".add_appliation #branch").val('').trigger('change');
			}
		});
				}
	});
	
	$(document).delegate('.add_appliation #partner','change', function(){
		
				var v = $('.add_appliation #partner option:selected').val();
				var explode = v.split('_');
				if(v != ''){
					$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getbranchproduct')}}',
			type:'GET',
			data:{cat_id:explode[0]},
			success:function(response){
				$('.popuploader').hide();
				$('.add_appliation #product').html(response);
				$(".add_appliation #product").val('').trigger('change');
			
			}
		});
				}
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
 $(document).delegate('.change_client_status', 'click', function(e){
	
	var v = $(this).attr('rating');
	$('.change_client_status').removeClass('active');
	$(this).addClass('active');
	
	 $.ajax({
		url: '{{URL::to('/admin/change-client-status')}}',
		type:'GET',
		datatype:'json',
		data:{id:'{{$fetchedData->id}}',rating:v},
		success: function(response){
			var res = JSON.parse(response);
			if(res.status){
				
				$('.custom-error-msg').html('<span class="alert alert-success">'+res.message+'</span>');
				getallactivities();
			}else{
				$('.custom-error-msg').html('<span class="alert alert-danger">'+response.message+'</span>');
			}
			
		}
	}); 
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
			 $("#emailmodal .summernote-simple").summernote('reset');  
                    $("#emailmodal .summernote-simple").summernote('code', res.description);  
					$("#emailmodal .summernote-simple").val(res.description); 
			
		}
	});
});

$(document).delegate('.selectapplicationtemplate', 'change', function(){
	var v = $(this).val();
	$.ajax({
		url: '{{URL::to('/admin/get-templates')}}',
		type:'GET',
		datatype:'json',
		data:{id:v},
		success: function(response){
			var res = JSON.parse(response);
			$('.selectedappsubject').val(res.subject);
			 $("#applicationemailmodal .summernote-simple").summernote('reset');  
                    $("#applicationemailmodal .summernote-simple").summernote('code', res.description);  
					$("#applicationemailmodal .summernote-simple").val(res.description); 
			
		}
	});
});
	$('.js-data-example-ajax').select2({
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

$('.js-data-example-ajaxcc').select2({
		 multiple: true,
		 closeOnSelect: false,
		dropdownParent: $('#create_note'),
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

/* $(".table-2").dataTable({
	"searching": false,
	"lengthChange": false,
  "columnDefs": [
    { "sortable": false, "targets": [0, 2, 3] }
  ],
  order: [[1, "desc"]] //column indexes is zero based

}); */

$(".invoicetable").dataTable({
	"searching": false,
	"lengthChange": false,
  "columnDefs": [
    { "sortable": false, "targets": [0, 2, 3] }
  ],
  order: [[1, "desc"]] //column indexes is zero based

});


$(document).delegate('#intrested_workflow', 'change', function(){
	
				var v = $('#intrested_workflow option:selected').val();
				
				if(v != ''){
						$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getpartner')}}',
			type:'GET',
			data:{cat_id:v},
			success:function(response){
				$('.popuploader').hide();
				$('#intrested_partner').html(response);
				
				$("#intrested_partner").val('').trigger('change');
			$("#intrested_product").val('').trigger('change');
			$("#intrested_branch").val('').trigger('change');
			}
		});
				}
	});
	
	$(document).delegate('#intrested_partner','change', function(){
		
				var v = $('#intrested_partner option:selected').val();
				if(v != ''){
					$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getproduct')}}',
			type:'GET',
			data:{cat_id:v},
			success:function(response){
				$('.popuploader').hide();
				$('#intrested_product').html(response);
				$("#intrested_product").val('').trigger('change');
			$("#intrested_branch").val('').trigger('change');
			}
		});
				}
	});
	
	
	
		<?php
      // $json = json_encode ( $appointmentdata, JSON_FORCE_OBJECT );
   ?>
$(document).delegate('.appointmentdata', 'click', function () {
	var v = $(this).attr('data-id');
$('.appointmentdata').removeClass('active');
$(this).addClass('active');
	var res = $.parseJSON('<?php echo @$json; ?>');
	
	$('.appointmentname').html(res[v].title);
	 $('.appointmenttime').html(res[v].time);
	$('.appointmentdate').html(res[v].date);
	$('.appointmentdescription').html(res[v].description);
	$('.appointmentcreatedby').html(res[v].createdby);
	$('.appointmentcreatedname').html(res[v].createdname);
	$('.appointmentcreatedemail').html(res[v].createdemail); 
	$('.editappointment .edit_link').attr('data-id', v); 
});	

$(document).delegate('.opencreate_task', 'click', function () {
	$('#tasktermform')[0].reset();
	$('#tasktermform select').val('').trigger('change');
	$('.create_task').modal('show');
	$('.ifselecttask').hide();
	$('.ifselecttask select').attr('data-valid', '');
	
});
	 var eduid = '';
    $(document).delegate('.deleteeducation', 'click', function(){
		eduid = $(this).attr('data-id');
		$('#confirmEducationModal').modal('show');
		
	});
	
	$(document).delegate('#confirmEducationModal .accepteducation', 'click', function(){
	
		$('.popuploader').show(); 
		$.ajax({
			url: '{{URL::to('/admin/')}}/delete-education',
			type:'GET',
			datatype:'json',
			data:{edu_id:eduid},
			success:function(response){
				$('.popuploader').hide(); 
				var res = JSON.parse(response);
				$('#confirmEducationModal').modal('hide');
				if(res.status){
					$('#edu_id_'+eduid).remove();
				}else{
					alert('Please try again')
				}
			}
		});
	});
    $(document).delegate('#educationform #subjectlist', 'change', function(){
	
				var v = $('#educationform #subjectlist option:selected').val();
				if(v != ''){
						$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getsubjects')}}',
			type:'GET',
			data:{cat_id:v},
			success:function(response){
				$('.popuploader').hide();
				$('#educationform #subject').html(response);
				
				$(".add_appliation #subject").val('').trigger('change');
			
			}
		});
				}
	});
	
	$(document).delegate('.edit_appointment', 'click', function(){
		var v = $(this).attr('data-id');
		$('.popuploader').show();
		$('#edit_appointment').modal('show');
		$.ajax({
			url: '{{URL::to('/admin/getAppointmentdetail')}}',
			type:'GET',
			data:{id:v},
			success:function(response){
				$('.popuploader').hide();
				$('.showappointmentdetail').html(response);
				 $(".datepicker").daterangepicker({
        locale: { format: "YYYY-MM-DD" },
        singleDatePicker: true,
        showDropdowns: true
      });
				$(".timepicker").timepicker({
      icons: {
        up: "fas fa-chevron-up",
        down: "fas fa-chevron-down"
      }
    });
			}
		});
	});
	
	$(document).delegate('.editeducation', 'click', function(){
		var v = $(this).attr('data-id');
		$('.popuploader').show();
		$('#edit_education').modal('show');
		$.ajax({
			url: '{{URL::to('/admin/getEducationdetail')}}',
			type:'GET',
			data:{id:v},
			success:function(response){
				$('.popuploader').hide();
				$('.showeducationdetail').html(response);
				 $(".datepicker").daterangepicker({
					locale: { format: "YYYY-MM-DD" },
					singleDatePicker: true,
					showDropdowns: true
				  });
			
			}
		});
	});
	
	$(document).delegate('.interest_service_view', 'click', function(){
		var v = $(this).attr('data-id');
		$('.popuploader').show();
		$('#interest_service_view').modal('show');
		$.ajax({
			url: '{{URL::to('/admin/getintrestedservice')}}',
			type:'GET',
			data:{id:v},
			success:function(response){
				$('.popuploader').hide();
				$('.showinterestedservice').html(response);
			}
		});
	});
	
	
	$(document).delegate('.openeditservices', 'click', function(){
		var v = $(this).attr('data-id');
		$('.popuploader').show();
		$('#interest_service_view').modal('hide');
		$('#eidt_interested_service').modal('show');
		$.ajax({
			url: '{{URL::to('/admin/getintrestedserviceedit')}}',
			type:'GET',
			data:{id:v},
			success:function(response){
				$('.popuploader').hide();
				$('.showinterestedserviceedit').html(response);
				
				 $(".datepicker").daterangepicker({
					locale: { format: "YYYY-MM-DD" },
					singleDatePicker: true,
					showDropdowns: true
				  });
			}
		});
	});
	
	$(document).delegate('.opencommissioninvoice', 'click', function(){
		$('#opencommissionmodal').modal('show');
	});
	
	$(document).delegate('.opengeneralinvoice', 'click', function(){
		$('#opengeneralinvoice').modal('show');
	});
	 

$(document).delegate('.addpaymentmodal','click', function(){
		var v = $(this).attr('data-invoiceid');
		var netamount = $(this).attr('data-netamount');
		var dueamount = $(this).attr('data-dueamount');
		$('#invoice_id').val(v);
		$('.invoicenetamount').html(netamount+' AUD');
		$('.totldueamount').html(dueamount);
		$('.totldueamount').attr('data-totaldue', dueamount);
		$('#addpaymentmodal').modal('show');
		$('.payment_field_clone').remove();
		$('.paymentAmount').val('');
	});	

$(document).delegate('.paymentAmount','keyup', function(){
		grandtotal();	
			
		});
		function grandtotal(){
			var p =0;
			$('.paymentAmount').each(function(){
				if($(this).val() != ''){
					p += parseFloat($(this).val());
				}
			});
		
			var tamount = $('.totldueamount').attr('data-totaldue');
			
			var am = parseFloat(tamount) - parseFloat(p);
			$('.totldueamount').html(am.toFixed(2));
		}
	$('.add_payment_field a').on('click', function(){
		var clonedval = $('.payment_field .payment_field_row .payment_first_step').html();
		$('.payment_field .payment_field_row').append('<div class="payment_field_col payment_field_clone">'+clonedval+'</div>');
	}); 
	$('.add_fee_type a.fee_type_btn').on('click', function(){ 
		var clonedval = $('.fees_type_sec .fee_type_row .fees_type_col').html();
		$('.fees_type_sec .fee_type_row').append('<div class="custom_type_col fees_type_clone">'+clonedval+'</div>');
	});
	$(document).delegate('.payment_field_col .field_remove_col a.remove_col', 'click', function(){ 
		var $tr    = $(this).closest('.payment_field_clone');
		var trclone = $('.payment_field_clone').length;		
		if(trclone > 0){
			$tr.remove();
			grandtotal();
		} 
	});
	$(document).delegate('.fees_type_sec .fee_type_row .fees_type_clone a.remove_btn', 'click', function(){ 
		var $tr    = $(this).closest('.fees_type_clone');
		var trclone = $('.fees_type_clone').length;		
		if(trclone > 0){
			$tr.remove();
			grandtotal();
		} 
	});	
	
	<?php
	if(isset($_GET['tab']) && $_GET['tab'] == 'application'){
		?>
		var appliid = '{{@$_GET['appid']}}';
		$('.if_applicationdetail').hide();
		$('.ifapplicationdetailnot').show();
		$.ajax({
			url: '{{URL::to('/admin/getapplicationdetail')}}',
			type:'GET',
			data:{id:appliid},
			success:function(response){
				$('.popuploader').hide();
				$('.ifapplicationdetailnot').html(response);
				$('.datepicker').daterangepicker({
				locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },
								singleDatePicker: true,
								
								showDropdowns: true,
				}, function(start, end, label) {
					$.ajax({
						url:"{{URL::to('/admin/application/updateintake')}}",
						method: "GET", // or POST
						dataType: "json",
						data: {from: start.format('YYYY-MM-DD'), appid: appliid},
						success:function(result) {
							console.log("sent back -> do whatever you want now");
						}
					});
				});
				

			}
		});
		<?php
	}
	?>
$(document).delegate('.discon_application', 'click', function(){
	var appliid = $(this).attr('data-id');
	$('#discon_application').modal('show');
	$('input[name="diapp_id"]').val(appliid);
});

$(document).delegate('.revertapp', 'click', function(){
	var appliid = $(this).attr('data-id');
	$('#revert_application').modal('show');
	$('input[name="revapp_id"]').val(appliid);
});
$(document).delegate('.completestage', 'click', function(){
	var appliid = $(this).attr('data-id');
	$('#confirmcompleteModal').modal('show');
	$('.acceptapplication').attr('data-id',appliid)

});
$(document).delegate('.openapplicationdetail', 'click', function(){
		var appliid = $(this).attr('data-id');
		$('.if_applicationdetail').hide();
		$('.ifapplicationdetailnot').show();
		$.ajax({
			url: '{{URL::to('/admin/getapplicationdetail')}}',
			type:'GET',
			data:{id:appliid},
			success:function(response){
				$('.popuploader').hide();
				$('.ifapplicationdetailnot').html(response);
				$('.datepicker').daterangepicker({
				locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },
								singleDatePicker: true,
								
								showDropdowns: true,
				}, function(start, end, label) {
					$.ajax({
						url:"{{URL::to('/admin/application/updateintake')}}",
						method: "GET", // or POST
						dataType: "json",
						data: {from: start.format('YYYY-MM-DD'), appid: appliid},
						success:function(result) {
							console.log("sent back -> do whatever you want now");
						}
					});
				});
				

			}
		});
	});
	
	$(document).delegate('#application-tab', 'click', function(){
		
		$('.if_applicationdetail').show();
		$('.ifapplicationdetailnot').hide();
		$('.ifapplicationdetailnot').html('<h4>Please wait ...</h4>');
	});
	
$(document).delegate('.openappnote', 'click', function(){
	var apptype = $(this).attr('data-app-type');
	var id = $(this).attr('data-id');
	$('#create_applicationnote #noteid').val(id);
	$('#create_applicationnote #type').val(apptype);
	$('#create_applicationnote').modal('show');
}); 
$(document).delegate('.openappappoint', 'click', function(){
	var id = $(this).attr('data-id');
	var apptype = $(this).attr('data-app-type');
	$('#create_applicationappoint #type').val(apptype);
	$('#create_applicationappoint #appointid').val(id);
	$('#create_applicationappoint').modal('show');
});

$(document).delegate('.openclientemail', 'click', function(){
	var id = $(this).attr('data-id');
	var apptype = $(this).attr('data-app-type');
	$('#applicationemailmodal #type').val(apptype);
	$('#applicationemailmodal #appointid').val(id);
	$('#applicationemailmodal').modal('show');
});

$(document).delegate('.openchecklist', 'click', function(){
	var id = $(this).attr('data-id'); 
	$('#create_checklist #checklistid').val(id);
	$('#create_checklist').modal('show');
});
$(document).delegate('.openpaymentschedule', 'click', function(){
	$('#create_paymentschedule').modal('show');
});

$(document).delegate('.createaddapointment', 'click', function(){
	$('#create_appoint').modal('show');
});
$(document).delegate('.due_date_sec a.due_date_btn', 'click', function(){
	$('.due_date_sec .due_date_col').show();
	$(this).hide();
});
$(document).delegate('.remove_col a.remove_btn', 'click', function(){
	$('.due_date_sec .due_date_col').hide();
	$('.due_date_sec a.due_date_btn').show();  
});
	
$(document).delegate('#notes-tab', 'click', function(){
		var appliid = $(this).attr('data-id');
		$('.if_applicationdetail').hide();
		$('.ifapplicationdetailnot').show();
		$.ajax({
			url: '{{URL::to('/admin/getapplicationnotes')}}',
			type:'GET',
			data:{id:appliid},
			success:function(response){
				$('.popuploader').hide();
				$('#notes').html(response);
				
			}
		});
	});
	
	 $(".timezoneselect2").select2({
    dropdownParent: $("#create_appoint .modal-content")
  });
  
  $('#attachments').on('change',function(){
       // output raw value of file input
      $('.showattachment').html(''); 

        // or, manipulate it further with regex etc.
        var filename = $(this).val().replace(/.*(\/|\\)/, '');
        // .. do your magic

       $('.showattachment').html(filename);
    });
});
</script>
@endsection