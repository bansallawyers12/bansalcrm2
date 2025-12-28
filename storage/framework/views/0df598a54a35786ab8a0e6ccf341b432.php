
<?php $__env->startSection('title', 'Agents'); ?>

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
							<h4>All Agents</h4>
							<div class="card-header-action">
								<a href="<?php echo e(route('admin.agents.create')); ?>" class="btn btn-primary">Create Agent</a>
							</div>
						</div>
						<div class="card-body">
							<ul class="nav nav-pills" id="client_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link" id="active-tab"  href="<?php echo e(URL::to('/admin/agents/active')); ?>" >Active</a>
								</li>
								<li class="nav-item">
									<a class="nav-link active" id="inactive-tab"  href="<?php echo e(URL::to('/admin/agents/inactive')); ?>" >Inactive</a>
								</li>
							</ul> 
							<div class="tab-content" id="clientContent">								
								<div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
									<div class="table-responsive common_table"> 
										<table class="table text_wrap">
											<thead>
												<tr> 
													<th class="text-center" style="width:30px;">
														<div class="custom-checkbox custom-checkbox-table custom-control">
															<input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad" class="custom-control-input" id="checkbox-all">
															<label for="checkbox-all" class="custom-control-label">&nbsp;</label>
														</div>
													</th>	
													<th>Name</th>
													<th>Type</th>
													<th>Structure</th>
													<!--<th>Phone</th>-->
													<th>City</th>
													<th>Associated Office</th>
													<th>Clients Count</th>
													<th>Applications Count</th>
													<th>Status</th>
												</tr> 
											</thead>
											
											<tbody class="tdata">	
												<?php if(@$totalData !== 0): ?>
												<?php $__currentLoopData = @$lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<tr id="id_<?php echo e(@$list->id); ?>"> 
													<td style="white-space: initial;" class="text-center">
														<div class="custom-checkbox custom-control">
															<input type="checkbox" data-checkboxes="mygroup" class="custom-control-input" id="checkbox-1">
															<label for="checkbox-1" class="custom-control-label">&nbsp;</label>
														</div>
													</td>
													<td style="white-space: initial;"><a href="<?php echo e(URL::to('/admin/agent/detail/'.base64_encode(convert_uuencode(@$list->id)))); ?>"><?php echo e(@$list->full_name == "" ? config('constants.empty') : str_limit(@$list->full_name, '50', '...')); ?></a> <br/></td> 
													<td style="white-space: initial;"><?php echo e(@$list->agent_type == "" ? config('constants.empty') : str_limit(@$list->agent_type, '50', '...')); ?></td>
													<td style="white-space: initial;"><?php echo e(@$list->struture == "" ? config('constants.empty') : str_limit(@$list->struture, '50', '...')); ?></td>
													<!--<td>--><!--</td>-->	
													<td style="white-space: initial;"><?php echo e(@$list->city == "" ? config('constants.empty') : str_limit(@$list->city, '50', '...')); ?></td> 	
													<td style="white-space: initial;"><?php echo e(@$list->related_office == "" ? config('constants.empty') : str_limit(@$list->related_office, '50', '...')); ?></td> 	
													<td style="white-space: initial;">0</td> 	
													<td style="white-space: initial;">0</td> 	
													<td style="white-space: initial;">
													    <div class="custom-switches">
									<label class="">
										<input value="1" data-id="<?php echo e(@$list->id); ?>"  data-status="<?php echo e(@$list->status); ?>" data-col="status" data-table="agents" type="checkbox" name="custom-switch-checkbox" class="change-status custom-switch-input" <?php echo e((@$list->status == 1 ? 'checked' : '')); ?>>
										<span class="custom-switch-indicator"></span>
									</label>
								</div>
													</td>
												</tr>	
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
											</tbody>
											<?php else: ?>
											<tbody>
												<tr>
													<td style="text-align:center;" colspan="10">
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
						<div class="card-footer">
							<?php echo $lists->appends(\Request::except('page'))->render(); ?>

						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<div class="modal fade clientemail custom_modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">Compose Email</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" autocomplete="off" enctype="multipart/form-data">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">From <span class="span_req">*</span></label>
								<?php echo Form::text('email_from', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter From' )); ?>

								<?php if($errors->has('email_from')): ?>
									<span class="custom-error" role="alert">
										<strong><?php echo e(@$errors->first('email_from')); ?></strong>
									</span> 
								<?php endif; ?>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_to">To <span class="span_req">*</span></label>
								<?php echo Form::text('email_to', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter To' )); ?>

								<?php if($errors->has('email_to')): ?>
									<span class="custom-error" role="alert">
										<strong><?php echo e(@$errors->first('email_to')); ?></strong>
									</span> 
								<?php endif; ?>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="subject">Subject <span class="span_req">*</span></label>
								<?php echo Form::text('subject', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Subject' )); ?>

								<?php if($errors->has('subject')): ?>
									<span class="custom-error" role="alert">
										<strong><?php echo e(@$errors->first('subject')); ?></strong>
									</span> 
								<?php endif; ?>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Message <span class="span_req">*</span></label>
								<textarea class="summernote-simple" name="message"></textarea>
								<?php if($errors->has('message')): ?>
									<span class="custom-error" role="alert">
										<strong><?php echo e(@$errors->first('message')); ?></strong>
									</span>  
								<?php endif; ?>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button type="submit" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\agents\inactive.blade.php ENDPATH**/ ?>