@extends('layouts.app')
@section('title', 'School List')

@section('content')
<div class="ui-page-shell">
    {{-- Page Heading --}}
    <div class="ui-page-heading">
        <div class="ui-page-heading__copy">
            <h1>🏫 School List</h1>
            <p>Manage all registered schools and their capacity</p>
        </div>
        <div class="ui-page-heading__actions">
            <span class="ui-badge ui-badge--info">
                <i class="fas fa-building"></i> {{ $schools->count() ?? 0 }} Schools
            </span>
        </div>
    </div>

    {{-- Statistics Cards using Edu Design System --}}
    @php
        $totalTeachers = $schools->sum('totalTeacher');
        $totalCapacity = $schools->sum(function($school) {
            return ($school->totalTeacher ?? 0) + ($school->vacancy ?? 0);
        });
        $totalVacancy = $schools->sum('vacancy');
        $totalSchools = $schools->count();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="ui-card ui-card--stat-admin text-center">
                <div class="ui-card__label">Total Schools</div>
                <div class="ui-card__value">{{ $totalSchools }}</div>
                <div style="font-size:11px;color:var(--app-text-subtle);margin-top:4px;">
                    <i class="fas fa-building"></i> Registered Institutions
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-card ui-card--stat-admin text-center">
                <div class="ui-card__label">Total Teachers</div>
                <div class="ui-card__value">{{ $totalTeachers }}</div>
                <div style="font-size:11px;color:var(--app-text-subtle);margin-top:4px;">
                    <i class="fas fa-chalkboard-user"></i> Across All Schools
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-card ui-card--stat-admin text-center">
                <div class="ui-card__label">Total Capacity</div>
                <div class="ui-card__value">{{ $totalCapacity }}</div>
                <div style="font-size:11px;color:var(--app-text-subtle);margin-top:4px;">
                    <i class="fas fa-users"></i> Maximum Teacher Slots
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-card ui-card--stat-admin text-center" style="border-left-color: {{ $totalVacancy > 0 ? 'var(--app-warning)' : 'var(--app-success)' }};">
                <div class="ui-card__label">Total Vacancy</div>
                <div class="ui-card__value" style="color:{{ $totalVacancy > 0 ? 'var(--app-warning)' : 'var(--app-success)' }};">
                    {{ $totalVacancy }}
                </div>
                <div style="font-size:11px;color:var(--app-text-subtle);margin-top:4px;">
                    <i class="fas fa-exclamation-circle"></i> Open Positions
                </div>
            </div>
        </div>
    </div>

    {{-- Search and Filter Section --}}
    <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:20px;align-items:center;justify-content:space-between;">
        <form method="GET" action="{{ route('school.list') }}" style="display:flex;gap:10px;flex-wrap:wrap;flex:1;max-width:500px;">
            <div style="flex:1;min-width:200px;">
                <input type="text" name="search" class="ui-input" placeholder="🔍 Search school name, code or address..." value="{{ request('search') }}" style="height:40px;">
            </div>
            <button type="submit" class="ui-button ui-button--primary" style="height:40px;">
                <i class="fas fa-search"></i> Search
            </button>
            @if(request('search'))
                <a href="{{ route('school.list') }}" class="ui-button ui-button--compact" style="height:40px;">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </form>
        
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button type="button" class="ui-button ui-button--primary" data-bs-toggle="modal" data-bs-target="#registerSchoolModal" style="height:40px;">
                <i class="fas fa-plus"></i> Register New School
            </button>
            <a href="{{ route('school.upload') }}" class="ui-button ui-button--outline" style="height:40px;">
                <i class="fas fa-file-import"></i> Import CSV
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="ui-message" style="border-color:#a7f3d0;background:#ecfdf5;color:#065f46;margin-bottom:16px;">
            <i class="fas fa-check-circle"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="ui-message ui-message--error" style="margin-bottom:16px;">
            <i class="fas fa-exclamation-triangle"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    {{-- School Table --}}
    <div class="ui-card ui-card--section">
        <div class="ui-card__header">
            <h3 style="margin:0;font-size:14px;font-weight:800;color:var(--app-text);display:flex;align-items:center;gap:8px;">
                <i class="fas fa-list" style="color:var(--app-primary);"></i> School Management Records
            </h3>
            <span class="ui-badge ui-badge--info">
                <i class="fas fa-school"></i> {{ $totalSchools }} Schools
            </span>
        </div>
        <div class="ui-card__body" style="padding:0;">
            <div class="ui-table-wrap">
                <table class="ui-table ui-table--dashboard">
                    <thead>
                        <tr>
                            <th>School Code</th>
                            <th>School Name</th>
                            <th class="text-center">👨‍🏫</th>
                            <th class="text-center">📊</th>
                            <th class="text-center">🎯</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schools as $school)
                            @php
                                $capacity = $school->capacity ?? 15;
                                $totalTeacher = $school->totalTeacher ?? 0;
                                $vacancy = $school->vacancy ?? ($capacity - $totalTeacher);
                                
                                $vacancyBadge = 'ui-badge--success';
                                $vacancyIcon = 'fa-check-circle';
                                if ($vacancy <= 0) {
                                    $vacancyBadge = 'ui-badge--danger';
                                    $vacancyIcon = 'fa-times-circle';
                                } elseif ($vacancy <= 3) {
                                    $vacancyBadge = 'ui-badge--warning';
                                    $vacancyIcon = 'fa-exclamation-triangle';
                                }
                            @endphp
                            <tr>
                                <td>
                                    <span class="ui-badge ui-badge--info" style="font-size:11px;">
                                        {{ $school->schoolID }}
                                    </span>
                                </td>
                                <td>
                                    <div style="font-weight:700;color:var(--app-text);">
                                        <i class="fas fa-school" style="color:var(--app-primary);font-size:12px;"></i>
                                        {{ $school->schoolName }}
                                    </div>
                                    <div style="font-size:11px;color:var(--app-text-subtle);">
                                        {{ $school->schoolAddress ? \Str::limit($school->schoolAddress, 40) : 'No address' }}
                                    </div>
                                </td>
                                <td class="text-center" style="font-weight:700;color:var(--app-primary);">
                                    {{ $totalTeacher }}
                                </td>
                                <td class="text-center" style="font-weight:700;color:var(--app-text-secondary);">
                                    {{ $capacity }}
                                </td>
                                <td class="text-center">
                                    <span class="ui-badge {{ $vacancyBadge }}" style="font-size:12px;">
                                        <i class="fas {{ $vacancyIcon }}"></i>
                                        {{ $vacancy }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap;">
                                        <a href="{{ route('school.show', $school->schoolID) }}" class="ui-button ui-button--compact" style="font-size:11px;padding:4px 12px;min-height:30px;border-color:var(--app-primary-border);color:var(--app-primary);">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('school.edit', $school->schoolID) }}" class="ui-button ui-button--compact" style="font-size:11px;padding:4px 12px;min-height:30px;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="ui-state ui-state--compact">
                                        <div style="font-size:2.5rem;margin-bottom:8px;">🏫</div>
                                        <div class="ui-state__title">No Schools Found</div>
                                        <div class="ui-state__copy">No schools match your search criteria.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($schools, 'links'))
            <div style="padding:12px 20px;border-top:1px solid var(--app-divider);">
                {{ $schools->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Register School Modal --}}
<div class="modal fade" id="registerSchoolModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="ui-modal" style="max-width:800px;width:100%;border-radius:18px;border:1px solid var(--app-border);background:var(--app-surface);">
            <div class="ui-modal__header" style="border-bottom:1px solid var(--app-divider);padding:16px 24px;">
                <h5 style="margin:0;font-size:18px;font-weight:800;color:var(--app-text);display:flex;align-items:center;gap:10px;">
                    <i class="fas fa-school" style="color:var(--app-primary);"></i> Register New School
                </h5>
                <button type="button" class="ui-button ui-button--icon" data-bs-dismiss="modal" style="width:36px;height:36px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('school.store') }}" method="POST">
                @csrf
                <div class="ui-modal__body" style="padding:24px;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="ui-field">
                                <label class="ui-label"><i class="fas fa-id-card"></i> School ID</label>
                                <input type="text" name="schoolID" class="ui-input" placeholder="Enter school ID" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="ui-field">
                                <label class="ui-label"><i class="fas fa-signature"></i> School Name</label>
                                <input type="text" name="schoolName" class="ui-input" placeholder="Enter school name" required>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="ui-field">
                            <label class="ui-label"><i class="fas fa-map-marker-alt"></i> School Address</label>
                            <textarea name="schoolAddress" class="ui-textarea" rows="3" placeholder="Enter complete address" required></textarea>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="ui-field">
                                <label class="ui-label"><i class="fas fa-calendar-alt"></i> Register Date</label>
                                <input type="date" name="registerDate" class="ui-input">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="ui-field">
                                <label class="ui-label"><i class="fas fa-phone"></i> Phone Number</label>
                                <input type="text" name="phoneNumber" class="ui-input" placeholder="e.g., +60 12 345 6789">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <div class="ui-field">
                                <label class="ui-label"><i class="fas fa-chalkboard-user"></i> Total Teacher</label>
                                <input type="number" name="totalTeacher" id="totalTeacher" class="ui-input" placeholder="0" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="ui-field">
                                <label class="ui-label"><i class="fas fa-users"></i> Capacity</label>
                                <input type="number" name="capacity" id="capacity" class="ui-input" placeholder="15" min="1" value="15" required>
                                <div class="ui-field__helper">Maximum teachers allowed</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="ui-field">
                                <label class="ui-label"><i class="fas fa-bullseye"></i> Vacancy (Auto)</label>
                                <input type="number" name="vacancy" id="vacancy" class="ui-input" placeholder="0" min="0" readonly style="background:var(--app-primary-soft);color:var(--app-primary);font-weight:700;">
                                <div class="ui-field__helper">Auto-calculated</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ui-modal__actions" style="padding:16px 24px;border-top:1px solid var(--app-divider);display:flex;gap:10px;justify-content:flex-end;">
                    <button type="button" class="ui-button ui-button--compact" data-bs-dismiss="modal" style="min-height:40px;padding:0 24px;">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="ui-button ui-button--primary" style="min-height:40px;padding:0 24px;">
                        <i class="fas fa-save"></i> Register School
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Auto-hide success message after 5 seconds
    setTimeout(function() {
        let alerts = document.querySelectorAll('.ui-message');
        alerts.forEach(function(alert) {
            if(alert.style.borderColor === '#a7f3d0' || alert.classList.contains('ui-message--error')) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    if(alert.parentNode) alert.remove();
                }, 500);
            }
        });
    }, 5000);

    // Auto-calculate vacancy in modal
    document.addEventListener('DOMContentLoaded', function() {
        var totalTeacherInput = document.getElementById('totalTeacher');
        var capacityInput = document.getElementById('capacity');
        var vacancyInput = document.getElementById('vacancy');
        
        if (totalTeacherInput && capacityInput && vacancyInput) {
            function calculateVacancy() {
                var totalTeacher = parseInt(totalTeacherInput.value) || 0;
                var capacity = parseInt(capacityInput.value) || 0;
                var vacancy = capacity - totalTeacher;
                vacancyInput.value = vacancy >= 0 ? vacancy : 0;
                
                if (vacancy < 0) {
                    vacancyInput.style.color = 'var(--app-danger)';
                    vacancyInput.style.background = 'var(--app-danger-soft)';
                } else {
                    vacancyInput.style.color = 'var(--app-primary)';
                    vacancyInput.style.background = 'var(--app-primary-soft)';
                }
            }
            
            totalTeacherInput.addEventListener('input', calculateVacancy);
            capacityInput.addEventListener('input', calculateVacancy);
            calculateVacancy();
        }
    });
</script>
@endsection