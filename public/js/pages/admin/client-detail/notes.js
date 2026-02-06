/**
 * Admin Client Detail - Note handlers
 */
'use strict';

(function() {
    jQuery(document).ready(function($){
        // ============================================================================
        // NOTE CREATION HANDLERS
        // ============================================================================

        // NOTE: createapplicationnewinvoice handler removed - Invoice Schedule feature has been removed

        $(document).on('click', '.create_note_d', function(){
            $('#create_note_d input[name="mailid"]').val(0);
            $('#create_note_d input[name="noteid"]').val('');
            $('#create_note_d #noteType').val('');
            $('#create_note_d #additionalFields').html('');
            $('#create_note_d .customerror').html('');
            $('#create_note_d .custom-error').html('');
            
            if($('#create_note_d .tinymce-simple').length > 0){
                try {
                    if($('#create_note_d .tinymce-simple').attr('id')){
                        TinyMCEHelpers.resetBySelector('#create_note_d .tinymce-simple');
                    } else {
                        $('#create_note_d .tinymce-simple').val('');
                    }
                } catch(e) {
                    $('#create_note_d .tinymce-simple').val('');
                }
            }
            
            $('#create_note_d #createNoteDModalLabel').html('Create Note');

            if($(this).attr('datatype') == 'note'){
                $('.is_not_note').hide();
            }else{
                var datasubject = $(this).attr('datasubject');
                var datamailid = $(this).attr('datamailid');
                $('#create_note_d input[name="title"]').val(datasubject);
                $('#create_note_d input[name="mailid"]').val(datamailid);
                $('.is_not_note').show();
            }
            
            $('#create_note_d').modal('show');
        });

        $('#noteType').on('change', function() {
            var selectedValue = $(this).val();
            var additionalFields = $("#additionalFields");
            additionalFields.html("");

            if(selectedValue === "Call") {
                additionalFields.append(`
                    <div class="form-group" style="margin-top:10px;">
                        <label for="mobileNumber">Mobile Number:</label>
                        <select name="mobileNumber" id="mobileNumber" class="form-control" data-valid="required"></select>
                        <span id="mobileNumberError" class="text-danger"></span>
                    </div>
                `);

                var client_id = $('#create_note_d #note_client_id').val();
                $('.popuploader').show();
                var url = App.getUrl('clientFetchContact') || App.getUrl('siteUrl') + '/clients/fetchClientContactNo';
                $.ajax({
                    url: url,
                    method: "POST",
                    headers: { 'X-CSRF-TOKEN': App.getCsrf()},
                    data: {client_id:client_id},
                    datatype: 'json',
                    success: function(response) {
                        $('.popuploader').hide();
                        var obj = typeof response === 'string' ? $.parseJSON(response) : response;
                        var contactlist = '<option value="">Select Contact</option>';
                        $.each(obj.clientContacts, function(index, subArray) {
                            var client_country_code = subArray.client_country_code || "";
                            contactlist += '<option value="'+client_country_code+' '+subArray.client_phone+'">'+client_country_code+' '+subArray.client_phone+' ('+subArray.contact_type+')</option>';
                        });
                        $('#mobileNumber').append(contactlist);
                    }
                });
            }
        });

        $(document).on('click', '.create_note', function(){
            $('#create_note').modal('show');
            $('#create_note input[name="mailid"]').val(0);
            $('#create_note input[name="title"]').val('');
            $('#create_note #createNoteModalLabel').html('Create Note');
            $('#create_note input[name="noteid"]').val('');
            if($("#create_note .tinymce-simple").length && typeof TinyMCEHelpers !== 'undefined') {
                TinyMCEHelpers.resetBySelector("#create_note .tinymce-simple");
            }
            if($(this).attr('datatype') == 'note'){
                $('.is_not_note').hide();
            }else{
                var datasubject = $(this).attr('datasubject');
                var datamailid = $(this).attr('datamailid');
                $('#create_note input[name="title"]').val(datasubject);
                $('#create_note input[name="mailid"]').val(datamailid);
                $('.is_not_note').show();
            }
        });

        // ============================================================================
        // SELECT2 INITIALIZATION FOR RECIPIENTS
        // ============================================================================
        
        $('.js-data-example-ajaxcc').select2({
            multiple: true,
            closeOnSelect: false,
            dropdownParent: $('#create_note'),
            ajax: {
                url: App.getUrl('clientGetRecipients') || App.getUrl('siteUrl') + '/clients/get-recipients',
                headers: { 'X-CSRF-TOKEN': App.getCsrf()},
                dataType: 'json',
                processResults: function (data) {
                    return {
                        results: data.items
                    };
                },
                cache: true
            },
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });

        $('.js-data-example-ajaxccapp').select2({
            multiple: true,
            closeOnSelect: false,
            dropdownParent: $('#applicationemailmodal'),
            ajax: {
                url: App.getUrl('clientGetRecipients') || App.getUrl('siteUrl') + '/clients/get-recipients',
                headers: { 'X-CSRF-TOKEN': App.getCsrf()},
                dataType: 'json',
                processResults: function (data) {
                    return {
                        results: data.items
                    };
                },
                cache: true
            },
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });

        function formatRepo (repo) {
            if (repo.loading) {
                return repo.text;
            }

            var $container = $(
                "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +
                    "<div  class='ag-flex ag-align-start'>" +
                        "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
                        "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small ></div>" +
                    "</div>" +
                "</div>" +
                "<div class='ag-flex ag-flex-column ag-align-end'>" +
                    "<span class='ui label yellow select2-result-repository__statistics'>" +
                    "</span>" +
                "</div>" +
                "</div>"
            );

            $container.find(".select2-result-repository__title").text(repo.name);
            $container.find(".select2-result-repository__description").text(repo.email);
            $container.find(".select2-result-repository__statistics").append(repo.status);

            return $container;
        }

        function formatRepoSelection (repo) {
            return repo.name || repo.text;
        }

        // ============================================================================
        // NOTE VIEWING AND EDITING HANDLERS
        // ============================================================================
        
        $(document).on('click', '.opennoteform', function(){
            $('#create_note').modal('show');
            $('#create_note #createNoteModalLabel').html('Edit Note');
            var v = $(this).attr('data-id');
            $('#create_note input[name="noteid"]').val(v);
            $('.popuploader').show();
            var url = App.getUrl('getNoteDetail') || App.getUrl('siteUrl') + '/getnotedetail';
            $.ajax({
                url: url,
                type:'GET',
                datatype:'json',
                data:{note_id:v},
                success:function(response){
                    $('.popuploader').hide();
                    var res = typeof response === 'string' ? JSON.parse(response) : response;

                    if(res.status){
                        $('#create_note input[name="title"]').val(res.data.title);
                        if($("#create_note .tinymce-simple").length && typeof TinyMCEHelpers !== 'undefined') {
                            TinyMCEHelpers.setContentBySelector("#create_note .tinymce-simple", res.data.description);
                        } else {
                            $("#create_note .tinymce-simple").val(res.data.description);
                        }
                    }
                }
            });
        });

        $(document).on('click', '.viewnote', function(){
            $('#view_note').modal('show');
            var v = $(this).attr('data-id');
            $('#view_note input[name="noteid"]').val(v);
            $('.popuploader').show();
            var url = App.getUrl('viewNoteDetail') || App.getUrl('siteUrl') + '/viewnotedetail';
            $.ajax({
                url: url,
                type:'GET',
                datatype:'json',
                data:{note_id:v},
                success:function(response){
                    $('.popuploader').hide();
                    var res = typeof response === 'string' ? JSON.parse(response) : response;

                    if(res.status){
                        $('#view_note .modal-body .note_content h5').html(res.data.title);
                        $("#view_note .modal-body .note_content p").html(res.data.description);
                    }
                }
            });
        });

        $(document).on('click', '.viewapplicationnote', function(){
            $('#view_application_note').modal('show');
            var v = $(this).attr('data-id');
            $('#view_application_note input[name="noteid"]').val(v);
            $('.popuploader').show();
            var url = App.getUrl('viewApplicationNote') || App.getUrl('siteUrl') + '/viewapplicationnote';
            $.ajax({
                url: url,
                type:'GET',
                datatype:'json',
                data:{note_id:v},
                success:function(response){
                    $('.popuploader').hide();
                    var res = typeof response === 'string' ? JSON.parse(response) : response;

                    if(res.status){
                        $('#view_application_note .modal-body .note_content h5').html(res.data.title);
                        $("#view_application_note .modal-body .note_content p").html(res.data.description);
                    }
                }
            });
        });
    });
})();
