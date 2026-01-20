/**
 * Admin Partner Detail - Notes Handlers Module
 * 
 * Handles note deadline checkbox, flatpickr initialization, and partner action modal
 * 
 * Dependencies:
 *   - jQuery
 *   - Flatpickr
 *   - config.js (App object)
 */

'use strict';

// ============================================================================
// ASYNC WRAPPER - Wait for vendor libraries before initialization
// ============================================================================

(async function() {
    // Wait for vendor libraries to be ready
    if (typeof window.vendorLibsReady !== 'undefined') {
        console.log('[notes-handlers.js] Waiting for vendorLibsReady promise...');
        await window.vendorLibsReady;
        console.log('[notes-handlers.js] Vendor libraries ready!');
    } else {
        // Fallback: Poll for vendor libraries
        console.log('[notes-handlers.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined' && typeof flatpickr !== 'undefined') {
                    console.log('[notes-handlers.js] All vendor libraries detected!');
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

// ============================================================================
// NOTE DEADLINE HANDLERS
// ============================================================================

jQuery(document).ready(function($){
    
    // Note deadline checkbox and text box field change
    const $checkbox = $('#note_deadline_checkbox');
    const $deadlineInput = $('#note_deadline');
    const $recurringSection = $('#recurring_type_section');

    function getCurrentDate() {
        const today = new Date();
        const day = String(today.getDate()).padStart(2, '0');
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const year = today.getFullYear();
        return `${day}/${month}/${year}`;
    }

    $checkbox.change(function () {
        if ($(this).is(':checked')) {
            $deadlineInput.prop('disabled', false);
            $recurringSection.show();
        } else {
            $deadlineInput.prop('disabled', true).val(getCurrentDate());
            $recurringSection.hide();
        }
    });

    $deadlineInput.on('input', function () {
        if ($checkbox.is(':checked') && $(this).val().trim() !== '') {
            $recurringSection.show();
        } else {
            $recurringSection.hide();
        }
    });
    
    // Flatpickr initialization
    if (typeof flatpickr !== 'undefined') {
        flatpickr('#popoverdatetime,#note_deadline', {
            dateFormat: 'd/m/Y',
            defaultDate: 'today',
            allowInput: true
        });
    }

    // Partner action modal handler
    $(document).delegate('.openpartneraction', 'click', function(){
        $('#create_partneraction').modal('show');
    });
    
    // Note deadline checkbox click handler
    $('#note_deadline_checkbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#note_deadline').prop('disabled', false);
            $('#note_deadline_checkbox').val(1);
        } else {
            $('#note_deadline').prop('disabled', true);
            $('#note_deadline_checkbox').val(0);
        }
    });
    
});

})(); // End async wrapper
