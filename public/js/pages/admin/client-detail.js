/**
 * Admin Client Detail Page - Page-Specific JavaScript
 * 
 * This file contains JavaScript code specific to the Admin Client Detail page.
 * Common/shared functionality should be in /js/common/ files.
 * 
 * Dependencies (loaded before this file):
 *   - config.js
 *   - ajax-helpers.js
 *   - crud-operations.js
 *   - activity-handlers.js
 *   - document-handlers.js
 *   - ui-components.js
 *   - utilities.js
 */

'use strict';

// ============================================================================
// ASYNC WRAPPER - Wait for vendor libraries before initialization
// ============================================================================

(async function() {
    // Wait for vendor libraries to be ready
    if (typeof window.vendorLibsReady !== 'undefined') {
        console.log('[client-detail.js] Waiting for vendorLibsReady promise...');
        await window.vendorLibsReady;
        console.log('[client-detail.js] Vendor libraries ready!');
    } else {
        // Fallback: Poll for vendor libraries
        console.log('[client-detail.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined' && 
                    typeof $.fn.select2 === 'function' &&
                    typeof flatpickr !== 'undefined') {
                    console.log('[client-detail.js] All vendor libraries detected!');
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

// ============================================================================
// INITIALIZATION
// ============================================================================

// Download + ChatGPT handlers moved to dedicated module file.

// ============================================================================
// MAIN JQUERY READY BLOCK
// ============================================================================

jQuery(document).ready(function($){
  
    // Tab visibility and layout handlers moved to dedicated module file.
  
    // UI initialization moved to ui-initialization module.

    // Google review + not picked call handlers moved to communications module.

    // Receipt flatpickr setup moved to ui-initialization module.

    // NOTE: .openproductrinfo handler has been moved to detail.blade.php inline script
    // to avoid duplication and ensure calculateReceiptTotal() is called properly

    // Receipt helpers moved to receipts-and-payments module.

    // ============================================================================
    // TAG HANDLERS (Client-only tags)
    // ============================================================================

    // UI initialization moved to ui-initialization module.

    // Not picked call handler moved to communications module.

    

    // Email/phone verification handler moved to session-handlers.js module

    // UI layout handlers moved to dedicated module file.

    // Modal handlers moved to modal-handlers module.

    // Assignment handlers moved to assignments module.

    // ============================================================================
    // OVERRIDE COMMON FUNCTIONS WITH PAGE-SPECIFIC IMPLEMENTATIONS
    // ============================================================================
    
    // Note: These functions override the common ones with more detailed implementations
    // If needed, these can be moved to common files later
    
    // Pin and publish handlers moved to pin-and-publish module.

    // Assignee handlers moved to assignee-handlers.js module

    // Document action handlers moved to document-actions module.

    // Delete handlers moved to delete-handlers module.

    // Pin and publish handlers moved to pin-and-publish module.

    // Note handlers moved to notes module.

    // Application workflow/partner/product handlers moved to application-handlers.js module

    // Email and template handlers moved to email-handlers.js module

    // Client status change handler moved to client-status.js module

    // Email template handlers moved to email-handlers.js module

    // Interested product handler moved to application-handlers.js module

    // Document upload handlers moved to document-upload.js module

    // Convert to application handler moved to application-handlers.js module
    
    // Application tab click handler moved to application-handlers.js module

    // Document rename handlers moved to document-rename.js module

    // DataTable initialization moved to datatable-handlers.js module

    // Application detail view handlers moved to application-handlers.js module
    
    // Application modal openers moved to application-handlers.js module

    // Application stage progression handlers moved to application-stage.js module
    
    // Application action button handlers moved to application-stage.js module
    
    // Agent assignment handlers moved to application-stage.js module
    
    // Product fee/commission status handlers moved to application-stage.js module

    // Commission calculation handlers moved to commission-handlers.js module

    // Drag and drop handlers for application checklist uploads moved to drag-drop-handlers.js module

    // ============================================================================
    // DOCUMENT RENAME HANDLERS (CONTINUED)
    // ============================================================================
    
    $(document).on('click', '.documnetlist .drow .btn-danger', function () {
        var parent = $(this).closest('.drow').find('.doc-row');
        var hourid = parent.data('id');
        if (hourid) {
            parent.html(parent.data('current-html'));
        } else {
            parent.remove();
        }
    });

    $(document).on('click', '.migdocumnetlist .drow .btn-danger', function () {
        var parent = $(this).closest('.drow').find('.doc-row');
        var hourid = parent.data('id');
        if (hourid) {
            parent.html(parent.data('current-html'));
        } else {
            parent.remove();
        }
    });

    $(document).on('click', '.documnetlist .drow .btn-primary', function () {
        var parent = $(this).closest('.drow').find('.doc-row');
        parent.find('.opentime').removeClass('is-invalid');
        parent.find('.invalid-feedback').remove();

        var opentime = parent.find('.opentime').val();

        if (!opentime) {
            parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
            parent.append($("<div class='invalid-feedback'>This field is required</div>"));
            return false;
        }

        var url = App.getUrl('renameDoc') || App.getUrl('siteUrl') + '/renamedoc';
        $.ajax({
            type: "POST",
            headers: { 'X-CSRF-TOKEN': App.getCsrf()},
            data: {"filename": opentime, "id": parent.data('id')},
            url: url,
            success: function(result){
                var obj = typeof result === 'string' ? JSON.parse(result) : result;
                if (obj.status) {
                    parent.empty()
                        .data('id', obj.Id)
                        .data('name', opentime)
                        .append(
                            $('<span>').html('<i class="fas fa-file-image"></i> '+obj.filename+'.'+obj.filetype)
                        );
                    $('#grid_'+obj.Id).html(obj.filename+'.'+obj.filetype);
                } else {
                    parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
                    parent.append($('<div class="invalid-feedback">' + obj.message + '</div>'));
                }
            }
        });
        return false;
    });

    $(document).on('click', '.migdocumnetlist .drow .btn-primary', function () {
        var parent = $(this).closest('.drow').find('.doc-row');
        parent.find('.opentime').removeClass('is-invalid');
        parent.find('.invalid-feedback').remove();

        var opentime = parent.find('.opentime').val();

        if (!opentime) {
            parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
            parent.append($("<div class='invalid-feedback'>This field is required</div>"));
            return false;
        }

        var url = App.getUrl('renameDoc') || App.getUrl('siteUrl') + '/renamedoc';
        $.ajax({
            type: "POST",
            headers: { 'X-CSRF-TOKEN': App.getCsrf()},
            data: {"filename": opentime, "id": parent.data('id')},
            url: url,
            success: function(result){
                var obj = typeof result === 'string' ? JSON.parse(result) : result;
                if (obj.status) {
                    parent.empty()
                        .data('id', obj.Id)
                        .data('name', opentime)
                        .append(
                            $('<span>').html('<i class="fas fa-file-image"></i> '+obj.filename+'.'+obj.filetype)
                        );
                    $('#grid_'+obj.Id).html(obj.filename+'.'+obj.filetype);
                } else {
                    parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
                    parent.append($('<div class="invalid-feedback">' + obj.message + '</div>'));
                }
            }
        });
        return false;
    });

    // All document rename handlers moved to document-rename.js module
    
    // Select2 initialization for application forms moved to application-handlers.js module
    
    // Checklist file selection handler moved to datatable-handlers.js module
    
    // Additional handlers and comments - most functionality moved to dedicated modules
    
    // Email form submission handler moved to email-handlers.js module

    console.log('Admin Client Detail page initialized');
});

})(); // End async wrapper

// Document context menu moved to dedicated module file.

// ============================================================================
// ADDITIONAL PAGE-SPECIFIC FUNCTIONS
// ============================================================================

/**
 * Re-initialize Bootstrap Collapse on accordion headers
 * Must be called after replacing accordion HTML to restore click functionality
 */
function reinitializeAccordions() {
    // Re-initialize Bootstrap collapse on all accordion headers
    var collapseElements = document.querySelectorAll('#accordion [data-bs-toggle="collapse"]');
    collapseElements.forEach(function(element) {
        // Check if already initialized to avoid duplicates
        var instance = bootstrap.Collapse.getInstance(element);
        if (!instance) {
            new bootstrap.Collapse(element, {
                toggle: false // Don't auto-toggle on init
            });
        }
    });
}

/**
 * Reload application activities accordion after state change
 * @param {number} appliid - Application ID
 */
function reloadApplicationActivities(appliid) {
    var url = App.getUrl('getApplicationsLogs') || App.getUrl('siteUrl') + '/get-applications-logs';
    $.ajax({
        url: url,
        type: 'GET',
        data: { id: appliid },
        success: function(response) {
            // Replace the accordion content
            $('#accordion').html(response);
            // Re-initialize Bootstrap Collapse for click functionality
            reinitializeAccordions();
            console.log('Activities reloaded successfully');
        },
        error: function(xhr, status, error) {
            console.error('Error reloading activities:', error);
        }
    });
}

/**
 * Update the circular progress bar
 * @param {number} width - Progress percentage (0-100)
 */
function updateProgressBar(width) {
    $('.progress-circle span').html(width + ' %');
    var over = width > 50 ? '50' : '';
    $('#progresscir').removeClass();
    $('#progresscir').addClass('progress-circle');
    $('#progresscir').addClass('prgs_' + width);
    $('#progresscir').addClass('over_' + over);
}

// NOTE: Additional functions will be extracted and added here

