@extends('layouts.app')

@section('title', 'Review Teacher Resignation')

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
    }

    body {
        background: linear-gradient(135deg, #eef9f2 0%, #e0f2e9 100%);
        font-family: 'Inter', 'Poppins', system-ui, sans-serif;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--primary-green), var(--primary-dark));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        margin-bottom: 1.5rem;
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-title::before {
        content: "📋";
        font-size: 2rem;
    }

    /* Profile Card */
    .profile-card {
        border: none;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        transition: transform 0.3s ease;
    }

    .profile-card:hover {
        transform: translateY(-2px);
    }

    .card-header-custom {
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-bottom: 1px solid var(--gray-100);
    }

    .card-header-custom h5 {
        margin: 0;
        font-weight: 700;
        font-size: 1rem;
    }

    .card-header-custom .badge-status {
        margin-left: auto;
        padding: 0.375rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .card-body-custom {
        padding: 1.5rem;
    }

    /* Info Grid */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }

    .info-item {
        background: var(--gray-50);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .info-item:hover {
        background: var(--primary-bg);
        transform: translateX(4px);
    }

    .info-item .label {
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--gray-600);
        margin-bottom: 0.25rem;
    }

    .info-item .value {
        font-weight: 600;
        color: var(--gray-800);
        font-size: 0.9rem;
    }

    .info-item .value code {
        background: var(--gray-200);
        padding: 0.125rem 0.5rem;
        border-radius: 6px;
        font-size: 0.8rem;
    }

    .info-item .value .text-muted {
        color: var(--gray-500);
        font-weight: 400;
    }

    /* Reason Box */
    .reason-box {
        background: var(--gray-50);
        border-radius: 16px;
        padding: 1.25rem;
        border-left: 4px solid var(--primary-green);
        transition: all 0.3s ease;
    }

    .reason-box:hover {
        background: var(--primary-bg);
        transform: translateX(4px);
    }

    .reason-box .label {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--gray-600);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .reason-box .text {
        color: var(--gray-700);
        line-height: 1.6;
        font-size: 0.95rem;
    }

    /* Status Badges */
    .badge-pending {
        background: #fef3c7;
        color: #92400e;
    }
    .badge-approved {
        background: #d1fae5;
        color: #065f46;
    }
    .badge-rejected {
        background: #fee2e2;
        color: #991b1b;
    }
    .badge-unknown {
        background: #f3f4f6;
        color: #6b7280;
    }

    /* Form Cards */
    .form-card {
        border: none;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        transition: transform 0.3s ease;
    }

    .form-card:hover {
        transform: translateY(-2px);
    }

    .form-card .card-header {
        border: none;
        padding: 1rem 1.5rem;
    }

    .form-card .card-header.bg-approve {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .form-card .card-header.bg-reject {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .form-card .card-body {
        padding: 1.5rem;
    }

    /* Buttons */
    .btn-approve {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-approve:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(5,150,105,0.3);
        color: white;
    }

    .btn-reject {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-reject:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220,38,38,0.3);
        color: white;
    }

    .btn-back {
        background: white;
        border: 1px solid var(--gray-200);
        color: var(--gray-700);
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-back:hover {
        background: var(--gray-50);
        border-color: var(--gray-300);
        transform: translateY(-2px);
        color: var(--gray-800);
    }

    /* Form Controls */
    .form-control {
        border: 1px solid var(--gray-200);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(5,150,105,0.1);
        outline: none;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }

    /* Info Alert */
    .info-alert {
        border-radius: 16px;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        border-left: 4px solid #3b82f6;
        background: #eff6ff;
        color: #1e40af;
    }

    .info-alert i {
        font-size: 1.25rem;
        margin-top: 0.125rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-title {
            font-size: 1.5rem;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .card-body-custom {
            padding: 1rem;
        }

        .btn-approve,
        .btn-reject,
        .btn-back {
            width: 100%;
            justify-content: center;
        }

        .d-flex.gap-2 {
            flex-direction: column;
        }
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

    .profile-card,
    .form-card {
        animation: fadeInUp 0.5s ease forwards;
    }

    .profile-card { animation-delay: 0.1s; }
    .form-card:nth-child(1) { animation-delay: 0.2s; }
    .form-card:nth-child(2) { animation-delay: 0.3s; }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Page Title --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h2 class="page-title">Review Teacher Resignation</h2>
                <div class="text-muted small">
                    <i class="fas fa-calendar-alt"></i> {{ now()->format('l, d F Y') }}
                </div>
            </div>

            {{-- Flash Messages --}}
            @if(session('error'))
                <div class="alert alert-danger d-flex align-items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Teacher Information Card --}}
            <div class="card profile-card mb-4">
                <div class="card-header-custom" style="background: linear-gradient(135deg, var(--gray-50), white);">
                    <i class="fas fa-user-circle fa-lg" style="color: var(--primary-green);"></i>
                    <h5>Teacher Information</h5>
                    <span class="badge-status 
                        @if($teacher->resignation_request_status == 'pending') badge-pending
                        @elseif($teacher->resignation_request_status == 'approved') badge-approved
                        @elseif($teacher->resignation_request_status == 'rejected') badge-rejected
                        @else badge-unknown @endif">
                        @if($teacher->resignation_request_status == 'pending')
                            ⏳ Pending
                        @elseif($teacher->resignation_request_status == 'approved')
                            ✅ Approved
                        @elseif($teacher->resignation_request_status == 'rejected')
                            ❌ Rejected
                        @else
                            {{ $teacher->resignation_request_status ?? 'Unknown' }}
                        @endif
                    </span>
                </div>
                <div class="card-body-custom">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="label">👤 Teacher ID</div>
                            <div class="value"><code>{{ $teacher->teacherID ?? 'N/A' }}</code></div>
                        </div>
                        <div class="info-item">
                            <div class="label">📛 Full Name</div>
                            <div class="value">{{ $teacher->teacherName ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="label">🪪 IC Number</div>
                            <div class="value">{{ $teacher->ICNumber ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="label">⚥ Gender</div>
                            <div class="value">{{ $teacher->gender ?? '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="label">🌏 Race</div>
                            <div class="value">{{ $teacher->race ?? '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="label">💍 Marital Status</div>
                            <div class="value">{{ $teacher->maritalStatus ?? '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="label">📞 Phone</div>
                            <div class="value">{{ $teacher->phoneNumber ?? '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="label">✉️ Email</div>
                            <div class="value">{{ $teacher->email ?? '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="label">🏫 School</div>
                            <div class="value">
                                @if(!empty($teacher->schoolName))
                                    {{ $teacher->schoolName }}
                                    @if(!empty($teacher->assignedSchoolID))
                                        <span class="text-muted">(ID: {{ $teacher->assignedSchoolID }})</span>
                                    @endif
                                @else
                                    <span class="text-muted">No school assigned</span>
                                @endif
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="label">📍 Address</div>
                            <div class="value">{{ $teacher->address ?? '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="label">📅 Appointed Date</div>
                            <div class="value">{{ $teacher->appointedDate ? \Carbon\Carbon::parse($teacher->appointedDate)->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="label">📅 Pension Date</div>
                            <div class="value">{{ $teacher->pensionDate ? \Carbon\Carbon::parse($teacher->pensionDate)->format('d/m/Y') : '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Resignation Details Card --}}
            <div class="card profile-card mb-4">
                <div class="card-header-custom" style="background: linear-gradient(135deg, #fef3c7, #fffbeb);">
                    <i class="fas fa-file-alt fa-lg" style="color: #f59e0b;"></i>
                    <h5>Resignation Details</h5>
                </div>
                <div class="card-body-custom">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="label">📅 Resignation Date</div>
                            <div class="value">
                                {{ $teacher->resignation_request_date ? \Carbon\Carbon::parse($teacher->resignation_request_date)->format('d/m/Y h:i A') : 'N/A' }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="label">📊 Status</div>
                            <div class="value">
                                @if($teacher->resignation_request_status == 'pending')
                                    <span class="badge badge-pending">⏳ Pending</span>
                                @elseif($teacher->resignation_request_status == 'approved')
                                    <span class="badge badge-approved">✅ Approved</span>
                                @elseif($teacher->resignation_request_status == 'rejected')
                                    <span class="badge badge-rejected">❌ Rejected</span>
                                @else
                                    <span class="badge badge-unknown">{{ $teacher->resignation_request_status ?? 'Unknown' }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <div class="label">📝 Reason for Resignation</div>
                            <div class="reason-box" style="margin-top: 0.5rem; border-left-color: #f59e0b;">
                                <div class="text">
                                    {{ $teacher->resignation_request_reason ?? 'No reason provided' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Approval Actions --}}
            @if($teacher->resignation_request_status == 'pending')
                <div class="row g-4">
                    {{-- Approve Form --}}
                    <div class="col-md-6">
                        <div class="form-card">
                            <div class="card-header bg-approve">
                                <i class="fas fa-check-circle"></i>
                                <h5 class="mb-0">Approve Resignation</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('hr.resignations.approve', $teacher->teacherID) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-calendar-check"></i> Pension Date (Last Working Day) <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" name="effective_date" class="form-control" required min="{{ date('Y-m-d') }}">
                                        <small class="text-muted">Teacher will be retired on this date</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-comment"></i> Remarks (Optional)
                                        </label>
                                        <textarea name="remarks" class="form-control" rows="3" placeholder="Add any additional remarks..."></textarea>
                                    </div>
                                    <button type="submit" class="btn-approve w-100" onclick="return confirm('Are you sure you want to approve this resignation for {{ $teacher->teacherName }}?')">
                                        <i class="fas fa-check"></i> Approve Resignation
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Reject Form --}}
                    <div class="col-md-6">
                        <div class="form-card">
                            <div class="card-header bg-reject">
                                <i class="fas fa-times-circle"></i>
                                <h5 class="mb-0">Reject Resignation</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('hr.resignations.reject', $teacher->teacherID) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-pen-alt"></i> Rejection Reason <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="rejection_reason" class="form-control" rows="4" required placeholder="Please provide a reason for rejection..."></textarea>
                                    </div>
                                    <button type="submit" class="btn-reject w-100" onclick="return confirm('Are you sure you want to reject this resignation for {{ $teacher->teacherName }}?')">
                                        <i class="fas fa-times"></i> Reject Resignation
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Already Processed --}}
                <div class="card profile-card">
                    <div class="card-header-custom" style="background: linear-gradient(135deg, #eff6ff, #dbeafe);">
                        <i class="fas fa-info-circle fa-lg" style="color: #3b82f6;"></i>
                        <h5>Resignation Status</h5>
                    </div>
                    <div class="card-body-custom">
                        <div class="info-alert">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                This resignation request has already been 
                                <strong>{{ ucfirst($teacher->resignation_request_status) }}</strong>.
                                @if($teacher->resignation_remarks)
                                    <br><strong>Remarks:</strong> {{ $teacher->resignation_remarks }}
                                @endif
                                @if($teacher->resignation_approved_date)
                                    <br><strong>Effective Date:</strong> 
                                    {{ \Carbon\Carbon::parse($teacher->resignation_approved_date)->format('d/m/Y') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Back Button --}}
            <div class="mt-4">
                <a href="{{ route('hr.resignations.pending') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Pending List
                </a>
            </div>

        </div>
    </div>
</div>

<script>
    // Auto-hide flash messages
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
@endsection