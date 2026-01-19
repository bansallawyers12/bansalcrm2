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
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .card-section-header h2 i {
        color: #667eea;
    }
    .card-section-body {
        padding: 20px;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f1f1f1;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-row .label {
        font-size: 14px;
        color: #6c757d;
        font-weight: 500;
    }
    .info-row .value {
        font-size: 14px;
        color: #2c3e50;
        font-weight: 500;
        text-align: right;
    }
    .info-row .value.muted {
        color: #9ca3af;
    }
    .badge-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
    }
    .badge-draft { background: #6c757d; color: white; }
    .badge-signature_placed, .badge-placed { background: #17a2b8; color: white; }
    .badge-sent { background: #ffc107; color: #000; }
    .badge-viewed { background: #17a2b8; color: white; }
    .badge-signed { background: #28a745; color: white; }
    .badge-pending { background: #ffc107; color: #000; }
    .badge-cancelled { background: #dc3545; color: white; }
    .badge-voided { background: #343a40; color: white; }

    /* Quick Actions Section */
    .quick-actions-section {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        padding: 20px;
    }
    .quick-actions-section h3 {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .quick-actions-section h3 i {
        color: #f59e0b;
    }
    .btn-action-primary {
        width: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        border: none;
        font-weight: 500;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
        margin-bottom: 10px;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .btn-action-primary:hover {
        color: white;
        opacity: 0.9;
        text-decoration: none;
    }
    .btn-action-success {
        width: 100%;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        border: none;
        font-weight: 500;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .btn-action-success:hover {
        color: white;
        opacity: 0.9;
        text-decoration: none;
    }
    .btn-action-success:disabled,
    .btn-action-primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    /* Sent Success Message */
    .sent-success-message {
        width: 100%;
        background: #d1fae5;
        color: #065f46;
        padding: 12px 20px;
        border-radius: 8px;
        border: 1px solid #6ee7b7;
        font-weight: 500;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .sent-success-message i {
        color: #10b981;
        font-size: 16px;
    }
    .sent-success-message.signed {
        background: #dcfce7;
        border-color: #86efac;
        color: #166534;
    }
    .sent-success-message.signed i {
        color: #22c55e;
    }

    /* Signers Section */
    .signer-card {
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        border-radius: 8px;
        padding: 15px 20px;
        margin-bottom: 15px;
    }
    .signer-card:last-child {
        margin-bottom: 0;
    }
    .signer-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }
    .signer-info h4 {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 4px 0;
    }
    .signer-info p {
        font-size: 13px;
        color: #6c757d;
        margin: 0;
    }
    .signer-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 12px;
    }
    .btn-signer-action {
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-reminder {
        background: #fef3c7;
        color: #92400e;
    }
    .btn-reminder:hover {
        background: #fde68a;
        color: #92400e;
        text-decoration: none;
    }
    .btn-copy-link {
        background: #e0e7ff;
        color: #3730a3;
    }
    .btn-copy-link:hover {
        background: #c7d2fe;
        color: #3730a3;
        text-decoration: none;
    }
    .btn-cancel-sig {
        background: #fee2e2;
        color: #dc2626;
    }
    .btn-cancel-sig:hover {
        background: #fecaca;
        color: #dc2626;
        text-decoration: none;
    }
    .reminder-info {
        font-size: 12px;
        color: #6b7280;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .reminder-info i {
        color: #9ca3af;
    }

    /* Activity Timeline */
    .activity-timeline {
        position: relative;
        padding-left: 35px;
    }
    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 12px;
        top: 5px;
        bottom: 5px;
        width: 2px;
        background: #e5e7eb;
    }
    .activity-item {
        position: relative;
        padding-bottom: 20px;
    }
    .activity-item:last-child {
        padding-bottom: 0;
    }
    .activity-item .activity-icon {
        position: absolute;
        left: -35px;
        top: 0;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }
    .activity-icon.icon-created {
        background: #6b7280;
        color: white;
    }
    .activity-icon.icon-placed {
        background: #667eea;
        color: white;
    }
    .activity-icon.icon-signer {
        background: #10b981;
        color: white;
    }
    .activity-icon.icon-sent {
        background: #3b82f6;
        color: white;
    }
    .activity-icon.icon-signed {
        background: #22c55e;
        color: white;
    }
    .activity-icon.icon-cancelled {
        background: #ef4444;
        color: white;
    }
    .activity-icon.icon-default {
        background: #667eea;
        color: white;
    }
    .activity-item .activity-time {
        font-size: 13px;
        color: #374151;
        font-weight: 500;
        margin-bottom: 3px;
    }
    .activity-item .activity-text {
        font-size: 14px;
        color: #2c3e50;
    }
    .activity-item .activity-ago {
        font-size: 12px;
        color: #9ca3af;
        margin-top: 2px;
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
    .document-preview {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 40px;
        text-align: center;
        min-height: 200px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .document-preview i {
        font-size: 48px;
        color: #667eea;
        margin-bottom: 15px;
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
    .no-signers {
        text-align: center;
        padding: 30px;
        color: #6c757d;
    }
    .no-signers i {
        font-size: 40px;
        margin-bottom: 15px;
        color: #d1d5db;
    }
</style>
@endpush

@section('content')
<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-body">
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
        </div>
        <div class="header-actions">
            @if($document->signed_doc_link)
                <a href="{{ route('public.documents.download.signed', $document->id) }}" class="btn-primary-custom">
                    <i class="fas fa-download"></i> Download Signed
                </a>
            @elseif($document->myfile)
                <a href="{{ $document->myfile }}" target="_blank" class="btn-outline-custom">
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

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
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
                    <h2><i class="fas fa-file-alt"></i> Document Information</h2>
                </div>
                <div class="card-section-body">
                    <div class="info-row">
                        <span class="label">Status</span>
                        <span class="value">
                            <span class="badge-status badge-{{ $document->status }}">
                                {{ $document->status === 'signature_placed' ? 'Placed' : ucfirst($document->status) }}
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="label">Document Type</span>
                        <span class="value {{ !$document->filetype ? 'muted' : '' }}">{{ $document->filetype ? strtoupper($document->filetype) : '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Priority</span>
                        <span class="value {{ !$document->priority ? 'muted' : '' }}">{{ ucfirst($document->priority ?? '-') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">File Name</span>
                        <span class="value">{{ $document->file_name ?? $document->display_title }}.{{ $document->filetype ?? 'pdf' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Created By</span>
                        <span class="value">{{ $document->creator->first_name ?? 'System' }} {{ $document->creator->last_name ?? '' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Created At</span>
                        <span class="value">
                            {{ $document->created_at->format('M d, Y g:i A') }}
                            <br><small class="text-muted">{{ $document->created_at->diffForHumans() }}</small>
                        </span>
                    </div>
                    @if($document->due_at)
                    <div class="info-row">
                        <span class="label">Due Date</span>
                        <span class="value {{ $document->is_overdue ? 'text-danger' : '' }}">
                            {{ $document->due_at->format('M d, Y') }}
                            @if($document->is_overdue)
                                <i class="fas fa-exclamation-triangle text-danger"></i>
                            @endif
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Signers -->
            <div class="card-section">
                <div class="card-section-header">
                    <h2><i class="fas fa-users"></i> Signers ({{ $document->signers->count() }})</h2>
                    @if(!in_array($document->status, ['signed', 'voided', 'archived']))
                        <a href="{{ route('signatures.create', ['document_id' => $document->id]) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-plus"></i> Add Signer
                        </a>
                    @endif
                </div>
                <div class="card-section-body">
                    @if($document->signers->count() > 0)
                        @foreach($document->signers as $signer)
                        <div class="signer-card">
                            <div class="signer-header">
                                <div class="signer-info">
                                    <h4>{{ $signer->name }}</h4>
                                    <p>{{ $signer->email }}</p>
                                </div>
                                <span class="badge-status badge-{{ $signer->status }}">
                                    <i class="fas fa-{{ $signer->status === 'pending' ? 'clock' : ($signer->status === 'signed' ? 'check' : 'times') }}"></i>
                                    {{ ucfirst($signer->status) }}
                                </span>
                            </div>
                            
                            @if($signer->status === 'pending' || $signer->status === 'viewed')
                                <div class="signer-actions">
                                    {{-- Send Reminder Button --}}
                                    @php
                                        $maxReminders = 3;
                                        $reminderCount = $signer->reminder_count ?? 0;
                                        $canSendReminder = $reminderCount < $maxReminders && in_array($document->status, ['sent', 'viewed']);
                                    @endphp
                                    <form action="{{ route('signatures.reminder', $document->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="signer_id" value="{{ $signer->id }}">
                                        <button type="submit" class="btn-signer-action btn-reminder" {{ !$canSendReminder ? 'disabled' : '' }}>
                                            <i class="fas fa-bell"></i> Send Reminder ({{ $reminderCount }}/{{ $maxReminders }})
                                        </button>
                                    </form>
                                    
                                    {{-- Copy Link Button --}}
                                    <button type="button" class="btn-signer-action btn-copy-link" onclick="copySigningLink('{{ route('public.documents.sign', ['id' => $document->id, 'token' => $signer->token]) }}')">
                                        <i class="fas fa-link"></i> Copy Link
                                    </button>
                                    
                                    {{-- Cancel Signature Button --}}
                                    <form action="{{ route('signatures.cancel', $document->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to cancel signature for {{ $signer->name }}? They will no longer be able to sign this document.')">
                                        @csrf
                                        <input type="hidden" name="signer_id" value="{{ $signer->id }}">
                                        <button type="submit" class="btn-signer-action btn-cancel-sig">
                                            <i class="fas fa-times"></i> Cancel Signature
                                        </button>
                                    </form>
                                </div>
                                
                                @if($signer->last_reminder_at)
                                <div class="reminder-info">
                                    <i class="fas fa-info-circle"></i>
                                    @if($reminderCount === 0)
                                        No reminders sent yet
                                    @else
                                        Last reminder: {{ \Carbon\Carbon::parse($signer->last_reminder_at)->diffForHumans() }}
                                    @endif
                                </div>
                                @else
                                <div class="reminder-info">
                                    <i class="fas fa-info-circle"></i>
                                    No reminders sent yet
                                </div>
                                @endif
                            @elseif($signer->status === 'signed')
                                <div class="reminder-info">
                                    <i class="fas fa-check-circle text-success"></i>
                                    Signed on {{ $signer->signed_at ? $signer->signed_at->format('M d, Y g:i A') : 'N/A' }}
                                </div>
                            @elseif($signer->status === 'cancelled')
                                <div class="reminder-info">
                                    <i class="fas fa-times-circle text-danger"></i>
                                    Cancelled {{ $signer->cancelled_at ? \Carbon\Carbon::parse($signer->cancelled_at)->diffForHumans() : '' }}
                                </div>
                            @endif
                        </div>
                        @endforeach
                    @else
                        <div class="no-signers">
                            <i class="fas fa-user-plus"></i>
                            <p>No signers added yet.</p>
                            <a href="{{ route('signatures.create', ['document_id' => $document->id]) }}" class="btn-primary-custom">
                                <i class="fas fa-plus"></i> Add First Signer
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="card-section">
                <div class="card-section-header">
                    <h2><i class="fas fa-history"></i> Activity Timeline</h2>
                </div>
                <div class="card-section-body">
                    @if($document->notes->count() > 0)
                        <div class="activity-timeline">
                            @foreach($document->notes->sortByDesc('created_at') as $note)
                            @php
                                $iconClass = 'icon-default';
                                $icon = 'circle';
                                if (str_contains($note->action_type ?? '', 'created') || str_contains($note->action_text ?? '', 'created')) {
                                    $iconClass = 'icon-created';
                                    $icon = 'file';
                                } elseif (str_contains($note->action_type ?? '', 'placed') || str_contains($note->action_text ?? '', 'placed')) {
                                    $iconClass = 'icon-placed';
                                    $icon = 'edit';
                                } elseif (str_contains($note->action_type ?? '', 'signer') || str_contains($note->action_text ?? '', 'Signer')) {
                                    $iconClass = 'icon-signer';
                                    $icon = 'user-plus';
                                } elseif (str_contains($note->action_type ?? '', 'sent') || str_contains($note->action_text ?? '', 'sent')) {
                                    $iconClass = 'icon-sent';
                                    $icon = 'paper-plane';
                                } elseif (str_contains($note->action_type ?? '', 'signed') || str_contains($note->action_text ?? '', 'signed')) {
                                    $iconClass = 'icon-signed';
                                    $icon = 'check';
                                } elseif (str_contains($note->action_type ?? '', 'cancel') || str_contains($note->action_text ?? '', 'cancel')) {
                                    $iconClass = 'icon-cancelled';
                                    $icon = 'times';
                                }
                            @endphp
                            <div class="activity-item">
                                <div class="activity-icon {{ $iconClass }}">
                                    <i class="fas fa-{{ $icon }}"></i>
                                </div>
                                <div class="activity-time">{{ $note->created_at->format('M d, Y g:i A') }}</div>
                                <div class="activity-text">{{ $note->action_text ?? $note->note }}</div>
                                <div class="activity-ago">{{ $note->created_at->diffForHumans() }}</div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No activity recorded yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="quick-actions-section">
                <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                
                {{-- Edit Signature Placement Button --}}
                @if(!in_array($document->status, ['signed', 'voided', 'archived']))
                    <a href="{{ route('signatures.edit', $document->id) }}" class="btn-action-primary">
                        <i class="fas fa-edit"></i> Edit Signature Placement
                    </a>
                @endif
                
                {{-- Send for Signature Button or Status Message --}}
                @php
                    $pendingSigners = $document->signers->where('status', 'pending');
                    $hasSignatureFields = $document->signatureFields && $document->signatureFields->count() > 0;
                    $canSendForSignature = $pendingSigners->count() > 0 && !in_array($document->status, ['sent', 'viewed', 'signed', 'voided', 'archived']);
                @endphp
                
                @if(in_array($document->status, ['sent', 'viewed']))
                    {{-- Document already sent - show success message --}}
                    <div class="sent-success-message">
                        <i class="fas fa-check-circle"></i> Document sent for signature
                    </div>
                @elseif($canSendForSignature)
                    <form action="{{ route('signatures.send', $document->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-action-success" onclick="return confirm('This will send signing link emails to all pending signers ({{ $pendingSigners->count() }}). Continue?')">
                            <i class="fas fa-paper-plane"></i> Send for Signature
                        </button>
                    </form>
                @elseif($document->signers->count() === 0)
                    <button type="button" class="btn-action-success" disabled title="Add a signer first">
                        <i class="fas fa-paper-plane"></i> Send for Signature
                    </button>
                    <small class="text-muted d-block text-center mt-2">Add a signer to enable sending</small>
                @elseif($document->status === 'signed')
                    <div class="sent-success-message signed">
                        <i class="fas fa-check-circle"></i> Document signed successfully
                    </div>
                @endif
            </div>

            <!-- Association -->
            <div class="card-section">
                <div class="card-section-header">
                    <h2><i class="fas fa-link"></i> Association</h2>
                </div>
                <div class="card-section-body">
                    @if($document->documentable)
                        <div class="info-row">
                            <span class="label">Client</span>
                            <span class="value">
                                <a href="{{ route('clients.detail', $document->documentable_id) }}">
                                    {{ $document->documentable->first_name }} {{ $document->documentable->last_name }}
                                </a>
                            </span>
                        </div>
                        <form action="{{ route('signatures.detach', $document->id) }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Are you sure you want to detach this document from the client?')">
                                <i class="fas fa-unlink"></i> Detach
                            </button>
                        </form>
                    @else
                        <p class="text-muted mb-3">Not associated with any client.</p>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#associateModal">
                            <i class="fas fa-link"></i> Associate with Client
                        </button>
                    @endif
                </div>
            </div>

            <!-- Hash Verification (for signed documents) -->
            @if($document->status == 'signed' && $document->signed_hash)
            <div class="card-section">
                <div class="card-section-header">
                    <h2><i class="fas fa-shield-alt"></i> Signature Verification</h2>
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
                        <label for="entity_id">Select Client</label>
                        <select class="form-control" id="entity_id" name="entity_id" required>
                            <option value="">-- Select Client --</option>
                            @foreach($clients ?? [] as $client)
                                <option value="{{ $client->id }}">{{ $client->first_name }} {{ $client->last_name }} ({{ $client->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="note">Note (optional)</label>
                        <textarea class="form-control" id="note" name="note" rows="2" placeholder="Add a note about this association..."></textarea>
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
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
function copySigningLink(url) {
    navigator.clipboard.writeText(url).then(function() {
        // Show a nice toast instead of alert
        showToast('Signing link copied to clipboard!', 'success');
    }, function(err) {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = url;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showToast('Signing link copied to clipboard!', 'success');
    });
}

function showToast(message, type) {
    // Create toast element
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : '#ef4444'};
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        animation: slideIn 0.3s ease;
    `;
    toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : 'times'}-circle"></i> ${message}`;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

$(document).ready(function() {
    if ($('#entity_id').length) {
        $('#entity_id').select2({
            dropdownParent: $('#associateModal'),
            placeholder: 'Search for a client...',
            allowClear: true
        });
    }
});
</script>
@endpush
