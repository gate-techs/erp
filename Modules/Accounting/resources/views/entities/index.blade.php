@extends('layouts/contentNavbarLayout')

@section('title', __('Accounting Entities') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-buildings text-primary font-24"></i>
            <span>{{ __('Accounting Entities') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Entities') }}</li>
            </ol>
        </nav>
    </div>

    @can('accounting.entities.create')
    <div>
        <!-- Button that opens the Side Offcanvas matching the theme design -->
        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddEntity" aria-controls="offcanvasAddEntity">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add New Entity') }}</span>
        </button>
    </div>
    @endcan
</div>

<!-- Alert Notifications -->
<div id="alertContainer"></div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible d-flex align-items-center mb-4" role="alert">
        <i class="icon-base bx bx-check-circle me-2 font-20"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible d-flex align-items-center mb-4" role="alert">
        <i class="icon-base bx bx-error-circle me-2 font-20"></i>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Standard Filters Card (Exact Match with /users layout) -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Type Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Entity Type') }}</label>
                <select id="filter_type" class="select2 form-select" data-placeholder="{{ __('All Types') }}" data-allow-clear="true">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Nursery Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Associated Nursery') }}</label>
                <select id="filter_nursery_id" class="select2 form-select" data-placeholder="{{ __('All Nurseries') }}" data-allow-clear="true">
                    <option value="">{{ __('All Nurseries') }}</option>
                    @foreach ($allNurseries as $nursery)
                        <option value="{{ $nursery->id }}">
                            {{ $nursery->translate(app()->getLocale())?->name ?? $nursery->translate('ar')?->name ?? $nursery->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Status Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                <select id="filter_status" class="select2 form-select" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
            </div>

            <!-- 4. Reset Filters Action Button (Exact Match with /users) -->
            <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-end">
                <button type="button" id="btnResetFilters" class="btn btn-label-secondary btn-sm w-100 d-flex align-items-center justify-content-center gap-1 shadow-xs" style="height: 38px;">
                    <i class="icon-base bx bx-reset font-18"></i>
                    <span class="fw-semibold">{{ __('Reset Filters') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Entities DataTable Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="entitiesTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Entity') }}</th>
                    <th class="text-center">{{ __('Entity Type') }}</th>
                    <th>{{ __('Associated Nursery') }}</th>
                    <th class="text-center">{{ __('Currency') }}</th>
                    <th class="text-center" style="width: 110px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Created Date') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loaded via Server-Side DataTables -->
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 1. Offcanvas: Add New Entity (Matching /users side offcanvas design)       -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddEntity" aria-labelledby="offcanvasAddEntityLabel" style="width: 500px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddEntityLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-plus-circle text-primary font-22"></i>
            <span>{{ __('Add New Entity') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form action="{{ route('accounting.entities.store') }}" method="POST" id="addEntityForm">
            @csrf

            <!-- Entity Name (Arabic) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Entity Name (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" class="form-control" placeholder="{{ __('Entity Name (Arabic)') }}" required />
            </div>

            <!-- Entity Name (English) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Entity Name (English)') }}</label>
                <input type="text" name="name_en" class="form-control" placeholder="{{ __('Entity Name (English)') }}" />
            </div>

            <!-- Code (Auto-generated) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Entity Code') }}</label>
                <input type="text" name="code" id="add_code" class="form-control bg-light" value="{{ $nextCode }}" readonly placeholder="{{ __('Auto-generated') }}" />
            </div>

            <!-- Entity Type -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Entity Type') }} <span class="text-danger">*</span></label>
                <select name="type" id="add_type" class="select2 form-select" required>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ $key === 'nursery' ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Associated Nursery (Only shown when type is nursery) -->
            <div class="mb-3" id="add_nursery_group">
                <label class="form-label required fw-semibold text-heading">{{ __('Associated Nursery') }} <span class="text-danger">*</span></label>
                <select name="nursery_id" id="add_nursery_id" class="select2 form-select" data-placeholder="{{ __('Select Nursery') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Nursery') }}</option>
                    @foreach ($availableNurseries as $nursery)
                        <option value="{{ $nursery->id }}">
                            {{ $nursery->translate(app()->getLocale())?->name ?? $nursery->translate('ar')?->name ?? $nursery->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Currency (Text Input) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Currency') }} <span class="text-danger">*</span></label>
                <input type="text" name="currency" class="form-control text-uppercase" placeholder="KWD" value="{{ $defaultCurrency }}" maxlength="10" required />
            </div>

            <!-- Description (Arabic) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (Arabic)') }}</label>
                <textarea name="description_ar" class="form-control" rows="2" placeholder="{{ __('Description (Arabic)') }}"></textarea>
            </div>

            <!-- Description (English) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (English)') }}</label>
                <textarea name="description_en" class="form-control" rows="2" placeholder="{{ __('Description (English)') }}"></textarea>
            </div>

            <!-- Chart of Accounts Setup Card -->
            <div class="card border border-primary border-opacity-25 bg-label-primary shadow-none mb-3">
                <div class="card-body p-3">
                    <label class="form-label fw-bold text-heading d-flex align-items-center gap-2 mb-1">
                        <i class="icon-base bx bx-sitemap text-primary font-18"></i>
                        <span>{{ __('Chart of Accounts Setup') }}</span>
                    </label>
                    <p class="text-muted small mb-2">
                        {{ __('Choose how you would like to initialize the Chart of Accounts for this entity.') }}
                    </p>

                    <div class="d-flex flex-column gap-2">
                        <!-- Option 1: Standard Template (Default) -->
                        <div class="form-check p-2 bg-white rounded border">
                            <input class="form-check-input ms-0 me-2" type="radio" name="coa_action" id="coa_standard" value="standard_template" checked>
                            <label class="form-check-label w-100 cursor-pointer" for="coa_standard">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fw-semibold text-heading">{{ __('Standard Chart of Accounts Template') }}</span>
                                    <span class="badge bg-label-primary text-uppercase">{{ __('Recommended') }}</span>
                                </div>
                                <small class="text-muted d-block mt-1">{{ __('Automatically creates standard assets, liabilities, equity, revenues, and expenses.') }}</small>
                            </label>
                        </div>

                        <!-- Option 2: Clone from Existing Entity -->
                        @if(isset($existingEntities) && $existingEntities->isNotEmpty())
                        <div class="form-check p-2 bg-white rounded border">
                            <input class="form-check-input ms-0 me-2" type="radio" name="coa_action" id="coa_clone" value="clone_entity">
                            <label class="form-check-label w-100 cursor-pointer" for="coa_clone">
                                <span class="fw-semibold text-heading">{{ __('Clone from Existing Entity') }}</span>
                                <small class="text-muted d-block mt-1">{{ __('Copy entire chart structure from another active entity.') }}</small>
                            </label>
                            <div class="mt-2 d-none" id="clone_entity_wrapper">
                                <select name="clone_from_entity_id" id="add_clone_from_entity_id" class="form-select select2" data-placeholder="{{ __('Select source entity...') }}">
                                    <option value="">{{ __('Select source entity...') }}</option>
                                    @foreach($existingEntities as $exEntity)
                                        <option value="{{ $exEntity->id }}">
                                            {{ $exEntity->translate(app()->getLocale())?->name ?? $exEntity->translate('ar')?->name ?? $exEntity->code }} ({{ $exEntity->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif

                        <!-- Option 3: Blank -->
                        <div class="form-check p-2 bg-white rounded border">
                            <input class="form-check-input ms-0 me-2" type="radio" name="coa_action" id="coa_blank" value="blank">
                            <label class="form-check-label w-100 cursor-pointer" for="coa_blank">
                                <span class="fw-semibold text-heading">{{ __('Blank Chart of Accounts') }}</span>
                                <small class="text-muted d-block mt-1">{{ __('Start with an empty tree and add accounts manually.') }}</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Radio -->
            <div class="mb-4">
                <label class="form-label d-block fw-semibold text-heading mb-2">{{ __('Status') }}</label>
                <div class="d-flex align-items-center gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="addStatusActive" value="active" checked>
                        <label class="form-check-label fw-semibold text-success" for="addStatusActive">{{ __('Active') }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="addStatusInactive" value="inactive">
                        <label class="form-check-label fw-semibold text-danger" for="addStatusInactive">{{ __('Inactive') }}</label>
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-4" id="btnSubmitAdd">
                    <i class="icon-base bx bx-save"></i>
                    <span>{{ __('Save Entity') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit Entity (Matching /users side offcanvas design)          -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditEntity" aria-labelledby="offcanvasEditEntityLabel" style="width: 500px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasEditEntityLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit text-info font-22"></i>
            <span>{{ __('Edit Entity') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form action="" method="POST" id="editEntityForm">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_entity_id" name="id" />

            <!-- Entity Name (Arabic) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Entity Name (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="edit_name_ar" class="form-control" required />
            </div>

            <!-- Entity Name (English) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Entity Name (English)') }}</label>
                <input type="text" name="name_en" id="edit_name_en" class="form-control" />
            </div>

            <!-- Code (Auto-generated / Readonly) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Entity Code') }}</label>
                <input type="text" name="code" id="edit_code" class="form-control bg-light" readonly />
            </div>

            <!-- Entity Type -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Entity Type') }} <span class="text-danger">*</span></label>
                <select name="type" id="edit_type" class="select2 form-select" required>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Associated Nursery (Only shown when type is nursery) -->
            <div class="mb-3" id="edit_nursery_group">
                <label class="form-label required fw-semibold text-heading">{{ __('Associated Nursery') }} <span class="text-danger">*</span></label>
                <select name="nursery_id" id="edit_nursery_id" class="select2 form-select" data-placeholder="{{ __('Select Nursery') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Nursery') }}</option>
                    @foreach ($allNurseries as $nursery)
                        <option value="{{ $nursery->id }}">
                            {{ $nursery->translate(app()->getLocale())?->name ?? $nursery->translate('ar')?->name ?? $nursery->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Currency (Text Input) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Currency') }} <span class="text-danger">*</span></label>
                <input type="text" name="currency" id="edit_currency" class="form-control text-uppercase" maxlength="10" required />
            </div>

            <!-- Description (Arabic) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (Arabic)') }}</label>
                <textarea name="description_ar" id="edit_description_ar" class="form-control" rows="2"></textarea>
            </div>

            <!-- Description (English) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (English)') }}</label>
                <textarea name="description_en" id="edit_description_en" class="form-control" rows="2"></textarea>
            </div>

            <!-- Status Radio -->
            <div class="mb-4">
                <label class="form-label d-block fw-semibold text-heading mb-2">{{ __('Status') }}</label>
                <div class="d-flex align-items-center gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="editStatusActive" value="active">
                        <label class="form-check-label fw-semibold text-success" for="editStatusActive">{{ __('Active') }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="editStatusInactive" value="inactive">
                        <label class="form-check-label fw-semibold text-danger" for="editStatusInactive">{{ __('Inactive') }}</label>
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-4" id="btnSubmitEdit">
                    <i class="icon-base bx bx-save"></i>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 3. Modal: Delete Entity Confirmation (Exact match with /users design)     -->
<!-- ========================================================================= -->
<div class="modal fade" id="deleteEntityModal" tabindex="-1" aria-labelledby="deleteEntityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form id="deleteEntityForm" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-body text-center py-4">
                <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-trash font-32 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Confirm Delete') }}</h5>
                <p class="text-muted mb-0">
                    {{ __('Are you sure you want to delete:') }}
                    <br>
                    <strong id="delete_target_name" class="text-danger"></strong>؟
                </p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 pb-4">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-danger">{{ __('Yes, Delete') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;
    const dataUrl = "{{ route('accounting.entities.data') }}";
    const baseUrl = "{{ url('accounting/entities') }}";

    // 1. Initialize Server-Side DataTable (Exact match with /users layout)
    const table = $('#entitiesTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pagingType: 'simple_numbers',
        ajax: {
            url: dataUrl,
            data: function (d) {
                d.type = $('#filter_type').val();
                d.nursery_id = $('#filter_nursery_id').val();
                d.status = $('#filter_status').val();
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
                data: 'entity_info',
                name: 'code',
                className: 'align-middle',
                render: (data, type, row) => data
            },
            {
                data: 'type_badge',
                name: 'type',
                className: 'text-center align-middle',
                render: (data, type, row) => data
            },
            {
                data: 'nursery_info',
                name: 'nursery_id',
                className: 'align-middle',
                render: (data, type, row) => data
            },
            {
                data: 'currency_badge',
                name: 'currency',
                className: 'text-center align-middle',
                render: (data, type, row) => data
            },
            {
                data: 'status_badge',
                name: 'status',
                className: 'text-center align-middle',
                render: (data, type, row) => data
            },
            {
                data: 'created_at',
                name: 'created_at',
                className: 'text-center align-middle',
                render: data => `<span class="text-muted">${data || '-'}</span>`
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-center align-middle',
                render: (data, type, row) => data
            }
        ],
        order: [[6, 'desc']],
        drawCallback: function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('#entitiesTable [data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });

    // 2. Custom Filters Trigger Reload with debounce / reset guard (Exact match with /users)
    let isResetting = false;

    $('#filter_type, #filter_nursery_id, #filter_status').on('change', function () {
        if (isResetting) return;
        table.ajax.reload();
    });

    // Ensure global select2 initializer is executed if present
    if (typeof window.initSelect2 === 'function') {
        window.initSelect2();
    } else {
        $('.select2').each(function () {
            const placeholder = $(this).data('placeholder') || '';
            const allowClear = $(this).data('allow-clear') !== false;

            $(this).select2({
                placeholder: placeholder,
                allowClear: allowClear,
                width: '100%',
                dropdownParent: $(this).closest('.offcanvas').length ? $(this).closest('.offcanvas') : $(document.body)
            });
        });
    }

    // 3. Dynamic Nursery Visibility Toggle (Show only if type === nursery)
    function toggleNurseryGroup(typeSelect, groupElement) {
        if ($(typeSelect).val() === 'nursery') {
            groupElement.removeClass('d-none').show();
            groupElement.find('select').prop('required', true);
        } else {
            groupElement.addClass('d-none').hide();
            groupElement.find('select').prop('required', false).val('').trigger('change');
        }
    }

    $('#add_type').on('change', function () {
        toggleNurseryGroup(this, $('#add_nursery_group'));
    });

    $('#edit_type').on('change', function () {
        toggleNurseryGroup(this, $('#edit_nursery_group'));
    });

    // Initial check
    toggleNurseryGroup($('#add_type'), $('#add_nursery_group'));
    $('#offcanvasAddEntity').on('show.bs.offcanvas', function () {
        toggleNurseryGroup($('#add_type'), $('#add_nursery_group'));
        $('#coa_standard').prop('checked', true).trigger('change');
    });

    // 3.1 Toggle Clone Entity Dropdown in Add Entity Offcanvas
    $('input[name="coa_action"]').on('change', function () {
        if ($('#coa_clone').is(':checked')) {
            $('#clone_entity_wrapper').removeClass('d-none');
            $('#add_clone_from_entity_id').prop('required', true);
        } else {
            $('#clone_entity_wrapper').addClass('d-none');
            $('#add_clone_from_entity_id').prop('required', false).val('').trigger('change');
        }
    });

    // 4. Reset Filters Handler (Exact match with /users)
    $('#btnResetFilters').on('click', function () {
        isResetting = true;
        $('#filter_type, #filter_nursery_id, #filter_status').val('').trigger('change');
        isResetting = false;
        table.ajax.reload();
    });

    // Helper for displaying alert message
    function showAlert(type, message) {
        const icon = type === 'success' ? 'bx-check-circle' : 'bx-error-circle';
        const html = `
            <div class="alert alert-${type} alert-dismissible d-flex align-items-center mb-4" role="alert">
                <i class="icon-base bx ${icon} me-2 font-20"></i>
                <div>${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('#alertContainer').html(html);
        $('html, body').animate({ scrollTop: $('#alertContainer').offset().top - 80 }, 300);
    }

    // 5. AJAX Submission: Add Entity
    const addOffcanvasEl = document.getElementById('offcanvasAddEntity');
    const addOffcanvas = addOffcanvasEl ? bootstrap.Offcanvas.getOrCreateInstance(addOffcanvasEl) : null;
    const addForm = $('#addEntityForm');

    addForm.on('submit', function (e) {
        e.preventDefault();
        const submitBtn = $('#btnSubmitAdd');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> ' + "{{ __('Saving...') }}");

        $.ajax({
            url: addForm.attr('action'),
            method: 'POST',
            data: addForm.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (res) {
                if (addOffcanvas) addOffcanvas.hide();
                addForm[0].reset();
                $('#add_type').val('nursery').trigger('change');
                $('#add_nursery_id').val('').trigger('change');
                $('#coa_standard').prop('checked', true).trigger('change');
                table.ajax.reload();
                showAlert('success', res.message || "{{ __('Entity created successfully.') }}");
                // Update auto-generated code for next entry if provided
                if (res.data && res.data.id) {
                    $('#add_code').val('ENT-' + String(res.data.id + 1).padStart(4, '0'));
                }
            },
            error: function (xhr) {
                let errorMsg = "{{ __('An error occurred while saving.') }}";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const firstKey = Object.keys(xhr.responseJSON.errors)[0];
                    errorMsg = xhr.responseJSON.errors[firstKey][0];
                }
                alert(errorMsg);
            },
            complete: function () {
                submitBtn.prop('disabled', false).html('<i class="icon-base bx bx-save me-1"></i> ' + "{{ __('Save Entity') }}");
            }
        });
    });

    // 6. Open Edit Offcanvas & Load Data
    const editOffcanvasEl = document.getElementById('offcanvasEditEntity');
    const editOffcanvas = editOffcanvasEl ? bootstrap.Offcanvas.getOrCreateInstance(editOffcanvasEl) : null;

    $(document).on('click', '.btn-edit-entity', function () {
        const id = $(this).data('id');
        const fetchUrl = baseUrl + '/' + id;

        $.ajax({
            url: fetchUrl,
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                if (!res.success) return;
                const d = res.data;

                $('#edit_entity_id').val(d.id);
                $('#editEntityForm').attr('action', baseUrl + '/' + d.id);
                $('#edit_name_ar').val(d.name_ar);
                $('#edit_name_en').val(d.name_en);
                $('#edit_description_ar').val(d.description_ar);
                $('#edit_description_en').val(d.description_en);
                $('#edit_code').val(d.code);
                $('#edit_currency').val(d.currency);

                $('#edit_type').val(d.type).trigger('change');
                toggleNurseryGroup($('#edit_type'), $('#edit_nursery_group'));

                if (d.type === 'nursery') {
                    $('#edit_nursery_id').val(d.nursery_id).trigger('change');
                } else {
                    $('#edit_nursery_id').val('').trigger('change');
                }

                if (d.status === 'active') {
                    $('#editStatusActive').prop('checked', true);
                } else {
                    $('#editStatusInactive').prop('checked', true);
                }

                if (editOffcanvas) editOffcanvas.show();
            },
            error: function () {
                alert("{{ __('Failed to load entity details.') }}");
            }
        });
    });

    // 6. AJAX Submission: Edit Entity
    const editForm = $('#editEntityForm');
    editForm.on('submit', function (e) {
        e.preventDefault();
        const submitBtn = $('#btnSubmitEdit');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> ' + "{{ __('Saving...') }}");

        $.ajax({
            url: editForm.attr('action'),
            method: 'POST',
            data: editForm.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (res) {
                if (editOffcanvas) editOffcanvas.hide();
                table.ajax.reload();
                showAlert('success', res.message || "{{ __('Entity updated successfully.') }}");
            },
            error: function (xhr) {
                let errorMsg = "{{ __('An error occurred while saving.') }}";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const firstKey = Object.keys(xhr.responseJSON.errors)[0];
                    errorMsg = xhr.responseJSON.errors[firstKey][0];
                }
                alert(errorMsg);
            },
            complete: function () {
                submitBtn.prop('disabled', false).html('<i class="icon-base bx bx-save me-1"></i> ' + "{{ __('Save Changes') }}");
            }
        });
    });

    // 7. Delete Confirmation Modal (Exact match with /users)
    const deleteModalEl = document.getElementById('deleteEntityModal');
    const deleteModal = deleteModalEl ? bootstrap.Modal.getOrCreateInstance(deleteModalEl) : null;
    const deleteForm = $('#deleteEntityForm');

    $(document).on('click', '.btn-delete-entity', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');

        deleteForm.attr('action', baseUrl + '/' + id);
        $('#delete_target_name').text(name);

        if (deleteModal) deleteModal.show();
    });

    deleteForm.on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: deleteForm.attr('action'),
            method: 'POST',
            data: deleteForm.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                if (deleteModal) deleteModal.hide();
                table.ajax.reload();
                showAlert('success', res.message || "{{ __('Entity deleted successfully.') }}");
            },
            error: function (xhr) {
                let errorMsg = "{{ __('Failed to delete entity.') }}";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            }
        });
    });
});
</script>
@endsection
