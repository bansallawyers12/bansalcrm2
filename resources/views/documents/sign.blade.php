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
            background: #f5f7fa;
            min-height: 100vh;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header-title {
            font-size: 18px;
            font-weight: 600;
        }
        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-primary {
            background: white;
            color: #667eea;
        }
        .btn-primary:hover {
            background: #f0f0f0;
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
            display: flex;
            margin-top: 60px;
            height: calc(100vh - 60px);
        }
        .document-viewer {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #e9ecef;
        }
        .page-container {
            max-width: 850px;
            margin: 0 auto;
        }
        .page-wrapper {
            position: relative;
            margin-bottom: 20px;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .page-image {
            width: 100%;
            display: block;
        }
        .signature-field {
            position: absolute;
            border: 2px dashed #667eea;
            background: rgba(102, 126, 234, 0.1);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .signature-field:hover {
            background: rgba(102, 126, 234, 0.2);
            border-color: #764ba2;
        }
        .signature-field.signed {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.1);
        }
        .signature-field-placeholder {
            color: #667eea;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            padding: 10px;
        }
        .signature-field.signed .signature-field-placeholder {
            display: none;
        }
        .signature-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .sidebar {
            width: 350px;
            background: white;
            border-left: 1px solid #e9ecef;
            display: flex;
            flex-direction: column;
        }
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        .sidebar-header h2 {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .sidebar-header p {
            font-size: 14px;
            color: #6c757d;
        }
        .sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        .signature-tabs {
            display: flex;
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 20px;
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
        }
        .signature-tab:hover {
            color: #667eea;
        }
        .signature-tab.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .signature-pad-container {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
        }
        #signaturePad {
            width: 100%;
            height: 200px;
            background: white;
        }
        .signature-pad-actions {
            display: flex;
            gap: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        .signature-pad-actions button {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: white;
            cursor: pointer;
            font-size: 13px;
        }
        .signature-pad-actions button:hover {
            background: #f8f9fa;
        }
        .type-signature-input {
            width: 100%;
            padding: 15px;
            font-size: 24px;
            font-family: 'Brush Script MT', cursive;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 15px;
        }
        .type-signature-preview {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            min-height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            background: white;
        }
        .type-signature-text {
            font-family: 'Brush Script MT', cursive;
            font-size: 36px;
            color: #2c3e50;
        }
        .upload-zone {
            border: 2px dashed #e9ecef;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 15px;
        }
        .upload-zone:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
        }
        .upload-zone i {
            font-size: 48px;
            color: #6c757d;
            margin-bottom: 15px;
        }
        .upload-zone p {
            color: #6c757d;
            margin-bottom: 10px;
        }
        .upload-preview {
            max-width: 100%;
            max-height: 150px;
            margin-top: 15px;
        }
        .apply-signature-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
        }
        .apply-signature-btn:hover {
            opacity: 0.9;
        }
        .apply-signature-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        .progress-indicator {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            padding: 15px;
            background: #e8f5e9;
            border-radius: 8px;
        }
        .progress-indicator i {
            color: #28a745;
            font-size: 20px;
        }
        .progress-indicator span {
            font-weight: 500;
            color: #2e7d32;
        }
        .signer-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .signer-info h4 {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .signer-info p {
            font-size: 16px;
            color: #2c3e50;
            font-weight: 500;
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.9);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }
        .loading-overlay.hidden {
            display: none;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e9ecef;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .loading-text {
            margin-top: 20px;
            font-size: 16px;
            color: #6c757d;
        }
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                border-left: none;
                border-top: 1px solid #e9ecef;
            }
            .document-viewer {
                max-height: 50vh;
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
            {{ $document->display_title ?? 'Document' }}
        </div>
        <div class="header-actions">
            <button type="button" class="btn btn-success" id="submitBtn" disabled onclick="submitSignatures()">
                <i class="fas fa-check"></i> Complete Signing
            </button>
        </div>
    </header>

    <div class="main-container">
        <div class="document-viewer" id="documentViewer">
            <div class="page-container" id="pageContainer">
                <!-- Pages will be loaded here -->
            </div>
        </div>

        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Sign Document</h2>
                <p>Create your signature and click on the highlighted areas to sign.</p>
            </div>
            
            <div class="sidebar-content">
                <div class="signer-info">
                    <h4>Signing as</h4>
                    <p>{{ $signer->name }}</p>
                    <small class="text-muted">{{ $signer->email }}</small>
                </div>

                <div class="progress-indicator" id="progressIndicator" style="display: none;">
                    <i class="fas fa-check-circle"></i>
                    <span id="progressText">0 of 0 signatures placed</span>
                </div>

                <div class="signature-tabs">
                    <div class="signature-tab active" data-tab="draw">
                        <i class="fas fa-pen"></i> Draw
                    </div>
                    <div class="signature-tab" data-tab="type">
                        <i class="fas fa-keyboard"></i> Type
                    </div>
                    <div class="signature-tab" data-tab="upload">
                        <i class="fas fa-upload"></i> Upload
                    </div>
                </div>

                <!-- Draw Tab -->
                <div class="tab-content active" id="tab-draw">
                    <div class="signature-pad-container">
                        <canvas id="signaturePad"></canvas>
                        <div class="signature-pad-actions">
                            <button type="button" onclick="clearSignaturePad()">
                                <i class="fas fa-eraser"></i> Clear
                            </button>
                            <button type="button" onclick="undoSignaturePad()">
                                <i class="fas fa-undo"></i> Undo
                            </button>
                        </div>
                    </div>
                    <button type="button" class="apply-signature-btn" onclick="applyDrawnSignature()" id="applyDrawBtn" disabled>
                        Apply Signature
                    </button>
                </div>

                <!-- Type Tab -->
                <div class="tab-content" id="tab-type">
                    <input type="text" class="type-signature-input" id="typeInput" placeholder="Type your name" oninput="updateTypePreview()">
                    <div class="type-signature-preview">
                        <span class="type-signature-text" id="typePreview"></span>
                    </div>
                    <button type="button" class="apply-signature-btn" onclick="applyTypedSignature()" id="applyTypeBtn" disabled>
                        Apply Signature
                    </button>
                </div>

                <!-- Upload Tab -->
                <div class="tab-content" id="tab-upload">
                    <div class="upload-zone" onclick="document.getElementById('uploadInput').click()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Click to upload signature image</p>
                        <small>PNG, JPG up to 2MB</small>
                        <img id="uploadPreview" class="upload-preview" style="display: none;">
                    </div>
                    <input type="file" id="uploadInput" accept="image/*" style="display: none;" onchange="handleUpload(this)">
                    <button type="button" class="apply-signature-btn" onclick="applyUploadedSignature()" id="applyUploadBtn" disabled>
                        Apply Signature
                    </button>
                </div>
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
        let currentSignatureData = null;
        let signedFields = {};
        let uploadedImage = null;

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
                penColor: 'rgb(0, 0, 0)'
            });

            // Resize canvas
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const container = canvas.parentElement;
                canvas.width = container.offsetWidth * ratio;
                canvas.height = 200 * ratio;
                canvas.getContext('2d').scale(ratio, ratio);
                signaturePad.clear();
            }

            window.addEventListener('resize', resizeCanvas);
            resizeCanvas();

            // Enable apply button when signature is drawn
            signaturePad.addEventListener('endStroke', function() {
                document.getElementById('applyDrawBtn').disabled = signaturePad.isEmpty();
            });
        }

        function initTabs() {
            document.querySelectorAll('.signature-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabName = this.dataset.tab;
                    
                    // Update active tab
                    document.querySelectorAll('.signature-tab').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Show tab content
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                    document.getElementById('tab-' + tabName).classList.add('active');
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
                    addSignatureFields(wrapper, i, img.naturalWidth, img.naturalHeight);
                };
                
                wrapper.appendChild(img);
                container.appendChild(wrapper);
            }

            document.getElementById('loadingOverlay').classList.add('hidden');
            updateProgress();
        }

        function addSignatureFields(wrapper, pageNum, imgWidth, imgHeight) {
            const pageFields = signatureFields.filter(f => f.page_number == pageNum);
            
            pageFields.forEach(field => {
                const fieldEl = document.createElement('div');
                fieldEl.className = 'signature-field';
                fieldEl.id = 'field-' + field.id;
                fieldEl.dataset.fieldId = field.id;
                
                // Position using percentages
                fieldEl.style.left = field.x_percent + '%';
                fieldEl.style.top = field.y_percent + '%';
                fieldEl.style.width = (field.width_percent || 20) + '%';
                fieldEl.style.height = (field.height_percent || 10) + '%';
                
                fieldEl.innerHTML = '<div class="signature-field-placeholder"><i class="fas fa-pen"></i> Click to sign</div>';
                fieldEl.onclick = function() { signField(field.id); };
                
                wrapper.appendChild(fieldEl);
            });
        }

        function clearSignaturePad() {
            signaturePad.clear();
            document.getElementById('applyDrawBtn').disabled = true;
        }

        function undoSignaturePad() {
            const data = signaturePad.toData();
            if (data && data.length > 0) {
                data.pop();
                signaturePad.fromData(data);
            }
            document.getElementById('applyDrawBtn').disabled = signaturePad.isEmpty();
        }

        function updateTypePreview() {
            const input = document.getElementById('typeInput');
            const preview = document.getElementById('typePreview');
            preview.textContent = input.value;
            document.getElementById('applyTypeBtn').disabled = !input.value.trim();
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
                    document.getElementById('applyUploadBtn').disabled = false;
                };
                reader.readAsDataURL(file);
            }
        }

        function applyDrawnSignature() {
            if (!signaturePad.isEmpty()) {
                currentSignatureData = signaturePad.toDataURL('image/png');
                highlightFieldsForSigning();
            }
        }

        function applyTypedSignature() {
            const text = document.getElementById('typeInput').value.trim();
            if (text) {
                // Create canvas to render typed signature
                const canvas = document.createElement('canvas');
                canvas.width = 400;
                canvas.height = 150;
                const ctx = canvas.getContext('2d');
                
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                
                ctx.fillStyle = 'black';
                ctx.font = '48px "Brush Script MT", cursive';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(text, canvas.width / 2, canvas.height / 2);
                
                currentSignatureData = canvas.toDataURL('image/png');
                highlightFieldsForSigning();
            }
        }

        function applyUploadedSignature() {
            if (uploadedImage) {
                currentSignatureData = uploadedImage;
                highlightFieldsForSigning();
            }
        }

        function highlightFieldsForSigning() {
            document.querySelectorAll('.signature-field:not(.signed)').forEach(field => {
                field.style.animation = 'pulse 1s infinite';
            });
        }

        function signField(fieldId) {
            if (!currentSignatureData) {
                alert('Please create a signature first using Draw, Type, or Upload options.');
                return;
            }

            const fieldEl = document.getElementById('field-' + fieldId);
            if (fieldEl.classList.contains('signed')) {
                if (!confirm('This field is already signed. Replace signature?')) {
                    return;
                }
            }

            // Add signature image
            fieldEl.innerHTML = '';
            const img = document.createElement('img');
            img.src = currentSignatureData;
            img.className = 'signature-image';
            fieldEl.appendChild(img);
            fieldEl.classList.add('signed');
            fieldEl.style.animation = '';

            signedFields[fieldId] = currentSignatureData;
            updateProgress();
        }

        function updateProgress() {
            const totalFields = signatureFields.length;
            const signedCount = Object.keys(signedFields).length;
            
            const progressEl = document.getElementById('progressIndicator');
            const progressText = document.getElementById('progressText');
            const submitBtn = document.getElementById('submitBtn');

            if (totalFields > 0) {
                progressEl.style.display = 'flex';
                progressText.textContent = `${signedCount} of ${totalFields} signatures placed`;
                submitBtn.disabled = signedCount < totalFields;
            } else {
                // No pre-defined fields, allow any signature
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        token: signerToken,
                        signatures: signedFields
                    })
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = result.redirect || '/documents/thankyou';
                } else {
                    throw new Error(result.message || 'Failed to submit signatures');
                }
            } catch (error) {
                loadingOverlay.classList.add('hidden');
                alert('Error: ' + error.message);
            }
        }

        // Add pulse animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.4); }
                70% { box-shadow: 0 0 0 10px rgba(102, 126, 234, 0); }
                100% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
