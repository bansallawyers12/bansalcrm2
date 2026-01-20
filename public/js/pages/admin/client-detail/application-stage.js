/**
 * Admin Client Detail - Application Stage Module
 * 
 * Handles application stage progression, discontinuation, refunds, and agent assignments
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
        console.log('[application-stage.js] Waiting for vendorLibsReady promise...');
        await window.vendorLibsReady;
        console.log('[application-stage.js] Vendor libraries ready!');
    } else {
        // Fallback: Poll for vendor libraries
        console.log('[application-stage.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined') {
                    console.log('[application-stage.js] All vendor libraries detected!');
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

// ============================================================================
// APPLICATION STAGE HANDLERS
// ============================================================================

jQuery(document).ready(function($){
    
    // ============================================================================
    // PROCEED TO NEXT STAGE HANDLER
    // ============================================================================
    
    $(document).on('click', '.nextstage', function(){
        var appliid = $(this).attr('data-id');
        var stage = $(this).attr('data-stage');
        var clientId = PageConfig.clientId;
        
        if (!appliid) {
            console.error('Application ID is missing');
            return;
        }
        
        if (!clientId) {
            console.error('Client ID is missing');
            return;
        }
        
        $('.popuploader').show();
        
        var url = App.getUrl('siteUrl') + '/updatestage';
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            data: {
                id: appliid,
                client_id: clientId
            },
            success: function(response){
                $('.popuploader').hide();
                
                // Handle both string and object responses
                var obj = typeof response === 'string' ? $.parseJSON(response) : response;
                
                if(obj.status){
                    // Show success message
                    if($('.custom-error-msg').length){
                        $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
                    } else {
                        // Create message container if it doesn't exist
                        $('.ifapplicationdetailnot').prepend('<div class="custom-error-msg"><span class="alert alert-success">'+obj.message+'</span></div>');
                    }
                    
                    // Update current stage text
                    $('.curerentstage').text(obj.stage);
                    
                    // Update progress bar if it exists
                    if(obj.width !== undefined){
                        var progressWidth = obj.width;
                        var over = progressWidth > 50 ? '50' : '';
                        var $progressCir = $('#progresscir');
                        if($progressCir.length){
                            // Remove old progress classes
                            $progressCir.removeClass(function(index, className) {
                                return (className.match(/(^|\s)prgs_\S+/g) || []).join(' ');
                            });
                            $progressCir.removeClass(function(index, className) {
                                return (className.match(/(^|\s)over_\S+/g) || []).join(' ');
                            });
                            // Add new progress classes
                            if(over){
                                $progressCir.addClass('over_' + over);
                            }
                            $progressCir.addClass('prgs_' + progressWidth);
                            // Update progress text
                            $progressCir.find('span').text(progressWidth + ' %');
                        }
                    }
                    
                    // Reload application activities log
                    var logsUrl = App.getUrl('getApplicationsLogs') || App.getUrl('siteUrl') + '/get-applications-logs';
                    $.ajax({
                        url: logsUrl,
                        type: 'GET',
                        data: {
                            clientid: clientId,
                            id: appliid
                        },
                        success: function(responses){
                            $('#accordion').html(responses);
                            // Re-initialize Bootstrap Collapse for click functionality
                            if (typeof reinitializeAccordions === 'function') {
                                reinitializeAccordions();
                            }
                        },
                        error: function(xhr, status, error){
                            console.error('Error loading application logs:', error);
                        }
                    });
                    
                    // Update button visibility - hide "Proceed to Next Stage" if at last stage
                    if(obj.displaycomplete){
                        $('.nextstage').hide();
                        $('.completestage').show();
                    }
                } else {
                    // Show error message
                    if($('.custom-error-msg').length){
                        $('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                    } else {
                        $('.ifapplicationdetailnot').prepend('<div class="custom-error-msg"><span class="alert alert-danger">'+obj.message+'</span></div>');
                    }
                }
            },
            error: function(xhr, status, error){
                $('.popuploader').hide();
                console.error('Error updating stage:', error);
                var errorMsg = 'An error occurred while updating the stage. Please try again.';
                if($('.custom-error-msg').length){
                    $('.custom-error-msg').html('<span class="alert alert-danger">'+errorMsg+'</span>');
                } else {
                    $('.ifapplicationdetailnot').prepend('<div class="custom-error-msg"><span class="alert alert-danger">'+errorMsg+'</span></div>');
                }
            }
        });
    });

    // ============================================================================
    // APPLICATION ACTION BUTTON HANDLERS
    // ============================================================================

    // Handler for "Discontinue Application" button
    $(document).on('click', '.discon_application', function(){
        var appliid = $(this).attr('data-id');
        $('#discon_application').modal('show');
        $('input[name="diapp_id"]').val(appliid);
    });

    // Handler for "Refund Application" button  
    $(document).on('click', '.refund_application', function(){
        var appliid = $(this).attr('data-id');
        $('#refund_application').modal('show');
        $('input[name="reapp_id"]').val(appliid);
    });

    // Handler for "Back to Previous Stage" button
    $(document).on('click', '.backstage', function(){
        var appliid = $(this).attr('data-id');
        var stage = $(this).attr('data-stage');
        var clientId = PageConfig.clientId;
        
        if (!appliid || !clientId) {
            console.error('Application ID or Client ID is missing');
            return;
        }
        
        $('.popuploader').show();
        
        var url = App.getUrl('siteUrl') + '/updatebackstage';
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            data: {
                id: appliid,
                client_id: clientId
            },
            success: function(response){
                $('.popuploader').hide();
                var obj = typeof response === 'string' ? $.parseJSON(response) : response;
                
                if(obj.status){
                    $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
                    $('.curerentstage').text(obj.stage);
                    
                    // Update progress bar
                    if(obj.width !== undefined){
                        updateProgressBar(obj.width);
                    }
                    
                    // Reload activities accordion
                    reloadApplicationActivities(appliid);
                } else {
                    $('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                }
            },
            error: function(xhr, status, error){
                $('.popuploader').hide();
                console.error('Error going back to previous stage:', error);
                $('.custom-error-msg').html('<span class="alert alert-danger">Error updating stage. Please try again.</span>');
            }
        });
    });

    // Handler for "Complete Application" button
    $(document).on('click', '.completestage', function(){
        var appliid = $(this).attr('data-id');
        $('#confirmcompleteModal').modal('show');
        $('.acceptapplication').attr('data-id', appliid);
    });

    // Handler for confirming application completion
    $(document).on('click', '.acceptapplication', function(){
        var appliid = $(this).attr('data-id');
        var clientId = PageConfig.clientId;
        
        if (!appliid || !clientId) {
            console.error('Application ID or Client ID is missing');
            return;
        }
        
        $('.popuploader').show();
        $('#confirmcompleteModal').modal('hide');
        
        var url = App.getUrl('siteUrl') + '/completestage';
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            data: {
                id: appliid,
                client_id: clientId
            },
            success: function(response){
                $('.popuploader').hide();
                var obj = typeof response === 'string' ? $.parseJSON(response) : response;
                
                if(obj.status){
                    $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
                    $('.applicationstatus').html('Completed');
                    $('.ifdiscont').hide();
                    $('.revertapp').show();
                    
                    // Update progress to 100%
                    updateProgressBar(100);
                    
                    // Reload activities accordion
                    reloadApplicationActivities(appliid);
                } else {
                    $('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                }
            },
            error: function(xhr, status, error){
                $('.popuploader').hide();
                console.error('Error completing application:', error);
                $('.custom-error-msg').html('<span class="alert alert-danger">Error completing application. Please try again.</span>');
            }
        });
    });

    // Handler for "Revert Application" button
    $(document).on('click', '.revertapp', function(){
        var appliid = $(this).attr('data-id');
        $('#revert_application').modal('show');
        $('input[name="revapp_id"]').val(appliid);
    });

    // ============================================================================
    // AGENT ASSIGNMENT HANDLERS
    // ============================================================================

    // Handler for "Add Super Agent" button
    $(document).on('click', '.opensuperagent', function(){
        var appliid = $(this).attr('data-id');
        $('#superagent_application').modal('show');
        $('#siapp_id').val(appliid);
    });

    // Handler for "Add Sub Agent" button
    $(document).on('click', '.opensubagent', function(){
        var appliid = $(this).attr('data-id');
        $('#subagent_application').modal('show');
        $('#sbapp_id').val(appliid);
    });

    // ============================================================================
    // PRODUCT FEE/COMMISSION STATUS HANDLERS
    // ============================================================================

    // Handler for "Edit Product Fees" button
    $(document).on('click', '.openpaymentfee', function(){
        var appliid = $(this).attr('data-id');
        var partnerid = $(this).attr('data-partnerid');
        
        // Load product fee form via AJAX
        var url = App.getUrl('showProductFee') || App.getUrl('siteUrl') + '/showproductfee';
        $.ajax({
            url: url,
            type: 'GET',
            data: {
                id: appliid,
                partnerid: partnerid
            },
            beforeSend: function() {
                // Show loading indicator
                $('.showproductfee').html('<div style="text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                $('#new_fee_option').modal('show');
            },
            success: function(response){
                // Load the HTML form into modal body
                $('.showproductfee').html(response);
                
                // Reinitialize form validation if needed
                if (typeof customValidate === 'function') {
                    // Form validation will be handled by the loaded form
                }
            },
            error: function(xhr, status, error){
                $('.showproductfee').html('<div style="text-align:center;padding:20px;color:red;"><i class="fas fa-exclamation-triangle"></i> Error loading fee details. Please try again.</div>');
                console.error('Error loading product fee:', error);
            }
        });
    });

    // Handler for "Edit Commission Status" button (Latest)
    $(document).on('click', '.openpaymentfeeLatest', function(){
        var appliid = $(this).attr('data-id');
        
        // Load commission status form via AJAX
        var url = App.getUrl('showProductFeeLatest') || App.getUrl('siteUrl') + '/showproductfeelatest';
        $.ajax({
            url: url,
            type: 'GET',
            data: {
                id: appliid
            },
            beforeSend: function() {
                // Show loading indicator
                $('.showproductfee_latest').html('<div style="text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                $('#new_fee_option_latest').modal('show');
            },
            success: function(response){
                // Load the HTML form into modal body
                $('.showproductfee_latest').html(response);
                
                // Initialize flatpickr for date fields in the loaded modal
                if (typeof flatpickr !== 'undefined') {
                    flatpickr('.showproductfee_latest .date_paid', {
                        dateFormat: 'Y-m-d',
                        allowInput: true
                    });
                }
                
                // Reinitialize form validation if needed
                if (typeof customValidate === 'function') {
                    // Form validation will be handled by the loaded form
                }
            },
            error: function(xhr, status, error){
                $('.showproductfee_latest').html('<div style="text-align:center;padding:20px;color:red;"><i class="fas fa-exclamation-triangle"></i> Error loading commission status. Please try again.</div>');
                console.error('Error loading commission status:', error);
            }
        });
    });

    console.log('[application-stage.js] Application stage handlers initialized');
});

})(); // End async wrapper

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

/**
 * Reload application activities accordion after state change
 * @param {number} appliid - Application ID
 */
function reloadApplicationActivities(appliid) {
    var clientId = PageConfig.clientId;
    var url = App.getUrl('getApplicationsLogs') || App.getUrl('siteUrl') + '/get-applications-logs';
    $.ajax({
        url: url,
        type: 'GET',
        data: { 
            id: appliid,
            clientid: clientId
        },
        success: function(response) {
            // Replace the accordion content
            $('#accordion').html(response);
            // Re-initialize Bootstrap Collapse for click functionality
            if (typeof reinitializeAccordions === 'function') {
                reinitializeAccordions();
            }
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

// Make functions available globally
if(typeof window !== 'undefined') {
    window.reloadApplicationActivities = reloadApplicationActivities;
    window.updateProgressBar = updateProgressBar;
}
