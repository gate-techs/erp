@extends('layouts/contentNavbarLayout')

@section('title', __('New Payment Matching') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
    .sticky-match-bar {
        position: sticky;
        bottom: 1rem;
        z-index: 1020;
        backdrop-filter: blur(12px);
        background: rgba(var(--bs-card-bg-rgb), 0.96);
        border: 1px solid var(--bs-border-color);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        border-radius: 0.75rem;
    }
    .matching-row {
        cursor: pointer;
        transition: background-color 0.15s ease-in-out;
    }
    .matching-row:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.04) !important;
    }
    .matching-row.is-selected {
        background-color: rgba(var(--bs-primary-rgb), 0.08) !important;
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
            <i class="icon-base bx bx-link text-primary font-24"></i>
            <span>{{ __('New Payment Matching') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('accounting.payment-matchings.index') }}">{{ __('Payment Matching & Allocations') }}</a></li>
                <li class="breadcrumb-item active">{{ __('New Payment Matching') }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('accounting.payment-matchings.index') }}" class="btn btn-label-secondary d-flex align-items-center gap-2 shadow-xs">
        <i class="icon-base bx bx-arrow-back font-18"></i>
        <span>{{ __('Back to List') }}</span>
    </a>
</div>

<div id="alertContainer"></div>

<!-- 1. Matching Parameters Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
        <i class="icon-base bx bx-slider-alt text-primary font-20"></i>
        <h6 class="fw-bold mb-0 text-heading">{{ __('Matching Parameters & Data') }}</h6>
    </div>
    <div class="card-body pt-3 pb-3">
        <div class="row g-3">
            <!-- 1. Accounting Entity -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1" for="entity_id">
                    {{ __('Accounting Entity') }} <span class="text-danger">*</span>
                </label>
                <select id="entity_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Accounting Entity') }}" data-allow-clear="false">
                    @foreach($entities as $entity)
                        <option value="{{ $entity->id }}" @selected($entity->id == $selectedEntityId)>
                            {{ $entity->translate(app()->getLocale())?->name ?? $entity->translate('ar')?->name ?? $entity->code }} ({{ $entity->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Account to Match -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1" for="match_account_id">
                    {{ __('Account to Match') }} <span class="text-danger">*</span>
                </label>
                <select id="match_account_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Account to Match') }}" data-allow-clear="false">
                    <option value="">{{ __('Select Account to Match') }}</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">
                            {{ $acc->code }} - {{ $acc->translate(app()->getLocale())?->name ?? $acc->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Search filter -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-heading small mb-1" for="txSearchInput">
                    {{ __('Search Transactions') }}
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="icon-base bx bx-search"></i></span>
                    <input type="text" id="txSearchInput" class="form-control" placeholder="{{ __('Search by entry # or narration...') }}">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2. Stacked Table 1: Unmatched Debits (Invoices / Receivables) -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-heading d-flex align-items-center gap-2">
            <i class="icon-base bx bx-file text-success font-20"></i>
            <span>{{ __('Debit Transactions / Unpaid Invoices') }}</span>
        </h6>
        <span class="badge bg-label-success fs-6 financial-num" id="debitCountBadge">0</span>
    </div>
    <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0" id="debitsTable">
            <thead class="table-light sticky-top bg-light">
                <tr>
                    <th width="5%" class="text-center">#</th>
                    <th style="width: 120px;">{{ __('Date') }}</th>
                    <th style="width: 140px;">{{ __('Entry #') }}</th>
                    <th>{{ __('Reference / Narration') }}</th>
                    <th class="text-end text-success" style="width: 140px;">{{ __('Remaining (Debit)') }}</th>
                </tr>
            </thead>
            <tbody id="debitsTbody">
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <span>{{ __('Please select an account to display pending transactions...') }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- 3. Stacked Table 2: Unmatched Credits (Payments / Deductions) -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-heading d-flex align-items-center gap-2">
            <i class="icon-base bx bx-wallet text-primary font-20"></i>
            <span>{{ __('Credit Transactions / Receipts & Payments') }}</span>
        </h6>
        <span class="badge bg-label-primary fs-6 financial-num" id="creditCountBadge">0</span>
    </div>
    <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0" id="creditsTable">
            <thead class="table-light sticky-top bg-light">
                <tr>
                    <th width="5%" class="text-center">#</th>
                    <th style="width: 120px;">{{ __('Date') }}</th>
                    <th style="width: 140px;">{{ __('Entry #') }}</th>
                    <th>{{ __('Reference / Narration') }}</th>
                    <th class="text-end text-primary" style="width: 140px;">{{ __('Remaining (Credit)') }}</th>
                </tr>
            </thead>
            <tbody id="creditsTbody">
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <span>{{ __('Please select an account to display pending transactions...') }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- 4. Sticky Match & Action Bar -->
<div class="sticky-match-bar p-3 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
    <div class="d-flex flex-wrap align-items-center gap-4">
        <!-- Selected Debit -->
        <div>
            <span class="text-muted small d-block">{{ __('Selected Debit') }}:</span>
            <span class="fw-bold text-success financial-num" id="barSelectedDebitText">{{ __('Not Selected') }}</span>
        </div>
        <!-- Selected Credit -->
        <div class="border-start ps-3">
            <span class="text-muted small d-block">{{ __('Selected Credit') }}:</span>
            <span class="fw-bold text-primary financial-num" id="barSelectedCreditText">{{ __('Not Selected') }}</span>
        </div>
        <!-- Amount Input -->
        <div class="border-start ps-3" style="min-width: 180px;">
            <label class="text-muted small d-block mb-0">{{ __('Amount to Match') }}:</label>
            <input type="number" step="0.0001" min="0.0001" id="matchAmountInput" class="form-control form-control-sm fw-bold text-success financial-num mt-1" placeholder="0.00">
        </div>
        <!-- Notes Input -->
        <div class="border-start ps-3 d-none d-xl-block" style="min-width: 220px;">
            <label class="text-muted small d-block mb-0">{{ __('Matching Notes') }}:</label>
            <input type="text" id="matchNotesInput" class="form-control form-control-sm mt-1" placeholder="{{ __('Optional notes...') }}">
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">
        <button type="button" id="btnResetSelection" class="btn btn-label-secondary d-flex align-items-center gap-1 shadow-xs">
            <i class="icon-base bx bx-refresh font-18"></i>
            <span>{{ __('Reset Selection') }}</span>
        </button>
        <button type="button" id="btnExecuteMatch" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" disabled>
            <i class="icon-base bx bx-link font-18"></i>
            <span>{{ __('Confirm & Match Both Entries') }}</span>
        </button>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;

    let rawDebitLines = [];
    let rawCreditLines = [];
    let selectedDebit = null;
    let selectedCredit = null;

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

    // Dynamic accounts loading on entity change
    $('#entity_id').on('change', function () {
        const entityId = $(this).val();
        if (!entityId) return;

        $.ajax({
            url: "{{ route('accounting.payment-matchings.accounts-by-entity') }}",
            type: 'GET',
            data: { entity_id: entityId },
            success: function (res) {
                if (res.success) {
                    const select = $('#match_account_id');
                    select.empty().append('<option value="">{{ __("Select Account to Match") }}</option>');
                    res.accounts.forEach(function (acc) {
                        select.append('<option value="' + acc.id + '">' + acc.code + ' - ' + acc.name + '</option>');
                    });
                    select.val('').trigger('change');
                    if (select.data('select2')) {
                        select.trigger('change.select2');
                    }
                }
            }
        });
    });

    function loadUnmatchedData() {
        const entityId = $('#entity_id').val();
        const accountId = $('#match_account_id').val();

        if (!accountId) {
            $('#debitsTbody').html('<tr><td colspan="5" class="text-center py-4 text-muted"><span>{{ __("Please select an account to display pending transactions...") }}</span></td></tr>');
            $('#creditsTbody').html('<tr><td colspan="5" class="text-center py-4 text-muted"><span>{{ __("Please select an account to display pending transactions...") }}</span></td></tr>');
            $('#debitCountBadge').text('0');
            $('#creditCountBadge').text('0');
            resetSelection();
            return;
        }

        $('#debitsTbody').html('<tr><td colspan="5" class="text-center py-4"><div class="spinner-border spinner-border-sm text-success" role="status"></div></td></tr>');
        $('#creditsTbody').html('<tr><td colspan="5" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></td></tr>');

        $.ajax({
            url: "{{ route('accounting.payment-matchings.unmatched-data') }}",
            type: 'GET',
            data: { entity_id: entityId, account_id: accountId },
            success: function (res) {
                if (res.success) {
                    rawDebitLines = res.debitLines || [];
                    rawCreditLines = res.creditLines || [];
                    resetSelection();
                    renderTables();
                }
            },
            error: function (xhr) {
                alertBox('danger', xhr.responseJSON?.message || 'Error loading transactions');
            }
        });
    }

    $('#match_account_id').on('change', loadUnmatchedData);

    function renderTables() {
        const q = $('#txSearchInput').val().toLowerCase().trim();

        // 1. Render Debits
        const debitsTbody = $('#debitsTbody');
        debitsTbody.empty();

        const filteredDebits = rawDebitLines.filter(line => {
            if (!q) return true;
            const str = `${line.entry_date} ${line.entry_number || ''} ${line.description || ''}`.toLowerCase();
            return str.includes(q);
        });

        $('#debitCountBadge').text(filteredDebits.length);

        if (!filteredDebits.length) {
            debitsTbody.html('<tr><td colspan="5" class="text-center py-4 text-muted">{{ __("No pending debit transactions for this account.") }}</td></tr>');
        } else {
            filteredDebits.forEach(line => {
                const isSel = selectedDebit && selectedDebit.id === line.id;
                const tr = $(`
                    <tr class="matching-row debit-row ${isSel ? 'is-selected' : ''}" data-id="${line.id}">
                        <td class="text-center">
                            <input type="radio" name="selectedDebitRadio" class="form-check-input" value="${line.id}" ${isSel ? 'checked' : ''}>
                        </td>
                        <td><span class="fw-semibold text-heading font-monospace font-12">${line.entry_date}</span></td>
                        <td>
                            <a href="{{ url('accounting/journal-entries') }}/${line.entry_id}" target="_blank" class="text-success fw-bold font-monospace font-12">
                                ${line.entry_number || '-'}
                            </a>
                        </td>
                        <td><span class="text-truncate d-inline-block font-12" style="max-width: 320px;">${line.description || '-'}</span></td>
                        <td class="text-end fw-bold text-success font-monospace">${line.remaining_amount.toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                    </tr>
                `);
                debitsTbody.append(tr);
            });
        }

        // 2. Render Credits
        const creditsTbody = $('#creditsTbody');
        creditsTbody.empty();

        const filteredCredits = rawCreditLines.filter(line => {
            if (!q) return true;
            const str = `${line.entry_date} ${line.entry_number || ''} ${line.description || ''}`.toLowerCase();
            return str.includes(q);
        });

        $('#creditCountBadge').text(filteredCredits.length);

        if (!filteredCredits.length) {
            creditsTbody.html('<tr><td colspan="5" class="text-center py-4 text-muted">{{ __("No pending credit transactions for this account.") }}</td></tr>');
        } else {
            filteredCredits.forEach(line => {
                const isSel = selectedCredit && selectedCredit.id === line.id;
                const tr = $(`
                    <tr class="matching-row credit-row ${isSel ? 'is-selected' : ''}" data-id="${line.id}">
                        <td class="text-center">
                            <input type="radio" name="selectedCreditRadio" class="form-check-input" value="${line.id}" ${isSel ? 'checked' : ''}>
                        </td>
                        <td><span class="fw-semibold text-heading font-monospace font-12">${line.entry_date}</span></td>
                        <td>
                            <a href="{{ url('accounting/journal-entries') }}/${line.entry_id}" target="_blank" class="text-primary fw-bold font-monospace font-12">
                                ${line.entry_number || '-'}
                            </a>
                        </td>
                        <td><span class="text-truncate d-inline-block font-12" style="max-width: 320px;">${line.description || '-'}</span></td>
                        <td class="text-end fw-bold text-primary font-monospace">${line.remaining_amount.toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                    </tr>
                `);
                creditsTbody.append(tr);
            });
        }

        updateBottomBar();
    }

    $('#txSearchInput').on('input', renderTables);

    // Row selection for Debits
    $(document).on('click', '.debit-row', function (e) {
        if ($(e.target).is('a')) return;
        const lineId = parseInt($(this).data('id'));
        selectedDebit = rawDebitLines.find(l => l.id === lineId) || null;

        $('.debit-row').removeClass('is-selected').find('input[type="radio"]').prop('checked', false);
        $(this).addClass('is-selected').find('input[type="radio"]').prop('checked', true);

        autoCalculateAmount();
    });

    // Row selection for Credits
    $(document).on('click', '.credit-row', function (e) {
        if ($(e.target).is('a')) return;
        const lineId = parseInt($(this).data('id'));
        selectedCredit = rawCreditLines.find(l => l.id === lineId) || null;

        $('.credit-row').removeClass('is-selected').find('input[type="radio"]').prop('checked', false);
        $(this).addClass('is-selected').find('input[type="radio"]').prop('checked', true);

        autoCalculateAmount();
    });

    function autoCalculateAmount() {
        if (selectedDebit && selectedCredit) {
            const recommended = Math.min(selectedDebit.remaining_amount, selectedCredit.remaining_amount);
            $('#matchAmountInput').val(recommended.toFixed(2));
        }
        updateBottomBar();
    }

    function updateBottomBar() {
        if (selectedDebit) {
            $('#barSelectedDebitText').html(`<i class="bx bx-check-circle me-1"></i> ${selectedDebit.entry_number} (${selectedDebit.remaining_amount.toLocaleString(undefined, { minimumFractionDigits: 2 })})`);
        } else {
            $('#barSelectedDebitText').text('{{ __("Not Selected") }}');
        }

        if (selectedCredit) {
            $('#barSelectedCreditText').html(`<i class="bx bx-check-circle me-1"></i> ${selectedCredit.entry_number} (${selectedCredit.remaining_amount.toLocaleString(undefined, { minimumFractionDigits: 2 })})`);
        } else {
            $('#barSelectedCreditText').text('{{ __("Not Selected") }}');
        }

        const canMatch = selectedDebit && selectedCredit && parseFloat($('#matchAmountInput').val()) > 0;
        $('#btnExecuteMatch').prop('disabled', !canMatch);
    }

    $('#matchAmountInput').on('input', updateBottomBar);

    function resetSelection() {
        selectedDebit = null;
        selectedCredit = null;
        $('#matchAmountInput').val('');
        $('#matchNotesInput').val('');
        $('.debit-row, .credit-row').removeClass('is-selected').find('input[type="radio"]').prop('checked', false);
        updateBottomBar();
    }

    $('#btnResetSelection').on('click', resetSelection);

    // Execute Match Action
    $('#btnExecuteMatch').on('click', function () {
        if (!selectedDebit || !selectedCredit) return;

        const entityId = $('#entity_id').val();
        const amount = parseFloat($('#matchAmountInput').val()) || 0;
        const notes = $('#matchNotesInput').val();

        if (amount <= 0) {
            alertBox('danger', '{{ __("Please enter a match amount greater than zero.") }}');
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: "{{ route('accounting.payment-matchings.store') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                entity_id: entityId,
                debit_line_id: selectedDebit.id,
                credit_line_id: selectedCredit.id,
                amount: amount,
                notes: notes
            },
            success: function (res) {
                if (res.success) {
                    alertBox('success', res.message || '{{ __("Matching completed successfully.") }}');
                    loadUnmatchedData();
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || '{{ __("An error occurred during matching.") }}';
                alertBox('danger', msg);
                btn.prop('disabled', false);
            }
        });
    });
});
</script>
@endsection
