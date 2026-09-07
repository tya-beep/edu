@extends('layouts.app')

@section('title', 'Teacher Details - ' . $teacher->teacherName)

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

    /* Main Card */
    .main-card {
        border: none;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    /* Card Header */
    .card-header-custom {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-dark));
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .card-header-custom h6 {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: white;
    }

    /* Back Button */
    .btn-back {
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-back:hover {
        background: rgba(255,255,255,0.3);
        color: white;
        transform: translateY(-1px);
        text-decoration: none;
    }

    /* Profile Cards */
    .profile-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }

    .profile-card-header {
        padding: 1rem 1.25rem;
        font-weight: 600;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .profile-card-header.personal {
        background: linear-gradient(135deg, #059669, #10b981);
        color: white;
    }

    .profile-card-header.contact {
        background: linear-gradient(135deg, #3b82f6, #60a5fa);
        color: white;
    }

    .profile-card-header.employment {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
        color: white;
    }

    .profile-card-body {
        padding: 1.25rem;
        background: white;
    }

    /* Info Table */
    .info-table {
        width: 100%;
        font-size: 0.875rem;
    }

    .info-table tr {
        border-bottom: 1px solid var(--gray-100);
    }

    .info-table tr:last-child {
        border-bottom: none;
    }

    .info-table th {
        width: 40%;
        padding: 0.75rem 0.5rem;
        font-weight: 600;
        color: var(--gray-700);
    }

    .info-table td {
        padding: 0.75rem 0.5rem;
        color: var(--gray-800);
    }

    /* Badges */
    .badge-active {
        background: var(--primary-soft);
        color: var(--primary-dark);
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .badge-stopped {
        background: #fee2e2;
        color: #dc2626;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .badge-male {
        background: #dbeafe;
        color: #2563eb;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .badge-female {
        background: #fce7f3;
        color: #db2777;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Info Cards */
    .info-card {
        background: white;
        border-radius: 16px;
        padding: 1rem;
        text-align: center;
        transition: all 0.2s ease;
        border: 1px solid var(--gray-100);
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .info-card-label {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--gray-600);
        margin-bottom: 0.5rem;
    }

    .info-card-value {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--gray-800);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .card-header-custom {
            flex-direction: column;
            text-align: center;
        }
        
        .info-table th,
        .info-table td {
            padding: 0.5rem;
        }
        
        .info-card {
            margin-bottom: 0.75rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            {{-- Main Card --}}
            <div class="card main-card shadow-sm">
                <div class="card-header-custom">
                    <h6>
                        👨‍🏫 Teacher Profile
                    </h6>
                    <div>
                        <a href="{{ route('principal.teachers.list') }}" class="btn-back">
                            ← Back to List
                        </a>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <div class="row">
                        {{-- Personal Information --}}
                        <div class="col-md-6 mb-4">
                            <div class="profile-card">
                                <div class="profile-card-header personal">
                                    👤 Personal Information
                                </div>
                                <div class="profile-card-body">
                                    <table class="info-table">
                                        <tr>
                                            <th>Teacher ID:</th>
                                            <td><strong>{{ $teacher->teacherID }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Full Name:</th>
                                            <td><strong>{{ $teacher->teacherName }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>IC Number:</th>
                                            <td>{{ $teacher->ICNumber ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Gender:</th>
                                            <td>
                                                @php
                                                    $genderValue = $teacher->gender ?? '';
                                                @endphp
                                                @if($genderValue == 'Male' || $genderValue == 'Lelaki')
                                                    <span class="badge-male"><i class="fas fa-mars"></i> Male</span>
                                                @elseif($genderValue == 'Female' || $genderValue == 'Perempuan')
                                                    <span class="badge-female"><i class="fas fa-venus"></i> Female</span>
                                                @else
                                                    <span class="badge-secondary">Not specified</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Race:</th>
                                            <td>{{ $teacher->race ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Marital Status:</th>
                                            <td>{{ $teacher->maritalStatus ?? 'Not specified' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Contact Information --}}
                        <div class="col-md-6 mb-4">
                            <div class="profile-card">
                                <div class="profile-card-header contact">
                                    📞 Contact Information
                                </div>
                                <div class="profile-card-body">
                                    <table class="info-table">
                                        <tr>
                                            <th>Phone Number:</th>
                                            <td>{{ $teacher->phoneNumber ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email Address:</th>
                                            <td>{{ $teacher->email ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Address:</th>
                                            <td>{{ $teacher->address ?? 'Not provided' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Employment Information --}}
                        <div class="col-md-12">
                            <div class="profile-card">
                                <div class="profile-card-header employment">
                                    💼 Employment Information
                                </div>
                                <div class="profile-card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <div class="info-card">
                                                <div class="info-card-label">Status</div>
                                                <div class="info-card-value">
                                                    @php
                                                        $status = $teacher->status ?? '';
                                                        $isActive = empty($status) || $status == 'Aktif' || $status == 'Active';
                                                    @endphp
                                                    @if($isActive)
                                                        <span class="badge-active">✅ Active</span>
                                                    @elseif($status == 'Berhenti' || $status == 'Stopped')
                                                        <span class="badge-stopped">⏹️ Stopped</span>
                                                    @else
                                                        <span class="badge-warning">{{ $status ?? 'Active' }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="info-card">
                                                <div class="info-card-label">📅 Appointed Date</div>
                                                <div class="info-card-value">
                                                    {{ $teacher->appointedDate ? date('d/m/Y', strtotime($teacher->appointedDate)) : 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="info-card">
                                                <div class="info-card-label">📅 Service Years</div>
                                                <div class="info-card-value">
                                                    @php
                                                        // serviceDate is stored as integer (years)
                                                        $serviceYears = $teacher->serviceDate ?? null;
                                                    @endphp
                                                    @if($serviceYears !== null && $serviceYears > 0)
                                                        {{ $serviceYears }} year{{ $serviceYears > 1 ? 's' : '' }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="info-card">
                                                <div class="info-card-label">📅 Pension Date</div>
                                                <div class="info-card-value">
                                                    {{ $teacher->pensionDate ? date('d/m/Y', strtotime($teacher->pensionDate)) : 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection