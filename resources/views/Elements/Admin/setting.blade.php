<?php
		$roles = \App\Models\UserRole::find(Auth::user()->role);
		$newarray = json_decode($roles->module_access);
		$module_access = (array) $newarray;
?>
<div class="custom_nav_setting">
    <ul>
        <?php
			if(Route::currentRouteName() == 'adminconsole.producttype.index' || Route::currentRouteName() == 'adminconsole.producttype.create' || Route::currentRouteName() == 'adminconsole.producttype.edit' || Route::currentRouteName() == 'adminconsole.visatype.index' || Route::currentRouteName() == 'adminconsole.visatype.create' || Route::currentRouteName() == 'adminconsole.visatype.edit' || Route::currentRouteName() == 'adminconsole.mastercategory.index' || Route::currentRouteName() == 'adminconsole.mastercategory.create' || Route::currentRouteName() == 'adminconsole.mastercategory.edit' || Route::currentRouteName() == 'adminconsole.leadservice.index' || Route::currentRouteName() == 'adminconsole.leadservice.create' || Route::currentRouteName() == 'adminconsole.leadservice.edit' || Route::currentRouteName() == 'adminconsole.source.index' || Route::currentRouteName() == 'adminconsole.source.create' || Route::currentRouteName() == 'adminconsole.source.edit' || Route::currentRouteName() == 'adminconsole.workflow.index' || Route::currentRouteName() == 'adminconsole.workflow.create' || Route::currentRouteName() == 'adminconsole.workflow.edit'){
				$addfeatureclasstype = 'active'; 
		}
		?>
		<li class="{{(Route::currentRouteName() == 'adminconsole.profiles.index' || Route::currentRouteName() == 'adminconsole.profiles.create' || Route::currentRouteName() == 'adminconsole.profiles.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.profiles.index')}}">Profiles</a></li> 	
		<li class="{{(Route::currentRouteName() == 'adminconsole.producttype.index' || Route::currentRouteName() == 'adminconsole.producttype.create' || Route::currentRouteName() == 'adminconsole.producttype.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.producttype.index')}}">Product Type</a></li> 
		<li class="{{(Route::currentRouteName() == 'adminconsole.partnertype.index' || Route::currentRouteName() == 'adminconsole.partnertype.create' || Route::currentRouteName() == 'adminconsole.partnertype.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.partnertype.index')}}">Partner Type</a></li> 
		<li class="{{(Route::currentRouteName() == 'adminconsole.leadservice.index' || Route::currentRouteName() == 'adminconsole.leadservice.create' || Route::currentRouteName() == 'adminconsole.leadservice.edit' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.leadservice.index')}}">Lead Service</a></li>
		<li class="{{(Route::currentRouteName() == 'adminconsole.mastercategory.index' || Route::currentRouteName() == 'adminconsole.mastercategory.create' || Route::currentRouteName() == 'adminconsole.mastercategory.edit' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.mastercategory.index')}}">Master Category</a></li>
		<li class="{{(Route::currentRouteName() == 'adminconsole.visatype.index' || Route::currentRouteName() == 'adminconsole.visatype.create' || Route::currentRouteName() == 'adminconsole.visatype.edit' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.visatype.index')}}">Visa Type</a></li>
		<!-- Subject Area menu removed - subject_areas table has been dropped -->
		<!-- Subject routes removed - NOTE: Subject routes have been removed from adminconsole.php -->
		<!-- Tax routes removed - NOTE: Tax routes have been removed from adminconsole.php -->
		<li class="{{(Route::currentRouteName() == 'adminconsole.source.index' || Route::currentRouteName() == 'adminconsole.source.create' || Route::currentRouteName() == 'adminconsole.source.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.source.index')}}">Source</a></li>
		<!-- Tags menu removed - tags work differently and don't need backend -->
		<li class="{{(Route::currentRouteName() == 'adminconsole.checklist.index' || Route::currentRouteName() == 'adminconsole.checklist.create' || Route::currentRouteName() == 'adminconsole.checklist.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.checklist.index')}}">Checklist</a></li>
		<li class="{{(Route::currentRouteName() == 'adminconsole.workflow.index' || Route::currentRouteName() == 'adminconsole.workflow.create' || Route::currentRouteName() == 'adminconsole.workflow.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.workflow.index')}}">Workflow</a></li>
		<li class="{{(Route::currentRouteName() == 'adminconsole.emails.index' || Route::currentRouteName() == 'adminconsole.emails.create' || Route::currentRouteName() == 'adminconsole.emails.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.emails.index')}}">Email</a></li>
		<li class="{{(Route::currentRouteName() == 'adminconsole.crmemailtemplate.index' || Route::currentRouteName() == 'adminconsole.crmemailtemplate.create' || Route::currentRouteName() == 'adminconsole.crmemailtemplate.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.crmemailtemplate.index')}}">Crm Email Template</a></li> 
		<li class="{{(Route::currentRouteName() == 'adminconsole.emaillabels.index' || Route::currentRouteName() == 'adminconsole.emaillabels.create' || Route::currentRouteName() == 'adminconsole.emaillabels.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.emaillabels.index')}}">Email Labels</a></li>
		
		<li class="{{(Route::currentRouteName() == 'adminconsole.recentclients.index') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.recentclients.index')}}">Recently Modified Clients</a></li>
		
		<?php
			if(Route::currentRouteName() == 'adminconsole.branch.index' || Route::currentRouteName() == 'adminconsole.branch.create' || Route::currentRouteName() == 'adminconsole.branch.edit' || Route::currentRouteName() == 'adminconsole.branch.userview' || Route::currentRouteName() == 'adminconsole.branch.clientview' || Route::currentRouteName() == 'adminconsole.users.active' || Route::currentRouteName() == 'adminconsole.users.inactive' || Route::currentRouteName() == 'adminconsole.userrole.index' || Route::currentRouteName() == 'adminconsole.userrole.create' || Route::currentRouteName() == 'adminconsole.userrole.edit' || Route::currentRouteName() == 'adminconsole.teams.index' || Route::currentRouteName() == 'adminconsole.teams.edit' || Route::currentRouteName() == 'adminconsole.upload_checklists.index'){ 
				$teamclasstype = 'active';
			}  
		?> 
			<?php
			if(array_key_exists('1',  $module_access)) {
			?>
			<li class="{{(Route::currentRouteName() == 'adminconsole.branch.index' || Route::currentRouteName() == 'adminconsole.branch.create' || Route::currentRouteName() == 'adminconsole.branch.edit' || Route::currentRouteName() == 'adminconsole.branch.userview' || Route::currentRouteName() == 'adminconsole.branch.clientview') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.branch.index')}}">Offices</a></li> 
			<?php } ?>
			<?php
			if(array_key_exists('4',  $module_access)) {
			?>
			<li class="{{(Route::currentRouteName() == 'adminconsole.users.active' || Route::currentRouteName() == 'adminconsole.users.inactive') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.users.active')}}">Users</a></li>
			<li class="{{(Route::currentRouteName() == 'adminconsole.teams.index' || Route::currentRouteName() == 'adminconsole.teams.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.teams.index')}}">Teams</a></li>
			<?php } ?>
			<?php
			if(array_key_exists('6',  $module_access)) {
			?>
			<li class="{{(Route::currentRouteName() == 'adminconsole.userrole.index' || Route::currentRouteName() == 'adminconsole.userrole.create' || Route::currentRouteName() == 'adminconsole.userrole.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.userrole.index')}}">Roles</a></li>
			<?php } ?>
			<li class="{{(Route::currentRouteName() == 'adminconsole.upload_checklists.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.upload_checklists.index')}}">Upload Checklists</a></li>
            
            <!--<li class="{{--(Route::currentRouteName() == 'admin.feature.appointmentdisabledate.index' ) ? 'active' : ''--}}"><a class="nav-link" href="{{--route('admin.feature.appointmentdisabledate.index')--}}">Block Slot</a></li>-->
      
           <li class="{{(Route::currentRouteName() == 'adminconsole.documentchecklist.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.documentchecklist.index')}}">Document Checklist</a></li>

           <li class="{{(Route::currentRouteName() == 'adminconsole.documentcategory.index' || Route::currentRouteName() == 'adminconsole.documentcategory.create' || Route::currentRouteName() == 'adminconsole.documentcategory.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.documentcategory.index')}}">Document Category</a></li>

    </ul>
</div>
	