
<?php $__env->startSection('title', 'Applications overdue'); ?>

<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
    .filter_panel {background: #f7f7f7;margin-bottom: 10px;border: 1pxsolid #eee;display: none;}
.card .card-body .filter_panel { padding: 20px;}
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
							<h4>All Overdue Applications</h4>
							<div class="card-header-action is_checked_clientn">
								<a href="#" class="btn btn-primary importmodal"> Import csv</a>
							</div>
							<div class="card-header-action">
							        <a href="javascript:;" class="btn btn-theme btn-theme-sm filter_btn"><i class="fas fa-filter"></i> Filter</a>
							 </div>
						</div>
						<div class="card-body">
                            <ul class="nav nav-pills" id="application_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link" id="applications-tab"  href="<?php echo e(URL::to('/admin/applications')); ?>" >All</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="applications-overdue-tab"  href="<?php echo e(URL::to('/admin/applications-overdue')); ?>" >Overdue</a>
								</li>
                                <li class="nav-item">
									<a class="nav-link" id="applications-finalize-tab"  href="<?php echo e(URL::to('/admin/applications-finalize')); ?>" >Finalized</a>
								</li>
							</ul>
						    <div class="filter_panel">
								<h4>Search By Details</h4>
								<form action="<?php echo e(URL::to('/admin/applications-finalize')); ?>" method="get">
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label for="ass_id" class="col-form-label">Assignee</label>
												<?php echo Form::text('ass_id', Request::get('ass_id'), array('class' => 'form-control assignee', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Assignee', 'id' => 'ass_id', 'onkeyup' => "suggestassignee(this.value)" )); ?>

											</div>
											<input type="hidden" value="<?php echo e(Request::get('assignee')); ?>" id="assigneeid" name="assignee">
										</div>
										<div class="col-md-4">
											<div class="form-group">
											    <?php
											    //$par = \App\Models\Partner::where('id', Request::get('partner'))->first();
											    ?>
												<label for="partner" class="col-form-label">Partner</label>
												
                                                <!--<input type="hidden" value="<?php echo e(Request::get('partner')); ?>" id="partnerid" name="partner">-->

                                                <select class="form-control" name="partner">
												    <option value="">Select Partner</option>
												    <?php $__currentLoopData = $allpartners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allpartner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option <?php if(Request::get('partner') == $allpartner->id): ?> selected <?php endif; ?> value="<?php echo e($allpartner->id); ?>"><?php echo e($allpartner->partner_name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>
                                            </div>
										</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
											<div class="form-group">
												<label for="" class="col-form-label">Stage</label>
												<select class="form-control" name="stage">
												    <option value="">Select Stage</option>
												    <?php $__currentLoopData = $allstages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allstage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											        <option <?php if(Request::get('stage') == $allstage->stage): ?> selected <?php endif; ?> value="<?php echo e($allstage->stage); ?>"><?php echo e($allstage->stage); ?></option>
											        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>
											</div>
										</div>

                                        <div class="col-md-4">
											<div class="form-group">
												<label for="" class="col-form-label">Status</label>
												<select class="form-control" name="status">
												    <option value="">Select Status</option>
                                                    <option value="0" <?php if(Request::get('status') == 0): ?> selected <?php endif; ?>>In Progress</option>
                                                    <option value="1" <?php if(Request::get('status') == 1): ?> selected <?php endif; ?>>Completed</option>
                                                    <option value="2" <?php if(Request::get('status') == 2): ?> selected <?php endif; ?>>Discontinued</option>
                                                    <option value="3" <?php if(Request::get('status') == 3): ?> selected <?php endif; ?>>Cancelled</option>
                                                </select>
											</div>
										</div>
                                    </div>

									<div class="row">
										<div class="col-md-12 text-center">
                                            <?php echo Form::submit('Search', ['class'=>'btn btn-primary btn-theme-lg' ]); ?>

											<a class="btn btn-info" href="<?php echo e(URL::to('/admin/applications-finalize')); ?>">Reset</a>
										</div>
									</div>
								</form>
							</div>
							<div class="table-responsive">
								<table class="table text_wrap">
									<thead>
										<tr>
											<th>Application ID</th>
											<th>Client Name</th>

											<!--<th>Client Phone</th>-->
											<th>Client Assignee</th>

											<th>Product</th>
											<th>Partner</th>
											<th>Partner Branch</th>

											<th>Workflow</th>
											<th>Stage</th>

											<th>Branch</th>
											<th>Status</th>

											<th>Created At</th>
											<th></th>
										</tr>
									</thead>
									<?php if(@$totalData !== 0): ?>
									<?php $__currentLoopData = @$lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php
									$productdetail = \App\Models\Product::where('id', $list->product_id)->first();
									$partnerdetail = \App\Models\Partner::where('id', $list->partner_id)->first();
									$clientdetail = \App\Models\Admin::where('id', $list->client_id)->first();
									$PartnerBranch = \App\Models\PartnerBranch::where('id', $list->branch)->first();
									$workflow = \App\Models\Workflow::where('id', $list->workflow)->first();
									?>
									<tbody class="tdata">
										<tr id="id_<?php echo e(@$list->id); ?>">
											<td style="white-space: initial;"><a href="<?php echo e(URL::to('admin/clients/detail/')); ?>/<?php echo e(base64_encode(convert_uuencode(@$clientdetail->id))); ?>?tab=application&appid=<?php echo e(@$list->id); ?>"><?php echo e(@$list->id == "" ? config('constants.empty') : str_limit(@$list->id, '50', '...')); ?></a></td>
											<td style="white-space: initial;"><a href="<?php echo e(URL::to('admin/clients/detail/')); ?>/<?php echo e(base64_encode(convert_uuencode(@$clientdetail->id))); ?>?tab=application"><?php echo e(@$clientdetail->first_name); ?> <?php echo e(@$clientdetail->last_name); ?></a><!--<br/>--></td>

											
											<td style="white-space: initial;"><?php echo e(@$list->application_assignee->first_name); ?></td>

											<td style="white-space: initial;"><?php echo e(@$productdetail->name); ?></td>
											<td style="white-space: initial;"><?php echo e(@$partnerdetail->partner_name); ?></td>
											<td style="white-space: initial;"><?php echo e($PartnerBranch->name ?? 'N/P'); ?></td>

											<td style="white-space: initial;"><?php echo @$workflow->name; ?></td>
											<td style="white-space: initial;"><?php echo e(@$list->stage == "" ? config('constants.empty') : str_limit(@$list->stage, '50', '...')); ?></td>

											<td style="white-space: initial;"><?php echo e($PartnerBranch->name ?? 'N/P'); ?></td>
											<td>
                                                <?php if($list->status == 0){ ?>
                                                    <span class="ag-label--circular" style="color: #6777ef" >In Progress</span>
                                                <?php }else if($list->status == 1){ ?>
                                                    <span class="ag-label--circular" style="color: #6777ef" >Completed</span>
                                                <?php } else if($list->status == 2){ ?>
                                                    <span class="ag-label--circular" style="color: red;" >Discontinued</span>
                                                <?php } else if($list->status == 3){ ?>
                                                    <span class="ag-label--circular" style="color: red;" >Cancelled</span>
                                                <?php }?>
                                            </td>

											<!--<td><?php echo e(@$list->created_at == "" ? config('constants.empty') : str_limit(@$list->created_at , '50', '...')); ?></td>-->
											<td style="white-space: initial;"><?php echo e(@$list->created_at == "" ? config('constants.empty') : date('d/m/Y', strtotime($list->created_at))); ?></td>

                                            <td>
												<div class="dropdown d-inline">
													<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
													<div class="dropdown-menu">


													</div>
												</div>
											</td>
										</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</tbody>
									<?php else: ?>
									<tbody>
										<tr>
											<td style="text-align:center;" colspan="12">
												No Record found
											</td>
										</tr>
									</tbody>
									<?php endif; ?>
								</table>
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
<div id="importmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="importmodalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="importmodalLabel">Import CSV</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="<?php echo e(URL::to('/admin/applications-import')); ?>"  enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="import">Select Import File<span class="span_req">*</span></label>
								<input type="file" required class="form-control" name="uploaded_file" id="uploaded_file">
								<small class="warning text-muted">Please upload only CSV file</small>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button  type="submit" class="btn btn-primary">Import</button>
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
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script>
    let currentUrl = window.location.href; //console.log(currentUrl);
    var currentUrlArr = currentUrl.split("/"); //console.log(currentUrlArr[4]);
    if( currentUrlArr.length >0 ){
        if(currentUrlArr[4] == 'applications'){
            $('a#applications-tab').addClass('active');
        } else if(currentUrlArr[4] == 'applications-overdue'){
            $('a#applications-overdue-tab').addClass('active');
        } else if(currentUrlArr[4] == 'applications-finalize'){
            $('a#applications-finalize-tab').addClass('active');
        }
    }

 function suggest(inputString) {
$( ".agent_company_name" ).autocomplete({
	autoFocus: true,
	minLength : 2,
	source : function(request, response) {
    $.ajax({
        type: "GET",
        url: "<?php echo e(URL::to('/')); ?>/admin/getpartnerajax",
        dataType : "json",
        cache : false,
        data: {likewith : 'agent_company_name', likevalue: inputString},
        success:
            function(data){
                var all_l=[];
                for(var i=0;i<data.length;i++)
                {
                    var city_name=data[i].agent_company_name;
                     var y=data[i].id;
                    all_l.push({ "label": city_name, "value": city_name , "s": y } );
                }
                response(all_l);
            }
        });
    },
});

}

$('.agent_company_name').on('autocompleteselect', function (e, ui) {
      $('#partnerid').val(ui.item.s);
    });

 function suggestassignee(inputString) {
$( ".assignee" ).autocomplete({
	autoFocus: true,
	minLength : 2,
	source : function(request, response) {
    $.ajax({
        type: "GET",
        url: "<?php echo e(URL::to('/')); ?>/admin/getassigneeajax",
        dataType : "json",
        cache : false,
        data: {likewith : 'assignee', likevalue: inputString},
        success:
            function(data){
                var all_l=[];
                for(var i=0;i<data.length;i++)
                {
                    var city_name=data[i].assignee;
                     var y=data[i].id;
                    all_l.push({ "label": city_name, "value": city_name , "s": y } );
                }
                response(all_l);
            }
        });
    },
});

}
$('.assignee').on('autocompleteselect', function (e, ui) {
      $('#assigneeid').val(ui.item.s);
    });
jQuery(document).ready(function($){

$('.filter_btn').on('click', function(){
		$('.filter_panel').slideToggle();
	});


});
$(document).delegate('.importmodal', 'click', function(){

$('#importmodal').modal('show');
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\applications\finalize.blade.php ENDPATH**/ ?>