<?php
		$roles = \App\Models\UserRole::find(Auth::user()->role);
		$newarray = json_decode($roles->module_access);
		$module_access = (array) $newarray;
?>
<div class="custom_nav_setting">
    <ul>
        <?php
			if(Route::currentRouteName() == 'adminconsole.producttype.index' || Route::currentRouteName() == 'adminconsole.producttype.create' || Route::currentRouteName() == 'adminconsole.producttype.edit' || Route::currentRouteName() == 'adminconsole.visatype.index' || Route::currentRouteName() == 'adminconsole.visatype.create' || Route::currentRouteName() == 'adminconsole.visatype.edit' || Route::currentRouteName() == 'adminconsole.mastercategory.index' || Route::currentRouteName() == 'adminconsole.mastercategory.create' || Route::currentRouteName() == 'adminconsole.mastercategory.edit' || Route::currentRouteName() == 'adminconsole.leadservice.index' || Route::currentRouteName() == 'adminconsole.leadservice.create' || Route::currentRouteName() == 'adminconsole.leadservice.edit' || Route::currentRouteName() == 'adminconsole.subjectarea.index' || Route::currentRouteName() == 'adminconsole.subjectarea.create' || Route::currentRouteName() == 'adminconsole.subjectarea.edit' || Route::currentRouteName() == 'adminconsole.subject.index' || Route::currentRouteName() == 'adminconsole.subject.create' || Route::currentRouteName() == 'adminconsole.subject.edit' || Route::currentRouteName() == 'adminconsole.tax.index' || Route::currentRouteName() == 'adminconsole.tax.create' || Route::currentRouteName() == 'adminconsole.tax.edit' || Route::currentRouteName() == 'adminconsole.source.index' || Route::currentRouteName() == 'adminconsole.source.create' || Route::currentRouteName() == 'adminconsole.source.edit' || Route::currentRouteName() == 'adminconsole.tags.index' || Route::currentRouteName() == 'adminconsole.tags.create' || Route::currentRouteName() == 'adminconsole.tags.edit' || Route::currentRouteName() == 'adminconsole.workflow.index' || Route::currentRouteName() == 'adminconsole.workflow.create' || Route::currentRouteName() == 'adminconsole.workflow.edit'){
				$addfeatureclasstype = 'active'; 
		}
		?>
		<li class="{{(Route::currentRouteName() == 'adminconsole.profiles.index' || Route::currentRouteName() == 'adminconsole.profiles.create' || Route::currentRouteName() == 'adminconsole.profiles.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.profiles.index')}}">Profiles</a></li> 	
		<li class="{{(Route::currentRouteName() == 'adminconsole.producttype.index' || Route::currentRouteName() == 'adminconsole.producttype.create' || Route::currentRouteName() == 'adminconsole.producttype.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.producttype.index')}}">Product Type</a></li> 
		<li class="{{(Route::currentRouteName() == 'adminconsole.partnertype.index' || Route::currentRouteName() == 'adminconsole.partnertype.create' || Route::currentRouteName() == 'adminconsole.partnertype.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.partnertype.index')}}">Partner Type</a></li> 
		<li class="{{(Route::currentRouteName() == 'adminconsole.leadservice.index' || Route::currentRouteName() == 'adminconsole.leadservice.create' || Route::currentRouteName() == 'adminconsole.leadservice.edit' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.leadservice.index')}}">Lead Service</a></li>
		<li class="{{(Route::currentRouteName() == 'adminconsole.mastercategory.index' || Route::currentRouteName() == 'adminconsole.mastercategory.create' || Route::currentRouteName() == 'adminconsole.mastercategory.edit' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.mastercategory.index')}}">Master Category</a></li>
		<li class="{{(Route::currentRouteName() == 'adminconsole.feetype.index' || Route::currentRouteName() == 'adminconsole.feetype.create' || Route::currentRouteName() == 'adminconsole.feetype.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.feetype.index')}}">Fee Type</a></li> 
    	<li class="{{(Route::currentRouteName() == 'adminconsole.visatype.index' || Route::currentRouteName() == 'adminconsole.visatype.create' || Route::currentRouteName() == 'adminconsole.visatype.edit' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.visatype.index')}}">Visa Type</a></li>
		<li class="{{(Route::currentRouteName() == 'adminconsole.subjectarea.index' || Route::currentRouteName() == 'adminconsole.subjectarea.create' || Route::currentRouteName() == 'adminconsole.subjectarea.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.subjectarea.index')}}">Subject Area</a></li>
		<li class="{{(Route::currentRouteName() == 'adminconsole.subject.index' || Route::currentRouteName() == 'adminconsole.subject.create' || Route::currentRouteName() == 'adminconsole.subject.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.subject.index')}}">Subjects</a></li>
		<li class="{{(Route::currentRouteName() == 'adminconsole.tax.index' || Route::currentRouteName() == 'adminconsole.tax.create' || Route::currentRouteName() == 'adminconsole.tax.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.tax.index')}}">Manage Tax</a></li>
		<li class="{{(Route::currentRouteName() == 'adminconsole.source.index' || Route::currentRouteName() == 'adminconsole.source.create' || Route::currentRouteName() == 'adminconsole.source.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.source.index')}}">Source</a></li>
		<li class="{{(Route::currentRouteName() == 'adminconsole.tags.index' || Route::currentRouteName() == 'adminconsole.tags.create' || Route::currentRouteName() == 'adminconsole.tags.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.tags.index')}}">Tags</a></li>
		<li class="{{(Route::currentRouteName() == 'adminconsole.checklist.index' || Route::currentRouteName() == 'adminconsole.checklist.create' || Route::currentRouteName() == 'adminconsole.checklist.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.checklist.index')}}">Checklist</a></li>
		<li class="{{(Route::currentRouteName() == 'adminconsole.workflow.index' || Route::currentRouteName() == 'adminconsole.workflow.create' || Route::currentRouteName() == 'adminconsole.workflow.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.workflow.index')}}">Workflow</a></li>
		<li class="{{(Route::currentRouteName() == 'adminconsole.emails.index' || Route::currentRouteName() == 'adminconsole.emails.create' || Route::currentRouteName() == 'adminconsole.emails.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.emails.index')}}">Email</a></li>
		<li class="{{(Route::currentRouteName() == 'adminconsole.crmemailtemplate.index' || Route::currentRouteName() == 'adminconsole.crmemailtemplate.create' || Route::currentRouteName() == 'adminconsole.crmemailtemplate.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.crmemailtemplate.index')}}">Crm Email Template</a></li> 
		
		<?php
			if(Route::currentRouteName() == 'admin.branch.index' || Route::currentRouteName() == 'admin.branch.create' || Route::currentRouteName() == 'admin.branch.edit' || Route::currentRouteName() == 'admin.branch.userview' || Route::currentRouteName() == 'admin.branch.clientview' || Route::currentRouteName() == 'admin.users.active' || Route::currentRouteName() == 'admin.users.inactive' || Route::currentRouteName() == 'admin.users.invited' || Route::currentRouteName() == 'admin.userrole.index' || Route::currentRouteName() == 'admin.userrole.create' || Route::currentRouteName() == 'admin.userrole.edit'){ 
				$teamclasstype = 'active';
			}  
		?> 
			<?php
			if(array_key_exists('1',  $module_access)) {
			?>
			<li class="{{(Route::currentRouteName() == 'admin.branch.index' || Route::currentRouteName() == 'admin.branch.create' || Route::currentRouteName() == 'admin.branch.edit' || Route::currentRouteName() == 'admin.branch.userview' || Route::currentRouteName() == 'admin.branch.clientview') ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.branch.index')}}">Offices</a></li> 
			<?php } ?>
			<?php
			if(array_key_exists('4',  $module_access)) {
			?>
			<li class="{{(Route::currentRouteName() == 'admin.users.active' || Route::currentRouteName() == 'admin.users.inactive' || Route::currentRouteName() == 'admin.users.invited') ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.users.active')}}">Users</a></li>
			<li class="{{(Route::currentRouteName() == 'admin.teams.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.teams.index')}}">Teams</a></li>
			<?php } ?>
			<?php
			if(array_key_exists('6',  $module_access)) {
			?>
			<li class="{{(Route::currentRouteName() == 'admin.userrole.index' || Route::currentRouteName() == 'admin.userrole.create' || Route::currentRouteName() == 'admin.userrole.edit') ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.userrole.index')}}">Roles</a></li>
			<?php } ?>
			<li class="{{(Route::currentRouteName() == 'admin.gensettings.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.gensettings.index')}}">Gen Settings</a></li>
			<li class="{{(Route::currentRouteName() == 'admin.upload_checklists.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('admin.upload_checklists.index')}}">Upload Checklists</a></li>
            
            <!--<li class="{{--(Route::currentRouteName() == 'admin.feature.appointmentdisabledate.index' ) ? 'active' : ''--}}"><a class="nav-link" href="{{--route('admin.feature.appointmentdisabledate.index')--}}">Block Slot</a></li>-->
      
           <li class="{{(Route::currentRouteName() == 'adminconsole.documentchecklist.index' ) ? 'active' : ''}}"><a class="nav-link" href="{{route('adminconsole.documentchecklist.index')}}">Document Checklist</a></li>

    </ul>
</div>
	