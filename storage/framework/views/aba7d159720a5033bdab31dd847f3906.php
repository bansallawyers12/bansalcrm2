
<?php $__env->startSection('title', 'Client Reports'); ?>

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
							<h4>Client Reports</h4>
							<div class="card-header-action">
								<div class="drop_table_data" style="display: inline-block;margin-right: 10px;">
									<button type="button" class="btn btn-primary dropdown-toggle"><i class="fas fa-columns"></i></button>
									<div class="dropdown_list client_report_list">
										<label class="dropdown-option all"><input type="checkbox" value="all" checked /> Display All</label>
										<label class="dropdown-option"><input type="checkbox" value="3" checked /> Rating</label>
										<label class="dropdown-option"><input type="checkbox" value="5" checked /> Status</label>
										<label class="dropdown-option"><input type="checkbox" value="4" checked /> Client Id</label>
										<label class="dropdown-option"><input type="checkbox" value="6" checked /> Added From</label>
										<label class="dropdown-option"><input type="checkbox" value="7" checked /> Tag(s)</label>
										<label class="dropdown-option"><input type="checkbox" value="8" checked /> Sub Agent</label>
										<label class="dropdown-option"><input type="checkbox" value="9" checked /> Contact Source</label>
										<label class="dropdown-option"><input type="checkbox" value="10" checked /> Phone</label>
										<label class="dropdown-option"><input type="checkbox" value="12" checked /> Street</label>
										<label class="dropdown-option"><input type="checkbox" value="13" checked /> City</label>
										<label class="dropdown-option"><input type="checkbox" value="14" checked /> State</label>
										<label class="dropdown-option"><input type="checkbox" value="15" checked /> Country</label>
										<label class="dropdown-option"><input type="checkbox" value="16" checked /> Country Of Passport</label>
										<label class="dropdown-option"><input type="checkbox" value="17" checked /> D.O.B</label>
										<label class="dropdown-option"><input type="checkbox" value="18" checked /> Added Date</label>
										<label class="dropdown-option"><input type="checkbox" value="19" checked /> Enquiry Date</label>
										<label class="dropdown-option"><input type="checkbox" value="20" checked /> Prospect Date</label>
										<label class="dropdown-option"><input type="checkbox" value="21" checked /> Client Date</label>
										<label class="dropdown-option"><input type="checkbox" value="22" checked /> Visa Expiry Date</label>
										<label class="dropdown-option"><input type="checkbox" value="23" checked /> Preferred Intake</label>
										<label class="dropdown-option"><input type="checkbox" value="25" checked /> Added By Office</label>
										<label class="dropdown-option"><input type="checkbox" value="24" checked /> Added By User</label>
										<label class="dropdown-option"><input type="checkbox" value="26" checked /> Assignee</label>
										<label class="dropdown-option"><input type="checkbox" value="27" checked /> Assignee Office</label>
										<label class="dropdown-option"><input type="checkbox" value="28" checked /> Followers</label>
									</div>
								</div>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive common_table client_report_data"> 
								<table class="table text_wrap">
									<thead>
										<tr>
											<th class="text-center" style="width:30px;">
												<div class="custom-checkbox custom-checkbox-table custom-control">
													<input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad" class="custom-control-input" id="checkbox-all">
													<label for="checkbox-all" class="custom-control-label">&nbsp;</label>
												</div>
											</th>
											<th style="white-space: initial;">Client</th>
											<th style="white-space: initial;">Rating</th>
											<th style="white-space: initial;">Client ID</th>
											<th style="white-space: initial;">Status</th>
											<th style="white-space: initial;">Added From</th>
											<th style="white-space: initial;">Tag(s)</th>
											<th style="white-space: initial;">Sub Agent</th>
											<th style="white-space: initial;">Contact Source</th>
											<th style="white-space: initial;">Phone</th>
											<th style="white-space: initial;">Email</th>
											<th style="white-space: initial;">Street</th>
											<th style="white-space: initial;">City</th>
											<th style="white-space: initial;">State</th>
											<th style="white-space: initial;">Country</th>
											<th style="white-space: initial;">Country Of Passport</th>
											<th style="white-space: initial;">D.O.B</th>
											<th style="white-space: initial;">Added Date</th>
											<th style="white-space: initial;">Enquiry Date</th>
											<th style="white-space: initial;">Prospect Date</th>
											<th style="white-space: initial;">Client Date</th>
											<th style="white-space: initial;">Visa Expiry Date</th>
											<th style="white-space: initial;">Preferred Intake</th>
											<th style="white-space: initial;">Added By User</th>
											<th style="white-space: initial;">Added By Office</th>
											<th style="white-space: initial;">Assignee</th>
											<th style="white-space: initial;">Assignee Office</th>
											<th style="white-space: initial;">Followers</th>
										</tr> 
									</thead>
									<?php if(count($lists) >0): ?>
									<tbody class="tdata">	
										<?php if(@$totalData !== 0): ?>
										<?php $i=0; ?>
										<?php $__currentLoopData = @$lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr id="id_<?php echo e(@$list->id); ?>"> 
											<td style="white-space: initial;" class="text-center">
												<div class="custom-checkbox custom-control">
													<input data-id="<?php echo e(@$list->id); ?>" data-email="<?php echo e(@$list->email); ?>" data-name="<?php echo e(@$list->first_name); ?> <?php echo e(@$list->last_name); ?>" type="checkbox" data-checkboxes="mygroup" class="cb-element custom-control-input" id="checkbox-<?php echo e($i); ?>">
													<label for="checkbox-<?php echo e($i); ?>" class="custom-control-label">&nbsp;</label>
												</div>
											</td>
											<td style="white-space: initial;"><?php echo e(@$list->first_name == "" ? config('constants.empty') : str_limit(@$list->first_name, '50', '...')); ?> <?php echo e(@$list->last_name == "" ? config('constants.empty') : str_limit(@$list->last_name, '50', '...')); ?></td>
											<td style="white-space: initial;"><?php echo e(@$list->rating == "" ? config('constants.empty') : str_limit(@$list->rating, '50', '...')); ?> </td>
											<td style="white-space: initial;"><?php echo e(@$list->client_id == "" ? config('constants.empty') : str_limit(@$list->client_id, '50', '...')); ?> </td>
											<td style="white-space: initial;"><?php echo e(@$list->status == "" ? config('constants.empty') : str_limit(@$list->status, '50', '...')); ?> </td>
											<td>-</td>
											<td style="white-space: initial;"><?php echo e(@$list->tags == "" ? config('constants.empty') : str_limit(@$list->tags, '50', '...')); ?></td>
											<td>-</td>
											<td style="white-space: initial;"><?php echo e(@$list->source == "" ? config('constants.empty') : str_limit(@$list->source, '50', '...')); ?></td>
											<td style="white-space: initial;"><?php echo e(@$list->phone == "" ? config('constants.empty') : str_limit(@$list->phone, '50', '...')); ?></td>
											<td style="white-space: initial;"><a data-id="<?php echo e(@$list->id); ?>" data-email="<?php echo e(@$list->email); ?>" data-name="<?php echo e(@$list->first_name); ?> <?php echo e(@$list->last_name); ?>" href="javascript:;" class="clientemail"><?php echo e(@$list->email == "" ? config('constants.empty') : str_limit(@$list->email, '50', '...')); ?></a></td>
											<td style="white-space: initial;"><?php echo e(@$list->address == "" ? config('constants.empty') : str_limit(@$list->address, '50', '...')); ?></td>
											<td style="white-space: initial;"><?php echo e(@$list->city == "" ? config('constants.empty') : str_limit(@$list->city, '50', '...')); ?></td>
											<td style="white-space: initial;"><?php echo e(@$list->state == "" ? config('constants.empty') : str_limit(@$list->state, '50', '...')); ?></td>
											<td style="white-space: initial;"><?php echo e(@$list->country == "" ? config('constants.empty') : str_limit(@$list->country, '50', '...')); ?></td>
											<td style="white-space: initial;"><?php echo e(@$list->country_passport == "" ? config('constants.empty') : str_limit(@$list->country_passport, '50', '...')); ?></td>
											<td style="white-space: initial;"><?php echo e(@$list->dob == "" ? config('constants.empty') : date('d/m/Y',strtotime(@$list->dob))); ?></td>
											<td style="white-space: initial;"><?php echo e(@$list->created_at == "" ? config('constants.empty') : date('d/m/Y',strtotime(@$list->created_at))); ?></td>
											<td>-</td>
											<td>-</td>
											<td>-</td>
											<td style="white-space: initial;"><?php echo e(@$list->visaExpiry == "" ? config('constants.empty') : date('d/m/Y',strtotime(@$list->visaExpiry))); ?></td>
											<td style="white-space: initial;"><?php echo e(@$list->preferredIntake == "" ? config('constants.empty') : str_limit(@$list->preferredIntake, '50', '...')); ?></td>
											<td>-</td>
											<td>-</td>
											<td style="white-space: initial;"><?php echo e(@$list->assignee == "" ? config('constants.empty') : str_limit(@$list->assignee, '50', '...')); ?></td>
											<td>-</td>
											<td style="white-space: initial;"><?php echo e(@$list->followers == "" ? config('constants.empty') : str_limit(@$list->followers, '50', '...')); ?></td>
										</tr>
										<?php $i++; ?>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
										<?php endif; ?>										
									</tbody>  
									<?php else: ?>
										<tbody>
											<tr>
												<td style="text-align:center;" colspan="18">
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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\reports\client.blade.php ENDPATH**/ ?>