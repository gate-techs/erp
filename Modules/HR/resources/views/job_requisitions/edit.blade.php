@extends('layouts/contentNavbarLayout')

@section('title', __('Edit Job Requisition') . ' - ' . config('variables.templateName'))

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
            <a href="{{ route('hr.job-requisitions.show', $requisition->id) }}" class="btn btn-icon btn-sm btn-outline-secondary rounded-circle" title="{{ __('Back') }}">
                <i class="icon-base bx bx-arrow-back font-16"></i>
            </a>
            <h4 class="fw-bold mb-0 d-flex align-items-center gap-2">
                <i class="icon-base bx bx-edit text-primary font-24"></i>
                <span>{{ __('Edit Job Requisition') }}: {{ $requisition->requisition_number }}</span>
            </h4>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('hr.job-requisitions.index') }}" class="text-muted">{{ __('Job Requisitions') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.job-requisitions.show', $requisition->id) }}" class="text-muted">{{ $requisition->requisition_number }}</a></li>
                <li class="breadcrumb-item active text-primary">{{ __('Edit') }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('hr.job-requisitions.show', $requisition->id) }}" class="btn btn-outline-secondary px-3 py-2 rounded-3">
            {{ __('Cancel') }}
        </a>
        <button type="submit" form="editRequisitionForm" class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 shadow-sm rounded-3">
            <i class="icon-base bx bx-save font-18"></i>
            <span>{{ __('Update Requisition') }}</span>
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

<form id="editRequisitionForm" action="{{ route('hr.job-requisitions.update', $requisition->id) }}" method="POST">
    @csrf
    @method('PUT')

    <!-- Card 1: Link with Approved Workforce Plan -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                <i class="icon-base bx bx-link font-20"></i>
                <span>{{ __('Workforce Plan Integration') }}</span>
            </h5>
        </div>
        <div class="card-body pt-4">
            <div class="row g-3">
                <div class="col-12 col-md-8">
                    <label class="form-label fw-semibold text-heading small">{{ __('Select Approved Plan Position (Optional)') }}</label>
                    <select name="workforce_plan_item_id" id="workforce_plan_item_id" class="form-select @error('workforce_plan_item_id') is-invalid @enderror">
                        <option value="">{{ __('-- Outside Plan / Emergency / Unbudgeted --') }}</option>
                        @foreach ($planItems as $pItem)
                            @php
                                $roleName = $pItem->jobTitle?->translate(app()->getLocale())?->name ?? $pItem->jobTitle?->name ?? '-';
                                $deptName = $pItem->department?->translate(app()->getLocale())?->name ?? $pItem->department?->name ?? '-';
                                $planCode = $pItem->plan?->plan_code ?? '';
                                $isSelected = old('workforce_plan_item_id', $requisition->workforce_plan_item_id) == $pItem->id;
                            @endphp
                            <option value="{{ $pItem->id }}"
                                data-department="{{ $pItem->department_id }}"
                                data-job="{{ $pItem->job_title_id }}"
                                data-branch="{{ $pItem->branch_id }}"
                                data-employment="{{ $pItem->employment_type?->value }}"
                                data-salary="{{ $pItem->estimated_monthly_salary }}"
                                data-vacancies="{{ $pItem->vacancies_count }}"
                                {{ $isSelected ? 'selected' : '' }}>
                                [{{ $planCode }}] {{ $roleName }} — {{ $deptName }} ({{ $pItem->vacancies_count }} {{ __('Vacancies Available') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4 d-flex align-items-center pt-md-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_budgeted" id="is_budgeted" value="1" {{ old('is_budgeted', $requisition->is_budgeted) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-heading small" for="is_budgeted">{{ __('Budgeted Position') }}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Position & Department Details -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                <i class="icon-base bx bx-buildings font-20"></i>
                <span>{{ __('Position & Department Details') }}</span>
            </h5>
        </div>
        <div class="card-body pt-4">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small required">{{ __('Department') }}</label>
                    <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                        <option value="">{{ __('Select Department') }}</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $requisition->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->translate(app()->getLocale())?->name ?? $dept->translate('ar')?->name ?? $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small required">{{ __('Job Title') }}</label>
                    <select name="job_title_id" id="job_title_id" class="form-select @error('job_title_id') is-invalid @enderror" required>
                        <option value="">{{ __('Select Job Title') }}</option>
                        @foreach ($jobTitles as $job)
                            <option value="{{ $job->id }}" {{ old('job_title_id', $requisition->job_title_id) == $job->id ? 'selected' : '' }}>
                                {{ $job->translate(app()->getLocale())?->name ?? $job->translate('ar')?->name ?? $job->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('job_title_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small">{{ __('Branch') }}</label>
                    <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
                        <option value="">{{ __('All Branches / Headquarter') }}</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $requisition->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->translate(app()->getLocale())?->name ?? $branch->translate('ar')?->name ?? $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-heading small required">{{ __('Employment Type') }}</label>
                    <select name="employment_type" id="employment_type" class="form-select @error('employment_type') is-invalid @enderror" required>
                        @foreach ($empTypes as $type)
                            <option value="{{ $type->value }}" {{ old('employment_type', $requisition->employment_type?->value) == $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                    @error('employment_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-heading small required">{{ __('Requisition Type') }}</label>
                    <select name="requisition_type" id="requisition_type" class="form-select @error('requisition_type') is-invalid @enderror" required>
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}" {{ old('requisition_type', $requisition->requisition_type?->value) == $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                    @error('requisition_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-3" id="replacement-employee-wrapper" style="display: none;">
                    <label class="form-label fw-semibold text-heading small">{{ __('Replacing Employee') }}</label>
                    <select name="replacement_employee_id" id="replacement_employee_id" class="form-select @error('replacement_employee_id') is-invalid @enderror">
                        <option value="">{{ __('Select Employee') }}</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('replacement_employee_id', $requisition->replacement_employee_id) == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }} ({{ $emp->employee_code }})
                            </option>
                        @endforeach
                    </select>
                    @error('replacement_employee_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-heading small required">{{ __('Priority') }}</label>
                    <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror" required>
                        @foreach ($priorities as $p)
                            <option value="{{ $p->value }}" {{ old('priority', $requisition->priority?->value) == $p->value ? 'selected' : '' }}>{{ $p->label() }}</option>
                        @endforeach
                    </select>
                    @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-heading small required">{{ __('Headcount Needed') }}</label>
                    <input type="number" name="headcount_needed" id="headcount_needed" class="form-control @error('headcount_needed') is-invalid @enderror" min="1" value="{{ old('headcount_needed', $requisition->headcount_needed) }}" required>
                    @error('headcount_needed')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-heading small">{{ __('Expected Start Date') }}</label>
                    <input type="date" name="expected_start_date" class="form-control @error('expected_start_date') is-invalid @enderror" value="{{ old('expected_start_date', $requisition->expected_start_date?->format('Y-m-d')) }}">
                    @error('expected_start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Compensation & Budget -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                <i class="icon-base bx bx-wallet font-20"></i>
                <span>{{ __('Target Compensation Range') }}</span>
            </h5>
        </div>
        <div class="card-body pt-4">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small">{{ __('Minimum Salary (KWD)') }}</label>
                    <input type="number" step="0.001" name="min_salary" id="min_salary" class="form-control @error('min_salary') is-invalid @enderror" value="{{ old('min_salary', $requisition->min_salary) }}" placeholder="0.000" min="0">
                    @error('min_salary')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-heading small">{{ __('Maximum Salary (KWD)') }}</label>
                    <input type="number" step="0.001" name="max_salary" id="max_salary" class="form-control @error('max_salary') is-invalid @enderror" value="{{ old('max_salary', $requisition->max_salary) }}" placeholder="0.000" min="0">
                    @error('max_salary')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Justification & Job Specification -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-primary">
                <i class="icon-base bx bx-detail font-20"></i>
                <span>{{ __('Business Justification & Role Specifications') }}</span>
            </h5>
        </div>
        <div class="card-body pt-4">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold text-heading small">{{ __('Reason / Business Justification') }}</label>
                    <textarea name="justification" class="form-control @error('justification') is-invalid @enderror" rows="2">{{ old('justification', $requisition->justification) }}</textarea>
                    @error('justification')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small">{{ __('Job Duties & Responsibilities') }}</label>
                    <textarea name="job_description" class="form-control @error('job_description') is-invalid @enderror" rows="3">{{ old('job_description', $requisition->job_description) }}</textarea>
                    @error('job_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-heading small">{{ __('Required Qualifications & Skills') }}</label>
                    <textarea name="requirements" class="form-control @error('requirements') is-invalid @enderror" rows="3">{{ old('requirements', $requisition->requirements) }}</textarea>
                    @error('requirements')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('page-script')
<script>
$(function() {
    function toggleReplacement() {
        const val = $('#requisition_type').val();
        if (val === 'replacement') {
            $('#replacement-employee-wrapper').fadeIn(200);
        } else {
            $('#replacement-employee-wrapper').fadeOut(200);
            $('#replacement_employee_id').val('');
        }
    }

    $('#requisition_type').on('change', toggleReplacement);
    toggleReplacement();

    $('#workforce_plan_item_id').on('change', function() {
        const $selected = $(this).find(':selected');
        const deptId = $selected.data('department');
        const jobId = $selected.data('job');
        const branchId = $selected.data('branch');
        const empType = $selected.data('employment');
        const salary = $selected.data('salary');

        if ($(this).val()) {
            $('#is_budgeted').prop('checked', true);
            if (deptId) $('#department_id').val(deptId).trigger('change');
            if (jobId) $('#job_title_id').val(jobId).trigger('change');
            if (branchId) $('#branch_id').val(branchId).trigger('change');
            if (empType) $('#employment_type').val(empType).trigger('change');
            if (salary) {
                $('#max_salary').val(salary);
            }
        }
    });
});
</script>
@endsection
