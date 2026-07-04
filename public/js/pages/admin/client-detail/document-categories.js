/**
 * Document Category Management for Client Detail Page
 * Handles category tabs, creation, and switching
 */

(function() {
    'use strict';

    function toastMsg(message, type) {
        if (typeof window.toastMsg === 'function') {
            window.toastMsg(message, type);
        } else if (message) {
            alert(message);
        }
    }

    // Global state
    window.DocumentCategoryManager = {
        currentClientId: null,
        currentCategoryId: null,
        categories: [],
        viewMode: 'list',
        initialized: false,
        _eventsBound: false,
        
        /**
         * Initialize the category system
         */
        init: function(clientId) {
            this.currentClientId = clientId;

            if (!this._eventsBound) {
                this.bindEvents();
                this._eventsBound = true;
            }

            if (!this.initialized) {
                this.viewMode = 'list';
                this.applyViewMode();
            }

            const preserveCategory = this.initialized && !!this.currentCategoryId;
            this.loadCategories(preserveCategory);
            this.initialized = true;
        },

        /**
         * Apply list or grid view within the Documents tab only
         */
        applyViewMode: function() {
            const $root = $('#alldocuments');
            if (!$root.length) {
                return;
            }

            const $list = $root.find('.list_data').first();
            const $grid = $root.find('.allgriddata').first();
            const $icons = $root.find('.document_layout_type a');

            if (this.viewMode === 'grid') {
                $list.hide();
                $grid.show();
                $icons.removeClass('active');
                $root.find('.document_layout_type a.grid').addClass('active');
            } else {
                $grid.hide();
                $list.css('display', 'inline-block');
                $icons.removeClass('active');
                $root.find('.document_layout_type a.list').addClass('active');
            }
        },

        setViewMode: function(mode) {
            if (mode !== 'list' && mode !== 'grid') {
                return;
            }
            this.viewMode = mode;
            this.applyViewMode();
        },
        
        /**
         * Load categories for the current client
         * @param {boolean} preserveCurrentCategory - If true, keeps current category active; if false, switches to first category
         */
        loadCategories: function(preserveCurrentCategory = false) {
            const self = this;
            
            $.ajax({
                url: '/document-categories/get',
                method: 'GET',
                data: { client_id: this.currentClientId },
                success: function(response) {
                    if (response.status) {
                        self.categories = response.categories;
                        self.renderCategoryTabs();
                        
                        // Only switch to first category if NOT preserving current category
                        if (!preserveCurrentCategory) {
                            // Load the first category (General) by default on initial load
                            if (self.categories.length > 0) {
                                self.switchCategory(self.categories[0].id);
                            }
                        } else {
                            // Preserve current category - just update the active state
                            if (self.currentCategoryId) {
                                // Check if current category still exists
                                const categoryExists = self.categories.find(cat => cat.id === self.currentCategoryId);
                                if (categoryExists) {
                                    // Re-apply active state to current category tab
                                    $('.doc-category-tab').removeClass('active btn-primary').addClass('btn-outline-primary');
                                    $(`.doc-category-tab[data-category-id="${self.currentCategoryId}"]`).removeClass('btn-outline-primary').addClass('btn-primary active');
                                } else {
                                    // Current category was deleted, switch to first category
                                    if (self.categories.length > 0) {
                                        self.switchCategory(self.categories[0].id);
                                    }
                                }
                            }
                        }
                    }
                },
                error: function(xhr) {
                    console.error('Error loading categories:', xhr);
                }
            });
        },
        
        /**
         * Render category tabs
         */
        renderCategoryTabs: function() {
            const tabsContainer = $('#document-category-tabs');
            if (!tabsContainer.length) return;
            
            let tabsHTML = '';
            
            this.categories.forEach((category, index) => {
                const isActive = (this.currentCategoryId && category.id === this.currentCategoryId) || (!this.currentCategoryId && index === 0);
                const btnClass = isActive ? 'btn-primary active' : 'btn-outline-primary';
                const docCount = category.document_count > 0 ? `(${category.document_count})` : '';
                const canRename = category.can_rename ? '1' : '0';
                const canDelete = category.can_delete_category ? '1' : '0';
                const actionsHtml = (canRename === '1' || canDelete === '1') 
                    ? '<span class="doc-category-actions ms-1" title="Right-click for options" style="opacity:0.7;">' + crmIcon('ellipsis-v') + '</span>'
                    : '';
                tabsHTML += `
                    <span class="doc-category-tab-wrap d-inline-block" style="margin-right: 10px; margin-bottom: 10px;">
                        <button class="btn ${btnClass} doc-category-tab" 
                                data-category-id="${category.id}" 
                                data-category-name="${this.escapeHtml(category.name)}"
                                data-can-rename="${canRename}"
                                data-can-delete="${canDelete}"
                                style="position:relative;">
                            ${category.name} ${docCount}${actionsHtml}
                        </button>
                    </span>
                `;
            });
            
            // Add "Add Category" button
            tabsHTML += `
                <button class="btn btn-success add-document-category-btn" 
                        style="margin-bottom: 10px;">
                    ${crmIcon('plus')} Add Category
                </button>
            `;
            
            tabsContainer.html(tabsHTML);
        },
        
        /**
         * Switch to a different category
         */
        switchCategory: function(categoryId) {
            const self = this;
            this.currentCategoryId = categoryId;
            
            // Update active tab
            $('.doc-category-tab').removeClass('active btn-primary').addClass('btn-outline-primary');
            $(`.doc-category-tab[data-category-id="${categoryId}"]`).removeClass('btn-outline-primary').addClass('btn-primary active');
            
            // Load documents for this category
            this.loadCategoryDocuments(categoryId);
        },
        
        /**
         * Load documents for a specific category
         */
        loadCategoryDocuments: function(categoryId) {
            const self = this;
            
            console.log('document-categories.js: loadCategoryDocuments() called with categoryId:', categoryId);
            
            $.ajax({
                url: '/document-categories/documents',
                method: 'GET',
                data: { 
                    category_id: categoryId,
                    client_id: this.currentClientId 
                },
                success: function(response) {
                    console.log('document-categories.js: loadCategoryDocuments response:', response);
                    console.log('document-categories.js: Number of documents:', response.documents ? response.documents.length : 0);
                    
                    if (response.status) {
                        self.renderDocuments(response.documents);
                    }
                },
                error: function(xhr) {
                    console.error('Error loading category documents:', xhr);
                }
            });
        },
        
        /**
         * Render documents in the table
         */
        renderDocuments: function(documents) {
            console.log('document-categories.js: renderDocuments() called with', documents.length, 'documents');
            
            const tbody = $('.alldocumnetlist');
            if (!tbody.length) {
                console.error('document-categories.js: ERROR - .alldocumnetlist not found!');
                return;
            }
            
            console.log('document-categories.js: Updating .alldocumnetlist HTML...');
            
            if (documents.length === 0) {
                tbody.html(`
                    <tr>
                        <td colspan="2" style="text-align:center;">
                            No documents in this category
                        </td>
                    </tr>
                `);
                this.renderGridDocuments(documents);
                this.applyViewMode();
                return;
            }
            
            const userRole = $('#document-category-tabs').data('user-role') || 0;
            let html = '';
            documents.forEach(doc => {
                const addedBy = doc.user ? `${doc.user.first_name} on ${this.formatDate(doc.created_at)}` : 'N/A';
                // Resolved URL (public path or S3) so context menu and link use the same URL
                const fileUrl = doc.preview_url || (doc.myfile_key ? doc.myfile : this.getAwsUrl(doc)) || '';
                const fileName = doc.file_name || '';
                const fileType = doc.filetype || '';
                const showActionBar = !doc.source_document_id && doc.file_name && 
                    ['signature_placed', 'sent', 'viewed'].indexOf(doc.signature_status || '') >= 0 && 
                    doc.signature_status !== 'signed';
                const showReminder = showActionBar && (doc.is_sent || doc.signature_status === 'sent' || doc.signature_status === 'viewed');
                
                html += `
                    <tr class="drow document-row" id="id_${doc.id}" 
                        data-doc-id="${doc.id}"
                        data-checklist-name="${this.escapeHtml(doc.checklist)}"
                        data-file-name="${this.escapeHtml(fileName)}"
                        data-file-type="${this.escapeHtml(fileType)}"
                        data-myfile="${this.escapeHtml(fileUrl)}"
                        data-myfile-key="${doc.myfile_key || ''}"
                        data-doc-type="${doc.doc_type || 'documents'}"
                        data-user-role="${userRole}"
                        data-signature-status="${doc.signature_status || ''}"
                        title="Added by: ${addedBy}"
                        style="cursor: context-menu;">
                        <td style="white-space: initial;">
                            <div data-id="${doc.id}" data-personalchecklistname="${this.escapeHtml(doc.checklist)}" class="personalchecklist-row">
                                <span>${this.escapeHtml(doc.checklist)}</span>
                            </div>
                        </td>
                        <td style="white-space: initial;">
                            ${this.renderDocumentFile(doc)}
                        </td>
                    </tr>
                `;
                if (showActionBar) {
                    html += `
                    <tr class="document-signature-action-bar" data-doc-id="${doc.id}">
                        <td colspan="2" class="py-2" style="background:#f8f9fa;border-left:3px solid #0d6efd;">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="text-muted small me-2">Signature:</span>
                                <button type="button" class="btn btn-sm btn-primary document-sig-send" ${doc.signature_status === 'sent' || doc.signature_status === 'viewed' ? 'disabled' : ''} title="Send for signature">
                                    ${crmIcon('paper-plane')} Send
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary document-sig-revise" title="Revise placement">
                                    ${crmIcon('edit')} Revise
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger document-sig-remove" title="Remove">
                                    ${crmIcon('times')} Remove
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning document-sig-reminder" title="Send reminder" ${showReminder ? '' : 'style="display:none;"'}>
                                    ${crmIcon('bell')} Reminder
                                </button>
                            </div>
                        </td>
                    </tr>
                    `;
                }
            });
            
            tbody.html(html);
            console.log('document-categories.js: .alldocumnetlist HTML updated successfully');
            this.renderGridDocuments(documents);
            this.applyViewMode();
            
            // Add a marker to check if this runs after old handler
            setTimeout(function() {
                console.log('document-categories.js: 500ms later - .alldocumnetlist row count:', $('.alldocumnetlist tr').length);
            }, 500);
        },

        /**
         * Render documents in the grid view (kept in sync with list view per category)
         */
        renderGridDocuments: function(documents) {
            const gridContainer = $('#alldocuments .allgriddata');
            if (!gridContainer.length) {
                return;
            }

            if (!documents.length) {
                gridContainer.html('<p style="text-align:center;padding:20px;color:#666;">No documents in this category</p><div class="clearfix"></div>');
                return;
            }

            const userRole = $('#document-category-tabs').data('user-role') || 0;
            const downloadBase = (typeof App !== 'undefined' && App.getUrl && App.getUrl('downloadDocument'))
                || (window.AppConfig && window.AppConfig.urls && window.AppConfig.urls.downloadDocument)
                || '/download-document';
            let html = '';

            documents.forEach(doc => {
                const hasFile = doc.myfile && String(doc.myfile).trim() !== '';
                html += `
                    <div class="grid_list" id="gid_${doc.id}">
                        <div class="grid_col">
                            <div class="grid_icon">
                                ${crmIcon('file-image')}
                            </div>
                `;

                if (hasFile) {
                    const fileUrl = doc.preview_url || (doc.myfile_key ? doc.myfile : this.getAwsUrl(doc));
                    const displayName = doc.file_name || doc.checklist || 'Document';
                    const suggestedDl = this.buildDownloadFilename(displayName, doc.filetype || '');
                    const downloadUrl = downloadBase + '?filelink=' + encodeURIComponent(fileUrl) + '&filename=' + encodeURIComponent(suggestedDl);

                    html += `
                            <div class="grid_content">
                                <span id="grid_${doc.id}" class="gridfilename">${this.escapeHtml(displayName)}</span>
                                <div class="dropdown d-inline dropdown_ellipsis_icon">
                                    <a class="dropdown-toggle" href="javascript:;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">${crmIcon('ellipsis-v')}</a>
                                    <div class="dropdown-menu">
                                        <a target="_blank" class="dropdown-item" href="${this.escapeHtml(fileUrl)}">Preview</a>
                                        <a href="${this.escapeHtml(downloadUrl)}" class="dropdown-item download-file" data-filelink="${this.escapeHtml(fileUrl)}" data-filename="${this.escapeHtml(suggestedDl)}" data-dl-base="${this.escapeHtml(doc.file_name || '')}" data-dl-ext="${this.escapeHtml(doc.filetype || '')}" target="_blank" rel="noopener">Download</a>
                    `;

                    if (parseInt(userRole, 10) === 1) {
                        html += `<a data-id="${doc.id}" class="dropdown-item deletenote" data-href="deletealldocs" href="javascript:;">Delete</a>`;
                    }

                    html += `
                                        <a data-id="${doc.id}" class="dropdown-item verifydoc" data-doctype="documents" data-href="verifydoc" href="javascript:;">Verify</a>
                                        <a data-id="${doc.id}" class="dropdown-item notuseddoc" data-doctype="documents" data-href="notuseddoc" href="javascript:;">Not Used</a>
                                    </div>
                                </div>
                            </div>
                    `;
                }

                html += `
                        </div>
                    </div>
                `;
            });

            html += '<div class="clearfix"></div>';
            gridContainer.html(html);

            if (typeof window.refreshCrmIcons === 'function') {
                window.refreshCrmIcons(gridContainer[0]);
            }
        },

        buildDownloadFilename: function(fileName, fileType) {
            const base = (fileName || 'document').trim() || 'document';
            const ext = (fileType || '').replace(/[^a-zA-Z0-9]/g, '').toLowerCase();
            if (ext && !base.toLowerCase().endsWith('.' + ext)) {
                return base + '.' + ext;
            }
            return base;
        },
        
        /**
         * Render document file cell
         */
        renderDocumentFile: function(doc) {
            if (doc.file_name) {
                // Use backend-provided preview_url for Education/Migration public-path docs; else S3 or AWS URL
                const fileUrl = doc.preview_url || (doc.myfile_key ? doc.myfile : this.getAwsUrl(doc));
                return `
                    <div data-id="${doc.id}" data-name="${this.escapeHtml(doc.file_name)}" class="doc-row">
                        <a href="javascript:void(0);"
                           data-preview-type="${this.escapeHtml(doc.filetype)}"
                           data-preview-url="${this.escapeHtml(fileUrl)}"
                           data-preview-container="preview-container-alldocumentlist">
                            ${crmIcon('file-image')} <span>${this.escapeHtml(doc.file_name)}.${doc.filetype}</span>
                        </a>
                    </div>
                `;
            } else {
                return `
                    <div class="allupload_document" style="display:inline-block;">
                        <form method="POST" enctype="multipart/form-data" id="upload_form_${doc.id}">
                            <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                            <input type="hidden" name="clientid" value="${this.currentClientId}">
                            <input type="hidden" name="fileid" value="${doc.id}">
                            <input type="hidden" name="type" value="client">
                            <input type="hidden" name="doctype" value="documents">
                            <input type="hidden" name="category_id" value="${this.currentCategoryId}">
                            <a href="javascript:;" class="btn btn-primary">${crmIcon('plus')} Add Document</a>
                            <input class="alldocupload" data-fileid="${doc.id}" type="file" name="document_upload"/>
                        </form>
                    </div>
                `;
            }
        },
        
        /**
         * Show context menu for custom category tab (Rename / Delete)
         */
        showCategoryContextMenu: function(event, $tab, canRename, canDelete) {
            const self = this;
            const categoryId = $tab.data('category-id');
            const categoryName = $tab.data('category-name');

            let menuId = 'doc-category-context-menu';
            let $old = $('#' + menuId);
            if ($old.length) $old.remove();

            const items = [];
            if (canRename) {
                items.push('<li><a href="javascript:;" class="category-menu-rename">' + crmIcon('edit', { class: 'me-2' }) + 'Rename</a></li>');
            }
            if (canDelete) {
                items.push('<li><a href="javascript:;" class="category-menu-delete">' + crmIcon('trash', { class: 'me-2' }) + 'Delete</a></li>');
            }
            if (canDelete === false && canRename) {
                items.push('<li><a href="javascript:;" class="category-menu-delete disabled text-muted" title="Move or delete all documents first">' + crmIcon('trash', { class: 'me-2' }) + 'Delete</a></li>');
            }

            const $menu = $('<ul id="' + menuId + '" class="list-unstyled document-context-menu show bg-white border shadow-sm rounded py-2" style="position:fixed;min-width:140px;z-index:9999;">' + items.join('') + '</ul>');
            $menu.css({ left: '-9999px', top: '-9999px' });
            $('body').append($menu);

            // Viewport-relative coords for position:fixed (pageX/pageY include scroll and misplace the menu)
            const menuWidth = $menu.outerWidth();
            const menuHeight = $menu.outerHeight();
            const vpWidth = window.innerWidth;
            const vpHeight = window.innerHeight;
            let left = event.clientX;
            let top = event.clientY;
            if (left + menuWidth > vpWidth) {
                left = vpWidth - menuWidth - 8;
            }
            if (top + menuHeight > vpHeight) {
                top = vpHeight - menuHeight - 8;
            }
            if (left < 8) {
                left = 8;
            }
            if (top < 8) {
                top = 8;
            }
            $menu.css({ left: left + 'px', top: top + 'px' });

            const hideMenu = function() {
                $menu.remove();
                $(document).off('click', hideMenu);
            };
            setTimeout(function() { $(document).on('click', hideMenu); }, 0);

            $menu.find('.category-menu-rename').on('click', function(e) {
                e.stopPropagation();
                hideMenu();
                self.showRenameCategoryModal(categoryId, categoryName);
            });

            $menu.find('.category-menu-delete:not(.disabled)').on('click', function(e) {
                e.stopPropagation();
                hideMenu();
                self.showDeleteCategoryModal(categoryId, categoryName);
            });
        },

        /**
         * Show rename category modal
         */
        showRenameCategoryModal: function(categoryId, currentName) {
            const self = this;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Rename Category',
                    html: '<label class="form-label">New name:</label><input type="text" id="rename-category-input" class="form-control" value="' + this.escapeHtml(currentName) + '" style="width:100%;">',
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    cancelButtonText: 'Cancel',
                    preConfirm: function() {
                        const val = document.getElementById('rename-category-input');
                        const name = val ? val.value.trim() : '';
                        if (!name) {
                            Swal.showValidationMessage('Please enter a name');
                            return false;
                        }
                        return name;
                    }
                }).then(function(result) {
                    if (result.isConfirmed && result.value) {
                        self.renameCategory(categoryId, result.value);
                    }
                });
            } else {
                self.showRenameCategoryBootstrapModal(categoryId, currentName);
            }
        },

        showRenameCategoryBootstrapModal: function(categoryId, currentName) {
            const self = this;
            const modalId = 'renameCategoryModal';
            let $old = $('#' + modalId);
            if ($old.length) $old.remove();

            const html = '<div class="modal fade" id="' + modalId + '" tabindex="-1">' +
                '<div class="modal-dialog"><div class="modal-content">' +
                '<div class="modal-header"><h5 class="modal-title">Rename Category</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>' +
                '<div class="modal-body"><label class="form-label">New name:</label><input type="text" id="rename-category-input-modal" class="form-control" value="' + this.escapeHtml(currentName) + '"></div>' +
                '<div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary rename-category-save-btn">Save</button></div>' +
                '</div></div></div>';
            const $wrap = $(html);
            $('body').append($wrap);

            const modal = new bootstrap.Modal($wrap[0]);
            modal.show();

            $wrap.find('.rename-category-save-btn').on('click', function() {
                const name = $wrap.find('#rename-category-input-modal').val().trim();
                if (!name) { toastMsg('Please enter a name', 'warning'); return; }
                modal.hide();
                $wrap.remove();
                self.renameCategory(categoryId, name);
            });

            $wrap.on('hidden.bs.modal', function() { $wrap.remove(); });
        },

        renameCategory: function(categoryId, newName) {
            const self = this;
            $.ajax({
                url: '/document-categories/update/' + categoryId,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'X-Requested-With': 'XMLHttpRequest' },
                data: { name: newName, _token: $('meta[name="csrf-token"]').attr('content') },
                dataType: 'json'
            }).done(function(res) {
                if (res.status) {
                    if (typeof Swal !== 'undefined') Swal.fire('Success!', res.message, 'success');
                    else toastMsg(res.message, 'success');
                    self.loadCategories(true);
                } else {
                    if (typeof Swal !== 'undefined') Swal.fire('Error!', res.message, 'error');
                    else toastMsg(res.message, 'error');
                }
            }).fail(function(xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to rename category';
                if (typeof Swal !== 'undefined') Swal.fire('Error!', msg, 'error');
                else toastMsg(msg, 'error');
            });
        },

        /**
         * Show delete category confirmation
         */
        showDeleteCategoryModal: function(categoryId, categoryName) {
            const self = this;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete Category',
                    text: 'Are you sure you want to delete the category "' + categoryName + '"?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc3545'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        self.deleteCategory(categoryId);
                    }
                });
            } else {
                crmConfirm('Are you sure you want to delete the category "' + categoryName + '"?').then(function (ok) {
                    if (!ok) return;
                    self.deleteCategory(categoryId);
                });
            }
        },

        deleteCategory: function(categoryId) {
            const self = this;
            $.ajax({
                url: '/document-categories/delete/' + categoryId,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'X-Requested-With': 'XMLHttpRequest' },
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    client_id: this.currentClientId
                },
                dataType: 'json'
            }).done(function(res) {
                if (res.status) {
                    if (typeof Swal !== 'undefined') Swal.fire('Success!', res.message, 'success');
                    else toastMsg(res.message, 'success');
                    self.loadCategories(false);
                } else {
                    if (typeof Swal !== 'undefined') Swal.fire('Error!', res.message, 'error');
                    else toastMsg(res.message, 'error');
                }
            }).fail(function(xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message)
                    || (xhr.status ? 'Failed to delete category (HTTP ' + xhr.status + ')' : 'Failed to delete category');
                if (typeof Swal !== 'undefined') Swal.fire('Error!', msg, 'error');
                else toastMsg(msg, 'error');
            });
        },

        /**
         * Show add category modal
         */
        showAddCategoryModal: function() {
            const self = this;
            
            // Check if Swal is available
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Add New Category',
                    html: `
                        <input type="text" id="category-name-input" class="swal2-input" placeholder="Category Name" style="width: 80%;">
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Create',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        const name = $('#category-name-input').val().trim();
                        if (!name) {
                            Swal.showValidationMessage('Please enter a category name');
                            return false;
                        }
                        return { name: name };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        self.createCategory(result.value.name);
                    }
                });
            } else {
                // Fallback to simple prompt
                const categoryName = prompt('Enter category name:');
                if (categoryName && categoryName.trim()) {
                    self.createCategory(categoryName.trim());
                }
            }
        },
        
        /**
         * Create a new category
         */
        createCategory: function(name) {
            const self = this;
            
            $.ajax({
                url: '/document-categories/store',
                method: 'POST',
                data: {
                    name: name,
                    client_id: this.currentClientId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Success!', response.message, 'success');
                        } else {
                            toastMsg(response.message, 'success');
                        }
                        // Reload categories to update document count (preserve current category)
                        self.loadCategories(true);
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error!', response.message, 'error');
                        } else {
                            toastMsg(response.message, 'error');
                        }
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON && xhr.responseJSON.message 
                        ? xhr.responseJSON.message 
                        : 'Error creating category';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error!', message, 'error');
                    } else {
                        toastMsg(message, 'error');
                    }
                }
            });
        },
        
        /**
         * Bind event listeners
         */
        bindEvents: function() {
            const self = this;

            // List / grid view toggle (Documents tab only)
            $(document).on('click', '#alldocuments .document_layout_type a.list', function(e) {
                e.preventDefault();
                self.setViewMode('list');
            });
            $(document).on('click', '#alldocuments .document_layout_type a.grid', function(e) {
                e.preventDefault();
                self.setViewMode('grid');
            });
            
            // Category tab click
            $(document).on('click', '.doc-category-tab', function(e) {
                if ($(e.target).closest('.doc-category-actions').length) return;
                const categoryId = $(this).data('category-id');
                self.switchCategory(categoryId);
            });

            // Category tab right-click: show Rename/Delete context menu for custom categories
            $(document).on('contextmenu', '.doc-category-tab', function(e) {
                const $tab = $(this);
                const canRename = $tab.data('can-rename') === 1;
                const canDelete = $tab.data('can-delete') === 1;
                if (!canRename && !canDelete) return;
                e.preventDefault();
                e.stopPropagation();
                self.showCategoryContextMenu(e, $tab, canRename, canDelete);
            });
            
            // Add category button click
            $(document).on('click', '.add-document-category-btn', function() {
                self.showAddCategoryModal();
            });
            
            // When "Add Checklist" button is clicked, set the category_id
            $(document).on('click', '.add_alldocument_doc', function() {
                if (self.currentCategoryId) {
                    $('#alldocs_category_id').val(self.currentCategoryId);
                }
            });
            
            // Intercept form submission to use AJAX and reload only current category
            $(document).on('submit', '#alldocs_upload_form', function(e) {
                console.log('=== document-categories.js: Form submit event triggered ===');
                console.log('document-categories.js: Current category ID:', self.currentCategoryId);
                
                e.preventDefault();
                e.stopImmediatePropagation(); // Stop other handlers from firing
                
                const form = $(this);
                
                // Ensure category_id is set
                if (!form.find('input[name="category_id"]').val() && self.currentCategoryId) {
                    form.find('input[name="category_id"]').val(self.currentCategoryId);
                }
                
                // Disable submit button to prevent double submission
                const submitBtn = form.find('button[type="submit"], button[onclick*="customValidate"]');
                submitBtn.prop('disabled', true);
                
                console.log('document-categories.js: Submitting via AJAX to:', form.attr('action'));
                console.log('document-categories.js: Form data - category_id:', form.find('input[name="category_id"]').val());
                
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        console.log('document-categories.js: AJAX Response received:', response);
                        
                        // Re-enable submit button
                        submitBtn.prop('disabled', false);
                        
                        if (response.status) {
                            // Reset form first
                            form[0].reset();
                            if (typeof clearEnhancedSelectValue === 'function') {
                                clearEnhancedSelectValue('#checklist');
                            } else {
                                $('#checklist').val(null).trigger('change');
                            }
                            
                            // Close modal immediately
                            const modalElement = document.getElementById('openalldocsmodal');
                            if (modalElement) {
                                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                                if (modalInstance) {
                                    modalInstance.hide();
                                } else {
                                    $('#openalldocsmodal').modal('hide');
                                }
                            }
                            
                            // Remove backdrop manually
                            setTimeout(function() {
                                $('.modal-backdrop').remove();
                                $('body').removeClass('modal-open').css('padding-right', '');
                            }, 200);
                            
                            // Show success message
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                toastMsg(response.message, 'success');
                            }
                            
                            // Reload current category documents
                            console.log('document-categories.js: Reloading documents for category:', self.currentCategoryId);
                            self.loadCategoryDocuments(self.currentCategoryId);
                            
                            // Reload categories to update document count (preserve current category)
                            console.log('document-categories.js: Reloading categories (preserve current)');
                            self.loadCategories(true);
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Error!', response.message, 'error');
                            } else {
                                toastMsg(response.message, 'error');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        // Re-enable submit button
                        submitBtn.prop('disabled', false);
                        
                        console.error('AJAX Error:', status, error);
                        const message = xhr.responseJSON && xhr.responseJSON.message 
                            ? xhr.responseJSON.message 
                            : 'Error adding checklist. Please try again.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error!', message, 'error');
                        } else {
                            toastMsg(message, 'error');
                        }
                    }
                });
                
                return false; // Prevent any other form handlers
            });
        },
        
        /**
         * Helper functions
         */
        escapeHtml: function(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
        },
        
        formatDate: function(dateString) {
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        },
        
        getAwsUrl: function(doc) {
            const bucket = $('meta[name="aws-bucket"]').attr('content') || '';
            const region = $('meta[name="aws-region"]').attr('content') || 'ap-southeast-2';
            return `https://${bucket}.s3.${region}.amazonaws.com/${doc.client_id}/${doc.doc_type}/${doc.myfile}`;
        }
    };
})();
