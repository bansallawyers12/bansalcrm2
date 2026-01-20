/**
 * Admin Client Detail - Document Upload Module
 * 
 * Handles document upload functionality for various document types
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
        console.log('[document-upload.js] Waiting for vendorLibsReady promise...');
        await window.vendorLibsReady;
        console.log('[document-upload.js] Vendor libraries ready!');
    } else {
        // Fallback: Poll for vendor libraries
        console.log('[document-upload.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined') {
                    console.log('[document-upload.js] All vendor libraries detected!');
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

// ============================================================================
// DOCUMENT UPLOAD HANDLERS
// ============================================================================

jQuery(document).ready(function($){
    
    // ============================================================================
    // STANDARD DOCUMENT UPLOAD HANDLERS
    // ============================================================================
    
    $(document).on('click', '.docupload', function() {
        $(this).attr("value", "");
    });

    $(document).on('change', '.docupload', function() {
        $('.popuploader').show();
        var formData = new FormData($('#upload_form')[0]);
        var url = App.getUrl('uploadDocument') || App.getUrl('siteUrl') + '/upload-document';
        $.ajax({
            url: url,
            type:'POST',
            headers: { 'X-CSRF-TOKEN': App.getCsrf()},
            datatype:'json',
            data: formData,
            contentType: false,
            processData: false,
            success: function(responses){
                $('.popuploader').hide();
                var ress = typeof responses === 'string' ? JSON.parse(responses) : responses;
                if(ress.status){
                    $('.custom-error-msg').html('<span class="alert alert-success">'+ress.message+'</span>');
                    $('.documnetlist').html(ress.data);
                    $('.griddata').html(ress.griddata);
                }else{
                    $('.custom-error-msg').html('<span class="alert alert-danger">'+ress.message+'</span>');
                }
                if(typeof getallactivities === 'function') {
                    getallactivities();
                }
            }
        });
    });

    // ============================================================================
    // MIGRATION DOCUMENT UPLOAD HANDLERS
    // ============================================================================
    
    $(document).on('click', '.migdocupload', function() {
        $(this).attr("value", "");
    });

    $(document).on('change', '.migdocupload', function() {
        $('.popuploader').show();
        var formData = new FormData($('#mig_upload_form')[0]);
        var url = App.getUrl('uploadDocument') || App.getUrl('siteUrl') + '/upload-document';
        $.ajax({
            url: url,
            type:'POST',
            headers: { 'X-CSRF-TOKEN': App.getCsrf()},
            datatype:'json',
            data: formData,
            contentType: false,
            processData: false,
            success: function(responses){
                $('.popuploader').hide();
                var ress = typeof responses === 'string' ? JSON.parse(responses) : responses;
                if(ress.status){
                    $('.custom-error-msg').html('<span class="alert alert-success">'+ress.message+'</span>');
                    $('.migdocumnetlist').html(ress.data);
                    $('.miggriddata').html(ress.griddata);
                }else{
                    $('.custom-error-msg').html('<span class="alert alert-danger">'+ress.message+'</span>');
                }
                if(typeof getallactivities === 'function') {
                    getallactivities();
                }
            }
        });
    });

    // ============================================================================
    // ALL DOCUMENTS UPLOAD HANDLERS
    // ============================================================================
    
    $(document).on('click', '.add_alldocument_doc', function () {
        $('.create_alldocument_docs').modal('show');
        $("#checklist").select2({dropdownParent: $(".create_alldocument_docs")});
    });

    // Trigger file input when "Add Document" button is clicked
    $(document).on('click', '.allupload_document .btn-primary', function(e) {
        e.preventDefault();
        $(this).closest('form').find('.alldocupload').click();
    });

    $(document).on('click', '.alldocupload', function() {
        $(this).attr("value", "");
    });

    $(document).on('change', '.alldocupload', function() {
        $('.popuploader').show();
        var fileidL = $(this).attr("data-fileid");
        var formData = new FormData($('#upload_form_'+fileidL)[0]);
        var url = App.getUrl('uploadAllDocument') || App.getUrl('siteUrl') + '/upload-alldocument';
        $.ajax({
            url: url,
            type:'POST',
            headers: { 'X-CSRF-TOKEN': App.getCsrf()},
            datatype:'json',
            data: formData,
            contentType: false,
            processData: false,
            success: function(responses){
                $('.popuploader').hide();
                var ress = typeof responses === 'string' ? JSON.parse(responses) : responses;
                if(ress.status){
                    $('.custom-error-msg').html('<span class="alert alert-success">'+ress.message+'</span>');
                    $('.alldocumnetlist').html(ress.data);
                    $('.allgriddata').html(ress.griddata);
                }else{
                    $('.custom-error-msg').html('<span class="alert alert-danger">'+ress.message+'</span>');
                }
                if(typeof getallactivities === 'function') {
                    getallactivities();
                }
            }
        });
    });

    // ============================================================================
    // APPLICATION CHECKLIST FILE UPLOAD MODAL HANDLERS
    // ============================================================================
    
    $(document).on('click', '.openfileupload', function(){
        var id = $(this).attr('data-id');
        var type = $(this).attr('data-type');
        var typename = $(this).attr('data-typename');
        var aid = $(this).attr('data-aid');
        $(".checklisttype").val(type);
        $(".checklistid").val(id);
        $(".checklisttypename").val(typename);
        $(".application_id").val(aid);
        $('#openfileuploadmodal').modal('show');
    });

    // Handler for opendocnote - Add Document icon in application detail
    $(document).on('click', '.opendocnote', function(){
        var apptype = $(this).attr('data-app-type');
        var typename = $(this).attr('data-typename');
        var aid = $(this).attr('data-id');
        var clientid = $(this).attr('data-appdocclientid');
        $(".checklisttype").val(apptype);
        $(".checklistid").val(''); // No specific checklist id for general document upload
        $(".checklisttypename").val(typename);
        $(".application_id").val(aid);
        $(".app_doc_client_id").val(clientid);
        $('#openfileuploadmodal').modal('show');
    });

    console.log('[document-upload.js] Document upload handlers initialized');
});

})(); // End async wrapper
