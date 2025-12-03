<?php if($message = Session::get('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">☓</button>
        <strong><?php echo e($message); ?></strong>
</div>
<?php endif; ?>
<?php if($message = Session::get('error')): ?>
<div class="alert alert-danger alert-dismissible fade show">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">☓</button>
        <strong><?php echo e($message); ?></strong>
</div>
<?php endif; ?>

<?php if($message = Session::get('warning')): ?>
<div class="alert alert-warning alert-dismissible fade show">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">☓</button>
	<strong><?php echo e($message); ?></strong>
</div>
<?php endif; ?>
<?php if($message = Session::get('info')): ?>
<div class="alert alert-info alert-dismissible fade show">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">☓</button>
	<strong><?php echo e($message); ?></strong>
</div>
<?php endif; ?>

<?php if($errors->any()): ?>
<div class="alert alert-danger alert-dismissible fade show">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">☓</button>	
	Please check the form below for errors
</div>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\bansalcrm\resources\views////Elements/flash-message.blade.php ENDPATH**/ ?>