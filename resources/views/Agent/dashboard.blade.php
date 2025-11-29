@extends('layouts.agent')
@section('title', 'Agent Dashboard')

@section('content')

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="row">
		
			
			
			
			<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12 mb-4">
				<div class="card dash_card">
					<div class="card-statistic-4">
						<div class="align-items-center justify-content-between">
							<div class="row "> 
								<div class="col-lg-12 col-md-12">
									<?php $client = \App\Admin::where('agent_id', Auth::user()->id)->where('role', '=', '7')->count();
									?>
									<div class="card-content">
										<h5 class="font-14">Total Clients</h5>
										<h2 class="mb-3 font-18"><?php echo $client; ?></h2>
										<p class="mb-0"><span class="col-green"><?php echo $client; ?> clients</span> ongoing</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
		</div>

		<div class="row">
			
		
	</section>
</div> 



@endsection
@section('scripts')
<?php
$leadsconverted = \App\Lead::where('converted', 1)->count();
$leadsprogress = \App\Lead::where('converted', 0)->count();
$leadstotal = \App\Lead::where('id', '!=','')->count();
$data = array($leadstotal, $leadsconverted, $leadsprogress); 


?>
<script>
var data = {{json_encode($data)}};

jQuery(document).ready(function($){
	$(document).delegate('#create_task', 'click', function(){
		$('#create_task_modal').modal('show');
		$('.cleintselect2').select2({
			dropdownParent: $('#create_task_modal .modal-content'),
		});
	});
	
	
	$(document).delegate('.opentaskview', 'click', function(){
		$('#opentaskview').modal('show');
		var v = $(this).attr('id');
		$.ajax({
			url: site_url+'/admin/get-task-detail',
			type:'GET',
			data:{task_id:v},
			success: function(responses){
				
				$('.taskview').html(responses);
			}
		});
	});
});
</script>
@endsection