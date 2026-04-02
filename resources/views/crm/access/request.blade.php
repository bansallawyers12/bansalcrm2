@extends('layouts.admin')
@section('title', 'Request access')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            @include('../Elements/flash-message')
            <div class="card">
                <div class="card-header">
                    <h4>Request temporary access</h4>
                    <p class="text-muted small mb-0">
                        Record #{{ $admin->id }} — {{ $admin->first_name }} {{ $admin->last_name }}
                        ({{ $admin->type }})
                    </p>
                </div>
                <div class="card-body">
                    @if(!$quickEnabled && !$canSupervisor)
                        <p class="text-warning">Quick access is not enabled on your account, and your role cannot use supervisor requests. Ask a Super Admin or Admin to enable quick access for you.</p>
                    @else
                        <div class="mb-3">
                            <label class="form-label">Office</label>
                            <select id="cag-office" class="form-control">
                                @foreach($offices as $o)
                                    <option value="{{ $o->id }}">{{ $o->office_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason</label>
                            <select id="cag-reason" class="form-control">
                                @foreach($reasons as $code => $label)
                                    <option value="{{ $code }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($quickEnabled)
                            <button type="button" id="cag-quick" class="btn btn-primary">Start {{ config('crm_access.quick_grant_minutes', 15) }}-minute access</button>
                        @endif
                        @if($canSupervisor)
                            <div class="mt-3">
                                <label class="form-label">Note for supervisor (optional)</label>
                                <textarea id="cag-note" class="form-control" rows="2"></textarea>
                                <button type="button" id="cag-super" class="btn btn-outline-primary mt-2">Request supervisor approval ({{ config('crm_access.supervisor_grant_hours', 24) }}h)</button>
                            </div>
                        @endif
                        <div id="cag-msg" class="mt-3 small"></div>
                    @endif
                    <p class="mt-3 mb-0"><a href="{{ url('/clients') }}">Back to clients</a></p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var adminId = {{ (int) $admin->id }};
    var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    function post(url, body, cb) {
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify(body)
        }).then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
          .then(cb).catch(function () { cb({ ok: false, j: { message: 'Network error' } }); });
    }
    var msg = document.getElementById('cag-msg');
    function show(t, ok) {
        if (!msg) return;
        msg.textContent = t;
        msg.className = 'mt-3 small ' + (ok ? 'text-success' : 'text-danger');
    }
    document.getElementById('cag-quick')?.addEventListener('click', function () {
        var office = document.getElementById('cag-office').value;
        var reason = document.getElementById('cag-reason').value;
        post('{{ url('/crm/access/quick') }}', { admin_id: adminId, office_id: parseInt(office, 10), reason: reason }, function (res) {
            if (res.ok && res.j && res.j.ok) {
                show('Access granted until ' + (res.j.ends_at || '') + '. You can open the record now.', true);
                setTimeout(function () { location.reload(); }, 800);
            } else {
                show((res.j && res.j.message) || 'Request failed', false);
            }
        });
    });
    document.getElementById('cag-super')?.addEventListener('click', function () {
        var office = document.getElementById('cag-office').value;
        var reason = document.getElementById('cag-reason').value;
        var note = document.getElementById('cag-note') ? document.getElementById('cag-note').value : '';
        post('{{ url('/crm/access/supervisor') }}', { admin_id: adminId, office_id: parseInt(office, 10), reason: reason, note: note }, function (res) {
            if (res.ok && res.j && res.j.ok) {
                show('Supervisor request submitted.', true);
            } else {
                show((res.j && res.j.message) || 'Request failed', false);
            }
        });
    });
})();
</script>
@endpush
