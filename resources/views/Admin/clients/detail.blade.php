@extends('layouts.admin')
@section('title', 'Client Detail')

@section('content')
<link rel="stylesheet" href="{{URL::asset('public/css/bootstrap-datepicker.min.css')}}">
<style>
.popover {max-width:700px;}
.ag-space-between {justify-content: space-between;}
.ag-align-center {align-items: center;}
.ag-flex {display: flex;}
.ag-align-start {align-items: flex-start;}
.ag-flex-column {flex-direction: column;}
.col-hr-1 {margin-right: 5px!important;}
.text-semi-bold {font-weight: 600!important;}
.small, small {font-size: 85%;}
.ag-align-end { align-items: flex-end;}


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
.badge-outline {
    display: inline-block;
    padding: 5px 8px;
    line-height: 12px;
    border: 1px solid;
    border-radius: 0.25rem;
    font-weight: 400;
    font-size: 13px;
}
.col-greenf{color: #9b9f9b !important;}
.badge-outline.col-greenf.active{background: #4caf50 !important;color:#fff!important;}
.badge-outline.col-redf.active{background: #4caf50 !important;color:#fff!important;}
.uploadchecklists .table thead th {
    border-bottom: none;
    background-color: rgba(0,0,0,0.04);
    color: #666;
    padding-top: 15px;
    padding-bottom: 15px;
}
.card .card-body ul.nav-pills li.nav-item {margin: 0px 0px 0px 0px;}

  

 .file-preview-container {
    border: 1px solid #ddd;
    padding: 10px;
    min-height: 300px;
    text-align: center;
    display: inline-block;
}

.preview-image {
    max-width: 100%;
    height: auto;
    display: block;
    margin: auto;
}

.pdf-viewer, .doc-viewer {
    width: 100%;
    height: 400px;
    border: none;
}

/*////////////////////////////////////////////
    ////// appointment popup css chnages start /////////
    //////////////////////////////////////////// */


.timeslots .timeslot_col.active{/*border:1px solid #0062cc;background-color:#fff;*/background-color: #007bff;color: #FFFFFF;margin: 0px 10px 8px 0px;}
#preloaderbook {
	display:none;
    background: #0d104d;
    background: -webkit-linear-gradient(to right, #0d104d, #28408b);
    background: linear-gradient(to right, #0d104d, #28408b);
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    z-index: 5000;
}
#preloaderbook .circle-preloader {
    display: block;
    width: 60px;
    height: 60px;
    border: 2px solid rgba(255, 255, 255, 0.5);
    border-bottom-color: #ffffff;
    border-radius: 50%;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    margin: auto;
    animation: spin 2s infinite linear;
}

#loading, #loading_popup{
    width: 100%;
    height: 100%;
    top: 0px;
    left: 0px;
    position: fixed;
    display: none;
    opacity: 0.7;
    background-color: #fff;
    z-index: 99;
    text-align: center;
}

#loading-image {
    position: absolute;
    top: 100px;
    left: 600px;
    z-index: 100;
}

#loading-image_popup {
    position: absolute;
    top: 100px;
    left: 100px;
    z-index: 100;
}

.services_item_title_span {
    font-size: 18px;
    line-height: 21px;
    color: #828F9A;
    display: inline-block;
    padding-left: 10px;
}

.services_item_price {
    float: right;
    display: inline-block;
    font-size: 24px;
    line-height: 30px;
    color: #53d56c;
    /* margin-top: 10px; */
}
.services_item_description {
    font-size: 14px;
    /* line-height: 18px; */
    color: #828F9A;
    display: inline-block;
    margin-bottom: 10px;
    margin-left: 25px;
    margin-top: 5px;
}
#datetimepicker {
    max-width: 330px;
    font-size: 14px;
    line-height: 21px;
    margin: 0px auto;
    background: #d3d4ec;
    padding: 8px;
    border-radius: 5px;
}
.timeslots .timeslot_col {
    display: flex;
    flex-direction: column;
    width: calc(33% - 10px);
    float: left;
    background: #d3d4ec;
    padding: 5px;
    margin: 0px 10px 10px 0px;
    text-align: center;
}

/*////////////////////////////////////////////
////// appointment popup css chnages end /////////
//////////////////////////////////////////// */

  .filter_panel {background: #f7f7f7;margin: 10px 10px 10px 10px;border: 1pxsolid #eee;display: none;}
.card .card-body .filter_panel { padding: 20px;}
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
							<h4>Client Detail </h4>
							<div class="card-header-action">
							    <a href="{{route('admin.clients.index')}}" class="btn btn-primary">Client List</a>
							</div>

							<?php
                            //List if any attending inperssion session
                            $attendingSessionExist = \App\Models\CheckinLog::where('client_id', '=', $fetchedData->id)->where('status', '=', '2')->orderBy('id', 'DESC')->get();
                            //dd(count($attendingSessionExist));
                            if(!empty($attendingSessionExist) && count($attendingSessionExist) >0){?>
                                <div class="card-header-action">
                                    <a href="javascript:void(0);" class="btn btn-primary complete_session" style="margin-left:5px;" data-clientid="<?php echo $fetchedData->id;?>">Complete Session</a>
                                </div>
                            <?php }?>

                           <a href="javascript:;" class="btn btn-theme btn-theme-sm filter_btn"><i class="fas fa-filter"></i> Filter</a>
						</div>

                         <div class="filter_panel">
                            <?php //echo $encodeId;?>
                            <form action="{{URL::to('/admin/clients/detail/'.$encodeId)}}" method="get">
                                <div class="row" style="padding-left: 10px;">

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="user" class="col-form-label">Search By User</label>
                                            {{ Form::text('user', Request::get('user'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter user', 'id' => 'user' )) }}
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="keyword" class="col-form-label">Search By keyword</label>
                                            {{ Form::text('keyword', Request::get('keyword'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter any keyword', 'id' => 'keyword' )) }}
                                        </div>
                                    </div>

                                    <div class="col-md-4" style="padding-top: 35px;">
                                        {{ Form::submit('Search', ['class'=>'btn btn-primary btn-theme-lg' ]) }}
                                        <a class="btn btn-info" href="{{URL::to('/admin/clients/detail/'.$encodeId)}}">Reset</a>
                                    </div>
                                </div>
                            </form>
                        </div>


					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-4 col-md-4 col-lg-4 left_section">
					<div class="card author-box left_section_upper">
						<div class="card-body">
							<div class="author-box-center">
							<span class="author-avtar" style="background: rgb(68, 182, 174);"><b>{{substr($fetchedData->first_name, 0, 1)}}{{substr($fetchedData->last_name, 0, 1)}}</b></span>
								<div class="clearfix"></div>
								<div class="author-box-name">
									<a href="#">{{$fetchedData->first_name}} {{$fetchedData->last_name}}</a>
										<span style="display:block;">{{$fetchedData->client_id}}</span>
								</div>
							<?php /*	<div class="author-rating">
									<a href="javascript:;" rating="Lost" class="change_client_status lost <?php if($fetchedData->rating == 'Lost'){ echo 'active'; } ?>" style=""><i class="fas fa-exclamation-triangle"></i> Lost</a>
									<a href="javascript:;" rating="Cold" class="change_client_status cold <?php if($fetchedData->rating == 'Cold'){ echo 'active'; } ?>" style=""><i class="fas fa-snowflake"></i> Cold</a>
									<a href="javascript:;" rating="Warm" class="change_client_status warm <?php if($fetchedData->rating == 'Warm'){ echo 'active'; } ?>" style=""><i class="fas fa-mug-hot" ></i> Warm</a>
									<a href="javascript:;" rating="Hot" class="change_client_status hot <?php if($fetchedData->rating == 'Hot'){ echo 'active'; } ?>" style=""><i class="fas fa-fire"></i> Hot</a>
								</div> */ ?>
								
                                    <div class="author-mail_sms">
                                      <!--<a href="#" title="Compose SMS"><i class="fas fa-comment-alt"></i></a>-->
                                      <a href="javascript:;" data-id="{{@$fetchedData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" class="sendmsg" title="Send Message"><i class="fas fa-comment-alt"></i></a>

                                      <a href="javascript:;" data-id="{{@$fetchedData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" class="clientemail" title="Compose Mail"><i class="fa fa-envelope"></i></a>
                                      <a href="{{URL::to('/admin/clients/edit/'.base64_encode(convert_uuencode(@$fetchedData->id)))}}" title="Edit"><i class="fa fa-edit"></i></a>
                                      @if($fetchedData->is_archived == 0)
                                          <a class="arcivedval" href="javascript:;" onclick="arcivedAction({{$fetchedData->id}}, 'admins')" title="Archive"><i class="fas fa-archive"></i></a>
                                      @else
                                          <a class="arcivedval" style="background-color:red;" href="javascript:;" onclick="arcivedAction({{$fetchedData->id}}, 'admins')" title="UnArchive"><i style="color: #fff;" class="fas fa-archive"></i></a>
                                      @endif

                                      @if($fetchedData->is_greview_mail_sent == 1)
                                          <span style="display: block;color:#4caf50;">Google review invitation already Sent</span>
                                      @else
                                          <a class="googleReviewBtn" href="javascript:;" data-is_greview_mail_sent="{{@$fetchedData->is_greview_mail_sent}}" title="Google Review"><i class="fab fa-google"></i></a>
                                      @endif
                                  </div>
                              
                              
									<p>
									<a  onclick="return confirm('Are you sure?')" class="badge-outline col-greenf <?php if($fetchedData->type == 'client'){ echo 'active'; } ?>" href="{{URL::to('/admin/clients/changetype/'.base64_encode(convert_uuencode($fetchedData->id)).'/client')}}">Client</a>
								    <a  onclick="return confirm('Are you sure?')" href="{{URL::to('/admin/clients/changetype/'.base64_encode(convert_uuencode($fetchedData->id)).'/lead')}}" class="badge-outline col-greenf <?php if($fetchedData->type == 'lead'){ echo 'active'; } ?>">Lead</a>

								</p>
								<p><button type="button" class="btn btn-primary btn-block" data-container="body" data-role="popover" data-placement="bottom" data-html="true" data-content="<div id=&quot;popover-content&quot;>
									<h4 class=&quot;text-center&quot;>Assign User</h4>
									<div class=&quot;clearfix&quot;></div>

							    <div class=&quot;box-header with-border&quot;>
								    <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
										<label for=&quot;inputSub3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Select Assignee</label>
										<div class=&quot;col-sm-9&quot;>

											<select class=&quot;assigneeselect2 form-control selec_reg&quot; id=&quot;rem_cat&quot; name=&quot;rem_cat&quot; onchange=&quot;&quot;>
												<option value=&quot;&quot; >Select</option>
												@foreach(\App\Models\Admin::select('id', 'office_id', 'first_name', 'last_name')->where('role','!=',7)->where('status',1)->orderby('first_name','ASC')->get() as $admin)

												<?php
												$branchname = \App\Models\Branch::select('id', 'office_name')->where('id',$admin->office_id)->first();
												?>
												<option value=&quot;<?php echo $admin->id; ?>&quot;><?php echo $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')'; ?></option>
												@endforeach
											</select>
										</div>
										<div class=&quot;clearfix&quot;></div>
								    </div>
							    </div><div id=&quot;popover-content&quot;>
							    <div class=&quot;box-header with-border&quot;>
								    <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
										<label for=&quot;inputEmail3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Note</label>
										<div class=&quot;col-sm-9&quot;>
										    <textarea id=&quot;assignnote&quot; class=&quot;form-control summernote-simple f13&quot; placeholder=&quot;Enter an note....&quot; type=&quot;text&quot;></textarea>
										</div>
										<div class=&quot;clearfix&quot;></div>
								    </div>
							    </div>
								<div class=&quot;box-header with-border&quot;>
								    <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
										<label for=&quot;inputEmail3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Date</label>
										<div class=&quot;col-sm-9&quot;>
											<input type=&quot;date&quot; class=&quot;form-control f13&quot; placeholder=&quot;yyyy-mm-dd&quot; id=&quot;popoverdatetime&quot; value=&quot;<?php echo date('Y-m-d');?>&quot;name=&quot;popoverdate&quot;>
										</div>
										<div class=&quot;clearfix&quot;></div>
								    </div>
							    </div>

                                <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
                                    <label for=&quot;inputSub3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Group</label>
                                    <div class=&quot;col-sm-9&quot;>
                                        <select class=&quot;assigneeselect2 form-control selec_reg&quot; id=&quot;task_group&quot; name=&quot;task_group&quot;>
                                            <option value=&quot;&quot;>Select</option>
                                            <option value=&quot;Call&quot;>Call</option>
                                            <option value=&quot;Checklist&quot;>Checklist</option>
                                            <option value=&quot;Review&quot;>Review</option>
                                            <option value=&quot;Query&quot;>Query</option>
                                            <option value=&quot;Urgent&quot;>Urgent</option>
                                        </select>
                                    </div>
                                    <div class=&quot;clearfix&quot;></div>
                                </div>

								<input id=&quot;assign_client_id&quot;  type=&quot;hidden&quot; value=&quot;{{base64_encode(convert_uuencode(@$fetchedData->id))}}&quot;>
							    <div class=&quot;box-footer&quot; style=&quot;padding:10px 0&quot;>
							    <div class=&quot;row&quot;>
									<input type=&quot;hidden&quot; value=&quot;&quot; id=&quot;popoverrealdate&quot; name=&quot;popoverrealdate&quot; />
							    </div>
							    <div class=&quot;row text-center&quot;>
									<div class=&quot;col-md-12 text-center&quot;>
									<button  class=&quot;btn btn-danger&quot; id=&quot;assignUser&quot;>Assign User</button>
									</div>
							    </div>
					    </div>" data-original-title="" title=""> Action</button></p>
							</div>
							<?php
									$agent = \App\Models\Agent::select('id', 'full_name', 'email')->where('id',@$fetchedData->agent_id)->first();
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

				<!--</div>
				<div class="col-8 col-md-8 col-lg-8">-->
                  
					<div class="card left_section_lower">
						<div class="card-header">
						    <div class="float-left">
								<h4>Personal Details</h4>
                                <div style="width: 170px;color: #212529;">
                                    Last Updated:
                                    <?php
                                    if( isset($fetchedData->updated_at) && $fetchedData->updated_at != "" ){
                                        echo date('d/m/Y',   strtotime('-5 hours 30 minute', strtotime($fetchedData->updated_at)));
                                    } ?>
                                </div>
                            </div>

                            <div class="add_note" style="text-align: right;width:155px;margin-top:-40px;">

                                 <!--<input type="checkbox" class="not_picked_call" name="not_picked_call" value="<?php //echo $fetchedData->not_picked_call;?>" <?php //if( isset($fetchedData->not_picked_call) && $fetchedData->not_picked_call == '1' ) { echo 'checked';}?>> NP -->
                                <a href="javascript:;" style="border-radius: 0px;padding: 2px 5px;" datatype="not_picked_call" class="not_picked_call btn btn-primary btn-sm">NP</a>



                                <a href="javascript:;" style="border-radius: 0px;padding: 2px 5px;" datatype="note" class="create_note_d btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add Notes</a>
                            </div>


						</div>
						<div class="card-body">
						    <p class="clearfix">
								<span class="float-left">Date Of Birth / Age:</span>
								<span class="float-right text-muted">
									<?php
										if($fetchedData->dob != '' && $fetchedData->dob != '0000-00-00'){
										    echo $dob = date('d/m/Y', strtotime($fetchedData->dob));
										}
										?>	<?php
										if($fetchedData->age != ''){
										    echo ' / '.$fetchedData->age;
										}
									?>
								</span>
							</p>
							<p class="clearfix">
								<span class="float-left">Visa:</span>
								<span class="float-right text-muted">{{$fetchedData->visa_type}}
									<?php
										if($fetchedData->visa_opt != ''){
										    //echo '<br>'.$fetchedData->visa_opt;
										    echo $fetchedData->visa_opt;
										}
									?>
								</span>
							</p>
							<p class="clearfix">
								<span class="float-left">Visa Expiry:</span>
								<span class="float-right text-muted"><?php
										if($fetchedData->visaExpiry != '' && $fetchedData->visaExpiry != '0000-00-00'){
										    echo date('d/m/Y', strtotime($fetchedData->visaExpiry));
										}
										?>
								</span>
							</p>


							<p class="clearfix">
								<span class="float-left">Phone No:</span>
								<span class="float-right text-muted">
                                    {{--$fetchedData->phone--}} {{--@if($fetchedData->att_phone != '') / {{$fetchedData->att_phone}} @endif--}}
                                    <?php
                                    if( \App\Models\ClientPhone::where('client_id', $fetchedData->id)->exists()) {
                                        $clientContacts = \App\Models\ClientPhone::select('client_phone','client_country_code','contact_type')->where('client_id', $fetchedData->id)->where('contact_type', '!=', 'Not In Use')->get();
                                    } else {
                                        if( \App\Models\Admin::where('id', $fetchedData->id)->exists()){
                                            $clientContacts = \App\Models\Admin::select('phone as client_phone','country_code as client_country_code','contact_type')->where('id', $fetchedData->id)->get();
                                        } else {
                                            $clientContacts = array();
                                        }
                                    }
                                    if( !empty($clientContacts) && count($clientContacts)>0 ){
                                        $phonenoStr = "";
                                        foreach($clientContacts as $conKey=>$conVal){
                                            //Check phone is verified or not
											$check_verified_phoneno = $conVal->client_country_code."".$conVal->client_phone;
											$verifiedNumber = \App\Models\VerifiedNumber::where('phone_number',$check_verified_phoneno)->where('is_verified', true)->first();


                                            if( isset($conVal->client_country_code) && $conVal->client_country_code != "" ){
                                                $client_country_code = $conVal->client_country_code;
                                            } else {
                                                $client_country_code = "";
                                            }

                                            if( isset($conVal->contact_type) && $conVal->contact_type != "" ){
												if( $conVal->contact_type == "Personal" ){
													if ( $verifiedNumber) {
														$phonenoStr .= $client_country_code."".$conVal->client_phone.'('.$conVal->contact_type .') <i class="fas fa-check-circle verified-icon fa-lg"></i> <br/>';
													} else {
														$phonenoStr .= $client_country_code."".$conVal->client_phone.'('.$conVal->contact_type .') <i class="far fa-circle unverified-icon fa-lg"></i> <br/>';
													}
												} else {
													$phonenoStr .= $client_country_code."".$conVal->client_phone.'('.$conVal->contact_type .') <br/>';
												}
											} else {
												$phonenoStr .= $client_country_code."".$conVal->client_phone.' <br/>';
                                            }
                                        }
                                        echo $phonenoStr;
                                    } else {
                                        echo "N/A";
                                    }?>
                                </span>
							</p>


							<p class="clearfix">
								<span class="float-left">Email / Is verified:</span>
								<span class="float-right text-muted">
								    {{$fetchedData->email}}

                                    <?php
                                    if( isset($fetchedData->manual_email_phone_verified) && $fetchedData->manual_email_phone_verified == '1' )
                                    {
                                        //echo '<span style="color:green;">/Already Verified</span>';
                                        echo '<i class="fas fa-check-circle verified-icon fa-lg"></i>';
                                    } else {
                                        //echo '<span style="color:red;">/Not Now</span>';
                                        echo '<i class="far fa-circle unverified-icon fa-lg"></i>';
                                    }?>
                                </span>
							</p>

                            <?php if( isset($fetchedData->email_verified_at) && $fetchedData->email_verified_at != "" ){ ?>
                                <p class="clearfix">
                                    <span class="float-left">Email Verified At:</span>
                                    <span class="float-right text-muted">{{ date('d/m/Y',strtotime($fetchedData->email_verified_at))}}</span>
                                </p>
                            <?php } ?>

							<p class="clearfix">
								<span class="float-left">City:</span>
								<span class="float-right text-muted">{{$fetchedData->city}}</span>
							</p>
							<p class="clearfix">
								<span class="float-left">Nominated Occupation:</span>
								<span class="float-right text-muted">{{$fetchedData->nomi_occupation}}</span>
							</p>
							<p class="clearfix">
								<span class="float-left">Highest Qualification in Australia:</span>
								<span class="float-right text-muted">{{$fetchedData->high_quali_aus}}</span>
							</p>
							<p class="clearfix">
								<span class="float-left">Highest Qualification Overseas:</span>
								<span class="float-right text-muted">{{$fetchedData->high_quali_overseas}}</span>
							</p>
							<p class="clearfix">
								<span class="float-left">Work experience Australia:</span>
								<span class="float-right text-muted">{{$fetchedData->relevant_work_exp_aus}}</span>
							</p>
								<p class="clearfix">
								<span class="float-left">Work experience Offshore:</span>
								<span class="float-right text-muted">{{$fetchedData->relevant_work_exp_over}}</span>
							</p>
							<p class="clearfix">
								<span class="float-left">Overall English score: </span>
								<?php
									$testscores = \App\Models\TestScore::where('client_id', $fetchedData->id)->where('type', 'client')->first();
								?>
								<span class="float-right text-muted">{{ isset($fetchedData->married_partner) ? $fetchedData->married_partner : '' }}

                                  <?php /* if(@$testscores->score_2 != ''){ echo @$testscores->score_2; }else{ echo '-'; } ?> / <?php if(@$testscores->score_3 != ''){ echo @$testscores->score_3; }else{ echo '-'; } */ ?></span>
							</p>
							<p class="clearfix">
								<span class="float-left">Preferred Intake:</span>
								<span class="float-right text-muted"><?php if($fetchedData->preferredIntake != ''){ ?>{{date('M Y', strtotime($fetchedData->preferredIntake))}}<?php } ?></span>
							</p>
							 <p class="clearfix">
								<span class="float-left">Naati/PY</span>
								<span class="float-right text-muted"><?php if($fetchedData->naati_py != ''){ ?>{{$fetchedData->naati_py}}<?php } ?></span>
							</p>
							<div class="clearfix">
								<span class="float-left">Client Portal:</span>
								<div class="custom-switches float-right">
									<label class="custom-switch">
										<input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input" checked>
										<span class="custom-switch-indicator"></span>
									</label>
								</div>
							</div>
							<?php
								$addedby = \App\Models\Admin::select('id', 'first_name', 'last_name')->where('id',@$fetchedData->user_id)->first();
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
                                use Illuminate\Support\Str;
                                //dd($fetchedData->assignee);
                                if( Str::contains($fetchedData->assignee, ',')){
                                    $assigneeUArr = explode(",",$fetchedData->assignee);
                                    $assigneeArr = \App\Models\Admin::select('id', 'first_name', 'last_name')->whereIn('id',$assigneeUArr)->get();
                                } else {
                                    $assigneeU = $fetchedData->assignee;
                                    $assigneeArr = \App\Models\Admin::select('id', 'first_name', 'last_name')->where('id',$assigneeU)->get();
                                }
                                //dd($assigneeArr);
                                ?>
							<div class="client_assign client_info_tags">
								<span class="">Assignee:</span>
								<span class="float-right text-muted">
								      <a href="javascript:;" data-id="{{$fetchedData->id}}" class="btn btn-primary openassigneeshow btn-sm"><i class="fa fa-plus"></i> Edit</a>
								    </span>
								    <div class="clearfix"></div>

								<?php
                                if( !empty($assigneeArr) && count($assigneeArr) >0 ){
                                    foreach ($assigneeArr as $assignee) {
                                ?>

								{{-- @if($assignee) --}}
                                <div class="client_info">
                                    <div class="cl_logo">{{substr(@$assignee->first_name, 0, 1)}}</div>
                                    <div class="cl_name">
                                        <span class="name">{{@$assignee->first_name}}</span>
                                        <!--<span class="email">{{--@$assignee->email--}}</span>-->
                                    </div>
                                </div>
                                <?php
                                    }
                                }
                                else { echo "-"; } ?>

								{{-- @else --}}
									<!-- -  -->
								{{-- @endif --}}

								<div class="assigneeshow" style="display:none;">
								    <table>
								        <tr>
								            <td><select class="form-control select2" id="changeassignee" name="changeassignee[]" multiple="multiple">
						                 	<?php
												foreach(\App\Models\Admin::select('id', 'office_id', 'first_name', 'last_name')->where('role','!=',7)->orderby('first_name','ASC')->get() as $admin){
													$branchname = \App\Models\Branch::select('id', 'office_name')->where('id',$admin->office_id)->first();
											?>
												<option value="<?php echo $admin->id; ?>"><?php echo $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')'; ?></option>
											<?php } ?>
												</select></td>
											<td><a class="saveassignee btn btn-success" data-id="<?php echo $fetchedData->id; ?>" href="javascript:;">Save</a></td>
											<td><a class="closeassigneeshow" href="javascript:;"><i class="fa fa-times"></i></a></td>
								        </tr>
								    </table>
								</div>
							</div>
							<div class="client_assign client_info_tags">
								<p class="clearfix">
                                    <span class="float-left">Services Taken:</span>
                                    <!--<span class="float-right text-muted">
                                        <a href="javascript:;" data-id="{{--$fetchedData->id--}}" class="btn btn-primary serviceTaken btn-sm"><i class="fa fa-plus"></i> Add</a>
                                    </span>-->
                                </p>

                                 <div class="client_info">
								    <ul style="margin-left: -35px;">
										<?php
                                        $serviceTakenArr = \App\Models\clientServiceTaken::where('client_id', $fetchedData->id )->orderBy('created_at', 'desc')->get();
                                        //dd($serviceTakenArr);
                                        if( !empty($serviceTakenArr) && count($serviceTakenArr) >0 ){
                                            foreach ($serviceTakenArr as $tokenkey => $tokenval) {
                                                if($tokenval['service_type']  == "Migration") {
                                                    echo $tokenval['service_type']."-".htmlspecialchars($tokenval['mig_ref_no'])."-".htmlspecialchars($tokenval['mig_service'])."-".htmlspecialchars($tokenval['mig_notes']). "<br/>";
                                                } else if($tokenval['service_type']  == "Education") {
                                                    echo $tokenval['service_type']."-".htmlspecialchars($tokenval['edu_course'])."-".htmlspecialchars($tokenval['edu_college'])."-".htmlspecialchars($tokenval['edu_service_start_date'])."-".htmlspecialchars($tokenval['edu_notes']). "<br/>";
                                                }
                                            }
                                        } ?>
									</ul>
								</div>
                             </div>

                             <div class="">
								<p class="clearfix">
                                    <span class="float-left">Related files:</span>
                                </p>

								<div class="client_info">
								    <ul >
										<?php
											//$relatedclientss = \App\Models\Admin::select('id',  'first_name', 'last_name')->whereRaw("FIND_IN_SET($fetchedData->id,related_files)")->get();
											//foreach($relatedclientss AS $res){
										?>
											<!--<li><a target="_blank" href="{{--URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$res->id)))--}}">{{--$res->first_name--}} {{--$res->last_name--}}</a></li>-->
										<?php //} ?>

										<?php
										if($fetchedData->related_files != ''){
											$exploder = explode(',', $fetchedData->related_files);

										?>
										<?php   foreach($exploder AS $EXP){
											$relatedclients = \App\Models\Admin::where('id', $EXP)->first();
										?>
											<li><a target="_blank" href="{{URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$relatedclients->id)))}}">{{$relatedclients->first_name}} {{$relatedclients->last_name}}</a></li>
										<?php } ?>
										<?php } ?>
									</ul>
								</div>
							</div>

							<p class="clearfix">
								<span class="float-left">Tag(s):</span>
								<span class="float-right text-muted">
									<a href="javascript:;" data-id="{{$fetchedData->id}}" class="btn btn-primary opentagspopup btn-sm"><i class="fa fa-plus"></i> Add</a>
								</span>
							</p>
							<p>
								<?php $tags = '';
									if($fetchedData->tagname != ''){
										$rs = explode(',', $fetchedData->tagname);

										foreach($rs as $key=>$r){
											$stagd = \App\Models\Tag::where('id','=',$r)->first();
											if($stagd){
											?>
												<span class="ui label ag-flex ag-align-center ag-space-between" style="display: inline-flex;">
													<span class="col-hr-1" style="font-size: 12px;">{{@$stagd->name}} <!--<a href="{{--URL::to('/admin/clients/removetag?rem_id='.$key.'&c='.$fetchedData->id)--}}" class="removetag" ><i class="fa fa-times"></i></a>--></span>
												</span>
											<?php
											}
										}
									}
								?>
							</p>
						</div>
					</div>
				</div>

				<!--<div class="col-12 col-md-12 col-lg-12">-->
              
             
                <div class="col-8 col-md-8 col-lg-8 ">
				<div class="card right_section">
						<div class="card-body">
							<ul class="nav nav-pills" id="client_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link <?php if(!isset($_GET['tab'])){ echo 'active'; } ?>" data-toggle="tab" id="activities-tab" href="#activities" role="tab" aria-controls="activities" aria-selected="true">Activities</a>
								</li>

								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="noteterm-tab" href="#noteterm" role="tab" aria-controls="noteterm" aria-selected="false">Notes & Terms</a>
								</li>

								<li class="nav-item">
									<a class="nav-link <?php if(isset($_GET['tab']) && $_GET['tab'] == 'application'){ echo 'active'; } ?>" data-toggle="tab" id="application-tab" href="#application" role="tab" aria-controls="application" aria-selected="false">Applications</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="interested_service-tab" href="#interested_service" role="tab" aria-controls="interested_service" aria-selected="false">Interested Services</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="documents-tab" href="#documents" role="tab" aria-controls="documents" aria-selected="false">Education Documents</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="migrationdocuments-tab" href="#migrationdocuments" role="tab" aria-controls="migrationdocuments" aria-selected="false">Migration Documents</a>
								</li>

                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" id="alldocuments-tab" href="#alldocuments" role="tab" aria-controls="alldocuments" aria-selected="false">Documents</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" id="notuseddocuments-tab" href="#notuseddocuments" role="tab" aria-controls="notuseddocuments" aria-selected="false">Not Used Documents</a>
                                </li>

								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="appointments-tab" href="#appointments" role="tab" aria-controls="appointments" aria-selected="false">Appointments</a>
								</li>

								<!--<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="noteterm-tab" href="#noteterm" role="tab" aria-controls="noteterm" aria-selected="false">Notes & Terms</a>
								</li>-->

								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="quotations-tab" href="#quotations" role="tab" aria-controls="quotations" aria-selected="false">Quotations</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="accounts-tab" href="#accounts" role="tab" aria-controls="accounts" aria-selected="false">Accounts</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="conversations-tab" href="#conversations" role="tab" aria-controls="conversations" aria-selected="false">Conversations</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="tasks-tab" href="#tasks" role="tab" aria-controls="tasks" aria-selected="false">Tasks</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="education-tab" href="#education" role="tab" aria-controls="education" aria-selected="false">Education</a>
								</li>
								<!--<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="other_info-tab" href="#other_info" role="tab" aria-controls="other_info" aria-selected="false">Other Information</a>
								</li>-->
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="checkinlogs-tab" href="#checkinlogs" role="tab" aria-controls="checkinlogs" aria-selected="false">Check-In Logs</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="prevvisa-tab" href="#prevvisa" role="tab" aria-controls="prevvisa" aria-selected="false">Previous History</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="tab" id="clientinfoform-tab" href="#clientinfoform" role="tab" aria-controls="clientinfoform" aria-selected="false">Client Info Form</a>
								</li>
							</ul>
							<div class="tab-content" id="clientContent" style="padding-top:15px;">
								<div class="tab-pane fade <?php if(!isset($_GET['tab']) ){ echo 'show active'; } ?>" id="activities" role="tabpanel" aria-labelledby="activities-tab">
									<div class="activities">
										<?php
										//$activities = \App\Models\ActivitiesLog::where('client_id', $fetchedData->id)->orderby('created_at', 'DESC')->get();
										//->where('subject', '<>','added a note')

										if(
                                            ( isset($_REQUEST['user']) && $_REQUEST['user'] != "" )
                                            ||
                                            ( isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != "" )
                                        ){ //dd('ifff');
											$user_search = $_REQUEST['user'];
											$keyword_search = $_REQUEST['keyword'];

											if($user_search != "" && $keyword_search != "") { //dd('ifff111');
												$activities = \App\Models\ActivitiesLog::select('activities_logs.*','admins.first_name')
                                                ->leftJoin('admins', 'activities_logs.created_by', '=', 'admins.id')
												->where('activities_logs.client_id', $fetchedData->id)
												->where(function($query) use ($user_search) {
													$query->where('admins.first_name', 'like', '%'.$user_search.'%');
												})
												->where(function($query) use ($keyword_search) {
													$query->where('activities_logs.description', 'like', '%'.$keyword_search.'%');
													$query->orWhere('activities_logs.subject', 'like', '%'.$keyword_search.'%');
												})
												->orderby('activities_logs.created_at', 'DESC')
												->get();
											}
											else if($user_search == "" && $keyword_search != "") { //dd('ifff2222');
												$activities = \App\Models\ActivitiesLog::select('activities_logs.*')
												->where('activities_logs.client_id', $fetchedData->id)
												->where(function($query) use ($keyword_search) {
													$query->where('activities_logs.description', 'like', '%'.$keyword_search.'%');
													$query->orWhere('activities_logs.subject', 'like', '%'.$keyword_search.'%');
												})
												->orderby('activities_logs.created_at', 'DESC')
												->get();
											}
											else if($user_search != "" && $keyword_search == "") { //dd('ifff333');
												$activities = \App\Models\ActivitiesLog::select('activities_logs.*','admins.first_name')
												->leftJoin('admins', 'activities_logs.created_by', '=', 'admins.id')
												->where('activities_logs.client_id', $fetchedData->id)
												->where(function($query) use ($user_search) {
													$query->where('admins.first_name', 'like', '%'.$user_search.'%');
												})
												->orderby('activities_logs.created_at', 'DESC')
												->get();
											}
										} else { //dd('elsee');
                                            /*if($fetchedData->id == 934){
                                              $activities = \App\Models\ActivitiesLog::where('client_id', $fetchedData->id)
                                                ->where('task_status',0)
                                              ->orderby('created_at', 'DESC')
                                              ->get(); //->where('subject', '<>','added a note')
                                              
                                            } else {*/
                                               $activities = \App\Models\ActivitiesLog::where('client_id', $fetchedData->id)
                                              ->orderby('created_at', 'DESC')
                                              ->get(); //->where('subject', '<>','added a note')
                                           // }
                                           
                                        }

										//dd($activities);
                                        foreach($activities as $activit){
											$admin = \App\Models\Admin::select('id', 'first_name', 'last_name')->where('id', $activit->created_by)->first();
                                            /*if($activit->use_for != ""){
                                                $receiver = \App\Models\Admin::where('id', $activit->use_for)->first();
                                                if($receiver->first_name){
                                                    $reciver_name = "to <b>{$receiver->first_name}</b>";
                                                } else {
                                                    $reciver_name = "";
                                                }
                                            } else {
                                                $reciver_name = "";
                                            }*/

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

                                                    <div class="right" style="float: right;margin-top: -40px;">
                                                        <?php if($activit->pin == 1){?>
                                                            <div class="pined_note"><i class="fa fa-thumbtack" style="font-size: 12px;color: #6777ef;"></i></div>
                                                        <?php } ?>

                                                        <div class="dropdown d-inline dropdown_ellipsis_icon">
                                                            <a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                                            <div class="dropdown-menu">
                                                                @if(Auth::user()->role == 1)
                                                                <a data-id="{{$activit->id}}" data-href="deleteactivitylog" class="dropdown-item deleteactivitylog" href="javascript:;" >Delete</a>
                                                               @endif
                                                                <?php if($activit->pin == 1){ ?>
                                                                    <a data-id="<?php echo $activit->id;?>"  class="dropdown-item pinactivitylog" href="javascript:;" >UnPin</a>
                                                                <?php
                                                                } else { ?>
                                                                    <a data-id="<?php echo $activit->id;?>"  class="dropdown-item pinactivitylog" href="javascript:;" >Pin</a>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>

													@if(!empty($activit->description))
                                                        @php
                                                            $description = $activit->description;
                                                        @endphp

                                                        @if(strpos($description, '<xml>') !== false || strpos($description, '<o:OfficeDocumentSettings>') !== false)
                                                            <p>{!! htmlentities($description) !!}</p>
                                                        @else
                                                            <p>{!! $description !!}</p>
                                                        @endif
                                                    @endif

                                                    @if(isset($activit->task_status) && $activit->task_status == '1')
														<p style="color:#4caf50;"><b>Completed</b></p>
													@endif

                                                    @if($activit->followup_date != '')
														<p>{!!$activit->followup_date!!}</p>
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
								<div class="tab-pane fade <?php if(isset($_GET['tab']) && $_GET['tab'] == 'application'){ echo 'show active'; } ?>" id="application" role="tabpanel" aria-labelledby="application-tab">
									<div class="card-header-action text-right if_applicationdetail" style="padding-bottom:15px;">
										<a href="javascript:;" data-toggle="modal" data-target=".add_appliation" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="table-responsive if_applicationdetail">
										<table class="table text_wrap table-2">
											<thead>
												<tr>
													<th>Name</th>
													<th>Workflow</th>
													<th>Current Stage</th>
													<th>Status</th>
													<th>Start Date</th>
													<th>End Date</th>

													<th></th>
												</tr>
											</thead>
											<tbody class="applicationtdata">
											<?php
											$application_data=\App\Models\Application::where('client_id', $fetchedData->id)->orderby('created_at','Desc')->get();
											if(count($application_data) > 0){
											foreach($application_data as $alist){
												$productdetail = \App\Models\Product::where('id', $alist->product_id)->first();
												$partnerdetail = \App\Models\Partner::where('id', $alist->partner_id)->first();
												$PartnerBranch = \App\Models\PartnerBranch::where('id', $alist->branch)->first();
												$workflow = \App\Models\Workflow::where('id', $alist->workflow)->first();

                                                $application_assign_count = \App\Models\Note::where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->where('application_id',$alist->id)->where('client_id',$fetchedData->id)->count();
                                                //dd($application_assign_count);
												?>
												<tr id="id_{{$alist->id}}">
													<td>
                                                      <a class="openapplicationdetail" data-id="{{$alist->id}}" href="javascript:;" style="display:block;">
                                                        {{@$productdetail->name}}

                                                        <?php if( $application_assign_count > 0 ) { ?>
                                                           <span class="countTotalActivityAction" style="background: #1f1655;padding: 0px 5px;border-radius: 50%;color: #fff;margin-left: 5px;">{{ $application_assign_count }}</span>
                                                        <?php } ?>
                                                      </a>
                                                      <small>{{@$partnerdetail->partner_name}} ({{@$PartnerBranch->name}})</small>
                                                    </td>
													<td>{{@$workflow->name}}</td>
													<td>{{@$alist->stage}}</td>
													<td>
                                                      @if(@$alist->status == 0)
                                                      <span class="ag-label--circular" style="color: #6777ef" >In Progress</span>
                                                      @elseif(@$alist->status == 1)
                                                      <span class="ag-label--circular" style="color: #6777ef" >Completed</span>
                                                      @elseif(@$alist->status == 2)
                                                      <span class="ag-label--circular" style="color: red;" >Discontinued</span>
                                                      @elseif(@$alist->status == 3)
                                                      <span class="ag-label--circular" style="color: red;" >Cancelled</span>
                                                      @elseif(@$alist->status == 4)
                                                      <span class="ag-label--circular" style="color: red;" >Withdrawn</span>
                                                      @elseif(@$alist->status == 5)
                                                      <span class="ag-label--circular" style="color: red;" >Deferred</span>
                                                      @elseif(@$alist->status == 6)
                                                      <span class="ag-label--circular" style="color: red;" >Future</span>
                                                      @elseif(@$alist->status == 7)
                                                      <span class="ag-label--circular" style="color: red;" >VOE</span>
                                                      @elseif(@$alist->status == 8)
                                                      <span class="ag-label--circular" style="color: red;" >Refund</span>
                                                      @endif
                                                    </td>

													<td><?php if(@$alist->start_date != ''){ echo date('d/m/Y', strtotime($alist->start_date)); } ?></td>
													<td><?php if(@$alist->end_date != ''){ echo date('d/m/Y', strtotime($alist->end_date)); } ?></td>

                                                  <?php
                                                  if( Auth::user()->role == 1 )
                                                  { //super admin or admin
                                                  ?>
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">

																<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction({{@$alist->id}}, 'applications')"><i class="fas fa-trash"></i> Delete</a>
															</div>
														</div>
													</td>

                                                   <?php
                                                    }?>
												</tr>
												<?php
											}

											?>

											</tbody>
											<?php
											}else{ ?>
											<tbody>
												<tr>
													<td style="text-align:center;" colspan="10">
														No Record found
													</td>
												</tr>
											</tbody>
									<?php	} ?>
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
									$inteservices = \App\Models\InterestedService::where('client_id',$fetchedData->id)->orderby('created_at', 'DESC')->get();
									foreach($inteservices as $inteservice){
										$workflowdetail = \App\Models\Workflow::where('id', $inteservice->workflow)->first();
										 $productdetail = \App\Models\Product::where('id', $inteservice->product)->first();
										$partnerdetail = \App\Models\Partner::where('id', $inteservice->partner)->first();
										$PartnerBranch = \App\Models\PartnerBranch::where('id', $inteservice->branch)->first();
										$admin = \App\Models\Admin::select('id','first_name', 'last_name')->where('id', $inteservice->user_id)->first();
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


												$appfeeoption = \App\Models\ServiceFeeOption::where('app_id', $inteservice->id)->first();

												$totl = 0.00;
												$net = 0.00;
												$discount = 0.00;
												if($appfeeoption){
													$appfeeoptiontype = \App\Models\ServiceFeeOptionType::where('fee_id', $appfeeoption->id)->get();
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
															<a data-id="{{$inteservice->id}}" data-href="deleteservices" class="dropdown-item deletenote" href="javascript:;">Delete</a>
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
												<input type="hidden" name="doctype" value="education">
											<!--<a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>-->

											<input class="docupload" multiple type="file" name="document_upload[]"/>
											</form>
										</div>
									</div>
									<div class="list_data col-6 col-md-6 col-lg-6" style="display:inline-block;vertical-align: top;">
										<div class="">
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
										$fetchd = \App\Models\Document::where('client_id',$fetchedData->id)->where('doc_type', 'education')->where('type','client')->orderby('created_at', 'DESC')->get();
										foreach($fetchd as $fetch){
										$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
										?>
													<tr class="drow" id="id_{{$fetch->id}}">
													<td  style="white-space: initial;">
														<div data-id="{{$fetch->id}}" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
															<a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset('/public/img/documents/'.$fetch->myfile); ?>','preview-container-documentlist')">
                                                                <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                                            </a>
														</div>
													</td>
													<td style="white-space: initial;"><?php echo $admin->first_name; ?></td>

													<td style="white-space: initial;"><?php echo date('d/m/Y', strtotime($fetch->created_at)); ?></td>
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">
																<a class="dropdown-item renamedoc" href="javascript:;">Rename</a>
																<a target="_blank" class="dropdown-item" href="{{URL::to('/public/img/documents')}}/<?php echo $fetch->myfile; ?>">Preview</a>
																<?php
																$explodeimg = explode('.',$fetch->myfile);
                                          						if($explodeimg[1] == 'jpg'|| $explodeimg[1] == 'png'|| $explodeimg[1] == 'jpeg'){
																?>
																	<a target="_blank" class="dropdown-item" href="{{URL::to('/admin/document/download/pdf')}}/<?php echo $fetch->id; ?>">PDF</a>
																	<?php } ?>
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
										$admin = \App\Models\Admin::select('id', 'first_name','email')->where('id', $fetch->user_id)->first();
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
                                     <!-- Container for File Preview -->
                            		<div class="col-5 col-md-5 col-lg-5 file-preview-container preview-container-documentlist">
                            			<p style="color:#000;">Click on a file to preview it here.</p>
                            		</div>
								</div>
                                      
								<div class="tab-pane fade" id="migrationdocuments" role="tabpanel" aria-labelledby="migrationdocuments-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<div class="document_layout_type">
											<a href="javascript:;" class="list active"><i class="fas fa-list"></i></a>
											<a href="javascript:;" class="grid"><i class="fas fa-columns"></i></a>
										</div>
										<div class="migration_upload_document" style="display:inline-block;">
                                              <form method="POST" enctype="multipart/form-data" id="mig_upload_form">
                                                  @csrf
                                                  <input type="hidden" name="clientid" value="{{$fetchedData->id}}">
                                                  <input type="hidden" name="type" value="client">
                                                  <input type="hidden" name="doctype" value="migration">
                                                  <!--<a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>-->

                                                  <input class="migdocupload" multiple type="file" name="document_upload[]"/>
											</form>
										</div>
									</div>
									<div class="list_data col-6 col-md-6 col-lg-6" style="display:inline-block;vertical-align: top;">
										<div class="">
											<table class="table text_wrap">
												<thead>
													<tr>
														<th>File Name</th>
														<th>Added By</th>

														<th>Added Date</th>
														<th></th>
													</tr>
												</thead>
												<tbody class="tdata migdocumnetlist">
										<?php
										$fetchd = \App\Models\Document::where('client_id',$fetchedData->id)->where('doc_type', 'migration')->where('type','client')->orderby('created_at', 'DESC')->get();
										//dd($fetchd);
										foreach($fetchd as $fetch){
										$admin = \App\Models\Admin::select('id', 'first_name','email')->where('id', $fetch->user_id)->first();
										?>
													<tr class="drow" id="id_{{$fetch->id}}">
													<td  style="white-space: initial;">
														<div data-id="{{$fetch->id}}" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
															<a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset('/public/img/documents/'.$fetch->myfile); ?>','preview-container-migrationdocumentlist')">
                                                                <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                                            </a>
														</div>
													</td>
													<td style="white-space: initial;"><?php echo $admin->first_name; ?></td>

													<td style="white-space: initial;"><?php echo date('d/m/Y', strtotime($fetch->created_at)); ?></td>
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">
																<a class="dropdown-item renamedoc" href="javascript:;">Rename</a>
																<a target="_blank" class="dropdown-item" href="{{URL::to('/public/img/documents')}}/<?php echo $fetch->myfile; ?>">Preview</a>
																<?php
																$explodeimg = explode('.',$fetch->myfile);
																if($explodeimg[1] == 'jpg'|| $explodeimg[1] == 'png'|| $explodeimg[1] == 'jpeg'){
																?>
																	<a target="_blank" class="dropdown-item" href="{{URL::to('/admin/document/download/pdf')}}/<?php echo $fetch->id; ?>">PDF</a>
																	<?php } ?>
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
									<div class="grid_data miggriddata">
									<?php
									foreach($fetchd as $fetch){
										$admin = \App\Models\Admin::select('id', 'first_name','email')->where('id', $fetch->user_id)->first();
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
                                  
                                     <!-- Container for File Preview -->
                                    <div class="col-5 col-md-5 col-lg-5 file-preview-container preview-container-migrationdocumentlist">
                                        <p style="color:#000;">Click on a file to preview it here.</p>
                                    </div>
								</div>



                                 <div class="tab-pane fade" id="alldocuments" role="tabpanel" aria-labelledby="alldocuments-tab">
                                    <div class="card-header-action text-right" style="padding-bottom:15px;">
                                        <div class="document_layout_type">
                                            <a href="javascript:;" class="list active"><i class="fas fa-list"></i></a>
                                            <a href="javascript:;" class="grid"><i class="fas fa-columns"></i></a>
                                        </div>
                                        <a href="javascript:;" class="btn btn-primary add_alldocument_doc"><i class="fa fa-plus"></i> Add Checklist</a>
                                    </div>
                                    <div class="list_data col-6 col-md-6 col-lg-6" style="display:inline-block;vertical-align: top;">
                                        <div class="">
                                            <table class="table text_wrap">
                                                <thead>
                                                    <tr>
                                                        <th>SNo.</th>
                                                        <th>Checklist</th>
                                                        <th>Added By</th>
                                                        <th>File Name</th>
                                                        <!--<th>Verified By</th>-->
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="tdata alldocumnetlist">
                                                    <?php
                                                    $fetchd = \App\Models\Document::where('client_id',$fetchedData->id)->whereNull('not_used_doc')->where('doc_type', 'documents')->where('type','client')->orderby('updated_at', 'DESC')->get();
                                                    foreach($fetchd as $docKey=>$fetch)
                                                    {
                                                        $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                                                        //Checklist verified by
                                                        /*if( isset($fetch->checklist_verified_by) && $fetch->checklist_verified_by != "") {
                                                            $checklist_verified_Info = \App\Models\Admin::select('first_name')->where('id', $fetch->checklist_verified_by)->first();
                                                            $checklist_verified_by = $checklist_verified_Info->first_name;
                                                        } else {
                                                            $checklist_verified_by = 'N/A';
                                                        }

                                                        if( isset($fetch->checklist_verified_at) && $fetch->checklist_verified_at != "") {
                                                            $checklist_verified_at = date('d/m/Y', strtotime($fetch->checklist_verified_at));
                                                        } else {
                                                            $checklist_verified_at = 'N/A';
                                                        }*/
                                                        ?>
                                                        <tr class="drow" id="id_{{$fetch->id}}">
                                                            <td><?php echo $docKey+1;?></td>
                                                            <td style="white-space: initial;">
                                                                <div data-id="<?php echo $fetch->id;?>" data-personalchecklistname="<?php echo $fetch->checklist; ?>" class="personalchecklist-row">
                                                                    <span><?php echo $fetch->checklist; ?></span>
                                                                </div>
                                                            </td>
                                                            <td style="white-space: initial;">
                                                                <?php
                                                                echo $admin->first_name. "<br>";
                                                                echo date('d/m/Y', strtotime($fetch->created_at));
                                                                ?>
                                                            </td>
                                                            <td style="white-space: initial;">
                                                                <?php
                                                                if( isset($fetch->file_name) && $fetch->file_name !=""){ ?>
                                                                    <div data-id="{{$fetch->id}}" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
                                                                        <?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
                                                                            <!--<a target="_blank" class="dropdown-item" href="<?php //echo $fetch->myfile; ?>" style="white-space: initial;">
                                                                                <i class="fas fa-file-image"></i> <span><?php //echo $fetch->file_name; ?><?php //echo '.'.$fetch->filetype; ?></span>
                                                                            </a>-->
                                                                      
                                                                            <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-alldocumentlist')">
                                                                                <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                                                            </a>
                                                                        <?php } else {  //For old file upload
                                                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                                                            $myawsfile = $url.$fetchedData->client_id.'/'.$fetch->doc_type.'/'.$fetch->myfile;
                                                                            
                                                                            ?>
                                                                            <!--<a target="_blank" class="dropdown-item" href="<?php //echo $url.$fetchedData->client_id.'/'.$fetch->doc_type.'/'.$fetch->myfile; ?>" style="white-space: initial;">
                                                                                <i class="fas fa-file-image"></i> <span><?php //echo $fetch->file_name; ?><?php //echo '.'.$fetch->filetype; ?></span>
                                                                            </a>-->
                                                                      
                                                                            <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($myawsfile); ?>','preview-container-alldocumentlist')">
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
                                                                            <input type="hidden" name="type" value="client">
                                                                            <input type="hidden" name="doctype" value="documents">
                                                                            <a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>
                                                                            <input class="alldocupload" data-fileid="<?php echo $fetch->id;?>" type="file" name="document_upload"/>
                                                                        </form>
                                                                    </div>
                                                                <?php
                                                                }?>
                                                            </td>
                                                            <!--<td id="docverifiedby_<?php //echo $fetch->id;?>">
                                                                <?php
                                                                //echo $checklist_verified_by. "<br>";
                                                                //echo $checklist_verified_at;
                                                                ?>
                                                            </td>-->

                                                            <td>
                                                                <?php
                                                                if( isset($fetch->myfile) && $fetch->myfile != "")
                                                                { ?>
                                                                    <div class="dropdown d-inline">
                                                                        <button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                                        <div class="dropdown-menu">
                                                                            <a class="dropdown-item renamechecklist" href="javascript:;">Rename Checklist</a>
                                                                            <a class="dropdown-item renamealldoc" href="javascript:;">Rename File Name</a>

                                                                            <?php
                                                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                                                            ?>
                                                                            <?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
                                                                                <!--<a target="_blank" class="dropdown-item" href="<?php //echo $fetch->myfile; ?>">Preview</a>-->
                                                                                <a class="dropdown-item" href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-alldocumentlist')">Preview</a>
                                                                            <?php } else {  //For old file upload?>
                                                                                <a target="_blank" class="dropdown-item" href="<?php echo $url.$fetchedData->client_id.'/'.$fetch->doc_type.'/'.$fetch->myfile; ?>">Preview</a>
                                                                            <?php } ?>


                                                                            <?php
                                                                            $explodeimg = explode('.',$fetch->myfile);
                                                                            if(strtolower($explodeimg[1]) == 'jpg'|| strtolower($explodeimg[1]) == 'png'|| strtolower($explodeimg[1]) == 'jpeg')
                                                                            { ?>
                                                                            <a target="_blank" class="dropdown-item" href="{{URL::to('/admin/document/download/pdf')}}/<?php echo $fetch->id; ?>">PDF</a>
                                                                            <?php
                                                                            } ?>

                                                                            <?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
                                                                                <!--<a download class="dropdown-item" href="<?php //echo $fetch->myfile; ?>">Download</a>-->
                                                                                <a href="#" class="dropdown-item download-file" data-filelink="<?= $fetch->myfile ?>" data-filename="<?= $fetch->myfile_key ?>">Download</a>
                                                                            
                                                                            <?php } else {  //For old file upload?>
                                                                                <!--<a download class="dropdown-item" href="<?php //echo $url.$fetchedData->client_id.'/'.$fetch->doc_type.'/'.$fetch->myfile; ?>">Download</a>-->
                                                                                <a href="#" class="dropdown-item download-file" data-filelink="<?= $url.$fetchedData->client_id.'/'.$fetch->doc_type.'/'.$fetch->myfile; ?>" data-filename="<?= $fetch->file_name; ?>">Download</a>
                                                                            <?php } ?>

                                                                            <?php if( Auth::user()->role == 1 ){ //echo Auth::user()->role;//super admin ?>
                                                                            <a data-id="{{$fetch->id}}" class="dropdown-item deletenote" data-href="deletealldocs" href="javascript:;">Delete</a>
                                                                            <?php } ?>
                                                                            <a data-id="{{$fetch->id}}" class="dropdown-item verifydoc" data-doctype="documents" data-href="verifydoc" href="javascript:;">Verify</a>
                                                                            <a data-id="{{$fetch->id}}" class="dropdown-item notuseddoc" data-doctype="documents" data-href="notuseddoc" href="javascript:;">Not Used</a>
                                                                        </div>
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
                                                                <a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                                                <div class="dropdown-menu">
                                                                    <?php $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';?>

                                                                    <?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
                                                                        <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                                                        <a download class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Download</a>
                                                                    <?php } else {  //For old file upload?>
                                                                        <a target="_blank" class="dropdown-item" href="<?php echo $url.$fetchedData->client_id.'/'.$fetch->doc_type.'/'.$fetch->myfile; ?>">Preview</a>
                                                                        <a download class="dropdown-item" href="<?php echo $url.$fetchedData->client_id.'/'.$fetch->doc_type.'/'.$fetch->myfile; ?>">Download</a>
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
                                    <div style="margin-left: 10px;" class="col-5 col-md-5 col-lg-5 file-preview-container preview-container-alldocumentlist">
                                        <p style="color:#000;">Click on a file to preview it here.</p>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="notuseddocuments" role="tabpanel" aria-labelledby="notuseddocuments-tab">
									<!--<div class="card-header-action text-right" style="padding-bottom:15px;">
										<div class="document_layout_type">
											<a href="javascript:;" class="list active"><i class="fas fa-list"></i></a>
											<a href="javascript:;" class="grid"><i class="fas fa-columns"></i></a>
										</div>
                                    </div>-->
									<div class="list_data col-6 col-md-6 col-lg-6" style="display:inline-block;vertical-align: top;">
										<div class="">
											<table class="table text_wrap">
												<thead>
													<tr>
                                                        <!--<th>SNo.</th>-->
                                                        <th>Checklist</th>
                                                        <th>Added By</th>
                                                        <th>File Name</th>
                                                        <!--<th>Verified By</th>-->
                                                        <th></th>
													</tr>
												</thead>
												<tbody class="tdata notuseddocumnetlist">
                                                    <?php
                                                    //$fetchd = \App\Models\Document::where('client_id',$fetchedData->id)->where('not_used_doc', 1)->where('doc_type', 'personal')->where('type','client')->orderby('updated_at', 'DESC')->get();
                                                    $fetchd = \App\Models\Document::where('client_id', $fetchedData->id)
                                                    ->where('not_used_doc', 1)
                                                    ->where('type','client')
                                                    ->where('doc_type','documents')
                                                    ->orderBy('type', 'DESC')->get();
                                                    //dd($fetchd);
                                                    foreach($fetchd as $notuseKey=>$fetch)
                                                    {
                                                        $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                                                        //Checklist verified by
                                                        /*if( isset($fetch->checklist_verified_by) && $fetch->checklist_verified_by != "") {
                                                            $checklist_verified_Info = \App\Models\Admin::select('first_name')->where('id', $fetch->checklist_verified_by)->first();
                                                            $checklist_verified_by = $checklist_verified_Info->first_name;
                                                        } else {
                                                            $checklist_verified_by = 'N/A';
                                                        }

                                                        if( isset($fetch->checklist_verified_at) && $fetch->checklist_verified_at != "") {
                                                            $checklist_verified_at = date('d/m/Y', strtotime($fetch->checklist_verified_at));
                                                        } else {
                                                            $checklist_verified_at = 'N/A';
                                                        }*/
                                                        ?>
                                                        <tr class="drow" id="id_{{$fetch->id}}">
                                                            <!--<td><?php //echo $notuseKey+1;?></td>-->
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
                                                                            <!--<a target="_blank" class="dropdown-item" href="<?php //echo $fetch->myfile; ?>">
                                                                                <i class="fas fa-file-image"></i> <span><?php //echo $fetch->file_name; ?><?php //echo '.'.$fetch->filetype; ?></span>
                                                                            </a>-->
                                                                      
                                                                            <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-notuseddocumentlist')">
                                                                                <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                                                            </a>
                                                                        <?php } else {  //For old file upload
                                                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                                                            $myawsfile = $url.$fetchedData->client_id.'/'.$fetch->doc_type.'/'.$fetch->myfile;
                                                                            ?>
                                                                            <!--<a target="_blank" class="dropdown-item" href="<?php //echo $url.$fetchedData->client_id.'/'.$fetch->doc_type.'/'.$fetch->myfile; ?>">
                                                                                <i class="fas fa-file-image"></i> <span><?php //echo $fetch->file_name; ?><?php //echo '.'.$fetch->filetype; ?></span>
                                                                            </a>-->
                                                                      
                                                                            <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($myawsfile); ?>','preview-container-notuseddocumentlist')">
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
                                                            <!--<td id="docverifiedby_<?php //echo $fetch->id;?>">
                                                                <?php
                                                                //echo $checklist_verified_by. "<br>";
                                                                //echo $checklist_verified_at;
                                                                ?>
                                                            </td>-->
                                                            <td>
                                                                <div class="dropdown d-inline">
                                                                    <button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                                    <div class="dropdown-menu">
                                                                        <?php
                                                                        $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                                                        ?>
                                                                        <?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
                                                                            <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                                                        <?php } else {  //For old file upload ?>
                                                                            <a target="_blank" class="dropdown-item" href="<?php echo $url.$fetchedData->client_id.'/'.$fetch->doc_type.'/'.$fetch->myfile; ?>">Preview</a>
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
                                    <div class="col-5 col-md-5 col-lg-5 file-preview-container preview-container-notuseddocumentlist">
                                        <p style="color:#000;">Click on a file to preview it here.</p>
                                    </div>
                                </div>



								<div class="tab-pane fade" id="appointments" role="tabpanel" aria-labelledby="appointments-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<a href="javascript:;" data-toggle="modal" class="btn btn-primary createaddapointment"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="appointmentlist">
										<div class="row">
											<div class="col-md-5 appointment_grid_list">
												<?php
												$rr=0;
												$appointmentdata = array();
												$appointmentlists = \App\Models\Appointment::where('client_id', $fetchedData->id)->where('related_to', 'client')->orderby('created_at', 'DESC')->get();

                                                /*if($_SERVER["REMOTE_ADDR"] == '49.36.214.255') {
                                                    dd($appointmentlists);
                                                }*/
												$appointmentlistslast = \App\Models\Appointment::where('client_id', $fetchedData->id)->where('related_to', 'client')->orderby('created_at', 'DESC')->first();
												foreach($appointmentlists as $appointmentlist){
													$admin = \App\Models\Admin::select('id', 'first_name','email')->where('id', $appointmentlist->user_id)->first();
													$first_name= $admin->first_name ?? 'N/A';
													$datetime = $appointmentlist->created_at;
													$timeago = Controller::time_elapsed_string($datetime);

													$appointmentdata[$appointmentlist->id] = array(
														'title' => $appointmentlist->title,
														'time' => date('H:i A', strtotime($appointmentlist->time)),
														'date' => date('d D, M Y', strtotime($appointmentlist->date)),
														//'description' => $appointmentlist->description,
                                                        'description' => htmlspecialchars($appointmentlist->description, ENT_QUOTES, 'UTF-8'),
														'createdby' => substr($first_name, 0, 1),
														'createdname' => $first_name,
														'createdemail' => $admin->email ?? 'N/A',
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
															<span>{{substr($first_name, 0, 1)}}</span></span>
															<div class="dropdown d-inline dropdown_ellipsis_icon">
																<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
																<div class="dropdown-menu">
																	<!--<a class="dropdown-item edit_appointment" data-id="{{--$appointmentlist->id--}}" href="javascript:;">Edit</a>-->
																	<a data-id="{{$appointmentlist->id}}" data-href="deleteappointment" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
																</div>
															</div>
														</div>
													</div>
												</div>
												<?php $rr++; } ?>
											</div>
											<div class="col-md-7">
												<div class="editappointment">
												@if($appointmentlistslast)
													<!--<a class="edit_link edit_appointment" href="javascript:;" data-id="<?php //echo @$appointmentlistslast->id; ?>"><i class="fa fa-edit"></i></a>-->
													<?php
													$adminfirst = \App\Models\Admin::select('id', 'first_name','email')->where('id', @$appointmentlistslast->user_id)->first();
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

									</div>
									<div class="note_term_list">
									<?php
									$notelist = \App\Models\Note::where('client_id', $fetchedData->id)->whereNull('assigned_to')->whereNull('task_group')->where('type', 'client')->orderby('pin', 'DESC')->orderBy('created_at', 'DESC')->get();
									//dd($notelist);
                                    foreach($notelist as $list){
										$admin = \App\Models\Admin::select('id', 'first_name','email')->where('id', $list->user_id)->first();//dd($admin);
										$color = \App\Models\Team::select('color')->where('id',$admin->team)->first();

									?>
										<div class="note_col" id="note_id_{{$list->id}}">
                                            <div class="note_content">
											    <h4><a <?php if($color){ ?>style="color: #fff!important;"<?php } ?> class="viewnote" data-id="{{$list->id}}" href="javascript:;">{{ @$list->title == "" ? config('constants.empty') : str_limit(@$list->title, '19', '...') }}</a></h4>
											<?php if($list->pin == 1){
									?><div class="pined_note"><i class="fa fa-thumbtack"></i></i></div><?php } ?>
											</div>
											<div class="extra_content">
											     @if(!empty($list->description))
                                                    @php
                                                        $description = $list->description;
                                                    @endphp

                                                    @if(strpos($description, '<xml>') !== false || strpos($description, '<o:OfficeDocumentSettings>') !== false)
                                                        <p>{!! htmlentities($description) !!}</p>
                                                    @else
                                                        <p>{!! $description !!}</p>
                                                    @endif
                                                @endif

                                                <?php if( isset($list->mobile_number) && $list->mobile_number != ""){ ?>
                                                    <p>{{ @$list->mobile_number }}</p>
                                                <?php }?>

												<div class="left">
													<div class="author">
														<a href="{{URL::to('/admin/users/view/'.$admin->id)}}">{{substr($admin->first_name, 0, 1)}}</a>
													</div>
													<div class="note_modify">
														<small>Last Modified <span>{{date('d/m/Y h:i A', strtotime($list->updated_at))}}</span></small>
														{{$admin->first_name}}	 {{$admin->last_name}}
													</div>
												</div>
												<div class="right">
													<div class="dropdown d-inline dropdown_ellipsis_icon">
														<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">
															<a class="dropdown-item opennoteform" data-id="{{$list->id}}" href="javascript:;">Edit</a>
                                                            @if(Auth::user()->role == 1)
															<a data-id="{{$list->id}}" data-href="deletenote" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
                                                            @endif
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
								<div class="tab-pane fade" id="quotations" role="tabpanel" aria-labelledby="quotations-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<a href="{{URL::to('/admin/quotations/client/create/'.$fetchedData->id)}}" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="table-responsive">
										<table class="table-2 table text_wrap">
											<thead>
												<tr>
													<th>No</th>
													<th>Status</th>
													<th>Products</th>
													<th>Total Fee</th>
													<th>Due Date</th>
													<th>Created On</th>
													<th>Created By</th>
													<th></th>
												</tr>
											</thead>
											<tbody class="tdata">
												<?php
												$qlists = \App\Models\Quotation::where('client_id', $fetchedData->id)->orderby('created_at','DESC')->get();
												foreach($qlists as $qlist){
													$client = \App\Models\Admin::select('id', 'first_name','email')->where('id',$qlist->client_id)->where('role', 7)->first();
									$createdby = \App\Models\Admin::select('id', 'first_name','email')->where('id',$qlist->user_id)->first();
									$countqou = \App\Models\QuotationInfo::where('quotation_id',$qlist->id)->count();
									$getq = \App\Models\QuotationInfo::where('quotation_id',$qlist->id)->get();
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
												<tr id="quid_<?php echo $qlist->id ?>">
													<td>{{@$qlist->id}}</td>
													<td class="statusupdate"><?php if($qlist->status == 0){ ?>
												<span title="draft" class="ui label uppercase">Draft</span>
												<?php }else if($qlist->status == 1){
													?>
													<span title="draft" class="ui label uppercase text-success">Sent</span>
													<?php
												}else if($qlist->status == 2){
													?>
													<span title="draft" class="ui label uppercase text-danger">Declined</span>
													<?php
												}?>
												<?php if($qlist->is_archive == 1){ ?>
													<span>(Archived)</span>
												<?php } ?>
												</td>
													<td>{{$countqou}}</td>
													<td>{{number_format($totfare,2,'.','')}} {{$qlist->currency}}</td>
													<td>{{$qlist->due_date}}</td>
													<td>{{date('Y-m-d', strtotime($qlist->created_at))}}</td>
													<td>{{$createdby->first_name}}</td>
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
														<div class="dropdown-menu">
															<a class="dropdown-item has-icon clientemail" href="javascript:;" data-q-id="{{@$qlist->id}}" data-id="{{@$client->id}}" data-email="{{@$client->email}}" data-name="{{@$client->first_name}} {{@$client->last_name}}">Send Email</a>
															<?php if($qlist->status == 0){?>
															<a class="dropdown-item has-icon ifdeclined" onClick="approveAction({{@$qlist->id}}, 'quotations')" href="javascript:;"><i class="far fa-mail"></i> Approve</a>
															<a class="dropdown-item has-icon ifdeclined" onClick="declineAction({{@$qlist->id}}, 'quotations')" href="javascript:;"><i class="far fa-mail"></i> Decline</a>
															<?php } ?>
															<a class="dropdown-item has-icon" href="javascript:;" onClick="archiveAction({{@$qlist->id}}, 'quotations')">Archive</a>
														</div>
														</div>
													</td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
								<div class="tab-pane fade" id="accounts" role="tabpanel" aria-labelledby="accounts-tab">
									<div class="row">
										<div class="col-md-12 text-right">

                                            <a class="btn btn-primary createclientreceipt" href="javascript:;" role="button"  style="margin-right:5px !important;">Create Client Receipt</a>


											<div class="cus_invice_btn dropdown d-inline">
												<a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle btn btn-outline-primary">Create Invoice <i class="fa fa-angle-down"></i></a>
												<div class="dropdown-menu">
													<a href="javascript:;" class="dropdown-item opencommissioninvoice">
														Commission Invoice
													</a>
													<a href="javascript:;" class="dropdown-item opengeneralinvoice">
														General Invoice
													</a>
												</div>
											</div>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="table-responsive">

                                      	<caption>Client Receipts</caption>
                                        <table border="1" style="margin-bottom:0rem !important;" class="table text_wrap table-striped table-hover table-md vertical_align">
                                            <thead>
                                                <tr>
                                                    <th>Trans. Date</th>
                                                    <th>Entry Date</th>
                                                    <th>Trans. No</th>
                                                    <th>Payment Method</th>
                                                    <th>Description</th>
                                                    <th>Deposit</th>
                                                </tr>
                                            </thead>
                                            <tbody class="productitemList">
                                                <?php
                                                $receipts_lists = DB::table('account_client_receipts')->where('client_id',$fetchedData->id)->where('receipt_type',1)->get();
                                                //dd($receipts_lists);
                                                if(!empty($receipts_lists) && count($receipts_lists)>0 )
                                                {
                                                    $total_deposit_amount = 0.00;
                                                    foreach($receipts_lists as $rec_list=>$rec_val)
                                                    {

                                                ?>
                                                <tr  id="TrRow_<?php echo $rec_val->id;?>">
                                                    <td>
                                                        <?php echo $rec_val->trans_date;?>

                                                        <?php
                                                        if(isset($rec_val->uploaded_doc_id) && $rec_val->uploaded_doc_id >0){
                                                            $client_info = DB::table('admins')->select('id','client_id')->where('id',$rec_val->client_id)->first();

                                                        	$client_doc_list = DB::table('documents')->select('id','myfile','client_id','doc_type','myfile_key')->where('id',$rec_val->uploaded_doc_id)->first();
                                                            if($client_doc_list){
                                                                if( isset($client_doc_list->myfile_key) && $client_doc_list->myfile_key != "") {
                                                                    $awsUrl = $client_doc_list->myfile;
                                                                } else { 
                                                                    $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                                                    $awsUrl = $url.$client_info->client_id.'/'.$client_doc_list->doc_type.'/'.$client_doc_list->myfile; 
                                                                }
                                                                ?>
                                                                <a target="_blank" class="link-primary" href="<?php echo $awsUrl;?>"><i class="fas fa-file-pdf"></i></a>
                                                            <?php
                                                            }
                                                        } ?>
                                                    </td>
                                                    <td><?php echo $rec_val->entry_date;?></td>
                                                    <td><?php echo $rec_val->trans_no;?></td>
                                                    <td><?php echo $rec_val->payment_method;?></td>
                                                    <td><?php echo $rec_val->description;?></td>
                                                    <td>
                                                        <?php echo "$".$rec_val->deposit_amount;?>
                                                        <a target="_blank" class="link-primary" href="{{URL::to('/admin/clients/printpreview')}}/{{$rec_val->id}}"><i class="fa fa-print" aria-hidden="true"></i></a>
                                                       <?php
                                                        if( isset($rec_val->validate_receipt) && $rec_val->validate_receipt != 1){
                                                        ?>
                                              			<a class="link-primary updateclientreceipt" href="javascript:;" data-id="<?php echo $rec_val->id;?>">
                                                          <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                       <?php
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php
                                                    $total_deposit_amount += $rec_val->deposit_amount;
                                                } //end foreach
                                                ?>

                                                <tr class="lastRow">
                                                    <td colspan="5" style="text-align:right;">Totals</td>
                                                    <td class="totDepoAmTillNow"><?php echo "$".$total_deposit_amount;?></td>
                                                </tr>
                                            <?php } else { ?>
                                                <!--<tr class="norecord"><td colspan="5">No Record Found</td></tr>-->
                                                <tr class="lastRow">
                                                    <td colspan="5" style="text-align:right;">Totals</td>
                                                    <td class="totDepoAmTillNow"><?php echo "$0";?></td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                        <br/>

                                        <br/>

										<table class="table invoicetable text_wrap">
											<thead>
												<tr>
													<th>Invoice No.</th>
													<th>Issue Date</th>
													<th>Service</th>
													<th>Invoice Amount</th>
													<th>Discount Given</th>
													<th>Income Shared</th>
													<th>Status</th>
													<th></th>
												</tr>
											</thead>
											<tbody class="tdata invoicedatalist">
												<?php
												$invoicelists = \App\Models\Invoice::where('client_id',$fetchedData->id)->orderby('created_at','DESC')->get();
												foreach($invoicelists as $invoicelist){
													if($invoicelist->type == 3){
														$workflowdaa = \App\Models\Workflow::where('id', $invoicelist->application_id)->first();
													}else{
														$applicationdata = \App\Models\Application::where('id', $invoicelist->application_id)->first();
														$workflowdaa = \App\Models\Workflow::where('id', $invoicelist->application_id)->first();
														$partnerdata = \App\Models\Partner::where('id', @$applicationdata->partner_id)->first();
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
													<td>{{$invoicelist->discount}}</td>
													<td>-</td>
													<td>
													@if($invoicelist->status == 1)
														<span class="ag-label--circular" style="color: #6777ef" >Paid</span></td>
													@else
														<span class="ag-label--circular" style="color: #ed5a5a" >UnPaid</span></td>
													@endif
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">
																<a class="dropdown-item has-icon" href="#">Send Email</a>
																<a target="_blank" class="dropdown-item has-icon" href="{{URL::to('admin/invoice/view/')}}/{{$invoicelist->id}}">View</a>
																<?php if($invoicelist->status == 0){ ?>
																<a target="_blank" class="dropdown-item has-icon" href="{{URL::to('admin/invoice/edit/')}}/{{$invoicelist->id}}">Edit</a>
																<a data-netamount="{{$netamount}}" data-dueamount="{{$totaldue}}" data-invoiceid="{{$invoicelist->id}}" class="dropdown-item has-icon addpaymentmodal" href="javascript:;"> Make Payment</a>
																<?php } ?>
															</div>
														</div>
													</td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
								<div class="tab-pane fade" id="conversations" role="tabpanel" aria-labelledby="conversations-tab">
									<div class="conversation_tabs">
										<ul class="nav nav-pills round_tabs" id="client_tabs" role="tablist">
											<li class="nav-item">
												<a class="nav-link active" data-toggle="tab" id="email-tab" href="#email" role="tab" aria-controls="email" aria-selected="true">Email</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" id="sms-tab" href="#sms" role="tab" aria-controls="sms" aria-selected="false">SMS</a>
											</li>

										</ul>
										<div class="tab-content" id="conversationContent">

											<div class="tab-pane fade show active" id="email" role="tabpanel" aria-labelledby="email-tab">
												<div class="row">
													<div class="col-md-12" style="text-align: right;    margin-bottom: 10px;">
														<a class="btn btn-outline-primary btn-sm uploadmail"  href="javascript:;" >Upload Mail</a>
													</div>
												</div>
												<ul class="nav nav-pills round_tabs" id="client_mail_tabs" role="tablist">
													<li class="nav-item">
														<a class="nav-link active" data-toggle="tab" id="sent-tab" href="#sent" role="tab" aria-controls="sent" aria-selected="false">Sent</a>
													</li>
													<li class="nav-item">
														<a class="nav-link " data-toggle="tab" id="inbox-tab" href="#inbox" role="tab" aria-controls="inbox" aria-selected="true">Inbox</a>
													</li>
												</ul>
										<div class="tab-content" id="conversationContent">
										<div class="tab-pane fade" id="inbox" role="tabpanel" aria-labelledby="inbox-tab" style="/*max-height: 1443px;*/overflow-y: auto;overflow-x: hidden;">

												<?php

											$mailreports = \App\Models\MailReport::where('client_id',$fetchedData->id)->where('type','client')->where('mail_type',1)->orderby('created_at', 'DESC')->get();

											foreach($mailreports as $mailreport){

											?>
												<div class="conversation_list" style="max-height: 200px;overflow-y: auto;overflow-x: hidden;margin-bottom: 10px;border-bottom: 1px solid rgba(34, 36, 38, .15);">
													<div class="conversa_item">
														<div class="ds_flex">
															<div class="title">
																<span>{{@$mailreport->subject}}</span>
															</div>
															<div class="conver_action">
																<div class="date">
																	<span>{{date('h:i A', strtotime(@$mailreport->created_at))}}</span>
																</div>

															</div>
														</div>
														<div class="email_info">
															<div class="avatar_img">
																<span>{{substr(@$mailreport->from_mail, 0, 1)}}</span>
															</div>
															<div class="email_content">
																<span class="email_label">Sent by:</span>
																<span class="email_sentby"><strong>{{@$mailreport->from_mail}}</strong> </span>
																<span class="label success">Delivered</span>
																<span class="span_desc">
																	<span class="email_label">Sent To</span>
																	<span class="email_sentby"><i class="fa fa-angle-left"></i>{{@$mailreport->to_mail}}<i class="fa fa-angle-right"></i></span>
																</span>
															</div>
														</div>
														<div class="divider"></div>
														<div class="email_desc">
														 @if(@$mailreport->attachments != '')
														 <?php
														/*  $decodeatta = json_decode($mailreport->attachments);
														 if(!empty($decodeatta)){
														 ?>
														    <div class="attachments">
														        <ul style="list-style: none;">
										@foreach($decodeatta as $attaa)
										    <li style="display:inline-block;padding: 0px 11px;
											border-radius: 4px;
											box-shadow: 0 3px 8px 0 rgb(0 0 0 / 8%), 0 1px 2px 0 rgb(0 0 0 / 10%);"><a href="<?php echo URL::to('/public/checklists/'.$attaa->file_url); ?>" target="_blank">{{$attaa->file_name}}</a></li>
																				@endforeach
										</ul>
														    </div>
														    	<?php } */ ?>
											@endif
														{!!$mailreport->message!!}
														</div>
														<div class="divider"></div>
														<?php
														/* if($mailreport->reciept_id != ''){
															if(\App\Models\InvoicePayment::where('id',$mailreport->reciept_id)->exists()){
																$invpayment = \App\Models\InvoicePayment::where('id',$mailreport->reciept_id)->first();
														?>
														<div class="email_attachment">
															<span class="attach_label"><i class="fa fa-link"></i> Attachments:</span>
															<div class="attach_file_list">
																<div class="attach_col">
																	<a href="{{URL::to('admin/payment/view/')}}/{{base64_encode(convert_uuencode(@$invpayment->id))}}">receipt_{{$invpayment->id}}.pdf</a>
																</div>
															</div>
														</div>
														<?php } ?>
														<?php } */ ?>
													</div>
												</div>
											<?php } ?>
										</div>
										<div class="tab-pane fade  show active" id="sent" role="tabpanel" aria-labelledby="sent-tab" style="/*max-height: 1443px;*/overflow-y: auto;overflow-x: hidden;">
											<?php

											$mailreports = \App\Models\MailReport::whereRaw('FIND_IN_SET("'.$fetchedData->id.'", to_mail)')->where('type','client')->where('mail_type',0)->orderby('created_at', 'DESC')->get();

											foreach($mailreports as $mailreport){
												$admin = \App\Models\Admin::select('id', 'first_name','email')->where('id', $mailreport->user_id)->first();

												$client = \App\Models\Admin::select('id', 'first_name','email')->Where('id', $fetchedData->id)->first();
												$subject = str_replace('{Client First Name}',$client->first_name, $mailreport->subject);
												$message = $mailreport->message;
												$message = str_replace('{Client First Name}',$client->first_name, $message);
												$message = str_replace('{Client Assignee Name}',$client->first_name, $message);
												$message = str_replace('{Company Name}',Auth::user()->company_name, $message);
											?>
												<div class="conversation_list" style="max-height: 200px;overflow-y: auto;overflow-x: hidden;margin-bottom: 10px;border-bottom: 1px solid rgba(34, 36, 38, .15);">
													<div class="conversa_item">
														<div class="ds_flex">
															<div class="title">
																<span>{{$subject}}</span>
															</div>
															<div class="conver_action">
																<div class="date">
																	<span>{{date('h:i A', strtotime($mailreport->created_at))}}</span>
																</div>
																<div class="conver_link">
																	<a datamailid="{{$mailreport->id}}" datasubject="{{$subject}}" class="create_note" datatype="mailnote" href="javascript:;" ><i class="fas fa-file-alt"></i></a>
																	<a datamailid="{{$mailreport->id}}" datasubject="{{$subject}}" href="javascript:;" class="opentaskmodal"><i class="fas fa-shopping-bag"></i></a>
																</div>
															</div>
														</div>
														<div class="email_info">
															<div class="avatar_img">
																<span>{{substr($admin->first_name, 0, 1)}}</span>
															</div>
															<div class="email_content">
																<span class="email_label">Sent by:</span>
																<span class="email_sentby"><strong>{{@$admin->first_name}}</strong> [{{$mailreport->from_mail}}]</span>
																<span class="label success">Delivered</span>
																<span class="span_desc">
																	<span class="email_label">Sent To</span>
																	<span class="email_sentby"><i class="fa fa-angle-left"></i>{{$client->email}}<i class="fa fa-angle-right"></i></span>
																</span>
															</div>
														</div>
														<div class="divider"></div>
														<div class="email_desc">
														 @if($mailreport->attachments != '')
														 <?php
														 $decodeatta = json_decode($mailreport->attachments);
														 if(!empty($decodeatta)){
														 ?>
														    <div class="attachments">
														        <ul style="list-style: none;">
											@foreach($decodeatta as $attaa)
												<li style="display:inline-block;padding: 0px 11px;
												border-radius: 4px;
												box-shadow: 0 3px 8px 0 rgb(0 0 0 / 8%), 0 1px 2px 0 rgb(0 0 0 / 10%);"><a href="<?php echo URL::to('/public/checklists/'.$attaa->file_url); ?>" target="_blank">{{$attaa->file_name}}</a></li>
											@endforeach
										</ul>
														    </div>
														    	<?php } ?>
											@endif
														{!!$message!!}
														</div>
														<div class="divider"></div>
														<?php
														if($mailreport->reciept_id != ''){
															if(\App\Models\InvoicePayment::where('id',$mailreport->reciept_id)->exists()){
																$invpayment = \App\Models\InvoicePayment::where('id',$mailreport->reciept_id)->first();
														?>
														<div class="email_attachment">
															<span class="attach_label"><i class="fa fa-link"></i> Attachments:</span>
															<div class="attach_file_list">
																<div class="attach_col">
																	<a href="{{URL::to('admin/payment/view/')}}/{{base64_encode(convert_uuencode(@$invpayment->id))}}">receipt_{{$invpayment->id}}.pdf</a>
																</div>
															</div>
														</div>
														<?php } ?>
														<?php } ?>
													</div>
												</div>
											<?php } ?>
											</div>
											</div>
											</div>
											<div class="tab-pane fade" id="sms" role="tabpanel" aria-labelledby="sms-tab">
												<span>sms</span>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">
									<div class="card-header-action text-right" style="padding-bottom:15px;">
										<a href="javascript:;"  class="btn btn-primary opencreate_task"><i class="fa fa-plus"></i> Add</a>

									</div>
									<div class="table-responsive">
										<table id="my-datatable" class="table-2 table text_wrap">
											<!--<thead>
												<tr>
													<th></th>
													<th></th>
													<th></th>
													<th></th>
													<th></th>
													<th></th>
												</tr>
											</thead>-->
											<tbody class="taskdata ">
											<?php
											foreach(\App\Models\Task::where('client_id', $fetchedData->id)->orderby('created_at','Desc')->get() as $alist){
												$admin = \App\Models\Admin::where('id', $alist->user_id)->first();
												?>
												<tr class="opentaskview" style="cursor:pointer;" id="{{$alist->id}}">
													<td><?php if($alist->status == 1 || $alist->status == 2){ echo "<span class='check'><i class='fa fa-check'></i></span>"; } else{ echo "<span class='round'></span>"; } ?></td>
													<td><b>{{$alist->category}}</b>: {{$alist->title}}</td>
													<td><span class="author-avtar" style="font-size: .8rem;height: 24px;line-height: 24px;width: 24px;min-width: 24px;background: rgb(3, 169, 244);"><?php echo substr($admin->first_name, 0, 1); ?></span></td>
													<td>{{$alist->priority}}</td>
													<td><i class="fa fa-clock"></i> {{$alist->due_date}} {{$alist->due_time}}</td>
													<td><?php
													if($alist->status == 1){
														echo '<span style="color: rgb(113, 204, 83); width: 84px;">Completed</span>';
													}else if($alist->status == 2){
														echo '<span style="color: rgb(255, 173, 0); width: 84px;">In Progress</span>';
													}else if($alist->status == 3){
														echo '<span style="color: rgb(156, 156, 156); width: 84px;">On Review</span>';
													}else{
														echo '<span style="color: rgb(255, 173, 0); width: 84px;">Todo</span>';
													}
													?></td>

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
								<div class="tab-pane fade" id="education" role="tabpanel" aria-labelledby="education-tab">
									<div class="card-header-action" style="padding-bottom:15px;">
										<div class="float-left">
											<h5>Education Background</h5>
										</div>
										<div class="float-right">
											<a href="javascript:;" data-toggle="modal" data-target=".create_education" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="divider"></div>

									<div class="education_list">
									<?php
									$totalecount = \App\Models\Education::where('client_id', $fetchedData->id)->orderby('created_at','DESC')->count();
									if($totalecount == 0){
									?>
									<div class="edu_note">
										<span>* Click add button to fill education background </span>
									</div>
									<?php } ?>
										<?php
										$edulists = \App\Models\Education::where('client_id', $fetchedData->id)->orderby('created_at','DESC')->get();

										foreach($edulists as $edulist){
											$subjectdetail = \App\Models\Subject::where('id',$edulist->subject)->first();
											$subjectareadetail = \App\Models\SubjectArea::where('id',$edulist->subject_area)->first();
											?>
											<div class="education_item" id="edu_id_<?php echo $edulist->id; ?>">
										<div class="row">
											<div class="col-md-5">
												<div class="title_desc">
													<h6>{{@$edulist->degree_title}}</h6>
													<p>{{@$edulist->institution}}</p>
												</div>
											</div>
											<div class="col-md-7">
												<div class="education_info">
													<div class="edu_date"><?php echo date('M Y',strtotime(@$edulist->course_start)); ?><span>-</span><?php echo date('M Y',strtotime(@$edulist->course_end)); ?></div>
													<div class="edu_score"><span>Score: {{@$edulist->score}} {{@$edulist->ac_score}} </span></div>
													<div class="edu_study_area">
														<span>{{@$edulist->degree_level}}</span>
														<span>{{@$subjectareadetail->name}}</span>
														<span>{{@$subjectdetail->name}}</span>
													</div>
												</div>
												<div class="education_action">
													<a class="editeducation" data-id="<?php echo @$edulist->id; ?>" href="javascript:;"><i class="fa fa-edit"></i></a>
													<a href="javascript:;" data-id="<?php echo @$edulist->id; ?>" class="deleteeducation"><i class="fa fa-trash"></i></a>
												</div>
											</div>
										</div>
									</div>
											<?php
										}
										?>
									</div>
									<div class="divider"></div>
									<div class="card-header-action" style="padding-top:15px;padding-bottom:10px;">
										<div class="float-left">
											<h5>English Test Scores</h5>
										</div>
										<div class="float-right">
											<a href="javascript:;" data-toggle="modal" data-target=".edit_english_test" class="btn btn-primary"><i class="fa fa-plus"></i> Edit</a>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="divider"></div>
									<div class="edu_test_score edu_english_score">
										<div class="edu_test_row" style="text-align:center;">
											<div class="edu_test_col">&nbsp;</div>
											<div class="edu_test_col"><span>Listening</span></div>
											<div class="edu_test_col"><span>Reading</span></div>
											<div class="edu_test_col"><span>Writing</span></div>
											<div class="edu_test_col"><span>Speaking</span></div>
											<div class="edu_test_col"><span>Overall Scores</span></div>
											<div class="edu_test_col"><span>Date</span></div>
										</div>
										<?php
										$testscores = \App\Models\TestScore::where('client_id', $fetchedData->id)->where('type', 'client')->first();
										?>
										<div class="edu_test_row flex_row">
											<div class="edu_test_col"><span>TOEFL</span></div>
											<div class="edu_test_col"><strong class="tofl_lis"><?php if(@$testscores->toefl_Listening != ''){ echo @$testscores->toefl_Listening; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col"><strong class="tofl_reading"><?php if(@$testscores->toefl_Reading != ''){ echo @$testscores->toefl_Reading; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col"><strong class="tofl_writing"><?php if(isset($testscores->toefl_Writing) && $testscores->toefl_Writing != ''){ echo $testscores->toefl_Writing; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col"><strong class="tofl_speaking"><?php if(@$testscores->toefl_Speaking != ''){ echo @$testscores->toefl_Speaking; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col overal_block"><strong class="tofl_score"><?php if(@$testscores->score_1 != ''){ echo @$testscores->score_1; }else{ echo '0'; } ?></strong></div>
											<div class="edu_test_col"><strong class="toefl_date"><?php if(@$testscores->toefl_Date != ''){ echo @$testscores->toefl_Date; }else{ echo '-'; } ?></strong></div>
										</div>
										<div class="edu_test_row flex_row">
											<div class="edu_test_col"><span>IELTS</span></div>
											<div class="edu_test_col"><strong class="ilets_Listening"><?php if(@$testscores->ilets_Listening != ''){ echo @$testscores->ilets_Listening; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col"><strong class="ilets_Reading"><?php if(@$testscores->ilets_Reading != ''){ echo @$testscores->ilets_Reading; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col"><strong class="ilets_Writing"><?php if(@$testscores->ilets_Writing != ''){ echo @$testscores->ilets_Writing; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col"><strong class="ilets_speaking"><?php if(@$testscores->ilets_Speaking != ''){ echo $testscores->ilets_Speaking; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col overal_block"><strong class="ilets_score"><?php if(@$testscores->score_2 != ''){ echo @$testscores->score_2; }else{ echo '0'; } ?></strong></div>
											<div class="edu_test_col"><strong class="ilets_date"><?php if(@$testscores->ilets_Date != ''){ echo $testscores->ilets_Date; }else{ echo '-'; } ?></strong></div>
										</div>
										<div class="edu_test_row flex_row">
											<div class="edu_test_col"><span>PTE</span></div>
											<div class="edu_test_col"><strong class="pte_Listening"><?php if(@$testscores->pte_Listening != ''){ echo @$testscores->pte_Listening; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col"><strong class="pte_Reading"><?php if(@$testscores->pte_Reading != ''){ echo @$testscores->pte_Reading; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col"><strong class="pte_Writing"><?php if(@$testscores->pte_Writing != ''){ echo @$testscores->pte_Writing; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col"><strong class="pte_Speaking"><?php if(@$testscores->pte_Speaking != ''){ echo @$testscores->pte_Speaking; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col overal_block"><strong class="pte_score"><?php if(@$testscores->score_3 != ''){ echo @$testscores->score_3; }else{ echo '0'; } ?></strong></div>
											<div class="edu_test_col"><strong class="pte_date"><?php if(@$testscores->pte_Date != ''){ echo @$testscores->pte_Date; }else{ echo '-'; } ?></strong></div>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="divider"></div>
									<div class="card-header-action" style="padding-top:15px;padding-bottom:10px;">
										<div class="float-left">
											<h5>Other Test Scores</h5>
										</div>
										<div class="float-right">
											<a href="javascript:;" data-toggle="modal" data-target=".edit_other_test" class="btn btn-primary"><i class="fa fa-plus"></i> Edit</a>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="divider"></div>
									<div class="edu_test_score edu_othertest_score">
										<div class="edu_test_row" style="text-align:center;">
											<div class="edu_test_col"></div>
											<div class="edu_test_col"><span>SAT I</span></div>
											<div class="edu_test_col"><span>SAT II</span></div>
											<div class="edu_test_col"><span>GRE</span></div>
											<div class="edu_test_col"><span>GMAT</span></div>
										</div>
										<div class="edu_test_row flex_row">
											<div class="edu_test_col">Overall Scores</div>
											<div class="edu_test_col overal_block"><strong class="sat_i"><?php if(@$testscores->sat_i != ''){ echo @$testscores->sat_i; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col overal_block"><strong class="sat_ii"><?php if(@$testscores->sat_ii != ''){ echo @$testscores->sat_ii; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col overal_block"><strong class="gre"><?php if(@$testscores->gre != ''){ echo @$testscores->gre; }else{ echo '-'; } ?></strong></div>
											<div class="edu_test_col overal_block"><strong class="gmat"><?php if(@$testscores->gmat != ''){ echo @$testscores->gmat; }else{ echo '-'; } ?></strong></div>
										</div>
									</div>
								</div>
								<!--<div class="tab-pane fade" id="other_info" role="tabpanel" aria-labelledby="other_info-tab">
									<span>other_info</span>
								</div>-->
								<div class="tab-pane fade" id="checkinlogs" role="tabpanel" aria-labelledby="checkinlogs-tab">
									<div class="table-responsive">
										<table class="table text_wrap">
											<thead>
												<tr>
													<th>ID</th>
													<th>Date</th>
													<th>Start</th>
													<th>End</th>
													<th>Session Time</th>
													<th>Visit Purpose</th>
													<th>Assignee</th>
													<th>Status</th>

												</tr>
											</thead>
											<tbody class="tdata checindata">
											<?php
											$checkins = \App\Models\CheckinLog::where('client_id', $fetchedData->id)->orderby('created_at','DESC')->get();
											foreach($checkins as $checkin){
											?>
												<tr did="{{@$checkin->id}}" id="id_{{$checkin->id}}">
													<td><a id="{{@$checkin->id}}" href="javascript:;" class="opencheckindetail">#{{$checkin->id}}</a></td>
													<td>{{date('l',strtotime($checkin->date))}}<br>{{$checkin->date}}</td>
													<td><?php if($checkin->sesion_start != ''){ echo date('h:i A',strtotime($checkin->sesion_start)); }else{ echo '-'; } ?></td>
													<td><?php if($checkin->sesion_end != ''){ echo date('h:i A',strtotime($checkin->sesion_end)); }else{ echo '-'; } ?></td>
													<td>{{$checkin->attend_time}}</td>
													<td>{{$checkin->visit_purpose}}</td>
													<td>
													<?php
													$ad = \App\Models\Admin::select('id', 'first_name','email')->where('id', $checkin->user_id)->first();
													echo @$ad->first_name.' <br>'.@$ad->email;
													?>
													</td>
													<td>
													<?php
													if($checkin->status == 1){
														echo '<span class="badge badge-success">Completed</span>';
													}else{
														echo '<span class="badge btn-warning">Waiting</span>';
													}
													?>
													</td>

												</tr>
											<?php } ?>
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
								<div class="tab-pane fade" id="prevvisa" role="tabpanel" aria-labelledby="prevvisa-tab">
									<div class="agreement_info">
										<h4>Previous Visa Information</h4>
										<form method="post"  action="{{URL::to('/admin/saveprevvisa')}}" autocomplete="off" name="saveprevvisa" id="saveprevvisa" enctype="multipart/form-data">
										@csrf
										<?php
										$prev_visa = array();
										if($fetchedData->prev_visa != ''){
								        	$prev_visa = json_decode($fetchedData->prev_visa);
										}
										?>
										<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
										@if(empty($prev_visa))
									<div class="multiplevisa">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label for="contract_expiry">Visa</label>
													{{ Form::text('prev_visa[name][]', '', array('class' => 'form-control ', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Visa' )) }}
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="">Start Date</label>

													<input type="date" name="prev_visa[start_date][]" data-valid="required" class="form-control visadatesse" autocomplete="off" value="" placeholder="">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="">End Date</label>

													<input type="date" name="prev_visa[end_date][]" data-valid="required" class="form-control visadatesse" autocomplete="off" value="" placeholder="">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="place_apply">Place of Apply</label>
													{{ Form::text('prev_visa[place][]', '', array('class' => 'form-control ', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Place of Apply' )) }}
													</div>
												</div>
												<div class="col-md-4 lastfiledcol">
													<div class="form-group">
														<label for="person_applies">Person who applies</label>
													{{ Form::text('prev_visa[person][]', '', array('class' => 'form-control ', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Person who applies' )) }}
													</div>
												</div>
											</div>
											</div>

											@else
											<?php $visai = 0; ?>
												@foreach($prev_visa as $prev)
												<div class="multiplevisa">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label for="contract_expiry">Visa</label>
													{{ Form::text('prev_visa[name][]', @$prev->name, array('class' => 'form-control ', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Visa' )) }}
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="contract_expiry">Start Date</label>

													<input type="date" name="prev_visa[start_date][]" data-valid="required" class="form-control visadatesse" autocomplete="off" value="{{@$prev->start_date}}" placeholder="">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="contract_expiry">End Date</label>

													<input type="date" name="prev_visa[end_date][]" data-valid="required" class="form-control visadatesse" autocomplete="off" value="{{@$prev->end_date}}" placeholder="">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="place_apply">Place of Apply</label>
													{{ Form::text('prev_visa[place][]', @$prev->place, array('class' => 'form-control ', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Place of Apply' )) }}
													</div>
												</div>
												<div class="col-md-4 lastfiledcol">
													<div class="form-group">
														<label for="person_applies">Person who applies</label>
													{{ Form::text('prev_visa[person][]', @$prev->person, array('class' => 'form-control ', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Person who applies' )) }}
													</div>
												</div>
												@if($visai != 0)
												<div class="col-md-4"><a href="javascript:;" class="removenewprevvisa btn btn-danger btn-sm">Remove</a></div>
												@endif
											</div>
											</div>
											<?php $visai++; ?>
											@endforeach
											@endif
											<div class="row">
												<div class="col-12 col-md-12 col-lg-12">
													<a href="javascript:;" class="addnewprevvisa btn btn-info btn-sm">Add New</a>

												</div>
												<div class="col-12 col-md-12 col-lg-12">
													<div class="form-group float-right">
														<button onclick="customValidate('saveprevvisa')" type="button" class="btn btn-primary">Save Changes</button>
													</div>
												</div>
											</div>
										</form>
									</div>

									<div class="clearfix"></div>
								</div>

								<div class="tab-pane fade" id="clientinfoform" role="tabpanel" aria-labelledby="clientinfoform-tab">
									<div class="agreement_info">
										<ul class="nav nav-pills" id="client_tabs" role="tablist">
											<li class="nav-item">
												<a class="nav-link active" data-toggle="tab" id="formprimary-tab" href="#formprimary" role="tab" aria-controls="formprimary" aria-selected="false">Primary</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" id="formsec-tab" href="#formsec" role="tab" aria-controls="formsec" aria-selected="false">Secondary</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" id="formchild-tab" href="#formchild" role="tab" aria-controls="formchild" aria-selected="false">Child</a>
											</li>
										</ul>
										<div class="tab-content " id="clientContent" style="padding-top:15px;">
											<div class="tab-pane fade show active" id="formprimary" role="tabpanel" aria-labelledby="formprimary-tab">
											<?php $primarydetail = \App\Models\OnlineForm::where('type', 'primary')->where('client_id', $fetchedData->id)->first(); ?>
											<form method="post"  action="{{URL::to('/admin/saveonlineprimaryform')}}" autocomplete="off" name="saveonlineprimaryform" id="saveonlineprimaryform" enctype="multipart/form-data">
											@csrf
											<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
											<input type="hidden" name="type" value="primary">
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="contract_expiry">Name</label>
													{{ Form::text('info_name', @$primarydetail->info_name, array('class' => 'form-control ', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Name' )) }}
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group">
													<label>Main Language</label>
													<?php
													$main_lang = array();
													if(isset($primarydetail->main_lang) && @$primarydetail->main_lang != ''){
														$main_lang = explode(',', $primarydetail->main_lang);
													}
													?>
													<ul style="padding-left:0px;"><li style="display: inline-block;padding-right: 10px;"><label><input <?php if(in_array('Punjabi', $main_lang)){ echo 'checked'; }?> type="checkbox" value="Punjabi" name="main_lang[]"> Punjabi</label></li>
													<li style="display: inline-block;padding-right: 10px;"><label><input value="Hindi" <?php if(in_array('Hindi', $main_lang)){ echo 'checked'; }?>  type="checkbox" name="main_lang[]"> Hindi</label></li>
														<li style="display: inline-block;padding-right: 10px;"><label><input <?php if(in_array('Other', $main_lang)){ echo 'checked'; }?>  value="Other" type="checkbox" name="main_lang[]"> Other</label></li>
													</ul>
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group">
														<label for="marital_status">Marital Status</label>
														<select class="form-control" name="marital_status">
															<option <?php if(@$primarydetail->marital_status == 'Married'){ echo 'selected'; }?> value="Married">Married</option>
															<option <?php if(@$primarydetail->marital_status == 'Single'){ echo 'selected'; }?> value="Single">Single</option>
															<option <?php if(@$primarydetail->marital_status == 'Other'){ echo 'selected'; }?> value="Other">Other</option>
														</select>
													</div>
												</div>
												<div class="col-md-12">

													<div class="form-group">
														<label for="mobile">Mobile</label>
													{{ Form::text('mobile', @$primarydetail->mobile, array('class' => 'form-control ', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Mobile' )) }}
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group">
														<label for="curr_address">Current Address</label>
													{{ Form::text('curr_address', @$primarydetail->curr_address, array('class' => 'form-control ', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Current Address' )) }}
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group">
														<label for="email">Email</label>
													{{ Form::text('email', @$primarydetail->email, array('class' => 'form-control ', 'data-valid'=>'required email', 'autocomplete'=>'off','placeholder'=>'Email' )) }}
													</div>
												</div>
												<div class="col-md-12">
													<h5>Parents Details</h5>
													<div class="row">
														<div class="col-md-6" style="border-right:1px solid #98a6ad;">
															<div class="form-group">
																<label for="parent_name">Name</label>
																{{ Form::text('parent_name', @$primarydetail->parent_name, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
															</div>
															<?php
															$parent_dob = '';
																if(@$primarydetail->parent_dob != ''){
																	$parent_dob = date('d/m/Y', strtotime($primarydetail->parent_dob));
																}
															?>
															<div class="form-group">
																<label for="parent_dob">DOB</label>
																{{ Form::text('parent_dob', $parent_dob, array('class' => 'form-control dobdatepickers', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
															</div>
															<div class="form-group">
																<label for="parent_occ">Occupation</label>
																{{ Form::text('parent_occ', @$primarydetail->parent_occ, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
															</div>
															<div class="form-group">
																<label for="parent_country">Country of Residence</label>
																{{ Form::text('parent_country', @$primarydetail->parent_country, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label for="parent_name_2">Name</label>
																{{ Form::text('parent_name_2', @$primarydetail->parent_name_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
															</div>
															<?php
															$parent_dob_2 = '';
																if(@$primarydetail->parent_dob_2 != ''){
																	$parent_dob_2 = date('d/m/Y', strtotime($primarydetail->parent_dob_2));
																}
															?>
															<div class="form-group">
																<label for="parent_dob_2">DOB</label>
																{{ Form::text('parent_dob_2', $parent_dob_2, array('class' => 'form-control dobdatepickers', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
															</div>
															<div class="form-group">
																<label for="parent_occ_2">Occupation</label>
																{{ Form::text('parent_occ_2', @$primarydetail->parent_occ_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
															</div>
															<div class="form-group">
																<label for="parent_country_2">Country of Residence</label>
																{{ Form::text('parent_country_2', @$primarydetail->parent_country_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
															</div>
														</div>
													</div>

												</div>
												<div class="col-md-12">
											<h5>All Siblings Details (in Australia and Overseas)</h5>
											<div class="row">
												<div class="col-md-6" style="border-right:1px solid #98a6ad;">
													<div class="form-group">
														<label for="sibling_name">Name</label>
														{{ Form::text('sibling_name', @$primarydetail->sibling_name, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<?php
													$sibling_dob = '';
														if(@$primarydetail->sibling_dob != ''){
															$sibling_dob = date('d/m/Y', strtotime($primarydetail->sibling_dob));
														}
													?>
													<div class="form-group">
														<label for="sibling_dob">DOB</label>
														{{ Form::text('sibling_dob', $sibling_dob, array('class' => 'form-control dobdatepickers', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_occ">Occupation</label>
														{{ Form::text('sibling_occ', @$primarydetail->sibling_occ, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_gender">Gender</label>
														{{ Form::text('sibling_gender', @$primarydetail->sibling_gender, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_country">Country of Residence</label>
														{{ Form::text('sibling_country', @$primarydetail->sibling_country, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_marital">Marital Status</label>
														{{ Form::text('sibling_marital', @$primarydetail->sibling_marital, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="sibling_name_2">Name</label>
														{{ Form::text('sibling_name_2', @$primarydetail->sibling_name_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<?php
													$sibling_dob_2 = '';
														if(@$primarydetail->sibling_dob_2 != ''){
															$sibling_dob_2 = date('d/m/Y', strtotime($primarydetail->sibling_dob_2));
														}
													?>
													<div class="form-group">
														<label for="sibling_dob_2">DOB</label>
														{{ Form::text('sibling_dob_2', $sibling_dob_2, array('class' => 'form-control dobdatepickers', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_occ_2">Occupation</label>
														{{ Form::text('sibling_occ_2', @$primarydetail->sibling_occ_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_gender_2">Gender</label>
														{{ Form::text('sibling_gender_2', @$primarydetail->sibling_gender_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_country_2">Country of Residence</label>
														{{ Form::text('sibling_country_2', @$primarydetail->sibling_country_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_marital_2">Marital Status</label>
														{{ Form::text('sibling_marital_2', @$primarydetail->sibling_marital_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
												</div>
											</div>

										</div>
										<div class="col-md-12">
											<h5>Do you hold or held any visa for Australia or any other country</h5>
											<div class="form-group">
												<label for="held_visa">If Yes Mention Visa Subclass, Country Name, Year (all current and previous)</label>
												<label for="held_visa">If no Mention "No"</label>
												<textarea class="form-control" name="held_visa">{{@$primarydetail->held_visa}}</textarea>
											</div>
										</div>
										<div class="col-md-12">
											<h5>Do you have any visa refused (Australia or any other country)</h5>
											<div class="form-group">
												<label for="visa_refused">If Yes Mention Visa Subclass, Country Name, Year (all visa refusals)</label>
												<label for="visa_refused">If no Mention "No"</label>
												<textarea class="form-control" name="visa_refused">{{@$primarydetail->visa_refused}}</textarea>
											</div>
										</div>
										<div class="col-md-12">
											<h5>Have you travelled to any other country including Australia in last 10 years</h5>
											<div class="form-group">
												<label for="traveled">If Yes Mention Visa Subclass, Country Name, Departure Date, Arrival Date, type of visa</label>
												<textarea class="form-control" name="traveled">{{@$primarydetail->traveled}}</textarea>
											</div>
										</div>
										<div class="col-12 col-md-12 col-lg-12">
											<div class="form-group float-right">
												<button onclick="customValidate('saveonlineprimaryform')" type="button" class="btn btn-primary">Save</button>
											</div>
										</div>
									</div>
									</form>
																			</div>
										<div class="tab-pane fade" id="formsec" role="tabpanel" aria-labelledby="formsec-tab">
									<?php $secdetail = \App\Models\OnlineForm::where('type', 'secondary')->where('client_id', $fetchedData->id)->first(); ?>
									<form method="post"  action="{{URL::to('/admin/saveonlinesecform')}}" autocomplete="off" name="saveonlinesecform" id="saveonlinesecform" enctype="multipart/form-data">
									@csrf
									<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
									<input type="hidden" name="type" value="secondary">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label for="contract_expiry">Name</label>
											{{ Form::text('info_name', @$secdetail->info_name, array('class' => 'form-control ', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Name' )) }}
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
											<label>Main Language</label>
											<?php
											$main_lang = array();
											if(isset($secdetail->main_lang) && @$secdetail->main_lang != ''){
												$main_lang = explode(',', $secdetail->main_lang);
											}
											?>
											<ul style="padding-left:0px;"><li style="display: inline-block;padding-right: 10px;"><label><input <?php if(in_array('Punjabi', $main_lang)){ echo 'checked'; }?> type="checkbox" value="Punjabi" name="main_lang[]"> Punjabi</label></li>
											<li style="display: inline-block;padding-right: 10px;"><label><input value="Hindi" <?php if(in_array('Hindi', $main_lang)){ echo 'checked'; }?>  type="checkbox" name="main_lang[]"> Hindi</label></li>
												<li style="display: inline-block;padding-right: 10px;"><label><input <?php if(in_array('Other', $main_lang)){ echo 'checked'; }?>  value="Other" type="checkbox" name="main_lang[]"> Other</label></li>
											</ul>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label for="marital_status">Marital Status</label>
												<select class="form-control" name="marital_status">
													<option <?php if(@$secdetail->marital_status == 'Married'){ echo 'selected'; }?> value="Married">Married</option>
													<option <?php if(@$secdetail->marital_status == 'Single'){ echo 'selected'; }?> value="Single">Single</option>
													<option <?php if(@$secdetail->marital_status == 'Other'){ echo 'selected'; }?> value="Other">Other</option>
												</select>
											</div>
										</div>
										<div class="col-md-12">

											<div class="form-group">
												<label for="mobile">Mobile</label>
											{{ Form::text('mobile', @$secdetail->mobile, array('class' => 'form-control ', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Mobile' )) }}
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label for="curr_address">Current Address</label>
											{{ Form::text('curr_address', @$secdetail->curr_address, array('class' => 'form-control ', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Current Address' )) }}
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label for="email">Email</label>
											{{ Form::text('email', @$secdetail->email, array('class' => 'form-control ', 'data-valid'=>'required email', 'autocomplete'=>'off','placeholder'=>'Email' )) }}
											</div>
										</div>
										<div class="col-md-12">
											<h5>Parents Details</h5>
											<div class="row">
												<div class="col-md-6" style="border-right:1px solid #98a6ad;">
													<div class="form-group">
														<label for="parent_name">Name</label>
														{{ Form::text('parent_name', @$secdetail->parent_name, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<?php
													$parent_dob = '';
														if(@$secdetail->parent_dob != ''){
															$parent_dob = date('d/m/Y', strtotime($secdetail->parent_dob));
														}
													?>
													<div class="form-group">
														<label for="parent_dob">DOB</label>
														{{ Form::text('parent_dob', $parent_dob, array('class' => 'form-control dobdatepickers', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="parent_occ">Occupation</label>
														{{ Form::text('parent_occ', @$secdetail->parent_occ, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="parent_country">Country of Residence</label>
														{{ Form::text('parent_country', @$secdetail->parent_country, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="parent_name_2">Name</label>
														{{ Form::text('parent_name_2', @$secdetail->parent_name_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<?php
													$parent_dob_2 = '';
														if(@$secdetail->parent_dob_2 != ''){
															$parent_dob_2 = date('d/m/Y', strtotime($secdetail->parent_dob_2));
														}
													?>
													<div class="form-group">
														<label for="parent_dob_2">DOB</label>
														{{ Form::text('parent_dob_2', $parent_dob_2, array('class' => 'form-control dobdatepickers', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="parent_occ_2">Occupation</label>
														{{ Form::text('parent_occ_2', @$secdetail->parent_occ_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="parent_country_2">Country of Residence</label>
														{{ Form::text('parent_country_2', @$secdetail->parent_country_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
												</div>
											</div>

										</div>
										<div class="col-md-12">
											<h5>All Siblings Details (in Australia and Overseas)</h5>
											<div class="row">
												<div class="col-md-6" style="border-right:1px solid #98a6ad;">
													<div class="form-group">
														<label for="sibling_name">Name</label>
														{{ Form::text('sibling_name', @$secdetail->sibling_name, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<?php
													$sibling_dob = '';
														if(@$secdetail->sibling_dob != ''){
															$sibling_dob = date('d/m/Y', strtotime($secdetail->sibling_dob));
														}
													?>
													<div class="form-group">
														<label for="sibling_dob">DOB</label>
														{{ Form::text('sibling_dob', $sibling_dob, array('class' => 'form-control dobdatepickers', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_occ">Occupation</label>
														{{ Form::text('sibling_occ', @$secdetail->sibling_occ, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_gender">Gender</label>
														{{ Form::text('sibling_gender', @$secdetail->sibling_gender, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_country">Country of Residence</label>
														{{ Form::text('sibling_country', @$secdetail->sibling_country, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_marital">Marital Status</label>
														{{ Form::text('sibling_marital', @$secdetail->sibling_marital, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="sibling_name_2">Name</label>
														{{ Form::text('sibling_name_2', @$secdetail->sibling_name_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<?php
													$sibling_dob_2 = '';
														if(@$secdetail->sibling_dob_2 != ''){
															$sibling_dob_2 = date('d/m/Y', strtotime($secdetail->sibling_dob_2));
														}
													?>
													<div class="form-group">
														<label for="sibling_dob_2">DOB</label>
														{{ Form::text('sibling_dob_2', $sibling_dob_2, array('class' => 'form-control dobdatepickers', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_occ_2">Occupation</label>
														{{ Form::text('sibling_occ_2', @$secdetail->sibling_occ_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_gender_2">Gender</label>
														{{ Form::text('sibling_gender_2', @$secdetail->sibling_gender_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_country_2">Country of Residence</label>
														{{ Form::text('sibling_country_2', @$secdetail->sibling_country_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_marital_2">Marital Status</label>
														{{ Form::text('sibling_marital_2', @$secdetail->sibling_marital_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
												</div>
											</div>

										</div>
										<div class="col-md-12">
											<h5>Do you hold or held any visa for Australia or any other country</h5>
											<div class="form-group">
												<label for="held_visa">If Yes Mention Visa Subclass, Country Name, Year (all current and previous)</label>
												<label for="held_visa">If no Mention "No"</label>
												<textarea class="form-control" name="held_visa">{{@$secdetail->held_visa}}</textarea>
											</div>
										</div>
										<div class="col-md-12">
											<h5>Do you have any visa refused (Australia or any other country)</h5>
											<div class="form-group">
												<label for="visa_refused">If Yes Mention Visa Subclass, Country Name, Year (all visa refusals)</label>
												<label for="visa_refused">If no Mention "No"</label>
												<textarea class="form-control" name="visa_refused">{{@$secdetail->visa_refused}}</textarea>
											</div>
										</div>
										<div class="col-md-12">
											<h5>Have you travelled to any other country including Australia in last 10 years</h5>
											<div class="form-group">
												<label for="traveled">If Yes Mention Visa Subclass, Country Name, Departure Date, Arrival Date, type of visa</label>
												<textarea class="form-control" name="traveled">{{@$secdetail->traveled}}</textarea>
											</div>
										</div>
										<div class="col-12 col-md-12 col-lg-12">
											<div class="form-group float-right">
												<button onclick="customValidate('saveonlinesecform')" type="button" class="btn btn-primary">Save</button>
											</div>
										</div>
									</div>
									</form>
																			</div>
									<div class="tab-pane fade" id="formchild" role="tabpanel" aria-labelledby="formchild-tab">
									<?php $childdetail = \App\Models\OnlineForm::where('type', 'child')->where('client_id', $fetchedData->id)->first(); ?>
									<form method="post"  action="{{URL::to('/admin/saveonlinechildform')}}" autocomplete="off" name="saveonlinechildform" id="saveonlinechildform" enctype="multipart/form-data">
									@csrf
									<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
									<input type="hidden" name="type" value="child">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label for="contract_expiry">Name</label>
											{{ Form::text('info_name', @$childdetail->info_name, array('class' => 'form-control ', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Name' )) }}
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
											<label>Main Language</label>
											<?php
											$main_lang = array();
											if(isset($childdetail->main_lang) && @$childdetail->main_lang != ''){
												$main_lang = explode(',', $childdetail->main_lang);
											}
											?>
											<ul style="padding-left:0px;"><li style="display: inline-block;padding-right: 10px;"><label><input <?php if(in_array('Punjabi', $main_lang)){ echo 'checked'; }?> type="checkbox" value="Punjabi" name="main_lang[]"> Punjabi</label></li>
											<li style="display: inline-block;padding-right: 10px;"><label><input value="Hindi" <?php if(in_array('Hindi', $main_lang)){ echo 'checked'; }?>  type="checkbox" name="main_lang[]"> Hindi</label></li>
												<li style="display: inline-block;padding-right: 10px;"><label><input <?php if(in_array('Other', $main_lang)){ echo 'checked'; }?>  value="Other" type="checkbox" name="main_lang[]"> Other</label></li>
											</ul>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label for="marital_status">Marital Status</label>
												<select class="form-control" name="marital_status">
													<option <?php if(@$childdetail->marital_status == 'Married'){ echo 'selected'; }?> value="Married">Married</option>
													<option <?php if(@$childdetail->marital_status == 'Single'){ echo 'selected'; }?> value="Single">Single</option>
													<option <?php if(@$childdetail->marital_status == 'Other'){ echo 'selected'; }?> value="Other">Other</option>
												</select>
											</div>
										</div>
										<div class="col-md-12">

											<div class="form-group">
												<label for="mobile">Mobile</label>
											{{ Form::text('mobile', @$childdetail->mobile, array('class' => 'form-control ', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Mobile' )) }}
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label for="curr_address">Current Address</label>
											{{ Form::text('curr_address', @$childdetail->curr_address, array('class' => 'form-control ', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Current Address' )) }}
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label for="email">Email</label>
											{{ Form::text('email', @$childdetail->email, array('class' => 'form-control ', 'data-valid'=>'required email', 'autocomplete'=>'off','placeholder'=>'Email' )) }}
											</div>
										</div>
										<div class="col-md-12">
											<h5>Parents Details</h5>
											<div class="row">
												<div class="col-md-6" style="border-right:1px solid #98a6ad;">
													<div class="form-group">
														<label for="parent_name">Name</label>
														{{ Form::text('parent_name', @$childdetail->parent_name, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<?php
													$parent_dob = '';
														if(@$childdetail->parent_dob != ''){
															$parent_dob = date('d/m/Y', strtotime($secdetail->parent_dob));
														}
													?>
													<div class="form-group">
														<label for="parent_dob">DOB</label>
														{{ Form::text('parent_dob', $parent_dob, array('class' => 'form-control dobdatepickers', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="parent_occ">Occupation</label>
														{{ Form::text('parent_occ', @$childdetail->parent_occ, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="parent_country">Country of Residence</label>
														{{ Form::text('parent_country', @$childdetail->parent_country, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="parent_name_2">Name</label>
														{{ Form::text('parent_name_2', @$childdetail->parent_name_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<?php
													$parent_dob_2 = '';
														if(@$childdetail->parent_dob_2 != ''){
															$parent_dob_2 = date('d/m/Y', strtotime($childdetail->parent_dob_2));
														}
													?>
													<div class="form-group">
														<label for="parent_dob_2">DOB</label>
														{{ Form::text('parent_dob_2', $parent_dob_2, array('class' => 'form-control dobdatepickers', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="parent_occ_2">Occupation</label>
														{{ Form::text('parent_occ_2', @$childdetail->parent_occ_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="parent_country_2">Country of Residence</label>
														{{ Form::text('parent_country_2', @$childdetail->parent_country_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
												</div>
											</div>

										</div>
										<div class="col-md-12">
											<h5>All Siblings Details (in Australia and Overseas)</h5>
											<div class="row">
												<div class="col-md-6" style="border-right:1px solid #98a6ad;">
													<div class="form-group">
														<label for="sibling_name">Name</label>
														{{ Form::text('sibling_name', @$childdetail->sibling_name, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<?php
													$sibling_dob = '';
														if(@$childdetail->sibling_dob != ''){
															$sibling_dob = date('d/m/Y', strtotime($childdetail->sibling_dob));
														}
													?>
													<div class="form-group">
														<label for="sibling_dob">DOB</label>
														{{ Form::text('sibling_dob', $sibling_dob, array('class' => 'form-control dobdatepickers', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_occ">Occupation</label>
														{{ Form::text('sibling_occ', @$childdetail->sibling_occ, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_gender">Gender</label>
														{{ Form::text('sibling_gender', @$childdetail->sibling_gender, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_country">Country of Residence</label>
														{{ Form::text('sibling_country', @$childdetail->sibling_country, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_marital">Marital Status</label>
														{{ Form::text('sibling_marital', @$childdetail->sibling_marital, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="sibling_name_2">Name</label>
														{{ Form::text('sibling_name_2', @$childdetail->sibling_name_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<?php
													$sibling_dob_2 = '';
														if(@$childdetail->sibling_dob_2 != ''){
															$sibling_dob_2 = date('d/m/Y', strtotime($childdetail->sibling_dob_2));
														}
													?>
													<div class="form-group">
														<label for="sibling_dob_2">DOB</label>
														{{ Form::text('sibling_dob_2', $sibling_dob_2, array('class' => 'form-control dobdatepickers', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_occ_2">Occupation</label>
														{{ Form::text('sibling_occ_2', @$childdetail->sibling_occ_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_gender_2">Gender</label>
														{{ Form::text('sibling_gender_2', @$childdetail->sibling_gender_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_country_2">Country of Residence</label>
														{{ Form::text('sibling_country_2', @$childdetail->sibling_country_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
													<div class="form-group">
														<label for="sibling_marital_2">Marital Status</label>
														{{ Form::text('sibling_marital_2', @$childdetail->sibling_marital_2, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'' )) }}
													</div>
												</div>
											</div>

										</div>
										<div class="col-md-12">
											<h5>Do you hold or held any visa for Australia or any other country</h5>
											<div class="form-group">
												<label for="held_visa">If Yes Mention Visa Subclass, Country Name, Year (all current and previous)</label>
												<label for="held_visa">If no Mention "No"</label>
												<textarea class="form-control" name="held_visa">{{@$childdetail->held_visa}}</textarea>
											</div>
										</div>
										<div class="col-md-12">
											<h5>Do you have any visa refused (Australia or any other country)</h5>
											<div class="form-group">
												<label for="visa_refused">If Yes Mention Visa Subclass, Country Name, Year (all visa refusals)</label>
												<label for="visa_refused">If no Mention "No"</label>
												<textarea class="form-control" name="visa_refused">{{@$childdetail->visa_refused}}</textarea>
											</div>
										</div>
										<div class="col-md-12">
											<h5>Have you travelled to any other country including Australia in last 10 years</h5>
											<div class="form-group">
												<label for="traveled">If Yes Mention Visa Subclass, Country Name, Departure Date, Arrival Date, type of visa</label>
												<textarea class="form-control" name="traveled">{{@$childdetail->traveled}}</textarea>
											</div>
										</div>
										<div class="col-12 col-md-12 col-lg-12">
											<div class="form-group float-right">
												<button onclick="customValidate('saveonlinechildform')" type="button" class="btn btn-primary">Save</button>
											</div>
										</div>
									</div>
									</form>
									</div>
								</div>
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

@include('Admin/clients/addclientmodal')
@include('Admin/clients/editclientmodal')

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
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">From <span class="span_req">*</span></label>
								<select class="form-control" name="email_from" data-valid="required">
                                    <option value="">Select From</option>
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
								<select data-valid="" class="js-data-example-ajaxccd" name="email_cc[]"></select>

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
                                 <?php
                                $assignee = \App\Models\Admin::select('first_name')->where('id',@$fetchedData->assignee)->first();
                                if($assignee){
                                    $clientAssigneeName = $assignee->first_name;
                                } else {
                                    $clientAssigneeName = 'NA';
                                }
                                ?>
								<select data-valid="" class="form-control select2 selecttemplate" name="template" data-clientid="{{@$fetchedData->id}}" data-clientfirstname="{{@$fetchedData->first_name}}" data-clientvisaExpiry="{{@$fetchedData->visaExpiry}}" data-clientreference_number="{{@$fetchedData->client_id}}" data-clientassignee_name="{{@$clientAssigneeName}}">
									<option value="">Select</option>
									@foreach(\App\Models\CrmEmailTemplate::orderBy('id', 'desc')->get() as $list)
										<option value="{{$list->id}}">{{$list->name}}</option>
									@endforeach
								</select>

							</div>
						</div>
                        <!-- Inline ChatGPT Section (hidden by default) -->
                        <div id="chatGptSection" class="collapse mt-3 col-9 col-md-9 col-lg-9">
                            <div class="card card-body">
                                <div class="form-group">
                                    <label for="chatGptInput">Enter your message to enhance:</label>
                                    <textarea class="form-control" id="chatGptInput" rows="5" placeholder="Type your message here..."></textarea>
                                </div>
                                <div class="mt-2 text-end">
                                    <button type="button" class="btn btn-primary" id="enhanceMessageBtn">Enhance</button>
                                    <button type="button" class="btn btn-secondary" id="chatGptClose">Close</button>
                                </div>
                            </div>
                        </div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="subject">Subject <span class="span_req">*</span>
                                <button type="button" class="btn btn-info" id="chatGptToggle">ChatGPT Enhance</button>  
                              </label>
								{{ Form::text('subject', '', array('id'=>'compose_email_subject','class' => 'form-control selectedsubject', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Subject' )) }}
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
								<textarea class="summernote-simple selectedmessage" id="compose_email_message" name="message"></textarea>
								@if ($errors->has('message'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('message') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
						     <div class="form-group">
						        <label>Attachment</label>
						        <input type="file" name="attach[]" class="form-control" multiple>
						     </div>
						</div>
                      
                         <div class="col-12 col-md-12 col-lg-12">
                            <div class="composeemail-tab">
                                <ul class="nav nav-pills round_tabs" id="composeemails-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="pill" id="composechecklist-tab" href="#composechecklist" role="tab" aria-controls="composechecklist" aria-selected="true">Checklist</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="pill" id="composedocument-tab" href="#composedocument" role="tab" aria-controls="composedocument" aria-selected="false">Document List</a>
                                    </li>
                                </ul>

                                <div class="tab-content" id="composeemailContent">
                                    <div class="tab-pane fade show active" id="composechecklist" role="tabpanel" aria-labelledby="composechecklist-tab">
                                        <div class="table-responsive uploadchecklists">

                                            <table id="mychecklist-datatable" class="table text_wrap table-2">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th style="white-space: initial;">File Name</th>
                                                        <th style="white-space: initial;">File</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach(\App\Models\UploadChecklist::all() as $uclist)
                                                    <tr>
                                                        <td><input type="checkbox" name="checklistfile[]" value="{{$uclist->id}}" {{ old('checklistfile') && in_array($uclist->id, old('checklistfile', [])) ? 'checked' : '' }}></td>
                                                        <td style="white-space: initial;">{{$uclist->name}}</td>
                                                        <td style="white-space: initial;"><a target="_blank" href="{{ URL::to('/public/checklists/'.$uclist->file) }}">{{$uclist->name}}</a></td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="composedocument" role="tabpanel" aria-labelledby="composedocument-tab">
                                        <?php echo $fetchedData->id;?>
                                        <table id="mydocumentlist-datatable" class="table text_wrap table-2">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th style="white-space: initial;">File Name</th>
                                                    <th>Document Type</th>
                                                    <th style="white-space: initial;">File</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(\App\Models\Document::where('client_id',$fetchedData->id)->where('type','client')->whereIn('doc_type', ['education', 'migration', 'documents'])->whereNull('not_used_doc')->orderby('created_at', 'DESC')->get() as $composedoclist)
                                                <tr>
                                                    <td><input type="checkbox" name="checklistfile_document[]" value="{{$composedoclist->id}}" {{ old('checklistfile_document') && in_array($composedoclist->id, old('checklistfile_document', [])) ? 'checked' : '' }}></td>
                                                    <td style="white-space: initial;">{{$composedoclist->file_name}}</td>
                                                    <td>
                                                        <?php
                                                        $docTypes = [
                                                            'education' => 'Education',
                                                            'migration' => 'Migration',
                                                            'documents' => 'Document'
                                                        ];

                                                        echo isset($composedoclist->doc_type)
                                                            ? ($docTypes[$composedoclist->doc_type] ?? 'N/A')
                                                            : 'N/A';
                                                        ?>
                                                    </td>

                                                    <td style="white-space: initial;">
                                                        <?php
                                                        if( isset($composedoclist->doc_type) && $composedoclist->doc_type != "" )
                                                        {
                                                            if( $composedoclist->doc_type == "education" || $composedoclist->doc_type == "migration" ){ ?>
                                                                <a target="_blank" class="dropdown-item" href="{{URL::to('/public/img/documents')}}/{{$composedoclist->myfile}}">{{$composedoclist->file_name}}</a>
                                                            <?php
                                                            }
                                                            else if( $composedoclist->doc_type == "documents")
                                                            {
                                                                if( isset($composedoclist->myfile_key) && $composedoclist->myfile_key != "")
                                                                { ?>
                                                                    <a target="_blank" href="<?php echo $composedoclist->myfile;?>">{{$composedoclist->file_name}}</a>
                                                                <?php
                                                                }
                                                                else
                                                                {
                                                                    $clientInfo = \App\Models\Admin::where('id',$fetchedData->id)->select('client_id')->first();
                                                                    if($clientInfo){
                                                                        $client_unique_id = $clientInfo->client_id;
                                                                    } else {
                                                                        $client_unique_id = 'N/A';
                                                                    }
                                                                    $doc_type = $composedoclist->doc_type;
                                                                    $myfile = $composedoclist->myfile;

                                                                    $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                                                    $composedoclistUrl = $url.$client_unique_id.'/'.$doc_type.'/'.$myfile; //dd($awsUrl);

                                                                    ?>
                                                                    <a target="_blank" href="<?php echo $composedoclistUrl;?>"><?php echo $composedoclist->file_name;?></a>
                                                                <?php
                                                                }
                                                            }
                                                        } ?>
                                                    </td>

                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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



<!-- Send Message-->
<div id="sendmsgmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="messageModalLabel">Send Message</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" name="sendmsg" id="sendmsg" action="{{URL::to('/admin/sendmsg')}}" autocomplete="off" enctype="multipart/form-data">
				    @csrf
                    <input type="hidden" name="client_id" id="sendmsg_client_id" value="">
                    <input type="hidden" name="vtype" value="client">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Message <span class="span_req">*</span></label>
								<textarea class="summernote-simple selectedmessage" name="message" data-valid="required"></textarea>
								@if ($errors->has('message'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('message') }}</strong>
									</span>
								@endif
							</div>
						</div>
                        <div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('sendmsg')" type="button" class="btn btn-primary">Send</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


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

<div id="confirmNotUseDocModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to send this document in Not Use Tab?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Send</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmBackToDocModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to send this in document Tab again?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Send</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmDocModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to verify this doc?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Verify</button>
				<button type="button" style="margin-top: 40px;" data-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmLogModal" tabindex="-1" role="dialog" aria-labelledby="confirmLogModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to delete this log?</h4>
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
				<form method="post" action="{{URL::to('/admin/application/saleforcast')}}" name="saleforcast" id="saleforcast" autocomplete="off" enctype="multipart/form-data">
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
				<form method="post" action="{{URL::to('/admin/application/application_ownership')}}" name="xapplication_ownership" id="xapplication_ownership" autocomplete="off" enctype="multipart/form-data">
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
				<form method="post" action="{{URL::to('/admin/application/spagent_application')}}" name="spagent_application" id="spagent_application" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="siapp_id" id="siapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="super_agent">Super Agent <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control super_agent" id="super_agent" name="super_agent">
									<option value="">Please Select</option>
									<?php $sagents = \App\Models\Agent::whereRaw('FIND_IN_SET("Super Agent", agent_type)')->get(); ?>
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
				<form method="post" action="{{URL::to('/admin/application/sbagent_application')}}" name="sbagent_application" id="sbagent_application" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="sbapp_id" id="sbapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="sub_agent">Sub Agent <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control sub_agent" id="sub_agent" name="sub_agent">
									<option value="">Please Select</option>
									<?php $sagents = \App\Models\Agent::whereRaw('FIND_IN_SET("Sub Agent", agent_type)')->where('is_acrchived',0)->get(); ?>
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
				<form method="post" action="{{URL::to('/admin/save_tag')}}" name="stags_application" id="stags_application" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" id="client_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="super_agent">Tags <span class="span_req">*</span></label>
									<!--<select data-valid="required" multiple class="tagsselec form-control super_tag" id="tag" name="tag[]">-->
                                  <select data-valid="required" multiple  id="tag" class="tagsselec form-control super_tag" name="tag[]">
                                    <?php /*$r = array();
                                    if($fetchedData->tagname != ''){
                                        $r = explode(',', $fetchedData->tagname);
                                    }*/
                                    ?>
									<!--<option value="">Please Select</option>-->
									<?php //$stagd = \App\Models\Tag::where('id','!=','')->paginate(5); ?>
									{{--@foreach($stagd as $sa)--}}
										<!--<option <?php //if(in_array($sa->id, $r)){ echo 'selected'; } ?> value="{{--$sa->id--}}">{{--$sa->name--}}</option>-->
									{{--@endforeach--}}
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

<div class="modal fade custom_modal" id="new_fee_option_latest" tabindex="-1" role="dialog" aria-labelledby="feeoptionModalLabelLatest" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="feeoptionModalLabelLatest">Other Fee Option</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showproductfee_latest">

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
				<form method="post" action="{{URL::to('/admin/application/saleforcast')}}" name="saleforcast" id="saleforcast" autocomplete="off" enctype="multipart/form-data">
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
				<form method="post" action="{{URL::to('/admin/application/saleforcastservice')}}" name="saleforcastservice" id="saleforcastservice" autocomplete="off" enctype="multipart/form-data">
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

<div class="modal fade custom_modal" id="serviceTaken" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel">Service Taken</h5>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                <form method="post" action="{{URL::to('/admin/client/createservicetaken')}}" name="createservicetaken" id="createservicetaken" autocomplete="off" enctype="multipart/form-data">
				@csrf
                    <input id="logged_client_id" name="logged_client_id"  type="hidden" value="<?php echo $fetchedData->id;?>">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">

							<div class="form-group">
								<label style="display:block;" for="service_type">Select Service Type:</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="Migration_inv" value="Migration" name="service_type" checked>
									<label class="form-check-label" for="Migration_inv">Migration</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="Eductaion_inv" value="Eductaion" name="service_type">
									<label class="form-check-label" for="Eductaion_inv">Eductaion</label>
								</div>
								<span class="custom-error service_type_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12 is_Migration_inv">
                            <div class="form-group">
								<label for="mig_ref_no">Reference No: <span class="span_req">*</span></label>
                                <input type="text" name="mig_ref_no" id="mig_ref_no" value="" class="form-control" data-valid="required">
                            </div>

                            <div class="form-group">
								<label for="mig_service">Service: <span class="span_req">*</span></label>
                                <input type="text" name="mig_service" id="mig_service" value="" class="form-control" data-valid="required">
                            </div>

                            <div class="form-group">
								<label for="mig_notes">Notes: <span class="span_req">*</span></label>
                                <input type="text" name="mig_notes" id="mig_notes" value="" class="form-control" data-valid="required">
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12 is_Eductaion_inv" style="display:none;">
                            <div class="form-group">
								<label for="edu_course">Course: <span class="span_req">*</span></label>
                                <input type="text" name="edu_course" id="edu_course" value="" class="form-control">
                            </div>

                            <div class="form-group">
								<label for="edu_college">College: <span class="span_req">*</span></label>
                                <input type="text" name="edu_college" id="edu_college" value="" class="form-control">
                            </div>

                            <div class="form-group">
								<label for="edu_service_start_date">Service Start Date: <span class="span_req">*</span></label>
                                <input type="text" name="edu_service_start_date" id="edu_service_start_date" value="" class="form-control">
                            </div>

                            <div class="form-group">
								<label for="edu_notes">Notes: <span class="span_req">*</span></label>
                                <input type="text" name="edu_notes" id="edu_notes" value="" class="form-control">
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('createservicetaken')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php
if($fetchedData->tagname != ''){
   $tagnameArr = explode(',', $fetchedData->tagname);
   foreach($tagnameArr AS $tag1){
       $tagWord = \App\Models\Tag::where('id', $tag1)->first();
   ?>
<input type="hidden" class="relatedtag" data-name="<?php echo $tagWord->name; ?>" data-id="<?php echo $tagWord->id; ?>">
<?php
   }
} ?>

@endsection
@section('scripts')
<script src="{{URL::to('/')}}/public/js/popover.js"></script>
<script src="{{URL::asset('public/js/bootstrap-datepicker.js')}}"></script>

@if($showAlert)
    <script>
        alert("Have u updated the following details - email address,current address,current visa,visa expiry,other fields? Pls update these details before forwarding this to anyone?");
    </script>
@endif

<script>
  
    //For download document
    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('click', function (e) {
            // Check if the clicked element has the class `.download-file`
            const target = e.target.closest('a.download-file');

            // If it's not a .download-file anchor, do nothing
            if (!target) return;

            e.preventDefault();

            const filelink = target.dataset.filelink;
            const filename = target.dataset.filename;

            if (!filelink || !filename) {
                alert('Missing file info.');
                return;
            }

            // Create and submit a hidden form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url("/admin/download-document") }}';
            form.target = '_blank';

            // CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.innerHTML = `
                <input type="hidden" name="_token" value="${token}">
                <input type="hidden" name="filelink" value="${filelink}">
                <input type="hidden" name="filename" value="${filename}">
            `;

            document.body.appendChild(form);
            form.submit();
            form.remove();
        });
    });
     
  	function previewFile(fileType,fileUrl, containerClass) {
        const container = document.querySelector(`.${containerClass}`);

        if (!container) {
            console.error('Preview container not found');
            return;
        }

        // Clear existing content
        container.innerHTML = '';

        switch (fileType.toLowerCase()) {
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                const img = document.createElement('img');
                img.src = fileUrl;
                img.className = 'preview-image';
                container.appendChild(img);
                break;

            case 'pdf':
                const iframe = document.createElement('iframe');
                iframe.src = `https://docs.google.com/viewer?url=${encodeURIComponent(fileUrl)}&embedded=true`;
                iframe.className = 'pdf-viewer';
                container.appendChild(iframe);
                break;

            case 'doc':
            case 'docx':
            case 'xls':
            case 'xlsx':
            case 'ppt':
            case 'pptx':
                const officeViewer = document.createElement('iframe');
                officeViewer.src = `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(fileUrl)}`;
                officeViewer.className = 'doc-viewer';
                container.appendChild(officeViewer);
                break;

            default:
                container.innerHTML = `
                    <div class="preview-placeholder">
                        <i class="fas fa-exclamation-circle fa-3x mb-3 text-warning"></i>
                        <p>Preview not available for this file type.</p>
                    </div>
                `;
        }
    }
  
    document.getElementById('chatGptToggle').addEventListener('click', function() {
        const section = document.getElementById('chatGptSection');
        section.classList.toggle('collapse');
    });

    document.getElementById('chatGptClose').addEventListener('click', function() {
        const section = document.getElementById('chatGptSection');
        section.classList.add('collapse');
    });

    document.getElementById('enhanceMessageBtn').addEventListener('click', function() {
        const chatGptInput = document.getElementById('chatGptInput').value;
        if (!chatGptInput) {
            alert('Please enter a message to enhance.');
            return;
        }

        fetch("{{ route('admin.mail.enhance') }}", {  // Use Laravel's route helper
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content") // Fetch CSRF token dynamically
            },
            body: JSON.stringify({ message: chatGptInput })
        })
        .then(response => response.json())
        .then(data => {
            if (data.enhanced_message) {
                // Split the enhanced message into lines
                const lines = data.enhanced_message.split('\n').filter(line => line.trim() !== '');

                // First line is the subject
                const subject = lines[0] || '';

                // Remaining lines are the body
                const body = lines.slice(1).join('\n') || '';

                // Update the subject and message fields
                document.getElementById('compose_email_subject').value = subject;
                //document.getElementById('compose_email_message').value = body;
                // Ensure Summernote is initialized before updating content
                $("#emailmodal .summernote-simple").summernote('code',body);

                // Close the ChatGPT section
                document.getElementById('chatGptSection').classList.add('collapse');
            } else {
                alert(data.error || 'Failed to enhance message.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while enhancing the message.');
        });
    });
</script>
  
<script>
jQuery(document).ready(function($){
  
     //Tab click
    $(document).delegate('#client_tabs a', 'click', function(){
        // Get the target tab's href
        var target = $(this).attr('href'); //console.log(target);

        if (target === '#documents' || target === '#migrationdocuments' || target === '#alldocuments' || target === '#notuseddocuments' ) {
           // Reset the visibility and classes
            $('.left_section').hide(); // Hide the left section by default
            $('.right_section').parent().removeClass('col-8 col-md-8 col-lg-8').addClass('col-12 col-md-12 col-lg-12');
        }  else {
            $('.left_section').show(); // Show the left section for Activities tab
            $('.right_section').parent().removeClass('col-12 col-md-12 col-lg-12').addClass('col-8 col-md-8 col-lg-8');
        }
    });
  
     $('.selecttemplate').select2({dropdownParent: $('#emailmodal')});

     /////////////////////////////////////////////
    ////// At Google review button sent email with review link code start /////////
    /////////////////////////////////////////////
    $(document).delegate('.googleReviewBtn', 'click', function(e){
        var is_greview_mail_sent = $(this).attr('data-is_greview_mail_sent');
        console.log(is_greview_mail_sent);
        if(is_greview_mail_sent != 1){
            is_greview_mail_sent = 0;
        } else {
            is_greview_mail_sent = 1;
        }
        var conf = confirm('Do you want to sent google review link in email?');
        //If review email not sent till now
	    if(conf && is_greview_mail_sent != 1 ){
            $.ajax({
                url: '{{URL::to('/admin/is_greview_mail_sent')}}',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type:'POST',
                datatype:'json',
                data:{id:'{{$fetchedData->id}}',is_greview_mail_sent:is_greview_mail_sent},
                success: function(response){
                    var obj = $.parseJSON(response);
                    if(obj.status){
                        alert(obj.message);
                        location.reload();
                    } else {
                        alert(obj.message);
                    }
                }
            });
        } else {
            return false;
        }
    });

    /////////////////////////////////////////////
    ////// At Google review button sent email with review link code end /////////
    /////////////////////////////////////////////

  	//create client receipt start
    //$('.report_date_fields').datepicker({ format: 'dd/mm/yyyy',autoclose: true });
    $('.report_date_fields').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());
    $('.report_entry_date_fields').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());


    $(document).delegate('.openproductrinfo', 'click', function(){
		var clonedval = $('.clonedrow').html();
		$('.productitem').append('<tr class="product_field_clone">'+clonedval+'</tr>');
        //$('.report_date_fields').datepicker({ format: 'dd/mm/yyyy', autoclose: true  });
        $('.report_date_fields').last().datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());
        $('.report_entry_date_fields').last().datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

    });

    $(document).delegate('.removeitems', 'click', function(){
		var $tr    = $(this).closest('.product_field_clone');
		var trclone = $('.product_field_clone').length;
		if(trclone > 0){
            $tr.remove();
		}
		grandtotalAccountTab();
	});

    $(document).delegate('.deposit_amount_per_row', 'keyup', function(){
        grandtotalAccountTab();
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function getTopReceiptValInDB(type) {
        $.ajax({
            type:'post',
            url: '{{URL::to('/admin/clients/getTopReceiptValInDB')}}',
            sync:true,
            data: {type:type},
            success: function(response){
                var obj = $.parseJSON(response); //console.log('record_count=='+obj.record_count);
                if(obj.receipt_type == 1){ //client receipt
                    if(obj.record_count >0){
                        $('#top_value_db').val(obj.record_count);
                    } else {
                        $('#top_value_db').val(obj.record_count);
                    }
                }
            }
        });
    }





    $(document).delegate('.deposit_amount_per_row', 'blur', function(){
        if( $(this).val() != ""){
            var randomNumber = $('#top_value_db').val();
            randomNumber = Number(randomNumber);
            randomNumber = randomNumber + 1; //console.log(randomNumber);
            $('#top_value_db').val(randomNumber);
            randomNumber = "Rec"+randomNumber;
            //$(this).closest('tr').find('.unique_trans_no').val(randomNumber);
            //$(this).closest('tr').find('.unique_trans_no_hidden').val(randomNumber);
        } else {
            //$(this).closest('tr').find('.unique_trans_no').val();
            //$(this).closest('tr').find('.unique_trans_no_hidden').val();
        }
    });

    function grandtotalAccountTab(){
        var total_deposit_amount_all_rows = 0;
        $('.productitem tr').each(function(){
            if($(this).find('.deposit_amount_per_row').val() != ''){
                var deposit_amount_per_row = $(this).find('.deposit_amount_per_row').val();

            }else{
                var deposit_amount_per_row = 0;
            }
            total_deposit_amount_all_rows += parseFloat(deposit_amount_per_row);
        });
        $('.total_deposit_amount_all_rows').html("$"+total_deposit_amount_all_rows.toFixed(2));
    }
    //create client receipt changes end


    /////////////////////////////////////////////////
    ////// stag code start ///
    ////////////////////////////////////////////////
    <?php if($fetchedData->tagname != '')
    { ?>
    	var array1 = [];
	    var data1 = [];
        $('.relatedtag').each(function(){
            var id1 = $(this).attr('data-id');
			array1.push(id1);
			var name1 = $(this).attr('data-name');
            data1.push({
				id: id1,
                text: name1,
            });
	    });

        $("#tag").select2({
            data: data1,
            escapeMarkup: function(markup) {
                return markup;
            },
            templateResult: function(data1) {
                return data1.html;
            },
            templateSelection: function(data1) {
                return data1.text;
            }
        });

	    $('#tag').val(array1);
		$('#tag').trigger('change');
    <?php
    } ?>

    $('#tag').select2({
        ajax: {
            url: '{{URL::to('/admin/gettagdata')}}',
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.items.map(item => ({
                        id: item.id,
                        text: item.text
                    })),
                    pagination: {
                        more: (params.page * data.per_page) < data.total_count
                    }
                };
            },
            cache: true
        },
        placeholder: 'Search & Select tag',
        minimumInputLength: 1,
        templateResult: formatItem, // Custom function to format the result
        templateSelection: formatItemSelection // Custom function to format the selection
    });

    function formatItem(item) {
        if (item.loading) {
            return item.text;
        }
        //return `<div class='select2-result-item'>${item.text}</div>`;
        return item.text;
    }

    function formatItemSelection(item) {
        return item.text || item.id;
    }

    /////////////////////////////////////////////////
    ////// stag code end ///
    ////////////////////////////////////////////////

    $('#edu_service_start_date').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true
    });

    $('.filter_btn').on('click', function(){
		$('.filter_panel').slideToggle();
	});

	/*var user_search = '<?php //echo $_REQUEST["user"]?>';
	var keyword_search = '<?php //echo $_REQUEST["keyword"]?>';
	if( user_search != "" || keyword_search != "" ) {
		$('.filter_panel').css('display','block');
	}*/

    //Service type on chnage div
    $('.modal-body form#createservicetaken input[name="service_type"]').on('change', function(){
        var invid = $(this).attr('id');
        if(invid == 'Migration_inv'){
            $('.modal-body form#createservicetaken .is_Migration_inv').show();
            $('.modal-body form#createservicetaken .is_Migration_inv input').attr('data-valid', 'required');
            $('.modal-body form#createservicetaken .is_Eductaion_inv').hide();
            $('.modal-body form#createservicetaken .is_Eductaion_inv input').attr('data-valid', '');
        }
        else {
            $('.modal-body form#createservicetaken .is_Eductaion_inv').show();
            $('.modal-body form#createservicetaken .is_Eductaion_inv input').attr('data-valid', 'required');
            $('.modal-body form#createservicetaken .is_Migration_inv').hide();
            $('.modal-body form#createservicetaken .is_Migration_inv input').attr('data-valid', '');
        }
    });

    //Set select2 drop down box width
    $('#changeassignee').select2();
    $('#changeassignee').next('.select2-container').first().css('width', '220px');

    var windowsize = $(window).width();
    if(windowsize > 2000){
        $('.add_note').css('width','980px');
    }


     /////////////////////////////////////////////
    ////// not picked call button code start /////////
    /////////////////////////////////////////////
    /*$(document).delegate('.not_picked_call', 'click', function(e){
        var conf = confirm('Are you sure want to send text message to this user?');
	    if(conf){
            var not_picked_call = 1;
            $.ajax({
                url: '{{URL::to('/admin/not-picked-call')}}',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type:'POST',
                datatype:'json',
                data:{id:'{{$fetchedData->id}}',not_picked_call:not_picked_call},
                success: function(response){
                    var obj = $.parseJSON(response);
                    if(obj.not_picked_call == 1){
                        alert(obj.message);
                        //location.reload();
                    } else {
                        alert(obj.message);
                    }
                    getallactivities();
                }
            });
        } else {
            return false;
        }
    });*/
  
  
    $(document).delegate('.not_picked_call', 'click', function (e) {
        var clientName = '{{$fetchedData->first_name ?? 'user'}}';
        clientName = clientName.charAt(0).toUpperCase() + clientName.slice(1).toLowerCase(); //alert(clientName);

        /*var loggedInUserId = '{{ Auth::user()->id }}';  //alert(loggedInUserId);
        if(loggedInUserId == 541){ //Arun
            var message = `Hi ${clientName},

We tried reaching you but couldn’t connect. Please call us at 0396021330 or let us know a suitable time.

Please do not reply via SMS.

Best regards,
Bansal Immigration`;
            
        } else if(loggedInUserId == 1346 || loggedInUserId == 1 || loggedInUserId == 35520){ //Ajay
           var message = `Hi ${clientName},

We tried reaching you but couldn’t connect. Please call us at 0396021330 or let us know a suitable time.

Please do not reply via SMS.

Best regards,
Bansal Immigration`;
          
        } else {
            var message = `Hi ${clientName},

We tried reaching you but couldn’t connect. Please call us at 0396021330 or let us know a suitable time.

Please do not reply via SMS.

Best regards,
Bansal Immigration`;
          
        }*/
      
        var message = `Hi ${clientName},
We tried reaching you but couldn’t connect. Please call us at 0396021330 or let us know a suitable time.
Please do not reply via SMS.
Bansal Immigration`;
      
        $('#messageText').val(message); // Set dynamic message text
        $('#notPickedCallModal').modal('show'); // Show Modal Window

        $('.sendMessage').on('click', function () {
            var message = $('#messageText').val();
            var not_picked_call = 1;
            $.ajax({
                url: '{{URL::to('/admin/not-picked-call')}}',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                datatype: 'json',
                data: {
                    id: '{{$fetchedData->id}}',
                    not_picked_call: not_picked_call,
                    message: message
                },
                success: function (response) {
                    var obj = $.parseJSON(response);
                    if (obj.not_picked_call == 1) {
                        alert(obj.message);
                    } else {
                        alert(obj.message);
                    }
                    getallactivities();
                    $('#notPickedCallModal').modal('hide'); // Hide Modal Window
                }
            });
        });
    });

     

    /////////////////////////////////////////////
    ////// not picked call button code end //////
    /////////////////////////////////////////////


    /////////////////////////////////////////////
    ////// appointment popup chnages start /////////
    /////////////////////////////////////////////

    $(document).delegate('.enquiry_item', 'change', function(){
        var id = $(this).val();
        if(id != ""){
            var v = 'services';
            if(id == 8){  //If nature of service == INDIA/UK/CANADA/EUROPE TO AUSTRALIA
                $('#serviceval_2').hide();
            } else {
                $('#serviceval_2').show();
            }

            $('.services_row').show();
            $('#myTab .nav-item #nature_of_enquiry-tab').addClass('disabled');
            $('#myTab .nav-item #services-tab').removeClass('disabled');
            $('#myTab a[href="#'+v+'"]').trigger('click');

             $('.services_item').prop('checked', false);
            $('.appointment_row').hide();
            $('.info_row').hide();
            $('.confirm_row').hide();

            $('.timeslots').html('');
            $('.showselecteddate').html('');

            $('#timeslot_col_date').val("");
            $('#timeslot_col_time').val(""); //Do blank Timeslot selected date and time
        } else {
            var v = 'nature_of_enquiry';
            $('.services_row').hide();
            $('.appointment_row').hide();
            $('.info_row').hide();
            $('.confirm_row').hide();

            $('#myTab .nav-item #services-tab').addClass('disabled');
            $('#myTab .nav-item #nature_of_enquiry-tab').removeClass('disabled');
            $('#myTab a[href="#'+v+'"]').trigger('click');
        }
        $('input[name="noe_id"]').val(id);
	});

    $(document).on('change', '.inperson_address', function() {
        var id = $("input[name='inperson_address']:checked").attr('data-val'); //alert(id);
        if(id != ""){
            var v = 'info';
            $('.info_row').show();
            $('.appointment_details_cls').show();

            $('#myTab .nav-item #appointment_details-tab').addClass('disabled');
            $('#myTab .nav-item #info-tab').removeClass('disabled');
            $('#myTab a[href="#'+v+'"]').trigger('click');
        } else {
            var v = 'appointment_details';
            $('.info_row').hide();
            $('.appointment_details_cls').hide();
            $('.confirm_row').hide();

            $('#myTab .nav-item #info-tab').addClass('disabled');
            $('#myTab .nav-item #appointment_details-tab').removeClass('disabled');
            $('#myTab a[href="#'+v+'"]').trigger('click');
        }

        if( $("input[name='radioGroup']:checked").val() == 1  ){ //paid
            $('#promo_code_used').css('display','inline-block');
        } else { //free
            $('#promo_code_used').css('display','none');
        }

        $("input[name='inperson_address']:checked").val(id);
        $('.timeslots').html('');
        if(id != ""){
            var enquiry_item  = $('.enquiry_item').val(); //alert(enquiry_item);
            var service_id = $("input[name='radioGroup']:checked").val(); //alert(service_id);
            var inperson_address = $("input[name='inperson_address']:checked").attr('data-val'); //alert(inperson_address);
            $.ajax({
                url:'{{URL::to('/getdatetimebackend')}}',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type:'POST',
                data:{id:service_id, enquiry_item:enquiry_item, inperson_address:inperson_address },
                datatype:'json',
                success:function(res){
                    var obj = JSON.parse(res);
                    if(obj.success){
                        duration = obj.duration;
                        daysOfWeek =  obj.weeks;
                        starttime =  obj.start_time;
                        endtime =  obj.end_time;
                        disabledtimeslotes = obj.disabledtimeslotes;
                        var datesForDisable = obj.disabledatesarray;

                        $('#datetimepicker').datepicker({
                            inline: true,
                            startDate: new Date(),
                            datesDisabled: datesForDisable,
                            daysOfWeekDisabled: daysOfWeek,
                            format: 'dd/mm/yyyy'
                        }).on('changeDate', function(e) {
                            var date = e.format();
                            var checked_date=e.date.toLocaleDateString('en-US');

                            $('.showselecteddate').html(date);
                            $('input[name="date"]').val(date);
                            $('#timeslot_col_date').val(date);


                            $('.timeslots').html('');
                            var start_time = parseTime(starttime),
                            end_time = parseTime(endtime),
                            interval = parseInt(duration);
                            var service_id = $("input[name='radioGroup']:checked").val(); //alert(service_id);
                            var inperson_address = $("input[name='inperson_address']:checked").attr('data-val'); //alert(inperson_address);
                            var enquiry_item  = $('.enquiry_item').val(); //alert(enquiry_item);
                            //var slot_overwrite_hidden = $('#slot_overwrite_hidden').val();
                            //console.log('slot_overwrite_hidden='+slot_overwrite_hidden)
                            $.ajax({
                                url:'{{URL::to('/getdisableddatetime')}}',
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                type:'POST',
                                data:{service_id:service_id,sel_date:date, enquiry_item:enquiry_item,inperson_address:inperson_address},
                                datatype:'json',
                                success:function(res){
                                    $('.timeslots').html('');
                                    var obj = JSON.parse(res);
                                    if(obj.success){
                                        //console.log('slot_overwrite_hidden='+$('#slot_overwrite_hidden').val() )
                                        if( $('#slot_overwrite_hidden').val() == 1){
                                            var objdisable = [];
                                        } else {
                                            var objdisable = obj.disabledtimeslotes;
                                        }
                                        //console.log('objdisable='+objdisable);

                                        var start_timer = start_time;
                                        for(var i = start_time; i<end_time; i = i+interval){
                                            var timeString = start_timer + interval;
                                            // Prepend any date. Use your birthday.
                                            const timeString12hr = new Date('1970-01-01T' + convertHours(start_timer) + 'Z')
                                            .toLocaleTimeString('en-US',
                                                {timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}
                                            );
                                            const timetoString12hr = new Date('1970-01-01T' + convertHours(timeString) + 'Z')
                                            .toLocaleTimeString('en-US',
                                                {timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}
                                            );

                                            var today_date = new Date();
                                            //const options = { timeZone: 'Australia/Sydney'};
                                            today_date = today_date.toLocaleDateString('en-US');

                                            // current time
                                            var now = new Date();
                                            var nowTime = new Date('1/1/1900 ' + now.toLocaleTimeString(navigator.language, {
                                                hour: '2-digit',
                                                minute: '2-digit',
                                                hour12: true
                                            }));

                                            var current_time=nowTime.toLocaleTimeString('en-US');
                                            if(objdisable.length > 0){
                                                if(jQuery.inArray(timeString12hr, objdisable) != -1  ) {

                                                } else if ((checked_date == today_date) && (current_time > timeString12hr || current_time > timetoString12hr)){ //console.log('elseee-ifff');
                                                } else{
                                                    $('.timeslots').append('<div data-fromtime="'+timeString12hr+'" data-totime="'+timetoString12hr+'" style="cursor: pointer;" class="timeslot_col"><span>'+timeString12hr+'</span></div>');
                                                }
                                            } else{
                                                if((checked_date == today_date) && (current_time > timeString12hr || current_time > timetoString12hr)){
                                                } else {
                                                    $('.timeslots').append('<div data-fromtime="'+timeString12hr+'" data-totime="'+timetoString12hr+'" style="cursor: pointer;" class="timeslot_col"><span>'+timeString12hr+'</span></div>');
                                                }
                                            }
                                            start_timer = timeString;
                                        }
                                    }else{

                                    }
                                }
                            });
                        });

                        if(id != ""){
                            var v = 'appointment_details';
                            $('#myTab .nav-item #services-tab').addClass('disabled');
                            $('#myTab .nav-item #appointment_details-tab').removeClass('disabled');
                            $('#myTab a[href="#'+v+'"]').trigger('click');
                        } else {
                            var v = 'services';
                            $('#myTab .nav-item #services-tab').removeClass('disabled');
                            $('#myTab .nav-item #appointment_details-tab').addClass('disabled');
                            $('#myTab a[href="#'+v+'"]').trigger('click');
                        }
                        $('input[name="service_id"]').val($("input[name='radioGroup']:checked").val());
                    } else {
                        $('input[name="service_id"]').val('');
                        var v = 'services';
                        alert('There is a problem in our system. please try again');
                        $('#myTab .nav-item #services-tab').removeClass('disabled');
                        $('#myTab .nav-item #appointment_details-tab').addClass('disabled');
                    }
                }
            })
        }
	});

    $(document).delegate('.appointment_item', 'change', function(){
        var id = $(this).val();
        if(id != ""){
            $('input[name="appointment_details"]').val(id);
        } else {
            $('input[name="appointment_details"]').val("");
        }
    });

    $(document).delegate('#promo_code', 'blur', function(){
        var promo_code_val = $(this).val();
        var client_id = '<?php echo $fetchedData->id;?>';
        $.ajax({
			url:'{{URL::to('/admin/promo-code/checkpromocode')}}',
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			type:'POST',
			data:{promo_code_val:promo_code_val, client_id:client_id },
			datatype:'json',
			success:function(res){
				var obj = JSON.parse(res);
				if(obj.success == true){
                    $('#promocode_id').val(obj.promocode_id);
                    $('#promo_msg').css('display','block');
                    $('#promo_msg').css('color','green');
                    $('#promo_msg').text(obj.msg);
                    $('#appointform_save').prop('disabled', false);
                } else {
                    $('#promocode_id').val("");
                    $('#promo_msg').css('display','block');
                    $('#promo_msg').css('color','red');
                    $('#promo_msg').text(obj.msg);
                    $('#appointform_save').prop('disabled', true);
                }
            }
        });
    });

	$(document).delegate('.services_item', 'change', function(){
        $('.info_row').hide();
        $('.confirm_row').hide();
        $("input[name='inperson_address']").prop("checked", false);
        $('.appointment_item').val("");
        $('.appointment_details_cls').hide();

        $('#timeslot_col_date').val("");
        $('#timeslot_col_time').val(""); //Do blank Timeslot selected date and time

        var id = $(this).val(); //console.log('id='+id);
        if ($("input[name='radioGroup'][value='+id+']").prop("checked")) {
            $('#service_id').val(id);
        }

        if( $('#service_id').val() == 1 ){ //paid
            $('#promo_code_used').css('display','inline-block');
            $('.submitappointment_paid').show();
            $('.submitappointment').hide();
        } else { //free
            $('#promo_code_used').css('display','none');
            $('.submitappointment').show();
            $('.submitappointment_paid').hide();
        }

        if(id != ""){
            var v = 'appointment_details';
            if( id == 1 ){ //paid service
                // Show the "Zoom / Google Meeting" option
                $('select[name="appointment_details"] option[value="zoom_google_meeting"]').show();
            } else {
                // Hide the "Zoom / Google Meeting" option
                $('select[name="appointment_details"] option[value="zoom_google_meeting"]').hide();
            }
            $('.appointment_row').show();
        } else {
            var v = 'services';
            $('.appointment_row').hide();
        }
        $('.timeslots').html('');
		$('.showselecteddate').html('');

        /*var enquiry_item  = $('.enquiry_item').val(); //alert(enquiry_item);
		$.ajax({
			url:'{{URL::to('/getdatetimebackend')}}',
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			type:'POST',
			data:{id:id, enquiry_item:enquiry_item},
			datatype:'json',
			success:function(res){
				var obj = JSON.parse(res);
				if(obj.success){
                    duration = obj.duration;
					daysOfWeek =  obj.weeks;
					starttime =  obj.start_time;
					endtime =  obj.end_time;
					disabledtimeslotes = obj.disabledtimeslotes;

                    var datesForDisable = obj.disabledatesarray;

                    $('#datetimepicker').datepicker({
						inline: true,
						startDate: new Date(),
						datesDisabled: datesForDisable,
						daysOfWeekDisabled: daysOfWeek,
						format: 'dd/mm/yyyy'
					}).on('changeDate', function(e) {
                        var date = e.format();
                        var checked_date=e.date.toLocaleDateString('en-US');

                        $('.showselecteddate').html(date);
                        //$('input[name="date"]').val(date);
                        $('#timeslot_col_date').val(date);

                        $('.timeslots').html('');
                        var start_time = parseTime(starttime),
				        end_time = parseTime(endtime),
			            interval = parseInt(duration);
                        var service_id = $('input[name="service_id"]').val();

                        //var slot_overwrite_hidden = $('#slot_overwrite_hidden').val();
                        //console.log('slot_overwrite_hidden='+slot_overwrite_hidden)
                        var enquiry_item  = $('.enquiry_item').val(); //alert(enquiry_item);
                        $.ajax({
                            url:'{{URL::to('/getdisableddatetime')}}',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            type:'POST',
                            data:{service_id:service_id,sel_date:date, enquiry_item:enquiry_item},
                            datatype:'json',
                            success:function(res){
                                $('.timeslots').html('');
                                var obj = JSON.parse(res);
                                 if(obj.success){
                                    console.log('slot_overwrite_hidden='+$('#slot_overwrite_hidden').val() )
                                    if( $('#slot_overwrite_hidden').val() == 1){
                                        var objdisable = [];
                                    } else {
                                        var objdisable = obj.disabledtimeslotes;
                                    }
                                    //console.log('objdisable='+objdisable);

                                    var start_timer = start_time;
                                    for(var i = start_time; i<end_time; i = i+interval){
                                        var timeString = start_timer + interval;
                                        // Prepend any date. Use your birthday.
                                        const timeString12hr = new Date('1970-01-01T' + convertHours(start_timer) + 'Z')
                                        .toLocaleTimeString('en-US',
                                            {timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}
                                        );
                                        const timetoString12hr = new Date('1970-01-01T' + convertHours(timeString) + 'Z')
                                        .toLocaleTimeString('en-US',
                                            {timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}
                                        );

                                        var today_date = new Date();
                                        //const options = { timeZone: 'Australia/Sydney'};
                                        today_date = today_date.toLocaleDateString('en-US');

                                        // current time
                                        var now = new Date();
                                        var nowTime = new Date('1/1/1900 ' + now.toLocaleTimeString(navigator.language, {
                                            hour: '2-digit',
                                            minute: '2-digit',
                                            hour12: true
									    }));

                                        var current_time=nowTime.toLocaleTimeString('en-US');
                                        if(objdisable.length > 0){
                                            //if(jQuery.inArray(timeString12hr, objdisable) != -1  || jQuery.inArray(timetoString12hr, objdisable) != -1) { //console.log('ifff');
                                            if(jQuery.inArray(timeString12hr, objdisable) != -1  ) {

                                            } else if ((checked_date == today_date) && (current_time > timeString12hr || current_time > timetoString12hr)){ //console.log('elseee-ifff');
                                            } else{
                                                $('.timeslots').append('<div data-fromtime="'+timeString12hr+'" data-totime="'+timetoString12hr+'" style="cursor: pointer;" class="timeslot_col"><span>'+timeString12hr+'</span></div>');
                                            }
                                        } else{
                                            if((checked_date == today_date) && (current_time > timeString12hr || current_time > timetoString12hr)){
                                            } else {
                                                $('.timeslots').append('<div data-fromtime="'+timeString12hr+'" data-totime="'+timetoString12hr+'" style="cursor: pointer;" class="timeslot_col"><span>'+timeString12hr+'</span></div>');
                                            }
                                            // $('.timeslots').append('<div data-fromtime="'+timeString12hr+'" data-totime="'+timetoString12hr+'" style="cursor: pointer;" class="timeslot_col"><span>'+timeString12hr+'</span><span>'+timetoString12hr+'</span></div>');
                                        }
						                start_timer = timeString;
					                }
                                }else{

                                }
                            }
                        });
                        //	var times_ara = calculate_time_slot( start_time, end_time, interval );
                    });
                    if(id != ""){
                        var v = 'appointment_details';
                        $('#myTab .nav-item #services-tab').addClass('disabled');
                        $('#myTab .nav-item #appointment_details-tab').removeClass('disabled');
                        $('#myTab a[href="#'+v+'"]').trigger('click');
                    } else {
                        var v = 'services';
                        $('#myTab .nav-item #services-tab').removeClass('disabled');
                        $('#myTab .nav-item #appointment_details-tab').addClass('disabled');
                        $('#myTab a[href="#'+v+'"]').trigger('click');
                    }
                    $('input[name="service_id"]').val(id);
				} else {
                    $('input[name="service_id"]').val('');
                    var v = 'services';
                    alert('There is a problem in our system. please try again');
                    $('#myTab .nav-item #services-tab').removeClass('disabled');
                    $('#myTab .nav-item #appointment_details-tab').addClass('disabled');
				}
			}
		})*/
	});

    $('.slot_overwrite_time_dropdown').change(function() {
        $('#timeslot_col_time').val("");
        var currentSelVal = $(this).val();
        //console.log('currentSelVal='+currentSelVal);
        $('#timeslot_col_time').val(currentSelVal);
    });

    $('#slot_overwrite').change(function() {
        $('#timeslot_col_date').val("");
        $('#timeslot_col_time').val("");
        if ($(this).is(':checked')) { //console.log('checked');
            $('#slot_overwrite_hidden').val(1);
            $('.timeslotDivCls').hide();
            $('.slotTimeOverwriteDivCls').show();
        } else { //console.log('not-checked');
            $('#slot_overwrite_hidden').val(0);
            $('.timeslotDivCls').show();
            $('.slotTimeOverwriteDivCls').hide();
        }
    });


    $(document).delegate('.nextbtn', 'click', function(){
		var v = $(this).attr('data-steps');
		$(".custom-error").remove();
		var flag = 1;
        if(v == 'confirm'){ //datetime
            $('#sendCodeBtn_txt').html("");
            $('#sendCodeBtn_txt').hide();
			var fullname = $('.fullname').val();
			var email = $('.email').val();
			//var title = $('.title').val();
			var phone = $('.phone').val();
			var description = $('.description').val();
            var timeslot_col_date = $('#timeslot_col_date').val();
            var timeslot_col_time = $('#timeslot_col_time').val();

            //var phoneRegex = /^[0-9]{10,}$/;
            var phoneRegex = /^\+?[0-9]{1,4}[-.\s]?[0-9]{10,}$/;
            // Regular expression to allow only letters and spaces (no special characters)
            var nameRegex = /^[a-zA-Z\s]+$/;

            var appointment_item = $('.appointment_item').val();
            if( !$.trim(appointment_item) ){
                flag = 0;
                $('.appointment_item').after('<span class="custom-error" role="alert">Appointment detail is required</span>');
            }
			if( !$.trim(fullname) ){
				flag = 0;
				$('.fullname').after('<span class="custom-error" role="alert">Fullname is required</span>');
			}
            else if (!nameRegex.test(fullname)) {
                flag = 0;
                // Show error message if fullname contains special characters
                $('.fullname').after('<span class="custom-error" role="alert">Full name must not contain special characters</span>');
            }
			if( !ValidateEmail(email) ){
				flag = 0;
				if(!$.trim(email)){
					$('.email').after('<span class="custom-error" role="alert">Email is required.</span>');
				}else{
					$('.email').after('<span class="custom-error" role="alert">You have entered an invalid email address!</span>');
				}
			}

            if( !$.trim(phone) ){
				flag = 0;
				$('#sendCodeBtn').after('<span class="custom-error" role="alert">Phone number is required</span>');
			} else if (!phoneRegex.test(phone)) {
                flag = 0;
                // Show error message if phone number is not valid (less than 10 digits or contains non-digits)
                //$('.phone').after('<span class="custom-error" role="alert">Phone number must be at least 10 digits and only contain numbers</span>');
                $('#sendCodeBtn').after('<span class="custom-error" role="alert">Phone must contain extension with phone.</span>');
            } else if( $('#phone_verified_bit').val() != "1" ){
				flag = 0;
				$('#sendCodeBtn').after('<span class="custom-error" role="alert">Phone number is not verified</span>');
			}

            if( !$.trim(description) ){
				flag = 0;
				$('.description').after('<span class="custom-error" role="alert">Description is required</span>');
			}
            if( !$.trim(description) ){
				flag = 0;
				$('.description').after('<span class="custom-error" role="alert">Description is required</span>');
			}
            if( !$.trim(timeslot_col_date) || !$.trim(timeslot_col_time)  ){
				flag = 0;
				$('.timeslot_col_date_time').after('<span class="custom-error" role="alert">Date and Time is required</span>');
			}
		}/*else if(v == 'confirm'){

		}*/
        //alert('flag=='+flag+'---v=='+v);
		if(flag == 1 && v == 'confirm'){
            $('.confirm_row').show();
            $('#myTab .nav-item .nav-link').addClass('disabled');
		    $('#myTab .nav-item #'+v+'-tab').removeClass('disabled');
			$('#myTab a[href="#'+v+'"]').trigger('click');

            $('.full_name').text($('.fullname').val());
            $('.email').text($('.email').val());
            //$('.title').text($('.title').val());
            $('.phone').text($('.phone').val());
            $('.description').text($('.description').val());
            $('.date').text($('input[name="date"]').val());
            $('.time').text($('input[name="time"]').val());
            //$('.date').text($('#timeslot_col_date').val());
            //$('.time').text($('#timeslot_col_time').val());

            if(  $("input[name='radioGroup']:checked").val() == 1 ){ //paid
                $('.submitappointment_paid').show();
                $('.submitappointment').hide();
            } else { //free
                $('.submitappointment').show();
                $('.submitappointment_paid').hide();
            }
		} else {
            $('.confirm_row').hide();
        }

		function ValidateEmail(inputText) {
			var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
			if(inputText.match(mailformat)) {
			    return true;
			} else {
			    // alert("You have entered an invalid email address!");
			    return false;
			}
		}
    });

    $(document).delegate('.timeslot_col', 'click', function(){
		$('.timeslot_col').removeClass('active');
		$(this).addClass('active');
        var service_id_val = $("input[name='radioGroup']:checked").val(); //alert(service_id_val);
		var fromtime = $(this).attr('data-fromtime');
        if(service_id_val == 2){ //15 min service
            var fromtime11 = parseTimeLatest(fromtime);
            var interval11 = 15;
            var timeString11 = fromtime11 + interval11;
            var totime = new Date('1970-01-01T' + convertHours(timeString11) + 'Z')
            .toLocaleTimeString('en-US',
                {timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}
            );
        } else {
            var totime = $(this).attr('data-totime');
        }
        //alert('totime='+totime);
        $('input[name="time"]').val(fromtime+'-'+totime);
        $('#timeslot_col_time').val(fromtime+'-'+totime);
    });

    function parseTime(s) {
        var c = s.split(':');
        return parseInt(c[0]) * 60 + parseInt(c[1]);
    }

    function parseTimeLatest(s) {
        var c = s.split(':');
        var c11 = c[1].split(' ');
        if(c11[1] == 'PM'){
            if(parseInt(c[0]) != 12 ){
                return ( parseInt(c[0])+12 ) * 60 + parseInt(c[1]);
            } else {
                return parseInt(c[0]) * 60 + parseInt(c[1]);
            }
        } else {
            return parseInt(c[0]) * 60 + parseInt(c[1]);
        }
    }

    function convertHours(mins){
        var hour = Math.floor(mins/60);
        var mins = mins%60;
        var converted = pad(hour, 2)+':'+pad(mins, 2);
        return converted;
    }

    function pad (str, max) {
        str = str.toString();
        return str.length < max ? pad("0" + str, max) : str;
    }

    function calculate_time_slot(start_time, end_time, interval = "15"){
        var i, formatted_time;
        var time_slots = new Array();
        for(var i=start_time; i<=end_time; i = i+interval){
            formatted_time = convertHours(i);
            const timeString = formatted_time;

            time_slots.push(timeString);
        }
        return time_slots;
    }

    /////////////////////////////////////////////
    ////// appointment popup chnages end /////////
    /////////////////////////////////////////////


    $('.manual_email_phone_verified').on('change', function(){
        if( $(this).is(":checked") ) {
            $('.manual_email_phone_verified').val(1);
            var manual_email_phone_verified = 1;
        } else {
            $('.manual_email_phone_verified').val(0);
            var manual_email_phone_verified = 0;
        }

        var client_id = '<?php echo $fetchedData->id;?>'; //alert(site_url);
		$.ajax({
			url: site_url+'/admin/clients/update-email-verified',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			type:'POST',
			data:{manual_email_phone_verified:manual_email_phone_verified,client_id:client_id},
			success: function(responses){
                location.reload();
			}
		});
    });

    $('#feather-icon').click(function(){
        var windowsize = $(window).width(); console.log('windowsize='+windowsize);
        console.log('click'+ $('.main-sidebar').width());
        if($('.main-sidebar').width() == 65){
            if(windowsize > 2000){
                $('.add_note').css('width','980px');
            } else {
                $('.add_note').css('width','155px');
            }

        } else if($('.main-sidebar').width() == 250) {
            if(windowsize > 2000){
                $('.add_note').css('width','1040px');
            } else {
                $('.add_note').css('width','215px');
            }
        }
    });

    //set height of right side section
    var left_upper_height = $('.left_section_upper').height();
    var left_section_lower = $('.left_section_lower').height();
    var total_left  = left_upper_height + left_section_lower;
    total_left = total_left +25;

    var right_section_height = $('.right_section').height();

    //alert(left_upper_height+'==='+left_section_lower+'==='+total_left+'==='+right_section_height);
    if(right_section_height >total_left ){
        var total_left_px = total_left+'px';
        $('.right_section').css({"maxHeight":total_left_px});
        $('.right_section').css({"overflow": 'scroll' });
    } else {
        var total_left_px = total_left+'px';
        $('.right_section').css({"maxHeight":total_left_px});
    }

	let css_property =
        {
            "display": "none",
        }
	$('#create_note_d').hide();
	$('.main-footer').css(css_property);
    $(document).delegate('.uploadmail','click', function(){
		$('#maclient_id').val('{{$fetchedData->id}}');
		$('#uploadmail').modal('show');
	});
    $(document).delegate('.addnewprevvisa','click', function(){
	 var $clone = $('.multiplevisa:eq(0)').clone(true,true);

	 $clone.find('.lastfiledcol').after('<div class="col-md-4"><a href="javascript:;" class="removenewprevvisa btn btn-danger btn-sm">Remove</a></div>');
	 $clone.find("input:text").val("");
	 $clone.find("input.visadatesse").val("");
	$('.multiplevisa:last').after($clone);


	});
	 $(document).delegate('.removenewprevvisa','click', function(){
	 $(this).parent().parent().parent().remove();
	 });
    $(document).delegate('#assignUser','click', function(){
		$(".popuploader").show();
		var flag = true;
		var error ="";
		$(".custom-error").remove();
		// if($('#lead_id').val() == ''){
		// 	$('.popuploader').hide();
		// 	error="Lead field is required.";
		// 	$('#lead_id').after("<span class='custom-error' role='alert'>"+error+"</span>");
		// 	flag = false;
		// }
		if($('#rem_cat').val() == ''){
			$('.popuploader').hide();
			error="Assignee field is required.";
			$('#rem_cat').after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
		if($('#assignnote').val() == ''){
			$('.popuploader').hide();
			error="Note field is required.";
			$('#assignnote').after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
        if($('#task_group').val() == ''){
			$('.popuploader').hide();
			error="Group field is required.";
			$('#task_group').after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
		if(flag){
			$.ajax({
				type:'post',
					url:"{{URL::to('/')}}/admin/clients/followup/store",
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

					data: {note_type:'follow_up',description:$('#assignnote').val(),client_id:$('#assign_client_id').val(),followup_datetime:$('#popoverdatetime').val(),assignee_name:$('#rem_cat :selected').text(),rem_cat:$('#rem_cat option:selected').val(),task_group:$('#task_group option:selected').val()},
					success: function(response){
						$('.popuploader').hide();
						var obj = $.parseJSON(response);
						if(obj.success){
							$("[data-role=popover]").each(function(){
									(($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false  // fix for BS 3.3.6
							});
							getallactivities();
							getallnotes();
						}else{


						}
					}
			});
		}else{
			$("#loader").hide();
		}
	});
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
			$('.popuploader').hide();
			$('.note_term_list').html(responses);
		}
	});
}

function getallactivities(){
	$.ajax({
      url: site_url+'/admin/get-activities',
      type:'GET',
      datatype:'json',
      data:{id:'{{$fetchedData->id}}'},
      success: function(responses){
        var ress = JSON.parse(responses);
        var html = '';
        $.each(ress.data, function(k, v) {
          /*if(v.reciver_name != ""){
              var receiver_name =  " to <b>"+v.reciver_name+"</b> ";
            } else {
              var receiver_name =  " ";
            }*/
            //alert(receiver_name);
		 //html += '<div class="activity"><div class="activity-icon bg-primary text-white"><span>'+v.createdname+'</span></div><div class="activity-detail"><div class="activity-head"><div class="activity-title"><p><b>'+v.name+'</b> ' + receiver_name +v.subject+'</p></div><div class="activity-date"><span class="text-job">'+v.date+'</span></div></div>';

          if(v.subject != ""){
            if(v.subject === null){
              var subject =  "";
            } else {
              var subject =  v.subject;
            }
          } else {
            var subject =  "";
          }

		 //html += '<div class="activity"><div class="activity-icon bg-primary text-white"><span>'+v.createdname+'</span></div><div class="activity-detail"><div class="activity-head"><div class="activity-title"><p><b>'+v.name+'</b> '+ subject+'</p></div><div class="activity-date"><span class="text-job">'+v.date+'</span></div></div>';

          if(v.pin == 1){
            html += '<div class="activity" id="activity_'+v.activity_id+'" ><div class="activity-icon bg-primary text-white"><span>'+v.createdname+'</span></div><div class="activity-detail" style="border: 1px solid #dbdbdb;"><div class="activity-head"><div class="activity-title"><p><b>'+v.name+'</b> '+ subject+'</p></div><div class="activity-date"><span class="text-job">'+v.date+'</span></div></div><div class="right" style="float: right;margin-top: -40px;"><div class="pined_note"><i class="fa fa-thumbtack" style="font-size: 12px;color: #6777ef;"></i></div><div class="dropdown d-inline dropdown_ellipsis_icon"><a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a><div class="dropdown-menu"><a data-id="'+v.activity_id+'" data-href="deleteactivitylog" class="dropdown-item deleteactivitylog" href="javascript:;" >Delete</a><a data-id="'+v.activity_id+'"  class="dropdown-item pinactivitylog" href="javascript:;" >UnPin</a></div></div></div>';
           } else {
             html += '<div class="activity" id="activity_'+v.activity_id+'" ><div class="activity-icon bg-primary text-white"><span>'+v.createdname+'</span></div><div class="activity-detail" style="border: 1px solid #dbdbdb;"><div class="activity-head"><div class="activity-title"><p><b>'+v.name+'</b> '+ subject+'</p></div><div class="activity-date"><span class="text-job">'+v.date+'</span></div></div><div class="right" style="float: right;margin-top: -40px;"><div class="dropdown d-inline dropdown_ellipsis_icon"><a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a><div class="dropdown-menu"><a data-id="'+v.activity_id+'" data-href="deleteactivitylog" class="dropdown-item deleteactivitylog" href="javascript:;" >Delete</a><a data-id="'+v.activity_id+'"  class="dropdown-item pinactivitylog" href="javascript:;" >Pin</a></div></div></div>';
           }

          if(v.message != null){
            html += '<p>'+v.message+'</p>';
          }
          if(v.followup_date != null){
            html += '<p>'+v.followup_date+'</p>';
          }
          if(v.task_group != null){
            html += '<p>'+v.task_group+'</p>';
          }
          html += '</div></div>';
	   });

	   $('.activities').html(html);
       $('.popuploader').hide();
	}
 });
}

var appcid = '';
	$(document).delegate('.publishdoc', 'click', function(){
		$('#confirmpublishdocModal').modal('show');
		appcid = $(this).attr('data-id');

	});
$(document).delegate('.openassigneeshow', 'click', function(){
        $('.assigneeshow').show();
    });
    $(document).delegate('.closeassigneeshow', 'click', function(){
        $('.assigneeshow').hide();
    });

$(document).delegate('.saveassignee', 'click', function(){
        var appliid = $(this).attr('data-id');
		$('.popuploader').show();
		$.ajax({
			url: site_url+'/admin/clients/change_assignee',
			type:'GET',
			data:{id: appliid,assinee: $('#changeassignee').val()},
			success: function(response){

				 var obj = $.parseJSON(response);
				if(obj.status){
				    alert(obj.message);
				location.reload();

				}else{
					alert(obj.message);
				}
			}
		});
    });
$(document).delegate('#confirmpublishdocModal .acceptpublishdoc', 'click', function(){
	$('.popuploader').show();
	$.ajax({
		url: '{{URL::to('/admin/')}}/'+'application/publishdoc',
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


  	var verify_doc_id = '';
	var verify_doc_href = '';
    var verify_doc_type = '';
	$(document).delegate('.verifydoc', 'click', function(){
		$('#confirmDocModal').modal('show');
		verify_doc_id = $(this).attr('data-id');
		verify_doc_href = $(this).attr('data-href');
        verify_doc_type = $(this).attr('data-doctype');
	});

	$(document).delegate('#confirmDocModal .accept', 'click', function(){
        $('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/')}}/'+verify_doc_href,
			type:'POST',
			datatype:'json',
			data:{doc_id:verify_doc_id, doc_type:verify_doc_type },
			success:function(response){
				$('.popuploader').hide();
				var res = JSON.parse(response);
				$('#confirmDocModal').modal('hide');
				if(res.status){
                    if(res.doc_type == 'documents') {
                        $('.alldocumnetlist #docverifiedby_'+verify_doc_id).html(res.verified_by + "<br>" + res.verified_at);
                        //$('.alldocumnetlist #docverifiedat_'+verify_doc_id).html(res.verified_at);
                    }
                    getallactivities();
				}
			}
		});
	});


	var notuse_doc_id = '';
	var notuse_doc_href = '';
    var notuse_doc_type = '';
    $(document).delegate('.notuseddoc', 'click', function(){
		$('#confirmNotUseDocModal').modal('show');
		notuse_doc_id = $(this).attr('data-id');
		notuse_doc_href = $(this).attr('data-href');
        notuse_doc_type = $(this).attr('data-doctype');
	});

	$(document).delegate('#confirmNotUseDocModal .accept', 'click', function(){
        $('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/')}}/'+notuse_doc_href,
			type:'POST',
			datatype:'json',
			data:{doc_id:notuse_doc_id, doc_type:notuse_doc_type },
			success:function(response){
				$('.popuploader').hide();
				var res = JSON.parse(response);
				$('#confirmNotUseDocModal').modal('hide');
				if(res.status){
                    if(res.doc_type == 'documents') {
                        $('.alldocumnetlist #id_'+res.doc_id).remove();
                    }
                    //location.reload();
                    if(res.docInfo) {
                        var subArray = res.docInfo;
                        var trRow = "";
                        if(subArray.myfile_key != ''){ //For new file upload
                            trRow += "<tr class='drow' id='id_"+subArray.id+"'><td>"+subArray.checklist+"</td><td>"+ res.Added_By + "<br>" + res.Added_date+"</td><td><a target='_blank' class='dropdown-item' href='"+subArray.myfile+"'><i class='fas fa-file-image'></i> <span>"+subArray.file_name+'.'+subArray.filetype+"</span></a></div></td><td>"+res.Verified_By+ "<br>" +res.Verified_At+"</td></tr>";
                        } else {
                            trRow += "<tr class='drow' id='id_"+subArray.id+"'><td>"+subArray.checklist+"</td><td>"+ res.Added_By + "<br>" + res.Added_date+"</td><td><i class='fas fa-file-image'></i> <span>"+subArray.file_name+'.'+subArray.filetype+"</span></div></td><td>"+res.Verified_By+ "<br>" +res.Verified_At+"</td></tr>";
                        }

                        $('.notuseddocumnetlist').append(trRow);
                    }
                    getallactivities();
				}
			}
		});
	});



    var backto_doc_id = '';
	var backto_doc_href = '';
    var backto_doc_type = '';
    $(document).delegate('.backtodoc', 'click', function(){
		$('#confirmBackToDocModal').modal('show');
		backto_doc_id = $(this).attr('data-id');
		backto_doc_href = $(this).attr('data-href');
        backto_doc_type = $(this).attr('data-doctype');
	});

	$(document).delegate('#confirmBackToDocModal .accept', 'click', function(){
        $('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/')}}/'+backto_doc_href,
			type:'POST',
			datatype:'json',
			data:{doc_id:backto_doc_id, doc_type:backto_doc_type },
			success:function(response){
				$('.popuploader').hide();
				var res = JSON.parse(response);
				$('#confirmBackToDocModal').modal('hide');
				if(res.status){
                    if(res.doc_type == 'documents') {
                        $('.notuseddocumnetlist #id_'+res.doc_id).remove();
                    }
                    location.reload();
                    /*if(res.docInfo) {
                        var subArray = res.docInfo;
                        var trRow = "";
                        trRow += "<tr class='drow' id='id_"+subArray.id+"'><td>"+subArray.checklist+"</td><td>"+ res.Added_By + "<br>" + res.Added_date+"</td><td><i class='fas fa-file-image'></i> <span>"+subArray.file_name+'.'+subArray.filetype+"</span></div></td><td>"+res.Verified_By+ "<br>" +res.Verified_At+"</td></tr>";
                        $('.notuseddocumnetlist').append(trRow);
                    }*/
                    getallactivities();
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
					if(res.status == true){
						$('#id_'+notid).remove();
					}

					if(delhref == 'deletedocs'){
						$('.documnetlist #id_'+notid).remove();

					}
                    if(delhref == 'deletealldocs'){
						$('.alldocumnetlist #id_'+notid).remove();
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
					}else if(delhref == 'superagent'){
						$('.supagent_data').html('');
					}else if(delhref == 'subagent'){
						$('.subagent_data').html('');
					}else if(delhref == 'deleteappointment'){
						$.ajax({
						url: site_url+'/admin/get-appointments',
						type:'GET',
						data:{clientid:'{{$fetchedData->id}}'},
						success: function(responses){

							$('.appointmentlist').html(responses);
						}
					});
					}else if(delhref == 'deletepaymentschedule'){
						$.ajax({
						url: site_url+'/admin/get-all-paymentschedules',
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

                       if(res.application_id){
                            $.ajax({
                                url: site_url+'/admin/get-applications-logs',
                                type:'GET',
                                data:{id: res.application_id},
                                success: function(responses){
                                    $('#accordion').html(responses);
                                }
                            });
                        }

					}else{
						getallnotes();
					}

					getallactivities();
				}
			}
		});
	});


    var activitylogid = '';
	var delloghref = '';
	$(document).delegate('.deleteactivitylog', 'click', function(){
		$('#confirmLogModal').modal('show');
		activitylogid = $(this).attr('data-id');
		delloghref = $(this).attr('data-href');
	});

	$(document).delegate('#confirmLogModal .accept', 'click', function(){
        $('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/')}}/'+delloghref,
			type:'GET',
			datatype:'json',
			data:{activitylogid:activitylogid},
			success:function(response){
				//$('.popuploader').hide();
				var res = JSON.parse(response);
				$('#confirmLogModal').modal('hide');
                //location.reload();
				if(res.status){
					$('#activity_'+activitylogid).remove();
					if(res.status == true){
						$('#activity_'+activitylogid).remove();
					}
                    getallactivities();
				}
			}
		});
	});


	$(document).delegate('.pinnote', 'click', function(){
		$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/pinnote')}}/',
			type:'GET',
			datatype:'json',
			data:{note_id:$(this).attr('data-id')},
			success:function(response){
				getallnotes();
			}
		});
	});

    //Pin activity log click
    $(document).delegate('.pinactivitylog', 'click', function(){
        $('.popuploader').show();
        $.ajax({
            url: '{{URL::to('/admin/pinactivitylog')}}/',
            type:'GET',
            datatype:'json',
            data:{activity_id:$(this).attr('data-id')},
            success:function(response){
                getallactivities();
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
	$(document).delegate('.create_note_d', 'click', function(){

		$('#create_note_d').modal('show');
		$('#create_note_d input[name="mailid"]').val(0);

		//$('#create_note input[name="title"]').val('');
		$('#create_note_d #appliationModalLabel').html('Create Note');
		// alert('yes');
	//	$('#create_note input[name="title"]').val('');
				//	$("#create_note .summernote-simple").val('');
				//	$('#create_note input[name="noteid"]').val('');
			//	$("#create_note .summernote-simple").summernote('code','');

		if($(this).attr('datatype') == 'note'){
			$('.is_not_note').hide();
		}else{
		var datasubject = $(this).attr('datasubject');
		var datamailid = $(this).attr('datamailid');
			$('#create_note_d input[name="title"]').val(datasubject);
			$('#create_note_d input[name="mailid"]').val(datamailid);
			$('.is_not_note').show();
		}
	});


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

            //Fetch all contact list of any client at create note popup
            var client_id = $('#client_id').val();
            $('.popuploader').show();
            $.ajax({
                url: "{{URL::to('/admin/clients/fetchClientContactNo')}}",
                method: "POST",
                data: {client_id:client_id},
                datatype: 'json',
                success: function(response) {
                    $('.popuploader').hide();
                    var obj = $.parseJSON(response); //console.log(obj.clientContacts);
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

            // Add validation for only digits and max 10 digits
            /*$("#mobileNumber").on("input", function() {
                var mobileNumber = $(this).val();
                var digitOnly = /^[0-9]*$/;

                if (!digitOnly.test(mobileNumber)) {
                    $("#mobileNumberError").text("Please enter only digits.");
                } else if (mobileNumber.length > 10) {
                    $("#mobileNumberError").text("Mobile number cannot exceed 10 digits.");
                } else {
                    $("#mobileNumberError").text("");
                }
            });*/
        }
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

$('.js-data-example-ajaxccapp').select2({
		 multiple: true,
		 closeOnSelect: false,
		dropdownParent: $('#applicationemailmodal'),
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


//Function is used for complete the session
$(document).delegate('.complete_session', 'click', function(){
    var client_id = $(this).attr('data-clientid'); //alert(client_id);
    if(client_id !=""){
        $.ajax({
            type:'post',
            url:"{{URL::to('/')}}/admin/clients/update-session-completed",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {client_id:client_id },
            success: function(response){
                //console.log(response);
                var obj = $.parseJSON(response);
                location.reload();
            }
        });
    }
});

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

	$(document).delegate('.viewapplicationnote', 'click', function(){
		$('#view_application_note').modal('show');
		var v = $(this).attr('data-id');
		$('#view_application_note input[name="noteid"]').val(v);
			$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/viewapplicationnote')}}',
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


  //Send message
    $(document).delegate('.sendmsg', 'click', function(){
        $('#sendmsgmodal').modal('show');
        var client_id = $(this).attr('data-id');
        $('#sendmsg_client_id').val(client_id);
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
    var client_id = $(this).data('clientid'); //alert(client_id);
    var client_firstname = $(this).data('clientfirstname'); //alert(client_firstname);
    if (client_firstname) {
        client_firstname = client_firstname.charAt(0).toUpperCase() + client_firstname.slice(1);
    }
    var client_reference_number = $(this).data('clientreference_number'); //alert(client_reference_number);
    var company_name = 'Bansal Education Group';
    var visa_valid_upto = $(this).data('clientvisaExpiry');
    if ( visa_valid_upto != '' && visa_valid_upto != '0000-00-00') {
        visa_valid_upto = visa_valid_upto;
    } else {
        visa_valid_upto = '';
    }
  
  
    var clientassignee_name = $(this).data('clientassignee_name');
    if ( clientassignee_name != '') {
        clientassignee_name = clientassignee_name;
    } else {
        clientassignee_name = '';
    }

  
	var v = $(this).val();
	$.ajax({
		url: '{{URL::to('/admin/get-templates')}}',
		type:'GET',
		datatype:'json',
		data:{id:v},
		success: function(response){
			var res = JSON.parse(response);

            // Replace {Client First Name} with actual client name
            //var subjct_message = res.subject
            //.replace('{Client First Name}', client_firstname)
            //.replace(/Ref:\s*\.{1,}\s*/, 'Ref: ' + client_reference_number)
            //.replace(/Ref_\s*-{1,}\s*/, 'Ref_' + client_reference_number);
      		var subjct_message = res.subject.replace('{Client First Name}', client_firstname).replace('{client reference}', client_reference_number);
            $('.selectedsubject').val(subjct_message);
      
           
             $("#emailmodal .summernote-simple").summernote('reset');
            //$("#emailmodal .summernote-simple").summernote('code', res.description);
            //$("#emailmodal .summernote-simple").val(res.description);
            //var subjct_description = res.description.replace('{Client First Name}', client_firstname);

            //var subjct_description = res.description
           // .replace(/Dear\s*\.{2,}\s*/, 'Dear ' + client_firstname)
            //.replace('{Client First Name}', client_firstname)
            //.replace('{Company Name}', company_name)
            //.replace('{Visa Valid Upto}', visa_valid_upto)
            //.replace('{Client Assignee Name}', clientassignee_name)
            //.replace(/Reference:\s*\.{2,}\s*/, 'Reference: ' + client_reference_number)
            //.replace('{client reference}', client_reference_number);
      
      		var subjct_description = res.description
            .replace('{Client First Name}', client_firstname)
            .replace('{Company Name}', company_name)
            .replace('{Visa Valid Upto}', visa_valid_upto)
            .replace('{Client Assignee Name}', clientassignee_name)
            .replace('{client reference}', client_reference_number);
      
            $("#emailmodal .summernote-simple").summernote('code', subjct_description);
            $("#emailmodal .summernote-simple").val(subjct_description);

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

$('.js-data-example-ajaxccd').select2({
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

/* $(".table-2").dataTable({
	"searching": false,
	"lengthChange": false,
  "columnDefs": [
    { "sortable": false, "targets": [0, 2, 3] }
  ],
  order: [[1, "desc"]] //column indexes is zero based

}); */
  
//$('#mychecklist-datatable').dataTable({"searching": true,});
  
// Initialize DataTable for the checklist table
let selectedChecklists = [];

let checklistTable = $('#mychecklist-datatable').DataTable({
    "paging": true,
    "pageLength": 10,
    "searching": true,
    "ordering": true,
    "info": true,
    "dom": 'lfrtip',
    "drawCallback": function(settings) {
        let api = this.api();
        api.rows().every(function() {
            let row = this.node();
            let checkbox = $(row).find('input[name="checklistfile[]"]');
            let checklistId = checkbox.val();
            if (selectedChecklists.includes(checklistId)) {
                checkbox.prop('checked', true);
            } else {
                checkbox.prop('checked', false);
            }
        });
    }
});

$(document).on('change', 'input[name="checklistfile[]"]', function() {
    let checklistId = $(this).val();
    if ($(this).is(':checked')) {
        if (!selectedChecklists.includes(checklistId)) {
            selectedChecklists.push(checklistId);
        }
    } else {
        selectedChecklists = selectedChecklists.filter(id => id !== checklistId);
    }
    console.log('Selected Checklists:', selectedChecklists);
});

/*$('form[name="sendmail"]').on('submit', function(e) {
    let form = $(this);
    form.find('input[type="hidden"][name="checklistfile[]"]').remove();

    selectedChecklists.forEach(checklistId => {
        let checkbox = $('input[name="checklistfile[]"][value="' + checklistId + '"]');
        let row = checkbox.closest('tr');
        if (!row.is(':visible')) {
            let hiddenInput = $('<input>').attr({
                type: 'hidden',
                name: 'checklistfile[]',
                value: checklistId
            });
            form.append(hiddenInput);
        }
    });

    console.log('Final Selected Checklists on Submit:', selectedChecklists);
});*/
  
//$('#mydocumentlist-datatable').dataTable({"searching": true,});
// Initialize DataTable for the checklist table
let selectedDocChecklists = [];

let docchecklistTable = $('#mydocumentlist-datatable').DataTable({
    "paging": true,
    "pageLength": 10,
    "searching": true,
    "ordering": true,
    "info": true,
    "dom": 'lfrtip',
    "drawCallback": function(settings) {
        let api = this.api();
        api.rows().every(function() {
            let row = this.node();
            let doccheckbox = $(row).find('input[name="checklistfile_document[]"]');
            let docchecklistId = doccheckbox.val();
            if (selectedDocChecklists.includes(docchecklistId)) {
                doccheckbox.prop('checked', true);
            } else {
                doccheckbox.prop('checked', false);
            }
        });
    }
});

$(document).on('change', 'input[name="checklistfile_document[]"]', function() {
    let docchecklistId = $(this).val();
    if ($(this).is(':checked')) {
        if (!selectedDocChecklists.includes(docchecklistId)) {
            selectedDocChecklists.push(docchecklistId);
        }
    } else {
        selectedDocChecklists = selectedDocChecklists.filter(id => id !== docchecklistId);
    }
    console.log('Selected Doc Checklists:', selectedDocChecklists);
});

$('form[name="sendmail"]').on('submit', function(e) {
    let form = $(this);
    form.find('input[type="hidden"][name="checklistfile[]"]').remove();
	form.find('input[type="hidden"][name="checklistfile_document[]"]').remove();

    selectedChecklists.forEach(checklistId => {
        let checkbox = $('input[name="checklistfile[]"][value="' + checklistId + '"]');
        let row = checkbox.closest('tr');
        if (!row.is(':visible')) {
            let hiddenInput = $('<input>').attr({
                type: 'hidden',
                name: 'checklistfile[]',
                value: checklistId
            });
            form.append(hiddenInput);
        }
    });

	selectedDocChecklists.forEach(checkdoclistId => {
        let doccheckbox = $('input[name="checklistfile_document[]"][value="' + checkdoclistId + '"]');
        let row1 = doccheckbox.closest('tr');
        if (!row1.is(':visible')) {
            let hiddenInput1 = $('<input>').attr({
                type: 'hidden',
                name: 'checklistfile_document[]',
                value: checkdoclistId
            });
            form.append(hiddenInput1);
        }
    });

    console.log('Final Selected Checklists on Submit:', selectedChecklists);
	console.log('Final Document Selected Checklists on Submit:', selectedDocChecklists);
});
  
  
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

	$(document).delegate('#edit_intrested_workflow', 'change', function(){

				var v = $('#edit_intrested_workflow option:selected').val();

				if(v != ''){
						$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getpartner')}}',
			type:'GET',
			data:{cat_id:v},
			success:function(response){
				$('.popuploader').hide();
				$('#edit_intrested_partner').html(response);

				$("#edit_intrested_partner").val('').trigger('change');
			$("#edit_intrested_product").val('').trigger('change');
			$("#edit_intrested_branch").val('').trigger('change');
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
	$(document).delegate('#edit_intrested_partner','change', function(){

				var v = $('#edit_intrested_partner option:selected').val();
				if(v != ''){
					$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getproduct')}}',
			type:'GET',
			data:{cat_id:v},
			success:function(response){
				$('.popuploader').hide();
				$('#edit_intrested_product').html(response);
				$("#edit_intrested_product").val('').trigger('change');
			$("#edit_intrested_branch").val('').trigger('change');
			}
		});
				}
	});

	$(document).delegate('#intrested_product','change', function(){

				var v = $('#intrested_product option:selected').val();
				if(v != ''){
					$('.popuploader').show();
		$.ajax({
			url: '{{URL::to('/admin/getbranch')}}',
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
			url: site_url+'/admin/upload-document',
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
$(document).delegate('.migdocupload', 'click', function() {
    $(this).attr("value", "");
});

$(document).delegate('.migdocupload', 'change', function() {
	$('.popuploader').show();
    var formData = new FormData($('#mig_upload_form')[0]);
	$.ajax({
		url: site_url+'/admin/upload-document',
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
            	$('.migdocumnetlist').html(ress.data);
            	$('.miggriddata').html(ress.griddata);
            }else{
                 $('.custom-error-msg').html('<span class="alert alert-danger">'+ress.message+'</span>');
            }
				getallactivities();
			}
	});
});

  //All Document Upload
    $(document).delegate('.add_alldocument_doc', 'click', function () {
        $('.create_alldocument_docs').modal('show');
        $("#checklist").select2({dropdownParent: $(".create_alldocument_docs")});
    });

    $(document).delegate('.alldocupload', 'click', function() {
        $(this).attr("value", "");
    })

	$(document).delegate('.alldocupload', 'change', function() {
		$('.popuploader').show();
        var fileidL = $(this).attr("data-fileid");
        console.log('fileidL='+fileidL);
        var formData = new FormData($('#upload_form_'+fileidL)[0]);
        //console.log(formData);
		$.ajax({
			url: site_url+'/admin/upload-alldocument',
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
                    $('.alldocumnetlist').html(ress.data);
                    $('.allgriddata').html(ress.griddata);
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
			url: '{{URL::to('/admin/convertapplication')}}',
			type:'GET',
			data:{cat_id:v,clientid:'{{$fetchedData->id}}'},
			success:function(response){

				$.ajax({
					url: site_url+'/admin/get-services',
					type:'GET',
					data:{clientid:'{{$fetchedData->id}}'},
					success: function(responses){

						$('.interest_serv_list').html(responses);
					}
				});
				$.ajax({
					url: site_url+'/admin/get-application-lists',
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
					url: site_url+'/admin/get-application-lists',
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

	$(document).on('click', '.migdocumnetlist .renamedoc', function () {
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

		$(document).on('click', '.migdocumnetlist .drow .btn-danger', function () {
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
			   url: '{{URL::to('/admin/renamedoc')}}',
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

		$(document).delegate('.migdocumnetlist .drow .btn-primary', 'click', function () {

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
			   url: '{{URL::to('/admin/renamedoc')}}',
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


  		//Rename File Name For All Documents
    $(document).on('click', '.alldocumnetlist .renamealldoc', function () {
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

    $(document).on('click', '.alldocumnetlist .drow .btn-danger', function () {
        var parent = $(this).closest('.drow').find('.doc-row');
        var hourid = parent.data('id');
        if (hourid) {
            parent.html(parent.data('current-html'));
        } else {
            parent.remove();
        }
    });

    $(document).delegate('.alldocumnetlist .drow .btn-primary', 'click', function () {
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
            url: '{{URL::to('/admin/renamealldoc')}}',
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

    //Rename Checklist Name For All Documents
    $(document).on('click', '.alldocumnetlist .renamechecklist', function () {
        var parent = $(this).closest('.drow').find('.personalchecklist-row');;
        parent.data('current-html', parent.html());
        var opentime = parent.data('personalchecklistname');
        parent.empty().append(
            $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
            $('<button class="btn btn-personalprimary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
            $('<button class="btn btn-personaldanger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
        );
        return false;
    });

    $(document).on('click', '.alldocumnetlist .drow .btn-personaldanger', function () {
        var parent = $(this).closest('.drow').find('.personalchecklist-row');
        var hourid = parent.data('id');
        if (hourid) {
            parent.html(parent.data('current-html'));
        } else {
            parent.remove();
        }
    });

    $(document).delegate('.alldocumnetlist .drow .btn-personalprimary', 'click', function () {
        var parent = $(this).closest('.drow').find('.personalchecklist-row');
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
            data: {"_token": $('meta[name="csrf-token"]').attr('content'),"checklist": opentime, "id": parent.data('id')},
            url: '{{URL::to('/admin/renamechecklistdoc')}}',
            success: function(result){
                var obj = JSON.parse(result);
                if (obj.status) {
                    parent.empty()
                        .data('id', obj.Id)
                        .data('name', opentime)
                        .append(
                            $('<span>').html(obj.checklist)
                        );
                        $('#grid_'+obj.Id).html(obj.checklist);
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
     $(".timezoneselects2").select2({
    dropdownParent: $("#edit_appointment")
  });

   $(".invitesselects2").select2({
    dropdownParent: $("#edit_appointment")
  });
			}
		});
	});
	$(".applicationselect2").select2({
    dropdownParent: $(".add_appliation")
  });
   $(".partner_branchselect2").select2({
    dropdownParent: $(".add_appliation")
  });
  $(".approductselect2").select2({
    dropdownParent: $(".add_appliation")
  });
	 $(".workflowselect2").select2({
    dropdownParent: $(".add_interested_service")
  });
  $(".partnerselect2").select2({
    dropdownParent: $(".add_interested_service")
  });
   $(".productselect2").select2({
    dropdownParent: $(".add_interested_service")
  });
  $(".branchselect2").select2({
    dropdownParent: $(".add_interested_service")
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
					 $(".workflowselect2").select2({
    dropdownParent: $("#eidt_interested_service")
  });
  $(".partnerselect2").select2({
    dropdownParent: $("#eidt_interested_service")
  });
   $(".productselect2").select2({
    dropdownParent: $("#eidt_interested_service")
  });
  $(".branchselect2").select2({
    dropdownParent: $("#eidt_interested_service")
  });
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


    //Account Tab Receipt Popup
    $(document).delegate('.createclientreceipt', 'click', function(){
        getTopReceiptValInDB(1);
       $('#function_type').val("add");
        $('#createclientreceiptmodal').modal('show');
    });

    $('#createclientreceiptmodal').on('show.bs.modal', function() {
        $('.modal-dialog').css('max-width', '80%');
    });

     $(document).delegate('.updateclientreceipt', 'click', function(){
        var id = $(this).data('id');
        //console.log('id'+id);
        getClientReceiptInfoById(id);
    });

    function getClientReceiptInfoById(id) {
        $.ajax({
            type:'post',
            url: '{{URL::to('/admin/clients/getClientReceiptInfoById')}}',
            sync:true,
            data: {id:id},
            success: function(response){
                var obj = $.parseJSON(response); //console.log('record_get=='+obj.record_get);

                if(obj.status){
                    $('#top_value_db').val(obj.last_record_id);

                    $('#function_type').val("edit");
                    $('#createclientreceiptmodal').modal('show');
                    if(obj.record_get){
                        var record_get = obj.record_get;
                        //var trRows_client = "";
                        var sum = 0;
                        $('.productitem tr.clonedrow').remove();
                        $('.productitem tr.product_field_clone').remove();
                        $.each(record_get, function(index, subArray) {
                            //console.log('index=='+index);
                            var value_sum = parseFloat(subArray.deposit_amount);
                            if (!isNaN(value_sum)) {
                                sum += value_sum;
                            }
                            if(index <1 ){
                                var rowCls = 'clonedrow';
                            } else {
                                var rowCls = 'product_field_clone';
                            }
                            var trRows_client = '<tr class="'+rowCls+'"><td><input name="id[]" type="hidden" value="'+subArray.id+'" /><input data-valid="required" class="form-control report_date_fields" name="trans_date[]" type="text" value="'+subArray.trans_date+'" /></td><td><input data-valid="required" class="form-control report_entry_date_fields" name="entry_date[]" type="text" value="'+subArray.entry_date+'" /></td><td><input class="form-control unique_trans_no" type="text" value="'+subArray.trans_no+'" readonly/><input class="unique_trans_no_hidden" name="trans_no[]" type="hidden" value="'+subArray.trans_no+'" /></td><td><select class="form-control payment_method_cls" name="payment_method[]"><option value="">Select</option><option value="Cash">Cash</option><option value="Bank transfer">Bank transfer</option><option value="EFTPOS">EFTPOS</option></select></td><td><input data-valid="required" class="form-control" name="description[]" type="text" value="'+subArray.description+'" /></td><td><span class="currencyinput" style="display: inline-block;">$</span><input data-valid="required" style="display: inline-block;" class="form-control deposit_amount_per_row" name="deposit_amount[]" type="text" value="'+subArray.deposit_amount+'" /></td><td><a class="removeitems" href="javascript:;"><i class="fa fa-times"></i></a></td></tr>';
                            $('.productitem').append(trRows_client);

                            $('.productitem tr:last .payment_method_cls').val(subArray.payment_method);
                            $('.report_date_fields').last().datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true });
                            $('.report_entry_date_fields').last().datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true });

                            if(index <1 ){
                                //$('#sel_invoice_agent_id').val(subArray.agent_id).trigger('change');

                               // $('.invoice_no').val(subArray.invoice_no);
                                //$('.unique_invoice_no').text(subArray.invoice_no);

                                $('#receipt_id').val(subArray.receipt_id);
                            }
                        });
                        $('.total_deposit_amount_all_rows').text("$"+sum.toFixed(2));
                    }
                }
            }
        });
    }

    //On Close Hide all content from popups
    $('#createclientreceiptmodal').on('hidden.bs.modal', function() {
        $('#create_client_receipt')[0].reset();
        $('.total_deposit_amount_all_rows').text("");
        $('#sel_client_agent_id').val("").trigger('change');
        $('.report_entry_date_fields').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());
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
					$('#popuploader').show();
					$.ajax({
						url:"{{URL::to('/admin/application/updateintake')}}",
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
						url:"{{URL::to('/admin/application/updateexpectwin')}}",
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
						url:"{{URL::to('/admin/application/updatedates')}}",
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
						url:"{{URL::to('/admin/application/updatedates')}}",
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

$(document).delegate('.refund_application', 'click', function(){
	var appliid = $(this).attr('data-id');
	$('#refund_application').modal('show');
	$('input[name="reapp_id"]').val(appliid);
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
					$('#popuploader').show();
					$.ajax({
						url:"{{URL::to('/admin/application/updateintake')}}",
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
						url:"{{URL::to('/admin/application/updateexpectwin')}}",
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
						url:"{{URL::to('/admin/application/updatedates')}}",
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
						url:"{{URL::to('/admin/application/updatedates')}}",
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

    const activeTab = localStorage.getItem('activeTab');
    const appliid = localStorage.getItem('appliid');

    //console.log('activeTab='+activeTab);
    //console.log('appliid='+appliid);

    if (activeTab === 'application' && appliid != "") {
        // Remove 'active' class from all tabs
        $('#client_tabs .nav-link').removeClass('active');
        $('.tab-content .tab-pane').removeClass('active show');

        // Add 'active' class to the "Applications" tab
        $('#application-tab').addClass('active');
        $('#application').addClass('active show');

        // Programmatically trigger the .openapplicationdetail click
        const $applicationDetailButton = $('<button>').addClass('openapplicationdetail').attr('data-id', appliid).hide(); // Create a hidden button
        $('body').append($applicationDetailButton); // Append to the DOM temporarily
        $applicationDetailButton.trigger('click'); // Trigger the click
        $applicationDetailButton.remove(); // Remove the temporary button



        // Optionally, clear localStorage after the click to avoid repeating the action
        localStorage.removeItem('activeTab');
        localStorage.removeItem('appliid');
    }


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


//application stages assign user
$(document).delegate('.openappaction', 'click', function(){
	var assign_application_id = $(this).attr('data-id');
    $('#create_applicationaction #assign_application_id').val(assign_application_id);

	var stage_name = $(this).attr('data-app-type');
    $('#create_applicationaction #stage_name').val(stage_name);
    $('#create_applicationaction #stage_name_f').html(stage_name);

    var course = $(this).attr('data-course');
    $('#create_applicationaction #course_s').html(course);
    $('#create_applicationaction #course').val(course);

    var school = $(this).attr('data-school');
    $('#create_applicationaction #school_s').html(school);
    $('#create_applicationaction #school').val(school);

	$('#create_applicationaction').modal('show');
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
			url: '{{URL::to('/admin/addscheduleinvoicedetail')}}',
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

/*$(document).delegate('.opendocnote', 'click', function(){
	var id = '';
	var type = $(this).attr('data-app-type');
	var aid = $(this).attr('data-id');
	$(".checklisttype").val(type);
	$(".checklistid").val(id);
	$(".application_id").val(aid);
	$('#openfileuploadmodal').modal('show');
});*/

$(document).delegate('.opendocnote', 'click', function(){
	var id = '';
	var type = $(this).attr('data-app-type');
	var aid = $(this).attr('data-id');
    var app_doc_client_id = $(this).attr('data-appdocclientid');
	$(".checklisttype").val(type);

    var typename = $(this).attr('data-typename');
    $(".checklisttypename").val(typename);

	$(".checklistid").val(id);
	$(".application_id").val(aid);
    $(".app_doc_client_id").val(app_doc_client_id);
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
		url: '{{URL::to('/admin/updatestage')}}',
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
					url: site_url+'/admin/get-applications-logs',
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
		url: '{{URL::to('/admin/completestage')}}',
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
						url: site_url+'/admin/get-applications-logs',
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
			url: '{{URL::to('/admin/updatebackstage')}}',
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
						url: site_url+'/admin/get-applications-logs',
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
			url: '{{URL::to('/admin/getapplicationnotes')}}',
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
  	 $(".Inviteesselects2").select2({
    dropdownParent: $("#create_appoint")
  });
  $(".assignee").select2({
    dropdownParent: $("#opentaskmodal")
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
		    tags: true,
					dropdownParent: $("#tags_clients .modal-content")
				});
	});

    $(document).delegate('.serviceTaken','click', function(){
		$('#serviceTaken').modal('show');
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
        var partnerid = $(this).attr('data-partnerid');
		$('.popuploader').show();
		$('#new_fee_option').modal('show');
		$.ajax({
			url: '{{URL::to('/admin/showproductfee')}}',
			type:'GET',
			data:{id:appliid,partnerid:partnerid},
			success:function(response){
				$('.popuploader').hide();
                $('.showproductfee').html(response);
            }
		});
	});


    $(document).delegate('.openpaymentfeeLatest', 'click', function(){
		var appliid = $(this).attr('data-id');
		$('.popuploader').show();
         /*$('#new_fee_option_latest .modal-dialog').css({
            'max-width': '100%',
            'margin': 'auto'
        });*/
		$('#new_fee_option_latest').modal('show');
		$.ajax({
			url: '{{URL::to('/admin/showproductfeelatest')}}',
			type:'GET',
			data:{id:appliid},
			success:function(response){
				$('.popuploader').hide();
                $('.showproductfee_latest').html(response);
                $('.date_paid').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true });
            }
		});
	});

	$(document).delegate('.openpaymentfeeserv', 'click', function(){
		var appliid = $(this).attr('data-id');
		$('.popuploader').show();
		$('#interest_service_view').modal('hide');
		$('#new_fee_option_serv').modal('show');
		$.ajax({
			url: '{{URL::to('/admin/showproductfeeserv')}}',
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

  $(document).delegate('#new_fee_option_latest .fee_option_addbtn_latest a', 'click', function(){
		//var html = '<tr class="add_fee_option cus_fee_option"><td><input type="text" data-valid="required" value="" class="form-control date_paid" name="date_paid[]"><input type="hidden" value="2" name="fee_option_type[]"></td><td><input type="number" data-valid="required" value="" class="form-control total_fee_am_2nd" name="total_fee[]"></td><td><input type="number" data-valid="required" value="" class="form-control commission_cal" name="commission[]"></td><td><input type="text" value="" class="form-control commission_earned" readonly><input type="text" value="" class="form-control commission_earned_hidden" name="commission_earned[]"></td><td><input type="number" data-valid="required" value="" class="form-control adjustment_discount_entry" name="adjustment_discount_entry[]"></td><td><input type="text" value="" class="form-control commission_claimed" readonly><input type="text" value="" class="form-control commission_claimed_hidden" name="commission_claimed[]"></td><td><select class="form-control" data-valid="required"  name="claimed_or_not[]" ><option value="">Select</option><option value="Yes">Yes</option><option value="No">No</option></select></td><td><select class="form-control" data-valid="required"  name="source[]" ><option value="">Select</option><option value="Prededuct">Prededuct</option><option value="Reported by college">Reported by college</option><option value="Calculated by us">Calculated by us</option><option value="Told by student">Told by student</option></select></td></tr>';
        var html = '<tr class="add_fee_option cus_fee_option"><td><input type="text" data-valid="required" value="" class="form-control date_paid" name="date_paid[]"><input type="hidden" value="2" name="fee_option_type[]"></td><td><input type="number" data-valid="required" value="" class="form-control total_fee_am_2nd" name="total_fee[]"></td><td><input type="number" data-valid="required" value="" class="form-control commission_percentage" name="commission_percentage[]"></td><td><input type="text" value="" class="form-control commission_cal" readonly><input type="hidden" value="" class="form-control commission_cal_hidden" name="commission[]"></td><td><input type="number" data-valid="required" value="" class="form-control adjustment_discount_entry" name="adjustment_discount_entry[]"></td><td><input type="text" value="" class="form-control commission_claimed" readonly><input type="hidden" value="" class="form-control commission_claimed_hidden" name="commission_claimed[]"></td><td><select class="form-control" data-valid="required"  name="claimed_or_not[]" ><option value="">Select</option><option value="Yes">Yes</option><option value="No">No</option><option value="Anticipated">Anticipated</option></select></td><td><select class="form-control" data-valid="required"  name="source[]" ><option value="">Select</option><option value="Prededuct">Prededuct</option><option value="Reported by college">Reported by college</option><option value="Calculated by us">Calculated by us</option><option value="Told by student">Told by student</option><option value="Bonus">Bonus</option></select></td></tr>';
        $('#new_fee_option_latest #productitemviewlatest tbody').append(html);
        $('.commission_percentage').last().val( $('#commission_percentage').val());
        $('.date_paid').last().datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true });
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
			url: '{{URL::to('/admin/scheduleinvoicedetail')}}',
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


		//////////////////////////////////////////////////////
    //////////////////////////////////////////////////////
    /////////Start 1st popup - Fee Option ///////////
    //////////////////////////////////////////////////////
    //////////////////////////////////////////////////////

    //Calculate Tution Fee => on blur of Total Course Fee, Scholarship Fee, Enrolment Fee, Material fees
    $(document).delegate('.total_fee_am','blur', function(){
        /*var commission_percentage = $('#commission_percentage').val();
        if(commission_percentage >0){
            commission_percentage = commission_percentage;
        } else {
            commission_percentage = "0.00";
        }*/
        //console.log('commission_percentage='+commission_percentage);

        var total_course_fee_amount = $('#total_course_fee_amount').val();
        if(total_course_fee_amount >0){
            total_course_fee_amount = total_course_fee_amount;
        } else {
            total_course_fee_amount = "0.00";
        }
        //console.log('total_course_fee_amount='+total_course_fee_amount);

        var scholarship_fee_amount = $('#scholarship_fee_amount').val();
        if(scholarship_fee_amount >0){
            scholarship_fee_amount = scholarship_fee_amount;
        } else {
            scholarship_fee_amount = "0.00";
        }
        //console.log('scholarship_fee_amount='+scholarship_fee_amount);

        var enrolment_fee_amount = $('#enrolment_fee_amount').val();
        if(enrolment_fee_amount >0){
            enrolment_fee_amount = enrolment_fee_amount;
        } else {
            enrolment_fee_amount = "0.00";
        }
        //console.log('enrolment_fee_amount='+enrolment_fee_amount);

        var material_fee_amount = $('#material_fee_amount').val();
        if(material_fee_amount >0){
            material_fee_amount = material_fee_amount;
        } else {
            material_fee_amount = "0.00";
        }
        //console.log('material_fee_amount='+material_fee_amount);

        var tution_fee = parseFloat(total_course_fee_amount) - parseFloat(scholarship_fee_amount) - parseFloat(enrolment_fee_amount) - parseFloat(material_fee_amount);
        if(tution_fee >0){
            tution_fee = tution_fee.toFixed(2);
        } else {
            tution_fee = "0.00";
        }
        //console.log('tution_fee='+tution_fee);

        /*var percentage = ( parseFloat(tution_fee) * parseFloat(commission_percentage))/100;
        percentage = percentage.toFixed(2);
        //console.log('percentage='+percentage);


        var bonus_amount = $('#bonus').val();
        if(bonus_amount >0){
            bonus_amount = bonus_amount;
        } else {
            bonus_amount = "0.00";
        }
        var commission_percentage_after_bonus = parseFloat(percentage) + parseFloat(bonus_amount);
        if(commission_percentage_after_bonus >0){
            commission_percentage_after_bonus = commission_percentage_after_bonus.toFixed(2);
        } else {
            commission_percentage_after_bonus = "0.00";
        }
        //console.log('commission_percentage_after_bonus='+commission_percentage_after_bonus);*/

        $('.calculate_tution_fee').html(tution_fee);
        //$('.calculate_total_commission').html(commission_percentage_after_bonus);
        $('#tution_fees').val(tution_fee);
        //$('#tution_fees_commission').val(commission_percentage_after_bonus);
    });

    //Student id blur
    $(document).delegate('#student_id','blur', function(){
        var student_id = $(this).val();
        var application_id = $(this).attr('data-applicationid');
        $('.popuploader').show();
        $.ajax({
            url: "{{URL::to('/admin/application/updateStudentId')}}",
            method: "POST",
            data: {student_id:student_id,application_id:application_id},
            datatype: 'json',
            success: function(response) {
                $('.popuploader').hide();
				var obj = $.parseJSON(response);
                $('#student_id').html(obj.student_id);
            }
        });
    });

    //commission_percentage blur
    /*$(document).delegate('#commission_percentage','blur', function(){
        var commission_percentage = $(this).val();
        $('#commission_percentage').val(commission_percentage);
        var partnerid = $('#partnerid').val();
        var given_tution_fee = $('.calculate_tution_fee').html();

        //Calculate commission percentage amount
        var percentage = ( parseFloat(given_tution_fee) * parseFloat(commission_percentage))/100;
        if(percentage >0){
            percentage = percentage.toFixed(2);
        } else {
            percentage = "0.00";
        }
        //console.log('percentage='+percentage);

        //Calculate bonus amount
        var bonus = $('#bonus').val();
        if(bonus >0){
            bonus = parseFloat(bonus);
        } else {
            bonus = "0.00";
        }
        $('#bonus_amount').val(bonus);
        //Calculate commission percentage amount after bonus amount
        var commission_percentage_after_bonus = parseFloat(percentage) + parseFloat(bonus);
        if(commission_percentage_after_bonus >0){
            commission_percentage_after_bonus = commission_percentage_after_bonus.toFixed(2);
        } else {
            commission_percentage_after_bonus = "0.00";
        }

        $('.calculate_total_commission').html(commission_percentage_after_bonus);
        $('#tution_fees_commission').val(commission_percentage_after_bonus);
    });*/


    //bonus  blur
    /*$(document).delegate('#bonus','blur', function(){
        var bonus = $(this).val();

        if(bonus != ""){
            if(bonus >0){
                bonus = bonus;
            } else {
                bonus = "0.00";
            }
        } else {
            bonus = "0.00";
        }
        $('#bonus_amount').val(bonus);

        var given_tution_fee = $('.calculate_tution_fee').html();
        var commission_percentage = $('#commission_percentage').val();
        //console.log('given_tution_fee='+given_tution_fee);
        //console.log('commission_percentage='+commission_percentage);
        //Calculate commission percentage amount
        var percentage = ( parseFloat(given_tution_fee) * parseFloat(commission_percentage))/100;
        if(percentage >0){
            percentage = percentage.toFixed(2);
        } else {
            percentage = "0.00";
        }
        console.log('percentage='+percentage);

        //Calculate commission percentage amount after bonus amount
        var commission_percentage_after_bonus = parseFloat(percentage) + parseFloat(bonus);
        if(commission_percentage_after_bonus >0){
            commission_percentage_after_bonus = commission_percentage_after_bonus.toFixed(2);
        } else {
            commission_percentage_after_bonus = "0.00";
        }
        //console.log('commission_percentage_after_bonus='+commission_percentage_after_bonus);
        $('.calculate_total_commission').html(commission_percentage_after_bonus);
        $('#tution_fees_commission').val(commission_percentage_after_bonus);
    });*/

    //////////////////////////////////////////////////////
    //////////////////////////////////////////////////////
    /////////End 1st popup - Fee Option ///////////
    //////////////////////////////////////////////////////
    //////////////////////////////////////////////////////

    //Calculate Commission amount on 2nd popup
     /*$(document).delegate('.total_fee_am_2nd','blur', function(){
        var fee_paid = $(this).val();
        var commission_percentage = $('#commission_percentage').val();

        var percentage = ( parseFloat(fee_paid) * parseFloat(commission_percentage))/100;
        percentage = percentage.toFixed(2);

        $(this).closest('tr').find('.commission_cal').val(percentage);

        var total_fee_paid = 0;
        $('.total_fee_am_2nd').each(function(){
			total_fee_paid += parseFloat($(this).val());
		});

        var total_com_price = 0;
        $('.commission_cal').each(function(){
			total_com_price += parseFloat($(this).val());
		});

        $('.total_fees_paid').html(total_fee_paid);
        $('.total_commission_earned').html(total_com_price);
    });*/



    //////////////////////////////////////////////////////
    //////////////////////////////////////////////////////
    /////////Start 2nd popup - other fee option ///////////
    //////////////////////////////////////////////////////
    //////////////////////////////////////////////////////

    //Tution fee amount blur change
    $(document).delegate('.total_fee_am_2nd','blur', function(){
        var tution_fee_paid = $(this).val();
        if(tution_fee_paid != ""){
            tution_fee_paid = tution_fee_paid;
        } else {
            tution_fee_paid = 0;
        }

        var commission_percentage = $(this).closest('tr').find('.commission_percentage').val();
        if(commission_percentage != ""){
            commission_percentage = commission_percentage;
        } else {
            commission_percentage = 0;
        }

        var commission_percentage_calculate = ( parseFloat(tution_fee_paid) * parseFloat(commission_percentage))/100;
        var commission_percentage_calculate_fixed = commission_percentage_calculate.toFixed(2);

        $(this).closest('tr').find('.commission_cal').val(commission_percentage_calculate);
        $(this).closest('tr').find('.commission_cal_hidden').val(commission_percentage_calculate);

        var adjustment_discount_entry = $(this).closest('tr').find('.adjustment_discount_entry').val();
        if(adjustment_discount_entry != ""){
            adjustment_discount_entry = parseFloat(adjustment_discount_entry);
        } else {
            adjustment_discount_entry = 0;
        }
        var commission_claimed = commission_percentage_calculate + adjustment_discount_entry;
        $(this).closest('tr').find('.commission_claimed').val(commission_claimed);
        $(this).closest('tr').find('.commission_claimed_hidden').val(commission_claimed);

        var total_fee_paid = 0;
        $('.total_fee_am_2nd').each(function(){
			total_fee_paid += parseFloat($(this).val());
		});

        var total_com_price = 0;
        $('.commission_cal').each(function(){
			total_com_price += parseFloat($(this).val());
		});

        var total_adjustment_discount_entry = 0;
        $('.adjustment_discount_entry').each(function(){
			total_adjustment_discount_entry += parseFloat($(this).val());
		});

        var total_commission_claimed = 0;
        $('.commission_claimed').each(function(){
			total_commission_claimed += parseFloat($(this).val());
		});

        $('.total_fees_paid').html(total_fee_paid);
        $('.total_commission_earned').html(total_com_price);

        $('.total_adjustment_discount_entry').html(total_adjustment_discount_entry);
        $('.total_commission_claimed').html(total_commission_claimed);
    });

    //Commission Percentage blur change
    $(document).delegate('.commission_percentage','blur', function(){
        var commission_percentage = $(this).val();
        if(commission_percentage != ""){
            commission_percentage = commission_percentage;
        } else {
            commission_percentage = 0;
        }
        var tution_fee_paid = $(this).closest('tr').find('.total_fee_am_2nd').val();
        if(tution_fee_paid != ""){
            tution_fee_paid = tution_fee_paid;
        } else {
            tution_fee_paid = 0;
        }

        var commission_percentage_calculate = ( parseFloat(tution_fee_paid) * parseFloat(commission_percentage))/100;
        var commission_percentage_calculate_fixed = commission_percentage_calculate.toFixed(2);

        $(this).closest('tr').find('.commission_cal').val(commission_percentage_calculate);
        $(this).closest('tr').find('.commission_cal_hidden').val(commission_percentage_calculate);

        var adjustment_discount_entry = $(this).closest('tr').find('.adjustment_discount_entry').val();
        if(adjustment_discount_entry != ""){
            adjustment_discount_entry = parseFloat(adjustment_discount_entry);
        } else {
            adjustment_discount_entry = 0;
        }

        var commission_claimed = commission_percentage_calculate + adjustment_discount_entry;
        $(this).closest('tr').find('.commission_claimed').val(commission_claimed);
        $(this).closest('tr').find('.commission_claimed_hidden').val(commission_claimed);


        var total_fee_paid = 0;
        $('.total_fee_am_2nd').each(function(){
			total_fee_paid += parseFloat($(this).val());
		});

        var total_com_price = 0;
        $('.commission_cal').each(function(){
			total_com_price += parseFloat($(this).val());
		});

        var total_adjustment_discount_entry = 0;
        $('.adjustment_discount_entry').each(function(){
			total_adjustment_discount_entry += parseFloat($(this).val());
		});

        var total_commission_claimed = 0;
        $('.commission_claimed').each(function(){
			total_commission_claimed += parseFloat($(this).val());
		});

        $('.total_fees_paid').html(total_fee_paid);
        $('.total_commission_earned').html(total_com_price);

        $('.total_adjustment_discount_entry').html(total_adjustment_discount_entry);
        $('.total_commission_claimed').html(total_commission_claimed);
    });

    //Adjustment Discount Entry blur change
    $(document).delegate('.adjustment_discount_entry','blur', function(){
        var adjustment_discount_entry = $(this).val();
        if(adjustment_discount_entry != ""){
            adjustment_discount_entry = parseFloat(adjustment_discount_entry);
        } else {
            adjustment_discount_entry = 0;
        }


        var tution_fee_paid = $(this).closest('tr').find('.total_fee_am_2nd').val();
        if(tution_fee_paid != ""){
            tution_fee_paid = tution_fee_paid;
        } else {
            tution_fee_paid = 0;
        }

        var commission_percentage = $(this).closest('tr').find('.commission_percentage').val();
        if(commission_percentage != ""){
            commission_percentage = commission_percentage;
        } else {
            commission_percentage = 0;
        }

        var commission_percentage_calculate = ( parseFloat(tution_fee_paid) * parseFloat(commission_percentage))/100;
        var commission_percentage_calculate_fixed = commission_percentage_calculate.toFixed(2);

        $(this).closest('tr').find('.commission_cal').val(commission_percentage_calculate);
        $(this).closest('tr').find('.commission_cal_hidden').val(commission_percentage_calculate);

        var commission_claimed = commission_percentage_calculate + adjustment_discount_entry;
        $(this).closest('tr').find('.commission_claimed').val(commission_claimed);
        $(this).closest('tr').find('.commission_claimed_hidden').val(commission_claimed);

        var total_fee_paid = 0;
        $('.total_fee_am_2nd').each(function(){
			total_fee_paid += parseFloat($(this).val());
		});

        var total_com_price = 0;
        $('.commission_cal').each(function(){
			total_com_price += parseFloat($(this).val());
		});

        var total_adjustment_discount_entry = 0;
        $('.adjustment_discount_entry').each(function(){
			total_adjustment_discount_entry += parseFloat($(this).val());
		});

        var total_commission_claimed = 0;
        $('.commission_claimed').each(function(){
			total_commission_claimed += parseFloat($(this).val());
		});

        $('.total_fees_paid').html(total_fee_paid);
        $('.total_commission_earned').html(total_com_price);

        $('.total_adjustment_discount_entry').html(total_adjustment_discount_entry);
        $('.total_commission_claimed').html(total_commission_claimed);
    });

    //////////////////////////////////////////////////////
    //////////////////////////////////////////////////////
    /////////End 2nd popup - other fee option ///////////
    //////////////////////////////////////////////////////
    //////////////////////////////////////////////////////





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
          formData.append("type", $('.checklisttype').val());
            formData.append("typename", $('.checklisttypename').val());
            formData.append("id", $('.checklistid').val());
            formData.append("application_id", $('.application_id').val());
            formData.append("client_id", $('.app_doc_client_id').val());
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
            formData.append("client_id", $('.app_doc_client_id').val());
            uploadFormData(formData);
          };
        }

       function uploadFormData(form_data) {
            $('.popuploader').show();
            $.ajax({
                url: "{{URL::to('/admin/application/checklistupload')}}",
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

                    if(obj.application_id){
                        $.ajax({
                            url: site_url+'/admin/get-applications-logs',
                            type:'GET',
                            data:{id: obj.application_id},
                            success: function(responses){
                                $('#accordion').html(responses);
                            }
                        });
                    }
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
					url:'{{URL::to('/')}}/admin/delete_action',
					data:{'id': id, 'table' : table},
					success:function(resp) {
						$('#popuploader').hide();
						var obj = $.parseJSON(resp);
						if(obj.status == 1) {
							location.reload();

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
@endsection
