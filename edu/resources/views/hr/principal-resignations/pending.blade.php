@extends('layouts.app')

@section('title', 'Pending Principal Resignations')

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
        content: "⏳";
        font-size: 2rem;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        text-align: center;
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
    }

    .stat-card.pending::before { background: #f59e0b; }
    .stat-card.approved::before { background: #10b981; }
    .stat-card.rejected::before { background: #ef4444; }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1.2;
    }

    .stat-label {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--gray-600);
        margin-top: 0.5rem;
    }

    /* Main Card */
    .main-card {
        border: none;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .card-header-custom {
        background: linear-gradient(135deg, var(--gray-50), white);
        border-bottom: 1px solid var(--gray-100);
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .card-header-custom h5 {
        margin: 0;
        font-weight: 700;
        font-size: 1rem;
        color: var(--gray-800);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .pending-badge {
        background: #fef3c7;
        color: #92400e;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .pending-badge::before {
        content: "⏳";
        font-size: 0.65rem;
    }

    /* Table Styles */
    .resignation-table {
        font-size: 0.875rem;
    }

    .resignation-table thead th {
        background: var(--gray-50);
        padding: 1rem;
        font-weight: 600;
        color: var(--gray-700);
        border-bottom: 2px solid var(--gray-200);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .resignation-table tbody td {
        padding: 1rem;
        vertical-align: middle;
        color: var(--gray-700);
        border-bottom: 1px solid var(--gray-100);
    }

    .resignation-table tbody tr {
        transition: background 0.2s ease;
        cursor: pointer;
    }

    .resignation-table tbody tr:hover {
        background: var(--primary-bg);
    }

    /* Reason Cell */
    .reason-cell {
        max-width: 250px;
        white-space: normal;
        word-wrap: break-word;
        line-height: 1.4;
    }

    /* Review Button */
    .btn-review {
        background: var(--gray-100);
        color: var(--gray-700);
        padding: 0.375rem 0.875rem;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 600;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .btn-review:hover {
        background: var(--primary-green);
        color: white;
        transform: translateY(-1px);
    }

    /* Back Button */
    .btn-back {
        background: white;
        border: 1px solid var(--gray-200);
        color: var(--gray-700);
        padding: 0.625rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-back:hover {
        background: var(--gray-50);
        border-color: var(--gray-300);
        transform: translateY(-1px);
        color: var(--gray-800);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem;
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state h5 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: var(--gray-500);
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-title {
            font-size: 1.5rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .stat-number {
            font-size: 1.5rem;
        }

        .resignation-table {
            font-size: 0.75rem;
        }

        .resignation-table td,
        .resignation-table th {
            padding: 0.5rem;
        }

        .reason-cell {
            max-width: 150px;
        }

        .card-header-custom {
            flex-direction: column;
            text-align: center;
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

    .stat-card {
        animation: fadeInUp 0.5s ease forwards;
        opacity: 0;
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    
    {{-- Page Title --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h2 class="page-title">Pending Principal Resignation Requests</h2>
        <div class="text-muted small">
            <i class="fas fa-calendar-alt"></i> {{ now()->format('l, d F Y') }}
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="stats-grid">
        <div class="stat-card pending">
            <div class="stat-number" style="color: #f59e0b;">{{ $stats['pending'] ?? 0 }}</div>
            <div class="stat-label">Pending Review</div>
        </div>
        <div class="stat-card approved">
            <div class="stat-number" style="color: #10b981;">{{ $stats['approved'] ?? 0 }}</div>
            <div class="stat-label">Approved</div>
        </div>
        <div class="stat-card rejected">
            <div class="stat-number" style="color: #ef4444;">{{ $stats['rejected'] ?? 0 }}</div>
            <div class="stat-label">Rejected</div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="card main-card shadow-sm">
        <div class="card-header-custom">
            <h5>
                <i class="fas fa-clock"></i> Principal Resignation Requests Awaiting Review
            </h5>
            <span class="pending-badge">
                <i class="fas fa-hourglass-half"></i> {{ isset($principals) ? $principals->count() : 0 }} Pending
            </span>
        </div>
        <div class="card-body p-0">
            @if(isset($principals) && $principals->count() > 0)
                <div class="table-responsive">
                    <table class="table resignation-table mb-0">
                        <thead>
                            <tr>
                                <th>Principal ID</th>
                                <th>Name</th>
                                <th>School</th>
                                <th>Resignation Date</th>
                                <th>Reason</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($principals as $principal)
                            <tr>
                                <td><code>{{ $principal->principalID }}</code></td>
                                <td class="fw-semibold">{{ $principal->principalName }}</td>
                                <td>
                                    @if($principal->schoolName)
                                        <i class="fas fa-school text-muted me-1"></i> {{ $principal->schoolName }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <i class="fas fa-calendar-alt text-muted me-1"></i>
                                    {{ \Carbon\Carbon::parse($principal->resignation_request_date)->format('d/m/Y') }}
                                </td>
                                <td class="reason-cell">
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $principal->resignation_request_reason }}">
                                        <i class="fas fa-quote-left text-muted me-1"></i>
                                        {{ Str::limit($principal->resignation_request_reason, 60) }}
                                    </div>
                                </div>
                                <td>
                                    <a href="{{ route('hr.principal-resignations.show', $principal->principalID) }}" 
                                       class="btn-review">
                                        <i class="fas fa-eye"></i> Review
                                    </a>
                                </div>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">🎉</div>
                    <h5>No Pending Requests</h5>
                    <p>All principal resignation requests have been reviewed. Great job!</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Back Button --}}
    <div class="mt-3">
        <a href="{{ route('hr.principal-resignations.all') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> View All Requests
        </a>
    </div>
</div>

<script>
    // Add row click functionality
    document.querySelectorAll('.resignation-table tbody tr').forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't trigger if clicking on the action button
            if (e.target.closest('.btn-review')) return;
            
            const reviewLink = this.querySelector('.btn-review');
            if (reviewLink) {
                window.location.href = reviewLink.href;
            }
        });
    });

    // Auto-hide any flash messages
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