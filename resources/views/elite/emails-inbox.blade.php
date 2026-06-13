@extends('layouts.outlook')
@section('title', 'Education Elite — Email')

@push('styles')
<style>
/* Reset browser defaults that create gaps */
html, body { margin: 0; padding: 0; height: 100%; overflow: hidden; }
#app { margin: 0; padding: 0; height: 100%; overflow: hidden; }
.loader { display: none !important; }

/* ── Identical base to Admin Outlook page ───────────────────────────────── */
.outlook-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 20px;
    background: #fff;
    border-bottom: 1px solid #d4d4d4;
    flex-shrink: 0;
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
.ses-badge {
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
.outlook-page { display: flex; flex-direction: column; height: 100vh; overflow: hidden; background: #f0f0f0; }
.server-error { flex-shrink: 0; }
.outlook-container { display: flex; flex: 1; min-height: 0; overflow: hidden; }

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
    width: 320px;
    min-width: 260px;
    max-width: 38%;
    display: flex;
    flex-direction: column;
    background: #fff;
    border-right: 1px solid #d4d4d4;
    overflow: hidden;
}
.outlook-reading {
    flex: 1 1 65%;
    min-width: 320px;
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
.elite-msg-snippet { font-size: 12px; color: #94a3b8; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-top: 2px; }
.elite-msg-attach { font-size: 11px; color: #64748b; margin-left: 5px; flex-shrink: 0; }

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
.outlook-read-actions { display: flex; gap: 8px; padding: 10px 24px; border-bottom: 1px solid #e2e8f0; flex-shrink: 0; }
.outlook-read-actions .btn-read-act { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border: 1px solid #d4d4d4; background: #fff; border-radius: 4px; font-size: 13px; cursor: pointer; color: #333; }
.outlook-read-actions .btn-read-act:hover { background: #f3f3f3; }
.elite-empty-body-msg { color: #94a3b8; font-size: 13px; font-style: italic; margin-top: 20px; }
.outlook-reading-empty {
    flex: 1; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: #94a3b8; text-align: center; padding: 32px 24px;
}
.outlook-reading-empty i { font-size: 40px; margin-bottom: 12px; opacity: 0.5; }
.outlook-reading-content { flex: 1; min-height: 0; display: flex; flex-direction: column; overflow: hidden; }
.outlook-reading-scroll {
    flex: 1;
    overflow-y: auto;
    padding: 20px 24px;
    width: 100%;
    max-width: none;
    box-sizing: border-box;
}
.outlook-read-subject { font-size: 18px; font-weight: 600; color: #0f172a; margin: 0 0 12px; line-height: 1.3; }
.outlook-read-meta { font-size: 13px; color: #475569; }
.outlook-read-meta > div { margin-bottom: 6px; }
.outlook-read-type-label { display: inline-block; margin-bottom: 8px; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 4px; background: #f1f5f9; color: #475569; }
.outlook-read-meta .email-meta-label { font-weight: 600; color: #64748b; min-width: 48px; display: inline-block; }
.outlook-read-body { margin-top: 16px; font-size: 14px; line-height: 1.5; color: #1e293b; word-break: break-word; white-space: pre-wrap; }
.elite-read-attachments { margin-top: 14px; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 6px; background: #f8fafc; }
.elite-read-attachments .elite-att-head { font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 8px; }
.elite-read-attachments ul { margin: 0; padding-left: 1.1rem; font-size: 13px; color: #1e293b; }
.elite-read-attachments a { color: #2563eb; text-decoration: underline; word-break: break-all; }
.outlook-read-frame {
    display: block;
    width: 100%;
    height: auto;
    min-height: 240px;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    background: #fff;
    margin-top: 12px;
    box-sizing: border-box;
    overflow: hidden;
}

/* ── Sent view — list + reading pane (same as Outlook page) ─────────────── */
.sent-triple { flex: 1; display: flex; min-width: 0; min-height: 0; }
.sent-list-col {
    width: 320px;
    min-width: 260px;
    max-width: 38%;
    display: flex; flex-direction: column;
    border-right: 1px solid #d4d4d4; overflow: hidden; background: #fff;
}
.sent-reading-col {
    flex: 1 1 65%;
    min-width: 320px;
    display: flex;
    flex-direction: column;
    background: #fff;
    overflow: hidden;
    min-height: 0;
}
.sent-list { list-style: none; margin: 0; padding: 0; overflow-y: auto; flex: 1; }
.sent-msg-item {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 12px 20px; border-bottom: 1px solid #f1f5f9;
    cursor: pointer; border-left: 3px solid transparent;
}
.sent-msg-item:hover { background: #f8fafc; }
.sent-msg-item.is-selected { background: #eef6fc; border-left-color: #0078d4; }
.sent-icon { width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; margin-top: 2px; background: #e0f2fe; color: #0078d4; }
.sent-msg-main { flex: 1; min-width: 0; }
.sent-msg-line1 { display: flex; align-items: baseline; justify-content: space-between; gap: 8px; margin-bottom: 3px; }
.sent-msg-to   { font-weight: 600; font-size: 13.5px; color: #0f172a; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.sent-msg-date { font-size: 12px; color: #94a3b8; flex-shrink: 0; }
.sent-msg-from { font-size: 12px; color: #64748b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-bottom: 2px; }
.sent-msg-subj { font-size: 13px; color: #64748b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
/* Sent reading pane */
.sent-reading-empty { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; text-align: center; padding: 32px; }
.sent-reading-empty i { font-size: 40px; margin-bottom: 12px; opacity: 0.5; }
.sent-reading-content { flex: 1; min-height: 0; display: flex; flex-direction: column; overflow: hidden; }
.sent-read-actions { display: flex; gap: 8px; padding: 12px 20px; border-bottom: 1px solid #e2e8f0; flex-shrink: 0; }
.sent-read-actions .btn-read-act { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border: 1px solid #d4d4d4; background: #fff; border-radius: 4px; font-size: 13px; cursor: pointer; color: #333; }
.sent-read-actions .btn-read-act:hover { background: #f3f3f3; }
.sent-reading-scroll {
    flex: 1;
    overflow-y: auto;
    padding: 20px 24px;
    width: 100%;
    max-width: none;
    box-sizing: border-box;
}
.sent-read-subject { font-size: 18px; font-weight: 600; color: #0f172a; margin: 0 0 12px; line-height: 1.3; }
.sent-read-meta { font-size: 13px; color: #475569; margin-bottom: 16px; }
.sent-read-meta > div { margin-bottom: 5px; }
.sent-read-meta .ml { font-weight: 600; color: #64748b; min-width: 52px; display: inline-block; }
.sent-read-body { font-size: 14px; line-height: 1.6; color: #1e293b; word-break: break-word; white-space: pre-wrap; }
.sent-read-frame {
    display: block;
    width: 100%;
    height: auto;
    min-height: 240px;
    border: none;
    margin-top: 8px;
    box-sizing: border-box;
    overflow: hidden;
}

/* (sent modal removed — reading pane replaces it) */
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
@media (max-width: 1200px) {
    .outlook-list-col  { max-width: 100%; }
    .outlook-reading   { min-width: 0; }
    .sent-list-col     { max-width: 100%; }
    .sent-reading-col  { min-width: 0; }
}
@media (max-width: 900px) {
    .outlook-triple,
    .sent-triple { flex-direction: column; }

    .outlook-list-col,
    .sent-list-col {
        width: 100% !important;
        max-width: none !important;
        border-right: none;
        border-bottom: 1px solid #e2e8f0;
        min-height: 36vh;
        max-height: 36vh;
    }
    .outlook-reading,
    .sent-reading-col { min-width: 0; min-height: 30vh; }
}
</style>
@endpush

@section('content')
<div class="outlook-page">

    {{-- Top bar --}}
    <header class="outlook-topbar">
        <h1 class="outlook-title">Inbox <span class="ses-badge">Education Elite · SES</span></h1>
        <a href="{{ route('dashboard') }}" class="btn-back">
            <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to CRM
        </a>
    </header>

    <div class="server-error">@include('Elements.flash-message')</div>

    <div class="outlook-container">

        {{-- ── Sidebar (identical to Admin Outlook) ───────────────────────── --}}
        <aside class="outlook-sidebar">
            {{-- New Message → opens compose modal inline --}}
            <button type="button" class="btn-compose" id="eliteBtnCompose">
                <i class="fas fa-plus" aria-hidden="true"></i> New Message
            </button>

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

            <div style="padding:10px 14px 14px;font-size:11px;color:#64748b;line-height:1.45;border-top:1px solid #e8e8e8;margin-top:8px;">
                <strong style="display:block;color:#334155;font-size:11px;margin-bottom:4px;">Inbound (AWS SES)</strong>
                MX for <strong>{{ '@' . ltrim((string) config('crm.education_elite_sender_domain', 'educationelite.com.au'), '@') }}</strong> → SES receipt rule → S3
                <code style="display:block;margin-top:6px;font-size:10px;background:#f1f5f9;padding:4px 6px;border-radius:3px;">{{ $sesInboundBucket ?? 'bucket' }}/{{ $sesInboundPrefix ?? 'prefix' }}</code>
                <span style="display:block;margin-top:6px;">Get Emails imports new .eml files (also runs every minute).</span>
            </div>
        </aside>

        {{-- ── Main area ───────────────────────────────────────────────────── --}}
        <main class="outlook-main mode-inbox" id="eliteMain">

            {{-- ── INBOX VIEW (3-col: list + reading pane) ─────────────────── --}}
            <div class="folder-view view-inbox" id="folderInbox">
            <div class="outlook-triple">

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
                                            @if(!empty($e['has_attachments']))
                                                <span class="elite-msg-attach" title="Has attachments"><i class="fas fa-paperclip" aria-hidden="true"></i></span>
                                            @endif
                                            <span class="elite-msg-when">{{ $e['date'] ?? '' }}</span>
                                        </div>
                                        <div class="elite-msg-subj">{{ ($e['subject'] ?? '') !== '' ? $e['subject'] : '(No subject)' }}</div>
                                        @if(!empty($e['snippet']))
                                            <div class="elite-msg-snippet">{{ $e['snippet'] }}</div>
                                        @endif
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
                            Configure AWS SES inbound (see sidebar). Forward Microsoft mail to your SES domain if needed. Apex:
                            <strong>{{ '@' . ltrim((string) config('crm.education_elite_sender_domain','educationelite.com.au'), '@') }}</strong>.
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
                        <div class="outlook-read-actions" id="eliteReadActions" style="display:none;">
                            <button type="button" class="btn-read-act" id="eliteInboxBtnReply"><i class="fas fa-reply" aria-hidden="true"></i> Reply</button>
                            <button type="button" class="btn-read-act" id="eliteInboxBtnFwd"><i class="fas fa-share" aria-hidden="true"></i> Forward</button>
                        </div>
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
                            <div id="eliteReadAttachments" class="elite-read-attachments" style="display:none;" aria-label="Attachments"></div>
                            <div class="outlook-read-body" id="eliteReadBody"></div>
                            <iframe id="eliteReadFrame" class="outlook-read-frame" title="HTML message" sandbox="allow-same-origin" style="display:none;"></iframe>
                        </div>
                    </div>
                </aside>
            </div>{{-- /outlook-triple --}}
            </div>{{-- /folder-view view-inbox --}}

            {{-- ── SENT VIEW — list + reading pane ──────────────────────── --}}
            <div class="folder-view view-sent" id="folderSent">
                {{-- Toolbar --}}
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
                {{-- 3-col split: list + reading pane --}}
                <div class="sent-triple">
                    {{-- List --}}
                    <div class="sent-list-col">
                        <ul class="sent-list" id="eliteSentList" role="listbox" aria-label="Sent messages"></ul>
                        <div class="empty-state" id="eliteSentEmpty">
                            <i class="fas fa-paper-plane" aria-hidden="true"></i>
                            <p>No sent messages</p>
                            <span>Emails sent from your <strong>@educationelite.com.au</strong> address appear here automatically.</span>
                        </div>
                    </div>
                    {{-- Reading pane --}}
                    <div class="sent-reading-col">
                        <div class="sent-reading-empty" id="eliteSentReadEmpty">
                            <i class="fas fa-paper-plane" aria-hidden="true"></i>
                            <p>Select a sent email to preview it.</p>
                        </div>
                        <div class="sent-reading-content" id="eliteSentReadContent" style="display:none;">
                            <div class="sent-read-actions">
                                <button type="button" class="btn-read-act" id="eliteSentBtnReply"><i class="fas fa-reply"></i> Reply</button>
                                <button type="button" class="btn-read-act" id="eliteSentBtnFwd"><i class="fas fa-share"></i> Forward</button>
                            </div>
                            <div class="sent-reading-scroll">
                                <h2 class="sent-read-subject" id="eliteSentReadSubj"></h2>
                                <div class="sent-read-meta">
                                    <div><span class="ml">From:</span> <span id="eliteSentReadFrom"></span></div>
                                    <div><span class="ml">To:</span>   <span id="eliteSentReadTo"></span></div>
                                    <div><span class="ml">Cc:</span>   <span id="eliteSentReadCc"></span></div>
                                    <div><span class="ml">Date:</span> <span id="eliteSentReadDate"></span></div>
                                </div>
                                <div class="sent-read-body" id="eliteSentReadBody"></div>
                                <iframe id="eliteSentReadFrame" class="sent-read-frame" title="Sent email body" sandbox="allow-same-origin" style="display:none;"></iframe>
                            </div>
                        </div>
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
                {{-- Two-column split: list + reading pane --}}
                <div class="sent-triple">
                    <div class="sent-list-col">
                        <ul class="email-list" id="eliteDraftList"></ul>
                        <div class="empty-state" id="eliteDraftEmpty">
                            <i class="fas fa-file-alt" aria-hidden="true"></i>
                            <p>No drafts</p>
                            <span>Use <strong>New Message</strong> to compose and save drafts with an Education Elite address.</span>
                        </div>
                    </div>
                    {{-- Reading pane --}}
                    <div class="sent-reading-col">
                        <div class="sent-reading-empty" id="eliteDraftReadEmpty">
                            <i class="fas fa-file-alt" aria-hidden="true"></i>
                            <p>Select a draft to preview it.</p>
                        </div>
                        <div class="sent-reading-content" id="eliteDraftReadContent" style="display:none;">
                            <div class="sent-read-actions">
                                <button type="button" class="btn-read-act" id="eliteDraftBtnEdit">
                                    <i class="fas fa-edit"></i> Edit &amp; Send
                                </button>
                            </div>
                            <div class="sent-reading-scroll">
                                <h2 class="sent-read-subject" id="eliteDraftReadSubj"></h2>
                                <div class="sent-read-meta">
                                    <div><span class="ml">From:</span> <span id="eliteDraftReadFrom"></span></div>
                                    <div><span class="ml">To:</span>   <span id="eliteDraftReadTo"></span></div>
                                    <div><span class="ml">Cc:</span>   <span id="eliteDraftReadCc"></span></div>
                                    <div><span class="ml">Date:</span> <span id="eliteDraftReadDate"></span></div>
                                </div>
                                <div class="sent-read-body" id="eliteDraftReadBody"></div>
                                <iframe id="eliteDraftReadFrame" class="sent-read-frame" title="Draft email body" sandbox="allow-same-origin" style="display:none;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>{{-- /outlook-main --}}
    </div>{{-- /outlook-container --}}
</div>{{-- /outlook-page --}}

{{-- ── Compose modal ──────────────────────────────────────────────────────── --}}
<div id="eliteComposeOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1050;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:6px;width:680px;max-width:96vw;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 8px 40px rgba(0,0,0,.25);overflow:hidden;">
        {{-- Modal header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #e2e8f0;flex-shrink:0;">
            <span id="eliteComposeTitle" style="font-size:15px;font-weight:600;color:#0f172a;">New Message</span>
            <button type="button" id="eliteComposeClose" style="border:none;background:transparent;font-size:20px;color:#64748b;cursor:pointer;line-height:1;padding:0 4px;" title="Close">&times;</button>
        </div>
        {{-- Alert bar (success / error) --}}
        <div id="eliteComposeAlert" style="display:none;padding:10px 20px;font-size:13px;flex-shrink:0;"></div>
        {{-- Compose form --}}
        <form id="eliteComposeForm" action="{{ route('admin.outlook.send') }}" method="POST" enctype="multipart/form-data" style="display:flex;flex-direction:column;flex:1;min-height:0;overflow:hidden;">
            @csrf
            <input type="hidden" name="_elite_compose" value="1">
            <div style="padding:16px 20px 0;flex-shrink:0;">
                {{-- From --}}
                <div style="display:flex;align-items:center;margin-bottom:12px;">
                    <label style="min-width:56px;font-size:13px;color:#555;">From <span style="color:#dc3545;">*</span></label>
                    <select name="from" id="eliteComposeFrom" required
                            style="flex:1;padding:8px 12px;border:1px solid #d4d4d4;border-radius:4px;font-size:13px;">
                        <option value="">Loading senders…</option>
                    </select>
                </div>
                {{-- To --}}
                <div style="display:flex;align-items:center;margin-bottom:12px;">
                    <label style="min-width:56px;font-size:13px;color:#555;">To</label>
                    <input type="text" name="to" id="eliteComposeTo" required placeholder="recipient@example.com"
                           autocomplete="off"
                           style="flex:1;padding:8px 12px;border:1px solid #d4d4d4;border-radius:4px;font-size:13px;">
                </div>
                {{-- Cc --}}
                <div style="display:flex;align-items:center;margin-bottom:12px;">
                    <label style="min-width:56px;font-size:13px;color:#555;">Cc</label>
                    <input type="text" name="cc" placeholder="Optional (comma-separated)"
                           autocomplete="off"
                           style="flex:1;padding:8px 12px;border:1px solid #d4d4d4;border-radius:4px;font-size:13px;">
                </div>
                {{-- Subject --}}
                <div style="display:flex;align-items:center;margin-bottom:12px;">
                    <label style="min-width:56px;font-size:13px;color:#555;">Subject</label>
                    <input type="text" name="subject" required placeholder="Subject"
                           style="flex:1;padding:8px 12px;border:1px solid #d4d4d4;border-radius:4px;font-size:13px;">
                </div>
            </div>
            {{-- Body --}}
            <div style="flex:1;min-height:0;padding:0 20px 12px;display:flex;flex-direction:column;">
                <textarea name="body" placeholder="Write your message here…"
                          style="flex:1;min-height:240px;padding:12px;border:1px solid #d4d4d4;border-radius:4px;font-size:14px;resize:vertical;font-family:inherit;line-height:1.5;"></textarea>
            </div>
            {{-- Attachment preview list --}}
            <div id="eliteAttachmentList" style="display:none;padding:0 20px 10px;flex-shrink:0;">
                <div style="font-size:12px;color:#64748b;margin-bottom:6px;">Attachments:</div>
                <div id="eliteAttachmentItems" style="display:flex;flex-wrap:wrap;gap:6px;"></div>
            </div>
            {{-- Hidden file input --}}
            <input type="file" id="eliteAttachmentInput" name="attachments[]" multiple style="display:none;">
            {{-- Footer buttons --}}
            <div style="display:flex;align-items:center;gap:10px;padding:12px 20px;border-top:1px solid #e2e8f0;flex-shrink:0;background:#fafafa;">
                <button type="submit" id="eliteComposeSend"
                        style="display:inline-flex;align-items:center;gap:7px;padding:9px 20px;background:#0078d4;color:#fff;border:none;border-radius:4px;font-size:13px;font-weight:600;cursor:pointer;">
                    <i class="fas fa-paper-plane"></i> Send
                </button>
                <button type="button" id="eliteComposeSaveDraft"
                        style="display:inline-flex;align-items:center;gap:6px;padding:9px 14px;background:#fff;color:#475569;border:1px solid #d4d4d4;border-radius:4px;font-size:13px;cursor:pointer;">
                    <i class="fas fa-save"></i> Save Draft
                </button>
                <button type="button" id="eliteAttachBtn" title="Attach files"
                        style="display:inline-flex;align-items:center;gap:6px;padding:9px 14px;background:#fff;color:#475569;border:1px solid #d4d4d4;border-radius:4px;font-size:13px;cursor:pointer;">
                    <i class="fas fa-paperclip"></i> Attach
                </button>
                <button type="button" id="eliteComposeCancel"
                        style="margin-left:auto;padding:9px 16px;background:#fff;color:#475569;border:1px solid #d4d4d4;border-radius:4px;font-size:13px;cursor:pointer;">
                    Cancel
                </button>
            </div>
        </form>
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
    var ELITE_QUOTE_MAX = 8000;

    function extractEmailAddress(emailString) {
        if (!emailString) return '';
        var s = String(emailString);
        var match = s.match(/<([^>]+)>/);
        if (match) return match[1].trim();
        if (s.indexOf('@') !== -1) return s.trim();
        return s.trim();
    }

    function dispatchComposePrefill(detail) {
        document.dispatchEvent(new CustomEvent('elite:composePrefill', { detail: detail || {} }));
    }

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
    /**
     * Prepare untrusted email HTML for iframe[srcdoc] with sandbox (no allow-scripts).
     * Strips scripts and inline handlers so the browser does not log sandbox violations
     * and so active content cannot run in the preview frame.
     */
    function safeSrcdoc(h) {
        var s = String(h);
        s = s.replace(/<script\b[^>]*>[\s\S]*?<\/script>/gi, '');
        s = s.replace(/<script\b[^>]*\/>/gi, '');
        s = s.replace(/\s+on[a-z]+\s*=\s*(?:"[^"]*"|'[^']*'|[^\s>]+)/gi, '');
        s = s.replace(/\b(src|href|poster|data)\s*=\s*(["'])\s*javascript:/gi, '$1=$2blocked:');
        s = s.replace(/\b(src|href|poster|data)\s*=\s*javascript:/gi, '$1=blocked:');
        s = s.replace(/<meta\b[^>]*http-equiv\s*=\s*["']?\s*refresh[^>]*>/gi, '');
        s = s.replace(/<\/iframe/gi, '<\\/iframe');
        /* Let HTML mail use the full iframe width (many templates fix body/table to ~600px). */
        var previewCss = '<style id="elite-email-preview-base">html,body{margin:0!important;padding:0!important;width:100%!important;max-width:none!important;box-sizing:border-box;}body{padding:12px!important;}table{max-width:100%!important;}img{max-width:100%!important;height:auto!important;}</style>';
        if (/^\s*<!DOCTYPE/i.test(s) || /^\s*<html\b/i.test(s)) {
            if (/<head[^>]*>/i.test(s)) {
                s = s.replace(/<head[^>]*>/i, function (m) { return m + previewCss; });
            } else {
                s = s.replace(/<html\b[^>]*>/i, function (m) { return m + '<head>' + previewCss + '</head>'; });
            }
        } else {
            s = previewCss + s;
        }
        return s;
    }

    /**
     * Auto-size a sandboxed iframe to its content after srcdoc loads.
     * Called before setting srcdoc so the onload fires for the new content.
     * Uses iframe.contentDocument from the same origin (sandboxed with allow-same-origin).
     */
    function autoResizeFrame(frame) {
        frame.style.height = frame.style.minHeight || '240px';
        function onLoaded() {
            try {
                var doc = frame.contentDocument || (frame.contentWindow && frame.contentWindow.document);
                if (!doc) return;
                /* Force scrollHeight recalc by reading it after a tick */
                setTimeout(function () {
                    try {
                        var h = Math.max(
                            doc.documentElement ? doc.documentElement.scrollHeight : 0,
                            doc.body            ? doc.body.scrollHeight            : 0
                        );
                        if (h > 0) frame.style.height = (h + 24) + 'px';
                    } catch (_) {}
                }, 0);
            } catch (_) {}
        }
        /* Remove any prior listener to avoid stacking handlers */
        frame._eliteResizeHandler && frame.removeEventListener('load', frame._eliteResizeHandler);
        frame._eliteResizeHandler = onLoaded;
        frame.addEventListener('load', onLoaded, { once: true });
    }

    function clearReading() {
        if (readingContent) readingContent.style.display = 'none';
        if (readingEmpty)   readingEmpty.style.display   = 'flex';
        var frame = document.getElementById('eliteReadFrame');
        var body  = document.getElementById('eliteReadBody');
        var acts  = document.getElementById('eliteReadActions');
        if (frame) { frame.srcdoc = ''; frame.style.display = 'none'; frame.style.height = ''; }
        if (body)  { body.textContent = ''; body.style.display = ''; }
        if (acts)  acts.style.display = 'none';
        if (readingScroll) readingScroll.querySelectorAll('.elite-empty-body-msg').forEach(function (n) { n.parentNode.removeChild(n); });
        var attBox = document.getElementById('eliteReadAttachments');
        if (attBox) { attBox.style.display = 'none'; attBox.innerHTML = ''; }
        if (listEl) listEl.querySelectorAll('.elite-msg-item.is-selected').forEach(function (n) { n.classList.remove('is-selected'); });
    }

    function showReading(p) {
        if (readingEmpty)   readingEmpty.style.display   = 'none';
        if (readingContent) readingContent.style.display = 'flex';
        if (readingScroll)  readingScroll.scrollTop = 0;

        var acts = document.getElementById('eliteReadActions');
        if (acts) acts.style.display = 'flex';

        var typeRow = document.getElementById('eliteReadTypeRow');
        var typeEl  = document.getElementById('eliteReadType');
        if (typeRow && typeEl) { typeEl.textContent = p.direction_label||''; typeRow.style.display = p.direction_label ? 'block' : 'none'; }
        document.getElementById('eliteReadSubject').textContent = p.subject || '(No subject)';
        document.getElementById('eliteReadFrom').textContent = p.from || '';
        document.getElementById('eliteReadTo').textContent   = p.to   || '';
        document.getElementById('eliteReadDate').textContent = p.date  || '';

        var body    = p.body || '';
        var plainEl = document.getElementById('eliteReadBody');
        var frame   = document.getElementById('eliteReadFrame');
        var attBox  = document.getElementById('eliteReadAttachments');
        var btnReply = document.getElementById('eliteInboxBtnReply');
        var btnFwd   = document.getElementById('eliteInboxBtnFwd');

        function openComposePrefilled(to, subject, quotedBody, title) {
            dispatchComposePrefill({
                title: title || 'Reply',
                to: to,
                subject: subject,
                body: quotedBody
            });
        }

        if (attBox) {
            var alist = p.attachments && p.attachments.length ? p.attachments : [];
            if (alist.length) {
                attBox.style.display = 'block';
                attBox.innerHTML = '';
                var h = document.createElement('div');
                h.className = 'elite-att-head';
                h.textContent = alist.length === 1 ? 'Attachment' : 'Attachments';
                attBox.appendChild(h);
                var ul = document.createElement('ul');
                alist.forEach(function (a) {
                    var li = document.createElement('li');
                    var link = document.createElement('a');
                    link.href = a.url || '#';
                    link.target = '_blank';
                    link.rel = 'noopener noreferrer';
                    link.textContent = a.filename || ('File #' + (a.id || ''));
                    li.appendChild(link);
                    ul.appendChild(li);
                });
                attBox.appendChild(ul);
            } else {
                attBox.style.display = 'none';
                attBox.innerHTML = '';
            }
        }

        if (p.bodyLoadError) {
            if (readingScroll) readingScroll.querySelectorAll('.elite-empty-body-msg').forEach(function (n) { n.parentNode.removeChild(n); });
            if (plainEl && frame) {
                frame.srcdoc = ''; frame.style.height = ''; frame.style.display = 'none';
                plainEl.style.display = '';
                plainEl.textContent = 'Could not load the message body. Please refresh and try again.';
            }
            var plainSnippetErr = (p.snippet || '').trim();
            var quotedErr = '\n\n--- Original message ---\nFrom: ' + (p.from||'') + '\nDate: ' + (p.date||'') + '\n\n' + plainSnippetErr.slice(0, ELITE_QUOTE_MAX);
            if (btnReply) btnReply.onclick = function () {
                var subj = (p.subject && !/^re:/i.test(p.subject)) ? 'Re: ' + p.subject : (p.subject || '');
                openComposePrefilled(p.from || '', subj, quotedErr, 'Reply');
            };
            if (btnFwd) btnFwd.onclick = function () {
                var subj = (p.subject && !/^fwd:/i.test(p.subject)) ? 'Fwd: ' + p.subject : (p.subject || '');
                openComposePrefilled('', subj, '\n\n--- Forwarded message ---\nFrom: ' + (p.from||'') + '\nTo: ' + (p.to||'') + '\nDate: ' + (p.date||'') + '\n\n' + plainSnippetErr.slice(0, ELITE_QUOTE_MAX), 'Forward');
            };
            return;
        }

        if (p.bodyLoading) {
            if (readingScroll) readingScroll.querySelectorAll('.elite-empty-body-msg').forEach(function (n) { n.parentNode.removeChild(n); });
            if (plainEl && frame) {
                frame.srcdoc = ''; frame.style.height = ''; frame.style.display = 'none';
                plainEl.style.display = '';
                plainEl.textContent = 'Loading message…';
            }
            var plainSnippetL = (p.snippet || '').trim();
            var quotedL = '\n\n--- Original message ---\nFrom: ' + (p.from||'') + '\nDate: ' + (p.date||'') + '\n\n' + plainSnippetL.slice(0, ELITE_QUOTE_MAX);
            if (btnReply) btnReply.onclick = function () {
                var subj = (p.subject && !/^re:/i.test(p.subject)) ? 'Re: ' + p.subject : (p.subject || '');
                openComposePrefilled(p.from || '', subj, quotedL, 'Reply');
            };
            if (btnFwd) btnFwd.onclick = function () {
                var subj = (p.subject && !/^fwd:/i.test(p.subject)) ? 'Fwd: ' + p.subject : (p.subject || '');
                openComposePrefilled('', subj, '\n\n--- Forwarded message ---\nFrom: ' + (p.from||'') + '\nTo: ' + (p.to||'') + '\nDate: ' + (p.date||'') + '\n\n' + plainSnippetL.slice(0, ELITE_QUOTE_MAX), 'Forward');
            };
            return;
        }

        /* Remove any previous empty-body notice */
        if (readingScroll) {
            readingScroll.querySelectorAll('.elite-empty-body-msg').forEach(function (n) { n.parentNode.removeChild(n); });
        }

        if (plainEl && frame) {
            if (looksLikeHtml(body)) {
                plainEl.textContent = ''; plainEl.style.display = 'none';
                autoResizeFrame(frame);
                frame.style.display = 'block'; frame.srcdoc = safeSrcdoc(body);
            } else if (body.trim()) {
                frame.srcdoc = ''; frame.style.height = ''; frame.style.display = 'none';
                plainEl.style.display = ''; plainEl.textContent = body;
            } else {
                frame.srcdoc = ''; frame.style.height = ''; frame.style.display = 'none';
                plainEl.style.display = 'none';
                var notice = document.createElement('p');
                notice.className = 'elite-empty-body-msg';
                notice.textContent = p.has_attachments
                    ? 'This email has no text body — it may contain attachments only.'
                    : 'No text content in this message.';
                if (readingScroll) readingScroll.appendChild(notice);
            }
        }

        var plainSnippet = looksLikeHtml(body)
            ? body.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim()
            : body.trim();
        var quoted = '\n\n--- Original message ---\nFrom: ' + (p.from||'') + '\nDate: ' + (p.date||'') + '\n\n' + plainSnippet.slice(0, ELITE_QUOTE_MAX);

        /* Wire Reply / Forward to compose modal */
        if (btnReply) btnReply.onclick = function () {
            var subj = (p.subject && !/^re:/i.test(p.subject)) ? 'Re: ' + p.subject : (p.subject || '');
            openComposePrefilled(p.from || '', subj, quoted, 'Reply');
        };
        if (btnFwd) btnFwd.onclick = function () {
            var subj = (p.subject && !/^fwd:/i.test(p.subject)) ? 'Fwd: ' + p.subject : (p.subject || '');
            openComposePrefilled('', subj, '\n\n--- Forwarded message ---\nFrom: ' + (p.from||'') + '\nTo: ' + (p.to||'') + '\nDate: ' + (p.date||'') + '\n\n' + plainSnippet.slice(0, ELITE_QUOTE_MAX), 'Forward');
        };
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
        l1.appendChild(addr);
        if (e.has_attachments) {
            var aiBadge = document.createElement('span'); aiBadge.className = 'elite-msg-attach'; aiBadge.title = 'Has attachments';
            var aiIc = document.createElement('i'); aiIc.className = 'fas fa-paperclip'; aiIc.setAttribute('aria-hidden','true');
            aiBadge.appendChild(aiIc); l1.appendChild(aiBadge);
        }
        var when = document.createElement('span'); when.className = 'elite-msg-when'; when.textContent = e.date||'';
        l1.appendChild(when); m.appendChild(l1);
        var subj = document.createElement('div'); subj.className = 'elite-msg-subj'; subj.textContent = (e.subject && String(e.subject).length) ? e.subject : '(No subject)';
        m.appendChild(subj);
        var snippetText = e.snippet || '';
        if (!snippetText && e.body) {
            snippetText = (looksLikeHtml(e.body) ? e.body.replace(/<[^>]+>/g, ' ') : e.body).replace(/\s+/g, ' ').trim().slice(0, 100);
        }
        if (snippetText) {
            var snEl = document.createElement('div'); snEl.className = 'elite-msg-snippet'; snEl.textContent = snippetText;
            m.appendChild(snEl);
        }
        li.appendChild(icon); li.appendChild(m);
        return li;
    }

    function onRowActivate(row) {
        if (!row || !listEl.contains(row)) return;
        var p = initialMap[row.dataset.id];
        if (!p) return;
        listEl.querySelectorAll('.elite-msg-item.is-selected').forEach(function (n) { n.classList.remove('is-selected'); });
        row.classList.add('is-selected');
        if (p.body_fetch_url) {
            showReading({
                from: p.from || '', to: p.to || '', subject: p.subject || '', date: p.date || '',
                body: '', bodyLoading: true, snippet: p.snippet || '',
                direction_label: p.direction_label || '', has_attachments: p.has_attachments || false, attachments: p.attachments || []
            });
            fetch(p.body_fetch_url, { credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (res) { if (!res.ok) throw new Error('bad'); return res.json(); })
                .then(function (data) {
                    p.body = data.body != null ? data.body : '';
                    initialMap[p.id] = p;
                    showReading({
                        from: p.from || '', to: p.to || '', subject: p.subject || '', date: p.date || '', body: p.body || '',
                        direction_label: p.direction_label || '', has_attachments: p.has_attachments || false, attachments: p.attachments || []
                    });
                })
                .catch(function () {
                    showReading({
                        from: p.from || '', to: p.to || '', subject: p.subject || '', date: p.date || '',
                        body: '', bodyLoadError: true, snippet: p.snippet || '',
                        direction_label: p.direction_label || '', has_attachments: p.has_attachments || false, attachments: p.attachments || []
                    });
                });
            return;
        }
        showReading({ from: p.from||'', to: p.to||'', subject: p.subject||'', date: p.date||'', body: p.body||'', direction_label: p.direction_label||'', has_attachments: p.has_attachments||false, attachments: p.attachments || [] });
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

    /* ── Inbox fetch (shared by button + auto-poll) ───────────────────────── */
    var autoPolling = false;
    var autoPollTimer = null;
    var AUTO_POLL_MS = 10000; // 10 seconds

    /* ── Burst-poll after send: polls every 5 s for 5 minutes ───────────────
       Uses a deadline timestamp so hidden-tab skips don't consume the budget.
       On tab-focus, checks immediately if deadline hasn't passed.            */
    var burstPollTimer    = null;
    var burstDeadlineMs   = 0;          // epoch ms when burst mode expires
    var BURST_POLL_MS     = 5000;       // check every 5 s while burst active
    var BURST_DURATION_MS = 5 * 60000; // 5 minutes

    function isBurstActive() { return Date.now() < burstDeadlineMs; }

    function startBurstPoll() {
        burstDeadlineMs = Date.now() + BURST_DURATION_MS;
        if (burstPollTimer) clearInterval(burstPollTimer);
        burstPollTimer = setInterval(function () {
            if (!isBurstActive()) {
                clearInterval(burstPollTimer);
                burstPollTimer = null;
                return;
            }
            doFetchInbox(true); // always fetch — even while tab is hidden (no DOM ops in silent mode)
        }, BURST_POLL_MS);
    }

    var inboxFetchInFlight = false;

    function doFetchInbox(silent) {
        /* Prevent overlapping concurrent fetches for silent (poll) calls */
        if (silent && inboxFetchInFlight) return;
        var btn     = document.querySelector('.btn-fetch-inbox');
        var toolbar = document.querySelector('#folderInbox .inbox-toolbar');
        if (!toolbar) return;
        if (!silent) { clearFetchError(); clearReading(); }
        var search = (toolbar.querySelector('.folder-search').value||'').trim();
        var dr     = getDateParams(toolbar);
        var sort   = toolbar.querySelector('.filter-sort').value || 'newest';
        var params = ['folder=inbox'];
        if (search)       params.push('search=' + encodeURIComponent(search));
        if (dr.date_from) params.push('date_from=' + encodeURIComponent(dr.date_from));
        if (dr.date_to)   params.push('date_to='   + encodeURIComponent(dr.date_to));
        params.push('sort=' + sort, 'account=' + encodeURIComponent(activeAccount||'all'));
        if (!silent) params.push('sync=1');

        if (!silent && btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing...'; }
        if (silent) inboxFetchInFlight = true;

        apiFetch(INBOX_URL, params).then(function (data) {
            if (!silent) clearFetchError();
            if (data.account) activeAccount = data.account;
            var rows = data.emails || [];

            if (silent) {
                /* Silent poll: only prepend genuinely new emails */
                var newRows = rows.filter(function (e) { return e.id && !initialMap[e.id]; });
                if (newRows.length > 0) {
                    newRows.forEach(function (e) { initialMap[e.id] = e; });
                    /* Show new-email banner */
                    showNewMailBanner(newRows.length);
                    /* Prepend rows at top of list */
                    if (listEl) {
                        var frag = document.createDocumentFragment();
                        newRows.slice().reverse().forEach(function (e) { frag.appendChild(buildEmailRow(e)); });
                        listEl.insertBefore(frag, listEl.firstChild);
                        if (listWrap) listWrap.style.display = '';
                        if (emptyEl)  emptyEl.style.display = 'none';
                    }
                }
            } else {
                /* Manual fetch: full reload */
                listEl.innerHTML = ''; initialMap = {};
                rows.forEach(function (e) { if (e.id) initialMap[e.id] = e; });
                if (rows.length === 0) {
                    if (listWrap) listWrap.style.display = 'none';
                    if (emptyEl)  { emptyEl.style.display = 'flex'; if (emptyHint) emptyHint.textContent = data.message||''; }
                } else {
                    if (listWrap) listWrap.style.display = '';
                    if (emptyEl)  emptyEl.style.display = 'none';
                    rows.forEach(function (e) { listEl.appendChild(buildEmailRow(e)); });
                }
            }
        }).catch(function () {
            if (!silent) showFetchError('Could not refresh. Check your connection and try again.');
        }).finally(function () {
            if (silent) inboxFetchInFlight = false;
            if (!silent && btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-sync-alt"></i> Get Emails'; }
        });
    }

    /* ── New-mail toast banner ────────────────────────────────────────────── */
    var newMailBanner = null;
    var newMailBannerTimer = null;
    function showNewMailBanner(count) {
        if (!newMailBanner) {
            newMailBanner = document.createElement('div');
            newMailBanner.style.cssText = [
                'position:fixed;top:16px;right:20px;z-index:9999',
                'display:flex;align-items:center;gap:10px',
                'padding:11px 18px;border-radius:6px',
                'background:#0078d4;color:#fff;font-size:13px;font-weight:600',
                'box-shadow:0 4px 18px rgba(0,0,0,.22)',
                'cursor:pointer;transition:opacity .3s'
            ].join(';');
            newMailBanner.addEventListener('click', function () {
                hideNewMailBanner();
                /* scroll list to top */
                if (listWrap) listWrap.scrollTop = 0;
            });
            document.body.appendChild(newMailBanner);
        }
        newMailBanner.innerHTML = '<i class="fas fa-envelope"></i> ' + count + ' new email' + (count > 1 ? 's' : '') + ' received — click to view';
        newMailBanner.style.opacity = '1';
        newMailBanner.style.display = 'flex';
        if (newMailBannerTimer) clearTimeout(newMailBannerTimer);
        newMailBannerTimer = setTimeout(hideNewMailBanner, 6000);
    }
    function hideNewMailBanner() {
        if (!newMailBanner) return;
        newMailBanner.style.opacity = '0';
        setTimeout(function () { if (newMailBanner) newMailBanner.style.display = 'none'; }, 320);
    }

    /* ── Auto-poll scheduler ──────────────────────────────────────────────── */
    function startAutoPoll() {
        if (autoPollTimer) return;
        autoPollTimer = setInterval(function () {
            if (document.hidden) return; // pause when tab is not visible
            /* Only poll if inbox panel is currently active */
            if (main && main.classList.contains('mode-inbox')) {
                doFetchInbox(true);
            }
        }, AUTO_POLL_MS);
    }
    function stopAutoPoll() {
        if (autoPollTimer) { clearInterval(autoPollTimer); autoPollTimer = null; }
    }

    /* Initial load: fetch inbox as soon as the page is ready */
    doFetchInbox(false);

    startAutoPoll();
    /* On tab focus: immediately fetch + resume burst if still in window */
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            stopAutoPoll();
        } else {
            doFetchInbox(true); // check right away when user returns to tab
            startAutoPoll();
            if (isBurstActive() && !burstPollTimer) startBurstPoll(); // resume burst
        }
    });

    document.querySelector('.btn-fetch-inbox') && document.querySelector('.btn-fetch-inbox').addEventListener('click', function () {
        doFetchInbox(false);
    });

    /* ═══════════════════════════════════════════════════════════════════════ */
    /* SENT — list + reading pane                                               */
    /* ═══════════════════════════════════════════════════════════════════════ */
    var sentListEl      = document.getElementById('eliteSentList');
    var sentEmptyEl     = document.getElementById('eliteSentEmpty');
    var sentReadEmpty   = document.getElementById('eliteSentReadEmpty');
    var sentReadContent = document.getElementById('eliteSentReadContent');

    function escHtml(s) {
        return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function openSentReading(e) {
        if (sentReadEmpty)   sentReadEmpty.style.display   = 'none';
        if (sentReadContent) sentReadContent.style.display = 'flex';
        document.getElementById('eliteSentReadSubj').textContent = e.subject || '(No subject)';
        document.getElementById('eliteSentReadFrom').textContent = e.from    || '—';
        document.getElementById('eliteSentReadTo').textContent   = e.to      || '—';
        document.getElementById('eliteSentReadCc').textContent   = e.cc      || '—';
        document.getElementById('eliteSentReadDate').textContent = e.date    || '—';
        var bodyEl  = document.getElementById('eliteSentReadBody');
        var frameEl = document.getElementById('eliteSentReadFrame');
        var raw = e.body || '';
        var isHtml = looksLikeHtml(raw);
        if (isHtml && frameEl) {
            bodyEl.style.display = 'none';
            autoResizeFrame(frameEl);
            frameEl.style.display = 'block';
            frameEl.srcdoc = safeSrcdoc(raw);
        } else {
            if (frameEl) { frameEl.style.display = 'none'; frameEl.style.height = ''; frameEl.srcdoc = ''; }
            bodyEl.style.display = '';
            bodyEl.textContent = raw || '(No content)';
        }
        var btnReply = document.getElementById('eliteSentBtnReply');
        var btnFwd   = document.getElementById('eliteSentBtnFwd');
        var sentPlain = looksLikeHtml(raw) ? raw.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim() : raw.trim();
        if (btnReply) btnReply.onclick = function () {
            var subj = (e.subject && !/^re:/i.test(e.subject)) ? 'Re: ' + e.subject : (e.subject || '');
            var body = '\n\n--- Original message ---\nFrom: ' + (e.from||'') + '\nDate: ' + (e.date||'') + '\n\n' + sentPlain.slice(0, ELITE_QUOTE_MAX);
            dispatchComposePrefill({ title: 'Reply', to: e.to || '', subject: subj, body: body });
        };
        if (btnFwd) btnFwd.onclick = function () {
            var subj = (e.subject && !/^fwd:/i.test(e.subject)) ? 'Fwd: ' + e.subject : (e.subject || '');
            var body = '\n\n--- Forwarded message ---\nFrom: ' + (e.from||'') + '\nTo: ' + (e.to||'') + '\nDate: ' + (e.date||'') + '\n\n' + sentPlain.slice(0, ELITE_QUOTE_MAX);
            dispatchComposePrefill({ title: 'Forward', subject: subj, body: body });
        };
    }

    document.querySelector('.btn-fetch-sent') && document.querySelector('.btn-fetch-sent').addEventListener('click', function () {
        var btn     = this;
        var toolbar = document.querySelector('#folderSent .inbox-toolbar');
        var search  = (toolbar.querySelector('.folder-search').value||'').trim();
        var dr      = getDateParams(toolbar);
        var sort    = toolbar.querySelector('.filter-sort').value || 'newest';
        var params  = [];
        if (search)       params.push('search='    + encodeURIComponent(search));
        if (dr.date_from) params.push('date_from=' + encodeURIComponent(dr.date_from));
        if (dr.date_to)   params.push('date_to='   + encodeURIComponent(dr.date_to));
        params.push('sort=' + sort);
        btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';

        apiFetch(SENT_URL, params).then(function (data) {
            sentListEl.innerHTML = '';
            var emails = data.emails || [];
            if (emails.length === 0) {
                sentListEl.style.display  = 'none';
                sentEmptyEl.style.display = 'flex';
                var hint = sentEmptyEl.querySelector('span');
                if (hint) hint.textContent = data.message || 'No sent messages yet.';
                if (sentReadEmpty)   sentReadEmpty.style.display   = 'flex';
                if (sentReadContent) sentReadContent.style.display = 'none';
                return;
            }
            sentListEl.style.display  = '';
            sentEmptyEl.style.display = 'none';
            emails.forEach(function (e) {
                var initials = (e.from||'S').split('@')[0].charAt(0).toUpperCase();
                var li = document.createElement('li');
                li.className = 'sent-msg-item';
                li.setAttribute('role', 'option');
                li.innerHTML =
                    '<div class="sent-icon" aria-hidden="true">' + initials + '</div>' +
                    '<div class="sent-msg-main">' +
                        '<div class="sent-msg-line1">' +
                            '<span class="sent-msg-to" title="To: ' + escHtml(e.to) + '">' + escHtml(e.to||'(no recipient)') + '</span>' +
                            '<span class="sent-msg-date">' + escHtml(e.date_short||e.date) + '</span>' +
                        '</div>' +
                        '<div class="sent-msg-from">From: ' + escHtml(e.from||'—') + '</div>' +
                        '<div class="sent-msg-subj">' + escHtml(e.subject||'(No subject)') + '</div>' +
                    '</div>';
                li.addEventListener('click', function () {
                    sentListEl.querySelectorAll('.sent-msg-item').forEach(function(i){ i.classList.remove('is-selected'); });
                    li.classList.add('is-selected');
                    openSentReading(e);
                });
                sentListEl.appendChild(li);
            });
        }).catch(function (err) {
            sentEmptyEl.style.display = 'flex';
            var hint = sentEmptyEl.querySelector('span');
            if (hint) hint.textContent = 'Could not load sent mail. Please try again.';
        }).finally(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-sync-alt"></i> Get Emails';
        });
    });

    /* ═══════════════════════════════════════════════════════════════════════ */
    /* DRAFTS                                                                   */
    /* ═══════════════════════════════════════════════════════════════════════ */
    var draftList        = document.getElementById('eliteDraftList');
    var draftEmpty       = document.getElementById('eliteDraftEmpty');
    var draftReadEmpty   = document.getElementById('eliteDraftReadEmpty');
    var draftReadContent = document.getElementById('eliteDraftReadContent');
    var draftStore       = {}; // id → draft object

    function openDraftReading(d) {
        if (draftReadEmpty)   draftReadEmpty.style.display   = 'none';
        if (draftReadContent) draftReadContent.style.display = 'flex';

        document.getElementById('eliteDraftReadSubj').textContent = d.subject || '(No subject)';
        document.getElementById('eliteDraftReadFrom').textContent = d.from    || '—';
        document.getElementById('eliteDraftReadTo').textContent   = d.to      || '—';
        document.getElementById('eliteDraftReadCc').textContent   = d.cc      || '—';
        document.getElementById('eliteDraftReadDate').textContent = d.date    || '—';

        var bodyEl  = document.getElementById('eliteDraftReadBody');
        var frameEl = document.getElementById('eliteDraftReadFrame');
        var body    = d.body || '';

        if (looksLikeHtml(body)) {
            bodyEl.style.display  = 'none';
            autoResizeFrame(frameEl);
            frameEl.style.display = 'block';
            frameEl.srcdoc = safeSrcdoc(body);
        } else {
            frameEl.style.display = 'none'; frameEl.style.height = ''; frameEl.srcdoc = '';
            bodyEl.style.display  = 'block';
            bodyEl.textContent    = body;
        }

        /* Wire "Edit & Send" button */
        var editBtn = document.getElementById('eliteDraftBtnEdit');
        if (editBtn) {
            editBtn.onclick = function () {
                dispatchComposePrefill({
                    title: 'Edit Draft',
                    from: d.from || '',
                    to: d.to || '',
                    cc: d.cc || '',
                    subject: (d.subject && d.subject !== '(No subject)') ? d.subject : '',
                    body: d.body || ''
                });
            };
        }

        /* Highlight active row */
        draftList.querySelectorAll('.email-row').forEach(function (r) { r.classList.remove('active'); });
        if (d._el) d._el.classList.add('active');
    }

    document.querySelector('.btn-fetch-drafts') && document.querySelector('.btn-fetch-drafts').addEventListener('click', function () {
        var btn = this;
        var toolbar = document.querySelector('#folderDrafts .inbox-toolbar');
        var search = (toolbar.querySelector('.folder-search').value||'').trim();
        var params = search ? ['search=' + encodeURIComponent(search)] : [];
        btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        apiFetch(DRAFTS_URL, params).then(function (data) {
            draftList.innerHTML = '';
            draftStore = {};
            /* Reset reading pane */
            if (draftReadEmpty)   draftReadEmpty.style.display   = 'flex';
            if (draftReadContent) draftReadContent.style.display = 'none';

            var rows = data.emails || [];
            if (rows.length === 0) {
                draftList.style.display = 'none'; draftEmpty.style.display = 'flex';
                return;
            }
            draftList.style.display = ''; draftEmpty.style.display = 'none';
            rows.forEach(function (d) {
                var li = document.createElement('li');
                li.className = 'email-row';
                li.setAttribute('role', 'option');
                li.setAttribute('tabindex', '0');
                li.innerHTML = '<span class="email-sender">' + escHtml(d.from||'—') + '</span>'
                             + '<span class="email-subject">' + escHtml(d.subject||'(No subject)') + '</span>'
                             + '<span class="email-date">'   + escHtml(d.date||'') + '</span>';
                d._el = li;
                draftStore[d.id] = d;
                li.addEventListener('click', function () { openDraftReading(d); });
                li.addEventListener('keydown', function (ev) {
                    if (ev.key === 'Enter' || ev.key === ' ') { ev.preventDefault(); openDraftReading(d); }
                });
                draftList.appendChild(li);
            });
        }).catch(function () { draftEmpty.style.display = 'flex'; })
          .finally(function () { btn.disabled = false; btn.innerHTML = '<i class="fas fa-sync-alt"></i> Get Drafts'; });
    });

    /* ═══════════════════════════════════════════════════════════════════════ */
    /* COMPOSE MODAL                                                             */
    /* ═══════════════════════════════════════════════════════════════════════ */
    (function () {
        var overlay        = document.getElementById('eliteComposeOverlay');
        var form           = document.getElementById('eliteComposeForm');
        var alertBar       = document.getElementById('eliteComposeAlert');
        var sendBtn        = document.getElementById('eliteComposeSend');
        var fromSel        = document.getElementById('eliteComposeFrom');
        if (fromSel) {
            fromSel.addEventListener('change', function () {
                fromSel.setCustomValidity('');
            });
        }
        var titleEl        = document.getElementById('eliteComposeTitle');
        var saveDraftBtn   = document.getElementById('eliteComposeSaveDraft');
        var attachBtn      = document.getElementById('eliteAttachBtn');
        var attachInput    = document.getElementById('eliteAttachmentInput');
        var attachList     = document.getElementById('eliteAttachmentList');
        var attachItems    = document.getElementById('eliteAttachmentItems');
        var SENDERS_URL    = @json(route('admin.outlook.senders', ['elite' => 1]));
        var SAVE_DRAFT_URL = @json(route('admin.outlook.saveDraft'));

        var sendersLoaded = false;
        var sendersLoading = false;
        var sendersWaiters = [];
        var selectedFiles = [];
        var composeBaseline = '';
        var composeReady = false;

        function setComposeTitle(title) {
            if (titleEl) titleEl.textContent = title || 'New Message';
        }

        function snapshotComposeState() {
            if (!form) return '';
            var toEl   = form.querySelector('[name="to"]');
            var ccEl   = form.querySelector('[name="cc"]');
            var subjEl = form.querySelector('[name="subject"]');
            var bodyEl = form.querySelector('[name="body"]');
            return [
                fromSel ? fromSel.value : '',
                toEl ? toEl.value : '',
                ccEl ? ccEl.value : '',
                subjEl ? subjEl.value : '',
                bodyEl ? bodyEl.value : '',
                String(selectedFiles.length)
            ].join('\x00');
        }

        function markComposeClean() {
            composeBaseline = snapshotComposeState();
        }

        function isComposeDirty() {
            return composeReady && composeBaseline !== '' && snapshotComposeState() !== composeBaseline;
        }

        function setComposeFrom(email) {
            if (!fromSel || !email) return;
            var want = extractEmailAddress(email).toLowerCase();
            if (!want) return;
            for (var i = 0; i < fromSel.options.length; i++) {
                if (fromSel.options[i].value.toLowerCase() === want) {
                    fromSel.selectedIndex = i;
                    return;
                }
            }
        }

        function validateRecipientField(value, label, required) {
            if (required === undefined) required = true;
            var trimmed = (value || '').trim();
            if (!trimmed) {
                return required ? (label + ' is required.') : '';
            }
            var parts = trimmed.split(/[,;]+/);
            var found = 0;
            for (var i = 0; i < parts.length; i++) {
                var raw = parts[i].trim();
                if (!raw) continue;
                var addr = extractEmailAddress(raw);
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(addr)) {
                    return 'Invalid email in ' + label.toLowerCase() + ': ' + raw;
                }
                found++;
            }
            if (found === 0) {
                return required
                    ? (label + ' is required.')
                    : ('Enter at least one valid email in ' + label + ', or leave it blank.');
            }
            return '';
        }

        function extractApiError(body) {
            if (!body) return '';
            if (body.errors && typeof body.errors === 'object') {
                var keys = Object.keys(body.errors);
                for (var i = 0; i < keys.length; i++) {
                    var msgs = body.errors[keys[i]];
                    if (msgs && msgs.length) return msgs[0];
                }
            }
            if (body.message && body.message !== 'The given data was invalid.') {
                return body.message;
            }
            return body.error || '';
        }

        function showComposeAlert(type, message) {
            if (!alertBar) return;
            var styles = {
                error: 'display:block;padding:10px 20px;font-size:13px;background:#fef2f2;color:#991b1b;border-bottom:1px solid #fecaca;',
                success: 'display:block;padding:10px 20px;font-size:13px;background:#dcfce7;color:#166534;border-bottom:1px solid #bbf7d0;',
                info: 'display:block;padding:10px 20px;font-size:13px;background:#eff6ff;color:#1e40af;border-bottom:1px solid #bfdbfe;'
            };
            alertBar.style.cssText = styles[type] || styles.error;
            alertBar.textContent = message;
        }

        /* ── Attachment helpers ──────────────────────────────────────────────── */
        function renderAttachments() {
            if (!attachItems || !attachList) return;
            attachItems.innerHTML = '';
            if (selectedFiles.length === 0) {
                attachList.style.display = 'none';
                return;
            }
            attachList.style.display = 'block';
            selectedFiles.forEach(function (file, idx) {
                var tag = document.createElement('div');
                tag.style.cssText = 'display:inline-flex;align-items:center;gap:5px;padding:3px 8px 3px 10px;background:#f1f5f9;border:1px solid #cbd5e1;border-radius:20px;font-size:12px;color:#334155;max-width:200px;';
                var name = document.createElement('span');
                name.style.cssText = 'overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:140px;';
                name.textContent = file.name;
                var rm = document.createElement('button');
                rm.type = 'button';
                rm.innerHTML = '&times;';
                rm.title = 'Remove';
                rm.style.cssText = 'border:none;background:transparent;color:#94a3b8;cursor:pointer;font-size:14px;line-height:1;padding:0;flex-shrink:0;';
                rm.setAttribute('data-idx', idx);
                rm.addEventListener('click', function () {
                    selectedFiles.splice(parseInt(this.getAttribute('data-idx'), 10), 1);
                    renderAttachments();
                });
                tag.appendChild(name);
                tag.appendChild(rm);
                attachItems.appendChild(tag);
            });
        }

        function clearAttachments() {
            selectedFiles = [];
            if (attachInput) attachInput.value = '';
            renderAttachments();
        }

        if (attachBtn && attachInput) {
            attachBtn.addEventListener('click', function () { attachInput.click(); });
            attachInput.addEventListener('change', function () {
                Array.prototype.forEach.call(attachInput.files, function (f) {
                    selectedFiles.push(f);
                });
                attachInput.value = ''; // reset so same file can be re-added
                renderAttachments();
            });
        }

        function validateComposeFrom() {
            if (!fromSel || !fromSel.value) {
                if (fromSel) {
                    fromSel.setCustomValidity('Please select a From address.');
                    fromSel.reportValidity();
                } else {
                    showComposeAlert('error', 'Please select a From address.');
                }
                return false;
            }
            fromSel.setCustomValidity('');
            return true;
        }

        /* ── Senders ─────────────────────────────────────────────────────────── */
        function loadSenders(done) {
            function flushWaiters() {
                var waiters = sendersWaiters.slice();
                sendersWaiters = [];
                waiters.forEach(function (fn) {
                    if (typeof fn === 'function') fn();
                });
            }
            if (sendersLoaded) {
                if (typeof done === 'function') done();
                return;
            }
            if (typeof done === 'function') sendersWaiters.push(done);
            if (sendersLoading) return;
            sendersLoading = true;
            fetch(SENDERS_URL, {
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json',
                           'X-CSRF-TOKEN': tokenMeta ? tokenMeta.getAttribute('content') : '' }
            }).then(function (r) { return r.json(); }).then(function (data) {
                var senders = data.senders || [];
                if (!fromSel) return;
                fromSel.innerHTML = '';
                if (senders.length === 0) {
                    fromSel.innerHTML = '<option value="">No verified senders found</option>';
                    return;
                }
                var placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = 'Select From Email address';
                placeholder.selected = true;
                fromSel.appendChild(placeholder);
                senders.forEach(function (s) {
                    var opt = document.createElement('option');
                    opt.value = s.email;
                    opt.textContent = s.name && s.name !== s.email ? s.name + ' <' + s.email + '>' : s.email;
                    fromSel.appendChild(opt);
                });
                sendersLoaded = true;
            }).catch(function () {
                if (fromSel) fromSel.innerHTML = '<option value="">Could not load senders</option>';
            }).finally(function () {
                sendersLoading = false;
                flushWaiters();
            });
        }

        function prefillCompose(opts) {
            opts = opts || {};
            if (!form || !overlay) return;

            form.reset();
            clearAttachments();
            if (alertBar) { alertBar.style.display = 'none'; alertBar.textContent = ''; }
            setComposeTitle(opts.title || 'New Message');

            var toEl   = form.querySelector('[name="to"]');
            var ccEl   = form.querySelector('[name="cc"]');
            var subjEl = form.querySelector('[name="subject"]');
            var bodyEl = form.querySelector('[name="body"]');

            if (toEl && opts.to) {
                var toRaw = String(opts.to).trim();
                if (toRaw.indexOf(',') === -1 && toRaw.indexOf(';') === -1) {
                    toEl.value = extractEmailAddress(toRaw) || toRaw;
                } else {
                    toEl.value = toRaw;
                }
            }
            if (ccEl && opts.cc) ccEl.value = opts.cc;
            if (subjEl && opts.subject) subjEl.value = opts.subject;
            if (bodyEl && opts.body) bodyEl.value = opts.body;

            overlay.style.display = 'flex';
            composeReady = false;
            loadSenders(function () {
                if (opts.from) setComposeFrom(opts.from);
                markComposeClean();
                composeReady = true;
                if (toEl) setTimeout(function () { toEl.focus(); }, 50);
            });
        }

        function openModal() {
            prefillCompose({ title: 'New Message' });
        }

        function closeModal(force) {
            if (!force && isComposeDirty() && !window.confirm('Discard this message?')) {
                return false;
            }
            if (overlay) overlay.style.display = 'none';
            composeBaseline = '';
            composeReady = false;
            return true;
        }

        function requestCloseModal() {
            closeModal(false);
        }

        var btnOpen   = document.getElementById('eliteBtnCompose');
        var btnClose  = document.getElementById('eliteComposeClose');
        var btnCancel = document.getElementById('eliteComposeCancel');
        if (btnOpen)   btnOpen.addEventListener('click', openModal);
        if (btnClose)  btnClose.addEventListener('click', requestCloseModal);
        if (btnCancel) btnCancel.addEventListener('click', requestCloseModal);
        document.addEventListener('elite:loadSenders', function () { loadSenders(); });
        document.addEventListener('elite:openModal', openModal);
        document.addEventListener('elite:composePrefill', function (ev) {
            prefillCompose(ev.detail || {});
        });

        if (form) {
            form.addEventListener('keydown', function (ev) {
                if (ev.key === 'Escape') {
                    ev.preventDefault();
                    requestCloseModal();
                }
                if ((ev.ctrlKey || ev.metaKey) && ev.key === 'Enter') {
                    ev.preventDefault();
                    if (sendBtn && !sendBtn.disabled) {
                        if (typeof form.requestSubmit === 'function') {
                            form.requestSubmit();
                        } else {
                            form.dispatchEvent(new Event('submit', { cancelable: true }));
                        }
                    }
                }
            });
        }

        /* ── Save Draft ──────────────────────────────────────────────────────── */
        if (saveDraftBtn) {
            saveDraftBtn.addEventListener('click', function () {
                var from    = fromSel    ? fromSel.value    : '';
                var toEl    = form.querySelector('[name="to"]');
                var ccEl    = form.querySelector('[name="cc"]');
                var subjEl  = form.querySelector('[name="subject"]');
                var bodyEl  = form.querySelector('[name="body"]');

                if (!from) {
                    showComposeAlert('error', 'Please select a From address before saving.');
                    if (fromSel) {
                        fromSel.setCustomValidity('Please select a From address.');
                        fromSel.reportValidity();
                    }
                    return;
                }
                if (fromSel) fromSel.setCustomValidity('');

                var toErr = validateRecipientField(toEl ? toEl.value : '', 'To', false);
                if (toErr) {
                    showComposeAlert('error', toErr);
                    return;
                }
                var ccErr = validateRecipientField(ccEl ? ccEl.value : '', 'Cc', false);
                if (ccErr) {
                    showComposeAlert('error', ccErr);
                    return;
                }

                saveDraftBtn.disabled   = true;
                saveDraftBtn.innerHTML  = '<i class="fas fa-spinner fa-spin"></i> Saving…';
                if (alertBar) alertBar.style.display = 'none';

                var payload = new URLSearchParams();
                payload.append('_token', tokenMeta ? tokenMeta.getAttribute('content') : '');
                payload.append('from',    from);
                payload.append('to',      toEl   ? toEl.value   : '');
                payload.append('cc',      ccEl   ? ccEl.value   : '');
                payload.append('subject', subjEl ? subjEl.value : '');
                payload.append('body',    bodyEl ? bodyEl.value : '');

                fetch(SAVE_DRAFT_URL, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': tokenMeta ? tokenMeta.getAttribute('content') : '',
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: payload.toString()
                }).then(function (res) {
                    return res.json().catch(function () { return { success: res.ok }; });
                }).then(function (data) {
                    if (data.success) {
                        var msg = data.message || 'Draft saved successfully.';
                        if (selectedFiles.length > 0) {
                            msg += ' Attachments are not saved with drafts.';
                        }
                        showComposeAlert('success', msg);
                        var prevFrom = fromSel ? fromSel.value : '';
                        form.reset();
                        clearAttachments();
                        if (fromSel && prevFrom) fromSel.value = prevFrom;
                        markComposeClean();
                        setTimeout(function () { closeModal(true); }, 1600);
                    } else {
                        showComposeAlert('error', extractApiError(data) || 'Could not save draft. Please try again.');
                    }
                }).catch(function () {
                    showComposeAlert('error', 'Network error. Please check your connection.');
                }).finally(function () {
                    saveDraftBtn.disabled  = false;
                    saveDraftBtn.innerHTML = '<i class="fas fa-save"></i> Save Draft';
                });
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function (ev) {
                if (ev.target === overlay) requestCloseModal();
            });
        }

        if (form) {
            form.addEventListener('submit', function (ev) {
                ev.preventDefault();
                if (alertBar) { alertBar.style.display = 'none'; }

                if (!validateComposeFrom()) {
                    return;
                }

                var toEl  = form.querySelector('[name="to"]');
                var ccEl  = form.querySelector('[name="cc"]');
                var toErr = validateRecipientField(toEl ? toEl.value : '', 'To', true);
                if (toErr) {
                    showComposeAlert('error', toErr);
                    return;
                }
                var ccErr = validateRecipientField(ccEl ? ccEl.value : '', 'Cc', false);
                if (ccErr) {
                    showComposeAlert('error', ccErr);
                    return;
                }

                if (sendBtn)  { sendBtn.disabled = true; sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending…'; }
                var fd = new FormData(form);
                // Append manually tracked files (file input is cleared after each selection)
                selectedFiles.forEach(function (file) {
                    fd.append('attachments[]', file, file.name);
                });
                var csrfToken = (tokenMeta ? tokenMeta.getAttribute('content') : '') || '';
                fetch(form.action, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: fd
                }).then(function (res) {
                    return res.json().catch(function () { return { ok: res.ok }; }).then(function (body) {
                        if (res.status === 401 || res.status === 419) {
                            showComposeAlert('error', 'Your session expired. Reloading so you can sign in again…');
                            setTimeout(function () { window.location.reload(); }, 1500);
                            return;
                        }
                        if (res.ok && body.ok !== false) {
                            showComposeAlert('success', body.message || 'Email sent successfully.');
                            var prevFrom = fromSel ? fromSel.value : '';
                            form.reset();
                            clearAttachments();
                            if (fromSel && prevFrom) fromSel.value = prevFrom;
                            markComposeClean();
                            startBurstPoll();
                            setTimeout(function () { closeModal(true); }, 1800);
                        } else {
                            var msg = extractApiError(body) || 'Failed to send. Please try again.';
                            showComposeAlert('error', msg);
                        }
                    });
                }).catch(function () {
                    showComposeAlert('error', 'Network error. Please check your connection and try again.');
                }).finally(function () {
                    if (sendBtn) { sendBtn.disabled = false; sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send'; }
                });
            });
        }
    }());

}());
</script>
@endpush
@endsection
