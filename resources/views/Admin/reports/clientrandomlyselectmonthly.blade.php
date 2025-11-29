@extends('layouts.admin')
@section('title', 'Task Report')

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
							<h4>Client Selection For Monthly Reward Report</h4>
                            <div class="card-header-action">
                                <button type="button" class="btn btn-primary random_client_selection">Monthly Client Selection For Reward </button>
							</div>
                        </div>

						<div class="card-body">
							<div class="tab-content" id="checkinContent">
								<div class="tab-pane fade show active" id="office" role="tabpanel" aria-labelledby="office-tab">
									<div class="table-responsive common_table">
										<table class="table text_wrap">
											<thead>
												<tr>
                                                    <th>Sno.</th>
													<th>Month</th>
													<th>Selected Client</th>
                                                    <th>Phone</th>
												</tr>
											</thead>
											@if(count($lists) >0)
											<tbody class="tdata">
												@if(@$totalData !== 0)
												@foreach (@$lists as $list)
                                                <tr>
                                                    <td>{{ ++$i }}</td>
													<td>{{ @$list->reward_date == "" ? config('constants.empty') : date('MY',strtotime($list->reward_date))  }}</td>
													<td><a href="{{URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$list->client_id)))}}">{{ @$list->first_name == "" ? config('constants.empty') : $list->first_name.' '.$list->last_name }}</a></td>
                                                    <td>{{ @$list->phone == "" ? config('constants.empty') : $list->phone }}</td>
                                                </tr>
												@endforeach
												@endif
											</tbody>
											@else
												<tbody>
													<tr>
														<td style="text-align:center;" colspan="3">
															No Record found
														</td>
													</tr>
												</tbody>
											@endif
										</table>
                                        {!! $lists->appends($_GET)->links() !!}
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
@section('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
jQuery(document).ready(function($){
    $(document).delegate('.random_client_selection', 'click', function(){
        var current_date = '<?php echo date("Y-m-d"); ?>';
        console.log('current_date='+current_date);
        $.ajax({
            url:'{{URL::to('/admin/report/save_random_client_selection')}}',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type:'POST',
            data:{current_date:current_date},
            datatype:'json',
            success:function(res){
                var obj = JSON.parse(res);
                if(obj.status == true){
                    location.reload();
                } else {
                    location.reload();
                }
            }
        });
    });
});
</script>
