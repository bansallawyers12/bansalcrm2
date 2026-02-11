/**
 * Admin Client Detail - Delete handlers
 */
'use strict';

(function() {
    jQuery(document).ready(function($){
        // ============================================================================
        // DELETE HANDLERS
        // ============================================================================
        var notid = '';
        var delhref = '';
        $(document).on('click', '.deletenote', function(){
            $('#confirmModal').modal('show');
            notid = $(this).attr('data-id');
            delhref = $(this).attr('data-href');
        });

        $(document).on('click', '#confirmModal .accept', function(){
            $('.popuploader').show();
            var baseUrl = App.getUrl('siteUrl') || '';
            $.ajax({
                url: baseUrl + '/' + delhref,
                type:'GET',
                datatype:'json',
                data:{note_id:notid},
                success:function(response){
                    $('.popuploader').hide();
                    var res = typeof response === 'string' ? JSON.parse(response) : response;
                    $('#confirmModal').modal('hide');
                    if(res.status){
                        $('#note_id_'+notid).remove();
                        if(res.status == true){
                            $('#id_'+notid).remove();
                        }

                        if(delhref == 'deletedocs'){
                            $('.documnetlist #id_'+notid).remove();
                            $('.migdocumnetlist #id_'+notid).remove();
                        }
                        if(delhref == 'deletealldocs'){
                            $('.alldocumnetlist #id_'+notid).remove();
                        }
                        if(delhref == 'superagent'){
                            $('.supagent_data').remove();
                        }
                        if(delhref == 'subagent'){
                            $('.subagent_data .client_info').remove();
                        }
                        if(delhref == 'deleteapplicationdocs'){
                            $('.mychecklistdocdata').html(res.doclistdata);
                            $('.checklistuploadcount').html(res.applicationuploadcount);
                            $('.'+res.type+'_checklists').html(res.checklistdata);

                            if(res.application_id){
                                var logsUrl = App.getUrl('getApplicationsLogs') || App.getUrl('siteUrl') + '/get-applications-logs';
                                $.ajax({
                                    url: logsUrl,
                                    type:'GET',
                                    data:{id: res.application_id},
                                    success: function(responses){
                                        $('#accordion').html(responses);
                                    }
                                });
                            }
                        }else if(delhref != 'superagent' && delhref != 'subagent'){
                            // Only call getallnotes for note deletions, not for super/sub agent
                            if(typeof getallnotes === 'function') {
                                getallnotes();
                            }
                        }

                        if(typeof getallactivities === 'function') {
                            getallactivities();
                        }
                    }
                },
                error: function(xhr, status, error){
                    $('.popuploader').hide();
                    $('#confirmModal').modal('hide');
                    console.error('Delete operation failed:', error);
                    alert('Delete operation failed. Please try again.');
                }
            });
        });

        var activitylogid = '';
        var delloghref = '';
        $(document).on('click', '.deleteactivitylog', function(){
            $('#confirmLogModal').modal('show');
            activitylogid = $(this).attr('data-id');
            delloghref = $(this).attr('data-href');
        });

        $(document).on('click', '#confirmLogModal .accept', function(){
            $('.popuploader').show();
            var baseUrl = App.getUrl('siteUrl') || '';
            $.ajax({
                url: baseUrl + '/' + delloghref,
                type:'GET',
                datatype:'json',
                data:{activitylogid:activitylogid},
                success:function(response){
                    var res = typeof response === 'string' ? JSON.parse(response) : response;
                    $('#confirmLogModal').modal('hide');
                    if(res.status){
                        $('#activity_'+activitylogid).remove();
                        if(typeof getallactivities === 'function') {
                            getallactivities();
                        }
                    }
                }
            });
        });
    });
})();
