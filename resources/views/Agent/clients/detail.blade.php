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
								<a href="{{route('agent.clients.index')}}" class="btn btn-primary">Client List</a>
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
									
									
									<a href="{{URL::to('/agent/clients/edit/'.base64_encode(convert_uuencode(@$fetchedData->id)))}}" title="Edit"><i class="fa fa-edit"></i></a>
									
								</div>
							
							</div>
							<?php
	$agent = \App\Agent::where('id',@$fetchedData->agent_id)->first();
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
								<span class="float-left">Tag(s):</span>
								<span class="float-right text-muted">
								
									
									
								</span>
							</p>
							<p>
							<?php $tags = ''; 
							if($fetchedData->tagname != ''){
								$rs = explode(',', $fetchedData->tagname);
								foreach($rs as $r){
									$stagd = \App\Tag::where('id','=',$r)->first();
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
								<span class="float-left">Client Id:</span>
								<span class="float-right text-muted">{{$fetchedData->client_id}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Internal Id:</span>
								<span class="float-right text-muted">{{$fetchedData->id}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Date Of Birth:</span>
								<span class="float-right text-muted">{{$fetchedData->dob}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Phone No:</span>
								<span class="float-right text-muted">{{$fetchedData->phone}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Email:</span>
								<span class="float-right text-muted">{{$fetchedData->email}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Secondary Email:</span>
								<span class="float-right text-muted">-</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Address:</span>
								<span class="float-right text-muted">{{$fetchedData->address}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Country of Passport:</span>
								<span class="float-right text-muted">{{$fetchedData->country_passport}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Passport Number:</span>
								<span class="float-right text-muted">{{$fetchedData->passport_number}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Preferred Intake:</span>
								<span class="float-right text-muted"><?php if($fetchedData->preferredIntake != ''){ ?>{{date('M Y', strtotime($fetchedData->preferredIntake))}}<?php } ?></span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Visa Expiry Date:</span>
								<span class="float-right text-muted">{{$fetchedData->visaExpiry}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-left">Visa type:</span>
								<span class="float-right text-muted">{{$fetchedData->visa_type}}</span>
							</p> 
							<?php
								$addedby = \App\Admin::where('id',@$fetchedData->user_id)->first();
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
								$assignee = \App\Admin::where('id',@$fetchedData->assignee)->first();
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
								        $relatedclientss = \App\Admin::whereRaw("FIND_IN_SET($fetchedData->id,related_files)")->get();	
								        foreach($relatedclientss AS $res){ 
									?>
									    <li><a target="_blank" href="{{URL::to('/agent/clients/detail/'.base64_encode(convert_uuencode(@$res->id)))}}">{{$res->first_name}} {{$res->last_name}}</a></li>
									<?php } ?>
									<?php
								if($fetchedData->related_files != ''){
								    $exploder = explode(',', $fetchedData->related_files);
								 
							
							?>
									<?php   foreach($exploder AS $EXP){ 
								        $relatedclients = \App\Admin::where('id', $EXP)->first();	
									?>
									    <li><a target="_blank" href="{{URL::to('/agent/clients/detail/'.base64_encode(convert_uuencode(@$relatedclients->id)))}}">{{$relatedclients->first_name}} {{$relatedclients->last_name}}</a></li>
									<?php } ?>
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
									<a class="nav-link <?php if(isset($_GET['tab']) && $_GET['tab'] == 'application'){ echo 'active'; }else{ echo 'active'; } ?>" data-toggle="tab" id="application-tab" href="#application" role="tab" aria-controls="application" aria-selected="false">Applications</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="interested_service-tab" href="#interested_service" role="tab" aria-controls="interested_service" aria-selected="false">Interested Services</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="documents-tab" href="#documents" role="tab" aria-controls="documents" aria-selected="false">Documents</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="appointments-tab" href="#appointments" role="tab" aria-controls="appointments" aria-selected="false">Appointments</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="noteterm-tab" href="#noteterm" role="tab" aria-controls="noteterm" aria-selected="false">Notes & Terms</a>
								</li>
								
							
							
							</ul> 
							<div class="tab-content" id="clientContent" style="padding-top:15px;">
								
								<div class="tab-pane fade <?php if(isset($_GET['tab']) && $_GET['tab'] == 'application'){ echo 'show active'; }else{ echo 'show active'; } ?>" id="application" role="tabpanel" aria-labelledby="application-tab">
									<div class="card-header-action text-right if_applicationdetail" style="padding-bottom:15px;">
									
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
											foreach(\App\Application::where('client_id', $fetchedData->id)->orderby('created_at','Desc')->get() as $alist){
												$productdetail = \App\Product::where('id', $alist->product_id)->first();
												$partnerdetail = \App\Partner::where('id', $alist->partner_id)->first();
												$PartnerBranch = \App\PartnerBranch::where('id', $alist->branch)->first();
												$workflow = \App\Workflow::where('id', $alist->workflow)->first();
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
															<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
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
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<a href="javascript:;" data-toggle="modal" data-target=".add_interested_service" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="interest_serv_list">
								
									<?php
									
									$inteservices = \App\InterestedService::where('client_id',$fetchedData->id)->orderby('created_at', 'DESC')->get();
									foreach($inteservices as $inteservice){
										$workflowdetail = \App\Workflow::where('id', $inteservice->workflow)->first();
										 $productdetail = \App\Product::where('id', $inteservice->product)->first();
										$partnerdetail = \App\Partner::where('id', $inteservice->partner)->first();
										$PartnerBranch = \App\PartnerBranch::where('id', $inteservice->branch)->first(); 
										$admin = \App\Admin::where('id', $inteservice->user_id)->first();
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
			
			
			$appfeeoption = \App\ServiceFeeOption::where('app_id', $inteservice->id)->first();
			
			$totl = 0.00;
			$net = 0.00;
			$discount = 0.00;
			if($appfeeoption){
				$appfeeoptiontype = \App\ServiceFeeOptionType::where('fee_id', $appfeeoption->id)->get();
				foreach($appfeeoptiontype as $fee){
					$totl += $fee->total_fee;
				}
			}
	
			if(@$appfeeoption->total_discount != ''){
				$discount = @$appfeeoption->total_discount;
			}
			$net = $totl -  $discount;
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
														<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
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
									<div class="card-header-action text-right" style="padding-bottom:15px;">
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
										$fetchd = \App\Document::where('client_id',$fetchedData->id)->where('type','client')->orderby('created_at', 'DESC')->get();
										foreach($fetchd as $fetch){ 
										$admin = \App\Admin::where('id', $fetch->user_id)->first();
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
															<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">
																<a class="dropdown-item renamedoc" href="javascript:;">Rename</a>
																<a target="_blank" class="dropdown-item" href="{{URL::to('/public/img/documents')}}/<?php echo $fetch->myfile; ?>">Preview</a>
																<a download class="dropdown-item" href="{{URL::to('/public/img/documents')}}/<?php echo $fetch->myfile; ?>">Download</a>
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
										$admin = \App\Admin::where('id', $fetch->user_id)->first();
									?>
										<div class="grid_list" id="gid_<?php echo $fetch->id; ?>">
											<div class="grid_col"> 
												<div class="grid_icon">
													<i class="fas fa-file-image"></i>
												</div> 
												<div class="grid_content">
													<span id="grid_<?php echo $fetch->id; ?>" class="gridfilename"><?php echo $fetch->file_name; ?></span>
													<div class="dropdown d-inline dropdown_ellipsis_icon">
														<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">
														
																<a target="_blank" class="dropdown-item" href="{{URL::to('/public/img/documents')}}/<?php echo $fetch->myfile; ?>">Preview</a>
																<a download class="dropdown-item" href="{{URL::to('/public/img/documents')}}/<?php echo $fetch->myfile; ?>">Download</a>
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
								<div class="tab-pane fade" id="appointments" role="tabpanel" aria-labelledby="appointments-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										
									</div>
									<div class="appointmentlist">
										<div class="row">
											<div class="col-md-5 appointment_grid_list">
												<?php
												$rr=0;
												$appointmentdata = array();
												$appointmentlists = \App\Appointment::where('client_id', $fetchedData->id)->where('related_to', 'client')->orderby('created_at', 'DESC')->get();
												$appointmentlistslast = \App\Appointment::where('client_id', $fetchedData->id)->where('related_to', 'client')->orderby('created_at', 'DESC')->first();
												foreach($appointmentlists as $appointmentlist){
													$admin = \App\Admin::where('id', $appointmentlist->user_id)->first();
													$datetime = $appointmentlist->created_at;
													$timeago = Controller::time_elapsed_string($datetime);
													
													$appointmentdata[$appointmentlist->id] = array(
														'title' => $appointmentlist->title,
														'time' => date('H:i A', strtotime($appointmentlist->time)),
														'date' => date('d D, M Y', strtotime($appointmentlist->date)),
														'description' => $appointmentlist->description,
														'createdby' => substr($admin->first_name, 0, 1),
														'createdname' => $admin->first_name,
														'createdemail' => $admin->email,
													);
												?> 
												<div class="appointmentdata <?php if($rr == 0){ echo 'active'; } ?>" data-id="<?php echo $appointmentlist->id; ?>">
													<div class="appointment_col">
														<div class="appointdate">
															<h5><?php echo date('d D', strtotime($appointmentlist->date)); ?></h5>
															<p><?php echo date('H:i A', strtotime($appointmentlist->time)); ?><br>
															<i><small><?php echo $timeago ?></small></i></p>
														</div>
														<div class="title_desc">
															<h5><?php echo $appointmentlist->title; ?></h5>
															<p><?php echo $appointmentlist->description; ?></p>
														</div>
														<div class="appoint_created">
															<span class="span_label">Created By: 
															<span>{{substr($admin->first_name, 0, 1)}}</span></span>
														
														</div>
													</div>
												</div>  
												<?php $rr++; } ?>
											</div> 
											<div class="col-md-7">
												<div class="editappointment">
												@if($appointmentlistslast)
													
													<?php
													$adminfirst = \App\Admin::where('id', @$appointmentlistslast->user_id)->first();
													?>
													<div class="content">
														<h4 class="appointmentname"><?php echo @$appointmentlistslast->title; ?></h4>
														<div class="appitem">
															<i class="fa fa-clock"></i>
															<span class="appcontent appointmenttime"><?php echo date('H:i A', strtotime(@$appointmentlistslast->time)); ?></span>
														</div>
														<div class="appitem">
															<i class="fa fa-calendar"></i>
															<span class="appcontent appointmentdate"><?php echo date('d D, M Y', strtotime(@$appointmentlistslast->date)); ?></span>
														</div>
														<div class="description appointmentdescription">
															<p><?php echo @$appointmentlistslast->description; ?></p>
														</div>
														<div class="created_by">
															<span class="label">Created By:</span>
															<div class="createdby">
																<span class="appointmentcreatedby"><?php echo substr(@$adminfirst->first_name, 0, 1); ?></span>
															</div>
															<div class="createdinfo">
																<a href="" class="appointmentcreatedname"><?php echo @$adminfirst->first_name ?></a>
																<p class="appointmentcreatedemail"><?php echo @$adminfirst->primary_email; ?></p>
															</div>
														</div>
													</div>
													@endif
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="noteterm" role="tabpanel" aria-labelledby="noteterm-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<a href="javascript:;" datatype="note" class="create_note btn btn-primary"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="note_term_list"> 									
									<?php									
									$notelist = \App\Note::where('client_id', $fetchedData->id)->where('type', 'client')->orderby('pin', 'DESC')->get();
									foreach($notelist as $list){
										$admin = \App\Admin::where('id', $list->user_id)->first();
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
														<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
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


<div id="confirmpublishdocModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Publish Document?</h4> 
				<h5 class="">Publishing documents will allow client to access from client portal , Are you sure you want to continue ?</h5> 
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger acceptpublishdoc">Publish Anyway</button> 
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
			url: site_url+'/agent/get-task-detail',
			type:'GET',
			data:{task_id:v},
			success: function(responses){
				
				$('.taskview').html(responses);
			}
		});
	});
	function getallnotes(){
	$.ajax({
		url: site_url+'/agent/get-notes',
		type:'GET',
		data:{clientid:'{{$fetchedData->id}}',type:'client'},
		success: function(responses){
			$('.popuploader').hide(); 
			$('.note_term_list').html(responses);
		}
	});
}

function getallactivities(){
	$.ajax({
					url: site_url+'/agent/get-activities',
					type:'GET',
					datatype:'json',
					data:{id:'{{$fetchedData->id}}'},
					success: function(responses){
						var ress = JSON.parse(responses);
						var html = '';
						$.each(ress.data, function(k, v) {
							html += '<div class="activity"><div class="activity-icon bg-primary text-white"><span>'+v.createdname+'</span></div><div class="activity-detail"><div class="activity-head"><div class="activity-title"><p><b>'+v.name+'</b> '+v.subject+'</p></div><div class="activity-date"><span class="text-job">'+v.date+'</span></div></div>';
							if(v.message != null){
								html += '<p>'+v.message+'</p>';
							}
							html += '</div></div>';
						});
						$('.activities').html(html);
					}
				});
}
var appcid = '';
	$(document).delegate('.publishdoc', 'click', function(){
		$('#confirmpublishdocModal').modal('show');
		appcid = $(this).attr('data-id');

	});

$(document).delegate('#confirmpublishdocModal .acceptpublishdoc', 'click', function(){
	$('.popuploader').show(); 
	$.ajax({
		url: '{{URL::to('/agent/')}}/'+'application/publishdoc',
		type:'GET',
		datatype:'json',
		data:{appid:appcid,status:'1'},
		success:function(response){
			$('.popuploader').hide(); 
			var res = JSON.parse(response);
			$('#confirmpublishdocModal').modal('hide');
			if(res.status){
				$('.mychecklistdocdata').html(res.doclistdata);
			}else{
				alert(res.message);
			}
		}
	});
});

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
			url: '{{URL::to('/agent/')}}/'+delhref,
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
						url: site_url+'/agent/get-services',
						type:'GET',
						data:{clientid:'{{$fetchedData->id}}'},
						success: function(responses){
							
							$('.interest_serv_list').html(responses);
						}
					});
					}else if(delhref == 'superagent'){
						$('.supagent_data').html('');
					}else if(delhref == 'subagent'){
						$('.subagent_data').html('');
					}else if(delhref == 'deleteappointment'){
						$.ajax({
						url: site_url+'/agent/get-appointments',
						type:'GET',
						data:{clientid:'{{$fetchedData->id}}'},
						success: function(responses){
							
							$('.appointmentlist').html(responses);
						}
					});
					}else if(delhref == 'deletepaymentschedule'){
						$.ajax({
						url: site_url+'/agent/get-all-paymentschedules',
						type:'GET',
						data:{client_id:'{{$fetchedData->id}}',appid:res.application_id},
						success: function(responses){
							
							$('.showpaymentscheduledata').html(responses);
						}
					});
					}else if(delhref == 'deleteapplicationdocs'){
						$('.mychecklistdocdata').html(res.doclistdata);
					  $('.checklistuploadcount').html(res.applicationuploadcount);
					  $('.'+res.type+'_checklists').html(res.checklistdata);
					
					}else{
						getallnotes();
					}
					
					getallactivities();
				}
			}
		});
	});
	$(document).delegate('.pinnote', 'click', function(){
		$('.popuploader').show(); 
		$.ajax({
			url: '{{URL::to('/agent/pinnote')}}/',
			type:'GET',
			datatype:'json',
			data:{note_id:$(this).attr('data-id')},
			success:function(response){
				getallnotes();
			}
		});
	});
	
	$(document).delegate('.createapplicationnewinvoice', 'click', function(){
		$('#opencreateinvoiceform').modal('show');	
		var sid	= $(this).attr('data-id');
		var cid	= $(this).attr('data-cid');
		var aid	= $(this).attr('data-app-id');
		$('#client_id').val(cid);
		$('#app_id').val(aid);
		$('#schedule_id').val(sid);
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

$('.js-data-example-ajaxccapp').select2({
		 multiple: true,
		 closeOnSelect: false,
		dropdownParent: $('#applicationemailmodal'),
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
$('.js-data-example-ajaxcontact').select2({
		 multiple: true,
		 closeOnSelect: false,
		dropdownParent: $('#opentaskmodal'),
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
	$(document).delegate('.opennoteform', 'click', function(){
		$('#create_note').modal('show');
		$('#create_note #appliationModalLabel').html('Edit Note');
		var v = $(this).attr('data-id');
		$('#create_note input[name="noteid"]').val(v);
			$('.popuploader').show(); 
		$.ajax({
			url: '{{URL::to('/agent/getnotedetail')}}',
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
			url: '{{URL::to('/agent/viewnotedetail')}}',
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
	
	$(document).delegate('.viewapplicationnote', 'click', function(){
		$('#view_application_note').modal('show');
		var v = $(this).attr('data-id');
		$('#view_application_note input[name="noteid"]').val(v);
			$('.popuploader').show(); 
		$.ajax({
			url: '{{URL::to('/agent/viewapplicationnote')}}',
			type:'GET',
			datatype:'json',
			data:{note_id:v},
			success:function(response){
				$('.popuploader').hide(); 
				var res = JSON.parse(response);
				
				if(res.status){
					$('#view_application_note .modal-body .note_content h5').html(res.data.title);
					$("#view_application_note .modal-body .note_content p").html(res.data.description);                    
					
				} 
			}
		});
	});
	$(document).delegate('.add_appliation #workflow', 'change', function(){
	
				var v = $('.add_appliation #workflow option:selected').val();
				if(v != ''){
						$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/agent/getpartnerbranch')}}',
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
			url: '{{URL::to('/agent/getbranchproduct')}}',
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
		url: '{{URL::to('/agent/change-client-status')}}',
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
		url: '{{URL::to('/agent/get-templates')}}',
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
		url: '{{URL::to('/agent/get-templates')}}',
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

$('.js-data-example-ajaxccd').select2({
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
			url: '{{URL::to('/agent/getpartner')}}',
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
			url: '{{URL::to('/agent/getproduct')}}',
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
	
	$(document).delegate('#intrested_product','change', function(){
		
				var v = $('#intrested_product option:selected').val();
				if(v != ''){
					$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/agent/getbranch')}}',
			type:'GET',
			data:{cat_id:v},
			success:function(response){
				$('.popuploader').hide();
				$('#intrested_branch').html(response);
				$("#intrested_branch").val('').trigger('change');
			}
		});
		}
	}); 
	$(document).delegate('.docupload', 'click', function() {
    $(this).attr("value", "");
})  
	$(document).delegate('.docupload', 'change', function() {
		$('.popuploader').show();	
var formData = new FormData($('#upload_form')[0]);		
		$.ajax({
			url: site_url+'/agent/upload-document',
			type:'POST',
			datatype:'json',
			 data: formData,
			contentType: false,
			processData: false,
			
			success: function(responses){
					$('.popuploader').hide();
var ress = JSON.parse(responses);
if(ress.status){
	$('.custom-error-msg').html('<span class="alert alert-success">'+ress.message+'</span>');
	$('.documnetlist').html(ress.data);
	$('.griddata').html(ress.griddata);
}else{
$('.custom-error-msg').html('<span class="alert alert-danger">'+ress.message+'</span>');	
}	
				getallactivities();
			}
		});
	});
	
	$(document).delegate('.converttoapplication','click', function(){
		
				var v = $(this).attr('data-id');
				if(v != ''){
					$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/agent/convertapplication')}}',
			type:'GET',
			data:{cat_id:v,clientid:'{{$fetchedData->id}}'},
			success:function(response){
				
				$.ajax({
					url: site_url+'/agent/get-services',
					type:'GET',
					data:{clientid:'{{$fetchedData->id}}'},
					success: function(responses){
						
						$('.interest_serv_list').html(responses);
					}
				});
				$.ajax({
					url: site_url+'/agent/get-application-lists',
					type:'GET',
					datatype:'json',
					data:{id:'{{$fetchedData->id}}'},
					success: function(responses){
						$('.applicationtdata').html(responses);
					}
				});
				//getallactivities();
					$('.popuploader').hide();
			}
		});
		}
	});
$(document).on('click', '#application-tab', function () {
    	$('.popuploader').show();
    	$.ajax({
					url: site_url+'/agent/get-application-lists',
					type:'GET',
					datatype:'json',
					data:{id:'{{$fetchedData->id}}'},
					success: function(responses){
					    	$('.popuploader').hide();
						$('.applicationtdata').html(responses);
					}
				});
});
	$(document).on('click', '.documnetlist .renamedoc', function () {
			var parent = $(this).closest('.drow').find('.doc-row');

			parent.data('current-html', parent.html());
			var opentime = parent.data('name');

			parent.empty().append(
				$('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
				
				$('<button class="btn btn-primary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
				$('<button class="btn btn-danger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
			);

			return false;
	
	});
	
	$(document).on('click', '.documnetlist .drow .btn-danger', function () {
			var parent = $(this).closest('.drow').find('.doc-row');
			var hourid = parent.data('id');
			if (hourid) {
				parent.html(parent.data('current-html'));
			} else {
				parent.remove();
				
			}
		});
		
	$(document).delegate('.documnetlist .drow .btn-primary', 'click', function () {
		
			var parent = $(this).closest('.drow').find('.doc-row');
			parent.find('.opentime').removeClass('is-invalid');
			parent.find('.invalid-feedback').remove();

			var opentime = parent.find('.opentime').val();


			if (!opentime) {
				parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
				parent.append($("<div class='invalid-feedback'>This field is required</div>"));
				return false;
			}
			
			$.ajax({
			   type: "POST",
			   data: {"_token": $('meta[name="csrf-token"]').attr('content'),"filename": opentime, "id": parent.data('id')},
			   url: '{{URL::to('/agent/renamedoc')}}',
			   success: function(result){
				   var obj = JSON.parse(result);
				 if (obj.status) {
						parent.empty()
							.data('id', obj.Id)
							.data('name', opentime)
							.append(
								$('<span>').html('<i class="fas fa-file-image"></i> '+obj.filename+'.'+obj.filetype)
							);
							$('#grid_'+obj.Id).html(obj.filename+'.'+obj.filetype);
					} else {
						parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
						parent.append($('<div class="invalid-feedback">' + obj.message + '</div>'));
					}
			   }
			});
			

			return false;
		});
		<?php
       $json = json_encode ( $appointmentdata, JSON_FORCE_OBJECT );
   ?>
$(document).delegate('.appointmentdata', 'click', function () {
	var v = $(this).attr('data-id');
$('.appointmentdata').removeClass('active');
$(this).addClass('active');
	var res = $.parseJSON('<?php echo $json; ?>');
	
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
			url: '{{URL::to('/agent/')}}/delete-education',
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
			url: '{{URL::to('/agent/getsubjects')}}',
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
			url: '{{URL::to('/agent/getAppointmentdetail')}}',
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
     $(".timezoneselects2").select2({
    dropdownParent: $("#edit_appointment")
  });
   $(".invitesselects2").select2({
    dropdownParent: $("#edit_appointment")
  });
			}
		});
	});
	
	$(document).delegate('.editeducation', 'click', function(){
		var v = $(this).attr('data-id');
		$('.popuploader').show();
		$('#edit_education').modal('show');
		$.ajax({
			url: '{{URL::to('/agent/getEducationdetail')}}',
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
			url: '{{URL::to('/agent/getintrestedservice')}}',
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
			url: '{{URL::to('/agent/getintrestedserviceedit')}}',
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
	if(isset($_GET['tab']) && $_GET['tab'] == 'application' && isset($_GET['appid']) && $_GET['appid'] != ''){
		?>
		var appliid = '{{@$_GET['appid']}}';
		$('.if_applicationdetail').hide();
		$('.ifapplicationdetailnot').show();
		$.ajax({
			url: '{{URL::to('/agent/getapplicationdetail')}}',
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
					$('#popuploader').show();
					$.ajax({
						url:"{{URL::to('/agent/application/updateintake')}}",
						method: "GET", // or POST
						dataType: "json",
						data: {from: start.format('YYYY-MM-DD'), appid: appliid},
						success:function(result) {
							$('#popuploader').hide();
							console.log("sent back -> do whatever you want now");
						}
					});
				});
				
				$('.expectdatepicker').daterangepicker({
				locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },
								singleDatePicker: true,
								
								showDropdowns: true,
				}, function(start, end, label) {
					$('#popuploader').show();
					$.ajax({
						url:"{{URL::to('/agent/application/updateexpectwin')}}",
						method: "GET", // or POST
						dataType: "json",
						data: {from: start.format('YYYY-MM-DD'), appid: appliid},
						success:function(result) {
							$('#popuploader').hide();
							console.log("sent back -> do whatever you want now");
						}
					});
				});
				
				$('.startdatepicker').daterangepicker({
				locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },
								singleDatePicker: true,
								
								showDropdowns: true,
				}, function(start, end, label) {
					$('#popuploader').show();
					$.ajax({
						url:"{{URL::to('/agent/application/updatedates')}}",
						method: "GET", // or POST
						dataType: "json",
						data: {from: start.format('YYYY-MM-DD'), appid: appliid, datetype: 'start'},
						success:function(result) {
							$('#popuploader').hide();
								var obj = result;
							if(obj.status){
								$('.app_start_date .month').html(obj.dates.month);
								$('.app_start_date .day').html(obj.dates.date);
								$('.app_start_date .year').html(obj.dates.year);
							}
							console.log("sent back -> do whatever you want now");
						}
					});
				});
				
				$('.enddatepicker').daterangepicker({
				locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },
								singleDatePicker: true,
								
								showDropdowns: true,
				}, function(start, end, label) {
					$('#popuploader').show();
					$.ajax({
						url:"{{URL::to('/agent/application/updatedates')}}",
						method: "GET", // or POST
						dataType: "json",
						data: {from: start.format('YYYY-MM-DD'), appid: appliid, datetype: 'end'},
						success:function(result) {
							$('#popuploader').hide();
								var obj =result;
							if(obj.status){
								$('.app_end_date .month').html(obj.dates.month);
								$('.app_end_date .day').html(obj.dates.date);
								$('.app_end_date .year').html(obj.dates.year);
							}
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
			url: '{{URL::to('/agent/getapplicationdetail')}}',
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
					$('#popuploader').show();
					$.ajax({
						url:"{{URL::to('/agent/application/updateintake')}}",
						method: "GET", // or POST
						dataType: "json",
						data: {from: start.format('YYYY-MM-DD'), appid: appliid},
						success:function(result) {
							$('#popuploader').hide();
							console.log("sent back -> do whatever you want now");
						}
					});
				});
				
				$('.expectdatepicker').daterangepicker({
				locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },
								singleDatePicker: true,
								
								showDropdowns: true,
				}, function(start, end, label) {
					$('#popuploader').show();
					$.ajax({
						url:"{{URL::to('/agent/application/updateexpectwin')}}",
						method: "GET", // or POST
						dataType: "json",
						data: {from: start.format('YYYY-MM-DD'), appid: appliid},
						success:function(result) {
							$('#popuploader').hide();
							
						}
					});
				});
				
				$('.startdatepicker').daterangepicker({
				locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },
								singleDatePicker: true,
								
								showDropdowns: true,
				}, function(start, end, label) {
					$('#popuploader').show();
					$.ajax({
						url:"{{URL::to('/agent/application/updatedates')}}",
						method: "GET", // or POST
						dataType: "json",
						data: {from: start.format('YYYY-MM-DD'), appid: appliid, datetype: 'start'},
						success:function(result) {
							$('#popuploader').hide();
							var obj = result;
							if(obj.status){
								$('.app_start_date .month').html(obj.dates.month);
								$('.app_start_date .day').html(obj.dates.date);
								$('.app_start_date .year').html(obj.dates.year);
							}
							console.log("sent back -> do whatever you want now");
						}
					});
				});
				$('.enddatepicker').daterangepicker({
				locale: { format: "YYYY-MM-DD",cancelLabel: 'Clear' },
								singleDatePicker: true,
								
								showDropdowns: true,
				}, function(start, end, label) {
					$('#popuploader').show();
					$.ajax({
						url:"{{URL::to('/agent/application/updatedates')}}",
						method: "GET", // or POST
						dataType: "json",
						data: {from: start.format('YYYY-MM-DD'), appid: appliid, datetype: 'end'},
						success:function(result) {
							$('#popuploader').hide();
							var obj = result;
							if(obj.status){
								$('.app_end_date .month').html(obj.dates.month);
								$('.app_end_date .day').html(obj.dates.date);
								$('.app_end_date .year').html(obj.dates.year);
							}
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
	var type = $(this).attr('data-type'); 
	var typename = $(this).attr('data-typename'); 
	$('#create_checklist #checklistapp_id').val(id);
	$('#create_checklist #checklist_type').val(type);
	$('#create_checklist #checklist_typename').val(typename);
	$('#create_checklist').modal('show');
});
$(document).delegate('.openpaymentschedule', 'click', function(){
	var id = $(this).attr('data-id'); 
	//$('#create_apppaymentschedule #application_id').val(id);
	$('#addpaymentschedule').modal('show');
	$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/agent/addscheduleinvoicedetail')}}',
			type: 'GET',
			data: {id: $(this).attr('data-id')},
			success: function(res){
				$('.popuploader').hide();
				$('.showpoppaymentscheduledata').html(res);
				$(".datepicker").daterangepicker({
        locale: { format: "YYYY-MM-DD" },
        singleDatePicker: true,
        showDropdowns: true
      });
			}
		});
});

$(document).delegate('.addfee', 'click', function(){ 
	var clonedval = $('.feetypecopy').html();
	$('.fee_type_sec .fee_fields').append('<div class="fee_fields_row field_clone">'+clonedval+'</div>'); 
	 
});
$(document).delegate('.payremoveitems', 'click', function(){ 
		$(this).parent().parent().remove();
		schedulecalculatetotal();		
	});

	$(document).delegate('.payfee_amount', 'keyup', function(){ 
		schedulecalculatetotal();
	});
	$(document).delegate('.paydiscount', 'keyup', function(){ 
		schedulecalculatetotal();
	});
	
function schedulecalculatetotal(){
		var feeamount = 0;
		$('.payfee_amount').each(function(){
			if($(this).val() != ''){
				feeamount += parseFloat($(this).val());
			}
		});
		var discount = 0;
		if($('.paydiscount').val() != ''){
			 discount = $('.paydiscount').val();
		}
		var netfee = feeamount - parseFloat(discount);
		$('.paytotlfee').html(feeamount.toFixed(2));
		$('.paynetfeeamt').html(netfee.toFixed(2));
		
	}

$(document).delegate('.createaddapointment', 'click', function(){
	$('#create_appoint').modal('show');
});

$(document).delegate('.openfileupload', 'click', function(){
	var id = $(this).attr('data-id');
	var type = $(this).attr('data-type');
	var typename = $(this).attr('data-typename');
	var aid = $(this).attr('data-aid');
	$(".checklisttype").val(type); 
	$(".checklistid").val(id); 
	$(".checklisttypename").val(typename); 
	$(".application_id").val(aid); 
	$('#openfileuploadmodal').modal('show');
});

$(document).delegate('.opendocnote', 'click', function(){
	var id = '';
	var type = $(this).attr('data-app-type');
	var aid = $(this).attr('data-id');
	$(".checklisttype").val(type); 
	$(".checklistid").val(id); 
	$(".application_id").val(aid); 
	$('#openfileuploadmodal').modal('show');
});
$(document).delegate('.due_date_sec a.due_date_btn', 'click', function(){
	$('.due_date_sec .due_date_col').show();
	$(this).hide();
	$('.checklistdue_date').val(1);
});
$(document).delegate('.remove_col a.remove_btn', 'click', function(){
	$('.due_date_sec .due_date_col').hide();
	$('.due_date_sec a.due_date_btn').show();  
	$('.checklistdue_date').val(0);
});
	
$(document).delegate('.nextstage', 'click', function(){
	var appliid = $(this).attr('data-id');
	var stage = $(this).attr('data-stage');
	$('.popuploader').show();
	$.ajax({
		url: '{{URL::to('/agent/updatestage')}}',
		type:'GET',
		datatype:'json',
		data:{id:appliid, client_id:'{{$fetchedData->id}}'},
		success:function(response){
			$('.popuploader').hide();
			var obj = $.parseJSON(response);
			if(obj.status){
				$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
				$('.curerentstage').text(obj.stage);
				$('.progress-circle span').html(obj.width+' %');
				var over = '';
				if(obj.width > 50){
					over = '50';
				}
				$("#progresscir").removeClass();
				$("#progresscir").addClass('progress-circle');
				$("#progresscir").addClass('prgs_'+obj.width);
				$("#progresscir").addClass('over_'+over); 
				if(obj.displaycomplete){
				
					$('.completestage').show();
					$('.nextstage').hide();
				}
				$.ajax({
					url: site_url+'/agent/get-applications-logs',
					type:'GET',
					data:{clientid:'{{$fetchedData->id}}',id: appliid},
					success: function(responses){
						 
						$('#accordion').html(responses);
					}
				});
			}else{
				$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
			}
		}
	});
});

$(document).delegate('.acceptapplication', 'click', function(){
	var appliid = $(this).attr('data-id');

	$('.popuploader').show();
	$.ajax({
		url: '{{URL::to('/agent/completestage')}}',
		type:'GET',
		datatype:'json',
		data:{id:appliid, client_id:'{{$fetchedData->id}}'},
		success:function(response){
			$('.popuploader').hide();
			var obj = $.parseJSON(response);
			if(obj.status){
				$('.progress-circle span').html(obj.width+' %');
				var over = '';
				if(obj.width > 50){
					over = '50';
				}
				$("#progresscir").removeClass();
				$("#progresscir").addClass('progress-circle');
				$("#progresscir").addClass('prgs_'+obj.width);
				$("#progresscir").addClass('over_'+over); 
				$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
					$('.ifdiscont').hide();
					$('.revertapp').show();
				$('#confirmcompleteModal').modal('hide');
				$.ajax({
						url: site_url+'/agent/get-applications-logs',
						type:'GET',
						data:{clientid:'{{$fetchedData->id}}',id: appliid},
						success: function(responses){
							 
							$('#accordion').html(responses);
						}
					});
			}else{
				$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
			}
		}
	});
});

$(document).delegate('.backstage', 'click', function(){
	var appliid = $(this).attr('data-id');
	var stage = $(this).attr('data-stage');
	if(stage == 'Application'){
		
	}else{
		$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/agent/updatebackstage')}}',
			type:'GET',
			datatype:'json',
			data:{id:appliid, client_id:'{{$fetchedData->id}}'},
			success:function(response){
				var obj = $.parseJSON(response);
				$('.popuploader').hide();
				if(obj.status){
					$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
					$('.curerentstage').text(obj.stage);
					$('.progress-circle span').html(obj.width+' %');
				var over = '';
				if(obj.width > 50){
					over = '50';
				}
				$("#progresscir").removeClass();
				$("#progresscir").addClass('progress-circle');
		$("#progresscir").addClass('prgs_'+obj.width);
				$("#progresscir").addClass('over_'+over); 
					if(obj.displaycomplete == false){
						$('.completestage').hide();
						$('.nextstage').show();
					}
					$.ajax({
						url: site_url+'/agent/get-applications-logs',
						type:'GET',
						data:{clientid:'{{$fetchedData->id}}',id: appliid},
						success: function(responses){
							 
							$('#accordion').html(responses);
						}
					});
				}else{
					$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
				}
			}
		});
	}
});


$(document).delegate('#notes-tab', 'click', function(){
		var appliid = $(this).attr('data-id');
		$('.if_applicationdetail').hide();
		$('.ifapplicationdetailnot').show();
		$.ajax({
			url: '{{URL::to('/agent/getapplicationnotes')}}',
			type:'GET',
			data:{id:appliid},
			success:function(response){
				$('.popuploader').hide();
				$('#notes').html(response);
				
			}
		});
	});
	
	 $(".timezoneselects2").select2({
    dropdownParent: $("#create_appoint")
  }); 
  $(".timezoneselect2").select2({
    dropdownParent: $("#create_applicationappoint")
  });
  
  $('#attachments').on('change',function(){
       // output raw value of file input
      $('.showattachment').html(''); 

        // or, manipulate it further with regex etc.
        var filename = $(this).val().replace(/.*(\/|\\)/, '');
        // .. do your magic

       $('.showattachment').html(filename);
    });
	
	$(document).delegate('.opensuperagent', 'click', function(){
		var appid = $(this).attr('data-id');
		$('#superagent_application').modal('show');
		$('#superagent_application #siapp_id').val(appid);
	});
	
	$(document).delegate('.opentagspopup', 'click', function(){
		var appid = $(this).attr('data-id');
		$('#tags_clients').modal('show');
		$('#tags_clients #client_id').val(appid);
		$(".tagsselec").select2({
					dropdownParent: $("#tags_clients .modal-content")
				});
	});
	
	$(document).delegate('.opensubagent', 'click', function(){
		var appid = $(this).attr('data-id');
		$('#subagent_application').modal('show');
		$('#subagent_application #sbapp_id').val(appid);
	});
	
	$(document).delegate('.removesuperagent', 'click', function(){
		var appid = $(this).attr('data-id');
		
	});
	
	$(document).delegate('.application_ownership', 'click', function(){
		var appid = $(this).attr('data-id');
		var ration = $(this).attr('data-ration');
		$('#application_ownership #mapp_id').val(appid);
		$('#application_ownership .sus_agent').val($(this).attr('data-name'));
		$('#application_ownership .ration').val(ration);
		$('#application_ownership').modal('show');
		
	});
	
	$(document).delegate('.opensaleforcast', 'click', function(){
		var fapp_id = $(this).attr('data-id');
		var client_revenue = $(this).attr('data-client_revenue');
		var partner_revenue = $(this).attr('data-partner_revenue');
		var discounts = $(this).attr('data-discounts');
		$('#application_opensaleforcast #fapp_id').val(fapp_id);
		$('#application_opensaleforcast #client_revenue').val(client_revenue);
		$('#application_opensaleforcast #partner_revenue').val(partner_revenue);
		$('#application_opensaleforcast #discounts').val(discounts);
		$('#application_opensaleforcast').modal('show');
		
	});
	
	$(document).delegate('.openpaymentfee', 'click', function(){
		var appliid = $(this).attr('data-id');
		$('.popuploader').show(); 
		$('#new_fee_option').modal('show');
		$.ajax({
			url: '{{URL::to('/agent/showproductfee')}}',
			type:'GET',
			data:{id:appliid},
			success:function(response){
				$('.popuploader').hide(); 
				
				$('.showproductfee').html(response);
				
			}
		});
	});
	
	$(document).delegate('.openpaymentfeeserv', 'click', function(){
		var appliid = $(this).attr('data-id');
		$('.popuploader').show(); 
		$('#interest_service_view').modal('hide');
		$('#new_fee_option_serv').modal('show');
		$.ajax({
			url: '{{URL::to('/agent/showproductfeeserv')}}',
			type:'GET',
			data:{id:appliid},
			success:function(response){
				$('.popuploader').hide(); 
				
				$('.showproductfeeserv').html(response);
				
			}
		});
		$(document).on("hidden.bs.modal", "#interest_service_view", function (e) {
		$('body').addClass('modal-open');
	});
	});
	
	$(document).delegate('.opensaleforcastservice', 'click', function(){
		var fapp_id = $(this).attr('data-id');
		var client_revenue = $(this).attr('data-client_revenue');
		var partner_revenue = $(this).attr('data-partner_revenue');
		var discounts = $(this).attr('data-discounts');
		$('#application_opensaleforcastservice #fapp_id').val(fapp_id);
		$('#application_opensaleforcastservice #client_revenue').val(client_revenue);
		$('#application_opensaleforcastservice #partner_revenue').val(partner_revenue);
		$('#application_opensaleforcastservice #discounts').val(discounts);
	$('#interest_service_view').modal('hide');
		$('#application_opensaleforcastservice').modal('show');
		
	});
	
	$(document).delegate('.closeservmodal', 'click', function(){
		
		$('#interest_service_view').modal('hide');
		$('#application_opensaleforcastservice').modal('hide');
		
	});
	$(document).on("hidden.bs.modal", "#application_opensaleforcastservice", function (e) {
		$('body').addClass('modal-open');
	});
	$(document).delegate('#new_fee_option .fee_option_addbtn a', 'click', function(){
		var html = '<tr class="add_fee_option cus_fee_option"><td><select data-valid="required" class="form-control course_fee_type" name="course_fee_type[]"><option value="">Select Type</option><option value="Accommodation Fee">Accommodation Fee</option><option value="Administration Fee">Administration Fee</option><option value="Airline Ticket">Airline Ticket</option><option value="Airport Transfer Fee">Airport Transfer Fee</option><option value="Application Fee">Application Fee</option><option value="Bond">Bond</option></select></td><td><input type="number" value="0" class="form-control semester_amount" name="semester_amount[]"></td><td><input type="number" value="1" class="form-control no_semester" name="no_semester[]"></td><td class="total_fee"><span>0.00</span><input type="hidden"  class="form-control total_fee_am" value="0" name="total_fee[]"></td><td><input type="number" value="1" class="form-control claimable_terms" name="claimable_semester[]"></td><td><input type="number" class="form-control commission" name="commission[]"></td><td> <a href="javascript:;" class="removefeetype"><i class="fa fa-trash"></i></a></td></tr>';
		$('#new_fee_option #productitemview tbody').append(html);
				
			}); 
			
	$(document).delegate('#new_fee_option .removefeetype', 'click', function(){
		$(this).parent().parent().remove();
		
		var price = 0;
		$('#new_fee_option .total_fee_am').each(function(){
			price += parseFloat($(this).val());
		});
		
		var discount_sem = $('.discount_sem').val();
		var discount_amount = $('.discount_amount').val();
		var cservd = 0.00;
		if(discount_sem != ''){
			cservd = discount_sem;
		}
		
		var cservs = 0.00;
		if(discount_amount != ''){
			cservs = discount_amount;
		}
		var dis = parseFloat(cservs) * parseFloat(cservd);
		var duductdis = price - dis;
		
		$('#new_fee_option .net_totl').html(duductdis.toFixed(2));
	});
	
	
	$(document).delegate('#new_fee_option .semester_amount','keyup', function(){
		var installment_amount = $(this).val();
		var cserv = 0.00;
		if(installment_amount != ''){
			cserv = installment_amount;
		}
		
		var installment = $(this).parent().parent().find('.no_semester').val();
		
		var totalamount = parseFloat(cserv) * parseInt(installment);
		$(this).parent().parent().find('.total_fee span').html(totalamount.toFixed(2));
		$(this).parent().parent().find('.total_fee_am').val(totalamount.toFixed(2));
		var price = 0;
		$('#new_fee_option .total_fee_am').each(function(){
			price += parseFloat($(this).val());
		});
		
		var discount_sem = $('.discount_sem').val();
		var discount_amount = $('.discount_amount').val();
		var cservd = 0.00;
		if(discount_sem != ''){
			cservd = discount_sem;
		}
		
		var cservs = 0.00;
		if(discount_amount != ''){
			cservs = discount_amount;
		}
		var dis = parseFloat(cservs) * parseFloat(cservd);
		var duductdis = price - dis;
		
		$('#new_fee_option .net_totl').html(duductdis.toFixed(2));
	});
	
	
	$(document).delegate('#new_fee_option .no_semester','keyup', function(){
		var installment = $(this).val();
		
		
		var installment_amount = $(this).parent().parent().find('.semester_amount').val();
		var cserv = 0.00;
		if(installment_amount != ''){
			cserv = installment_amount;
		}
		var totalamount = parseFloat(cserv) * parseInt(installment);
		$(this).parent().parent().find('.total_fee span').html(totalamount.toFixed(2));
		$(this).parent().parent().find('.total_fee_am').val(totalamount.toFixed(2));
		var price = 0;
		$('#new_fee_option .total_fee_am').each(function(){
			price += parseFloat($(this).val());
		});
		
		var discount_sem = $('.discount_sem').val();
		var discount_amount = $('.discount_amount').val();
		var cservd = 0.00;
		if(discount_sem != ''){
			cservd = discount_sem;
		}
		
		var cservs = 0.00;
		if(discount_amount != ''){
			cservs = discount_amount;
		}
		var dis = parseFloat(cservs) * parseFloat(cservd);
		var duductdis = price - dis;
		
		$('#new_fee_option .net_totl').html(duductdis.toFixed(2));
	});
	
	$(document).delegate('#new_fee_option .discount_amount','keyup', function(){
		var discount_amount = $(this).val();
		var discount_sem = $('.discount_sem').val();
		var cserv = 0.00;
		if(discount_sem != ''){
			cserv = discount_sem;
		}
		
		var cservs = 0.00;
		if(discount_amount != ''){
			cservs = discount_amount;
		}
		var dis = parseFloat(cservs) * parseFloat(cserv);
		$('.totaldis span').html(dis.toFixed(2));
		var price = 0;
		$('#new_fee_option .total_fee_am').each(function(){
			price += parseFloat($(this).val());
		});
		var duductdis = price - dis;
		$('#new_fee_option .net_totl').html(duductdis.toFixed(2));
		$('.totaldis .total_dis_am').val(dis.toFixed(2));
		
	});
	
	$(document).delegate('#new_fee_option .discount_sem','keyup', function(){
		var discount_sem = $(this).val();
		var discount_amount = $('.discount_amount').val();
		var cserv = 0.00;
		if(discount_sem != ''){
			cserv = discount_sem;
		}
		
		var cservs = 0.00;
		if(discount_amount != ''){
			cservs = discount_amount;
		}
		var dis = parseFloat(cservs) * parseFloat(cserv);
		$('.totaldis span').html(dis.toFixed(2));
		$('.totaldis .total_dis_am').val(dis.toFixed(2));
		
		var price = 0;
		$('#new_fee_option .total_fee_am').each(function(){
			price += parseFloat($(this).val());
		});
		var duductdis = price - dis;
		$('#new_fee_option .net_totl').html(duductdis.toFixed(2));
		
	});
	
	$(document).delegate('.editpaymentschedule', 'click', function(){ 
		$('#editpaymentschedule').modal('show');
		$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/agent/scheduleinvoicedetail')}}',
			type: 'GET',
			data: {id: $(this).attr('data-id'),t:'application'},
			success: function(res){
				$('.popuploader').hide();
				$('.showeditmodule').html(res);
				$(".editclientname").select2({
					dropdownParent: $("#editpaymentschedule .modal-content")
				}); 
				
				$(".datepicker").daterangepicker({
        locale: { format: "YYYY-MM-DD" },
        singleDatePicker: true,
        showDropdowns: true
      });
			}
		});
	});
	
});
$(document).ready(function() {
        $(document).delegate("#ddArea", "dragover", function() {
          $(this).addClass("drag_over");
          return false;
        });

        $(document).delegate("#ddArea", "dragleave", function() {
          $(this).removeClass("drag_over");
          return false;
        });

        $(document).delegate("#ddArea", "click", function(e) {
          file_explorer();
        });

        $(document).delegate("#ddArea", "drop", function(e) {
          e.preventDefault();
          $(this).removeClass("drag_over");
          var formData = new FormData();
          var files = e.originalEvent.dataTransfer.files;
          for (var i = 0; i < files.length; i++) {
            formData.append("file[]", files[i]);
          }
          uploadFormData(formData);
        });

        function file_explorer() {
          document.getElementById("selectfile").click();
          document.getElementById("selectfile").onchange = function() {
            files = document.getElementById("selectfile").files;
            var formData = new FormData();

            for (var i = 0; i < files.length; i++) {
              formData.append("file[]", files[i]);
            }
			formData.append("type", $('.checklisttype').val());
			formData.append("typename", $('.checklisttypename').val());
			formData.append("id", $('.checklistid').val());
			formData.append("application_id", $('.application_id').val());
			
            uploadFormData(formData);
          };
        }

        function uploadFormData(form_data) {
        $('.popuploader').show(); 
          $.ajax({
            url: "{{URL::to('/agent/application/checklistupload')}}",
            method: "POST",
            data: form_data,
            datatype: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function(response) {
				var obj = $.parseJSON(response);
              $('.popuploader').hide(); 
              $('#openfileuploadmodal').modal('hide');
              $('.mychecklistdocdata').html(obj.doclistdata);
              $('.checklistuploadcount').html(obj.applicationuploadcount);
			  $('.'+obj.type+'_checklists').html(obj.checklistdata);
			   $('#selectfile').val(''); 
            }
          });
        }
		
		
	$(document).delegate('#new_fee_option_serv .fee_option_addbtn a', 'click', function(){
		var html = '<tr class="add_fee_option cus_fee_option"><td><select data-valid="required" class="form-control course_fee_type" name="course_fee_type[]"><option value="">Select Type</option><option value="Accommodation Fee">Accommodation Fee</option><option value="Administration Fee">Administration Fee</option><option value="Airline Ticket">Airline Ticket</option><option value="Airport Transfer Fee">Airport Transfer Fee</option><option value="Application Fee">Application Fee</option><option value="Bond">Bond</option></select></td><td><input type="number" value="0" class="form-control semester_amount" name="semester_amount[]"></td><td><input type="number" value="1" class="form-control no_semester" name="no_semester[]"></td><td class="total_fee"><span>0.00</span><input type="hidden"  class="form-control total_fee_am" value="0" name="total_fee[]"></td><td><input type="number" value="1" class="form-control claimable_terms" name="claimable_semester[]"></td><td><input type="number" class="form-control commission" name="commission[]"></td><td> <a href="javascript:;" class="removefeetype"><i class="fa fa-trash"></i></a></td></tr>';
		$('#new_fee_option_serv #productitemview tbody').append(html);
				
			}); 
			
	$(document).delegate('#new_fee_option_serv .removefeetype', 'click', function(){
		$(this).parent().parent().remove();
		
		var price = 0;
		$('#new_fee_option_serv .total_fee_am').each(function(){
			price += parseFloat($(this).val());
		});
		
		var discount_sem = $('#new_fee_option_serv .discount_sem').val();
		var discount_amount = $('#new_fee_option_serv .discount_amount').val();
		var cservd = 0.00;
		if(discount_sem != ''){
			cservd = discount_sem;
		}
		
		var cservs = 0.00;
		if(discount_amount != ''){
			cservs = discount_amount;
		}
		var dis = parseFloat(cservs) * parseFloat(cservd);
		var duductdis = price - dis;
		
		$('#new_fee_option_serv .net_totl').html(duductdis.toFixed(2));
	});
	
	
	$(document).delegate('#new_fee_option_serv .semester_amount','keyup', function(){
		var installment_amount = $(this).val();
		var cserv = 0.00;
		if(installment_amount != ''){
			cserv = installment_amount;
		}
		
		var installment = $(this).parent().parent().find('.no_semester').val();
		
		var totalamount = parseFloat(cserv) * parseInt(installment);
		$(this).parent().parent().find('.total_fee span').html(totalamount.toFixed(2));
		$(this).parent().parent().find('.total_fee_am').val(totalamount.toFixed(2));
		var price = 0;
		$('#new_fee_option_serv .total_fee_am').each(function(){
			price += parseFloat($(this).val());
		});
		
		var discount_sem = $('#new_fee_option_serv .discount_sem').val();
		var discount_amount = $('#new_fee_option_serv .discount_amount').val();
		var cservd = 0.00;
		if(discount_sem != ''){
			cservd = discount_sem;
		}
		
		var cservs = 0.00;
		if(discount_amount != ''){
			cservs = discount_amount;
		}
		var dis = parseFloat(cservs) * parseFloat(cservd);
		var duductdis = price - dis;
		
		$('#new_fee_option_serv .net_totl').html(duductdis.toFixed(2));
	});
	
	
	$(document).delegate('#new_fee_option_serv .no_semester','keyup', function(){
		var installment = $(this).val();
		
		
		var installment_amount = $(this).parent().parent().find('.semester_amount').val();
		var cserv = 0.00;
		if(installment_amount != ''){
			cserv = installment_amount;
		}
		var totalamount = parseFloat(cserv) * parseInt(installment);
		$(this).parent().parent().find('.total_fee span').html(totalamount.toFixed(2));
		$(this).parent().parent().find('.total_fee_am').val(totalamount.toFixed(2));
		var price = 0;
		$('#new_fee_option_serv .total_fee_am').each(function(){
			price += parseFloat($(this).val());
		});
		
		var discount_sem = $('.discount_sem').val();
		var discount_amount = $('.discount_amount').val();
		var cservd = 0.00;
		if(discount_sem != ''){
			cservd = discount_sem;
		}
		
		var cservs = 0.00;
		if(discount_amount != ''){
			cservs = discount_amount;
		}
		var dis = parseFloat(cservs) * parseFloat(cservd);
		var duductdis = price - dis;
		
		$('#new_fee_option_serv .net_totl').html(duductdis.toFixed(2));
	});
	
	$(document).delegate('#new_fee_option_serv .discount_amount','keyup', function(){
		var discount_amount = $(this).val();
		var discount_sem = $('#new_fee_option_serv .discount_sem').val();
		var cserv = 0.00;
		if(discount_sem != ''){
			cserv = discount_sem;
		}
		
		var cservs = 0.00;
		if(discount_amount != ''){
			cservs = discount_amount;
		}
		var dis = parseFloat(cservs) * parseFloat(cserv);
		$('#new_fee_option_serv .totaldis span').html(dis.toFixed(2));
		var price = 0;
		$('#new_fee_option_serv .total_fee_am').each(function(){
			price += parseFloat($(this).val());
		});
		var duductdis = price - dis;
		$('#new_fee_option_serv .net_totl').html(duductdis.toFixed(2));
		$('#new_fee_option_serv .totaldis .total_dis_am').val(dis.toFixed(2));
		
	});
	
	$(document).delegate('#new_fee_option_serv .discount_sem','keyup', function(){
		var discount_sem = $(this).val();
		var discount_amount = $('#new_fee_option_serv .discount_amount').val();
		var cserv = 0.00;
		if(discount_sem != ''){
			cserv = discount_sem;
		}
		
		var cservs = 0.00;
		if(discount_amount != ''){
			cservs = discount_amount;
		}
		var dis = parseFloat(cservs) * parseFloat(cserv);
		$('#new_fee_option_serv .totaldis span').html(dis.toFixed(2));
		$('#new_fee_option_serv .totaldis .total_dis_am').val(dis.toFixed(2));
		
		var price = 0;
		$('#new_fee_option_serv .total_fee_am').each(function(){
			price += parseFloat($(this).val());
		});
		var duductdis = price - dis;
		$('#new_fee_option_serv .net_totl').html(duductdis.toFixed(2));
		
	});	
});
</script>
<div class="modal fade custom_modal" id="application_opensaleforcast" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"> 
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Sales Forecast</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
									<?php $sagents = \App\Agent::whereRaw('FIND_IN_SET("Super Agent", agent_type)')->get(); ?>
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
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
									<?php $sagents = \App\Agent::whereRaw('FIND_IN_SET("Sub Agent", agent_type)')->where('is_acrchived',0)->get(); ?>
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
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/agent/save_tag')}}" name="stags_application" id="stags_application" autocomplete="off" enctype="multipart/form-data">
				@csrf 
				<input type="hidden" name="client_id" id="client_id" value="">
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
									<?php $stagd = \App\Tag::where('id','!=','')->get(); ?>
									@foreach($stagd as $sa)
										<option <?php if(in_array($sa->id, $r)){ echo 'selected'; } ?> value="{{$sa->id}}">{{$sa->name}}</option>
									@endforeach
								</select>
								
							</div>
						</div>
						
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('stags_application')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showproductfee">
				 
			</div>
		</div>
	</div>
</div> 


<div class="modal fade custom_modal" id="new_fee_option_serv" tabindex="-1" role="dialog" aria-labelledby="feeoptionModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="feeoptionModalLabel">Fee Option</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showproductfeeserv">
				 
			</div>
		</div>
	</div>
</div> 

<div class="modal fade custom_modal" id="application_opensaleforcast" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"> 
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Sales Forecast</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form> 
			</div>
		</div>
	</div>
</div>
@endsection