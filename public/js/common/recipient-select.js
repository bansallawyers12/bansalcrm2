/**
 * Recipient Select — shared Tom Select AJAX recipient picker (email modals, notes CC, etc.).
 *
 * Phase 3: Tom Select backend via tomselect-init.js (Select2 API surface retained for callers).
 *
 * Usage:
 *   RecipientSelect.init('#emailmodal .js-data-example-ajax', { url: '/clients/get-recipients', dropdownParent: '#emailmodal' });
 *   RecipientSelect.setClientEmailRecipient('#emailmodal .js-data-example-ajax', id, name, email, 'Client', { dropdownParent: '#emailmodal' });
 *   var collected = RecipientSelect.collectFromCheckboxes('.cb-element', 'Client');
 *   RecipientSelect.setData('#emailmodal .js-data-example-ajax', collected.entries, { dropdownParent: '#emailmodal' });
 */
(function (window) {
    'use strict';

    function get$() {
        return window.jQuery;
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
        var $ = get$();
        if ($ && (el instanceof $ || el.jquery !== undefined)) {
            return el.length ? el[0] : null;
        }
        if (el.nodeType === 1) {
            return el;
        }
        return null;
    }

    function resolveUrl(options) {
        options = options || {};
        if (options.url) {
            return options.url;
        }
        if (typeof App !== 'undefined' && typeof App.getUrl === 'function') {
            return App.getUrl('clientGetRecipients')
                || App.getUrl('clientsGetRecipients')
                || App.getUrl('getRecipients')
                || ((App.getUrl('siteUrl') || '') + '/clients/get-recipients');
        }
        if (window.AppConfig && window.AppConfig.urls) {
            if (window.AppConfig.urls.getRecipients) {
                return window.AppConfig.urls.getRecipients;
            }
            if (window.AppConfig.urls.clientsGetRecipients) {
                return window.AppConfig.urls.clientsGetRecipients;
            }
        }
        return '/clients/get-recipients';
    }

    function buildRecipientHtml(name, email, status) {
        name = name || '';
        email = email || '';
        status = status || '';
        return (
            "<div class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +
            "<div class='ag-flex ag-align-start'>" +
            "<div class='ag-flex ag-flex-column col-hr-1'>" +
            "<div class='ag-flex'><span class='select2-result-repository__title text-semi-bold'>" + name + "</span>&nbsp;</div>" +
            "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'>" + email + "</small></div>" +
            "</div></div>" +
            "<div class='ag-flex ag-flex-column ag-align-end'>" +
            "<span class='badge bg-warning text-dark select2-result-repository__statistics'>" + status + "</span>" +
            "</div></div>"
        );
    }

    function buildRecipientEntry(id, name, email, status) {
        name = name || '';
        return {
            id: id,
            text: name,
            name: name,
            email: email || '',
            status: status || '',
            html: buildRecipientHtml(name, email, status),
            title: name
        };
    }

    function formatRepo(repo) {
        if (!repo) {
            return '';
        }
        if (repo.loading) {
            return repo.text;
        }
        if (repo.html) {
            return repo.html;
        }

        var $ = get$();
        if (!$) {
            return repo.name || repo.text || '';
        }

        var $container = $(
            "<div class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +
            "<div class='ag-flex ag-align-start'>" +
            "<div class='ag-flex ag-flex-column col-hr-1'>" +
            "<div class='ag-flex'><span class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
            "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small></div>" +
            "</div></div>" +
            "<div class='ag-flex ag-flex-column ag-align-end'>" +
            "<span class='badge bg-warning text-dark select2-result-repository__statistics'></span>" +
            "</div></div>"
        );

        $container.find('.select2-result-repository__title').text(repo.name || repo.text || '');
        $container.find('.select2-result-repository__description').text(repo.email || '');
        $container.find('.select2-result-repository__statistics').text(repo.status || '');

        return $container;
    }

    function formatRepoSelection(repo) {
        return repo.name || repo.text || '';
    }

    function isEnhanced(element) {
        if (!element) {
            return false;
        }
        if (typeof window.isTomSelect === 'function' && window.isTomSelect(element)) {
            return true;
        }
        if (typeof window.isSelect2 === 'function' && window.isSelect2(element)) {
            return true;
        }
        return false;
    }

    function destroyRecipientSelect(el) {
        if (typeof window.destroyEnhancedSelect === 'function') {
            window.destroyEnhancedSelect(el);
            return;
        }
        var element = resolveElement(el);
        if (!element) {
            return;
        }
        if (typeof window.destroyTomSelect === 'function') {
            window.destroyTomSelect(element);
        }
        var $ = get$();
        if ($ && element && $(element).data('select2')) {
            $(element).select2('destroy');
        }
    }

    function buildAjaxOptions(url, options) {
        var ajax = {
            url: url,
            dataType: 'json',
            processResults: function (data) {
                return { results: (data && data.items) ? data.items : [] };
            },
            cache: true
        };

        if (options.csrf && typeof App !== 'undefined' && typeof App.getCsrf === 'function') {
            ajax.headers = { 'X-CSRF-TOKEN': App.getCsrf() };
        }

        if (options.minimumInputLength) {
            ajax.delay = options.delay || 250;
            ajax.data = function (params) {
                return { q: params.term, page: params.page || 1 };
            };
        }

        return ajax;
    }

    function ensureMultipleAttribute(element, isMultiple) {
        if (!isMultiple || !element) {
            return;
        }
        if (!element.hasAttribute('multiple')) {
            element.setAttribute('multiple', 'multiple');
        }
    }

    function buildInitOptions(url, options) {
        options = options || {};
        var isMultiple = options.multiple !== false;

        var initOpts = {
            multiple: isMultiple,
            closeOnSelect: false,
            dropdownParent: options.dropdownParent,
            width: '100%',
            ajax: buildAjaxOptions(url, options),
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        };

        if (options.minimumInputLength) {
            initOpts.minimumInputLength = options.minimumInputLength;
        }

        if (options.placeholder) {
            initOpts.placeholder = options.placeholder;
        }

        return initOpts;
    }

    function buildStaticOptions(options) {
        options = options || {};
        var isMultiple = options.multiple !== false;

        return {
            multiple: isMultiple,
            closeOnSelect: false,
            dropdownParent: options.dropdownParent,
            width: '100%',
            escapeMarkup: function (markup) {
                return markup;
            },
            templateResult: function (data) {
                return data.html;
            },
            templateSelection: function (data) {
                return data.text;
            }
        };
    }

    function collectRelatedFileEntries() {
        var entries = [];

        if (window.PageConfig && Array.isArray(window.PageConfig.relatedFilesData) && window.PageConfig.relatedFilesData.length) {
            window.PageConfig.relatedFilesData.forEach(function (file) {
                entries.push(buildRecipientEntry(file.id, file.name, file.email, file.status || 'Client'));
            });
            return entries;
        }

        if (typeof App !== 'undefined' && typeof App.getPageConfig === 'function') {
            var pageData = App.getPageConfig('relatedFilesData');
            if (pageData && pageData.length) {
                pageData.forEach(function (file) {
                    entries.push(buildRecipientEntry(file.id, file.name, file.email, file.status || 'Client'));
                });
                return entries;
            }
        }

        var $ = get$();
        if ($) {
            $('.relatedfile').each(function () {
                var $item = $(this);
                var id = $item.data('id');
                if (id) {
                    entries.push(buildRecipientEntry(
                        id,
                        $item.data('name') || '',
                        $item.data('email') || '',
                        'Client'
                    ));
                }
            });
        }

        return entries;
    }

    /**
     * Related Files field on client/leads create & edit — AJAX search with optional preloaded selections.
     */
    function initRelatedFiles(options) {
        options = options || {};
        var selector = options.selector || 'select[name="related_files[]"]';
        var element = resolveElement(selector);

        if (!element) {
            return null;
        }

        if (typeof window.initTomSelect !== 'function') {
            console.warn('[RecipientSelect] initTomSelect not available for related files');
            return null;
        }

        if (options.force || options.reinit) {
            destroyRecipientSelect(element);
        } else if (isEnhanced(element)) {
            return element.tomselect || null;
        }

        var url = resolveUrl(options);
        var isMultiple = options.multiple !== false;
        ensureMultipleAttribute(element, isMultiple);

        var entries = options.entries || collectRelatedFileEntries();
        var initOpts = buildInitOptions(url, Object.assign({ minimumInputLength: 1 }, options));

        if (entries.length) {
            initOpts.data = entries;
        }

        var instance = window.initTomSelect(element, initOpts);

        if (instance && entries.length) {
            var ids = entries.map(function (entry) {
                return String(entry.id);
            });
            if (typeof window.setEnhancedSelectValue === 'function') {
                window.setEnhancedSelectValue(element, ids, true);
            } else {
                instance.setValue(ids, true);
            }
        }

        return instance;
    }

    function ensureRelatedFiles(options, maxAttempts) {
        var limit = maxAttempts || 50;
        var attempts = 0;
        var selector = (options && options.selector) || 'select[name="related_files[]"]';

        function check() {
            attempts += 1;
            var element = resolveElement(selector);
            if (!element) {
                return;
            }
            if (typeof TomSelect === 'undefined' || typeof window.initTomSelect !== 'function') {
                if (attempts < limit) {
                    setTimeout(check, 50);
                }
                return;
            }
            if (!isEnhanced(element)) {
                initRelatedFiles(options);
            }
        }

        check();
    }

    function initRecipientSelect(el, options) {
        options = options || {};

        if (typeof TomSelect === 'undefined' || typeof window.initTomSelect !== 'function') {
            console.warn('[RecipientSelect] Tom Select not available');
            return null;
        }

        var element = resolveElement(el);
        if (!element) {
            return null;
        }

        if (options.force || options.reinit) {
            destroyRecipientSelect(element);
        } else if (isEnhanced(element)) {
            return element.tomselect || null;
        }

        var isMultiple = options.multiple !== false;
        ensureMultipleAttribute(element, isMultiple);

        return window.initTomSelect(element, buildInitOptions(resolveUrl(options), options));
    }

    function initRecipientSelects(selector, options) {
        var instances = [];
        document.querySelectorAll(selector).forEach(function (element) {
            var instance = initRecipientSelect(element, options);
            if (instance) {
                instances.push(instance);
            }
        });
        return instances;
    }

    function reinitRecipientSelect(el, options) {
        if (typeof window.reinitTomSelect === 'function') {
            var element = resolveElement(el);
            if (!element) {
                return null;
            }
            ensureMultipleAttribute(element, (options && options.multiple !== false));
            return window.reinitTomSelect(element, buildInitOptions(resolveUrl(options || {}), options || {}));
        }
        options = options || {};
        options.reinit = true;
        return initRecipientSelect(el, options);
    }

    function setRecipientSelectData(el, entries, options) {
        options = options || {};

        if (typeof TomSelect === 'undefined' || typeof window.initTomSelect !== 'function') {
            return;
        }

        var element = resolveElement(el);
        if (!element) {
            return;
        }

        var ids = (entries || []).map(function (e) {
            return String(e.id);
        });
        var isMultiple = options.multiple !== false;
        ensureMultipleAttribute(element, isMultiple);

        var initOpts = buildStaticOptions(options);
        initOpts.data = entries || [];

        var instance = typeof window.reinitTomSelect === 'function'
            ? window.reinitTomSelect(element, initOpts)
            : (function () {
                destroyRecipientSelect(element);
                return window.initTomSelect(element, initOpts);
            }());

        if (!instance) {
            return;
        }

        if (typeof window.setEnhancedSelectValue === 'function') {
            window.setEnhancedSelectValue(element, ids, true);
        } else if (element.tomselect) {
            element.tomselect.setValue(ids, true);
        }
    }

    function setClientEmailRecipient(el, id, name, email, status, options) {
        setRecipientSelectData(el, [buildRecipientEntry(id, name, email, status || 'Client')], options);
    }

    function getRecipientSelectValue(el) {
        var element = resolveElement(el);
        if (!element) {
            return null;
        }
        if (element.tomselect) {
            var value = element.tomselect.getValue();
            if (Array.isArray(value)) {
                return value;
            }
            return value ? [value] : [];
        }
        var $ = get$();
        if ($) {
            var nativeVal = $(element).val();
            if (Array.isArray(nativeVal)) {
                return nativeVal;
            }
            return nativeVal ? [nativeVal] : [];
        }
        return null;
    }

    function collectFromCheckboxes(checkboxSelector, statusLabel) {
        var entries = [];
        var $ = get$();
        if (!$) {
            return { ids: [], entries: [] };
        }

        $(checkboxSelector + ':checked').each(function () {
            var id = $(this).attr('data-id');
            var email = $(this).attr('data-email');
            var name = $(this).attr('data-name');
            entries.push(buildRecipientEntry(id, name, email, statusLabel || 'Client'));
        });

        return {
            ids: entries.map(function (e) {
                return e.id;
            }),
            entries: entries
        };
    }

    // Only fill globals when no page-level definition is already present.
    // recipient-select.js is deferred, so it runs after inline scripts.
    if (!window.formatRepo) {
        window.formatRepo = formatRepo;
    }
    if (!window.formatRepoSelection) {
        window.formatRepoSelection = formatRepoSelection;
    }

    var api = {
        resolveUrl: resolveUrl,
        buildHtml: buildRecipientHtml,
        buildEntry: buildRecipientEntry,
        formatRepo: formatRepo,
        formatRepoSelection: formatRepoSelection,
        destroy: destroyRecipientSelect,
        init: initRecipientSelect,
        initAll: initRecipientSelects,
        reinit: reinitRecipientSelect,
        setData: setRecipientSelectData,
        setClientEmailRecipient: setClientEmailRecipient,
        getValue: getRecipientSelectValue,
        collectFromCheckboxes: collectFromCheckboxes,
        collectRelatedFileEntries: collectRelatedFileEntries,
        initRelatedFiles: initRelatedFiles,
        ensureRelatedFiles: ensureRelatedFiles
    };

    function waitForRecipientSelect(maxAttempts) {
        var limit = maxAttempts || 200;

        return new Promise(function (resolve) {
            var attempts = 0;

            function check() {
                attempts += 1;
                if (typeof window.RecipientSelect !== 'undefined') {
                    resolve(window.RecipientSelect);
                } else if (attempts >= limit) {
                    console.warn('[waitForRecipientSelect] RecipientSelect not loaded after timeout');
                    resolve(null);
                } else {
                    setTimeout(check, 50);
                }
            }

            check();
        });
    }

    window.waitForRecipientSelect = waitForRecipientSelect;
    window.RecipientSelect = api;
    window.BansalRecipientSelect = api;
})(window);
