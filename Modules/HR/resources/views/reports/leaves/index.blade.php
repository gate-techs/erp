@extends('layouts/contentNavbarLayout')

@section('title', __('تقارير الإجازات والاستئذانات'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Page Header & Action Buttons -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3">
        <div>
            <h4 class="report-page-title fw-bold py-1 mb-0">
                <span class="text-muted fw-light">{{ __('الموارد البشرية') }} / {{ __('التقارير') }} /</span> 
                @if($reportType === 'leaves_log')
                    {{ __('سجل طلبات الإجازات التفصيلي') }}
                @elseif($reportType === 'permissions')
                    {{ __('تقرير الاستئذانات والأذونات') }}
                @else
                    {{ __('تقرير أرصدة الإجازات') }}
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
                    <form id="columnsForm" method="GET" action="{{ route('hr.reports.leaves.index') }}">
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
            <a href="{{ route('hr.reports.leaves.export', request()->all()) }}" class="btn btn-label-success">
                <i class="bx bx-export me-1"></i> {{ __('تصدير Excel') }}
            </a>

            <!-- Smart Split Print Button -->
            <div class="btn-group">
                <a href="{{ route('hr.reports.leaves.print', request()->all()) }}" target="_blank" class="btn btn-primary">
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
                               href="{{ route('hr.reports.leaves.print', array_merge(request()->all(), ['template_id' => $tpl->id])) }}">
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
        </div>
    </div>

    <!-- 3 Sub-Reports Navigation Tabs -->
    <div class="nav-align-top mb-4">
        <ul class="nav nav-pills report-type-tabs gap-2">
            <li class="nav-item">
                <a class="nav-link {{ $reportType === 'balances' ? 'active' : '' }}" 
                   href="{{ route('hr.reports.leaves.index', array_merge(request()->except(['page', 'columns']), ['report_type' => 'balances'])) }}">
                    <i class="bx bx-pie-chart-alt-2 me-1"></i> {{ __('تقرير أرصدة الإجازات') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $reportType === 'leaves_log' ? 'active' : '' }}" 
                   href="{{ route('hr.reports.leaves.index', array_merge(request()->except(['page', 'columns']), ['report_type' => 'leaves_log'])) }}">
                    <i class="bx bx-list-check me-1"></i> {{ __('سجل طلبات الإجازات التفصيلي') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $reportType === 'permissions' ? 'active' : '' }}" 
                   href="{{ route('hr.reports.leaves.index', array_merge(request()->except(['page', 'columns']), ['report_type' => 'permissions'])) }}">
                    <i class="bx bx-time-five me-1"></i> {{ __('تقرير الاستئذانات والأذونات') }}
                </a>
            </li>
        </ul>
    </div>

    <!-- Advanced Filter Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header report-filter-header bg-transparent border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2 text-nowrap">
                <i class="bx bx-filter-alt text-primary"></i> {{ __('فلاتر التقرير') }}
            </h6>

            @if($reportType !== 'balances')
                <!-- Quick Date Presets -->
                @php
                    $todayDate = now()->format('Y-m-d');
                    $yesterdayDate = now()->subDay()->format('Y-m-d');
                    $startOfWeek = now()->startOfWeek(\Carbon\Carbon::SATURDAY)->format('Y-m-d');
                    $startOfMonth = now()->startOfMonth()->format('Y-m-d');
                    $startOfLastMonth = now()->subMonth()->startOfMonth()->format('Y-m-d');
                    $endOfLastMonth = now()->subMonth()->endOfMonth()->format('Y-m-d');

                    $curStart = request('start_date', $startOfMonth);
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
            @endif
        </div>

        <div class="card-body pt-3">
            <form id="leaveFilterForm" method="GET" action="{{ route('hr.reports.leaves.index') }}">
                <input type="hidden" name="report_type" value="{{ $reportType }}">
                
                <!-- Preserve currently selected columns in the filter form -->
                @foreach($availableColumns as $colKey => $colDef)
                    @if(!empty($selectedColumns[$colKey]))
                        <input type="hidden" name="columns[]" value="{{ $colKey }}">
                    @endif
                @endforeach

                <div class="row g-3">
                    @if($reportType !== 'balances')
                        <!-- Date From -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('من تاريخ') }}</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <input type="text" id="start_date" name="start_date" class="form-control js-flatpickr-date" 
                                       value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" placeholder="YYYY-MM-DD">
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
                    @endif

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

                    @if($reportType === 'leaves_log')
                        <!-- Leave Type -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('نوع الإجازة') }}</label>
                            <select name="leave_type" class="form-select select2">
                                <option value="">{{ __('جميع أنواع الإجازات') }}</option>
                                @foreach($leaveTypes as $lt)
                                    <option value="{{ $lt->value }}" {{ request('leave_type') == $lt->value ? 'selected' : '' }}>
                                        {{ $lt->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Leave Status -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('الحالة') }}</label>
                            <select name="status" class="form-select select2">
                                <option value="">{{ __('جميع الحالات') }}</option>
                                @foreach($leaveStatuses as $st)
                                    <option value="{{ $st->value }}" {{ request('status') == $st->value ? 'selected' : '' }}>
                                        {{ $st->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($reportType === 'permissions')
                        <!-- Permission Type -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('نوع الإذن') }}</label>
                            <select name="permission_type" class="form-select select2">
                                <option value="">{{ __('جميع الأنواع') }}</option>
                                @foreach($permissionTypes as $pt)
                                    <option value="{{ $pt->value }}" {{ request('permission_type') == $pt->value ? 'selected' : '' }}>
                                        {{ $pt->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Permission Status -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('الحالة') }}</label>
                            <select name="status" class="form-select select2">
                                <option value="">{{ __('جميع الحالات') }}</option>
                                @foreach($permissionStatuses as $pst)
                                    <option value="{{ $pst->value }}" {{ request('status') == $pst->value ? 'selected' : '' }}>
                                        {{ $pst->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Per Page -->
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-semibold">{{ __('عدد السجلات في الصفحة') }}</label>
                        <select name="per_page" class="form-select">
                            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-12 report-filter-actions d-flex justify-content-end gap-2 mt-2">
                        <a href="{{ route('hr.reports.leaves.index', ['report_type' => $reportType]) }}" class="btn btn-outline-secondary">
                            <i class="bx bx-reset me-1"></i> {{ __('إعادة تعيين') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-filter-alt me-1"></i> {{ __('تصفية النتائج') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        @foreach($availableColumns as $colKey => $colDef)
                            @if(!empty($selectedColumns[$colKey]))
                                <th class="{{ $colDef['class'] ?? '' }}" style="width: {{ $colDef['width'] ?? 'auto' }};">
                                    {{ $colDef['label'] }}
                                </th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if($reportType === 'leaves_log')
                        {{-- 1. Detailed Leave Requests Log --}}
                        @forelse($records as $index => $row)
                            <tr>
                                @if(!empty($selectedColumns['index']))
                                    <td class="text-center text-muted small">
                                        {{ method_exists($records, 'firstItem') ? ($records->firstItem() + $index) : ($index + 1) }}
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['code']))
                                    <td><span class="badge bg-label-secondary font-monospace">{{ $row->employee->employee_code ?? '-' }}</span></td>
                                @endif

                                @if(!empty($selectedColumns['employee']))
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $row->employee->name ?? '-' }}</div>
                                        <small class="text-muted">{{ $row->employee->jobTitle->name ?? '' }}</small>
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['department']))
                                    <td>{{ $row->employee->department->name ?? '-' }}</td>
                                @endif

                                @if(!empty($selectedColumns['branch']))
                                    <td>{{ $row->employee->primaryBranch->name ?? '-' }}</td>
                                @endif

                                @if(!empty($selectedColumns['leave_type']))
                                    <td class="text-center">
                                        @if($row->leave_type)
                                            <span class="badge bg-label-primary">{{ $row->leave_type->label() }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['start_date']))
                                    <td class="text-center font-monospace">{{ $row->start_date ? \Carbon\Carbon::parse($row->start_date)->format('Y-m-d') : '-' }}</td>
                                @endif

                                @if(!empty($selectedColumns['end_date']))
                                    <td class="text-center font-monospace">{{ $row->end_date ? \Carbon\Carbon::parse($row->end_date)->format('Y-m-d') : '-' }}</td>
                                @endif

                                @if(!empty($selectedColumns['days_count']))
                                    <td class="text-center">
                                        <span class="badge bg-label-info fw-bold">{{ $row->days_count }} {{ __('أيام') }}</span>
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['replacement']))
                                    <td>{{ $row->replacementEmployee->name ?? '-' }}</td>
                                @endif

                                @if(!empty($selectedColumns['status']))
                                    <td class="text-center">
                                        @if($row->status)
                                            <span class="badge {{ $row->status->badgeClass() }}">{{ $row->status->label() }}</span>
                                        @else
                                            <span class="badge bg-label-secondary">-</span>
                                        @endif
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['reason']))
                                    <td><small class="text-muted">{{ $row->reason ?: '-' }}</small></td>
                                @endif

                                @if(!empty($selectedColumns['created_at']))
                                    <td class="text-center font-monospace small text-muted">{{ $row->created_at ? $row->created_at->format('Y-m-d') : '-' }}</td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="20" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-label-secondary rounded-circle mb-3 p-3">
                                            <i class="bx bx-calendar-x bx-lg"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1 text-dark">{{ __('لا توجد طلبات إجازات تطابق شروط الفلترة') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('يرجى تغيير نطاق التواريخ أو معايير الفلترة وإعادة المحاولة.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    @elseif($reportType === 'permissions')
                        {{-- 2. Permission Requests Report --}}
                        @forelse($records as $index => $row)
                            <tr>
                                @if(!empty($selectedColumns['index']))
                                    <td class="text-center text-muted small">
                                        {{ method_exists($records, 'firstItem') ? ($records->firstItem() + $index) : ($index + 1) }}
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['code']))
                                    <td><span class="badge bg-label-secondary font-monospace">{{ $row->employee->employee_code ?? '-' }}</span></td>
                                @endif

                                @if(!empty($selectedColumns['employee']))
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $row->employee->name ?? '-' }}</div>
                                        <small class="text-muted">{{ $row->employee->jobTitle->name ?? '' }}</small>
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['department']))
                                    <td>{{ $row->employee->department->name ?? '-' }}</td>
                                @endif

                                @if(!empty($selectedColumns['branch']))
                                    <td>{{ $row->employee->primaryBranch->name ?? '-' }}</td>
                                @endif

                                @if(!empty($selectedColumns['date']))
                                    <td class="text-center font-monospace">{{ $row->date ? \Carbon\Carbon::parse($row->date)->format('Y-m-d') : '-' }}</td>
                                @endif

                                @if(!empty($selectedColumns['permission_type']))
                                    <td class="text-center">
                                        @if($row->type)
                                            <span class="badge {{ $row->type->badgeClass() }}">{{ $row->type->label() }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['start_time']))
                                    <td class="text-center font-monospace">{{ $row->start_time ?? '-' }}</td>
                                @endif

                                @if(!empty($selectedColumns['end_time']))
                                    <td class="text-center font-monospace">{{ $row->end_time ?? '-' }}</td>
                                @endif

                                @if(!empty($selectedColumns['minutes']))
                                    <td class="text-center">
                                        <span class="badge bg-label-info fw-bold">{{ $row->duration_minutes }} {{ __('دقيقة') }}</span>
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['hours']))
                                    <td class="text-center font-monospace">{{ round(($row->duration_minutes ?? 0) / 60, 2) }} {{ __('ساعة') }}</td>
                                @endif

                                @if(!empty($selectedColumns['status']))
                                    <td class="text-center">
                                        @if($row->status)
                                            <span class="badge {{ $row->status->badgeClass() }}">{{ $row->status->label() }}</span>
                                        @else
                                            <span class="badge bg-label-secondary">-</span>
                                        @endif
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['reason']))
                                    <td><small class="text-muted">{{ $row->reason ?: '-' }}</small></td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="20" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-label-secondary rounded-circle mb-3 p-3">
                                            <i class="bx bx-time-five bx-lg"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1 text-dark">{{ __('لا توجد سجلات أذونات واستئذانات تطابق شروط الفلترة') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('يرجى تغيير نطاق التواريخ أو معايير الفلترة وإعادة المحاولة.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    @else
                        {{-- 3. Leave Balances Report --}}
                        @forelse($records as $index => $row)
                            <tr>
                                @if(!empty($selectedColumns['index']))
                                    <td class="text-center text-muted small">
                                        {{ method_exists($records, 'firstItem') ? ($records->firstItem() + $index) : ($index + 1) }}
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['code']))
                                    <td><span class="badge bg-label-secondary font-monospace">{{ $row->employee->employee_code ?? '-' }}</span></td>
                                @endif

                                @if(!empty($selectedColumns['employee']))
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $row->employee->name ?? '-' }}</div>
                                        <small class="text-muted">{{ $row->employee->jobTitle->name ?? '' }}</small>
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['department']))
                                    <td>{{ $row->employee->department->name ?? '-' }}</td>
                                @endif

                                @if(!empty($selectedColumns['branch']))
                                    <td>{{ $row->employee->primaryBranch->name ?? '-' }}</td>
                                @endif

                                @if(!empty($selectedColumns['hire_date']))
                                    <td class="text-center font-monospace small">
                                        {{ $row->hire_date ? \Carbon\Carbon::parse($row->hire_date)->format('Y-m-d') : '-' }}
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['annual_entitled']))
                                    <td class="text-center font-monospace">{{ $row->annual_entitled }}</td>
                                @endif

                                @if(!empty($selectedColumns['annual_consumed']))
                                    <td class="text-center">
                                        @if($row->annual_consumed > 0)
                                            <span class="badge bg-label-warning fw-bold">{{ $row->annual_consumed }}</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['annual_pending']))
                                    <td class="text-center">
                                        @if($row->annual_pending > 0)
                                            <span class="badge bg-label-info fw-bold">{{ $row->annual_pending }}</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['annual_remaining']))
                                    <td class="text-center">
                                        <span class="badge bg-label-success fw-bold fs-6">{{ $row->annual_remaining }}</span>
                                    </td>
                                @endif

                                @if(!empty($selectedColumns['casual_consumed']))
                                    <td class="text-center font-monospace">{{ $row->casual_consumed }} / {{ $row->casual_entitled }}</td>
                                @endif

                                @if(!empty($selectedColumns['sick_consumed']))
                                    <td class="text-center font-monospace">{{ $row->sick_consumed }} / {{ $row->sick_entitled }}</td>
                                @endif

                                @if(!empty($selectedColumns['total_entitled']))
                                    <td class="text-center font-monospace">{{ $row->total_entitled }}</td>
                                @endif

                                @if(!empty($selectedColumns['total_consumed']))
                                    <td class="text-center font-monospace">{{ $row->total_consumed }}</td>
                                @endif

                                @if(!empty($selectedColumns['total_remaining']))
                                    <td class="text-center font-monospace fw-bold text-success">{{ $row->total_remaining }}</td>
                                @endif

                                @if(!empty($selectedColumns['consumption_rate']))
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <div class="progress w-100" style="height: 6px; min-width: 60px;">
                                                <div class="progress-bar {{ $row->consumption_rate > 80 ? 'bg-danger' : ($row->consumption_rate > 50 ? 'bg-warning' : 'bg-primary') }}" 
                                                     role="progressbar" style="width: {{ min(100, $row->consumption_rate) }}%"></div>
                                            </div>
                                            <span class="small fw-semibold">{{ $row->consumption_rate }}%</span>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="20" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-label-secondary rounded-circle mb-3 p-3">
                                            <i class="bx bx-user-x bx-lg"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1 text-dark">{{ __('لا توجد بيانات موظفين تطابق شروط الفلترة') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('يرجى تغيير معايير الفلترة وإعادة المحاولة.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>

        @include('hr::reports._pagination', ['records' => $records])
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

        if (startInput._flatpickr) {
            startInput._flatpickr.setDate(startVal, true);
        }
        if (endInput._flatpickr) {
            endInput._flatpickr.setDate(endVal, true);
        }

        const filterForm = document.getElementById('leaveFilterForm');
        if (filterForm) {
            filterForm.submit();
        }
    };
</script>
@endsection

@endsection
