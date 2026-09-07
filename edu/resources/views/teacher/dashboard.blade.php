@extends('layouts.app')

@section('title', 'My Profile')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

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
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 50%, #e0f2e9 100%);
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
        border: 1px solid rgba(5, 150, 105, 0.06);
        margin-top: 2rem;
    }

    /* ===== Profile Header ===== */
    .profile-header {
        background: linear-gradient(135deg, #0f2e1f 0%, #1a4732 40%, #1e5a3a 100%);
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
        background: radial-gradient(circle, rgba(16, 185, 129, 0.08) 0%, transparent 70%);
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
        background: linear-gradient(135deg, #10b981, #059669);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: white;
        font-weight: 700;
        box-shadow: 0 8px 32px rgba(5, 150, 105, 0.3);
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

    .profile-title .subtitle .text-muted {
        color: rgba(255, 255, 255, 0.5);
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
        color: var(--primary-green);
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

    /* ===== Pension Progress ===== */
    .pension-progress-wrapper {
        background: var(--gray-50);
        border-radius: 16px;
        padding: 1.5rem;
        margin-top: 0.5rem;
        border: 1px solid var(--gray-100);
    }

    .pension-progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .pension-progress-header .label {
        font-weight: 600;
        color: var(--gray-700);
        font-size: 0.9rem;
    }

    .pension-progress-header .value {
        font-weight: 800;
        color: var(--primary-green);
        font-size: 1.1rem;
    }

    .progress-bar {
        width: 100%;
        height: 10px;
        background: var(--gray-200);
        border-radius: 10px;
        overflow: hidden;
        position: relative;
    }

    .progress-bar .fill {
        height: 100%;
        border-radius: 10px;
        background: linear-gradient(90deg, var(--primary-green), var(--primary-light));
        transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .progress-bar .fill.warning {
        background: linear-gradient(90deg, #f59e0b, #fbbf24);
    }

    .progress-bar .fill.danger {
        background: linear-gradient(90deg, #ef4444, #f87171);
    }

    .progress-bar .fill.complete {
        background: linear-gradient(90deg, #10b981, #34d399);
    }

    .pension-summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-top: 1rem;
    }

    .pension-summary-item {
        text-align: center;
        padding: 0.75rem;
        background: white;
        border-radius: 12px;
        border: 1px solid var(--gray-100);
    }

    .pension-summary-item .number {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--gray-800);
    }

    .pension-summary-item .number.green { color: #059669; }
    .pension-summary-item .number.orange { color: #d97706; }
    .pension-summary-item .number.blue { color: #2563eb; }
    .pension-summary-item .number.gray { color: #9ca3af; }

    .pension-summary-item .label {
        font-size: 0.6rem;
        font-weight: 600;
        color: var(--gray-400);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 0.1rem;
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

    /* ===== Not Set Placeholder ===== */
    .not-set-placeholder {
        color: #9ca3af;
        font-weight: 400;
        font-style: italic;
        font-size: 0.8rem;
    }

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

        .pension-summary {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .pension-summary-item {
            padding: 0.5rem;
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
                    {{ strtoupper(substr($selfInfo['teacherName'] ?? 'T', 0, 1)) }}
                </div>
                <div class="profile-title">
                    <h1>{{ $selfInfo['teacherName'] ?? 'Teacher' }}</h1>
                    <div class="subtitle">
                        <span>🆔 {{ $selfInfo['teacherID'] ?? '—' }}</span>
                        <span class="dot"></span>
                        <span>🏫 {{ $schoolName ?? 'Not Assigned' }}</span>
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
                        <span class="value">{{ $selfInfo['teacherName'] ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">🆔 Teacher ID</span>
                        <span class="value"><span class="highlight">{{ $selfInfo['teacherID'] ?? '—' }}</span></span>
                    </div>
                    <div class="info-item">
                        <span class="label">🪪 IC Number</span>
                        <span class="value">{{ $selfInfo['ICNumber'] ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">⚥ Gender</span>
                        <span class="value">{{ $selfInfo['gender'] ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">🌏 Race</span>
                        <span class="value">{{ $selfInfo['race'] ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">💍 Marital Status</span>
                        <span class="value">{{ $selfInfo['maritalStatus'] ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">✉️ Email</span>
                        <span class="value">{{ $selfInfo['email'] ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">📱 Phone</span>
                        <span class="value">{{ $selfInfo['phoneNumber'] ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">📅 Appointed Date</span>
                        <span class="value">
                            @if($selfInfo['appointedDate'] && $selfInfo['appointedDate'] != 'Not Set')
                                {{ $selfInfo['appointedDate'] }}
                            @else
                                <span class="not-set-placeholder">—</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="label">⏳ Years of Service</span>
                        <span class="value">
                            @php
                                // Use serviceDate from teacher data (stored as integer years)
                                $serviceYearsFromDb = $teacherData->serviceDate ?? null;
                            @endphp
                            
                            @if($serviceYearsFromDb !== null && $serviceYearsFromDb > 0)
                                <strong>{{ $serviceYearsFromDb }}</strong> year{{ $serviceYearsFromDb > 1 ? 's' : '' }}
                            @elseif($serviceYears && $serviceYears > 0)
                                <strong>{{ $serviceYears }}</strong> year{{ $serviceYears > 1 ? 's' : '' }}
                            @else
                                <span class="not-set-placeholder">—</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            {{-- ===== SCHOOL INFORMATION ===== --}}
            <div class="profile-section">
                <div class="section-title">
                    🏫 School Information
                    <span class="badge">Current Assignment</span>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">🏛️ School Name</span>
                        <span class="value"><strong>{{ $schoolName ?? '—' }}</strong></span>
                    </div>
                    <div class="info-item">
                        <span class="label">🆔 School ID</span>
                        <span class="value">{{ $schoolID ?? '—' }}</span>
                    </div>
                    @if($schoolAddress)
                    <div class="info-item" style="grid-column: 1 / -1;">
                        <span class="label">📍 Address</span>
                        <span class="value" style="font-size:0.85rem;">{{ $schoolAddress }}</span>
                    </div>
                    @endif
                    @if($schoolPhone)
                    <div class="info-item">
                        <span class="label">📞 Phone</span>
                        <span class="value">{{ $schoolPhone }}</span>
                    </div>
                    @endif
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
                                <strong>{{ $serviceStartDate }}</strong>
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

            {{-- ===== ADDRESS ===== --}}
            @if($selfInfo['address'] && $selfInfo['address'] != 'Not Set')
            <div class="profile-section">
                <div class="section-title">
                    📍 Home Address
                    <span class="badge">Contact</span>
                </div>
                <div style="padding:0.75rem 1rem;background:var(--gray-50);border-radius:12px;border:1px solid var(--gray-100);">
                    <p style="margin:0;color:var(--gray-700);font-size:0.9rem;line-height:1.6;">
                        {{ $selfInfo['address'] }}
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