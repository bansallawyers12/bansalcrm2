<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign Document - {{ $document->display_title ?? 'Document' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: #1a1a2e;
            min-height: 100vh;
        }
        .header {
            background: #1a1a2e;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        .header-title {
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .btn {
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 14px;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-success:hover {
            background: #218838;
        }
        .btn-success:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        .main-container {
            margin-top: 60px;
            min-height: calc(100vh - 60px);
            background: #2d2d44;
            padding: 20px;
            overflow-y: auto;
        }
        .page-indicator {
            text-align: center;
            color: #888;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .page-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .page-wrapper {
            position: relative;
            margin-bottom: 20px;
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .page-image {
            width: 100%;
            display: block;
        }
        .signature-field {
            position: absolute;
            border: 2px dashed #28a745;
            background: rgba(40, 167, 69, 0.15);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .signature-field:hover {
            background: rgba(40, 167, 69, 0.25);
            border-color: #1e7e34;
        }
        .signature-field.signed {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.1);
            border-style: solid;
        }
        .signature-field-placeholder {
            color: #28a745;
            font-size: 13px;
            font-weight: 500;
            text-align: center;
            padding: 8px;
        }
        .signature-field.signed .signature-field-placeholder {
            display: none;
        }
        .signature-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow: hidden;
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }
        .modal-overlay.active .modal-content {
            transform: scale(1);
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            border-bottom: 1px solid #e9ecef;
        }
        .modal-header h2 {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            color: #999;
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }
        .modal-close:hover {
            color: #333;
        }
        .modal-body {
            padding: 25px;
        }

        /* Signature Tabs */
        .signature-tabs {
            display: flex;
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 25px;
        }
        .signature-tab {
            flex: 1;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            font-weight: 500;
            color: #6c757d;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s ease;
            font-size: 14px;
        }
        .signature-tab:hover {
            color: #28a745;
        }
        .signature-tab.active {
            color: #28a745;
            border-bottom-color: #28a745;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }

        /* Draw Signature */
        .signature-instruction {
            text-align: center;
            color: #888;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .signature-pad-container {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            background: white;
        }
        #signaturePad {
            width: 100%;
            height: 180px;
            background: white;
            display: block;
        }

        /* Type Signature */
        .type-signature-input {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 15px;
        }
        .type-signature-input:focus {
            outline: none;
            border-color: #28a745;
        }
        .type-signature-preview {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            min-height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            background: white;
        }
        .type-signature-text {
            font-family: 'Brush Script MT', cursive;
            font-size: 42px;
            color: #333;
        }

        /* Upload Signature */
        .upload-zone {
            border: 2px dashed #e9ecef;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 20px;
        }
        .upload-zone:hover {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.05);
        }
        .upload-zone i {
            font-size: 40px;
            color: #ccc;
            margin-bottom: 10px;
        }
        .upload-zone p {
            color: #888;
            margin-bottom: 5px;
        }
        .upload-preview {
            max-width: 100%;
            max-height: 120px;
            margin-top: 15px;
        }

        /* Modal Footer */
        .modal-footer {
            display: flex;
            gap: 10px;
            justify-content: center;
            padding: 20px 25px;
            border-top: 1px solid #e9ecef;
            background: #f8f9fa;
        }
        .btn-clear {
            background: #dc3545;
            color: white;
            padding: 12px 30px;
        }
        .btn-clear:hover {
            background: #c82333;
        }
        .btn-cancel {
            background: #6c757d;
            color: white;
            padding: 12px 30px;
        }
        .btn-cancel:hover {
            background: #5a6268;
        }
        .btn-save {
            background: #28a745;
            color: white;
            padding: 12px 30px;
        }
        .btn-save:hover {
            background: #218838;
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.95);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 3000;
        }
        .loading-overlay.hidden {
            display: none;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e9ecef;
            border-top-color: #28a745;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .loading-text {
            margin-top: 20px;
            font-size: 16px;
            color: #666;
        }

        /* Progress Bar */
        .progress-bar-container {
            background: rgba(255,255,255,0.1);
            padding: 8px 15px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .progress-bar-container span {
            color: white;
            font-size: 13px;
        }

        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 10px;
            }
            .header-title span {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
        <div class="loading-text">Loading document...</div>
    </div>

    <header class="header">
        <div class="header-title">
            <i class="fas fa-file-signature"></i>
            <span>{{ $document->display_title ?? 'Document' }}</span>
        </div>
        <div class="header-actions">
            <div class="progress-bar-container" id="progressContainer" style="display: none;">
                <i class="fas fa-check-circle" style="color: #28a745;"></i>
                <span id="progressText">0 of 0 signed</span>
            </div>
            <button type="button" class="btn btn-success" id="submitBtn" disabled onclick="submitSignatures()">
                <i class="fas fa-check"></i> Complete Signing
            </button>
        </div>
    </header>

    <div class="main-container">
        <div class="page-indicator" id="pageIndicator">Page 1</div>
        <div class="page-container" id="pageContainer">
            <!-- Pages will be loaded here -->
        </div>
    </div>

    <!-- Sign Modal -->
    <div class="modal-overlay" id="signModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Sign Here</h2>
                <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="signature-tabs">
                    <div class="signature-tab active" data-tab="draw">Draw</div>
                    <div class="signature-tab" data-tab="type">Type</div>
                    <div class="signature-tab" data-tab="upload">Upload</div>
                </div>

                <!-- Draw Tab -->
                <div class="tab-content active" id="tab-draw">
                    <p class="signature-instruction">Use your mouse, touch, or stylus to draw your signature below</p>
                    <div class="signature-pad-container">
                        <canvas id="signaturePad"></canvas>
                    </div>
                </div>

                <!-- Type Tab -->
                <div class="tab-content" id="tab-type">
                    <input type="text" class="type-signature-input" id="typeInput" placeholder="Type your full name" oninput="updateTypePreview()">
                    <div class="type-signature-preview">
                        <span class="type-signature-text" id="typePreview"></span>
                    </div>
                </div>

                <!-- Upload Tab -->
                <div class="tab-content" id="tab-upload">
                    <div class="upload-zone" onclick="document.getElementById('uploadInput').click()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Click to upload signature image</p>
                        <small style="color: #aaa;">PNG, JPG up to 2MB</small>
                        <img id="uploadPreview" class="upload-preview" style="display: none;">
                    </div>
                    <input type="file" id="uploadInput" accept="image/*" style="display: none;" onchange="handleUpload(this)">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-clear" onclick="clearCurrentSignature()">
                    <i class="fas fa-eraser"></i> Clear
                </button>
                <button type="button" class="btn btn-cancel" onclick="closeModal()">
                    Cancel
                </button>
                <button type="button" class="btn btn-save" id="saveSignatureBtn" onclick="saveSignature()">
                    Save Signature
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        // Configuration
        const documentId = {{ $document->id }};
        const signerToken = '{{ $signer->token }}';
        const totalPages = {{ $pdfPages ?? 1 }};
        const signatureFields = @json($signatureFields ?? []);
        
        // State
        let signaturePad;
        let currentFieldId = null;
        let signedFields = {};
        let uploadedImage = null;
        let currentTab = 'draw';

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initSignaturePad();
            initTabs();
            loadPages();
        });

        function initSignaturePad() {
            const canvas = document.getElementById('signaturePad');
            signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)',
                minWidth: 1,
                maxWidth: 3
            });

            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const container = canvas.parentElement;
                canvas.width = container.offsetWidth * ratio;
                canvas.height = 180 * ratio;
                canvas.getContext('2d').scale(ratio, ratio);
                signaturePad.clear();
            }

            window.addEventListener('resize', resizeCanvas);
            setTimeout(resizeCanvas, 100);
        }

        function initTabs() {
            document.querySelectorAll('.signature-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabName = this.dataset.tab;
                    currentTab = tabName;
                    
                    document.querySelectorAll('.signature-tab').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                    document.getElementById('tab-' + tabName).classList.add('active');

                    // Resize canvas when switching to draw tab
                    if (tabName === 'draw') {
                        setTimeout(() => {
                            const canvas = document.getElementById('signaturePad');
                            const ratio = Math.max(window.devicePixelRatio || 1, 1);
                            const container = canvas.parentElement;
                            canvas.width = container.offsetWidth * ratio;
                            canvas.height = 180 * ratio;
                            canvas.getContext('2d').scale(ratio, ratio);
                        }, 50);
                    }
                });
            });
        }

        async function loadPages() {
            const container = document.getElementById('pageContainer');
            container.innerHTML = '';

            for (let i = 1; i <= totalPages; i++) {
                const wrapper = document.createElement('div');
                wrapper.className = 'page-wrapper';
                wrapper.id = 'page-' + i;
                
                const img = document.createElement('img');
                img.className = 'page-image';
                img.src = `{{ url('/documents') }}/${documentId}/page/${i}?token=${signerToken}`;
                img.alt = 'Page ' + i;
                img.onload = function() {
                    addSignatureFields(wrapper, i);
                };
                
                wrapper.appendChild(img);
                container.appendChild(wrapper);
            }

            document.getElementById('loadingOverlay').classList.add('hidden');
            document.getElementById('pageIndicator').textContent = 'Page 1 of ' + totalPages;
            updateProgress();
        }

        function addSignatureFields(wrapper, pageNum) {
            const pageFields = signatureFields.filter(f => f.page_number == pageNum);
            
            pageFields.forEach(field => {
                const fieldEl = document.createElement('div');
                fieldEl.className = 'signature-field';
                fieldEl.id = 'field-' + field.id;
                fieldEl.dataset.fieldId = field.id;
                
                fieldEl.style.left = field.x_percent + '%';
                fieldEl.style.top = field.y_percent + '%';
                fieldEl.style.width = (field.width_percent || 20) + '%';
                fieldEl.style.height = (field.height_percent || 10) + '%';
                
                fieldEl.innerHTML = '<div class="signature-field-placeholder"><i class="fas fa-pen"></i> Click to sign</div>';
                fieldEl.onclick = function() { openSignModal(field.id); };
                
                wrapper.appendChild(fieldEl);
            });
        }

        function openSignModal(fieldId) {
            currentFieldId = fieldId;
            document.getElementById('signModal').classList.add('active');
            
            // Reset modal
            signaturePad.clear();
            document.getElementById('typeInput').value = '';
            document.getElementById('typePreview').textContent = '';
            uploadedImage = null;
            document.getElementById('uploadPreview').style.display = 'none';
            
            // Resize canvas
            setTimeout(() => {
                const canvas = document.getElementById('signaturePad');
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const container = canvas.parentElement;
                canvas.width = container.offsetWidth * ratio;
                canvas.height = 180 * ratio;
                canvas.getContext('2d').scale(ratio, ratio);
            }, 100);
        }

        function closeModal() {
            document.getElementById('signModal').classList.remove('active');
            currentFieldId = null;
        }

        function clearCurrentSignature() {
            if (currentTab === 'draw') {
                signaturePad.clear();
            } else if (currentTab === 'type') {
                document.getElementById('typeInput').value = '';
                document.getElementById('typePreview').textContent = '';
            } else if (currentTab === 'upload') {
                uploadedImage = null;
                document.getElementById('uploadPreview').style.display = 'none';
                document.getElementById('uploadInput').value = '';
            }
        }

        function updateTypePreview() {
            const input = document.getElementById('typeInput');
            document.getElementById('typePreview').textContent = input.value;
        }

        function handleUpload(input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    uploadedImage = e.target.result;
                    const preview = document.getElementById('uploadPreview');
                    preview.src = uploadedImage;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }

        function saveSignature() {
            let signatureData = null;

            if (currentTab === 'draw') {
                if (signaturePad.isEmpty()) {
                    alert('Please draw your signature first.');
                    return;
                }
                signatureData = signaturePad.toDataURL('image/png');
            } else if (currentTab === 'type') {
                const text = document.getElementById('typeInput').value.trim();
                if (!text) {
                    alert('Please type your name first.');
                    return;
                }
                // Create canvas for typed signature
                const canvas = document.createElement('canvas');
                canvas.width = 400;
                canvas.height = 120;
                const ctx = canvas.getContext('2d');
                
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                
                ctx.fillStyle = 'black';
                ctx.font = '48px "Brush Script MT", cursive';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(text, canvas.width / 2, canvas.height / 2);
                
                signatureData = canvas.toDataURL('image/png');
            } else if (currentTab === 'upload') {
                if (!uploadedImage) {
                    alert('Please upload a signature image first.');
                    return;
                }
                signatureData = uploadedImage;
            }

            if (signatureData && currentFieldId) {
                applySignatureToField(currentFieldId, signatureData);
                closeModal();
            }
        }

        function applySignatureToField(fieldId, signatureData) {
            const fieldEl = document.getElementById('field-' + fieldId);
            if (!fieldEl) return;

            fieldEl.innerHTML = '';
            const img = document.createElement('img');
            img.src = signatureData;
            img.className = 'signature-image';
            fieldEl.appendChild(img);
            fieldEl.classList.add('signed');

            signedFields[fieldId] = signatureData;
            updateProgress();
        }

        function updateProgress() {
            const totalFields = signatureFields.length;
            const signedCount = Object.keys(signedFields).length;
            
            const progressContainer = document.getElementById('progressContainer');
            const progressText = document.getElementById('progressText');
            const submitBtn = document.getElementById('submitBtn');

            if (totalFields > 0) {
                progressContainer.style.display = 'flex';
                progressText.textContent = `${signedCount} of ${totalFields} signed`;
                submitBtn.disabled = signedCount < totalFields;
            } else {
                submitBtn.disabled = signedCount === 0;
            }
        }

        async function submitSignatures() {
            if (Object.keys(signedFields).length === 0) {
                alert('Please sign at least one field before submitting.');
                return;
            }

            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.querySelector('.loading-text').textContent = 'Submitting signatures...';
            loadingOverlay.classList.remove('hidden');

            try {
                const response = await fetch('{{ route("public.documents.submitSignatures", $document->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        token: signerToken,
                        signatures: signedFields
                    })
                });

                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    const result = await response.json();

                    if (result.success) {
                        window.location.href = result.redirect || '/documents/thankyou/{{ $document->id }}';
                    } else {
                        throw new Error(result.message || 'Failed to submit signatures');
                    }
                } else {
                    // If not JSON, it might be a redirect or HTML error page
                    if (response.ok) {
                        window.location.href = '/documents/thankyou/{{ $document->id }}';
                    } else {
                        const text = await response.text();
                        console.error('Non-JSON response:', text.substring(0, 500));
                        throw new Error('Server returned an unexpected response. Please try again.');
                    }
                }
            } catch (error) {
                loadingOverlay.classList.add('hidden');
                alert('Error: ' + error.message);
                console.error('Submit error:', error);
            }
        }
    </script>
</body>
</html>
