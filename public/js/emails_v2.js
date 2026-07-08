/**
 * Emails Module for CRM Client Email Tab
 * Handles upload, search, and display of .msg and .eml email files
 * Adapted from email-viewer app to work with migration manager backend
 */

(function() {
    'use strict';

    function labelIconHtml(icon, extraClass) {
        const options = extraClass ? { class: extraClass } : {};
        return window.crmIconStored(icon || 'tag', options);
    }

    function spinnerHtml(label) {
        return window.crmIconSpinner(label || '');
    }

    // =========================================================================
    // Module State
    // =========================================================================
    let currentPage = 1;
    let lastPage = 1;
    let isLoading = false;
    let isUploading = false;
    let selectedEmailId = null;
    let currentReadingEmail = null;
    let currentMailType = 'inbox'; // 'inbox' or 'sent' - determines endpoint
    let currentEmailCategory = 'client'; // 'client' or 'college' - client detail only
    let currentLabelId = ''; // EmailLabel.id for filtering
    let currentSearch = '';
    let currentSort = 'date';
    let availableLabels = []; // Loaded from API

    // Expose function to set mail type (for external use)
    window.setEmailMailTypeV2 = function(type) {
        currentMailType = type;
        const mailTypeFilter = document.getElementById('mailTypeFilterV2');
        if (mailTypeFilter) {
            mailTypeFilter.value = type;
        }
        updateFolderTabButtons(type);
    };

    // Expose function to set category tab Client/College (for external use after compose send)
    window.setEmailCategoryV2 = function(category) {
        if (!showEmailCategoryTabs()) return;
        const cat = (category === 'college' || category === 'client') ? category : 'client';
        currentEmailCategory = cat;
        updateCategoryTabButtons(currentEmailCategory);
    };

    function updateFolderTabButtons(folder) {
        document.querySelectorAll('.folder-tab-btn, .folder-item').forEach(btn => {
            const isActive = (btn.dataset.folder || btn.getAttribute('data-folder')) === folder;
            btn.classList.toggle('active', isActive);
            btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });
    }

    /**
     * Whether to show Client/College category tabs (client detail only, not partner)
     */
    function showEmailCategoryTabs() {
        const container = document.querySelector('.email-v2-interface-container');
        return container && container.dataset.showEmailCategory === '1';
    }

    function updateCategoryTabButtons(category) {
        document.querySelectorAll('.category-tab-btn').forEach(btn => {
            const cat = btn.dataset.category || btn.getAttribute('data-category');
            const isActive = cat === category;
            btn.classList.toggle('active', isActive);
            btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });
    }

    // =========================================================================
    // Utility Functions
    // =========================================================================

    /**
     * Get entity ID from the DOM (supports both client and partner)
     */
    function getEntityId() {
        const container = document.querySelector('.email-v2-interface-container');
        if (!container) {
            // Page doesn't have email interface - this is normal for pages that don't support emails
            return null;
        }
        
        // Check if the container has the required attribute
        const entityId = container.dataset.entityId;
        if (!entityId || entityId === '') {
            // Container exists but entity ID is not set - page may not be configured for emails
            // This is not an error, just return null silently
            return null;
        }
        
        return entityId;
    }

    /**
     * Get entity type from the DOM (client or partner)
     */
    function getEntityType() {
        const container = document.querySelector('.email-v2-interface-container');
        if (!container) {
            return 'client'; // default
        }
        return container.dataset.entityType || 'client';
    }

    /**
     * Allowed upload extensions from blade data attribute (e.g. .msg,.eml)
     */
    function getAllowedEmailExtensions() {
        const container = document.querySelector('.email-v2-interface-container');
        const accept = container && container.dataset.emailUploadAccept
            ? container.dataset.emailUploadAccept
            : '.msg,.eml';
        return accept.split(',').map(function(ext) {
            return ext.trim().toLowerCase().replace(/^\./, '');
        }).filter(Boolean);
    }

    function getAllowedExtensionsLabel() {
        return getAllowedEmailExtensions().map(function(ext) {
            return '.' + ext;
        }).join(', ');
    }

    function isAllowedEmailUploadFile(file) {
        if (!file || !file.name) return false;
        const ext = file.name.toLowerCase().split('.').pop();
        return getAllowedEmailExtensions().indexOf(ext) !== -1;
    }

    function filterAllowedEmailUploadFiles(files) {
        return Array.from(files || []).filter(isAllowedEmailUploadFile);
    }

    const DUPLICATE_EXISTS_MESSAGE = 'This email already exists.';

    function showDuplicateEmailPrompt(fileName) {
        return new Promise(function(resolve) {
            const modal = document.getElementById('duplicateEmailModalV2');
            if (!modal) {
                resolve(window.confirm(DUPLICATE_EXISTS_MESSAGE + ' Upload anyway?'));
                return;
            }

            const fileNameEl = document.getElementById('duplicateEmailFileNameV2');
            const acceptBtn = document.getElementById('duplicateEmailAcceptV2');
            const rejectBtn = document.getElementById('duplicateEmailRejectV2');

            if (fileNameEl) {
                fileNameEl.textContent = fileName ? ('File: ' + fileName) : '';
            }

            function cleanup() {
                modal.classList.remove('active');
                modal.setAttribute('aria-hidden', 'true');
                if (acceptBtn) acceptBtn.removeEventListener('click', onAccept);
                if (rejectBtn) rejectBtn.removeEventListener('click', onReject);
                modal.removeEventListener('click', onOverlayClick);
                document.removeEventListener('keydown', onKeyDown);
            }

            function onAccept() {
                cleanup();
                resolve(true);
            }

            function onReject() {
                cleanup();
                resolve(false);
            }

            function onOverlayClick(event) {
                if (event.target === modal) {
                    onReject();
                }
            }

            function onKeyDown(event) {
                if (event.key === 'Escape') {
                    onReject();
                }
            }

            if (acceptBtn) acceptBtn.addEventListener('click', onAccept);
            if (rejectBtn) rejectBtn.addEventListener('click', onReject);
            modal.addEventListener('click', onOverlayClick);
            document.addEventListener('keydown', onKeyDown);

            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');
            if (acceptBtn) acceptBtn.focus();
        });
    }

    function getDuplicateUploadError(data) {
        if (!data || !Array.isArray(data.errors)) {
            return null;
        }
        return data.errors.find(function(err) {
            return err && err.duplicate;
        }) || null;
    }

    /**
     * Get CSRF token from meta tag
     */
    function getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    }

    /**
     * Show notification message
     */
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `email-notification email-notification-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 4px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 10000;
            max-width: 500px;
            max-height: 400px;
            overflow-y: auto;
            animation: slideIn 0.3s ease-out;
            font-size: 14px;
            white-space: pre-wrap;
            word-wrap: break-word;
            ${type === 'success' ? 'background: #10b981; color: white;' : ''}
            ${type === 'error' ? 'background: #ef4444; color: white;' : ''}
            ${type === 'info' ? 'background: #3b82f6; color: white;' : ''}
        `;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Longer display time for error messages
        const displayTime = type === 'error' ? 8000 : 4000;

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, displayTime);
    }

    /**
     * Format date to readable string
     * Handles both ISO date strings and formatted strings like "d/m/Y h:i a"
     */
    function formatDate(dateString) {
        if (!dateString) return 'Unknown';
        try {
            // Check if it's already in formatted format (d/m/Y h:i a)
            if (typeof dateString === 'string' && dateString.match(/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2} (am|pm)$/i)) {
                // Parse formatted date: "dd/mm/yyyy hh:mm am/pm"
                const parts = dateString.match(/^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2}) (am|pm)$/i);
                if (parts) {
                    const [, day, month, year, hour, minute, ampm] = parts;
                    let hour24 = parseInt(hour);
                    if (ampm.toLowerCase() === 'pm' && hour24 !== 12) hour24 += 12;
                    if (ampm.toLowerCase() === 'am' && hour24 === 12) hour24 = 0;
                    const date = new Date(year, month - 1, day, hour24, minute);
                    return date.toLocaleString('en-AU', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            }
            // Try parsing as ISO date string
            const date = new Date(dateString);
            if (isNaN(date.getTime())) {
                return dateString; // Return as-is if can't parse
            }
            return date.toLocaleString('en-AU', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (e) {
            return dateString;
        }
    }

    /**
     * Get the email date to display (prefers sent date over upload date)
     */
    function getEmailDate(email) {
        // Prefer fetch_mail_sent_time (email's original sent date)
        if (email.fetch_mail_sent_time) {
            return email.fetch_mail_sent_time;
        }
        // Fallback to received_date if available
        if (email.received_date) {
            return email.received_date;
        }
        // Last resort: use created_at (upload/send time)
        return email.created_at || null;
    }

    /**
     * Format file size to readable string
     */
    function formatFileSize(bytes) {
        if (!bytes || bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
    }

    /**
     * Get attachment icon class based on content type
     */
    function getAttachmentIcon(contentType) {
        if (!contentType) return 'paperclip';
        
        const type = contentType.toLowerCase();
        
        // Images
        if (type.includes('image')) {
            return 'image';
        }
        
        // PDFs
        if (type.includes('pdf')) {
            return 'file-pdf';
        }
        
        // Word documents
        if (type.includes('word') || type.includes('document') || type.includes('.docx')) {
            return 'file-word';
        }
        
        // Excel spreadsheets
        if (type.includes('excel') || type.includes('spreadsheet') || type.includes('.xlsx')) {
            return 'file-excel';
        }
        
        // PowerPoint
        if (type.includes('powerpoint') || type.includes('presentation')) {
            return 'file-powerpoint';
        }
        
        // Archives
        if (type.includes('zip') || type.includes('rar') || type.includes('archive')) {
            return 'file-archive';
        }
        
        // Code files
        if (type.includes('text/plain') || type.includes('code') || type.includes('javascript') || type.includes('html')) {
            return 'file-code';
        }
        
        // Default
        return 'paperclip';
    }

    /**
     * Get attachment icon color class based on content type
     */
    function getAttachmentIconColor(contentType) {
        if (!contentType) return '';
        
        const type = contentType.toLowerCase();
        
        if (type.includes('image')) return 'attachment-icon-image';
        if (type.includes('pdf')) return 'attachment-icon-pdf';
        if (type.includes('word') || type.includes('document')) return 'attachment-icon-word';
        if (type.includes('excel') || type.includes('spreadsheet')) return 'attachment-icon-excel';
        
        return '';
    }

    /**
     * Check if attachment can be previewed
     */
    function canPreviewAttachment(contentType) {
        if (!contentType) return false;

        const type = contentType.toLowerCase();
        return type.includes('image/') || type.includes('pdf');
    }

    /**
     * Whether /email-v2/attachments/{id}/preview can show this file.
     * Prefer API `previewable` (MailReportAttachment::canPreview) when present; else client heuristics.
     */
    function attachmentSupportsBrowserPreview(att) {
        if (!att || typeof att !== 'object') return false;
        if (att.previewable === true) {
            return true;
        }
        if (att.previewable === false) {
            return false;
        }
        if (canPreviewAttachment(att.content_type)) {
            return true;
        }
        const ext = String(att.extension || '').toLowerCase().replace(/^\./, '');
        if (['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(ext)) {
            return true;
        }
        const name = String(att.filename || att.display_name || '');
        let m = name.match(/\.([a-zA-Z0-9]{1,8})$/i);
        if (m && ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(m[1].toLowerCase())) {
            return true;
        }
        const resolved = resolveAttachmentDownloadFilename(att);
        m = resolved.match(/\.([a-zA-Z0-9]{1,8})$/i);
        if (m && ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(m[1].toLowerCase())) {
            return true;
        }
        const fromPath = extensionFromPathOrUrl(att.file_path || '') || extensionFromPathOrUrl(att.s3_key || '');
        if (['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(fromPath)) {
            return true;
        }
        return false;
    }

    /**
     * Sanitize filename for safe download
     */
    function sanitizeFilename(filename) {
        if (!filename) return 'download';
        
        // Remove invalid filename characters (Windows + common problem chars)
        return filename
            .replace(/[/\\?%*:|"<>]/g, '-')
            .replace(/[\[\]]/g, '-')
            .replace(/\s+/g, '_')             // Replace spaces with underscore
            .substring(0, 200);               // Limit length
    }

    /**
     * Map Content-Type to a file extension when DB filename omits it.
     */
    function extensionFromContentType(ct) {
        if (!ct || typeof ct !== 'string') return '';
        const base = ct.split(';')[0].trim().toLowerCase();
        const map = {
            'application/pdf': 'pdf',
            'image/jpeg': 'jpg',
            'image/jpg': 'jpg',
            'image/png': 'png',
            'image/gif': 'gif',
            'image/webp': 'webp',
            'image/bmp': 'bmp',
            'application/msword': 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'docx',
            'application/vnd.ms-excel': 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'xlsx',
            'text/plain': 'txt',
            'text/csv': 'csv',
        };
        return map[base] || '';
    }

    function extensionFromPathOrUrl(s) {
        if (!s || typeof s !== 'string') return '';
        const path = s.replace(/\\/g, '/').split('?')[0];
        const seg = path.split('/').pop() || '';
        const m = seg.match(/\.([a-zA-Z0-9]{1,8})$/);
        return m ? m[1].toLowerCase() : '';
    }

    /**
     * Build a download filename with extension for attachment rows (matches server-side naming).
     */
    function resolveAttachmentDownloadFilename(att) {
        if (!att || typeof att !== 'object') return 'download';
        let name = String(att.filename || att.display_name || 'file').trim() || 'file';
        name = sanitizeFilename(name);

        let ext = '';
        if (att.extension) {
            ext = String(att.extension).replace(/^\./, '').toLowerCase().replace(/[^a-z0-9]/g, '');
        }
        if (!ext && att.content_type) {
            ext = extensionFromContentType(att.content_type);
        }
        if (!ext) {
            ext = extensionFromPathOrUrl(att.file_path) || extensionFromPathOrUrl(att.s3_key);
        }

        const lower = name.toLowerCase();
        if (ext && !lower.endsWith('.' + ext)) {
            name = name + '.' + ext;
        }
        return name;
    }

    /**
     * Parse filename from Content-Disposition (RFC 5987 filename* preferred).
     */
    function parseFilenameFromContentDisposition(header) {
        if (!header || typeof header !== 'string') return null;
        const star = /filename\*=UTF-8''([^;\n]+)/i.exec(header);
        if (star && star[1]) {
            try {
                return decodeURIComponent(star[1].trim());
            } catch (e) {
                return null;
            }
        }
        const quoted = /filename="([^"]+)"/i.exec(header);
        if (quoted && quoted[1]) return quoted[1];
        const plain = /filename=([^;\n]+)/i.exec(header);
        if (plain && plain[1]) {
            return plain[1].trim().replace(/^["']|["']$/g, '');
        }
        return null;
    }

    /**
     * Filter to get only regular (non-inline) attachments
     */
    function getRegularAttachments(attachments) {
        if (!attachments || !Array.isArray(attachments)) {
            return [];
        }
        
        return attachments.filter(att => !att.is_inline);
    }

    /**
     * Debounce function
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // =========================================================================
    // Upload Functionality
    // =========================================================================

    /**
     * Initialize upload functionality with drag & drop
     */
    window.initializeUpload = function() {
        console.log('Initializing upload module...');
        
        const fileInput = document.getElementById('emailV2FileInput');
        const uploadArea = document.getElementById('upload-area-v2');
        const fileStatus = document.getElementById('fileStatusV2');
        const fileCountBadge = document.getElementById('file-count-v2');
        const uploadProgress = document.getElementById('upload-progress-v2');

        if (!fileInput || !uploadArea || !fileStatus) {
            console.warn('Upload elements not found - skipping email upload initialization (page may not have emails UI)');
            return;
        }

        let dragCounter = 0;

        // Prevent default drag behaviors on document
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop area when item is dragged over it
        uploadArea.addEventListener('dragenter', function(e) {
            dragCounter++;
            uploadArea.classList.add('drag-over');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            dragCounter--;
            if (dragCounter === 0) {
                uploadArea.classList.remove('drag-over');
            }
        });

        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
        });

        // Handle dropped files
        uploadArea.addEventListener('drop', function(e) {
            dragCounter = 0;
            uploadArea.classList.remove('drag-over');
            
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files && files.length > 0) {
                handleFiles(files);
            }
        });

        // Click to open file dialog
        uploadArea.addEventListener('click', function() {
            if (!isUploading) {
                fileInput.click();
            }
        });

        // Handle file input change
        fileInput.addEventListener('change', function() {
            const files = this.files;
            if (files && files.length > 0) {
                handleFiles(files);
            }
        });

        function handleFiles(files) {
            if (isUploading) {
                console.log('Upload already in progress');
                return;
            }

            console.log('Files selected:', files.length);

            // Filter to allowed email extensions (.msg, .eml)
            const allowedLabel = getAllowedExtensionsLabel();
            const emailFiles = filterAllowedEmailUploadFiles(files);

            if (emailFiles.length === 0) {
                showNotification('Please upload Outlook email files only (' + allowedLabel + ')', 'error');
                fileStatus.textContent = 'Allowed: ' + allowedLabel;
                fileStatus.parentElement.className = 'upload-progress error';
                setTimeout(() => {
                    fileStatus.textContent = 'Ready to upload';
                    fileStatus.parentElement.className = 'upload-progress';
                }, 3000);
                return;
            }

            if (emailFiles.length !== files.length) {
                showNotification('Only ' + emailFiles.length + ' of ' + files.length + ' files are valid (' + allowedLabel + ')', 'info');
            }

            // Update file count badge
            updateFileCount(emailFiles.length);

            // Update status
            fileStatus.textContent = `${emailFiles.length} file(s) ready to upload`;
            fileStatus.parentElement.className = 'upload-progress';

            // Auto-upload immediately
            uploadFiles(emailFiles);
        }

        function updateFileCount(count) {
            if (fileCountBadge) {
                fileCountBadge.textContent = count;
                if (count > 0) {
                    fileCountBadge.classList.add('show');
                } else {
                    fileCountBadge.classList.remove('show');
                }
            }
        }

        console.log('Upload module initialized with drag & drop');
    };

    /**
     * POST a single email file to the upload endpoint.
     */
    async function uploadSingleEmailFile(file, forceUpload, attachmentStorage) {
        const clientId = getEntityId();
        const csrfToken = getCsrfToken();
        if (!clientId) {
            throw new Error('Client ID not found');
        }
        if (!csrfToken) {
            throw new Error('Security token not found. Please refresh the page and try again.');
        }

        const formData = new FormData();
        formData.append('email_files[]', file);
        formData.append('client_id', clientId);
        formData.append('type', getEntityType());
        if (showEmailCategoryTabs()) {
            formData.append('email_category', currentEmailCategory);
        }
        const selectedLabels = getSelectedLabelIds();
        selectedLabels.forEach(function(labelId) {
            formData.append('label_ids[]', labelId);
        });
        formData.append('_token', csrfToken);
        if (forceUpload) {
            formData.append('force_upload', '1');
        }
        if (attachmentStorage && attachmentStorage.length) {
            formData.append('attachment_storage', JSON.stringify(attachmentStorage));
        }

        const uploadUrl = currentMailType === 'sent' ? '/email-v2/upload-sent' : '/email-v2/upload-inbox';
        const response = await fetch(uploadUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData,
            credentials: 'same-origin'
        });

        const contentType = response.headers.get('content-type') || '';
        let data = null;
        if (contentType.includes('application/json')) {
            data = await response.json();
        } else {
            const errorText = await response.text();
            throw new Error('Server returned invalid response: ' + errorText.substring(0, 200));
        }

        if (response.status === 422) {
            const errorMsg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Validation failed');
            throw new Error('Upload validation failed: ' + errorMsg);
        }

        return data;
    }

    /**
     * Preview attachment metadata before upload (metadata only, no save).
     */
    async function previewEmailAttachments(file) {
        const clientId = getEntityId();
        const csrfToken = getCsrfToken();
        const formData = new FormData();
        formData.append('email_files[]', file);
        formData.append('client_id', clientId);
        formData.append('type', getEntityType());
        if (showEmailCategoryTabs()) {
            formData.append('email_category', currentEmailCategory);
        }
        formData.append('_token', csrfToken);

        const response = await fetch('/email-v2/preview-attachments', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData,
            credentials: 'same-origin'
        });

        const result = await response.json();
        if (!response.ok || !result.status) {
            throw new Error(result.message || 'Failed to preview attachments');
        }
        return result.attachments || [];
    }

    let documentCategoriesCacheV2 = null;
    let documentCategoriesCacheClientIdV2 = null;

    async function loadDocumentCategoriesForAttachmentModal() {
        const clientId = getEntityId();
        if (!clientId) {
            return [];
        }
        if (documentCategoriesCacheV2 && documentCategoriesCacheClientIdV2 === clientId) {
            return documentCategoriesCacheV2;
        }
        try {
            const response = await fetch(
                '/document-categories/get?client_id=' + encodeURIComponent(clientId),
                {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                }
            );
            const data = await response.json();
            if (response.ok && data && data.status && data.categories) {
                documentCategoriesCacheV2 = data.categories;
            } else {
                documentCategoriesCacheV2 = [];
            }
            documentCategoriesCacheClientIdV2 = clientId;
        } catch (e) {
            console.warn('Could not load document categories', e);
            documentCategoriesCacheV2 = [];
            documentCategoriesCacheClientIdV2 = clientId;
        }
        return documentCategoriesCacheV2;
    }

    /**
     * Preview attachments for each email file before upload (metadata only).
     * Returns map of email filename -> attachment metadata array.
     */
    async function previewBatchEmailAttachments(files) {
        const byEmail = {};
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            try {
                const attachments = await previewEmailAttachments(file);
                byEmail[file.name] = (attachments || []).map(function(att) {
                    return Object.assign({}, att, { email_filename: file.name });
                });
            } catch (previewErr) {
                console.warn('Attachment preview skipped for ' + file.name + ':', previewErr);
                byEmail[file.name] = [];
            }
        }
        return byEmail;
    }

    /**
     * Flatten batch preview map into one list for the attachment modal.
     */
    function flattenBatchAttachments(byEmail) {
        const flat = [];
        Object.keys(byEmail).forEach(function(emailName) {
            (byEmail[emailName] || []).forEach(function(att) {
                flat.push(att);
            });
        });
        return flat;
    }

    /**
     * Group modal attachment_storage rows back per email file for upload.
     */
    function groupAttachmentStorageByEmail(storageList) {
        const map = {};
        (storageList || []).forEach(function(item) {
            const emailName = item.email_filename;
            if (!emailName) {
                return;
            }
            if (!map[emailName]) {
                map[emailName] = [];
            }
            map[emailName].push({
                original_filename: item.original_filename,
                filename: item.filename,
                file_name: item.file_name,
                storage_type: item.storage_type,
                category_id: item.category_id
            });
        });
        return map;
    }

    /**
     * Unique email filenames that have attachments in the batch modal.
     */
    function getAttachmentEmailGroups(attachments) {
        const groups = [];
        const seen = {};
        (attachments || []).forEach(function(att) {
            const emailName = att.email_filename;
            if (!emailName || seen[emailName]) {
                return;
            }
            seen[emailName] = true;
            groups.push({ key: 'email-' + groups.length, name: emailName });
        });
        return groups;
    }

    /**
     * Build <option> HTML for category selects.
     */
    function buildDocumentCategoryOptionsHtml(categories) {
        let html = '<option value="">Select category…</option>';
        (categories || []).forEach(function(cat) {
            html += '<option value="' + escapeHtml(String(cat.id)) + '">' +
                escapeHtml(cat.name || cat.category_name || ('Category ' + cat.id)) +
                '</option>';
        });
        return html;
    }

    /**
     * Show attachment rename / document-folder modal (client only; partners skip).
     * Returns attachment_storage array, empty array, or null if cancelled.
     * @param {Array} attachments
     * @param {{ emailCount?: number }} [options]
     */
    function showAttachmentStorageModal(attachments, options) {
        options = options || {};
        return new Promise(function(resolve) {
            const modal = document.getElementById('attachmentStorageModalV2');
            const body = document.getElementById('attachmentStorageModalBodyV2');
            const countEl = document.getElementById('attachmentStorageCountV2');
            const globalDestination = document.getElementById('attachmentStorageDestinationV2');
            const perEmailDestination = document.getElementById('attachmentStoragePerEmailV2');
            const saveToDocsCheckbox = document.getElementById('attachmentSaveToDocumentsV2');
            const categorySelect = document.getElementById('attachmentDocumentCategoryV2');
            const confirmBtn = document.getElementById('attachmentStorageConfirmV2');
            const cancelBtn = document.getElementById('attachmentStorageCancelV2');
            const emailGroups = getAttachmentEmailGroups(attachments);
            const usePerEmailCategories = emailGroups.length > 1;
            const perEmailToggleHandlers = [];

            if (!modal || !body || !confirmBtn || !cancelBtn) {
                resolve([]);
                return;
            }

            if (countEl) {
                if ((options.emailCount || 0) > 1) {
                    countEl.textContent = attachments.length + (attachments.length === 1 ? ' file' : ' files') +
                        ' across ' + options.emailCount + ' emails';
                } else {
                    countEl.textContent = attachments.length + (attachments.length === 1 ? ' file' : ' files');
                }
            }

            function renderAttachmentRow(att, showEmailLabel) {
                const stem = (att.display_name || att.filename || 'attachment').replace(/\.[^.]+$/, '');
                const emailKey = att._email_key || '';
                const emailFilenameAttr = att.email_filename
                    ? ' data-email-filename="' + escapeHtml(att.email_filename) + '"'
                    : '';
                const emailKeyAttr = emailKey ? ' data-email-key="' + escapeHtml(emailKey) + '"' : '';
                const emailLabel = showEmailLabel && att.email_filename
                    ? '<div class="attachment-storage-email-label">' + escapeHtml(att.email_filename) + '</div>'
                    : '';
                return '<tr data-original-filename="' + escapeHtml(att.filename) + '"' +
                    emailFilenameAttr + emailKeyAttr + '>' +
                    '<td>' + emailLabel + escapeHtml(att.filename) + '</td>' +
                    '<td>' + formatFileSize(att.file_size || 0) + '</td>' +
                    '<td><input type="text" class="attachment-rename-input" value="' + escapeHtml(stem) + '" aria-label="Save as"></td>' +
                    '</tr>';
            }

            function populateGlobalCategorySelect(categories) {
                if (!categorySelect) {
                    return;
                }
                categorySelect.disabled = true;
                categorySelect.innerHTML = buildDocumentCategoryOptionsHtml(categories);
            }

            function renderPerEmailDestination(categories) {
                if (!perEmailDestination) {
                    return;
                }
                const categoryOptionsHtml = buildDocumentCategoryOptionsHtml(categories);
                perEmailDestination.innerHTML = emailGroups.map(function(group) {
                    return '<div class="attachment-storage-email-group" data-email-key="' + escapeHtml(group.key) + '">' +
                        '<div class="attachment-storage-email-group__title">' + escapeHtml(group.name) + '</div>' +
                        '<div class="attachment-storage-email-group__controls">' +
                        '<label class="attachment-storage-checkbox">' +
                        '<input type="checkbox" class="attachment-email-save-docs" data-email-key="' + escapeHtml(group.key) + '">' +
                        'Also save copies to Documents tab' +
                        '</label>' +
                        '<select class="attachment-storage-select attachment-email-category" data-email-key="' + escapeHtml(group.key) + '" aria-label="Document category for ' + escapeHtml(group.name) + '" disabled>' +
                        categoryOptionsHtml +
                        '</select>' +
                        '</div>' +
                        '</div>';
                }).join('');

                perEmailDestination.querySelectorAll('.attachment-email-save-docs').forEach(function(checkbox) {
                    function onToggle() {
                        const key = checkbox.getAttribute('data-email-key');
                        const select = perEmailDestination.querySelector('.attachment-email-category[data-email-key="' + key + '"]');
                        if (select) {
                            select.disabled = !checkbox.checked;
                        }
                    }
                    checkbox.addEventListener('change', onToggle);
                    perEmailToggleHandlers.push({ el: checkbox, fn: onToggle });
                });
            }

            if (usePerEmailCategories) {
                if (globalDestination) {
                    globalDestination.hidden = true;
                }
                if (perEmailDestination) {
                    perEmailDestination.hidden = false;
                    renderPerEmailDestination([]);
                }
                const keyByEmail = {};
                emailGroups.forEach(function(group) {
                    keyByEmail[group.name] = group.key;
                });
                const rowsHtml = [];
                emailGroups.forEach(function(group, groupIndex) {
                    if (groupIndex > 0) {
                        rowsHtml.push('<tr class="attachment-storage-group-spacer"><td colspan="3"></td></tr>');
                    }
                    (attachments || []).forEach(function(att) {
                        if (att.email_filename !== group.name) {
                            return;
                        }
                        const rowAtt = Object.assign({}, att, { _email_key: group.key });
                        rowsHtml.push(renderAttachmentRow(rowAtt, false));
                    });
                });
                body.innerHTML = rowsHtml.join('');
            } else {
                if (globalDestination) {
                    globalDestination.hidden = false;
                }
                if (perEmailDestination) {
                    perEmailDestination.hidden = true;
                    perEmailDestination.innerHTML = '';
                }
                if (saveToDocsCheckbox) {
                    saveToDocsCheckbox.checked = false;
                }
                body.innerHTML = (attachments || []).map(function(att) {
                    return renderAttachmentRow(att, !!att.email_filename);
                }).join('');
            }

            loadDocumentCategoriesForAttachmentModal().then(function(categories) {
                if (usePerEmailCategories) {
                    renderPerEmailDestination(categories);
                } else {
                    populateGlobalCategorySelect(categories);
                }
            });

            function closeModal() {
                modal.classList.remove('active');
                modal.setAttribute('aria-hidden', 'true');
                confirmBtn.removeEventListener('click', onConfirm);
                cancelBtn.removeEventListener('click', onCancel);
                if (saveToDocsCheckbox) {
                    saveToDocsCheckbox.removeEventListener('change', onSaveToDocsToggle);
                }
                perEmailToggleHandlers.forEach(function(handler) {
                    handler.el.removeEventListener('change', handler.fn);
                });
                if (globalDestination) {
                    globalDestination.hidden = false;
                }
                if (perEmailDestination) {
                    perEmailDestination.hidden = true;
                    perEmailDestination.innerHTML = '';
                }
            }

            function onSaveToDocsToggle() {
                if (categorySelect) {
                    categorySelect.disabled = !saveToDocsCheckbox.checked;
                }
            }

            function getPerEmailStoragePrefs() {
                const prefs = {};
                if (!perEmailDestination) {
                    return prefs;
                }
                emailGroups.forEach(function(group) {
                    const checkbox = perEmailDestination.querySelector('.attachment-email-save-docs[data-email-key="' + group.key + '"]');
                    const select = perEmailDestination.querySelector('.attachment-email-category[data-email-key="' + group.key + '"]');
                    const saveToDocs = checkbox && checkbox.checked;
                    const categoryId = select ? parseInt(select.value, 10) : 0;
                    prefs[group.key] = {
                        saveToDocs: saveToDocs,
                        categoryId: categoryId,
                        storageType: (saveToDocs && categoryId > 0) ? 'documents' : 'email'
                    };
                });
                return prefs;
            }

            function onCancel() {
                closeModal();
                resolve(null);
            }

            function onConfirm() {
                let globalStorageType = 'email';
                let globalCategoryId = 0;
                if (!usePerEmailCategories) {
                    const saveToDocs = saveToDocsCheckbox && saveToDocsCheckbox.checked;
                    globalCategoryId = categorySelect ? parseInt(categorySelect.value, 10) : 0;
                    globalStorageType = (saveToDocs && globalCategoryId > 0) ? 'documents' : 'email';
                }
                const perEmailPrefs = usePerEmailCategories ? getPerEmailStoragePrefs() : {};
                const rows = body.querySelectorAll('tr[data-original-filename]');
                const storageList = [];
                rows.forEach(function(row) {
                    const originalFilename = row.getAttribute('data-original-filename');
                    const emailFilename = row.getAttribute('data-email-filename');
                    const emailKey = row.getAttribute('data-email-key');
                    const input = row.querySelector('.attachment-rename-input');
                    const fileName = input ? input.value.trim() : '';
                    let storageType = globalStorageType;
                    let categoryId = globalStorageType === 'documents' ? globalCategoryId : null;
                    if (usePerEmailCategories && emailKey && perEmailPrefs[emailKey]) {
                        storageType = perEmailPrefs[emailKey].storageType;
                        categoryId = storageType === 'documents' ? perEmailPrefs[emailKey].categoryId : null;
                    }
                    const entry = {
                        original_filename: originalFilename,
                        filename: originalFilename,
                        file_name: fileName || originalFilename,
                        storage_type: storageType,
                        category_id: categoryId
                    };
                    if (emailFilename) {
                        entry.email_filename = emailFilename;
                    }
                    storageList.push(entry);
                });
                closeModal();
                resolve(storageList);
            }

            if (!usePerEmailCategories && saveToDocsCheckbox) {
                saveToDocsCheckbox.addEventListener('change', onSaveToDocsToggle);
            }
            confirmBtn.addEventListener('click', onConfirm);
            cancelBtn.addEventListener('click', onCancel);

            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');
        });
    }

    function updateEmailUploadLoading(title, message, filename, progressPercent) {
        const titleEl = document.getElementById('emailUploadLoadingTitleV2');
        const messageEl = document.getElementById('emailUploadLoadingMessageV2');
        const filenameEl = document.getElementById('emailUploadLoadingFilenameV2');
        const progressBar = document.getElementById('emailUploadLoadingProgressBarV2');

        if (titleEl && title) {
            titleEl.textContent = title;
        }
        if (messageEl && message) {
            messageEl.textContent = message;
        }
        if (filenameEl) {
            filenameEl.textContent = filename || '';
        }
        if (progressBar) {
            const pct = Math.max(0, Math.min(100, Number(progressPercent) || 0));
            progressBar.style.width = pct + '%';
        }
    }

    function showEmailUploadLoading(title, message, filename, progressPercent) {
        const overlay = document.getElementById('emailUploadLoadingOverlayV2');
        if (!overlay) {
            return;
        }
        updateEmailUploadLoading(title, message, filename, progressPercent);
        overlay.classList.add('active');
        overlay.setAttribute('aria-hidden', 'false');
        overlay.setAttribute('aria-busy', 'true');
        document.body.classList.add('email-upload-in-progress');
    }

    function hideEmailUploadLoading() {
        const overlay = document.getElementById('emailUploadLoadingOverlayV2');
        const progressBar = document.getElementById('emailUploadLoadingProgressBarV2');
        if (!overlay) {
            return;
        }
        overlay.classList.remove('active');
        overlay.setAttribute('aria-hidden', 'true');
        overlay.setAttribute('aria-busy', 'false');
        document.body.classList.remove('email-upload-in-progress');
        if (progressBar) {
            progressBar.style.width = '0%';
        }
    }

    /**
     * Process one email file with loading overlay updates (duplicate modals pause overlay).
     * attachmentStorage is prepared before the upload loop (batch modal for multi-upload).
     */
    async function processSingleEmailUpload(file, fileIndex, totalFiles, attachmentStorage) {
        const baseProgress = totalFiles > 0 ? Math.round((fileIndex / totalFiles) * 100) : 0;
        attachmentStorage = attachmentStorage || [];

        updateEmailUploadLoading(
            'Uploading email',
            'Uploading and processing email…',
            file.name,
            baseProgress + (totalFiles > 0 ? Math.round(50 / totalFiles) : 0)
        );

        let result = await uploadSingleEmailFile(file, false, attachmentStorage);
        let duplicateError = getDuplicateUploadError(result);

        if (duplicateError) {
            hideEmailUploadLoading();
            const acceptUpload = await showDuplicateEmailPrompt(file.name);
            if (acceptUpload) {
                showEmailUploadLoading(
                    'Uploading email',
                    'Uploading duplicate email…',
                    file.name,
                    baseProgress
                );
                result = await uploadSingleEmailFile(file, true, attachmentStorage);
                duplicateError = getDuplicateUploadError(result);
            } else {
                return {
                    rejected: 1,
                    uploaded: 0,
                    failed: 0,
                    duplicateError: duplicateError,
                    errors: [{
                        filename: file.name,
                        error: DUPLICATE_EXISTS_MESSAGE,
                        duplicate: true
                    }]
                };
            }
        }

        const uploadedCount = result.uploaded || 0;
        const failedCount = result.failed || 0;
        const errors = Array.isArray(result.errors) ? result.errors.slice() : [];
        let extraFailed = 0;

        if (!result.status && uploadedCount === 0 && failedCount === 0 && !duplicateError) {
            extraFailed = 1;
            errors.push({
                filename: file.name,
                error: result.message || 'Upload failed'
            });
        }

        return {
            rejected: 0,
            uploaded: uploadedCount,
            failed: failedCount + extraFailed,
            duplicateError: duplicateError,
            errors: errors
        };
    }

    /**
     * Upload files to server (one at a time — supports duplicate prompt per file).
     */
    async function uploadFiles(files) {
        const clientId = getEntityId();

        if (!clientId) {
            showNotification('Client ID not found', 'error');
            return;
        }

        isUploading = true;

        const fileStatus = document.getElementById('fileStatusV2');
        const uploadProgress = document.getElementById('upload-progress-v2');
        const fileCountBadge = document.getElementById('file-count-v2');
        const fileInput = document.getElementById('emailV2FileInput');
        const allowedLabel = getAllowedExtensionsLabel();
        let overlayHideDelay = 900;

        if (uploadProgress) {
            uploadProgress.className = 'upload-progress uploading';
        }

        showEmailUploadLoading(
            'Uploading email',
            'Preparing to upload ' + files.length + ' email' + (files.length > 1 ? 's' : '') + '…',
            '',
            0
        );

        let uploadedTotal = 0;
        let failedTotal = 0;
        let rejectedTotal = 0;
        const allErrors = [];
        let attachmentStorageByEmail = {};

        try {
            if (getEntityType() !== 'partner') {
                updateEmailUploadLoading(
                    'Uploading email',
                    files.length > 1
                        ? 'Analyzing attachments for ' + files.length + ' emails…'
                        : 'Analyzing email attachments…',
                    '',
                    0
                );

                const previewsByEmail = await previewBatchEmailAttachments(files);
                const flatAttachments = flattenBatchAttachments(previewsByEmail);

                if (flatAttachments.length > 0) {
                    hideEmailUploadLoading();
                    const modalResult = await showAttachmentStorageModal(flatAttachments, {
                        emailCount: files.length
                    });
                    if (modalResult === null) {
                        rejectedTotal = files.length;
                        if (uploadProgress) {
                            uploadProgress.className = 'upload-progress error';
                        }
                        if (fileStatus) {
                            fileStatus.textContent = 'Upload skipped';
                        }
                        hideEmailUploadLoading();
                        overlayHideDelay = 0;
                        return;
                    }
                    attachmentStorageByEmail = groupAttachmentStorageByEmail(modalResult);
                    showEmailUploadLoading(
                        'Uploading email',
                        'Preparing to upload ' + files.length + ' email' + (files.length > 1 ? 's' : '') + '…',
                        '',
                        0
                    );
                }
            }

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const progressPct = Math.round((i / files.length) * 100);

                showEmailUploadLoading(
                    'Uploading email',
                    'Processing email ' + (i + 1) + ' of ' + files.length,
                    file.name,
                    progressPct
                );

                if (fileStatus) {
                    fileStatus.textContent = 'Uploading ' + (i + 1) + ' of ' + files.length + ': ' + file.name;
                }

                const fileResult = await processSingleEmailUpload(
                    file,
                    i,
                    files.length,
                    attachmentStorageByEmail[file.name] || []
                );

                uploadedTotal += fileResult.uploaded || 0;
                failedTotal += fileResult.failed || 0;
                rejectedTotal += fileResult.rejected || 0;

                if (Array.isArray(fileResult.errors)) {
                    fileResult.errors.forEach(function(err) {
                        allErrors.push(err);
                    });
                }

                updateEmailUploadLoading(
                    'Uploading email',
                    'Completed ' + (i + 1) + ' of ' + files.length,
                    file.name,
                    Math.round(((i + 1) / files.length) * 100)
                );
            }

            if (uploadedTotal > 0 && failedTotal === 0 && rejectedTotal === 0) {
                if (uploadProgress) uploadProgress.className = 'upload-progress success';
                if (fileStatus) fileStatus.textContent = 'Upload successful!';
                updateEmailUploadLoading('Upload complete', 'Your email was uploaded successfully.', '', 100);
                overlayHideDelay = 600;
                showNotification(
                    uploadedTotal === 1 ? 'Successfully uploaded 1 email' : 'Successfully uploaded ' + uploadedTotal + ' emails',
                    'success'
                );
                clearAllSelectedLabels();
                setTimeout(function() {
                    if (fileInput) fileInput.value = '';
                    if (fileStatus) fileStatus.textContent = 'Ready to upload';
                    if (uploadProgress) uploadProgress.className = 'upload-progress';
                    if (fileCountBadge) fileCountBadge.classList.remove('show');
                }, 2000);
                loadEmailsFromServer();
            } else if (uploadedTotal > 0) {
                if (uploadProgress) uploadProgress.className = 'upload-progress error';
                if (fileStatus) fileStatus.textContent = 'Upload completed with errors';
                updateEmailUploadLoading('Upload finished', 'Some emails were uploaded with issues.', '', 100);
                showNotification(
                    'Partially successful: ' + uploadedTotal + ' uploaded, ' + (failedTotal + rejectedTotal) + ' skipped/failed',
                    'error'
                );
                loadEmailsFromServer();
            } else if (rejectedTotal > 0 && failedTotal === 0) {
                if (uploadProgress) uploadProgress.className = 'upload-progress error';
                if (fileStatus) fileStatus.textContent = 'Upload skipped';
                hideEmailUploadLoading();
                overlayHideDelay = 0;
            } else {
                if (uploadProgress) uploadProgress.className = 'upload-progress error';
                if (fileStatus) fileStatus.textContent = 'Upload failed';
                updateEmailUploadLoading('Upload failed', 'The email could not be uploaded.', '', 100);
                let errorMessage = 'Upload failed';
                if (allErrors.length > 0) {
                    errorMessage += '\n\n' + allErrors.map(function(err, index) {
                        return (index + 1) + '. ' + (err.filename || 'Unknown') + ': ' + (err.error || 'Unknown error');
                    }).join('\n');
                }
                if (failedTotal > 0 && uploadedTotal === 0) {
                    errorMessage += '\n\n💡 Tip: Ensure the Python service is running and files are valid Outlook emails (' + allowedLabel + ').';
                }
                showNotification(errorMessage, 'error');
                setTimeout(function() {
                    if (fileStatus) fileStatus.textContent = 'Ready to upload';
                    if (uploadProgress) uploadProgress.className = 'upload-progress';
                    if (fileCountBadge && uploadedTotal === 0) fileCountBadge.classList.remove('show');
                }, 5000);
            }
        } catch (error) {
            console.error('Upload error:', error);
            if (uploadProgress) uploadProgress.className = 'upload-progress error';
            if (fileStatus) fileStatus.textContent = 'Upload failed';
            const msg = error && error.message ? String(error.message) : 'Unknown error';
            updateEmailUploadLoading('Upload failed', msg.indexOf('Upload') === 0 ? msg : 'Upload failed: ' + msg, '', 100);
            showNotification(msg.indexOf('Upload') === 0 ? msg : 'Upload failed: ' + msg, 'error');
            setTimeout(function() {
                if (fileStatus) fileStatus.textContent = 'Ready to upload';
                if (uploadProgress) uploadProgress.className = 'upload-progress';
            }, 3000);
        } finally {
            isUploading = false;
            if (overlayHideDelay > 0) {
                setTimeout(function() {
                    hideEmailUploadLoading();
                }, overlayHideDelay);
            }
        }
    }

    // =========================================================================
    // Search Functionality
    // =========================================================================

    /**
     * Initialize search functionality
     */
    window.initializeSearch = function() {
        console.log('Initializing search module...');

        const searchInput = document.getElementById('emailV2SearchInput');
        const labelFilter = document.getElementById('labelV2Filter');

        if (!searchInput) {
            console.warn('Search input not found - skipping search initialization');
            return;
        }
        
        if (!labelFilter) {
            console.warn('Label filter not found - search will work with limited functionality');
        }

        // Real-time search (debounced)
        const debouncedSearch = debounce(function() {
            currentSearch = searchInput.value;
            currentPage = 1;
            loadEmailsFromServer();
        }, 500);

        searchInput.addEventListener('input', debouncedSearch);

        // Label filter change - auto-applies when changed
        if (labelFilter) {
            labelFilter.addEventListener('change', function() {
                currentLabelId = this.value;
                currentPage = 1;
                console.log('Label filter changed to:', currentLabelId);
                loadEmailsFromServer();
            });
        }

        console.log('Search module initialized');
    };

    // =========================================================================
    // Email List Functionality
    // =========================================================================

    /**
     * Initialize email list and load initial emails
     */
    window.loadEmailsV2 = function() {
        // Check if email interface exists on this page before attempting to load
        const container = document.querySelector('.email-v2-interface-container');
        if (!container) {
            // Page doesn't support emails - silently return
            return;
        }
        
        // Check if required attributes are present
        if (!container.dataset.entityId) {
            // Email interface container exists but is not properly configured
            // This page may not be set up for emails yet
            return;
        }
        
        console.log('Loading emails...');
        loadEmailsFromServer();
    };

    /**
     * Fetch and display emails from server
     */
    async function loadEmailsFromServer() {
        const clientId = getEntityId();
        
        if (!clientId) {
            // Client ID not available - page may not support emails
            // Don't show warning as this is expected on pages without email interface
            return;
        }

        if (isLoading) {
            console.log('Already loading emails');
            return;
        }

        isLoading = true;
        updateLoadingState(true);

        try {
            // Determine endpoint based on mail type
            const endpoint = currentMailType === 'sent' 
                ? '/email-v2/filter-sentemails' 
                : '/email-v2/filter-emails';

            const requestBody = {
                client_id: clientId,
                type: getEntityType(),
                search: currentSearch,
                status: '', // Keep for backward compatibility (mail_is_read)
                label_id: currentLabelId
            };
            if (showEmailCategoryTabs()) {
                requestBody.email_category = currentEmailCategory;
            }

            console.log('Fetching emails from:', endpoint, requestBody);

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestBody)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const emails = await response.json();
            console.log('Emails received:', emails);
            
            // Debug: Check attachments in received emails
            emails.forEach((email, index) => {
                if (email.attachments && email.attachments.length > 0) {
                    console.log(`Email ${index} (ID: ${email.id}) has ${email.attachments.length} attachments`);
                }
            });

            // Apply sorting
            const sortedEmails = sortEmails(emails);

            // Render emails
            renderEmails(sortedEmails);

            // Update counts
            updateEmailCounts(sortedEmails.length);

        } catch (error) {
            console.error('Error loading emails:', error);
            showNotification('Failed to load emails: ' + error.message, 'error');
            renderEmptyState('Error loading emails');
        } finally {
            isLoading = false;
            updateLoadingState(false);
        }
    }

    /**
     * Sort emails based on current sort option
     */
    function sortEmails(emails) {
        if (!Array.isArray(emails)) {
            console.error('Emails is not an array:', emails);
            return [];
        }

        return emails.slice().sort((a, b) => {
            switch (currentSort) {
                case 'subject':
                    return (a.subject || '').localeCompare(b.subject || '');
                case 'sender':
                    return (a.from_mail || '').localeCompare(b.from_mail || '');
                case 'date':
                default:
                    // Use sent date for sorting, fallback to created_at
                    const getDateForSort = (email) => {
                        if (email.fetch_mail_sent_time) {
                            // Parse formatted date: "dd/mm/yyyy hh:mm am/pm"
                            const parts = email.fetch_mail_sent_time.match(/^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2}) (am|pm)$/i);
                            if (parts) {
                                const [, day, month, year, hour, minute, ampm] = parts;
                                let hour24 = parseInt(hour);
                                if (ampm.toLowerCase() === 'pm' && hour24 !== 12) hour24 += 12;
                                if (ampm.toLowerCase() === 'am' && hour24 === 12) hour24 = 0;
                                return new Date(year, month - 1, day, hour24, minute);
                            }
                        }
                        if (email.received_date) {
                            return new Date(email.received_date);
                        }
                        return new Date(email.created_at || 0);
                    };
                    const dateA = getDateForSort(a);
                    const dateB = getDateForSort(b);
                    return dateB - dateA; // Newest first
            }
        });
    }

    /**
     * Render emails in the list
     */
    function renderEmails(emails) {
        const emailList = document.getElementById('emailListV2');
        if (!emailList) {
            console.error('Email list element not found');
            return;
        }

        // Clear existing content
        emailList.innerHTML = '';

        if (!emails || emails.length === 0) {
            renderEmptyState();
            return;
        }

        emails.forEach(email => {
            const emailItem = createEmailItem(email);
            emailList.appendChild(emailItem);
        });
    }

    function updateEmailCounts(total) {
        const resultsCount = document.getElementById('resultsCountV2');
        const pageInfo = document.getElementById('pageInfoV2');
        if (resultsCount) {
            resultsCount.textContent = total + ' result' + (total !== 1 ? 's' : '');
        }
        if (pageInfo) {
            if (total === 0) {
                pageInfo.textContent = 'Showing 0';
            } else {
                pageInfo.textContent = 'Showing 1-' + total + ' of ' + total;
            }
        }
    }

    function normalizePreviewText(text, maxLen) {
        if (!text) {
            return '';
        }
        let cleaned = String(text).replace(/\s+/g, ' ').trim();
        if (maxLen && cleaned.length > maxLen) {
            cleaned = cleaned.substring(0, maxLen).trim() + '…';
        }
        return cleaned;
    }

    function getEmailPreviewText(email, maxLen) {
        let text = email.text_preview || '';
        if (!text && email.message) {
            text = String(email.message).replace(/<[^>]+>/g, ' ');
        }
        return normalizePreviewText(text, maxLen || 80);
    }

    function formatRecipientLine(label, value) {
        const cleaned = cleanRecipients(value);
        if (!cleaned) {
            return '';
        }
        return label + ': ' + cleaned;
    }

    function renderEmailAttachmentListSummary(email) {
        const items = collectEmailAttachmentItems(email);
        if (!items.length) {
            return '';
        }

        const lines = items.slice(0, 3).map(function(item) {
            return '<span class="email-item-attachment-line">' +
                crmIcon('file', 'solid', { class: 'email-item-attachment-icon' }) +
                ' ' + escapeHtml(item.name) + '</span>';
        }).join('');

        const extra = items.length > 3
            ? '<span class="email-item-attachment-more">+' + (items.length - 3) + ' more</span>'
            : '';

        return '<div class="email-item-attachments">' + lines + extra + '</div>';
    }

    function renderReadingPaneAttachments(email) {
        const items = collectEmailAttachmentItems(email);
        if (!items.length) {
            return '';
        }

        const subject = email.subject || '(No subject)';
        const regularAttachments = items.filter(function(item) { return !item.isSourceFile; });
        const rows = items.map(function(item, attIndex) {
            const sizeLabel = item.size ? formatFileSize(item.size) : '';
            let actionsHtml = '';

            if (item.isSourceFile) {
                const link = item.downloadUrl || '#';
                actionsHtml = '<a href="' + escapeHtml(link) + '" target="_blank" rel="noopener noreferrer" ' +
                    'class="email-attachment-btn email-attachment-btn--download">' +
                    crmIcon('download') + ' Download</a>';
                if (item.previewUrl) {
                    actionsHtml += '<a href="' + escapeHtml(item.previewUrl) + '" target="_blank" rel="noopener noreferrer" ' +
                        'class="email-attachment-btn email-attachment-btn--preview">' +
                        crmIcon('eye') + ' Preview</a>';
                }
            } else {
                const att = item.attachment;
                const downloadName = resolveAttachmentDownloadFilename(att);
                const hasNumericId = att.id !== null && att.id !== undefined && /^\d+$/.test(String(att.id));
                if (hasNumericId) {
                    actionsHtml = '<button type="button" class="email-attachment-btn email-attachment-btn--download download-attachment-btn" ' +
                        'data-attachment-id="' + att.id + '" data-mail-report-id="' + email.id + '" ' +
                        'data-filename="' + escapeHtml(downloadName) + '">' +
                        crmIcon('download') + ' Download</button>';
                    if (attachmentSupportsBrowserPreview(att)) {
                        actionsHtml += '<button type="button" class="email-attachment-btn email-attachment-btn--preview preview-attachment-btn" ' +
                            'data-attachment-id="' + att.id + '" data-filename="' + escapeHtml(downloadName) + '">' +
                            crmIcon('eye') + ' Preview</button>';
                    }
                } else if (item.downloadUrl) {
                    actionsHtml = '<a href="' + escapeHtml(item.downloadUrl) + '" target="_blank" rel="noopener noreferrer" ' +
                        'class="email-attachment-btn email-attachment-btn--download">' +
                        crmIcon('download') + ' Download</a>';
                } else {
                    actionsHtml = '<button type="button" class="email-attachment-btn email-attachment-btn--download download-attachment-btn" ' +
                        'data-mail-report-id="' + email.id + '" data-legacy-index="' + attIndex + '" ' +
                        'data-filename="' + escapeHtml(downloadName) + '">' +
                        crmIcon('download') + ' Download</button>';
                }
            }

            const iconName = item.isSourceFile ? 'file' : getAttachmentIcon(item.attachment ? item.attachment.content_type : '');

            return '<div class="email-attachment-row">' +
                '<div class="email-attachment-row__icon">' + crmIcon(iconName, 'solid') + '</div>' +
                '<div class="email-attachment-row__info">' +
                '<div class="email-attachment-row__name" title="' + escapeHtml(item.name) + '">' + escapeHtml(item.name) + '</div>' +
                (sizeLabel ? '<div class="email-attachment-row__meta">' + escapeHtml(sizeLabel) + '</div>' : '') +
                '</div>' +
                '<div class="email-attachment-row__actions">' + actionsHtml + '</div>' +
                '</div>';
        }).join('');

        let headerExtra = '';
        if (regularAttachments.length > 1) {
            headerExtra = '<button type="button" class="email-attachment-btn email-attachment-btn--download download-all-btn" ' +
                'data-mail-report-id="' + email.id + '" data-email-subject="' + escapeHtml(subject) + '">' +
                crmIcon('download') + ' Download All</button>';
        }

        return '<div class="email-attachments-panel">' +
            '<div class="email-attachments-panel__header">' +
            crmIcon('paperclip') + ' <span>Attachments (' + items.length + ')</span>' +
            headerExtra +
            '</div>' +
            '<div class="email-attachments-panel__list">' + rows + '</div>' +
            '</div>';
    }

    function updateReadingPaneActions(email) {
        const deleteBtn = document.getElementById('btnDeleteEmailV2');
        if (deleteBtn) {
            deleteBtn.style.display = isEmailAdminUser() ? 'inline-flex' : 'none';
        }
        currentReadingEmail = email;
        currentContextEmail = email;
    }

    function resetReadingPane() {
        const placeholder = document.getElementById('emailContentPlaceholderV2');
        const readingPane = document.getElementById('emailContentViewV2');
        if (readingPane) {
            readingPane.classList.remove('is-visible');
        }
        if (placeholder) {
            placeholder.hidden = false;
            placeholder.style.display = '';
        }
        selectedEmailId = null;
        currentReadingEmail = null;
    }

    /**
     * Create email list item element (Outlook-style)
     */
    function createEmailItem(email) {
        const div = document.createElement('div');
        const isRead = email.mail_is_read == 1;
        div.className = 'email-item' + (isRead ? '' : ' unread');
        if (selectedEmailId === email.id) {
            div.classList.add('active');
        }
        div.dataset.emailId = email.id;

        const sender = email.from_mail || 'Unknown';
        const subject = email.subject || '(No Subject)';
        const preview = getEmailPreviewText(email, 80);
        const hasAttachment = (email.attachments && email.attachments.length > 0) ||
            email.msg_file_url || email.pdf_file_url || email.preview_url;
        const attachmentIcon = hasAttachment
            ? crmIcon('paperclip', 'solid', { class: 'email-list-clip', attrs: { title: 'Has attachments' } })
            : '';
        const attachmentSummary = renderEmailAttachmentListSummary(email);
        const dateStr = formatDate(getEmailDate(email));

        const labelBadges = (email.labels && Array.isArray(email.labels))
            ? email.labels.map(function(label) {
                return '<span class="label-badge" style="background-color: ' + label.color + '20; border-color: ' + label.color + '; color: ' + label.color + '">' +
                    labelIconHtml(label.icon) + ' ' + escapeHtml(label.name) + '</span>';
            }).join('')
            : '';

        div.innerHTML =
            '<div class="email-item-header">' +
            '<div class="email-sender">' + escapeHtml(sender) + attachmentIcon + '</div>' +
            '</div>' +
            '<div class="email-subject">' + escapeHtml(subject) + '</div>' +
            (preview ? '<div class="email-preview">' + escapeHtml(preview) + '</div>' : '') +
            (labelBadges ? '<div class="email-item-labels">' + labelBadges + '</div>' : '') +
            '<div class="email-item-footer">' +
            attachmentSummary +
            '<div class="email-date">' + dateStr + '</div>' +
            '</div>';

        div.addEventListener('click', function(e) {
            const contextMenu = document.getElementById('emailContextMenuV2');
            if (contextMenu && contextMenu.style.display === 'block') {
                hideContextMenu();
                return;
            }

            document.querySelectorAll('.email-item').forEach(function(item) {
                item.classList.remove('selected', 'active');
            });

            this.classList.add('active', 'selected');
            selectedEmailId = email.id;
            loadEmailDetail(email);
        });

        div.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.dataset.emailData = JSON.stringify(email);
            showContextMenu(e.clientX, e.clientY, email);
        });

        return div;
    }

    /**
     * Render empty state
     */
    function renderEmptyState(message = null) {
        const emailList = document.getElementById('emailListV2');
        if (!emailList) return;

        emailList.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">
                    ${crmIcon('inbox')}
                </div>
                <div class="empty-state-text">
                    <h3>${message || 'No emails found'}</h3>
                    <p>${message ? 'Please try again.' : 'Upload ' + getAllowedExtensionsLabel() + ' files to get started with email management.'}</p>
                </div>
            </div>
        `;
    }

    /**
     * Update loading state visual indicator
     */
    function updateLoadingState(loading) {
        const emailList = document.getElementById('emailListV2');
        if (!emailList) return;

        if (loading) {
            emailList.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">
                        ${spinnerHtml()}
                    </div>
                    <div class="empty-state-text">
                        <h3>Loading emails...</h3>
                        <p>Please wait</p>
                    </div>
                </div>
            `;
        }
    }

    /**
     * Collect all attachment-like items including original .msg/.eml and parsed PDF.
     */
    function collectEmailAttachmentItems(email) {
        const items = [];

        const msgUrl = email.msg_file_url || email.preview_url;
        if (msgUrl) {
            items.push({
                key: 'original-msg',
                name: 'Original email file',
                size: null,
                downloadUrl: msgUrl,
                previewUrl: null,
                isSourceFile: true
            });
        }

        if (email.pdf_file_url || email.pdf_preview_url) {
            items.push({
                key: 'parsed-pdf',
                name: 'Parsed email (PDF)',
                size: null,
                downloadUrl: email.pdf_file_url || email.pdf_preview_url,
                previewUrl: email.pdf_preview_url || email.pdf_file_url,
                isSourceFile: true
            });
        }

        (email.attachments || []).forEach(function(att) {
            if (att.is_inline) {
                return;
            }
            const hasNumericId = att.id !== null && att.id !== undefined && /^\d+$/.test(String(att.id));
            items.push({
                id: att.id,
                key: 'att-' + (att.id || att.filename),
                name: att.display_name || att.filename || 'Attachment',
                size: att.file_size,
                downloadUrl: hasNumericId ? '/email-v2/attachments/' + att.id + '/download' : null,
                previewUrl: hasNumericId && attachmentSupportsBrowserPreview(att)
                    ? '/email-v2/attachments/' + att.id + '/preview'
                    : null,
                attachment: att,
                isSourceFile: false
            });
        });

        return items;
    }

    function renderHtmlIframe(iframe, html) {
        if (!iframe) {
            return;
        }
        iframe.style.height = '100%';
        iframe.style.minHeight = '320px';
        iframe.removeAttribute('src');
        const doc = iframe.contentDocument || (iframe.contentWindow && iframe.contentWindow.document);
        if (!doc) {
            return;
        }
        const bodyHtml = html || '';
        doc.open();
        doc.write('<!DOCTYPE html><html><head><meta charset="utf-8"><base target="_blank"><style>' +
            'html,body{height:100%;margin:0;padding:0;box-sizing:border-box;}' +
            'body{font-family:"Segoe UI",-apple-system,BlinkMacSystemFont,sans-serif;font-size:14px;line-height:1.6;color:#242424;word-wrap:break-word;overflow-wrap:break-word;padding:16px 20px;overflow-y:auto;}' +
            'img{max-width:100%;height:auto;}' +
            'table{max-width:100%;}' +
            'a{color:#0078d4;}' +
            'blockquote{margin:0;padding-left:12px;border-left:3px solid #edebe9;color:#605e5c;}' +
            'p{margin:0 0 0.75em;}' +
            '</style></head><body>' + bodyHtml + '</body></html>');
        doc.close();
    }

    function renderEmailBodyInIframe(email, messageHtml, allAttachments) {
        const iframe = document.getElementById('emailReadBodyV2');
        if (!iframe) {
            return;
        }

        let contentStr = (messageHtml || '').trim();
        let pdfToPreview = null;

        if (!contentStr || contentStr === '(No content)') {
            if (email.pdf_preview_url || email.pdf_file_url) {
                pdfToPreview = email.pdf_preview_url || email.pdf_file_url;
            }
        }

        if (pdfToPreview) {
            iframe.onload = null;
            iframe.removeAttribute('srcdoc');
            iframe.style.minHeight = '480px';
            iframe.src = pdfToPreview;
            return;
        }

        iframe.removeAttribute('src');
        let bodyHtml = replaceCidReferences(contentStr, allAttachments);
        if (bodyHtml && bodyHtml.indexOf('<') === -1) {
            bodyHtml = escapeHtml(bodyHtml).replace(/\n/g, '<br>');
        }
        renderHtmlIframe(iframe, bodyHtml || '<p>No content available.</p>');
    }

    /**
     * Load and display email details with attachments
     */
    function loadEmailDetail(email) {
        const readingPane = document.getElementById('emailContentViewV2');
        const placeholder = document.getElementById('emailContentPlaceholderV2');

        if (!readingPane || !placeholder) {
            console.error('Email detail elements not found');
            return;
        }

        selectedEmailId = email.id;
        updateReadingPaneActions(email);

        placeholder.hidden = true;
        placeholder.style.display = 'none';
        readingPane.classList.add('is-visible');

        const subjectEl = document.getElementById('readSubjectV2');
        const senderEl = document.getElementById('readSenderV2');
        const toEl = document.getElementById('readToV2');
        const ccEl = document.getElementById('readCcV2');
        const dateEl = document.getElementById('readDateV2');
        const avatarEl = document.getElementById('readAvatarV2');
        const attachmentsContainer = document.getElementById('attachmentsContainerV2');

        if (subjectEl) {
            subjectEl.textContent = email.subject || '(No Subject)';
        }
        if (senderEl) {
            senderEl.textContent = email.from_mail || 'Unknown Sender';
        }
        if (toEl) {
            const toLine = formatRecipientLine('To', email.to_mail);
            toEl.textContent = toLine || 'To: Unknown';
        }
        if (ccEl) {
            const ccLine = formatRecipientLine('Cc', email.cc);
            if (ccLine) {
                ccEl.textContent = ccLine;
                ccEl.hidden = false;
            } else {
                ccEl.textContent = '';
                ccEl.hidden = true;
            }
        }
        if (dateEl) {
            dateEl.textContent = formatDate(getEmailDate(email));
        }
        if (avatarEl) {
            avatarEl.textContent = (email.from_mail || '?').charAt(0).toUpperCase();
        }

        const attachmentHtml = renderReadingPaneAttachments(email);
        if (attachmentsContainer) {
            if (attachmentHtml) {
                attachmentsContainer.hidden = false;
                attachmentsContainer.innerHTML = attachmentHtml;
            } else {
                attachmentsContainer.hidden = true;
                attachmentsContainer.innerHTML = '';
            }
        }

        const allAttachments = email.attachments && Array.isArray(email.attachments) ? email.attachments : [];
        const message = email.message || email.rendered_html || '';
        renderEmailBodyInIframe(email, message, allAttachments);
    }

    // 1x1 transparent GIF - used as fallback when cid: cannot be resolved (avoids ERR_UNKNOWN_URL_SCHEME)
    const TRANSPARENT_PIXEL = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

    /**
     * Replace cid: references in email HTML with actual preview URLs for inline attachments.
     * Browsers cannot load cid: URLs; unresolved refs are replaced with a transparent pixel.
     */
    function replaceCidReferences(htmlContent, attachments) {
        if (!htmlContent) return htmlContent;

        // Build lookup map when we have attachments
        const cidMap = {};
        if (attachments && attachments.length > 0) {
            attachments.forEach(att => {
                if (!att.id) return;
                if (att.filename) {
                    const filenameKey = att.filename.toLowerCase();
                    cidMap[filenameKey] = att;
                    const filenameWithoutExt = filenameKey.replace(/\.[^.]+$/, '');
                    if (filenameWithoutExt !== filenameKey) cidMap[filenameWithoutExt] = att;
                }
                if (att.content_id) {
                    const normalized = att.content_id.replace(/^<|>$/g, '').trim().toLowerCase();
                    if (normalized) cidMap[normalized] = att;
                }
            });
        }

        function findAttachment(cidValue) {
            const normalized = cidValue.replace(/^<|>$/g, '').trim().toLowerCase();
            let att = cidMap[normalized] || cidMap[normalized.replace(/:\d+$/, '')];
            if (!att && normalized.includes('@')) {
                att = cidMap[normalized.split('@')[0]];
            }
            return att;
        }

        // Replace cid: in img src (always replace to prevent ERR_UNKNOWN_URL_SCHEME)
        // Handles: src="cid:...", src='cid:...', src=cid:... (unquoted)
        htmlContent = htmlContent.replace(/src=(["']?)cid:([^"'>\s]+)\1?/gi, (match, quote, cidValue) => {
            const attachment = findAttachment(cidValue);
            if (attachment && attachment.id) {
                return `src="/email-v2/attachments/${attachment.id}/preview"`;
            }
            return `src="${TRANSPARENT_PIXEL}"`;
        });

        // Replace cid: in background-image CSS
        htmlContent = htmlContent.replace(/background-image:\s*url\(["']?cid:([^"')]+)["']?\)/gi, (match, cidValue) => {
            const attachment = findAttachment(cidValue);
            if (attachment && attachment.id) {
                return `background-image: url("/email-v2/attachments/${attachment.id}/preview")`;
            }
            return 'background-image: none';
        });

        return htmlContent;
    }

    /**
     * Clean recipient strings by removing Python object representations
     */
    function cleanRecipients(recipientString) {
        if (!recipientString) return '';
        
        // Split by comma to handle multiple recipients
        const recipients = recipientString.split(',');
        
        // Filter out invalid recipients (Python object strings, malformed addresses)
        const validRecipients = recipients
            .map(r => r.trim())
            .filter(r => {
                // Remove entries that look like Python object representations
                if (r.includes('<extract_msg.') || r.includes('object at 0x')) {
                    return false;
                }
                // Remove entries that look like raw object references
                if (r.includes('Recipient') && r.includes('0x')) {
                    return false;
                }
                // Keep only entries that look like valid email addresses or names
                return r.length > 0 && !r.startsWith('<') && !r.includes('0x');
            });
        
        // Return cleaned recipient list or a placeholder if none are valid
        return validRecipients.length > 0 ? validRecipients.join(', ') : '';
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // =========================================================================
    // Pagination
    // =========================================================================

    function initializePagination() {
        const prevBtn = document.getElementById('prevBtnV2');
        const nextBtn = document.getElementById('nextBtnV2');

        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    loadEmailsFromServer();
                }
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                if (currentPage < lastPage) {
                    currentPage++;
                    loadEmailsFromServer();
                }
            });
        }
    }

    // =========================================================================
    // Context Menu Management
    // =========================================================================

    let currentContextEmail = null; // Store email object for context menu actions

    /**
     * Format reply subject (add "Re:" prefix if not already present)
     */
    function formatReplySubject(originalSubject) {
        if (!originalSubject) return 'Re:';
        const subject = originalSubject.trim();
        if (subject.toLowerCase().startsWith('re:')) {
            return subject;
        }
        return 'Re: ' + subject;
    }

    /**
     * Format forward subject (add "Fwd:" prefix if not already present)
     */
    function formatForwardSubject(originalSubject) {
        if (!originalSubject) return 'Fwd:';
        const subject = originalSubject.trim();
        if (subject.toLowerCase().startsWith('fwd:') || subject.toLowerCase().startsWith('fw:')) {
            return subject;
        }
        return 'Fwd: ' + subject;
    }

    /**
     * Format quoted message for reply/forward
     */
    function formatQuotedMessage(email, isForward = false) {
        const from = email.from_mail || 'Unknown';
        const to = cleanRecipients(email.to_mail) || 'Unknown';
        const date = formatDate(getEmailDate(email));
        const subject = email.subject || '(No subject)';
        const message = email.message || '(No content)';
        
        let quotedText = '';
        
        if (isForward) {
            // Forward format with headers
            quotedText = '\n\n---------- Forwarded message ----------\n';
            quotedText += 'From: ' + from + '\n';
            quotedText += 'To: ' + to + '\n';
            quotedText += 'Date: ' + date + '\n';
            quotedText += 'Subject: ' + subject + '\n\n';
        } else {
            // Reply format (simpler)
            quotedText = '\n\n';
        }
        
        // Add original message with quote markers
        quotedText += 'On ' + date + ', ' + from + ' wrote:\n';
        quotedText += '> ' + message.replace(/\n/g, '\n> ');
        
        return quotedText;
    }

    /**
     * Extract email address from a string (handles "Name <email@domain.com>" format)
     */
    function extractEmailAddress(emailString) {
        if (!emailString) return '';
        
        // Try to extract email from angle brackets
        const match = emailString.match(/<([^>]+)>/);
        if (match) {
            return match[1].trim();
        }
        
        // If no brackets, check if it's a valid email
        if (emailString.includes('@')) {
            return emailString.trim();
        }
        
        return emailString.trim();
    }

    /**
     * Open compose modal and populate fields
     */
    function openComposeModal(data) {
        const modal = document.getElementById('emailmodal');
        if (!modal) {
            showNotification('Compose email modal not found. Please ensure you are on the client detail page.', 'error');
            return;
        }

        // Set subject
        const subjectInput = document.getElementById('compose_email_subject');
        if (subjectInput && data.subject) {
            subjectInput.value = data.subject;
        }

        // Set message (for TinyMCE editor)
        const messageTextarea = document.querySelector('#compose_email_message');
        if (messageTextarea && data.message) {
            // Wait for modal to be fully shown before setting TinyMCE content
            const setMessageContent = () => {
                // If TinyMCE is initialized, update it
                if (typeof tinymce !== 'undefined' && tinymce.get('compose_email_message')) {
                    try {
                        tinymce.get('compose_email_message').setContent(data.message);
                    } catch (e) {
                        // If TinyMCE not ready, set value directly
                        messageTextarea.value = data.message;
                    }
                } else {
                    // Set the value directly if TinyMCE not initialized
                    messageTextarea.value = data.message;
                }
            };
            
            // If modal is already shown, set immediately, otherwise wait
            if (modal.classList.contains('show') || modal.style.display === 'block') {
                setTimeout(setMessageContent, 200);
            } else {
                // Wait for modal to be shown
                modal.addEventListener('shown.bs.modal', setMessageContent, { once: true });
                if (typeof jQuery !== 'undefined') {
                    jQuery(modal).on('shown.bs.modal', setMessageContent);
                }
            }
        }

        // Set "To" field (Tom Select via RecipientSelect — applied on shown.bs.modal)
        if (typeof jQuery !== 'undefined') {
            const $modal = jQuery(modal);
            if (data.to && data.to.length > 0 && typeof window.RecipientSelect !== 'undefined') {
                const emailAddresses = data.to.map(email => extractEmailAddress(email)).filter(addr => addr);
                const entries = emailAddresses.map(function (emailAddr) {
                    return RecipientSelect.buildEntry(emailAddr, emailAddr, emailAddr, 'Client');
                });
                if (typeof window.scheduleComposeEmailRecipients === 'function') {
                    window.scheduleComposeEmailRecipients(entries);
                } else {
                    $modal.data('composeRecipientsPending', entries);
                }
                $modal.removeData('composeSkipAutofill');
            } else if (typeof window.skipComposeEmailAutofill === 'function') {
                window.skipComposeEmailAutofill();
            } else {
                $modal.data('composeSkipAutofill', true);
                $modal.removeData('composeRecipientsPending');
            }
        }

        // Open modal using Bootstrap
        if (typeof jQuery !== 'undefined') {
            jQuery(modal).modal('show');
        } else if (typeof bootstrap !== 'undefined') {
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        } else {
            // Fallback: just show the modal
            modal.style.display = 'block';
            modal.classList.add('show');
        }
    }

    /**
     * Handle Reply action
     */
    function handleReply(email) {
        if (!email) {
            showNotification('No email selected for reply', 'error');
            return;
        }

        // Extract sender email for "To" field
        const senderEmail = extractEmailAddress(email.from_mail);
        if (!senderEmail) {
            showNotification('Could not extract sender email address', 'error');
            return;
        }

        // Format subject
        const replySubject = formatReplySubject(email.subject);

        // Format message with quoted original
        const replyMessage = formatQuotedMessage(email, false);

        // Open compose modal with reply data
        openComposeModal({
            to: [senderEmail],
            subject: replySubject,
            message: replyMessage
        });

        showNotification('Reply email opened', 'info');
    }

    /**
     * Parse comma-separated recipient string into unique email addresses.
     */
    function parseRecipientEmails(value) {
        const cleaned = cleanRecipients(value);
        if (!cleaned) {
            return [];
        }
        return cleaned.split(',').map(function(part) {
            return extractEmailAddress(part.trim());
        }).filter(Boolean);
    }

    /**
     * Handle Reply All action
     */
    function handleReplyAll(email) {
        if (!email) {
            showNotification('No email selected for reply', 'error');
            return;
        }

        const seen = {};
        const recipients = [];

        function addRecipient(value) {
            if (!value) {
                return;
            }
            const key = value.toLowerCase();
            if (!seen[key]) {
                seen[key] = true;
                recipients.push(value);
            }
        }

        parseRecipientEmails(email.from_mail).forEach(addRecipient);
        parseRecipientEmails(email.to_mail).forEach(addRecipient);
        parseRecipientEmails(email.cc).forEach(addRecipient);

        if (recipients.length === 0) {
            showNotification('Could not extract recipient email addresses', 'error');
            return;
        }

        openComposeModal({
            to: recipients,
            subject: formatReplySubject(email.subject),
            message: formatQuotedMessage(email, false)
        });

        showNotification('Reply all email opened', 'info');
    }

    /**
     * Handle Forward action
     */
    function handleForward(email) {
        if (!email) {
            showNotification('No email selected for forward', 'error');
            return;
        }

        // Format subject
        const forwardSubject = formatForwardSubject(email.subject);

        // Format message with forwarded content
        const forwardMessage = formatQuotedMessage(email, true);

        // Open compose modal with forward data (no "To" pre-filled)
        openComposeModal({
            to: [],
            subject: forwardSubject,
            message: forwardMessage
        });

        showNotification('Forward email opened', 'info');
    }

    /**
     * Handle Delete action (super admin only)
     */
    async function handleDeleteEmail(email) {
        if (!email || !email.id) {
            showNotification('No email selected for delete', 'error');
            return;
        }

        if (!isEmailAdminUser()) {
            showNotification('Only administrators can delete emails.', 'error');
            return;
        }

        const entityId = getEntityId();
        if (!entityId) {
            showNotification('Client or partner ID not found', 'error');
            return;
        }

        const confirmMessage = 'Are you sure you want to delete this email? This cannot be undone.';
        let confirmed = false;
        if (typeof window.crmConfirm === 'function') {
            confirmed = await window.crmConfirm(confirmMessage);
        } else {
            confirmed = window.confirm(confirmMessage);
        }
        if (!confirmed) {
            return;
        }

        const deleteUrl = '/email-v2/' + encodeURIComponent(String(email.id)) + '/delete';

        try {
            const body = new URLSearchParams({
                client_id: String(entityId),
                type: getEntityType(),
                _token: getCsrfToken()
            });

            const response = await fetch(deleteUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: body.toString(),
                credentials: 'same-origin'
            });

            let result = null;
            const contentType = response.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                result = await response.json();
            }

            if (response.ok && result && (result.status === 1 || result.success === true)) {
                showNotification(result.message || 'Email deleted successfully', 'success');
                resetReadingPane();
                document.querySelectorAll('.email-item').forEach(item => {
                    item.classList.remove('selected', 'active');
                });
                loadEmailsFromServer();
            } else {
                const message = (result && result.message)
                    ? result.message
                    : (response.status === 403 ? 'You do not have permission to delete this email.' : 'Failed to delete email');
                showNotification(message, 'error');
            }
        } catch (error) {
            console.error('Delete email error:', error);
            showNotification('Failed to delete email', 'error');
        }
    }

    /**
     * Show context menu at specified coordinates
     */
    function isEmailAdminUser() {
        const container = document.querySelector('.email-v2-interface-container');
        return container && String(container.dataset.userRole) === '1';
    }

    function showContextMenu(x, y, email) {
        const contextMenu = document.getElementById('emailContextMenuV2');
        const overlay = document.getElementById('contextMenuOverlayV2');
        
        if (!contextMenu || !overlay) return;
        
        // Store current email
        currentContextEmail = email;

        const deleteItem = contextMenu.querySelector('[data-action="delete"]');
        if (deleteItem) {
            deleteItem.style.display = isEmailAdminUser() ? 'flex' : 'none';
        }
        
        // Position menu
        contextMenu.style.display = 'block';
        contextMenu.style.left = x + 'px';
        contextMenu.style.top = y + 'px';
        
        // Show overlay
        overlay.style.display = 'block';
        
        // Adjust menu position if it goes off-screen
        setTimeout(() => {
            const rect = contextMenu.getBoundingClientRect();
            const windowWidth = window.innerWidth;
            const windowHeight = window.innerHeight;
            
            if (rect.right > windowWidth) {
                contextMenu.style.left = (x - rect.width) + 'px';
            }
            if (rect.bottom > windowHeight) {
                contextMenu.style.top = (y - rect.height) + 'px';
            }
        }, 0);
    }

    /**
     * Hide context menu
     */
    function hideContextMenu() {
        const contextMenu = document.getElementById('emailContextMenuV2');
        const submenu = document.getElementById('labelSubmenuV2');
        const overlay = document.getElementById('contextMenuOverlayV2');
        
        if (contextMenu) contextMenu.style.display = 'none';
        if (submenu) submenu.style.display = 'none';
        if (overlay) overlay.style.display = 'none';
        
        currentContextEmail = null;
    }

    /**
     * Show label submenu
     */
    function showLabelSubmenu() {
        const contextMenu = document.getElementById('emailContextMenuV2');
        const submenu = document.getElementById('labelSubmenuV2');
        const labelContent = document.getElementById('labelSubmenuContentV2');
        
        if (!submenu || !labelContent || !currentContextEmail) return;
        
        // Get context menu position before hiding it
        const rect = contextMenu.getBoundingClientRect();
        
        // Hide main context menu
        contextMenu.style.display = 'none';
        
        // Position submenu next to context menu
        submenu.style.display = 'block';
        submenu.style.left = (rect.right + 2) + 'px';
        submenu.style.top = rect.top + 'px';
        
        // Get current email labels
        const currentLabels = currentContextEmail.labels || [];
        const currentLabelIds = currentLabels.map(l => l.id);
        
        // Filter out already applied labels
        const filteredLabels = availableLabels.filter(label => {
            return !currentLabelIds.includes(label.id);
        });
        
        // Build label options HTML
        if (filteredLabels.length === 0) {
            labelContent.innerHTML = `
                <div class="submenu-empty">
                    <p>All available labels are already applied</p>
                </div>
            `;
        } else {
            labelContent.innerHTML = filteredLabels.map(label => {
                const isApplied = currentLabelIds.includes(label.id);
                const color = label.color || '#3B82F6';
                
                return `
                    <div class="submenu-item ${isApplied ? 'applied' : ''}" 
                         data-label-id="${label.id}" 
                         data-label-name="${escapeHtml(label.name)}">
                        <span class="submenu-item-badge" style="background-color: ${color}20; border-color: ${color}; color: ${color}">
                            ${labelIconHtml(label.icon)}
                        </span>
                        <span class="submenu-item-text">${escapeHtml(label.name)}</span>
                        ${isApplied ? crmIcon('check', { class: 'submenu-item-check' }) : ''}
                    </div>
                `;
            }).join('');
            
            // Add click handlers
            labelContent.querySelectorAll('.submenu-item').forEach(item => {
                item.addEventListener('click', async function() {
                    const labelId = this.dataset.labelId;
                    const labelName = this.dataset.labelName;
                    const isApplied = this.classList.contains('applied');
                    
                    if (isApplied) {
                        // Already applied (shouldn't happen due to filter, but handle it)
                        return;
                    }
                    
                    // Apply label
                    const success = await applyLabel(currentContextEmail.id, labelId);
                    if (success) {
                        // Reload email list to show updated labels
                        loadEmailsFromServer();
                        hideContextMenu();
                    }
                });
            });
        }
        
        // Back button handler
        const backBtn = submenu.querySelector('.submenu-back');
        if (backBtn) {
            backBtn.onclick = function() {
                submenu.style.display = 'none';
                contextMenu.style.display = 'block';
            };
        }
        
        // Adjust submenu position if it goes off-screen
        setTimeout(() => {
            const submenuRect = submenu.getBoundingClientRect();
            const windowWidth = window.innerWidth;
            
            if (submenuRect.right > windowWidth) {
                submenu.style.left = (rect.left - submenuRect.width) + 'px';
            }
        }, 0);
    }

    /**
     * Initialize context menu handlers
     */
    function initializeContextMenu() {
        const contextMenu = document.getElementById('emailContextMenuV2');
        const overlay = document.getElementById('contextMenuOverlayV2');
        
        if (!contextMenu || !overlay) return;
        
        // Handle menu item clicks
        contextMenu.addEventListener('click', function(e) {
            const item = e.target.closest('.context-menu-item');
            if (!item) return;
            
            const action = item.dataset.action;
            
            switch (action) {
                case 'apply-label':
                    showLabelSubmenu();
                    break;
                case 'reply':
                    if (currentContextEmail) {
                        handleReply(currentContextEmail);
                    }
                    hideContextMenu();
                    break;
                case 'forward':
                    if (currentContextEmail) {
                        handleForward(currentContextEmail);
                    }
                    hideContextMenu();
                    break;
                case 'delete':
                    if (currentContextEmail) {
                        handleDeleteEmail(currentContextEmail);
                    }
                    hideContextMenu();
                    break;
                default:
                    hideContextMenu();
            }
        });
        
        // Close menu when clicking overlay or outside
        overlay.addEventListener('click', hideContextMenu);
        
        // Close menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideContextMenu();
            }
        });
        
        // Close menu on scroll
        document.addEventListener('scroll', hideContextMenu, true);
    }

    // =========================================================================
    // Label Management
    // =========================================================================

    /**
     * Fetch all labels from API
     */
    async function fetchLabels() {
        try {
            const response = await fetch('/email-v2/labels', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (data.success && Array.isArray(data.labels)) {
                availableLabels = data.labels;
                populateLabelFilter();
                populateUploadLabelSelector(); // NEW
                initializeUploadLabelSelector(); // NEW
                populateComposeLabelSelector();
            }
        } catch (error) {
            console.error('Error fetching labels:', error);
        }
    }

    /**
     * Populate label filter dropdown
     */
    function populateLabelFilter() {
        const labelFilter = document.getElementById('labelV2Filter');
        if (!labelFilter) {
            console.warn('Label filter dropdown (labelV2Filter) not found');
            return;
        }

        // Clear existing options (except "All Labels")
        while (labelFilter.options.length > 1) {
            labelFilter.remove(1);
        }

        // Add label options
        availableLabels.forEach(label => {
            const option = document.createElement('option');
            option.value = label.id;
            option.textContent = label.name;
            labelFilter.appendChild(option);
        });
        
        console.log(`Populated ${availableLabels.length} labels in filter dropdown`);
    }

    /**
     * Compose labels: Sent is always applied server-side. Populate Add label dropdown and handle chips.
     */
    let composeSelectedLabelIds = [];

    function clearComposeLabelChips() {
        composeSelectedLabelIds = [];
        const chipsEl = document.getElementById('composeAdditionalLabelsChips');
        const containerEl = document.getElementById('composeLabelIdsContainer');
        if (chipsEl) chipsEl.innerHTML = '';
        if (containerEl) containerEl.innerHTML = '';
    }

    function renderComposeLabelChips() {
        const chipsEl = document.getElementById('composeAdditionalLabelsChips');
        const containerEl = document.getElementById('composeLabelIdsContainer');
        if (!chipsEl || !containerEl) return;
        chipsEl.innerHTML = '';
        containerEl.innerHTML = '';
        composeSelectedLabelIds.forEach(labelId => {
            const label = availableLabels.find(l => l.id == labelId);
            if (!label) return;
            const chip = document.createElement('span');
            chip.className = 'compose-label-chip';
            chip.style.backgroundColor = (label.color || '#3B82F6') + '20';
            chip.style.borderColor = label.color || '#3B82F6';
            chip.style.color = label.color || '#3B82F6';
            chip.innerHTML = `${labelIconHtml(label.icon)}<span>${escapeHtml(label.name || '')}</span>${crmIcon('times', 'solid', { class: 'chip-remove', attrs: { 'data-label-id': label.id } })}`;
            chip.querySelector('.chip-remove').addEventListener('click', function() {
                composeSelectedLabelIds = composeSelectedLabelIds.filter(id => id != label.id);
                renderComposeLabelChips();
            });
            chipsEl.appendChild(chip);
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'label_ids[]';
            input.value = label.id;
            containerEl.appendChild(input);
        });
    }

    function populateComposeLabelSelector() {
        const dropdown = document.getElementById('composeLabelDropdown');
        if (!dropdown) return;

        dropdown.innerHTML = '';
        const sortedLabels = availableLabels
            .filter(l => (l.name || '').toLowerCase() !== 'sent')
            .sort((a, b) => {
                if (a.type === 'system' && b.type !== 'system') return -1;
                if (a.type !== 'system' && b.type === 'system') return 1;
                return (a.name || '').localeCompare(b.name || '');
            });

        if (sortedLabels.length === 0) {
            dropdown.innerHTML = '<li><span class="dropdown-item text-muted">No additional labels</span></li>';
            return;
        }

        sortedLabels.forEach(label => {
            const item = document.createElement('li');
            const link = document.createElement('a');
            link.className = 'dropdown-item';
            link.href = '#';
            link.innerHTML = `<span class="label-color-dot" style="background:${label.color || '#3B82F6'}"></span>${escapeHtml(label.name || '')}`;
            link.addEventListener('click', function(e) {
                e.preventDefault();
                if (!composeSelectedLabelIds.includes(label.id)) {
                    composeSelectedLabelIds.push(label.id);
                    renderComposeLabelChips();
                }
            });
            item.appendChild(link);
            dropdown.appendChild(item);
        });

        // Clear chips when modal opens (emails_v2 may load before/without email-handlers on partners)
        const emailModal = document.getElementById('emailmodal');
        if (emailModal && !emailModal.dataset.composeLabelsInit) {
            emailModal.dataset.composeLabelsInit = '1';
            $(emailModal).on('shown.bs.modal', clearComposeLabelChips);
            $('form[name="sendmail"]').on('reset', clearComposeLabelChips);
        }
    }

    /**
     * Label creation removed - labels are now managed in Admin Console
     * Use /adminconsole/features/email-labels to create/edit labels
     * Frontend only handles filtering and applying existing labels
     */

    // =========================================================================
    // Upload Label Selector Functions
    // =========================================================================

    // Track selected label IDs
    let selectedLabelIds = new Set();

    /**
     * Populate the upload label selector dropdown
     */
    function populateUploadLabelSelector() {
        const optionsList = document.getElementById('labelOptionsList');
        if (!optionsList) return;
        
        // Clear existing options
        optionsList.innerHTML = '';
        
        if (availableLabels.length === 0) {
            optionsList.innerHTML = '<div style="padding: 12px; text-align: center; color: #6c757d;">No labels available</div>';
            return;
        }
        
        // Sort: system labels first, then alphabetically
        const sortedLabels = [...availableLabels].sort((a, b) => {
            if (a.type === 'system' && b.type !== 'system') return -1;
            if (a.type !== 'system' && b.type === 'system') return 1;
            return a.name.localeCompare(b.name);
        });
        
        // Create option items
        sortedLabels.forEach(label => {
            const item = document.createElement('div');
            item.className = 'label-option-item';
            item.dataset.labelId = label.id;
            item.dataset.labelName = label.name;
            item.dataset.labelColor = label.color || '#3B82F6';
            item.dataset.labelIcon = label.icon || 'tag';
            item.dataset.labelType = label.type || 'custom';
            
            item.innerHTML = `
                <input type="checkbox" class="label-option-checkbox" id="label-opt-${label.id}">
                <div class="label-option-color" style="background-color: ${label.color || '#3B82F6'}"></div>
                <span class="label-option-icon" style="color: ${label.color || '#3B82F6'}">${labelIconHtml(label.icon)}</span>
                <span class="label-option-name">${escapeHtml(label.name)}</span>
                ${label.type === 'system' ? '<span class="label-option-type">System</span>' : ''}
            `;
            
            // Click handler for the entire item
            item.addEventListener('click', function(e) {
                if (e.target.classList.contains('label-option-checkbox')) return;
                const checkbox = this.querySelector('.label-option-checkbox');
                checkbox.checked = !checkbox.checked;
                toggleLabelSelection(label.id);
            });
            
            // Checkbox change handler
            const checkbox = item.querySelector('.label-option-checkbox');
            checkbox.addEventListener('change', function() {
                toggleLabelSelection(label.id);
            });
            
            optionsList.appendChild(item);
        });
    }

    /**
     * Initialize upload label selector event listeners
     */
    function initializeUploadLabelSelector() {
        const container = document.getElementById('uploadLabelSelectorContainer');
        const trigger = document.getElementById('labelDropdownTrigger');
        const menu = document.getElementById('labelDropdownMenu');
        const searchInput = document.getElementById('labelSearchInput');
        const clearBtn = document.getElementById('clearLabelsBtn');
        
        if (!container || !trigger || !menu) return;
        
        // Show container once labels are loaded
        if (container.style.display === 'none') {
            container.style.display = 'block';
        }
        
        // Toggle dropdown on trigger click
        trigger.addEventListener('click', function() {
            const isActive = menu.style.display === 'block';
            menu.style.display = isActive ? 'none' : 'block';
            trigger.classList.toggle('active', !isActive);
            
            if (!isActive && searchInput) {
                setTimeout(() => searchInput.focus(), 100);
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!container.contains(e.target)) {
                menu.style.display = 'none';
                trigger.classList.remove('active');
            }
        });
        
        // Search functionality
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const items = document.querySelectorAll('.label-option-item');
                
                items.forEach(item => {
                    const labelName = item.dataset.labelName.toLowerCase();
                    item.style.display = labelName.includes(searchTerm) ? 'flex' : 'none';
                });
            });
            
            searchInput.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
        
        // Clear all labels button
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                clearAllSelectedLabels();
            });
        }
    }

    /**
     * Toggle label selection
     */
    function toggleLabelSelection(labelId) {
        if (selectedLabelIds.has(labelId)) {
            selectedLabelIds.delete(labelId);
        } else {
            selectedLabelIds.add(labelId);
        }
        
        // Update checkbox state
        const checkbox = document.getElementById(`label-opt-${labelId}`);
        if (checkbox) {
            checkbox.checked = selectedLabelIds.has(labelId);
            checkbox.closest('.label-option-item').classList.toggle('selected', selectedLabelIds.has(labelId));
        }
        
        updateSelectedLabelsPreview();
        updateDropdownTriggerText();
    }

    /**
     * Update selected labels preview (badges)
     */
    function updateSelectedLabelsPreview() {
        const preview = document.getElementById('selectedLabelsPreview');
        if (!preview) return;
        
        preview.innerHTML = '';
        
        selectedLabelIds.forEach(labelId => {
            const label = availableLabels.find(l => l.id == labelId);
            if (!label) return;
            
            const badge = document.createElement('div');
            badge.className = 'selected-label-badge';
            badge.style.backgroundColor = `${label.color}20`;
            badge.style.borderColor = label.color;
            badge.style.color = label.color;
            
            badge.innerHTML = `
                ${labelIconHtml(label.icon)}
                <span>${escapeHtml(label.name)}</span>
                ${crmIcon('times', 'solid', { class: 'remove-label', attrs: { 'data-label-id': label.id } })}
            `;
            
            const removeBtn = badge.querySelector('.remove-label');
            removeBtn.addEventListener('click', function() {
                toggleLabelSelection(label.id);
            });
            
            preview.appendChild(badge);
        });
    }

    /**
     * Update dropdown trigger text
     */
    function updateDropdownTriggerText() {
        const trigger = document.getElementById('labelDropdownTrigger');
        const placeholder = trigger?.querySelector('.dropdown-placeholder');
        if (!placeholder) return;
        
        const count = selectedLabelIds.size;
        if (count === 0) {
            placeholder.textContent = 'Select labels...';
            placeholder.style.color = '#6c757d';
        } else {
            placeholder.textContent = `${count} label${count > 1 ? 's' : ''} selected`;
            placeholder.style.color = '#212529';
            placeholder.style.fontWeight = '500';
        }
    }

    /**
     * Clear all selected labels
     */
    function clearAllSelectedLabels() {
        selectedLabelIds.clear();
        
        document.querySelectorAll('.label-option-checkbox').forEach(checkbox => {
            checkbox.checked = false;
            checkbox.closest('.label-option-item')?.classList.remove('selected');
        });
        
        updateSelectedLabelsPreview();
        updateDropdownTriggerText();
    }

    /**
     * Get selected label IDs as array
     */
    function getSelectedLabelIds() {
        return Array.from(selectedLabelIds);
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Apply label to email
     */
    async function applyLabel(mailReportId, labelId) {
        try {
            const response = await fetch('/email-v2/labels/apply', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ mail_report_id: mailReportId, label_id: labelId })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (data.success) {
                showNotification('Label applied successfully', 'success');
                return true;
            } else {
                throw new Error(data.message || 'Failed to apply label');
            }
        } catch (error) {
            console.error('Error applying label:', error);
            showNotification('Error applying label: ' + error.message, 'error');
            return false;
        }
    }

    /**
     * Remove label from email
     */
    async function removeLabel(mailReportId, labelId) {
        try {
            const response = await fetch('/email-v2/labels/remove', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ mail_report_id: mailReportId, label_id: labelId })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (data.success) {
                showNotification('Label removed successfully', 'success');
                return true;
            } else {
                throw new Error(data.message || 'Failed to remove label');
            }
        } catch (error) {
            console.error('Error removing label:', error);
            showNotification('Error removing label: ' + error.message, 'error');
            return false;
        }
    }

    // =========================================================================
    // Attachment Handling
    // =========================================================================

    /**
     * Download individual attachment (mail_report_attachments row)
     */
    async function downloadAttachment(attachmentId, filename) {
        try {
            if (!attachmentId || !/^\d+$/.test(String(attachmentId))) {
                showNotification('Invalid attachment.', 'error');
                return;
            }
            const response = await fetch(`/email-v2/attachments/${attachmentId}/download`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/octet-stream'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const cd = response.headers.get('Content-Disposition');
            let saveName = parseFilenameFromContentDisposition(cd);
            if (saveName) {
                saveName = sanitizeFilename(saveName);
            } else {
                saveName = filename;
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = saveName;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            showNotification(`Downloaded: ${saveName}`, 'success');
        } catch (error) {
            console.error('Error downloading attachment:', error);
            showNotification('Error downloading attachment: ' + error.message, 'error');
        }
    }

    /**
     * Download legacy attachment (emails.attachments JSON only — no DB row id)
     */
    async function downloadLegacyAttachment(mailReportId, index, filename) {
        try {
            if (!mailReportId || !/^\d+$/.test(String(mailReportId))) {
                showNotification('Cannot download this attachment.', 'error');
                return;
            }
            if (typeof index !== 'number' || !Number.isFinite(index) || index < 0) {
                showNotification('Cannot download this attachment.', 'error');
                return;
            }
            const response = await fetch(`/email-v2/attachments/mail/${mailReportId}/legacy/${index}/download`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/octet-stream'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const cd = response.headers.get('Content-Disposition');
            let saveName = parseFilenameFromContentDisposition(cd);
            if (saveName) {
                saveName = sanitizeFilename(saveName);
            } else {
                saveName = filename;
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = saveName;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            showNotification(`Downloaded: ${saveName}`, 'success');
        } catch (error) {
            console.error('Error downloading attachment:', error);
            showNotification('Error downloading attachment: ' + error.message, 'error');
        }
    }

    /**
     * Download all attachments as ZIP
     */
    async function downloadAllAttachments(mailReportId, emailSubject) {
        try {
            const response = await fetch(`/email-v2/attachments/${mailReportId}/download-all`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/octet-stream'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            const sanitizedSubject = sanitizeFilename(emailSubject || 'email');
            a.download = `${sanitizedSubject}_attachments.zip`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            showNotification('Attachments downloaded successfully', 'success');
        } catch (error) {
            console.error('Error downloading attachments:', error);
            showNotification('Error downloading attachments: ' + error.message, 'error');
        }
    }

    // Timeout handle for legacy preview modal (still used if modal opened elsewhere)
    let previewLoadTimeout = null;

    /**
     * Open attachment preview in a new browser tab (same URL as server preview endpoint).
     */
    function openAttachmentPreviewInNewTab(attachmentId) {
        if (!attachmentId || !/^\d+$/.test(String(attachmentId))) {
            showNotification('Invalid attachment.', 'error');
            return;
        }
        const url = `/email-v2/attachments/${attachmentId}/preview`;
        window.open(url, '_blank', 'noopener,noreferrer');
    }

    // =========================================================================
    // Initialization
    // =========================================================================

    // Initialize pagination on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializePagination);
    } else {
        initializePagination();
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeNewFeatures);
    } else {
        initializeNewFeatures();
    }

    function initReadingPaneActions() {
        const btnReply = document.getElementById('btnReplyV2');
        const btnReplyAll = document.getElementById('btnReplyAllV2');
        const btnForward = document.getElementById('btnForwardV2');
        const btnDelete = document.getElementById('btnDeleteEmailV2');

        if (btnReply) {
            btnReply.addEventListener('click', function() {
                if (currentReadingEmail) {
                    handleReply(currentReadingEmail);
                }
            });
        }
        if (btnReplyAll) {
            btnReplyAll.addEventListener('click', function() {
                if (currentReadingEmail) {
                    handleReplyAll(currentReadingEmail);
                }
            });
        }
        if (btnForward) {
            btnForward.addEventListener('click', function() {
                if (currentReadingEmail) {
                    handleForward(currentReadingEmail);
                }
            });
        }
        if (btnDelete) {
            btnDelete.addEventListener('click', function() {
                if (currentReadingEmail) {
                    handleDeleteEmail(currentReadingEmail);
                }
            });
        }
    }

    /**
     * Initialize collapsible upload section to save space
     * Remembers collapsed state in localStorage
     */
    function initUploadSectionCollapse() {
        const toggle = document.querySelector('.js-upload-toggle');
        const body = document.getElementById('upload-section-body');
        if (!toggle || !body) return;

        const STORAGE_KEY = 'emailUploadSectionCollapsed';

        function getStoredCollapsed() {
            try {
                return localStorage.getItem(STORAGE_KEY) === 'true';
            } catch (e) {
                return false;
            }
        }

        function setStoredCollapsed(value) {
            try {
                localStorage.setItem(STORAGE_KEY, String(value));
            } catch (e) { /* ignore - private mode / quota */ }
        }

        function applyState(collapsed) {
            toggle.classList.toggle('collapsed', collapsed);
            body.classList.toggle('collapsed', collapsed);
            toggle.setAttribute('aria-expanded', !collapsed);
        }

        applyState(getStoredCollapsed());

        function handleToggle() {
            const collapsed = !getStoredCollapsed();
            setStoredCollapsed(collapsed);
            applyState(collapsed);
        }

        toggle.addEventListener('click', handleToggle);
        toggle.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                handleToggle();
            }
        });
    }

    /**
     * Soft-check Python microservice — browsing works when down; uploads show errors.
     */
    async function checkEmailPythonServiceStatus() {
        const banner = document.getElementById('pythonServiceWarningV2');
        if (!banner) {
            return;
        }

        try {
            const response = await fetch('/email-v2/check-service', {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            let data = null;
            if (response.ok) {
                data = await response.json();
            }

            if (data && data.status === true) {
                banner.style.display = 'none';
                banner.textContent = '';
                return;
            }

            banner.style.display = 'block';
            banner.textContent = 'Email parsing service is unavailable. You can still browse emails; uploads will fail until the service is restarted.';
        } catch (e) {
            banner.style.display = 'block';
            banner.textContent = 'Could not reach the email parsing service. Browsing still works; uploads may fail.';
        }
    }

    /**
     * Initialize new filter and modal features
     */
    function initializeNewFeatures() {
        // Initialize upload section collapsible toggle
        initUploadSectionCollapse();

        checkEmailPythonServiceStatus();

        // Initialize upload functionality (drag & drop)
        if (typeof window.initializeUpload === 'function') {
            window.initializeUpload();
        }

        // Fetch labels on load
        fetchLabels();

        // Initialize context menu
        initializeContextMenu();

        // Reading pane action toolbar (Reply, Forward, Delete)
        initReadingPaneActions();
        
        // Initialize search functionality
        if (typeof window.initializeSearch === 'function') {
            window.initializeSearch();
        }

        // Mail type filter (Inbox/Sent) - support both tab buttons and hidden select
        const mailTypeFilter = document.getElementById('mailTypeFilterV2');
        if (mailTypeFilter) {
            mailTypeFilter.addEventListener('change', function() {
                currentMailType = this.value;
                updateFolderTabButtons(currentMailType);
                loadEmailsFromServer();
            });
        }
        // Folder tab buttons (Inbox | Sent)
        document.querySelectorAll('.folder-tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const folder = this.dataset.folder || this.getAttribute('data-folder');
                if (folder && folder !== currentMailType) {
                    currentMailType = folder;
                    if (mailTypeFilter) mailTypeFilter.value = folder;
                    updateFolderTabButtons(folder);
                    loadEmailsFromServer();
                }
            });
        });

        // Category tab buttons (Client | College) - client detail only
        document.querySelectorAll('.category-tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const category = this.dataset.category || this.getAttribute('data-category');
                if (category && category !== currentEmailCategory) {
                    currentEmailCategory = category;
                    updateCategoryTabButtons(category);
                    loadEmailsFromServer();
                }
            });
        });
        
        // Apply button removed - all filters auto-apply:
        // - Search auto-applies as you type (debounced)
        // - Label filter auto-applies on change
        // - Mail type filter auto-applies on change

        // Label creation removed - now managed in Admin Console
        // Labels can only be created via /adminconsole/features/email-labels

        // Preview modal close
        const closePreviewBtn = document.getElementById('closePreviewBtnV2');
        const previewOverlay = document.getElementById('previewOverlayV2');
        if (closePreviewBtn) {
            closePreviewBtn.addEventListener('click', hidePreviewModal);
        }
        if (previewOverlay) {
            previewOverlay.addEventListener('click', hidePreviewModal);
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('attachmentPreviewModalV2');
                if (modal && modal.style.display !== 'none') {
                    hidePreviewModal();
                }
            }
        });

        // Initialize attachment handlers
        initializeAttachmentHandlers();
    }

    /**
     * Event delegation for attachment buttons
     * Handles all attachment-related clicks
     */
    function initializeAttachmentHandlers() {
        // Single delegated listener for all attachment actions
        document.addEventListener('click', function(e) {
            const target = e.target.closest('button');
            if (!target) return;

            // Download individual attachment
            if (target.classList.contains('download-attachment-btn')) {
                e.preventDefault();
                const attachmentId = target.dataset.attachmentId;
                const mailReportId = target.dataset.mailReportId;
                const legacyIndexRaw = target.dataset.legacyIndex;
                const filename = target.dataset.filename;

                const originalHtml = target.innerHTML;
                const runDownload = () => {
                    target.disabled = true;
                    target.innerHTML = spinnerHtml(' Downloading...');
                };
                const finishDownload = () => {
                    target.disabled = false;
                    target.innerHTML = originalHtml;
                };

                if (attachmentId && /^\d+$/.test(String(attachmentId)) && filename) {
                    runDownload();
                    downloadAttachment(attachmentId, filename).finally(finishDownload);
                } else if (
                    mailReportId &&
                    /^\d+$/.test(String(mailReportId)) &&
                    legacyIndexRaw !== undefined &&
                    legacyIndexRaw !== '' &&
                    /^\d+$/.test(String(legacyIndexRaw)) &&
                    filename
                ) {
                    runDownload();
                    downloadLegacyAttachment(
                        mailReportId,
                        parseInt(legacyIndexRaw, 10),
                        filename
                    ).finally(finishDownload);
                } else {
                    showNotification('Cannot download this attachment.', 'error');
                }
            }

            // Preview attachment (new tab — same behaviour as clicking the filename link)
            if (target.classList.contains('preview-attachment-btn')) {
                e.preventDefault();
                const attachmentId = target.dataset.attachmentId;
                if (attachmentId && /^\d+$/.test(String(attachmentId))) {
                    openAttachmentPreviewInNewTab(attachmentId);
                }
            }

            // Download all attachments as ZIP
            if (target.classList.contains('download-all-btn')) {
                e.preventDefault();
                const mailReportId = target.dataset.mailReportId;
                const emailSubject = target.dataset.emailSubject;
                
                if (mailReportId) {
                    // Disable button during download
                    const originalHtml = target.innerHTML;
                    target.disabled = true;
                    target.innerHTML = spinnerHtml(' Creating ZIP...');
                    
                    downloadAllAttachments(mailReportId, emailSubject).finally(() => {
                        target.disabled = false;
                        target.innerHTML = originalHtml;
                    });
                }
            }
        });
    }

    /**
     * Label creation functions removed - labels are now managed in Admin Console
     * Navigate to /adminconsole/features/email-labels to create/edit labels
     */

    /**
     * Hide preview modal
     */
    function hidePreviewModal() {
        const modal = document.getElementById('attachmentPreviewModalV2');
        const frame = document.getElementById('previewFrameV2');
        const loading = document.getElementById('previewLoadingV2');
        if (modal && frame) {
            // Cancel pending load timeout
            clearTimeout(previewLoadTimeout);
            frame.onload = null;

            modal.style.display = 'none';
            frame.style.display = 'none';
            frame.src = ''; // Stop loading
            // Reset spinner for next open
            if (loading) {
                loading.style.display = 'flex';
                loading.innerHTML = spinnerHtml() + '<span>Loading preview&hellip;</span>';
            }
            document.body.style.overflow = '';
        }
    }

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);

    console.log('Emails module loaded');

})();

