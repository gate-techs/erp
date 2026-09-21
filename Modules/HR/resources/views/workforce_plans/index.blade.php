@extends('layouts/contentNavbarLayout')

@section('title', __('Workforce Plans') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-line-chart text-primary font-24"></i>
            <span>{{ __('Workforce Plans') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Human Resources') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Workforce Plans') }}</li>
            </ol>
        </nav>
    </div>

    <div>
        <a href="{{ route('hr.workforce-plans.create') }}" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('New Workforce Plan') }}</span>
        </a>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Total Planned Positions') }}</span>
                    <h3 class="fw-bold mb-0 text-primary">{{ number_format($kpis['total_planned_positions'] ?? 0) }}</h3>
                </div>
                <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-user-plus font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Active Plans') }}</span>
                    <h3 class="fw-bold mb-0 text-success">{{ $kpis['active_plans'] ?? 0 }}</h3>
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
                    <span class="text-muted small d-block mb-1">{{ __('Total Budgeted Cost') }}</span>
                    <h3 class="fw-bold mb-0 text-info">{{ number_format($kpis['total_budgeted_cost'] ?? 0, 3) }} <span class="fs-6 fw-normal text-muted">{{ __('KWD') }}</span></h3>
                </div>
                <div class="avatar avatar-md bg-label-info rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-wallet font-22"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card kpi-card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small d-block mb-1">{{ __('Avg. Fulfillment Rate') }}</span>
                    <h3 class="fw-bold mb-0 text-warning">{{ $kpis['avg_fulfillment_rate'] ?? 0 }}%</h3>
                </div>
                <div class="avatar avatar-md bg-label-warning rounded p-2 d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-trending-up font-22"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Standard Filters Card (Exact Match with hr/penalties layout) -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Plan Year Filter -->
            <div class="col-12 col-sm-6 col-lg-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Plan Year') }}</label>
                <select id="filter_year" class="select2 form-select" data-placeholder="{{ __('All Years') }}" data-allow-clear="true">
                    <option value="">{{ __('All Years') }}</option>
                    @foreach ($years as $yr)
                        <option value="{{ $yr }}">{{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Status Filter -->
            <div class="col-12 col-sm-6 col-lg-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                <select id="filter_status" class="select2 form-select" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Reset Filters Action Button -->
            <div class="col-12 col-sm-6 col-lg-4 d-flex align-items-end">
                <button type="button" id="btnResetFilters" class="btn btn-label-secondary btn-sm w-100 d-flex align-items-center justify-content-center gap-1 shadow-xs" style="height: 38px;">
                    <i class="icon-base bx bx-reset font-18"></i>
                    <span class="fw-semibold">{{ __('Reset Filters') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Workforce Plans DataTable Card (Exact Match with hr/penalties layout) -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="workforcePlansTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Plan Info') }}</th>
                    <th>{{ __('Period') }}</th>
                    <th class="text-center">{{ __('Target Headcount') }}</th>
                    <th class="text-center">{{ __('Budgeted Cost') }}</th>
                    <th>{{ __('Fulfillment') }}</th>
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
    const dataUrl = "{{ route('hr.workforce-plans.data') }}";
    const baseUrl = "{{ url('hr/workforce-plans') }}";

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
    const table = $('#workforcePlansTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pagingType: 'simple_numbers',
        ajax: {
            url: dataUrl,
            data: function(d) {
                d.year   = $('#filter_year').val();
                d.status = $('#filter_status').val();
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
            { data: 'plan_info', name: 'title', className: 'align-middle' },
            { data: 'duration', name: 'start_date', searchable: false, className: 'align-middle' },
            { data: 'target_headcount', name: 'total_target_headcount', searchable: false, className: 'text-center align-middle' },
            { data: 'budgeted_cost', name: 'total_budgeted_cost', searchable: false, className: 'text-center align-middle' },
            { data: 'fulfillment', name: 'fulfillment', orderable: false, searchable: false, className: 'align-middle' },
            { data: 'status_badge', name: 'status', className: 'text-center align-middle' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
        ],
        order: [[1, 'desc']],
        drawCallback: function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('#workforcePlansTable [data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });

    // 2. Custom Filters Trigger Reload with debounce & reset guard (Exact match with hr/penalties)
    let isResetting = false;

    $('#filter_year, #filter_status').on('change', function() {
        if (isResetting) return;
        table.ajax.reload();
    });

    // 3. Reset Filters Handler (Exact match with hr/penalties)
    $('#btnResetFilters').on('click', function() {
        isResetting = true;
        $('#filter_year, #filter_status').val('').trigger('change');
        isResetting = false;
        table.ajax.reload();
    });

    // 4. Delete Plan Action
    $(document).on('click', '.btn-delete-plan', function() {
        const id = $(this).data('id');
        const title = $(this).data('title');

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
                    const msg = xhr.responseJSON?.message || '{{ __("Failed to delete plan.") }}';
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
                text: '{{ __("Do you really want to delete workforce plan:") }} ' + title,
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
            if (confirm('{{ __("Are you sure you want to delete this plan?") }}')) doDelete();
        }
    });
});
</script>
@endsection
