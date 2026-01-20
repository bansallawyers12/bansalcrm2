/**
 * Admin Partner Detail - Payment Field Handlers
 *
 * Handles add payment modal fields and dynamic fee/payment rows.
 *
 * Dependencies:
 *   - jQuery
 */

'use strict';

(async function() {
    if (typeof window.vendorLibsReady !== 'undefined') {
        await window.vendorLibsReady;
    } else {
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined') {
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

jQuery(document).ready(function($){
    $(document).delegate('.addpaymentmodal','click', function(){
        var netamount = $(this).attr('data-netamount');
        var dueamount = $(this).attr('data-dueamount');
        var invoiceId = $(this).attr('data-invoiceid');
        $('#invoice_id').val(invoiceId);
        $('.invoicenetamount').html(netamount+' AUD');
        $('.totldueamount').html(dueamount);
        $('.totldueamount').attr('data-totaldue', dueamount);
        $('#addpaymentmodal').modal('show');
        $('.payment_field_clone').remove();
        $('.paymentAmount').val('');
    });

    $(document).delegate('.paymentAmount','keyup', function(){
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

    $(document).delegate('.payment_field_col .field_remove_col a.remove_col', 'click', function(){
        var $tr    = $(this).closest('.payment_field_clone');
        var trclone = $('.payment_field_clone').length;
        if(trclone > 0){
            $tr.remove();
            grandtotal();
        }
    });

    $(document).delegate('.fees_type_sec .fee_type_row .fees_type_clone a.remove_btn', 'click', function(){
        var $tr    = $(this).closest('.fees_type_clone');
        var trclone = $('.fees_type_clone').length;
        if(trclone > 0){
            $tr.remove();
            grandtotal();
        }
    });
});

})(); // End async wrapper
