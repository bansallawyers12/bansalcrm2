@extends('layouts.adminconsole')
@section('title', 'Grants Dashboard')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            @include('../Elements/flash-message')

            <style>
                .grants-dash-card .card-header, .grants-dash-panel-header {
                    background: #1e3a8f;
                    color: #fff;
                    border-radius: 0.25rem 0.25rem 0 0;
                }
                .grants-dash-card .card-header .badge {
                    background: rgba(255,255,255,0.2);
                    color: #fff;
                }
                .grants-dash-stat { font-size: 0.95rem; margin-right: 1.5rem; display: inline-block; }
            </style>

            {{-- Pending approvals (preview) --}}
            <div class="card grants-dash-card mb-4">
                <div class="card-header grants-dash-panel-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <i class="fas fa-clock me-2"></i><strong>Pending approvals</strong>
                        <span class="badge ms-2">{{ $globalPending }}</span>
                    </div>
                    <a href="{{ route('crm.access.queue') }}" class="btn btn-sm btn-outline-light">Open queue</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Requested</th>
                                    <th>Requester</th>
                                    <th>Record</th>
                                    <th>Office / team</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingPreview as $g)
                                    <tr>
                                        <td>{{ $g->requested_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                                        <td>{{ $g->staff?->first_name }} {{ $g->staff?->last_name }} <span class="text-muted">(#{{ $g->staff_id }})</span></td>
                                        <td>#{{ $g->admin_id }} ({{ $g->record_type }})</td>
                                        <td class="small">
                                            {{ $g->office_label_snapshot ?? '—' }}
                                            @if($g->team_label_snapshot)
                                                <span class="text-muted"> / {{ $g->team_label_snapshot }}</span>
                                            @endif
                                        </td>
                                        <td class="small">{{ \Illuminate\Support\Str::limit($g->requester_note ?? '', 80) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-check-circle text-success me-1"></i> No pending requests
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('crm.access.queue') }}">View full queue page &rarr;</a>
                </div>
            </div>

            {{-- Filterable grants --}}
            <div class="card grants-dash-card">
                <div class="card-header grants-dash-panel-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <strong><i class="fas fa-key me-2"></i>All grants (filterable)</strong>
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-light">Main dashboard</a>
                </div>
                <div class="card-body">
                    <form method="get" action="{{ route('crm.access.dashboard') }}" class="row g-3 mb-3">
                        <div class="col-md-2 col-sm-6">
                            <label class="form-label">Staff ID</label>
                            <input type="number" name="staff_id" class="form-control" placeholder="Staff #"
                                   value="{{ $filters['staff_id'] ?? '' }}" min="1">
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label class="form-label">Record (admin) ID</label>
                            <input type="number" name="admin_id" class="form-control" placeholder="Client/lead #"
                                   value="{{ $filters['admin_id'] ?? '' }}" min="1">
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label class="form-label">From</label>
                            <input type="date" name="from" class="form-control" value="{{ $filters['from'] ?? '' }}">
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label class="form-label">To</label>
                            <input type="date" name="to" class="form-control" value="{{ $filters['to'] ?? '' }}">
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label class="form-label">Office</label>
                            <select name="office_id" class="form-control">
                                <option value="">Any</option>
                                @foreach($offices as $o)
                                    <option value="{{ $o->id }}" @selected(($filters['office_id'] ?? null) == $o->id)>{{ $o->office_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label class="form-label">Team</label>
                            <select name="team_id" class="form-control">
                                <option value="">Any</option>
                                @foreach($teams as $t)
                                    <option value="{{ $t->id }}" @selected(($filters['team_id'] ?? null) == $t->id)>{{ $t->name ?? ('Team #' . $t->id) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label class="form-label">Grant type</label>
                            <select name="grant_type" class="form-control">
                                <option value="">Any</option>
                                <option value="quick" @selected(($filters['grant_type'] ?? '') === 'quick')>Quick</option>
                                <option value="supervisor_approved" @selected(($filters['grant_type'] ?? '') === 'supervisor_approved')>Supervisor approved</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="">Any</option>
                                <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Pending</option>
                                <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                                <option value="rejected" @selected(($filters['status'] ?? '') === 'rejected')>Rejected</option>
                                <option value="revoked" @selected(($filters['status'] ?? '') === 'revoked')>Revoked</option>
                                <option value="expired" @selected(($filters['status'] ?? '') === 'expired')>Expired</option>
                            </select>
                        </div>
                        <div class="col-12 d-flex align-items-center flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">Apply</button>
                            <a href="{{ route('crm.access.dashboard') }}" class="text-decoration-none">Reset</a>
                            <a href="{{ route('crm.access.dashboard.export', request()->query()) }}" class="text-decoration-none">Export CSV</a>
                        </div>
                    </form>

                    <div class="mb-3">
                        <span class="grants-dash-stat"><strong>Global pending:</strong> {{ $globalPending }}</span>
                        <span class="grants-dash-stat"><strong>Global active:</strong> {{ $globalActive }}</span>
                        <span class="grants-dash-stat"><strong>Rows (filtered):</strong> {{ number_format($rowCount) }}</span>
                        <span class="grants-dash-stat"><strong>Distinct records:</strong> {{ number_format($distinctRecords) }}</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Requested</th>
                                    <th>Staff</th>
                                    <th>Record</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Reason</th>
                                    <th>Office / team</th>
                                    <th>Ends</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($grants as $g)
                                    <tr>
                                        <td>{{ $g->requested_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                                        <td>{{ $g->staff?->first_name }} {{ $g->staff?->last_name }} <span class="text-muted">(#{{ $g->staff_id }})</span></td>
                                        <td>#{{ $g->admin_id }} ({{ $g->record_type }})</td>
                                        <td>{{ str_replace('_', ' ', $g->grant_type) }}</td>
                                        <td>{{ $g->status }}</td>
                                        <td class="small">{{ $reasonLabels[$g->quick_reason_code] ?? $g->quick_reason_code ?? '—' }}</td>
                                        <td class="small">
                                            {{ $g->office_label_snapshot ?? '—' }}
                                            @if($g->team_label_snapshot)
                                                <span class="text-muted"> / {{ $g->team_label_snapshot }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $g->ends_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-muted py-4">No grants match the current filters.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($grants->hasPages())
                        <div class="card-footer">{{ $grants->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
