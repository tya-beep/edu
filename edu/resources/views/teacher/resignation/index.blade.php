@extends('layouts.app')

@section('title', 'My Resignation Status')

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

    .status-container {
        max-width: 800px;
        margin: 0 auto;
    }

    /* Header Section */
    .status-header {
        background: linear-gradient(135deg, #1e3d2c 0%, #2e5a42 100%);
        border-radius: 24px;
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .status-header:hover {
        transform: translateY(-2px);
    }

    .status-header::before {
        content: "👨‍🏫";
        position: absolute;
        right: -20px;
        top: -20px;
        font-size: 100px;
        opacity: 0.08;
        pointer-events: none;
    }

    .status-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-header p {
        color: rgba(255,255,255,0.8);
        margin: 0;
        font-size: 0.9rem;
    }

    /* Status Cards */
    .status-card {
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        animation: fadeInUp 0.5s ease;
    }

    .status-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.12);
    }

    .status-card-header {
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .status-icon {
        width: 50px;
        height: 50px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        transition: transform 0.3s ease;
    }

    .status-card:hover .status-icon {
        transform: scale(1.05);
    }

    .status-title {
        flex: 1;
    }

    .status-title h3 {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0 0 0.25rem 0;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.875rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .status-card-body {
        padding: 1.5rem;
    }

    /* Pending Status */
    .status-pending {
        border-left: 4px solid #f59e0b;
        background: linear-gradient(135deg, #fffbeb, white);
    }
    .status-pending .status-card-header {
        background: linear-gradient(135deg, #fef3c7, #fffbeb);
    }
    .status-pending .status-icon {
        background: #f59e0b;
        color: white;
        box-shadow: 0 4px 15px rgba(245,158,11,0.3);
    }
    .status-pending .status-title h3 {
        color: #92400e;
    }
    .badge-pending {
        background: #fef3c7;
        color: #92400e;
    }

    /* Approved Status */
    .status-approved {
        border-left: 4px solid #10b981;
        background: linear-gradient(135deg, #ecfdf5, white);
    }
    .status-approved .status-card-header {
        background: linear-gradient(135deg, #d1fae5, #ecfdf5);
    }
    .status-approved .status-icon {
        background: #10b981;
        color: white;
        box-shadow: 0 4px 15px rgba(16,185,129,0.3);
    }
    .status-approved .status-title h3 {
        color: #065f46;
    }
    .badge-approved {
        background: #d1fae5;
        color: #065f46;
    }

    /* Rejected Status */
    .status-rejected {
        border-left: 4px solid #ef4444;
        background: linear-gradient(135deg, #fef2f2, white);
    }
    .status-rejected .status-card-header {
        background: linear-gradient(135deg, #fee2e2, #fef2f2);
    }
    .status-rejected .status-icon {
        background: #ef4444;
        color: white;
        box-shadow: 0 4px 15px rgba(239,68,68,0.3);
    }
    .status-rejected .status-title h3 {
        color: #991b1b;
    }
    .badge-rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    /* No Status */
    .status-none {
        border-left: 4px solid #6b7280;
        background: linear-gradient(135deg, #f9fafb, white);
    }
    .status-none .status-card-header {
        background: linear-gradient(135deg, #f3f4f6, #f9fafb);
    }
    .status-none .status-icon {
        background: #6b7280;
        color: white;
        box-shadow: 0 4px 15px rgba(107,114,128,0.3);
    }
    .status-none .status-title h3 {
        color: #374151;
    }

    /* Info Grid */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .info-item {
        background: var(--gray-50);
        border-radius: 16px;
        padding: 1rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .info-item::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background: var(--primary-green);
        transition: width 0.3s ease;
    }

    .info-item:hover::before {
        width: 100%;
    }

    .info-item:hover {
        background: var(--primary-bg);
        transform: translateX(5px);
    }

    .info-icon-sm {
        width: 36px;
        height: 36px;
        background: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .info-item:hover .info-icon-sm {
        background: var(--primary-green);
        color: white;
    }

    .info-label {
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--gray-600);
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-weight: 700;
        color: var(--gray-800);
        font-size: 0.9rem;
    }

    .info-value-large {
        font-size: 1rem;
        font-weight: 700;
        color: var(--gray-800);
    }

    /* Days Remaining Counter */
    .days-counter {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #d1fae5;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #065f46;
        margin-top: 1rem;
    }

    /* Reason Box */
    .reason-box {
        background: var(--gray-50);
        border-radius: 16px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        border-left: 3px solid var(--primary-green);
        transition: all 0.3s ease;
    }

    .reason-box:hover {
        background: var(--primary-bg);
        transform: translateX(5px);
    }

    .reason-title {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--gray-600);
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .reason-text {
        color: var(--gray-700);
        line-height: 1.6;
        font-size: 0.9rem;
    }

    /* Note Box */
    .note-box {
        border-radius: 16px;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    .note-box:hover {
        transform: translateX(5px);
    }

    .note-icon {
        font-size: 1.25rem;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .note-content {
        flex: 1;
        font-size: 0.85rem;
    }

    .note-info {
        background: #dbeafe;
        border-left: 4px solid #3b82f6;
    }
    .note-info .note-content {
        color: #1e40af;
    }

    /* Button Styles */
    .btn-cancel {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        color: white;
        border: none;
        padding: 0.875rem 2rem;
        border-radius: 14px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }

    .btn-cancel::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn-cancel:hover::before {
        left: 100%;
    }

    .btn-cancel:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220,38,38,0.3);
        color: white;
    }

    .btn-submit {
        background: linear-gradient(135deg, #059669, #047857);
        color: white;
        border: none;
        padding: 0.875rem 2rem;
        border-radius: 14px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }

    .btn-submit::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn-submit:hover::before {
        left: 100%;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(5,150,105,0.3);
        color: white;
    }

    /* Alert */
    .alert-custom {
        border-radius: 16px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        animation: slideIn 0.3s ease;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border-left: 4px solid #10b981;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #dc2626;
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

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .status-header {
            padding: 1.25rem;
        }

        .status-header h1 {
            font-size: 1.25rem;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .status-card-header {
            padding: 1rem;
        }

        .status-card-body {
            padding: 1rem;
        }

        .btn-cancel, .btn-submit {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="status-container">
        {{-- Header --}}
        <div class="status-header">
            <h1>
                <i class="fas fa-chalkboard-user"></i>
                My Resignation Status
            </h1>
            <p>Track your resignation application progress</p>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="alert-custom alert-success">
                <i class="fas fa-check-circle fa-lg"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-custom alert-danger">
                <i class="fas fa-exclamation-circle fa-lg"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if($teacher->resignation_request_status == 'pending')
            {{-- Pending Status Card --}}
            <div class="status-card status-pending">
                <div class="status-card-header">
                    <div class="status-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="status-title">
                        <h3>Status: Pending Review</h3>
                        <span class="status-badge badge-pending">
                            <i class="fas fa-hourglass-half"></i> Awaiting HR Decision
                        </span>
                    </div>
                </div>
                <div class="status-card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon-sm">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div>
                                <div class="info-label">Resignation Date</div>
                                <div class="info-value-large">{{ $teacher->resignation_request_date }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon-sm">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <div class="info-label">Days Since Submission</div>
                                <div class="info-value-large">
                                    {{ \Carbon\Carbon::parse($teacher->resignation_request_date)->diffInDays(now()) }} days
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="reason-box">
                        <div class="reason-title">
                            <i class="fas fa-pen-alt"></i>
                            Reason for Resignation
                        </div>
                        <div class="reason-text">
                            {{ $teacher->resignation_request_reason }}
                        </div>
                    </div>

                    <div class="note-box note-info">
                        <div class="note-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="note-content">
                            <strong>Your application is being reviewed by HR.</strong> You will be notified once a decision is made. You may cancel your request at any time.
                        </div>
                    </div>

                    <form method="POST" action="{{ route('teacher.resignations.cancel') }}" 
                          onsubmit="return confirm('Are you sure you want to cancel your resignation request? This action cannot be undone.')">
                        @csrf
                        <button type="submit" class="btn-cancel">
                            <i class="fas fa-times-circle"></i> Cancel Request
                        </button>
                    </form>
                </div>
            </div>

        @elseif($teacher->resignation_request_status == 'approved')
            {{-- Approved Status Card --}}
            <div class="status-card status-approved">
                <div class="status-card-header">
                    <div class="status-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="status-title">
                        <h3>Status: Approved</h3>
                        <span class="status-badge badge-approved">
                            <i class="fas fa-check"></i> Resignation Accepted
                        </span>
                    </div>
                </div>
                <div class="status-card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon-sm">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div>
                                <div class="info-label">Resignation Date</div>
                                <div class="info-value">{{ $teacher->resignation_request_date }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon-sm">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <div class="info-label">Pension Date (Last Working Day)</div>
                                <div class="info-value">{{ $teacher->pensionDate }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon-sm">
                                <i class="fas fa-hourglass-end"></i>
                            </div>
                            <div>
                                <div class="info-label">Days Remaining</div>
                                <div class="info-value">
                                    {{ \Carbon\Carbon::now()->diffInDays($teacher->pensionDate) }} days
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="days-counter">
                        <i class="fas fa-calendar-week"></i>
                        @php
                            $daysLeft = \Carbon\Carbon::now()->diffInDays($teacher->pensionDate);
                        @endphp
                        @if($daysLeft <= 30)
                            <span>⚠️ Only {{ $daysLeft }} days remaining until retirement</span>
                        @else
                            <span>{{ $daysLeft }} days remaining until retirement</span>
                        @endif
                    </div>

                    <div class="reason-box">
                        <div class="reason-title">
                            <i class="fas fa-pen-alt"></i>
                            Reason for Resignation
                        </div>
                        <div class="reason-text">
                            {{ $teacher->resignation_request_reason }}
                        </div>
                    </div>

                    <div class="note-box" style="background: #d1fae5; border-left-color: #10b981;">
                        <div class="note-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="note-content" style="color: #065f46;">
                            <strong>Your resignation has been approved.</strong> You will be officially retired on <strong>{{ $teacher->pensionDate }}</strong>. Please complete all necessary handover procedures.
                        </div>
                    </div>
                </div>
            </div>

        @elseif($teacher->resignation_request_status == 'rejected')
            {{-- Rejected Status Card --}}
            <div class="status-card status-rejected">
                <div class="status-card-header">
                    <div class="status-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="status-title">
                        <h3>Status: Rejected</h3>
                        <span class="status-badge badge-rejected">
                            <i class="fas fa-ban"></i> Resignation Declined
                        </span>
                    </div>
                </div>
                <div class="status-card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon-sm">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div>
                                <div class="info-label">Resignation Date</div>
                                <div class="info-value">{{ $teacher->resignation_request_date }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="reason-box">
                        <div class="reason-title">
                            <i class="fas fa-pen-alt"></i>
                            Reason for Resignation
                        </div>
                        <div class="reason-text">
                            {{ $teacher->resignation_request_reason }}
                        </div>
                    </div>

                    <div class="note-box" style="background: #fee2e2; border-left-color: #dc2626;">
                        <div class="note-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="note-content" style="color: #991b1b;">
                            <strong>Your resignation request has been rejected.</strong> Please contact HR for more information or submit a new request.
                        </div>
                    </div>

                    <a href="{{ route('teacher.resignations.create') }}" class="btn-submit">
                        <i class="fas fa-plus-circle"></i> Submit New Request
                    </a>
                </div>
            </div>

        @else
            {{-- No Request State --}}
            <div class="status-card status-none">
                <div class="status-card-header">
                    <div class="status-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <div class="status-title">
                        <h3>No Resignation Request</h3>
                        <span class="status-badge" style="background: #f3f4f6; color: #6b7280;">
                            <i class="fas fa-info-circle"></i> No Active Request
                        </span>
                    </div>
                </div>
                <div class="status-card-body">
                    <div style="text-align: center; padding: 2rem;">
                        <div style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.5;">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h4 style="font-size: 1.25rem; font-weight: 600; color: var(--gray-700); margin-bottom: 0.5rem;">
                            No Resignation Submitted
                        </h4>
                        <p style="color: var(--gray-500); margin-bottom: 1.5rem;">
                            You haven't submitted any resignation request yet.
                        </p>
                        <a href="{{ route('teacher.resignations.create') }}" class="btn-submit">
                            <i class="fas fa-pen-alt"></i> Submit Resignation
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    // Add animation on scroll
    const animateElements = document.querySelectorAll('.status-card');
    
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