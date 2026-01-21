/**
 * Admin Partner Detail - Promotion Handlers
 *
 * Handles promotion modals, status changes, and product selection UI.
 *
 * Dependencies:
 *   - jQuery
 *   - Select2
 *   - config.js (App object)
 */

'use strict';

(async function() {
    if (typeof window.vendorLibsReady !== 'undefined') {
        await window.vendorLibsReady;
    } else {
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined') {
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
        var v = $('input[name="apply_to"]:checked').val();
        if(v == 'All Products'){
            $('.ifselectproducts').hide();
            $('.productselect2').attr('data-valid', '');
        }else{
            $('.ifselectproducts').show();
            $('.productselect2').attr('data-valid', 'required');
        }
    });

    $('.productselect2').select2({
        placeholder: "Select Product",
        multiple: true,
        width: "100%"
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
                $('.showpromotionedit').html(response);
                $('.productselect2').select2({
                    placeholder: "Select Product",
                    multiple: true,
                    width: "100%"
                });
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
