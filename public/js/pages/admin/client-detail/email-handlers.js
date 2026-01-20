/**
 * Admin Client Detail - Email Handlers Module
 * 
 * Handles email composition, template selection, and email sending functionality
 * 
 * Dependencies:
 *   - jQuery
 *   - Select2
 *   - Summernote (for rich text editing)
 *   - config.js (App object)
 */

'use strict';

// ============================================================================
// ASYNC WRAPPER - Wait for vendor libraries before initialization
// ============================================================================

(async function() {
    // Wait for vendor libraries to be ready
    if (typeof window.vendorLibsReady !== 'undefined') {
        console.log('[email-handlers.js] Waiting for vendorLibsReady promise...');
        await window.vendorLibsReady;
        console.log('[email-handlers.js] Vendor libraries ready!');
    } else {
        // Fallback: Poll for vendor libraries
        console.log('[email-handlers.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined' && 
                    typeof $.fn.select2 === 'function') {
                    console.log('[email-handlers.js] All vendor libraries detected!');
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

// ============================================================================
// EMAIL MODAL HANDLERS
// ============================================================================

jQuery(document).ready(function($){
    
    // ============================================================================
    // OPEN EMAIL MODAL
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

    // ============================================================================
    // OPEN SMS MODAL
    // ============================================================================
    
    $(document).on('click', '.sendmsg', function(){
        $('#sendmsgmodal').modal('show');
        var client_id = $(this).attr('data-id');
        $('#sendmsg_client_id').val(client_id);
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

    // ============================================================================
    // SELECT2 INITIALIZATION FOR EMAIL RECIPIENTS
    // ============================================================================
    
    // Initialize Select2 for email recipients (To field)
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

    // Initialize Select2 for email CC recipients
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
    // EMAIL FORM SUBMISSION HANDLER
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

    console.log('[email-handlers.js] Email handlers initialized');
});

})(); // End async wrapper

// ============================================================================
// HELPER FUNCTIONS (for Select2 templates)
// ============================================================================

// Note: formatRepo and formatRepoSelection are expected to be defined globally
// or in a common utilities file. If they don't exist, provide fallback implementations.

if (typeof window.formatRepo === 'undefined') {
    window.formatRepo = function(repo) {
        if (repo.loading) {
            return repo.text;
        }
        return repo.html || repo.text;
    };
}

if (typeof window.formatRepoSelection === 'undefined') {
    window.formatRepoSelection = function(repo) {
        return repo.text || repo.title;
    };
}
