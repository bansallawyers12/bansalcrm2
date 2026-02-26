/**
 * Admin Client Detail - Download + ChatGPT handlers
 */
'use strict';

(function() {
    // Download document handler
    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('click', function (e) {
            // Check if the clicked element has the class `.download-file`
            const target = e.target.closest('a.download-file');

            // If it's not a .download-file anchor, do nothing
            if (!target) return;

            // If the link already points to a download URL with params, let the browser handle it
            const href = target.getAttribute('href') || '';
            if (href && href !== '#' && href.includes('/download-document') && href.includes('filelink=')) {
                return;
            }

            e.preventDefault();

            const filelink = target.dataset.filelink;
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
            } else {
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
            composeMessageEnhanceBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enhancing...';

            var enhanceUrl = App.getUrl('mailEnhance') || App.getUrl('siteUrl') + '/mail/enhance';
            fetch(enhanceUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": App.getCsrf()
                },
                body: JSON.stringify({ message: textForApi })
            })
            .then(response => response.json())
            .then(data => {
                if (data.enhanced_message) {
                    // Replace message body with enhanced content (preserve as HTML with line breaks)
                    var enhancedHtml = data.enhanced_message.replace(/\n/g, '<br>');
                    if ($("#emailmodal .tinymce-simple").length && typeof TinyMCEHelpers !== 'undefined') {
                        TinyMCEHelpers.setContentBySelector("#emailmodal .tinymce-simple", enhancedHtml);
                    } else {
                        var msgEl = document.getElementById('compose_email_message');
                        if (msgEl) msgEl.value = data.enhanced_message;
                    }
                } else {
                    alert(data.error || 'Failed to enhance message.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while enhancing the message.');
            })
            .finally(function() {
                composeMessageEnhanceBtn.disabled = false;
                composeMessageEnhanceBtn.innerHTML = '<i class="fas fa-magic"></i> Enhance';
            });
        });
    }
})();
