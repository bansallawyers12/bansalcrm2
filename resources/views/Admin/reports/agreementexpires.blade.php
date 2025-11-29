@extends('layouts.admin')
@section('title', 'Aggrement Expire Report')

@section('content')
<style>
.fc-event-container .fc-h-event{cursor:pointer;}
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
							<h4>Aggrement Expire Report</h4>
							
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
$partners = \App\Partner::where('id','!=', '')->get();
foreach($partners as $partner){
	if($partner->contract_expiry != ''){
		$row['id'] = $partner->id;
		$row['title'] = $partner->partner_name;
		$row['startdate'] = date("F d, Y",strtotime($partner->contract_expiry));
		$row['start'] = date("F d, Y h:i A",strtotime($partner->contract_expiry));
		$row['end'] = date("F d, Y h:i A",strtotime($partner->contract_expiry));
		$row['url'] = URL::to('/admin/partners/detail/'.base64_encode(convert_uuencode(@$partner->id)));
		$row['backgroundColor'] = "#00bcd4";
		$sched_res[$partner->id] = $row;
	}
	
	
}
?>
@endsection
@section('scripts')
<script>
var events = [];
 var scheds = $.parseJSON('<?= json_encode($sched_res) ?>');
 if (!!scheds) {
        Object.keys(scheds).map(k => {
            var row = scheds[k]
            events.push({ id: row.id, title: row.title, start: row.start, end: row.end });
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
                details.find('#title').text(scheds[id].title);
                details.find('#description').text(scheds[id].description);
                details.find('#start').text(scheds[id].startdate);
               if (scheds[id].url) {
					window.open(scheds[id].url, "_blank");
					return false;
				}
                //details.modal('show');
				//window.location.href = scheds[id].url;
            } else {
                alert("Event is undefined");
            }
        },
  /* events: [
    {
      title: "Palak Jani",
      start: new Date(year, month, day, 11, 30),
      end: new Date(year, month, day, 12, 00),
      backgroundColor: "#00bcd4",
    },
    {
      title: "Priya Sarma",
      start: new Date(year, month, day, 13, 30),
      end: new Date(year, month, day, 14, 00),
      backgroundColor: "#fe9701",
    },
    {
      title: "John Doe",
      start: new Date(year, month, day, 17, 30),
      end: new Date(year, month, day, 18, 00),
      backgroundColor: "#F3565D",
    },
    {
      title: "Sarah Smith",
      start: new Date(year, month, day, 19, 00),
      end: new Date(year, month, day, 19, 30),
      backgroundColor: "#1bbc9b",
    },
    {
      title: "Airi Satou",
      start: new Date(year, month, day + 1, 19, 00),
      end: new Date(year, month, day + 1, 19, 30),
      backgroundColor: "#DC35A9",
    },
    {
      title: "Angelica Ramos",
      start: new Date(year, month, day + 1, 21, 00),
      end: new Date(year, month, day + 1, 21, 30),
      backgroundColor: "#fe9701",
    },
    {
      title: "Palak Jani",
      start: new Date(year, month, day + 3, 11, 30),
      end: new Date(year, month, day + 3, 12, 00),
      backgroundColor: "#00bcd4",
    },
    {
      title: "Priya Sarma",
      start: new Date(year, month, day + 5, 2, 30),
      end: new Date(year, month, day + 5, 3, 00),
      backgroundColor: "#9b59b6",
    },
    {
      title: "John Doe",
      start: new Date(year, month, day + 7, 17, 30),
      end: new Date(year, month, day + 7, 18, 00),
      backgroundColor: "#F3565D",
    },
    {
      title: "Mark Hay",
      start: new Date(year, month, day + 5, 9, 30),
      end: new Date(year, month, day + 5, 10, 00),
      backgroundColor: "#F3565D",
    },
  ], */
});
</script>
<div class="modal fade" tabindex="-1" data-bs-backdrop="static" id="event-details-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title">Schedule Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <div class="modal-body rounded-0">
                    <div class="container-fluid">
                        <dl>
                            <dt class="text-muted">Title</dt>
                            <dd id="title" class="fw-bold fs-4"></dd>
                           
                            <dt class="text-muted">Expire Date</dt>
                            <dd id="start" class=""></dd>
                          
                        </dl>
                    </div>
                </div>
               
            </div>
        </div>
    </div>
@endsection