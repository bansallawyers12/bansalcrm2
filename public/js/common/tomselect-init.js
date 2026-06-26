/**
 * Tom Select utilities (Select2 migration — Phase 0 foundation).
 *
 * Usage:
 *   initTomSelect('#my-select', { placeholder: 'Choose…', allowClear: true });
 *   destroyTomSelect('#my-select');
 *   isTomSelect('#my-select');  isSelect2('#my-select');
 *   getEnhancementWrapper('#my-select');
 *   await waitForTomSelect();
 *
 * Migrated elements get classes `tomselect` + `tomselect-migrated` (global Select2 init skips both).
 * See docs/SELECT2-TOMSELECT-MIGRATION.md for Select2 → Tom Select option mapping.
 */
(function (window) {
    'use strict';

    /** Select2-only keys — never pass through to TomSelect constructor */
    var SELECT2_ONLY_KEYS = {
        ajax: true,
        data: true,
        templateResult: true,
        templateSelection: true,
        escapeMarkup: true,
        width: true,
        containerCssClass: true,
        dropdownCssClass: true,
        allowClear: true,
        minimumInputLength: true,
        tags: true,
        dropdownParent: true,
        multiple: true,
        closeOnSelect: true,
        minimumResultsForSearch: true,
        dropdownAutoWidth: true,
        language: true,
        matcher: true,
        sorter: true,
        maximumSelectionLength: true,
        tokenSeparators: true
    };

    function isJQuery(obj) {
        return !!(obj && window.jQuery && (obj.jquery !== undefined || obj instanceof window.jQuery));
    }

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
        if (isJQuery(el) && el.length) {
            return el[0];
        }
        if (el.nodeType === 1) {
            return el;
        }
        return null;
    }

    function ensurePlugin(plugins, name) {
        var list = plugins ? plugins.slice() : [];
        if (list.indexOf(name) === -1) {
            list.push(name);
        }
        return list;
    }

    /** Handles strings, DOM nodes, and jQuery objects (common in this codebase). */
    function normalizeTemplateOutput(rendered, escape, data) {
        if (rendered == null) {
            return '';
        }
        if (typeof rendered === 'string') {
            return rendered;
        }
        if (isJQuery(rendered)) {
            var node = rendered[0];
            return node ? (node.outerHTML || rendered.text() || '') : '';
        }
        if (rendered instanceof Element) {
            return rendered.outerHTML;
        }
        if (rendered && typeof rendered.text === 'string') {
            return rendered.text;
        }
        return escape(data && data.text ? data.text : '');
    }

    function stripSelect2Keys(config) {
        Object.keys(SELECT2_ONLY_KEYS).forEach(function (key) {
            delete config[key];
        });
    }

    function mapSelect2Options(options) {
        if (!options || typeof options !== 'object') {
            return {};
        }

        var mapped = {};
        var key;

        for (key in options) {
            if (!Object.prototype.hasOwnProperty.call(options, key)) {
                continue;
            }

            switch (key) {
                case 'dropdownParent':
                    mapped.dropdownParent = resolveElement(options.dropdownParent) || options.dropdownParent;
                    break;
                case 'allowClear':
                    if (options.allowClear) {
                        mapped.plugins = ensurePlugin(mapped.plugins, 'clear_button');
                    }
                    break;
                case 'plugins':
                    if (Array.isArray(options.plugins)) {
                        options.plugins.forEach(function (plugin) {
                            mapped.plugins = ensurePlugin(mapped.plugins, plugin);
                        });
                    }
                    break;
                case 'minimumInputLength':
                    mapped._minimumInputLength = options.minimumInputLength;
                    break;
                case 'ajax':
                    mapped._select2Ajax = options.ajax;
                    break;
                case 'data':
                    mapped._select2Data = options.data;
                    break;
                case 'templateResult':
                    mapped.render = mapped.render || {};
                    mapped.render.option = function (data, escape) {
                        return normalizeTemplateOutput(options.templateResult(data), escape, data);
                    };
                    break;
                case 'templateSelection':
                    mapped.render = mapped.render || {};
                    mapped.render.item = function (data, escape) {
                        return normalizeTemplateOutput(options.templateSelection(data), escape, data);
                    };
                    break;
                case 'width':
                    mapped._select2Width = options.width;
                    break;
                case 'escapeMarkup':
                    if (typeof options.escapeMarkup === 'function' && !options.templateResult) {
                        mapped.render = mapped.render || {};
                        mapped.render.option = function (data) {
                            return options.escapeMarkup(data.text || '');
                        };
                        mapped.render.item = function (data) {
                            return options.escapeMarkup(data.text || '');
                        };
                    }
                    break;
                case 'containerCssClass':
                    mapped.wrapperClass = options.containerCssClass;
                    break;
                case 'dropdownCssClass':
                    mapped.dropdownClass = options.dropdownCssClass;
                    break;
                case 'tags':
                    if (options.tags) {
                        mapped.create = true;
                    }
                    break;
                case 'closeOnSelect':
                    mapped.closeAfterSelect = options.closeOnSelect !== false;
                    break;
                case 'maximumSelectionLength':
                    mapped.maxItems = options.maximumSelectionLength;
                    break;
                case 'minimumResultsForSearch':
                    if (options.minimumResultsForSearch === Infinity) {
                        mapped._disableSearch = true;
                    }
                    break;
                default:
                    if (!SELECT2_ONLY_KEYS[key]) {
                        mapped[key] = options[key];
                    }
            }
        }

        return mapped;
    }

    function applyWidth(instance, width) {
        if (!width || !instance || !instance.wrapper) {
            return;
        }
        if (width === '100%' || width === 'resolve' || width === 'style') {
            instance.wrapper.style.width = '100%';
        } else {
            instance.wrapper.style.width = width;
        }
    }

    function ensurePlaceholderOption(element, placeholder) {
        if (!placeholder || element.querySelector('option[value=""]')) {
            return;
        }
        var option = document.createElement('option');
        option.value = '';
        option.textContent = placeholder;
        element.insertBefore(option, element.firstChild);
    }

    function buildAjaxLoader(ajax, minimumInputLength) {
        var $ = window.jQuery;

        return function (query, callback) {
            if (query.length < (minimumInputLength || 0)) {
                callback();
                return;
            }

            var requestData = typeof ajax.data === 'function'
                ? ajax.data({ term: query, page: 1 })
                : { q: query };

            if (!$ || !$.ajax) {
                console.warn('[initTomSelect] jQuery.ajax required for ajax option mapping');
                callback();
                return;
            }

            $.ajax({
                url: ajax.url,
                dataType: ajax.dataType || 'json',
                data: requestData,
                success: function (response) {
                    var processed = ajax.processResults
                        ? ajax.processResults(response, { term: query, page: 1 })
                        : response;
                    var results = processed && processed.results ? processed.results : processed;
                    callback(Array.isArray(results) ? results : []);
                },
                error: function () {
                    callback();
                }
            });
        };
    }

    function markMigration(element) {
        element.classList.add('tomselect', 'tomselect-migrated');
        element.setAttribute('data-enhanced', 'tomselect');
    }

    function unmarkMigration(element) {
        element.classList.remove('tomselect', 'tomselect-migrated');
        element.removeAttribute('data-enhanced');
    }

    function initTomSelect(el, options) {
        if (typeof TomSelect === 'undefined') {
            console.warn('[initTomSelect] Tom Select is not loaded');
            return null;
        }

        var element = resolveElement(el);
        if (!element) {
            console.warn('[initTomSelect] Invalid element');
            return null;
        }

        if (element.tomselect) {
            return element.tomselect;
        }

        if (isSelect2(element) && window.jQuery) {
            window.jQuery(element).select2('destroy');
        }

        options = options || {};

        if (options.multiple && !element.hasAttribute('multiple')) {
            element.setAttribute('multiple', 'multiple');
        }

        ensurePlaceholderOption(element, options.placeholder);
        markMigration(element);

        var config = mapSelect2Options(options);
        var minimumInputLength = config._minimumInputLength || 0;

        if (config._select2Ajax) {
            config.valueField = config.valueField || 'id';
            config.labelField = config.labelField || 'text';
            config.searchField = config.searchField || ['text'];
            config.load = buildAjaxLoader(config._select2Ajax, minimumInputLength);
            config.loadThrottle = config.loadThrottle || config._select2Ajax.delay || 250;
        }

        if (config._select2Data) {
            config.options = config._select2Data.map(function (item) {
                return {
                    value: item.id != null ? item.id : item.value,
                    text: item.text,
                    html: item.html
                };
            });
        }

        if (config._disableSearch) {
            config.controlInput = null;
        }

        var width = config._select2Width;

        delete config._minimumInputLength;
        delete config._select2Ajax;
        delete config._select2Data;
        delete config._select2Width;
        delete config._disableSearch;

        stripSelect2Keys(config);

        try {
            var instance = new TomSelect(element, config);
            applyWidth(instance, width);
            return instance;
        } catch (err) {
            console.error('[initTomSelect] Failed to initialize Tom Select', err);
            unmarkMigration(element);
            return null;
        }
    }

    function initTomSelectAll(selector, options) {
        var instances = [];
        document.querySelectorAll(selector).forEach(function (element) {
            var instance = initTomSelect(element, options);
            if (instance) {
                instances.push(instance);
            }
        });
        return instances;
    }

    function destroyTomSelect(el) {
        var element = resolveElement(el);
        if (element && element.tomselect) {
            element.tomselect.destroy();
            unmarkMigration(element);
        }
    }

    function isTomSelect(el) {
        var element = resolveElement(el);
        return !!(element && element.tomselect);
    }

    function isSelect2(el) {
        var element = resolveElement(el);
        if (!element) {
            return false;
        }
        if (window.jQuery && window.jQuery(element).data('select2')) {
            return true;
        }
        return element.classList.contains('select2-hidden-accessible');
    }

    function getEnhancementWrapper(el) {
        var element = resolveElement(el);
        if (!element) {
            return null;
        }
        if (element.tomselect && element.tomselect.wrapper) {
            return element.tomselect.wrapper;
        }
        if (window.jQuery) {
            var $wrapper = window.jQuery(element).next('.select2-container');
            if ($wrapper.length) {
                return $wrapper[0];
            }
        }
        var sibling = element.nextElementSibling;
        if (sibling && sibling.classList.contains('select2-container')) {
            return sibling;
        }
        return null;
    }

    function placeValidationError(el, errorHtml) {
        var element = resolveElement(el);
        if (!element) {
            return;
        }
        var wrapper = getEnhancementWrapper(element);
        if (wrapper) {
            wrapper.insertAdjacentHTML('afterend', errorHtml);
            return;
        }
        if (window.jQuery) {
            window.jQuery(element).after(errorHtml);
        }
    }

    function destroyEnhancedSelect(el) {
        var element = resolveElement(el);
        if (!element) {
            return;
        }
        if (element.tomselect) {
            destroyTomSelect(element);
            return;
        }
        if (isSelect2(element) && window.jQuery) {
            window.jQuery(element).select2('destroy');
        }
    }

    function reinitTomSelect(el, options) {
        destroyEnhancedSelect(el);
        return initTomSelect(el, options);
    }

    function resolveModalDropdownParent(modalEl) {
        var modal = resolveElement(modalEl);
        if (!modal) {
            return null;
        }
        return modal.querySelector('.modal-content') || modal;
    }

    /**
     * Init all select.tomselect inside a modal (call on shown.bs.modal).
     */
    function initModalTomSelects(modalEl, options) {
        var modal = resolveElement(modalEl);
        if (!modal || typeof TomSelect === 'undefined') {
            return [];
        }

        var dropdownParent = resolveModalDropdownParent(modal);
        var base = Object.assign({ width: '100%', dropdownParent: dropdownParent }, options || {});
        var instances = [];

        modal.querySelectorAll('select.tomselect').forEach(function (element) {
            if (element.tomselect) {
                return;
            }
            var opts = Object.assign({}, base);
            if (element.multiple) {
                opts.plugins = ensurePlugin(opts.plugins, 'remove_button');
                if (opts.closeAfterSelect === undefined) {
                    opts.closeAfterSelect = false;
                }
            }
            var instance = initTomSelect(element, opts);
            if (instance) {
                instances.push(instance);
            }
        });

        return instances;
    }

    function setEnhancedSelectValue(el, value, silent) {
        var element = resolveElement(el);
        if (!element) {
            return;
        }
        if (element.tomselect) {
            if (element.multiple && Array.isArray(value)) {
                element.tomselect.setValue(value, silent !== false);
            } else {
                element.tomselect.setValue(value, silent !== false);
            }
            return;
        }
        if (window.jQuery) {
            window.jQuery(element).val(value).trigger('change');
        }
    }

    function waitForTomSelect(maxAttempts) {
        var limit = maxAttempts || 200;

        return new Promise(function (resolve) {
            var attempts = 0;

            function check() {
                attempts += 1;
                if (typeof TomSelect !== 'undefined') {
                    resolve();
                } else if (attempts >= limit) {
                    console.warn('[waitForTomSelect] Tom Select not loaded after timeout');
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            }

            check();
        });
    }

    window.initTomSelect = initTomSelect;
    window.initTomSelectAll = initTomSelectAll;
    window.destroyTomSelect = destroyTomSelect;
    window.isTomSelect = isTomSelect;
    window.isSelect2 = isSelect2;
    window.getEnhancementWrapper = getEnhancementWrapper;
    window.placeValidationError = placeValidationError;
    window.destroyEnhancedSelect = destroyEnhancedSelect;
    window.reinitTomSelect = reinitTomSelect;
    window.initModalTomSelects = initModalTomSelects;
    window.setEnhancedSelectValue = setEnhancedSelectValue;
    window.waitForTomSelect = waitForTomSelect;

    if (window.jQuery) {
        window.jQuery(document).on('shown.bs.modal', '.modal', function () {
            initModalTomSelects(this);
        });
    }

    window.BansalTomSelect = {
        init: initTomSelect,
        initAll: initTomSelectAll,
        destroy: destroyTomSelect,
        destroyEnhanced: destroyEnhancedSelect,
        reinit: reinitTomSelect,
        initModal: initModalTomSelects,
        setValue: setEnhancedSelectValue,
        isTomSelect: isTomSelect,
        isSelect2: isSelect2,
        getEnhancementWrapper: getEnhancementWrapper,
        placeValidationError: placeValidationError,
        waitForTomSelect: waitForTomSelect
    };
})(window);
