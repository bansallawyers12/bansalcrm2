{{-- Schedule Follow-up — opened from client detail “Add follow-up” icon --}}
<style>
#scheduleFollowupModal .modal-header.schedule-followup-header {
	background: linear-gradient(135deg, #1e3a8a 0%, #312e81 100%);
	color: #fff;
	border-bottom: none;
}
#scheduleFollowupModal .schedule-followup-header .btn-close { filter: invert(1); opacity: .85; }
#scheduleFollowupModal .schedule-followup-header .modal-title { font-weight: 600; font-size: 1.05rem; }
#scheduleFollowupModal .schedule-field-label { font-weight: 600; font-size: .82rem; margin-bottom: .35rem; color: #334155; }
#scheduleFollowupModal .modal-content {
	border: none;
	border-radius: 16px;
	overflow: hidden;
	box-shadow: 0 25px 50px -12px rgba(30, 27, 75, 0.28), 0 0 0 1px rgba(148, 163, 184, 0.2);
}
#scheduleFollowupModal .modal-body {
	background: linear-gradient(165deg, #eef2ff 0%, #f8fafc 28%, #f1f5f9 100%);
	padding: 1.1rem 1.35rem 1.35rem;
}
#scheduleFollowupModal .modal-footer {
	background: #fff;
	border-top: 1px solid rgba(148, 163, 184, 0.28);
	padding: 1rem 1.5rem;
}
#scheduleFollowupModal .schedule-followup-section-card {
	background: #fff;
	border-radius: 14px;
	border: 1px solid rgba(148, 163, 184, 0.35);
	box-shadow: 0 10px 40px -10px rgba(30, 58, 138, 0.12), 0 4px 12px -4px rgba(15, 23, 42, 0.08);
	overflow: hidden;
	height: 100%;
	display: flex;
	flex-direction: column;
}
#scheduleFollowupModal .schedule-followup-section-card--date {
	overflow: visible;
}
#scheduleFollowupModal .schedule-section-card-head {
	background: linear-gradient(135deg, rgba(99, 102, 241, 0.14) 0%, rgba(124, 58, 237, 0.1) 50%, rgba(59, 130, 246, 0.08) 100%);
	padding: 11px 14px;
	min-height: 46px;
	border-bottom: 1px solid rgba(148, 163, 184, 0.3);
	display: flex;
	align-items: center;
	justify-content: flex-start;
	gap: 10px;
	flex-wrap: nowrap;
	box-sizing: border-box;
}
#scheduleFollowupModal .schedule-section-card-title {
	font-weight: 700;
	font-size: 0.88rem;
	color: #1e293b;
	display: inline-flex;
	align-items: center;
	gap: 8px;
}
#scheduleFollowupModal .schedule-section-card-title i {
	color: #4f46e5;
	opacity: 0.95;
	font-size: 1rem;
}
#scheduleFollowupModal .schedule-section-card-body {
	flex: 1;
	padding: 12px 14px 14px;
	display: flex;
	flex-direction: column;
	min-height: 0;
}
#scheduleFollowupModal .schedule-followup-section-card--date .schedule-section-card-body {
	padding: 6px 10px 14px;
	background: transparent;
	overflow-x: visible;
	min-width: 0;
}
#scheduleFollowupModal .schedule-client-reference-wrap {
	max-width: 15rem;
	width: 100%;
}
#scheduleFollowupModal .schedule-choice-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
	gap: 10px;
}
#scheduleFollowupModal .schedule-consultant-grid {
	grid-template-columns: repeat(auto-fit, minmax(136px, 1fr));
	max-width: 640px;
	margin-left: 0;
	margin-right: auto;
	gap: 10px;
}
#scheduleFollowupModal .schedule-consultant-grid .schedule-choice-face {
	min-height: 0;
	padding: 8px 10px;
}
#scheduleFollowupModal .schedule-consultant-grid .schedule-choice-meta {
	font-size: 0.68rem;
	margin-top: 3px;
}
#scheduleFollowupModal .schedule-type-service-strip {
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	gap: 12px;
}
#scheduleFollowupModal .schedule-duo-field {
	display: flex;
	flex-direction: column;
	gap: 0.4rem;
	min-width: 0;
}
#scheduleFollowupModal .schedule-duo-field > .schedule-field-label,
#scheduleFollowupModal .schedule-duo-field > label.schedule-field-label {
	margin-bottom: 0;
}
#scheduleFollowupModal .schedule-followup-type-select-wrap {
	min-width: 12rem;
	max-width: 100%;
	width: 12rem;
}
#scheduleFollowupModal .schedule-duo-select {
	border-radius: 10px !important;
	border: 2px solid #e2e8f0 !important;
	padding: 0.55rem 0.85rem !important;
	font-weight: 600 !important;
	font-size: 0.875rem !important;
	color: #0f172a !important;
	background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%) !important;
	box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06), inset 0 1px 0 rgba(255, 255, 255, 0.9) !important;
	transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
}
#scheduleFollowupModal .schedule-duo-select:focus {
	border-color: #818cf8 !important;
	box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.22) !important;
	outline: none !important;
}
#scheduleFollowupModal .schedule-service-segment-wrap {
	width: 14rem;
	max-width: 100%;
}
#scheduleFollowupModal .schedule-service-option {
	position: relative;
	margin: 0;
	cursor: pointer;
	display: block;
}
#scheduleFollowupModal .schedule-service-option input {
	position: absolute;
	opacity: 0;
	width: 0;
	height: 0;
	pointer-events: none;
}
#scheduleFollowupModal .schedule-service-option-face {
	display: flex;
	flex-direction: column;
	justify-content: center;
	gap: 2px;
	min-height: calc(1.5em + 1.1rem + 4px);
	padding: 0.55rem 0.85rem;
	border-radius: 10px;
	border: 2px solid #e2e8f0;
	background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
	box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06), inset 0 1px 0 rgba(255, 255, 255, 0.9);
	transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
}
#scheduleFollowupModal .schedule-service-option-title {
	font-weight: 700;
	font-size: 0.875rem;
	color: #0f172a;
	line-height: 1.25;
}
#scheduleFollowupModal .schedule-service-option-sub {
	font-size: 0.72rem;
	font-weight: 600;
	color: #64748b;
	letter-spacing: 0.02em;
}
#scheduleFollowupModal .schedule-service-option input:focus-visible + .schedule-service-option-face {
	outline: 2px solid #6366f1;
	outline-offset: 2px;
}
#scheduleFollowupModal .schedule-service-option input:checked + .schedule-service-option-face {
	border-color: #6366f1;
	box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2), 0 4px 14px rgba(79, 70, 229, 0.12);
	background: linear-gradient(180deg, #eef2ff 0%, #ffffff 55%);
}
#scheduleFollowupModal .schedule-choice-card {
	position: relative;
	margin: 0;
	cursor: pointer;
}
#scheduleFollowupModal .schedule-choice-card input {
	position: absolute;
	opacity: 0;
	pointer-events: none;
}
#scheduleFollowupModal .schedule-choice-face {
	display: block;
	border: 2px solid #e2e8f0;
	border-radius: 10px;
	padding: 10px 10px 8px;
	background: #fff;
	transition: border-color .15s ease, box-shadow .15s ease;
	min-height: 72px;
}
#scheduleFollowupModal .schedule-choice-card input:focus-visible + .schedule-choice-face {
	outline: 2px solid #6366f1;
	outline-offset: 2px;
}
#scheduleFollowupModal .schedule-choice-card input:checked + .schedule-choice-face {
	border-color: #6366f1;
	box-shadow: 0 0 0 1px rgba(99, 102, 241, .25);
	background: #f8fafc;
}
#scheduleFollowupModal .schedule-choice-title { font-weight: 700; font-size: .85rem; color: #0f172a; display: block; }
#scheduleFollowupModal .schedule-choice-meta { font-size: .72rem; color: #64748b; margin-top: 2px; display: block; }
#scheduleFollowupModal .schedule-followup-flatpickr-wrap {
	position: relative;
	min-height: auto;
	min-width: 0;
	border: none;
	border-radius: 0;
	padding: 4px 0 8px;
	background: transparent;
	box-shadow: none;
	overflow: visible;
	display: flex;
	justify-content: center;
	align-items: flex-start;
}
#scheduleFollowupModal .schedule-followup-flatpickr-wrap > input.flatpickr-input {
	position: absolute !important;
	width: 1px !important;
	height: 1px !important;
	padding: 0 !important;
	margin: 0 !important;
	opacity: 0 !important;
	pointer-events: none !important;
	border: none !important;
	left: 0;
	top: 0;
	overflow: hidden;
	clip: rect(0, 0, 0, 0);
}
#scheduleFollowupModal .schedule-followup-flatpickr-wrap .flatpickr-calendar.inline {
	box-shadow: none !important;
	border: none !important;
	background: transparent !important;
	width: 100% !important;
	max-width: none !important;
	min-width: 296px;
	margin: 0 auto;
	font-family: inherit;
}
#scheduleFollowupModal .schedule-followup-flatpickr-wrap .flatpickr-months {
	padding: 0 4px 10px;
	align-items: center;
}
#scheduleFollowupModal .schedule-followup-flatpickr-wrap .flatpickr-current-month {
	font-size: 1rem;
	font-weight: 600;
	color: #0f172a;
	padding: 4px 0;
}
#scheduleFollowupModal .schedule-followup-flatpickr-wrap .flatpickr-prev-month,
#scheduleFollowupModal .schedule-followup-flatpickr-wrap .flatpickr-next-month {
	top: 10px;
	fill: #475569;
	width: 34px;
	height: 34px;
	padding: 0;
	line-height: 34px;
	border-radius: 8px;
}
#scheduleFollowupModal .schedule-followup-flatpickr-wrap .flatpickr-prev-month:hover,
#scheduleFollowupModal .schedule-followup-flatpickr-wrap .flatpickr-next-month:hover {
	background: #f1f5f9;
}
#scheduleFollowupModal .schedule-followup-flatpickr-wrap .flatpickr-weekdays {
	padding-bottom: 4px;
	background: transparent;
	border-bottom: none;
}
#scheduleFollowupModal .schedule-followup-flatpickr-wrap span.flatpickr-weekday {
	font-size: 0.72rem;
	font-weight: 600;
	color: #64748b;
	text-transform: capitalize;
}
#scheduleFollowupModal .schedule-followup-flatpickr-wrap .flatpickr-days {
	border-top: none;
}
#scheduleFollowupModal .schedule-followup-flatpickr-wrap .dayContainer {
	width: 100%;
	min-width: 100%;
	max-width: 100%;
}
#scheduleFollowupModal .schedule-followup-flatpickr-wrap .flatpickr-day {
	border-radius: 999px;
	max-width: 100%;
	height: 34px;
	line-height: 34px;
	margin: 2px auto;
	border: none;
	color: #1e293b;
	font-weight: 500;
	box-shadow: none;
}
#scheduleFollowupModal .schedule-followup-flatpickr-wrap .flatpickr-day.prevMonthDay,
#scheduleFollowupModal .schedule-followup-flatpickr-wrap .flatpickr-day.nextMonthDay {
	color: #cbd5e1 !important;
	font-weight: 400;
}
#scheduleFollowupModal .schedule-followup-flatpickr-wrap .flatpickr-day.selected,
#scheduleFollowupModal .schedule-followup-flatpickr-wrap .flatpickr-day.selected:focus {
	background: #2563eb !important;
	border-color: #2563eb !important;
	color: #fff !important;
	box-shadow: none;
}
#scheduleFollowupModal .schedule-followup-flatpickr-wrap .flatpickr-day.selected:hover {
	background: #1d4ed8 !important;
	border-color: #1d4ed8 !important;
	color: #fff !important;
}
#scheduleFollowupModal .schedule-followup-flatpickr-wrap .flatpickr-day.today:not(.selected) {
	border: 2px solid #93c5fd;
	background: transparent;
	color: #2563eb;
}
#scheduleFollowupModal .schedule-followup-flatpickr-wrap .flatpickr-day.flatpickr-disabled,
#scheduleFollowupModal .schedule-followup-flatpickr-wrap .flatpickr-day.disabled {
	color: #e2e8f0 !important;
	cursor: not-allowed;
}
#scheduleFollowupModal .schedule-followup-flatpickr-wrap .flatpickr-day:hover:not(.selected):not(.flatpickr-disabled):not(.disabled) {
	background: #f1f5f9;
}
#scheduleFollowupModal .schedule-followup-datetime-row {
	max-width: min(704px, 100%);
	width: 100%;
	margin-left: 0;
	margin-right: auto;
}
#scheduleFollowupModal .schedule-slots-panel {
	flex: 1;
	min-height: 248px;
	padding: 14px;
	border: 1px solid rgba(226, 232, 240, 0.95);
	border-radius: 12px;
	background: linear-gradient(180deg, #fafbff 0%, #ffffff 45%);
	box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9);
	display: flex;
	flex-direction: column;
}
#scheduleFollowupModal .schedule-slot-empty {
	text-align: center;
	padding: 36px 14px;
	color: #64748b;
	flex: 1;
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
}
#scheduleFollowupModal .schedule-slot-empty .schedule-slot-empty-icon-wrap {
	width: 56px;
	height: 56px;
	margin-bottom: 14px;
	border-radius: 50%;
	background: linear-gradient(135deg, #e0e7ff 0%, #dbeafe 100%);
	display: flex;
	align-items: center;
	justify-content: center;
	box-shadow: 0 4px 14px rgba(79, 70, 229, 0.18);
}
#scheduleFollowupModal .schedule-slot-empty .schedule-slot-empty-icon-wrap i {
	font-size: 1.35rem;
	color: #4f46e5;
	opacity: 1;
	margin: 0;
}
#scheduleFollowupModal .schedule-slot-empty .fw-semibold {
	color: #334155;
	font-size: 0.95rem;
}
#scheduleFollowupModal #scheduleSlotsButtons {
	display: grid;
	grid-template-columns: repeat(3, minmax(0, 1fr));
	gap: 8px;
	width: 100%;
	align-content: start;
}
#scheduleFollowupModal .schedule-slot-btn {
	font-size: .72rem;
	font-weight: 600;
	padding: 7px 6px;
	margin: 0;
	width: 100%;
	text-align: center;
	border-radius: 8px;
	border: 1px solid #e2e8f0;
	background: #fff;
	color: #334155;
	cursor: pointer;
	transition: background .15s ease, border-color .15s ease, box-shadow .15s ease, transform .1s ease;
	box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
}
#scheduleFollowupModal .schedule-slot-btn:hover {
	border-color: #a5b4fc;
	background: linear-gradient(180deg, #eef2ff 0%, #e0e7ff 100%);
	box-shadow: 0 2px 8px rgba(99, 102, 241, 0.15);
}
#scheduleFollowupModal .schedule-slot-btn.is-active {
	border-color: #6366f1;
	background: linear-gradient(135deg, #6366f1 0%, #7c3aed 100%);
	color: #fff;
	box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
}
#scheduleFollowupModal .schedule-followup-footer-actions .btn-schedule-followup {
	background: linear-gradient(135deg, #6366f1 0%, #7c3aed 100%);
	border: none;
	font-weight: 600;
}
#scheduleFollowupModal .schedule-followup-footer-actions .btn-schedule-followup:disabled { opacity: .65; }
#scheduleFollowupModal .schedule-followup-lower-fields {
	max-width: 460px;
	margin-left: 0;
	margin-right: auto;
}
#scheduleFollowupModal .schedule-followup-field-select {
	border-radius: 8px !important;
	border: 1px solid #cbd5e1 !important;
	font-weight: 500 !important;
	font-size: 0.875rem !important;
	padding: 0.45rem 0.75rem !important;
	background: #fff !important;
	box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05) !important;
	color: #0f172a !important;
	transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
}
#scheduleFollowupModal .schedule-followup-field-select:focus {
	border-color: #818cf8 !important;
	box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
	outline: none !important;
}
#scheduleFollowupModal .schedule-followup-lower-fields textarea.form-control {
	border-radius: 8px;
	border: 1px solid #cbd5e1;
	font-size: 0.875rem;
	box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
	resize: vertical;
	min-height: 5rem;
}
@media (max-width: 380px) {
	#scheduleFollowupModal #scheduleSlotsButtons {
		grid-template-columns: repeat(2, minmax(0, 1fr));
	}
}
</style>

<div class="modal fade custom_modal" id="scheduleFollowupModal" tabindex="-1" role="dialog" aria-labelledby="scheduleFollowupModalLabel" aria-hidden="true" data-bs-backdrop="static">
	<div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
		<div class="modal-content">
			<div class="modal-header schedule-followup-header">
				<h5 class="modal-title" id="scheduleFollowupModalLabel"><i class="far fa-calendar-alt me-2"></i>Schedule Follow-up</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div id="scheduleFollowupFormErrors" class="alert alert-danger py-2 px-3 small d-none" role="alert"></div>
				<form id="scheduleFollowupForm" autocomplete="off">
					@csrf
					<input type="hidden" name="client_id" id="schedule_fu_client_id" value="{{ base64_encode(convert_uuencode(@$fetchedData->id)) }}">

					<div class="row mb-3">
						<div class="col-12">
							<div class="schedule-client-reference-wrap">
								<label class="schedule-field-label" for="schedule_client_reference">Client Reference No <span class="text-danger">*</span></label>
								<input type="text" class="form-control" id="schedule_client_reference" name="client_reference" readonly value="{{ $fetchedData->client_id ?? '' }}">
							</div>
						</div>
					</div>

					<div class="schedule-type-service-strip mb-3">
						<div class="schedule-duo-field">
							<label class="schedule-field-label d-block" for="schedule_followup_type">Type <span class="text-danger">*</span></label>
							<div class="schedule-followup-type-select-wrap">
								<select class="form-control schedule-duo-select" id="schedule_followup_type" name="followup_type" required>
									<option value="Education" selected>Education</option>
								</select>
							</div>
						</div>
						<div class="schedule-duo-field">
							<span class="schedule-field-label d-block">Service <span class="text-danger">*</span></span>
							<div class="schedule-service-segment-wrap">
								<label class="schedule-service-option">
									<input type="radio" name="service" value="free" checked>
									<span class="schedule-service-option-face">
										<span class="schedule-service-option-title">Free Consultation</span>
										<span class="schedule-service-option-sub" id="schedule_service_duration_label">Select a consultant for duration</span>
									</span>
								</label>
							</div>
						</div>
					</div>

					<div class="mb-3">
						<span class="schedule-field-label d-block">Consultant <span class="text-danger">*</span></span>
						<div class="schedule-choice-grid schedule-consultant-grid">
							@forelse ($followupConsultants ?? [] as $fuConsultant)
								@php
									$consultDisplayName = preg_replace('/\s+Calendar$/u', '', $fuConsultant->name);
								@endphp
								<label class="schedule-choice-card">
									<input type="radio" name="consultant" value="{{ $fuConsultant->slug }}">
									<span class="schedule-choice-face">
										<span class="schedule-choice-title">{{ $consultDisplayName }}</span>
										<span class="schedule-choice-meta">Follow-up slots</span>
									</span>
								</label>
							@empty
								<p class="text-muted small mb-0">No consultants are configured yet.</p>
							@endforelse
						</div>
					</div>

					<div class="row gx-3 gy-3 mb-4 align-items-stretch schedule-followup-datetime-row">
						<div class="col-md-6">
							<div class="schedule-followup-section-card schedule-followup-section-card--date h-100">
								<div class="schedule-section-card-head">
									<span class="schedule-section-card-title"><i class="far fa-calendar-alt"></i><span>Select date</span></span>
								</div>
								<div class="schedule-section-card-body">
									<div id="scheduleFollowupFlatpickr" class="schedule-followup-flatpickr-wrap"></div>
								</div>
							</div>
							<input type="hidden" name="followup_date" id="schedule_followup_date_hidden" value="">
						</div>
						<div class="col-md-6">
							<div class="schedule-followup-section-card h-100">
								<div class="schedule-section-card-head">
									<span class="schedule-section-card-title"><i class="far fa-clock"></i><span>Available time slots</span></span>
								</div>
								<div class="schedule-section-card-body">
									<div class="schedule-slots-panel" id="scheduleSlotsPanel">
										<div class="schedule-slot-empty" id="scheduleSlotsEmpty">
											<div class="schedule-slot-empty-icon-wrap">
												<i class="far fa-clock"></i>
											</div>
											<div class="fw-semibold" id="scheduleSlotsEmptyTitle">Select a consultant</div>
											<div class="small mt-2 text-muted px-1" id="scheduleSlotsEmptyHint">Pick a consultant above, then choose a time for the selected date.</div>
										</div>
										<div id="scheduleSlotsButtons" class="d-none"></div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="schedule-followup-lower-fields mb-3">
						<div class="mb-3">
							<label class="schedule-field-label" for="schedule_followup_detail">Follow-up details <span class="text-danger">*</span></label>
							<select class="form-control schedule-followup-field-select" id="schedule_followup_detail" name="followup_detail" required>
								<option value="">Select</option>
								<option value="In-Person">In-Person</option>
								<option value="Phone call">Phone call</option>
							</select>
						</div>
						<div class="mb-3">
							<label class="schedule-field-label" for="schedule_preferred_language">Preferred Language <span class="text-danger">*</span></label>
							<select class="form-control schedule-followup-field-select" id="schedule_preferred_language" name="preferred_language" required>
								<option value="">Select</option>
								<option value="English">English</option>
								<option value="Hindi">Hindi</option>
								<option value="Punjabi">Punjabi</option>
							</select>
						</div>
						<div>
							<label class="schedule-field-label" for="schedule_details_of_enquiry">Details Of Enquiry <span class="text-danger">*</span></label>
							<textarea class="form-control" id="schedule_details_of_enquiry" name="details_of_enquiry" rows="3" required placeholder="Enter Details Of Enquiry"></textarea>
						</div>
					</div>

					<input type="hidden" name="followup_datetime" id="schedule_followup_datetime" value="">

				</form>
			</div>
			<div class="modal-footer schedule-followup-footer-actions flex-wrap gap-2 justify-content-between">
				<button type="button" class="btn btn-link text-muted p-0" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Cancel</button>
				<button type="button" class="btn btn-primary px-4 btn-schedule-followup" id="scheduleFollowupSubmitBtn">
					<i class="far fa-calendar-check me-2"></i>Schedule Follow-up
				</button>
			</div>
		</div>
	</div>
</div>
