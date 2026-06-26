/**
 * Recipient Select — shared Select2 AJAX recipient picker (email modals, notes CC, etc.).
 *
 * Phase 2.5 consolidation; Phase 3 will swap internals to Tom Select via tomselect-init.js.
 *
 * Usage:
 *   RecipientSelect.init('#emailmodal .js-data-example-ajax', { url: '/clients/get-recipients', dropdownParent: '#emailmodal' });
 *   RecipientSelect.setClientEmailRecipient('#emailmodal .js-data-example-ajax', id, name, email, 'Client', { dropdownParent: '#emailmodal' });
 *   var collected = RecipientSelect.collectFromCheckboxes('.cb-element', 'Client');
 *   RecipientSelect.setRecipientSelectData('#emailmodal .js-data-example-ajax', collected.entries, { dropdownParent: '#emailmodal' });
 */
(function (window) {
    'use strict';

    var $ = window.jQuery;

    function resolveUrl(options) {
        options = options || {};
        if (options.url) {
            return options.url;
        }
        if (typeof App !== 'undefined' && typeof App.getUrl === 'function') {
            return App.getUrl('clientGetRecipients')
                || App.getUrl('clientsGetRecipients')
                || App.getUrl('getRecipients')
                || (App.getUrl('siteUrl') || '') + '/clients/get-recipients';
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
            "<span class='ui label yellow select2-result-repository__statistics'>" + status + "</span>" +
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
            "<span class='ui label yellow select2-result-repository__statistics'></span>" +
            "</div></div>"
        );

        $container.find('.select2-result-repository__title').text(repo.name || repo.text || '');
        $container.find('.select2-result-repository__description').text(repo.email || '');
        $container.find('.select2-result-repository__statistics').append(repo.status || '');

        return $container;
    }

    function formatRepoSelection(repo) {
        return repo.name || repo.text || '';
    }

    function resolveJQuery(el) {
        if (!el) {
            return $();
        }
        if ($ && el instanceof $) {
            return el;
        }
        return $(el);
    }

    function destroyRecipientSelect(el) {
        if (!$) {
            return;
        }
        var $el = resolveJQuery(el);
        if ($el.data('select2')) {
            $el.select2('destroy');
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

    function initRecipientSelect(el, options) {
        options = options || {};

        if (!$ || typeof $.fn.select2 !== 'function') {
            console.warn('[RecipientSelect] Select2 not available');
            return;
        }

        var $el = resolveJQuery(el);
        if (!$el.length) {
            return;
        }

        if ($el.data('select2')) {
            return;
        }

        var url = resolveUrl(options);
        var dropdownParent = options.dropdownParent;
        if (dropdownParent) {
            dropdownParent = resolveJQuery(dropdownParent);
        }

        var isMultiple = options.multiple !== false;
        var select2Options = {
            multiple: isMultiple,
            closeOnSelect: options.closeOnSelect === false ? false : (isMultiple ? false : true),
            dropdownParent: dropdownParent,
            ajax: buildAjaxOptions(url, options),
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        };

        if (options.minimumInputLength) {
            select2Options.minimumInputLength = options.minimumInputLength;
        }

        $el.select2(select2Options);
    }

    function initRecipientSelects(selector, options) {
        if (!$) {
            return;
        }
        $(selector).each(function () {
            initRecipientSelect(this, options);
        });
    }

    function setRecipientSelectData(el, entries, options) {
        options = options || {};

        if (!$ || typeof $.fn.select2 !== 'function') {
            return;
        }

        var $el = resolveJQuery(el);
        if (!$el.length) {
            return;
        }

        destroyRecipientSelect($el);

        var ids = (entries || []).map(function (e) {
            return String(e.id);
        });
        var dropdownParent = options.dropdownParent;
        if (dropdownParent) {
            dropdownParent = resolveJQuery(dropdownParent);
        }

        var isMultiple = options.multiple !== false;

        $el.select2({
            multiple: isMultiple,
            closeOnSelect: options.closeOnSelect === false ? false : false,
            dropdownParent: dropdownParent,
            data: entries || [],
            escapeMarkup: function (markup) {
                return markup;
            },
            templateResult: function (data) {
                return data.html;
            },
            templateSelection: function (data) {
                return data.text;
            }
        });

        $el.val(ids).trigger('change');
    }

    function setClientEmailRecipient(el, id, name, email, status, options) {
        setRecipientSelectData(el, [buildRecipientEntry(id, name, email, status || 'Client')], options);
    }

    function collectFromCheckboxes(checkboxSelector, statusLabel) {
        var entries = [];
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

    window.formatRepo = formatRepo;
    window.formatRepoSelection = formatRepoSelection;

    var api = {
        resolveUrl: resolveUrl,
        buildHtml: buildRecipientHtml,
        buildEntry: buildRecipientEntry,
        formatRepo: formatRepo,
        formatRepoSelection: formatRepoSelection,
        destroy: destroyRecipientSelect,
        init: initRecipientSelect,
        initAll: initRecipientSelects,
        setData: setRecipientSelectData,
        setClientEmailRecipient: setClientEmailRecipient,
        collectFromCheckboxes: collectFromCheckboxes
    };

    window.RecipientSelect = api;
    window.BansalRecipientSelect = api;
})(window);
