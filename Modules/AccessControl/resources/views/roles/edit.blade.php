@extends('layouts/contentNavbarLayout')

@section('title', __('Edit Role') . ' - ' . config('variables.templateName'))

@section('content')
    <!-- Header & Breadcrumb -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="icon-base bx bx-shield-quarter text-primary font-22"></i>
                <span>{{ __('Edit Role') }}:</span>
                <span class="text-primary">{{ $role->name_ar ?: $role->display_name }}</span>
                @if(!empty($role->name_en) && $role->name_en !== ($role->name_ar ?: $role->display_name))
                    <span class="badge bg-label-secondary font-14 fw-normal">({{ $role->name_en }})</span>
                @endif
            </h4>
            <p class="text-muted mb-0">{{ __('Configure role name and assign granular permissions for all system screens.') }}</p>
        </div>
        <div>
            <a href="{{ route('settings.roles.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1">
                <i class="icon-base bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-right' : 'bx-chevron-left' }}"></i>
                <span>{{ __('Back to Roles') }}</span>
            </a>
        </div>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
            <h6 class="alert-heading fw-bold mb-1">{{ __('Please review the following errors:') }}</h6>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('settings.roles.update', $role->id) }}" id="editRoleForm">
        @csrf
        @method('PUT')

        <!-- Card 1: Role Information -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-bottom py-3">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-info-circle text-primary font-18"></i>
                    <span>{{ __('Role Information') }}</span>
                </h5>
            </div>
            <div class="card-body pt-3">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label required fw-semibold" for="roleNameAr">
                            {{ __('Role Name (Arabic)') }} <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="name_ar"
                               id="roleNameAr"
                               class="form-control form-control-lg fw-semibold"
                               value="{{ old('name_ar', $role->name_ar ?? $role->display_name) }}"
                               required
                               dir="rtl"
                               placeholder="مثال: مدير النظام / محاسب عام">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label required fw-semibold" for="roleNameEn">
                            {{ __('Role Name (English)') }} <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="name_en"
                               id="roleNameEn"
                               class="form-control form-control-lg fw-semibold"
                               value="{{ old('name_en', $role->name_en ?? $role->name) }}"
                               required
                               dir="ltr"
                               placeholder="e.g. System Administrator / Accountant">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">{{ __('Current Assigned Permissions') }}</label>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="badge bg-label-primary fs-6 px-3 py-2" id="selectedCountBadge">
                                {{ count($rolePermissions) }} {{ __('Permissions Selected') }}
                            </span>
                            <span class="text-muted small">({{ __('Out of all available system screens') }})</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Permissions Matrix -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-key text-primary font-20"></i>
                    <h5 class="card-title mb-0 fw-bold">{{ __('Screens & Permissions Matrix') }}</h5>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <!-- Live Search Filter -->
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <span class="input-group-text"><i class="icon-base bx bx-search"></i></span>
                        <input type="text" id="permissionSearch" class="form-control" placeholder="{{ __('Filter screen or module...') }}">
                    </div>

                    <!-- Bulk Select Toggles -->
                    <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" id="btnSelectAll">
                        <i class="icon-base bx bx-check-double font-16"></i>
                        <span>{{ __('Select All') }}</span>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" id="btnDeselectAll">
                        <i class="icon-base bx bx-x font-16"></i>
                        <span>{{ __('Deselect All') }}</span>
                    </button>
                </div>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover border-top align-middle mb-0" id="permissionsMatrixTable">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 260px;">{{ __('Screen / Module') }}</th>
                            <th class="text-center" style="width: 100px;">{{ __('View') }}</th>
                            <th class="text-center" style="width: 100px;">{{ __('Create') }}</th>
                            <th class="text-center" style="width: 100px;">{{ __('Edit') }}</th>
                            <th class="text-center" style="width: 100px;">{{ __('Delete') }}</th>
                            <th style="min-width: 280px;">{{ __('Custom / Workflow Actions') }}</th>
                            <th class="text-center" style="width: 90px;">{{ __('Select All') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissionMatrix as $module)
                            <!-- Module Group Header -->
                            <tr class="table-active module-header-row" data-module="{{ $module['module'] }}">
                                <td colspan="6" class="py-2 px-3 fw-bold text-heading">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="icon-base bx {{ $module['icon'] }} text-primary font-18"></i>
                                        <span>{{ app()->getLocale() === 'ar' ? $module['module_ar'] : $module['module'] }}</span>
                                        <span class="badge bg-label-secondary ms-1">{{ count($module['screens']) }} {{ __('Screens') }}</span>
                                    </div>
                                </td>
                                <td class="text-center py-2">
                                    <div class="form-check d-inline-block">
                                        <input class="form-check-input module-select-all" type="checkbox" title="{{ __('Select all in this section') }}">
                                    </div>
                                </td>
                            </tr>

                            <!-- Screens Rows -->
                            @foreach ($module['screens'] as $screen)
                                <tr class="screen-row" data-search="{{ strtolower(($screen['name_ar'] ?? '') . ' ' . ($screen['name'] ?? '') . ' ' . ($module['module_ar'] ?? '') . ' ' . ($module['module'] ?? '')) }}">
                                    <!-- Screen Name -->
                                    <td>
                                        <div class="d-flex align-items-center gap-2 ps-2">
                                            <div class="avatar avatar-xs bg-label-primary rounded d-inline-flex align-items-center justify-content-center">
                                                <i class="icon-base bx {{ $screen['icon'] ?? 'bx-cube' }} font-14"></i>
                                            </div>
                                            <div>
                                                <span class="fw-semibold text-heading d-block">
                                                    {{ app()->getLocale() === 'ar' ? ($screen['name_ar'] ?? $screen['name']) : $screen['name'] }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 1. View Checkbox -->
                                    <td class="text-center">
                                        @if (isset($screen['view']))
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input perm-check"
                                                       type="checkbox"
                                                       name="permissions[]"
                                                       value="{{ $screen['view'] }}"
                                                       id="perm_{{ md5($screen['view']) }}"
                                                       {{ in_array($screen['view'], $rolePermissions) ? 'checked' : '' }}>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <!-- 2. Create Checkbox -->
                                    <td class="text-center">
                                        @if (isset($screen['create']))
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input perm-check"
                                                       type="checkbox"
                                                       name="permissions[]"
                                                       value="{{ $screen['create'] }}"
                                                       id="perm_{{ md5($screen['create']) }}"
                                                       {{ in_array($screen['create'], $rolePermissions) ? 'checked' : '' }}>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <!-- 3. Edit Checkbox -->
                                    <td class="text-center">
                                        @if (isset($screen['edit']))
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input perm-check"
                                                       type="checkbox"
                                                       name="permissions[]"
                                                       value="{{ $screen['edit'] }}"
                                                       id="perm_{{ md5($screen['edit']) }}"
                                                       {{ in_array($screen['edit'], $rolePermissions) ? 'checked' : '' }}>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <!-- 4. Delete Checkbox -->
                                    <td class="text-center">
                                        @if (isset($screen['delete']))
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input perm-check"
                                                       type="checkbox"
                                                       name="permissions[]"
                                                       value="{{ $screen['delete'] }}"
                                                       id="perm_{{ md5($screen['delete']) }}"
                                                       {{ in_array($screen['delete'], $rolePermissions) ? 'checked' : '' }}>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <!-- 5. Custom / Extra Actions -->
                                    <td>
                                        @if (!empty($screen['custom']))
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach ($screen['custom'] as $customAct)
                                                    <label class="badge bg-label-info d-inline-flex align-items-center gap-1 py-1 px-2 border cursor-pointer">
                                                        <input class="form-check-input mt-0 perm-check"
                                                               type="checkbox"
                                                               name="permissions[]"
                                                               value="{{ $customAct['name'] }}"
                                                               id="perm_{{ md5($customAct['name']) }}"
                                                               {{ in_array($customAct['name'], $rolePermissions) ? 'checked' : '' }}>
                                                        <span class="text-heading small">
                                                            {{ app()->getLocale() === 'ar' ? ($customAct['label'] ?? $customAct['label_en']) : ($customAct['label_en'] ?? $customAct['label']) }}
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted small">{{ __('None') }}</span>
                                        @endif
                                    </td>

                                    <!-- 6. Row Select All -->
                                    <td class="text-center">
                                        <div class="form-check d-inline-block">
                                            <input class="form-check-input row-select-all" type="checkbox" title="{{ __('Select all permissions in this row') }}">
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sticky Footer Action Bar -->
        <div class="card shadow-sm border-0">
            <div class="card-body py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted">{{ __('Total permissions checked:') }}</span>
                    <strong class="text-primary fs-5" id="totalCheckedCount">0</strong>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('settings.roles.index') }}" class="btn btn-label-secondary">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
                        <i class="icon-base bx bx-save font-18"></i>
                        <span>{{ __('Save Role & Permissions') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('permissionsMatrixTable');
    if (!table) return;

    const allCheckboxes = table.querySelectorAll('.perm-check');
    const totalCheckedEl = document.getElementById('totalCheckedCount');
    const selectedBadgeEl = document.getElementById('selectedCountBadge');
    const btnSelectAll = document.getElementById('btnSelectAll');
    const btnDeselectAll = document.getElementById('btnDeselectAll');
    const searchInput = document.getElementById('permissionSearch');

    function updateCounters() {
        let checkedCount = 0;
        allCheckboxes.forEach(cb => {
            if (cb.checked) checkedCount++;
        });
        if (totalCheckedEl) totalCheckedEl.textContent = checkedCount;
        if (selectedBadgeEl) {
            selectedBadgeEl.textContent = `${checkedCount} {{ __('Permissions Selected') }}`;
        }

        // Update row select-alls
        table.querySelectorAll('.screen-row').forEach(row => {
            const rowPerms = row.querySelectorAll('.perm-check');
            const rowSelectAll = row.querySelector('.row-select-all');
            if (rowSelectAll && rowPerms.length > 0) {
                const allRowChecked = Array.from(rowPerms).every(cb => cb.checked);
                const someRowChecked = Array.from(rowPerms).some(cb => cb.checked);
                rowSelectAll.checked = allRowChecked;
                rowSelectAll.indeterminate = someRowChecked && !allRowChecked;
            }
        });

        // Update module select-alls
        table.querySelectorAll('.module-header-row').forEach(modRow => {
            const modSelectAll = modRow.querySelector('.module-select-all');
            if (!modSelectAll) return;

            let sibling = modRow.nextElementSibling;
            const modulePerms = [];
            while (sibling && sibling.classList.contains('screen-row')) {
                sibling.querySelectorAll('.perm-check').forEach(cb => modulePerms.push(cb));
                sibling = sibling.nextElementSibling;
            }

            if (modulePerms.length > 0) {
                const allModChecked = modulePerms.every(cb => cb.checked);
                const someModChecked = modulePerms.some(cb => cb.checked);
                modSelectAll.checked = allModChecked;
                modSelectAll.indeterminate = someModChecked && !allModChecked;
            }
        });
    }

    // Individual checkbox change
    table.addEventListener('change', function (e) {
        if (e.target.classList.contains('perm-check')) {
            updateCounters();
        }

        // Row Select All
        if (e.target.classList.contains('row-select-all')) {
            const row = e.target.closest('.screen-row');
            if (row) {
                row.querySelectorAll('.perm-check').forEach(cb => {
                    cb.checked = e.target.checked;
                });
                updateCounters();
            }
        }

        // Module Select All
        if (e.target.classList.contains('module-select-all')) {
            const modRow = e.target.closest('.module-header-row');
            if (modRow) {
                let sibling = modRow.nextElementSibling;
                while (sibling && sibling.classList.contains('screen-row')) {
                    sibling.querySelectorAll('.perm-check').forEach(cb => {
                        cb.checked = e.target.checked;
                    });
                    sibling = sibling.nextElementSibling;
                }
                updateCounters();
            }
        }
    });

    // Master Select All / Deselect All
    if (btnSelectAll) {
        btnSelectAll.addEventListener('click', function () {
            allCheckboxes.forEach(cb => { cb.checked = true; });
            updateCounters();
        });
    }

    if (btnDeselectAll) {
        btnDeselectAll.addEventListener('click', function () {
            allCheckboxes.forEach(cb => { cb.checked = false; });
            updateCounters();
        });
    }

    // Live Search Filter
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();
            const rows = table.querySelectorAll('.screen-row');
            const moduleRows = table.querySelectorAll('.module-header-row');

            if (!term) {
                rows.forEach(r => r.style.display = '');
                moduleRows.forEach(r => r.style.display = '');
                return;
            }

            rows.forEach(r => {
                const text = r.getAttribute('data-search') || r.textContent.toLowerCase();
                if (text.includes(term)) {
                    r.style.display = '';
                } else {
                    r.style.display = 'none';
                }
            });

            // Hide module headers if no visible rows in that module
            moduleRows.forEach(modRow => {
                let sibling = modRow.nextElementSibling;
                let hasVisible = false;
                while (sibling && sibling.classList.contains('screen-row')) {
                    if (sibling.style.display !== 'none') {
                        hasVisible = true;
                        break;
                    }
                    sibling = sibling.nextElementSibling;
                }
                modRow.style.display = hasVisible ? '' : 'none';
            });
        });
    }

    // Initial counter update
    updateCounters();
});
</script>
@endsection
