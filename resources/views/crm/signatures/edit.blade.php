@extends('layouts.admin')
@section('title', 'Designate Signatures - ' . ($document->display_title ?? 'Document'))

@push('styles')
<style>
    .signature-editor {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .editor-header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .editor-header h1 {
        font-size: 28px;
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 10px;
    }
    
    .editor-header p {
        color: #6b7280;
        font-size: 15px;
    }
    
    .alert-success {
        background-color: #d1fae5;
        border: 1px solid #6ee7b7;
        color: #065f46;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .editor-content {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 30px;
    }
    
    @media (max-width: 1024px) {
        .editor-content {
            grid-template-columns: 1fr;
        }
    }
    
    .pdf-preview-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 20px;
    }
    
    .pdf-preview-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .pdf-preview-header h2 {
        font-size: 18px;
        font-weight: 600;
        color: #1a202c;
        margin: 0;
    }
    
    .page-nav {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .page-nav button {
        background: #e5e7eb;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
    }
    
    .page-nav button:hover {
        background: #d1d5db;
    }
    
    .page-nav button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .page-nav span {
        font-size: 14px;
        color: #6b7280;
    }
    
    .pdf-container {
        position: relative;
        background: #f3f4f6;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        min-height: 600px;
        overflow: hidden;
    }
    
    .pdf-page {
        width: 100%;
        display: block;
    }
    
    .pdf-loading {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 600px;
        color: #6b7280;
    }
    
    .signature-field {
        position: absolute;
        border: 2px dashed #10b981;
        background: rgba(16, 185, 129, 0.1);
        cursor: move;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        color: #10b981;
        font-weight: 600;
    }
    
    .signature-field:hover {
        background: rgba(16, 185, 129, 0.2);
    }
    
    .signature-field .field-label {
        background: #10b981;
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 10px;
        position: absolute;
        top: -18px;
        left: 0;
    }
    
    .signature-field .delete-field {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }
    
    .sidebar-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 20px;
    }
    
    .sidebar-section h2 {
        font-size: 18px;
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 20px;
    }
    
    /* Signature Field Card */
    .field-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background: #fafafa;
    }
    
    .field-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }
    
    .field-card-title {
        font-size: 15px;
        font-weight: 600;
        color: #1a202c;
    }
    
    .field-card-actions {
        display: flex;
        gap: 5px;
    }
    
    .field-card-actions button {
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        border: none;
        cursor: pointer;
    }
    
    .btn-preview {
        background: #3b82f6;
        color: white;
    }
    
    .btn-preview:hover {
        background: #2563eb;
    }
    
    .btn-edit {
        background: #10b981;
        color: white;
    }
    
    .btn-edit:hover {
        background: #059669;
    }
    
    .btn-delete {
        background: #ef4444;
        color: white;
    }
    
    .btn-delete:hover {
        background: #dc2626;
    }
    
    .field-card-coords {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
    }
    
    .field-card-coords.has-height {
        grid-template-columns: repeat(4, 1fr);
    }
    
    .coord-group {
        display: flex;
        flex-direction: column;
    }
    
    .coord-group label {
        font-size: 11px;
        color: #6b7280;
        margin-bottom: 4px;
        font-weight: 500;
    }
    
    .coord-group input {
        width: 100%;
        padding: 6px 8px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        font-size: 13px;
        text-align: center;
    }
    
    .coord-group input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }
    
    .height-row {
        margin-top: 10px;
    }
    
    .height-row .coord-group {
        max-width: 80px;
    }
    
    .btn-add-field {
        width: 100%;
        background: #3b82f6;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .btn-add-field:hover {
        background: #2563eb;
    }
    
    .btn-save {
        width: 100%;
        background: #10b981;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .btn-save:hover {
        background: #059669;
    }
    
    .back-link {
        display: block;
        text-align: center;
        margin-top: 20px;
        color: #6b7280;
        text-decoration: none;
        font-size: 14px;
    }
    
    .back-link:hover {
        color: #374151;
    }
    
    .no-fields {
        text-align: center;
        color: #9ca3af;
        font-size: 13px;
        padding: 30px 20px;
        border: 2px dashed #e5e7eb;
        border-radius: 8px;
        margin-bottom: 15px;
    }
</style>
@endpush

@section('content')
<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="signature-editor">
                <div class="editor-header">
                    <h1>Designate Signatures for {{ $document->display_title }}</h1>
                    <p>Click and drag on the document preview to position signature fields</p>
                </div>
                
                @if(session('success'))
                    <div class="alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                <div class="editor-content">
                    <div class="pdf-preview-section">
                        <div class="pdf-preview-header">
                            <h2>Document Preview (<span id="totalPages">1</span> pages)</h2>
                            <div class="page-nav">
                                <button type="button" id="prevPage" disabled>&larr; Prev</button>
                                <span>Page <span id="currentPage">1</span> of <span id="pageCount">1</span></span>
                                <button type="button" id="nextPage">Next &rarr;</button>
                            </div>
                        </div>
                        
                        <div class="pdf-container" id="pdfContainer">
                            <div class="pdf-loading" id="pdfLoading">
                                <span>Loading document preview...</span>
                            </div>
                            <img src="" alt="PDF Page" class="pdf-page" id="pdfPage" style="display: none;">
                        </div>
                    </div>
                    
                    <div class="sidebar-section">
                        <h2>Signature Fields</h2>
                        
                        <div id="fieldsList">
                            <div class="no-fields" id="noFieldsMsg">No signature fields added yet.<br>Click on the document or use the button below.</div>
                        </div>
                        
                        <button type="button" class="btn-add-field" id="addFieldBtn">
                            <i class="fas fa-plus"></i> + Add Signature Field
                        </button>
                        
                        <form action="{{ route('signatures.save-fields', $document->id) }}" method="POST" id="signatureForm">
                            @csrf
                            <button type="submit" class="btn-save">
                                <i class="fas fa-save"></i> Save Signature Locations
                            </button>
                            
                            <div id="hiddenFields"></div>
                        </form>
                        
                        <a href="{{ route('signatures.show', $document->id) }}" class="back-link">
                            &larr; Back to Document Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    const documentId = {{ $document->id }};
    const documentUrl = @json($document->myfile);
    let currentPage = 1;
    let totalPages = 1;
    let signatureFields = [];
    let fieldIdCounter = 0;
    
    // Load existing fields
    const existingFields = @json($document->signatureFields ?? []);
    
    document.addEventListener('DOMContentLoaded', function() {
        // First fetch document info to get total pages, then load first page
        fetchDocumentInfo().then(() => {
            loadPdfPage(1);
            loadExistingFields();
        });
        
        document.getElementById('addFieldBtn').addEventListener('click', addSignatureField);
        document.getElementById('prevPage').addEventListener('click', () => changePage(-1));
        document.getElementById('nextPage').addEventListener('click', () => changePage(1));
        
        // Make PDF container clickable to add fields
        document.getElementById('pdfContainer').addEventListener('click', function(e) {
            if (e.target.id === 'pdfPage') {
                const rect = e.target.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                addSignatureFieldAt(x, y);
            }
        });
    });
    
    // Fetch document info including page count
    function fetchDocumentInfo() {
        return fetch(`/documents/${documentId}/info`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.page_count) {
                    totalPages = data.page_count;
                    updatePageNav();
                }
            })
            .catch(error => {
                console.error('Error fetching document info:', error);
                // Keep totalPages as 1 if fetch fails
            });
    }
    
    function loadPdfPage(page) {
        const loading = document.getElementById('pdfLoading');
        const img = document.getElementById('pdfPage');
        
        loading.style.display = 'flex';
        img.style.display = 'none';
        
        // Use the public route to get PDF page
        const pageUrl = `/documents/${documentId}/page/${page}`;
        
        img.onload = function() {
            loading.style.display = 'none';
            img.style.display = 'block';
            renderFieldsForPage(page);
        };
        
        img.onerror = function() {
            loading.innerHTML = '<span style="color: #ef4444;">Failed to load page. Please ensure the PDF service is running.</span>';
        };
        
        img.src = pageUrl;
    }
    
    function changePage(delta) {
        const newPage = currentPage + delta;
        if (newPage >= 1 && newPage <= totalPages) {
            currentPage = newPage;
            loadPdfPage(currentPage);
            updatePageNav();
        }
    }
    
    function updatePageNav() {
        document.getElementById('currentPage').textContent = currentPage;
        document.getElementById('pageCount').textContent = totalPages;
        document.getElementById('totalPages').textContent = totalPages;
        document.getElementById('prevPage').disabled = currentPage <= 1;
        document.getElementById('nextPage').disabled = currentPage >= totalPages;
    }
    
    function loadExistingFields() {
        existingFields.forEach(field => {
            signatureFields.push({
                id: ++fieldIdCounter,
                page: field.page,
                x_percent: parseFloat(field.x_percent),
                y_percent: parseFloat(field.y_percent),
                width_percent: parseFloat(field.width_percent),
                height_percent: parseFloat(field.height_percent),
                type: field.type || 'signature'
            });
        });
        updateFieldsList();
        renderFieldsForPage(currentPage);
    }
    
    function addSignatureField() {
        // Add field in center of current page
        addSignatureFieldAt(40, 30);
    }
    
    function addSignatureFieldAt(x, y) {
        const field = {
            id: ++fieldIdCounter,
            page: currentPage,
            x_percent: Math.max(0, Math.min(82, x)),
            y_percent: Math.max(0, Math.min(92, y)),
            width_percent: 18,
            height_percent: 8,
            type: 'signature'
        };
        
        signatureFields.push(field);
        updateFieldsList();
        renderFieldsForPage(currentPage);
    }
    
    function renderFieldsForPage(page) {
        // Remove existing field elements
        document.querySelectorAll('.signature-field').forEach(el => el.remove());
        
        const container = document.getElementById('pdfContainer');
        const img = document.getElementById('pdfPage');
        
        if (img.style.display === 'none') return;
        
        signatureFields.filter(f => f.page === page).forEach((field, index) => {
            const fieldEl = document.createElement('div');
            fieldEl.className = 'signature-field';
            fieldEl.dataset.fieldId = field.id;
            fieldEl.style.left = field.x_percent + '%';
            fieldEl.style.top = field.y_percent + '%';
            fieldEl.style.width = field.width_percent + '%';
            fieldEl.style.height = field.height_percent + '%';
            
            const fieldIndex = signatureFields.findIndex(f => f.id === field.id) + 1;
            fieldEl.innerHTML = `
                <span class="field-label">Signature ${fieldIndex}</span>
                <button type="button" class="delete-field" onclick="deleteField(${field.id})">&times;</button>
            `;
            
            // Make draggable
            makeDraggable(fieldEl, field);
            
            container.appendChild(fieldEl);
        });
    }
    
    function makeDraggable(element, field) {
        let isDragging = false;
        let startX, startY, startLeft, startTop;
        
        element.addEventListener('mousedown', function(e) {
            if (e.target.classList.contains('delete-field')) return;
            
            isDragging = true;
            startX = e.clientX;
            startY = e.clientY;
            startLeft = parseFloat(element.style.left);
            startTop = parseFloat(element.style.top);
            element.style.cursor = 'grabbing';
            e.preventDefault();
        });
        
        document.addEventListener('mousemove', function(e) {
            if (!isDragging) return;
            
            const container = document.getElementById('pdfContainer');
            const rect = container.getBoundingClientRect();
            
            const deltaX = ((e.clientX - startX) / rect.width) * 100;
            const deltaY = ((e.clientY - startY) / rect.height) * 100;
            
            const newLeft = Math.max(0, Math.min(100 - field.width_percent, startLeft + deltaX));
            const newTop = Math.max(0, Math.min(100 - field.height_percent, startTop + deltaY));
            
            element.style.left = newLeft + '%';
            element.style.top = newTop + '%';
            
            field.x_percent = newLeft;
            field.y_percent = newTop;
            
            // Update the input fields in real-time
            updateFieldInputs(field);
        });
        
        document.addEventListener('mouseup', function() {
            if (isDragging) {
                isDragging = false;
                element.style.cursor = 'move';
                updateHiddenFields();
            }
        });
    }
    
    function deleteField(fieldId) {
        signatureFields = signatureFields.filter(f => f.id !== fieldId);
        updateFieldsList();
        renderFieldsForPage(currentPage);
    }
    
    function previewField(fieldId) {
        const field = signatureFields.find(f => f.id === fieldId);
        if (field && field.page !== currentPage) {
            currentPage = field.page;
            loadPdfPage(currentPage);
            updatePageNav();
        }
        // Highlight the field briefly
        const fieldEl = document.querySelector(`.signature-field[data-field-id="${fieldId}"]`);
        if (fieldEl) {
            fieldEl.style.background = 'rgba(16, 185, 129, 0.4)';
            setTimeout(() => {
                fieldEl.style.background = 'rgba(16, 185, 129, 0.1)';
            }, 1000);
        }
    }
    
    function updateFieldInputs(field) {
        const xInput = document.getElementById(`field_x_${field.id}`);
        const yInput = document.getElementById(`field_y_${field.id}`);
        if (xInput) xInput.value = field.x_percent.toFixed(1);
        if (yInput) yInput.value = field.y_percent.toFixed(1);
    }
    
    function onCoordChange(fieldId, coordType, value) {
        const field = signatureFields.find(f => f.id === fieldId);
        if (!field) return;
        
        const numValue = parseFloat(value) || 0;
        
        switch(coordType) {
            case 'page':
                field.page = Math.max(1, Math.min(totalPages, Math.floor(numValue)));
                break;
            case 'x':
                field.x_percent = Math.max(0, Math.min(100, numValue));
                break;
            case 'y':
                field.y_percent = Math.max(0, Math.min(100, numValue));
                break;
            case 'width':
                field.width_percent = Math.max(5, Math.min(100, numValue));
                break;
            case 'height':
                field.height_percent = Math.max(3, Math.min(100, numValue));
                break;
        }
        
        renderFieldsForPage(currentPage);
        updateHiddenFields();
    }
    
    function updateFieldsList() {
        const list = document.getElementById('fieldsList');
        const hiddenFields = document.getElementById('hiddenFields');
        
        if (signatureFields.length === 0) {
            list.innerHTML = '<div class="no-fields" id="noFieldsMsg">No signature fields added yet.<br>Click on the document or use the button below.</div>';
            hiddenFields.innerHTML = '';
            return;
        }
        
        let html = '';
        signatureFields.forEach((field, index) => {
            html += `
                <div class="field-card" id="fieldCard_${field.id}">
                    <div class="field-card-header">
                        <span class="field-card-title">Signature ${index + 1}</span>
                        <div class="field-card-actions">
                            <button type="button" class="btn-preview" onclick="previewField(${field.id})">Preview</button>
                            <button type="button" class="btn-delete" onclick="deleteField(${field.id})">Delete</button>
                        </div>
                    </div>
                    <div class="field-card-coords">
                        <div class="coord-group">
                            <label>Page</label>
                            <input type="number" id="field_page_${field.id}" value="${field.page}" min="1" max="${totalPages}" onchange="onCoordChange(${field.id}, 'page', this.value)">
                        </div>
                        <div class="coord-group">
                            <label>X %</label>
                            <input type="number" id="field_x_${field.id}" value="${field.x_percent.toFixed(1)}" step="0.1" min="0" max="100" onchange="onCoordChange(${field.id}, 'x', this.value)">
                        </div>
                        <div class="coord-group">
                            <label>Y %</label>
                            <input type="number" id="field_y_${field.id}" value="${field.y_percent.toFixed(1)}" step="0.1" min="0" max="100" onchange="onCoordChange(${field.id}, 'y', this.value)">
                        </div>
                        <div class="coord-group">
                            <label>Width %</label>
                            <input type="number" id="field_w_${field.id}" value="${field.width_percent.toFixed(1)}" step="0.1" min="5" max="100" onchange="onCoordChange(${field.id}, 'width', this.value)">
                        </div>
                    </div>
                    <div class="height-row">
                        <div class="coord-group">
                            <label>Height %</label>
                            <input type="number" id="field_h_${field.id}" value="${field.height_percent.toFixed(1)}" step="0.1" min="3" max="100" onchange="onCoordChange(${field.id}, 'height', this.value)">
                        </div>
                    </div>
                </div>
            `;
        });
        
        list.innerHTML = html;
        updateHiddenFields();
    }
    
    function updateHiddenFields() {
        const hiddenFields = document.getElementById('hiddenFields');
        hiddenFields.innerHTML = '';
        
        signatureFields.forEach((field, index) => {
            hiddenFields.innerHTML += `
                <input type="hidden" name="fields[${index}][page]" value="${field.page}">
                <input type="hidden" name="fields[${index}][x_percent]" value="${field.x_percent}">
                <input type="hidden" name="fields[${index}][y_percent]" value="${field.y_percent}">
                <input type="hidden" name="fields[${index}][width_percent]" value="${field.width_percent}">
                <input type="hidden" name="fields[${index}][height_percent]" value="${field.height_percent}">
                <input type="hidden" name="fields[${index}][type]" value="${field.type}">
            `;
        });
    }
</script>
@endpush
