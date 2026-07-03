/**
 * Admin Client Detail - Document context menu (right-click)
 */
'use strict';

(function() {
    function toastMsg(message, type) {
        if (typeof window.toastMsg === 'function') {
            window.toastMsg(message, type);
        } else if (message) {
            alert(message);
        }
    }

    // Create context menu element
    let contextMenu = null;
    let currentDocumentRow = null;

    /** Display file name + timestamp + ext; falls back to storage key when no display name (see buildClientDocumentDownloadName in download-and-chatgpt.js). */
    function resolveContextMenuDownloadFilename(fileName, fileType, storageKeyFallback) {
        if (fileName) {
            if (typeof window.buildClientDocumentDownloadName === 'function') {
                return window.buildClientDocumentDownloadName(fileName, fileType || '');
            }
            return fileName + (fileType ? '.' + fileType : '');
        }
        return storageKeyFallback || 'download';
    }

    function parsePreviewFileOnclick(onclickAttr) {
        if (!onclickAttr || onclickAttr.indexOf('previewFile') === -1) {
            return null;
        }
        const match = onclickAttr.match(
            /previewFile\s*\(\s*'((?:\\'|[^'])*)'\s*,\s*'((?:\\'|[^'])*)'\s*,\s*'((?:\\'|[^'])*)'\s*\)/
        );
        if (!match) {
            return null;
        }
        return {
            fileType: match[1],
            fileUrl: match[2].replace(/\\'/g, "'"),
            containerClass: match[3]
        };
    }

    /** Same preview URL as the inline file link (onclick / rename cache / row data). */
    function resolvePreviewUrlFromRow(row) {
        if (!row) {
            return '';
        }
        const anchor = row.querySelector('.doc-row a[onclick*="previewFile"]');
        if (anchor) {
            const parsed = parsePreviewFileOnclick(anchor.getAttribute('onclick') || '');
            if (parsed && parsed.fileUrl) {
                return parsed.fileUrl;
            }
        }
        if (typeof jQuery !== 'undefined') {
            const stored = jQuery(row).find('.doc-row').data('preview-file-url');
            if (stored) {
                return stored;
            }
        }
        const myfile = row.getAttribute('data-myfile') || '';
        if (!myfile) {
            return '';
        }
        if (myfile.startsWith('http://') || myfile.startsWith('https://')) {
            return myfile;
        }
        if (myfile.startsWith('/')) {
            return window.location.origin + myfile;
        }
        return window.location.origin + '/' + myfile;
    }

    function resolveContextMenuPreviewFilename(fileName, fileType, storageKeyFallback) {
        if (fileName) {
            return fileName + (fileType ? '.' + fileType : '');
        }
        if (storageKeyFallback && storageKeyFallback.indexOf('.') !== -1) {
            return storageKeyFallback;
        }
        return 'preview.pdf';
    }

    function getPreviewViewerBaseUrl() {
        return (typeof App !== 'undefined' && App.getUrl && App.getUrl('documentPreviewView'))
            || (typeof App !== 'undefined' && App.getUrl && App.getUrl('siteUrl')
                ? App.getUrl('siteUrl') + '/document-preview-view'
                : window.location.origin + '/document-preview-view');
    }

    function buildPreviewViewerUrl(fileUrl, previewFilename, fileType) {
        const baseUrl = getPreviewViewerBaseUrl();
        const separator = baseUrl.indexOf('?') !== -1 ? '&' : '?';
        return baseUrl + separator +
            'filelink=' + encodeURIComponent(fileUrl) +
            '&filename=' + encodeURIComponent(previewFilename) +
            '&filetype=' + encodeURIComponent(fileType || '');
    }

    /** Open server-rendered preview viewer in a new tab (real URL — avoids pop-up blocker). */
    function openPreviewViewerTab(fileUrl, previewFilename, fileType) {
        const viewerUrl = buildPreviewViewerUrl(fileUrl, previewFilename, fileType);
        const win = window.open(viewerUrl, '_blank', 'noopener,noreferrer');
        if (!win) {
            toastMsg('Please allow pop-ups to preview this file.', 'error');
        }
    }

    function openPreviewInNewTab(row, fileName, fileType, myfile, myfileKey, docType) {
        const previewFilename = resolveContextMenuPreviewFilename(fileName, fileType, myfileKey || myfile);
        let fileUrl = resolvePreviewUrlFromRow(row);

        if (!fileUrl && myfile && (myfile.startsWith('http://') || myfile.startsWith('https://'))) {
            fileUrl = myfile;
        }

        if (!fileUrl) {
            const awsBucket = window.awsBucket || '';
            const awsRegion = window.awsRegion || '';
            const entityId = window.PageConfig?.clientId || window.PageConfig?.partnerId || '';

            if (awsBucket && awsRegion && entityId && myfile) {
                fileUrl = 'https://' + awsBucket + '.s3.' + awsRegion + '.amazonaws.com/' +
                    entityId + '/' + docType + '/' + myfile;
            }
        }

        if (!fileUrl) {
            console.error('Missing AWS configuration or file data for preview');
            toastMsg('Unable to preview file. Missing configuration.', 'error');
            return;
        }

        openPreviewViewerTab(fileUrl, previewFilename, fileType);
    }

    function createContextMenu() {
        if (contextMenu) return contextMenu;

        contextMenu = document.createElement('ul');
        contextMenu.className = 'document-context-menu';
        contextMenu.id = 'documentContextMenu';
        document.body.appendChild(contextMenu);
        return contextMenu;
    }

    function showContextMenu(event, row) {
        event.preventDefault();
        event.stopPropagation();

        currentDocumentRow = row;
        const menu = createContextMenu();

        // Get document data from row
        const docId = row.getAttribute('data-doc-id');
        const checklistName = row.getAttribute('data-checklist-name') || '';
        const fileName = row.getAttribute('data-file-name') || '';
        const fileType = row.getAttribute('data-file-type') || '';
        const myfile = row.getAttribute('data-myfile') || '';
        const myfileKey = row.getAttribute('data-myfile-key') || '';
        const docType = row.getAttribute('data-doc-type') || '';
        const userRole = parseInt(row.getAttribute('data-user-role') || '0');

        // Clear existing menu items
        menu.innerHTML = '';

        // Build menu items
        if (checklistName) {
            menu.appendChild(createMenuItem('Rename Checklist', function() {
                const row = document.querySelector(`.alldocumnetlist .drow[data-doc-id="${docId}"]`);
                if (row && typeof window.DocumentRename !== 'undefined') {
                    const parent = $(row).find('.personalchecklist-row');
                    if (parent.length) {
                        window.DocumentRename.enterChecklistEditMode(parent);
                    }
                }
                hideContextMenu();
            }));
        }

        if (fileName) {
            menu.appendChild(createMenuItem('Rename File Name', function() {
                const row = document.querySelector(`.alldocumnetlist .drow[data-doc-id="${docId}"]`);
                if (row && typeof window.DocumentRename !== 'undefined') {
                    const parent = $(row).find('.doc-row');
                    if (parent.length) {
                        window.DocumentRename.enterFileEditMode(parent);
                    }
                }
                hideContextMenu();
            }));
        }

        menu.appendChild(createDivider());

        // Send for signature (only for documents with a file, and no placement/flow yet)
        const sigStatus = row.getAttribute('data-signature-status') || '';
        if (fileName && !['signature_placed', 'sent', 'viewed', 'signed'].includes(sigStatus)) {
            menu.appendChild(createMenuItem('Send for signature', function() {
                if (typeof window.DocumentSignatureFlow !== 'undefined') {
                    window.DocumentSignatureFlow.openPlacementModal(docId, checklistName, fileName, fileType, myfile, myfileKey, docType);
                }
                hideContextMenu();
            }));
        }

        menu.appendChild(createDivider());

        // Preview
        menu.appendChild(createMenuItem('Preview', function() {
            openPreviewInNewTab(currentDocumentRow, fileName, fileType, myfile, myfileKey, docType);
            hideContextMenu();
        }));

        // Download
        menu.appendChild(createMenuItem('Download', function() {
            if (myfile && (myfile.startsWith('http://') || myfile.startsWith('https://'))) {
                // Cross-origin (e.g. S3): browser ignores <a download>; use app route so server sends Content-Disposition: attachment
                const isS3OrCrossOrigin = (myfile.indexOf('s3.') !== -1 || myfile.indexOf('amazonaws.com') !== -1) ||
                    (myfile.indexOf(window.location.origin) !== 0);
                const downloadFilename = resolveContextMenuDownloadFilename(fileName, fileType, myfileKey);
                if (isS3OrCrossOrigin) {
                    const baseUrl = (typeof App !== 'undefined' && App.getUrl && App.getUrl('downloadDocument')) || (typeof App !== 'undefined' && App.getUrl && App.getUrl('siteUrl') ? App.getUrl('siteUrl') + '/download-document' : window.location.origin + '/download-document');
                    const downloadUrl = baseUrl + (baseUrl.indexOf('?') !== -1 ? '&' : '?') + 'filelink=' + encodeURIComponent(myfile) + '&filename=' + encodeURIComponent(downloadFilename);
                    window.open(downloadUrl, '_blank', 'noopener,noreferrer');
                } else {
                    // Same-origin: <a download> is honored
                    const a = document.createElement('a');
                    a.href = myfile;
                    a.download = downloadFilename;
                    a.target = '_blank';
                    a.style.display = 'none';
                    document.body.appendChild(a);
                    a.click();
                    setTimeout(function() { if (a.parentNode) document.body.removeChild(a); }, 100);
                }
            } else if (myfileKey) {
                // New file upload - try to find download control on the row (matches on .download-file, not storage key)
                const downloadFilename = resolveContextMenuDownloadFilename(fileName, fileType, myfileKey);
                const downloadEl = currentDocumentRow && currentDocumentRow.querySelector
                    ? currentDocumentRow.querySelector('a.download-file')
                    : null;
                if (downloadEl) {
                    downloadEl.click();
                } else {
                    // Create and trigger download via form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = (App.getUrl('downloadDocument') || App.getUrl('siteUrl') + '/download-document');
                    form.target = '_blank';
                    form.innerHTML = `
                        <input type="hidden" name="_token" value="${App.getCsrf()}">
                        <input type="hidden" name="filelink" value="${myfile}">
                        <input type="hidden" name="filename" value="${downloadFilename}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                    setTimeout(() => form.remove(), 100);
                }
            } else {
                // Old file upload - S3 URL (key is clientId/docType/filename)
                const url = 'https://' + (window.awsBucket || '') + '.s3.' + (window.awsRegion || '') + '.amazonaws.com/';
                const clientId = window.PageConfig?.clientId || '';
                const fileUrl = url + clientId + '/' + docType + '/' + myfile;
                const downloadEl = document.querySelector(`.download-file[data-filelink*="${myfile}"]`);
                if (downloadEl) {
                    downloadEl.click();
                } else {
                    // S3 is cross-origin; use app route so server sends Content-Disposition: attachment
                    const baseUrl = (typeof App !== 'undefined' && App.getUrl && App.getUrl('downloadDocument')) || (typeof App !== 'undefined' && App.getUrl && App.getUrl('siteUrl') ? App.getUrl('siteUrl') + '/download-document' : window.location.origin + '/download-document');
                    const fallbackName = myfile || 'download';
                    const downloadFilename = resolveContextMenuDownloadFilename(fileName, fileType, fallbackName);
                    const downloadUrl = baseUrl + (baseUrl.indexOf('?') !== -1 ? '&' : '?') + 'filelink=' + encodeURIComponent(fileUrl) + '&filename=' + encodeURIComponent(downloadFilename);
                    window.open(downloadUrl, '_blank', 'noopener,noreferrer');
                }
            }
            hideContextMenu();
        }));

        menu.appendChild(createDivider());

        // Move to Category (only when DocumentCategoryManager is active)
        if (typeof window.DocumentCategoryManager !== 'undefined' && window.DocumentCategoryManager.categories && window.DocumentCategoryManager.categories.length > 1) {
            const currentCategoryId = window.DocumentCategoryManager.currentCategoryId;
            const otherCategories = window.DocumentCategoryManager.categories.filter(function(c) {
                return parseInt(c.id, 10) !== parseInt(currentCategoryId, 10);
            });
            if (otherCategories.length > 0) {
                menu.appendChild(createMenuItem('Move to Category', function() {
                    showMoveToCategoryModal(docId, currentCategoryId, otherCategories);
                    hideContextMenu();
                }));
            }
        }

        // Verify
        menu.appendChild(createMenuItem('Verify', function() {
            const tempEl = document.createElement('a');
            tempEl.className = 'dropdown-item verifydoc';
            tempEl.setAttribute('data-id', docId);
            tempEl.setAttribute('data-href', 'verifydoc');
            tempEl.setAttribute('data-doctype', docType || 'documents');
            tempEl.style.display = 'none';
            document.body.appendChild(tempEl);
            $(tempEl).trigger('click');
            setTimeout(() => { if (tempEl.parentNode) tempEl.parentNode.removeChild(tempEl); }, 100);
            hideContextMenu();
        }));

        // Delete (only for super admin)
        if (userRole === 1) {
            menu.appendChild(createMenuItem('Delete', function() {
                const tempEl = document.createElement('a');
                tempEl.className = 'dropdown-item deletenote';
                tempEl.setAttribute('data-id', docId);
                tempEl.setAttribute('data-href', 'deletealldocs');
                tempEl.style.display = 'none';
                document.body.appendChild(tempEl);
                $(tempEl).trigger('click');
                setTimeout(() => { if (tempEl.parentNode) tempEl.parentNode.removeChild(tempEl); }, 100);
                hideContextMenu();
            }));
        }

        // Not Used
        menu.appendChild(createMenuItem('Not Used', function() {
            // Create a temporary element to trigger the existing handler
            const tempEl = document.createElement('a');
            tempEl.className = 'dropdown-item notuseddoc';
            tempEl.setAttribute('data-id', docId);
            tempEl.setAttribute('data-href', 'notuseddoc');
            tempEl.setAttribute('data-doctype', docType || 'documents');
            tempEl.style.display = 'none';
            document.body.appendChild(tempEl);

            // Trigger click to use existing handler
            $(tempEl).trigger('click');

            // Clean up
            setTimeout(() => {
                if (tempEl.parentNode) {
                    tempEl.parentNode.removeChild(tempEl);
                }
            }, 100);

            hideContextMenu();
        }));

        // Show menu off-screen first to measure its real rendered dimensions
        menu.style.left = '-9999px';
        menu.style.top = '-9999px';
        menu.classList.add('show');

        // Now use actual rendered size for boundary checks (viewport-relative, matches position:fixed)
        const menuWidth = menu.offsetWidth;
        const menuHeight = menu.offsetHeight;
        const vpWidth = window.innerWidth;
        const vpHeight = window.innerHeight;
        let left = event.clientX;
        let top = event.clientY;
        if (left + menuWidth > vpWidth) left = vpWidth - menuWidth - 8;
        if (top + menuHeight > vpHeight) top = vpHeight - menuHeight - 8;
        if (left < 0) left = 8;
        if (top < 0) top = 8;
        menu.style.left = left + 'px';
        menu.style.top = top + 'px';

        // Hide menu on outside click
        setTimeout(() => {
            document.addEventListener('click', hideContextMenu, { once: true });
            document.addEventListener('contextmenu', hideContextMenu, { once: true });
        }, 0);
    }

    function hideContextMenu() {
        if (contextMenu) {
            contextMenu.classList.remove('show');
        }
        currentDocumentRow = null;
    }

    function createMenuItem(text, onClick) {
        const li = document.createElement('li');
        const a = document.createElement('a');
        a.textContent = text;
        a.href = 'javascript:;';
        a.addEventListener('click', onClick);
        li.appendChild(a);
        return li;
    }

    function createDivider() {
        const li = document.createElement('li');
        li.className = 'divider';
        return li;
    }

    function showMoveToCategoryModal(docId, currentCategoryId, categories) {
        const clientId = window.DocumentCategoryManager.currentClientId;
        const options = '<option value="">— Select a category —</option>' + categories.map(function(c) {
            return '<option value="' + c.id + '">' + (c.name || '') + (c.document_count > 0 ? ' (' + c.document_count + ')' : '') + '</option>';
        }).join('');

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Move to Category',
                html: '<div class="text-start mb-3">' +
                    '<label for="move-category-select" class="form-label" style="display:block;margin-bottom:8px;">Select target category:</label>' +
                    '<select id="move-category-select" class="form-select form-control" style="width:100%;padding:8px 12px;font-size:14px;">' + options + '</select>' +
                    '</div>',
                showCancelButton: true,
                confirmButtonText: 'Move',
                cancelButtonText: 'Cancel',
                customClass: { popup: 'swal2-popup-move-category' },
                didOpen: function() {
                    var selectEl = document.getElementById('move-category-select');
                    if (selectEl) {
                        selectEl.focus();
                    }
                },
                preConfirm: function() {
                    var selectEl = document.getElementById('move-category-select');
                    var val = selectEl ? selectEl.value : '';
                    if (!val) {
                        Swal.showValidationMessage('Please select a category');
                        return false;
                    }
                    return val;
                }
            }).then(function(result) {
                if (result.isConfirmed && result.value) {
                    const categoryId = result.value;
                    const baseUrl = (typeof App !== 'undefined' && App.getUrl && App.getUrl('siteUrl')) || '';
                    jQuery.ajax({
                        url: (baseUrl || '') + '/document-categories/move-document',
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': (typeof App !== 'undefined' && App.getCsrf && App.getCsrf()) || (function(){ var m=document.querySelector('meta[name="csrf-token"]'); return m?m.getAttribute('content'):''; })() },
                        data: {
                            doc_id: docId,
                            category_id: categoryId,
                            client_id: clientId
                        },
                        dataType: 'json'
                    }).done(function(res) {
                        if (res.status) {
                            Swal.fire('Success!', res.message, 'success');
                            if (window.DocumentCategoryManager) {
                                window.DocumentCategoryManager.loadCategoryDocuments(window.DocumentCategoryManager.currentCategoryId);
                                window.DocumentCategoryManager.loadCategories(true);
                            }
                        } else {
                            Swal.fire('Error!', res.message || 'Failed to move document', 'error');
                        }
                    }).fail(function(xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to move document';
                        Swal.fire('Error!', msg, 'error');
                    });
                }
            });
        } else {
            showMoveToCategoryBootstrapModal(docId, clientId, categories);
        }
    }

    function showMoveToCategoryBootstrapModal(docId, clientId, categories) {
        var optionsHtml = '<option value="">— Select a category —</option>' + categories.map(function(c) {
            return '<option value="' + c.id + '">' + (c.name || '') + (c.document_count > 0 ? ' (' + c.document_count + ')' : '') + '</option>';
        }).join('');

        var modalId = 'moveToCategoryModal';
        var existing = document.getElementById(modalId);
        if (existing) existing.remove();

        var modalHtml = '<div class="modal fade" id="' + modalId + '" tabindex="-1" role="dialog">' +
            '<div class="modal-dialog" role="document">' +
            '<div class="modal-content">' +
            '<div class="modal-header">' +
            '<h5 class="modal-title">Move to Category</h5>' +
            '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
            '</div>' +
            '<div class="modal-body text-start">' +
            '<label for="move-category-select-modal" class="form-label">Select target category:</label>' +
            '<select id="move-category-select-modal" class="form-select form-control" style="width:100%;">' + optionsHtml + '</select>' +
            '<div class="text-danger mt-2 move-category-error" style="display:none;">Please select a category</div>' +
            '</div>' +
            '<div class="modal-footer">' +
            '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>' +
            '<button type="button" class="btn btn-primary move-category-confirm-btn">Move</button>' +
            '</div>' +
            '</div></div></div>';

        var wrap = document.createElement('div');
        wrap.innerHTML = modalHtml;
        var modalEl = wrap.firstElementChild;
        document.body.appendChild(modalEl);

        var modal = new bootstrap.Modal(modalEl);
        modal.show();

        function doMove(categoryId) {
            if (!categoryId) {
                modalEl.querySelector('.move-category-error').style.display = 'block';
                return;
            }
            modalEl.querySelector('.move-category-error').style.display = 'none';
            var baseUrl = (typeof App !== 'undefined' && App.getUrl && App.getUrl('siteUrl')) || '';
            var csrf = (typeof App !== 'undefined' && App.getCsrf && App.getCsrf()) || (function(){ var m=document.querySelector('meta[name="csrf-token"]'); return m?m.getAttribute('content'):''; })();
            jQuery.ajax({
                url: (baseUrl || '') + '/document-categories/move-document',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf },
                data: { doc_id: docId, category_id: categoryId, client_id: clientId },
                dataType: 'json'
            }).done(function(res) {
                modal.hide();
                modalEl.remove();
                if (res.status) {
                    toastMsg(res.message || 'Document moved successfully.', 'success');
                    if (window.DocumentCategoryManager) {
                        window.DocumentCategoryManager.loadCategoryDocuments(window.DocumentCategoryManager.currentCategoryId);
                        window.DocumentCategoryManager.loadCategories(true);
                    }
                } else {
                    toastMsg(res.message || 'Failed to move document', 'error');
                }
            }).fail(function(xhr) {
                toastMsg((xhr.responseJSON && xhr.responseJSON.message) || 'Failed to move document', 'error');
            });
        }

        modalEl.querySelector('.move-category-confirm-btn').addEventListener('click', function() {
            var sel = document.getElementById('move-category-select-modal');
            doMove(sel ? sel.value : '');
        });

        modalEl.addEventListener('hidden.bs.modal', function() {
            modalEl.remove();
        });
    }

    // Attach context menu to document rows
    document.addEventListener('DOMContentLoaded', function() {
        console.log('[Context Menu] DOMContentLoaded fired');
        console.log('[Context Menu] Looking for .document-row elements:', document.querySelectorAll('.document-row').length);

        // Handle right-click on document rows - works on entire row
        // Use capture phase to catch events before they bubble
        document.addEventListener('contextmenu', function(e) {
            console.log('[Context Menu] Context menu event fired on:', e.target);

            // Check if click is on a document row or any element inside it
            const row = e.target.closest('.document-row');
            console.log('[Context Menu] Found row:', row);

            if (row) {
                // Check if the click is on an interactive element that should have its own behavior
                const isInteractiveElement = e.target.closest('a[href]:not([href^="javascript:"]), button:not([type="button"]), input, textarea, select, [contenteditable="true"]');

                console.log('[Context Menu] Is interactive element:', isInteractiveElement);

                // If it's not an interactive element, show our context menu
                if (!isInteractiveElement) {
                    // Prevent default browser context menu
                    e.preventDefault();
                    e.stopPropagation();

                    console.log('[Context Menu] Showing context menu');

                    // Show our custom context menu
                    showContextMenu(e, row);
                    return false;
                }
            }
        }, true); // Use capture phase to catch events early

        // Also handle dynamically added rows
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) {
                        // Check if the node itself is a document row
                        if (node.classList && node.classList.contains('document-row')) {
                            // Row will work through event delegation
                            node.style.cursor = 'context-menu';
                        }
                        // Check if any child is a document row
                        const childRows = node.querySelectorAll ? node.querySelectorAll('.document-row') : [];
                        childRows.forEach(function(row) {
                            // Ensure row has proper styling for context menu
                            row.style.cursor = 'context-menu';
                            // Also set cursor on all cells
                            const cells = row.querySelectorAll('td');
                            cells.forEach(function(cell) {
                                cell.style.cursor = 'context-menu';
                            });
                        });
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        // Ensure existing rows have proper cursor style
        function styleDocumentRows() {
            document.querySelectorAll('.document-row').forEach(function(row) {
                row.style.cursor = 'context-menu';
                // Also set cursor on all cells
                const cells = row.querySelectorAll('td');
                cells.forEach(function(cell) {
                    cell.style.cursor = 'context-menu';
                });
            });
        }

        // Style existing rows
        styleDocumentRows();

        // Also style after a short delay to catch any rows added during page load
        setTimeout(styleDocumentRows, 500);

        console.log('[Context Menu] Initialized for document rows');
    });
})();
