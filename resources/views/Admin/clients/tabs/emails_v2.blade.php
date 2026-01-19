<!-- Emails V2 Interface - Generic for Clients and Partners -->

<!-- Email V2 Styles -->
<link rel="stylesheet" href="{{ asset('css/emails_v2.css') }}">

@php
    // Support both $client and $fetchedData variable names
    $entityData = $client ?? $partner ?? $fetchedData ?? null;
    
    // Determine entity type (client or partner)
    $entityType = 'client'; // default
    if (isset($partner)) {
        $entityType = 'partner';
    } elseif (isset($fetchedData)) {
        // Check if it's a Partner model by checking if it has partner_name attribute
        if (isset($fetchedData->partner_name) || (method_exists($fetchedData, 'getTable') && $fetchedData->getTable() === 'partners')) {
            $entityType = 'partner';
        } else {
            $entityType = 'client';
        }
    }
@endphp
<div class="email-v2-interface-container" 
     data-entity-id="{{ $entityData->id ?? '' }}" 
     data-entity-type="{{ $entityType }}">
    <!-- Top Control Bar (Search & Filters) -->
    <div class="email-v2-control-bar">
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
            <!-- Drag & Drop Upload Section -->
            <div class="upload-section-header">
                <span class="upload-title">Upload Emails</span>
            </div>
            <div class="upload-section-container">
                <div id="upload-area-v2" class="drag-drop-zone">
                    <div class="drag-drop-content">
                        <i class="fas fa-cloud-upload-alt drag-drop-icon"></i>
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
                        <i class="fas fa-inbox"></i>
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
                    <i class="fas fa-envelope-open"></i>
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
<div id="attachmentPreviewModalV2" class="preview-modal" style="display: none;">
    <div class="preview-modal-overlay" id="previewOverlayV2"></div>
    <div class="preview-modal-content">
        <div class="preview-modal-header">
            <h3 id="previewFileNameV2">Preview</h3>
            <button class="preview-close" id="closePreviewBtnV2">&times;</button>
        </div>
        <div class="preview-modal-body">
            <iframe id="previewFrameV2" src=""></iframe>
        </div>
    </div>
</div>

<!-- Email Context Menu -->
<div id="emailContextMenuV2" class="email-context-menu" style="display: none;">
    <div class="context-menu-item" data-action="apply-label">
        <i class="fas fa-tag"></i>
        <span>Apply Label</span>
        <i class="fas fa-chevron-right context-menu-arrow"></i>
    </div>
    <div class="context-menu-item" data-action="reply">
        <i class="fas fa-reply"></i>
        <span>Reply</span>
    </div>
    <div class="context-menu-item" data-action="forward">
        <i class="fas fa-share"></i>
        <span>Forward</span>
    </div>
    <div class="context-menu-separator"></div>
    <div class="context-menu-item" data-action="delete" style="display: none;">
        <i class="fas fa-trash"></i>
        <span>Delete</span>
    </div>
</div>

<!-- Label Submenu -->
<div id="labelSubmenuV2" class="email-context-submenu" style="display: none;">
    <div class="submenu-header">
        <i class="fas fa-arrow-left submenu-back"></i>
        <span>Select Label</span>
    </div>
    <div class="submenu-content" id="labelSubmenuContentV2">
        <!-- Labels will be populated dynamically -->
    </div>
</div>

<!-- Context Menu Overlay (for closing menu on outside click) -->
<div id="contextMenuOverlayV2" class="context-menu-overlay" style="display: none;"></div>

<!-- Include necessary CSS and JavaScript -->
<link rel="stylesheet" href="{{ asset('css/emails_v2.css') }}">
<script src="{{ asset('js/emails_v2.js') }}"></script>

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
