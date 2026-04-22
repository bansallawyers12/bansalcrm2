@extends('layouts.outlook')
@section('title', 'Outlook - SendGrid Email')

@push('styles')
<style>
/* Outlook standalone - no CRM sidebar */
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
.outlook-topbar .outlook-title .outlook-title-name {
    font-family: Georgia, 'Times New Roman', Times, serif;
    font-weight: 600;
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

/* Main layout */
.outlook-container { display: flex; height: calc(100vh - 50px); min-height: 500px; }

/* Left sidebar */
.outlook-sidebar {
    width: 200px;
    min-width: 200px;
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
}
.outlook-sidebar .btn-compose:hover { background: #106ebe; color: #fff; }
.outlook-folders { padding: 8px 0; }
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
}
.outlook-folders .folder-item:hover { background: #f3f3f3; }
.outlook-folders .folder-item.active {
    background: #e5f3ff;
    color: #0078d4;
    font-weight: 600;
    border-left-color: #0078d4;
}
.outlook-folders .folder-item i { font-size: 14px; width: 18px; text-align: center; }

/* Main content area */
.outlook-main { flex: 1; display: flex; flex-direction: column; background: #fff; overflow: hidden; }

/* Ribbon tabs */
.ribbon-tabs {
    display: flex;
    gap: 0;
    padding: 0 16px;
    background: #f3f3f3;
    border-bottom: 1px solid #d4d4d4;
}
.ribbon-tabs .ribbon-tab {
    padding: 10px 16px;
    font-size: 13px;
    color: #333;
    background: none;
    border: none;
    cursor: pointer;
    border-bottom: 2px solid transparent;
}
.ribbon-tabs .ribbon-tab:hover { background: #e8e8e8; }
.ribbon-tabs .ribbon-tab.active { background: #fff; border-bottom-color: #fff; margin-bottom: -1px; }

/* Ribbon command bar */
.ribbon-commands {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 4px;
    padding: 8px 16px;
    background: #fff;
    border-bottom: 1px solid #d4d4d4;
}
.ribbon-commands .ribbon-group { display: flex; align-items: center; gap: 2px; padding-right: 12px; margin-right: 8px; border-right: 1px solid #e0e0e0; }
.ribbon-commands .ribbon-group:last-child { border-right: none; }
.ribbon-commands .ribbon-btn {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: transparent;
    border-radius: 4px;
    cursor: pointer;
    color: #333;
    font-size: 14px;
}
.ribbon-commands .ribbon-btn:hover { background: #e5f3ff; color: #0078d4; }
.ribbon-commands .ribbon-btn.primary { background: #0078d4; color: #fff; }
.ribbon-commands .ribbon-btn.primary:hover { background: #106ebe; color: #fff; }
.ribbon-commands .ribbon-btn.send-btn { width: auto; padding: 0 14px; font-weight: 600; }
/* Buttons with icon + label (e.g. Insert tab) - prevent overlap */
.ribbon-commands .ribbon-btn.ribbon-btn-with-label { width: auto; min-width: 32px; padding: 0 10px; gap: 6px; font-size: 13px; white-space: nowrap; margin-right: 4px; }
.ribbon-commands select.ribbon-select { padding: 4px 8px; font-size: 12px; border: 1px solid #ccc; border-radius: 4px; }

/* Ribbon panels (one visible per tab) */
.ribbon-panel { display: none; flex-wrap: wrap; align-items: center; gap: 4px; padding: 8px 16px; background: #fff; border-bottom: 1px solid #d4d4d4; }
.ribbon-panel.active { display: flex; }
.ribbon-panel.ribbon-panel-placeholder { color: #666; font-size: 13px; }

/* Compose area */
.compose-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
.compose-ribbon-wrapper { flex-shrink: 0; border-bottom: 1px solid #d4d4d4; }
.draft-saved-msg {
    padding: 8px 20px;
    background: #dcfce7;
    color: #166534;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.draft-saved-msg i { color: #16a34a; }
.compose-body-row { flex: 1; display: flex; overflow: hidden; min-height: 0; }
.compose-fields-full { padding-left: 20px; }
.compose-send-col { display: none; }
.compose-send-col .btn-send-main {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 12px 16px;
    background: #e0e0e0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    color: #333;
    font-size: 12px;
    font-weight: 600;
}
.compose-send-col .btn-send-main:hover { background: #d0d0d0; }
.compose-send-col .btn-send-main i { font-size: 20px; }

.compose-fields-col { flex: 1; display: flex; flex-direction: column; overflow: hidden; padding: 16px 20px; }
.compose-field { display: flex; align-items: center; margin-bottom: 12px; }
.compose-field .field-label {
    min-width: 60px;
    font-size: 13px;
    color: #555;
}
.compose-field .field-input {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid #d4d4d4;
    border-radius: 4px;
    font-size: 13px;
}
.compose-field .field-input:focus { outline: none; border-color: #0078d4; box-shadow: 0 0 0 1px #0078d4; }

.compose-body-wrap { flex: 1; min-height: 200px; display: flex; flex-direction: column; margin-top: 12px; }
.compose-body-wrap .body-label { font-size: 13px; color: #555; margin-bottom: 8px; }
.compose-body-wrap .outlook-body-editor {
    flex: 1;
    min-height: 240px;
    padding: 12px;
    border: 1px solid #d4d4d4;
    border-radius: 4px;
    font-size: 14px;
    overflow-y: auto;
    background: #fff;
}
.compose-body-wrap .outlook-body-editor:focus { outline: none; border-color: #0078d4; }

/* Folder views (Inbox, Sent, Drafts, Trash) - each is a separate page */
.folder-view { flex: 1; display: none; flex-direction: column; overflow: hidden; }
.outlook-main.mode-inbox .view-inbox,
.outlook-main.mode-sent .view-sent,
.outlook-main.mode-drafts .view-drafts,
.outlook-main.mode-trash .view-trash { display: flex !important; }
.folder-view .inbox-toolbar {
    padding: 10px 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
}
.inbox-toolbar .search-wrap { flex: 1; min-width: 180px; max-width: 240px; position: relative; }
.inbox-toolbar .search-wrap input {
    width: 100%;
    padding: 8px 12px 8px 36px;
    border: 1px solid #d4d4d4;
    border-radius: 4px;
    font-size: 13px;
}
.inbox-toolbar .ms-2 { margin-left: 0; }
.inbox-toolbar .search-wrap i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
}
.inbox-toolbar .filter-select,
.inbox-toolbar .filter-date { padding: 6px 10px; font-size: 12px; border: 1px solid #d4d4d4; border-radius: 4px; min-width: 100px; }
.inbox-toolbar .filter-from-to { min-width: 160px; max-width: 200px; }
.inbox-toolbar .filter-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.inbox-toolbar .filter-attach { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #555; white-space: nowrap; }
.inbox-toolbar .filter-attach input { margin: 0; }

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
.empty-state span { font-size: 13px; }

.email-list { list-style: none; margin: 0; padding: 0; }
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
.email-list .email-row .email-date { color: #94a3b8; font-size: 13px; }

/* Sent view: 3-col split (list + reading pane) identical to inbox */
.sent-triple { flex: 1; display: flex; min-width: 0; min-height: 0; }
.sent-list-col {
    width: 440px;
    min-width: 300px;
    max-width: 50%;
    display: flex;
    flex-direction: column;
    border-right: 1px solid #d4d4d4;
    overflow: hidden;
    background: #fff;
}
.sent-reading-col {
    flex: 1;
    min-width: 300px;
    display: flex;
    flex-direction: column;
    background: #fff;
    overflow: hidden;
}
.sent-list { list-style: none; margin: 0; padding: 0; overflow-y: auto; flex: 1; }
.sent-msg-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 20px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    border-left: 3px solid transparent;
}
.sent-msg-item:hover { background: #f8fafc; }
.sent-msg-item.is-selected { background: #eef6fc; border-left-color: #0078d4; }
.sent-icon { width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; margin-top: 2px; background: #e0f2fe; color: #0078d4; }
.sent-msg-main { flex: 1; min-width: 0; }
.sent-msg-line1 { display: flex; align-items: baseline; justify-content: space-between; gap: 8px; margin-bottom: 3px; }
.sent-msg-to { font-weight: 600; font-size: 13.5px; color: #0f172a; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.sent-msg-date { font-size: 12px; color: #94a3b8; flex-shrink: 0; }
.sent-msg-from { font-size: 12px; color: #64748b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-bottom: 2px; }
.sent-msg-subj { font-size: 13px; color: #64748b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
/* Reading pane for sent */
.sent-reading-empty { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; text-align: center; padding: 32px; }
.sent-reading-empty i { font-size: 40px; margin-bottom: 12px; opacity: 0.5; }
.sent-reading-content { flex: 1; min-height: 0; display: flex; flex-direction: column; overflow: hidden; }
.sent-reading-scroll { flex: 1; overflow-y: auto; padding: 20px 24px; }
.sent-read-actions { display: flex; gap: 8px; margin-bottom: 16px; flex-shrink: 0; border-bottom: 1px solid #e2e8f0; padding: 12px 24px; }
.sent-read-actions .btn-read-action {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px; border: 1px solid #d4d4d4; background: #fff;
    border-radius: 4px; font-size: 13px; cursor: pointer; color: #333;
}
.sent-read-actions .btn-read-action:hover { background: #f3f3f3; }
.sent-read-subject { font-size: 18px; font-weight: 600; color: #0f172a; margin: 0 0 12px; line-height: 1.3; }
.sent-read-meta { font-size: 13px; color: #475569; margin-bottom: 16px; }
.sent-read-meta > div { margin-bottom: 5px; }
.sent-read-meta .meta-label { font-weight: 600; color: #64748b; min-width: 52px; display: inline-block; }
.sent-read-body { font-size: 14px; line-height: 1.6; color: #1e293b; word-break: break-word; white-space: pre-wrap; }
.sent-read-frame { width: 100%; flex: 1; min-height: 300px; border: none; margin-top: 8px; }

/* Legacy kept for .sent-section-header .sent-toggle (may still exist in DOM) */
.sent-section-header .sent-toggle {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: transparent;
    border-radius: 4px;
    cursor: pointer;
    color: #475569;
    flex-shrink: 0;
}
.sent-section-header .sent-toggle:hover { background: #e2e8f0; color: #1e293b; }
.sent-section-body { display: block; }
.sent-section.collapsed .sent-section-body { display: none; }
.sent-section.collapsed .sent-section-header { border-bottom: none; }
.sent-section.collapsed .sent-toggle i { transform: rotate(-90deg); }

/* Sent view filter */
#folderSent .sent-filter-wrap { display: flex; align-items: center; gap: 8px; margin-left: 12px; }
#folderSent .sent-filter-wrap label { font-size: 13px; color: #64748b; margin: 0; white-space: nowrap; }
#folderSent .sent-filter-select { padding: 6px 10px; font-size: 13px; border: 1px solid #d4d4d4; border-radius: 4px; min-width: 200px; }
.sent-table { width: 100%; border-collapse: collapse; }
.sent-table th {
    text-align: left;
    padding: 10px 20px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    border-bottom: 1px solid #e2e8f0;
    background: #fafafa;
}
.sent-table th.sent-th-to { width: 28%; min-width: 160px; }
.sent-table th.sent-th-subject { width: 50%; }
.sent-table th.sent-th-date { width: 22%; min-width: 120px; }
.sent-table td {
    padding: 12px 20px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13px;
    vertical-align: middle;
}
.sent-table tr.sent-row { cursor: pointer; }
.sent-table tr.sent-row:hover { background: #f8fafc; }
.sent-table .sent-cell-to { color: #1e293b; font-weight: 500; }
.sent-table .sent-cell-subject { color: #475569; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 0; }
.sent-table .sent-cell-date { color: #94a3b8; font-size: 12px; }

/* Sent email view modal */
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
.sent-email-modal .email-body-wrap iframe { width: 100%; min-height: 200px; border: none; }

.view-compose { display: none !important; }
.outlook-main.mode-compose .view-inbox,
.outlook-main.mode-compose .view-sent,
.outlook-main.mode-compose .view-drafts,
.outlook-main.mode-compose .view-trash { display: none !important; }
.outlook-main.mode-compose .view-compose { display: flex !important; }
.outlook-main.mode-inbox .view-inbox { display: flex !important; }
.outlook-main.mode-sent .view-sent { display: flex !important; }
.outlook-main.mode-drafts .view-drafts { display: flex !important; }
.outlook-main.mode-trash .view-trash { display: flex !important; }

.attachment-list {
    margin-top: 10px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.attachment-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    background: #e5f3ff;
    border-radius: 4px;
    font-size: 12px;
    color: #0078d4;
}
.attachment-item .remove-attach { cursor: pointer; color: #666; }
.attachment-item .remove-attach:hover { color: #c00; }

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
}
</style>
@endpush

@section('content')
<div class="outlook-page">
    <header class="outlook-topbar">
        <h1 class="outlook-title"><span class="outlook-title-name">Outlook</span> <span class="sendgrid-badge">SendGrid</span></h1>
        <a href="{{ route('dashboard') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Back to CRM</a>
    </header>

    <div class="server-error">@include('../Elements/flash-message')</div>

    <div class="outlook-container">
        <aside class="outlook-sidebar">
            <button type="button" class="btn btn-compose" id="btnNewMessage">
                <i class="fas fa-plus" aria-hidden="true"></i> New Message
            </button>
            <nav class="outlook-folders">
                <a href="#" class="folder-item active" data-view="inbox"><i class="fas fa-inbox"></i> Inbox</a>
                <a href="#" class="folder-item" data-view="sent"><i class="fas fa-paper-plane"></i> Sent</a>
                <a href="#" class="folder-item" data-view="drafts"><i class="fas fa-file-alt"></i> Drafts</a>
                <a href="#" class="folder-item" data-view="trash"><i class="fas fa-trash"></i> Trash</a>
            </nav>
        </aside>

        <main class="outlook-main landing-inbox mode-inbox" id="outlookMain">
            {{-- Inbox view --}}
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
                    <label class="filter-attach">
                        <input type="checkbox" class="filter-has-attachments" data-folder="inbox"> With attachments
                    </label>
                    <button type="button" class="btn btn-primary btn-sm ms-2 btn-fetch" data-folder="inbox">
                        <i class="fas fa-sync-alt"></i> Get Emails
                    </button>
                </div>
                <ul class="email-list folder-list"></ul>
                <div class="empty-state folder-empty">
                    <i class="fas fa-inbox"></i>
                    <p>Your inbox is empty</p>
                    <span>Click "Get Emails" to fetch from SendGrid, or connect the API to receive emails automatically.</span>
                </div>
            </div>

            {{-- Sent view: list + reading pane (like inbox) --}}
            <div class="folder-view view-sent" id="folderSent">
                {{-- Toolbar --}}
                <div class="inbox-toolbar">
                    <div class="search-wrap">
                        <i class="fas fa-search"></i>
                        <input type="text" class="form-control folder-search" placeholder="Search sent...">
                    </div>
                    <select class="filter-select filter-date-range" data-folder="sent">
                        <option value="" selected>All time</option>
                        <option value="today">Today</option>
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="custom">Custom range</option>
                    </select>
                    <span class="filter-custom-dates filter-custom-sent" style="display:none;">
                        <input type="date" class="filter-date filter-date-from" data-folder="sent">
                        <input type="date" class="filter-date filter-date-to" data-folder="sent">
                    </span>
                    <select class="filter-select filter-sort" data-folder="sent">
                        <option value="newest">Newest first</option>
                        <option value="oldest">Oldest first</option>
                    </select>
                    <select class="filter-select filter-from filter-from-to" data-folder="sent" style="max-width:180px;">
                        <option value="">All senders</option>
                    </select>
                    <select class="filter-select filter-to filter-from-to" data-folder="sent" style="max-width:180px;">
                        <option value="">All recipients</option>
                    </select>
                    <button type="button" class="btn btn-primary btn-sm ms-2 btn-fetch" data-folder="sent">
                        <i class="fas fa-sync-alt"></i> Get Emails
                    </button>
                </div>

                {{-- 3-col: list + reading pane --}}
                <div class="sent-triple">
                    {{-- List --}}
                    <div class="sent-list-col">
                        <ul class="sent-list folder-list" id="sentEmailList" role="listbox" aria-label="Sent messages"></ul>
                        <div class="empty-state folder-empty" id="sentEmpty">
                            <i class="fas fa-paper-plane"></i>
                            <p>No sent messages</p>
                            <span>All emails you send via this page appear here. Click "Get Emails" to load.</span>
                        </div>
                    </div>

                    {{-- Reading pane --}}
                    <div class="sent-reading-col" id="sentReadingCol">
                        <div class="sent-reading-empty" id="sentReadingEmpty">
                            <i class="fas fa-paper-plane" aria-hidden="true"></i>
                            <p>Select a sent email to preview it.</p>
                        </div>
                        <div class="sent-reading-content" id="sentReadingContent" style="display:none;">
                            <div class="sent-read-actions">
                                <button type="button" class="btn-read-action" id="sentBtnReply">
                                    <i class="fas fa-reply"></i> Reply
                                </button>
                                <button type="button" class="btn-read-action" id="sentBtnForward">
                                    <i class="fas fa-share"></i> Forward
                                </button>
                            </div>
                            <div class="sent-reading-scroll" id="sentReadingScroll">
                                <h2 class="sent-read-subject" id="sentReadSubject"></h2>
                                <div class="sent-read-meta">
                                    <div><span class="meta-label">From:</span> <span id="sentReadFrom"></span></div>
                                    <div><span class="meta-label">To:</span>   <span id="sentReadTo"></span></div>
                                    <div><span class="meta-label">Cc:</span>   <span id="sentReadCc"></span></div>
                                    <div><span class="meta-label">Date:</span> <span id="sentReadDate"></span></div>
                                </div>
                                <div class="sent-read-body" id="sentReadBody"></div>
                                <iframe id="sentReadFrame" class="sent-read-frame" title="Email body" sandbox="allow-same-origin" style="display:none;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Drafts view --}}
            <div class="folder-view view-drafts" id="folderDrafts">
                <div class="inbox-toolbar">
                    <div class="search-wrap">
                        <i class="fas fa-search"></i>
                        <input type="text" class="form-control folder-search" placeholder="Search drafts...">
                    </div>
                    <button type="button" class="btn btn-primary btn-sm ms-2 btn-fetch" data-folder="drafts">
                        <i class="fas fa-sync-alt"></i> Get Drafts
                    </button>
                </div>
                <ul class="email-list folder-list"></ul>
                <div class="empty-state folder-empty">
                    <i class="fas fa-file-alt"></i>
                    <p>No drafts</p>
                    <span>Drafts you save will appear here.</span>
                </div>
            </div>

            {{-- Trash view --}}
            <div class="folder-view view-trash" id="folderTrash">
                <div class="inbox-toolbar">
                    <div class="search-wrap">
                        <i class="fas fa-search"></i>
                        <input type="text" class="form-control folder-search" placeholder="Search trash...">
                    </div>
                    <button type="button" class="btn btn-primary btn-sm ms-2 btn-fetch" data-folder="trash">
                        <i class="fas fa-sync-alt"></i> Get Emails
                    </button>
                </div>
                <ul class="email-list folder-list"></ul>
                <div class="empty-state folder-empty">
                    <i class="fas fa-trash"></i>
                    <p>Trash is empty</p>
                    <span>Deleted emails will appear here.</span>
                </div>
            </div>

            {{-- Compose view (Outlook-style) --}}
            <div class="compose-area view-compose">
                {{-- Ribbon (full width on top) --}}
                <div class="compose-ribbon-wrapper">
                    <div class="ribbon-tabs">
                        <button type="button" class="ribbon-tab" data-tab="message">File</button>
                        <button type="button" class="ribbon-tab active" data-tab="message">Message</button>
                        <button type="button" class="ribbon-tab" data-tab="insert">Insert</button>
                        <button type="button" class="ribbon-tab" data-tab="extras">Draw</button>
                        <button type="button" class="ribbon-tab" data-tab="extras">Options</button>
                        <button type="button" class="ribbon-tab" data-tab="message">Format Text</button>
                        <button type="button" class="ribbon-tab" data-tab="extras">Review</button>
                        <button type="button" class="ribbon-tab" data-tab="extras">Help</button>
                    </div>
                    {{-- Message / Format Text panel (main toolbar) --}}
                    <div class="ribbon-panel active ribbon-commands" id="ribbon-panel-message" data-panel="message">
                        <div class="ribbon-group">
                            <button type="button" class="ribbon-btn send-btn primary" id="btnSend" title="Send">
                                <i class="fas fa-paper-plane"></i> Send
                            </button>
                            <button type="button" class="ribbon-btn send-btn" id="btnSaveDraft" title="Save Draft">
                                <i class="fas fa-save"></i> Save Draft
                            </button>
                        </div>
                        <div class="ribbon-group">
                            <button type="button" class="ribbon-btn btn-attach" title="Attach"><i class="fas fa-paperclip"></i></button>
                        </div>
                        <div class="ribbon-group">
                            <button type="button" class="ribbon-btn ribbon-undo" title="Undo"><i class="fas fa-undo"></i></button>
                            <button type="button" class="ribbon-btn ribbon-redo" title="Redo"><i class="fas fa-redo"></i></button>
                        </div>
                        <div class="ribbon-group">
                            <select class="ribbon-select" id="ribbonFont" title="Font">
                                <option value="Arial">Arial</option>
                                <option value="Calibri" selected>Calibri</option>
                                <option value="Cambria">Cambria</option>
                                <option value="Comic Sans MS">Comic Sans MS</option>
                                <option value="Consolas">Consolas</option>
                                <option value="Courier New">Courier New</option>
                                <option value="Georgia">Georgia</option>
                                <option value="Helvetica">Helvetica</option>
                                <option value="Impact">Impact</option>
                                <option value="Lucida Console">Lucida Console</option>
                                <option value="Lucida Sans Unicode">Lucida Sans Unicode</option>
                                <option value="Microsoft Sans Serif">Microsoft Sans Serif</option>
                                <option value="Palatino Linotype">Palatino Linotype</option>
                                <option value="Segoe UI">Segoe UI</option>
                                <option value="Tahoma">Tahoma</option>
                                <option value="Times New Roman">Times New Roman</option>
                                <option value="Trebuchet MS">Trebuchet MS</option>
                                <option value="Verdana">Verdana</option>
                            </select>
                            <select class="ribbon-select" id="ribbonSize" title="Font size">
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="14" selected>14</option>
                                <option value="16">16</option>
                                <option value="18">18</option>
                                <option value="20">20</option>
                                <option value="24">24</option>
                                <option value="28">28</option>
                                <option value="36">36</option>
                                <option value="48">48</option>
                                <option value="72">72</option>
                            </select>
                        </div>
                        <div class="ribbon-group">
                            <button type="button" class="ribbon-btn format-btn" data-cmd="bold" title="Bold"><i class="fas fa-bold"></i></button>
                            <button type="button" class="ribbon-btn format-btn" data-cmd="italic" title="Italic"><i class="fas fa-italic"></i></button>
                            <button type="button" class="ribbon-btn format-btn" data-cmd="underline" title="Underline"><i class="fas fa-underline"></i></button>
                            <button type="button" class="ribbon-btn format-btn" data-cmd="strikeThrough" title="Strikethrough"><i class="fas fa-strikethrough"></i></button>
                        </div>
                        <div class="ribbon-group">
                            <button type="button" class="ribbon-btn format-btn" data-cmd="justifyLeft" title="Align left"><i class="fas fa-align-left"></i></button>
                            <button type="button" class="ribbon-btn format-btn" data-cmd="justifyCenter" title="Align center"><i class="fas fa-align-center"></i></button>
                            <button type="button" class="ribbon-btn format-btn" data-cmd="justifyRight" title="Align right"><i class="fas fa-align-right"></i></button>
                            <button type="button" class="ribbon-btn format-btn" data-cmd="insertUnorderedList" title="Bullet list"><i class="fas fa-list-ul"></i></button>
                            <button type="button" class="ribbon-btn format-btn" data-cmd="insertOrderedList" title="Numbered list"><i class="fas fa-list-ol"></i></button>
                        </div>
                        <div class="ribbon-group">
                            <button type="button" class="ribbon-btn" title="Address Book"><i class="fas fa-address-book"></i></button>
                            <button type="button" class="ribbon-btn btn-attach" title="Attach File"><i class="fas fa-paperclip"></i></button>
                            <button type="button" class="ribbon-btn" title="Signature"><i class="fas fa-signature"></i></button>
                            <button type="button" class="ribbon-btn" title="Dictate"><i class="fas fa-microphone"></i></button>
                        </div>
                    </div>
                    {{-- Insert panel --}}
                    <div class="ribbon-panel ribbon-commands" id="ribbon-panel-insert" data-panel="insert">
                        <div class="ribbon-group">
                            <button type="button" class="ribbon-btn ribbon-btn-with-label" id="ribbonInsertLink" title="Insert link"><i class="fas fa-link"></i> Link</button>
                            <button type="button" class="ribbon-btn ribbon-btn-with-label" id="ribbonInsertImage" title="Insert image"><i class="fas fa-image"></i> Picture</button>
                            <button type="button" class="ribbon-btn ribbon-btn-with-label format-btn" data-cmd="insertHorizontalRule" title="Horizontal line"><i class="fas fa-minus"></i> Line</button>
                        </div>
                    </div>
                    {{-- Draw / Options / Review / Help placeholder --}}
                    <div class="ribbon-panel ribbon-panel-placeholder" id="ribbon-panel-extras" data-panel="extras">
                        <span>Use the <strong>Message</strong> or <strong>Format Text</strong> tab for formatting and sending. Insert links and pictures from the <strong>Insert</strong> tab.</span>
                    </div>
                </div>

                {{-- Draft saved message (hidden by default) --}}
                <div id="draftSavedMessage" class="draft-saved-msg" style="display:none;">
                    <i class="fas fa-check-circle"></i> Draft saved. Open Drafts to see it.
                </div>
                {{-- Compose form: From/To/Cc/Subject/Body (single Send in ribbon) --}}
                <div class="compose-body-row">
                <div class="compose-fields-col compose-fields-full">
                    <form id="composeForm" method="post" action="{{ route('admin.outlook.send') }}" autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        <div class="compose-field">
                            <span class="field-label">From</span>
                            <select class="field-input" name="from" id="from_email" required>
                                <option value="">Select sender email</option>
                                @forelse($verifiedSenders ?? [] as $sender)
                                    <option value="{{ $sender['email'] }}" {{ ($sender['email'] ?? '') === $fromEmail ? 'selected' : '' }}>
                                        {{ $sender['name'] }} &lt;{{ $sender['email'] }}&gt;
                                    </option>
                                @empty
                                    @if(!empty($fromEmail))
                                        <option value="{{ $fromEmail }}" selected>{{ $fromEmail }}</option>
                                    @else
                                        <option value="">No verified senders found</option>
                                    @endif
                                @endforelse
                            </select>
                        </div>
                        <div class="compose-field">
                            <span class="field-label">To</span>
                            <input type="email" class="field-input" name="to" id="to_email" placeholder="Enter recipient email" required>
                        </div>
                        <div class="compose-field">
                            <span class="field-label">Cc</span>
                            <input type="email" class="field-input" name="cc" id="cc_email" placeholder="Optional">
                        </div>
                        <div class="compose-field">
                            <span class="field-label">Subject</span>
                            <input type="text" class="field-input" name="subject" id="subject_email" placeholder="Subject" required>
                        </div>
                        <div class="compose-body-wrap">
                            <label class="body-label">Message</label>
                            <div id="outlook-body" contenteditable="true" class="outlook-body-editor"></div>
                            <input type="hidden" name="body" id="body-hidden">
                        </div>
                        <input type="file" id="attachmentInput" name="attachments[]" multiple style="display:none;">
                        <div id="attachmentList" class="attachment-list"></div>
                        <button type="submit" id="composeFormSubmit" style="display:none;">                        </button>
                    </form>
                </div>
                </div>
            </div>
        </main>
    </div>

    {{-- Modal: view full sent email --}}
    <div id="sentEmailModalOverlay" class="sent-email-modal-overlay" aria-hidden="true">
        <div class="sent-email-modal" role="dialog" aria-labelledby="sentEmailModalTitle">
            <div class="modal-header">
                <h3 id="sentEmailModalTitle">Sent email</h3>
                <button type="button" class="modal-close" id="sentEmailModalClose" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="email-meta-row"><span class="email-meta-label">From:</span> <span id="sentEmailFrom"></span></div>
                <div class="email-meta-row"><span class="email-meta-label">To:</span> <span id="sentEmailTo"></span></div>
                <div class="email-meta-row"><span class="email-meta-label">Cc:</span> <span id="sentEmailCc"></span></div>
                <div class="email-meta-row"><span class="email-meta-label">Subject:</span> <span id="sentEmailSubject"></span></div>
                <div class="email-meta-row"><span class="email-meta-label">Date:</span> <span id="sentEmailDate"></span></div>
                <div class="email-body-wrap">
                    <div class="email-meta-label">Message</div>
                    <div id="sentEmailBody"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var main = document.getElementById('outlookMain');
    var form = document.getElementById('composeForm');
    var attachmentInput = document.getElementById('attachmentInput');
    var attachmentList = document.getElementById('attachmentList');
    var sendersUrl = '{{ route("admin.outlook.senders") }}';
    var draftUrl = '{{ route("admin.outlook.saveDraft") }}';
    var refreshSentAfterSend = @json(session('refresh_sent', false));

    // Refresh From dropdown from SendGrid (live) on page load
    function refreshFromSenders() {
        var select = document.getElementById('from_email');
        if (!select) return;
        fetch(sendersUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var senders = data.senders || [];
                var defaultFrom = data.default_from || '';
                select.innerHTML = '';
                var opt0 = document.createElement('option');
                opt0.value = '';
                opt0.textContent = 'Select sender email';
                select.appendChild(opt0);
                senders.forEach(function(s) {
                    var opt = document.createElement('option');
                    opt.value = s.email;
                    opt.textContent = (s.name || s.email) + ' <' + s.email + '>';
                    if (s.email === defaultFrom) opt.selected = true;
                    select.appendChild(opt);
                });
                if (senders.length === 0 && defaultFrom) {
                    var fallback = document.createElement('option');
                    fallback.value = defaultFrom;
                    fallback.textContent = defaultFrom;
                    fallback.selected = true;
                    select.appendChild(fallback);
                }
            })
            .catch(function() { /* keep server-rendered options on error */ });
    }
    refreshFromSenders();

    document.getElementById('btnNewMessage').addEventListener('click', function() {
        main.classList.remove('mode-inbox', 'mode-sent', 'mode-drafts', 'mode-trash');
        main.classList.add('mode-compose');
    });

    // Ribbon tabs: switch active tab and show matching panel
    document.querySelectorAll('.compose-ribbon-wrapper .ribbon-tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            var panelId = this.getAttribute('data-tab');
            document.querySelectorAll('.compose-ribbon-wrapper .ribbon-tab').forEach(function(t) { t.classList.remove('active'); });
            this.classList.add('active');
            document.querySelectorAll('.compose-ribbon-wrapper .ribbon-panel').forEach(function(p) { p.classList.remove('active'); });
            var panel = document.getElementById('ribbon-panel-' + panelId);
            if (panel) panel.classList.add('active');
        });
    });

    // Insert panel: Link
    var insertLinkBtn = document.getElementById('ribbonInsertLink');
    if (insertLinkBtn) {
        insertLinkBtn.addEventListener('click', function() {
            var editor = document.getElementById('outlook-body');
            if (!editor) return;
            editor.focus();
            var url = window.prompt('Enter URL:', 'https://');
            if (url) document.execCommand('createLink', false, url);
        });
    }
    // Insert panel: Image (URL)
    var insertImageBtn = document.getElementById('ribbonInsertImage');
    if (insertImageBtn) {
        insertImageBtn.addEventListener('click', function() {
            var editor = document.getElementById('outlook-body');
            if (!editor) return;
            editor.focus();
            var url = window.prompt('Enter image URL:', 'https://');
            if (url) document.execCommand('insertImage', false, url);
        });
    }

    document.querySelectorAll('.folder-item').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            var folder = this.dataset.view;
            main.classList.remove('mode-compose', 'landing-compose');
            main.classList.remove('mode-inbox', 'mode-sent', 'mode-drafts', 'mode-trash');
            main.classList.add('mode-' + folder);
            document.querySelectorAll('.folder-item').forEach(function(f) { f.classList.remove('active'); });
            this.classList.add('active');
            if (folder === 'sent') {
                var sentBtn = document.querySelector('.btn-fetch[data-folder="sent"]');
                if (sentBtn && !sentBtn.disabled) sentBtn.click();
            }
        });
    });

    // HTML-escape helper for building list items
    function esc(str) {
        if (!str) return '';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // Sent reading pane
    function openSentReadingPane(e) {
        var empty   = document.getElementById('sentReadingEmpty');
        var content = document.getElementById('sentReadingContent');
        var scroll  = document.getElementById('sentReadingScroll');
        if (!content) return;
        document.getElementById('sentReadSubject').textContent = e.subject || '(No subject)';
        document.getElementById('sentReadFrom').textContent    = e.from   || '—';
        document.getElementById('sentReadTo').textContent      = e.to     || '—';
        document.getElementById('sentReadCc').textContent      = e.cc     || '—';
        document.getElementById('sentReadDate').textContent    = e.date   || '—';
        var bodyEl  = document.getElementById('sentReadBody');
        var frameEl = document.getElementById('sentReadFrame');
        var raw = e.body || '';
        var isHtml = /<[a-z][\s\S]*>/i.test(raw);
        if (isHtml && frameEl) {
            bodyEl.style.display  = 'none';
            frameEl.style.display = 'block';
            frameEl.srcdoc = raw;
            frameEl.onload = function() {
                try { frameEl.style.height = (frameEl.contentWindow.document.body.scrollHeight + 24) + 'px'; } catch(ex) {}
            };
        } else {
            if (frameEl) { frameEl.style.display = 'none'; frameEl.srcdoc = ''; }
            bodyEl.style.display = '';
            bodyEl.textContent = raw || '(No content)';
        }
        if (empty) empty.style.display = 'none';
        content.style.display = 'flex';
        // Reply / Forward hooks
        var btnReply = document.getElementById('sentBtnReply');
        var btnFwd   = document.getElementById('sentBtnForward');
        if (btnReply) btnReply.onclick = function() {
            main.classList.remove('mode-inbox','mode-sent','mode-drafts','mode-trash');
            main.classList.add('mode-compose','landing-compose');
            var toEl = document.getElementById('to_email');
            var subEl = document.getElementById('subject_email');
            if (toEl)  toEl.value  = e.from || '';
            if (subEl) subEl.value = 'Re: ' + (e.subject || '');
        };
        if (btnFwd) btnFwd.onclick = function() {
            main.classList.remove('mode-inbox','mode-sent','mode-drafts','mode-trash');
            main.classList.add('mode-compose','landing-compose');
            var subEl = document.getElementById('subject_email');
            var bodyEdEl = document.getElementById('outlook-body');
            if (subEl)    subEl.value = 'Fwd: ' + (e.subject || '');
            if (bodyEdEl) bodyEdEl.innerHTML = '<br><br>---------- Forwarded message ----------<br>From: ' + esc(e.from) + '<br>To: ' + esc(e.to) + '<br>Date: ' + esc(e.date) + '<br>Subject: ' + esc(e.subject) + '<br><br>' + (isHtml ? raw : esc(raw).replace(/\n/g,'<br>'));
        };
    }
    function closeSentReadingPane() {
        var empty   = document.getElementById('sentReadingEmpty');
        var content = document.getElementById('sentReadingContent');
        if (empty)   empty.style.display   = 'flex';
        if (content) content.style.display = 'none';
    }
    closeSentReadingPane();

    function updateAttachmentList() {
        attachmentList.innerHTML = '';
        if (attachmentInput.files && attachmentInput.files.length) {
            for (var i = 0; i < attachmentInput.files.length; i++) {
                var file = attachmentInput.files[i];
                var item = document.createElement('span');
                item.className = 'attachment-item';
                item.innerHTML = '<i class="fas fa-paperclip"></i> ' + file.name + ' <span class="remove-attach" data-idx="' + i + '">&times;</span>';
                item.querySelector('.remove-attach').addEventListener('click', function() {
                    removeAttachment(parseInt(this.dataset.idx, 10));
                });
                attachmentList.appendChild(item);
            }
        }
    }
    function removeAttachment(idx) {
        var dt = new DataTransfer();
        for (var i = 0; i < attachmentInput.files.length; i++) {
            if (i !== idx) dt.items.add(attachmentInput.files[i]);
        }
        attachmentInput.files = dt.files;
        updateAttachmentList();
    }

    document.querySelectorAll('.btn-attach').forEach(function(btn) {
        btn.addEventListener('click', function() {
            attachmentInput.click();
        });
    });
    attachmentInput.addEventListener('change', updateAttachmentList);

    function submitForm() {
        var editor = document.getElementById('outlook-body');
        var hidden = document.getElementById('body-hidden');
        if (editor && hidden) hidden.value = editor.innerHTML;
        document.getElementById('composeFormSubmit').click();
    }

    document.getElementById('btnSend').addEventListener('click', submitForm);

    document.getElementById('btnSaveDraft').addEventListener('click', function() {
        var editor = document.getElementById('outlook-body');
        var fromEl = document.getElementById('from_email');
        var toEl = document.getElementById('to_email');
        var ccEl = document.getElementById('cc_email');
        var subjectEl = document.getElementById('subject_email');
        if (!fromEl || fromEl.value === '') {
            alert('Please select a From address.');
            return;
        }
        var body = editor ? editor.innerHTML : '';
        var token = document.querySelector('input[name="_token"]');
        var fd = new FormData();
        if (token) fd.append('_token', token.value);
        fd.append('from', fromEl.value);
        fd.append('to', toEl.value || '');
        fd.append('cc', ccEl ? ccEl.value : '');
        fd.append('subject', subjectEl ? subjectEl.value : '');
        fd.append('body', body);
        var msgEl = document.getElementById('draftSavedMessage');
        fetch(draftUrl, {
            method: 'POST',
            body: fd,
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (msgEl) {
                    msgEl.style.display = 'flex';
                    setTimeout(function() { msgEl.style.display = 'none'; }, 4000);
                }
            })
            .catch(function() {
                alert('Could not save draft. Please try again.');
            });
    });

    // Date range presets
    function getDateRangeParams(folder) {
        var view = document.getElementById('folder' + folder.charAt(0).toUpperCase() + folder.slice(1)) || document.querySelector('.view-' + folder);
        if (!view) return { date_from: '', date_to: '' };
        var rangeSel = view.querySelector('.filter-date-range');
        var customSpan = view.querySelector('.filter-custom-dates, .filter-custom-inbox, .filter-custom-sent');
        var fromInput = view.querySelector('.filter-date-from[data-folder="' + folder + '"]');
        var toInput = view.querySelector('.filter-date-to[data-folder="' + folder + '"]');
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
        }
        return { date_from: dateFrom, date_to: dateTo };
    }

    document.querySelectorAll('.filter-date-range').forEach(function(sel) {
        sel.addEventListener('change', function() {
            var isCustom = this.value === 'custom';
            var customSpan = this.closest('.inbox-toolbar').querySelector('.filter-custom-dates, .filter-custom-inbox, .filter-custom-sent');
            if (customSpan) customSpan.style.display = isCustom ? 'inline' : 'none';
        });
    });

    document.querySelectorAll('.btn-fetch').forEach(function(btnEl) {
        btnEl.addEventListener('click', function() {
        var btn = this;
        var folder = btn.dataset.folder;
        var view = btn.closest('.folder-view');
        var searchInput = view.querySelector('.folder-search');
        var search = (searchInput && searchInput.value) ? encodeURIComponent(searchInput.value.trim()) : '';
        var dateParams = getDateRangeParams(folder);
        var sortSel = view.querySelector('.filter-sort');
        var sort = (sortSel && sortSel.value) ? sortSel.value : 'newest';
        var filterFromSel = view.querySelector('.filter-from');
        var filterToSel = view.querySelector('.filter-to');
        var filterFrom = (filterFromSel && filterFromSel.value) ? encodeURIComponent(filterFromSel.value) : '';
        var filterTo = (filterToSel && filterToSel.value) ? encodeURIComponent(filterToSel.value) : '';
        var hasAttachCb = view.querySelector('.filter-has-attachments');
        var hasAttachments = hasAttachCb && hasAttachCb.checked;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        var token = document.querySelector('meta[name="csrf-token"]');
        var params = ['folder=' + folder];
        if (search) params.push('search=' + search);
        if (dateParams.date_from) params.push('date_from=' + encodeURIComponent(dateParams.date_from));
        if (dateParams.date_to) params.push('date_to=' + encodeURIComponent(dateParams.date_to));
        params.push('sort=' + sort);
        if (filterFrom) params.push('filter_from=' + filterFrom);
        if (filterTo) params.push('filter_to=' + filterTo);
        if (hasAttachments) params.push('has_attachments=1');
        var url = '{{ route("admin.outlook.inbox") }}?' + params.join('&');
        fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token ? token.getAttribute('content') : '' }
        })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var list = view.querySelector('.folder-list');
                var empty = view.querySelector('.folder-empty');
                if (!list) return;
                list.innerHTML = '';
                if (folder === 'sent' && data.filter_options) {
                    var fromSel = view.querySelector('.filter-from');
                    var toSel = view.querySelector('.filter-to');
                    var curFrom = fromSel ? fromSel.value : '';
                    var curTo = toSel ? toSel.value : '';
                    if (fromSel) {
                        fromSel.innerHTML = '<option value="">All senders</option>';
                        (data.filter_options.from_list || []).forEach(function(e) {
                            var opt = document.createElement('option');
                            opt.value = e;
                            opt.textContent = e;
                            if (e === curFrom) opt.selected = true;
                            fromSel.appendChild(opt);
                        });
                    }
                    if (toSel) {
                        toSel.innerHTML = '<option value="">All recipients</option>';
                        (data.filter_options.to_list || []).forEach(function(e) {
                            var opt = document.createElement('option');
                            opt.value = e;
                            opt.textContent = e;
                            if (e === curTo) opt.selected = true;
                            toSel.appendChild(opt);
                        });
                    }
                }
                var hasEmails = data.emails && data.emails.length > 0;
                if (folder === 'sent') {
                    // Sent: render inbox-style list into #sentEmailList + reading pane
                    var sentList = document.getElementById('sentEmailList');
                    var sentEmptyEl = document.getElementById('sentEmpty');
                    if (!sentList) { list = null; } else { list = sentList; list.innerHTML = ''; }
                    if (hasEmails) {
                        if (sentEmptyEl) sentEmptyEl.style.display = 'none';
                        if (empty) empty.style.display = 'none';
                        data.emails.forEach(function(e, idx) {
                            var initials = (e.from || 'S').split('@')[0].charAt(0).toUpperCase();
                            var li = document.createElement('li');
                            li.className = 'sent-msg-item';
                            li.setAttribute('role', 'option');
                            li.dataset.idx = idx;
                            li.innerHTML =
                                '<div class="sent-icon" aria-hidden="true">' + initials + '</div>' +
                                '<div class="sent-msg-main">' +
                                    '<div class="sent-msg-line1">' +
                                        '<span class="sent-msg-to" title="To: ' + esc(e.to) + '">' + esc(e.to || '(no recipient)') + '</span>' +
                                        '<span class="sent-msg-date">' + esc(e.date_short || e.date) + '</span>' +
                                    '</div>' +
                                    '<div class="sent-msg-from">From: ' + esc(e.from || '—') + '</div>' +
                                    '<div class="sent-msg-subj">' + esc(e.subject || '(No subject)') + '</div>' +
                                '</div>';
                            li.addEventListener('click', function() {
                                document.querySelectorAll('#sentEmailList .sent-msg-item').forEach(function(i) { i.classList.remove('is-selected'); });
                                li.classList.add('is-selected');
                                openSentReadingPane(e);
                            });
                            if (sentList) sentList.appendChild(li);
                        });
                    } else {
                        if (sentEmptyEl) { sentEmptyEl.style.display = 'flex'; sentEmptyEl.querySelector('span').textContent = data.message || 'No sent messages found.'; }
                        closeSentReadingPane();
                    }
                } else if (hasEmails) {
                    empty.style.display = 'none';
                    data.emails.forEach(function(e) {
                        var row = document.createElement('li');
                        row.className = 'email-row';
                        row.innerHTML = '<span class="email-sender">' + (e.from || 'Unknown') + '</span><span class="email-subject">' + (e.subject || '(No subject)') + '</span><span class="email-date">' + (e.date || '') + '</span>';
                        list.appendChild(row);
                    });
                } else {
                    empty.style.display = 'flex';
                    empty.querySelector('span').textContent = data.message || 'No emails found. Connect SendGrid API to receive emails.';
                }
            })
            .catch(function() {
                view.querySelector('.folder-empty span').textContent = 'Could not fetch emails.';
            })
            .finally(function() {
                btn.disabled = false;
                btn.innerHTML = btn.dataset.folder === 'drafts' ? '<i class="fas fa-sync-alt"></i> Get Drafts' : '<i class="fas fa-sync-alt"></i> Get Emails';
            });
        });
    });

    if (refreshSentAfterSend) {
        main.classList.remove('mode-compose', 'landing-compose', 'mode-inbox', 'mode-drafts', 'mode-trash');
        main.classList.add('mode-sent');
        document.querySelectorAll('.folder-item').forEach(function(f) { f.classList.remove('active'); });
        var sentTab = document.querySelector('.folder-item[data-view="sent"]');
        if (sentTab) sentTab.classList.add('active');
        var sentBtn = document.querySelector('.btn-fetch[data-folder="sent"]');
        if (sentBtn) sentBtn.click();
    }

    document.querySelectorAll('.format-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var cmd = this.dataset.cmd;
            var editor = document.getElementById('outlook-body');
            if (cmd && editor) {
                editor.focus();
                document.execCommand(cmd, false, null);
            }
        });
    });
    document.querySelectorAll('.ribbon-undo').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var editor = document.getElementById('outlook-body');
            if (editor) { editor.focus(); document.execCommand('undo', false, null); }
        });
    });
    document.querySelectorAll('.ribbon-redo').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var editor = document.getElementById('outlook-body');
            if (editor) { editor.focus(); document.execCommand('redo', false, null); }
        });
    });

    document.getElementById('ribbonFont').addEventListener('change', function() {
        var editor = document.getElementById('outlook-body');
        editor.focus();
        document.execCommand('fontName', false, this.value);
    });
    document.getElementById('ribbonSize').addEventListener('change', function() {
        var editor = document.getElementById('outlook-body');
        editor.focus();
        var pt = this.value;
        var sel = window.getSelection();
        var txt = sel.toString();
        if (txt) {
            document.execCommand('insertHTML', false, '<span style="font-size:' + pt + 'pt">' + txt + '</span>');
        } else {
            document.execCommand('insertHTML', false, '<span style="font-size:' + pt + 'pt">&nbsp;</span>');
        }
    });
})();
</script>
@endpush
@endsection
