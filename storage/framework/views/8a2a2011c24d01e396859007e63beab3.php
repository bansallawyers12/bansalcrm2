<style>
.ui.label {
    display: inline-block;
    line-height: 1;
    vertical-align: baseline;
    margin: 0 0.14285714em;
    background-color: #e8e8e8;
    background-image: none;
    padding: 0.5833em 0.833em;
    color: rgba(0,0,0,.6);
    text-transform: none;
    font-weight: 700;
    border: 0 solid transparent;
    border-radius: 0.28571429rem;
    -webkit-transition: background .1s ease;
    transition: background .1s ease;
}
.ui.yellow.label, .ui.yellow.labels .label {
    background-color: #fbbd08!important;
    border-color: #fbbd08!important;
    color: #fff!important;
}
.ui.red.label, .ui.red.labels .label {
    background-color: #db2828!important;
    border-color: #db2828!important;
    color: #fff!important;
}
</style>
<nav class="navbar navbar-expand-lg main-navbar sticky">
	<div class="form-inline mr-auto">
		<ul class="navbar-nav mr-3">
			<li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn"> <i data-feather="align-justify" id="feather-icon"></i></a></li>
			<li><a href="#" class="nav-link nav-link-lg fullscreen-btn"><i data-feather="maximize"></i></a></li>
			
			<?php
            if( Auth::user()->role == 1 || Auth::user()->role == 12 ){ //super admin or admin
            ?>
			<li class="dropdown dropdown-list-toggle">
			    <a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle"><i data-feather="plus"></i></a>
                <div style="width: 50px;" class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
				
    				<div class="">
    					<a href="<?php echo e(URL::to('/admin/clients')); ?>" class="dropdown-item">
    						Client
    					</a>
    					<a href="<?php echo e(URL::to('/admin/tasks')); ?>" class="dropdown-item">
    						Task
    					</a>
    					<a href="#" class="dropdown-item">
    						Appointment
    					</a>
    					<a href="<?php echo e(URL::to('/admin/partners')); ?>" class="dropdown-item">
    						Partner
    					</a>
    					<a href="<?php echo e(URL::to('/admin/products')); ?>" class="dropdown-item">
    						Product
    					</a>
    					<a href="#" class="dropdown-item">
    						Workflow
    					</a>
    					<a href="<?php echo e(URL::to('/admin/users/active')); ?>" class="dropdown-item">
    						User
    					</a>
    				</div>
			    </div>
		    </li>
		     <?php }?>
		<li>
			<form class="form-inline mr-auto" onsubmit="return false;">
				<div class="search-element">
					<select class="form-control js-data-example-ajaxccsearch" type="search" placeholder="Search" aria-label="Search" data-width="200"></select>
					<button class="btn" type="button"><i class="fas fa-search"></i></button>
				</div>
			</form>
		</li>
		</ul>
	</div>
	<ul class="navbar-nav navbar-right">
	<li class="dropdown dropdown-list-toggle">
	<a href="javascript:;" data-toggle="dropdown" title="Add Office Check-In" class="nav-link nav-link-lg opencheckin"><i data-feather="log-in"></i></a>
	</li>
		<!--  -->
	<li class="dropdown dropdown-list-toggle">
		<?php if(Auth::user()): ?>
			<a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg" data-toggle="tooltip" data-placement="bottom" title="Click To See Notifications"><i data-feather="bell" class="bell"></i><span class="countbell" id="countbell_notification"><?php  echo \App\Models\Notification::where('receiver_id', Auth::user()->id)->where('receiver_status', 0)->count(); ?></span></a>
        <?php endif; ?>
			<!--<div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
				<div class="dropdown-header">Notifications
					<div class="float-right">
            
					</div>
				</div>
				<div class="dropdown-list-content dropdown-list-icons showallnotifications">
				     <?php
					/*if(Auth::user()){
						$notificalists = \App\Models\Notification::where('receiver_id', Auth::user()->id)->where('receiver_status', 0)->orderby('created_at','DESC')->paginate(5);
				    foreach($notificalists as $listnoti){*/
				    ?>
					<a href="?t=" class="dropdown-item dropdown-item-unread">
						<span class="dropdown-item-icon bg-primary text-white">
							<i class="fas fa-code"></i>
						</span>
						<span class="dropdown-item-desc"> <span class="time"></span></span>
					</a>
					<?php //} }?>
					
				</div>
				<div class="dropdown-footer text-center">
					<a href="">View All <i class="fas fa-chevron-right"></i></a>
				</div>
			</div>-->
		</li>
		<li class="dropdown">
			<a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
				<?php if(@Auth::user()->profile_img == ''): ?>
				<img alt="user image" src="<?php echo e(asset('img/user.png')); ?>" class="user-img-radious-style">
				<?php else: ?>
				<img  alt="<?php echo e(str_limit(Auth::user()->first_name.' '.Auth::user()->last_name, 150, '...')); ?>" src="<?php echo e(asset('img/profile_imgs')); ?>/<?php echo e(@Auth::user()->profile_img); ?>" class="user-img-radious-style"/>
				<?php endif; ?>	
				<span class="d-sm-none d-lg-inline-block"></span>
			</a>
            <div class="dropdown-menu dropdown-menu-right pullDown">
				<div class="dropdown-title"><?php echo e(str_limit(Auth::user()->first_name.' '.Auth::user()->last_name, 150, '...')); ?></div>
				<a href="<?php echo e(route('admin.my_profile')); ?>" class="dropdown-item has-icon">
					<i class="far fa-user"></i> Profile
				</a>
				<?php if(@Auth::user()->role == 1): ?>
			    <a href="<?php echo e(route('admin.feature.producttype.index')); ?>" class="dropdown-item has-icon">
					<i class="fas fa-cogs"></i> Admin Console
				</a>
				<?php endif; ?>
				<div class="dropdown-divider"></div>
				<a href="<?php echo e(route('admin.logout')); ?>" class="dropdown-item has-icon text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> <i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
		</li>
	</ul>
</nav>
<form id="logout-form" action="<?php echo e(route('admin.logout')); ?>" method="POST" style="display: none;">
	<?php echo csrf_field(); ?>
	<input type="hidden" name="id" value="<?php echo e(Auth::user()->id); ?>">
</form>
<?php /**PATH C:\xampp\htdocs\bansalcrm\resources\views////Elements/Admin/header.blade.php ENDPATH**/ ?>