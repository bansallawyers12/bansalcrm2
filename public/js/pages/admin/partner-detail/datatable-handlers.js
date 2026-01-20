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

    $(".table-2").dataTable({
        "searching": false,
        "lengthChange": false,
        "columnDefs": [
            { "sortable": false }
        ],
        order: [[1, "desc"]]
    });

    // For student active list
    var table33 = $(".table-3").DataTable({
        dom: '<"row"<"col-md-4 text-start"l><"col-md-4 text-center"B><"col-md-4 text-end"f>>rtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21],
                    format: {
                        body: function (data) {
                            if (typeof data === 'string') {
                                data = data.replace(/<[^>]*>/g, '');
                                var txt = document.createElement('textarea');
                                txt.innerHTML = data;
                                data = txt.value;
                            }
                            return data || '';
                        }
                    }
                },
                filename: function() {
                    return 'Partner_Student_Data_' + partnerName.replace(/[^a-z0-9]/gi, '_') + '_' + new Date().toISOString().split('T')[0];
                },
                title: 'Partner Student Data - ' + partnerName,
                messageTop: 'Partner: ' + partnerName + '\nExport Date: ' + new Date().toLocaleString(),
                customize: function(xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    $('row c', sheet).attr('s', '50');
                }
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21],
                    format: {
                        body: function (data) {
                            if (typeof data === 'string') {
                                data = data.replace(/<[^>]*>/g, '');
                                var txt = document.createElement('textarea');
                                txt.innerHTML = data;
                                data = txt.value;
                            }
                            return data || '';
                        }
                    }
                },
                filename: function() {
                    return 'Partner_Student_Data_' + partnerName.replace(/[^a-z0-9]/gi, '_') + '_' + new Date().toISOString().split('T')[0];
                }
            }
        ],
        "searching": true,
        "lengthChange": true,
        "lengthMenu": [ [10, 20, 50,100,200,500,1000], [10, 20, 50,100,200,500,1000] ],
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { targets: 22, visible: false }
        ],
        order: [],
        drawCallback: function () {
            var api = this.api();
            var sumAllRecords = function (index) {
                return api
                    .column(index, { search: "applied" })
                    .data()
                    .reduce(function (a, b) {
                        return parseFloat(a) + parseFloat(b.replace(/[^0-9.-]+/g, "") || 0);
                    }, 0);
            };

            $("#total_commission_claimed").text(sumAllRecords(17).toFixed(2));
            $("#total_commission_anticipated").text(sumAllRecords(18).toFixed(2));
            $("#total_commission_paid").text(sumAllRecords(19).toFixed(2));
            $("#total_commission_pending").text(sumAllRecords(20).toFixed(2));
        }
    });

    var statusFilter = `
        <label>Filter by Status:
            <select id="statusFilter" class="form-control form-control-sm">
                <option value="">All</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
                <option value="Discontinued">Discontinued</option>
                <option value="Cancelled">Cancelled</option>
                <option value="Withdrawn">Withdrawn</option>
                <option value="Deferred">Deferred</option>
                <option value="Future">Future</option>
                <option value="VOE">VOE</option>
                <option value="Refund">Refund</option>
            </select>
        </label>`;

    $(".dataTables_filter").append(statusFilter);

    $("#statusFilter").on("change", function () {
        var statusFilterval = $(this).val();
        if (statusFilterval === "") {
            table33.column(21).search("").draw();
        } else {
            table33.column(21).search("^" + statusFilterval + "$", true, false).draw();
        }
    });

    $(document).on('change', '.note-field', function () {
        var studentid = $(this).attr('data-studentid');
        var newValue = $(this).val();
        $.ajax({
            url: App.getUrl('partnersSaveStudentNote'),
            method: 'POST',
            data: { rowId: studentid, note: newValue},
            success: function (response) {
                if (response && response.status) {
                    const studentId = response.studentId;
                    const studentNote = response.studentNote;
                    const rowIndex = table33.rows().eq(0).filter((rowIdx) => {
                        return table33.cell(rowIdx, 22).data() == studentId;
                    });

                    if (rowIndex.length > 0) {
                        table33.cell(rowIndex[0], 23).data(studentNote).draw();
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

    // For student inactive list
    var table331 = $(".table-31").dataTable({
        dom: '<"row"<"col-md-4 text-start"l><"col-md-4 text-center"B><"col-md-4 text-end"f>>rtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21],
                    format: {
                        body: function (data) {
                            if (typeof data === 'string') {
                                data = data.replace(/<[^>]*>/g, '');
                                var txt = document.createElement('textarea');
                                txt.innerHTML = data;
                                data = txt.value;
                            }
                            return data || '';
                        }
                    }
                },
                filename: function() {
                    return 'Partner_Student_Data_Inactive_' + partnerName.replace(/[^a-z0-9]/gi, '_') + '_' + new Date().toISOString().split('T')[0];
                },
                title: 'Partner Student Data (Inactive) - ' + partnerName,
                messageTop: 'Partner: ' + partnerName + '\nExport Date: ' + new Date().toLocaleString(),
                customize: function(xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    $('row c', sheet).attr('s', '50');
                }
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21],
                    format: {
                        body: function (data) {
                            if (typeof data === 'string') {
                                data = data.replace(/<[^>]*>/g, '');
                                var txt = document.createElement('textarea');
                                txt.innerHTML = data;
                                data = txt.value;
                            }
                            return data || '';
                        }
                    }
                },
                filename: function() {
                    return 'Partner_Student_Data_Inactive_' + partnerName.replace(/[^a-z0-9]/gi, '_') + '_' + new Date().toISOString().split('T')[0];
                }
            }
        ],
        "searching": true,
        "lengthChange": true,
        "lengthMenu": [ [10, 20, 50,100,200,500,1000], [10, 20, 50,100,200,500,1000] ],
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            }
        ],
        order: [],
        drawCallback: function () {
            var api = this.api();
            var sumColumn1 = function (index) {
                return api
                    .column(index, { page: "current" })
                    .data()
                    .reduce(function (a, b) {
                        return parseFloat(a) + parseFloat(b.replace(/[^0-9.-]+/g, "") || 0);
                    }, 0);
            };

            $("#total_commission_as_per_fee_reported1").text(sumColumn1(17).toFixed(2));
            $("#total_commission_anticipated1").text(sumColumn1(18).toFixed(2));
            $("#total_commission_paid_as_per_fee_reported1").text(sumColumn1(19).toFixed(2));
            $("#total_commission_pending1").text(sumColumn1(20).toFixed(2));
        }
    });

    $(document).on('change', '.note-field1', function () {
        var studentid = $(this).attr('data-studentid');
        var newValue = $(this).val();
        $.ajax({
            url: App.getUrl('partnersSaveStudentNote'),
            method: 'POST',
            data: { rowId: studentid, note: newValue},
            success: function (response) {
                if (response && response.status) {
                    const studentId = response.studentId;
                    const studentNote = response.studentNote;
                    const rowIndex = table331.rows().eq(0).filter((rowIdx) => {
                        return table331.cell(rowIdx, 22).data() == studentId;
                    });

                    if (rowIndex.length > 0) {
                        table331.cell(rowIndex[0], 23).data(studentNote).draw();
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

    $(".invoicetable").dataTable({
        "searching": false,
        "lengthChange": false,
        "columnDefs": [
            { "sortable": false, "targets": [0, 2, 3] }
        ],
        order: [[1, "desc"]]
    });
});

})(); // End async wrapper
