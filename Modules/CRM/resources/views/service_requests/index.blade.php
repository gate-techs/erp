@extends('layouts/contentNavbarLayout')

@section('title', __('Service Requests') . ' - ' . config('variables.templateName'))

@section('page-style')
    <style>
        .status-filter-card {
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            border: 2px solid transparent !important;
        }
        .status-filter-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.25rem 1rem rgba(161, 172, 184, 0.35) !important;
        }
        .status-filter-card.active-status-card {
            border-color: var(--bs-primary) !important;
            background-color: rgba(var(--bs-primary-rgb), 0.03) !important;
            box-shadow: 0 0.25rem 0.75rem rgba(var(--bs-primary-rgb), 0.18) !important;
        }
    </style>
@endsection

@section('content')
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2 text-heading">
                <i class="icon-base bx bx-conversation text-primary font-24"></i>
                <span>{{ __('Service Requests') }}</span>
            </h4>
            <p class="text-muted mb-0">{{ __('Manage service requests and complaints.') }}</p>
        </div>
        @can('crm.service-requests.create')
        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm"
            data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddServiceRequest">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add New Request / Complaint') }}</span>
        </button>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (!empty($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Status Cards Filter Row (Interactive Metric Cards) --}}
    <div class="row g-3 mb-4">
        <!-- All Requests -->
        <div class="col-6 col-md-3">
            <div class="card status-filter-card shadow-sm border h-100 active-status-card" data-status="" role="button">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div class="d-flex flex-column">
                        <span class="text-muted small fw-medium mb-1">{{ __('All Requests') }}</span>
                        <h4 class="mb-0 fw-bold text-heading" id="count_status_all">{{ $statusCounts['all'] ?? 0 }}</h4>
                    </div>
                    <div class="avatar avatar-md">
                        <span class="avatar-initial rounded-2 bg-label-primary">
                            <i class="icon-base bx bx-layer font-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="col-6 col-md-3">
            <div class="card status-filter-card shadow-sm border h-100" data-status="pending" role="button">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div class="d-flex flex-column">
                        <span class="text-muted small fw-medium mb-1">{{ __('Pending') }}</span>
                        <h4 class="mb-0 fw-bold text-warning" id="count_status_pending">{{ $statusCounts['pending'] ?? 0 }}</h4>
                    </div>
                    <div class="avatar avatar-md">
                        <span class="avatar-initial rounded-2 bg-label-warning">
                            <i class="icon-base bx bx-time-five font-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="col-6 col-md-3">
            <div class="card status-filter-card shadow-sm border h-100" data-status="inprogress" role="button">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div class="d-flex flex-column">
                        <span class="text-muted small fw-medium mb-1">{{ __('In Progress') }}</span>
                        <h4 class="mb-0 fw-bold text-info" id="count_status_inprogress">{{ $statusCounts['inprogress'] ?? 0 }}</h4>
                    </div>
                    <div class="avatar avatar-md">
                        <span class="avatar-initial rounded-2 bg-label-info">
                            <i class="icon-base bx bx-loader-alt font-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complete -->
        <div class="col-6 col-md-3">
            <div class="card status-filter-card shadow-sm border h-100" data-status="complete" role="button">
                <div class="card-body d-flex align-items-center justify-content-between p-3">
                    <div class="d-flex flex-column">
                        <span class="text-muted small fw-medium mb-1">{{ __('Complete') }}</span>
                        <h4 class="mb-0 fw-bold text-success" id="count_status_complete">{{ $statusCounts['complete'] ?? 0 }}</h4>
                    </div>
                    <div class="avatar avatar-md">
                        <span class="avatar-initial rounded-2 bg-label-success">
                            <i class="icon-base bx bx-check-circle font-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters (Exact /items style) --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label fw-semibold text-heading small mb-1">{{ __('Type') }}</label>
                    <select id="filter_type" class="form-select select2">
                        <option value="">{{ __('All Types') }}</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                    <select id="filter_status" class="form-select select2">
                        <option value="">{{ __('All Statuses') }}</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                    <select id="filter_branch_id" class="form-select select2">
                        <option value="">{{ __('All Branches') }}</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label fw-semibold text-heading small mb-1">{{ __('Department') }}</label>
                    <select id="filter_department_id" class="form-select select2">
                        <option value="">{{ __('All Departments') }}</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-lg-2">
                    <button type="button" id="btnResetFilters" class="btn btn-label-secondary w-100">
                        <i class="icon-base bx bx-reset me-1"></i>{{ __('Reset Filters') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTable Card -->
    <div class="card shadow-sm border-0">
        <div class="card-datatable table-responsive">
            <table id="serviceRequestsTable" class="table table-hover border-top w-100 text-nowrap">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>{{ __('Civil ID') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Branch') }}</th>
                        <th>{{ __('Department') }}</th>
                        <th>{{ __('Call Recording') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th class="text-center" style="width: 120px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Offcanvas Add -->
    <div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg"
        tabindex="-1" id="offcanvasAddServiceRequest" style="width: 580px; max-width: 95vw;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <h5 class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
                <i class="icon-base bx bx-plus-circle text-primary"></i>
                <span>{{ __('Add New Request / Complaint') }}</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-4">
            <form action="{{ route('crm.service-requests.store') }}" method="POST" enctype="multipart/form-data" id="addServiceRequestForm">
                @csrf
                @include('crm::service_requests.partials.form', ['prefix' => 'add', 'serviceRequest' => null])
                <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-3">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base bx bx-check me-1"></i>{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Offcanvas Edit -->
    <div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg"
        tabindex="-1" id="offcanvasEditServiceRequest" style="width: 580px; max-width: 95vw;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <h5 class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
                <i class="icon-base bx bx-edit text-primary"></i>
                <span>{{ __('Edit Request / Complaint') }}</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-4">
            <form action="" method="POST" id="editServiceRequestForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('crm::service_requests.partials.form', ['prefix' => 'edit', 'serviceRequest' => null])
                <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-3">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base bx bx-check me-1"></i>{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Details Modal -->
    <div class="modal fade" id="modalViewServiceRequest" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
                        <i class="icon-base bx bx-detail text-info"></i>
                        <span>{{ __('Request Details') }}</span>
                        <span id="view_badge_type" class="ms-2"></span>
                        <span id="view_badge_status"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-md-4">
                            <small class="text-muted d-block mb-1">{{ __('Civil ID') }}</small>
                            <span id="view_civil_id" class="fw-semibold font-monospace text-heading">-</span>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <small class="text-muted d-block mb-1">{{ __('Name') }}</small>
                            <span id="view_name" class="fw-semibold text-heading">-</span>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <small class="text-muted d-block mb-1">{{ __('Email') }}</small>
                            <span id="view_email" class="font-monospace">-</span>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <small class="text-muted d-block mb-1">{{ __('Branch') }}</small>
                            <span id="view_branch">-</span>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <small class="text-muted d-block mb-1">{{ __('Department') }}</small>
                            <span id="view_department">-</span>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <small class="text-muted d-block mb-1">{{ __('Call Recording Number') }}</small>
                            <span id="view_call_recording" class="font-monospace">-</span>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <small class="text-muted d-block mb-1">{{ __('Created At') }}</small>
                            <span id="view_created_at" class="font-monospace">-</span>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <h6 class="fw-bold mb-2">{{ __('Notes') }}:</h6>
                            <div id="view_notes" class="p-3 bg-light rounded text-body border" style="min-height: 80px; white-space: pre-wrap;">-</div>
                        </div>

                        <div class="col-12 col-md-6" id="view_image_container">
                            <h6 class="fw-bold mb-2">{{ __('Image') }}:</h6>
                            <div id="view_image_content">
                                <span class="text-muted">{{ __('No Image') }}</span>
                            </div>
                        </div>

                        <div class="col-12 col-md-6" id="view_attachments_container">
                            <h6 class="fw-bold mb-2">{{ __('Attachments') }}:</h6>
                            <ul id="view_attachments_list" class="list-group list-group-flush border rounded p-1">
                                <li class="list-group-item text-muted">{{ __('No Attachments') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Delete Form -->
    <form id="deleteServiceRequestForm" action="" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const $ = window.jQuery || window.$;
            if (!$) return;

            const isRtl = $('html').attr('dir') === 'rtl' || $('html').attr('lang') === 'ar';

            // Universal Select2 Initializer with Bootstrap 5 Theme (produces .select2-selection__rendered)
            function initPageSelect2(container) {
                const target = container ? $(container) : $(document);
                target.find('select.select2').each(function () {
                    const $el = $(this);
                    if ($el.hasClass('select2-hidden-accessible')) {
                        return;
                    }
                    const $parent = $el.closest('.offcanvas, .modal');
                    $el.select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        dir: isRtl ? 'rtl' : 'ltr',
                        dropdownParent: $parent.length ? $parent : null,
                        placeholder: $el.data('placeholder') || ($el.find('option:first').val() === '' ? $el.find('option:first').text() : ''),
                        allowClear: $el.data('allow-clear') !== false && !$el.prop('required')
                    });
                });
            }

            // Ensure Select2 is initialized on filters and page controls
            initPageSelect2(document);

            // Re-initialize whenever an offcanvas or modal is shown
            $(document).on('shown.bs.offcanvas shown.bs.modal', function (e) {
                initPageSelect2(e.target);
            });

            // 1. Initialize DataTable
            const table = $('#serviceRequestsTable').DataTable({
                processing: true,
                serverSide: true,
                pagingType: 'simple_numbers',
                ajax: {
                    url: '{{ route('crm.service-requests.data') }}',
                    data: function (d) {
                        d.type = $('#filter_type').val();
                        d.status = $('#filter_status').val();
                        d.branch_id = $('#filter_branch_id').val();
                        d.department_id = $('#filter_department_id').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'civil_id', name: 'civil_id' },
                    { data: 'name', name: 'name' },
                    { data: 'type', name: 'type' },
                    { data: 'status', name: 'status' },
                    { data: 'branch_name', name: 'branch.name' },
                    { data: 'department_name', name: 'department.name' },
                    { data: 'call_recording_number', name: 'call_recording_number' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
                ],
                order: [[8, 'desc']]
            });

            // Filters change event (Exact /items logic)
            $('#filter_type, #filter_status, #filter_branch_id, #filter_department_id').on('change', () => table.ajax.reload());

            // Click on Status Filter Card (Top Metric Cards)
            $(document).on('click', '.status-filter-card', function () {
                const status = $(this).data('status') || '';
                $('.status-filter-card').removeClass('active-status-card');
                $(this).addClass('active-status-card');

                // Sync with filter_status select dropdown
                $('#filter_status').val(status).trigger('change');
            });

            // Sync card active state when filter_status changes via select dropdown
            $('#filter_status').on('change', function () {
                const currentVal = $(this).val() || '';
                $('.status-filter-card').removeClass('active-status-card');
                $(`.status-filter-card[data-status="${currentVal}"]`).addClass('active-status-card');
            });

            // Update status counts dynamically when DataTable reloads
            table.on('xhr', function () {
                const json = table.ajax.json();
                if (json && json.statusCounts) {
                    $('#count_status_all').text(json.statusCounts.all ?? 0);
                    $('#count_status_pending').text(json.statusCounts.pending ?? 0);
                    $('#count_status_inprogress').text(json.statusCounts.inprogress ?? 0);
                    $('#count_status_complete').text(json.statusCounts.complete ?? 0);
                }
            });

            // Reset Filters
            $('#btnResetFilters').on('click', function () {
                $('#filter_type, #filter_status, #filter_branch_id, #filter_department_id').val('').trigger('change');
                $('.status-filter-card').removeClass('active-status-card');
                $('.status-filter-card[data-status=""]').addClass('active-status-card');
                table.ajax.reload();
            });

            // Helper: Toggle Civil ID Mode between input and select
            function setCivilIdMode(prefix, mode) {
                const inputWrapper = $(`#${prefix}_input_wrapper`);
                const selectWrapper = $(`#${prefix}_select_wrapper`);
                const switchBtn = $(`#${prefix}_switch_mode_btn`);
                const inputEl = $(`#${prefix}_civil_id`);
                const selectEl = $(`#${prefix}_civil_id_select`);

                if (mode === 'select') {
                    inputWrapper.hide();
                    selectWrapper.show();
                    switchBtn.show().html('<i class="icon-base bx bx-edit-alt me-1"></i><span>{{ __('Manual Input') }}</span>');

                    // Initialize Select2 on the dynamic select so it gets .select2-selection__rendered
                    const $parent = selectEl.closest('.offcanvas, .modal');
                    if (selectEl.hasClass('select2-hidden-accessible')) {
                        selectEl.select2('destroy');
                    }
                    selectEl.select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        dir: isRtl ? 'rtl' : 'ltr',
                        dropdownParent: $parent.length ? $parent : null,
                        placeholder: selectEl.data('placeholder') || '{{ __('Select from matched records...') }}'
                    });
                    setTimeout(function () {
                        selectEl.select2('open');
                    }, 100);
                } else {
                    selectWrapper.hide();
                    inputWrapper.show();
                    switchBtn.hide();
                    if (selectEl.hasClass('select2-hidden-accessible')) {
                        selectEl.val('').trigger('change.select2');
                    }
                    inputEl.focus();
                }
            }

            // Click on Switch to Manual Input button
            $(document).on('click', '.btn-switch-mode', function () {
                const prefix = $(this).data('prefix');
                setCivilIdMode(prefix, 'input');
                $(`#${prefix}_civil_id_feedback`).html(`
                    <small class="text-muted d-inline-flex align-items-center gap-1">
                        <i class="icon-base bx bx-edit"></i>
                        <span>{{ __('Manual entry mode') }}</span>
                    </small>
                `).show();
            });

            // 2. Civil ID AutoSearch (Live Search as user types in input)
            let searchTimeout = null;
            $(document).on('input', '.civil-id-input', function () {
                const input = $(this);
                const prefix = input.data('prefix') || (input.attr('id').startsWith('add') ? 'add' : 'edit');
                const query = input.val().trim();
                const feedbackEl = $(`#${prefix}_civil_id_feedback`);
                const selectEl = $(`#${prefix}_civil_id_select`);

                clearTimeout(searchTimeout);

                if (!query || query.length < 2) {
                    setCivilIdMode(prefix, 'input');
                    feedbackEl.hide().empty();
                    return;
                }

                feedbackEl.html(`
                    <small class="text-primary d-inline-flex align-items-center gap-1">
                        <i class="icon-base bx bx-loader-alt bx-spin"></i>
                        <span>{{ __('Searching for customer...') }}</span>
                    </small>
                `).show();

                searchTimeout = setTimeout(function () {
                    $.ajax({
                        url: '{{ route('crm.service-requests.search-civil-id') }}',
                        type: 'GET',
                        data: { term: query },
                        dataType: 'json',
                        success: function (res) {
                            if (res.found && res.data && res.data.length > 0) {
                                // Matching records found -> Switch to SELECT
                                selectEl.empty();
                                selectEl.append(`<option value="">{{ __('Select from matched records...') }} (${res.data.length})</option>`);

                                res.data.forEach(function (item) {
                                    const opt = $('<option></option>')
                                        .val(item.civil_id)
                                        .text(item.civil_id)
                                        .attr('data-civil-id', item.civil_id)
                                        .attr('data-name', item.name)
                                        .attr('data-email', item.email || '')
                                        .attr('data-branch-id', item.branch_id || '')
                                        .attr('data-department-id', item.department_id || '');
                                    selectEl.append(opt);
                                });

                                // Manual option at the bottom
                                selectEl.append(`<option value="__manual__">{{ __('Manual Input / New Customer') }}</option>`);

                                setCivilIdMode(prefix, 'select');

                                feedbackEl.html(`
                                    <small class="text-success d-inline-flex align-items-center gap-1">
                                        <i class="icon-base bx bx-check-double"></i>
                                        <span>{{ __('Found matching records. Please select or choose manual input.') }}</span>
                                    </small>
                                `).show();
                            } else {
                                // Not found -> Remain as / Switch to INPUT
                                setCivilIdMode(prefix, 'input');
                                feedbackEl.html(`
                                    <small class="text-info d-inline-flex align-items-center gap-1">
                                        <i class="icon-base bx bx-info-circle"></i>
                                        <span>{{ __('Civil ID not found - enter customer details manually') }}</span>
                                    </small>
                                `).show();
                            }
                        },
                        error: function () {
                            setCivilIdMode(prefix, 'input');
                            feedbackEl.hide().empty();
                        }
                    });
                }, 350);
            });

            // When an option is selected from the Civil ID SELECT dropdown
            $(document).on('change', '.civil-id-select', function () {
                const selectEl = $(this);
                const prefix = selectEl.data('prefix') || (selectEl.attr('id').startsWith('add') ? 'add' : 'edit');
                const selectedVal = selectEl.val();
                const feedbackEl = $(`#${prefix}_civil_id_feedback`);

                if (!selectedVal) return;

                if (selectedVal === '__manual__') {
                    setCivilIdMode(prefix, 'input');
                    feedbackEl.html(`
                        <small class="text-muted d-inline-flex align-items-center gap-1">
                            <i class="icon-base bx bx-edit"></i>
                            <span>{{ __('Manual entry mode') }}</span>
                        </small>
                    `).show();
                    return;
                }

                const selectedOpt = selectEl.find('option:selected');
                const civilId = selectedOpt.attr('data-civil-id') || selectedVal;
                const name = selectedOpt.attr('data-name');
                const email = selectedOpt.attr('data-email');
                const branchId = selectedOpt.attr('data-branch-id');
                const deptId = selectedOpt.attr('data-department-id');

                $(`#${prefix}_civil_id`).val(civilId);
                if (name) $(`#${prefix}_name`).val(name);
                if (email) $(`#${prefix}_email`).val(email);
                if (branchId) $(`#${prefix}_branch_id`).val(branchId).trigger('change');
                if (deptId) $(`#${prefix}_department_id`).val(deptId).trigger('change');

                feedbackEl.html(`
                    <small class="text-success d-inline-flex align-items-center gap-1">
                        <i class="icon-base bx bx-check-circle"></i>
                        <span>{{ __('Customer selected:') }} <strong>${name}</strong> (${civilId})</span>
                    </small>
                `).show();
            });

            // 3. Edit Service Request
            $(document).on('click', '.btn-edit-request', function () {
                const button = $(this);
                const id = button.data('id');

                $('#editServiceRequestForm').attr('action', `{{ url('crm/service-requests') }}/${id}`);
                $('#edit_civil_id').val(button.data('civil-id'));
                $('#edit_name').val(button.data('name'));
                $('#edit_email').val(button.data('email'));
                $('#edit_type').val(button.data('type')).trigger('change');
                $('#edit_status').val(button.data('status')).trigger('change');
                $('#edit_branch_id').val(button.data('branch-id')).trigger('change');
                $('#edit_department_id').val(button.data('department-id')).trigger('change');
                $('#edit_call_recording_number').val(button.data('call-recording-number'));
                $('#edit_notes').val(button.data('notes'));
                setCivilIdMode('edit', 'input');
                $('#edit_civil_id_select').empty().append('<option value="">{{ __('Select from matched records...') }}</option>');
                $('#edit_civil_id_feedback').hide().empty();

                const image = button.data('image');
                if (image) {
                    $('#edit_image_preview').attr('src', image);
                    $('#edit_image_preview_link').attr('href', image);
                    $('#edit_image_preview_box').removeClass('d-none');
                } else {
                    $('#edit_image_preview_box').addClass('d-none');
                }

                bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('offcanvasEditServiceRequest')).show();
            });

            // Clear Add Form on Close
            $('#offcanvasAddServiceRequest').on('hidden.bs.offcanvas', function () {
                $('#addServiceRequestForm')[0].reset();
                $('#add_type').val('').trigger('change');
                $('#add_status').val('pending').trigger('change');
                $('#add_branch_id').val('').trigger('change');
                $('#add_department_id').val('').trigger('change');
                setCivilIdMode('add', 'input');
                $('#add_civil_id_select').empty().append('<option value="">{{ __('Select from matched records...') }}</option>');
                $('#add_civil_id_feedback').hide().empty();
            });

            // 4. View Service Request Details
            $(document).on('click', '.btn-view-request', function () {
                const button = $(this);

                $('#view_civil_id').text(button.data('civil-id') || '-');
                $('#view_name').text(button.data('name') || '-');
                $('#view_email').text(button.data('email') || '-');
                $('#view_branch').text(button.data('branch-name') || '-');
                $('#view_department').text(button.data('department-name') || '-');
                $('#view_call_recording').text(button.data('call-recording-number') || '-');
                $('#view_created_at').text(button.data('created-at') || '-');
                $('#view_notes').text(button.data('notes') || '{{ __('Notes') }} -');

                // Badges
                $('#view_badge_type').html(`<span class="badge ${button.data('type-class')}">${button.data('type-label')}</span>`);
                $('#view_badge_status').html(`<span class="badge ${button.data('status-class')}">${button.data('status-label')}</span>`);

                // Image
                const image = button.data('image');
                if (image) {
                    $('#view_image_content').html(`
                        <a href="${image}" target="_blank" class="d-inline-block">
                            <img src="${image}" class="img-fluid rounded border p-1" style="max-height: 180px; object-fit: contain;">
                        </a>
                    `);
                } else {
                    $('#view_image_content').html('<span class="text-muted">{{ __('No Image') }}</span>');
                }

                // Attachments
                let attachments = [];
                try {
                    attachments = button.data('attachments') || [];
                    if (typeof attachments === 'string') {
                        attachments = JSON.parse(attachments);
                    }
                } catch (e) {
                    attachments = [];
                }

                if (attachments && attachments.length > 0) {
                    let itemsHtml = '';
                    attachments.forEach(function (att) {
                        itemsHtml += `
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <div class="text-truncate me-2">
                                    <i class="icon-base bx bx-paperclip me-1 text-primary"></i>
                                    <span title="${att.name}">${att.name}</span>
                                    ${att.size ? `<small class="text-muted ms-2">(${att.size})</small>` : ''}
                                </div>
                                <a href="${att.url}" target="_blank" download class="btn btn-xs btn-outline-primary">
                                    <i class="icon-base bx bx-download"></i>
                                </a>
                            </li>
                        `;
                    });
                    $('#view_attachments_list').html(itemsHtml);
                } else {
                    $('#view_attachments_list').html('<li class="list-group-item text-muted">{{ __('No Attachments') }}</li>');
                }

                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalViewServiceRequest')).show();
            });

            // 5. Delete Service Request
            $(document).on('click', '.btn-delete-request', function () {
                if (!confirm('{{ __('Are you sure you want to delete this request?') }}')) return;
                $('#deleteServiceRequestForm').attr('action', `{{ url('crm/service-requests') }}/${$(this).data('id')}`).submit();
            });
        });
    </script>
@endsection
