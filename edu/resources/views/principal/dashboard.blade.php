@extends('layouts.app')

@section('title', 'Principal Dashboard - ' . ($school->schoolName ?? 'Dashboard'))

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

    body {
        background: linear-gradient(135deg, #eef9f2 0%, #e0f2e9 100%);
        font-family: 'Inter', 'Poppins', system-ui, sans-serif;
    }

    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0.75rem;
    }

    .welcome-card {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-dark));
        border: none;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 1.25rem;
        position: relative;
    }

    .welcome-card::before {
        content: "👨‍💼";
        position: absolute;
        right: -20px;
        top: -20px;
        font-size: 100px;
        opacity: 0.1;
        pointer-events: none;
    }

    .welcome-card .card-header {
        background: transparent;
        border: none;
        padding: 1.25rem 1.25rem 0.5rem 1.25rem;
    }

    .welcome-card .card-body {
        padding: 0.5rem 1.25rem 1.25rem 1.25rem;
    }

    .stat-card {
        border: none;
        border-radius: 16px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        background: white;
        height: 100%;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
    }

    .stat-card.border-left-success::before { background: #10b981; }
    .stat-card.border-left-danger::before { background: #ef4444; }
    .stat-card.border-left-warning::before { background: #f59e0b; }
    .stat-card.border-left-info::before { background: #3b82f6; }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }

    .stat-card .card-body {
        padding: 1rem 1.25rem;
    }

    .stat-label {
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--gray-600);
        text-align: center;
        margin-bottom: 0.25rem;
    }

    .stat-number {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--gray-800);
        line-height: 1.2;
        text-align: center;
        margin: 0;
    }

    .stat-number.text-success { color: #10b981; }
    .stat-number.text-danger { color: #ef4444; }
    .stat-number.text-warning { color: #f59e0b; }
    .stat-number.text-primary { color: #3b82f6; }

    .section-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        height: 100%;
    }

    .section-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }

    .section-card .card-header {
        background: var(--gray-50);
        border-bottom: 1px solid var(--gray-100);
        padding: 0.75rem 1rem;
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--gray-800);
    }

    .table-custom {
        font-size: 0.8rem;
        margin-bottom: 0;
    }

    .table-custom thead th {
        background: var(--gray-50);
        padding: 0.6rem 0.75rem;
        font-weight: 600;
        color: var(--gray-700);
        border-bottom: 2px solid var(--gray-200);
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        text-align: center;
    }

    .table-custom tbody td {
        padding: 0.6rem 0.75rem;
        vertical-align: middle;
        color: var(--gray-700);
        border-bottom: 1px solid var(--gray-100);
        text-align: center;
    }

    .table-custom tbody tr:hover {
        background: var(--primary-bg);
    }

    .action-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid var(--gray-100);
        text-decoration: none;
        display: block;
    }

    .action-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        border-color: var(--primary-soft);
        text-decoration: none;
    }

    .action-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        display: inline-block;
    }

    .action-title {
        font-weight: 600;
        color: var(--gray-800);
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }

    .action-desc {
        font-size: 0.7rem;
        color: var(--gray-500);
    }

    .badge-active {
        background: var(--primary-soft);
        color: var(--primary-dark);
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.65rem;
        font-weight: 600;
        display: inline-block;
    }

    .badge-resigned {
        background: #fee2e2;
        color: #dc2626;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.65rem;
        font-weight: 600;
        display: inline-block;
    }

    .badge-warning-custom {
        background: #fef3c7;
        color: #d97706;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.65rem;
        font-weight: 600;
        display: inline-block;
    }

    .badge-info-custom {
        background: #dbeafe;
        color: #1e40af;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.65rem;
        font-weight: 600;
        display: inline-block;
    }

    .badge-success-custom {
        background: #d1fae5;
        color: #065f46;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.65rem;
        font-weight: 600;
        display: inline-block;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-dark));
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        text-decoration: none;
        transition: all 0.2s ease;
        padding: 0.2rem 0.6rem;
        font-size: 0.65rem;
    }

    .btn-primary-custom:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(5,150,105,0.3);
        color: white;
        text-decoration: none;
    }

    .btn-outline-light {
        border: 1px solid rgba(255,255,255,0.3);
        background: transparent;
        color: white;
        transition: all 0.2s ease;
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
    }

    .btn-outline-light:hover {
        background: rgba(255,255,255,0.1);
        color: white;
    }

    code {
        background: var(--gray-100);
        padding: 0.15rem 0.35rem;
        border-radius: 4px;
        font-size: 0.7rem;
    }

    .stats-display {
        display: flex;
        justify-content: center;
        gap: 1.5rem;
        padding: 0.25rem 0;
        flex-wrap: wrap;
    }

    .stats-display-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.85rem;
    }

    .stats-display-item .label {
        font-weight: 600;
        color: var(--gray-600);
    }

    .stats-display-item .value {
        font-weight: 800;
        font-size: 1rem;
        color: var(--gray-800);
    }

    .stats-display-item .percentage {
        font-weight: 600;
        color: var(--gray-500);
        font-size: 0.8rem;
    }

    .color-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }

    .color-dot-blue { background: #3b82f6; }
    .color-dot-pink { background: #ec4899; }
    .color-dot-green { background: #10b981; }
    .color-dot-red { background: #ef4444; }
    .color-dot-orange { background: #f59e0b; }
    .color-dot-purple { background: #8b5cf6; }

    .progress-track {
        height: 6px;
        background: var(--gray-100);
        border-radius: 100px;
        overflow: hidden;
        display: flex;
        gap: 2px;
    }

    .progress-fill {
        height: 100%;
        border-radius: 100px;
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .progress-legend {
        display: flex;
        gap: 1rem;
        margin-top: 0.35rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.68rem;
        color: var(--gray-600);
        font-weight: 500;
    }

    .legend-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
    }

    .capacity-info {
        font-size: 0.75rem;
        color: var(--gray-500);
        margin-top: 0.15rem;
        text-align: center;
    }

    @media (max-width: 768px) {
        .stat-number {
            font-size: 1.1rem;
        }
        
        .action-card {
            margin-bottom: 0.75rem;
        }
        
        .welcome-card .card-header {
            padding: 0.75rem;
        }
        
        .welcome-card .card-body {
            padding: 0.25rem 0.75rem 0.75rem 0.75rem;
        }

        .dashboard-container {
            padding: 0.5rem;
        }

        .section-card .card-header {
            font-size: 0.75rem;
            padding: 0.6rem 0.75rem;
        }

        .stats-display {
            gap: 0.75rem;
        }

        .stats-display-item {
            font-size: 0.75rem;
        }
    }
</style>
@endsection

@section('content')
@php
    // Suppress IDE warnings for undefined variables
    if (!isset($principal)) $principal = new \stdClass();
    if (!isset($school)) $school = new \stdClass();
    if (!isset($totalTeachers)) $totalTeachers = 0;
    if (!isset($activeTeachers)) $activeTeachers = 0;
    if (!isset($resignedTeachers)) $resignedTeachers = 0;
    if (!isset($maleTeachers)) $maleTeachers = 0;
    if (!isset($femaleTeachers)) $femaleTeachers = 0;
    if (!isset($schoolCapacity)) $schoolCapacity = 0;
    if (!isset($vacancy)) $vacancy = 0;
    if (!isset($capacityPercentage)) $capacityPercentage = 0;
    if (!isset($totalTeacherFromSchool)) $totalTeacherFromSchool = 0;
    if (!isset($recentTeachers)) $recentTeachers = [];
    if (!isset($recentResigned)) $recentResigned = [];
    if (!isset($monthlyResignations)) $monthlyResignations = [];
    
    // Calculate total for percentage calculations
    $totalGender = $maleTeachers + $femaleTeachers;
    $malePercent = $totalGender > 0 ? round(($maleTeachers / $totalGender) * 100) : 0;
    $femalePercent = $totalGender > 0 ? round(($femaleTeachers / $totalGender) * 100) : 0;
    
    $totalStatus = $activeTeachers + $resignedTeachers;
    $activePercent = $totalStatus > 0 ? round(($activeTeachers / $totalStatus) * 100) : 0;
    $resignedPercent = $totalStatus > 0 ? round(($resignedTeachers / $totalStatus) * 100) : 0;

    // Determine vacancy status
    $vacancyStatus = '';
    $vacancyColor = '';
    if ($vacancy == 0) {
        $vacancyStatus = 'Full Capacity';
        $vacancyColor = 'text-success';
    } elseif ($vacancy > 0 && $vacancy <= 5) {
        $vacancyStatus = 'Low Vacancy';
        $vacancyColor = 'text-warning';
    } else {
        $vacancyStatus = 'Has Vacancy';
        $vacancyColor = 'text-primary';
    }
@endphp

<div class="dashboard-container">
    {{-- Welcome Card --}}
    <div class="card welcome-card shadow-sm">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0 text-white">
                        👋 Welcome back, {{ $principal->principalName ?? 'Principal' }}!
                    </h5>
                    <p class="text-white-50 mb-0 mt-1" style="font-size: 0.85rem;">
                        🏫 {{ $school->schoolName ?? 'School Name' }}
                    </p>
                </div>
                <div>
                    <span class="badge bg-light text-dark" style="font-size: 0.7rem;">
                        📅 {{ now()->format('l, d F Y') }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <p class="mb-0 text-white-50" style="font-size: 0.85rem;">
                    Manage your school's teachers, track resignations, and monitor overall performance from this dashboard.
                </p>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-light btn-sm" style="font-size: 0.75rem;">
                        👨‍🏫 Manage Teachers
                    </a>
                    <a href="#" class="btn btn-outline-light btn-sm" style="font-size: 0.75rem;">
                        👋 View Resigned
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-3 col-md-6 col-6">
            <div class="card stat-card border-left-success shadow-sm">
                <div class="card-body">
                    <div class="stat-label">Active Teachers</div>
                    <div class="stat-number text-success">{{ number_format($activeTeachers) }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-6">
            <div class="card stat-card border-left-danger shadow-sm">
                <div class="card-body">
                    <div class="stat-label">Resigned Teachers</div>
                    <div class="stat-number text-danger">{{ number_format($resignedTeachers) }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-6">
            <div class="card stat-card border-left-info shadow-sm">
                <div class="card-body">
                    <div class="stat-label">School Capacity</div>
                    <div class="stat-number">{{ number_format($schoolCapacity) }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-6">
            <div class="card stat-card border-left-warning shadow-sm">
                <div class="card-body">
                    <div class="stat-label">Vacancy</div>
                    <div class="stat-number {{ $vacancyColor }}">{{ number_format($vacancy) }}</div>
                    <div class="capacity-info">
                        <span class="badge {{ $vacancy == 0 ? 'badge-resigned' : ($vacancy <= 5 ? 'badge-warning-custom' : 'badge-info-custom') }}" style="font-size: 0.6rem;">
                            {{ $vacancyStatus }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Gender Distribution and Teacher Status --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-6 col-lg-6">
            <div class="card section-card shadow-sm">
                <div class="card-header">
                    📊 Gender Distribution
                </div>
                <div class="card-body">
                    <div class="stats-display">
                        <div class="stats-display-item">
                            <span class="color-dot color-dot-blue"></span>
                            <span class="label">Male:</span>
                            <span class="value">{{ number_format($maleTeachers) }}</span>
                            <span class="percentage">({{ $malePercent }}%)</span>
                        </div>
                        <div class="stats-display-item">
                            <span class="color-dot color-dot-pink"></span>
                            <span class="label">Female:</span>
                            <span class="value">{{ number_format($femaleTeachers) }}</span>
                            <span class="percentage">({{ $femalePercent }}%)</span>
                        </div>
                    </div>
                    <div class="progress-track" style="margin-top: 0.35rem;">
                        <div class="progress-fill" style="width: {{ $malePercent }}%; background: #3b82f6; border-radius: 100px 0 0 100px;"></div>
                        <div class="progress-fill" style="width: {{ $femalePercent }}%; background: #ec4899; border-radius: 0 100px 100px 0;"></div>
                    </div>
                    <div class="progress-legend">
                        <div class="legend-item">
                            <div class="legend-dot" style="background:#3b82f6;"></div>
                            Male — {{ $malePercent }}%
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot" style="background:#ec4899;"></div>
                            Female — {{ $femalePercent }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card section-card shadow-sm">
                <div class="card-header">
                    📈 Teacher Status
                </div>
                <div class="card-body">
                    <div class="stats-display">
                        <div class="stats-display-item">
                            <span class="color-dot color-dot-green"></span>
                            <span class="label">Active:</span>
                            <span class="value">{{ number_format($activeTeachers) }}</span>
                            <span class="percentage">({{ $activePercent }}%)</span>
                        </div>
                        <div class="stats-display-item">
                            <span class="color-dot color-dot-red"></span>
                            <span class="label">Resigned:</span>
                            <span class="value">{{ number_format($resignedTeachers) }}</span>
                            <span class="percentage">({{ $resignedPercent }}%)</span>
                        </div>
                    </div>
                    <div class="progress-track" style="margin-top: 0.35rem;">
                        <div class="progress-fill" style="width: {{ $activePercent }}%; background: #10b981; border-radius: 100px 0 0 100px;"></div>
                        <div class="progress-fill" style="width: {{ $resignedPercent }}%; background: #ef4444; border-radius: 0 100px 100px 0;"></div>
                    </div>
                    <div class="progress-legend">
                        <div class="legend-item">
                            <div class="legend-dot" style="background:#10b981;"></div>
                            Active — {{ $activePercent }}%
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot" style="background:#ef4444;"></div>
                            Resigned — {{ $resignedPercent }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Monthly Resignation Trends --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card section-card shadow-sm">
                <div class="card-header">
                    📉 Monthly Resignation Trends (Last 6 Months)
                </div>
                <div class="card-body">
                    @if(count($monthlyResignations) > 0)
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Resignations</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthlyResignations as $month)
                                        <tr>
                                            <td><strong>{{ $month['month'] }}</strong></td>
                                            <td>{{ $month['count'] }}</td>
                                            <td>
                                                @if($month['count'] == 0)
                                                    <span class="badge-active">✅ No resignations</span>
                                                @elseif($month['count'] <= 2)
                                                    <span class="badge-warning-custom">⚠️ {{ $month['count'] }} resignation(s)</span>
                                                @else
                                                    <span class="badge-resigned">🔴 {{ $month['count'] }} resignations</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted" style="font-size: 0.85rem;">
                            <i class="fas fa-info-circle me-2"></i>No resignation data available
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activities Row --}}
    <div class="row g-3">
        <div class="col-xl-6 col-lg-6">
            <div class="card section-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>🆕 Recently Added Teachers</span>
                    <a href="#" class="btn-primary-custom">
                        View All →
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Teacher ID</th>
                                    <th>Name</th>
                                    <th>Assign Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTeachers as $teacher)
                                <tr>
                                    <td><code>{{ $teacher->teacherID }}</code></td>
                                    <td><strong>{{ $teacher->teacherName }}</strong></td>
                                    <td>
                                        @if(isset($teacher->assignDate) && $teacher->assignDate)
                                            {{ date('d/m/Y', strtotime($teacher->assignDate)) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted" style="font-size: 0.85rem;">No teachers found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card section-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>⚠️ Recently Resigned Teachers</span>
                    <a href="#" class="btn-primary-custom">
                        View All →
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Teacher ID</th>
                                    <th>Name</th>
                                    <th>Resigned Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentResigned as $teacher)
                                <tr>
                                    <td><code>{{ $teacher->teacherID }}</code></td>
                                    <td><strong>{{ $teacher->teacherName }}</strong></td>
                                    <td>
                                        @if(isset($teacher->resigned_date) && $teacher->resigned_date)
                                            {{ date('d/m/Y', strtotime($teacher->resigned_date)) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted" style="font-size: 0.85rem;">No resigned teachers</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection