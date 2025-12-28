
<?php $__env->startSection('title', 'Offices'); ?>

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
							<h4>Offices</h4>
							<div class="card-header-action">
								<a href="<?php echo e(route('admin.branch.index')); ?>" class="btn btn-primary">Office List</a>
							</div>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
									<div class="row">
										<div class="col-md-4"></div>
										<div class="col-md-2">
											<h5>Overview</h5>
										</div>
										<div class="col-md-3">
											<h5>TOTAL USERS : <?php echo e(\App\Models\Admin::where('role', 1)->where('office_id',$fetchedData->id)->count()); ?></h5>
										</div>
										<div class="col-md-3">
											<h5>TOTAL CLIENTS : <?php echo e(\App\Models\Admin::where('role', 7)->where('office_id',$fetchedData->id)->count()); ?></h5>
										</div>
									</div>
									
								</div>
							</div>
							
						</div>
					</div>
					<div class="card">
						<div class="card-header">
							<h4>Office Information</h4>
							<div class="card-header-action">
								<a href="<?php echo e(URL::to('/admin/branch/edit/'.base64_encode(convert_uuencode(@$fetchedData->id)))); ?>" class="btn btn-primary">Edit</a>
							</div>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-6">
									<h4><?php echo e($fetchedData->office_name); ?> <span class="btn btn-warning">Active</span></h4>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<table class="table">
										<tr>
											<td><b>Email:</b></td>
											<td><?php echo e($fetchedData->email); ?></td>
										</tr>
										<tr>
											<td><b>Mobile:</b></td>
											<td><?php echo e($fetchedData->mobile); ?></td>
										</tr>
										<tr>
											<td><b>Phone:</b></td>
											<td><?php echo e($fetchedData->phone); ?></td>
										</tr>
										<tr>
											<td><b>Person to Contact:</b></td>
											<td><?php echo e($fetchedData->contact_person); ?></td>
										</tr>
									</table>
								</div>
								<div class="col-md-6">
									<table class="table">
										<tr>
											<td><b>Street:</b></td>
											<td><?php echo e($fetchedData->address); ?></td>
										</tr>
										<tr>
											<td><b>City:</b></td>
											<td><?php echo e($fetchedData->city); ?></td>
										</tr>
										<tr>
											<td><b>State:</b></td>
											<td><?php echo e($fetchedData->state); ?></td>
										</tr>
										<tr>
											<td><b>Zip/Post Code:</b></td>
											<td><?php echo e($fetchedData->zip); ?></td>
										</tr>
										<tr>
											<td><b>Country:</b></td>
											<td><?php echo e($fetchedData->country); ?></td>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="card">
						
						<div class="card-body">
							<ul class="nav nav-pills" id="client_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link "  id="clients-tab" href="<?php echo e(URL::to('/admin/branch/view/')); ?>/<?php echo e($fetchedData->id); ?>" role="tab" >User List</a>
								</li>
								<li class="nav-item">
									<a class="nav-link active"  id="date-tab" href="<?php echo e(URL::to('/admin/branch/view/client/')); ?>/<?php echo e($fetchedData->id); ?>" role="tab" >Client List</a>
								</li>
								
							</ul> 
							<div class="tab-content" id="clientContent" style="padding-top:15px;">
								<div class="tab-pane fade show active" id="date" role="tabpanel" aria-labelledby="date-tab">
																		
									<div class="table-responsive"> 
										<table class="table text_wrap table-2">
											<thead>
												<tr>
													<th>Name</th>
													<th>DOB</th>
													<th>Email</th>
													<th>Workflow</th>
													<th>Added By</th>
													<th>Office</th>
													
												</tr> 
											</thead>
											<tbody class="applicationtdata">
											<?php
											$lists = \App\Models\Admin::where('role', 7)->where('office_id',$fetchedData->id)->with(['usertype'])->paginate(10);
											foreach($lists as $alist){
												$b = \App\Models\Branch::where('id', $alist->office_id)->first();
												?>
												<tr id="id_<?php echo e($alist->id); ?>">
													<td><a class="" data-id="<?php echo e($alist->id); ?>" href="<?php echo e(URL::to('/admin/clients/detail')); ?>/<?php echo e(base64_encode(convert_uuencode(@$alist->id))); ?>" style="display:block;"><?php echo e($alist->first_name); ?></a> </td> 
													<td><?php echo e($alist->dob); ?></td>
													<td><?php echo e($alist->email); ?></td>
													<td></td>
													
													<td></td>
													
													<td><?php echo e($b->office_name); ?></td> 
													
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
									<?php echo $lists->appends(\Request::except('page'))->render(); ?>

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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\branch\viewclient.blade.php ENDPATH**/ ?>