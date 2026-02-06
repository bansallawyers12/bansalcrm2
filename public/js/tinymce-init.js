/**
 * TinyMCE Initialization Script
 * Single rich-text editor; use .tinymce-simple (compact) or .tinymce-full (full toolbar).
 * Image upload: actual file upload to server (not URL-only).
 */

(function() {
    'use strict';

    function getImageUploadHandler() {
        return function(blobInfo, progress) {
            return new Promise(function(resolve, reject) {
                var url = typeof window.TINYMCE_UPLOAD_URL !== 'undefined' ? window.TINYMCE_UPLOAD_URL : '/tinymce/upload-image';
                var token = typeof window.TINYMCE_CSRF_TOKEN !== 'undefined' ? window.TINYMCE_CSRF_TOKEN : (document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                var xhr = new XMLHttpRequest();
                var formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                if (token) formData.append('_token', token);
                xhr.open('POST', url);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                if (token) xhr.setRequestHeader('X-CSRF-TOKEN', token);
                xhr.onload = function() {
                    if (xhr.status < 200 || xhr.status >= 300) {
                        reject('Upload failed: ' + (xhr.status ? xhr.statusText : 'Network error'));
                        return;
                    }
                    try {
                        var json = JSON.parse(xhr.responseText);
                        if (json.location) resolve(json.location);
                        else reject(json.message || 'Upload failed');
                    } catch (e) {
                        reject('Invalid server response');
                    }
                };
                xhr.onerror = function() { reject('Upload failed'); };
                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable && typeof progress === 'function') progress(e.loaded / e.total * 100);
                };
                xhr.send(formData);
            });
        };
    }

    // Wait for TinyMCE to load
    function initTinyMCE() {
        if (typeof tinymce === 'undefined') {
            setTimeout(initTinyMCE, 100);
            return;
        }

        // Ensure all tinymce-simple elements have IDs
        $('.tinymce-simple').each(function() {
            if (!$(this).attr('id')) {
                $(this).attr('id', 'tinymce_' + Math.random().toString(36).substr(2, 9));
            }
        });

        // Simple mode: compact toolbar (e.g. email signature, short notes)
        tinymce.init({
            selector: '.tinymce-simple',
            license_key: 'gpl',
            apiKey: 'hb79upb7jkaf2aid0a2roy4l51kl8kae9k5rn2wxwtl0jry9',
            height: 150,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
            ],
            toolbar: 'bold italic underline strikethrough | bullist numlist | link image | removeformat',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            branding: false,
            promotion: false,
            browser_spellcheck: true,
            images_upload_handler: getImageUploadHandler(),
            setup: function(editor) {
                editor.on('init', function() {});
                editor.on('change', function() { editor.save(); });
                editor.on('blur', function() { editor.save(); });
            }
        });

        // Ensure all tinymce-full elements have IDs
        $('.tinymce-full').each(function() {
            if (!$(this).attr('id')) {
                $(this).attr('id', 'tinymce_' + Math.random().toString(36).substr(2, 9));
            }
        });

        // Full mode: full toolbar (e.g. email body, descriptions)
        tinymce.init({
            selector: '.tinymce-full',
            license_key: 'gpl',
            apiKey: 'hb79upb7jkaf2aid0a2roy4l51kl8kae9k5rn2wxwtl0jry9',
            height: 250,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount',
                'emoticons', 'directionality', 'pagebreak', 'nonbreaking', 'save'
            ],
            toolbar: 'undo redo | formatselect | ' +
                'bold italic underline strikethrough | forecolor backcolor | ' +
                'alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist | outdent indent | ' +
                'removeformat | link image media table | code preview fullscreen | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            branding: false,
            promotion: false,
            browser_spellcheck: true,
            images_upload_handler: getImageUploadHandler(),
            setup: function(editor) {
                editor.on('change', function() { editor.save(); });
                editor.on('blur', function() { editor.save(); });
            }
        });

        // Dedicated #editor1 instance (e.g. custom-popover.js)
        tinymce.init({
            selector: '#editor1',
            license_key: 'gpl',
            apiKey: 'hb79upb7jkaf2aid0a2roy4l51kl8kae9k5rn2wxwtl0jry9',
            height: 400,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount',
                'emoticons', 'directionality', 'pagebreak', 'nonbreaking', 'save'
            ],
            toolbar: 'undo redo | formatselect | ' +
                'bold italic underline strikethrough | forecolor backcolor | ' +
                'alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist | outdent indent | ' +
                'removeformat | link image media table | code preview fullscreen | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            branding: false,
            promotion: false,
            browser_spellcheck: true,
            images_upload_handler: getImageUploadHandler()
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTinyMCE);
    } else {
        initTinyMCE();
    }

    // TinyMCE helper API (use by ID selector e.g. '#email_signature', or use *BySelector for CSS selectors)
    window.TinyMCEHelpers = {
        getContent: function(selector) {
            if (typeof tinymce !== 'undefined') {
                var editor = tinymce.get(selector);
                if (editor) {
                    return editor.getContent();
                }
                // Try by ID if selector doesn't work
                if (selector.startsWith('#')) {
                    editor = tinymce.get(selector.substring(1));
                    if (editor) {
                        return editor.getContent();
                    }
                }
            }
            // Fallback to textarea value
            var $el = $(selector);
            if ($el.length) {
                return $el.val();
            }
            return '';
        },

        setContent: function(selector, content) {
            if (typeof tinymce !== 'undefined') {
                var editor = tinymce.get(selector);
                if (editor) {
                    editor.setContent(content || '');
                    return;
                }
                // Try by ID
                if (selector.startsWith('#')) {
                    editor = tinymce.get(selector.substring(1));
                    if (editor) {
                        editor.setContent(content || '');
                        return;
                    }
                }
            }
            // Fallback to textarea value
            var $el = $(selector);
            if ($el.length) {
                $el.val(content || '');
            }
        },

        reset: function(selector) {
            this.setContent(selector, '');
        },

        // Resolve CSS selector to editor by ID, then get/set (e.g. "#emailmodal .tinymce-simple")
        getContentBySelector: function(selector) {
            var $el = $(selector).first();
            if (!$el.length) return '';
            var id = $el.attr('id');
            if (id) return this.getContent('#' + id);
            return $el.val() || '';
        },
        setContentBySelector: function(selector, content) {
            var $el = $(selector).first();
            if (!$el.length) return;
            var id = $el.attr('id');
            if (id) this.setContent('#' + id, content);
            $el.val(content || '');
        },
        resetBySelector: function(selector) {
            this.setContentBySelector(selector, '');
        },

        insertHtml: function(selector, html) {
            if (typeof tinymce !== 'undefined') {
                var editor = tinymce.get(selector);
                if (editor) {
                    editor.insertContent(html);
                    return;
                }
                // Try by ID
                if (selector.startsWith('#')) {
                    editor = tinymce.get(selector.substring(1));
                    if (editor) {
                        editor.insertContent(html);
                        return;
                    }
                }
            }
        }
    };

})();

