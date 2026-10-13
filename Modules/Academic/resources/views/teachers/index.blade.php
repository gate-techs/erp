@extends('layouts/contentNavbarLayout')

@section('title', __('Teachers') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Student Services') }}</li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Teachers') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-chalkboard text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Teachers') }}</h4>
        </div>
    </div>

    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddTeacher">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add New Teacher') }}</span>
        </button>
    </div>
</div>

<!-- Filters Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Branch -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                <select id="filter_branch_id" class="select2 form-select" data-allow-clear="true">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branches as $br)
                        <option value="{{ $br->id }}">{{ $br->translate(app()->getLocale())?->name ?? $br->translate('ar')?->name ?? $br->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Job Title -->
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Job Title') }}</label>
                <select id="filter_job_title_id" class="select2 form-select" data-allow-clear="true">
                    <option value="">{{ __('All Job Titles') }}</option>
                    @foreach ($jobTitles as $jt)
                        <option value="{{ $jt->id }}">{{ $jt->translate(app()->getLocale())?->name ?? $jt->translate('ar')?->name ?? $jt->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Status & Reset -->
            <div class="col-12 col-md-5">
                <div class="row g-2 align-items-end">
                    <div class="col-8">
                        <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                        <select id="filter_status" class="select2 form-select" data-allow-clear="true">
                            <option value="">{{ __('All Statuses') }}</option>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="inactive">{{ __('Inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <button type="button" id="btn_reset_filters" class="btn btn-label-secondary w-100 d-flex align-items-center justify-content-center gap-1">
                            <i class="icon-base bx bx-reset font-18"></i>
                            <span>{{ __('Reset') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Teachers DataTable Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="teachersTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Employee Code') }}</th>
                    <th>{{ __('Teacher Name') }}</th>
                    <th>{{ __('Job Title') }}</th>
                    <th class="text-center">{{ __('Role') }}</th>
                    <th>{{ __('Direct Manager') }}</th>
                    <th>{{ __('Branch') }}</th>
                    <th>{{ __('Stages, Classes & Subjects') }}</th>
                    <th class="text-center">{{ __('Phone Number') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 130px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal: Add New Teacher (Quick)                                             -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalAddTeacher" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 540px;">
        <div class="modal-content shadow border-0" style="border-radius: 8px;">
            <div class="modal-header border-bottom py-2 px-3">
                <h6 class="modal-title fw-bold text-heading d-flex align-items-center gap-2 mb-0 font-14">
                    <i class="icon-base bx bx-user-plus text-primary font-18"></i>
                    <span>{{ __('Add New Teacher') }}</span>
                </h6>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAddTeacher">
                @csrf
                <div class="modal-body p-3">
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-heading font-12 mb-1 required">{{ __('Teacher Name') }}</label>
                            <input type="text" name="name" class="form-control form-control-sm" required placeholder="{{ __('Enter full name') }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading font-12 mb-1">{{ __('Employee Code') }}</label>
                            <input type="text" name="employee_code" class="form-control form-control-sm" placeholder="TCH-XXXX">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading font-12 mb-1 required">{{ __('Branch') }}</label>
                            <select name="primary_branch_id" class="form-select form-select-sm select2-modal" required>
                                <option value="">{{ __('Select Branch') }}</option>
                                @foreach ($branches as $br)
                                    <option value="{{ $br->id }}">{{ $br->translate(app()->getLocale())?->name ?? $br->translate('ar')?->name ?? $br->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading font-12 mb-1">{{ __('Job Title') }}</label>
                            <select name="job_title_id" class="form-select form-select-sm select2-modal">
                                <option value="">{{ __('Select Job Title') }}</option>
                                @foreach ($jobTitles as $jt)
                                    <option value="{{ $jt->id }}">{{ $jt->translate(app()->getLocale())?->name ?? $jt->translate('ar')?->name ?? $jt->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading font-12 mb-1">{{ __('Direct Manager') }}</label>
                            <select name="manager_id" class="form-select form-select-sm select2-modal">
                                <option value="">{{ __('Select Manager') }}</option>
                                @foreach ($managers as $mgr)
                                    <option value="{{ $mgr->id }}">{{ $mgr->translate(app()->getLocale())?->name ?? $mgr->translate('ar')?->name ?? $mgr->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading font-12 mb-1">{{ __('Phone Number') }}</label>
                            <input type="text" name="phone" class="form-control form-control-sm" placeholder="965XXXXXXXX">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading font-12 mb-1">{{ __('Email') }}</label>
                            <input type="email" name="email" class="form-control form-control-sm" placeholder="teacher@example.com">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-heading font-12 mb-1 required">{{ __('Status') }}</label>
                            <select name="status" class="form-select form-select-sm" required>
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-3">
                    <button type="button" class="btn btn-label-secondary btn-sm py-1 px-3" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" id="btn_submit_add_teacher" class="btn btn-primary btn-sm py-1 px-3 d-flex align-items-center gap-1">
                        <i class="icon-base bx bx-save font-14"></i>
                        <span>{{ __('Save') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal: Edit Basic Info Only (تعديل بيانات أساسية فقط)                        -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalEditTeacher" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 540px;">
        <div class="modal-content shadow border-0" style="border-radius: 8px;">
            <div class="modal-header border-bottom py-2 px-3">
                <h6 class="modal-title fw-bold text-heading d-flex align-items-center gap-2 mb-0 font-14">
                    <i class="icon-base bx bx-edit text-primary font-18"></i>
                    <span>{{ __('Edit Basic Info') }}</span>
                </h6>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditTeacher">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_teacher_id" name="id">
                <div class="modal-body p-3">
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-heading font-12 mb-1 required">{{ __('Teacher Name') }}</label>
                            <input type="text" id="edit_teacher_name" name="name" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading font-12 mb-1 required">{{ __('Branch') }}</label>
                            <select id="edit_primary_branch_id" name="primary_branch_id" class="form-select form-select-sm select2-modal" required>
                                <option value="">{{ __('Select Branch') }}</option>
                                @foreach ($branches as $br)
                                    <option value="{{ $br->id }}">{{ $br->translate(app()->getLocale())?->name ?? $br->translate('ar')?->name ?? $br->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading font-12 mb-1">{{ __('Job Title') }}</label>
                            <select id="edit_job_title_id" name="job_title_id" class="form-select form-select-sm select2-modal">
                                <option value="">{{ __('Select Job Title') }}</option>
                                @foreach ($jobTitles as $jt)
                                    <option value="{{ $jt->id }}">{{ $jt->translate(app()->getLocale())?->name ?? $jt->translate('ar')?->name ?? $jt->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading font-12 mb-1">{{ __('Direct Manager') }}</label>
                            <select id="edit_manager_id" name="manager_id" class="form-select form-select-sm select2-modal">
                                <option value="">{{ __('Select Manager') }}</option>
                                @foreach ($managers as $mgr)
                                    <option value="{{ $mgr->id }}">{{ $mgr->translate(app()->getLocale())?->name ?? $mgr->translate('ar')?->name ?? $mgr->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading font-12 mb-1">{{ __('Phone Number') }}</label>
                            <input type="text" id="edit_teacher_phone" name="phone" class="form-control form-control-sm">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading font-12 mb-1">{{ __('Email') }}</label>
                            <input type="email" id="edit_teacher_email" name="email" class="form-control form-control-sm">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-heading font-12 mb-1 required">{{ __('Status') }}</label>
                            <select id="edit_teacher_status" name="status" class="form-select form-select-sm" required>
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-3">
                    <button type="button" class="btn btn-label-secondary btn-sm py-1 px-3" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" id="btn_submit_edit_teacher" class="btn btn-primary btn-sm py-1 px-3 d-flex align-items-center gap-1">
                        <i class="icon-base bx bx-save font-14"></i>
                        <span>{{ __('Update') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal: Delete Teacher                                                     -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalDeleteTeacher" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-body text-center p-4">
                <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="icon-base bx bx-trash font-32 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Confirm Delete') }}</h5>
                <p class="text-muted small mb-4">
                    {{ __('Are you sure you want to delete') }} <strong id="delete_teacher_name" class="text-heading"></strong>؟
                    <br>{{ __('This action cannot be undone.') }}
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" id="btn_confirm_delete_teacher" class="btn btn-danger btn-sm">{{ __('Yes, Delete') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- ========================================================================= -->
<!-- Modal: Teacher Allocation (تسكين المعلم: المراحل والفصول والمواد)             -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalTeacherAllocation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <!-- Standard Modal Header -->
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-heading d-flex align-items-center gap-2 mb-0 font-16">
                    <i class="icon-base bx bx-user-check text-primary font-22"></i>
                    <span>{{ __('Teacher Allocation') }}:</span>
                    <span id="alloc_modal_title_name" class="text-primary"></span>
                    <span class="badge bg-label-secondary font-12" id="alloc_modal_title_code"></span>
                </h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Stages Bar Row + Teacher Info -->
            <div class="d-flex align-items-center justify-content-between p-3 bg-white border-bottom flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="fw-bold text-heading small text-nowrap d-flex align-items-center gap-1">
                        <i class="icon-base bx bx-layer text-primary font-18"></i>
                        <span>{{ __('Academic Stages') }}:</span>
                    </span>
                    <div class="d-flex flex-wrap gap-2" id="stages_pills_container">
                        <!-- Dynamic Stage Pills -->
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted small me-2">
                        <i class="icon-base bx bx-buildings me-1 text-primary"></i><span id="alloc_modal_sub_branch"></span>
                        <span class="mx-1">•</span>
                        <i class="icon-base bx bx-briefcase me-1 text-primary"></i><span id="alloc_modal_sub_job"></span>
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-primary py-1 px-3" id="btn_select_all_branch_classes">
                        <i class="icon-base bx bx-check-double me-1 font-16"></i>
                        <span>{{ __('Select All Branch Classes') }}</span>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-3" id="btn_deselect_all_classes">
                        <i class="icon-base bx bx-x me-1 font-16"></i>
                        <span>{{ __('Deselect All') }}</span>
                    </button>
                </div>
            </div>

            <!-- Modal Body (2-Column Classes & Subjects) -->
            <div class="modal-body p-3 bg-light bg-opacity-25">
                <div class="card border shadow-xs" style="border-radius: 8px;">
                    <div class="card-body p-3" id="stage_detail_card_body">
                        <!-- Content rendered dynamically via JS -->
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer py-3 px-4 border-top d-flex justify-content-between align-items-center bg-white">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-label-primary font-13 py-2 px-3">
                        {{ __('Total Selected Classes') }}: <strong id="footer_total_classes" class="font-monospace ms-1">0</strong>
                    </span>
                    <span class="badge bg-label-info font-13 py-2 px-3">
                        {{ __('Total Selected Subjects') }}: <strong id="footer_total_subjects" class="font-monospace ms-1">0</strong>
                    </span>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-label-secondary px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" id="btn_save_allocations" class="btn btn-primary px-4 d-flex align-items-center gap-2">
                        <i class="icon-base bx bx-save font-18"></i>
                        <span>{{ __('Save Allocations') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Streamlined Styling for Allocation Modal */
#modalTeacherAllocation .modal-title {
    white-space: nowrap !important;
}
#modalTeacherAllocation #alloc_modal_title_name {
    white-space: nowrap !important;
    display: inline-block;
}
.stage-pill-btn {
    border-radius: 6px;
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    border: 1px solid #dcdfe4;
    background-color: #ffffff;
    color: #475569;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s ease;
}
.stage-pill-btn:hover {
    border-color: var(--bs-primary, #696cff);
    color: var(--bs-primary, #696cff);
    background-color: #f8faff;
}
.stage-pill-btn.active {
    background-color: var(--bs-primary, #696cff) !important;
    color: #ffffff !important;
    border-color: var(--bs-primary, #696cff) !important;
}
.stage-pill-btn .badge-pill-counter {
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 4px;
}

.alloc-item-card {
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 8px 12px;
    background-color: #ffffff;
    cursor: pointer;
    transition: all 0.15s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    user-select: none;
    font-size: 13px;
    margin-bottom: 6px;
}
.alloc-item-card:hover {
    border-color: #94a3b8;
    background-color: #f8fafc;
}
.alloc-item-card.selected-class {
    background-color: #eff6ff !important;
    border-color: #60a5fa !important;
    color: #1d4ed8;
}
.alloc-item-card.selected-subject {
    background-color: #ecfeff !important;
    border-color: #38bdf8 !important;
    color: #0369a1;
}
.alloc-checkbox {
    width: 17px;
    height: 17px;
    margin: 0;
    cursor: pointer;
}
.alloc-scroll-area {
    max-height: 320px;
    overflow-y: auto;
    padding-left: 4px;
    padding-right: 4px;
}
.btn-group-xs > .btn, .btn-xs {
    padding: 2px 8px;
    font-size: 11.5px;
    border-radius: 4px;
}
</style>
@endsection

@section('page-script')
<script>
(function () {
    'use strict';

    function initTeachers() {
        const $ = window.jQuery || window.$;
        if (!$ || !$.fn.DataTable) {
            setTimeout(initTeachers, 50);
            return;
        }

        const baseUrl = "{{ url('academic/teachers') }}";
        const dataUrl = "{{ route('academic.teachers.data') }}";

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        // Toast Helper
        function showToast(type, message, title = '') {
            const mappedType = type === 'danger' ? 'error' : type;
            if (window.toastr && typeof window.toastr[mappedType] === 'function') {
                window.toastr[mappedType](message, title);
            } else if (window.notify && typeof window.notify[mappedType] === 'function') {
                window.notify[mappedType](message, title);
            } else {
                alert(message);
            }
        }

        // Apply Select2
        if ($.fn.select2) {
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: function () {
                    return $(this).data('placeholder') || '';
                },
                allowClear: true,
                width: '100%',
                dir: $('html').attr('dir') === 'rtl' ? 'rtl' : 'ltr'
            });

            $('.select2-modal').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalAddTeacher'),
                allowClear: true,
                width: '100%',
                dir: $('html').attr('dir') === 'rtl' ? 'rtl' : 'ltr'
            });
        }

        // -------------------------------------------------------------
        // DataTable Initialization
        // -------------------------------------------------------------
        const table = $('#teachersTable').DataTable({
            processing: true,
            serverSide: true,
            pagingType: 'simple_numbers',
            language: {
                emptyTable: `
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="icon-base bx bx-user-x font-32 text-secondary"></i>
                        </div>
                        <h6 class="fw-bold mb-1">{{ __('No teachers found') }}</h6>
                        <p class="text-muted small mb-0">{{ __('Click "Add New Teacher" to create the first teacher.') }}</p>
                    </div>
                `
            },
            ajax: {
                url: dataUrl,
                data: function (d) {
                    d.branch_id    = $('#filter_branch_id').val();
                    d.job_title_id = $('#filter_job_title_id').val();
                    d.status       = $('#filter_status').val();
                }
            },
            columns: [
                { 
                    data: 'DT_RowIndex', 
                    name: 'DT_RowIndex', 
                    orderable: false, 
                    searchable: false, 
                    className: 'text-center',
                    render: (data, type, row, meta) => `<strong class="text-muted">#${data || (meta.row + 1 + (meta.settings._iDisplayStart || 0))}</strong>`
                },
                { data: 'employee_code_formatted', name: 'employee_code', className: 'text-center' },
                { data: 'teacher_name', name: 'translations.name', className: 'fw-semibold text-heading' },
                { data: 'job_title', name: 'job_title_id' },
                { data: 'role', name: 'role', className: 'text-center', orderable: false, searchable: false },
                { data: 'manager', name: 'manager_id' },
                { data: 'branch', name: 'primary_branch_id' },
                { data: 'allocations', name: 'allocations', orderable: false, searchable: false },
                { data: 'phone_formatted', name: 'phone', className: 'text-center' },
                { data: 'status_badge', name: 'status', className: 'text-center' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[0, 'desc']]
        });

        $('#filter_branch_id, #filter_job_title_id, #filter_status').on('change', function () {
            table.ajax.reload();
        });

        $('#btn_reset_filters').on('click', function () {
            $('#filter_branch_id').val('').trigger('change');
            $('#filter_job_title_id').val('').trigger('change');
            $('#filter_status').val('').trigger('change');
            table.ajax.reload();
        });

        // Toggle Status Handler
        $(document).on('click', '.toggle-status-btn', function () {
            const id = $(this).data('id');
            const badge = $(this);

            $.ajax({
                url: `${baseUrl}/${id}/toggle-status`,
                type: 'PATCH',
                success: function (res) {
                    if (res.success) {
                        const isActive = res.status === 'active';
                        badge.text(isActive ? '{{ __("Active") }}' : '{{ __("Inactive") }}');
                        badge.attr('class', `badge ${isActive ? 'bg-label-success' : 'bg-label-danger'} cursor-pointer toggle-status-btn`);
                        showToast('success', res.message || '{{ __("Status updated successfully.") }}');
                    }
                },
                error: function () {
                    showToast('error', '{{ __("Error toggling status.") }}');
                }
            });
        });

        // -------------------------------------------------------------
        // Add Teacher Form
        // -------------------------------------------------------------
        $('#formAddTeacher').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const btn = $('#btn_submit_add_teacher');
            const originalHtml = btn.html();

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>');

            $.ajax({
                url: baseUrl,
                type: 'POST',
                data: form.serialize(),
                success: function (res) {
                    btn.prop('disabled', false).html(originalHtml);
                    $('#modalAddTeacher').modal('hide');
                    form[0].reset();
                    showToast('success', res.message || '{{ __("Teacher created successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    btn.prop('disabled', false).html(originalHtml);
                    const msg = xhr.responseJSON?.message || '{{ __("Failed to create teacher.") }}';
                    showToast('error', msg);
                }
            });
        });

        // -------------------------------------------------------------
        // Edit Basic Info Form
        // -------------------------------------------------------------
        $(document).on('click', '.edit-teacher-btn', function () {
            const id = $(this).data('id');
            $.ajax({
                url: `${baseUrl}/${id}`,
                type: 'GET',
                success: function (res) {
                    if (res.success && res.data) {
                        const d = res.data;
                        $('#edit_teacher_id').val(d.id);
                        $('#edit_teacher_name').val(d.name);
                        $('#edit_primary_branch_id').val(d.primary_branch_id).trigger('change');
                        $('#edit_job_title_id').val(d.job_title_id).trigger('change');
                        $('#edit_manager_id').val(d.manager_id).trigger('change');
                        $('#edit_teacher_phone').val(d.phone);
                        $('#edit_teacher_email').val(d.email);
                        $('#edit_teacher_status').val(d.status);

                        $('#modalEditTeacher').modal('show');
                    }
                },
                error: function () {
                    showToast('error', '{{ __("Failed to load teacher data.") }}');
                }
            });
        });

        $('#formEditTeacher').on('submit', function (e) {
            e.preventDefault();
            const id = $('#edit_teacher_id').val();
            const btn = $('#btn_submit_edit_teacher');
            const originalHtml = btn.html();

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>');

            $.ajax({
                url: `${baseUrl}/${id}`,
                type: 'PUT',
                data: $(this).serialize(),
                success: function (res) {
                    btn.prop('disabled', false).html(originalHtml);
                    $('#modalEditTeacher').modal('hide');
                    showToast('success', res.message || '{{ __("Teacher basic info updated successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    btn.prop('disabled', false).html(originalHtml);
                    const msg = xhr.responseJSON?.message || '{{ __("Failed to update teacher.") }}';
                    showToast('error', msg);
                }
            });
        });

        // -------------------------------------------------------------
        // Delete Teacher
        // -------------------------------------------------------------
        let deleteTeacherId = null;
        $(document).on('click', '.delete-teacher-btn', function () {
            deleteTeacherId = $(this).data('id');
            const name = $(this).data('name') || $(this).closest('tr').find('.teacher-name-text').text() || '{{ __("this teacher") }}';
            $('#delete_teacher_name').text(name);
            $('#modalDeleteTeacher').modal('show');
        });

        $('#btn_confirm_delete_teacher').on('click', function () {
            if (!deleteTeacherId) return;

            const btn = $(this);
            const originalText = btn.text();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>');

            $.ajax({
                url: `${baseUrl}/${deleteTeacherId}`,
                type: 'DELETE',
                success: function (res) {
                    btn.prop('disabled', false).text(originalText);
                    $('#modalDeleteTeacher').modal('hide');
                    deleteTeacherId = null;
                    showToast('success', res.message || '{{ __("Teacher deleted successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    btn.prop('disabled', false).text(originalText);
                    const msg = xhr.responseJSON?.message || '{{ __("Error deleting teacher.") }}';
                    showToast('error', msg);
                }
            });
        });

        // =============================================================
        // Teacher Allocation Logic (تسكين المعلم: فصول ومواد ومراحل)
        // =============================================================
        let currentAllocationTeacherId = null;
        let allocationData = null;
        let activeStageId = null;

        // State maps: stageId -> Set of classIds / subjectIds
        let selectedClassesMap = {};
        let selectedSubjectsMap = {};

        $(document).on('click', '.assign-teacher-btn', function () {
            currentAllocationTeacherId = $(this).data('id');
            openAllocationModal(currentAllocationTeacherId);
        });

        function openAllocationModal(teacherId) {
            $('#stage_detail_card_body').html('<div class="text-center py-5"><span class="spinner-border text-primary"></span><p class="text-muted mt-2 mb-0">{{ __("Loading allocation data...") }}</p></div>');
            $('#modalTeacherAllocation').modal('show');

            $.ajax({
                url: `${baseUrl}/${teacherId}/allocation`,
                type: 'GET',
                success: function (res) {
                    if (res.success && res.data) {
                        allocationData = res.data;
                        renderAllocationUI();
                    } else {
                        showToast('error', '{{ __("Failed to load allocation data.") }}');
                    }
                },
                error: function () {
                    showToast('error', '{{ __("Error fetching allocation data.") }}');
                }
            });
        }

        function renderAllocationUI() {
            if (!allocationData) return;

            const t = allocationData.teacher;
            $('#alloc_modal_title_name').text(t.name);
            $('#alloc_modal_title_code').text(t.employee_code || '');
            $('#alloc_modal_sub_branch').text(t.branch_name || '-');
            $('#alloc_modal_sub_job').text(t.job_title || '-');

            // Initialize selections maps from backend
            selectedClassesMap = {};
            selectedSubjectsMap = {};

            if (allocationData.stages && allocationData.stages.length > 0) {
                allocationData.stages.forEach(stg => {
                    selectedClassesMap[stg.id] = new Set(stg.selected_class_ids || []);
                    selectedSubjectsMap[stg.id] = new Set(stg.selected_subject_ids || []);
                });

                // Set initial active stage to first stage
                activeStageId = allocationData.stages[0].id;
            }

            renderStagePills();
            renderActiveStageDetails();
            updateGlobalKPIs();
        }

        // Render stage tabs/pills
        function renderStagePills() {
            const container = $('#stages_pills_container');
            container.empty();

            if (!allocationData.stages || allocationData.stages.length === 0) {
                container.html('<span class="text-muted small">{{ __("No academic stages found.") }}</span>');
                return;
            }

            allocationData.stages.forEach(stg => {
                const isCurrentActive = (stg.id === activeStageId);
                const selClassesCount = (selectedClassesMap[stg.id] ? selectedClassesMap[stg.id].size : 0);
                const selSubjectsCount = (selectedSubjectsMap[stg.id] ? selectedSubjectsMap[stg.id].size : 0);

                const pillHtml = `
                    <button type="button" class="stage-pill-btn ${isCurrentActive ? 'active' : ''}" data-stage-id="${stg.id}">
                        <span>${stg.name}</span>
                        ${selClassesCount > 0 ? `<span class="badge badge-pill-counter ${isCurrentActive ? 'bg-light text-dark' : 'bg-label-primary'} font-10">${selClassesCount}ف</span>` : ''}
                        ${selSubjectsCount > 0 ? `<span class="badge badge-pill-counter ${isCurrentActive ? 'bg-light text-dark' : 'bg-label-info'} font-10">${selSubjectsCount}م</span>` : ''}
                    </button>
                `;
                container.append(pillHtml);
            });
        }

        // Switch active stage pill
        $(document).on('click', '.stage-pill-btn', function () {
            const stgId = parseInt($(this).data('stage-id'), 10);
            if (activeStageId !== stgId) {
                activeStageId = stgId;
                renderStagePills();
                renderActiveStageDetails();
            }
        });

        // Render detail container for currently active stage
        function renderActiveStageDetails() {
            const body = $('#stage_detail_card_body');
            body.empty();

            if (!allocationData || !allocationData.stages) return;

            const stg = allocationData.stages.find(s => s.id === activeStageId);
            if (!stg) {
                body.html('<div class="text-muted text-center py-3">{{ __("Select a stage to view details") }}</div>');
                return;
            }

            const selClassesSet = selectedClassesMap[stg.id] || new Set();
            const selSubjectsSet = selectedSubjectsMap[stg.id] || new Set();

            let classesCardsHtml = '';
            if (stg.classes && stg.classes.length > 0) {
                stg.classes.forEach(cls => {
                    const isChecked = selClassesSet.has(cls.id);
                    classesCardsHtml += `
                        <label class="alloc-item-card ${isChecked ? 'selected-class' : ''}">
                            <input type="checkbox" class="form-check-input alloc-checkbox stage-class-check"
                                data-stage-id="${stg.id}" data-class-id="${cls.id}" ${isChecked ? 'checked' : ''}>
                            <span class="fw-semibold text-truncate flex-grow-1">${cls.name}</span>
                        </label>
                    `;
                });
            } else {
                classesCardsHtml = '<div class="text-muted small py-3 text-center">{{ __("No classes found for this stage") }}</div>';
            }

            let subjectsCardsHtml = '';
            if (stg.subjects && stg.subjects.length > 0) {
                stg.subjects.forEach(sub => {
                    const isChecked = selSubjectsSet.has(sub.id);
                    subjectsCardsHtml += `
                        <div class="col-6">
                            <label class="alloc-item-card ${isChecked ? 'selected-subject' : ''}">
                                <input type="checkbox" class="form-check-input alloc-checkbox stage-subject-check"
                                    data-stage-id="${stg.id}" data-subject-id="${sub.id}" ${isChecked ? 'checked' : ''}>
                                <span class="fw-semibold text-truncate flex-grow-1">${sub.name}</span>
                            </label>
                        </div>
                    `;
                });
            } else {
                subjectsCardsHtml = '<div class="col-12"><div class="text-muted small py-3 text-center">{{ __("No subjects found for this stage") }}</div></div>';
            }

            const stageHtml = `
                <div class="row g-2">
                    <!-- Classes Column (Right in RTL) -->
                    <div class="col-12 col-md-5 border-end">
                        <div class="d-flex align-items-center justify-content-between pb-1 mb-2 border-bottom">
                            <span class="fw-bold text-heading font-12 d-flex align-items-center gap-1">
                                <i class="icon-base bx bx-chalkboard text-primary font-14"></i>
                                <span>{{ __("Classes") }}</span>
                                <span class="text-primary font-11 ms-1" id="badge_selected_classes_stg">(${selClassesSet.size}/${stg.total_classes || 0})</span>
                            </span>
                            <div class="font-11">
                                <a href="javascript:void(0)" class="btn-select-all-stg-classes text-primary text-decoration-none me-1" data-stage-id="${stg.id}">{{ __("All") }}</a>
                                <span class="text-muted">/</span>
                                <a href="javascript:void(0)" class="btn-deselect-all-stg-classes text-muted text-decoration-none ms-1" data-stage-id="${stg.id}">{{ __("Deselect") }}</a>
                            </div>
                        </div>
                        <div class="alloc-scroll-area">
                            ${classesCardsHtml}
                        </div>
                    </div>

                    <!-- Subjects Column (Left in RTL) -->
                    <div class="col-12 col-md-7">
                        <div class="d-flex align-items-center justify-content-between pb-1 mb-2 border-bottom">
                            <span class="fw-bold text-heading font-12 d-flex align-items-center gap-1">
                                <i class="icon-base bx bx-book text-info font-14"></i>
                                <span>{{ __("Subjects") }}</span>
                                <span class="text-info font-11 ms-1" id="badge_selected_subjects_stg">(${selSubjectsSet.size}/${stg.total_subjects || 0})</span>
                            </span>
                            <div class="font-11">
                                <a href="javascript:void(0)" class="btn-select-all-stg-subjects text-info text-decoration-none me-1" data-stage-id="${stg.id}">{{ __("All") }}</a>
                                <span class="text-muted">/</span>
                                <a href="javascript:void(0)" class="btn-deselect-all-stg-subjects text-muted text-decoration-none ms-1" data-stage-id="${stg.id}">{{ __("Deselect") }}</a>
                            </div>
                        </div>
                        <div class="alloc-scroll-area">
                            <div class="row g-1">
                                ${subjectsCardsHtml}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            body.html(stageHtml);
        }

        // Checkbox change handlers
        $(document).on('change', '.stage-class-check', function () {
            const stgId = parseInt($(this).data('stage-id'), 10);
            const clsId = parseInt($(this).data('class-id'), 10);

            if (!selectedClassesMap[stgId]) {
                selectedClassesMap[stgId] = new Set();
            }

            if ($(this).is(':checked')) {
                selectedClassesMap[stgId].add(clsId);
                $(this).closest('.alloc-item-card').addClass('selected-class');
            } else {
                selectedClassesMap[stgId].delete(clsId);
                $(this).closest('.alloc-item-card').removeClass('selected-class');
            }

            updateStageSectionCounters(stgId);
            renderStagePills();
            updateGlobalKPIs();
        });

        $(document).on('change', '.stage-subject-check', function () {
            const stgId = parseInt($(this).data('stage-id'), 10);
            const subId = parseInt($(this).data('subject-id'), 10);

            if (!selectedSubjectsMap[stgId]) {
                selectedSubjectsMap[stgId] = new Set();
            }

            if ($(this).is(':checked')) {
                selectedSubjectsMap[stgId].add(subId);
                $(this).closest('.alloc-item-card').addClass('selected-subject');
            } else {
                selectedSubjectsMap[stgId].delete(subId);
                $(this).closest('.alloc-item-card').removeClass('selected-subject');
            }

            updateStageSectionCounters(stgId);
            renderStagePills();
            updateGlobalKPIs();
        });

        // Stage-level Select All / Deselect for Classes
        $(document).on('click', '.btn-select-all-stg-classes', function () {
            const stgId = parseInt($(this).data('stage-id'), 10);
            const stg = allocationData.stages.find(s => s.id === stgId);
            if (!stg || !stg.classes) return;

            if (!selectedClassesMap[stgId]) {
                selectedClassesMap[stgId] = new Set();
            }

            stg.classes.forEach(c => selectedClassesMap[stgId].add(c.id));
            $('.stage-class-check[data-stage-id="' + stgId + '"]').prop('checked', true).closest('.alloc-item-card').addClass('selected-class');

            updateStageSectionCounters(stgId);
            renderStagePills();
            updateGlobalKPIs();
        });

        $(document).on('click', '.btn-deselect-all-stg-classes', function () {
            const stgId = parseInt($(this).data('stage-id'), 10);
            if (selectedClassesMap[stgId]) {
                selectedClassesMap[stgId].clear();
            }

            $('.stage-class-check[data-stage-id="' + stgId + '"]').prop('checked', false).closest('.alloc-item-card').removeClass('selected-class');

            updateStageSectionCounters(stgId);
            renderStagePills();
            updateGlobalKPIs();
        });

        // Stage-level Select All / Deselect for Subjects
        $(document).on('click', '.btn-select-all-stg-subjects', function () {
            const stgId = parseInt($(this).data('stage-id'), 10);
            const stg = allocationData.stages.find(s => s.id === stgId);
            if (!stg || !stg.subjects) return;

            if (!selectedSubjectsMap[stgId]) {
                selectedSubjectsMap[stgId] = new Set();
            }

            stg.subjects.forEach(s => selectedSubjectsMap[stgId].add(s.id));
            $('.stage-subject-check[data-stage-id="' + stgId + '"]').prop('checked', true).closest('.alloc-item-card').addClass('selected-subject');

            updateStageSectionCounters(stgId);
            renderStagePills();
            updateGlobalKPIs();
        });

        $(document).on('click', '.btn-deselect-all-stg-subjects', function () {
            const stgId = parseInt($(this).data('stage-id'), 10);
            if (selectedSubjectsMap[stgId]) {
                selectedSubjectsMap[stgId].clear();
            }

            $('.stage-subject-check[data-stage-id="' + stgId + '"]').prop('checked', false).closest('.alloc-item-card').removeClass('selected-subject');

            updateStageSectionCounters(stgId);
            renderStagePills();
            updateGlobalKPIs();
        });

        // Global Select All / Deselect Branch Classes
        $('#btn_select_all_branch_classes').on('click', function () {
            if (!allocationData || !allocationData.stages) return;

            allocationData.stages.forEach(stg => {
                if (!selectedClassesMap[stg.id]) selectedClassesMap[stg.id] = new Set();
                if (stg.classes) {
                    stg.classes.forEach(c => selectedClassesMap[stg.id].add(c.id));
                }
            });

            $('.stage-class-check').prop('checked', true).closest('.alloc-item-card').addClass('selected-class');
            if (activeStageId) updateStageSectionCounters(activeStageId);
            renderStagePills();
            updateGlobalKPIs();
        });

        $('#btn_deselect_all_classes').on('click', function () {
            if (!allocationData || !allocationData.stages) return;

            allocationData.stages.forEach(stg => {
                if (selectedClassesMap[stg.id]) selectedClassesMap[stg.id].clear();
            });

            $('.stage-class-check').prop('checked', false).closest('.alloc-item-card').removeClass('selected-class');
            if (activeStageId) updateStageSectionCounters(activeStageId);
            renderStagePills();
            updateGlobalKPIs();
        });

        function updateStageSectionCounters(stgId) {
            const stg = allocationData.stages.find(s => s.id === stgId);
            if (!stg) return;

            const cCount = selectedClassesMap[stgId] ? selectedClassesMap[stgId].size : 0;
            const sCount = selectedSubjectsMap[stgId] ? selectedSubjectsMap[stgId].size : 0;

            $('#badge_selected_classes_stg').text(`(${cCount}/${stg.total_classes || 0})`);
            $('#badge_selected_subjects_stg').text(`(${sCount}/${stg.total_subjects || 0})`);
        }

        // Update Bottom Footer Summary
        function updateGlobalKPIs() {
            if (!allocationData || !allocationData.stages) return;

            let totalSelectedClassesCount = 0;
            let totalSelectedSubjectsCount = 0;

            allocationData.stages.forEach(stg => {
                const cSize = selectedClassesMap[stg.id] ? selectedClassesMap[stg.id].size : 0;
                const sSize = selectedSubjectsMap[stg.id] ? selectedSubjectsMap[stg.id].size : 0;

                totalSelectedClassesCount += cSize;
                totalSelectedSubjectsCount += sSize;
            });

            $('#footer_total_classes').text(totalSelectedClassesCount);
            $('#footer_total_subjects').text(totalSelectedSubjectsCount);
        }

        // Save Allocation
        $('#btn_save_allocations').on('click', function () {
            if (!currentAllocationTeacherId) return;

            const btn = $(this);
            const originalHtml = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> {{ __("Saving...") }}');

            // Format payload
            const classesPayload = [];
            for (const [stageId, classSet] of Object.entries(selectedClassesMap)) {
                classSet.forEach(classId => {
                    classesPayload.push({
                        stage_id: parseInt(stageId, 10),
                        class_id: parseInt(classId, 10)
                    });
                });
            }

            const subjectsPayload = [];
            for (const [stageId, subjectSet] of Object.entries(selectedSubjectsMap)) {
                subjectSet.forEach(subjectId => {
                    subjectsPayload.push({
                        stage_id: parseInt(stageId, 10),
                        subject_id: parseInt(subjectId, 10)
                    });
                });
            }

            $.ajax({
                url: `${baseUrl}/${currentAllocationTeacherId}/allocation`,
                type: 'POST',
                data: {
                    classes: classesPayload,
                    subjects: subjectsPayload
                },
                success: function (res) {
                    btn.prop('disabled', false).html(originalHtml);
                    $('#modalTeacherAllocation').modal('hide');
                    showToast('success', res.message || '{{ __("Academic assignments saved successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    btn.prop('disabled', false).html(originalHtml);
                    const msg = xhr.responseJSON?.message || '{{ __("Error saving assignments.") }}';
                    showToast('error', msg);
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTeachers);
    } else {
        initTeachers();
    }
})();
</script>
@endsection
