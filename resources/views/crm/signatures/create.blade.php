@extends('layouts.admin')
@section('title', 'Send New Document for Signature')

@push('styles')
<style>
    .send-document-container {
        max-width: 900px;
        margin: 30px auto;
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .page-header {
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .page-header h1 {
        font-size: 24px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
        font-size: 14px;
    }
    .form-section {
        padding: 25px 0;
        border-bottom: 1px solid #eee;
    }
    .form-section:last-child {
        border-bottom: none;
    }
    .form-section h2 {
        font-size: 20px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
    }
    .form-group label {
        font-weight: 600;
        color: #495057;
    }
    .form-control {
        border-radius: 5px;
    }
    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-primary-custom:hover {
        color: white;
        opacity: 0.9;
    }
    .match-alert {
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        border-left: 4px solid #4caf50;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: none;
        animation: slideDown 0.3s ease;
    }
    .match-alert.show {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .match-alert-icon {
        font-size: 32px;
        color: #4caf50;
    }
    .match-alert-content {
        flex: 1;
    }
    .match-alert-title {
        font-weight: 600;
        color: #2e7d32;
        margin-bottom: 5px;
    }
    .match-alert-text {
        color: #558b2f;
        font-size: 14px;
    }
    .match-alert-actions {
        display: flex;
        gap: 10px;
    }
    .btn-match-accept {
        background: #4caf50;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-weight: 500;
    }
    .btn-match-dismiss {
        background: #757575;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-weight: 500;
    }
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@section('content')
<div class="send-document-container">
    <div class="page-header">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('signatures.index') }}">Signature Dashboard</a></li>
                    <li class="breadcrumb-item active">Send New Document</li>
                </ol>
            </nav>
            <h1>Send New Document for Signature</h1>
        </div>
        <div class="header-actions">
            <a href="{{ route('signatures.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('signatures.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if(isset($document) && $document)
            <input type="hidden" name="document_id" value="{{ $document->id }}">
            <div class="form-section">
                <h2>Existing Document</h2>
                <div class="form-group">
                    <label>Document Title</label>
                    <input type="text" class="form-control" value="{{ $document->display_title }}" readonly>
                    <small class="form-text text-muted">You are adding a signer to an existing document.</small>
                </div>
            </div>
        @else
            <div class="form-section">
                <h2>Document Details</h2>
                <div class="form-group">
                    <label for="file">Document File <span style="color: #dc3545;">*</span></label>
                    <input type="file" class="form-control" id="file" name="file" accept=".pdf" required>
                    <small class="form-text text-muted">Only PDF files are allowed (max 10MB).</small>
                </div>

                <div class="form-group mt-3">
                    <label for="title">Document Title (Optional)</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="e.g., Client Agreement" value="{{ old('title') }}">
                    <small class="form-text text-muted">Leave blank to use the file name as title.</small>
                </div>
            </div>
        @endif

        <div class="form-section">
            <h2>Signer Details</h2>
            <div class="form-group">
                <label for="signer_email">Signer Email <span style="color: #dc3545;">*</span></label>
                <input type="email" class="form-control" id="signer_email" name="signer_email" placeholder="john.doe@example.com" value="{{ old('signer_email') }}" required oninput="debounceSearch(this.value)">
                @error('signer_email')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="match-alert" id="matchAlert">
                <div class="match-alert-icon"><i class="fas fa-check-circle"></i></div>
                <div class="match-alert-content">
                    <div class="match-alert-title">Matching Client Found!</div>
                    <div class="match-alert-text" id="matchAlertText"></div>
                </div>
                <div class="match-alert-actions">
                    <button type="button" class="btn-match-accept" onclick="acceptMatch()">Use This Client</button>
                    <button type="button" class="btn-match-dismiss" onclick="dismissMatch()">Dismiss</button>
                </div>
            </div>

            <div class="form-group mt-3">
                <label for="signer_name">Signer Name <span style="color: #dc3545;">*</span></label>
                <input type="text" class="form-control" id="signer_name" name="signer_name" placeholder="John Doe" value="{{ old('signer_name') }}" required>
                @error('signer_name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <input type="hidden" id="association_id" name="association_id" value="{{ old('association_id') }}">
        </div>

        <div class="form-section">
            <h2>Email Settings (Optional)</h2>
            <div class="form-group">
                <label for="email_subject">Email Subject</label>
                <input type="text" class="form-control" id="email_subject" name="email_subject" placeholder="Document Signature Request" value="{{ old('email_subject') }}">
                <small class="form-text text-muted">Leave blank for default subject.</small>
            </div>

            <div class="form-group mt-3">
                <label for="email_message">Custom Message</label>
                <textarea class="form-control" id="email_message" name="email_message" rows="3" placeholder="Add a personal message to the signer...">{{ old('email_message') }}</textarea>
                <small class="form-text text-muted">This message will be included in the email.</small>
            </div>
        </div>

        <div class="form-section">
            <h2>Workflow Settings (Optional)</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="due_at">Due Date</label>
                        <input type="datetime-local" class="form-control" id="due_at" name="due_at" value="{{ old('due_at') }}">
                        <small class="form-text text-muted">The date by which the document should be signed.</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="priority">Priority</label>
                        <select class="form-control" id="priority" name="priority">
                            <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-right mt-4">
            <button type="submit" class="btn-primary-custom">
                <i class="fas fa-paper-plane"></i> Send for Signature
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const clients = @json($clients ?? []);
    let currentMatch = null;
    let searchTimeout;

    function debounceSearch(email) {
        clearTimeout(searchTimeout);
        if (email.length > 3 && email.includes('@')) {
            searchTimeout = setTimeout(() => {
                searchClientMatch(email);
            }, 500);
        } else {
            document.getElementById('matchAlert').classList.remove('show');
            currentMatch = null;
            document.getElementById('association_id').value = '';
            document.getElementById('signer_name').readOnly = false;
        }
    }

    function searchClientMatch(email) {
        const clientMatch = clients.find(client => 
            client.email && client.email.toLowerCase() === email.toLowerCase()
        );

        if (clientMatch) {
            currentMatch = clientMatch;
            document.getElementById('matchAlertText').innerHTML = `Client: <strong>${clientMatch.first_name} ${clientMatch.last_name}</strong> (${clientMatch.email})`;
            document.getElementById('matchAlert').classList.add('show');
        } else {
            document.getElementById('matchAlert').classList.remove('show');
            currentMatch = null;
            document.getElementById('association_id').value = '';
            document.getElementById('signer_name').readOnly = false;
        }
    }

    function acceptMatch() {
        if (currentMatch) {
            document.getElementById('signer_name').value = `${currentMatch.first_name} ${currentMatch.last_name}`;
            document.getElementById('association_id').value = currentMatch.id;
            document.getElementById('signer_name').readOnly = true;
            document.getElementById('matchAlert').classList.remove('show');
        }
    }

    function dismissMatch() {
        document.getElementById('matchAlert').classList.remove('show');
        currentMatch = null;
        document.getElementById('association_id').value = '';
        document.getElementById('signer_name').readOnly = false;
    }
</script>
@endpush
