
<?php $__env->startSection('title', 'Agents'); ?>

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
							<h4>All Agents</h4>
							<div class="card-header-action">
								<a href="<?php echo e(route('admin.agents.create')); ?>" class="btn btn-primary">Create Agent</a>
								<a href="javascript:;" class="btn btn-primary openimportmodal">Import</a>
							</div>
						</div>
						<div class="card-body">
							<ul class="nav nav-pills" id="client_tabs" role="tablist">
								<li class="nav-item is_checked_client" style="display:none;">
									<a class="btn btn-primary emailmodal" id=""  href="javascript:;"  >Send Mail</a>
								</li>
								<li class="nav-item is_checked_clientn">
									<a class="nav-link active" id="active-tab"  href="<?php echo e(URL::to('/admin/agents/active')); ?>" >Active</a>
								</li>
								<li class="nav-item is_checked_clientn">
									<a class="nav-link" id="inactive-tab"  href="<?php echo e(URL::to('/admin/agents/inactive')); ?>" >Inactive</a>
								</li>
							</ul> 
							<div class="tab-content" id="clientContent">								
								<div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
									<div class="table-responsive common_table"> 
										<table class="table text_wrap">
											<thead>
												<tr> 
													<th class="text-center" style="width:30px;">
														<div class="custom-checkbox custom-checkbox-table custom-control">
															<input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad" class="custom-control-input" id="checkbox-all">
															<label for="checkbox-all" class="custom-control-label">&nbsp;</label>
														</div>
													</th>	
													<th>Name</th>
													<th>Type</th>
													<th>Structure</th>
													<!--<th>Phone</th>-->
													<th>City</th>
													<th>Associated Office</th>
													<th>Clients Count</th>
													<th>Applications Count</th>
													<th>Status</th>
													<th></th>
												</tr> 
											</thead>
											
											<tbody class="tdata">	
												<?php if(@$totalData !== 0): ?>
													<?php $i=0; ?>
												<?php $__currentLoopData = @$lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<tr id="id_<?php echo e(@$list->id); ?>"> 
													<td style="white-space: initial;" class="text-center">
														<div class="custom-checkbox custom-control">
															<input data-id="<?php echo e(@$list->id); ?>" data-email="<?php echo e(@$list->email); ?>" data-name="<?php echo e(@$list->full_name); ?>" type="checkbox" data-checkboxes="mygroup" class="cb-element custom-control-input" id="checkbox-<?php echo e($i); ?>">
															<label for="checkbox-<?php echo e($i); ?>" class="custom-control-label">&nbsp;</label>
														</div>
													</td>
													<td style="white-space: initial;"><a href="<?php echo e(URL::to('/admin/agent/detail/'.base64_encode(convert_uuencode(@$list->id)))); ?>"><?php echo e(@$list->full_name == "" ? config('constants.empty') : str_limit(@$list->full_name, '50', '...')); ?></a> <br/></td> 
													<td style="white-space: initial;"><?php echo e(@$list->agent_type == "" ? config('constants.empty') : str_limit(@$list->agent_type, '50', '...')); ?></td>
													<td style="white-space: initial;"><?php echo e(@$list->struture == "" ? config('constants.empty') : str_limit(@$list->struture, '50', '...')); ?></td>
													<!--<td>--><!--</td>-->	
													<td style="white-space: initial;"><?php echo e(@$list->city == "" ? config('constants.empty') : str_limit(@$list->city, '50', '...')); ?></td> 	
													<td style="white-space: initial;"><?php echo e(@$list->related_office == "" ? config('constants.empty') : str_limit(@$list->related_office, '50', '...')); ?></td> 	
													<td style="white-space: initial;">0</td> 	
													<td style="white-space: initial;">0</td> 
													<td style="white-space: initial;">
													    <div class="custom-switches">
									<label class="">
										<input value="1" data-id="<?php echo e(@$list->id); ?>"  data-status="<?php echo e(@$list->status); ?>" data-col="status" data-table="agents" type="checkbox" name="custom-switch-checkbox" class="change-status custom-switch-input" <?php echo e((@$list->status == 1 ? 'checked' : '')); ?>>
										<span class="custom-switch-indicator"></span>
									</label>
								</div>
													</td>
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">
																<a class="dropdown-item has-icon" href="<?php echo e(URL::to('/admin/agents/edit/'.base64_encode(convert_uuencode(@$list->id)))); ?>"><i class="far fa-edit"></i> Edit</a>
																<a class="dropdown-item has-icon" href="javascript:;" onclick="deleteAction(<?php echo e($list->id); ?>, 'agents')"><i class="fas fa-trash"></i> Archived</a>
															</div>
														</div>								  
													</td>
												</tr>	
												<?php $i++; ?>
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

<div id="emailmodal" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">Compose Email</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" name="sendmail" action="<?php echo e(URL::to('/admin/sendmail')); ?>" autocomplete="off" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
				<input type="hidden" name="type" value="agent">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">From <span class="span_req">*</span></label>
								<?php echo Form::text('email_from', 'support@digitrex.live', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter From' )); ?>

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
								<select data-valid="required" class="js-data-example-ajax" name="email_to[]"></select>
								
								<?php if($errors->has('email_to')): ?>
									<span class="custom-error" role="alert">
										<strong><?php echo e(@$errors->first('email_to')); ?></strong>
									</span> 
								<?php endif; ?>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_cc">CC </label>
								<select data-valid="" class="js-data-example-ajaxccdd" name="email_cc[]"></select>
								
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
								<select data-valid="" class="form-control select2 selecttemplate" name="template">
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
								<?php echo Form::text('subject', '', array('class' => 'form-control selectedsubject', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Subject' )); ?>

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
							<button onclick="customValidate('sendmail')" type="button" class="btn btn-primary">Send</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="modal fade custom_modal" id="openimportmodal" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel">Import Agents</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				 <div class="col-md-12">	
					<h5>Business</h5>
					<a href="<?php echo e(URL::to('admin/agents/import/business')); ?>" style="background-color: transparent;color: #9c9c9c;fill: #9c9c9c;width: 48%;border: 1px solid #9c9c9c;display: inline-flex;" class="btn btn-info defaultButton ghostButton">Import Business Agents</a>
				 </div>
				  <div class="col-md-12" style="margin-top: 20px!important;">
				  <h5>Individual</h5>
					<a href="<?php echo e(URL::to('admin/agents/import/individual')); ?>" style="background-color: transparent;color: #9c9c9c;fill: #9c9c9c;width: 48%;border: 1px solid #9c9c9c;display: inline-flex;" class="btn btn-info defaultButton ghostButton">Import Individual Agents</a>
				 </div>
			</div>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script>
jQuery(document).ready(function($){
	$(document).delegate('.openimportmodal','click', function(){
		$('#openimportmodal').modal('show');
	});
	$("[data-checkboxes]").each(function () {
  var me = $(this),
    group = me.data('checkboxes'),
    role = me.data('checkbox-role');

  me.change(function () {
    var all = $('[data-checkboxes="' + group + '"]:not([data-checkbox-role="dad"])'),
      checked = $('[data-checkboxes="' + group + '"]:not([data-checkbox-role="dad"]):checked'),
      dad = $('[data-checkboxes="' + group + '"][data-checkbox-role="dad"]'),
      total = all.length,
      checked_length = checked.length;

    if (role == 'dad') {
      if (me.is(':checked')) {
        all.prop('checked', true);
		$('.is_checked_client').show();
		$('.is_checked_clientn').hide();
      } else {
        all.prop('checked', false);
		$('.is_checked_client').hide();
		$('.is_checked_clientn').show();
      }
    } else {
      if (checked_length >= total) {
        dad.prop('checked', true);
			$('.is_checked_client').show();
		$('.is_checked_clientn').hide();
      } else {
        dad.prop('checked', false);
		$('.is_checked_client').hide();
		$('.is_checked_clientn').show();
      }
    }
  });
});

$('.cb-element').change(function () {
	
 if ($('.cb-element:checked').length == $('.cb-element').length){
  $('#checkbox-all').prop('checked',true);
 }
 else {
  $('#checkbox-all').prop('checked',false);
 }

 if ($('.cb-element:checked').length > 0){
		$('.is_checked_client').show();
		$('.is_checked_clientn').hide();
	}else{
		$('.is_checked_client').hide();
		$('.is_checked_clientn').show();
	}
});
$(document).delegate('.emailmodal', 'click', function(){

	$('#emailmodal').modal('show');
	var array = [];
	var data = [];
	$('.cb-element:checked').each(function(){
		
			var id = $(this).attr('data-id');
			 array.push(id);
			var email = $(this).attr('data-email');
			var name = $(this).attr('data-name');
			var status = 'Client';
			
			data.push({
				id: id,
  text: name,
  html:  "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

      "<div  class='ag-flex ag-align-start'>" +
        "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'>"+name+"</span>&nbsp;</div>" +
        "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'>"+email+"</small ></div>" +
      
      "</div>" +
      "</div>" +
	  
    "</div>",
  title: name
				});
		


	});
	
	$(".js-data-example-ajax").select2({
  data: data,
  escapeMarkup: function(markup) {
    return markup;
  },
  templateResult: function(data) {
    return data.html;
  },
  templateSelection: function(data) {
    return data.text;
  }
})
	$('.js-data-example-ajax').val(array);
		$('.js-data-example-ajax').trigger('change');
	
});

$(document).delegate('.clientemail', 'click', function(){
	$('#emailmodal').modal('show');
	var array = [];
	var data = [];

		
			var id = $(this).attr('data-id');
			 array.push(id);
			var email = $(this).attr('data-email');
			var name = $(this).attr('data-name');
			var status = 'Client';
			
			data.push({
				id: id,
  text: name,
  html:  "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

      "<div  class='ag-flex ag-align-start'>" +
        "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'>"+name+"</span>&nbsp;</div>" +
        "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'>"+email+"</small ></div>" +
      
      "</div>" +
      "</div>" +
	  
    "</div>",
  title: name
				});
	
	$(".js-data-example-ajax").select2({
  data: data,
  escapeMarkup: function(markup) {
    return markup;
  },
  templateResult: function(data) {
    return data.html;
  },
  templateSelection: function(data) {
    return data.text;
  }
})
	$('.js-data-example-ajax').val(array);
		$('.js-data-example-ajax').trigger('change');
	
});
$(document).delegate('.selecttemplate', 'change', function(){
	var v = $(this).val();
	$.ajax({
		url: '<?php echo e(URL::to('/admin/get-templates')); ?>',
		type:'GET',
		datatype:'json',
		data:{id:v},
		success: function(response){
			var res = JSON.parse(response);
			$('.selectedsubject').val(res.subject);
			 $(".summernote-simple").summernote('reset');  
                    $(".summernote-simple").summernote('code', res.description);  
					$(".summernote-simple").val(res.description); 
			
		}
	});
});
	$('.js-data-example-ajax').select2({
		 multiple: true,
		 closeOnSelect: false,
		dropdownParent: $('#emailmodal'),
		  ajax: {
			url: '<?php echo e(URL::to('/admin/clients/get-recipients')); ?>',
			dataType: 'json',
			processResults: function (data) {
			  // Transforms the top-level key of the response object from 'items' to 'results'
			  return {
				results: data.items
			  };
			  
			},
			 cache: true
			
		  },
	templateResult: formatRepo,
	templateSelection: formatRepoSelection
});

$('.js-data-example-ajaxccdd').select2({
		 multiple: true,
		 closeOnSelect: false,
		dropdownParent: $('#emailmodal'),
		  ajax: {
			url: '<?php echo e(URL::to('/admin/clients/get-recipients')); ?>',
			dataType: 'json',
			processResults: function (data) {
			  // Transforms the top-level key of the response object from 'items' to 'results'
			  return {
				results: data.items
			  };
			  
			},
			 cache: true
			
		  },
	templateResult: formatRepo,
	templateSelection: formatRepoSelection
});
function formatRepo (repo) {
  if (repo.loading) {
    return repo.text;
  }

  var $container = $(
    "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

      "<div  class='ag-flex ag-align-start'>" +
        "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
        "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small ></div>" +
      
      "</div>" +
      "</div>" +
	   "<div class='ag-flex ag-flex-column ag-align-end'>" +
        
        "<span class='ui label yellow select2-result-repository__statistics'>" +
          
        "</span>" +
      "</div>" +
    "</div>"
  );

  $container.find(".select2-result-repository__title").text(repo.name);
  $container.find(".select2-result-repository__description").text(repo.email);
  $container.find(".select2-result-repository__statistics").append(repo.status);
 
  return $container;
}

function formatRepoSelection (repo) {
  return repo.name || repo.text;
}
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\agents\active.blade.php ENDPATH**/ ?>