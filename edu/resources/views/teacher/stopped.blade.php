@extends('layouts.app')

@section('title', 'Resigned Teachers')

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
        --gray-300: #d1d5db;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
    }

    body {
        background: linear-gradient(135deg, #eef9f2 0%, #e0f2e9 100%);
        font-family: 'Inter', 'Poppins', system-ui, -apple-system, sans-serif;
        min-height: 100vh;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--primary-green), var(--primary-dark));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        margin-bottom: 0;
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-title::before {
        content: "👋";
        font-size: 2rem;
    }

    /* Stats Summary */
    .stats-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.25rem;
        border-radius: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        border: 1px solid var(--gray-100);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-green), var(--primary-light));
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }

    .stat-icon {
        font-size: 2rem;
        margin-bottom: 0.75rem;
    }

    .stat-info h4 {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--gray-600);
        margin-bottom: 0.5rem;
    }

    .stat-info p {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
        color: var(--gray-800);
    }

    .stat-info small {
        font-size: 0.7rem;
        color: var(--gray-500);
        display: block;
        margin-top: 0.25rem;
    }

    /* School Sections */
    .school-section {
        background: white;
        border-radius: 24px;
        overflow: hidden;
        margin-bottom: 1.75rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        transition: transform 0.2s ease;
    }

    .school-section:hover {
        transform: translateY(-2px);
    }

    .school-header {
        background: linear-gradient(135deg, #1e3d2c, #2e5a42);
        color: white;
        padding: 1rem 1.5rem;
        font-weight: 700;
        font-size: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .school-header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .school-header-left::before {
        content: "🏫";
        font-size: 1.25rem;
    }

    .school-badge {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Table Styles */
    .table-wrapper {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .table thead {
        background: var(--gray-50);
    }

    .table thead th {
        padding: 1rem;
        font-weight: 600;
        color: var(--gray-700);
        border-bottom: 2px solid var(--gray-200);
        text-align: left;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .table tbody td {
        padding: 1rem;
        border-bottom: 1px solid var(--gray-100);
        color: var(--gray-600);
        vertical-align: middle;
    }

    .table tbody tr {
        transition: background 0.2s ease;
    }

    .table tbody tr:hover {
        background: var(--primary-bg);
    }

    .teacher-name {
        font-weight: 700;
        color: var(--gray-800);
    }

    /* Status Badge */
    .status-stop {
        background: #fee2e2;
        color: #dc2626;
        padding: 0.25rem 0.875rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .status-stop::before {
        content: "●";
        font-size: 0.5rem;
    }

    /* Button */
    .btn-details {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-dark));
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        text-decoration: none;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .btn-details:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(5,150,105,0.3);
        color: white;
    }

    .btn-outline-custom {
        background: white;
        border: 1px solid var(--gray-200);
        color: var(--gray-700);
        padding: 0.625rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn-outline-custom:hover {
        background: var(--gray-50);
        border-color: var(--gray-300);
        transform: translateY(-1px);
        color: var(--gray-800);
    }

    /* Empty State */
    .empty-state {
        background: white;
        padding: 3rem;
        border-radius: 24px;
        text-align: center;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state h4 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: var(--gray-500);
        margin: 0;
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

    .school-section {
        animation: fadeInUp 0.4s ease-out;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stats-summary {
            grid-template-columns: 1fr;
        }
        
        .school-header {
            flex-direction: column;
            text-align: center;
        }
        
        .table thead th,
        .table tbody td {
            padding: 0.75rem;
        }
        
        .page-title {
            font-size: 1.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container py-4">

    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h2 class="page-title">Resigned Teachers</h2>

        <a href="{{ route('teachers.index') }}" class="btn-outline-custom">
            ← Back to Active Teachers
        </a>
    </div>

    {{-- Calculate statistics safely --}}
    @php
        $totalResigned = 0;
        $schoolsAffected = $teachers->count();
        
        foreach($teachers as $group) {
            $totalResigned += $group->count();
        }
        
        $currentYear = now()->year;
        $thisYearResigned = 0;
        
        foreach($teachers as $group) {
            foreach($group as $teacher) {
                if(isset($teacher->status_updated_at) && $teacher->status_updated_at) {
                    $updatedYear = \Carbon\Carbon::parse($teacher->status_updated_at)->year;
                    if($updatedYear == $currentYear) {
                        $thisYearResigned++;
                    }
                }
            }
        }
    @endphp

    {{-- Statistics Cards --}}
    <div class="stats-summary">
        <div class="stat-card">
            <div class="stat-icon">👋</div>
            <div class="stat-info">
                <h4>Total Resigned</h4>
                <p>{{ $totalResigned }}</p>
                <small>All time resignations</small>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">🏫</div>
            <div class="stat-info">
                <h4>Schools Affected</h4>
                <p>{{ $schoolsAffected }}</p>
                <small>Schools with resigned teachers</small>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">📅</div>
            <div class="stat-info">
                <h4>This Year</h4>
                <p>{{ $thisYearResigned }}</p>
                <small>Resignations in {{ $currentYear }}</small>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-info">
                <h4>Status</h4>
                <p>Inactive</p>
                <small>All resigned teachers</small>
            </div>
        </div>
    </div>

    {{-- Teachers List Grouped by School --}}
    @forelse($teachers as $schoolName => $group)
        <div class="school-section">
            <div class="school-header">
                <div class="school-header-left">
                    {{ $schoolName }}
                </div>
                <div class="school-badge">
                    {{ $group->count() }} {{ Str::plural('Teacher', $group->count()) }}
                </div>
            </div>

            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Teacher ID</th>
                            <th>Name</th>
                            <th>IC Number</th>
                            <th>Status</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group as $teacher)
                        <tr>
                            <td>
                                <code style="background: var(--gray-100); padding: 0.25rem 0.5rem; border-radius: 6px;">
                                    {{ $teacher->teacherID }}
                                </code>
                            </td>
                            <td class="teacher-name">{{ $teacher->teacherName }}</td>
                            <td>{{ $teacher->ICNumber }}</td>
                            <td>
                                <span class="status-stop">Resigned</span>
                            </td>
                            <td>
                                <a href="{{ route('teachers.show', $teacher->teacherID) }}" class="btn-details">
                                    📋 View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <div class="empty-state-icon">🎉</div>
            <h4>No Resigned Teachers Found</h4>
            <p>There are currently no resigned teachers in the system. All teachers are active.</p>
        </div>
    @endforelse

</div>
@endsection