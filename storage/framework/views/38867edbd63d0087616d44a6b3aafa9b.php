
<?php $__env->startSection('title', 'Archived Enquiries'); ?>

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
							<h4>Archived Queries</h4>
							<div class="card-header-action">
								<div class="drop_table_data" style="display: inline-block;margin-right: 10px;">
									<button type="button" class="btn btn-primary dropdown-toggle"><i class="fas fa-columns"></i></button>
									<div class="dropdown_list">
										<label class="dropdown-option all"><input type="checkbox" value="all" checked /> Display All</label>
										<label class="dropdown-option"><input type="checkbox" value="2" checked /> Email</label>
										<label class="dropdown-option"><input type="checkbox" value="3" checked /> Phone</label>
										<label class="dropdown-option"><input type="checkbox" value="4" checked /> Country</label>
										<label class="dropdown-option"><input type="checkbox" value="5" checked /> Current City</label>
										<label class="dropdown-option"><input type="checkbox" value="6" checked /> Added On</label>
										<label class="dropdown-option"><input type="checkbox" value="7" checked /> Archived By</label>
										<label class="dropdown-option"><input type="checkbox" value="8" checked /> Archived On</label>
										
									</div>
								</div>
								
							</div>
						</div>
						<div class="card-body">
							<ul class="nav nav-pills" id="client_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link " id="active-tab"  href="<?php echo e(URL::to('/admin/enquiries')); ?>" >Enquiries</a>
								</li>
								<li class="nav-item">
									<a class="nav-link active" id="inactive-tab"  href="<?php echo e(URL::to('/admin/enquiries/archived-enquiries')); ?>" >Archived</a>
								</li>
							</ul> 
							<div class="tab-content" id="clientContent">								
								<div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
									<div class="table-responsive common_table"> 
										<table class="table text_wrap">
											<thead>
												<tr> 
													<th>Name</th>
													<th>Email</th>
													<th>Phone</th>
													<th>Country</th>
													<th>Current City</th>
                                                    <th>Subject</th>
													<th>Message</th>
													<th>Source</th>
													<th>Added On</th>
													<th>Archived By</th>
													<th>Archived On</th>
													<th></th>
												</tr> 
											</thead>
											<?php
										$query =\App\Models\Enquiry::where('is_archived',1)->orderby('created_at','Desc');
										$totalcount = $query->count();
										$enqs = $query->get();
										if($totalcount !== 0){
									?>
									<tbody class="referredclienttdata">
												<?php
													foreach($enqs as $enqlist){
														$admin = \App\Models\Admin::where('id', $enqlist->archived_by)->first();
												?>
												<tr id="id_<?php echo e($enqlist->id); ?>">
													<td><?php echo e($enqlist->first_name ?? 'N/A'); ?> <?php echo e($enqlist->last_name ?? 'N/A'); ?></td>
													<td><?php echo e($enqlist->email); ?></td>
													<td><?php echo e($enqlist->phone); ?></td>
													<td><?php echo e($enqlist->country); ?></td>
													<td><?php echo e($enqlist->city); ?></td>
                                                    <td><?php echo e($enqlist->subject); ?></td>
													<td><?php echo e($enqlist->message); ?></td>
													<td><?php echo e($enqlist->source); ?></td>
													<td><?php echo e(date('d/m/Y', strtotime($enqlist->created_at))); ?></td>
													<td><?php echo e($admin->first_name ?? 'N/A'); ?> <?php echo e($admin->last_name ?? 'N/A'); ?></td>
													<td><?php echo e(date('d/m/Y', strtotime($enqlist->archived_date))); ?></td>
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">
																
																<a class="dropdown-item has-icon" href="javascript:;" onclick="movetoclientAction(<?php echo e($enqlist->id); ?>, 'enquiries','is_archived')">Move to Enquiries</a>
															</div>
														</div>								  
													</td>
												</tr>
												<?php
													} 
												?>
											</tbody>
										<?php
									} else{ ?>	
											<tbody>
												<tr>
													<td style="text-align:center;" colspan="9">
														No Record found
													</td>
												</tr>
											</tbody>
									<?php } ?>
										</table>
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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\enquire\archived.blade.php ENDPATH**/ ?>