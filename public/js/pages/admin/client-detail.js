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

// Download document handler
document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (e) {
        // Check if the clicked element has the class `.download-file`
        const target = e.target.closest('a.download-file');

        // If it's not a .download-file anchor, do nothing
        if (!target) return;

        // If the link already points to a download URL with params, let the browser handle it
        const href = target.getAttribute('href') || '';
        if (href && href !== '#' && href.includes('/download-document') && href.includes('filelink=')) {
            return;
        }

        e.preventDefault();

        const filelink = target.dataset.filelink;
        const filename = target.dataset.filename;

        if (!filelink || !filename) {
            alert('Missing file info.');
            return;
        }

        // Create and submit a hidden form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = App.getUrl('downloadDocument') || App.getUrl('siteUrl') + '/download-document';
        form.target = '_blank';

        // CSRF token
        const token = App.getCsrf();
        form.innerHTML = `
            <input type="hidden" name="_token" value="${token}">
            <input type="hidden" name="filelink" value="${filelink}">
            <input type="hidden" name="filename" value="${filename}">
        `;

        document.body.appendChild(form);
        form.submit();
        form.remove();
    });
});

// ChatGPT handlers
const chatGptToggle = document.getElementById('chatGptToggle');
if (chatGptToggle) {
    chatGptToggle.addEventListener('click', function() {
        const section = document.getElementById('chatGptSection');
        if (section) {
            section.classList.toggle('collapse');
        }
    });
}

const chatGptClose = document.getElementById('chatGptClose');
if (chatGptClose) {
    chatGptClose.addEventListener('click', function() {
        const section = document.getElementById('chatGptSection');
        if (section) {
            section.classList.add('collapse');
        }
    });
}

const enhanceMessageBtn = document.getElementById('enhanceMessageBtn');
if (enhanceMessageBtn) {
    enhanceMessageBtn.addEventListener('click', function() {
        const chatGptInput = document.getElementById('chatGptInput');
        if (!chatGptInput || !chatGptInput.value) {
            alert('Please enter a message to enhance.');
            return;
        }

        var enhanceUrl = App.getUrl('mailEnhance') || App.getUrl('siteUrl') + '/mail/enhance';
        
        fetch(enhanceUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": App.getCsrf()
            },
            body: JSON.stringify({ message: chatGptInput.value })
        })
        .then(response => response.json())
        .then(data => {
            if (data.enhanced_message) {
                // Split the enhanced message into lines
                const lines = data.enhanced_message.split('\n').filter(line => line.trim() !== '');

                // First line is the subject
                const subject = lines[0] || '';

                // Remaining lines are the body
                const body = lines.slice(1).join('\n') || '';

                // Update the subject and message fields
                const composeEmailSubject = document.getElementById('compose_email_subject');
                if (composeEmailSubject) {
                    composeEmailSubject.value = subject;
                }
                // Ensure Summernote is initialized before updating content
                if ($("#emailmodal .summernote-simple").length && typeof $.fn.summernote !== 'undefined') {
                    $("#emailmodal .summernote-simple").summernote('code', body);
                }

                // Close the ChatGPT section
                const chatGptSection = document.getElementById('chatGptSection');
                if (chatGptSection) {
                    chatGptSection.classList.add('collapse');
                }
            } else {
                alert(data.error || 'Failed to enhance message.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while enhancing the message.');
        });
    });
}

// ============================================================================
// MAIN JQUERY READY BLOCK
// ============================================================================

jQuery(document).ready(function($){
  
    // Ensure Activities tab is active when filter parameters are present
    // This fixes the issue where filters are applied but tab doesn't show
    var urlParams = new URLSearchParams(window.location.search);
    var hasFilters = urlParams.has('keyword') || 
                     (urlParams.has('activity_type') && urlParams.get('activity_type') !== 'all') ||
                     urlParams.has('date_from') || 
                     urlParams.has('date_to');
    var tabParam = urlParams.get('tab');
    
    // If filters are present and tab is empty/not set, activate Activities tab
    if (hasFilters && (!tabParam || tabParam === '')) {
        var activitiesTab = $('#activities-tab');
        var activitiesPane = $('#activities');
        
        // Remove active class from all tabs and panes
        $('#client_tabs .nav-link').removeClass('active');
        $('#clientContent .tab-pane').removeClass('show active');
        
        // Activate Activities tab and pane
        activitiesTab.addClass('active').attr('aria-selected', 'true');
        activitiesPane.addClass('show active');
    }
  
    // Function to handle personal-details-container visibility based on active tab
    function handlePersonalDetailsVisibility() {
        // Get the currently active tab
        var activeTab = $('#client_tabs .nav-link.active');
        var targetHref = activeTab.attr('href') || '';
        
        // Document-related tabs
        var documentTabs = ['#documents', '#migrationdocuments', '#alldocuments', '#notuseddocuments'];
        
        if (documentTabs.indexOf(targetHref) !== -1) {
            // Hide personal-details-container for document tabs (right_section takes full width)
            $('.personal-details-container').hide();
        } else {
            // Show personal-details-container for other tabs (right_section takes remaining space)
            $('.personal-details-container').show();
        }
    }
  
    // Set initial visibility on page load based on active tab
    // Use setTimeout to ensure DOM is fully ready and active tab is set
    setTimeout(function() {
        handlePersonalDetailsVisibility();
    }, 50);
  
    // Tab click handler - update visibility when tabs are clicked
    $(document).on('click', '#client_tabs a', function(){
        // Use setTimeout to ensure Bootstrap tab switching completes first
        setTimeout(function() {
            handlePersonalDetailsVisibility();
        }, 10);
    });
    
    // Also listen to Bootstrap's shown.bs.tab event for more reliable handling
    $('#client_tabs a').on('shown.bs.tab', function (e) {
        handlePersonalDetailsVisibility();
    });
  
    $('.selecttemplate').select2({dropdownParent: $('#emailmodal')});

    /////////////////////////////////////////////
    ////// At Google review button sent email with review link code start /////////
    /////////////////////////////////////////////
    $(document).on('click', '.googleReviewBtn', function(e){
        var is_greview_mail_sent = $(this).attr('data-is_greview_mail_sent');
        console.log(is_greview_mail_sent);
        if(is_greview_mail_sent != 1){
            is_greview_mail_sent = 0;
        } else {
            is_greview_mail_sent = 1;
        }
        var conf = confirm('Do you want to sent google review link in email?');
        //If review email not sent till now
        if(conf && is_greview_mail_sent != 1 ){
            var url = App.getUrl('isGReviewMailSent') || App.getUrl('siteUrl') + '/is_greview_mail_sent';
            $.ajax({
                url: url,
                headers: { 'X-CSRF-TOKEN': App.getCsrf()},
                type:'POST',
                datatype:'json',
                data:{id: App.getPageConfig('clientId'), is_greview_mail_sent: is_greview_mail_sent},
                success: function(response){
                    var obj = typeof response === 'string' ? $.parseJSON(response) : response;
                    if(obj.status){
                        alert(obj.message);
                        location.reload();
                    } else {
                        alert(obj.message);
                    }
                }
            });
        } else {
            return false;
        }
    });

    /////////////////////////////////////////////
    ////// At Google review button sent email with review link code end /////////
    /////////////////////////////////////////////

    //create client receipt start
    // Flatpickr replacements
    if (typeof flatpickr !== 'undefined') {
        $('.report_date_fields').each(function() {
            flatpickr(this, {
                dateFormat: 'd/m/Y',
                defaultDate: 'today',
                allowInput: true
            });
        });
        $('.report_entry_date_fields').each(function() {
            flatpickr(this, {
                dateFormat: 'd/m/Y',
                defaultDate: 'today',
                allowInput: true
            });
        });
    }

    // NOTE: .openproductrinfo handler has been moved to detail.blade.php inline script
    // to avoid duplication and ensure calculateReceiptTotal() is called properly

    // ============================================================================
    // RECEIPT FUNCTIONS
    // ============================================================================
    
    function getTopReceiptValInDB(type) {
        var url = App.getUrl('clientGetTopReceipt') || App.getUrl('siteUrl') + '/clients/getTopReceiptValInDB';
        $.ajax({
            type:'post',
            url: url,
            sync:true,
            headers: { 'X-CSRF-TOKEN': App.getCsrf()},
            data: {type:type},
            success: function(response){
                var obj = typeof response === 'string' ? $.parseJSON(response) : response;
                if(obj.receipt_type == 1){ //client receipt
                    if(obj.record_count >0){
                        $('#top_value_db').val(obj.record_count);
                    } else {
                        $('#top_value_db').val(obj.record_count);
                    }
                }
            }
        });
    }

    $(document).on('blur', '.deposit_amount_per_row', function(){
        if( $(this).val() != ""){
            var randomNumber = $('#top_value_db').val();
            randomNumber = Number(randomNumber);
            randomNumber = randomNumber + 1;
            $('#top_value_db').val(randomNumber);
            randomNumber = "Rec"+randomNumber;
        }
    });

    function grandtotalAccountTab(){
        var total_deposit_amount_all_rows = 0;
        $('.productitem tr').each(function(){
            if($(this).find('.deposit_amount_per_row').val() != ''){
                var deposit_amount_per_row = $(this).find('.deposit_amount_per_row').val();
            }else{
                var deposit_amount_per_row = 0;
            }
            total_deposit_amount_all_rows += parseFloat(deposit_amount_per_row);
        });
        $('.total_deposit_amount_all_rows').html("$"+total_deposit_amount_all_rows.toFixed(2));
    }

    // ============================================================================
    // TAG HANDLERS (Client-only tags)
    // ============================================================================

    // ============================================================================
    // UI INITIALIZATION
    // ============================================================================
    
    if (typeof flatpickr !== 'undefined') {
        flatpickr('#edu_service_start_date', {
            dateFormat: 'd/m/Y',
            allowInput: true
        });
    }

    $('.filter_btn').on('click', function(){
        $('.filter_panel').slideToggle();
    });

    //Service type on change div
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

    //Set select2 drop down box width
    $('#changeassignee').select2();
    $('#changeassignee').next('.select2-container').first().css('width', '220px');

    var windowsize = $(window).width();
    if(windowsize > 2000){
        $('.add_note').css('width','980px');
    }

    // ============================================================================
    // NOT PICKED CALL HANDLER
    // ============================================================================
    
    $(document).on('click', '.not_picked_call', function (e) {
        var clientName = App.getPageConfig('clientName') || 'user';
        clientName = clientName.charAt(0).toUpperCase() + clientName.slice(1).toLowerCase();

        var message = `Hi ${clientName},
We tried reaching you but couldn't connect. Please call us at 0396021330 or let us know a suitable time.
Please do not reply via SMS.
Bansal Immigration`;
      
        $('#messageText').val(message);
        $('#notPickedCallModal').modal('show');

        $('.sendMessage').off('click').on('click', function () {
            var message = $('#messageText').val();
            var not_picked_call = 1;
            var url = App.getUrl('notPickedCall') || App.getUrl('siteUrl') + '/not-picked-call';
            $.ajax({
                url: url,
                headers: { 'X-CSRF-TOKEN': App.getCsrf() },
                type: 'POST',
                datatype: 'json',
                data: {
                    id: App.getPageConfig('clientId'),
                    not_picked_call: not_picked_call,
                    message: message
                },
                success: function (response) {
                    var obj = typeof response === 'string' ? $.parseJSON(response) : response;
                    if (obj.not_picked_call == 1) {
                        alert(obj.message);
                    } else {
                        alert(obj.message);
                    }
                    if(typeof getallactivities === 'function') {
                        getallactivities();
                    }
                    $('#notPickedCallModal').modal('hide');
                }
            });
        });
    });

    // ============================================================================
    // SERVICE AND APPOINTMENT HANDLERS
    // ============================================================================
    
    // Global variables for appointment scheduling
    var duration, daysOfWeek, starttime, endtime, disabledtimeslotes;

    $(document).on('change', '.enquiry_item', function(){
        var id = $(this).val();
        if(id != ""){
            var v = 'services';
            if(id == 8){  //If nature of service == INDIA/UK/CANADA/EUROPE TO AUSTRALIA
                $('#serviceval_2').hide();
            } else {
                $('#serviceval_2').show();
            }

            $('.services_row').show();
            $('#myTab .nav-item #nature_of_enquiry-tab').addClass('disabled');
            $('#myTab .nav-item #services-tab').removeClass('disabled');
            $('#myTab a[href="#'+v+'"]').trigger('click');

            $('.services_item').prop('checked', false);
            $('.appointment_row').hide();
            $('.info_row').hide();
            $('.confirm_row').hide();

            $('.timeslots').html('');
            $('.showselecteddate').html('');

            $('#timeslot_col_date').val("");
            $('#timeslot_col_time').val("");
        } else {
            var v = 'nature_of_enquiry';
            $('.services_row').hide();
            $('.appointment_row').hide();
            $('.info_row').hide();
            $('.confirm_row').hide();

            $('#myTab .nav-item #services-tab').addClass('disabled');
            $('#myTab .nav-item #nature_of_enquiry-tab').removeClass('disabled');
            $('#myTab a[href="#'+v+'"]').trigger('click');
        }
        $('input[name="noe_id"]').val(id);
    });

    $(document).on('change', '.inperson_address', function() {
        var id = $("input[name='inperson_address']:checked").attr('data-val');
        if(id != ""){
            var v = 'info';
            $('.info_row').show();
            $('.appointment_details_cls').show();

            $('#myTab .nav-item #appointment_details-tab').addClass('disabled');
            $('#myTab .nav-item #info-tab').removeClass('disabled');
            $('#myTab a[href="#'+v+'"]').trigger('click');
        } else {
            var v = 'appointment_details';
            $('.info_row').hide();
            $('.appointment_details_cls').hide();
            $('.confirm_row').hide();

            $('#myTab .nav-item #info-tab').addClass('disabled');
            $('#myTab .nav-item #appointment_details-tab').removeClass('disabled');
            $('#myTab a[href="#'+v+'"]').trigger('click');
        }

        $("input[name='inperson_address']:checked").val(id);
        $('.timeslots').html('');
        if(id != ""){
            var enquiry_item  = $('.enquiry_item').val();
            var service_id = $("input[name='radioGroup']:checked").val();
            var inperson_address = $("input[name='inperson_address']:checked").attr('data-val');
            var url = App.getUrl('getDateTimeBackend') || App.getUrl('siteUrl') + '/getdatetimebackend';
            $.ajax({
                url: url,
                headers: { 'X-CSRF-TOKEN': App.getCsrf()},
                type:'POST',
                data:{id:service_id, enquiry_item:enquiry_item, inperson_address:inperson_address },
                datatype:'json',
                success:function(res){
                    var obj = typeof res === 'string' ? JSON.parse(res) : res;
                    if(obj.success){
                        duration = obj.duration;
                        daysOfWeek =  obj.weeks;
                        starttime =  obj.start_time;
                        endtime =  obj.end_time;
                        disabledtimeslotes = obj.disabledtimeslotes;
                        var datesForDisable = obj.disabledatesarray;

                        if (typeof flatpickr !== 'undefined') {
                            flatpickr('#datetimepicker', {
                                inline: true,
                                dateFormat: 'd/m/Y',
                                minDate: 'today',
                                disable: function(date) {
                                    var dateYear = date.getFullYear();
                                    var dateMonth = date.getMonth();
                                    var dateDay = date.getDate();
                                    
                                    var isDisabledDate = datesForDisable && datesForDisable.length > 0 && datesForDisable.some(function(disabledDate) {
                                        try {
                                            var disabledDateObj = disabledDate instanceof Date ? disabledDate : new Date(disabledDate);
                                            return disabledDateObj.getFullYear() === dateYear && 
                                                   disabledDateObj.getMonth() === dateMonth && 
                                                   disabledDateObj.getDate() === dateDay;
                                        } catch(e) {
                                            return false;
                                        }
                                    });
                                    var isDisabledDay = daysOfWeek && daysOfWeek.length > 0 && daysOfWeek.includes(date.getDay());
                                    return isDisabledDate || isDisabledDay;
                                },
                                onChange: function(selectedDates, dateStr, instance) {
                                    if (selectedDates.length > 0) {
                                        var date = dateStr;
                                        var checked_date = selectedDates[0].toLocaleDateString('en-US');

                                        $('.showselecteddate').html(date);
                                        $('input[name="date"]').val(date);
                                        $('#timeslot_col_date').val(date);

                                        $('.timeslots').html('');
                                        var start_time = parseTime(starttime);
                                        var end_time = parseTime(endtime);
                                        var interval = parseInt(duration);
                                        var service_id = $("input[name='radioGroup']:checked").val();
                                        var inperson_address = $("input[name='inperson_address']:checked").attr('data-val');
                                        var enquiry_item  = $('.enquiry_item').val();
                                        
                                        var disabledUrl = App.getUrl('getDisabledDateTime') || App.getUrl('siteUrl') + '/getdisableddatetime';
                                        $.ajax({
                                            url: disabledUrl,
                                            headers: {'X-CSRF-TOKEN': App.getCsrf()},
                                            type:'POST',
                                            data:{service_id:service_id,sel_date:date, enquiry_item:enquiry_item,inperson_address:inperson_address},
                                            datatype:'json',
                                            success:function(res){
                                                $('.timeslots').html('');
                                                var obj = typeof res === 'string' ? JSON.parse(res) : res;
                                                if(obj.success){
                                                    var objdisable = [];
                                                    if( $('#slot_overwrite_hidden').val() != 1){
                                                        objdisable = obj.disabledtimeslotes || [];
                                                    }

                                                    var start_timer = start_time;
                                                    for(var i = start_time; i<end_time; i = i+interval){
                                                        var timeString = start_timer + interval;
                                                        const timeString12hr = new Date('1970-01-01T' + convertHours(start_timer) + 'Z')
                                                            .toLocaleTimeString('en-US',
                                                                {timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}
                                                            );
                                                        const timetoString12hr = new Date('1970-01-01T' + convertHours(timeString) + 'Z')
                                                            .toLocaleTimeString('en-US',
                                                                {timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}
                                                            );

                                                        var today_date = new Date();
                                                        today_date = today_date.toLocaleDateString('en-US');

                                                        var now = new Date();
                                                        var nowTime = new Date('1/1/1900 ' + now.toLocaleTimeString(navigator.language, {
                                                            hour: '2-digit',
                                                            minute: '2-digit',
                                                            hour12: true
                                                        }));

                                                        var current_time=nowTime.toLocaleTimeString('en-US');
                                                        if(objdisable.length > 0){
                                                            if(objdisable.indexOf(timeString12hr) !== -1  ) {
                                                                // Skip disabled time
                                                            } else if ((checked_date == today_date) && (current_time > timeString12hr || current_time > timetoString12hr)){
                                                                // Skip past times for today
                                                            } else{
                                                                $('.timeslots').append('<div data-fromtime="'+timeString12hr+'" data-totime="'+timetoString12hr+'" style="cursor: pointer;" class="timeslot_col"><span>'+timeString12hr+'</span></div>');
                                                            }
                                                        } else{
                                                            if((checked_date == today_date) && (current_time > timeString12hr || current_time > timetoString12hr)){
                                                                // Skip past times for today
                                                            } else {
                                                                $('.timeslots').append('<div data-fromtime="'+timeString12hr+'" data-totime="'+timetoString12hr+'" style="cursor: pointer;" class="timeslot_col"><span>'+timeString12hr+'</span></div>');
                                                            }
                                                        }
                                                        start_timer = timeString;
                                                    }
                                                }
                                            }
                                        });
                                    }
                                }
                            });
                        }

                        if(id != ""){
                            var v = 'appointment_details';
                            $('#myTab .nav-item #services-tab').addClass('disabled');
                            $('#myTab .nav-item #appointment_details-tab').removeClass('disabled');
                            $('#myTab a[href="#'+v+'"]').trigger('click');
                        } else {
                            var v = 'services';
                            $('#myTab .nav-item #services-tab').removeClass('disabled');
                            $('#myTab .nav-item #appointment_details-tab').addClass('disabled');
                            $('#myTab a[href="#'+v+'"]').trigger('click');
                        }
                        $('input[name="service_id"]').val($("input[name='radioGroup']:checked").val());
                    } else {
                        $('input[name="service_id"]').val('');
                        var v = 'services';
                        alert('There is a problem in our system. please try again');
                        $('#myTab .nav-item #services-tab').removeClass('disabled');
                        $('#myTab .nav-item #appointment_details-tab').addClass('disabled');
                    }
                }
            });
        }
    });

    $(document).on('change', '.appointment_item', function(){
        var id = $(this).val();
        if(id != ""){
            $('input[name="appointment_details"]').val(id);
        } else {
            $('input[name="appointment_details"]').val("");
        }
    });

    $(document).on('change', '.services_item', function(){
        $('.info_row').hide();
        $('.confirm_row').hide();
        $("input[name='inperson_address']").prop("checked", false);
        $('.appointment_item').val("");
        $('.appointment_details_cls').hide();

        $('#timeslot_col_date').val("");
        $('#timeslot_col_time').val("");

        var id = $(this).val();
        if ($("input[name='radioGroup'][value='" + id + "']").prop("checked")) {
            $('#service_id').val(id);
        }

        if( $('#service_id').val() == 1 ){ //paid
            $('.submitappointment_paid').show();
            $('.submitappointment').hide();
        } else { //free
            $('.submitappointment').show();
            $('.submitappointment_paid').hide();
        }

        if(id != ""){
            var v = 'appointment_details';
            if( id == 1 ){ //paid service
                $('select[name="appointment_details"] option[value="zoom_google_meeting"]').show();
            } else {
                $('select[name="appointment_details"] option[value="zoom_google_meeting"]').hide();
            }
            $('.appointment_row').show();
        } else {
            var v = 'services';
            $('.appointment_row').hide();
        }
        $('.timeslots').html('');
        $('.showselecteddate').html('');

        // Similar AJAX call for getting datetime backend (code continues...)
        // This section will be completed in next iteration
    });

    // ============================================================================
    // TIME SLOT HANDLERS
    // ============================================================================
    
    $(document).on('click', '.timeslot_col', function(){
        $('.timeslot_col').removeClass('active');
        $(this).addClass('active');
        var service_id_val = $("input[name='radioGroup']:checked").val();
        var fromtime = $(this).attr('data-fromtime');
        
        // Page-specific function parseTimeLatest (not in common utilities)
        function parseTimeLatest(s) {
            var c = s.split(':');
            var c11 = c[1].split(' ');
            if(c11[1] == 'PM'){
                if(parseInt(c[0]) != 12 ){
                    return ( parseInt(c[0])+12 ) * 60 + parseInt(c[1]);
                } else {
                    return parseInt(c[0]) * 60 + parseInt(c[1]);
                }
            } else {
                return parseInt(c[0]) * 60 + parseInt(c[1]);
            }
        }
        
        if(service_id_val == 2){ //15 min service
            var fromtime11 = parseTimeLatest(fromtime);
            var interval11 = 15;
            var timeString11 = fromtime11 + interval11;
            var totime = new Date('1970-01-01T' + convertHours(timeString11) + 'Z')
                .toLocaleTimeString('en-US',
                    {timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}
                );
        } else {
            var totime = $(this).attr('data-totime');
        }
        $('input[name="time"]').val(fromtime+'-'+totime);
        $('#timeslot_col_time').val(fromtime+'-'+totime);
    });

    // Page-specific function: calculate_time_slot
    function calculate_time_slot(start_time, end_time, interval) {
        interval = interval || 15;
        var i, formatted_time;
        var time_slots = new Array();
        for(var i=start_time; i<=end_time; i = i+interval){
            formatted_time = convertHours(i);
            const timeString = formatted_time;
            time_slots.push(timeString);
        }
        return time_slots;
    }

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

    // ============================================================================
    // UI LAYOUT HANDLERS
    // ============================================================================
    
    $('#feather-icon').click(function(){
        var windowsize = $(window).width();
        if($('.main-sidebar').width() == 65){
            if(windowsize > 2000){
                $('.add_note').css('width','980px');
            } else {
                $('.add_note').css('width','155px');
            }
        } else if($('.main-sidebar').width() == 250) {
            if(windowsize > 2000){
                $('.add_note').css('width','1040px');
            } else {
                $('.add_note').css('width','215px');
            }
        }
    });

    //set height of right side section
    var left_upper_height = $('.left_section_upper').height();
    var left_section_lower = $('.left_section_lower').height();
    var total_left  = left_upper_height + left_section_lower;
    total_left = total_left +25;

    var right_section_height = $('.right_section').height();

    if(right_section_height >total_left ){
        var total_left_px = total_left+'px';
        $('.right_section').css({"maxHeight":total_left_px});
        $('.right_section').css({"overflow": 'scroll' });
    } else {
        var total_left_px = total_left+'px';
        $('.right_section').css({"maxHeight":total_left_px});
    }

    let css_property = {
        "display": "none",
    };
    $('#create_note_d').hide();
    $('.main-footer').css(css_property);

    // ============================================================================
    // MODAL HANDLERS
    // ============================================================================
    
    $(document).on('click', '.uploadmail', function(){
        $('#maclient_id').val(App.getPageConfig('clientId'));
        $('#uploadmail').modal('show');
    });

    // Create Client Receipt modal handler
    $(document).on('click', '.createclientreceipt', function(){
        // Reset form for new receipt
        if ($('#create_client_receipt').length) {
            $('#function_type').val('add');
            $('#top_value_db').val('');
            // Clear any existing rows from previous edits
            // Remove only dynamically added rows, keep the template clonedrow
            $('.productitem tr.product_field_clone').remove();
            // Remove all but the first clonedrow (keep template row)
            var clonedRows = $('.productitem tr.clonedrow');
            if (clonedRows.length > 1) {
                clonedRows.not(':first').remove();
            }
            // Clear all input values in the remaining clonedrow
            $('.productitem tr.clonedrow:first').find('input[type="text"], select').val('');
            $('.productitem tr.clonedrow:first').find('input[type="hidden"]').not('[name="trans_no[]"]').val('');
            $('.total_deposit_amount_all_rows').text('');
        }
        $('#createclientreceiptmodal').modal('show');
    });

    // Commission Invoice modal handler
    $(document).on('click', '.opencommissioninvoice', function(){
        $('#opencommissionmodal').modal('show');
    });

    // General Invoice modal handler
    $(document).on('click', '.opengeneralinvoice', function(){
        $('#opengeneralinvoice').modal('show');
    });

    // Tags popup modal handler
    $(document).on('click', '.opentagspopup', function(){
        var clientId = $(this).attr('data-id');
        if (clientId) {
            $('#tags_client_id').val(clientId);
        }
        var modalEl = document.getElementById('tags_clients');
        if (modalEl) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            } else if (typeof $ !== 'undefined' && $.fn.modal) {
                $(modalEl).modal('show');
            }
        }
    });

    // ============================================================================
    // ASSIGN USER HANDLER
    // ============================================================================
    
    $(document).on('click', '#assignUser', function(){
        $(".popuploader").show();
        var flag = true;
        var error = "";
        $(".custom-error").remove();
        
        if($('#rem_cat').val() == ''){
            $('.popuploader').hide();
            error="Assignee field is required.";
            $('#rem_cat').after("<span class='custom-error' role='alert'>"+error+"</span>");
            flag = false;
        }
        if($('#assignnote').val() == ''){
            $('.popuploader').hide();
            error="Note field is required.";
            $('#assignnote').after("<span class='custom-error' role='alert'>"+error+"</span>");
            flag = false;
        }
        if($('#task_group').val() == ''){
            $('.popuploader').hide();
            error="Group field is required.";
            $('#task_group').after("<span class='custom-error' role='alert'>"+error+"</span>");
            flag = false;
        }
        if(flag){
            var url = App.getUrl('clientFollowup') || App.getUrl('siteUrl') + '/clients/followup/store';
            $.ajax({
                type:'post',
                url: url,
                headers: { 'X-CSRF-TOKEN': App.getCsrf()},
                data: {
                    note_type:'follow_up',
                    description:$('#assignnote').val(),
                    client_id:$('#assign_client_id').val(),
                    followup_datetime:$('#popoverdatetime').val(),
                    assignee_name:$('#rem_cat :selected').text(),
                    rem_cat:$('#rem_cat option:selected').val(),
                    task_group:$('#task_group option:selected').val()
                },
                success: function(response){
                    $('.popuploader').hide();
                    var obj = typeof response === 'string' ? $.parseJSON(response) : response;
                    if(obj.success){
                        $("[data-role=popover]").each(function(){
                            (($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false
                        });
                        if(typeof getallactivities === 'function') {
                            getallactivities();
                        }
                        if(typeof getallnotes === 'function') {
                            getallnotes();
                        }
                    }
                }
            });
        }else{
            $("#loader").hide();
        }
    });

    // ============================================================================
    // OVERRIDE COMMON FUNCTIONS WITH PAGE-SPECIFIC IMPLEMENTATIONS
    // ============================================================================
    
    // Note: These functions override the common ones with more detailed implementations
    // If needed, these can be moved to common files later
    
    var appcid = '';
    $(document).on('click', '.publishdoc', function(){
        $('#confirmpublishdocModal').modal('show');
        appcid = $(this).attr('data-id');
    });

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

    // ============================================================================
    // DOCUMENT VERIFICATION HANDLERS
    // ============================================================================
    
    var verify_doc_id = '';
    var verify_doc_href = '';
    var verify_doc_type = '';
    $(document).on('click', '.verifydoc', function(){
        $('#confirmDocModal').modal('show');
        verify_doc_id = $(this).attr('data-id');
        verify_doc_href = $(this).attr('data-href');
        verify_doc_type = $(this).attr('data-doctype');
    });

    $(document).on('click', '#confirmDocModal .accept', function(){
        $('.popuploader').show();
        var baseUrl = App.getUrl('siteUrl') || '';
        $.ajax({
            url: baseUrl + '/' + verify_doc_href,
            type:'POST',
            headers: { 'X-CSRF-TOKEN': App.getCsrf()},
            datatype:'json',
            data:{doc_id:verify_doc_id, doc_type:verify_doc_type },
            success:function(response){
                $('.popuploader').hide();
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                $('#confirmDocModal').modal('hide');
                if(res.status){
                    if(res.doc_type == 'documents') {
                        $('.alldocumnetlist #docverifiedby_'+verify_doc_id).html(res.verified_by + "<br>" + res.verified_at);
                    }
                    if(typeof getallactivities === 'function') {
                        getallactivities();
                    }
                }
            }
        });
    });

    var notuse_doc_id = '';
    var notuse_doc_href = '';
    var notuse_doc_type = '';
    $(document).on('click', '.notuseddoc', function(){
        $('#confirmNotUseDocModal').modal('show');
        notuse_doc_id = $(this).attr('data-id');
        notuse_doc_href = $(this).attr('data-href');
        notuse_doc_type = $(this).attr('data-doctype');
    });

    $(document).on('click', '#confirmNotUseDocModal .accept', function(){
        $('.popuploader').show();
        var baseUrl = App.getUrl('siteUrl') || '';
        $.ajax({
            url: baseUrl + '/' + notuse_doc_href,
            type:'POST',
            headers: { 'X-CSRF-TOKEN': App.getCsrf()},
            datatype:'json',
            data:{doc_id:notuse_doc_id, doc_type:notuse_doc_type },
            success:function(response){
                $('.popuploader').hide();
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                $('#confirmNotUseDocModal').modal('hide');
                if(res.status){
                    if(res.doc_type == 'documents') {
                        $('.alldocumnetlist #id_'+res.doc_id).remove();
                    }
                    if(res.docInfo) {
                        var subArray = res.docInfo;
                        var trRow = "";
                        if(subArray.myfile_key != ''){
                            trRow += "<tr class='drow' id='id_"+subArray.id+"'><td>"+subArray.checklist+"</td><td>"+ res.Added_By + "<br>" + res.Added_date+"</td><td><a target='_blank' class='dropdown-item' href='"+subArray.myfile+"'><i class='fas fa-file-image'></i> <span>"+subArray.file_name+'.'+subArray.filetype+"</span></a></div></td><td>"+res.Verified_By+ "<br>" +res.Verified_At+"</td></tr>";
                        } else {
                            trRow += "<tr class='drow' id='id_"+subArray.id+"'><td>"+subArray.checklist+"</td><td>"+ res.Added_By + "<br>" + res.Added_date+"</td><td><i class='fas fa-file-image'></i> <span>"+subArray.file_name+'.'+subArray.filetype+"</span></div></td><td>"+res.Verified_By+ "<br>" +res.Verified_At+"</td></tr>";
                        }
                        $('.notuseddocumnetlist').append(trRow);
                    }
                    if(typeof getallactivities === 'function') {
                        getallactivities();
                    }
                }
            }
        });
    });

    var backto_doc_id = '';
    var backto_doc_href = '';
    var backto_doc_type = '';
    $(document).on('click', '.backtodoc', function(){
        $('#confirmBackToDocModal').modal('show');
        backto_doc_id = $(this).attr('data-id');
        backto_doc_href = $(this).attr('data-href');
        backto_doc_type = $(this).attr('data-doctype');
    });

    $(document).on('click', '#confirmBackToDocModal .accept', function(){
        $('.popuploader').show();
        var baseUrl = App.getUrl('siteUrl') || '';
        $.ajax({
            url: baseUrl + '/' + backto_doc_href,
            type:'POST',
            headers: { 'X-CSRF-TOKEN': App.getCsrf()},
            datatype:'json',
            data:{doc_id:backto_doc_id, doc_type:backto_doc_type },
            success:function(response){
                $('.popuploader').hide();
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                $('#confirmBackToDocModal').modal('hide');
                if(res.status){
                    if(res.doc_type == 'documents') {
                        $('.notuseddocumnetlist #id_'+res.doc_id).remove();
                    }
                    location.reload();
                }
            }
        });
    });

    // ============================================================================
    // DELETE HANDLERS
    // ============================================================================
    
    var notid = '';
    var delhref = '';
    $(document).on('click', '.deletenote', function(){
        $('#confirmModal').modal('show');
        notid = $(this).attr('data-id');
        delhref = $(this).attr('data-href');
    });

    $(document).on('click', '#confirmModal .accept', function(){
        $('.popuploader').show();
        var baseUrl = App.getUrl('siteUrl') || '';
        $.ajax({
            url: baseUrl + '/' + delhref,
            type:'GET',
            datatype:'json',
            data:{note_id:notid},
            success:function(response){
                $('.popuploader').hide();
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                $('#confirmModal').modal('hide');
                if(res.status){
                    $('#note_id_'+notid).remove();
                    if(res.status == true){
                        $('#id_'+notid).remove();
                    }

                    if(delhref == 'deletedocs'){
                        $('.documnetlist #id_'+notid).remove();
                    }
                    if(delhref == 'deletealldocs'){
                        $('.alldocumnetlist #id_'+notid).remove();
                    }
                    if(delhref == 'superagent'){
                        $('.supagent_data').remove();
                    }
                    if(delhref == 'subagent'){
                        $('.subagent_data').remove();
                    }
                    if(delhref == 'deleteservices'){
                        var url = App.getUrl('getServices') || App.getUrl('siteUrl') + '/get-services';
                        $.ajax({
                            url: url,
                            type:'GET',
                            data:{clientid: App.getPageConfig('clientId')},
                            success: function(responses){
                                $('.interest_serv_list').html(responses);
                            }
                        });
                    // NOTE: deletepaymentschedule handler removed - Invoice Schedule feature has been removed
                    }else if(delhref == 'deleteapplicationdocs'){
                        $('.mychecklistdocdata').html(res.doclistdata);
                        $('.checklistuploadcount').html(res.applicationuploadcount);
                        $('.'+res.type+'_checklists').html(res.checklistdata);

                        if(res.application_id){
                            var logsUrl = App.getUrl('getApplicationsLogs') || App.getUrl('siteUrl') + '/get-applications-logs';
                            $.ajax({
                                url: logsUrl,
                                type:'GET',
                                data:{id: res.application_id},
                                success: function(responses){
                                    $('#accordion').html(responses);
                                }
                            });
                        }
                    }else{
                        if(typeof getallnotes === 'function') {
                            getallnotes();
                        }
                    }

                    if(typeof getallactivities === 'function') {
                        getallactivities();
                    }
                }
            }
        });
    });

    var activitylogid = '';
    var delloghref = '';
    $(document).on('click', '.deleteactivitylog', function(){
        $('#confirmLogModal').modal('show');
        activitylogid = $(this).attr('data-id');
        delloghref = $(this).attr('data-href');
    });

    $(document).on('click', '#confirmLogModal .accept', function(){
        $('.popuploader').show();
        var baseUrl = App.getUrl('siteUrl') || '';
        $.ajax({
            url: baseUrl + '/' + delloghref,
            type:'GET',
            datatype:'json',
            data:{activitylogid:activitylogid},
            success:function(response){
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                $('#confirmLogModal').modal('hide');
                if(res.status){
                    $('#activity_'+activitylogid).remove();
                    if(typeof getallactivities === 'function') {
                        getallactivities();
                    }
                }
            }
        });
    });

    // ============================================================================
    // PIN HANDLERS
    // ============================================================================
    
    $(document).on('click', '.pinnote', function(){
        $('.popuploader').show();
        var url = App.getUrl('pinNote') || App.getUrl('siteUrl') + '/pinnote';
        $.ajax({
            url: url + '/',
            type:'GET',
            datatype:'json',
            data:{note_id:$(this).attr('data-id')},
            success:function(response){
                if(typeof getallnotes === 'function') {
                    getallnotes();
                }
            }
        });
    });

    $(document).on('click', '.pinactivitylog', function(){
        $('.popuploader').show();
        var url = App.getUrl('pinActivityLog') || App.getUrl('siteUrl') + '/pinactivitylog';
        $.ajax({
            url: url + '/',
            type:'GET',
            datatype:'json',
            data:{activity_id:$(this).attr('data-id')},
            success:function(response){
                if(typeof getallactivities === 'function') {
                    getallactivities();
                }
            }
        });
    });

    // ============================================================================
    // PUBLISH DOCUMENT HANDLER
    // ============================================================================
    
    $(document).on('click', '#confirmpublishdocModal .acceptpublishdoc', function(){
        $('.popuploader').show();
        var baseUrl = App.getUrl('siteUrl') || '';
        $.ajax({
            url: baseUrl + '/application/publishdoc',
            type:'GET',
            datatype:'json',
            data:{appid:appcid,status:'1'},
            success:function(response){
                $('.popuploader').hide();
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                $('#confirmpublishdocModal').modal('hide');
                if(res.status){
                    $('.mychecklistdocdata').html(res.doclistdata);
                }else{
                    alert(res.message);
                }
            }
        });
    });

    // ============================================================================
    // NOTE CREATION HANDLERS
    // ============================================================================
    
    // NOTE: createapplicationnewinvoice handler removed - Invoice Schedule feature has been removed

    $(document).on('click', '.create_note_d', function(){
        $('#create_note_d input[name="mailid"]').val(0);
        $('#create_note_d input[name="noteid"]').val('');
        $('#create_note_d #noteType').val('');
        $('#create_note_d #additionalFields').html('');
        $('#create_note_d .customerror').html('');
        $('#create_note_d .custom-error').html('');
        
        if($('#create_note_d .summernote-simple').length > 0){
            try {
                if($('#create_note_d .summernote-simple').data('summernote')){
                    $('#create_note_d .summernote-simple').summernote('code', '');
                } else {
                    $('#create_note_d .summernote-simple').val('');
                }
            } catch(e) {
                $('#create_note_d .summernote-simple').val('');
            }
        }
        
        $('#create_note_d #createNoteDModalLabel').html('Create Note');

        if($(this).attr('datatype') == 'note'){
            $('.is_not_note').hide();
        }else{
            var datasubject = $(this).attr('datasubject');
            var datamailid = $(this).attr('datamailid');
            $('#create_note_d input[name="title"]').val(datasubject);
            $('#create_note_d input[name="mailid"]').val(datamailid);
            $('.is_not_note').show();
        }
        
        $('#create_note_d').modal('show');
    });

    $('#noteType').on('change', function() {
        var selectedValue = $(this).val();
        var additionalFields = $("#additionalFields");
        additionalFields.html("");

        if(selectedValue === "Call") {
            additionalFields.append(`
                <div class="form-group" style="margin-top:10px;">
                    <label for="mobileNumber">Mobile Number:</label>
                    <select name="mobileNumber" id="mobileNumber" class="form-control" data-valid="required"></select>
                    <span id="mobileNumberError" class="text-danger"></span>
                </div>
            `);

            var client_id = $('#create_note_d #note_client_id').val();
            $('.popuploader').show();
            var url = App.getUrl('clientFetchContact') || App.getUrl('siteUrl') + '/clients/fetchClientContactNo';
            $.ajax({
                url: url,
                method: "POST",
                headers: { 'X-CSRF-TOKEN': App.getCsrf()},
                data: {client_id:client_id},
                datatype: 'json',
                success: function(response) {
                    $('.popuploader').hide();
                    var obj = typeof response === 'string' ? $.parseJSON(response) : response;
                    var contactlist = '<option value="">Select Contact</option>';
                    $.each(obj.clientContacts, function(index, subArray) {
                        var client_country_code = subArray.client_country_code || "";
                        contactlist += '<option value="'+client_country_code+' '+subArray.client_phone+'">'+client_country_code+' '+subArray.client_phone+' ('+subArray.contact_type+')</option>';
                    });
                    $('#mobileNumber').append(contactlist);
                }
            });
        }
    });

    $(document).on('click', '.create_note', function(){
        $('#create_note').modal('show');
        $('#create_note input[name="mailid"]').val(0);
        $('#create_note input[name="title"]').val('');
        $('#create_note #createNoteModalLabel').html('Create Note');
        $('#create_note input[name="noteid"]').val('');
        if($("#create_note .summernote-simple").length && typeof $.fn.summernote !== 'undefined') {
            $("#create_note .summernote-simple").summernote('code','');
        }
        if($(this).attr('datatype') == 'note'){
            $('.is_not_note').hide();
        }else{
            var datasubject = $(this).attr('datasubject');
            var datamailid = $(this).attr('datamailid');
            $('#create_note input[name="title"]').val(datasubject);
            $('#create_note input[name="mailid"]').val(datamailid);
            $('.is_not_note').show();
        }
    });

    // ============================================================================
    // SELECT2 INITIALIZATION FOR RECIPIENTS
    // ============================================================================
    
    $('.js-data-example-ajaxcc').select2({
        multiple: true,
        closeOnSelect: false,
        dropdownParent: $('#create_note'),
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

    $('.js-data-example-ajaxccapp').select2({
        multiple: true,
        closeOnSelect: false,
        dropdownParent: $('#applicationemailmodal'),
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

    function formatRepo (repo) {
        if (repo.loading) {
            return repo.text;
        }

        var $container = $(
            "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +
                "<div  class='ag-flex ag-align-start'>" +
                    "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
                    "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small ></div>" +
                "</div>" +
            "</div>" +
            "<div class='ag-flex ag-flex-column ag-align-end'>" +
                "<span class='ui label yellow select2-result-repository__statistics'>" +
                "</span>" +
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
    // COMPLETE SESSION HANDLER
    // ============================================================================
    
    $(document).on('click', '.complete_session', function(){
        var client_id = $(this).attr('data-clientid');
        if(client_id !=""){
            var url = App.getUrl('clientUpdateSession') || App.getUrl('siteUrl') + '/clients/update-session-completed';
            $.ajax({
                type:'POST',
                url: url,
                headers: { 'X-CSRF-TOKEN': App.getCsrf()},
                data: {client_id:client_id },
                success: function(response){
                    var obj = typeof response === 'string' ? $.parseJSON(response) : response;
                    if(obj.status){
                        alert(obj.message);
                        location.reload();
                    } else {
                        alert('Error: ' + obj.message);
                    }
                },
                error: function(xhr, status, error){
                    alert('Failed to complete session. Error: ' + error);
                }
            });
        } else {
            alert('Client ID is missing');
        }
    });

    // ============================================================================
    // NOTE VIEWING AND EDITING HANDLERS
    // ============================================================================
    
    $(document).on('click', '.opennoteform', function(){
        $('#create_note').modal('show');
        $('#create_note #createNoteModalLabel').html('Edit Note');
        var v = $(this).attr('data-id');
        $('#create_note input[name="noteid"]').val(v);
        $('.popuploader').show();
        var url = App.getUrl('getNoteDetail') || App.getUrl('siteUrl') + '/getnotedetail';
        $.ajax({
            url: url,
            type:'GET',
            datatype:'json',
            data:{note_id:v},
            success:function(response){
                $('.popuploader').hide();
                var res = typeof response === 'string' ? JSON.parse(response) : response;

                if(res.status){
                    $('#create_note input[name="title"]').val(res.data.title);
                    if($("#create_note .summernote-simple").length && typeof $.fn.summernote !== 'undefined') {
                        $("#create_note .summernote-simple").summernote('code',res.data.description);
                    } else {
                        $("#create_note .summernote-simple").val(res.data.description);
                    }
                }
            }
        });
    });

    $(document).on('click', '.viewnote', function(){
        $('#view_note').modal('show');
        var v = $(this).attr('data-id');
        $('#view_note input[name="noteid"]').val(v);
        $('.popuploader').show();
        var url = App.getUrl('viewNoteDetail') || App.getUrl('siteUrl') + '/viewnotedetail';
        $.ajax({
            url: url,
            type:'GET',
            datatype:'json',
            data:{note_id:v},
            success:function(response){
                $('.popuploader').hide();
                var res = typeof response === 'string' ? JSON.parse(response) : response;

                if(res.status){
                    $('#view_note .modal-body .note_content h5').html(res.data.title);
                    $("#view_note .modal-body .note_content p").html(res.data.description);
                }
            }
        });
    });

    $(document).on('click', '.viewapplicationnote', function(){
        $('#view_application_note').modal('show');
        var v = $(this).attr('data-id');
        $('#view_application_note input[name="noteid"]').val(v);
        $('.popuploader').show();
        var url = App.getUrl('viewApplicationNote') || App.getUrl('siteUrl') + '/viewapplicationnote';
        $.ajax({
            url: url,
            type:'GET',
            datatype:'json',
            data:{note_id:v},
            success:function(response){
                $('.popuploader').hide();
                var res = typeof response === 'string' ? JSON.parse(response) : response;

                if(res.status){
                    $('#view_application_note .modal-body .note_content h5').html(res.data.title);
                    $("#view_application_note .modal-body .note_content p").html(res.data.description);
                }
            }
        });
    });

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
                    var servicesUrl = App.getUrl('getServices') || App.getUrl('siteUrl') + '/get-services';
                    $.ajax({
                        url: servicesUrl,
                        type:'GET',
                        data:{clientid: App.getPageConfig('clientId')},
                        success: function(responses){
                            $('.interest_serv_list').html(responses);
                        }
                    });
                    var appListsUrl = App.getUrl('getApplicationLists') || App.getUrl('siteUrl') + '/get-application-lists';
                    $.ajax({
                        url: appListsUrl,
                        type:'GET',
                        datatype:'json',
                        data:{id: App.getPageConfig('clientId')},
                        success: function(responses){
                            $('.applicationtdata').html(responses);
                        }
                    });
                    $('.popuploader').hide();
                }
            });
        }
    });

    $(document).on('click', '#application-tab', function () {
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
    
    // Handle localStorage for active tab and application ID
    const activeTab = localStorage.getItem('activeTab');
    const appliid = localStorage.getItem('appliid');

    if (activeTab === 'application' && appliid != "") {
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

    // Handle clicking on application/course name to view details
    $(document).on('click', '.openapplicationdetail', function(){
        var appliid = $(this).attr('data-id');
        $('.if_applicationdetail').hide();
        $('.ifapplicationdetailnot').show();
        $('.popuploader').show();
        
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

    $(document).on('click', '#application-tab', function(){
        $('.if_applicationdetail').show();
        $('.ifapplicationdetailnot').hide();
        $('.ifapplicationdetailnot').html('<h4>Please wait ...</h4>');
    });

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
        $('input[name="refapp_id"]').val(appliid);
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
    
    // Calculate Tution Fee (sum of all fee inputs in Product Fee modal)
    function calculateTutionFee() {
        var totalFee = 0;
        var hasValues = false;
        
        // Sum all fee inputs
        $('.total_fee_am').each(function(){
            var val = parseFloat($(this).val());
            if(!isNaN(val) && val >= 0) {
                totalFee += val;
                hasValues = true;
            }
        });
        
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

    // NOTE: .deposit_amount_per_row handler has been moved to detail.blade.php inline script
    // to avoid duplication and ensure calculateReceiptTotal() is called properly

    // ============================================================================
    // APPOINTMENT CONFIRMATION HANDLER
    // ============================================================================
    
    $(document).on('click', '.nextbtn', function(){
        var v = $(this).attr('data-steps');
        $(".custom-error").remove();
        var flag = 1;
        if(v == 'confirm'){ //datetime
            $('#sendCodeBtn_txt').html("");
            $('#sendCodeBtn_txt').hide();
            var fullname = $('.fullname').val();
            var email = $('.email').val();
            var phone = $('.phone').val();
            var description = $('.description').val();
            var timeslot_col_date = $('#timeslot_col_date').val();
            var timeslot_col_time = $('#timeslot_col_time').val();

            var phoneRegex = /^\+?[0-9]{1,4}[-.\s]?[0-9]{10,}$/;
            var nameRegex = /^[a-zA-Z\s]+$/;

            var appointment_item = $('.appointment_item').val();
            if( !$.trim(appointment_item) ){
                flag = 0;
                $('.appointment_item').after('<span class="custom-error" role="alert">Appointment detail is required</span>');
            }
            if( !$.trim(fullname) ){
                flag = 0;
                $('.fullname').after('<span class="custom-error" role="alert">Fullname is required</span>');
            }
            else if (!nameRegex.test(fullname)) {
                flag = 0;
                $('.fullname').after('<span class="custom-error" role="alert">Full name must not contain special characters</span>');
            }
            if( !ValidateEmail(email) ){
                flag = 0;
                if(!$.trim(email)){
                    $('.email').after('<span class="custom-error" role="alert">Email is required.</span>');
                }else{
                    $('.email').after('<span class="custom-error" role="alert">You have entered an invalid email address!</span>');
                }
            }

            if( !$.trim(phone) ){
                flag = 0;
                $('#sendCodeBtn').after('<span class="custom-error" role="alert">Phone number is required</span>');
            } else if (!phoneRegex.test(phone)) {
                flag = 0;
                $('#sendCodeBtn').after('<span class="custom-error" role="alert">Phone must contain extension with phone.</span>');
            } else if( $('#phone_verified_bit').val() != "1" ){
                flag = 0;
                $('#sendCodeBtn').after('<span class="custom-error" role="alert">Phone number is not verified</span>');
            }

            if( !$.trim(description) ){
                flag = 0;
                $('.description').after('<span class="custom-error" role="alert">Description is required</span>');
            }
            if( !$.trim(timeslot_col_date) || !$.trim(timeslot_col_time)  ){
                flag = 0;
                $('.timeslot_col_date_time').after('<span class="custom-error" role="alert">Date and Time is required</span>');
            }
        }
        
        if(flag == 1 && v == 'confirm'){
            $('.confirm_row').show();
            $('#myTab .nav-item .nav-link').addClass('disabled');
            $('#myTab .nav-item #'+v+'-tab').removeClass('disabled');
            $('#myTab a[href="#'+v+'"]').trigger('click');

            $('.full_name').text($('.fullname').val());
            $('.email').text($('.email').val());
            $('.phone').text($('.phone').val());
            $('.description').text($('.description').val());
            $('.date').text($('input[name="date"]').val());
            $('.time').text($('input[name="time"]').val());

            if(  $("input[name='radioGroup']:checked").val() == 1 ){ //paid
                $('.submitappointment_paid').show();
                $('.submitappointment').hide();
            } else { //free
                $('.submitappointment').show();
                $('.submitappointment_paid').hide();
            }
        } else {
            $('.confirm_row').hide();
        }
    });

    // ============================================================================
    // RECEIPT HANDLERS
    // ============================================================================
    
    function getClientReceiptInfoById(id) {
        var url = App.getUrl('clientGetReceiptInfo') || App.getUrl('siteUrl') + '/clients/getClientReceiptInfoById';
        $.ajax({
            type:'post',
            url: url,
            headers: { 'X-CSRF-TOKEN': App.getCsrf()},
            sync:true,
            data: {id:id},
            success: function(response){
                var obj = typeof response === 'string' ? $.parseJSON(response) : response;

                if(obj.status){
                    $('#top_value_db').val(obj.last_record_id);

                    $('#function_type').val("edit");
                    $('#createclientreceiptmodal').modal('show');
                    if(obj.record_get){
                        var record_get = obj.record_get;
                        var sum = 0;
                        $('.productitem tr.clonedrow').remove();
                        $('.productitem tr.product_field_clone').remove();
                        $.each(record_get, function(index, subArray) {
                            var value_sum = parseFloat(subArray.deposit_amount);
                            if (!isNaN(value_sum)) {
                                sum += value_sum;
                            }
                            if(index <1 ){
                                var rowCls = 'clonedrow';
                            } else {
                                var rowCls = 'product_field_clone';
                            }
                            var trRows_client = '<tr class="'+rowCls+'"><td><input name="id[]" type="hidden" value="'+subArray.id+'" /><input data-valid="required" class="form-control report_date_fields" name="trans_date[]" type="text" value="'+subArray.trans_date+'" /></td><td><input data-valid="required" class="form-control report_entry_date_fields" name="entry_date[]" type="text" value="'+subArray.entry_date+'" /></td><td><input class="form-control unique_trans_no" type="text" value="'+subArray.trans_no+'" readonly/><input class="unique_trans_no_hidden" name="trans_no[]" type="hidden" value="'+subArray.trans_no+'" /></td><td><select class="form-control payment_method_cls" name="payment_method[]"><option value="">Select</option><option value="Cash">Cash</option><option value="Bank transfer">Bank transfer</option><option value="EFTPOS">EFTPOS</option></select></td><td><input data-valid="required" class="form-control" name="description[]" type="text" value="'+subArray.description+'" /></td><td><span class="currencyinput" style="display: inline-block;">$</span><input data-valid="required" style="display: inline-block;" class="form-control deposit_amount_per_row" name="deposit_amount[]" type="text" value="'+subArray.deposit_amount+'" /></td><td><a class="removeitems" href="javascript:;"><i class="fa fa-times"></i></a></td></tr>';
                            $('.productitem').append(trRows_client);

                            $('.productitem tr:last .payment_method_cls').val(subArray.payment_method);
                            
                            if (typeof flatpickr !== 'undefined') {
                                flatpickr('.report_date_fields:not(._flatpickr-initialized)', {
                                    dateFormat: 'd/m/Y',
                                    allowInput: true,
                                    onReady: function(selectedDates, dateStr, instance) {
                                        instance.element.classList.add('_flatpickr-initialized');
                                    }
                                });
                                flatpickr('.report_entry_date_fields:not(._flatpickr-initialized)', {
                                    dateFormat: 'd/m/Y',
                                    allowInput: true,
                                    onReady: function(selectedDates, dateStr, instance) {
                                        instance.element.classList.add('_flatpickr-initialized');
                                    }
                                });
                            }

                            if(index <1 ){
                                $('#receipt_id').val(subArray.receipt_id);
                            }
                        });
                        $('.total_deposit_amount_all_rows').text("$"+sum.toFixed(2));
                    }
                }
            }
        });
    }

    //On Close Hide all content from popups
    $('#createclientreceiptmodal').on('hidden.bs.modal', function() {
        $('#create_client_receipt')[0].reset();
        $('.total_deposit_amount_all_rows').text("");
        $('#sel_client_agent_id').val("").trigger('change');
        
        if (typeof flatpickr !== 'undefined') {
            flatpickr('.report_entry_date_fields', {
                dateFormat: 'd/m/Y',
                defaultDate: 'today',
                allowInput: true
            });
        }
    });

    // Make function available globally
    if(typeof window !== 'undefined') {
        window.getClientReceiptInfoById = getClientReceiptInfoById;
    }

    // ============================================================================
    // PAYMENT HANDLERS
    // ============================================================================
    
    $(document).on('click', '.addpaymentmodal', function(){
        var v = $(this).attr('data-invoiceid');
        var netamount = $(this).attr('data-netamount');
        var dueamount = $(this).attr('data-dueamount');
        $('#invoice_id').val(v);
        $('.invoicenetamount').html(netamount+' AUD');
        $('.totldueamount').html(dueamount);
        $('.totldueamount').attr('data-totaldue', dueamount);
        $('#addpaymentmodal').modal('show');
        $('.payment_field_clone').remove();
        $('.paymentAmount').val('');
    });

    $(document).on('keyup', '.paymentAmount', function(){
        grandtotal();
    });

    function grandtotal(){
        var p = 0;
        $('.paymentAmount').each(function(){
            if($(this).val() != ''){
                p += parseFloat($(this).val());
            }
        });

        var tamount = $('.totldueamount').attr('data-totaldue');
        var am = parseFloat(tamount) - parseFloat(p);
        $('.totldueamount').html(am.toFixed(2));
    }

    $('.add_payment_field a').on('click', function(){
        var clonedval = $('.payment_field .payment_field_row .payment_first_step').html();
        $('.payment_field .payment_field_row').append('<div class="payment_field_col payment_field_clone">'+clonedval+'</div>');
    });

    $('.add_fee_type a.fee_type_btn').on('click', function(){
        var clonedval = $('.fees_type_sec .fee_type_row .fees_type_col').html();
        $('.fees_type_sec .fee_type_row').append('<div class="custom_type_col fees_type_clone">'+clonedval+'</div>');
    });

    $(document).on('click', '.payment_field_col .field_remove_col a.remove_col', function(){
        var $tr = $(this).closest('.payment_field_clone');
        var trclone = $('.payment_field_clone').length;
        if(trclone > 0){
            $tr.remove();
            grandtotal();
        }
    });

    $(document).on('click', '.fees_type_sec .fee_type_row .fees_type_clone a.remove_btn', function(){
        var $tr = $(this).closest('.fees_type_clone');
        var trclone = $('.fees_type_clone').length;
        if(trclone > 0){
            $tr.remove();
            grandtotal();
        }
    });

    // Make function available globally
    if(typeof window !== 'undefined') {
        window.grandtotal = grandtotal;
    }

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

// ============================================================================
// DOCUMENT CONTEXT MENU (Right-Click) - Must be outside async wrapper
// ============================================================================

// Create context menu element
let contextMenu = null;
let currentDocumentRow = null;

function createContextMenu() {
    if (contextMenu) return contextMenu;
    
    contextMenu = document.createElement('ul');
    contextMenu.className = 'document-context-menu';
    contextMenu.id = 'documentContextMenu';
    document.body.appendChild(contextMenu);
    return contextMenu;
}

function showContextMenu(event, row) {
    event.preventDefault();
    event.stopPropagation();
    
    currentDocumentRow = row;
    const menu = createContextMenu();
    
    // Get document data from row
    const docId = row.getAttribute('data-doc-id');
    const checklistName = row.getAttribute('data-checklist-name') || '';
    const fileName = row.getAttribute('data-file-name') || '';
    const fileType = row.getAttribute('data-file-type') || '';
    const myfile = row.getAttribute('data-myfile') || '';
    const myfileKey = row.getAttribute('data-myfile-key') || '';
    const docType = row.getAttribute('data-doc-type') || '';
    const userRole = parseInt(row.getAttribute('data-user-role') || '0');
    
    // Clear existing menu items
    menu.innerHTML = '';
    
    // Build menu items
    if (checklistName) {
        menu.appendChild(createMenuItem('Rename Checklist', function() {
            // Find the row and trigger rename directly
            const row = document.querySelector(`.alldocumnetlist .drow[data-doc-id="${docId}"]`);
            if (row) {
                const parent = $(row).find('.personalchecklist-row');
                if (parent.length) {
                    parent.data('current-html', parent.html());
                    const opentime = parent.data('personalchecklistname');
                    parent.empty().append(
                        $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
                        $('<button class="btn btn-personalprimary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
                        $('<button class="btn btn-personaldanger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
                    );
                }
            }
            hideContextMenu();
        }));
    }
    
    if (fileName) {
        menu.appendChild(createMenuItem('Rename File Name', function() {
            // Find the row and trigger rename directly
            const row = document.querySelector(`.alldocumnetlist .drow[data-doc-id="${docId}"]`);
            if (row) {
                const parent = $(row).find('.doc-row');
                if (parent.length) {
                    parent.data('current-html', parent.html());
                    const opentime = parent.data('name');
                    parent.empty().append(
                        $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
                        $('<button class="btn btn-primary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
                        $('<button class="btn btn-danger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
                    );
                }
            }
            hideContextMenu();
        }));
    }
    
    menu.appendChild(createDivider());
    
    // Preview
    menu.appendChild(createMenuItem('Preview', function() {
        if (myfileKey) {
            // New file upload - open in new tab
            let fileUrl = myfile;
            // If myfile doesn't start with http, construct the full URL
            if (!fileUrl.startsWith('http://') && !fileUrl.startsWith('https://')) {
                // Ensure it starts with / for proper URL construction
                if (!fileUrl.startsWith('/')) {
                    fileUrl = '/' + fileUrl;
                }
                fileUrl = window.location.origin + fileUrl;
            }
            window.open(fileUrl, '_blank');
        } else {
            // Old file upload - construct AWS S3 URL and open in new tab
            const awsBucket = window.awsBucket || '';
            const awsRegion = window.awsRegion || '';
            const clientId = window.PageConfig?.clientId || '';
            
            if (awsBucket && awsRegion && clientId && myfile) {
                const fileUrl = `https://${awsBucket}.s3.${awsRegion}.amazonaws.com/${clientId}/${docType}/${myfile}`;
                window.open(fileUrl, '_blank');
            } else {
                console.error('Missing AWS configuration or file data for preview');
                alert('Unable to preview file. Missing configuration.');
            }
        }
        hideContextMenu();
    }));
    
    // Download
    menu.appendChild(createMenuItem('Download', function() {
        if (myfileKey) {
            // New file upload - try to find download element first
            const downloadEl = document.querySelector(`.download-file[data-filelink][data-filename="${myfileKey}"]`);
            if (downloadEl) {
                downloadEl.click();
            } else {
                // Create and trigger download via form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = (App.getUrl('downloadDocument') || App.getUrl('siteUrl') + '/download-document');
                form.target = '_blank';
                form.innerHTML = `
                    <input type="hidden" name="_token" value="${App.getCsrf()}">
                    <input type="hidden" name="filelink" value="${myfile}">
                    <input type="hidden" name="filename" value="${myfileKey}">
                `;
                document.body.appendChild(form);
                form.submit();
                setTimeout(() => form.remove(), 100);
            }
        } else {
            // Old file upload
            const url = 'https://' + (window.awsBucket || '') + '.s3.' + (window.awsRegion || '') + '.amazonaws.com/';
            const clientId = window.PageConfig?.clientId || '';
            const fileUrl = url + clientId + '/' + docType + '/' + myfile;
            const downloadEl = document.querySelector(`.download-file[data-filelink*="${myfile}"]`);
            if (downloadEl) {
                downloadEl.click();
            } else {
                // Direct download via anchor
                const a = document.createElement('a');
                a.href = fileUrl;
                a.download = fileName + '.' + fileType;
                a.style.display = 'none';
                document.body.appendChild(a);
                a.click();
                setTimeout(() => document.body.removeChild(a), 100);
            }
        }
        hideContextMenu();
    }));
    
    menu.appendChild(createDivider());
    
    // Delete (only for super admin)
    if (userRole === 1) {
        menu.appendChild(createMenuItem('Delete', function() {
            const deleteEl = document.querySelector(`.deletenote[data-id="${docId}"][data-href="deletealldocs"]`);
            if (deleteEl) {
                if (confirm('Are you sure you want to delete this document?')) {
                    deleteEl.click();
                }
            }
            hideContextMenu();
        }));
    }
    
    // Not Used
    menu.appendChild(createMenuItem('Not Used', function() {
        // Create a temporary element to trigger the existing handler
        const tempEl = document.createElement('a');
        tempEl.className = 'dropdown-item notuseddoc';
        tempEl.setAttribute('data-id', docId);
        tempEl.setAttribute('data-href', 'notuseddoc');
        tempEl.setAttribute('data-doctype', docType || 'documents');
        tempEl.style.display = 'none';
        document.body.appendChild(tempEl);
        
        // Trigger click to use existing handler
        $(tempEl).trigger('click');
        
        // Clean up
        setTimeout(() => {
            if (tempEl.parentNode) {
                tempEl.parentNode.removeChild(tempEl);
            }
        }, 100);
        
        hideContextMenu();
    }));
    
    // Position menu
    menu.style.left = event.pageX + 'px';
    menu.style.top = event.pageY + 'px';
    menu.classList.add('show');
    
    // Hide menu on outside click
    setTimeout(() => {
        document.addEventListener('click', hideContextMenu, { once: true });
        document.addEventListener('contextmenu', hideContextMenu, { once: true });
    }, 0);
}

function hideContextMenu() {
    if (contextMenu) {
        contextMenu.classList.remove('show');
    }
    currentDocumentRow = null;
}

function createMenuItem(text, onClick) {
    const li = document.createElement('li');
    const a = document.createElement('a');
    a.textContent = text;
    a.href = 'javascript:;';
    a.addEventListener('click', onClick);
    li.appendChild(a);
    return li;
}

function createDivider() {
    const li = document.createElement('li');
    li.className = 'divider';
    return li;
}
    
// Attach context menu to document rows
document.addEventListener('DOMContentLoaded', function() {
    console.log('[Context Menu] DOMContentLoaded fired');
    console.log('[Context Menu] Looking for .document-row elements:', document.querySelectorAll('.document-row').length);
    
    // Handle right-click on document rows - works on entire row
    // Use capture phase to catch events before they bubble
    document.addEventListener('contextmenu', function(e) {
        console.log('[Context Menu] Context menu event fired on:', e.target);
        
        // Check if click is on a document row or any element inside it
        const row = e.target.closest('.document-row');
        console.log('[Context Menu] Found row:', row);
        
        if (row) {
            // Check if the click is on an interactive element that should have its own behavior
            const isInteractiveElement = e.target.closest('a[href]:not([href^="javascript:"]), button:not([type="button"]), input, textarea, select, [contenteditable="true"]');
            
            console.log('[Context Menu] Is interactive element:', isInteractiveElement);
            
            // If it's not an interactive element, show our context menu
            if (!isInteractiveElement) {
                // Prevent default browser context menu
                e.preventDefault();
                e.stopPropagation();
                
                console.log('[Context Menu] Showing context menu');
                
                // Show our custom context menu
                showContextMenu(e, row);
                return false;
            }
        }
    }, true); // Use capture phase to catch events early
        
        // Also handle dynamically added rows
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) {
                        // Check if the node itself is a document row
                        if (node.classList && node.classList.contains('document-row')) {
                            // Row will work through event delegation
                            node.style.cursor = 'context-menu';
                        }
                        // Check if any child is a document row
                        const childRows = node.querySelectorAll ? node.querySelectorAll('.document-row') : [];
                        childRows.forEach(function(row) {
                            // Ensure row has proper styling for context menu
                            row.style.cursor = 'context-menu';
                            // Also set cursor on all cells
                            const cells = row.querySelectorAll('td');
                            cells.forEach(function(cell) {
                                cell.style.cursor = 'context-menu';
                            });
                        });
                    }
                });
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        // Ensure existing rows have proper cursor style
        function styleDocumentRows() {
            document.querySelectorAll('.document-row').forEach(function(row) {
                row.style.cursor = 'context-menu';
                // Also set cursor on all cells
                const cells = row.querySelectorAll('td');
                cells.forEach(function(cell) {
                    cell.style.cursor = 'context-menu';
                });
            });
        }
        
        // Style existing rows
        styleDocumentRows();
        
        // Also style after a short delay to catch any rows added during page load
        setTimeout(styleDocumentRows, 500);
        
        console.log('[Context Menu] Initialized for document rows');
    });

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
// Functions like getTopReceiptValInDB, grandtotalAccountTab, etc. will be added

