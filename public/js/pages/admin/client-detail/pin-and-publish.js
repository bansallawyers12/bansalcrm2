/**
 * Admin Client Detail - Pin and publish handlers
 */
'use strict';

(function() {
    jQuery(document).ready(function($){
        var appcid = '';
        $(document).on('click', '.publishdoc', function(){
            $('#confirmpublishdocModal').modal('show');
            appcid = $(this).attr('data-id');
        });

        // ============================================================================
        // PIN HANDLERS
        // ============================================================================
        $(document).on('click', '.pinnote', function(){
            $('.popuploader').show();
            var url = App.getUrl('pinNote') || App.getUrl('siteUrl') + '/pinnote';
            $.ajax({
                url: url + '/',
                type:'GET',
                datatype:'json',
                data:{note_id:$(this).attr('data-id')},
                success:function(response){
                    if(typeof getallnotes === 'function') {
                        getallnotes();
                    }
                }
            });
        });

        $(document).on('click', '.pinactivitylog', function(){
            $('.popuploader').show();
            var url = App.getUrl('pinActivityLog') || App.getUrl('siteUrl') + '/pinactivitylog';
            $.ajax({
                url: url + '/',
                type:'GET',
                datatype:'json',
                data:{activity_id:$(this).attr('data-id')},
                success:function(response){
                    if(typeof getallactivities === 'function') {
                        getallactivities();
                    }
                }
            });
        });

        // ============================================================================
        // PUBLISH DOCUMENT HANDLER
        // ============================================================================
        $(document).on('click', '#confirmpublishdocModal .acceptpublishdoc', function(){
            $('.popuploader').show();
            var baseUrl = App.getUrl('siteUrl') || '';
            $.ajax({
                url: baseUrl + '/application/publishdoc',
                type:'GET',
                datatype:'json',
                data:{appid:appcid,status:'1'},
                success:function(response){
                    $('.popuploader').hide();
                    var res = typeof response === 'string' ? JSON.parse(response) : response;
                    $('#confirmpublishdocModal').modal('hide');
                    if(res.status){
                        $('.mychecklistdocdata').html(res.doclistdata);
                    }else{
                        alert(res.message);
                    }
                }
            });
        });
    });
})();
