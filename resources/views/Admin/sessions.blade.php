@extends('layouts.admin')
@section('title', 'Sessions')
@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark">Sessions</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Sessions</li>
					</ol>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.container-fluid -->
	</div>
<!-- Main content --> 
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<!-- Flash Message Start -->
					<div class="server-error">
						@include('../Elements/flash-message')
					</div>
					<!-- Flash Message End -->
				</div>
			</div>	
			<div class="row">
				<div class="col-sm-12">
					<div class="card card-primary sessions">  
						<div class="card-header"> 
							<h3 class="card-title">Active Sessions</h3>
							<p>View and manage all of your active sessions.</p> 
						</div>   
						<div class="card-body session_body">
							<div id="current_sesion">		
								<div class="Field_session cus_session_1" id="activesession_entry6" onclick="show_selected_session(6,'device_personalcomputer');">							 
									<div class="info_tab">									
										<div class="device_div"> 
											<span class="device_pic device_personalcomputer"></span>
											<span class="device_details">
												<span class="device_name">Personal Computer</span>
												<span class="device_time">22 days ago</span>
											</span>
										</div> 
										<div class="activesession_entry_info">
											<div class="asession_os os_windows" data-tippy="" data-original-title="Windows 6.1"></div>
											<div class="asession_browser browser_googlechrome" data-tippy="" data-original-title="Google Chrome 80"></div>
											<div class="asession_ip hide">223.185.40.10</div>
											<div class="asession_location">Chandigarh, Chandigarh, India</div>
											<div class="asession_action current">Current Session</div>
										</div>
									</div>  
							    
									<div class="aw_info modal fade" id="activesession_info6">	
										<div class="modal-dialog modal-md">
											<div class="modal-content">
												<div class="modal-body">
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
													<div class="device_div on_popup">			
														<span id="device_pic" class="device_personalcomputer"></span>
														<span class="device_details">
															<span class="device_name">Personal Computer</span>
															<span class="device_time">23 days ago</span>
														</span>
													</div>
													<div id="sessions_current_info" class="list_show">
														<div class="info_div">
															<div class="info_lable">Started Time</div>
															<div class="info_value" id="pop_up_time">04/06/2020</div>
														</div>
														<div class="info_div">
															<div class="info_lable">Operating System</div>
															<div class="info_value" id="pop_up_os"><div class="asession_os_popup minios_windows"></div><span>Windows 6.1</span></div>
														</div>
														<div class="info_div">
															<div class="info_lable">Browser</div>
															<div class="info_value" id="pop_up_browser"><span class="asession_browser_popup minibrowser_googlechrome"></span><span>Google Chrome 80</span></div>
														</div>
														<div class="info_div">
															<div class="info_lable">Location</div>
															<div class="info_value location_unavail" id="pop_up_location">Chandigarh, Chandigarh, India</div>
															<div class="info_ip"></div>
														</div>		
													</div>		
												</div>
											</div> 
										</div>
									</div> 
								</div>			
							</div>
							<div id="other_sesion">		
								<div class="Field_session cus_session_2" id="activesession_entry1" onclick="show_selected_session(1,'device_personalcomputer');">
									<div class="info_tab">	
										<div class="select_holder hide" id="select_session_1">
											<input data-validate="zform_field" id="96d7375826decbfca5622e2ff86801c168225ae212461dd5befbc66bba82b9b86a4706ca12d70a60319a72cdef448609a7bdc225198f229b78d844f21b35df36" onclick="display_removeselected_session();" name="signoutfromweb" class="checkbox_check" type="checkbox">
											<span class="checkbox">
												<span class="checkbox_tick"></span>
											</span>
										</div>
										<div class="device_div">
											<span class="device_pic device_personalcomputer"></span>
											<span class="device_details">
												<span class="device_name">Personal Computer</span>
												<span class="device_time">5 days ago</span>
											</span>
										</div>
										<div class="activesession_entry_info">
											<div class="asession_os os_windows" data-tippy="" data-original-title="Windows 10.0"></div>
											<div class="asession_browser browser_googlechrome" data-tippy="" data-original-title="Google Chrome 81"></div>
											<div class="asession_ip hide">117.199.100.59</div>
											<div class="asession_location">Chima, Punjab, India</div>
											<div class="asession_action session_logout">Terminate</div>
										</div>
									</div>									
									
									<div class="aw_info modal fade" id="activesession_info1">	
										<div class="modal-dialog modal-md">
											<div class="modal-content">
												<div class="modal-body">
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
													<div class="device_div on_popup">	
														<span id="device_pic" class="device_personalcomputer"></span>
														<span class="device_details">
															<span class="device_name">Personal Computer</span>
															<span class="device_time">5 days ago</span>
														</span>
													</div>
													<div id="sessions_current_info" class="list_show">
														<div class="info_div">
															<div class="info_lable">Started Time</div>
															<div class="info_value" id="pop_up_time">04/24/2020</div>
														</div>
														<div class="info_div">
															<div class="info_lable">Operating System</div>
															<div class="info_value" id="pop_up_os"><div class="asession_os_popup minios_windows"></div><span>Windows 10.0</span></div>
														</div>
														<div class="info_div">
															<div class="info_lable">Browser</div>
															<div class="info_value" id="pop_up_browser"><span class="asession_browser_popup minibrowser_googlechrome"></span><span>Google Chrome 81</span></div>
														</div>
														<div class="info_div">
															<div class="info_lable">Location</div>
															<div class="info_value location_unavail" id="pop_up_location">Chima, Punjab, India</div>
															<div class="info_ip"></div>
														</div>
														<button class="primary_btn_check negative_btn_red inline" tabindex="1" id="current_session_logout" onclick=""><span>Terminate</span></button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								
								<div class="Field_session cus_session_3" id="activesession_entry2" onclick="show_selected_session(2,'device_personalcomputer');">
									<div class="info_tab">	
										<div class="select_holder hide" id="select_session_2">
											<input data-validate="zform_field" id="5259eb9533436b78db12bcd0c6c70ce966d1359ed5458af3826a30439e21bc6e11dcef1b08654e5e653e301ebfce35dab707c62cb392bafa42317cbed068fe3a" onclick="display_removeselected_session();" name="signoutfromweb" class="checkbox_check" type="checkbox">
											<span class="checkbox">
												<span class="checkbox_tick"></span>
											</span>
										</div>
										<div class="device_div">
											<span class="device_pic device_personalcomputer"></span>
											<span class="device_details">
												<span class="device_name">Personal Computer</span>
												<span class="device_time">6 days ago</span>
											</span>
										</div>
										<div class="activesession_entry_info">
											<div class="asession_os os_windows" data-tippy="" data-original-title="Windows 6.1"></div>
											<div class="asession_browser browser_googlechrome" data-tippy="" data-original-title="Google Chrome 81"></div>
											<div class="asession_ip hide">117.215.230.230</div>
											<div class="asession_location">Khanna, Punjab, India</div>
											<div class="asession_action session_logout">Terminate</div>
										</div>
									</div>
									 
									<div class="aw_info modal fade" id="activesession_info2">	
										<div class="modal-dialog modal-md">
											<div class="modal-content">
												<div class="modal-body">
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
													<div class="device_div on_popup">			
														<span id="device_pic" class="device_personalcomputer"></span>
														<span class="device_details">
															<span class="device_name">Personal Computer</span>
															<span class="device_time">23 days ago</span>
														</span>
													</div>
													<div id="sessions_current_info" class="list_show">
														<div class="info_div">
															<div class="info_lable">Started Time</div>
															<div class="info_value" id="pop_up_time">04/23/2020</div>
														</div>
														<div class="info_div">
															<div class="info_lable">Operating System</div>
															<div class="info_value" id="pop_up_os"><div class="asession_os_popup minios_windows"></div><span>Windows 6.1</span></div>
														</div>
														<div class="info_div">
															<div class="info_lable">Browser</div>
															<div class="info_value" id="pop_up_browser"><span class="asession_browser_popup minibrowser_googlechrome"></span><span>Google Chrome 81</span></div>
														</div>
														<div class="info_div">
															<div class="info_lable">Location</div>
															<div class="info_value location_unavail" id="pop_up_location">Khanna, Punjab, India</div>
															<div class="info_ip"></div>
														</div>
														<button class="primary_btn_check negative_btn_red inline" tabindex="1" id="current_session_logout" onclick=""><span>Terminate</span></button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="view_btn">
									<a href="javascript:;"><i class="fa fa-eye"></i> View More</a>
								</div>
								<div class="view_all">  
									<div class="Field_session activesession_entry_hidden" id="activesession_entry3" onclick="show_selected_session(3,'device_personalcomputer');">
										<div class="info_tab">	
											<div class="select_holder hide" id="select_session_3">
												<input data-validate="zform_field" id="8afa398bdfa8f6bd76ae9429ea53d50df477f2c712f482c3c5f2cfc57bf1a59393958d0aa113a0c3404938de30de2c30c09b1e13b51246b23ebfc242c70f65ee" onclick="display_removeselected_session();" name="signoutfromweb" class="checkbox_check" type="checkbox">
												<span class="checkbox">
													<span class="checkbox_tick"></span>
												</span>
											</div>
											<div class="device_div">
												<span class="device_pic device_personalcomputer"></span>
												<span class="device_details">
													<span class="device_name">Personal Computer</span>
													<span class="device_time">10 days ago</span>
												</span> 
											</div>
											<div class="activesession_entry_info">
												<div class="asession_os os_windows" data-tippy="" data-original-title="Windows 10.0"></div>
												<div class="asession_browser browser_googlechrome" data-tippy="" data-original-title="Google Chrome 81"></div>
												<div class="asession_ip hide">112.196.133.63</div>
												<div class="asession_location">New delhi, Delhi, India</div>
												<div class="asession_action session_logout">Terminate</div>
											</div>
										</div>
										
										<div class="aw_info" id="activesession_info3">			
											<div class="info_div">
												<div class="info_lable">Started Time</div>
												<div class="info_value" id="pop_up_time">04/18/2020</div>
											</div>
											<div class="info_div">
												<div class="info_lable">Operating System</div>
												<div class="info_value" id="pop_up_os"><div class="asession_os_popup minios_windows"></div><span>Windows 10.0</span></div>
											</div>
											<div class="info_div">
												<div class="info_lable">Browser</div>
												<div class="info_value" id="pop_up_browser"><span class="asession_browser_popup minibrowser_googlechrome"></span><span>Google Chrome 81</span></div>
											</div>
											<div class="info_div">
												<div class="info_lable">Location</div>
												<div class="info_value location_unavail" id="pop_up_location">New delhi, Delhi, India</div>											 
												<div class="info_ip"></div>
											</div>
											<button class="primary_btn_check negative_btn_red inline" tabindex="1" id="current_session_logout" onclick=""><span>Terminate</span></button>									
										</div>
									</div>
						
									<div class="Field_session activesession_entry_hidden" id="activesession_entry4" onclick="show_selected_session(4,'device_personalcomputer');">
										<div class="info_tab">	
											<div class="select_holder hide" id="select_session_4">
												<input data-validate="zform_field" id="34195399763583bd42872a2a23d6886754d8a0f1c48449ab9a1ed63a1401c494393cab67e87f6e671a193217dbe3632230986aaee6e3cbcf10a678235cd2bae2" onclick="display_removeselected_session();" name="signoutfromweb" class="checkbox_check" type="checkbox">
												<span class="checkbox">
													<span class="checkbox_tick"></span>
												</span>
											</div>
											<div class="device_div">
												<span class="device_pic device_personalcomputer"></span>
												<span class="device_details">
													<span class="device_name">Personal Computer</span>
													<span class="device_time">18 days ago</span>
												</span>
											</div>
											<div class="activesession_entry_info">
												<div class="asession_os os_windows" data-tippy="" data-original-title="Windows 10.0"></div>
												<div class="asession_browser browser_googlechrome" data-tippy="" data-original-title="Google Chrome 80"></div>
												<div class="asession_ip hide">112.196.133.109</div>
												<div class="asession_location">New delhi, Delhi, India</div>
												<div class="asession_action session_logout">Terminate</div>
											</div>
										</div>
									
										<div class="aw_info" id="activesession_info4">
											<div class="info_div">
												<div class="info_lable">Started Time</div>
												<div class="info_value" id="pop_up_time">04/10/2020</div>
											</div>
											<div class="info_div">
												<div class="info_lable">Operating System</div>
												<div class="info_value" id="pop_up_os"><div class="asession_os_popup minios_windows"></div><span>Windows 10.0</span></div>
											</div>
											<div class="info_div">
												<div class="info_lable">Browser</div>
												<div class="info_value" id="pop_up_browser"><span class="asession_browser_popup minibrowser_googlechrome"></span><span>Google Chrome 80</span></div>
											</div>
											<div class="info_div">
												<div class="info_lable">Location</div>
												<div class="info_value location_unavail" id="pop_up_location">New delhi, Delhi, India</div>											 
												<div class="info_ip"></div>
											</div>
											<button class="primary_btn_check negative_btn_red inline" tabindex="1" id="current_session_logout" onclick=""><span>Terminate</span></button>
										</div>
									</div>
						
									<div class="Field_session activesession_entry_hidden" id="activesession_entry5" onclick="show_selected_session(5,'device_personalcomputer');">
										<div class="info_tab">	
											<div class="select_holder hide" id="select_session_5">
												<input data-validate="zform_field" id="601dbc0e836d9214e3efe962bc6dc2b7ba3ba1cb3e470017d0028688119f410d854cb12763355fa11dc845ea83ee49165db81fa84e06d1bb30008accd27210d6" onclick="display_removeselected_session();" name="signoutfromweb" class="checkbox_check" type="checkbox">
												<span class="checkbox">
													<span class="checkbox_tick"></span>
												</span>
											</div>
											<div class="device_div">
												<span class="device_pic device_personalcomputer"></span>
												<span class="device_details">
													<span class="device_name">Personal Computer</span>
													<span class="device_time">22 days ago</span>
												</span>
											</div>
											<div class="activesession_entry_info">
												<div class="asession_os os_windows" data-tippy="" data-original-title="Windows 6.1"></div>
												<div class="asession_browser browser_googlechrome" data-tippy="" data-original-title="Google Chrome 80"></div>
												<div class="asession_ip hide">117.199.98.134</div>
												<div class="asession_location">Chima, Punjab, India</div>
												<div class="asession_action session_logout">Terminate</div>
											</div>
										</div>
										
										<div class="aw_info" id="activesession_info5">
											<div class="info_div">
												<div class="info_lable">Started Time</div>
												<div class="info_value" id="pop_up_time">04/07/2020</div>
											</div>
											<div class="info_div">
												<div class="info_lable">Operating System</div>
												<div class="info_value" id="pop_up_os"><div class="asession_os_popup minios_windows"></div><span>Windows 6.1</span></div>
											</div>
											<div class="info_div">
												<div class="info_lable">Browser</div>
												<div class="info_value" id="pop_up_browser"><span class="asession_browser_popup minibrowser_googlechrome"></span><span>Google Chrome 80</span></div>
											</div>
											<div class="info_div">
												<div class="info_lable">Location</div>
												<div class="info_value location_unavail" id="pop_up_location">Chima, Punjab, India</div>
												<div class="info_ip"></div>
											</div>
											<button class="primary_btn_check negative_btn_red inline" tabindex="1" id="current_session_logout" onclick="deleteTicket('601dbc0e836d9214e3efe962bc6dc2b7ba3ba1cb3e470017d0028688119f410d854cb12763355fa11dc845ea83ee49165db81fa84e06d1bb30008accd27210d6');"><span>Terminate</span></button>									
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection