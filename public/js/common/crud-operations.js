/**
 * CRUD Operations Module
 * 
 * Common CRUD functions used across multiple pages:
 * - arcivedAction: Archive/Unarchive records
 * - deleteAction: Delete records
 * 
 * Usage:
 *   arcivedAction(id, table)
 *   deleteAction(id, table)
 */

'use strict';

function runCrudIfConfirmed(message, fn) {
    if (typeof window.crmConfirm === 'function') {
        window.crmConfirm(message).then(function (ok) {
            if (ok) {
                fn();
            } else {
                $("#loader").hide();
            }
        });
        return;
    }
    if (confirm(message)) {
        fn();
    } else {
        $("#loader").hide();
    }
}

/**
 * Archive/Unarchive action
 * @param {number|string} id - Record ID
 * @param {string} table - Table name
 */
function arcivedAction(id, table) {
    runCrudIfConfirmed('Are you sure, you would like to delete this record. Remember all Related data would be deleted.', function () {
        if (id == '' || id === null || id === undefined) {
            toastMsg('Please select ID to delete the record.', 'warning');
            return false;
        }

        $('#popuploader').show();
        $(".server-error").html('');
        $(".custom-error-msg").html('');

        var deleteUrl = App.getUrl('deleteAction') || App.getUrl('siteUrl') + '/delete_action';

        $.ajax({
            type: 'post',
            headers: { 'X-CSRF-TOKEN': App.getCsrf() },
            url: deleteUrl,
            data: { 'id': id, 'table': table },
            success: function(resp) {
                $('#popuploader').hide();
                var obj = typeof resp === 'string' ? $.parseJSON(resp) : resp;

                if (obj.status == 1) {
                    location.reload();
                } else {
                    var html = errorMessage(obj.message);
                    $(".custom-error-msg").html(html);
                }
                $("#popuploader").hide();
            },
            beforeSend: function() {
                $("#popuploader").show();
            },
            error: function() {
                $('#popuploader').hide();
                $("#popuploader").hide();
            }
        });

        $('html, body').animate({scrollTop: 0}, 'slow');
    });
}

/**
 * Delete action
 * @param {number|string} id - Record ID
 * @param {string} table - Table name
 */
function deleteAction(id, table) {
    runCrudIfConfirmed('Are you sure you want to delete this record?', function () {
        if (id == '' || id === null || id === undefined) {
            toastMsg('Please select ID to delete the record.', 'warning');
            return false;
        }

        $('#popuploader').show();
        $(".server-error").html('');
        $(".custom-error-msg").html('');

        AjaxHelper.post(
            App.getUrl('deleteAction'),
            { 'id': id, 'table': table },
            function(resp) {
                $('#popuploader').hide();
                var obj = typeof resp === 'string' ? $.parseJSON(resp) : resp;

                if (obj.status == 1) {
                    location.reload();
                } else {
                    var html = errorMessage(obj.message);
                    $(".custom-error-msg").html(html);
                }
            },
            function() {
                $('#popuploader').hide();
            }
        );
    });
}

// Export functions for use in other modules
if (typeof window !== 'undefined') {
    window.arcivedAction = arcivedAction;
    window.deleteAction = deleteAction;
}
