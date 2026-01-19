@extends('layouts.admin')
@section('title', 'Document Details - ' . ($document->display_title ?? 'Signature'))

@push('styles')
<style>
    .document-detail-container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 20px;
    }
    .page-header {
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 20px;
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
    .header-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .card-section {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        overflow: hidden;
    }
    .card-section-header {
        background: #f8f9fa;
        padding: 15px 20px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .card-section-header h2 {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }
    .card-section-body {
        padding: 20px;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    .info-item {
        display: flex;
        flex-direction: column;
    }
    .info-item label {
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        margin-bottom: 5px;
    }
    .info-item span {
        font-size: 15px;
        color: #2c3e50;
    }
    .badge-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
    }
    .badge-draft { background: #6c757d; color: white; }
    .badge-sent { background: #ffc107; color: #000; }
    .badge-viewed { background: #17a2b8; color: white; }
    .badge-signed { background: #28a745; color: white; }
    .badge-cancelled { background: #dc3545; color: white; }
    .badge-voided { background: #343a40; color: white; }
    .signers-table {
        width: 100%;
    }
    .signers-table th {
        text-align: left;
        padding: 12px;
        color: #6c757d;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        border-bottom: 2px solid #e9ecef;
    }
    .signers-table td {
        padding: 15px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #f8f9fa;
    }
    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    .btn-primary-custom:hover {
        color: white;
        opacity: 0.9;
    }
    .btn-outline-custom {
        background: transparent;
        color: #667eea;
        padding: 8px 16px;
        border-radius: 6px;
        border: 1px solid #667eea;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    .btn-outline-custom:hover {
        background: #667eea;
        color: white;
    }
    .activity-timeline {
        position: relative;
        padding-left: 30px;
    }
    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }
    .activity-item {
        position: relative;
        padding-bottom: 20px;
    }
    .activity-item:last-child {
        padding-bottom: 0;
    }
    .activity-item::before {
        content: '';
        position: absolute;
        left: -24px;
        top: 5px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #667eea;
        border: 2px solid white;
        box-shadow: 0 0 0 2px #667eea;
    }
    .activity-item .activity-time {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 5px;
    }
    .activity-item .activity-text {
        font-size: 14px;
        color: #2c3e50;
    }
    .document-preview {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 40px;
        text-align: center;
        min-height: 300px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .document-preview i {
        font-size: 64px;
        color: #667eea;
        margin-bottom: 20px;
    }
    .copy-link-container {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .copy-link-input {
        flex: 1;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background: #f8f9fa;
        font-size: 13px;
    }
    .hash-display {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        font-family: monospace;
        font-size: 12px;
        word-break: break-all;
    }
    .hash-valid {
        border-left: 4px solid #28a745;
    }
    .hash-invalid {
        border-left: 4px solid #dc3545;
    }
</style>
@endpush

@section('content')
<div class="document-detail-container">
    <!-- Header -->
    <div class="page-header">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('signatures.index') }}">Signature Dashboard</a></li>
                    <li class="breadcrumb-item active">{{ $document->display_title }}</li>
                </ol>
            </nav>
            <h1>{{ $document->display_title }}</h1>
            <span class="badge-status badge-{{ $document->status }}">{{ ucfirst($document->status) }}</span>
        </div>
        <div class="header-actions">
            @if($document->status == 'draft')
                <form action="{{ route('signatures.send', $document->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-primary-custom">
                        <i class="fas fa-paper-plane"></i> Send for Signature
                    </button>
                </form>
            @endif
            
            @if(in_array($document->status, ['sent', 'viewed']))
                <form action="{{ route('signatures.reminder', $document->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-outline-custom">
                        <i class="fas fa-bell"></i> Send Reminder
                    </button>
                </form>
                
                <form action="{{ route('signatures.cancel', $document->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to cancel this signature request?')">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </form>
            @endif
            
            @if($document->signed_doc_link)
                <a href="{{ route('public.documents.download.signed', $document->id) }}" class="btn-primary-custom">
                    <i class="fas fa-download"></i> Download Signed
                </a>
            @elseif($document->signature_doc_link)
                <a href="{{ Storage::url($document->signature_doc_link) }}" target="_blank" class="btn-outline-custom">
                    <i class="fas fa-eye"></i> View Document
                </a>
            @endif
            
            <a href="{{ route('signatures.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Document Info -->
            <div class="card-section">
                <div class="card-section-header">
                    <h2>Document Information</h2>
                </div>
                <div class="card-section-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Title</label>
                            <span>{{ $document->display_title }}</span>
                        </div>
                        <div class="info-item">
                            <label>Status</label>
                            <span class="badge-status badge-{{ $document->status }}">{{ ucfirst($document->status) }}</span>
                        </div>
                        <div class="info-item">
                            <label>Created</label>
                            <span>{{ $document->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="info-item">
                            <label>Created By</label>
                            <span>{{ $document->creator->first_name ?? 'System' }} {{ $document->creator->last_name ?? '' }}</span>
                        </div>
                        @if($document->due_at)
                        <div class="info-item">
                            <label>Due Date</label>
                            <span class="{{ $document->is_overdue ? 'text-danger' : '' }}">
                                {{ $document->due_at->format('M d, Y') }}
                                @if($document->is_overdue)
                                    <i class="fas fa-exclamation-triangle text-danger"></i>
                                @endif
                            </span>
                        </div>
                        @endif
                        <div class="info-item">
                            <label>Priority</label>
                            <span>{{ ucfirst($document->priority ?? 'Normal') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signers -->
            <div class="card-section">
                <div class="card-section-header">
                    <h2>Signers</h2>
                </div>
                <div class="card-section-body">
                    @if($document->signers->count() > 0)
                        <table class="signers-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Signed At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($document->signers as $signer)
                                <tr>
                                    <td>{{ $signer->name }}</td>
                                    <td>{{ $signer->email }}</td>
                                    <td>
                                        <span class="badge-status badge-{{ $signer->status }}">
                                            {{ ucfirst($signer->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($signer->signed_at)
                                            {{ $signer->signed_at->format('M d, Y h:i A') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($signer->status == 'pending' || $signer->status == 'viewed')
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="copySigningLink('{{ route('public.documents.sign', ['id' => $document->id, 'token' => $signer->token]) }}')">
                                                <i class="fas fa-link"></i> Copy Link
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted mb-0">No signers added yet.</p>
                    @endif
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="card-section">
                <div class="card-section-header">
                    <h2>Activity</h2>
                </div>
                <div class="card-section-body">
                    @if($document->notes->count() > 0)
                        <div class="activity-timeline">
                            @foreach($document->notes->sortByDesc('created_at') as $note)
                            <div class="activity-item">
                                <div class="activity-time">{{ $note->created_at->format('M d, Y h:i A') }}</div>
                                <div class="activity-text">
                                    {{ $note->action_text }}
                                    @if($note->note)
                                        <br><small class="text-muted">{{ $note->note }}</small>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No activity recorded yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Association -->
            <div class="card-section">
                <div class="card-section-header">
                    <h2>Association</h2>
                </div>
                <div class="card-section-body">
                    @if($document->documentable)
                        <div class="info-item mb-3">
                            <label>Client</label>
                            <span>
                                <a href="{{ route('clients.detail', $document->documentable_id) }}">
                                    {{ $document->documentable->first_name }} {{ $document->documentable->last_name }}
                                </a>
                            </span>
                        </div>
                        <form action="{{ route('signatures.detach', $document->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-unlink"></i> Detach
                            </button>
                        </form>
                    @else
                        <p class="text-muted">Not associated with any client.</p>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#associateModal">
                            <i class="fas fa-link"></i> Associate with Client
                        </button>
                    @endif
                </div>
            </div>

            <!-- Document Preview -->
            <div class="card-section">
                <div class="card-section-header">
                    <h2>Preview</h2>
                </div>
                <div class="card-section-body">
                    <div class="document-preview">
                        <i class="fas fa-file-pdf"></i>
                        <p>{{ $document->display_title }}</p>
                        @if($document->signature_doc_link)
                            <a href="{{ Storage::url($document->signature_doc_link) }}" target="_blank" class="btn-outline-custom">
                                <i class="fas fa-external-link-alt"></i> Open PDF
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Hash Verification (for signed documents) -->
            @if($document->status == 'signed' && $document->signed_hash)
            <div class="card-section">
                <div class="card-section-header">
                    <h2>Signature Verification</h2>
                </div>
                <div class="card-section-body">
                    <div class="hash-display {{ $document->verifySignedHash() ? 'hash-valid' : 'hash-invalid' }}">
                        <small>
                            @if($document->verifySignedHash())
                                <i class="fas fa-check-circle text-success"></i> Document integrity verified
                            @else
                                <i class="fas fa-exclamation-triangle text-danger"></i> Document may have been modified
                            @endif
                        </small>
                        <br><br>
                        <strong>Hash:</strong><br>
                        {{ $document->signed_hash }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Associate Modal -->
<div class="modal fade" id="associateModal" tabindex="-1" role="dialog" aria-labelledby="associateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="associateModalLabel">Associate with Client</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('signatures.associate', $document->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="client_id">Select Client</label>
                        <select class="form-control" id="client_id" name="client_id" required>
                            <option value="">-- Select Client --</option>
                            @foreach($clients ?? [] as $client)
                                <option value="{{ $client->id }}">{{ $client->first_name }} {{ $client->last_name }} ({{ $client->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-primary-custom">Associate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copySigningLink(url) {
    navigator.clipboard.writeText(url).then(function() {
        alert('Signing link copied to clipboard!');
    }, function(err) {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = url;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Signing link copied to clipboard!');
    });
}

$(document).ready(function() {
    $('#client_id').select2({
        dropdownParent: $('#associateModal'),
        placeholder: 'Search for a client...',
        allowClear: true
    });
});
</script>
@endpush
