@extends('layouts.admin')
@section('title', 'Signature Dashboard')

@push('styles')
<style>
    .signature-dashboard {
        padding: 20px;
    }
    
    .dashboard-header {
        margin-bottom: 30px;
    }
    
    .dashboard-header h1 {
        font-size: 24px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
        border-radius: 10px;
        color: white;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .stat-card.pending {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .stat-card.signed {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .stat-card h3 {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 10px;
        opacity: 0.9;
    }
    
    .stat-card .number {
        font-size: 32px;
        font-weight: 700;
    }
    
    .tabs-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .nav-tabs {
        border-bottom: 2px solid #e9ecef;
        padding: 0 20px;
        background: #f8f9fa;
    }
    
    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        padding: 15px 20px;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        color: #667eea;
        background: transparent;
        border-bottom: 2px solid #667eea;
    }
    
    .documents-table {
        width: 100%;
        padding: 20px;
    }
    
    .documents-table table {
        width: 100%;
    }
    
    .documents-table th {
        text-align: left;
        padding: 12px;
        color: #6c757d;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        border-bottom: 2px solid #e9ecef;
    }
    
    .documents-table td {
        padding: 15px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #f8f9fa;
    }
    
    .document-link {
        color: #2c3e50;
        text-decoration: none;
        font-weight: 600;
    }
    
    .document-link:hover {
        color: #667eea;
    }
    
    .badge-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .badge-draft { background: #6c757d; color: white; }
    .badge-sent { background: #ffc107; color: #000; }
    .badge-signed { background: #28a745; color: white; }
    
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
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 48px;
        margin-bottom: 20px;
        opacity: 0.5;
    }
</style>
@endpush

@section('content')
<div class="signature-dashboard">
    <!-- Header -->
    <div class="dashboard-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Signature Dashboard</h1>
                <p style="color: #6c757d;">Manage and track document signatures</p>
            </div>
            <div>
                <a href="{{ route('signatures.create') }}" class="btn-primary-custom">
                    <i class="fas fa-plus"></i> Send New Document
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="stat-card">
            <h3>My Documents</h3>
            <div class="number">{{ $counts['sent_by_me'] ?? 0 }}</div>
        </div>
        <div class="stat-card pending">
            <h3>Pending Signature</h3>
            <div class="number">{{ $counts['pending'] ?? 0 }}</div>
        </div>
        <div class="stat-card signed">
            <h3>Signed</h3>
            <div class="number">{{ $counts['signed'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Success/Error Messages -->
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

    <!-- Tabs -->
    <div class="tabs-container">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ !request('tab') || request('tab') == 'sent_by_me' ? 'active' : '' }}" 
                   href="{{ route('signatures.index', ['tab' => 'sent_by_me']) }}">
                    Sent by Me
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('tab') == 'pending' ? 'active' : '' }}" 
                   href="{{ route('signatures.index', ['tab' => 'pending']) }}">
                    Pending
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('tab') == 'signed' ? 'active' : '' }}" 
                   href="{{ route('signatures.index', ['tab' => 'signed']) }}">
                    Signed
                </a>
            </li>
        </ul>

        <!-- Filters -->
        <div style="padding: 20px; background: #f8f9fa; border-bottom: 1px solid #e9ecef;">
            <form method="GET" action="{{ route('signatures.index') }}" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
                <input type="hidden" name="tab" value="{{ request('tab') }}">
                
                <select name="status" class="form-control" style="width: auto;" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="signed" {{ request('status') == 'signed' ? 'selected' : '' }}>Signed</option>
                </select>
                
                <input type="text" name="search" class="form-control" placeholder="Search documents..." 
                       value="{{ request('search') }}" style="flex: 1; min-width: 200px;">
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
                
                @if(request()->anyFilled(['status', 'search']))
                <a href="{{ route('signatures.index', ['tab' => request('tab')]) }}" class="btn btn-secondary">
                    Clear Filters
                </a>
                @endif
            </form>
        </div>

        <!-- Documents Table -->
        <div class="documents-table">
            @if($documents->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Document</th>
                        <th>Signer</th>
                        <th>Status</th>
                        <th>Association</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $doc)
                    <tr>
                        <td>
                            <a href="{{ route('signatures.show', $doc->id) }}" class="document-link">
                                {{ $doc->display_title }}
                            </a>
                        </td>
                        <td>
                            {{ $doc->primary_signer_email ?? 'N/A' }}
                            @if($doc->signer_count > 1)
                            <br><small>(+{{ $doc->signer_count - 1 }} more)</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge-status badge-{{ $doc->status }}">
                                {{ ucfirst($doc->status) }}
                            </span>
                        </td>
                        <td>
                            @if($doc->documentable)
                                <a href="{{ route('clients.detail', $doc->documentable_id) }}">
                                    {{ $doc->documentable->first_name }} {{ $doc->documentable->last_name }}
                                </a>
                            @else
                                <span style="color: #6c757d;">Ad-hoc</span>
                            @endif
                        </td>
                        <td>
                            {{ $doc->created_at->format('M d, Y') }}<br>
                            <small style="color: #6c757d;">{{ $doc->created_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Pagination -->
            <div style="margin-top: 20px;">
                {{ $documents->appends(request()->query())->links() }}
            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>No documents found</h3>
                <p>Start by sending a new document for signature</p>
                <a href="{{ route('signatures.create') }}" class="btn-primary-custom" style="margin-top: 20px;">
                    <i class="fas fa-plus"></i> Send New Document
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
