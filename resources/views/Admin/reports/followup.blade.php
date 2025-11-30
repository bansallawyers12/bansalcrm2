@extends('layouts.admin')
@section('title', 'Followup')

@section('content')
<style>
.fc-event-container .fc-h-event{cursor:pointer;}
.fc-more-popover {
    overflow-y: scroll;
   max-height: 50%;
    max-width: auto;
}
</style>
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
							<h4>Followup</h4>
							
						</div>
						<div class="card-body">
							 <div class="fc-overflow">
								<div id="myEvent"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<?php
 $sched_res = [];
 if(Auth::user()->role == 1){
     $visaexpires = \App\Models\Note::select('client_id','id', 'followup_date', 'description')->where('type','client')->where('folloup',1)->where('status',0)->get()->toArray();
 }else{
     $visaexpires = \App\Models\Note::select('client_id','id', 'followup_date', 'description')->where('assigned_to',Auth::user()->id)->where('type','client')->where('folloup',1)->where('status',0)->get()->toArray();

 }
foreach($visaexpires as $visaexpire){
  
    $addd = \App\Models\Admin::where('id',$visaexpire['client_id'])->select('id','client_id','first_name','last_name','email','phone')->first();
    //print_r($addd->id);
	/*if($visaexpire['followup_date'] != ''){
	    $visaexpire['id'] = $visaexpire['id'];
	    $visaexpire['clientid'] = $addd->id;
		$visaexpire['stitle'] = addslashes($addd->client_id);
		$visaexpire['name'] = base64_encode($addd->first_name.' '.$addd->last_name);
		$visaexpire['email'] = base64_encode($addd->email);
		$visaexpire['phone'] = base64_encode($addd->phone);
		$visaexpire['startdate'] = date("F d, Y",strtotime($visaexpire['followup_date']));
		$visaexpire['description'] = $visaexpire['description'];
	    //$row['start'] = date("F d, Y h:i A",strtotime($visaexpire->followup_date));
		$visaexpire['end'] = date("F d, Y h:i A",strtotime($visaexpire['followup_date']));
	    //$row['backgroundColor'] = "#00bcd4";
		$visaexpire['url'] = URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$addd->id)));
		$sched_res[$visaexpire['id']] = $visaexpire;
    }*/
	
	
}
?>
@endsection
@section('scripts')
<script>
var events = [];
 var scheds = $.parseJSON('<?= json_encode($sched_res, JSON_HEX_APOS | JSON_UNESCAPED_SLASHES); ?>');
 if (!!scheds) {
        Object.keys(scheds).map(k => {
            var row = scheds[k]
            events.push({ id: row.id, title: row.stitle, start: row.startdate, end: row.end });
        });
    }
var today = new Date();
year = today.getFullYear();
month = today.getMonth();
day = today.getDate();
var calendar = $("#myEvent").fullCalendar({
  height: "auto",
  defaultView: "month",
  editable: false,
  selectable: true,
   eventLimit: true, // If you set a number it will hide the itens
    eventLimitText: "More",
  displayEventTime: false,
  header: {
    left: "prev,next today",
    center: "title",
    right: "month,agendaWeek,agendaDay,listMonth",
  },
   events: events,
    eventClick: function(info) {
		console.log(info);
            var details = $('#event-details-modal');
            var id = info.id;

            if (!!scheds[id]) {
                details.find('#title').text(scheds[id].stitle);
                details.find('#description').html(scheds[id].description);
                details.find('#clname').text( atob(scheds[id].name));
                 details.find('#phone').text( atob(scheds[id].phone));
                 details.find('#email').text( atob(scheds[id].email));
                 details.find('#start').text(scheds[id].followup_date);
                details.find('#client_id').val(scheds[id].clientid);
                details.find('#lead_id').val(scheds[id].id);
                if (scheds[id].url) {client_id
                details.find('#url').html('<a target="_blank" href="'+scheds[id].url+'">View Client '+scheds[id].id+'</a>');
				
				}
                details.modal('show');
            } else {
                alert("Event is undefined");
            }
        },

});
</script>
<div class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" id="event-details-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title">Schedule Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <div class="modal-body rounded-0">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <dl>
                                    <dt class="text-muted">Client ID</dt>
                                    <dd id="title" class="fw-bold fs-4"></dd>
                                    <dd id="url" class="fw-bold fs-4"></dd>
                                    <dt class="text-muted">Client Name</dt>
                                    <dd id="clname" class="fw-bold fs-4"></dd>
                                   
                                   
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <dl>
                                    <dt class="text-muted">Email</dt>
                                    <dd id="email" class="fw-bold fs-4"></dd>
                                    
                                    <dt class="text-muted">Phone</dt>
                                    <dd id="phone" class="fw-bold fs-4"></dd>
                                   
                                   
                                </dl>
                            </div>
                            <div class="col-md-12">
                                <dt class="text-muted">Note</dt>
                                    <dd id="description" class="fw-bold fs-4"></dd>
                                </div>
                                 </div>
                         <form method="post" name="retagmodalsave" id="retagmodalsave" action="{{URL::to('/admin/clients/followup/retagfollowup')}}" autocomplete="off" enctype="multipart/form-data">
			            	@csrf   
			            	<input type="hidden" name="client_id" id="client_id">
			            	<input type="hidden" name="lead_id" id="lead_id">
			            	 <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Assigned To</label>
                                    <select data-valid="required" class="form-control select2" id="changeassignee" name="changeassignee">
                                         <option value="">Select</option>
						                 <?php 
											foreach(\App\Models\Admin::where('role','!=',7)->orderby('first_name','ASC')->get() as $admin){
												$branchname = \App\Models\Branch::where('id',$admin->office_id)->first();
										?>
												<option value="<?php echo $admin->id; ?>"><?php echo $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')'; ?></option>
										<?php } ?>
									</select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="date" name="followup_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Time</label>
                                    <input type="time" name="followup_time" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Message</label>
                                    <textarea class="form-control" name="message"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary" onclick="customValidate('retagmodalsave')">Save</button>
                                </div>
                                </div>
                                </form>
                        
                        
                    </div>
                </div>
               
            </div>
        </div>
    </div>
@endsection