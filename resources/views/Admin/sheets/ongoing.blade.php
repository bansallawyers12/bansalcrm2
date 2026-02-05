@extends('layouts.admin')
@section('title', 'Ongoing Sheet')

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
    
    /* Ongoing sheet table: wider, horizontal scroll */
    .ongoing-sheet-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .ongoing-sheet-table-wrap .table {
        min-width: 1400px;
        table-layout: auto;
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
                <h4><i class="fas fa-clipboard-list"></i> Ongoing Sheet</h4>
            </div>
            
            {{-- Filter Bar --}}
            <div class="card ongoing-filter-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <button class="ongoing-filter-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#filterPanel" aria-expanded="{{ $activeFilterCount > 0 ? 'true' : 'false' }}" aria-controls="filterPanel">
                            <i class="fas fa-filter"></i>
                            <span>Filters</span>
                            @if($activeFilterCount > 0)
                                <span class="badge">{{ $activeFilterCount }}</span>
                            @endif
                        </button>
                        
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            @if($activeFilterCount > 0)
                                <a href="{{ route('clients.sheets.ongoing', ['clear_filters' => 1]) }}" class="btn-ongoing-reset text-decoration-none">
                                    <i class="fas fa-times me-1"></i> Clear Filters
                                </a>
                            @endif
                            
                            <select name="per_page" class="form-control ongoing-per-page-select" 
                                    onchange="window.location.href='{{ route('clients.sheets.ongoing') }}?per_page=' + this.value + '{{ request()->except('page', 'per_page') ? '&' . http_build_query(request()->except('page', 'per_page')) : '' }}';">
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
                            <form method="get" action="{{ route('clients.sheets.ongoing') }}">
                                <input type="hidden" name="per_page" value="{{ $perPage }}">
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
                                    
                                    <div class="col-12">
                                        <label class="form-label">Search (Name, CRM Ref, Current Stage)</label>
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Search by name, CRM reference or current stage..." value="{{ request('search') }}">
                                    </div>
                                    
                                    <div class="col-12 ongoing-filter-actions">
                                        <button type="submit" class="btn-ongoing-apply">
                                            <i class="fas fa-search me-1"></i> Apply Filters
                                        </button>
                                        <a href="{{ route('clients.sheets.ongoing', ['clear_filters' => 1]) }}" class="btn-ongoing-reset text-decoration-none">
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
                    <div class="table-responsive ongoing-sheet-table-wrap">
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
                                </tr>
                            </thead>
                            <tbody>
                                @if($rows->isEmpty())
                                    <tr>
                                        <td colspan="12" class="text-center py-4">
                                            <i class="fas fa-info-circle fa-2x text-muted mb-2 d-block"></i>
                                            <p class="mb-0">No ongoing records found.</p>
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
                                            <td>{{ trim(($row->assignee_first_name ?? '') . ' ' . ($row->assignee_last_name ?? '')) ?: '—' }}</td>
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
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize flatpickr for date inputs
    flatpickr('.dobdatepicker', {
        dateFormat: 'd/m/Y',
        allowInput: true,
        clickOpens: true
    });
    
    // Initialize Select2 for office filter
    $('.select2').select2({
        placeholder: 'Select offices',
        allowClear: true,
        width: '100%'
    });
    
    // Sheet comment: open modal
    $(document).on('click', '.sheet-comment-edit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var appId = $(this).data('app-id');
        var comment = $(this).data('comment') || '';
        $('#sheet_comment_application_id').val(appId);
        $('#sheet_comment_text').val(comment);
        $('#sheetCommentModal').modal('show');
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
                $('#sheetCommentModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to save comment.');
            },
            complete: function() { $btn.prop('disabled', false); }
        });
    });
});
</script>
@endpush
