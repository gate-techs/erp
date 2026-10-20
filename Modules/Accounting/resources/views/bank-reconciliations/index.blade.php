@extends('layouts/contentNavbarLayout')

@section('title', __('Bank Reconciliations') . ' - ' . config('variables.templateName'))

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-check-shield text-primary font-24"></i>
            <span>{{ __('Bank Reconciliations') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Bank Reconciliations') }}</li>
            </ol>
        </nav>
    </div>
    @can('accounting.bank-reconciliations.create')
    <a href="{{ route('accounting.bank-reconciliations.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
        <i class="icon-base bx bx-plus font-18"></i><span>{{ __('New Bank Reconciliation') }}</span>
    </a>
    @endcan
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

<!-- Advanced Filters Toolbar (Identical to journal-entries) -->
<div class="card shadow-sm border-0 mb-4" id="filtersCard">
    <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h6 class="fw-bold mb-0 text-heading">{{ __('Filter Bank Reconciliations') }}</h6>
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

            <!-- 2. Bank Account -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1" for="filter_cash_account_id">
                    {{ __('Bank Account') }}
                </label>
                <select id="filter_cash_account_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('All Bank Accounts') }}" data-allow-clear="true">
                    <option value="">{{ __('All Bank Accounts') }}</option>
                    @foreach($bankAccounts as $acc)
                        <option value="{{ $acc->id }}" data-entity="{{ $acc->entity_id }}">
                            {{ $acc->translate(app()->getLocale())?->name ?? $acc->code }} ({{ $acc->bank_name ?? $acc->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Status -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1" for="filter_status">
                    {{ __('Reconciliation Status') }}
                </label>
                <select id="filter_status" class="form-select select2" data-control="select2" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                    <option value="all">{{ __('All Statuses') }}</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. From Date -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1" for="filter_from_date">
                    {{ __('From Date') }}
                </label>
                <input type="date" id="filter_from_date" class="form-control">
            </div>

            <!-- 5. To Date -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1" for="filter_to_date">
                    {{ __('To Date') }}
                </label>
                <input type="date" id="filter_to_date" class="form-control">
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table class="table table-hover border-top w-100 text-nowrap" id="reconciliationsTable">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th style="min-width: 200px;">{{ __('Reconciliation # & Date') }}</th>
                    <th>{{ __('Accounting Entity') }}</th>
                    <th style="min-width: 220px;">{{ __('Bank Account') }}</th>
                    <th class="text-end" style="min-width: 140px;">{{ __('Statement Balance') }}</th>
                    <th class="text-end" style="min-width: 140px;">{{ __('Book Balance') }}</th>
                    <th class="text-center" style="min-width: 120px;">{{ __('Difference') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 140px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Reopen Modal -->
<div class="modal fade" id="reopenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="avatar avatar-xl bg-label-warning mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-lock-open font-32 text-warning"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Reopen Reconciliation') }}</h5>
                <p class="text-muted mb-0">{{ __('Are you sure you want to reopen this reconciliation to draft status?') }}</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 pb-4">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" id="btnConfirmReopen" class="btn btn-warning">{{ __('Yes, Reopen') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-trash font-32 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Confirm Delete') }}</h5>
                <p class="text-muted mb-0">
                    {{ __('Are you sure you want to delete:') }}
                    <br>
                    <strong id="delete_target_name" class="text-danger"></strong>؟
                </p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 pb-4">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" id="btnConfirmDelete" class="btn btn-danger">{{ __('Yes, Delete') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;
    const dataUrl = "{{ route('accounting.bank-reconciliations.data') }}";
    const baseUrl = "{{ url('accounting/bank-reconciliations') }}";

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

    const table = $('#reconciliationsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: dataUrl,
            data: function (d) {
                d.entity_id = $('#filter_entity_id').val();
                d.cash_account_id = $('#filter_cash_account_id').val();
                d.status = $('#filter_status').val();
                d.from_date = $('#filter_from_date').val();
                d.to_date = $('#filter_to_date').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, className: 'text-center align-middle' },
            { data: 'rec_info', name: 'reconciliation_number', className: 'align-middle' },
            { data: 'entity_name', name: 'entity_id', orderable: false, searchable: false, className: 'align-middle' },
            { data: 'bank_info', name: 'cashAccount.code', className: 'align-middle' },
            { data: 'statement_balance_fmt', name: 'statement_balance', className: 'text-end align-middle' },
            { data: 'book_balance_fmt', name: 'book_balance', className: 'text-end align-middle' },
            { data: 'difference_fmt', name: 'difference', className: 'text-center align-middle' },
            { data: 'status_badge', name: 'status', className: 'text-center align-middle' },
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

    // Filter banks in dropdown by entity
    function filterBanksByEntity() {
        const selectedEntity = $('#filter_entity_id').val();
        const bankSelect = $('#filter_cash_account_id');
        const currentBank = bankSelect.val();
        let isCurrentValid = false;

        bankSelect.find('option').each(function () {
            const bEntity = $(this).data('entity');
            if (!$(this).val() || !selectedEntity || String(bEntity) === String(selectedEntity)) {
                $(this).prop('disabled', false).show();
                if ($(this).val() && $(this).val() === currentBank) {
                    isCurrentValid = true;
                }
            } else {
                $(this).prop('disabled', true).hide();
            }
        });

        if (currentBank && !isCurrentValid) {
            bankSelect.val('').trigger('change.select2');
        } else {
            bankSelect.trigger('change.select2');
        }
    }

    // Quick Date Presets
    $('#quickDateFilters button').on('click', function () {
        $('#quickDateFilters button').removeClass('active btn-primary').addClass('btn-outline-secondary');
        $(this).addClass('active btn-primary').removeClass('btn-outline-secondary');

        const range = $(this).data('range');
        const now = new Date();

        if (range === 'all') {
            $('#filter_from_date').val('');
            $('#filter_to_date').val('');
        } else if (range === 'today') {
            const str = formatDate(now);
            $('#filter_from_date').val(str);
            $('#filter_to_date').val(str);
        } else if (range === 'month') {
            const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            $('#filter_from_date').val(formatDate(firstDay));
            $('#filter_to_date').val(formatDate(lastDay));
        } else if (range === 'quarter') {
            const currentQuarter = Math.floor(now.getMonth() / 3);
            const firstDay = new Date(now.getFullYear(), currentQuarter * 3, 1);
            const lastDay = new Date(now.getFullYear(), (currentQuarter + 1) * 3, 0);
            $('#filter_from_date').val(formatDate(firstDay));
            $('#filter_to_date').val(formatDate(lastDay));
        } else if (range === 'year') {
            const firstDay = new Date(now.getFullYear(), 0, 1);
            const lastDay = new Date(now.getFullYear(), 11, 31);
            $('#filter_from_date').val(formatDate(firstDay));
            $('#filter_to_date').val(formatDate(lastDay));
        }

        table.ajax.reload();
    });

    $('#filter_entity_id').on('change', function () {
        filterBanksByEntity();
        table.ajax.reload();
    });

    $('#filter_cash_account_id, #filter_status, #filter_from_date, #filter_to_date').on('change', function () {
        table.ajax.reload();
    });

    $('#btnResetFilters').on('click', function () {
        $('#filter_entity_id').val('').trigger('change.select2');
        $('#filter_cash_account_id').val('').trigger('change.select2');
        $('#filter_status').val('all').trigger('change.select2');
        $('#filter_from_date').val('');
        $('#filter_to_date').val('');
        $('#quickDateFilters button').removeClass('active btn-primary').addClass('btn-outline-secondary');
        $('#quickDateFilters button[data-range="all"]').addClass('active btn-primary').removeClass('btn-outline-secondary');
        table.ajax.reload();
    });

    let currentActionId = null;

    // Reopen Modal
    const reopenModalEl = document.getElementById('reopenModal');
    const reopenModal = reopenModalEl ? bootstrap.Modal.getOrCreateInstance(reopenModalEl) : null;

    $(document).on('click', '.btn-reopen-rec', function () {
        currentActionId = $(this).data('id');
        if (reopenModal) reopenModal.show();
    });

    $('#btnConfirmReopen').on('click', function () {
        if (!currentActionId) return;
        const btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: baseUrl + '/' + currentActionId + '/reopen',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                if (reopenModal) reopenModal.hide();
                table.ajax.reload(null, false);
                alertBox('success', res.message || "{{ __('Reopened successfully.') }}");
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message || 'Error reopening reconciliation');
            },
            complete: function () {
                btn.prop('disabled', false);
            }
        });
    });

    // Delete Modal
    const deleteModalEl = document.getElementById('deleteModal');
    const deleteModal = deleteModalEl ? bootstrap.Modal.getOrCreateInstance(deleteModalEl) : null;

    $(document).on('click', '.btn-delete-rec', function () {
        currentActionId = $(this).data('id');
        const number = $(this).data('number');
        $('#delete_target_name').text(number);
        if (deleteModal) deleteModal.show();
    });

    $('#btnConfirmDelete').on('click', function () {
        if (!currentActionId) return;
        const btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: baseUrl + '/' + currentActionId,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                if (deleteModal) deleteModal.hide();
                table.ajax.reload(null, false);
                alertBox('success', res.message || "{{ __('Deleted successfully.') }}");
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message || 'Error deleting reconciliation');
            },
            complete: function () {
                btn.prop('disabled', false);
            }
        });
    });
});
</script>
@endsection
