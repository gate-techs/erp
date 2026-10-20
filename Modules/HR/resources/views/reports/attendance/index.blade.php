@extends('layouts/contentNavbarLayout')

@section('title', __('تقرير الحضور والانصراف والدوام'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Page Header & Action Buttons -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3">
        <div>
            <h4 class="report-page-title fw-bold py-1 mb-0">
                <span class="text-muted fw-light">{{ __('الموارد البشرية') }} / {{ __('التقارير') }} /</span> 
                @if($reportType === 'monthly_summary')
                    {{ __('ملخص الحضور الشهري (Timesheet)') }}
                @elseif($reportType === 'lateness')
                    {{ __('تقرير التأخير والانصراف المبكر') }}
                @elseif($reportType === 'absenteeism')
                    {{ __('تقرير الغياب بدون إذن') }}
                @else
                    {{ __('تقرير الحضور والانصراف اليومي') }}
                @endif
            </h4>
        </div>

        <div class="report-actions d-flex align-items-center gap-2">
            <!-- Column Selector Dropdown -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="columnSelectorDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <i class="bx bx-columns me-1"></i> {{ __('تخصيص الأعمدة') }}
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg" style="min-width: 280px; max-height: 400px; overflow-y: auto;" aria-labelledby="columnSelectorDropdown">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <span class="fw-bold text-dark small">{{ __('الأعمدة الظاهرة في الجدول والطباعة') }}</span>
                    </div>
                    <form id="columnsForm" method="GET" action="{{ route('hr.reports.attendance.index') }}">
                        <!-- Preserve current filter inputs -->
                        @foreach(request()->except(['columns', 'page']) as $k => $v)
                            @if(is_array($v))
                                @foreach($v as $subV)
                                    <input type="hidden" name="{{ $k }}[]" value="{{ $subV }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endif
                        @endforeach

                        <div class="vstack gap-2">
                            @foreach($availableColumns as $colKey => $colDef)
                                <div class="form-check form-switch mb-1">
                                    <input class="form-check-input col-toggle-checkbox" type="checkbox" name="columns[]" value="{{ $colKey }}" id="col_{{ $colKey }}" {{ !empty($selectedColumns[$colKey]) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="col_{{ $colKey }}">
                                        {{ $colDef['label'] }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-grid gap-2 mt-3 pt-2 border-top">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bx bx-check me-1"></i> {{ __('تطبيق الأعمدة') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Export CSV / Excel Button -->
            @can('hr.reports.attendance.export')
            <a href="{{ route('hr.reports.attendance.export', request()->all()) }}" class="btn btn-label-success">
                <i class="bx bx-export me-1"></i> {{ __('تصدير Excel') }}
            </a>
            @endcan

            <!-- Smart Split Print Button -->
            @can('hr.reports.attendance.print')
            <div class="btn-group">
                <a href="{{ route('hr.reports.attendance.print', request()->all()) }}" target="_blank" class="btn btn-primary">
                    <i class="bx bx-printer me-1"></i> {{ __('طباعة التقرير') }}
                </a>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden">{{ __('خيارات القوالب') }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><h6 class="dropdown-header text-uppercase small">{{ __('اختر قالب الطباعة:') }}</h6></li>
                    @forelse($printTemplates as $tpl)
                        <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2" target="_blank" 
                               href="{{ route('hr.reports.attendance.print', array_merge(request()->all(), ['template_id' => $tpl->id])) }}">
                                <span>
                                    <i class="bx bx-file me-2 text-primary"></i> {{ $tpl->display_name }}
                                    <small class="text-muted d-block ps-4">({{ $tpl->page_size }} - {{ ucfirst($tpl->orientation) }})</small>
                                </span>
                                @if($tpl->is_default)
                                    <span class="badge bg-label-primary small">{{ __('افتراضي') }}</span>
                                @endif
                            </a>
                        </li>
                    @empty
                        <li><span class="dropdown-item text-muted">{{ __('لا توجد قوالب نشطة') }}</span></li>
                    @endforelse
                </ul>
            </div>
            @endcan
        </div>
    </div>

    <!-- 4 Sub-Reports Navigation Tabs -->
    <div class="nav-align-top mb-4">
        <ul class="nav nav-pills report-type-tabs gap-2">
            <li class="nav-item">
                <a class="nav-link {{ $reportType === 'daily' ? 'active' : '' }}" 
                   href="{{ route('hr.reports.attendance.index', array_merge(request()->except(['page', 'columns']), ['report_type' => 'daily'])) }}">
                    <i class="bx bx-calendar-event me-1"></i> {{ __('تقرير الحضور والانصراف اليومي') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $reportType === 'monthly_summary' ? 'active' : '' }}" 
                   href="{{ route('hr.reports.attendance.index', array_merge(request()->except(['page', 'columns']), ['report_type' => 'monthly_summary'])) }}">
                    <i class="bx bx-spreadsheet me-1"></i> {{ __('ملخص الحضور الشهري (Timesheet)') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $reportType === 'lateness' ? 'active' : '' }}" 
                   href="{{ route('hr.reports.attendance.index', array_merge(request()->except(['page', 'columns']), ['report_type' => 'lateness'])) }}">
                    <i class="bx bx-time-five me-1"></i> {{ __('تقرير التأخير والانصراف المبكر') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $reportType === 'absenteeism' ? 'active' : '' }}" 
                   href="{{ route('hr.reports.attendance.index', array_merge(request()->except(['page', 'columns']), ['report_type' => 'absenteeism'])) }}">
                    <i class="bx bx-user-x me-1"></i> {{ __('تقرير الغياب بدون إذن') }}
                </a>
            </li>
        </ul>
    </div>

    <!-- Advanced Filter Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header report-filter-header bg-transparent border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2 text-nowrap">
                <i class="bx bx-filter-alt text-primary"></i> {{ __('فلاتر التقرير الشاملة') }}
            </h6>

            <!-- Quick Date Presets -->
            @php
                $todayDate = now()->format('Y-m-d');
                $yesterdayDate = now()->subDay()->format('Y-m-d');
                $startOfWeek = now()->startOfWeek(\Carbon\Carbon::SATURDAY)->format('Y-m-d');
                $startOfMonth = now()->startOfMonth()->format('Y-m-d');
                $startOfLastMonth = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $endOfLastMonth = now()->subMonth()->endOfMonth()->format('Y-m-d');

                $curStart = request('start_date', ($reportType === 'daily' ? $todayDate : $startOfMonth));
                $curEnd = request('end_date', $todayDate);

                $isToday = ($curStart === $todayDate && $curEnd === $todayDate);
                $isYesterday = ($curStart === $yesterdayDate && $curEnd === $yesterdayDate);
                $isThisWeek = ($curStart === $startOfWeek && $curEnd === $todayDate);
                $isThisMonth = ($curStart === $startOfMonth && $curEnd === $todayDate);
                $isLastMonth = ($curStart === $startOfLastMonth && $curEnd === $endOfLastMonth);
            @endphp
            <div class="btn-group btn-group-sm date-presets" role="group">
                <button type="button" class="btn btn-outline-primary {{ $isToday ? 'active' : '' }}" onclick="setDatePreset('today')">{{ __('اليوم') }}</button>
                <button type="button" class="btn btn-outline-primary {{ $isYesterday ? 'active' : '' }}" onclick="setDatePreset('yesterday')">{{ __('أمس') }}</button>
                <button type="button" class="btn btn-outline-primary {{ $isThisWeek ? 'active' : '' }}" onclick="setDatePreset('this_week')">{{ __('هذا الأسبوع') }}</button>
                <button type="button" class="btn btn-outline-primary {{ $isThisMonth ? 'active' : '' }}" onclick="setDatePreset('this_month')">{{ __('هذا الشهر') }}</button>
                <button type="button" class="btn btn-outline-primary {{ $isLastMonth ? 'active' : '' }}" onclick="setDatePreset('last_month')">{{ __('الشهر السابق') }}</button>
            </div>
        </div>

        <div class="card-body pt-3">
            <form id="attendanceFilterForm" method="GET" action="{{ route('hr.reports.attendance.index') }}">
                <input type="hidden" name="report_type" value="{{ $reportType }}">
                
                <!-- Preserve currently selected columns in the filter form -->
                @foreach($availableColumns as $colKey => $colDef)
                    @if(!empty($selectedColumns[$colKey]))
                        <input type="hidden" name="columns[]" value="{{ $colKey }}">
                    @endif
                @endforeach

                <div class="row g-3">
                    <!-- Date From -->
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-semibold">{{ __('من تاريخ') }}</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                            <input type="text" id="start_date" name="start_date" class="form-control js-flatpickr-date" 
                                   value="{{ request('start_date', ($reportType === 'daily' ? now()->format('Y-m-d') : now()->startOfMonth()->format('Y-m-d'))) }}" placeholder="YYYY-MM-DD">
                        </div>
                    </div>

                    <!-- Date To -->
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-semibold">{{ __('إلى تاريخ') }}</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                            <input type="text" id="end_date" name="end_date" class="form-control js-flatpickr-date" 
                                   value="{{ request('end_date', now()->format('Y-m-d')) }}" placeholder="YYYY-MM-DD">
                        </div>
                    </div>

                    <!-- Department -->
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-semibold">{{ __('القسم / الإدارة') }}</label>
                        <select name="department_id" class="form-select select2">
                            <option value="">{{ __('جميع الأقسام') }}</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Branch -->
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-semibold">{{ __('الفرع') }}</label>
                        <select name="branch_id" class="form-select select2">
                            <option value="">{{ __('جميع الفروع') }}</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Shift -->
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-semibold">{{ __('الوردية') }}</label>
                        <select name="shift_id" class="form-select select2">
                            <option value="">{{ __('جميع الورديات') }}</option>
                            @foreach($shifts as $sh)
                                <option value="{{ $sh->id }}" {{ request('shift_id') == $sh->id ? 'selected' : '' }}>
                                    {{ $sh->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Employee -->
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-semibold">{{ __('الموظف') }}</label>
                        <select name="employee_id" class="form-select select2">
                            <option value="">{{ __('جميع الموظفين') }}</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->employee_code }} - {{ $emp->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($reportType !== 'lateness' && $reportType !== 'absenteeism')
                        <!-- Status -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('حالة الحضور') }}</label>
                            <select name="status" class="form-select select2">
                                <option value="">{{ __('جميع الحالات') }}</option>
                                @foreach($statuses as $st)
                                    <option value="{{ $st->value }}" {{ request('status') == $st->value ? 'selected' : '' }}>
                                        {{ $st->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Per Page -->
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-semibold">{{ __('عدد السجلات بالصفحة') }}</label>
                        <select name="per_page" class="form-select">
                            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            <option value="250" {{ request('per_page') == 250 ? 'selected' : '' }}>250</option>
                        </select>
                    </div>

                    <!-- Filter Action Buttons -->
                    <div class="col-12 report-filter-actions d-flex justify-content-end gap-2 pt-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-search me-1"></i> {{ __('تحديث التقرير') }}
                        </button>
                        <a href="{{ route('hr.reports.attendance.index', ['report_type' => $reportType]) }}" class="btn btn-label-secondary">
                            <i class="bx bx-reset me-1"></i> {{ __('إعادة تعيين') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Data Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bx bx-table text-primary"></i> {{ __('بيانات وسجلات التقرير') }}
                <span class="badge bg-label-secondary rounded-pill small">{{ $attendances->total() }} {{ __('سجل') }}</span>
            </h6>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        @foreach($availableColumns as $colKey => $colDef)
                            @if(!empty($selectedColumns[$colKey]))
                                <th class="{{ $colDef['class'] }}" style="width: {{ $colDef['width'] }};">{{ $colDef['label'] }}</th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @if($reportType === 'monthly_summary')
                        {{-- Monthly Summary (Timesheet) Rows --}}
                        @forelse($attendances as $index => $row)
                            <tr>
                                @if(!empty($selectedColumns['index']))
                                    <td class="text-center text-muted small">{{ $attendances->firstItem() + $index }}</td>
                                @endif
                                @if(!empty($selectedColumns['code']))
                                    <td><span class="badge bg-label-dark font-monospace">{{ $row->employee->employee_code ?? '-' }}</span></td>
                                @endif
                                @if(!empty($selectedColumns['employee']))
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar avatar-xs">
                                                <span class="avatar-initial rounded-circle bg-label-primary font-weight-bold">
                                                    {{ mb_substr($row->employee->name ?? 'U', 0, 1) }}
                                                </span>
                                            </div>
                                            <span class="fw-bold text-dark">{{ $row->employee->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                @endif
                                @if(!empty($selectedColumns['department']))
                                    <td>{{ $row->employee->department->name ?? '-' }}</td>
                                @endif
                                @if(!empty($selectedColumns['branch']))
                                    <td>{{ $row->employee->primaryBranch->name ?? '-' }}</td>
                                @endif
                                @if(!empty($selectedColumns['scheduled_days']))
                                    <td class="text-center fw-bold">{{ $row->scheduled_days }}</td>
                                @endif
                                @if(!empty($selectedColumns['present_days']))
                                    <td class="text-center text-success fw-bold">{{ $row->present_days }}</td>
                                @endif
                                @if(!empty($selectedColumns['absent_days']))
                                    <td class="text-center text-danger fw-bold">{{ $row->absent_days }}</td>
                                @endif
                                @if(!empty($selectedColumns['leave_days']))
                                    <td class="text-center text-primary">{{ $row->leave_days }}</td>
                                @endif
                                @if(!empty($selectedColumns['rest_days']))
                                    <td class="text-center text-muted">{{ $row->rest_days }}</td>
                                @endif
                                @if(!empty($selectedColumns['late_minutes']))
                                    <td class="text-center">
                                        @if($row->late_minutes > 0)
                                            <span class="badge bg-label-warning fw-bold">{{ $row->late_minutes }} د</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endif
                                @if(!empty($selectedColumns['early_leave']))
                                    <td class="text-center">
                                        @if($row->early_leave_minutes > 0)
                                            <span class="badge bg-label-danger fw-bold">{{ $row->early_leave_minutes }} د</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endif
                                @if(!empty($selectedColumns['overtime']))
                                    <td class="text-center">
                                        @if($row->overtime_minutes > 0)
                                            <span class="badge bg-label-info fw-bold">+{{ $row->overtime_minutes }} د</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endif
                                @if(!empty($selectedColumns['total_working_hours']))
                                    <td class="text-center fw-bold text-dark">{{ $row->total_working_hours }} <small class="text-muted">س</small></td>
                                @endif
                                @if(!empty($selectedColumns['attendance_rate']))
                                    <td class="text-center">
                                        <span class="badge {{ $row->attendance_rate >= 90 ? 'bg-label-success' : ($row->attendance_rate >= 75 ? 'bg-label-warning' : 'bg-label-danger') }} fw-bold">
                                            {{ $row->attendance_rate }}%
                                        </span>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="20" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-label-secondary rounded-circle mb-3 p-3">
                                            <i class="bx bx-calendar-x bx-lg"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1 text-dark">{{ __('لا توجد سجلات حضور تطابق شروط الفلترة') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('يرجى تغيير نطاق التواريخ أو معايير الفلترة وإعادة المحاولة.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    @else
                        {{-- Standard Attendance / Lateness / Absenteeism Rows --}}
                        @forelse($attendances as $index => $row)
                            <tr>
                                @if(!empty($selectedColumns['index']))
                                    <td class="text-center text-muted small">{{ $attendances->firstItem() + $index }}</td>
                                @endif

                                @if(!empty($selectedColumns['code']))
                                    <td>
                                        <span class="badge bg-label-dark font-monospace">{{ $row->employee->employee_code ?? '-' }}</span>
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['employee']))
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar avatar-xs">
                                                <span class="avatar-initial rounded-circle bg-label-primary font-weight-bold">
                                                    {{ mb_substr($row->employee->name ?? 'U', 0, 1) }}
                                                </span>
                                            </div>
                                            <span class="fw-bold text-dark">{{ $row->employee->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['department']))
                                    <td>{{ $row->employee->department->name ?? '-' }}</td>
                                @endif

                                @if(!empty($selectedColumns['branch']))
                                    <td>{{ $row->employee->primaryBranch->name ?? '-' }}</td>
                                @endif

                                @if(!empty($selectedColumns['date']))
                                    <td class="text-center font-monospace">{{ $row->date ? $row->date->format('Y-m-d') : '-' }}</td>
                                @endif

                                @if(!empty($selectedColumns['day']))
                                    <td class="text-center text-muted small">{{ $row->date ? $row->date->translatedFormat('l') : '-' }}</td>
                                @endif

                                @if(!empty($selectedColumns['shift']))
                                    <td>
                                        <span class="badge bg-label-secondary">{{ $row->shift->name ?? '-' }}</span>
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['check_in']))
                                    <td class="text-center font-monospace">
                                        @if($row->check_in)
                                            <span class="text-dark fw-semibold">{{ \Carbon\Carbon::parse($row->check_in)->format('H:i') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['late_minutes']))
                                    <td class="text-center">
                                        @if($row->late_minutes > 0)
                                            <span class="badge bg-label-warning fw-bold">{{ $row->late_minutes }} د</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['check_out']))
                                    <td class="text-center font-monospace">
                                        @if($row->check_out)
                                            <span class="text-dark fw-semibold">{{ \Carbon\Carbon::parse($row->check_out)->format('H:i') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['early_leave']))
                                    <td class="text-center">
                                        @if($row->early_leave_minutes > 0)
                                            <span class="badge bg-label-danger fw-bold">{{ $row->early_leave_minutes }} د</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['work_hours']))
                                    <td class="text-center">
                                        <span class="fw-bold">{{ $row->total_working_hours ?: '0.00' }}</span>
                                        <small class="text-muted">س</small>
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['overtime']))
                                    <td class="text-center">
                                        @if($row->overtime_minutes > 0)
                                            <span class="badge bg-label-info fw-bold">+{{ $row->overtime_minutes }} د</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['status']))
                                    <td class="text-center">
                                        @if($reportType === 'absenteeism')
                                            <span class="badge bg-label-danger fw-bold">{{ __('غياب بدون إذن') }}</span>
                                        @elseif($row->status)
                                            <span class="badge {{ $row->status->badgeClass() }}">
                                                {{ $row->status->label() }}
                                            </span>
                                        @else
                                            <span class="badge bg-label-secondary">-</span>
                                        @endif
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['reason']))
                                    <td>
                                        <span class="text-danger small">{{ $row->adjustment_reason ?: __('غير مبرر - اتخاذ إجراء إداري') }}</span>
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['source']))
                                    <td class="text-center">
                                        @if($row->is_manual_adjusted)
                                            <span class="badge bg-label-warning" title="{{ $row->adjustment_reason }}">{{ __('تعديل يدوي') }}</span>
                                        @else
                                            <span class="badge bg-label-secondary">{{ __('بصمة') }}</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="20" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-label-secondary rounded-circle mb-3 p-3">
                                            <i class="bx bx-calendar-x bx-lg"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1 text-dark">{{ __('لا توجد سجلات حضور تطابق شروط الفلترة') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('يرجى تغيير نطاق التواريخ أو معايير الفلترة وإعادة المحاولة.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>

        @include('hr::reports._pagination', ['records' => $attendances])
    </div>

</div>

@section('page-script')
<script>
    window.setDatePreset = function(type) {
        const startInput = document.getElementById('start_date');
        const endInput = document.getElementById('end_date');
        if (!startInput || !endInput) return;

        const today = new Date();

        const formatDate = (d) => {
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        let startVal = '';
        let endVal = '';

        if (type === 'today') {
            startVal = formatDate(today);
            endVal = formatDate(today);
        } else if (type === 'yesterday') {
            const yest = new Date();
            yest.setDate(yest.getDate() - 1);
            startVal = formatDate(yest);
            endVal = formatDate(yest);
        } else if (type === 'this_week') {
            const now = new Date();
            const day = now.getDay(); // 0: Sun, 1: Mon, ..., 6: Sat
            // Week starts on Saturday in Arabic/Middle East
            const daysFromSaturday = (day + 1) % 7;
            const startOfWeek = new Date(now);
            startOfWeek.setDate(now.getDate() - daysFromSaturday);
            startVal = formatDate(startOfWeek);
            endVal = formatDate(new Date());
        } else if (type === 'this_month') {
            const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            startVal = formatDate(startOfMonth);
            endVal = formatDate(today);
        } else if (type === 'last_month') {
            const startOfLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            const endOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
            startVal = formatDate(startOfLastMonth);
            endVal = formatDate(endOfLastMonth);
        }

        startInput.value = startVal;
        endInput.value = endVal;

        // Trigger flatpickr updates if initialized
        if (startInput._flatpickr) {
            startInput._flatpickr.setDate(startVal, true);
        }
        if (endInput._flatpickr) {
            endInput._flatpickr.setDate(endVal, true);
        }

        // Auto submit the filter form immediately
        const filterForm = document.getElementById('attendanceFilterForm');
        if (filterForm) {
            filterForm.submit();
        }
    };
</script>
@endsection

@endsection
