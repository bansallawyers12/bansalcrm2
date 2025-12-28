
<?php $__env->startSection('title', 'Office Check In'); ?>

<?php $__env->startSection('content'); ?>
<style>
.countAction {background: #1f1655;padding: 0px 5px;border-radius: 50%;color: #fff;margin-left: 5px;}
</style>
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
							<h4>In Person</h4>
							<div class="card-header-action">
								<a href="javascript:;" class="btn btn-primary opencheckin">Create In Person</a>
							</div>
						</div>
						<div class="card-body">
                            <?php
                            //if(\Auth::user()->role == 1){
                                $InPersonCount_All_type = \App\Models\CheckinLog::orderBy('created_at', 'desc')->count();

                                $InPersonCount_waiting_type = \App\Models\CheckinLog::where('status',0)->orderBy('created_at', 'desc')->count();

                                $InPersonCount_attending_type = \App\Models\CheckinLog::where('status',2)->orderBy('created_at', 'desc')->count();

                                $InPersonCount_completed_type = \App\Models\CheckinLog::where('status',1)->orderBy('created_at', 'desc')->count();

                                $InPersonCount_archived_type = \App\Models\CheckinLog::where('is_archived',1)->orderBy('created_at', 'desc')->count();

                            /*} else {
                                $InPersonCount_All_type = \App\Models\CheckinLog::where('user_id',Auth::user()->id)->where('id', '!=', '')->orderBy('created_at', 'desc')->count();

                                $InPersonCount_waiting_type = \App\Models\CheckinLog::where('user_id',Auth::user()->id)->where('status',0)->orderBy('created_at', 'desc')->count();

                                $InPersonCount_attending_type = \App\Models\CheckinLog::where('user_id',Auth::user()->id)->where('status',2)->orderBy('created_at', 'desc')->count();

                                $InPersonCount_completed_type = \App\Models\CheckinLog::where('user_id',Auth::user()->id)->where('status',1)->orderBy('created_at', 'desc')->count();

                                $InPersonCount_archived_type = \App\Models\CheckinLog::where('is_archived',1)->orderBy('created_at', 'desc')->count();

                            }*/ ?>
							<ul class="nav nav-pills" id="checkin_tabs" role="tablist">

								<li class="nav-item">
									<a class="nav-link " id="waiting-tab"  href="<?php echo e(URL::to('/admin/office-visits/waiting')); ?>" >Waiting <span class="countAction"><?php echo e($InPersonCount_waiting_type); ?></span></a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="attending-tab"  href="<?php echo e(URL::to('/admin/office-visits/attending')); ?>" >Attending <span class="countAction"><?php echo e($InPersonCount_attending_type); ?></span></a>
								</li>
								<li class="nav-item">
									<a class="nav-link active" id="completed-tab"  href="<?php echo e(URL::to('/admin/office-visits/completed')); ?>" >Completed <span class="countAction"><?php echo e($InPersonCount_completed_type); ?></span></a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="archived-tab"  href="<?php echo e(URL::to('/admin/office-visits/archived')); ?>" >Archived <span class="countAction"><?php echo e($InPersonCount_archived_type); ?></span></a>
								</li>
								<li class="nav-item">
									<a class="nav-link " id="all-tab"  href="<?php echo e(URL::to('/admin/office-visits')); ?>" >All <span class="countAction"><?php echo e($InPersonCount_All_type); ?></span></a>
								</li>
							</ul>
							<div class="tab-content" id="checkinContent">
							<div class="mydropdown" style="margin-top:10px;">
								  <button onclick="myFunction()" class="dropbtn">
								  <?php echo isset($_GET['office_name']) ? $_GET['office_name'] : 'All Branches'; ?>
								   <i style="font-size: 10px;" class="fa fa-arrow-down"></i></button>
								  <div id="myDropdown" class="dropdown-content">
								  <a href="<?php echo e(URL::to('/admin/office-visits/completed')); ?>">All Branches</a>
								  <?php
								  $branchs = \App\Models\Branch::all();
								  foreach($branchs as $branch){
									?>
									<a href="<?php echo e(URL::to('/admin/office-visits/completed')); ?>?office=<?php echo e($branch->id); ?>&office_name=<?php echo e($branch->office_name); ?>"><?php echo e($branch->office_name); ?></a>
								  <?php } ?>

								  </div>
								</div>
								<div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
									<div class="table-responsive common_table">
										<table class="table text_wrap">
											<thead>
												<tr>

													<th>ID</th>
													<th>Date</th>
													<th>Start</th>
													<th>End</th>
													<th>Session Time</th>
													<th>Contact Name</th>
													<th>Contact Type</th>
													<th>Visit Purpose</th>
													<th>Assignee</th>
												</tr>
											</thead>

											<tbody class="tdata checindata">
												<?php if(@$totalData !== 0): ?>
												<?php $__currentLoopData = @$lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<tr did="<?php echo e(@$list->id); ?>" id="id_<?php echo e(@$list->id); ?>">
													<td style="white-space: initial;"><a id="<?php echo e(@$list->id); ?>" class="opencheckindetail" href="javascript:;">#<?php echo e($list->id); ?></a></td>
													<td style="white-space: initial;"><a href="javascript:;"><?php echo e(date('l',strtotime($list->created_at))); ?></a><br><?php echo e(date('d/m/Y',strtotime($list->created_at))); ?></td>
													<td style="white-space: initial;"><?php if($list->sesion_start != ''){ echo date('h:i A',strtotime($list->sesion_start)); }else{ echo '-'; } ?></td>
													<td style="white-space: initial;"><?php if($list->sesion_end != ''){ echo date('h:i A',strtotime($list->sesion_end)); }else{ echo '-'; } ?></td>
													<td style="white-space: initial;"><?php echo e($list->attend_time); ?></td>
												<td>
													<?php
													if($list->contact_type == 'Lead'){
												$client = \App\Models\Lead::where('id', '=', $list->client_id)->first();
												 ?>
										    <a href="<?php echo e(route('admin.leads.detail', base64_encode(convert_uuencode(@$client->id)))); ?>"><?php echo e(@$client->first_name); ?> <?php echo e(@$client->last_name); ?></a>
										    <?php
										}else{
										    $client = \App\Models\Admin::where('role', '=', '7')->where('id', '=', $list->client_id)->first();
										    ?>
										    <a href="<?php echo e(URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$client->id)))); ?>"><?php echo e(@$client->first_name); ?> <?php echo e(@$client->last_name); ?></a>
										    <?php
										}

													?>
													<br>
													</td>
													<td style="white-space: initial;"><?php echo e($list->contact_type); ?></td>
													<td style="white-space: initial;"><?php echo e($list->visit_purpose); ?></td>
													<td style="white-space: initial;">
													<?php
													$admin = \App\Models\Admin::where('role', '!=', '7')->where('id', '=', $list->user_id)->first();
													?>
													<a href="<?php echo e(URL::to('/admin/users/view/'.@$admin->id)); ?>"><?php echo e(@$admin->first_name); ?> <?php echo e(@$admin->last_name); ?></a><br>
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
<?php $__env->startSection('scripts'); ?>
<script>
jQuery(document).ready(function($){
    $(document).delegate('.openassignee', 'click', function(){
        $('.assignee').show();
    });
     $(document).delegate('.closeassignee', 'click', function(){
        $('.assignee').hide();
    });
     $(document).delegate('.saveassignee', 'click', function(){
        var appliid = $(this).attr('data-id');
		$('.popuploader').show();
		$.ajax({
			url: site_url+'/admin/office-visits/change_assignee',
			type:'GET',
			data:{id: appliid,assinee: $('#changeassignee').val()},
			success: function(response){

				 var obj = $.parseJSON(response);
				if(obj.status){
				    alert(obj.message);
				location.reload();

				}else{
					alert(obj.message);
				}
			}
		});
    });
});
function myFunction() {
  document.getElementById("myDropdown").classList.toggle("show");
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\officevisits\completed.blade.php ENDPATH**/ ?>