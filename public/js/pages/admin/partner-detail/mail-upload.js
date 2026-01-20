/**
 * Admin Partner Detail - Mail Upload Module
 * 
 * Handles inbox and sent email upload modal functionality
 * 
 * Dependencies:
 *   - jQuery
 *   - Bootstrap (for modals)
 *   - config.js (App object)
 */

'use strict';

// ============================================================================
// ASYNC WRAPPER - Wait for vendor libraries before initialization
// ============================================================================

(async function() {
    // Wait for vendor libraries to be ready
    if (typeof window.vendorLibsReady !== 'undefined') {
        console.log('[mail-upload.js] Waiting for vendorLibsReady promise...');
        await window.vendorLibsReady;
        console.log('[mail-upload.js] Vendor libraries ready!');
    } else {
        // Fallback: Poll for vendor libraries
        console.log('[mail-upload.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined') {
                    console.log('[mail-upload.js] All vendor libraries detected!');
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

// ============================================================================
// MAIL UPLOAD HANDLERS
// ============================================================================

jQuery(document).ready(function($){
    
    // Upload inbox email handler
    $(document).delegate('.partnerUploadAndFetchMail','click', function(){
        $('#mapartner_id_fetch').val(PageConfig.partnerId);
        $('#partnerUploadAndFetchMail').modal('show');
    });

    // Upload sent email handler
    $(document).delegate('.partnerUploadSentAndFetchMail','click', function(){
        $('#mapartner_id_fetch_sent').val(PageConfig.partnerId);
        $('#partnerUploadSentAndFetchMail').modal('show');
    });
    
});

})(); // End async wrapper
