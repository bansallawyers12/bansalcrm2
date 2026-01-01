var requiredError = 'This field is required.';
var emailError = "Please enter the valid email address.";
var captcha = "Captcha invalid.";
var maxError = "Number should be less than or equal to ";
var min = "This field should be greater than or equal to ";
var max = "This field should be less than or equal to ";
var equal = "This field should be equal to ";

// Helper function to sync TinyMCE/summernote content to textarea before form submission
function syncEditorContent($form) {
	$form.find('textarea.summernote-simple, textarea.summernote').each(function() {
		var $field = $(this);
		
		// Try to sync from TinyMCE first
		if(typeof tinymce !== 'undefined') {
			var editorId = $field.attr('id');
			if(editorId) {
				var editor = tinymce.get(editorId);
				if(editor) {
					var content = editor.getContent();
					$field.val(content);
					return;
				}
			}
			// Try to find editor by textarea element
			try {
				var editor = tinymce.get($field[0]);
				if(editor) {
					var content = editor.getContent();
					$field.val(content);
					return;
				}
			} catch(e) {
				// Continue to next method
			}
		}
		// Fallback to summernote method
		if(typeof $.fn.summernote !== 'undefined') {
			try {
				var content = $field.summernote('code');
				if(content !== undefined && content !== null) {
					$field.val(content);
					return;
				}
			} catch(e) {
				// Continue
			}
		}
	});
}

// Helper function to get value from summernote/tinymce fields
function getFieldValue($field) {
	var for_class = $field.attr('class') || '';
	
	// Check if it's a summernote/tinymce field
	if(for_class.indexOf('summernote-simple') !== -1 || for_class.indexOf('summernote') !== -1) {
		// Try to get content from TinyMCE first
		if(typeof tinymce !== 'undefined') {
			var editorId = $field.attr('id');
			if(editorId) {
				var editor = tinymce.get(editorId);
				if(editor) {
					var content = editor.getContent();
					// Remove HTML tags and trim whitespace for validation
					var textContent = $('<div>').html(content).text();
					return $.trim(textContent);
				}
			}
			// Try to find editor by textarea element
			try {
				var editor = tinymce.get($field[0]);
				if(editor) {
					var content = editor.getContent();
					var textContent = $('<div>').html(content).text();
					return $.trim(textContent);
				}
			} catch(e) {
				// Continue to next method
			}
		}
		// Fallback to summernote method
		if(typeof $.fn.summernote !== 'undefined') {
			try {
				var content = $field.summernote('code');
				if(content) {
					var textContent = $('<div>').html(content).text();
					return $.trim(textContent);
				}
			} catch(e) {
				// Continue to next method
			}
		}
		// Final fallback: check if textarea has value directly
		var directValue = $.trim($field.val());
		if(directValue) {
			// Remove HTML tags if present
			var textContent = $('<div>').html(directValue).text();
			return $.trim(textContent);
		}
	}
	
	// Default to regular val() for other fields
	return $.trim($field.val());
}

function customValidate(formName, savetype = '')
	{
		$(".popuploader").show(); //all form submit
		
		var i = 0;	
		$(".custom-error").remove(); //remove all errors when submit the button
		
		$("form[name="+formName+"] :input[data-valid]").each(function(){
			var dataValidation = $(this).attr('data-valid');
			var splitDataValidation = dataValidation.split(' ');
			
			var j = 0; //for serial wise errors shown	
			if($.inArray("required", splitDataValidation) !== -1) //for required
				{
					var for_class = $(this).attr('class');	
					if(for_class.indexOf('multiselect_subject') != -1)
						{
							var value = $.trim($(this).val());	
							if (value.length === 0) 
								{
									i++;
									j++;
									$(this).parent().after(errorDisplay(requiredError)); 
								}	
						} 
					else 
						{
							var fieldValue = getFieldValue($(this));
							if( !fieldValue ) 
								{
									i++;
									j++;
									$(this).after(errorDisplay(requiredError));  
								}
						}
				}
			if(j <= 0)
				{
					var fieldValue = getFieldValue($(this));
					
					if($.inArray("email", splitDataValidation) !== -1) //for email
						{
							if(!validateEmail(fieldValue)) 
								{
									i++;
									$(this).after(errorDisplay(emailError));  
								}
						}
						
							
					var forMin = splitDataValidation.find(a =>a.includes("min"));
					if(typeof forMin != 'undefined')
						{
							var breakMin = forMin.split('-');
							var digit = breakMin[1];

							var value = fieldValue.length;
							if(value < digit) 
								{
									i++;
									$(this).after(errorDisplay(min+' '+digit+' character.'));  
								}	
						}
						
					var forMax = splitDataValidation.find(a =>a.includes("max"));
					if(typeof forMax != 'undefined')
						{
							var breakMax = forMax.split('-');
							var digit = breakMax[1];

							var value = fieldValue.length;
							if(value > digit) 
								{
									i++;
									$(this).after(errorDisplay(max+' '+digit+' character.'));  
								}	
						}
						
					var forEqual = splitDataValidation.find(a =>a.includes("equal"));
					if(typeof forEqual != 'undefined')
						{
							var breakEqual = forEqual.split('-');
							var digit = breakEqual[1];

							var value = (fieldValue.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-')).length;
							if(value != digit) 
								{
									i++;
									$(this).after(errorDisplay(equal+' '+digit+' character.'));  
								}	
						}
				}			
		});
		
		if(i > 0)
			{
				
				if(formName == 'add-query'){
					$('html, body').animate({scrollTop:$("#row_scroll"). offset(). top}, 'slow');
				}else if(formName != 'upload-answer')	{
					$('html, body').animate({scrollTop:0}, 'slow');
				}
				$(".popuploader").hide();
				return false;
			}	
		else
			{
				if(formName == 'add-query')
					{
						$('#preloader').show();
						$('#preloader div').show();
						var myform = document.getElementById('enquiryco');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('#preloader').hide();
								$('#preloader div').hide();
								var obj = $.parseJSON(response);
								if(obj.success){
									window.location = redirecturl;
								}else{
									$('.customerror').html(obj.message);
									$('html, body').animate({scrollTop:$("#row_scroll"). offset(). top}, 'slow');
								}
							}
						});
					}else if(formName == 'queryform')
					{
						$('#preloader').show();
						$('#preloader div').show();
						var myform = document.getElementById('popenquiryco');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('#preloader').hide();
								$('#preloader div').hide();
								var obj = $.parseJSON(response);
								if(obj.success){
									window.location = redirecturl;
								}else{
									$('.customerror').html(obj.message);
									
								}
							}
						});
					}else if(formName == 'add-note')
					{   
						var myform = document.getElementById('addnoteform');
						syncEditorContent($(myform));
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								if(obj.success){
									$('#myAddnotes .modal-title').html('');
									$('#myAddnotes #note_type').html('');
									$('#myAddnotes').modal('hide');
									myfollowuplist(obj.leadid);
								}else{
									$('#myAddnotes .customerror').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
					}else if(formName == 'edit-note')
					{   
						var myform = document.getElementById('editnoteform');
						syncEditorContent($(myform));
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								if(obj.success){
									$('#myeditnotes .modal-title').html('');
									$('#myeditnotes #note_type').html('');
									$('#myeditnotes').modal('hide');
									myfollowuplist(obj.leadid);
								}else{
									$('#myeditnotes .customerror').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
					}else if(formName == 'appnotetermform')
					{   
				var noteid = $('#appnotetermform input[name="noteid"]').val();
						var myform = document.getElementById('appnotetermform');
						syncEditorContent($(myform));
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
									$('.popuploader').hide();
								var obj = $.parseJSON(response);
								if(obj.status){
									$('#create_applicationnote').modal('hide');
									$.ajax({
										url: site_url+'/admin/get-applications-logs',
										type:'GET',
										data:{id: noteid},
										success: function(responses){
											 
											$('#accordion').html(responses);
										}
									});
								}else{
									$('#create_applicationnote .customerror').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
					}else if(formName == 'clientnotetermform')
					{
						
						var client_id = $('input[name="client_id"]').val(); 	
						var myform = document.getElementById('clientnotetermform');
						syncEditorContent($(myform));
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('#create_note').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								$.ajax({
									url: site_url+'/admin/get-partner-notes',
									type:'GET',
									data:{clientid:client_id,type:'partner'},
									success: function(responses){
										
										$('.note_term_list').html(responses);
									}
								});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
					}

					else if(formName == 'studentnotetermform')
                    {
                        var partner_id = $('input[name="partner_id"]').val();
                        var myform = document.getElementById('studentnotetermform');
                        syncEditorContent($(myform));
                        var fd = new FormData(myform);
                        $.ajax({
                            type:'post',
                            url:$("form[name="+formName+"]").attr('action'),
                            processData: false,
                            contentType: false,
                            data: fd,
                            success: function(response){
                                $('.popuploader').hide();
                                var obj = $.parseJSON(response);
                                if(obj.status){
                                    $('#create_student_note').modal('hide');
                                    $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
                                    //Get Partner all activity logs
                                    $.ajax({
                                        url: site_url+'/admin/get-partner-activities',
                                        type:'GET',
                                        datatype:'json',
                                        data:{partner_id:partner_id},
                                        success: function(responses){
                                            var ress = JSON.parse(responses);
                                            var html = '';
                                            $.each(ress.data, function(k, v) {
                                                if(v.subject != ""){
                                                    if(v.subject === null){
                                                        var subject =  "";
                                                    } else {
                                                        var subject =  v.subject;
                                                    }
                                                } else {
                                                    var subject =  "";
                                                }

                                                if(v.pin == 1){
                                                    html += '<div class="activity" id="activity_'+v.activity_id+'" ><div class="activity-icon bg-primary text-white"><span>'+v.createdname+'</span></div><div class="activity-detail" style="border: 1px solid #dbdbdb;"><div class="activity-head"><div class="activity-title"><p><b>'+v.name+'</b> '+ subject+'</p></div><div class="activity-date"><span class="text-job">'+v.date+'</span></div></div><div class="right" style="float: right;margin-top: -40px;"><div class="pined_note"><i class="fa fa-thumbtack" style="font-size: 12px;color: #6777ef;"></i></div><div class="dropdown d-inline dropdown_ellipsis_icon"><a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a><div class="dropdown-menu"><a data-id="'+v.activity_id+'" data-href="deleteactivitylog" class="dropdown-item deleteactivitylog" href="javascript:;" >Delete</a><a data-id="'+v.activity_id+'"  class="dropdown-item pinactivitylog" href="javascript:;" >UnPin</a></div></div></div>';
                                                } else {
                                                    html += '<div class="activity" id="activity_'+v.activity_id+'" ><div class="activity-icon bg-primary text-white"><span>'+v.createdname+'</span></div><div class="activity-detail" style="border: 1px solid #dbdbdb;"><div class="activity-head"><div class="activity-title"><p><b>'+v.name+'</b> '+ subject+'</p></div><div class="activity-date"><span class="text-job">'+v.date+'</span></div></div><div class="right" style="float: right;margin-top: -40px;"><div class="dropdown d-inline dropdown_ellipsis_icon"><a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a><div class="dropdown-menu"><a data-id="'+v.activity_id+'" data-href="deleteactivitylog" class="dropdown-item deleteactivitylog" href="javascript:;" >Delete</a><a data-id="'+v.activity_id+'"  class="dropdown-item pinactivitylog" href="javascript:;" >Pin</a></div></div></div>';
                                                }

                                                if(v.message != null){
                                                    html += '<p>'+v.message+'</p>';
                                                }
                                                if(v.followup_date != null){
                                                    html += '<p>'+v.followup_date+'</p>';
                                                }
                                                if(v.task_group != null){
                                                    html += '<p>'+v.task_group+'</p>';
                                                }
                                                html += '</div></div>';
                                            });

                                            $('.activities').html(html);
                                            $('.popuploader').hide();
                                        }
                                    });
                                } else {
                                    $('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
                            }
                        });
                    }

					else if(formName == 'clientcontact')
					{
						
						var client_id = $('input[name="client_id"]').val(); 	
						var myform = document.getElementById('clientcontact');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('#add_clientcontact').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								$.ajax({
									url: site_url+'/admin/get-contacts',
									type:'GET',
									data:{clientid:client_id,type:'partner'},
									success: function(responses){
										
										$('.contact_term_list').html(responses);
									}
								});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
					}else if(formName == 'clientbranch')
					{
						
						var client_id = $('input[name="client_id"]').val(); 	
						var myform = document.getElementById('clientbranch');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('#add_clientbranch').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								$.ajax({
									url: site_url+'/admin/get-branches',
									type:'GET',
									data:{clientid:client_id,type:'partner'},
									success: function(responses){
										
										$('.branch_term_list').html(responses);
									}
								});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
					}else if(formName == 'taskform'){
						var client_id = $('#tasktermform input[name="client_id"]').val();
						var myform = document.getElementById('tasktermform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('#opentaskmodal').modal('hide');
								if(obj.status){
									$('#create_note').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/admin/get-tasks',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											// $('#my-datatable').DataTable().destroy();
											$('.taskdata').html(responses);
											$('#my-datatable').DataTable({
												"searching": false,
												"lengthChange": false,
											  "columnDefs": [
												{ "sortable": false, "targets": [0, 2, 3] }
											  ],
											  order: [[1, "desc"]] //column indexes is zero based

												
											}).draw();
											
										}
									});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}

					 else if(formName == 'create_client_receipt'){
						var client_id = $('#create_client_receipt input[name="client_id"]').val();
						var myform = document.getElementById('create_client_receipt');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide();
								var obj = $.parseJSON(response);
								$('#createclientreceiptmodal').modal('hide');
								if(obj.status){
                                    if(obj.function_type == 'add')
                                    {
                                        if(obj.requestData){
                                            var reqData = obj.requestData;
                                            var awsUrl = obj.awsUrl; //console.log('awsUrl='+awsUrl);
                                            var printUrl = obj.printUrl; //console.log('printUrl='+printUrl);
                                            var lastInsertedId = obj.lastInsertedId; //console.log('lastInsertedId='+lastInsertedId);
 	                                        var validate_receipt = obj.validate_receipt;
                                            var trRows = "";
                                            $.each(reqData, function(index, subArray) {
                                                if(awsUrl != ""){
                                                    var awsLink = '<a target="_blank" class="link-primary" href="'+awsUrl+'"><i class="fas fa-file-pdf"></i></a>';
                                                } else {
                                                    var awsLink = '';
                                                }

                                                if(printUrl != ""){
                                                    var printLink = '<a target="_blank" class="link-primary" href="'+printUrl+'"><i class="fa fa-print" aria-hidden="true"></i></a>';
                                                } else {
                                                    var printLink = '';
                                                }

                                                if(validate_receipt != "1"){
                                                	var editLink = '<a class="link-primary updateclientreceipt" href="javascript:;" data-id="'+lastInsertedId+'"><i class="fas fa-pencil-alt"></i></a>';
												} else {
                                                    var editLink = '';
                                                }

                                                trRows += "<tr><td>"+subArray.trans_date+" "+awsLink+"</td><td>"+subArray.entry_date+"</td><td>"+subArray.trans_no+"</td><td>"+subArray.payment_method+"</td><td>"+subArray.description+"</td><td>$"+subArray.deposit_amount+" "+printLink+" "+editLink+"</td></tr>";
                                            });
                                        }
                                        //console.log('trRows='+trRows);
                                        //$('.productitemListTr').append(trRows);
                                        $('.lastRow').before(trRows);
                                    }
                                    if(obj.function_type == 'edit')
                                    {
                                        if(obj.requestData){
											var reqData = obj.requestData;
                                            var reqData = obj.requestData;
                                            var awsUrl = obj.awsUrl; //console.log('awsUrl='+awsUrl);
                                            var printUrl = obj.printUrl; //console.log('printUrl='+printUrl);
                                            var lastInsertedId = obj.lastInsertedId; //console.log('lastInsertedId='+lastInsertedId);
                                            var validate_receipt = obj.validate_receipt;

											//var trRows = "";
											$.each(reqData, function(index, subArray) {
												$('#TrRow_'+subArray.id).empty();
												if(awsUrl != ""){
                                                    var awsLink = '<a target="_blank" class="link-primary" href="'+awsUrl+'"><i class="fas fa-file-pdf"></i></a>';
                                                } else {
                                                    var awsLink = '';
                                                }

                                                if(printUrl != ""){
                                                    var printLink = '<a target="_blank" class="link-primary" href="'+printUrl+'"><i class="fa fa-print" aria-hidden="true"></i></a>';
                                                } else {
                                                    var printLink = '';
                                                }

                                                if(validate_receipt != "1"){
                                                	var editLink = '<a class="link-primary updateclientreceipt" href="javascript:;" data-id="'+lastInsertedId+'"><i class="fas fa-pencil-alt"></i></a>';
												} else {
                                                    var editLink = '';
                                                }

                                                var trRows = "<td>"+subArray.trans_date+" "+awsLink+"</td><td>"+subArray.entry_date+"</td><td>"+subArray.trans_no+"</td><td>"+subArray.payment_method+"</td><td>"+subArray.description+"</td><td>$"+subArray.deposit_amount+" "+printLink+" "+editLink+"</td>";

												$('#TrRow_'+subArray.id).append(trRows);
											});
										}
                                    }
									if(obj.db_total_deposit_amount){
										$('.totDepoAmTillNow').html("$"+obj.db_total_deposit_amount);

										$('#sum_of_client_receipts').val("$"+obj.db_total_deposit_amount);
                                    }
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
								}
                            }
						});
					}
					
                    
					 else if(formName == 'create_student_invoice')
                    {
						var partner_id = $('#create_student_invoice input[name="partner_id"]').val();
						var myform = document.getElementById('create_student_invoice');
						var fd = new FormData(myform);
                        fd.append('save_type', savetype);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide();
								var obj = $.parseJSON(response);
								$('#createpartnerstudentinvoicemodal').modal('hide');
								if(obj.status){
                                    if(obj.function_type == 'add')
                                    {
                                        if(obj.requestData)
                                        {
                                            var subArray = obj.requestData;
                                            var awsUrl = obj.awsUrl;
                                            var printUrl = obj.printUrl;
                                            //var lastInsertedId = obj.lastInsertedId;
                                            var db_total_enrolled_student = obj.db_total_enrolled_student;
                                            var db_total_amount = obj.db_total_amount;
                                            var last_invoice_id = obj.last_invoice_id;
                                            var trRows = "";

                                            if(awsUrl != ""){
                                                var awsLink = '<a target="_blank" class="link-primary" href="'+awsUrl+'"><i class="fas fa-file-pdf"></i></a>';
                                            } else {
                                                var awsLink = '';
                                            }
                                            if(printUrl != ""){
                                                var printLink = '<a target="_blank" class="link-primary" href="'+printUrl+'"><i class="fa fa-print" aria-hidden="true"></i></a>';
                                            } else {
                                                var printLink = '';
                                            }
                                            var draftlink = '<a class="link-primary updatedraftstudentinvoice" href="javascript:;" data-invoiceid="'+last_invoice_id+'"><i class="fas fa-pencil-alt"></i></a>';
                                            var dellink = '<a class="link-primary deletestudentinvoice" href="javascript:;" data-invoiceid="'+last_invoice_id+'" data-invoicetype="1" data-partnerid="'+obj.partnerid+'"><i class="fas fa-trash"></i></a>';
                                            trRows += "<tr id='TrRow_"+last_invoice_id+"'><td style='padding-top: 5px !important;padding-bottom: 5px !important;'>"+subArray.invoice_date+" "+awsLink+"</td><td style='padding-top: 5px !important;padding-bottom: 5px !important;'>"+subArray.invoice_no+"</td><td style='padding-top: 5px !important;padding-bottom: 5px !important;'>"+db_total_enrolled_student+"</td><td style='padding-top: 5px !important;padding-bottom: 5px !important;'>$"+db_total_amount+" "+printLink+" "+draftlink+" "+dellink+"</td><td style='padding-top: 5px !important;padding-bottom: 5px !important;'><select name='sent_option'  class='sent_option' data-invoiceid="+last_invoice_id+"><option value='No'>No</option><option value='Yes'>Yes</option></select></td></tr>";
                                        }
                                        $('.lastRow').before(trRows);
                                        if(obj.db_total_deposit_amount){
                                            $('.totDepoAmTillNow').html("$"+obj.db_total_deposit_amount);
                                        }
                                    }

                                    if(obj.function_type == 'edit')
                                    {
                                        //for delete
                                        if(obj.requestDeleteDataType == 'delete'){
                                            if(obj.requestDeleteData){
                                                var requestDeleteData = obj.requestDeleteData;
                                                $.each(requestDeleteData, function(index1, subArray1) {
                                                    $('#TrRow_'+subArray1).remove();
                                                });
                                            }
                                        }

                                        //for add new entry
                                        if(obj.requestAddDataType == 'add'){
                                            if(obj.requestAddData){
                                                var awsUrl2 = obj.awsUrl2;
                                                var printUrl2 = obj.printUrl2;
                                                var db_total_enrolled_student2 = obj.db_total_enrolled_student2;
                                                var db_total_amount2 = obj.db_total_amount2;
                                                var last_invoice_id = obj.last_invoice_id;

                                                if(awsUrl2 != ""){
                                                    var awsLink2 = '<a target="_blank" class="link-primary" href="'+awsUrl2+'"><i class="fas fa-file-pdf"></i></a>';
                                                } else {
                                                    var awsLink2 = '';
                                                }
                                                if(printUrl2 != ""){
                                                    var printLink2 = '<a target="_blank" class="link-primary" href="'+printUrl2+'"><i class="fa fa-print" aria-hidden="true"></i></a>';
                                                } else {
                                                    var printLink2 = '';
                                                }
                                                var unique_invoice_id2 = "TrRow_"+obj.requestAddData.invoice_id;
                                                var draftlink2 = '<a class="link-primary updatedraftstudentinvoice" href="javascript:;" data-invoiceid="'+obj.requestAddData.invoice_id+'"><i class="fas fa-pencil-alt"></i></a>';
                                                $('#'+unique_invoice_id2+' td:first').html(obj.requestAddData.invoice_date+" "+awsLink2);
                                                $('#'+unique_invoice_id2+' td:nth-child(3)').html(db_total_enrolled_student2);
                                                $('#'+unique_invoice_id2+' td:nth-child(4)').html("$"+db_total_amount2+" "+printLink2+" "+draftlink2);
                                            }
                                        } //end function type edit and add data

                                        if(obj.requestData){
                                            var awsUrl2 = obj.awsUrl2;
                                            var printUrl2 = obj.printUrl2;
                                            var db_total_enrolled_student2 = obj.db_total_enrolled_student2;
                                            var db_total_amount2 = obj.db_total_amount2;
                                            var last_invoice_id = obj.last_invoice_id;

                                            if(awsUrl2 != ""){
                                                var awsLink2 = '<a target="_blank" class="link-primary" href="'+awsUrl2+'"><i class="fas fa-file-pdf"></i></a>';
                                            } else {
                                                var awsLink2 = '';
                                            }

                                            if(printUrl2 != ""){
                                                var printLink2 = '<a target="_blank" class="link-primary" href="'+printUrl2+'"><i class="fa fa-print" aria-hidden="true"></i></a>';
                                            } else {
                                                var printLink2 = '';
                                            }
                                            var unique_invoice_id2 = "TrRow_"+obj.requestData.invoice_id;
                                            var draftlink2 = '<a class="link-primary updatedraftstudentinvoice" href="javascript:;" data-invoiceid="'+obj.requestData.invoice_id+'"><i class="fas fa-pencil-alt"></i></a>';
                                            var dellink2 = '<a class="link-primary deletestudentinvoice" href="javascript:;" data-invoiceid="'+obj.requestData.invoice_id+'" data-invoicetype="1" data-partnerid="'+obj.partnerid+'"><i class="fas fa-trash"></i></a>';
                                            $('#'+unique_invoice_id2+' td:first').html(obj.requestData.invoice_date+" "+awsLink2);
                                            $('#'+unique_invoice_id2+' td:nth-child(3)').html(db_total_enrolled_student2);
                                            $('#'+unique_invoice_id2+' td:nth-child(4)').html("$"+db_total_amount2+" "+printLink2+" "+draftlink2+" "+dellink2);
                                        }
                                        if(obj.db_total_deposit_amount2){
                                            $('.totDepoAmTillNow').html("$"+obj.db_total_deposit_amount2);
                                        }
                                    } //end function type edit an edit data


									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								} else {
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
								}
                            }
						});
					}

                    else if(formName == 'create_record_invoice')
                    {
                        var partner_id = $('#create_record_invoice input[name="partner_id"]').val();
                        var myform = document.getElementById('create_record_invoice');
                        var fd = new FormData(myform);
                        $.ajax({
                            type:'post',
                            url:$("form[name="+formName+"]").attr('action'),
                            processData: false,
                            contentType: false,
                            data: fd,
                            success: function(response){
                                $('.popuploader').hide();
                                var obj = $.parseJSON(response);
                                $('#createrecordinvoicemodal').modal('hide');
                                if(obj.status){
                                    if(obj.function_type == 'add')
                                    {
                                        if(obj.requestData){
                                            var reqData = obj.requestData;
                                            var awsUrl = obj.awsUrl; //console.log('awsUrl='+awsUrl);
                                            //var printUrl = obj.printUrl; //console.log('printUrl='+printUrl);
                                            var lastInsertedId = obj.lastInsertedId; //console.log('lastInsertedId='+lastInsertedId);
                                            var trRows = "";
                                            $.each(reqData, function(index, subArray) {
                                                if(awsUrl != ""){
                                                    var awsLink = '<a target="_blank" class="link-primary" href="'+awsUrl+'"><i class="fas fa-file-pdf"></i></a>';
                                                } else {
                                                    var awsLink = '';
                                                }
                                                var dellink2 = '<a class="link-primary deletestudentrecordinvoice" href="javascript:;" data-uniqueid="'+subArray.id+'" data-invoicetype="2" data-partnerid="'+subArray.partnerid+'"><i class="fas fa-trash"></i></a>';
                                                trRows += "<tr id='TrRecordRow_"+subArray.id+"'><td>"+subArray.invoice_date+" "+awsLink+"</td><td>"+subArray.sent_date+"</td><td>"+subArray.invoice_no+"</td><td>$"+subArray.amount_aud+" "+dellink2+"</td></tr>";
                                            });
                                        }
                                        //console.log('trRows='+trRows);
                                        $('.lastRow_invoice').before(trRows);
                                    }
                                    if(obj.db_total_deposit_amount){
                                        $('.totDepoAmTillNow_invoice').html("$"+obj.db_total_deposit_amount);
                                    }
                                    $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
                                }else{
                                    $('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
                            }
                        });
                    }

                    else if(formName == 'create_record_payment')
                    {
                        var partner_id = $('#create_record_payment input[name="partner_id"]').val();
                        var myform = document.getElementById('create_record_payment');
                        var fd = new FormData(myform);
                        $.ajax({
                            type:'post',
                            url:$("form[name="+formName+"]").attr('action'),
                            processData: false,
                            contentType: false,
                            data: fd,
                            success: function(response){
                                $('.popuploader').hide();
                                var obj = $.parseJSON(response);
                                $('#createrecordpaymentmodal').modal('hide');
                                if(obj.status){
                                    if(obj.function_type == 'add')
                                    {
                                        if(obj.requestData){
                                            var reqData = obj.requestData;
                                            var awsUrl = obj.awsUrl; //console.log('awsUrl='+awsUrl);
                                            //var printUrl = obj.printUrl; //console.log('printUrl='+printUrl);
                                            var lastInsertedId = obj.lastInsertedId; //console.log('lastInsertedId='+lastInsertedId);
                                            var trRows = "";
                                            $.each(reqData, function(index, subArray) {
                                                if(awsUrl != ""){
                                                    var awsLink = '<a target="_blank" class="link-primary" href="'+awsUrl+'"><i class="fas fa-file-pdf"></i></a>';
                                                } else {
                                                    var awsLink = '';
                                                }
                                                var dellink3 = '<a class="link-primary deletestudentpaymentinvoice" href="javascript:;" data-uniqueid="'+subArray.id+'" data-invoicetype="3" data-partnerid="'+subArray.partnerid+'"><i class="fas fa-trash"></i></a>';
                                                trRows += "<tr id='TrPaymentRow_"+subArray.id+"'><td>"+subArray.invoice_no+" "+awsLink+"</td><td>"+subArray.method_received+"</td><td>"+subArray.verified_by+"</td><td>"+subArray.verified_date+"</td><td>$"+subArray.amount_aud+" "+dellink3+"</td></tr>";
                                            });
                                        }
                                        //console.log('trRows='+trRows);
                                        $('.lastRow_payment').before(trRows);
                                    }

                                    if(obj.db_total_deposit_amount){
                                        $('.totDepoAmTillNow_payment').html("$"+obj.db_total_deposit_amount);
                                        $('#Total_Payment_Received').val("$"+obj.db_total_deposit_amount);
                                    }
                                    $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
                                }else{
                                    $('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
                            }
                        });
                    }

                  
                    
                    
                    else if(formName == 'partner_upload_inbox_mail'){
						var myform = document.getElementById('partner_upload_inbox_mail');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide();
								var obj = $.parseJSON(response);
								$('#partnerUploadAndFetchMail').modal('hide');
                                if(obj.status){
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								} else {
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
                                location.reload();
							}
						});
					}
                    else if(formName == 'partner_upload_sent_mail'){
						var myform = document.getElementById('partner_upload_sent_mail');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide();
								var obj = $.parseJSON(response);
								$('#partnerUploadSentAndFetchMail').modal('hide');
                                if(obj.status){
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								} else {
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
                                location.reload();
							}
						});
					}
                  
                   

                   
					else if(formName == 'tasktermclientform'){
						var client_id = $('#tasktermclientform input[name="partnerid"]').val();
						var myform = document.getElementById('tasktermclientform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('#opentaskmodal').modal('hide');
								if(obj.status){
									$('#create_note').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/admin/partner/get-tasks',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											 $('#my-datatable').DataTable().destroy();
											$('.taskdata').html(responses);
											$('#my-datatable').DataTable({
												"searching": false,
												"lengthChange": false,
											  "columnDefs": [
												{ "sortable": false, "targets": [0, 2, 3] }
											  ],
											  order: [[1, "desc"]] //column indexes is zero based

												
											}).draw();
											
										}
									});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'educationform'){
						var client_id = $('#educationform input[name="client_id"]').val();
						var myform = document.getElementById('educationform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('.create_education').modal('hide');
								if(obj.status){
									$('#create_note').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/admin/get-educations',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											 
											$('.education_list').html(responses);
										}
									});
									/* $.ajax({
										url: site_url+'/admin/get-activities',
										type:'GET',
										datatype:'json',
										data:{id:client_id},
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
									}); */
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'feeform'){
						var product_id = $('#feeform input[name="product_id"]').val();
						var myform = document.getElementById('feeform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('#new_fee_option').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/admin/get-all-fees',
										type:'GET',
										data:{clientid:product_id},
										success: function(responses){
											 $('.popuploader').hide(); 
											$('.feeslist').html(responses);
										}
									});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}

					else if(formName == 'applicationfeeform'){
						
						var myform = document.getElementById('applicationfeeform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
									$('#new_fee_option').modal('hide');
								var obj = $.parseJSON(response);
							
								if(obj.status){
									
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									//$('.product_totalfee').html(obj.totalfee);
									//$('.product_discount').html(obj.discount);
									//var t = parseFloat(obj.totalfee) - parseFloat(obj.discount);
									//$('.product_net_fee').html(t);

									$('.total_course_fee_amount').html(obj.total_course_fee_amount);
									$('.scholarship_fee_amount').html(obj.scholarship_fee_amount);
                                    $('.enrolment_fee_amount').html(obj.enrolment_fee_amount);
                                    $('.material_fees').html(obj.material_fees);
                                    $('.tution_fees').html(obj.tution_fees);

		
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}

					else if(formName == 'applicationfeeformlatest'){
                        var myform = document.getElementById('applicationfeeformlatest');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide();
								$('#new_fee_option_latest').modal('hide');
								var obj = $.parseJSON(response);
                                if(obj.status){
                                    $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
      								$('.fee_reported_by_college').html(obj.totalfee);
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
							}
						});
					}

					else if(formName == 'servicefeeform'){
						
						var myform = document.getElementById('servicefeeform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
									$('#new_fee_option_serv').modal('hide');
									
								var obj = $.parseJSON(response);
							
								if(obj.status){
									$('#interest_service_view').modal('show');
									$(document).on("hidden.bs.modal", "#new_fee_option_serv", function (e) {
										$('body').addClass('modal-open');
									});
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$('.productfeedata .installtype').html(obj.installment_type);
									$('.productfeedata .feedata').html(obj.feedata);
								
									$('.productfeedata .client_dicounts').html(obj.discount);
									var t = parseFloat(obj.totalfee) - parseFloat(obj.discount);
									$('.productfeedata .client_totl').html(t);
									$.ajax({
										url: site_url+'/admin/get-services',
										type:'GET',
										data:{clientid:obj.client_id},
										success: function(responses){
											$('.popuploader').hide(); 
											$('.interest_serv_list').html(responses);
										}
									});
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'setuppaymentschedule'){
						
						var myform = document.getElementById('setuppaymentschedule');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
									$('#create_apppaymentschedule').modal('hide');
								var obj = $.parseJSON(response);
							
								if(obj.status){
									
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$('.openpaymentschedule').hide();
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'editinvpaymentschedule'){
						if($('#editinvpaymentschedule input[name="is_ajax"]').val() == 'true'){
						var myform = document.getElementById('editinvpaymentschedule');
						var appliid = $('#editinvpaymentschedule input[name="id"]').val();
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('#editpaymentschedule').modal('hide');
								var obj = $.parseJSON(response);
								
									if(obj.status){
									
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/admin/get-all-paymentschedules',
										type:'GET',
										data:{appid:obj.application_id, client_id:obj.client_id},
										success: function(responses){
											 $('.popuploader').hide(); 
											$('.showpaymentscheduledata').html(responses);
										}
									});
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});	
						}						
					}else if(formName == 'checklistform'){
						
						var myform = document.getElementById('checklistform');
						var checklist_type = $('#checklist_type').val();
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('#create_checklist').modal('hide');
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
									if(obj.status){
									$('#document_type').val();
									$('#checklistdesc').val();
									$('.due_date_col').hide();
									$('.checklistdue_date').val(0);
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$('.'+checklist_type+'_checklists').html(obj.data);
									$('.checklistcount').html(obj.countchecklist);
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});	
											
					}else if(formName == 'addinvpaymentschedule'){
						
						var myform = document.getElementById('addinvpaymentschedule');
			
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('#addpaymentschedule').modal('hide');
								var obj = $.parseJSON(response);
								
									if(obj.status){
									
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/admin/get-all-paymentschedules',
										type:'GET',
										data:{appid:obj.application_id, client_id:obj.client_id},
										success: function(responses){
											 $('.popuploader').hide(); 
											$('.showpaymentscheduledata').html(responses);
										}
									});
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});	
										
					}else if(formName == 'editfeeform'){
						var product_id = $('#editfeeform input[name="product_id"]').val();
						var myform = document.getElementById('editfeeform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('#editfeeoption').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/admin/get-all-fees',
										type:'GET',
										data:{clientid:product_id},
										success: function(responses){
											 $('.popuploader').hide(); 
											$('.feeslist').html(responses);
										}
									});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'promotionform'){
						var client_id = $('#promotionform input[name="client_id"]').val();
						var myform = document.getElementById('promotionform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
							
								if(obj.status){
									$('#create_promotion').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/admin/get-promotions',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											 
											$('.promotionlists').html(responses);
										}
									});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'editpromotionform'){
						var client_id = $('#editpromotionform input[name="client_id"]').val();
						var myform = document.getElementById('editpromotionform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
							
								if(obj.status){
									$('#edit_promotion').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/admin/get-promotions',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											 
											$('.promotionlists').html(responses);
										}
									});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'editeducationform'){
						var client_id = $('#editeducationform input[name="client_id"]').val();
						var myform = document.getElementById('editeducationform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
							
								if(obj.status){
									$('#edit_education').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/admin/get-educations',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											 
											$('.education_list').html(responses);
										}
									});
									/* $.ajax({
										url: site_url+'/admin/get-activities',
										type:'GET',
										datatype:'json',
										data:{id:client_id},
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
									}); */
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'testscoreform'){
						var client_id = $('#testscoreform input[name="client_id"]').val();
						var myform = document.getElementById('testscoreform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('.edit_english_test').modal('hide');
								if(obj.status){
									
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								$('.tofl_lis').html(obj.toefl_Listening);	
								$('.tofl_reading').html(obj.toefl_Reading);	
								$('.tofl_writing').html(obj.toefl_Writing);	
								$('.tofl_speaking').html(obj.toefl_Speaking);
								$('.tofl_score').html(obj.score_1);	
								$('.toefl_date').html(obj.toefl_Date);
								$('.ilets_Listening').html(obj.ilets_Listening);
								$('.ilets_Reading').html(obj.ilets_Reading);
								$('.ilets_Writing').html(obj.ilets_Writing);
								$('.ilets_speaking').html(obj.ilets_Speaking);	
								$('.ilets_score').html(obj.score_2);
								$('.ilets_date').html(obj.ilets_date);
								$('.pte_Listening').html(obj.pte_Listening);
								$('.pte_Reading').html(obj.pte_Reading);	
								$('.pte_Writing').html(obj.pte_Writing);
								$('.pte_Speaking').html(obj.pte_Speaking);	
								$('.pte_score').html(obj.score_3);
								$('.pte_date').html(obj.pte_Date);
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'saveagreement'){
						
						var myform = document.getElementById('saveagreement');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								if(obj.status){
                                    location.reload();
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'savesubjectarea'){
						
						var myform = document.getElementById('savesubjectarea');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								if(obj.status){
									$('#other_info_add').modal('hide');
									$('.otherinfolist').html(obj.data);
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'editsubjectarea'){
						
						var myform = document.getElementById('editsubjectarea');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								if(obj.status){
									$('#other_info_edit').modal('hide');
									$('.otherinfolist').html(obj.data);
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'othertestform'){
						var client_id = $('#othertestform input[name="client_id"]').val();
						var myform = document.getElementById('othertestform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('.edit_other_test').modal('hide');
								if(obj.status){
									
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								$('.gmat').html(obj.gmat);	
								$('.gre').html(obj.gre);	
								$('.sat_ii').html(obj.sat_ii);	
								$('.sat_i').html(obj.sat_i);
								
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'ajaxinvoicepaymentform'){
							var client_id = $('#ajaxinvoicepaymentform input[name="client_id"]').val();
						var myform = document.getElementById('ajaxinvoicepaymentform');
						var fd = new FormData(myform);	
						
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('#addpaymentmodal').modal('hide');
								if(obj.status){
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									 $.ajax({
										url: site_url+'/admin/get-invoices',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
												 $('.invoicetable').DataTable().destroy();
													$('.invoicedatalist').html(responses);
												$('.invoicetable').DataTable({
													"searching": false,
													"lengthChange": false,
												  "columnDefs": [
													{ "sortable": false, "targets": [0, 2, 3] }
												  ],
												  order: [[1, "desc"]] //column indexes is zero based

													
												}).draw();
											
										}
									}); 
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
					
					}
					
                    else if(formName == 'discontinue_application'){
						var client_id = $('#discontinue_application input[name="client_id"]').val();
						var myform = document.getElementById('discontinue_application');
						var fd = new FormData(myform);
                        $.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide();
								var obj = $.parseJSON(response);
								$('#discon_application').modal('hide');
								if(obj.status){
								    $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$('.ifdiscont').hide();
									$('.revertapp').show();
									$('.applicationstatus').html('Discontinued');

                                    $('#discontinue_reason').css('display','block');
                                    $('#discontinue_note').css('display','block');

                                    $('#discontinue_reason_text').html(obj.discontinue_reason);
                                    $('#discontinue_note_text').html(obj.discontinue_note);
                                } else {
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
							}
						});
                    }

                    else if(formName == 'refund_app')
                    {
                        //var client_id = $('#refund_app input[name="client_id"]').val();
                        var myform = document.getElementById('refund_app');
                        var fd = new FormData(myform);
                        $.ajax({
                            type:'post',
                            url:$("form[name="+formName+"]").attr('action'),
                            processData: false,
                            contentType: false,
                            data: fd,
                            success: function(response){
                                $('.popuploader').hide();
                                var obj = $.parseJSON(response);
                                $('#refund_application').modal('hide');
                                if(obj.status){
                                    $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
                                    $('.ifdiscont').hide();
                                    $('.backstage').hide();
                                    $('.revertapp').hide();
                                    $('.completestage').hide();
                                    $('.nextstage').hide();
                                    $('.revertapp').hide();
                                    $('.applicationstatus').html('Refund');
                                    $('#refund_note').css('display','block');
                                    $('#refund_note_text').html(obj.refund_note);
                                } else {
                                    $('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
                            }
                        });
                    }

					else if(formName == 'revertapplication'){
						var appliid = $('#revertapplication input[name="revapp_id"]').val();	
						var myform = document.getElementById('revertapplication');
						var fd = new FormData(myform);	
						
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('#revert_application').modal('hide');
								if(obj.status){
									
									$.ajax({
										url: site_url+'/admin/get-applications-logs',
										type:'GET',
										data:{id: appliid},
										success: function(responses){
											 
											$('#accordion').html(responses);
										}
									});
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								
								$('.progress-circle span').html(obj.width+' %');
				var over = '';
				if(obj.width > 50){
					over = '50';
				}
				$("#progresscir").removeClass();
				$("#progresscir").addClass('progress-circle');
				$("#progresscir").addClass('prgs_'+obj.width);
				$("#progresscir").addClass('over_'+over); 
									 $('.ifdiscont').show();
									$('.completestage').show();
									 $('.nextstage').hide();
									 $('.revertapp').hide();
									 $('.applicationstatus').html('In Progress');
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
					
					}else if(formName == 'spagent_application'){
							
						var myform = document.getElementById('spagent_application');
						var fd = new FormData(myform);	
						
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('#superagent_application').modal('hide');
								if(obj.status){
									$('#super_agent').val('');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									 
								$('.supagent_data').html(obj.data);	
								}else{
									alert(obj.message);
								}
							}
						});
					
					}else if(formName == 'sbagent_application'){
							
						var myform = document.getElementById('sbagent_application');
						var fd = new FormData(myform);	
						
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('#subagent_application').modal('hide');
								if(obj.status){
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									 
									$('.subagent_data').html(obj.data);	
								}else{
									alert(obj.message);
									
								}
							}
						});
					
					}else if(formName == 'xapplication_ownership'){
							
						var myform = document.getElementById('xapplication_ownership');
						var fd = new FormData(myform);	
						
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('#application_ownership').modal('hide');
								if(obj.status){
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									 
									$('.application_ownership').attr('data-ration',obj.ratio);	
								}else{
									alert(obj.message);
									
								}
							}
						});
					
					}else if(formName == 'saleforcast'){
							
						var myform = document.getElementById('saleforcast');
						var fd = new FormData(myform);	
						
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('#application_opensaleforcast').modal('hide');
								if(obj.status){
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									 $('.opensaleforcast').attr('data-client_revenue',obj.client_revenue);	
									 $('.opensaleforcast').attr('data-partner_revenue',obj.partner_revenue);	
									 $('.opensaleforcast').attr('data-discounts',obj.discounts);	
									 
									$('.appsaleforcast .client_revenue').html(obj.client_revenue);	
									$('.appsaleforcast .partner_revenue').html(obj.partner_revenue);	
									$('.appsaleforcast .discounts').html(obj.discounts);	
									var t = parseFloat(obj.client_revenue) + parseFloat(obj.partner_revenue) - parseFloat(obj.discounts);
									$('.appsaleforcast .netrevenue').html(t);	
									$('.app_sale_forcast').html(t+ 'AUD');	
								}else{
									alert(obj.message);
									
								}
							}
						});
					
					}else if(formName == 'saleforcastservice'){
						var myform = document.getElementById('saleforcastservice');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								
								var obj = $.parseJSON(response);
								$('#application_opensaleforcastservice').modal('hide');
								if(obj.status){
									$('#interest_service_view').modal('show');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									 $('.opensaleforcastservice').attr('data-client_revenue',obj.client_revenue);	
									 $('.opensaleforcastservice').attr('data-partner_revenue',obj.partner_revenue);	
									 $('.opensaleforcastservice').attr('data-discounts',obj.discounts);	
									 
									$('.appsaleforcastserv .client_revenue').html(obj.client_revenue);	
									$('.appsaleforcastserv .partner_revenue').html(obj.partner_revenue);	
									$('.appsaleforcastserv .discounts').html(obj.discounts);	
									var t = parseFloat(obj.client_revenue) + parseFloat(obj.partner_revenue) - parseFloat(obj.discounts);
									$('.appsaleforcastserv .netrevenue').html(t);	
									$.ajax({
										url: site_url+'/admin/get-services',
										type:'GET',
										data:{clientid:obj.client_id},
										success: function(responses){
											$('.popuploader').hide(); 
											$('.interest_serv_list').html(responses);
										}
									});
								}else{
									alert(obj.message);
									
								}
							}
						});
					
					}else if(formName == 'inter_servform'){
						var client_id = $('#inter_servform input[name="client_id"]').val();
						var myform = document.getElementById('inter_servform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
							
								if(obj.status){
									$('.add_interested_service').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/admin/get-services',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											
											$('.interest_serv_list').html(responses);
										}
									});
									$.ajax({
										url: site_url+'/admin/get-activities',
										type:'GET',
										datatype:'json',
										data:{id:client_id},
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
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'inter_servform_serv'){
						var myform = document.getElementById('inter_servform_serv');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('#add_interested_service').modal('hide');
								if(obj.status){
									
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
								$('html, body').animate({scrollTop:0}, 'slow');
							}
						});		
					}else if(formName == 'editinter_servform'){
						var client_id = $('#editinter_servform input[name="client_id"]').val();
						var myform = document.getElementById('editinter_servform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$('#eidt_interested_service').modal('hide');
								if(obj.status){ 
									
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/admin/get-services',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											
											$('.interest_serv_list').html(responses);
										}
									});
									$.ajax({
										url: site_url+'/admin/get-activities',
										type:'GET',
										datatype:'json',
										data:{id:client_id},
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
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'appointform'){
						var client_id = $('#appointform input[name="client_id"]').val();
						 var appoint_date = $('#timeslot_col_date').val(); //alert(appoint_date);
                        var appoint_time = $('#timeslot_col_time').val(); //alert(appoint_time);

						if( appoint_date == "" || appoint_time == ""){
                            $('.popuploader').hide();
                            $('.timeslot_col_date_time').show();
                            return false;
                        } else {
							$('.timeslot_col_date_time').hide();	
							var myform = document.getElementById('appointform');
							var fd = new FormData(myform);	
                            $.ajax({
                                type:'post',
                                url:$("form[name="+formName+"]").attr('action'),
                                processData: false,
                                contentType: false,
                                data: fd,
                                success: function(response){
                                    $('.popuploader').hide(); 
                                    var obj = $.parseJSON(response);

                                    if(obj.status){
                                        /*if(obj.reloadpage){
                                            location.reload();
                                        }*/
                                        $('.add_appointment').modal('hide');
                                        $('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
                                        $.ajax({
                                            url: site_url+'/admin/get-appointments',
                                            type:'GET',
                                            data:{clientid:client_id},
                                            success: function(responses){

                                                $('.appointmentlist').html(responses);
                                            }
                                        });
                                        $.ajax({
                                            url: site_url+'/admin/get-activities',
                                            type:'GET',
                                            datatype:'json',
                                            data:{id:client_id},
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
									}else{
										$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
									}
								}
							});	
					  	}
					}

					else if(formName == 'partnerappointform'){
						var client_id = $('#partnerappointform input[name="client_id"]').val();
						var myform = document.getElementById('partnerappointform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('#create_appoint').modal('hide');
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/admin/partner/get-appointments',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											
											$('.appointmentlist').html(responses);
										}
									});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}
					else if(formName == 'applipaidserviceform'){
						var client_id = $('#appliappointform input[name="client_id"]').val();
						var noteid = $('#appliappointform input[name="noteid"]').val();
						var myform = document.getElementById('appliappointform');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('.add_appointment').modal('hide');
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/admin/get-appointments',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											
											$('.appointmentlist').html(responses);
										}
									});
									
									$.ajax({
										url: site_url+'/admin/get-applications-logs',
										type:'GET',
										data:{id: noteid},
										success: function(responses){
											 
											$('#accordion').html(responses);
										}
									});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}

                    else if(formName == 'appliassignform'){
						var client_id = $('#appliassignform input[name="client_id"]').val();
						var noteid = $('#appliassignform input[name="noteid"]').val();
						var myform = document.getElementById('appliassignform');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide();
								var obj = $.parseJSON(response);
                                if(obj.success){
									$('#create_applicationaction').modal('hide');
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');

									if(obj.application_id){
                                        $.ajax({
                                            url: site_url+'/admin/get-applications-logs',
                                            type:'GET',
                                            data:{id: obj.application_id},
                                            success: function(responses){
                                                $('#accordion').html(responses);
                                            }
                                        });
                                    }
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
							}
						});
					}

                    else if(formName == 'partnerassignform'){
						var partner_id = $('#partnerassignform input[name="partner_id"]').val();
						var noteid = $('#partnerassignform input[name="noteid"]').val();
						var myform = document.getElementById('partnerassignform');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide();
								var obj = $.parseJSON(response);
                                if(obj.success){
									$('#create_partneraction').modal('hide');
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
                                    $.ajax({
                                        url: site_url+'/admin/get-partner-activities',
                                        type:'GET',
                                        datatype:'json',
                                        data:{partner_id:obj.partner_id},
                                        success: function(responses){
                                            var ress = JSON.parse(responses);
                                            var html = '';
                                            $.each(ress.data, function(k, v) {
                                                html += '<div class="activity"><div class="activity-icon bg-primary text-white"><span>'+v.createdname+'</span></div><div class="activity-detail"><div class="mb-2"><span class="text-job">'+v.date+'</span></div><p><b>'+v.name+'</b> '+v.subject+'</p>';
                                                if(v.message != null){
                                                    html += '<p>'+v.message+'</p>';
                                                }
                                                if(v.followup_date != null){
                                                    html += '<p>'+v.followup_date+'</p>';
                                                }
                                                if(v.task_group != null){
                                                    html += '<p>'+v.task_group+'</p>';
                                                }
                                                html += '</div></div>';
                                            });
                                            $('.activities').html(html);
                                        }
                                    });
                                }else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
							}
						});
					}

                    else if(formName == 'appkicationsendmail'){
						var client_id = $('#appkicationsendmail input[name="client_id"]').val();
						var noteid = $('#appkicationsendmail input[name="noteid"]').val();
						var myform = document.getElementById('appkicationsendmail');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('#applicationemailmodal').modal('hide');
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/admin/get-appointments',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											
											$('.appointmentlist').html(responses);
										}
									});
									
									$.ajax({
										url: site_url+'/admin/get-applications-logs',
										type:'GET',
										data:{id: noteid},
										success: function(responses){
											 
											$('#accordion').html(responses);
										}
									});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}
                 
                    else if(formName == 'sendmsg'){
                        var client_id = $('#sendmsg input[name="client_id"]').val();
						var myform = document.getElementById('sendmsg');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide();
								var obj = $.parseJSON(response);
                                if(obj.status){
									$('#sendmsgmodal').modal('hide');
                                    $.ajax({
                                        url: site_url+'/admin/get-notes',
                                        type:'GET',
                                        data:{clientid:client_id,type:'client'},
                                        success: function(responses){
                                            $('.note_term_list').html(responses);
                                        }
                                    });
                                    $.ajax({
                                        url: site_url+'/admin/get-activities',
                                        type:'GET',
                                        datatype:'json',
                                        data:{id:client_id},
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
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								} else {
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
                                }
							}
						});
					}
					

					else if(formName == 'editappointment'){
						var client_id = $('#editappointment input[name="client_id"]').val();
						var myform = document.getElementById('editappointment');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('#edit_appointment').modal('hide');
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/admin/get-appointments',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											
											$('.appointmentlist').html(responses);
										}
									});
									$.ajax({
										url: site_url+'/admin/get-activities',
										type:'GET',
										datatype:'json',
										data:{id:client_id},
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
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'editpartnerappointment'){
						var client_id = $('#editpartnerappointment input[name="client_id"]').val();
						var myform = document.getElementById('editpartnerappointment');
						var fd = new FormData(myform);	
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('#edit_appointment').modal('hide');
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$.ajax({
										url: site_url+'/admin/partner/get-appointments',
										type:'GET',
										data:{clientid:client_id},
										success: function(responses){
											
											$('.appointmentlist').html(responses);
										}
									});
									
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});		
					}else if(formName == 'notetermform')
					{
						
						var client_id = $('input[name="client_id"]').val(); 	
						var myform = document.getElementById('notetermform');
						syncEditorContent($(myform));
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
									$('#create_note').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								$.ajax({
		url: site_url+'/admin/get-notes',
		type:'GET',
		data:{clientid:client_id,type:'client'},
		success: function(responses){
			
			$('.note_term_list').html(responses);
		}
	});
									$.ajax({
										url: site_url+'/admin/get-activities',
										type:'GET',
										datatype:'json',
										data:{id:client_id},
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
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
					}
					else if(formName == 'notetermform_n')
					{
						
						var client_id = $('input[name="client_id"]').val(); 	
						var myform = document.getElementById('notetermform_n');
						syncEditorContent($(myform));
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
								    $('#create_note_d input[name="title"]').val('');
								    $('#create_note_d input[name="title"]').val('');
					$("#create_note_d .summernote-simple").val('');
				$('#create_note_d input[name="noteid"]').val('');                    
			// TinyMCE replacement for summernote
			if (typeof TinyMCEHelpers !== 'undefined') {
				TinyMCEHelpers.setContent('#create_note_d .summernote-simple', '');
			} else if (typeof tinymce !== 'undefined') {
				var editor = tinymce.get($("#create_note_d .summernote-simple").attr('id') || 'create_note_d');
				if (editor) {
					editor.setContent('');
				}
			}
									$('#create_note_d').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								$.ajax({
		url: site_url+'/admin/get-notes',
		type:'GET',
		data:{clientid:client_id,type:'client'},
		success: function(responses){
			
			$('.note_term_list').html(responses);
		}
	});
									$.ajax({
										url: site_url+'/admin/get-activities',
										type:'GET',
										datatype:'json',
										data:{id:client_id},
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
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
					}
					else if(formName == 'addtoapplicationform'){
						var myform = document.getElementById('addtoapplicationform');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								
								if(obj.status){
$('#add_application').modal('hide');
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								}else{
									$('.custom-error-popupmsg').html('<span  class="alert alert-danger">'+obj.message+'</span>');
								}
							}
						});
					}

					else if(formName == 'alldocs_upload_form'){
						var client_id = $('#alldocs_upload_form input[name="client_id"]').val();
						var myform = document.getElementById('alldocs_upload_form');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							//datatype:'json',
							success: function(response){
								$('.popuploader').hide();
								var obj = $.parseJSON(response);
								$('#openalldocsmodal').modal('hide');
								if(obj.status){
									$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
									$('.alldocumnetlist').html(obj.data);
									$('.allgriddata').html(obj.griddata);
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
								}
								//getallactivities();
							}
						});
					}

					else if(formName == 'applicationform')
					{   
						var client_id = $('input[name="client_id"]').val(); 
						var myform = document.getElementById('addapplicationformform');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('.popuploader').hide(); 
								var obj = $.parseJSON(response);
								$(".add_appliation #workflow").val('').trigger('change');
			$(".add_appliation #partner").val('').trigger('change');
			$(".add_appliation #product").val('').trigger('change');
								if(obj.status){
									$('.add_appliation').modal('hide');
								$('.custom-error-msg').html('<span class="alert alert-success">'+obj.message+'</span>');
								$.ajax({
					url: site_url+'/admin/get-application-lists',
					type:'GET',
					datatype:'json',
					data:{id:client_id},
					success: function(responses){
						$('.applicationtdata').html(responses);
					}
				});
									$.ajax({
					url: site_url+'/admin/get-activities',
					type:'GET',
					datatype:'json',
					data:{id:client_id},
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
								}else{
									$('.custom-error-msg').html('<span class="alert alert-danger">'+obj.message+'</span>');
									
								}
							}
						});
						
						
					}
				else if(formName == 'submit-review')
					{
						$("form[name=submit-review] :input[data-max]").each(function(){
							var data_max  = $(this).attr('data-max');
							var value = $.trim($(this).val());	
							if(parseInt(value) > parseInt(data_max))	
								{
									$(this).after(errorDisplay(maxError + data_max)); 
									$("#loader").hide();
									return false;	
								}
							else
								{
									$("form[name="+formName+"]").submit();
									return true;
								}	
						});	
					}
				else
					{	
						if(formName == 'invoiceform')
						{
							$('input[name="btn"]').val(savetype);
						}
						$("form[name="+formName+"]").submit();
						return true;	
					} 
			}	
		
	}	
	

function customInvoiceValidate(formName, savetype)
	{
		$("#loader").show(); //all form submit
		
		var i = 0;	
		$(".custom-error").remove(); //remove all errors when submit the button
		$("#save_type").val(savetype);
		$("form[name="+formName+"] :input[data-valid]").each(function(){
			var dataValidation = $(this).attr('data-valid');
			var splitDataValidation = dataValidation.split(' ');
			
			var j = 0; //for serial wise errors shown	
			if($.inArray("required", splitDataValidation) !== -1) //for required
				{
					var for_class = $(this).attr('class');	
					if(for_class.indexOf('multiselect_subject') != -1)
						{
							var value = $.trim($(this).val());	
							if (value.length === 0) 
								{
									i++;
									j++;
									$(this).parent().after(errorDisplay(requiredError)); 
								}	
						} 
					else 
						{
							var fieldValue = getFieldValue($(this));
							if( !fieldValue ) 
								{
									i++;
									j++;
									$(this).after(errorDisplay(requiredError));  
								}
						}
				}
			if(j <= 0)
				{
					var fieldValue = getFieldValue($(this));
					
					if($.inArray("email", splitDataValidation) !== -1) //for email
						{
							if(!validateEmail(fieldValue)) 
								{
									i++;
									$(this).after(errorDisplay(emailError));  
								}
						}
						
							
					var forMin = splitDataValidation.find(a =>a.includes("min"));
					if(typeof forMin != 'undefined')
						{
							var breakMin = forMin.split('-');
							var digit = breakMin[1];

							var value = fieldValue.length;
							if(value < digit) 
								{
									i++;
									$(this).after(errorDisplay(min+' '+digit+' character.'));  
								}	
						}
						
					var forMax = splitDataValidation.find(a =>a.includes("max"));
					if(typeof forMax != 'undefined')
						{
							var breakMax = forMax.split('-');
							var digit = breakMax[1];

							var value = fieldValue.length;
							if(value > digit) 
								{
									i++;
									$(this).after(errorDisplay(max+' '+digit+' character.'));  
								}	
						}
						
					var forEqual = splitDataValidation.find(a =>a.includes("equal"));
					if(typeof forEqual != 'undefined')
						{
							var breakEqual = forEqual.split('-');
							var digit = breakEqual[1];

							var value = (fieldValue.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-')).length;
							if(value != digit) 
								{
									i++;
									$(this).after(errorDisplay(equal+' '+digit+' character.'));  
								}	
						}
				}			
		});
		
		if(i > 0)
			{
				if(formName == 'add-query'){
					$('html, body').animate({scrollTop:$("#row_scroll"). offset(). top}, 'slow');
				}else if(formName != 'upload-answer')	{
					$('html, body').animate({scrollTop:0}, 'slow');
				}
				$("#loader").hide();
				return false;
			}	
		else
			{
				if(formName == 'add-query')
					{
						$('#preloader').show();
						$('#preloader div').show();
						var myform = document.getElementById('enquiryco');
						var fd = new FormData(myform);
						$.ajax({
							type:'post',
							url:$("form[name="+formName+"]").attr('action'),
							processData: false,
							contentType: false,
							data: fd,
							success: function(response){
								$('#preloader').hide();
								$('#preloader div').hide();
								var obj = $.parseJSON(response);
								if(obj.success){
									window.location = redirecturl;
								}else{
									$('.customerror').html(obj.message);
									$('html, body').animate({scrollTop:$("#row_scroll"). offset(). top}, 'slow');
								}
							}
						});
					}
				else
					{	
				
						$("form[name="+formName+"]").submit();
						return true;	
					} 
			}	
		
	}
	
function errorDisplay(error) {
	return "<span class='custom-error' role='alert'>"+error+"</span>";
}

function validateEmail(sEmail) {
    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (filter.test(sEmail)) {
		return true;
	}
    else {
		return false;
    }
}