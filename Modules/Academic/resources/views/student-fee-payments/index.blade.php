@extends('layouts/contentNavbarLayout')

@section('title', __('المدفوعات') . ' - ' . config('variables.templateName'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('academic.student-fees.index') }}" class="text-muted small">{{ __('Student Fees') }}</a></li>
                <li class="breadcrumb-item active text-primary small">{{ __('المدفوعات') }}</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-money text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('المدفوعات') }}</h4>
        </div>
    </div>
    <button type="button" id="btn_add_payment" class="d-none" data-bs-toggle="modal" data-bs-target="#paymentFormModal">
        <i class="icon-base bx bx-plus"></i>{{ __('إضافة دفعة') }}
    </button>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Student Fee') }}</label>
                <select id="filter_student_fee_id" class="select2 select2-selection__rendered form-select">
                    <option value="">{{ __('All Student Fees') }}</option>
                    @foreach ($studentFees as $fee)
                        <option value="{{ $fee->id }}" @selected($studentFeeId === $fee->id)>
                            {{ $fee->student?->translate(app()->getLocale())?->name ?? $fee->student?->translate('ar')?->name ?? '-' }}
                            - {{ $fee->fee?->translate(app()->getLocale())?->name ?? $fee->fee?->translate('ar')?->name ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Payment Status') }}</label>
                <select id="filter_status" class="select2 select2-selection__rendered form-select">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="paid">{{ __('Paid') }}</option>
                    <option value="unpaid">{{ __('غير مدفوع') }}</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <button type="button" id="btn_reset_filters" class="btn btn-label-secondary btn-sm w-100">
                    <i class="icon-base bx bx-reset me-1"></i>{{ __('Reset') }}
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="paymentsTable" class="table table-hover border-top w-100 text-nowrap">
            <thead><tr>
                <th>#</th><th>{{ __('Student') }}</th><th>{{ __('Academic Fee') }}</th>
                <th>{{ __('دفعة') }}</th><th>{{ __('Payment Date') }}</th><th>{{ __('Amount') }}</th>
                <th>{{ __('Invoice Number') }}</th><th>{{ __('Payment Number') }}</th>
                <th>{{ __('Payment Status') }}</th><th class="text-center">{{ __('Actions') }}</th>
            </tr></thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="paymentFormModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="paymentFormTitle">{{ __('إضافة دفعة') }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form id="paymentForm">
                    <input type="hidden" id="payment_id">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">{{ __('Amount') }}</label><input type="number" step="0.01" min="0.01" name="amount" id="payment_amount" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">{{ __('Payment Date') }}</label><input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required></div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-4"><button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-primary" id="btn_save_payment">تحديث</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">{{ __('تفاصيل المدفوعة') }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body" id="paymentDetailsBody"></div></div></div>
</div>
@endsection

@section('page-script')
<script>
(function () {
    'use strict';
    function initPayments() {
        const $ = window.jQuery || window.$;
        if (!$ || !$.fn.DataTable) return setTimeout(initPayments, 50);
        const baseUrl = @json(url('academic/student-fee-payments'));
        const initialStudentFeeId = @json($studentFeeId);
        const cache = {};
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), Accept: 'application/json' } });
        $('.select2').select2({ theme: 'bootstrap-5', width: '100%', allowClear: true, dir: $('html').attr('dir') === 'rtl' ? 'rtl' : 'ltr' });
        if (initialStudentFeeId) {
            $('#filter_student_fee_id').val(String(initialStudentFeeId)).trigger('change.select2');
        }
        const table = $('#paymentsTable').DataTable({
            processing: true, serverSide: true, pagingType: 'simple_numbers',
            ajax: { url: @json(route('academic.student-fees.payments.data')), data: function (d) { d.academic_student_fee_id = $('#filter_student_fee_id').val() || initialStudentFeeId || ''; d.status = $('#filter_status').val(); }, dataSrc: function (json) { (json.data || []).forEach(row => cache[row.id] = row); return json.data; } },
            columns: [
                { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false },
                { data: 'student_name', name: 'academic_student_fee_id' },
                { data: 'fee_name', name: 'academic_student_fee_id' },
                { data: 'installment_name', name: 'academic_fee_installment_id' },
                { data: 'payment_date', name: 'payment_date' },
                { data: 'amount', name: 'amount' },
                { data: 'invoice_number', name: 'invoice_number' },
                { data: 'payment_number', name: 'payment_number' },
                { data: 'status_badge', name: 'status' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[4, 'desc']], rawColumns: ['amount', 'status_badge', 'actions']
        });
        $('#filter_student_fee_id, #filter_status').on('change', () => table.ajax.reload());
        $('#btn_reset_filters').on('click', () => { $('#filter_student_fee_id, #filter_status').val('').trigger('change'); });
        $('#btn_add_payment').on('click', function () { $('#paymentForm')[0].reset(); $('#payment_id').val(''); $('#paymentFormTitle').text('{{ __("إضافة دفعة") }}'); $('#payment_student_fee_id').val($('#filter_student_fee_id').val()).trigger('change'); });
        function esc(v) { return $('<div>').text(v ?? '-').html(); }
        $(document).on('click', '.btn-payment-details', function () {
            const p = cache[$(this).data('id')]; if (!p) return;
            $('#paymentDetailsBody').html('<div class="row g-3"><div class="col-md-6"><b>{{ __("Payment Date") }}:</b> '+esc(p.payment_date)+'</div><div class="col-md-6"><b>{{ __("Amount") }}:</b> '+p.amount+'</div><div class="col-md-6"><b>{{ __("Invoice Number") }}:</b> '+esc(p.invoice_number)+'</div><div class="col-md-6"><b>{{ __("Payment Number") }}:</b> '+esc(p.payment_number)+'</div><div class="col-md-4"><b>{{ __("Transaction Number") }}:</b> '+esc(p.transaction_number)+'</div><div class="col-md-4"><b>{{ __("Reference Number") }}:</b> '+esc(p.reference_number)+'</div><div class="col-md-4"><b>{{ __("Tracking Number") }}:</b> '+esc(p.tracking_number)+'</div></div>');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('paymentDetailsModal')).show();
        });
        $(document).on('click', '.btn-edit-payment', function () {
            const p = cache[$(this).data('id')]; if (!p) return;
            $('#payment_id').val(p.id);
            $('#payment_amount').val(p.raw_amount ?? p.amount); $('#payment_date').val(p.payment_date);
            $('#paymentFormTitle').text('تعديل المدفوعة'); $('#btn_save_payment').text('تحديث'); bootstrap.Modal.getOrCreateInstance(document.getElementById('paymentFormModal')).show();
        });
        $(document).on('click', '.btn-delete-payment', function () {
            if (!confirm('{{ __("Are you sure you want to delete this payment?") }}')) return;
            $.ajax({ url: baseUrl + '/' + $(this).data('id'), type: 'DELETE' }).done(res => { if (window.toastr) toastr.success(res.message); table.ajax.reload(null, false); }).fail(xhr => { if (window.toastr) toastr.error(xhr.responseJSON?.message || '{{ __("Error deleting payment.") }}'); });
        });
        $(document).on('click', '.btn-toggle-payment-status', function () {
            const button = $(this);
            $.ajax({ url: baseUrl + '/' + button.data('id') + '/toggle-status', type: 'PATCH' })
                .done(res => { if (window.toastr) toastr.success(res.message); table.ajax.reload(null, false); })
                .fail(xhr => { if (window.toastr) toastr.error(xhr.responseJSON?.message || '{{ __("Error updating payment status.") }}'); });
        });
        $('#paymentForm').on('submit', function (e) {
            e.preventDefault(); const id = $('#payment_id').val();
            $.ajax({ url: baseUrl + '/' + id, type: 'PUT', data: $(this).serialize() }).done(res => { if (window.toastr) toastr.success(res.message); bootstrap.Modal.getInstance(document.getElementById('paymentFormModal')).hide(); table.ajax.reload(null, false); }).fail(xhr => { if (window.toastr) toastr.error(xhr.responseJSON?.message || '{{ __("Error recording payment.") }}'); });
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initPayments); else initPayments();
})();
</script>
@endsection
