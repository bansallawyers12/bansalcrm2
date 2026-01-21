/**
 * Admin Partner Detail - Service and Document Handlers
 *
 * Handles interested services, application creation, and document uploads.
 *
 * Dependencies:
 *   - jQuery
 *   - Flatpickr
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
    const siteUrl = App.getUrl('siteUrl') || AppConfig.siteUrl || '';

    // Upload partner document
    $(document).delegate('.upload_document .btn-primary', 'click', function(e) {
        e.preventDefault();
        $(this).closest('.upload_document').find('input[name=document_upload]').click();
    });

    $(document).delegate('input[name=document_upload]', 'click', function() {
        $(this).attr("value", "");
    });

    $(document).delegate('input[name=document_upload]', 'change', function() {
        $('.popuploader').show();
        var formData = new FormData($(this).closest('form')[0]);
        $.ajax({
            url: App.getUrl('partnersUploadPartnerDocument') || (siteUrl + '/upload-partner-document-upload'),
            type:'POST',
            datatype:'json',
            data: formData,
            contentType: false,
            processData: false,
            success: function(responses){
                $('.popuploader').hide();
                var ress = JSON.parse(responses);
                if(ress.status){
                    $('.custom-error-msg').html('<span class="alert alert-success">'+ress.message+'</span>');
                    $('.documnetlist').html(ress.data);
                    $('.griddata').html(ress.griddata);
                }else{
                    $('.custom-error-msg').html('<span class="alert alert-danger">'+ress.message+'</span>');
                }
                if (typeof window.getallactivities === 'function') {
                    window.getallactivities();
                }
            }
        });
    });

    // Interested service workflow changes
    $(document).delegate('#intrested_workflow', 'change', function(){
        var v = $('#intrested_workflow option:selected').val();
        if(v != ''){
            $('.popuploader').show();
            $.ajax({
                url: App.getUrl('getPartner'),
                type:'GET',
                data:{cat_id:v},
                success:function(response){
                    $('.popuploader').hide();
                    $('#intrested_partner').html(response);
                    $("#intrested_partner").val('').trigger('change');
                    $("#intrested_product").val('').trigger('change');
                    $("#intrested_branch").val('').trigger('change');
                }
            });
        }
    });

    $(document).delegate('#intrested_partner','change', function(){
        var v = $('#intrested_partner option:selected').val();
        if(v != ''){
            $('.popuploader').show();
            $.ajax({
                url: App.getUrl('getProduct'),
                type:'GET',
                data:{cat_id:v},
                success:function(response){
                    $('.popuploader').hide();
                    $('#intrested_product').html(response);
                    $("#intrested_product").val('').trigger('change');
                    $("#intrested_branch").val('').trigger('change');
                }
            });
        }
    });

    $(document).delegate('#intrested_product','change', function(){
        var v = $('#intrested_product option:selected').val();
        if(v != ''){
            $('.popuploader').show();
            $.ajax({
                url: App.getUrl('getBranch'),
                type:'GET',
                data:{cat_id:v},
                success:function(response){
                    $('.popuploader').hide();
                    $('#intrested_branch').html(response);
                    $("#intrested_branch").val('').trigger('change');
                }
            });
        }
    });

    // Convert to application
    $(document).delegate('.converttoapplication','click', function(){
        var v = $(this).attr('data-id');
        if(v != ''){
            $('.popuploader').show();
            $.ajax({
                url: App.getUrl('convertApplication'),
                type:'GET',
                data:{cat_id:v,clientid: partnerId},
                success:function(response){
                    var res = typeof response === 'string' ? JSON.parse(response) : response;
                    if(!res || res.status !== true){
                        $('.popuploader').hide();
                        alert((res && res.message) ? res.message : 'Failed to create application. Please try again.');
                        return;
                    }
                    $.ajax({
                        url: App.getUrl('getServices') || (siteUrl + '/get-services'),
                        type:'GET',
                        data:{clientid: partnerId},
                        success: function(responses){
                            $('.interest_serv_list').html(responses);
                            $('.popuploader').hide();
                            if (typeof window.getallactivities === 'function') {
                                window.getallactivities();
                            }
                        },
                        error: function(){
                            $('.popuploader').hide();
                            alert('Application created, but failed to refresh services. Please refresh the page.');
                            if (typeof window.getallactivities === 'function') {
                                window.getallactivities();
                            }
                        }
                    });
                }
            });
        }
    });

    // Interested service view/edit
    $(document).delegate('.openinterestedservice', 'click', function(){
        var v = $(this).attr('data-id');
        $('.popuploader').show();
        $('#interest_service_view').modal('show');
        $.ajax({
            url: App.getUrl('getInterestedService'),
            type:'GET',
            data:{id:v},
            success:function(response){
                $('.popuploader').hide();
                $('.showinterestedservice').html(response);
            }
        });
    });

    $(document).delegate('.openeditservices', 'click', function(){
        var v = $(this).attr('data-id');
        $('.popuploader').show();
        $('#interest_service_view').modal('hide');
        $('#eidt_interested_service').modal('show');
        $.ajax({
            url: App.getUrl('getInterestedServiceEdit'),
            type:'GET',
            data:{id:v},
            success:function(response){
                $('.popuploader').hide();
                $('.showinterestedserviceedit').html(response);
                if (typeof flatpickr !== 'undefined') {
                    flatpickr(".datepicker", {
                        dateFormat: "Y-m-d",
                        allowInput: true
                    });
                }
            }
        });
    });

    // Application workflow/partner/product for add application modal
    $(document).delegate('.add_appliation #workflow', 'change', function(){
        var v = $('.add_appliation #workflow option:selected').val();
        if(v != ''){
            $('.popuploader').show();
            $.ajax({
                url: App.getUrl('getpartnerbranch'),
                type:'GET',
                data:{cat_id:v},
                success:function(response){
                    $('.popuploader').hide();
                    $('.add_appliation #partner').html(response);
                    $(".add_appliation #partner").val('').trigger('change');
                    $(".add_appliation #product").val('').trigger('change');
                    $(".add_appliation #branch").val('').trigger('change');
                }
            });
        }
    });

    $(document).delegate('.add_appliation #partner','change', function(){
        var v = $('.add_appliation #partner option:selected').val();
        var explode = v.split('_');
        if(v != ''){
            $('.popuploader').show();
            $.ajax({
                url: App.getUrl('getbranchproduct'),
                type:'GET',
                data:{cat_id:explode[0]},
                success:function(response){
                    $('.popuploader').hide();
                    $('.add_appliation #product').html(response);
                    $(".add_appliation #product").val('').trigger('change');
                }
            });
        }
    });
});

})(); // End async wrapper
