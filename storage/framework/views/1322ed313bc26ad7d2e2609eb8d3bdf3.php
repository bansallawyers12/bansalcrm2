
<?php $__env->startSection('title', 'Edit Checklist'); ?>

<?php $__env->startSection('content'); ?>
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<?php echo Form::open(array('url' => 'admin/documentchecklist/edit', 'name'=>"edit-checklist", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")); ?>

			<?php echo Form::hidden('id', @$fetchedData->id); ?>

				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Edit Checklist</h4>
								<div class="card-header-action">
									<a href="<?php echo e(route('admin.feature.documentchecklist.index')); ?>" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
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
											<h4>Checklist Information</h4>
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

                                                <div class="col-12 col-md-4 col-lg-4">
                                                    <div class="form-group"> <?php //dd($fetchedData->doc_type);?>
                                                        <label for="doc_type">Document Type <span class="span_req">*</span></label>
                                                        <select name="doc_type" class="form-control" data-valid="required">
                                                            <option value="">Select Document Type</option>
                                                            <option value="1" <?php if ( isset($fetchedData->doc_type) &&  $fetchedData->doc_type == 1 ) { echo 'selected';} else{ echo '';} ?>>Personal</option>
                                                            <option value="2" <?php if ( isset($fetchedData->doc_type) &&  $fetchedData->doc_type == 2 ) { echo 'selected';} else{ echo '';} ?>>Visa</option>
                                                        </select>
                                                        <?php if($errors->has('doc_type')): ?>
                                                            <span class="custom-error" role="alert">
                                                                <strong><?php echo e($errors->first('doc_type')); ?></strong>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group float-right">
									<?php echo Form::submit('Update Checklist', ['class'=>'btn btn-primary' ]); ?>

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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\feature\documentchecklist\edit.blade.php ENDPATH**/ ?>