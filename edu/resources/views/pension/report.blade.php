@extends('layouts.app')

@section('title', 'Pension Report')

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
        content: "📊";
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

    /* Filter Card */
    .filter-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .filter-card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .filter-header {
        background: linear-gradient(135deg, var(--gray-50), white);
        padding: 1rem 1.5rem;
        border-bottom: 2px solid var(--gray-100);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-header i {
        color: var(--primary-green);
        font-size: 1.1rem;
    }

    .filter-header h5 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--gray-800);
        margin: 0;
    }

    /* Data Cards */
    .data-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        animation: fadeInUp 0.5s ease;
    }

    .data-card:hover {
        box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }

    .card-header-custom {
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .card-header-custom i {
        font-size: 1.25rem;
    }

    .card-header-custom h5 {
        flex: 1;
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
    }

    .badge-count {
        background: rgba(255,255,255,0.2);
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
        padding: 0.875rem 1rem;
        font-weight: 600;
        color: var(--gray-700);
        border-bottom: 1px solid var(--gray-200);
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .modern-table tbody td {
        padding: 0.875rem 1rem;
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

    /* Teacher Section */
    .card-teacher .card-header-custom {
        background: linear-gradient(135deg, #dbeafe, #eff6ff);
        border-bottom-color: #bfdbfe;
    }
    .card-teacher .card-header-custom i,
    .card-teacher .card-header-custom h5 {
        color: #1e40af;
    }

    /* Staff Section */
    .card-staff .card-header-custom {
        background: linear-gradient(135deg, #d1fae5, #ecfdf5);
        border-bottom-color: #a7f3d0;
    }
    .card-staff .card-header-custom i,
    .card-staff .card-header-custom h5 {
        color: #065f46;
    }

    /* Principal Section */
    .card-principal .card-header-custom {
        background: linear-gradient(135deg, #fed7aa, #fffbeb);
        border-bottom-color: #fdba74;
    }
    .card-principal .card-header-custom i,
    .card-principal .card-header-custom h5 {
        color: #9a3412;
    }

    /* Button Styles */
    .btn-apply {
        background: linear-gradient(135deg, #059669, #047857);
        color: white;
        border: none;
        padding: 0.6rem 1rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        width: 100%;
        justify-content: center;
    }

    .btn-apply:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(5,150,105,0.3);
    }

    .btn-export {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        border: none;
        padding: 0.6rem 1rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        width: 100%;
        justify-content: center;
        text-decoration: none;
    }

    .btn-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37,99,235,0.3);
        color: white;
    }

    .btn-back {
        background: white;
        border: 2px solid var(--gray-200);
        color: var(--gray-700);
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-back:hover {
        background: var(--gray-50);
        border-color: var(--gray-300);
        transform: translateY(-2px);
    }

    /* Select Dropdown */
    .form-select-custom {
        border: 2px solid var(--gray-200);
        border-radius: 12px;
        padding: 0.6rem 1rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        width: 100%;
    }

    .form-select-custom:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(5,150,105,0.1);
        outline: none;
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
        opacity: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            padding: 1.25rem;
        }

        .page-header h1 {
            font-size: 1.25rem;
        }

        .filter-header {
            padding: 0.75rem 1rem;
        }

        .card-header-custom {
            padding: 0.75rem 1rem;
        }

        .modern-table {
            font-size: 0.75rem;
        }

        .modern-table thead th,
        .modern-table tbody td {
            padding: 0.5rem;
        }

        .btn-apply, .btn-export {
            margin-top: 0.5rem;
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
            Pension Report
        </h1>
        <p>View and export pension information for all staff members</p>
    </div>

    {{-- Filter Card --}}
    <div class="filter-card animate-in">
        <div class="filter-header">
            <i class="fas fa-sliders-h"></i>
            <h5>Filter Report</h5>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('pension.report') }}" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-semibold small text-uppercase text-muted">
                        <i class="fas fa-users"></i> Staff Type
                    </label>
                    <select name="type" class="form-select-custom">
                        <option value="all" {{ ($type ?? 'all') == 'all' ? 'selected' : '' }}>📊 All Staff</option>
                        <option value="teacher" {{ ($type ?? '') == 'teacher' ? 'selected' : '' }}>👨‍🏫 Teachers Only</option>
                        <option value="staff" {{ ($type ?? '') == 'staff' ? 'selected' : '' }}>👔 Staff Only</option>
                        <option value="principal" {{ ($type ?? '') == 'principal' ? 'selected' : '' }}>👑 Principals Only</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-uppercase text-muted">&nbsp;</label>
                    <button type="submit" class="btn-apply">
                        <i class="fas fa-filter"></i> Apply Filter
                    </button>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-uppercase text-muted">&nbsp;</label>
                    <a href="{{ route('pension.export', request()->all()) }}" class="btn-export">
                        <i class="fas fa-download"></i> Export to CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Teachers Section --}}
    @if(isset($teachers) && $teachers->count() > 0)
    <div class="data-card card-teacher animate-in">
        <div class="card-header-custom">
            <i class="fas fa-chalkboard-user"></i>
            <h5>Teachers with Pension Date</h5>
            <span class="badge-count" style="background: #bfdbfe; color: #1e40af;">{{ $teachers->count() }} Teachers</span>
        </div>
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Teacher ID</th>
                        <th>Name</th>
                        <th>IC Number</th>
                        <th>School</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Pension Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachers as $teacher)
                    <tr>
                        <td><code>{{ $teacher->teacherID }}</code></td>
                        <td class="fw-semibold">{{ $teacher->teacherName }}</td>
                        <td>{{ $teacher->ICNumber }}</td>
                        <td>{{ $teacher->schoolName ?? '-' }}</td>
                        <td>{{ $teacher->phoneNumber ?? '-' }}</td>
                        <td>{{ $teacher->email ?? '-' }}</td>
                        <td>
                            <span class="badge" style="background: #d1fae5; color: #065f46;">
                                <i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($teacher->pensionDate)->format('d/m/Y') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Staff Section --}}
    @if(isset($staff) && $staff->count() > 0)
    <div class="data-card card-staff animate-in">
        <div class="card-header-custom">
            <i class="fas fa-users-gear"></i>
            <h5>Staff with Pension Date</h5>
            <span class="badge-count" style="background: #a7f3d0; color: #065f46;">{{ $staff->count() }} Staff</span>
        </div>
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Staff ID</th>
                        <th>Name</th>
                        <th>IC Number</th>
                        <th>Department</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Pension Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staff as $staffMember)
                    <tr>
                        <td><code>{{ $staffMember->staffID }}</code></td>
                        <td class="fw-semibold">{{ $staffMember->staffName }}</td>
                        <td>{{ $staffMember->ICNumber }}</td>
                        <td>{{ $staffMember->department ?? '-' }}</td>
                        <td>{{ $staffMember->phoneNumber ?? '-' }}</td>
                        <td>{{ $staffMember->email ?? '-' }}</td>
                        <td>
                            <span class="badge" style="background: #d1fae5; color: #065f46;">
                                <i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($staffMember->pensionDate)->format('d/m/Y') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Principals Section --}}
    @if(isset($principals) && $principals->count() > 0)
    <div class="data-card card-principal animate-in">
        <div class="card-header-custom">
            <i class="fas fa-user-tie"></i>
            <h5>Principals with Pension Date</h5>
            <span class="badge-count" style="background: #fdba74; color: #9a3412;">{{ $principals->count() }} Principals</span>
        </div>
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Principal ID</th>
                        <th>Name</th>
                        <th>IC Number</th>
                        <th>School</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Pension Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($principals as $principal)
                    <tr>
                        <td><code>{{ $principal->principalID }}</code></td>
                        <td class="fw-semibold">{{ $principal->principalName }}</td>
                        <td>{{ $principal->ICNumber }}</td>
                        <td>{{ $principal->schoolName ?? '-' }}</td>
                        <td>{{ $principal->phoneNumber ?? '-' }}</td>
                        <td>{{ $principal->email ?? '-' }}</td>
                        <td>
                            <span class="badge" style="background: #fed7aa; color: #9a3412;">
                                <i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($principal->pensionDate)->format('d/m/Y') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Empty State --}}
    @if((!isset($teachers) || $teachers->count() == 0) && 
        (!isset($staff) || $staff->count() == 0) && 
        (!isset($principals) || $principals->count() == 0))
        <div class="data-card animate-in">
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="empty-title">No Data Found</div>
                <div class="empty-text">No staff members have pension dates set.</div>
            </div>
        </div>
    @endif

    {{-- Back Button --}}
    <div class="mt-4 animate-in">
        <a href="{{ route('pension.dashboard') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
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