@extends('layouts/contentNavbarLayout')

@section('title', __('Cost Centers') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-pie-chart-alt-2 text-primary font-24"></i>
            <span>{{ __('Cost Centers') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Cost Centers') }}</li>
            </ol>
        </nav>
    </div>

    <div>
        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddCostCenter" aria-controls="offcanvasAddCostCenter">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add New Cost Center') }}</span>
        </button>
    </div>
</div>

<!-- Alert Notifications Container -->
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

<!-- Standard Filters Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Entity Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Accounting Entity') }}</label>
                <select id="filter_entity_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('All Entities') }}" data-allow-clear="true">
                    <option value="">{{ __('All Entities') }}</option>
                    @foreach ($entities as $entity)
                        <option value="{{ $entity->id }}" @selected($selectedEntityId === $entity->id)>
                            {{ $entity->translate(app()->getLocale())?->name ?? $entity->translate('ar')?->name ?? $entity->code }} ({{ $entity->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Type Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Type') }}</label>
                <select id="filter_type" class="form-select select2" data-control="select2" data-placeholder="{{ __('All Types') }}" data-allow-clear="true">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach($types as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Status Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                <select id="filter_status" class="form-select select2" data-control="select2" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Reset Filters Action Button -->
            <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-end">
                <button type="button" id="btnResetFilters" class="btn btn-label-secondary btn-sm w-100 d-flex align-items-center justify-content-center gap-1 shadow-xs" style="height: 38px;">
                    <i class="icon-base bx bx-reset font-18"></i>
                    <span class="fw-semibold">{{ __('Reset Filters') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cost Centers DataTable Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="costCentersTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Cost Center') }}</th>
                    <th>{{ __('Accounting Entity') }}</th>
                    <th class="text-center" style="width: 130px;">{{ __('Type') }}</th>
                    <th>{{ __('Parent Center') }}</th>
                    <th>{{ __('Branch') }}</th>
                    <th class="text-center" style="width: 110px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Created Date') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loaded via Server-Side DataTables -->
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 1. Offcanvas: Add New Cost Center                                         -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddCostCenter" aria-labelledby="offcanvasAddCostCenterLabel" style="width: 540px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddCostCenterLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-pie-chart-alt-2 text-primary font-22"></i>
            <span>{{ __('Add New Cost Center') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form action="{{ route('accounting.cost-centers.store') }}" method="POST" id="addCostCenterForm">
            @csrf

            <!-- Entity Selection -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Accounting Entity') }} <span class="text-danger">*</span></label>
                <select name="entity_id" id="add_entity_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Entity') }}" required>
                    <option value="">{{ __('Select Entity') }}</option>
                    @foreach ($entities as $entity)
                        <option value="{{ $entity->id }}" @selected($selectedEntityId === $entity->id)>
                            {{ $entity->translate(app()->getLocale())?->name ?? $entity->translate('ar')?->name ?? $entity->code }} ({{ $entity->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Code & Type Row -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label required fw-semibold text-heading">{{ __('Cost Center Code') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="icon-base bx bx-barcode"></i></span>
                        <input type="text" name="code" id="add_code" class="form-control" placeholder="{{ __('e.g. CC-101') }}" required />
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label required fw-semibold text-heading">{{ __('Type') }} <span class="text-danger">*</span></label>
                    <select name="type" id="add_type" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Type') }}" required>
                        @foreach($types as $type)
                            <option value="{{ $type->value }}" @selected($type->value === 'cost')>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Cost Center Name (Arabic) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Cost Center Name (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="add_name_ar" class="form-control" placeholder="{{ __('e.g. Administration & Finance') }}" required dir="rtl" />
            </div>

            <!-- Cost Center Name (English) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Cost Center Name (English)') }}</label>
                <input type="text" name="name_en" id="add_name_en" class="form-control" placeholder="e.g. Administration & Finance" dir="ltr" />
            </div>

            <!-- Parent Cost Center (Hierarchical Tree) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Parent Cost Center') }}</label>
                <select name="parent_id" id="add_parent_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Main Center (No Parent)') }}" data-allow-clear="true">
                    <option value="">{{ __('Main Center (No Parent)') }}</option>
                </select>
                <small class="text-muted d-block mt-1">{{ __('Must belong to the same accounting entity.') }}</small>
            </div>

            <!-- Branch Selection (Optional) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Branch') }}</label>
                <select name="branch_id" id="add_branch_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('All Branches / General') }}" data-allow-clear="true">
                    <option value="">{{ __('All Branches / General') }}</option>
                </select>
                <small class="text-muted d-block mt-1">{{ __('Assign this center to a specific branch within the organization.') }}</small>
            </div>

            <!-- Status -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Status') }} <span class="text-danger">*</span></label>
                <select name="status" id="add_status" class="form-select select2" data-control="select2" required>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" @selected($status->value === 'active')>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Description (Arabic) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (Arabic)') }}</label>
                <textarea name="description_ar" id="add_description_ar" class="form-control" rows="2" placeholder="{{ __('Description (Arabic)') }}" dir="rtl"></textarea>
            </div>

            <!-- Description (English) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (English)') }}</label>
                <textarea name="description_en" id="add_description_en" class="form-control" rows="2" placeholder="{{ __('Description (English)') }}" dir="ltr"></textarea>
            </div>

            <!-- Footer Buttons -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-4" id="btnSubmitAdd">
                    <i class="icon-base bx bx-save font-18"></i>
                    <span>{{ __('Save') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit Cost Center                                            -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditCostCenter" aria-labelledby="offcanvasEditCostCenterLabel" style="width: 540px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasEditCostCenterLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit text-info font-22"></i>
            <span>{{ __('Edit Cost Center') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form action="" method="POST" id="editCostCenterForm">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_cost_center_id" name="id" />

            <!-- Entity Selection -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Accounting Entity') }} <span class="text-danger">*</span></label>
                <select name="entity_id" id="edit_entity_id" class="form-select select2" data-control="select2" required>
                    @foreach ($entities as $entity)
                        <option value="{{ $entity->id }}">
                            {{ $entity->translate(app()->getLocale())?->name ?? $entity->translate('ar')?->name ?? $entity->code }} ({{ $entity->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Code & Type Row -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label required fw-semibold text-heading">{{ __('Cost Center Code') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="icon-base bx bx-barcode"></i></span>
                        <input type="text" name="code" id="edit_code" class="form-control" required />
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label required fw-semibold text-heading">{{ __('Type') }} <span class="text-danger">*</span></label>
                    <select name="type" id="edit_type" class="form-select select2" data-control="select2" required>
                        @foreach($types as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Cost Center Name (Arabic) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Cost Center Name (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="edit_name_ar" class="form-control" required dir="rtl" />
            </div>

            <!-- Cost Center Name (English) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Cost Center Name (English)') }}</label>
                <input type="text" name="name_en" id="edit_name_en" class="form-control" dir="ltr" />
            </div>

            <!-- Parent Cost Center (Hierarchical Tree) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Parent Cost Center') }}</label>
                <select name="parent_id" id="edit_parent_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Main Center (No Parent)') }}" data-allow-clear="true">
                    <option value="">{{ __('Main Center (No Parent)') }}</option>
                </select>
                <small class="text-muted d-block mt-1">{{ __('Must belong to the same accounting entity.') }}</small>
            </div>

            <!-- Branch Selection (Optional) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Branch') }}</label>
                <select name="branch_id" id="edit_branch_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('All Branches / General') }}" data-allow-clear="true">
                    <option value="">{{ __('All Branches / General') }}</option>
                </select>
                <small class="text-muted d-block mt-1">{{ __('Assign this center to a specific branch within the organization.') }}</small>
            </div>

            <!-- Status -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Status') }} <span class="text-danger">*</span></label>
                <select name="status" id="edit_status" class="form-select select2" data-control="select2" required>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Description (Arabic) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (Arabic)') }}</label>
                <textarea name="description_ar" id="edit_description_ar" class="form-control" rows="2" dir="rtl"></textarea>
            </div>

            <!-- Description (English) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (English)') }}</label>
                <textarea name="description_en" id="edit_description_en" class="form-control" rows="2" dir="ltr"></textarea>
            </div>

            <!-- Footer Buttons -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-4" id="btnSubmitEdit">
                    <i class="icon-base bx bx-save font-18"></i>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 3. Modal: Delete Confirmation                                             -->
<!-- ========================================================================= -->
<div class="modal fade" id="deleteCostCenterModal" tabindex="-1" aria-labelledby="deleteCostCenterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form id="deleteCostCenterForm" method="POST" class="modal-content">
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
                <button type="submit" class="btn btn-danger" id="btnConfirmDelete">{{ __('Yes, Delete') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;

    // URLs Configuration
    const dataUrl = "{{ route('accounting.cost-centers.data') }}";
    const baseUrl = "{{ url('accounting/cost-centers') }}";
    const parentsUrl = "{{ route('accounting.cost-centers.parents') }}";
    const branchesUrl = "{{ route('accounting.cost-centers.branches') }}";

    // 1. Initialize Universal Sneat Bootstrap-5 Select2
    function setupSelect2(container = document) {
        if (!$) return;

        const isRtl = $('html').attr('dir') === 'rtl' || $('html').attr('lang') === 'ar';
        const noResultsText = isRtl ? 'لا توجد نتائج مطابقة' : 'No results found';
        const searchingText = isRtl ? 'جاري البحث...' : 'Searching...';

        if (typeof window.initSelect2 === 'function') {
            window.initSelect2(container);
        } else if ($.fn.select2) {
            $(container).find('.select2, [data-control="select2"]').each(function () {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2({
                        theme: 'bootstrap-5',
                        placeholder: $(this).data('placeholder') || '',
                        allowClear: $(this).data('allow-clear') !== false,
                        dir: isRtl ? 'rtl' : 'ltr',
                        width: '100%',
                        dropdownParent: $(this).closest('.modal, .offcanvas').length ? $(this).closest('.modal, .offcanvas') : null,
                        language: {
                            noResults: () => noResultsText,
                            searching: () => searchingText
                        }
                    });
                }
            });
        }
    }

    setupSelect2();

    // 2. Alert notifications helper
    function showAlert(type, message) {
        const iconClass = type === 'success' ? 'bx-check-circle' : 'bx-error-circle';
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible d-flex align-items-center mb-4" role="alert">
                <i class="icon-base bx ${iconClass} me-2 font-20"></i>
                <div>${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('#alertContainer').html(alertHtml);
        $('html, body').animate({ scrollTop: 0 }, 200);
    }

    // 3. Load dynamic parents and branches dropdowns for an entity
    function loadEntityHierarchy(entityId, parentSelectSelector, branchSelectSelector, selectedParentId = null, selectedBranchId = null, excludeId = null) {
        if (!entityId) {
            $(parentSelectSelector).html('<option value="">' + "{{ __('Main Center (No Parent)') }}" + '</option>').trigger('change');
            $(branchSelectSelector).html('<option value="">' + "{{ __('All Branches / General') }}" + '</option>').trigger('change');
            return;
        }

        // Fetch Parents
        $.ajax({
            url: parentsUrl,
            data: { entity_id: entityId, exclude_id: excludeId },
            success: function (res) {
                let options = '<option value="">' + "{{ __('Main Center (No Parent)') }}" + '</option>';
                if (res.data && res.data.length > 0) {
                    res.data.forEach(function (item) {
                        const isSelected = selectedParentId && parseInt(selectedParentId) === parseInt(item.id) ? 'selected' : '';
                        options += `<option value="${item.id}" ${isSelected}>${item.name}</option>`;
                    });
                }
                $(parentSelectSelector).html(options).trigger('change');
            }
        });

        // Fetch Branches
        $.ajax({
            url: branchesUrl,
            data: { entity_id: entityId },
            success: function (res) {
                let options = '<option value="">' + "{{ __('All Branches / General') }}" + '</option>';
                if (res.data && res.data.length > 0) {
                    res.data.forEach(function (item) {
                        const isSelected = selectedBranchId && parseInt(selectedBranchId) === parseInt(item.id) ? 'selected' : '';
                        options += `<option value="${item.id}" ${isSelected}>${item.name}</option>`;
                    });
                }
                $(branchSelectSelector).html(options).trigger('change');
            }
        });
    }

    // Trigger hierarchy load on Add form entity change
    $('#add_entity_id').on('change', function () {
        loadEntityHierarchy($(this).val(), '#add_parent_id', '#add_branch_id');
    });

    // Trigger hierarchy load on Edit form entity change
    $('#edit_entity_id').on('change', function () {
        const excludeId = $('#edit_cost_center_id').val();
        loadEntityHierarchy($(this).val(), '#edit_parent_id', '#edit_branch_id', null, null, excludeId);
    });

    // 4. Initialize Server-Side DataTable
    const table = $('#costCentersTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pagingType: 'simple_numbers',
        ajax: {
            url: dataUrl,
            data: function (d) {
                d.entity_id = $('#filter_entity_id').val();
                d.type = $('#filter_type').val();
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
                data: 'cost_center_info',
                name: 'cost_center_info',
                className: 'align-middle',
                render: (data, type, row) => data
            },
            {
                data: 'entity_info',
                name: 'accounting_cost_centers.entity_id',
                orderable: false,
                searchable: false,
                className: 'align-middle',
                render: (data, type, row) => data
            },
            {
                data: 'type_badge',
                name: 'accounting_cost_centers.type',
                className: 'text-center align-middle',
                render: (data, type, row) => data
            },
            {
                data: 'parent_info',
                name: 'accounting_cost_centers.parent_id',
                orderable: false,
                searchable: false,
                className: 'align-middle',
                render: (data, type, row) => data
            },
            {
                data: 'branch_info',
                name: 'accounting_cost_centers.branch_id',
                orderable: false,
                searchable: false,
                className: 'align-middle',
                render: (data, type, row) => data
            },
            {
                data: 'status_badge',
                name: 'accounting_cost_centers.status',
                className: 'text-center align-middle',
                render: (data, type, row) => data
            },
            {
                data: 'created_at',
                name: 'accounting_cost_centers.created_at',
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
        order: [[1, 'asc']],
        language: {
            search: "",
            searchPlaceholder: "{{ __('Search...') }}",
            lengthMenu: "_MENU_",
            paginate: {
                previous: '<i class="bx bx-chevron-left"></i>',
                next: '<i class="bx bx-chevron-right"></i>'
            }
        },
        drawCallback: function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('#costCentersTable [data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });

    // Custom Filters Trigger Reload with debounce / reset guard
    let isResetting = false;

    $('#filter_entity_id, #filter_type, #filter_status').on('change', function () {
        if (isResetting) return;
        table.ajax.reload();
    });

    // Reset filters
    $('#btnResetFilters').on('click', function () {
        isResetting = true;
        $('#filter_entity_id, #filter_type, #filter_status').val('').trigger('change');
        isResetting = false;
        table.ajax.reload();
    });

    // 5. Handle Offcanvas initializations
    const addOffcanvasEl = document.getElementById('offcanvasAddCostCenter');
    if (addOffcanvasEl) {
        addOffcanvasEl.addEventListener('shown.bs.offcanvas', function () {
            setupSelect2(addOffcanvasEl);
            const currentEntityId = $('#add_entity_id').val();
            if (currentEntityId && $('#add_parent_id option').length <= 1) {
                loadEntityHierarchy(currentEntityId, '#add_parent_id', '#add_branch_id');
            }
        });
    }

    const editOffcanvasEl = document.getElementById('offcanvasEditCostCenter');
    const editOffcanvas = editOffcanvasEl ? bootstrap.Offcanvas.getOrCreateInstance(editOffcanvasEl) : null;

    if (editOffcanvasEl) {
        editOffcanvasEl.addEventListener('shown.bs.offcanvas', function () {
            setupSelect2(editOffcanvasEl);
        });
    }

    // 6. Submit Add Cost Center Form via AJAX
    $('#addCostCenterForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#btnSubmitAdd');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> ' + "{{ __('Saving...') }}");

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                const addOffcanvasInstance = bootstrap.Offcanvas.getInstance(addOffcanvasEl);
                if (addOffcanvasInstance) addOffcanvasInstance.hide();
                form[0].reset();
                $('#add_parent_id, #add_branch_id').val('').trigger('change');
                table.ajax.reload();
                showAlert('success', res.message || "{{ __('Cost center created successfully.') }}");
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
                submitBtn.prop('disabled', false).html('<i class="icon-base bx bx-save font-18"></i> <span>' + "{{ __('Save') }}" + '</span>');
            }
        });
    });

    // 7. Load Data for Edit Offcanvas
    $(document).on('click', '.btn-edit-cost-center', function () {
        const id = $(this).data('id');
        const url = baseUrl + '/' + id;

        $.ajax({
            url: url,
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                if (!res.success || !res.data) return;
                const d = res.data;

                $('#edit_cost_center_id').val(d.id);
                $('#editCostCenterForm').attr('action', baseUrl + '/' + d.id);
                $('#edit_entity_id').val(d.entity_id).trigger('change');
                $('#edit_code').val(d.code);
                $('#edit_type').val(d.type).trigger('change');
                $('#edit_status').val(d.status).trigger('change');
                $('#edit_name_ar').val(d.name_ar);
                $('#edit_name_en').val(d.name_en);
                $('#edit_description_ar').val(d.description_ar);
                $('#edit_description_en').val(d.description_en);

                // Load hierarchy with preselection
                loadEntityHierarchy(d.entity_id, '#edit_parent_id', '#edit_branch_id', d.parent_id, d.branch_id, d.id);

                if (editOffcanvas) editOffcanvas.show();
            },
            error: function () {
                alert("{{ __('Failed to load cost center details.') }}");
            }
        });
    });

    // 8. Submit Edit Cost Center Form via AJAX
    $('#editCostCenterForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#btnSubmitEdit');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> ' + "{{ __('Saving...') }}");

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                if (editOffcanvas) editOffcanvas.hide();
                table.ajax.reload();
                showAlert('success', res.message || "{{ __('Cost center updated successfully.') }}");
            },
            error: function (xhr) {
                let errorMsg = "{{ __('An error occurred while updating.') }}";
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
                submitBtn.prop('disabled', false).html('<i class="icon-base bx bx-save font-18"></i> <span>' + "{{ __('Save Changes') }}" + '</span>');
            }
        });
    });

    // 9. Toggle Status (Active/Inactive)
    $(document).on('click', '.btn-toggle-status', function () {
        const id = $(this).data('id');
        const status = $(this).data('status');
        const actionText = status === 'active'
            ? "{{ __('Are you sure you want to deactivate this cost center?') }}"
            : "{{ __('Are you sure you want to activate this cost center?') }}";

        if (!confirm(actionText)) return;

        $.ajax({
            url: baseUrl + '/' + id + '/toggle-status',
            method: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                table.ajax.reload();
                showAlert('success', res.message || "{{ __('Status updated successfully.') }}");
            },
            error: function (xhr) {
                alert("{{ __('Failed to change status.') }}");
            }
        });
    });

    // 10. Delete Cost Center
    const deleteModalEl = document.getElementById('deleteCostCenterModal');
    const deleteModal = deleteModalEl ? bootstrap.Modal.getOrCreateInstance(deleteModalEl) : null;
    const deleteForm = $('#deleteCostCenterForm');

    $(document).on('click', '.btn-delete-cost-center', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');

        deleteForm.attr('action', baseUrl + '/' + id);
        $('#delete_target_name').text(name);

        if (deleteModal) deleteModal.show();
    });

    deleteForm.on('submit', function (e) {
        e.preventDefault();
        const submitBtn = $('#btnConfirmDelete');
        submitBtn.prop('disabled', true);

        $.ajax({
            url: deleteForm.attr('action'),
            method: 'POST',
            data: deleteForm.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                if (deleteModal) deleteModal.hide();
                table.ajax.reload();
                showAlert('success', res.message || "{{ __('Cost center deleted successfully.') }}");
            },
            error: function (xhr) {
                let errorMsg = "{{ __('Failed to delete cost center.') }}";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            },
            complete: function () {
                submitBtn.prop('disabled', false);
            }
        });
    });
});
</script>
@endsection
