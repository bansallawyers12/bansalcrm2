<!-- Appliation Modal -->
<div class="modal fade add_appliation custom_modal"  tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addApplicationModalLabel">Add Application</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?php echo e(URL::to('/admin/saveapplication')); ?>" name="applicationform" id="addapplicationformform" autocomplete="off" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
				<input type="hidden" name="client_id" value="<?php echo e($fetchedData->id); ?>">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="workflow">Select Workflow <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control workflow applicationselect2" id="workflow" name="workflow">
									<option value="">Please Select a Workflow</option>
									<?php $__currentLoopData = \App\Models\Workflow::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wlist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($wlist->id); ?>"><?php echo e($wlist->name); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="partner_branch">Select Partner & Branch <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control partner_branch partner_branchselect2" id="partner" name="partner_branch">
									<option value="">Please Select a Partner & Branch</option>
								</select>
								<span class="custom-error partner_branch_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="product">Select Product</label>
								<select data-valid="required" class="form-control product approductselect2" id="product" name="product">
									<option value="">Please Select a Product</option>

								</select>
								<span class="custom-error product_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('applicationform')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<!-- Appliation Modal -->
<div class="modal fade custom_modal" id="discon_application" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="disconApplicationModalLabel">Discontinue Application</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?php echo e(URL::to('/admin/discontinue_application')); ?>" name="discontinue_application" id="discontinue_application" autocomplete="off" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
				<input type="hidden" name="diapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="workflow">Discontinue Reason <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control workflow" id="workflow" name="workflow">
									<option value="">Please Select</option>
									<option value="Change of Application">Change of Application</option>
									<option value="Error by Team Member">Error by Team Member</option>
									<option value="Financial Difficulties">Financial Difficulties</option>
									<option value="Loss of competitor">Loss of competitor</option>
									<option value="Other Reasons">Other Reasons</option>
                                </select>
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="">Notes <span class="span_req">*</span></label>
								<textarea data-valid="required"  class="form-control" name="note"></textarea>

							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('discontinue_application')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="revert_application" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="revertApplicationModalLabel">Revert Discontinued Application</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?php echo e(URL::to('/admin/revert_application')); ?>" name="revertapplication" id="revertapplication" autocomplete="off" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
				<input type="hidden" name="revapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="">Notes <span class="span_req">*</span></label>
								<textarea data-valid="required"  class="form-control" name="note"></textarea>

							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('revertapplication')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- Interested Service Modal -->
<div class="modal fade add_interested_service custom_modal" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel">Add Interested Services</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?php echo e(URL::to('/admin/interested-service')); ?>" name="inter_servform" autocomplete="off" id="inter_servform" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
				<input type="hidden" name="client_id" value="<?php echo e($fetchedData->id); ?>">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="intrested_workflow">Select Workflow <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control workflowselect2" id="intrested_workflow" name="workflow">
									<option value="">Please Select a Workflow</option>
									<?php $__currentLoopData = \App\Models\Workflow::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wlist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($wlist->id); ?>"><?php echo e($wlist->name); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="intrested_partner">Select Partner</label>
								<select data-valid="required" class="form-control partnerselect2" id="intrested_partner" name="partner">
									<option value="">Please Select a Partner</option>

								</select>
								<span class="custom-error partner_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="intrested_product">Select Product</label>
								<select data-valid="required" class="form-control productselect2" id="intrested_product" name="product">
									<option value="">Please Select a Product</option>

								</select>
								<span class="custom-error product_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="branch">Select Branch</label>
								<select data-valid="required" class="form-control branchselect2" id="intrested_branch" name="branch">
									<option value="">Please Select a Branch</option>

								</select>
								<span class="custom-error branch_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="expect_start_date">Expected Start Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<?php echo Form::text('expect_start_date', '', array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )); ?>

								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error expect_start_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="expect_win_date">Expected Win Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<?php echo Form::text('expect_win_date', '', array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )); ?>

								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error expect_win_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('inter_servform')" type="button" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>



<!-- Appointment Modal -->
<div class="modal fade add_appointment custom_modal" id="create_appoint" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel">Add Appointment</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?php echo e(URL::to('/admin/add-appointment-book')); ?>" name="appointform" id="appointform" autocomplete="off" enctype="multipart/form-data">
				    <?php echo csrf_field(); ?>
				    <input type="hidden" name="client_id" value="<?php echo e($fetchedData->id); ?>">
                    <input type="hidden" name="client_unique_id" value="<?php echo e($fetchedData->client_id); ?>">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
                            <?php
                            //$timelist = \DateTimeZone::listIdentifiers(DateTimeZone::ALL);
                            ?>
							<!--<div class="form-group">
								<label style="display:block;" for="related_to">Related to:</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="client" value="Client" name="related_to" checked>
									<label class="form-check-label" for="client">Client</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="partner" value="Partner" name="related_to">
									<label class="form-check-label" for="partner">Partner</label>
								</div>
								<span class="custom-error related_to_error" role="alert">
									<strong></strong>
								</span>
							</div>-->
						</div>

						<!--<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label style="display:block;" for="related_to">Added by:</label>
								<span></span>
							</div>
						</div>-->

						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group row align-items-center">
								<label for="client_name" class="col-sm-3 col-form-label">Client Reference No<span class="span_req">*</span></label>
                                <div class="col-sm-6">
                                    <?php echo Form::text('client_name', @$fetchedData->client_id, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Client Reference','readonly'=>'readonly' )); ?>

                                    
                                </div>
                            </div>
						</div>

						<!--<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="timezone">Timezone <span class="span_req">*</span></label>
								<select class="form-control timezoneselects2" name="timezone" data-valid="required">
									<option value="">Select Timezone</option>
									<?php //foreach($timelist as $tlist){ ?>
										<option value="<?php //echo $tlist; ?>" <?php //if($tlist == 'Australia/Melbourne'){ echo "selected"; } ?>><?php //echo $tlist; ?></option>
									<?php //} ?>
								</select>
							</div>
						</div>-->

                        <input type="hidden" name="timezone" value="Australia/Melbourne">

                        <div class="col-12 col-md-12 col-lg-12 services_row" id="services" style="display: none;">
							<div class="form-group">
								<label for="service_id">Services <span class="span_req">*</span></label>
                                
                                <input type="hidden"  id="service_id" name="service_id" value="">
                            </div>
						</div>

                        <div class="col-12 col-md-12 col-lg-12 appointment_row" id="appointment_details" style="display: none;">
                            <div class="form-group inperson_address_cls">
                                <label for="inperson_address" class="heading_title">Location</label>
                                <div class="inperson_address_header" id="inperson_address_1">
                                    <label class="inperson_address_title">
                                        <input type="radio" class="inperson_address" name="inperson_address" data-val="1" value="1">
                                        <div class="inperson_address_title_span">
                                            ADELAIDE<br/><span style="font-size: 10px;">(Unit 5 5/55 Gawler Pl, Adelaide SA 5000)</span>
                                        </div>
                                    </label>

                                    <label class="inperson_address_title">
                                        <input type="radio" class="inperson_address" name="inperson_address" data-val="2" value="2">
                                        <div class="inperson_address_title_span">
                                            MELBOURNE<br/><span style="font-size: 10px;">(Next to Flight Center, Level 8/278 Collins St, Melbourne VIC 3000, Australia)</span>
                                        </div>
                                    </label>
                                </div>

                                <style>
                                    .inperson_address_header {
                                        display: flex;
                                        align-items: center;
                                        gap: 20px; /* Adjust spacing between radio options */
                                        flex-wrap: nowrap; /* Ensures everything stays in one line */
                                    }

                                    .inperson_address_title {
                                        display: flex;
                                        align-items: center;
                                        gap: 8px; /* Space between radio button and text */
                                        white-space: nowrap; /* Prevents text from breaking into multiple lines */
                                    }

                                    .inperson_address_title_span {
                                        display: inline-block;
                                        color: #828F9A;
                                    }
                                    /* Mobile Devices: Stack items vertically */
                                    @media (max-width: 768px) {
                                        .inperson_address_header {
                                            display: inline;
                                        }
                                    }
                                </style>
                            </div>

                            <div class="form-group row align-items-center appointment_details_cls" style="display: none;">
                                <label for="appointment_details" class="heading_title col-sm-3 col-form-label">Appointment details</label>
                                <div class="col-sm-9">
                                    <select class="form-control appointment_item" name="appointment_details" data-valid="required">
                                        <option value="">Select</option>
                                        <option value="phone"> Phone</option>
                                        <option value="in_person">In person</option>
                                        <option value="zoom_google_meeting" style="display: none;">Zoom / Google Meeting</option>
                                    </select>
                                </div>
                             </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12 row info_row" id="info" style="display: none;">
                            <!--<div class="tab_header">
                                <h4 style="margin: 15px 0px 15px;">Fill Information</h4>
                            </div>-->
                            <div class="tab_body">
                                <div class="row">
                                    <!--<div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fullname">Full Name <span class="span_req">*</span></label>
                                            <input type="text" class="form-control fullname" placeholder="Enter Name" name="fullname" data-valid="required"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email <span class="span_req">*</span></label>
                                            <input id="email" type="email" class="form-control email" placeholder="Enter Email" name="email" data-valid="required"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Phone <span class="span_req">*</span></label>
                                            <input id="phone" type="text" class="form-control phone" placeholder="Enter Phone" name="phone" data-valid="required" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Reference if any</label>
                                            <input type="text" class="form-control title" placeholder="Enter Reference" name="title" />
                                        </div>
                                    </div>-->
                                    <div class="col-12 col-md-12 col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label for="description" class="col-sm-3 col-form-label">Details Of Enquiry <span class="span_req">*</span></label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control description" placeholder="Enter Details Of Enquiry" name="description" data-valid="required"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-12 col-lg-12">
                                        <div class="form-group row align-items-center">
                                            <label for="description" class="col-sm-3 col-form-label">Date & Time <span class="span_req">*</span></label>
                                            <div class="col-sm-9">
                                                <span style="float:right;">
                                                    <input type="checkbox" name="slot_overwrite" id="slot_overwrite" value="0"> Slot Overwrite
                                                    <input type="hidden" name="slot_overwrite_hidden" id="slot_overwrite_hidden" value="0">
                                                </span>

                                                <div style="height:205px;">
                                                    <div style="width:37%;float: left;">
                                                        <div id='datetimepicker' class="datePickerCls"></div>
                                                    </div>
                                                    <div class="timeslotDivCls" style="width: 60%;float: right;/*border-left: 1px solid #E3EAF3;*/">
                                                        <div class="showselecteddate" style="font-size: 14px;text-align: center; padding: 5px 0 3px;border-bottom: 1px solid #E3EAF3;color: #0d0d0f !important;font-weight: bold;"></div>
                                                        <div class="timeslots" style="overflow:scroll !important;height:166px;"></div>
                                                    </div>


                                                    <div class="slotTimeOverwriteDivCls" style="display: none;">
                                                        <?php
                                                        if (!function_exists('generateTimeDropdown')) {
                                                            function generateTimeDropdown($interval = 15) {
                                                                $start = new DateTime('00:00');
                                                                $end = new DateTime('23:45'); // Set the end time to 11:45 PM

                                                                $intervalDuration = new DateInterval('PT' . $interval . 'M');
                                                                $times = new DatePeriod($start, $intervalDuration, $end);

                                                                echo '<select class="slot_overwrite_time_dropdown" style="margin-left: 50px;margin-top: 50px;">';
                                                                echo '<option value="">Select Time</option>';
                                                                foreach ($times as $time) {
                                                                    // Calculate the end time for each option
                                                                    $endTime = clone $time;
                                                                    $endTime->add($intervalDuration);

                                                                    // Format both start and end times for display
                                                                    echo '<option value="' . $time->format('g:i A') . ' - ' . $endTime->format('g:i A') . '">';
                                                                    echo $time->format('g:i A') . ' - ' . $endTime->format('g:i A');
                                                                    echo '</option>';

                                                                    //echo '<option value="' . $time->format('g:i A') . '">' . $time->format('g:i A') . '</option>';
                                                                }
                                                                echo '</select>';
                                                            }
                                                        }

                                                        generateTimeDropdown(15); // 15-minute interval
                                                        ?>
                                                    </div>
                                                </div>
                                                <input type="hidden"  id="timeslot_col_date" name="appoint_date" value=""  >
                                                <input type="hidden"  id="timeslot_col_time" name="appoint_time" value=""  >
                                                <span class="timeslot_col_date_time" role="alert" style="display: none;color:#f00;">Date and Time is required.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

						<!--<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="invites">Invitees</label>
								<select class="form-control timezoneselects2" name="invites">
									<option value="">Select Invitees</option>
								    <?php
									//$headoffice = \App\Models\Admin::where('role','!=',7)->get();
									//foreach($headoffice as $holist){ ?>
									<option value="">  ()</option>
									<?php //} ?>
								</select>
							</div>
						</div>-->

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('appointform')" type="button" class="btn btn-primary" id="appointform_save">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Note & Terms Modal -->
<div class="modal fade custom_modal" id="create_note" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="createNoteModalLabel">Create Note</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?php echo e(URL::to('/admin/create-note')); ?>" name="notetermform" autocomplete="off" id="notetermform" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
				<input type="hidden" name="client_id" value="<?php echo e($fetchedData->id); ?>">
				<input type="hidden" name="noteid" value="">
				<input type="hidden" name="mailid" value="0">
				<input type="hidden" name="vtype" value="client">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="title">Title <span class="span_req">*</span></label>
								<select name="title" class="form-control" data-valid="required">
								    <option value="">Please Select Note</option>
								    <option value="Call">Call</option>
								    <option value="Email">Email</option>
								    <option value="In-Person">In-Person</option>
								    <option value="Others">Others</option>
								    <option value="Attention">Attention</option>
								</select>
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Description <span class="span_req">*</span></label>
								<textarea  class="summernote-simple" name="description" data-valid="required"></textarea>
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<!--<div class="col-12 col-md-12 col-lg-12 is_not_note" style="display:none;">
							<div class="form-group">
								<label class="d-block" for="">Related To</label>
								<div class="form-check form-check-inline">
									<input class="" type="radio" id="note_contact" value="Contact" name="related_to_note" checked>
									<label style="padding-left: 6px;" class="form-check-label" for="note_contact">Contact</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="" type="radio" id="note_partner" value="Partner" name="related_to_note">
									<label style="padding-left: 6px;" class="form-check-label" for="note_partner">Partner</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="" type="radio" id="note_application" value="Application" name="related_to_note">
									<label style="padding-left: 6px;" class="form-check-label" for="note_application">Application</label>
								</div>

							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12 is_not_note" style="display:none;">
							<div class="form-group">
								<label for="contact_name">Contact Name <span class="span_req">*</span></label>
								<select data-valid="" class="form-control contact_name js-data-example-ajaxcc" name="contact_name[]" id="contact_name_select">


								</select>
								<span class="custom-error contact_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>-->
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('notetermform')" type="button" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="create_note_d" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="createNoteDModalLabel">Create Note</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		<div class="modal-body">
			<form method="post" action="<?php echo e(URL::to('/admin/create-note')); ?>" name="notetermform_n" autocomplete="off" id="notetermform_n" enctype="multipart/form-data">
			<?php echo csrf_field(); ?>
			<input type="hidden" name="client_id" id="note_client_id" value="<?php echo e($fetchedData->id); ?>">
			<input type="hidden" name="noteid" value="">
			<input type="hidden" name="mailid" value="0">
			<input type="hidden" name="vtype" value="client">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="title">Title <span class="span_req">*</span></label>
								<select name="title" class="form-control" data-valid="required" id="noteType">
								    <option value="">Please Select Note</option>
								    <option value="Call">Call</option>
								    <option value="Email">Email</option>
								    <option value="In-Person">In-Person</option>
								    <option value="Others">Others</option>
								    <option value="Attention">Attention</option>
								</select>

                                <!-- Container for additional inputs -->
						        <div id="additionalFields"></div>

								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Description <span class="span_req">*</span></label>
								<textarea  class="summernote-simple" name="description" data-valid="required"></textarea>
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('notetermform_n')" type="button" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Note & Terms Modal -->
<div class="modal fade custom_modal" id="view_note" tabindex="-1" role="dialog" aria-labelledby="view_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="note_col">
					<div class="note_content">
						<h5></h5>
						<p></p>
						<div class="extra_content">

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Note & Terms Modal -->
<div class="modal fade custom_modal" id="view_application_note" tabindex="-1" role="dialog" aria-labelledby="view_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="note_col">
					<div class="note_content">
						<h5></h5>
						<p></p>
						<div class="extra_content">

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Task Modal -->
<div class="modal fade custom_modal" id="opentaskview" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content taskview">

		</div>
	</div>
</div>
<div class="modal fade create_task custom_modal" id="opentaskmodal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="taskModalLabel">Create New Task</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- Task system removed - December 2025 -->
				<form method="post" action="#" name="taskform" autocomplete="off" id="tasktermform" enctype="multipart/form-data" onsubmit="alert('Task system has been removed'); return false;">
				<?php echo csrf_field(); ?>
				<input type="hidden" name="mailid" value="">

					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="title">Title <span class="span_req">*</span></label>
								<?php echo Form::text('title', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Title' )); ?>

								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="category">Category <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control category select2" name="category">
									<option value="">Choose Category</option>
									<option value="Reminder">Reminder</option>
									<option value="Call">Call</option>
									<option value="Follow Up">Follow Up</option>
									<option value="Email">Email</option>
									<option value="Meeting">Meeting</option>
									<option value="Support">Support</option>
									<option value="Others">Others</option>
								</select>
								<span class="custom-error category_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="assignee">Assignee</label>
								<select data-valid="" class="form-control assignee select2" name="assignee" id="task_assignee">
									<option value="">Select</option>
									<?php
									$headoffice = \App\Models\Admin::where('role','!=',7)->get();
									foreach($headoffice as $holist){
										?>
										<option value="<?php echo e($holist->id); ?>"><?php echo e($holist->first_name); ?> (<?php echo e($holist->email); ?>)</option>
										<?php
									}
									?>
								</select>
								<span class="custom-error assignee_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="priority">Priority</label>
								<select data-valid="" class="form-control priority select2" name="priority" id="task_priority">
									<option value="">Choose Priority</option>
									<option value="Low">Low</option>
									<option value="Normal">Normal</option>
									<option value="High">High</option>
									<option value="Urgent">Urgent</option>
								</select>
								<span class="custom-error priority_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="due_date">Due Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<?php echo Form::text('due_date', '', array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )); ?>

								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error due_date_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="due_time">Due Time</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-clock"></i>
										</div>
									</div>
									<?php echo Form::time('due_time', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off', 'placeholder'=>'Select Time' )); ?>

								</div>
								<span class="custom-error due_time_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Description</label>
								<textarea class="form-control" name="description"></textarea>
								<span class="custom-error description_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12 ifselecttask">
							<div class="form-group">
								<label class="d-block" for="related_to">Related To</label>
								<div class="form-check form-check-inline">
									<input  type="radio" id="contact" value="Contact" name="related_to" checked>
									<label style="padding-left:6" class="form-check-label" for="contact">Contact</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="" type="radio" id="partner" value="Partner" name="related_to">
									<label style="padding-left:6" class="form-check-label" for="partner">Partner</label>
								</div>
							
								<?php if($errors->has('related_to')): ?>
									<span class="custom-error" role="alert">
										<strong><?php echo e(@$errors->first('related_to')); ?></strong>
									</span>
								<?php endif; ?>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6 is_contact ifselecttask">
							<div class="form-group">
								<label for="contact_name">Contact Name <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control contact_name js-data-example-ajaxcontact" name="contact_name[]">

								</select>
								<span class="custom-error contact_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6 is_partner ifselecttask">
							<div class="form-group">
								<label for="partner_name">Partner Name <span class="span_req">*</span></label>
								<select data-valid="" class="form-control partner_name select2" name="partner_name" id="task_partner_name">
									<option value="">Choose Partner</option>
									<option value="Amit">Amit</option>
								</select>
								<span class="custom-error partner_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6 is_application ifselecttask">
							<div class="form-group">
								<label for="client_name">Client Name <span class="span_req">*</span></label>
								<select data-valid="" class="form-control client_name select2" name="client_name" id="task_client_name">
									<option value="">Choose Client</option>
									<option value="Amit">Amit</option>
								</select>
								<span class="custom-error client_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6 is_application ifselecttask">
							<div class="form-group">
								<label for="application">Application <span class="span_req">*</span></label>
								<select data-valid="" class="form-control application select2" name="application" id="task_application">
									<option value="">Choose Application</option>
									<option value="Demo">Demo</option>
								</select>
								<span class="custom-error application_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6 is_application ifselecttask">
							<div class="form-group">
								<label for="stage">Stage <span class="span_req">*</span></label>
								<select data-valid="" class="form-control stage select2" name="stage" id="task_stage">
									<option value="">Choose Stage</option>
									<option value="Stage 1">Stage 1</option>
								</select>
								<span class="custom-error stage_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="followers">Followers <span class="span_req">*</span></label>
								<select data-valid="" multiple class="form-control followers  select2" name="followers" id="task_followers">

									<?php
										$headoffice = \App\Models\Admin::where('role','!=',7)->get();
									foreach($headoffice as $holist){
										?>
										<option value="<?php echo e($holist->id); ?>"><?php echo e($holist->first_name); ?> <?php echo e($holist->last_name); ?> (<?php echo e($holist->email); ?>)</option>
										<?php
									}
									?>
								</select>
								<span class="custom-error followers_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="attachments">Attachments</label>
								<div class="custom-file">
									<input type="file" name="attachments" class="custom-file-input" id="attachments">
									<label class="custom-file-label showattachment" for="attachments">Choose file</label>
								</div>
								<span class="custom-error attachments_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
						<input type="hidden" name="client_id" value="<?php echo e($fetchedData->id); ?>">
							<button onclick="customValidate('taskform')" type="button" class="btn btn-primary">Create</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Education Modal -->
<div class="modal fade create_education custom_modal" tabindex="-1" role="dialog" aria-labelledby="create_educationModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="createEducationModalLabel">Create Education</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?php echo e(URL::to('/admin/saveeducation')); ?>" name="educationform" id="educationform" autocomplete="off" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
				<input type="hidden" name="client_id" value="<?php echo e($fetchedData->id); ?>">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="degree_title">Degree Title <span class="span_req">*</span></label>
								<?php echo Form::text('degree_title', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Degree Title' )); ?>

								<span class="custom-error degree_title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="degree_level">Degree Level <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control degree_level select2" name="degree_level">
									<option value="">Please Select Degree Level</option>
									<option value="Bachelor">Bachelor</option>
									<option value="Certificate">Certificate</option>
									<option value="Diploma">Diploma</option>
									<option value="High School">High School</option>
									<option value="Master">Master</option>
								</select>
								<span class="custom-error degree_level_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="institution">Institution <span class="span_req">*</span></label>
								<?php echo Form::text('institution', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Institution' )); ?>

								<span class="custom-error institution_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="course_start">Course Start</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<?php echo Form::text('course_start', '', array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )); ?>

									<?php if($errors->has('course_start')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('course_start')); ?></strong>
										</span>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="course_end">Course End</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<?php echo Form::text('course_end', '', array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select Date' )); ?>

									<?php if($errors->has('course_end')): ?>
										<span class="custom-error" role="alert">
											<strong><?php echo e(@$errors->first('course_end')); ?></strong>
										</span>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="subject_area">Subject Area</label>
								<select data-valid="" class="form-control subject_area select2" id="subjectlist" name="subject_area">
									<option value="">Please Select Subject Area</option>
									<?php
									foreach(\App\Models\SubjectArea::all() as $sublist){
										?>
										<option value="<?php echo e($sublist->id); ?>"><?php echo e($sublist->name); ?></option>
										<?php
									}
									?>
								</select>
								<span class="custom-error subject_area_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="subject">Subject</label>
								<select data-valid="" class="form-control subject select2" id="subject" name="subject">
									<option value="">Please Select Subject</option>
								</select>
								<span class="custom-error subject_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label class="d-block" for="academic_score">Academic Score</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="percentage" value="%" name="academic_score_type" checked>
									<label class="form-check-label" for="percentage">Percentage</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="GPA" value="GPA" name="academic_score_type">
									<label class="form-check-label" for="GPA">GPA</label>
								</div>
								<?php echo Form::number('academic_score', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','step'=>'0.01' )); ?>

								<span class="custom-error academic_score_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('educationform')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<!-- Note & Terms Modal -->
<div class="modal fade custom_modal" id="opencommissionmodal" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="commissionInvoiceModalLabel">Commission Invoice</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?php echo e(URL::to('/admin/create-invoice')); ?>" name="noteinvform" autocomplete="off" id="noteinvform" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
				<input type="hidden" name="client_id" value="<?php echo e($fetchedData->id); ?>">

					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
						<?php
						$timelist = \DateTimeZone::listIdentifiers(DateTimeZone::ALL);
						?>
							<div class="form-group">
								<label style="display:block;" for="invoice_type">Choose invoice:</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="net_invoice" value="1" name="invoice_type" checked>
									<label class="form-check-label" for="net_invoice">Net Claim Invoice</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="gross_invoice" value="2" name="invoice_type">
									<label class="form-check-label" for="gross_invoice">Gross Claim Invoice</label>
								</div>
								<span class="custom-error related_to_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="client">Client <span class="span_req">*</span></label>
								<?php echo Form::text('client', @$fetchedData->first_name.' '.@$fetchedData->last_name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )); ?>

								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Application <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control select2" name="application">
									<option value="">Select</option>
									<?php $__currentLoopData = \App\Models\Application::where('client_id',$fetchedData->id)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $aplist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php
									$productdetail = \App\Models\Product::where('id', $aplist->product_id)->first();
				$partnerdetail = \App\Models\Partner::where('id', $aplist->partner_id)->first();
				$PartnerBranch = \App\Models\PartnerBranch::where('id', $aplist->branch)->first();
				$workflow = \App\Models\Workflow::where('id', $aplist->workflow)->first();
									?>
										<option value="<?php echo e($aplist->id); ?>"><?php echo e(@$productdetail->name); ?> (<?php echo e(@$partnerdetail->partner_name); ?>)</option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>

							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('noteinvform')" type="button" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Note & Terms Modal -->
<div class="modal fade custom_modal" id="opengeneralinvoice" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="generalInvoiceModalLabel">General Invoice</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?php echo e(URL::to('/admin/create-invoice')); ?>" name="notegetinvform" autocomplete="off" id="notegetinvform" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
				<input type="hidden" name="client_id" value="<?php echo e($fetchedData->id); ?>">

					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
						<?php
						$timelist = \DateTimeZone::listIdentifiers(DateTimeZone::ALL);
						?>
							<div class="form-group">
								<label style="display:block;" for="invoice_type">Choose invoice:</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="net_invoice" value="3" name="invoice_type" checked>
									<label class="form-check-label" for="net_invoice">Client Invoice</label>
								</div>

								<span class="custom-error related_to_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="client">Client <span class="span_req">*</span></label>
								<?php echo Form::text('client', @$fetchedData->first_name.' '.@$fetchedData->last_name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )); ?>

								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Service <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control select2" name="application">
									<option value="">Select</option>
									<?php $__currentLoopData = \App\Models\Application::where('client_id',$fetchedData->id)->select('workflow')->distinct()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $aplist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php

				$workflow = \App\Models\Workflow::where('id', $aplist->workflow)->first();
									?>
										<option value="<?php echo e($workflow->id); ?>"><?php echo e($workflow->name); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>

							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('notegetinvform')" type="button" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="addpaymentmodal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
	<?php echo Form::open(array('url' => 'admin/invoice/payment-store', 'method' => 'post', 'name'=>"ajaxinvoicepaymentform", 'autocomplete'=>'off', "enctype"=>"multipart/form-data", "id"=>"ajaxinvoicepaymentform")); ?>

	<input type="hidden" value="" name="invoice_id" id="invoice_id">
	<input type="hidden" value="true" name="is_ajax" id="invoice_is_ajax">
	<input type="hidden" value="<?php echo e($fetchedData->id); ?>" name="client_id" id="payment_invoice_client_id">
		<div class="modal-content ">
			<div class="modal-header">
				<h4 class="modal-title">Payment Details</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">

				<div class="payment_field">
					<div class="payment_field_row">
						<div class="payment_field_col payment_first_step">
							<div class="field_col">
								<div class="label_input">
									<input data-valid="required" type="number" name="payment_amount[]" placeholder="" class="paymentAmount" />
									<div class="basic_label">AUD</div>
								</div>
							</div>

							<div class="field_col">
								<select name="payment_mode[]" class="form-control">
									<option value="Cheque">Cheque</option>
									<option value="Cash">Cash</option>
									<option value="Credit Card">Credit Card</option>
									<option value="Bank Transfers">Bank Transfers</option>
								</select>
							</div>
							<div class="field_col">
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-clock"></i>
										</div>
									</div>
									<input type="text" name="payment_date[]" placeholder="" class="datepicker form-control" />
								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
							</div>
							<div class="field_remove_col">
								<a href="javascript:;" class="remove_col"><i class="fa fa-times"></i></a>
							</div>
						</div>
					</div>
					<div class="add_payment_field">
						<a href="javascript:;"><i class="fa fa-plus"></i> Add New Line</a>
					</div>
					<div class="clearfix"></div>
					<div class="invoiceamount">
						<table class="table">
							<tr>
								<td><b>Invoice Amount:</b></td>
								<td class="invoicenetamount"></td>
								<td><b>Total Due:</b></td>
								<td class="totldueamount" data-totaldue=""></td>
							</tr>

						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" onclick="customValidate('ajaxinvoicepaymentform')" class="btn btn-primary" >Save & Close</button>
				<button type="button" class="btn btn-primary">Save & Send Receipt</button>
			  </div>
		</div>
		</form>
	</div>
</div>

<div class="modal fade custom_modal" id="create_applicationnote" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appNoteModalLabel">Create Note</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?php echo e(URL::to('/admin/create-app-note')); ?>" name="appnotetermform" autocomplete="off" id="appnotetermform" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
				<input type="hidden" name="client_id" value="<?php echo e($fetchedData->id); ?>">
				<input type="hidden" name="noteid" id="noteid" value="">
				<input type="hidden" name="type" id="type" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="title">Title <span class="span_req">*</span></label>
								<?php echo Form::text('title', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Title' )); ?>

								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Description <span class="span_req">*</span></label>
								<textarea class="summernote-simple" name="description" data-valid="required"></textarea>
								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('appnotetermform')" type="button" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Appointment Modal -->
<div class="modal fade add_appointment custom_modal" id="create_applicationappoint" tabindex="-1" role="dialog" aria-labelledby="create_appointModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appointModalLabel">Add Appointment</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?php echo e(URL::to('/admin/add-appointment')); ?>" name="appliappointform" id="appliappointform" autocomplete="off" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
				<input type="hidden" name="client_id" value="<?php echo e($fetchedData->id); ?>">
				<input type="hidden" id="type" name="type" value="application">
				<input type="hidden" id="appointid" name="noteid" value="">
				<input type="hidden"  name="atype" value="application">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
						<?php
						$timelist = \DateTimeZone::listIdentifiers(DateTimeZone::ALL);
						?>
							<div class="form-group">
								<label style="display:block;" for="related_to">Related to:</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="client" value="Client" name="related_to" checked>
									<label class="form-check-label" for="client">Client</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="partner" value="Partner" name="related_to">
									<label class="form-check-label" for="partner">Partner</label>
								</div>
								<span class="custom-error related_to_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label style="display:block;" for="related_to">Added by:</label>
								<span><?php echo e(@Auth::user()->first_name); ?></span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="client_name">Client Name <span class="span_req">*</span></label>
								<?php echo Form::text('client_name', @$fetchedData->first_name.' '.@$fetchedData->last_name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Client Name','readonly'=>'readonly' )); ?>

							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="timezone">Timezone <span class="span_req">*</span></label>
								<select class="form-control timezoneselect2" name="timezone" data-valid="required">
									<option value="">Select Timezone</option>
									<?php
									foreach($timelist as $tlist){
									?>
									<option value="<?php echo $tlist; ?>" <?php if($tlist == 'Australia/Melbourne'){ echo "selected"; } ?>><?php echo $tlist; ?></option>
									<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-7 col-lg-7">
							<div class="form-group">
								<label for="appoint_date">Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<?php echo Form::text('appoint_date', '', array('class' => 'form-control datepicker', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Select Date' )); ?>

								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error appoint_date_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-5 col-lg-5">
							<div class="form-group">
								<label for="appoint_time">Time</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-clock"></i>
										</div>
									</div>
									<?php echo Form::time('appoint_time', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Select Time' )); ?>

								</div>
								<span class="custom-error appoint_time_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="title">Title <span class="span_req">*</span></label>
								<?php echo Form::text('title', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Title' )); ?>

								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="description">Description</label>
								<textarea class="form-control" name="description" placeholder="Description"></textarea>
								<span class="custom-error description_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="invitees">Invitees</label>
								<select class="form-control select2" name="invitees">
									<option value="">Select Invitees</option>
									<?php
										$headoffice = \App\Models\Admin::where('role','!=',7)->get();
									foreach($headoffice as $holist){
										?>
										<option value="<?php echo e($holist->id); ?>"><?php echo e($holist->first_name); ?> <?php echo e($holist->last_name); ?> (<?php echo e($holist->email); ?>)</option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('appliappointform')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Checklist Modal -->
<div class="modal fade custom_modal" id="create_checklist" tabindex="-1" role="dialog" aria-labelledby="create_checklistModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="checklistModalLabel">Add New Checklist</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?php echo e(URL::to('/admin/add-checklists')); ?>" name="checklistform" id="checklistform" autocomplete="off" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
				<input type="hidden" name="client_id" value="<?php echo e($fetchedData->id); ?>">
				<input type="hidden" id="checklistapp_id" name="app_id" value="<?php echo e($fetchedData->id); ?>">
				<input type="hidden" id="checklist_type" name="type" value="">
				<input type="hidden" id="checklist_typename" name="typename" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="document_type">Document Type <span class="span_req">*</span></label>
								<select class="form-control " id="document_type" name="document_type" data-valid="required">
									<option value="">Please Select Document Type</option>
									<?php foreach(\App\Models\Checklist::all() as $checklist){ ?>
									<option value="<?php echo e($checklist->name); ?>"><?php echo e($checklist->name); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="description">Description</label>
								<textarea class="form-control" id="checklistdesc" name="description" placeholder="Description"></textarea>
								<span class="custom-error description_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<div class="form-check form-check-inline">
									<input value="1" class="" type="checkbox" value="Allow clients to upload documents from client portal" name="allow_upload_docu">
									<label style="padding-left: 8px;" class="form-check-label" for="allow_upload_docu">Allow clients to upload documents from client portal</label>
								</div>
								<div class="form-check form-check-inline">
									<input value="1" class="" type="checkbox" value="Make this as mandatory inorder to proceed next stage" name="proceed_next_stage">
									<label style="padding-left: 8px;" class="form-check-label" for="proceed_next_stage">Make this as mandatory inorder to proceed next stage</label>
								</div>
							</div>
						</div>
					</div>
					<div class="due_date_sec">
						<a href="javascript:;" class="btn btn-primary due_date_btn"><i class="fa fa-plus"></i> Add Due Date</a>
						<input type="hidden" value="0" class="checklistdue_date" name="due_date">
						<div class="due_date_col">
							<div class="row">
								<div class="col-12 col-md-6 col-lg-6">
									<div class="form-group">
										<label for="appoint_date">Date</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<div class="input-group-text">
													<i class="fas fa-calendar-alt"></i>
												</div>
											</div>
											<?php echo Form::text('appoint_date', '', array('class' => 'form-control datepicker', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Select Date' )); ?>

										</div>
										<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
										<span class="custom-error appoint_date_error" role="alert">
											<strong></strong>
										</span>
									</div>
								</div>
								<div class="col-12 col-md-5 col-lg-5">
									<div class="form-group">
										<label for="appoint_time">Time</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<div class="input-group-text">
													<i class="fas fa-clock"></i>
												</div>
											</div>
											<?php echo Form::time('appoint_time', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Select Time' )); ?>

										</div>
										<span class="custom-error appoint_time_error" role="alert">
											<strong></strong>
										</span>
									</div>
								</div>
								<div class="col-12 col-md-1 col-lg-1 remove_col">
									<a href="javascript:;" class="remove_btn"><i class="fa fa-trash"></i></a>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('checklistform')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Payment Schedule Modal -->
<div class="modal fade custom_modal paymentschedule" id="create_paymentschedule" tabindex="-1" role="dialog" aria-labelledby="create_paymentscheduleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="paymentscheduleModalLabel">Add Payment Schedule</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?php echo e(URL::to('/admin/add-appointment')); ?>" name="paymentform" id="paymentform" autocomplete="off" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
				<input type="hidden" name="client_id" value="<?php echo e($fetchedData->id); ?>">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="client_name">Client Name</label>
								<?php echo Form::text('client_name', '', array('class' => 'form-control', 'autocomplete'=>'off', 'data-valid'=>'', 'placeholder'=>'Enter Client Name' )); ?>

								<span class="custom-error client_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="application">Application</label>
								<?php echo Form::text('application', '', array('class' => 'form-control', 'autocomplete'=>'off', 'data-valid'=>'', 'placeholder'=>'Enter Application' )); ?>

								<span class="custom-error application_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="installment_name">Installment Name <span class="span_req">*</span></label>
								<?php echo Form::text('installment_name', '', array('class' => 'form-control', 'autocomplete'=>'off', 'data-valid'=>'required', 'placeholder'=>'Enter Installment Name' )); ?>

								<span class="custom-error installment_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="installment_date">Installment Date <span class="span_req">*</span></label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<?php echo Form::text('installment_date', '', array('class' => 'form-control datepicker', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Select Date' )); ?>

								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error installment_date_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="fees_type_sec">
								<div class="fee_type_row">
									<div class="custom_type_col">
										<div class="feetype_field">
											<div class="form-group">
												<label for="fee_type">Fee Type <span class="span_req">*</span></label>
											</div>
										</div>
										<div class="feeamount_field">
											<div class="form-group">
												<label for="fee_amount">Fee Amount <span class="span_req">*</span></label>
											</div>
										</div>
										<div class="commission_field">
											<div class="form-group">
												<label for="commission_percent">Commission %</label>
											</div>
										</div>
										<div class="remove_field">
											<div class="form-group">
											</div>
										</div>
									</div>
									<div class="fees_type_col custom_type_col">
										<div class="feetype_field">
											<div class="form-group">
												<select class="form-control select2" name="fee_type" data-valid="required">
													<option value="">Select Fee Type</option>
													<option value="Accommodation Fee">Accommodation Fee</option>
													<option value="Administration Fee">Administration Fee</option>
													<option value="Airline Ticket">Airline Ticket</option>
													<option value="Airport Transfer Fee">Airport Transfer Fee</option>
													<option value="Application Fee">Application Fee</option>
													<option value="Bond">Bond</option>
													<option value="Tuition Fee">Tuition Fee</option>
												</select>
											</div>
										</div>
										<div class="feeamount_field">
											<div class="form-group">
												<?php echo Form::text('fee_amount', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'0.00' )); ?>

											</div>
										</div>
										<div class="commission_field">
											<div class="form-group">
												<?php echo Form::text('commission_percent', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'0.00' )); ?>

											</div>
										</div>
										<div class="remove_field">
											<a href="javascript:;" class="remove_btn"><i class="fa fa-trash"></i></a>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="discount_row">
									<div class="discount_col custom_type_col">
										<div class="feetype_field">
											<div class="form-group">
												<input class="form-control" placeholder="Discount" disabled />
											</div>
										</div>
										<div class="feeamount_field">
											<div class="form-group">
												<?php echo Form::text('discount_amount', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'0.00' )); ?>

											</div>
										</div>
										<div class="commission_field">
											<div class="form-group">
												<?php echo Form::text('dispcunt_commission_percent', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'0.00' )); ?>

											</div>
										</div>
										<div class="remove_field">
											<a href="javascript:;" class="remove_btn"><i class="fa fa-trash"></i></a>
										</div>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="divider"></div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="add_fee_type">
								<a href="javascript:;" class="btn btn-outline-primary fee_type_btn"><i class="fa fa-plus"></i> Add Fee</a>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6 text-right">
							<div class="total_fee">
								<h4>Total Fee (USD)</h4>
								<span>11.00</span>
							</div>
							<div class="net_fee">
								<span class="span_label">Net Fee</span>
								<span class="span_value">0.00</span>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="divider"></div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="schedule_title">
								<h4>Setup Invoice Scheduling</h4>
							</div>
							<span class="schedule_note"><i class="fa fa-explanation-circle"></i> Schedule your Invoices by selecting an Invoice date for this installment.</span>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="invoice_date">Invoice Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<?php echo Form::text('invoice_date', '', array('class' => 'form-control datepicker', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Select Date' )); ?>

								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error installment_date_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" value="Allow clients to upload documents from client portal" name="allow_upload_docu">
									<label class="form-check-label" for="allow_upload_docu">Auto Invoicing</label>
								</div>
								<span class="schedule_note"><i class="fa fa-explanation-circle"></i> Enabling Auto Invoicing will automatically create unpaid invoices at above stated Invoice Date.</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="fee_type">Invoice Type <span class="span_req">*</span></label>
								<select class="form-control select2" name="fee_type" data-valid="required">
									<option value="">Select Invoice Type</option>
									<option value="Net Claim">Net Claim</option>
									<option value="Gross Claim">Gross Claim</option>
								</select>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="divider"></div>
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('paymentform')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<div id="applicationemailmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">Compose Email</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" name="appkicationsendmail" id="appkicationsendmail" action="<?php echo e(URL::to('/admin/application-sendmail')); ?>" autocomplete="off" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
				<input type="hidden" name="client_id" value="<?php echo e($fetchedData->id); ?>">
				<input type="hidden" id="type" name="type" value="application">
				<input type="hidden" id="appointid" name="noteid" value="">
				<input type="hidden"  name="atype" value="application">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">From <span class="span_req">*</span></label>
								<select class="form-control" name="email_from">
									<?php
									$emails = \App\Models\Email::select('email')->where('status', 1)->get();
									foreach($emails as $nemail){
										?>
											<option value="<?php echo $nemail->email; ?>"><?php echo $nemail->email; ?></option>
										<?php
									}

									?>
								</select>
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
								<input type="text" readonly class="form-control" name="to" value="<?php echo e($fetchedData->first_name); ?> <?php echo e($fetchedData->last_name); ?>">
								<input type="hidden" class="form-control" name="to" value="<?php echo e($fetchedData->email); ?>">
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_cc">CC </label>
								<select data-valid="" class="js-data-example-ajaxccapp" name="email_cc[]" id="email_cc_select"></select>

								<?php if($errors->has('email_cc')): ?>
									<span class="custom-error" role="alert">
										<strong><?php echo e(@$errors->first('email_cc')); ?></strong>
									</span>
								<?php endif; ?>
							</div>
						</div>

						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="template">Templates </label>
								<select data-valid="" class="form-control select2 selectapplicationtemplate" name="template" id="email_template_select">
									<option value="">Select</option>
									<?php $__currentLoopData = \App\Models\CrmEmailTemplate::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($list->id); ?>"><?php echo e($list->name); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>

							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="subject">Subject <span class="span_req">*</span></label>
								<?php echo Form::text('subject', '', array('class' => 'form-control selectedappsubject', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Subject' )); ?>

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
								<textarea class="summernote-simple selectedmessage" name="message"></textarea>
								<?php if($errors->has('message')): ?>
									<span class="custom-error" role="alert">
										<strong><?php echo e(@$errors->first('message')); ?></strong>
									</span>
								<?php endif; ?>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('appkicationsendmail')" type="button" class="btn btn-primary">Send</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<!-- Payment Schedule Modal -->
<div class="modal fade custom_modal paymentschedule" id="create_apppaymentschedule" tabindex="-1" role="dialog" aria-labelledby="create_paymentscheduleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="paymentscheduleModalLabel">Payment Schedule Setup</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?php echo e(URL::to('/admin/setup-paymentschedule')); ?>" name="setuppaymentschedule" id="setuppaymentschedule" autocomplete="off" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
				<input type="hidden" name="application_id" id="application_id" value="">
				<input type="hidden" name="is_ajax" id="is_ajax" value="true">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="installment_date">Installment Date <span class="span_req">*</span></label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<?php echo Form::text('installment_date', '', array('class' => 'form-control datepicker', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Select Date' )); ?>

								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error installment_date_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="row">
								<div class="col-md-12">
									<label for="installment_date">Installment Interval  <span class="span_req">*</span></label>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<?php echo Form::text('installment_no', 1, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )); ?>

									</div>
								</div>
								<div class="col-md-8">
									<div class="input-group">
										<select class="form-control" name="installment_intervel">
											<option value="">Select Intervel</option>
											<option value="Day">Day</option>
											<option value="Week">Week</option>
											<option value="Month">Month</option>
											<option value="Year">Year</option>

										</select>
									</div>
								</div>
							</div>
						</div>

						<div class="clearfix"></div>
						<div class="divider"></div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="schedule_title">
								<h4>Setup Invoice Scheduling</h4>
							</div>
							<span class="schedule_note"><i class="fa fa-explanation-circle"></i> Schedule your Invoices by selecting an Invoice date for this installment.</span>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="invoice_date">Invoice Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<?php echo Form::text('invoice_date', '', array('class' => 'form-control datepicker', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Select Date' )); ?>

								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error installment_date_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

					</div>
					<div class="clearfix"></div>
					<div class="divider"></div>
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('setuppaymentschedule')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<!-- Payment Schedule Modal -->
<div class="modal fade custom_modal" id="editpaymentschedule" tabindex="-1" role="dialog" aria-labelledby="paymentscheModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="paymentscheModalLabel">Edit Payment Schedule</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showeditmodule">

			</div>
		</div>
	</div>
</div>


<!-- Payment Schedule Modal -->
<div class="modal fade add_payment_schedule custom_modal" id="addpaymentschedule" tabindex="-1" role="dialog" aria-labelledby="paymentscheModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="paymentscheModalLabel">Add Payment Schedule</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showpoppaymentscheduledata">

			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="opencreateinvoiceform" tabindex="-1" role="dialog" aria-labelledby="paymentscheModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="paymentscheModalLabel">Select Invoice Type:</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		<div class="modal-body">
		<form method="post" action="<?php echo e(URL::to('/admin/create-invoice')); ?>" name="createinvoive"  autocomplete="off" enctype="multipart/form-data">
			<?php echo csrf_field(); ?>
			<input type="hidden" name="client_id" id="invoice_client_id">
			<input type="hidden" name="application" id="app_id">
			<input type="hidden" name="schedule_id" id="schedule_id">
					<div class="row">
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="netclaim"><input id="netclaim" value="1" type="radio" name="invoice_type" > Net Claim</label>
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="grossclaim"><input value="2" id="grossclaim" type="radio" name="invoice_type" > Gross Claim</label>
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="geclaim"><input value="3" id="geclaim" type="radio" name="invoice_type" > Client General</label>
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<button onclick="customValidate('createinvoive')" class="btn btn-info" type="button">Create</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="uploadmail" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="paymentscheModalLabel">Upload Mail:</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			<form method="post" action="<?php echo e(URL::to('/admin/upload-mail')); ?>" name="uploadmail"  autocomplete="off" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
				<input type="hidden" name="client_id" id="maclient_id">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="">From <span class="span_req">*</span></label>
								<input type="text" data-valid="required" name="from" class="form-control">
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="">To <span class="span_req">*</span></label>
								<input type="text" data-valid="required" name="to" class="form-control">
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="">Subject <span class="span_req">*</span></label>
								<input type="text" data-valid="required" name="subject" class="form-control">
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Message <span class="span_req">*</span></label>
								<textarea data-valid="required" class="summernote-simple selectedmessage" name="message"></textarea>

							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<button onclick="customValidate('uploadmail')" class="btn btn-info" type="button">Create</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="openfileuploadmodal" tabindex="-1" role="dialog" aria-labelledby="paymentscheModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="paymentscheModalLabel">Upload Document</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
				<style>
#ddArea {height: 200px;border: 2px dashed #ccc;line-height: 200px;font-size: 20px;background: #f9f9f9;margin-bottom: 15px;}
.drag_over {color: #000;border-color: #000;}
.thumbnail {width: 100px;height: 100px;padding: 2px;margin: 2px;border: 2px solid lightgray;border-radius: 3px;float: left;}
.d-none {display: none;}
				</style>
					<div class="col-md-8">
					<input type="hidden" class="checklisttype" value="">
					<input type="hidden" class="checklisttypename" value="">
					<input type="hidden" class="checklistid" value="">
					<input type="hidden" class="application_id" value="">
                    <input type="hidden" class="app_doc_client_id" value="">
						<div id="ddArea" style="text-align: center;">
							Click or drag to upload new file from your device

							<a style="display: none;" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent ">

							</a>
						</div>

						<input type="file" class="d-none" id="selectfile" />
					</div>
					<div class="col-md-4">
						<div id="showThumb">
							<ul>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>



<!-- Create Client Receipt Modal -->
<div class="modal fade custom_modal" id="createclientreceiptmodal" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientReceiptModalLabel">Create Client Receipt</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                <input type="hidden"  id="top_value_db" value="">
				<form method="post" action="<?php echo e(URL::to('/admin/clients/saveaccountreport')); ?>" name="create_client_receipt" autocomplete="off" id="create_client_receipt" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
				<input type="hidden" name="client_id" value="<?php echo e($fetchedData->id); ?>">
                <input type="hidden" name="loggedin_userid" value="<?php echo e(@Auth::user()->id); ?>">
                <input type="hidden" name="receipt_type" value="1">
                <input type="hidden" name="function_type" id="function_type" value="">
					<div class="row">
						<div class="col-6 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="client">Client <span class="span_req">*</span></label>
								<?php echo Form::text('client', @$fetchedData->first_name.' '.@$fetchedData->last_name, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'' )); ?>

								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

                        <div class="col-6 col-md-6 col-lg-6 d-none">
                            <div class="form-group">
                                <label for="agent_id">Agent <span class="span_req">*</span></label>
                                <select class="form-control select2" name="agent_id" id="sel_client_agent_id">
                                    <option value="">Select Agent</option>
                                    <?php $__currentLoopData = \App\Models\Agent::where('status',1)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $aplist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($aplist->id); ?>"><?php echo e(@$aplist->full_name); ?> (<?php echo e(@$aplist->email); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
                                <table border="1" style="margin-bottom:0rem !important;" class="table text_wrap table-striped table-hover table-md vertical_align">
                                    <thead>
                                        <tr>
                                            <th style="width:15%;">Trans. Date</th>
                                            <th style="width:15%;">Entry Date</th>
                                            <th style="width:15%;">Trans. No</th>
                                            <th style="width:5%;">Payment Method</th>
                                            <th style="width:35%;">Description</th>
                                            <th style="width:14%;">Deposit</th>
                                            <th style="width:1%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="productitem">
                                        <tr class="clonedrow">
                                            <td>
                                                <input data-valid="required"  class="form-control report_date_fields" name="trans_date[]" type="text" value="" />
                                            </td>
                                            <td>
                                                <input data-valid="required" class="form-control report_entry_date_fields" name="entry_date[]" type="text" value="" />
                                            </td>
                                            <td>
                                                <input class="form-control unique_trans_no" type="text" value="" readonly/>
                                                <input class="unique_trans_no_hidden" name="trans_no[]" type="hidden" value="" />
                                            </td>
                                            <td>
                                                <select data-valid="required" class="form-control" name="payment_method[]">
                                                    <option value="">Select</option>
                                                    <option value="Cash">Cash</option>
                                                    <option value="Bank transfer">Bank transfer</option>
                                                    <option value="EFTPOS">EFTPOS</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input data-valid="required" class="form-control" name="description[]" type="text" value="" />
                                            </td>

                                            <td>
                                                <span class="currencyinput" style="display: inline-block;">$</span>
                                                <input data-valid="required" style="display: inline-block;" class="form-control deposit_amount_per_row" name="deposit_amount[]" type="text" value="" />
                                            </td>

                                            <td>
                                                <a class="removeitems" href="javascript:;"><i class="fa fa-times"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table border="1" class="table text_wrap table-striped table-hover table-md vertical_align">
                                    <tbody>
                                        <tr>
                                            <td colspan="5" style="width:83.6%;text-align:right;">Totals</td>
                                            <td colspan="2">
                                                <span class="total_deposit_amount_all_rows"></span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
						</div>

                        <div class="col-3 col-md-3 col-lg-3">
                            <!--<a href="javascript:;" class="openproductrinfo"><i class="fa fa-plus"></i> Add New Line</a>-->
                        </div>

						<div class="col-9 col-md-9 col-lg-9 text-right">

                            <div class="upload_client_receipt_document" style="display:inline-block;">
                                <input type="hidden" name="type" value="client">
                                <input type="hidden" name="doctype" value="client_receipt">
                                <a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>
                                <input class="docclientreceiptupload" type="file" name="document_upload[]"/>
                            </div>

                            <button onclick="customValidate('create_client_receipt')" type="button" class="btn btn-primary">Save Receipt</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
                    </div>
				</form>
            </div>
		</div>
	</div>
</div>

<!-- Create All document Docs Modal -->
<div class="modal fade create_alldocument_docs custom_modal" id="openalldocsmodal" tabindex="-1" role="dialog" aria-labelledby="checklistModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="checklistModalLabel">Add Checklist</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?php echo e(URL::to('/admin/add-alldocchecklist')); ?>" name="alldocs_upload_form" id="alldocs_upload_form" autocomplete="off"  enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="clientid" value="<?php echo e($fetchedData->id); ?>">
                    <input type="hidden" name="type" value="client">
                    <input type="hidden" name="doctype" value="documents">

                    <div class="row">
                        <div class="col-6 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="checklist">Select Checklist<span class="span_req">*</span></label>
								<select data-valid="required" class="form-control select2" name="checklist[]" id="checklist" multiple>
									<option value="">Select</option>
									<?php
									$eduChkList = \App\Models\DocumentChecklist::where('status',1)->get();
									foreach($eduChkList as $edulist){
									?>
										<option value="<?php echo e($edulist->name); ?>"><?php echo e($edulist->name); ?></option>
									<?php
									}
									?>
								</select>
								<span class="custom-error checklist_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
                    </div>

                    <div class="row">
						<!--<div class="col-9 col-md-9 col-lg-9">
							<div class="form-group">
								<label for="attachments">Upload Document<span class="span_req">*</span></label>
								<div class="custom-file">
									<input data-valid="required" class="docupload" multiple type="file" name="document_upload[]"/>
								</div>
								<span class="custom-error document_upload_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>-->
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('alldocs_upload_form')" type="button" class="btn btn-primary">Create</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<!-- Add application assign user Modal -->
<div class="modal fade add_appointment custom_modal" id="create_applicationaction" tabindex="-1" role="dialog" aria-labelledby="create_appointModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appointModalLabel">Assign User</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?php echo e(URL::to('/admin/clients/followup_application/store_application')); ?>" name="appliassignform" id="appliassignform" autocomplete="off" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="client_id" value="<?php echo e($fetchedData->id); ?>">
                    <input type="hidden" id="assign_application_id" name="application_id" value="">
                    <input type="hidden" id="stage_name" name="stage_name" value="">
                    <input type="hidden" name="atype" value="application">
                    <input type="hidden" name="course" id="course" value="">
                    <input type="hidden" name="school" id="school" value="">

					<div class="row">
                        <div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="course">Course:</label>
								<span id="course_s"></span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="school">School:</label>
                                <span id="school_s"></span>
							</div>
						</div>

                        <div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="stage">Stage</label>
                                <span id="stage_name_f"></span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="rem_cat11">Select Assignee <span class="span_req">*</span></label>
								<select class="assigneeselect211 form-control selec_reg11" id="rem_cat11" name="rem_cat11" data-valid="required">
                                    <option value="">Select</option>
                                    <?php $__currentLoopData = \App\Models\Admin::select('id', 'office_id', 'first_name', 'last_name')->where('role','!=',7)->where('status',1)->orderby('first_name','ASC')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                    $branchname = \App\Models\Branch::select('id', 'office_name')->where('id',$admin->office_id)->first();
                                    ?>
                                    <option value="<?php echo $admin->id; ?>"><?php echo $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')'; ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('appliassignform')" type="button" class="btn btn-primary">Assign User</button>
							<!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Refund Appliation Modal -->
<div class="modal fade custom_modal" id="refund_application" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="refundApplicationModalLabel">Refund Application</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?php echo e(URL::to('/admin/refund_application')); ?>" name="refund_app" id="refund_app" autocomplete="off" enctype="multipart/form-data">
				    <?php echo csrf_field(); ?>
				    <input type="hidden" name="reapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="">Notes <span class="span_req">*</span></label>
								<textarea data-valid="required"  class="form-control" name="refund_note" id="refund_note_textarea"></textarea>
                            </div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('refund_app')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<!-- NP message -->
<div class="modal fade" id="notPickedCallModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Are you sure want to send text message to this user?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="messageText" rows="10" style="height: 130px !important;"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary sendMessage">Send</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\clients\addclientmodal.blade.php ENDPATH**/ ?>