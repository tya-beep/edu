@extends('layouts.app')

@section('title', 'Pending Staff Resignations')

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
        content: "👥";
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

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 1.25rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--gray-100);
        position: relative;
        overflow: hidden;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
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

    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .stat-pending .stat-icon {
        background: #fef3c7;
        color: #f59e0b;
    }

    .stat-approved .stat-icon {
        background: #d1fae5;
        color: #10b981;
    }

    .stat-rejected .stat-icon {
        background: #fee2e2;
        color: #ef4444;
    }

    .stat-label {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--gray-600);
        margin-bottom: 0.25rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--gray-800);
        line-height: 1.1;
    }

    .stat-trend {
        font-size: 0.7rem;
        margin-top: 0.5rem;
        color: var(--gray-500);
    }

    /* Table Card */
    .table-card {
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .table-card:hover {
        box-shadow: 0 15px 50px rgba(0,0,0,0.12);
    }

    .table-header {
        background: linear-gradient(135deg, var(--gray-50), white);
        padding: 1.25rem 1.5rem;
        border-bottom: 2px solid var(--gray-100);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .table-header h5 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--gray-800);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .table-header h5 i {
        color: var(--primary-green);
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
        transform: scale(1.01);
    }

    /* Reason Preview */
    .reason-preview {
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: var(--gray-600);
    }

    .reason-preview:hover {
        white-space: normal;
        word-wrap: break-word;
        background: var(--gray-50);
        padding: 0.5rem;
        border-radius: 8px;
        position: relative;
        z-index: 1;
    }

    /* Button Styles */
    .btn-review {
        background: linear-gradient(135deg, #059669, #047857);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-review:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(5,150,105,0.3);
        color: white;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem;
    }

    .empty-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }

    .empty-text {
        color: var(--gray-500);
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

    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            padding: 1.25rem;
        }

        .page-header h1 {
            font-size: 1.25rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .table-header {
            flex-direction: column;
            text-align: center;
        }

        .modern-table {
            font-size: 0.75rem;
        }

        .modern-table thead th,
        .modern-table tbody td {
            padding: 0.5rem;
        }

        .btn-review {
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="page-header animate-in">
        <h1>
            <i class="fas fa-users-gear"></i>
            Staff Resignation Management
        </h1>
        <p>Review and manage pending staff resignation requests</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="stats-grid animate-in delay-1">
        <div class="stat-card stat-pending">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-label">Pending Review</div>
            <div class="stat-value">{{ $stats['pending'] ?? 0 }}</div>
            <div class="stat-trend">
                <i class="fas fa-hourglass-half"></i> Awaiting Action
            </div>
        </div>
        <div class="stat-card stat-approved">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-label">Approved</div>
            <div class="stat-value">{{ $stats['approved'] ?? 0 }}</div>
            <div class="stat-trend">
                <i class="fas fa-check"></i> Resignations Accepted
            </div>
        </div>
        <div class="stat-card stat-rejected">
            <div class="stat-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-label">Rejected</div>
            <div class="stat-value">{{ $stats['rejected'] ?? 0 }}</div>
            <div class="stat-trend">
                <i class="fas fa-ban"></i> Resignations Declined
            </div>
        </div>
    </div>

    {{-- Pending Staff Table --}}
    <div class="table-card animate-in delay-2">
        <div class="table-header">
            <h5>
                <i class="fas fa-list"></i>
                Pending Staff Resignation Requests
            </h5>
            @if(isset($staff) && $staff->count() > 0)
                <span class="badge-count">{{ $staff->count() }} Pending</span>
            @endif
        </div>
        <div class="table-responsive">
            @if(isset($staff) && $staff->count() > 0)
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Staff ID</th>
                            <th>Staff Name</th>
                            <th>Department</th>
                            <th>Resignation Date</th>
                            <th>Reason</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $staffMember)
                        <tr>
                            <td>
                                <code style="background: var(--gray-100); padding: 0.25rem 0.5rem; border-radius: 6px;">
                                    {{ $staffMember->staffID }}
                                </code>
                            </td>
                            <td class="fw-semibold">{{ $staffMember->staffName }}</td>
                            <td>{{ $staffMember->department ?? '-' }}</td>
                            <td>
                                <span class="badge" style="background: #fef3c7; color: #92400e;">
                                    <i class="fas fa-calendar-day"></i> {{ $staffMember->resignation_request_date }}
                                </span>
                            </td>
                            <td>
                                <div class="reason-preview" title="{{ $staffMember->resignation_request_reason }}">
                                    {{ Str::limit($staffMember->resignation_request_reason, 60) }}
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('hr.staff-resignations.show', $staffMember->staffID) }}" 
                                   class="btn-review">
                                    <i class="fas fa-eye"></i> Review
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="empty-title">No Pending Requests</div>
                    <div class="empty-text">All staff resignation requests have been reviewed.</div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
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
</script>
@endsection