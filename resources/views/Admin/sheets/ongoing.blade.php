@extends('layouts.admin')
@section('title', 'Ongoing Sheet')

@push('styles')
<style>
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
    
    /* Filter panel styling */
    .filter-panel {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            
            {{-- Top Bar: Title + Back Button --}}
            <div class="card-header">
                <h4><i class="fas fa-clipboard-list"></i> Ongoing Sheet</h4>
                <div class="card-header-action">
                    <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Clients
                    </a>
                </div>
            </div>
            
            {{-- Filter Bar --}}
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                        <button class="btn btn-primary mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterPanel" aria-expanded="{{ $activeFilterCount > 0 ? 'true' : 'false' }}" aria-controls="filterPanel">
                            <i class="fas fa-filter"></i> Filters
                            @if($activeFilterCount > 0)
                                <span class="badge badge-light">{{ $activeFilterCount }}</span>
                            @endif
                        </button>
                        
                        <div class="d-flex gap-2 mb-2">
                            @if($activeFilterCount > 0)
                                <a href="{{ route('clients.sheets.ongoing') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear Filters
                                </a>
                            @endif
                            
                            <select name="per_page" class="form-control" style="width: auto;" 
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
                        <div class="filter-panel">
                            <form method="get" action="{{ route('clients.sheets.ongoing') }}">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label>Office</label>
                                        <select name="office[]" class="form-control select2" multiple>
                                            @foreach($offices as $office)
                                                <option value="{{ $office->id }}" 
                                                    {{ in_array($office->id, (array)request('office', [])) ? 'selected' : '' }}>
                                                    {{ $office->office_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label>Visa Expiry From</label>
                                        <input type="text" name="visa_expiry_from" class="form-control dobdatepicker" 
                                               placeholder="DD/MM/YYYY" value="{{ request('visa_expiry_from') }}" autocomplete="off">
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label>Visa Expiry To</label>
                                        <input type="text" name="visa_expiry_to" class="form-control dobdatepicker" 
                                               placeholder="DD/MM/YYYY" value="{{ request('visa_expiry_to') }}" autocomplete="off">
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label>Search (Name, CRM Ref, Current Status)</label>
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Search..." value="{{ request('search') }}">
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Apply Filters
                                        </button>
                                        <a href="{{ route('clients.sheets.ongoing') }}" class="btn btn-secondary">
                                            <i class="fas fa-redo"></i> Reset
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
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <thead>
                                <tr class="ongoing-sheet-header">
                                    <th>CRM Reference</th>
                                    <th>Client Name</th>
                                    <th>Date of Birth</th>
                                    <th>Payment Received</th>
                                    <th>Institute</th>
                                    <th>Visa Expiry Date</th>
                                    <th>Visa Category</th>
                                    <th>Current Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($rows->isEmpty())
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-info-circle fa-2x text-muted mb-2 d-block"></i>
                                            <p class="mb-0">No ongoing records found.</p>
                                        </td>
                                    </tr>
                                @else
                                    @foreach($rows as $row)
                                        <tr onclick="window.location.href='{{ route('clients.detail', ['id' => base64_encode(convert_uuencode($row->client_id))]) }}'">
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
                                            <td class="status-cell">{{ $row->current_status ?? '—' }}</td>
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
    
    // Bootstrap 5 collapse should work automatically with data-bs-toggle
    // No additional JavaScript needed
});
</script>
@endpush
