@extends('layouts/contentNavbarLayout')

@section('title', __('Opening Balances') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
    .sticky-balance-bar {
        position: sticky;
        bottom: 1rem;
        z-index: 1020;
        backdrop-filter: blur(12px);
        background: rgba(var(--bs-card-bg-rgb), 0.92);
        border: 1px solid var(--bs-border-color);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        border-radius: 0.75rem;
    }
    .account-row:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.03) !important;
    }
    .account-type-header {
        background-color: var(--bs-table-bg, #f8f9fa);
        font-weight: 700;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
</style>
@endsection

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-book-bookmark text-primary font-24"></i>
            <span>{{ __('Opening Balances') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Opening Balances') }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
        @can('accounting.opening-balances.print')
        <a id="btnPrintStatement" href="javascript:void(0);" class="btn btn-outline-primary d-flex align-items-center gap-2 shadow-xs">
            <i class="icon-base bx bx-printer font-18"></i>
            <span>{{ __('Print Statement') }}</span>
        </a>
        @endcan
    </div>
</div>

<div id="alertContainer"></div>

<!-- Status Banner if Posted -->
{{-- Visibility is toggled by JS (slideDown/slideUp), so the outer element must not carry d-flex (!important) --}}
<div id="postedBanner" class="alert alert-success mb-4 shadow-sm" style="display: none;">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2" style="flex: 1 1 220px; min-width: 0;">
            <i class="icon-base bx bx-check-double font-24 flex-shrink-0"></i>
            <div>
                <span class="fw-bold fs-6">{{ __('Opening Entry Posted & Approved') }}</span>
                <span class="d-block small text-muted" id="postedBannerDetails"></span>
            </div>
        </div>
        @can('accounting.opening-balances.reopen')
        <button type="button" id="btnReopenEntry" class="btn btn-sm btn-outline-success d-flex align-items-center justify-content-center gap-1 shadow-xs text-nowrap flex-shrink-0 ms-auto">
            <i class="icon-base bx bx-lock-open font-16"></i>
            <span>{{ __('Reopen to Draft') }}</span>
        </button>
        @endcan
    </div>
</div>

<!-- Header Controls Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Accounting Entity') }} <span class="text-danger">*</span></label>
                <select id="entity_id" class="form-select select2" data-control="select2">
                    @foreach($entities as $entity)
                        <option value="{{ $entity->id }}" @selected($entity->id == $selectedEntityId)>
                            {{ $entity->translate(app()->getLocale())?->name ?? $entity->translate('ar')?->name ?? $entity->code }} ({{ $entity->currency }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Fiscal Year') }} <span class="text-danger">*</span></label>
                <select id="fiscal_year_id" class="form-select select2" data-control="select2">
                    @foreach($fiscalYears as $fy)
                        <option value="{{ $fy->id }}" data-start-date="{{ $fy->start_date->format('Y-m-d') }}" @selected($fy->id == $selectedFiscalYearId)>
                            {{ $fy->translate(app()->getLocale())?->name ?? $fy->name }} ({{ $fy->start_date->format('Y-m-d') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Opening Entry Date') }} <span class="text-danger">*</span></label>
                <input type="date" id="entry_date" class="form-control" value="{{ date('Y-01-01') }}">
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Quick Search Account') }}</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="icon-base bx bx-search"></i></span>
                    <input type="text" id="accountSearchInput" class="form-control" placeholder="{{ __('Search code or name...') }}">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Accounts Grid Table Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 560px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0" id="openingBalancesTable">
                <thead class="table-light sticky-top bg-light shadow-2xs">
                    <tr>
                        <th style="width: 140px;">{{ __('Account Code') }}</th>
                        <th style="min-width: 260px;">{{ __('Account Name') }}</th>
                        <th class="text-center" style="width: 130px;">{{ __('Type') }}</th>
                        <th class="text-center" style="width: 100px;">{{ __('Nature') }}</th>
                        <th class="text-end" style="width: 180px;">{{ __('Debit Amount') }}</th>
                        <th class="text-end" style="width: 180px;">{{ __('Credit Amount') }}</th>
                        <th style="min-width: 200px;">{{ __('Notes / Description') }}</th>
                    </tr>
                </thead>
                <tbody id="accountsTableBody">
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">{{ __('Loading...') }}</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Sticky Bottom Live Balancing Bar -->
<div class="sticky-balance-bar p-3 mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-center justify-content-between gap-3">
        <!-- Totals Info -->
        <div class="d-flex flex-wrap align-items-center gap-4">
            <div>
                <span class="text-muted small d-block">{{ __('Total Debit') }}:</span>
                <span class="fs-5 fw-bold text-primary font-monospace" id="displayTotalDebit">0.0000</span>
            </div>
            <div class="border-start ps-3">
                <span class="text-muted small d-block">{{ __('Total Credit') }}:</span>
                <span class="fs-5 fw-bold text-info font-monospace" id="displayTotalCredit">0.0000</span>
            </div>
            <div class="border-start ps-3">
                <span class="text-muted small d-block">{{ __('Difference') }}:</span>
                <span class="fs-5 fw-bold font-monospace" id="displayDifference">0.0000</span>
            </div>
            <div class="border-start ps-3">
                <span class="text-muted small d-block">{{ __('Balance Status') }}:</span>
                <span class="badge" id="displayBalanceBadge">--</span>
            </div>
        </div>

        <!-- Buttons -->
        @can('accounting.opening-balances.create')
        <div class="d-flex align-items-center gap-2">
            <button type="button" id="btnSaveDraft" class="btn btn-label-secondary d-flex align-items-center gap-2 shadow-xs">
                <i class="icon-base bx bx-save font-18"></i>
                <span>{{ __('Save Draft') }}</span>
            </button>
            <button type="button" id="btnPostOpening" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
                <i class="icon-base bx bx-check-double font-18"></i>
                <span>{{ __('Post & Approve Entry') }}</span>
            </button>
        </div>
        @endcan
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;
    let isPosted = false;

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

    function calculateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;

        $('.account-row').each(function () {
            const debit = parseFloat($(this).find('.debit-input').val()) || 0;
            const credit = parseFloat($(this).find('.credit-input').val()) || 0;
            totalDebit += debit;
            totalCredit += credit;
        });

        const diff = totalDebit - totalCredit;
        const absDiff = Math.abs(diff);
        const isBalanced = (absDiff < 0.0001) && (totalDebit > 0);

        $('#displayTotalDebit').text(totalDebit.toLocaleString(undefined, { minimumFractionDigits: 4, maximumFractionDigits: 4 }));
        $('#displayTotalCredit').text(totalCredit.toLocaleString(undefined, { minimumFractionDigits: 4, maximumFractionDigits: 4 }));
        
        $('#displayDifference').text(absDiff.toLocaleString(undefined, { minimumFractionDigits: 4, maximumFractionDigits: 4 }));

        if (isBalanced) {
            $('#displayDifference').removeClass('text-danger').addClass('text-success');
            $('#displayBalanceBadge').removeClass('bg-label-danger bg-label-secondary').addClass('bg-label-success')
                .html('<i class="icon-base bx bx-check me-1"></i>' + "{{ __('Balanced') }}");
            $('#btnPostOpening').prop('disabled', isPosted);
        } else {
            $('#displayDifference').removeClass('text-success').addClass('text-danger');
            $('#displayBalanceBadge').removeClass('bg-label-success bg-label-secondary').addClass('bg-label-danger')
                .html('<i class="icon-base bx bx-x me-1"></i>' + "{{ __('Unbalanced') }}");
            $('#btnPostOpening').prop('disabled', true);
        }
    }

    function loadAccountsData() {
        const entityId = $('#entity_id').val();
        const fiscalYearId = $('#fiscal_year_id').val();

        if (!entityId || !fiscalYearId) return;

        const startDate = $('#fiscal_year_id option:selected').data('start-date');
        if (startDate && !$('#entry_date').val()) {
            $('#entry_date').val(startDate);
        }

        $('#accountsTableBody').html(`
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">{{ __('Loading...') }}</span>
                    </div>
                </td>
            </tr>
        `);

        $.ajax({
            url: "{{ route('accounting.opening-balances.accounts') }}",
            type: 'GET',
            data: { entity_id: entityId, fiscal_year_id: fiscalYearId },
            success: function (res) {
                if (!res.success || !res.accounts) return;

                let html = '';
                let currentType = null;
                isPosted = res.totals.is_posted;

                if (res.opening_entry) {
                    $('#entry_date').val(res.opening_entry.entry_date);
                    if (res.opening_entry.is_posted) {
                        $('#postedBanner').slideDown(200);
                        $('#postedBannerDetails').text(
                            "{{ __('Entry #:') }} " + res.opening_entry.entry_number + " | {{ __('Approved on:') }} " + res.opening_entry.posted_at + " | {{ __('By:') }} " + (res.opening_entry.posted_by_name || '-')
                        );
                        $('#btnSaveDraft, #btnPostOpening').prop('disabled', true);
                    } else {
                        $('#postedBanner').slideUp(200);
                        $('#btnSaveDraft').prop('disabled', false);
                    }
                } else {
                    $('#postedBanner').slideUp(200);
                    $('#btnSaveDraft').prop('disabled', false);
                }

                res.accounts.forEach(function (acc) {
                    if (acc.type !== currentType) {
                        currentType = acc.type;
                        html += `
                            <tr class="account-type-header">
                                <td colspan="7" class="py-2 text-uppercase text-primary">
                                    <i class="icon-base bx bx-folder me-1"></i> ${acc.type_label}
                                </td>
                            </tr>
                        `;
                    }

                    const debitVal = acc.debit > 0 ? parseFloat(acc.debit).toFixed(4) : '';
                    const creditVal = acc.credit > 0 ? parseFloat(acc.credit).toFixed(4) : '';
                    const natureBadge = acc.normal_balance === 'debit'
                        ? '<span class="badge bg-label-success">' + "{{ __('Debit') }}" + '</span>'
                        : '<span class="badge bg-label-info">' + "{{ __('Credit') }}" + '</span>';

                    const disabledAttr = isPosted ? 'disabled' : '';

                    html += `
                        <tr class="account-row" data-account-id="${acc.id}" data-search="${acc.code.toLowerCase()} ${acc.name.toLowerCase()}">
                            <td><span class="fw-bold font-monospace text-heading">${acc.code}</span></td>
                            <td><span class="fw-semibold text-heading">${acc.name}</span></td>
                            <td class="text-center"><span class="badge bg-label-secondary font-11">${acc.type_label}</span></td>
                            <td class="text-center">${natureBadge}</td>
                            <td>
                                <input type="number" step="0.0001" min="0" class="form-control form-control-sm text-end debit-input font-monospace" value="${debitVal}" placeholder="0.0000" ${disabledAttr}>
                            </td>
                            <td>
                                <input type="number" step="0.0001" min="0" class="form-control form-control-sm text-end credit-input font-monospace" value="${creditVal}" placeholder="0.0000" ${disabledAttr}>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm notes-input" value="${acc.notes || ''}" placeholder="{{ __('Optional line memo...') }}" ${disabledAttr}>
                            </td>
                        </tr>
                    `;
                });

                $('#accountsTableBody').html(html || '<tr><td colspan="7" class="text-center py-4 text-muted">{{ __('No posting accounts found for this entity.') }}</td></tr>');
                calculateTotals();
            },
            error: function () {
                $('#accountsTableBody').html('<tr><td colspan="7" class="text-center py-4 text-danger">{{ __('Failed to load accounts.') }}</td></tr>');
            }
        });
    }

    // Mutual exclusion between Debit & Credit
    $(document).on('input', '.debit-input', function () {
        const val = parseFloat($(this).val()) || 0;
        if (val > 0) {
            $(this).closest('tr').find('.credit-input').val('');
        }
        calculateTotals();
    });

    $(document).on('input', '.credit-input', function () {
        const val = parseFloat($(this).val()) || 0;
        if (val > 0) {
            $(this).closest('tr').find('.debit-input').val('');
        }
        calculateTotals();
    });

    // Quick filter
    $('#accountSearchInput').on('input', function () {
        const query = $(this).val().toLowerCase().trim();
        $('.account-row').each(function () {
            const searchData = $(this).data('search');
            if (!query || searchData.includes(query)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    $('#entity_id, #fiscal_year_id').on('change', loadAccountsData);

    // Save Draft or Post
    function submitOpeningBalances(action) {
        const entityId = $('#entity_id').val();
        const fiscalYearId = $('#fiscal_year_id').val();
        const entryDate = $('#entry_date').val();

        if (!entryDate) {
            alertBox('danger', "{{ __('Please select a valid opening entry date.') }}");
            return;
        }

        const lines = [];
        $('.account-row').each(function () {
            const accId = $(this).data('account-id');
            const debit = parseFloat($(this).find('.debit-input').val()) || 0;
            const credit = parseFloat($(this).find('.credit-input').val()) || 0;
            const notes = $(this).find('.notes-input').val();

            if (debit > 0 || credit > 0) {
                lines.push({
                    account_id: accId,
                    debit: debit,
                    credit: credit,
                    notes: notes
                });
            }
        });

        if (lines.length === 0) {
            alertBox('danger', "{{ __('Please enter at least one non-zero opening balance line.') }}");
            return;
        }

        const btn = (action === 'post') ? $('#btnPostOpening') : $('#btnSaveDraft');
        btn.prop('disabled', true);

        $.ajax({
            url: "{{ route('accounting.opening-balances.save') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                entity_id: entityId,
                fiscal_year_id: fiscalYearId,
                entry_date: entryDate,
                action: action,
                lines: lines
            },
            success: function (res) {
                alertBox('success', res.message);
                loadAccountsData();
            },
            error: function (xhr) {
                let err = "{{ __('Failed to save opening balances.') }}";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    err = xhr.responseJSON.message;
                }
                alertBox('danger', err);
            },
            complete: function () {
                btn.prop('disabled', false);
            }
        });
    }

    $('#btnSaveDraft').on('click', () => submitOpeningBalances('save_draft'));
    $('#btnPostOpening').on('click', () => submitOpeningBalances('post'));

    // Reopen Entry
    $('#btnReopenEntry').on('click', function () {
        if (!confirm("{{ __('Are you sure you want to reopen this posted opening entry to draft?') }}")) return;

        const entityId = $('#entity_id').val();
        const fiscalYearId = $('#fiscal_year_id').val();

        $.ajax({
            url: "{{ route('accounting.opening-balances.reopen') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                entity_id: entityId,
                fiscal_year_id: fiscalYearId
            },
            success: function (res) {
                alertBox('success', res.message);
                loadAccountsData();
            },
            error: function (xhr) {
                let err = "{{ __('Failed to reopen opening entry.') }}";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    err = xhr.responseJSON.message;
                }
                alertBox('danger', err);
            }
        });
    });

    // Print Button
    $('#btnPrintStatement').on('click', function () {
        const entityId = $('#entity_id').val();
        const fiscalYearId = $('#fiscal_year_id').val();
        window.open(`{{ route('accounting.opening-balances.print') }}?entity_id=${entityId}&fiscal_year_id=${fiscalYearId}`, '_blank');
    });

    loadAccountsData();
});
</script>
@endsection
