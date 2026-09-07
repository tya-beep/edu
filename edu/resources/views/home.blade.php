@extends('layouts.app')

@section('title', 'Home')

@section('content')
<style>
    .hero-section {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
        padding: 60px 0 80px 0;
        border-radius: var(--app-radius-panel);
        margin-bottom: 40px;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.08) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .hero-section::after {
        content: '🏢';
        position: absolute;
        right: 40px;
        bottom: 40px;
        font-size: 120px;
        opacity: 0.06;
        pointer-events: none;
    }

    .hero-title {
        font-size: 48px;
        font-weight: 900;
        color: #ffffff;
        line-height: 1.1;
        margin-bottom: 16px;
        letter-spacing: -0.02em;
    }

    .hero-title span {
        background: linear-gradient(135deg, #60a5fa, #3b82f6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .hero-subtitle {
        font-size: 18px;
        color: #94a3b8;
        max-width: 600px;
        line-height: 1.6;
        margin-bottom: 24px;
    }

    .hero-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        margin-top: 40px;
        padding-top: 40px;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    .hero-stat {
        text-align: center;
    }

    .hero-stat-number {
        font-size: 32px;
        font-weight: 900;
        color: #ffffff;
        display: block;
    }

    .hero-stat-label {
        font-size: 14px;
        color: #94a3b8;
        margin-top: 4px;
    }

    .section-title {
        font-size: 28px;
        font-weight: 800;
        color: var(--app-text);
        margin-bottom: 8px;
        letter-spacing: -0.02em;
    }

    .section-subtitle {
        font-size: 16px;
        color: var(--app-text-subtle);
        margin-bottom: 32px;
    }

    .feature-card {
        background: var(--app-surface);
        border: 1px solid var(--app-border);
        border-radius: var(--app-radius-card);
        padding: 24px;
        transition: all 0.3s ease;
        height: 100%;
        cursor: pointer;
    }

    .feature-card:hover {
        transform: translateY(-6px);
        border-color: var(--app-primary);
        box-shadow: var(--app-shadow-card);
    }

    .feature-icon {
        width: 56px;
        height: 56px;
        border-radius: var(--app-radius-card);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 16px;
    }

    .feature-icon.blue {
        background: var(--app-primary-soft);
        color: var(--app-primary);
    }

    .feature-icon.green {
        background: var(--app-success-soft);
        color: var(--app-success);
    }

    .feature-icon.orange {
        background: var(--app-warning-soft);
        color: var(--app-warning);
    }

    .feature-icon.purple {
        background: #ede9fe;
        color: #7c3aed;
    }

    .feature-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--app-text);
        margin-bottom: 8px;
    }

    .feature-desc {
        font-size: 14px;
        color: var(--app-text-subtle);
        line-height: 1.6;
        margin-bottom: 12px;
    }

    .feature-link {
        color: var(--app-primary);
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: gap 0.2s ease;
    }

    .feature-link:hover {
        gap: 12px;
        color: var(--app-primary-dark);
    }

    .directory-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-top: 24px;
    }

    .dir-item {
        background: var(--app-surface);
        border: 1px solid var(--app-border);
        border-radius: var(--app-radius-card);
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }

    .dir-item:hover {
        transform: translateY(-4px);
        border-color: var(--app-primary);
        box-shadow: var(--app-shadow-card);
    }

    .dir-icon {
        font-size: 28px;
        margin-bottom: 8px;
        display: block;
    }

    .dir-name {
        font-weight: 700;
        font-size: 14px;
        color: var(--app-text);
        display: block;
    }

    .dir-count {
        font-size: 12px;
        color: var(--app-text-subtle);
        display: block;
        margin-top: 2px;
    }

    @media (max-width: 992px) {
        .hero-title {
            font-size: 36px;
        }

        .hero-stats {
            grid-template-columns: repeat(2, 1fr);
        }

        .directory-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 28px;
        }

        .hero-section {
            padding: 40px 0 60px 0;
        }

        .hero-stats {
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .hero-stat-number {
            font-size: 24px;
        }

        .directory-grid {
            grid-template-columns: 1fr 1fr;
        }

        .section-title {
            font-size: 22px;
        }
    }

    @media (max-width: 480px) {
        .directory-grid {
            grid-template-columns: 1fr;
        }

        .hero-stats {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>

<div class="ui-page-shell">

    {{-- HERO SECTION --}}
    <div class="hero-section">
        <div style="position:relative;z-index:1;max-width:1200px;margin:0 auto;padding:0 24px;">
            <div style="display:flex;flex-wrap:wrap;gap:40px;align-items:center;">
                <div style="flex:1;min-width:280px;">
                    <div style="display:inline-block;background:rgba(37,99,235,0.15);color:#60a5fa;padding:4px 16px;border-radius:var(--app-radius-pill);font-size:12px;font-weight:700;letter-spacing:0.05em;text-transform:uppercase;margin-bottom:16px;">
                        🚀 HR Management System
                    </div>
                    <h1 class="hero-title">
                        Welcome to <span>Al Amin HRMS</span>
                    </h1>
                    <p class="hero-subtitle">
                        Your comprehensive human resource management system. 
                        Streamline your workforce management with powerful tools and insights.
                    </p>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        <a href="{{ route('teachers.index') }}" class="ui-button ui-button--primary" style="padding:12px 28px;font-size:14px;border-radius:var(--app-radius-control);">
                            <i class="fas fa-users"></i> Get Started
                        </a>
                        <a href="{{ route('school.list') }}" class="ui-button" style="background:rgba(255,255,255,0.1);color:#fff;border:1px solid rgba(255,255,255,0.2);padding:12px 28px;font-size:14px;border-radius:var(--app-radius-control);text-decoration:none;">
                            <i class="fas fa-school"></i> View Schools
                        </a>
                    </div>
                </div>
                <div style="flex:0 0 auto;">
                    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:var(--app-radius-panel);padding:32px;min-width:200px;">
                        <div style="text-align:center;margin-bottom:20px;">
                            <div style="font-size:48px;font-weight:900;color:#ffffff;display:block;">{{ $totalEmployees ?? 0 }}</div>
                            <div style="font-size:14px;color:#94a3b8;">Total Employees</div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                            <div style="text-align:center;padding:12px;background:rgba(255,255,255,0.03);border-radius:var(--app-radius-compact);">
                                <div style="font-size:20px;font-weight:700;color:#34d399;">{{ $activeEmployees ?? 0 }}</div>
                                <div style="font-size:11px;color:#94a3b8;">Active</div>
                            </div>
                            <div style="text-align:center;padding:12px;background:rgba(255,255,255,0.03);border-radius:var(--app-radius-compact);">
                                <div style="font-size:20px;font-weight:700;color:#f59e0b;">{{ $pendingCount ?? 0 }}</div>
                                <div style="font-size:11px;color:#94a3b8;">Pending</div>
                            </div>
                            <div style="text-align:center;padding:12px;background:rgba(255,255,255,0.03);border-radius:var(--app-radius-compact);">
                                <div style="font-size:20px;font-weight:700;color:#60a5fa;">{{ $totalSchools ?? 0 }}</div>
                                <div style="font-size:11px;color:#94a3b8;">Schools</div>
                            </div>
                            <div style="text-align:center;padding:12px;background:rgba(255,255,255,0.03);border-radius:var(--app-radius-compact);">
                                <div style="font-size:20px;font-weight:700;color:#a78bfa;">{{ $totalTeachers ?? 0 }}</div>
                                <div style="font-size:11px;color:#94a3b8;">Teachers</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats Bar --}}
            <div class="hero-stats">
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ $totalTeachers ?? 0 }}</span>
                    <span class="hero-stat-label">👨‍🏫 Teachers</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ $totalStaff ?? 0 }}</span>
                    <span class="hero-stat-label">👥 Staff</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ $totalPrincipals ?? 0 }}</span>
                    <span class="hero-stat-label">👑 Principals</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ $totalSchools ?? 0 }}</span>
                    <span class="hero-stat-label">🏫 Schools</span>
                </div>
            </div>
        </div>
    </div>

    {{-- WHY WE ARE SECTION --}}
    <div style="margin-bottom:40px;">
        <div style="text-align:center;max-width:700px;margin:0 auto 32px;">
            <div style="display:inline-block;background:var(--app-primary-soft);color:var(--app-primary);padding:4px 16px;border-radius:var(--app-radius-pill);font-size:12px;font-weight:700;letter-spacing:0.05em;text-transform:uppercase;margin-bottom:12px;">
                ✨ Why Al Amin HRMS
            </div>
            <h2 class="section-title">Building Better Workplaces Through Smart HR</h2>
            <p class="section-subtitle">
                We combine innovative technology, data-driven insights, and personalized support to help you manage your workforce with confidence.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="feature-card" style="text-align:center;border-left:4px solid var(--app-primary);">
                    <div class="feature-icon blue" style="margin:0 auto 12px;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="feature-title" style="font-size:16px;">Manage Staff</div>
                    <div class="feature-desc" style="font-size:12px;">Complete staff management</div>
                    <div style="font-size:28px;font-weight:800;color:var(--app-primary);">{{ $totalStaff ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="feature-card" style="text-align:center;border-left:4px solid var(--app-success);">
                    <div class="feature-icon green" style="margin:0 auto 12px;">
                        <i class="fas fa-chalkboard-user"></i>
                    </div>
                    <div class="feature-title" style="font-size:16px;">Teachers</div>
                    <div class="feature-desc" style="font-size:12px;">Complete teacher management</div>
                    <div style="font-size:28px;font-weight:800;color:var(--app-success);">{{ $totalTeachers ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="feature-card" style="text-align:center;border-left:4px solid var(--app-warning);">
                    <div class="feature-icon orange" style="margin:0 auto 12px;">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="feature-title" style="font-size:16px;">Principals</div>
                    <div class="feature-desc" style="font-size:12px;">Complete principal management</div>
                    <div style="font-size:28px;font-weight:800;color:var(--app-warning);">{{ $totalPrincipals ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="feature-card" style="text-align:center;border-left:4px solid #7c3aed;">
                    <div class="feature-icon purple" style="margin:0 auto 12px;">
                        <i class="fas fa-school"></i>
                    </div>
                    <div class="feature-title" style="font-size:16px;">Schools</div>
                    <div class="feature-desc" style="font-size:12px;">Complete school management</div>
                    <div style="font-size:28px;font-weight:800;color:#7c3aed;">{{ $totalSchools ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- OUR SOLUTIONS / FEATURES SECTION --}}
    <div style="margin-bottom:40px;">
        <div style="text-align:center;max-width:700px;margin:0 auto 32px;">
            <div style="display:inline-block;background:var(--app-success-soft);color:var(--app-success);padding:4px 16px;border-radius:var(--app-radius-pill);font-size:12px;font-weight:700;letter-spacing:0.05em;text-transform:uppercase;margin-bottom:12px;">
                🚀 Our Solutions
            </div>
            <h2 class="section-title">Helping you manage your workforce efficiently</h2>
            <p class="section-subtitle">
                From recruitment to retirement, Al Amin HRMS provides the tools you need to succeed.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon blue">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="feature-title">Teacher Management</div>
                    <div class="feature-desc">
                        Complete teacher lifecycle management from hiring to retirement. Track performance, manage resignations, and more.
                    </div>
                    <a href="{{ route('teachers.index') }}" class="feature-link">
                        Learn More <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon green">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="feature-title">Staff & Principal Management</div>
                    <div class="feature-desc">
                        Streamline staff and principal management with centralized profiles, assignment tracking, and performance monitoring.
                    </div>
                    <a href="{{ route('staff.list') }}" class="feature-link">
                        Learn More <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon orange">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="feature-title">School Management</div>
                    <div class="feature-desc">
                        Manage all schools, track vacancies, assign principals, and maintain school profiles in one centralized system.
                    </div>
                    <a href="{{ route('school.list') }}" class="feature-link">
                        Learn More <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- QUICK ACCESS DIRECTORY --}}
    <div class="ui-card ui-card--section">
        <div class="ui-card__header">
            <h3 style="margin:0;font-size:16px;font-weight:800;color:var(--app-text);display:flex;align-items:center;gap:8px;">
                <i class="fas fa-sitemap" style="color:var(--app-primary);"></i> Quick Access
            </h3>
            <span class="ui-badge ui-badge--info">System Directory</span>
        </div>
        <div class="ui-card__body">
            <div class="directory-grid">
                <a href="{{ route('school.list') }}" class="dir-item">
                    <span class="dir-icon">🏫</span>
                    <span class="dir-name">Schools</span>
                    <span class="dir-count">{{ $totalSchools ?? 0 }} schools</span>
                </a>
                <a href="{{ route('teachers.index') }}" class="dir-item">
                    <span class="dir-icon">👨‍🏫</span>
                    <span class="dir-name">Teachers</span>
                    <span class="dir-count">{{ $totalTeachers ?? 0 }} teachers</span>
                </a>
                <a href="{{ route('staff.list') }}" class="dir-item">
                    <span class="dir-icon">👥</span>
                    <span class="dir-name">Staff</span>
                    <span class="dir-count">{{ $totalStaff ?? 0 }} staff</span>
                </a>
                <a href="{{ route('principal.index') }}" class="dir-item">
                    <span class="dir-icon">👑</span>
                    <span class="dir-name">Principals</span>
                    <span class="dir-count">{{ $totalPrincipals ?? 0 }} principals</span>
                </a>
                <a href="{{ route('placement.index') }}" class="dir-item">
                    <span class="dir-icon">📍</span>
                    <span class="dir-name">Placements</span>
                    <span class="dir-count">Assign teachers</span>
                </a>
                <a href="{{ route('school.organizations') }}" class="dir-item">
                    <span class="dir-icon">🏢</span>
                    <span class="dir-name">Organizations</span>
                    <span class="dir-count">Manage orgs</span>
                </a>
            </div>
        </div>
    </div>

    {{-- System Status Footer --}}
    <div style="margin-top:24px;padding:16px 20px;background:var(--app-background);border-radius:var(--app-radius-card);border:1px solid var(--app-border);display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px;font-size:12px;color:var(--app-text-subtle);">
        <span>
            <i class="fas fa-database"></i> System Status: <strong style="color:var(--app-success);">Operational</strong>
        </span>
        <span>
            <i class="fas fa-clock"></i> Last Updated: {{ now()->format('d/m/Y H:i:s') }}
        </span>
        <span>
            <i class="fas fa-user-check"></i> Logged in as: <strong>{{ session('userName', 'HR Admin') }}</strong>
        </span>
        <span>
            <i class="fas fa-code-branch"></i> Version: 4.2.1
        </span>
    </div>
</div>
@endsection