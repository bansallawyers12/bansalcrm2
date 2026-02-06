@extends('layouts.admin')
@section('title', $sheetTitle ?? 'Ongoing Sheet')

@push('styles')
<style>
    /* Page title header - distinct background and colour */
    .ongoing-sheet-page-header {
        background: linear-gradient(135deg, #5b4d96 0%, #6f5fb8 50%, #5b4d96 100%);
        color: #fff;
        padding: 1rem 1.25rem;
        border-radius: 10px;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(91, 77, 150, 0.25);
    }
    .ongoing-sheet-page-header h4 {
        margin: 0;
        font-weight: 600;
        color: #fff;
        font-size: 1.35rem;
    }
    .ongoing-sheet-page-header h4 i {
        margin-right: 0.5rem;
        opacity: 0.95;
    }
    /* Header row - light blue */
    .ongoing-sheet-header {
        background: linear-gradient(135deg, #cfe2ff 0%, #b8daff 100%);
        font-weight: 600;
        text-align: center;
        border: 1px solid #9ec5fe;
    }
    
    /* Hover effect for rows */
    tbody tr:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    
    /* Current status column - allow text wrapping */
    .status-cell {
        max-width: 300px;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    
    /* Filter section design */
    .ongoing-filter-card {
        border: 1px solid #e3e6f0;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }
    .ongoing-filter-card .card-body {
        padding: 1rem 1.25rem;
    }
    .ongoing-filter-toggle {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        font-weight: 600;
        border-radius: 8px;
        background: linear-gradient(135deg, #5b4d96 0%, #6f5fb8 100%);
        color: #fff;
        border: none;
        transition: box-shadow 0.2s, transform 0.1s;
    }
    .ongoing-filter-toggle:hover {
        color: #fff;
        box-shadow: 0 4px 12px rgba(91, 77, 150, 0.35);
    }
    .ongoing-filter-toggle .badge {
        background: rgba(255,255,255,0.25);
        color: #fff;
        font-size: 0.75rem;
    }
    .ongoing-filter-panel {
        background: linear-gradient(180deg, #fafbfc 0%, #f4f6f9 100%);
        border-top: 1px solid #e3e6f0;
        padding: 1.25rem 1.5rem;
        border-radius: 0 0 10px 10px;
    }
    .ongoing-filter-panel .form-label {
        font-weight: 600;
        color: #374151;
        font-size: 0.8125rem;
        margin-bottom: 0.35rem;
    }
    .ongoing-filter-panel .form-control,
    .ongoing-filter-panel .select2-container .select2-selection {
        border-radius: 6px;
        border: 1px solid #d1d5db;
    }
    .ongoing-filter-panel .form-control:focus {
        border-color: #6f5fb8;
        box-shadow: 0 0 0 3px rgba(111, 95, 184, 0.15);
    }
    .ongoing-filter-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
        padding-top: 0.25rem;
    }
    .btn-ongoing-apply {
        background: linear-gradient(135deg, #5b4d96 0%, #6f5fb8 100%);
        color: #fff;
        border: none;
        padding: 0.5rem 1.25rem;
        border-radius: 6px;
        font-weight: 600;
        transition: box-shadow 0.2s;
    }
    .btn-ongoing-apply:hover {
        color: #fff;
        box-shadow: 0 4px 12px rgba(91, 77, 150, 0.35);
    }
    .btn-ongoing-reset {
        background: #fff;
        color: #4b5563;
        border: 1px solid #d1d5db;
        padding: 0.5rem 1.25rem;
        border-radius: 6px;
        font-weight: 500;
        transition: background 0.2s, border-color 0.2s;
    }
    .btn-ongoing-reset:hover {
        background: #f9fafb;
        border-color: #9ca3af;
        color: #374151;
    }
    .ongoing-per-page-select {
        min-width: 120px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        padding: 0.35rem 0.75rem;
        font-size: 0.875rem;
    }
    .ongoing-assignee-select {
        min-width: 160px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        padding: 0.35rem 0.75rem;
        font-size: 0.875rem;
        height: auto;
    }
    
    /* Ongoing sheet table: wider, horizontal scroll (matches MigrationManager2 pattern) */
    .ongoing-sheet-table-container {
        position: relative;
    }
    .ongoing-sheet-table-wrap {
        position: relative;
        overflow-x: auto;
        overflow-y: visible;
        -webkit-overflow-scrolling: touch;
    }
    .ongoing-sheet-table-wrap .table {
        min-width: 1400px;
        table-layout: auto;
    }
    /* Scroll indicators (left/right gradient overlays when content is scrollable) */
    .ongoing-sheet-scroll-indicator {
        position: absolute;
        top: 0;
        bottom: 20px;
        width: 40px;
        pointer-events: none;
        z-index: 10;
        transition: opacity 0.3s;
    }
    .ongoing-sheet-scroll-indicator-left {
        left: 0;
        background: linear-gradient(to right, rgba(255,255,255,0.95), transparent);
        opacity: 0;
    }
    .ongoing-sheet-scroll-indicator-right {
        right: 0;
        background: linear-gradient(to left, rgba(255,255,255,0.95), transparent);
    }
    .ongoing-sheet-scroll-indicator-left.visible,
    .ongoing-sheet-scroll-indicator-right.visible {
        opacity: 1;
    }
    /* Custom scrollbar styling */
    .ongoing-sheet-table-wrap::-webkit-scrollbar {
        height: 12px;
    }
    .ongoing-sheet-table-wrap::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .ongoing-sheet-table-wrap::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #5b4d96 0%, #6f5fb8 100%);
        border-radius: 10px;
    }
    .ongoing-sheet-table-wrap::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #6f5fb8 0%, #5b4d96 100%);
    }
    /* Scroll hint */
    .ongoing-sheet-scroll-hint {
        text-align: center;
        padding: 10px;
        background: #f4f1fa;
        border-radius: 5px;
        margin-bottom: 10px;
        font-size: 13px;
        color: #4a4063;
    }
    .ongoing-sheet-scroll-hint i {
        margin-right: 6px;
    }
    .ongoing-sheet-table-wrap .table th.branch-cell,
    .ongoing-sheet-table-wrap .table td.branch-cell {
        min-width: 180px;
        max-width: 280px;
    }
    
    /* Course name column: dark blue / black text */
    .ongoing-course-cell {
        font-weight: 500;
    }
    .ongoing-course-link {
        color: #1e3a5f !important;
        text-decoration: none;
    }
    .ongoing-course-link:hover {
        color: #0f172a !important;
        text-decoration: underline;
    }
    
    .comment-cell {
        max-width: 220px;
        min-width: 120px;
    }
    .checklist-status-cell .checklist-status-select {
        min-width: 140px;
        max-width: 180px;
    }
    .comment-cell .sheet-comment-text {
        display: inline-block;
        max-height: 3.6em;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: pre-wrap;
        word-break: break-word;
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            
            {{-- Page title header --}}
            <div class="ongoing-sheet-page-header">
                <h4><i class="fas fa-clipboard-list"></i> {{ $sheetTitle ?? 'Ongoing Sheet' }}</h4>
            </div>
            
            {{-- Filter Bar --}}
            <div class="card ongoing-filter-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <button class="ongoing-filter-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#filterPanel" aria-expanded="{{ $activeFilterCount > 0 ? 'true' : 'false' }}" aria-controls="filterPanel">
                                <i class="fas fa-filter"></i>
                                <span>Filters</span>
                                @if($activeFilterCount > 0)
                                    <span class="badge">{{ $activeFilterCount }}</span>
                                @endif
                            </button>
                            <label class="mb-0 d-flex align-items-center gap-1">
                                <span class="text-nowrap text-muted small">Assignee</span>
                                <select id="ongoing-assignee-bar" class="form-control ongoing-assignee-select" aria-label="Assignee">
                                    <option value="all" {{ request('assignee') === 'all' || request('assignee') === '' ? 'selected' : '' }}>All</option>
                                    @foreach($assignees as $a)
                                        @php $displayName = trim(($a->first_name ?? '') . ' ' . ($a->last_name ?? '')) ?: ($a->email ?? '—'); @endphp
                                        <option value="{{ $a->id }}" {{ request('assignee') == $a->id ? 'selected' : '' }}>{{ $displayName }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                        
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            @if($activeFilterCount > 0)
                                <a href="{{ route($sheetRoute ?? 'clients.sheets.ongoing', ['clear_filters' => 1]) }}" class="btn-ongoing-reset text-decoration-none">
                                    <i class="fas fa-times me-1"></i> Clear Filters
                                </a>
                            @endif
                            
                            <select name="per_page" class="form-control ongoing-per-page-select" 
                                    onchange="window.location.href='{{ route($sheetRoute ?? 'clients.sheets.ongoing') }}?per_page=' + this.value + '{{ request()->except('page', 'per_page') ? '&' . http_build_query(request()->except('page', 'per_page')) : '' }}';">
                                @foreach([10, 25, 50, 100, 200] as $option)
                                    <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>
                                        {{ $option }} per page
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    {{-- Filter Panel (Collapsible) --}}
                    <div class="collapse {{ $activeFilterCount > 0 ? 'show' : '' }}" id="filterPanel">
                        <div class="ongoing-filter-panel">
                            <form method="get" action="{{ route($sheetRoute ?? 'clients.sheets.ongoing') }}">
                                <input type="hidden" name="per_page" value="{{ $perPage }}">
                                <input type="hidden" name="assignee" value="{{ request('assignee') }}">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Office</label>
                                        <select name="office[]" class="form-control select2" multiple>
                                            @foreach($offices as $office)
                                                <option value="{{ $office->id }}" 
                                                    {{ in_array($office->id, (array)request('office', [])) ? 'selected' : '' }}>
                                                    {{ $office->office_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label class="form-label">Visa Expiry From</label>
                                        <input type="text" name="visa_expiry_from" class="form-control dobdatepicker" 
                                               placeholder="DD/MM/YYYY" value="{{ request('visa_expiry_from') }}" autocomplete="off">
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label class="form-label">Visa Expiry To</label>
                                        <input type="text" name="visa_expiry_to" class="form-control dobdatepicker" 
                                               placeholder="DD/MM/YYYY" value="{{ request('visa_expiry_to') }}" autocomplete="off">
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label class="form-label">Branch</label>
                                        <select name="branch" class="form-control select2-single">
                                            <option value="">All branches</option>
                                            @foreach($branches as $b)
                                                <option value="{{ $b->id }}" {{ request('branch') == $b->id ? 'selected' : '' }}>
                                                    {{ $b->office_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label class="form-label">Current Stage</label>
                                        <select name="current_stage" class="form-control select2-single">
                                            <option value="">All stages</option>
                                            @foreach($currentStages as $value => $label)
                                                <option value="{{ $value }}" {{ request('current_stage') == $value ? 'selected' : '' }}>
                                                    {{ $label ?: '—' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label class="form-label">Search (Name, CRM Ref, Current Stage)</label>
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Search by name, CRM reference or current stage..." value="{{ request('search') }}">
                                    </div>
                                    
                                    <div class="col-12 ongoing-filter-actions">
                                        <button type="submit" class="btn-ongoing-apply">
                                            <i class="fas fa-search me-1"></i> Apply Filters
                                        </button>
                                        <a href="{{ route($sheetRoute ?? 'clients.sheets.ongoing', ['clear_filters' => 1]) }}" class="btn-ongoing-reset text-decoration-none">
                                            <i class="fas fa-redo me-1"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Table --}}
            <div class="card">
                <div class="card-body p-0">
                    {{-- Scroll hint --}}
                    <div class="ongoing-sheet-scroll-hint px-3 pt-2">
                        <i class="fas fa-arrows-alt-h"></i> Scroll horizontally to see all columns.
                    </div>
                    <div class="ongoing-sheet-table-container px-0">
                        <div class="ongoing-sheet-scroll-indicator ongoing-sheet-scroll-indicator-left"></div>
                        <div class="ongoing-sheet-scroll-indicator ongoing-sheet-scroll-indicator-right visible"></div>
                        <div class="table-responsive ongoing-sheet-table-wrap" id="ongoing-sheet-scroll-container">
                            <table class="table table-striped table-bordered mb-0">
                            <thead>
                                <tr class="ongoing-sheet-header">
                                    <th>Course Name</th>
                                    <th>CRM Reference</th>
                                    <th>Client Name</th>
                                    <th>Date of Birth</th>
                                    <th>Payment Received</th>
                                    <th>Institute</th>
                                    <th class="branch-cell">Branch</th>
                                    <th>Assignee</th>
                                    <th>Visa Expiry Date</th>
                                    <th>Visa Category</th>
                                    <th>Current Stage</th>
                                    <th>Comment</th>
                                    @if(isset($sheetType) && $sheetType === 'checklist')
                                    <th>Status</th>
                                    <th>Checklist sent</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if($rows->isEmpty())
                                    <tr>
                                        <td colspan="{{ (isset($sheetType) && $sheetType === 'checklist') ? 14 : 12 }}" class="text-center py-4">
                                            <i class="fas fa-info-circle fa-2x text-muted mb-2 d-block"></i>
                                            <p class="mb-0">No records found.</p>
                                        </td>
                                    </tr>
                                @else
                                    @foreach($rows as $row)
                                        @php
                                            $clientEncodedId = base64_encode(convert_uuencode($row->client_id));
                                            $appDetailUrl = route('clients.detail.application', ['id' => $clientEncodedId, 'applicationId' => $row->application_id]);
                                        @endphp
                                        <tr onclick="window.location.href='{{ $appDetailUrl }}'">
                                            <td class="ongoing-course-cell">
                                                <a href="{{ $appDetailUrl }}" class="ongoing-course-link" onclick="event.stopPropagation();">{{ $row->course_name ?? '—' }}</a>
                                            </td>
                                            <td>{{ $row->crm_ref ?? '—' }}</td>
                                            <td>{{ trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')) ?: '—' }}</td>
                                            <td>{{ $row->dob ? \Carbon\Carbon::parse($row->dob)->format('d/m/Y') : '—' }}</td>
                                            <td>
                                                @if($row->payment_display_note)
                                                    {{ $row->payment_display_note }}
                                                @elseif($row->total_payment > 0)
                                                    ${{ number_format($row->total_payment, 2) }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>{{ $row->institute_override ?? $row->partner_name ?? $row->service_college ?? '—' }}</td>
                                            <td class="branch-cell">{{ $row->branch_name ?? '—' }}</td>
                                            <td onclick="event.stopPropagation();">
                                                <span class="ongoing-assignee-display">{{ trim(($row->assignee_first_name ?? '') . ' ' . ($row->assignee_last_name ?? '')) ?: '—' }}</span>
                                                <a href="javascript:;" class="ongoing-assignee-edit ms-1" data-app-id="{{ $row->application_id }}" data-assignee-id="{{ $row->assignee_id ?? '' }}" title="Change assignee"><i class="fas fa-edit text-muted small"></i></a>
                                            </td>
                                            <td>
                                                @if($row->visaexpiry && $row->visaexpiry != '0000-00-00')
                                                    {{ \Carbon\Carbon::parse($row->visaexpiry)->format('d/m/Y') }}
                                                    @if($row->visa_opt)
                                                        <span class="text-muted">({{ $row->visa_opt }})</span>
                                                    @endif
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>
                                                @if($row->visa_category_override)
                                                    {{ $row->visa_category_override }}
                                                @else
                                                    {{ trim(($row->visa_type ?? '') . ' ' . ($row->visa_opt ?? '')) ?: '—' }}
                                                @endif
                                            </td>
                                            <td class="status-cell">{{ $row->application_stage ?? '—' }}</td>
                                            <td class="comment-cell" onclick="event.stopPropagation();">
                                                <span class="sheet-comment-text">{{ $row->sheet_comment_text ?? '—' }}</span>
                                                <a href="javascript:;" class="sheet-comment-edit ms-1" data-app-id="{{ $row->application_id }}" data-comment="{{ e($row->sheet_comment_text ?? '') }}" title="Add/Edit comment"><i class="fas fa-edit text-muted small"></i></a>
                                            </td>
                                            @if(isset($sheetType) && $sheetType === 'checklist')
                                            <td class="checklist-status-cell" onclick="event.stopPropagation();">
                                                @php
                                                    $currentStatus = $row->checklist_sheet_status ?? 'active';
                                                    $statusLabels = ['active' => 'Active', 'convert_to_client' => 'Convert to client', 'discontinue' => 'Discontinue', 'hold' => 'Hold'];
                                                @endphp
                                                <select class="form-control form-control-sm checklist-status-select" data-app-id="{{ $row->application_id }}" title="Status">
                                                    @foreach($statusLabels as $val => $label)
                                                    <option value="{{ $val }}" {{ $currentStatus === $val ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="checklist-sent-cell" onclick="event.stopPropagation();">
                                                @php
                                                    $clientEncodedIdForResend = base64_encode(convert_uuencode($row->client_id));
                                                    $resendChecklistUrl = route('clients.detail', ['id' => $clientEncodedIdForResend]) . '?applicationId=' . $row->application_id . '&open_checklist_email=1';
                                                @endphp
                                                @if($row->checklist_sent_at)
                                                    {{ \Carbon\Carbon::parse($row->checklist_sent_at)->format('d/m/Y') }}
                                                    <br><a href="{{ $resendChecklistUrl }}" class="btn btn-sm btn-outline-secondary mt-1" onclick="event.stopPropagation();" title="Resend checklist email">Resend checklist</a>
                                                @else
                                                    Not sent
                                                    <br><a href="{{ $resendChecklistUrl }}" class="btn btn-sm btn-outline-primary mt-1" onclick="event.stopPropagation();" title="Send checklist email">Send checklist</a>
                                                @endif
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        </div>
                    </div>
                    
                    {{-- Pagination --}}
                    @if($rows->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    Showing {{ $rows->firstItem() ?? 0 }} to {{ $rows->lastItem() ?? 0 }} of {{ $rows->total() }} entries
                                </div>
                                <div>
                                    {{ $rows->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
        </div>
    </section>
</div>

{{-- Sheet comment modal --}}
<div class="modal fade" id="sheetCommentModal" tabindex="-1" aria-labelledby="sheetCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sheetCommentModalLabel">Sheet comment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="sheet_comment_application_id" value="">
                <p class="small text-muted">This comment replaces the previous one and appears in Notes &amp; Activity with course and college name.</p>
                <div class="mb-3">
                    <label for="sheet_comment_text" class="form-label">Comment</label>
                    <textarea class="form-control" id="sheet_comment_text" rows="4" maxlength="65535" placeholder="Enter comment..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="sheet_comment_save">Save</button>
            </div>
        </div>
    </div>
</div>

{{-- Change assignee modal --}}
<div class="modal fade" id="sheetChangeAssigneeModal" tabindex="-1" aria-labelledby="sheetChangeAssigneeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sheetChangeAssigneeModalLabel">Change assignee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="sheet_assignee_application_id" value="">
                <div class="mb-3">
                    <label for="sheet_assignee_select" class="form-label">Assignee</label>
                    <select class="form-control" id="sheet_assignee_select">
                        <option value="">Select assignee</option>
                        @foreach($assigneesForChangeModal ?? $assignees as $a)
                            <option value="{{ $a->id }}">{{ trim(($a->first_name ?? '') . ' ' . ($a->last_name ?? '')) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="sheet_assignee_save">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Horizontal scroll indicators (matches MigrationManager2 pattern)
    var $scrollContainer = $('#ongoing-sheet-scroll-container');
    var $leftIndicator = $('.ongoing-sheet-scroll-indicator-left');
    var $rightIndicator = $('.ongoing-sheet-scroll-indicator-right');
    function updateScrollIndicators() {
        if (!$scrollContainer.length || !$scrollContainer[0]) return;
        var scrollLeft = $scrollContainer.scrollLeft();
        var scrollWidth = $scrollContainer[0].scrollWidth;
        var clientWidth = $scrollContainer[0].clientWidth;
        var maxScroll = scrollWidth - clientWidth;
        $leftIndicator.toggleClass('visible', scrollLeft > 10);
        $rightIndicator.toggleClass('visible', scrollLeft < maxScroll - 10);
    }
    $scrollContainer.on('scroll', updateScrollIndicators);
    $(window).on('resize', updateScrollIndicators);
    setTimeout(updateScrollIndicators, 100);
    // Vertical mouse wheel scrolls horizontally when content overflows
    $scrollContainer.on('wheel', function(e) {
        if (e.originalEvent.deltaY !== 0 && !e.shiftKey) {
            var el = this;
            if (el.scrollWidth > el.clientWidth) {
                e.preventDefault();
                el.scrollLeft += e.originalEvent.deltaY;
            }
        }
    });

    // Initialize flatpickr for date inputs
    flatpickr('.dobdatepicker', {
        dateFormat: 'd/m/Y',
        allowInput: true,
        clickOpens: true
    });
    
    // Initialize Select2 for office filter (multiple)
    $('.select2').select2({
        placeholder: 'Select offices',
        allowClear: true,
        width: '100%'
    });
    // Initialize Select2 for single-select filters
    $('.select2-single').select2({
        placeholder: 'Select...',
        allowClear: true,
        width: '100%'
    });
    // Assignee in top bar: navigate on change, preserve other params
    $('#ongoing-assignee-bar').on('change', function() {
        var assignee = $(this).val();
        var params = new URLSearchParams(window.location.search);
        params.set('assignee', assignee || 'all');
        params.delete('page');
        window.location.href = '{{ route($sheetRoute ?? "clients.sheets.ongoing") }}?' + params.toString();
    });
    
    // Sheet comment: open modal. Bind directly to the link (not delegated) because the
    // td's onclick="event.stopPropagation()" prevents the event from bubbling to document.
    $('.sheet-comment-edit').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var appId = $(this).data('app-id');
        var comment = $(this).data('comment') || '';
        $('#sheet_comment_application_id').val(appId);
        $('#sheet_comment_text').val(comment);
        var modalEl = document.getElementById('sheetCommentModal');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        } else {
            $(modalEl).modal('show');
        }
    });

    // Change assignee: open modal. Direct binding (same as sheet-comment-edit) because
    // the td's onclick="event.stopPropagation()" prevents bubbling to document.
    $('.ongoing-assignee-edit').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var appId = $(this).data('app-id');
        var assigneeId = $(this).data('assignee-id') || '';
        $('#sheet_assignee_application_id').val(appId);
        $('#sheet_assignee_select').val(assigneeId);
        var modalEl = document.getElementById('sheetChangeAssigneeModal');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        } else {
            $(modalEl).modal('show');
        }
    });

    $('#sheet_assignee_save').on('click', function() {
        var appId = $('#sheet_assignee_application_id').val();
        var assigneeId = $('#sheet_assignee_select').val();
        if (!assigneeId) {
            alert('Please select an assignee.');
            return;
        }
        var $btn = $(this).prop('disabled', true);
        $.ajax({
            url: '{{ route("application.change-assignee") }}',
            method: 'POST',
            dataType: 'json',
            data: {
                _token: '{{ csrf_token() }}',
                application_id: parseInt(appId, 10) || appId,
                assignee_id: parseInt(assigneeId, 10) || assigneeId
            },
            success: function(res) {
                if (res && res.success) {
                    var modalEl = document.getElementById('sheetChangeAssigneeModal');
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                    } else {
                        $(modalEl).modal('hide');
                    }
                    location.reload();
                } else {
                    alert((res && res.message) || 'Failed to update assignee.');
                }
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to update assignee.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join(' ');
                }
                alert(msg);
            },
            complete: function() { $btn.prop('disabled', false); }
        });
    });

    $('#sheet_comment_save').on('click', function() {
        var appId = $('#sheet_comment_application_id').val();
        var comment = $('#sheet_comment_text').val().trim();
        if (!comment) {
            alert('Please enter a comment.');
            return;
        }
        var $btn = $(this).prop('disabled', true);
        $.ajax({
            url: '{{ route("clients.sheets.ongoing.sheet-comment") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                application_id: appId,
                comment: comment
            },
            success: function() {
                var modalEl = document.getElementById('sheetCommentModal');
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                } else {
                    $(modalEl).modal('hide');
                }
                location.reload();
            },
            error: function(xhr) {
                alert(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to save comment.');
            },
            complete: function() { $btn.prop('disabled', false); }
        });
    });

    // Checklist sheet: Status dropdown — update status; reload if row leaves sheet or Hold (reorder)
    $(document).on('change', '.checklist-status-select', function() {
        var $select = $(this);
        var appId = $select.data('app-id');
        var status = $select.val();
        var $selectParent = $select.closest('td');
        $select.prop('disabled', true);
        $.ajax({
            url: '{{ route("clients.sheets.checklist.update-status") }}',
            method: 'POST',
            dataType: 'json',
            data: {
                _token: '{{ csrf_token() }}',
                application_id: parseInt(appId, 10),
                status: status
            },
            success: function(res) {
                if (res && res.success) {
                    if (res.data && res.data.leaves_sheet) {
                        location.reload();
                    } else if (status === 'hold') {
                        location.reload();
                    }
                } else {
                    alert((res && res.message) || 'Failed to update status.');
                    $select.val($select.data('previous') || 'active');
                }
                $select.prop('disabled', false);
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to update status.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join(' ');
                }
                alert(msg);
                $select.val($select.data('previous') || 'active');
                $select.prop('disabled', false);
            }
        });
    });
    $(document).on('focus', '.checklist-status-select', function() {
        $(this).data('previous', $(this).val());
    });
});
</script>
@endpush
