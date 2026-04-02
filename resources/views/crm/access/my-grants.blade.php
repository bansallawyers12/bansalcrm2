@extends('layouts.admin')
@section('title', 'My access grants')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            @include('../Elements/flash-message')
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>My access grants</h4>
                    @if(auth('admin')->user() && in_array((int) auth('admin')->user()->role, config('crm_access.exempt_role_ids', [1, 12]), true))
                        <a href="{{ route('crm.access.queue') }}" class="btn btn-outline-primary btn-sm">Approver queue</a>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>When</th>
                                    <th>Record</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Until</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($grants as $g)
                                    <tr>
                                        <td>{{ $g->requested_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</td>
                                        <td>#{{ $g->admin_id }}</td>
                                        <td>{{ $g->grant_type }}</td>
                                        <td>{{ $g->status }}</td>
                                        <td>{{ $g->ends_at ? $g->ends_at->timezone(config('app.timezone'))->format('Y-m-d H:i') : '—' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">No grants yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($grants->hasPages())
                    <div class="card-footer">{{ $grants->links() }}</div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
