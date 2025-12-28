<?php if(Auth::user()->role == 1): ?>
	<li class="breadcrumb-menu d-md-down-none">
		<div class="btn-group" role="group" aria-label="Button group">
			<a class="btn" href="<?php echo e(URL::to('/admin/website_setting')); ?>">
			<i class="icon-settings"></i>  Website Settings</a>
		</div>
	</li>
<?php endif; ?>	<?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\Elements\Admin\breadcrumb.blade.php ENDPATH**/ ?>