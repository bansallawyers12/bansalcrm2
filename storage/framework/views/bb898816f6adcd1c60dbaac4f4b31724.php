
<?php $__env->startSection('title', 'Manage Contacts'); ?>

<?php $__env->startSection('content'); ?>

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Manage Contacts</h4>
							<div class="card-header-action">
								<a href="<?php echo e(route('admin.managecontact.create')); ?>" class="btn btn-primary">New Contacts</a>
								<a href="javascript:;" data-toggle="modal" data-target="#amnetsearch_modal" class="btn btn-primary"><i class="fas fa-search"></i></a>
							</div>
						</div>
						<div class="card-body">
							<table class="table">
								<thead>
									<tr>
										<th>ID</th>
										<th>Name</th>
										<th>Company Name</th>
										<th>Email</th>
										<th>Work Phone</th>
										<th>Receivables</th>
										<th>Unused Credits</th>
										<th></th>
									</tr> 
								</thead>
								<tbody class="tdata">	
								<?php if(@$totalData !== 0): ?>
								<?php $__currentLoopData = @$lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr id="id_<?php echo e(@$list->id); ?>"> 
										<td><?php echo e(@$list->id == "" ? config('constants.empty') : str_limit(@$list->id, '50', '...')); ?></td> 
										<td><?php echo e(@$list->srname == "" ? config('constants.empty') : str_limit(@$list->srname, '50', '...')); ?>

										<?php echo e(@$list->first_name == "" ? config('constants.empty') : str_limit(@$list->first_name, '50', '...')); ?>

										<?php echo e(@$list->middle_name == "" ? config('constants.empty') : str_limit(@$list->middle_name, '50', '...')); ?>

										<?php echo e(@$list->last_name == "" ? config('constants.empty') : str_limit(@$list->last_name, '50', '...')); ?></td> 
										<td><?php echo e(@$list->company_name == "" ? config('constants.empty') : str_limit(@$list->company_name, '50', '...')); ?></td> 
										<td><?php echo e(@$list->contact_email == "" ? config('constants.empty') : str_limit(@$list->contact_email, '50', '...')); ?></td>
										<td><?php echo e(@$list->contact_phone == "" ? config('constants.empty') : str_limit(@$list->contact_phone, '50', '...')); ?>, <?php echo e(@$list->work_phone == "" ? config('constants.empty') : str_limit(@$list->work_phone, '50', '...')); ?></td>
										<td></td>
										<td></td> 
										<td>
											<div class="dropdown d-inline">
												<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
												<div class="dropdown-menu">
													<a class="dropdown-item has-icon" href="<?php echo e(URL::to('/admin/contact/edit/'.base64_encode(convert_uuencode(@$list->id)))); ?>"><i class="far fa-edit"></i> Edit</a>
													<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction(<?php echo e(@$list->id); ?>, 'contacts')"><i class="fas fa-trash"></i> Delete</a>
												</div>
											</div>								  
										</td>
									</tr>	
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
								</tbody>
								<?php else: ?>
								<tbody> 
									<tr>
										<td style="text-align:center;" colspan="6">
											No Record found
										</td>
									</tr>
								</tbody>
								<?php endif; ?>
							</table> 
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


<div class="modal fade bd-example-modal-lg" id="amnetsearch_modal" tabindex="-1" role="dialog" aria-labelledby="amnetsearchModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="amnetsearchModalLabel">Search</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form action="<?php echo e(route('admin.managecontact.index')); ?>" method="get"> 
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="company_name" class="col-form-label">Company Name</label>
								<?php echo Form::text('company_name', Request::get('company_name'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Company Name', 'id' => 'company_name' )); ?>

							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="email" class="col-form-label">Email</label>
								<?php echo Form::text('email', Request::get('email'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Email', 'id' => 'name' )); ?>

							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="first_name" class="col-form-label">First Name</label>
								<?php echo Form::text('first_name', Request::get('first_name'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'First Name', 'id' => 'first_name' )); ?>

							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="last_name" class="col-form-label">Last Name</label>
								<?php echo Form::text('last_name', Request::get('last_name'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Last Name', 'id' => 'last_name' )); ?>

							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="phone" class="col-form-label">Phone</label>
								<?php echo Form::text('phone', Request::get('phone'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Phone', 'id' => 'phone' )); ?>

							</div>
						</div>
					</div>
					<div class="justify-content-between">
						<a href="<?php echo e(route('admin.managecontact.index')); ?>" class="btn btn-secondary" >Reset</a>
						<button type="submit" id="" class="btn btn-primary">Search</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\managecontact\index.blade.php ENDPATH**/ ?>