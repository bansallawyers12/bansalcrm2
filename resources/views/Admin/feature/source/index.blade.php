@extends('layouts.admin')
@section('title', 'Source')
 
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
				 <div class="col-3 col-md-3 col-lg-3">
			        	@include('../Elements/Admin/setting')
		        </div>       
				<div class="col-9 col-md-9 col-lg-9">
					<div class="card">
						<div class="card-header">
							<h4>All Source</h4>
							<div class="card-header-action">
								<a href="{{route('admin.feature.source.create')}}" class="btn btn-primary">Create Source</a>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive common_table"> 
								<table class="table text_wrap">
								<thead>
									<tr>
										<!--<th class="text-center" style="width:30px;">
											<div class="custom-checkbox custom-checkbox-table custom-control">
												<input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad" class="custom-control-input" id="checkbox-all">
												<label for="checkbox-all" class="custom-control-label">&nbsp;</label>
											</div>
										</th>-->
										<th>Name</th>
										<th></th>
									</tr> 
								</thead>
								@if(@$totalData !== 0)
								<?php $i=0; ?>
								<tbody class="tdata">	
								@foreach (@$lists as $list)
									<tr id="id_{{@$list->id}}">
										<!--<td class="text-center">
											<div class="custom-checkbox custom-control">
											{{--	<input data-id="{{@$list->id}}" data-email="{{@$list->email}}" data-name="{{@$list->first_name}} {{@$list->last_name}}" type="checkbox" data-checkboxes="mygroup" class="cb-element custom-control-input" id="checkbox-{{$i}}">
												<label for="checkbox-{{$i}}" class="custom-control-label">&nbsp;</label> --}}
											</div>
										</td>-->
										<td>{{ @$list->name == "" ? config('constants.empty') : str_limit(@$list->name, '50', '...') }}</td> 	
										<td>
											<div class="dropdown d-inline">
												<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
												<div class="dropdown-menu">
													<a class="dropdown-item has-icon" href="{{URL::to('/admin/source/edit/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="far fa-edit"></i> Edit</a>
													<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction({{@$list->id}}, 'sources')"><i class="fas fa-trash"></i> Delete</a>
												</div>
											</div>								  
										</td>
									</tr>	
								@endforeach	 
								</tbody>
								@else
								<tbody>
									<tr>
										<td style="text-align:center;" colspan="4">
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
		</div>
	</section>
</div>
 
@endsection
@section('scripts') 
<script>
jQuery(document).ready(function($){	
	$('.cb-element').change(function () {		
	if ($('.cb-element:checked').length == $('.cb-element').length){
	  $('#checkbox-all').prop('checked',true);
	}
	else {
	  $('#checkbox-all').prop('checked',false);
	}
	/* if ($('.cb-element:checked').length > 0){
			$('.is_checked_client').show();
			$('.is_checked_clientn').hide();
		}else{
			$('.is_checked_client').hide();
			$('.is_checked_clientn').show();
		} */
	});	
});	
</script>
@endsection