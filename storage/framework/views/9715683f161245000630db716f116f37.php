
<?php $__env->startSection('title', 'Teams'); ?>

<?php $__env->startSection('content'); ?>

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
		    <?php if(@$fetchedData): ?>
			<?php echo Form::open(array('url' => 'admin/teams/edit', 'name'=>"add-visatype", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")); ?> 
		    	<input type="hidden" name="id" value="<?php echo e($fetchedData->id); ?>">
			<?php else: ?>
			<?php echo Form::open(array('url' => 'admin/teams/store', 'name'=>"add-visatype", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")); ?> 
			<?php endif; ?>
				<div class="row">   
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Checklists</h4>
								<div class="card-header-action">
									<a href="<?php echo e(route('admin.teams.index')); ?>" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-3 col-md-3 col-lg-3">
			        	<?php echo $__env->make('../Elements/Admin/setting', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    		        </div>       
    				<div class="col-9 col-md-9 col-lg-9">
						<div class="card">
							<div class="card-body">
								<div id="accordion"> 
									<div class="accordion">
										<div class="accordion-header" role="button" data-toggle="collapse" data-target="#primary_info" aria-expanded="true">
											<h4>Primary Information</h4>
										</div>
										<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
											<div class="row"> 						
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group"> 
														<label for="name">Name <span class="span_req">*</span></label>
														<?php echo Form::text('name', @$fetchedData->name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Name' )); ?>

														<?php if($errors->has('name')): ?>
															<span class="custom-error" role="alert">
																<strong><?php echo e(@$errors->first('name')); ?></strong>
															</span> 
														<?php endif; ?>
													</div>
												</div>
										<div class="col-12 col-md-2 col-lg-2">
													<div class="form-group"> 
														<label for="name">Color <span class="span_req">*</span></label>
													<input data-valid="required" type="color" value="<?php echo e(@$fetchedData->color); ?>" name="color" class="">
													
													</div>
												</div>		
												
											</div>
										</div>
									</div>
								</div>
								<div class="form-group float-right">
									<?php echo Form::submit('Save', ['class'=>'btn btn-primary' ]); ?>

								</div> 
							</div>
						</div>	
						
						
						<div class="card">
							<div class="card-body">
								<div id="accordion"> 
									<div class="accordion">
										<div class="accordion-header" role="button" data-toggle="collapse" data-target="#primary_info" aria-expanded="true">
											<h4>Teams</h4>
										</div>
										<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
											<div class="table-responsive common_table"> 
								<table class="table text_wrap">
								<thead>
									<tr>
										<th>Name</th>
										<th>Color</th>
										<th>Action</th>
									</tr> 
								</thead>
								<?php if(@$totalData !== 0): ?>
								<?php $i=0; ?>
								<tbody class="tdata">	
								<?php $__currentLoopData = @$lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								
									<tr id="id_<?php echo e(@$list->id); ?>">
										
										<td><?php echo e(@$list->name == "" ? config('constants.empty') : str_limit(@$list->name, '50', '...')); ?></td> 	
										
										<td>
											<span style="background-color:<?php echo e($list->color); ?>;width: 40px;display: inline-block;">&nbsp;</span>						  
										</td>
										<td>
										<a class="dropdown-item has-icon" href="<?php echo e(URL::to('/admin/teams/edit/'.$list->id)); ?>" ><i class="fas fa-edit"></i> Edit</a>							  
										</td>
									</tr>	
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	 
								</tbody>
								<?php else: ?>
								<tbody>
									<tr>
										<td style="text-align:center;" colspan="7">
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
			 <?php echo Form::close(); ?>	
		</div>
	</section>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\teams\index.blade.php ENDPATH**/ ?>