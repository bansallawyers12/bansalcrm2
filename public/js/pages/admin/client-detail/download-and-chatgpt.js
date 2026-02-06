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

    // ChatGPT handlers
    const chatGptToggle = document.getElementById('chatGptToggle');
    if (chatGptToggle) {
        chatGptToggle.addEventListener('click', function() {
            const section = document.getElementById('chatGptSection');
            if (section) {
                section.classList.toggle('collapse');
            }
        });
    }

    const chatGptClose = document.getElementById('chatGptClose');
    if (chatGptClose) {
        chatGptClose.addEventListener('click', function() {
            const section = document.getElementById('chatGptSection');
            if (section) {
                section.classList.add('collapse');
            }
        });
    }

    const enhanceMessageBtn = document.getElementById('enhanceMessageBtn');
    if (enhanceMessageBtn) {
        enhanceMessageBtn.addEventListener('click', function() {
            const chatGptInput = document.getElementById('chatGptInput');
            if (!chatGptInput || !chatGptInput.value) {
                alert('Please enter a message to enhance.');
                return;
            }

            var enhanceUrl = App.getUrl('mailEnhance') || App.getUrl('siteUrl') + '/mail/enhance';

            fetch(enhanceUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": App.getCsrf()
                },
                body: JSON.stringify({ message: chatGptInput.value })
            })
            .then(response => response.json())
            .then(data => {
                if (data.enhanced_message) {
                    // Split the enhanced message into lines
                    const lines = data.enhanced_message.split('\n').filter(line => line.trim() !== '');

                    // First line is the subject
                    const subject = lines[0] || '';

                    // Remaining lines are the body
                    const body = lines.slice(1).join('\n') || '';

                    // Update the subject and message fields
                    const composeEmailSubject = document.getElementById('compose_email_subject');
                    if (composeEmailSubject) {
                        composeEmailSubject.value = subject;
                    }
                    // Ensure Summernote is initialized before updating content
                    if ($("#emailmodal .tinymce-simple").length && typeof TinyMCEHelpers !== 'undefined') {
                        TinyMCEHelpers.setContentBySelector("#emailmodal .tinymce-simple", body);
                    }

                    // Close the ChatGPT section
                    const chatGptSection = document.getElementById('chatGptSection');
                    if (chatGptSection) {
                        chatGptSection.classList.add('collapse');
                    }
                } else {
                    alert(data.error || 'Failed to enhance message.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while enhancing the message.');
            });
        });
    }
})();
