@extends('layouts/contentNavbarLayout')

@section('title', __('Activity & Audit Logs') . ' - ' . config('variables.templateName'))

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
        'resources/assets/vendor/libs/select2/select2.scss'
    ])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
        'resources/assets/vendor/libs/select2/select2.js'
    ])
@endsection

@section('content')
<!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div style="flex: 1 1 260px; min-width: 0;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item">{{ __('System') }}</li>
                    <li class="breadcrumb-item active">{{ __('Activity & Audit Logs') }}</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="icon-base bx bx-history text-primary font-24 flex-shrink-0"></i>
                <span>{{ __('Activity & Audit Logs') }}</span>
            </h4>
            <p class="text-muted small mb-0">{{ __('Track and document all operations, additions, modifications and user activities on the system instantly.') }}</p>
        </div>

        <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-1 text-nowrap flex-shrink-0" id="btnRefreshTable">
            <i class="bx bx-refresh"></i> {{ __('Refresh Table') }}
        </button>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3">
            <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bx bx-filter-alt text-primary"></i> {{ __('Filter and Search Options') }}
            </h6>
        </div>
        <div class="card-body pt-3">
            <div class="row g-3">
                <!-- User Filter -->
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold">{{ __('User Executed') }}</label>
                    <select id="filter_user_id" class="form-select select2" data-placeholder="{{ __('All Users') }}" data-allow-clear="true">
                        <option value="">{{ __('All Users') }}</option>
                        @foreach($lookups['users'] as $u)
                            <option value="{{ $u->id }}">
                                {{ $u->name }} ({{ $u->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Event Type -->
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold">{{ __('Action Type') }}</label>
                    <select id="filter_event" class="form-select select2" data-placeholder="{{ __('All Actions') }}" data-allow-clear="true">
                        <option value="">{{ __('All Actions') }}</option>
                        @foreach($lookups['events'] as $evt)
                            <option value="{{ $evt }}">
                                {{ ucfirst($evt) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range: From -->
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-semibold">{{ __('من تاريخ') }}</label>
                    <input type="date" id="filter_start_date" class="form-control">
                </div>

                <!-- Date Range: To -->
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-semibold">{{ __('إلى تاريخ') }}</label>
                    <input type="date" id="filter_end_date" class="form-control">
                </div>

                <!-- Action Buttons -->
                <div class="col-md-2 col-sm-12 d-flex align-items-end">
                    <button type="button" id="btnResetFilters" class="btn btn-label-secondary w-100 d-flex align-items-center justify-content-center gap-1 shadow-xs" style="height: 38px;">
                        <i class="bx bx-reset"></i>
                        <span class="fw-semibold">{{ __('إعادة تعيين') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-datatable table-responsive">
            <table id="auditLogsTable" class="table table-hover border-top w-100 align-middle text-nowrap">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th style="width: 170px;">{{ __('التاريخ والوقت') }}</th>
                        <th style="width: 190px;">{{ __('المستخدم') }}</th>
                        <th style="width: 120px;" class="text-center">{{ __('Action Type') }}</th>
                        <th style="width: 180px;">{{ __('Model / Record') }}</th>
                        <th>{{ __('Action Statement / Description') }}</th>
                        <th style="width: 90px;" class="text-center">{{ __('Details') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loaded via DataTables AJAX -->
                </tbody>
            </table>
        </div>
    </div>


<!-- Activity Log Details & Diff Modal -->
<div class="modal fade" id="auditDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2 mb-0">
                    <i class="bx bx-file-find text-primary"></i> {{ __('Record Activity Details and Modification Comparison') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="auditModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">{{ __('Loading...') }}</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ __('إغلاق') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;
    const dataUrl = "{{ route('audit-logs.data') }}";
    const baseUrl = "{{ url('audit-logs') }}";

    // 1. Initialize Server-side DataTable
    const table = $('#auditLogsTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pagingType: 'simple_numbers',
        order: [[0, 'desc']],
        ajax: {
            url: dataUrl,
            data: function (d) {
                d.user_id = $('#filter_user_id').val();
                d.event = $('#filter_event').val();
                d.start_date = $('#filter_start_date').val();
                d.end_date = $('#filter_end_date').val();
            }
        },
        columns: [
            { 
                data: 'DT_RowIndex', 
                name: 'id', 
                orderable: false, 
                searchable: false, 
                className: 'text-center align-middle',
                render: (data, type, row, meta) => `<strong class="text-muted">#${data || (meta.row + 1 + (meta.settings._iDisplayStart || 0))}</strong>`
            },
            { 
                data: 'date_formatted', 
                name: 'created_at', 
                className: 'align-middle',
                render: (data) => data 
            },
            { 
                data: 'causer_info', 
                name: 'causer.name', 
                className: 'align-middle',
                render: (data) => data 
            },
            { 
                data: 'event_badge', 
                name: 'event', 
                className: 'text-center align-middle',
                render: (data) => data 
            },
            { 
                data: 'subject_info', 
                name: 'subject_type', 
                className: 'align-middle',
                render: (data) => data 
            },
            { 
                data: 'description_formatted', 
                name: 'description', 
                className: 'align-middle text-wrap',
                render: (data) => data 
            },
            { 
                data: 'actions', 
                name: 'actions', 
                orderable: false, 
                searchable: false, 
                className: 'text-center align-middle',
                render: (data) => data 
            }
        ],
        // Search / length / info / paging texts come from the global DataTables defaults
        language: {
            zeroRecords: "{{ __('No matching records found') }}",
            emptyTable: "{{ __('No activity logs recorded') }}",
            processing: '<div class="spinner-border text-primary spinner-border-sm" role="status"><span class="visually-hidden">{{ __("Loading...") }}</span></div> {{ __("Loading data...") }}'
        }
    });

    // 2. Refresh Table Button
    $('#btnRefreshTable').on('click', function () {
        table.ajax.reload(null, false);
    });

    // 3. Filter Triggers
    let isResetting = false;
    $('#filter_user_id, #filter_event, #filter_start_date, #filter_end_date').on('change', function () {
        if (isResetting) return;
        table.ajax.reload();
    });

    // 4. Reset Filters
    $('#btnResetFilters').on('click', function () {
        isResetting = true;
        $('#filter_user_id').val('').trigger('change');
        $('#filter_event').val('').trigger('change');
        $('#filter_start_date').val('');
        $('#filter_end_date').val('');
        isResetting = false;
        table.ajax.reload();
    });

    // 5. Modal Diff / Details via Event Delegation
    const modalEl = document.getElementById('auditDetailModal');
    const modal = new bootstrap.Modal(modalEl);
    const modalBody = document.getElementById('auditModalBody');

    $(document).on('click', '.view-diff-btn', function () {
        const id = $(this).data('id') || $(this).attr('data-id');
        if (!id) return;

        modalBody.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">{{ __('Loading...') }}</span>
                </div>
                <div class="mt-2 text-muted small">{{ __('Fetching record details and differences...') }}</div>
            </div>
        `;
        modal.show();

        $.ajax({
            url: baseUrl + '/' + id,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (res) {
                if (res.success && res.html) {
                    modalBody.innerHTML = res.html;
                } else {
                    modalBody.innerHTML = `<div class="alert alert-danger mb-0">${res.error || '{{ __("Failed to load data") }}'}</div>`;
                }
            },
            error: function (xhr) {
                let err = '{{ __("An error occurred while fetching details") }}';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    err = xhr.responseJSON.error;
                }
                modalBody.innerHTML = `<div class="alert alert-danger mb-0">${err}</div>`;
            }
        });
    });
});
</script>
@endsection
