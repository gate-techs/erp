@extends('layouts/contentNavbarLayout')

@section('title', __('تقارير الرواتب والمالية'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Page Header & Action Buttons -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3">
        <div>
            <h4 class="report-page-title fw-bold py-1 mb-0">
                <span class="text-muted fw-light">{{ __('الموارد البشرية') }} / {{ __('التقارير') }} /</span> 
                @if($reportType === 'loans')
                    {{ __('تقرير السلف والأقساط') }}
                @elseif($reportType === 'rewards_penalties')
                    {{ __('تقرير المكافآت والجزاءات') }}
                @elseif($reportType === 'eos')
                    {{ __('تقرير مخصصات نهاية الخدمة (EOS)') }}
                @else
                    {{ __('مسير الرواتب الشهري المجمع') }}
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
                    <form id="columnsForm" method="GET" action="{{ route('hr.reports.payrolls.index') }}">
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
                                @if($colKey !== 'actions')
                                    <div class="form-check form-switch mb-1">
                                        <input class="form-check-input col-toggle-checkbox" type="checkbox" name="columns[]" value="{{ $colKey }}" id="col_{{ $colKey }}" {{ !empty($selectedColumns[$colKey]) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="col_{{ $colKey }}">
                                            {{ $colDef['label'] }}
                                        </label>
                                    </div>
                                @endif
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
            @can('hr.reports.payrolls.export')
            <a href="{{ route('hr.reports.payrolls.export', request()->all()) }}" class="btn btn-label-success">
                <i class="bx bx-export me-1"></i> {{ __('تصدير Excel') }}
            </a>
            @endcan

            <!-- Smart Split Print Button -->
            @can('hr.reports.payrolls.print')
            <div class="btn-group">
                <a href="{{ route('hr.reports.payrolls.print', request()->all()) }}" target="_blank" class="btn btn-primary">
                    <i class="bx bx-printer me-1"></i> {{ __('طباعة الكشف') }}
                </a>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden">{{ __('خيارات القوالب') }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><h6 class="dropdown-header text-uppercase small">{{ __('اختر قالب الطباعة:') }}</h6></li>
                    @forelse($printTemplates as $tpl)
                        <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between py-2" target="_blank" 
                               href="{{ route('hr.reports.payrolls.print', array_merge(request()->all(), ['template_id' => $tpl->id])) }}">
                                <span>
                                    <i class="bx bx-file me-2 text-primary"></i> {{ $tpl->display_name }}
                                    <small class="text-muted d-block ps-4">({{ $tpl->page_size }} - {{ ucfirst($tpl->orientation) }})</small>
                                </span>
                                @if($tpl->code === 'payslip_voucher' || $tpl->is_default)
                                    <span class="badge bg-label-primary small">{{ __('موصى به') }}</span>
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
                <a class="nav-link {{ $reportType === 'payroll_sheet' ? 'active' : '' }}" 
                   href="{{ route('hr.reports.payrolls.index', array_merge(request()->except(['page', 'columns']), ['report_type' => 'payroll_sheet'])) }}">
                    <i class="bx bx-spreadsheet me-1"></i> {{ __('مسير الرواتب الشهري المجمع') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $reportType === 'loans' ? 'active' : '' }}" 
                   href="{{ route('hr.reports.payrolls.index', array_merge(request()->except(['page', 'columns']), ['report_type' => 'loans'])) }}">
                    <i class="bx bx-credit-card me-1"></i> {{ __('تقرير السلف والأقساط') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $reportType === 'rewards_penalties' ? 'active' : '' }}" 
                   href="{{ route('hr.reports.payrolls.index', array_merge(request()->except(['page', 'columns']), ['report_type' => 'rewards_penalties'])) }}">
                    <i class="bx bx-badge-check me-1"></i> {{ __('تقرير المكافآت والجزاءات') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $reportType === 'eos' ? 'active' : '' }}" 
                   href="{{ route('hr.reports.payrolls.index', array_merge(request()->except(['page', 'columns']), ['report_type' => 'eos'])) }}">
                    <i class="bx bx-shield-quarter me-1"></i> {{ __('مخصصات نهاية الخدمة (EOS)') }}
                </a>
            </li>
        </ul>
    </div>

    <!-- Advanced Filter Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header report-filter-header bg-transparent border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2 text-nowrap">
                <i class="bx bx-filter-alt text-primary"></i> {{ __('معايير التصفية والبحث') }}
            </h6>

            @if($reportType === 'rewards_penalties' || $reportType === 'loans')
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
            <form id="payrollFilterForm" method="GET" action="{{ route('hr.reports.payrolls.index') }}">
                <input type="hidden" name="report_type" value="{{ $reportType }}">
                
                <!-- Preserve currently selected columns in the filter form -->
                @foreach($availableColumns as $colKey => $colDef)
                    @if(!empty($selectedColumns[$colKey]))
                        <input type="hidden" name="columns[]" value="{{ $colKey }}">
                    @endif
                @endforeach

                <div class="row g-3">
                    @if($reportType === 'payroll_sheet')
                        <!-- Payroll Month -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('شهر المسير') }}</label>
                            <select name="payroll_month" class="form-select select2">
                                <option value="">{{ __('جميع الأشهر') }}</option>
                                @php
                                    $uniqueMonths = $payrolls->pluck('payroll_month')->unique()->filter()->values();
                                @endphp
                                @foreach($uniqueMonths as $m)
                                    <option value="{{ $m }}" {{ request('payroll_month') == $m ? 'selected' : '' }}>
                                        {{ $m }} ({{ \Carbon\Carbon::parse($m . '-01')->translatedFormat('F Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Specific Payroll Run -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('مسير محدد') }}</label>
                            <select name="payroll_id" class="form-select select2">
                                <option value="">{{ __('جميع المسيرات') }}</option>
                                @foreach($payrolls as $p)
                                    <option value="{{ $p->id }}" {{ request('payroll_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->payroll_number }} - {{ $p->payroll_month }} ({{ $p->title ?: __('مسير عام') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Payment Status -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('حالة الصرف') }}</label>
                            <select name="payment_status" class="form-select select2">
                                <option value="">{{ __('جميع الحالات') }}</option>
                                @foreach($paymentStatuses as $pst)
                                    <option value="{{ $pst->value }}" {{ request('payment_status') == $pst->value ? 'selected' : '' }}>
                                        {{ $pst->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    @elseif($reportType === 'loans')
                        <!-- Loan Status -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('حالة السلفة') }}</label>
                            <select name="status" class="form-select select2">
                                <option value="">{{ __('جميع الحالات') }}</option>
                                @foreach($loanStatuses as $ls)
                                    <option value="{{ $ls->value }}" {{ request('status') == $ls->value ? 'selected' : '' }}>
                                        {{ $ls->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Loan Type -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('نوع السلفة') }}</label>
                            <select name="loan_type" class="form-select select2">
                                <option value="">{{ __('جميع الأنواع') }}</option>
                                @foreach($loanTypes as $lt)
                                    <option value="{{ $lt->value }}" {{ request('loan_type') == $lt->value ? 'selected' : '' }}>
                                        {{ $lt->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Start Date -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('من تاريخ') }}</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <input type="text" id="start_date" name="start_date" class="form-control js-flatpickr-date" 
                                       value="{{ request('start_date') }}" placeholder="YYYY-MM-DD">
                            </div>
                        </div>

                        <!-- End Date -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('إلى تاريخ') }}</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <input type="text" id="end_date" name="end_date" class="form-control js-flatpickr-date" 
                                       value="{{ request('end_date') }}" placeholder="YYYY-MM-DD">
                            </div>
                        </div>

                    @elseif($reportType === 'rewards_penalties')
                        <!-- Item Kind -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('النوع') }}</label>
                            <select name="item_type" class="form-select select2">
                                <option value="all" {{ request('item_type', 'all') === 'all' ? 'selected' : '' }}>{{ __('الكل (مكافآت وجزاءات)') }}</option>
                                <option value="reward" {{ request('item_type') === 'reward' ? 'selected' : '' }}>{{ __('مكافآت وحوافز فقط') }}</option>
                                <option value="penalty" {{ request('item_type') === 'penalty' ? 'selected' : '' }}>{{ __('جزاءات واستقطاعات فقط') }}</option>
                            </select>
                        </div>

                        <!-- Start Date -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('من تاريخ') }}</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <input type="text" id="start_date" name="start_date" class="form-control js-flatpickr-date" 
                                       value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" placeholder="YYYY-MM-DD">
                            </div>
                        </div>

                        <!-- End Date -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('إلى تاريخ') }}</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <input type="text" id="end_date" name="end_date" class="form-control js-flatpickr-date" 
                                       value="{{ request('end_date', now()->format('Y-m-d')) }}" placeholder="YYYY-MM-DD">
                            </div>
                        </div>

                    @elseif($reportType === 'eos')
                        <!-- As of Date -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('حتى تاريخ (As of Date)') }}</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <input type="text" id="as_of_date" name="as_of_date" class="form-control js-flatpickr-date" 
                                       value="{{ request('as_of_date', now()->format('Y-m-d')) }}" placeholder="YYYY-MM-DD">
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
                        <a href="{{ route('hr.reports.payrolls.index', ['report_type' => $reportType]) }}" class="btn btn-outline-secondary">
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
                    @if($reportType === 'loans')
                        {{-- 1. Loans & Installments Report --}}
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
                                @if(!empty($selectedColumns['loan_type']))
                                    <td class="text-center">
                                        <span class="badge bg-label-primary">{{ $row->type?->label() ?? '-' }}</span>
                                    </td>
                                @endif
                                @if(!empty($selectedColumns['start_date']))
                                    <td class="text-center font-monospace">{{ $row->start_date ? \Carbon\Carbon::parse($row->start_date)->format('Y-m-d') : '-' }}</td>
                                @endif
                                @if(!empty($selectedColumns['amount']))
                                    <td class="text-center font-monospace fw-bold">{{ number_format($row->amount, 2) }}</td>
                                @endif
                                @if(!empty($selectedColumns['installments_count']))
                                    <td class="text-center">{{ $row->installments_count }}</td>
                                @endif
                                @if(!empty($selectedColumns['monthly_installment']))
                                    <td class="text-center font-monospace">{{ number_format($row->monthly_installment, 2) }}</td>
                                @endif
                                @if(!empty($selectedColumns['total_paid']))
                                    <td class="text-center font-monospace text-success fw-semibold">{{ number_format($row->total_paid, 2) }}</td>
                                @endif
                                @if(!empty($selectedColumns['remaining_amount']))
                                    <td class="text-center font-monospace text-danger fw-bold">{{ number_format($row->remaining_amount, 2) }}</td>
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="20" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-label-secondary rounded-circle mb-3 p-3">
                                            <i class="bx bx-credit-card bx-lg"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1 text-dark">{{ __('لا توجد سجلات سلف تطابق شروط الفلترة') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('يرجى تغيير معايير البحث وإعادة المحاولة.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    @elseif($reportType === 'rewards_penalties')
                        {{-- 2. Rewards & Penalties Report --}}
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
                                @if(!empty($selectedColumns['item_kind']))
                                    <td class="text-center">
                                        @if($row->item_kind === 'reward')
                                            <span class="badge bg-label-success"><i class="bx bx-plus me-1"></i> {{ __('مكافأة') }}</span>
                                        @else
                                            <span class="badge bg-label-danger"><i class="bx bx-minus me-1"></i> {{ __('جزاء') }}</span>
                                        @endif
                                    </td>
                                @endif
                                @if(!empty($selectedColumns['date']))
                                    <td class="text-center font-monospace">{{ $row->date ? \Carbon\Carbon::parse($row->date)->format('Y-m-d') : '-' }}</td>
                                @endif
                                @if(!empty($selectedColumns['amount']))
                                    <td class="text-center font-monospace fw-bold {{ $row->item_kind === 'reward' ? 'text-success' : 'text-danger' }}">
                                        {{ $row->item_kind === 'reward' ? '+' : '-' }}{{ number_format($row->amount, 2) }}
                                    </td>
                                @endif
                                @if(!empty($selectedColumns['type_name']))
                                    <td><span class="fw-semibold text-dark">{{ $row->type_name }}</span></td>
                                @endif
                                @if(!empty($selectedColumns['reason']))
                                    <td><small class="text-muted">{{ $row->reason ?: '-' }}</small></td>
                                @endif
                                @if(!empty($selectedColumns['status']))
                                    <td class="text-center">
                                        @if($row->status && method_exists($row->status, 'label'))
                                            <span class="badge {{ method_exists($row->status, 'badgeClass') ? $row->status->badgeClass() : 'bg-label-primary' }}">{{ $row->status->label() }}</span>
                                        @else
                                            <span class="badge bg-label-secondary">-</span>
                                        @endif
                                    </td>
                                @endif
                                @if(!empty($selectedColumns['approved_by']))
                                    <td><small class="text-muted">{{ $row->approved_by }}</small></td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="20" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-label-secondary rounded-circle mb-3 p-3">
                                            <i class="bx bx-badge-check bx-lg"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1 text-dark">{{ __('لا توجد سجلات مكافآت أو جزاءات تطابق الفلترة') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('يرجى تغيير نطاق التواريخ أو معايير الفلترة وإعادة المحاولة.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    @elseif($reportType === 'eos')
                        {{-- 3. End of Service Provisions Report --}}
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
                                    <td class="text-center font-monospace">{{ $row->hire_date }}</td>
                                @endif
                                @if(!empty($selectedColumns['service_duration']))
                                    <td class="text-center font-monospace fw-semibold text-dark">{{ $row->service_duration_text }}</td>
                                @endif
                                @if(!empty($selectedColumns['monthly_salary']))
                                    <td class="text-center font-monospace">{{ number_format($row->monthly_salary, 2) }}</td>
                                @endif
                                @if(!empty($selectedColumns['tier1_provision']))
                                    <td class="text-center font-monospace text-muted">{{ number_format($row->tier1_provision, 2) }}</td>
                                @endif
                                @if(!empty($selectedColumns['tier2_provision']))
                                    <td class="text-center font-monospace text-muted">{{ number_format($row->tier2_provision, 2) }}</td>
                                @endif
                                @if(!empty($selectedColumns['total_eos_provision']))
                                    <td class="text-center font-monospace fw-bold text-primary fs-6">
                                        {{ number_format($row->total_eos_provision, 2) }}
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="20" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-label-secondary rounded-circle mb-3 p-3">
                                            <i class="bx bx-shield-quarter bx-lg"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1 text-dark">{{ __('لا توجد سجلات موظفين لاحتساب مخصص نهاية الخدمة') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('يرجى التحقق من بيانات التعيين والرواتب وإعادة المحاولة.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    @else
                        {{-- 4. Monthly Payroll Sheet --}}
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
                                @if(!empty($selectedColumns['basic_salary']))
                                    <td class="text-center font-monospace">{{ number_format($row->basic_salary, 2) }}</td>
                                @endif
                                @if(!empty($selectedColumns['allowances']))
                                    <td class="text-center font-monospace text-info">{{ number_format($row->allowances_amount, 2) }}</td>
                                @endif
                                @if(!empty($selectedColumns['overtime']))
                                    <td class="text-center font-monospace">{{ number_format($row->overtime_amount, 2) }}</td>
                                @endif
                                @if(!empty($selectedColumns['rewards']))
                                    <td class="text-center font-monospace text-success">{{ number_format($row->rewards_amount, 2) }}</td>
                                @endif
                                @if(!empty($selectedColumns['gross_salary']))
                                    <td class="text-center font-monospace fw-bold text-dark">{{ number_format($row->gross_salary, 2) }}</td>
                                @endif
                                @if(!empty($selectedColumns['late_deduction']))
                                    <td class="text-center font-monospace text-danger">{{ number_format($row->late_deduction, 2) }}</td>
                                @endif
                                @if(!empty($selectedColumns['absence_deduction']))
                                    <td class="text-center font-monospace text-danger">{{ number_format($row->absence_deduction, 2) }}</td>
                                @endif
                                @if(!empty($selectedColumns['loans_deduction']))
                                    <td class="text-center font-monospace text-danger">{{ number_format($row->loans_deduction, 2) }}</td>
                                @endif
                                @if(!empty($selectedColumns['penalties_deduction']))
                                    <td class="text-center font-monospace text-danger">{{ number_format($row->penalties_deduction, 2) }}</td>
                                @endif
                                @if(!empty($selectedColumns['insurance_deduction']))
                                    <td class="text-center font-monospace text-danger">{{ number_format($row->insurance_deduction, 2) }}</td>
                                @endif
                                @if(!empty($selectedColumns['total_deductions']))
                                    <td class="text-center font-monospace fw-bold text-danger">{{ number_format($row->total_deductions, 2) }}</td>
                                @endif
                                @if(!empty($selectedColumns['net_salary']))
                                    <td class="text-center font-monospace fw-bold text-success fs-6">{{ number_format($row->net_salary, 2) }}</td>
                                @endif
                                @if(!empty($selectedColumns['payment_status']))
                                    <td class="text-center">
                                        @if($row->payment_status)
                                            <span class="badge {{ $row->payment_status->badgeClass() }}">{{ $row->payment_status->label() }}</span>
                                        @else
                                            <span class="badge bg-label-secondary">-</span>
                                        @endif
                                    </td>
                                @endif
                                @if(!empty($selectedColumns['actions']))
                                    <td class="text-center">
                                        <a href="{{ route('hr.reports.payrolls.payslip', $row->id) }}" target="_blank" class="btn btn-icon btn-sm btn-label-primary" title="{{ __('طباعة قسيمة الراتب') }}">
                                            <i class="bx bx-receipt"></i>
                                        </a>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="20" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-label-secondary rounded-circle mb-3 p-3">
                                            <i class="bx bx-spreadsheet bx-lg"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1 text-dark">{{ __('لا توجد سجلات مسير رواتب تطابق معايير الفلترة') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('يرجى تغيير الشهر أو معايير الفلترة وإعادة المحاولة.') }}</p>
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
            const day = now.getDay();
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

        const filterForm = document.getElementById('payrollFilterForm');
        if (filterForm) {
            filterForm.submit();
        }
    };
</script>
@endsection

@endsection
