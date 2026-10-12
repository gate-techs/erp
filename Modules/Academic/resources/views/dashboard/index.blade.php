@extends('layouts/contentNavbarLayout')

@section('title', __('Dashboard') . ' - ' . config('variables.templateName'))

@section('vendor-style')
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('content')
<style>
    /* ========================================================
       Odoo-Inspired Executive Academic Structure & Curriculum Theme
       ======================================================== */
    :root {
        --odoo-primary: #714B67;
        --odoo-primary-dark: #56334d;
        --odoo-primary-light: #f7f1f5;
        --odoo-teal: #017E84;
        --odoo-teal-dark: #005f63;
        --odoo-teal-light: #e6f5f5;
        --odoo-amber: #e59866;
        --odoo-amber-dark: #d97706;
        --odoo-amber-light: #fff7ed;
        --odoo-green: #28A745;
        --odoo-green-light: #ecfdf5;
        --odoo-card-border: #ede7ec;
    }

    .odoo-dashboard {
        font-family: inherit;
    }

    /* Odoo Control Panel Header */
    .odoo-control-panel {
        background: #ffffff;
        border: 1px solid var(--odoo-card-border);
        border-radius: 12px;
        padding: 18px 24px;
        box-shadow: 0 2px 10px rgba(113, 75, 103, 0.04);
        margin-bottom: 24px;
    }

    .odoo-badge-year {
        background-color: var(--odoo-primary-light);
        color: var(--odoo-primary);
        border: 1px solid rgba(113, 75, 103, 0.2);
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Odoo Buttons */
    .btn-odoo-primary {
        background-color: var(--odoo-primary);
        border-color: var(--odoo-primary);
        color: #ffffff;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .btn-odoo-primary:hover, .btn-odoo-primary:focus {
        background-color: var(--odoo-primary-dark);
        border-color: var(--odoo-primary-dark);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(113, 75, 103, 0.25);
    }

    .btn-odoo-outline {
        background-color: #ffffff;
        border: 1px solid var(--odoo-card-border);
        color: #495057;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .btn-odoo-outline:hover {
        background-color: var(--odoo-primary-light);
        border-color: var(--odoo-primary);
        color: var(--odoo-primary);
    }

    /* KPI Stat Cards (3 per row) */
    .odoo-kpi-card {
        background: #ffffff;
        border: 1px solid var(--odoo-card-border);
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(113, 75, 103, 0.04);
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .odoo-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 22px rgba(113, 75, 103, 0.09);
        border-color: rgba(113, 75, 103, 0.25);
    }

    .odoo-kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: transparent;
    }
    .odoo-kpi-card.border-kpi-plum::before { background: var(--odoo-primary); }
    .odoo-kpi-card.border-kpi-teal::before { background: var(--odoo-teal); }
    .odoo-kpi-card.border-kpi-amber::before { background: var(--odoo-amber); }
    .odoo-kpi-card.border-kpi-green::before { background: var(--odoo-green); }
    .odoo-kpi-card.border-kpi-indigo::before { background: #5a56df; }
    .odoo-kpi-card.border-kpi-rose::before { background: #e46a76; }

    .odoo-kpi-icon {
        width: 46px;
        height: 46px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .icon-bg-plum { background: var(--odoo-primary-light); color: var(--odoo-primary); }
    .icon-bg-teal { background: var(--odoo-teal-light); color: var(--odoo-teal); }
    .icon-bg-amber { background: var(--odoo-amber-light); color: var(--odoo-amber-dark); }
    .icon-bg-green { background: var(--odoo-green-light); color: var(--odoo-green); }
    .icon-bg-indigo { background: #f0f0ff; color: #5a56df; }
    .icon-bg-rose { background: #fdf2f4; color: #e46a76; }

    .odoo-kpi-number {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1.2;
        color: #212529;
    }
    .odoo-kpi-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #555;
    }

    /* Odoo App Cards */
    .odoo-app-card {
        background: #ffffff;
        border: 1px solid var(--odoo-card-border);
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(113, 75, 103, 0.04);
        transition: all 0.25s ease;
        height: 100%;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .odoo-app-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(113, 75, 103, 0.1);
        border-color: rgba(113, 75, 103, 0.3);
    }
    .odoo-app-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f1ecf0;
        background: #faf8fa;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .odoo-app-body {
        padding: 18px 20px;
        flex: 1;
    }
    .odoo-app-footer {
        padding: 12px 20px;
        background: #ffffff;
        border-top: 1px solid #f1ecf0;
    }

    /* Content Cards */
    .odoo-card {
        background: #ffffff;
        border: 1px solid var(--odoo-card-border);
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(113, 75, 103, 0.04);
    }
    .odoo-card-header {
        padding: 18px 22px;
        border-bottom: 1px solid var(--odoo-card-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .table-odoo th {
        background: #faf8fa;
        color: #555;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--odoo-card-border);
        padding: 12px 16px;
    }
    .table-odoo td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f4eff3;
    }
</style>

<div class="odoo-dashboard">

    <!-- ========================================================
         1. ODOO CONTROL PANEL / EXECUTIVE HEADER
         ======================================================== -->
    <div class="odoo-control-panel d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted small">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item text-muted small">{{ __('Academic Management') }}</li>
                    <li class="breadcrumb-item active fw-bold small" style="color: var(--odoo-primary)">{{ __('Dashboard') }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 mt-1">
                <div class="odoo-kpi-icon icon-bg-plum" style="width: 40px; height: 40px; font-size: 20px;">
                    <i class="bx bxs-dashboard"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold" style="color: #212529;">{{ __('Dashboard') }}</h4>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            @if ($currentYear)
                <span class="odoo-badge-year" title="{{ __('Current Academic Year') }}">
                    <i class="bx bx-calendar-star fs-5"></i>
                    <span>{{ $currentYear->name }}</span>
                    <span class="badge bg-success rounded-pill ms-1 py-0 px-2 small">{{ __('Active') }}</span>
                </span>
            @endif

            <!-- Quick Action Dropdown for the 7 Items -->
            <div class="btn-group">
                <button type="button" class="btn btn-odoo-primary dropdown-toggle d-flex align-items-center gap-1" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bx bx-plus-circle"></i>
                    <span>{{ __('Quick Actions') }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li><h6 class="dropdown-header text-uppercase small text-muted">{{ __('Academic Structure') }}</h6></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('academic.academic-years.index') }}"><i class="bx bx-calendar text-primary"></i>{{ __('Academic Years') }}</a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('academic.stages.index') }}"><i class="bx bx-layer text-success"></i>{{ __('Academic Stages') }}</a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('academic.classes.index') }}"><i class="bx bx-chalkboard text-info"></i>{{ __('Active Classes') }}</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><h6 class="dropdown-header text-uppercase small text-muted">{{ __('Curriculum & Planning') }}</h6></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('academic.subjects.index') }}"><i class="bx bx-book text-warning"></i>{{ __('Subjects') }}</a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('academic.lessons.index') }}"><i class="bx bx-book-reader text-danger"></i>{{ __('Lessons') }}</a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('academic.weekly-plans.index') }}"><i class="bx bx-calendar-week text-primary"></i>{{ __('Weekly Plans') }}</a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('academic.monthly-plans.index') }}"><i class="bx bx-calendar-event text-secondary"></i>{{ __('Monthly Plans') }}</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- ========================================================
         2. EXECUTIVE METRICS (3 CARDS PER ROW - ROW 1 & ROW 2)
         ======================================================== -->
    <div class="row g-3 mb-4">
        <!-- 1. Academic Years -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="odoo-kpi-card border-kpi-plum">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="odoo-kpi-icon icon-bg-plum"><i class="bx bx-calendar-star"></i></div>
                        <div>
                            <span class="odoo-kpi-label d-block">{{ __('Academic Years') }}</span>
                            <span class="badge bg-label-primary rounded-pill small">{{ $counts['active_years'] }} {{ __('active') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('academic.academic-years.index') }}" class="btn btn-sm btn-icon btn-label-primary rounded-circle" title="{{ __('View') }}">
                        <i class="bx bx-arrow-to-right"></i>
                    </a>
                </div>
                <div class="my-2">
                    <h3 class="odoo-kpi-number mb-0">{{ number_format($counts['years']) }}</h3>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top text-muted small">
                    <span class="text-success"><i class="bx bx-check-circle me-1"></i>{{ $counts['active_years'] }} {{ __('Active') }}</span>
                    <span class="text-dark">{{ $currentYear ? $currentYear->name : '-' }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Academic Stages -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="odoo-kpi-card border-kpi-teal">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="odoo-kpi-icon icon-bg-teal"><i class="bx bx-layer"></i></div>
                        <div>
                            <span class="odoo-kpi-label d-block">{{ __('Academic Stages') }}</span>
                            <span class="badge bg-label-info rounded-pill small">{{ $counts['active_stages'] }} {{ __('active') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('academic.stages.index') }}" class="btn btn-sm btn-icon btn-label-info rounded-circle" title="{{ __('View') }}">
                        <i class="bx bx-arrow-to-right"></i>
                    </a>
                </div>
                <div class="my-2">
                    <h3 class="odoo-kpi-number mb-0">{{ number_format($counts['stages']) }}</h3>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top text-muted small">
                    <span class="text-success"><i class="bx bx-check-circle me-1"></i>{{ $counts['active_stages'] }} {{ __('Active') }}</span>
                    <span class="text-info"><i class="bx bx-chalkboard me-1"></i>{{ $counts['active_classes'] }} {{ __('Classes') }}</span>
                </div>
            </div>
        </div>

        <!-- 3. Academic Classes -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="odoo-kpi-card border-kpi-amber">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="odoo-kpi-icon icon-bg-amber"><i class="bx bx-chalkboard"></i></div>
                        <div>
                            <span class="odoo-kpi-label d-block">{{ __('Active Classes') }}</span>
                            <span class="badge bg-label-warning rounded-pill small">{{ $counts['active_classes'] }} {{ __('active') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('academic.classes.index') }}" class="btn btn-sm btn-icon btn-label-warning rounded-circle" title="{{ __('View') }}">
                        <i class="bx bx-arrow-to-right"></i>
                    </a>
                </div>
                <div class="my-2">
                    <h3 class="odoo-kpi-number mb-0">{{ number_format($counts['classes']) }}</h3>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top text-muted small">
                    <span class="text-success"><i class="bx bx-check-circle me-1"></i>{{ $counts['active_classes'] }} {{ __('Active') }}</span>
                    <span class="text-warning"><i class="bx bx-layer me-1"></i>{{ $counts['active_stages'] }} {{ __('stages') }}</span>
                </div>
            </div>
        </div>

        <!-- 4. Academic Subjects -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="odoo-kpi-card border-kpi-indigo">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="odoo-kpi-icon icon-bg-indigo"><i class="bx bx-book"></i></div>
                        <div>
                            <span class="odoo-kpi-label d-block">{{ __('Subjects') }}</span>
                            <span class="badge rounded-pill small" style="background-color: #f0f0ff; color: #5a56df;">{{ $counts['active_subjects'] }} {{ __('active') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('academic.subjects.index') }}" class="btn btn-sm btn-icon rounded-circle" style="background-color: #f0f0ff; color: #5a56df;" title="{{ __('View') }}">
                        <i class="bx bx-arrow-to-right"></i>
                    </a>
                </div>
                <div class="my-2">
                    <h3 class="odoo-kpi-number mb-0">{{ number_format($counts['subjects']) }}</h3>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top text-muted small">
                    <span class="text-success"><i class="bx bx-check-circle me-1"></i>{{ $counts['active_subjects'] }} {{ __('Active') }}</span>
                    <span class="text-primary"><i class="bx bx-book-reader me-1"></i>{{ $counts['active_lessons'] }} {{ __('Lessons') }}</span>
                </div>
            </div>
        </div>

        <!-- 5. Academic Lessons -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="odoo-kpi-card border-kpi-rose">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="odoo-kpi-icon icon-bg-rose"><i class="bx bx-book-reader"></i></div>
                        <div>
                            <span class="odoo-kpi-label d-block">{{ __('Lessons') }}</span>
                            <span class="badge bg-label-danger rounded-pill small">{{ $counts['active_lessons'] }} {{ __('active') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('academic.lessons.index') }}" class="btn btn-sm btn-icon btn-label-danger rounded-circle" title="{{ __('View') }}">
                        <i class="bx bx-arrow-to-right"></i>
                    </a>
                </div>
                <div class="my-2">
                    <h3 class="odoo-kpi-number mb-0">{{ number_format($counts['lessons']) }}</h3>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top text-muted small">
                    <span class="text-success"><i class="bx bx-check-circle me-1"></i>{{ $counts['active_lessons'] }} {{ __('Active') }}</span>
                    <span class="text-danger"><i class="bx bx-book me-1"></i>{{ $counts['active_subjects'] }} {{ __('Subjects') }}</span>
                </div>
            </div>
        </div>

        <!-- 6. Curriculum Plans (Weekly & Monthly) -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="odoo-kpi-card border-kpi-green">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="odoo-kpi-icon icon-bg-green"><i class="bx bx-calendar-week"></i></div>
                        <div>
                            <span class="odoo-kpi-label d-block">{{ __('Curriculum Plans') }}</span>
                            <span class="badge bg-label-success rounded-pill small">{{ __('Weekly & Monthly Plans') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('academic.weekly-plans.index') }}" class="btn btn-sm btn-icon btn-label-success rounded-circle" title="{{ __('View') }}">
                        <i class="bx bx-arrow-to-right"></i>
                    </a>
                </div>
                <div class="my-2">
                    <h3 class="odoo-kpi-number mb-0">{{ number_format($counts['weekly_plans'] + $counts['monthly_plans']) }}</h3>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top text-muted small">
                    <span class="text-success"><i class="bx bx-calendar-week me-1"></i><strong>{{ $counts['weekly_plans'] }}</strong> {{ __('Weekly Plans') }}</span>
                    <span class="text-warning"><i class="bx bx-calendar-event me-1"></i><strong>{{ $counts['monthly_plans'] }}</strong> {{ __('Monthly Plans') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================
         3. DEDICATED STRUCTURE & CURRICULUM MODULES (3 CARDS PER ROW)
         ======================================================== -->
    <div class="row g-3 mb-4">
        <!-- 1. Academic Structure (Years & Stages) -->
        <div class="col-md-6 col-xl-4">
            <div class="odoo-app-card">
                <div class="odoo-app-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="odoo-kpi-icon icon-bg-plum" style="width: 36px; height: 36px; font-size: 18px;">
                            <i class="bx bx-calendar"></i>
                        </div>
                        <h6 class="mb-0 fw-bold">{{ __('Academic Years & Lifecycle') }}</h6>
                    </div>
                    <span class="badge bg-label-primary rounded-pill">{{ $counts['active_years'] }} {{ __('Active') }}</span>
                </div>
                <div class="odoo-app-body">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 border rounded-3 bg-light text-center">
                                <span class="d-block text-muted small">{{ __('Academic Years') }}</span>
                                <span class="fw-bold fs-6 text-dark">{{ $counts['years'] }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded-3 bg-light text-center">
                                <span class="d-block text-muted small">{{ __('Academic Stages') }}</span>
                                <span class="fw-bold fs-6 text-dark">{{ $counts['stages'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-1 small">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">{{ __('Current Academic Year') }}</span>
                            <span class="fw-semibold text-primary">{{ $currentYear ? $currentYear->name : '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">{{ __('Active Stages') }}</span>
                            <span class="fw-semibold text-success">{{ $counts['active_stages'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">{{ __('Active Classes') }}</span>
                            <span class="fw-semibold text-info">{{ $counts['active_classes'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="odoo-app-footer d-flex justify-content-between align-items-center">
                    <a href="{{ route('academic.academic-years.index') }}" class="btn btn-sm btn-label-primary">{{ __('Manage Academic Years') }}</a>
                    <a href="{{ route('academic.stages.index') }}" class="btn btn-sm btn-link text-muted">{{ __('Academic Stages') }}</a>
                </div>
            </div>
        </div>

        <!-- 2. Subjects & Lessons -->
        <div class="col-md-6 col-xl-4">
            <div class="odoo-app-card">
                <div class="odoo-app-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="odoo-kpi-icon icon-bg-teal" style="width: 36px; height: 36px; font-size: 18px;">
                            <i class="bx bx-book-open"></i>
                        </div>
                        <h6 class="mb-0 fw-bold">{{ __('Subjects & Lessons') }}</h6>
                    </div>
                    <span class="badge bg-label-info rounded-pill">{{ $counts['active_subjects'] }} {{ __('Active') }}</span>
                </div>
                <div class="odoo-app-body">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 border rounded-3 bg-light text-center">
                                <span class="d-block text-muted small">{{ __('Subjects') }}</span>
                                <span class="fw-bold fs-6 text-dark">{{ $counts['subjects'] }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded-3 bg-light text-center">
                                <span class="d-block text-muted small">{{ __('Lessons') }}</span>
                                <span class="fw-bold fs-6 text-dark">{{ $counts['lessons'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-1 small">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">{{ __('Active Subjects') }}</span>
                            <span class="fw-semibold text-success">{{ $counts['active_subjects'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">{{ __('Active Lessons') }}</span>
                            <span class="fw-semibold text-info">{{ $counts['active_lessons'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">{{ __('Active Classes') }}</span>
                            <span class="fw-semibold text-dark">{{ $counts['active_classes'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="odoo-app-footer d-flex justify-content-between align-items-center">
                    <a href="{{ route('academic.subjects.index') }}" class="btn btn-sm btn-label-info">{{ __('Subjects') }}</a>
                    <a href="{{ route('academic.lessons.index') }}" class="btn btn-sm btn-link text-muted">{{ __('Lessons') }}</a>
                </div>
            </div>
        </div>

        <!-- 3. Curriculum Planning (Weekly & Monthly) -->
        <div class="col-md-6 col-xl-4">
            <div class="odoo-app-card">
                <div class="odoo-app-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="odoo-kpi-icon icon-bg-green" style="width: 36px; height: 36px; font-size: 18px;">
                            <i class="bx bx-calendar-check"></i>
                        </div>
                        <h6 class="mb-0 fw-bold">{{ __('Curriculum Planning') }}</h6>
                    </div>
                    <span class="badge bg-label-success rounded-pill">{{ $counts['weekly_plans'] + $counts['monthly_plans'] }} {{ __('Plans') }}</span>
                </div>
                <div class="odoo-app-body">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 border rounded-3 bg-light text-center">
                                <span class="d-block text-muted small">{{ __('Weekly Plans') }}</span>
                                <span class="fw-bold fs-6 text-success">{{ $counts['weekly_plans'] }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded-3 bg-light text-center">
                                <span class="d-block text-muted small">{{ __('Monthly Plans') }}</span>
                                <span class="fw-bold fs-6 text-warning">{{ $counts['monthly_plans'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-1 small">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">{{ __('Active Weekly Plans') }}</span>
                            <span class="fw-semibold text-success">{{ $counts['active_weekly_plans'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">{{ __('Active Monthly Plans') }}</span>
                            <span class="fw-semibold text-warning">{{ $counts['active_monthly_plans'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">{{ __('Current Academic Year') }}</span>
                            <span class="fw-semibold text-primary">{{ $currentYear ? $currentYear->name : '-' }}</span>
                        </div>
                    </div>
                </div>
                <div class="odoo-app-footer d-flex justify-content-between align-items-center">
                    <a href="{{ route('academic.weekly-plans.index') }}" class="btn btn-sm btn-label-success">{{ __('Weekly Plans') }}</a>
                    <a href="{{ route('academic.monthly-plans.index') }}" class="btn btn-sm btn-link text-muted">{{ __('Monthly Plans') }}</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================
         4. VISUAL ANALYTICS (APEXCHARTS IN ODOO COLORS)
         ======================================================== -->
    <div class="row g-3 mb-4">
        <!-- Stage Distribution Chart -->
        <div class="col-xl-7">
            <div class="odoo-card h-100">
                <div class="odoo-card-header">
                    <h5 class="mb-0 fw-bold" style="color: var(--odoo-primary);">{{ __('Academic Structure by Stage') }}</h5>
                    <a href="{{ route('academic.stages.index') }}" class="btn btn-sm btn-odoo-outline">{{ __('View Stages') }}</a>
                </div>
                <div class="card-body">
                    <div id="stageDistributionChart"></div>
                </div>
            </div>
        </div>

        <!-- Curriculum Content Distribution Bar Chart -->
        <div class="col-xl-5">
            <div class="odoo-card h-100">
                <div class="odoo-card-header">
                    <h5 class="mb-0 fw-bold">{{ __('Academic Curriculum & Content Repository') }}</h5>
                    <div class="d-flex gap-1">
                        <a href="{{ route('academic.subjects.index') }}" class="btn btn-sm btn-odoo-outline">{{ __('Subjects') }}</a>
                        <a href="{{ route('academic.lessons.index') }}" class="btn btn-sm btn-odoo-outline">{{ __('Lessons') }}</a>
                    </div>
                </div>
                <div class="card-body">
                    <div id="contentDistributionChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================
         5. DETAILED OPERATIONAL TABLES (ACADEMIC YEARS & STAGES)
         ======================================================== -->
    <div class="row g-3 mb-4">
        <!-- Academic Years Table -->
        <div class="col-xl-6">
            <div class="odoo-card h-100">
                <div class="odoo-card-header">
                    <h5 class="mb-0 fw-bold" style="color: var(--odoo-primary);">{{ __('Academic Years & Lifecycle') }}</h5>
                    <a href="{{ route('academic.academic-years.index') }}" class="btn btn-sm btn-odoo-outline">{{ __('Manage Academic Years') }}</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-odoo mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Academic Year') }}</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Duration Period') }}</th>
                                <th>{{ __('Lifecycle Status') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentYears as $year)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bx bx-calendar-star fs-4" style="color: var(--odoo-primary);"></i>
                                            <span class="fw-bold">{{ $year->name ?: $year->code }}</span>
                                            @if ($year->is_current)
                                                <span class="badge bg-label-success rounded-pill ms-2"><i class="bx bx-check me-1"></i>{{ __('Current Academic Year') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td><code>{{ $year->code ?: '-' }}</code></td>
                                    <td>
                                        <i class="bx bx-time text-muted me-1"></i>
                                        {{ optional($year->start_date)->format('Y-m-d') }} - {{ optional($year->end_date)->format('Y-m-d') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-label-{{ $year->status === 'active' ? 'success' : 'secondary' }} rounded-pill">
                                            {{ __($year->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('academic.academic-years.index') }}" class="btn btn-sm btn-icon btn-label-secondary" title="{{ __('Manage') }}">
                                            <i class="bx bx-cog"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        {{ __('No academic years registered') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Academic Stages Overview -->
        <div class="col-xl-6">
            <div class="odoo-card h-100">
                <div class="odoo-card-header">
                    <h5 class="mb-0 fw-bold">{{ __('Academic Stages') }}</h5>
                    <a href="{{ route('academic.stages.index') }}" class="btn btn-sm btn-odoo-outline">{{ __('View Stages') }}</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-odoo mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Stage') }}</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Classes') }}</th>
                                <th>{{ __('Subjects') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stagesList as $stage)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar avatar-xs rounded bg-label-primary d-flex align-items-center justify-content-center">
                                                <i class="bx bx-layer"></i>
                                            </div>
                                            <span class="fw-bold">{{ $stage->name }}</span>
                                        </div>
                                    </td>
                                    <td><code>{{ $stage->code ?: '-' }}</code></td>
                                    <td><span class="badge bg-label-info rounded-pill">{{ $stage->classes_count }}</span></td>
                                    <td><span class="badge bg-label-warning rounded-pill">{{ $stage->subjects_count }}</span></td>
                                    <td>
                                        <span class="badge bg-label-{{ $stage->status === 'active' ? 'success' : 'secondary' }} rounded-pill">
                                            {{ __($stage->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('academic.stages.index') }}" class="btn btn-sm btn-icon btn-label-secondary" title="{{ __('Manage') }}">
                                            <i class="bx bx-show"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        {{ __('No stages data available') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================
         6. CURRICULUM RECENT ASSETS (PLANS & LESSONS)
         ======================================================== -->
    <div class="row g-3">
        <!-- Recent Weekly & Monthly Plans -->
        <div class="col-xl-6">
            <div class="odoo-card h-100">
                <div class="odoo-card-header">
                    <h5 class="mb-0 fw-bold">{{ __('Curriculum Plans') }}</h5>
                    <div class="d-flex gap-1">
                        <a href="{{ route('academic.weekly-plans.index') }}" class="btn btn-sm btn-odoo-outline">{{ __('Weekly Plans') }}</a>
                        <a href="{{ route('academic.monthly-plans.index') }}" class="btn btn-sm btn-odoo-outline">{{ __('Monthly Plans') }}</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-tabs nav-fill border-bottom" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active py-2" data-bs-toggle="tab" data-bs-target="#tab-weekly" role="tab">
                                <i class="bx bx-calendar-week me-1"></i>{{ __('Weekly Plans') }} ({{ count($recentWeeklyPlans) }})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link py-2" data-bs-toggle="tab" data-bs-target="#tab-monthly" role="tab">
                                <i class="bx bx-calendar-event me-1"></i>{{ __('Monthly Plans') }} ({{ count($recentMonthlyPlans) }})
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content p-3">
                        <!-- Weekly Plans Tab -->
                        <div class="tab-pane fade show active" id="tab-weekly" role="tabpanel">
                            @forelse ($recentWeeklyPlans as $plan)
                                <div class="d-flex align-items-center justify-content-between p-2 mb-2 rounded border bg-light">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm rounded bg-label-success d-flex align-items-center justify-content-center">
                                            <i class="bx bx-calendar-week"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $plan->title }}</h6>
                                            <small class="text-muted">
                                                {{ optional($plan->academicStage)->name ?: '-' }}
                                                @if($plan->academicClass) • {{ $plan->academicClass->name }} @endif
                                                • {{ optional($plan->start_date)->format('Y-m-d') }}
                                            </small>
                                        </div>
                                    </div>
                                    <span class="badge bg-label-{{ $plan->status === 'active' ? 'success' : 'secondary' }} rounded-pill">{{ __($plan->status) }}</span>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    {{ __('No data available') }}
                                </div>
                            @endforelse
                        </div>

                        <!-- Monthly Plans Tab -->
                        <div class="tab-pane fade" id="tab-monthly" role="tabpanel">
                            @forelse ($recentMonthlyPlans as $plan)
                                <div class="d-flex align-items-center justify-content-between p-2 mb-2 rounded border bg-light">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm rounded bg-label-warning d-flex align-items-center justify-content-center">
                                            <i class="bx bx-calendar-event"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $plan->title }}</h6>
                                            <small class="text-muted">
                                                {{ optional($plan->academicStage)->name ?: '-' }}
                                                • {{ optional($plan->start_date)->format('Y-m-d') }} - {{ optional($plan->end_date)->format('Y-m-d') }}
                                            </small>
                                        </div>
                                    </div>
                                    <span class="badge bg-label-{{ $plan->status === 'active' ? 'success' : 'secondary' }} rounded-pill">{{ __($plan->status) }}</span>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    {{ __('No data available') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Lessons & Subjects -->
        <div class="col-xl-6">
            <div class="odoo-card h-100">
                <div class="odoo-card-header">
                    <h5 class="mb-0 fw-bold">{{ __('Subjects & Lessons') }}</h5>
                    <div class="d-flex gap-1">
                        <a href="{{ route('academic.subjects.index') }}" class="btn btn-sm btn-odoo-outline">{{ __('Subjects') }}</a>
                        <a href="{{ route('academic.lessons.index') }}" class="btn btn-sm btn-odoo-outline">{{ __('Lessons') }}</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-odoo mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Lesson') }}</th>
                                <th>{{ __('Subject') }}</th>
                                <th>{{ __('Stage') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentLessons as $lesson)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar avatar-xs rounded bg-label-danger d-flex align-items-center justify-content-center">
                                                <i class="bx bx-book-reader"></i>
                                            </div>
                                            <span class="fw-bold">{{ $lesson->title }}</span>
                                        </div>
                                    </td>
                                    <td><span class="small">{{ optional($lesson->academicSubject)->name ?: '-' }}</span></td>
                                    <td><span class="small">{{ optional($lesson->academicStage)->name ?: '-' }}</span></td>
                                    <td>
                                        <span class="badge bg-label-{{ $lesson->status === 'active' ? 'success' : 'secondary' }} rounded-pill">
                                            {{ __($lesson->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('academic.lessons.index') }}" class="btn btn-sm btn-icon btn-label-secondary" title="{{ __('Manage') }}">
                                            <i class="bx bx-show"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        {{ __('No data available') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isRtl = document.documentElement.dir === 'rtl';

    // Odoo Signature Colors
    const odooColors = {
        plum: '#714B67',
        teal: '#017E84',
        amber: '#e59866',
        green: '#28A745',
        danger: '#e46a76',
        indigo: '#5a56df'
    };

    const baseChartConfig = {
        chart: {
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        legend: {
            position: 'bottom',
            horizontalAlign: isRtl ? 'right' : 'left',
            fontFamily: 'inherit'
        }
    };

    // 1. Stage Distribution (Classes & Subjects Grouped Bar)
    const stageData = @json($stageDistribution);
    if (document.querySelector('#stageDistributionChart')) {
        new ApexCharts(document.querySelector('#stageDistributionChart'), {
            ...baseChartConfig,
            chart: {
                ...baseChartConfig.chart,
                type: 'bar',
                height: 310
            },
            series: [
                {
                    name: @json(__('Classes')),
                    data: stageData.map(item => item.classes)
                },
                {
                    name: @json(__('Subjects')),
                    data: stageData.map(item => item.subjects)
                }
            ],
            xaxis: {
                categories: stageData.map(item => item.label),
                labels: { style: { fontFamily: 'inherit' } }
            },
            colors: [odooColors.plum, odooColors.teal],
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: '45%',
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: {
                enabled: true,
                offsetY: -18,
                style: { fontSize: '11px', colors: ['#495057'] }
            },
            noData: { text: @json(__('No stages data available')) },
            grid: { borderColor: '#f1ecf0', strokeDashArray: 4 }
        }).render();
    }

    // 2. Curriculum Content Distributed Bar Chart
    const contentData = @json($contentDistribution);
    if (document.querySelector('#contentDistributionChart')) {
        new ApexCharts(document.querySelector('#contentDistributionChart'), {
            ...baseChartConfig,
            chart: {
                ...baseChartConfig.chart,
                type: 'bar',
                height: 310
            },
            series: [{
                name: @json(__('Total Count')),
                data: contentData.map(item => item.value)
            }],
            xaxis: {
                categories: contentData.map(item => item.label),
                labels: { style: { fontFamily: 'inherit', fontWeight: 600 } }
            },
            colors: [odooColors.plum, odooColors.teal, odooColors.green, odooColors.amber],
            plotOptions: {
                bar: {
                    borderRadius: 8,
                    columnWidth: '40%',
                    distributed: true
                }
            },
            dataLabels: {
                enabled: true,
                offsetY: -20,
                style: { fontSize: '12px', colors: ['#495057'] }
            },
            legend: { show: false },
            grid: { borderColor: '#f1ecf0', strokeDashArray: 4 }
        }).render();
    }
});
</script>
@endsection
