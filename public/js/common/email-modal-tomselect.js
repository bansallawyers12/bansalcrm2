/**
 * Email modal template select + common page static Tom Select inits (Phase 6c).
 */
(function (window) {
    'use strict';

    function initCompactTomSelect(el, extra) {
        if (typeof initTomSelect !== 'function') {
            return null;
        }
        var base = typeof compactTomSelectOptions === 'function'
            ? compactTomSelectOptions(extra || {})
            : Object.assign({ width: '100%', minimumResultsForSearch: Infinity }, extra || {});
        return initTomSelect(el, base);
    }

    function initEmailTemplateSelects(modalEl) {
        if (typeof initTomSelect !== 'function') {
            return [];
        }
        var modal = modalEl;
        if (typeof modalEl === 'string') {
            modal = document.querySelector(modalEl);
        }
        if (!modal) {
            modal = document.querySelector('#emailmodal');
        }
        if (!modal) {
            return [];
        }
        var dropdownParent = modal.querySelector('.modal-content') || modal;
        var instances = [];
        modal.querySelectorAll('select.selecttemplate').forEach(function (element) {
            if (element.tomselect) {
                return;
            }
            element.classList.add('tomselect');
            var instance = initCompactTomSelect(element, { dropdownParent: dropdownParent });
            if (instance) {
                instances.push(instance);
            }
        });
        return instances;
    }

    function initGroupInvoicePartnerSelect() {
        document.querySelectorAll('select.group-invoice-partner-select').forEach(function (element) {
            if (element.tomselect) {
                return;
            }
            initCompactTomSelect(element);
        });
    }

    function initStaffTimezoneSelects() {
        document.querySelectorAll('select.staff-timezone-select').forEach(function (element) {
            if (element.tomselect) {
                return;
            }
            initCompactTomSelect(element);
        });
    }

    window.EmailModalTomSelect = {
        initTemplates: initEmailTemplateSelects,
        initGroupInvoicePartner: initGroupInvoicePartnerSelect,
        initStaffTimezone: initStaffTimezoneSelects,
        initCompact: initCompactTomSelect
    };

    function bindEmailModalTemplates() {
        if (!window.jQuery) {
            return;
        }
        window.jQuery(document).on('shown.bs.modal', '#emailmodal', function () {
            initEmailTemplateSelects(this);
        });
    }

    if (typeof whenTomSelectReady === 'function') {
        whenTomSelectReady(function () {
            bindEmailModalTemplates();
            initGroupInvoicePartnerSelect();
            initStaffTimezoneSelects();
        });
    } else if (window.jQuery) {
        window.jQuery(function () {
            bindEmailModalTemplates();
            initGroupInvoicePartnerSelect();
            initStaffTimezoneSelects();
        });
    }
})(window);
