@extends('layouts/contentNavbarLayout')

@section('title', __('Incident Reports') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Student Affairs') }}</li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Incident Reports') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <div class="avatar avatar-sm bg-label-danger rounded p-1 d-flex align-items-center justify-content-center">
                <i class="icon-base bx bx-first-aid text-danger font-22"></i>
            </div>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Incident Reports List') }}</h4>
        </div>
    </div>

    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
        <a href="{{ route('academic.incident-reports.create') }}" class="btn btn-danger btn-sm d-flex align-items-center gap-2 shadow-sm">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add Incident Report') }}</span>
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="bx bx-check-circle me-2 font-20"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Filters Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Institution Filter -->
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Nursery / Institution') }}</label>
                <select id="filter_institution_id" class="select2 form-select" data-placeholder="{{ __('All Nurseries') }}">
                    <option value="">{{ __('All Nurseries') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Branch Filter -->
            <div class="col-12 col-md-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                <select id="filter_branch_id" class="select2 form-select" data-placeholder="{{ __('All Branches') }}">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branches as $br)
                        <option value="{{ $br->id }}">{{ $br->translate(app()->getLocale())?->name ?? $br->translate('ar')?->name ?? $br->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Incident Type Filter -->
            <div class="col-12 col-md-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Incident Type') }}</label>
                <select id="filter_incident_type_id" class="select2 form-select" data-placeholder="{{ __('All Incident Types') }}">
                    <option value="">{{ __('All Incident Types') }}</option>
                    @foreach ($incidentTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->translate(app()->getLocale())?->name ?? $type->translate('ar')?->name ?? $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Date Filter -->
            <div class="col-12 col-md-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Incident Date') }}</label>
                <input type="text" id="filter_date" class="form-control js-flatpickr-date flatpickr-input active" placeholder="YYYY-MM-DD" autocomplete="off">
            </div>

            <!-- 5. Search & Reset Button -->
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="button" id="btn_filter_reset" class="btn btn-label-secondary btn-sm w-100">
                    <i class="bx bx-refresh me-1 font-16"></i>{{ __('Reset') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">{{ __('Incident Reports List') }}</h5>
    </div>

    <div class="card-datatable table-responsive">
        <table id="incidentReportsTable" class="table table-hover border-top w-100">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>{{ __('Student') }}</th>
                    <th>{{ __('Incident Type') }}</th>
                    <th>{{ __('Incident Date') }}</th>
                    <th>{{ __('Location') }}</th>
                    <th>{{ __('Responsible Officer') }}</th>
                    <th>{{ __('Witnesses') }}</th>
                    <th class="text-center" style="width: 130px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteIncidentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center p-3">
            <div class="modal-body">
                <div class="avatar avatar-lg bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bx bx-trash text-danger font-32"></i>
                </div>
                <h5 class="mb-2 fw-bold">{{ __('Delete Incident Report') }}</h5>
                <p class="text-muted small mb-4">{{ __('This action cannot be undone.') }}</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteIncidentBtn">{{ __('Yes, Delete') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let deleteId = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Initialize Select2 if present
    if (window.jQuery && jQuery().select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dir: document.documentElement.dir === 'rtl' ? 'rtl' : 'ltr'
        });
    }

    // Initialize Flatpickr if present
    if (typeof window.flatpickr !== 'undefined') {
        $('.js-flatpickr-date').flatpickr({
            dateFormat: 'Y-m-d',
            allowInput: true
        });
    }

    // Initialize DataTable
    const table = $('#incidentReportsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: @json(route('academic.incident-reports.data')),
            data: function (d) {
                d.institution_id   = $('#filter_institution_id').val();
                d.branch_id        = $('#filter_branch_id').val();
                d.incident_type_id = $('#filter_incident_type_id').val();
                d.date             = $('#filter_date').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false },
            { data: 'student_html', name: 'student_html' },
            { data: 'incident_type_badge', name: 'incident_type_badge' },
            { data: 'incident_datetime', name: 'date' },
            { data: 'location_html', name: 'location' },
            { data: 'officer_name', name: 'officer_name' },
            { data: 'witnesses_badge', name: 'persons', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[3, 'desc']],
        pagingType: 'simple_numbers',
        language: {
            emptyTable: `
                <div class="text-center py-5">
                    <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                        <i class="icon-base bx bx-first-aid font-32 text-danger"></i>
                    </div>
                    <h6 class="fw-bold mb-1">{{ __('No incident reports found') }}</h6>
                    <p class="text-muted small mb-0">{{ __('Click "Add Incident Report" to create the first record.') }}</p>
                </div>
            `
        },
        drawCallback: function () {
            // Enable tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });

    // Filters event listeners
    $('#filter_institution_id, #filter_branch_id, #filter_incident_type_id, #filter_date').on('change', function () {
        table.ajax.reload();
    });

    $('#btn_filter_reset').on('click', function () {
        $('#filter_institution_id').val('').trigger('change');
        $('#filter_branch_id').val('').trigger('change');
        $('#filter_incident_type_id').val('').trigger('change');
        $('#filter_date').val('');
        table.ajax.reload();
    });

    // Delete handling
    $(document).on('click', '.btn-delete-incident', function () {
        deleteId = $(this).data('id');
        $('#deleteIncidentModal').modal('show');
    });

    $('#confirmDeleteIncidentBtn').on('click', function () {
        if (!deleteId) return;

        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> ' + @json(__('Deleting...')));

        $.ajax({
            url: @json(url('academic/incident-reports')) + '/' + deleteId,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function (res) {
                $('#deleteIncidentModal').modal('hide');
                btn.prop('disabled', false).text(@json(__('Yes, Delete')));
                table.ajax.reload(null, false);
            },
            error: function () {
                btn.prop('disabled', false).text(@json(__('Yes, Delete')));
                alert(@json(__('An error occurred while deleting the record.')));
            }
        });
    });
});
</script>
@endsection
