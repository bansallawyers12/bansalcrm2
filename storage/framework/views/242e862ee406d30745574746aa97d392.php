
<?php $__env->startSection('title', 'Clients'); ?>

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
							<h4>All Clients</h4>
							<div class="card-header-action">
								<a href="<?php echo e(route('admin.clients.create')); ?>" class="btn btn-primary">Create Client</a>
							</div>
						</div>
						<div class="card-body">
							<ul class="nav nav-pills" id="client_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" id="prospects-tab"  href="<?php echo e(URL::to('/admin/prospects')); ?>" >Prospects</a>
								</li>
								<li class="nav-item">
									<a class="nav-link " id="clients-tab"  href="<?php echo e(URL::to('/admin/clients')); ?>" >Clients</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="archived-tab"  href="<?php echo e(URL::to('/admin/archived')); ?>" >Archived</a>
								</li>
							</ul> 
							<div class="tab-content" id="clientContent">
								<div class="tab-pane fade show active" id="prospects" role="tabpanel" aria-labelledby="prospects-tab">
									<div class="table-responsive"> 
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
													<th>Added From</th>
													<th>Tag(s)</th>
													<th>Rating</th>
													<th>Internal ID</th>
													<th>Client ID</th>
													<th>Followers</th>
													<th>Phone</th>
													<th>Passport Number</th>
													<th>Passport</th>
													<th>Current City</th>
													<th>Assignee</th>
													<th>Added On</th>
													<th>Last Updated</th>
													<th>Preferred Intake</th>
													<th></th>
												</tr> 
											</thead>
											
											<tbody class="tdata">	
												<tbody>
												<tr>
													<td style="text-align:center;" colspan="17">
														No Record found
													</td>
												</tr>
											</tbody>
										</table>
									</div>	
								</div>
								
							</div> 
						</div>
						<div class="card-footer">
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\prospects\index.blade.php ENDPATH**/ ?>