@extends('layouts/contentNavbarLayout')

@section('title', __('Payment Matching & Allocations') . ' - ' . config('variables.templateName'))

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-link text-primary font-24"></i>
            <span>{{ __('Payment Matching & Allocations') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Payment Matching') }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('accounting.payment-matchings.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
        <i class="icon-base bx bx-plus font-18"></i>
        <span>{{ __('New Payment Matching') }}</span>
    </a>
</div>

<div id="alertContainer">
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
</div>

<!-- Advanced Filters Toolbar -->
<div class="card shadow-sm border-0 mb-4" id="filtersCard">
    <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h6 class="fw-bold mb-0 text-heading">{{ __('Filter Payment Matchings') }}</h6>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Quick Date Presets -->
            <div class="btn-group btn-group-sm" role="group" id="quickDateFilters" aria-label="Quick Date Ranges">
                <button type="button" class="btn btn-primary active" data-range="all">{{ __('All') }}</button>
                <button type="button" class="btn btn-outline-secondary" data-range="today">{{ __('Today') }}</button>
                <button type="button" class="btn btn-outline-secondary" data-range="month">{{ __('This Month') }}</button>
                <button type="button" class="btn btn-outline-secondary" data-range="quarter">{{ __('This Quarter') }}</button>
                <button type="button" class="btn btn-outline-secondary" data-range="year">{{ __('This Year') }}</button>
            </div>

            <button type="button" id="btnResetFilters" class="btn btn-label-secondary btn-sm shadow-xs" title="{{ __('Reset all filters') }}">
                <span class="fw-semibold">{{ __('Reset') }}</span>
            </button>
        </div>
    </div>

    <div class="card-body pt-3 pb-3">
        <div class="row g-3">
            <!-- 1. Accounting Entity -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1" for="filter_entity_id">
                    {{ __('Accounting Entity') }}
                </label>
                <select id="filter_entity_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('All Entities') }}" data-allow-clear="true">
                    <option value="">{{ __('All Entities') }}</option>
                    @foreach($entities as $entity)
                        <option value="{{ $entity->id }}">
                            {{ $entity->translate(app()->getLocale())?->name ?? $entity->translate('ar')?->name ?? $entity->code }} ({{ $entity->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Account -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1" for="filter_account_id">
                    {{ __('Accounting Account') }}
                </label>
                <select id="filter_account_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('All Accounts') }}" data-allow-clear="true">
                    <option value="">{{ __('All Accounts') }}</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" data-entity="{{ $acc->entity_id }}">
                            {{ $acc->code }} - {{ $acc->translate(app()->getLocale())?->name ?? $acc->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 3. From Date -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1" for="filter_date_from">
                    {{ __('From Date') }}
                </label>
                <input type="date" id="filter_date_from" class="form-control">
            </div>

            <!-- 4. To Date -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1" for="filter_date_to">
                    {{ __('To Date') }}
                </label>
                <input type="date" id="filter_date_to" class="form-control">
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table class="table table-hover border-top w-100 text-nowrap" id="matchingsTable">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th style="min-width: 150px;">{{ __('Matching ID & Date') }}</th>
                    <th style="min-width: 220px;">{{ __('Debit Entry (Invoice/Payment)') }}</th>
                    <th style="min-width: 220px;">{{ __('Credit Entry (Payment/Credit)') }}</th>
                    <th class="text-end" style="min-width: 140px;">{{ __('Matched Amount') }}</th>
                    <th style="min-width: 120px;">{{ __('Matched By') }}</th>
                    <th class="text-center" style="width: 80px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Unmatch Confirmation Modal -->
<div class="modal fade" id="unmatchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-unlink font-32 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Unmatch Confirmation') }}</h5>
                <p class="text-muted mb-0">{{ __('Are you sure you want to remove this payment allocation?') }}</p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" id="btnConfirmUnmatch" class="btn btn-danger">{{ __('Yes, Unlink') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;
    const dataUrl = "{{ route('accounting.payment-matchings.data') }}";

    function alertBox(type, message) {
        $('#alertContainer').html(`
            <div class="alert alert-${type} alert-dismissible d-flex align-items-center mb-4 shadow-xs" role="alert">
                <i class="icon-base bx ${type === 'success' ? 'bx-check-circle' : 'bx-error-circle'} me-2 font-20"></i>
                <div>${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
        $('html, body').animate({ scrollTop: 0 }, 200);
    }

    const table = $('#matchingsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: dataUrl,
            data: function (d) {
                d.entity_id = $('#filter_entity_id').val();
                d.account_id = $('#filter_account_id').val();
                d.date_from = $('#filter_date_from').val();
                d.date_to = $('#filter_date_to').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, className: 'text-center align-middle' },
            { data: 'matching_info', name: 'id', className: 'align-middle' },
            { data: 'debit_info', name: 'debit_line_id', className: 'align-middle' },
            { data: 'credit_info', name: 'credit_line_id', className: 'align-middle' },
            { data: 'amount_fmt', name: 'matched_amount', className: 'text-end align-middle' },
            { data: 'user_info', name: 'matched_by', className: 'align-middle' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center align-middle' }
        ],
        order: [[1, 'desc']],
        language: {
            search: '',
            searchPlaceholder: "{{ __('Search...') }}",
            paginate: {
                previous: '<i class="bx bx-chevron-left"></i>',
                next: '<i class="bx bx-chevron-right"></i>'
            }
        }
    });

    function formatDate(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    // Filter accounts in dropdown by entity
    function filterAccountsByEntity() {
        const selectedEntity = $('#filter_entity_id').val();
        const accountSelect = $('#filter_account_id');
        const currentAccount = accountSelect.val();
        let isCurrentValid = false;

        accountSelect.find('option').each(function () {
            const accEntity = $(this).data('entity');
            if (!$(this).val() || !selectedEntity || String(accEntity) === String(selectedEntity)) {
                $(this).prop('disabled', false).show();
                if ($(this).val() && $(this).val() === currentAccount) {
                    isCurrentValid = true;
                }
            } else {
                $(this).prop('disabled', true).hide();
            }
        });

        if (currentAccount && !isCurrentValid) {
            accountSelect.val('').trigger('change.select2');
        } else {
            accountSelect.trigger('change.select2');
        }
    }

    // Quick Date Presets
    $('#quickDateFilters button').on('click', function () {
        $('#quickDateFilters button').removeClass('active btn-primary').addClass('btn-outline-secondary');
        $(this).addClass('active btn-primary').removeClass('btn-outline-secondary');

        const range = $(this).data('range');
        const now = new Date();

        if (range === 'all') {
            $('#filter_date_from').val('');
            $('#filter_date_to').val('');
        } else if (range === 'today') {
            const str = formatDate(now);
            $('#filter_date_from').val(str);
            $('#filter_date_to').val(str);
        } else if (range === 'month') {
            const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            $('#filter_date_from').val(formatDate(firstDay));
            $('#filter_date_to').val(formatDate(lastDay));
        } else if (range === 'quarter') {
            const currentQuarter = Math.floor(now.getMonth() / 3);
            const firstDay = new Date(now.getFullYear(), currentQuarter * 3, 1);
            const lastDay = new Date(now.getFullYear(), (currentQuarter + 1) * 3, 0);
            $('#filter_date_from').val(formatDate(firstDay));
            $('#filter_date_to').val(formatDate(lastDay));
        } else if (range === 'year') {
            const firstDay = new Date(now.getFullYear(), 0, 1);
            const lastDay = new Date(now.getFullYear(), 11, 31);
            $('#filter_date_from').val(formatDate(firstDay));
            $('#filter_date_to').val(formatDate(lastDay));
        }

        table.ajax.reload();
    });

    $('#filter_entity_id').on('change', function () {
        filterAccountsByEntity();
        table.ajax.reload();
    });

    $('#filter_account_id, #filter_date_from, #filter_date_to').on('change', function () {
        table.ajax.reload();
    });

    $('#btnResetFilters').on('click', function () {
        $('#filter_entity_id').val('').trigger('change.select2');
        $('#filter_account_id').val('').trigger('change.select2');
        $('#filter_date_from').val('');
        $('#filter_date_to').val('');
        $('#quickDateFilters button').removeClass('active btn-primary').addClass('btn-outline-secondary');
        $('#quickDateFilters button[data-range="all"]').addClass('active btn-primary').removeClass('btn-outline-secondary');
        table.ajax.reload();
    });

    let deleteMatchingId = null;
    const unmatchModalEl = document.getElementById('unmatchModal');
    const unmatchModal = unmatchModalEl ? bootstrap.Modal.getOrCreateInstance(unmatchModalEl) : null;

    $(document).on('click', '.btn-unmatch', function () {
        deleteMatchingId = $(this).data('id');
        if (unmatchModal) unmatchModal.show();
    });

    $('#btnConfirmUnmatch').on('click', function () {
        if (!deleteMatchingId) return;
        const btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: "{{ url('accounting/payment-matchings') }}/" + deleteMatchingId,
            type: 'DELETE',
            data: { _token: "{{ csrf_token() }}" },
            success: function (res) {
                if (unmatchModal) unmatchModal.hide();
                table.ajax.reload();
                alertBox('success', res.message || '{{ __("Matching removed successfully.") }}');
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || '{{ __("Error removing matching.") }}';
                alertBox('danger', msg);
            },
            complete: function () {
                btn.prop('disabled', false);
                deleteMatchingId = null;
            }
        });
    });
});
</script>
@endsection
