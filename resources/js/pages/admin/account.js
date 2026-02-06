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

        function formatRepo(repo) {
            if (repo.loading) {
                return repo.text;
            }

            const $container = $(
                "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +
                    "<div  class='ag-flex ag-align-start'>" +
                        "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
                        "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small ></div>" +
                        "</div>" +
                    "</div>" +
                    "<div class='ag-flex ag-flex-column ag-align-end'>" +
                        "<span class='ui label yellow select2-result-repository__statistics'></span>" +
                    "</div>" +
                "</div>"
            );

            $container.find('.select2-result-repository__title').text(repo.name || repo.text || '');
            $container.find('.select2-result-repository__description').text(repo.email || '');
            $container.find('.select2-result-repository__statistics').append(repo.status || '');

            return $container;
        }

        function formatRepoSelection(repo) {
            return repo.name || repo.text;
        }

        function initRecipientSelect($el) {
            if (!$el.length || typeof $.fn.select2 !== 'function') {
                return;
            }

            if ($el.data('select2')) {
                return;
            }

            $el.select2({
                multiple: true,
                closeOnSelect: false,
                dropdownParent: $('#emailmodal'),
                ajax: {
                    url: recipientsUrl,
                    dataType: 'json',
                    processResults: function (data) {
                        return {
                            results: data.items || []
                        };
                    },
                    cache: true
                },
                templateResult: formatRepo,
                templateSelection: formatRepoSelection
            });
        }

        initRecipientSelect($('.js-data-example-ajax'));
        initRecipientSelect($('.js-data-example-ajaxcc'));

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

            const id = $(this).attr('data-cus-id');
            const email = $(this).attr('data-email');
            const name = $(this).attr('data-name');

            const $recipientSelect = $('.js-data-example-ajax');
            initRecipientSelect($recipientSelect);

            if (id && name) {
                const option = new Option(name, id, true, true);
                $recipientSelect.append(option).trigger('change');
            }

            if ($recipientSelect.data('select2')) {
                $recipientSelect.select2('open');
                $recipientSelect.select2('close');
            }

            if (email && typeof $.fn.select2 === 'function') {
                $recipientSelect.trigger({
                    type: 'select2:select',
                    params: { data: { id: id, text: name, email: email, status: 'Client' } }
                });
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
