/**
 * Document Category Management for Client Detail Page
 * Handles category tabs, creation, and switching
 */

(function() {
    'use strict';

    var toastMsg = typeof window.toastMsg === 'function'
        ? window.toastMsg.bind(window)
        : (typeof window.showToast === 'function'
            ? window.showToast.bind(window)
            : function (message) { if (message) alert(message); });

    // Global state
    window.DocumentCategoryManager = {
        currentClientId: null,
        currentCategoryId: null,
        categories: [],
        
        /**
         * Initialize the category system
         */
        init: function(clientId) {
            this.currentClientId = clientId;
            this.loadCategories();
            this.bindEvents();
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
                    ? '<span class="doc-category-actions ms-1" title="Right-click for options" style="opacity:0.7;"><i class="fas fa-ellipsis-v"></i></span>'
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
                    <i class="fas fa-plus"></i> Add Category
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
                                    <i class="fas fa-paper-plane"></i> Send
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary document-sig-revise" title="Revise placement">
                                    <i class="fas fa-edit"></i> Revise
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger document-sig-remove" title="Remove">
                                    <i class="fas fa-times"></i> Remove
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning document-sig-reminder" title="Send reminder" ${showReminder ? '' : 'style="display:none;"'}>
                                    <i class="fas fa-bell"></i> Reminder
                                </button>
                            </div>
                        </td>
                    </tr>
                    `;
                }
            });
            
            tbody.html(html);
            console.log('document-categories.js: .alldocumnetlist HTML updated successfully');
            
            // Add a marker to check if this runs after old handler
            setTimeout(function() {
                console.log('document-categories.js: 500ms later - .alldocumnetlist row count:', $('.alldocumnetlist tr').length);
            }, 500);
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
                        <a href="javascript:void(0);" onclick="previewFile('${doc.filetype}','${fileUrl}','preview-container-alldocumentlist')">
                            <i class="fas fa-file-image"></i> <span>${this.escapeHtml(doc.file_name)}.${doc.filetype}</span>
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
                            <a href="javascript:;" class="btn btn-primary"><i class="fas fa-plus"></i> Add Document</a>
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
                items.push('<li><a href="javascript:;" class="category-menu-rename"><i class="fas fa-edit me-2"></i>Rename</a></li>');
            }
            if (canDelete) {
                items.push('<li><a href="javascript:;" class="category-menu-delete"><i class="fas fa-trash me-2"></i>Delete</a></li>');
            }
            if (canDelete === false && canRename) {
                items.push('<li><a href="javascript:;" class="category-menu-delete disabled text-muted" title="Move or delete all documents first"><i class="fas fa-trash me-2"></i>Delete</a></li>');
            }

            const $menu = $('<ul id="' + menuId + '" class="list-unstyled document-context-menu show bg-white border shadow-sm rounded py-2" style="position:fixed;min-width:140px;z-index:9999;">' + items.join('') + '</ul>');
            $menu.css({ left: event.pageX + 'px', top: event.pageY + 'px' });
            $('body').append($menu);

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
                if (confirm('Are you sure you want to delete the category "' + categoryName + '"?')) {
                    self.deleteCategory(categoryId);
                }
            }
        },

        deleteCategory: function(categoryId) {
            const self = this;
            $.ajax({
                url: '/document-categories/' + categoryId,
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'X-Requested-With': 'XMLHttpRequest' },
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
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
                const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to delete category';
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
