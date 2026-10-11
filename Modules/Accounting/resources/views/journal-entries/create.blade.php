@extends('layouts/contentNavbarLayout')

@section('title', __('New Journal Entry') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
    .lines-table-scroll-wrapper {
        overflow-x: auto !important;
        overflow-y: visible;
        width: 100%;
        display: block;
        scrollbar-width: thin;
        border: 1px solid #e7e7e8;
        border-radius: 0.5rem;
    }
    .lines-table-scroll-wrapper::-webkit-scrollbar {
        height: 6px;
    }
    .lines-table-scroll-wrapper::-webkit-scrollbar-track {
        background: #f5f5f9;
        border-radius: 3px;
    }
    .lines-table-scroll-wrapper::-webkit-scrollbar-thumb {
        background: #c7cad1;
        border-radius: 3px;
    }
    .lines-table-scroll-wrapper::-webkit-scrollbar-thumb:hover {
        background: #a1a5b0;
    }
    #linesTable {
        width: 100%;
        min-width: 950px;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    #linesTable th, #linesTable td {
        vertical-align: middle;
        padding: 0.45rem 0.55rem;
        border-color: #ebecef;
    }
    #linesTable thead th {
        font-weight: 600;
        font-size: 0.8125rem;
        letter-spacing: 0.2px;
        background-color: #f8f9fa;
        color: #566a7f;
        white-space: nowrap;
        border-bottom: 2px solid #e1e4ea;
        padding-top: 0.65rem;
        padding-bottom: 0.65rem;
    }
    #linesTable tbody tr:hover {
        background-color: #fbfcfd;
    }
    #linesTable .line-number {
        font-size: 0.8rem;
        color: #a1acb8;
        font-weight: 600;
    }
    /* Harmonized inputs & select2 heights */
    #linesTable .form-control-sm,
    #linesTable .form-select-sm {
        height: 36px;
        font-size: 0.825rem;
        border-radius: 0.375rem;
        border-color: #d9dee3;
        box-shadow: none;
    }
    #linesTable .select2-container .select2-selection--single {
        height: 36px !important;
        padding: 0.25rem 0.6rem !important;
        font-size: 0.825rem !important;
        border-color: #d9dee3 !important;
        border-radius: 0.375rem !important;
        display: flex !important;
        align-items: center !important;
    }
    #linesTable .select2-container .select2-selection__rendered {
        line-height: normal !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        color: #435971 !important;
    }
    /* Professional Financial Numbers */
    .line-debit, .line-credit {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        letter-spacing: 0.3px;
        font-variant-numeric: tabular-nums;
        text-align: right !important;
        direction: ltr !important;
        padding-right: 0.6rem !important;
    }
    .line-debit:focus, .line-credit:focus {
        background-color: #fff !important;
        border-color: var(--bs-primary) !important;
        box-shadow: none !important;
    }
    /* Hide spin buttons */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
    /* Footer Totals */
    #linesTable tfoot td {
        background-color: #f8f9fa;
        padding: 0.6rem 0.55rem;
        border-top: 2px solid #e1e4ea;
    }
    .totals-label {
        font-size: 0.8125rem;
        font-weight: 700;
        color: #566a7f;
    }
    .total-amount-display {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        font-size: 0.875rem !important;
        font-weight: 700 !important;
        font-variant-numeric: tabular-nums;
        direction: ltr !important;
        display: inline-block;
    }
    .btn-remove-line {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
    }
</style>
@endsection

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-receipt text-primary font-24"></i>
            <span>{{ __('New Journal Entry') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('accounting.journal-entries.index') }}">{{ __('Journal Entries') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Create') }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('accounting.journal-entries.index') }}" class="btn btn-label-secondary d-flex align-items-center gap-2 shadow-xs">
        <i class="icon-base bx bx-arrow-back font-18"></i>
        <span>{{ __('Back to Entries') }}</span>
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible d-flex align-items-center mb-4" role="alert">
        <i class="icon-base bx bx-error-circle me-2 font-20 flex-shrink-0"></i>
        <div>
            <div class="fw-bold">{{ __('Please fix the following errors:') }}</div>
            <ul class="mb-0 ps-3 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('accounting.journal-entries.store') }}" method="POST" id="journalEntryForm">
    @csrf

    <!-- 1. Header Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="icon-base bx bx-info-circle text-primary font-20"></i>
                <h6 class="fw-bold mb-0">{{ __('Entry Details') }}</h6>
            </div>
            <span class="badge bg-label-warning px-3 py-1 font-12 fw-semibold">{{ __('Draft') }}</span>
        </div>
        <div class="card-body pt-4">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold" for="entity_id">
                        {{ __('Accounting Entity') }} <span class="text-danger">*</span>
                    </label>
                    <select name="entity_id" id="entity_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Entity') }}" required>
                        <option value="">{{ __('Select Entity') }}</option>
                        @foreach ($entities as $entity)
                            <option value="{{ $entity->id }}" @selected(old('entity_id', $selectedEntityId) == $entity->id)>
                                {{ $entity->translate(app()->getLocale())?->name ?? $entity->translate('ar')?->name ?? $entity->code }} ({{ $entity->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold" for="journal_id">
                        {{ __('Journal') }} <span class="text-danger">*</span>
                    </label>
                    <select name="journal_id" id="journal_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Journal') }}" required>
                        <option value="">{{ __('Select Journal') }}</option>
                        @foreach ($journals as $journal)
                            <option value="{{ $journal->id }}" data-entity="{{ $journal->entity_id }}" data-currency="{{ $journal->currency }}" data-default-account="{{ $journal->default_account_id }}" @selected(old('journal_id') == $journal->id)>
                                {{ $journal->code }} - {{ $journal->translate(app()->getLocale())?->name ?? $journal->translate('ar')?->name ?? $journal->code }} ({{ $journal->type->label() }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold" for="entry_date">
                        {{ __('Entry Date') }} <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="entry_date" id="entry_date" class="form-control" value="{{ old('entry_date', now()->toDateString()) }}" required>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold" for="reference">
                        {{ __('Reference / Document #') }}
                    </label>
                    <input type="text" name="reference" id="reference" class="form-control" value="{{ old('reference') }}" placeholder="{{ __('e.g. INV-2026-089, Check #441') }}">
                </div>

                <div class="col-12 col-md-8">
                    <label class="form-label fw-semibold" for="narration">
                        {{ __('General Description / Narration') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="narration" id="narration" class="form-control" value="{{ old('narration') }}" placeholder="{{ __('e.g. Recording subscription revenue or operational expenses...') }}" required>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Lines Items Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="icon-base bx bx-list-ul text-primary font-20"></i>
                <h6 class="fw-bold mb-0">{{ __('Journal Entry Lines (Double Entry)') }}</h6>
            </div>
            <button type="button" class="btn btn-sm btn-label-primary d-flex align-items-center gap-1 shadow-xs" id="btnAddLine">
                <i class="icon-base bx bx-plus font-16"></i>
                <span class="fw-semibold">{{ __('Add Line') }}</span>
            </button>
        </div>
        <div class="lines-table-scroll-wrapper text-nowrap">
            <table class="table table-bordered align-middle mb-0" id="linesTable" style="min-width: 950px;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 44px;" class="text-center">#</th>
                        <th style="width: 32%;">{{ __('Account') }} <span class="text-danger">*</span></th>
                        <th style="width: 20%;">{{ __('Cost Center') }}</th>
                        <th style="width: 135px;" class="text-end">{{ __('Debit') }}</th>
                        <th style="width: 135px;" class="text-end">{{ __('Credit') }}</th>
                        <th>{{ __('Line Description') }}</th>
                        <th style="width: 46px;" class="text-center">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody id="linesContainer">
                    <!-- Dynamic Line Rows (Minimum 2) -->
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="text-end totals-label">{{ __('Totals') }}:</td>
                        <td class="text-end">
                            <span class="total-amount-display text-success" id="totalDebitDisplay">0.00</span>
                        </td>
                        <td class="text-end">
                            <span class="total-amount-display text-danger" id="totalCreditDisplay">0.00</span>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Real-time Balance Status Bar -->
        <div class="card-footer bg-transparent border-top py-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2" id="balanceStatusWrapper">
                <span class="badge bg-label-danger px-2 py-1 font-13 d-flex align-items-center gap-1" id="balanceBadge">
                    <i class="icon-base bx bx-x-circle font-16"></i>
                    <span id="balanceText">{{ __('Unbalanced') }}</span>
                </span>
                <span class="text-muted small fw-semibold" id="differenceText">{{ __('Difference:') }} <span id="differenceAmount" class="total-amount-display fw-bold font-13">0.00</span></span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('accounting.journal-entries.index') }}" class="btn btn-outline-secondary btn-sm">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" name="post_immediately" value="0" class="btn btn-label-primary btn-sm fw-semibold" id="btnSaveDraft">
                    <i class="icon-base bx bx-save me-1"></i>
                    <span>{{ __('Save as Draft') }}</span>
                </button>
                <button type="submit" name="post_immediately" value="1" class="btn btn-primary btn-sm fw-semibold" id="btnSavePost">
                    <i class="icon-base bx bx-check-double me-1"></i>
                    <span>{{ __('Save & Post Immediately') }}</span>
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;
    let lineIndex = 0;

    const accountsData = @json($accounts);
    const costCentersData = @json($costCenters);

    function formatNumber(num) {
        return Number(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatAccountOption(account) {
        const arName = account.translations?.find(t => t.locale === 'ar')?.name || account.name || account.code;
        return `${account.code} - ${arName}`;
    }

    function createLineRow(debitVal = '', creditVal = '') {
        const currentEntityId = $('#entity_id').val();
        const currentNarration = $('#narration').val() || '';

        let accountsOptions = '<option value="">' + "{{ __('Select Account') }}" + '</option>';
        accountsData.forEach(function (acc) {
            if (!currentEntityId || String(acc.entity_id) === String(currentEntityId)) {
                accountsOptions += `<option value="${acc.id}">${formatAccountOption(acc)}</option>`;
            }
        });

        let costCenterOptions = '<option value="">' + "{{ __('No Cost Center') }}" + '</option>';
        costCentersData.forEach(function (cc) {
            if (!currentEntityId || String(cc.entity_id) === String(currentEntityId)) {
                const ccName = cc.translations?.find(t => t.locale === 'ar')?.name || cc.code;
                costCenterOptions += `<option value="${cc.id}">${cc.code} - ${ccName}</option>`;
            }
        });

        const rowHtml = `
            <tr class="line-row" data-index="${lineIndex}">
                <td class="text-center line-number">${lineIndex + 1}</td>
                <td>
                    <select name="lines[${lineIndex}][account_id]" class="form-select form-select-sm select2 line-account" required>
                        ${accountsOptions}
                    </select>
                </td>
                <td>
                    <select name="lines[${lineIndex}][cost_center_id]" class="form-select form-select-sm select2 line-cost-center">
                        ${costCenterOptions}
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="lines[${lineIndex}][debit]" class="form-control form-control-sm text-end line-debit" value="${debitVal}" placeholder="0.00">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="lines[${lineIndex}][credit]" class="form-control form-control-sm text-end line-credit" value="${creditVal}" placeholder="0.00">
                </td>
                <td>
                    <input type="text" name="lines[${lineIndex}][description]" class="form-control form-control-sm line-desc" value="${currentNarration}" placeholder="{{ __('Description') }}">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-line" title="{{ __('Remove Line') }}">
                        <i class="icon-base bx bx-trash font-16"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#linesContainer').append(rowHtml);

        const newRow = $('#linesContainer tr:last');
        if ($.fn.select2) {
            newRow.find('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                dir: $('html').attr('dir') === 'rtl' ? 'rtl' : 'ltr'
            });
        }

        lineIndex++;
        renumberRows();
        calculateTotals();
    }

    function renumberRows() {
        $('#linesContainer tr').each(function (idx) {
            $(this).find('.line-number').text(idx + 1);
        });
    }

    function calculateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;

        $('#linesContainer tr').each(function () {
            const debit = parseFloat($(this).find('.line-debit').val()) || 0;
            const credit = parseFloat($(this).find('.line-credit').val()) || 0;
            totalDebit += debit;
            totalCredit += credit;
        });

        $('#totalDebitDisplay').text(formatNumber(totalDebit));
        $('#totalCreditDisplay').text(formatNumber(totalCredit));

        const diff = Math.abs(totalDebit - totalCredit);
        $('#differenceAmount').text(formatNumber(diff));

        const isBalanced = diff < 0.0001 && totalDebit > 0;

        if (isBalanced) {
            $('#balanceBadge').attr('class', 'badge bg-label-success px-2 py-1 font-13 d-flex align-items-center gap-1');
            $('#balanceBadge i').attr('class', 'icon-base bx bx-check-circle font-16');
            $('#balanceText').text("{{ __('Balanced') }}");
            $('#differenceText').addClass('text-success').removeClass('text-danger');
            $('#btnSavePost').prop('disabled', false);
        } else {
            $('#balanceBadge').attr('class', 'badge bg-label-danger px-2 py-1 font-13 d-flex align-items-center gap-1');
            $('#balanceBadge i').attr('class', 'icon-base bx bx-x-circle font-16');
            $('#balanceText').text("{{ __('Unbalanced') }}");
            $('#differenceText').addClass('text-danger').removeClass('text-success');
            $('#btnSavePost').prop('disabled', true);
        }
    }

    // Initialize 2 default blank lines
    createLineRow();
    createLineRow();

    $('#btnAddLine').on('click', function () {
        let totalDebit = 0, totalCredit = 0;
        $('#linesContainer tr').each(function () {
            totalDebit += parseFloat($(this).find('.line-debit').val()) || 0;
            totalCredit += parseFloat($(this).find('.line-credit').val()) || 0;
        });

        // Smart counterpart auto-fill
        let autoDebit = '', autoCredit = '';
        if (totalDebit > totalCredit) {
            autoCredit = (totalDebit - totalCredit).toFixed(2);
        } else if (totalCredit > totalDebit) {
            autoDebit = (totalCredit - totalDebit).toFixed(2);
        }

        createLineRow(autoDebit, autoCredit);
    });

    $(document).on('click', '.btn-remove-line', function () {
        if ($('#linesContainer tr').length <= 2) {
            alert("{{ __('A journal entry must contain at least two lines.') }}");
            return;
        }
        $(this).closest('tr').remove();
        renumberRows();
        calculateTotals();
    });

    $(document).on('input', '.line-debit', function () {
        const row = $(this).closest('tr');
        if (parseFloat($(this).val()) > 0) {
            row.find('.line-credit').val('');
        }
        calculateTotals();
    });

    $(document).on('input', '.line-credit', function () {
        const row = $(this).closest('tr');
        if (parseFloat($(this).val()) > 0) {
            row.find('.line-debit').val('');
        }
        calculateTotals();
    });

    // Update lines description on main narration change
    $('#narration').on('input', function () {
        const val = $(this).val();
        $('.line-desc').each(function () {
            if (!$(this).val() || $(this).data('auto') !== false) {
                $(this).val(val);
            }
        });
    });

    // Dynamic filtering for Entity change
    $('#entity_id').on('change', function () {
        const entityId = $(this).val();

        // Filter Journals
        $('#journal_id option').each(function () {
            const jEntity = $(this).data('entity');
            if (!entityId || !jEntity || String(jEntity) === String(entityId)) {
                $(this).prop('disabled', false).show();
            } else {
                $(this).prop('disabled', true).hide();
            }
        });
        $('#journal_id').val('').trigger('change');

        // Refresh existing lines with entity's accounts
        $('#linesContainer').empty();
        lineIndex = 0;
        createLineRow();
        createLineRow();
    });

    $('#journal_id').on('change', function () {
        const selected = $(this).find('option:selected');
        const defaultAccountId = selected.attr('data-default-account') || selected.data('default-account');
        const currency = selected.data('currency') || selected.attr('data-currency');
        if (currency) {
            $('#currency').val(currency);
        }
        if (defaultAccountId) {
            const firstRowAccount = $('#linesContainer tr:first .line-account');
            if (firstRowAccount.length) {
                firstRowAccount.val(String(defaultAccountId)).trigger('change');
                if (firstRowAccount.data('select2')) {
                    firstRowAccount.trigger('change.select2');
                }
                const container = firstRowAccount.next('.select2-container');
                container.addClass('border border-primary shadow-xs');
                setTimeout(() => container.removeClass('border border-primary shadow-xs'), 1200);
            }
        }
    });

    if ($('#journal_id').val()) {
        $('#journal_id').trigger('change');
    }
});
</script>
@endsection
