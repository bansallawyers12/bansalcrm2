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

    

    // ============================================================================
    // EMAIL/PHONE VERIFICATION HANDLER
    // ============================================================================
    
    $('.manual_email_phone_verified').on('change', function(){
        if( $(this).is(":checked") ) {
            $('.manual_email_phone_verified').val(1);
            var manual_email_phone_verified = 1;
        } else {
            $('.manual_email_phone_verified').val(0);
            var manual_email_phone_verified = 0;
        }

        var client_id = App.getPageConfig('clientId');
        var url = App.getUrl('clientUpdateEmailVerified') || App.getUrl('siteUrl') + '/clients/update-email-verified';
        $.ajax({
            url: url,
            headers: { 'X-CSRF-TOKEN': App.getCsrf()},
            type:'POST',
            data:{manual_email_phone_verified:manual_email_phone_verified,client_id:client_id},
            success: function(responses){
                location.reload();
            }
        });
    });

    // UI layout handlers moved to dedicated module file.

    // Modal handlers moved to modal-handlers module.

    // Assignment handlers moved to assignments module.

    // ============================================================================
    // OVERRIDE COMMON FUNCTIONS WITH PAGE-SPECIFIC IMPLEMENTATIONS
    // ============================================================================
    
    // Note: These functions override the common ones with more detailed implementations
    // If needed, these can be moved to common files later
    
    // Pin and publish handlers moved to pin-and-publish module.

    $(document).on('click', '.openassigneeshow', function(){
        $('.assigneeshow').show();
    });

    $(document).on('click', '.closeassigneeshow', function(){
        $('.assigneeshow').hide();
    });

    // ============================================================================
    // ASSIGNEE HANDLER
    // ============================================================================
    
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

    // Document action handlers moved to document-actions module.

    // Delete handlers moved to delete-handlers module.

    // Pin and publish handlers moved to pin-and-publish module.

    // Note handlers moved to notes module.

    // ============================================================================
    // APPLICATION HANDLERS
    // ============================================================================
    
    $(document).on('change', '.add_appliation #workflow', function(){
        var v = $('.add_appliation #workflow option:selected').val();
        if(v != ''){
            $('.popuploader').show();
            var url = App.getUrl('getPartnerBranch') || App.getUrl('siteUrl') + '/admin/getpartnerbranch';
            $.ajax({
                url: url,
                type:'GET',
                data:{cat_id:v},
                success:function(response){
                    $('.popuploader').hide();
                    $('.add_appliation #partner').html(response);
                    $(".add_appliation #partner").val('').trigger('change');
                    $(".add_appliation #product").val('').trigger('change');
                    $(".add_appliation #branch").val('').trigger('change');
                }
            });
        }
    });

    $(document).on('change', '.add_appliation #partner', function(){
        var v = $('.add_appliation #partner option:selected').val();
        var explode = v.split('_');
        if(v != ''){
            $('.popuploader').show();
            $('.add_appliation #product').attr('data-valid', '');
            $('.add_appliation #product').prop('disabled', true);
            $('.add_appliation .product_error').html('');
            var url = App.getUrl('getBranchProduct') || App.getUrl('siteUrl') + '/admin/getbranchproduct';
            $.ajax({
                url: url,
                type:'GET',
                data:{cat_id:explode[0]},
                success:function(response){
                    $('.popuploader').hide();
                    $('.add_appliation #product').html(response);
                    $('.add_appliation #product').prop('disabled', false);
                    $('.add_appliation #product').attr('data-valid', 'required');
                    $(".add_appliation #product").val('').trigger('change');
                },
                error: function() {
                    $('.popuploader').hide();
                    $('.add_appliation #product').prop('disabled', false);
                    $('.add_appliation #product').attr('data-valid', 'required');
                    $('.add_appliation #product').html('<option value="">Select Product</option>');
                }
            });
        }
    });

    // ============================================================================
    // EMAIL HANDLERS
    // ============================================================================
    
    $(document).on('click', '.clientemail', function(){
        $('#emailmodal').modal('show');
        var array = [];
        var data = [];

        var id = $(this).attr('data-id');
        array.push(id);
        var email = $(this).attr('data-email');
        var name = $(this).attr('data-name');
        var status = 'Client';

        data.push({
            id: id,
            text: name,
            html:  "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +
                "<div  class='ag-flex ag-align-start'>" +
                    "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'>"+name+"</span>&nbsp;</div>" +
                    "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'>"+email+"</small ></div>" +
                "</div>" +
            "</div>" +
            "<div class='ag-flex ag-flex-column ag-align-end'>" +
                "<span class='ui label yellow select2-result-repository__statistics'>"+ status +
                "</span>" +
            "</div>" +
            "</div>",
            title: name
        });

        $(".js-data-example-ajax").select2({
            data: data,
            escapeMarkup: function(markup) {
                return markup;
            },
            templateResult: function(data) {
                return data.html;
            },
            templateSelection: function(data) {
                return data.text;
            }
        });
        $('.js-data-example-ajax').val(array);
        $('.js-data-example-ajax').trigger('change');
    });

    $(document).on('click', '.sendmsg', function(){
        $('#sendmsgmodal').modal('show');
        var client_id = $(this).attr('data-id');
        $('#sendmsg_client_id').val(client_id);
    });

    // ============================================================================
    // CLIENT STATUS HANDLER
    // ============================================================================
    
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

    // ============================================================================
    // EMAIL TEMPLATE HANDLERS
    // ============================================================================
    
    $(document).on('change', '.selecttemplate', function(){
        var client_id = $(this).data('clientid');
        var client_firstname = $(this).data('clientfirstname');
        if (client_firstname) {
            client_firstname = client_firstname.charAt(0).toUpperCase() + client_firstname.slice(1);
        }
        var client_reference_number = $(this).data('clientreference_number');
        var company_name = 'Bansal Education Group';
        var visa_valid_upto = $(this).data('clientvisaExpiry');
        if ( visa_valid_upto != '' && visa_valid_upto != '0000-00-00') {
            visa_valid_upto = visa_valid_upto;
        } else {
            visa_valid_upto = '';
        }
        
        var clientassignee_name = $(this).data('clientassignee_name');
        if ( clientassignee_name != '') {
            clientassignee_name = clientassignee_name;
        } else {
            clientassignee_name = '';
        }

        var v = $(this).val();
        var url = App.getUrl('getTemplates') || App.getUrl('siteUrl') + '/get-templates';
        $.ajax({
            url: url,
            type:'GET',
            datatype:'json',
            data:{id:v},
            success: function(response){
                var res = typeof response === 'string' ? JSON.parse(response) : response;

                var subjct_message = res.subject.replace('{Client First Name}', client_firstname).replace('{client reference}', client_reference_number);
                $('.selectedsubject').val(subjct_message);
      
                if($("#emailmodal .summernote-simple").length && typeof $.fn.summernote !== 'undefined') {
                    $("#emailmodal .summernote-simple").summernote('reset');
                }
      
                var subjct_description = res.description
                    .replace('{Client First Name}', client_firstname)
                    .replace('{Company Name}', company_name)
                    .replace('{Visa Valid Upto}', visa_valid_upto)
                    .replace('{Client Assignee Name}', clientassignee_name)
                    .replace('{client reference}', client_reference_number);
      
                if($("#emailmodal .summernote-simple").length && typeof $.fn.summernote !== 'undefined') {
                    $("#emailmodal .summernote-simple").summernote('code', subjct_description);
                }
                $("#emailmodal .summernote-simple").val(subjct_description);
            }
        });
    });

    $(document).on('change', '.selectapplicationtemplate', function(){
        var v = $(this).val();
        var url = App.getUrl('getTemplates') || App.getUrl('siteUrl') + '/get-templates';
        $.ajax({
            url: url,
            type:'GET',
            datatype:'json',
            data:{id:v},
            success: function(response){
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                $('.selectedappsubject').val(res.subject);
                if($("#applicationemailmodal .summernote-simple").length && typeof $.fn.summernote !== 'undefined') {
                    $("#applicationemailmodal .summernote-simple").summernote('reset');
                    $("#applicationemailmodal .summernote-simple").summernote('code', res.description);
                }
                $("#applicationemailmodal .summernote-simple").val(res.description);
            }
        });
    });

    // Initialize Select2 for email recipients
    $('.js-data-example-ajax').select2({
        multiple: true,
        closeOnSelect: false,
        dropdownParent: $('#emailmodal'),
        ajax: {
            url: App.getUrl('clientGetRecipients') || App.getUrl('siteUrl') + '/clients/get-recipients',
            headers: { 'X-CSRF-TOKEN': App.getCsrf()},
            dataType: 'json',
            processResults: function (data) {
                return {
                    results: data.items
                };
            },
            cache: true
        },
        templateResult: formatRepo,
        templateSelection: formatRepoSelection
    });

    $('.js-data-example-ajaxccd').select2({
        multiple: true,
        closeOnSelect: false,
        dropdownParent: $('#emailmodal'),
        ajax: {
            url: App.getUrl('clientGetRecipients') || App.getUrl('siteUrl') + '/clients/get-recipients',
            headers: { 'X-CSRF-TOKEN': App.getCsrf()},
            dataType: 'json',
            processResults: function (data) {
                return {
                    results: data.items
                };
            },
            cache: true
        },
        templateResult: formatRepo,
        templateSelection: formatRepoSelection
    });

    // ============================================================================
    // INTERESTED PRODUCT HANDLER
    // ============================================================================
    
    $(document).on('change', '#intrested_product', function(){
        var v = $('#intrested_product option:selected').val();
        if(v != ''){
            $('.popuploader').show();
            var url = App.getUrl('getBranch') || App.getUrl('siteUrl') + '/get-branches';
            $.ajax({
                url: url,
                type:'GET',
                data:{cat_id:v},
                success:function(response){
                    $('.popuploader').hide();
                    $('#intrested_branch').html(response);
                    $("#intrested_branch").val('').trigger('change');
                }
            });
        }
    });

    // ============================================================================
    // DOCUMENT UPLOAD HANDLERS
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
    // CONVERT TO APPLICATION HANDLER
    // ============================================================================
    
    $(document).on('click', '.converttoapplication', function(){
        var v = $(this).attr('data-id');
        if(v != ''){
            $('.popuploader').show();
            var url = App.getUrl('convertApplication') || App.getUrl('siteUrl') + '/convertapplication';
            $.ajax({
                url: url,
                type:'GET',
                data:{cat_id:v, clientid: App.getPageConfig('clientId')},
                success:function(response){
                    var res = typeof response === 'string' ? JSON.parse(response) : response;
                    if(!res || res.status !== true){
                        $('.popuploader').hide();
                        alert((res && res.message) ? res.message : 'Failed to create application. Please try again.');
                        return;
                    }

                    var servicesUrl = App.getUrl('getServices') || App.getUrl('siteUrl') + '/get-services';
                    $.ajax({
                        url: servicesUrl,
                        type:'GET',
                        data:{clientid: App.getPageConfig('clientId')},
                        success: function(responses){
                            $('.interest_serv_list').html(responses);
                            var appListsUrl = App.getUrl('getApplicationLists') || App.getUrl('siteUrl') + '/get-application-lists';
                            $.ajax({
                                url: appListsUrl,
                                type:'GET',
                                datatype:'json',
                                data:{id: App.getPageConfig('clientId')},
                                success: function(responses){
                                    $('.applicationtdata').html(responses);
                                    $('.popuploader').hide();
                                },
                                error: function(){
                                    $('.popuploader').hide();
                                    alert('Application created, but failed to refresh the application list. Please refresh the page.');
                                }
                            });
                        },
                        error: function(){
                            $('.popuploader').hide();
                            alert('Application created, but failed to refresh the services list. Please refresh the page.');
                        }
                    });
                },
                error: function(){
                    $('.popuploader').hide();
                    alert('Failed to create application. Please try again.');
                }
            });
        }
    });

    $(document).on('click', '#application-tab', function (e) {
        // Check if we're currently viewing an application detail
        var tabList = document.getElementById('client_tabs');
        var isViewingDetail = tabList && tabList.getAttribute('data-application-id');
        
        // If viewing detail, reset to list and prevent default tab behavior
        if (isViewingDetail) {
            e.preventDefault();
            e.stopPropagation();
            showApplicationList();
            $('.popuploader').show();
            var url = App.getUrl('getApplicationLists') || App.getUrl('siteUrl') + '/get-application-lists';
            $.ajax({
                url: url,
                type:'GET',
                datatype:'json',
                data:{id: App.getPageConfig('clientId')},
                success: function(responses){
                    $('.popuploader').hide();
                    $('.applicationtdata').html(responses);
                }
            });
        } else {
            // Otherwise, refresh the list normally
            showApplicationList();
            $('.popuploader').show();
            var url = App.getUrl('getApplicationLists') || App.getUrl('siteUrl') + '/get-application-lists';
            $.ajax({
                url: url,
                type:'GET',
                datatype:'json',
                data:{id: App.getPageConfig('clientId')},
                success: function(responses){
                    $('.popuploader').hide();
                    $('.applicationtdata').html(responses);
                }
            });
        }
    });

    // ============================================================================
    // DOCUMENT RENAME HANDLERS
    // ============================================================================
    
    $(document).on('click', '.documnetlist .renamedoc', function () {
        var parent = $(this).closest('.drow').find('.doc-row');
        parent.data('current-html', parent.html());
        var opentime = parent.data('name');
        parent.empty().append(
            $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
            $('<button class="btn btn-primary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
            $('<button class="btn btn-danger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
        );
        return false;
    });

    $(document).on('click', '.migdocumnetlist .renamedoc', function () {
        var parent = $(this).closest('.drow').find('.doc-row');
        parent.data('current-html', parent.html());
        var opentime = parent.data('name');
        parent.empty().append(
            $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
            $('<button class="btn btn-primary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
            $('<button class="btn btn-danger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
        );
        return false;
    });

    // ============================================================================
    // DATA TABLE INITIALIZATION
    // ============================================================================
    
    // Initialize DataTable for the checklist table
    let selectedChecklists = [];
    if($('#mychecklist-datatable').length) {
        let checklistTable = $('#mychecklist-datatable').DataTable({
            "paging": true,
            "pageLength": 10,
            "searching": true,
            "ordering": true,
            "info": true,
            "dom": 'lfrtip',
            "drawCallback": function(settings) {
                let api = this.api();
                api.rows().every(function() {
                    let row = this.node();
                    let checkbox = $(row).find('input[name="checklistfile[]"]');
                    let checklistId = checkbox.val();
                    if (selectedChecklists.includes(checklistId)) {
                        checkbox.prop('checked', true);
                    } else {
                        checkbox.prop('checked', false);
                    }
                });
            }
        });
    }

    // ============================================================================
    // APPLICATION DETAIL HANDLERS
    // ============================================================================
    
    function updateClientDetailUrl(tab, applicationId) {
        var tabList = document.getElementById('client_tabs');
        if (!tabList) {
            return;
        }
        var baseUrl = tabList.getAttribute('data-base-url');
        if (!baseUrl) {
            return;
        }
        var base = new URL(baseUrl, window.location.origin);
        var basePath = base.pathname.replace(/\/+$/, '');
        var url = new URL(window.location.href);
        url.searchParams.delete('tab');

        if (tab === 'application' && applicationId) {
            tabList.setAttribute('data-application-id', applicationId);
            url.pathname = basePath + '/application/' + applicationId;
        } else if (tab && tab !== 'activities') {
            tabList.removeAttribute('data-application-id');
            url.pathname = basePath + '/' + tab;
        } else {
            tabList.removeAttribute('data-application-id');
            url.pathname = basePath;
        }

        history.replaceState(null, '', url.toString());
    }

    function showApplicationList() {
        $('.if_applicationdetail').show();
        $('.ifapplicationdetailnot').hide();
        $('.ifapplicationdetailnot').html('<h4>Please wait ...</h4>');
        var tabList = document.getElementById('client_tabs');
        if (tabList) {
            tabList.removeAttribute('data-application-id');
        }
        localStorage.removeItem('activeTab');
        localStorage.removeItem('appliid');
        updateClientDetailUrl('application', null);
    }

    // Handle clicking on application/course name to view details
    // NOTE: This handler must be registered BEFORE the automatic click trigger below
    $(document).on('click', '.openapplicationdetail', function(event){
        if (event && (event.ctrlKey || event.metaKey || event.shiftKey || event.which === 2)) {
            return;
        }
        event.preventDefault();
        var appliid = $(this).attr('data-id');
        $('.if_applicationdetail').hide();
        $('.ifapplicationdetailnot').show();
        $('.popuploader').show();
        updateClientDetailUrl('application', appliid);
        
        var url = App.getUrl('getApplicationDetail') || App.getUrl('siteUrl') + '/getapplicationdetail';
        $.ajax({
            url: url,
            type: 'GET',
            data: {id: appliid},
            success: function(response){
                $('.popuploader').hide();
                $('.ifapplicationdetailnot').html(response);
                
                if (typeof flatpickr !== 'undefined') {
                    flatpickr('.datepicker', {
                        dateFormat: "Y-m-d",
                        allowInput: true,
                        onChange: function(selectedDates, dateStr, instance) {
                            if (selectedDates.length > 0) {
                                $.ajax({
                                    url: App.getUrl('siteUrl') + '/application/updateintake',
                                    method: "GET",
                                    dataType: "json",
                                    data: {from: dateStr, appid: appliid},
                                    success: function(result) {
                                        console.log("Intake date updated");
                                    }
                                });
                            }
                        }
                    });
                    
                    // Initialize start date picker with save callback
                    flatpickr('.startdatepicker', {
                        dateFormat: "Y-m-d",
                        allowInput: true,
                        onChange: function(selectedDates, dateStr, instance) {
                            if (selectedDates.length > 0) {
                                var appid = $('.startdatepicker').closest('.cus_sidebar').find('#student_id').attr('data-applicationid');
                                $.ajax({
                                    url: App.getUrl('updateApplicationDates') || App.getUrl('siteUrl') + '/application/updatedates',
                                    method: "GET",
                                    dataType: "json",
                                    data: {
                                        start_date: dateStr,
                                        appid: appid
                                    },
                                    success: function(result) {
                                        console.log("Start date updated");
                                        // Update the displayed date
                                        var date = new Date(dateStr);
                                        $('.app_start_date .month').text(date.toLocaleString('default', { month: 'short' }));
                                        $('.app_start_date .day').text(('0' + date.getDate()).slice(-2));
                                        $('.app_start_date .year').text(date.getFullYear());
                                    },
                                    error: function() {
                                        console.error("Error updating start date");
                                    }
                                });
                            }
                        }
                    });
                    
                    // Initialize end date picker with save callback
                    flatpickr('.enddatepicker', {
                        dateFormat: "Y-m-d",
                        allowInput: true,
                        onChange: function(selectedDates, dateStr, instance) {
                            if (selectedDates.length > 0) {
                                var appid = $('.enddatepicker').closest('.cus_sidebar').find('#student_id').attr('data-applicationid');
                                $.ajax({
                                    url: App.getUrl('updateApplicationDates') || App.getUrl('siteUrl') + '/application/updatedates',
                                    method: "GET",
                                    dataType: "json",
                                    data: {
                                        end_date: dateStr,
                                        appid: appid
                                    },
                                    success: function(result) {
                                        console.log("End date updated");
                                        // Update the displayed date
                                        var date = new Date(dateStr);
                                        $('.app_end_date .month').text(date.toLocaleString('default', { month: 'short' }));
                                        $('.app_end_date .day').text(('0' + date.getDate()).slice(-2));
                                        $('.app_end_date .year').text(date.getFullYear());
                                    },
                                    error: function() {
                                        console.error("Error updating end date");
                                    }
                                });
                            }
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                $('.popuploader').hide();
                console.error('Error loading application detail:', error);
                $('.ifapplicationdetailnot').html('<h4>Error loading application details. Please try again.</h4>');
            }
        });
    });

    // Handle application detail from URL or localStorage
    // NOTE: This must run AFTER the openapplicationdetail click handler is registered above
    const tabList = document.getElementById('client_tabs');
    const applicationIdFromUrl = tabList ? tabList.getAttribute('data-application-id') : '';
    const activeTab = localStorage.getItem('activeTab');
    const appliid = localStorage.getItem('appliid');

    if (applicationIdFromUrl) {
        $('#client_tabs .nav-link').removeClass('active');
        $('.tab-content .tab-pane').removeClass('active show');
        $('#application-tab').addClass('active');
        $('#application').addClass('active show');
        const $applicationDetailButton = $('<button>').addClass('openapplicationdetail').attr('data-id', applicationIdFromUrl).hide();
        $('body').append($applicationDetailButton);
        $applicationDetailButton.trigger('click');
        $applicationDetailButton.remove();
    } else if (activeTab === 'application' && appliid != "") {
        $('#client_tabs .nav-link').removeClass('active');
        $('.tab-content .tab-pane').removeClass('active show');
        $('#application-tab').addClass('active');
        $('#application').addClass('active show');
        const $applicationDetailButton = $('<button>').addClass('openapplicationdetail').attr('data-id', appliid).hide();
        $('body').append($applicationDetailButton);
        $applicationDetailButton.trigger('click');
        $applicationDetailButton.remove();
        localStorage.removeItem('activeTab');
        localStorage.removeItem('appliid');
    }

    $(document).on('click', '.openappnote', function(){
        var apptype = $(this).attr('data-app-type');
        var id = $(this).attr('data-id');
        $('#create_applicationnote #noteid').val(id);
        $('#create_applicationnote #type').val(apptype);
        $('#create_applicationnote').modal('show');
    });

    $(document).on('click', '.openappappoint', function(){
        var id = $(this).attr('data-id');
        var apptype = $(this).attr('data-app-type');
        $('#create_applicationappoint #type').val(apptype);
        $('#create_applicationappoint #appointid').val(id);
        $('#create_applicationappoint').modal('show');
    });

    $(document).on('click', '.openappaction', function(){
        var assign_application_id = $(this).attr('data-id');
        $('#create_applicationaction #assign_application_id').val(assign_application_id);
        var stage_name = $(this).attr('data-app-type');
        $('#create_applicationaction #stage_name').val(stage_name);
        $('#create_applicationaction #stage_name_f').html(stage_name);
        var course = $(this).attr('data-course');
        $('#create_applicationaction #course_s').html(course);
        $('#create_applicationaction #course').val(course);
        var school = $(this).attr('data-school');
        $('#create_applicationaction #school_s').html(school);
        $('#create_applicationaction #school').val(school);
        $('#create_applicationaction').modal('show');
    });

    $(document).on('click', '.openclientemail', function(){
        var id = $(this).attr('data-id');
        var apptype = $(this).attr('data-app-type');
        $('#applicationemailmodal #type').val(apptype);
        $('#applicationemailmodal #appointid').val(id);
        $('#applicationemailmodal').modal('show');
    });

    $(document).on('click', '.openchecklist', function(){
        var id = $(this).attr('data-id');
        var type = $(this).attr('data-type');
        var typename = $(this).attr('data-typename');
        $('#create_checklist #checklistapp_id').val(id);
        $('#create_checklist #checklist_type').val(type);
        $('#create_checklist #checklist_typename').val(typename);
        $('#create_checklist').modal('show');
    });

    // Handler for "Proceed to Next Stage" button
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
                            reinitializeAccordions();
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
        $('input[name="reapp_id"]').val(appliid);  // Fixed: Changed from refapp_id to reapp_id to match form field name
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

    // NOTE: openpaymentschedule handler removed - Invoice Schedule feature has been removed

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

    // ============================================================================
    // PRODUCT FEE AUTO-CALCULATION
    // ============================================================================
    
    // Calculate Tution Fee (Total Course Fee - Scholarship Fee - Enrolment Fee - Material Fees)
    function calculateTutionFee() {
        // Get individual fee values
        var totalCourseFee = parseFloat($('#total_course_fee_amount').val()) || 0;
        var scholarshipFee = parseFloat($('#scholarship_fee_amount').val()) || 0;
        var enrolmentFee = parseFloat($('#enrolment_fee_amount').val()) || 0;
        var materialFees = parseFloat($('#material_fee_amount').val()) || 0;
        
        // Calculate tuition fee: All three fees are SUBTRACTED from Total Course Fee
        var totalFee = totalCourseFee - scholarshipFee - enrolmentFee - materialFees;
        
        // Ensure tuition fee doesn't go negative
        if (totalFee < 0) {
            totalFee = 0;
        }
        
        // Format to 2 decimal places
        var formattedTotal = totalFee.toFixed(2);
        
        // Update display in table footer
        $('.calculate_tution_fee').html(formattedTotal);
        
        // Update hidden field for form submission
        $('#tution_fees').val(formattedTotal);
        
        return totalFee;
    }

    // Trigger calculation when any fee input changes
    $(document).on('keyup change blur', '.total_fee_am', function(){
        calculateTutionFee();
    });

    // Calculate on modal load (when form is populated with existing data)
    $(document).on('shown.bs.modal', '#new_fee_option', function(){
        // Small delay to ensure form is fully loaded
        setTimeout(function(){
            calculateTutionFee();
        }, 100);
    });

    $(document).on('click', '.addfee', function(){
        var clonedval = $('.feetypecopy').html();
        $('.fee_type_sec .fee_fields').append('<div class="fee_fields_row field_clone">'+clonedval+'</div>');
    });

    $(document).on('click', '.payremoveitems', function(){
        $(this).parent().parent().remove();
        schedulecalculatetotal();
    });

    $(document).on('keyup', '.payfee_amount', function(){
        schedulecalculatetotal();
    });

    $(document).on('keyup', '.paydiscount', function(){
        schedulecalculatetotal();
    });

    function schedulecalculatetotal(){
        var feeamount = 0;
        $('.payfee_amount').each(function(){
            if($(this).val() != ''){
                feeamount += parseFloat($(this).val());
            }
        });
        var discount = 0;
        if($('.paydiscount').val() != ''){
            discount = $('.paydiscount').val();
        }
        var netfee = feeamount - parseFloat(discount);
        $('.paytotlfee').html(feeamount.toFixed(2));
        $('.paynetfeeamt').html(netfee.toFixed(2));
    }

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

    // ============================================================================
    // COMMISSION CALCULATION HANDLERS
    // ============================================================================
    
    // Total Fee Amount blur change
    $(document).on('blur', '.total_fee_am_2nd', function(){
        var tution_fee_paid = $(this).val();
        if(tution_fee_paid != ""){
            tution_fee_paid = tution_fee_paid;
        } else {
            tution_fee_paid = 0;
        }

        var commission_percentage = $(this).closest('tr').find('.commission_percentage').val();
        if(commission_percentage != ""){
            commission_percentage = commission_percentage;
        } else {
            commission_percentage = 0;
        }

        var commission_percentage_calculate = ( parseFloat(tution_fee_paid) * parseFloat(commission_percentage))/100;
        var commission_percentage_calculate_fixed = commission_percentage_calculate.toFixed(2);

        $(this).closest('tr').find('.commission_cal').val(commission_percentage_calculate);
        $(this).closest('tr').find('.commission_cal_hidden').val(commission_percentage_calculate);

        var adjustment_discount_entry = $(this).closest('tr').find('.adjustment_discount_entry').val();
        if(adjustment_discount_entry != ""){
            adjustment_discount_entry = parseFloat(adjustment_discount_entry);
        } else {
            adjustment_discount_entry = 0;
        }

        var commission_claimed = commission_percentage_calculate + adjustment_discount_entry;
        $(this).closest('tr').find('.commission_claimed').val(commission_claimed);
        $(this).closest('tr').find('.commission_claimed_hidden').val(commission_claimed);

        var total_fee_paid = 0;
        $('.total_fee_am_2nd').each(function(){
            total_fee_paid += parseFloat($(this).val());
        });

        var total_com_price = 0;
        $('.commission_cal').each(function(){
            total_com_price += parseFloat($(this).val());
        });

        var total_adjustment_discount_entry = 0;
        $('.adjustment_discount_entry').each(function(){
            total_adjustment_discount_entry += parseFloat($(this).val());
        });

        var total_commission_claimed = 0;
        $('.commission_claimed').each(function(){
            total_commission_claimed += parseFloat($(this).val());
        });

        $('.total_fees_paid').html(total_fee_paid);
        $('.total_commission_earned').html(total_com_price);
        $('.total_adjustment_discount_entry').html(total_adjustment_discount_entry);
        $('.total_commission_claimed').html(total_commission_claimed);
    });

    // Commission Percentage blur change
    $(document).on('blur', '.commission_percentage', function(){
        var commission_percentage = $(this).val();
        if(commission_percentage != ""){
            commission_percentage = commission_percentage;
        } else {
            commission_percentage = 0;
        }
        var tution_fee_paid = $(this).closest('tr').find('.total_fee_am_2nd').val();
        if(tution_fee_paid != ""){
            tution_fee_paid = tution_fee_paid;
        } else {
            tution_fee_paid = 0;
        }

        var commission_percentage_calculate = ( parseFloat(tution_fee_paid) * parseFloat(commission_percentage))/100;
        var commission_percentage_calculate_fixed = commission_percentage_calculate.toFixed(2);

        $(this).closest('tr').find('.commission_cal').val(commission_percentage_calculate);
        $(this).closest('tr').find('.commission_cal_hidden').val(commission_percentage_calculate);

        var adjustment_discount_entry = $(this).closest('tr').find('.adjustment_discount_entry').val();
        if(adjustment_discount_entry != ""){
            adjustment_discount_entry = parseFloat(adjustment_discount_entry);
        } else {
            adjustment_discount_entry = 0;
        }

        var commission_claimed = commission_percentage_calculate + adjustment_discount_entry;
        $(this).closest('tr').find('.commission_claimed').val(commission_claimed);
        $(this).closest('tr').find('.commission_claimed_hidden').val(commission_claimed);

        var total_fee_paid = 0;
        $('.total_fee_am_2nd').each(function(){
            total_fee_paid += parseFloat($(this).val());
        });

        var total_com_price = 0;
        $('.commission_cal').each(function(){
            total_com_price += parseFloat($(this).val());
        });

        var total_adjustment_discount_entry = 0;
        $('.adjustment_discount_entry').each(function(){
            total_adjustment_discount_entry += parseFloat($(this).val());
        });

        var total_commission_claimed = 0;
        $('.commission_claimed').each(function(){
            total_commission_claimed += parseFloat($(this).val());
        });

        $('.total_fees_paid').html(total_fee_paid);
        $('.total_commission_earned').html(total_com_price);
        $('.total_adjustment_discount_entry').html(total_adjustment_discount_entry);
        $('.total_commission_claimed').html(total_commission_claimed);
    });

    // Adjustment Discount Entry blur change
    $(document).on('blur', '.adjustment_discount_entry', function(){
        var adjustment_discount_entry = $(this).val();
        if(adjustment_discount_entry != ""){
            adjustment_discount_entry = parseFloat(adjustment_discount_entry);
        } else {
            adjustment_discount_entry = 0;
        }

        var tution_fee_paid = $(this).closest('tr').find('.total_fee_am_2nd').val();
        if(tution_fee_paid != ""){
            tution_fee_paid = tution_fee_paid;
        } else {
            tution_fee_paid = 0;
        }

        var commission_percentage = $(this).closest('tr').find('.commission_percentage').val();
        if(commission_percentage != ""){
            commission_percentage = commission_percentage;
        } else {
            commission_percentage = 0;
        }

        var commission_percentage_calculate = ( parseFloat(tution_fee_paid) * parseFloat(commission_percentage))/100;
        var commission_percentage_calculate_fixed = commission_percentage_calculate.toFixed(2);

        $(this).closest('tr').find('.commission_cal').val(commission_percentage_calculate);
        $(this).closest('tr').find('.commission_cal_hidden').val(commission_percentage_calculate);

        var commission_claimed = commission_percentage_calculate + adjustment_discount_entry;
        $(this).closest('tr').find('.commission_claimed').val(commission_claimed);
        $(this).closest('tr').find('.commission_claimed_hidden').val(commission_claimed);

        var total_fee_paid = 0;
        $('.total_fee_am_2nd').each(function(){
            total_fee_paid += parseFloat($(this).val());
        });

        var total_com_price = 0;
        $('.commission_cal').each(function(){
            total_com_price += parseFloat($(this).val());
        });

        var total_adjustment_discount_entry = 0;
        $('.adjustment_discount_entry').each(function(){
            total_adjustment_discount_entry += parseFloat($(this).val());
        });

        var total_commission_claimed = 0;
        $('.commission_claimed').each(function(){
            total_commission_claimed += parseFloat($(this).val());
        });

        $('.total_fees_paid').html(total_fee_paid);
        $('.total_commission_earned').html(total_com_price);
        $('.total_adjustment_discount_entry').html(total_adjustment_discount_entry);
        $('.total_commission_claimed').html(total_commission_claimed);
    });

    // ============================================================================
    // DRAG AND DROP HANDLERS FOR APPLICATION CHECKLIST UPLOADS
    // ============================================================================
    
    $(document).on("dragover", "#ddArea", function() {
        $(this).addClass("drag_over");
        return false;
    });

    $(document).on("dragleave", "#ddArea", function() {
        $(this).removeClass("drag_over");
        return false;
    });

    $(document).on("click", "#ddArea", function(e) {
        applicationFileExplorer();
    });

    $(document).on("drop", "#ddArea", function(e) {
        e.preventDefault();
        $(this).removeClass("drag_over");
        var formData = new FormData();
        var files = e.originalEvent.dataTransfer.files;
        for (var i = 0; i < files.length; i++) {
            formData.append("file[]", files[i]);
        }
        formData.append("type", $('.checklisttype').val());
        formData.append("typename", $('.checklisttypename').val());
        formData.append("id", $('.checklistid').val());
        formData.append("application_id", $('.application_id').val());
        formData.append("client_id", $('.app_doc_client_id').val());
        applicationUploadFormData(formData);
    });

    // Page-specific file explorer for application checklist uploads
    function applicationFileExplorer() {
        const selectfile = document.getElementById("selectfile");
        if (!selectfile) {
            console.warn("selectfile element not found");
            return;
        }
        selectfile.click();
        selectfile.onchange = function() {
            var files = selectfile.files;
            var formData = new FormData();

            for (var i = 0; i < files.length; i++) {
                formData.append("file[]", files[i]);
            }
            formData.append("type", $('.checklisttype').val());
            formData.append("typename", $('.checklisttypename').val());
            formData.append("id", $('.checklistid').val());
            formData.append("application_id", $('.application_id').val());
            formData.append("client_id", $('.app_doc_client_id').val());
            applicationUploadFormData(formData);
        };
    }

    // Page-specific upload function for application checklist uploads
    function applicationUploadFormData(form_data) {
        function updateUploadSummary(type, message) {
            var summaryEl = document.getElementById('uploadSummary');
            if (!summaryEl) {
                return;
            }
            summaryEl.className = 'alert alert-' + type;
            summaryEl.textContent = message;
            summaryEl.style.display = 'block';
        }

        function showUploadToast(type, title, message) {
            if (typeof iziToast !== 'undefined') {
                iziToast[type]({
                    title: title,
                    message: message,
                    position: 'topRight',
                    timeout: 8000
                });
            } else {
                alert(title + ': ' + message);
            }
        }

        $('.popuploader').show();
        var url = App.getUrl('applicationChecklistUpload') || App.getUrl('siteUrl') + '/application/checklistupload';
        $.ajax({
            url: url,
            method: "POST",
            headers: { 'X-CSRF-TOKEN': App.getCsrf()},
            data: form_data,
            datatype: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function(response) {
                var obj = typeof response === 'string' ? $.parseJSON(response) : response;
                $('.popuploader').hide();

                if (!obj) {
                    showUploadToast('error', 'Upload failed', 'Unable to upload files.');
                    updateUploadSummary('danger', 'Upload failed. Unable to upload files.');
                    return;
                }
                if (obj.status === false && !obj.doclistdata) {
                    showUploadToast('error', 'Upload failed', obj.message || 'Unable to upload files.');
                    updateUploadSummary('danger', obj.message || 'Upload failed. Unable to upload files.');
                    return;
                }

                $('#openfileuploadmodal').modal('hide');
                $('.mychecklistdocdata').html(obj.doclistdata || '');
                $('.checklistuploadcount').html(obj.applicationuploadcount || '');
                if (obj.type && obj.checklistdata) {
                    $('.'+obj.type+'_checklists').html(obj.checklistdata);
                }
                if ($('#selectfile').length) {
                    $('#selectfile').val('');
                }

                if (obj.status === false && obj.message) {
                    showUploadToast('warning', 'Upload completed with errors', obj.message);
                    updateUploadSummary('warning', obj.message);
                }

                if (obj.upload_summary) {
                    var summary = obj.upload_summary;
                    var failedFiles = summary.failed_files || [];
                    if (summary.failed_count > 0) {
                        var detailText = failedFiles.map(function(item) {
                            return item.name + (item.reason ? ' (' + item.reason + ')' : '');
                        }).join(', ');
                        showUploadToast(
                            'warning',
                            'Upload completed with errors',
                            'Uploaded ' + summary.uploaded_count + '/' + summary.total + '. Failed: ' + detailText
                        );
                        updateUploadSummary(
                            'warning',
                            'Uploaded ' + summary.uploaded_count + '/' + summary.total + '. Failed: ' + detailText
                        );
                    } else {
                        showUploadToast('success', 'Upload completed', summary.uploaded_count + ' file(s) uploaded.');
                        updateUploadSummary('success', summary.uploaded_count + ' file(s) uploaded.');
                    }
                }

                if(obj.application_id){
                    var logsUrl = App.getUrl('getApplicationsLogs') || App.getUrl('siteUrl') + '/get-applications-logs';
                    $.ajax({
                        url: logsUrl,
                        type:'GET',
                        data:{id: obj.application_id},
                        success: function(responses){
                            $('#accordion').html(responses);
                            // Re-initialize Bootstrap Collapse for click functionality
                            reinitializeAccordions();
                        }
                    });
                }
            },
            error: function(xhr) {
                $('.popuploader').hide();
                var message = 'Unable to upload files. Please try again.';
                if (xhr && xhr.responseText) {
                    try {
                        var errObj = $.parseJSON(xhr.responseText);
                        if (errObj && errObj.message) {
                            message = errObj.message;
                        }
                    } catch (e) {
                        // keep default message
                    }
                }
                showUploadToast('error', 'Upload failed', message);
                updateUploadSummary('danger', message);
            }
        });
    }

    // Make these functions available globally for backward compatibility
    if(typeof window !== 'undefined') {
        window.applicationFileExplorer = applicationFileExplorer;
        window.applicationUploadFormData = applicationUploadFormData;
    }

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

    // Rename File Name For All Documents
    $(document).on('click', '.alldocumnetlist .renamealldoc', function () {
        var parent = $(this).closest('.drow').find('.doc-row');
        parent.data('current-html', parent.html());
        var opentime = parent.data('name');
        parent.empty().append(
            $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
            $('<button class="btn btn-primary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
            $('<button class="btn btn-danger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
        );
        return false;
    });

    $(document).on('click', '.alldocumnetlist .drow .btn-danger', function () {
        var parent = $(this).closest('.drow').find('.doc-row');
        var hourid = parent.data('id');
        if (hourid) {
            parent.html(parent.data('current-html'));
        } else {
            parent.remove();
        }
    });

    $(document).on('click', '.alldocumnetlist .drow .btn-primary', function () {
        var parent = $(this).closest('.drow').find('.doc-row');
        parent.find('.opentime').removeClass('is-invalid');
        parent.find('.invalid-feedback').remove();
        var opentime = parent.find('.opentime').val();
        if (!opentime) {
            parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
            parent.append($("<div class='invalid-feedback'>This field is required</div>"));
            return false;
        }
        var url = App.getUrl('renameAllDoc') || App.getUrl('siteUrl') + '/renamealldoc';
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

    // Rename Checklist Name For All Documents
    $(document).on('click', '.alldocumnetlist .renamechecklist', function () {
        var parent = $(this).closest('.drow').find('.personalchecklist-row');
        parent.data('current-html', parent.html());
        var opentime = parent.data('personalchecklistname');
        parent.empty().append(
            $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
            $('<button class="btn btn-personalprimary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
            $('<button class="btn btn-personaldanger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
        );
        return false;
    });

    $(document).on('click', '.alldocumnetlist .drow .btn-personaldanger', function () {
        var parent = $(this).closest('.drow').find('.personalchecklist-row');
        var hourid = parent.data('id');
        if (hourid) {
            parent.html(parent.data('current-html'));
        } else {
            parent.remove();
        }
    });

    $(document).on('click', '.alldocumnetlist .drow .btn-personalprimary', function () {
        var parent = $(this).closest('.drow').find('.personalchecklist-row');
        parent.find('.opentime').removeClass('is-invalid');
        parent.find('.invalid-feedback').remove();
        var opentime = parent.find('.opentime').val();
        if (!opentime) {
            parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
            parent.append($("<div class='invalid-feedback'>This field is required</div>"));
            return false;
        }
        var url = App.getUrl('renameChecklistDoc') || App.getUrl('siteUrl') + '/renamechecklistdoc';
        $.ajax({
            type: "POST",
            headers: { 'X-CSRF-TOKEN': App.getCsrf()},
            data: {"checklist": opentime, "id": parent.data('id')},
            url: url,
            success: function(result){
                var obj = typeof result === 'string' ? JSON.parse(result) : result;
                if (obj.status) {
                    parent.empty()
                        .data('id', obj.Id)
                        .data('name', opentime)
                        .append(
                            $('<span>').html(obj.checklist)
                        );
                    $('#grid_'+obj.Id).html(obj.checklist);
                } else {
                    parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
                    parent.append($('<div class="invalid-feedback">' + obj.message + '</div>'));
                }
            }
        });
        return false;
    });

    // ============================================================================
    // SELECT2 INITIALIZATION FOR APPLICATION FORMS
    // ============================================================================
    
    // Fix for Add Application modal - ensure dropdown appears above modal
    $(".applicationselect2").select2({
        dropdownParent: $(".add_appliation")
    });

    $(".partner_branchselect2").select2({
        dropdownParent: $(".add_appliation")
    });
    
    // Initialize product select2 if it exists
    $(".approductselect2").select2({
        dropdownParent: $(".add_appliation")
    });

    // ============================================================================
    // CHECKLIST FILE SELECTION HANDLER
    // ============================================================================
    
    $(document).on('change', 'input[name="checklistfile[]"]', function() {
        var checklistId = $(this).val();
        if ($(this).is(':checked')) {
            if (!selectedChecklists.includes(checklistId)) {
                selectedChecklists.push(checklistId);
            }
        } else {
            selectedChecklists = selectedChecklists.filter(id => id !== checklistId);
        }
    });

    // ============================================================================
    // ADDITIONAL HANDLERS
    // ============================================================================
    
    // NOTE: .removeitems handler has been moved to detail.blade.php inline script
    // to avoid duplication and ensure proper validation (at least one row required)

    // NOTE: Receipt handlers moved to receipts-and-payments module.

    

    // Receipt and payment handlers moved to receipts-and-payments module.

    // ============================================================================
    // FORM SUBMISSION HANDLERS
    // ============================================================================
    
    // Email form submission handler
    $('form[name="sendmail"]').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action') || App.getUrl('sendMail') || App.getUrl('siteUrl') + '/sendmsg';
        
        // Get summernote content if available
        var emailContent = '';
        if($("#emailmodal .summernote-simple").length && typeof $.fn.summernote !== 'undefined') {
            emailContent = $("#emailmodal .summernote-simple").summernote('code');
        } else {
            emailContent = $("#emailmodal .summernote-simple").val();
        }
        
        var formData = {
            _token: App.getCsrf(),
            to: $('.js-data-example-ajax').val(),
            cc: $('.js-data-example-ajaxcc').val(),
            subject: $('.selectedsubject').val(),
            message: emailContent,
            client_id: App.getPageConfig('clientId')
        };
        
        $('.popuploader').show();
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                $('.popuploader').hide();
                var res = typeof response === 'string' ? $.parseJSON(response) : response;
                if(res.status) {
                    alert(res.message || 'Email sent successfully');
                    $('#emailmodal').modal('hide');
                    if(typeof getallactivities === 'function') {
                        getallactivities();
                    }
                } else {
                    alert(res.message || 'Failed to send email');
                }
            },
            error: function() {
                $('.popuploader').hide();
                alert('An error occurred while sending the email');
            }
        });
        
        return false;
    });

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

