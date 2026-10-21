@extends('layouts/contentNavbarLayout')

@section('title', __('Attendance & Absence') . ' - ' . config('variables.templateName'))

@section('page-style')
@endsection

@section('content')
<div class="attendance-page-root">

    <!-- ========================================================================= -->
    <!-- 1. Top Header: Title & Action Controls                                    -->
    <!-- ========================================================================= -->
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 attendance-page-header">
        <div class="d-flex flex-column justify-content-center">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="text-muted fw-medium small">{{ __('Human Resources') }}</a></li>
                    <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('الحضور والإنصراف') }}</li>
                </ol>
            </nav>
            <!-- Title -->
            <div class="d-flex align-items-center gap-2 mt-1">
                <i class="icon-base bx bx-calendar-check text-primary font-24"></i>
                <h4 class="mb-0 fw-bold text-heading">{{ __('الحضور والإنصراف') }}</h4>
            </div>
        </div>

        <div class="mt-3 mt-md-0 d-flex flex-wrap align-items-center gap-2">
            <!-- Navy Quick Punch Button -->
            @can('hr.attendances.create')
            <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm attendance-page-btn" data-bs-toggle="offcanvas" data-bs-target="#offcanvasQuickPunch">
                <i class="icon-base bx bx-plus font-18"></i>
                <span>{{ __('+ تسجيل حضور / إنصراف') }}</span>
            </button>
            @endcan

            <!-- Excel Import -->
            @can('hr.attendances.import')
            <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2 attendance-page-btn" data-bs-toggle="modal" data-bs-target="#modalImportExcel">
                <i class="icon-base bx bx-upload font-18"></i>
                <span>{{ __('استيراد إكسيل') }}</span>
            </button>
            @endcan

            <!-- Segmented Switcher -->
            <div class="btn-group shadow-sm" role="group">
                <button type="button" class="btn btn-outline-primary active" id="btnTabGrid" onclick="switchAttendanceTab('grid')">
                    <i class="icon-base bx bx-grid-alt me-1"></i>
                    <span>{{ __('الجدول الشهري') }}</span>
                </button>
                <button type="button" class="btn btn-outline-primary" id="btnTabList" onclick="switchAttendanceTab('list')">
                    <i class="icon-base bx bx-list-ul me-1"></i>
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
                        <select name="branch_id" id="filter_branch_id" class="select2 form-select" data-placeholder="{{ __('All Branches') }}" data-allow-clear="true">
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
                        <select name="department_id" id="filter_department_id" class="select2 form-select" data-placeholder="{{ __('All Departments') }}" data-allow-clear="true">
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
                        <select name="employee_id" id="filter_employee_id" class="select2 form-select" data-placeholder="{{ __('All Employees') }}" data-allow-clear="true">
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
                        <select name="month" id="filter_month" class="select2 form-select">
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
                        <select name="year" id="filter_year" class="select2 form-select text-center">
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
    <!-- 2.5 Dynamic Employee Attendance Timeline Tracker (شريط متابعة الدوام)     -->
    <!-- ========================================================================= -->
    <div id="attendanceTrackerContainer" class="attendance-tracker-card" style="{{ empty($selectedEmployeeTimeline) ? 'display: none;' : '' }}">
        @php
            $trackerData = $selectedEmployeeTimeline ?? [];
            $tEmp = $trackerData['employee'] ?? [];
            $tState = $trackerData['progress_state'] ?? 'not_started';
            $tPct = $trackerData['progress_percent'] ?? 0;
            $hasIn = !empty($trackerData['has_check_in']);
            $hasOut = !empty($trackerData['has_check_out']);
        @endphp

        <!-- Tracker Header -->
        <div class="attendance-tracker-header">
            <div class="attendance-tracker-emp-info">
                <div class="attendance-tracker-avatar" id="trackerAvatar">
                    @if(!empty($tEmp['avatar_url']))
                        <img src="{{ $tEmp['avatar_url'] }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                    @else
                        <span id="trackerAvatarInitial">{{ mb_substr($tEmp['name'] ?? 'م', 0, 1) }}</span>
                    @endif
                </div>
                <div>
                    <h5 class="attendance-tracker-title">
                        <i class="bx bx-run text-primary font-20"></i>
                        <span>{{ __('شريط متابعة الدوام اليومي') }}</span>
                        <span class="text-muted fw-normal font-13" id="trackerEmpNameWrap">
                            ( <strong class="text-heading" id="trackerEmpName">{{ $tEmp['name'] ?? '-' }}</strong> )
                        </span>
                    </h5>
                    <p class="attendance-tracker-sub" id="trackerEmpSub">
                        <span id="trackerEmpCode">{{ $tEmp['code'] ?? '-' }}</span> • 
                        <span id="trackerEmpDept">{{ $tEmp['department'] ?? '-' }}</span> • 
                        <span id="trackerEmpBranch">{{ $tEmp['branch'] ?? '-' }}</span>
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Status Pill -->
                <div class="attendance-tracker-status-pill status-{{ $tState }}" id="trackerStatusPill">
                    <span class="pulse-dot"></span>
                    <span id="trackerStatusText">{{ $trackerData['status_badge'] ?? __('لم يبدأ بعد') }}</span>
                </div>

                <!-- Date Info Badge -->
                <span class="badge bg-label-secondary px-3 py-2 rounded-pill font-12" id="trackerDateBadge">
                    <i class="bx bx-calendar me-1"></i> {{ $trackerData['date'] ?? now()->format('Y-m-d') }}
                </span>

                <!-- Close / Hide Tracker Button -->
                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary rounded-circle" onclick="closeAttendanceTracker()" title="{{ __('إخفاء الشريط') }}">
                    <i class="bx bx-x font-18"></i>
                </button>
            </div>
        </div>

        <!-- 3-Node Timeline Pipeline Track (مطابق للمرجع بتصميم أرقى) -->
        <div class="att-track-pipeline">
            <!-- Background Full Line -->
            <div class="att-track-line-bg"></div>

            <!-- Active Gradient Fill Line -->
            <div class="att-track-line-fill" id="trackerLineFill" style="width: {{ $tPct }}%;"></div>

            <!-- Node 1: Attendance Check-In (الحضور) -->
            <div class="att-track-step">
                <div class="att-track-node {{ $hasIn ? 'node-active' : 'node-pending' }}" id="trackerNodeIn">
                    <i class="bx bx-check font-22"></i>
                </div>
                <div class="att-track-step-label">{{ __('الحضور') }}</div>
                <div class="att-track-step-time" id="trackerTimeIn">{{ $trackerData['check_in_formatted'] ?? '--:--' }}</div>
                <div class="att-track-step-sub" id="trackerShiftStart">{{ __('بدء الوردية') }}: {{ $tEmp['shift_start'] ?? '--:--' }}</div>
            </div>

            <!-- Node 2: Shift In Progress (الدوام جاري حالياً) -->
            <div class="att-track-step">
                <div class="att-track-node {{ $hasOut ? 'node-active' : ($hasIn ? 'node-in-progress' : 'node-pending') }}" id="trackerNodeMid">
                    @if($hasOut)
                        <i class="bx bx-check-double font-22"></i>
                    @elseif($hasIn)
                        <i class="bx bx-time-five font-22"></i>
                    @else
                        <i class="bx bx-circle font-18"></i>
                    @endif
                </div>
                <div class="att-track-step-label">{{ __('الدوام جاري حالياً') }}</div>
                <div class="att-track-step-time" id="trackerStateMid">
                    @if($hasOut)
                        <span class="text-success font-14">✓</span>
                    @elseif($hasIn)
                        <span class="text-info">{{ __('قيد العمل') }}</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </div>
                <div class="att-track-step-sub" id="trackerHoursCount">
                    {{ __('ساعات العمل') }}: <strong class="text-heading" id="trackerHoursVal">{{ $trackerData['total_working_hours'] ?? 0 }}</strong> {{ __('ساعة') }}
                </div>
            </div>

            <!-- Node 3: Check-Out (الانصراف) -->
            <div class="att-track-step">
                <div class="att-track-node {{ $hasOut ? 'node-active' : 'node-pending' }}" id="trackerNodeOut">
                    <i class="bx bx-check-double font-22"></i>
                </div>
                <div class="att-track-step-label">{{ __('الانصراف') }}</div>
                <div class="att-track-step-time" id="trackerTimeOut">{{ $trackerData['check_out_formatted'] ?? '--:--' }}</div>
                <div class="att-track-step-sub" id="trackerShiftEnd">{{ __('نهاية الوردية') }}: {{ $tEmp['shift_end'] ?? '--:--' }}</div>
            </div>
        </div>

        <!-- Quick Summary Metrics Strip -->
        <div class="att-track-metrics-strip">
            <div class="att-track-metric-item">
                <i class="bx bx-timer text-primary font-18"></i>
                <span>{{ __('إجمالي ساعات العمل') }}:</span>
                <strong id="trackerStripHours">{{ $trackerData['total_working_hours'] ?? 0 }} {{ __('ساعة') }}</strong>
            </div>

            <div class="att-track-metric-item">
                <i class="bx bx-alarm-exclamation text-warning font-18"></i>
                <span>{{ __('دقائق التأخير') }}:</span>
                <strong id="trackerStripLate">{{ $trackerData['late_minutes'] ?? 0 }} {{ __('دقيقة') }}</strong>
            </div>

            <div class="att-track-metric-item">
                <i class="bx bx-door-open text-danger font-18"></i>
                <span>{{ __('خروج مبكر') }}:</span>
                <strong id="trackerStripEarly">{{ $trackerData['early_leave_minutes'] ?? 0 }} {{ __('دقيقة') }}</strong>
            </div>

            <div class="att-track-metric-item">
                <i class="bx bx-calendar-star text-info font-18"></i>
                <span>{{ __('الوردية المحددة') }}:</span>
                <strong id="trackerStripShift">{{ $tEmp['shift_name'] ?? __('وردية قياسية') }}</strong>
            </div>
        </div>
    </div>
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
        <div class="card shadow-sm border-0 attendance-index-table-wrapper">
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
                <input type="datetime-local" class="form-control js-flatpickr-datetime" name="punch_time" id="punch_time" value="{{ now()->format('Y-m-d\TH:i') }}" required>
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
                    pageLength: 10,
                    dom: '<"row mx-2 py-3"<"col-sm-12 col-md-6 d-flex align-items-center justify-content-center justify-content-md-start"l><"col-sm-12 col-md-6 d-flex align-items-center justify-content-center justify-content-md-end"f>>t<"row mx-2 py-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
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
                        url: '{{ app()->getLocale() === "ar" ? asset("assets/vendor/libs/datatables-bs5/i18n/ar.json") : "" }}',
                        search: "",
                        searchPlaceholder: "{{ __('البحث في السجلات...') }}",
                        lengthMenu: "_MENU_",
                        emptyTable: '<div class="attendance-empty-state text-center p-4"><div class="avatar avatar-md bg-label-secondary mx-auto mb-3"><i class="icon-base bx bx-calendar-x font-24"></i></div><h6 class="fw-semibold mb-1">{{ __('لا توجد سجلات حضور') }}</h6><p class="text-muted small mb-0">{{ __('لا توجد سجلات مطابقة للفلاتر المحددة.') }}</p></div>'
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

    // =========================================================================
    // Dynamic Employee Attendance Timeline Tracker (شريط متابعة الدوام)
    // =========================================================================
    function closeAttendanceTracker() {
        const container = document.getElementById('attendanceTrackerContainer');
        if (container) {
            container.style.transition = 'all 0.3s ease';
            container.style.opacity = '0';
            container.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                container.style.display = 'none';
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 300);
        }
    }

    function fetchEmployeeTimeline(employeeId, date = null) {
        if (!employeeId) {
            closeAttendanceTracker();
            return;
        }

        const dateVal = date || $('#filter_year').val() + '-' + String($('#filter_month').val()).padStart(2, '0') + '-' + String(new Date().getDate()).padStart(2, '0');

        $.ajax({
            url: '{{ route("hr.attendances.timeline-data") }}',
            type: 'GET',
            data: {
                employee_id: employeeId,
                date: dateVal
            },
            success: function (res) {
                if (res.success && res.data) {
                    renderAttendanceTracker(res.data);
                }
            },
            error: function () {
                // Keep quiet or log error
            }
        });
    }

    function renderAttendanceTracker(data) {
        const container = document.getElementById('attendanceTrackerContainer');
        if (!container) return;

        const emp = data.employee || {};

        // 1. Employee Info
        document.getElementById('trackerEmpName').textContent = emp.name || '-';
        document.getElementById('trackerEmpCode').textContent = emp.code || '-';
        document.getElementById('trackerEmpDept').textContent = emp.department || '-';
        document.getElementById('trackerEmpBranch').textContent = emp.branch || '-';

        const avatarBox = document.getElementById('trackerAvatar');
        if (emp.avatar_url) {
            avatarBox.innerHTML = `<img src="${emp.avatar_url}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">`;
        } else {
            avatarBox.innerHTML = `<span id="trackerAvatarInitial">${(emp.name || 'م').substring(0, 1)}</span>`;
        }

        // 2. Status Pill
        const pill = document.getElementById('trackerStatusPill');
        pill.className = `attendance-tracker-status-pill status-${data.progress_state}`;
        document.getElementById('trackerStatusText').textContent = data.status_badge || '-';

        // 3. Date
        document.getElementById('trackerDateBadge').innerHTML = `<i class="bx bx-calendar me-1"></i> ${data.date}`;

        // 4. Progress Fill
        document.getElementById('trackerLineFill').style.width = `${data.progress_percent}%`;

        // 5. Node In (Check-In)
        const nodeIn = document.getElementById('trackerNodeIn');
        nodeIn.className = `att-track-node ${data.has_check_in ? 'node-active' : 'node-pending'}`;
        document.getElementById('trackerTimeIn').textContent = data.check_in_formatted || '--:--';
        document.getElementById('trackerShiftStart').textContent = `{{ __('بدء الوردية') }}: ${emp.shift_start || '--:--'}`;

        // 6. Node Mid (In Progress)
        const nodeMid = document.getElementById('trackerNodeMid');
        const stateMid = document.getElementById('trackerStateMid');
        if (data.has_check_out) {
            nodeMid.className = 'att-track-node node-active';
            nodeMid.innerHTML = '<i class="bx bx-check-double font-22"></i>';
            stateMid.innerHTML = '<span class="text-success font-14">✓</span>';
        } else if (data.has_check_in) {
            nodeMid.className = 'att-track-node node-in-progress';
            nodeMid.innerHTML = '<i class="bx bx-time-five font-22"></i>';
            stateMid.innerHTML = '<span class="text-info">{{ __("قيد العمل") }}</span>';
        } else {
            nodeMid.className = 'att-track-node node-pending';
            nodeMid.innerHTML = '<i class="bx bx-circle font-18"></i>';
            stateMid.innerHTML = '<span class="text-muted">-</span>';
        }
        document.getElementById('trackerHoursVal').textContent = data.total_working_hours || 0;

        // 7. Node Out (Check-Out)
        const nodeOut = document.getElementById('trackerNodeOut');
        nodeOut.className = `att-track-node ${data.has_check_out ? 'node-active' : 'node-pending'}`;
        document.getElementById('trackerTimeOut').textContent = data.check_out_formatted || '--:--';
        document.getElementById('trackerShiftEnd').textContent = `{{ __('نهاية الوردية') }}: ${emp.shift_end || '--:--'}`;

        // 8. Bottom Metrics Strip
        document.getElementById('trackerStripHours').textContent = `${data.total_working_hours || 0} {{ __('ساعة') }}`;
        document.getElementById('trackerStripLate').textContent = `${data.late_minutes || 0} {{ __('دقيقة') }}`;
        document.getElementById('trackerStripEarly').textContent = `${data.early_leave_minutes || 0} {{ __('دقيقة') }}`;
        document.getElementById('trackerStripShift').textContent = emp.shift_name || '{{ __("وردية قياسية") }}';

        // Reveal smoothly
        container.style.display = 'block';
        container.style.opacity = '1';
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Auto submit filter on change
        $('#filter_branch_id, #filter_department_id, #filter_month, #filter_year').on('change', function () {
            if ($('#listTableViewBox').is(':visible')) {
                if (dataTableInstance) dataTableInstance.ajax.reload();
            } else {
                $('#attendanceFilterForm').submit();
            }
        });

        // If Employee filter changes, fetch timeline immediately via AJAX without full page reload if preferred or on submit
        $('#filter_employee_id').on('change', function () {
            const empId = $(this).val();
            if (empId) {
                fetchEmployeeTimeline(empId);
            } else {
                closeAttendanceTracker();
            }
            if ($('#listTableViewBox').is(':visible')) {
                if (dataTableInstance) dataTableInstance.ajax.reload();
            } else {
                $('#attendanceFilterForm').submit();
            }
        });

        // Click on employee avatar / name row in matrix grid to view their live timeline
        $(document).on('click', '.col-sticky-emp', function () {
            const row = $(this).closest('tr');
            const firstCell = row.find('td[data-employee-id]').first();
            const empId = firstCell.data('employeeId');
            if (empId) {
                fetchEmployeeTimeline(empId);
                // Smooth scroll to tracker if not fully visible
                const tracker = document.getElementById('attendanceTrackerContainer');
                if (tracker) {
                    tracker.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
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
            if (rowData.employee_id) {
                fetchEmployeeTimeline(rowData.employee_id, rowData.date);
            }
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
