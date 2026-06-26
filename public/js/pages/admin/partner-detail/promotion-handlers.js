/**
 * Admin Partner Detail - Promotion Handlers
 *
 * Handles promotion modals, status changes, and product selection UI.
 *
 * Dependencies:
 *   - jQuery
 *   - Tom Select (tomselect-init.js)
 *   - config.js (App object)
 */

'use strict';

(function initPromotionProductSelectsHelper() {
    function promotionProductDropdownParent(el) {
        if (!el) {
            return null;
        }
        var modal = el.closest ? el.closest('.modal-content') || el.closest('.modal') : null;
        return modal || document.body;
    }

    function initPromotionProductSelects(context) {
        if (typeof initTomSelect !== 'function') {
            return [];
        }

        var root = context;
        if (typeof context === 'string') {
            root = document.querySelector(context);
        }
        if (!root) {
            root = document;
        }

        var instances = [];
        var nodes = root.querySelectorAll
            ? root.querySelectorAll('.promotion-product-select')
            : [];

        nodes.forEach(function (element) {
            if (element.tomselect) {
                return;
            }
            if (!element.hasAttribute('multiple')) {
                element.setAttribute('multiple', 'multiple');
            }

            var instance;
            if (typeof initTomSelectPreserveValue === 'function') {
                instance = initTomSelectPreserveValue(element, {
                    width: '100%',
                    multiple: true,
                    closeOnSelect: false,
                    dropdownParent: promotionProductDropdownParent(element)
                });
            } else {
                instance = initTomSelect(element, {
                    width: '100%',
                    multiple: true,
                    closeOnSelect: false,
                    dropdownParent: promotionProductDropdownParent(element)
                });
            }

            if (instance) {
                instances.push(instance);
            }
        });

        return instances;
    }

    window.initPromotionProductSelects = initPromotionProductSelects;
})();

(async function() {
    if (typeof window.vendorLibsReady !== 'undefined') {
        await window.vendorLibsReady;
    } else {
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined' && typeof initTomSelect === 'function') {
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

jQuery(document).ready(function($){
    const partnerId = PageConfig.partnerId;

    $(document).delegate('input[name="apply_to"]', 'change', function () {
        var $form = $(this).closest('form');
        var v = $(this).val();
        if (v == 'All Products') {
            $form.find('.ifselectproducts').hide();
            $form.find('.promotion-product-select').attr('data-valid', '');
        } else {
            $form.find('.ifselectproducts').show();
            $form.find('.promotion-product-select').attr('data-valid', 'required');
            if (typeof initPromotionProductSelects === 'function') {
                var modalRoot = $form.closest('.modal')[0] || $form[0];
                initPromotionProductSelects(modalRoot);
            }
        }
    });

    $('#create_promotion').on('hidden.bs.modal', function () {
        var form = this.querySelector('#promotionform');
        if (form) {
            form.reset();
        }
        this.querySelectorAll('.promotion-product-select').forEach(function (el) {
            if (typeof destroyEnhancedSelect === 'function') {
                destroyEnhancedSelect(el);
            }
        });
        $(this).find('.ifselectproducts').hide();
        $(this).find('#all_product').prop('checked', true);
    });

    $('#create_promotion').on('shown.bs.modal', function () {
        if (typeof initPromotionProductSelects === 'function') {
            initPromotionProductSelects(this);
        }
    });

    $(document).delegate('.add_promotion', 'click', function(){
        $('#create_promotion').modal('show');
    });

    $(document).delegate('.changepromotonstatus', 'change', function(){
        $('.popuploader').show();
        var appliid = $(this).attr('data-id');
        var dstatus = $(this).attr('data-status');
        $.ajax({
            url: App.getUrl('changePromotionStatus'),
            type:'GET',
            data:{id:appliid, status:dstatus},
            success:function(response){
                $('.popuploader').hide();
                var obj = $.parseJSON(response);
                if(obj.status){
                    $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
                    var updated_status = dstatus == 1 ? 0 : 1;
                    $(".changepromotonstatus[data-id="+appliid+"]").attr('data-status', updated_status);
                    $.ajax({
                        url: App.getUrl('getPromotions'),
                        type:'GET',
                        data:{clientid: partnerId},
                        success: function(responses){
                            $('.promotionlists').html(responses);
                        }
                    });
                }else{
                    $('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                    if(dstatus == 1){
                        $(".changepromotonstatus[data-id="+appliid+"]").prop('checked', true);
                    } else {
                        $(".changepromotonstatus[data-id="+appliid+"]").prop('checked', false);
                    }
                }
            }
        });
    });

    $(document).delegate('.openpromotonform', 'click', function(){
        var appliid = $(this).attr('data-id');
        $('#edit_promotion').modal('show');
        $('.popuploader').show();
        $.ajax({
            url: App.getUrl('getPromotionEditForm'),
            type:'GET',
            data:{id:appliid},
            success:function(response){
                $('.popuploader').hide();
                $('.showpromotionedit .promotion-product-select').each(function () {
                    if (typeof destroyEnhancedSelect === 'function') {
                        destroyEnhancedSelect(this);
                    }
                });
                $('.showpromotionedit').html(response);
                if (typeof initPromotionProductSelects === 'function') {
                    initPromotionProductSelects('.showpromotionedit');
                }
            }
        });
    });

    $(document).delegate('.opencommissioninvoice', 'click', function(){
        $('#opencommissionmodal').modal('show');
    });

    $(document).delegate('.opengeneralinvoice', 'click', function(){
        $('#opengeneralinvoice').modal('show');
    });

    $('#attachments').on('change',function(){
        $('.showattachment').html('');
        var filename = $(this).val().replace(/.*(\/|\\)/, '');
        $('.showattachment').html(filename);
    });
});

})(); // End async wrapper
