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
                    typeof $.fn.select2 === 'function' &&
                    typeof flatpickr !== 'undefined' &&
                    typeof $.fn.intlTelInput === 'function') {
                    console.log('[client-edit.js] All vendor libraries detected!');
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

jQuery(document).ready(function($){
    function autoSaveClientEditForm(successMessage) {
        if (window.__clientEditAutoSaveInProgress) {
            return;
        }
        window.__clientEditAutoSaveInProgress = true;
        
        if (successMessage) {
            if (typeof iziToast !== 'undefined') {
                iziToast.success({
                    title: 'Success',
                    message: successMessage,
                    position: 'topRight'
                });
            } else {
                alert(successMessage);
            }
        }
        
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
                // No redirect; keep user on edit page
            }).fail(function(xhr) {
                var errorMsg = 'Failed to save changes.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
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
    
    var itag_phone = $('.clientphonedata .row').length;
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
        $('#clientphoneform')[0].reset();
        $('.addclientphone').modal('show');
    });

    $('.addclientphone').on('shown.bs.modal', function () {
        // Initialize intlTelInput when modal is shown
        if ($(".telephone").length > 0 && typeof $.fn.intlTelInput === 'function') {
            var $telephone = $(".telephone");
            var currentValue = $telephone.val() || '';
            var isEmpty = currentValue.trim() === '';
            
            // Check if countries data is available before initializing
            if (window.intlTelInput && window.intlTelInput.countries && Array.isArray(window.intlTelInput.countries) && window.intlTelInput.countries.length > 0) {
                try {
                    // Initialize with initialDialCode enabled
                    $telephone.intlTelInput({
                        initialDialCode: true,
                        preferredCountries: window.PREFERRED_COUNTRIES ? window.PREFERRED_COUNTRIES.split(',') : ['au', 'in', 'pk', 'np', 'gb', 'ca'],
                        initialCountry: window.DEFAULT_COUNTRY || 'au'
                    });
                    
                    // Set value only if there was an existing value
                    if (currentValue && !isEmpty) {
                        try {
                            $telephone.intlTelInput('setNumber', currentValue);
                        } catch (e) {
                            $telephone.val(currentValue);
                        }
                    } else if (isEmpty) {
                        // Ensure default dial code is set if empty
                        setTimeout(function() {
                            const val = $telephone.val() || '';
                            if (val.trim() === '') {
                                try {
                                    const countryData = $telephone.intlTelInput('getSelectedCountryData');
                                    if (countryData && countryData.dialCode) {
                                        $telephone.val('+' + countryData.dialCode);
                                    } else {
                                        $telephone.val(window.DEFAULT_COUNTRY_CODE || '+61');
                                    }
                                } catch (e) {
                                    $telephone.val(window.DEFAULT_COUNTRY_CODE || '+61');
                                }
                            }
                        }, 50);
                    }
                } catch (e) {
                    console.warn('Error initializing intlTelInput in modal, retrying...', e);
                    // Retry after a short delay
                    setTimeout(function() {
                        if (window.intlTelInput && window.intlTelInput.countries && Array.isArray(window.intlTelInput.countries) && window.intlTelInput.countries.length > 0) {
                            try {
                                $telephone.intlTelInput({
                                    initialDialCode: true,
                                    preferredCountries: window.PREFERRED_COUNTRIES ? window.PREFERRED_COUNTRIES.split(',') : ['au', 'in', 'pk', 'np', 'gb', 'ca'],
                                    initialCountry: window.DEFAULT_COUNTRY || 'au'
                                });
                                // Set default if empty
                                if (isEmpty) {
                                    setTimeout(function() {
                                        const val = $telephone.val() || '';
                                        if (val.trim() === '') {
                                            $telephone.val(window.DEFAULT_COUNTRY_CODE || '+61');
                                        }
                                    }, 50);
                                }
                            } catch (retryError) {
                                console.error('intlTelInput initialization failed in modal after retry:', retryError);
                            }
                        }
                    }, 100);
                }
            } else {
                // Wait for countries data to be available
                var retryCount = 0;
                var maxRetries = 10;
                var checkInterval = setInterval(function() {
                    retryCount++;
                    if (window.intlTelInput && window.intlTelInput.countries && Array.isArray(window.intlTelInput.countries) && window.intlTelInput.countries.length > 0) {
                        clearInterval(checkInterval);
                        try {
                            $telephone.intlTelInput({
                                initialDialCode: true,
                                preferredCountries: window.PREFERRED_COUNTRIES ? window.PREFERRED_COUNTRIES.split(',') : ['au', 'in', 'pk', 'np', 'gb', 'ca'],
                                initialCountry: window.DEFAULT_COUNTRY || 'au'
                            });
                            // Set default if empty
                            if (isEmpty) {
                                setTimeout(function() {
                                    const val = $telephone.val() || '';
                                    if (val.trim() === '') {
                                        $telephone.val(window.DEFAULT_COUNTRY_CODE || '+61');
                                    }
                                }, 50);
                            }
                        } catch (e) {
                            console.error('Error initializing intlTelInput in modal:', e);
                        }
                    } else if (retryCount >= maxRetries) {
                        clearInterval(checkInterval);
                        console.error('intlTelInput countries data not available after retries');
                    }
                }, 50);
            }
        }
    });
  
    // Save client phone
    $(document).delegate('.saveclientphone','click', function() {
        // Clear previous errors
        $('.client_phone_error').html('');
        $('.contact_type_error').html('');
        $('input[name="client_phone"]').parent().removeClass('error');
        $('#contact_type').parent().removeClass('error');
        
        // Get form values
        var contact_type = $('#contact_type').val();
        var client_phone = $('input[name="client_phone"]').val();
        
        // Get country code from intlTelInput - extract dial code from input value
        var country_code_input = $('.telephone').val();
        var country_code = '';
        if (country_code_input) {
            // Extract dial code (e.g., "+61 " -> "61" or "+61" -> "61")
            var match = country_code_input.match(/^\+?(\d+)/);
            if (match) {
                country_code = '+' + match[1];
            }
        }
        
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
            $('.telephone').parent().addClass('error');
            flag = true;
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
                html += '<a href="javascript:;" dataid="'+itag_phone+'" class="deletecontact btn-delete"><i class="fa fa-trash"></i></a>';
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
            // Re-initialize intlTelInput after reset
            if (typeof $.fn.intlTelInput === 'function') {
                if (window.intlTelInput && window.intlTelInput.countries && Array.isArray(window.intlTelInput.countries) && window.intlTelInput.countries.length > 0) {
                    try {
                        var $telephone = $(".telephone");
                        // Destroy existing instance if any
                        if ($telephone.data('intlTelInput')) {
                            $telephone.intlTelInput('destroy');
                        }
                        // Re-initialize with initialDialCode
                        $telephone.intlTelInput({
                            initialDialCode: true,
                            preferredCountries: window.PREFERRED_COUNTRIES ? window.PREFERRED_COUNTRIES.split(',') : ['au', 'in', 'pk', 'np', 'gb', 'ca'],
                            initialCountry: window.DEFAULT_COUNTRY || 'au'
                        });
                        // Ensure default dial code is set
                        setTimeout(function() {
                            const val = $telephone.val() || '';
                            if (val.trim() === '') {
                                try {
                                    const countryData = $telephone.intlTelInput('getSelectedCountryData');
                                    if (countryData && countryData.dialCode) {
                                        $telephone.val('+' + countryData.dialCode);
                                    } else {
                                        $telephone.val(window.DEFAULT_COUNTRY_CODE || '+61');
                                    }
                                } catch (e) {
                                    $telephone.val(window.DEFAULT_COUNTRY_CODE || '+61');
                                }
                            }
                        }, 50);
                    } catch (e) {
                        console.warn('Error re-initializing intlTelInput after reset:', e);
                    }
                }
            }
            $('.addclientphone').modal('hide');
            itag_phone++;
        }
    });

    $(document).delegate('.deletecontact','click', function(){
        var v = $(this).attr('dataid');
        var contactid = $(this).attr('contactid');
        // Show confirmation message
        if (confirm('Are you sure you want to delete this contact?')) {
            // If user clicks Yes
            $('#metatag2_'+v).remove();
            if (typeof contactid !== 'undefined' && contactid !== false) {
                $('.removesids_contact').append('<input type="hidden" name="rem_phone[]" value="'+contactid+'">');
            }
        }
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
        
        // Set country code in intlTelInput
        if ($(".telephone").length > 0 && typeof $.fn.intlTelInput === 'function') {
            // Ensure intlTelInput is initialized before setting value
            if (window.intlTelInput && window.intlTelInput.countries && Array.isArray(window.intlTelInput.countries) && window.intlTelInput.countries.length > 0) {
                try {
                    // Check if already initialized, if not initialize it
                    if (!$(".telephone").data('intlTelInput')) {
                        $(".telephone").intlTelInput();
                    }
                    $(".telephone").val(country_code);
                } catch (e) {
                    console.warn('Error setting country code in intlTelInput:', e);
                    // Fallback: just set the value directly
                    $(".telephone").val(country_code);
                }
            } else {
                // Fallback: set value directly if intlTelInput not ready
                $(".telephone").val(country_code);
            }
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
        
        // Get form values
        var contact_type = $('#contact_type').val();
        var client_phone = $('input[name="client_phone"]').val();
        var phone_index = $('#edit_phone_index').val();
        var phone_id = $('#edit_phone_id').val();
        
        // Get country code from intlTelInput
        var country_code_input = $('.telephone').val();
        var country_code = '';
        if (country_code_input) {
            var match = country_code_input.match(/^\+?(\d+)/);
            if (match) {
                country_code = '+' + match[1];
            }
        }
        
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
            $('.telephone').parent().addClass('error');
            flag = true;
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
                
                // Update data attributes for future edits
                $phoneItem.find('.editclientphone').attr('data-type', contact_type);
                $phoneItem.find('.editclientphone').attr('data-country', country_code);
                $phoneItem.find('.editclientphone').attr('data-phone', client_phone);
                
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
                alert('Error: Could not find the phone item to update.');
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
        $('#clientemailform')[0].reset();
        $('.addclientemail').modal('show');
    });

    // Save client email
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
            alert('Please select email type.');
            flag = true;
        }

        if(!flag){
            // Check if this is main email or additional
            var isMainEmail = (email_type == 'Personal' || email_type == 'Business');
            var emailId = isMainEmail ? 'email_main' : 'email_additional_' + itag_email;
            var hiddenName = isMainEmail ? 'email' : 'att_email';
            var hiddenTypeName = isMainEmail ? 'email_type' : '';
            
            // Remove existing main email if adding new main email
            if(isMainEmail) {
                $('#email_main').remove();
            }
            
            var html = '<div class="compact-contact-item" id="'+emailId+'">';
            html += '<span class="contact-type-tag">'+email_type+'</span>';
            html += '<span class="contact-email">'+client_email+'</span>';
            html += '<div class="contact-actions">';
            
            if(isMainEmail) {
                html += '<button type="button" class="btn-verify manual_email_phone_verified" data-fname="' + App.getPageConfig('clientFirstName') + '" data-email="'+client_email+'" data-clientid="' + App.getPageConfig('clientId') + '">';
                html += '<i class="fas fa-check"></i>';
                html += '</button>';
            } else {
                html += '<a href="javascript:;" class="deleteemail btn-delete" data-email="'+emailId+'">';
                html += '<i class="fa fa-trash"></i>';
                html += '</a>';
            }
            
            html += '</div>';
            
            // Hidden fields
            html += '<input type="hidden" name="'+hiddenName+'" value="'+client_email+'">';
            if(hiddenTypeName) {
                html += '<input type="hidden" name="'+hiddenTypeName+'" value="'+email_type+'">';
            }
            html += '</div>';

            $('.clientemaildata').append(html);
            $('#clientemailform')[0].reset();
            $('.addclientemail').modal('hide');
            itag_email++;
        }
    });

    // Delete email
    $(document).delegate('.deleteemail','click', function(){
        var emailId = $(this).attr('data-email');
        if (confirm('Are you sure you want to delete this email?')) {
            $('#'+emailId).remove();
        }
    });

    // Edit client email
    $(document).delegate('.editclientemail','click', function(){
        $('#clientEmailModalLabel').html('Edit Email Address');
        $('.saveclientemail').hide();
        $('#update_clientemail').show();
        
        // Get data from clicked element
        var email_id = $(this).data('email-id');
        var email_type = $(this).data('type');
        var email_address = $(this).data('email');
        
        // Store edit mode data
        $('#edit_email_mode').val('1');
        $('#edit_email_id').val(email_id);
        
        // Clear errors
        $('.client_email_error').html('');
        $('input[name="client_email"]').parent().removeClass('error');
        
        // Populate form
        $('#email_type_modal').val(email_type);
        $('input[name="client_email"]').val(email_address);
        
        $('.addclientemail').modal('show');
    });

    // Update client email
    $(document).delegate('#update_clientemail','click', function(){
        // Clear previous errors
        $('.client_email_error').html('');
        $('input[name="client_email"]').parent().removeClass('error');
        
        // Get form values
        var client_email = $('input[name="client_email"]').val();
        var email_type = $('select[name="email_type_modal"]').val();
        var email_id = $('#edit_email_id').val();
        
        console.log('Update Email - ID:', email_id);
        console.log('Update Email - Type:', email_type);
        console.log('Update Email - Address:', client_email);
        
        // Validate
        var flag = false;
        if(client_email == ''){
            $('.client_email_error').html('The Email field is required.');
            $('input[name="client_email"]').parent().addClass('error');
            flag = true;
        }
        if(email_type == ''){
            alert('Please select email type.');
            flag = true;
        }

        if(!flag){
            var $emailItem = null;
            
            if(email_id == 'main'){
                $emailItem = $('#email_main');
            } else if(email_id == 'additional'){
                $emailItem = $('#email_additional');
            }
            
            if($emailItem && $emailItem.length > 0){
                console.log('Found email item, updating...');
                
                // Update the display
                $emailItem.find('.contact-type-tag').text(email_type);
                $emailItem.find('.contact-email').text(client_email);
                
                // Update hidden fields - CRITICAL for form submission
                if(email_id == 'main'){
                    $emailItem.find('input[name="email"]').val(client_email);
                    $emailItem.find('input[name="email_type"]').val(email_type);
                    console.log('Updated main email:', {
                        email: $emailItem.find('input[name="email"]').val(),
                        type: $emailItem.find('input[name="email_type"]').val()
                    });
                } else {
                    $emailItem.find('input[name="att_email"]').val(client_email);
                    console.log('Updated additional email:', $emailItem.find('input[name="att_email"]').val());
                }
                
                // Update data attributes for future edits
                $emailItem.find('.editclientemail').attr('data-type', email_type);
                $emailItem.find('.editclientemail').attr('data-email', client_email);
                
                // Clear form and close modal
                $('#clientemailform')[0].reset();
                $('#edit_email_mode').val('0');
                $('#edit_email_id').val('');
                $('.addclientemail').modal('hide');
                
                autoSaveClientEditForm('Email updated successfully.');
            } else {
                console.error('Could not find email item with id:', email_id);
                alert('Error: Could not find the email item to update.');
            }
        }
    });

    // ============================================================================
    // SOURCE FIELD HANDLING
    // ============================================================================
    
    var source_val = App.getPageConfig('source') || '';
    if(source_val != '') {
        if(source_val == 'Sub Agent') {
            $('.is_subagent').css('display','inline-block');
        } else {
            $('.is_subagent').css('display','none');
        }
    } else {
        $('.is_subagent').css('display','none');
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
                    alert(obj.message);
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
                    cardHtml += '<a href="javascript:;" class="service_taken_edit text-primary" id="' + value.id + '" title="Edit"><i class="fa fa-edit"></i></a>';
                    cardHtml += '<a href="javascript:;" class="service_taken_trash text-danger ms-2" id="' + value.id + '" title="Delete"><i class="fa fa-trash"></i></a>';
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
                                emptyHtml += '<i class="fas fa-inbox fa-3x text-muted mb-3"></i>';
                                emptyHtml += '<p class="text-muted">No services have been added yet.</p>';
                                emptyHtml += '<p class="text-muted"><small>Click "Add Service" to create a new service record.</small></p>';
                                emptyHtml += '</div>';
                                $('.services-taken-grid').html(emptyHtml);
                            }
                        });
                        
                        // Show success message
                        if (typeof iziToast !== 'undefined') {
                            iziToast.success({
                                title: 'Success',
                                message: obj.message,
                                position: 'topRight'
                            });
                        } else {
                            alert(obj.message);
                        }
                    } else {
                        alert(obj.message);
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
    
    $("#country_select").select2({ width: '200px' });
    
    $(document).delegate('.add_other_email_phone', 'click', function(){
        const section = $('.additional-contact-section');
        if (section.css('display') == 'none') {
            section.slideDown(300);
            $(this).html('<i class="fa fa-minus" aria-hidden="true"></i> Hide');
        } else {
            section.slideUp(300);
            $(this).html('<i class="fa fa-plus" aria-hidden="true"></i> Add More');
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
                    alert(obj.message);
                    // Reload page if successful to update verification status
                    if(obj.status === true) {
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
                    alert(errorMsg);
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
            alert('Please enter a phone number');
            return;
        }

        $.post(App.getUrl('verifySendCode') || App.getUrl('siteUrl') + '/verify/send-code', {
            phone_number: phoneNumber
        })
        .done(function(response) {
            if (response.success) {
                alert(response.message || 'Verification code sent successfully');
                $('#verificationCodeSection').show();
            } else {
                alert(response.message || 'Failed to send verification code');
            }
        })
        .fail(function(xhr) {
            const errorMsg = xhr.responseJSON?.message || xhr.responseJSON?.error || 'Failed to send verification code';
            alert(errorMsg);
        });
    });

    $('#verifyCodeBtn').click(function() {
        const phoneNumber = $('#verify_phone_number').val();
        const code = $('#verification_code').val();
        if (!phoneNumber || !code) {
            alert('Please enter phone number and verification code');
            return;
        }

        $.post(App.getUrl('verifyCheckCode') || App.getUrl('siteUrl') + '/verify/check-code', {
            phone_number: phoneNumber,
            verification_code: code
        })
        .done(function(response) {
            if (response.success) {
                alert(response.message || 'Phone number verified successfully');
                $('#verifyphonemodal').modal('hide');
                location.reload(); // Reload to show updated verified numbers list
            } else {
                alert(response.message || 'Verification failed');
            }
        })
        .fail(function(xhr) {
            const errorMsg = xhr.responseJSON?.message || xhr.responseJSON?.error || 'Verification failed';
            alert(errorMsg);
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
                        alert('Client Id is already exist in our record.');
                    }
                }
            );
        }
    });
    
    // ============================================================================
    // RELATED FILES SELECT2
    // ============================================================================
    
    var relatedFilesData = App.getPageConfig('relatedFilesData');
    if (!relatedFilesData || relatedFilesData.length === 0) {
        relatedFilesData = [];
        $('.relatedfile').each(function() {
            var $item = $(this);
            var id = $item.data('id');
            var name = $item.data('name');
            var email = $item.data('email');
            if (id) {
                relatedFilesData.push({
                    id: id,
                    name: name || '',
                    email: email || ''
                });
            }
        });
    }
    if (relatedFilesData && relatedFilesData.length > 0) {
        var array = [];
        var data = [];
        
        relatedFilesData.forEach(function(file) {
            array.push(file.id);
            var status = 'Client';
            
            data.push({
                id: file.id,
                name: file.name,
                email: file.email,
                status: status,
                text: file.name,
                html: "<div class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +
                    "<div class='ag-flex ag-align-start'>" +
                    "<div class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span class='select2-result-repository__title text-semi-bold'>"+file.name+"</span>&nbsp;</div>" +
                    "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'>"+file.email+"</small></div>" +
                    "</div>" +
                    "</div>" +
                    "<div class='ag-flex ag-flex-column ag-align-end'>" +
                    "<span class='ui label yellow select2-result-repository__statistics'>"+ status +"</span>" +
                    "</div>" +
                    "</div>",
                title: file.name
            });
        });
        
        $(".js-data-example-ajaxcc").select2({
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
        $('.js-data-example-ajaxcc').val(array);
        $('.js-data-example-ajaxcc').trigger('change');
    }
    
    // Re-initialize with AJAX support (this overrides the previous initialization)
    console.log('About to initialize Select2 with AJAX for:', $('.js-data-example-ajaxcc').length, 'elements');
    $('.js-data-example-ajaxcc').select2({
        multiple: true,
        closeOnSelect: false,
        ajax: {
            url: App.getUrl('clientGetRecipients') || App.getUrl('siteUrl') + '/clients/get-recipients',
            dataType: 'json',
            data: function (params) {
                console.log('AJAX data function called with params:', params);
                return {
                    q: params.term, // search term
                    page: params.page || 1
                };
            },
            processResults: function (data) {
                console.log('AJAX processResults called with:', data);
                return {
                    results: data.items
                };
            },
            cache: true
        },
        templateResult: formatRepo,
        templateSelection: formatRepoSelection
    });
    console.log('Select2 initialized. Configuration:', $('.js-data-example-ajaxcc').data('select2'));
    
    function formatRepo (repo) {
        if (repo.loading) {
            return repo.text;
        }

        var $container = $(
            "<div class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +
            "<div class='ag-flex ag-align-start'>" +
            "<div class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
            "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small></div>" +
            "</div>" +
            "</div>" +
            "<div class='ag-flex ag-flex-column ag-align-end'>" +
            "<span class='ui label yellow select2-result-repository__statistics'></span>" +
            "</div>" +
            "</div>"
        );

        $container.find(".select2-result-repository__title").text(repo.name);
        $container.find(".select2-result-repository__description").text(repo.email);
        $container.find(".select2-result-repository__statistics").append(repo.status);
 
        return $container;
    }

    function formatRepoSelection (repo) {
        return repo.name || repo.text;
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

