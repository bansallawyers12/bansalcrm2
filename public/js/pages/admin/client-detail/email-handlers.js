/**
 * Admin Client Detail - Email Handlers Module
 * 
 * Handles email composition, template selection, and email sending functionality
 * 
 * Dependencies:
 *   - jQuery
 *   - Tom Select (via RecipientSelect / tomselect-init.js)
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
    }

    if (typeof window.waitForTomSelect === 'function') {
        await window.waitForTomSelect();
    }

    if (typeof window.waitForRecipientSelect === 'function') {
        await window.waitForRecipientSelect();
    } else if (typeof window.RecipientSelect === 'undefined') {
        // Fallback: Poll for vendor libraries + Tom Select + RecipientSelect
        console.log('[email-handlers.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined' &&
                    typeof TomSelect !== 'undefined' &&
                    typeof window.RecipientSelect !== 'undefined') {
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

    /**
     * Replace To field with fixed Select2 options (client id or raw email for college).
     */
    function setComposeToRecipients(entries) {
        RecipientSelect.setData('#emailmodal .js-data-example-ajax', entries, { dropdownParent: '#emailmodal' });
    }

    function getEmailRecipientInitOptions() {
        var rsUrl = App.getUrl('clientGetRecipients') || App.getUrl('siteUrl') + '/clients/get-recipients';
        return {
            url: rsUrl,
            dropdownParent: '#emailmodal',
            csrf: true
        };
    }

    function restoreEmailRecipientAjaxSelects() {
        if (!window.RecipientSelect) {
            return;
        }
        var opts = getEmailRecipientInitOptions();
        RecipientSelect.reinit('#emailmodal .js-data-example-ajax', opts);
        if ($('#emailmodal .js-data-example-ajaxccd').length) {
            RecipientSelect.reinit('#emailmodal .js-data-example-ajaxccd', opts);
        }
    }
    
    // Clear send_context when email modal is closed (e.g. user clicks Close without sending)
    $('#emailmodal').on('hidden.bs.modal', function() {
        $('#sendmail_send_context').val('');
        $('#sendmail_application_id').val('');
        $('#compose_email_category').val('');
        // Multi-file state is managed in detail.blade.php inline script
        if (typeof window.clearComposeAttachFiles === 'function') window.clearComposeAttachFiles();
        restoreEmailRecipientAjaxSelects();
    });

    // Compose labels: Sent is always applied server-side. Populate Add label dropdown and handle chip add/remove.
    (function initComposeLabels() {
        var selectedComposeLabelIds = [];
        var composeLabelsCache = [];

        function clearComposeLabelChips() {
            selectedComposeLabelIds = [];
            var chipsEl = document.getElementById('composeAdditionalLabelsChips');
            var containerEl = document.getElementById('composeLabelIdsContainer');
            if (chipsEl) chipsEl.innerHTML = '';
            if (containerEl) containerEl.innerHTML = '';
        }

        function renderComposeLabelChips() {
            var chipsEl = document.getElementById('composeAdditionalLabelsChips');
            var containerEl = document.getElementById('composeLabelIdsContainer');
            if (!chipsEl || !containerEl) return;
            chipsEl.innerHTML = '';
            containerEl.innerHTML = '';
            selectedComposeLabelIds.forEach(function(labelId) {
                var label = composeLabelsCache.find(function(l) { return l.id == labelId; });
                if (!label) return;
                var chip = document.createElement('span');
                chip.className = 'compose-label-chip';
                chip.style.backgroundColor = (label.color || '#3B82F6') + '20';
                chip.style.borderColor = label.color || '#3B82F6';
                chip.style.color = label.color || '#3B82F6';
                chip.innerHTML = '<i class="' + (label.icon || 'fas fa-tag') + '"></i><span>' + (label.name || '') + '</span><i class="fas fa-times chip-remove" data-label-id="' + label.id + '"></i>';
                chip.querySelector('.chip-remove').addEventListener('click', function() {
                    selectedComposeLabelIds = selectedComposeLabelIds.filter(function(id) { return id != label.id; });
                    renderComposeLabelChips();
                });
                chipsEl.appendChild(chip);
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'label_ids[]';
                input.value = label.id;
                containerEl.appendChild(input);
            });
        }

        function populateComposeLabelDropdown(labels) {
            composeLabelsCache = labels || [];
            var dropdown = document.getElementById('composeLabelDropdown');
            if (!dropdown) return;
            dropdown.innerHTML = '';
            var sorted = labels.filter(function(l) { return (l.name || '').toLowerCase() !== 'sent'; });
            sorted.sort(function(a, b) {
                if (a.type === 'system' && b.type !== 'system') return -1;
                if (a.type !== 'system' && b.type === 'system') return 1;
                return (a.name || '').localeCompare(b.name || '');
            });
            if (sorted.length === 0) {
                dropdown.innerHTML = '<li><span class="dropdown-item text-muted">No additional labels</span></li>';
                return;
            }
            sorted.forEach(function(label) {
                var item = document.createElement('li');
                var link = document.createElement('a');
                link.className = 'dropdown-item';
                link.href = '#';
                link.innerHTML = '<span class="label-color-dot" style="background:' + (label.color || '#3B82F6') + '"></span>' + (label.name || '');
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (selectedComposeLabelIds.indexOf(label.id) === -1) {
                        selectedComposeLabelIds.push(label.id);
                        renderComposeLabelChips();
                    }
                });
                item.appendChild(link);
                dropdown.appendChild(item);
            });
        }

        $('#emailmodal').on('shown.bs.modal', function() {
            if (typeof window.refreshEmailFromSenders === 'function') {
                window.refreshEmailFromSenders();
            }
            clearComposeLabelChips();
            fetch('/email-v2/labels', {
                method: 'GET',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': ($('meta[name="csrf-token"]').attr('content') || '') }
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (data.success && Array.isArray(data.labels) && data.labels.length) {
                    populateComposeLabelDropdown(data.labels);
                } else {
                    document.getElementById('composeLabelDropdown').innerHTML = '<li><span class="dropdown-item text-muted">No labels available</span></li>';
                }
            }).catch(function(e) { console.warn('Could not load labels for compose:', e); });
        });

        $('form[name="sendmail"]').on('reset', function() {
            clearComposeLabelChips();
        });
    })();
    
    // ============================================================================
    // OPEN EMAIL MODAL
    // ============================================================================
    
    $(document).on('click', '.clientemail', function(){
        $('#sendmail_send_context').val(''); // Clear context when opening from generic email link
        $('#sendmail_application_id').val('');
        $('#compose_email_category').val('');
        $('#emailmodal').modal('show');
        var id = $(this).attr('data-id');
        var email = $(this).attr('data-email');
        var name = $(this).attr('data-name');
        var status = 'Client';
        var safeName = $('<span>').text(name || '').html();
        var safeEmail = $('<span>').text(email || '').html();
        var data = [{
            id: id,
            text: name,
            html:  "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +
                "<div  class='ag-flex ag-align-start'>" +
                    "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'>"+safeName+"</span>&nbsp;</div>" +
                    "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'>"+safeEmail+"</small ></div>" +
                "</div>" +
            "</div>" +
            "<div class='ag-flex ag-flex-column ag-align-end'>" +
                "<span class='badge bg-warning text-dark select2-result-repository__statistics'>"+ status +
                "</span>" +
            "</div>" +
            "</div>",
            title: name
        }];
        setComposeToRecipients(data);
    });

    // Applications tab: compose to college (category + application_id; send_context avoids "email reminder" logging)
    $(document).on('click', '.application-compose-email', function () {
        var $btn = $(this);
        if ($btn.is(':disabled')) {
            return;
        }
        var appId = $btn.attr('data-application-id') || '';
        var cEmail = ($btn.attr('data-college-email') || '').trim();
        var cName = ($btn.attr('data-college-name') || 'College').trim();
        if (!cEmail) {
            alert('No college email on file. Add branch or partner email in the partner profile.');
            return;
        }
        $('#sendmail_application_id').val(appId);
        $('#sendmail_send_context').val('application_compose');
        $('#compose_email_category').val('college');
        var safeCollegeName = $('<span>').text(cName).html();
        var safeCollegeEmail = $('<span>').text(cEmail).html();
        var collegeEntry = [{
            id: cEmail,
            text: cName,
            html: "<div class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +
                "<div class='ag-flex ag-align-start'>" +
                "<div class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span class='select2-result-repository__title text-semi-bold'>" + safeCollegeName + "</span>&nbsp;</div>" +
                "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'>" + safeCollegeEmail + "</small></div></div></div>" +
                "<div class='ag-flex ag-flex-column ag-align-end'>" +
                "<span class='badge bg-primary select2-result-repository__statistics'>College</span>" +
                "</div></div>",
            title: cName
        }];
        setComposeToRecipients(collegeEntry);
        $('#emailmodal').modal('show');
    });

    // ============================================================================
    // OPEN SMS MODAL
    // ============================================================================
    
    $(document).on('click', '.sendmsg', function(){
        $('#sendmsgmodal').modal('show');
        var client_id = $(this).attr('data-id');
        $('#sendmsg_client_id').val(client_id);
        $('#sendmsg_application_id').val(''); // clear so normal SMS is not recorded as reminder
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

        var client_dob = $(this).data('clientdob') || '';

        var v = $(this).val();
        var url = App.getUrl('getTemplates') || App.getUrl('siteUrl') + '/get-templates';
        $.ajax({
            url: url,
            type:'GET',
            datatype:'json',
            data:{id:v},
            success: function(response){
                var res = typeof response === 'string' ? JSON.parse(response) : response;

                var subjct_message = res.subject.replace('{Client First Name}', client_firstname || '').replace('{client reference}', client_reference_number || '').replace('{DOB}', client_dob);
                $('.selectedsubject').val(subjct_message);
      
                if($("#emailmodal .tinymce-simple").length && typeof TinyMCEHelpers !== 'undefined') {
                    TinyMCEHelpers.resetBySelector("#emailmodal .tinymce-simple");
                }
      
                var subjct_description = res.description
                    .replace('{Client First Name}', client_firstname || '')
                    .replace('{Company Name}', company_name)
                    .replace('{Visa Valid Upto}', visa_valid_upto)
                    .replace('{Client Assignee Name}', clientassignee_name)
                    .replace('{client reference}', client_reference_number || '')
                    .replace('{DOB}', client_dob);
      
                if($("#emailmodal .tinymce-simple").length && typeof TinyMCEHelpers !== 'undefined') {
                    TinyMCEHelpers.setContentBySelector("#emailmodal .tinymce-simple", subjct_description);
                }
                $("#emailmodal .tinymce-simple").val(subjct_description);
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
                if($("#applicationemailmodal .tinymce-simple").length && typeof TinyMCEHelpers !== 'undefined') {
                    TinyMCEHelpers.resetBySelector("#applicationemailmodal .tinymce-simple");
                    TinyMCEHelpers.setContentBySelector("#applicationemailmodal .tinymce-simple", res.description);
                }
                $("#applicationemailmodal .tinymce-simple").val(res.description);
            }
        });
    });

    // ============================================================================
    // SELECT2 INITIALIZATION FOR EMAIL RECIPIENTS
    // ============================================================================
    
    // When opening from Send checklist / Email reminder (?open_checklist_email=1 or ?open_email_reminder=1),
    // the blade inline script pre-populates the To field. Skip re-init here to avoid overwriting it.
    var urlParams = new URLSearchParams(window.location.search);
    var openedFromChecklist = urlParams.get('open_checklist_email') === '1' || urlParams.get('open_email_reminder') === '1';
    
    if (!openedFromChecklist && window.RecipientSelect) {
        var opts = getEmailRecipientInitOptions();
        RecipientSelect.init('#emailmodal .js-data-example-ajax', opts);
        if ($('#emailmodal .js-data-example-ajaxccd').length) {
            RecipientSelect.init('#emailmodal .js-data-example-ajaxccd', opts);
        }
    }

    // ============================================================================
    // EMAIL FORM SUBMISSION HANDLER
    // ============================================================================
    
    // Email form submission handler
    $('form[name="sendmail"]').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action') || App.getUrl('sendMail') || App.getUrl('siteUrl') + '/sendmail';
        
        // Get TinyMCE content if available
        var emailContent = '';
        if($("#emailmodal .tinymce-simple").length && typeof TinyMCEHelpers !== 'undefined') {
            emailContent = TinyMCEHelpers.getContentBySelector("#emailmodal .tinymce-simple");
        } else {
            emailContent = $("#emailmodal .tinymce-simple").val();
        }
        
        // Validate required fields before submission
        var emailFrom = $('select[name="email_from"]').val();
        var emailTo = window.RecipientSelect
            ? RecipientSelect.getValue('#emailmodal .js-data-example-ajax')
            : $('#emailmodal .js-data-example-ajax').val();
        var subject = $('.selectedsubject').val();
        
        if (!emailFrom || emailFrom === '') {
            alert('Please select a From email address');
            $('.popuploader').hide();
            return false;
        }
        
        if (!emailTo || emailTo.length === 0) {
            alert('Please select at least one recipient');
            $('.popuploader').hide();
            return false;
        }
        
        if (!subject || subject.trim() === '') {
            alert('Please enter email subject');
            $('.popuploader').hide();
            return false;
        }
        
        if (!emailContent || emailContent.trim() === '') {
            alert('Please enter email message');
            $('.popuploader').hide();
            return false;
        }
        
        // Create FormData to handle file uploads
        var formData = new FormData(form[0]);
        
        // Override/ensure message field has TinyMCE content
        formData.set('message', emailContent);

        // Inject accumulated multi-file attachments (state managed in detail.blade.php inline script)
        var attachedFiles = (typeof window.getComposeAttachedFiles === 'function') ? window.getComposeAttachedFiles() : [];
        if (attachedFiles.length > 0) {
            formData.delete('attach[]');
            attachedFiles.forEach(function(file) {
                formData.append('attach[]', file, file.name);
            });
        }
        
        $('.popuploader').show();
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                $('.popuploader').hide();
                var res = typeof response === 'string' ? $.parseJSON(response) : response;
                if(res.status) {
                    alert(res.message || 'Email sent successfully');
                    $('#sendmail_send_context').val(''); // Clear context after successful send
                    $('#sendmail_application_id').val('');
                    $('#compose_email_category').val('');
                    $('#emailmodal').modal('hide');
                    // Reset form and attachments
                    form[0].reset();
                    if (typeof window.clearComposeAttachFiles === 'function') window.clearComposeAttachFiles();
                    if($("#emailmodal .tinymce-simple").length && typeof TinyMCEHelpers !== 'undefined') {
                        TinyMCEHelpers.resetBySelector("#emailmodal .tinymce-simple");
                    }
                    // Refresh activity log
                    if(typeof getallactivities === 'function') {
                        getallactivities();
                    }
                    // Switch to Emails tab, set Sent folder and Client/College sub-tab, then refresh
                    var emailV2Tab = document.getElementById('email-v2-tab');
                    if (emailV2Tab && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                        try {
                            bootstrap.Tab.getOrCreateInstance(emailV2Tab).show();
                            if (typeof window.setEmailMailTypeV2 === 'function') {
                                window.setEmailMailTypeV2('sent');
                            }
                            // When sent from college address, select College sub-tab; otherwise Client
                            if (typeof window.setEmailCategoryV2 === 'function') {
                                window.setEmailCategoryV2(res.email_category || 'client');
                            }
                            if (typeof window.loadEmailsV2 === 'function') {
                                setTimeout(window.loadEmailsV2, 150);
                            }
                        } catch (e) { /* tab may not exist on non-client/partner pages */ }
                    }
                } else {
                    alert(res.message || 'Failed to send email');
                }
            },
            error: function(xhr, status, error) {
                $('.popuploader').hide();
                console.error('Email send error:', xhr.responseText);
                var errorMessage = 'An error occurred while sending the email';
                
                // Try to parse error response
                try {
                    var errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    // If response is not JSON, check if it's HTML error page
                    if (xhr.responseText && xhr.responseText.includes('<html')) {
                        errorMessage = 'Server error occurred. Please check server logs.';
                    } else if (xhr.responseText) {
                        errorMessage = xhr.responseText;
                    }
                }
                
                alert(errorMessage);
            }
        });
        
        return false;
    });

    console.log('[email-handlers.js] Email handlers initialized');
});

})(); // End async wrapper
