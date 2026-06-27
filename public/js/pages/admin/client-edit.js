/**
 * Admin Client Edit Page - Page-Specific JavaScript
 * 
 * This file contains JavaScript code specific to the Admin Client Edit page.
 * Common/shared functionality should be in /js/common/ files.
 * 
 * Dependencies (loaded before this file):
 *   - config.js
 *   - ajax-helpers.js
 *   - utilities.js
 *   - ui-components.js
 *   - google-maps.js
 */

'use strict';

// ============================================================================
// ASYNC WRAPPER - Wait for vendor libraries before initialization
// ============================================================================

(async function() {
    // Wait for vendor libraries to be ready
    if (typeof window.vendorLibsReady !== 'undefined') {
        console.log('[client-edit.js] Waiting for vendorLibsReady promise...');
        await window.vendorLibsReady;
        console.log('[client-edit.js] Vendor libraries ready!');
    } else {
        // Fallback: Poll for vendor libraries
        console.log('[client-edit.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined' &&
                    typeof window.initTomSelect === 'function' &&
                    typeof flatpickr !== 'undefined') {
                    console.log('[client-edit.js] Required vendor libraries detected!');
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

    function toastMsg(message, type) {
        if (typeof window.toastMsg === 'function') {
            window.toastMsg(message, type);
        } else if (message) {
            alert(message);
        }
    }

// ============================================================================
// INITIALIZATION
// ============================================================================

jQuery(document).ready(function($){
    /**
     * Sync contact_type[] and client_phone[] from .editclientphone data attributes to hidden inputs before form submit.
     * Ensures validation never fails due to stale/empty arrays.
     */
    function syncPhoneContactArrays() {
        $('.clientphonedata .compact-contact-item').each(function() {
            var $item = $(this);
            var $edit = $item.find('.editclientphone');
            if ($edit.length) {
                var type = $edit.data('type') || $item.find('input[name="contact_type[]"]').val();
                var country = $edit.data('country') || $item.find('input[name="client_country_code[]"]').val();
                var phone = $edit.data('phone') || $item.find('input[name="client_phone[]"]').val();
                $item.find('input[name="contact_type[]"]').val(type);
                $item.find('input[name="client_country_code[]"]').val(country);
                $item.find('input[name="client_phone[]"]').val(phone);
            }
        });
    }

    function autoSaveClientEditForm(successMessage) {
        if (window.__clientEditAutoSaveInProgress) {
            return;
        }
        window.__clientEditAutoSaveInProgress = true;

        syncPhoneContactArrays();

        var $form = $('form[name="edit-clients"]');
        if ($form.length > 0) {
            var formEl = $form[0];
            var formData = new FormData(formEl);

            $.ajax({
                url: $form.attr('action'),
                method: ($form.attr('method') || 'POST').toUpperCase(),
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': App.getCsrf()
                }
            }).done(function() {
                if (successMessage) {
                    toastMsg(successMessage, 'success');
                }
            }).fail(function(xhr) {
                var errorMsg = 'Failed to save changes.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                toastMsg(errorMsg, 'error');
            }).always(function() {
                window.__clientEditAutoSaveInProgress = false;
            });
        } else {
            window.__clientEditAutoSaveInProgress = false;
        }
    }
  
    // ============================================================================
    // PHONE MANAGEMENT
    // ============================================================================

    // Sync phone arrays before form submit (fixes 422 when Save Changes is clicked)
    $('form[name="edit-clients"]').on('submit', function() {
        syncPhoneContactArrays();
    });

    var itag_phone = $('.clientphonedata .compact-contact-item').length;
    var clientphonedata = {};

    // Add client phone
    $(document).delegate('.openclientphonenew','click', function(){
        $('#clientPhoneModalLabel').html('Add New Client Phone');
        $('.saveclientphone').show();
        $('#update_clientphone').hide();
        // Clear errors
        $('.client_phone_error').html('');
        $('.contact_type_error').html('');
        $('input[name="client_phone"]').parent().removeClass('error');
        $('#contact_type').parent().removeClass('error');
        $('.country_code').removeClass('error');
        $('#clientphoneform')[0].reset();
        // Disable Personal option if one already exists (only one Personal/primary per client)
        var hasPersonal = $('.clientphonedata .contact-type-tag').filter(function() { return $(this).text().trim() === 'Personal'; }).length > 0;
        $('#contact_type option[value="Personal"]').prop('disabled', hasPersonal);
        if (hasPersonal) { $('#contact_type').val(''); }
        $('.addclientphone').modal('show');
    });

    $('.addclientphone').on('shown.bs.modal', function () {
        $('.country_code').removeClass('error');
    });

    // Fix aria-hidden focus warning: move focus out of modal before it hides
    $('.addclientphone').on('hide.bs.modal', function () {
        var activeEl = document.activeElement;
        if (activeEl && $(activeEl).closest('.addclientphone').length) {
            var $target = $('.openclientphonenew, .editclientphone, button[onclick*="edit-clients"]').filter(':visible').first();
            if ($target.length) $target[0].focus();
        }
    });
  
    // Save client phone
    $(document).delegate('.saveclientphone','click', function() {
        // Clear previous errors
        $('.client_phone_error').html('');
        $('.contact_type_error').html('');
        $('input[name="client_phone"]').parent().removeClass('error');
        $('#contact_type').parent().removeClass('error');
        $('.country_code').removeClass('error');
        
        // Get form values
        var contact_type = $('#contact_type').val();
        var client_phone = $('input[name="client_phone"]').val();
        
        // Get country code from select input
        var country_code = $('select[name="client_country_code"]').val() || '';
        
        // Check if phone already exists
        if ($('table#metatag_table').find('#metatag2_'+itag_phone).length > 0) {
            // Phone already exists, skip
            return;
        }
        
        // Validate form fields
        var flag = false;
        
        // Validate contact type (required field)
        if(contact_type == '' || contact_type == null){
            $('.contact_type_error').html('The Contact Type field is required.');
            $('#contact_type').parent().addClass('error');
            flag = true;
        }
        
        // Validate phone number
        if(client_phone == ''){
            $('.client_phone_error').html('The Phone field is required.');
            $('input[name="client_phone"]').parent().addClass('error');
            flag = true;
        }
        
        // Validate country code
        if(country_code == ''){
            $('.client_phone_error').html('Please select a valid country code.');
            $('.country_code').addClass('error');
            flag = true;
        }
        // Personal (primary) phone type can only be used once
        if (!flag && contact_type === 'Personal') {
            var hasPersonal = $('.clientphonedata .contact-type-tag').filter(function() { return $(this).text().trim() === 'Personal'; }).length > 0;
            if (hasPersonal) {
                $('.contact_type_error').html("'Personal' phone type can only be used once. Please choose another type (e.g. Office, Work).");
                $('#contact_type').parent().addClass('error');
                flag = true;
            }
        }

        if(!flag){
            // Store data for reference
            clientphonedata[itag_phone] = {
                "contact_type": contact_type,
                "country_code": country_code,
                "phone": client_phone
            };

            // New compact design HTML
            var html = '<div class="compact-contact-item" id="metatag2_'+itag_phone+'">';
            html += '<span class="contact-type-tag">'+contact_type+'</span>';
            html += '<span class="contact-phone">'+country_code+' '+client_phone+'</span>';
            html += '<div class="contact-actions">';
            
            if(contact_type != 'Personal') {
                html += '<a href="javascript:;" dataid="'+itag_phone+'" class="deletecontact btn-delete">' + crmIcon('trash') + '</a>';
            }
            
            html += '</div>';
            
            // Hidden fields
            html += '<input type="hidden" name="contact_type[]" value="'+contact_type+'">';
            html += '<input type="hidden" name="client_country_code[]" value="'+country_code+'">';
            html += '<input type="hidden" name="client_phone[]" value="'+client_phone+'">';
            html += '<input type="hidden" name="clientphoneid[]" value="">';
            html += '</div>';

            $('.clientphonedata').append(html);
            $('#clientphoneform')[0].reset();
            $('.addclientphone').modal('hide');
            itag_phone++;
        }
    });

    $(document).delegate('.deletecontact','click', function(){
        var v = $(this).attr('dataid');
        var contactid = $(this).attr('contactid');
        // Show confirmation message
        crmConfirm('Are you sure you want to delete this contact?').then(function (ok) {
            if (!ok) return;
            $('#metatag2_'+v).remove();
            if (typeof contactid !== 'undefined' && contactid !== false) {
                $('.removesids_contact').append('<input type="hidden" name="rem_phone[]" value="'+contactid+'">');
            }
        });
    });

    // Edit client phone
    $(document).delegate('.editclientphone','click', function(){
        $('#clientPhoneModalLabel').html('Edit Phone Number');
        $('.saveclientphone').hide();
        $('#update_clientphone').show();
        
        // Get data from clicked element
        var phone_id = $(this).data('id');
        var phone_index = $(this).data('index');
        var contact_type = $(this).data('type');
        var country_code = $(this).data('country');
        var phone_number = $(this).data('phone');
        
        // Store edit mode data
        $('#edit_phone_mode').val('1');
        $('#edit_phone_id').val(phone_id);
        $('#edit_phone_index').val(phone_index);
        
        // Clear errors
        $('.client_phone_error').html('');
        $('.contact_type_error').html('');
        $('input[name="client_phone"]').parent().removeClass('error');
        $('#contact_type').parent().removeClass('error');
        
        // Populate form
        $('#contact_type').val(contact_type);
        $('input[name="client_phone"]').val(phone_number);
        // Disable Personal if another phone already has it (excluding the one being edited)
        var $item = $('#metatag2_' + phone_index);
        if ($item.length === 0 && phone_id) {
            $item = $('.clientphonedata input[name="clientphoneid[]"][value="' + phone_id + '"]').closest('.compact-contact-item');
        }
        var otherHasPersonal = $('.clientphonedata .compact-contact-item').not($item).find('.contact-type-tag').filter(function() { return $(this).text().trim() === 'Personal'; }).length > 0;
        $('#contact_type option[value="Personal"]').prop('disabled', otherHasPersonal);
        
        // Set country code in select
        var $countrySelect = $('select[name="client_country_code"]');
        if ($countrySelect.length) {
            if (country_code && $countrySelect.find('option[value="' + country_code + '"]').length === 0) {
                $countrySelect.append('<option value="' + country_code + '" selected>' + country_code + '</option>');
            }
            $countrySelect.val(country_code || '');
        }
        
        $('.addclientphone').modal('show');
    });

    // Update client phone
    $(document).delegate('#update_clientphone','click', function() {
        // Clear previous errors
        $('.client_phone_error').html('');
        $('.contact_type_error').html('');
        $('input[name="client_phone"]').parent().removeClass('error');
        $('#contact_type').parent().removeClass('error');
        $('.country_code').removeClass('error');
        
        // Get form values
        var contact_type = $('#contact_type').val();
        var client_phone = $('input[name="client_phone"]').val();
        var phone_index = $('#edit_phone_index').val();
        var phone_id = $('#edit_phone_id').val();
        
        // Get country code from select input
        var country_code = $('select[name="client_country_code"]').val() || '';
        
        console.log('Update Phone - Index:', phone_index);
        console.log('Update Phone - ID:', phone_id);
        console.log('Update Phone - Type:', contact_type);
        console.log('Update Phone - Country:', country_code);
        console.log('Update Phone - Number:', client_phone);
        
        // Validate
        var flag = false;
        if(contact_type == '' || contact_type == null){
            $('.contact_type_error').html('The Contact Type field is required.');
            $('#contact_type').parent().addClass('error');
            flag = true;
        }
        if(client_phone == ''){
            $('.client_phone_error').html('The Phone field is required.');
            $('input[name="client_phone"]').parent().addClass('error');
            flag = true;
        }
        if(country_code == ''){
            $('.client_phone_error').html('Please select a valid country code.');
            $('.country_code').addClass('error');
            flag = true;
        }
        // Personal (primary) phone type can only be used once (exclude the item being edited)
        if (!flag && contact_type === 'Personal') {
            var $phoneItem = null;
            if (phone_id) {
                $phoneItem = $('.clientphonedata input[name="clientphoneid[]"][value="' + phone_id + '"]').closest('.compact-contact-item');
            }
            if (!$phoneItem || $phoneItem.length === 0) {
                $phoneItem = $('#metatag2_' + phone_index);
            }
            var otherHasPersonal = $('.clientphonedata .compact-contact-item').not($phoneItem).find('.contact-type-tag').filter(function() { return $(this).text().trim() === 'Personal'; }).length > 0;
            if (otherHasPersonal) {
                $('.contact_type_error').html("'Personal' phone type can only be used once. Please choose another type (e.g. Office, Work).");
                $('#contact_type').parent().addClass('error');
                flag = true;
            }
        }

        if(!flag){
            // Find the phone item
            var $phoneItem = null;
            if (phone_id) {
                $phoneItem = $('.clientphonedata input[name="clientphoneid[]"][value="' + phone_id + '"]').closest('.compact-contact-item');
            }
            if (!$phoneItem || $phoneItem.length === 0) {
                $phoneItem = $('#metatag2_' + phone_index);
            }
            
            if($phoneItem.length > 0) {
                console.log('Found phone item, updating...');
                
                // Update the display
                $phoneItem.find('.contact-type-tag').text(contact_type);
                $phoneItem.find('.contact-phone').text(country_code + ' ' + client_phone);
                
                // Update hidden fields - CRITICAL for form submission
                $phoneItem.find('input[name="contact_type[]"]').val(contact_type);
                $phoneItem.find('input[name="client_country_code[]"]').val(country_code);
                $phoneItem.find('input[name="client_phone[]"]').val(client_phone);
                
                // Update data attributes (use .data() to sync jQuery cache so syncPhoneContactArrays reads correct values)
                $phoneItem.find('.editclientphone').data('type', contact_type).data('country', country_code).data('phone', client_phone);
                
                console.log('Updated hidden input values:', {
                    type: $phoneItem.find('input[name="contact_type[]"]').val(),
                    country: $phoneItem.find('input[name="client_country_code[]"]').val(),
                    phone: $phoneItem.find('input[name="client_phone[]"]').val()
                });
                
                // Clear form and close modal
                $('#clientphoneform')[0].reset();
                $('#edit_phone_mode').val('0');
                $('#edit_phone_id').val('');
                $('#edit_phone_index').val('');
                $('.addclientphone').modal('hide');
                
                autoSaveClientEditForm('Phone number updated successfully.');
            } else {
                console.error('Could not find phone item with index:', phone_index);
                toastMsg('Error: Could not find the phone item to update.', 'error');
            }
        }
    });

    // ============================================================================
    // EMAIL MANAGEMENT
    // ============================================================================
    
    var itag_email = 0;
    
    $(document).delegate('.openclientemailnew','click', function(){
        $('#clientEmailModalLabel').html('Add New Email');
        $('.saveclientemail').show();
        $('#update_clientemail').hide();
        $('#edit_email_mode').val('0');
        $('#edit_email_id').val('');
        $('#clientemailform')[0].reset();
        // Disable Personal option if one already exists (only one Personal per client)
        var hasPersonal = $('.clientemaildata .contact-type-tag').filter(function() { return $(this).text().trim() === 'Personal'; }).length > 0;
        $('#email_type_modal option[value="Personal"]').prop('disabled', hasPersonal);
        if (hasPersonal) { $('#email_type_modal').val(''); }
        $('.addclientemail').modal('show');
    });

    // Save client email (append new, do not replace)
    $(document).delegate('.saveclientemail','click', function(){
        var client_email = $('input[name="client_email"]').val();
        var email_type = $('select[name="email_type_modal"]').val();
        
        $('.client_email_error').html('');
        $('input[name="client_email"]').parent().removeClass('error');
        
        var flag = false;
        if(client_email == ''){
            $('.client_email_error').html('The Email field is required.');
            $('input[name="client_email"]').parent().addClass('error');
            flag = true;
        }
        if(email_type == ''){
            toastMsg('Please select email type.', 'warning');
            flag = true;
        }
        // Personal email type can only be used once
        if (!flag && email_type === 'Personal') {
            var hasPersonal = $('.clientemaildata .contact-type-tag').filter(function() { return $(this).text().trim() === 'Personal'; }).length > 0;
            if (hasPersonal) {
                toastMsg("'Personal' email type can only be used once. Please choose another type (e.g. Work, Business).", 'warning');
                flag = true;
            }
        }

        if(!flag){
            var emailDomId = 'email_new_' + (new Date().getTime());
            var escapedEmail = $('<div/>').text(client_email).html();
            var escapedType = $('<div/>').text(email_type).html();
            
            var html = '<div class="compact-contact-item email-item" id="'+emailDomId+'">';
            html += '<span class="contact-type-tag">'+escapedType+'</span>';
            html += '<a href="javascript:;" class="set-email-primary me-1" title="Set as primary" data-email-id="'+emailDomId+'">' + crmIcon('star', 'regular', { class: 'text-muted' }) + '</a>';
            html += '<span class="contact-email">'+escapedEmail+'</span>';
            html += '<div class="contact-actions">';
            html += '<a href="javascript:;" class="editclientemail btn-edit" data-email-id="'+emailDomId+'" data-type="'+escapedType+'" data-email="'+escapedEmail+'" title="Edit">' + crmIcon('edit') + '</a>';
            html += '<button type="button" class="btn-verify manual_email_phone_verified" data-fname="' + (App.getPageConfig('clientFirstName') || '') + '" data-email="'+escapedEmail+'" data-clientid="' + (App.getPageConfig('clientId') || '') + '" title="Verify">' + crmIcon('paper-plane') + '</button>';
            html += '<a href="javascript:;" class="deleteemail btn-delete" data-email-id="'+emailDomId+'" title="Delete">' + crmIcon('trash') + '</a>';
            html += '</div>';
            html += '<input type="hidden" name="email[]" value="'+escapedEmail+'">';
            html += '<input type="hidden" name="email_type[]" value="'+escapedType+'">';
            html += '<input type="hidden" name="clientemailid[]" value="">';
            html += '</div>';

            $('.clientemaildata').append(html);
            $('#clientemailform')[0].reset();
            $('.addclientemail').modal('hide');
            itag_email++;
        }
    });

    // Set as primary (move email to first position - first = primary for admins table)
    $(document).delegate('.set-email-primary','click', function(){
        var emailId = $(this).attr('data-email-id');
        var $item = $('#' + emailId);
        var $container = $('.clientemaildata');
        if ($item.length && $item.index() !== 0) {
            $item.prependTo($container);
            // Refresh badges: first = primary (filled star), others = set-primary (outline star)
            $container.find('.email-item').each(function(idx){
                var $el = $(this);
                var id = $el.attr('id');
                var $existing = $el.find('.set-email-primary, .primary-badge');
                if (idx === 0) {
                    $existing.replaceWith('<span class="primary-badge me-1" title="Primary email (stored in system)">' + crmIcon('star', { class: 'text-warning' }) + '</span>');
                } else {
                    $existing.replaceWith('<a href="javascript:;" class="set-email-primary me-1" title="Set as primary" data-email-id="'+id+'">' + crmIcon('star', 'regular', { class: 'text-muted' }) + '</a>');
                }
            });
        }
    });

    // Delete email
    $(document).delegate('.deleteemail','click', function(){
        var emailId = $(this).attr('data-email-id');
        var count = $('.clientemaildata .compact-contact-item').length;
        if (count <= 1) {
            toastMsg('At least one email address is required.', 'warning');
            return;
        }
        crmConfirm('Are you sure you want to delete this email?').then(function (ok) { if (!ok) return;
            $('#' + emailId).remove();
        }
    });

    // Edit client email
    $(document).delegate('.editclientemail','click', function(){
        $('#clientEmailModalLabel').html('Edit Email Address');
        $('.saveclientemail').hide();
        $('#update_clientemail').show();
        
        var email_id = $(this).data('email-id');
        var email_type = $(this).data('type');
        var email_address = $(this).data('email');
        var $item = $('#' + email_id);
        
        $('#edit_email_mode').val('1');
        $('#edit_email_id').val(email_id);
        
        $('.client_email_error').html('');
        $('input[name="client_email"]').parent().removeClass('error');
        
        $('#email_type_modal').val(email_type);
        $('input[name="client_email"]').val(email_address);
        // Disable Personal if another email already has it (excluding the one being edited)
        var otherHasPersonal = $('.clientemaildata .compact-contact-item').not($item).find('.contact-type-tag').filter(function() { return $(this).text().trim() === 'Personal'; }).length > 0;
        $('#email_type_modal option[value="Personal"]').prop('disabled', otherHasPersonal);
        
        $('.addclientemail').modal('show');
    });

    // Update client email
    $(document).delegate('#update_clientemail','click', function(){
        $('.client_email_error').html('');
        $('input[name="client_email"]').parent().removeClass('error');
        
        var client_email = $('input[name="client_email"]').val();
        var email_type = $('select[name="email_type_modal"]').val();
        var email_id = $('#edit_email_id').val();
        
        var flag = false;
        if(client_email == ''){
            $('.client_email_error').html('The Email field is required.');
            $('input[name="client_email"]').parent().addClass('error');
            flag = true;
        }
        if(email_type == ''){
            toastMsg('Please select email type.', 'warning');
            flag = true;
        }
        // Personal email type can only be used once (exclude the item being edited)
        if (!flag && email_type === 'Personal') {
            var $item = $('#' + email_id);
            var otherHasPersonal = $('.clientemaildata .compact-contact-item').not($item).find('.contact-type-tag').filter(function() { return $(this).text().trim() === 'Personal'; }).length > 0;
            if (otherHasPersonal) {
                toastMsg("'Personal' email type can only be used once. Please choose another type (e.g. Work, Business).", 'warning');
                flag = true;
            }
        }

        if(!flag){
            var $emailItem = $('#' + email_id);
            
            if($emailItem.length > 0){
                var escapedEmail = $('<div/>').text(client_email).html();
                var escapedType = $('<div/>').text(email_type).html();
                
                $emailItem.find('.contact-type-tag').text(email_type);
                $emailItem.find('.contact-email').text(client_email);
                $emailItem.find('input[name="email[]"]').val(client_email);
                $emailItem.find('input[name="email_type[]"]').val(email_type);
                $emailItem.find('.editclientemail').attr('data-type', email_type).attr('data-email', client_email);
                $emailItem.find('.manual_email_phone_verified').attr('data-email', client_email);
                
                $('#clientemailform')[0].reset();
                $('#edit_email_mode').val('0');
                $('#edit_email_id').val('');
                $('.saveclientemail').show();
                $('#update_clientemail').hide();
                $('.addclientemail').modal('hide');
            } else {
                toastMsg('Error: Could not find the email item to update.', 'error');
            }
        }
    });

    // ============================================================================
    // SOURCE FIELD HANDLING
    // ============================================================================
    
    var source_val = App.getPageConfig('source') || '';
    if (source_val !== '') {
        syncSubagentVisibility();
    } else {
        $('.is_subagent').css('display', 'none');
    }
  
    $('.filter_btn').on('click', function(){
        $('.filter_panel').slideToggle();
    });

    // ============================================================================
    // SERVICE TAKEN MANAGEMENT
    // ============================================================================

    // Add button popup
    $(document).delegate('.serviceTaken','click', function(){
        $('#entity_type').val("add");
        $('#mig_ref_no').val("");
        $('#mig_service').val("");
        $('#mig_notes').val("");
        $('#edu_course').val("");
        $('#edu_college').val("");
        $('#edu_service_start_date').val("");
        $('#edu_notes').val("");
        $('#serviceTaken').modal('show');
        $('#createservicetaken_btn').text("Save");
    });

    // Edit button click and form submit
    $(document).delegate('.service_taken_edit','click', function(){
        $('#createservicetaken_btn').text("Update");
        var sel_service_taken_id = $(this).attr('id');
        $('#entity_id').val(sel_service_taken_id);
        
        AjaxHelper.post(
            App.getUrl('getServiceTaken') || App.getUrl('siteUrl') + '/client/getservicetaken',
            {sel_service_taken_id: sel_service_taken_id},
            function(response){
                var obj = typeof response === 'string' ? $.parseJSON(response) : response;
                if(obj.status){
                    console.log(obj.user_rec.service_type);
                    $('#entity_type').val("edit");
                    if(obj.user_rec.service_type == 'Migration') {
                        $('#Migration_inv').prop('checked', true);
                        $('#Eductaion_inv').prop('checked', false);
                        $('#Migration_inv').trigger('change');

                        $('#mig_ref_no').val(obj.user_rec.mig_ref_no);
                        $('#mig_service').val(obj.user_rec.mig_service);
                        $('#mig_notes').val(obj.user_rec.mig_notes);

                        $('#edu_course').val("");
                        $('#edu_college').val("");
                        $('#edu_service_start_date').val("");
                        $('#edu_notes').val("");

                    }
                } else {
                    toastMsg(obj.message, 'error');
                }
            }
        );
        $('#serviceTaken').modal('show');
    });

    // Initialize date picker for service start date
    if (typeof flatpickr !== 'undefined') {
        flatpickr('#edu_service_start_date', {
            dateFormat: 'd/m/Y',
            allowInput: true
        });
    }

    // Service type on change div
    $('.modal-body form#createservicetaken input[name="service_type"]').on('change', function(){
        var invid = $(this).attr('id');
        if(invid == 'Migration_inv'){
            $('.modal-body form#createservicetaken .is_Migration_inv').show();
            $('.modal-body form#createservicetaken .is_Migration_inv input').attr('data-valid', 'required');
            $('.modal-body form#createservicetaken .is_Eductaion_inv').hide();
            $('.modal-body form#createservicetaken .is_Eductaion_inv input').attr('data-valid', '');
        }
        else {
            $('.modal-body form#createservicetaken .is_Eductaion_inv').show();
            $('.modal-body form#createservicetaken .is_Eductaion_inv input').attr('data-valid', 'required');
            $('.modal-body form#createservicetaken .is_Migration_inv').hide();
            $('.modal-body form#createservicetaken .is_Migration_inv input').attr('data-valid', '');
        }
    });

    // Add and edit button service taken form submit
    $('#createservicetaken').submit(function(event) {
        event.preventDefault();
        var formData = $(this).serialize();
        
        AjaxHelper.post(
            App.getUrl('createServiceTaken') || App.getUrl('siteUrl') + '/client/createservicetaken',
            formData,
            function(response) {
                var res = response.user_rec;
                console.log(res);
                $('#serviceTaken').modal('hide');
                $(".popuploader").hide();

                // Clear and rebuild services grid
                $('.services-taken-grid').html('');
                
                $.each(res, function(index, value) {
                    var serviceClass = value.service_type.toLowerCase();
                    var badgeClass = serviceClass == 'migration' ? 'primary' : 'info';
                    
                    var cardHtml = '<div class="service-card service-card-' + serviceClass + '" id="service-card-' + value.id + '">';
                    cardHtml += '<div class="service-card-header">';
                    cardHtml += '<span class="service-type-badge badge badge-' + badgeClass + '">' + value.service_type + '</span>';
                    cardHtml += '<div class="service-actions">';
                    cardHtml += '<a href="javascript:;" class="service_taken_edit text-primary" id="' + value.id + '" title="Edit">' + crmIcon('edit') + '</a>';
                    cardHtml += '<a href="javascript:;" class="service_taken_trash text-danger ms-2" id="' + value.id + '" title="Delete">' + crmIcon('trash') + '</a>';
                    cardHtml += '</div></div>';
                    cardHtml += '<div class="service-card-body">';
                    
                    if(value.service_type == 'Migration') {
                        cardHtml += '<div class="service-detail"><span class="detail-label">Reference No:</span><span class="detail-value">' + value.mig_ref_no + '</span></div>';
                        cardHtml += '<div class="service-detail"><span class="detail-label">Service:</span><span class="detail-value">' + value.mig_service + '</span></div>';
                        cardHtml += '<div class="service-detail"><span class="detail-label">Notes:</span><span class="detail-value">' + value.mig_notes + '</span></div>';
                    }
                    
                    cardHtml += '</div></div>';
                    $('.services-taken-grid').append(cardHtml);
                });
            },
            function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        );
    });

    // Delete service taken
    $(document).delegate('.service_taken_trash', 'click', function(e){
        var conf = confirm('Are you sure you want to delete this service?');
        if(conf){
            var sel_service_taken_id = $(this).attr('id');
            
            AjaxHelper.post(
                App.getUrl('removeServiceTaken') || App.getUrl('siteUrl') + '/client/removeservicetaken',
                {sel_service_taken_id: sel_service_taken_id},
                function(response){
                    var obj = typeof response === 'string' ? $.parseJSON(response) : response;
                    if(obj.status){
                        // Remove the service card with animation
                        $('#service-card-' + obj.record_id).fadeOut(300, function(){
                            $(this).remove();
                            
                            // Check if no services left, show empty message
                            if($('.services-taken-grid .service-card').length === 0) {
                                var emptyHtml = '<div class="no-services-message">';
                                emptyHtml += crmIcon('inbox', { size: '3x', class: 'text-muted mb-3' });
                                emptyHtml += '<p class="text-muted">No services have been added yet.</p>';
                                emptyHtml += '<p class="text-muted"><small>Click "Add Service" to create a new service record.</small></p>';
                                emptyHtml += '</div>';
                                $('.services-taken-grid').html(emptyHtml);
                            }
                        });
                        
                        // Show success message
                        toastMsg(obj.message, 'success');
                    } else {
                        toastMsg(obj.message, 'error');
                    }
                }
            );
        } else {
            return false;
        }
    });

    // ============================================================================
    // ADDITIONAL CONTACT SECTION
    // ============================================================================

    function initClientFormTomSelects() {
        if (typeof waitForTomSelect !== 'function' || typeof initTomSelect !== 'function') {
            return;
        }

        waitForTomSelect().then(function () {
            var fullWidth = { width: '100%' };

            initTomSelect('select[name="visa_type"]', Object.assign({
                placeholder: '- Select Visa Type -',
                allowClear: true
            }, fullWidth));

            initTomSelect('select[name="country_passport"]', fullWidth);

            initTomSelect('#country_select', { width: '200px' });

            initTomSelect('select[name="service"]', Object.assign({
                placeholder: '- Select Lead Service -',
                allowClear: true
            }, fullWidth));

            initTomSelect('#assign_to', Object.assign({}, fullWidth, {
                plugins: ['remove_button'],
                closeAfterSelect: false,
                maxOptions: null
            }));

            if (document.querySelector('#lead_source')) {
                initTomSelectPreserveValue('#lead_source', Object.assign({
                    allowClear: true
                }, fullWidth));
                syncSubagentVisibility();
            }

            if (document.querySelector('select[name="subagent"]')) {
                initTomSelectPreserveValue('select[name="subagent"]', Object.assign({
                    allowClear: true
                }, fullWidth));
            }
        });
    }

    function getLeadSourceValue() {
        if (typeof getEnhancedSelectValue === 'function') {
            return getEnhancedSelectValue('#lead_source') || '';
        }
        var el = document.querySelector('#lead_source');
        return el ? (el.value || '') : '';
    }

    function syncSubagentVisibility() {
        if (getLeadSourceValue() === 'Sub Agent') {
            $('.is_subagent').css('display', 'inline-block');
        } else {
            $('.is_subagent').css('display', 'none');
        }
    }

    $(document).on('change', '#lead_source', function () {
        syncSubagentVisibility();
    });

    initClientFormTomSelects();
    
    $(document).delegate('.add_other_email_phone', 'click', function(){
        const section = $('.additional-contact-section');
        if (section.css('display') == 'none') {
            section.slideDown(300);
            $(this).html(crmIcon('minus') + ' Hide');
        } else {
            section.slideUp(300);
            $(this).html(crmIcon('plus') + ' Add More');
        }
    });
    
    // ============================================================================
    // EMAIL VERIFICATION
    // ============================================================================
    
    $('.manual_email_phone_verified').on('click', function(){
        var client_email = $(this).attr('data-email');
        var client_id = $(this).attr('data-clientid');
        var client_fname = $(this).attr('data-fname');
        if(client_email != '' && client_id != ""){
            AjaxHelper.post(
                App.getUrl('emailVerify') || App.getUrl('siteUrl') + '/email-verify',
                {client_email: client_email, client_id: client_id, client_fname: client_fname},
                function(response){
                    var obj = typeof response === 'string' ? $.parseJSON(response) : response;
                    toastMsg(obj.message, obj.status === true ? 'success' : 'error');
                    if (obj.status === true) {
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    }
                },
                function(xhr, status, error) {
                    var errorMsg = 'Failed to send verification email.';
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    toastMsg(errorMsg, 'error');
                }
            );
        }
    });
  
    // ============================================================================
    // PHONE VERIFICATION (Cellcast)
    // ============================================================================
    
    // Verify Phone
    $(document).delegate('.phone_verified', 'click', function(){
        $('#verifyphonemodal').modal('show');
        var client_id = $(this).attr('data-clientid');
        $('#verifyphone_client_id').val(client_id);

        var phone = $(this).attr('data-phone');
        $('#verify_phone_number').val(phone);
    });
  
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': App.getCsrf()
        }
    });

    $('#sendCodeBtn').click(function() {
        const phoneNumber = $('#verify_phone_number').val();
        if (!phoneNumber) {
            toastMsg('Please enter a phone number', 'warning');
            return;
        }
        const clientId = (typeof App !== 'undefined' && App.getPageConfig && App.getPageConfig('clientId')) || null;
        if (!clientId) {
            toastMsg('Client context is missing. Please refresh the page.', 'warning');
            return;
        }

        $.post(App.getUrl('verifySendCode') || App.getUrl('siteUrl') + '/verify/send-code', {
            client_id: clientId,
            phone_number: phoneNumber
        })
        .done(function(response) {
            if (response.success) {
                toastMsg(response.message || 'Verification code sent successfully', 'success');
                $('#verificationCodeSection').show();
            } else {
                toastMsg(response.message || 'Failed to send verification code', 'error');
            }
        })
        .fail(function(xhr) {
            const errorMsg = xhr.responseJSON?.message || xhr.responseJSON?.error || 'Failed to send verification code';
            toastMsg(errorMsg, 'error');
        });
    });

    $('#verifyCodeBtn').click(function() {
        const phoneNumber = $('#verify_phone_number').val();
        const code = $('#verification_code').val();
        if (!phoneNumber || !code) {
            toastMsg('Please enter phone number and verification code', 'warning');
            return;
        }
        const clientId = (typeof App !== 'undefined' && App.getPageConfig && App.getPageConfig('clientId')) || null;
        if (!clientId) {
            toastMsg('Client context is missing. Please refresh the page.', 'warning');
            return;
        }

        $.post(App.getUrl('verifyCheckCode') || App.getUrl('siteUrl') + '/verify/check-code', {
            client_id: clientId,
            phone_number: phoneNumber,
            verification_code: code
        })
        .done(function(response) {
            if (response.success) {
                toastMsg(response.message || 'Phone number verified successfully', 'success');
                $('#verifyphonemodal').modal('hide');
                location.reload(); // Reload to show updated verified numbers list
            } else {
                toastMsg(response.message || 'Verification failed', 'error');
            }
        })
        .fail(function(xhr) {
            const errorMsg = xhr.responseJSON?.message || xhr.responseJSON?.error || 'Verification failed';
            toastMsg(errorMsg, 'error');
        });
    });

    // ============================================================================
    // CLIENT ID VALIDATION
    // ============================================================================
    
    $('#checkclientid').on('blur', function(){
        var v = $(this).val();
        if(v != ''){
            AjaxHelper.get(
                App.getUrl('checkClientExist') || App.getUrl('siteUrl') + '/checkclientexist',
                {vl: v, type: 'clientid'},
                function(res){
                    if(res == 1){
                        toastMsg('Client Id is already exist in our record.', 'warning');
                    }
                }
            );
        }
    });
    
    // ============================================================================
    // RELATED FILES (Tom Select AJAX — Phase 4)
    // ============================================================================

    function initEditRelatedFiles() {
        if (!document.querySelector('select[name="related_files[]"]')) {
            return;
        }
        var opts = {
            url: (typeof App !== 'undefined' && typeof App.getUrl === 'function' && App.getUrl('getRecipients'))
                || (window.AppConfig && window.AppConfig.urls && window.AppConfig.urls.getRecipients)
                || '/clients/get-recipients',
            minimumInputLength: 1
        };
        if (window.RecipientSelect && typeof window.RecipientSelect.ensureRelatedFiles === 'function') {
            window.RecipientSelect.ensureRelatedFiles(opts);
        } else if (window.RecipientSelect && typeof window.RecipientSelect.initRelatedFiles === 'function') {
            window.RecipientSelect.initRelatedFiles(opts);
        }
    }

    if (typeof waitForRecipientSelect === 'function') {
        waitForRecipientSelect().then(initEditRelatedFiles);
    } else if (typeof window.RecipientSelect !== 'undefined') {
        initEditRelatedFiles();
    } else {
        var editRelatedAttempts = 0;
        var editRelatedTimer = setInterval(function () {
            editRelatedAttempts += 1;
            if (typeof window.RecipientSelect !== 'undefined' || editRelatedAttempts >= 100) {
                clearInterval(editRelatedTimer);
                initEditRelatedFiles();
            }
        }, 50);
    }

    // ============================================================================
    // PROFILE IMAGE PREVIEW
    // ============================================================================
    
    var loadFile = function(event) {
        var output = document.getElementById('output');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
            URL.revokeObjectURL(output.src); // free memory
            $('.if_image').hide();
            $('#output').css({'width':"100px",'height':"100px"});
        }
    };
    
    // Make loadFile available globally for inline onclick handlers
    if(typeof window !== 'undefined') {
        window.loadFile = loadFile;
    }
    
    console.log('Admin Client Edit page initialized');
});

})(); // End async wrapper

// ============================================================================
// GOOGLE MAPS INITIALIZATION
// ============================================================================

// Initialize Google Maps when API is loaded
function initAutocomplete() {
    if (typeof GoogleMaps === 'undefined') {
        console.error('GoogleMaps module not loaded');
        return;
    }
    
    GoogleMaps.initAutocomplete('map', 'pac-input', {
        fieldMappings: {
            postalCode: '#postal_code',
            locality: '#locality',
            state: 'select[name="state"]'
        }
    });
}

// Make initAutocomplete available globally for Google Maps callback
if(typeof window !== 'undefined') {
    window.initAutocomplete = initAutocomplete;
}

/**
 * Client edit form save — runs customValidate and shows a top summary when fields are missing.
 * Scoped to edit-clients only; does not affect other forms using customValidate().
 */
window.validateEditClientForm = function() {
    var $summary = $('#edit-clients-js-validation-alert');
    if ($summary.length) {
        $summary.hide();
    }

    if (typeof customValidate !== 'function') {
        return false;
    }

    var isValid = customValidate('edit-clients');

    if (!isValid && $summary.length) {
        $summary.show();
        var scrollTarget = $summary.offset().top - 20;
        $('html, body').animate({ scrollTop: scrollTarget }, 'slow');
    }

    return isValid;
};

