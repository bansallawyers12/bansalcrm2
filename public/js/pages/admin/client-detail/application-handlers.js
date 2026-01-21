/**
 * Admin Client Detail - Application Handlers Module
 * 
 * Handles application creation, workflow, partner/product selection, and application detail view
 * 
 * Dependencies:
 *   - jQuery
 *   - Select2
 *   - Bootstrap (for modals and tabs)
 *   - Flatpickr (for date pickers)
 *   - config.js (App object)
 */

'use strict';

// ============================================================================
// ASYNC WRAPPER - Wait for vendor libraries before initialization
// ============================================================================

(async function() {
    // Wait for vendor libraries to be ready
    if (typeof window.vendorLibsReady !== 'undefined') {
        console.log('[application-handlers.js] Waiting for vendorLibsReady promise...');
        await window.vendorLibsReady;
        console.log('[application-handlers.js] Vendor libraries ready!');
    } else {
        // Fallback: Poll for vendor libraries
        console.log('[application-handlers.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined' && 
                    typeof $.fn.select2 === 'function') {
                    console.log('[application-handlers.js] All vendor libraries detected!');
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

// ============================================================================
// APPLICATION WORKFLOW HANDLERS
// ============================================================================

jQuery(document).ready(function($){
    
    // ============================================================================
    // APPLICATION FORM - WORKFLOW CHANGE HANDLER
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

    // ============================================================================
    // APPLICATION FORM - PARTNER CHANGE HANDLER
    // ============================================================================
    
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

    // ============================================================================
    // APPLICATION TAB HANDLERS
    // ============================================================================
    
    // Direct click handler for when tab is already active
    $(document).ready(function() {
        $('#application-tab').off('click.appHandler').on('click.appHandler', function(e) {
            var isAlreadyActive = $(this).hasClass('active');
            
            if (isAlreadyActive) {
                var detailVisible = $('.ifapplicationdetailnot').is(':visible');
                var listHidden = $('.if_applicationdetail').is(':hidden');
                
                if (detailVisible && listHidden) {
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
                        },
                        error: function() {
                            $('.popuploader').hide();
                        }
                    });
                }
            }
        });
    });
    
    // Bootstrap tab show event for switching from other tabs
    $('#application-tab').on('show.bs.tab', function (e) {
        var tabList = document.getElementById('client_tabs');
        var isViewingDetail = tabList && tabList.getAttribute('data-application-id');
        var detailVisible = $('.ifapplicationdetailnot').is(':visible');
        var listHidden = $('.if_applicationdetail').is(':hidden');
        
        if (isViewingDetail || (detailVisible && listHidden)) {
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
                },
                error: function() {
                    $('.popuploader').hide();
                }
            });
        } else {
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
                },
                error: function() {
                    $('.popuploader').hide();
                }
            });
        }
    });

    // ============================================================================
    // APPLICATION DETAIL VIEW HANDLERS
    // ============================================================================
    
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
                                        from: dateStr,
                                        datetype: 'start',
                                        appid: appid
                                    },
                                    success: function(result) {
                                        console.log("Start date updated");
                                        // Update the displayed date in the detail view
                                        if (result.status && result.dates) {
                                            $('.app_start_date .month').text(result.dates.month);
                                            $('.app_start_date .day').text(result.dates.date);
                                            $('.app_start_date .year').text(result.dates.year);
                                        }
                                        // Refresh the applications table to show the updated date
                                        var clientId = App.getPageConfig('clientId');
                                        var url = App.getUrl('getApplicationLists') || App.getUrl('siteUrl') + '/get-application-lists';
                                        $.ajax({
                                            url: url,
                                            type: 'GET',
                                            datatype: 'json',
                                            data: {id: clientId},
                                            success: function(responses) {
                                                $('.applicationtdata').html(responses);
                                            }
                                        });
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
                                        from: dateStr,
                                        datetype: 'end',
                                        appid: appid
                                    },
                                    success: function(result) {
                                        console.log("End date updated");
                                        // Update the displayed date in the detail view
                                        if (result.status && result.dates) {
                                            $('.app_end_date .month').text(result.dates.month);
                                            $('.app_end_date .day').text(result.dates.date);
                                            $('.app_end_date .year').text(result.dates.year);
                                        }
                                        // Refresh the applications table to show the updated date
                                        var clientId = App.getPageConfig('clientId');
                                        var url = App.getUrl('getApplicationLists') || App.getUrl('siteUrl') + '/get-application-lists';
                                        $.ajax({
                                            url: url,
                                            type: 'GET',
                                            datatype: 'json',
                                            data: {id: clientId},
                                            success: function(responses) {
                                                $('.applicationtdata').html(responses);
                                            }
                                        });
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

    // ============================================================================
    // APPLICATION MODAL OPENERS
    // ============================================================================
    
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

    // ============================================================================
    // SELECT2 INITIALIZATION FOR APPLICATION FORMS
    // ============================================================================
    
    // Initialize Select2 when modal is shown to ensure proper rendering
    $('.add_appliation').on('shown.bs.modal', function () {
        // Destroy any existing Select2 instances first
        if ($(".applicationselect2").hasClass("select2-hidden-accessible")) {
            $(".applicationselect2").select2('destroy');
        }
        if ($(".partner_branchselect2").hasClass("select2-hidden-accessible")) {
            $(".partner_branchselect2").select2('destroy');
        }
        if ($(".approductselect2").hasClass("select2-hidden-accessible")) {
            $(".approductselect2").select2('destroy');
        }
        
        // Initialize Select2 with dropdownParent
        $(".applicationselect2").select2({
            dropdownParent: $(".add_appliation"),
            width: '100%'
        });

        $(".partner_branchselect2").select2({
            dropdownParent: $(".add_appliation"),
            width: '100%'
        });
        
        $(".approductselect2").select2({
            dropdownParent: $(".add_appliation"),
            width: '100%'
        });
    });
    
    // Clean up Select2 when modal is hidden
    $('.add_appliation').on('hidden.bs.modal', function () {
        if ($(".applicationselect2").hasClass("select2-hidden-accessible")) {
            $(".applicationselect2").select2('destroy');
        }
        if ($(".partner_branchselect2").hasClass("select2-hidden-accessible")) {
            $(".partner_branchselect2").select2('destroy');
        }
        if ($(".approductselect2").hasClass("select2-hidden-accessible")) {
            $(".approductselect2").select2('destroy');
        }
    });

    console.log('[application-handlers.js] Application handlers initialized');
});

})(); // End async wrapper

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

/**
 * Update client detail URL based on active tab and application ID
 * @param {string} tab - The active tab name
 * @param {string} applicationId - The application ID (optional)
 */
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

/**
 * Show application list view (hide detail view)
 */
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

// Make functions available globally
if(typeof window !== 'undefined') {
    window.updateClientDetailUrl = updateClientDetailUrl;
    window.showApplicationList = showApplicationList;
}
