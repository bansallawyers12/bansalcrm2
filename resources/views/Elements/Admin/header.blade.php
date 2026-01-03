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
	<div class="form-inline me-auto">
		<ul class="navbar-nav me-3">
			<li><a href="#" data-bs-toggle="sidebar" class="nav-link nav-link-lg collapse-btn"> <i class="fas fa-bars"></i></a></li>
			<li><a href="#" class="nav-link nav-link-lg fullscreen-btn"><i class="fas fa-expand"></i></a></li>
			
			<?php
            if( Auth::user()->role == 1 || Auth::user()->role == 12 ){ //super admin or admin
            ?>
			<li class="dropdown dropdown-list-toggle">
			    <a href="#" data-bs-toggle="dropdown" class="nav-link nav-link-lg message-toggle"><i class="fas fa-plus"></i></a>
                <div style="width: 50px;" class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
				
    				<div class="">
    					<a href="{{URL::to('/clients')}}" class="dropdown-item">
    						Client
    					</a>
    					<!-- Task system removed - December 2025 -->
    					<!-- <a href="{{URL::to('/tasks')}}" class="dropdown-item">
    						Task
    					</a> -->
    					<a href="#" class="dropdown-item">
    						Appointment
    					</a>
    					<a href="{{URL::to('/partners')}}" class="dropdown-item">
    						Partner
    					</a>
    					<a href="{{URL::to('/products')}}" class="dropdown-item">
    						Product
    					</a>
    					<a href="#" class="dropdown-item">
    						Workflow
    					</a>
    					<a href="{{URL::to('/users/active')}}" class="dropdown-item">
    						User
    					</a>
    				</div>
			    </div>
		    </li>
		     <?php }?>
		<li>
			<form class="form-inline me-auto" onsubmit="return false;">
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
	<a href="javascript:;" data-bs-toggle="dropdown" title="Add Office Check-In" class="nav-link nav-link-lg opencheckin"><i class="fas fa-sign-in-alt"></i></a>
	</li>
		<!-- {{--	<li class="dropdown dropdown-list-toggle">
			<a href="#" data-bs-toggle="dropdown" class="nav-link nav-link-lg message-toggle"><i class="fas fa-envelope"></i><span class="badge headerBadge1">6</span></a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
				<div class="dropdown-header">Messages
					<div class="float-end">
						<a href="#">Mark All As Read</a>
					</div>
				</div>
				<div class="dropdown-list-content dropdown-list-message">
				   
					<a href="#" class="dropdown-item">
						<span class="dropdown-item-avatar text-white">
							<img alt="image" src="{!! asset('img/users/user-2.png') !!}" class="rounded-circle">
						</span>
						<span class="dropdown-item-desc">
							<span class="message-user"></span>
							<span class="time messege-text">Please check your mail !!</span>
							<span class="time">2 Min Ago</span>
						</span>
					</a>
				
					<a href="#" class="dropdown-item">
						<span class="dropdown-item-avatar text-white">
							<img alt="image" src="{!! asset('img/users/user-2.png') !!}" class="rounded-circle">
						</span>
						<span class="dropdown-item-desc">
							<span class="message-user">Sarah Smith</span>
							<span class="time messege-text">Request for leave application</span>
							<span class="time">5 Min Ago</span>
						</span>
					</a>
					<a href="#" class="dropdown-item">
						<span class="dropdown-item-avatar text-white">
							<img alt="image" src="{!! asset('img/users/user-5.png') !!}" class="rounded-circle">
						</span>
						<span class="dropdown-item-desc">
							<span class="message-user">Jacob Ryan</span>
							<span class="time messege-text">Your payment invoice is generated.</span>
							<span class="time">12 Min Ago</span>
						</span>
					</a>
				</div>
				<div class="dropdown-footer text-center">
					<a href="#">View All <i class="fas fa-chevron-right"></i></a>
				</div>
			</div>
		</li>--}} -->
	<li class="dropdown dropdown-list-toggle">
		@if(Auth::user())
			<a href="#" data-bs-toggle="dropdown" class="nav-link notification-toggle nav-link-lg" data-bs-toggle="tooltip" data-placement="bottom" title="Click To See Notifications"><i class="fas fa-bell bell"></i><span class="countbell" id="countbell_notification"><?php  echo \App\Models\Notification::where('receiver_id', Auth::user()->id)->where('receiver_status', 0)->count(); ?></span></a>
        @endif
			<!--<div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
				<div class="dropdown-header">Notifications
					<div class="float-end">
            
					</div>
				</div>
				<div class="dropdown-list-content dropdown-list-icons showallnotifications">
				     <?php
					/*if(Auth::user()){
						$notificalists = \App\Models\Notification::where('receiver_id', Auth::user()->id)->where('receiver_status', 0)->orderby('created_at','DESC')->paginate(5);
				    foreach($notificalists as $listnoti){*/
				    ?>
					<a href="{{--$listnoti->url--}}?t={{--$listnoti->id--}}" class="dropdown-item dropdown-item-unread">
						<span class="dropdown-item-icon bg-primary text-white">
							<i class="fas fa-code"></i>
						</span>
						<span class="dropdown-item-desc">{{--$listnoti->message--}} <span class="time">{{--date('d/m/Y h:i A',strtotime($listnoti->created_at))--}}</span></span>
					</a>
					<?php //} }?>
					
				</div>
				<div class="dropdown-footer text-center">
					<a href="{{--URL::to('/all-notifications')--}}">View All <i class="fas fa-chevron-right"></i></a>
				</div>
			</div>-->
		</li>
		<li class="dropdown">
			<a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
				@if(@Auth::user()->profile_img == '')
				<img alt="user image" src="{{ asset('img/user.png') }}" class="user-img-radious-style">
				@else
				<img  alt="{{str_limit(Auth::user()->first_name.' '.Auth::user()->last_name, 150, '...')}}" src="{{asset('img/profile_imgs')}}/{{@Auth::user()->profile_img}}" class="user-img-radious-style"/>
				@endif	
				<span class="d-sm-none d-lg-inline-block"></span>
			</a>
            <div class="dropdown-menu dropdown-menu-right pullDown">
				<div class="dropdown-title">{{str_limit(Auth::user()->first_name.' '.Auth::user()->last_name, 150, '...')}}</div>
				<a href="{{route('my_profile')}}" class="dropdown-item has-icon">
					<i class="far fa-user"></i> Profile
				</a>
				@if(@Auth::user()->role == 1)
			    <a href="{{route('adminconsole.producttype.index')}}" class="dropdown-item has-icon">
					<i class="fas fa-cogs"></i> Admin Console
				</a>
				@endif
				<div class="dropdown-divider"></div>
				<a href="{{route('admin.logout')}}" class="dropdown-item has-icon text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> <i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
		</li>
	</ul>
</nav>
<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
	@csrf
	<input type="hidden" name="id" value="{{Auth::user()->id}}">
</form>
