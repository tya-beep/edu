@extends('layouts.app')

@section('title', 'My Resignation Status - Principal')

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
        max-width: 900px;
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
    }

    .status-header::before {
        content: "👑";
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
        animation: fadeInUp 0.5s ease;
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
    }

    .status-title {
        flex: 1;
    }

    .status-title h3 {
        font-size: 1rem;
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
    }
    .status-pending .status-card-header {
        background: linear-gradient(135deg, #fef3c7, #fffbeb);
    }
    .status-pending .status-icon {
        background: #f59e0b;
        color: white;
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
    }
    .status-approved .status-card-header {
        background: linear-gradient(135deg, #d1fae5, #ecfdf5);
    }
    .status-approved .status-icon {
        background: #10b981;
        color: white;
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
    }
    .status-rejected .status-card-header {
        background: linear-gradient(135deg, #fee2e2, #fef2f2);
    }
    .status-rejected .status-icon {
        background: #ef4444;
        color: white;
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
    }
    .status-none .status-card-header {
        background: linear-gradient(135deg, #f3f4f6, #f9fafb);
    }
    .status-none .status-icon {
        background: #6b7280;
        color: white;
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
        transition: all 0.2s ease;
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

    /* Reason Box */
    .reason-box {
        background: var(--gray-50);
        border-radius: 16px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        border-left: 3px solid var(--primary-green);
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
        background: #dbeafe;
        border-radius: 16px;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-left: 4px solid #3b82f6;
    }

    .note-icon {
        font-size: 1.25rem;
    }

    .note-content {
        flex: 1;
        font-size: 0.85rem;
        color: #1e40af;
    }

    .note-content strong {
        font-weight: 700;
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
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-cancel:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220,38,38,0.3);
        color: white;
    }

    .btn-primary-new {
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
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-primary-new:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(5,150,105,0.3);
        color: white;
    }

    .btn-outline {
        background: white;
        border: 2px solid var(--gray-200);
        color: var(--gray-700);
        padding: 0.875rem 2rem;
        border-radius: 14px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-outline:hover {
        background: var(--gray-50);
        border-color: var(--gray-300);
        transform: translateY(-2px);
        text-decoration: none;
        color: var(--gray-800);
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
        margin-bottom: 1.5rem;
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

        .btn-cancel, .btn-primary-new, .btn-outline {
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
            <h1>My Resignation Status</h1>
            <p>Track your resignation application progress</p>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert-custom alert-success">
                <i class="fas fa-check-circle fa-lg"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if($principal->resignation_request_status == 'pending')
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
                                <div class="info-value-large">{{ $principal->resignation_request_date }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon-sm">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <div class="info-label">Days Since Submission</div>
                                <div class="info-value-large">
                                    {{ \Carbon\Carbon::parse($principal->resignation_request_date)->diffInDays(now()) }} days
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
                            {{ $principal->resignation_request_reason }}
                        </div>
                    </div>

                    <div class="note-box">
                        <div class="note-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="note-content">
                            <strong>Your application is being reviewed by HR.</strong> You will be notified once a decision is made. You may cancel your request at any time.
                        </div>
                    </div>

                    <form method="POST" action="{{ route('principal.resignations.cancel') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn-cancel" onclick="return confirm('Are you sure you want to cancel your resignation request? This action cannot be undone.')">
                            <i class="fas fa-times-circle"></i> Cancel Request
                        </button>
                    </form>
                </div>
            </div>

        @elseif($principal->resignation_request_status == 'approved')
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
                                <div class="info-value">{{ $principal->resignation_request_date }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon-sm">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <div class="info-label">Pension Date (Last Working Day)</div>
                                <div class="info-value">{{ $principal->pensionDate }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon-sm">
                                <i class="fas fa-hourglass-end"></i>
                            </div>
                            <div>
                                <div class="info-label">Days Remaining</div>
                                <div class="info-value">
                                    {{ \Carbon\Carbon::now()->diffInDays($principal->pensionDate) }} days
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
                            {{ $principal->resignation_request_reason }}
                        </div>
                    </div>

                    <div class="note-box" style="background: #d1fae5; border-left-color: #10b981;">
                        <div class="note-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="note-content" style="color: #065f46;">
                            <strong>Your resignation has been approved.</strong> You will be officially retired on <strong>{{ $principal->pensionDate }}</strong>. Please complete all necessary handover procedures.
                        </div>
                    </div>
                </div>
            </div>

        @elseif($principal->resignation_request_status == 'rejected')
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
                                <div class="info-value">{{ $principal->resignation_request_date }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="reason-box">
                        <div class="reason-title">
                            <i class="fas fa-pen-alt"></i>
                            Reason for Resignation
                        </div>
                        <div class="reason-text">
                            {{ $principal->resignation_request_reason }}
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

                    <div class="mt-3">
                        <a href="{{ route('principal.resignations.create') }}" class="btn-primary-new">
                            <i class="fas fa-plus-circle"></i> Submit New Request
                        </a>
                    </div>
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
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="empty-title">No Resignation Submitted</div>
                        <div class="empty-text">You haven't submitted any resignation request yet.</div>
                        <a href="{{ route('principal.resignations.create') }}" class="btn-primary-new">
                            <i class="fas fa-pen-alt"></i> Submit Resignation
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection