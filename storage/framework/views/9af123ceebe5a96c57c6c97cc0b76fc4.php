
<?php $__env->startSection('title', 'Unpaid Invoices'); ?>

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
							<h4>All Payments</h4>
							<div class="card-header-action">								
								
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive common_table"> 
								<table class="table text_wrap">
									<thead>
										<tr> 
											<th>Date</th>
											<th>Payment By</th>
											<th>Invoice No.</th>
											<th>Amount</th>			
											<th>Payment Type</th>
											<th>Action</th>
										</tr> 
									</thead>
									<?php if($totalData !== 0): ?>									
									<tbody class="tdata">
										<?php $__currentLoopData = @$lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr id="id_<?php echo e(@$list->id); ?>">
											<td><?php echo e(date('d/m/Y', strtotime($list->payment_date))); ?></td>
											
											<td ><?php echo e(@$list->invoice->customer->first_name); ?> <?php echo e(@$list->invoice->customer->last_name); ?></td>
											<td><?php echo e(@$list->invoice_id); ?></td>
											<td><?php echo e(number_format($list->amount_rec,2,'.','')); ?>	</td>
											<td><?php echo e(@$list->payment_mode); ?></td>
											<td>
												<div class="dropdown d-inline">
													<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
													<div class="dropdown-menu">
														<a target="_blank" class="dropdown-item has-icon" href="<?php echo e(URL::to('admin/payment/view/')); ?>/<?php echo e(base64_encode(convert_uuencode(@$list->id))); ?>"><i class="fa fa-eye"></i> View Receipt</a>
														<a data-id="<?php echo e($list->id); ?>" data-rec-name="receipt_<?php echo e($list->id); ?>.pdf" data-href="<?php echo e(URL::to('admin/payment/view/')); ?>/<?php echo e(base64_encode(convert_uuencode(@$list->id))); ?>" data-cus-id="<?php echo e(@$list->invoice->customer->id); ?>" data-email="<?php echo e(@$list->invoice->customer->email); ?>" data-name="<?php echo e(@$list->invoice->customer->first_name); ?> <?php echo e(@$list->invoice->customer->last_name); ?>" href="javascript:;" class="clientemail dropdown-item has-icon"><i class="far fa-envelope"></i> Email Receipt</a>
													</div>
												</div>		
											</td>
										</tr>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</tbody>
								<?php else: ?>
									<tfoot>
										<tr>
											<td colspan="6" class="text-center">No Record Found</td>
										</tr>
									</tfoot>
								<?php endif; ?>
								</table>
							</div>
						</div>
						<div class="card-footer">
							
						</div>
					</div> 
				</div>
			</div>
		</div>
	</section>
</div>


<div id="emailmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
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
				<input type="hidden" name="type" value="client">
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
								<select data-valid="" class="js-data-example-ajaxcc" name="email_cc[]"></select>
								
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
							<div class="form-group">
								<label for="receipt"><input type="checkbox" checked name="receipt" value=""> <a target="_blank" href="#" id="receipt"></a></label> 
							</div>
						</div>
						<!--<div class="col-12 col-md-12 col-lg-12">
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
						</div> -->
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

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>

<script>
jQuery(document).ready(function($){
	$(document).delegate('.clientemail', 'click', function(){
		$('#emailmodal').modal('show');
		$('#receipt').html('');
		$('#receipt').attr('href', '#');
		$('input[name="receipt"]').val('');
		var recname = $(this).attr('data-rec-name');
		var recid = $(this).attr('data-id');
		var href = $(this).attr('data-href');
		$('#receipt').html(recname);
		$('input[name="receipt"]').val(recid);
		$('#receipt').attr('href', href);
		var array = [];
		var data = [];

			
				var id = $(this).attr('data-cus-id');
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
		   "<div class='ag-flex ag-flex-column ag-align-end'>" +
			
			"<span class='ui label yellow select2-result-repository__statistics'>"+ status +
			  
			"</span>" +
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
	$('.js-data-example-ajaxcc').select2({
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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\account\payment.blade.php ENDPATH**/ ?>