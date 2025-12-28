
<?php $__env->startSection('title', 'Application Reports'); ?>

<?php $__env->startSection('content'); ?>

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				<?php echo $__env->make('../Elements/flash-message', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
			</div>
			<div class="custom-error-msg">
			</div>
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Application Reports</h4>
							<div class="card-header-action">
								<div class="drop_table_data" style="display: inline-block;margin-right: 10px;"> 
									<button type="button" class="btn btn-primary dropdown-toggle"><i class="fas fa-columns"></i></button>
									<div class="dropdown_list application_report_list">
										<label class="dropdown-option all"><input type="checkbox" value="all" checked /> Display All</label>
										<label class="dropdown-option"><input type="checkbox" value="4" checked /> Intake Date</label>
										<label class="dropdown-option"><input type="checkbox" value="6" checked /> Internal Client ID</label>
										<label class="dropdown-option"><input type="checkbox" value="8" checked /> DOB</label>
										<label class="dropdown-option"><input type="checkbox" value="9" checked /> Client Phone</label>
										<label class="dropdown-option"><input type="checkbox" value="10" checked /> Client Followers</label>
										<label class="dropdown-option"><input type="checkbox" value="11" checked /> Partner's Client ID</label>
										<label class="dropdown-option"><input type="checkbox" value="12" checked /> Workflow</label>
										<label class="dropdown-option"><input type="checkbox" value="13" checked /> Partner</label>
										<label class="dropdown-option"><input type="checkbox" value="14" checked /> Product</label>
										<label class="dropdown-option"><input type="checkbox" value="15" checked /> Partner Branch</label>
										<label class="dropdown-option"><input type="checkbox" value="16" checked /> Duration</label>
										<label class="dropdown-option"><input type="checkbox" value="17" checked /> Total Fee</label>
										<label class="dropdown-option"><input type="checkbox" value="18" checked /> Total Fee Discount</label>
										<label class="dropdown-option"><input type="checkbox" value="19" checked /> Installment Type</label>
										<label class="dropdown-option"><input type="checkbox" value="20" checked /> Net First Installment Amount</label>
										<label class="dropdown-option"><input type="checkbox" value="21" checked /> Status</label>
										<label class="dropdown-option"><input type="checkbox" value="22" checked /> Application In Queue</label>
										<label class="dropdown-option"><input type="checkbox" value="23" checked /> Stage In Queue</label>
										<label class="dropdown-option"><input type="checkbox" value="24" checked /> Discontinue Reason</label>
										<label class="dropdown-option"><input type="checkbox" value="25" checked /> Stage</label>
										<label class="dropdown-option"><input type="checkbox" value="26" checked /> Assignee</label>
										<label class="dropdown-option"><input type="checkbox" value="27" checked /> Started By</label>
										<label class="dropdown-option"><input type="checkbox" value="28" checked /> Office</label>
										<label class="dropdown-option"><input type="checkbox" value="29" checked /> Client Source</label>
										<label class="dropdown-option"><input type="checkbox" value="30" checked /> Sub Agent</label>
										<label class="dropdown-option"><input type="checkbox" value="31" checked /> Super Agent</label>
										<label class="dropdown-option"><input type="checkbox" value="32" checked /> Visa Expiry</label>
										<label class="dropdown-option"><input type="checkbox" value="33" checked /> Added Date</label>
										<label class="dropdown-option"><input type="checkbox" value="34" checked /> Start Date</label>
										<label class="dropdown-option"><input type="checkbox" value="35" checked /> End Date</label>
										<label class="dropdown-option"><input type="checkbox" value="36" checked /> Last Updated</label>
									</div>
								</div>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive common_table application_report_data"> 
								<table class="table text_wrap">
									<thead> 
										<tr>
											<th class="text-center" style="width:30px;">
												<div class="custom-checkbox custom-checkbox-table custom-control">
													<input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad" class="custom-control-input" id="checkbox-all">
													<label for="checkbox-all" class="custom-control-label">&nbsp;</label>
												</div>
											</th>
											<th style="white-space: initial;">Application ID</th>
											<th style="white-space: initial;">Client</th>
											<th style="white-space: initial;">Intake Date</th>
											<th style="white-space: initial;">Email</th>
											<th style="white-space: initial;">Internal Client ID</th>
											<th style="white-space: initial;">Client ID</th>
											<th style="white-space: initial;">D.O.B</th>
											<th style="white-space: initial;">Client Phone</th>
											<th style="white-space: initial;">Client Followers</th>
											<th style="white-space: initial;">Partner's Client ID</th>
											<th style="white-space: initial;">Workflow</th>
											<th style="white-space: initial;">Partner</th>
											<th style="white-space: initial;">Product</th>
											<th style="white-space: initial;">Partner Branch</th>
											<th style="white-space: initial;">Duration</th>
											<th style="white-space: initial;">Total Fee</th>
											<th style="white-space: initial;">Total Fee Discount</th>
											<th style="white-space: initial;">Installment Type</th>
											<th style="white-space: initial;">Net First Installment Amount</th>
											<th style="white-space: initial;">Status</th>
											<th style="white-space: initial;">Application In Queue</th>
											<th style="white-space: initial;">Stage In Queue</th>
											<th style="white-space: initial;">Discontinue Reason</th>
											<th style="white-space: initial;">Stage</th>
											<th style="white-space: initial;">Assignee</th>
											<th style="white-space: initial;">Started By</th>
											<th style="white-space: initial;">Office</th>
											<th style="white-space: initial;">Client Source</th>
											<th style="white-space: initial;">Sub Agent</th>
											<th style="white-space: initial;">Super Agent</th>
											<th style="white-space: initial;">Visa Expiry</th>
											<th style="white-space: initial;">Added Date</th>
											<th style="white-space: initial;">Start Date</th>
											<th style="white-space: initial;">End Date</th>
											<th style="white-space: initial;">Last Updated</th>
										</tr> 
									</thead>
									<?php if(count($lists) >0): ?>
									<tbody class="tdata">	
										<?php if(@$totalData !== 0): ?>
										<?php $i=0; ?>
										<?php $__currentLoopData = @$lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<?php 
											$productdetail = \App\Models\Product::where('id', $list->product_id)->first();
											$partnerdetail = \App\Models\Partner::where('id', $list->partner_id)->first();		
											$clientdetail = \App\Models\Admin::where('id', $list->client_id)->first();
											$PartnerBranch = \App\Models\PartnerBranch::where('id', $list->branch)->first();
										?>
										<tr id="id_<?php echo e(@$list->id); ?>"> 
											<td class="text-center">
												<div class="custom-checkbox custom-control">
													<input data-id="<?php echo e(@$list->id); ?>" data-email="<?php echo e(@$list->email); ?>" data-name="<?php echo e(@$list->first_name); ?> <?php echo e(@$list->last_name); ?>" type="checkbox" data-checkboxes="mygroup" class="cb-element custom-control-input" id="checkbox-<?php echo e($i); ?>">
													<label for="checkbox-<?php echo e($i); ?>" class="custom-control-label">&nbsp;</label>
												</div>
											</td>
											<td style="white-space: initial;"><a href="<?php echo e(URL::to('admin/clients/detail/')); ?>/<?php echo e(base64_encode(convert_uuencode(@$clientdetail->id))); ?>?tab=application&appid=<?php echo e(@$list->id); ?>"><?php echo e(@$list->id == "" ? config('constants.empty') : str_limit(@$list->id, '50', '...')); ?></a></td> 
											<td style="white-space: initial;"><a href="<?php echo e(URL::to('admin/clients/detail/')); ?>/<?php echo e(base64_encode(convert_uuencode(@$clientdetail->id))); ?>"><?php echo e(@$clientdetail->first_name); ?> <?php echo e(@$clientdetail->last_name); ?></a></td> 
											<td style="white-space: initial;"><?php echo e(@$list->intakedate == "" ? config('constants.empty') : str_limit(@$list->intakedate, '50', '...')); ?> </td>
											<td style="white-space: initial;"><a data-id="<?php echo e(@$list->id); ?>" data-email="<?php echo e(@$clientdetail->email); ?>" data-name="<?php echo e(@$clientdetail->first_name); ?> <?php echo e(@$clientdetail->last_name); ?>" href="javascript:;" class="clientemail"><?php echo e(@$clientdetail->email == "" ? config('constants.empty') : str_limit(@$clientdetail->email, '50', '...')); ?></a></td>
											<td>-</td>
											<td style="white-space: initial;"><?php echo e(@$clientdetail->client_id); ?> </td>
											<td style="white-space: initial;"><?php echo e(date('d/m/Y',strtotime(@$clientdetail->dob))); ?> </td>
											<td style="white-space: initial;"><?php echo e(@$clientdetail->phone); ?> </td>
											<td style="white-space: initial;"><?php echo e(@$clientdetail->followers); ?> </td>
											<td>-</td>
											<td style="white-space: initial;"><?php echo e(@$list->workflow == "" ? config('constants.empty') : str_limit(@$list->workflow, '50', '...')); ?> </td>
											<td style="white-space: initial;"><?php echo e(@$partnerdetail->partner_name); ?></td> 
											<td style="white-space: initial;"><?php echo e(@$productdetail->name); ?></td> 
											<td style="white-space: initial;"><?php echo e($PartnerBranch->name); ?></td>
											<td style="white-space: initial;"><?php echo e(@$productdetail->duration); ?></td> 
											<td>-</td>
											<td>-</td>
											<td>-</td>
											<td>-</td>
											<td style="white-space: initial;"><?php echo e(@$list->status == "" ? config('constants.empty') : str_limit(@$list->status, '50', '...')); ?> </td>
											<td>-</td>
											<td>-</td>
											<td>-</td>
											<td style="white-space: initial;"><?php echo e(@$list->stage == "" ? config('constants.empty') : str_limit(@$list->stage, '50', '...')); ?> </td>
											<td style="white-space: initial;"><?php echo e(@$clientdetail->assignee); ?> </td>
											<td style="white-space: initial;"><?php echo e(@$list->created_at == "" ? config('constants.empty') : date('d/m/Y',strtotime(@$list->created_at))); ?></td>
											<td style="white-space: initial;"><?php echo e($PartnerBranch->name); ?></td>
											<td style="white-space: initial;"><?php echo e(@$clientdetail->source); ?> </td>
											<td style="white-space: initial;"><?php echo e(@$list->sub_agent == "" ? config('constants.empty') : str_limit(@$list->sub_agent, '50', '...')); ?></td>
											<td style="white-space: initial;"><?php echo e(@$list->super_agent == "" ? config('constants.empty') : str_limit(@$list->super_agent, '50', '...')); ?></td>
											<td style="white-space: initial;"><?php echo e(date('d/m/Y',strtotime(@$clientdetail->visaExpiry))); ?></td>
											<td style="white-space: initial;"><?php echo e(@$list->created_at == "" ? config('constants.empty') : date('d/m/Y',strtotime(@$list->created_at))); ?></td>
											<td style="white-space: initial;"><?php echo e(@$list->start_date == "" ? config('constants.empty') : date('d/m/Y',strtotime(@$list->start_date))); ?></td>
											<td style="white-space: initial;"><?php echo e(@$list->end_date == "" ? config('constants.empty') : date('d/m/Y',strtotime(@$list->end_date))); ?></td>
											<td style="white-space: initial;"><?php echo e(@$list->updated_at == "" ? config('constants.empty') : date('d/m/Y',strtotime(@$list->updated_at))); ?></td>
										</tr>
										<?php $i++; ?>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
										<?php endif; ?>										
									</tbody>  
									<?php else: ?>
										<tbody>
											<tr>
												<td style="text-align:center;" colspan="12">
													No Record found
												</td>
											</tr>
										</tbody>
									<?php endif; ?>			
								</table> 
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\reports\application.blade.php ENDPATH**/ ?>