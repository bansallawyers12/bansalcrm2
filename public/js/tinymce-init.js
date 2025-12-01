/**
 * TinyMCE Initialization Script
 * Replaces Summernote and CKEditor
 */

(function() {
    'use strict';

    // Wait for TinyMCE to load
    function initTinyMCE() {
        if (typeof tinymce === 'undefined') {
            setTimeout(initTinyMCE, 100);
            return;
        }

        // Ensure all summernote-simple elements have IDs
        $('.summernote-simple, .tinymce-simple').each(function() {
            if (!$(this).attr('id')) {
                $(this).attr('id', 'tinymce_' + Math.random().toString(36).substr(2, 9));
            }
        });

        // Simple mode configuration (replaces Summernote simple)
        // Used with class: "summernote-simple" or "tinymce-simple"
        tinymce.init({
            selector: '.summernote-simple, .tinymce-simple',
            license_key: 'gpl',
            apiKey: 'hb79upb7jkaf2aid0a2roy4l51kl8kae9k5rn2wxwtl0jry9',
            height: 150,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
            ],
            toolbar: 'bold italic underline strikethrough | bullist numlist | link | removeformat',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            branding: false,
            promotion: false,
            setup: function(editor) {
                // Ensure editor is ready
                editor.on('init', function() {
                    // Editor initialized
                });
            }
        });

        // Ensure all summernote elements have IDs
        $('.summernote, .tinymce-full').each(function() {
            if (!$(this).attr('id')) {
                $(this).attr('id', 'tinymce_' + Math.random().toString(36).substr(2, 9));
            }
        });

        // Full mode configuration (replaces Summernote full and CKEditor)
        // Used with class: "summernote" or "tinymce-full"
        tinymce.init({
            selector: '.summernote, .tinymce-full',
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
            promotion: false
        });

        // CKEditor replacement - for editor1 (used in custom-popover.js)
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
            promotion: false
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTinyMCE);
    } else {
        initTinyMCE();
    }

    // Helper functions to replace Summernote/CKEditor methods
    window.TinyMCEHelpers = {
        // Get content from editor (replaces summernote('code') and CKEDITOR.instances['editor1'].getData())
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

        // Set content in editor (replaces summernote('code', content) and CKEDITOR.instances['editor1'].setData())
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

        // Reset editor (replaces summernote('reset'))
        reset: function(selector) {
            this.setContent(selector, '');
        },

        // Insert HTML (replaces CKEDITOR.instances.editor1.insertHtml())
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

