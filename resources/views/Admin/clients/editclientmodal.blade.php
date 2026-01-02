<!-- Appointment Modal -->
<div class="modal fade custom_modal" id="edit_appointment" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel">Edit Appointment</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button> 
			</div>
			<div class="modal-body showappointmentdetail">
				<h4>Please Wait ......</h4>
			</div>
			
		</div>
	</div>
</div>

<!-- Note & Terms Modal -->
<div class="modal fade custom_modal" id="edit_note" tabindex="-1" role="dialog" aria-labelledby="create_noteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Create Note</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/create-note')}}" name="editnotetermform" autocomplete="off" id="editnotetermform" enctype="multipart/form-data">
				@csrf 
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
				<input type="hidden" name="noteid" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="title">Title <span class="span_req">*</span></label>
								{!! Form::text('title', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Title' ))  !!}
								<select name="title" class="form-control" data-valid="required">
								    <option value="">Please Select Note</option>
								    <option value="Call" <?php if($fetchedData->title = 'Call') { echo 'selected'; } ?>>Call</option>
								    <option value="Email" <?php if($fetchedData->title = 'Email') { echo 'selected'; } ?>>Email</option>
								    <option value="In-Person" <?php if($fetchedData->title = 'In-Person') { echo 'selected'; } ?>>In-Person</option>
								    <option value="Others" <?php if($fetchedData->title = 'Others') { echo 'selected'; } ?>>Others</option>
								    <option value="Attention" <?php if($fetchedData->title = 'Attention') { echo 'selected'; } ?>>Attention</option>
								</select>
								
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
						<!--<div class="col-12 col-md-12 col-lg-12 is_not_note" style="display:none;">
							<div class="form-group"> 
								<label class="d-block" for="related_to">Related To</label> 
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="contact" value="Contact" name="related_to" checked>
									<label class="form-check-label" for="contact">Contact</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="partner" value="Partner" name="related_to">
									<label class="form-check-label" for="partner">Partner</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="application" value="Application" name="related_to">
									<label class="form-check-label" for="application">Application</label>
								</div>
							
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12 is_not_note" style="display:none;">
							<div class="form-group">
								<label for="contact_name">Contact Name <span class="span_req">*</span></label> 	
								<select data-valid="" class="form-control contact_name select2" name="contact_name">
									<option value="">Choose Contact</option>
									<option value="Amit">Amit</option>
								</select>
								<span class="custom-error contact_name_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>-->
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('editnotetermform')" type="button" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form> 
			</div>
		</div> 
	</div>
</div>

<!-- English Test Modal -->
<div class="modal fade edit_english_test custom_modal" tabindex="-1" role="dialog" aria-labelledby="editenglishModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content"> 
			<div class="modal-header">
				<h5 class="modal-title" id="editenglishModalLabel">Edit English Test Scores</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			<?php
				$testscores = \App\Models\TestScore::where('client_id', $fetchedData->id)->where('type', 'client')->first();
				$selectedTestType = 'toefl'; // Default to TOEFL
				?>
				<form method="post" action="{{URL::to('/admin/edit-test-scores')}}" name="testscoreform" autocomplete="off" id="testscoreform" enctype="multipart/form-data">
				@csrf 
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
				<input type="hidden" name="type" value="client">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="test_type">Test Type</label>
								<select class="form-control" name="test_type" id="test_type" onchange="loadTestScores()">
									<option value="toefl">TOEFL</option>
									<option value="ilets">IELTS</option>
									<option value="pte">PTE</option>
								</select>
							</div>
						</div>
					</div>
					<div class="edu_test_score edu_english_score" style="margin-bottom:15px;">
						<div class="edu_test_row" style="text-align:center;">
							<div class="edu_test_col"><span>L</span></div>
							<div class="edu_test_col"><span>R</span></div>
							<div class="edu_test_col"><span>W</span></div>
							<div class="edu_test_col"><span>S</span></div>
							<div class="edu_test_col"><span style="color:#71cc53;">O</span></div>
							<div class="edu_test_col"><span>Date</span></div>
						</div> 
						<div class="edu_test_row flex_row">
							<div class="edu_test_col">
								<div class="edu_field">
									<input type="number" class="form-control" name="listening" id="listening" step="0.01" placeholder="Listening"/>
								</div>
							</div>
							<div class="edu_test_col">
								<div class="edu_field">
									<input type="number" class="form-control" name="reading" id="reading" step="0.01" placeholder="Reading"/>
								</div>
							</div>
							<div class="edu_test_col">
								<div class="edu_field">
									<input type="number" class="form-control" name="writing" id="writing" step="0.01" placeholder="Writing"/>
								</div>
							</div>
							<div class="edu_test_col">
								<div class="edu_field">
									<input type="number" class="form-control" name="speaking" id="speaking" step="0.01" placeholder="Speaking"/>
								</div>
							</div>
							<div class="edu_test_col overal_block">
								<div class="edu_field">
									<input type="number" class="form-control" name="overall" id="overall" step="0.01" placeholder="Overall"/>
								</div>
							</div>
							<div class="edu_test_col">
								<div class="edu_field">
									<input type="text" class="form-control datepicker" name="test_date" id="test_date" placeholder="Date"/>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('testscoreform')" type="button" class="btn btn-primary">Update</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						</div>
					</div>
				</form>
				<script>
				function loadTestScores() {
					var testType = document.getElementById('test_type').value;
					var testscores = @json($testscores);
					
					if (!testscores) {
						document.getElementById('listening').value = '';
						document.getElementById('reading').value = '';
						document.getElementById('writing').value = '';
						document.getElementById('speaking').value = '';
						document.getElementById('overall').value = '';
						document.getElementById('test_date').value = '';
						return;
					}
					
					if (testType === 'toefl') {
						document.getElementById('listening').value = testscores.toefl_Listening || '';
						document.getElementById('reading').value = testscores.toefl_Reading || '';
						document.getElementById('writing').value = testscores.toefl_Writing || '';
						document.getElementById('speaking').value = testscores.toefl_Speaking || '';
						document.getElementById('overall').value = testscores.score_1 || '';
						document.getElementById('test_date').value = testscores.toefl_Date || '';
					} else if (testType === 'ilets') {
						document.getElementById('listening').value = testscores.ilets_Listening || '';
						document.getElementById('reading').value = testscores.ilets_Reading || '';
						document.getElementById('writing').value = testscores.ilets_Writing || '';
						document.getElementById('speaking').value = testscores.ilets_Speaking || '';
						document.getElementById('overall').value = testscores.score_2 || '';
						document.getElementById('test_date').value = testscores.ilets_Date || '';
					} else if (testType === 'pte') {
						document.getElementById('listening').value = testscores.pte_Listening || '';
						document.getElementById('reading').value = testscores.pte_Reading || '';
						document.getElementById('writing').value = testscores.pte_Writing || '';
						document.getElementById('speaking').value = testscores.pte_Speaking || '';
						document.getElementById('overall').value = testscores.score_3 || '';
						document.getElementById('test_date').value = testscores.pte_Date || '';
					}
				}
				// Load initial data when modal is shown
				$('.edit_english_test').on('shown.bs.modal', function () {
					loadTestScores();
				});
				</script> 
			</div>
		</div> 
	</div>
</div>

<!-- Other Test Modal -->
<div class="modal fade edit_other_test custom_modal" tabindex="-1" role="dialog" aria-labelledby="editotherModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"> 
			<div class="modal-header">
				<h5 class="modal-title" id="editotherModalLabel">Edit Other Test Scores</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/admin/other-test-scores')}}" name="othertestform" autocomplete="off" id="othertestform" enctype="multipart/form-data">
				@csrf 
				<input type="hidden" name="client_id" value="{{$fetchedData->id}}">
				<input type="hidden" name="type" value="client">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="sat_i">SAT I</label>
							
								<input type="number" class="form-control" name="sat_i" value="<?php if(@$testscores->sat_i != ''){ echo @$testscores->sat_i; }else{ echo ''; } ?>" step="0.01"/>
								
								<span class="custom-error sat_i_error" role="alert">
									<strong></strong>
								</span> 
							</div>
							<div class="form-group"> 
								<label for="sat_ii">SAT II</label>
								<input type="number" class="form-control" name="sat_ii" value="<?php if(@$testscores->sat_ii != ''){ echo @$testscores->sat_ii; }else{ echo ''; } ?>" step="0.01"/>
							
								<span class="custom-error sat_ii_error" role="alert">
									<strong></strong>
								</span> 
							</div>
							<div class="form-group">
								<label for="gre">GRE</label>
								<input type="number" class="form-control" name="gre" value="<?php if(@$testscores->gre != ''){ echo $testscores->gre; }else{ echo ''; } ?>" step="0.01"/>
								
								<span class="custom-error gre_error" role="alert">
									<strong></strong>
								</span> 
							</div>
							<div class="form-group">
								<label for="gmat">GMAT</label>
								<input type="number" class="form-control" name="gmat" value="<?php if(@$testscores->gmat != ''){ echo @$testscores->gmat; }else{ echo ''; } ?>" step="0.01"/>
								
								<span class="custom-error gmat_error" role="alert">
									<strong></strong>
								</span> 
							</div> 
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('othertestform')" type="button" class="btn btn-primary">Update</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						</div>
					</div>
				</form> 
			</div>
		</div> 
	</div>
</div>


<!-- Education Modal -->
<div class="modal fade  custom_modal" id="edit_education" tabindex="-1" role="dialog" aria-labelledby="create_educationModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Edit Education</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showeducationdetail">
				<h4>Please wait ...</h4>
			</div>
		</div>
	</div>
</div> 

<!-- Interested Service Modal -->
<div class="modal fade  custom_modal" id="eidt_interested_service" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel">Edit Interested Services</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showinterestedserviceedit">
				 
			</div>
		</div>
	</div>
</div>