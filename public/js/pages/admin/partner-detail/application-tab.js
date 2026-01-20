/**
 * Admin Partner Detail - Application Tab Module
 * 
 * Handles application tab activation and localStorage persistence
 * 
 * Dependencies:
 *   - jQuery
 *   - config.js (App object)
 */

'use strict';

// ============================================================================
// ASYNC WRAPPER - Wait for vendor libraries before initialization
// ============================================================================

(async function() {
    // Wait for vendor libraries to be ready
    if (typeof window.vendorLibsReady !== 'undefined') {
        console.log('[application-tab.js] Waiting for vendorLibsReady promise...');
        await window.vendorLibsReady;
        console.log('[application-tab.js] Vendor libraries ready!');
    } else {
        // Fallback: Poll for vendor libraries
        console.log('[application-tab.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined') {
                    console.log('[application-tab.js] All vendor libraries detected!');
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

// ============================================================================
// APPLICATION TAB HANDLERS
// ============================================================================

jQuery(document).ready(function($){
    
    // Activate application tab handler
    $(document).on('click', '.activate-app-tab', function () {
        const tab = $(this).data('tab'); // Get the tab from the custom attribute
        const appliid = $(this).data('id'); // Get the application ID
        
        localStorage.setItem('activeTab', tab);
        localStorage.setItem('appliid', appliid);
    });
    
});

})(); // End async wrapper
