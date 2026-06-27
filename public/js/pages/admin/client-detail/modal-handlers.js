/**
 * Admin Client Detail - Modal handlers
 */
'use strict';

(function() {
    jQuery(document).ready(function($){
        $(document).on('click', '.uploadmail', function(){
            $('#maclient_id').val(App.getPageConfig('clientId'));
            $('#uploadmail').modal('show');
        });

        // Commission Invoice modal handler
        $(document).on('click', '.opencommissioninvoice', function(){
            $('#opencommissionmodal').modal('show');
        });

        // General Invoice modal handler
        $(document).on('click', '.opengeneralinvoice', function(){
            $('#opengeneralinvoice').modal('show');
        });

        // Tom Select for commission / general invoice modals (global initModalTomSelects also runs on shown.bs.modal)
        $(document).on('shown.bs.modal', '#opencommissionmodal, #opengeneralinvoice', function(){
            if (typeof initModalTomSelects === 'function') {
                initModalTomSelects(this);
            }
        });

        // Tags popup modal handler
        $(document).on('click', '.opentagspopup', function(){
            var clientId = $(this).attr('data-id');
            if (clientId) {
                $('#tags_client_id').val(clientId);
            }
            var modalEl = document.getElementById('tags_clients');
            if (modalEl) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                } else if (typeof $ !== 'undefined' && $.fn.modal) {
                    $(modalEl).modal('show');
                }
            }
        });
    });
})();
