@extends('layouts.admin')
@section('title', 'Leads')

@section('content')
<style>
.mytooltip{display: inline;position: relative;z-index: 999;}
.mytooltip .tooltip-item {background: rgba(0, 0, 0, 0.1);cursor: pointer;display: inline-block; font-weight: 500; padding: 0 10px;}
.mytooltip .tooltip-content {position: absolute;z-index: 9999;width: 360px;left: 50%;margin: 0 0 20px -180px;bottom: 100%;text-align: left;font-size: 14px;line-height: 30px; -webkit-box-shadow: -5px -5px 15px rgba(48, 54, 61, 0.2);box-shadow: -5px -5px 15px rgba(48, 54, 61, 0.2);background: #2b2b2b;opacity: 0;cursor: default;pointer-events: none;}
.mytooltip .tooltip-content::after {content: '';top: 100%;left: 50%;border: solid transparent; 
height: 0;width: 0;position: absolute;pointer-events: none;border-color: #2a3035 transparent transparent;border-width: 10px;margin-left: -10px;}
.mytooltip .tooltip-content img {position: relative;height: 140px;display: block;float: left; margin-right: 1em;}
.mytooltip .tooltip-item::after {content: '';position: absolute;width: 360px;height: 20px;
bottom: 100%;left: 50%;pointer-events: none;-webkit-transform: translateX(-50%);transform: translateX(-50%);}
.mytooltip:hover .tooltip-item::after {pointer-events: auto;}
.mytooltip:hover .tooltip-content {pointer-events: auto;opacity: 1;-webkit-transform: translate3d(0, 0, 0) rotate3d(0, 0, 0, 0deg);transform: translate3d(0, 0, 0) rotate3d(0, 0, 0, 0deg);}
.mytooltip:hover .tooltip-content2 {opacity: 1;font-size: 18px;}
.mytooltip .tooltip-text {font-size: 14px;line-height: 24px;display: block;padding: 1.31em 1.21em 1.21em 0;color: #fff;}
.filter_panel {background: #f7f7f7;margin-bottom: 10px;border: 1pxsolid #eee;display: none;}
.card .card-body .filter_panel { padding: 20px;}
</style>
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
						<h4>Leads</h4>
						<div class="card-header-action">
							<a href="javascript:;" class="btn btn-theme btn-theme-sm" data-bs-toggle="modal" data-bs-target="#importLeadModal" title="Import Lead">
								<i class="fas fa-upload"></i> Import Lead
							</a>
							<a href="{{route('leads.create')}}" class="btn btn-primary">Add Lead</a>
							<a href="javascript:;" class="btn btn-theme btn-theme-sm filter_btn"><i class="fas fa-filter"></i> Filter</a>
						</div>
					</div>
						<div class="card-body">
						    <div class="filter_panel">
								<h4>Search By Details</h4>								
								<form action="{{URL::to('/leads')}}" method="get">
									<div class="row">
									<div class="col-md-4">
											<div class="form-group">
												<label for="did" class="col-form-label" style="visibility:hidden;">Lead ID</label>
											<div class="row">
											    <div class="col-md-3">
											       <b>Lead -</b>
											        </div>
											    	<div class="col-md-7">		
											 {!! Form::text('id', Request::get('id'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Lead ID', 'id' => 'did' ))  !!}
											</div>	</div></div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="company_name" class="col-form-label">Name</label>
												{!! Form::text('name', Request::get('name'), array('class' => 'form-control agent_company_name', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Name', 'id' => 'name' ))  !!}
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="email" class="col-form-label">Email</label>
												{!! Form::text('email', Request::get('email'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Email', 'id' => 'email' ))  !!}
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="phone" class="col-form-label">Phone</label>
												{!! Form::text('phone', Request::get('phone'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Phone', 'id' => 'phone' ))  !!}
											</div>
										</div>
										
										<div class="col-md-4">
											<div class="form-group">
												<label for="from" class="col-form-label">From</label>
												{!! Form::text('from', Request::get('from'), array('class' => 'form-control filterdatepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'From', 'id' => '' ))  !!}
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="to" class="col-form-label">To</label>
												{!! Form::text('to', Request::get('to'), array('class' => 'form-control filterdatepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'To', 'id' => '' ))  !!}
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12 text-center">
									
											{!! Form::submit('Search', ['class'=>'btn btn-primary btn-theme-lg' ])  !!}
											<a class="btn btn-info" href="{{URL::to('/leads')}}">Reset</a>
										</div>
									</div>
								</form>
							</div>
							<div class="table-responsive common_table lead_table_data"> 
								<table class="table text_wrap">
									<thead>
										<tr>
											<th>Lead</th>
											<th>Client</th>
											<th>Services</th>
											<th>Level & Status</th>
											<th>Action</th>
											<th>Options</th>
										</tr> 
									</thead>
									<tbody class="tdata">	
										@if(@$totalData !== 0)
										@foreach (@$lists as $list)	
										<?php 
										$leadIdForLinks = $list->lead_id ?? $list->id;
										$displayId = $list->lead_id ?? $list->id;
										$assigneeId = $list->assignee ?? $list->assign_to ?? null;
										$statusDisplay = ($list->status === 0 || $list->status === '0') ? 'Not Contacted' : ((is_string($list->status) && $list->status !== '') ? $list->status : '—');
										?> 
										<tr id="id_{{@$list->id}}">
											<td><i class="fas fa-ticket-alt"></i> <a class="" href="{{route('leads.detail', base64_encode(convert_uuencode($leadIdForLinks)))}}">Lead - {{str_pad($displayId, 3, '0', STR_PAD_LEFT)}}</a> <br/><i class="fas fa-calendar-alt"></i> 
										
											{{@$list->created_at}}
											<?php
											$assigneduser = \App\Support\StaffAssigneeResolver::firstStaffFromAssigneeValue($assigneeId);
											if($assigneduser){
											    ?>
											    <br>
											   Assigned: <a target="_blank" href="{{ route('staff.view', ['id' => $assigneduser->id]) }}">{{$assigneduser->first_name}} {{$assigneduser->last_name}}</a> 
											    <?php
											}else{ echo '-'; }
											?>
											</td>
											<td><i class="fas fa-user"></i>  {{@$list->first_name}} {{@$list->last_name}} <br/> <i class="fas fa-mobile"></i> {{@$list->phone}} <br/> <i class="fas fa-envelope"></i> {{@$list->email}}</td>
											<td>{{@$list->service}} <br/> {{@$list->created_at}}</td>
											<td><div class="lead_stars"><i class="fas fa-star"></i><span>{{@$list->lead_quality}}</span> {{ $statusDisplay }}</div></td>
											<td>{{ $statusDisplay }}</td>
											<td>
												<div class="dropdown action_toggle">
													<a class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></a>
													<div class="dropdown-menu">
														<a class="dropdown-item has-icon" href="{{route('leads.detail', base64_encode(convert_uuencode($leadIdForLinks)))}}"><i class="fas fa-eye"></i> View Details</a>
														<a class="dropdown-item has-icon assignlead_modal" href="javascript:;" mleadid="{{base64_encode(convert_uuencode($leadIdForLinks))}}"><i class="fas fa-edit"></i> Assign To</a>
										@if($list->converted == 0)
											<a class="dropdown-item has-icon" href="{{URL::to('/leads/convert/'.$leadIdForLinks)}}" onclick="return confirm('Are you sure?')"><i class="fas fa-user"></i> Convert To Client</a>	
											@endif
													</div>
												</div>	
											</td>
										</tr>
										@endforeach 

										@else
										<tr>
											<td style="text-align:center;" colspan="10">
											No Record found
											</td>
										</tr>										
										@endif  
									</tbody>
								</table> 
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

<div class="modal fade" id="assignlead_modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				  <h4 class="modal-title">Assign Lead</h4>
				  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
			</div>
			{!! Form::open(array('url' => 'leads/assign', 'name'=>"add-assign", 'autocomplete'=>'off', "enctype"=>"multipart/form-data", 'id'=>"addnoteform"))  !!}
			<div class="modal-body">
				<div class="form-group row">
					<div class="col-sm-12">
						<input id="mlead_id" name="mlead_id" type="hidden" value="">
						<select name="assignto" class="form-control tomselect" style="width: 100%;">
							<option value="">Select</option>
							@foreach(\App\Models\Staff::all() as $ulist)
							<option value="{{@$ulist->id}}">{{@$ulist->first_name}} {{@$ulist->last_name}}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				{!! Form::button('<i class="fas fa-save"></i> Assign Lead', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-assign")' ])  !!}
			</div>
			{!! Form::close()  !!}
		</div>
	</div>
</div>

<!-- Import Lead Modal -->
<div id="importLeadModal" data-bs-backdrop="static" data-bs-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload"></i> Import Lead from File
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" name="importLeadForm" action="{{ route('leads.import') }}" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Instructions:</strong> Upload a JSON file exported from migrationmanager2, bansalcrm2, or the Office Visit Form (<code>office-visit-{id}-crm.json</code>).
                    </div>

                    <div class="form-group">
                        <label for="import_file">Select JSON File <span class="span_req">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="import_file" name="import_file" accept=".json" required>
                            <label class="custom-file-label" for="import_file">Choose file...</label>
                        </div>
                        <small class="form-text text-muted">Supported: CRM exports (migrationmanager2, bansalcrm2) and Office Visit Form JSON.</small>
                        @if ($errors->has('import_file'))
                            <span class="custom-error" role="alert">
                                <strong>{{ @$errors->first('import_file') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="skip_duplicates" name="skip_duplicates" value="1" checked>
                            <label class="form-check-label" for="skip_duplicates">
                                Skip if lead with same email or phone number already exists
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Import Lead
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    jQuery(document).ready(function($){
        $('.filter_btn').on('click', function(){
            $('.filter_panel').slideToggle();
        });
        $('.assignlead_modal').on('click', function(){
            var val = $(this).attr('mleadid');
            $('#assignlead_modal #mlead_id').val(val);
            $('#assignlead_modal').modal('show');
        });

        // File input label update for import modal
        $('#import_file').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName || 'Choose file...');
        });
    });

    // Show error toast when import fails
    @if ($errors->has('import_file'))
    $(document).ready(function() {
        var modalElement = document.getElementById('importLeadModal');
        if (modalElement) {
            var modal = bootstrap.Modal.getInstance(modalElement);
            if (!modal) { modal = new bootstrap.Modal(modalElement); }
            modal.show();
        }
        var errorMessage = {!! json_encode($errors->first('import_file')) !!};
        if (typeof iziToast !== 'undefined') {
            iziToast.error({ title: 'Import Failed', message: errorMessage, position: 'topRight', timeout: 8000 });
        } else {
            alert('Import Error:\n\n' + errorMessage);
        }
    });
    @endif

    // Show success toast when import succeeds
    @if (Session::has('success'))
    $(document).ready(function() {
        var successMessage = {!! json_encode(Session::get('success')) !!};
        if (typeof iziToast !== 'undefined') {
            iziToast.success({ title: 'Import Successful', message: successMessage, position: 'topRight', timeout: 5000 });
        }
    });
    @endif

    whenTomSelectReady(function () {
        initTomSelectPreserveValue('#assignlead_modal select[name="assignto"]', {
            width: '100%',
            placeholder: 'Select',
            allowClear: true,
            dropdownParent: document.querySelector('#assignlead_modal .modal-content') || '#assignlead_modal'
        });
    });
</script>
@endsection