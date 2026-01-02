@extends('layouts.agent')
@section('title', 'Client Detail')

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
							<h4>Client Detail</h4>
							<div class="card-header-action">
								<a href="{{route('clients.index')}}" class="btn btn-primary">Client List</a>
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
							<span class="author-avtar" style="background: rgb(68, 182, 174);"><b>{{substr($fetchedData->first_name, 0, 1)}}{{substr($fetchedData->last_name, 0, 1)}}</b></span>
								<div class="clearfix"></div>
								<div class="author-box-name">
									<a href="#">{{$fetchedData->first_name}} {{$fetchedData->last_name}}</a>
								</div>
								<div class="author-rating">
									<a href="javascript:;"  class=" lost <?php if($fetchedData->rating == 'Lost'){ echo 'active'; } ?>" style=""><i class="fas fa-exclamation-triangle"></i> Lost</a>
									<a href="javascript:;"  class=" cold <?php if($fetchedData->rating == 'Cold'){ echo 'active'; } ?>" style=""><i class="fas fa-snowflake"></i> Cold</a>
									<a href="javascript:;" class=" warm <?php if($fetchedData->rating == 'Warm'){ echo 'active'; } ?>" style=""><i class="fas fa-mug-hot" ></i> Warm</a>
									<a href="javascript:;" rating="Hot" class=" hot <?php if($fetchedData->rating == 'Hot'){ echo 'active'; } ?>" style=""><i class="fas fa-fire"></i> Hot</a>
								</div>
								<div class="author-mail_sms">
									
									
									<a href="{{URL::to('/clients/edit/'.base64_encode(convert_uuencode(@$fetchedData->id)))}}" title="Edit"><i class="fa fa-edit"></i></a>
									
								</div>
							
							</div>
							<?php
	// PostgreSQL doesn't accept empty strings for integer columns - check before querying
	$agent = null;
	if(!empty(@$fetchedData->agent_id) && @$fetchedData->agent_id !== '') {
		$agent = \App\Models\Agent::where('id', @$fetchedData->agent_id)->first();
	}
	if($agent){
		?>
		<div class="client_assign client_info_tags"> 
								<span class=""><b>Agent:</b></span>
								@if($agent)
								<div class="client_info">
									<div class="cl_logo">{{substr(@$agent->full_name, 0, 1)}}</div>
									<div class="cl_name">
										<span class="name">{{@$agent->full_name}}</span>
										<span class="email">{{@$agent->email}}</span>
									</div>
								</div>
								@else
									-
								@endif
							</div>
		<?php
	}
?>

						</div>
					</div>
					<div class="card">
						<div class="card-header">
							<h4>Personal Details</h4>
						</div>
						<div class="card-body">
							<p class="clearfix"> 
								<span class="float-start">Tag(s):</span>
								<span class="float-end text-muted">
								
									
									
								</span>
							</p>
							<p>
							<?php $tags = ''; 
							if($fetchedData->tagname != ''){
								$rs = explode(',', $fetchedData->tagname);
								foreach($rs as $r){
									$stagd = \App\Models\Tag::where('id','=',$r)->first();
									if($stagd){
									?>
										<span class="ui label ag-flex ag-align-center ag-space-between" style="display: inline-flex;">
											<span class="col-hr-1" style="font-size: 12px;">{{@$stagd->name}}</span> 
										</span>
									<?php
									}
								}								
							} 
							?>
							
							</p>
							
						
							<p class="clearfix"> 
								<span class="float-start">Client Id:</span>
								<span class="float-end text-muted">{{$fetchedData->client_id}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-start">Internal Id:</span>
								<span class="float-end text-muted">{{$fetchedData->id}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-start">Date Of Birth:</span>
								<span class="float-end text-muted">{{$fetchedData->dob}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-start">Phone No:</span>
								<span class="float-end text-muted">{{$fetchedData->phone}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-start">Email:</span>
								<span class="float-end text-muted">{{$fetchedData->email}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-start">Secondary Email:</span>
								<span class="float-end text-muted">-</span>
							</p>
							<p class="clearfix"> 
								<span class="float-start">Address:</span>
								<span class="float-end text-muted">{{$fetchedData->address}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-start">Country of Passport:</span>
								<span class="float-end text-muted">{{$fetchedData->country_passport}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-start">Passport Number:</span>
								<span class="float-end text-muted">{{$fetchedData->passport_number}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-start">Preferred Intake:</span>
								<span class="float-end text-muted"><?php if($fetchedData->preferredIntake != ''){ ?>{{date('M Y', strtotime($fetchedData->preferredIntake))}}<?php } ?></span>
							</p>
							<p class="clearfix"> 
								<span class="float-start">Visa Expiry Date:</span>
								<span class="float-end text-muted">{{$fetchedData->visaExpiry}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-start">Visa type:</span>
								<span class="float-end text-muted">{{$fetchedData->visa_type}}</span>
							</p> 
							<?php
								// PostgreSQL doesn't accept empty strings for integer columns - check before querying
								$addedby = null;
								if(!empty(@$fetchedData->user_id) && @$fetchedData->user_id !== '') {
									$addedby = \App\Models\Admin::where('id', @$fetchedData->user_id)->first();
								}
							?>
							<div class="client_added client_info_tags"> 
								<span class="">Added By:</span>
								@if($addedby)
								<div class="client_info">
									<div class="cl_logo">{{substr(@$addedby->first_name, 0, 1)}}</div>
									<div class="cl_name">
										<span class="name">{{@$addedby->first_name}}</span>
										<span class="email">{{@$addedby->email}}</span>
									</div>
								</div>
								@else
									-
								@endif
							</div>
							<?php
								// PostgreSQL doesn't accept empty strings for integer columns - check before querying
								$assignee = null;
								if(!empty(@$fetchedData->assignee) && @$fetchedData->assignee !== '') {
									$assignee = \App\Models\Admin::where('id', @$fetchedData->assignee)->first();
								}
							?>
							<div class="client_assign client_info_tags"> 
								<span class="">Assignee:</span>
								@if($assignee)
								<div class="client_info">
									<div class="cl_logo">{{substr(@$assignee->first_name, 0, 1)}}</div>
									<div class="cl_name">
										<span class="name">{{@$assignee->first_name}}</span>
										<span class="email">{{@$assignee->email}}</span>
									</div>
								</div>
								@else
									-
								@endif
							</div>
								
							<div class="client_assign client_info_tags"> 
								<span class="">Related Files:</span>
								
								<div class="client_info">
								    <ul>
								    <?php   
								        $relatedclientss = \App\Models\Admin::whereRaw('? = ANY(string_to_array(related_files, \',\'))', [$fetchedData->id])->get();	
								        foreach($relatedclientss AS $res){ 
									?>
									    <li><a target="_blank" href="{{URL::to('/clients/detail/'.base64_encode(convert_uuencode(@$res->id)))}}">{{$res->first_name}} {{$res->last_name}}</a></li>
									<?php } ?>
									<?php
								if($fetchedData->related_files != ''){
								    $exploder = explode(',', $fetchedData->related_files);
								 
							
							?>
									<?php   
									if(!empty($exploder)) {
										foreach($exploder AS $EXP){ 
											// PostgreSQL doesn't accept empty strings for integer columns - filter empty values
											if(!empty(trim($EXP)) && trim($EXP) !== '') {
												$relatedclients = \App\Models\Admin::where('id', trim($EXP))->first();	
												if($relatedclients) {
									?>
													<li><a target="_blank" href="{{URL::to('/clients/detail/'.base64_encode(convert_uuencode(@$relatedclients->id)))}}">{{$relatedclients->first_name}} {{$relatedclients->last_name}}</a></li>
									<?php 
												}
											}
										} 
									}
									?>
									<?php } ?>	
									</ul>
								</div>
								
							</div>
								
						</div>
					</div>
				</div>
				<div class="col-9 col-md-9 col-lg-9">
					<div class="card">
						<div class="card-body">
							<ul class="nav nav-pills" id="client_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link <?php if(isset($_GET['tab']) && $_GET['tab'] == 'application'){ echo 'active'; }else{ echo 'active'; } ?>" data-bs-toggle="tab" id="application-tab" href="#application" role="tab" aria-controls="application" aria-selected="false">Applications</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-bs-toggle="tab" id="interested_service-tab" href="#interested_service" role="tab" aria-controls="interested_service" aria-selected="false">Interested Services</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-bs-toggle="tab" id="documents-tab" href="#documents" role="tab" aria-controls="documents" aria-selected="false">Documents</a>
								</li>
								<li class="nav-item">
									{{-- <a class="nav-link" data-bs-toggle="tab" id="appointments-tab" href="#appointments" role="tab" aria-controls="appointments" aria-selected="false">Appointments</a> --}}
								</li>
								<li class="nav-item">
									<a class="nav-link" data-bs-toggle="tab" id="noteterm-tab" href="#noteterm" role="tab" aria-controls="noteterm" aria-selected="false">Notes & Terms</a>
								</li>
								
							
							
							</ul> 
							<div class="tab-content" id="clientContent" style="padding-top:15px;">
								
								<div class="tab-pane fade <?php if(isset($_GET['tab']) && $_GET['tab'] == 'application'){ echo 'show active'; }else{ echo 'show active'; } ?>" id="application" role="tabpanel" aria-labelledby="application-tab">
									<div class="card-header-action text-end if_applicationdetail" style="padding-bottom:15px;">
									
									</div>									
									<div class="table-responsive if_applicationdetail"> 
										<table class="table text_wrap table-2">
											<thead>
												<tr>
													<th>Name</th>
													<th>Workflow</th>
													<th>Current Stage</th>
													<th>Status</th>
													<th>Sales Forecast</th>
													<th>Started</th>
													<th>Last Updated</th>
													<th></th>
												</tr> 
											</thead>
											<tbody class="applicationtdata">
											<?php
											foreach(\App\Models\Application::where('client_id', $fetchedData->id)->orderby('created_at','Desc')->get() as $alist){
												$productdetail = \App\Models\Product::where('id', $alist->product_id)->first();
												$partnerdetail = \App\Models\Partner::where('id', $alist->partner_id)->first();
												$PartnerBranch = \App\Models\PartnerBranch::where('id', $alist->branch)->first();
												$workflow = \App\Models\Workflow::where('id', $alist->workflow)->first();
												?>
												<tr id="id_{{$alist->id}}">
													<td><a class="openapplicationdetail" data-id="{{$alist->id}}" href="javascript:;" style="display:block;">{{@$productdetail->name}}</a> <small>{{@$partnerdetail->partner_name}} ({{@$PartnerBranch->name}})</small></td> 
													<td>{{@$workflow->name}}</td>
													<td>{{@$alist->stage}}</td>
													<td>
													@if(@$alist->status == 0)
													<span class="ag-label--circular" style="color: #6777ef" >In Progress</span>
													@elseif(@$alist->status == 1)
														<span class="ag-label--circular" style="color: #6777ef" >Completed</span>
															@elseif(@$alist->status == 2)
														<span class="ag-label--circular" style="color: red;" >Discontinued</span>
													@endif
												</td> 
													<td>{{@$alist->sale_forcast}}</td>
													<td>{{date('Y-m-d', strtotime(@$alist->created_at))}}</td> 
													<td>{{date('Y-m-d', strtotime(@$alist->updated_at))}}</td> 
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">
																
															</div>
														</div>								  
													</td>
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
									<div class="ifapplicationdetailnot" style="display:none;">
										<h4>Please wait ...</h4>
									</div>
								</div>
								<div class="tab-pane fade" id="interested_service" role="tabpanel" aria-labelledby="interested_service-tab">
									<div class="card-header-action text-end" style="padding-bottom:15px;">
										<a href="javascript:;" data-bs-toggle="modal" data-bs-target=".add_interested_service" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="interest_serv_list">
								
									<?php
									
									$inteservices = \App\Models\InterestedService::where('client_id',$fetchedData->id)->orderby('created_at', 'DESC')->get();
									foreach($inteservices as $inteservice){
										$workflowdetail = \App\Models\Workflow::where('id', $inteservice->workflow)->first();
										 $productdetail = \App\Models\Product::where('id', $inteservice->product)->first();
										$partnerdetail = \App\Models\Partner::where('id', $inteservice->partner)->first();
										$PartnerBranch = \App\Models\PartnerBranch::where('id', $inteservice->branch)->first(); 
										$admin = \App\Models\Admin::where('id', $inteservice->user_id)->first();
									?>
										<div class="interest_column">
											<?php
												if($inteservice->status == 1){
													?>
													<div class="interest_serv_status status_active">
														<span>Converted</span>
													</div>
													<?php
												}else{
													?>
													<div class="interest_serv_status status_default">
														<span>Draft</span>
													</div>
													<?php
												}
												?>
											<?php
			$client_revenue = '0.00';
			if($inteservice->client_revenue != ''){
				$client_revenue = $inteservice->client_revenue;
			}
			$partner_revenue = '0.00';
			if($inteservice->partner_revenue != ''){
				$partner_revenue = $inteservice->partner_revenue;
			}
			$discounts = '0.00';
			if($inteservice->discounts != ''){
				$discounts = $inteservice->discounts;
			}
			$nettotal = $client_revenue + $partner_revenue - $discounts;
			
			
			$totl = 0.00;
			$net = 0.00;
			$discount = 0.00;
			?>
											<div class="interest_serv_info">
												<h4>{{@$workflowdetail->name}}</h4>
												<h6>{{@$productdetail->name}}</h6>
												<p>{{@$partnerdetail->partner_name}}</p>
												<p>{{@$PartnerBranch->name}}</p>
											</div>
											<div class="interest_serv_fees">
												<div class="fees_col cus_col">
													<span class="cus_label">Product Fees</span>
													<span class="cus_value">AUD: <?php echo number_format($net,2,'.',''); ?></span>
												</div>
												<div class="fees_col cus_col">
													<span class="cus_label">Sales Forecast</span>
													<span class="cus_value">AUD: <?php echo number_format($nettotal,2,'.',''); ?></span>
												</div>
											</div>
											<div class="interest_serv_date">
												<div class="date_col cus_col">
													<span class="cus_label">Expected Start Date</span>
													<span class="cus_value">{{$inteservice->start_date}}</span>
												</div>
												<div class="fees_col cus_col">
													<span class="cus_label">Expected Win Date</span>
													<span class="cus_value">{{$inteservice->exp_date}}</span>
												</div>
											</div>
											<div class="interest_serv_row">
												<div class="serv_user_data">
													<div class="serv_user_img"><?php echo substr($admin->first_name, 0, 1); ?></div>
													<div class="serv_user_info">
														<span class="serv_name">{{$admin->first_name}}</span>
														<span class="serv_create">{{date('Y-m-d', strtotime($inteservice->exp_date))}}</span>
													</div> 
												</div>
												<div class="serv_user_action">
													<a href="javascript:;" data-id="{{$inteservice->id}}" class="btn btn-primary interest_service_view">View</a>
													<div class="dropdown d-inline dropdown_ellipsis_icon" style="margin-left:10px;">
														<a class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">
														<?php if($inteservice->status == 0){ ?>
															<a class="dropdown-item converttoapplication" data-id="{{$inteservice->id}}" href="javascript:;">Create Appliation</a>
														<?php } ?>
															
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>
										
									</div>
									<div class="clearfix"></div>
								</div>	
								<div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
									<div class="card-header-action text-end" style="padding-bottom:15px;">
										<div class="document_layout_type">
											<a href="javascript:;" class="list active"><i class="fas fa-list"></i></a>
											<a href="javascript:;" class="grid"><i class="fas fa-columns"></i></a>
										</div>
										<div class="upload_document" style="display:inline-block;">
										<form method="POST" enctype="multipart/form-data" id="upload_form">
											@csrf
											<input type="hidden" name="clientid" value="{{$fetchedData->id}}">
											<input type="hidden" name="type" value="client">
											<a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>
											
											<input class="docupload" multiple type="file" name="document_upload[]"/>
											</form>
										</div>
									</div>
									<div class="list_data"> 
										<div class="table-responsive"> 
											<table class="table text_wrap">
												<thead>
													<tr>
														<th>File Name</th>
														<th>Added By</th>
													
														<th>Added Date</th>
														<th></th>
													</tr> 
												</thead>
												<tbody class="tdata documnetlist">
										<?php 
										$fetchd = \App\Models\Document::where('client_id',$fetchedData->id)->where('type','client')->orderby('created_at', 'DESC')->get();
										foreach($fetchd as $fetch){ 
										$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
										?>												
													<tr class="drow" id="id_{{$fetch->id}}">
													<td  >
														<div data-id="{{$fetch->id}}" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
															<i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name; ?><?php echo '.'.$fetch->filetype; ?></span>
														</div>
													</td> 
													<td><?php echo $admin->first_name; ?></td>
													
													<td><?php echo date('Y-m-d', strtotime($fetch->created_at)); ?></td> 
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">
																<a class="dropdown-item renamedoc" href="javascript:;">Rename</a>
																<a target="_blank" class="dropdown-item" href="{{asset('img/documents')}}/<?php echo $fetch->myfile; ?>">Preview</a>
																<a download class="dropdown-item" href="{{asset('img/documents')}}/<?php echo $fetch->myfile; ?>">Download</a>
																<a data-id="{{$fetch->id}}" class="dropdown-item deletenote" data-href="deletedocs" href="javascript:;">Delete</a>
															</div>
														</div>								  
													</td>
												</tr>
												<?php } ?>
												</tbody>
												
											</table> 
										</div>
									</div>
									<div class="grid_data griddata">
									<?php
									foreach($fetchd as $fetch){ 
										$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
									?>
										<div class="grid_list" id="gid_<?php echo $fetch->id; ?>">
											<div class="grid_col"> 
												<div class="grid_icon">
													<i class="fas fa-file-image"></i>
												</div> 
												<div class="grid_content">
													<span id="grid_<?php echo $fetch->id; ?>" class="gridfilename"><?php echo $fetch->file_name; ?></span>
													<div class="dropdown d-inline dropdown_ellipsis_icon">
														<a class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">
														
																<a target="_blank" class="dropdown-item" href="{{asset('img/documents')}}/<?php echo $fetch->myfile; ?>">Preview</a>
																<a download class="dropdown-item" href="{{asset('img/documents')}}/<?php echo $fetch->myfile; ?>">Download</a>
																<a data-id="{{$fetch->id}}" class="dropdown-item deletenote" data-href="deletedocs" href="javascript:;">Delete</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>
										<div class="clearfix"></div>
									</div>
								</div>
								{{-- Appointments tab removed - Appointment model deleted --}}
								<div class="tab-pane fade" id="noteterm" role="tabpanel" aria-labelledby="noteterm-tab">
									<div class="card-header-action text-end" style="padding-bottom:15px;">
										<a href="javascript:;" datatype="note" class="create_note btn btn-primary"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="note_term_list"> 									
									<?php									
									$notelist = \App\Models\Note::where('client_id', $fetchedData->id)->where('type', 'client')->orderby('pin', 'DESC')->get();
									foreach($notelist as $list){
										$admin = \App\Models\Admin::where('id', $list->user_id)->first();
									?>
										<div class="note_col" id="note_id_{{$list->id}}"> 
											<div class="note_content">
												<h4><a class="viewnote" data-id="{{$list->id}}" href="javascript:;">{{ @$list->title == "" ? config('constants.empty') : str_limit(@$list->title, '19', '...') }}</a></h4>
											<?php if($list->pin == 1){
									?><div class="pined_note"><i class="fa fa-thumbtack"></i></i></div><?php } ?>
											</div>
											<div class="extra_content">
											    <p>{!! @$list->description !!}</p>
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
														<a class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">
															<a class="dropdown-item opennoteform" data-id="{{$list->id}}" href="javascript:;">Edit</a>
															<a data-id="{{$list->id}}" data-href="deletenote" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
															<?php if($list->pin == 1){
									?>
									<a data-id="<?php echo $list->id; ?>"  class="dropdown-item pinnote" href="javascript:;" >UnPin</a>
									<?php
								}else{ ?>
									<a data-id="<?php echo $list->id; ?>"  class="dropdown-item pinnote" href="javascript:;" >Pin</a>
								<?php } ?>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>
									</div>
									<div class="clearfix"></div>
								</div>
								
									
							</div> 
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div> 

@include('Agent/clients/addclientmodal')  
@include('Agent/clients/editclientmodal')   




<div class="modal fade  custom_modal" id="interest_service_view" tabindex="-1" role="dialog" aria-labelledby="interest_serviceModalLabel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content showinterestedservice">
			
		</div>
	</div>
</div>

<div id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn-close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to delete this note?</h4> 
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Delete</button> 
				<button type="button" style="margin-top: 40px;" data-bs-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmEducationModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn-close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to delete this note?</h4> 
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accepteducation">Delete</button> 
				<button type="button" style="margin-top: 40px;" data-bs-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>
<div id="confirmcompleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn-close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to complete the Application?</h4> 
				<button  data-id="" type="submit" style="margin-top: 40px;" class="button btn btn-danger acceptapplication">Complete</button> 
				<button type="button" style="margin-top: 40px;" data-bs-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>


<div id="confirmpublishdocModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn-close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Publish Document?</h4> 
				<h5 class="">Publishing documents will allow client to access from client portal , Are you sure you want to continue ?</h5> 
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger acceptpublishdoc">Publish Anyway</button> 
				<button type="button" style="margin-top: 40px;" data-bs-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
{{-- Configuration Script: Pass Blade variables to JavaScript --}}
<script>
    window.AppConfig = window.AppConfig || {};
    window.PageConfig = window.PageConfig || {};
				
    // Global Configuration
    AppConfig.csrf = '{{ csrf_token() }}';
    AppConfig.siteUrl = '{{ url("/") }}';
    AppConfig.urls = {
        siteUrl: '{{ url("/") }}',
        // Activity & Notes
        getNotes: '{{ url("/agent/get-notes") }}',
        getActivities: '{{ url("/agent/get-activities") }}',
        getNoteDetail: '{{ url("/agent/getnotedetail") }}',
        viewNoteDetail: '{{ url("/agent/viewnotedetail") }}',
        viewApplicationNote: '{{ url("/agent/viewapplicationnote") }}',
        pinNote: '{{ url("/agent/pinnote") }}',
        
        // Tasks
        getTaskDetail: '{{ url("/agent/get-task-detail") }}',
        
        // Documents
        publishDoc: '{{ url("/agent/application/publishdoc") }}',
        uploadDocument: '{{ url("/agent/upload-document") }}',
        renameDoc: '{{ url("/agent/renamedoc") }}',
        applicationChecklistUpload: '{{ url("/agent/application/checklistupload") }}',
        
        // Applications
        getApplicationDetail: '{{ url("/agent/getapplicationdetail") }}',
        getApplicationLists: '{{ url("/agent/get-application-lists") }}',
        getApplicationsLogs: '{{ url("/agent/get-applications-logs") }}',
        updateStage: '{{ url("/agent/updatestage") }}',
        updateBackStage: '{{ url("/agent/updatebackstage") }}',
        completeStage: '{{ url("/agent/completestage") }}',
        updateApplicationDates: '{{ url("/agent/application/updatedates") }}',
        updateIntake: '{{ url("/agent/application/updateintake") }}',
        updateExpectWin: '{{ url("/agent/application/updateexpectwin") }}',
        addScheduleInvoiceDetail: '{{ url("/agent/addscheduleinvoicedetail") }}',
        scheduleInvoiceDetail: '{{ url("/agent/scheduleinvoicedetail") }}',
        
        // Services
        getServices: '{{ url("/agent/get-services") }}',
        convertApplication: '{{ url("/agent/convertapplication") }}',
        getInterestedService: '{{ url("/agent/getintrestedservice") }}',
        getInterestedServiceEdit: '{{ url("/agent/getintrestedserviceedit") }}',
        
        // Email & Templates
        clientGetRecipients: '{{ url("/clients/get-recipients") }}',
        getTemplates: '{{ url("/agent/get-templates") }}',
        
        // Partner/Product/Branch
        getPartnerBranch: '{{ url("/agent/getpartnerbranch") }}',
        getBranchProduct: '{{ url("/agent/getbranchproduct") }}',
        getPartner: '{{ url("/agent/getpartner") }}',
        getProduct: '{{ url("/agent/getproduct") }}',
        getBranch: '{{ url("/agent/getbranch") }}',
        getSubjects: '{{ url("/agent/getsubjects") }}',
        
        // Client Management
        changeClientStatus: '{{ url("/agent/change-client-status") }}',
        
        // Other
        getAppointmentDetail: '{{ url("/agent/getAppointmentdetail") }}',
        getEducationDetail: '{{ url("/agent/getEducationdetail") }}',
        getApplicationNotes: '{{ url("/agent/getapplicationnotes") }}',
        showProductFee: '{{ url("/agent/showproductfee") }}',
        saveTag: '{{ url("/agent/save_tag") }}',
    };

    // Page-Specific Configuration
    PageConfig.clientId = {{ $fetchedData->id }};
    PageConfig.clientType = 'client';
    PageConfig.siteUrl = '{{ url("/") }}';
    
    @if(isset($_GET['tab']) && $_GET['tab'] == 'application' && isset($_GET['appid']) && $_GET['appid'] != '')
    PageConfig.initialAppId = {{ $_GET['appid'] }};
    @endif
    
    @if(isset($appointmentdata) && !empty($appointmentdata))
    PageConfig.appointmentData = @json($appointmentdata);
    @else
    PageConfig.appointmentData = {};
    @endif
</script>

{{-- Common Modules (load first) --}}
<script src="{{ asset('js/common/config.js') }}"></script>
<script src="{{ asset('js/common/ajax-helpers.js') }}"></script>
<script src="{{ asset('js/common/utilities.js') }}"></script>
<script src="{{ asset('js/common/crud-operations.js') }}"></script>
<script src="{{ asset('js/common/activity-handlers.js') }}"></script>
<script src="{{ asset('js/common/document-handlers.js') }}"></script>
<script src="{{ asset('js/common/ui-components.js') }}"></script>

{{-- Page-Specific Module (load last) --}}
<script src="{{ asset('js/pages/agent/client-detail.js') }}"></script>
@endsection

<div class="modal fade custom_modal" id="application_opensaleforcast" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"> 
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Sales Forecast</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/agent/application/saleforcast')}}" name="saleforcast" id="saleforcast" autocomplete="off" enctype="multipart/form-data">
				@csrf 
				<input type="hidden" name="fapp_id" id="fapp_id" value="">
					<div class="row">
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Client Revenue</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="client_revenue" name="client_revenue">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Partner Revenue</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="partner_revenue" name="partner_revenue">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Discounts</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="discounts" name="discounts">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('saleforcast')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form> 
			</div>
		</div>
	</div>
</div>
<div class="modal fade custom_modal" id="application_ownership" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"> 
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Application Ownership Ratio</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/agent/application/application_ownership')}}" name="xapplication_ownership" id="xapplication_ownership" autocomplete="off" enctype="multipart/form-data">
				@csrf 
				<input type="hidden" name="mapp_id" id="mapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="sus_agent"> </label>
								<input type="number" max="100" min="0" step="0.01" class="form-control ration" name="ratio">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('xapplication_ownership')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form> 
			</div>
		</div>
	</div>
</div>
<div class="modal fade custom_modal" id="superagent_application" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"> 
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Select Super Agent</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/agent/application/spagent_application')}}" name="spagent_application" id="spagent_application" autocomplete="off" enctype="multipart/form-data">
				@csrf 
				<input type="hidden" name="siapp_id" id="siapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="super_agent">Super Agent <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control super_agent" id="super_agent" name="super_agent">
									<option value="">Please Select</option>
									<?php $sagents = \App\Models\Agent::whereRaw('? = ANY(string_to_array(agent_type, \',\'))', ['Super Agent'])->get(); ?>
									@foreach($sagents as $sa)
										<option value="{{$sa->id}}">{{$sa->full_name}} {{$sa->email}}</option>
									@endforeach
								</select>
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('spagent_application')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form> 
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="subagent_application" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"> 
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Select Sub Agent</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/agent/application/sbagent_application')}}" name="sbagent_application" id="sbagent_application" autocomplete="off" enctype="multipart/form-data">
				@csrf 
				<input type="hidden" name="sbapp_id" id="sbapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="sub_agent">Sub Agent <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control sub_agent" id="sub_agent" name="sub_agent">
									<option value="">Please Select</option>
									<?php $sagents = \App\Models\Agent::whereRaw('? = ANY(string_to_array(agent_type, \',\'))', ['Sub Agent'])->where('is_acrchived',0)->get(); ?>
									@foreach($sagents as $sa)
										<option value="{{$sa->id}}">{{$sa->full_name}} {{$sa->email}}</option>
									@endforeach
								</select>
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('sbagent_application')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form> 
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="tags_clients" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Tags</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/agent/save_tag')}}" name="stags_application" id="stags_application" autocomplete="off" enctype="multipart/form-data">
				@csrf 
				<input type="hidden" name="client_id" id="tags_client_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="super_agent">Tags <span class="span_req">*</span></label>
								<select data-valid="required" multiple class="tagsselec form-control super_tag" id="tag" name="tag[]">
								<?php $r = array(); 
								if($fetchedData->tagname != ''){
									$r = explode(',', $fetchedData->tagname);
								} 
								?>
									<option value="">Please Select</option>
									<?php $stagd = \App\Models\Tag::where('id','!=','')->get(); ?>
									@foreach($stagd as $sa)
										<option <?php if(in_array($sa->id, $r)){ echo 'selected'; } ?> value="{{$sa->id}}">{{$sa->name}}</option>
									@endforeach
								</select>
								
							</div>
						</div>
						
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('stags_application')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form> 
			</div>
		</div>
	</div>
</div>
<div class="modal fade custom_modal" id="new_fee_option" tabindex="-1" role="dialog" aria-labelledby="feeoptionModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="feeoptionModalLabel">Fee Option</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showproductfee">
				 
			</div>
		</div>
	</div>
</div> 



<div class="modal fade custom_modal" id="application_opensaleforcast" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"> 
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Sales Forecast</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/agent/application/saleforcast')}}" name="saleforcast" id="saleforcast" autocomplete="off" enctype="multipart/form-data">
				@csrf 
				<input type="hidden" name="fapp_id" id="fapp_id" value="">
					<div class="row">
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Client Revenue</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="client_revenue" name="client_revenue">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Partner Revenue</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="partner_revenue" name="partner_revenue">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Discounts</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="discounts" name="discounts">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('saleforcast')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form> 
			</div>
		</div>
	</div>
</div>


<div class="modal fade custom_modal" id="application_opensaleforcastservice" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"> 
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Sales Forecast</h5>
				<button type="button" class="close closeservmodal" >
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/agent/application/saleforcastservice')}}" name="saleforcastservice" id="saleforcastservice" autocomplete="off" enctype="multipart/form-data">
				@csrf 
				<input type="hidden" name="fapp_id" id="fapp_id" value="">
					<div class="row">
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Client Revenue</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="client_revenue" name="client_revenue">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Partner Revenue</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="partner_revenue" name="partner_revenue">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Discounts</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="discounts" name="discounts">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('saleforcastservice')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form> 
			</div>
		</div>
	</div>
</div>
@endsection
