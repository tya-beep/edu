@extends('layouts.app')

@section('title', 'Pension Management Dashboard')

@section('styles')
<style>
    :root {
        --primary-green: #059669;
        --primary-dark: #047857;
        --primary-light: #10b981;
        --primary-soft: #d1fae5;
        --primary-bg: #ecfdf5;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --warning: #f59e0b;
        --danger: #ef4444;
        --success: #10b981;
    }

    /* Header Section */
    .page-header {
        background: linear-gradient(135deg, #1e3d2c 0%, #2e5a42 100%);
        border-radius: 24px;
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }

    .page-header::before {
        content: "📅";
        position: absolute;
        right: -20px;
        top: -20px;
        font-size: 100px;
        opacity: 0.08;
        pointer-events: none;
    }

    .page-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .page-header p {
        color: rgba(255,255,255,0.8);
        margin: 0;
        font-size: 0.9rem;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--gray-100);
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .stat-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-green), var(--primary-light));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 35px -12px rgba(0,0,0,0.15);
    }

    .stat-card:hover::after {
        transform: scaleX(1);
    }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-pending .stat-icon {
        background: #fef3c7;
        color: #f59e0b;
    }

    .stat-retired .stat-icon {
        background: #fee2e2;
        color: #ef4444;
    }

    .stat-warning .stat-icon {
        background: #fce4ec;
        color: #e53935;
    }

    .stat-urgent .stat-icon {
        background: #ffebee;
        color: #c62828;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--gray-600);
    }

    .stat-sub {
        font-size: 0.7rem;
        color: var(--gray-500);
        margin-top: 0.5rem;
    }

    /* Section Cards */
    .section-card {
        background: white;
        border-radius: 20px;
        margin-bottom: 1.5rem;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .section-card:hover {
        box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }

    .section-header {
        background: linear-gradient(135deg, var(--gray-50), white);
        padding: 1rem 1.5rem;
        border-bottom: 2px solid var(--gray-100);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .section-header i {
        font-size: 1.25rem;
        color: var(--primary-green);
    }

    .section-header h3 {
        flex: 1;
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: var(--gray-800);
    }

    .badge-count {
        background: var(--primary-soft);
        color: var(--primary-dark);
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    /* Table Styles */
    .modern-table {
        width: 100%;
        font-size: 0.875rem;
    }

    .modern-table thead th {
        background: var(--gray-50);
        padding: 1rem;
        font-weight: 600;
        color: var(--gray-700);
        border-bottom: 1px solid var(--gray-200);
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }

    .modern-table tbody td {
        padding: 1rem;
        vertical-align: middle;
        color: var(--gray-700);
        border-bottom: 1px solid var(--gray-100);
    }

    .modern-table tbody tr {
        transition: all 0.2s ease;
    }

    .modern-table tbody tr:hover {
        background: var(--primary-bg);
    }

    .modern-table tbody tr.urgent-row {
        background: #fff5f5;
        border-left: 4px solid #dc2626;
    }

    .modern-table tbody tr.warning-row {
        background: #fffbeb;
        border-left: 4px solid #f59e0b;
    }

    /* Days Badge */
    .days-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        white-space: nowrap;
    }

    .days-urgent {
        background: #fee2e2;
        color: #dc2626;
    }

    .days-warning {
        background: #fef3c7;
        color: #d97706;
    }

    .days-normal {
        background: var(--gray-100);
        color: var(--gray-600);
    }

    .days-safe {
        background: #d1fae5;
        color: #065f46;
    }

    /* Status Badge */
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .status-active {
        background: #d1fae5;
        color: #065f46;
    }

    .status-resigned {
        background: #fee2e2;
        color: #dc2626;
    }

    /* Filter Section */
    .filter-section {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .filter-section .filter-group {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
    }

    .filter-section .filter-group .form-group {
        flex: 1;
        min-width: 180px;
    }

    .filter-section .filter-group .form-group label {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--gray-600);
        margin-bottom: 0.25rem;
        display: block;
    }

    .filter-section .filter-group .form-control {
        border-radius: 12px;
        border: 2px solid var(--gray-200);
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        width: 100%;
        height: 42px;
    }

    .filter-section .filter-group .form-control:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
        outline: none;
    }

    .filter-section .filter-group .btn-filter {
        background: var(--primary-green);
        color: white;
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        height: 42px;
    }

    .filter-section .filter-group .btn-filter:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(5, 150, 105, 0.3);
    }

    .filter-section .filter-group .btn-filter-outline {
        background: white;
        color: var(--gray-600);
        border: 2px solid var(--gray-200);
        padding: 0.5rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        height: 42px;
        text-decoration: none;
    }

    .filter-section .filter-group .btn-filter-outline:hover {
        background: var(--gray-50);
        border-color: var(--gray-300);
        transform: translateY(-2px);
        text-decoration: none;
        color: var(--gray-700);
    }

    .filter-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--gray-100);
    }

    .filter-tag {
        background: var(--gray-100);
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.7rem;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        color: var(--gray-700);
    }

    .filter-tag .remove-tag {
        cursor: pointer;
        color: var(--gray-500);
        transition: color 0.2s ease;
        background: none;
        border: none;
        font-size: 1rem;
        padding: 0 0.25rem;
    }

    .filter-tag .remove-tag:hover {
        color: #dc2626;
    }

    /* Quick Filter Buttons */
    .quick-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--gray-100);
        align-items: center;
    }

    .quick-filters-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--gray-600);
        display: flex;
        align-items: center;
        gap: 0.375rem;
        margin-right: 0.5rem;
    }

    .quick-filter-btn {
        padding: 0.375rem 1rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 2px solid var(--gray-200);
        background: white;
        color: var(--gray-600);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .quick-filter-btn:hover {
        border-color: var(--primary-green);
        color: var(--primary-green);
        transform: translateY(-1px);
    }

    .quick-filter-btn.active {
        background: var(--primary-green);
        border-color: var(--primary-green);
        color: white;
    }

    .quick-filter-btn.danger.active {
        background: #dc2626;
        border-color: #dc2626;
        color: white;
    }

    .quick-filter-btn.warning.active {
        background: #f59e0b;
        border-color: #f59e0b;
        color: white;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }

    .btn-action {
        flex: 1;
        padding: 0.875rem 1rem;
        border-radius: 14px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        text-decoration: none;
        min-width: 180px;
    }

    .btn-primary-action {
        background: linear-gradient(135deg, #059669, #047857);
        color: white;
    }

    .btn-primary-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(5,150,105,0.3);
        color: white;
        text-decoration: none;
    }

    .btn-secondary-action {
        background: white;
        border: 2px solid var(--gray-200);
        color: var(--gray-700);
    }

    .btn-secondary-action:hover {
        background: var(--gray-50);
        border-color: var(--gray-300);
        transform: translateY(-2px);
        text-decoration: none;
        color: var(--gray-700);
    }

    .btn-danger-action {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        color: white;
    }

    .btn-danger-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220,38,38,0.3);
        color: white;
        text-decoration: none;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem;
    }

    .empty-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-text {
        color: var(--gray-500);
        font-size: 0.875rem;
    }

    /* Code Styling */
    code {
        background: var(--gray-100);
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.8rem;
    }

    /* Badge Styles */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-in {
        animation: fadeInUp 0.5s ease forwards;
    }

    .delay-1 { animation-delay: 0.05s; }
    .delay-2 { animation-delay: 0.1s; }
    .delay-3 { animation-delay: 0.15s; }
    .delay-4 { animation-delay: 0.2s; }
    .delay-5 { animation-delay: 0.25s; }

    /* Progress Bar */
    .pension-progress {
        width: 80px;
        height: 6px;
        background: var(--gray-200);
        border-radius: 3px;
        overflow: hidden;
        display: inline-block;
    }

    .pension-progress .progress-bar {
        height: 100%;
        border-radius: 3px;
        transition: width 0.5s ease;
    }

    .pension-progress .progress-bar.danger {
        background: #dc2626;
    }

    .pension-progress .progress-bar.warning {
        background: #f59e0b;
    }

    .pension-progress .progress-bar.success {
        background: #10b981;
    }

    /* Pagination */
    .pagination-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--gray-100);
        gap: 1rem;
    }

    .pagination-info {
        font-size: 0.85rem;
        color: var(--gray-600);
    }

    .pagination .page-link {
        color: var(--gray-700);
        border: 1px solid var(--gray-200);
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .pagination .page-link:hover {
        background: var(--primary-bg);
        border-color: var(--primary-green);
        color: var(--primary-green);
    }

    .pagination .page-item.active .page-link {
        background: var(--primary-green);
        border-color: var(--primary-green);
        color: white;
    }

    .pagination .page-item.disabled .page-link {
        color: var(--gray-400);
        pointer-events: none;
        cursor: not-allowed;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            padding: 1.25rem;
        }

        .page-header h1 {
            font-size: 1.25rem;
        }

        .stats-grid {
            grid-template-columns: 1fr 1fr;
        }

        .section-header {
            padding: 0.75rem 1rem;
        }

        .modern-table {
            font-size: 0.75rem;
        }

        .modern-table thead th,
        .modern-table tbody td {
            padding: 0.5rem;
        }

        .action-buttons {
            flex-direction: column;
        }

        .filter-section .filter-group {
            flex-direction: column;
        }

        .filter-section .filter-group .form-group {
            min-width: 100%;
        }

        .filter-section .filter-group .btn-filter,
        .filter-section .filter-group .btn-filter-outline {
            width: 100%;
            justify-content: center;
        }

        .pagination-container {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }

        .pagination-container .pagination {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .quick-filters {
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="page-header animate-in">
        <h1>
            <i class="fas fa-chart-line"></i>
            Pension Management Dashboard
        </h1>
        <p>Monitor and manage staff retirement schedules</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="stats-grid">
        <div class="stat-card stat-pending animate-in delay-1">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-chalkboard-user"></i>
                </div>
            </div>
            <div class="stat-value" style="color: #f59e0b;">{{ $stats['teachers_pending'] ?? 0 }}</div>
            <div class="stat-label">Teachers with Pension Date</div>
            <div class="stat-sub">Pending retirement</div>
        </div>
        <div class="stat-card stat-pending animate-in delay-2">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-users-gear"></i>
                </div>
            </div>
            <div class="stat-value" style="color: #f59e0b;">{{ $stats['staff_pending'] ?? 0 }}</div>
            <div class="stat-label">Staff with Pension Date</div>
            <div class="stat-sub">Pending retirement</div>
        </div>
        <div class="stat-card stat-pending animate-in delay-3">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
            <div class="stat-value" style="color: #f59e0b;">{{ $stats['principals_pending'] ?? 0 }}</div>
            <div class="stat-label">Principals with Pension Date</div>
            <div class="stat-sub">Pending retirement</div>
        </div>
        <div class="stat-card stat-warning animate-in delay-4">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-value" style="color: #e53935;">{{ $stats['near_retirement'] ?? 0 }}</div>
            <div class="stat-label">Near Retirement (≤ 90 Days)</div>
            <div class="stat-sub">Urgent attention needed</div>
        </div>
        <div class="stat-card stat-retired animate-in delay-5">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
            <div class="stat-value" style="color: #ef4444;">{{ $stats['total_retired'] ?? 0 }}</div>
            <div class="stat-label">Total Retired</div>
            <div class="stat-sub">All time</div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="filter-section animate-in">
        <form method="GET" action="{{ route('pension.dashboard') }}" id="filterForm">
            <div class="filter-group">
                <div class="form-group">
                    <label for="search"><i class="fas fa-search"></i> Search</label>
                    <input type="text" id="search" name="search" class="form-control" 
                           placeholder="Search by name, ID, or school..." 
                           value="{{ request('search') }}">
                </div>
                <div class="form-group">
                    <label for="filterType"><i class="fas fa-users"></i> Type</label>
                    <select id="filterType" name="type" class="form-control">
                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Employees</option>
                        <option value="teacher" {{ request('type') == 'teacher' ? 'selected' : '' }}>Teachers</option>
                        <option value="staff" {{ request('type') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="principal" {{ request('type') == 'principal' ? 'selected' : '' }}>Principals</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="daysFilter"><i class="fas fa-calendar-alt"></i> Days Until Retirement</label>
                    <select id="daysFilter" name="days" class="form-control">
                        <option value="all" {{ request('days') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="30" {{ request('days') == '30' ? 'selected' : '' }}>≤ 30 Days (Urgent)</option>
                        <option value="60" {{ request('days') == '60' ? 'selected' : '' }}>≤ 60 Days</option>
                        <option value="90" {{ request('days') == '90' ? 'selected' : '' }}>≤ 90 Days</option>
                        <option value="180" {{ request('days') == '180' ? 'selected' : '' }}>≤ 180 Days</option>
                        <option value="365" {{ request('days') == '365' ? 'selected' : '' }}>≤ 1 Year</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="statusFilter"><i class="fas fa-circle"></i> Status</label>
                    <select id="statusFilter" name="status" class="form-control">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="resigned" {{ request('status') == 'resigned' ? 'selected' : '' }}>Resigned</option>
                    </select>
                </div>
                <div style="display: flex; gap: 0.5rem; align-items: flex-end;">
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="{{ route('pension.dashboard') }}" class="btn-filter-outline">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        {{-- Quick Filters --}}
        <div class="quick-filters">
            <span class="quick-filters-label">
                <i class="fas fa-bolt"></i> Quick:
            </span>
            <button class="quick-filter-btn" onclick="applyQuickFilter('all')">All</button>
            <button class="quick-filter-btn warning" onclick="applyQuickFilter('90')">≤ 90 Days</button>
            <button class="quick-filter-btn danger" onclick="applyQuickFilter('30')">≤ 30 Days</button>
            <button class="quick-filter-btn" onclick="applyQuickFilter('teachers')">Teachers Only</button>
            <button class="quick-filter-btn" onclick="applyQuickFilter('staff')">Staff Only</button>
            <button class="quick-filter-btn" onclick="applyQuickFilter('principals')">Principals Only</button>
        </div>

        {{-- Active Filters Summary --}}
        @if(request()->hasAny(['search', 'type', 'days', 'status']) && (request('search') !== null || request('type') != 'all' || request('days') != 'all' || request('status') != 'all'))
        <div class="filter-summary">
            <span style="font-size:0.75rem;font-weight:600;color:var(--gray-600);display:flex;align-items:center;gap:0.375rem;">
                <i class="fas fa-tags"></i> Active Filters:
            </span>
            @if(request('search'))
                <span class="filter-tag">
                    <i class="fas fa-search"></i> "{{ request('search') }}"
                    <button class="remove-tag" onclick="removeFilter('search')" title="Remove filter">×</button>
                </span>
            @endif
            @if(request('type') && request('type') != 'all')
                <span class="filter-tag">
                    <i class="fas fa-users"></i> {{ ucfirst(request('type')) }}
                    <button class="remove-tag" onclick="removeFilter('type')" title="Remove filter">×</button>
                </span>
            @endif
            @if(request('days') && request('days') != 'all')
                <span class="filter-tag">
                    <i class="fas fa-calendar-alt"></i> ≤ {{ request('days') }} days
                    <button class="remove-tag" onclick="removeFilter('days')" title="Remove filter">×</button>
                </span>
            @endif
            @if(request('status') && request('status') != 'all')
                <span class="filter-tag">
                    <i class="fas fa-circle"></i> {{ ucfirst(request('status')) }}
                    <button class="remove-tag" onclick="removeFilter('status')" title="Remove filter">×</button>
                </span>
            @endif
        </div>
        @endif
    </div>

    {{-- Combined Employee Table --}}
    <div class="section-card animate-in">
        <div class="section-header">
            <i class="fas fa-list"></i>
            <h3>Employees Scheduled for Retirement</h3>
            <span class="badge-count">{{ $employees->total() ?? 0 }} Employees</span>
        </div>
        <div class="p-0">
            @if(isset($employees) && $employees->count() > 0)
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>School/Department</th>
                                <th>Pension Date</th>
                                <th>Days Left</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                            @php
                                $daysLeft = \Carbon\Carbon::now()->diffInDays($employee->pensionDate, false);
                                $rowClass = '';
                                if ($daysLeft <= 30) $rowClass = 'urgent-row';
                                elseif ($daysLeft <= 90) $rowClass = 'warning-row';
                                
                                // Determine school/department display
                                $schoolOrDept = '-';
                                if ($employee->type == 'teacher' || $employee->type == 'principal') {
                                    $schoolOrDept = $employee->schoolName ?? '-';
                                } elseif ($employee->type == 'staff') {
                                    $schoolOrDept = $employee->department ?? '-';
                                }
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td><code>{{ $employee->id }}</code></td>
                                <td class="fw-semibold">{{ $employee->name }}</td>
                                <td>
                                    @if($employee->type == 'teacher')
                                        <span class="badge" style="background: #dbeafe; color: #1e40af;">👨‍🏫 Teacher</span>
                                    @elseif($employee->type == 'staff')
                                        <span class="badge" style="background: #d1fae5; color: #065f46;">👔 Staff</span>
                                    @else
                                        <span class="badge" style="background: #fed7aa; color: #9a3412;">👑 Principal</span>
                                    @endif
                                </td>
                                <td>{{ $schoolOrDept }}</td>
                                <td>{{ \Carbon\Carbon::parse($employee->pensionDate)->format('d/m/Y') }}</td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
                                        <span class="days-badge {{ $daysLeft <= 30 ? 'days-urgent' : ($daysLeft <= 90 ? 'days-warning' : ($daysLeft <= 180 ? 'days-normal' : 'days-safe')) }}">
                                            @if($daysLeft <= 30)
                                                <i class="fas fa-exclamation-triangle"></i>
                                            @elseif($daysLeft <= 90)
                                                <i class="fas fa-clock"></i>
                                            @else
                                                <i class="fas fa-calendar-alt"></i>
                                            @endif
                                            {{ $daysLeft }} days
                                        </span>
                                        <div class="pension-progress">
                                            @php
                                                $totalDays = 365 * 30;
                                                $progress = max(0, min(100, (1 - ($daysLeft / 365)) * 100));
                                                $progressColor = $daysLeft <= 30 ? 'danger' : ($daysLeft <= 90 ? 'warning' : 'success');
                                            @endphp
                                            <div class="progress-bar {{ $progressColor }}" style="width: {{ $progress }}%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if(isset($employee->status) && ($employee->status == 'Berhenti' || $employee->status == 'Resigned'))
                                        <span class="status-badge status-resigned">
                                            <i class="fas fa-check-circle"></i> Resigned
                                        </span>
                                    @else
                                        <span class="status-badge status-active">
                                            <i class="fas fa-circle" style="font-size:0.5rem;"></i> Active
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="pagination-container">
                    <div class="pagination-info">
                        Showing {{ $employees->firstItem() ?? 0 }} to {{ $employees->lastItem() ?? 0 }} of {{ $employees->total() }} results
                    </div>
                    <div>
                        @if ($employees->hasPages())
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-sm mb-0">
                                    {{-- Previous Page Link --}}
                                    @if ($employees->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link">« Previous</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $employees->previousPageUrl() }}" rel="prev">« Previous</a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($employees->getUrlRange(1, $employees->lastPage()) as $page => $url)
                                        @if ($page == $employees->currentPage())
                                            <li class="page-item active" aria-current="page">
                                                <span class="page-link">{{ $page }}</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                            </li>
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($employees->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $employees->nextPageUrl() }}" rel="next">Next »</a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link">Next »</span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        @endif
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="empty-text">No employees match the current filters.</div>
                    <a href="{{ route('pension.dashboard') }}" class="btn-filter" style="display:inline-flex;margin-top:1rem;text-decoration:none;">
                        <i class="fas fa-undo"></i> Reset Filters
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Recently Retired Section --}}
    <div class="section-card animate-in">
        <div class="section-header">
            <i class="fas fa-clock"></i>
            <h3>Recently Retired (Last 30 Days)</h3>
            <span class="badge-count">{{ isset($recentlyRetired) ? $recentlyRetired->count() : 0 }} Retired</span>
        </div>
        <div class="p-0">
            @if(isset($recentlyRetired) && $recentlyRetired->count() > 0)
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>School/Department</th>
                                <th>Pension Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentlyRetired as $retired)
                            @php
                                $schoolOrDept = '-';
                                if (isset($retired->type)) {
                                    if ($retired->type == 'teacher' || $retired->type == 'principal') {
                                        $schoolOrDept = $retired->schoolName ?? '-';
                                    } elseif ($retired->type == 'staff') {
                                        $schoolOrDept = $retired->department ?? '-';
                                    }
                                }
                            @endphp
                            <tr>
                                <td><code>{{ $retired->id }}</code></td>
                                <td class="fw-semibold">{{ $retired->name }}</td>
                                <td>
                                    @if(($retired->type ?? '') == 'teacher')
                                        <span class="badge" style="background: #dbeafe; color: #1e40af;">👨‍🏫 Teacher</span>
                                    @elseif(($retired->type ?? '') == 'staff')
                                        <span class="badge" style="background: #d1fae5; color: #065f46;">👔 Staff</span>
                                    @else
                                        <span class="badge" style="background: #fed7aa; color: #9a3412;">👑 Principal</span>
                                    @endif
                                </td>
                                <td>{{ $schoolOrDept }}</td>
                                <td>
                                    <span class="badge" style="background: #e5e7eb; color: #374151;">
                                        <i class="fas fa-calendar-alt"></i> {{ isset($retired->pensionDate) ? \Carbon\Carbon::parse($retired->pensionDate)->format('d/m/Y') : '-' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="empty-text">No recent retirements in the last 30 days.</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="action-buttons animate-in">
        <a href="{{ route('pension.report') }}" class="btn-action btn-secondary-action">
            <i class="fas fa-chart-bar"></i> View Full Report
        </a>
        <a href="{{ route('pension.export') }}" class="btn-action btn-secondary-action">
            <i class="fas fa-file-export"></i> Export Report
        </a>
    </div>
</div>

<script>
    // Apply quick filter
    function applyQuickFilter(value) {
        const url = new URL(window.location.href);
        
        if (value === 'all') {
            url.searchParams.delete('days');
            url.searchParams.delete('type');
            url.searchParams.delete('search');
            url.searchParams.delete('status');
        } else if (value === 'teachers') {
            url.searchParams.set('type', 'teacher');
            url.searchParams.delete('days');
            url.searchParams.delete('status');
        } else if (value === 'staff') {
            url.searchParams.set('type', 'staff');
            url.searchParams.delete('days');
            url.searchParams.delete('status');
        } else if (value === 'principals') {
            url.searchParams.set('type', 'principal');
            url.searchParams.delete('days');
            url.searchParams.delete('status');
        } else {
            url.searchParams.set('days', value);
            url.searchParams.delete('type');
            url.searchParams.delete('status');
        }
        
        window.location.href = url.toString();
    }

    // Remove a specific filter
    function removeFilter(key) {
        const url = new URL(window.location.href);
        url.searchParams.delete(key);
        window.location.href = url.toString();
    }

    // Add animation on scroll
    const animateElements = document.querySelectorAll('.animate-in');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });
    
    animateElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'all 0.5s ease';
        observer.observe(el);
    });

    // Auto-submit form on select change
    document.querySelectorAll('#filterType, #daysFilter, #statusFilter').forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });

    // Debounced search
    let searchTimeout;
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500);
        });
    }
</script>
@endsection