
<?php $__env->startSection('title', 'Partner Type'); ?>
 
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
				 <div class="col-3 col-md-3 col-lg-3">
			        	<?php echo $__env->make('../Elements/Admin/setting', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
		        </div>       
				<div class="col-9 col-md-9 col-lg-9">
					<div class="card">
						<div class="card-header">
							<h4>All Partner Type</h4>
							<div class="card-header-action">
								<a href="<?php echo e(route('admin.feature.partnertype.create')); ?>" class="btn btn-primary">Create Partner Type</a>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive common_table"> 
								<table class="table text_wrap">
								<thead>
									<tr>
										
										<th>Name</th>
										<th>Master Category</th>
										<th></th>
									</tr> 
								</thead>
								<?php if(@$totalData !== 0): ?>
								<?php $i=0; ?>
								<tbody class="tdata">	
								<?php $__currentLoopData = @$lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr id="id_<?php echo e(@$list->id); ?>">
									
										<td><?php echo e(@$list->name == "" ? config('constants.empty') : str_limit(@$list->name, '50', '...')); ?></td> 	
										<td><?php echo e(@$list->categorydata->category_name == "" ? config('constants.empty') : str_limit(@$list->categorydata->category_name, '50', '...')); ?></td> 	
										<td>
											<div class="dropdown d-inline">
												<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
												<div class="dropdown-menu">
													<a class="dropdown-item has-icon" href="<?php echo e(URL::to('/admin/partner-type/edit/'.base64_encode(convert_uuencode(@$list->id)))); ?>"><i class="far fa-edit"></i> Edit</a>
													<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction(<?php echo e(@$list->id); ?>, 'partner_types')"><i class="fas fa-trash"></i> Delete</a>
												</div>
											</div>								  
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
	</section>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script>
jQuery(document).ready(function($){	
	$('.cb-element').change(function () {		
	if ($('.cb-element:checked').length == $('.cb-element').length){
	  $('#checkbox-all').prop('checked',true);
	}
	else {
	  $('#checkbox-all').prop('checked',false);
	}  
	/* if ($('.cb-element:checked').length > 0){
			$('.is_checked_client').show();
			$('.is_checked_clientn').hide();
		}else{
			$('.is_checked_client').hide();
			$('.is_checked_clientn').show();
		} */
	});	
});	
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\feature\partnertype\index.blade.php ENDPATH**/ ?>