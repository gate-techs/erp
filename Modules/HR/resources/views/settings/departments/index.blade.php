@extends('layouts/contentNavbarLayout')

@section('title', __('Departments') . ' - ' . config('variables.templateName'))

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
                    <i class="icon-base bx bx-sitemap text-primary"></i>
                    {{ __('Departments') }}
                </h5>
            </div>

            <div>
                <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm w-100 w-sm-auto" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddDepartment" aria-controls="offcanvasAddDepartment">
                    <i class="icon-base bx bx-plus font-18"></i>
                    <span>{{ __('Add New Department') }}</span>
                </button>
            </div>
        </div>

        <!-- Departments Server-Side DataTable Card -->
        <div class="card shadow-sm border-0">
            <div class="card-datatable">
                <table id="departmentsTable" class="table table-hover border-top w-100 text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>{{ __('Arabic Name') }}</th>
                            <th>{{ __('English Name') }}</th>
                            <th>{{ __('Parent Administration') }}</th>
                            <th>{{ __('Branch') }}</th>
                            <th>{{ __('Code / Symbol') }}</th>
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
<!-- 1. Offcanvas: Add New Department                                          -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddDepartment" aria-labelledby="offcanvasAddDepartmentLabel" style="width: 480px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddDepartmentLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-sitemap text-primary"></i>
            <span>{{ __('Add New Department') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form action="{{ route('hr.settings.departments.store') }}" method="POST" id="addDepartmentForm">
            @csrf

            <!-- Parent Administration Selection -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Parent Administration') }} <span class="text-danger">*</span></label>
                <select class="select2 form-select" id="add_parent_id" name="parent_id" data-placeholder="{{ __('Select Administration') }}" required>
                    <option value="" selected disabled>{{ __('Select Administration') }}</option>
                    @foreach($administrations as $admin)
                        <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Department Name [Arabic] -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Department Name [Arabic]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" class="form-control" placeholder="{{ __('e.g. Personnel Affairs') }}" required dir="rtl">
            </div>

            <!-- Department Name [English] -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Department Name [English]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" class="form-control" placeholder="{{ __('e.g. Personnel Affairs') }}" required dir="ltr">
            </div>

            <!-- Branch Selection (Nullable) -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Branch') }} <small class="text-muted">({{ __('Optional - Leave empty for All Branches') }})</small></label>
                <select class="select2 form-select" name="branch_id" id="add_dept_branch_id" data-placeholder="{{ __('All Branches (Central)') }}" data-allow-clear="true">
                    <option value="">{{ __('All Branches (Central)') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Code -->
            <div class="mb-4">
                <label class="form-label fw-semibold">{{ __('Code / Symbol') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                <input type="text" name="code" class="form-control" placeholder="{{ __('e.g. HR-PERS') }}" dir="ltr">
            </div>

            <!-- Footer Buttons -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-4">
                    <i class="icon-base bx bx-save"></i>
                    <span>{{ __('Save') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit Department                                             -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditDepartment" aria-labelledby="offcanvasEditDepartmentLabel" style="width: 480px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasEditDepartmentLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit-alt text-primary"></i>
            <span>{{ __('Edit Department') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="editDepartmentForm" method="POST">
            @csrf
            @method('PUT')

            <!-- Parent Administration Selection -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Parent Administration') }} <span class="text-danger">*</span></label>
                <select class="select2 form-select" id="edit_parent_id" name="parent_id" data-placeholder="{{ __('Select Administration') }}" required>
                    <option value="" disabled>{{ __('Select Administration') }}</option>
                    @foreach($administrations as $admin)
                        <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Department Name [Arabic] -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Department Name [Arabic]') }} <span class="text-danger">*</span></label>
                <input type="text" id="edit_name_ar" name="name_ar" class="form-control" required dir="rtl">
            </div>

            <!-- Department Name [English] -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Department Name [English]') }} <span class="text-danger">*</span></label>
                <input type="text" id="edit_name_en" name="name_en" class="form-control" required dir="ltr">
            </div>

            <!-- Branch Selection (Nullable) -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Branch') }} <small class="text-muted">({{ __('Optional - Leave empty for All Branches') }})</small></label>
                <select class="select2 form-select" name="branch_id" id="edit_dept_branch_id" data-placeholder="{{ __('All Branches (Central)') }}" data-allow-clear="true">
                    <option value="">{{ __('All Branches (Central)') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Code -->
            <div class="mb-4">
                <label class="form-label fw-semibold">{{ __('Code / Symbol') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                <input type="text" id="edit_code" name="code" class="form-control" dir="ltr">
            </div>

            <!-- Footer Buttons -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-4">
                    <i class="icon-base bx bx-save"></i>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 3. Modal: Delete Department                                               -->
<!-- ========================================================================= -->
<div class="modal fade" id="deleteDepartmentModal" tabindex="-1" aria-labelledby="deleteDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form id="deleteDepartmentForm" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-body text-center py-4">
                <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-trash font-32 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Confirm Delete') }}</h5>
                <p class="text-muted mb-0">
                    {{ __('Are you sure you want to delete the department:') }}
                    <br>
                    <strong id="delete_target_name" class="text-danger"></strong>
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
window.addEventListener('load', function () {
    const $ = window.jQuery || window.$;
    if (!$ || !$.fn.DataTable) {
        console.error('jQuery or DataTables not loaded');
        return;
    }

    const dataUrl = "{{ route('hr.settings.departments.data') }}";
    const baseUrl = "{{ url('hr/settings/departments') }}";

    // Initialize Server-Side DataTable exactly like governments
    const table = $('#departmentsTable').DataTable({
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
                render: data => `<span class="fw-semibold text-heading">${data}</span>`
            },
            { 
                data: 'name_en', 
                name: 'name_en',
                render: data => `<span class="text-body">${data}</span>`
            },
            { 
                data: 'parent_name', 
                name: 'parent_id',
                render: data => `<span class="badge bg-label-primary">${data}</span>`
            },
            { 
                data: 'branch_name', 
                name: 'branch_id',
                render: data => `<span class="badge bg-label-info">${data}</span>`
            },
            { 
                data: 'code', 
                name: 'code',
                render: data => data !== '-'
                    ? `<span class="badge bg-label-secondary font-monospace">${data}</span>`
                    : `<span class="text-muted">-</span>`
            },
            { 
                data: 'created_at', 
                name: 'created_at',
                render: data => `<small class="text-muted">${data}</small>`
            },
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                className: 'text-center',
                render: (data, type, row) => `
                    <div class="d-inline-flex gap-1">
                        <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-dept"
                                title="{{ __('Edit') }}"
                                data-id="${row.id}"
                                data-name-ar="${row.name_ar}"
                                data-name-en="${row.name_en}"
                                data-code="${row.code !== '-' ? row.code : ''}"
                                data-parent-id="${row.parent_id || ''}"
                                data-branch-id="${row.branch_id || ''}">
                            <i class="icon-base bx bx-edit-alt"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-dept"
                                title="{{ __('Delete') }}"
                                data-id="${row.id}"
                                data-name="${row.name_ar}">
                            <i class="icon-base bx bx-trash"></i>
                        </button>
                    </div>`
            }
        ],
        order: [[0, 'asc']]
    });

    // Handle Edit Drawer Population
    const editOffcanvasEl = document.getElementById('offcanvasEditDepartment');
    const editForm = document.getElementById('editDepartmentForm');
    const inputNameAr = document.getElementById('edit_name_ar');
    const inputNameEn = document.getElementById('edit_name_en');
    const inputCode = document.getElementById('edit_code');
    const selectParent = document.getElementById('edit_parent_id');
    const selectBranch = document.getElementById('edit_dept_branch_id');

    $(document).on('click', '.btn-edit-dept', function () {
        const id = $(this).data('id');
        editForm.action = baseUrl + '/' + id;
        inputNameAr.value = $(this).data('name-ar') || '';
        inputNameEn.value = $(this).data('name-en') || '';
        inputCode.value = $(this).data('code') || '';
        $(selectParent).val($(this).data('parent-id') || '').trigger('change');
        $(selectBranch).val($(this).data('branch-id') || '').trigger('change');

        if (editOffcanvasEl) {
            const editOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(editOffcanvasEl);
            editOffcanvas.show();
        }
    });

    // Handle Delete Modal Population
    const deleteModalEl = document.getElementById('deleteDepartmentModal');
    const deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;
    const deleteForm = document.getElementById('deleteDepartmentForm');
    const deleteTargetName = document.getElementById('delete_target_name');

    $(document).on('click', '.btn-delete-dept', function () {
        const id = $(this).data('id');
        deleteForm.action = baseUrl + '/' + id;
        deleteTargetName.textContent = $(this).data('name') || '';
        if (deleteModal) deleteModal.show();
    });
});
</script>
@endsection
