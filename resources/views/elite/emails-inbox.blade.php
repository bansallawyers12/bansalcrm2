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
    cursor: pointer;
    border: none;
    background: transparent;
    width: 100%;
    text-align: left;
    font-family: inherit;
    box-sizing: border-box;
}
.outlook-folders .folder-item:hover { background: #f3f9ff; }
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
.email-list-wrap { flex: 1; display: flex; flex-direction: column; min-height: 0; overflow: hidden; }
.email-list-header {
    display: grid;
    grid-template-columns: 36px minmax(200px, 1.1fr) minmax(120px, 2fr) 130px;
    gap: 12px;
    align-items: center;
    padding: 8px 20px;
    background: #fafafa;
    border-bottom: 1px solid #e2e8f0;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: #64748b;
}
.email-list-header .col-source { text-align: center; }
.email-list-header .col-date { text-align: right; }
.email-list { list-style: none; margin: 0; padding: 0; overflow-y: auto; flex: 1; }
.email-list .email-row {
    display: grid;
    grid-template-columns: 36px minmax(200px, 1.1fr) minmax(120px, 2fr) 130px;
    gap: 12px;
    align-items: center;
    padding: 10px 20px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
}
.email-list .email-row:hover { background: #f8fafc; }
.email-list .email-row:focus { outline: 2px solid #0078d4; outline-offset: -2px; }
.email-row .email-dir-icon {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
}
.email-row--incoming .email-dir-icon {
    background: #e8f5e9;
    color: #2e7d32;
}
.email-row--outgoing .email-dir-icon {
    background: #e3f2fd;
    color: #1565c0;
}
.email-row .email-address-block { min-width: 0; }
.email-row .email-address-label {
    display: block;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #94a3b8;
    margin-bottom: 2px;
}
.email-row .email-address-value {
    font-weight: 600;
    color: #1e293b;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.email-row .email-address-secondary {
    display: block;
    font-size: 12px;
    color: #64748b;
    margin-top: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.email-row .email-address-secondary .sec-label {
    font-weight: 600;
    color: #94a3b8;
    margin-right: 4px;
}
.inbox-fetch-error {
    display: none;
    margin: 0 20px 10px;
    padding: 10px 14px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 6px;
    color: #991b1b;
    font-size: 13px;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.inbox-fetch-error.show { display: flex; }
.inbox-fetch-error button {
    flex-shrink: 0;
    border: none;
    background: transparent;
    color: #991b1b;
    cursor: pointer;
    font-size: 12px;
    text-decoration: underline;
    padding: 0;
}
.sent-email-modal .email-body-frame {
    width: 100%;
    min-height: 220px;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    background: #fff;
}
.email-row .email-subject-block { min-width: 0; }
.email-row .email-source-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
    margin-bottom: 4px;
    background: #f1f5f9;
    color: #475569;
}
.email-row .email-subject-line {
    color: #475569;
    font-size: 13px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.email-row .email-date { color: #94a3b8; font-size: 13px; white-space: nowrap; text-align: right; }
@media (max-width: 900px) {
    .email-list-header,
    .email-list .email-row {
        grid-template-columns: 28px minmax(140px, 1fr) minmax(80px, 1.2fr) 96px;
        gap: 8px;
        padding-left: 12px;
        padding-right: 12px;
    }
}
.outlook-module-badge {
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
.elite-email-plain {
    white-space: pre-wrap;
    word-break: break-word;
    max-height: 50vh;
    overflow-y: auto;
}
</style>
@endpush

@section('content')
<div class="outlook-page">
    <header class="outlook-topbar">
        <h1 class="outlook-title">Inbox <span class="outlook-module-badge">Education Elite</span></h1>
        <a href="{{ route('dashboard') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Back to CRM</a>
    </header>

    <div class="server-error">@include('Elements.flash-message')</div>

    <div class="outlook-container">
        <aside class="outlook-sidebar">
            <nav class="outlook-folders" aria-label="Mail folders">
                <button type="button" class="folder-item active" data-folder="inbox" id="eliteFolderInbox" aria-current="page">
                    <i class="fas fa-inbox" aria-hidden="true"></i> Inbox
                </button>
                <button type="button" class="folder-item" data-folder="sent" id="eliteFolderSent">
                    <i class="fas fa-paper-plane" aria-hidden="true"></i> Sent Items
                </button>
            </nav>
            <div class="elite-sidebar-foot">
                <div>Inbound POST URL (SendGrid Inbound Parse):</div>
                <code>{{ $webhookUrl ?? url('/elite/emails') }}</code>
                <div class="mt-2">This list includes <strong>inbound</strong> (webhook) and <strong>sent/received in CRM</strong> where <strong>@<span>{{ config('crm.education_elite_sender_domain', 'educationelite.com.au') }}</span></strong> appears in From, To, or CC. Webhook posts still require the sender to be <strong>@<span>{{ config('crm.education_elite_sender_domain', 'educationelite.com.au') }}</span></strong>.</div>
                @if(config('crm.education_elite_inbound_secret'))
                    <div class="mt-2 text-success"><i class="fas fa-lock"></i> Webhook secret is enabled (URL includes <code>?secret=</code>).</div>
                @endif
            </div>
            <div class="simulate-panel">
                <h3><i class="fas fa-flask"></i> Simulate inbound</h3>
                <form method="post" action="{{ route('elite.emails.simulate') }}">
                    @csrf
                    <div class="field">
                        <label for="sim_from">From</label>
                        <input id="sim_from" name="from" type="text" required
                               value="{{ old('from', 'noreply@' . config('crm.education_elite_sender_domain', 'educationelite.com.au')) }}"
                               placeholder="name@{{ config('crm.education_elite_sender_domain', 'educationelite.com.au') }}">
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
                        <i class="fas fa-sync-alt" aria-hidden="true"></i> Get Emails
                    </button>
                </div>
                <div class="inbox-fetch-error" id="eliteFetchError" role="alert">
                    <span id="eliteFetchErrorText"></span>
                    <button type="button" id="eliteFetchErrorDismiss">Dismiss</button>
                </div>
                @php
                    $inboxList = $eliteInboxItems ?? [];
                    $eliteEmailMap = collect($inboxList)->keyBy('id');
                @endphp
                <script type="application/json" id="elite-emails-initial">@json($eliteEmailMap)</script>
                <div class="email-list-wrap" id="eliteListWrap" style="{{ count($inboxList) ? '' : 'display:none;' }}">
                    <div class="email-list-header" id="eliteListHeader" aria-hidden="false">
                        <span class="col-icon"></span>
                        <span class="col-address" id="eliteHeaderAddress">From</span>
                        <span class="col-subject">Subject</span>
                        <span class="col-date" id="eliteHeaderDate">Received</span>
                    </div>
                    <ul class="email-list folder-list" id="eliteEmailList">
                        @forelse($inboxList as $e)
                            @php
                                $dir = (string) ($e['direction'] ?? '');
                                $incoming = in_array($dir, ['inbound', 'inbox'], true);
                                $rowClass = $incoming ? 'email-row--incoming' : 'email-row--outgoing';
                                $iconClass = $incoming ? 'fa-arrow-down' : 'fa-arrow-up';
                                $primaryLabel = 'From';
                                $primaryAddr = $e['from'] ?? '—';
                                $subj = ($e['subject'] ?? '') !== '' ? $e['subject'] : '(No subject)';
                                $dlabel = $e['direction_label'] ?? '';
                            @endphp
                            <li class="email-row {{ $rowClass }}" role="button" tabindex="0" data-id="{{ $e['id'] }}"
                                data-direction="{{ e($dir) }}"
                                data-direction-label="{{ e($dlabel) }}">
                                <span class="email-dir-icon" title="{{ $incoming ? 'Incoming' : 'Outgoing' }}"><i class="fas {{ $iconClass }}"></i></span>
                                <div class="email-address-block">
                                    <span class="email-address-label">{{ $primaryLabel }}</span>
                                    <span class="email-address-value">{{ $primaryAddr }}</span>
                                    @if($incoming && ($e['to'] ?? '') !== '')
                                        <span class="email-address-secondary"><span class="sec-label">To</span>{{ $e['to'] }}</span>
                                    @elseif(! $incoming && ($e['from'] ?? '') !== '')
                                        <span class="email-address-secondary"><span class="sec-label">From</span>{{ $e['from'] }}</span>
                                    @endif
                                </div>
                                <div class="email-subject-block">
                                    @if($dlabel !== '')
                                        <span class="email-source-badge">{{ $dlabel }}</span>
                                    @endif
                                    <div class="email-subject-line">{{ $subj }}</div>
                                </div>
                                <span class="email-date">{{ $e['date'] ?? '' }}</span>
                            </li>
                        @empty
                        @endforelse
                    </ul>
                </div>
                <div class="empty-state folder-empty" id="eliteEmpty" style="{{ count($inboxList) ? 'display:none;' : '' }}">
                    <i class="fas fa-inbox" id="eliteEmptyIcon"></i>
                    <p id="eliteEmptyTitle">No incoming messages</p>
                    <span id="eliteEmptyHint">Send or receive mail for <strong>{{ '@' . config('crm.education_elite_sender_domain', 'educationelite.com.au') }}</strong> in the CRM, post inbound to the webhook, or use “Simulate inbound”.</span>
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
                <div class="email-meta-row" id="eliteEmailTypeRow"><span class="email-meta-label">Type:</span> <span id="eliteEmailType"></span></div>
                <div class="email-meta-row"><span class="email-meta-label">From:</span> <span id="eliteEmailFrom"></span></div>
                <div class="email-meta-row"><span class="email-meta-label">To:</span> <span id="eliteEmailTo"></span></div>
                <div class="email-meta-row"><span class="email-meta-label">Subject:</span> <span id="eliteEmailSubject"></span></div>
                <div class="email-meta-row"><span class="email-meta-label">Date:</span> <span id="eliteEmailDate"></span></div>
                <div class="email-body-wrap">
                    <div class="email-meta-label">Message</div>
                    <div id="eliteEmailBody" class="elite-email-plain"></div>
                    <iframe id="eliteEmailBodyFrame" class="email-body-frame" title="HTML message" sandbox="" style="display:none;"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var listUrl = @json(route('elite.emails.inbox'));
    var activeFolder = @json($eliteInitialFolder ?? 'inbox');
    var view = document.getElementById('folderInbox');
    var listEl = document.getElementById('eliteEmailList');
    var listWrap = document.getElementById('eliteListWrap');
    var emptyEl = document.getElementById('eliteEmpty');
    var emptyIcon = document.getElementById('eliteEmptyIcon');
    var emptyTitle = document.getElementById('eliteEmptyTitle');
    var emptyHint = document.getElementById('eliteEmptyHint');
    var overlay = document.getElementById('eliteEmailModalOverlay');
    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
    var initialJson = document.getElementById('elite-emails-initial');
    var initialMap = {};
    try {
        initialMap = initialJson ? JSON.parse(initialJson.textContent || '{}') : {};
    } catch (err) { initialMap = {}; }

    var fetchErrEl = document.getElementById('eliteFetchError');
    var fetchErrText = document.getElementById('eliteFetchErrorText');
    function showFetchError(msg) {
        if (fetchErrText) fetchErrText.textContent = msg;
        if (fetchErrEl) fetchErrEl.classList.add('show');
    }
    function clearFetchError() {
        if (fetchErrEl) fetchErrEl.classList.remove('show');
    }
    var fetchErrDismiss = document.getElementById('eliteFetchErrorDismiss');
    if (fetchErrDismiss) fetchErrDismiss.addEventListener('click', clearFetchError);

    function safeSrcdoc(html) {
        return String(html).replace(/<\/iframe/gi, '<\\/iframe');
    }
    function looksLikeHtml(body) {
        if (!body || typeof body !== 'string') return false;
        var t = body.trim();
        return t.indexOf('<') !== -1 && t.indexOf('>') !== -1;
    }

    function openModal(payload) {
        var typeRow = document.getElementById('eliteEmailTypeRow');
        var typeEl = document.getElementById('eliteEmailType');
        var t = payload.direction_label || '';
        if (typeRow && typeEl) {
            typeEl.textContent = t;
            typeRow.style.display = t ? '' : 'none';
        }
        document.getElementById('eliteEmailFrom').textContent = payload.from || '';
        document.getElementById('eliteEmailTo').textContent = payload.to || '';
        document.getElementById('eliteEmailSubject').textContent = payload.subject || '';
        document.getElementById('eliteEmailDate').textContent = payload.date || '';
        var body = payload.body || '';
        var plainEl = document.getElementById('eliteEmailBody');
        var frame = document.getElementById('eliteEmailBodyFrame');
        if (plainEl && frame) {
            if (looksLikeHtml(body)) {
                plainEl.textContent = '';
                plainEl.style.display = 'none';
                frame.style.display = 'block';
                frame.srcdoc = safeSrcdoc(body);
            } else {
                frame.srcdoc = '';
                frame.style.display = 'none';
                plainEl.style.display = '';
                plainEl.textContent = body;
            }
        }
        overlay.classList.add('show');
        overlay.setAttribute('aria-hidden', 'false');
    }
    function closeModal() {
        overlay.classList.remove('show');
        overlay.setAttribute('aria-hidden', 'true');
        var frame = document.getElementById('eliteEmailBodyFrame');
        if (frame) {
            frame.srcdoc = '';
            frame.style.display = 'none';
        }
        var plainEl = document.getElementById('eliteEmailBody');
        if (plainEl) {
            plainEl.textContent = '';
            plainEl.style.display = '';
        }
    }
    document.getElementById('eliteEmailModalClose').addEventListener('click', closeModal);
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closeModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && overlay.classList.contains('show')) closeModal();
    });

    function updateListHeader(folder) {
        var addr = document.getElementById('eliteHeaderAddress');
        var dat = document.getElementById('eliteHeaderDate');
        if (!addr || !dat) return;
        if (folder === 'sent') {
            addr.textContent = 'To';
            dat.textContent = 'Sent';
        } else {
            addr.textContent = 'From';
            dat.textContent = 'Received';
        }
    }

    function showEmptyState(folder, message) {
        if (listWrap) listWrap.style.display = 'none';
        emptyEl.style.display = 'flex';
        if (emptyIcon) {
            emptyIcon.className = 'fas ' + (folder === 'sent' ? 'fa-paper-plane' : 'fa-inbox');
        }
        if (emptyTitle) emptyTitle.textContent = folder === 'sent' ? 'No sent messages' : 'No incoming messages';
        if (emptyHint) emptyHint.textContent = message || '';
    }

    function hideEmptyState() {
        if (listWrap) listWrap.style.display = '';
        emptyEl.style.display = 'none';
    }

    function buildEmailRow(e, folder) {
        var li = document.createElement('li');
        var direction = e.direction || '';
        var incoming = folder !== 'sent' && (direction === 'inbound' || direction === 'inbox');
        li.className = 'email-row ' + (incoming ? 'email-row--incoming' : 'email-row--outgoing');
        li.setAttribute('role', 'button');
        li.setAttribute('tabindex', '0');
        li.dataset.id = e.id || '';
        li.dataset.direction = direction;
        li.dataset.directionLabel = e.direction_label || '';

        var iconWrap = document.createElement('span');
        iconWrap.className = 'email-dir-icon';
        iconWrap.title = incoming ? 'Incoming' : 'Outgoing';
        var ic = document.createElement('i');
        ic.className = 'fas ' + (incoming ? 'fa-arrow-down' : 'fa-arrow-up');
        iconWrap.appendChild(ic);

        var addrBlock = document.createElement('div');
        addrBlock.className = 'email-address-block';
        var addrLabel = document.createElement('span');
        addrLabel.className = 'email-address-label';
        var addrVal = document.createElement('span');
        addrVal.className = 'email-address-value';
        if (folder === 'sent') {
            addrLabel.textContent = 'To';
            addrVal.textContent = e.to || '—';
        } else {
            addrLabel.textContent = 'From';
            addrVal.textContent = e.from || '—';
        }
        addrBlock.appendChild(addrLabel);
        addrBlock.appendChild(addrVal);
        if (folder === 'sent' && e.from) {
            var secF = document.createElement('span');
            secF.className = 'email-address-secondary';
            var secFL = document.createElement('span');
            secFL.className = 'sec-label';
            secFL.textContent = 'From';
            secF.appendChild(secFL);
            secF.appendChild(document.createTextNode(e.from));
            addrBlock.appendChild(secF);
        } else if (folder !== 'sent' && e.to) {
            var secT = document.createElement('span');
            secT.className = 'email-address-secondary';
            var secTL = document.createElement('span');
            secTL.className = 'sec-label';
            secTL.textContent = 'To';
            secT.appendChild(secTL);
            secT.appendChild(document.createTextNode(e.to));
            addrBlock.appendChild(secT);
        }

        var subjBlock = document.createElement('div');
        subjBlock.className = 'email-subject-block';
        var dlabel = e.direction_label || '';
        if (dlabel) {
            var badge = document.createElement('span');
            badge.className = 'email-source-badge';
            badge.textContent = dlabel;
            subjBlock.appendChild(badge);
        }
        var subjLine = document.createElement('div');
        subjLine.className = 'email-subject-line';
        subjLine.textContent = (e.subject && String(e.subject).length) ? e.subject : '(No subject)';
        subjBlock.appendChild(subjLine);

        var dateSpan = document.createElement('span');
        dateSpan.className = 'email-date';
        dateSpan.textContent = e.date || '';

        li.appendChild(iconWrap);
        li.appendChild(addrBlock);
        li.appendChild(subjBlock);
        li.appendChild(dateSpan);
        return li;
    }

    listEl.addEventListener('click', function(ev) {
        var row = ev.target.closest('.email-row');
        if (!row) return;
        var id = row.dataset.id;
        var payload = (id && initialMap[id]) ? initialMap[id] : null;
        if (payload) {
            openModal({
                from: payload.from || '',
                to: payload.to || '',
                subject: (payload.subject && String(payload.subject).length) ? payload.subject : '(No subject)',
                date: payload.date || '',
                body: payload.body || '',
                direction_label: payload.direction_label || ''
            });
        }
    });
    listEl.addEventListener('keydown', function(ev) {
        if (ev.key !== 'Enter' && ev.key !== ' ') return;
        var row = ev.target.closest('.email-row');
        if (!row || !listEl.contains(row)) return;
        ev.preventDefault();
        row.click();
    });

    function syncFolderAria(activeBtn) {
        document.querySelectorAll('.outlook-folders .folder-item[data-folder]').forEach(function(b) {
            if (b === activeBtn) {
                b.setAttribute('aria-current', 'page');
            } else {
                b.removeAttribute('aria-current');
            }
        });
    }
    var initialFolderBtn = document.querySelector('.outlook-folders .folder-item.active[data-folder]');
    if (initialFolderBtn) syncFolderAria(initialFolderBtn);

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

    function fetchEmails() {
        clearFetchError();
        var btn = view.querySelector('.btn-fetch');
        var searchInput = view.querySelector('.folder-search');
        var search = (searchInput && searchInput.value) ? encodeURIComponent(searchInput.value.trim()) : '';
        var dr = getDateRangeParams();
        var sortSel = view.querySelector('.filter-sort');
        var sort = (sortSel && sortSel.value) ? sortSel.value : 'newest';
        var params = ['folder=' + encodeURIComponent(activeFolder)];
        if (search) params.push('search=' + search);
        if (dr.date_from) params.push('date_from=' + encodeURIComponent(dr.date_from));
        if (dr.date_to) params.push('date_to=' + encodeURIComponent(dr.date_to));
        params.push('sort=' + sort);
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Loading...';
        fetch(listUrl + '?' + params.join('&'), {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': tokenMeta ? tokenMeta.getAttribute('content') : ''
            }
        })
            .then(function(r) {
                if (!r.ok) {
                    throw new Error('Server returned ' + r.status);
                }
                return r.json();
            })
            .then(function(data) {
                clearFetchError();
                if (data.folder) activeFolder = data.folder;
                updateListHeader(activeFolder);
                listEl.innerHTML = '';
                initialMap = {};
                var rows = data.emails || [];
                rows.forEach(function(e) {
                    if (e.id) initialMap[e.id] = e;
                });
                if (rows.length === 0) {
                    showEmptyState(activeFolder, data.message || '');
                    return;
                }
                hideEmptyState();
                rows.forEach(function(e) {
                    listEl.appendChild(buildEmailRow(e, activeFolder));
                });
            })
            .catch(function() {
                showFetchError('Could not refresh the list. Check your connection or try again.');
            })
            .finally(function() {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt" aria-hidden="true"></i> Get Emails';
            });
    }

    document.querySelectorAll('.outlook-folders .folder-item[data-folder]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            activeFolder = btn.getAttribute('data-folder') || 'inbox';
            document.querySelectorAll('.outlook-folders .folder-item[data-folder]').forEach(function(b) {
                b.classList.toggle('active', b === btn);
            });
            syncFolderAria(btn);
            updateListHeader(activeFolder);
            fetchEmails();
        });
    });

    view.querySelector('.btn-fetch').addEventListener('click', fetchEmails);

    updateListHeader(activeFolder);
})();
</script>
@endpush
@endsection
