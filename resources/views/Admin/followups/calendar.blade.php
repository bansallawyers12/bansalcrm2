@extends('layouts.admin')
@section('title', $consultantLabel)

@section('content')
<style>
.fc-event-container .fc-h-event{cursor:pointer;}
.fc-more-popover {
	overflow-y: scroll;
	max-height: 50%;
	max-width: auto;
}
</style>
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
							<h4 class="mb-0">{{ $consultantLabel }}</h4>
							<a href="{{ route('followups.index') }}" class="btn btn-outline-secondary btn-sm">Back to listing</a>
						</div>
						<div class="card-body">
							<div class="fc-overflow">
								<div id="followupCalendar"></div>
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
	if (typeof window.FullCalendar === 'undefined') {
		console.error('FullCalendar v6 not loaded');
		return;
	}

	var events = [];
	var scheds = {!! json_encode($sched_res, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
	if (!!scheds && typeof scheds === 'object') {
		Object.keys(scheds).map(function(k) {
			var row = scheds[k];
			events.push({ id: row.id, title: row.stitle, start: row.startdate, end: row.end });
		});
	}

	var calendarEl = document.getElementById('followupCalendar');
	if (!calendarEl) return;

	var calendar = new window.FullCalendar.Calendar(calendarEl, {
		height: "auto",
		initialView: "dayGridMonth",
		editable: false,
		selectable: false,
		dayMaxEvents: true,
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
		events: events,
		eventClick: function(info) {
			var details = document.getElementById('appointment-event-details-modal');
			if (!details) return;
			var id = info.event.id;
			if (!scheds[id]) {
				alert("Event is undefined");
				return;
			}
			var row = scheds[id];
			var titleEl = details.querySelector('#appt-title');
			var descEl = details.querySelector('#appt-description');
			var clnameEl = details.querySelector('#appt-clname');
			var phoneEl = details.querySelector('#appt-phone');
			var emailEl = details.querySelector('#appt-email');
			var startEl = details.querySelector('#appt-start');
			var urlEl = details.querySelector('#appt-url');
			if (titleEl) titleEl.textContent = row.stitle;
			if (descEl) descEl.innerHTML = row.description;
			if (clnameEl) clnameEl.textContent = atob(row.name);
			if (phoneEl) phoneEl.textContent = atob(row.phone);
			if (emailEl) emailEl.textContent = atob(row.email);
			if (startEl) startEl.textContent = row.followup_date;
			if (urlEl && row.url) {
				urlEl.innerHTML = '<a target="_blank" rel="noopener noreferrer" href="'+row.url+'">View client</a>';
			}
			bootstrap.Modal.getOrCreateInstance(details).show();
		}
	});

	calendar.render();
});
</script>

<div class="modal fade" tabindex="-1" data-bs-backdrop="true" data-bs-keyboard="true" id="appointment-event-details-modal">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content rounded-0">
			<div class="modal-header rounded-0">
				<h5 class="modal-title">Appointment details</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body rounded-0">
				<div class="row">
					<div class="col-md-6">
						<dl>
							<dt class="text-muted small">Client reference</dt>
							<dd id="appt-title" class="fw-semibold"></dd>
							<dt class="text-muted small mt-2">Client name</dt>
							<dd id="appt-clname" class="fw-semibold"></dd>
							<dt class="text-muted small mt-2">Link</dt>
							<dd id="appt-url"></dd>
						</dl>
					</div>
					<div class="col-md-6">
						<dl>
							<dt class="text-muted small">Email</dt>
							<dd id="appt-email" class="fw-semibold"></dd>
							<dt class="text-muted small mt-2">Phone</dt>
							<dd id="appt-phone" class="fw-semibold"></dd>
							<dt class="text-muted small mt-2">Scheduled</dt>
							<dd id="appt-start" class="fw-semibold"></dd>
						</dl>
					</div>
					<div class="col-12">
						<dt class="text-muted small">Notes</dt>
						<dd id="appt-description" class="border rounded p-2 bg-light"></dd>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
