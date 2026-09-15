@extends('layouts.adminconsole')
@section('title', 'Staff Workload — '.$staff->full_name)

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="server-error">@include('../Elements/flash-message')</div>
            <div class="mb-3">
                <a href="{{ route('adminconsole.staff-workload.index') }}" class="btn btn-sm btn-outline-secondary">@icon('arrow-left') Back to all staff</a>
            </div>
            @include('Admin.partials.my-day-panel', [
                'summary' => $summary,
                'panelTitle' => $staff->full_name.' — My Day',
                'workloadSectionTitle' => $staff->full_name.'\'s workload',
                'embeddedOnDashboard' => true,
            ])
            <div class="card mt-3">
                <div class="card-header">
                    <h4 class="mb-0">End-of-day summary</h4>
                </div>
                <div class="card-body">
                    @if(!empty($daySummary['stored']))
                        <p class="text-muted mb-2">
                            @if(!empty($daySummary['day_label']))
                                For {{ $daySummary['day_label'] }}
                                &middot;
                            @endif
                            Saved
                            @if(!empty($daySummary['saved_at']))
                                {{ \Carbon\Carbon::parse($daySummary['saved_at'])->timezone(config('app.timezone'))->format('d/m/Y g:i a') }}
                            @endif
                            @if(!empty($daySummary['source_label']))
                                ({{ $daySummary['source_label'] }})
                            @elseif(!empty($daySummary['source']))
                                ({{ $daySummary['source'] }})
                            @endif
                        </p>
                        <pre class="mb-0" style="white-space:pre-wrap;background:#f8fafc;border:1px solid #e5e7eb;border-radius:8px;padding:0.75rem;font-size:0.8rem;max-height:360px;overflow:auto;">{{ $daySummary['text'] }}</pre>
                    @else
                        <p class="text-muted mb-0">No summary saved yet. Staff can copy/save from My Day diary, or the nightly snapshot at 11:55pm Melbourne will store it.</p>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
