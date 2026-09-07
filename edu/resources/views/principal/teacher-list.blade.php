@extends('layouts.app')

@section('title', 'Teacher List')

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

    body {
        background: linear-gradient(135deg, #eef9f2 0%, #e0f2e9 100%);
        font-family: 'Inter', 'Poppins', system-ui, -apple-system, sans-serif;
        min-height: 100vh;
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
        content: "👨‍🏫";
        font-size: 2rem;
    }

    .top-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.75rem;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .btn-group-custom {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .btn-custom {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-dark));
        color: white;
        border: none;
        padding: 0.625rem 1.5rem;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-custom:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(5,150,105,0.3);
        background: linear-gradient(135deg, var(--primary-dark), #065f46);
    }

    .btn-light-custom {
        background: white;
        border: 1px solid var(--gray-200);
        color: var(--gray-700);
        padding: 0.625rem 1.5rem;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-light-custom:hover {
        background: var(--primary-soft);
        border-color: var(--primary-light);
        color: var(--primary-dark);
        transform: translateY(-1px);
    }

    .search-box {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }

    .search-input {
        width: 320px;
        border: 1px solid var(--gray-200);
        border-radius: 12px;
        padding: 0.625rem 1rem;
        background: white;
        outline: none;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .search-input:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(5,150,105,0.1);
    }

    .alert-success {
        background: var(--primary-soft);
        color: var(--primary-dark);
        padding: 1rem 1.25rem;
        border-radius: 16px;
        margin-bottom: 1.5rem;
        border-left: 4px solid var(--primary-green);
        font-weight: 500;
        animation: slideIn 0.3s ease;
    }

    .alert-error {
        background: #fee2e2;
        color: #dc2626;
        padding: 1rem 1.25rem;
        border-radius: 16px;
        margin-bottom: 1.5rem;
        border-left: 4px solid #dc2626;
        font-weight: 500;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .school-section {
        margin-bottom: 2rem;
    }

    .school-header {
        background: linear-gradient(135deg, #1e3d2c, #2e5a42);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 16px 16px 0 0;
        font-weight: 700;
        font-size: 1rem;
        letter-spacing: 0.01em;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .school-header::before {
        content: "🏫";
        font-size: 1.25rem;
    }

    .table-wrapper {
        background: white;
        border-radius: 0 0 16px 16px;
        overflow-x: auto;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .table thead th {
        background: var(--gray-50);
        padding: 1rem;
        font-weight: 600;
        color: var(--gray-700);
        border-bottom: 2px solid var(--gray-200);
        text-align: left;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .table tbody td {
        padding: 1rem;
        border-bottom: 1px solid var(--gray-100);
        color: var(--gray-600);
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background: var(--primary-bg);
        transition: background 0.2s ease;
    }

    .teacher-name {
        font-weight: 700;
        color: var(--gray-800);
    }

    .status-active {
        background: #d1fae5;
        color: #065f46;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .status-active::before {
        content: "●";
        font-size: 0.5rem;
        animation: pulse 2s infinite;
    }

    .status-stop {
        background: #fee2e2;
        color: #dc2626;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .status-stop::before {
        content: "●";
        font-size: 0.5rem;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
    }

    /* ============================================ */
    /* MODAL STYLES - FIXED WORKING VERSION */
    /* ============================================ */
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        z-index: 99999;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .modal.show {
        display: flex !important;
    }

    .modal-box {
        background: white;
        width: 650px;
        max-width: 95%;
        max-height: 90vh;
        overflow-y: auto;
        border-radius: 24px;
        animation: modalSlideIn 0.3s ease;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(-10px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .modal-header {
        background: linear-gradient(135deg, var(--primary-green), var(--primary-dark));
        padding: 1.25rem 1.5rem;
        border-radius: 24px 24px 0 0;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modal-title::before {
        content: "📝";
        font-size: 1.25rem;
    }

    .modal-close-btn {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        font-size: 1.5rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease;
    }

    .modal-close-btn:hover {
        background: rgba(255,255,255,0.4);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .form-control-custom {
        width: 100%;
        border: 1px solid var(--gray-200);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        outline: none;
        font-family: inherit;
        box-sizing: border-box;
    }

    .form-control-custom:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(5,150,105,0.1);
    }

    textarea.form-control-custom {
        resize: vertical;
        min-height: 80px;
    }

    select.form-control-custom {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%234b5563'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1.25rem;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--gray-600);
        margin-bottom: 0.5rem;
        margin-top: 0.5rem;
        display: block;
    }

    .form-label:first-of-type {
        margin-top: 0;
    }

    .modal-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid var(--gray-200);
        flex-wrap: wrap;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        background: white;
        border-radius: 16px;
        color: var(--gray-400);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .top-actions {
            flex-direction: column;
            align-items: stretch;
        }
        
        .search-box {
            width: 100%;
        }
        
        .search-input {
            flex: 1;
            width: auto;
        }
        
        .btn-group-custom {
            justify-content: center;
        }
        
        .table-wrapper {
            overflow-x: auto;
        }
        
        .table {
            min-width: 800px;
        }
        
        .modal-body {
            padding: 1rem;
        }
        
        .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .modal-box {
            width: 100%;
            max-width: 100%;
        }
    }
</style>
@endsection

@section('content')

<div class="container py-4">
    
    <h2 class="page-title">Teacher List</h2>

    <div class="top-actions">
        <div class="btn-group-custom">
            <button class="btn-custom" onclick="openModal('registerModal')">
                ➕ Register Teacher
            </button>

            <button class="btn-light-custom" onclick="openModal('csvModal')">
                📂 Import CSV
            </button>

            <a href="{{ route('teachers.template') }}" class="btn-light-custom">
                📥 Download Template
            </a>
        </div>

        <form method="GET" class="search-box">
            <input type="text"
                   name="search"
                   class="search-input"
                   placeholder="Search teacher by name or ID..."
                   value="{{ request('search') }}">
            <button type="submit" class="btn-custom">🔍 Search</button>
        </form>
    </div>

    @if(session('status'))
    <div class="alert-success">
        ✨ {{ session('status') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert-error">
        ⚠️ {{ session('error') }}
    </div>
    @endif

    @if(count($teachers) > 0)
        @foreach($teachers as $schoolID => $group)
        <div class="school-section">
            <div class="school-header">
                {{ $group->first()->schoolName ?? $schoolID }}
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>IC Number</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group as $teacher)
                        <tr>
                            <td>{{ $teacher->teacherID }}</td>
                            <td class="teacher-name">{{ $teacher->teacherName }}</td>
                            <td>{{ $teacher->ICNumber ?? '-' }}</td>
                            <td>{{ $teacher->phoneNumber ?? '-' }}</td>
                            <td>{{ $teacher->email ?? '-' }}</td>
                            <td>
                                @php
                                    $status = $teacher->status ?? 'Aktif';
                                    $badgeClass = 'status-active';
                                    $statusText = 'Active';
                                    if ($status == 'Berhenti' || $status == 'Inactive' || $status == 'inactive') {
                                        $badgeClass = 'status-stop';
                                        $statusText = 'Inactive';
                                    } elseif ($status == 'Aktif' || $status == 'Active' || $status == 'active' || $status == null) {
                                        $badgeClass = 'status-active';
                                        $statusText = 'Active';
                                    }
                                @endphp
                                <span class="{{ $badgeClass }}">{{ $statusText }}</span>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="{{ route('teachers.show', $teacher->teacherID) }}"
                                       class="btn-light-custom"
                                       style="padding:0.5rem 1rem; font-size:0.75rem;">
                                        📋 Details
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
        
        @if(isset($teachers) && method_exists($teachers, 'links'))
        <div class="pagination-container">
            {{ $teachers->links() }}
        </div>
        @endif
    @else
        <div class="empty-state">
            <p>No teachers found.</p>
            @if(request('search'))
                <p style="margin-top: 0.5rem;">Try clearing your search filter.</p>
            @endif
        </div>
    @endif
</div>

{{-- ============================================ --}}
{{-- REGISTER MODAL --}}
{{-- ============================================ --}}
<div id="registerModal" class="modal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Register New Teacher</div>
            <button type="button" class="modal-close-btn" onclick="closeModal('registerModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('teachers.store') }}">
                @csrf
                
                <div class="form-label">🆔 Basic Information</div>
                <input name="teacherID" placeholder="Teacher ID" class="form-control-custom" required>
                <input name="teacherName" placeholder="Teacher Name" class="form-control-custom" required>
                <input name="ICNumber" placeholder="IC Number" class="form-control-custom" required>
                
                <div class="form-label">📞 Contact Information</div>
                <input name="phoneNumber" placeholder="Phone Number" class="form-control-custom">
                <input type="email" name="email" placeholder="Email" class="form-control-custom">
                <textarea name="address" placeholder="Address" class="form-control-custom"></textarea>
                
                <div class="form-label">👤 Personal Details</div>
                <div class="form-row">
                    <select name="maritalStatus" class="form-control-custom">
                        <option value="">Marital Status</option>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Divorced">Divorced</option>
                        <option value="Widowed">Widowed</option>
                    </select>

                    <select name="gender" class="form-control-custom">
                        <option value="">Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>

                <select name="race" id="raceSelect" class="form-control-custom" onchange="toggleOtherRace()">
                    <option value="">Select Race</option>
                    <option value="Malay">Malay</option>
                    <option value="Chinese">Chinese</option>
                    <option value="Indian">Indian</option>
                    <option value="Other">Other</option>
                </select>

                <input type="text" name="otherRace" id="otherRaceInput" placeholder="Please specify race" class="form-control-custom" style="display:none;">

                <div class="form-label">📅 Employment Dates</div>
                <div class="form-row">
                    <input type="date" name="appointedDate" class="form-control-custom">
                    <input type="date" name="pensionDate" class="form-control-custom">
                </div>
                <div class="form-row">
                    <input type="number" name="latestAge" placeholder="Latest Age" class="form-control-custom">
                </div>

                <div class="form-label">🏫 School Assignment</div>
                <select name="schoolID" class="form-control-custom" required>
                    <option value="">Select School</option>
                    @foreach($schools ?? [] as $school)
                        <option value="{{ $school->schoolID }}">
                            {{ $school->schoolName }}
                        </option>
                    @endforeach
                </select>

                <div class="modal-actions">
                    <button type="submit" class="btn-custom">💾 Save Teacher</button>
                    <button type="button" class="btn-light-custom" onclick="closeModal('registerModal')">❌ Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- CSV MODAL --}}
{{-- ============================================ --}}
<div id="csvModal" class="modal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Import Teacher CSV</div>
            <button type="button" class="modal-close-btn" onclick="closeModal('csvModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('teachers.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-label">📄 Select CSV File</div>
                <input type="file" name="csvfile" class="form-control-custom" accept=".csv,.xlsx" required>
                <div style="font-size:0.75rem; color:var(--gray-500); margin-top:-0.5rem; margin-bottom:1rem;">
                    Accepted formats: .csv, .xlsx
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn-custom">📤 Upload & Import</button>
                    <button type="button" class="btn-light-custom" onclick="closeModal('csvModal')">❌ Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // ============================================
    // MODAL FUNCTIONS - FIXED
    // ============================================
    
    function openModal(id) { 
        var modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('show');
            modal.style.display = 'flex';
            document.body.style.overflow = "hidden";
        }
    }
    
    function closeModal(id) { 
        var modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
            document.body.style.overflow = "";
        }
    }

    function toggleOtherRace() {
        var race = document.getElementById('raceSelect');
        var otherInput = document.getElementById('otherRaceInput');
        if (race && otherInput) {
            if(race.value === 'Other') {
                otherInput.style.display = 'block';
                otherInput.required = true;
            } else {
                otherInput.style.display = 'none';
                otherInput.required = false;
                otherInput.value = '';
            }
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        var registerModal = document.getElementById('registerModal');
        var csvModal = document.getElementById('csvModal');
        if(registerModal && event.target == registerModal) {
            closeModal('registerModal');
        }
        if(csvModal && event.target == csvModal) {
            closeModal('csvModal');
        }
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal('registerModal');
            closeModal('csvModal');
        }
    });

    // Auto-hide success message after 5 seconds
    setTimeout(function() {
        var alert = document.querySelector('.alert-success');
        if(alert) {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function() {
                if(alert && alert.parentNode) alert.remove();
            }, 500);
        }
    }, 5000);

    // Auto-hide error message after 5 seconds
    setTimeout(function() {
        var alert = document.querySelector('.alert-error');
        if(alert) {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function() {
                if(alert && alert.parentNode) alert.remove();
            }, 500);
        }
    }, 5000);
</script>

@endsection