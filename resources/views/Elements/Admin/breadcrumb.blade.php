@if(Auth::user()->role == 1)
	<li class="breadcrumb-menu d-md-down-none">
		<div class="btn-group" role="group" aria-label="Button group">
			<a class="btn" href="{{URL::to('/admin/website_setting')}}">
			<i class="icon-settings"></i>  Website Settings</a>
		</div>
	</li>
@endif	