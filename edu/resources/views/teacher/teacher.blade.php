@extends('layouts.app')

@section('title', 'Teacher List')

@section('content')
<div class="ui-page-shell">
    {{-- Page Heading --}}
    <div class="ui-page-heading">
        <div class="ui-page-heading__copy">
            <h1 style="display:flex;align-items:center;gap:10px;">
                <i class="fas fa-chalkboard-user" style="color:var(--app-primary);font-size:24px;"></i> Teacher List
            </h1>
            <p>Manage all registered teachers</p>
        </div>
        <div class="ui-page-heading__actions">
            <span class="ui-badge ui-badge--info">
                <i class="fas fa-user"></i> {{ isset($teachers) ? $teachers->count() : 0 }} Teachers
            </span>
            <button type="button" class="ui-button ui-button--primary" onclick="openModal('registerModal')" style="min-height:36px;padding:0 16px;display:inline-flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                <i class="fas fa-plus"></i> Register
            </button>
            <button type="button" class="ui-button ui-button--outline" onclick="openModal('csvModal')" style="min-height:36px;padding:0 16px;display:inline-flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                <i class="fas fa-file-import"></i> Import CSV
            </button>
            <a href="{{ route('teachers.template') }}" class="ui-button ui-button--compact" style="min-height:36px;padding:0 16px;display:inline-flex;align-items:center;gap:6px;font-size:13px;">
                <i class="fas fa-download"></i> Template
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('status'))
        <div class="ui-message" style="border-color:#a7f3d0;background:#ecfdf5;color:#065f46;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;">
            <div style="display:flex;align-items:center;gap:8px;">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('status') }}</span>
            </div>
            <button type="button" class="ui-button ui-button--icon" style="width:28px;height:28px;font-size:14px;" onclick="this.closest('.ui-message').remove();">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="ui-message ui-message--error" style="margin-bottom:16px;">
            <i class="fas fa-exclamation-triangle"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    {{-- Search Section --}}
    <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:20px;align-items:center;justify-content:space-between;">
        <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;flex:1;">
            <div style="position:relative;flex:1;min-width:200px;">
                <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--app-text-subtle);font-size:14px;"></i>
                <input type="text" name="search" class="ui-input" placeholder="Search by name, ID, IC or email..." value="{{ request('search') }}" style="padding-left:36px;height:40px;">
            </div>
            <button type="submit" class="ui-button ui-button--primary" style="height:40px;padding:0 20px;">
                <i class="fas fa-search"></i> Search
            </button>
            @if(request('search'))
                <a href="{{ route('teachers.index') }}" class="ui-button ui-button--compact" style="height:40px;padding:0 16px;">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Teachers Grouped by School --}}
    @if(isset($teachers) && count($teachers) > 0)
        @foreach($teachers as $schoolID => $group)
            <div style="margin-bottom:24px;">
                {{-- School Header --}}
                <div style="background:linear-gradient(135deg, #1e3a5f, #1e40af);color:#fff;padding:12px 20px;border-radius:var(--app-radius-card) var(--app-radius-card) 0 0;font-weight:700;font-size:14px;display:flex;align-items:center;gap:10px;">
                    <i class="fas fa-school"></i>
                    {{ $group->first()->schoolName ?? $schoolID }}
                    <span class="ui-badge" style="background:rgba(255,255,255,0.2);border-color:rgba(255,255,255,0.3);color:#fff;font-size:11px;padding:2px 12px;margin-left:auto;">
                        <i class="fas fa-user"></i> {{ $group->count() }} Teachers
                    </span>
                </div>

                {{-- Teacher Table --}}
                <div class="ui-card ui-card--section" style="border-radius:0 0 var(--app-radius-card) var(--app-radius-card);">
                    <div class="ui-card__body" style="padding:0;">
                        <div class="ui-table-wrap">
                            <table class="ui-table ui-table--dashboard">
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
                                    @php
                                        $status = $teacher->status ?? 'Aktif';
                                        $isActive = ($status == 'Aktif' || $status == 'Active' || $status == 'active' || $status == null);
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="ui-badge ui-badge--info" style="font-size:11px;">
                                                {{ $teacher->teacherID }}
                                            </span>
                                        </td>
                                        <td style="font-weight:700;color:var(--app-text);">
                                            <i class="fas fa-user" style="color:var(--app-primary);font-size:12px;"></i>
                                            {{ $teacher->teacherName }}
                                        </td>
                                        <td style="font-size:13px;color:var(--app-text-secondary);">
                                            {{ $teacher->ICNumber ?? '-' }}
                                        </td>
                                        <td style="font-size:13px;color:var(--app-text-secondary);">
                                            {{ $teacher->phoneNumber ?? '-' }}
                                        </td>
                                        <td style="font-size:13px;color:var(--app-text-secondary);">
                                            {{ $teacher->email ?? '-' }}
                                        </td>
                                        <td>
                                            @if($isActive)
                                                <span class="ui-badge ui-badge--success" style="font-size:11px;">
                                                    <i class="fas fa-circle" style="font-size:8px;"></i> Active
                                                </span>
                                            @else
                                                <span class="ui-badge ui-badge--danger" style="font-size:11px;">
                                                    <i class="fas fa-circle" style="font-size:8px;"></i> Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('teachers.show', $teacher->teacherID) }}" 
                                               class="ui-button ui-button--compact" 
                                               style="font-size:11px;padding:4px 14px;min-height:30px;border-color:var(--app-primary-border);color:var(--app-primary);">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        
        @if(isset($teachers) && method_exists($teachers, 'links'))
            <div style="margin-top:20px;">
                {{ $teachers->links() }}
            </div>
        @endif
    @else
        <div class="ui-state ui-state--compact">
            <div style="font-size:3rem;margin-bottom:8px;">👨‍🏫</div>
            <div class="ui-state__title">No Teachers Found</div>
            <div class="ui-state__copy">
                @if(request('search'))
                    No teachers match your search criteria.
                @else
                    No teachers have been registered yet.
                @endif
            </div>
            @if(request('search'))
                <a href="{{ route('teachers.index') }}" class="ui-button ui-button--primary" style="margin-top:16px;">
                    <i class="fas fa-times"></i> Clear Search
                </a>
            @endif
        </div>
    @endif
</div>

{{-- ============================================================ --}}
{{-- REGISTER MODAL --}}
{{-- ============================================================ --}}
<div id="registerModal" class="modal" style="display:none;">
    <div class="modal-box">
        <div class="modal-header" style="display:flex;justify-content:space-between;align-items:center;">
            <div class="modal-title">Register New Teacher</div>
            <button type="button" onclick="closeModal('registerModal')" style="background:rgba(255,255,255,0.2);border:none;color:#fff;width:36px;height:36px;border-radius:50%;font-size:20px;cursor:pointer;">
                <i class="fas fa-times"></i>
            </button>
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

{{-- ============================================================ --}}
{{-- CSV MODAL --}}
{{-- ============================================================ --}}
<div id="csvModal" class="modal" style="display:none;">
    <div class="modal-box">
        <div class="modal-header" style="display:flex;justify-content:space-between;align-items:center;">
            <div class="modal-title">Import Teacher CSV</div>
            <button type="button" onclick="closeModal('csvModal')" style="background:rgba(255,255,255,0.2);border:none;color:#fff;width:36px;height:36px;border-radius:50%;font-size:20px;cursor:pointer;">
                <i class="fas fa-times"></i>
            </button>
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

<style>
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        justify-content: center;
        align-items: center;
        z-index: 99999;
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
        background: linear-gradient(135deg, var(--app-primary), var(--app-primary-dark));
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

    .modal-body {
        padding: 1.5rem;
    }

    .form-control-custom {
        width: 100%;
        border: 1px solid var(--app-border);
        border-radius: var(--app-radius-control);
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        outline: none;
        font-family: inherit;
        box-sizing: border-box;
        background: var(--app-surface);
        color: var(--app-text);
    }

    .form-control-custom:focus {
        border-color: var(--app-primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
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
        padding-right: 2.5rem;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--app-text-secondary);
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
        border-top: 1px solid var(--app-divider);
        flex-wrap: wrap;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .btn-custom {
        background: linear-gradient(135deg, var(--app-primary), var(--app-primary-dark));
        color: white;
        border: none;
        padding: 0.625rem 1.5rem;
        border-radius: var(--app-radius-control);
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-custom:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .btn-light-custom {
        background: white;
        border: 1px solid var(--app-border);
        color: var(--app-text-secondary);
        padding: 0.625rem 1.5rem;
        border-radius: var(--app-radius-control);
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-light-custom:hover {
        background: var(--app-primary-soft);
        border-color: var(--app-primary-border);
        color: var(--app-primary-dark);
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }
        .modal-box {
            width: 100%;
            max-width: 100%;
        }
        .modal-body {
            padding: 1rem;
        }
    }
</style>

<script>
    function openModal(id) { 
        var modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.add('show');
            document.body.style.overflow = "hidden";
        }
    }
    
    function closeModal(id) { 
        var modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
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

    // Auto-hide success/error messages after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.ui-message');
        alerts.forEach(function(alert) {
            if (!alert.classList.contains('ui-message--error')) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    if (alert.parentElement) alert.remove();
                }, 500);
            }
        });
    }, 5000);
</script>
@endsection