@extends('layouts/contentNavbarLayout')

@section('title', __('Students') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Academic Management') }}</li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Students') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-user text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Students') }}</h4>
        </div>
    </div>

    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
        <a href="{{ route('academic.students.create', $guardianId ? ['guardian_id' => $guardianId] : []) }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2 shadow-sm">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add New Student') }}</span>
        </a>
    </div>
</div>

@if(isset($guardian) && $guardian)
<div class="alert alert-primary d-flex align-items-center justify-content-between mb-4 shadow-sm border-0 py-2 px-3" role="alert">
    <div class="d-flex align-items-center gap-2">
        <i class="icon-base bx bx-user text-primary font-20"></i>
        <div>
            <span class="fw-semibold">{{ __('Guardian') }}:</span>
            <span class="badge bg-primary ms-1">{{ $guardian->name }}</span>
        </div>
    </div>
    <a href="{{ route('academic.students.index') }}" class="btn btn-sm btn-outline-primary bg-white">
        <i class="icon-base bx bx-x me-1"></i>{{ __('View All Students') }}
    </a>
</div>
@endif

<!-- Filters Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Institution -->
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Educational Institution / Nursery') }}</label>
                <select id="filter_institution_id" class="select2 form-select" data-allow-clear="true">
                    <option value="">{{ __('All Institutions') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Branch -->
            <div class="col-12 col-md-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                <select id="filter_branch_id" class="select2 form-select" data-allow-clear="true">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branches as $br)
                        <option value="{{ $br->id }}">{{ $br->translate(app()->getLocale())?->name ?? $br->translate('ar')?->name ?? $br->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Stage -->
            <div class="col-12 col-md-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Academic Stage') }}</label>
                <select id="filter_academic_stage_id" class="select2 form-select" data-allow-clear="true">
                    <option value="">{{ __('All Stages') }}</option>
                    @foreach ($stages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->translate(app()->getLocale())?->name ?? $stage->translate('ar')?->name ?? $stage->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Class -->
            <div class="col-12 col-md-2">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Class') }}</label>
                <select id="filter_academic_class_id" class="select2 form-select" data-allow-clear="true">
                    <option value="">{{ __('All Classes') }}</option>
                    @foreach ($classes as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->translate(app()->getLocale())?->name ?? $cls->translate('ar')?->name ?? $cls->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 5. Status & Reset -->
            <div class="col-12 col-md-3">
                <div class="row g-2 align-items-end">
                    <div class="col-7">
                        <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                        <select id="filter_status" class="select2 form-select" data-allow-clear="true">
                            <option value="">{{ __('All Statuses') }}</option>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="pending">{{ __('Pending') }}</option>
                            <option value="inactive">{{ __('Inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-5">
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

<!-- Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="studentsTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>{{ __('Student') }}</th>
                    <th>{{ __('Class') }}</th>
                    <th>{{ __('Academic Year') }}</th>
                    <th>{{ __('Branch') }}</th>
                    <th>{{ __('Educational Institution / Nursery') }}</th>
                    <th class="text-center">{{ __('Registration Date') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('Status') }}</th>
                    <th>{{ __('Added By') }}</th>
                    <th class="text-center" style="width: 130px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="modalDeleteStudent" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="icon-base bx bx-error-circle font-32 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Confirm Delete') }}</h5>
                <p class="text-muted small mb-4">
                    {{ __('Are you sure you want to delete') }} <strong id="delete_student_name" class="text-heading"></strong>؟
                    <br>{{ __('This action cannot be undone.') }}
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" id="btn_confirm_delete" class="btn btn-danger btn-sm">{{ __('Yes, Delete') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
(function () {
    'use strict';

    function initStudents() {
        const $ = window.jQuery || window.$;
        if (!$ || !$.fn.DataTable) {
            setTimeout(initStudents, 50);
            return;
        }

        const baseUrl = "{{ url('academic/students') }}";
        const dataUrl = "{{ route('academic.students.data') }}";
        const guardianId = @json($guardianId);

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
        }

        // DataTable
        const table = $('#studentsTable').DataTable({
            processing: true,
            serverSide: true,
            pagingType: 'simple_numbers',
            language: {
                emptyTable: `
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="icon-base bx bx-user-x font-32 text-secondary"></i>
                        </div>
                        <h6 class="fw-bold mb-1">{{ __('No students found') }}</h6>
                        <p class="text-muted small mb-0">{{ __('Click "Add New Student" to create the first student.') }}</p>
                    </div>
                `
            },
            ajax: {
                url: dataUrl,
                data: function (d) {
                    d.institution_id     = $('#filter_institution_id').val();
                    d.branch_id          = $('#filter_branch_id').val();
                    d.academic_stage_id  = $('#filter_academic_stage_id').val();
                    d.academic_class_id  = $('#filter_academic_class_id').val();
                    d.status             = $('#filter_status').val();
                    d.guardian_id        = guardianId;
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
                { data: 'student', name: 'translations.name', className: 'fw-semibold text-heading' },
                { data: 'class_name', name: 'academic_class_id' },
                { data: 'academic_year_name', name: 'academic_year_id' },
                { data: 'branch_name', name: 'branch_id' },
                { data: 'institution_name', name: 'institution_id' },
                { data: 'created_date', name: 'created_at', className: 'text-center' },
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'created_by', name: 'created_by' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[0, 'desc']]
        });

        // Filter Changes
        $('#filter_institution_id, #filter_branch_id, #filter_academic_stage_id, #filter_academic_class_id, #filter_status').on('change', function () {
            table.ajax.reload();
        });

        $('#btn_reset_filters').on('click', function () {
            $('#filter_institution_id').val('').trigger('change');
            $('#filter_branch_id').val('').trigger('change');
            $('#filter_academic_stage_id').val('').trigger('change');
            $('#filter_academic_class_id').val('').trigger('change');
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

        // Delete Student Handler
        let deleteStudentId = null;
        const deleteModal = new bootstrap.Modal(document.getElementById('modalDeleteStudent'));

        $(document).on('click', '.delete-record-btn, .btn-delete', function () {
            deleteStudentId = $(this).data('id');
            const name = $(this).data('name') || $(this).closest('tr').find('.student-name-text').text() || '{{ __("this student") }}';
            $('#delete_student_name').text(name);
            deleteModal.show();
        });

        $('#btn_confirm_delete').on('click', function () {
            if (!deleteStudentId) return;

            const btn = $(this);
            const originalText = btn.text();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>');

            $.ajax({
                url: `${baseUrl}/${deleteStudentId}`,
                type: 'DELETE',
                success: function (res) {
                    btn.prop('disabled', false).text(originalText);
                    deleteModal.hide();
                    deleteStudentId = null;
                    showToast('success', res.message || '{{ __("Student deleted successfully.") }}');
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    btn.prop('disabled', false).text(originalText);
                    const msg = xhr.responseJSON?.message || '{{ __("Error deleting student.") }}';
                    showToast('error', msg);
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initStudents);
    } else {
        initStudents();
    }
})();
</script>
@endsection
