@extends('layouts.app')

@section('title', 'Submit Resignation - Staff')

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
        transition: transform 0.3s ease;
    }

    .form-header:hover {
        transform: translateY(-2px);
    }

    .form-header::before {
        content: "👥";
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
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-header p {
        color: rgba(255,255,255,0.8);
        margin: 0;
        font-size: 0.9rem;
    }

    /* Info Card */
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
        transition: all 0.3s ease;
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
        transition: all 0.3s ease;
    }

    .warning-card:hover {
        transform: translateX(5px);
    }

    .warning-icon {
        font-size: 1.5rem;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
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
        transition: all 0.3s ease;
    }

    .form-card:hover {
        box-shadow: 0 15px 50px rgba(0,0,0,0.12);
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
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Character Counter */
    .char-counter {
        text-align: right;
        font-size: 0.7rem;
        margin-top: 0.5rem;
        color: var(--gray-500);
    }

    .char-counter .warning {
        color: #f59e0b;
    }

    .char-counter .danger {
        color: #ef4444;
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
        transition: all 0.3s ease;
        cursor: pointer;
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
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-cancel:hover {
        background: var(--gray-50);
        border-color: var(--gray-300);
        transform: translateY(-2px);
        text-decoration: none;
        color: var(--gray-800);
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

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #dc2626;
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

    /* Process Steps */
    .process-steps {
        background: var(--gray-50);
        border-radius: 20px;
        padding: 1.5rem;
        margin-top: 1.5rem;
    }

    .step-item {
        text-align: center;
        padding: 1rem;
        transition: all 0.3s ease;
    }

    .step-item:hover {
        transform: translateY(-5px);
    }

    .step-icon {
        width: 60px;
        height: 60px;
        background: white;
        border-radius: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.5rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .step-item:hover .step-icon {
        transform: scale(1.1);
        box-shadow: 0 8px 20px rgba(5,150,105,0.15);
    }

    .step-number {
        font-size: 0.7rem;
        color: var(--gray-500);
        margin-bottom: 0.25rem;
    }

    .step-title {
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--gray-800);
        margin-bottom: 0.25rem;
    }

    .step-desc {
        font-size: 0.7rem;
        color: var(--gray-500);
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

        .form-card-header {
            padding: 1rem;
        }

        .form-card-body {
            padding: 1rem;
        }

        .btn-submit, .btn-cancel {
            flex: 1;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="form-container">
        {{-- Header --}}
        <div class="form-header">
            <h1>
                <i class="fas fa-file-signature"></i>
                Submit Resignation Application
            </h1>
            <p>Please fill out the form below to initiate your resignation process</p>
        </div>

        {{-- Error Alert --}}
        @if(session('error'))
            <div class="alert-custom alert-danger">
                <i class="fas fa-exclamation-circle fa-lg"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        {{-- Staff Information Card --}}
        <div class="info-card">
            <div class="info-title">
                <i class="fas fa-user-circle"></i>
                STAFF INFORMATION
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div>
                        <div class="info-label">Staff ID</div>
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
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <div class="info-label">Department</div>
                        <div class="info-value">{{ $staff->department ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Warning Card --}}
        <div class="warning-card">
            <div class="warning-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="warning-content">
                <h4>Important Notes Before Submitting</h4>
                <p>Your resignation will be reviewed by HR. Please ensure you have completed all handover tasks and settled any pending responsibilities before submitting.</p>
            </div>
        </div>

        {{-- Resignation Form --}}
        <div class="form-card">
            <div class="form-card-header">
                <h3>
                    <i class="fas fa-file-alt"></i>
                    Resignation Details
                </h3>
            </div>
            <div class="form-card-body">
                <form method="POST" action="{{ route('staff.resignations.store') }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar-day"></i>
                            Resignation Date
                            <span class="required">*</span>
                        </label>
                        <input type="date" name="resignation_date" 
                               class="form-control @error('resignation_date') is-invalid @enderror" 
                               value="{{ old('resignation_date') }}" 
                               required>
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i> The date you are submitting this resignation
                        </div>
                        @error('resignation_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-pen-alt"></i>
                            Reason for Resignation
                            <span class="required">*</span>
                        </label>
                        <textarea name="reason" 
                                  class="form-control @error('reason') is-invalid @enderror" 
                                  rows="6" 
                                  required 
                                  placeholder="Please provide a detailed reason for your resignation. This will help us understand your situation better..."
                                  maxlength="1000">{{ old('reason') }}</textarea>
                        <div class="char-counter">
                            <span id="charCount">0</span> / 1000 characters
                        </div>
                        <div class="form-text">
                            <i class="fas fa-lightbulb"></i> Be specific and professional in your explanation
                        </div>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('staff.resignations.index') }}" class="btn-cancel">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane"></i> Submit Application
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Process Steps --}}
        <div class="process-steps">
            <div class="info-title" style="margin-bottom: 1rem;">
                <i class="fas fa-clock"></i>
                WHAT HAPPENS NEXT?
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="step-item">
                        <div class="step-icon">
                            <i class="fas fa-envelope-open-text" style="color: #059669;"></i>
                        </div>
                        <div class="step-number">Step 1</div>
                        <div class="step-title">Submit Application</div>
                        <div class="step-desc">Your application is submitted to HR</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-item">
                        <div class="step-icon">
                            <i class="fas fa-clipboard-list" style="color: #f59e0b;"></i>
                        </div>
                        <div class="step-number">Step 2</div>
                        <div class="step-title">HR Review</div>
                        <div class="step-desc">HR will review your resignation</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-item">
                        <div class="step-icon">
                            <i class="fas fa-check-circle" style="color: #10b981;"></i>
                        </div>
                        <div class="step-number">Step 3</div>
                        <div class="step-title">Final Decision</div>
                        <div class="step-desc">You'll receive HR's decision</div>
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
        // Initial count
        charCountSpan.textContent = textarea.value.length;
        
        textarea.addEventListener('input', function() {
            const count = this.value.length;
            charCountSpan.textContent = count;
            
            // Change color based on character count
            if (count >= 900 && count < 990) {
                charCountSpan.classList.add('warning');
                charCountSpan.classList.remove('danger');
            } else if (count >= 990) {
                charCountSpan.classList.add('danger');
                charCountSpan.classList.remove('warning');
            } else {
                charCountSpan.classList.remove('warning', 'danger');
            }
        });
    }
</script>
@endsection