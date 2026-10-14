@extends('layouts/contentNavbarLayout')

@section('title', __('Onboarding & Employee Conversion') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-compass text-primary font-24"></i>
            <span>{{ __('Employee Onboarding & Hired Journey') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.job-openings.index') }}" class="text-muted">{{ __('Recruitment') }}</a></li>
                <li class="breadcrumb-item active text-primary">{{ __('Onboarding') }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="{{ route('hr.job-offers.index') }}" class="btn btn-outline-primary d-flex align-items-center gap-1 shadow-sm px-3 py-2 rounded-3">
            <i class="bx bx-gift font-18"></i>
            <span class="fw-semibold">{{ __('Job Offers') }}</span>
        </a>

        <a href="{{ route('hr.employees.index') }}" class="btn btn-outline-success d-flex align-items-center gap-1 shadow-sm px-3 py-2 rounded-3">
            <i class="bx bx-user font-18"></i>
            <span class="fw-semibold">{{ __('Employees Directory') }}</span>
        </a>

        <a href="{{ route('hr.onboardings.create') }}" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-3 py-2 rounded-3">
            <i class="icon-base bx bx-plus font-18"></i>
            <span class="fw-semibold">{{ __('Initiate Onboarding') }}</span>
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm rounded-3" role="alert">
        <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Quick Metrics KPI Row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 rounded-3" style="border: 1px solid #edf0f6 !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1 fw-medium">{{ __('Total Journeys') }}</span>
                    <h4 class="fw-bold mb-0 text-primary">{{ $kpis['total'] }}</h4>
                </div>
                <div class="avatar avatar-md bg-label-primary rounded-3 p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-compass font-20"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 rounded-3" style="border: 1px solid #edf0f6 !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1 fw-medium">{{ __('In Progress') }}</span>
                    <h4 class="fw-bold mb-0 text-info">{{ $kpis['in_progress'] }}</h4>
                </div>
                <div class="avatar avatar-md bg-label-info rounded-3 p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-loader-circle font-20"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 rounded-3" style="border: 1px solid #edf0f6 !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1 fw-medium">{{ __('Completed') }}</span>
                    <h4 class="fw-bold mb-0 text-success">{{ $kpis['completed'] }}</h4>
                </div>
                <div class="avatar avatar-md bg-label-success rounded-3 p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-check-double font-20"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 rounded-3" style="border: 1px solid #edf0f6 !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1 fw-medium">{{ __('Converted to Staff') }}</span>
                    <h4 class="fw-bold mb-0 text-success">{{ $kpis['converted'] }}</h4>
                </div>
                <div class="avatar avatar-md bg-label-success rounded-3 p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-user-check font-20"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="card border-0 shadow-sm mb-4 rounded-3" style="border: 1px solid #edf0f6 !important;">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('hr.onboardings.index') }}" id="filterForm" class="row g-3 align-items-end">
            <div class="col-12 col-md-5">
                <label class="form-label fw-semibold text-heading small">{{ __('Filter by Department') }}</label>
                <select name="department_id" id="filter_department_id" class="select2 form-select" onchange="document.getElementById('filterForm').submit();">
                    <option value="">{{ __('-- All Departments --') }}</option>
                    @foreach ($departments as $dept)
                        @php
                            $deptName = $dept->translate(app()->getLocale())?->name ?? $dept->name;
                        @endphp
                        <option value="{{ $dept->id }}" {{ (string)$selectedDepartment === (string)$dept->id ? 'selected' : '' }}>
                            {{ $deptName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small">{{ __('Filter by Status') }}</label>
                <select name="status" id="filter_status" class="form-select" onchange="document.getElementById('filterForm').submit();">
                    <option value="">{{ __('-- All Statuses --') }}</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}" {{ $selectedStatus === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary px-3 rounded-3 w-100">
                    <i class="bx bx-filter-alt me-1"></i> {{ __('Filter') }}
                </button>
                @if ($selectedDepartment || $selectedStatus)
                    <a href="{{ route('hr.onboardings.index') }}" class="btn btn-outline-secondary px-3 rounded-3" title="{{ __('Reset All') }}">
                        <i class="bx bx-reset"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table View -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-datatable table-responsive">
        <table class="table border-top table-hover text-nowrap" id="onboardingsTable" style="width:100%">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">#</th>
                    <th>{{ __('Onboarding Code') }}</th>
                    <th>{{ __('Candidate') }}</th>
                    <th>{{ __('Target Position') }}</th>
                    <th>{{ __('Task Progress') }}</th>
                    <th>{{ __('Staff Conversion') }}</th>
                    <th class="text-center">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 110px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;

    // Initialize Select2
    if (typeof window.initSelect2 === 'function') {
        window.initSelect2();
    } else if ($.fn.select2) {
        $('.select2').select2({ width: '100%' });
    }

    // Initialize DataTables
    const dtTable = $('#onboardingsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('hr.onboardings.data') }}",
            data: function (d) {
                d.department_id = $('#filter_department_id').val();
                d.status        = $('#filter_status').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center align-middle' },
            { data: 'onboarding_code_info', name: 'onboarding_code', className: 'align-middle' },
            { data: 'candidate_info', name: 'candidate.full_name', className: 'align-middle' },
            { data: 'position_info', name: 'jobTitle.title', className: 'align-middle' },
            { data: 'progress_bar', name: 'progress_percentage', className: 'align-middle' },
            { data: 'conversion_status', name: 'employee_id', className: 'align-middle' },
            { data: 'status_badge', name: 'status', className: 'text-center align-middle' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
        ],
        order: [[1, 'desc']]
    });

    // Quick conversion redirect
    $(document).on('click', '.btn-convert-modal', function () {
        const id = $(this).data('id');
        window.location.href = "{{ url('hr/onboardings') }}/" + id + "#convert";
    });
});
</script>
@endsection
