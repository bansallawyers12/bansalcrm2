/**
 * Legacy Initialization Script
 * 
 * This file contains all the inline initialization code that was previously
 * in admin.blade.php. It waits for vendor libraries to be ready before executing.
 */

// Wait for vendor libraries to be ready, then initialize all components
(async function() {
    // Wait for vendorLibsReady promise (created in vendor-libs.js)
    if (typeof window.vendorLibsReady !== 'undefined') {
        await window.vendorLibsReady;
        console.log('Vendor libraries ready, initializing components...');
    } else {
        // Fallback: wait for event or check periodically
        await new Promise((resolve) => {
            const check = () => {
                if (typeof window.$ !== 'undefined' &&
                    typeof window.$.fn.select2 === 'function') {
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

    // Now initialize all components
    function initializeComponents() {
        $(document).ready(function () {
           
            $(".tel_input").on("blur", function() {
                this.value = this.value;
            });
            
            // Initialize select2 for assignee dropdown
            if (typeof $.fn.select2 !== 'undefined') {
                $('.assineeselect2').select2({
                   dropdownParent: $('#checkinmodal'),
                });
            }
         
            // Modern search is now initialized in modern-search.js
            // This provides better performance with debouncing, keyboard shortcuts, and categorization
            

            $(document).delegate('.opencheckin', 'click', function(){
                $('#checkinmodal').modal('show');
            });
            
            $(document).delegate('.visitpurpose', 'blur', function(){
                var visitpurpose = $(this).val();
                var appliid = $(this).attr('data-id');
                $('.popuploader').show();
                $.ajax({
                    url: site_url+'/update_visit_purpose',
                    type:'POST',
                    data:{id: appliid,visit_purpose:visitpurpose},
                    success: function(responses){
                         $.ajax({
                            url: site_url+'/get-checkin-detail',
                            type:'GET',
                            data:{id: appliid},
                            success: function(res){
                                 $('.popuploader').hide();
                                $('.showchecindetail').html(res);
                            }
                        });
                        
                    }
                });
            });
            
            $(document).delegate('.savevisitcomment', 'click', function(){
                var visitcomment = $('.visit_comment').val();
                var appliid = $(this).attr('data-id');
                $('.popuploader').show();
                $.ajax({
                    url: site_url+'/update_visit_comment',
                    type:'POST',
                    data:{id: appliid,visit_comment:visitcomment},
                    success: function(responses){
                        // $('.popuploader').hide();
                        $('.visit_comment').val('');
                        $.ajax({
                            url: site_url+'/get-checkin-detail',
                            type:'GET',
                            data:{id: appliid},
                            success: function(res){
                                 $('.popuploader').hide();
                                $('.showchecindetail').html(res);
                            }
                        });
                    }
                });
            });
            
            $(document).delegate('.attendsession', 'click', function(){
                var appliid = $(this).attr('data-id');
                $('.popuploader').show();
                $.ajax({
                    url: site_url+'/attend_session',
                    type:'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data:{id: appliid,waitcountdata: $('#waitcountdata').val()},
                    success: function(response){
                         var obj = $.parseJSON(response);
                        if(obj.status){
                            $.ajax({
                            url: site_url+'/get-checkin-detail',
                            type:'GET',
                            data:{id: appliid},
                            success: function(res){
                                 $('.popuploader').hide();
                                $('.showchecindetail').html(res);
                            }
                        });
                            $('.checindata #id_'+appliid).remove();
                        }else{
                            alert(obj.message);
                        }
                    }
                });
            });
            
            $(document).delegate('.completesession', 'click', function(){
                var appliid = $(this).attr('data-id');
                var attendcountdata = $('#attendcountdata').val();
                console.log('Complete Session clicked - ID:', appliid, 'Attend Count:', attendcountdata);
                $('.popuploader').show();
                $.ajax({
                    url: site_url+'/complete_session',
                    type:'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data:{id: appliid, attendcountdata: attendcountdata},
                    success: function(response){
                        console.log('Complete Session Response:', response);
                        try {
                            var obj = $.parseJSON(response);
                            if(obj.status){
                                // Update office-visits tab badges (Attending / Completed / Waiting) if counts returned
                                if (obj.attending !== undefined && obj.completed !== undefined && obj.waiting !== undefined) {
                                    $('#attending-tab .countAction').text(obj.attending);
                                    $('#completed-tab .countAction').text(obj.completed);
                                    $('#waiting-tab .countAction').text(obj.waiting);
                                }
                                $.ajax({
                                    url: site_url+'/get-checkin-detail',
                                    type:'GET',
                                    data:{id: appliid},
                                    success: function(res){
                                        $('.popuploader').hide();
                                        $('.showchecindetail').html(res);
                                        $('#checkindetailmodal').modal('hide');
                                        alert('Session completed successfully!');
                                    },
                                    error: function(xhr, status, error){
                                        $('.popuploader').hide();
                                        console.error('Error fetching checkin detail:', error);
                                        alert('Session completed but failed to refresh details.');
                                    }
                                });
                                $('.checindata #id_'+appliid).remove();
                            }else{
                                $('.popuploader').hide();
                                alert(obj.message);
                            }
                        } catch(e) {
                            $('.popuploader').hide();
                            console.error('Error parsing response:', e);
                            alert('Error processing response');
                        }
                    },
                    error: function(xhr, status, error){
                        $('.popuploader').hide();
                        console.error('AJAX Error:', {xhr: xhr, status: status, error: error});
                        alert('Failed to complete session. Error: ' + error + '\nStatus: ' + xhr.status + '\nResponse: ' + xhr.responseText);
                    }
                });
            });
            
            $(document).delegate('.opencheckindetail', 'click', function(){
                var appliid = $(this).attr('id');
                $('#checkindetailmodal').data('currentCheckinId', appliid);
                $('#checkindetailmodal').modal('show');
                $('.popuploader').show();
                $.ajax({
                        url: site_url+'/get-checkin-detail',
                        type:'GET',
                        data:{id: appliid},
                        success: function(responses){
                             $('.popuploader').hide();
                            $('.showchecindetail').html(responses);
                        }
                    });
            });
            
            // intlTelInput plugin no longer used - replaced with country code select dropdowns
            
            // All the table column show/hide logic
            $('.drop_table_data button').on('click', function(){
                $('.client_dropdown_list').toggleClass('active');
            });
            $('.client_dropdown_list label.dropdown-option input').on('click', function(){
                var val = $(this).val();
                if(val == 'all'){ 
                    if ($(this).is(":checked")) {
                        $('.client_table_data table tr td').show();
                        $('.client_table_data table tr th').show();
                        $('.client_dropdown_list label.dropdown-option input').prop('checked', true);
                    } else{
                        $('.client_dropdown_list label.dropdown-option input').prop('checked', false);
                        $('.client_table_data table tr td').hide(); 
                        $('.client_table_data table tr th').hide();
                        $('.client_table_data table tr td:nth-child(1)').show();
                        $('.client_table_data table tr th:nth-child(1)').show();
                        $('.client_table_data table tr td:nth-child(2)').show();
                        $('.client_table_data table tr th:nth-child(2)').show();
                        $('.client_table_data table tr td:nth-child(17)').show();
                        $('.client_table_data table tr th:nth-child(17)').show();
                    }
                } else{
                     if ($(this).is(":checked")) {
                        $('.client_table_data table tr td:nth-child('+val+')').show();
                        $('.client_table_data table tr th:nth-child('+val+')').show();
                      }
                      else{
                        $('.client_dropdown_list label.dropdown-option.all input').prop('checked', false);
                        $('.client_table_data table tr td:nth-child('+val+')').hide();
                        $('.client_table_data table tr th:nth-child('+val+')').hide();
                      }
                }
            });
            
            // Student dropdown logic (active)
            $('.student_drop_table_data button').on('click', function(){
                $('.student_dropdown_list').toggleClass('active');
            });
            $('.student_dropdown_list label.dropdown-option input').on('click', function(){
                var val = $(this).val();
                if(val == 'all'){
                    if ($(this).is(":checked")) {
                        $('.student_table_data table tr td').show();
                        $('.student_table_data table tr th').show();
                        $('.student_dropdown_list label.dropdown-option input').prop('checked', true);
                    } else {
                        $('.student_dropdown_list label.dropdown-option input').prop('checked', false);
                        $('.student_table_data table tr td').hide();
                        $('.student_table_data table tr th').hide();
                        $('.student_table_data table tr td:nth-child(1)').show();
                        $('.student_table_data table tr th:nth-child(1)').show();
                        $('.student_table_data table tr td:nth-child(2)').show();
                        $('.student_table_data table tr th:nth-child(2)').show();
                        $('.student_table_data table tr td:nth-child(22)').show();
                        $('.student_table_data table tr th:nth-child(22)').show();
                    }
                } else {
                    if ($(this).is(":checked")) {
                        $('.student_table_data table tr td:nth-child('+val+')').show();
                        $('.student_table_data table tr th:nth-child('+val+')').show();
                    } else{
                        $('.student_dropdown_list label.dropdown-option.all input').prop('checked', false);
                        $('.student_table_data table tr td:nth-child('+val+')').hide();
                        $('.student_table_data table tr th:nth-child('+val+')').hide();
                    }
                }
            });
            
            // Student dropdown logic (inactive)
            $('.student_drop_table_data1 button').on('click', function(){
                $('.student_dropdown_list1').toggleClass('active');
            });
            $('.student_dropdown_list1 label.dropdown-option input').on('click', function(){
                var val = $(this).val();
                if(val == 'all'){
                    if ($(this).is(":checked")) {
                        $('.student_table_data1 table tr td').show();
                        $('.student_table_data1 table tr th').show();
                        $('.student_dropdown_list1 label.dropdown-option input').prop('checked', true);
                    } else {
                        $('.student_dropdown_list1 label.dropdown-option input').prop('checked', false);
                        $('.student_table_data1 table tr td').hide();
                        $('.student_table_data1 table tr th').hide();
                        $('.student_table_data1 table tr td:nth-child(1)').show();
                        $('.student_table_data1 table tr th:nth-child(1)').show();
                        $('.student_table_data1 table tr td:nth-child(2)').show();
                        $('.student_table_data1 table tr th:nth-child(2)').show();
                        $('.student_table_data1 table tr td:nth-child(22)').show();
                        $('.student_table_data1 table tr th:nth-child(22)').show();
                    }
                } else {
                    if ($(this).is(":checked")) {
                        $('.student_table_data1 table tr td:nth-child('+val+')').show();
                        $('.student_table_data1 table tr th:nth-child('+val+')').show();
                    } else{
                        $('.student_dropdown_list1 label.dropdown-option.all input').prop('checked', false);
                        $('.student_table_data1 table tr td:nth-child('+val+')').hide();
                        $('.student_table_data1 table tr th:nth-child('+val+')').hide();
                    }
                }
            });
            
            // Client report dropdown
            $('.drop_table_data button').on('click', function(){
                $('.client_report_list').toggleClass('active');
            });
            $('.client_report_list label.dropdown-option input').on('click', function(){
                var val = $(this).val();
                if(val == 'all'){
                    if ($(this).is(":checked")) {
                        $('.client_report_data table tr td').show();
                        $('.client_report_data table tr th').show();
                        $('.client_report_list label.dropdown-option input').prop('checked', true);
                    } else{
                        $('.client_report_list label.dropdown-option input').prop('checked', false);
                        $('.client_report_data table tr td').hide(); 
                        $('.client_report_data table tr th').hide();
                        $('.client_report_data table tr td:nth-child(1)').show();
                        $('.client_report_data table tr th:nth-child(1)').show();
                        $('.client_report_data table tr td:nth-child(2)').show();
                        $('.client_report_data table tr th:nth-child(2)').show();
                        $('.client_report_data table tr td:nth-child(11)').show();
                        $('.client_report_data table tr th:nth-child(11)').show();
                    }
                } else{
                     if ($(this).is(":checked")) {
                        $('.client_report_data table tr td:nth-child('+val+')').show();
                        $('.client_report_data table tr th:nth-child('+val+')').show();
                      }
                      else{
                        $('.client_report_list label.dropdown-option.all input').prop('checked', false);
                        $('.client_report_data table tr td:nth-child('+val+')').hide();
                        $('.client_report_data table tr th:nth-child('+val+')').hide();
                      }
                }
            });
            
            // Application report dropdown
            $('.drop_table_data button').on('click', function(){
                $('.application_report_list').toggleClass('active');
            });
            $('.application_report_list label.dropdown-option input').on('click', function(){
                var val = $(this).val();
                if(val == 'all'){
                    if ($(this).is(":checked")) {
                        $('.application_report_data table tr td').show();
                        $('.application_report_data table tr th').show();
                        $('.application_report_list label.dropdown-option input').prop('checked', true);
                    } else{
                        $('.application_report_list label.dropdown-option input').prop('checked', false);
                        $('.application_report_data table tr td').hide(); 
                        $('.application_report_data table tr th').hide();
                        $('.application_report_data table tr td:nth-child(1)').show();
                        $('.application_report_data table tr th:nth-child(1)').show();
                        $('.application_report_data table tr td:nth-child(2)').show();
                        $('.application_report_data table tr th:nth-child(2)').show();
                        $('.application_report_data table tr td:nth-child(3)').show();
                        $('.application_report_data table tr th:nth-child(3)').show();
                        $('.application_report_data table tr td:nth-child(5)').show();
                        $('.application_report_data table tr th:nth-child(5)').show(); 
                        $('.application_report_data table tr td:nth-child(7)').show();
                        $('.application_report_data table tr th:nth-child(7)').show(); 
                    }
                } else{
                     if ($(this).is(":checked")) {
                        $('.application_report_data table tr td:nth-child('+val+')').show();
                        $('.application_report_data table tr th:nth-child('+val+')').show();
                      }
                      else{
                        $('.application_report_list label.dropdown-option.all input').prop('checked', false);
                        $('.application_report_data table tr td:nth-child('+val+')').hide();
                        $('.application_report_data table tr th:nth-child('+val+')').hide();
                      }
                }
            });
            
            // Office visit report dropdown
            $('.drop_table_data button').on('click', function(){
                $('.officevisit_report_list').toggleClass('active');
            });
            $('.officevisit_report_list label.dropdown-option input').on('click', function(){
                var val = $(this).val();
                if(val == 'all'){
                    if ($(this).is(":checked")) {
                        $('.officevisit_report_data table tr td').show();
                        $('.officevisit_report_data table tr th').show();
                        $('.officevisit_report_list label.dropdown-option input').prop('checked', true);
                    } else{
                        $('.officevisit_report_list label.dropdown-option input').prop('checked', false);
                        $('.officevisit_report_data table tr td').hide(); 
                        $('.officevisit_report_data table tr th').hide();
                        $('.officevisit_report_data table tr td:nth-child(1)').show();
                        $('.officevisit_report_data table tr th:nth-child(1)').show();
                        $('.officevisit_report_data table tr td:nth-child(2)').show();
                        $('.officevisit_report_data table tr th:nth-child(2)').show();
                        $('.officevisit_report_data table tr td:nth-child(4)').show();
                        $('.officevisit_report_data table tr th:nth-child(4)').show(); 
                    }
                } else{
                     if ($(this).is(":checked")) {
                        $('.officevisit_report_data table tr td:nth-child('+val+')').show();
                        $('.officevisit_report_data table tr th:nth-child('+val+')').show();
                      }
                      else{
                        $('.officevisit_report_list label.dropdown-option.all input').prop('checked', false);
                        $('.officevisit_report_data table tr td:nth-child('+val+')').hide();
                        $('.officevisit_report_data table tr th:nth-child('+val+')').hide();
                      }
                }
            });
            
            // Invoice report dropdown
            $('.drop_table_data button').on('click', function(){
                $('.invoice_report_list').toggleClass('active');
            });
            $('.invoice_report_list label.dropdown-option input').on('click', function(){
                var val = $(this).val();
                if(val == 'all'){
                    if ($(this).is(":checked")) { 
                        $('.invoice_report_data table tr td').show();
                        $('.invoice_report_data table tr th').show();
                        $('.invoice_report_list label.dropdown-option input').prop('checked', true);
                    } else{
                        $('.invoice_report_list label.dropdown-option input').prop('checked', false);
                        $('.invoice_report_data table tr td').hide(); 
                        $('.invoice_report_data table tr th').hide();
                        $('.invoice_report_data table tr td:nth-child(1)').show();
                        $('.invoice_report_data table tr th:nth-child(1)').show();
                        $('.invoice_report_data table tr td:nth-child(2)').show();
                        $('.invoice_report_data table tr th:nth-child(2)').show();
                        $('.invoice_report_data table tr td:nth-child(4)').show();
                        $('.invoice_report_data table tr th:nth-child(4)').show(); 
                    } 
                } else{
                     if ($(this).is(":checked")) {
                        $('.invoice_report_data table tr td:nth-child('+val+')').show();
                        $('.invoice_report_data table tr th:nth-child('+val+')').show();
                      }
                      else{
                        $('.invoice_report_list label.dropdown-option.all input').prop('checked', false);
                        $('.invoice_report_data table tr td:nth-child('+val+')').hide();
                        $('.invoice_report_data table tr th:nth-child('+val+')').hide();
                      }
                }
            });
            
            // Sales forecast report dropdown
            $('.drop_table_data button').on('click', function(){
                $('.saleforecast_applic_report_list').toggleClass('active');
            });
            $('.saleforecast_applic_report_list label.dropdown-option input').on('click', function(){
                var val = $(this).val();
                if(val == 'all'){
                    if ($(this).is(":checked")) { 
                        $('.saleforecast_application_report_data table tr td').show();
                        $('.saleforecast_application_report_data table tr th').show();
                        $('.saleforecast_applic_report_list label.dropdown-option input').prop('checked', true);
                    } else{
                        $('.saleforecast_applic_report_list label.dropdown-option input').prop('checked', false);
                        $('.saleforecast_application_report_data table tr td').hide(); 
                        $('.saleforecast_application_report_data table tr th').hide();
                        $('.saleforecast_application_report_data table tr td:nth-child(1)').show();
                        $('.saleforecast_application_report_data table tr th:nth-child(1)').show();
                        $('.saleforecast_application_report_data table tr td:nth-child(2)').show();
                        $('.saleforecast_application_report_data table tr th:nth-child(2)').show();
                        $('.saleforecast_application_report_data table tr td:nth-child(4)').show();
                        $('.saleforecast_application_report_data table tr th:nth-child(4)').show(); 
                    } 
                } else{
                     if ($(this).is(":checked")) {
                        $('.saleforecast_application_report_data table tr td:nth-child('+val+')').show();
                        $('.saleforecast_application_report_data table tr th:nth-child('+val+')').show();
                      }
                      else{
                        $('.saleforecast_applic_report_list label.dropdown-option.all input').prop('checked', false);
                        $('.saleforecast_application_report_data table tr td:nth-child('+val+')').hide();
                        $('.saleforecast_application_report_data table tr th:nth-child('+val+')').hide();
                      }
                }
            });
            
            // Interest service report dropdown
            $('.drop_table_data button').on('click', function(){
                $('.interest_service_report_list').toggleClass('active');
            });
            $('.interest_service_report_list label.dropdown-option input').on('click', function(){
                var val = $(this).val();
                if(val == 'all'){
                    if ($(this).is(":checked")) { 
                        $('.interest_service_report_data table tr td').show();
                        $('.interest_service_report_data table tr th').show();
                        $('.interest_service_report_list label.dropdown-option input').prop('checked', true);
                    } else{
                        $('.interest_service_report_list label.dropdown-option input').prop('checked', false);
                        $('.interest_service_report_data table tr td').hide(); 
                        $('.interest_service_report_data table tr th').hide();
                        $('.interest_service_report_data table tr td:nth-child(1)').show();
                        $('.interest_service_report_data table tr th:nth-child(1)').show();
                        $('.interest_service_report_data table tr td:nth-child(2)').show();
                        $('.interest_service_report_data table tr th:nth-child(2)').show();
                        $('.interest_service_report_data table tr td:nth-child(10)').show();
                        $('.interest_service_report_data table tr th:nth-child(10)').show(); 
                        $('.interest_service_report_data table tr td:nth-child(14)').show();
                        $('.interest_service_report_data table tr th:nth-child(14)').show(); 
                    } 
                } else{
                     if ($(this).is(":checked")) {
                        $('.interest_service_report_data table tr td:nth-child('+val+')').show();
                        $('.interest_service_report_data table tr th:nth-child('+val+')').show();
                      }
                      else{
                        $('.interest_service_report_list label.dropdown-option.all input').prop('checked', false);
                        $('.interest_service_report_data table tr td:nth-child('+val+')').hide();
                        $('.interest_service_report_data table tr th:nth-child('+val+')').hide();
                      }
                }
            });
            
            // Form field visibility logic
            $('#personal_details .is_business').hide();
            $('#office_income_share .is_super_agent').hide();
            $('#office_income_share .is_sub_agent').hide();
            $('.modal-body form#addgroupinvoice .is_superagentinv').hide();
            
            $('#agentstructure input[name="struture"]').on('change', function(){
                var id = $(this).attr('id'); 
                if(id == 'individual'){
                    $('#personal_details .is_business').hide();
                    $('#personal_details .is_individual').show();
                    $('#personal_details .is_business input').attr('data-valid', '');
                    $('#personal_details .is_individual input').attr('data-valid', 'required');
                } 
                else{
                    $('#personal_details .is_individual').hide();
                    $('#personal_details .is_business').show(); 
                    $('#personal_details .is_business input').attr('data-valid', 'required');
                    $('#personal_details .is_individual input').attr('data-valid', '');
                }
            });
            
            $('.modal-body form#addgroupinvoice input[name="partner_type"]').on('change', function(){
                var invid = $(this).attr('id'); 
                if(invid == 'superagent_inv'){
                    $('.modal-body form#addgroupinvoice .is_partnerinv').hide();
                    $('.modal-body form#addgroupinvoice .is_superagentinv').show();
                    $('.modal-body form#addgroupinvoice .is_partnerinv input').attr('data-valid', '');
                    $('.modal-body form#addgroupinvoice .is_superagentinv input').attr('data-valid', 'required');
                } 
                else{
                    $('.modal-body form#addgroupinvoice .is_superagentinv').hide();
                    $('.modal-body form#addgroupinvoice .is_partnerinv').show(); 
                    $('.modal-body form#addgroupinvoice .is_partnerinv input').attr('data-valid', 'required');
                    $('.modal-body form#addgroupinvoice .is_superagentinv input').attr('data-valid', '');
                }
            });
            
            $('.modal .modal-body .is_partner').hide();
            $('.modal .modal-body .is_application').hide();
            $('.modal .modal-body input[name="related_to"]').on('change', function(){
                var relid = $(this).attr('id'); 
                if(relid == 'contact'){
                    $('.modal .modal-body .is_partner').hide();
                    $('.modal .modal-body .is_application').hide();
                    $('.modal .modal-body .is_contact').show();
                    $('.modal .modal-body .is_partner select').attr('data-valid', '');
                    $('.modal .modal-body .is_application select').attr('data-valid', '');
                    $('.modal .modal-body .is_contact select').attr('data-valid', 'required');
                } 
                else if(relid == 'partner'){
                    $('.modal .modal-body .is_contact').hide();
                    $('.modal .modal-body .is_application').hide();
                    $('.modal .modal-body .is_partner').show();
                    $('.modal .modal-body .is_contact select').attr('data-valid', '');
                    $('.modal .modal-body .is_application select').attr('data-valid', '');
                    $('.modal .modal-body .is_partner select').attr('data-valid', 'required');
                }
                else if(relid == 'application'){
                    $('.modal .modal-body .is_contact').hide();
                    $('.modal .modal-body .is_partner').hide();
                    $('.modal .modal-body .is_application').show();
                    $('.modal .modal-body .is_contact select').attr('data-valid', '');
                    $('.modal .modal-body .is_partner select').attr('data-valid', '');
                    $('.modal .modal-body .is_application select').attr('data-valid', 'required');
                }
                else{
                    $('.modal .modal-body .is_contact').hide();
                    $('.modal .modal-body .is_partner').hide(); 
                    $('.modal .modal-body .is_application').hide(); 
                    $('.modal .modal-body .is_contact input').attr('data-valid', '');
                    $('.modal .modal-body .is_partner input').attr('data-valid', '');
                    $('.modal .modal-body .is_application input').attr('data-valid', '');
                }
            });
            
            $('#agenttype input#super_agent').on('click', function(){
                if ($(this).is(":checked")) {
                    $('#office_income_share .is_super_agent').show();
                } 
                else{
                    $('#office_income_share .is_super_agent').hide();
                }
            });	
            
            $('#agenttype input#sub_agent').on('click', function(){
                if ($(this).is(":checked")) {
                    $('#office_income_share .is_sub_agent').show();
                } 
                else{
                    $('#office_income_share .is_sub_agent').hide();
                }
            });	 
             
            $('#internal select[name="source"]').on('change', function(){				
                var sourceval = $(this).val(); 
                if(sourceval == 'Sub Agent'){
                    $('#internal .is_subagent').show();
                    $('#internal .is_subagent select').attr('data-valid', 'required');
                } 
                else{
                    $('#internal .is_subagent').hide();
                    $('#internal .is_subagent select').attr('data-valid', '');
                }
            });
            
            $('.card .card-body .grid_data').hide();
            $('.card .card-body .document_layout_type a.list').on('click', function(){
                $('.card .card-body .document_layout_type a').removeClass('active');
                $(this).addClass('active');
                $('.card .card-body .grid_data').hide();
                $('.card .card-body .list_data').show();
            });
            $('.card .card-body .document_layout_type a.grid').on('click', function(){
                $('.card .card-body .document_layout_type a').removeClass('active');
                $(this).addClass('active');
                $('.card .card-body .list_data').hide();
                $('.card .card-body .grid_data').show();
            });
            
            // Initialize select2 for check-in modal if available
            if (typeof $.fn.select2 !== 'undefined') {
                $('.js-data-example-ajax-check').on("select2:select", function(e) { 
                    var data = e.params.data;
                    console.log(data);
                    $('#utype').val(data.status);
                });				
                $('.js-data-example-ajax-check').select2({
                    multiple: true,
                    closeOnSelect: false,
                    dropdownParent: $('#checkinmodal'),
                    ajax: {
                        url: site_url + '/clients/get-recipients',
                        dataType: 'json',
                        processResults: function (data) {
                            return {
                                results: data.items
                            };
                        },
                        cache: true
                    },
                    templateResult: formatRepocheck,
                    templateSelection: formatRepoSelectioncheck
                });
            }
            
            function formatRepocheck (repo) {
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
                    "<span class='select2resultrepositorystatistics'>" +
                    "</span>" +
                    "</div>" +
                    "</div>"
                );
                $container.find(".select2-result-repository__title").text(repo.name);
                $container.find(".select2-result-repository__description").text(repo.email);
                if(repo.status == 'Archived'){
                    $container.find(".select2resultrepositorystatistics").append('<span class="ui label  select2-result-repository__statistics">'+repo.status+'</span>');
                } else{
                    $container.find(".select2resultrepositorystatistics").append('<span class="ui label yellow select2-result-repository__statistics">'+repo.status+'</span>');
                }
                return $container;
            }
            
            function formatRepoSelectioncheck (repo) {
                return repo.name || repo.text;
            }
        }); // End of $(document).ready
    }
    
    // Initialize components
    initializeComponents();
    
    // Notification polling - separate from component initialization
    $(document).ready(function(){
        if (document.getElementById('countbell_notification')) {
            document.getElementById('countbell_notification').parentNode.addEventListener('click', function(event){
                window.location = "/all-notifications";
            });
        }
        
        function load_unseen_notification(view = '') {
            $.ajax({
                url: site_url + "/fetch-notification",
                method:"GET",
                dataType:"json",
                success:function(data) {
                    if(data.unseen_notification > 0) {
                        $('.countbell').html(data.unseen_notification);
                    }
                }
            });
        }
        
        function load_unseen_messages(view = '') {
            load_unseen_notification();
            var playing = false;
            $.ajax({
                url: site_url + "/fetch-messages",
                method:"GET",
                success:function(data) {
                    if(data != 0){
                        if (typeof iziToast !== 'undefined') {
                            iziToast.show({
                                backgroundColor: 'rgba(0,0,255,0.3)',
                                messageColor: 'rgba(255,255,255)',
                                title: '',
                                message: data,
                                position: 'bottomRight'
                            });
                        }
                        $(this).toggleClass("down");
                        if (playing == false) {
                            var player = document.getElementById('player');
                            if (player) {
                                player.play();
                                playing = true;
                                $(this).text("stop sound");
                            }
                        } else {
                            var player = document.getElementById('player');
                            if (player) {
                                player.pause();
                                playing = false;
                                $(this).text("restart sound");
                            }
                        }
                    }
                }
            });
        }
        
        setInterval(function(){
            // Polling functions commented out
        },120000);
    });
})();

