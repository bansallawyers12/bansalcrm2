
<?php $__env->startSection('title', 'Sales Forecast Report'); ?>

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
							<h4>Sales Forecast Report</h4>
							<div class="card-header-action">
								<div class="drop_table_data" style="display: inline-block;margin-right: 10px;"> 
									<button type="button" class="btn btn-primary dropdown-toggle"><i class="fas fa-columns"></i></button>
									<div class="dropdown_list saleforecast_applic_report_list">
										<label class="dropdown-option all"><input type="checkbox" value="all" checked /> Display All</label>
										<label class="dropdown-option"><input type="checkbox" value="3" checked /> Contact Email</label>
										<label class="dropdown-option"><input type="checkbox" value="4" checked /> Rating</label>
										<label class="dropdown-option"><input type="checkbox" value="5" checked /> Workflow</label>
										<label class="dropdown-option"><input type="checkbox" value="6" checked /> Partner</label>
										<label class="dropdown-option"><input type="checkbox" value="7" checked /> Product</label>
										<label class="dropdown-option"><input type="checkbox" value="8" checked /> Status</label>
										<label class="dropdown-option"><input type="checkbox" value="9" checked /> Stage</label>
										<label class="dropdown-option"><input type="checkbox" value="10" checked /> Added Date</label>
										<label class="dropdown-option"><input type="checkbox" value="12" checked /> Added By Office</label>
										<label class="dropdown-option"><input type="checkbox" value="13" checked /> Assignee</label>
										<label class="dropdown-option"><input type="checkbox" value="14" checked /> Client Revenue</label>
										<label class="dropdown-option"><input type="checkbox" value="15" checked /> Partner Revenue</label>
										<label class="dropdown-option"><input type="checkbox" value="16" checked /> Discount</label>
										<label class="dropdown-option"><input type="checkbox" value="18" checked /> Won Value</label>
										<label class="dropdown-option"><input type="checkbox" value="19" checked /> Won At</label>
										<label class="dropdown-option"><input type="checkbox" value="20" checked /> Expected Win Date</label>
									</div>
								</div>
							</div>
						</div>
						<div class="card-body">
							<ul class="nav nav-pills" id="checkin_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" id="forecast_application-tab"  href="<?php echo e(URL::to('/admin/report/sale-forecast/application')); ?>" >Application</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="interested_service-tab"  href="<?php echo e(URL::to('/admin/report/sale-forecast/interested-service')); ?>" >Interested Service</a>
								</li>								
							</ul>  
							<div class="tab-content" id="checkinContent">
								<div class="tab-pane fade show active" id="forecast_application" role="tabpanel" aria-labelledby="forecast_application-tab">
									<div class="table-responsive common_table saleforecast_application_report_data">  
										<table class="table text_wrap">
											<thead> 
												<tr>
													<th>Application ID</th>
													<th>Contact Name</th>
													<th>Contact Email</th>
													<th>Rating</th>
													<th>Workflow</th>
													<th>Partner</th>
													<th>Product</th>
													<th>Status</th>
													<th>Stage</th>
													<th>Added Date</th>
													<th>Added By</th>
													<th>Added By Office</th>
													<th>Assignee</th>
													<th>Client Revenue</th>
													<th>Partner Revenue</th>
													<th>Discount</th>
													<th>Total Forecast</th>
													<th>Won Value</th>
													<th>Won At</th>
													<th>Expected Win Date</th>
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
													<td><a href="<?php echo e(URL::to('admin/clients/detail/')); ?>/<?php echo e(base64_encode(convert_uuencode(@$clientdetail->id))); ?>?tab=application&appid=<?php echo e(@$list->id); ?>"><?php echo e(@$list->id == "" ? config('constants.empty') : str_limit(@$list->id, '50', '...')); ?></a></td> 
													<td><a href="<?php echo e(URL::to('admin/clients/detail/')); ?>/<?php echo e(base64_encode(convert_uuencode(@$clientdetail->id))); ?>"><?php echo e(@$clientdetail->first_name); ?> <?php echo e(@$clientdetail->last_name); ?></a></td> 
													<td><?php echo e(@$list->intakedate == "" ? config('constants.empty') : str_limit(@$list->intakedate, '50', '...')); ?> </td>
													<td><a data-id="<?php echo e(@$list->id); ?>" data-email="<?php echo e(@$clientdetail->email); ?>" data-name="<?php echo e(@$clientdetail->first_name); ?> <?php echo e(@$clientdetail->last_name); ?>" href="javascript:;" class="clientemail"><?php echo e(@$clientdetail->email == "" ? config('constants.empty') : str_limit(@$clientdetail->email, '50', '...')); ?></a></td>
													<td>-</td>
													<td><?php echo e(@$clientdetail->client_id); ?> </td>
													<td><?php echo e(date('d/m/Y',strtotime($clientdetail->dob))); ?></td>
													<td><?php echo e(@$clientdetail->phone); ?> </td>
													<td><?php echo e(@$clientdetail->followers); ?> </td>
													<td>-</td>
													<td><?php echo e(@$list->workflow == "" ? config('constants.empty') : str_limit(@$list->workflow, '50', '...')); ?> </td>
													<td><?php echo e(@$partnerdetail->partner_name); ?></td> 
													<td><?php echo e(@$productdetail->name); ?></td> 
													<td><?php echo e($PartnerBranch->name); ?></td>
													<td><?php echo e(@$productdetail->duration); ?></td> 
													<td>-</td>
													<td>-</td>
													<td>-</td>
													<td>-</td>
													<td><?php echo e(@$list->status == "" ? config('constants.empty') : str_limit(@$list->status, '50', '...')); ?> </td>
													<td>-</td>
													<td>-</td>
													<td>-</td>
													<td><?php echo e(@$list->stage == "" ? config('constants.empty') : str_limit(@$list->stage, '50', '...')); ?> </td>
													<td><?php echo e(@$clientdetail->assignee); ?> </td>
													<td><?php echo e(@$list->created_at == "" ? config('constants.empty') : date('d/m/Y',strtotime($list->created_at))); ?></td>
													<td><?php echo e($PartnerBranch->name); ?></td>
													<td><?php echo e(@$clientdetail->source); ?> </td>
													<td><?php echo e(@$list->sub_agent == "" ? config('constants.empty') : str_limit(@$list->sub_agent, '50', '...')); ?></td>
													<td><?php echo e(@$list->super_agent == "" ? config('constants.empty') : str_limit(@$list->super_agent, '50', '...')); ?></td>
													<td><?php echo e(date('d/m/Y',strtotime($clientdetail->visaExpiry))); ?></td>
													<td><?php echo e(@$list->created_at == "" ? config('constants.empty') : date('d/m/Y',strtotime($list->created_at))); ?></td>
													<td><?php echo e(@$list->start_date == "" ? config('constants.empty') : date('d/m/Y',strtotime($list->start_date))); ?></td>
													<td><?php echo e(@$list->end_date == "" ? config('constants.empty') : date('d/m/Y',strtotime($list->end_date))); ?></td>
													<td><?php echo e(@$list->updated_at == "" ? config('constants.empty') : date('d/m/Y',strtotime($list->updated_at))); ?></td>
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
			</div>
		</div>
	</section>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\reports\saleforecast-application.blade.php ENDPATH**/ ?>