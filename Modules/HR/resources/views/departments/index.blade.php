@extends('layouts/contentNavbarLayout')

@section('title', __('Departments & Administrations') . ' - ' . config('variables.templateName'))

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
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-sitemap text-primary font-24"></i>
            <span>{{ __('Departments & Administrations') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item">{{ __('Human Resources') }}</li>
                <li class="breadcrumb-item active">{{ __('Departments & Administrations') }}</li>
            </ol>
        </nav>
    </div>

    <div>
        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddDepartment" aria-controls="offcanvasAddDepartment">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add New Department') }}</span>
        </button>
    </div>
</div>

<!-- Alert Notifications -->
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

<!-- Filters Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Type Filter -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Structure Level') }}</label>
                <select id="filter_type" class="select2 form-select" data-placeholder="{{ __('All Levels') }}" data-allow-clear="true">
                    <option value="">{{ __('All Levels') }}</option>
                    <option value="main">{{ __('Main Administrations') }}</option>
                    <option value="sub">{{ __('Sub Departments') }}</option>
                </select>
            </div>

            <!-- 2. Parent Administration Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Parent Administration') }}</label>
                <select id="filter_parent_id" class="select2 form-select" data-placeholder="{{ __('All Administrations') }}" data-allow-clear="true">
                    <option value="">{{ __('All Administrations') }}</option>
                    @foreach ($parentDepartments as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Nursery Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Nursery') }}</label>
                <select id="filter_nursery_id" class="select2 form-select" data-placeholder="{{ __('All Nurseries') }}" data-allow-clear="true">
                    <option value="">{{ __('All Nurseries') }}</option>
                    @foreach ($nurseries as $nursery)
                        <option value="{{ $nursery->id }}">{{ $nursery->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Branch Filter -->
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                <select id="filter_branch_id" class="select2 form-select" data-placeholder="{{ __('All Branches') }}" data-allow-clear="true">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 5. Status Filter -->
            <div class="col-12 col-sm-6 col-lg-2">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <label class="form-label fw-semibold text-heading small mb-0">{{ __('Status') }}</label>
                    <button type="button" id="btnResetFilters" class="btn btn-link btn-xs text-muted p-0 text-decoration-none" title="{{ __('Reset Filters') }}">
                        <i class="icon-base bx bx-reset me-1"></i>{{ __('Reset') }}
                    </button>
                </div>
                <select id="filter_status" class="select2 form-select" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Departments DataTable Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="departmentsTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Code') }}</th>
                    <th>{{ __('Name') }}</th>
                    <th class="text-center">{{ __('Level') }}</th>
                    <th>{{ __('Parent Administration') }}</th>
                    <th>{{ __('Nursery / Branch') }}</th>
                    <th>{{ __('Manager') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Created Date') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- ========================================== -->
<!-- 1. Offcanvas: Add Department               -->
<!-- ========================================== -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddDepartment" aria-labelledby="offcanvasAddDepartmentLabel" style="width: 500px;">
    <div class="offcanvas-header border-bottom">
        <h5 id="offcanvasAddDepartmentLabel" class="offcanvas-title d-flex align-items-center gap-2">
            <i class="icon-base bx bx-plus-circle text-primary"></i>
            <span>{{ __('Add New Department / Administration') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body mx-0 flex-grow-0 p-4">
        <form action="{{ route('hr.departments.store') }}" method="POST" id="addDepartmentForm">
            @csrf

            <!-- Nav Tabs: General / Translation -->
            <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#addTabAr" aria-controls="addTabAr" aria-selected="true">
                        <i class="icon-base bx bx-globe me-1"></i> {{ __('Arabic Info') }} <span class="text-danger">*</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#addTabEn" aria-controls="addTabEn" aria-selected="false">
                        <i class="icon-base bx bx-globe me-1"></i> {{ __('English Info') }} <span class="text-danger">*</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content border rounded p-3 mb-4">
                <!-- Tab Arabic -->
                <div class="tab-pane fade show active" id="addTabAr" role="tabpanel">
                    <div class="mb-3">
                        <label class="form-label required">{{ __('Department Name (Arabic)') }}</label>
                        <input type="text" name="name_ar" class="form-control" placeholder="مثال: الإدارة المالية أو قسم المحاسبة" required />
                    </div>
                    <div class="mb-0">
                        <label class="form-label">{{ __('Description (Arabic)') }}</label>
                        <textarea name="description_ar" class="form-control" rows="3" placeholder="وصف مهام الإدارة أو القسم"></textarea>
                    </div>
                </div>

                <!-- Tab English -->
                <div class="tab-pane fade" id="addTabEn" role="tabpanel">
                    <div class="mb-3">
                        <label class="form-label required">{{ __('Department Name (English)') }}</label>
                        <input type="text" name="name_en" class="form-control" placeholder="e.g. Finance Department" required />
                    </div>
                    <div class="mb-0">
                        <label class="form-label">{{ __('Description (English)') }}</label>
                        <textarea name="description_en" class="form-control" rows="3" placeholder="Description of the department duties"></textarea>
                    </div>
                </div>
            </div>

            <!-- Code -->
            <div class="mb-3">
                <label class="form-label">{{ __('Code') }}</label>
                <input type="text" name="code" class="form-control" placeholder="مثال: HR-01 أو FIN" />
                <small class="text-muted">{{ __('Optional unique code for organizational structure') }}</small>
            </div>

            <!-- Level / Parent Administration -->
            <div class="mb-3">
                <label class="form-label">{{ __('Parent Administration') }}</label>
                <select name="parent_id" id="add_parent_id" class="select2 form-select" data-placeholder="{{ __('Leave empty if Main Administration') }}" data-allow-clear="true">
                    <option value="">{{ __('Main Administration (No Parent)') }}</option>
                    @foreach ($parentDepartments as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                    @endforeach
                </select>
                <small class="text-muted">{{ __('Leave blank to create as a Top-Level Administration') }}</small>
            </div>

            <!-- Nursery Selection -->
            <div class="mb-3">
                <label class="form-label">{{ __('Nursery') }}</label>
                <select name="nursery_id" id="add_nursery_id" class="select2 form-select" data-placeholder="{{ __('General / All Nurseries') }}" data-allow-clear="true">
                    <option value="">{{ __('General / All Nurseries') }}</option>
                    @foreach ($nurseries as $nursery)
                        <option value="{{ $nursery->id }}">{{ $nursery->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Branch Selection (Dynamic) -->
            <div class="mb-3">
                <label class="form-label">{{ __('Specific Branch') }}</label>
                <select name="branch_id" id="add_branch_id" class="select2 form-select" data-placeholder="{{ __('All Branches / Central') }}" data-allow-clear="true">
                    <option value="">{{ __('All Branches / Central') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" data-nursery="{{ $branch->nursery_id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
                <small class="text-muted">{{ __('Select a branch if this department belongs only to one branch') }}</small>
            </div>

            <!-- Manager Selection -->
            <div class="mb-3">
                <label class="form-label">{{ __('Responsible Manager') }}</label>
                <select name="manager_id" id="add_manager_id" class="select2 form-select" data-placeholder="{{ __('Select Manager') }}" data-allow-clear="true">
                    <option value="">{{ __('None') }}</option>
                    @foreach ($managers as $mgr)
                        <option value="{{ $mgr->id }}">{{ $mgr->name }} ({{ $mgr->email }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Radio -->
            <div class="mb-4">
                <label class="form-label d-block">{{ __('Status') }}</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="addStatusActive" value="active" checked />
                    <label class="form-check-label text-success fw-medium" for="addStatusActive">{{ __('Active') }}</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="addStatusInactive" value="inactive" />
                    <label class="form-check-label text-danger fw-medium" for="addStatusInactive">{{ __('Inactive') }}</label>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. Offcanvas: Edit Department              -->
<!-- ========================================== -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditDepartment" aria-labelledby="offcanvasEditDepartmentLabel" style="width: 500px;">
    <div class="offcanvas-header border-bottom">
        <h5 id="offcanvasEditDepartmentLabel" class="offcanvas-title d-flex align-items-center gap-2">
            <i class="icon-base bx bx-edit text-warning"></i>
            <span>{{ __('Edit Department / Administration') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body mx-0 flex-grow-0 p-4">
        <form method="POST" id="editDepartmentForm">
            @csrf
            @method('PUT')

            <!-- Nav Tabs: General / Translation -->
            <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#editTabAr" aria-controls="editTabAr" aria-selected="true">
                        <i class="icon-base bx bx-globe me-1"></i> {{ __('Arabic Info') }} <span class="text-danger">*</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#editTabEn" aria-controls="editTabEn" aria-selected="false">
                        <i class="icon-base bx bx-globe me-1"></i> {{ __('English Info') }} <span class="text-danger">*</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content border rounded p-3 mb-4">
                <!-- Tab Arabic -->
                <div class="tab-pane fade show active" id="editTabAr" role="tabpanel">
                    <div class="mb-3">
                        <label class="form-label required">{{ __('Department Name (Arabic)') }}</label>
                        <input type="text" name="name_ar" id="edit_name_ar" class="form-control" required />
                    </div>
                    <div class="mb-0">
                        <label class="form-label">{{ __('Description (Arabic)') }}</label>
                        <textarea name="description_ar" id="edit_description_ar" class="form-control" rows="3"></textarea>
                    </div>
                </div>

                <!-- Tab English -->
                <div class="tab-pane fade" id="editTabEn" role="tabpanel">
                    <div class="mb-3">
                        <label class="form-label required">{{ __('Department Name (English)') }}</label>
                        <input type="text" name="name_en" id="edit_name_en" class="form-control" required />
                    </div>
                    <div class="mb-0">
                        <label class="form-label">{{ __('Description (English)') }}</label>
                        <textarea name="description_en" id="edit_description_en" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <!-- Code -->
            <div class="mb-3">
                <label class="form-label">{{ __('Code') }}</label>
                <input type="text" name="code" id="edit_code" class="form-control" />
            </div>

            <!-- Level / Parent Administration -->
            <div class="mb-3">
                <label class="form-label">{{ __('Parent Administration') }}</label>
                <select name="parent_id" id="edit_parent_id" class="select2 form-select" data-placeholder="{{ __('Leave empty if Main Administration') }}" data-allow-clear="true">
                    <option value="">{{ __('Main Administration (No Parent)') }}</option>
                    @foreach ($parentDepartments as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Nursery Selection -->
            <div class="mb-3">
                <label class="form-label">{{ __('Nursery') }}</label>
                <select name="nursery_id" id="edit_nursery_id" class="select2 form-select" data-placeholder="{{ __('General / All Nurseries') }}" data-allow-clear="true">
                    <option value="">{{ __('General / All Nurseries') }}</option>
                    @foreach ($nurseries as $nursery)
                        <option value="{{ $nursery->id }}">{{ $nursery->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Branch Selection (Dynamic) -->
            <div class="mb-3">
                <label class="form-label">{{ __('Specific Branch') }}</label>
                <select name="branch_id" id="edit_branch_id" class="select2 form-select" data-placeholder="{{ __('All Branches / Central') }}" data-allow-clear="true">
                    <option value="">{{ __('All Branches / Central') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" data-nursery="{{ $branch->nursery_id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Manager Selection -->
            <div class="mb-3">
                <label class="form-label">{{ __('Responsible Manager') }}</label>
                <select name="manager_id" id="edit_manager_id" class="select2 form-select" data-placeholder="{{ __('Select Manager') }}" data-allow-clear="true">
                    <option value="">{{ __('None') }}</option>
                    @foreach ($managers as $mgr)
                        <option value="{{ $mgr->id }}">{{ $mgr->name }} ({{ $mgr->email }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Radio -->
            <div class="mb-4">
                <label class="form-label d-block">{{ __('Status') }}</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="editStatusActive" value="active" />
                    <label class="form-check-label text-success fw-medium" for="editStatusActive">{{ __('Active') }}</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="editStatusInactive" value="inactive" />
                    <label class="form-check-label text-danger fw-medium" for="editStatusInactive">{{ __('Inactive') }}</label>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- 3. Modal: Delete Confirmation              -->
<!-- ========================================== -->
<div class="modal fade" id="deleteDepartmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-0">
                <div class="text-danger mb-3">
                    <i class="icon-base bx bx-error-circle" style="font-size: 4rem;"></i>
                </div>
                <h4 class="mb-2">{{ __('Are you sure?') }}</h4>
                <p class="text-muted mb-4">
                    {{ __('Do you really want to delete this department?') }}<br>
                    <strong id="delete_target_name" class="text-heading"></strong>
                </p>
                <form id="deleteDepartmentForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('Yes, Delete') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isRtl = document.documentElement.getAttribute('dir') === 'rtl';

    // 1. Initialize Select2 elements with search and clear
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

    // 2. Dynamic Branches filtering based on selected Nursery in Add Offcanvas
    $('#add_nursery_id').on('change', function () {
        const nurseryId = $(this).val();
        const branchSelect = $('#add_branch_id');

        branchSelect.find('option').each(function () {
            const optionNursery = $(this).data('nursery');
            if (!nurseryId || !optionNursery || optionNursery == nurseryId) {
                $(this).prop('disabled', false).show();
            } else {
                $(this).prop('disabled', true).hide();
            }
        });

        branchSelect.val('').trigger('change');
    });

    // 3. Dynamic Branches filtering based on selected Nursery in Edit Offcanvas
    $('#edit_nursery_id').on('change', function () {
        const nurseryId = $(this).val();
        const branchSelect = $('#edit_branch_id');

        branchSelect.find('option').each(function () {
            const optionNursery = $(this).data('nursery');
            if (!nurseryId || !optionNursery || optionNursery == nurseryId) {
                $(this).prop('disabled', false).show();
            } else {
                $(this).prop('disabled', true).hide();
            }
        });

        branchSelect.val('').trigger('change');
    });

    // 4. Initialize DataTables
    const baseUrl = '{{ url("/hr/departments") }}';
    const table = $('#departmentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("hr.departments.data") }}',
            data: function (d) {
                d.type = $('#filter_type').val();
                d.parent_id = $('#filter_parent_id').val();
                d.nursery_id = $('#filter_nursery_id').val();
                d.branch_id = $('#filter_branch_id').val();
                d.status = $('#filter_status').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center align-middle' },
            { 
                data: 'code', 
                name: 'code',
                render: function (data) {
                    return data ? '<span class="badge bg-label-secondary">' + data + '</span>' : '<span class="text-muted">-</span>';
                },
                className: 'align-middle'
            },
            {
                data: 'name_ar',
                name: 'name_ar',
                render: function (data, type, row) {
                    const desc = row.description_ar ? '<div class="text-muted small text-truncate" style="max-width: 200px;">' + row.description_ar + '</div>' : '';
                    return '<div class="fw-semibold text-heading">' + data + '</div>' +
                           '<div class="text-muted small">' + (row.name_en || '') + '</div>' + desc;
                },
                className: 'align-middle'
            },
            { data: 'type_label', name: 'parent_id', className: 'text-center align-middle' },
            { data: 'parent_name', name: 'parent_id', className: 'align-middle' },
            { 
                data: 'nursery_name', 
                name: 'nursery_id',
                render: function (data, type, row) {
                    return '<div>' + data + '</div>' +
                           '<div class="small text-muted">' + row.branch_name + '</div>';
                },
                className: 'align-middle'
            },
            { data: 'manager_name', name: 'manager_id', className: 'align-middle' },
            {
                data: 'status',
                name: 'status',
                className: 'text-center align-middle',
                render: function (data, type, row) {
                    return '<span class="badge ' + row.status_badge + '">' + row.status_label + '</span>';
                }
            },
            { data: 'created_at', name: 'created_at', className: 'text-center align-middle' },
            {
                data: 'id',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-center align-middle',
                render: function (data, type, row) {
                    return `
                        <div class="d-inline-flex gap-1">
                            <button type="button" class="btn btn-icon btn-sm btn-label-primary btn-edit-department" 
                                data-id="${data}"
                                data-code="${row.code || ''}"
                                data-name-ar="${row.name_ar || ''}"
                                data-name-en="${row.name_en || ''}"
                                data-desc-ar="${row.description_ar || ''}"
                                data-desc-en="${row.description_en || ''}"
                                data-parent-id="${row.parent_id || ''}"
                                data-nursery-id="${row.nursery_id || ''}"
                                data-branch-id="${row.branch_id || ''}"
                                data-manager-id="${row.manager_id || ''}"
                                data-status="${row.status || 'active'}"
                                title="{{ __('Edit') }}">
                                <i class="icon-base bx bx-edit-alt"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-sm btn-label-danger btn-delete-department"
                                data-id="${data}"
                                data-name="${row.name_ar}"
                                title="{{ __('Delete') }}">
                                <i class="icon-base bx bx-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[0, 'desc']],
        dom: '<"row mx-1"' +
             '<"col-12 col-md-6 d-flex align-items-center justify-content-center justify-content-md-start gap-2"l>' +
             '<"col-12 col-md-6 d-flex align-items-center justify-content-end flex-column flex-md-row pe-3 gap-md-3"f>' +
             '>t' +
             '<"row mx-2"' +
             '<"col-sm-12 col-md-6"i>' +
             '<"col-sm-12 col-md-6"p>' +
             '>',
        language: {
            search: "",
            searchPlaceholder: "{{ __('Search...') }}",
            lengthMenu: "_MENU_",
            paginate: {
                previous: '<i class="bx bx-chevron-left"></i>',
                next: '<i class="bx bx-chevron-right"></i>'
            }
        }
    });

    // 5. Trigger reload on filters change
    $('#filter_type, #filter_parent_id, #filter_nursery_id, #filter_branch_id, #filter_status').on('change', function () {
        table.ajax.reload();
    });

    // 6. Reset Filters
    $('#btnResetFilters').on('click', function () {
        $('#filter_type').val('').trigger('change');
        $('#filter_parent_id').val('').trigger('change');
        $('#filter_nursery_id').val('').trigger('change');
        $('#filter_branch_id').val('').trigger('change');
        $('#filter_status').val('').trigger('change');
        table.ajax.reload();
    });

    // 7. Edit Department Offcanvas Fill
    const editOffcanvasEl = document.getElementById('offcanvasEditDepartment');
    const editForm = document.getElementById('editDepartmentForm');

    $(document).on('click', '.btn-edit-department', function () {
        const id = $(this).data('id');
        editForm.action = baseUrl + '/' + id;

        $('#edit_name_ar').val($(this).data('name-ar'));
        $('#edit_name_en').val($(this).data('name-en'));
        $('#edit_description_ar').val($(this).data('desc-ar'));
        $('#edit_description_en').val($(this).data('desc-en'));
        $('#edit_code').val($(this).data('code'));

        $('#edit_parent_id').val($(this).data('parent-id')).trigger('change');
        $('#edit_nursery_id').val($(this).data('nursery-id')).trigger('change');
        $('#edit_branch_id').val($(this).data('branch-id')).trigger('change');
        $('#edit_manager_id').val($(this).data('manager-id')).trigger('change');

        const status = $(this).data('status') || 'active';
        if (status === 'active') {
            $('#editStatusActive').prop('checked', true);
        } else {
            $('#editStatusInactive').prop('checked', true);
        }

        if (editOffcanvasEl) {
            const editOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(editOffcanvasEl);
            editOffcanvas.show();
        }
    });

    // 8. Delete Department Modal
    const deleteModalEl = document.getElementById('deleteDepartmentModal');
    const deleteForm = document.getElementById('deleteDepartmentForm');
    const deleteTargetName = document.getElementById('delete_target_name');

    $(document).on('click', '.btn-delete-department', function () {
        const id = $(this).data('id');
        deleteForm.action = baseUrl + '/' + id;
        deleteTargetName.textContent = $(this).data('name') || '';

        if (deleteModalEl) {
            const deleteModal = bootstrap.Modal.getOrCreateInstance(deleteModalEl);
            deleteModal.show();
        }
    });
});
</script>
@endsection
