@extends('layouts.outlook')
@section('title', 'Education Elite — Email')

@push('styles')
<style>
/* ── Identical base to Admin Outlook page ───────────────────────────────── */
.outlook-page { min-height: 100vh; background: #f0f0f0; }
.outlook-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 20px;
    background: #fff;
    border-bottom: 1px solid #d4d4d4;
}
.outlook-topbar .outlook-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0;
    font-family: Georgia, 'Times New Roman', Times, serif;
    letter-spacing: 0.01em;
}
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
    font-family: Nunito, "Segoe UI", system-ui, sans-serif;
    vertical-align: middle;
    letter-spacing: 0;
}

/* ── Layout ─────────────────────────────────────────────────────────────── */
.outlook-container { display: flex; height: calc(100vh - 50px); min-height: 500px; }

/* ── Left sidebar — identical to Admin Outlook ──────────────────────────── */
.outlook-sidebar {
    width: 220px;
    min-width: 220px;
    background: #fff;
    border-right: 1px solid #d4d4d4;
    display: flex;
    flex-direction: column;
}
.outlook-sidebar .btn-compose {
    margin: 14px;
    padding: 10px 16px;
    background: #0078d4;
    color: #fff;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    text-decoration: none;
}
.outlook-sidebar .btn-compose:hover { background: #106ebe; color: #fff; }
.outlook-folders { padding: 4px 0 8px; }
.outlook-folders .folder-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 18px 10px 15px;
    color: #444;
    text-decoration: none;
    font-size: 13px;
    border-left: 3px solid transparent;
    box-sizing: border-box;
    cursor: pointer;
    border-top: none;
    border-right: none;
    border-bottom: none;
    background: transparent;
    width: 100%;
    text-align: left;
    font-family: inherit;
}
.outlook-folders .folder-item:hover { background: #f3f3f3; }
.outlook-folders .folder-item.active {
    background: #e5f3ff;
    color: #0078d4;
    font-weight: 600;
    border-left-color: #0078d4;
}
.outlook-folders .folder-item i { font-size: 14px; width: 18px; text-align: center; flex-shrink: 0; }
.elite-sidebar-foot {
    margin-top: auto;
    border-top: 1px solid #e2e8f0;
    padding: 11px 14px;
    background: #fafafa;
    font-size: 11px;
    color: #64748b;
    line-height: 1.45;
}
.elite-sidebar-foot .foot-label { font-weight: 600; color: #475569; margin-bottom: 3px; display: block; }
.elite-sidebar-foot code { font-size: 10.5px; word-break: break-all; color: #0078d4; }

/* ── Main area ──────────────────────────────────────────────────────────── */
.outlook-main {
    flex: 1;
    display: flex;
    min-width: 0;
    min-height: 0;
}

/* ── Folder view system (identical to Outlook page) ─────────────────────── */
.folder-view { flex: 1; display: none; flex-direction: column; overflow: hidden; }
.outlook-main.mode-inbox  .view-inbox  { display: flex !important; }
.outlook-main.mode-sent   .view-sent   { display: flex !important; }
.outlook-main.mode-drafts .view-drafts { display: flex !important; }

/* ── 3-col split (inbox only) ───────────────────────────────────────────── */
.outlook-triple { flex: 1; display: flex; min-width: 0; min-height: 0; }
.outlook-list-col {
    width: 440px;
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
    min-width: 300px;
    display: flex;
    flex-direction: column;
    background: #fff;
    overflow: hidden;
}

/* ── Toolbar ─────────────────────────────────────────────────────────────── */
.folder-view .inbox-toolbar {
    padding: 8px 16px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}
.inbox-toolbar .search-wrap { flex: 1; min-width: 140px; max-width: 220px; position: relative; }
.inbox-toolbar .search-wrap input {
    width: 100%;
    padding: 8px 12px 8px 36px;
    border: 1px solid #d4d4d4;
    border-radius: 4px;
    font-size: 13px;
    box-sizing: border-box;
}
.inbox-toolbar .search-wrap i {
    position: absolute; left: 12px; top: 50%;
    transform: translateY(-50%); color: #888; pointer-events: none;
}
.inbox-toolbar .filter-select,
.inbox-toolbar .filter-date {
    padding: 6px 10px;
    font-size: 12px;
    border: 1px solid #d4d4d4;
    border-radius: 4px;
    min-width: 100px;
}
.inbox-toolbar .filter-custom-dates { display: inline-flex; align-items: center; gap: 6px; }

/* ── Empty state ─────────────────────────────────────────────────────────── */
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
.empty-state span { font-size: 13px; max-width: 420px; display: block; }

/* ── Inbox email list ────────────────────────────────────────────────────── */
.email-list-wrap { flex: 1; display: flex; flex-direction: column; min-height: 0; overflow: hidden; }
.elite-message-list { list-style: none; margin: 0; padding: 0; overflow-y: auto; flex: 1; }
.elite-msg-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 20px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    border-left: 3px solid transparent;
}
.elite-msg-item:hover { background: #f8fafc; }
.elite-msg-item.is-selected { background: #eef6fc; border-left-color: #0078d4; }
.elite-msg-item:focus { outline: 2px solid #0078d4; outline-offset: -2px; }
.elite-msg-item .email-dir-icon {
    width: 32px; height: 32px; border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; flex-shrink: 0; margin-top: 2px;
    background: #e8f5e9; color: #2e7d32;
}
.elite-msg-main { flex: 1; min-width: 0; }
.elite-msg-line1 { display: flex; align-items: baseline; justify-content: space-between; gap: 10px; margin-bottom: 4px; }
.elite-msg-addr { font-weight: 600; font-size: 13.5px; color: #0f172a; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.elite-msg-when { font-size: 12px; color: #94a3b8; flex-shrink: 0; }
.elite-msg-subj { font-size: 13px; color: #64748b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

/* ── Error bar ───────────────────────────────────────────────────────────── */
.inbox-fetch-error {
    display: none;
    margin: 0 20px 10px;
    padding: 10px 14px;
    background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px;
    color: #991b1b; font-size: 13px;
    align-items: center; justify-content: space-between; gap: 12px;
}
.inbox-fetch-error.show { display: flex; }
.inbox-fetch-error button { flex-shrink: 0; border: none; background: transparent; color: #991b1b; cursor: pointer; font-size: 12px; text-decoration: underline; padding: 0; }

/* ── Reading pane ────────────────────────────────────────────────────────── */
.outlook-reading-empty {
    flex: 1; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: #94a3b8; text-align: center; padding: 32px 24px;
}
.outlook-reading-empty i { font-size: 40px; margin-bottom: 12px; opacity: 0.5; }
.outlook-reading-content { flex: 1; min-height: 0; display: flex; flex-direction: column; overflow: hidden; }
.outlook-reading-scroll { flex: 1; overflow-y: auto; padding: 20px 24px; }
.outlook-read-subject { font-size: 18px; font-weight: 600; color: #0f172a; margin: 0 0 12px; line-height: 1.3; }
.outlook-read-meta { font-size: 13px; color: #475569; }
.outlook-read-meta > div { margin-bottom: 6px; }
.outlook-read-type-label { display: inline-block; margin-bottom: 8px; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 4px; background: #f1f5f9; color: #475569; }
.outlook-read-meta .email-meta-label { font-weight: 600; color: #64748b; min-width: 48px; display: inline-block; }
.outlook-read-body { margin-top: 16px; font-size: 14px; line-height: 1.5; color: #1e293b; word-break: break-word; white-space: pre-wrap; }
.outlook-read-frame {
    width: 100%; flex: 1; min-height: 200px;
    border: 1px solid #e2e8f0; border-radius: 4px; background: #fff; margin-top: 12px;
}

/* ── Sent view (identical style to Admin Outlook) ───────────────────────── */
.sent-content { flex: 1; display: flex; flex-direction: column; overflow: auto; }
.sent-sections { flex: 1; padding: 0; }
.sent-section { margin-bottom: 24px; border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; background: #fff; }
.sent-section-header {
    padding: 10px 20px;
    background: #f1f5f9;
    font-weight: 600; color: #1e293b; font-size: 14px;
    border-bottom: 1px solid #e2e8f0;
    display: flex; align-items: center; gap: 8px;
}
.sent-section-header i.fa-envelope { color: #0078d4; }
.sent-toggle {
    width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;
    border: none; background: transparent; border-radius: 4px; cursor: pointer; color: #475569; flex-shrink: 0;
}
.sent-toggle:hover { background: #e2e8f0; color: #1e293b; }
.sent-section.collapsed .sent-section-body { display: none; }
.sent-section.collapsed .sent-section-header { border-bottom: none; }
.sent-section.collapsed .sent-toggle i { transform: rotate(-90deg); }
.sent-table { width: 100%; border-collapse: collapse; }
.sent-table th {
    text-align: left; padding: 10px 20px;
    font-size: 12px; font-weight: 600; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.02em;
    border-bottom: 1px solid #e2e8f0; background: #fafafa;
}
.sent-table td { padding: 12px 20px; border-bottom: 1px solid #f1f5f9; font-size: 13px; vertical-align: middle; }
.sent-table tr.sent-row { cursor: pointer; }
.sent-table tr.sent-row:hover { background: #f8fafc; }
.sent-cell-subject { color: #475569; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 0; }
.sent-cell-date { color: #94a3b8; font-size: 12px; }

/* ── Sent email modal ────────────────────────────────────────────────────── */
.sent-email-modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.4); z-index: 9999;
    align-items: center; justify-content: center; padding: 20px;
}
.sent-email-modal-overlay.show { display: flex; }
.sent-email-modal {
    background: #fff; border-radius: 8px; max-width: 700px; width: 100%;
    max-height: 90vh; display: flex; flex-direction: column;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}
.sent-email-modal .modal-header {
    padding: 16px 20px; border-bottom: 1px solid #e2e8f0;
    display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
}
.sent-email-modal .modal-header h3 { margin: 0; font-size: 16px; color: #1e293b; }
.sent-email-modal .modal-close {
    width: 32px; height: 32px; border: none; background: transparent;
    border-radius: 4px; cursor: pointer; color: #64748b; font-size: 18px;
    display: flex; align-items: center; justify-content: center;
}
.sent-email-modal .modal-close:hover { background: #f1f5f9; color: #1e293b; }
.sent-email-modal .modal-body { padding: 20px; overflow-y: auto; flex: 1; min-height: 0; }
.sent-email-modal .email-meta-row { margin-bottom: 12px; font-size: 13px; }
.sent-email-modal .email-meta-label { font-weight: 600; color: #64748b; min-width: 60px; display: inline-block; }
.sent-email-modal .email-body-wrap { margin-top: 16px; padding-top: 16px; border-top: 1px solid #e2e8f0; font-size: 14px; line-height: 1.5; word-break: break-word; }

/* ── Drafts list (simple, same as email-list rows) ───────────────────────── */
.email-list { list-style: none; margin: 0; padding: 0; }
.email-list .email-row { display: flex; align-items: center; padding: 12px 20px; border-bottom: 1px solid #f1f5f9; cursor: pointer; }
.email-list .email-row:hover { background: #f8fafc; }
.email-list .email-row .email-sender { min-width: 180px; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.email-list .email-row .email-subject { flex: 1; color: #475569; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding: 0 12px; }
.email-list .email-row .email-date { color: #94a3b8; font-size: 13px; flex-shrink: 0; }

/* ── Responsive ──────────────────────────────────────────────────────────── */
@media (max-width: 1200px) { .outlook-list-col { max-width: 100%; } .outlook-reading { min-width: 0; } }
@media (max-width: 900px) {
    .outlook-triple { flex-direction: column; }
    .outlook-list-col { width: 100% !important; max-width: none; border-right: none; border-bottom: 1px solid #e2e8f0; min-height: 40vh; }
    .outlook-reading { min-height: 30vh; }
}
</style>
@endpush

@section('content')
<div class="outlook-page">

    {{-- Top bar --}}
    <header class="outlook-topbar">
        <h1 class="outlook-title">Inbox <span class="sendgrid-badge">Education Elite</span></h1>
        <a href="{{ route('dashboard') }}" class="btn-back">
            <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to CRM
        </a>
    </header>

    <div class="server-error">@include('Elements.flash-message')</div>

    <div class="outlook-container">

        {{-- ── Sidebar (identical to Admin Outlook) ───────────────────────── --}}
        <aside class="outlook-sidebar">
            {{-- New Message → goes to full Outlook compose page --}}
            <a href="{{ route('admin.outlook.index') }}" class="btn-compose">
                <i class="fas fa-plus" aria-hidden="true"></i> New Message
            </a>

            <nav class="outlook-folders" aria-label="Folders">
                <button type="button" class="folder-item active" data-view="inbox" id="eliteFolderInbox">
                    <i class="fas fa-inbox" aria-hidden="true"></i> Inbox
                </button>
                <button type="button" class="folder-item" data-view="sent" id="eliteFolderSent">
                    <i class="fas fa-paper-plane" aria-hidden="true"></i> Sent
                </button>
                <button type="button" class="folder-item" data-view="drafts" id="eliteFolderDrafts">
                    <i class="fas fa-file-alt" aria-hidden="true"></i> Drafts
                </button>
            </nav>

            {{-- Mailbox filter (sub-accounts) --}}
            @if(count($eliteMailboxes ?? []) > 0)
            <div style="padding: 0 0 8px;">
                <div style="padding: 6px 15px 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8;">Mailboxes</div>
                <nav class="outlook-folders" id="eliteAccountTree" aria-label="Mailboxes">
                    @php $activeAcct = $eliteInitialAccount ?? 'all'; @endphp
                    <button type="button" class="folder-item{{ $activeAcct === 'all' ? ' active' : '' }}"
                        data-account="all" style="padding-left:26px;font-size:12.5px;">
                        <i class="fas fa-inbox" aria-hidden="true"></i> All
                    </button>
                    @foreach($eliteMailboxes ?? [] as $mbox)
                    <button type="button"
                        class="folder-item{{ ($activeAcct === $mbox) ? ' active' : '' }}"
                        data-account="{{ e($mbox) }}"
                        title="{{ e($mbox) }}"
                        style="padding-left:26px;font-size:12px;">
                        <i class="fas fa-at" aria-hidden="true"></i>
                        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;min-width:0;">{{ e($mbox) }}</span>
                    </button>
                    @endforeach
                </nav>
            </div>
            @endif

            {{-- Webhook URL footer --}}
            <div class="elite-sidebar-foot">
                <span class="foot-label">Inbound webhook</span>
                <code>{{ $webhookUrl ?? url('/elite/emails') }}</code>
                <div style="margin-top:5px;">
                    From: <strong>@{{ config('crm.education_elite_sender_domain','educationelite.com.au') }}</strong>
                    @if(config('crm.education_elite_inbound_secret'))
                        &nbsp;<span style="color:#16a34a;"><i class="fas fa-lock"></i></span>
                    @endif
                </div>
            </div>
        </aside>

        {{-- ── Main area ───────────────────────────────────────────────────── --}}
        <main class="outlook-main mode-inbox" id="eliteMain">

            {{-- ── INBOX VIEW (3-col: list + reading pane) ─────────────────── --}}
            <div class="outlook-triple view-inbox folder-view" id="folderInbox">

                {{-- List col --}}
                <div class="outlook-list-col">
                    <div class="inbox-toolbar">
                        <div class="search-wrap">
                            <i class="fas fa-search" aria-hidden="true"></i>
                            <input type="text" class="folder-search" placeholder="Search mail...">
                        </div>
                        <select class="filter-select filter-date-range">
                            <option value="" selected>All time</option>
                            <option value="today">Today</option>
                            <option value="7">Last 7 days</option>
                            <option value="30">Last 30 days</option>
                            <option value="custom">Custom range</option>
                        </select>
                        <span class="filter-custom-dates" style="display:none;">
                            <input type="date" class="filter-date filter-date-from">
                            <input type="date" class="filter-date filter-date-to">
                        </span>
                        <select class="filter-select filter-sort">
                            <option value="newest">Newest first</option>
                            <option value="oldest">Oldest first</option>
                        </select>
                        <button type="button" class="btn btn-primary btn-sm ms-2 btn-fetch-inbox">
                            <i class="fas fa-sync-alt" aria-hidden="true"></i> Get Emails
                        </button>
                    </div>
                    <div class="inbox-fetch-error" id="eliteFetchError" role="alert">
                        <span id="eliteFetchErrorText"></span>
                        <button type="button" id="eliteFetchErrorDismiss">Dismiss</button>
                    </div>

                    @php $inboxList = $eliteInboxItems ?? []; @endphp
                    <script type="application/json" id="elite-emails-initial">@json(collect($inboxList)->keyBy('id'))</script>

                    <div class="email-list-wrap" id="eliteListWrap" style="{{ count($inboxList) ? '' : 'display:none;' }}">
                        <ul class="elite-message-list" id="eliteEmailList" role="listbox" aria-label="Inbox messages">
                            @forelse($inboxList as $e)
                                <li class="elite-msg-item" role="option" tabindex="0" data-id="{{ $e['id'] }}">
                                    <span class="email-dir-icon" title="Incoming"><i class="fas fa-arrow-down" aria-hidden="true"></i></span>
                                    <div class="elite-msg-main">
                                        <div class="elite-msg-line1">
                                            <span class="elite-msg-addr">{{ $e['from'] ?? '—' }}</span>
                                            <span class="elite-msg-when">{{ $e['date'] ?? '' }}</span>
                                        </div>
                                        <div class="elite-msg-subj">{{ ($e['subject'] ?? '') !== '' ? $e['subject'] : '(No subject)' }}</div>
                                    </div>
                                </li>
                            @empty
                            @endforelse
                        </ul>
                    </div>
                    <div class="empty-state" id="eliteEmpty" style="{{ count($inboxList) ? 'display:none;' : '' }}">
                        <i class="fas fa-inbox" aria-hidden="true"></i>
                        <p>No incoming messages</p>
                        <span id="eliteEmptyHint">
                            Point SendGrid Inbound Parse at the webhook and send to
                            <strong>{{ '@' . config('crm.education_elite_sender_domain','educationelite.com.au') }}</strong>.
                        </span>
                    </div>
                </div>

                {{-- Reading pane --}}
                <aside class="outlook-reading" id="eliteReadingPane" aria-label="Reading pane">
                    <div class="outlook-reading-empty" id="eliteReadingEmpty">
                        <i class="fas fa-envelope-open" aria-hidden="true"></i>
                        <p>Select a message to read it here.</p>
                    </div>
                    <div class="outlook-reading-content" id="eliteReadingContent" style="display:none" aria-live="polite">
                        <div class="outlook-reading-scroll" id="eliteReadingScroll">
                            <div id="eliteReadTypeRow" style="display:none">
                                <span class="outlook-read-type-label" id="eliteReadType"></span>
                            </div>
                            <h2 class="outlook-read-subject" id="eliteReadSubject"></h2>
                            <div class="outlook-read-meta">
                                <div><span class="email-meta-label">From:</span> <span id="eliteReadFrom"></span></div>
                                <div><span class="email-meta-label">To:</span>   <span id="eliteReadTo"></span></div>
                                <div><span class="email-meta-label">Date:</span> <span id="eliteReadDate"></span></div>
                            </div>
                            <div class="outlook-read-body" id="eliteReadBody"></div>
                            <iframe id="eliteReadFrame" class="outlook-read-frame" title="HTML message" sandbox="allow-same-origin" style="display:none;"></iframe>
                        </div>
                    </div>
                </aside>
            </div>

            {{-- ── SENT VIEW ────────────────────────────────────────────────── --}}
            <div class="folder-view view-sent" id="folderSent">
                <div class="inbox-toolbar">
                    <div class="search-wrap">
                        <i class="fas fa-search" aria-hidden="true"></i>
                        <input type="text" class="folder-search" placeholder="Search sent...">
                    </div>
                    <select class="filter-select filter-date-range">
                        <option value="" selected>All time</option>
                        <option value="today">Today</option>
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="custom">Custom range</option>
                    </select>
                    <span class="filter-custom-dates" style="display:none;">
                        <input type="date" class="filter-date filter-date-from">
                        <input type="date" class="filter-date filter-date-to">
                    </span>
                    <select class="filter-select filter-sort">
                        <option value="newest">Newest first</option>
                        <option value="oldest">Oldest first</option>
                    </select>
                    <button type="button" class="btn btn-primary btn-sm ms-2 btn-fetch-sent">
                        <i class="fas fa-sync-alt" aria-hidden="true"></i> Get Emails
                    </button>
                </div>
                <div class="sent-content">
                    <div class="sent-sections folder-list-sent" id="eliteSentSections"></div>
                    <div class="empty-state folder-empty-sent" id="eliteSentEmpty">
                        <i class="fas fa-paper-plane" aria-hidden="true"></i>
                        <p>No sent messages</p>
                        <span>Emails sent from your Education Elite address appear here. Click "Get Emails" to load.</span>
                    </div>
                </div>
            </div>

            {{-- ── DRAFTS VIEW ──────────────────────────────────────────────── --}}
            <div class="folder-view view-drafts" id="folderDrafts">
                <div class="inbox-toolbar">
                    <div class="search-wrap">
                        <i class="fas fa-search" aria-hidden="true"></i>
                        <input type="text" class="folder-search" placeholder="Search drafts...">
                    </div>
                    <button type="button" class="btn btn-primary btn-sm ms-2 btn-fetch-drafts">
                        <i class="fas fa-sync-alt" aria-hidden="true"></i> Get Drafts
                    </button>
                </div>
                <ul class="email-list" id="eliteDraftList"></ul>
                <div class="empty-state" id="eliteDraftEmpty">
                    <i class="fas fa-file-alt" aria-hidden="true"></i>
                    <p>No drafts</p>
                    <span>Drafts saved on the <a href="{{ route('admin.outlook.index') }}">Outlook page</a> using an Education Elite address will appear here.</span>
                </div>
            </div>

        </main>{{-- /outlook-main --}}
    </div>{{-- /outlook-container --}}
</div>{{-- /outlook-page --}}

{{-- Sent email modal --}}
<div id="sentEmailModalOverlay" class="sent-email-modal-overlay" aria-hidden="true">
    <div class="sent-email-modal" role="dialog" aria-labelledby="sentModalTitle">
        <div class="modal-header">
            <h3 id="sentModalTitle">Sent email</h3>
            <button type="button" class="modal-close" id="sentModalClose" aria-label="Close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="email-meta-row"><span class="email-meta-label">From:</span> <span id="smFrom"></span></div>
            <div class="email-meta-row"><span class="email-meta-label">To:</span>   <span id="smTo"></span></div>
            <div class="email-meta-row"><span class="email-meta-label">Cc:</span>   <span id="smCc"></span></div>
            <div class="email-meta-row"><span class="email-meta-label">Subject:</span> <span id="smSubject"></span></div>
            <div class="email-meta-row"><span class="email-meta-label">Date:</span> <span id="smDate"></span></div>
            <div class="email-body-wrap"><div class="email-meta-label" style="margin-bottom:8px;">Message</div><div id="smBody"></div></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    'use strict';

    /* ── URLs ──────────────────────────────────────────────────────────────── */
    var INBOX_URL  = @json(route('elite.emails.inbox'));
    var SENT_URL   = @json(route('elite.emails.sent'));
    var DRAFTS_URL = @json(route('elite.emails.drafts'));

    var main         = document.getElementById('eliteMain');
    var tokenMeta    = document.querySelector('meta[name="csrf-token"]');
    var activeAccount = @json($eliteInitialAccount ?? 'all');

    /* ── Fetch helper ─────────────────────────────────────────────────────── */
    function apiFetch(url, params) {
        var fullUrl = url + (params && params.length ? '?' + params.join('&') : '');
        return fetch(fullUrl, {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': tokenMeta ? tokenMeta.getAttribute('content') : ''
            }
        }).then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); });
    }

    /* ── Date range helper ────────────────────────────────────────────────── */
    function getDateParams(toolbar) {
        var rangeSel  = toolbar.querySelector('.filter-date-range');
        var customSpan = toolbar.querySelector('.filter-custom-dates');
        var fromInput  = toolbar.querySelector('.filter-date-from');
        var toInput    = toolbar.querySelector('.filter-date-to');
        if (!rangeSel) return { date_from: '', date_to: '' };
        var val = rangeSel.value;
        var today = new Date(); today.setHours(0,0,0,0);
        var todayStr = today.toISOString().slice(0,10);
        if (val === 'custom' && fromInput && toInput) return { date_from: fromInput.value||'', date_to: toInput.value||'' };
        if (val === 'today') return { date_from: todayStr, date_to: todayStr };
        if (val === '7' || val === '30') {
            var f = new Date(today); f.setDate(f.getDate() - parseInt(val,10));
            return { date_from: f.toISOString().slice(0,10), date_to: todayStr };
        }
        return { date_from: '', date_to: '' };
    }
    document.querySelectorAll('.filter-date-range').forEach(function (sel) {
        sel.addEventListener('change', function () {
            var span = this.closest('.inbox-toolbar').querySelector('.filter-custom-dates');
            if (span) span.style.display = this.value === 'custom' ? 'inline-flex' : 'none';
        });
    });

    /* ── Folder switching ─────────────────────────────────────────────────── */
    document.querySelectorAll('.outlook-sidebar .folder-item[data-view]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var view = this.dataset.view;
            main.className = 'outlook-main mode-' + view;
            document.querySelectorAll('.outlook-sidebar .folder-item[data-view]').forEach(function (b) {
                b.classList.toggle('active', b.dataset.view === view);
            });
            if (view === 'sent')   { triggerFetch('sent');   }
            if (view === 'drafts') { triggerFetch('drafts'); }
        });
    });

    function triggerFetch(folder) {
        var btn = document.querySelector('.btn-fetch-' + folder);
        if (btn && !btn.disabled) btn.click();
    }

    /* ═══════════════════════════════════════════════════════════════════════ */
    /* INBOX                                                                    */
    /* ═══════════════════════════════════════════════════════════════════════ */
    var listEl         = document.getElementById('eliteEmailList');
    var listWrap       = document.getElementById('eliteListWrap');
    var emptyEl        = document.getElementById('eliteEmpty');
    var emptyHint      = document.getElementById('eliteEmptyHint');
    var readingEmpty   = document.getElementById('eliteReadingEmpty');
    var readingContent = document.getElementById('eliteReadingContent');
    var readingScroll  = document.getElementById('eliteReadingScroll');
    var fetchErrEl     = document.getElementById('eliteFetchError');
    var fetchErrText   = document.getElementById('eliteFetchErrorText');

    var initialMap = {};
    try { initialMap = JSON.parse(document.getElementById('elite-emails-initial').textContent || '{}'); }
    catch (_) { initialMap = {}; }

    function showFetchError(msg) { if (fetchErrText) fetchErrText.textContent = msg; if (fetchErrEl) fetchErrEl.classList.add('show'); }
    function clearFetchError()   { if (fetchErrEl) fetchErrEl.classList.remove('show'); }
    document.getElementById('eliteFetchErrorDismiss').addEventListener('click', clearFetchError);

    var HTML_TAG_RE = /<([a-z][a-z0-9]*)\b[^>]*>/i;
    function looksLikeHtml(s) { return typeof s === 'string' && HTML_TAG_RE.test(s.trim()); }
    function safeSrcdoc(h) { return String(h).replace(/<\/iframe/gi, '<\\/iframe'); }

    function clearReading() {
        if (readingContent) readingContent.style.display = 'none';
        if (readingEmpty)   readingEmpty.style.display   = 'flex';
        var frame = document.getElementById('eliteReadFrame');
        var body  = document.getElementById('eliteReadBody');
        if (frame) { frame.srcdoc = ''; frame.style.display = 'none'; }
        if (body)  { body.textContent = ''; body.style.display = ''; }
        if (listEl) listEl.querySelectorAll('.elite-msg-item.is-selected').forEach(function (n) { n.classList.remove('is-selected'); });
    }

    function showReading(p) {
        if (readingEmpty)   readingEmpty.style.display   = 'none';
        if (readingContent) readingContent.style.display = 'flex';
        if (readingScroll)  readingScroll.scrollTop = 0;
        var typeRow = document.getElementById('eliteReadTypeRow');
        var typeEl  = document.getElementById('eliteReadType');
        if (typeRow && typeEl) { typeEl.textContent = p.direction_label||''; typeRow.style.display = p.direction_label ? 'block' : 'none'; }
        document.getElementById('eliteReadSubject').textContent = p.subject || '(No subject)';
        document.getElementById('eliteReadFrom').textContent = p.from || '';
        document.getElementById('eliteReadTo').textContent   = p.to   || '';
        document.getElementById('eliteReadDate').textContent = p.date  || '';
        var body  = p.body || '';
        var plainEl = document.getElementById('eliteReadBody');
        var frame   = document.getElementById('eliteReadFrame');
        if (plainEl && frame) {
            if (looksLikeHtml(body)) {
                plainEl.textContent = ''; plainEl.style.display = 'none';
                frame.style.display = 'block'; frame.srcdoc = safeSrcdoc(body);
            } else {
                frame.srcdoc = ''; frame.style.display = 'none';
                plainEl.style.display = ''; plainEl.textContent = body;
            }
        }
    }

    function buildEmailRow(e) {
        var li = document.createElement('li');
        li.className = 'elite-msg-item'; li.setAttribute('role','option'); li.setAttribute('tabindex','0'); li.dataset.id = e.id||'';
        var icon = document.createElement('span'); icon.className = 'email-dir-icon'; icon.title = 'Incoming';
        var ic = document.createElement('i'); ic.className = 'fas fa-arrow-down'; ic.setAttribute('aria-hidden','true');
        icon.appendChild(ic);
        var m = document.createElement('div'); m.className = 'elite-msg-main';
        var l1 = document.createElement('div'); l1.className = 'elite-msg-line1';
        var addr = document.createElement('span'); addr.className = 'elite-msg-addr'; addr.textContent = e.from||'—';
        var when = document.createElement('span'); when.className = 'elite-msg-when'; when.textContent = e.date||'';
        l1.appendChild(addr); l1.appendChild(when); m.appendChild(l1);
        var subj = document.createElement('div'); subj.className = 'elite-msg-subj'; subj.textContent = (e.subject && String(e.subject).length) ? e.subject : '(No subject)';
        m.appendChild(subj); li.appendChild(icon); li.appendChild(m);
        return li;
    }

    function onRowActivate(row) {
        if (!row || !listEl.contains(row)) return;
        var p = initialMap[row.dataset.id];
        if (!p) return;
        listEl.querySelectorAll('.elite-msg-item.is-selected').forEach(function (n) { n.classList.remove('is-selected'); });
        row.classList.add('is-selected');
        showReading({ from: p.from||'', to: p.to||'', subject: p.subject||'', date: p.date||'', body: p.body||'', direction_label: p.direction_label||'' });
    }

    if (listEl) {
        listEl.addEventListener('click', function (ev) { var r = ev.target.closest('.elite-msg-item'); if (r) onRowActivate(r); });
        listEl.addEventListener('keydown', function (ev) {
            if (ev.key !== 'Enter' && ev.key !== ' ') return;
            var r = ev.target.closest('.elite-msg-item'); if (!r || !listEl.contains(r)) return;
            ev.preventDefault(); onRowActivate(r);
        });
    }

    /* Mailbox filter buttons */
    document.querySelectorAll('#eliteAccountTree .folder-item[data-account]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            activeAccount = this.dataset.account || 'all';
            document.querySelectorAll('#eliteAccountTree .folder-item').forEach(function (b) {
                var isA = (b.dataset.account || 'all') === activeAccount;
                b.classList.toggle('active', isA);
                if (isA) b.setAttribute('aria-current','page'); else b.removeAttribute('aria-current');
            });
            document.querySelector('.btn-fetch-inbox') && document.querySelector('.btn-fetch-inbox').click();
        });
    });

    document.querySelector('.btn-fetch-inbox') && document.querySelector('.btn-fetch-inbox').addEventListener('click', function () {
        var btn = this;
        var toolbar = document.querySelector('#folderInbox .inbox-toolbar');
        clearFetchError(); clearReading();
        var search = (toolbar.querySelector('.folder-search').value||'').trim();
        var dr     = getDateParams(toolbar);
        var sort   = toolbar.querySelector('.filter-sort').value || 'newest';
        var params = ['folder=inbox'];
        if (search)       params.push('search=' + encodeURIComponent(search));
        if (dr.date_from) params.push('date_from=' + encodeURIComponent(dr.date_from));
        if (dr.date_to)   params.push('date_to='   + encodeURIComponent(dr.date_to));
        params.push('sort=' + sort, 'account=' + encodeURIComponent(activeAccount||'all'));
        btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        apiFetch(INBOX_URL, params).then(function (data) {
            clearFetchError();
            if (data.account) activeAccount = data.account;
            listEl.innerHTML = ''; initialMap = {};
            var rows = data.emails || [];
            rows.forEach(function (e) { if (e.id) initialMap[e.id] = e; });
            if (rows.length === 0) {
                if (listWrap) listWrap.style.display = 'none';
                if (emptyEl)  { emptyEl.style.display = 'flex'; if (emptyHint) emptyHint.textContent = data.message||''; }
            } else {
                if (listWrap) listWrap.style.display = '';
                if (emptyEl)  emptyEl.style.display = 'none';
                rows.forEach(function (e) { listEl.appendChild(buildEmailRow(e)); });
            }
        }).catch(function () { showFetchError('Could not refresh. Check your connection and try again.'); })
          .finally(function () { btn.disabled = false; btn.innerHTML = '<i class="fas fa-sync-alt"></i> Get Emails'; });
    });

    /* ═══════════════════════════════════════════════════════════════════════ */
    /* SENT                                                                     */
    /* ═══════════════════════════════════════════════════════════════════════ */
    var sentSections = document.getElementById('eliteSentSections');
    var sentEmpty    = document.getElementById('eliteSentEmpty');

    var sentModalOverlay = document.getElementById('sentEmailModalOverlay');
    document.getElementById('sentModalClose').addEventListener('click', function () { sentModalOverlay.classList.remove('show'); });
    sentModalOverlay.addEventListener('click', function (e) { if (e.target === sentModalOverlay) sentModalOverlay.classList.remove('show'); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') sentModalOverlay.classList.remove('show'); });

    function openSentModal(d) {
        document.getElementById('smFrom').textContent    = d.from    || '';
        document.getElementById('smTo').textContent      = d.to      || '';
        document.getElementById('smCc').textContent      = d.cc      || '—';
        document.getElementById('smSubject').textContent = d.subject  || '(No subject)';
        document.getElementById('smDate').textContent    = d.date     || '';
        document.getElementById('smBody').innerHTML = d.body || '<em>No content</em>';
        sentModalOverlay.classList.add('show');
    }

    document.querySelector('.btn-fetch-sent') && document.querySelector('.btn-fetch-sent').addEventListener('click', function () {
        var btn = this;
        var toolbar = document.querySelector('#folderSent .inbox-toolbar');
        var search = (toolbar.querySelector('.folder-search').value||'').trim();
        var dr     = getDateParams(toolbar);
        var sort   = toolbar.querySelector('.filter-sort').value || 'newest';
        var params = [];
        if (search)       params.push('search=' + encodeURIComponent(search));
        if (dr.date_from) params.push('date_from=' + encodeURIComponent(dr.date_from));
        if (dr.date_to)   params.push('date_to='   + encodeURIComponent(dr.date_to));
        params.push('sort=' + sort);
        btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        apiFetch(SENT_URL, params).then(function (data) {
            sentSections.innerHTML = '';
            var groups = data.sent_groups || [];
            if (groups.length === 0) {
                sentSections.style.display = 'none';
                sentEmpty.style.display = 'flex';
                sentEmpty.querySelector('span').textContent = data.message || 'No sent messages yet.';
                return;
            }
            sentSections.style.display = '';
            sentEmpty.style.display = 'none';
            groups.forEach(function (grp) {
                var fromEmail = grp.from_email || '';
                var section = document.createElement('div'); section.className = 'sent-section'; section.dataset.fromEmail = fromEmail;
                var hdr = document.createElement('div'); hdr.className = 'sent-section-header';
                hdr.innerHTML = '<button type="button" class="sent-toggle" title="Expand/Collapse"><i class="fas fa-chevron-down"></i></button><i class="fas fa-envelope"></i> ' + fromEmail;
                var body = document.createElement('div'); body.className = 'sent-section-body';
                var table = document.createElement('table'); table.className = 'sent-table';
                table.innerHTML = '<thead><tr><th style="width:28%">To</th><th>Subject</th><th style="width:160px">Date</th></tr></thead><tbody></tbody>';
                var tbody = table.querySelector('tbody');
                (grp.emails||[]).forEach(function (e) {
                    var tr = document.createElement('tr'); tr.className = 'sent-row'; tr.title = 'Click to view';
                    tr.innerHTML = '<td>' + (e.to||'') + '</td><td class="sent-cell-subject" title="' + (e.subject||'').replace(/"/g,'&quot;') + '">' + (e.subject||'(No subject)') + '</td><td class="sent-cell-date">' + (e.date||'') + '</td>';
                    tr.addEventListener('click', function () { openSentModal({ from: fromEmail, to: e.to||'', cc: e.cc||'', subject: e.subject||'', body: e.body||'', date: e.date||'' }); });
                    tbody.appendChild(tr);
                });
                body.appendChild(table); section.appendChild(hdr); section.appendChild(body);
                hdr.querySelector('.sent-toggle').addEventListener('click', function () {
                    section.classList.toggle('collapsed');
                    var ic = this.querySelector('i');
                    if (ic) ic.className = section.classList.contains('collapsed') ? 'fas fa-chevron-right' : 'fas fa-chevron-down';
                });
                sentSections.appendChild(section);
            });
        }).catch(function () { sentEmpty.style.display = 'flex'; sentEmpty.querySelector('span').textContent = 'Could not load sent mail.'; })
          .finally(function () { btn.disabled = false; btn.innerHTML = '<i class="fas fa-sync-alt"></i> Get Emails'; });
    });

    /* ═══════════════════════════════════════════════════════════════════════ */
    /* DRAFTS                                                                   */
    /* ═══════════════════════════════════════════════════════════════════════ */
    var draftList  = document.getElementById('eliteDraftList');
    var draftEmpty = document.getElementById('eliteDraftEmpty');

    document.querySelector('.btn-fetch-drafts') && document.querySelector('.btn-fetch-drafts').addEventListener('click', function () {
        var btn = this;
        var toolbar = document.querySelector('#folderDrafts .inbox-toolbar');
        var search = (toolbar.querySelector('.folder-search').value||'').trim();
        var params = search ? ['search=' + encodeURIComponent(search)] : [];
        btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        apiFetch(DRAFTS_URL, params).then(function (data) {
            draftList.innerHTML = '';
            var rows = data.emails || [];
            if (rows.length === 0) {
                draftList.style.display = 'none'; draftEmpty.style.display = 'flex';
                return;
            }
            draftList.style.display = ''; draftEmpty.style.display = 'none';
            rows.forEach(function (d) {
                var li = document.createElement('li'); li.className = 'email-row';
                li.innerHTML = '<span class="email-sender">' + (d.from||'—') + '</span><span class="email-subject">' + (d.subject||'(No subject)') + '</span><span class="email-date">' + (d.date||'') + '</span>';
                draftList.appendChild(li);
            });
        }).catch(function () { draftEmpty.style.display = 'flex'; })
          .finally(function () { btn.disabled = false; btn.innerHTML = '<i class="fas fa-sync-alt"></i> Get Drafts'; });
    });

}());
</script>
@endpush
@endsection
