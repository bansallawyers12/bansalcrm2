/**
 * Admin Client Detail - Document actions
 */
'use strict';

(function() {
    jQuery(document).ready(function($){
        // ============================================================================
        // DOCUMENT VERIFICATION HANDLERS
        // ============================================================================
        var verify_doc_id = '';
        var verify_doc_href = '';
        var verify_doc_type = '';
        $(document).on('click', '.verifydoc', function(){
            $('#confirmDocModal').modal('show');
            verify_doc_id = $(this).attr('data-id');
            verify_doc_href = $(this).attr('data-href');
            verify_doc_type = $(this).attr('data-doctype');
        });

        $(document).on('click', '#confirmDocModal .accept', function(){
            $('.popuploader').show();
            var baseUrl = App.getUrl('siteUrl') || '';
            $.ajax({
                url: baseUrl + '/' + verify_doc_href,
                type:'POST',
                headers: { 'X-CSRF-TOKEN': App.getCsrf()},
                datatype:'json',
                data:{doc_id:verify_doc_id, doc_type:verify_doc_type },
                success:function(response){
                    $('.popuploader').hide();
                    var res = typeof response === 'string' ? JSON.parse(response) : response;
                    $('#confirmDocModal').modal('hide');
                    if(res.status){
                        if(res.doc_type == 'documents') {
                            $('.alldocumnetlist #docverifiedby_'+verify_doc_id).html(res.verified_by + "<br>" + res.verified_at);
                        }
                        if(typeof getallactivities === 'function') {
                            getallactivities();
                        }
                    }
                }
            });
        });

        var notuse_doc_id = '';
        var notuse_doc_href = '';
        var notuse_doc_type = '';
        $(document).on('click', '.notuseddoc', function(){
            $('#confirmNotUseDocModal').modal('show');
            notuse_doc_id = $(this).attr('data-id');
            notuse_doc_href = $(this).attr('data-href');
            notuse_doc_type = $(this).attr('data-doctype');
        });

        $(document).on('click', '#confirmNotUseDocModal .accept', function(){
            $('.popuploader').show();
            var baseUrl = App.getUrl('siteUrl') || '';
            $.ajax({
                url: baseUrl + '/' + notuse_doc_href,
                type:'POST',
                headers: { 'X-CSRF-TOKEN': App.getCsrf()},
                datatype:'json',
                data:{doc_id:notuse_doc_id, doc_type:notuse_doc_type },
                success:function(response){
                    $('.popuploader').hide();
                    var res = typeof response === 'string' ? JSON.parse(response) : response;
                    $('#confirmNotUseDocModal').modal('hide');
                    if(res.status){
                        if(res.doc_type == 'documents') {
                            $('.alldocumnetlist #id_'+res.doc_id).remove();
                        }
                        if(res.docInfo) {
                            var subArray = res.docInfo;
                            var trRow = "";
                            if(subArray.myfile_key != ''){
                                trRow += "<tr class='drow' id='id_"+subArray.id+"'><td>"+subArray.checklist+"</td><td>"+ res.Added_By + "<br>" + res.Added_date+"</td><td><a target='_blank' class='dropdown-item' href='"+subArray.myfile+"'><i class='fas fa-file-image'></i> <span>"+subArray.file_name+'.'+subArray.filetype+"</span></a></div></td><td>"+res.Verified_By+ "<br>" +res.Verified_At+"</td></tr>";
                            } else {
                                trRow += "<tr class='drow' id='id_"+subArray.id+"'><td>"+subArray.checklist+"</td><td>"+ res.Added_By + "<br>" + res.Added_date+"</td><td><i class='fas fa-file-image'></i> <span>"+subArray.file_name+'.'+subArray.filetype+"</span></div></td><td>"+res.Verified_By+ "<br>" +res.Verified_At+"</td></tr>";
                            }
                            $('.notuseddocumnetlist').append(trRow);
                        }
                        if(typeof getallactivities === 'function') {
                            getallactivities();
                        }
                    }
                }
            });
        });

        var backto_doc_id = '';
        var backto_doc_href = '';
        var backto_doc_type = '';
        $(document).on('click', '.backtodoc', function(){
            $('#confirmBackToDocModal').modal('show');
            backto_doc_id = $(this).attr('data-id');
            backto_doc_href = $(this).attr('data-href');
            backto_doc_type = $(this).attr('data-doctype');
        });

        $(document).on('click', '#confirmBackToDocModal .accept', function(){
            $('.popuploader').show();
            var baseUrl = App.getUrl('siteUrl') || '';
            $.ajax({
                url: baseUrl + '/' + backto_doc_href,
                type:'POST',
                headers: { 'X-CSRF-TOKEN': App.getCsrf()},
                datatype:'json',
                data:{doc_id:backto_doc_id, doc_type:backto_doc_type },
                success:function(response){
                    $('.popuploader').hide();
                    var res = typeof response === 'string' ? JSON.parse(response) : response;
                    $('#confirmBackToDocModal').modal('hide');
                    if(res.status){
                        if(res.doc_type == 'documents') {
                            $('.notuseddocumnetlist #id_'+res.doc_id).remove();
                        }
                        location.reload();
                    }
                }
            });
        });
    });
})();
