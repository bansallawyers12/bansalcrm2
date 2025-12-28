
<?php $__env->startSection('title', 'Gen Settings'); ?>

<?php $__env->startSection('content'); ?>

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<?php echo Form::open(array('url' => 'admin/gen-settings/update', 'name'=>"add-visatype", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")); ?> 
			<?php echo Form::hidden('id', @$fetchedData->id); ?>

				<div class="row">   
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Enquiry source</h4>
								<div class="card-header-action">
									<a href="<?php echo e(route('admin.gensettings.index')); ?>" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
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
											<h4>Settings</h4>
										</div>
										<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
											<div class="row"> 						
												<div class="col-12 col-md-12 col-lg-12">
												   
											<div class="form-group">
											    <label>Date Format</label>
											    <ul style="list-style:none;">
											        <li><label><input type="radio" <?php if( $setting && $setting->date_format == 'F j, Y'){ echo 'checked'; } ?> name="date_format" value="F j, Y"> <?php echo e(date('F j, Y')); ?> <span>(F j, Y)</span></label></li>
											              <li><label><input type="radio" <?php if($setting && $setting->date_format == 'Y-m-d'){ echo 'checked'; } ?> name="date_format" value="Y-m-d"> <?php echo e(date('Y-m-d')); ?> <span>(Y-m-d)</span></label></li>
											              <li><label><input type="radio" <?php if($setting && $setting->date_format == 'm/d/Y'){ echo 'checked'; } ?> name="date_format" value="m/d/Y"> <?php echo e(date('m/d/Y')); ?> <span>(m/d/Y)</span></label></li>
											                <li><label><input type="radio" <?php if($setting && $setting->date_format == 'd/m/Y'){ echo 'checked'; } ?> name="date_format" value="d/m/Y"> <?php echo e(date('d/m/Y')); ?> <span>(d/m/Y)</span></label></li>
											    </ul>
											    
											</div>
												</div>
											<div class="col-12 col-md-12 col-lg-12">
												   
											<div class="form-group">
											    <label>Time Format</label>
											    <ul style="list-style:none;">
											        <li><label><input type="radio" <?php if($setting && $setting->time_format == 'g:i a'){ echo 'checked'; } ?> name="time_format" value="g:i a"> <?php echo e(date('g:i a')); ?> <span>(g:i a)</span></label></li>
											              <li><label><input type="radio" <?php if($setting && $setting->time_format == 'g:i A'){ echo 'checked'; } ?> name="time_format" value="g:i A"> <?php echo e(date('g:i A')); ?> <span>(g:i A)</span></label></li>
											              <li><label><input type="radio" <?php if($setting && $setting->time_format == 'H:i'){ echo 'checked'; } ?> name="time_format" value="H:i"> <?php echo e(date('H:i')); ?> <span>(H:i)</span></label></li>
											              
											    </ul>
											    
											</div>
												</div>		
											</div>
										</div>
									</div>
								</div>
								<div class="form-group float-right">
									<?php echo Form::submit('Update', ['class'=>'btn btn-primary' ]); ?>

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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\gensettings\index.blade.php ENDPATH**/ ?>