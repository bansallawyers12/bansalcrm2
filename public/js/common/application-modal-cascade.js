/**
 * Add Application modal — workflow → partner → product cascade (Tom Select).
 * Loaded on any page that includes `.add_appliation` modal markup.
 */
(function (window) {
    'use strict';

    function applicationModalDropdownParent() {
        var modal = document.querySelector('.add_appliation .modal-content') ||
            document.querySelector('.add_appliation');
        return modal || document.body;
    }

    function applicationModalTomSelectOpts(extra) {
        return Object.assign({
            width: '100%',
            dropdownParent: applicationModalDropdownParent()
        }, extra || {});
    }

    function readApplicationSelectValue(selector) {
        if (typeof getEnhancedSelectValue === 'function') {
            return getEnhancedSelectValue(selector);
        }
        if (window.jQuery) {
            return window.jQuery(selector).val() || '';
        }
        return '';
    }

    function reloadApplicationSelectHtml(selector, html) {
        if (typeof reinitTomSelectAfterHtml === 'function') {
            return reinitTomSelectAfterHtml(selector, html, applicationModalTomSelectOpts());
        }
        if (window.jQuery) {
            window.jQuery(selector).html(html);
        }
        return null;
    }

    function clearApplicationSelectValue(selector) {
        if (typeof clearEnhancedSelectValue === 'function') {
            clearEnhancedSelectValue(selector, true);
            return;
        }
        if (window.jQuery) {
            window.jQuery(selector).val('').trigger('change');
        }
    }

    function setApplicationProductDisabled(disabled) {
        var productEl = document.querySelector('.add_appliation #product');
        if (productEl && productEl.tomselect) {
            if (disabled) {
                productEl.tomselect.disable();
            } else {
                productEl.tomselect.enable();
            }
            return;
        }
        if (window.jQuery) {
            window.jQuery('.add_appliation #product').prop('disabled', disabled);
        }
    }

    function destroyApplicationModalSelects() {
        ['#workflow', '#partner', '#product'].forEach(function (selector) {
            if (typeof destroyEnhancedSelect === 'function') {
                destroyEnhancedSelect('.add_appliation ' + selector);
            }
        });
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
        return fallbackPath || '';
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
            var v = readApplicationSelectValue('.add_appliation #workflow');
            if (v === '') {
                return;
            }
            $('.popuploader').show();
            $.ajax({
                url: partnerUrl,
                type: 'GET',
                data: { cat_id: v },
                success: function (response) {
                    $('.popuploader').hide();
                    reloadApplicationSelectHtml('.add_appliation #partner', response);
                    clearApplicationSelectValue('.add_appliation #partner');
                    reloadApplicationSelectHtml('.add_appliation #product', '<option value="">Please Select a Product</option>');
                    clearApplicationSelectValue('.add_appliation #product');
                    setApplicationProductDisabled(false);
                }
            });
        });

        $(document).on('change', '.add_appliation #partner', function () {
            var v = readApplicationSelectValue('.add_appliation #partner');
            var explode = v ? String(v).split('_') : [];
            if (v === '') {
                return;
            }
            $('.popuploader').show();
            $('.add_appliation #product').attr('data-valid', '');
            setApplicationProductDisabled(true);
            $('.add_appliation .product_error').html('');
            $.ajax({
                url: productUrl,
                type: 'GET',
                data: { cat_id: explode[0] },
                success: function (response) {
                    $('.popuploader').hide();
                    reloadApplicationSelectHtml('.add_appliation #product', response);
                    setApplicationProductDisabled(false);
                    $('.add_appliation #product').attr('data-valid', 'required');
                    clearApplicationSelectValue('.add_appliation #product');
                },
                error: function () {
                    $('.popuploader').hide();
                    setApplicationProductDisabled(false);
                    $('.add_appliation #product').attr('data-valid', 'required');
                    reloadApplicationSelectHtml('.add_appliation #product', '<option value="">Select Product</option>');
                }
            });
        });

        $('.add_appliation').on('hidden.bs.modal', function () {
            destroyApplicationModalSelects();
        });
    }

    window.ApplicationModalCascade = {
        bind: bindApplicationModalCascade,
        destroySelects: destroyApplicationModalSelects,
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
