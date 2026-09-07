@extends('layouts.app')

@section('title', 'Resigned Teachers - ' . ($school->schoolName ?? 'My School'))

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

    /* School Info Card */
    .school-info-card {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-dark));
        border: none;
        border-radius: 20px;
        color: white;
        margin-bottom: 1.5rem;
        padding: 1rem 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .school-info-card::before {
        content: "🏫";
        position: absolute;
        right: -20px;
        top: -20px;
        font-size: 80px;
        opacity: 0.1;
        pointer-events: none;
    }

    /* Main Card */
    .main-card {
        border: none;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .card-header-custom {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-dark));
        padding: 1.25rem 1.5rem;
        border-bottom: none;
    }

    .card-header-custom h4 {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
        color: white;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-header-custom p {
        color: rgba(255,255,255,0.8);
        margin: 0.5rem 0 0 0;
        font-size: 0.875rem;
    }

    /* Stat Card */
    .stat-card {
        border: none;
        border-radius: 20px;
        transition: all 0.3s ease;
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
        background: linear-gradient(90deg, #ef4444, #f97316);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }

    .stat-card .card-body {
        padding: 1.25rem;
    }

    .stat-label {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--gray-600);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 800;
        color: var(--gray-800);
        line-height: 1.2;
    }

    /* Search Section */
    .search-section {
        background: var(--gray-50);
        border-radius: 16px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .search-input {
        border: 1px solid var(--gray-200);
        border-radius: 12px;
        padding: 0.625rem 1rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .search-input:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(5,150,105,0.1);
        outline: none;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-dark));
        color: white;
        border: none;
        padding: 0.625rem 1.25rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-primary-custom:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(5,150,105,0.3);
        color: white;
    }

    .btn-secondary-custom {
        background: white;
        border: 1px solid var(--gray-200);
        color: var(--gray-700);
        padding: 0.625rem 1.25rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-secondary-custom:hover {
        background: var(--gray-50);
        border-color: var(--gray-300);
        color: var(--gray-800);
    }

    .btn-light-custom {
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 600;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-light-custom:hover {
        background: rgba(255,255,255,0.3);
        color: white;
    }

    /* Table Styles */
    .teachers-table {
        font-size: 0.875rem;
    }

    .teachers-table thead th {
        background: var(--gray-50);
        padding: 1rem;
        font-weight: 600;
        color: var(--gray-700);
        border-bottom: 2px solid var(--gray-200);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .teachers-table tbody td {
        padding: 0.875rem 1rem;
        vertical-align: middle;
        color: var(--gray-700);
        border-bottom: 1px solid var(--gray-100);
    }

    .teachers-table tbody tr:hover {
        background: var(--primary-bg);
    }

    /* Badges */
    .badge-resigned {
        background: #fee2e2;
        color: #dc2626;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
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

    /* Pagination Styles - Matching your image */
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid var(--gray-200);
    }

    .pagination-info {
        color: var(--gray-600);
        font-size: 0.875rem;
    }

    .pagination-info i {
        margin-right: 0.25rem;
    }

    .pagination {
        display: flex;
        gap: 0.25rem;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .pagination .page-item {
        display: inline-block;
    }

    .pagination .page-item .page-link {
        border-radius: 8px;
        border: 1px solid var(--gray-200);
        color: var(--gray-700);
        padding: 0.5rem 0.875rem;
        font-size: 0.875rem;
        text-decoration: none;
        transition: all 0.2s ease;
        background: white;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        min-width: 36px;
        justify-content: center;
    }

    .pagination .page-item .page-link:hover {
        background: var(--primary-bg);
        border-color: var(--primary-green);
        color: var(--primary-green);
    }

    .pagination .page-item.active .page-link {
        background: var(--primary-green);
        border-color: var(--primary-green);
        color: white;
        font-weight: 600;
    }

    .pagination .page-item.disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
        padding: 0.5rem 1rem;
    }

    /* Responsive Pagination */
    @media (max-width: 768px) {
        .pagination-wrapper {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .pagination .page-item .page-link {
            padding: 0.375rem 0.625rem;
            font-size: 0.75rem;
            min-width: 30px;
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .card-header-custom {
            padding: 1rem;
        }
        
        .card-header-custom h4 {
            font-size: 1rem;
        }
        
        .stat-number {
            font-size: 1.5rem;
        }
        
        .teachers-table {
            font-size: 0.75rem;
        }
        
        .teachers-table td,
        .teachers-table th {
            padding: 0.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

    {{-- School Info Card --}}
    <div class="school-info-card">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $school->schoolName ?? 'Your School' }}</strong> - Resigned Teachers Management
            </div>
            <a href="{{ route('principal.teachers.list') }}" class="btn-light-custom">
                <i class="fas fa-arrow-left"></i> Active Teachers
            </a>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="card main-card shadow-sm mb-4">
        <div class="card-header-custom">
            <h4>
                <i class="fas fa-user-graduate"></i> Resigned Teachers
            </h4>
            <p>Teachers who have resigned from {{ $school->schoolName ?? 'your school' }}</p>
        </div>
        
        <div class="card-body p-4">
            
            {{-- Statistics Card --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-label">Total Resigned Teachers</div>
                                    <div class="stat-number">{{ $stats['total_resigned'] ?? 0 }}</div>
                                </div>
                                <div>
                                    <i class="fas fa-users fa-3x" style="color: var(--gray-300);"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Search Section --}}
            <div class="search-section">
                <form method="GET" action="{{ route('principal.resigned') }}">
                    <div class="d-flex gap-2">
                        <input type="text" name="search" class="form-control search-input" 
                               placeholder="Search by name, ID or IC number..." 
                               value="{{ request('search') }}">
                        <button class="btn-primary-custom" type="submit">
                            <i class="fas fa-search"></i> Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('principal.resigned') }}" class="btn-secondary-custom">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>
            
            {{-- Resigned Teachers Table --}}
            <div class="table-responsive">
                <table class="table teachers-table">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="10%">Teacher ID</th>
                            <th width="20%">Name</th>
                            <th width="15%">IC Number</th>
                            <th width="15%">Phone</th>
                            <th width="25%">Email</th>
                            <th width="10%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($resignedTeachers) && count($resignedTeachers) > 0)
                            @forelse($resignedTeachers as $index => $teacher)
                            <tr>
                                <td class="text-center">{{ $resignedTeachers->firstItem() + $index }}</td>
                                <td><code>{{ $teacher->teacherID }}</code></td>
                                <td><strong>{{ $teacher->teacherName }}</strong></td>
                                <td>{{ $teacher->ICNumber ?? '-' }}</td>
                                <td>{{ $teacher->phoneNumber ?? '-' }}</td>
                                <td>{{ $teacher->email ?? '-' }}</td>
                                <td>
                                    <span class="badge-resigned">
                                        <i class="fas fa-ban"></i> Resigned
                                    </span>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="empty-state">
                                            <div class="empty-state-icon">👨‍🏫</div>
                                            <h5>No resigned teachers found!</h5>
                                            <p>Try adjusting your search criteria.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">🎉</div>
                                        <h5>No resigned teachers found!</h5>
                                        <p>All teachers are currently active in your school.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination with Info --}}
            @if(isset($resignedTeachers) && $resignedTeachers->total() > 0)
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        <i class="fas fa-info-circle"></i>
                        Showing {{ $resignedTeachers->firstItem() ?? 0 }} to {{ $resignedTeachers->lastItem() ?? 0 }} 
                        of {{ $resignedTeachers->total() }} results
                    </div>
                    
                    @if($resignedTeachers->hasPages())
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                {{-- Previous Page Link --}}
                                @if($resignedTeachers->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-left"></i> Previous
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $resignedTeachers->previousPageUrl() }}" rel="prev">
                                            <i class="fas fa-chevron-left"></i> Previous
                                        </a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach($resignedTeachers->getUrlRange(1, $resignedTeachers->lastPage()) as $page => $url)
                                    @if($page == $resignedTeachers->currentPage())
                                        <li class="page-item active" aria-current="page">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if($resignedTeachers->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $resignedTeachers->nextPageUrl() }}" rel="next">
                                            Next <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            Next <i class="fas fa-chevron-right"></i>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Font Awesome 6 Free CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js" crossorigin="anonymous"></script>

<script>
$(document).ready(function() {
    // Auto-hide any flash messages after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endsection