/**
 * Admin Client Detail - Assignee Handlers Module
 * 
 * Handles assignee show/hide and save functionality
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
        console.log('[assignee-handlers.js] Waiting for vendorLibsReady promise...');
        await window.vendorLibsReady;
        console.log('[assignee-handlers.js] Vendor libraries ready!');
    } else {
        // Fallback: Poll for vendor libraries
        console.log('[assignee-handlers.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined') {
                    console.log('[assignee-handlers.js] All vendor libraries detected!');
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

// ============================================================================
// ASSIGNEE HANDLERS
// ============================================================================

jQuery(document).ready(function($){
    
    $(document).on('click', '.openassigneeshow', function(){
        $('.assigneeshow').show();
    });

    $(document).on('click', '.closeassigneeshow', function(){
        $('.assigneeshow').hide();
    });

    $(document).on('click', '.saveassignee', function(){
        var appliid = $(this).attr('data-id');
        $('.popuploader').show();
        var url = App.getUrl('clientChangeAssignee') || App.getUrl('siteUrl') + '/clients/change_assignee';
        $.ajax({
            url: url,
            type:'GET',
            data:{id: appliid, assignee: $('#changeassignee').val()},
            success: function(response){
                var obj = typeof response === 'string' ? $.parseJSON(response) : response;
                if(obj.status){
                    alert(obj.message);
                    location.reload();
                }else{
                    alert(obj.message);
                }
            }
        });
    });

    console.log('[assignee-handlers.js] Assignee handlers initialized');
});

})(); // End async wrapper
