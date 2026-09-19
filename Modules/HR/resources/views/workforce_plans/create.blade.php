@extends('layouts/contentNavbarLayout')

@section('title', __('Create Workforce Plan') . ' - ' . config('variables.templateName'))

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/select2/select2.scss'
    ])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/select2/select2.js'
    ])
@endsection

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="{{ route('hr.workforce-plans.index') }}" class="btn btn-icon btn-sm btn-outline-secondary rounded-circle" title="{{ __('Back') }}">
                <i class="icon-base bx bx-arrow-back font-16"></i>
            </a>
            <h4 class="fw-bold mb-0 d-flex align-items-center gap-2">
                <i class="icon-base bx bx-line-chart text-primary font-24"></i>
                <span>{{ __('Create Workforce Plan') }}</span>
            </h4>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('hr.workforce-plans.index') }}" class="text-muted">{{ __('Workforce Plans') }}</a></li>
                <li class="breadcrumb-item active text-primary">{{ __('Create Plan') }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('hr.workforce-plans.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3">
            {{ __('Cancel') }}
        </a>
        <button type="submit" form="createPlanForm" class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 shadow-sm rounded-3">
            <i class="icon-base bx bx-save font-18"></i>
            <span>{{ __('Save Plan') }}</span>
        </button>
    </div>
</div>

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-start gap-2">
            <i class="icon-base bx bx-error-circle font-22 flex-shrink-0 mt-1"></i>
            <div>
                <strong class="d-block mb-1">{{ __('Please correct the following errors:') }}</strong>
                <ul class="mb-0 ps-3 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form id="createPlanForm" action="{{ route('hr.workforce-plans.store') }}" method="POST">
    @csrf

    <!-- Card 1: Plan Basic Information -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                <i class="icon-base bx bx-info-circle font-20"></i>
                <span>{{ __('General Plan Information') }}</span>
            </h5>
        </div>
        <div class="card-body pt-4">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small required">{{ __('Plan Title') }}</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="{{ __('e.g. Annual Workforce Plan 2026') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold text-heading small required">{{ __('Year') }}</label>
                    <input type="number" name="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', now()->year) }}" min="2020" max="2050" required>
                    @error('year')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold text-heading small required">{{ __('Start Date') }}</label>
                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', now()->startOfYear()->format('Y-m-d')) }}" required>
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold text-heading small required">{{ __('End Date') }}</label>
                    <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', now()->endOfYear()->format('Y-m-d')) }}" required>
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-heading small">{{ __('Plan Description & Objectives') }}</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2" placeholder="{{ __('Provide strategic goals, growth assumptions, or guidelines for this plan...') }}">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Planned Positions / Items -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-transparent border-bottom py-3 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                <i class="icon-base bx bx-group font-20"></i>
                <span>{{ __('Planned Positions & Headcount Details') }}</span>
            </h5>
            <button type="button" id="btn-add-item" onclick="window.addPlanPositionRow && window.addPlanPositionRow()" class="btn btn-sm btn-primary d-flex align-items-center gap-1 shadow-sm">
                <i class="icon-base bx bx-plus font-16"></i>
                <span>{{ __('Add Position to Plan') }}</span>
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0" id="plan-items-table" style="min-width: 1400px;">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 220px;">{{ __('Department') }} <span class="text-danger">*</span></th>
                            <th style="min-width: 220px;">{{ __('Job Title') }} <span class="text-danger">*</span></th>
                            <th style="min-width: 180px;">{{ __('Branch') }}</th>
                            <th style="min-width: 150px;">{{ __('Type') }}</th>
                            <th style="min-width: 140px;">{{ __('Quarter') }}</th>
                            <th style="min-width: 140px;">{{ __('Priority') }}</th>
                            <th style="min-width: 90px;" class="text-center">{{ __('Current') }}</th>
                            <th style="min-width: 100px;" class="text-center">{{ __('Target') }} <span class="text-danger">*</span></th>
                            <th style="min-width: 90px;" class="text-center">{{ __('Vacancies') }}</th>
                            <th style="min-width: 160px;">{{ __('Est. Salary (KWD)') }}</th>
                            <th style="min-width: 60px;" class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="items-container">
                        <!-- Items rendered via JS / Blade -->
                    </tbody>
                </table>
            </div>

            <!-- Table Footer Totals Bar -->
            <div class="p-3 bg-light border-top d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-4">
                    <div>
                        <span class="text-muted small">{{ __('Total Planned Positions:') }}</span>
                        <strong class="fs-6 text-primary ms-1" id="summary-total-positions">0</strong>
                    </div>
                    <div>
                        <span class="text-muted small">{{ __('Total Vacancies:') }}</span>
                        <strong class="fs-6 text-warning ms-1" id="summary-total-vacancies">0</strong>
                    </div>
                </div>
                <div>
                    <span class="text-muted small">{{ __('Estimated Annual Cost:') }}</span>
                    <strong class="fs-6 text-success ms-1 font-monospace" id="summary-total-budget">0.000 KWD</strong>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Template Row for Dynamic Position Items -->
<template id="item-row-template">
    <tr class="plan-item-row">
        <td>
            <select name="items[INDEX][department_id]" class="form-select form-select-sm item-dept" required>
                <option value="">{{ __('Select Department') }}</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->translate(app()->getLocale())?->name ?? $dept->translate('ar')?->name ?? $dept->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select name="items[INDEX][job_title_id]" class="form-select form-select-sm item-job" required>
                <option value="">{{ __('Select Job Title') }}</option>
                @foreach ($jobTitles as $job)
                    <option value="{{ $job->id }}">{{ $job->translate(app()->getLocale())?->name ?? $job->translate('ar')?->name ?? $job->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select name="items[INDEX][branch_id]" class="form-select form-select-sm item-branch">
                <option value="">{{ __('All Branches') }}</option>
                @foreach ($branches as $br)
                    <option value="{{ $br->id }}">{{ $br->translate(app()->getLocale())?->name ?? $br->translate('ar')?->name ?? $br->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select name="items[INDEX][employment_type]" class="form-select form-select-sm">
                @foreach ($empTypes as $type)
                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select name="items[INDEX][target_quarter]" class="form-select form-select-sm">
                @foreach ($quarters as $q)
                    <option value="{{ $q->value }}">{{ $q->label() }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select name="items[INDEX][priority]" class="form-select form-select-sm">
                @foreach ($priorities as $pr)
                    <option value="{{ $pr->value }}" {{ $pr === \Modules\HR\Enums\PlanItemPriority::MEDIUM ? 'selected' : '' }}>{{ $pr->label() }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="items[INDEX][current_headcount]" class="form-control form-control-sm text-center item-current bg-light" value="0" readonly>
        </td>
        <td>
            <input type="number" name="items[INDEX][target_headcount]" class="form-control form-control-sm text-center item-target" min="1" value="1" required>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm text-center item-vacancies bg-light" value="1" readonly>
        </td>
        <td>
            <input type="number" step="0.001" name="items[INDEX][estimated_monthly_salary]" class="form-control form-control-sm item-salary" placeholder="0.000" min="0">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-remove-row" title="{{ __('Remove') }}">
                <i class="bx bx-trash font-16"></i>
            </button>
        </td>
    </tr>
</template>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const $ = window.jQuery || window.$;
    let rowIndex = 0;

    function getTemplateHtml() {
        const tpl = document.getElementById('item-row-template');
        if (!tpl) return '';
        if (tpl.innerHTML && tpl.innerHTML.trim().length > 0) {
            return tpl.innerHTML;
        }
        if (tpl.content) {
            const tempDiv = document.createElement('div');
            tempDiv.appendChild(tpl.content.cloneNode(true));
            return tempDiv.innerHTML;
        }
        return $(tpl).html() || '';
    }

    window.addPlanPositionRow = function(data = null) {
        const rawTemplate = getTemplateHtml();
        if (!rawTemplate) {
            console.error('Template #item-row-template not found or empty.');
            return;
        }
        const html = rawTemplate.replace(/INDEX/g, rowIndex);
        const parsed = $.parseHTML($.trim(html));
        const $row = $(parsed);
        $('#items-container').append($row);

        if (data) {
            $row.find('.item-dept').val(data.department_id);
            $row.find('.item-job').val(data.job_title_id);
            $row.find('.item-branch').val(data.branch_id || '');
            $row.find('[name="items[' + rowIndex + '][employment_type]"]').val(data.employment_type);
            $row.find('[name="items[' + rowIndex + '][target_quarter]"]').val(data.target_quarter);
            $row.find('[name="items[' + rowIndex + '][priority]"]').val(data.priority);
            $row.find('.item-current').val(data.current_headcount || 0);
            $row.find('.item-target').val(data.target_headcount || 1);
            $row.find('.item-salary').val(data.estimated_monthly_salary || '');
        }

        rowIndex++;
        calculateRow($row);
        calculateSummary();
    };

    // Add first row by default if none exists
    if ($('#items-container tr.plan-item-row').length === 0) {
        window.addPlanPositionRow();
    }

    $(document).on('click', '#btn-add-item', function(e) {
        e.preventDefault();
        window.addPlanPositionRow();
    });

    $(document).on('click', '.btn-remove-row', function() {
        if ($('#items-container tr.plan-item-row').length <= 1) {
            if (typeof Swal !== 'undefined') {
                Swal.fire('{{ __("Warning") }}', '{{ __("A workforce plan must have at least one position.") }}', 'warning');
            } else {
                alert('{{ __("A workforce plan must have at least one position.") }}');
            }
            return;
        }
        $(this).closest('tr.plan-item-row').remove();
        calculateSummary();
    });

    // Auto fetch live headcount when dept or job title changes
    $(document).on('change', '.item-dept, .item-job, .item-branch', function() {
        const $row = $(this).closest('tr');
        const deptId = $row.find('.item-dept').val();
        const jobId = $row.find('.item-job').val();
        const branchId = $row.find('.item-branch').val();

        if (deptId && jobId) {
            $.ajax({
                url: "{{ route('hr.workforce-plans.live-headcount') }}",
                type: 'GET',
                data: {
                    department_id: deptId,
                    job_title_id: jobId,
                    branch_id: branchId
                },
                success: function(res) {
                    $row.find('.item-current').val(res.current_headcount || 0);
                    calculateRow($row);
                    calculateSummary();
                }
            });
        }
    });

    $(document).on('input', '.item-target, .item-current, .item-salary', function() {
        const $row = $(this).closest('tr');
        calculateRow($row);
        calculateSummary();
    });

    function calculateRow($row) {
        const current = parseInt($row.find('.item-current').val()) || 0;
        const target = parseInt($row.find('.item-target').val()) || 0;
        const vacancies = Math.max(0, target - current);
        $row.find('.item-vacancies').val(vacancies);
    }

    function calculateSummary() {
        let totalPositions = 0;
        let totalVacancies = 0;
        let totalAnnualCost = 0;

        $('#items-container tr.plan-item-row').each(function() {
            const target = parseInt($(this).find('.item-target').val()) || 0;
            const current = parseInt($(this).find('.item-current').val()) || 0;
            const vacancies = Math.max(0, target - current);
            const salary = parseFloat($(this).find('.item-salary').val()) || 0;

            totalPositions += target;
            totalVacancies += vacancies;
            totalAnnualCost += (vacancies * salary * 12);
        });

        $('#summary-total-positions').text(totalPositions);
        $('#summary-total-vacancies').text(totalVacancies);
        $('#summary-total-budget').text(totalAnnualCost.toFixed(3) + ' KWD');
    }
});
</script>
@endsection
