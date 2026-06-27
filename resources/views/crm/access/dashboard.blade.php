@extends('layouts.adminconsole')
@section('title', 'Grants Dashboard')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-body grants-access-dashboard pb-4">
            @include('../Elements/flash-message')

            <style>
                .grants-access-dashboard {
                    --grants-dash-header: linear-gradient(135deg, #1e3a8f 0%, #2563eb 100%);
                    --grants-dash-card-radius: 0.5rem;
                }
                .grants-dash-card {
                    border: 1px solid rgba(30, 58, 143, 0.12);
                    border-radius: var(--grants-dash-card-radius);
                    box-shadow: 0 2px 12px rgba(15, 23, 42, 0.06);
                    overflow: hidden;
                }
                .grants-dash-card .card-header.grants-dash-panel-header {
                    background: var(--grants-dash-header);
                    color: #fff;
                    border: none;
                    padding: 0.85rem 1.1rem;
                }
                .grants-dash-card .card-header .badge {
                    background: rgba(255,255,255,0.22);
                    color: #fff;
                    font-weight: 600;
                    padding: 0.35em 0.65em;
                }
                .grants-dash-collapse-toggle {
                    color: #fff !important;
                    border: none;
                    background: transparent;
                    font: inherit;
                    align-items: center;
                    gap: 0.35rem;
                    text-align: left;
                    flex: 1;
                    min-width: 0;
                    padding: 0.15rem 0;
                }
                .grants-dash-collapse-toggle:hover,
                .grants-dash-collapse-toggle:focus {
                    color: #fff !important;
                    opacity: 0.92;
                }
                .grants-dash-collapse-toggle:focus {
                    box-shadow: none;
                    outline: 2px solid rgba(255,255,255,0.5);
                    outline-offset: 2px;
                }
                .grants-dash-chevron {
                    display: inline-block;
                    transition: transform 0.2s ease;
                    width: 1.1rem;
                    text-align: center;
                }
                .grants-dash-collapse-toggle.collapsed .grants-dash-chevron {
                    transform: rotate(-90deg);
                }
                .grants-dash-filters {
                    background: #f8fafc;
                    border: 1px solid #e2e8f0;
                    border-radius: 0.5rem;
                    padding: 1.1rem 1.15rem 0.85rem;
                    margin-bottom: 1.25rem;
                }
                .grants-dash-filters .form-label {
                    font-size: 0.8rem;
                    font-weight: 600;
                    color: #475569;
                    margin-bottom: 0.3rem;
                }
                .grants-dash-filters .form-control,
                .grants-dash-filters select.form-control {
                    border-color: #cbd5e1;
                    font-size: 0.9rem;
                }
                .grants-dash-stats {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 0.5rem;
                    margin-bottom: 1rem;
                }
                .grants-dash-stat-pill {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.35rem;
                    padding: 0.35rem 0.75rem;
                    background: #fff;
                    border: 1px solid #e2e8f0;
                    border-radius: 2rem;
                    font-size: 0.85rem;
                    color: #334155;
                    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
                }
                .grants-dash-stat-pill strong {
                    color: #0f172a;
                    font-weight: 600;
                }
                .grants-dash-table-wrap {
                    border: 1px solid #e2e8f0;
                    border-radius: 0.35rem;
                    overflow: hidden;
                }
                .grants-dash-table-wrap .table {
                    margin-bottom: 0;
                }
                .grants-dash-table-wrap thead th {
                    background: #f1f5f9;
                    color: #334155;
                    font-size: 0.78rem;
                    text-transform: uppercase;
                    letter-spacing: 0.03em;
                    font-weight: 700;
                    border-bottom-width: 1px;
                    white-space: nowrap;
                }
                .grants-dash-pending-wrap .table thead th {
                    background: #f8fafc;
                }
                .grants-dash-pending-wrap .card-footer {
                    background: #fff;
                    border-top: 1px solid #e2e8f0;
                    padding: 0.6rem 1rem;
                }
            </style>

            {{-- Pending approvals (preview) — collapsible; expanded when there is global pending work --}}
            <div class="card grants-dash-card mb-4">
                <div class="card-header grants-dash-panel-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <button type="button"
                            class="grants-dash-collapse-toggle d-flex collapsed"
                            data-bs-toggle="collapse"
                            data-bs-target="#accessPendingApprovals"
                            aria-expanded="false"
                            aria-controls="accessPendingApprovals">
                        <span class="grants-dash-chevron">@icon('chevron-down')</span>
                        <span>
                            @icon('clock', 'solid', ['class' => 'me-sm-2'])<strong>Pending approvals</strong>
                            <span class="badge ms-2">{{ $globalPending }}</span>
                        </span>
                    </button>
                    <a href="{{ route('crm.access.queue') }}" class="btn btn-sm btn-outline-light flex-shrink-0">Open queue</a>
                </div>
                <div id="accessPendingApprovals" class="collapse grants-dash-pending-wrap">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 align-middle">
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
                                            <td colspan="5" class="text-center text-muted py-5">
                                                @icon('check-circle', 'solid', ['class' => 'text-success me-2'])No pending requests
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('crm.access.queue') }}" class="text-decoration-none">View full queue page →</a>
                    </div>
                </div>
            </div>

            {{-- Filterable grants --}}
            <div class="card grants-dash-card">
                <div class="card-header grants-dash-panel-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <strong class="d-flex align-items-center gap-2">@icon('filter')<span>All grants (filterable)</span></strong>
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-light">Main dashboard</a>
                </div>
                <div class="card-body p-3 p-md-4">
                    <form method="get" action="{{ route('crm.access.dashboard') }}" class="grants-dash-filters">
                        <div class="row g-3">
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
                            <div class="col-12 d-flex align-items-center flex-wrap gap-2 pt-1">
                                <button type="submit" class="btn btn-primary px-4">Apply</button>
                                <a href="{{ route('crm.access.dashboard') }}" class="btn btn-link text-decoration-none p-0">Reset</a>
                                <span class="text-muted d-none d-sm-inline">·</span>
                                <a href="{{ route('crm.access.dashboard.export', request()->query()) }}" class="btn btn-link text-decoration-none p-0">Export CSV</a>
                            </div>
                        </div>
                    </form>

                    <div class="grants-dash-stats">
                        <span class="grants-dash-stat-pill"><strong>Global pending</strong> {{ $globalPending }}</span>
                        <span class="grants-dash-stat-pill"><strong>Global active</strong> {{ $globalActive }}</span>
                        <span class="grants-dash-stat-pill"><strong>Rows (filtered)</strong> {{ number_format($rowCount) }}</span>
                        <span class="grants-dash-stat-pill"><strong>Distinct records</strong> {{ number_format($distinctRecords) }}</span>
                    </div>

                    <div class="table-responsive grants-dash-table-wrap">
                        <table class="table table-striped table-sm mb-0 align-middle">
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
                </div>
                @if($grants->hasPages())
                    <div class="card-footer border-top bg-light px-3 px-md-4 py-3">{{ $grants->links() }}</div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
