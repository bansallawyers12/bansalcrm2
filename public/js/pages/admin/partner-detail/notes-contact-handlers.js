/**
 * Admin Partner Detail - Notes, Contacts, Email Handlers
 *
 * Handles notes, contacts, branches, templates, and email modals.
 *
 * Dependencies:
 *   - jQuery
 *   - Select2
 *   - Summernote
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

    // Set contract dates
    if (PageConfig.contractExpiry) {
        $('#contract_expiry').val(PageConfig.contractExpiry);
    } else {
        $('#contract_expiry').val('');
    }

    if (PageConfig.contractStart) {
        $('#contract_start').val(PageConfig.contractStart);
    } else {
        $('#contract_start').val('');
    }

    // View note modal
    $(document).delegate('.viewnote', 'click', function(){
        $('#view_note').modal('show');
        var v = $(this).attr('data-id');
        $('#view_note input[name="noteid"]').val(v);
        $('.popuploader').show();
        $.ajax({
            url: App.getUrl('viewnotedetail'),
            type:'GET',
            datatype:'json',
            data:{note_id:v},
            success:function(response){
                $('.popuploader').hide();
                var res = JSON.parse(response);
                if(res.status){
                    $('#view_note .modal-body .note_content h5').html(res.data.title);
                    $("#view_note .modal-body .note_content p").html(res.data.description);
                    var ad = res.data.admin;
                    $("#view_note .modal-body .note_content .author").html('<a href="#">'+ad+'</a>');
                    var updated_at = res.data.updated_at;
                    $("#view_note .modal-body .note_content .lastdate").html('<a href="#">'+updated_at+'</a>');
                }
            }
        });
    });

    // Add contact
    $(document).delegate('.add_clientcontact','click', function(){
        $('#add_clientcontact #appliationModalLabel').html('Add Contact');
        $('#add_clientcontact input[name="contact_id"]').val('');
        $('#add_clientcontact select[name="country_code"]').val(PageConfig.defaultCountryCode || '');
        $('#add_clientcontact #primary_contact').prop('checked', false);
        $('#add_clientcontact .allinputfields input').val('');
        $('#add_clientcontact .allinputfields select').val('');
        $('#add_clientcontact').modal('show');
    });

    // Add branch
    $(document).delegate('.openbranchnew','click', function(){
        $('#add_clientbranch #appliationModalLabel').html('Add new branch');
        $('#add_clientbranch input[name="branch_id"]').val('');
        $('#add_clientbranch select[name="country_code"]').val(PageConfig.defaultCountryCode || '');
        $('#add_clientbranch #head_office').prop('checked', false);
        $('#add_clientbranch .allinputfields input').val('');
        $('#add_clientbranch .allinputfields select').val('');
        $('#add_clientbranch').modal('show');
    });

    function getallnotes(){
        $.ajax({
            url: App.getUrl('getNotes'),
            type:'GET',
            data:{clientid: partnerId, type:'partner'},
            success: function(responses){
                $('.popuploader').hide();
                $('.note_term_list').html(responses);
            }
        });
    }

    function getallactivities(){
        $.ajax({
            url: App.getUrl('getActivities'),
            type:'GET',
            datatype:'json',
            data:{id: partnerId},
            success: function(responses){
                var ress = JSON.parse(responses);
                var html = '';
                $.each(ress.data, function(k, v) {
                    html += '<div class="activity"><div class="activity-icon bg-primary text-white"><span>'+v.createdname+'</span></div><div class="activity-detail"><div class="mb-2"><span class="text-job">'+v.date+'</span></div><p><b>'+v.name+'</b> '+v.subject+'</p>';
                    if(v.message != null){
                        html += '<p>'+v.message+'</p>';
                    }
                    html += '</div></div>';
                });
                $('.activities').html(html);
            }
        });
    }

    // Expose helpers for other modules
    window.getallnotes = getallnotes;
    window.getallactivities = getallactivities;

    var notid = '';
    var delhref = '';
    $(document).delegate('.deletenote', 'click', function(){
        $('#confirmModal').modal('show');
        notid = $(this).attr('data-id');
        delhref = $(this).attr('data-href');
    });
    $(document).delegate('#confirmModal .accept', 'click', function(){
        $('.popuploader').show();
        $.ajax({
            url: siteUrl + '/' + delhref,
            type:'GET',
            datatype:'json',
            data:{note_id:notid,type:'partner'},
            success:function(response){
                $('.popuploader').hide();
                var res = JSON.parse(response);
                $('#confirmModal').modal('hide');
                if(res.status){
                    $('#note_id_'+notid).remove();
                    if(delhref == 'deletedocs'){
                        $('.documnetlist #id_'+notid).remove();
                    }
                    else if(delhref == 'deleteservices'){
                        $.ajax({
                            url: App.getUrl('getServices'),
                            type:'GET',
                            data:{clientid: partnerId},
                            success: function(responses){
                                $('.interest_serv_list').html(responses);
                            }
                        });
                    }else if(delhref == 'deletecontact'){
                        $.ajax({
                            url: App.getUrl('getContacts'),
                            type:'GET',
                            data:{clientid: partnerId},
                            success: function(responses){
                                $('.contact_term_list').html(responses);
                            }
                        });
                    }else if(delhref == 'deletebranch'){
                        $.ajax({
                            url: App.getUrl('getBranches'),
                            type:'GET',
                            data:{clientid: partnerId},
                            success: function(responses){
                                $('.branch_term_list').html(responses);
                            }
                        });
                    }else{
                        getallnotes();
                    }
                }
            }
        });
    });

    $(document).delegate('.create_note', 'click', function(){
        $('#create_note').modal('show');
        $('#create_note input[name="mailid"]').val(0);
        $('#create_note input[name="title"]').val('');
        $('#create_note #appliationModalLabel').html('Create Note');
        $("#create_note .summernote-simple").val('');
        $('#create_note input[name="noteid"]').val('');
        $("#create_note .summernote-simple").summernote('code','');
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

            var partner_id = $('#partner_id').val();
            $('.popuploader').show();
            $.ajax({
                url: App.getUrl('partnersFetchPartnerContactNo'),
                method: "POST",
                data: {partner_id:partner_id},
                datatype: 'json',
                success: function(response) {
                    $('.popuploader').hide();
                    var obj = $.parseJSON(response);
                    var partnerlist = '<option value="">Select Contact</option>';
                    $.each(obj.partnerContacts, function(index, subArray) {
                        var partner_country_code = subArray.partner_country_code || "";
                        var partner_phone = subArray.partner_phone || "";
                        partnerlist += '<option value="'+partner_country_code+' '+partner_phone+'">'+partner_country_code+' '+partner_phone+'</option>';
                    });
                    $('#mobileNumber').append(partnerlist);
                }
            });
        }
    });

    $(document).delegate('.create_student_note', 'click', function(){
        $('#create_student_note').modal('show');

        $('#student_id').val($(this).attr('data-studentid'));
        $('#student_ref_no').val($(this).attr('data-studentrefno'));
        $('#college_name').val($(this).attr('data-collegename'));

        $('#create_student_note input[name="mailid"]').val(0);
        $('#create_student_note input[name="title"]').val('');
        $('#create_student_note #studentappliationModalLabel').html('Add Note To Student');
        $("#create_student_note .summernote-simple").val('');
        $('#create_student_note input[name="noteid"]').val('');
        $("#create_student_note .summernote-simple").summernote('code','');
        if($(this).attr('datatype') == 'note'){
            $('.is_not_note').hide();
        } else {
            var datasubject = $(this).attr('datasubject');
            var datamailid = $(this).attr('datamailid');
            $('#create_student_note input[name="title"]').val(datasubject);
            $('#create_student_note input[name="mailid"]').val(datamailid);
            $('.is_not_note').show();
        }
    });

    $('#studentNoteType').on('change', function() {
        var selectedValue = $(this).val();
        var additionalStudentNoteFields = $("#additionalStudentNoteFields");
        additionalStudentNoteFields.html("");

        if(selectedValue === "Call") {
            additionalStudentNoteFields.append(`
                <div class="form-group" style="margin-top:10px;">
                    <label for="mobileNumber">Contact Number:</label>
                    <select name="mobileNumber" id="mobileNumber" class="form-control" data-valid="required"></select>
                    <span id="mobileNumberError" class="text-danger"></span>
                </div>
            `);

            var client_id = $('#student_id').val();
            $('.popuploader').show();
            $.ajax({
                url: App.getUrl('clientsFetchClientContactNo'),
                method: "POST",
                data: {client_id:client_id},
                datatype: 'json',
                success: function(response) {
                    $('.popuploader').hide();
                    var obj = $.parseJSON(response);
                    var contactlist = '<option value="">Select Contact</option>';
                    $.each(obj.clientContacts, function(index, subArray) {
                        var client_country_code = subArray.client_country_code || "";
                        var client_phone = subArray.client_phone || "";
                        contactlist += '<option value="'+client_country_code+' '+client_phone+'">'+client_country_code+' '+client_phone+' ('+subArray.contact_type+')</option>';
                    });
                    $('#mobileNumber').append(contactlist);
                }
            });
        }
    });

    $(document).delegate('.opentaskmodal', 'click', function(){
        $('#opentaskmodal').modal('show');
        $('#opentaskmodal input[name="mailid"]').val(0);
        $('#opentaskmodal input[name="title"]').val('');
        $('#opentaskmodal #appliationModalLabel').html('Create Note');
        $('#opentaskmodal input[name="attachments"]').val('');
        $('#opentaskmodal .showattachment').val('Choose file');

        var datasubject = $(this).attr('datasubject');
        var datamailid = $(this).attr('datamailid');
        $('#opentaskmodal input[name="title"]').val(datasubject);
        $('#opentaskmodal input[name="mailid"]').val(datamailid);
    });

    $('.js-data-example-ajaxcc').select2({
        multiple: true,
        closeOnSelect: false,
        dropdownParent: $('#create_note'),
        ajax: {
            url: App.getUrl('clientsGetRecipients'),
            dataType: 'json',
            processResults: function (data) {
                return { results: data.items };
            },
            cache: true
        },
        templateResult: formatRepo,
        templateSelection: formatRepoSelection
    });

    $('.js-data-example-ajaxcontact').select2({
        multiple: true,
        closeOnSelect: false,
        dropdownParent: $('#opentaskmodal'),
        ajax: {
            url: App.getUrl('clientsGetRecipients'),
            dataType: 'json',
            processResults: function (data) {
                return { results: data.items };
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

    $(document).delegate('.opennoteform', 'click', function(){
        $('#create_note').modal('show');
        $('#create_note #appliationModalLabel').html('Edit Note');
        var v = $(this).attr('data-id');
        $('#create_note input[name="noteid"]').val(v);
        $('.popuploader').show();
        $.ajax({
            url: App.getUrl('getnotedetail'),
            type:'GET',
            datatype:'json',
            data:{note_id:v},
            success:function(response){
                $('.popuploader').hide();
                var res = JSON.parse(response);
                if(res.status){
                    $('#create_note input[name="title"]').val(res.data.title);
                    $("#create_note .summernote-simple").val(res.data.description);
                    $("#create_note .summernote-simple").summernote('code',res.data.description);
                }
            }
        });
    });

    $(document).delegate('.opencontactform', 'click', function(){
        $('#add_clientcontact').modal('show');
        $('#add_clientcontact #appliationModalLabel').html('Edit Contact');
        var v = $(this).attr('data-id');
        $('#add_clientcontact input[name="contact_id"]').val(v);
        $('.popuploader').show();
        $.ajax({
            url: App.getUrl('getcontactdetail'),
            type:'GET',
            datatype:'json',
            data:{note_id:v},
            success:function(response){
                $('.popuploader').hide();
                var res = JSON.parse(response);
                if(res.status){
                    $('#add_clientcontact input[name="name"]').val(res.data.name);
                    $('#add_clientcontact input[name="email"]').val(res.data.contact_email);
                    $('#add_clientcontact input[name="phone"]').val(res.data.contact_phone);
                    $('#add_clientcontact input[name="fax"]').val(res.data.fax);
                    $('#add_clientcontact select[name="country_code"]').val(res.data.countrycode || '');
                    $('#add_clientcontact input[name="department"]').val(res.data.department);
                    $('#add_clientcontact input[name="position"]').val(res.data.position);
                    $('#add_clientcontact #branch').val(res.data.branch);
                    $('#add_clientcontact #primary_contact').prop('checked', res.data.primary_contact == 1);
                }
            }
        });
    });

    $(document).delegate('.openbranchform', 'click', function(){
        $('#add_clientbranch').modal('show');
        $('#add_clientbranch #appliationModalLabel').html('Edit Contact');
        var v = $(this).attr('data-id');
        $('#add_clientbranch input[name="branch_id"]').val(v);
        $('.popuploader').show();
        $.ajax({
            url: App.getUrl('getbranchdetail'),
            type:'GET',
            datatype:'json',
            data:{note_id:v},
            success:function(response){
                $('.popuploader').hide();
                var res = JSON.parse(response);
                if(res.status){
                    $('#add_clientbranch input[name="name"]').val(res.data.name);
                    $('#add_clientbranch input[name="email"]').val(res.data.email);
                    $('#add_clientbranch input[name="phone"]').val(res.data.phone);
                    $('#add_clientbranch #country').val(res.data.country);
                    $('#add_clientbranch select[name="country_code"]').val(res.data.country_code || '');
                    $('#add_clientbranch input[name="city"]').val(res.data.city);
                    $('#add_clientbranch input[name="state"]').val(res.data.state);
                    $('#add_clientbranch input[name="zip_code"]').val(res.data.zip);
                    $('#add_clientbranch #branch').val(res.data.branch);
                    $('#add_clientbranch #head_office').prop('checked', res.data.primary_contact == 1);
                }
            }
        });
    });

    $(document).delegate('.pinnote', 'click', function(){
        $('.popuploader').show();
        $.ajax({
            url: App.getUrl('pinnote') + '/',
            type:'GET',
            datatype:'json',
            data:{note_id:$(this).attr('data-id')},
            success:function(){
                getallnotes();
            }
        });
    });

    $(document).delegate('.clientemail', 'click', function(){
        $('#emailmodal').modal('show');
        var array = [];
        var data = [];
        var id = $(this).attr('data-id');
        array.push(id);
        var email = $(this).attr('data-email');
        var name = $(this).attr('data-name');
        var status = 'Client';

        data.push({
            id: id,
            text: name,
            html:  "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +
            "<div  class='ag-flex ag-align-start'>" +
            "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'>"+name+"</span>&nbsp;</div>" +
            "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'>"+email+"</small ></div>" +
            "</div>" +
            "</div>" +
            "<div class='ag-flex ag-flex-column ag-align-end'>" +
            "<span class='ui label yellow select2-result-repository__statistics'>"+ status +
            "</span>" +
            "</div>" +
            "</div>",
            title: name
        });

        $(".js-data-example-ajax").select2({
            data: data,
            escapeMarkup: function(markup) { return markup; },
            templateResult: function(data) { return data.html; },
            templateSelection: function(data) { return data.text; }
        });
        $('.js-data-example-ajax').val(array);
        $('.js-data-example-ajax').trigger('change');
    });

    $(document).delegate('.change_client_status', 'click', function(){
        var v = $(this).attr('rating');
        $('.change_client_status').removeClass('active');
        $(this).addClass('active');

        $.ajax({
            url: App.getUrl('changeClientStatus'),
            type:'GET',
            datatype:'json',
            data:{id: partnerId, rating: v},
            success: function(response){
                var res = JSON.parse(response);
                if(res.status){
                    $('.custom-error-msg').html('<span class="alert alert-success">'+res.message+'</span>');
                    getallactivities();
                }else{
                    $('.custom-error-msg').html('<span class="alert alert-danger">'+response.message+'</span>');
                }
            }
        });
    });

    $(document).delegate('.selecttemplate', 'change', function(){
        var v = $(this).val();
        $.ajax({
            url: App.getUrl('getTemplates'),
            type:'GET',
            datatype:'json',
            data:{id:v},
            success: function(response){
                var res = JSON.parse(response);
                $('.selectedsubject').val(res.subject);
                $("#emailmodal .summernote-simple").summernote('reset');
                $("#emailmodal .summernote-simple").summernote('code', res.description);
                $("#emailmodal .summernote-simple").val(res.description);
            }
        });
    });

    $(document).delegate('.selectapplicationtemplate', 'change', function(){
        var v = $(this).val();
        $.ajax({
            url: App.getUrl('getTemplates'),
            type:'GET',
            datatype:'json',
            data:{id:v},
            success: function(response){
                var res = JSON.parse(response);
                $('.selectedappsubject').val(res.subject);
                $("#applicationemailmodal .summernote-simple").summernote('reset');
                $("#applicationemailmodal .summernote-simple").summernote('code', res.description);
                $("#applicationemailmodal .summernote-simple").val(res.description);
            }
        });
    });

    $('.js-data-example-ajax').select2({
        multiple: true,
        closeOnSelect: false,
        dropdownParent: $('#emailmodal'),
        ajax: {
            url: App.getUrl('clientsGetRecipients'),
            dataType: 'json',
            processResults: function (data) {
                return { results: data.items };
            },
            cache: true
        },
        templateResult: formatRepo,
        templateSelection: formatRepoSelection
    });
});

})(); // End async wrapper
