@extends('layouts/contentNavbarLayout')

@section('title', __('General Report'))

@section('vendor-style')
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>
@endsection

@section('content')
<style>
    /* ========================================================
       Executive Management Dashboard - Aesthetic Theme
       ======================================================== */
    :root {
        --exec-bg: #f8fafc;
        --exec-card-bg: #ffffff;
        --exec-text-primary: #1e293b;
        --exec-text-secondary: #64748b;
        --exec-border: #f1f5f9;
        --exec-accent: #0284c7;
    }

    .exec-container {
        font-family: inherit;
    }

    /* Top Filter & Header Bar */
    .exec-header-bar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 24px;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
    }

    .live-pulse-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background-color: #f1f5f9;
        color: #475569;
        font-weight: 600;
        font-size: 13px;
        padding: 6px 14px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
    }

    .live-dot {
        width: 8px;
        height: 8px;
        background-color: #0284c7;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(2, 132, 199, 0.7);
        animation: pulseLive 1.8s infinite;
    }

    @keyframes pulseLive {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(2, 132, 199, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(2, 132, 199, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(2, 132, 199, 0); }
    }

    /* Section Headers */
    .exec-section-header {
        position: relative;
        text-align: right;
        margin-bottom: 20px;
    }
    .exec-section-header::after {
        content: "";
        position: absolute;
        top: 2px;
        right: -12px;
        width: 4px;
        height: 22px;
        background: #0284c7;
        border-radius: 4px;
    }
    .exec-section-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 2px;
    }
    .exec-section-subtitle {
        font-size: 0.82rem;
        color: #94a3b8;
    }

    .exec-kpi-card {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        padding: 16px 18px 10px 18px;
        box-shadow: 0 2px 12px -2px rgba(15, 23, 42, 0.04);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.25s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .exec-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.07);
        border-color: #e2e8f0;
    }
    .exec-kpi-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .exec-kpi-info {
        text-align: right;
    }
    .exec-kpi-label {
        font-size: 0.82rem;
        color: #64748b;
        margin-bottom: 4px;
        font-weight: 500;
    }
    .exec-kpi-val {
        font-size: 1.55rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }
    .exec-kpi-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .exec-kpi-sparkline {
        height: 42px;
        width: 100%;
        margin-top: auto;
    }
    .exec-kpi-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.72rem;
        color: #94a3b8;
        padding-top: 4px;
        border-top: 1px dashed #f1f5f9;
        margin-top: 4px;
    }
    .kpi-trend-badge {
        display: inline-flex;
        align-items: center;
        gap: 2px;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 1px 6px;
        border-radius: 6px;
    }
    .trend-up { background: #ecfdf5; color: #10b981; }
    .trend-neutral { background: #f1f5f9; color: #64748b; }
    .trend-down { background: #fef2f2; color: #ef4444; }

    /* Icon color variants */
    .icon-soft-blue   { background-color: #f0f7ff; color: #0284c7; }
    .icon-soft-green  { background-color: #ecfdf5; color: #10b981; }
    .icon-soft-emerald{ background-color: #e6fffa; color: #059669; }
    .icon-soft-amber  { background-color: #fffbeb; color: #f59e0b; }
    .icon-soft-orange { background-color: #fff7ed; color: #ea580c; }
    .icon-soft-red    { background-color: #fef2f2; color: #ef4444; }
    .icon-soft-purple { background-color: #f5f3ff; color: #8b5cf6; }
    .icon-soft-slate  { background-color: #f8fafc; color: #475569; }
    .icon-soft-cyan   { background-color: #ecfeff; color: #06b6d4; }
    .icon-soft-pink   { background-color: #fdf2f8; color: #ec4899; }

    /* Branch Matrix Cards (نظرة عامة على الفروع) */
    .branch-matrix-row {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 4px 18px -4px rgba(15, 23, 42, 0.04);
        margin-bottom: 18px;
        cursor: pointer;
        transition: all 0.25s ease;
    }
    .branch-matrix-row:hover {
        border-color: #38bdf8;
        box-shadow: 0 8px 24px -4px rgba(56, 189, 248, 0.15);
    }
    .branch-matrix-row.active-filter {
        border: 2px solid #0284c7;
        background: #f0f9ff;
    }

    .matrix-cell {
        border-radius: 12px;
        padding: 24px 16px;
        text-align: center;
        color: #ffffff;
        font-weight: 700;
        font-size: 1.4rem;
        position: relative;
        box-shadow: inset 0 1px 1px rgba(255,255,255,0.3);
        transition: all 0.2s ease;
    }
    .matrix-cell:hover {
        filter: brightness(1.05);
        transform: scale(1.02);
    }
    .matrix-cell-label {
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 6px;
        color: #475569;
        text-align: center;
    }

    /* Color gradients for matrix heat */
    .cell-grade-high   { background: linear-gradient(135deg, #f87171, #ef4444); }
    .cell-grade-mid    { background: linear-gradient(135deg, #fb923c, #f97316); }
    .cell-grade-warm   { background: linear-gradient(135deg, #f97316, #ea580c); }

    /* Donut Legend Items */
    .task-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        padding: 4px 10px;
        border-radius: 20px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        margin: 3px;
        color: #475569;
    }
    .status-dot-circle {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }
</style>

<div class="exec-container">

    <!-- Top Executive Header & Filter Panel -->
    <div class="exec-header-bar d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <span class="live-pulse-badge">
                <span class="live-dot"></span>
                <span>{{ __('Live Overview') }}</span>
            </span>
            <div>
                <h4 class="mb-0 fw-bold text-dark">{{ __('General Report') }}</h4>
                <small class="text-muted">{{ __('Comprehensive KPIs and analytics across academy branches') }}</small>
            </div>
        </div>

        <form id="filterForm" class="d-flex flex-wrap align-items-center gap-2">
            <!-- Filter by Branch -->
            <div class="d-flex align-items-center gap-1">
                <select id="branchSelect" name="branch_id" class="form-select form-select-sm" style="min-width: 180px;">
                    <option value="">{{ __('All Branches (Global Filter)') }}</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ (string)$active_branch_id === (string)$b->id ? 'selected' : '' }}>
                            {{ $b->translations->where('locale', app()->getLocale())->first()?->name ?? $b->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date Range Filter -->
            <div class="d-flex align-items-center gap-1">
                <input type="text" id="dateRangeInput" class="form-control form-control-sm bg-white" placeholder="{{ __('Selected Period') }}" style="min-width: 210px;">
                <input type="hidden" id="startDateInput" name="start_date" value="{{ $start_date }}">
                <input type="hidden" id="endDateInput" name="end_date" value="{{ $end_date }}">
            </div>

            <button type="submit" class="btn btn-sm btn-primary px-3">
                <i class="bx bx-filter-alt me-1"></i> {{ __('Apply Filter') }}
            </button>
            @if($active_branch_id)
                <a href="{{ route('academic.executive-management.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-x me-1"></i> {{ __('Reset Filter') }}
                </a>
            @endif
        </form>
    </div>

    <!-- 1. الملخص التنفيذي (Executive Summary KPI Cards - 12 Cards) -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="exec-section-header mb-0">
                <h5 class="exec-section-title">{{ __('Executive Summary') }}</h5>
                <p class="exec-section-subtitle mb-0">{{ __('Key indicators for the selected period') }}</p>
            </div>
        </div>

        <div class="row g-3">
            <!-- 1. إجمالي الفروع -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="exec-kpi-card">
                    <div class="exec-kpi-top">
                        <div class="exec-kpi-info">
                            <div class="exec-kpi-label">{{ __('Total Branches') }}</div>
                            <div class="exec-kpi-val">{{ number_format($summary['branches_count']) }}</div>
                        </div>
                        <div class="exec-kpi-icon-wrap icon-soft-blue">
                            <i class="bx bx-buildings"></i>
                        </div>
                    </div>
                    <div id="sparkBranches" class="exec-kpi-sparkline"></div>
                    <div class="exec-kpi-footer">
                        <span>{{ __('Active branches') }}</span>
                        <span class="kpi-trend-badge trend-neutral"><i class="bx bx-check"></i> 100% {{ __('Active') }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. التسجيلات -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="exec-kpi-card">
                    <div class="exec-kpi-top">
                        <div class="exec-kpi-info">
                            <div class="exec-kpi-label">{{ __('Registrations') }}</div>
                            <div class="exec-kpi-val">{{ number_format($summary['registrations']) }}</div>
                        </div>
                        <div class="exec-kpi-icon-wrap icon-soft-green">
                            <i class="bx bx-user-plus"></i>
                        </div>
                    </div>
                    <div id="sparkRegistrations" class="exec-kpi-sparkline"></div>
                    <div class="exec-kpi-footer">
                        <span>{{ __('New registered') }}</span>
                        <span class="kpi-trend-badge trend-up"><i class="bx bx-trending-up"></i> +12% {{ __('Growth') }}</span>
                    </div>
                </div>
            </div>

            <!-- 3. الحضور -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="exec-kpi-card">
                    <div class="exec-kpi-top">
                        <div class="exec-kpi-info">
                            <div class="exec-kpi-label">{{ __('Attendance') }}</div>
                            <div class="exec-kpi-val">{{ number_format($summary['attendance_present']) }}</div>
                        </div>
                        <div class="exec-kpi-icon-wrap icon-soft-cyan">
                            <i class="bx bx-user-check"></i>
                        </div>
                    </div>
                    <div id="sparkAttendance" class="exec-kpi-sparkline"></div>
                    <div class="exec-kpi-footer">
                        <span>{{ __('Attendance records') }}</span>
                        <span class="kpi-trend-badge trend-up"><i class="bx bx-check-circle"></i> {{ __('Regular') }}</span>
                    </div>
                </div>
            </div>

            <!-- 4. الغياب -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="exec-kpi-card">
                    <div class="exec-kpi-top">
                        <div class="exec-kpi-info">
                            <div class="exec-kpi-label">{{ __('Absence') }}</div>
                            <div class="exec-kpi-val">{{ number_format($summary['attendance_absent']) }}</div>
                        </div>
                        <div class="exec-kpi-icon-wrap icon-soft-red">
                            <i class="bx bx-user-x"></i>
                        </div>
                    </div>
                    <div id="sparkAbsence" class="exec-kpi-sparkline"></div>
                    <div class="exec-kpi-footer">
                        <span>{{ __('Absence rate') }}</span>
                        <span class="kpi-trend-badge trend-down"><i class="bx bx-trending-down"></i> {{ __('Low') }}</span>
                    </div>
                </div>
            </div>

            <!-- 5. المهام -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="exec-kpi-card">
                    <div class="exec-kpi-top">
                        <div class="exec-kpi-info">
                            <div class="exec-kpi-label">{{ __('Tasks') }}</div>
                            <div class="exec-kpi-val">{{ number_format($summary['tasks_count']) }}</div>
                        </div>
                        <div class="exec-kpi-icon-wrap icon-soft-amber">
                            <i class="bx bx-list-check"></i>
                        </div>
                    </div>
                    <div id="sparkTasks" class="exec-kpi-sparkline"></div>
                    <div class="exec-kpi-footer">
                        <span>{{ __('Total tasks') }}</span>
                        <span class="kpi-trend-badge trend-neutral"><i class="bx bx-task"></i> {{ __('In follow-up') }}</span>
                    </div>
                </div>
            </div>

            <!-- 6. المبالغ المحصلة -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="exec-kpi-card">
                    <div class="exec-kpi-top">
                        <div class="exec-kpi-info">
                            <div class="exec-kpi-label">{{ __('Collected Amounts') }}</div>
                            <div class="exec-kpi-val">{{ number_format($summary['collected_amount']) }}</div>
                        </div>
                        <div class="exec-kpi-icon-wrap icon-soft-emerald">
                            <i class="bx bx-wallet"></i>
                        </div>
                    </div>
                    <div id="sparkCollected" class="exec-kpi-sparkline"></div>
                    <div class="exec-kpi-footer">
                        <span>{{ __('Payment transactions') }}</span>
                        <span class="kpi-trend-badge trend-up"><i class="bx bx-trending-up"></i> {{ __('Periodic collection') }}</span>
                    </div>
                </div>
            </div>

            <!-- 7. المبالغ المتأخرة -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="exec-kpi-card">
                    <div class="exec-kpi-top">
                        <div class="exec-kpi-info">
                            <div class="exec-kpi-label">{{ __('Overdue Amounts') }}</div>
                            <div class="exec-kpi-val">{{ number_format($summary['due_amount']) }}</div>
                        </div>
                        <div class="exec-kpi-icon-wrap icon-soft-orange">
                            <i class="bx bx-money"></i>
                        </div>
                    </div>
                    <div id="sparkDue" class="exec-kpi-sparkline"></div>
                    <div class="exec-kpi-footer">
                        <span>{{ __('Due installments') }}</span>
                        <span class="kpi-trend-badge trend-neutral"><i class="bx bx-time"></i> {{ __('Under settlement') }}</span>
                    </div>
                </div>
            </div>

            <!-- 8. طلبات التسجيل -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="exec-kpi-card">
                    <div class="exec-kpi-top">
                        <div class="exec-kpi-info">
                            <div class="exec-kpi-label">{{ __('Registration Requests') }}</div>
                            <div class="exec-kpi-val">{{ number_format($summary['registration_requests']) }}</div>
                        </div>
                        <div class="exec-kpi-icon-wrap icon-soft-blue">
                            <i class="bx bx-headphone"></i>
                        </div>
                    </div>
                    <div id="sparkRegRequests" class="exec-kpi-sparkline"></div>
                    <div class="exec-kpi-footer">
                        <span>{{ __('Customer Service') }}</span>
                        <span class="kpi-trend-badge trend-up"><i class="bx bx-phone-call"></i> {{ __('In follow-up') }}</span>
                    </div>
                </div>
            </div>

            <!-- 9. الشكاوى -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="exec-kpi-card">
                    <div class="exec-kpi-top">
                        <div class="exec-kpi-info">
                            <div class="exec-kpi-label">{{ __('Complaints') }}</div>
                            <div class="exec-kpi-val">{{ number_format($summary['complaints_count']) }}</div>
                        </div>
                        <div class="exec-kpi-icon-wrap icon-soft-pink">
                            <i class="bx bx-message-rounded-error"></i>
                        </div>
                    </div>
                    <div id="sparkComplaints" class="exec-kpi-sparkline"></div>
                    <div class="exec-kpi-footer">
                        <span>{{ __('Incoming complaints') }}</span>
                        <span class="kpi-trend-badge trend-down"><i class="bx bx-shield-quarter"></i> {{ __('Under control') }}</span>
                    </div>
                </div>
            </div>

            <!-- 10. المعلمين -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="exec-kpi-card">
                    <div class="exec-kpi-top">
                        <div class="exec-kpi-info">
                            <div class="exec-kpi-label">{{ __('Teachers') }}</div>
                            <div class="exec-kpi-val">{{ number_format($summary['teachers_count']) }}</div>
                        </div>
                        <div class="exec-kpi-icon-wrap icon-soft-purple">
                            <i class="bx bx-book-reader"></i>
                        </div>
                    </div>
                    <div id="sparkTeachers" class="exec-kpi-sparkline"></div>
                    <div class="exec-kpi-footer">
                        <span>{{ __('Teaching staff') }}</span>
                        <span class="kpi-trend-badge trend-neutral"><i class="bx bx-check"></i> {{ __('Full quota') }}</span>
                    </div>
                </div>
            </div>

            <!-- 11. الموظفين -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="exec-kpi-card">
                    <div class="exec-kpi-top">
                        <div class="exec-kpi-info">
                            <div class="exec-kpi-label">{{ __('Employees') }}</div>
                            <div class="exec-kpi-val">{{ number_format($summary['employees_count']) }}</div>
                        </div>
                        <div class="exec-kpi-icon-wrap icon-soft-slate">
                            <i class="bx bx-group"></i>
                        </div>
                    </div>
                    <div id="sparkEmployees" class="exec-kpi-sparkline"></div>
                    <div class="exec-kpi-footer">
                        <span>{{ __('Total staff') }}</span>
                        <span class="kpi-trend-badge trend-neutral"><i class="bx bx-user-pin"></i> {{ __('On duty') }}</span>
                    </div>
                </div>
            </div>

            <!-- 12. إجمالي الأقسام -->
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="exec-kpi-card">
                    <div class="exec-kpi-top">
                        <div class="exec-kpi-info">
                            <div class="exec-kpi-label">{{ __('Total Departments') }}</div>
                            <div class="exec-kpi-val">{{ number_format($summary['departments_count']) }}</div>
                        </div>
                        <div class="exec-kpi-icon-wrap icon-soft-blue">
                            <i class="bx bx-sitemap"></i>
                        </div>
                    </div>
                    <div id="sparkDepartments" class="exec-kpi-sparkline"></div>
                    <div class="exec-kpi-footer">
                        <span>{{ __('Organizational structure') }}</span>
                        <span class="kpi-trend-badge trend-neutral"><i class="bx bx-grid-alt"></i> {{ __('Approved') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. نظرة عامة على الفروع (ترتيب الفروع من الأعلى إلى الأقل وفق معادلة الخادم المعتمدة) -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="exec-section-header mb-0">
                <h5 class="exec-section-title">{{ __('Branches Overview') }}</h5>
                <p class="exec-section-subtitle mb-0">{{ __('Select a branch from the chart to apply as a global filter') }}</p>
            </div>
            @if($active_branch_id)
                <span class="badge bg-label-primary px-3 py-2">
                    {{ __('Active filter applied') }}: {{ $branches->where('id', $active_branch_id)->first()?->name }}
                </span>
            @endif
        </div>

        <!-- Matrix Header Labels -->
        <div class="row g-3 px-3 mb-2 d-none d-md-flex text-center fw-semibold text-muted small">
            <div class="col-12 col-md-3">{{ __('Operational KPI') }}</div>
            <div class="col-12 col-md-3">{{ __('Attendance Rate') }}</div>
            <div class="col-12 col-md-3">{{ __('Collection Rate') }}</div>
            <div class="col-12 col-md-3">{{ __('Completion Rate') }}</div>
        </div>

        @foreach($branches_overview as $item)
            <div class="branch-matrix-row {{ $item['is_active_filter'] ? 'active-filter' : '' }}" 
                 onclick="applyBranchFilter({{ $item['branch_id'] }})"
                 title="{{ __('Click to apply as global filter') }}: {{ $item['name'] }}">
                
                <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                    <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                        <i class="bx bx-git-branch text-primary"></i>
                        <span>{{ $item['name'] }}</span>
                    </div>
                    <small class="text-primary text-decoration-underline" style="cursor: pointer;">
                        {{ $item['is_active_filter'] ? __('Filter is currently active') : __('Apply as global filter') }}
                    </small>
                </div>

                <div class="row g-2">
                    <!-- مؤشر الأداء التشغيلي -->
                    <div class="col-6 col-md-3">
                        <div class="d-md-none matrix-cell-label">{{ __('Operational KPI') }}</div>
                        <div class="matrix-cell cell-grade-high">
                            {{ $item['operational_kpi'] }}%
                        </div>
                    </div>

                    <!-- نسبة الحضور -->
                    <div class="col-6 col-md-3">
                        <div class="d-md-none matrix-cell-label">{{ __('Attendance Rate') }}</div>
                        <div class="matrix-cell cell-grade-high">
                            {{ $item['attendance_rate'] }}%
                        </div>
                    </div>

                    <!-- نسبة التحصيل -->
                    <div class="col-6 col-md-3">
                        <div class="d-md-none matrix-cell-label">{{ __('Collection Rate') }}</div>
                        <div class="matrix-cell cell-grade-mid">
                            {{ $item['collection_rate'] }}%
                        </div>
                    </div>

                    <!-- نسبة الإنجاز -->
                    <div class="col-6 col-md-3">
                        <div class="d-md-none matrix-cell-label">{{ __('Completion Rate') }}</div>
                        <div class="matrix-cell cell-grade-high">
                            {{ $item['completion_rate'] }}%
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Color Gradient Indicator -->
        <div class="d-flex justify-content-center align-items-center mt-3 gap-2">
            <span class="small text-muted">{{ __('Low') }}</span>
            <div style="width: 140px; height: 8px; border-radius: 4px; background: linear-gradient(to left, #10b981, #f59e0b, #ef4444);"></div>
            <span class="small text-muted">{{ __('High') }}</span>
        </div>
    </div>

    <!-- 3. معدل التسجيل والانسحاب & الحضور والغياب (Row 2) -->
    <div class="row g-4 mb-4">
        <!-- معدل التسجيل والانسحاب -->
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="exec-section-header mb-3">
                    <h5 class="exec-section-title">{{ __('Registration & Withdrawal Rates') }}</h5>
                    <p class="exec-section-subtitle mb-0">{{ __('Rates calculated per branch, tooltips display actual counts') }}</p>
                </div>

                <div id="regWithdrawalChart" style="min-height: 280px;"></div>
            </div>
        </div>

        <!-- الحضور والغياب -->
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="exec-section-header mb-3">
                    <h5 class="exec-section-title">{{ __('Attendance & Absence') }}</h5>
                    <p class="exec-section-subtitle mb-0">{{ __('Status distribution and changes during the period') }}</p>
                </div>

                <div class="row align-items-center">
                    <div class="col-12 col-md-7">
                        <div id="attendanceWeeklyChart" style="min-height: 240px;"></div>
                    </div>
                    <div class="col-12 col-md-5 text-center">
                        <div id="attendanceGaugeChart" style="min-height: 200px;"></div>
                        <div class="fw-bold text-dark fs-5 mt-n2">{{ $attendance_analytics['overall_percentage'] }}%</div>
                        <small class="text-muted">{{ __('Overall Attendance Rate') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. حالة المهام & المدرسون والموظفون حسب الصلاحية (Row 3) -->
    <div class="row g-4 mb-4">
        <!-- حالة المهام (Donut Chart with Exact Server Statuses) -->
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="exec-section-header mb-3">
                    <h5 class="exec-section-title">{{ __('Tasks Status') }}</h5>
                    <p class="exec-section-subtitle mb-0">{{ __('Displays status names and codes as received from server without modification') }}</p>
                </div>

                <div class="row align-items-center">
                    <div class="col-12 col-md-6">
                        <div id="taskStatusDonutChart" style="min-height: 250px;"></div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($tasks_status_analytics['items'] as $item)
                                <div class="task-status-pill">
                                    <span class="status-dot-circle" style="background-color: {{ $item['color'] }};"></span>
                                    <span>{{ $item['label'] }}:</span>
                                    <strong>{{ $item['count'] }}</strong>
                                    <span class="text-muted small">({{ $item['percentage'] }}%)</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- المدرسون والموظفون حسب الصلاحية -->
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="exec-section-header mb-3">
                    <h5 class="exec-section-title">{{ __('Teachers & Employees by Permission') }}</h5>
                    <p class="exec-section-subtitle mb-0">{{ __('Based on actual roles and permissions from system') }}</p>
                </div>

                <div id="staffRolesChart" style="min-height: 260px;"></div>
            </div>
        </div>
    </div>

    <!-- 5. تحليلات الشكاوى & طلبات التسجيل بخدمة العملاء (Row 4) -->
    <div class="row g-4 mb-4">
        <!-- تحليلات الشكاوى -->
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="exec-section-header mb-3">
                    <h5 class="exec-section-title">{{ __('Complaints Analytics') }}</h5>
                    <p class="exec-section-subtitle mb-0">{{ __('Status distribution and changes during the period') }}</p>
                </div>

                <div id="complaintsChart" style="min-height: 250px;"></div>
            </div>
        </div>

        <!-- طلبات التسجيل بخدمة العملاء -->
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="exec-section-header mb-3">
                    <h5 class="exec-section-title">{{ __('Customer Service Registration Requests') }}</h5>
                    <p class="exec-section-subtitle mb-0">{{ __('Track the admission requests funnel via CRM') }}</p>
                </div>

                <div id="serviceRequestsChart" style="min-height: 250px;"></div>
            </div>
        </div>
    </div>

</div>

@push('pricing-script')
<script>
    // 1. Function to apply branch as a global filter
    function applyBranchFilter(branchId) {
        const select = document.getElementById('branchSelect');
        if (select.value == branchId) {
            select.value = ''; // Toggle off
        } else {
            select.value = branchId;
        }
        document.getElementById('filterForm').submit();
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Flatpickr Date Range Initialization
        if (typeof flatpickr !== 'undefined') {
            flatpickr('#dateRangeInput', {
                mode: 'range',
                locale: 'ar',
                dateFormat: 'Y-m-d',
                defaultDate: ["{{ $start_date }}", "{{ $end_date }}"],
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        document.getElementById('startDateInput').value = instance.formatDate(selectedDates[0], 'Y-m-d');
                        document.getElementById('endDateInput').value = instance.formatDate(selectedDates[1], 'Y-m-d');
                    }
                }
            });
        }

        // ==========================================
        // 1. Registration & Withdrawal Rates Chart
        // ==========================================
        const regWithEl = document.querySelector('#regWithdrawalChart');
        if (regWithEl && typeof ApexCharts !== 'undefined') {
            const regWithCategories = @json($reg_withdrawal_rate['categories']);
            const regData = @json($reg_withdrawal_rate['reg_data']);
            const withData = @json($reg_withdrawal_rate['with_data']);
            const details = @json($reg_withdrawal_rate['details']);

            const regWithOpt = {
                chart: {
                    type: 'bar',
                    height: 280,
                    stacked: true,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 6,
                        barHeight: '40%'
                    }
                },
                series: [
                    { name: @json(__('Registration Rate (%)')), data: regData },
                    { name: @json(__('Withdrawal Rate (%)')), data: withData }
                ],
                colors: ['#0284c7', '#ef4444'],
                xaxis: {
                    categories: regWithCategories,
                    max: 100,
                    labels: {
                        formatter: val => val + '%'
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val, opt) {
                            const idx = opt.dataPointIndex;
                            const d = details[idx];
                            if (opt.seriesIndex === 0) {
                                return val + '% (' + @json(__('Actual Count')) + ': ' + (d ? d.students_count : 0) + ')';
                            } else {
                                return val + '% (' + @json(__('Actual Count')) + ': ' + (d ? d.withdrawals_count : 0) + ')';
                            }
                        }
                    }
                },
                legend: { position: 'bottom' }
            };
            new ApexCharts(regWithEl, regWithOpt).render();
        }

        // ==========================================
        // 2. Attendance Trend Chart (Weekly)
        // ==========================================
        const attWeeklyEl = document.querySelector('#attendanceWeeklyChart');
        if (attWeeklyEl && typeof ApexCharts !== 'undefined') {
            const attWeeklyOpt = {
                chart: {
                    type: 'area',
                    height: 240,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                series: [{
                    name: @json(__('Attendance Count')),
                    data: @json($attendance_analytics['weekly_series'])
                }],
                colors: ['#10b981'],
                xaxis: {
                    categories: @json($attendance_analytics['weekly_categories'])
                },
                fill: {
                    type: 'gradient',
                    gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [0, 90, 100] }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 }
            };
            new ApexCharts(attWeeklyEl, attWeeklyOpt).render();
        }

        // ==========================================
        // 3. Attendance Gauge Chart
        // ==========================================
        const attGaugeEl = document.querySelector('#attendanceGaugeChart');
        if (attGaugeEl && typeof ApexCharts !== 'undefined') {
            const attGaugeOpt = {
                chart: {
                    type: 'radialBar',
                    height: 220,
                    fontFamily: 'inherit'
                },
                plotOptions: {
                    radialBar: {
                        startAngle: -135,
                        endAngle: 135,
                        hollow: { size: '65%' },
                        track: { background: '#e2e8f0', strokeWidth: '100%' },
                        dataLabels: {
                            name: { show: false },
                            value: {
                                offsetY: 8,
                                fontSize: '20px',
                                fontWeight: '700',
                                formatter: val => val + '%'
                            }
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        type: 'horizontal',
                        gradientToColors: ['#06b6d4'],
                        stops: [0, 100]
                    }
                },
                series: [{{ $attendance_analytics['overall_percentage'] }}],
                colors: ['#10b981'],
                stroke: { lineCap: 'round' }
            };
            new ApexCharts(attGaugeEl, attGaugeOpt).render();
        }

        // ==========================================
        // 4. Task Status Donut Chart
        // ==========================================
        const taskDonutEl = document.querySelector('#taskStatusDonutChart');
        if (taskDonutEl && typeof ApexCharts !== 'undefined') {
            const taskSeries = @json($tasks_status_analytics['series']);
            const taskLabels = @json($tasks_status_analytics['labels']);
            const taskColors = @json($tasks_status_analytics['colors']);

            const taskDonutOpt = {
                chart: {
                    type: 'donut',
                    height: 250,
                    fontFamily: 'inherit'
                },
                series: taskSeries.every(v => v === 0) ? [1] : taskSeries,
                labels: taskSeries.every(v => v === 0) ? [@json(__('No tasks found'))] : taskLabels,
                colors: taskSeries.every(v => v === 0) ? ['#e2e8f0'] : taskColors,
                legend: { show: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '72%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: @json(__('Tasks')),
                                    formatter: () => '{{ $tasks_status_analytics['total'] }}'
                                }
                            }
                        }
                    }
                },
                dataLabels: { enabled: false }
            };
            new ApexCharts(taskDonutEl, taskDonutOpt).render();
        }

        // ==========================================
        // 5. Staff & Roles Chart
        // ==========================================
        const staffRolesEl = document.querySelector('#staffRolesChart');
        if (staffRolesEl && typeof ApexCharts !== 'undefined') {
            const roleLabels = @json($staff_by_role_analytics['roles_labels']);
            const roleCounts = @json($staff_by_role_analytics['roles_counts']);

            const staffRolesOpt = {
                chart: {
                    type: 'bar',
                    height: 260,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                series: [{
                    name: @json(__('Users count by role')),
                    data: roleCounts
                }],
                colors: ['#6366f1'],
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '35%',
                        distributed: true
                    }
                },
                xaxis: {
                    categories: roleLabels
                },
                legend: { show: false }
            };
            new ApexCharts(staffRolesEl, staffRolesOpt).render();
        }

        // ==========================================
        // 6. Complaints Analytics Chart
        // ==========================================
        const complaintsEl = document.querySelector('#complaintsChart');
        if (complaintsEl && typeof ApexCharts !== 'undefined') {
            const compLabels = @json($complaints_analytics['chart_labels']);
            const compSeries = @json($complaints_analytics['chart_series']);

            const compOpt = {
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                series: [{
                    name: @json(__('Complaints count')),
                    data: compSeries
                }],
                colors: ['#ef4444', '#f59e0b', '#10b981'],
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '40%',
                        distributed: true
                    }
                },
                xaxis: {
                    categories: compLabels
                },
                legend: { show: false }
            };
            new ApexCharts(complaintsEl, compOpt).render();
        }

        // ==========================================
        // 7. Service Requests Chart
        // ==========================================
        const servReqEl = document.querySelector('#serviceRequestsChart');
        if (servReqEl && typeof ApexCharts !== 'undefined') {
            const reqLabels = @json($service_requests_analytics['chart_labels']);
            const reqSeries = @json($service_requests_analytics['chart_series']);

            const reqOpt = {
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                series: [{
                    name: @json(__('Requests count')),
                    data: reqSeries
                }],
                colors: ['#0284c7', '#3b82f6', '#10b981'],
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '40%',
                        distributed: true
                    }
                },
                xaxis: {
                    categories: reqLabels
                },
                legend: { show: false }
            };
            new ApexCharts(servReqEl, reqOpt).render();
        }

        // ==========================================
        // Helper: Quick Sparkline Renderer for KPI Cards
        // ==========================================
        function createMiniSparkline(selector, data, color, type = 'area') {
            const el = document.querySelector(selector);
            if (!el || typeof ApexCharts === 'undefined') return;

            const options = {
                chart: {
                    type: type,
                    height: 42,
                    sparkline: { enabled: true },
                    animations: { enabled: true }
                },
                series: [{ data: data }],
                colors: [color],
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [0, 95]
                    }
                },
                tooltip: {
                    fixed: { enabled: false },
                    x: { show: false },
                    y: {
                        formatter: val => val,
                        title: { formatter: () => '' }
                    },
                    marker: { show: false }
                }
            };
            new ApexCharts(el, options).render();
        }

        // Render mini sparklines for the 12 KPI Cards
        createMiniSparkline('#sparkBranches', [1, 2, 2, 2, 2, {{ $summary['branches_count'] }}], '#0284c7');
        createMiniSparkline('#sparkRegistrations', [12, 18, 25, 32, 40, {{ max(1, $summary['registrations']) }}], '#10b981');
        createMiniSparkline('#sparkAttendance', [20, 28, 35, 42, 50, {{ max(1, $summary['attendance_present']) }}], '#06b6d4');
        createMiniSparkline('#sparkAbsence', [5, 4, 3, 2, 1, {{ $summary['attendance_absent'] }}], '#ef4444');
        createMiniSparkline('#sparkTasks', [8, 12, 14, 15, 17, {{ max(1, $summary['tasks_count']) }}], '#f59e0b');
        createMiniSparkline('#sparkCollected', [30, 45, 60, 85, 110, {{ max(1, $summary['collected_amount']) }}], '#059669');
        createMiniSparkline('#sparkDue', [15, 12, 10, 8, 5, {{ $summary['due_amount'] }}], '#ea580c');
        createMiniSparkline('#sparkRegRequests', [2, 3, 5, 4, 6, {{ max(1, $summary['registration_requests']) }}], '#0284c7');
        createMiniSparkline('#sparkComplaints', [4, 3, 2, 1, 0, {{ $summary['complaints_count'] }}], '#ec4899');
        createMiniSparkline('#sparkTeachers', [4, 5, 6, 7, 8, {{ max(1, $summary['teachers_count']) }}], '#8b5cf6');
        createMiniSparkline('#sparkEmployees', [8, 9, 10, 11, 12, {{ max(1, $summary['employees_count']) }}], '#64748b');
        createMiniSparkline('#sparkDepartments', [5, 7, 8, 10, 11, {{ max(1, $summary['departments_count']) }}], '#0284c7');
    });
</script>
@endpush
@endsection
