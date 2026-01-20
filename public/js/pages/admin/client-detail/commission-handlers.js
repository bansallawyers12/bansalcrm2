/**
 * Admin Client Detail - Commission Handlers Module
 * 
 * Handles commission calculations and product fee management
 * 
 * Dependencies:
 *   - jQuery
 *   - config.js (App object)
 */

'use strict';

// ============================================================================
// ASYNC WRAPPER - Wait for vendor libraries before initialization
// ============================================================================

(async function() {
    // Wait for vendor libraries to be ready
    if (typeof window.vendorLibsReady !== 'undefined') {
        console.log('[commission-handlers.js] Waiting for vendorLibsReady promise...');
        await window.vendorLibsReady;
        console.log('[commission-handlers.js] Vendor libraries ready!');
    } else {
        // Fallback: Poll for vendor libraries
        console.log('[commission-handlers.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined') {
                    console.log('[commission-handlers.js] All vendor libraries detected!');
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

// ============================================================================
// COMMISSION CALCULATION HANDLERS
// ============================================================================

jQuery(document).ready(function($){
    
    // ============================================================================
    // PRODUCT FEE AUTO-CALCULATION
    // ============================================================================
    
    // Calculate Tution Fee (Total Course Fee - Scholarship Fee - Enrolment Fee - Material Fees)
    function calculateTutionFee() {
        // Get individual fee values
        var totalCourseFee = parseFloat($('#total_course_fee_amount').val()) || 0;
        var scholarshipFee = parseFloat($('#scholarship_fee_amount').val()) || 0;
        var enrolmentFee = parseFloat($('#enrolment_fee_amount').val()) || 0;
        var materialFees = parseFloat($('#material_fee_amount').val()) || 0;
        
        // Calculate tuition fee: All three fees are SUBTRACTED from Total Course Fee
        var totalFee = totalCourseFee - scholarshipFee - enrolmentFee - materialFees;
        
        // Ensure tuition fee doesn't go negative
        if (totalFee < 0) {
            totalFee = 0;
        }
        
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

    // ============================================================================
    // PAYMENT SCHEDULE CALCULATION
    // ============================================================================
    
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

    // ============================================================================
    // COMMISSION PERCENTAGE CALCULATIONS
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

    console.log('[commission-handlers.js] Commission handlers initialized');
});

})(); // End async wrapper
