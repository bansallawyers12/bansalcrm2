/**
 * Document Category Management for Client Detail Page
 * Handles category tabs, creation, and switching
 */

(function() {
    'use strict';

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
                const isActive = index === 0 ? 'active' : '';
                const docCount = category.document_count > 0 ? `(${category.document_count})` : '';
                
                tabsHTML += `
                    <button class="btn ${isActive} doc-category-tab" 
                            data-category-id="${category.id}" 
                            data-category-name="${category.name}"
                            style="margin-right: 10px; margin-bottom: 10px;">
                        ${category.name} ${docCount}
                    </button>
                `;
            });
            
            // Add "Add Category" button
            tabsHTML += `
                <button class="btn btn-success add-document-category-btn" 
                        style="margin-bottom: 10px;">
                    <i class="fa fa-plus"></i> Add Category
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
            
            let html = '';
            documents.forEach(doc => {
                const addedBy = doc.user ? `${doc.user.first_name} on ${this.formatDate(doc.created_at)}` : 'N/A';
                // Resolved URL (public path or S3) so context menu and link use the same URL
                const fileUrl = doc.preview_url || (doc.myfile_key ? doc.myfile : this.getAwsUrl(doc)) || '';
                const fileName = doc.file_name || '';
                const fileType = doc.filetype || '';
                
                html += `
                    <tr class="drow document-row" id="id_${doc.id}" 
                        data-doc-id="${doc.id}"
                        data-checklist-name="${this.escapeHtml(doc.checklist)}"
                        data-file-name="${this.escapeHtml(fileName)}"
                        data-file-type="${this.escapeHtml(fileType)}"
                        data-myfile="${this.escapeHtml(fileUrl)}"
                        data-myfile-key="${doc.myfile_key || ''}"
                        data-doc-type="${doc.doc_type || ''}"
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
                            <a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>
                            <input class="alldocupload" data-fileid="${doc.id}" type="file" name="document_upload"/>
                        </form>
                    </div>
                `;
            }
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
                            alert('Success: ' + response.message);
                        }
                        // Reload categories to update document count (preserve current category)
                        self.loadCategories(true);
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error!', response.message, 'error');
                        } else {
                            alert('Error: ' + response.message);
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
                        alert('Error: ' + message);
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
            $(document).on('click', '.doc-category-tab', function() {
                const categoryId = $(this).data('category-id');
                self.switchCategory(categoryId);
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
                            $('#checklist').val(null).trigger('change');
                            
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
                                alert('Success: ' + response.message);
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
                                alert('Error: ' + response.message);
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
                            alert('Error: ' + message);
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
