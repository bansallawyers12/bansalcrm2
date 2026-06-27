/**
 * Client detail: scroll document preview into view after opening.
 * Loaded from client-detail-entry.js only (was inline in detail.blade.php).
 */
'use strict';

(function () {
    var _previewFile = window.previewFile;
    if (typeof _previewFile === 'function') {
        window.previewFile = function (fileType, fileUrl, containerClass) {
            _previewFile(fileType, fileUrl, containerClass);
            var container = document.querySelector('.' + containerClass);
            if (container) {
                container.scrollIntoView({ behavior: 'smooth', block: 'start' });
                var iframe = container.querySelector('.pdf-viewer, .doc-viewer');
                if (iframe) {
                    iframe.style.height = '75vh';
                    iframe.style.minHeight = '500px';
                }
            }
        };
    }
})();
