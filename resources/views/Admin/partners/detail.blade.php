@extends('layouts.admin')
@section('title', 'Partner Detail')

@section('content')
<link rel="stylesheet" href="{{asset('css/client-detail.css')}}">
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
					<div class="card author-box left_section_upper">
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
									'notuseddocuments',
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
									<a class="nav-link {{ $activeTab === 'notuseddocuments' ? 'active' : '' }}" href="{{route('partners.detail', ['id' => $partnerId, 'tab' => 'notuseddocuments'])}}" id="notuseddocuments-tab" role="tab" aria-controls="notuseddocuments" aria-selected="{{ $activeTab === 'notuseddocuments' ? 'true' : 'false' }}">Not Used Documents</a>
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
														{!! Form::text('contract_start', @$fetchedData->contract_start, array('id' => 'contract_start','class' => 'form-control contract_expiry', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' ))  !!}
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
																			<input type="hidden" name="checklist" value="<?php echo htmlspecialchars($fetch->checklist ?? '', ENT_QUOTES, 'UTF-8'); ?>">
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
								<div class="tab-pane fade <?php echo ($activeTab === 'notuseddocuments') ? 'show active' : ''; ?>" id="notuseddocuments" role="tabpanel" aria-labelledby="notuseddocuments-tab">
									<div class="list_data col-6 col-md-6 col-lg-6" style="display:inline-block;vertical-align: top;">
										<div class="">
											<table class="table text_wrap">
												<thead>
													<tr>
														<th>Checklist</th>
														<th>Added By</th>
														<th>File Name</th>
														<th></th>
													</tr>
												</thead>
												<tbody class="tdata notuseddocumnetlist">
													<?php
													$fetchd = \App\Models\Document::where('client_id', $fetchedData->id)
														->where('not_used_doc', 1)
														->where('type', 'partner')
														->where('doc_type', 'documents')
														->orderBy('updated_at', 'DESC')
														->get();
													foreach($fetchd as $notuseKey=>$fetch)
													{
														$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
														?>
														<tr class="drow" id="id_{{$fetch->id}}">
															<td style="white-space: initial;"><?php echo $fetch->checklist; ?></td>
															<td style="white-space: initial;">
																<?php
																	echo $admin->first_name. "<br>";
																	echo date('d/m/Y', strtotime($fetch->created_at));
																?>
															</td>
															<td style="white-space: initial;">
																<?php if( isset($fetch->file_name) && $fetch->file_name !=""){ ?>
																	<div data-id="{{$fetch->id}}" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
																		<?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
																			<a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-notuseddocumentlist-partner')">
																				<i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
																			</a>
																		<?php } else {  //For old file upload
																			$url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
																			$myawsfile = $url.$fetchedData->id.'/'.$fetch->doc_type.'/'.$fetch->myfile;
																			?>
																			<a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($myawsfile); ?>','preview-container-notuseddocumentlist-partner')">
																				<i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
																			</a>
																		<?php } ?>
																	</div>
																<?php
																}
																else
																{
																	echo "N/A";
																}?>
															</td>
															<td>
																<div class="dropdown d-inline">
																	<button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
																	<div class="dropdown-menu">
																		<?php
																		$url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
																		?>
																		<?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
																			<a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
																		<?php } else {  //For old file upload ?>
																			<a target="_blank" class="dropdown-item" href="<?php echo $url.$fetchedData->id.'/'.$fetch->doc_type.'/'.$fetch->myfile; ?>">Preview</a>
																		<?php } ?>

																		<a data-id="{{$fetch->id}}" class="dropdown-item backtodoc" data-doctype="documents" data-href="backtodoc" href="javascript:;">Back To Document</a>
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

									<!-- Container for File Preview -->
									<div class="col-5 col-md-5 col-lg-5 file-preview-container preview-container-notuseddocumentlist-partner">
										<p style="color:#000;">Click on a file to preview it here.</p>
									</div>
								</div>
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
                                                                //->where('applications.overall_status', 0) //overall status = Active
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
                                                                                $client_encoded_id_course = base64_encode(convert_uuencode(@$data->client_id));
                                                                                echo '<a href="'.url('/clients/detail/'.$client_encoded_id_course.'/application/'.$data->id).'" target="_blank">'.$data->coursename.'</a>';
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
                                                                            $client_encoded_id_course1 = base64_encode(convert_uuencode(@$data1->client_id));
                                                                            echo '<a href="'.url('/clients/detail/'.$client_encoded_id_course1.'/application/'.$data1->id).'" target="_blank">'.$data1->coursename.'</a>';
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

<div id="confirmNotUseDocModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn-close"></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to send this document in Not Use Tab?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Send</button>
				<button type="button" style="margin-top: 40px;" data-bs-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmBackToDocModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn-close"></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to send this in document Tab again?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Send</button>
				<button type="button" style="margin-top: 40px;" data-bs-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmDocModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn-close"></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to verify this doc?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Verify</button>
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
    // ============================================================================
    // GLOBAL CONFIGURATION
    // ============================================================================
    
    // Ensure global config objects exist before setting properties
    window.AppConfig = window.AppConfig || {};
    window.PageConfig = window.PageConfig || {};
    
    // Application Configuration
    AppConfig.csrf = '{{ csrf_token() }}';
    AppConfig.siteUrl = '{{ url("/") }}';
    AppConfig.urls = {
        siteUrl: '{{ url("/") }}',
        partnersUpdateStudentStatus: '{{ url("/partners/update-student-status") }}',
        partnersUpdateStudentApplicationStatus: '{{ url("/partners/update-student-application-overall-status") }}',
        partnersGetEnrolledStudentList: '{{ URL::to("/partners/getEnrolledStudentList") }}',
        partnersGetTopReceiptValInDB: '{{ URL::to("/partners/getTopReceiptValInDB") }}',
        partnersGetTopInvoiceValInDB: '{{ URL::to("/partners/getTopInvoiceValInDB") }}',
        partnersGetStudentInfo: '{{ URL::to("/partners/getStudentInfo") }}',
        partnersGetStudentCourseInfo: '{{ URL::to("/partners/getStudentCourseInfo") }}',
        partnersUpdateInvoiceSentOptionToYes: '{{ URL::to("/partners/updateInvoiceSentOptionToYes") }}',
        partnersGetInfoByInvoiceId: '{{ URL::to("/partners/getInfoByInvoiceId") }}',
        partnersDeleteStudentRecordByInvoiceId: '{{ URL::to("/partners/deleteStudentRecordByInvoiceId") }}',
        partnersGetEnrolledStudentListInEditMode: '{{ URL::to("/partners/getEnrolledStudentListInEditMode") }}',
        partnersDeleteStudentRecordInvoiceByInvoiceId: '{{ URL::to("/partners/deleteStudentRecordInvoiceByInvoiceId") }}',
        partnersGetRecordedInvoiceList: '{{ URL::to("/partners/getRecordedInvoiceList") }}',
        partnersDeleteStudentPaymentInvoiceByInvoiceId: '{{ URL::to("/partners/deleteStudentPaymentInvoiceByInvoiceId") }}',
        partnersAddAllDocChecklist: '{{ URL::to("/partners/add-alldocchecklist") }}',
        partnersUploadAllDocument: '{{ URL::to("/partners/upload-alldocument") }}',
        partnersUploadPartnerDocument: '{{ url("/upload-partner-document-upload") }}',
        partnersSaveStudentNote: '{{ url("/partners/save-student-note") }}',
        getPartner: '{{ url("/getpartner") }}',
        getProduct: '{{ url("/getproduct") }}',
        getBranch: '{{ url("/getbranch") }}',
        changePromotionStatus: '{{ url("/change-promotion-status") }}',
        getPromotionEditForm: '{{ url("/getpromotioneditform") }}',
        getPromotions: '{{ url("/get-promotions") }}',
        changeClientStatus: '{{ url("/change-client-status") }}',
        getApplicationsLogs: '{{ url("/get-applications-logs") }}',
        getApplicationDetail: '{{ url("/getapplicationdetail") }}',
        updateApplicationIntake: '{{ url("/application/updateintake") }}',
        updateStage: '{{ url("/updatestage") }}',
        updateBackStage: '{{ url("/updatebackstage") }}',
        getApplicationNotes: '{{ url("/getapplicationnotes") }}',
        partnersFetchPartnerContactNo: '{{ URL::to("/partners/fetchPartnerContactNo") }}',
        clientsFetchClientContactNo: '{{ URL::to("/clients/fetchClientContactNo") }}',
        clientsGetRecipients: '{{ URL::to("/clients/get-recipients") }}',
        getNotes: '{{ url("/get-notes") }}',
        getActivities: '{{ url("/get-activities") }}',
        deleteAction: '{{ URL::to("/") }}',
        pinnote: '{{ URL::to("/pinnote") }}',
        viewnotedetail: '{{ URL::to("/viewnotedetail") }}',
        getnotedetail: '{{ URL::to("/getnotedetail") }}',
        getcontactdetail: '{{ URL::to("/getcontactdetail") }}',
        getbranchdetail: '{{ URL::to("/getbranchdetail") }}',
        getpartnerbranch: '{{ URL::to("/getpartnerbranch") }}',
        getbranchproduct: '{{ URL::to("/getbranchproduct") }}',
        getTemplates: '{{ URL::to("/get-templates") }}',
        getContacts: '{{ url("/get-contacts") }}',
        getBranches: '{{ url("/get-branches") }}',
        deletedocs: '{{ url("/deletedocs") }}',
        deletecontact: '{{ url("/deletecontact") }}',
        deletebranch: '{{ url("/deletebranch") }}'
    };
    
    // Page-Specific Configuration
    PageConfig.partnerId = {{ $fetchedData->id ?? 'null' }};
    PageConfig.partnerName = '{{ $fetchedData->partner_name ?? "" }}';
    PageConfig.partnerType = 'partner';
    PageConfig.defaultCountryCode = '{{ \App\Helpers\PhoneHelper::getDefaultCountryCode() }}';
    PageConfig.contractStart = '{{ @$fetchedData->contract_start ?? "" }}';
    PageConfig.contractExpiry = '{{ @$fetchedData->contract_expiry ?? "" }}';
</script>

{{-- Common JavaScript Files (load first) --}}
<script src="{{ asset('js/common/config.js') }}"></script>
<script src="{{ asset('js/common/document-handlers.js') }}"></script>

{{-- Page-Specific JavaScript Modules --}}
<script src="{{ asset('js/pages/admin/partner-detail/status-handlers.js') }}"></script>
<script src="{{ asset('js/pages/admin/partner-detail/notes-handlers.js') }}"></script>
<script src="{{ asset('js/pages/admin/partner-detail/mail-upload.js') }}"></script>
<script src="{{ asset('js/pages/admin/partner-detail/application-tab.js') }}"></script>
<script src="{{ asset('js/pages/admin/partner-detail/invoice-handlers.js') }}"></script>
<script src="{{ asset('js/pages/admin/partner-detail/bulk-upload.js') }}"></script>
<script src="{{ asset('js/pages/admin/partner-detail/datatable-handlers.js') }}"></script>
<script src="{{ asset('js/pages/admin/partner-detail/payment-field-handlers.js') }}"></script>
<script src="{{ asset('js/pages/admin/partner-detail/application-handlers.js') }}"></script>
<script src="{{ asset('js/pages/admin/partner-detail/promotion-handlers.js') }}"></script>
<script src="{{ asset('js/pages/admin/partner-detail/notes-contact-handlers.js') }}"></script>
<script src="{{ asset('js/pages/admin/partner-detail/service-handlers.js') }}"></script>
<script src="{{ asset('js/pages/admin/partner-detail/archive-handlers.js') }}"></script>
<script src="{{ asset('js/pages/admin/client-detail/document-context-menu.js') }}"></script>
<script src="{{ asset('js/pages/admin/client-detail/document-rename.js') }}"></script>
<script src="{{ asset('js/pages/admin/client-detail/document-actions.js') }}"></script>

{{-- Main partner-detail file (cleaned up, orchestrates modules) --}}
<script src="{{ asset('js/pages/admin/partner-detail.js') }}"></script>

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