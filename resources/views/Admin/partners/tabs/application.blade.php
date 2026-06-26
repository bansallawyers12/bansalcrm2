									@php
										$appprogresscount = 0;
										$appcompletecount = 0;
										$appdisccount = 0;
										$appenrolcount = 0;
									@endphp
									<div class="row">
										<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
											<div class="card">
												<div class="card-statistic-4">
													<div class="align-items-center justify-content-between">
														<div class="card-content">
															<h5 class="font-13">IN PROGRESS</h5>
															<h2 class="mb-3 font-18" id="app-status-count-0">{{ $appprogresscount }}</h2>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
											<div class="card">
												<div class="card-statistic-4">
													<div class="align-items-center justify-content-between">
														<div class="card-content">
															<h5 class="font-13">COMPLETED</h5>
															<h2 class="mb-3 font-18" id="app-status-count-1">{{ $appcompletecount }}</h2>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
											<div class="card">
												<div class="card-statistic-4">
													<div class="align-items-center justify-content-between">
														<div class="card-content">
															<h5 class="font-13">DISCONTINUED</h5>
															<h2 class="mb-3 font-18" id="app-status-count-2">{{ $appdisccount }}</h2>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
											<div class="card">
												<div class="card-statistic-4">
													<div class="align-items-center justify-content-between">
														<div class="card-content">
															<h5 class="font-13">ENROLLED</h5>
															<h2 class="mb-3 font-18" id="app-status-count-3">{{ $appenrolcount }}</h2>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>	
									 
									<div class="row">
										<div class="col-12 col-sm-12 col-lg-12">
											<div class="card">
												<div class="card-body">
													<div class="summary">
														<div class="summary-chart active" data-tab-group="summary-tab" id="summary-chart">
															<canvas id="myChart" height="180"></canvas>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="table-responsive if_applicationdetail"> 
										<table class="table text_wrap table-2">
											<thead>
												<tr>
													<th>Name</th>
													<th>Assignee</th>
													<th>Product Name</th>
													<th>Workflow</th>
													<th>Current Stage</th>
													<th>Enrolment Type</th>
													<th>Status</th>
													<th>Added On</th>
													<th>Last Updated</th>
													
												</tr> 
											</thead>
											<tbody class="applicationtdata">
												{{-- Rows loaded via AJAX (server-side DataTables) --}}
											</tbody>
										</table> 
									</div>
									<div class="ifapplicationdetailnot" style="display:none;">
										<h4>Please wait ...</h4>
									</div>
