/**
 * Add Application modal — workflow → partner → product cascade (Tom Select).
 * Loaded on any page that includes `.add_appliation` modal markup.
 */
(function (window) {
    'use strict';

    function resolveApplicationModal(el) {
        if (!el) {
            return document.querySelector('.add_appliation');
        }
        if (el.classList && el.classList.contains('add_appliation')) {
            return el;
        }
        if (el.closest) {
            return el.closest('.add_appliation');
        }
        return document.querySelector('.add_appliation');
    }

    /** Searchable selects; no dropdownParent — menu stays on .ts-wrapper under the control. */
    function applicationModalTomSelectOpts(modalEl, extra) {
        return Object.assign({
            width: '100%',
            maxOptions: null
        }, extra || {});
    }

    function readApplicationSelectValue(el) {
        if (typeof getEnhancedSelectValue === 'function') {
            return getEnhancedSelectValue(el);
        }
        if (window.jQuery) {
            return window.jQuery(el).val() || '';
        }
        return el && el.value ? el.value : '';
    }

    function reloadApplicationSelectHtml(el, html, modalEl) {
        var element = typeof el === 'string' ? document.querySelector(el) : el;
        if (!element) {
            return null;
        }
        if (typeof reinitTomSelectAfterHtml === 'function') {
            return reinitTomSelectAfterHtml(element, html, applicationModalTomSelectOpts(modalEl || element));
        }
        element.innerHTML = html;
        return null;
    }

    function clearApplicationSelectValue(el, silent) {
        if (typeof clearEnhancedSelectValue === 'function') {
            clearEnhancedSelectValue(el, silent !== false);
            return;
        }
        if (window.jQuery) {
            window.jQuery(el).val('').trigger('change');
        }
    }

    function setApplicationProductDisabled(modalEl, disabled) {
        var modal = resolveApplicationModal(modalEl);
        var productEl = modal ? modal.querySelector('#product') : null;
        if (productEl && productEl.tomselect) {
            if (disabled) {
                productEl.tomselect.disable();
            } else {
                productEl.tomselect.enable();
            }
            return;
        }
        if (productEl) {
            productEl.disabled = !!disabled;
        }
    }

    function destroyApplicationModalSelects(modalEl) {
        var modal = resolveApplicationModal(modalEl);
        if (!modal) {
            return;
        }
        ['#workflow', '#partner', '#product'].forEach(function (selector) {
            var el = modal.querySelector(selector);
            if (el && typeof destroyEnhancedSelect === 'function') {
                destroyEnhancedSelect(el);
            }
        });
    }

    function resetApplicationModalSelects(modalEl) {
        var modal = resolveApplicationModal(modalEl);
        if (!modal) {
            return;
        }
        destroyApplicationModalSelects(modal);
        var partner = modal.querySelector('#partner');
        var product = modal.querySelector('#product');
        var workflow = modal.querySelector('#workflow');
        if (partner) {
            partner.innerHTML = '<option value="">Please Select a Partner & Branch</option>';
            partner.classList.add('tomselect');
        }
        if (product) {
            product.innerHTML = '<option value="">Please Select a Product</option>';
            product.disabled = false;
            product.classList.add('tomselect');
        }
        if (workflow) {
            workflow.value = '';
            workflow.classList.add('tomselect');
        }
    }

    function resolveCascadeUrl(keys, fallbackPath) {
        if (typeof App !== 'undefined' && typeof App.getUrl === 'function') {
            var keyList = Array.isArray(keys) ? keys : [keys];
            for (var i = 0; i < keyList.length; i++) {
                var url = App.getUrl(keyList[i]);
                if (url) {
                    return url;
                }
            }
        }
        if (!fallbackPath) {
            return '';
        }
        var base = '';
        if (typeof site_url !== 'undefined' && site_url) {
            base = String(site_url).replace(/\/+$/, '');
        }
        return base + fallbackPath;
    }

    function bindApplicationModalCascade(getPartnerBranchUrl, getBranchProductUrl) {
        if (!window.jQuery || window.__applicationModalCascadeBound) {
            return;
        }
        window.__applicationModalCascadeBound = true;

        var $ = window.jQuery;
        var partnerUrl = getPartnerBranchUrl || resolveCascadeUrl(['getPartnerBranch', 'getpartnerbranch'], '/getpartnerbranch');
        var productUrl = getBranchProductUrl || resolveCascadeUrl(['getBranchProduct', 'getbranchproduct'], '/getbranchproduct');

        $(document).on('change', '.add_appliation #workflow', function () {
            var modal = resolveApplicationModal(this);
            var v = readApplicationSelectValue(this);
            if (v === '' || !modal) {
                return;
            }
            $('.popuploader').show();
            $.ajax({
                url: partnerUrl,
                type: 'GET',
                data: { cat_id: v },
                success: function (response) {
                    $('.popuploader').hide();
                    reloadApplicationSelectHtml(modal.querySelector('#partner'), response, modal);
                    clearApplicationSelectValue(modal.querySelector('#partner'), true);
                    reloadApplicationSelectHtml(
                        modal.querySelector('#product'),
                        '<option value="">Please Select a Product</option>',
                        modal
                    );
                    clearApplicationSelectValue(modal.querySelector('#product'), true);
                    setApplicationProductDisabled(modal, false);
                },
                error: function () {
                    $('.popuploader').hide();
                }
            });
        });

        $(document).on('change', '.add_appliation #partner', function () {
            var modal = resolveApplicationModal(this);
            var v = readApplicationSelectValue(this);
            var explode = v ? String(v).split('_') : [];
            if (v === '' || !modal) {
                return;
            }
            var productEl = modal.querySelector('#product');
            $('.popuploader').show();
            if (productEl) {
                productEl.setAttribute('data-valid', '');
            }
            setApplicationProductDisabled(modal, true);
            $(modal).find('.product_error').html('');
            $.ajax({
                url: productUrl,
                type: 'GET',
                data: { cat_id: explode[0] },
                success: function (response) {
                    $('.popuploader').hide();
                    reloadApplicationSelectHtml(productEl, response, modal);
                    setApplicationProductDisabled(modal, false);
                    if (productEl) {
                        productEl.setAttribute('data-valid', 'required');
                    }
                    clearApplicationSelectValue(productEl, true);
                },
                error: function () {
                    $('.popuploader').hide();
                    setApplicationProductDisabled(modal, false);
                    if (productEl) {
                        productEl.setAttribute('data-valid', 'required');
                    }
                    reloadApplicationSelectHtml(
                        productEl,
                        '<option value="">Select Product</option>',
                        modal
                    );
                }
            });
        });

        $(document).on('hidden.bs.modal', '.add_appliation', function () {
            resetApplicationModalSelects(this);
        });
    }

    window.ApplicationModalCascade = {
        bind: bindApplicationModalCascade,
        destroySelects: destroyApplicationModalSelects,
        resetSelects: resetApplicationModalSelects,
        clearSelectValue: clearApplicationSelectValue
    };

    if (typeof whenTomSelectReady === 'function') {
        whenTomSelectReady(function () {
            bindApplicationModalCascade();
        });
    } else if (window.jQuery) {
        window.jQuery(function () {
            bindApplicationModalCascade();
        });
    }
})(window);
