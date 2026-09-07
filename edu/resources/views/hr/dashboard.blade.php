@extends('layouts.app')
@section('title', 'HR Dashboard')

@section('content')
<div class="ui-page-shell">
    {{-- Page Heading --}}
    <div class="ui-page-heading">
        <div class="ui-page-heading__copy">
            <h1>Welcome back, HR Team! 👋</h1>
            <p>Here's what's happening with your workforce today.</p>
        </div>
        <div class="ui-page-heading__actions">
            <span class="ui-badge ui-badge--info">
                <i class="fas fa-calendar-alt me-1"></i>{{ now()->format('l, d F Y') }}
            </span>
        </div>
    </div>

    {{-- Statistics Grid using Edu Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="ui-card ui-card--stat-admin text-center">
                <div class="ui-card__label">Total Schools</div>
                <div class="ui-card__value">{{ $totalSchools ?? 0 }}</div>
                <div style="font-size:11px;color:var(--app-text-subtle);margin-top:4px;">
                    <i class="fas fa-building"></i> Active Institutions
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-card ui-card--stat-admin text-center">
                <div class="ui-card__label">Principals</div>
                <div class="ui-card__value">{{ $totalPrincipals ?? 0 }}</div>
                <div style="font-size:11px;color:var(--app-text-subtle);margin-top:4px;">
                    <i class="fas fa-user-tie"></i> School Leaders
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-card ui-card--stat-admin text-center">
                <div class="ui-card__label">Teachers</div>
                <div class="ui-card__value">{{ $totalTeachers ?? 0 }}</div>
                <div style="font-size:11px;color:var(--app-text-subtle);margin-top:4px;">
                    <i class="fas fa-chalkboard-user"></i> Teaching Staff
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-card ui-card--stat-admin text-center" style="border-left-color: var(--app-danger);">
                <div class="ui-card__label">Schools w/o Principal</div>
                <div class="ui-card__value" style="color:var(--app-danger);">
                    {{ $totalSchoolsWithoutPrincipal ?? 0 }}
                </div>
                <div style="font-size:11px;color:var(--app-text-subtle);margin-top:4px;">
                    <i class="fas fa-exclamation-triangle" style="color:var(--app-warning);"></i> Requires Attention
                </div>
            </div>
        </div>
    </div>

    {{-- Schools Without Principal Alert --}}
    @if(($totalSchoolsWithoutPrincipal ?? 0) > 0)
        <div class="ui-message ui-message--error" style="margin-bottom: 1.5rem;">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>{{ $totalSchoolsWithoutPrincipal }}</strong> school(s) currently don't have an active principal assigned.
                Please assign principals to these schools as soon as possible.
            </div>
        </div>
    @endif

    {{-- Resignation Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="ui-card ui-card--stat-admin" style="border-left-color: var(--app-warning);">
                <div class="ui-card__label">Pending Review</div>
                <div class="ui-card__value" style="color:var(--app-warning);">
                    {{ ($teacherResignations['pending'] ?? 0) + ($staffResignations['pending'] ?? 0) + ($principalResignations['pending'] ?? 0) }}
                </div>
                <div style="font-size:11px;color:var(--app-text-subtle);margin-top:4px;">
                    👨‍🏫 {{ $teacherResignations['pending'] ?? 0 }} Teachers | 
                    👥 {{ $staffResignations['pending'] ?? 0 }} Staff | 
                    👑 {{ $principalResignations['pending'] ?? 0 }} Principals
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-card ui-card--stat-admin" style="border-left-color: var(--app-success);">
                <div class="ui-card__label">Approved</div>
                <div class="ui-card__value" style="color:var(--app-success);">
                    {{ ($teacherResignations['approved'] ?? 0) + ($staffResignations['approved'] ?? 0) + ($principalResignations['approved'] ?? 0) }}
                </div>
                <div style="font-size:11px;color:var(--app-text-subtle);margin-top:4px;">
                    👨‍🏫 {{ $teacherResignations['approved'] ?? 0 }} | 
                    👥 {{ $staffResignations['approved'] ?? 0 }} | 
                    👑 {{ $principalResignations['approved'] ?? 0 }}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-card ui-card--stat-admin" style="border-left-color: var(--app-danger);">
                <div class="ui-card__label">Rejected</div>
                <div class="ui-card__value" style="color:var(--app-danger);">
                    {{ ($teacherResignations['rejected'] ?? 0) + ($staffResignations['rejected'] ?? 0) + ($principalResignations['rejected'] ?? 0) }}
                </div>
                <div style="font-size:11px;color:var(--app-text-subtle);margin-top:4px;">
                    👨‍🏫 {{ $teacherResignations['rejected'] ?? 0 }} | 
                    👥 {{ $staffResignations['rejected'] ?? 0 }} | 
                    👑 {{ $principalResignations['rejected'] ?? 0 }}
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- LEFT COLUMN --}}
        <div class="col-lg-7">
            {{-- School Capacity Table --}}
            <div class="ui-card ui-card--section">
                <div class="ui-card__header">
                    <h3 style="margin:0;font-size:14px;font-weight:800;color:var(--app-text);">
                        <i class="fas fa-school" style="color:var(--app-primary);"></i> School Capacity Overview
                    </h3>
                    <span class="ui-badge ui-badge--info">
                        {{ isset($schools) ? count($schools) : 0 }} Schools
                    </span>
                </div>
                <div class="ui-card__body" style="padding:0;">
                    <div class="ui-table-wrap">
                        <table class="ui-table ui-table--dashboard">
                            <thead>
                                <tr>
                                    <th>School Name</th>
                                    <th class="text-center">👨‍🏫</th>
                                    <th class="text-center">🎯</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($schools ?? collect())->take(6) as $school)
                                <tr>
                                    <td class="fw-semibold">{{ $school->schoolName ?? 'N/A' }}</td>
                                    <td class="text-center fw-bold">{{ $school->totalTeacher ?? 0 }}</td>
                                    <td class="text-center">
                                        @if(($school->vacancy ?? 0) > 0)
                                            <span class="fw-bold" style="color:var(--app-warning);">{{ $school->vacancy }}</span>
                                        @else
                                            <span style="color:var(--app-text-subtle);">0</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(($school->vacancy ?? 0) > 0)
                                            <span class="ui-badge ui-badge--warning">
                                                <i class="fas fa-exclamation-triangle"></i> Needs
                                            </span>
                                        @else
                                            <span class="ui-badge ui-badge--success">
                                                <i class="fas fa-check-circle"></i> Full
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center" style="padding:20px;color:var(--app-text-subtle);">
                                            No Schools Found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Recent Resignations --}}
            <div class="ui-card ui-card--section mt-4">
                <div class="ui-card__header">
                    <h3 style="margin:0;font-size:14px;font-weight:800;color:var(--app-text);">
                        <i class="fas fa-clock" style="color:var(--app-warning);"></i> Recent Resignation Requests
                    </h3>
                    <span class="ui-badge ui-badge--info">Last 5</span>
                </div>
                <div class="ui-card__body" style="padding:0;">
                    @forelse(($recentResignations ?? collect()) as $resignation)
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 18px;border-bottom:1px solid var(--app-divider);">
                            <div>
                                <div style="font-weight:700;font-size:14px;color:var(--app-text);display:flex;align-items:center;gap:6px;">
                                    {{ $resignation->name ?? ($resignation->teacherName ?? $resignation->staffName ?? $resignation->principalName ?? 'Unknown') }}
                                    <span class="ui-badge" style="font-size:10px;padding:2px 8px;border-color:var(--app-border);background:var(--app-surface);">
                                        {{ ucfirst($resignation->type ?? 'Teacher') }}
                                    </span>
                                </div>
                                <div style="font-size:12px;color:var(--app-text-subtle);">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    {{ isset($resignation->request_date) ? \Carbon\Carbon::parse($resignation->request_date)->format('d M Y') : 'N/A' }}
                                </div>
                            </div>
                            <div>
                                @if(($resignation->status ?? 'pending') == 'pending')
                                    <span class="ui-badge ui-badge--warning">Pending</span>
                                @elseif(($resignation->status ?? '') == 'approved')
                                    <span class="ui-badge ui-badge--success">Approved</span>
                                @else
                                    <span class="ui-badge ui-badge--danger">Rejected</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="ui-state ui-state--compact">
                            <div class="ui-state__title">No Recent Resignations</div>
                            <div class="ui-state__copy">There are no resignation requests at this time.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-lg-5">
            {{-- Latest Assignment --}}
            <div class="ui-card ui-card--section">
                <div class="ui-card__header">
                    <h3 style="margin:0;font-size:14px;font-weight:800;color:var(--app-text);">
                        <i class="fas fa-user-plus" style="color:var(--app-primary);"></i> Latest Assignment
                    </h3>
                </div>
                <div class="ui-card__body">
                    @if(isset($latestAssign) && $latestAssign)
                        <div style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);border-radius:var(--app-radius-card);padding:16px 20px;color:#fff;">
                            <div style="font-weight:800;font-size:16px;margin-bottom:4px;">
                                {{ $latestAssign->teacherName ?? $latestAssign->name ?? 'Unknown' }}
                            </div>
                            <div style="opacity:0.8;font-size:13px;">
                                <i class="fas fa-school me-1"></i> {{ $latestAssign->schoolName ?? $latestAssign->school ?? 'N/A' }}
                            </div>
                            <div style="margin-top:8px;">
                                <span style="background:rgba(255,255,255,0.2);padding:2px 12px;border-radius:var(--app-radius-pill);font-size:12px;">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ isset($latestAssign->assignDate) ? \Carbon\Carbon::parse($latestAssign->assignDate)->format('d M Y') : 'N/A' }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="ui-state ui-state--compact">
                            <div class="ui-state__title">No Recent Placements</div>
                            <div class="ui-state__copy">No teachers have been assigned recently.</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Retirement Watch --}}
            <div class="ui-card ui-card--section mt-4">
                <div class="ui-card__header">
                    <h3 style="margin:0;font-size:14px;font-weight:800;color:var(--app-text);">
                        <i class="fas fa-clock" style="color:var(--app-warning);"></i> Retirement Watch
                    </h3>
                    <span class="ui-badge ui-badge--info">Next 6 Months</span>
                </div>
                <div class="ui-card__body" style="padding:0;max-height:300px;overflow-y:auto;">
                    @php
                        $hasRetiring = isset($retiringSoon) && $retiringSoon->count() > 0;
                    @endphp
                    
                    @if($hasRetiring)
                        @foreach($retiringSoon as $person)
                            @php
                                $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($person->pensionDate));
                                $urgencyColor = $daysLeft <= 30 ? '#dc2626' : ($daysLeft <= 60 ? '#f59e0b' : '#3b82f6');
                                $urgencyText = $daysLeft <= 30 ? '🔴 Near' : ($daysLeft <= 60 ? '🟠 Soon' : '🔵 Upcoming');
                            @endphp
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 18px;border-bottom:1px solid var(--app-divider);border-left:3px solid {{ $urgencyColor }};padding-left:15px;">
                                <div>
                                    <div style="font-weight:700;font-size:14px;color:var(--app-text);">
                                        {{ $person->name ?? 'Unknown' }}
                                        <span class="ui-badge" style="font-size:10px;padding:2px 8px;border-color:var(--app-border);background:var(--app-surface);">
                                            {{ ucfirst($person->type ?? 'Unknown') }}
                                        </span>
                                        <span style="font-size:11px;color:{{ $urgencyColor }};font-weight:700;">{{ $urgencyText }}</span>
                                    </div>
                                    <div style="font-size:12px;color:var(--app-text-subtle);">
                                        <i class="fas fa-id-card me-1"></i> {{ $person->id ?? 'N/A' }}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div style="font-size:12px;font-weight:700;color:var(--app-text-secondary);">
                                        {{ isset($person->pensionDate) ? \Carbon\Carbon::parse($person->pensionDate)->format('d M Y') : 'N/A' }}
                                    </div>
                                    <div style="font-size:11px;color:{{ $urgencyColor }};font-weight:700;">
                                        {{ number_format($daysLeft) }} days left
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="ui-state ui-state--compact">
                            <div class="ui-state__title">No Upcoming Retirements</div>
                            <div class="ui-state__copy">No retirements scheduled in the next 6 months.</div>
                        </div>
                    @endif
                </div>
                @if($hasRetiring)
                    <div style="padding:10px 18px;border-top:1px solid var(--app-divider);background:var(--app-background);font-size:12px;color:var(--app-text-secondary);">
                        <strong>Total:</strong> {{ $retiringSoon->count() }} 
                        (👨‍🏫 {{ $retiringSoon->where('type', 'teacher')->count() }} Teachers | 
                        👑 {{ $retiringSoon->where('type', 'principal')->count() }} Principals | 
                        👔 {{ $retiringSoon->where('type', 'staff')->count() }} Staff)
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection