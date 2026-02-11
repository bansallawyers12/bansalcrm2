/**
 * Admin Client Detail - Document context menu (right-click)
 */
'use strict';

(function() {
    // Create context menu element
    let contextMenu = null;
    let currentDocumentRow = null;

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
                // Find the row and trigger rename directly
                const row = document.querySelector(`.alldocumnetlist .drow[data-doc-id="${docId}"]`);
                if (row) {
                    const parent = $(row).find('.personalchecklist-row');
                    if (parent.length) {
                        parent.data('current-html', parent.html());
                        const opentime = parent.data('personalchecklistname');
                        parent.empty().append(
                            $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
                            $('<button class="btn btn-personalprimary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
                            $('<button class="btn btn-personaldanger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
                        );
                    }
                }
                hideContextMenu();
            }));
        }

        if (fileName) {
            menu.appendChild(createMenuItem('Rename File Name', function() {
                // Find the row and trigger rename directly
                const row = document.querySelector(`.alldocumnetlist .drow[data-doc-id="${docId}"]`);
                if (row) {
                    const parent = $(row).find('.doc-row');
                    if (parent.length) {
                        parent.data('current-html', parent.html());
                        const opentime = parent.data('name');
                        parent.empty().append(
                            $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
                            $('<button class="btn btn-primary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
                            $('<button class="btn btn-danger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
                        );
                    }
                }
                hideContextMenu();
            }));
        }

        menu.appendChild(createDivider());

        // Preview
        menu.appendChild(createMenuItem('Preview', function() {
            // Full URL (e.g. public path or S3) - open directly
            if (myfile && (myfile.startsWith('http://') || myfile.startsWith('https://'))) {
                window.open(myfile, '_blank');
            } else if (myfileKey) {
                // New file upload - open in new tab
                let fileUrl = myfile;
                if (!fileUrl.startsWith('http://') && !fileUrl.startsWith('https://')) {
                    if (!fileUrl.startsWith('/')) {
                        fileUrl = '/' + fileUrl;
                    }
                    fileUrl = window.location.origin + fileUrl;
                }
                window.open(fileUrl, '_blank');
            } else {
                // Old file upload - construct AWS S3 URL and open in new tab
                const awsBucket = window.awsBucket || '';
                const awsRegion = window.awsRegion || '';
                const clientId = window.PageConfig?.clientId || window.PageConfig?.partnerId || '';

                if (awsBucket && awsRegion && clientId && myfile) {
                    const fileUrl = `https://${awsBucket}.s3.${awsRegion}.amazonaws.com/${clientId}/${docType}/${myfile}`;
                    window.open(fileUrl, '_blank');
                } else {
                    console.error('Missing AWS configuration or file data for preview');
                    alert('Unable to preview file. Missing configuration.');
                }
            }
            hideContextMenu();
        }));

        // Download
        menu.appendChild(createMenuItem('Download', function() {
            if (myfile && (myfile.startsWith('http://') || myfile.startsWith('https://'))) {
                // Full URL (e.g. public path) - direct download
                const a = document.createElement('a');
                a.href = myfile;
                a.download = fileName + (fileType ? '.' + fileType : '') || 'download';
                a.target = '_blank';
                a.style.display = 'none';
                document.body.appendChild(a);
                a.click();
                setTimeout(() => { if (a.parentNode) document.body.removeChild(a); }, 100);
            } else if (myfileKey) {
                // New file upload - try to find download element first
                const downloadEl = document.querySelector(`.download-file[data-filelink][data-filename="${myfileKey}"]`);
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
                        <input type="hidden" name="filename" value="${myfileKey}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                    setTimeout(() => form.remove(), 100);
                }
            } else {
                // Old file upload - S3 URL
                const url = 'https://' + (window.awsBucket || '') + '.s3.' + (window.awsRegion || '') + '.amazonaws.com/';
                const clientId = window.PageConfig?.clientId || '';
                const fileUrl = url + clientId + '/' + docType + '/' + myfile;
                const downloadEl = document.querySelector(`.download-file[data-filelink*="${myfile}"]`);
                if (downloadEl) {
                    downloadEl.click();
                } else {
                    const a = document.createElement('a');
                    a.href = fileUrl;
                    a.download = fileName + '.' + fileType;
                    a.style.display = 'none';
                    document.body.appendChild(a);
                    a.click();
                    setTimeout(() => document.body.removeChild(a), 100);
                }
            }
            hideContextMenu();
        }));

        menu.appendChild(createDivider());

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

        // Position menu
        menu.style.left = event.pageX + 'px';
        menu.style.top = event.pageY + 'px';
        menu.classList.add('show');

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
