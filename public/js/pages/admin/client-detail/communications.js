/**
 * Admin Client Detail - Communication actions
 */
'use strict';

(function() {
    jQuery(document).ready(function($){
        /////////////////////////////////////////////
        ////// At Google review button sent email with review link code start /////////
        /////////////////////////////////////////////
        $(document).on('click', '.googleReviewBtn', function(e){
            var is_greview_mail_sent = $(this).attr('data-is_greview_mail_sent');
            console.log(is_greview_mail_sent);
            if(is_greview_mail_sent != 1){
                is_greview_mail_sent = 0;
            } else {
                is_greview_mail_sent = 1;
            }
            var conf = confirm('Do you want to sent google review link in email?');
            //If review email not sent till now
            if(conf && is_greview_mail_sent != 1 ){
                var url = App.getUrl('isGReviewMailSent') || App.getUrl('siteUrl') + '/is_greview_mail_sent';
                $.ajax({
                    url: url,
                    headers: { 'X-CSRF-TOKEN': App.getCsrf()},
                    type:'POST',
                    datatype:'json',
                    data:{id: App.getPageConfig('clientId'), is_greview_mail_sent: is_greview_mail_sent},
                    success: function(response){
                        var obj = typeof response === 'string' ? $.parseJSON(response) : response;
                        if(obj.status){
                            alert(obj.message);
                            location.reload();
                        } else {
                            alert(obj.message);
                        }
                    }
                });
            } else {
                return false;
            }
        });

        /////////////////////////////////////////////
        ////// At Google review button sent email with review link code end /////////
        /////////////////////////////////////////////

        // ============================================================================
        // NOT PICKED CALL HANDLER
        // ============================================================================
        $(document).on('click', '.not_picked_call', function (e) {
            var clientName = App.getPageConfig('clientName') || 'user';
            clientName = clientName.charAt(0).toUpperCase() + clientName.slice(1).toLowerCase();

            var message = `Hi ${clientName},
We tried reaching you but couldn't connect. Please call us at 0396021330 or let us know a suitable time.
Please do not reply via SMS.
Bansal Immigration`;

            $('#messageText').val(message);
            $('#notPickedCallModal').modal('show');

            $('.sendMessage').off('click').on('click', function () {
                var message = $('#messageText').val();
                var not_picked_call = 1;
                var url = App.getUrl('notPickedCall') || App.getUrl('siteUrl') + '/not-picked-call';
                $.ajax({
                    url: url,
                    headers: { 'X-CSRF-TOKEN': App.getCsrf() },
                    type: 'POST',
                    datatype: 'json',
                    data: {
                        id: App.getPageConfig('clientId'),
                        not_picked_call: not_picked_call,
                        message: message
                    },
                    success: function (response) {
                        var obj = typeof response === 'string' ? $.parseJSON(response) : response;
                        if (obj.not_picked_call == 1) {
                            alert(obj.message);
                        } else {
                            alert(obj.message);
                        }
                        if(typeof getallactivities === 'function') {
                            getallactivities();
                        }
                        $('#notPickedCallModal').modal('hide');
                    }
                });
            });
        });
    });
})();
