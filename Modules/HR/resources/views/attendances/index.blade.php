@extends('layouts/contentNavbarLayout')

@section('title', __('Attendance & Absence') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
    /* ========================================================================= */
    /* 1. Global Page Layout & Typography                                       */
    /* ========================================================================= */
    .attendance-page-root {
        direction: rtl;
        font-family: inherit;
        color: #1e293b;
    }

    /* Top Action Bar */
    .attendance-top-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }
    .attendance-main-title {
        font-size: 20px;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }
    .attendance-main-title i {
        font-size: 24px;
        color: #0f2b48;
    }

    /* Dark Navy "+ تسجيل حضور / إنصراف" Button */
    .btn-action-navy {
        background-color: #0b2545 !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 6px !important;
        padding: 8px 18px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        cursor: pointer !important;
        box-shadow: 0 1px 2px rgba(11, 37, 69, 0.2) !important;
        transition: background-color 0.2s ease !important;
    }
    .btn-action-navy:hover {
        background-color: #153965 !important;
        color: #ffffff !important;
    }

    /* View Switcher: "عرض قائمة" / "الجدول الشهري" */
    .attendance-view-switcher {
        display: inline-flex;
        align-items: center;
        background: #ffffff;
        border: 1px solid #dcdfe6;
        border-radius: 6px;
        padding: 3px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .view-switch-btn {
        border: none !important;
        background: transparent !important;
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        padding: 6px 14px !important;
        border-radius: 4px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }
    .view-switch-btn:hover {
        color: #0f2b48 !important;
    }
    .view-switch-btn.active {
        background-color: #e0ecf8 !important;
        color: #1d4ed8 !important;
    }

    /* ========================================================================= */
    /* 2. Floating-Label Filters Bar (Exact Match with Reference Image)          */
    /* ========================================================================= */
    .attendance-filters-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .floating-field-wrapper {
        position: relative;
        background: #ffffff;
        border: 1px solid #dcdfe6;
        border-radius: 6px;
        height: 42px;
        padding: 0 10px;
        display: flex;
        align-items: center;
        transition: border-color 0.2s ease;
    }
    .floating-field-wrapper:focus-within {
        border-color: #3b82f6;
    }
    .floating-field-title {
        position: absolute;
        top: -9px;
        right: 14px;
        background: #ffffff;
        padding: 0 4px;
        font-size: 11px;
        color: #64748b;
        font-weight: 500;
        line-height: 1;
        z-index: 2;
    }
    .floating-field-control {
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        background: transparent !important;
        width: 100% !important;
        font-size: 13px !important;
        color: #1e293b !important;
        font-weight: 500 !important;
        padding: 0 !important;
        cursor: pointer;
    }
    .btn-green-filter {
        background-color: #22c55e !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 6px !important;
        padding: 8px 22px !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        height: 42px !important;
        cursor: pointer !important;
        transition: background-color 0.2s ease !important;
    }
    .btn-green-filter:hover {
        background-color: #16a34a !important;
    }
    .btn-gray-filter {
        background-color: #f8fafc !important;
        color: #64748b !important;
        border: 1px solid #dcdfe6 !important;
        border-radius: 6px !important;
        padding: 8px 18px !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        height: 42px !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }
    .btn-gray-filter:hover {
        background-color: #f1f5f9 !important;
        color: #334155 !important;
    }

    /* ========================================================================= */
    /* 3. Subtitle & Legend Bar (Exact Match with Reference Image)               */
    /* ========================================================================= */
    .attendance-legend-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 16px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .legend-report-title {
        color: #16a34a;
        font-weight: 700;
        font-size: 16px;
        text-decoration: underline;
        text-underline-offset: 4px;
        white-space: nowrap;
    }
    .legend-symbols-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        font-size: 12px;
        color: #475569;
        font-weight: 500;
    }
    .legend-badge-item {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }

    /* ========================================================================= */
    /* 4. Strict Matrix Grid Table (Clean Borders, Perfectly Centered)           */
    /* ========================================================================= */
    .attendance-matrix-scroll-box {
        position: relative;
        overflow-x: auto;
        border: 1px solid #dcdfe6;
        border-radius: 6px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    table.matrix-attendance-table {
        table-layout: fixed !important;
        border-collapse: collapse !important;
        width: max-content !important;
        min-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        background-color: #ffffff !important;
        border: 1px solid #dcdfe6 !important;
    }
    table.matrix-attendance-table th,
    table.matrix-attendance-table td {
        border: 1px solid #dcdfe6 !important;
        padding: 0 !important;
        margin: 0 !important;
        text-align: center !important;
        vertical-align: middle !important;
        box-sizing: border-box !important;
        position: relative !important;
    }

    /* Column Widths & Heights */
    table.matrix-attendance-table th.col-matrix-day,
    table.matrix-attendance-table td.col-matrix-day {
        width: 42px !important;
        min-width: 42px !important;
        max-width: 42px !important;
        height: 52px !important;
        cursor: pointer !important;
        user-select: none !important;
    }
    table.matrix-attendance-table td.col-matrix-day:hover {
        background-color: #eff6ff !important;
        box-shadow: inset 0 0 0 2px #3b82f6 !important;
    }

    /* Sticky Employee Column */
    table.matrix-attendance-table th.col-sticky-emp,
    table.matrix-attendance-table td.col-sticky-emp {
        position: sticky !important;
        right: 0 !important;
        z-index: 10 !important;
        width: 220px !important;
        min-width: 220px !important;
        max-width: 240px !important;
        background-color: #ffffff !important;
        border-left: 2px solid #cbd5e1 !important;
        padding: 6px 12px !important;
        text-align: right !important;
    }
    table.matrix-attendance-table thead th.col-sticky-emp {
        background-color: #f8fafc !important;
        z-index: 15 !important;
        font-weight: 700 !important;
        font-size: 14px !important;
        color: #334155 !important;
    }
    table.matrix-attendance-table tbody tr:hover td.col-sticky-emp {
        background-color: #f8fafc !important;
    }

    /* Table Headers */
    table.matrix-attendance-table thead tr:first-child th {
        height: 36px !important;
        background-color: #f8fafc !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #334155 !important;
        border-bottom: 1px solid #dcdfe6 !important;
    }
    table.matrix-attendance-table thead tr:nth-child(2) th {
        height: 28px !important;
        background-color: #ffffff !important;
        font-size: 11px !important;
        font-weight: 500 !important;
        color: #64748b !important;
        border-bottom: 2px solid #cbd5e1 !important;
    }
    table.matrix-attendance-table thead th.today-highlight {
        background-color: #fef3c7 !important;
        color: #92400e !important;
        font-weight: 700 !important;
    }

    /* Exact Status Symbols */
    .sym-present-check {
        color: #10b981 !important;
        font-size: 16px !important;
        font-weight: 700 !important;
        display: block !important;
        line-height: 1 !important;
    }
    .sym-absent-cross {
        color: #ef4444 !important;
        font-size: 16px !important;
        font-weight: 700 !important;
        display: block !important;
        line-height: 1 !important;
    }
    .sym-restday-box {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 1px !important;
        color: #94a3b8 !important;
        line-height: 1 !important;
    }
    .sym-restday-box .ico {
        font-size: 16px !important;
    }
    .sym-restday-box .txt {
        font-size: 9px !important;
        color: #94a3b8 !important;
        white-space: nowrap !important;
    }
    .sym-future-dash {
        color: #cbd5e1 !important;
        font-size: 16px !important;
        display: block !important;
        line-height: 1 !important;
    }
    .sym-holiday-star {
        color: #eab308 !important;
        font-size: 15px !important;
    }
    .sym-late-clock {
        color: #ea580c !important;
        font-size: 15px !important;
        font-weight: bold !important;
    }
    .sym-half-day {
        color: #0284c7 !important;
        font-size: 14px !important;
        font-weight: bold !important;
    }
    .sym-leave-flag {
        color: #dc2626 !important;
        font-size: 14px !important;
    }

    /* Employee Card layout in Sticky Column */
    .emp-card-layout {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        width: 100% !important;
        gap: 8px !important;
    }
    .emp-card-name {
        font-weight: 600 !important;
        color: #1e293b !important;
        font-size: 13px !important;
        white-space: nowrap !important;
        text-overflow: ellipsis !important;
        overflow: hidden !important;
        text-align: right !important;
    }
    .emp-card-sub {
        font-size: 11px !important;
        color: #64748b !important;
        white-space: nowrap !important;
        text-overflow: ellipsis !important;
        overflow: hidden !important;
        text-align: right !important;
    }
    .emp-avatar-badge {
        width: 28px !important;
        height: 28px !important;
        border-radius: 50% !important;
        background-color: #cbd5e1 !important;
        color: #475569 !important;
        font-weight: 700 !important;
        font-size: 12px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
    }
</style>
@endsection

@section('content')
<div class="attendance-page-root">

    <!-- ========================================================================= -->
    <!-- 1. Top Header: Title & Action Controls                                    -->
    <!-- ========================================================================= -->
    <div class="attendance-top-bar">
        <!-- Title -->
        <div>
            <h4 class="attendance-main-title">
                <i class="bx bx-calendar-check"></i>
                <span>{{ __('الحضور والإنصراف') }}</span>
            </h4>
        </div>

        <!-- Left Controls -->
        <div class="d-flex align-items-center gap-2">
            <!-- Navy Quick Punch Button -->
            <button type="button" class="btn-action-navy" data-bs-toggle="offcanvas" data-bs-target="#offcanvasQuickPunch">
                <i class="bx bx-plus"></i>
                <span>{{ __('+ تسجيل حضور / إنصراف') }}</span>
            </button>

            <!-- Excel Import -->
            <button type="button" class="btn btn-outline-secondary btn-sm bg-white" data-bs-toggle="modal" data-bs-target="#modalImportExcel" style="height: 35px; border-radius: 6px; font-weight: 600;">
                <i class="bx bx-upload me-1"></i>
                <span>{{ __('استيراد إكسيل') }}</span>
            </button>

            <!-- Segmented Switcher -->
            <div class="attendance-view-switcher">
                <button type="button" class="view-switch-btn active" id="btnTabGrid" onclick="switchAttendanceTab('grid')">
                    <i class="bx bx-grid-alt"></i>
                    <span>{{ __('الجدول الشهري') }}</span>
                </button>
                <button type="button" class="view-switch-btn" id="btnTabList" onclick="switchAttendanceTab('list')">
                    <i class="bx bx-list-ul"></i>
                    <span>{{ __('عرض قائمة') }}</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-3" role="alert">
            <i class="bx bx-check-circle me-2 font-20"></i>
            <div class="flex-grow-1">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-3" role="alert">
            <i class="bx bx-error me-2 font-20"></i>
            <div class="flex-grow-1">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 2. Standard Filters Card (Matching other screens - Auto Filter on Change)   -->
    <!-- ========================================================================= -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('hr.attendances.index') }}" id="attendanceFilterForm">
                <div class="row g-3 align-items-end">
                    <!-- 1. Branch Filter -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                        <select name="branch_id" id="filter_branch_id" class="select2 form-select form-select-sm" data-placeholder="{{ __('All Branches') }}" data-allow-clear="true">
                            <option value="">{{ __('All Branches') }}</option>
                            @foreach ($branches as $br)
                                <option value="{{ $br->id }}" {{ $selectedBranch == $br->id ? 'selected' : '' }}>
                                    {{ $br->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2. Department Filter -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label fw-semibold text-heading small mb-1">{{ __('Department') }}</label>
                        <select name="department_id" id="filter_department_id" class="select2 form-select form-select-sm" data-placeholder="{{ __('All Departments') }}" data-allow-clear="true">
                            <option value="">{{ __('All Departments') }}</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}" {{ $selectedDepartment == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 3. Employee Filter -->
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label fw-semibold text-heading small mb-1">{{ __('Employee') }}</label>
                        <select name="employee_id" id="filter_employee_id" class="select2 form-select form-select-sm" data-placeholder="{{ __('All Employees') }}" data-allow-clear="true">
                            <option value="">{{ __('All Employees') }}</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $selectedEmployee == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 4. Month Filter -->
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label fw-semibold text-heading small mb-1">{{ __('Month') }}</label>
                        <select name="month" id="filter_month" class="select2 form-select form-select-sm">
                            @foreach ($months as $num => $name)
                                <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 5. Year Filter -->
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label fw-semibold text-heading small mb-1">{{ __('Year') }}</label>
                        <select name="year" id="filter_year" class="select2 form-select form-select-sm text-center">
                            @foreach ($years as $yr)
                                <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>
                                    {{ $yr }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. Legend & Report Subtitle Bar (Exact Replica of Reference Image)        -->
    <!-- ========================================================================= -->
    <div class="attendance-legend-card">
        <!-- Right Title -->
        <div>
            <span class="legend-report-title">
                {{ __('تقرير الحضور') }} : {{ $matrixData['month_name'] }} {{ $matrixData['year'] }}
            </span>
        </div>

        <!-- Left Legend List -->
        <div class="legend-symbols-row">
            <span class="legend-badge-item"><span style="color: #10b981; font-weight: bold; font-size: 14px;">✓</span> {{ __('حاضر') }}</span>
            <span class="legend-badge-item"><span style="color: #ef4444; font-weight: bold; font-size: 14px;">✕</span> {{ __('غائب') }}</span>
            <span class="legend-badge-item"><span style="color: #ea580c; font-weight: bold;">½</span> {{ __('نصف يوم') }}</span>
            <span class="legend-badge-item"><span style="color: #ef4444;">🚩</span> {{ __('إجازة') }}</span>
            <span class="legend-badge-item"><span style="color: #eab308;">⭐</span> {{ __('عطلة') }}</span>
            <span class="legend-badge-item"><span style="color: #94a3b8;">⃠</span> {{ __('يوم عطلة') }}</span>
            <span class="legend-badge-item"><span style="color: #94a3b8;">-</span> {{ __('المستقبل') }}</span>
            <span class="legend-badge-item"><span style="color: #f59e0b;">⏳</span> {{ __('قيد الانتظار') }}</span>
            <span class="legend-badge-item"><span style="color: #ea580c;">🕒</span> {{ __('متأخر') }}</span>
            <span class="legend-badge-item"><span style="color: #ef4444;">⇄</span> {{ __('انصراف مبكر') }}</span>
            <span class="legend-badge-item"><span style="color: #06b6d4;">⏱</span> {{ __('ساعات إضافية') }}</span>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. Monthly Grid Matrix Table                                              -->
    <!-- ========================================================================= -->
    <div id="gridMatrixBox">
        <div class="attendance-matrix-scroll-box">
            <table class="matrix-attendance-table">
                <thead>
                    <!-- Row 1: Day Numbers (1 .. 31) -->
                    <tr>
                        <th class="col-sticky-emp">{{ __('الموظف') }}</th>
                        @foreach ($matrixData['days'] as $d)
                            <th class="col-matrix-day {{ $d['is_today'] ? 'today-highlight' : '' }}">
                                {{ $d['day'] }}
                            </th>
                        @endforeach
                    </tr>
                    <!-- Row 2: Day Names (ثلاثاء، أربعاء، خميس...) -->
                    <tr>
                        <th class="col-sticky-emp" style="font-size: 11px !important; color: #64748b !important; font-weight: 500 !important;">
                            {{ __('الإجمالي / القسم') }}
                        </th>
                        @foreach ($matrixData['days'] as $d)
                            <th class="col-matrix-day {{ $d['is_today'] ? 'today-highlight' : '' }}">
                                {{ app()->getLocale() === 'ar' ? $d['day_name_ar'] : $d['day_name_en'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($matrixData['matrix_rows'] as $row)
                        <tr>
                            <!-- Sticky Employee Column -->
                            <td class="col-sticky-emp">
                                <div class="emp-card-layout">
                                    <div class="emp-avatar-badge">
                                        @if ($row['avatar'])
                                            <img src="{{ asset('storage/' . $row['avatar']) }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                        @else
                                            {{ strtoupper(mb_substr($row['employee_name'], 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="emp-card-info" style="overflow: hidden;">
                                        <div class="emp-card-name" title="{{ $row['employee_name'] }}">
                                            {{ $row['employee_name'] }}
                                        </div>
                                        <div class="emp-card-sub">
                                            {{ $row['employee_code'] }} • {{ $row['department'] ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- 1 to 31 Day Cells -->
                            @foreach ($matrixData['days'] as $d)
                                @php
                                    $cell = $row['days'][$d['day']] ?? null;
                                    $status = $cell['status'] ?? 'future';
                                    $isToday = $d['is_today'];
                                @endphp
                                <td class="col-matrix-day {{ $isToday ? 'bg-warning-subtle' : '' }}"
                                    data-employee-id="{{ $row['employee_id'] }}"
                                    data-employee-name="{{ e($row['employee_name']) }}"
                                    data-date="{{ $d['date'] }}"
                                    data-attendance-id="{{ $cell['attendance_id'] ?? '' }}"
                                    data-status="{{ $status }}"
                                    data-check-in="{{ $cell['check_in'] ?? '' }}"
                                    data-check-out="{{ $cell['check_out'] ?? '' }}"
                                    data-work-hours="{{ $cell['work_hours'] ?? 0 }}"
                                    data-late-minutes="{{ $cell['late_minutes'] ?? 0 }}"
                                    onclick="openAttendanceModal(this)">
                                    
                                    @if ($status === 'present')
                                        <span class="sym-present-check">✓</span>
                                    @elseif ($status === 'absent')
                                        <span class="sym-absent-cross">✕</span>
                                    @elseif ($status === 'rest_day')
                                        <div class="sym-restday-box">
                                            <span class="ico">⃠</span>
                                            <span class="txt">{{ __('يوم عطلة') }}</span>
                                        </div>
                                    @elseif ($status === 'holiday')
                                        <span class="sym-holiday-star">⭐</span>
                                    @elseif ($status === 'late')
                                        <span class="sym-late-clock">🕒</span>
                                    @elseif ($status === 'half_day')
                                        <span class="sym-half-day">½</span>
                                    @elseif ($status === 'on_leave')
                                        <span class="sym-leave-flag">🚩</span>
                                    @elseif ($status === 'future')
                                        <span class="sym-future-dash">-</span>
                                    @else
                                        <span class="sym-future-dash">-</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($matrixData['days']) + 1 }}" class="text-center py-5 text-muted">
                                <i class="bx bx-user-x font-36 mb-2 text-secondary d-block"></i>
                                <p class="mb-0">{{ __('لا توجد سجلات مطابقة للفلاتر المحددة.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. List View Table (DataTables)                                           -->
    <!-- ========================================================================= -->
    <div id="listTableViewBox" style="display: none;">
        <div class="card shadow-sm border-0">
            <div class="card-datatable table-responsive p-3">
                <table id="attendancesDataTable" class="table table-hover border-top w-100 text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>{{ __('الموظف') }}</th>
                            <th>{{ __('التاريخ') }}</th>
                            <th>{{ __('وقت الحضور') }}</th>
                            <th>{{ __('وقت الانصراف') }}</th>
                            <th>{{ __('ساعات العمل') }}</th>
                            <th>{{ __('التأخير / مبكر') }}</th>
                            <th>{{ __('الحالة') }}</th>
                            <th class="text-center" style="width: 100px;">{{ __('الإجراءات') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- Offcanvas: Quick Record Punch (+ تسجيل حضور / إنصراف)                     -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasQuickPunch" style="width: 480px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="bx bx-time-five text-primary font-22"></i>
            <span>{{ __('تسجيل حضور / إنصراف سريع') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form action="{{ route('hr.attendances.record-punch') }}" method="POST" id="quickPunchForm">
            @csrf

            <!-- Employee -->
            <div class="mb-3">
                <label class="form-label required fw-semibold" for="punch_employee_id">{{ __('الموظف') }}</label>
                <select name="employee_id" id="punch_employee_id" class="form-select select2" required>
                    <option value="">{{ __('اختر الموظف...') }}</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->employee_code }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Punch Type -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('نوع الحركة') }}</label>
                <div class="row g-2">
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="punch_type" id="punch_auto" value="auto" checked>
                        <label class="btn btn-outline-primary w-100 py-2 d-flex flex-column align-items-center gap-1" for="punch_auto">
                            <i class="bx bx-sync font-20"></i>
                            <span class="small">{{ __('تلقائي') }}</span>
                        </label>
                    </div>
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="punch_type" id="punch_in" value="check_in">
                        <label class="btn btn-outline-success w-100 py-2 d-flex flex-column align-items-center gap-1" for="punch_in">
                            <i class="bx bx-log-in-circle font-20"></i>
                            <span class="small">{{ __('حضور (In)') }}</span>
                        </label>
                    </div>
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="punch_type" id="punch_out" value="check_out">
                        <label class="btn btn-outline-danger w-100 py-2 d-flex flex-column align-items-center gap-1" for="punch_out">
                            <i class="bx bx-log-out-circle font-20"></i>
                            <span class="small">{{ __('انصراف (Out)') }}</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Date & Time -->
            <div class="mb-3">
                <label class="form-label required fw-semibold" for="punch_time">{{ __('التاريخ والوقت') }}</label>
                <input type="datetime-local" class="form-control" name="punch_time" id="punch_time" value="{{ now()->format('Y-m-d\TH:i') }}" required>
            </div>

            <!-- Source -->
            <div class="mb-3">
                <label class="form-label fw-semibold" for="punch_source">{{ __('المصدر') }}</label>
                <select name="source" id="punch_source" class="form-select">
                    <option value="manual">{{ __('يدوي من لوحة التحكم (Manual)') }}</option>
                    <option value="mobile_app">{{ __('بصمة الموبايل (Mobile App)') }}</option>
                    <option value="biometric_device">{{ __('جهاز بصمة (Biometric Device)') }}</option>
                    <option value="excel_import">{{ __('استيراد إكسيل (Excel Import)') }}</option>
                </select>
            </div>

            <!-- Notes -->
            <div class="mb-4">
                <label class="form-label fw-semibold" for="punch_notes">{{ __('ملاحظات') }}</label>
                <textarea name="notes" id="punch_notes" rows="2" class="form-control" placeholder="{{ __('ملاحظة أو سبب التسجيل...') }}"></textarea>
            </div>

            <div class="d-flex align-items-center justify-content-end gap-2 border-top pt-3">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('إلغاء') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="bx bx-check"></i>
                    <span>{{ __('تسجيل الحركة') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal: Edit / Detail Cell Modal                                           -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalCellDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-heading d-flex align-items-center gap-2">
                    <i class="bx bx-edit text-primary"></i>
                    <span>{{ __('تفاصيل / تعديل الحضور') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('hr.attendances.store') }}" method="POST" id="cellDetailForm">
                @csrf
                <div id="method_container"></div>
                <input type="hidden" name="employee_id" id="modal_employee_id">
                <input type="hidden" name="date" id="modal_date">

                <div class="modal-body p-4">
                    <div class="alert alert-primary d-flex align-items-center gap-2 py-2 px-3 mb-3">
                        <i class="bx bx-info-circle font-20"></i>
                        <div>
                            <strong id="modal_employee_name_text">-</strong>
                            <div class="small text-muted" id="modal_date_text">-</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Status -->
                        <div class="col-12">
                            <label class="form-label required fw-semibold" for="modal_status">{{ __('حالة الحضور') }}</label>
                            <select name="status" id="modal_status" class="form-select" required>
                                <option value="present">{{ __('حاضر (Present)') }}</option>
                                <option value="absent">{{ __('غائب (Absent)') }}</option>
                                <option value="late">{{ __('متأخر (Late)') }}</option>
                                <option value="half_day">{{ __('نصف يوم (Half Day)') }}</option>
                                <option value="on_leave">{{ __('إجازة (On Leave)') }}</option>
                                <option value="holiday">{{ __('عطلة رسمية (Holiday)') }}</option>
                                <option value="rest_day">{{ __('يوم راحة أسبوعية (Rest Day)') }}</option>
                            </select>
                        </div>

                        <!-- Check In & Check Out Times -->
                        <div class="col-6">
                            <label class="form-label fw-semibold" for="modal_check_in">{{ __('وقت الحضور') }}</label>
                            <input type="time" class="form-control" name="check_in" id="modal_check_in">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" for="modal_check_out">{{ __('وقت الانصراف') }}</label>
                            <input type="time" class="form-control" name="check_out" id="modal_check_out">
                        </div>

                        <!-- Working hours & Late min -->
                        <div class="col-6">
                            <label class="form-label fw-semibold" for="modal_total_working_hours">{{ __('إجمالي الساعات') }}</label>
                            <input type="number" step="0.1" class="form-control" name="total_working_hours" id="modal_total_working_hours" placeholder="8.0">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" for="modal_late_minutes">{{ __('دقائق التأخير') }}</label>
                            <input type="number" class="form-control" name="late_minutes" id="modal_late_minutes" placeholder="0">
                        </div>

                        <!-- Adjustment Reason -->
                        <div class="col-12">
                            <label class="form-label fw-semibold" for="modal_adjustment_reason">{{ __('سبب التعديل / ملاحظات') }}</label>
                            <textarea name="adjustment_reason" id="modal_adjustment_reason" rows="2" class="form-control" placeholder="{{ __('سبب التعديل اليدوي...') }}"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top py-2 px-4 d-flex justify-content-between">
                    <div>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="btnDeleteAttendance" style="display: none;" onclick="deleteCurrentAttendance()">
                            <i class="bx bx-trash"></i> {{ __('حذف السجل') }}
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('إلغاء') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> {{ __('حفظ التعديلات') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal: Import Excel Sheet                                                 -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalImportExcel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-heading d-flex align-items-center gap-2">
                    <i class="bx bx-file text-success"></i>
                    <span>{{ __('استيراد شيت الحضور والإنصراف') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('hr.attendances.import-excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 mb-3 small">
                        <i class="bx bx-info-circle me-1"></i>
                        {{ __('يرجى رفع ملف بصيغة CSV أو Excel يحتوي على الأعمدة: employee_code, date, check_in, check_out, status.') }}
                    </div>

                    <div class="mb-3">
                        <label class="form-label required fw-semibold" for="excel_file">{{ __('اختر ملف الإكسيل / CSV') }}</label>
                        <input type="file" name="file" id="excel_file" class="form-control" accept=".csv, .xlsx, .xls" required>
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3">
                        <span class="small text-muted">{{ __('تحميل نموذج ملف فارغ للإدخال:') }}</span>
                        <a href="{{ route('hr.attendances.download-template') }}" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                            <i class="bx bx-download"></i>
                            <span>{{ __('تحميل النموذج CSV') }}</span>
                        </a>
                    </div>
                </div>

                <div class="modal-footer border-top py-2 px-4">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('إلغاء') }}</button>
                    <button type="submit" class="btn btn-success d-flex align-items-center gap-2">
                        <i class="bx bx-upload"></i>
                        <span>{{ __('بدء الاستيراد والمعالجة') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="globalDeleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('scripts')
<script>
    let dataTableInstance = null;
    let currentAttendanceId = null;

    function switchAttendanceTab(view) {
        const gridBtn = document.getElementById('btnTabGrid');
        const listBtn = document.getElementById('btnTabList');
        const gridBox = document.getElementById('gridMatrixBox');
        const listBox = document.getElementById('listTableViewBox');

        if (view === 'grid') {
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
            gridBox.style.display = 'block';
            listBox.style.display = 'none';
        } else {
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');
            gridBox.style.display = 'none';
            listBox.style.display = 'block';

            if (!dataTableInstance) {
                dataTableInstance = $('#attendancesDataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('hr.attendances.data') }}',
                        data: function (d) {
                            d.employee_id   = $('#filter_employee_id').val();
                            d.month         = $('#filter_month').val();
                            d.year          = $('#filter_year').val();
                            d.branch_id     = $('#filter_branch_id').val();
                            d.department_id = $('#filter_department_id').val();
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'employee_name', name: 'employee.translations.name' },
                        { data: 'date', name: 'date' },
                        { data: 'check_in_badge', name: 'check_in', orderable: false },
                        { data: 'check_out_badge', name: 'check_out', orderable: false },
                        { data: 'total_working_hours', name: 'total_working_hours' },
                        { data: 'late_early', name: 'late_minutes', orderable: false },
                        { data: 'status', name: 'status' },
                        { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
                    ],
                    order: [[2, 'desc']],
                    language: {
                        url: '{{ app()->getLocale() === "ar" ? asset("assets/vendor/libs/datatables-bs5/i18n/ar.json") : "" }}'
                    }
                });
            } else {
                dataTableInstance.ajax.reload();
            }
        }
    }

    function openAttendanceModal(el) {
        const empId = el.dataset.employeeId;
        const empName = el.dataset.employeeName;
        const date = el.dataset.date;
        const attId = el.dataset.attendanceId;
        const status = el.dataset.status;
        const checkIn = el.dataset.checkIn;
        const checkOut = el.dataset.checkOut;
        const hours = el.dataset.workHours;
        const late = el.dataset.lateMinutes;

        currentAttendanceId = attId || null;
        const form = document.getElementById('cellDetailForm');
        const methodContainer = document.getElementById('method_container');
        const btnDelete = document.getElementById('btnDeleteAttendance');

        document.getElementById('modal_employee_id').value = empId;
        document.getElementById('modal_date').value = date;
        document.getElementById('modal_employee_name_text').textContent = empName;
        document.getElementById('modal_date_text').textContent = date;

        document.getElementById('modal_status').value = (status === 'future' || !status) ? 'present' : status;
        document.getElementById('modal_check_in').value = checkIn || '';
        document.getElementById('modal_check_out').value = checkOut || '';
        document.getElementById('modal_total_working_hours').value = hours || '';
        document.getElementById('modal_late_minutes').value = late || '';
        document.getElementById('modal_adjustment_reason').value = '';

        if (attId) {
            form.action = '{{ url("hr/attendances") }}/' + attId;
            methodContainer.innerHTML = '@method("PUT")';
            btnDelete.style.display = 'inline-block';
        } else {
            form.action = '{{ route("hr.attendances.store") }}';
            methodContainer.innerHTML = '';
            btnDelete.style.display = 'none';
        }

        const modal = new bootstrap.Modal(document.getElementById('modalCellDetail'));
        modal.show();
    }

    function deleteCurrentAttendance() {
        if (!currentAttendanceId) return;
        if (confirm('{{ __("هل أنت متأكد من حذف هذا السجل؟") }}')) {
            const form = document.getElementById('globalDeleteForm');
            form.action = '{{ url("hr/attendances") }}/' + currentAttendanceId;
            form.submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Auto submit filter on change
        $('#filter_branch_id, #filter_department_id, #filter_employee_id, #filter_month, #filter_year').on('change', function () {
            if ($('#listTableViewBox').is(':visible')) {
                if (dataTableInstance) dataTableInstance.ajax.reload();
            } else {
                $('#attendanceFilterForm').submit();
            }
        });

        $(document).on('click', '.btn-edit-attendance', function () {
            const rowData = $(this).data('row');
            const fakeEl = {
                dataset: {
                    employeeId: rowData.employee_id,
                    employeeName: rowData.employee_name,
                    date: rowData.date,
                    attendanceId: rowData.id,
                    status: rowData.status,
                    checkIn: rowData.check_in,
                    checkOut: rowData.check_out,
                    workHours: rowData.total_working_hours,
                    lateMinutes: rowData.late_minutes
                }
            };
            openAttendanceModal(fakeEl);
        });

        $(document).on('click', '.btn-delete-attendance', function () {
            const id = $(this).data('id');
            const name = $(this).data('name');
            if (confirm('{{ __("هل أنت متأكد من حذف سجل الحضور هذا لـ: ") }}' + name + '؟')) {
                const form = document.getElementById('globalDeleteForm');
                form.action = '{{ url("hr/attendances") }}/' + id;
                form.submit();
            }
        });
    });
</script>
@endsection
