/**
 * Agent Client Detail Page - Page-Specific JavaScript
 * 
 * This file contains JavaScript code specific to the Agent Client Detail page.
 * Common/shared functionality should be in /js/common/ files.
 * 
 * Dependencies (loaded before this file):
 *   - config.js
 *   - ajax-helpers.js
 *   - utilities.js
 *   - crud-operations.js
 *   - activity-handlers.js
 *   - document-handlers.js
 *   - ui-components.js
 */

'use strict';

// ============================================================================
// ASYNC WRAPPER - Wait for vendor libraries before initialization
// ============================================================================

(async function() {
    // Wait for vendor libraries to be ready
    if (typeof window.vendorLibsReady !== 'undefined') {
        console.log('[agent/client-detail.js] Waiting for vendorLibsReady promise...');
        await window.vendorLibsReady;
        console.log('[agent/client-detail.js] Vendor libraries ready!');
    } else {
        // Fallback: Poll for vendor libraries
        console.log('[agent/client-detail.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined' && 
                    typeof $.fn.select2 === 'function' &&
                    typeof flatpickr !== 'undefined') {
                    console.log('[agent/client-detail.js] All vendor libraries detected!');
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

jQuery(document).ready(function($) {
    var appcid = '';
    var notid = '';
    var delhref = '';
    var eduid = '';

    // Get site URL from config (replaces global site_url variable)
    var siteUrl = App.getUrl('siteUrl') || App.getPageConfig('siteUrl') || '';
    
    // Get client ID from config
    var clientId = App.getPageConfig('clientId');
    
    // Get appointment data from config (if available)
    var appointmentData = App.getPageConfig('appointmentData') || {};

    // ============================================================================
    // ACTIVITY & NOTES MANAGEMENT (Agent-specific overrides)
    // ============================================================================

    /**
     * Get all activities - Agent version uses .activities selector
     */
    function getallactivitiesAgent() {
        $.ajax({
            url: App.getUrl('getActivities') || siteUrl + '/agent/get-activities',
            type: 'GET',
            datatype: 'json',
            data: { id: clientId },
            success: function(responses) {
                var ress = JSON.parse(responses);
                var html = '';
                $.each(ress.data, function(k, v) {
                    html += '<div class="activity"><div class="activity-icon bg-primary text-white"><span>' + v.createdname + '</span></div><div class="activity-detail"><div class="activity-head"><div class="activity-title"><p><b>' + v.name + '</b> ' + v.subject + '</p></div><div class="activity-date"><span class="text-job">' + v.date + '</span></div></div>';
                    if (v.message != null) {
                        html += '<p>' + v.message + '</p>';
                    }
                    html += '</div></div>';
                });
                $('.activities').html(html);
            }
        });
    }

    /**
     * Get all notes - Agent version
     */
    function getallnotesAgent() {
        $.ajax({
            url: App.getUrl('getNotes') || siteUrl + '/agent/get-notes',
            type: 'GET',
            data: { clientid: clientId, type: 'client' },
            success: function(responses) {
                $('.popuploader').hide();
                $('.note_term_list').html(responses);
            }
        });
    }

    // Override global functions for agent-specific behavior
    window.getallactivities = getallactivitiesAgent;
    window.getallnotes = getallnotesAgent;

    // Initialize activities and notes on page load
    getallactivitiesAgent();
    getallnotesAgent();

    // ============================================================================
    // TASK MANAGEMENT
    // ============================================================================

    $(document).delegate('.opentaskview', 'click', function() {
        $('#opentaskview').modal('show');
        var v = $(this).attr('id');
        $.ajax({
            url: App.getUrl('getTaskDetail') || siteUrl + '/agent/get-task-detail',
            type: 'GET',
            data: { task_id: v },
            success: function(responses) {
                $('.taskview').html(responses);
            }
        });
    });

    // ============================================================================
    // DOCUMENT MANAGEMENT
    // ============================================================================

    $(document).delegate('.publishdoc', 'click', function() {
        $('#confirmpublishdocModal').modal('show');
        appcid = $(this).attr('data-id');
    });

    $(document).delegate('#confirmpublishdocModal .acceptpublishdoc', 'click', function() {
        $('.popuploader').show();
        $.ajax({
            url: App.getUrl('publishDoc') || siteUrl + '/agent/application/publishdoc',
            type: 'GET',
            datatype: 'json',
            data: { appid: appcid, status: '1' },
            success: function(response) {
                $('.popuploader').hide();
                var res = JSON.parse(response);
                $('#confirmpublishdocModal').modal('hide');
                if (res.status) {
                    $('.mychecklistdocdata').html(res.doclistdata);
                } else {
                    alert(res.message);
                }
            }
        });
    });

    // ============================================================================
    // NOTE DELETION (with dynamic href handling)
    // ============================================================================

    $(document).delegate('.deletenote', 'click', function() {
        $('#confirmModal').modal('show');
        notid = $(this).attr('data-id');
        delhref = $(this).attr('data-href');
    });

    $(document).delegate('#confirmModal .accept', 'click', function() {
        $('.popuploader').show();
        var deleteUrl = siteUrl + '/agent/' + delhref;
        $.ajax({
            url: deleteUrl,
            type: 'GET',
            datatype: 'json',
            data: { note_id: notid },
            success: function(response) {
                $('.popuploader').hide();
                var res = JSON.parse(response);
                $('#confirmModal').modal('hide');
                if (res.status) {
                    $('#note_id_' + notid).remove();
                    if (delhref == 'deletedocs') {
                        $('.documnetlist #id_' + notid).remove();
                    }
                    if (delhref == 'deleteservices') {
                        $.ajax({
                            url: App.getUrl('getServices') || siteUrl + '/agent/get-services',
                            type: 'GET',
                            data: { clientid: clientId },
                            success: function(responses) {
                                $('.interest_serv_list').html(responses);
                            }
                        });
                    } else if (delhref == 'superagent') {
                        $('.supagent_data').html('');
                    } else if (delhref == 'subagent') {
                        $('.subagent_data').html('');
                    } else if (delhref == 'deleteappointment') {
                        alert('Appointment functionality has been removed');
                    } else if (delhref == 'deletepaymentschedule') {
                        $.ajax({
                            url: siteUrl + '/agent/get-all-paymentschedules',
                            type: 'GET',
                            data: { client_id: clientId, appid: res.application_id },
                            success: function(responses) {
                                $('.showpaymentscheduledata').html(responses);
                            }
                        });
                    } else if (delhref == 'deleteapplicationdocs') {
                        $('.mychecklistdocdata').html(res.doclistdata);
                        $('.checklistuploadcount').html(res.applicationuploadcount);
                        $('.' + res.type + '_checklists').html(res.checklistdata);
                    } else {
                        getallnotesAgent();
                    }
                    getallactivitiesAgent();
                }
            }
        });
    });

    $(document).delegate('.pinnote', 'click', function() {
        $('.popuploader').show();
        $.ajax({
            url: App.getUrl('pinNote') || siteUrl + '/agent/pinnote',
            type: 'GET',
            datatype: 'json',
            data: { note_id: $(this).attr('data-id') },
            success: function(response) {
                getallnotesAgent();
            }
        });
    });

    // ============================================================================
    // INVOICE CREATION
    // ============================================================================

    $(document).delegate('.createapplicationnewinvoice', 'click', function() {
        $('#opencreateinvoiceform').modal('show');
        var sid = $(this).attr('data-id');
        var cid = $(this).attr('data-cid');
        var aid = $(this).attr('data-app-id');
        $('#opencreateinvoiceform #invoice_client_id').val(cid);
        $('#app_id').val(aid);
        $('#schedule_id').val(sid);
    });

    // ============================================================================
    // NOTE CREATION/EDITING
    // ============================================================================

    $(document).delegate('.create_note', 'click', function() {
        $('#create_note').modal('show');
        $('#create_note input[name="mailid"]').val(0);
        $('#create_note input[name="title"]').val('');
        $('#create_note #appliationModalLabel').html('Create Note');
        $('#create_note input[name="title"]').val('');
        $("#create_note .summernote-simple").val('');
        $('#create_note input[name="noteid"]').val('');
        $("#create_note .summernote-simple").summernote('code', '');
        if ($(this).attr('datatype') == 'note') {
            $('.is_not_note').hide();
        } else {
            var datasubject = $(this).attr('datasubject');
            var datamailid = $(this).attr('datamailid');
            $('#create_note input[name="title"]').val(datasubject);
            $('#create_note input[name="mailid"]').val(datamailid);
            $('.is_not_note').show();
        }
    });

    $(document).delegate('.opentaskmodal', 'click', function() {
        $('#opentaskmodal').modal('show');
        $('#opentaskmodal input[name="mailid"]').val(0);
        $('#opentaskmodal input[name="title"]').val('');
        $('#opentaskmodal #appliationModalLabel').html('Create Note');
        $('#opentaskmodal input[name="attachments"]').val('');
        $('#opentaskmodal input[name="title"]').val('');
        $('#opentaskmodal .showattachment').val('Choose file');
        var datasubject = $(this).attr('datasubject');
        var datamailid = $(this).attr('datamailid');
        $('#opentaskmodal input[name="title"]').val(datasubject);
        $('#opentaskmodal input[name="mailid"]').val(datamailid);
    });

    // ============================================================================
    // SELECT2 RECIPIENT SELECTION
    // ============================================================================

    function formatRepo(repo) {
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

    function formatRepoSelection(repo) {
        return repo.name || repo.text;
    }

    $('.js-data-example-ajaxcc').select2({
        multiple: true,
        closeOnSelect: false,
        dropdownParent: $('#create_note'),
        ajax: {
            url: App.getUrl('clientGetRecipients') || siteUrl + '/clients/get-recipients',
            dataType: 'json',
            processResults: function(data) {
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
            url: App.getUrl('clientGetRecipients') || siteUrl + '/clients/get-recipients',
            dataType: 'json',
            processResults: function(data) {
                return {
                    results: data.items
                };
            },
            cache: true
        },
        templateResult: formatRepo,
        templateSelection: formatRepoSelection
    });

    $('.js-data-example-ajaxcontact').select2({
        multiple: true,
        closeOnSelect: false,
        dropdownParent: $('#opentaskmodal'),
        ajax: {
            url: App.getUrl('clientGetRecipients') || siteUrl + '/clients/get-recipients',
            dataType: 'json',
            processResults: function(data) {
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
    // NOTE VIEWING/EDITING
    // ============================================================================

    $(document).delegate('.opennoteform', 'click', function() {
        $('#create_note').modal('show');
        $('#create_note #appliationModalLabel').html('Edit Note');
        var v = $(this).attr('data-id');
        $('#create_note input[name="noteid"]').val(v);
        $('.popuploader').show();
        $.ajax({
            url: App.getUrl('getNoteDetail') || siteUrl + '/agent/getnotedetail',
            type: 'GET',
            datatype: 'json',
            data: { note_id: v },
            success: function(response) {
                $('.popuploader').hide();
                var res = JSON.parse(response);
                if (res.status) {
                    $('#create_note input[name="title"]').val(res.data.title);
                    $("#create_note .summernote-simple").val(res.data.description);
                    $("#create_note .summernote-simple").summernote('code', res.data.description);
                }
            }
        });
    });

    $(document).delegate('.viewnote', 'click', function() {
        $('#view_note').modal('show');
        var v = $(this).attr('data-id');
        $('#view_note input[name="noteid"]').val(v);
        $('.popuploader').show();
        $.ajax({
            url: App.getUrl('viewNoteDetail') || siteUrl + '/agent/viewnotedetail',
            type: 'GET',
            datatype: 'json',
            data: { note_id: v },
            success: function(response) {
                $('.popuploader').hide();
                var res = JSON.parse(response);
                if (res.status) {
                    $('#view_note .modal-body .note_content h5').html(res.data.title);
                    $("#view_note .modal-body .note_content p").html(res.data.description);
                }
            }
        });
    });

    $(document).delegate('.viewapplicationnote', 'click', function() {
        $('#view_application_note').modal('show');
        var v = $(this).attr('data-id');
        $('#view_application_note input[name="noteid"]').val(v);
        $('.popuploader').show();
        $.ajax({
            url: App.getUrl('viewApplicationNote') || siteUrl + '/agent/viewapplicationnote',
            type: 'GET',
            datatype: 'json',
            data: { note_id: v },
            success: function(response) {
                $('.popuploader').hide();
                var res = JSON.parse(response);
                if (res.status) {
                    $('#view_application_note .modal-body .note_content h5').html(res.data.title);
                    $("#view_application_note .modal-body .note_content p").html(res.data.description);
                }
            }
        });
    });

    // ============================================================================
    // PARTNER/PRODUCT/BRANCH SELECTION (Application Workflow)
    // ============================================================================

    $(document).delegate('.add_appliation #workflow', 'change', function() {
        var v = $('.add_appliation #workflow option:selected').val();
        if (v != '') {
            $('.popuploader').show();
            $.ajax({
                url: App.getUrl('getPartnerBranch') || siteUrl + '/agent/getpartnerbranch',
                type: 'GET',
                data: { cat_id: v },
                success: function(response) {
                    $('.popuploader').hide();
                    $('.add_appliation #partner').html(response);
                    $(".add_appliation #partner").val('').trigger('change');
                    $(".add_appliation #product").val('').trigger('change');
                    $(".add_appliation #branch").val('').trigger('change');
                }
            });
        }
    });

    $(document).delegate('.add_appliation #partner', 'change', function() {
        var v = $('.add_appliation #partner option:selected').val();
        var explode = v.split('_');
        if (v != '') {
            $('.popuploader').show();
            $.ajax({
                url: App.getUrl('getBranchProduct') || siteUrl + '/agent/getbranchproduct',
                type: 'GET',
                data: { cat_id: explode[0] },
                success: function(response) {
                    $('.popuploader').hide();
                    $('.add_appliation #product').html(response);
                    $(".add_appliation #product").val('').trigger('change');
                }
            });
        }
    });

    // ============================================================================
    // EMAIL & TEMPLATE MANAGEMENT
    // ============================================================================

    $(document).delegate('.clientemail', 'click', function() {
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
            html: "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +
                "<div  class='ag-flex ag-align-start'>" +
                "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'>" + name + "</span>&nbsp;</div>" +
                "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'>" + email + "</small ></div>" +
                "</div>" +
                "</div>" +
                "<div class='ag-flex ag-flex-column ag-align-end'>" +
                "<span class='ui label yellow select2-result-repository__statistics'>" + status +
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

    $(document).delegate('.change_client_status', 'click', function(e) {
        var v = $(this).attr('rating');
        $('.change_client_status').removeClass('active');
        $(this).addClass('active');
        $.ajax({
            url: App.getUrl('changeClientStatus') || siteUrl + '/agent/change-client-status',
            type: 'GET',
            datatype: 'json',
            data: { id: clientId, rating: v },
            success: function(response) {
                var res = JSON.parse(response);
                if (res.status) {
                    $('.custom-error-msg').html('<span class="alert alert-success">' + res.message + '</span>');
                    getallactivitiesAgent();
                } else {
                    $('.custom-error-msg').html('<span class="alert alert-danger">' + response.message + '</span>');
                }
            }
        });
    });

    $(document).delegate('.selecttemplate', 'change', function() {
        var v = $(this).val();
        $.ajax({
            url: App.getUrl('getTemplates') || siteUrl + '/agent/get-templates',
            type: 'GET',
            datatype: 'json',
            data: { id: v },
            success: function(response) {
                var res = JSON.parse(response);
                $('.selectedsubject').val(res.subject);
                $("#emailmodal .summernote-simple").summernote('reset');
                $("#emailmodal .summernote-simple").summernote('code', res.description);
                $("#emailmodal .summernote-simple").val(res.description);
            }
        });
    });

    $(document).delegate('.selectapplicationtemplate', 'change', function() {
        var v = $(this).val();
        $.ajax({
            url: App.getUrl('getTemplates') || siteUrl + '/agent/get-templates',
            type: 'GET',
            datatype: 'json',
            data: { id: v },
            success: function(response) {
                var res = JSON.parse(response);
                $('.selectedappsubject').val(res.subject);
                $("#applicationemailmodal .summernote-simple").summernote('reset');
                $("#applicationemailmodal .summernote-simple").summernote('code', res.description);
                $("#applicationemailmodal .summernote-simple").val(res.description);
            }
        });
    });

    $('.js-data-example-ajax').select2({
        multiple: true,
        closeOnSelect: false,
        dropdownParent: $('#emailmodal'),
        ajax: {
            url: App.getUrl('clientGetRecipients') || siteUrl + '/clients/get-recipients',
            dataType: 'json',
            processResults: function(data) {
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
            url: App.getUrl('clientGetRecipients') || siteUrl + '/clients/get-recipients',
            dataType: 'json',
            processResults: function(data) {
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
    // DATATABLES INITIALIZATION
    // ============================================================================

    $(".invoicetable").dataTable({
        "searching": false,
        "lengthChange": false,
        "columnDefs": [
            { "sortable": false, "targets": [0, 2, 3] }
        ],
        order: [[1, "desc"]]
    });

    // ============================================================================
    // INTEREST SERVICE WORKFLOW SELECTION
    // ============================================================================

    $(document).delegate('#intrested_workflow', 'change', function() {
        var v = $('#intrested_workflow option:selected').val();
        if (v != '') {
            $('.popuploader').show();
            $.ajax({
                url: App.getUrl('getPartner') || siteUrl + '/agent/getpartner',
                type: 'GET',
                data: { cat_id: v },
                success: function(response) {
                    $('.popuploader').hide();
                    $('#intrested_partner').html(response);
                    $("#intrested_partner").val('').trigger('change');
                    $("#intrested_product").val('').trigger('change');
                    $("#intrested_branch").val('').trigger('change');
                }
            });
        }
    });

    $(document).delegate('#intrested_partner', 'change', function() {
        var v = $('#intrested_partner option:selected').val();
        if (v != '') {
            $('.popuploader').show();
            $.ajax({
                url: App.getUrl('getProduct') || siteUrl + '/agent/getproduct',
                type: 'GET',
                data: { cat_id: v },
                success: function(response) {
                    $('.popuploader').hide();
                    $('#intrested_product').html(response);
                    $("#intrested_product").val('').trigger('change');
                    $("#intrested_branch").val('').trigger('change');
                }
            });
        }
    });

    $(document).delegate('#intrested_product', 'change', function() {
        var v = $('#intrested_product option:selected').val();
        if (v != '') {
            $('.popuploader').show();
            $.ajax({
                url: App.getUrl('getBranch') || siteUrl + '/agent/getbranch',
                type: 'GET',
                data: { cat_id: v },
                success: function(response) {
                    $('.popuploader').hide();
                    $('#intrested_branch').html(response);
                    $("#intrested_branch").val('').trigger('change');
                }
            });
        }
    });

    // ============================================================================
    // DOCUMENT UPLOAD
    // ============================================================================

    $(document).delegate('.docupload', 'click', function() {
        $(this).attr("value", "");
    });

    $(document).delegate('.docupload', 'change', function() {
        $('.popuploader').show();
        var formData = new FormData($('#upload_form')[0]);
        $.ajax({
            url: App.getUrl('uploadDocument') || siteUrl + '/agent/upload-document',
            type: 'POST',
            datatype: 'json',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': App.getCsrf()
            },
            success: function(responses) {
                $('.popuploader').hide();
                var ress = JSON.parse(responses);
                if (ress.status) {
                    $('.custom-error-msg').html('<span class="alert alert-success">' + ress.message + '</span>');
                    $('.documnetlist').html(ress.data);
                    $('.griddata').html(ress.griddata);
                } else {
                    $('.custom-error-msg').html('<span class="alert alert-danger">' + ress.message + '</span>');
                }
                getallactivitiesAgent();
            }
        });
    });

    // ============================================================================
    // SERVICE CONVERSION TO APPLICATION
    // ============================================================================

    $(document).delegate('.converttoapplication', 'click', function() {
        var v = $(this).attr('data-id');
        if (v != '') {
            $('.popuploader').show();
            $.ajax({
                url: App.getUrl('convertApplication') || siteUrl + '/agent/convertapplication',
                type: 'GET',
                data: { cat_id: v, clientid: clientId },
                success: function(response) {
                    $.ajax({
                        url: App.getUrl('getServices') || siteUrl + '/agent/get-services',
                        type: 'GET',
                        data: { clientid: clientId },
                        success: function(responses) {
                            $('.interest_serv_list').html(responses);
                        }
                    });
                    $.ajax({
                        url: App.getUrl('getApplicationLists') || siteUrl + '/agent/get-application-lists',
                        type: 'GET',
                        datatype: 'json',
                        data: { id: clientId },
                        success: function(responses) {
                            $('.applicationtdata').html(responses);
                        }
                    });
                    $('.popuploader').hide();
                }
            });
        }
    });

    // ============================================================================
    // APPLICATION TAB CLICK
    // ============================================================================

    $(document).on('click', '#application-tab', function() {
        $('.popuploader').show();
        $.ajax({
            url: App.getUrl('getApplicationLists') || siteUrl + '/agent/get-application-lists',
            type: 'GET',
            datatype: 'json',
            data: { id: clientId },
            success: function(responses) {
                $('.popuploader').hide();
                $('.applicationtdata').html(responses);
            }
        });
    });

    // ============================================================================
    // DOCUMENT RENAME
    // ============================================================================

    $(document).on('click', '.documnetlist .renamedoc', function() {
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

    $(document).on('click', '.documnetlist .drow .btn-danger', function() {
        var parent = $(this).closest('.drow').find('.doc-row');
        var hourid = parent.data('id');
        if (hourid) {
            parent.html(parent.data('current-html'));
        } else {
            parent.remove();
        }
    });

    $(document).delegate('.documnetlist .drow .btn-primary', 'click', function() {
        var parent = $(this).closest('.drow').find('.doc-row');
        parent.find('.opentime').removeClass('is-invalid');
        parent.find('.invalid-feedback').remove();
        var opentime = parent.find('.opentime').val();
        if (!opentime) {
            parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
            parent.append($("<div class='invalid-feedback'>This field is required</div>"));
            return false;
        }
        $.ajax({
            type: "POST",
            data: { "_token": App.getCsrf(), "filename": opentime, "id": parent.data('id') },
            url: App.getUrl('renameDoc') || siteUrl + '/agent/renamedoc',
            success: function(result) {
                var obj = JSON.parse(result);
                if (obj.status) {
                    parent.empty()
                        .data('id', obj.Id)
                        .data('name', opentime)
                        .append(
                            $('<span>').html('<i class="fas fa-file-image"></i> ' + obj.filename + '.' + obj.filetype)
                        );
                    $('#grid_' + obj.Id).html(obj.filename + '.' + obj.filetype);
                } else {
                    parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
                    parent.append($('<div class="invalid-feedback">' + obj.message + '</div>'));
                }
            }
        });
        return false;
    });

    // ============================================================================
    // APPOINTMENT MANAGEMENT
    // ============================================================================

    $(document).delegate('.appointmentdata', 'click', function() {
        var v = $(this).attr('data-id');
        $('.appointmentdata').removeClass('active');
        $(this).addClass('active');
        var res = appointmentData;
        $('.appointmentname').html(res[v].title);
        $('.appointmenttime').html(res[v].time);
        $('.appointmentdate').html(res[v].date);
        $('.appointmentdescription').html(res[v].description);
        $('.appointmentcreatedby').html(res[v].createdby);
        $('.appointmentcreatedname').html(res[v].createdname);
        $('.appointmentcreatedemail').html(res[v].createdemail);
        $('.editappointment .edit_link').attr('data-id', v);
    });

    $(document).delegate('.opencreate_task', 'click', function() {
        $('#tasktermform')[0].reset();
        $('#tasktermform select').val('').trigger('change');
        $('.create_task').modal('show');
        $('.ifselecttask').hide();
        $('.ifselecttask select').attr('data-valid', '');
    });

    // ============================================================================
    // EDUCATION MANAGEMENT
    // ============================================================================

    $(document).delegate('.deleteeducation', 'click', function() {
        eduid = $(this).attr('data-id');
        $('#confirmEducationModal').modal('show');
    });

    $(document).delegate('#confirmEducationModal .accepteducation', 'click', function() {
        $('.popuploader').show();
        $.ajax({
            url: siteUrl + '/agent/delete-education',
            type: 'GET',
            datatype: 'json',
            data: { edu_id: eduid },
            success: function(response) {
                $('.popuploader').hide();
                var res = JSON.parse(response);
                $('#confirmEducationModal').modal('hide');
                if (res.status) {
                    $('#edu_id_' + eduid).remove();
                } else {
                    alert('Please try again');
                }
            }
        });
    });

    $(document).delegate('#educationform #subjectlist', 'change', function() {
        var v = $('#educationform #subjectlist option:selected').val();
        if (v != '') {
            $('.popuploader').show();
            $.ajax({
                url: App.getUrl('getSubjects') || siteUrl + '/agent/getsubjects',
                type: 'GET',
                data: { cat_id: v },
                success: function(response) {
                    $('.popuploader').hide();
                    $('#educationform #subject').html(response);
                    $(".add_appliation #subject").val('').trigger('change');
                }
            });
        }
    });

    $(document).delegate('.edit_appointment', 'click', function() {
        var v = $(this).attr('data-id');
        $('.popuploader').show();
        $('#edit_appointment').modal('show');
        $.ajax({
            url: App.getUrl('getAppointmentDetail') || siteUrl + '/agent/getAppointmentdetail',
            type: 'GET',
            data: { id: v },
            success: function(response) {
                $('.popuploader').hide();
                $('.showappointmentdetail').html(response);
                if (typeof flatpickr !== 'undefined') {
                    flatpickr(".datepicker", {
                        dateFormat: "Y-m-d",
                        allowInput: true
                    });
                }
                $(".timepicker").timepicker({
                    icons: {
                        up: "fas fa-chevron-up",
                        down: "fas fa-chevron-down"
                    }
                });
                $(".timezoneselects2").select2({
                    dropdownParent: $("#edit_appointment")
                });
                $(".invitesselects2").select2({
                    dropdownParent: $("#edit_appointment")
                });
            }
        });
    });

    $(document).delegate('.editeducation', 'click', function() {
        var v = $(this).attr('data-id');
        $('.popuploader').show();
        $('#edit_education').modal('show');
        $.ajax({
            url: App.getUrl('getEducationDetail') || siteUrl + '/agent/getEducationdetail',
            type: 'GET',
            data: { id: v },
            success: function(response) {
                $('.popuploader').hide();
                $('.showeducationdetail').html(response);
                if (typeof flatpickr !== 'undefined') {
                    flatpickr(".datepicker", {
                        dateFormat: "Y-m-d",
                        allowInput: true
                    });
                }
            }
        });
    });

    // ============================================================================
    // INTEREST SERVICE MANAGEMENT
    // ============================================================================

    $(document).delegate('.interest_service_view', 'click', function() {
        var v = $(this).attr('data-id');
        $('.popuploader').show();
        $('#interest_service_view').modal('show');
        $.ajax({
            url: App.getUrl('getInterestedService') || siteUrl + '/agent/getintrestedservice',
            type: 'GET',
            data: { id: v },
            success: function(response) {
                $('.popuploader').hide();
                $('.showinterestedservice').html(response);
            }
        });
    });

    $(document).delegate('.openeditservices', 'click', function() {
        var v = $(this).attr('data-id');
        $('.popuploader').show();
        $('#interest_service_view').modal('hide');
        $('#eidt_interested_service').modal('show');
        $.ajax({
            url: App.getUrl('getInterestedServiceEdit') || siteUrl + '/agent/getintrestedserviceedit',
            type: 'GET',
            data: { id: v },
            success: function(response) {
                $('.popuploader').hide();
                $('.showinterestedserviceedit').html(response);
                if (typeof flatpickr !== 'undefined') {
                    flatpickr(".datepicker", {
                        dateFormat: "Y-m-d",
                        allowInput: true
                    });
                }
            }
        });
    });

    // ============================================================================
    // PAYMENT MANAGEMENT
    // ============================================================================

    $(document).delegate('.opencommissioninvoice', 'click', function() {
        $('#opencommissionmodal').modal('show');
    });

    $(document).delegate('.opengeneralinvoice', 'click', function() {
        $('#opengeneralinvoice').modal('show');
    });

    $(document).delegate('.addpaymentmodal', 'click', function() {
        var v = $(this).attr('data-invoiceid');
        var netamount = $(this).attr('data-netamount');
        var dueamount = $(this).attr('data-dueamount');
        $('#invoice_id').val(v);
        $('.invoicenetamount').html(netamount + ' AUD');
        $('.totldueamount').html(dueamount);
        $('.totldueamount').attr('data-totaldue', dueamount);
        $('#addpaymentmodal').modal('show');
        $('.payment_field_clone').remove();
        $('.paymentAmount').val('');
    });

    $(document).delegate('.paymentAmount', 'keyup', function() {
        grandtotal();
    });

    function grandtotal() {
        var p = 0;
        $('.paymentAmount').each(function() {
            if ($(this).val() != '') {
                p += parseFloat($(this).val());
            }
        });
        var tamount = $('.totldueamount').attr('data-totaldue');
        var am = parseFloat(tamount) - parseFloat(p);
        $('.totldueamount').html(am.toFixed(2));
    }

    $('.add_payment_field a').on('click', function() {
        var clonedval = $('.payment_field .payment_field_row .payment_first_step').html();
        $('.payment_field .payment_field_row').append('<div class="payment_field_col payment_field_clone">' + clonedval + '</div>');
    });

    $('.add_fee_type a.fee_type_btn').on('click', function() {
        var clonedval = $('.fees_type_sec .fee_type_row .fees_type_col').html();
        $('.fees_type_sec .fee_type_row').append('<div class="custom_type_col fees_type_clone">' + clonedval + '</div>');
    });

    $(document).delegate('.payment_field_col .field_remove_col a.remove_col', 'click', function() {
        var $tr = $(this).closest('.payment_field_clone');
        var trclone = $('.payment_field_clone').length;
        if (trclone > 0) {
            $tr.remove();
            grandtotal();
        }
    });

    $(document).delegate('.fees_type_sec .fee_type_row .fees_type_clone a.remove_btn', 'click', function() {
        var $tr = $(this).closest('.fees_type_clone');
        var trclone = $('.fees_type_clone').length;
        if (trclone > 0) {
            $tr.remove();
            grandtotal();
        }
    });

    // ============================================================================
    // APPLICATION DETAIL LOADING (Conditional on page load)
    // ============================================================================

    var appliid = App.getPageConfig('initialAppId');
    if (appliid) {
        $('.if_applicationdetail').hide();
        $('.ifapplicationdetailnot').show();
        $.ajax({
            url: App.getUrl('getApplicationDetail') || siteUrl + '/agent/getapplicationdetail',
            type: 'GET',
            data: { id: appliid },
            success: function(response) {
                $('.popuploader').hide();
                $('.ifapplicationdetailnot').html(response);
                initApplicationDatePickers(appliid);
            }
        });
    }

    // ============================================================================
    // APPLICATION MANAGEMENT
    // ============================================================================

    $(document).delegate('.discon_application', 'click', function() {
        var appliid = $(this).attr('data-id');
        $('#discon_application').modal('show');
        $('input[name="diapp_id"]').val(appliid);
    });

    $(document).delegate('.revertapp', 'click', function() {
        var appliid = $(this).attr('data-id');
        $('#revert_application').modal('show');
        $('input[name="revapp_id"]').val(appliid);
    });

    $(document).delegate('.completestage', 'click', function() {
        var appliid = $(this).attr('data-id');
        $('#confirmcompleteModal').modal('show');
        $('.acceptapplication').attr('data-id', appliid);
    });

    $(document).delegate('.openapplicationdetail', 'click', function() {
        var appliid = $(this).attr('data-id');
        $('.if_applicationdetail').hide();
        $('.ifapplicationdetailnot').show();
        $.ajax({
            url: App.getUrl('getApplicationDetail') || siteUrl + '/agent/getapplicationdetail',
            type: 'GET',
            data: { id: appliid },
            success: function(response) {
                $('.popuploader').hide();
                $('.ifapplicationdetailnot').html(response);
                initApplicationDatePickers(appliid);
            }
        });
    });

    /**
     * Initialize date pickers for application detail
     */
    function initApplicationDatePickers(appliid) {
        if (typeof flatpickr !== 'undefined') {
            $('.datepicker').each(function() {
                flatpickr(this, {
                    dateFormat: "Y-m-d",
                    allowInput: true,
                    onChange: function(selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0) {
                            $('#popuploader').show();
                            $.ajax({
                                url: App.getUrl('updateIntake') || siteUrl + '/agent/application/updateintake',
                                method: "GET",
                                dataType: "json",
                                data: { from: dateStr, appid: appliid },
                                success: function(result) {
                                    $('#popuploader').hide();
                                }
                            });
                        }
                    }
                });
            });

            $('.expectdatepicker').each(function() {
                flatpickr(this, {
                    dateFormat: "Y-m-d",
                    allowInput: true,
                    onChange: function(selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0) {
                            $('#popuploader').show();
                            $.ajax({
                                url: App.getUrl('updateExpectWin') || siteUrl + '/agent/application/updateexpectwin',
                                method: "GET",
                                dataType: "json",
                                data: { from: dateStr, appid: appliid },
                                success: function(result) {
                                    $('#popuploader').hide();
                                }
                            });
                        }
                    }
                });
            });

            $('.startdatepicker').each(function() {
                flatpickr(this, {
                    dateFormat: "Y-m-d",
                    allowInput: true,
                    onChange: function(selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0) {
                            $('#popuploader').show();
                            $.ajax({
                                url: App.getUrl('updateApplicationDates') || siteUrl + '/agent/application/updatedates',
                                method: "GET",
                                dataType: "json",
                                data: { from: dateStr, appid: appliid, datetype: 'start' },
                                success: function(result) {
                                    $('#popuploader').hide();
                                    var obj = result;
                                    if (obj.status) {
                                        $('.app_start_date .month').html(obj.dates.month);
                                        $('.app_start_date .day').html(obj.dates.date);
                                        $('.app_start_date .year').html(obj.dates.year);
                                    }
                                }
                            });
                        }
                    }
                });
            });

            $('.enddatepicker').each(function() {
                flatpickr(this, {
                    dateFormat: "Y-m-d",
                    allowInput: true,
                    onChange: function(selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0) {
                            $('#popuploader').show();
                            $.ajax({
                                url: App.getUrl('updateApplicationDates') || siteUrl + '/agent/application/updatedates',
                                method: "GET",
                                dataType: "json",
                                data: { from: dateStr, appid: appliid, datetype: 'end' },
                                success: function(result) {
                                    $('#popuploader').hide();
                                    var obj = result;
                                    if (obj.status) {
                                        $('.app_end_date .month').html(obj.dates.month);
                                        $('.app_end_date .day').html(obj.dates.date);
                                        $('.app_end_date .year').html(obj.dates.year);
                                    }
                                }
                            });
                        }
                    }
                });
            });
        }
    }

    $(document).delegate('#application-tab', 'click', function() {
        $('.if_applicationdetail').show();
        $('.ifapplicationdetailnot').hide();
        $('.ifapplicationdetailnot').html('<h4>Please wait ...</h4>');
    });

    // ============================================================================
    // APPLICATION NOTES/APPOINTMENTS/EMAILS/CHECKLISTS
    // ============================================================================

    $(document).delegate('.openappnote', 'click', function() {
        var apptype = $(this).attr('data-app-type');
        var id = $(this).attr('data-id');
        $('#create_applicationnote #noteid').val(id);
        $('#create_applicationnote #type').val(apptype);
        $('#create_applicationnote').modal('show');
    });

    $(document).delegate('.openappappoint', 'click', function() {
        var id = $(this).attr('data-id');
        var apptype = $(this).attr('data-app-type');
        $('#create_applicationappoint #type').val(apptype);
        $('#create_applicationappoint #appointid').val(id);
        $('#create_applicationappoint').modal('show');
    });

    $(document).delegate('.openclientemail', 'click', function() {
        var id = $(this).attr('data-id');
        var apptype = $(this).attr('data-app-type');
        $('#applicationemailmodal #type').val(apptype);
        $('#applicationemailmodal #appointid').val(id);
        $('#applicationemailmodal').modal('show');
    });

    $(document).delegate('.openchecklist', 'click', function() {
        var id = $(this).attr('data-id');
        var type = $(this).attr('data-type');
        var typename = $(this).attr('data-typename');
        $('#create_checklist #checklistapp_id').val(id);
        $('#create_checklist #checklist_type').val(type);
        $('#create_checklist #checklist_typename').val(typename);
        $('#create_checklist').modal('show');
    });

    $(document).delegate('.openpaymentschedule', 'click', function() {
        var id = $(this).attr('data-id');
        $('#addpaymentschedule').modal('show');
        $('.popuploader').show();
        $.ajax({
            url: App.getUrl('addScheduleInvoiceDetail') || siteUrl + '/agent/addscheduleinvoicedetail',
            type: 'GET',
            data: { id: id },
            success: function(res) {
                $('.popuploader').hide();
                $('.showpoppaymentscheduledata').html(res);
                if (typeof flatpickr !== 'undefined') {
                    flatpickr(".datepicker", {
                        dateFormat: "Y-m-d",
                        allowInput: true
                    });
                }
            }
        });
    });

    // ============================================================================
    // PAYMENT SCHEDULE CALCULATIONS
    // ============================================================================

    $(document).delegate('.addfee', 'click', function() {
        var clonedval = $('.feetypecopy').html();
        $('.fee_type_sec .fee_fields').append('<div class="fee_fields_row field_clone">' + clonedval + '</div>');
    });

    $(document).delegate('.payremoveitems', 'click', function() {
        $(this).parent().parent().remove();
        schedulecalculatetotal();
    });

    $(document).delegate('.payfee_amount', 'keyup', function() {
        schedulecalculatetotal();
    });

    $(document).delegate('.paydiscount', 'keyup', function() {
        schedulecalculatetotal();
    });

    function schedulecalculatetotal() {
        var feeamount = 0;
        $('.payfee_amount').each(function() {
            if ($(this).val() != '') {
                feeamount += parseFloat($(this).val());
            }
        });
        var discount = 0;
        if ($('.paydiscount').val() != '') {
            discount = $('.paydiscount').val();
        }
        var netfee = feeamount - parseFloat(discount);
        $('.paytotlfee').html(feeamount.toFixed(2));
        $('.paynetfeeamt').html(netfee.toFixed(2));
    }

    // ============================================================================
    // APPLICATION APPOINTMENT CREATION
    // ============================================================================

    $(document).delegate('.createaddapointment', 'click', function() {
        $('#create_appoint').modal('show');
    });

    // ============================================================================
    // FILE UPLOAD MODAL
    // ============================================================================

    $(document).delegate('.openfileupload', 'click', function() {
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

    $(document).delegate('.opendocnote', 'click', function() {
        var id = '';
        var type = $(this).attr('data-app-type');
        var aid = $(this).attr('data-id');
        $(".checklisttype").val(type);
        $(".checklistid").val(id);
        $(".application_id").val(aid);
        $('#openfileuploadmodal').modal('show');
    });

    $(document).delegate('.due_date_sec a.due_date_btn', 'click', function() {
        $('.due_date_sec .due_date_col').show();
        $(this).hide();
        $('.checklistdue_date').val(1);
    });

    $(document).delegate('.remove_col a.remove_btn', 'click', function() {
        $('.due_date_sec .due_date_col').hide();
        $('.due_date_sec a.due_date_btn').show();
        $('.checklistdue_date').val(0);
    });

    // ============================================================================
    // APPLICATION STAGE MANAGEMENT
    // ============================================================================

    $(document).delegate('.nextstage', 'click', function() {
        var appliid = $(this).attr('data-id');
        var stage = $(this).attr('data-stage');
        $('.popuploader').show();
        $.ajax({
            url: App.getUrl('updateStage') || siteUrl + '/agent/updatestage',
            type: 'GET',
            datatype: 'json',
            data: { id: appliid, client_id: clientId },
            success: function(response) {
                $('.popuploader').hide();
                var obj = $.parseJSON(response);
                if (obj.status) {
                    $('.custom-error-msg').html('<span class="alert alert-success">' + obj.message + '</span>');
                    $('.curerentstage').text(obj.stage);
                    $('.progress-circle span').html(obj.width + ' %');
                    var over = '';
                    if (obj.width > 50) {
                        over = '50';
                    }
                    $("#progresscir").removeClass();
                    $("#progresscir").addClass('progress-circle');
                    $("#progresscir").addClass('prgs_' + obj.width);
                    $("#progresscir").addClass('over_' + over);
                    if (obj.displaycomplete) {
                        $('.completestage').show();
                        $('.nextstage').hide();
                    }
                    $.ajax({
                        url: App.getUrl('getApplicationsLogs') || siteUrl + '/agent/get-applications-logs',
                        type: 'GET',
                        data: { clientid: clientId, id: appliid },
                        success: function(responses) {
                            $('#accordion').html(responses);
                        }
                    });
                } else {
                    $('.custom-error-msg').html('<span class="alert alert-danger">' + obj.message + '</span>');
                }
            }
        });
    });

    $(document).delegate('.acceptapplication', 'click', function() {
        var appliid = $(this).attr('data-id');
        $('.popuploader').show();
        $.ajax({
            url: App.getUrl('completeStage') || siteUrl + '/agent/completestage',
            type: 'GET',
            datatype: 'json',
            data: { id: appliid, client_id: clientId },
            success: function(response) {
                $('.popuploader').hide();
                var obj = $.parseJSON(response);
                if (obj.status) {
                    $('.progress-circle span').html(obj.width + ' %');
                    var over = '';
                    if (obj.width > 50) {
                        over = '50';
                    }
                    $("#progresscir").removeClass();
                    $("#progresscir").addClass('progress-circle');
                    $("#progresscir").addClass('prgs_' + obj.width);
                    $("#progresscir").addClass('over_' + over);
                    $('.custom-error-msg').html('<span class="alert alert-success">' + obj.message + '</span>');
                    $('.ifdiscont').hide();
                    $('.revertapp').show();
                    $('#confirmcompleteModal').modal('hide');
                    $.ajax({
                        url: App.getUrl('getApplicationsLogs') || siteUrl + '/agent/get-applications-logs',
                        type: 'GET',
                        data: { clientid: clientId, id: appliid },
                        success: function(responses) {
                            $('#accordion').html(responses);
                        }
                    });
                } else {
                    $('.custom-error-msg').html('<span class="alert alert-danger">' + obj.message + '</span>');
                }
            }
        });
    });

    $(document).delegate('.backstage', 'click', function() {
        var appliid = $(this).attr('data-id');
        var stage = $(this).attr('data-stage');
        if (stage == 'Application') {
            // Do nothing
        } else {
            $('.popuploader').show();
            $.ajax({
                url: App.getUrl('updateBackStage') || siteUrl + '/agent/updatebackstage',
                type: 'GET',
                datatype: 'json',
                data: { id: appliid, client_id: clientId },
                success: function(response) {
                    var obj = $.parseJSON(response);
                    $('.popuploader').hide();
                    if (obj.status) {
                        $('.custom-error-msg').html('<span class="alert alert-success">' + obj.message + '</span>');
                        $('.curerentstage').text(obj.stage);
                        $('.progress-circle span').html(obj.width + ' %');
                        var over = '';
                        if (obj.width > 50) {
                            over = '50';
                        }
                        $("#progresscir").removeClass();
                        $("#progresscir").addClass('progress-circle');
                        $("#progresscir").addClass('prgs_' + obj.width);
                        $("#progresscir").addClass('over_' + over);
                        if (obj.displaycomplete == false) {
                            $('.completestage').hide();
                            $('.nextstage').show();
                        }
                        $.ajax({
                            url: App.getUrl('getApplicationsLogs') || siteUrl + '/agent/get-applications-logs',
                            type: 'GET',
                            data: { clientid: clientId, id: appliid },
                            success: function(responses) {
                                $('#accordion').html(responses);
                            }
                        });
                    } else {
                        $('.custom-error-msg').html('<span class="alert alert-danger">' + obj.message + '</span>');
                    }
                }
            });
        }
    });

    // ============================================================================
    // APPLICATION NOTES TAB
    // ============================================================================

    $(document).delegate('#notes-tab', 'click', function() {
        var appliid = $(this).attr('data-id');
        $('.if_applicationdetail').hide();
        $('.ifapplicationdetailnot').show();
        $.ajax({
            url: App.getUrl('getApplicationNotes') || siteUrl + '/agent/getapplicationnotes',
            type: 'GET',
            data: { id: appliid },
            success: function(response) {
                $('.popuploader').hide();
                $('#notes').html(response);
            }
        });
    });

    // ============================================================================
    // SELECT2 INITIALIZATION FOR APPOINTMENTS
    // ============================================================================

    $(".timezoneselects2").select2({
        dropdownParent: $("#create_appoint")
    });

    $(".timezoneselect2").select2({
        dropdownParent: $("#create_applicationappoint")
    });

    // ============================================================================
    // ATTACHMENT HANDLING
    // ============================================================================

    $('#attachments').on('change', function() {
        $('.showattachment').html('');
        var filename = $(this).val().replace(/.*(\/|\\)/, '');
        $('.showattachment').html(filename);
    });

    // ============================================================================
    // APPLICATION OWNERSHIP & SALES FORECAST
    // ============================================================================

    $(document).delegate('.opensuperagent', 'click', function() {
        var appid = $(this).attr('data-id');
        $('#superagent_application').modal('show');
        $('#superagent_application #siapp_id').val(appid);
    });

    $(document).delegate('.opentagspopup', 'click', function() {
        var appid = $(this).attr('data-id');
        $('#tags_clients').modal('show');
        $('#tags_clients #tags_client_id').val(appid);
        $(".tagsselec").select2({
            dropdownParent: $("#tags_clients .modal-content")
        });
    });

    $(document).delegate('.opensubagent', 'click', function() {
        var appid = $(this).attr('data-id');
        $('#subagent_application').modal('show');
        $('#subagent_application #sbapp_id').val(appid);
    });

    $(document).delegate('.application_ownership', 'click', function() {
        var appid = $(this).attr('data-id');
        var ration = $(this).attr('data-ration');
        $('#application_ownership #mapp_id').val(appid);
        $('#application_ownership .sus_agent').val($(this).attr('data-name'));
        $('#application_ownership .ration').val(ration);
        $('#application_ownership').modal('show');
    });

    $(document).delegate('.opensaleforcast', 'click', function() {
        var fapp_id = $(this).attr('data-id');
        var client_revenue = $(this).attr('data-client_revenue');
        var partner_revenue = $(this).attr('data-partner_revenue');
        var discounts = $(this).attr('data-discounts');
        $('#application_opensaleforcast #fapp_id').val(fapp_id);
        $('#application_opensaleforcast #client_revenue').val(client_revenue);
        $('#application_opensaleforcast #partner_revenue').val(partner_revenue);
        $('#application_opensaleforcast #discounts').val(discounts);
        $('#application_opensaleforcast').modal('show');
    });

    $(document).delegate('.openpaymentfee', 'click', function() {
        var appliid = $(this).attr('data-id');
        $('.popuploader').show();
        $('#new_fee_option').modal('show');
        $.ajax({
            url: App.getUrl('showProductFee') || siteUrl + '/agent/showproductfee',
            type: 'GET',
            data: { id: appliid },
            success: function(response) {
                $('.popuploader').hide();
                $('.showproductfee').html(response);
            }
        });
    });

    $(document).on("hidden.bs.modal", "#interest_service_view", function(e) {
        $('body').addClass('modal-open');
    });

    $(document).delegate('.opensaleforcastservice', 'click', function() {
        var fapp_id = $(this).attr('data-id');
        var client_revenue = $(this).attr('data-client_revenue');
        var partner_revenue = $(this).attr('data-partner_revenue');
        var discounts = $(this).attr('data-discounts');
        $('#application_opensaleforcastservice #fapp_id').val(fapp_id);
        $('#application_opensaleforcastservice #client_revenue').val(client_revenue);
        $('#application_opensaleforcastservice #partner_revenue').val(partner_revenue);
        $('#application_opensaleforcastservice #discounts').val(discounts);
        $('#interest_service_view').modal('hide');
        $('#application_opensaleforcastservice').modal('show');
    });

    $(document).delegate('.closeservmodal', 'click', function() {
        $('#interest_service_view').modal('hide');
        $('#application_opensaleforcastservice').modal('hide');
    });

    $(document).on("hidden.bs.modal", "#application_opensaleforcastservice", function(e) {
        $('body').addClass('modal-open');
    });

    // ============================================================================
    // FEE OPTION MANAGEMENT
    // ============================================================================

    $(document).delegate('#new_fee_option .fee_option_addbtn a', 'click', function() {
        var html = '<tr class="add_fee_option cus_fee_option"><td><select data-valid="required" class="form-control course_fee_type" name="course_fee_type[]"><option value="">Select Type</option><option value="Accommodation Fee">Accommodation Fee</option><option value="Administration Fee">Administration Fee</option><option value="Airline Ticket">Airline Ticket</option><option value="Airport Transfer Fee">Airport Transfer Fee</option><option value="Application Fee">Application Fee</option><option value="Bond">Bond</option></select></td><td><input type="number" value="0" class="form-control semester_amount" name="semester_amount[]"></td><td><input type="number" value="1" class="form-control no_semester" name="no_semester[]"></td><td class="total_fee"><span>0.00</span><input type="hidden"  class="form-control total_fee_am" value="0" name="total_fee[]"></td><td><input type="number" value="1" class="form-control claimable_terms" name="claimable_semester[]"></td><td><input type="number" class="form-control commission" name="commission[]"></td><td> <a href="javascript:;" class="removefeetype"><i class="fa fa-trash"></i></a></td></tr>';
        $('#new_fee_option #productitemview tbody').append(html);
    });

    $(document).delegate('#new_fee_option .removefeetype', 'click', function() {
        $(this).parent().parent().remove();
        calculateFeeTotal();
    });

    $(document).delegate('#new_fee_option .semester_amount', 'keyup', function() {
        var installment_amount = $(this).val();
        var cserv = 0.00;
        if (installment_amount != '') {
            cserv = installment_amount;
        }
        var installment = $(this).parent().parent().find('.no_semester').val();
        var totalamount = parseFloat(cserv) * parseInt(installment);
        $(this).parent().parent().find('.total_fee span').html(totalamount.toFixed(2));
        $(this).parent().parent().find('.total_fee_am').val(totalamount.toFixed(2));
        calculateFeeTotal();
    });

    $(document).delegate('#new_fee_option .no_semester', 'keyup', function() {
        var installment = $(this).val();
        var installment_amount = $(this).parent().parent().find('.semester_amount').val();
        var cserv = 0.00;
        if (installment_amount != '') {
            cserv = installment_amount;
        }
        var totalamount = parseFloat(cserv) * parseInt(installment);
        $(this).parent().parent().find('.total_fee span').html(totalamount.toFixed(2));
        $(this).parent().parent().find('.total_fee_am').val(totalamount.toFixed(2));
        calculateFeeTotal();
    });

    $(document).delegate('#new_fee_option .discount_amount', 'keyup', function() {
        var discount_amount = $(this).val();
        var discount_sem = $('.discount_sem').val();
        var cserv = 0.00;
        if (discount_sem != '') {
            cserv = discount_sem;
        }
        var cservs = 0.00;
        if (discount_amount != '') {
            cservs = discount_amount;
        }
        var dis = parseFloat(cservs) * parseFloat(cserv);
        $('.totaldis span').html(dis.toFixed(2));
        calculateFeeTotal();
        $('.totaldis .total_dis_am').val(dis.toFixed(2));
    });

    $(document).delegate('#new_fee_option .discount_sem', 'keyup', function() {
        var discount_sem = $(this).val();
        var discount_amount = $('.discount_amount').val();
        var cserv = 0.00;
        if (discount_sem != '') {
            cserv = discount_sem;
        }
        var cservs = 0.00;
        if (discount_amount != '') {
            cservs = discount_amount;
        }
        var dis = parseFloat(cservs) * parseFloat(cserv);
        $('.totaldis span').html(dis.toFixed(2));
        $('.totaldis .total_dis_am').val(dis.toFixed(2));
        calculateFeeTotal();
    });

    function calculateFeeTotal() {
        var price = 0;
        $('#new_fee_option .total_fee_am').each(function() {
            price += parseFloat($(this).val());
        });
        var discount_sem = $('.discount_sem').val();
        var discount_amount = $('.discount_amount').val();
        var cservd = 0.00;
        if (discount_sem != '') {
            cservd = discount_sem;
        }
        var cservs = 0.00;
        if (discount_amount != '') {
            cservs = discount_amount;
        }
        var dis = parseFloat(cservs) * parseFloat(cservd);
        var duductdis = price - dis;
        $('#new_fee_option .net_totl').html(duductdis.toFixed(2));
    }

    // ============================================================================
    // PAYMENT SCHEDULE EDITING
    // ============================================================================

    $(document).delegate('.editpaymentschedule', 'click', function() {
        $('#editpaymentschedule').modal('show');
        $('.popuploader').show();
        $.ajax({
            url: App.getUrl('scheduleInvoiceDetail') || siteUrl + '/agent/scheduleinvoicedetail',
            type: 'GET',
            data: { id: $(this).attr('data-id'), t: 'application' },
            success: function(res) {
                $('.popuploader').hide();
                $('.showeditmodule').html(res);
                $(".editclientname").select2({
                    dropdownParent: $("#editpaymentschedule .modal-content")
                });
                if (typeof flatpickr !== 'undefined') {
                    flatpickr(".datepicker", {
                        dateFormat: "Y-m-d",
                        allowInput: true
                    });
                }
            }
        });
    });

    // ============================================================================
    // DRAG-AND-DROP FILE UPLOAD
    // ============================================================================

    $(document).delegate("#ddArea", "dragover", function() {
        $(this).addClass("drag_over");
        return false;
    });

    $(document).delegate("#ddArea", "dragleave", function() {
        $(this).removeClass("drag_over");
        return false;
    });

    $(document).delegate("#ddArea", "click", function(e) {
        file_explorer();
    });

    $(document).delegate("#ddArea", "drop", function(e) {
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
        uploadChecklistFormData(formData);
    });

    /**
     * Upload checklist form data (agent-specific)
     */
    function uploadChecklistFormData(form_data) {
        $('.popuploader').show();
        $.ajax({
            url: App.getUrl('applicationChecklistUpload') || siteUrl + '/agent/application/checklistupload',
            method: "POST",
            data: form_data,
            datatype: 'json',
            contentType: false,
            cache: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': App.getCsrf()
            },
            success: function(response) {
                var obj = $.parseJSON(response);
                $('.popuploader').hide();
                $('#openfileuploadmodal').modal('hide');
                $('.mychecklistdocdata').html(obj.doclistdata);
                $('.checklistuploadcount').html(obj.applicationuploadcount);
                $('.' + obj.type + '_checklists').html(obj.checklistdata);
                $('#selectfile').val('');
            }
        });
    }

    // Override file_explorer for checklist uploads
    var originalFileExplorer = window.file_explorer;
    window.file_explorer = function() {
        var selectfile = document.getElementById("selectfile");
        if (!selectfile) {
            console.warn("selectfile element not found");
            return;
        }
        selectfile.click();
        selectfile.onchange = function() {
            var files = selectfile.files;
            if (!files || files.length === 0) {
                return;
            }
            var formData = new FormData();
            for (var i = 0; i < files.length; i++) {
                formData.append("file[]", files[i]);
            }
            formData.append("type", $('.checklisttype').val());
            formData.append("typename", $('.checklisttypename').val());
            formData.append("id", $('.checklistid').val());
            formData.append("application_id", $('.application_id').val());
            uploadChecklistFormData(formData);
        };
    };

});

})(); // End async wrapper

