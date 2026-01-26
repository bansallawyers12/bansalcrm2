/**
 * Admin Client Detail - Assignment handlers
 */
'use strict';

(function() {
    jQuery(document).ready(function($){
        // ============================================================================
        // ASSIGN USER HANDLER
        // ============================================================================
        $(document).on('click', '#assignUser', function(){
            $(".popuploader").show();
            var flag = true;
            var error = "";
            $(".custom-error").remove();
            
            if($('#rem_cat').val() == ''){
                $('.popuploader').hide();
                error="Assignee field is required.";
                $('#rem_cat').after("<span class='custom-error' role='alert'>"+error+"</span>");
                flag = false;
            }
            if($('#assignnote').val() == ''){
                $('.popuploader').hide();
                error="Note field is required.";
                $('#assignnote').after("<span class='custom-error' role='alert'>"+error+"</span>");
                flag = false;
            }
            if($('#task_group').val() == ''){
                $('.popuploader').hide();
                error="Group field is required.";
                $('#task_group').after("<span class='custom-error' role='alert'>"+error+"</span>");
                flag = false;
            }
            if(flag){
                var url = App.getUrl('clientAction') || App.getUrl('siteUrl') + '/clients/action/store';
                $.ajax({
                    type:'post',
                    url: url,
                    headers: { 'X-CSRF-TOKEN': App.getCsrf()},
                    data: {
                        note_type:'action',
                        description:$('#assignnote').val(),
                        client_id:$('#assign_client_id').val(),
                        followup_datetime:$('#popoverdatetime').val(),
                        assignee_name:$('#rem_cat :selected').text(),
                        rem_cat:$('#rem_cat option:selected').val(),
                        task_group:$('#task_group option:selected').val()
                    },
                    success: function(response){
                        $('.popuploader').hide();
                        var obj = typeof response === 'string' ? $.parseJSON(response) : response;
                        if(obj.success){
                            $("[data-role=popover]").each(function(){
                                (($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false
                            });
                            if(typeof getallactivities === 'function') {
                                getallactivities();
                            }
                            if(typeof getallnotes === 'function') {
                                getallnotes();
                            }
                        }
                    }
                });
            }else{
                $("#loader").hide();
            }
        });
    });
})();
