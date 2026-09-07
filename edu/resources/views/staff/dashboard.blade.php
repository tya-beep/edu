@extends('layouts.app')

@section('title', 'My Profile - Staff')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

    :root {
        --primary-blue: #2563eb;
        --primary-dark: #1d4ed8;
        --primary-light: #60a5fa;
        --primary-soft: #dbeafe;
        --primary-bg: #eff6ff;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        --warning: #f59e0b;
        --danger: #ef4444;
        --success: #10b981;
        --info: #3b82f6;
    }

    * {
        font-family: 'Inter', 'Poppins', system-ui, -apple-system, sans-serif;
    }

    body {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 50%, #e0e7ff 100%);
        min-height: 100vh;
    }

    .container {
        max-width: 1000px;
    }

    /* ===== Profile Card ===== */
    .profile-card {
        background: white;
        border-radius: 32px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        border: 1px solid rgba(37, 99, 235, 0.06);
        margin-top: 2rem;
    }

    /* ===== Profile Header ===== */
    .profile-header {
        background: linear-gradient(135deg, #1e2a4a 0%, #1a3a6a 40%, #1e4a8a 100%);
        padding: 2.5rem 3rem;
        position: relative;
        overflow: hidden;
    }

    .profile-header::before {
        content: "👤";
        position: absolute;
        right: -20px;
        top: -40px;
        font-size: 180px;
        opacity: 0.05;
        pointer-events: none;
    }

    .profile-header::after {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(96, 165, 250, 0.08) 0%, transparent 70%);
        border-radius: 50%;
        transform: translate(20%, -30%);
        pointer-events: none;
    }

    .profile-header-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 2rem;
        flex-wrap: wrap;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #60a5fa, #2563eb);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: white;
        font-weight: 700;
        box-shadow: 0 8px 32px rgba(37, 99, 235, 0.3);
        border: 4px solid rgba(255, 255, 255, 0.2);
        flex-shrink: 0;
        user-select: none;
    }

    .profile-title {
        flex: 1;
    }

    .profile-title h1 {
        font-size: 2rem;
        font-weight: 800;
        color: white;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .profile-title .subtitle {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.95rem;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .profile-title .subtitle .dot {
        width: 4px;
        height: 4px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        display: inline-block;
    }

    .profile-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(10px);
        padding: 0.4rem 1.2rem;
        border-radius: 50px;
        color: white;
        font-size: 0.8rem;
        font-weight: 500;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .profile-badge .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        display: inline-block;
        animation: pulse-dot 2s infinite;
    }

    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(0.8); }
    }

    /* ===== Profile Body ===== */
    .profile-body {
        padding: 2.5rem 3rem;
    }

    /* ===== Profile Sections ===== */
    .profile-section {
        margin-bottom: 2rem;
    }

    .profile-section:last-child {
        margin-bottom: 0;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--gray-100);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title .badge {
        font-size: 0.6rem;
        padding: 0.15rem 0.6rem;
        border-radius: 50px;
        font-weight: 600;
        background: var(--primary-soft);
        color: var(--primary-dark);
    }

    /* ===== Info Grid ===== */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem 2rem;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.6rem 0;
        border-bottom: 1px solid var(--gray-50);
    }

    .info-item .label {
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--gray-500);
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .info-item .value {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--gray-800);
    }

    .info-item .value .highlight {
        color: var(--primary-blue);
    }

    .info-item .value .tag {
        display: inline-block;
        padding: 0.15rem 0.6rem;
        border-radius: 50px;
        font-size: 0.65rem;
        font-weight: 600;
    }

    .tag-success { background: #d1fae5; color: #065f46; }
    .tag-warning { background: #fef3c7; color: #92400e; }
    .tag-danger { background: #fee2e2; color: #dc2626; }
    .tag-info { background: #dbeafe; color: #1e40af; }
    .tag-secondary { background: #f3f4f6; color: #6b7280; }

    .info-item .value .not-set {
        color: #9ca3af;
        font-weight: 400;
        font-style: italic;
        font-size: 0.8rem;
    }

    .not-set-placeholder {
        color: #9ca3af;
        font-weight: 400;
        font-style: italic;
        font-size: 0.8rem;
    }

    /* ===== Status Badge ===== */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .status-badge .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-badge .dot.green { background: #10b981; }
    .status-badge .dot.yellow { background: #f59e0b; }
    .status-badge .dot.red { background: #ef4444; }
    .status-badge .dot.blue { background: #3b82f6; }
    .status-badge .dot.gray { background: #9ca3af; }

    .status-badge.success { background: #d1fae5; color: #065f46; }
    .status-badge.warning { background: #fef3c7; color: #92400e; }
    .status-badge.danger { background: #fee2e2; color: #dc2626; }
    .status-badge.info { background: #dbeafe; color: #1e40af; }
    .status-badge.secondary { background: #f3f4f6; color: #6b7280; }

    /* ===== Responsive ===== */
    @media (max-width: 992px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .profile-header {
            padding: 1.5rem;
        }

        .profile-header-content {
            flex-direction: column;
            text-align: center;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            font-size: 2.5rem;
        }

        .profile-title h1 {
            font-size: 1.5rem;
        }

        .profile-title .subtitle {
            justify-content: center;
        }

        .profile-body {
            padding: 1.5rem;
        }

        .info-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.15rem;
        }
    }

    @media (max-width: 480px) {
        .profile-header::before {
            font-size: 100px;
            right: -10px;
            top: -20px;
        }

        .profile-badge {
            font-size: 0.7rem;
            padding: 0.3rem 0.8rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container py-4">

    {{-- ===== PROFILE CARD ===== --}}
    <div class="profile-card">

        {{-- ===== PROFILE HEADER ===== --}}
        <div class="profile-header">
            <div class="profile-header-content">
                <div class="profile-avatar">
                    {{ strtoupper(substr($staffData['staffName'] ?? 'S', 0, 1)) }}
                </div>
                <div class="profile-title">
                    <h1>{{ $staffData['staffName'] ?? 'Staff Member' }}</h1>
                    <div class="subtitle">
                        <span>🆔 {{ $staffData['staffID'] ?? '—' }}</span>
                        <span class="dot"></span>
                        <span>🏢 {{ $staffData['department'] ?? 'Not Assigned' }}</span>
                        <span class="dot"></span>
                        <span>📅 {{ date('d F Y') }}</span>
                    </div>
                </div>
                <div class="profile-badge">
                    <span class="status-dot"></span>
                    <span>Active</span>
                </div>
            </div>
        </div>

        {{-- ===== PROFILE BODY ===== --}}
        <div class="profile-body">

            {{-- ===== PERSONAL INFORMATION ===== --}}
            <div class="profile-section">
                <div class="section-title">
                    📋 Personal Information
                    <span class="badge">Basic Details</span>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">📛 Full Name</span>
                        <span class="value">{{ $staffData['staffName'] ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">🆔 Staff ID</span>
                        <span class="value"><span class="highlight">{{ $staffData['staffID'] ?? '—' }}</span></span>
                    </div>
                    <div class="info-item">
                        <span class="label">🪪 IC Number</span>
                        <span class="value">{{ $staffData['ICNumber'] ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">⚥ Gender</span>
                        <span class="value">{{ $staffData['gender'] ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">🌏 Race</span>
                        <span class="value">{{ $staffData['race'] ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">💍 Marital Status</span>
                        <span class="value">{{ $staffData['maritalStatus'] ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">✉️ Email</span>
                        <span class="value">{{ $staffData['email'] ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">📱 Phone</span>
                        <span class="value">{{ $staffData['phoneNumber'] ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">📅 Appointed Date</span>
                        <span class="value">
                            @if(isset($staffData['appointedDate']) && $staffData['appointedDate'] && $staffData['appointedDate'] != 'Not Set')
                                {{ $staffData['appointedDate'] }}
                            @else
                                <span class="not-set-placeholder">—</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="label">⏳ Years of Service</span>
                        <span class="value">
                            @if($serviceYears)
                                <strong>{{ $serviceYears }}</strong> years
                            @else
                                <span class="not-set-placeholder">—</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            {{-- ===== DEPARTMENT INFORMATION ===== --}}
            <div class="profile-section">
                <div class="section-title">
                    🏢 Department Information
                    <span class="badge">Current Assignment</span>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">🏛️ Department</span>
                        <span class="value"><strong>{{ $staffData['department'] ?? '—' }}</strong></span>
                    </div>
                    <div class="info-item">
                        <span class="label">📋 Role</span>
                        <span class="value">{{ $staffData['role'] ?? 'Staff' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">📊 Credit Hours</span>
                        <span class="value">{{ $staffData['credit_hour'] ?? '0' }}</span>
                    </div>
                </div>
            </div>

            {{-- ===== PENSION INFORMATION ===== --}}
            <div class="profile-section">
                <div class="section-title">
                    🏦 Pension Information
                    <span class="badge">Retirement Planning</span>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">📅 Pension Date</span>
                        <span class="value">
                            @if($pensionDate && $pensionDate != 'Not Set')
                                <strong class="highlight">{{ $pensionDate }}</strong>
                            @else
                                <span class="not-set-placeholder">—</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="label">📅 Service Start Date</span>
                        <span class="value">
                            @if($serviceStartDate && $serviceStartDate != 'Not Set')
                                {{ $serviceStartDate }}
                            @else
                                <span class="not-set-placeholder">—</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="label">⏳ Days Until Pension</span>
                        <span class="value">
                            @if($daysUntilPension !== null && $daysUntilPension > 0)
                                <strong>{{ number_format($daysUntilPension) }}</strong> days
                            @elseif($daysUntilPension !== null && $daysUntilPension <= 0)
                                <span class="tag tag-success">🎉 Retired</span>
                            @else
                                <span class="not-set-placeholder">—</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="label">📋 Pension Status</span>
                        <span class="value">
                            @if($pensionStatus && $pensionStatus != 'Not Set')
                                <span class="status-badge {{ $pensionStatusClass }}">
                                    <span class="dot 
                                        @if($pensionStatusClass == 'success') green
                                        @elseif($pensionStatusClass == 'warning') yellow
                                        @elseif($pensionStatusClass == 'danger') red
                                        @elseif($pensionStatusClass == 'info') blue
                                        @else gray
                                        @endif
                                    "></span>
                                    {{ $pensionStatus }}
                                </span>
                            @else
                                <span class="not-set-placeholder">—</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            {{-- ===== ADDRESS ===== --}}
            @if(isset($staffData['address']) && $staffData['address'] && $staffData['address'] != 'Not Set')
            <div class="profile-section">
                <div class="section-title">
                    📍 Home Address
                    <span class="badge">Contact</span>
                </div>
                <div style="padding:0.75rem 1rem;background:var(--gray-50);border-radius:12px;border:1px solid var(--gray-100);">
                    <p style="margin:0;color:var(--gray-700);font-size:0.9rem;line-height:1.6;">
                        {{ $staffData['address'] }}
                    </p>
                </div>
            </div>
            @endif

        </div>
        {{-- End Profile Body --}}
    </div>
    {{-- End Profile Card --}}

</div>


@endsection