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
							<h4>Office Visit Report Date wise</h4>
						</div>
						<div class="card-body">
							<div class="tab-content" id="checkinContent">
								<div class="tab-pane fade show active" id="office" role="tabpanel" aria-labelledby="office-tab">
									<div class="table-responsive common_table">
										<table class="table text_wrap">
											<thead>
												<tr>
                                                    <th>Sno.</th>
													<th>Date</th>
                                                    <th>Office</th>
													<th>No of person</th>
												</tr>
											</thead>
											@if(count($lists) >0)
											<tbody class="tdata">
												@if(@$totalData !== 0)
												@foreach (@$lists as $list)

												<tr>
                                                    <td>{{ ++$i }}</td>
													<td>{{ @$list->date == "" ? config('constants.empty') : date('d/m/Y',strtotime($list->date))  }}</td>
                                                    <td>{{ @$list->office_name == "" ? config('constants.empty') : $list->office_name }}</td>
													<td>{{ @$list->personCount == "" ? config('constants.empty') : $list->personCount }}</td>
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
