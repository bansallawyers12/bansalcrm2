<?php
$productdetail = \App\Product::where('id', $fetchData->product_id)->first();
$partnerdetail = \App\Partner::where('id', $fetchData->partner_id)->first();
$PartnerBranch = \App\PartnerBranch::where('id', $fetchData->branch)->first();
$workflow = \App\Workflow::where('id', $fetchData->workflow)->first();
?>
<style>
.checklist .round{background: #fff;border: 1px solid #000; border-radius: 50%;font-size: 10px;line-height: 14px; padding: 2px 5px;width: 16px; height: 16px; display: inline-block;}
.circular-box {height: 24px;width: 24px;line-height: 24px;display: inline-block; text-align: center; box-shadow: 0 4px 6px 0 rgb(34 36 38 / 12%), 0 2px 12px 0 rgb(34 36 38 / 15%);}
.circular-box{background: #fff;border: 1px solid #d2d2d2;border-radius: 50%;}
.transparent-button { background-color: transparent;border: none;cursor: pointer;}
.checklist span.check, .mychecklistdocdata span.check{background: #71cc53;color: #fff;border-radius: 50%;font-size: 10px;line-height: 14px;padding: 2px 3px;width: 18px;height: 18px;display: inline-block;}
</style>
<div class="card-header-action" style="padding-bottom:15px;">
	<div class="float-left">
		<h5 class="applicationstatus"><?php if($fetchData->status == 0){ ?>In Progress<?php }else if($fetchData->status == 1){ echo 'Completed'; } else if($fetchData->status == 2){ echo 'Discontinued'; } ?></h5> 
	</div>
	<div class="float-right">
		<div class="application_btns">
			<a target="_blank" href="{{URL::to('/agent/application/export/pdf/')}}/{{$fetchData->id}}" class="btn btn-primary"><i class="fa fa-print"></i></a>
			
			<?php
			$displayback = false;
			$workflowstage = \App\WorkflowStage::where('w_id', $fetchData->workflow)->orderBy('id','desc')->first();
		
			if($workflowstage->name == $fetchData->stage){
				$displayback = true;
			} 
			?>
			
		</div>
	</div>
</div>
<div class="clearfix"></div>
<div class="application_grid_col">
	<div class="grid_column">
		<span>Course:</span>
		<p>{{@$productdetail->name}}</p>
	</div>
	<div class="grid_column">
		<span>School:</span>
		<p>{{@$partnerdetail->partner_name}}</p>
	</div>
	<div class="grid_column">
		<span>Branch:</span>
		<p>{{@$PartnerBranch->name}}</p>
	</div>
	<div class="grid_column">
		<span>Workflow:</span>
		<p>{{@$workflow->name}}</p>
	</div>
	<div class="grid_column">
		<span>Current Stage:</span>
		<p class="text-success curerentstage" style="font-weight: 600;font-size: 14px;">{{$fetchData->stage}}</p>
	</div>
	<div class="grid_column">
		<span>Application Id:</span>
		<p>{{$fetchData->id}}</p>
	</div>
	<!--<div class="grid_column">
		<span>Partner's Client Id:</span>
		<p>22</p>
	</div>-->
	<div class="grid_column">
		<span>Started at:</span>
		<p>{{date('Y-m-d', strtotime($fetchData->created_at))}}</p>
	</div>
	<div class="grid_column">
		<span>Last Updated:</span>
		<p>{{date('Y-m-d', strtotime($fetchData->updated_at))}}</p>
	</div>
	<div class="grid_column">
		<div class="overall_progress">
			<span>Overall Progress:</span>
			<?php
			if($fetchData->progresswidth == 0){
				$width = 0;
			}else{
				$width = $fetchData->progresswidth;
			}
			
			$over = '';
			if($width > 50){
				$over = '50';
			}
			?>
			 <div id="progresscir" class="progress-circle over_{{$over}} prgs_{{$width}}">
			   <span>{{$width}} %</span>
			   <div class="left-half-clipper"> 
				  <div class="first50-bar"></div>
				  <div class="value-bar"></div>
			   </div>
			</div> 
		</div>
	</div>
	<div class="grid_column last_grid_column">
		<!--<div class="view_other_detail">
			<a href="#" class="btn btn-outline-primary">View Other Details</a>
		</div>-->
	</div>
</div>
<div class="clearfix"></div>
<div class="divider"></div>
<div class="row">
	<div class="col-md-9">
		<div class="application_other_info">
			<ul class="nav nav-pills" id="applicat_detail_tabs" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="applicate_activities-tab" data-toggle="tab" href="#applicate_activities">Activities</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="documents-tab" data-toggle="tab" href="#documents">Documents</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="notes-tab" data-id="{{$fetchData->id}}" data-toggle="tab"  href="#notes">Notes</a>
				</li>
			
			</ul> 
			<div class="tab-content" id="applicationContent">
				<div class="tab-pane fade show active" id="applicate_activities" role="tabpanel" aria-labelledby="applicate_activities-tab">
					<div id="accordion">
					<?php
			
						
						if($fetchData->status == 1){
							$stage9 = 'app_green';
						}
						$stagesquery = \App\WorkflowStage::where('w_id', $fetchData->workflow)->get();
							
		
						foreach($stagesquery as $stages){
								$stage1 = '';
					?>
					<?php
					$workflowstagess = \App\WorkflowStage::where('name', $fetchData->stage)->where('w_id', $fetchData->workflow)->first();
					$stagearray = array();
					if($workflowstagess){
					$prevdata = \App\WorkflowStage::where('id', '<', @$workflowstagess->id)->where('w_id', $fetchData->workflow)->orderBy('id','Desc')->get();
					
					foreach($prevdata as $pre){
						$stagearray[] = $pre->id;
					}
					}
					
							
							if(in_array($stages->id, $stagearray)){
								$stage1 = 'app_green';
							}
							if($fetchData->status == 1){
								$stage1 = 'app_green';
							}
							$stagname = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $stages->name)));
							?>
						<div class="accordion cus_accrodian">
							
							<div class="accordion-header collapsed <?php echo $stage1; ?> <?php if($fetchData->stage == $stages->name && $fetchData->status != 1){ echo  'app_blue'; }  ?>" role="button" data-toggle="collapse" data-target="#<?php echo $stagname; ?>_accor" aria-expanded="false">
								<h4><?php echo $stages->name; ?></h4>
								
							</div>
							<?php
							$applicationlists = \App\ApplicationActivitiesLog::where('app_id', $fetchData->id)->where('stage',$stages->name)->orderby('created_at', 'DESC')->get();
							
							?>
							<div class="accordion-body collapse" id="<?php echo $stagname; ?>_accor" data-parent="#accordion" style="">
								<div class="activity_list">
								<?php foreach($applicationlists as $applicationlist){ 
								$admin = \App\Admin::where('id',$applicationlist->user_id)->first();
								?>
									<div class="activity_col">
										<div class="activity_txt_time">
											<span class="span_txt"><b>{{$admin->first_name}}</b> {!! $applicationlist->comment !!}</span>
											<span class="span_time"><?php echo date('d D, M Y h:i A', strtotime($applicationlist->created_at)); ?></span>
										</div>
										<?php if($applicationlist->title != ''){ ?>
										<div class="app_description"> 
											<div class="app_card">
												<div class="app_title"><?php echo $applicationlist->title; ?></div>
											</div>
											<?php if($applicationlist->description != ''){ ?>
											<div class="log_desc">
												<?php echo $applicationlist->description; ?>
											</div>
											<?php } ?>
										</div>	
										<?php } ?> 
									</div>
								<?php } ?>
								</div>
							</div>
						</div>
						<?php } ?>
						
					</div> 
				</div> 
				<div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
					<div class="document_checklist">
					<?php
					$applicationdocumentco = \App\ApplicationDocumentList::where('application_id', $fetchData->id)->count();
					$application_id =  $fetchData->id;
					$applicationuploadcount = DB::select("SELECT COUNT(DISTINCT list_id) AS cnt FROM application_documents where application_id = '$application_id'");
					$stagesquery = \App\WorkflowStage::where('w_id', $fetchData->workflow)->get();
					?>
						<h4>Document Checklist (<span class="checklistuploadcount">{{@$applicationuploadcount[0]->cnt}}</span>/<span class="checklistcount">{{@$applicationdocumentco}}</span>)</h4>
						<p>The changes & addition of the checklist will only be affected to current application only.</p>
						<div class="row">
							<div class="col-md-5">
								<div class="checklist">
										<ul>
										<?php
										foreach($stagesquery as $stages){
											$name = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $stages->name)));
											
										?>
											<li><span><?php echo $stages->name; ?></span>
											<div class="<?php echo $name; ?>_checklists">
												<?php
												$applicationdocumentsquery = \App\ApplicationDocumentList::where('application_id', $fetchData->id)->where('type', $name);
												$applicationdocumentscount = $applicationdocumentsquery->count();
												$applicationdocuments = $applicationdocumentsquery->get();
												if($applicationdocumentscount !== 0){
													
												?>
												<table class="table">
													<tbody>
														<?php foreach($applicationdocuments as $applicationdocument){ 
														$appcount = \App\ApplicationDocument::where('list_id', $applicationdocument->id)->count();
														?>
														<tr>
															<td><?php if($appcount >0){ ?><span class="check"><i class="fa fa-check"></i></span><?php }else{ ?><span class="round"></span><?php } ?></td>
															<td>{{@$applicationdocument->document_type}}</td>
															<td><div class="circular-box cursor-pointer"><button class="transparent-button paddingNone">{{@$appcount}}</button></div></td>
															<td></td>
														</tr>
														<?php } ?>
													</tbody>
													
												</table>
												<?php } ?>
											</div>
											</li>
										<?php } ?>
											
										</ul> 
						</div>
							</div>
							<div class="col-md-7">
								<div class="table-responsive"> 
						<table class="table text_wrap">
							<thead>
								<tr>
									<th>Filename / Checklist</th>
									<th>Related Stage</th>
									<th>Added By</th>
									<th>Added On</th>
									<th></th>
								</tr> 
							</thead>
							<tbody class="tdata mychecklistdocdata">	
							<?php
							$doclists = \App\ApplicationDocument::where('application_id',$fetchData->id)->orderby('created_at','DESC')->get();
							foreach($doclists as $doclist){
								$docdata = \App\ApplicationDocumentList::where('id', $doclist->list_id)->first();
							?>
								<tr id="">
									<td><i class="fa fa-file"></i> <?php echo $doclist->file_name; ?><br><?php echo @$docdata->document_type; ?></td>
									<td>
										<?php
										echo $doclist->typename;
										?>
									</td>
									<td><?php
									$admin = \App\Admin::where('id', $doclist->user_id)->first();
									?><span style="position: relative;background: rgb(3, 169, 244);font-size: .8rem;height: 24px;line-height: 24px;min-width: 24px;width: 24px;color: #fff;display: block;font-weight: 600;letter-spacing: 1px;text-align: center;border-radius: 50%;overflow: hidden;"><?php echo substr($admin->first_name, 0, 1); ?></span><?php echo $admin->first_name; ?></td>
									<td><?php echo date('Y-m-d',strtotime($doclist->created_at)); ?></td>
									<td>
										<?php if($doclist->status == 1){
											?>
											<span class="check"><i class="fa fa-eye"></i></span>
											<?php
										} ?>
																  
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
						</div>
						
					</div>
					
				</div>
				<div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
				</div>
				<div class="tab-pane fade" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">
					<div id="taskaccordion">
					<?php
					$stagesquery = \App\WorkflowStage::where('w_id', $fetchData->workflow)->get();
					foreach($stagesquery as $stages){
						$stagname = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $stages->name)));
					?>
						<div class="accordion cus_accrodian">
							<div class="accordion-header collapsed active" role="button" data-toggle="collapse" data-target="#<?php echo $stagname; ?>_accor" aria-expanded="false">
								<h4><?php echo $stages->name; ?></h4>
								<div class="accord_hover">
									<a title="Add Task" class="opentaskmodal" href="javascript:;"><i class="fa fa-suitcase"></i></a>
								</div>
							</div>
							<!--<div class="accordion-body collapse" id="application_accor" data-parent="#taskaccordion" style="">
							</div>-->
						</div>
					<?php } ?>
						
					</div>
				</div>
				
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="cus_sidebar">
			<div class="form-group">
				<label for="applied_intake">Applied Intake:</label>
				{{ Form::text('applied_intake', $fetchData->intakedate, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','readonly'=>true )) }}
				<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
				@if ($errors->has('applied_intake'))
					<span class="custom-error" role="alert">
						<strong>{{ @$errors->first('applied_intake') }}</strong>
					</span> 
				@endif 
			</div>
			
			<div class="app_date_sec">
				<div class="app_start_date common_apply_date">
					<span>Start</span>
					<div class="date_col">
						<div class="add_date">
							<span><i class="fa fa-plus"></i> Add</span>
						</div>  
						<input type="text" value="{{@$fetchData->start_date}}" class="" />
						<div class="apply_val">
						
							<span class="month"><?php if(@$fetchData->start_date != ''){ echo date('M',strtotime($fetchData->start_date)); }else{ echo '-'; }?></span>
							<span class="day"><?php if(@$fetchData->start_date != ''){ echo date('d',strtotime($fetchData->start_date)); }else{ echo '-'; }?></span>
							<span class="year"><?php if(@$fetchData->start_date != ''){ echo date('Y',strtotime($fetchData->start_date)); }else{ echo '-'; }?></span>
						</div>
					</div>
				</div>
				<div class="app_end_date common_apply_date">
					<span>End</span>
					<div class="date_col">
						<div class="add_date">
							<span><i class="fa fa-plus"></i> Add</span> 
						</div>
						<input type="text" value="{{@$fetchData->end_date}}" class="enddatepicker" />
						<div class="apply_val">
							<span class="month"><?php if(@$fetchData->end_date != ''){ echo date('M',strtotime($fetchData->end_date)); }else{ echo '-'; }?></span>
							<span class="day"><?php if(@$fetchData->end_date != ''){ echo date('d',strtotime($fetchData->end_date)); }else{ echo '-'; }?></span>
							<span class="year"><?php if(@$fetchData->end_date != ''){ echo date('Y',strtotime($fetchData->end_date)); }else{ echo '-'; }?></span>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="divider"></div>
			<div class="setup_payment_sche">
				<?php
			$appfeeoption = \App\ApplicationFeeOption::where('app_id', $fetchData->id)->first();
			$totl = 0.00;
			$discount = 0.00;
			if($appfeeoption){
				$appfeeoptiontype = \App\ApplicationFeeOptionType::where('fee_id', $appfeeoption->id)->get();
				foreach($appfeeoptiontype as $fee){
					$totl += $fee->total_fee;
				}
			}
	
			if(@$appfeeoption->total_discount != ''){
				$discount = @$appfeeoption->total_discount;
			}
			$net = $totl -  $discount;
			$invoiceschedule = \App\InvoiceSchedule::where('application_id', $fetchData->id)->first();
			?>
				
			</div>
			<div class="divider"></div>
			
			<div class="cus_prod_fees">
				<h5>Product Fees <span>AUD</span></h5>
				
				<div class="clearfix"></div>
			</div>
			<p class="clearfix"> 
				<span class="float-left">Total Fee</span>
				<span class="float-right text-muted product_totalfee">{{$totl}}</span>
			</p>
			<p class="clearfix" style="color:#ff0000;"> 
				<span class="float-left">Discount</span>
				<span class="float-right text-muted product_discount">{{$discount}}</span>
			</p>
			<p class="clearfix" style="color:#6777ef;"> 
				<span class="float-left">Net Fee</span>
				<span class="float-right text-muted product_net_fee">{{$net}}</span>
			</p>
			<div class="divider"></div>
			<div class="cus_prod_fees ">
				<h5>Sales Forecast <span>AUD</span></h5>
				<?php
			$client_revenue = '0.00';
			if($fetchData->client_revenue != ''){
				$client_revenue = $fetchData->client_revenue;
			}
			$partner_revenue = '0.00';
			if($fetchData->partner_revenue != ''){
				$partner_revenue = $fetchData->partner_revenue;
			}
			$discounts = '0.00';
			if($fetchData->discounts != ''){
				$discounts = $fetchData->discounts;
			}
			$nettotal = $client_revenue + $partner_revenue - $discounts;
			?>
			
				<div class="clearfix"></div>
			</div>
			
			<p class="clearfix appsaleforcast"> 
				<span class="float-left">Partner Revenue</span>
				<span class="float-right text-muted partner_revenue">{{$partner_revenue}}</span>
			</p>
			<p class="clearfix appsaleforcast"> 
				<span class="float-left">Client Revenue</span>
				<span class="float-right text-muted client_revenue">{{$client_revenue}}</span>
			</p>
			<p class="clearfix appsaleforcast" style="color:#ff0000;"> 
				<span class="float-left">Discount</span>
				<span class="float-right text-muted discounts">{{$discounts}}</span>
			</p>
			<p class="clearfix appsaleforcast" style="color:#6777ef;"> 
				<span class="float-left">Net Revenue</span>
				<span class="float-right text-muted netrevenue">{{number_format($nettotal,2,'.','')}}</span>
			</p>
			<div class="form-group">
				<label for="expect_win_date">Expected Win Date:</label>
				{{ Form::text('expect_win_date', $fetchData->expect_win_date, array('class' => 'form-control ', 'data-valid'=>'', 'autocomplete'=>'off','readonly'=>true )) }}
				<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
				@if ($errors->has('expect_win_date'))
					<span class="custom-error" role="alert">
						<strong>{{ @$errors->first('expect_win_date') }}</strong>
					</span> 
				@endif 
			</div>
			<?php
				$admin = \App\Admin::where('id', $fetchData->user_id)->first();
			?>
			<div class="divider"></div>
			<div class="setup_payment_sche">
			<?php
			$ratio = '100';
			if($fetchData->ratio != ''){
				$ratio = $fetchData->ratio;
			}
			?>
			
			</div>
			
			<div class="divider"></div> 
			<div class="client_added client_info_tags"> 
				<span class="">Started By:</span>
				<div class="client_info">
					<div class="cl_logo">{{substr($admin->first_name, 0, 1)}}</div>
					<div class="cl_name">
						<span class="name">{{$admin->first_name}}</span>
						<span class="email">{{$admin->email}}</span>
					</div>
				</div> 
			</div>
			<div class="client_assign client_info_tags"> 
				<span class="">Assignee:</span>
				<div class="client_info">
					<div class="cl_logo">{{substr($admin->first_name, 0, 1)}}</div>
					<div class="cl_name">
						<span class="name">{{$admin->first_name}}</span>
						<span class="email">{{$admin->email}}</span>
					</div>
				</div>
			</div>
			<div class="divider"></div> 
		
		</div> 
	</div>
</div>




