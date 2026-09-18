<link rel="stylesheet" href="{{ asset('css/my-day-diary.css') }}?v={{ file_exists(public_path('css/my-day-diary.css')) ? filemtime(public_path('css/my-day-diary.css')) : time() }}">

<div class="my-day-diary" id="my-day-file-time"
     data-diary-url="{{ route('dashboard.my-day.diary') }}"
     data-log-url="{{ route('dashboard.my-day.file-time.log') }}"
     data-copy-url="{{ route('dashboard.my-day.copy-summary') }}"
     data-save-url="{{ route('dashboard.my-day.copy-summary.save') }}"
     data-search-url="{{ route('dashboard.my-day.record-search') }}"
     data-session-update-base="{{ url('/dashboard/my-day/sessions') }}"
     data-csrf="{{ csrf_token() }}">
    <div class="my-day-diary-header">
        <div>
            <h3 class="my-day-diary-title">My day diary</h3>
            <p class="my-day-diary-sub">Hours in CRM: <strong data-hours-label>—</strong></p>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" id="myDayDiaryLogBtn">+ Log minutes</button>
    </div>

    <section class="my-day-diary-section">
        <h4>Already in CRM</h4>
        <ul class="my-day-diary-list" data-crm-list></ul>
        <p class="my-day-diary-more d-none" data-crm-more></p>
    </section>

    <div class="row">
        <div class="col-md-6">
            <section class="my-day-diary-section my-day-auto">
                <h4>Time on files (auto)</h4>
                <ul class="my-day-diary-list" data-auto-list></ul>
            </section>
        </div>
        <div class="col-md-6">
            <section class="my-day-diary-section my-day-opened">
                <h4>Files opened</h4>
                <ul class="my-day-diary-list" data-opened-list></ul>
            </section>
        </div>
    </div>

    <details class="my-day-diary-section my-day-manual">
        <summary>End of day — manual logs &amp; copy summary</summary>
        <ul class="my-day-diary-list mt-2" data-manual-list></ul>
        <pre class="my-day-diary-summary" data-copy-text></pre>
        <p class="my-day-diary-saved-status text-muted mb-2" data-saved-status></p>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-sm btn-primary" id="myDayDiaryCopyBtn">Copy summary</button>
            <button type="button" class="btn btn-sm btn-outline-primary" id="myDayDiarySaveBtn">Save for admin</button>
        </div>
    </details>
</div>

<dialog id="myDayDiaryLogDialog" class="my-day-diary-dialog">
    <form method="dialog" id="myDayDiaryLogForm">
        <h4>Log off-CRM minutes</h4>
        <label class="d-block mb-2">
            Kind
            <select name="kind" class="form-control" required>
                <option value="call">Call</option>
                <option value="email">Email</option>
                <option value="in_person">In-Person</option>
                <option value="others">Others</option>
                <option value="attention">Attention</option>
                <option value="sms">SMS</option>
                <option value="provider_portal">Provider portal</option>
                <option value="draft">Drafting</option>
                <option value="internal">Internal</option>
                <option value="staff_meeting">Staff meeting</option>
            </select>
        </label>
        <label class="d-block mb-2">
            Title
            <input type="text" name="title" class="form-control" maxlength="255" required>
        </label>
        <label class="d-block mb-2">
            Minutes
            <input type="number" name="confirmed_minutes" class="form-control" min="1" max="480" required>
        </label>
        <label class="d-block mb-2">
            <input type="checkbox" name="admin" value="1"> Admin / no file
        </label>
        <label class="d-block mb-2" data-record-search-wrap>
            Record search
            <input type="search" name="record_q" class="form-control" placeholder="Student, application, college…" autocomplete="off">
            <input type="hidden" name="record_type">
            <input type="hidden" name="record_id">
            <input type="hidden" name="application_id">
            <div class="my-day-diary-search-results" data-search-results></div>
        </label>
        <div class="d-flex gap-2 justify-content-end mt-3">
            <button type="submit" value="cancel" formnovalidate class="btn btn-sm btn-light">Cancel</button>
            <button type="submit" value="save" class="btn btn-sm btn-primary">Save</button>
        </div>
    </form>
</dialog>

<dialog id="myDayAutoEventsDialog" class="my-day-diary-dialog my-day-auto-events-dialog">
    <div class="my-day-auto-events-header">
        <h4 id="myDayAutoEventsTitle">Activities on this file</h4>
        <button type="button" class="btn btn-sm btn-light" data-auto-events-close>Close</button>
    </div>
    <p class="text-muted mb-2" data-auto-events-ref></p>
    <div class="my-day-auto-events-list" data-auto-events-list></div>
</dialog>

<script defer src="{{ asset('js/my-day/dashboard-diary.js') }}?v={{ file_exists(public_path('js/my-day/dashboard-diary.js')) ? filemtime(public_path('js/my-day/dashboard-diary.js')) : time() }}"></script>
