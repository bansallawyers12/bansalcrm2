@extends('layouts.admin')
@section('title', 'Sheets Insights')

@push('styles')
<style>
    .insights-page-header {
        background: linear-gradient(135deg, #5b4d96 0%, #6f5fb8 50%, #5b4d96 100%);
        color: #fff;
        padding: 1rem 1.25rem;
        border-radius: 10px;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(91, 77, 150, 0.25);
    }
    .insights-page-header h4 { margin: 0; font-weight: 600; color: #fff; font-size: 1.35rem; }
    .insights-kpi-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        background: #fff;
        position: relative;
        padding: 1.25rem;
        height: 100%;
    }
    .insights-kpi-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
    }
    .insights-kpi-card.kpi-conv::before { background: linear-gradient(90deg, #10b981 0%, #059669 100%); }
    .insights-kpi-card.kpi-seen::before { background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%); }
    .insights-kpi-card.kpi-disc::before { background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%); }
    .insights-kpi-card.kpi-rate::before { background: linear-gradient(90deg, #8b5cf6 0%, #7c3aed 100%); }
    .insights-kpi-card h6 { font-size: 0.8125rem; color: #6b7280; margin-bottom: 0.5rem; font-weight: 600; }
    .insights-kpi-card .kpi-value { font-size: 2rem; font-weight: 700; color: #1f2937; }
    .insights-chart-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        background: #fff;
        margin-bottom: 1.5rem;
    }
    .insights-chart-card .card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.25rem;
        font-weight: 600;
        color: #334155;
    }
    .insights-chart-card .card-body { padding: 1.25rem; }
    .insights-filter-card {
        border: 1px solid #e3e6f0;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        margin-bottom: 1.5rem;
    }
    .insights-filter-card .card-body { padding: 1rem; }
    .insights-table .table thead th { font-weight: 600; color: #374151; }
</style>
@endpush

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-body">

            <div class="insights-page-header">
                <h4><i class="fas fa-chart-bar"></i> Sheets Insights</h4>
            </div>

            {{-- Filters --}}
            <div class="card insights-filter-card">
                <div class="card-body">
                    <form method="get" action="{{ route('clients.sheets.insights') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-md-2">
                            <label class="form-label small fw-semibold">Date From</label>
                            <input type="text" name="date_from" class="form-control insights-datepicker" placeholder="DD/MM/YYYY" value="{{ request('date_from') }}" autocomplete="off">
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="form-label small fw-semibold">Date To</label>
                            <input type="text" name="date_to" class="form-control insights-datepicker" placeholder="DD/MM/YYYY" value="{{ request('date_to') }}" autocomplete="off">
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="form-label small fw-semibold">Branch</label>
                            <select name="branch[]" class="form-select select2" multiple>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ in_array($b->id, (array)request('branch', [])) ? 'selected' : '' }}>{{ $b->office_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="form-label small fw-semibold">Assignee</label>
                            <select name="assignee" class="form-select">
                                <option value="all">All</option>
                                @foreach($assigneesForFilter as $a)
                                    @php $dn = trim(($a->first_name ?? '') . ' ' . ($a->last_name ?? '')) ?: ($a->email ?? '—'); @endphp
                                    <option value="{{ $a->id }}" {{ request('assignee') == $a->id ? 'selected' : '' }}>{{ $dn }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Apply</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- KPI Cards --}}
            <div class="row mb-4">
                <div class="col-6 col-lg-3 mb-3">
                    <div class="insights-kpi-card kpi-conv">
                        <h6><i class="fas fa-user-check me-1"></i> Total Converted</h6>
                        <div class="kpi-value">{{ number_format($totalConversions) }}</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 mb-3">
                    <div class="insights-kpi-card kpi-seen">
                        <h6><i class="fas fa-eye me-1"></i> Clients Seen</h6>
                        <div class="kpi-value">{{ number_format($totalSeen) }}</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 mb-3">
                    <div class="insights-kpi-card kpi-disc">
                        <h6><i class="fas fa-ban me-1"></i> Discontinued</h6>
                        <div class="kpi-value">{{ number_format($totalDiscontinued) }}</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 mb-3">
                    <div class="insights-kpi-card kpi-rate">
                        <h6><i class="fas fa-percentage me-1"></i> Conversion Rate</h6>
                        <div class="kpi-value">{{ $conversionRate }}%</div>
                    </div>
                </div>
            </div>

            {{-- Charts Row 1 --}}
            <div class="row">
                <div class="col-12 col-lg-6 mb-4">
                    <div class="insights-chart-card card">
                        <div class="card-header">Conversions by Assignee</div>
                        <div class="card-body">
                            <div id="chartConversionsByAssignee" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 mb-4">
                    <div class="insights-chart-card card">
                        <div class="card-header">Clients Seen by Assignee</div>
                        <div class="card-body">
                            <div id="chartSeenByAssignee" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts Row 2 --}}
            <div class="row">
                <div class="col-12 col-lg-6 mb-4">
                    <div class="insights-chart-card card">
                        <div class="card-header">Converted vs Discontinued</div>
                        <div class="card-body">
                            <div id="chartConvertVsDiscontinue" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 mb-4">
                    <div class="insights-chart-card card">
                        <div class="card-header">Monthly Trend (Last 12 Months)</div>
                        <div class="card-body">
                            <div id="chartMonthlyTrend" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Assignee Table --}}
            <div class="card insights-chart-card">
                <div class="card-header">Assignee Performance</div>
                <div class="card-body">
                    <div class="table-responsive insights-table">
                        <table class="table table-hover" id="insightsAssigneeTable">
                            <thead>
                                <tr>
                                    <th>Assignee</th>
                                    <th class="text-end">Converted</th>
                                    <th class="text-end">Seen</th>
                                    <th class="text-end">Discontinued</th>
                                    <th class="text-end">Rate %</th>
                                    <th class="text-end">Current Load</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assigneeData as $row)
                                    <tr>
                                        <td>{{ $row['name'] }}</td>
                                        <td class="text-end">{{ $row['converted'] }}</td>
                                        <td class="text-end">{{ $row['seen'] }}</td>
                                        <td class="text-end">{{ $row['discontinued'] }}</td>
                                        <td class="text-end">{{ $row['rate'] }}%</td>
                                        <td class="text-end">{{ $row['load'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">No data available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/apexcharts.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Flatpickr for date inputs
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.insights-datepicker', { dateFormat: 'd/m/Y', allowInput: true });
    }
    // Select2
    if ($ && $.fn.select2) {
        $('.insights-filter-card .select2').select2({ width: '100%' });
    }

    // Chart: Conversions by Assignee
    var convLabels = @json($chartConversionsByAssignee['labels']);
    var convValues = @json($chartConversionsByAssignee['values']);
    if (document.getElementById('chartConversionsByAssignee') && typeof ApexCharts !== 'undefined') {
        new ApexCharts(document.querySelector('#chartConversionsByAssignee'), {
            chart: { type: 'bar', height: 300, toolbar: { show: false } },
            plotOptions: { bar: { horizontal: true, barHeight: '70%', borderRadius: 4 } },
            dataLabels: { enabled: true },
            series: [{ name: 'Converted', data: convValues }],
            xaxis: { categories: convLabels },
            colors: ['#10b981'],
        }).render();
    }

    // Chart: Clients Seen by Assignee
    var seenLabels = @json($chartSeenByAssignee['labels']);
    var seenValues = @json($chartSeenByAssignee['values']);
    if (document.getElementById('chartSeenByAssignee') && typeof ApexCharts !== 'undefined') {
        new ApexCharts(document.querySelector('#chartSeenByAssignee'), {
            chart: { type: 'bar', height: 300, toolbar: { show: false } },
            plotOptions: { bar: { horizontal: true, barHeight: '70%', borderRadius: 4 } },
            dataLabels: { enabled: true },
            series: [{ name: 'Seen', data: seenValues }],
            xaxis: { categories: seenLabels },
            colors: ['#3b82f6'],
        }).render();
    }

    // Chart: Convert vs Discontinue (Donut)
    var pieData = @json($chartConvertVsDiscontinue);
    if (document.getElementById('chartConvertVsDiscontinue') && typeof ApexCharts !== 'undefined') {
        new ApexCharts(document.querySelector('#chartConvertVsDiscontinue'), {
            chart: { type: 'donut', height: 300 },
            labels: pieData.map(function(r) { return r[0]; }),
            series: pieData.map(function(r) { return r[1]; }),
            colors: ['#10b981', '#ef4444'],
            legend: { position: 'bottom' },
        }).render();
    }

    // Chart: Monthly Trend
    var monthLabels = @json($chartMonthlyTrend['labels']);
    var convMonth = @json($chartMonthlyTrend['conversions']);
    var discMonth = @json($chartMonthlyTrend['discontinues']);
    if (document.getElementById('chartMonthlyTrend') && typeof ApexCharts !== 'undefined') {
        new ApexCharts(document.querySelector('#chartMonthlyTrend'), {
            chart: { type: 'line', height: 300, toolbar: { show: false }, zoom: { enabled: false } },
            stroke: { curve: 'smooth', width: 2 },
            series: [
                { name: 'Conversions', data: convMonth },
                { name: 'Discontinues', data: discMonth },
            ],
            xaxis: { categories: monthLabels },
            colors: ['#10b981', '#ef4444'],
            legend: { position: 'top' },
        }).render();
    }

    // DataTable for assignee table (optional)
    if ($ && $.fn.DataTable && $('#insightsAssigneeTable tbody tr td').length && !$('#insightsAssigneeTable tbody tr td').first().text().includes('No data')) {
        $('#insightsAssigneeTable').DataTable({ pageLength: 25, order: [[1, 'desc']] });
    }
});
</script>
@endpush
