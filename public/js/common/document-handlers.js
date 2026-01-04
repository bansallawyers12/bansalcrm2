/**
 * Document Handlers Module
 * 
 * Functions for handling file uploads and document management
 * 
 * Usage:
 *   file_explorer()
 *   uploadFormData(formData)
 *   previewFile(fileType, fileUrl, containerClass)
 */

'use strict';

/**
 * Trigger file explorer dialog
 * Sets up file selection handler
 */
function file_explorer() {
    const selectfile = document.getElementById("selectfile");
    
    if (!selectfile) {
        console.warn("selectfile element not found");
        return;
    }
    
    selectfile.click();
    
    selectfile.onchange = function() {
        var files = selectfile.files;
        if (!files || files.length === 0) {
            return;
        }
        
        var formData = new FormData();
        
        for (var i = 0; i < files.length; i++) {
            formData.append("file[]", files[i]);
        }
        
        // Add form fields from config or DOM
        var type = $('.checklisttype').val();
        var typename = $('.checklisttypename').val();
        var id = $('.checklistid').val();
        var applicationId = $('.application_id').val();
        var clientId = $('.app_doc_client_id').val();
        
        if (type) formData.append("type", type);
        if (typename) formData.append("typename", typename);
        if (id) formData.append("id", id);
        if (applicationId) formData.append("application_id", applicationId);
        if (clientId) formData.append("client_id", clientId);
        
        uploadFormData(formData);
    };
}

/**
 * Upload form data (files + form fields)
 * @param {FormData} formData - FormData object with files and fields
 */
function uploadFormData(formData) {
    $('.popuploader').show();
    
    var url = App.getUrl('checklistUpload');
    if (!url) {
        console.error('checklistUpload URL not configured');
        $('.popuploader').hide();
        return;
    }
    
    $.ajax({
        url: url,
        method: "POST",
        data: formData,
        datatype: 'json',
        contentType: false,
        cache: false,
        processData: false,
        headers: {
            'X-CSRF-TOKEN': App.getCsrf()
        },
        success: function(response) {
            var obj = typeof response === 'string' ? $.parseJSON(response) : response;
            $('.popuploader').hide();
            $('#openfileuploadmodal').modal('hide');
            $('.mychecklistdocdata').html(obj.doclistdata || '');
            $('.checklistuploadcount').html(obj.applicationuploadcount || '');
            
            if (obj.type && obj.checklistdata) {
                $('.' + obj.type + '_checklists').html(obj.checklistdata);
            }
            
            if ($('#selectfile').length) {
                $('#selectfile').val('');
            }
            
            // Refresh application logs if application_id exists
            if (obj.application_id) {
                var logsUrl = App.getUrl('getApplicationsLogs') || App.getUrl('siteUrl') + '/get-applications-logs';
                if (logsUrl) {
                    $.ajax({
                        url: logsUrl,
                        type: 'GET',
                        data: { id: obj.application_id },
                        success: function(responses) {
                            $('#accordion').html(responses);
                        }
                    });
                }
            }
        },
        error: function() {
            $('.popuploader').hide();
            alert('Error uploading file. Please try again.');
        }
    });
}

/**
 * Preview file in container
 * @param {string} fileType - File type/extension (jpg, pdf, doc, etc.)
 * @param {string} fileUrl - URL to the file
 * @param {string} containerClass - CSS class of container element
 */
function previewFile(fileType, fileUrl, containerClass) {
    const container = document.querySelector(`.${containerClass}`);

    if (!container) {
        console.error('Preview container not found');
        return;
    }

    // Clear existing content
    container.innerHTML = '';

    switch (fileType.toLowerCase()) {
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
            const img = document.createElement('img');
            img.src = fileUrl;
            img.className = 'preview-image';
            container.appendChild(img);
            break;

        case 'pdf':
            const iframe = document.createElement('iframe');
            iframe.src = `https://docs.google.com/viewer?url=${encodeURIComponent(fileUrl)}&embedded=true`;
            iframe.className = 'pdf-viewer';
            container.appendChild(iframe);
            break;

        case 'doc':
        case 'docx':
        case 'xls':
        case 'xlsx':
        case 'ppt':
        case 'pptx':
            const officeViewer = document.createElement('iframe');
            officeViewer.src = `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(fileUrl)}`;
            officeViewer.className = 'doc-viewer';
            container.appendChild(officeViewer);
            break;

        default:
            container.innerHTML = `
                <div class="preview-placeholder">
                    <i class="fas fa-exclamation-circle fa-3x mb-3 text-warning"></i>
                    <p>Preview not available for this file type.</p>
                </div>
            `;
    }
}

// Export functions for use in other modules
if (typeof window !== 'undefined') {
    window.file_explorer = file_explorer;
    window.uploadFormData = uploadFormData;
    window.previewFile = previewFile;
}

// ============================================================================
// DOCUMENT UPLOAD BUTTON HANDLERS
// ============================================================================
// Trigger file input when "Add Document" button is clicked in upload_client_receipt_document containers
// This handles: Client Receipt, Student Invoice, Record Invoice, Record Payment documents
$(document).on('click', '.upload_client_receipt_document .btn-primary, .upload_client_receipt_document .btn-outline-primary', function(e) {
    e.preventDefault();
    var fileInput = $(this).closest('.upload_client_receipt_document').find('.docclientreceiptupload');
    if (fileInput.length) {
        fileInput.click();
    }
});

