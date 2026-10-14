@extends('layouts/contentNavbarLayout')

@section('title', __('Student Fees') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Fees & Packages') }}</li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Student Fees') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-receipt text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Student Fees') }}</h4>
        </div>
    </div>
    <button type="button" id="btn_add_student_fee" class="btn btn-primary btn-sm d-flex align-items-center gap-2 mt-3 mt-md-0 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddStudentFee">
        <i class="icon-base bx bx-plus font-18"></i>
        <span>{{ __('Assign Fee to Student') }}</span>
    </button>
</div>

<!-- Filters Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Student -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Student') }}</label>
                <select id="filter_student_id" class="select2 form-select" data-allow-clear="true">
                    <option value="">{{ __('All Students') }}</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}">{{ $student->translate(app()->getLocale())?->name ?? $student->translate('ar')?->name ?? $student->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Academic Fee -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Academic Fee') }}</label>
                <select id="filter_fee_id" class="select2 form-select" data-allow-clear="true">
                    <option value="">{{ __('All Fees') }}</option>
                    @foreach ($fees as $fee)
                        <option value="{{ $fee->id }}">{{ $fee->translate(app()->getLocale())?->name ?? $fee->translate('ar')?->name ?? $fee->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Status & Reset -->
            <div class="col-12 col-md-4">
                <div class="row g-2 align-items-end">
                    <div class="col-8">
                        <label class="form-label fw-semibold text-heading small mb-1">{{ __('Status') }}</label>
                        <select id="filter_status" class="select2 form-select" data-allow-clear="true">
                            <option value="">{{ __('All Statuses') }}</option>
                            <option value="active">{{ __('Active') }}</option>
                            <option value="completed">{{ __('Completed') }}</option>
                            <option value="cancelled">{{ __('Cancelled') }}</option>
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

<!-- Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="studentFeesTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>{{ __('Student') }}</th>
                    <th>{{ __('Academic Fee') }}</th>
                    <th>{{ __('عدد المدفوعات') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Start Date') }}</th>
                    <th class="text-center">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 110px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Offcanvas: Assign Fee to Student (RTL & LTR Aware) -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddStudentFee" style="width: 480px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3">
        <h5 class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-plus-circle text-primary font-22"></i>
            <span id="studentFeeModalTitle">{{ __('Assign Fee to Student') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="{{ __('Close') }}"></button>
    </div>
    <div class="offcanvas-body p-4">
        <form id="formAddStudentFee">
            <input type="hidden" name="student_fee_id" id="student_fee_id">
            <div class="mb-3 text-start">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Student') }}</label>
                <select name="academic_student_id" id="add_student_id" class="select2 form-select" required>
                    <option value="">{{ __('Select Student') }}</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}">{{ $student->translate(app()->getLocale())?->name ?? $student->translate('ar')?->name ?? $student->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3 text-start">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Academic Fee') }}</label>
                <select name="academic_fee_id" id="add_fee_id" class="select2 form-select" required>
                    <option value="">{{ __('Select Fee') }}</option>
                    @foreach ($fees as $fee)
                        <option value="{{ $fee->id }}" data-amount="{{ $fee->amount }}">
                            {{ $fee->translate(app()->getLocale())?->name ?? $fee->translate('ar')?->name ?? $fee->name }} ({{ number_format($fee->amount, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="row g-3 mb-3 text-start">
                <div class="col-6">
                    <label class="form-label fw-semibold text-heading small mb-1">{{ __('Total Amount') }}</label>
                    <input type="number" step="0.01" min="0" name="total_amount" id="add_total_amount" class="form-control" placeholder="0.00" required>
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold text-heading small mb-1">{{ __('عدد المدفوعات') }}</label>
                    <input type="number" min="1" max="120" name="installments_count" id="add_installments_count" class="form-control" value="1" required>
                </div>
            </div>
            <div class="mb-4 text-start">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('First Due Date') }}</label>
                <input type="text" name="start_date" id="add_start_date" class="form-control js-flatpickr-date flatpickr-input active" value="{{ now()->format('Y-m-d') }}" placeholder="YYYY-MM-DD" autocomplete="off" required>
            </div>
            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary" id="btn_submit_student_fee">{{ __('Save') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Payments Table -->
<div class="modal fade" id="modalStudentFeeDetails" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
                    <i class="icon-base bx bx-list-ul text-info font-22"></i>
                    <span>{{ __('جدول المدفوعات') }}</span>
                </h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="modal-body p-4" id="studentFeeDetailsBody"></div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Payment Details -->
<div class="modal fade" id="modalPaymentDetails" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-heading">{{ __('تفاصيل المدفوعة') }}</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="paymentDetailsBody"></div>
        </div>
    </div>
</div>

<!-- Modal: Add/Edit Payment -->
<div class="modal fade" id="modalAddStudentFeePayment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
                    <i class="icon-base bx bx-credit-card text-success font-22"></i>
                    <span id="paymentModalTitle">{{ __('إضافة دفعة') }}</span>
                </h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formAddStudentFeePayment">
                    <input type="hidden" id="payment_student_fee_id">
                    <input type="hidden" id="payment_id">
                    <div class="row g-3 text-start">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-heading small mb-1">{{ __('Installment') }}</label>
                            <select name="academic_fee_installment_id" id="payment_installment_id" class="form-select">
                                <option value="">{{ __('Next unpaid installment') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-heading small mb-1">{{ __('Amount') }}</label>
                            <input type="number" step="0.01" min="0.01" name="amount" id="payment_amount" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-heading small mb-1">{{ __('Payment Date') }}</label>
                            <input type="text" name="payment_date" id="payment_date" class="form-control js-flatpickr-date flatpickr-input active" value="{{ now()->format('Y-m-d') }}" placeholder="YYYY-MM-DD" autocomplete="off" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-heading small mb-1">{{ __('Payment Status') }}</label>
                            <select name="status" class="form-select">
                                <option value="paid">{{ __('Paid') }}</option>
                                <option value="pending">{{ __('Pending') }}</option>
                                <option value="failed">{{ __('Failed') }}</option>
                                <option value="refunded">{{ __('Refunded') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-heading small mb-1">{{ __('Invoice Number') }}</label>
                            <input type="text" name="invoice_number" class="form-control" placeholder="{{ __('Invoice Number') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-heading small mb-1">{{ __('Payment Number') }}</label>
                            <input type="text" name="payment_number" class="form-control" placeholder="{{ __('Auto generated if empty') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-heading small mb-1">{{ __('Transaction Number') }}</label>
                            <input type="text" name="transaction_number" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-heading small mb-1">{{ __('Reference Number') }}</label>
                            <input type="text" name="reference_number" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-heading small mb-1">{{ __('Tracking Number') }}</label>
                            <input type="text" name="tracking_number" class="form-control">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-4">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-success" id="btn_submit_payment">{{ __('Save Payment') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
(function () {
    'use strict';

    function initStudentFees() {
        const $ = window.jQuery || window.$;
        if (!$ || !$.fn.DataTable) {
            setTimeout(initStudentFees, 50);
            return;
        }

        const isRtl = $('html').attr('dir') === 'rtl' || $('html').attr('lang') === 'ar';
        const dataUrl = @json(route('academic.student-fees.data'));
        const baseUrl = @json(url('academic/student-fees'));
        const fees = @json($fees->mapWithKeys(fn ($fee) => [$fee->id => (float) $fee->amount]));

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        // Helper: Initialize Select2 with parent awareness (offcanvas/modal)
        function applySelect2(context) {
            const target = context ? $(context).find('select.select2') : $('select.select2');
            target.each(function () {
                const $this = $(this);
                if ($this.hasClass('select2-hidden-accessible')) {
                    $this.select2('destroy');
                }
                const $parent = $this.closest('.offcanvas, .modal');
                $this.select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $parent.length ? $parent : null,
                    placeholder: function () {
                        return $(this).data('placeholder') || '';
                    },
                    allowClear: $this.data('allow-clear') !== false,
                    dir: isRtl ? 'rtl' : 'ltr',
                    width: '100%'
                });
            });
        }

        applySelect2();

        // Re-apply Select2 on offcanvas and modal open
        $('#offcanvasAddStudentFee').on('shown.bs.offcanvas', function () {
            applySelect2(this);
        });

        $('#btn_add_student_fee').on('click', function () {
            $('#formAddStudentFee')[0].reset();
            $('#student_fee_id').val('');
            $('#studentFeeModalTitle').text('{{ __("Assign Fee to Student") }}');
            $('#btn_submit_student_fee').text('{{ __("Save") }}');
            $('#add_student_id, #add_fee_id').val('').trigger('change');
        });

        $('#modalAddStudentFeePayment').on('shown.bs.modal', function () {
            applySelect2(this);
        });

        // Flatpickr Init
        if (typeof window.flatpickr !== 'undefined') {
            $('.js-flatpickr-date').flatpickr({
                dateFormat: 'Y-m-d',
                allowInput: true
            });
        }

        // DataTable Initialization
        const table = $('#studentFeesTable').DataTable({
            processing: true,
            serverSide: true,
            pagingType: 'simple_numbers',
            language: {
                search: "",
                searchPlaceholder: "{{ __('Search') }}...",
                zeroRecords: "{{ __('No matching records found') }}",
                emptyTable: `
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="icon-base bx bx-receipt font-32 text-secondary"></i>
                        </div>
                        <h6 class="fw-bold mb-1">{{ __('No student fees found') }}</h6>
                        <p class="text-muted small mb-0">{{ __('Click "Assign Fee to Student" to create the first assignment.') }}</p>
                    </div>
                `
            },
            ajax: {
                url: dataUrl,
                data: function (d) {
                    d.academic_student_id = $('#filter_student_id').val();
                    d.academic_fee_id     = $('#filter_fee_id').val();
                    d.status              = $('#filter_status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false },
                { data: 'student_name', name: 'academic_student_id' },
                { data: 'fee_name', name: 'academic_fee_id' },
                { data: 'installments_summary', name: 'installments_count', orderable: false, searchable: false },
                { data: 'amount_summary', name: 'total_amount', orderable: false, searchable: false },
                { data: 'start_date', name: 'start_date' },
                { data: 'status_badge', name: 'status', className: 'text-center' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[0, 'desc']]
        });

        // Filter Handlers
        $('#filter_student_id, #filter_fee_id, #filter_status').on('change', function () {
            table.ajax.reload();
        });

        $('#btn_reset_filters').on('click', function () {
            $('#filter_student_id').val('').trigger('change');
            $('#filter_fee_id').val('').trigger('change');
            $('#filter_status').val('').trigger('change');
        });

        // Auto-fill total amount on selecting fee
        $('#add_fee_id').on('change', function () {
            const feeVal = $(this).val();
            if (feeVal && fees[feeVal]) {
                $('#add_total_amount').val(fees[feeVal]);
            }
        });

        function toast(type, message) {
            const mappedType = type === 'danger' ? 'error' : type;
            if (window.toastr && typeof window.toastr[mappedType] === 'function') {
                window.toastr[mappedType](message);
            } else if (window.notify && typeof window.notify[mappedType] === 'function') {
                window.notify[mappedType](message);
            }
        }

        const studentFeeCache = {};
        const paymentCache = {};

        function escapeHtml(value) {
            return $('<div>').text(value ?? '-').html();
        }

        function paymentStatusBadge(status) {
            const classes = { paid: 'success', pending: 'warning', failed: 'danger', refunded: 'info' };
            return '<span class="badge bg-label-' + (classes[status] || 'secondary') + '">' + escapeHtml(status) + '</span>';
        }

        // Opens the separate Payments Table for a student fee.
        function loadDetails(id) {
            $.get(baseUrl + '/' + id, function (res) {
                const data = res.data;
                studentFeeCache[id] = data;
                (data.payments || []).forEach(function (payment) { paymentCache[payment.id] = payment; });

                const studentName = data.student?.name || data.student?.translations?.[0]?.name || '-';
                const feeName = data.fee?.name || data.fee?.translations?.[0]?.name || '-';
                const rows = (data.payments || []).map(function (payment, index) {
                    return '<tr>' +
                        '<td>' + (index + 1) + '</td>' +
                        '<td>' + escapeHtml(payment.payment_date || '-') + '</td>' +
                        '<td class="fw-bold">' + parseFloat(payment.amount || 0).toFixed(2) + '</td>' +
                        '<td>' + escapeHtml(payment.invoice_number || '-') + '</td>' +
                        '<td>' + escapeHtml(payment.payment_number || '-') + '</td>' +
                        '<td>' + paymentStatusBadge(payment.status) + '</td>' +
                        '<td class="text-center"><div class="d-inline-flex gap-1">' +
                            '<button type="button" class="btn btn-icon btn-sm btn-outline-info btn-payment-details" data-id="' + payment.id + '" title="{{ __("Details") }}"><i class="icon-base bx bx-show"></i></button>' +
                            '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-payment" data-id="' + payment.id + '" title="{{ __("Edit") }}"><i class="icon-base bx bx-edit"></i></button>' +
                            '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-payment" data-id="' + payment.id + '" title="{{ __("Delete") }}"><i class="icon-base bx bx-trash"></i></button>' +
                        '</div></td>' +
                    '</tr>';
                }).join('');

                $('#studentFeeDetailsBody').html(
                    '<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">' +
                        '<div><strong>{{ __("Student") }}:</strong> ' + escapeHtml(studentName) + ' <span class="text-muted">|</span> <strong>{{ __("Academic Fee") }}:</strong> ' + escapeHtml(feeName) + '</div>' +
                        '<button type="button" class="btn btn-success btn-sm btn-add-payment-from-table" data-id="' + id + '"><i class="icon-base bx bx-plus me-1"></i>{{ __("إضافة دفعة") }}</button>' +
                    '</div>' +
                    '<div class="table-responsive border rounded">' +
                        '<table class="table table-hover align-middle mb-0 text-nowrap"><thead class="table-light"><tr>' +
                            '<th>#</th><th>{{ __("Payment Date") }}</th><th>{{ __("Amount") }}</th><th>{{ __("Invoice Number") }}</th><th>{{ __("Payment Number") }}</th><th>{{ __("Payment Status") }}</th><th class="text-center">{{ __("Actions") }}</th>' +
                        '</tr></thead><tbody>' +
                        (rows || '<tr><td colspan="7" class="text-center text-muted py-4">{{ __("No payments found") }}</td></tr>') +
                        '</tbody></table></div>'
                );
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalStudentFeeDetails')).show();
            });
        }

        function openPaymentForm(studentFeeId, payment = null) {
            const data = studentFeeCache[studentFeeId];
            const form = $('#formAddStudentFeePayment')[0];
            form.reset();
            $('#payment_student_fee_id').val(studentFeeId);
            $('#payment_id').val(payment?.id || '');
            $('#paymentModalTitle').text(payment ? '{{ __("تعديل المدفوعة") }}' : '{{ __("إضافة دفعة") }}');
            $('#btn_submit_payment').text(payment ? '{{ __("Update") }}' : '{{ __("Save Payment") }}');

            const installments = (data?.installments || []).filter(function (i) {
                return payment ? true : parseFloat(i.paid_amount || 0) < parseFloat(i.amount);
            });
            $('#payment_installment_id').html('<option value="">{{ __("Next unpaid installment") }}</option>' +
                installments.map(i => '<option value="' + i.id + '">' + '{{ __("دفعة") }} #' + i.installment_number + ' - ' + (parseFloat(i.amount) - parseFloat(i.paid_amount || 0)).toFixed(2) + '</option>').join(''));

            if (payment) {
                $('#payment_installment_id').val(payment.academic_fee_installment_id || '');
                $('#payment_amount').val(payment.amount || '');
                $('#payment_date').val(payment.payment_date || '');
                $('#formAddStudentFeePayment [name="status"]').val(payment.status || 'paid');
                $('#formAddStudentFeePayment [name="invoice_number"]').val(payment.invoice_number || '');
                $('#formAddStudentFeePayment [name="payment_number"]').val(payment.payment_number || '');
                $('#formAddStudentFeePayment [name="transaction_number"]').val(payment.transaction_number || '');
                $('#formAddStudentFeePayment [name="reference_number"]').val(payment.reference_number || '');
                $('#formAddStudentFeePayment [name="tracking_number"]').val(payment.tracking_number || '');
            } else {
                const first = installments[0];
                if (first) {
                    $('#payment_installment_id').val(first.id);
                    $('#payment_amount').val((parseFloat(first.amount) - parseFloat(first.paid_amount || 0)).toFixed(2));
                }
            }
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalAddStudentFeePayment')).show();
        }

        $(document).on('click', '.btn-open-student-payments', function () { loadDetails($(this).data('id')); });
        $(document).on('click', '.btn-add-payment-from-table', function () { openPaymentForm($(this).data('id')); });
        $(document).on('click', '.btn-payment-details', function () {
            const payment = paymentCache[$(this).data('id')];
            if (!payment) return;
            $('#paymentDetailsBody').html(
                '<div class="row g-3">' +
                    '<div class="col-md-6"><strong>{{ __("Payment Date") }}:</strong> ' + escapeHtml(payment.payment_date) + '</div>' +
                    '<div class="col-md-6"><strong>{{ __("Amount") }}:</strong> ' + parseFloat(payment.amount || 0).toFixed(2) + '</div>' +
                    '<div class="col-md-6"><strong>{{ __("Invoice Number") }}:</strong> ' + escapeHtml(payment.invoice_number) + '</div>' +
                    '<div class="col-md-6"><strong>{{ __("Payment Number") }}:</strong> ' + escapeHtml(payment.payment_number) + '</div>' +
                    '<div class="col-md-4"><strong>{{ __("Transaction Number") }}:</strong> ' + escapeHtml(payment.transaction_number) + '</div>' +
                    '<div class="col-md-4"><strong>{{ __("Reference Number") }}:</strong> ' + escapeHtml(payment.reference_number) + '</div>' +
                    '<div class="col-md-4"><strong>{{ __("Tracking Number") }}:</strong> ' + escapeHtml(payment.tracking_number) + '</div>' +
                    '<div class="col-md-6"><strong>{{ __("Payment Status") }}:</strong> ' + paymentStatusBadge(payment.status) + '</div>' +
                '</div>'
            );
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPaymentDetails')).show();
        });
        $(document).on('click', '.btn-edit-payment', function () {
            const payment = paymentCache[$(this).data('id')];
            if (payment) openPaymentForm(payment.academic_student_fee_id, payment);
        });
        $(document).on('click', '.btn-edit-student-fee', function () {
            const id = $(this).data('id');
            $.get(baseUrl + '/' + id, function (res) {
                const data = res.data;
                $('#student_fee_id').val(data.id);
                $('#add_student_id').val(data.academic_student_id).trigger('change');
                $('#add_fee_id').val(data.academic_fee_id).trigger('change');
                $('#add_total_amount').val(data.total_amount);
                $('#add_installments_count').val(data.installments_count);
                $('#add_start_date').val(data.start_date);
                $('#studentFeeModalTitle').text('{{ __("Edit Student Fee") }}');
                $('#btn_submit_student_fee').text('{{ __("Update") }}');
                bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('offcanvasAddStudentFee')).show();
            });
        });
        $(document).on('click', '.btn-delete-student-fee', function () {
            if (!confirm('{{ __("Are you sure you want to delete this student fee?") }}')) return;
            $.ajax({ url: baseUrl + '/' + $(this).data('id'), type: 'DELETE' })
                .done(function (res) { toast('success', res.message); table.ajax.reload(null, false); })
                .fail(function (xhr) { toast('error', xhr.responseJSON?.message || '{{ __("Error deleting student fee.") }}'); });
        });
        $(document).on('click', '.btn-delete-payment', function () {
            if (!confirm('{{ __("Are you sure you want to delete this payment?") }}')) return;
            $.ajax({ url: @json(url('academic/student-fee-payments')) + '/' + $(this).data('id'), type: 'DELETE' })
                .done(function (res) {
                    toast('success', res.message);
                    const currentId = $('#payment_student_fee_id').val();
                    if (currentId) loadDetails(currentId);
                    table.ajax.reload(null, false);
                })
                .fail(function (xhr) { toast('error', xhr.responseJSON?.message || '{{ __("Error deleting payment.") }}'); });
        });

        // Submit Add/Edit Student Fee Form
        $('#formAddStudentFee').on('submit', function (e) {
            e.preventDefault();
            const $btn = $('#btn_submit_student_fee');
            $btn.prop('disabled', true);
            const id = $('#student_fee_id').val();
            $.ajax({
                url: id ? baseUrl + '/' + id : baseUrl,
                type: id ? 'PUT' : 'POST',
                data: $(this).serialize()
            })
                .done(function (res) {
                    toast('success', res.message || '{{ __("Student fee saved successfully.") }}');
                    bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasAddStudentFee')).hide();
                    $('#formAddStudentFee')[0].reset();
                    $('#student_fee_id').val('');
                    $('#studentFeeModalTitle').text('{{ __("Assign Fee to Student") }}');
                    $('#btn_submit_student_fee').text('{{ __("Save") }}');
                    $('#add_student_id, #add_fee_id').val('').trigger('change');
                    table.ajax.reload(null, false);
                })
                .fail(function (xhr) {
                    toast('error', xhr.responseJSON?.message || '{{ __("Error saving student fee.") }}');
                })
                .always(function () {
                    $btn.prop('disabled', false);
                });
        });

        // Submit Add/Edit Payment Form
        $('#formAddStudentFeePayment').on('submit', function (e) {
            e.preventDefault();
            const id = $('#payment_student_fee_id').val();
            const paymentId = $('#payment_id').val();
            const $btn = $(this).find('button[type="submit"]');
            $btn.prop('disabled', true);
            $.ajax({
                url: paymentId ? @json(url('academic/student-fee-payments')) + '/' + paymentId : baseUrl + '/' + id + '/payments',
                type: paymentId ? 'PUT' : 'POST',
                data: $(this).serialize()
            })
                .done(function (res) {
                    toast('success', res.message || '{{ __("Payment saved successfully.") }}');
                    bootstrap.Modal.getInstance(document.getElementById('modalAddStudentFeePayment')).hide();
                    $('#formAddStudentFeePayment')[0].reset();
                    $('#payment_id').val('');
                    $('#paymentModalTitle').text('{{ __("إضافة دفعة") }}');
                    $('#btn_submit_payment').text('{{ __("Save Payment") }}');
                    if (id) loadDetails(id);
                    table.ajax.reload(null, false);
                })
                .fail(function (xhr) {
                    toast('error', xhr.responseJSON?.message || '{{ __("Error recording payment.") }}');
                })
                .always(function () {
                    $btn.prop('disabled', false);
                });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initStudentFees);
    } else {
        initStudentFees();
    }
})();
</script>
@endsection
