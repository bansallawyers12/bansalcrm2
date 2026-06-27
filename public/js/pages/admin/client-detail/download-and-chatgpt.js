/**
 * Admin Client Detail - Download + ChatGPT handlers
 */
'use strict';

/**
 * Matches ClientDocumentController::buildClientDocumentDownloadFilename — display name + local timestamp + extension.
 * @param {string} fileName
 * @param {string} fileType
 * @returns {string}
 */
function buildClientDocumentDownloadName(fileName, fileType) {
    var ext = (fileType || 'bin').replace(/[^a-zA-Z0-9]/g, '');
    if (!ext) {
        ext = 'bin';
    }
    var d = new Date();
    var ts = d.getFullYear() +
        String(d.getMonth() + 1).padStart(2, '0') +
        String(d.getDate()).padStart(2, '0') + '_' +
        String(d.getHours()).padStart(2, '0') +
        String(d.getMinutes()).padStart(2, '0') +
        String(d.getSeconds()).padStart(2, '0');
    var base = (fileName || 'document').replace(/[<>:"|?*\/\\]/g, '_').replace(/"/g, '').trim();
    if (!base) {
        base = 'document';
    }
    return base + '_' + ts + '.' + ext;
}

window.buildClientDocumentDownloadName = buildClientDocumentDownloadName;

(function() {
    // Download document handler
    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('click', function (e) {
            // Check if the clicked element has the class `.download-file`
            const target = e.target.closest('a.download-file');

            // If it's not a .download-file anchor, do nothing
            if (!target) return;

            const filelink = target.getAttribute('data-filelink') || '';
            const dlBase = target.getAttribute('data-dl-base');
            const dlExt = target.getAttribute('data-dl-ext');
            const href = target.getAttribute('href') || '';

            // Prefer fresh timestamp when we have structured display name + extension (Documents tab)
            if (filelink && dlBase !== null && dlExt !== null && typeof window.buildClientDocumentDownloadName === 'function') {
                e.preventDefault();
                const filename = window.buildClientDocumentDownloadName(dlBase, dlExt);
                const baseUrl = (typeof App !== 'undefined' && App.getUrl && App.getUrl('downloadDocument')) || (typeof App !== 'undefined' && App.getUrl && App.getUrl('siteUrl') ? App.getUrl('siteUrl') + '/download-document' : window.location.origin + '/download-document');
                const sep = baseUrl.indexOf('?') !== -1 ? '&' : '?';
                const downloadUrl = baseUrl + sep + 'filelink=' + encodeURIComponent(filelink) + '&filename=' + encodeURIComponent(filename);
                window.open(downloadUrl, '_blank', 'noopener,noreferrer');
                return;
            }

            // If the link already points to a download URL with params, let the browser handle it
            if (href && href !== '#' && href.includes('/download-document') && href.includes('filelink=')) {
                return;
            }

            e.preventDefault();

            const filename = target.dataset.filename;

            if (!filelink || !filename) {
                alert('Missing file info.');
                return;
            }

            // Create and submit a hidden form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = App.getUrl('downloadDocument') || App.getUrl('siteUrl') + '/download-document';
            form.target = '_blank';

            // CSRF token
            const token = App.getCsrf();
            form.innerHTML = `
                <input type="hidden" name="_token" value="${token}">
                <input type="hidden" name="filelink" value="${filelink}">
                <input type="hidden" name="filename" value="${filename}">
            `;

            document.body.appendChild(form);
            form.submit();
            form.remove();
        });
    });

    // Floating Enhance button in message box – enhances current message content
    const composeMessageEnhanceBtn = document.getElementById('composeMessageEnhanceBtn');
    if (composeMessageEnhanceBtn) {
        composeMessageEnhanceBtn.addEventListener('click', function() {
            var currentContent = '';
            if ($("#emailmodal .tinymce-simple").length && typeof TinyMCEHelpers !== 'undefined') {
                currentContent = TinyMCEHelpers.getContentBySelector("#emailmodal .tinymce-simple") || '';
            } else if (typeof tinymce !== 'undefined') {
                var editor = tinymce.get('compose_email_message');
                if (editor) {
                    currentContent = editor.getContent() || '';
                }
            }
            if (!currentContent) {
                var msgEl = document.getElementById('compose_email_message');
                currentContent = (msgEl && msgEl.value) ? msgEl.value : '';
            }
            // Strip HTML tags for API (plain text enhance)
            var textForApi = currentContent.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
            if (!textForApi) {
                alert('Please enter some message content to enhance.');
                return;
            }

            composeMessageEnhanceBtn.disabled = true;
            composeMessageEnhanceBtn.innerHTML = crmIconSpinner(' Enhancing...');

            var csrfToken = (typeof App !== 'undefined' && typeof App.getCsrf === 'function')
                ? App.getCsrf()
                : ($('meta[name="csrf-token"]').attr('content') || '');
            var enhanceUrl = (typeof App !== 'undefined' && typeof App.getUrl === 'function' && App.getUrl('mailEnhance'))
                ? App.getUrl('mailEnhance')
                : ((typeof App !== 'undefined' && typeof App.getUrl === 'function' ? App.getUrl('siteUrl') : '') || '') + '/mail/enhance';

            fetch(enhanceUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({ message: textForApi })
            })
            .then(function(response) {
                return response.json().then(function(data) {
                    if (!response.ok) {
                        throw new Error(data.error || data.message || ('Request failed (' + response.status + ')'));
                    }
                    return data;
                });
            })
            .then(function(data) {
                if (data.enhanced_message) {
                    // Replace message body with enhanced content (preserve as HTML with line breaks)
                    var enhancedHtml = data.enhanced_message.replace(/\n/g, '<br>');
                    if ($("#emailmodal .tinymce-simple").length && typeof TinyMCEHelpers !== 'undefined') {
                        TinyMCEHelpers.setContentBySelector("#emailmodal .tinymce-simple", enhancedHtml);
                    } else if (typeof tinymce !== 'undefined') {
                        var ed = tinymce.get('compose_email_message');
                        if (ed) {
                            ed.setContent(enhancedHtml);
                        }
                    } else {
                        var msgEl = document.getElementById('compose_email_message');
                        if (msgEl) msgEl.value = data.enhanced_message;
                    }
                } else {
                    alert(data.error || 'Failed to enhance message.');
                }
            })
            .catch(function(error) {
                console.error('Enhance error:', error);
                alert(error.message || 'An error occurred while enhancing the message.');
            })
            .finally(function() {
                composeMessageEnhanceBtn.disabled = false;
                composeMessageEnhanceBtn.innerHTML = crmIcon('magic') + ' Enhance';
            });
        });
    }
})();
