<?php echo Form::open(array('url' => 'admin/followup/update', 'name'=>"edit-note", 'autocomplete'=>'off', "enctype"=>"multipart/form-data", 'id'=>"editnoteform")); ?>


	<div class="customerror"></div> 
	<div class="form-group row">
		<div class="col-sm-12">
		 <label>Note Type</label>
		<select class="form-control" name="note_type">
		    <option value=""></option>
		    <?php
				$followuptypes = \App\Models\FollowupType::where('show',1)->get();
				foreach($followuptypes as $followuptype){
				?>
				 <option <?php if($followuptype->type == $fetchedData->followup_type): ?> selected <?php endif; ?> value="<?php echo e($followuptype->type); ?>"><?php echo e($followuptype->name); ?></option>
				<?php } ?>
		</select>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-sm-12">
		
			<input id="" name="lead_id" type="hidden" value="<?php echo e(base64_encode(convert_uuencode(@$fetchedData->id))); ?>">
			<textarea id="description" name="description" class="form-control summernote-simple" placeholder="Add note" style=""><?php echo e($fetchedData->note); ?></textarea>
		</div>
	</div>


 <?php echo Form::close(); ?>

 <div class="modal-footer">
	<?php echo Form::button('<i class="fa fa-save"></i> Save', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("edit-note")' ]); ?>

</div><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Admin\leads\editnotemodal.blade.php ENDPATH**/ ?>