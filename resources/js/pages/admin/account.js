'use strict';

// ============================================================================
// ASYNC WRAPPER - Wait for vendor libraries before initialization
// ============================================================================
(async function() {
    if (typeof window.vendorLibsReady !== 'undefined') {
        await window.vendorLibsReady;
    } else {
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined' && typeof $.fn.select2 === 'function') {
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

    // ============================================================================
    // MAIN JQUERY READY BLOCK
    // ============================================================================
    jQuery(document).ready(function($){
        const getUrl = (key, fallback) => {
            if (typeof App !== 'undefined' && typeof App.getUrl === 'function') {
                return App.getUrl(key) || fallback;
            }
            return fallback;
        };

        const recipientsUrl = getUrl('getRecipients', getUrl('siteUrl', '') + '/clients/get-recipients');
        const templatesUrl = getUrl('getTemplates', getUrl('siteUrl', '') + '/get-templates');
        const recipientOpts = { url: recipientsUrl, dropdownParent: '#emailmodal' };

        if (window.RecipientSelect) {
            RecipientSelect.init('#emailmodal .js-data-example-ajax', recipientOpts);
            RecipientSelect.init('#emailmodal .js-data-example-ajaxcc', recipientOpts);
        }

        // Email receipt modal handler
        $(document).on('click', '.clientemail', function(){
            $('#emailmodal').modal('show');

            const receiptName = $(this).attr('data-rec-name') || '';
            const receiptId = $(this).attr('data-id') || '';
            const receiptHref = $(this).attr('data-href') || '#';

            if ($('#receipt').length) {
                $('#receipt').text(receiptName).attr('href', receiptHref);
            }
            $('input[name="receipt"]').val(receiptId);

            if (window.RecipientSelect) {
                RecipientSelect.setClientEmailRecipient(
                    '#emailmodal .js-data-example-ajax',
                    $(this).attr('data-cus-id'),
                    $(this).attr('data-name'),
                    $(this).attr('data-email'),
                    'Client',
                    recipientOpts
                );
            }
        });

        // Email template handler
        $(document).on('change', '.selecttemplate', function(){
            const v = $(this).val();
            if (!v) {
                return;
            }

            $.ajax({
                url: templatesUrl,
                type: 'GET',
                datatype: 'json',
                data: { id: v },
                success: function(response){
                    const res = typeof response === 'string' ? JSON.parse(response) : response;
                    const subject = res.subject || '';
                    const description = res.description || '';

                    $('.selectedsubject').val(subject);
                    if ($("#emailmodal .tinymce-simple").length && typeof TinyMCEHelpers !== 'undefined') {
                        TinyMCEHelpers.resetBySelector("#emailmodal .tinymce-simple");
                        TinyMCEHelpers.setContentBySelector("#emailmodal .tinymce-simple", description);
                    }
                    $("#emailmodal .tinymce-simple").val(description);
                }
            });
        });

        // Income sharing payment modal handler
        $(document).on('click', '.openpaymentform', function(){
            const invoiceId = $(this).attr('data-invoiceid');
            const netAmount = $(this).attr('data-netamount');

            $('#invoice_id').val(invoiceId);
            $('.invoicenetamount').html(netAmount + ' AUD');
            $('.totldueamount').html('0 AUD');
            $('#addpaymentmodal').modal('show');
            $('.paymentAmount').val(netAmount);
        });
    });
})();
