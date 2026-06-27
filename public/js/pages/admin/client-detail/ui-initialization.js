/**
 * Admin Client Detail - UI initialization
 */
'use strict';

(function() {
    jQuery(document).ready(function($){
        // Email template select is initialized by email-modal-tomselect.js on #emailmodal shown.bs.modal.
        // Do NOT init here while the modal is hidden — dropdownParent would bind to a hidden element,
        // causing the dropdown to render off-screen. The shown.bs.modal handler re-inits correctly.

        // Flatpickr replacements for receipt forms
        if (typeof flatpickr !== 'undefined') {
            $('.report_date_fields').each(function() {
                flatpickr(this, {
                    dateFormat: 'd/m/Y',
                    defaultDate: 'today',
                    allowInput: true
                });
            });
            $('.report_entry_date_fields').each(function() {
                flatpickr(this, {
                    dateFormat: 'd/m/Y',
                    defaultDate: 'today',
                    allowInput: true
                });
            });
        }

        if (typeof flatpickr !== 'undefined') {
            flatpickr('#edu_service_start_date', {
                dateFormat: 'd/m/Y',
                allowInput: true
            });
        }

        $('.filter_btn').on('click', function(){
            $('.filter_panel').slideToggle();
        });

        // Service type on change div
        $('.modal-body form#createservicetaken input[name="service_type"]').on('change', function(){
            var invid = $(this).attr('id');
            if(invid == 'Migration_inv'){
                $('.modal-body form#createservicetaken .is_Migration_inv').show();
                $('.modal-body form#createservicetaken .is_Migration_inv input').attr('data-valid', 'required');
                $('.modal-body form#createservicetaken .is_Eductaion_inv').hide();
                $('.modal-body form#createservicetaken .is_Eductaion_inv input').attr('data-valid', '');
            }
            else {
                $('.modal-body form#createservicetaken .is_Eductaion_inv').show();
                $('.modal-body form#createservicetaken .is_Eductaion_inv input').attr('data-valid', 'required');
                $('.modal-body form#createservicetaken .is_Migration_inv').hide();
                $('.modal-body form#createservicetaken .is_Migration_inv input').attr('data-valid', '');
            }
        });

        if ($('#changeassignee').length && typeof initTomSelect === 'function') {
            initTomSelect('#changeassignee', {
                width: '220px',
                multiple: true,
                closeOnSelect: false
            });
        }
    });
})();
