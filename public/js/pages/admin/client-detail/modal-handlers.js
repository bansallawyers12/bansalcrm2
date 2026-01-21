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

        function initModalSelect2(modalSelector) {
            if (typeof $.fn.select2 !== 'function') {
                return;
            }

            var $modal = $(modalSelector);
            $modal.find('select.select2').each(function(){
                var $select = $(this);
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }
                $select.select2({
                    dropdownParent: $modal
                });
            });
        }

        // Ensure Select2 inside Commission Invoice modal works correctly
        $(document).on('shown.bs.modal', '#opencommissionmodal', function(){
            initModalSelect2('#opencommissionmodal');
        });

        // General Invoice modal handler
        $(document).on('click', '.opengeneralinvoice', function(){
            $('#opengeneralinvoice').modal('show');
        });

        // Ensure Select2 inside General Invoice modal works correctly
        $(document).on('shown.bs.modal', '#opengeneralinvoice', function(){
            initModalSelect2('#opengeneralinvoice');
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
