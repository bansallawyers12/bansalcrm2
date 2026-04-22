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
    width: 252px;
    min-width: 220px;
    max-width: 300px;
    background: #fff;
    border-right: 1px solid #d4d4d4;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.outlook-triple { flex: 1; display: flex; min-width: 0; min-height: 0; }
.outlook-list-col {
    width: 400px;
    min-width: 300px;
    max-width: 50%;
    display: flex;
    flex-direction: column;
    background: #fff;
    border-right: 1px solid #d4d4d4;
    overflow: hidden;
}
.outlook-reading {
    flex: 1;
    min-width: 280px;
    display: flex;
    flex-direction: column;
    background: #fff;
    overflow: hidden;
}
.outlook-account-tree { padding: 8px 0; flex: 1; overflow-y: auto; }
.elite-account { border-bottom: 1px solid #f1f5f9; }
.elite-account-header {
    font-size: 12px;
    font-weight: 600;
    color: #334155;
    padding: 8px 14px 4px 14px;
    word-break: break-all;
    line-height: 1.35;
}
.elite-account .elite-account-folders { padding: 0 0 6px; }
.outlook-folders .folder-item,
.outlook-folders .folder-item--nested {
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
.outlook-folders .folder-item--nested {
    padding: 6px 14px 6px 28px;
    font-size: 12.5px;
}
.outlook-folders .folder-item:hover,
.outlook-folders .folder-item--nested:hover { background: #f3f9ff; }
.outlook-folders .folder-item.active,
.outlook-folders .folder-item--nested.active { background: #e5f3ff; color: #0078d4; font-weight: 600; }
.outlook-folders .folder-item i { font-size: 14px; width: 18px; text-align: center; }
.elite-account.is-picked > .elite-account-header { color: #0078d4; }
.elite-sidebar-foot {
    border-top: 1px solid #e2e8f0;
    padding: 12px 14px;
    background: #fafafa;
    font-size: 12px;
    color: #64748b;
}
.elite-sidebar-foot code { font-size: 11px; word-break: break-all; }
.outlook-main { flex: 1; display: flex; min-width: 0; min-height: 0; }
.folder-view { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-height: 0; }
.inbox-list-heading {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    margin-right: 8px;
    white-space: nowrap;
}
.outlook-reading-empty {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    text-align: center;
    padding: 32px 24px;
}
.outlook-reading-empty i { font-size: 40px; margin-bottom: 12px; opacity: 0.5; }
.outlook-reading-content { flex: 1; min-height: 0; display: flex; flex-direction: column; overflow: hidden; }
.outlook-reading-scroll { flex: 1; overflow-y: auto; padding: 20px 24px; }
.outlook-read-subject { font-size: 18px; font-weight: 600; color: #0f172a; margin: 0 0 12px; line-height: 1.3; }
.outlook-read-meta { font-size: 13px; color: #475569; }
.outlook-read-meta > div { margin-bottom: 6px; }
.outlook-read-type-label { display: inline-block; margin-bottom: 8px; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 4px; background: #f1f5f9; color: #475569; }
.outlook-read-meta .email-meta-label { font-weight: 600; color: #64748b; min-width: 48px; display: inline-block; }
.outlook-read-body { margin-top: 16px; font-size: 14px; line-height: 1.5; color: #1e293b; word-break: break-word; }
.outlook-read-body.elite-email-plain { white-space: pre-wrap; }
.outlook-read-frame {
    width: 100%;
    flex: 1;
    min-height: 200px;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    background: #fff;
    margin-top: 12px;
}
.folder-view .inbox-toolbar {
    padding: 10px 14px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
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
.elite-message-list { list-style: none; margin: 0; padding: 0; overflow-y: auto; flex: 1; }
.elite-msg-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px 16px 10px 14px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    border-left: 3px solid transparent;
}
.elite-msg-item:hover { background: #f8fafc; }
.elite-msg-item.is-selected { background: #eef6fc; border-left-color: #0078d4; }
.elite-msg-item:focus { outline: 2px solid #0078d4; outline-offset: -2px; }
.elite-msg-item .email-dir-icon {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
    margin-top: 2px;
}
.email-row--incoming .email-dir-icon {
    background: #e8f5e9;
    color: #2e7d32;
}
.elite-msg-main { flex: 1; min-width: 0; }
.elite-msg-line1 {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 4px;
}
.elite-msg-addr {
    font-weight: 600;
    font-size: 13.5px;
    color: #0f172a;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.elite-msg-when { font-size: 12px; color: #94a3b8; flex-shrink: 0; }
.elite-msg-subj {
    font-size: 13px;
    color: #64748b;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
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
@media (max-width: 1200px) {
    .outlook-list-col { max-width: 100%; }
    .outlook-reading { min-width: 0; }
}
@media (max-width: 900px) {
    .outlook-triple { flex-direction: column; }
    .outlook-list-col { width: 100% !important; max-width: none; border-right: none; border-bottom: 1px solid #e2e8f0; min-height: 40vh; }
    .outlook-reading { min-height: 30vh; }
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
.elite-read-plain {
    white-space: pre-wrap;
    word-break: break-word;
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
            <nav class="outlook-folders outlook-account-tree" id="eliteAccountTree" aria-label="Mailboxes and folders">
                @php
                    $activeAcct = $eliteInitialAccount ?? 'all';
                @endphp
                <div class="elite-account{{ $activeAcct === 'all' ? ' is-picked' : '' }}" data-account="all" id="eliteAccountAll">
                    <div class="elite-account-header">All mailboxes</div>
                    <div class="elite-account-folders">
                        <button type="button" class="folder-item--nested{{ $activeAcct === 'all' ? ' active' : '' }}"
                            data-account="all"
                            {!! $activeAcct === 'all' ? 'aria-current="page"' : '' !!}>
                            <i class="fas fa-inbox" aria-hidden="true"></i> Inbox
                        </button>
                    </div>
                </div>
                @foreach($eliteMailboxes ?? [] as $mbox)
                    @php $mActive = $activeAcct === $mbox; @endphp
                    <div class="elite-account{{ $mActive ? ' is-picked' : '' }}" data-account="{{ e($mbox) }}">
                        <div class="elite-account-header" title="{{ e($mbox) }}">{{ e($mbox) }}</div>
                        <div class="elite-account-folders">
                            <button type="button" class="folder-item--nested{{ $mActive ? ' active' : '' }}"
                                data-account="{{ e($mbox) }}"
                                {!! $mActive ? 'aria-current="page"' : '' !!}>
                                <i class="fas fa-inbox" aria-hidden="true"></i> Inbox
                            </button>
                        </div>
                    </div>
                @endforeach
            </nav>
            <div class="elite-sidebar-foot">
                <div>Inbound POST URL (SendGrid Inbound Parse):</div>
                <code>{{ $webhookUrl ?? url('/elite/emails') }}</code>
                <div class="mt-2">This list shows only <strong>SendGrid Inbound Parse</strong> messages stored via the webhook. Outbound/CRM email is not included. Inbound <strong>From</strong> must be <strong>@<span>{{ config('crm.education_elite_sender_domain', 'educationelite.com.au') }}</span></strong> (see controller rules).</div>
                @if(config('crm.education_elite_inbound_secret'))
                    <div class="mt-2 text-success"><i class="fas fa-lock"></i> Webhook secret is enabled (URL includes <code>?secret=</code>).</div>
                @endif
            </div>
        </aside>

        <div class="outlook-triple" id="eliteTriple">
            <main class="outlook-list-col outlook-main" id="eliteMain">
                <div class="folder-view view-inbox" id="folderInbox">
                    <div class="inbox-toolbar">
                        <span class="inbox-list-heading" id="eliteListTitle">Inbox</span>
                        <div class="search-wrap" style="flex:1;min-width:100px;max-width:200px;">
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
                        <button type="button" class="btn btn-primary btn-sm ms-1 btn-fetch" data-folder="inbox">
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
                        <ul class="elite-message-list" id="eliteEmailList" role="listbox" aria-label="Message list" aria-multiselectable="false">
                            @forelse($inboxList as $e)
                                @php
                                    $lineAddr = $e['from'] ?? '—';
                                    $subj = ($e['subject'] ?? '') !== '' ? $e['subject'] : '(No subject)';
                                @endphp
                                <li class="elite-msg-item email-row--incoming"
                                    role="option"
                                    tabindex="0"
                                    data-id="{{ $e['id'] }}">
                                    <span class="email-dir-icon" title="Incoming"><i class="fas fa-arrow-down"></i></span>
                                    <div class="elite-msg-main">
                                        <div class="elite-msg-line1">
                                            <span class="elite-msg-addr">{{ $lineAddr }}</span>
                                            <span class="elite-msg-when">{{ $e['date'] ?? '' }}</span>
                                        </div>
                                        <div class="elite-msg-subj">{{ $subj }}</div>
                                    </div>
                                </li>
                            @empty
                            @endforelse
                        </ul>
                    </div>
                    <div class="empty-state folder-empty" id="eliteEmpty" style="{{ count($inboxList) ? 'display:none;' : '' }}">
                        <i class="fas fa-inbox" id="eliteEmptyIcon"></i>
                        <p id="eliteEmptyTitle">No incoming messages</p>
                        <span id="eliteEmptyHint">Point SendGrid Inbound Parse at the webhook and send from <strong>{{ '@' . config('crm.education_elite_sender_domain', 'educationelite.com.au') }}</strong> per the rules.</span>
                    </div>
                </div>
            </main>
            <aside class="outlook-reading" id="eliteReadingPane" aria-label="Reading pane">
                <div class="outlook-reading-empty" id="eliteReadingEmpty">
                    <i class="fas fa-envelope-open" aria-hidden="true"></i>
                    <p>Select a message to read it here.</p>
                </div>
                <div class="outlook-reading-content" id="eliteReadingContent" style="display:none" aria-live="polite">
                    <div class="outlook-reading-scroll" id="eliteReadingScroll">
                        <div class="outlook-read-type-row" id="eliteReadTypeRow" style="display:none">
                            <span class="outlook-read-type-label" id="eliteReadType"></span>
                        </div>
                        <h2 class="outlook-read-subject" id="eliteReadSubject"></h2>
                        <div class="outlook-read-meta" id="eliteReadMeta">
                            <div><span class="email-meta-label">From:</span> <span id="eliteReadFrom"></span></div>
                            <div><span class="email-meta-label">To:</span> <span id="eliteReadTo"></span></div>
                            <div><span class="email-meta-label">Date:</span> <span id="eliteReadDate"></span></div>
                        </div>
                        <div class="outlook-read-body elite-read-plain" id="eliteReadBody"></div>
                        <iframe id="eliteReadFrame" class="outlook-read-frame" title="HTML message" sandbox="allow-same-origin" style="display:none;"></iframe>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    'use strict';
    var listUrl = @json(route('elite.emails.inbox'));
    var activeAccount = @json($eliteInitialAccount ?? 'all');

    var view       = document.getElementById('folderInbox');
    if (!view) return;
    var listEl     = document.getElementById('eliteEmailList');
    if (!listEl) return;
    var listWrap   = document.getElementById('eliteListWrap');
    var emptyEl    = document.getElementById('eliteEmpty');
    var emptyIcon  = document.getElementById('eliteEmptyIcon');
    var emptyTitle = document.getElementById('eliteEmptyTitle');
    var emptyHint  = document.getElementById('eliteEmptyHint');
    var listTitle  = document.getElementById('eliteListTitle');
    var readingEmpty   = document.getElementById('eliteReadingEmpty');
    var readingContent = document.getElementById('eliteReadingContent');
    var readingScroll  = document.getElementById('eliteReadingScroll');
    var tokenMeta  = document.querySelector('meta[name="csrf-token"]');

    var initialJson = document.getElementById('elite-emails-initial');
    var initialMap  = {};
    try { initialMap = initialJson ? JSON.parse(initialJson.textContent || '{}') : {}; }
    catch (e) { initialMap = {}; }

    // ── error bar ────────────────────────────────────────────────────────────
    var fetchErrEl   = document.getElementById('eliteFetchError');
    var fetchErrText = document.getElementById('eliteFetchErrorText');
    function showFetchError(msg) {
        if (fetchErrText) fetchErrText.textContent = msg;
        if (fetchErrEl)   fetchErrEl.classList.add('show');
    }
    function clearFetchError() {
        if (fetchErrEl) fetchErrEl.classList.remove('show');
    }
    var dismissBtn = document.getElementById('eliteFetchErrorDismiss');
    if (dismissBtn) dismissBtn.addEventListener('click', clearFetchError);

    // ── HTML body detection (proper tag regex, not just < >) ─────────────────
    function safeSrcdoc(html) {
        return String(html).replace(/<\/iframe/gi, '<\\/iframe');
    }
    var HTML_TAG_RE = /<([a-z][a-z0-9]*)\b[^>]*>/i;
    function looksLikeHtml(body) {
        return typeof body === 'string' && HTML_TAG_RE.test(body.trim());
    }

    // ── reading pane ─────────────────────────────────────────────────────────
    function clearReading() {
        if (readingContent) readingContent.style.display = 'none';
        if (readingEmpty)   readingEmpty.style.display   = 'flex';
        var frame  = document.getElementById('eliteReadFrame');
        var bodyEl = document.getElementById('eliteReadBody');
        if (frame)  { frame.srcdoc = '';   frame.style.display  = 'none'; }
        if (bodyEl) { bodyEl.textContent = ''; bodyEl.style.display = ''; }
        listEl.querySelectorAll('.elite-msg-item.is-selected')
              .forEach(function(n) { n.classList.remove('is-selected'); });
    }

    function showReading(payload) {
        if (readingEmpty)   readingEmpty.style.display   = 'none';
        if (readingContent) readingContent.style.display = 'flex';

        // scroll to top so a long previous email doesn't leave position mid-way
        if (readingScroll) readingScroll.scrollTop = 0;

        var typeRow = document.getElementById('eliteReadTypeRow');
        var typeEl  = document.getElementById('eliteReadType');
        if (typeRow && typeEl) {
            typeEl.textContent        = payload.direction_label || '';
            typeRow.style.display     = payload.direction_label ? 'block' : 'none';
        }

        var subjEl = document.getElementById('eliteReadSubject');
        if (subjEl) subjEl.textContent = payload.subject || '(No subject)';

        document.getElementById('eliteReadFrom').textContent = payload.from || '';
        document.getElementById('eliteReadTo').textContent   = payload.to   || '';
        document.getElementById('eliteReadDate').textContent = payload.date  || '';

        var body    = payload.body || '';
        var plainEl = document.getElementById('eliteReadBody');
        var frame   = document.getElementById('eliteReadFrame');
        if (plainEl && frame) {
            if (looksLikeHtml(body)) {
                plainEl.textContent   = ''; plainEl.style.display = 'none';
                frame.style.display   = 'block';
                frame.srcdoc          = safeSrcdoc(body);
            } else {
                frame.srcdoc         = ''; frame.style.display   = 'none';
                plainEl.style.display = '';
                plainEl.textContent   = body;
            }
        }
    }

    // ── list title ───────────────────────────────────────────────────────────
    function updateListTitle() {
        if (!listTitle) return;
        listTitle.textContent = (activeAccount && activeAccount !== 'all')
            ? 'Inbox \u2014 ' + activeAccount
            : 'Inbox';
    }

    // ── empty / non-empty states ──────────────────────────────────────────────
    function showEmptyState(message) {
        clearReading();
        if (listWrap)   listWrap.style.display = 'none';
        if (!emptyEl)   return;
        emptyEl.style.display = 'flex';
        if (emptyIcon)  emptyIcon.className = 'fas fa-inbox';
        if (emptyTitle) emptyTitle.textContent = 'No incoming messages';
        if (emptyHint)  emptyHint.textContent  = message || '';
    }
    function hideEmptyState() {
        if (listWrap) listWrap.style.display = '';
        if (emptyEl)  emptyEl.style.display  = 'none';
    }

    // ── build list row (JS, after fetch) ────────────────────────────────────
    function buildEmailRow(e) {
        var li = document.createElement('li');
        li.className = 'elite-msg-item email-row--incoming';
        li.setAttribute('role', 'option');
        li.setAttribute('tabindex', '0');
        li.dataset.id = e.id || '';

        var iconWrap = document.createElement('span');
        iconWrap.className = 'email-dir-icon';
        iconWrap.title = 'Incoming';
        var ic = document.createElement('i');
        ic.className = 'fas fa-arrow-down';
        iconWrap.appendChild(ic);

        var main  = document.createElement('div');
        main.className = 'elite-msg-main';

        var line1 = document.createElement('div');
        line1.className = 'elite-msg-line1';
        var addr = document.createElement('span');
        addr.className   = 'elite-msg-addr';
        addr.textContent = e.from || '—';
        var when = document.createElement('span');
        when.className   = 'elite-msg-when';
        when.textContent = e.date || '';
        line1.appendChild(addr);
        line1.appendChild(when);
        main.appendChild(line1);

        var subj = document.createElement('div');
        subj.className   = 'elite-msg-subj';
        subj.textContent = (e.subject && String(e.subject).length) ? e.subject : '(No subject)';
        main.appendChild(subj);

        li.appendChild(iconWrap);
        li.appendChild(main);
        return li;
    }

    // ── row activation ───────────────────────────────────────────────────────
    function onRowActivate(row) {
        if (!row || !listEl.contains(row)) return;
        var payload = initialMap[row.dataset.id];
        if (!payload) return;
        listEl.querySelectorAll('.elite-msg-item.is-selected')
              .forEach(function(n) { n.classList.remove('is-selected'); });
        row.classList.add('is-selected');
        showReading({
            from:            payload.from            || '',
            to:              payload.to              || '',
            subject:         payload.subject         || '',
            date:            payload.date            || '',
            body:            payload.body            || '',
            direction_label: payload.direction_label || ''
        });
    }

    listEl.addEventListener('click', function(ev) {
        var row = ev.target.closest('.elite-msg-item');
        if (row) onRowActivate(row);
    });
    listEl.addEventListener('keydown', function(ev) {
        if (ev.key !== 'Enter' && ev.key !== ' ') return;
        var row = ev.target.closest('.elite-msg-item');
        if (!row || !listEl.contains(row)) return;
        ev.preventDefault();
        onRowActivate(row);
    });

    // ── sidebar account tree ─────────────────────────────────────────────────
    function syncAccountTree(activeBtn) {
        document.querySelectorAll('#eliteAccountTree .folder-item--nested').forEach(function(b) {
            var isActive = (b === activeBtn);
            b.classList.toggle('active', isActive);
            if (isActive) b.setAttribute('aria-current', 'page');
            else          b.removeAttribute('aria-current');
        });
        if (!activeBtn) return;
        var acc = activeBtn.getAttribute('data-account') || 'all';
        document.querySelectorAll('#eliteAccountTree .elite-account').forEach(function(block) {
            block.classList.toggle('is-picked', (block.getAttribute('data-account') || 'all') === acc);
        });
    }

    var initialBtn = document.querySelector('#eliteAccountTree .folder-item--nested.active');
    if (initialBtn) syncAccountTree(initialBtn);

    document.querySelectorAll('#eliteAccountTree .folder-item--nested').forEach(function(btn) {
        btn.addEventListener('click', function() {
            activeAccount = btn.getAttribute('data-account') || 'all';
            syncAccountTree(btn);
            updateListTitle();
            fetchEmails();
        });
    });

    // ── date range helpers ────────────────────────────────────────────────────
    var dateRangeSel = view.querySelector('.filter-date-range');
    var customSpan   = view.querySelector('.filter-custom-inbox');
    if (dateRangeSel) {
        dateRangeSel.addEventListener('change', function() {
            if (customSpan) customSpan.style.display = this.value === 'custom' ? 'inline-flex' : 'none';
        });
    }

    function getDateRangeParams() {
        if (!dateRangeSel) return { date_from: '', date_to: '' };
        var val       = dateRangeSel.value;
        var fromInput = view.querySelector('.filter-date-from');
        var toInput   = view.querySelector('.filter-date-to');
        var today     = new Date();
        today.setHours(0, 0, 0, 0);
        var todayStr = today.toISOString().slice(0, 10);
        if (val === 'custom' && fromInput && toInput) {
            return { date_from: fromInput.value || '', date_to: toInput.value || '' };
        }
        if (val === 'today')  return { date_from: todayStr, date_to: todayStr };
        if (val === '7' || val === '30') {
            var from = new Date(today);
            from.setDate(from.getDate() - parseInt(val, 10));
            return { date_from: from.toISOString().slice(0, 10), date_to: todayStr };
        }
        return { date_from: '', date_to: '' };
    }

    // ── fetch ─────────────────────────────────────────────────────────────────
    function fetchEmails() {
        clearFetchError();
        clearReading();
        var btn = view.querySelector('.btn-fetch');
        if (!btn) return;

        var searchInput = view.querySelector('.folder-search');
        var search      = (searchInput && searchInput.value.trim()) ? encodeURIComponent(searchInput.value.trim()) : '';
        var dr          = getDateRangeParams();
        var sortSel     = view.querySelector('.filter-sort');
        var sort        = (sortSel && sortSel.value) || 'newest';

        var params = ['folder=inbox'];
        if (search)          params.push('search='    + search);
        if (dr.date_from)    params.push('date_from=' + encodeURIComponent(dr.date_from));
        if (dr.date_to)      params.push('date_to='   + encodeURIComponent(dr.date_to));
        params.push('sort='    + sort);
        params.push('account=' + encodeURIComponent(activeAccount || 'all'));

        btn.disabled   = true;
        btn.innerHTML  = '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Loading...';

        fetch(listUrl + '?' + params.join('&'), {
            credentials: 'same-origin',
            headers: {
                'Accept':           'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN':     tokenMeta ? tokenMeta.getAttribute('content') : ''
            }
        })
        .then(function(r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function(data) {
            clearFetchError();
            if (data && data.account) activeAccount = data.account;
            updateListTitle();

            listEl.innerHTML = '';
            initialMap = {};
            var rows = data.emails || [];
            rows.forEach(function(e) { if (e.id) initialMap[e.id] = e; });

            if (rows.length === 0) {
                showEmptyState(data.message || '');
            } else {
                hideEmptyState();
                rows.forEach(function(e) { listEl.appendChild(buildEmailRow(e)); });
            }
        })
        .catch(function() {
            showFetchError('Could not refresh. Check your connection and try again.');
        })
        .finally(function() {
            btn.disabled  = false;
            btn.innerHTML = '<i class="fas fa-sync-alt" aria-hidden="true"></i> Get Emails';
        });
    }

    var fetchBtn = view.querySelector('.btn-fetch');
    if (fetchBtn) fetchBtn.addEventListener('click', fetchEmails);

    updateListTitle();
})();
</script>
@endpush
@endsection
