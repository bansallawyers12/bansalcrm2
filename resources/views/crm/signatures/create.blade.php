@extends('layouts.admin')
@section('title', isset($document) ? 'Add Signer to Document' : 'Upload Document for Signature')

@push('styles')
<style>
    .upload-container {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: calc(100vh - 200px);
        padding: 40px 20px;
    }
    
    .upload-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 40px;
        width: 100%;
        max-width: 700px;
    }
    
    /* Breadcrumb */
    .breadcrumb-nav {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 15px;
    }
    
    .breadcrumb-nav a {
        color: #3b82f6;
        text-decoration: none;
    }
    
    .breadcrumb-nav a:hover {
        text-decoration: underline;
    }
    
    .breadcrumb-nav span {
        color: #9ca3af;
        margin: 0 8px;
    }
    
    /* Page title with icon */
    .page-title {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 30px;
    }
    
    .page-title-icon {
        width: 36px;
        height: 36px;
        background: #e0e7ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4f46e5;
    }
    
    .page-title h1 {
        font-size: 22px;
        font-weight: 600;
        color: #1a202c;
        margin: 0;
    }
    
    /* Hide any document preview elements that may appear between page-title and wizard-steps */
    .page-title + .document-preview,
    .page-title + .signature-preview,
    .page-title + .pdf-preview,
    .page-title + div:not(.wizard-steps) {
        display: none !important;
    }
    
    /* Wizard steps indicator */
    .wizard-steps {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 30px;
    }
    
    .wizard-step {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #e5e7eb;
    }
    
    /*.wizard-step.active {
        background: #3b82f6;
    }
    
    .wizard-step.completed {
        background: #10b981;
    }*/
    
    /* Section header */
    .section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .section-header .icon {
        color: #6366f1;
        font-size: 18px;
    }
    
    .section-header h2 {
        font-size: 16px;
        font-weight: 600;
        color: #374151;
        margin: 0;
    }
    
    .form-group {
        margin-bottom: 24px;
    }
    
    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 8px;
    }
    
    .form-group label .required {
        color: #ef4444;
    }
    
    .form-group input[type="text"],
    .form-group input[type="email"] {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 15px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    
    .form-group input[type="text"]:focus,
    .form-group input[type="email"]:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .form-group input::placeholder {
        color: #9ca3af;
    }
    
    .form-help {
        font-size: 13px;
        color: #6b7280;
        margin-top: 6px;
    }
    
    .file-input-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .file-input-wrapper input[type="file"] {
        display: none;
    }
    
    .file-choose-btn {
        background: #3b82f6;
        color: white;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        border: none;
        transition: background-color 0.2s;
    }
    
    .file-choose-btn:hover {
        background: #2563eb;
    }
    
    .file-name {
        font-size: 14px;
        color: #6b7280;
    }
    
    /* Button row */
    .button-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }
    
    .button-row-right {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .btn-cancel {
        background: #6b7280;
        color: white;
        padding: 12px 32px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: background-color 0.2s;
    }
    
    .btn-cancel:hover {
        background: #4b5563;
        color: white;
        text-decoration: none;
    }
    
    .btn-back {
        background: #6b7280;
        color: white;
        padding: 12px 32px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.2s;
    }
    
    .btn-back:hover {
        background: #4b5563;
    }
    
    .btn-next {
        background: #6366f1;
        color: white;
        padding: 12px 32px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.2s;
    }
    
    .btn-next:hover {
        background: #4f46e5;
    }
    
    .btn-add-signer {
        background: #6366f1;
        color: white;
        padding: 12px 32px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.2s;
    }
    
    .btn-add-signer:hover {
        background: #4f46e5;
    }
    
    .btn-upload {
        background: #3b82f6;
        color: white;
        padding: 12px 32px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .btn-upload:hover {
        background: #2563eb;
    }
    
    .back-link {
        display: block;
        text-align: center;
        margin-top: 24px;
        color: #3b82f6;
        text-decoration: none;
        font-size: 14px;
    }
    
    .back-link:hover {
        text-decoration: underline;
    }
    
    .alert {
        margin-bottom: 20px;
        padding: 12px 16px;
        border-radius: 8px;
    }
    
    .alert-danger {
        background-color: #fef2f2;
        border: 1px solid #fecaca;
        color: #dc2626;
    }
    
    .alert-success {
        background-color: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #16a34a;
    }
    
    .alert-danger ul,
    .alert-success ul {
        margin: 0;
        padding-left: 20px;
    }
    
    /* Wizard step panels */
    .wizard-panel {
        display: none;
    }
    
    .wizard-panel.active {
        display: block;
    }
</style>
@endpush

@section('content')
<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="upload-container">
                <div class="upload-card">
                    
                    @if(isset($document) && $document)
                        {{-- Add Signer Flow --}}
                        <nav class="breadcrumb-nav">
                            <a href="{{ route('signatures.index') }}">Signature Dashboard</a>
                            <span>/</span>
                            <a href="{{ route('signatures.show', $document->id) }}">Document Details</a>
                            <span>/</span>
                            Add Signer
                        </nav>
                        
                        <div class="page-title">
                            <div class="page-title-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <h1>Add Signer to Document</h1>
                        </div>
                        
                        <div class="wizard-steps">
                            <div class="wizard-step active" id="step1Dot"></div>
                            <div class="wizard-step" id="step2Dot"></div>
                        </div>
                        
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form action="{{ route('signatures.store') }}" method="POST" id="signerForm">
                            @csrf
                            <input type="hidden" name="document_id" value="{{ $document->id }}">
                            
                            {{-- Step 1: Find Signer (Email) --}}
                            <div class="wizard-panel active" id="step1Panel">
                                <div class="section-header">
                                    <span class="icon"><i class="fas fa-envelope"></i></span>
                                    <h2>Find Signer</h2>
                                </div>
                                
                                <div class="form-group">
                                    <label for="signer_email">Signer Email <span class="required">*</span></label>
                                    <input type="email" id="signer_email" name="signer_email" 
                                           placeholder="john@example.com" 
                                           value="{{ old('signer_email') }}">
                                    <p class="form-help">Enter the email address to find existing clients/leads</p>
                                </div>
                                
                                <div class="button-row">
                                    <a href="{{ route('signatures.show', $document->id) }}" class="btn-cancel">Cancel</a>
                                    <button type="button" class="btn-next" id="nextBtn">
                                        Next <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                            
                            {{-- Step 2: Signer Information (Name) --}}
                            <div class="wizard-panel" id="step2Panel">
                                <div class="section-header">
                                    <span class="icon"><i class="fas fa-user-plus"></i></span>
                                    <h2>Signer Information</h2>
                                </div>
                                
                                <div class="form-group">
                                    <label for="signer_name">Signer Name <span class="required">*</span></label>
                                    <input type="text" id="signer_name" name="signer_name" 
                                           placeholder="John Doe" 
                                           value="{{ old('signer_name') }}">
                                    <p class="form-help">Enter the full name of the signer</p>
                                </div>
                                
                                <div class="button-row">
                                    <a href="{{ route('signatures.show', $document->id) }}" class="btn-cancel">Cancel</a>
                                    <div class="button-row-right">
                                        <button type="button" class="btn-back" id="backBtn">
                                            <i class="fas fa-arrow-left"></i> Back
                                        </button>
                                        <button type="submit" class="btn-add-signer">
                                            <i class="fas fa-user-plus"></i> Add Signer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                    @else
                        {{-- Upload Document Flow --}}
                        <h1 style="font-size: 24px; font-weight: 600; color: #1a202c; text-align: center; margin-bottom: 30px;">Upload Document</h1>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form action="{{ route('signatures.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" id="title" name="title" placeholder="Enter document title" value="{{ old('title') }}">
                            </div>
                            
                            <div class="form-group">
                                <label>Document (PDF)</label>
                                <div class="file-input-wrapper">
                                    <label for="file" class="file-choose-btn">Choose file</label>
                                    <input type="file" id="file" name="file" accept=".pdf,.doc,.docx" required onchange="updateFileName(this)">
                                    <span class="file-name" id="fileName">No file chosen</span>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn-upload" style="float: right;">Upload</button>
                            
                            <a href="{{ route('signatures.index') }}" class="back-link" style="clear: both; padding-top: 60px;">Back to Documents</a>
                        </form>
                    @endif
                    
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    function updateFileName(input) {
        const fileName = input.files[0] ? input.files[0].name : 'No file chosen';
        document.getElementById('fileName').textContent = fileName;
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const nextBtn = document.getElementById('nextBtn');
        const backBtn = document.getElementById('backBtn');
        const step1Panel = document.getElementById('step1Panel');
        const step2Panel = document.getElementById('step2Panel');
        const step1Dot = document.getElementById('step1Dot');
        const step2Dot = document.getElementById('step2Dot');
        const emailInput = document.getElementById('signer_email');
        const nameInput = document.getElementById('signer_name');
        
        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                // Validate email
                const email = emailInput.value.trim();
                if (!email) {
                    alert('Please enter a signer email address.');
                    emailInput.focus();
                    return;
                }
                
                // Simple email validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    alert('Please enter a valid email address.');
                    emailInput.focus();
                    return;
                }
                
                // Switch to step 2
                step1Panel.classList.remove('active');
                step2Panel.classList.add('active');
                step1Dot.classList.remove('active');
                step1Dot.classList.add('completed');
                step2Dot.classList.add('active');
                
                // Focus on name input
                nameInput.focus();
            });
        }
        
        if (backBtn) {
            backBtn.addEventListener('click', function() {
                // Switch back to step 1
                step2Panel.classList.remove('active');
                step1Panel.classList.add('active');
                step2Dot.classList.remove('active');
                step1Dot.classList.remove('completed');
                step1Dot.classList.add('active');
                
                // Focus on email input
                emailInput.focus();
            });
        }
        
        // Handle form submission validation
        const form = document.getElementById('signerForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const email = emailInput.value.trim();
                const name = nameInput.value.trim();
                
                if (!email) {
                    e.preventDefault();
                    alert('Please enter a signer email address.');
                    // Switch to step 1 if needed
                    step2Panel.classList.remove('active');
                    step1Panel.classList.add('active');
                    step2Dot.classList.remove('active');
                    step1Dot.classList.remove('completed');
                    step1Dot.classList.add('active');
                    emailInput.focus();
                    return;
                }
                
                if (!name) {
                    e.preventDefault();
                    alert('Please enter a signer name.');
                    nameInput.focus();
                    return;
                }
            });
        }
        
        // If there's a validation error for name, show step 2
        @if(old('signer_name') || $errors->has('signer_name'))
            step1Panel.classList.remove('active');
            step2Panel.classList.add('active');
            step1Dot.classList.remove('active');
            step1Dot.classList.add('completed');
            step2Dot.classList.add('active');
        @endif
    });
</script>
@endpush
