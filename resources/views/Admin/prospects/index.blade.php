@extends('layouts.admin')
@section('title', 'Clients')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="custom-error-msg">
			</div>
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>All Clients</h4>
							<div class="card-header-action">
								<a href="{{route('admin.clients.create')}}" class="btn btn-primary">Create Client</a>
							</div>
						</div>
						<div class="card-body">
							<ul class="nav nav-pills" id="client_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" id="prospects-tab"  href="{{URL::to('/admin/prospects')}}" >Prospects</a>
								</li>
								<li class="nav-item">
									<a class="nav-link " id="clients-tab"  href="{{URL::to('/admin/clients')}}" >Clients</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="archived-tab"  href="{{URL::to('/admin/archived')}}" >Archived</a>
								</li>
							</ul> 
							<div class="tab-content" id="clientContent">
								<div class="tab-pane fade show active" id="prospects" role="tabpanel" aria-labelledby="prospects-tab">
									<div class="table-responsive"> 
										<table class="table text_wrap">
											<thead>
												<tr>
													<th class="text-center" style="width:30px;">
														<div class="custom-checkbox custom-checkbox-table custom-control">
															<input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad" class="custom-control-input" id="checkbox-all">
															<label for="checkbox-all" class="custom-control-label">&nbsp;</label>
														</div>
													</th>	
													<th>Name</th>
													<th>Added From</th>
													<th>Tag(s)</th>
													<th>Rating</th>
													<th>Internal ID</th>
													<th>Client ID</th>
													<th>Followers</th>
													<th>Phone</th>
													<th>Passport Number</th>
													<th>Passport</th>
													<th>Current City</th>
													<th>Assignee</th>
													<th>Added On</th>
													<th>Last Updated</th>
													<th>Preferred Intake</th>
													<th></th>
												</tr> 
											</thead>
											
											<tbody class="tdata">	
												<tbody>
												<tr>
													<td style="text-align:center;" colspan="17">
														No Record found
													</td>
												</tr>
											</tbody>
										</table>
									</div>	
								</div>
								
							</div> 
						</div>
						<div class="card-footer">
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

@endsection