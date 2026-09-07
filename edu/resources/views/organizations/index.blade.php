@extends('layouts.app')

@section('title', 'Organizations')

@section('content')
<div class="ui-page-shell">
    {{-- Page Heading --}}
    <div class="ui-page-heading">
        <div class="ui-page-heading__copy">
            <h1 style="display:flex;align-items:center;gap:10px;">
                <i class="fas fa-sitemap" style="color:var(--app-primary);font-size:24px;"></i> Organizations
            </h1>
            <p>Manage all organizations in the system</p>
        </div>
        <div class="ui-page-heading__actions">
            <span class="ui-badge ui-badge--info">
                <i class="fas fa-building"></i> {{ $totalOrganizations ?? 0 }} Organizations
            </span>
            <button type="button" class="ui-button ui-button--primary" onclick="openModal('registerModal')" style="min-height:36px;padding:0 16px;display:inline-flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                <i class="fas fa-plus"></i> Register
            </button>
            <button type="button" class="ui-button ui-button--outline" onclick="openModal('csvModal')" style="min-height:36px;padding:0 16px;display:inline-flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                <i class="fas fa-file-import"></i> Import CSV
            </button>
            <a href="{{ route('orgs.download-template') }}" class="ui-button ui-button--compact" style="min-height:36px;padding:0 16px;display:inline-flex;align-items:center;gap:6px;font-size:13px;">
                <i class="fas fa-download"></i> Download Template
            </a>
        </div>
    </div>

    {{-- Statistics --}}
    <div class="row g-3 mb-4">
        <div class="col-md-12">
            <div class="ui-card ui-card--stat-admin text-center">
                <div class="ui-card__label">Total Organizations</div>
                <div class="ui-card__value">{{ $totalOrganizations ?? 0 }}</div>
                <div style="font-size:11px;color:var(--app-text-subtle);margin-top:4px;">
                    <i class="fas fa-building"></i> All Organizations
                </div>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:20px;align-items:center;justify-content:space-between;">
        <form method="GET" action="{{ route('orgs.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;flex:1;">
            <div style="position:relative;flex:1;min-width:200px;">
                <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--app-text-subtle);font-size:14px;"></i>
                <input type="text" name="search" class="ui-input" placeholder="Search by name, ID, address..." value="{{ request('search') }}" style="padding-left:36px;height:40px;">
            </div>
            <button type="submit" class="ui-button ui-button--primary" style="height:40px;padding:0 20px;">
                <i class="fas fa-search"></i> Search
            </button>
            @if(request('search'))
                <a href="{{ route('orgs.index') }}" class="ui-button ui-button--compact" style="height:40px;padding:0 16px;">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Organizations Table --}}
    <div class="ui-card ui-card--section">
        <div class="ui-card__header">
            <h3 style="margin:0;font-size:14px;font-weight:800;color:var(--app-text);display:flex;align-items:center;gap:8px;">
                <i class="fas fa-list" style="color:var(--app-primary);"></i> Organization List
            </h3>
            <span class="ui-badge ui-badge--info">
                <i class="fas fa-building"></i> {{ $organizations->total() ?? 0 }} Organizations
            </span>
        </div>
        <div class="ui-card__body" style="padding:0;">
            <div class="ui-table-wrap">
                <table class="ui-table ui-table--dashboard">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Phone</th>
                            <th>Register Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($organizations as $org)
                        <tr>
                            <td>
                                <span class="ui-badge ui-badge--info" style="font-size:11px;">
                                    {{ $org->OrganizationID }}
                                </span>
                            </td>
                            <td style="font-weight:700;color:var(--app-text);">
                                <i class="fas fa-building" style="color:var(--app-primary);font-size:12px;"></i>
                                {{ $org->OrganizationName }}
                            </td>
                            <td style="font-size:13px;color:var(--app-text-secondary);">
                                {{ Str::limit($org->OrganizationAddress ?? '-', 50) }}
                            </td>
                            <td style="font-size:13px;color:var(--app-text-secondary);">
                                {{ $org->PhoneNumber ?? '-' }}
                            </td>
                            <td style="font-size:13px;color:var(--app-text-secondary);">
                                {{ $org->RegisterDate ? \Carbon\Carbon::parse($org->RegisterDate)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-center">
                                <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap;">
                                    <a href="{{ route('orgs.show', $org->OrganizationID) }}" 
                                       class="ui-button ui-button--compact" 
                                       style="font-size:11px;padding:4px 14px;min-height:30px;border-color:var(--app-primary-border);color:var(--app-primary);">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('orgs.edit', $org->OrganizationID) }}" 
                                       class="ui-button ui-button--compact" 
                                       style="font-size:11px;padding:4px 14px;min-height:30px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('orgs.destroy', $org->OrganizationID) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this organization?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ui-button" style="font-size:11px;padding:4px 14px;min-height:30px;background:var(--app-danger);color:#fff;border:1px solid var(--app-danger);border-radius:var(--app-radius-control);cursor:pointer;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="ui-state ui-state--compact">
                                    <div style="font-size:3rem;margin-bottom:8px;">🏢</div>
                                    <div class="ui-state__title">No Organizations Found</div>
                                    <div class="ui-state__copy">
                                        @if(request('search'))
                                            No organizations match your search criteria.
                                        @else
                                            No organizations have been registered yet.
                                        @endif
                                    </div>
                                    @if(request('search'))
                                        <a href="{{ route('orgs.index') }}" class="ui-button ui-button--primary" style="margin-top:16px;">
                                            <i class="fas fa-times"></i> Clear Search
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($organizations->hasPages())
            <div style="padding:12px 20px;border-top:1px solid var(--app-divider);">
                {{ $organizations->links() }}
            </div>
        @endif
        <div style="padding:8px 20px;border-top:1px solid var(--app-divider);display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px;font-size:12px;color:var(--app-text-subtle);">
            <span>Showing {{ $organizations->firstItem() ?? 0 }} to {{ $organizations->lastItem() ?? 0 }} of {{ $organizations->total() }} results</span>
            <span>Last updated: {{ now()->format('d/m/Y H:i:s') }}</span>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- REGISTER MODAL --}}
{{-- ============================================================ --}}
<div id="registerModal" class="modal" style="display:none;">
    <div class="modal-box">
        <div class="modal-header" style="display:flex;justify-content:space-between;align-items:center;">
            <div class="modal-title">Register New Organization</div>
            <button type="button" onclick="closeModal('registerModal')" style="background:rgba(255,255,255,0.2);border:none;color:#fff;width:36px;height:36px;border-radius:50%;font-size:20px;cursor:pointer;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('orgs.store') }}">
                @csrf
                
                <div class="form-label">🆔 Organization ID <span style="color:var(--app-danger);">*</span></div>
                <input name="OrganizationID" placeholder="Organization ID (e.g., ORG001)" class="form-control-custom" required>
                
                <div class="form-label">📛 Organization Name <span style="color:var(--app-danger);">*</span></div>
                <input name="OrganizationName" placeholder="Organization Name" class="form-control-custom" required>
                
                <div class="form-label">📍 Organization Address</div>
                <textarea name="OrganizationAddress" placeholder="Address" class="form-control-custom" rows="2"></textarea>
                
                <div class="form-label">📅 Register Date</div>
                <input type="date" name="RegisterDate" class="form-control-custom">
                
                <div class="form-label">📞 Phone Number</div>
                <input name="PhoneNumber" placeholder="Phone Number" class="form-control-custom">

                <div class="modal-actions">
                    <button type="submit" class="btn-custom">💾 Save Organization</button>
                    <button type="button" class="btn-light-custom" onclick="closeModal('registerModal')">❌ Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- CSV MODAL --}}
{{-- ============================================================ --}}
<div id="csvModal" class="modal" style="display:none;">
    <div class="modal-box">
        <div class="modal-header" style="display:flex;justify-content:space-between;align-items:center;">
            <div class="modal-title">Import Organizations CSV</div>
            <button type="button" onclick="closeModal('csvModal')" style="background:rgba(255,255,255,0.2);border:none;color:#fff;width:36px;height:36px;border-radius:50%;font-size:20px;cursor:pointer;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('orgs.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-label">📄 Select CSV File</div>
                <input type="file" name="csvfile" class="form-control-custom" accept=".csv,.txt" required>
                <div style="font-size:0.75rem; color:var(--gray-500); margin-top:-0.5rem; margin-bottom:1rem;">
                    Required columns: OrganizationID, OrganizationName, OrganizationAddress, RegisterDate, PhoneNumber
                </div>
                <div style="font-size:0.75rem; color:var(--gray-500); margin-top:0.25rem;">
                    <a href="{{ route('orgs.download-template') }}" style="color:var(--app-primary);text-decoration:none;font-weight:600;">
                        📥 Download Template
                    </a>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn-custom">📤 Upload & Import</button>
                    <button type="button" class="btn-light-custom" onclick="closeModal('csvModal')">❌ Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        justify-content: center;
        align-items: center;
        z-index: 99999;
        padding: 20px;
    }

    .modal.show {
        display: flex !important;
    }

    .modal-box {
        background: white;
        width: 650px;
        max-width: 95%;
        max-height: 90vh;
        overflow-y: auto;
        border-radius: 24px;
        animation: modalSlideIn 0.3s ease;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(-10px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .modal-header {
        background: linear-gradient(135deg, var(--app-primary), var(--app-primary-dark));
        padding: 1.25rem 1.5rem;
        border-radius: 24px 24px 0 0;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modal-title::before {
        content: "📝";
        font-size: 1.25rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .form-control-custom {
        width: 100%;
        border: 1px solid var(--app-border);
        border-radius: var(--app-radius-control);
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        outline: none;
        font-family: inherit;
        box-sizing: border-box;
        background: var(--app-surface);
        color: var(--app-text);
    }

    .form-control-custom:focus {
        border-color: var(--app-primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    textarea.form-control-custom {
        resize: vertical;
        min-height: 60px;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--app-text-secondary);
        margin-bottom: 0.5rem;
        margin-top: 0.5rem;
        display: block;
    }

    .form-label:first-of-type {
        margin-top: 0;
    }

    .modal-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid var(--app-divider);
        flex-wrap: wrap;
    }

    .btn-custom {
        background: linear-gradient(135deg, var(--app-primary), var(--app-primary-dark));
        color: white;
        border: none;
        padding: 0.625rem 1.5rem;
        border-radius: var(--app-radius-control);
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-custom:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .btn-light-custom {
        background: white;
        border: 1px solid var(--app-border);
        color: var(--app-text-secondary);
        padding: 0.625rem 1.5rem;
        border-radius: var(--app-radius-control);
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-light-custom:hover {
        background: var(--app-primary-soft);
        border-color: var(--app-primary-border);
        color: var(--app-primary-dark);
    }

    @media (max-width: 768px) {
        .modal-box {
            width: 100%;
            max-width: 100%;
        }
        .modal-body {
            padding: 1rem;
        }
    }
</style>

<script>
    function openModal(id) {
        var modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeModal(id) {
        var modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        var registerModal = document.getElementById('registerModal');
        var csvModal = document.getElementById('csvModal');
        if(registerModal && event.target == registerModal) {
            closeModal('registerModal');
        }
        if(csvModal && event.target == csvModal) {
            closeModal('csvModal');
        }
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal('registerModal');
            closeModal('csvModal');
        }
    });
</script>
@endsection