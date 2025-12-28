
<?php $__env->startSection('title', 'Task Report'); ?>

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
							<h4>Office Visit Report Date wise</h4>
						</div>
						<div class="card-body">
							<div class="tab-content" id="checkinContent">
								<div class="tab-pane fade show active" id="office" role="tabpanel" aria-labelledby="office-tab">
									<div class="table-responsive common_table">
										<table class="table text_wrap">
											<thead>
												<tr>
                                                    <th>Sno.</th>
													<th>Date</th>
                                                    <th>Office</th>
													<th>No of person</th>
												</tr>
											</thead>
											<?php if(count($lists) >0): ?>
											<tbody class="tdata">
												<?php if(@$totalData !== 0): ?>
												<?php $__currentLoopData = @$lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

												<tr>
                                                    <td><?php echo e(++$i); ?></td>
													<td><?php echo e(@$list->date == "" ? config('constants.empty') : date('d/m/Y',strtotime($list->date))); ?></td>
                                                    <td><?php echo e(@$list->office_name == "" ? config('constants.empty') : $list->office_name); ?></td>
													<td><?php echo e(@$list->person_count ?? 0); ?></td>
                                                </tr>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												<?php endif; ?>
											</tbody>
											<?php else: ?>
												<tbody>
													<tr>
														<td style="text-align:center;" colspan="3">
															No Record found
														</td>
													</tr>
												</tbody>
											<?php endif; ?>
										</table>
                                        <?php echo $lists->appends($_GET)->links(); ?>

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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\reports\noofpersonofficevisit.blade.php ENDPATH**/ ?>