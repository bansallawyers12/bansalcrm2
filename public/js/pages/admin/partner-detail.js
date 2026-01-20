/**
 * Admin Partner Detail Page - Main Orchestrator
 * 
 * This file contains partner detail page-specific JavaScript.
 * Most functionality has been moved to dedicated modules.
 * 
 * Dependencies (loaded before this file):
 *   - config.js
 *   - ajax-helpers.js (if available)
 *   - All partner-detail/* modules
 */

'use strict';

// ============================================================================
// ASYNC WRAPPER - Wait for vendor libraries before initialization
// ============================================================================

(async function() {
    // Wait for vendor libraries to be ready
    if (typeof window.vendorLibsReady !== 'undefined') {
        console.log('[partner-detail.js] Waiting for vendorLibsReady promise...');
        await window.vendorLibsReady;
        console.log('[partner-detail.js] Vendor libraries ready!');
    } else {
        // Fallback: Poll for vendor libraries
        console.log('[partner-detail.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined' && 
                    typeof $.fn.select2 === 'function' &&
                    typeof flatpickr !== 'undefined') {
                    console.log('[partner-detail.js] All vendor libraries detected!');
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

// ============================================================================
// MAIN JQUERY READY BLOCK
// ============================================================================

jQuery(document).ready(function($){
    
    // Status handlers moved to status-handlers.js module
    
    // Notes handlers moved to notes-handlers.js module
    
    // Mail upload handlers moved to mail-upload.js module
    
    // Application tab handlers moved to application-tab.js module
    
    // Invoice/payment handlers - see blade-inline.js (contains Blade URLs)
    
    // Bulk upload handlers - see blade-inline.js (contains Blade variables)
    
    console.log('Admin Partner Detail page initialized');
});

})(); // End async wrapper
