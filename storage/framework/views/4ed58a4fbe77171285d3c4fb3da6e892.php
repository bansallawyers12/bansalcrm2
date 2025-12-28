<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
	<meta name="description" content="Best tour planner is leading travel agency in delhi offer best holiday packages services">
	<meta name="author" content="Ansonika">
	<link rel="shortcut icon" type="image/png" href="<?php echo asset('img/favicon.png'); ?>"/>
	<title>CRM Digitrex | <?php echo $__env->yieldContent('title'); ?></title>
	<!-- Favicons-->
	<link rel="shortcut icon" href="<?php echo e(asset('img/favicon.png')); ?>" type="image/x-icon">
			 
	 <!-- BASE CSS -->
	<link href="<?php echo e(asset('css/app.min.css')); ?>" rel="stylesheet">	
	<link href="<?php echo e(asset('css/bootstrap-social.css')); ?>" rel="stylesheet">	
	<link href="<?php echo e(asset('css/style.css')); ?>" rel="stylesheet">	
	<link href="<?php echo e(asset('css/components.css')); ?>" rel="stylesheet">	
	<link href="<?php echo e(asset('css/custom.css')); ?>" rel="stylesheet">	
</head>
<style>
	.bg{
		background-image: url('/img/bansal_crm_background_image.jpg');
		height: 100%;
		margin: 0;
		background-position: center;
		background-repeat: no-repeat;
		background-size: cover;
	}
	</style>
<body class="bg">
	<div class="loader"></div>
	<div id="app">
		<?php echo $__env->yieldContent('content'); ?>
	</div>
	<!-- COMMON SCRIPTS -->
	<script type="text/javascript">
		var site_url = "<?php echo URL::to('/'); ?>";
	</script>
	<script src="<?php echo e(asset('js/app.min.js')); ?>"></script>
	<script src="<?php echo e(asset('js/scripts.js')); ?>"></script>
	<script src="<?php echo e(asset('js/custom.js')); ?>"></script>
</body>
</html><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\layouts\agent-login.blade.php ENDPATH**/ ?>