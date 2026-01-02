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
// INITIALIZATION
// ============================================================================

jQuery(document).ready(function($){
  
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
        $('#clientphoneform')[0].reset();
        $('.addclientphone').modal('show');
        $(".telephone").intlTelInput();
    });

    $('.addclientphone').on('shown.bs.modal', function () {
        $(".telephone").intlTelInput();
    });
  
    // Save client phone
    $(document).delegate('.saveclientphone','click', function() {
        var client_phone = $('input[name="client_phone"]').val();
        $('.client_phone_error').html('');
        $('input[name="client_phone"]').parent().removeClass('error');
        if ($('table#metatag_table').find('#metatag2_'+itag_phone).length > 0) {
        }
        else {
            var flag = false;
            if(client_phone == ''){
                $('.client_phone_error').html('The Phone field is required.');
                $('input[name="client_phone"]').parent().addClass('error');
                flag = true;
            }

            if(!flag){
                var str = $( "#clientphoneform" ).serializeArray();
                console.log(str);
                clientphonedata[itag_phone] = {"contact_type":str[0].value, "country_code":str[1].value ,"phone":str[2].value}
                console.log(clientphonedata);

                // New compact design HTML
                var html = '<div class="compact-contact-item" id="metatag2_'+itag_phone+'">';
                html += '<span class="contact-type-tag">'+str[0].value+'</span>';
                html += '<span class="contact-phone">'+str[1].value+' '+str[2].value+'</span>';
                html += '<div class="contact-actions">';
                
                if(str[0].value != 'Personal') {
                    html += '<a href="javascript:;" dataid="'+itag_phone+'" class="deletecontact btn-delete"><i class="fa fa-trash"></i></a>';
                }
                
                html += '</div>';
                
                // Hidden fields
                html += '<input type="hidden" name="contact_type[]" value="'+str[0].value+'">';
                html += '<input type="hidden" name="client_country_code[]" value="'+str[1].value+'">';
                html += '<input type="hidden" name="client_phone[]" value="'+str[2].value+'">';
                html += '<input type="hidden" name="clientphoneid[]" value="">';
                html += '</div>';

                $('.clientphonedata').append(html);
                $('#clientphoneform')[0].reset();
                $('.addclientphone').modal('hide');
                itag_phone++;
            }
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

    // ============================================================================
    // TAG MANAGEMENT (Select2)
    // ============================================================================
    
    // Initialize tags Select2 if tags exist
    var tagInitialData = App.getPageConfig('tagInitialData');
    if (tagInitialData && tagInitialData.length > 0) {
        var array1 = [];
        var data1 = [];
        
        tagInitialData.forEach(function(tag) {
            array1.push(tag.id);
            data1.push({
                id: tag.id,
                text: tag.name,
            });
        });

        $("#tag").select2({
            data: data1,
            escapeMarkup: function(markup) {
                return markup;
            },
            templateResult: function(data1) {
                return data1.html;
            },
            templateSelection: function(data1) {
                return data1.text;
            }
        });

        $('#tag').val(array1);
        $('#tag').trigger('change');
    }

    // Initialize tags Select2 with AJAX
    $('#tag').select2({
        ajax: {
            url: App.getUrl('getTagData') || App.getUrl('siteUrl') + '/admin/gettagdata',
            headers: { 'X-CSRF-TOKEN': App.getCsrf()},
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.items.map(item => ({
                        id: item.id,
                        text: item.text
                    })),
                    pagination: {
                        more: (params.page * data.per_page) < data.total_count
                    }
                };
            },
            cache: true
        },
        placeholder: 'Search & Select tag',
        minimumInputLength: 1,
        templateResult: formatItem,
        templateSelection: formatItemSelection
    });

    function formatItem(item) {
        if (item.loading) {
            return item.text;
        }
        return item.text;
    }

    function formatItemSelection(item) {
        return item.text || item.id;
    }

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
            App.getUrl('getServiceTaken') || App.getUrl('siteUrl') + '/admin/client/getservicetaken',
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

                    } else if(obj.user_rec.service_type == 'Education') {
                        $('#Eductaion_inv').prop('checked', true);
                        $('#Migration_inv').prop('checked', false);
                        $('#Eductaion_inv').trigger('change');

                        $('#edu_course').val(obj.user_rec.edu_course);
                        $('#edu_college').val(obj.user_rec.edu_college);
                        $('#edu_service_start_date').val(obj.user_rec.edu_service_start_date);
                        $('#edu_notes').val(obj.user_rec.edu_notes);

                        $('#mig_ref_no').val("");
                        $('#mig_service').val("");
                        $('#mig_notes').val("");
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
            App.getUrl('createServiceTaken') || App.getUrl('siteUrl') + '/admin/client/createservicetaken',
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
                    } else if(value.service_type == 'Education') {
                        cardHtml += '<div class="service-detail"><span class="detail-label">Course:</span><span class="detail-value">' + value.edu_course + '</span></div>';
                        cardHtml += '<div class="service-detail"><span class="detail-label">College:</span><span class="detail-value">' + value.edu_college + '</span></div>';
                        cardHtml += '<div class="service-detail"><span class="detail-label">Start Date:</span><span class="detail-value">' + value.edu_service_start_date + '</span></div>';
                        cardHtml += '<div class="service-detail"><span class="detail-label">Notes:</span><span class="detail-value">' + value.edu_notes + '</span></div>';
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
                App.getUrl('removeServiceTaken') || App.getUrl('siteUrl') + '/admin/client/removeservicetaken',
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
                }
            );
        }
    });
  
    // ============================================================================
    // PHONE VERIFICATION (Twilio)
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
        if (!phoneNumber) return;

        $.post(App.getUrl('verifySendCode') || App.getUrl('siteUrl') + '/verify/send-code', {
            phone_number: phoneNumber
        })
        .done(function(response) {
            alert(response.message);
            $('#verificationCodeSection').show();
        })
        .fail(function(xhr) {
            alert('Failed to send verification code');
        });
    });

    $('#verifyCodeBtn').click(function() {
        const phoneNumber = $('#verify_phone_number').val();
        const code = $('#verification_code').val();
        if (!phoneNumber || !code) return;

        $.post(App.getUrl('verifyCheckCode') || App.getUrl('siteUrl') + '/verify/check-code', {
            phone_number: phoneNumber,
            verification_code: code
        })
        .done(function(response) {
            alert(response.message);
            $('#verifyphonemodal').modal('hide');
            location.reload(); // Reload to show updated verified numbers list
        })
        .fail(function(xhr) {
            alert(xhr.responseJSON?.message || 'Verification failed');
        });
    });

    // ============================================================================
    // CLIENT ID VALIDATION
    // ============================================================================
    
    $('#checkclientid').on('blur', function(){
        var v = $(this).val();
        if(v != ''){
            AjaxHelper.get(
                App.getUrl('checkClientExist') || App.getUrl('siteUrl') + '/admin/checkclientexist',
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
            url: App.getUrl('clientGetRecipients') || App.getUrl('siteUrl') + '/admin/clients/get-recipients',
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

