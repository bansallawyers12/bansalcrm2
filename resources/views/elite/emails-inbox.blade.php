@extends('layouts.outlook')
@section('title', 'Education Elite — Inbox')

@push('styles')
<style>
.outlook-page { min-height: 100vh; background: #f0f0f0; }
.outlook-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 20px;
    background: #fff;
    border-bottom: 1px solid #d4d4d4;
}
.outlook-topbar .outlook-title { font-size: 16px; font-weight: 600; color: #333; margin: 0; }
.outlook-topbar .btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    color: #444;
    text-decoration: none;
    border-radius: 4px;
    font-size: 13px;
}
.outlook-topbar .btn-back:hover { background: #f3f3f3; color: #222; }
.outlook-container { display: flex; height: calc(100vh - 50px); min-height: 500px; }
.outlook-sidebar {
    width: 220px;
    min-width: 220px;
    background: #fff;
    border-right: 1px solid #d4d4d4;
    display: flex;
    flex-direction: column;
}
.outlook-folders { padding: 8px 0; flex: 1; }
.outlook-folders .folder-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 18px;
    color: #444;
    text-decoration: none;
    font-size: 13px;
    cursor: default;
}
.outlook-folders .folder-item.active { background: #e5f3ff; color: #0078d4; font-weight: 600; }
.outlook-folders .folder-item i { font-size: 14px; width: 18px; text-align: center; }
.elite-sidebar-foot {
    border-top: 1px solid #e2e8f0;
    padding: 12px 14px;
    background: #fafafa;
    font-size: 12px;
    color: #64748b;
}
.elite-sidebar-foot code { font-size: 11px; word-break: break-all; }
.simulate-panel {
    border-top: 1px solid #e2e8f0;
    padding: 12px 14px;
    background: #fff;
    max-height: 45vh;
    overflow-y: auto;
}
.simulate-panel h3 { font-size: 13px; font-weight: 600; margin: 0 0 10px; color: #334155; }
.simulate-panel .field { margin-bottom: 10px; }
.simulate-panel label { display: block; font-size: 12px; color: #64748b; margin-bottom: 4px; }
.simulate-panel input, .simulate-panel textarea {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #d4d4d4;
    border-radius: 4px;
    font-size: 13px;
    box-sizing: border-box;
}
.simulate-panel textarea { min-height: 80px; resize: vertical; }
.simulate-panel .btn-submit {
    width: 100%;
    padding: 8px 12px;
    background: #0078d4;
    color: #fff;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
}
.simulate-panel .btn-submit:hover { background: #106ebe; }
.outlook-main { flex: 1; display: flex; flex-direction: column; background: #fff; overflow: hidden; }
.folder-view { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
.folder-view .inbox-toolbar {
    padding: 10px 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
}
.inbox-toolbar .search-wrap { flex: 1; min-width: 180px; max-width: 280px; position: relative; }
.inbox-toolbar .search-wrap input {
    width: 100%;
    padding: 8px 12px 8px 36px;
    border: 1px solid #d4d4d4;
    border-radius: 4px;
    font-size: 13px;
    box-sizing: border-box;
}
.inbox-toolbar .search-wrap i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
}
.inbox-toolbar .filter-select,
.inbox-toolbar .filter-date { padding: 6px 10px; font-size: 12px; border: 1px solid #d4d4d4; border-radius: 4px; min-width: 100px; }
.inbox-toolbar .filter-custom-dates { display: inline-flex; align-items: center; gap: 6px; }
.empty-state {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #888;
    text-align: center;
    padding: 24px;
}
.empty-state i { font-size: 48px; margin-bottom: 16px; opacity: 0.5; }
.empty-state p { font-size: 15px; margin: 0 0 8px; }
.empty-state span { font-size: 13px; max-width: 420px; }
.email-list { list-style: none; margin: 0; padding: 0; overflow-y: auto; flex: 1; }
.email-list .email-row {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
}
.email-list .email-row:hover { background: #f8fafc; }
.email-list .email-row .email-sender { min-width: 180px; font-weight: 600; color: #1e293b; }
.email-list .email-row .email-subject { flex: 1; color: #475569; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.email-list .email-row .email-date { color: #94a3b8; font-size: 13px; white-space: nowrap; margin-left: 8px; }
.sendgrid-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px;
    background: #e0f2fe;
    color: #0078d4;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
    margin-left: 8px;
}
.sent-email-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.sent-email-modal-overlay.show { display: flex; }
.sent-email-modal {
    background: #fff;
    border-radius: 8px;
    max-width: 700px;
    width: 100%;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}
.sent-email-modal .modal-header {
    padding: 16px 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.sent-email-modal .modal-header h3 { margin: 0; font-size: 16px; color: #1e293b; }
.sent-email-modal .modal-close {
    width: 32px;
    height: 32px;
    border: none;
    background: transparent;
    border-radius: 4px;
    cursor: pointer;
    color: #64748b;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.sent-email-modal .modal-close:hover { background: #f1f5f9; color: #1e293b; }
.sent-email-modal .modal-body {
    padding: 20px;
    overflow-y: auto;
    flex: 1;
    min-height: 0;
}
.sent-email-modal .email-meta-row { margin-bottom: 12px; font-size: 13px; }
.sent-email-modal .email-meta-label { font-weight: 600; color: #64748b; min-width: 60px; display: inline-block; }
.sent-email-modal .email-body-wrap {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #e2e8f0;
    font-size: 14px;
    line-height: 1.5;
    word-break: break-word;
}
</style>
@endpush

@section('content')
<div class="outlook-page">
    <header class="outlook-topbar">
        <h1 class="outlook-title">Inbox <span class="sendgrid-badge">Education Elite</span></h1>
        <a href="{{ url('/admin') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Admin login</a>
    </header>

    <div class="server-error">@include('Elements.flash-message')</div>

    <div class="outlook-container">
        <aside class="outlook-sidebar">
            <nav class="outlook-folders">
                <div class="folder-item active"><i class="fas fa-inbox"></i> Inbox</div>
            </nav>
            <div class="elite-sidebar-foot">
                <div>Inbound POST URL (webhook):</div>
                <code>{{ url('/elite/emails') }}</code>
                <div class="mt-2">Only <strong>@educationelite.com.au</strong> senders are stored.</div>
            </div>
            <div class="simulate-panel">
                <h3><i class="fas fa-flask"></i> Simulate inbound</h3>
                <form method="post" action="{{ route('elite.emails.store') }}">
                    @csrf
                    <div class="field">
                        <label for="sim_from">From</label>
                        <input id="sim_from" name="from" type="text" required
                               value="{{ old('from', 'noreply@educationelite.com.au') }}"
                               placeholder="name@educationelite.com.au">
                    </div>
                    <div class="field">
                        <label for="sim_to">To (optional)</label>
                        <input id="sim_to" name="to" type="text" value="{{ old('to') }}" placeholder="recipient@example.com">
                    </div>
                    <div class="field">
                        <label for="sim_subject">Subject</label>
                        <input id="sim_subject" name="subject" type="text" value="{{ old('subject') }}" placeholder="Subject">
                    </div>
                    <div class="field">
                        <label for="sim_body">Body</label>
                        <textarea id="sim_body" name="text" placeholder="Plain text body">{{ old('text') }}</textarea>
                    </div>
                    <button type="submit" class="btn-submit">Create record</button>
                </form>
            </div>
        </aside>

        <main class="outlook-main mode-inbox" id="eliteMain">
            <div class="folder-view view-inbox" id="folderInbox">
                <div class="inbox-toolbar">
                    <div class="search-wrap">
                        <i class="fas fa-search"></i>
                        <input type="text" class="form-control folder-search" placeholder="Search mail...">
                    </div>
                    <select class="filter-select filter-date-range" data-folder="inbox">
                        <option value="">All time</option>
                        <option value="today">Today</option>
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="custom">Custom range</option>
                    </select>
                    <span class="filter-custom-dates filter-custom-inbox" style="display:none;">
                        <input type="date" class="filter-date filter-date-from" data-folder="inbox">
                        <input type="date" class="filter-date filter-date-to" data-folder="inbox">
                    </span>
                    <select class="filter-select filter-sort" data-folder="inbox">
                        <option value="newest">Newest first</option>
                        <option value="oldest">Oldest first</option>
                    </select>
                    <button type="button" class="btn btn-primary btn-sm ms-2 btn-fetch" data-folder="inbox">
                        <i class="fas fa-sync-alt"></i> Get Emails
                    </button>
                </div>
                @php
                    $eliteEmailMap = $emails->keyBy('id')->map(function ($row) {
                        return [
                            'from' => $row->from_address,
                            'to' => $row->to_address,
                            'subject' => $row->subject ?: '(No subject)',
                            'date' => $row->created_at->format('d/m/Y g:i A'),
                            'body' => $row->body_html ?: $row->body_text,
                        ];
                    });
                @endphp
                <script type="application/json" id="elite-emails-initial">@json($eliteEmailMap)</script>
                <ul class="email-list folder-list" id="eliteEmailList">
                    @forelse($emails as $e)
                        <li class="email-row" role="button" tabindex="0" data-id="{{ $e->id }}">
                            <span class="email-sender">{{ $e->from_address }}</span>
                            <span class="email-subject">{{ $e->subject ?: '(No subject)' }}</span>
                            <span class="email-date">{{ $e->created_at->format('d/m/Y g:i A') }}</span>
                        </li>
                    @empty
                    @endforelse
                </ul>
                <div class="empty-state folder-empty" id="eliteEmpty" style="{{ $emails->isEmpty() ? '' : 'display:none;' }}">
                    <i class="fas fa-inbox"></i>
                    <p>Your inbox is empty</p>
                    <span>POST inbound mail to this page’s webhook URL, or use “Simulate inbound” on the left. Only @educationelite.com.au senders are accepted.</span>
                </div>
            </div>
        </main>
    </div>

    <div id="eliteEmailModalOverlay" class="sent-email-modal-overlay" aria-hidden="true">
        <div class="sent-email-modal" role="dialog" aria-labelledby="eliteEmailModalTitle">
            <div class="modal-header">
                <h3 id="eliteEmailModalTitle">Message</h3>
                <button type="button" class="modal-close" id="eliteEmailModalClose" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="email-meta-row"><span class="email-meta-label">From:</span> <span id="eliteEmailFrom"></span></div>
                <div class="email-meta-row"><span class="email-meta-label">To:</span> <span id="eliteEmailTo"></span></div>
                <div class="email-meta-row"><span class="email-meta-label">Subject:</span> <span id="eliteEmailSubject"></span></div>
                <div class="email-meta-row"><span class="email-meta-label">Date:</span> <span id="eliteEmailDate"></span></div>
                <div class="email-body-wrap">
                    <div class="email-meta-label">Message</div>
                    <div id="eliteEmailBody"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var listUrl = @json(route('elite.emails.inbox'));
    var view = document.getElementById('folderInbox');
    var listEl = document.getElementById('eliteEmailList');
    var emptyEl = document.getElementById('eliteEmpty');
    var overlay = document.getElementById('eliteEmailModalOverlay');
    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
    var initialJson = document.getElementById('elite-emails-initial');
    var initialMap = {};
    try {
        initialMap = initialJson ? JSON.parse(initialJson.textContent || '{}') : {};
    } catch (err) { initialMap = {}; }

    function openModal(payload) {
        document.getElementById('eliteEmailFrom').textContent = payload.from || '';
        document.getElementById('eliteEmailTo').textContent = payload.to || '';
        document.getElementById('eliteEmailSubject').textContent = payload.subject || '';
        document.getElementById('eliteEmailDate').textContent = payload.date || '';
        var body = payload.body || '';
        var wrap = document.getElementById('eliteEmailBody');
        if (body.indexOf('<') !== -1 && body.indexOf('>') !== -1) {
            wrap.innerHTML = body;
        } else {
            wrap.textContent = body;
        }
        overlay.classList.add('show');
        overlay.setAttribute('aria-hidden', 'false');
    }
    function closeModal() {
        overlay.classList.remove('show');
        overlay.setAttribute('aria-hidden', 'true');
        document.getElementById('eliteEmailBody').innerHTML = '';
    }
    document.getElementById('eliteEmailModalClose').addEventListener('click', closeModal);
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closeModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && overlay.classList.contains('show')) closeModal();
    });

    function bindRowClicks() {
        listEl.querySelectorAll('.email-row').forEach(function(row) {
            row.addEventListener('click', function() {
                var id = row.dataset.id;
                var payload;
                if (id && initialMap[id]) {
                    payload = initialMap[id];
                } else {
                    payload = {
                        from: row.dataset.from || '',
                        to: row.dataset.to || '',
                        subject: row.dataset.subject || '',
                        date: row.dataset.date || '',
                        body: row.dataset.body || ''
                    };
                }
                openModal({
                    from: payload.from || '',
                    to: payload.to || '',
                    subject: payload.subject || '',
                    date: payload.date || '',
                    body: payload.body || ''
                });
            });
        });
    }
    bindRowClicks();

    function getDateRangeParams() {
        var rangeSel = view.querySelector('.filter-date-range');
        var fromInput = view.querySelector('.filter-date-from');
        var toInput = view.querySelector('.filter-date-to');
        if (!rangeSel) return { date_from: '', date_to: '' };
        var val = rangeSel.value;
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        var dateFrom = '', dateTo = today.toISOString().slice(0, 10);
        if (val === 'custom' && fromInput && toInput) {
            dateFrom = fromInput.value || '';
            dateTo = toInput.value || '';
        } else if (val === 'today') {
            dateFrom = dateTo = today.toISOString().slice(0, 10);
        } else if (val === '7' || val === '30') {
            var from = new Date(today);
            from.setDate(from.getDate() - parseInt(val, 10));
            dateFrom = from.toISOString().slice(0, 10);
        } else if (val === '') {
            dateFrom = '';
            dateTo = '';
        }
        return { date_from: dateFrom, date_to: dateTo };
    }

    view.querySelector('.filter-date-range').addEventListener('change', function() {
        var customSpan = view.querySelector('.filter-custom-inbox');
        if (customSpan) customSpan.style.display = this.value === 'custom' ? 'inline-flex' : 'none';
    });

    view.querySelector('.btn-fetch').addEventListener('click', function() {
        var btn = this;
        var searchInput = view.querySelector('.folder-search');
        var search = (searchInput && searchInput.value) ? encodeURIComponent(searchInput.value.trim()) : '';
        var dr = getDateRangeParams();
        var sortSel = view.querySelector('.filter-sort');
        var sort = (sortSel && sortSel.value) ? sortSel.value : 'newest';
        var params = ['folder=inbox'];
        if (search) params.push('search=' + search);
        if (dr.date_from) params.push('date_from=' + encodeURIComponent(dr.date_from));
        if (dr.date_to) params.push('date_to=' + encodeURIComponent(dr.date_to));
        params.push('sort=' + sort);
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        fetch(listUrl + '?' + params.join('&'), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': tokenMeta ? tokenMeta.getAttribute('content') : ''
            }
        })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                listEl.innerHTML = '';
                var rows = data.emails || [];
                if (rows.length === 0) {
                    emptyEl.style.display = 'flex';
                    var span = emptyEl.querySelector('span');
                    if (span) span.textContent = data.message || 'No emails found.';
                    return;
                }
                emptyEl.style.display = 'none';
                rows.forEach(function(e) {
                    var li = document.createElement('li');
                    li.className = 'email-row';
                    li.setAttribute('role', 'button');
                    li.setAttribute('tabindex', '0');
                    var body = e.body || '';
                    li.dataset.from = e.from || '';
                    li.dataset.to = e.to || '';
                    li.dataset.subject = e.subject || '(No subject)';
                    li.dataset.date = e.date || '';
                    li.dataset.body = body;
                    li.innerHTML = '<span class="email-sender">' + (e.from || '') + '</span>' +
                        '<span class="email-subject">' + (e.subject || '(No subject)') + '</span>' +
                        '<span class="email-date">' + (e.date || '') + '</span>';
                    li.addEventListener('click', function() {
                        openModal({
                            from: li.dataset.from,
                            to: li.dataset.to,
                            subject: li.dataset.subject,
                            date: li.dataset.date,
                            body: li.dataset.body
                        });
                    });
                    listEl.appendChild(li);
                });
            })
            .catch(function() {
                emptyEl.style.display = 'flex';
                var span = emptyEl.querySelector('span');
                if (span) span.textContent = 'Could not fetch emails.';
            })
            .finally(function() {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt"></i> Get Emails';
            });
    });
})();
</script>
@endpush
@endsection
