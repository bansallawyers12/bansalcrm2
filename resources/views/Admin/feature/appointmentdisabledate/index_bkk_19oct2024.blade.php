@extends('layouts.admin')
@section('title', 'Block Slot')

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
							<h4>Block Slot</h4>
							<!--<div class="card-header-action">
								<a href="{{--route('admin.feature.appointmentdisabledate.create')--}}" class="btn btn-primary">Create Not Available Appointment Dates</a>
							</div>-->
						</div>
						<div class="card-body">
							<div class="table-responsive common_table">
								<table class="table text_wrap">
								<thead>
									<tr>
                                        <th>Person</th>
                                        <th>Block Slot</th>
										<th></th>
									</tr>
								</thead>
								@if(@$totalData !== 0)
								<?php $i=0; ?>
								<tbody class="tdata">
								@foreach (@$lists as $list)
									<tr id="id_{{@$list->id}}">
                                        <td><?php
                                        if(isset($list->person_id)){
                                            if($list->person_id == 1){
                                                $title = 'Ajay';
                                                /*if($list->service_type == 1){ //Paid
                                                    $title = 'Ajay - Paid';
                                                } elseif ($list->service_type == 2 ) { //Free
                                                    $title = 'Ajay - Free';
                                                }*/
                                            } else if( $list->person_id == 2 ){
                                                $title = 'Shubam';
                                            } else if( $list->person_id == 3 ){
                                                $title = 'Tourist';
                                            } else if( $list->person_id == 4 ){
                                                $title = 'Education';
                                            }
                                            //$title .=  "<br/>Daily Timings- ".date('H:i',strtotime($list->start_time))."-".date('H:i',strtotime($list->end_time));
                                            //$title .=  "<br/>Weekend- ".$list->weekend;
                                            echo  $title;
                                        }?></td>
										<td>
                                            <?php
                                            $disableSlotArr = \App\BookServiceDisableSlot::select('id','book_service_slot_per_person_id','disabledates','slots')->where('book_service_slot_per_person_id',$list->id)->get();
                                            //echo count($disableSlotArr);
                                            if( !empty($disableSlotArr) && count($disableSlotArr)>0){
                                                foreach($disableSlotArr as $slotVal){
                                                ?>
                                                <li>{{ date("d/m/Y",strtotime($slotVal->disabledates)); }} - {{ $slotVal->slots }}</li>
                                                <?php
                                                }
                                            } else { echo 'N/A';}?>
                                        </td>

                                        <td>
											<div class="dropdown d-inline">
												<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
												<div class="dropdown-menu">
													<a class="dropdown-item has-icon" href="{{URL::to('/admin/appointment-dates-disable/edit/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="far fa-edit"></i> Edit</a>
												</div>
											</div>
										</td>
									</tr>
								@endforeach
								</tbody>
								@else
								<tbody>
									<tr>
										<td style="text-align:center;" colspan="7">
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

	});
});
</script>
@endsection
