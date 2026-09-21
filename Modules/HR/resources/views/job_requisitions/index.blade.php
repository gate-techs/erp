@extends('layouts/contentNavbarLayout')

@section('title', __('Job Requisitions') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-briefcase text-primary font-24"></i>
            <span>{{ __('Job Requisitions') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Human Resources') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Job Requisitions') }}</li>
            </ol>
        </nav>
    </div>

    <div>
        <a href="{{ route('hr.job-requisitions.create') }}" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('New Job Requisition') }}</span>
        </a>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Total Requisitions') }}</span>
                    <h3 class="fw-bold mb-0 text-primary">{{ number_format($kpis['total'] ?? 0) }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-file font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Pending Approval') }}</span>
                    <h3 class="fw-bold mb-0 text-warning">{{ $kpis['pending_approval'] ?? 0 }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-warning rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-hourglass font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('In Recruitment') }}</span>
                    <h3 class="fw-bold mb-0 text-info">{{ $kpis['in_recruitment'] ?? 0 }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-info rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-user-voice font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Fulfilled Requests') }}</span>
                    <h3 class="fw-bold mb-0 text-success">{{ $kpis['fulfilled'] ?? 0 }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-success rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-check-double font-22"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Standard Filters Card (Exact Match with hr/penalties layout) -->
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

            <!-- 4. Requisition Type Filter -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Requisition Type') }}</label>
                <select id="filter_type" class="select2 form-select" data-placeholder="{{ __('All Types') }}" data-allow-clear="true">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach ($types as $tp)
                        <option value="{{ $tp->value }}">{{ $tp->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 5. Priority Filter -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Priority') }}</label>
                <select id="filter_priority" class="select2 form-select" data-placeholder="{{ __('All Priorities') }}" data-allow-clear="true">
                    <option value="">{{ __('All Priorities') }}</option>
                    @foreach ($priorities as $p)
                        <option value="{{ $p->value }}">{{ $p->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 6. Budget Status Filter -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Budget Status') }}</label>
                <select id="filter_is_budgeted" class="select2 form-select" data-placeholder="{{ __('All') }}" data-allow-clear="true">
                    <option value="">{{ __('All') }}</option>
                    <option value="1">{{ __('Budgeted') }}</option>
                    <option value="0">{{ __('Unbudgeted') }}</option>
                </select>
            </div>

            <!-- 7. Reset Filters Action Button -->
            <div class="col-12 col-sm-6 col-lg-2 d-flex align-items-end">
                <button type="button" id="btnResetFilters" class="btn btn-label-secondary btn-sm w-100 d-flex align-items-center justify-content-center gap-1 shadow-xs" style="height: 38px;">
                    <i class="icon-base bx bx-reset font-18"></i>
                    <span class="fw-semibold">{{ __('Reset Filters') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Job Requisitions DataTable Card (Exact Match with hr/penalties layout) -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="jobRequisitionsTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Requisition Info') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th class="text-center">{{ __('Headcount') }}</th>
                    <th>{{ __('Salary Range') }}</th>
                    <th class="text-center">{{ __('Priority') }}</th>
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
    const dataUrl = "{{ route('hr.job-requisitions.data') }}";
    const baseUrl = "{{ url('hr/job-requisitions') }}";

    // 0. Select2 Initialization (Exact match with hr/penalties)
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

    // 1. Initialize Server-Side DataTable (Exact match with hr/penalties)
    const table = $('#jobRequisitionsTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pagingType: 'simple_numbers',
        ajax: {
            url: dataUrl,
            data: function(d) {
                d.branch_id        = $('#filter_branch_id').val();
                d.department_id    = $('#filter_department_id').val();
                d.status           = $('#filter_status').val();
                d.requisition_type = $('#filter_type').val();
                d.priority         = $('#filter_priority').val();
                d.is_budgeted      = $('#filter_is_budgeted').val();
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
            { data: 'requisition_info', name: 'requisition_number', className: 'align-middle' },
            { data: 'type_badge', name: 'requisition_type', className: 'align-middle' },
            { data: 'headcount', name: 'headcount_needed', className: 'text-center align-middle' },
            { data: 'salary_range', name: 'min_salary', searchable: false, className: 'align-middle' },
            { data: 'priority_badge', name: 'priority', className: 'text-center align-middle' },
            { data: 'status_badge', name: 'status', className: 'text-center align-middle' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
        ],
        order: [[1, 'desc']],
        drawCallback: function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('#jobRequisitionsTable [data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });

    // 2. Custom Filters Trigger Reload with debounce & reset guard (Exact match with hr/penalties)
    let isResetting = false;

    $('#filter_branch_id, #filter_department_id, #filter_status, #filter_type, #filter_priority, #filter_is_budgeted').on('change', function() {
        if (isResetting) return;
        table.ajax.reload();
    });

    // 3. Reset Filters Handler (Exact match with hr/penalties)
    $('#btnResetFilters').on('click', function() {
        isResetting = true;
        $('#filter_branch_id, #filter_department_id, #filter_status, #filter_type, #filter_priority, #filter_is_budgeted').val('').trigger('change');
        isResetting = false;
        table.ajax.reload();
    });

    // 4. Delete Requisition Action
    $(document).on('click', '.btn-delete-requisition', function() {
        const id = $(this).data('id');
        const num = $(this).data('num');

        const doDelete = function() {
            $.ajax({
                url: baseUrl + '/' + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    table.ajax.reload();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '{{ __("Deleted") }}',
                            text: res.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert(res.message);
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || '{{ __("Failed to delete requisition.") }}';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('{{ __("Error") }}', msg, 'error');
                    } else {
                        alert(msg);
                    }
                }
            });
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '{{ __("Are you sure?") }}',
                text: '{{ __("Do you really want to delete requisition:") }} ' + num,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#82868b',
                confirmButtonText: '{{ __("Yes, delete it!") }}',
                cancelButtonText: '{{ __("Cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) doDelete();
            });
        } else {
            if (confirm('{{ __("Are you sure you want to delete this requisition?") }}')) doDelete();
        }
    });
});
</script>
@endsection
