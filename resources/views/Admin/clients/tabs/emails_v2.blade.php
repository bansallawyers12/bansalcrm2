<!-- Emails V2 Interface - Generic for Clients and Partners -->

@php
    // Cache-busting: bump when emails_v2.js changes; emails_v2.css uses mtime so it updates automatically
    $emailsV2AssetVer = 13;
    $emailsV2CssVer = @filemtime(public_path('css/emails_v2.css')) ?: $emailsV2AssetVer;
@endphp
<!-- Email V2 Styles -->
<link rel="stylesheet" href="{{ asset('css/emails_v2.css') }}?v={{ $emailsV2CssVer }}">

@php
    // Support both $client and $fetchedData variable names
    $entityData = $client ?? $partner ?? $fetchedData ?? null;
    
    // Determine entity type (client, lead, or partner)
    $entityType = 'client'; // default
    if (isset($partner)) {
        $entityType = 'partner';
    } elseif (isset($fetchedData)) {
        // Check if it's a Partner model
        if (isset($fetchedData->partner_name) || (method_exists($fetchedData, 'getTable') && $fetchedData->getTable() === 'partners')) {
            $entityType = 'partner';
        } else {
            // Admin: use actual type (client or lead) so sent emails match
            $entityType = $fetchedData->type ?? 'client';
        }
    }
@endphp
<div class="email-v2-interface-container" 
     data-entity-id="{{ $entityData->id ?? '' }}" 
     data-entity-type="{{ $entityType }}"
     data-show-email-category="{{ $entityType !== 'partner' ? '1' : '0' }}"
     data-user-role="{{ Auth::user()->role ?? '' }}">
    @if($entityType !== 'partner')
    <!-- Client detail only: Client | College sub-tabs -->
    <div class="email-v2-category-tabs" role="tablist" aria-label="Email category">
        <button type="button" class="category-tab-btn active" data-category="client" id="category-tab-client" aria-selected="true">
            @icon('user') Client
        </button>
        <button type="button" class="category-tab-btn" data-category="college" id="category-tab-college" aria-selected="false">
            @icon('university') College
        </button>
    </div>
    @endif
    <!-- Top Control Bar: Inbox | Sent tabs + Search + Labels -->
    <div class="email-v2-control-bar">
        <div class="control-section mail-type-section">
            <div class="email-v2-folder-tabs" role="tablist">
                <button type="button" class="folder-tab-btn active" data-folder="inbox" id="folder-tab-inbox" aria-selected="true">
                    @icon('inbox') Inbox
                </button>
                <button type="button" class="folder-tab-btn" data-folder="sent" id="folder-tab-sent" aria-selected="false">
                    @icon('paper-plane') Sent
                </button>
            </div>
            <!-- Hidden select kept for JS compatibility -->
            <select id="mailTypeFilterV2" class="filter-select" style="display:none;" aria-hidden="true">
                <option value="inbox" selected>Inbox</option>
                <option value="sent">Sent</option>
            </select>
        </div>
        <div class="control-section search-section">
            <label for="emailV2SearchInput">Search:</label>
            <input type="text" id="emailV2SearchInput" class="search-input" placeholder="Search emails...">
        </div>
        
        <div class="control-section filter-section">
            <label for="labelV2Filter">Label:</label>
            <select id="labelV2Filter" class="filter-select">
                <option value="">All Labels</option>
                <!-- Populated dynamically -->
            </select>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="email-v2-main-content">
        <!-- Left Email List Pane with Upload Area -->
        <div class="email-v2-list-pane">
            <!-- Drag & Drop Upload Section (Collapsible) -->
            <div class="upload-section-header js-upload-toggle" role="button" tabindex="0" aria-expanded="true" aria-controls="upload-section-body" aria-label="Toggle upload section">
                <span class="upload-title">Upload Emails</span>
                @icon('chevron-down', 'solid', ['class' => 'upload-toggle-icon'])
            </div>
            <div id="upload-section-body" class="upload-section-container">
                <div id="upload-area-v2" class="drag-drop-zone">
                    <div class="drag-drop-content">
                        @icon('cloud-upload-alt', 'solid', ['class' => 'drag-drop-icon'])
                        <div class="drag-drop-text">Drag & drop .msg files here</div>
                        <div class="drag-drop-subtext">or click to browse</div>
                        <div id="file-count-v2" class="file-count-badge">0</div>
                    </div>
                    <input type="file" id="emailV2FileInput" class="file-input" accept=".msg" multiple style="display: none;">
                </div>
                <div id="upload-progress-v2" class="upload-progress">
                    <span id="fileStatusV2">Ready to upload</span>
                </div>
            </div>
            
            <!-- Email List Header -->
            <div class="email-v2-list-header">
                <span class="results-count" id="resultsCountV2">0 results</span>
                <div class="pagination-controls">
                    <button class="pagination-btn" id="prevBtnV2">Prev</button>
                    <span class="page-info" id="pageInfoV2">1/1</span>
                    <button class="pagination-btn" id="nextBtnV2">Next</button>
                </div>
            </div>
            
            <div class="email-v2-list" id="emailListV2">
                <!-- Email items will be populated here by JavaScript -->
                <div class="empty-state">
                    <div class="empty-state-icon">
                        @icon('inbox')
                    </div>
                    <div class="empty-state-text">
                        <h3>No emails found</h3>
                        <p>Upload .msg files above to get started.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Content Viewing Pane -->
        <div class="email-v2-content-pane">
            <div class="email-v2-content-placeholder" id="emailContentPlaceholderV2">
                <div class="placeholder-content">
                    @icon('envelope-open')
                    <h3>Select an email to view its contents</h3>
                </div>
            </div>
            
            <div class="email-v2-content-view" id="emailContentViewV2" style="display: none;">
                <!-- Email content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Attachment Preview Modal -->
<div id="attachmentPreviewModalV2" class="preview-modal" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="previewFileNameV2">
    <div class="preview-modal-overlay" id="previewOverlayV2"></div>
    <div class="preview-modal-content">
        <div class="preview-modal-header">
            <h3 id="previewFileNameV2">Preview</h3>
            <button class="preview-close" id="closePreviewBtnV2" aria-label="Close preview">&times;</button>
        </div>
        <div class="preview-modal-body">
            <div class="preview-loading" id="previewLoadingV2">
                @icon('spinner', 'solid', ['spin' => true])
                <span>Loading preview&hellip;</span>
            </div>
            <iframe id="previewFrameV2" src="" style="display:none;"></iframe>
        </div>
    </div>
</div>

<!-- Email Context Menu -->
<div id="emailContextMenuV2" class="email-context-menu" style="display: none;">
    <div class="context-menu-item" data-action="apply-label">
        @icon('tag')
        <span>Apply Label</span>
        @icon('chevron-right', 'solid', ['class' => 'context-menu-arrow'])
    </div>
    <div class="context-menu-item" data-action="reply">
        @icon('reply')
        <span>Reply</span>
    </div>
    <div class="context-menu-item" data-action="forward">
        @icon('share')
        <span>Forward</span>
    </div>
    <div class="context-menu-separator"></div>
    <div class="context-menu-item" data-action="delete" style="display: none;">
        @icon('trash')
        <span>Delete</span>
    </div>
</div>

<!-- Label Submenu -->
<div id="labelSubmenuV2" class="email-context-submenu" style="display: none;">
    <div class="submenu-header">
        @icon('arrow-left', 'solid', ['class' => 'submenu-back'])
        <span>Select Label</span>
    </div>
    <div class="submenu-content" id="labelSubmenuContentV2">
        <!-- Labels will be populated dynamically -->
    </div>
</div>

<!-- Context Menu Overlay (for closing menu on outside click) -->
<div id="contextMenuOverlayV2" class="context-menu-overlay" style="display: none;"></div>

<!-- Include necessary JavaScript -->
@once
@push('scripts')
@vite(['resources/js/pages/admin/emails-v2-entry.js'])
@endpush
@endonce

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Emails V2 interface loaded');
    
    // Debug: Check if elements exist
    const fileInput = document.getElementById('emailV2FileInput');
    const uploadArea = document.getElementById('upload-area-v2');
    const fileStatus = document.getElementById('fileStatusV2');
    
    console.log('File input found:', !!fileInput);
    console.log('Upload area found:', !!uploadArea);
    console.log('File status found:', !!fileStatus);
    
    // Debug: Check if modules are available (V2 module auto-initializes)
    console.log('loadEmailsV2 available:', typeof window.loadEmailsV2);
    
    // Load emails on page load
    if (typeof window.loadEmailsV2 === 'function') {
        console.log('Loading initial emails V2...');
        setTimeout(function() {
            window.loadEmailsV2();
        }, 100);
    } else {
        console.log('Auto-loading emails from main module');
    }
});
</script>
