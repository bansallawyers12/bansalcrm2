/**
 * Admin Partner Detail - Invoice & Payment Handlers
 *
 * Handles student invoice creation, record invoices, and payments.
 *
 * Dependencies:
 *   - jQuery
 *   - Flatpickr
 *   - jQuery Confirm
 *   - config.js (App object)
 */

'use strict';

// ============================================================================
// ASYNC WRAPPER - Wait for vendor libraries before initialization
// ============================================================================

(async function() {
    if (typeof window.vendorLibsReady !== 'undefined') {
        console.log('[invoice-handlers.js] Waiting for vendorLibsReady promise...');
        await window.vendorLibsReady;
        console.log('[invoice-handlers.js] Vendor libraries ready!');
    } else {
        console.log('[invoice-handlers.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined' && typeof flatpickr !== 'undefined') {
                    console.log('[invoice-handlers.js] All vendor libraries detected!');
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

// ============================================================================
// INVOICE/PAYMENT HANDLERS
// ============================================================================

jQuery(document).ready(function($){
    const csrfToken = App.getCsrf();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    // ============================================================================
    // CREATE STUDENT INVOICE
    // ============================================================================

    if (typeof flatpickr !== 'undefined') {
        $('.invoice_date_fields').each(function() {
            flatpickr(this, {
                dateFormat: 'd/m/Y',
                defaultDate: 'today',
                allowInput: true
            });
        });
    }

    $(document).delegate('.openproductrinfo', 'click', function(){
        var clonedval =
                    ` <td>
                        <input name="id[]" type="hidden" value="" />
                        <select data-valid="required" class="form-control student_no_cls" name="student_id[]">
                        </select>
                    </td>
                    <td>
                        <input data-valid="required" class="form-control student_dob" name="student_dob[]" type="text" value="" />
                        <input class="form-control student_name" name="student_name[]" type="hidden" value="" />
                        <input class="form-control student_ref_no" name="student_ref_no[]" type="hidden" value="" />
                    </td>
                    <td>
                        <input data-valid="required" class="form-control student_course_name" name="course_name[]" type="text" value="" />
                    </td>
                    <td>
                        <input data-valid="required" class="form-control student_info_id" name="student_info_id[]" type="text" value="" />
                    </td>
                    <td>
                        <input data-valid="required" class="form-control" name="description[]" type="text" value="" />
                    </td>
                    <td>
                        <span class="currencyinput" style="display: inline-block;">$</span>
                        <input style="display: inline-block;" data-valid="required" class="form-control deposit_amount_per_row" type="text" value="" readonly/>
                        <input class="form-control deposit_amount_per_row_hidden" name="amount_aud[]" type="hidden" value="" />
                    </td>
                    <td>
                        <a class="removeitems" href="javascript:;"><i class="fa fa-times"></i></a>
                    </td>`;
        $('.productitem').append('<tr class="product_field_clone">'+clonedval+'</tr>');

        // Set student drop down for newly added row
        var partnerid =  $('#partner_id').val();
        $.ajax({
            type:'post',
            url: App.getUrl('partnersGetEnrolledStudentList'),
            sync:true,
            data: {partnerid:partnerid},
            success: function(response){
                var obj = $.parseJSON(response);
                $('.student_no_cls').last().html(obj.record_get);
            }
        });
        if (typeof flatpickr !== 'undefined') {
            var lastField = $('.invoice_date_fields').last()[0];
            if (lastField) {
                flatpickr(lastField, {
                    dateFormat: 'd/m/Y',
                    defaultDate: 'today',
                    allowInput: true
                });
            }
        }
    });

    $(document).delegate('.removeitems', 'click', function(){
        var $tr    = $(this).closest('.product_field_clone');
        var trclone = $('.product_field_clone').length;
        if(trclone > 0){
            $tr.remove();
        }
        grandtotalAccountTab();
    });

    function grandtotalAccountTab(){
        var total_deposit_amount_all_rows = 0;
        $('.productitem tr').each(function(){
            if($(this).find('.deposit_amount_per_row_hidden').val() != ''){
                var deposit_amount_per_row = $(this).find('.deposit_amount_per_row_hidden').val();
            }else{
                var deposit_amount_per_row = 0;
            }
            total_deposit_amount_all_rows += parseFloat(deposit_amount_per_row);
        });
        $('.total_deposit_amount_all_rows').html("$"+total_deposit_amount_all_rows.toFixed(2));
    }

    function getTopReceiptValInDB(type) {
        $.ajax({
            type:'post',
            url: App.getUrl('partnersGetTopReceiptValInDB'),
            sync:true,
            data: {type:type},
            success: function(response){
                var obj = $.parseJSON(response);
                if(obj.invoice_type == 1){
                    $('#top_value_db').val(obj.record_count);
                }
                else if(obj.invoice_type == 2){
                    $('#top_value_db_invoice').val(obj.record_count);
                }
                else if(obj.invoice_type == 3){
                    $('#top_value_db_payment').val(obj.record_count);
                }
            }
        });
    }

    function getTopInvoiceValInDB(type) {
        $.ajax({
            type:'post',
            url: App.getUrl('partnersGetTopInvoiceValInDB'),
            sync:true,
            data: {type:type},
            success: function(response){
                var obj = $.parseJSON(response);
                if(obj.invoice_type == 1){
                    $('.unique_trans_no').val(obj.max_invoice_id);
                    $('.unique_trans_no_hidden').val(obj.max_invoice_id);
                    $('#createpartnerstudentinvoicemodal').modal('show');
                }
            }
        });
    }

    function getEnrolledStudentList(partnerid) {
        $.ajax({
            type:'post',
            url: App.getUrl('partnersGetEnrolledStudentList'),
            sync:true,
            data: {partnerid:partnerid},
            success: function(response){
                var obj = $.parseJSON(response);
                $('.student_no_cls').html(obj.record_get);
            }
        });
    }

    $(document).delegate('.deposit_amount_per_row', 'keyup', function(){
        grandtotalAccountTab();
    });

    $(document).delegate('.createpartnerstudentinvoice', 'click', function(){
        var partnerid =  $(this).attr('data-partnerid');
        getTopReceiptValInDB(1);
        getEnrolledStudentList(partnerid);
        $('#function_type').val("add");
        getTopInvoiceValInDB(1);
    });

    $('#createpartnerstudentinvoicemodal').on('show.bs.modal', function() {
        $('.modal-dialog').css('max-width', '100%');
    });

    $(document).delegate('.student_no_cls', 'change', function(){
        var student_no_cls = $(this);
        var sel_student_id = $(this).val();
        if(sel_student_id != ""){
            $.ajax({
                type:'post',
                url: App.getUrl('partnersGetStudentInfo'),
                sync:true,
                data: {sel_student_id:sel_student_id},
                success: function(response){
                    var obj = $.parseJSON(response);
                    student_no_cls.closest('tr').find('.student_dob').val(obj.student_db);
                    student_no_cls.closest('tr').find('.student_name').val(obj.student_name);
                    student_no_cls.closest('tr').find('.student_ref_no').val(obj.student_ref_no);
                }
            });
        } else {
            student_no_cls.closest('tr').find('.student_dob').val("");
        }

        if(sel_student_id != "")
        {
            var partner_id = $('#partner_id').val();
            $.ajax({
                type:'post',
                url: App.getUrl('partnersGetStudentCourseInfo'),
                sync:true,
                data: {sel_student_id:sel_student_id,partner_id:partner_id},
                success: function(response){
                    var obj = $.parseJSON(response);
                    student_no_cls.closest('tr').find('.student_course_name').val(obj.student_course_info.coursename);
                    student_no_cls.closest('tr').find('.student_info_id').val(obj.student_course_info.student_id);
                    var commission_pending = obj.student_course_info.commission_pending || 0;
                    student_no_cls.closest('tr').find('.deposit_amount_per_row').val(commission_pending);
                    student_no_cls.closest('tr').find('.deposit_amount_per_row_hidden').val(commission_pending);
                    calculateTotalDeposit();
                }
            });
        } else {
            student_no_cls.closest('tr').find('.student_course_name').val("");
            student_no_cls.closest('tr').find('.student_info_id').val("");
            student_no_cls.closest('tr').find('.deposit_amount_per_row').val("");
            student_no_cls.closest('tr').find('.deposit_amount_per_row_hidden').val(0);
            calculateTotalDeposit();
        }
    });

    function calculateTotalDeposit() {
        var total_deposit_amount_all_rows = 0;

        $('.productitem tr').each(function () {
            var deposit_amount_per_row = parseFloat($(this).closest('tr').find('.deposit_amount_per_row_hidden').val()) || 0;
            total_deposit_amount_all_rows += deposit_amount_per_row;
        });

        $('.total_deposit_amount_all_rows').html("$" + total_deposit_amount_all_rows.toFixed(2));
    }

    $(document).delegate('.sent_option', 'change', function(){
        var sel_invoice_id = $(this).attr('data-invoiceid');
        var sel_option_val = $(this).val();
        if(sel_invoice_id != "" && sel_option_val == 'Yes'){
            $.confirm({
                title: 'Are you sure you want to confirm and send the invoice?',
                content: `
                    <label for="sent-date">Sent Date:</label>
                    <input type="text" id="sent-date" data-valid="required" class="datepicker-input" placeholder="Select sent date"><br>
                `,
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        action: function () {
                            var sentDate = $('#sent-date').val();
                            if (sentDate) {
                                $.ajax({
                                    type:'post',
                                    url: App.getUrl('partnersUpdateInvoiceSentOptionToYes'),
                                    sync:true,
                                    data: {sel_invoice_id:sel_invoice_id,sentDate:sentDate},
                                    success: function(){
                                        $('#TrRow_'+sel_invoice_id).find('td:last-child select').remove();
                                        $('#TrRow_'+sel_invoice_id).find('td:last-child').html('<span> Yes <br>'+ sentDate+'</span>');
                                        $('#TrRow_' + sel_invoice_id + ' td:nth-child(4) .updatedraftstudentinvoice').remove();
                                        $('#TrRow_' + sel_invoice_id + ' td:nth-child(4) .deletestudentinvoice').remove();
                                    }
                                });
                            } else {
                                alert('Please select sent date.');
                                return false;
                            }
                        }
                    },
                    cancel: {
                        text: 'Cancel',
                        action: function () {}
                    }
                },
                onContentReady: function () {
                    if (typeof flatpickr !== 'undefined') {
                        flatpickr('#sent-date', {
                            dateFormat: 'd/m/Y',
                            defaultDate: 'today',
                            allowInput: true
                        });
                    }
                }
            });
        }
    });

    $(document).delegate('.updatedraftstudentinvoice', 'click', function(){
        var invoiceid = $(this).data('invoiceid');
        getInfoByInvoiceId(invoiceid);
    });

    function getInfoByInvoiceId(invoiceid) {
        $.ajax({
            type:'post',
            url: App.getUrl('partnersGetInfoByInvoiceId'),
            sync:true,
            data: {invoiceid:invoiceid},
            success: function(response){
                var obj = $.parseJSON(response);
                if(obj.status){
                    $('#invoice_id_val').val(obj.invoiceid);
                    $('#top_value_db').val(obj.last_record_id);
                    $('#function_type').val("edit");
                    $('#createpartnerstudentinvoicemodal').modal('show');
                    if(obj.record_get){
                        var record_get = obj.record_get;
                        var sum = 0;
                        $('.productitem tr.clonedrow').remove();
                        $('.productitem tr.product_field_clone').remove();
                        $.each(record_get, function(index, subArray) {
                            var value_sum = parseFloat(subArray.amount_aud);
                            if (!isNaN(value_sum)) {
                                sum += value_sum;
                            }
                            var rowCls = index < 1 ? 'clonedrow' : 'product_field_clone';
                            var trRows_invoice = '<tr class="'+rowCls+'"><td><input name="id[]" type="hidden" value="'+subArray.id+'" /><select data-valid="required" class="form-control student_no_cls" name="student_id[]" id="studentnocls_'+subArray.id+'"><option value="">Select</option></select></td><td><input data-valid="required" class="form-control student_dob" name="student_dob[]" type="text" value="'+subArray.student_dob+'"><input class="form-control student_name" name="student_name[]" type="hidden" value="'+subArray.student_name+'"><input class="form-control student_ref_no" name="student_ref_no[]" type="hidden" value="'+subArray.student_ref_no+'"></td><td><input data-valid="required" class="form-control student_course_name" name="course_name[]" type="text" value="'+subArray.course_name+'"></td><td><input data-valid="required" class="form-control student_info_id" name="student_info_id[]" type="text" value="'+subArray.student_info_id+'"></td><td><input data-valid="required" class="form-control" name="description[]" type="text" value="'+subArray.description+'"></td><td><span class="currencyinput" style="display: inline-block;">$</span><input style="display: inline-block;" data-valid="required" class="form-control deposit_amount_per_row" type="text" value="'+subArray.amount_aud+'" readonly=""><input class="form-control deposit_amount_per_row_hidden" name="amount_aud[]" type="hidden" value="'+subArray.amount_aud+'"></td><td><a class="removeitems" href="javascript:;"><i class="fa fa-times"></i></a></td></tr>';
                            $('.productitem').append(trRows_invoice);
                            getEnrolledStudentListInEditMode(subArray.partner_id,subArray.id);
                            if(index < 1){
                                $('.invoice_date_fields').val(subArray.invoice_date);
                                $('.unique_trans_no_hidden').val(subArray.invoice_no);
                                $('.unique_trans_no').val(subArray.invoice_no);
                                $('#invoice_id').val(subArray.invoice_id);
                            }
                        });
                        $('.total_deposit_amount_all_rows').text("$"+sum.toFixed(2));
                    }
                }
            }
        });
    }

    $(document).delegate('.deletestudentinvoice', 'click', function(){
        var invoiceid = $(this).data('invoiceid');
        var invoicetype = $(this).data('invoicetype');
        var partnerid = $(this).data('partnerid');
        if( invoiceid != "" && confirm('Are you sure you want to delete this invoice?') ) {
            $.ajax({
                type:'post',
                url: App.getUrl('partnersDeleteStudentRecordByInvoiceId'),
                sync:true,
                data: {invoiceid:invoiceid,invoicetype:invoicetype,partnerid:partnerid},
                success: function(response){
                    var obj = $.parseJSON(response);
                    if(obj.status){
                        $('#TrRow_'+obj.invoiceid).remove();
                        $('.totDepoAmTillNow').html("$"+obj.sum);
                    }
                }
            });
        }
    });

    function getEnrolledStudentListInEditMode(partnerid,uniqueRowId) {
        $.ajax({
            type:'post',
            url: App.getUrl('partnersGetEnrolledStudentListInEditMode'),
            sync:true,
            data: {partnerid:partnerid,uniqueRowId:uniqueRowId},
            success: function(response){
                var obj = $.parseJSON(response);
                let dropdown = $(".productitem #studentnocls_"+uniqueRowId);
                dropdown.empty();
                dropdown.append(obj.record_get);
            }
        });
    }

    // ============================================================================
    // CREATE RECORD INVOICE
    // ============================================================================

    if (typeof flatpickr !== 'undefined') {
        $('.record_invoice_date_fields').each(function() {
            flatpickr(this, {
                dateFormat: 'd/m/Y',
                defaultDate: 'today',
                allowInput: true
            });
        });
        $('.record_sent_date_fields').each(function() {
            flatpickr(this, {
                dateFormat: 'd/m/Y',
                defaultDate: 'today',
                allowInput: true
            });
        });
    }

    $(document).delegate('.openproductrinfo_invoice', 'click', function(){
        var clonedval_invoice = $('.clonedrow_invoice').html();
        $('.productitem_invoice').append('<tr class="product_field_clone_invoice">'+clonedval_invoice+'</tr>');
        if (typeof flatpickr !== 'undefined') {
            var lastInvoiceField = $('.record_invoice_date_fields').last()[0];
            var lastSentField = $('.record_sent_date_fields').last()[0];
            if (lastInvoiceField) {
                flatpickr(lastInvoiceField, {
                    dateFormat: 'd/m/Y',
                    defaultDate: 'today',
                    allowInput: true
                });
            }
            if (lastSentField) {
                flatpickr(lastSentField, {
                    dateFormat: 'd/m/Y',
                    defaultDate: 'today',
                    allowInput: true
                });
            }
        }
    });

    $(document).delegate('.removeitems_invoice', 'click', function(){
        var $tr_invoice   = $(this).closest('.product_field_clone_invoice');
        var trclone_invoice = $('.product_field_clone_invoice').length;
        if(trclone_invoice > 0){
            $tr_invoice.remove();
        }
        grandtotalAccountTab_invoice();
    });

    $(document).delegate('.deposit_invoice_amount_per_row', 'keyup', function(){
        grandtotalAccountTab_invoice();
    });

    function grandtotalAccountTab_invoice(){
        var total_deposit_amount_all_rows_invoice = 0;
        $('.productitem_invoice tr').each(function(){
            var deposit_amount_per_row_invoice = $(this).find('.deposit_invoice_amount_per_row').val() || 0;
            total_deposit_amount_all_rows_invoice += parseFloat(deposit_amount_per_row_invoice);
        });
        $('.total_deposit_amount_all_rows_invoice').html("$"+total_deposit_amount_all_rows_invoice.toFixed(2));
    }

    $(document).delegate('.createrecordinvoice', 'click', function(){
        getTopReceiptValInDB(2);
        $('#function_type_invoice').val("add");
        $('#createrecordinvoicemodal').modal('show');
    });

    $('#createrecordinvoicemodal').on('show.bs.modal', function() {
        $('.modal-dialog').css('max-width', '80%');
    });

    $(document).delegate('.deletestudentrecordinvoice', 'click', function(){
        var id = $(this).data('uniqueid');
        var invoicetype = $(this).data('invoicetype');
        var partnerid = $(this).data('partnerid');
        if( id != "" && confirm('Are you sure you want to delete this record invoice?') ) {
            $.ajax({
                type:'post',
                url: App.getUrl('partnersDeleteStudentRecordInvoiceByInvoiceId'),
                sync:true,
                data: {id:id,invoicetype:invoicetype,partnerid:partnerid},
                success: function(response){
                    var obj = $.parseJSON(response);
                    if(obj.status && obj.invoicetype == 2){
                        $('#TrRecordRow_'+obj.id).remove();
                        $('.totDepoAmTillNow_invoice').html("$"+obj.sum);
                    }
                }
            });
        }
    });

    // ============================================================================
    // CREATE RECORD PAYMENT
    // ============================================================================

    if (typeof flatpickr !== 'undefined') {
        $('.record_payment_date_fields').each(function() {
            flatpickr(this, {
                dateFormat: 'd/m/Y',
                defaultDate: 'today',
                allowInput: true
            });
        });
    }

    $(document).delegate('.openproductrinfo_payment', 'click', function(){
        var clonedval_payment = $('.clonedrow_payment').html();
        $('.productitem_payment').append('<tr class="product_field_clone_payment">'+clonedval_payment+'</tr>');
        if (typeof flatpickr !== 'undefined') {
            var lastPaymentField = $('.record_payment_date_fields').last()[0];
            if (lastPaymentField) {
                flatpickr(lastPaymentField, {
                    dateFormat: 'd/m/Y',
                    defaultDate: 'today',
                    allowInput: true
                });
            }
        }
    });

    $(document).delegate('.removeitems_payment', 'click', function(){
        var $tr_payment   = $(this).closest('.product_field_clone_payment');
        var trclone_payment = $('.product_field_clone_payment').length;
        if(trclone_payment > 0){
            $tr_payment.remove();
        }
        grandtotalAccountTab_payment();
    });

    $(document).delegate('.deposit_payment_amount_per_row', 'keyup', function(){
        grandtotalAccountTab_payment();
    });

    $(document).delegate('.deposit_payment_amount_per_row', 'blur', function(){
        if( $(this).val() != ""){
            var randomNumber = $('#top_value_db_payment').val();
            randomNumber = Number(randomNumber);
            randomNumber = randomNumber + 1;
            $('#top_value_db_payment').val(randomNumber);
            randomNumber = "PAY"+randomNumber;
            $(this).closest('tr').find('.unique_record_payment_trans_no').val(randomNumber);
            $(this).closest('tr').find('.unique_record_payment_trans_no_hidden').val(randomNumber);
        } else {
            $(this).closest('tr').find('.unique_record_payment_trans_no').val();
            $(this).closest('tr').find('.unique_record_payment_trans_no_hidden').val();
        }
    });

    function grandtotalAccountTab_payment(){
        var total_deposit_amount_all_rows_payment = 0;
        $('.productitem_payment tr').each(function(){
            var deposit_amount_per_row_payment = $(this).find('.deposit_payment_amount_per_row').val() || 0;
            total_deposit_amount_all_rows_payment += parseFloat(deposit_amount_per_row_payment);
        });
        $('.total_deposit_amount_all_rows_payment').html("$"+total_deposit_amount_all_rows_payment.toFixed(2));
    }

    function getRecordedInvoiceList(partnerid) {
        $.ajax({
            type:'post',
            url: App.getUrl('partnersGetRecordedInvoiceList'),
            sync:true,
            data: {partnerid:partnerid},
            success: function(response){
                var obj = $.parseJSON(response);
                $('.invoice_no_cls').html(obj.record_get);
            }
        });
    }

    $(document).delegate('.createrecordpayment', 'click', function(){
        var partnerid =  $(this).attr('data-partnerid');
        getTopReceiptValInDB(3);
        getRecordedInvoiceList(partnerid);
        $('#function_type_payment').val("add");
        $('#createrecordpaymentmodal').modal('show');
    });

    $('#createrecordpaymentmodal').on('show.bs.modal', function() {
        $('.modal-dialog').css('max-width', '80%');
    });

    $(document).delegate('.deletestudentpaymentinvoice', 'click', function(){
        var id = $(this).data('uniqueid');
        var invoicetype = $(this).data('invoicetype');
        var partnerid = $(this).data('partnerid');
        if( id != "" && confirm('Are you sure you want to delete this payment invoice?') ) {
            $.ajax({
                type:'post',
                url: App.getUrl('partnersDeleteStudentPaymentInvoiceByInvoiceId'),
                sync:true,
                data: {id:id,invoicetype:invoicetype,partnerid:partnerid},
                success: function(response){
                    var obj = $.parseJSON(response);
                    if(obj.status && obj.invoicetype == 3){
                        $('#TrPaymentRow_'+obj.id).remove();
                        $('.totDepoAmTillNow_payment').html("$"+obj.sum);
                    }
                }
            });
        }
    });
});

})(); // End async wrapper
