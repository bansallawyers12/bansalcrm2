<div class="main-sidebar sidebar-style-2">
	<aside id="sidebar-wrapper">
		<div class="sidebar-brand">
			<a href="#">
				<img alt="Bansal CRM" src="{!! asset('public/img/logo.png') !!}" class="header-logo" />
				<!--<span class="logo-name"></span>-->
			</a>
		</div>
		<ul class="sidebar-menu">
	
			<li class="menu-header">Main</li>
			<?php
			if(Route::currentRouteName() == 'agent.dashboard'){
				$dashclasstype = 'active';
			}
			?> 
			<li class="dropdown {{@$dashclasstype}}">
				<a href="{{route('agent.dashboard')}}" class="nav-link"><i data-feather="monitor"></i><span>Dashboard</span></a>
			</li>
		    	<li class="dropdown {{@$clientclasstype}}">
				<a href="{{route('agent.clients.index')}}" class="nav-link"><i data-feather="user"></i><span>Clients Manager</span></a>
			</li>
		
			<li class="dropdown">
				<a href="{{route('agent.logout')}}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i data-feather="log-out"></i><span>Logout</span></a>
				{{ Form::open(array('url' => 'agent/logout', 'name'=>'admin_login', 'id' => 'logout-form')) }}
				<input type="hidden" name="id" value="{{Auth::user()->id}}">
				{{ Form::close() }}
			</li>
		</ul>
	</aside>
</div>