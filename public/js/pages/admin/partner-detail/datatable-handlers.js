/**
 * Admin Partner Detail - DataTable Handlers
 *
 * Initializes DataTables and handles inline note updates.
 *
 * Dependencies:
 *   - jQuery
 *   - DataTables
 *   - config.js (App object)
 */

'use strict';

// ============================================================================
// ASYNC WRAPPER - Wait for vendor libraries before initialization
// ============================================================================

(async function() {
    if (typeof window.vendorLibsReady !== 'undefined') {
        console.log('[datatable-handlers.js] Waiting for vendorLibsReady promise...');
        await window.vendorLibsReady;
        console.log('[datatable-handlers.js] Vendor libraries ready!');
    } else {
        console.log('[datatable-handlers.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined' && typeof $.fn.DataTable === 'function') {
                    console.log('[datatable-handlers.js] All vendor libraries detected!');
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

// ============================================================================
// DATATABLE INITIALIZATION
// ============================================================================

jQuery(document).ready(function($){
    const partnerName = PageConfig.partnerName || 'Partner';
    const activeTab = (typeof PageConfig !== 'undefined' && PageConfig.activeTab) ? PageConfig.activeTab : 'application';
    const partnerNumericId = (typeof PageConfig !== 'undefined' && PageConfig.partnerId)
        ? parseInt(PageConfig.partnerId, 10)
        : 0;

    var enrolmentTypeLabels = {
        transfer_option: 'Transfer',
        course_progression: 'Course progression'
    };

    function parseEnrolmentTypeValue(data) {
        if (data === null || data === undefined) {
            return '';
        }

        if (typeof data === 'string' && data.indexOf('<select') !== -1) {
            var attrMatch = data.match(/data-enrolment-type="([^"]*)"/);
            if (attrMatch) {
                return attrMatch[1] || '';
            }

            var selectedMatch = data.match(/<option value="(transfer_option|course_progression)" selected/);
            if (selectedMatch) {
                return selectedMatch[1];
            }

            return '';
        }

        return String(data);
    }

    function buildEnrolmentTypeSelect(applicationId, currentValue, cssClass) {
        currentValue = parseEnrolmentTypeValue(currentValue);
        var html = '<select class="' + cssClass + '" data-application-id="' + applicationId + '" data-enrolment-type="' + currentValue + '">';
        html += '<option value=""' + (currentValue === '' ? ' selected="selected"' : '') + '>Select</option>';

        Object.keys(enrolmentTypeLabels).forEach(function (value) {
            html += '<option value="' + value + '"' + (currentValue === value ? ' selected="selected"' : '') + '>' + enrolmentTypeLabels[value] + '</option>';
        });

        html += '</select>';
        return html;
    }

    function enrolmentTypeColumnRender(cssClass) {
        return function (data, type, row) {
            var applicationId = row[23];
            var currentValue = parseEnrolmentTypeValue(data);

            if (type === 'display') {
                return buildEnrolmentTypeSelect(applicationId, currentValue, cssClass);
            }

            if (type === 'export' || type === 'filter' || type === 'sort') {
                return enrolmentTypeLabels[currentValue] || 'Select';
            }

            return currentValue;
        };
    }

    function syncEnrolmentTypeSelects(container) {
        $(container).find('.enrolment-type-field, .enrolment-type-field1').each(function () {
            var value = $(this).attr('data-enrolment-type') || '';
            $(this).val(value);
        });
    }

    var studentToolbarDom = '<"row student-dt-toolbar align-items-center g-2"<"col-auto"l><"col-auto"B><"col-auto ms-auto"f>>rtip';

    function buildStatusFilterHtml(selectId) {
        return '<label class="student-dt-status-filter mb-0">Filter by Status:' +
            '<select id="' + selectId + '" class="form-control form-control-sm">' +
            '<option value="">All</option>' +
            '<option value="In Progress">In Progress</option>' +
            '<option value="Completed">Completed</option>' +
            '<option value="Discontinued">Discontinued</option>' +
            '<option value="Cancelled">Cancelled</option>' +
            '<option value="Withdrawn">Withdrawn</option>' +
            '<option value="Deferred">Deferred</option>' +
            '<option value="Future">Future</option>' +
            '<option value="VOE">VOE</option>' +
            '<option value="Refund">Refund</option>' +
            '</select></label>';
    }

    function setupStudentToolbar(api, options) {
        var $wrapper = $(api.table().container()).closest('.dataTables_wrapper');
        var $toolbar = $wrapper.children('.student-dt-toolbar').first();
        var $toolbarHost = $(options.toolbarHostSelector);

        if (!$toolbar.length || !$toolbarHost.length) {
            return;
        }

        var $colToggle = $(options.columnToggleSelector).detach().removeAttr('style');
        $toolbar.prepend($('<div class="col-auto student-dt-columns">').append($colToggle));
        $toolbar.detach().appendTo($toolbarHost);

        var $filter = $toolbar.find('.dataTables_filter');
        $filter.addClass('student-dt-filter-controls');

        if (options.statusFilterId) {
            $filter.append(buildStatusFilterHtml(options.statusFilterId));
            if (!options.serverSideStatusFilter) {
                $('#' + options.statusFilterId).on('change', function () {
                    var statusFilterval = $(this).val();
                    if (statusFilterval === '') {
                        api.column(21).search('').draw();
                    } else {
                        api.column(21).search('^' + statusFilterval + '$', true, false).draw();
                    }
                });
            }
        }
    }

    if (activeTab === 'application' && $('.table-2').length) {
        var applicationDataUrl = (typeof AppConfig !== 'undefined' && AppConfig.urls && AppConfig.urls.partnersGetApplicationTabData)
            ? AppConfig.urls.partnersGetApplicationTabData
            : (typeof App !== 'undefined' && App.getUrl ? App.getUrl('partnersGetApplicationTabData') : null);

        $(".table-2").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: applicationDataUrl,
                type: 'GET',
                data: function (d) {
                    d.partner_id = partnerNumericId;
                }
            },
            searching: true,
            lengthChange: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[7, 'desc']],
            columns: [
                { data: 0 }, // Name
                { data: 1, orderable: false }, // Assignee
                { data: 2 }, // Product Name
                { data: 3 }, // Workflow
                { data: 4 }, // Current Stage
                { data: 5, orderable: false }, // Enrolment Type
                { data: 6 }, // Status
                { data: 7 }, // Added On
                { data: 8 }  // Last Updated
            ],
            columnDefs: [
                { targets: [1, 5], orderable: false },
                { targets: '_all', defaultContent: '' }
            ]
        });
    }

    if (activeTab === 'accounts' && $('.invoicetable').length) {
        $(".invoicetable").dataTable({
            "searching": false,
            "lengthChange": false,
            "columnDefs": [
                { "sortable": false, "targets": [0, 2, 3] }
            ],
            order: [[1, "desc"]]
        });
    }

    // Student tab — client-side DataTables with one bulk AJAX load (morning approach).
    if (activeTab !== 'student') {
        return;
    }

    var studentDataUrl = (typeof AppConfig !== 'undefined' && AppConfig.urls && AppConfig.urls.partnersGetStudentTabData)
        ? AppConfig.urls.partnersGetStudentTabData
        : (typeof App !== 'undefined' && App.getUrl ? App.getUrl('partnersGetStudentTabData') : null);

    if (!studentDataUrl) {
        console.error('[partner-detail] partnersGetStudentTabData URL is not configured.');
        return;
    }

    if (!partnerNumericId) {
        console.error('[partner-detail] PageConfig.partnerId is not configured.');
        return;
    }

    var studentAjaxConfig = {
        url: studentDataUrl,
        type: 'GET',
        data: function () {
            return { partner_id: partnerNumericId };
        },
        error: function (xhr, textStatus) {
            var responsePreview = xhr && xhr.responseText ? xhr.responseText.substring(0, 500) : '';
            console.error('[partner-detail] Student tab data request failed:', xhr.status, textStatus, responsePreview);
        }
    };

    var studentExportFormat = {
        body: function (data) {
            if (typeof data === 'string') {
                data = data.replace(/<[^>]*>/g, '');
                var txt = document.createElement('textarea');
                txt.innerHTML = data;
                data = txt.value;
            }
            return data || '';
        }
    };

    var studentColumnList = [
        { data: 0 }, { data: 1 }, { data: 2 }, { data: 3 }, { data: 4 },
        { data: 5 }, { data: 6 }, { data: 7 }, { data: 8 }, { data: 9 },
        { data: 10 }, { data: 11 }, { data: 12 }, { data: 13 }, { data: 14 },
        { data: 15 }, { data: 16 }, { data: 17 }, { data: 18 }, { data: 19 },
        { data: 20 }, { data: 21 }, { data: 22 }, { data: 23 }, { data: 24 }, { data: 25 }
    ];

    function sumStudentColumn(api, index, scope) {
        return api
            .column(index, scope)
            .data()
            .reduce(function (a, b) {
                var val = typeof b === 'string' ? b.replace(/[^0-9.-]+/g, '') : String(b);
                return parseFloat(a) + (parseFloat(val) || 0);
            }, 0);
    }

    var table33 = $('.table-3').DataTable({
        processing: true,
        ajax: $.extend({}, studentAjaxConfig, { dataSrc: 'active' }),
        columns: studentColumnList,
        dom: studentToolbarDom,
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22],
                    format: { body: studentExportFormat.body }
                },
                filename: function () {
                    return 'Partner_Student_Data_' + partnerName.replace(/[^a-z0-9]/gi, '_') + '_' + new Date().toISOString().split('T')[0];
                },
                title: 'Partner Student Data - ' + partnerName,
                messageTop: 'Partner: ' + partnerName + '\nExport Date: ' + new Date().toLocaleString()
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22],
                    format: { body: studentExportFormat.body }
                },
                filename: function () {
                    return 'Partner_Student_Data_' + partnerName.replace(/[^a-z0-9]/gi, '_') + '_' + new Date().toISOString().split('T')[0];
                }
            }
        ],
        searching: true,
        lengthChange: true,
        lengthMenu: [[10, 20, 50, 100, 200, 500, 1000], [10, 20, 50, 100, 200, 500, 1000]],
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {
                targets: 22,
                render: enrolmentTypeColumnRender('form-control form-control-sm enrolment-type-field')
            },
            { targets: 23, visible: false }
        ],
        order: [],
        initComplete: function () {
            setupStudentToolbar(this.api(), {
                columnToggleSelector: '.student_drop_table_data',
                toolbarHostSelector: '.student_table_panel .student-dt-toolbar-host',
                statusFilterId: 'statusFilter'
            });
        },
        drawCallback: function () {
            var api = this.api();
            $('#total_commission_claimed').text(sumStudentColumn(api, 17, { search: 'applied' }).toFixed(2));
            $('#total_commission_anticipated').text(sumStudentColumn(api, 18, { search: 'applied' }).toFixed(2));
            $('#total_commission_paid').text(sumStudentColumn(api, 19, { search: 'applied' }).toFixed(2));
            $('#total_commission_pending').text(sumStudentColumn(api, 20, { search: 'applied' }).toFixed(2));
            syncEnrolmentTypeSelects(api.table().container());
        }
    });

    var table331 = $('.table-31').DataTable({
        processing: true,
        ajax: $.extend({}, studentAjaxConfig, { dataSrc: 'inactive' }),
        columns: studentColumnList,
        dom: studentToolbarDom,
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22],
                    format: { body: studentExportFormat.body }
                },
                filename: function () {
                    return 'Partner_Student_Data_Inactive_' + partnerName.replace(/[^a-z0-9]/gi, '_') + '_' + new Date().toISOString().split('T')[0];
                },
                title: 'Partner Student Data (Inactive) - ' + partnerName,
                messageTop: 'Partner: ' + partnerName + '\nExport Date: ' + new Date().toLocaleString()
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22],
                    format: { body: studentExportFormat.body }
                },
                filename: function () {
                    return 'Partner_Student_Data_Inactive_' + partnerName.replace(/[^a-z0-9]/gi, '_') + '_' + new Date().toISOString().split('T')[0];
                }
            }
        ],
        searching: true,
        lengthChange: true,
        lengthMenu: [[10, 20, 50, 100, 200, 500, 1000], [10, 20, 50, 100, 200, 500, 1000]],
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {
                targets: 22,
                render: enrolmentTypeColumnRender('form-control form-control-sm enrolment-type-field1')
            },
            { targets: 23, visible: false }
        ],
        order: [],
        initComplete: function () {
            setupStudentToolbar(this.api(), {
                columnToggleSelector: '.student_drop_table_data1',
                toolbarHostSelector: '.student_table_panel1 .student-dt-toolbar-host'
            });
        },
        drawCallback: function () {
            var api = this.api();
            $('#total_commission_as_per_fee_reported1').text(sumStudentColumn(api, 17, { page: 'current' }).toFixed(2));
            $('#total_commission_anticipated1').text(sumStudentColumn(api, 18, { page: 'current' }).toFixed(2));
            $('#total_commission_paid_as_per_fee_reported1').text(sumStudentColumn(api, 19, { page: 'current' }).toFixed(2));
            $('#total_commission_pending1').text(sumStudentColumn(api, 20, { page: 'current' }).toFixed(2));
            syncEnrolmentTypeSelects(api.table().container());
        }
    });

    function updateEnrolmentTypeCell(table, studentId, enrolmentType) {
        var rowIndex = table.rows().eq(0).filter(function (rowIdx) {
            return table.cell(rowIdx, 23).data() == studentId;
        });

        if (rowIndex.length > 0) {
            table.cell(rowIndex[0], 22).data(enrolmentType || '').draw(false);
            syncEnrolmentTypeSelects(table.table().container());
        }
    }

    $(document).on('change', '.enrolment-type-field, .enrolment-type-field1', function () {
        var applicationId = $(this).data('application-id');
        var newValue = $(this).val();
        var tableType = $(this).hasClass('enrolment-type-field1') ? 'inactive' : 'active';

        $.ajax({
            url: App.getUrl('partnersSaveStudentEnrolmentType'),
            method: 'POST',
            data: {
                rowId: applicationId,
                enrolment_type: newValue,
                table: tableType,
                _token: App.getCsrf()
            },
            success: function (response) {
                if (response && response.status) {
                    var table = tableType === 'inactive' ? table331 : table33;
                    updateEnrolmentTypeCell(table, response.studentId, response.enrolmentType);
                    $('.custom-error-msg').html('<span class="alert alert-success">' + response.message + '</span>');
                } else {
                    $('.custom-error-msg').html('<span class="alert alert-danger">' + (response ? response.message : 'Failed to update enrolment type') + '</span>');
                }
            },
            error: function (error) {
                console.error('Error saving enrolment type:', error);
                $('.custom-error-msg').html('<span class="alert alert-danger">Failed to update enrolment type. Please try again.</span>');
            }
        });
    });

    $(document).on('change', '.note-field', function () {
        var studentid = $(this).attr('data-studentid');
        var newValue = $(this).val();
        $.ajax({
            url: App.getUrl('partnersSaveStudentNote'),
            method: 'POST',
            data: { rowId: studentid, note: newValue, _token: App.getCsrf()},
            success: function (response) {
                if (response && response.status) {
                    const studentId = response.studentId;
                    const studentNote = response.studentNote;
                    const rowIndex = table33.rows().eq(0).filter((rowIdx) => {
                        return table33.cell(rowIdx, 23).data() == studentId;
                    });

                    if (rowIndex.length > 0) {
                        table33.cell(rowIndex[0], 24).data(studentNote).draw();
                    }
                    $('.custom-error-msg').html('<span class="alert alert-success">'+response.message+'</span>');
                } else {
                    $('.custom-error-msg').html('<span class="alert alert-danger">'+(response ? response.message : 'Failed to save note')+'</span>');
                }
            },
            error: function (error) {
                console.error('Error saving note:', error);
            }
        });
    });

    $(document).on('change', '.note-field1', function () {
        var studentid = $(this).attr('data-studentid');
        var newValue = $(this).val();
        $.ajax({
            url: App.getUrl('partnersSaveStudentNote'),
            method: 'POST',
            data: { rowId: studentid, note: newValue, _token: App.getCsrf()},
            success: function (response) {
                if (response && response.status) {
                    const studentId = response.studentId;
                    const studentNote = response.studentNote;
                    const rowIndex = table331.rows().eq(0).filter((rowIdx) => {
                        return table331.cell(rowIdx, 23).data() == studentId;
                    });

                    if (rowIndex.length > 0) {
                        table331.cell(rowIndex[0], 24).data(studentNote).draw();
                    }
                    $('.custom-error-msg').html('<span class="alert alert-success">'+response.message+'</span>');
                } else {
                    $('.custom-error-msg').html('<span class="alert alert-danger">'+(response ? response.message : 'Failed to save note')+'</span>');
                }
            },
            error: function (error) {
                console.error('Error saving note:', error);
            }
        });
    });
});

})(); // End async wrapper
