/**
 * Action page popovers + check-in modal — Tom Select for assignee / task group selects (Phase 6d).
 */
(function (window) {
    'use strict';

    function resolveElement(el) {
        if (!el) {
            return null;
        }
        if (el instanceof Element) {
            return el;
        }
        if (typeof el === 'string') {
            return document.querySelector(el);
        }
        if (window.jQuery && (el instanceof window.jQuery || el.jquery !== undefined)) {
            return el.length ? el[0] : null;
        }
        return el.nodeType === 1 ? el : null;
    }

    function normalizeOptionsHtml(html) {
        if (Array.isArray(html)) {
            return html.join('');
        }
        return html == null ? '' : String(html);
    }

    /**
     * Popovers: omit dropdownParent so Tom Select keeps the menu on each .ts-wrapper (below the control).
     * Modals: attach to .modal-content (same pattern as initModalTomSelects) to stay above the backdrop.
     */
    function resolveDropdownParent(container, selectEl) {
        var root = resolveElement(container);
        var select = resolveElement(selectEl);
        var modal = (root && root.closest && root.closest('.modal')) ||
            (select && select.closest && select.closest('.modal'));

        if (modal) {
            // Create In Person Client: menu stays on .ts-wrapper (see initModalTomSelects omitDropdownParent)
            if (modal.id === 'checkinmodal') {
                return null;
            }
            return modal.querySelector('.modal-content') || modal;
        }

        return null;
    }

    /** Searchable action popover selects (assignee, group, etc.). */
    function selectOpts(dropdownParent) {
        var opts = { width: '100%', maxOptions: null };
        if (dropdownParent) {
            opts.dropdownParent = dropdownParent;
        }
        return opts;
    }

    function initInContainer(container) {
        if (typeof initTomSelect !== 'function') {
            return [];
        }
        var root = resolveElement(container);
        if (!root) {
            return [];
        }
        var dropdownParent = resolveDropdownParent(root, null);
        var instances = [];

        root.querySelectorAll('select.assignee-tomselect, select.task_group, select.checkin-assignee-tomselect').forEach(function (element) {
            if (element.tomselect) {
                return;
            }
            if (!element.classList.contains('tomselect')) {
                element.classList.add('tomselect');
            }
            var instance = initTomSelect(element, selectOpts(dropdownParent));
            if (instance) {
                instances.push(instance);
            }
        });

        return instances;
    }

    function refreshAssigneeSelect(selectEl, html, container) {
        var element = resolveElement(selectEl);
        if (!element) {
            return null;
        }
        var dropdownParent = resolveDropdownParent(container, element);
        var opts = selectOpts(dropdownParent);
        var optionsHtml = normalizeOptionsHtml(html);

        if (typeof reinitTomSelectAfterHtml === 'function') {
            return reinitTomSelectAfterHtml(element, optionsHtml, opts);
        }

        if (element.tomselect) {
            element.tomselect.destroy();
        }
        element.innerHTML = optionsHtml;
        element.classList.add('tomselect');
        return initTomSelect(element, opts);
    }

    function getValue(el) {
        if (typeof getEnhancedSelectValue === 'function') {
            return getEnhancedSelectValue(el);
        }
        var element = resolveElement(el);
        return element ? (element.value || '') : '';
    }

    function setValue(el, value, silent) {
        if (typeof setEnhancedSelectValue === 'function') {
            setEnhancedSelectValue(el, value, silent);
            return;
        }
        var element = resolveElement(el);
        if (element) {
            element.value = value == null ? '' : value;
        }
    }

    function getAssigneeLabel(el) {
        var element = resolveElement(el);
        if (!element) {
            return '';
        }
        if (element.tomselect) {
            var value = element.tomselect.getValue();
            if (Array.isArray(value)) {
                value = value.length ? value[0] : '';
            }
            if (value == null || value === '') {
                return '';
            }
            var opt = element.tomselect.options[value];
            return opt ? (opt.text || opt.name || '') : '';
        }
        if (window.jQuery) {
            return window.jQuery(element).find('option:selected').text() || '';
        }
        var idx = element.selectedIndex;
        return idx >= 0 ? (element.options[idx].text || '') : '';
    }

    var ACTION_POPOVER_MIN_WIDTH = 480;

    /** Lock width before Tom Select init so popover does not collapse when native selects lose intrinsic sizing. */
    function lockActionPopoverWidth(popover) {
        if (!popover || !popover.querySelector('select.assignee-tomselect')) {
            return;
        }
        var viewportMax = window.innerWidth ? Math.max(280, window.innerWidth - 32) : ACTION_POPOVER_MIN_WIDTH;
        var floor = Math.min(ACTION_POPOVER_MIN_WIDTH, viewportMax);
        var measured = popover.getBoundingClientRect().width;
        var locked = measured > 0 ? Math.max(measured, floor) : floor;
        popover.style.minWidth = locked + 'px';
    }

    function bindEvents() {
        if (!window.jQuery) {
            return;
        }
        var $ = window.jQuery;

        $(document).on('shown.bs.popover', function () {
            setTimeout(function () {
                var popover = document.querySelector('.popover.show');
                if (popover) {
                    lockActionPopoverWidth(popover);
                    initInContainer(popover);
                }
            }, 0);
        });

        $(document).on('shown.bs.modal', '#checkinmodal, #actionPopoverModal', function () {
            var body = this.querySelector('.modal-body') || this;
            initInContainer(body);
        });
    }

    window.ActionPopoverTomSelect = {
        initContainer: initInContainer,
        refreshAssigneeSelect: refreshAssigneeSelect,
        getValue: getValue,
        setValue: setValue,
        getAssigneeLabel: getAssigneeLabel
    };

    window.actionPopoverSelectVal = function (el) {
        return getValue(el && el.jquery ? el[0] : el);
    };

    window.actionPopoverAssigneeLabel = function (el) {
        return getAssigneeLabel(el && el.jquery ? el[0] : el);
    };

    if (typeof whenTomSelectReady === 'function') {
        whenTomSelectReady(bindEvents);
    } else if (window.jQuery) {
        window.jQuery(bindEvents);
    }
})(window);
