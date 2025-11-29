<ul class="nav nav-tabs" role="tablist">
	<li role="presentation" class="{{(Route::currentRouteName() == 'dashboard.index' || Route::currentRouteName() == 'dashboard.view_order_summary' || Route::currentRouteName() == 'dashboard.view_test_series_order') ? 'active' : ''}}">
		<a href="{{URL::to('/dashboard')}}">
			<i class="fa fa-list-alt" aria-hidden="true"></i> 
			<span>Order Summary</span>
		</a>
	</li>
	<li role="presentation">
		<a href="{{URL::to('/cart')}}">
			<i class="fa fa-cart-plus" aria-hidden="true"></i>
			<span>Your Cart</span>
		</a>
	</li>
	<li role="presentation" class="{{(Route::currentRouteName() == 'test.index' || Route::currentRouteName() == 'test.view_test') ? 'active' : ''}}">
		<a href="{{URL::to('/test')}}">
			<i class="fa fa-book" aria-hidden="true"></i> 
			<span>Test Series</span>
		</a>
	</li>
	<li role="presentation" class="{{(Route::currentRouteName() == 'change_password') ? 'active' : ''}}">
		<a href="{{URL::to('/change_password')}}">
			<i class="fa fa-key" aria-hidden="true"></i>
			<span>Change Password</span>
		</a>
	</li>
	<li role="presentation" class="{{(Route::currentRouteName() == 'dashboard.edit_profile') ? 'active' : ''}}">
		<a href="{{URL::to('/edit_profile')}}">
			<i class="fa fa-user" aria-hidden="true"></i>
			<span>Edit My Profile</span>
		</a>
	</li>
	<li role="presentation" class="{{(Route::currentRouteName() == 'dashboard.modified_test_series') ? 'active' : ''}}">
		<a href="{{URL::to('/modified_test_series')}}">
			<i class="fa fa-book" aria-hidden="true"></i>
			<span>Modified Test Series Plan</span>
		</a>
	</li>	
</ul>