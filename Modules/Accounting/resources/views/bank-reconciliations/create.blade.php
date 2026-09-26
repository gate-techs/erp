@extends('layouts/contentNavbarLayout')

@section('title', ($reconciliation ? __('Edit Bank Reconciliation') : __('New Bank Reconciliation')) . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
    .sticky-rec-bar {
        position: sticky;
        bottom: 1rem;
        z-index: 1020;
        backdrop-filter: blur(12px);
        background: rgba(var(--bs-card-bg-rgb), 0.94);
        border: 1px solid var(--bs-border-color);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        border-radius: 0.75rem;
    }
    .transaction-row {
        cursor: pointer;
        transition: background-color 0.15s ease-in-out;
    }
    .transaction-row:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.04) !important;
    }
    .transaction-row.is-cleared {
        background-color: rgba(var(--bs-success-rgb), 0.06) !important;
    }
    .financial-num {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        font-weight: 700;
        letter-spacing: 0.3px;
        font-variant-numeric: tabular-nums;
    }
</style>
@endsection

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-check-shield text-primary font-24"></i>
            <span>{{ $reconciliation ? __('Edit Bank Reconciliation') . ' (' . $reconciliation->reconciliation_number . ')' : __('New Bank Reconciliation') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('accounting.bank-reconciliations.index') }}">{{ __('Bank Reconciliations') }}</a></li>
                <li class="breadcrumb-item active">{{ $reconciliation ? __('Edit') : __('New') }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('accounting.bank-reconciliations.index') }}" class="btn btn-label-secondary d-flex align-items-center gap-2 shadow-xs">
        <i class="icon-base bx bx-arrow-back font-18"></i>
        <span>{{ __('Back to List') }}</span>
    </a>
</div>

<div id="alertContainer"></div>

<!-- 1. Master Configuration Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
        <i class="icon-base bx bx-slider-alt text-primary font-20"></i>
        <h6 class="fw-bold mb-0 text-heading">{{ __('Reconciliation Parameters & Data') }}</h6>
    </div>
    <div class="card-body pt-4">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1" for="entity_id">
                    {{ __('Accounting Entity') }} <span class="text-danger">*</span>
                </label>
                <select id="entity_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Accounting Entity') }}" data-allow-clear="false" @disabled($reconciliation !== null)>
                    @foreach($entities as $entity)
                        <option value="{{ $entity->id }}" @selected($entity->id == $selectedEntityId)>
                            {{ $entity->translate(app()->getLocale())?->name ?? $entity->translate('ar')?->name ?? $entity->code }} ({{ $entity->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1" for="cash_account_id">
                    {{ __('Bank Account') }} <span class="text-danger">*</span>
                </label>
                <select id="cash_account_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Bank Account') }}" data-allow-clear="false" @disabled($reconciliation !== null)>
                    <option value="">{{ __('Select Bank Account') }}</option>
                    @foreach($bankAccounts as $acc)
                        <option value="{{ $acc->id }}" @selected(($selectedBankAccountId ?? null) == $acc->id)>
                            {{ $acc->translate(app()->getLocale())?->name ?? $acc->code }} ({{ $acc->bank_name ?? $acc->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1" for="statement_date">
                    {{ __('Statement Date') }} <span class="text-danger">*</span>
                </label>
                <input type="date" id="statement_date" class="form-control" value="{{ $reconciliation ? $reconciliation->statement_date->format('Y-m-d') : date('Y-m-d') }}">
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1" for="statement_balance">
                    {{ __('Statement Ending Balance') }} <span class="text-danger">*</span>
                </label>
                <input type="number" step="0.0001" id="statement_balance" class="form-control fw-bold text-primary financial-num" value="{{ $reconciliation ? $reconciliation->statement_balance : '0.00' }}">
            </div>
        </div>
    </div>
</div>

<!-- 2. Transactions Matching Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header border-bottom py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h6 class="card-title fw-bold mb-0">{{ __('Book Transactions for Reconciliation') }}</h6>
            <span class="text-muted small">{{ __('Check transactions that appear on your bank statement to clear them.') }}</span>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Filter Pills -->
            <div class="btn-group btn-group-sm" role="group">
                <input type="radio" class="btn-check" name="btnradioFilter" id="filterAll" autocomplete="off" checked>
                <label class="btn btn-outline-primary" for="filterAll">{{ __('All') }}</label>

                <input type="radio" class="btn-check" name="btnradioFilter" id="filterDeposits" autocomplete="off">
                <label class="btn btn-outline-primary" for="filterDeposits">{{ __('Deposits') }}</label>

                <input type="radio" class="btn-check" name="btnradioFilter" id="filterPayments" autocomplete="off">
                <label class="btn btn-outline-primary" for="filterPayments">{{ __('Payments') }}</label>
            </div>

            <!-- Quick Search -->
            <div class="input-group input-group-sm" style="width: 200px;">
                <span class="input-group-text"><i class="icon-base bx bx-search"></i></span>
                <input type="text" id="txSearchInput" class="form-control" placeholder="{{ __('Search...') }}">
            </div>

            <button type="button" id="btnSelectAll" class="btn btn-sm btn-outline-primary">{{ __('Select All') }}</button>
            <button type="button" id="btnDeselectAll" class="btn btn-sm btn-outline-secondary">{{ __('Deselect All') }}</button>
        </div>
    </div>

    <div class="table-responsive text-nowrap" style="max-height: 520px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0" id="transactionsTable">
            <thead class="table-light sticky-top bg-light">
                <tr>
                    <th width="4%" class="text-center">
                        <input type="checkbox" class="form-check-input" id="checkAllHeader">
                    </th>
                    <th style="width: 110px;">{{ __('Date') }}</th>
                    <th style="width: 130px;">{{ __('Entry #') }}</th>
                    <th style="width: 140px;">{{ __('Journal') }}</th>
                    <th>{{ __('Reference / Narration') }}</th>
                    <th class="text-end text-success" style="width: 140px;">{{ __('Deposits (Debit)') }}</th>
                    <th class="text-end text-danger" style="width: 140px;">{{ __('Payments (Credit)') }}</th>
                    <th width="10%" class="text-center">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody id="transactionsTbody">
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="icon-base bx bx-info-circle text-primary font-24 mb-2 d-block"></i>
                        <span>{{ __('Please select a bank account to view transactions.') }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="noTransactionsMsg" class="text-center py-5" style="display: none;">
        <div class="avatar avatar-xl bg-label-success mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center">
            <i class="icon-base bx bx-check-double font-32 text-success"></i>
        </div>
        <h6 class="fw-semibold text-heading">{{ __('No unreconciled transactions found.') }}</h6>
        <span class="text-muted small">{{ __('All recorded transactions for this bank account are already reconciled.') }}</span>
    </div>
</div>

<!-- 3. Notes Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <label class="form-label fw-semibold text-heading small mb-1">{{ __('Reconciliation Notes & Remarks') }}</label>
        <textarea id="rec_notes" class="form-control" rows="2" placeholder="{{ __('Optional notes regarding this reconciliation statement...') }}">{{ $reconciliation?->notes }}</textarea>
    </div>
</div>

<!-- 4. Sticky Balance & Action Bar -->
<div class="sticky-rec-bar p-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div class="d-flex flex-wrap align-items-center gap-4">
        <div>
            <span class="text-muted small d-block">{{ __('Cleared Items') }}:</span>
            <span class="fw-bold text-heading financial-num" id="barClearedCount">0 {{ __('items') }}</span>
        </div>
        <div class="border-start ps-3">
            <span class="text-muted small d-block">{{ __('Cleared Total') }}:</span>
            <span class="fw-bold text-success fs-6 financial-num" id="barClearedTotal">0.00</span>
        </div>
        <div class="border-start ps-3">
            <span class="text-muted small d-block">{{ __('Statement Total') }}:</span>
            <span class="fw-bold text-primary fs-6 financial-num" id="barStatementTotal">0.00</span>
        </div>
        <div class="border-start ps-3">
            <span class="text-muted small d-block">{{ __('Difference') }}:</span>
            <span class="badge bg-label-danger fs-6 financial-num" id="barDiffBadge">0.00</span>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">
        <button type="button" id="btnSaveDraft" class="btn btn-label-secondary d-flex align-items-center gap-2 shadow-xs">
            <i class="icon-base bx bx-save font-18"></i>
            <span>{{ __('Save Draft') }}</span>
        </button>
        <button type="button" id="btnCompleteRec" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" disabled>
            <i class="icon-base bx bx-check-double font-18"></i>
            <span>{{ __('Complete & Reconcile') }}</span>
        </button>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;
    const reconciliationId = {{ $reconciliation ? $reconciliation->id : 'null' }};
    let unreconciledLines = [];
    let clearedSet = new Set();
    let bookBalance = 0;
    let statementBalance = 0;
    let currentFilterType = 'all';

    // Dynamic bank loading if entity changes
    $('#entity_id').on('change', function () {
        const entityId = $(this).val();
        if (!entityId) return;

        $.ajax({
            url: "{{ route('accounting.bank-reconciliations.banks-by-entity') }}",
            type: 'GET',
            data: { entity_id: entityId },
            success: function (res) {
                if (res.success) {
                    const select = $('#cash_account_id');
                    select.empty().append('<option value="">{{ __("Select Bank Account") }}</option>');
                    res.banks.forEach(function (bank) {
                        select.append('<option value="' + bank.id + '">' + bank.name + ' (' + (bank.bank_name || bank.code) + ')</option>');
                    });
                    if (res.banks.length > 0) {
                        select.val(res.banks[0].id);
                    }
                    select.trigger('change');
                    if (select.data('select2')) {
                        select.trigger('change.select2');
                    }
                }
            }
        });
    });

    function recalculateBalances() {
        statementBalance = parseFloat($('#statement_balance').val()) || 0;
        let totalClearedDebit = 0;
        let totalClearedCredit = 0;
        let count = 0;

        unreconciledLines.forEach(function (line) {
            if (clearedSet.has(line.id)) {
                totalClearedDebit += line.debit;
                totalClearedCredit += line.credit;
                count++;
            }
        });

        const clearedBalance = totalClearedDebit - totalClearedCredit;
        const diff = statementBalance - clearedBalance;

        $('#barClearedCount').text(count + ' {{ __("items") }}');
        $('#barClearedTotal').text(clearedBalance.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#barStatementTotal').text(statementBalance.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        const badge = $('#barDiffBadge');
        if (Math.abs(diff) < 0.0001) {
            badge.removeClass('bg-label-danger').addClass('bg-label-success').html('<i class="icon-base bx bx-check-circle me-1"></i> 0.00');
            $('#btnCompleteRec').prop('disabled', false);
        } else {
            badge.removeClass('bg-label-success').addClass('bg-label-danger').html('<i class="icon-base bx bx-error me-1"></i> ' + diff.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#btnCompleteRec').prop('disabled', true);
        }
    }

    function renderTransactions() {
        const tbody = $('#transactionsTbody');
        tbody.empty();

        if (!unreconciledLines.length) {
            $('#transactionsTable').hide();
            $('#noTransactionsMsg').show();
            recalculateBalances();
            return;
        }

        $('#transactionsTable').show();
        $('#noTransactionsMsg').hide();

        const searchQ = $('#txSearchInput').val().toLowerCase().trim();

        unreconciledLines.forEach(function (line) {
            const isCleared = clearedSet.has(line.id);
            const isDeposit = line.debit > 0;
            const isPayment = line.credit > 0;

            // Filter logic
            if (currentFilterType === 'deposits' && !isDeposit) return;
            if (currentFilterType === 'payments' && !isPayment) return;

            const searchableText = `${line.entry_date} ${line.entry_number || ''} ${line.journal_name || ''} ${line.description || ''} ${line.reference || ''}`.toLowerCase();
            if (searchQ && !searchableText.includes(searchQ)) return;

            const tr = $(`
                <tr class="transaction-row ${isCleared ? 'is-cleared' : ''}" data-id="${line.id}">
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input line-checkbox" value="${line.id}" ${isCleared ? 'checked' : ''}>
                    </td>
                    <td><span class="fw-semibold text-heading font-monospace">${line.entry_date}</span></td>
                    <td>
                        <a href="{{ url('accounting/journal-entries') }}/${line.entry_id}" target="_blank" class="text-primary fw-bold font-monospace">
                            ${line.entry_number || '-'}
                        </a>
                    </td>
                    <td><span class="badge bg-label-secondary font-11">${line.journal_name || '-'}</span></td>
                    <td><span class="text-truncate d-inline-block" style="max-width: 250px;">${line.description || line.reference || '-'}</span></td>
                    <td class="text-end fw-bold text-success font-monospace">${line.debit > 0 ? line.debit.toLocaleString(undefined, { minimumFractionDigits: 2 }) : '-'}</td>
                    <td class="text-end fw-bold text-danger font-monospace">${line.credit > 0 ? line.credit.toLocaleString(undefined, { minimumFractionDigits: 2 }) : '-'}</td>
                    <td class="text-center status-col">
                        <span class="badge ${isCleared ? 'bg-label-success' : 'bg-label-warning'} font-11">${isCleared ? '{{ __("Cleared") }}' : '{{ __("Outstanding") }}'}</span>
                    </td>
                </tr>
            `);

            tbody.append(tr);
        });

        recalculateBalances();
    }

    function loadWorkspaceData() {
        const cashAccountId = $('#cash_account_id').val();
        const statementDate = $('#statement_date').val();
        const statementBal = $('#statement_balance').val();

        if (!cashAccountId) {
            $('#transactionsTable').show();
            $('#noTransactionsMsg').hide();
            $('#transactionsTbody').html('<tr><td colspan="8" class="text-center py-5 text-muted"><i class="icon-base bx bx-info-circle text-primary font-24 mb-2 d-block"></i><span>{{ __("Please select a bank account to view transactions.") }}</span></td></tr>');
            return;
        }

        $('#transactionsTable').show();
        $('#noTransactionsMsg').hide();
        $('#transactionsTbody').html('<tr><td colspan="8" class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></td></tr>');

        $.ajax({
            url: "{{ route('accounting.bank-reconciliations.workspace-data') }}",
            type: 'GET',
            data: {
                cash_account_id: cashAccountId,
                statement_date: statementDate,
                statement_balance: statementBal,
                reconciliation_id: reconciliationId
            },
            success: function (res) {
                if (res.success) {
                    bookBalance = res.book_balance;
                    unreconciledLines = res.unreconciled_lines || [];
                    clearedSet = new Set(res.cleared_line_ids || []);
                    renderTransactions();
                }
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message || 'Error loading transactions');
            }
        });
    }

    $('#cash_account_id, #statement_date').on('change', loadWorkspaceData);

    // Row / Checkbox toggle
    $(document).on('click', '.transaction-row', function (e) {
        if ($(e.target).is('input, a')) return;
        const checkbox = $(this).find('.line-checkbox');
        checkbox.prop('checked', !checkbox.is(':checked')).trigger('change');
    });

    $(document).on('change', '.line-checkbox', function () {
        const lineId = parseInt($(this).val());
        const tr = $(this).closest('tr');
        const statusBadge = tr.find('.status-col span');

        if ($(this).is(':checked')) {
            clearedSet.add(lineId);
            tr.addClass('is-cleared');
            statusBadge.removeClass('bg-label-warning').addClass('bg-label-success').text('{{ __("Cleared") }}');
        } else {
            clearedSet.delete(lineId);
            tr.removeClass('is-cleared');
            statusBadge.removeClass('bg-label-success').addClass('bg-label-warning').text('{{ __("Outstanding") }}');
        }

        recalculateBalances();
    });

    // Check all header
    $('#checkAllHeader').on('change', function () {
        const checked = $(this).is(':checked');
        $('.line-checkbox').prop('checked', checked).trigger('change');
    });

    $('#btnSelectAll').on('click', function () {
        $('#checkAllHeader').prop('checked', true).trigger('change');
    });

    $('#btnDeselectAll').on('click', function () {
        $('#checkAllHeader').prop('checked', false).trigger('change');
    });

    $('#statement_balance').on('input', recalculateBalances);

    // Filter pills
    $('#filterAll').on('change', function () { currentFilterType = 'all'; renderTransactions(); });
    $('#filterDeposits').on('change', function () { currentFilterType = 'deposits'; renderTransactions(); });
    $('#filterPayments').on('change', function () { currentFilterType = 'payments'; renderTransactions(); });

    $('#txSearchInput').on('input', renderTransactions);

    // Save Draft / Complete Handler
    function saveReconciliation(actionType) {
        const cashAccountId = $('#cash_account_id').val();
        const statementDate = $('#statement_date').val();
        const statementBal = $('#statement_balance').val();
        const notes = $('#rec_notes').val();

        if (!cashAccountId) {
            alert('{{ __("Please select a bank account.") }}');
            return;
        }

        const items = [];
        unreconciledLines.forEach(function (line) {
            if (clearedSet.has(line.id)) {
                items.push({
                    journal_entry_line_id: line.id,
                    type: 'matched_entry',
                    statement_reference: line.reference,
                    transaction_date: line.entry_date,
                    debit: line.debit,
                    credit: line.credit,
                    is_cleared: true,
                    notes: line.description
                });
            }
        });

        const postData = {
            _token: '{{ csrf_token() }}',
            id: reconciliationId,
            cash_account_id: cashAccountId,
            statement_date: statementDate,
            statement_balance: statementBal,
            notes: notes,
            action: actionType,
            items: items
        };

        const btn = actionType === 'complete' ? $('#btnCompleteRec') : $('#btnSaveDraft');
        btn.prop('disabled', true);

        $.ajax({
            url: "{{ route('accounting.bank-reconciliations.store') }}",
            type: 'POST',
            data: postData,
            success: function (res) {
                if (res.success) {
                    window.location.href = res.redirect_url;
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || xhr.responseJSON?.errors?.difference?.[0] || 'Error saving reconciliation';
                $('#alertContainer').html('<div class="alert alert-danger alert-dismissible shadow-sm fade show" role="alert">' +
                    '<i class="icon-base bx bx-error me-1"></i> ' + msg +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                btn.prop('disabled', false);
            }
        });
    }

    $('#btnSaveDraft').on('click', () => saveReconciliation('save_draft'));
    $('#btnCompleteRec').on('click', () => saveReconciliation('complete'));

    // Auto-trigger initial load
    if ($('#cash_account_id').val()) {
        loadWorkspaceData();
    }
});
</script>
@endsection
