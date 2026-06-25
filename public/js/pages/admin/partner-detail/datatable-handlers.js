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

    // Student tab DataTables — only init when that tab is active (full page navigation).
    if (activeTab !== 'student') {
        return;
    }

    var studentDataUrl = (typeof AppConfig !== 'undefined' && AppConfig.urls && AppConfig.urls.partnersGetStudentTabData)
        ? AppConfig.urls.partnersGetStudentTabData
        : (typeof App !== 'undefined' && App.getUrl ? App.getUrl('partnersGetStudentTabData') : null);

    var studentTotalsUrl = (typeof AppConfig !== 'undefined' && AppConfig.urls && AppConfig.urls.partnersGetStudentTabTotals)
        ? AppConfig.urls.partnersGetStudentTabTotals
        : (typeof App !== 'undefined' && App.getUrl ? App.getUrl('partnersGetStudentTabTotals') : null);

    var studentExportUrl = (typeof AppConfig !== 'undefined' && AppConfig.urls && AppConfig.urls.partnersExportStudentTabData)
        ? AppConfig.urls.partnersExportStudentTabData
        : (typeof App !== 'undefined' && App.getUrl ? App.getUrl('partnersExportStudentTabData') : null);

    if (!studentDataUrl) {
        console.error('[partner-detail] partnersGetStudentTabData URL is not configured.');
        return;
    }

    if (!partnerNumericId) {
        console.error('[partner-detail] PageConfig.partnerId is not configured.');
        return;
    }

    var refreshTotalsTimer = null;
    function scheduleStudentTotalsRefresh(api, list) {
        if (!studentTotalsUrl) {
            return;
        }
        clearTimeout(refreshTotalsTimer);
        refreshTotalsTimer = setTimeout(function () {
            refreshStudentTotals(api, list);
        }, 400);
    }

    var studentColumnDefs = [
        { data: 0 }, { data: 1 }, { data: 2 }, { data: 3 }, { data: 4 },
        { data: 5 }, { data: 6 }, { data: 7 }, { data: 8 }, { data: 9 },
        { data: 10 }, { data: 11 }, { data: 12 }, { data: 13 }, { data: 14 },
        { data: 15 }, { data: 16 }, { data: 17 }, { data: 18 }, { data: 19 },
        { data: 20 }, { data: 21 }, { data: 22 }, { data: 23 }, { data: 24 }, { data: 25 }
    ];

    function buildStudentExportUrl(list, api) {
        var params = new URLSearchParams();
        params.set('partner_id', partnerNumericId);
        params.set('list', list);
        params.set('format', 'csv');
        if (api && typeof api.search === 'function') {
            var searchVal = api.search();
            if (searchVal) {
                params.set('search', searchVal);
            }
        }
        if (list === 'active') {
            var statusVal = $('#statusFilter').val();
            if (statusVal) {
                params.set('status_filter', statusVal);
            }
        }
        return studentExportUrl + '?' + params.toString();
    }

    function refreshStudentTotals(api, list) {
        if (!studentTotalsUrl) {
            return;
        }
        var payload = {
            partner_id: partnerNumericId,
            list: list,
            search: api && typeof api.search === 'function' ? api.search() : ''
        };
        if (list === 'active') {
            payload.status_filter = $('#statusFilter').val() || '';
        }
        $.get(studentTotalsUrl, payload, function (resp) {
            if (!resp || !resp.status) {
                return;
            }
            if (list === 'active') {
                $('#total_commission_claimed').text(resp.claimed);
                $('#total_commission_anticipated').text(resp.anticipated);
                $('#total_commission_paid').text(resp.paid);
                $('#total_commission_pending').text(resp.pending);
            } else {
                $('#total_commission_as_per_fee_reported1').text(resp.claimed);
                $('#total_commission_anticipated1').text(resp.anticipated);
                $('#total_commission_paid_as_per_fee_reported1').text(resp.paid);
                $('#total_commission_pending1').text(resp.pending);
            }
        });
    }

    function buildStudentExportButtons(list, apiGetter) {
        var suffix = list === 'inactive' ? '_Inactive_' : '_';
        return [
            {
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                action: function () {
                    var api = typeof apiGetter === 'function' ? apiGetter() : null;
                    window.location.href = buildStudentExportUrl(list, api);
                }
            },
            {
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-info btn-sm',
                action: function () {
                    var api = typeof apiGetter === 'function' ? apiGetter() : null;
                    window.location.href = buildStudentExportUrl(list, api);
                }
            }
        ];
    }

    function initPartnerStudentTable(options) {
        return $(options.tableSelector).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: studentDataUrl,
                type: 'GET',
                data: function (d) {
                    d.partner_id = partnerNumericId;
                    d.list = options.list;
                    if (options.statusFilterId) {
                        d.status_filter = $('#' + options.statusFilterId).val() || '';
                    }
                },
                error: function (xhr, textStatus) {
                    var responsePreview = xhr && xhr.responseText ? xhr.responseText.substring(0, 500) : '';
                    console.error('[partner-detail] Student tab data request failed:', xhr.status, textStatus, responsePreview);
                }
            },
            columns: studentColumnDefs,
            dom: studentToolbarDom,
            buttons: buildStudentExportButtons(options.list, options.apiGetter),
            searching: true,
            lengthChange: true,
            lengthMenu: [[10, 20, 50, 100, 200, 500], [10, 20, 50, 100, 200, 500]],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    }
                },
                {
                    targets: 22,
                    render: enrolmentTypeColumnRender(options.enrolmentClass)
                },
                { targets: 23, visible: false }
            ],
            order: [],
            initComplete: function () {
                setupStudentToolbar(this.api(), {
                    columnToggleSelector: options.columnToggleSelector,
                    toolbarHostSelector: options.toolbarHostSelector,
                    statusFilterId: options.statusFilterId || null,
                    serverSideStatusFilter: true
                });
                if (options.statusFilterId) {
                    $('#' + options.statusFilterId).off('change.partnerStudent').on('change.partnerStudent', function () {
                        var api = options.apiGetter();
                        api.ajax.reload();
                        scheduleStudentTotalsRefresh(api, options.list);
                    });
                }
                refreshStudentTotals(this.api(), options.list);
                this.api().on('search.dt', function () {
                    scheduleStudentTotalsRefresh(options.apiGetter(), options.list);
                });
            },
            drawCallback: function () {
                syncEnrolmentTypeSelects(this.api().table().container());
            }
        });
    }

    var table33 = initPartnerStudentTable({
        list: 'active',
        tableSelector: '.table-3',
        enrolmentClass: 'form-control form-control-sm enrolment-type-field',
        columnToggleSelector: '.student_drop_table_data',
        toolbarHostSelector: '.student_table_panel .student-dt-toolbar-host',
        statusFilterId: 'statusFilter',
        apiGetter: function () { return table33; }
    });

    var table331 = null;
    var inactiveStudentTableInitialized = false;

    function initInactiveStudentTable() {
        if (inactiveStudentTableInitialized || !$('.table-31').length) {
            return;
        }
        inactiveStudentTableInitialized = true;
        table331 = initPartnerStudentTable({
            list: 'inactive',
            tableSelector: '.table-31',
            enrolmentClass: 'form-control form-control-sm enrolment-type-field1',
            columnToggleSelector: '.student_drop_table_data1',
            toolbarHostSelector: '.student_table_panel1 .student-dt-toolbar-host',
            apiGetter: function () { return table331; }
        });
    }

    $('a#stdinactive-tab').on('shown.bs.tab', function () {
        initInactiveStudentTable();
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
                    if (table) {
                        updateEnrolmentTypeCell(table, response.studentId, response.enrolmentType);
                    }
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
                    if (!table331) {
                        return;
                    }
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
