@extends('layouts/contentNavbarLayout')

@section('title', __('Reward & Incentive Types') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Header -->
<div class="mb-4">
    <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
        <i class="icon-base bx bx-slider-alt text-primary font-22"></i>
        {{ __('HR Settings') }}
    </h4>
</div>

<!-- 2-Column Layout with Sub-Sidebar -->
<div class="row g-4">
    <!-- Sub-Sidebar Column -->
    <div class="col-12 col-md-4 col-lg-3">
        @include('hr::settings.partials.sidebar')
    </div>

    <!-- Main Content Column -->
    <div class="col-12 col-md-8 col-lg-9">
        <!-- Action Bar: Title & Add Button -->
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-3">
            <div>
                <h5 class="fw-bold mb-1 d-flex align-items-center gap-2 text-heading">
                    <i class="icon-base bx bx-gift text-primary"></i>
                    {{ __('Reward & Incentive Types') }}
                </h5>
                <p class="text-muted mb-0 small">{{ __('Manage employee reward, bonus, and recognition categories.') }}</p>
            </div>

            <div>
                <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm w-100 w-sm-auto" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddRewardType" aria-controls="offcanvasAddRewardType">
                    <i class="icon-base bx bx-plus font-18"></i>
                    <span>{{ __('Add New Reward Type') }}</span>
                </button>
            </div>
        </div>

        <!-- Reward Types Server-Side DataTable Card -->
        <div class="card shadow-sm border-0">
            <div class="card-datatable">
                <table id="rewardTypesTable" class="table table-hover border-top w-100 text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>{{ __('Arabic Name') }}</th>
                            <th>{{ __('English Name') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Created Date') }}</th>
                            <th class="text-center" style="width: 120px;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loaded via DataTables Server-Side -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 1. Offcanvas: Add New Reward Type                                         -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddRewardType" aria-labelledby="offcanvasAddRewardTypeLabel" style="width: 480px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddRewardTypeLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-gift text-primary"></i>
            <span>{{ __('Add New Reward Type') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form action="{{ route('hr.settings.reward-types.store') }}" method="POST" id="addRewardTypeForm">
            @csrf
            <input type="hidden" name="type" value="reward-types">

            <!-- Parent Category (Optional) -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Parent Category') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                <select class="form-select select2" name="parent_id" id="add_parent_id" data-placeholder="{{ __('Select Parent Category') }}" data-allow-clear="true">
                    <option value="">{{ __('None (Main Category)') }}</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Arabic Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Reward Type Name [Arabic]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" class="form-control" placeholder="{{ __('e.g. مكافأة تميز / حافز إنتاجية / عيدية') }}" required dir="rtl">
            </div>

            <!-- English Name -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Reward Type Name [English]') }}</label>
                <input type="text" name="name_en" class="form-control" placeholder="{{ __('e.g. Performance Bonus / Holiday Grant') }}" dir="ltr">
            </div>

            <!-- Status -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Status') }} <span class="text-danger">*</span></label>
                <select class="form-select" name="status" required>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" {{ $status->value === 'active' ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Arabic Description -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Description [Arabic]') }}</label>
                <textarea name="description_ar" class="form-control" rows="3" placeholder="{{ __('Description or criteria for awarding...') }}" dir="rtl"></textarea>
            </div>

            <!-- English Description -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Description [English]') }}</label>
                <textarea name="description_en" class="form-control" rows="3" placeholder="{{ __('English criteria or description...') }}" dir="ltr"></textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-check"></i>
                    <span>{{ __('Save Reward Type') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit Reward Type                                            -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditRewardType" aria-labelledby="offcanvasEditRewardTypeLabel" style="width: 480px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasEditRewardTypeLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit text-primary"></i>
            <span>{{ __('Edit Reward Type') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form action="" method="POST" id="editRewardTypeForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="type" value="reward-types">
            <input type="hidden" name="id" id="edit_id">

            <!-- Parent Category (Optional) -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Parent Category') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                <select class="form-select select2" name="parent_id" id="edit_parent_id" data-placeholder="{{ __('Select Parent Category') }}" data-allow-clear="true">
                    <option value="">{{ __('None (Main Category)') }}</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Arabic Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Reward Type Name [Arabic]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="edit_name_ar" class="form-control" required dir="rtl">
            </div>

            <!-- English Name -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Reward Type Name [English]') }}</label>
                <input type="text" name="name_en" id="edit_name_en" class="form-control" dir="ltr">
            </div>

            <!-- Status -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Status') }} <span class="text-danger">*</span></label>
                <select class="form-select" name="status" id="edit_status" required>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Arabic Description -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Description [Arabic]') }}</label>
                <textarea name="description_ar" id="edit_description_ar" class="form-control" rows="3" dir="rtl"></textarea>
            </div>

            <!-- English Description -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Description [English]') }}</label>
                <textarea name="description_en" id="edit_description_en" class="form-control" rows="3" dir="ltr"></textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-save"></i>
                    <span>{{ __('Update Reward Type') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 3. Modal: Delete Confirmation                                             -->
<!-- ========================================================================= -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form action="" method="POST" id="deleteRewardTypeForm" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <i class="icon-base bx bx-error-circle" style="font-size: 3.5rem;"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Delete Reward Type?') }}</h5>
                <p class="text-muted small mb-0">
                    {{ __('Are you sure you want to delete:') }}
                    <span id="deleteItemName" class="fw-bold text-heading"></span>
                </p>
                <p class="text-muted small mt-1">{{ __('This action will remove this reward type.') }}</p>
            </div>
            <div class="modal-footer border-top justify-content-center gap-2">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-danger">{{ __('Yes, Delete') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-script')
<script>
window.addEventListener('load', function () {
    const $ = window.jQuery || window.$;
    if (!$ || !$.fn.DataTable) {
        console.error('jQuery or DataTables not loaded');
        return;
    }

    const dataUrl = "{{ route('hr.settings.reward-types.data') }}";
    const baseUrl = "{{ url('hr/settings/reward-types') }}";

    // 1. Initialize Server-Side DataTable
    const table = $('#rewardTypesTable').DataTable({
        processing: true,
        serverSide: true,
        pagingType: 'simple_numbers',
        ajax: dataUrl,
        columns: [
            { 
                data: 'DT_RowIndex', 
                name: 'DT_RowIndex',
                orderable: false, 
                searchable: false,
                render: (data, type, row, meta) => `<strong class="text-muted">#${data || (meta.row + 1 + (meta.settings._iDisplayStart || 0))}</strong>`
            },
            { 
                data: 'name_ar', 
                name: 'name_ar',
                render: data => `<span class="fw-semibold text-heading">${data || '-'}</span>`
            },
            { 
                data: 'name_en', 
                name: 'name_en',
                render: data => `<span class="text-body">${data || '-'}</span>`
            },
            { 
                data: 'description_ar', 
                name: 'description_ar',
                render: (data, type, row) => `<span class="text-muted small">${data || row.description_en || '-'}</span>`
            },
            { 
                data: 'status', 
                name: 'status'
            },
            { 
                data: 'created_at', 
                name: 'created_at',
                render: data => `<small class="text-muted">${data || '-'}</small>`
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: (data, type, row) => {
                    const rowJson = encodeURIComponent(JSON.stringify(row));
                    return `
                        <div class="d-inline-flex gap-1">
                            <button type="button" class="btn btn-icon btn-sm btn-outline-primary edit-reward-btn" data-row="${rowJson}" title="{{ __('Edit') }}">
                                <i class="icon-base bx bx-edit-alt"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-sm btn-outline-danger delete-reward-btn" data-id="${row.id}" data-name="${row.name_ar || row.name_en}" title="{{ __('Delete') }}">
                                <i class="icon-base bx bx-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[0, 'asc']]
    });

    // 2. Edit Action: Populate & Open Offcanvas
    const editOffcanvasEl = document.getElementById('offcanvasEditRewardType');
    const editForm = document.getElementById('editRewardTypeForm');

    $(document).on('click', '.edit-reward-btn', function () {
        const row = JSON.parse(decodeURIComponent($(this).data('row')));

        editForm.action = baseUrl + '/' + row.id;
        $('#edit_id').val(row.id);
        $('#edit_name_ar').val(row.name_ar || '');
        $('#edit_name_en').val(row.name_en || '');
        $('#edit_description_ar').val(row.description_ar || '');
        $('#edit_description_en').val(row.description_en || '');
        $('#edit_status').val(row.status_raw || row.status).trigger('change');
        $('#edit_parent_id').val(row.parent_id || '').trigger('change');

        // Prevent choosing self as parent
        $('#edit_parent_id option').prop('disabled', false);
        $('#edit_parent_id option[value="' + row.id + '"]').prop('disabled', true);
        $('#edit_parent_id').trigger('change');

        if (editOffcanvasEl) {
            const editOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(editOffcanvasEl);
            editOffcanvas.show();
        }
    });

    // Initialize Select2 in page and offcanvases
    function setupSelect2(container = document) {
        if (typeof window.initSelect2 === 'function') {
            window.initSelect2(container);
        } else if ($.fn.select2) {
            $(container).find('.select2').each(function () {
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
    }

    setupSelect2();

    $(document).on('shown.bs.offcanvas', function (e) {
        setupSelect2(e.target);
    });

    // 3. Delete Action: Confirmation Modal
    const deleteModalEl = document.getElementById('deleteConfirmModal');
    const deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;
    const deleteForm = document.getElementById('deleteRewardTypeForm');
    const deleteTargetName = document.getElementById('deleteItemName');

    $(document).on('click', '.delete-reward-btn', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');

        deleteForm.action = baseUrl + '/' + id;
        deleteTargetName.textContent = name || '';

        if (deleteModal) {
            deleteModal.show();
        }
    });
});
</script>
@endsection
