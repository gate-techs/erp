@extends('layouts/contentNavbarLayout')

@section('title', __('Chart of Accounts') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
    .coa-tree-table th {
        background-color: var(--bs-table-bg, #f8f9fa);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        position: sticky;
        top: 0;
        z-index: 5;
    }
    .coa-tree-row {
        transition: background-color 0.15s ease;
    }
    .coa-tree-row:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.04) !important;
    }
    .coa-tree-row.row-deprecated {
        background-color: rgba(var(--bs-danger-rgb), 0.03);
    }
    .btn-tree-toggle {
        width: 24px;
        height: 24px;
        line-height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .btn-tree-toggle i {
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-tree-toggle.collapsed i {
        transform: rotate(-90deg);
    }
    [dir="rtl"] .btn-tree-toggle.collapsed i {
        transform: rotate(90deg);
    }
    .view-mode-btn.active {
        background-color: var(--bs-primary);
        color: #fff !important;
    }
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-git-repo-forked text-primary font-24"></i>
            <span>{{ __('Chart of Accounts') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Chart of Accounts') }}</li>
            </ol>
        </nav>
    </div>

    <!-- Right Header Controls: Entity Switcher & Add Button -->
    <div class="d-flex flex-wrap align-items-center gap-2">
        <div style="min-width: 240px;">
            <select id="entitySelector" class="select2 form-select" data-placeholder="{{ __('Entity') }}">
                @foreach($entities as $entity)
                    @php
                        $entityName = $entity->translate(app()->getLocale())?->name ?? $entity->translate('ar')?->name ?? $entity->name;
                    @endphp
                    <option value="{{ $entity->id }}" {{ $entity->id === $selectedEntityId ? 'selected' : '' }}>
                        {{ $entityName }} ({{ $entity->currency }})
                    </option>
                @endforeach
            </select>
        </div>

        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddAccount" aria-controls="offcanvasAddAccount">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('New Account') }}</span>
        </button>
    </div>
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

<!-- Standard Filters Card (Exact Match with /users and /accounting/entities layout) -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Type Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Account Type') }}</label>
                <select id="filter_type" class="select2 form-select" data-placeholder="{{ __('All Types') }}" data-allow-clear="true">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach($accountTypes as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Nature Filter (Group / Posting) -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Account Nature') }}</label>
                <select id="filter_nature" class="select2 form-select" data-placeholder="{{ __('All Natures') }}" data-allow-clear="true">
                    <option value="">{{ __('All Natures') }}</option>
                    <option value="group">{{ __('Group Accounts') }}</option>
                    <option value="posting">{{ __('Posting Accounts') }}</option>
                </select>
            </div>

            <!-- 3. Status Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                <select id="filter_status" class="select2 form-select" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                    <option value="deprecated">{{ __('Deprecated') }}</option>
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

<!-- Main Table Card with Toolbar -->
<div class="card shadow-sm border-0">
    <div class="card-header border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <h5 class="card-title fw-bold mb-0 text-heading d-flex align-items-center gap-2">
            <i class="icon-base bx bx-git-branch text-primary font-20"></i>
            <span>{{ __('Chart of Accounts') }}</span>
        </h5>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Search Input -->
            <div class="input-group input-group-merge" style="width: 240px;">
                <span class="input-group-text bg-light border-end-0"><i class="bx bx-search text-muted"></i></span>
                <input type="text" id="filter_search" class="form-control bg-light border-start-0" placeholder="{{ __('Search code or name...') }}">
            </div>

            <!-- Mode Switcher (List View is DEFAULT) -->
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-primary view-mode-btn active" id="btnModeFlat" title="{{ __('List View') }}">
                    <i class="icon-base bx bx-list-ul me-1"></i>{{ __('List View') }}
                </button>
                <button type="button" class="btn btn-outline-primary view-mode-btn" id="btnModeTree" title="{{ __('Tree View') }}">
                    <i class="icon-base bx bx-git-branch me-1"></i>{{ __('Tree View') }}
                </button>
            </div>

            <!-- Expand / Collapse All (Hidden on List View, Shown on Tree View) -->
            <div class="btn-group btn-group-sm d-none" id="treeToggleControls">
                <button type="button" class="btn btn-outline-secondary" id="btnExpandAll" title="{{ __('Expand All') }}">
                    <i class="icon-base bx bx-expand me-1"></i>{{ __('Expand All') }}
                </button>
                <button type="button" class="btn btn-outline-secondary" id="btnCollapseAll" title="{{ __('Collapse All') }}">
                    <i class="icon-base bx bx-collapse me-1"></i>{{ __('Collapse All') }}
                </button>
            </div>
        </div>
    </div>

    <!-- 1. Flat DataTables View (DEFAULT) -->
    <div id="flatViewContainer" class="card-datatable table-responsive">
        <table class="table table-hover border-top align-middle w-100 text-nowrap" id="accountsDataTable">
            <thead>
                <tr>
                    <th style="width: 50px;" class="text-center">#</th>
                    <th class="text-center" style="width: 100px;">{{ __('Account Code') }}</th>
                    <th>{{ __('Account Name') }}</th>
                    <th>{{ __('Parent Account') }}</th>
                    <th class="text-center">{{ __('Account Type') }}</th>
                    <th class="text-center">{{ __('Normal Balance') }}</th>
                    <th class="text-center">{{ __('Account Nature') }}</th>
                    <th class="text-center" style="width: 110px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- 2. Tree Grid (Secondary View) -->
    <div id="treeViewContainer" class="table-responsive text-nowrap d-none">
        <table class="table table-hover coa-tree-table align-middle mb-0" id="coaTreeTable">
            <thead>
                <tr>
                    <th style="min-width: 380px;">{{ __('Account & Code') }}</th>
                    <th>{{ __('Account Type') }}</th>
                    <th>{{ __('Normal Balance') }}</th>
                    <th>{{ __('Account Nature') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="text-end" style="width: 140px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody id="coaTreeTableBody">
                @if($accountTree->isNotEmpty())
                    @foreach($accountTree as $rootAccount)
                        @include('accounting::accounts._tree_row', ['account' => $rootAccount])
                    @endforeach
                @else
                    <tr id="emptyTreeRow">
                        <td colspan="6" class="text-center py-5">
                            <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center">
                                <i class="icon-base bx bx-folder-open font-36 text-muted"></i>
                            </div>
                            <h5 class="fw-bold text-heading mb-1">{{ __('No Accounts Found') }}</h5>
                            <p class="text-muted mb-3">{{ __('Start building your chart of accounts for this entity.') }}</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddAccount">
                                <i class="icon-base bx bx-plus me-1"></i>{{ __('New Account') }}
                            </button>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 1. Offcanvas: Add New Account (Matching /accounting/entities design)       -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddAccount" aria-labelledby="offcanvasAddAccountLabel" style="width: 500px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddAccountLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-plus-circle text-primary font-22"></i>
            <span>{{ __('New Account') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form action="{{ route('accounting.accounts.store') }}" method="POST" id="addAccountForm">
            @csrf
            <input type="hidden" name="entity_id" id="add_entity_id" value="{{ $selectedEntityId }}">

            <!-- Name (Arabic) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Account Name (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="add_name_ar" class="form-control" placeholder="{{ __('Account Name (Arabic)') }}" required dir="rtl" />
            </div>

            <!-- Name (English) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Account Name (English)') }}</label>
                <input type="text" name="name_en" id="add_name_en" class="form-control" placeholder="{{ __('Account Name (English)') }}" />
            </div>

            <!-- Parent Account -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Parent Account') }}</label>
                <select name="parent_id" id="add_parent_id" class="select2 form-select" data-placeholder="{{ __('None (Top Level Root Account)') }}" data-allow-clear="true">
                    <option value="">{{ __('None (Top Level Root Account)') }}</option>
                    @foreach($potentialParents as $parent)
                        @php
                            $pName = $parent->translate(app()->getLocale())?->name ?? $parent->translate('ar')?->name ?? '-';
                        @endphp
                        <option value="{{ $parent->id }}" data-code="{{ $parent->code }}" data-type="{{ $parent->account_type->value }}" data-level="{{ $parent->level }}">
                            {{ str_repeat('— ', max(0, $parent->level - 1)) }}{{ $parent->code }} - {{ $pName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Code with Suggestion Button -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Account Code') }} <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="text" name="code" id="add_code" class="form-control font-monospace" placeholder="{{ __('Account Code') }}" required />
                    <button type="button" class="btn btn-outline-primary" id="btnSuggestCode">{{ __('Suggest') }}</button>
                </div>
            </div>

            <!-- Account Type -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Account Type') }} <span class="text-danger">*</span></label>
                <select name="account_type" id="add_account_type" class="select2 form-select" data-placeholder="{{ __('Account Type') }}" required>
                    <option value="">{{ __('Account Type') }}</option>
                    @foreach($accountTypes as $type)
                        <option value="{{ $type->value }}" data-balance="{{ $type->normalBalance() }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Switches: Group, Posting, Reconcile -->
            <div class="mb-3">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="is_group" id="add_is_group" value="1">
                    <label class="form-check-label fw-semibold text-heading" for="add_is_group">{{ __('Group Account') }}</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="allow_posting" id="add_allow_posting" value="1" checked>
                    <label class="form-check-label fw-semibold text-heading" for="add_allow_posting">{{ __('Allow Direct Posting') }}</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="reconcile" id="add_reconcile" value="1">
                    <label class="form-check-label fw-semibold text-heading" for="add_reconcile">{{ __('Enable Reconciliation') }}</label>
                </div>
            </div>

            <!-- Description (Arabic) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (Arabic)') }}</label>
                <textarea name="description_ar" id="add_description_ar" class="form-control" rows="2" placeholder="{{ __('Description (Arabic)') }}" dir="rtl"></textarea>
            </div>

            <!-- Description (English) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (English)') }}</label>
                <textarea name="description_en" id="add_description_en" class="form-control" rows="2" placeholder="{{ __('Description (English)') }}"></textarea>
            </div>

            <!-- Status Radio (Matching entities/index.blade.php) -->
            <div class="mb-4">
                <label class="form-label d-block fw-semibold text-heading mb-2">{{ __('Status') }}</label>
                <div class="d-flex align-items-center gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_active" id="addStatusActive" value="1" checked>
                        <label class="form-check-label fw-semibold text-success" for="addStatusActive">{{ __('Active') }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_active" id="addStatusInactive" value="0">
                        <label class="form-check-label fw-semibold text-danger" for="addStatusInactive">{{ __('Inactive') }}</label>
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-4" id="btnSubmitAdd">
                    <i class="icon-base bx bx-save"></i>
                    <span>{{ __('Save Account') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit Account (Matching /accounting/entities design)          -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditAccount" aria-labelledby="offcanvasEditAccountLabel" style="width: 500px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasEditAccountLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit text-info font-22"></i>
            <span>{{ __('Edit Account') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form action="" method="POST" id="editAccountForm">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_id" name="id" />
            <input type="hidden" name="entity_id" id="edit_entity_id" value="{{ $selectedEntityId }}">

            <!-- Name (Arabic) -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Account Name (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="edit_name_ar" class="form-control" required dir="rtl" />
            </div>

            <!-- Name (English) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Account Name (English)') }}</label>
                <input type="text" name="name_en" id="edit_name_en" class="form-control" />
            </div>

            <!-- Parent Account -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Parent Account') }}</label>
                <select name="parent_id" id="edit_parent_id" class="select2 form-select" data-placeholder="{{ __('None (Top Level Root Account)') }}" data-allow-clear="true">
                    <option value="">{{ __('None (Top Level Root Account)') }}</option>
                    @foreach($potentialParents as $parent)
                        @php
                            $pName = $parent->translate(app()->getLocale())?->name ?? $parent->translate('ar')?->name ?? '-';
                        @endphp
                        <option value="{{ $parent->id }}" data-code="{{ $parent->code }}" data-type="{{ $parent->account_type->value }}" data-level="{{ $parent->level }}">
                            {{ str_repeat('— ', max(0, $parent->level - 1)) }}{{ $parent->code }} - {{ $pName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Code -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Account Code') }} <span class="text-danger">*</span></label>
                <input type="text" name="code" id="edit_code" class="form-control font-monospace" required />
            </div>

            <!-- Account Type -->
            <div class="mb-3">
                <label class="form-label required fw-semibold text-heading">{{ __('Account Type') }} <span class="text-danger">*</span></label>
                <select name="account_type" id="edit_account_type" class="select2 form-select" data-placeholder="{{ __('Account Type') }}" required>
                    <option value="">{{ __('Account Type') }}</option>
                    @foreach($accountTypes as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Switches: Group, Posting, Reconcile -->
            <div class="mb-3">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="is_group" id="edit_is_group" value="1">
                    <label class="form-check-label fw-semibold text-heading" for="edit_is_group">{{ __('Group Account') }}</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="allow_posting" id="edit_allow_posting" value="1">
                    <label class="form-check-label fw-semibold text-heading" for="edit_allow_posting">{{ __('Allow Direct Posting') }}</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="reconcile" id="edit_reconcile" value="1">
                    <label class="form-check-label fw-semibold text-heading" for="edit_reconcile">{{ __('Enable Reconciliation') }}</label>
                </div>
            </div>

            <!-- Description (Arabic) -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-heading">{{ __('Description (Arabic)') }}</label>
                <textarea name="description_ar" id="edit_description_ar" class="form-control" rows="2" dir="rtl"></textarea>
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
                        <input class="form-check-input" type="radio" name="is_active" id="editStatusActive" value="1">
                        <label class="form-check-label fw-semibold text-success" for="editStatusActive">{{ __('Active') }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_active" id="editStatusInactive" value="0">
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
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;
    const baseUrl = "{{ route('accounting.accounts.index') }}";
    const currentEntityId = {{ $selectedEntityId ?? 0 }};

    // Ensure global select2 initializer is executed exactly like entities/index.blade.php
    if (typeof window.initSelect2 === 'function') {
        window.initSelect2();
    } else {
        const isRtl = $('html').attr('dir') === 'rtl' || $('html').attr('lang') === 'ar';
        $('.select2').each(function () {
            const placeholder = $(this).data('placeholder') || '';
            const allowClear = $(this).data('allow-clear') !== false;

            $(this).select2({
                theme: 'bootstrap-5',
                placeholder: placeholder,
                allowClear: allowClear,
                dir: isRtl ? 'rtl' : 'ltr',
                width: '100%',
                dropdownParent: $(this).closest('.offcanvas, .modal').length ? $(this).closest('.offcanvas, .modal') : $(document.body)
            });
        });
    }

    // 1. Entity Switcher
    $('#entitySelector').on('change', function () {
        const entityId = $(this).val();
        window.location.href = baseUrl + '?entity_id=' + entityId;
    });

    // 2. Alert Notification Helper
    function showAlert(type, message) {
        const icon = type === 'success' ? 'bx-check-circle' : 'bx-error-circle';
        const html = `
            <div class="alert alert-${type} alert-dismissible d-flex align-items-center mb-4 shadow-sm" role="alert">
                <i class="icon-base bx ${icon} me-2 font-20"></i>
                <div>${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('#alertContainer').html(html);
        $('html, body').animate({ scrollTop: $('#alertContainer').offset().top - 80 }, 300);
    }

    // 3. Initialize Flat DataTable immediately as DEFAULT
    const flatDataTable = $('#accountsDataTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pagingType: 'simple_numbers',
        ajax: {
            url: "{{ route('accounting.accounts.data') }}",
            data: function (d) {
                d.entity_id = $('#entitySelector').val();
                d.account_type = $('#filter_type').val();
                d.is_group = $('#filter_nature').val();
                d.is_active = $('#filter_status').val() === 'active' ? '1' : ($('#filter_status').val() === 'inactive' ? '0' : '');
                d.is_deprecated = $('#filter_status').val() === 'deprecated' ? '1' : '';
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
            { data: 'code_formatted', name: 'code', className: 'text-center align-middle', render: data => data },
            { data: 'account_name', name: 'translations.name', className: 'align-middle', render: data => data },
            { data: 'parent_name', name: 'parent_id', orderable: false, className: 'align-middle', render: data => data },
            { data: 'type_badge', name: 'account_type', className: 'text-center align-middle', render: data => data },
            { data: 'balance_badge', name: 'normal_balance', className: 'text-center align-middle', render: data => data },
            { data: 'nature_badge', name: 'is_group', className: 'text-center align-middle', render: data => data },
            { data: 'status_badges', name: 'is_active', orderable: false, className: 'text-center align-middle', render: data => data },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle', render: data => data }
        ],
        language: {
            search: "",
            searchPlaceholder: "{{ __('Search accounts...') }}",
            paginate: {
                previous: '<i class="bx bx-chevron-left"></i>',
                next: '<i class="bx bx-chevron-right"></i>'
            }
        }
    });

    // View Mode Toggle (Flat View is default)
    let isTreeView = false;

    $('#btnModeFlat').on('click', function () {
        isTreeView = false;
        $(this).addClass('active');
        $('#btnModeTree').removeClass('active');
        $('#flatViewContainer').removeClass('d-none');
        $('#treeViewContainer').addClass('d-none');
        $('#treeToggleControls').addClass('d-none');
        flatDataTable.ajax.reload();
    });

    $('#btnModeTree').on('click', function () {
        isTreeView = true;
        $(this).addClass('active');
        $('#btnModeFlat').removeClass('active');
        $('#treeViewContainer').removeClass('d-none');
        $('#flatViewContainer').addClass('d-none');
        $('#treeToggleControls').removeClass('d-none');
        applyFilters();
    });

    // 4. Tree Expand / Collapse Logic
    $(document).on('click', '.btn-tree-toggle', function (e) {
        e.stopPropagation();
        const $btn = $(this);
        const parentId = $btn.data('target');
        const isCollapsed = $btn.hasClass('collapsed');

        if (isCollapsed) {
            $btn.removeClass('collapsed');
            showChildren(parentId);
            $btn.closest('tr').find('.node-icon').removeClass('bx-folder').addClass('bx-folder-open');
        } else {
            $btn.addClass('collapsed');
            hideChildren(parentId);
            $btn.closest('tr').find('.node-icon').removeClass('bx-folder-open').addClass('bx-folder');
        }
    });

    function hideChildren(parentId) {
        const directChildren = $(`tr.child-of-${parentId}`);
        directChildren.each(function () {
            const childId = $(this).data('id');
            $(this).addClass('d-none');
            hideChildren(childId);
        });
    }

    function showChildren(parentId) {
        const directChildren = $(`tr.child-of-${parentId}`);
        directChildren.each(function () {
            const childId = $(this).data('id');
            const $toggle = $(this).find('.btn-tree-toggle');
            $(this).removeClass('d-none');
            if (!$toggle.hasClass('collapsed')) {
                showChildren(childId);
            }
        });
    }

    $('#btnExpandAll').on('click', function () {
        $('.coa-tree-row').removeClass('d-none');
        $('.btn-tree-toggle').removeClass('collapsed');
        $('.node-icon').removeClass('bx-folder').addClass('bx-folder-open');
    });

    $('#btnCollapseAll').on('click', function () {
        $('.coa-tree-row:not(.root-node)').addClass('d-none');
        $('.btn-tree-toggle').addClass('collapsed');
        $('.node-icon').removeClass('bx-folder-open').addClass('bx-folder');
    });

    // 5. Standard Filters Handling (Exact Match with /accounting/entities)
    let isResetting = false;

    $('#filter_type, #filter_nature, #filter_status').on('change', function () {
        if (isResetting) return;
        applyFilters();
    });

    $('#filter_search').on('input', function () {
        applyFilters();
    });

    $('#btnResetFilters').on('click', function () {
        isResetting = true;
        $('#filter_search').val('');
        $('#filter_type').val('').trigger('change');
        $('#filter_nature').val('').trigger('change');
        $('#filter_status').val('').trigger('change');
        isResetting = false;
        applyFilters();
    });

    function applyFilters() {
        const term = $('#filter_search').val().trim();
        const type = $('#filter_type').val();
        const nature = $('#filter_nature').val();
        const status = $('#filter_status').val();

        // Always reload flat table
        flatDataTable.search(term).ajax.reload();

        // Also filter tree if in tree view
        if (!isTreeView) return;

        const lowerTerm = term.toLowerCase();
        if (!lowerTerm && !type && !nature && !status) {
            $('.coa-tree-row').removeClass('d-none');
            return;
        }

        const matchingIds = new Set();
        const parentIdsToExpand = new Set();

        $('.coa-tree-row').each(function () {
            const $row = $(this);
            const rowId = $row.data('id');
            const rowParentId = $row.data('parent-id');
            const rowType = $row.data('type');
            const rowNature = $row.data('nature');
            const rowStatus = $row.data('status');
            const rowName = String($row.data('name') || '');

            let matches = true;

            if (lowerTerm && !rowName.includes(lowerTerm)) matches = false;
            if (type && rowType !== type) matches = false;
            if (nature && rowNature !== nature) matches = false;
            if (status && rowStatus !== status) matches = false;

            if (matches) {
                matchingIds.add(rowId);
                let pId = rowParentId;
                while (pId) {
                    parentIdsToExpand.add(pId);
                    const $pRow = $(`#coa-row-${pId}`);
                    pId = $pRow.data('parent-id');
                }
            }
        });

        $('.coa-tree-row').each(function () {
            const rowId = $(this).data('id');
            if (matchingIds.has(rowId) || parentIdsToExpand.has(rowId)) {
                $(this).removeClass('d-none');
                if (parentIdsToExpand.has(rowId)) {
                    $(this).find('.btn-tree-toggle').removeClass('collapsed');
                }
            } else {
                $(this).addClass('d-none');
            }
        });
    }

    // 6. Group Account Toggle Rules (is_group = true -> allow_posting = false)
    $('#add_is_group').on('change', function () {
        const isGroup = $(this).is(':checked');
        const postingSwitch = $('#add_allow_posting');
        if (isGroup) {
            postingSwitch.prop('checked', false).prop('disabled', true);
        } else {
            postingSwitch.prop('checked', true).prop('disabled', false);
        }
    });

    $('#edit_is_group').on('change', function () {
        const isGroup = $(this).is(':checked');
        const postingSwitch = $('#edit_allow_posting');
        if (isGroup) {
            postingSwitch.prop('checked', false).prop('disabled', true);
        } else {
            postingSwitch.prop('disabled', false);
        }
    });

    // Parent change: suggest next code
    $('#add_parent_id').on('change', function () {
        const parentId = $(this).val();
        fetchSuggestedCode(parentId, $('#add_code'));
    });

    function fetchSuggestedCode(parentId, codeInput) {
        $.ajax({
            url: "{{ route('accounting.accounts.suggest-code') }}",
            data: {
                entity_id: currentEntityId,
                parent_id: parentId || ''
            },
            success: function (res) {
                if (res.success && res.code) {
                    codeInput.val(res.code);
                }
            }
        });
    }

    $('#btnSuggestCode').on('click', function () {
        fetchSuggestedCode($('#add_parent_id').val(), $('#add_code'));
    });

    // Quick Add Child Account Button (+ فرعي)
    const addOffcanvasEl = document.getElementById('offcanvasAddAccount');
    const addOffcanvas = addOffcanvasEl ? bootstrap.Offcanvas.getOrCreateInstance(addOffcanvasEl) : null;

    $(document).on('click', '.btn-quick-add-child', function (e) {
        e.stopPropagation();
        const parentId = $(this).data('parent-id');
        const parentType = $(this).data('type');

        $('#addAccountForm')[0].reset();
        $('#add_parent_id').val(parentId).trigger('change');
        if (parentType) {
            $('#add_account_type').val(parentType).trigger('change');
        }
        $('#add_is_group').prop('checked', false).trigger('change');
        $('#add_allow_posting').prop('checked', true).prop('disabled', false);

        if (addOffcanvas) addOffcanvas.show();
    });

    // Submit Add Account
    $('#addAccountForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#btnSubmitAdd');

        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> {{ __("Saving...") }}');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                if (addOffcanvas) addOffcanvas.hide();
                form[0].reset();
                showAlert('success', res.message || "{{ __('Account created successfully.') }}");
                setTimeout(() => window.location.reload(), 600);
            },
            error: function (xhr) {
                let msg = "{{ __('An error occurred while saving.') }}";
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    if (xhr.responseJSON.errors) {
                        const firstKey = Object.keys(xhr.responseJSON.errors)[0];
                        msg = xhr.responseJSON.errors[firstKey][0];
                    }
                }
                alert(msg);
            },
            complete: function () {
                submitBtn.prop('disabled', false).html('<i class="icon-base bx bx-save"></i> <span>{{ __("Save Account") }}</span>');
            }
        });
    });

    // Open Edit Offcanvas
    const editOffcanvasEl = document.getElementById('offcanvasEditAccount');
    const editOffcanvas = editOffcanvasEl ? bootstrap.Offcanvas.getOrCreateInstance(editOffcanvasEl) : null;

    $(document).on('click', '.btn-edit-account', function (e) {
        e.stopPropagation();
        const id = $(this).data('id');

        $.ajax({
            url: baseUrl + '/' + id,
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                if (!res.success) return;
                const d = res.data;

                $('#edit_id').val(d.id);
                $('#editAccountForm').attr('action', baseUrl + '/' + d.id);

                $('#edit_name_ar').val(d.name_ar);
                $('#edit_name_en').val(d.name_en);
                $('#edit_description_ar').val(d.description_ar);
                $('#edit_description_en').val(d.description_en);
                $('#edit_code').val(d.code);

                $('#edit_parent_id').val(d.parent_id).trigger('change');
                $('#edit_account_type').val(d.account_type).trigger('change');

                $('#edit_is_group').prop('checked', d.is_group).trigger('change');
                $('#edit_allow_posting').prop('checked', d.allow_posting);
                $('#edit_reconcile').prop('checked', d.reconcile);

                if (d.is_active) {
                    $('#editStatusActive').prop('checked', true);
                } else {
                    $('#editStatusInactive').prop('checked', true);
                }

                if (editOffcanvas) editOffcanvas.show();
            },
            error: function () {
                alert("{{ __('Unable to fetch account details.') }}");
            }
        });
    });

    // Submit Edit Account
    $('#editAccountForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#btnSubmitEdit');

        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> {{ __("Saving...") }}');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                if (editOffcanvas) editOffcanvas.hide();
                showAlert('success', res.message || "{{ __('Account updated successfully.') }}");
                setTimeout(() => window.location.reload(), 600);
            },
            error: function (xhr) {
                let msg = "{{ __('An error occurred while saving.') }}";
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    if (xhr.responseJSON.errors) {
                        const firstKey = Object.keys(xhr.responseJSON.errors)[0];
                        msg = xhr.responseJSON.errors[firstKey][0];
                    }
                }
                alert(msg);
            },
            complete: function () {
                submitBtn.prop('disabled', false).html('<i class="icon-base bx bx-save"></i> <span>{{ __("Save Changes") }}</span>');
            }
        });
    });

    // Deprecate / Restore Action
    $(document).on('click', '.btn-deprecate-account', function (e) {
        e.stopPropagation();
        const id = $(this).data('id');
        const isDep = $(this).data('deprecated') === 1;
        const code = $(this).data('code');
        const name = $(this).data('name');

        const promptMsg = isDep
            ? "{{ __('Are you sure you want to restore account') }} " + code + " (" + name + ")?"
            : "{{ __('Are you sure you want to deprecate account') }} " + code + " (" + name + ")?";

        if (!confirm(promptMsg)) return;

        $.ajax({
            url: baseUrl + '/' + id + '/toggle-deprecated',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                showAlert('success', res.message);
                setTimeout(() => window.location.reload(), 600);
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message || "{{ __('An error occurred.') }}");
            }
        });
    });

    // Delete Account Action
    $(document).on('click', '.btn-delete-account', function (e) {
        e.stopPropagation();
        const id = $(this).data('id');
        const hasChildren = $(this).data('has-children') === 1;
        const code = $(this).data('code');

        if (hasChildren) {
            alert("{{ __('Cannot delete this account because it contains sub-accounts. Please delete or reassign its child accounts first.') }}");
            return;
        }

        if (!confirm("{{ __('Are you sure you want to delete account') }} " + code + "?")) {
            return;
        }

        $.ajax({
            url: baseUrl + '/' + id,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            },
            success: function (res) {
                showAlert('success', res.message || "{{ __('Account deleted.') }}");
                setTimeout(() => window.location.reload(), 600);
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message || "{{ __('Could not delete account.') }}");
            }
        });
    });
});
</script>
@endsection
