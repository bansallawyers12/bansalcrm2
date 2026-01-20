/**
 * Admin Partner Detail - Archive Handlers
 *
 * Handles archive/unarchive actions.
 *
 * Dependencies:
 *   - jQuery
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

window.arcivedAction = function(id, table) {
    var conf = confirm('Are you sure, you would like to delete this record. Remember all Related data would be deleted.');
    if(conf){
        if(id == '') {
            alert('Please select ID to delete the record.');
            return false;
        } else {
            $('#popuploader').show();
            $(".server-error").html('');
            $(".custom-error-msg").html('');
            $.ajax({
                type:'post',
                headers: { 'X-CSRF-TOKEN': App.getCsrf() },
                url: App.getUrl('deleteAction') + '/delete_action',
                data:{'id': id, 'table' : table},
                success:function(resp) {
                    $('#popuploader').hide();
                    var obj = $.parseJSON(resp);
                    if(obj.status == 1) {
                        window.location.href = App.getUrl('siteUrl') + '/partners';
                    } else{
                        var html = errorMessage(obj.message);
                        $(".custom-error-msg").html(html);
                    }
                    $("#popuploader").hide();
                },
                beforeSend: function() {
                    $("#popuploader").show();
                }
            });
            $('html, body').animate({scrollTop:0}, 'slow');
        }
    } else{
        $("#loader").hide();
    }
};

})(); // End async wrapper
