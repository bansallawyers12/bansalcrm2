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
        var instances = [];
        modal.querySelectorAll('select.selecttemplate').forEach(function (element) {
            if (element.tomselect) {
                return;
            }
            element.classList.add('tomselect');
            var instance = initTomSelect(element, {
                width: '100%',
                dropdownParent: document.body,
                placeholder: 'Select template...'
            });
            if (instance) {
                instances.push(instance);
            }
        });
        return instances;
    }

    function destroyEmailTemplateSelects(modalEl) {
        var modal = modalEl;
        if (typeof modalEl === 'string') {
            modal = document.querySelector(modalEl);
        }
        if (!modal) {
            modal = document.querySelector('#emailmodal');
        }
        if (!modal || typeof destroyTomSelect !== 'function') {
            return;
        }
        modal.querySelectorAll('select.selecttemplate').forEach(function (element) {
            destroyTomSelect(element);
        });
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
        destroyTemplates: destroyEmailTemplateSelects,
        initGroupInvoicePartner: initGroupInvoicePartnerSelect,
        initStaffTimezone: initStaffTimezoneSelects,
        initCompact: initCompactTomSelect
    };

    function bindEmailModalTemplates() {
        if (!window.jQuery) {
            return;
        }
        window.jQuery(document).on('shown.bs.modal', '#emailmodal', function () {
            var modal = this;
            setTimeout(function () {
                initEmailTemplateSelects(modal);
            }, 0);
        });
        window.jQuery(document).on('hidden.bs.modal', '#emailmodal', function () {
            destroyEmailTemplateSelects(this);
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
