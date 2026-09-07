@extends('layouts.app')

@section('title', 'Submit Resignation - Principal')

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

    .form-container {
        max-width: 900px;
        margin: 0 auto;
    }

    /* Header Section */
    .form-header {
        background: linear-gradient(135deg, #1e3d2c 0%, #2e5a42 100%);
        border-radius: 24px;
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }

    .form-header::before {
        content: "👑";
        position: absolute;
        right: -20px;
        top: -20px;
        font-size: 100px;
        opacity: 0.08;
        pointer-events: none;
    }

    .form-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin-bottom: 0.5rem;
    }

    .form-header p {
        color: rgba(255,255,255,0.8);
        margin: 0;
        font-size: 0.9rem;
    }

    /* Info Cards */
    .info-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: 1px solid var(--gray-100);
        transition: all 0.3s ease;
    }

    .info-card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .info-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--gray-600);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-title i {
        color: var(--primary-green);
        font-size: 1rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: var(--gray-50);
        border-radius: 16px;
        transition: all 0.2s ease;
    }

    .info-item:hover {
        background: var(--primary-bg);
        transform: translateX(5px);
    }

    .info-icon {
        width: 40px;
        height: 40px;
        background: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .info-label {
        font-size: 0.7rem;
        color: var(--gray-600);
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-weight: 700;
        color: var(--gray-800);
        font-size: 0.9rem;
    }

    /* Warning Card */
    .warning-card {
        background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
        border-radius: 16px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #f59e0b;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }

    .warning-icon {
        font-size: 1.5rem;
    }

    .warning-content h4 {
        font-size: 0.9rem;
        font-weight: 700;
        color: #92400e;
        margin-bottom: 0.25rem;
    }

    .warning-content p {
        font-size: 0.8rem;
        color: #78350f;
        margin: 0;
    }

    /* Form Card */
    .form-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .form-card-header {
        background: linear-gradient(135deg, var(--gray-50), white);
        padding: 1.25rem 1.5rem;
        border-bottom: 2px solid var(--gray-100);
    }

    .form-card-header h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--gray-800);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-card-header h3 i {
        color: var(--primary-green);
    }

    .form-card-body {
        padding: 1.5rem;
    }

    /* Form Elements */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label i {
        color: var(--primary-green);
        font-size: 0.8rem;
    }

    .form-label .required {
        color: #ef4444;
        font-size: 0.7rem;
    }

    .form-control {
        border: 2px solid var(--gray-200);
        border-radius: 14px;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 4px rgba(5,150,105,0.1);
        outline: none;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }

    .form-text {
        font-size: 0.7rem;
        color: var(--gray-500);
        margin-top: 0.25rem;
    }

    /* Button Styles */
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
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(5,150,105,0.3);
    }

    .btn-cancel {
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

    .btn-cancel:hover {
        background: var(--gray-50);
        border-color: var(--gray-300);
        transform: translateY(-2px);
        text-decoration: none;
        color: var(--gray-800);
    }

    /* Character Counter */
    .char-counter {
        text-align: right;
        font-size: 0.7rem;
        color: var(--gray-500);
        margin-top: 0.25rem;
    }

    .char-counter .warning {
        color: #f59e0b;
    }

    .char-counter .danger {
        color: #ef4444;
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
    .delay-4 { animation-delay: 0.4s; }

    /* Alert Styles */
    .alert-custom {
        border-radius: 16px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        border: none;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #dc2626;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-header {
            padding: 1.25rem;
        }

        .form-header h1 {
            font-size: 1.25rem;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .form-card-body {
            padding: 1rem;
        }

        .btn-submit, .btn-cancel {
            padding: 0.75rem 1.5rem;
            font-size: 0.8rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="form-container">
        {{-- Header --}}
        <div class="form-header animate-in">
            <h1>Submit Resignation Application</h1>
            <p>Please fill out the form below to initiate your resignation process</p>
        </div>

        {{-- Error Alert --}}
        @if(session('error'))
            <div class="alert-custom alert-danger animate-in">
                <i class="fas fa-exclamation-circle fa-lg"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        {{-- Principal Information Card --}}
        <div class="info-card animate-in delay-1">
            <div class="info-title">
                <i class="fas fa-user-circle"></i>
                PRINCIPAL INFORMATION
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div>
                        <div class="info-label">Principal ID</div>
                        <div class="info-value">{{ session('userID') }}</div>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ session('userName') }}</div>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-school"></i>
                    </div>
                    <div>
                        <div class="info-label">School</div>
                        <div class="info-value">{{ session('schoolID') ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Warning Card --}}
        <div class="warning-card animate-in delay-2">
            <div class="warning-icon">
                <i class="fas fa-exclamation-triangle fa-lg"></i>
            </div>
            <div class="warning-content">
                <h4>Important Notes Before Submitting</h4>
                <p>Your resignation will be reviewed by HR. Please ensure you have completed all handover tasks and settled any pending responsibilities before submitting.</p>
            </div>
        </div>

        {{-- Resignation Form --}}
        <div class="form-card animate-in delay-3">
            <div class="form-card-header">
                <h3>
                    <i class="fas fa-file-alt"></i>
                    Resignation Details
                </h3>
            </div>
            <div class="form-card-body">
                <form method="POST" action="{{ route('principal.resignations.store') }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar-day"></i>
                            Resignation Date
                            <span class="required">*</span>
                        </label>
                        <input type="date" name="resignation_date" class="form-control" required>
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i> The date you are submitting this resignation
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-pen-alt"></i>
                            Reason for Resignation
                            <span class="required">*</span>
                        </label>
                        <textarea name="reason" class="form-control" rows="6" required 
                                  placeholder="Please provide a detailed reason for your resignation. This will help us understand your situation better..." 
                                  maxlength="1000"></textarea>
                        <div class="char-counter">
                            <span id="charCount">0</span> / 1000 characters
                        </div>
                        <div class="form-text">
                            <i class="fas fa-lightbulb"></i> Be specific and professional in your explanation
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('principal.resignations.index') }}" class="btn-cancel">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane"></i> Submit Application
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{<!-- Additional Info Card -->
        <div class="info-card animate-in delay-4" style="margin-top: 1.5rem; background: var(--gray-50);">
            <div class="info-title">
                <i class="fas fa-clock"></i>
                WHAT HAPPENS NEXT?
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-center p-3">
                        <div class="mb-2">
                            <i class="fas fa-envelope-open-text fa-2x" style="color: #059669;"></i>
                        </div>
                        <div class="fw-bold mb-1">Step 1: Submit</div>
                        <div class="small text-muted">Your application is submitted to HR</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3">
                        <div class="mb-2">
                            <i class="fas fa-clipboard-list fa-2x" style="color: #f59e0b;"></i>
                        </div>
                        <div class="fw-bold mb-1">Step 2: Review</div>
                        <div class="small text-muted">HR will review your resignation</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3">
                        <div class="mb-2">
                            <i class="fas fa-check-circle fa-2x" style="color: #10b981;"></i>
                        </div>
                        <div class="fw-bold mb-1">Step 3: Decision</div>
                        <div class="small text-muted">You'll receive HR's decision</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Character counter for reason textarea
    const textarea = document.querySelector('textarea[name="reason"]');
    const charCountSpan = document.getElementById('charCount');
    
    if (textarea && charCountSpan) {
        textarea.addEventListener('input', function() {
            const count = this.value.length;
            charCountSpan.textContent = count;
            
            if (count >= 900) {
                charCountSpan.classList.add('warning');
            } else if (count >= 990) {
                charCountSpan.classList.add('danger');
            } else {
                charCountSpan.classList.remove('warning', 'danger');
            }
        });
        
        // Trigger on load
        charCountSpan.textContent = textarea.value.length;
    }
</script>
@endsection