@extends('layouts.admin')
@section('title', 'Access requests')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            @include('../Elements/flash-message')
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Pending supervisor access requests</h4>
                    <a href="{{ route('crm.access.my-grants') }}" class="btn btn-outline-secondary btn-sm">My grants</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Requested</th>
                                    <th>Staff</th>
                                    <th>Record</th>
                                    <th>Reason</th>
                                    <th>Note</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pending as $g)
                                    <tr>
                                        <td>{{ $g->requested_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</td>
                                        <td>{{ $g->staff?->first_name }} {{ $g->staff?->last_name }}</td>
                                        <td>#{{ $g->admin_id }} ({{ $g->record_type }})</td>
                                        <td>{{ config('crm_access.quick_reason_options')[$g->quick_reason_code] ?? $g->quick_reason_code }}</td>
                                        <td class="small">{{ \Illuminate\Support\Str::limit($g->requester_note ?? '', 80) }}</td>
                                        <td class="text-nowrap">
                                            <form action="{{ route('crm.access.approve', $g->id) }}" method="post" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                            </form>
                                            <form action="{{ route('crm.access.reject', $g->id) }}" method="post" class="d-inline ms-1">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Reject</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">No pending requests.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($pending->hasPages())
                    <div class="card-footer">{{ $pending->links() }}</div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
