/**
 * Invoice create page — Tom Select for customer, terms, and line-item selects.
 */
(function () {
    'use strict';

    function initInvoiceCreateTomSelects() {
        if (typeof waitForTomSelect !== 'function' || typeof initTomSelect !== 'function') {
            return;
        }

        waitForTomSelect().then(function () {
            var fullWidth = { width: '100%', allowClear: true };

            initTomSelectPreserveValue('#customer_name', Object.assign({
                placeholder: 'Select Customer'
            }, fullWidth));

            initTomSelectPreserveValue('select[name="terms"]', fullWidth);

            initTomSelectAllPreserveValues('.invoice-item-select', Object.assign({
                placeholder: 'Type or click to select an item.'
            }, fullWidth));
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initInvoiceCreateTomSelects);
    } else {
        initInvoiceCreateTomSelects();
    }
})();
