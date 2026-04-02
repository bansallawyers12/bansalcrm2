{{-- Global search: quick / supervisor access without full page navigation --}}
<div class="modal fade" id="crmAccessRequestModal" tabindex="-1" aria-labelledby="crmAccessRequestModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="crmAccessRequestModalLabel">Request access</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p class="text-muted small mb-2" id="crm-access-record-label"></p>
				<div id="crm-access-modal-blocked" class="alert alert-warning d-none" role="alert"></div>
				<div id="crm-access-modal-form">
					<div class="mb-3">
						<label class="form-label" for="crm-access-office">Office</label>
						<select id="crm-access-office" class="form-select"></select>
					</div>
					<div class="mb-3">
						<label class="form-label" for="crm-access-reason">Reason for request</label>
						<select id="crm-access-reason" class="form-select"></select>
					</div>
					<div class="mb-3 d-none" id="crm-access-supervisor-note-wrap">
						<label class="form-label" for="crm-access-note">Note for supervisor</label>
						<textarea id="crm-access-note" class="form-control" rows="2" placeholder="Optional"></textarea>
					</div>
					<div id="crm-access-msg" class="small mt-2"></div>
				</div>
			</div>
			<div class="modal-footer flex-wrap justify-content-end gap-2">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="crm-access-btn-quick">
					Quick access (<span id="crm-access-quick-mins-label">{{ (int) config('crm_access.quick_grant_minutes', 15) }}</span> min)
				</button>
				<button type="button" class="btn btn-outline-primary d-none" id="crm-access-btn-supervisor">Request supervisor access</button>
			</div>
		</div>
	</div>
</div>
