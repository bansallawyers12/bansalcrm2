@extends('layouts.admin')
@section('title', 'Partner Detail')

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
.zippyLabel{background-color: #e8e8e8; line-height: 1;display: inline-block;color: rgba(0,0,0,.6);font-weight: 700; border: 0 solid transparent; font-size: 10px;padding: 3px; }
.accordion .accordion-header.app_green{background-color: #54b24b;color: #fff;}
.accordion .accordion-header.app_green .accord_hover a{color: #fff!important;}
.accordion .accordion-header.app_blue{background-color: rgba(3,169,244,.1);color: #03a9f4;}
.buttons-excel {margin-top: 28px;}
/* Export buttons styling */
.dt-buttons {
    margin-bottom: 15px;
}
.dt-buttons .btn {
    margin-right: 5px;
    margin-bottom: 5px;
}
.dt-buttons .btn i {
    margin-right: 5px;
}
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
							<h4>Partner Detail</h4>
							<div class="card-header-action">
                              
								<a href="{{route('partners.index')}}" class="btn btn-primary">Partner List</a>
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
							<span class="author-avtar" style="background: rgb(68, 182, 174);"><b>{{substr($fetchedData->partner_name, 0, 1)}}</b></span>
								<div class="clearfix"></div>
								<div class="author-box-name"> 
									<a href="#">{{$fetchedData->partner_name}}</a>
								</div>
								<div class="author-mail_sms">
									<a href="#" title="Compose SMS"><i class="fas fa-comment-alt"></i></a>
									<a href="javascript:;" data-id="{{@$fetchedData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->partner_name}}" class="clientemail" title="Compose Mail"><i class="fa fa-envelope"></i></a> 
									<a href="{{URL::to('/partners/edit/'.base64_encode(convert_uuencode(@$fetchedData->id)))}}" title="Edit"><i class="fa fa-edit"></i></a>
									
									@if($fetchedData->is_archived == 0)
										<a class="arcivedval" href="javascript:;" onclick="arcivedAction({{$fetchedData->id}}, 'partners')" title="Archive"><i class="fas fa-archive"></i></a>
									@else
										<a class="arcivedval" style="background-color:red;" href="javascript:;" onclick="arcivedAction({{$fetchedData->id}}, 'partners')" title="UnArchive"><i style="color: #fff;" class="fas fa-archive"></i></a>
									@endif
								</div>
							</div>
                            
                            <p><button type="button" style="border-radius:30px;" class="btn btn-primary btn-block openpartneraction" title="Actions"> Action</button></p>
                        
                            
						</div>
					</div>
					<div class="card">
						<div class="card-header">
							<h4>General Information</h4>
						</div>
						<div class="card-body">
							<p class="clearfix"> 
								<span class="float-start">Phone No:</span>
								<span class="float-end text-muted">
                                    {{--$fetchedData->phone--}}
                                    <?php
                                    if( \App\Models\PartnerPhone::where('partner_id', $fetchedData->id)->exists()) {
                                        $partnerContacts = \App\Models\PartnerPhone::select('partner_phone','partner_country_code','partner_phone_type')->where('partner_id', $fetchedData->id)->get();
                                    } else {
                                        if( \App\Models\Partner::where('id', $fetchedData->id)->exists()){
                                            $partnerContacts = \App\Models\Partner::select('phone as partner_phone','country_code as partner_country_code')->where('id', $fetchedData->id)->get();
                                        } else {
                                            $partnerContacts = array();
                                        }
                                    }
                                    //dd($partnerContacts);
                                    if( !empty($partnerContacts) && count($partnerContacts)>0 ){
                                        $phonenoStr = "";
                                        foreach($partnerContacts as $conKey=>$conVal){
                                            if( isset($conVal->partner_country_code) && $conVal->partner_country_code != "" ){
                                                $partner_country_code = $conVal->partner_country_code;
                                            } else {
                                                $partner_country_code = "";
                                            }

                                            if( isset($conVal->partner_phone_type) && $conVal->partner_phone_type != "" ){
                                                if( isset($conVal->partner_phone_type) && $conVal->partner_phone_type != "Not In Use" ){
                                                    $phonenoStr .= $partner_country_code."".$conVal->partner_phone.'('.$conVal->partner_phone_type .')<br/>';
                                                }
                                            } else {
                                                $phonenoStr .= $partner_country_code."".$conVal->partner_phone."<br/>";
                                            }
                                        }
                                        echo $phonenoStr;
                                    } else {
                                        echo "N/A";
                                    }?>
                                </span>
							</p>
							<p class="clearfix"> 
								<span class="float-start">Fax:</span>
								<span class="float-end text-muted">{{$fetchedData->fax}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-start">Email:</span>
								<span class="float-end text-muted">
                                    {{--$fetchedData->email--}}
                                    <?php
                                    if( \App\Models\PartnerEmail::where('partner_id', $fetchedData->id)->exists()) {
                                        $partnerEmails = \App\Models\PartnerEmail::select('partner_email','partner_email_type')->where('partner_id', $fetchedData->id)->get();
                                    } else {
                                        if( \App\Models\Partner::where('id', $fetchedData->id)->exists()){
                                            $partnerEmails = \App\Models\Partner::select('email as partner_email')->where('id', $fetchedData->id)->get();
                                        } else {
                                            $partnerEmails = array();
                                        }
                                    }
                                    if( !empty($partnerEmails) && count($partnerEmails)>0 ){
                                        $emailStr = "";
                                        foreach($partnerEmails as $emailKey=>$emailVal){
                                            if( isset($emailVal->partner_email_type) && $emailVal->partner_email_type != "" ){
                                                if( isset($emailVal->partner_email_type) && $emailVal->partner_email_type != "Not In Use" ){
                                                    $emailStr .= $emailVal->partner_email.'('.$emailVal->partner_email_type .')<br/>';
                                                }
                                            } else {
                                                $emailStr .= $emailVal->partner_email."<br/>";
                                            }
                                        }
                                        echo $emailStr;
                                    } else {
                                        echo "N/A";
                                    }?>
                                </span>
							</p>
							<p class="clearfix"> 
								<span class="float-start">Address:</span>
								<span class="float-end text-muted">{{$fetchedData->address}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-start">Website:</span>
								<span class="float-end text-muted">{{$fetchedData->website}}</span>
							</p>
							<?php
						
							$workflows = \App\Models\Workflow::where('id', $fetchedData->service_workflow)->first();
							?>
							
							<p class="clearfix"> 
								<span class="float-start">Services:</span>
								<span class="float-end text-muted">{{@$workflows->name}}</span>
							</p>
							
							<p class="clearfix"> 
								<span class="float-start">Added On:</span>
								<span class="float-end text-muted">{{date('d/m/Y', strtotime($fetchedData->created_at))}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-start">Business Registration Number:</span>
								<span class="float-end text-muted">{{$fetchedData->business_reg_no}}</span>
							</p>
							<p class="clearfix"> 
								<span class="float-start">Currency code:</span>
								<span class="float-end text-muted">{{$fetchedData->currency}}</span>
							</p>
							
						</div>
					</div>
				</div>
				<div class="col-9 col-md-9 col-lg-9">
					<div class="card">
						<div class="card-body">
							@php
								$allowedTabs = [
									'application',
									'partner-activities',
									'products',
									'branches',
									'agreements',
									'contacts',
									'noteterm',
									'documents',
									'accounts',
									'conversations',
									'promotions',
									'student',
									'invoice',
									'email-v2'
								];
								$tabAliases = [
									'activities' => 'partner-activities',
									'notestrm' => 'noteterm'
								];
								$allowedTabSlugs = array_unique(array_merge($allowedTabs, array_keys($tabAliases)));
								$requestedTab = Request::route('tab') ?? Request::get('tab');
								if (empty($requestedTab) || !in_array($requestedTab, $allowedTabSlugs, true)) {
									$requestedTab = 'application';
								}
								$activeTab = $tabAliases[$requestedTab] ?? $requestedTab;
								$partnerId = base64_encode(convert_uuencode($fetchedData->id));
							@endphp
							<ul class="nav nav-pills" id="partner_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link {{ $activeTab === 'application' ? 'active' : '' }}" href="{{route('partners.detail', ['id' => $partnerId])}}" id="application-tab" role="tab" aria-controls="application" aria-selected="{{ $activeTab === 'application' ? 'true' : 'false' }}">Applications</a>
								</li>
                              
                                <li class="nav-item">
									<a class="nav-link {{ $activeTab === 'partner-activities' ? 'active' : '' }}" href="{{route('partners.detail', ['id' => $partnerId, 'tab' => 'activities'])}}" id="partner-activities-tab" role="tab" aria-controls="partner-activities" aria-selected="{{ $activeTab === 'partner-activities' ? 'true' : 'false' }}">Activities</a>
                                </li>
                              
								<li class="nav-item">
									<a class="nav-link {{ $activeTab === 'products' ? 'active' : '' }}" href="{{route('partners.detail', ['id' => $partnerId, 'tab' => 'products'])}}" id="products-tab" role="tab" aria-controls="products" aria-selected="{{ $activeTab === 'products' ? 'true' : 'false' }}">Products</a>
								</li>
								<li class="nav-item">
									<a class="nav-link {{ $activeTab === 'branches' ? 'active' : '' }}" href="{{route('partners.detail', ['id' => $partnerId, 'tab' => 'branches'])}}" id="branches-tab" role="tab" aria-controls="branches" aria-selected="{{ $activeTab === 'branches' ? 'true' : 'false' }}">Branches</a>
								</li>
								<li class="nav-item">
									<a class="nav-link {{ $activeTab === 'agreements' ? 'active' : '' }}" href="{{route('partners.detail', ['id' => $partnerId, 'tab' => 'agreements'])}}" id="agreements-tab" role="tab" aria-controls="agreements" aria-selected="{{ $activeTab === 'agreements' ? 'true' : 'false' }}">Agreements</a>
								</li>
								<li class="nav-item">
									<a class="nav-link {{ $activeTab === 'contacts' ? 'active' : '' }}" href="{{route('partners.detail', ['id' => $partnerId, 'tab' => 'contacts'])}}" id="contacts-tab" role="tab" aria-controls="contacts" aria-selected="{{ $activeTab === 'contacts' ? 'true' : 'false' }}">Contacts</a>
								</li>
								<li class="nav-item">
									<a class="nav-link {{ $activeTab === 'noteterm' ? 'active' : '' }}" href="{{route('partners.detail', ['id' => $partnerId, 'tab' => 'notestrm'])}}" id="noteterm-tab" role="tab" aria-controls="noteterm" aria-selected="{{ $activeTab === 'noteterm' ? 'true' : 'false' }}">Notes & Terms</a>
								</li>
								<li class="nav-item">
									<a class="nav-link {{ $activeTab === 'documents' ? 'active' : '' }}" href="{{route('partners.detail', ['id' => $partnerId, 'tab' => 'documents'])}}" id="documents-tab" role="tab" aria-controls="documents" aria-selected="{{ $activeTab === 'documents' ? 'true' : 'false' }}">Documents</a>
								</li>
								<li class="nav-item">
									{{-- <a class="nav-link" data-bs-toggle="tab" id="appointments-tab" href="#appointments" role="tab" aria-controls="appointments" aria-selected="false">Appointments</a> --}}
								</li>
								<li class="nav-item">
									<a class="nav-link {{ $activeTab === 'accounts' ? 'active' : '' }}" href="{{route('partners.detail', ['id' => $partnerId, 'tab' => 'accounts'])}}" id="accounts-tab" role="tab" aria-controls="accounts" aria-selected="{{ $activeTab === 'accounts' ? 'true' : 'false' }}">Accounts</a>
								</li>
								<li class="nav-item">
									<a class="nav-link {{ $activeTab === 'conversations' ? 'active' : '' }}" href="{{route('partners.detail', ['id' => $partnerId, 'tab' => 'conversations'])}}" id="conversations-tab" role="tab" aria-controls="conversations" aria-selected="{{ $activeTab === 'conversations' ? 'true' : 'false' }}">Conversations</a>
								</li>
								<li class="nav-item">
									<a class="nav-link {{ $activeTab === 'email-v2' ? 'active' : '' }}" href="{{route('partners.detail', ['id' => $partnerId, 'tab' => 'email-v2'])}}" id="email-v2-tab" role="tab" aria-controls="email-v2" aria-selected="{{ $activeTab === 'email-v2' ? 'true' : 'false' }}">Emails</a>
								</li>
								{{-- Task system removed - December 2025 --}}
								<!--<li class="nav-item">
									<a class="nav-link" data-bs-toggle="tab" id="tasks-tab" href="#tasks" role="tab" aria-controls="tasks" aria-selected="false">Tasks</a>
								</li>-->
								<li class="nav-item">
									<a class="nav-link {{ $activeTab === 'promotions' ? 'active' : '' }}" href="{{route('partners.detail', ['id' => $partnerId, 'tab' => 'promotions'])}}" id="promotions-tab" role="tab" aria-controls="promotions" aria-selected="{{ $activeTab === 'promotions' ? 'true' : 'false' }}">Promotions</a>
								</li>
                              
                                <li class="nav-item">
									<a class="nav-link {{ $activeTab === 'student' ? 'active' : '' }}" href="{{route('partners.detail', ['id' => $partnerId, 'tab' => 'student'])}}" id="student-tab" role="tab" aria-controls="student" aria-selected="{{ $activeTab === 'student' ? 'true' : 'false' }}">Student</a>
								</li>
                              
                                <li class="nav-item">
									<a class="nav-link {{ $activeTab === 'invoice' ? 'active' : '' }}" href="{{route('partners.detail', ['id' => $partnerId, 'tab' => 'invoice'])}}" id="invoice-tab" role="tab" aria-controls="invoice" aria-selected="{{ $activeTab === 'invoice' ? 'true' : 'false' }}">Invoice</a>
								</li>
							</ul> 
							<div class="tab-content" id="partnerContent" style="padding-top:15px;">
								<div class="tab-pane fade {{ $activeTab === 'application' ? 'show active' : '' }}" id="application" role="tabpanel" aria-labelledby="application-tab">
									

									<?php
									$appprogresscount = \App\Models\Application::where('partner_id', $fetchedData->id)->where('status',0)->count();
									$appcompletecount = \App\Models\Application::where('partner_id', $fetchedData->id)->where('status',1)->count();
									$appdisccount = \App\Models\Application::where('partner_id', $fetchedData->id)->where('status',2)->count();
									$appenrolcount = \App\Models\Application::where('partner_id', $fetchedData->id)->where('status',3)->count();
									?>
									<div class="row">
										<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
											<div class="card">
												<div class="card-statistic-4">
													<div class="align-items-center justify-content-between">
														<div class="card-content">
															<h5 class="font-13">IN PROGRESS</h5>
															<h2 class="mb-3 font-18">{{$appprogresscount}}</h2>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
											<div class="card">
												<div class="card-statistic-4">
													<div class="align-items-center justify-content-between">
														<div class="card-content">
															<h5 class="font-13">COMPLETED</h5>
															<h2 class="mb-3 font-18">{{$appcompletecount}}</h2>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
											<div class="card">
												<div class="card-statistic-4">
													<div class="align-items-center justify-content-between">
														<div class="card-content">
															<h5 class="font-13">DISCONTINUED</h5>
															<h2 class="mb-3 font-18">{{$appdisccount}}</h2>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
											<div class="card">
												<div class="card-statistic-4">
													<div class="align-items-center justify-content-between">
														<div class="card-content">
															<h5 class="font-13">ENROLLED</h5>
															<h2 class="mb-3 font-18">{{$appenrolcount}}</h2>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>	
									 
									<div class="row">
										<div class="col-12 col-sm-12 col-lg-12">
											<div class="card">
												<div class="card-body">
													<div class="summary">
														<div class="summary-chart active" data-tab-group="summary-tab" id="summary-chart">
															<canvas id="myChart" height="180"></canvas>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="table-responsive if_applicationdetail"> 
										<table class="table text_wrap table-2">
											<thead>
												<tr>
													<th>Name</th>
													<th>Assignee</th>
													<th>Product Name</th>
													<th>Workflow</th>
													<th>Current Stage</th>
													<th>Status</th>
													<th>Added On</th>
													<th>Last Updated</th>
													
												</tr> 
											</thead>
											<tbody class="applicationtdata">
											<?php
											 foreach(\App\Models\Application::where('partner_id', $fetchedData->id)->orderby('created_at','Desc')->get() as $alist){
												$productdetail = \App\Models\Product::where('id', $alist->product_id)->first();
				$partnerdetail = \App\Models\Admin::where('id', $alist->client_id)->first();
				$PartnerBranch = \App\Models\PartnerBranch::where('id', $alist->branch)->first();
				$workflow = \App\Models\Workflow::where('id', $alist->workflow)->first();
												?>
												<tr id="id_{{$alist->id}}">
													<td><a href="{{URL::to('/clients/detail/'.base64_encode(convert_uuencode(@$partnerdetail->id)))}}">{{$partnerdetail->first_name}} {{$partnerdetail->last_name}}</a></td>
													<td></td>
													<td><a href="{{URL::to('clients/detail/')}}/{{base64_encode(convert_uuencode(@$partnerdetail->id))}}?tab=application&appid={{@$alist->id}}">{{$productdetail->name}}</a></td>
													<td>{{$workflow->name}}</td>
													<td>{{$alist->stage}}</td>
													<td>
													@if($alist->status == 0)
														<span class="badge badge-info" style="margin-top: 5px;margin-bottom:5px;">In Progress</span>
													@elseif($alist->status == 1)
														<span class="badge badge-success" style="margin-top: 5px;margin-bottom:5px;">Completed</span>
													@elseif($alist->status == 2)
														<span class="badge badge-success" style="margin-top: 5px;margin-bottom:5px;">Discontinued</span>
                                                    @elseif($alist->status == 3)
                                                        <span class="badge badge-success" style="margin-top: 5px;margin-bottom:5px;">Cancelled</span>
                                                    @elseif($alist->status == 4)
                                                        <span class="badge badge-success" style="margin-top: 5px;margin-bottom:5px;">Withdrawn</span>
                                                    @elseif($alist->status == 5)
                                                        <span class="badge badge-success" style="margin-top: 5px;margin-bottom:5px;">Deferred</span>
                                                    @elseif($alist->status == 6)
                                                        <span class="badge badge-success" style="margin-top: 5px;margin-bottom:5px;">Future</span>
                                                    @elseif($alist->status == 7)
                                                        <span class="badge badge-success" style="margin-top: 5px;margin-bottom:5px;">VOE</span>
													@endif
													</td>
											 		<td>{{date('d/m/Y', strtotime($alist->created_at))}}</td>
											 		<td>{{date('d/m/Y', strtotime($alist->updated_at))}}</td>
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
                              
                              
                                 <div class="tab-pane fade <?php echo ($activeTab === 'partner-activities') ? 'show active' : ''; ?>" id="partner-activities" role="tabpanel" aria-labelledby="partner-activities-tab">
                                    <div class="activities">
                                        <?php
                                        $activities = \App\Models\ActivitiesLog::where('client_id', $fetchedData->id)->where('task_group', 'partner')->orderby('created_at', 'DESC')->get();
                                        //dd($activities);
                                        foreach($activities as $activit){
                                            $admin = \App\Models\Admin::select('id', 'first_name', 'last_name')->where('id', $activit->created_by)->first();
                                            ?>
                                            <div class="activity" id="activity_{{$activit->id}}">
                                                <div class="activity-icon bg-primary text-white">
                                                    <span>{{substr($admin->first_name, 0, 1)}}</span>
                                                </div>
                                                <div class="activity-detail" style="border: 1px solid #dbdbdb;background-color: #dbdbdb;">
                                                    <div class="activity-head">
                                                        <div class="activity-title">
                                                            <p><b>{{$admin->first_name}}</b>  <?php echo @$activit->subject; ?></p>
                                                        </div>

                                                        <div class="activity-date">
                                                          <span class="text-job">{{date('d M Y, H:i A', strtotime($activit->created_at))}}</span>
                                                        </div>
                                                    </div>

                                                    <!--<div class="right" style="float: right;margin-top: -40px;">
                                                        <?php //if($activit->pin == 1){?>
                                                            <div class="pined_note"><i class="fa fa-thumbtack" style="font-size: 12px;color: #6777ef;"></i></div>
                                                        <?php //} ?>

                                                        <div class="dropdown d-inline dropdown_ellipsis_icon">
                                                            <a class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                                            <div class="dropdown-menu">
                                                                <a data-id="{{--$activit->id--}}" data-href="deleteactivitylog" class="dropdown-item deleteactivitylog" href="javascript:;" >Delete</a>
                                                                <?php //if($activit->pin == 1){ ?>
                                                                    <a data-id="<?php //echo $activit->id;?>"  class="dropdown-item pinactivitylog" href="javascript:;" >UnPin</a>
                                                                <?php
                                                                //} else { ?>
                                                                    <a data-id="<?php //echo $activit->id;?>"  class="dropdown-item pinactivitylog" href="javascript:;" >Pin</a>
                                                                <?php //} ?>
                                                            </div>
                                                        </div>
                                                    </div>-->

                                                    @if($activit->description != '')
                                                        <p>{!!$activit->description!!}</p>
                                                    @endif

                                                    @if($activit->followup_date != '')
                                                        <p>{!!date('d/m/Y',strtotime($activit->followup_date))!!}</p>
                                                    @endif

                                                    @if($activit->task_group != '')
                                                        <p>{!!$activit->task_group!!}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <?php
                                                }
                                            ?>
                                    </div>
                                </div>
                              
								<div class="tab-pane fade <?php echo ($activeTab === 'products') ? 'show active' : ''; ?>" id="products" role="tabpanel" aria-labelledby="products-tab">
									<div class="card-header-action text-end" style="padding-bottom:15px;">
										<a href="{{route('products.create')}}" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="table-responsive"> 
										<table class="table text_wrap">
											<thead>
												<tr>
													<th>Product Name</th>
													<th>Sync</th>
													<th>Branches</th>
													<th>In Progress</th>
													<th></th>
												</tr> 
											</thead>
											<tbody class="applicationtdata">
											<?php
											$products = \App\Models\Product::where('partner', $fetchedData->id)->orderby('created_at', 'DESC')->get();
											foreach($products as $product){
											?>
												<tr id="id_{{@$product->id}}">
													<td>{{$product->name}}</td>
													<td></td>
													<?php
													$bname = array();
													if($product->branches != ''){
														$branches = \App\Models\PartnerBranch::whereIn('id', explode(',',$product->branches))->get();
														foreach($branches as $b){
															$bname[] = $b->name;
														}
													}
													?>
													<td>{{implode(', ', $bname)}}</td>
													
													<?php
													$countapplication = \App\Models\Application::where('product_id', $product->id)->where('status', 0)->count();
													?>
													<td>{{$countapplication}}</td>
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu"> 
																<a class="dropdown-item has-icon" href="{{URL::to('/products/detail/'.base64_encode(convert_uuencode(@$product->id)))}}"><i class="far fa-eye"></i> View</a>
																<a class="dropdown-item has-icon" href="{{URL::to('/products/edit/'.base64_encode(convert_uuencode(@$product->id)))}}"><i class="far fa-edit"></i> Edit</a>
																<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction({{@$product->id}}, 'products')"><i class="fas fa-trash"></i> Delete</a>
															</div>
														</div>
													</td>
												</tr>
											<?php } ?>
											</tbody>
										</table> 
									</div>	
									<div class="clearfix"></div>
								</div>
								<div class="tab-pane fade <?php echo ($activeTab === 'branches') ? 'show active' : ''; ?>" id="branches" role="tabpanel" aria-labelledby="branches-tab">
									<div class="card-header-action text-end" style="padding-bottom:15px;">
										<a href="javascript:;" class="btn btn-primary openbranchnew"><i class="fa fa-plus"></i> Add</a> 
									</div>
									<div class="branch_term_list">
									<?php
										$branchesquery = \App\Models\PartnerBranch::where('partner_id', $fetchedData->id)->orderby('created_at', 'DESC');
										$branchescount = $branchesquery->count();
										$branches = $branchesquery->get();
										if($branchescount !== 0){
										foreach($branches as $branch){
									?>
										<div class="branch_col" id="contact_"> 
											<div class="branch_content">
												<h4><?php echo $branch->name; ?></h4>
												<div class="" style="margin-top: 15px!important;">
													<p><i class="fa fa-map-marker-alt" style="margin-right: 10px!important;"></i> <?php echo $branch->city; ?>, <?php echo $branch->a; ?></p>
												</div>
											</div>
											<div class="extra_content">
												<div class="left">
													<p><i class="fa fa-phone" style="margin-right: 20px!important;"></i> <?php if($branch->phone != ''){ echo $branch->phone; }else{ echo '-'; } ?></p>
													<p><i class="fa fa-envelope-o" style="margin-right: 20px!important;"></i> <?php if($branch->email != ''){ echo $branch->email; }else{ echo '-'; } ?></p>
												</div>  
												<div class="right">
													<div class="dropdown d-inline dropdown_ellipsis_icon">
														<a class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">
															<a class="dropdown-item openbranchform" data-id="{{$branch->id}}" href="javascript:;">Edit</a>
															<a data-id="{{$branch->id}}" data-href="deletebranch" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
														</div>
													</div>
												</div>
											</div>
										</div>
										<?php }
										}else{
											?>
											<h4>No Record Found</h4>
											<?php
										}
										?>
									</div>	
									<div class="clearfix"></div>
								</div>	
								<div class="tab-pane fade <?php echo ($activeTab === 'agreements') ? 'show active' : ''; ?>" id="agreements" role="tabpanel" aria-labelledby="agreements-tab">
									<div class="agreement_info">
										<h4>Contact Information</h4>
										<form method="post"  action="{{URL::to('/partner/saveagreement')}}" autocomplete="off" name="saveagreement" id="saveagreement" enctype="multipart/form-data">
										@csrf
										<input type="hidden" name="partner_id" value="{{$fetchedData->id}}">
										<div class="row">
                                            <div class="col-md-4">
												<div class="form-group">
													<label for="contract_start">Contract Start Date</label>
													<div class="input-group">
														<div class="input-group-prepend">
															<div class="input-group-text">
																<i class="fas fa-calendar-alt"></i>
															</div>
														</div>
														{!! Form::text('contract_start','', array('id' => 'contract_start','class' => 'form-control contract_expiry', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' ))  !!}
														@if ($errors->has('contract_start'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('contract_start') }}</strong>
															</span>
														@endif
													</div>
												</div>
											</div>
                                          
											<div class="col-md-4">
												<div class="form-group">
													<label for="contract_expiry">Contract Expiry Date</label>
													<div class="input-group">
														<div class="input-group-prepend">
															<div class="input-group-text">
																<i class="fas fa-calendar-alt"></i>
															</div>
														</div>
														{!! Form::text('contract_expiry', @$fetchedData->contract_expiry, array('id' => 'contract_expiry', 'class' => 'form-control contract_expiry', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' ))  !!}
														@if ($errors->has('contract_expiry'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('contract_expiry') }}</strong>
															</span> 
														@endif
													</div>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label for="represent_region">Representing Regions</label>
													<select class="form-control represent_region select2" multiple name="represent_region[]" >
														<option value="">Select</option>
														<?php
														$represent_region = explode(',',$fetchedData->represent_region);
														foreach(\App\Models\Country::all() as $list){
															?>
															<option <?php if(in_array($list->name, $represent_region)){ echo 'selected'; } ?> value="{{@$list->name}}">{{@$list->name}}</option>
															<?php
														}
														?>
													</select>
													@if ($errors->has('represent_region'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('represent_region') }}</strong>
														</span> 
													@endif
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label for="commission_percentage">Commission Percentage</label>
													{!! Form::number('commission_percentage', @$fetchedData->commission_percentage, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Commission Percentage', 'step'=>'0.01' ))  !!}
													@if ($errors->has('commission_percentage'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('commission_percentage') }}</strong>
														</span> 
													@endif
												</div>
											</div>
											<div class="col-md-1">
												<div class="form-group">
													<label for="gst">GST</label>
													<input type="checkbox" <?php if(@$fetchedData->gst == 1){ echo 'checked'; } ?> name="gst" value="1">
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label for="default_super_agent">Default Super Agent</label>
													<select class="form-control default_super_agent select2" name="default_super_agent" >
														<option value="">Select</option>
													</select>
													@if ($errors->has('default_super_agent'))
														<span class="custom-error" role="alert">
															<strong>{{ @$errors->first('default_super_agent') }}</strong>
														</span> 
													@endif
												</div>
											</div>
                                          
                                            <div class="col-md-3">
												<div class="form-group">
													<label for="default_super_agent">Document upload</label>
													<input type="file" name="file_upload"/>
												</div>
											</div>
                                          
											<div class="col-12 col-md-12 col-lg-12">
												<div class="form-group float-end">
													<button onclick="customValidate('saveagreement')" type="button" class="btn btn-primary">Save Changes</button>
												</div>
                                              
                                                <?php
                                                if( isset($fetchedData->file_upload ) && $fetchedData->file_upload != ""){
                                                    // Check if it's a full URL (S3) or just filename (old local)
                                                    if(filter_var($fetchedData->file_upload, FILTER_VALIDATE_URL)){
                                                        // It's a full S3 URL
                                                        $file_url = $fetchedData->file_upload;
                                                        $file_display_name = basename(parse_url($fetchedData->file_upload, PHP_URL_PATH));
                                                    } else {
                                                        // Old local file - backward compatibility
                                                        $file_url = "https://bansalcrm.com/public/img/documents/".$fetchedData->file_upload;
                                                        $file_display_name = $fetchedData->file_upload;
                                                    }
                                                ?>
                                                    <a href="<?php echo $file_url;?>" target="_blank"><?php echo $file_display_name;?></a>
                                                <?php
                                                }
                                                ?>

											</div>
										</div>
										</form>
									</div>
									
									<div class="clearfix"></div>
								</div>
								<div class="tab-pane fade <?php echo ($activeTab === 'contacts') ? 'show active' : ''; ?>" id="contacts" role="tabpanel" aria-labelledby="contacts-tab">  
									<div class="card-header-action text-end" style="padding-bottom:15px;">
										<a href="javascript:;"  class="btn btn-primary add_clientcontact"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="contact_term_list">
									<?php
									
									$querycontactlist = \App\Models\Contact::where('user_id', $fetchedData->id)->orderby('created_at', 'DESC');
									$contactlistcount = $querycontactlist->count();
									$contactlist = $querycontactlist->get();
									if($contactlistcount !== 0){
									foreach($contactlist as $clist){
										$branch = \App\Models\PartnerBranch::where('id', $clist->branch)->first();
									?>
										<div class="note_col" id="contact_{{$clist->id}}" style="width:33.33333333%"> 
											<div class="note_content">
												<h4>{{$clist->name}}</h4>
												<p><span class="text-semi-bold"><?php if($clist->position != ''){ echo $clist->position; }else{ echo '-'; } ?></span> In <span class="text-semi-bold"><?php if($clist->department != ''){ echo $clist->department; }else{ echo '-'; } ?></span></p>
												<div class="" style="margin-top: 15px!important;">
													<p><i class="fa fa-phone"></i> <?php if($clist->contact_phone != ''){ echo $clist->contact_phone; }else{ echo '-'; } ?></p>
													<p style="margin-top: 5px!important;"><i class="fa fa-fax"></i> <?php if($clist->fax != ''){ echo $clist->fax; }else{ echo '-'; } ?></p>
													<p style="margin-top: 5px!important;"><i class="fa fa-mail"></i> <?php if($clist->contact_email != ''){ echo $clist->contact_email; }else{ echo '-'; } ?></p>
												</div>
											</div>
											<div class="extra_content">
												<div class="left">
													<i class="fa fa-map-marker" style="margin-right: 20px!important;"></i>
													<?php echo $branch->city; ?>, <?php echo $branch->country; ?>
												</div>  
												<div class="right">
													<div class="dropdown d-inline dropdown_ellipsis_icon">
														<a class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">
															<a class="dropdown-item opencontactform" data-id="{{$clist->id}}" href="javascript:;">Edit</a>
															<a data-id="{{$clist->id}}" data-href="deletecontact" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php }
									}else{
										echo '<h4>Record not found</h4>';
									}									?>
									</div>									
									<div class="clearfix"></div>
								</div>
								<div class="tab-pane fade <?php echo ($activeTab === 'noteterm') ? 'show active' : ''; ?>" id="noteterm" role="tabpanel" aria-labelledby="noteterm-tab">
									<div class="card-header-action text-end" style="padding-bottom:15px;">
										<a href="javascript:;" datatype="note" class="create_note btn btn-primary"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="note_term_list"> 
									
									<?php
									
									$querynotelist = \App\Models\Note::where('client_id', $fetchedData->id)->where('type', 'partner')->whereNull('task_group')->orderby('pin', 'DESC')->orderby('created_at', 'DESC');
									$notelistcount = $querynotelist->count();
									$notelist = $querynotelist->get();
									if($notelistcount !== 0){
									foreach($notelist as $list){
										$admin = \App\Models\Admin::where('id', $list->user_id)->first();
										
									?>
										<div class="note_col" id="note_id_{{$list->id}}"> 
                                            <div class="note-icon bg-primary text-white" style="width: 50px;height: 50px;line-height: 50px;font-size: 20px;margin-right: 20px;border-radius: 50%;text-align: center;">
                                                <span>{{substr($admin->first_name, 0, 1)}}</span>
                                            </div>
                                          
											<div class="note_content">
												<!--<h4><a class="viewnote" data-id="{{$list->id}}" href="javascript:;">{{ @$list->title == "" ? config('constants.empty') : str_limit(@$list->title, '19', '...') }}</a></h4>-->
                                              
                                                <div class="note-title" style="display: inline-block;margin-right: 60px;">
                                                    <p><b>{{$admin->first_name}}</b>  Added Note with Title <b><?php echo @$list->title; ?></b></p>
                                                </div>

                                                <div class="note-date" style="display: inline-block;">
                                                  <span class="text-job">{{date('d M Y, H:i A', strtotime($list->updated_at))}}</span>
                                                </div>

                                                <div class="right" style="float: right;width: 15px;">
													<div class="dropdown d-inline dropdown_ellipsis_icon">
														<a class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">
															<a class="dropdown-item opennoteform" data-id="{{$list->id}}" href="javascript:;">Edit</a>
                                                            @if(Auth::user()->role == 1)
															<a data-id="{{$list->id}}" data-href="deletenote" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
                                                            @endif
															<?php if($list->pin == 1){ ?>
                                                                <a data-id="<?php echo $list->id; ?>"  class="dropdown-item pinnote" href="javascript:;" >UnPin</a>
                                                            <?php }else{ ?>
                                                                <a data-id="<?php echo $list->id; ?>"  class="dropdown-item pinnote" href="javascript:;" >Pin</a>
                                                            <?php } ?>
														</div>
													</div>
												</div>
												
											</div>
											<div class="extra_content">
                                                <p>{!! @$list->description !!}</p>
                                              
                                                 <?php if( isset($list->mobile_number) && $list->mobile_number != ""){ ?>
                                                    <p>{{ @$list->mobile_number }}</p>
                                                <?php }?>
                                              
												<!--<div class="left">
													<div class="author">
														<a href="#">{{substr($admin->first_name, 0, 1)}}</a>
													</div>
													<div class="note_modify">
														<small>Last Modified <span>{{date('d/m/Y', strtotime($list->updated_at))}}</span></small>
													</div>
												</div>-->  
												
											</div>
										</div>
									<?php }
									}else{
										echo '<h4>No Record Found</h4>';
									}
									?>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="tab-pane fade <?php echo ($activeTab === 'documents') ? 'show active' : ''; ?>" id="documents" role="tabpanel" aria-labelledby="documents-tab">
									<div class="card-header-action text-end" style="padding-bottom:15px;">
										<div class="document_layout_type">
											<a href="javascript:;" class="list active"><i class="fas fa-list"></i></a>
											<a href="javascript:;" class="grid"><i class="fas fa-columns"></i></a>
										</div>
										<a href="javascript:;" class="btn btn-primary add_alldocument_doc"><i class="fa fa-plus"></i> Add Checklist</a>
										<button type="button" class="btn btn-info bulk-upload-toggle-btn ms-2"><i class="fas fa-upload"></i> Bulk Upload</button>
									</div>
									
									<!-- Bulk Upload Dropzone (Hidden by default) -->
									<div class="bulk-upload-dropzone-container" id="bulk-upload-documents" style="display: none; margin-bottom: 20px; padding: 0 15px;">
										<div class="bulk-upload-dropzone" 
											 style="border: 2px dashed #4a90e2; border-radius: 8px; padding: 40px; 
													text-align: center; background-color: #f8f9fa; cursor: pointer;">
											<i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #4a90e2; margin-bottom: 15px;"></i>
											<h4 style="color: #333; margin-bottom: 10px;">Drop files here or click to browse</h4>
											<p style="color: #666; margin-bottom: 0;">Supported: PDF, JPG, PNG, DOC, DOCX (Max 50MB per file)</p>
											<input type="file" class="bulk-upload-file-input" multiple style="display: none;" 
												   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
										</div>
										<div class="bulk-upload-file-list" style="display: none; margin-top: 15px; padding: 15px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px;">
											<strong style="color: #333;">Selected Files: <span class="file-count">0</span></strong>
											<div class="bulk-upload-files-container"></div>
										</div>
									</div>
									
									<div class="list_data col-6 col-md-6 col-lg-6" style="display:inline-block;vertical-align: top;">
										<div class="">
											<table class="table text_wrap">
												<thead>
													<tr>
														<th>Checklist</th>
														<th>File Name</th>
														<!--<th>Verified By</th>-->
													</tr>
												</thead>
												<tbody class="tdata alldocumnetlist">
													<?php
													$fetchd = \App\Models\Document::where('client_id',$fetchedData->id)
														->where('type','partner')
														->whereNull('not_used_doc')
														->where(function ($query) {
															$query->where('doc_type', 'documents')
																->orWhere(function ($q) {
																	$q->whereNull('doc_type')->orWhere('doc_type', '');
																});
														})->orderby('updated_at', 'DESC')->get();
													foreach($fetchd as $docKey=>$fetch)
													{
														$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
														$addedByInfo = $admin->first_name . ' on ' . date('d/m/Y', strtotime($fetch->created_at));
														// Handle checklist field - use existing checklist if available, otherwise use file_name as fallback for backward compatibility
														$checklist = !empty($fetch->checklist) ? $fetch->checklist : (!empty($fetch->file_name) ? $fetch->file_name : 'N/A');
														?>
														<tr class="drow document-row" id="id_{{$fetch->id}}" 
															data-doc-id="<?php echo $fetch->id;?>"
															data-checklist-name="<?php echo htmlspecialchars($checklist, ENT_QUOTES, 'UTF-8'); ?>"
															data-file-name="<?php echo htmlspecialchars($fetch->file_name, ENT_QUOTES, 'UTF-8'); ?>"
															data-file-type="<?php echo htmlspecialchars($fetch->filetype, ENT_QUOTES, 'UTF-8'); ?>"
															data-myfile="<?php echo htmlspecialchars($fetch->myfile, ENT_QUOTES, 'UTF-8'); ?>"
															data-myfile-key="<?php echo isset($fetch->myfile_key) ? htmlspecialchars($fetch->myfile_key, ENT_QUOTES, 'UTF-8') : ''; ?>"
															data-doc-type="<?php echo htmlspecialchars($fetch->doc_type ? $fetch->doc_type : 'documents', ENT_QUOTES, 'UTF-8'); ?>"
															data-user-role="<?php echo Auth::user()->role; ?>"
															title="Added by: <?php echo htmlspecialchars($addedByInfo, ENT_QUOTES, 'UTF-8'); ?>"
															style="cursor: context-menu;">
															<td style="white-space: initial;">
																<div data-id="<?php echo $fetch->id;?>" data-personalchecklistname="<?php echo $checklist; ?>" class="personalchecklist-row">
																	<span><?php echo $checklist; ?></span>
																</div>
															</td>
															<td style="white-space: initial;">
																<?php
																if( isset($fetch->file_name) && $fetch->file_name !=""){ ?>
																	<div data-id="{{$fetch->id}}" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
																		<?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
																			<a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-alldocumentlist-partner')">
																				<i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
																			</a>
																		<?php } else {  //For old file upload
																			$docType = $fetch->doc_type ? $fetch->doc_type : 'documents';
																			if (filter_var($fetch->myfile, FILTER_VALIDATE_URL)) {
																				// String is a valid URL
																				$previewUrl = $fetch->myfile;
																			} else {
																				// Check if it's AWS path or local path
																				$url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
																				$previewUrl = $url.$fetchedData->id.'/'.$docType.'/'.$fetch->myfile;
																			}
																			?>
																			<a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($previewUrl); ?>','preview-container-alldocumentlist-partner')">
																				<i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
																			</a>
																		<?php } ?>
																	</div>
																<?php
																}
																else
																{?>
																	<div class="allupload_document" style="display:inline-block;">
																		<form method="POST" enctype="multipart/form-data" id="upload_form_<?php echo $fetch->id;?>">
																			@csrf
																			<input type="hidden" name="clientid" value="{{$fetchedData->id}}">
																			<input type="hidden" name="fileid" value="{{$fetch->id}}">
																			<input type="hidden" name="type" value="partner">
																			<input type="hidden" name="doctype" value="documents">
																			<a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>
																			<input class="alldocupload" data-fileid="<?php echo $fetch->id;?>" type="file" name="document_upload"/>
																		</form>
																	</div>
																<?php
																}?>
															</td>
														</tr>
													<?php
													} //end foreach?>
												</tbody>
											</table>
										</div>
									</div>
									<div class="grid_data allgriddata">
										<?php
										foreach($fetchd as $fetch)
										{
											$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
											?>
											<div class="grid_list" id="gid_<?php echo $fetch->id; ?>">
												<div class="grid_col">
													<div class="grid_icon">
														<i class="fas fa-file-image"></i>
													</div>
													<?php
													if( isset($fetch->myfile) && $fetch->myfile != "")
													{ ?>
														<div class="grid_content">
															<span id="grid_<?php echo $fetch->id; ?>" class="gridfilename"><?php echo $fetch->file_name; ?></span>
															<div class="dropdown d-inline dropdown_ellipsis_icon">
																<a class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
																<div class="dropdown-menu">
																	<?php $docType = $fetch->doc_type ? $fetch->doc_type : 'documents'; ?>
																	<?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
																		<a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
																		<a download class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Download</a>
																	<?php } else {  //For old file upload
																		if (filter_var($fetch->myfile, FILTER_VALIDATE_URL)) {
																			$previewUrl = $fetch->myfile;
																		} else {
																			$url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
																			$previewUrl = $url.$fetchedData->id.'/'.$docType.'/'.$fetch->myfile;
																		}
																	?>
																		<a target="_blank" class="dropdown-item" href="<?php echo $previewUrl; ?>">Preview</a>
																		<a download class="dropdown-item" href="<?php echo $previewUrl; ?>">Download</a>
																	<?php } ?>

																	<?php if( Auth::user()->role == 1 ){ //super admin ?>
																	<a data-id="{{$fetch->id}}" class="dropdown-item deletenote" data-href="deletealldocs" href="javascript:;">Delete</a>
																	<?php } ?>
																	<a data-id="{{$fetch->id}}" class="dropdown-item verifydoc" data-doctype="documents" data-href="verifydoc" href="javascript:;">Verify</a>
																	<a data-id="{{$fetch->id}}" class="dropdown-item notuseddoc" data-doctype="documents" data-href="notuseddoc" href="javascript:;">Not Used</a>
																</div>
															</div>
														</div>
													<?php
													}?>
												</div>
											</div>
										<?php
										} //end foreach ?>
										<div class="clearfix"></div>
									</div>
								   
									<!-- Container for File Preview -->
									<div style="margin-left: 10px;" class="col-5 col-md-5 col-lg-5 file-preview-container preview-container-alldocumentlist-partner">
										<p style="color:#000;">Click on a file to preview it here.</p>
									</div>
									
									<!-- Bulk Upload Mapping Modal -->
									<div id="bulk-upload-mapping-modal-partner" class="bulk-upload-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; overflow-y: auto;">
										<div class="bulk-upload-modal-content">
											<div style="padding: 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
												<h3 style="margin: 0; color: #333;">Map Files to Checklists</h3>
												<button type="button" class="close-mapping-modal" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
											</div>
											<div style="padding: 20px; overflow-x: auto;">
												<div id="bulk-upload-mapping-table-partner"></div>
											</div>
											<div style="padding: 20px; border-top: 1px solid #e2e8f0;">
												<div class="bulk-upload-progress" id="bulk-upload-progress-partner" style="display: none; margin-bottom: 15px;">
													<div style="background: #e2e8f0; border-radius: 4px; overflow: hidden; height: 30px;">
														<div class="progress-bar" id="bulk-upload-progress-bar-partner" 
															 style="background: #4a90e2; height: 100%; color: white; display: flex; 
																	align-items: center; justify-content: center; font-weight: bold; 
																	transition: width 0.3s; width: 0%;">0%</div>
													</div>
												</div>
												<div style="text-align: right;">
													<button type="button" class="btn btn-secondary" id="cancel-bulk-upload-partner">Cancel</button>
													<button type="button" class="btn btn-primary" id="confirm-bulk-upload-partner" style="margin-left: 10px;">Upload All</button>
												</div>
											</div>
										</div>
									</div>
								</div>
								{{-- Appointments tab removed - Appointment model deleted --}}
								<div class="tab-pane fade <?php echo ($activeTab === 'accounts') ? 'show active' : ''; ?>" id="accounts" role="tabpanel" aria-labelledby="accounts-tab">
									
									<div class="table-responsive"> 
										<table class="table invoicetable text_wrap">
											<thead>
												<tr>
													<th>Invoice No.</th>
													<th>Issue Date</th>
													<th>Service</th>
													<th>Invoice Amount</th>
													<th>Paid Amount</th>
													<th>Status</th>
													<th>Actions</th>
												</tr> 
											</thead>
											<tbody class="tdata invoicedatalist">
												<?php
												$applications = \App\Models\Application::where('partner_id',$fetchedData->id)->get();
												
												foreach($applications as $application){
													$invoicelists = \App\Models\Invoice::where('application_id',$application->id)->orderby('created_at','DESC')->get();
													
														foreach($invoicelists as $invoicelist){
															if($invoicelist->type == 3){
																$workflowdaa = \App\Models\Workflow::where('id', $invoicelist->application_id)->first();
															}else{
																$applicationdata = \App\Models\Application::where('id', $invoicelist->application_id)->first();
																$workflowdaa = \App\Models\Workflow::where('id', $invoicelist->application_id)->first();
																$partnerdata = \App\Models\Partner::where('id', $applicationdata->partner_id)->first();
															}
															$invoiceitemdetails = \App\Models\InvoiceDetail::where('invoice_id', $invoicelist->id)->orderby('id','ASC')->get();
															$netamount = 0;
															$coom_amt = 0;
															$total_fee = 0;
															foreach($invoiceitemdetails as $invoiceitemdetail){
																$netamount += $invoiceitemdetail->netamount;
																$coom_amt += $invoiceitemdetail->comm_amt;
																$total_fee += $invoiceitemdetail->total_fee;
															}
													
													$paymentdetails = \App\Models\InvoicePayment::where('invoice_id', $invoicelist->id)->orderby('created_at', 'DESC')->get();
													$amount_rec = 0;
													foreach($paymentdetails as $paymentdetail){
														$amount_rec += $paymentdetail->amount_rec;
													} 
													if($invoicelist->type == 1){
														$totaldue = $total_fee - $coom_amt;
													} if($invoicelist->type == 2){
														$totaldue = $netamount - $amount_rec;
													}else{
														$totaldue = $netamount - $amount_rec;
													}
													
													
												?>
												<tr id="iid_{{$invoicelist->id}}">
													<td>{{$invoicelist->id}}</td>
													<td>{{$invoicelist->invoice_date}}
													<?php if($invoicelist->type == 1){
														$rtype = 'Net Claim';
													}else if($invoicelist->type == 2){
														$rtype = 'Gross Claim';
													}else{
														$rtype = 'General';
													} ?>
													<span title="{{$rtype}}" class="ui label zippyLabel">{{$rtype}}</span></td>
													<td>{{@$workflowdaa->name}}<br>{{@$partnerdata->partner_name}}</td>
													<td>AUD {{$invoicelist->net_fee_rec}}</td>	
													<td>{{$amount_rec}}</td>
													
													<td>
													@if($invoicelist->status == 1)
														<span class="ag-label--circular" style="color: #6777ef" >Paid</span></td> 
													@else
														<span class="ag-label--circular" style="color: #ed5a5a" >UnPaid</span></td> 
													@endif
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">
																<a class="dropdown-item has-icon" href="#">Send Email</a>
																<a target="_blank" class="dropdown-item has-icon" href="{{URL::to('invoice/view/')}}/{{$invoicelist->id}}">View</a>
																<?php if($invoicelist->status == 0){ ?>
																<a target="_blank" class="dropdown-item has-icon" href="{{URL::to('invoice/edit/')}}/{{$invoicelist->id}}">Edit</a>
																<a data-netamount="{{$netamount}}" data-dueamount="{{$totaldue}}" data-invoiceid="{{$invoicelist->id}}" class="dropdown-item has-icon addpaymentmodal" href="javascript:;"> Make Payment</a>
																<?php } ?>
															</div>
														</div>								  
													</td>
												</tr>
												<?php } 
													
												}
												?>
											</tbody>
										</table>
									</div>
								</div>
                      
                      
								<div class="tab-pane fade <?php echo ($activeTab === 'conversations') ? 'show active' : ''; ?>" id="conversations" role="tabpanel" aria-labelledby="conversations-tab">
									<div class="conversation_tabs">
										<ul class="nav nav-pills round_tabs" id="client_tabs" role="tablist">
										    <li class="nav-item">
                                                <a class="nav-link active" data-bs-toggle="tab" id="inbox-tab" href="#inbox" role="tab" aria-controls="inbox" aria-selected="true">Inbox</a>
                                            </li>

                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" id="sent-tab" href="#sent" role="tab" aria-controls="sent" aria-selected="false">Sent</a>
                                            </li>
										</ul>
										<div class="tab-content" id="conversationContent">
											<div class="tab-pane fade show active" id="inbox" role="tabpanel" aria-labelledby="inbox-tab">
                                                <div class="row">
                                                    <div class="col-md-12" style="text-align: right;margin-bottom: 10px;">
                                                        <a class="btn btn-outline-primary btn-sm partnerUploadAndFetchMail" href="javascript:;">Upload Inbox Mail</a>
                                                    </div>
                                                </div>

                                                <div class="tab-content" id="conversationContent">
                                                    <div class="tab-pane show active" id="fetchemail" role="tabpanel" aria-labelledby="fetchemail-tab">
                                                        <?php
                                                        //inbox mail
                                                         //dd($fetchedData->id);
                                                        $mailreports = \App\Models\MailReport::where('client_id',$fetchedData->id)->where('type','partner')->where('mail_type',1)->where('conversion_type','partner_email_fetch')->where('mail_body_type','inbox')->orderby('created_at', 'DESC')->get();
                                                        //dd($mailreports);
                                                        foreach($mailreports as $mailreport)
                                                        {
                                                            $DocInfo = \App\Models\Document::select('id','doc_type','myfile','mail_type')->where('id',$mailreport->uploaded_doc_id)->first();
                                                            $PartnerInfo = \App\Models\Partner::select('id','partner_name','email')->where('id',$mailreport->client_id)->first();
                                                        ?>
                                                        <div class="conversation_list inbox_conversation_list">
                                                            <div class="conversa_item">
                                                                <div class="ds_flex">
                                                                    <div class="title">
                                                                        <span>{{ substr(@$mailreport->subject, 0, 50) }}</span>
                                                                    </div>
                                                                    <div class="conver_action">
                                                                        <div class="date">
                                                                            <?php if(isset($mailreport->fetch_mail_sent_time) && $mailreport->fetch_mail_sent_time != "") { ?>
                                                                                <span>{{$mailreport->fetch_mail_sent_time}}</span>
                                                                            <?php }  else {?>
                                                                                <span></span>
                                                                            <?php } ?>
                                                                        </div>

                                                                        <div class="dropdown d-inline">
                                                                            <button class="btn btn-primary dropdown-toggle" style="width: 100px;margin:0px !important;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                                            <div class="dropdown-menu">
                                                                                <?php
                                                                                if($DocInfo)
                                                                                { ?>
                                                                                <a class="dropdown-item has-icon mail_preview_modal" memail_id="{{@$mailreport->id}}" target="_blank"  href="<?php echo $DocInfo->myfile; ?>" ><i class="fas fa-eye"></i> Preview</a>
                                                                                <?php }?>
                                                                                <!--<a class="dropdown-item has-icon create_note" datamailid="{{--$mailreport->id--}}" datasubject="{{--@$mailreport->subject--}}" class="create_note" datatype="mailnote" ><i class="fas fa-file-alt"></i> Create Note</a>
                                                                                <a class="dropdown-item has-icon inbox_reassignemail_modal" memail_id="{{--@$mailreport->id--}}" user_mail="{{--@$mailreport->to_mail--}}" uploaded_doc_id="{{--@$mailreport->uploaded_doc_id--}}" href="javascript:;"><i class="fas fa-shopping-bag"></i> Reassign</a>-->
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="email_info">
                                                                    <div class="avatar_img">
                                                                        <span>{{substr(@$mailreport->from_mail, 0, 1)}}</span>
                                                                    </div>
                                                                    <div class="email_content">
                                                                        <span class="email_label" style="color: #000;">From:</span>
                                                                        <span class="email_sentby"><strong>{{@$mailreport->from_mail}}</strong> </span>
                                                                        <span class="span_desc">
                                                                            <span class="email_label" style="color: #000;">To</span>
                                                                            <span class="email_sentby" style="color: #000;"><i class="fa fa-angle-left"></i>{{@$mailreport->to_mail}}<i class="fa fa-angle-right"></i></span>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="tab-pane fade" id="sent" role="tabpanel" aria-labelledby="sent-tab">
                                                <div class="row">
                                                    <div class="col-md-12" style="text-align: right;margin-bottom: 10px;">
                                                        <a class="btn btn-outline-primary btn-sm clientemail" data-id="{{@$fetchedData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->partner_name}}" id="email-tab" href="#email" role="tab" aria-controls="email" aria-selected="true">Compose Email</a>
                                                        <a class="btn btn-outline-primary btn-sm partnerUploadSentAndFetchMail" href="javascript:;">Upload Sent Mail</a>
                                                    </div>
                                                </div>
                                                <?php
                                                //Sent Mail after assign user and Compose email
                                                $mailreports = \App\Models\MailReport::where('client_id', $fetchedData->id)
                                                ->where('type', 'partner')
                                                //->where('mail_type', 1)
                                                ->where(function($query) {
                                                    $query->whereNull('conversion_type')
                                                    ->orWhere(function($subQuery) {
                                                        $subQuery->where('conversion_type', 'partner_email_fetch')
                                                        ->where('mail_body_type', 'sent');
                                                    });
                                                })
                                                ->orderBy('created_at', 'DESC')
                                                ->get(); //dd($mailreports);
                                                foreach($mailreports as $mailreport){
                                                    $admin = \App\Models\Admin::where('id', $mailreport->user_id)->first();
                                                    $partner = \App\Models\Partner::Where('id', $fetchedData->id)->first();
                                                    $subject = str_replace('{Client First Name}',$partner->partner_name, $mailreport->subject);
                                                    $message = $mailreport->message;
                                                    $message = str_replace('{Client First Name}',$partner->partner_name, $message);
                                                    $message = str_replace('{Client Assignee Name}',$partner->partner_name, $message);
                                                    $message = str_replace('{Company Name}',Auth::user()->company_name, $message);
                                                ?>
                                                <div class="conversation_list sent_conversation_list">
                                                    <div class="conversa_item">
                                                        <div class="ds_flex">
                                                            <div class="title">
                                                                <span>{{$subject}}</span>
                                                            </div>
                                                            <div class="conver_action">
                                                                <?php
                                                                if( isset($mailreport->conversion_type) && $mailreport->conversion_type == "partner_email_fetch")
                                                                {?>
                                                                     <div class="date">
                                                                        <?php if(isset($mailreport->fetch_mail_sent_time) && $mailreport->fetch_mail_sent_time != "") { ?>
                                                                            <span>{{ $mailreport->fetch_mail_sent_time}}</span>
                                                                        <?php }  else {?>
                                                                            <span></span>
                                                                        <?php } ?>
                                                                    </div>
                                                                <?php
                                                                }
                                                                else
                                                                { ?>
                                                                    <div class="date">
                                                                        <?php if(isset($mailreport->created_at) && $mailreport->created_at != "") { ?>
                                                                            <span>{{@date('d/m/Y h:i A',strtotime($mailreport->created_at))}}</span>
                                                                        <?php }  else {?>
                                                                            <span></span>
                                                                        <?php } ?>
                                                                    </div>
                                                                <?php
                                                                }?>

                                                                <div class="dropdown d-inline">
																	<button class="btn btn-primary dropdown-toggle" style="width: 100px;margin:0px !important;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
																	<div class="dropdown-menu">
                                                                        <!--<a target="_blank" class="dropdown-item"  href="{{--url('/admin/clients/preview-msg/'.$DocInfo->myfile)--}}"><i class="fas fa-eye"></i></a>-->
																		<?php
                                                                        if( isset($mailreport->uploaded_doc_id) && $mailreport->uploaded_doc_id != ""){
                                                                            $DocInfo = \App\Models\Document::select('id','doc_type','myfile','mail_type')->where('id',$mailreport->uploaded_doc_id)->first();
                                                                            if($DocInfo) { ?>
                                                                                <a class="dropdown-item has-icon mail_preview_modal" memail_id="{{@$mailreport->id}}" target="_blank"  href="<?php echo $DocInfo->myfile; ?>" ><i class="fas fa-eye"></i> Preview</a>
                                                                            <?php
                                                                            }
                                                                        } else { ?>
                                                                            <a class="dropdown-item has-icon sent_mail_preview_modal" memail_message="{{@$mailreport->message}}" memail_subject="{{@$mailreport->subject}}"><i class="fas fa-eye"></i> Preview Mail</a>
                                                                        <?php } ?>

																		<!--<a class="dropdown-item has-icon create_note" datamailid="{{--$mailreport->id--}}" datasubject="{{--@$mailreport->subject--}}" class="create_note" datatype="mailnote" ><i class="fas fa-file-alt"></i> Create Note</a>-->

																		<?php
                                                                		//if( isset($mailreport->conversion_type) && $mailreport->conversion_type != ""){ ?>
																		<!--<a class="dropdown-item has-icon sent_reassignemail_modal" memail_id="{{--@$mailreport->id--}}" user_mail="{{--@$mailreport->to_mail--}}" uploaded_doc_id="{{--@$mailreport->uploaded_doc_id--}}" href="javascript:;"><i class="fas fa-shopping-bag"></i> Reassign</a>-->
																		<?php //} ?>
																	</div>
																</div>
                                                            </div>
                                                        </div>
                                                        <div class="email_info">
                                                            <div class="avatar_img">
                                                                <span>{{substr(@$mailreport->from_mail, 0, 1)}}</span>
                                                            </div>
                                                            <div class="email_content">
                                                                <span class="email_label" style="color: #000;">Sent by:</span>
                                                                <span class="email_sentby" style="color: #000;"><strong>{{@$mailreport->from_mail}}</strong></span>

                                                                <?php
                                                                if( isset($mailreport->conversion_type) && $mailreport->conversion_type != ""){ ?>
                                                                <?php } else { ?>
                                                                    <span class="label success">Delivered</span>
                                                                <?php } ?>

                                                                <span class="span_desc">
                                                                    <span class="email_label" style="color: #000;">Sent To</span>
                                                                    <span class="email_sentby" style="color: #000;"><i class="fa fa-angle-left"></i>{{$mailreport->to_mail}}<i class="fa fa-angle-right"></i></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php } ?>
                                            </div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade <?php echo ($activeTab === 'email-v2') ? 'show active' : ''; ?>" id="email-v2" role="tabpanel" aria-labelledby="email-v2-tab">
									@php
										$partner = $fetchedData;
									@endphp
									@include('Admin.clients.tabs.emails_v2')
								</div>
                      
                      
								{{-- Task system removed - December 2025 --}}
								<!--<div class="tab-pane fade" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">
									<div class="card-header-action text-end" style="padding-bottom:15px;">
										<a href="javascript:;"  class="btn btn-primary opencreate_task"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="table-responsive"> 
										<table id="my-datatable" class="table-2 table text_wrap">
											<thead>
												<tr>
													<th></th>
													<th></th>
													<th></th>
													<th></th>
													<th></th>
													<th></th>
												</tr>
											</thead>
											<tbody class="taskdata ">
											</tbody>
										</table> 
									</div>
								</div>-->
								<div class="tab-pane fade" id="other_info" role="tabpanel" aria-labelledby="other_info-tab">
									<span>other_info</span>
								</div>
								<div class="tab-pane fade" id="promotions" role="tabpanel" aria-labelledby="promotions-tab">
									<div class="card-header-action text-end" style="padding-bottom:15px;">
										<a href="javascript:;"  class="btn btn-primary add_promotion"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="promotionlists"> 
									<?php
									$promotionslist = \App\Models\Promotion::where('partner_id',$fetchedData->id)->orderby('created_at','DESC')->get();
									foreach($promotionslist as $promotion){
										$countproducts = 0;
										$countbranches = 0;
										if($promotion->apply_to == 'All Products'){
											$countproducts = \App\Models\Product::where('partner', $fetchedData->id)->count();
											$countbranches = \App\Models\PartnerBranch::where('partner_id', $fetchedData->id)->count();
										}else{
											$selectproduct = explode(',',$promotion->selectproduct);
											$countproducts = count($selectproduct);
											$branch = \App\Models\Product::select('branches')->whereIn('id', $selectproduct)->get()->toArray();
											$output =  array_map("unserialize", array_unique(array_map("serialize", $branch)));
											$countbranches = count($output);
										}
									?>
										<div class="promotion_col" id="contact_<?php echo $promotion->id; ?>"> 
											<div class="promotion_content">
											@if($promotion->status == 1)
												<span class="text-success"><b>Active</b></span>
											@else
												<span class="text-danger"><b>Inactive</b></span>
											@endif
												<div class="" style="margin-top: 15px!important;">
													<h4>{{$promotion->promotion_title}}</h4>
													<p>{{ @$promotion->promotion_desc == "" ? config('constants.empty') : str_limit(@$promotion->promotion_desc, '50', '...') }}</p>
												</div>
												<div class="" style="margin-top: 15px!important;">
													<div class="row">
														<div class="col-md-6">
														<span class="text-semi-bold text-underline">For Branches</span>
														</div>
														<div class="col-md-6">
														<span  class="">{{$countbranches}}</span>
														</div>
													</div>
													<div class="row">
														<div class="col-md-6">
														<span class="text-semi-bold text-underline">For Products</span>
														</div>
														<div class="col-md-6">
														<span  class="">{{$countproducts}}</span>
														</div>
													</div>
												</div>
												<div class="" style="margin-top: 15px!important;">
													<div class="row">
														<div class="col-md-6">
														<span ><b>Start Date</b></span>
														<p>{{$promotion->promotion_start_date}}</p>
														</div>
														<div class="col-md-6">
														<span ><b>Expiry Date</b></span>
														<p>{{$promotion->promotion_end_date}}</p>
														</div>
													</div>  
												</div>
											</div>
											<div class="extra_content">
												<div class="left">
													<div class="dropdown d-inline dropdown_ellipsis_icon">
														<a class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">
															<a class="dropdown-item openpromotonform" data-id="{{$promotion->id}}" href="javascript:;">Edit</a>
														</div>
													</div>	
												</div>  
												<div class="right">
													<div class="custom-switches">
														<label class="custom-switch">
															<input type="checkbox" data-status="<?php echo $promotion->status; ?>" data-id="{{$promotion->id}}" name="custom-switch-checkbox" class="custom-switch-input changepromotonstatus" @if($promotion->status == 1) checked @endif>
															<span class="custom-switch-indicator"></span>
														</label>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>
									</div>
								</div>
                      
                      			
                      
                                <div class="tab-pane fade <?php echo ($activeTab === 'student') ? 'show active' : ''; ?>" id="student" role="tabpanel" aria-labelledby="student-tab">
                                    <div class="student_tabs">
                                        <ul class="nav nav-pills round_tabs" id="student_tabs" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-bs-toggle="tab" id="stdactive-tab" href="#stdactive" role="tab" aria-controls="stdactive" aria-selected="true">Active</a>
                                            </li>

                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" id="stdinactive-tab" href="#stdinactive" role="tab" aria-controls="stdinactive" aria-selected="false">Inactive</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="studentContent">
                                            <div class="tab-pane fade show active" id="stdactive" role="tabpanel" aria-labelledby="stdactive-tab">
                                                <div class="tab-content" id="studentContent">
                                                    <div class="student_drop_table_data" style="display: inline-block;margin-right: 10px;">
                                                        <button type="button" class="btn btn-primary dropdown-toggle"><i class="fas fa-columns"></i></button>
                                                        <div class="dropdown_list student_dropdown_list">
                                                            <label class="dropdown-option all"><input type="checkbox" value="all" checked /> Display All</label>
                                                            <label class="dropdown-option"><input type="checkbox" value="3" checked /> Student Name</label>
                                                            <label class="dropdown-option"><input type="checkbox" value="4" checked /> Date of Birth</label>
                                                            <label class="dropdown-option"><input type="checkbox" value="5" checked /> Student Id</label>
                                                            <label class="dropdown-option"><input type="checkbox" value="6" checked /> College Name</label>
                                                            <label class="dropdown-option"><input type="checkbox" value="7" checked /> Course Name</label>
                                                            <label class="dropdown-option"><input type="checkbox" value="8" checked /> Start Date</label>
                                                            <label class="dropdown-option"><input type="checkbox" value="9" checked /> End Date</label>
                                                            <label class="dropdown-option"><input type="checkbox" value="10" checked /> Total Course Fee</label>
                                                            <label class="dropdown-option"><input type="checkbox" value="11" checked /> Enrolment Fee</label>
                                                            <label class="dropdown-option"><input type="checkbox" value="12" checked /> Material Fee</label>
                                                            <label class="dropdown-option"><input type="checkbox" value="13" checked /> Tution Fee</label>
                                                            <label class="dropdown-option"><input type="checkbox" value="14" checked /> Fee Reported by College</label>
                                                            <label class="dropdown-option"><input type="checkbox" value="15" checked /> Total Bonus</label>
                                                            <label class="dropdown-option"><input type="checkbox" value="16" checked /> Bonus Pending</label>
                                                            <label class="dropdown-option"><input type="checkbox" value="17" checked /> Scholarship Fee</label>

                                                            <label class="dropdown-option"><input type="checkbox" value="18" checked /> Commission as per Fee reported</label>
                                                            <label class="dropdown-option"><input type="checkbox" value="19" checked /> Commission payable as per anticipated fee</label>
                                                            <label class="dropdown-option"><input type="checkbox" value="20" checked /> Commission paid as per fee Reported</label>
                                                            <label class="dropdown-option"><input type="checkbox" value="21" checked /> Commission Pending</label>

                                                            <label class="dropdown-option"><input type="checkbox" value="22" checked /> Student Status</label>
                                                        </div>
                                                    </div>
                                                    <div class="table-responsive student_table_data">
                                                        <div class="totals-container mb-3">
                                                            <div>Total Commission Claimed: <strong>$<span id="total_commission_claimed">0.00</span></strong></div>
                                                            <div>Total Commission Paid: <strong>$<span id="total_commission_paid">0.00</span></strong></div>
                                                            <div>Total Commission Pending : <strong>$<span id="total_commission_pending">0.00</span></strong></div>
                                                            <div>Total Commission Anticipated : <strong>$<span id="total_commission_anticipated">0.00</span></strong></div>
                                                        </div>
                                                        <table class="table text_wrap table-3">
                                                            <thead>
                                                                <tr>
                                                                    <th>SNo.</th>
                                                                    <th>CRM Ref</th>
                                                                    <th>Student Name</th>
                                                                    <th>Date of Birth</th>
                                                                    <th>Student Id</th>
                                                                    <th>College Name</th>
                                                                    <th>Course Name</th>
                                                                    <th>Start Date</th>
                                                                    <th>End Date</th>
                                                                    <th>Total Course Fee</th>
                                                                    <th>Enrolment Fee</th>
                                                                    <th>Material Fee</th>
                                                                    <th>Tution Fee</th>

                                                                    <!--<th>Total Anticipated Fee</th>-->
                                                                    <th>Fee Reported by College</th>
                                                                    <th>Total Bonus</th>
                                                                    <th>Bonus Pending</th>
                                                                    <th>Scholarship Fee</th>
                                                                    <!--<th>Bonus Paid</th>-->
                                                                    <!--<th>Commission as per anticipated fee</th>-->
                                                                    <th>Commission as per Fee reported</th>
                                                                    <th>Commission payable as per anticipated fee</th>
                                                                    <th>Commission paid as per fee Reported</th>
                                                                    <th>Commission Pending</th>


                                                                    <th>Student Status</th>
                                                                    <th style="display: none;">Student ID</th> <!-- Hidden column -->
                                                                    <th>Add Note</th>
                                                                    <th>Action</th>

                                                                </tr>
                                                            </thead>
                                                            <tbody class="invoicedatalist">
                                                                <?php
                                                                //dd($fetchedData->id);
                                                                $studentdatas = \App\Models\Application::join('admins', 'applications.client_id', '=', 'admins.id')
                                                                ->leftJoin('partners', 'applications.partner_id', '=', 'partners.id')
                                                                ->leftJoin('products', 'applications.product_id', '=', 'products.id')
                                                                ->leftJoin('application_fee_options', 'applications.id', '=', 'application_fee_options.app_id')
                                                                ->select(
                                                                    'applications.*',
                                                                    'admins.client_id as client_reference',
                                                                    'admins.first_name',
                                                                    'admins.last_name',
                                                                    'admins.dob',
                                                                    'partners.partner_name',
                                                                    'products.name as coursename',
                                                                    'application_fee_options.total_course_fee_amount',
                                                                    'application_fee_options.enrolment_fee_amount',
                                                                    'application_fee_options.material_fees',
                                                                    'application_fee_options.tution_fees',
                                                                    'application_fee_options.total_anticipated_fee',
                                                                    'application_fee_options.fee_reported_by_college',
                                                                    'application_fee_options.bonus_amount',
                                                                    'application_fee_options.bonus_pending_amount',
                                                                    'application_fee_options.bonus_paid',
                                                                    'application_fee_options.scholarship_fee_amount',
                                                                    'application_fee_options.commission_as_per_anticipated_fee',
                                                                    'application_fee_options.commission_as_per_fee_reported',
                                                                    'application_fee_options.commission_payable_as_per_anticipated_fee',
                                                                    'application_fee_options.commission_paid_as_per_fee_reported',
                                                                    'application_fee_options.commission_pending'
                                                                )
                                                                ->where('applications.partner_id', $fetchedData->id)
                                                                ->where('applications.overall_status', 0) //overall status = Active
                                                                ->where(function ($query) {
                                                                    $query->where('applications.stage', 'Coe issued')
                                                                          ->orWhere('applications.stage', 'Enrolled')
                                                                          ->orWhere('applications.stage', 'Coe Cancelled');
                                                                })
                                                                ->orderBy('applications.created_at', 'ASC')
                                                                ->distinct()
                                                                ->get(); //dd($studentdatas);

																foreach($studentdatas as $datakey=>$data)
                                                                { 
                                                              	 ?>
                                                                    <tr>
                                                                        <td><?php echo ($datakey+1);?></td>
                                                                        <td>
                                                                            <?php
                                                                            if($data->client_reference){
                                                                                $client_encoded_id = base64_encode(convert_uuencode(@$data->client_id)) ;
                                                                                echo $client_reference = '<a href="'.url('/clients/detail/'.$client_encoded_id).'" class="activate-app-tab" data-tab="application" data-id="'.$data->id.'" target="_blank" >'.$data->client_reference.'</a>';
                                                                            } else {
                                                                                echo $client_reference = 'N/P';
                                                                            }?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            if($data->first_name != ""){
                                                                                echo $full_name = $data->first_name.' '.$data->last_name;
                                                                            } else {
                                                                                echo $full_name = 'N/P';
                                                                            } ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            if($data->dob != ""){ //1992-02-19 Y-m-d
                                                                                $dobArr = explode("-",$data->dob);
                                                                                echo $dob = $dobArr[2]."/".$dobArr[1]."/".$dobArr[0];
                                                                            } else {
                                                                                echo $dob = 'N/P';
                                                                            } ?>
                                                                        </td>

                                                                        <td>
                                                                            <?php
                                                                            if($data->student_id != ""){
                                                                                echo $student_id = $data->student_id;
                                                                            } else {
                                                                                echo $student_id = 'N/P';
                                                                            } ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            if($data->partner_name != ""){
                                                                                echo $partner_name = $data->partner_name;
                                                                            } else {
                                                                                echo $partner_name = 'N/P';
                                                                            } ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            if($data->coursename != ""){
                                                                                echo $coursename = $data->coursename;
                                                                            } else {
                                                                                echo $coursename = 'N/P';
                                                                            } ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            if($data->start_date != ""){
                                                                                echo $start_date = date('d/m/Y',strtotime($data->start_date));
                                                                            } else {
                                                                                echo $start_date = 'N/P';
                                                                            } ?>
                                                                        </td>

                                                                        <td>
                                                                            <?php
                                                                            if($data->end_date != ""){
                                                                                echo $end_date = date('d/m/Y',strtotime($data->end_date));
                                                                            } else {
                                                                                echo $end_date = 'N/P';
                                                                            }?>
                                                                        </td>

                                                                        <td>
                                                                            <?php
                                                                            if($data->total_course_fee_amount != ""){
                                                                                echo $total_course_fee_amount = $data->total_course_fee_amount;
                                                                            } else {
                                                                                echo $total_course_fee_amount = '0.00';
                                                                            } ?>
                                                                        </td>

                                                                        <td>
                                                                            <?php
                                                                            if($data->enrolment_fee_amount != ""){
                                                                                echo $enrolment_fee_amount = $data->enrolment_fee_amount;
                                                                            } else {
                                                                                echo $enrolment_fee_amount = '0.00';
                                                                            } ?>
                                                                        </td>

                                                                        <td>
                                                                            <?php
                                                                            if($data->material_fees != ""){
                                                                                echo $material_fees = $data->material_fees;
                                                                            } else {
                                                                                echo $material_fees = '0.00';
                                                                            } ?>
                                                                        </td>

                                                                        <td>
                                                                            <?php
                                                                            if($data->tution_fees != ""){
                                                                                echo $tution_fees = $data->tution_fees;
                                                                            } else {
                                                                                echo $tution_fees = '0.00';
                                                                            } ?>
                                                                        </td>
                                                                        <!--<td>
                                                                            <?php
                                                                            /*if($data->total_anticipated_fee != ""){
                                                                                echo $total_anticipated_fee = $data->total_anticipated_fee;
                                                                            } else {
                                                                                echo $total_anticipated_fee = '0.00';
                                                                            } */?>
                                                                        </td>-->

                                                                        <td>
                                                                            <?php
                                                                            if($data->fee_reported_by_college != ""){
                                                                                echo $fee_reported_by_college = $data->fee_reported_by_college;
                                                                            } else {
                                                                                echo $fee_reported_by_college = '0.00';
                                                                            } ?>
                                                                        </td>

                                                                        <td>
                                                                            <?php
                                                                            if($data->bonus_amount != ""){
                                                                                echo $bonus_amount = $data->bonus_amount;
                                                                            } else {
                                                                                echo $bonus_amount = '0.00';
                                                                            } ?>
                                                                        </td>

                                                                        <td>
                                                                            <?php
                                                                            if($data->bonus_pending_amount != ""){
                                                                                echo $bonus_pending_amount = $data->bonus_pending_amount;
                                                                            } else {
                                                                                echo $bonus_pending_amount = '0.00';
                                                                            } ?>
                                                                        </td>

                                                                        <td>
                                                                            <?php
                                                                            if($data->scholarship_fee_amount != ""){
                                                                                echo $scholarship_fee_amount = $data->scholarship_fee_amount;
                                                                            } else {
                                                                                echo $scholarship_fee_amount = '0.00';
                                                                            } ?>
                                                                        </td>

                                                                        <!--<td>
                                                                            <?php
                                                                            /*if($data->bonus_paid != ""){
                                                                                echo $bonus_paid = $data->bonus_paid;
                                                                            } else {
                                                                                echo $bonus_paid = '0.00';
                                                                            } */?>
                                                                        </td>-->

                                                                        <!--<td>
                                                                            <?php
                                                                            /*if($data->commission_as_per_anticipated_fee != ""){
                                                                                echo $commission_as_per_anticipated_fee = $data->commission_as_per_anticipated_fee;
                                                                            } else {
                                                                                echo $commission_as_per_anticipated_fee = '0.00';
                                                                            }*/ ?>
                                                                        </td>-->

                                                                        <td>
                                                                            <?php
                                                                            if($data->commission_as_per_fee_reported != ""){
                                                                                echo $commission_as_per_fee_reported = $data->commission_as_per_fee_reported;
                                                                            } else {
                                                                                echo $commission_as_per_fee_reported = '0.00';
                                                                            } ?>
                                                                        </td>

                                                                         <td>
                                                                            <?php
                                                                            if($data->commission_payable_as_per_anticipated_fee != ""){
                                                                                echo $commission_payable_as_per_anticipated_fee = $data->commission_payable_as_per_anticipated_fee;
                                                                            } else {
                                                                                echo $commission_payable_as_per_anticipated_fee = '0.00';
                                                                            } ?>
                                                                        </td>

                                                                        <td>
                                                                            <?php
                                                                            if($data->commission_paid_as_per_fee_reported != ""){
                                                                                echo $commission_paid_as_per_fee_reported = $data->commission_paid_as_per_fee_reported;
                                                                            } else {
                                                                                echo $commission_paid_as_per_fee_reported = '0.00';
                                                                            } ?>
                                                                        </td>

                                                                        <td>
                                                                            <?php
                                                                            if($data->commission_pending != ""){
                                                                                echo $commission_pending = $data->commission_pending;
                                                                            } else {
                                                                                echo $commission_pending = '0.00';
                                                                            } ?>
                                                                        </td>

                                                                        <td>
                                                                            <?php
                                                                            if($data->status == 0){
                                                                                echo $student_status = "In Progress";
                                                                            } else if($data->status == 1){
                                                                                echo $student_status = "Completed";
                                                                            } else if($data->status == 2){
                                                                                echo $student_status = "Discontinued";
                                                                            } else if($data->status == 3){
                                                                                echo $student_status = "Cancelled";
                                                                            } else if($data->status == 4){
                                                                                echo $student_status = "Withdrawn";
                                                                            } else if($data->status == 5){
                                                                                echo $student_status = "Deferred";
                                                                            } else if($data->status == 6){
                                                                                echo $student_status = "Future";
                                                                            } else if($data->status == 7){
                                                                                echo $student_status = "VOE";
                                                                            } else if($data->status == 8){
                                                                                echo $student_status = "Refund";
                                                                            }?>
                                                                        </td>
                                                                        <td style="display: none;"><?php echo $data->id;?></td>
                                                                      
                                                                        <td><textarea class="note-field" data-studentid="<?php echo $data->id;?>"><?php echo $data->student_add_notes;?></textarea></td>

                                                                        <td style="white-space: initial;">
                                                                            <div class="dropdown d-inline">
                                                                                <button style="margin-top:3px; margin-bottom:3px;" class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                                                <div class="dropdown-menu">
                                                                                    <button class="btn btn-sm btn-primary dropdown-item change-status-btn" data-id="<?php echo $data->id; ?>" data-current-status="<?php echo $data->status; ?>" data-bs-toggle="modal" data-bs-target="#changeStatusModal">Change Status</button>
                                                                                    <!--<a href="javascript:;" datatype="note" class="btn btn-sm btn-primary dropdown-item create_student_note" data-studentid="<?php echo $data->client_id; ?>" data-studentrefno="<?php //echo $data->client_reference; ?>"  data-collegename="<?php //echo $data->partner_name; ?>">Add Student Note</a>-->
                                                                                    
                                                                                    <button class="btn btn-sm btn-primary dropdown-item change-application-overall-status-btn" data-id="<?php echo $data->id; ?>" data-application-overall-status="<?php echo $data->overall_status; ?>" data-bs-toggle="modal" data-bs-target="#changeApplicationOverallStatusModal">Change Application To Inactive</button>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                <?php
                                                                } //end foreach?>
                                                            </tbody>
                                                          
                                                            
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="stdinactive" role="tabpanel" aria-labelledby="stdinactive-tab">
                                                <div class="student_drop_table_data1" style="display: inline-block;margin-right: 10px;">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"><i class="fas fa-columns"></i></button>
                                                    <div class="dropdown_list student_dropdown_list1">
                                                        <label class="dropdown-option all"><input type="checkbox" value="all" checked /> Display All</label>
                                                        <label class="dropdown-option"><input type="checkbox" value="3" checked /> Student Name</label>
                                                        <label class="dropdown-option"><input type="checkbox" value="4" checked /> Date of Birth</label>
                                                        <label class="dropdown-option"><input type="checkbox" value="5" checked /> Student Id</label>
                                                        <label class="dropdown-option"><input type="checkbox" value="6" checked /> College Name</label>
                                                        <label class="dropdown-option"><input type="checkbox" value="7" checked /> Course Name</label>
                                                        <label class="dropdown-option"><input type="checkbox" value="8" checked /> Start Date</label>
                                                        <label class="dropdown-option"><input type="checkbox" value="9" checked /> End Date</label>
                                                        <label class="dropdown-option"><input type="checkbox" value="10" checked /> Total Course Fee</label>
                                                        <label class="dropdown-option"><input type="checkbox" value="11" checked /> Enrolment Fee</label>
                                                        <label class="dropdown-option"><input type="checkbox" value="12" checked /> Material Fee</label>
                                                        <label class="dropdown-option"><input type="checkbox" value="13" checked /> Tution Fee</label>
                                                        <label class="dropdown-option"><input type="checkbox" value="14" checked /> Fee Reported by College</label>
                                                        <label class="dropdown-option"><input type="checkbox" value="15" checked /> Total Bonus</label>
                                                        <label class="dropdown-option"><input type="checkbox" value="16" checked /> Bonus Pending</label>
                                                        <label class="dropdown-option"><input type="checkbox" value="17" checked /> Scholarship Fee</label>

                                                        <label class="dropdown-option"><input type="checkbox" value="18" checked /> Commission as per Fee reported</label>
                                                        <label class="dropdown-option"><input type="checkbox" value="19" checked /> Commission payable as per anticipated fee</label>
                                                        <label class="dropdown-option"><input type="checkbox" value="20" checked /> Commission paid as per fee Reported</label>
                                                        <label class="dropdown-option"><input type="checkbox" value="21" checked /> Commission Pending</label>
                                                        <label class="dropdown-option"><input type="checkbox" value="22" checked /> Student Status</label>
                                                    </div>
                                                </div>
                                                <div class="table-responsive student_table_data1">
                                                    <table class="table text_wrap table-31">
                                                        <thead>
                                                            <tr>
                                                                <th>SNo.</th>
                                                                <th>CRM Ref</th>
                                                                <th>Student Name</th>
                                                                <th>Date of Birth</th>
                                                                <th>Student Id</th>
                                                                <th>College Name</th>
                                                                <th>Course Name</th>
                                                                <th>Start Date</th>
                                                                <th>End Date</th>
                                                                <th>Total Course Fee</th>
                                                                <th>Enrolment Fee</th>
                                                                <th>Material Fee</th>
                                                                <th>Tution Fee</th>

                                                                <!--<th>Total Anticipated Fee</th>-->
                                                                <th>Fee Reported by College</th>
                                                                <th>Total Bonus</th>
                                                                <th>Bonus Pending</th>
                                                                <th>Scholarship Fee</th>
                                                                <!--<th>Bonus Paid</th>-->
                                                                <!--<th>Commission as per anticipated fee</th>-->
                                                                <th>Commission as per Fee reported</th>
                                                                <th>Commission payable as per anticipated fee</th>
                                                                <th>Commission paid as per fee Reported</th>
                                                                <th>Commission Pending</th>

                                                                <th>Student Status</th>
                                                                <th style="display: none;">Student ID</th> <!-- Hidden column -->
                                                                <th>Add Note</th>
                                                                <th>Action</th>

                                                            </tr>
                                                        </thead>
                                                        <tbody class="invoicedatalist">
                                                            <?php
                                                            //dd($fetchedData->id);
                                                            $studentdatas1 = \App\Models\Application::join('admins', 'applications.client_id', '=', 'admins.id')
                                                            ->leftJoin('partners', 'applications.partner_id', '=', 'partners.id')
                                                            ->leftJoin('products', 'applications.product_id', '=', 'products.id')
                                                            ->leftJoin('application_fee_options', 'applications.id', '=', 'application_fee_options.app_id')
                                                            ->select(
                                                                'applications.*',
                                                                'admins.client_id as client_reference',
                                                                'admins.first_name',
                                                                'admins.last_name',
                                                                'admins.dob',
                                                                'partners.partner_name',
                                                                'products.name as coursename',
                                                                'application_fee_options.total_course_fee_amount',
                                                                'application_fee_options.enrolment_fee_amount',
                                                                'application_fee_options.material_fees',
                                                                'application_fee_options.tution_fees',
                                                                'application_fee_options.total_anticipated_fee',
                                                                'application_fee_options.fee_reported_by_college',
                                                                'application_fee_options.bonus_amount',
                                                                'application_fee_options.bonus_pending_amount',
                                                                'application_fee_options.bonus_paid',
                                                                'application_fee_options.scholarship_fee_amount',
                                                                'application_fee_options.commission_as_per_anticipated_fee',
                                                                'application_fee_options.commission_as_per_fee_reported',
                                                                'application_fee_options.commission_payable_as_per_anticipated_fee',
                                                                'application_fee_options.commission_paid_as_per_fee_reported',
                                                                'application_fee_options.commission_pending'
                                                            )
                                                            ->where('applications.partner_id', $fetchedData->id)
                                                            ->where('applications.overall_status', 1) //overall status = Inactive
                                                            ->where(function ($query) {
                                                                $query->where('applications.stage', 'Coe issued')
                                                                        ->orWhere('applications.stage', 'Enrolled')
                                                                        ->orWhere('applications.stage', 'Coe Cancelled');
                                                            })
                                                            ->orderBy('applications.created_at', 'ASC')
                                                            ->get(); //dd($studentdatas1);

															foreach($studentdatas1 as $datakey1=>$data1)
                                                            { 
                                                              ?>
                                                                <tr>
                                                                    <td><?php echo ($datakey1+1);?></td>
                                                                    <td>
                                                                        <?php
                                                                        if($data1->client_reference){
                                                                            $client_encoded_id1 = base64_encode(convert_uuencode(@$data1->client_id)) ;
                                                                           echo $client_reference1 = '<a href="'.url('/clients/detail/'.$client_encoded_id1).'" class="activate-app-tab" data-tab="application" data-id="'.$data1->id.'" target="_blank" >'.$data1->client_reference.'</a>';
                                                                        } else {
                                                                            echo $client_reference1 = 'N/P';
                                                                        }?>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                        if($data1->first_name != ""){
                                                                            echo $full_name1 = $data1->first_name.' '.$data1->last_name;
                                                                        } else {
                                                                            echo $full_name1 = 'N/P';
                                                                        } ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                        if($data1->dob != ""){ //1992-02-19 Y-m-d
                                                                            $dobArr1 = explode("-",$data1->dob);
                                                                            echo $dob1 = $dobArr1[2]."/".$dobArr1[1]."/".$dobArr1[0];
                                                                        } else {
                                                                            echo $dob1 = 'N/P';
                                                                        } ?>
                                                                    </td>

                                                                    <td>
                                                                        <?php
                                                                        if($data1->student_id != ""){
                                                                            echo $student_id1 = $data1->student_id;
                                                                        } else {
                                                                            echo $student_id1 = 'N/P';
                                                                        } ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                        if($data1->partner_name != ""){
                                                                            echo $partner_name1 = $data1->partner_name;
                                                                        } else {
                                                                            echo $partner_name1 = 'N/P';
                                                                        } ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                        if($data1->coursename != ""){
                                                                            echo $coursename1 = $data1->coursename;
                                                                        } else {
                                                                            echo $coursename1 = 'N/P';
                                                                        } ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                        if($data1->start_date != ""){
                                                                            echo $start_date1 = date('d/m/Y',strtotime($data1->start_date));
                                                                        } else {
                                                                            echo $start_date1 = 'N/P';
                                                                        } ?>
                                                                    </td>

                                                                    <td>
                                                                        <?php
                                                                        if($data1->end_date != ""){
                                                                            echo $end_date1 = date('d/m/Y',strtotime($data1->end_date));
                                                                        } else {
                                                                            echo $end_date1 = 'N/P';
                                                                        }?>
                                                                    </td>

                                                                    <td>
                                                                        <?php
                                                                        if($data1->total_course_fee_amount != ""){
                                                                            echo $total_course_fee_amount1 = $data1->total_course_fee_amount;
                                                                        } else {
                                                                            echo $total_course_fee_amount1 = '0.00';
                                                                        } ?>
                                                                    </td>

                                                                    <td>
                                                                        <?php
                                                                        if($data1->enrolment_fee_amount != ""){
                                                                            echo $enrolment_fee_amount1 = $data1->enrolment_fee_amount;
                                                                        } else {
                                                                            echo $enrolment_fee_amount1 = '0.00';
                                                                        } ?>
                                                                    </td>

                                                                    <td>
                                                                        <?php
                                                                        if($data1->material_fees != ""){
                                                                            echo $material_fees1 = $data1->material_fees;
                                                                        } else {
                                                                            echo $material_fees1 = '0.00';
                                                                        } ?>
                                                                    </td>

                                                                    <td>
                                                                        <?php
                                                                        if($data1->tution_fees != ""){
                                                                            echo $tution_fees1 = $data1->tution_fees;
                                                                        } else {
                                                                            echo $tution_fees1 = '0.00';
                                                                        } ?>
                                                                    </td>
                                                                    <!--<td>
                                                                        <?php
                                                                        /*if($data1->total_anticipated_fee != ""){
                                                                            echo $total_anticipated_fee1 = $data1->total_anticipated_fee;
                                                                        } else {
                                                                            echo $total_anticipated_fee1 = '0.00';
                                                                        } */?>
                                                                    </td>-->

                                                                    <td>
                                                                        <?php
                                                                        if($data1->fee_reported_by_college != ""){
                                                                            echo $fee_reported_by_college1 = $data1->fee_reported_by_college;
                                                                        } else {
                                                                            echo $fee_reported_by_college1 = '0.00';
                                                                        } ?>
                                                                    </td>

                                                                    <td>
                                                                        <?php
                                                                        if($data1->bonus_amount != ""){
                                                                            echo $bonus_amount1 = $data1->bonus_amount;
                                                                        } else {
                                                                            echo $bonus_amount1 = '0.00';
                                                                        } ?>
                                                                    </td>

                                                                    <td>
                                                                        <?php
                                                                        if($data1->bonus_pending_amount != ""){
                                                                            echo $bonus_pending_amount1 = $data1->bonus_pending_amount;
                                                                        } else {
                                                                            echo $bonus_pending_amount1 = '0.00';
                                                                        } ?>
                                                                    </td>

                                                                    <td>
                                                                        <?php
                                                                        if($data1->scholarship_fee_amount != ""){
                                                                            echo $scholarship_fee_amount1 = $data1->scholarship_fee_amount;
                                                                        } else {
                                                                            echo $scholarship_fee_amount1 = '0.00';
                                                                        } ?>
                                                                    </td>

                                                                    <!--<td>
                                                                        <?php
                                                                        /*if($data1->bonus_paid != ""){
                                                                            echo $bonus_paid1 = $data1->bonus_paid;
                                                                        } else {
                                                                            echo $bonus_paid1 = '0.00';
                                                                        } */?>
                                                                    </td>-->

                                                                    <!--<td>
                                                                        <?php
                                                                        /*if($data1->commission_as_per_anticipated_fee != ""){
                                                                            echo $commission_as_per_anticipated_fee1 = $data1->commission_as_per_anticipated_fee;
                                                                        } else {
                                                                            echo $commission_as_per_anticipated_fee1 = '0.00';
                                                                        }*/ ?>
                                                                    </td>-->

                                                                    <td>
                                                                        <?php
                                                                        if($data1->commission_as_per_fee_reported != ""){
                                                                            echo $commission_as_per_fee_reported1 = $data1->commission_as_per_fee_reported;
                                                                        } else {
                                                                            echo $commission_as_per_fee_reported1 = '0.00';
                                                                        } ?>
                                                                    </td>

                                                                    <td>
                                                                        <?php
                                                                        if($data1->commission_payable_as_per_anticipated_fee != ""){
                                                                            echo $commission_payable_as_per_anticipated_fee1 = $data1->commission_payable_as_per_anticipated_fee;
                                                                        } else {
                                                                            echo $commission_payable_as_per_anticipated_fee1 = '0.00';
                                                                        } ?>
                                                                    </td>

                                                                    <td>
                                                                        <?php
                                                                        if($data1->commission_paid_as_per_fee_reported != ""){
                                                                            echo $commission_paid_as_per_fee_reported1 = $data1->commission_paid_as_per_fee_reported;
                                                                        } else {
                                                                            echo $commission_paid_as_per_fee_reported1 = '0.00';
                                                                        } ?>
                                                                    </td>

                                                                    <td>
                                                                        <?php
                                                                        if($data1->commission_pending != ""){
                                                                            echo $commission_pending1 = $data1->commission_pending;
                                                                        } else {
                                                                            echo $commission_pending1 = '0.00';
                                                                        } ?>
                                                                    </td>

                                                                    <td>
                                                                        <?php
                                                                        if($data1->status == 0){
                                                                            echo $student_status1 = "In Progress";
                                                                        } else if($data1->status == 1){
                                                                            echo $student_status1 = "Completed";
                                                                        } else if($data1->status == 2){
                                                                            echo $student_status1 = "Discontinued";
                                                                        } else if($data1->status == 3){
                                                                            echo $student_status1 = "Cancelled";
                                                                        } else if($data1->status == 4){
                                                                            echo $student_status1 = "Withdrawn";
                                                                        } else if($data1->status == 5){
                                                                            echo $student_status1 = "Deferred";
                                                                        } else if($data1->status == 6){
                                                                            echo $student_status1 = "Future";
                                                                        } else if($data1->status == 7){
                                                                            echo $student_status1 = "VOE";
                                                                        } else if($data1->status == 8){
                                                                            echo $student_status1 = "Refund";
                                                                        }?>
                                                                    </td>
                                                                    <td style="display: none;"><?php echo $data1->id;?></td>
                                                                    <td><textarea class="note-field1" data-studentid="<?php echo $data1->id;?>"><?php echo $data1->student_add_notes;?></textarea></td>
                                                                    
                                                                    <td style="white-space: initial;">
                                                                        <div class="dropdown d-inline">
                                                                            <button style="margin-top:3px; margin-bottom:3px;" class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                                            <div class="dropdown-menu">
                                                                                <button class="btn btn-sm btn-primary dropdown-item change-status-btn" data-id="<?php echo $data1->id; ?>" data-current-status="<?php echo $data1->status; ?>" data-bs-toggle="modal" data-bs-target="#changeStatusModal">Change Status</button>
                                                                                
                                                                                <button class="btn btn-sm btn-primary dropdown-item change-application-overall-status-btn" data-id="<?php echo $data1->id; ?>" data-application-overall-status="<?php echo $data1->overall_status; ?>" data-bs-toggle="modal" data-bs-target="#changeApplicationOverallStatusModal">Change Application To Active</button>
                                                                            </div>
                                                                        </div>
                                                                    </td>

                                                                </tr>
                                                            <?php
                                                            } //end foreach?>
                                                        </tbody>
                                                      
                                                        <tfoot>
                                                            <tr>
                                                                <th colspan="17" style="text-align: right;">Total</th>
                                                                <th id="total_commission_as_per_fee_reported1">0.00</th>
                                                                <th id="total_commission_anticipated1">0.00</th>
                                                                <th id="total_commission_paid_as_per_fee_reported1">0.00</th>
                                                                <th id="total_commission_pending1">0.00</th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                      
                      
                                 <div class="tab-pane fade <?php echo ($activeTab === 'invoice') ? 'show active' : ''; ?>" id="invoice" role="tabpanel" aria-labelledby="invoice-tab">
                                    <div class="row">
										<div class="col-md-12 mt-3 mb-3">
                                            <?php
                                            $studentdataArr = \App\Models\Application::leftJoin('partners', 'applications.partner_id', '=', 'partners.id')
                                            ->leftJoin('application_fee_options', 'applications.id', '=', 'application_fee_options.app_id')
                                            ->select(
                                                'application_fee_options.tution_fees',
                                                'application_fee_options.commission_as_per_fee_reported',
                                                'application_fee_options.commission_pending'
                                            )
                                            ->where('applications.partner_id', $fetchedData->id)
                                            ->where(function ($query) {
                                                $query->where('applications.stage', 'Coe issued')
                                                        ->orWhere('applications.stage', 'Enrolled')
                                                        ->orWhere('applications.stage', 'Coe Cancelled');
                                            })
                                            ->orderBy('applications.created_at', 'ASC')
                                            ->get(); //dd($studentdataArr);
                                            $Total_Projected_Fee = 0;
                                            $Total_Intended_Commission = 0;
                                            $Total_Pending_Commission = 0;
                                            if( !empty($studentdataArr) && count($studentdataArr) >0 ){
                                                foreach ($studentdataArr as $stdkey => $stdvalue) {
                                                    $Total_Projected_Fee += $stdvalue->tution_fees;
                                                    $Total_Intended_Commission += $stdvalue->commission_as_per_fee_reported;
                                                    $Total_Pending_Commission += $stdvalue->commission_pending;
                                                }
                                            }

                                            $Total_Amount_Invoiced = DB::table('partner_student_invoices')->where('partner_id',$fetchedData->id)->where('invoice_type',1)->sum('amount_aud');
                                            //dd($Total_Amount_Invoiced);

                                            $Total_Payment_Received = DB::table('partner_student_invoices')->where('partner_id',$fetchedData->id)->where('invoice_type',3)->sum('amount_aud');
                                            //dd($Total_Payment_Received);
                                            ?>
                                            <div class="list-group">
                                                <a class="list-group-item list-group-item-action" href="#">Total Projected Fee - <input style="margin-left:84px;border: 1px solid grey;border-radius: 5px;" type="text" id="Total_Projected_Fee" value="<?php echo "$".$Total_Projected_Fee;?>" readonly></a>
                                                <a class="list-group-item list-group-item-action" href="javascript:;">Total Amount Invoiced  - <input style="margin-left:65px;border: 1px solid grey;border-radius: 5px;" type="text" id="Total_Amount_Invoiced" value="<?php echo "$".$Total_Amount_Invoiced;?>"  readonly></a>
                                                <a class="list-group-item list-group-item-action" href="javascript:;">Total Payment Received -<input style="margin-left:60px;border: 1px solid grey;border-radius: 5px;" type="text" id="Total_Payment_Received" value="<?php echo "$".$Total_Payment_Received;?>" readonly></a>
                                                <a class="list-group-item list-group-item-action" href="javascript:;" >Total Intended Commission - <input style="margin-left:37px;border: 1px solid grey;border-radius: 5px;" type="text" id="Total_Intended_Commission" value="<?php echo "$".$Total_Intended_Commission;?>" readonly></a>
                                                <a class="list-group-item list-group-item-action" href="javascript:;" >Total Pending Commission - <input style="margin-left:41px;border: 1px solid grey;border-radius: 5px;" type="text" id="Total_Pending_Commission" value="<?php echo "$".$Total_Pending_Commission;?>" readonly></a>
                                            </div>
                                        </div>
										<div class="clearfix"></div>
									</div>

                                    <div class="invoices_tabs">
                                        <ul class="nav nav-pills round_tabs" id="client_tabs" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-bs-toggle="tab" id="create_invoice-tab" href="#create_invoice" role="tab" aria-controls="create_invoice" aria-selected="true">Create Invoice</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" id="record_invoice-tab" href="#record_invoice" role="tab" aria-controls="record_invoice" aria-selected="false">Record Invoice</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" id="record_payment-tab" href="#record_payment" role="tab" aria-controls="record_payment" aria-selected="false">Record Payment</a>
                                            </li>
                                        </ul>

                                        <div class="tab-content" id="invoicesContent">
                                          
											 <div class="tab-pane fade show active" id="create_invoice" role="tabpanel" aria-labelledby="create_invoice-tab">
                                                <div class="row">
                                                    <div class="col-md-12 text-end">
                                                        <a class="btn btn-primary createpartnerstudentinvoice" href="javascript:;" data-partnerid="{{ $fetchedData->id }}" role="button"  style="margin-right:5px !important;">Create Invoice</a>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="table-responsive">
                                                    <caption>Invoice</caption>
                                                    <table border="1" style="margin-bottom:0rem !important;" class="table text_wrap table-striped table-hover table-md vertical_align">
                                                        <thead>
                                                            <tr>
                                                                <th>Invoice Date</th>
                                                                <th>Invoice Number</th>
                                                                <th>No Of Students Enrolled</th>
                                                                <th>Amount(Incl GST)</th>
                                                                <th>Sent</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="productitemList">
                                                            <?php
                                                            $receipts_lists = DB::table('partner_student_invoices')
                                                            ->select('invoice_id', DB::raw('COUNT(student_id) as student_count'), DB::raw('SUM(amount_aud) as total_amount_aud'))
                                                            ->where('partner_id',$fetchedData->id)->where('invoice_type',1)->groupBy('invoice_id')->get();
                                                            //dd($receipts_lists);
                                                            if(!empty($receipts_lists) && count($receipts_lists)>0 )
                                                            {
                                                                $total_deposit_amount = 0.00;
                                                                foreach($receipts_lists as $rec_list=>$rec_val)
                                                                {
                                                            ?>
                                                            <tr  id="TrRow_<?php echo $rec_val->invoice_id;?>">
                                                                <td style="padding-top: 5px !important;padding-bottom: 5px !important;">
                                                                    <?php echo $rec_val->invoice_date;?>
                                                                    <?php
                                                                    if(isset($rec_val->uploaded_doc_id) && $rec_val->uploaded_doc_id >0){
                                                                        $client_doc_list = DB::table('documents')->select('id','myfile','client_id','doc_type')->where('id',$rec_val->uploaded_doc_id)->first();
                                                                        if($client_doc_list){
                                                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                                                            $awsUrl =  $client_doc_list->myfile;
                                                                        ?>
                                                                            <a target="_blank" class="link-primary" href="<?php echo $awsUrl;?>"><i class="fas fa-file-pdf"></i></a>
                                                                        <?php
                                                                        }
                                                                    } ?>
                                                                </td>
                                                                <td style="padding-top: 5px !important;padding-bottom: 5px !important;"><?php echo $rec_val->invoice_no;?></td>
                                                                <td style="padding-top: 5px !important;padding-bottom: 5px !important;"><?php echo $rec_val->student_count;?></td>
                                                                <td style="padding-top: 5px !important;padding-bottom: 5px !important;">
                                                                    <?php echo "$".$rec_val->total_amount_aud;?>
                                                                    <a target="_blank" class="link-primary" href="{{URL::to('/partners/printpreviewcreateinvoice')}}/{{$rec_val->invoice_id}}"><i class="fa fa-print" aria-hidden="true"></i></a>
                                                                    <?php if ( isset( $rec_val->sent_option ) && $rec_val->sent_option == 'Yes' ) { ?>
                                                                    <?php } else { ?>
                                                                        <a class="link-primary updatedraftstudentinvoice" href="javascript:;" data-invoiceid="<?php echo $rec_val->invoice_id;?>"><i class="fas fa-pencil-alt"></i></a>
                                                                        <a class="link-primary deletestudentinvoice" href="javascript:;" data-invoiceid="<?php echo $rec_val->invoice_id;?>" data-invoicetype="<?php echo $rec_val->invoice_type;?>" data-partnerid="<?php echo $rec_val->partner_id;?>"><i class="fas fa-trash"></i></a>
                                                                    <?php } ?>
                                                                </td>
                                                                <td style="padding-top: 5px !important;padding-bottom: 5px !important;">
                                                                    <?php if ( isset( $rec_val->sent_option ) && $rec_val->sent_option == 'Yes') { ?>
                                                                        <span><?php echo $rec_val->sent_option."<br/>".$rec_val->sent_date; ?></span>
                                                                    <?php } else { ?>
                                                                        <select name="sent_option"  class="sent_option" data-invoiceid="<?php echo $rec_val->invoice_id;?>">
                                                                            <option value="No" <?php if ($rec_val->sent_option == 'No') echo 'selected'; ?>>No</option>
                                                                            <option value="Yes" <?php if ($rec_val->sent_option == 'Yes') echo 'selected'; ?>>Yes</option>
                                                                        </select>
                                                                    <?php } ?>
																</td>
                                                            </tr>
                                                            <?php
                                                                $total_deposit_amount += $rec_val->total_amount_aud;
                                                            } //end foreach
                                                            ?>
                                                            <tr class="lastRow">
                                                                <td colspan="3" style="text-align:right;">Totals</td>
                                                                <td colspan="2" class="totDepoAmTillNow"><?php echo "$".$total_deposit_amount;?></td>
                                                            </tr>
                                                        <?php } else { ?>
                                                            <tr class="lastRow">
                                                                <td colspan="3" style="text-align:right;">Totals</td>
                                                                <td colspan="2" class="totDepoAmTillNow"><?php echo "$0";?></td>
                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="record_invoice" role="tabpanel" aria-labelledby="record_invoice-tab">
                                                <div class="row">
                                                    <div class="col-md-12 text-end">
                                                        <a class="btn btn-primary createrecordinvoice" href="javascript:;" data-partnerid="{{ $fetchedData->id }}" role="button"  style="margin-right:5px !important;">Create Record Invoice</a>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="table-responsive">
                                                    <caption>Record Invoice</caption>
                                                    <table border="1" style="margin-bottom:0rem !important;" class="table text_wrap table-striped table-hover table-md vertical_align">
                                                        <thead>
                                                            <tr>
                                                                <th>Invoice Date</th>
                                                                <th>Sent Date</th>
                                                                <th>Invoice Number</th>
                                                                <th>Amount(Incl GST)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="productitemList_invoice">
                                                            <?php
                                                            $record_invoices = DB::table('partner_student_invoices')->where('partner_id',$fetchedData->id)->where('invoice_type',2)->get();
                                                            //dd($record_invoices);
                                                            if(!empty($record_invoices) && count($record_invoices)>0 )
                                                            {
                                                                $total_invoice_amount = 0.00;
                                                                foreach($record_invoices as $inv_list=>$inv_val)
                                                                {

                                                            ?>
                                                            <tr  id="TrRecordRow_<?php echo $inv_val->id;?>">
                                                                <td>
                                                                    <?php echo $inv_val->invoice_date;?>
                                                                    <?php
                                                                    if(isset($inv_val->uploaded_doc_id) && $inv_val->uploaded_doc_id >0){
                                                                        $client_inv_doc_list = DB::table('documents')->select('id','myfile','client_id','doc_type')->where('id',$inv_val->uploaded_doc_id)->first();
                                                                        if($client_inv_doc_list){
                                                                            $awsUrl_inv =  $client_inv_doc_list->myfile;
                                                                        ?>
                                                                            <a target="_blank" class="link-primary" href="<?php echo $awsUrl_inv;?>"><i class="fas fa-file-pdf"></i></a>
                                                                        <?php
                                                                        }
                                                                    } ?>
                                                                </td>
                                                                <td><?php echo $inv_val->sent_date;?></td>
                                                                <td><?php echo $inv_val->invoice_no;?></td>
                                                                <td>
                                                                    <?php echo "$".$inv_val->amount_aud;?>
                                                                    <!--<a target="_blank" class="link-primary" href="{{--URL::to('/clients/printpreview')--}}/{{--$rec_val->id--}}"><i class="fa fa-print" aria-hidden="true"></i></a>
                                                                    <a class="link-primary updateclientreceipt" href="javascript:;" data-id="<?php //echo $rec_val->id;?>"><i class="fas fa-pencil-alt"></i></a>-->
                                                                    <a class="link-primary deletestudentrecordinvoice" href="javascript:;" data-uniqueid="<?php echo $inv_val->id;?>" data-invoicetype="<?php echo $inv_val->invoice_type;?>" data-partnerid="<?php echo $inv_val->partner_id;?>"><i class="fas fa-trash"></i></a>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                                $total_invoice_amount += $inv_val->amount_aud;
                                                            } //end foreach
                                                            ?>

                                                            <tr class="lastRow_invoice">
                                                                <td colspan="3" style="text-align:right;">Totals</td>
                                                                <td class="totDepoAmTillNow_invoice"><?php echo "$".$total_invoice_amount;?></td>
                                                            </tr>
                                                            <?php
                                                            } else { ?>
                                                            <tr class="lastRow_invoice">
                                                                <td colspan="3" style="text-align:right;">Totals</td>
                                                                <td class="totDepoAmTillNow_invoice"><?php echo "$0";?></td>
                                                            </tr>
                                                            <?php
                                                            } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="record_payment" role="tabpanel" aria-labelledby="record_payment-tab">
                                                <div class="row">
                                                    <div class="col-md-12 text-end">
                                                        <a class="btn btn-primary createrecordpayment" href="javascript:;" data-partnerid="{{ $fetchedData->id }}" role="button"  style="margin-right:5px !important;">Create Record Payment</a>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="table-responsive">
                                                    <caption>Record Payment</caption>
                                                    <table border="1" style="margin-bottom:0rem !important;" class="table text_wrap table-striped table-hover table-md vertical_align">
                                                        <thead>
                                                            <tr>
                                                                <th>Invoice Number</th>
                                                                <th>Method Received</th>
                                                                <th>Verified By</th>
                                                                <th>Verified Date</th>
                                                                <th>Amount Received</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="productitemList_payment">
                                                            <?php
                                                            $record_payments = DB::table('partner_student_invoices')->where('partner_id',$fetchedData->id)->where('invoice_type',3)->get();
                                                            //dd($record_payments);
                                                            if(!empty($record_payments) && count($record_payments)>0 )
                                                            {
                                                                $total_payment_amount = 0.00;
                                                                foreach($record_payments as $pay_list=>$pay_val)
                                                                {

                                                            ?>
                                                            <tr  id="TrPaymentRow_<?php echo $pay_val->id;?>">
                                                                <td>
                                                                    <?php echo $pay_val->invoice_no;?>
                                                                    <?php
                                                                    if(isset($pay_val->uploaded_doc_id) && $pay_val->uploaded_doc_id >0){
                                                                        $client_pay_doc_list = DB::table('documents')->select('id','myfile','client_id','doc_type')->where('id',$pay_val->uploaded_doc_id)->first();
                                                                        if($client_pay_doc_list){
                                                                            $awsUrl_pay =  $client_pay_doc_list->myfile;
                                                                        ?>
                                                                            <a target="_blank" class="link-primary" href="<?php echo $awsUrl_pay;?>"><i class="fas fa-file-pdf"></i></a>
                                                                        <?php
                                                                        }
                                                                    } ?>
                                                                </td>
                                                                <td><?php echo $pay_val->method_received;?></td>
                                                                <td><?php echo $pay_val->verified_by;?></td>
                                                                <td><?php echo $pay_val->verified_date;?></td>
                                                                <td>
                                                                    <?php echo "$".$pay_val->amount_aud;?>
                                                                    <!--<a target="_blank" class="link-primary" href="{{--URL::to('/clients/printpreview')--}}/{{--$rec_val->id--}}"><i class="fa fa-print" aria-hidden="true"></i></a>
                                                                    <a class="link-primary updateclientreceipt" href="javascript:;" data-id="<?php //echo $rec_val->id;?>"><i class="fas fa-pencil-alt"></i></a>-->
                                                                    <a class="link-primary deletestudentpaymentinvoice" href="javascript:;" data-uniqueid="<?php echo $pay_val->id;?>" data-invoicetype="<?php echo $pay_val->invoice_type;?>" data-partnerid="<?php echo $pay_val->partner_id;?>"><i class="fas fa-trash"></i></a>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                                $total_payment_amount += $pay_val->amount_aud;
                                                            } //end foreach
                                                            ?>

                                                            <tr class="lastRow_payment">
                                                                <td colspan="4" style="text-align:right;">Totals</td>
                                                                <td class="totDepoAmTillNow_payment"><?php echo "$".$total_payment_amount;?></td>
                                                            </tr>
                                                            <?php
                                                            } else { ?>
                                                            <tr class="lastRow_payment">
                                                                <td colspan="4" style="text-align:right;">Totals</td>
                                                                <td class="totDepoAmTillNow_payment"><?php echo "$0";?></td>
                                                            </tr>
                                                            <?php
                                                            } ?>
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
				</div>
			</div>
		</div>
	</section>
</div> 

@include('Admin/partners/addpartnermodal')  
@include('Admin/partners/editpartnermodal')   

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

<div class="modal fade addbranch custom_modal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">Add New Branch</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" id="branchform" autocomplete="off" enctype="multipart/form-data">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch_name">Name <span class="span_req">*</span></label>
								{!! Form::text('branch_name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Name' ))  !!}
								<span class="custom-error branch_name_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch_email">Email <span class="span_req">*</span></label>
								{!! Form::text('branch_email', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Email' ))  !!}
									<span class="custom-error branch_email_error" role="alert">
										<strong></strong>
									</span> 
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group"> 
								<label for="branch_country">Country</label>
							<select class="form-control branch_country select2" name="branch_country" >
								<option value="">Select</option>
								<?php
								foreach(\App\Models\Country::all() as $list){
									?>
									<option value="{{@$list->name}}">{{@$list->name}}</option>
									<?php
								}
								?>
							</select>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch_city">City</label>
								{!! Form::text('branch_city', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter City' ))  !!}
								@if ($errors->has('branch_city'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('branch_city') }}</strong>
									</span> 
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group"> 
								<label for="branch_state">State</label>
								{!! Form::text('branch_state', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter State' ))  !!}
								@if ($errors->has('branch_state'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('branch_state') }}</strong>
									</span> 
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group"> 
								<label for="branch_address">Street</label>
								{!! Form::text('branch_address', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Street' ))  !!}
								@if ($errors->has('branch_address'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('branch_address') }}</strong>
									</span> 
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group"> 
								<label for="branch_zip">Zip Code</label>
								{!! Form::text('branch_zip', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Zip / Post Code' ))  !!}
								@if ($errors->has('branch_zip'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('branch_zip') }}</strong>
									</span> 
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group"> 
								<label for="branch_phone">Phone</label>
								<div class="cus_field_input"> 
									<div class="country_code"> 
										@include('partials.country-code-select', [
											'name' => 'brnch_country_code',
											'selected' => old('brnch_country_code', \App\Helpers\PhoneHelper::getDefaultCountryCode())
										])
									</div>	
									{!! Form::text('branch_phone', '', array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Phone' ))  !!}
									@if ($errors->has('branch_phone'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('branch_phone') }}</strong>
										</span> 
									@endif
								</div> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button type="button" class="btn btn-primary savebranch">Save</button>
							<button type="button" id="update_branch" style="display:none" class="btn btn-primary">Update</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade  custom_modal " id="interest_service_view" tabindex="-1" role="dialog" aria-labelledby="interest_serviceModalLabel">
	<div class="modal-dialog">
		<div class="modal-content showinterestedservice">
			
		</div>
	</div>
</div>

<div id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn-close"></button>
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
				<button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn-close"></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to delete this note?</h4> 
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accepteducation">Delete</button> 
				<button type="button" style="margin-top: 40px;" data-bs-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeStatusModalLabel">Change Student Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
            </div>
            <div class="modal-body">
                <form id="changeStatusForm">
                    <input type="hidden" name="student_id" id="studentId">
                    <div class="mb-3">
                        <label for="newStatus" class="form-label">Select Status</label>
                        <select class="form-select" id="newStatus" name="new_status">
                            <option value="0">In Progress</option>
                            <option value="1">Completed</option>
                            <option value="2">Discontinued</option>
                            <option value="3">Cancelled</option>
                            <option value="4">Withdrawn</option>
                            <option value="5">Deferred</option>
                            <option value="6">Future</option>
                            <option value="7">VOE</option>
                            <option value="8">Refund</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="changeApplicationOverallStatusModal" tabindex="-1" aria-labelledby="changeApplicationOverallStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeApplicationOverallStatusModalLabel">Application Overall Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
            </div>
            <div class="modal-body">
                <h6>Do you want to change application overall status?</h6>
                <form id="changeApplicationOverallStatusForm">
                    <input type="hidden" name="application_student_id" id="applicationStudentId">
                    <input type="hidden" name="application_overall_status" id="applicationOverallStatus" value="">
                    <button type="submit" class="btn btn-primary">Change</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<!-- jQuery Confirm for custom confirmation box -->
<script src="https://cdn.jsdelivr.net/npm/jquery-confirm@3.3.0/dist/jquery-confirm.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery-confirm@3.3.0/dist/jquery-confirm.min.css">

<style>
    /* Custom styles for datepicker fields */
    .datepicker-input {
        width: 200px;
        padding: 5px;
        margin-top: 10px;
    }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    
    //Change status
    document.querySelectorAll('.change-status-btn').forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.getAttribute('data-id');
            const currentStatus = this.getAttribute('data-current-status');
            document.getElementById('studentId').value = studentId;
            document.getElementById('newStatus').value = currentStatus;
        });
    });

    document.getElementById('changeStatusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        //console.log(formData);
        fetch('/partners/update-student-status', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(response => response.json())
          .then(data => {
               //alert(data.message);
               //location.reload(); // Reload to reflect changes

                if (data.status) {

                    $('#changeStatusModal').modal('hide');
                    // Update the status in the DataTable without reloading
                    //const studentId = formData.get('studentId'); // Extract student ID from form data
                    //const newStatus = formData.get('newStatus'); // Extract new status from form data

                    const studentId = data.studentId; // Extract student ID from form data
                    const newStatus = data.newStatus; // Extract new status from form data
                    const newStatus_id = data.newStatus_id; // Extract new status from form data

                    //console.log('studentId='+studentId);
                    //console.log('newStatus='+newStatus);
                    // Locate the row in the DataTable
                    const table = $('.table-3').DataTable(); // Replace `#myTable` with your table ID or class
                    const rowIndex = table.rows().eq(0).filter((rowIdx) => {
                        //console.log(table.cell(rowIdx, 21).data());
                        return table.cell(rowIdx, 22).data() == studentId; // Match student ID column
                    });

                    // Update the cell value
                    if (rowIndex.length > 0) {
                        table.cell(rowIndex[0], 21).data(newStatus).draw(); // Update the status column
                         $('.change-status-btn[data-id="' + studentId + '"]').attr('data-current-status', newStatus_id);
                    }

                    //alert(data.message); // Show success message
                    $('.custom-error-msg').html('<span class="alert alert-success">'+data.message+'</span>');
                } else {
                    //alert(data.message); // Show error message
                    $('.custom-error-msg').html('<span class="alert alert-danger">'+data.message+'</span>');
                }
          }).catch(error => console.error('Error:', error));
    });
    
    //Change Application overall status
    document.querySelectorAll('.change-application-overall-status-btn').forEach(button => {
        button.addEventListener('click', function() {
            const applicationStudentId = this.getAttribute('data-id');
            const applicationOverallStatus = this.getAttribute('data-application-overall-status');
            document.getElementById('applicationStudentId').value = applicationStudentId;
            document.getElementById('applicationOverallStatus').value = applicationOverallStatus;
        });
    });

    document.getElementById('changeApplicationOverallStatusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('/partners/update-student-application-overall-status', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(response => response.json())
          .then(data => {
              alert(data.message);
              location.reload(); // Reload to reflect changes
          }).catch(error => console.error('Error:', error));
    });
    
});
  
jQuery(document).ready(function($){
  
     $(document).on('click', '.activate-app-tab', function () {
        const tab = $(this).data('tab'); // Get the tab from the custom attribute
        const appliid = $(this).data('id'); // Get the application ID
        //console.log('tab='+tab)
        //console.log('appliid='+appliid)
        localStorage.setItem('activeTab', tab);
        localStorage.setItem('appliid', appliid);
    });
  
    //Start Note deadline checkbox and text box field change
    const $checkbox = $('#note_deadline_checkbox');
    const $deadlineInput = $('#note_deadline');
    const $recurringSection = $('#recurring_type_section');

    function getCurrentDate() {
        const today = new Date();
        const day = String(today.getDate()).padStart(2, '0');
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const year = today.getFullYear();
        return `${day}/${month}/${year}`;
    }

    $checkbox.change(function () {
        if ($(this).is(':checked')) {
            $deadlineInput.prop('disabled', false);
            $recurringSection.show();
        } else {
            //$deadlineInput.prop('disabled', true).val('');
            $deadlineInput.prop('disabled', true).val(getCurrentDate());
            $recurringSection.hide();
        }
    });

    $deadlineInput.on('input', function () {
        if ($checkbox.is(':checked') && $(this).val().trim() !== '') {
            $recurringSection.show();
        } else {
            $recurringSection.hide();
        }
    });
    //End Note deadline checkbox and text box field change
  
    if (typeof flatpickr !== 'undefined') {
        flatpickr('#popoverdatetime,#note_deadline', {
            dateFormat: 'd/m/Y',
            defaultDate: 'today',
            allowInput: true
        });
    }


    //application stages assign user
    $(document).delegate('.openpartneraction', 'click', function(){
        $('#create_partneraction').modal('show');
    });
  
    $('#note_deadline_checkbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#note_deadline').prop('disabled', false);
            $('#note_deadline_checkbox').val(1);
        } else {
            $('#note_deadline').prop('disabled', true);
            $('#note_deadline_checkbox').val(0);
        }
    });
  
   //////////////////////////////
    //////////////////////////////
    //upload inbox/sent email start
    //////////////////////////////
    //////////////////////////////
    $(document).delegate('.partnerUploadAndFetchMail','click', function(){
		$('#mapartner_id_fetch').val('{{$fetchedData->id}}');
        $('#partnerUploadAndFetchMail').modal('show');
	});

    $(document).delegate('.partnerUploadSentAndFetchMail','click', function(){
		$('#mapartner_id_fetch_sent').val('{{$fetchedData->id}}');
        $('#partnerUploadSentAndFetchMail').modal('show');
	});

    //////////////////////////////
    //////////////////////////////
    //upload inbox/sent email end
    //////////////////////////////
    //////////////////////////////
  
   

    //////////////////////////////
    //////////////////////////////
    //create student invoice start
    //////////////////////////////
    //////////////////////////////
    if (typeof flatpickr !== 'undefined') {
        $('.invoice_date_fields').each(function() {
            flatpickr(this, {
                dateFormat: 'd/m/Y',
                defaultDate: 'today',
                allowInput: true
            });
        });
    }

    $(document).delegate('.openproductrinfo', 'click', function(){
		//var clonedval = $('.clonedrow').html();
        var clonedval =
                    ` <td>
                        <input name="id[]" type="hidden" value="" />
                        <select data-valid="required" class="form-control student_no_cls" name="student_id[]">
                        </select>
                    </td>
                    <td>
                        <input data-valid="required" class="form-control student_dob" name="student_dob[]" type="text" value="" />
                        <input class="form-control student_name" name="student_name[]" type="hidden" value="" />
                        <input class="form-control student_ref_no" name="student_ref_no[]" type="hidden" value="" />
                    </td>
                    <td>
                        <input data-valid="required" class="form-control student_course_name" name="course_name[]" type="text" value="" />
                    </td>
                    <td>
                        <input data-valid="required" class="form-control student_info_id" name="student_info_id[]" type="text" value="" />
                    </td>
                    <td>
                        <input data-valid="required" class="form-control" name="description[]" type="text" value="" />
                    </td>
                    <td>
                        <span class="currencyinput" style="display: inline-block;">$</span>
                        <input style="display: inline-block;" data-valid="required" class="form-control deposit_amount_per_row" type="text" value="" readonly/>
                        <input class="form-control deposit_amount_per_row_hidden" name="amount_aud[]" type="hidden" value="" />
                    </td>
                    <td>
                        <a class="removeitems" href="javascript:;"><i class="fa fa-times"></i></a>
                    </td>`;
        $('.productitem').append('<tr class="product_field_clone">'+clonedval+'</tr>');

        //Set student drop down for newly added row
        var partnerid =  $('#partner_id').val();
        $.ajax({
            type:'post',
            url: '{{URL::to('/partners/getEnrolledStudentList')}}',
            sync:true,
            data: {partnerid:partnerid},
            success: function(response){
                var obj = $.parseJSON(response); //console.log('record_get=='+obj.record_get);
                $('.student_no_cls').last().html(obj.record_get);
            }
        });
        if (typeof flatpickr !== 'undefined') {
            var lastField = $('.invoice_date_fields').last()[0];
            if (lastField) {
                flatpickr(lastField, {
                    dateFormat: 'd/m/Y',
                    defaultDate: 'today',
                    allowInput: true
                });
            }
        }
    });

    $(document).delegate('.removeitems', 'click', function(){
		var $tr    = $(this).closest('.product_field_clone');
		var trclone = $('.product_field_clone').length;
		if(trclone > 0){
            $tr.remove();
		}
		grandtotalAccountTab();
    });

    function grandtotalAccountTab(){
        var total_deposit_amount_all_rows = 0;
        $('.productitem tr').each(function(){
            if($(this).find('.deposit_amount_per_row_hidden').val() != ''){
                var deposit_amount_per_row = $(this).find('.deposit_amount_per_row_hidden').val();
            }else{
                var deposit_amount_per_row = 0;
            }
            //console.log('deposit_amount_per_row='+deposit_amount_per_row);
            total_deposit_amount_all_rows += parseFloat(deposit_amount_per_row);
        });
        //console.log('total_deposit_amount_all_rows='+total_deposit_amount_all_rows);
        $('.total_deposit_amount_all_rows').html("$"+total_deposit_amount_all_rows.toFixed(2));
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function getTopReceiptValInDB(type) {
        $.ajax({
            type:'post',
            url: '{{URL::to('/partners/getTopReceiptValInDB')}}',
            sync:true,
            data: {type:type},
            success: function(response){
                var obj = $.parseJSON(response); //console.log('record_count=='+obj.record_count);
                if(obj.invoice_type == 1){ //create student invoice
                    if(obj.record_count >0){
                        $('#top_value_db').val(obj.record_count);
                    } else {
                        $('#top_value_db').val(obj.record_count);
                    }
                }
                else if(obj.invoice_type == 2){ //record student invoice
                    if(obj.record_count >0){
                        $('#top_value_db_invoice').val(obj.record_count);
                    } else {
                        $('#top_value_db_invoice').val(obj.record_count);
                    }
                }
                else if(obj.invoice_type == 3){ //record student payment
                    if(obj.record_count >0){
                        $('#top_value_db_payment').val(obj.record_count);
                    } else {
                        $('#top_value_db_payment').val(obj.record_count);
                    }
                }
            }
        });
    }

    function getTopInvoiceValInDB(type) {
        $.ajax({
            type:'post',
            url: '{{URL::to('/partners/getTopInvoiceValInDB')}}',
            sync:true,
            data: {type:type},
            success: function(response){
                var obj = $.parseJSON(response);
                if(obj.invoice_type == 1){ //create student invoice
                    $('.unique_trans_no').val(obj.max_invoice_id);
                    $('.unique_trans_no_hidden').val(obj.max_invoice_id);
                    $('#createpartnerstudentinvoicemodal').modal('show');
                }
                /*else if(obj.invoice_type == 2){ //record student invoice
                    $('.unique_trans_no_invoice').val(obj.max_invoice_id);
                    $('.unique_trans_no_hidden_invoice').val(obj.max_invoice_id);
                    $('#createrecordinvoicemodal').modal('show');
                }
                else if(obj.invoice_type == 3){ //record student payment
                    if(obj.record_count >0){
                        $('#top_value_db_payment').val(obj.record_count);
                    } else {
                        $('#top_value_db_payment').val(obj.record_count);
                    }
                }*/
            }
        });
    }

    function getEnrolledStudentList(partnerid) {
        //console.log('partnerid='+partnerid);
        $.ajax({
            type:'post',
            url: '{{URL::to('/partners/getEnrolledStudentList')}}',
            sync:true,
            data: {partnerid:partnerid},
            success: function(response){
                var obj = $.parseJSON(response); //console.log('record_get=='+obj.record_get);
                $('.student_no_cls').html(obj.record_get);
            }
        });
    }

    $(document).delegate('.deposit_amount_per_row', 'keyup', function(){
        grandtotalAccountTab();
    });

    $(document).delegate('.createpartnerstudentinvoice', 'click', function(){
        var partnerid =  $(this).attr('data-partnerid');
        getTopReceiptValInDB(1);
        getEnrolledStudentList(partnerid);
        $('#function_type').val("add");
        getTopInvoiceValInDB(1); //Get Unique invoice id
        //$('#createpartnerstudentinvoicemodal').modal('show');
    });

    $('#createpartnerstudentinvoicemodal').on('show.bs.modal', function() {
        $('.modal-dialog').css('max-width', '100%');
    });


    $(document).delegate('.student_no_cls', 'change', function(){
        var student_no_cls = $(this);
        //Get Student Info
        var sel_student_id = $(this).val();
        //console.log("sel_student_id:", sel_student_id);
        if(sel_student_id != ""){
            $.ajax({
                type:'post',
                url: '{{URL::to('/partners/getStudentInfo')}}',
                sync:true,
                data: {sel_student_id:sel_student_id},
                success: function(response){
                    var obj = $.parseJSON(response);
                    student_no_cls.closest('tr').find('.student_dob').val(obj.student_db);
                    student_no_cls.closest('tr').find('.student_name').val(obj.student_name);
                    student_no_cls.closest('tr').find('.student_ref_no').val(obj.student_ref_no);
                }
            });
        } else {
            student_no_cls.closest('tr').find('.student_dob').val("");
        }

        //Get Student Course info and student id
        if(sel_student_id != "")
        {
            var partner_id = $('#partner_id').val();
            $.ajax({
                type:'post',
                url: '{{URL::to('/partners/getStudentCourseInfo')}}',
                sync:true,
                data: {sel_student_id:sel_student_id,partner_id:partner_id},
                success: function(response){
                    var obj = $.parseJSON(response);
                    student_no_cls.closest('tr').find('.student_course_name').val(obj.student_course_info.coursename);
                    student_no_cls.closest('tr').find('.student_info_id').val(obj.student_course_info.student_id);
                    if(obj.student_course_info.commission_pending != ""){
                        var commission_pending = obj.student_course_info.commission_pending;
                    } else {
                        var commission_pending = 0;
                    }
                    student_no_cls.closest('tr').find('.deposit_amount_per_row').val(commission_pending);
                    student_no_cls.closest('tr').find('.deposit_amount_per_row_hidden').val(commission_pending);
                    calculateTotalDeposit(); // Recalculate total after reset
                }
            });
        } else {
            student_no_cls.closest('tr').find('.student_course_name').val("");
            student_no_cls.closest('tr').find('.student_info_id').val("");
            student_no_cls.closest('tr').find('.deposit_amount_per_row').val("");
            student_no_cls.closest('tr').find('.deposit_amount_per_row_hidden').val(0);
            calculateTotalDeposit(); // Recalculate total after reset
        }
        //grandtotalAccountTab();
        /*setTimeout(function() {
            calculateTotalDeposit(); // Recalculate total after reset
        }, 5000);*/
    });

    // Function to calculate the total deposit
    function calculateTotalDeposit() {
        var total_deposit_amount_all_rows = 0;

        $('.productitem tr').each(function () {
            var row = $(this); // Current row

            // Debugging: Log the row content
            //console.log('Row HTML:', row.html());

            // Retrieve and parse the value of the deposit amount
            var deposit_amount_per_row = parseFloat(row.closest('tr').find('.deposit_amount_per_row_hidden').val()) || 0;

            console.log('Parsed deposit amount:', deposit_amount_per_row); // Debugging: Log parsed value
            total_deposit_amount_all_rows += deposit_amount_per_row;
        });

        console.log('total_deposit_amount_all_rows=' + total_deposit_amount_all_rows);
        $('.total_deposit_amount_all_rows').html("$" + total_deposit_amount_all_rows.toFixed(2));
    }

    // Function to change sent option to Yes
	$(document).delegate('.sent_option', 'change', function(){
        var $select = $(this);
        var sel_invoice_id = $(this).attr('data-invoiceid'); //console.log("sel_invoice_id:", sel_invoice_id);
        var sel_option_val = $(this).val(); //console.log("sel_option_val:", sel_option_val);
        if(sel_invoice_id != "" && sel_option_val == 'Yes'){
            $.confirm({
                title: 'Are you sure you want to confirm and send the invoice?',
                content: `
                    <label for="sent-date">Sent Date:</label>
                    <input type="text" id="sent-date" data-valid="required" class="datepicker-input" placeholder="Select sent date"><br>
                `,
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        action: function () {
                            var sentDate = $('#sent-date').val();
                            if (sentDate) {
                                //alert('Confirmed! Sent Date: ' + sentDate);
                                $.ajax({
                                    type:'post',
                                    url: '{{URL::to('/partners/updateInvoiceSentOptionToYes')}}',
                                    sync:true,
                                    data: {sel_invoice_id:sel_invoice_id,sentDate:sentDate},
                                    success: function(response){
                                        //location.reload();
                                        $('#TrRow_'+sel_invoice_id).find('td:last-child select').remove();
                                        $('#TrRow_'+sel_invoice_id).find('td:last-child').html('<span> Yes <br>'+ sentDate+'</span>');
                                        $('#TrRow_' + sel_invoice_id + ' td:nth-child(4) .updatedraftstudentinvoice').remove();
                                        $('#TrRow_' + sel_invoice_id + ' td:nth-child(4) .deletestudentinvoice').remove();
                                    }
                                });
                            } else {
                                alert('Please select sent date.');
                                return false; // Prevent the confirm box from closing
                            }
                        }
                    },
                    cancel: {
                        text: 'Cancel',
                        action: function () {
                            // Action for cancel (do nothing)
                        }
                    }
                },
                onContentReady: function () {
                    // Initialize datepickers after the modal content is loaded
                    if (typeof flatpickr !== 'undefined') {
                        flatpickr('#sent-date', {
                            dateFormat: 'd/m/Y',
                            defaultDate: 'today',
                            allowInput: true
                        });
                    }
                }
            });
        }
	});

    //Draft Invoice Click Event
    $(document).delegate('.updatedraftstudentinvoice', 'click', function(){
        var invoiceid = $(this).data('invoiceid');
        //console.log('invoiceid='+invoiceid);
        getInfoByInvoiceId(invoiceid);
    });

    //Get Info By Invoice Id For draft invoice
    function getInfoByInvoiceId(invoiceid) {
        $.ajax({
            type:'post',
            url: '{{URL::to('/partners/getInfoByInvoiceId')}}',
            sync:true,
            data: {invoiceid:invoiceid},
            success: function(response){
                var obj = $.parseJSON(response); //console.log('record_get=='+obj.record_get);
                if(obj.status){
                    $('#invoice_id_val').val(obj.invoiceid);
                    $('#top_value_db').val(obj.last_record_id);
                    $('#function_type').val("edit");
                    $('#createpartnerstudentinvoicemodal').modal('show');
                    if(obj.record_get){
                        var record_get = obj.record_get;
                        var sum = 0;
                        $('.productitem tr.clonedrow').remove();
                        $('.productitem tr.product_field_clone').remove();
                        $.each(record_get, function(index, subArray) {
                            var value_sum = parseFloat(subArray.amount_aud);
                            if (!isNaN(value_sum)) {
                                sum += value_sum;
                            }
                            if(index <1 ){
                                var rowCls = 'clonedrow';
                            } else {
                                var rowCls = 'product_field_clone';
                            }
                            var trRows_invoice = '<tr class="'+rowCls+'"><td><input name="id[]" type="hidden" value="'+subArray.id+'" /><select data-valid="required" class="form-control student_no_cls" name="student_id[]" id="studentnocls_'+subArray.id+'"><option value="">Select</option></select></td><td><input data-valid="required" class="form-control student_dob" name="student_dob[]" type="text" value="'+subArray.student_dob+'"><input class="form-control student_name" name="student_name[]" type="hidden" value="'+subArray.student_name+'"><input class="form-control student_ref_no" name="student_ref_no[]" type="hidden" value="'+subArray.student_ref_no+'"></td><td><input data-valid="required" class="form-control student_course_name" name="course_name[]" type="text" value="'+subArray.course_name+'"></td><td><input data-valid="required" class="form-control student_info_id" name="student_info_id[]" type="text" value="'+subArray.student_info_id+'"></td><td><input data-valid="required" class="form-control" name="description[]" type="text" value="'+subArray.description+'"></td><td><span class="currencyinput" style="display: inline-block;">$</span><input style="display: inline-block;" data-valid="required" class="form-control deposit_amount_per_row" type="text" value="'+subArray.amount_aud+'" readonly=""><input class="form-control deposit_amount_per_row_hidden" name="amount_aud[]" type="hidden" value="'+subArray.amount_aud+'"></td><td><a class="removeitems" href="javascript:;"><i class="fa fa-times"></i></a></td></tr>';
                            $('.productitem').append(trRows_invoice);
                            getEnrolledStudentListInEditMode(subArray.partner_id,subArray.id);
                            if(index < 1){
                                $('.invoice_date_fields').val(subArray.invoice_date);
                                $('.unique_trans_no_hidden').val(subArray.invoice_no);
                                $('.unique_trans_no').val(subArray.invoice_no);
                                $('#invoice_id').val(subArray.invoice_id);
                            }
                        });
                        $('.total_deposit_amount_all_rows').text("$"+sum.toFixed(2));
                    }
                }
            }
        });
    }

    //Delete Student Invoice
    $(document).delegate('.deletestudentinvoice', 'click', function(){
        var invoiceid = $(this).data('invoiceid'); console.log('invoiceid='+invoiceid);
        var invoicetype = $(this).data('invoicetype'); console.log('invoicetype='+invoicetype);
        var partnerid = $(this).data('partnerid'); console.log('partnerid='+partnerid);
        if( invoiceid != "" && confirm('Are you sure you want to delete this invoice?') ) {
            $.ajax({
                type:'post',
                url: '{{URL::to('/partners/deleteStudentRecordByInvoiceId')}}',
                sync:true,
                data: {invoiceid:invoiceid,invoicetype:invoicetype,partnerid:partnerid},
                success: function(response){
                    var obj = $.parseJSON(response);
                    if(obj.status){
                        $('#TrRow_'+obj.invoiceid).remove();
                        $('.totDepoAmTillNow').html("$"+obj.sum);
                    }
                }
            });
        }
    });

    //Get enrolled student list in draft mode
    function getEnrolledStudentListInEditMode(partnerid,uniqueRowId) {
        $.ajax({
            type:'post',
            url: '{{URL::to('/partners/getEnrolledStudentListInEditMode')}}',
            sync:true,
            data: {partnerid:partnerid,uniqueRowId:uniqueRowId},
            success: function(response){
                var obj = $.parseJSON(response); //console.log('record_get##=='+obj.record_get);
                let dropdown = $(".productitem #studentnocls_"+uniqueRowId);
                dropdown.empty();
                dropdown.append(obj.record_get);
            }
        });
    }

    //////////////////////////////
    //////////////////////////////
    //create student invoice end
    //////////////////////////////
    //////////////////////////////


    //////////////////////////////
    //////////////////////////////
    //create record invoice start
    //////////////////////////////
    //////////////////////////////

    if (typeof flatpickr !== 'undefined') {
        $('.record_invoice_date_fields').each(function() {
            flatpickr(this, {
                dateFormat: 'd/m/Y',
                defaultDate: 'today',
                allowInput: true
            });
        });
        $('.record_sent_date_fields').each(function() {
            flatpickr(this, {
                dateFormat: 'd/m/Y',
                defaultDate: 'today',
                allowInput: true
            });
        });
    }

    $(document).delegate('.openproductrinfo_invoice', 'click', function(){
		var clonedval_invoice = $('.clonedrow_invoice').html();
		$('.productitem_invoice').append('<tr class="product_field_clone_invoice">'+clonedval_invoice+'</tr>');
        if (typeof flatpickr !== 'undefined') {
            var lastInvoiceField = $('.record_invoice_date_fields').last()[0];
            var lastSentField = $('.record_sent_date_fields').last()[0];
            if (lastInvoiceField) {
                flatpickr(lastInvoiceField, {
                    dateFormat: 'd/m/Y',
                    defaultDate: 'today',
                    allowInput: true
                });
            }
            if (lastSentField) {
                flatpickr(lastSentField, {
                    dateFormat: 'd/m/Y',
                    defaultDate: 'today',
                    allowInput: true
                });
            }
        }
    });

    $(document).delegate('.removeitems_invoice', 'click', function(){
		var $tr_invoice   = $(this).closest('.product_field_clone_invoice');
		var trclone_invoice = $('.product_field_clone_invoice').length;
		if(trclone_invoice > 0){
            $tr_invoice.remove();
		}
		grandtotalAccountTab_invoice();
    });

    $(document).delegate('.deposit_invoice_amount_per_row', 'keyup', function(){
        grandtotalAccountTab_invoice();
    });

    /*$(document).delegate('.deposit_invoice_amount_per_row', 'blur', function(){
        if( $(this).val() != ""){
            var randomNumber = $('#top_value_db_invoice').val();
            randomNumber = Number(randomNumber);
            randomNumber = randomNumber + 1; //console.log(randomNumber);
            $('#top_value_db_invoice').val(randomNumber);
            randomNumber = "REC"+randomNumber;
            $(this).closest('tr').find('.unique_record_invoice_trans_no').val(randomNumber);
            //$(this).closest('tr').find('.unique_record_invoice_trans_no_hidden').val(randomNumber);
        } else {
            $(this).closest('tr').find('.unique_record_invoice_trans_no').val();
            //$(this).closest('tr').find('.unique_record_invoice_trans_no_hidden').val();
        }
    });*/

    function grandtotalAccountTab_invoice(){
        var total_deposit_amount_all_rows_invoice = 0;
        $('.productitem_invoice tr').each(function(){
            if($(this).find('.deposit_invoice_amount_per_row').val() != ''){
                var deposit_amount_per_row_invoice = $(this).find('.deposit_invoice_amount_per_row').val();
            }else{
                var deposit_amount_per_row_invoice = 0;
            }
            //console.log('deposit_amount_per_row_invoice='+deposit_amount_per_row_invoice);
            total_deposit_amount_all_rows_invoice += parseFloat(deposit_amount_per_row_invoice);
        });
        //console.log('total_deposit_amount_all_rows_invoice='+total_deposit_amount_all_rows_invoice);
        $('.total_deposit_amount_all_rows_invoice').html("$"+total_deposit_amount_all_rows_invoice.toFixed(2));
    }

    $(document).delegate('.createrecordinvoice', 'click', function(){
        var partnerid =  $(this).attr('data-partnerid');
        getTopReceiptValInDB(2);
        $('#function_type_invoice').val("add");
        $('#createrecordinvoicemodal').modal('show');
    });

    $('#createrecordinvoicemodal').on('show.bs.modal', function() {
        $('.modal-dialog').css('max-width', '80%');
    });

    //Delete Student Record Invoice
    $(document).delegate('.deletestudentrecordinvoice', 'click', function(){
        var id = $(this).data('uniqueid'); console.log('id='+id);
        var invoicetype = $(this).data('invoicetype'); console.log('invoicetype='+invoicetype);
        var partnerid = $(this).data('partnerid'); console.log('partnerid='+partnerid);
        if( id != "" && confirm('Are you sure you want to delete this record invoice?') ) {
            $.ajax({
                type:'post',
                url: '{{URL::to('/partners/deleteStudentRecordInvoiceByInvoiceId')}}',
                sync:true,
                data: {id:id,invoicetype:invoicetype,partnerid:partnerid},
                success: function(response){
                    var obj = $.parseJSON(response);
                    if(obj.status){
                        if(obj.invoicetype == 2){ //student record invoice
                            $('#TrRecordRow_'+obj.id).remove();
                            $('.totDepoAmTillNow_invoice').html("$"+obj.sum);
                        }
                    }
                }
            });
        }
    });
    //////////////////////////////
    //////////////////////////////
    //create record invoice end
    //////////////////////////////
    //////////////////////////////



    //////////////////////////////
    //////////////////////////////
    //create record payment start
    //////////////////////////////
    //////////////////////////////

    if (typeof flatpickr !== 'undefined') {
        $('.record_payment_date_fields').each(function() {
            flatpickr(this, {
                dateFormat: 'd/m/Y',
                defaultDate: 'today',
                allowInput: true
            });
        });
    }

    $(document).delegate('.openproductrinfo_payment', 'click', function(){
		var clonedval_payment = $('.clonedrow_payment').html();
		$('.productitem_payment').append('<tr class="product_field_clone_payment">'+clonedval_payment+'</tr>');
        if (typeof flatpickr !== 'undefined') {
            var lastPaymentField = $('.record_payment_date_fields').last()[0];
            if (lastPaymentField) {
                flatpickr(lastPaymentField, {
                    dateFormat: 'd/m/Y',
                    defaultDate: 'today',
                    allowInput: true
                });
            }
        }
    });

    $(document).delegate('.removeitems_payment', 'click', function(){
		var $tr_payment   = $(this).closest('.product_field_clone_payment');
		var trclone_payment = $('.product_field_clone_payment').length;
		if(trclone_payment > 0){
            $tr_payment.remove();
		}
		grandtotalAccountTab_payment();
    });

    $(document).delegate('.deposit_payment_amount_per_row', 'keyup', function(){
        grandtotalAccountTab_payment();
    });

    $(document).delegate('.deposit_payment_amount_per_row', 'blur', function(){
        if( $(this).val() != ""){
            var randomNumber = $('#top_value_db_payment').val();
            randomNumber = Number(randomNumber);
            randomNumber = randomNumber + 1; //console.log(randomNumber);
            $('#top_value_db_payment').val(randomNumber);
            randomNumber = "PAY"+randomNumber;
            $(this).closest('tr').find('.unique_record_payment_trans_no').val(randomNumber);
            $(this).closest('tr').find('.unique_record_payment_trans_no_hidden').val(randomNumber);
        } else {
            $(this).closest('tr').find('.unique_record_payment_trans_no').val();
            $(this).closest('tr').find('.unique_record_payment_trans_no_hidden').val();
        }
    });

    function grandtotalAccountTab_payment(){
        var total_deposit_amount_all_rows_payment = 0;
        $('.productitem_payment tr').each(function(){
            if($(this).find('.deposit_payment_amount_per_row').val() != ''){
                var deposit_amount_per_row_payment = $(this).find('.deposit_payment_amount_per_row').val();
            }else{
                var deposit_amount_per_row_payment = 0;
            }
            //console.log('deposit_amount_per_row_payment='+deposit_amount_per_row_payment);
            total_deposit_amount_all_rows_payment += parseFloat(deposit_amount_per_row_payment);
        });
        //console.log('total_deposit_amount_all_rows_payment='+total_deposit_amount_all_rows_payment);
        $('.total_deposit_amount_all_rows_payment').html("$"+total_deposit_amount_all_rows_payment.toFixed(2));
    }

    function getRecordedInvoiceList(partnerid) {
        //console.log('partnerid='+partnerid);
        $.ajax({
            type:'post',
            url: '{{URL::to('/partners/getRecordedInvoiceList')}}',
            sync:true,
            data: {partnerid:partnerid},
            success: function(response){
                var obj = $.parseJSON(response); //console.log('record_get=='+obj.record_get);
                $('.invoice_no_cls').html(obj.record_get);
            }
        });
    }

    $(document).delegate('.createrecordpayment', 'click', function(){
        var partnerid =  $(this).attr('data-partnerid');
        getTopReceiptValInDB(3);
        getRecordedInvoiceList(partnerid);
        $('#function_type_payment').val("add");
        $('#createrecordpaymentmodal').modal('show');
    });

    $('#createrecordpaymentmodal').on('show.bs.modal', function() {
        $('.modal-dialog').css('max-width', '80%');
    });

     //Delete Student payment Invoice
     $(document).delegate('.deletestudentpaymentinvoice', 'click', function(){
        var id = $(this).data('uniqueid'); console.log('id='+id);
        var invoicetype = $(this).data('invoicetype'); console.log('invoicetype='+invoicetype);
        var partnerid = $(this).data('partnerid'); console.log('partnerid='+partnerid);
        if( id != "" && confirm('Are you sure you want to delete this payment invoice?') ) {
            $.ajax({
                type:'post',
                url: '{{URL::to('/partners/deleteStudentPaymentInvoiceByInvoiceId')}}',
                sync:true,
                data: {id:id,invoicetype:invoicetype,partnerid:partnerid},
                success: function(response){
                    var obj = $.parseJSON(response);
                    if(obj.status){
                        if(obj.invoicetype == 3){ //student payment invoice
                            $('#TrPaymentRow_'+obj.id).remove();
                            $('.totDepoAmTillNow_payment').html("$"+obj.sum);
                        }
                    }
                }
            });
        }
    });
    //////////////////////////////
    //////////////////////////////
    //create record payment end
    //////////////////////////////
    //////////////////////////////
    
   
   $(document).delegate('input[name="apply_to"]', 'change', function () {
        var v = $('input[name="apply_to"]:checked').val();
        if(v == 'All Products'){
            $('.ifselectproducts').hide();
            $('.productselect2').attr('data-valid', '');
        }else{
            $('.ifselectproducts').show();
            $('.productselect2').attr('data-valid', 'required');
        }
    });
  
	$('.productselect2').select2({
      	placeholder: "Select Product",
      	multiple: true,
        width: "100%"
    });

	// Task system removed - December 2025 (dead code - modal is commented out)
	/*$(document).delegate('.opencreate_task', 'click', function () {
        $('#tasktermclientform')[0].reset();
        $('#tasktermclientform select').val('').trigger('change');
        $('.create_task').modal('show');
        $('.ifselecttask').hide();
        $('.ifselecttask select').attr('data-valid', '');

    });*/
  
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
	$(document).delegate('.viewnote', 'click', function(){
		$('#view_note').modal('show');
		var v = $(this).attr('data-id');
		$('#view_note input[name="noteid"]').val(v);
			$('.popuploader').show(); 
		$.ajax({
			url: '{{URL::to('/viewnotedetail')}}',
			type:'GET',
			datatype:'json',
			data:{note_id:v},
			success:function(response){
				$('.popuploader').hide(); 
				var res = JSON.parse(response);
				
				if(res.status){
					$('#view_note .modal-body .note_content h5').html(res.data.title);
					$("#view_note .modal-body .note_content p").html(res.data.description);
					var ad = res.data.admin;                    
					$("#view_note .modal-body .note_content .author").html('<a href="#">'+ad+'</a>');   
					var updated_at = res.data.updated_at;                  
					$("#view_note .modal-body .note_content .lastdate").html('<a href="#">'+updated_at+'</a>');                    
					
				} 
			}
		});
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
	$(document).delegate('.add_clientcontact','click', function(){
		$('#add_clientcontact #appliationModalLabel').html('Add Contact');

		$('#add_clientcontact input[name="contact_id"]').val('');
		$('#add_clientcontact select[name="country_code"]').val('{{ \App\Helpers\PhoneHelper::getDefaultCountryCode() }}');
		$('#add_clientcontact #primary_contact').prop('checked', false);
		$('#add_clientcontact .allinputfields input').val('');
		$('#add_clientcontact .allinputfields select').val('');
		$('#add_clientcontact').modal('show');
	});
	
	$(document).delegate('.openbranchnew','click', function(){
		$('#add_clientbranch #appliationModalLabel').html('Add new branch');

		$('#add_clientbranch input[name="branch_id"]').val('');
		$('#add_clientbranch select[name="country_code"]').val('{{ \App\Helpers\PhoneHelper::getDefaultCountryCode() }}');
		$('#add_clientbranch #head_office').prop('checked', false);
		$('#add_clientbranch .allinputfields input').val('');
		$('#add_clientbranch .allinputfields select').val('');
		$('#add_clientbranch').modal('show');
		
	});
  
  
	<?php
	if(@$fetchedData->contract_expiry != ''){
		?>
		 $('#contract_expiry').val('{{@$fetchedData->contract_expiry}}');
		<?php
	}else{
		?>
		 $('#contract_expiry').val('');
		<?php
	}

	?>

    <?php
	if(@$fetchedData->contract_start != ''){
		?>
		 $('#contract_start').val('{{@$fetchedData->contract_start}}');
		<?php
	}else{
		?>
		 $('#contract_start').val('');
		<?php
	}

	?>
  
   
  
	$(document).delegate('.opentaskview', 'click', function(){
		$('#opentaskview').modal('show');
		var v = $(this).attr('id');
		$.ajax({
			url: site_url+'/partner/get-task-detail',
			type:'GET',
			data:{task_id:v},
			success: function(responses){
				
				$('.taskview').html(responses);
			}
		});
	});
	function getallnotes(){
	$.ajax({
		url: site_url+'/get-notes',
		type:'GET',
		data:{clientid:'{{$fetchedData->id}}',type:'partner'},
		success: function(responses){
			$('.popuploader').hide(); 
			$('.note_term_list').html(responses);
		}
	});
}

function getallactivities(){
	$.ajax({
					url: site_url+'/get-activities',
					type:'GET',
					datatype:'json',
					data:{id:'{{$fetchedData->id}}'},
					success: function(responses){
						var ress = JSON.parse(responses);
						var html = '';
						$.each(ress.data, function(k, v) {
							html += '<div class="activity"><div class="activity-icon bg-primary text-white"><span>'+v.createdname+'</span></div><div class="activity-detail"><div class="mb-2"><span class="text-job">'+v.date+'</span></div><p><b>'+v.name+'</b> '+v.subject+'</p>';
							if(v.message != null){
								html += '<p>'+v.message+'</p>';
							}
							html += '</div></div>';
						});
						$('.activities').html(html);
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
			url: '{{URL::to('/')}}/'+delhref,
			type:'GET',
			datatype:'json',
			data:{note_id:notid,type:'partner'},
			success:function(response){
				$('.popuploader').hide(); 
				var res = JSON.parse(response);
				$('#confirmModal').modal('hide');
				if(res.status){
					$('#note_id_'+notid).remove();
					if(delhref == 'deletedocs'){
						$('.documnetlist #id_'+notid).remove();
					}
					else if(delhref == 'deleteservices'){
						$.ajax({
						url: site_url+'/get-services',
						type:'GET',
						data:{clientid:'{{$fetchedData->id}}'},
						success: function(responses){
							
							$('.interest_serv_list').html(responses);
						}
					});
					}else if(delhref == 'deletecontact'){
						$.ajax({
						url: site_url+'/get-contacts',
						type:'GET',
						data:{clientid:'{{$fetchedData->id}}'},
						success: function(responses){
							
							$('.contact_term_list').html(responses);
						}
					});
					}else if(delhref == 'deletebranch'){
						$.ajax({
						url: site_url+'/get-branches',
						type:'GET',
						data:{clientid:'{{$fetchedData->id}}'},
						success: function(responses){
							
							$('.branch_term_list').html(responses);
						}
					});
					}else{
						getallnotes();
					}
				
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
  
    //Note type change
    $('#noteType').on('change', function() {
        var selectedValue = $(this).val();
        var additionalFields = $("#additionalFields");

        // Clear any existing fields
        additionalFields.html("");

        if(selectedValue === "Call") {
            additionalFields.append(`
                <div class="form-group" style="margin-top:10px;">
                    <label for="mobileNumber">Mobile Number:</label>
                    <select name="mobileNumber" id="mobileNumber" class="form-control" data-valid="required"></select>
                    <span id="mobileNumberError" class="text-danger"></span>
                </div>
            `);

            //Fetch all contact list of any partner at create note popup
            var partner_id = $('#partner_id').val();
            $('.popuploader').show();
            $.ajax({
                url: "{{URL::to('/partners/fetchPartnerContactNo')}}",
                method: "POST",
                data: {partner_id:partner_id},
                datatype: 'json',
                success: function(response) {
                    $('.popuploader').hide();
                    var obj = $.parseJSON(response); //console.log(obj.partnerContacts);
                    var partnerlist = '<option value="">Select Contact</option>';
                    $.each(obj.partnerContacts, function(index, subArray) {
                        if(subArray.partner_country_code != ""){
                            var partner_country_code = subArray.partner_country_code;
                        } else {
                            var partner_country_code = "";
                        }
                        if (subArray.partner_phone == null || subArray.partner_phone === "") {
                            var partner_phone = "";
                        } else {
                            var partner_phone = subArray.partner_phone;
                        }
                        partnerlist += '<option value="'+partner_country_code+' '+subArray.partner_phone+'">'+partner_country_code+' '+subArray.partner_phone+'</option>';
                    });
                    $('#mobileNumber').append(partnerlist);
                }
            });
        }
    });
  
  
    //Add Note To Student
    $(document).delegate('.create_student_note', 'click', function(){
        $('#create_student_note').modal('show');

        $('#student_id').val($(this).attr('data-studentid'));
        $('#student_ref_no').val($(this).attr('data-studentrefno'));
        $('#college_name').val($(this).attr('data-collegename'));


        $('#create_student_note input[name="mailid"]').val(0);
        $('#create_student_note input[name="title"]').val('');
        $('#create_student_note #studentappliationModalLabel').html('Add Note To Student');
        $('#create_student_note input[name="title"]').val('');
        $("#create_student_note .summernote-simple").val('');
        $('#create_student_note input[name="noteid"]').val('');
        $("#create_student_note .summernote-simple").summernote('code','');
        if($(this).attr('datatype') == 'note'){
            $('.is_not_note').hide();
        } else {
            var datasubject = $(this).attr('datasubject');
            var datamailid = $(this).attr('datamailid');
            $('#create_student_note input[name="title"]').val(datasubject);
            $('#create_student_note input[name="mailid"]').val(datamailid);
            $('.is_not_note').show();
        }
    });

    //Student Note Type Change
    $('#studentNoteType').on('change', function() {
        var selectedValue = $(this).val();
        var additionalStudentNoteFields = $("#additionalStudentNoteFields");

        // Clear any existing fields
        additionalStudentNoteFields.html("");

        if(selectedValue === "Call") {
            additionalStudentNoteFields.append(`
                <div class="form-group" style="margin-top:10px;">
                    <label for="mobileNumber">Contact Number:</label>
                    <select name="mobileNumber" id="mobileNumber" class="form-control" data-valid="required"></select>
                    <span id="mobileNumberError" class="text-danger"></span>
                </div>
            `);

            //Fetch all contact list of student at add note to studnet popup
            var client_id = $('#student_id').val();
            $('.popuploader').show();
            $.ajax({
                url: "{{URL::to('/clients/fetchClientContactNo')}}",
                method: "POST",
                data: {client_id:client_id},
                datatype: 'json',
                success: function(response) {
                    $('.popuploader').hide();
                    var obj = $.parseJSON(response); //console.log(obj.partnerContacts);
                    var contactlist = '<option value="">Select Contact</option>';
                    $.each(obj.clientContacts, function(index, subArray) {
                        if(subArray.client_country_code != ""){
                            var client_country_code = subArray.client_country_code;
                        } else {
                            var client_country_code = "";
                        }
                        if (subArray.client_country_code == null || subArray.client_country_code === "") {
                            var client_country_code = "";
                        } else {
                            var client_country_code = subArray.client_country_code;
                        }

                        contactlist += '<option value="'+client_country_code+' '+subArray.client_phone+'">'+client_country_code+' '+subArray.client_phone+' ('+subArray.contact_type+')</option>';
                    });
                    $('#mobileNumber').append(contactlist);
                }
            });
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
$('.js-data-example-ajaxcontact').select2({
		 multiple: true,
		 closeOnSelect: false,
		dropdownParent: $('#opentaskmodal'),
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
	$(document).delegate('.opennoteform', 'click', function(){
		$('#create_note').modal('show');
		$('#create_note #appliationModalLabel').html('Edit Note');
		var v = $(this).attr('data-id');
		$('#create_note input[name="noteid"]').val(v);
			$('.popuploader').show(); 
		$.ajax({
			url: '{{URL::to('/getnotedetail')}}',
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
	
	$(document).delegate('.opencontactform', 'click', function(){
		$('#add_clientcontact').modal('show');
		$('#add_clientcontact #appliationModalLabel').html('Edit Contact');
		var v = $(this).attr('data-id');
		$('#add_clientcontact input[name="contact_id"]').val(v);
			$('.popuploader').show(); 
		$.ajax({
			url: '{{URL::to('/getcontactdetail')}}',
			type:'GET',
			datatype:'json',
			data:{note_id:v},
			success:function(response){
				$('.popuploader').hide(); 
				var res = JSON.parse(response);
				
				if(res.status){
					$('#add_clientcontact input[name="name"]').val(res.data.name);
					$('#add_clientcontact input[name="email"]').val(res.data.contact_email);
					$('#add_clientcontact input[name="phone"]').val(res.data.contact_phone);
					$('#add_clientcontact input[name="fax"]').val(res.data.fax);
					$('#add_clientcontact select[name="country_code"]').val(res.data.countrycode || '');
					$('#add_clientcontact input[name="department"]').val(res.data.department);
					$('#add_clientcontact input[name="position"]').val(res.data.position);
					$('#add_clientcontact #branch').val(res.data.branch);
					if(res.data.primary_contact == 1){
						$('#add_clientcontact #primary_contact').prop('checked', true);
					}else{
						$('#add_clientcontact #primary_contact').prop('checked', false);
					}
					                    
				
				}
			}
		});
	});
	$(document).delegate('.openbranchform', 'click', function(){
		$('#add_clientbranch').modal('show');
		$('#add_clientbranch #appliationModalLabel').html('Edit Contact');
		var v = $(this).attr('data-id');
		$('#add_clientbranch input[name="branch_id"]').val(v);
			$('.popuploader').show(); 
		$.ajax({
			url: '{{URL::to('/getbranchdetail')}}',
			type:'GET',
			datatype:'json',
			data:{note_id:v},
			success:function(response){
				$('.popuploader').hide(); 
				var res = JSON.parse(response);
				
				if(res.status){
					$('#add_clientbranch input[name="name"]').val(res.data.name);
					$('#add_clientbranch input[name="email"]').val(res.data.email);
					$('#add_clientbranch input[name="phone"]').val(res.data.phone);
					$('#add_clientbranch #country').val(res.data.country);
					$('#add_clientbranch select[name="country_code"]').val(res.data.country_code || '');
					$('#add_clientbranch input[name="city"]').val(res.data.city);
					$('#add_clientbranch input[name="state"]').val(res.data.state);
					$('#add_clientbranch input[name="zip_code"]').val(res.data.zip);
					$('#add_clientbranch #branch').val(res.data.branch);
					if(res.data.primary_contact == 1){
						$('#add_clientbranch #head_office').prop('checked', true);
					}else{
						$('#add_clientbranch #head_office').prop('checked', false);
					}
					                    
				
				}
			}
		});
	});
	$(document).delegate('.add_appliation #workflow', 'change', function(){
	
				var v = $('.add_appliation #workflow option:selected').val();
				if(v != ''){
						$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/getpartnerbranch')}}',
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
			url: '{{URL::to('/getbranchproduct')}}',
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
	
	$(document).delegate('.pinnote', 'click', function(){
		$('.popuploader').show(); 
		$.ajax({
			url: '{{URL::to('/pinnote')}}/',
			type:'GET',
			datatype:'json',
			data:{note_id:$(this).attr('data-id')},
			success:function(response){
				getallnotes();
			}
		});
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
		url: '{{URL::to('/change-client-status')}}',
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
		url: '{{URL::to('/get-templates')}}',
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
		url: '{{URL::to('/get-templates')}}',
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
		dropdownParent: $('#create_note'),
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

 $(".table-2").dataTable({
	"searching": false,
	"lengthChange": false,
  "columnDefs": [
    { "sortable": false }
  ],
  order: [[1, "desc"]] //column indexes is zero based

}); 
  

//For student active list
var table33 = $(".table-3").DataTable({
    dom: '<"row"<"col-md-4 text-start"l><"col-md-4 text-center"B><"col-md-4 text-end"f>>rtip',
    buttons: [
        {
            extend: 'excelHtml5',
            text: '<i class="fas fa-file-excel"></i> Excel',
            className: 'btn btn-success btn-sm',
            exportOptions: {
                columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21], // Export all data columns, exclude Add Note (23) and Action (24)
                format: {
                    body: function (data, row, column, node) {
                        // Remove HTML tags and get clean text
                        if (typeof data === 'string') {
                            // Remove HTML tags
                            data = data.replace(/<[^>]*>/g, '');
                            // Decode HTML entities
                            var txt = document.createElement('textarea');
                            txt.innerHTML = data;
                            data = txt.value;
                        }
                        return data || '';
                    }
                }
            },
            filename: function() {
                var partnerName = '{{ $fetchedData->partner_name ?? "Partner" }}';
                return 'Partner_Student_Data_' + partnerName.replace(/[^a-z0-9]/gi, '_') + '_' + new Date().toISOString().split('T')[0];
            },
            title: 'Partner Student Data - {{ $fetchedData->partner_name ?? "Partner" }}',
            messageTop: 'Partner: {{ $fetchedData->partner_name ?? "N/A" }}\nExport Date: ' + new Date().toLocaleString(),
            customize: function(xlsx) {
                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                // Auto-size columns
                $('row c', sheet).attr('s', '50');
            }
        },
        {
            extend: 'csvHtml5',
            text: '<i class="fas fa-file-csv"></i> CSV',
            className: 'btn btn-info btn-sm',
            exportOptions: {
                columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21], // Export all data columns, exclude Add Note (23) and Action (24)
                format: {
                    body: function (data, row, column, node) {
                        // Remove HTML tags and get clean text
                        if (typeof data === 'string') {
                            // Remove HTML tags
                            data = data.replace(/<[^>]*>/g, '');
                            // Decode HTML entities
                            var txt = document.createElement('textarea');
                            txt.innerHTML = data;
                            data = txt.value;
                        }
                        return data || '';
                    }
                }
            },
            filename: function() {
                var partnerName = '{{ $fetchedData->partner_name ?? "Partner" }}';
                return 'Partner_Student_Data_' + partnerName.replace(/[^a-z0-9]/gi, '_') + '_' + new Date().toISOString().split('T')[0];
            }
        }
    ],
    "searching": true,
    "lengthChange": true, // Enable dropdown for page length
    "lengthMenu": [ [10, 20, 50,100,200,500,1000], [10, 20, 50,100,200,500,1000] ], // Dropdown options for pagination
    columnDefs: [
        {
            targets: 0, // Index of the "Sno" column
            orderable: false, // Prevent sorting on this column
            searchable: false, // Prevent searching this column
            render: function (data, type, row, meta) {
                return meta.row + 1; // Display row index starting from 1
            }
        },
        { targets: 22, visible: false } // Hide the Student ID column

    ],
    order: [], // Disable initial ordering

    drawCallback: function () {
        var api = this.api();

        // Function to calculate total for all records (ignoring pagination)
        var sumAllRecords = function (index) {
            return api
                .column(index, { search: "applied" }) // Include all rows (filtered if applicable)
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b.replace(/[^0-9.-]+/g, "") || 0); // Clean and parse values
                }, 0);
        };

        // Update footer totals
        var totalCommissionAsPerFeeReported = sumAllRecords(17).toFixed(2);
        var totalCommissionAnticipated = sumAllRecords(18).toFixed(2);
        var totalCommissionPaidAsPerFeeReported = sumAllRecords(19).toFixed(2);
        var totalCommissionPending = sumAllRecords(20).toFixed(2);


        // Update totals above the table
        $("#total_commission_claimed").text(totalCommissionAsPerFeeReported);
        $("#total_commission_anticipated").text(totalCommissionAnticipated);
        $("#total_commission_paid").text(totalCommissionPaidAsPerFeeReported);
        $("#total_commission_pending").text(totalCommissionPending);

    }
});

// Add a custom dropdown filter above the table
var statusFilter = `
        <label>Filter by Status:
            <select id="statusFilter" class="form-control form-control-sm">
                <option value="">All</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
                <option value="Discontinued">Discontinued</option>
                <option value="Cancelled">Cancelled</option>
                <option value="Withdrawn">Withdrawn</option>
                <option value="Deferred">Deferred</option>
                <option value="Future">Future</option>
                <option value="VOE">VOE</option>
                <option value="Refund">Refund</option>
            </select>
        </label>`;

    // Append the filter to the DOM (e.g., above the table)
    $(".dataTables_filter").append(statusFilter);

     // Handle filter change
     $("#statusFilter").on("change", function () {
        var statusFilterval = $(this).val(); // Get the selected value
        //console.log("Filter Value:", statusFilterval); // Debugging
        //console.log("DataTable Column Instance:", table33.column(20));

        if (statusFilterval === "") {
            // Reset filter (show all)
            table33.column(21).search("").draw(); // Adjust index to match "Student Status" column
        } else {
            // Apply filter with regex for exact match
            table33.column(21).search("^" + statusFilterval + "$", true, false).draw();
        }
    });
  
  
$(document).on('change', '.note-field', function () {
    let textarea = $(this);
    var studentid = $(this).attr('data-studentid');
    let newValue = textarea.val();
    $.ajax({
        url: '/partners/save-student-note',
        method: 'POST',
        data: { rowId: studentid, note: newValue},
        success: function (response) {
            if (data.status) {
                const studentId = data.studentId;
                const studentNote = data.studentNote;
                //console.log('studentId='+studentId);
                //console.log('studentNote='+studentNote);
                // Locate the row in the DataTable
                //const table = $('.table-3').DataTable();
                const rowIndex = table33.rows().eq(0).filter((rowIdx) => {
                    //console.log(table33.cell(rowIdx, 22).data());
                    return table33.cell(rowIdx, 22).data() == studentId; // Match student ID column
                });

                // Update the cell value
                if (rowIndex.length > 0) {
                    table33.cell(rowIndex[0], 23).data(studentNote).draw(); // Update the note column
                }
                $('.custom-error-msg').html('<span class="alert alert-success">'+data.message+'</span>');
            } else {
                $('.custom-error-msg').html('<span class="alert alert-danger">'+data.message+'</span>');
            }
        },
        error: function (error) {
            console.error('Error saving note:', error);
        }
    });
});

//For student inactive list
var table331 = $(".table-31").dataTable({
    dom: '<"row"<"col-md-4 text-start"l><"col-md-4 text-center"B><"col-md-4 text-end"f>>rtip',
    buttons: [
        {
            extend: 'excelHtml5',
            text: '<i class="fas fa-file-excel"></i> Excel',
            className: 'btn btn-success btn-sm',
            exportOptions: {
                columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21], // Export all data columns, exclude Add Note (23) and Action (24)
                format: {
                    body: function (data, row, column, node) {
                        // Remove HTML tags and get clean text
                        if (typeof data === 'string') {
                            // Remove HTML tags
                            data = data.replace(/<[^>]*>/g, '');
                            // Decode HTML entities
                            var txt = document.createElement('textarea');
                            txt.innerHTML = data;
                            data = txt.value;
                        }
                        return data || '';
                    }
                }
            },
            filename: function() {
                var partnerName = '{{ $fetchedData->partner_name ?? "Partner" }}';
                return 'Partner_Student_Data_Inactive_' + partnerName.replace(/[^a-z0-9]/gi, '_') + '_' + new Date().toISOString().split('T')[0];
            },
            title: 'Partner Student Data (Inactive) - {{ $fetchedData->partner_name ?? "Partner" }}',
            messageTop: 'Partner: {{ $fetchedData->partner_name ?? "N/A" }}\nExport Date: ' + new Date().toLocaleString(),
            customize: function(xlsx) {
                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                // Auto-size columns
                $('row c', sheet).attr('s', '50');
            }
        },
        {
            extend: 'csvHtml5',
            text: '<i class="fas fa-file-csv"></i> CSV',
            className: 'btn btn-info btn-sm',
            exportOptions: {
                columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21], // Export all data columns, exclude Add Note (23) and Action (24)
                format: {
                    body: function (data, row, column, node) {
                        // Remove HTML tags and get clean text
                        if (typeof data === 'string') {
                            // Remove HTML tags
                            data = data.replace(/<[^>]*>/g, '');
                            // Decode HTML entities
                            var txt = document.createElement('textarea');
                            txt.innerHTML = data;
                            data = txt.value;
                        }
                        return data || '';
                    }
                }
            },
            filename: function() {
                var partnerName = '{{ $fetchedData->partner_name ?? "Partner" }}';
                return 'Partner_Student_Data_Inactive_' + partnerName.replace(/[^a-z0-9]/gi, '_') + '_' + new Date().toISOString().split('T')[0];
            }
        }
    ],
    "searching": true,
    "lengthChange": true, // Enable dropdown for page length
    "lengthMenu": [ [10, 20, 50,100,200,500,1000], [10, 20, 50,100,200,500,1000] ], // Dropdown options for pagination
    columnDefs: [
        {
            targets: 0, // Index of the "Sno" column
            orderable: false, // Prevent sorting on this column
            searchable: false, // Prevent searching this column
            render: function (data, type, row, meta) {
                return meta.row + 1; // Display row index starting from 1
            }
        }
    ],
    order: [], // Disable initial ordering

    drawCallback: function () {
        var api = this.api();

        // Function to calculate column total
        var sumColumn1 = function (index) {
            return api
                .column(index, { page: "current" }) // Only calculate for visible rows
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b.replace(/[^0-9.-]+/g, "") || 0); // Clean and parse values
                }, 0);
        };

        // Update footer totals
        var totalCommissionAsPerFeeReported1 = sumColumn1(17).toFixed(2);
        var totalCommissionAnticipated1 = sumColumn1(18).toFixed(2);
        var totalCommissionPaidAsPerFeeReported1 = sumColumn1(19).toFixed(2);
        var totalCommissionPending1 = sumColumn1(20).toFixed(2);

        $("#total_commission_as_per_fee_reported1").text(totalCommissionAsPerFeeReported1);
        $("#total_commission_anticipated1").text(totalCommissionAnticipated1);
        $("#total_commission_paid_as_per_fee_reported1").text(totalCommissionPaidAsPerFeeReported1);
        $("#total_commission_pending1").text(totalCommissionPending1);
    }
}); 
  
$(document).on('change', '.note-field1', function () {
    let textarea = $(this);
    var studentid = $(this).attr('data-studentid');
    let newValue = textarea.val();
    $.ajax({
        url: '/partners/save-student-note',
        method: 'POST',
        data: { rowId: studentid, note: newValue},
        success: function (response) {
            if (data.status) {
                const studentId = data.studentId;
                const studentNote = data.studentNote;
                //console.log('studentId='+studentId);
                //console.log('studentNote='+studentNote);
                // Locate the row in the DataTable
                const rowIndex = table331.rows().eq(0).filter((rowIdx) => {
                    //console.log(table331.cell(rowIdx, 22).data());
                    return table331.cell(rowIdx, 22).data() == studentId; // Match student ID column
                });

                // Update the cell value
                if (rowIndex.length > 0) {
                    table331.cell(rowIndex[0], 23).data(studentNote).draw(); // Update the note column
                }
                $('.custom-error-msg').html('<span class="alert alert-success">'+data.message+'</span>');
            } else {
                $('.custom-error-msg').html('<span class="alert alert-danger">'+data.message+'</span>');
            }
        },
        error: function (error) {
            console.error('Error saving note:', error);
        }
    });
});


  $(".invoicetable").dataTable({
	"searching": false,
	"lengthChange": false,
  "columnDefs": [
    { "sortable": false, "targets": [0, 2, 3] }
  ],
  order: [[1, "desc"]] //column indexes is zero based

});  

// Trigger file input when "Add Document" button is clicked in upload_document container
$(document).delegate('.upload_document .btn-primary', 'click', function(e) {
	e.preventDefault();
	$(this).closest('.upload_document').find('input[name=document_upload]').click();
});

$(document).delegate('input[name=document_upload]', 'click', function() {
		$(this).attr("value", "");
	}); 
	$(document).delegate('input[name=document_upload]', 'change', function() {
		$('.popuploader').show();	
var formData = new FormData($('#upload_form')[0]);		
		$.ajax({
			url: site_url+'/upload-partner-document-upload',
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
$(document).delegate('#intrested_workflow', 'change', function(){
	
				var v = $('#intrested_workflow option:selected').val();
				
				if(v != ''){
						$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/getpartner')}}',
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
			url: '{{URL::to('/getproduct')}}',
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
			url: '{{URL::to('/getbranch')}}',
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
	
	
	$(document).delegate('.converttoapplication','click', function(){
		var v = $(this).attr('data-id');
		if(v != ''){
			$('.popuploader').show();
			$.ajax({
				url: '{{URL::to('/convertapplication')}}',
				type:'GET',
				data:{cat_id:v,clientid:'{{$fetchedData->id}}'},
				success:function(response){
					var res = typeof response === 'string' ? JSON.parse(response) : response;
					if(!res || res.status !== true){
						$('.popuploader').hide();
						alert((res && res.message) ? res.message : 'Failed to create application. Please try again.');
						return;
					}
					$.ajax({
						url: site_url+'/get-services',
						type:'GET',
						data:{clientid:'{{$fetchedData->id}}'},
						success: function(responses){
							$('.interest_serv_list').html(responses);
							$('.popuploader').hide();
							getallactivities();
						},
						error: function(){
							$('.popuploader').hide();
							alert('Application created, but failed to refresh services. Please refresh the page.');
							getallactivities();
						}
					});
				},
				error: function(){
					$('.popuploader').hide();
					alert('Failed to create application. Please try again.');
				}
			});
		}
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
			   url: '{{URL::to('/renamedoc')}}',
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
		


	 var eduid = '';	
	$(document).delegate('.interest_service_view', 'click', function(){
		var v = $(this).attr('data-id');
		$('.popuploader').show();
		$('#interest_service_view').modal('show');
		$.ajax({
			url: '{{URL::to('/getintrestedservice')}}',
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
			url: '{{URL::to('/getintrestedserviceedit')}}',
			type:'GET',
			data:{id:v},
		success:function(response){
			$('.popuploader').hide();
			$('.showinterestedserviceedit').html(response);
			
			if (typeof flatpickr !== 'undefined') {
				flatpickr(".datepicker", {
					dateFormat: "Y-m-d",
					allowInput: true
				});
			}
		}
	});
});
	
	$(document).delegate('.opencommissioninvoice', 'click', function(){
		$('#opencommissionmodal').modal('show');
	});
	
	$(document).delegate('.opengeneralinvoice', 'click', function(){
		$('#opengeneralinvoice').modal('show');
	});
	
	$(document).delegate('.add_promotion', 'click', function(){
		$('#create_promotion').modal('show');
	
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
	
	
$(document).delegate('.openapplicationdetail', 'click', function(){
		var appliid = $(this).attr('data-id');
		$('.if_applicationdetail').hide();
		$('.ifapplicationdetailnot').show();
		$.ajax({
			url: '{{URL::to('/getapplicationdetail')}}',
			type:'GET',
			data:{id:appliid},
		success:function(response){
			$('.popuploader').hide();
			$('.ifapplicationdetailnot').html(response);
			
			if (typeof flatpickr !== 'undefined') {
				flatpickr('.datepicker', {
					dateFormat: "Y-m-d",
					allowInput: true,
					onChange: function(selectedDates, dateStr, instance) {
						if (selectedDates.length > 0) {
							$.ajax({
								url:"{{URL::to('/application/updateintake')}}",
								method: "GET",
								dataType: "json",
								data: {from: dateStr, appid: appliid},
								success:function(result) {
									console.log("sent back -> do whatever you want now");
								}
							});
						}
					}
				});
			}
			

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

$(document).delegate('.due_date_sec a.due_date_btn', 'click', function(){
	$('.due_date_sec .due_date_col').show();
	$(this).hide();
});
$(document).delegate('.remove_col a.remove_btn', 'click', function(){
	$('.due_date_sec .due_date_col').hide();
	$('.due_date_sec a.due_date_btn').show();  
});
	
$(document).delegate('.nextstage', 'click', function(){
	var appliid = $(this).attr('data-id');
	var stage = $(this).attr('data-stage');
	$('.popuploader').show();
	$.ajax({
		url: '{{URL::to('/updatestage')}}',
		type:'GET',
		datatype:'json',
		data:{id:appliid, client_id:'{{$fetchedData->id}}'},
		success:function(response){
			$('.popuploader').hide();
			var obj = $.parseJSON(response);
			if(obj.status){
				$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
				$('.curerentstage').text(obj.stage);
				$.ajax({
					url: site_url+'/get-applications-logs',
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
	$('.popuploader').show();
	$.ajax({
		url: '{{URL::to('/updatebackstage')}}',
		type:'GET',
		datatype:'json',
		data:{id:appliid, client_id:'{{$fetchedData->id}}'},
		success:function(response){
			var obj = $.parseJSON(response);
			$('.popuploader').hide();
			if(obj.status){
				$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
				$('.curerentstage').text(obj.stage);
				$.ajax({
					url: site_url+'/get-applications-logs',
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


$(document).delegate('#notes-tab', 'click', function(){
		var appliid = $(this).attr('data-id');
		$('.if_applicationdetail').hide();
		$('.ifapplicationdetailnot').show();
		$.ajax({
			url: '{{URL::to('/getapplicationnotes')}}',
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
  
  $(document).delegate('.changepromotonstatus', 'change', function(){
	  $('.popuploader').show();
	  var appliid = $(this).attr('data-id');
	  var dstatus = $(this).attr('data-status');
	  $.ajax({
			url: '{{URL::to('/change-promotion-status')}}',
			type:'GET',
			data:{id:appliid, status:dstatus},
			success:function(response){
				$('.popuploader').hide();
				var obj = $.parseJSON(response);
				if(obj.status){
					$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
					if(dstatus == 1){
							var updated_status = 0;
						} else {
							var updated_status = 1;
						}
					$(".changepromotonstatus[data-id="+appliid+"]").attr('data-status', updated_status);
					$.ajax({
						url: site_url+'/get-promotions',
						type:'GET',
						data:{clientid:'{{$fetchedData->id}}'},
						success: function(responses){
							 
							$('.promotionlists').html(responses);
						}
					});
				}else{
					$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
					if(current_status == 1){
							$(".changepromotonstatus[data-id="+appliid+"]").prop('checked', true);
						} else {
							$(".changepromotonstatus[data-id="+appliid+"]").prop('checked', false);
						}
				}
			}
		});
  });
  $(document).delegate('.changetaskstatus', 'click', function(){
	  var statusname = $(this).attr('data-statusname');
	  var did = $(this).attr('data-id');
	  var dstatus = $(this).attr('data-status');
	  $('.taskstatus').html(statusname+' <i class="fa fa-angle-down"></i>');
	  $('.popuploader').show();
	  $.ajax({
			url: '{{URL::to('/change-task-status')}}',
			type:'GET',
			data:{id:did, status:dstatus},
			success:function(response){
				$('.popuploader').hide();
				var obj = $.parseJSON(response);
				if(obj.status){
					$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
					$('.tasklogs').html(obj.data);
					// Task system removed - December 2025
					// $.ajax({
					// 		url: site_url+'/partner/get-tasks',
					// 		type:'GET',
					// 		data:{clientid:'{{$fetchedData->id}}'},
					// 		success: function(responses){
					// 			 $('#my-datatable').DataTable().destroy();
					// 			$('.taskdata').html(responses);
					// 			$('#my-datatable').DataTable({
					// 				"searching": false,
					// 				"lengthChange": false,
					// 			  "columnDefs": [
					// 				{ "sortable": false, "targets": [0, 2, 3] }
					// 			  ],
					// 			  order: [[1, "desc"]] //column indexes is zero based
					// 			}).draw();
					// 		}
					// 	});
				}else{
					$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
					
				}
			}
		});
  });
  
  $(document).delegate('.changeprioritystatus', 'click', function(){
	  var statusname = $(this).attr('data-statusname');
	  var did = $(this).attr('data-id');
	  var dstatus = $(this).attr('data-status');
	  $('.prioritystatus').html(statusname+' <i class="fa fa-angle-down"></i>');
	  $('.popuploader').show();
	  $.ajax({
			url: '{{URL::to('/change-task-priority')}}',
			type:'GET',
			data:{id:did, status:statusname},
			success:function(response){
				$('.popuploader').hide();
				var obj = $.parseJSON(response);
				if(obj.status){
					$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
					$('.tasklogs').html(obj.data);
					// Task system removed - December 2025
					// $.ajax({
					// 		url: site_url+'/partner/get-tasks',
					// 		type:'GET',
					// 		data:{clientid:'{{$fetchedData->id}}'},
					// 		success: function(responses){
					// 			 $('#my-datatable').DataTable().destroy();
					// 			$('.taskdata').html(responses);
					// 			$('#my-datatable').DataTable({
					// 				"searching": false,
					// 				"lengthChange": false,
					// 			  "columnDefs": [
					// 				{ "sortable": false, "targets": [0, 2, 3] }
					// 			  ],
					// 			  order: [[1, "desc"]] //column indexes is zero based
					// 			}).draw();
					// 		}
					// 	});
				}else{
					$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
					
				}
			}
		});
  });
  $(document).delegate('.savecomment', 'click', function(){
	  var flag = false;
	  if($('#comment').val() == ''){
		  $('.comment-error').html('The Comment field is required.');
		  flag = true;
	  }
	  
	  if(!flag){
		  $('.popuploader').show();
		  $.ajax({
			url: '{{URL::to('/partner/savecomment')}}',
			type:'POST',
			data:{comment:$('#comment').val(), taskid:$('#taskid').val()},
			 headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
			success:function(response){
				$('.popuploader').hide();
				var obj = $.parseJSON(response);
				if(obj.status){
					$('.tasklogs').html(obj.data);
				}
			}
		});
	  }
  });
  $(document).delegate('.openpromotonform', 'click', function(){
		var appliid = $(this).attr('data-id');
		$('#edit_promotion').modal('show');
		$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/getpromotioneditform')}}',
			type:'GET',
			data:{id:appliid},
			success:function(response){
				$('.popuploader').hide();
				$('.showpromotionedit').html(response);
					$('.productselect2').select2({
				  placeholder: "Select Product",
				  multiple: true,
					  width: "100%"
				});
			}
		});
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
function arcivedAction( id, table ) {
		var conf = confirm('Are you sure, you would like to delete this record. Remember all Related data would be deleted.');
		if(conf){	 
			if(id == '') {
				alert('Please select ID to delete the record.');
				return false;	
			} else {
				$('#popuploader').show();
				$(".server-error").html(''); //remove server error.
				$(".custom-error-msg").html(''); //remove custom error.
				$.ajax({
					type:'post',
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					url:'{{URL::to('/')}}/delete_action',
					data:{'id': id, 'table' : table},
					success:function(resp) {
						$('#popuploader').hide();
						var obj = $.parseJSON(resp);
						if(obj.status == 1) {
							window.location.href= '{{URL::to('partners')}}';
							
						} else{
							var html = errorMessage(obj.message);
							$(".custom-error-msg").html(html);
						}
						$("#popuploader").hide();
					},
					beforeSend: function() {
						$("#popuploader").show();
					}
				});
				$('html, body').animate({scrollTop:0}, 'slow');
			}
		} else{
			$("#loader").hide();
		}
	}
</script>

<script>
    // ============================================================================
    // BULK UPLOAD AND CHECKLIST FUNCTIONALITY FOR PARTNERS DOCUMENTS TAB
    // ============================================================================
    
    let bulkUploadFilesPartner = [];
    let currentPartnerId = {{$fetchedData->id}};
    
    // Add Checklist handler
    $(document).on('click', '.add_alldocument_doc', function() {
        var checklistName = prompt('Enter checklist name:');
        if (checklistName && checklistName.trim() !== '') {
            $.ajax({
                url: '{{URL::to('/partners/add-alldocchecklist')}}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    clientid: currentPartnerId,
                    checklist: checklistName.trim(),
                    type: 'partner',
                    doctype: 'documents'
                },
                success: function(response) {
                    var obj = JSON.parse(response);
                    if (obj.status) {
                        location.reload();
                    } else {
                        alert(obj.message || 'Error adding checklist');
                    }
                },
                error: function() {
                    alert('Error adding checklist. Please try again.');
                }
            });
        }
    });
    
    // Toggle bulk upload dropzone
    $(document).on('click', '.bulk-upload-toggle-btn', function() {
        const dropzoneContainer = $(this).closest('.card-header-action').next('.bulk-upload-dropzone-container');
        
        if (dropzoneContainer.length && dropzoneContainer.is(':visible')) {
            dropzoneContainer.slideUp();
            $(this).html('<i class="fas fa-upload"></i> Bulk Upload');
            bulkUploadFilesPartner = [];
            dropzoneContainer.find('.bulk-upload-file-list').hide();
            dropzoneContainer.find('.file-count').text('0');
        } else {
            dropzoneContainer.slideDown();
            $(this).html('<i class="fas fa-times"></i> Close');
        }
    });
    
    // Click to browse files
    $(document).on('click', '.bulk-upload-dropzone', function(e) {
        if (!$(e.target).is('input')) {
            $(this).find('.bulk-upload-file-input').click();
        }
    });
    
    // File input change
    $(document).on('change', '.bulk-upload-file-input', function() {
        const files = this.files;
        if (files.length > 0) {
            handleBulkFilesSelectedPartner(files);
        }
    });
    
    // Drag and drop handlers
    $(document).on('dragover', '.bulk-upload-dropzone', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('drag_over');
    });
    
    $(document).on('dragleave', '.bulk-upload-dropzone', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('drag_over');
    });
    
    $(document).on('drop', '.bulk-upload-dropzone', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('drag_over');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files && files.length > 0) {
            handleBulkFilesSelectedPartner(files);
        }
    });
    
    // Handle files selected
    function handleBulkFilesSelectedPartner(files) {
        bulkUploadFilesPartner = [];
        
        const invalidFiles = [];
        const maxSize = 50 * 1024 * 1024; // 50MB
        const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        
        Array.from(files).forEach(file => {
            if (file.size > maxSize) {
                invalidFiles.push(file.name + ' (exceeds 50MB)');
                return;
            }
            
            const ext = file.name.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(ext)) {
                invalidFiles.push(file.name + ' (invalid file type)');
                return;
            }
            
            bulkUploadFilesPartner.push(file);
        });
        
        if (invalidFiles.length > 0) {
            alert('The following files were skipped:\n' + invalidFiles.join('\n'));
        }
        
        if (bulkUploadFilesPartner.length === 0) {
            alert('No valid files selected. Please select PDF, JPG, PNG, DOC, or DOCX files under 50MB.');
            return;
        }
        
        // Show file list
        $('.bulk-upload-file-list').show();
        $('.file-count').text(bulkUploadFilesPartner.length);
        
        // Show mapping interface
        showBulkUploadMappingPartner();
    }
    
    // Show mapping interface
    function showBulkUploadMappingPartner() {
        if (bulkUploadFilesPartner.length === 0) return;
        
        // Get existing checklists
        getExistingChecklistsPartner(function(checklists) {
            displayMappingInterfacePartner(bulkUploadFilesPartner, checklists);
        });
    }
    
    // Get existing checklists from table
    function getExistingChecklistsPartner(callback) {
        const checklists = [];
        const checklistNames = new Set();
        
        $('.alldocumnetlist tr').each(function() {
            const checklistName = $(this).data('checklist-name');
            if (checklistName && !checklistNames.has(checklistName)) {
                checklistNames.add(checklistName);
                checklists.push({ name: checklistName });
            }
        });
        
        callback(checklists);
    }
    
    // Display mapping interface
    function displayMappingInterfacePartner(files, checklists) {
        const modal = $('#bulk-upload-mapping-modal-partner');
        const tableContainer = $('#bulk-upload-mapping-table-partner');
        
        let html = '<div class="table-responsive" style="overflow-x: auto;">';
        html += '<table class="table table-bordered" style="width: 100%; min-width: 600px; margin-bottom: 0;">';
        html += '<thead><tr><th style="min-width: 150px;">File Name</th><th style="min-width: 200px;">Checklist Assignment</th><th style="min-width: 100px;">Status</th><th style="min-width: 80px;">Action</th></tr></thead>';
        html += '<tbody>';
        
        Array.from(files).forEach((file, index) => {
            const fileName = file.name;
            const fileSize = formatFileSizePartner(file.size);
            
            html += '<tr class="bulk-upload-file-item">';
            html += '<td style="word-break: break-word;"><div class="file-info" style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-file" style="color: #4a90e2;"></i><div><div class="file-name">' + escapeHtmlPartner(fileName) + '</div><div class="file-size" style="font-size: 12px; color: #666;">' + fileSize + '</div></div></div></td>';
            html += '<td style="min-width: 200px;">';
            html += '<select class="form-control checklist-select" data-file-index="' + index + '" style="width: 100%;">';
            html += '<option value="">-- Select Checklist --</option>';
            html += '<option value="__NEW__">+ Create New Checklist</option>';
            checklists.forEach(checklist => {
                html += '<option value="' + escapeHtmlPartner(checklist.name) + '">' + escapeHtmlPartner(checklist.name) + '</option>';
            });
            html += '</select>';
            html += '<input type="text" class="form-control mt-2 new-checklist-input" data-file-index="' + index + '" placeholder="Enter new checklist name" style="display: none; width: 100%;">';
            html += '</td>';
            html += '<td style="white-space: nowrap;"><span class="match-status manual">Manual selection</span></td>';
            html += '<td style="white-space: nowrap;"><button type="button" class="btn btn-sm btn-outline-danger bulk-upload-remove-file" data-file-index="' + index + '">Remove</button></td>';
            html += '</tr>';
        });
        
        html += '</tbody></table>';
        html += '</div>';
        tableContainer.html(html);
        modal.show();
    }
    
    // Handle new checklist option
    $(document).on('change', '#bulk-upload-mapping-modal-partner .checklist-select', function() {
        const fileIndex = $(this).data('file-index');
        const value = $(this).val();
        const newInput = $('#bulk-upload-mapping-modal-partner .new-checklist-input[data-file-index="' + fileIndex + '"]');
        
        if (value === '__NEW__') {
            newInput.show();
            $(this).closest('tr').find('.match-status').removeClass('auto-matched manual').addClass('new-checklist').text('New checklist');
        } else {
            newInput.hide();
            if (value) {
                $(this).closest('tr').find('.match-status').removeClass('new-checklist').addClass('manual').text('Manual selection');
            }
        }
    });
    
    // Close modal
    $(document).on('click', '#bulk-upload-mapping-modal-partner .close-mapping-modal, #cancel-bulk-upload-partner', function() {
        $('#bulk-upload-mapping-modal-partner').hide();
        $('#bulk-upload-progress-partner').hide();
        $('#confirm-bulk-upload-partner').prop('disabled', false);
    });

    // Remove a file from bulk upload
    $(document).on('click', '#bulk-upload-mapping-modal-partner .bulk-upload-remove-file', function() {
        const index = parseInt($(this).data('file-index'), 10);
        if (Number.isNaN(index)) {
            return;
        }
        bulkUploadFilesPartner.splice(index, 1);
        $('.file-count').text(bulkUploadFilesPartner.length);
        if (bulkUploadFilesPartner.length === 0) {
            $('#bulk-upload-mapping-modal-partner').hide();
            $('.bulk-upload-file-list').hide();
            return;
        }
        showBulkUploadMappingPartner();
    });
    
    // Confirm bulk upload
    $(document).on('click', '#confirm-bulk-upload-partner', function() {
        const mappings = [];
        
        // Collect mappings
        bulkUploadFilesPartner.forEach((file, index) => {
            const selectElement = $('#bulk-upload-mapping-modal-partner .checklist-select[data-file-index="' + index + '"]');
            const checklist = selectElement.val();
            
            let mapping = null;
            
            if (checklist === '__NEW__') {
                const newChecklistName = $('#bulk-upload-mapping-modal-partner .new-checklist-input[data-file-index="' + index + '"]').val();
                if (newChecklistName) {
                    mapping = { type: 'new', name: newChecklistName.trim() };
                }
            } else if (checklist) {
                mapping = { type: 'existing', name: checklist };
            }
            
            mappings.push(mapping);
        });
        
        // Validate all files have mappings
        const unmappedFiles = [];
        mappings.forEach((mapping, index) => {
            if (!mapping || !mapping.name) {
                unmappedFiles.push(bulkUploadFilesPartner[index].name);
            }
        });
        
        if (unmappedFiles.length > 0) {
            alert('Please map all files to checklists:\n' + unmappedFiles.join('\n'));
            return;
        }
        
        // Upload files one by one
        uploadBulkFilesPartner(bulkUploadFilesPartner, mappings);
    });
    
    // Upload bulk files
    function uploadBulkFilesPartner(files, mappings) {
        $('#bulk-upload-progress-partner').show();
        $('#bulk-upload-progress-bar-partner').css('width', '0%').text('0%');
        $('#confirm-bulk-upload-partner').prop('disabled', true);
        
        let uploadedCount = 0;
        let failedFiles = [];
        
        // Upload files sequentially
        function uploadNext(index) {
            if (index >= files.length) {
                // All uploads complete
                $('#bulk-upload-progress-partner').hide();
                $('#confirm-bulk-upload-partner').prop('disabled', false);
                
                let message = 'Upload completed: ' + uploadedCount + ' file(s) uploaded.';
                if (failedFiles.length > 0) {
                    message += '\n\nFailed files:\n' + failedFiles.join('\n');
                }
                alert(message);
                $('#bulk-upload-mapping-modal-partner').hide();
                $('.bulk-upload-dropzone-container').hide();
                $('.bulk-upload-toggle-btn').html('<i class="fas fa-upload"></i> Bulk Upload');
                bulkUploadFilesPartner = [];
                
                location.reload();
                return;
            }
            
            const file = files[index];
            const mapping = mappings[index];
            const formData = new FormData();
            
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('clientid', currentPartnerId);
            formData.append('type', 'partner');
            formData.append('doctype', 'documents');
            formData.append('document_upload', file);
            formData.append('checklist', mapping.name);
            formData.append('checklist_type', mapping.type);
            
            $.ajax({
                url: '{{URL::to('/partners/upload-alldocument')}}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    uploadedCount++;
                    const percentComplete = ((uploadedCount / files.length) * 100);
                    $('#bulk-upload-progress-bar-partner').css('width', percentComplete + '%').text(Math.round(percentComplete) + '%');
                    uploadNext(index + 1);
                },
                error: function() {
                    failedFiles.push(file.name);
                    const percentComplete = ((uploadedCount / files.length) * 100);
                    $('#bulk-upload-progress-bar-partner').css('width', percentComplete + '%').text(Math.round(percentComplete) + '%');
                    uploadNext(index + 1);
                }
            });
        }
        
        uploadNext(0);
    }
    
    // Helper functions
    function formatFileSizePartner(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }
    
    function escapeHtmlPartner(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>

@push('tinymce-scripts')
@include('partials.tinymce')
@endpush

<!-- DataTables Buttons Extension for Export Functionality -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

@endsection