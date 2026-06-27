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
                closeOnSelect: false
            });
        }
        if (el.tomselect) {
            return el.tomselect;
        }
        return initTomSelect(el, {
            width: '100%',
            placeholder: 'Select',
            allowClear: true,
            dropdownParent: resolveDropdownParent(el)
        });
    }

    function initChangeAssigneeTomSelect(container) {
        var root = container && container.nodeType === 1 ? container : document;
        var el = root.querySelector ? root.querySelector('#changeassignee') : null;
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
            if (block.querySelector('#changeassignee')) {
                initTaskViewTomSelects(block);
            }
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
})(window);
