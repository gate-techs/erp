@extends('layouts/contentNavbarLayout')

@section('title', __('Student Attendance Report') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Student Services') }}</li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Student Attendance Report') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-calendar-check text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Student Attendance Report') }}</h4>
        </div>
    </div>
</div>

<!-- Filters Card (3 items per row) -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <!-- Row 1: Institution, Branch, Class (3 items) -->
        <div class="row g-3 align-items-end mb-3">
            <!-- 1. Institution -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Educational Institution / Nursery') }}</label>
                <select id="filter_institution_id" class="select2 form-select" data-allow-clear="true">
                    <option value="">{{ __('All Institutions') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Branch -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Branch') }}</label>
                <select id="filter_branch_id" class="select2 form-select" data-allow-clear="true">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branches as $br)
                        <option value="{{ $br->id }}">{{ $br->translate(app()->getLocale())?->name ?? $br->translate('ar')?->name ?? $br->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Class -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Class') }}</label>
                <select id="filter_academic_class_id" class="select2 form-select" data-allow-clear="true">
                    <option value="">{{ __('All Classes') }}</option>
                    @foreach ($classes as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->translate(app()->getLocale())?->name ?? $cls->translate('ar')?->name ?? $cls->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Row 2: From Date, To Date, Reset Button (3 items) -->
        <div class="row g-3 align-items-end">
            <!-- 4. From Date -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('From Date') }}</label>
                <input type="text" id="filter_start_date" class="form-control js-flatpickr-date flatpickr-input active" value="{{ $defaultStartDate }}" placeholder="YYYY-MM-DD" autocomplete="off" />
            </div>

            <!-- 5. To Date -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('To Date') }}</label>
                <input type="text" id="filter_end_date" class="form-control js-flatpickr-date flatpickr-input active" value="{{ $defaultEndDate }}" placeholder="YYYY-MM-DD" autocomplete="off" />
            </div>

            <!-- 6. Reset Button -->
            <div class="col-12 col-md-4">
                <label class="form-label d-none d-md-block">&nbsp;</label>
                <button type="button" id="btn_reset_filters" class="btn btn-label-secondary w-100 d-flex align-items-center justify-content-center gap-1">
                    <i class="icon-base bx bx-reset font-18"></i>
                    <span>{{ __('Reset') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="attendanceReportTable" class="table table-hover border-top">
            <thead>
                <tr>
                    <th style="width: 70px;">{{ __('Identifier') }}</th>
                    <th style="min-width: 200px;">{{ __('Student') }}</th>
                    <th>{{ __('Civil ID') }}</th>
                    <th>{{ __('Class') }}</th>
                    <th>{{ __('Academic Year') }}</th>
                    <th>{{ __('Branch') }}</th>
                    <th>{{ __('Nursery / Institution') }}</th>
                    <th class="text-center" style="width: 90px;">{{ __('Total Days') }}</th>
                    <th class="text-center" style="width: 90px;">{{ __('Attend') }}</th>
                    <th class="text-center" style="width: 90px;">{{ __('Absent') }}</th>
                    <th class="text-center" style="width: 110px;">{{ __('Attendance Log') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal: Student Attendance History Log -->
<div class="modal fade" id="modalStudentAttendanceHistory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                        <i class="icon-base bx bx-history font-20"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-heading mb-0" id="historyModalStudentName">
                            {{ __('Student Attendance Log') }}
                        </h5>
                        <small class="text-muted" id="historyModalPeriod"></small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Stats Badges -->
                <div class="row g-3 mb-4 text-center">
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded border bg-light">
                            <span class="text-muted small d-block mb-1">{{ __('Total Days') }}</span>
                            <h4 class="fw-bold mb-0 text-info" id="stat_total_days">0</h4>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded border bg-light">
                            <span class="text-muted small d-block mb-1">{{ __('Present') }}</span>
                            <h4 class="fw-bold mb-0 text-success" id="stat_present_days">0</h4>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded border bg-light">
                            <span class="text-muted small d-block mb-1">{{ __('Absent') }}</span>
                            <h4 class="fw-bold mb-0 text-danger" id="stat_absent_days">0</h4>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded border bg-light">
                            <span class="text-muted small d-block mb-1">{{ __('Attendance Rate') }}</span>
                            <h4 class="fw-bold mb-0 text-primary" id="stat_attendance_rate">0%</h4>
                        </div>
                    </div>
                </div>

                <!-- Daily Records Table -->
                <div class="table-responsive border rounded">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Class') }}</th>
                                <th class="text-center">{{ __('Status') }}</th>
                                <th class="text-center">{{ __('Approval') }}</th>
                                <th>{{ __('Notes') }}</th>
                            </tr>
                        </thead>
                        <tbody id="historyModalTableBody">
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                    {{ __('Loading...') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
(function () {
    'use strict';

    function initAttendanceReport() {
        const $ = window.jQuery || window.$;
        if (!$ || !$.fn.DataTable) {
            setTimeout(initAttendanceReport, 50);
            return;
        }

        const dataUrl       = "{{ route('academic.attendance.data') }}";
        const studentRptUrl = "{{ url('academic/attendance/student') }}";

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

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

        // Apply Flatpickr to .js-flatpickr-date
        if (typeof window.flatpickr !== 'undefined') {
            $('.js-flatpickr-date').flatpickr({
                dateFormat: 'Y-m-d',
                allowInput: true,
                onChange: function () {
                    table.ajax.reload();
                }
            });
        }

        // Standard DataTable
        const table = $('#attendanceReportTable').DataTable({
            processing: true,
            serverSide: true,
            order: [[0, 'asc']],
            language: {
                search: "",
                searchPlaceholder: "{{ __('Search') }}...",
                lengthMenu: "{{ __('Show _MENU_ entries') }}",
                zeroRecords: "{{ __('No matching records found') }}",
                info: "{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}",
                infoEmpty: "{{ __('Showing 0 to 0 of 0 entries') }}",
                infoFiltered: "({{ __('filtered from _MAX_ total entries') }})",
                paginate: {
                    first: "«",
                    previous: "‹",
                    next: "›",
                    last: "»"
                }
            },
            ajax: {
                url: dataUrl,
                data: function (d) {
                    d.institution_id     = $('#filter_institution_id').val();
                    d.branch_id          = $('#filter_branch_id').val();
                    d.academic_class_id  = $('#filter_academic_class_id').val();
                    d.start_date         = $('#filter_start_date').val();
                    d.end_date           = $('#filter_end_date').val();
                }
            },
            columns: [
                { data: 'identifier', name: 'id' },
                { data: 'student', name: 'translations.name', orderable: false },
                { data: 'civil_id', name: 'civil_id' },
                { data: 'class_name', name: 'academic_class_id', orderable: false },
                { data: 'academic_year_name', name: 'academic_year_id', orderable: false },
                { data: 'branch_name', name: 'branch_id', orderable: false },
                { data: 'institution_name', name: 'institution_id', orderable: false },
                { data: 'total_days', name: 'total_days', className: 'text-center', orderable: false, searchable: false },
                { data: 'present_days', name: 'present_days', className: 'text-center', orderable: false, searchable: false },
                { data: 'absent_days', name: 'absent_days', className: 'text-center', orderable: false, searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ]
        });

        // Filters Change
        $('#filter_institution_id, #filter_branch_id, #filter_academic_class_id, #filter_start_date, #filter_end_date').on('change', function () {
            table.ajax.reload();
        });

        // Reset Filters
        $('#btn_reset_filters').on('click', function () {
            $('#filter_institution_id').val('').trigger('change');
            $('#filter_branch_id').val('').trigger('change');
            $('#filter_academic_class_id').val('').trigger('change');
            table.ajax.reload();
        });

        // View Student History Modal
        $(document).on('click', '.btn-view-history', function () {
            const studentId   = $(this).data('student-id');
            const studentName = $(this).data('student-name');
            const startDate   = $('#filter_start_date').val();
            const endDate     = $('#filter_end_date').val();

            $('#historyModalStudentName').text("{{ __('Student Attendance Log') }}: " + studentName);
            $('#historyModalPeriod').text(startDate + " → " + endDate);
            $('#historyModalTableBody').html('<tr><td colspan="5" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>{{ __("Loading...") }}</td></tr>');

            const modal = new bootstrap.Modal(document.getElementById('modalStudentAttendanceHistory'));
            modal.show();

            $.ajax({
                url: studentRptUrl + '/' + studentId + '/report',
                data: { start_date: startDate, end_date: endDate },
                success: function (res) {
                    if (!res.success) {
                        $('#historyModalTableBody').html('<tr><td colspan="5" class="text-center text-muted py-3">{{ __("No attendance records found.") }}</td></tr>');
                        return;
                    }

                    $('#stat_total_days').text(res.total_days || 0);
                    $('#stat_present_days').text(res.present_days || 0);
                    $('#stat_absent_days').text(res.absent_days || 0);
                    $('#stat_attendance_rate').text((res.attendance_rate || 0) + '%');

                    if (!res.records || res.records.length === 0) {
                        $('#historyModalTableBody').html('<tr><td colspan="5" class="text-center text-muted py-3">{{ __("No attendance records found in this period.") }}</td></tr>');
                        return;
                    }

                    let rowsHtml = '';
                    res.records.forEach(function (rec) {
                        let statusBadge = '';
                        switch (parseInt(rec.status)) {
                            case 1:
                                statusBadge = '<span class="badge bg-label-success">{{ __("Present") }}</span>';
                                break;
                            case 0:
                                statusBadge = '<span class="badge bg-label-danger">{{ __("Absent") }}</span>';
                                break;
                            case 2:
                                statusBadge = '<span class="badge bg-label-warning">{{ __("Late") }}</span>';
                                break;
                            case 3:
                                statusBadge = '<span class="badge bg-label-info">{{ __("Excused") }}</span>';
                                break;
                            default:
                                statusBadge = '<span class="badge bg-label-secondary">-</span>';
                        }

                        let approvedBadge = rec.approved
                            ? '<span class="badge bg-label-success">{{ __("Approved") }}</span>'
                            : '<span class="badge bg-label-secondary">{{ __("Pending") }}</span>';

                        rowsHtml += '<tr>' +
                            '<td class="font-monospace">' + (rec.date ? rec.date.substring(0, 10) : '-') + '</td>' +
                            '<td>' + (rec.class ? (rec.class.name || '-') : '-') + '</td>' +
                            '<td class="text-center">' + statusBadge + '</td>' +
                            '<td class="text-center">' + approvedBadge + '</td>' +
                            '<td>' + (rec.notes ? rec.notes : '<span class="text-muted">-</span>') + '</td>' +
                        '</tr>';
                    });

                    $('#historyModalTableBody').html(rowsHtml);
                },
                error: function () {
                    $('#historyModalTableBody').html('<tr><td colspan="5" class="text-center text-danger py-3">{{ __("Error loading attendance records.") }}</td></tr>');
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAttendanceReport);
    } else {
        initAttendanceReport();
    }
})();
</script>
@endsection
