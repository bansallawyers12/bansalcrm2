
<?php $__env->startSection('title', 'Admin Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<style>
    /* Dashboard Widget Improvements */
    .dash_card {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: box-shadow 0.3s ease;
    }
    
    .dash_card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .card-statistic-4 {
        padding: 1.5rem;
    }
    
    .card_header {
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .card_header h5 {
        margin: 0;
        font-weight: 600;
        color: #333;
    }
    
    .card_body {
        min-height: 100px;
    }
    
    .card_body .text-center {
        padding: 2rem 1rem;
    }
    
    .card_body .text-muted {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    /* Task Filter Dropdown */
    #task_filter {
        font-size: 0.75rem;
        height: 24px;
        padding: 2px 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background-color: #fff;
        cursor: pointer;
    }
    
    #task_filter:focus {
        outline: none;
        border-color: #6777ef;
        box-shadow: 0 0 0 2px rgba(103, 119, 239, 0.1);
    }
    
    /* Empty State Styling */
    .card_body .text-center p {
        margin-bottom: 0.5rem;
    }
    
    .card_body .text-center small {
        font-size: 0.8rem;
        color: #999;
    }
    
    /* Table Improvements */
    .card_body table {
        width: 100%;
        margin: 0;
    }
    
    .card_body table tbody tr {
        border-bottom: 1px solid #f5f5f5;
    }
    
    .card_body table tbody tr:last-child {
        border-bottom: none;
    }
    
    .card_body table tbody tr:hover {
        background-color: #f9f9f9;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .dash_card {
            margin-bottom: 1rem;
        }
        
        .card-statistic-4 {
            padding: 1rem;
        }
    }
</style>

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		


        <div class="row">

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-4">
				<div class="card dash_card">
					<div class="card-statistic-4">
						<div class="align-items-center justify-content-between">
							<div class="row ">
								<div class="col-lg-12 col-md-12">
									<div class="card-content">
										<h5 class="font-14">Today Followup</h5>
										<h2 class="mb-3 font-18"><?php echo e($todayFollowupCount); ?></h2>
										<p class="mb-0"><span class="col-green"><?php echo e($todayFollowupCount); ?></span> <a href="<?php echo e(URL::to('/admin/followup-dates/')); ?>">click here</a></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>



            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-4">
                <div class="card dash_card">
                    <div class="card-statistic-4">
                        <div class="card-content cus_card_content">
                            <div class="card_header">
                                <h5 class="font-14">My Tasks 
                                    <select id="task_filter" class="form-control form-control-sm d-inline-block" style="width: auto; display: inline-block; margin-left: 5px; padding: 2px 5px;">
                                        <option value="today" <?php echo e($dateFilter == 'today' ? 'selected' : ''); ?>>Today</option>
                                        <option value="week" <?php echo e($dateFilter == 'week' ? 'selected' : ''); ?>>This Week</option>
                                        <option value="month" <?php echo e($dateFilter == 'month' ? 'selected' : ''); ?>>This Month</option>
                                    </select>
                                </h5>
                            </div>
                            <div class="card_body">
                                <?php if($todayTasks->count() > 0): ?>
                                <div class="taskdata_list">
                                    <div class="table-responsive">
                                        <table id="my-datatable" class="table-2 table text_wrap">
                                            <tbody class="taskdata">
                                            <?php $__currentLoopData = $todayTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    // Use eager-loaded user relationship
                                                    if($alist->user){
                                                        $first_name = $alist->user->first_name ?? 'N/A';
                                                        $last_name = $alist->user->last_name ?? 'N/A';
                                                        $full_name = $first_name.' '.$last_name;
                                                    } else {
                                                        $full_name = 'N/A';
                                                    }
                                                ?>
                                                <tr class="opentaskview" style="cursor:pointer;" id="<?php echo e($alist->id); ?>">
                                                    <td>
                                                        <?php if($alist->status == 1 || $alist->status == 2): ?>
                                                            <span class='check'><i class='fa fa-check'></i></span>
                                                        <?php else: ?>
                                                            <span class='round'></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo e($full_name); ?><br>
                                                        <i class="fa fa-clock"></i> <?php echo e($alist->formatted_due_date ?? 'N/A'); ?>

                                                    </td>
                                                    <td>
                                                        <?php if($alist->status == 1): ?>
                                                            <span style="color: rgb(113, 204, 83); width: 84px;">Completed</span>
                                                        <?php elseif($alist->status == 2): ?>
                                                            <span style="color: rgb(255, 173, 0); width: 84px;">In Progress</span>
                                                        <?php elseif($alist->status == 3): ?>
                                                            <span style="color: rgb(156, 156, 156); width: 84px;">On Review</span>
                                                        <?php else: ?>
                                                            <span style="color: rgb(255, 173, 0); width: 84px;">Todo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="text-center py-3">
                                    <p class="text-muted mb-0">No tasks for <?php echo e($dateFilter == 'today' ? 'today' : ($dateFilter == 'week' ? 'this week' : 'this month')); ?>.</p>
                                    <small class="text-muted">All caught up!</small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-4">
                <div class="card dash_card">
                    <div class="card-statistic-4">
                        <div class="card-content cus_card_content">
                            <div class="card_header">
                                <h5 class="font-14">Check-In Queue</h5>
                            </div>
                            <div class="card_body">
                            <?php if($checkInQueue['total'] > 0): ?>
                                <table>
                                    <tbody>
                                    <?php $__currentLoopData = $checkInQueue['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $checkinslist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <a id="<?php echo e(@$checkinslist->id); ?>" class="opencheckindetail" href="javascript:;">
                                                    <?php echo e(@$checkinslist->client->first_name ?? 'N/A'); ?> <?php echo e(@$checkinslist->client->last_name ?? ''); ?>

                                                </a>
                                                <br>
                                                <span>Waiting since <?php echo e($checkinslist->formatted_waiting_time ?? 'N/A'); ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="text-center py-3">
                                    <p class="text-muted mb-0">No office check-in at the moment.</p>
                                </div>
                            <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
          </div>
      	</div>  
          
          <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Note Lists With Deadline</h4>
                        <div class="card-header-action">
                            <!-- Additional header actions can be added here -->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Partner Name</th>
                                        <th>Description</th>
                                        <th>Deadline</th>
                                        <th>Created at</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     <?php if($notesData->isEmpty()): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No record found</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php $__currentLoopData = $notesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><a href="<?php echo e(URL::to('/admin/partners/detail/'.base64_encode(convert_uuencode(@$note->client_id)) )); ?>"><?php echo e(@$note->partner_name == "" ? config('constants.empty') : str_limit(@$note->partner_name, '50', '...')); ?></a></td>
                                        <td><?php echo preg_replace('/<\/?p>/', '', $note->description ); ?></td>
                                        <td><?php echo e($note->formatted_deadline ?? 'N/A'); ?></td>
                                        <td><?php echo e($note->formatted_created_at ?? 'N/A'); ?></td>
                                        <td style="white-space: initial;">
                                            <div class="dropdown d-inline">
                                                <button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item has-icon" href="javascript:;" onclick="closeNotesDeadlineAction(<?php echo e($note->id); ?>)">Close</a>
                                                    <a class="dropdown-item has-icon btn-extend_note_deadline"  data-noteid="<?php echo e($note->id); ?>" data-assignnote="<?php echo e($note->description); ?>" data-deadlinedate="<?php echo e($note->formatted_deadline ?? ''); ?>" href="javascript:;">Extend</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                     <?php endif; ?>
                                    </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <!-- Footer content can be added here -->
                        <?php echo $notesData->appends(\Request::except('page'))->render(); ?>

                    </div>
                </div>
            </div>
        </div>

	</section>
</div>

<!-- Action Popup Modal -->
<div class="modal fade custom_modal" id="extend_note_popup" tabindex="-1" role="dialog" aria-labelledby="create_action_popupLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="padding: 20px;">
            <div class="modal-header" style="padding-bottom: 11px;">
                <h5 class="modal-title assignnn" id="create_action_popupLabel" style="margin: 0 -24px;">Extend Notes Deadline</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <input id="note_id" type="hidden" value="">
            <div id="popover-content">
                <div class="box-header with-border">
                    <div class="form-group row" style="margin-bottom:12px;">
                        <label for="inputEmail3" class="col-sm-3 control-label c6 f13" style="margin-top:8px;">Note</label>
                        <div class="col-sm-9">
                            <textarea id="assignnote" class="form-control" placeholder="Enter a note..."></textarea>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <div class="form-group row note_deadline">
                    <label for="inputSub3" class="col-sm-3 control-label c6 f13" style="margin-top:8px;">
                        Note Deadline
                    </label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control f13" placeholder="dd/mm/yyyy" id="note_deadline" value="<?php echo date('d/m/Y');?>" name="note_deadline">
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="box-footer" style="padding:10px 0;">
                    <div class="row text-center">
                        <div class="col-md-12 text-center">
                            <button class="btn btn-danger" id="extend_deadline">Extend Deadline</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-datepicker.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(asset('js/bootstrap-datepicker.js')); ?>"></script>
<script src="<?php echo e(asset('js/popover.js')); ?>"></script>
<script>
$(document).ready(function() {
    // Task filter change handler
    $('#task_filter').on('change', function() {
        var filter = $(this).val();
        window.location.href = '<?php echo e(URL::to('/admin/dashboard')); ?>?task_filter=' + filter;
    });

    $('#note_deadline').datepicker({ format: 'dd/mm/yyyy',todayHighlight: true,autoclose: true }).datepicker('setDate', new Date());

    $(document).on('click', '#extend_deadline', function() {
        $(".popuploader").show();
        let flag = true;
        let error = "";
        $(".custom-error").remove();

        if ($('#assignnote').val() === '') {
            $('.popuploader').hide();
            error = "Note field is required.";
            $('#assignnote').after("<span class='custom-error' role='alert'>" + error + "</span>");
            flag = false;
        }
        if ($('#note_deadline').val() === '') {
            $('.popuploader').hide();
            error = "Note Deadline is required.";
            $('#task_group').after("<span class='custom-error' role='alert'>" + error + "</span>");
            flag = false;
        }

        if (flag) {
            $.ajax({
                type: 'POST',
                url: "<?php echo e(URL::to('/')); ?>/admin/extenddeadlinedate",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    note_id: $('#note_id').val(),
                    description: $('#assignnote').val(),
                    note_deadline: $('#note_deadline').val()
                },
                success: function(response) {
                    $('.popuploader').hide();
                    $('#extend_note_popup').modal('hide');
                    location.reload();
                    //var obj = $.parseJSON(response);
                }
            });
        } else {
            $('.popuploader').hide();
        }
    });

    // Handle click event on the action button
    $(document).delegate('.btn-extend_note_deadline', 'click', function(){
        var noteid = $(this).attr("data-noteid");
        var assignnote = $(this).attr("data-assignnote");
        var deadlinedate = $(this).attr("data-deadlinedate");
        $('#note_id').val(noteid);
        $('#assignnote').val(assignnote);
        $('#note_deadline').val(deadlinedate);
        $('#extend_note_popup').modal('show');
    });
});

//close Notes Deadline Action
function closeNotesDeadlineAction( noteid) {
    var conf = confirm('Are you sure, you want to close this note deadline.');
    if(conf){
        if(noteid == '') {
            alert('Please select note to close the deadline.');
            return false;
        } else {
            $('.popuploader').show();
            $.ajax({
                type:'post',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url:"<?php echo e(URL::to('/')); ?>/admin/update-note-deadline-completed",
                data:{'id': noteid},
                success:function(resp) {
                    $('.popuploader').hide();
                    location.reload();
                }
            });
        }
    } else{
        $('.popuploader').hide();
    }
}
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\dashboard.blade.php ENDPATH**/ ?>