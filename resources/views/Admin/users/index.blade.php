@extends('layouts.adminconsole')
@section('title', 'Users')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="row">
				@if(isset($viewType) && ($viewType == 'active' || $viewType == 'inactive'))
				<div class="col-12"><div class="custom-error-msg"></div></div>
				@endif
				<div class="col-12">
					<div class="card">
						<div class="card-header">
							<h4>Users</h4>
							<div class="card-header-action">
								@if(isset($viewType) && $viewType == 'active')
								<a href="{{URL::to('users/create')}}" class="btn btn-primary">Add User</a>
								@endif
							</div>
						</div>
						<div class="card-body">
							<ul class="nav nav-pills" id="user_tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link {{ (isset($viewType) && $viewType == 'active') ? 'active' : '' }}" id="active-tab"  href="{{URL::to('/users/active')}}" >Active</a>
								</li>
								<li class="nav-item">
									<a class="nav-link {{ (isset($viewType) && $viewType == 'inactive') ? 'active' : '' }}" id="inactive-tab"  href="{{URL::to('/users/inactive')}}" >Inactive</a>
								</li>
								
								@if(isset($viewType) && $viewType == 'active')
								<form action="{{ route('users.active') }}" method="get">
									<div class="" style="display: inline-flex;float: right;margin-left:540px;">
										<input id="search-input" type="search" name="search_by"  class="form-control" value="{{ isset($_GET['search_by']) && $_GET['search_by'] != '' ? $_GET['search_by'] : '' }}" />
										<button id="search-button" type="submit" class="btn btn-primary">
										<i class="fas fa-search"></i>
										</button>
									</div>
								</form>
								@endif
							</ul>
							<div class="tab-content" id="checkinContent">
								<div class="tab-pane fade show active" id="{{ isset($viewType) ? $viewType : 'active' }}" role="tabpanel" aria-labelledby="{{ isset($viewType) ? $viewType : 'active' }}-tab">
									<div class="table-responsive common_table"> 
										<table class="table"> 
											<thead>
												<tr>
												  <th>Name</th>
												  <th>Position</th>
												  <th>Office</th> 
												  <th>Role</th>
												  @if(isset($viewType) && ($viewType == 'active' || $viewType == 'inactive'))
												  <th>Status</th>
												  @endif
												  @if(isset($viewType) && $viewType == 'active')
												  <th>Action</th>
												  @endif
												</tr> 
											</thead>
											@if(@$totalData !== 0)
											<tbody class="tdata">
											@foreach (@$lists as $list)
												<tr id="id_{{@$list->id}}"> 
													<td><a href="{{URL::to('/users/view')}}/{{$list->id}}">{{@$list->first_name}}</a><br>{{@$list->email}}</td> 
													<td>{{@$list->position}}</td>
													<td>@if($list->office)<a href="{{URL::to('/branch/view/')}}/{{$list->office->id}}">{{$list->office->office_name}}</a>@else{{ config('constants.empty') }}@endif</td>
													
													
													<td>{{ @$list->usertype->name == "" ? config('constants.empty') : str_limit(@$list->usertype->name, '50', '...') }}</td>  
													@if(isset($viewType) && ($viewType == 'active' || $viewType == 'inactive'))
													<td>
													    <div class="custom-switches">
															<label class="">
																<input value="1" data-id="{{@$list->id}}"  data-status="{{@$list->status}}" data-col="status" data-table="admins" type="checkbox" name="custom-switch-checkbox" class="change-status custom-switch-input" {{ (@$list->status == 1 ? 'checked' : '')}}>
																<span class="custom-switch-indicator"></span>
															</label>
														</div>
													</td>
													@endif
													
													@if(isset($viewType) && $viewType == 'active')
													<td>
														@if(\Auth::user()->id != $list->id)
														<div class="card-header-action">
															<a href="{{URL::to('users/edit/'.$list->id)}}" class="btn btn-primary">Edit User</a>
														</div>
														@endif
													</td>
													@endif
												</tr>	
											@endforeach
											</tbody>
											@else
											<tbody>
												<tr>
													<td style="text-align:center;" colspan="{{ isset($viewType) && $viewType == 'active' ? '6' : '5' }}">
														No Record found
													</td>
												</tr>
											</tbody>
											@endif
										</table> 
									</div>
								</div>
							</div>
						</div>
						<div class="card-footer">
							{!! $lists->appends(\Request::except('page'))->render() !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

@endsection
