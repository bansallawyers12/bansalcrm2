/**
 * Document Signature Flow - Inline in Documents Tab
 * Handles: placement modal, action bar (Send, Revise, Remove, Reminder)
 */
'use strict';

(function() {
    const baseUrl = (typeof App !== 'undefined' && App.getUrl && App.getUrl('siteUrl')) || '';
    const csrf = (typeof App !== 'undefined' && App.getCsrf && App.getCsrf()) || (function() {
        const m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    })();

    window.DocumentSignatureFlow = {
        currentDocId: null,
        currentClientId: null,
        currentRow: null,
        signatureFields: [],
        fieldIdCounter: 0,
        currentPage: 1,
        totalPages: 1,

        openPlacementModal: function(docId, checklistName, fileName, fileType, myfile, myfileKey, docType) {
            this.currentDocId = docId;
            this.currentClientId = window.PageConfig && window.PageConfig.clientId ? window.PageConfig.clientId : 
                (document.querySelector('[data-encode-id]') ? document.querySelector('[data-encode-id]').getAttribute('data-encode-id') : null);
            this.signatureFields = [];
            this.fieldIdCounter = 0;
            this.currentPage = 1;
            this.totalPages = 1;

            let clientEmail = (window.PageConfig && window.PageConfig.clientEmail) ? window.PageConfig.clientEmail : '';
            const clientName = (window.PageConfig && window.PageConfig.clientName) ? window.PageConfig.clientName : 'Client';

            let modalHtml = `
                <div class="modal fade" id="documentSignaturePlacementModal" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Place Signature on Document</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted small mb-3">Click on the document to place where the client will sign.</p>
                                <div class="mb-3">
                                    <label class="form-label small">Signer email (required)</label>
                                    <input type="email" class="form-control form-control-sm" id="signatureSignerEmail" placeholder="Client email for signing link" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="pdf-preview-wrapper border rounded position-relative" id="signaturePdfContainer" style="min-height:500px;background:#f5f5f5;overflow:hidden;">
                                            <div class="pdf-loading position-absolute top-50 start-50 translate-middle" id="signaturePdfLoading">
                                                <span class="spinner-border text-primary"></span> Loading...
                                            </div>
                                            <img src="" alt="PDF Page" class="img-fluid pdf-page-img" id="signaturePdfPage" style="display:none;width:100%;">
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="signaturePrevPage" disabled><i class="fas fa-chevron-left"></i> Prev</button>
                                            <span>Page <span id="signatureCurrentPage">1</span> of <span id="signaturePageCount">1</span></span>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="signatureNextPage">Next <i class="fas fa-chevron-right"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>Signature Fields</h6>
                                        <div id="signatureFieldsList" class="mb-2">
                                            <p class="text-muted small" id="signatureNoFieldsMsg">No fields yet. Click on the document.</p>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm mb-2" id="signatureAddFieldBtn"><i class="fas fa-plus"></i> Add Field</button>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="signatureSavePlacementBtn"><i class="fas fa-save"></i> Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            const existing = document.getElementById('documentSignaturePlacementModal');
            if (existing) existing.remove();

            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modalEl = document.getElementById('documentSignaturePlacementModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            const emailInput = modalEl.querySelector('#signatureSignerEmail');
            if (emailInput) emailInput.value = clientEmail || '';

            const self = this;
            modalEl.addEventListener('hidden.bs.modal', function() {
                modalEl.remove();
        self.currentDocId = null;
        self.currentRow = null;
            }, { once: true });

            this.loadDocumentInfo(docId).then(function() {
                self.loadPdfPage(docId, 1);
            });

            document.getElementById('signaturePrevPage').onclick = () => {
                if (self.currentPage > 1) {
                    self.currentPage--;
                    self.loadPdfPage(docId, self.currentPage);
                    self.updatePageNav();
                }
            };
            document.getElementById('signatureNextPage').onclick = () => {
                if (self.currentPage < self.totalPages) {
                    self.currentPage++;
                    self.loadPdfPage(docId, self.currentPage);
                    self.updatePageNav();
                }
            };

            document.getElementById('signaturePdfContainer').onclick = function(e) {
                if (e.target.id !== 'signaturePdfPage') return;
                const rect = e.target.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                self.addFieldAt(x, y);
            };

            document.getElementById('signatureAddFieldBtn').onclick = () => self.addFieldAt(40, 30);
            document.getElementById('signatureSavePlacementBtn').onclick = () => {
                const emailInput = document.getElementById('signatureSignerEmail');
                const signerEmail = emailInput ? emailInput.value.trim() : clientEmail;
                const signerName = clientName;
                if (!signerEmail) {
                    if (typeof Swal !== 'undefined') Swal.fire('Email required', 'Please enter the signer email address.', 'warning');
                    else alert('Please enter the signer email address.');
                    return;
                }
                self.savePlacement(docId, signerEmail, signerName);
            };
        },

        loadDocumentInfo: function(docId) {
            const self = this;
            return fetch(baseUrl + '/documents/' + docId + '/info')
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.page_count) {
                        self.totalPages = data.page_count;
                        self.updatePageNav();
                    }
                })
                .catch(err => console.error('Failed to load document info:', err));
        },

        loadPdfPage: function(docId, page) {
            const img = document.getElementById('signaturePdfPage');
            const loading = document.getElementById('signaturePdfLoading');
            if (!img || !loading) return;

            loading.style.display = 'block';
            img.style.display = 'none';
            img.onload = () => {
                loading.style.display = 'none';
                img.style.display = 'block';
                this.renderFieldsForPage(page);
            };
            img.onerror = () => {
                loading.innerHTML = '<span class="text-danger">Failed to load page. PDF service may be unavailable.</span>';
            };
            img.src = baseUrl + '/documents/' + docId + '/page/' + page;
        },

        updatePageNav: function() {
            const cur = document.getElementById('signatureCurrentPage');
            const cnt = document.getElementById('signaturePageCount');
            const prev = document.getElementById('signaturePrevPage');
            const next = document.getElementById('signatureNextPage');
            if (cur) cur.textContent = this.currentPage;
            if (cnt) cnt.textContent = this.totalPages;
            if (prev) prev.disabled = this.currentPage <= 1;
            if (next) next.disabled = this.currentPage >= this.totalPages;
        },

        addFieldAt: function(x, y) {
            const field = {
                id: ++this.fieldIdCounter,
                page: this.currentPage,
                x_percent: Math.max(0, Math.min(82, x)),
                y_percent: Math.max(0, Math.min(92, y)),
                width_percent: 18,
                height_percent: 8
            };
            this.signatureFields.push(field);
            this.updateFieldsList();
            this.renderFieldsForPage(this.currentPage);
        },

        updateFieldsList: function() {
            const list = document.getElementById('signatureFieldsList');
            const noMsg = document.getElementById('signatureNoFieldsMsg');
            if (!list) return;
            if (this.signatureFields.length === 0) {
                if (noMsg) noMsg.style.display = 'block';
                list.querySelectorAll('.signature-field-item').forEach(el => el.remove());
            } else {
                if (noMsg) noMsg.style.display = 'none';
                list.querySelectorAll('.signature-field-item').forEach(el => el.remove());
                this.signatureFields.forEach((f, i) => {
                    const div = document.createElement('div');
                    div.className = 'signature-field-item small text-muted mb-1';
                    div.textContent = 'Field ' + (i + 1) + ' (Page ' + f.page + ')';
                    list.appendChild(div);
                });
            }
        },

        renderFieldsForPage: function(page) {
            const container = document.getElementById('signaturePdfContainer');
            const img = document.getElementById('signaturePdfPage');
            if (!container || !img || img.style.display === 'none') return;

            container.querySelectorAll('.signature-field-overlay').forEach(el => el.remove());
            this.signatureFields.filter(f => f.page === page).forEach(field => {
                const el = document.createElement('div');
                el.className = 'signature-field-overlay position-absolute border border-success bg-success bg-opacity-10';
                el.style.left = field.x_percent + '%';
                el.style.top = field.y_percent + '%';
                el.style.width = field.width_percent + '%';
                el.style.height = field.height_percent + '%';
                el.style.pointerEvents = 'none';
                el.dataset.fieldId = field.id;
                container.appendChild(el);
            });
        },

        savePlacement: function(docId, clientEmail, clientName) {
            if (this.signatureFields.length === 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Add a field', 'Click on the document to place at least one signature field.', 'warning');
                } else {
                    alert('Please add at least one signature field.');
                }
                return;
            }

            const btn = document.getElementById('signatureSavePlacementBtn');
            if (btn) btn.disabled = true;

            const fields = this.signatureFields.map(f => ({
                page: f.page,
                x_percent: f.x_percent,
                y_percent: f.y_percent,
                width_percent: f.width_percent,
                height_percent: f.height_percent
            }));

            const payload = {
                _token: csrf,
                doc_id: docId,
                client_id: this.currentClientId,
                signer_email: clientEmail || '',
                signer_name: clientName || 'Client',
                fields: fields
            };

            fetch(baseUrl + '/document-signature/save-placement', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(res => {
                if (btn) btn.disabled = false;
                if (res.status) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('documentSignaturePlacementModal'));
                    if (modal) modal.hide();
                    // Reload the document list – the action bar is rendered server-side via getDocuments
                    if (typeof window.DocumentCategoryManager !== 'undefined') {
                        window.DocumentCategoryManager.loadCategoryDocuments(window.DocumentCategoryManager.currentCategoryId);
                    }
                } else {
                    alert(res.message || 'Failed to save placement.');
                }
            })
            .catch(err => {
                if (btn) btn.disabled = false;
                console.error(err);
                alert('Failed to save. Please try again.');
            });
        },

        reloadDocuments: function() {
            if (typeof window.DocumentCategoryManager !== 'undefined') {
                window.DocumentCategoryManager.loadCategoryDocuments(window.DocumentCategoryManager.currentCategoryId);
            }
        },

        sendForSignature: function(docId) {
            fetch(baseUrl + '/document-signature/send', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({ _token: csrf, doc_id: docId })
            })
            .then(r => r.json())
            .then(res => {
                if (res.status) {
                    // Reload list so Send becomes disabled and Reminder appears
                    this.reloadDocuments();
                } else {
                    alert(res.message || 'Failed to send.');
                }
            })
            .catch(() => alert('Failed to send. Please try again.'));
        },

        sendReminder: function(docId) {
            fetch(baseUrl + '/document-signature/reminder', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({ _token: csrf, doc_id: docId })
            })
            .then(r => r.json())
            .then(res => {
                if (res.status) {
                    if (typeof Swal !== 'undefined') Swal.fire('Success', 'Reminder sent.', 'success');
                    else alert('Reminder sent.');
                } else {
                    alert(res.message || 'Failed to send reminder.');
                }
            });
        },

        removeSignature: function(docId) {
            if (!confirm('Remove signature request? This will cancel pending signers.')) return;
            fetch(baseUrl + '/document-signature/remove', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({ _token: csrf, doc_id: docId })
            })
            .then(r => r.json())
            .then(res => {
                if (res.status) {
                    this.reloadDocuments();
                }
            })
            .catch(() => alert('Failed to remove. Please try again.'));
        }
    };

    // Event delegation for action bars rendered by DocumentCategoryManager
    document.addEventListener('click', function(e) {
        const sendBtn = e.target.closest('.document-sig-send');
        const reviseBtn = e.target.closest('.document-sig-revise');
        const removeBtn = e.target.closest('.document-sig-remove');
        const reminderBtn = e.target.closest('.document-sig-reminder');
        const bar = e.target.closest('.document-signature-action-bar');
        const docId = bar ? bar.getAttribute('data-doc-id') : null;
        if (!docId) return;
        if (sendBtn && !sendBtn.disabled) window.DocumentSignatureFlow.sendForSignature(docId);
        else if (reviseBtn) window.DocumentSignatureFlow.openPlacementModal(docId, '', '', '', '', '', '');
        else if (removeBtn) window.DocumentSignatureFlow.removeSignature(docId);
        else if (reminderBtn) window.DocumentSignatureFlow.sendReminder(docId);
    });
})();
