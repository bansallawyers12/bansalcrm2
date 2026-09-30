/**
 * Tom Select init for AJAX-injected task / assignee / check-in views (#changeassignee, etc.).
 */
(function (window) {
    'use strict';

    var TASK_VIEW_URLS = [
        'get-assigne-detail',
        'get-task-detail',
        'get-checkin-detail',
        'getotherinfo'
    ];

    function resolveDropdownParent(element) {
        if (!element) {
            return 'body';
        }
        var modal = element.closest('.modal');
        if (modal) {
            if (modal.id === 'checkindetailmodal') {
                return null;
            }
            return modal.querySelector('.modal-content') || modal;
        }
        return 'body';
    }

    function initSingleChangeAssignee(el) {
        if (!el || typeof initTomSelect !== 'function') {
            return null;
        }
        if (el.hasAttribute('multiple')) {
            if (el.tomselect) {
                return el.tomselect;
            }
            return initTomSelect(el, {
                width: '220px',
                multiple: true,
                closeOnSelect: false,
                maxOptions: null
            });
        }
        if (el.tomselect) {
            return el.tomselect;
        }
        var singleOpts = {
            width: '100%',
            placeholder: 'Select',
            allowClear: true,
            maxOptions: null
        };
        var dropdownParent = resolveDropdownParent(el);
        if (dropdownParent) {
            singleOpts.dropdownParent = dropdownParent;
        }
        return initTomSelect(el, singleOpts);
    }

    function initChangeAssigneeTomSelect(container) {
        var root = container && container.nodeType === 1 ? container : document;
        var el = root.querySelector ? root.querySelector('#changeassignee') : null;
        return initSingleChangeAssignee(el);
    }

    function changeAssigneeTomSelectOptions(el) {
        var opts = {
            width: '100%',
            placeholder: 'Select assignee',
            allowClear: true,
            maxOptions: null,
            openOnFocus: true
        };
        var dropdownParent = resolveDropdownParent(el);
        if (dropdownParent) {
            opts.dropdownParent = dropdownParent;
        }
        return opts;
    }

    /**
     * Re-init #changeassignee after the In Person assignee row is shown. Tom Select
     * can mis-render when first initialized inside display:none.
     */
    function refreshChangeAssigneeTomSelect(container) {
        var root = container && container.nodeType === 1 ? container : document;
        var el = root.querySelector ? root.querySelector('#changeassignee') : null;
        if (!el) {
            return null;
        }

        if (el.tomselect) {
            if (typeof destroyTomSelect === 'function') {
                destroyTomSelect(el);
            } else {
                el.tomselect.destroy();
            }
        }

        if (typeof initTomSelectPreserveValue === 'function') {
            return initTomSelectPreserveValue(el, changeAssigneeTomSelectOptions(el));
        }

        return initSingleChangeAssignee(el);
    }

    function initTaskViewTomSelects(container) {
        initChangeAssigneeTomSelect(container);
    }

    function initInjectedDegreeLevel(container) {
        if (typeof initTomSelect !== 'function') {
            return;
        }
        var root = container && container.nodeType === 1 ? container : document;
        if (!root.querySelectorAll) {
            return;
        }
        root.querySelectorAll('select.degree_level.tomselect').forEach(function (el) {
            if (el.tomselect) {
                return;
            }
            initTomSelectPreserveValue(el, {
                width: '100%',
                allowClear: true,
                dropdownParent: resolveDropdownParent(el)
            });
        });
    }

    function afterAjaxInject(url) {
        if (typeof whenTomSelectReady === 'function') {
            whenTomSelectReady(function () {
                runAfterInject(url);
            });
            return;
        }
        if (typeof waitForTomSelect === 'function') {
            waitForTomSelect().then(function () {
                runAfterInject(url);
            });
        }
    }

    function runAfterInject(url) {
        if (url.indexOf('getotherinfo') !== -1) {
            document.querySelectorAll('.showsubjecthtml').forEach(function (block) {
                initInjectedDegreeLevel(block);
            });
            return;
        }

        document.querySelectorAll('.taskview, .showchecindetail').forEach(function (block) {
            if (!block.querySelector('#changeassignee')) {
                return;
            }
            // In Person Details assignee: init on pencil click only (hidden until then).
            if (block.classList.contains('showchecindetail')) {
                return;
            }
            initTaskViewTomSelects(block);
        });
    }

    function urlMatchesTaskView(url) {
        if (!url) {
            return false;
        }
        for (var i = 0; i < TASK_VIEW_URLS.length; i += 1) {
            if (url.indexOf(TASK_VIEW_URLS[i]) !== -1) {
                return true;
            }
        }
        return false;
    }

    if (window.jQuery) {
        window.jQuery(document).ajaxSuccess(function (event, xhr, settings) {
            var url = settings && settings.url ? settings.url : '';
            if (urlMatchesTaskView(url)) {
                afterAjaxInject(url);
            }
        });
    }

    window.initTaskViewTomSelects = initTaskViewTomSelects;
    window.initChangeAssigneeTomSelect = initChangeAssigneeTomSelect;
    window.refreshChangeAssigneeTomSelect = refreshChangeAssigneeTomSelect;
})(window);
