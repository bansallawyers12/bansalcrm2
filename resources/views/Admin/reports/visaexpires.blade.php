@extends('layouts.admin')
@section('title', 'Application Reports')

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
				@include('Elements.flash-message')
			</div>
			<div class="custom-error-msg">
			</div>
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Visa Expires Reports</h4>
							
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
@endsection
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wait for FullCalendar to be loaded
    if (typeof window.FullCalendar === 'undefined') {
        console.error('FullCalendar v6 not loaded');
        return;
    }

    var calendarEl = document.getElementById('myEvent');
    if (!calendarEl) {
        console.error('Calendar element #myEvent not found');
        return;
    }

    var calendar = new window.FullCalendar.Calendar(calendarEl, {
        height: "auto",
        initialView: "dayGridMonth",
        editable: false,
        selectable: true,
        dayMaxEvents: true, // Shows "more" link when too many events
        moreLinkText: "More",
        plugins: [
            window.FullCalendar.dayGridPlugin,
            window.FullCalendar.timeGridPlugin,
            window.FullCalendar.listPlugin,
            window.FullCalendar.interactionPlugin
        ],
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,timeGridDay,listMonth",
        },
        events: @json($visaExpiresEventsUrl),
        eventClick: function(info) {
            console.log(info);

            // Prevent default FullCalendar behavior
            info.jsEvent.preventDefault();

            var displayDate = (info.event.extendedProps && info.event.extendedProps.displayDate)
                ? info.event.extendedProps.displayDate
                : info.event.startStr;

            var details = document.getElementById('event-details-modal');
            if (details) {
                var titleEl = details.querySelector('#title');
                var startEl = details.querySelector('#start');
                if (titleEl) titleEl.textContent = info.event.title;
                if (startEl) startEl.textContent = displayDate;
            }

            if (info.event.url) {
                window.open(info.event.url, "_blank", "noopener,noreferrer");
            }
        }
    });

    calendar.render();
});
</script>
@endsection
@section('extra')
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
</script>
<div class="modal fade" tabindex="-1" data-bs-backdrop="static" id="event-details-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title">Schedule Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">@icon('times')</button>
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