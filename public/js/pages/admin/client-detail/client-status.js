/**
 * Admin Client Detail - Client Status Module
 * 
 * Handles client status (rating) change functionality
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
        console.log('[client-status.js] Waiting for vendorLibsReady promise...');
        await window.vendorLibsReady;
        console.log('[client-status.js] Vendor libraries ready!');
    } else {
        // Fallback: Poll for vendor libraries
        console.log('[client-status.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined') {
                    console.log('[client-status.js] All vendor libraries detected!');
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

// ============================================================================
// CLIENT STATUS HANDLER
// ============================================================================

jQuery(document).ready(function($){
    
    $(document).on('click', '.change_client_status', function(e){
        var v = $(this).attr('rating');
        $('.change_client_status').removeClass('active');
        $(this).addClass('active');

        var url = App.getUrl('changeClientStatus') || App.getUrl('siteUrl') + '/change-client-status';
        $.ajax({
            url: url,
            type:'GET',
            datatype:'json',
            data:{id: App.getPageConfig('clientId'), rating:v},
            success: function(response){
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if(res.status){
                    $('.custom-error-msg').html('<span class="alert alert-success">'+res.message+'</span>');
                    if(typeof getallactivities === 'function') {
                        getallactivities();
                    }
                }else{
                    $('.custom-error-msg').html('<span class="alert alert-danger">'+res.message+'</span>');
                }
            }
        });
    });

    console.log('[client-status.js] Client status handler initialized');
});

})(); // End async wrapper
