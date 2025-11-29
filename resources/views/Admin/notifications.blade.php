@extends('layouts.admin')
@section('title', 'Notifications')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Notifications</h4>
							
						</div>
						<div class="card-body">
							<table class="table">
								<thead>
									<tr>
									  <th></th>
									  <th></th>
									  <th>Date</th>
									 
									</tr> 
								</thead>
								
								@foreach (@$lists as $list)
							
								<tbody class="tdata taskdata ">	
									<tr id="id_{{@$list->id}}"> 
										<td >
										@if($list->receiver_status == 1)
											<span class="check"><i class="fa fa-eye"></i></span>
										@else
											<span class="round"></span>
										@endif
										</td>
										<td><a href="{{$list->url}}?t={{$list->id}}">{{$list->message}}</a></td> 
										<td>{{date('d/m/Y h:i A',strtotime($list->created_at))}}</td> 
										
									</tr>	
								@endforeach	
								</tbody>
								
							</table> 
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