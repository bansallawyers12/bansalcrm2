@extends('layouts.outlook')
@section('title', 'Education Elite — Email')

@push('styles')
<style>
/* Reset browser defaults that create gaps */
html, body { margin: 0; padding: 0; height: 100%; }
#app { margin: 0; padding: 0; }
.loader { display: none !important; }

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

/* ── Sent view — list + reading pane (same as Outlook page) ─────────────── */
.sent-triple { flex: 1; display: flex; min-width: 0; min-height: 0; }
.sent-list-col {
    width: 440px; min-width: 280px; max-width: 50%;
    display: flex; flex-direction: column;
    border-right: 1px solid #d4d4d4; overflow: hidden; background: #fff;
}
.sent-reading-col { flex: 1; min-width: 260px; display: flex; flex-direction: column; background: #fff; overflow: hidden; }
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
.sent-reading-scroll { flex: 1; overflow-y: auto; padding: 20px 24px; }
.sent-read-subject { font-size: 18px; font-weight: 600; color: #0f172a; margin: 0 0 12px; line-height: 1.3; }
.sent-read-meta { font-size: 13px; color: #475569; margin-bottom: 16px; }
.sent-read-meta > div { margin-bottom: 5px; }
.sent-read-meta .ml { font-weight: 600; color: #64748b; min-width: 52px; display: inline-block; }
.sent-read-body { font-size: 14px; line-height: 1.6; color: #1e293b; word-break: break-word; white-space: pre-wrap; }
.sent-read-frame { width: 100%; flex: 1; min-height: 300px; border: none; margin-top: 8px; }

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

            {{-- Webhook URL footer + Option A (real mailbox + Inbound Parse) --}}
            @php
                $eliteApex = ltrim((string) config('crm.education_elite_sender_domain', 'educationelite.com.au'), '@');
                $inboundParseHost = trim((string) config('crm.education_elite_inbound_parse_host', ''));
            @endphp
            <div class="elite-sidebar-foot">
                <span class="foot-label">Inbound webhook</span>
                <code>{{ $webhookUrl ?? url('/elite/emails') }}</code>
                <div style="margin-top:5px;">
                    Apex domain: <strong>{{ '@'.$eliteApex }}</strong>
                    @if(config('crm.education_elite_inbound_secret'))
                        &nbsp;<span style="color:#16a34a;"><i class="fas fa-lock"></i></span>
                    @endif
                </div>
                <div style="margin-top:8px;font-size:11px;line-height:1.45;color:#444;">
                    <strong>Real mailbox + CRM (Option&nbsp;A):</strong>
                    (1) In SendGrid → Inbound Parse, use a host such as <code>{{ $inboundParseHost !== '' ? $inboundParseHost : 'parse.'.$eliteApex }}</code>
                    with MX to SendGrid and this POST URL.
                    (2) In Microsoft 365 / Outlook admin, on each real mailbox (e.g. <code>apply@{{ $eliteApex }}</code>), add a rule or forwarding to deliver a copy to
                    @if($inboundParseHost !== '')
                        <code>inbound@{{ $inboundParseHost }}</code> (or any local-part you create on that host).
                    @else
                        <code>anything@&lt;your-parse-host&gt;</code>. Set <code>EDUCATION_ELITE_INBOUND_PARSE_HOST</code> in <code>.env</code> to that host for a reminder here.
                    @endif
                    The CRM only lists mail SendGrid POSTs to the webhook; it does not read Outlook directly.
                    <br><strong>Replies in this inbox:</strong> set <code>EDUCATION_ELITE_INBOUND_PARSE_HOST</code> (e.g. <code>parse.{{ $eliteApex }}</code>) or <code>EDUCATION_ELITE_INBOUND_REPLY_TO</code> in <code>.env</code>. <strong>New Message</strong> then sets <strong>Reply-To</strong> to <code>inbound@&lt;parse-host&gt;</code> (or your explicit address) so when the contact replies in Outlook, SendGrid receives the reply and it appears here. Set <code>EDUCATION_ELITE_INBOUND_SET_REPLY_TO=false</code> to turn that off. Alternatively use an M365 forward of <code>info@…</code> to the parse address (Option&nbsp;A above).
                </div>
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
                            Point SendGrid Inbound Parse at the webhook. For a real mailbox on Microsoft, forward a copy to your Inbound Parse host (see sidebar). Apex:
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
                            <span>Drafts saved on the <a href="{{ route('admin.outlook.index') }}">Outlook page</a> using an Education Elite address will appear here.</span>
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
            <span style="font-size:15px;font-weight:600;color:#0f172a;">New Message</span>
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
                    <label style="min-width:56px;font-size:13px;color:#555;">From</label>
                    <select name="from" id="eliteComposeFrom"
                            style="flex:1;padding:8px 12px;border:1px solid #d4d4d4;border-radius:4px;font-size:13px;">
                        <option value="">Loading senders…</option>
                    </select>
                </div>
                {{-- To --}}
                <div style="display:flex;align-items:center;margin-bottom:12px;">
                    <label style="min-width:56px;font-size:13px;color:#555;">To</label>
                    <input type="email" name="to" id="eliteComposeTo" required placeholder="recipient@example.com"
                           style="flex:1;padding:8px 12px;border:1px solid #d4d4d4;border-radius:4px;font-size:13px;">
                </div>
                {{-- Cc --}}
                <div style="display:flex;align-items:center;margin-bottom:12px;">
                    <label style="min-width:56px;font-size:13px;color:#555;">Cc</label>
                    <input type="email" name="cc" placeholder="Optional"
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
                          style="flex:1;min-height:160px;padding:12px;border:1px solid #d4d4d4;border-radius:4px;font-size:14px;resize:vertical;font-family:inherit;line-height:1.5;"></textarea>
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
                        style="padding:9px 16px;background:#fff;color:#475569;border:1px solid #d4d4d4;border-radius:4px;font-size:13px;cursor:pointer;">
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
        return s;
    }

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

    /* ── Inbox fetch (shared by button + auto-poll) ───────────────────────── */
    var autoPolling = false;
    var autoPollTimer = null;
    var AUTO_POLL_MS = 30000; // 30 seconds

    function doFetchInbox(silent) {
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

        if (!silent && btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...'; }

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
    /* Resume / pause with tab visibility */
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) stopAutoPoll(); else startAutoPoll();
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
            frameEl.style.display = 'block';
            frameEl.srcdoc = safeSrcdoc(raw);
        } else {
            if (frameEl) { frameEl.style.display = 'none'; frameEl.srcdoc = ''; }
            bodyEl.style.display = '';
            bodyEl.textContent = raw || '(No content)';
        }
        var btnReply = document.getElementById('eliteSentBtnReply');
        var btnFwd   = document.getElementById('eliteSentBtnFwd');
        if (btnReply) btnReply.onclick = function() { window.location.href = '{{ route("admin.outlook.index") }}?reply_to=' + encodeURIComponent(e.from||''); };
        if (btnFwd)   btnFwd.onclick   = function() { window.location.href = '{{ route("admin.outlook.index") }}'; };
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

    var HTML_TAG_RE_D = /<([a-z][a-z0-9]*)\b[^>]*>/i;
    function looksLikeHtmlD(s) { return typeof s === 'string' && HTML_TAG_RE_D.test(s.trim()); }

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

        if (looksLikeHtmlD(body)) {
            bodyEl.style.display  = 'none';
            frameEl.style.display = 'block';
            frameEl.srcdoc = body;
        } else {
            frameEl.style.display = 'none';
            bodyEl.style.display  = 'block';
            bodyEl.textContent    = body;
        }

        /* Wire "Edit & Send" button */
        var editBtn = document.getElementById('eliteDraftBtnEdit');
        if (editBtn) {
            editBtn.onclick = function () {
                /* Pre-fill compose modal with draft data */
                var composeOverlay = document.getElementById('eliteComposeOverlay');
                var composeForm    = document.getElementById('eliteComposeForm');
                if (!composeOverlay || !composeForm) return;
                composeForm.reset();
                var toEl      = composeForm.querySelector('[name="to"]');
                var ccEl      = composeForm.querySelector('[name="cc"]');
                var subjEl    = composeForm.querySelector('[name="subject"]');
                var bodyTa    = composeForm.querySelector('[name="body"]');
                if (toEl   && d.to)      toEl.value   = d.to;
                if (ccEl   && d.cc)      ccEl.value   = d.cc;
                if (subjEl && d.subject) subjEl.value = (d.subject === '(No subject)') ? '' : d.subject;
                if (bodyTa && d.body)    bodyTa.value  = d.body;
                composeOverlay.style.display = 'flex';
                /* Load senders if not already done */
                document.getElementById('eliteBtnCompose') && document.getElementById('eliteBtnCompose').dispatchEvent(new CustomEvent('_loadSenders'));
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
        var saveDraftBtn   = document.getElementById('eliteComposeSaveDraft');
        var attachBtn      = document.getElementById('eliteAttachBtn');
        var attachInput    = document.getElementById('eliteAttachmentInput');
        var attachList     = document.getElementById('eliteAttachmentList');
        var attachItems    = document.getElementById('eliteAttachmentItems');
        var SENDERS_URL    = @json(route('admin.outlook.senders'));
        var SAVE_DRAFT_URL = @json(route('admin.outlook.saveDraft'));

        var sendersLoaded = false;
        var selectedFiles = []; // track File objects for manual FormData append

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
                    selectedFiles.splice(parseInt(this.getAttribute('data-idx')), 1);
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

        /* ── Senders ─────────────────────────────────────────────────────────── */
        function loadSenders() {
            if (sendersLoaded) return;
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
                senders.forEach(function (s) {
                    var opt = document.createElement('option');
                    opt.value = s.email;
                    opt.textContent = s.name && s.name !== s.email ? s.name + ' <' + s.email + '>' : s.email;
                    if (s.email === data.default_from) opt.selected = true;
                    fromSel.appendChild(opt);
                });
                sendersLoaded = true;
            }).catch(function () {
                if (fromSel) fromSel.innerHTML = '<option value="">Could not load senders</option>';
            });
        }

        function openModal() {
            if (form) form.reset();
            clearAttachments();
            if (alertBar) { alertBar.style.display = 'none'; alertBar.textContent = ''; }
            if (overlay) { overlay.style.display = 'flex'; }
            loadSenders();
            var toInput = document.getElementById('eliteComposeTo');
            if (toInput) setTimeout(function () { toInput.focus(); }, 50);
        }
        function closeModal() {
            if (overlay) overlay.style.display = 'none';
        }

        var btnOpen   = document.getElementById('eliteBtnCompose');
        var btnClose  = document.getElementById('eliteComposeClose');
        var btnCancel = document.getElementById('eliteComposeCancel');
        if (btnOpen)   btnOpen.addEventListener('click',   openModal);
        if (btnClose)  btnClose.addEventListener('click',  closeModal);
        if (btnCancel) btnCancel.addEventListener('click', closeModal);

        /* ── Save Draft ──────────────────────────────────────────────────────── */
        if (saveDraftBtn) {
            saveDraftBtn.addEventListener('click', function () {
                var from    = fromSel    ? fromSel.value    : '';
                var toEl    = form.querySelector('[name="to"]');
                var ccEl    = form.querySelector('[name="cc"]');
                var subjEl  = form.querySelector('[name="subject"]');
                var bodyEl  = form.querySelector('[name="body"]');

                if (!from) {
                    alertBar.style.cssText = 'display:block;padding:10px 20px;font-size:13px;background:#fef2f2;color:#991b1b;border-bottom:1px solid #fecaca;';
                    alertBar.textContent   = 'Please select a From address before saving.';
                    return;
                }

                saveDraftBtn.disabled   = true;
                saveDraftBtn.innerHTML  = '<i class="fas fa-spinner fa-spin"></i> Saving…';
                alertBar.style.display  = 'none';

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
                        alertBar.style.cssText = 'display:block;padding:10px 20px;font-size:13px;background:#dcfce7;color:#166534;border-bottom:1px solid #bbf7d0;';
                        alertBar.textContent   = data.message || 'Draft saved successfully.';
                        var prevFrom = fromSel ? fromSel.value : '';
                        form.reset();
                        clearAttachments();
                        if (fromSel && prevFrom) fromSel.value = prevFrom;
                        setTimeout(closeModal, 1600);
                    } else {
                        alertBar.style.cssText = 'display:block;padding:10px 20px;font-size:13px;background:#fef2f2;color:#991b1b;border-bottom:1px solid #fecaca;';
                        alertBar.textContent   = (data.message) || 'Could not save draft. Please try again.';
                    }
                }).catch(function () {
                    alertBar.style.cssText = 'display:block;padding:10px 20px;font-size:13px;background:#fef2f2;color:#991b1b;border-bottom:1px solid #fecaca;';
                    alertBar.textContent   = 'Network error. Please check your connection.';
                }).finally(function () {
                    saveDraftBtn.disabled  = false;
                    saveDraftBtn.innerHTML = '<i class="fas fa-save"></i> Save Draft';
                });
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function (ev) {
                if (ev.target === overlay) closeModal();
            });
        }

        if (form) {
            form.addEventListener('submit', function (ev) {
                ev.preventDefault();
                if (alertBar) { alertBar.style.display = 'none'; }
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
                        if (res.ok && body.ok !== false) {
                            alertBar.style.cssText = 'display:block;padding:10px 20px;font-size:13px;background:#dcfce7;color:#166534;border-bottom:1px solid #bbf7d0;';
                            alertBar.textContent = body.message || 'Email sent successfully.';
                            var prevFrom = fromSel ? fromSel.value : '';
                            form.reset();
                            clearAttachments();
                            if (fromSel && prevFrom) fromSel.value = prevFrom;
                            setTimeout(closeModal, 1800);
                        } else {
                            var msg = (body && body.message) ? body.message : 'Failed to send. Please try again.';
                            alertBar.style.cssText = 'display:block;padding:10px 20px;font-size:13px;background:#fef2f2;color:#991b1b;border-bottom:1px solid #fecaca;';
                            alertBar.textContent = msg;
                        }
                    });
                }).catch(function () {
                    alertBar.style.cssText = 'display:block;padding:10px 20px;font-size:13px;background:#fef2f2;color:#991b1b;border-bottom:1px solid #fecaca;';
                    alertBar.textContent = 'Network error. Please check your connection and try again.';
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
