<!DOCTYPE html>
<html lang="en">
	<head>
		<base href="./">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
		<meta name="description" content="ApnaMentor for higher education">
		<meta name="author" content="Raghav Garg">
		<meta name="keyword" content="Bootstrap,Admin,Template,Open,Source,jQuery,CSS,HTML,RWD,Dashboard">
		<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
		<title>Tour Planner | Exception</title>
    
		<!-- Icons-->
			<!-- Removed broken references: @coreui/icons, flag-icon-css, simple-line-icons (not installed) -->
			<link rel="stylesheet" type="text/css" href="<?php echo e(asset('icons/font-awesome/css/all.min.css')); ?>" />
		
		<!-- Main styles for this application-->
			<link rel="stylesheet" type="text/css" href="<?php echo e(asset('css/style.css')); ?>" />
			<link rel="stylesheet" type="text/css" href="<?php echo e(asset('css/pace.min.css')); ?>" />
			<link rel="stylesheet" type="text/css" href="<?php echo e(asset('css/admin.css')); ?>" />
	</head>
	<body class="app flex-row align-items-center">
		<div id="loader">
			<div class="loading_image">
				<div class="valid">
					<img src="<?php echo e(asset('img/loading.gif')); ?>">
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-8">
					<?php echo $__env->make('Elements/flash-message', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
					<div class="card-group">
						<div class="card p-4">
							<?php echo Form::open(array('url' => '/exception', 'name'=>'exception')); ?>

								<div class="card-body">
									<h1>Exception</h1>
									<div class="input-group mb-3">
										<textarea class="form-control" name="comment" placeholder="Please write comment, what did you face." data-valid="required"></textarea>	
									</div>
									<div class="row">
										<div class="col-6">
											<?php echo Form::button('Post', ['class'=>'btn btn-primary px-4', 'onClick'=>'customValidate("exception")']); ?>	
										</div>
									</div>
								</div>
							<?php echo Form::close(); ?>

						</div>
						<div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%">
							<div class="card-body text-center">
								<div>
									<p>Please write your comment, if you are seeing this page.</p>
									<p>This page occur because you are facing any issue, so please write your comment what your are exactly facing, so we can resolve as soon as possible</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Apnamentor necessary plugins-->
			<script src="<?php echo e(asset('js/jquery/js/jquery.min.js')); ?>"></script>
			<script src="<?php echo e(asset('js/popper.js/js/popper.min.js')); ?>"></script>
			<script src="<?php echo e(asset('js/bootstarp/js/bootstrap.min.js')); ?>"></script>
			<script src="<?php echo e(asset('js/pace-progress/js/pace.min.js')); ?>"></script>
			<script src="<?php echo e(asset('js/perfect-scrollbar/js/perfect-scrollbar.min.js')); ?>"></script>
			<!-- Removed broken reference: icons/@coreui/coreui-pro/js/coreui.min.js (not installed) -->
			<script src="<?php echo e(asset('js/custom-form-validation.js')); ?>"></script>
	</body>
</html><?php /**PATH C:\xampp\htdocs\bansalcrm2\resources\views\exception.blade.php ENDPATH**/ ?>