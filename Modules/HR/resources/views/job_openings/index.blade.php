@extends('layouts/contentNavbarLayout')

@section('title', __('Job Openings') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-broadcast text-primary font-24"></i>
            <span>{{ __('Job Openings') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Human Resources') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Recruitment') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Job Openings') }}</li>
            </ol>
        </nav>
    </div>

    <div>
        <a href="{{ route('hr.job-openings.create') }}" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('New Job Opening') }}</span>
        </a>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Total Openings') }}</span>
                    <h3 class="fw-bold mb-0 text-primary">{{ number_format($kpis['total'] ?? 0) }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-briefcase font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Active / Published') }}</span>
                    <h3 class="fw-bold mb-0 text-success">{{ $kpis['active'] ?? 0 }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-success rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-check-circle font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Open Positions Target') }}</span>
                    <h3 class="fw-bold mb-0 text-info">{{ $kpis['open_positions'] ?? 0 }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-info rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-group font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Closed / Archived') }}</span>
                    <h3 class="fw-bold mb-0 text-secondary">{{ $kpis['closed'] ?? 0 }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-secondary rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-archive font-22"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Branch Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                <select id="filter_branch_id" class="select2 form-select" data-placeholder="{{ __('All Branches') }}" data-allow-clear="true">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->translate(app()->getLocale())?->name ?? $branch->translate('ar')?->name ?? $branch->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Department Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Department') }}</label>
                <select id="filter_department_id" class="select2 form-select" data-placeholder="{{ __('All Departments') }}" data-allow-clear="true">
                    <option value="">{{ __('All Departments') }}</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->translate(app()->getLocale())?->name ?? $dept->translate('ar')?->name ?? $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Status Filter -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                <select id="filter_status" class="select2 form-select" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Workplace Type Filter -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Workplace Type') }}</label>
                <select id="filter_workplace_type" class="select2 form-select" data-placeholder="{{ __('All Types') }}" data-allow-clear="true">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach ($workplaceTypes as $wt)
                        <option value="{{ $wt->value }}">{{ $wt->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 5. Reset Filters Action Button -->
            <div class="col-12 col-sm-6 col-lg-2 d-flex align-items-end">
                <button type="button" id="btnResetFilters" class="btn btn-label-secondary btn-sm w-100 d-flex align-items-center justify-content-center gap-1 shadow-xs" style="height: 38px;">
                    <i class="icon-base bx bx-reset font-18"></i>
                    <span class="fw-semibold">{{ __('Reset Filters') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Job Openings DataTable Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="jobOpeningsTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Opening Info') }}</th>
                    <th class="text-center">{{ __('Workplace') }}</th>
                    <th class="text-center">{{ __('Posting Type') }}</th>
                    <th class="text-center">{{ __('Positions (Target / Filled)') }}</th>
                    <th>{{ __('Salary Range') }}</th>
                    <th>{{ __('Dates') }}</th>
                    <th class="text-center">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 110px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loaded via Server-Side DataTables -->
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;
    const dataUrl = "{{ route('hr.job-openings.data') }}";
    const baseUrl = "{{ url('hr/job-openings') }}";

    // 0. Select2 Initialization
    if (typeof window.initSelect2 === 'function') {
        window.initSelect2();
    } else if ($.fn.select2) {
        $('.select2').each(function () {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    placeholder: $(this).data('placeholder') || '',
                    allowClear: $(this).data('allow-clear') !== false,
                    width: '100%',
                    dropdownParent: $(this).closest('.offcanvas').length ? $(this).closest('.offcanvas') : $(document.body)
                });
            }
        });
    }

    // 1. Initialize Server-Side DataTable
    const table = $('#jobOpeningsTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pagingType: 'simple_numbers',
        ajax: {
            url: dataUrl,
            data: function(d) {
                d.branch_id       = $('#filter_branch_id').val();
                d.department_id   = $('#filter_department_id').val();
                d.status          = $('#filter_status').val();
                d.workplace_type  = $('#filter_workplace_type').val();
            }
        },
        columns: [
            { 
                data: 'DT_RowIndex', 
                name: 'DT_RowIndex', 
                orderable: false, 
                searchable: false, 
                className: 'text-center align-middle',
                render: (data, type, row, meta) => `<strong class="text-muted">#${data || (meta.row + 1 + (meta.settings._iDisplayStart || 0))}</strong>`
            },
            { data: 'opening_info', name: 'opening_code', className: 'align-middle' },
            { data: 'workplace_badge', name: 'workplace_type', className: 'text-center align-middle' },
            { data: 'posting_badge', name: 'posting_type', className: 'text-center align-middle' },
            { data: 'positions', name: 'open_positions', className: 'text-center align-middle' },
            { data: 'salary_range', name: 'min_salary', searchable: false, className: 'align-middle' },
            { data: 'dates', name: 'publish_date', className: 'align-middle' },
            { data: 'status_badge', name: 'status', className: 'text-center align-middle' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
        ],
        order: [[1, 'desc']],
        drawCallback: function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('#jobOpeningsTable [data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });

    // 2. Custom Filters Trigger Reload
    let isResetting = false;

    $('#filter_branch_id, #filter_department_id, #filter_status, #filter_workplace_type').on('change', function () {
        if (isResetting) return;
        table.ajax.reload();
    });

    // 3. Reset Filters
    $('#btnResetFilters').on('click', function () {
        isResetting = true;
        $('#filter_branch_id, #filter_department_id, #filter_status, #filter_workplace_type').val('').trigger('change');
        isResetting = false;
        table.ajax.reload();
    });

    // 4. Publish Action
    $(document).on('click', '.btn-publish-opening', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        const url = `${baseUrl}/${id}/publish`;

        const confirmFn = window.Swal ? Swal.fire({
            title: "{{ __('Publish Job Opening?') }}",
            text: "{{ __('The job opening will be made active and open for candidate applications.') }}",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#7367f0',
            cancelButtonColor: '#8592a3',
            confirmButtonText: "{{ __('Yes, Publish!') }}",
            cancelButtonText: "{{ __('Cancel') }}"
        }) : Promise.resolve({ isConfirmed: confirm("{{ __('Publish this job opening?') }}") });

        confirmFn.then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = `@csrf`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // 5. Close Action
    $(document).on('click', '.btn-close-opening', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        const url = `${baseUrl}/${id}/close`;

        const confirmFn = window.Swal ? Swal.fire({
            title: "{{ __('Close Job Opening?') }}",
            text: "{{ __('The job opening will be closed and will stop accepting candidate applications.') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff9f43',
            cancelButtonColor: '#8592a3',
            confirmButtonText: "{{ __('Yes, Close!') }}",
            cancelButtonText: "{{ __('Cancel') }}"
        }) : Promise.resolve({ isConfirmed: confirm("{{ __('Close this job opening?') }}") });

        confirmFn.then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = `@csrf`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // 6. Delete Action
    $(document).on('click', '.btn-delete-opening', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        const openingCode = $(this).data('num');
        const url = `${baseUrl}/${id}`;

        const confirmFn = window.Swal ? Swal.fire({
            title: "{{ __('Delete Job Opening?') }}",
            text: `${openingCode}: {{ __('Are you sure you want to delete this job opening? This action cannot be undone.') }}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ea5455',
            cancelButtonColor: '#8592a3',
            confirmButtonText: "{{ __('Yes, delete!') }}",
            cancelButtonText: "{{ __('Cancel') }}"
        }) : Promise.resolve({ isConfirmed: confirm("{{ __('Are you sure you want to delete this job opening?') }}") });

        confirmFn.then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
</script>
@endsection
