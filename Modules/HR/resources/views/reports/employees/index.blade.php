@extends('layouts/contentNavbarLayout')

@section('title', __('تقارير بيانات وملفات الموظفين'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Page Header & Action Buttons -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3">
        <div>
            <h4 class="fw-bold py-1 mb-0">
                <span class="text-muted fw-light">{{ __('الموارد البشرية') }} / {{ __('التقارير') }} /</span> 
                @if($reportType === 'expiring_docs')
                    {{ __('تقرير انتهاء وثائق الموظفين') }}
                @elseif($reportType === 'demographics')
                    {{ __('تقرير توزيع الموظفين والتكلفة') }}
                @else
                    {{ __('دليل الموظفين الشامل') }}
                @endif
            </h4>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if($reportType !== 'demographics')
                <!-- Column Selector Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="columnSelectorDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <i class="bx bx-columns me-1"></i> {{ __('تخصيص الأعمدة') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg" style="min-width: 280px; max-height: 400px; overflow-y: auto;" aria-labelledby="columnSelectorDropdown">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="fw-bold text-dark small">{{ __('الأعمدة الظاهرة في الجدول والطباعة') }}</span>
                        </div>
                        <form id="columnsForm" method="GET" action="{{ route('hr.reports.employees.index') }}">
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
                                    @if($colKey !== 'action')
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
            @endif

            <!-- Export CSV / Excel Button -->
            <a href="{{ route('hr.reports.employees.export', request()->all()) }}" class="btn btn-label-success">
                <i class="bx bx-export me-1"></i> {{ __('تصدير Excel') }}
            </a>

            <!-- Smart Split Print Button -->
            <div class="btn-group">
                <a href="{{ route('hr.reports.employees.print', request()->all()) }}" target="_blank" class="btn btn-primary">
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
                               href="{{ route('hr.reports.employees.print', array_merge(request()->all(), ['template_id' => $tpl->id])) }}">
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
        <ul class="nav nav-pills flex-column flex-md-row gap-2">
            <li class="nav-item">
                <a class="nav-link {{ $reportType === 'master' ? 'active' : '' }}" 
                   href="{{ route('hr.reports.employees.index', array_merge(request()->except(['page', 'columns']), ['report_type' => 'master'])) }}">
                    <i class="bx bx-id-card me-1"></i> {{ __('دليل الموظفين الشامل') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $reportType === 'expiring_docs' ? 'active' : '' }}" 
                   href="{{ route('hr.reports.employees.index', array_merge(request()->except(['page', 'columns']), ['report_type' => 'expiring_docs'])) }}">
                    <i class="bx bx-error-circle me-1 text-warning"></i> {{ __('انتهاء وثائق الموظفين') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $reportType === 'demographics' ? 'active' : '' }}" 
                   href="{{ route('hr.reports.employees.index', array_merge(request()->except(['page', 'columns']), ['report_type' => 'demographics'])) }}">
                    <i class="bx bx-pie-chart-alt me-1"></i> {{ __('توزيع الموظفين والتكلفة') }}
                </a>
            </li>
        </ul>
    </div>

    <!-- Advanced Filter Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bx bx-filter-alt text-primary"></i> {{ __('معايير التصفية والبحث') }}
            </h6>

            @if($reportType === 'expiring_docs')
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('hr.reports.employees.index', array_merge(request()->all(), ['expiry_range' => 'all'])) }}" 
                       class="btn btn-outline-primary {{ request('expiry_range', 'all') === 'all' ? 'active' : '' }}">{{ __('الكل') }}</a>
                    <a href="{{ route('hr.reports.employees.index', array_merge(request()->all(), ['expiry_range' => 'expired'])) }}" 
                       class="btn btn-outline-danger {{ request('expiry_range') === 'expired' ? 'active' : '' }}">{{ __('المنتهية') }}</a>
                    <a href="{{ route('hr.reports.employees.index', array_merge(request()->all(), ['expiry_range' => '30_days'])) }}" 
                       class="btn btn-outline-danger {{ request('expiry_range') === '30_days' ? 'active' : '' }}">{{ __('خلال 30 يوم') }}</a>
                    <a href="{{ route('hr.reports.employees.index', array_merge(request()->all(), ['expiry_range' => '60_days'])) }}" 
                       class="btn btn-outline-warning {{ request('expiry_range') === '60_days' ? 'active' : '' }}">{{ __('خلال 60 يوم') }}</a>
                    <a href="{{ route('hr.reports.employees.index', array_merge(request()->all(), ['expiry_range' => '90_days'])) }}" 
                       class="btn btn-outline-info {{ request('expiry_range') === '90_days' ? 'active' : '' }}">{{ __('خلال 90 يوم') }}</a>
                </div>
            @endif
        </div>

        <div class="card-body pt-3">
            <form id="employeeFilterForm" method="GET" action="{{ route('hr.reports.employees.index') }}">
                <input type="hidden" name="report_type" value="{{ $reportType }}">
                
                <!-- Preserve currently selected columns in the filter form -->
                @foreach($availableColumns as $colKey => $colDef)
                    @if(!empty($selectedColumns[$colKey]))
                        <input type="hidden" name="columns[]" value="{{ $colKey }}">
                    @endif
                @endforeach

                <div class="row g-3">
                    @if($reportType === 'master')
                        <!-- Keyword Search -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('بحث عام (اسم / كود / هاتف / هوية)') }}</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="{{ __('اكتب للبحث...') }}">
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('حالة العمل') }}</label>
                            <select name="status" class="form-select select2">
                                <option value="">{{ __('جميع الحالات') }}</option>
                                @foreach($employeeStatuses as $st)
                                    <option value="{{ $st->value }}" {{ request('status') == $st->value ? 'selected' : '' }}>
                                        {{ $st->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Job Title -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('المسمى الوظيفي') }}</label>
                            <select name="job_title_id" class="form-select select2">
                                <option value="">{{ __('جميع المسميات') }}</option>
                                @foreach($jobTitles as $jt)
                                    <option value="{{ $jt->id }}" {{ request('job_title_id') == $jt->id ? 'selected' : '' }}>
                                        {{ $jt->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Employment Type -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('نوع التوظيف') }}</label>
                            <select name="employment_type" class="form-select select2">
                                <option value="">{{ __('جميع الأنواع') }}</option>
                                @foreach($employmentTypes as $et)
                                    <option value="{{ $et->value }}" {{ request('employment_type') == $et->value ? 'selected' : '' }}>
                                        {{ $et->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Nationality -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('الجنسية') }}</label>
                            <select name="nationality" class="form-select select2">
                                <option value="">{{ __('جميع الجنسيات') }}</option>
                                @foreach($nationalities as $nat)
                                    <option value="{{ $nat }}" {{ request('nationality') == $nat ? 'selected' : '' }}>
                                        {{ $nat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Gender -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('الجنس') }}</label>
                            <select name="gender" class="form-select select2">
                                <option value="">{{ __('الكل') }}</option>
                                @foreach($genders as $g)
                                    <option value="{{ $g->value }}" {{ request('gender') == $g->value ? 'selected' : '' }}>
                                        {{ $g->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($reportType === 'expiring_docs')
                        <!-- Expiry Range -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('نطاق الانتهاء') }}</label>
                            <select name="expiry_range" class="form-select">
                                <option value="all" {{ request('expiry_range', 'all') === 'all' ? 'selected' : '' }}>{{ __('جميع الوثائق') }}</option>
                                <option value="expired" {{ request('expiry_range') === 'expired' ? 'selected' : '' }}>{{ __('المنتهية بالفعل فقط') }}</option>
                                <option value="30_days" {{ request('expiry_range') === '30_days' ? 'selected' : '' }}>{{ __('تنتهي خلال 30 يوم') }}</option>
                                <option value="60_days" {{ request('expiry_range') === '60_days' ? 'selected' : '' }}>{{ __('تنتهي خلال 60 يوم') }}</option>
                                <option value="90_days" {{ request('expiry_range') === '90_days' ? 'selected' : '' }}>{{ __('تنتهي خلال 90 يوم') }}</option>
                            </select>
                        </div>

                        <!-- Document Type -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-semibold">{{ __('نوع المستند') }}</label>
                            <select name="document_type_id" class="form-select select2">
                                <option value="">{{ __('جميع أنواع المستندات') }}</option>
                                @foreach($documentTypes as $dt)
                                    <option value="{{ $dt->id }}" {{ request('document_type_id') == $dt->id ? 'selected' : '' }}>
                                        {{ $dt->name }}
                                    </option>
                                @endforeach
                            </select>
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

                    @if($reportType !== 'demographics')
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
                    @endif

                    <!-- Action Buttons -->
                    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                        <a href="{{ route('hr.reports.employees.index', ['report_type' => $reportType]) }}" class="btn btn-outline-secondary">
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

    @if($reportType === 'demographics')
        {{-- Demographics & Cost Breakdown Mode --}}
        <div class="row g-4">
            <!-- 1. Breakdown By Department -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bx bx-buildings text-primary"></i> {{ __('توزيع الموظفين والتكلفة حسب الأقسام') }}
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('القسم') }}</th>
                                    <th class="text-center">{{ __('العدد') }}</th>
                                    <th style="width: 140px;">{{ __('النسبة') }}</th>
                                    <th class="text-center">{{ __('إجمالي الرواتب') }}</th>
                                    <th class="text-center">{{ __('المتوسط') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($demographics['byDepartment'] as $row)
                                    <tr>
                                        <td><strong>{{ $row->name }}</strong></td>
                                        <td class="text-center fw-bold">{{ $row->count }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress w-100" style="height: 6px;">
                                                    <div class="progress-bar bg-primary" style="width: {{ $row->percentage }}%"></div>
                                                </div>
                                                <small class="fw-semibold">{{ $row->percentage }}%</small>
                                            </div>
                                        </td>
                                        <td class="text-center font-monospace">{{ number_format($row->total_salary, 2) }}</td>
                                        <td class="text-center font-monospace text-muted">{{ number_format($row->avg_salary, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-3 text-muted">{{ __('لا توجد بيانات') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 2. Breakdown By Nationality -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bx bx-globe text-success"></i> {{ __('توزيع الموظفين والتكلفة حسب الجنسيات') }}
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('الجنسية') }}</th>
                                    <th class="text-center">{{ __('العدد') }}</th>
                                    <th style="width: 140px;">{{ __('النسبة') }}</th>
                                    <th class="text-center">{{ __('إجمالي الرواتب') }}</th>
                                    <th class="text-center">{{ __('المتوسط') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($demographics['byNationality'] as $row)
                                    <tr>
                                        <td><strong>{{ $row->name }}</strong></td>
                                        <td class="text-center fw-bold">{{ $row->count }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress w-100" style="height: 6px;">
                                                    <div class="progress-bar bg-success" style="width: {{ $row->percentage }}%"></div>
                                                </div>
                                                <small class="fw-semibold">{{ $row->percentage }}%</small>
                                            </div>
                                        </td>
                                        <td class="text-center font-monospace">{{ number_format($row->total_salary, 2) }}</td>
                                        <td class="text-center font-monospace text-muted">{{ number_format($row->avg_salary, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-3 text-muted">{{ __('لا توجد بيانات') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 3. Breakdown By Branch -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bx bx-git-branch text-info"></i> {{ __('توزيع الموظفين والتكلفة حسب الفروع') }}
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('الفرع') }}</th>
                                    <th class="text-center">{{ __('العدد') }}</th>
                                    <th style="width: 140px;">{{ __('النسبة') }}</th>
                                    <th class="text-center">{{ __('إجمالي الرواتب') }}</th>
                                    <th class="text-center">{{ __('المتوسط') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($demographics['byBranch'] as $row)
                                    <tr>
                                        <td><strong>{{ $row->name }}</strong></td>
                                        <td class="text-center fw-bold">{{ $row->count }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress w-100" style="height: 6px;">
                                                    <div class="progress-bar bg-info" style="width: {{ $row->percentage }}%"></div>
                                                </div>
                                                <small class="fw-semibold">{{ $row->percentage }}%</small>
                                            </div>
                                        </td>
                                        <td class="text-center font-monospace">{{ number_format($row->total_salary, 2) }}</td>
                                        <td class="text-center font-monospace text-muted">{{ number_format($row->avg_salary, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-3 text-muted">{{ __('لا توجد بيانات') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 4. Breakdown By Job Title -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bx bx-briefcase text-warning"></i> {{ __('توزيع الموظفين والتكلفة حسب المسميات') }}
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('المسمى الوظيفي') }}</th>
                                    <th class="text-center">{{ __('العدد') }}</th>
                                    <th style="width: 140px;">{{ __('النسبة') }}</th>
                                    <th class="text-center">{{ __('إجمالي الرواتب') }}</th>
                                    <th class="text-center">{{ __('المتوسط') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($demographics['byJobTitle'] as $row)
                                    <tr>
                                        <td><strong>{{ $row->name }}</strong></td>
                                        <td class="text-center fw-bold">{{ $row->count }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress w-100" style="height: 6px;">
                                                    <div class="progress-bar bg-warning" style="width: {{ $row->percentage }}%"></div>
                                                </div>
                                                <small class="fw-semibold">{{ $row->percentage }}%</small>
                                            </div>
                                        </td>
                                        <td class="text-center font-monospace">{{ number_format($row->total_salary, 2) }}</td>
                                        <td class="text-center font-monospace text-muted">{{ number_format($row->avg_salary, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-3 text-muted">{{ __('لا توجد بيانات') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Data Table Card for Master & Expiring Docs -->
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
                        @if($reportType === 'expiring_docs')
                            {{-- Expiring Documents Rows --}}
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
                                    @if(!empty($selectedColumns['document_type']))
                                        <td class="text-center">
                                            <span class="fw-bold text-dark">{{ $row->document_type }}</span>
                                        </td>
                                    @endif
                                    @if(!empty($selectedColumns['document_number']))
                                        <td class="text-center font-monospace">{{ $row->document_number }}</td>
                                    @endif
                                    @if(!empty($selectedColumns['issue_date']))
                                        <td class="text-center font-monospace small">{{ $row->issue_date }}</td>
                                    @endif
                                    @if(!empty($selectedColumns['expiry_date']))
                                        <td class="text-center font-monospace fw-bold">{{ $row->expiry_date }}</td>
                                    @endif
                                    @if(!empty($selectedColumns['days_remaining']))
                                        <td class="text-center">
                                            @if($row->days_remaining < 0)
                                                <span class="badge bg-label-danger fw-bold">{{ __('منتهية منذ') }} {{ abs($row->days_remaining) }} {{ __('يوم') }}</span>
                                            @elseif($row->days_remaining <= 30)
                                                <span class="badge bg-label-danger fw-bold">{{ $row->days_remaining }} {{ __('يوم متبقي') }}</span>
                                            @elseif($row->days_remaining <= 60)
                                                <span class="badge bg-label-warning fw-bold">{{ $row->days_remaining }} {{ __('يوم متبقي') }}</span>
                                            @else
                                                <span class="badge bg-label-success fw-bold">{{ $row->days_remaining }} {{ __('يوم متبقي') }}</span>
                                            @endif
                                        </td>
                                    @endif
                                    @if(!empty($selectedColumns['status']))
                                        <td class="text-center">
                                            <span class="badge {{ $row->badge_class }}">{{ $row->status_label }}</span>
                                        </td>
                                    @endif
                                    @if(!empty($selectedColumns['action']))
                                        <td class="text-center">
                                            @if(!empty($row->file_url))
                                                <a href="{{ $row->file_url }}" target="_blank" class="btn btn-icon btn-sm btn-label-primary" title="{{ __('معاينة الملف') }}">
                                                    <i class="bx bx-file"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="20" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <div class="avatar avatar-xl bg-label-secondary rounded-circle mb-3 p-3">
                                                <i class="bx bx-check-double bx-lg text-success"></i>
                                            </div>
                                            <h6 class="fw-bold mb-1 text-dark">{{ __('لا توجد وثائق منتهية أو قريبة من الانتهاء') }}</h6>
                                            <p class="text-muted small mb-0">{{ __('كافة وثائق ومستندات الموظفين سارية وتعمل بانتظام.') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                        @else
                            {{-- Employee Master List Rows --}}
                            @forelse($records as $index => $row)
                                <tr>
                                    @if(!empty($selectedColumns['index']))
                                        <td class="text-center text-muted small">
                                            {{ method_exists($records, 'firstItem') ? ($records->firstItem() + $index) : ($index + 1) }}
                                        </td>
                                    @endif
                                    @if(!empty($selectedColumns['code']))
                                        <td><span class="badge bg-label-secondary font-monospace">{{ $row->employee_code ?? '-' }}</span></td>
                                    @endif
                                    @if(!empty($selectedColumns['employee']))
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $row->name ?? '-' }}</div>
                                            <small class="text-muted">{{ $row->gender?->label() }}</small>
                                        </td>
                                    @endif
                                    @if(!empty($selectedColumns['department']))
                                        <td>{{ $row->department->name ?? '-' }}</td>
                                    @endif
                                    @if(!empty($selectedColumns['branch']))
                                        <td>{{ $row->primaryBranch->name ?? '-' }}</td>
                                    @endif
                                    @if(!empty($selectedColumns['job_title']))
                                        <td>{{ $row->jobTitle->name ?? '-' }}</td>
                                    @endif
                                    @if(!empty($selectedColumns['nationality']))
                                        <td class="text-center">{{ $row->nationality ?: '-' }}</td>
                                    @endif
                                    @if(!empty($selectedColumns['national_id']))
                                        <td class="text-center font-monospace">{{ $row->national_id ?: '-' }}</td>
                                    @endif
                                    @if(!empty($selectedColumns['phone']))
                                        <td class="text-center font-monospace" dir="ltr">{{ $row->phone ?: '-' }}</td>
                                    @endif
                                    @if(!empty($selectedColumns['email']))
                                        <td><small class="text-muted">{{ $row->email ?: '-' }}</small></td>
                                    @endif
                                    @if(!empty($selectedColumns['employment_type']))
                                        <td class="text-center">{{ $row->employment_type?->label() ?? '-' }}</td>
                                    @endif
                                    @if(!empty($selectedColumns['hire_date']))
                                        <td class="text-center font-monospace">{{ $row->hire_date ? \Carbon\Carbon::parse($row->hire_date)->format('Y-m-d') : '-' }}</td>
                                    @endif
                                    @if(!empty($selectedColumns['basic_salary']))
                                        <td class="text-center font-monospace fw-bold">{{ number_format($row->basic_salary ?? 0, 2) }}</td>
                                    @endif
                                        @if(!empty($selectedColumns['status']))
                                            <td class="text-center">
                                                @if($row->status)
                                                    <span class="badge {{ $row->status->badgeColor() }}">{{ $row->status->label() }}</span>
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
                                                <i class="bx bx-user-x bx-lg"></i>
                                            </div>
                                            <h6 class="fw-bold mb-1 text-dark">{{ __('لا توجد سجلات موظفين تطابق شروط الفلترة') }}</h6>
                                            <p class="text-muted small mb-0">{{ __('يرجى تغيير معايير البحث والفلترة وإعادة المحاولة.') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>

            @if(method_exists($records, 'hasPages') && $records->hasPages())
                <div class="card-footer bg-transparent border-top py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <small class="text-muted">
                            {{ __('عرض السجلات من :from إلى :to من إجمالي :total', [
                                'from'  => $records->firstItem() ?? 0,
                                'to'    => $records->lastItem() ?? 0,
                                'total' => $records->total(),
                            ]) }}
                        </small>
                        {{ $records->links() }}
                    </div>
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
