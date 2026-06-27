@extends('layouts.admin')
@section('title', 'Staff Login Log')

@push('styles')
<style>
.audit-page-header {
    background: linear-gradient(135deg, #5b4d96 0%, #6f5fb8 50%, #5b4d96 100%);
    color: #fff;
    padding: 1rem 1.25rem;
    border-radius: 10px;
    margin-bottom: 1rem;
    box-shadow: 0 2px 8px rgba(91, 77, 150, 0.25);
}
.audit-page-header h4 { margin: 0; font-weight: 600; color: #fff; font-size: 1.35rem; }
.audit-kpi-card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    background: #fff;
    position: relative;
    padding: 1.25rem;
    height: 100%;
}
.audit-kpi-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
}
.audit-kpi-card.kpi-login::before { background: linear-gradient(90deg, #10b981 0%, #059669 100%); }
.audit-kpi-card.kpi-logout::before { background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%); }
.audit-kpi-card.kpi-active::before { background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%); }
.audit-kpi-card.kpi-week::before { background: linear-gradient(90deg, #8b5cf6 0%, #7c3aed 100%); }
.audit-kpi-card.kpi-top::before { background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%); }
.audit-kpi-card h6 { font-size: 0.8125rem; color: #6b7280; margin-bottom: 0.5rem; font-weight: 600; }
.audit-kpi-card .kpi-value { font-size: 1.5rem; font-weight: 700; color: #1f2937; }
.audit-chart-card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    background: #fff;
    margin-bottom: 1.5rem;
}
.audit-chart-card .card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom: 1px solid #e2e8f0;
    padding: 0.75rem 1rem;
    font-weight: 600;
    color: #334155;
}
.audit-chart-card .card-body { padding: 0.75rem 1rem; }
.audit-filter-card { margin-bottom: 1.5rem; }
.audit-table .table thead th { font-weight: 600; color: #374151; }
.audit-table tr.row-login { background-color: rgba(16, 185, 129, 0.06); }
.audit-table tr.row-logout { background-color: rgba(239, 68, 68, 0.04); }
.badge-event { font-size: 0.75rem; padding: 0.35em 0.65em; }
.badge-login { background-color: #10b981; color: #fff; }
.badge-logout { background-color: #ef4444; color: #fff; }
.staff-initials { width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.8rem; color: #fff; background: #5b4d96; }
.staff-session-accordion .accordion-button { font-weight: 600; }
.staff-session-accordion .accordion-body { padding: 0.75rem 1rem; }
</style>
@endpush

@section('content')

<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="server-error">@include('../Elements/flash-message')</div>
            <div class="custom-error-msg"></div>

            <div class="audit-page-header">
                <h4><i class="fas fa-clipboard-list"></i> Staff Login Log</h4>
            </div>

            {{-- Filters --}}
            <div class="card audit-filter-card">
                <div class="card-body">
                    <form method="get" action="{{ route('auditlogs.index') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-md-2">
                            <label class="form-label small fw-semibold">Staff</label>
                            <select name="staff_id" class="form-control tomselect audit-staff-select">
                                <option value="">All Staff</option>
                                @foreach($staffList as $s)
                                    @php $dn = $s->full_name; @endphp
                                    <option value="{{ $s->id }}" {{ request('staff_id') == $s->id ? 'selected' : '' }}>{{ $dn }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="form-label small fw-semibold">Date From</label>
                            <input type="text" name="date_from" class="form-control audit-datepicker" placeholder="DD/MM/YYYY" value="{{ request('date_from') }}" autocomplete="off">
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="form-label small fw-semibold">Date To</label>
                            <input type="text" name="date_to" class="form-control audit-datepicker" placeholder="DD/MM/YYYY" value="{{ request('date_to') }}" autocomplete="off">
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="form-label small fw-semibold">Event Type</label>
                            <select name="event_type" class="form-select">
                                <option value="">All</option>
                                <option value="login" {{ request('event_type') == 'login' ? 'selected' : '' }}>Logged In</option>
                                <option value="logout" {{ request('event_type') == 'logout' ? 'selected' : '' }}>Logged Out</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="form-label small fw-semibold">IP Address</label>
                            <input type="text" name="ip_address" class="form-control" placeholder="Search IP" value="{{ request('ip_address') }}" autocomplete="off">
                        </div>
                        <div class="col-12 col-md-2">
                            <input type="hidden" name="per_page" value="{{ $perPage }}">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill"><i class="fas fa-search me-1"></i> Apply</button>
                                <a href="{{ route('auditlogs.index') }}" class="btn btn-outline-secondary flex-fill">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- KPI Cards --}}
            <div class="row mb-4">
                <div class="col-6 col-lg mb-3">
                    <div class="audit-kpi-card kpi-login">
                        <h6><i class="fas fa-sign-in-alt me-1"></i> Logins Today</h6>
                        <div class="kpi-value">{{ number_format($todayLogins) }}</div>
                    </div>
                </div>
                <div class="col-6 col-lg mb-3">
                    <div class="audit-kpi-card kpi-logout">
                        <h6><i class="fas fa-sign-out-alt me-1"></i> Logouts Today</h6>
                        <div class="kpi-value">{{ number_format($todayLogouts) }}</div>
                    </div>
                </div>
                <div class="col-6 col-lg mb-3">
                    <div class="audit-kpi-card kpi-active">
                        <h6><i class="fas fa-user-check me-1"></i> Active Staff Now</h6>
                        <div class="kpi-value">{{ number_format($activeStaffCount) }}</div>
                    </div>
                </div>
                <div class="col-6 col-lg mb-3">
                    <div class="audit-kpi-card kpi-week">
                        <h6><i class="fas fa-users me-1"></i> Staff This Week</h6>
                        <div class="kpi-value">{{ number_format($uniqueStaffThisWeek) }}</div>
                    </div>
                </div>
                <div class="col-6 col-lg mb-3">
                    <div class="audit-kpi-card kpi-top">
                        <h6><i class="fas fa-star me-1"></i> Most Active Today</h6>
                        <div class="kpi-value" style="font-size:1rem;">{{ $topStaffName }}</div>
                    </div>
                </div>
            </div>

            {{-- Charts Row --}}
            <div class="row">
                <div class="col-12 col-lg-4 mb-4">
                    <div class="audit-chart-card card">
                        <div class="card-header">Login Activity by Hour</div>
                        <div class="card-body">
                            <div id="chartLoginsByHour" style="min-height: 260px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 mb-4">
                    <div class="audit-chart-card card">
                        <div class="card-header">Top Staff by Login Count</div>
                        <div class="card-body">
                            <div id="chartTopStaff" style="min-height: 260px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 mb-4">
                    <div class="audit-chart-card card">
                        <div class="card-header">Weekly Login Trend</div>
                        <div class="card-body">
                            <div id="chart30DayTrend" style="min-height: 260px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="card audit-chart-card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span>Log Entries</span>
                    <div class="d-flex gap-2 align-items-center">
                        <form method="get" action="{{ route('auditlogs.index') }}" class="d-inline" id="perPageForm">
                            @if(request('staff_id'))<input type="hidden" name="staff_id" value="{{ request('staff_id') }}">@endif
                            @if(request('date_from'))<input type="hidden" name="date_from" value="{{ request('date_from') }}">@endif
                            @if(request('date_to'))<input type="hidden" name="date_to" value="{{ request('date_to') }}">@endif
                            @if(request('event_type'))<input type="hidden" name="event_type" value="{{ request('event_type') }}">@endif
                            @if(request('ip_address'))<input type="hidden" name="ip_address" value="{{ request('ip_address') }}">@endif
                            <select name="per_page" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                                <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </form>
                        <a href="{{ route('auditlogs.export', request()->query()) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-download me-1"></i> Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive audit-table">
                        <table class="table table-hover text_wrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date & Time</th>
                                    <th>Staff</th>
                                    <th>Event</th>
                                    <th>IP Address</th>
                                    <th>Device / Browser</th>
                                    <th>Session Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lists as $idx => $list)
                                    @php
                                        $staff = $list->staff;
                                        $staffName = $staff ? $staff->full_name : '—';
                                        $initials = $staff ? strtoupper(substr($staff->first_name ?? '', 0, 1) . substr($staff->last_name ?? '', 0, 1)) : '—';
                                        if (empty(trim($initials))) $initials = $staff ? strtoupper(substr($staff->email ?? '', 0, 2)) : '—';
                                        $isLogin = $list->isLogin();
                                        $rowClass = $isLogin ? 'row-login' : 'row-logout';
                                        $durationKey = $list->user_id . '_' . $list->created_at->format('Y-m-d H:i:s');
                                        $durationSecs = $durationMap[$durationKey] ?? null;
                                        $durationStr = $durationSecs !== null ? gmdate('H:i:s', $durationSecs) : '—';
                                    @endphp
                                    <tr class="{{ $rowClass }}">
                                        <td>{{ $lists->firstItem() + $idx }}</td>
                                        <td>{{ $list->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            @if($staff)
                                                <span class="staff-initials me-1">{{ $initials }}</span>
                                                <a href="#">{{ $staffName }}</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            @if($isLogin)
                                                <span class="badge badge-event badge-login">Logged In</span>
                                            @else
                                                <span class="badge badge-event badge-logout">Logged Out</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($list->ip_address)
                                                <a target="_blank" rel="noopener" href="https://whatismyipaddress.com/ip/{{ $list->ip_address }}">{{ $list->ip_address }}</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td><span title="{{ $list->user_agent ?? '' }}">{{ \App\Helpers\UserAgentParser::parse($list->user_agent) }}</span></td>
                                        <td>{{ $durationStr }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">No log entries found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="text-muted small">
                        @if($lists->total() > 0)
                            Showing {{ $lists->firstItem() }} to {{ $lists->lastItem() }} of {{ $lists->total() }} entries
                        @else
                            No entries
                        @endif
                    </div>
                    <div>{!! $lists->appends(request()->except('page'))->links() !!}</div>
                </div>
            </div>

            {{-- Per-Staff Login Hours Accordion --}}
            @if(count($staffSessions) > 0)
            <div class="card audit-chart-card mt-4">
                <div class="card-header">Login Hours by Staff</div>
                <div class="card-body">
                    <div class="accordion staff-session-accordion" id="staffSessionsAccordion">
                        @foreach($staffSessions as $staffId => $data)
                            @php
                                $totalSecs = collect($data['sessions'] ?? [])->sum('duration_secs');
                                $totalHours = round($totalSecs / 3600, 1);
                                $sessionCount = count($data['sessions'] ?? []);
                            @endphp
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#staff-{{ $staffId }}" aria-expanded="false">
                                        {{ $data['name'] }} — {{ $totalHours }}h total ({{ $sessionCount }} session(s))
                                    </button>
                                </h2>
                                <div id="staff-{{ $staffId }}" class="accordion-collapse collapse" data-bs-parent="#staffSessionsAccordion">
                                    <div class="accordion-body">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead><tr><th>Date</th><th>Login</th><th>Logout</th><th>Duration</th></tr></thead>
                                            <tbody>
                                                @foreach($data['sessions'] ?? [] as $s)
                                                    <tr>
                                                        <td>{{ $s['login_at']->format('d/m/Y') }}</td>
                                                        <td>{{ $s['login_at']->format('H:i:s') }}</td>
                                                        <td>{{ $s['logout_at']->format('H:i:s') }}</td>
                                                        <td>{{ gmdate('H:i:s', $s['duration_secs']) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot><tr><td colspan="3" class="fw-bold">Total</td><td class="fw-bold">{{ gmdate('H:i:s', $totalSecs) }}</td></tr></tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </section>
</div>

@endsection

@push('scripts')
@vite(['resources/js/apexcharts-init.js'])
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.audit-datepicker', { dateFormat: 'd/m/Y', allowInput: true });
    }
    if (typeof whenTomSelectReady === 'function') {
        whenTomSelectReady(function () {
            initTomSelect('.audit-staff-select', {
                width: '100%',
                allowClear: true
            });
        });
    } else if (typeof waitForTomSelect === 'function') {
        waitForTomSelect().then(function () {
            initTomSelect('.audit-staff-select', {
                width: '100%',
                allowClear: true
            });
        });
    }

    var loginsByHour = @json(array_values($loginsByHour));
    var hourLabels = @json(array_map(function($h) {
        if ($h === 0)  return '12 AM';
        if ($h === 12) return '12 PM';
        return ($h < 12) ? ($h . ' AM') : (($h - 12) . ' PM');
    }, array_keys($loginsByHour)));
    if (document.getElementById('chartLoginsByHour') && typeof ApexCharts !== 'undefined') {
        var maxLogins = Math.max(...loginsByHour, 1);
        var yMax = Math.ceil(maxLogins * 1.12);
        new ApexCharts(document.querySelector('#chartLoginsByHour'), {
            chart: { type: 'bar', height: 250, toolbar: { show: false }, parentHeightOffset: 0 },
            plotOptions: { bar: { borderRadius: 4, columnWidth: '75%', dataLabels: { position: 'top' } } },
            dataLabels: { enabled: true, formatter: function(v) { return v > 0 ? v : ''; } },
            grid: { padding: { left: 0, right: 0, top: 4, bottom: 0 } },
            series: [{ name: 'Logins', data: loginsByHour }],
            xaxis: { categories: hourLabels, labels: { rotate: -45, style: { fontSize: '10px' } } },
            yaxis: { max: yMax, tickAmount: 6, labels: { style: { fontSize: '11px' } } },
            tooltip: { y: { formatter: function(v) { return v + ' login(s)'; } } },
            colors: ['#10b981'],
        }).render();
    }

    var topStaffLabels = @json($topStaffChartLabels);
    var topStaffValues = @json($topStaffChartValues);
    if (document.getElementById('chartTopStaff') && typeof ApexCharts !== 'undefined' && topStaffLabels.length) {
        new ApexCharts(document.querySelector('#chartTopStaff'), {
            chart: { type: 'bar', height: 250, toolbar: { show: false }, parentHeightOffset: 0 },
            plotOptions: { bar: { horizontal: true, barHeight: '88%', borderRadius: 4 } },
            dataLabels: { enabled: true, style: { fontSize: '11px' } },
            grid: { padding: { left: 0, right: 4, top: 0, bottom: 0 } },
            series: [{ name: 'Logins', data: topStaffValues }],
            xaxis: { categories: topStaffLabels, labels: { style: { fontSize: '11px' } } },
            yaxis: { labels: { style: { fontSize: '11px' }, offsetX: -2 } },
            colors: ['#3b82f6'],
        }).render();
    } else if (document.getElementById('chartTopStaff')) {
        document.getElementById('chartTopStaff').innerHTML = '<p class="text-muted text-center py-5">No data</p>';
    }

    var trendLabels = @json($labels30);
    var trendValues = @json($values30);
    if (document.getElementById('chart30DayTrend') && typeof ApexCharts !== 'undefined') {
        new ApexCharts(document.querySelector('#chart30DayTrend'), {
            chart: { type: 'line', height: 250, toolbar: { show: false }, zoom: { enabled: false }, parentHeightOffset: 0 },
            stroke: { curve: 'smooth', width: 2 },
            grid: { padding: { left: 0, right: 0, top: 4, bottom: 0 } },
            series: [{ name: 'Logins', data: trendValues }],
            xaxis: { categories: trendLabels, labels: { style: { fontSize: '11px' } } },
            colors: ['#8b5cf6'],
        }).render();
    }
});
</script>
@endpush
