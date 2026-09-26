@extends('layouts/contentNavbarLayout')

@section('title', __('Edit Journal') . ' - ' . config('variables.templateName'))

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-book-open text-primary font-24"></i>
            <span>{{ __('Edit Journal') }}: {{ $journal->translate(app()->getLocale())?->name ?? $journal->code }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('accounting.journals.index') }}">{{ __('Journals') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Edit') }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('accounting.journals.index') }}" class="btn btn-label-secondary d-flex align-items-center gap-2 shadow-xs">
        <i class="icon-base bx bx-arrow-back font-18"></i>
        <span>{{ __('Back to Journals') }}</span>
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

<form action="{{ route('accounting.journals.update', $journal->id) }}" method="POST" id="journalForm">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-12 col-lg-8">
            <!-- 1. Basic Information Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-info-circle text-primary font-20"></i>
                    <h6 class="fw-bold mb-0">{{ __('Basic Information') }}</h6>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold" for="entity_id">
                                {{ __('Accounting Entity') }} <span class="text-danger">*</span>
                            </label>
                            <select name="entity_id" id="entity_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Entity') }}" required>
                                <option value="">{{ __('Select Entity') }}</option>
                                @foreach ($entities as $entity)
                                    <option value="{{ $entity->id }}" @selected(old('entity_id', $journal->entity_id) == $entity->id)>
                                        {{ $entity->translate(app()->getLocale())?->name ?? $entity->translate('ar')?->name ?? $entity->code }} ({{ $entity->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold" for="type">
                                {{ __('Journal Type') }} <span class="text-danger">*</span>
                            </label>
                            <select name="type" id="type" class="form-select select2" data-control="select2" required>
                                @foreach ($types as $type)
                                    <option value="{{ $type->value }}" @selected(old('type', $journal->type->value) == $type->value)>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-5">
                            <label class="form-label fw-semibold" for="name_ar">
                                {{ __('Journal Name (Arabic)') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name_ar" id="name_ar" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $journal->translate('ar')?->name) }}" required>
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold" for="code">
                                {{ __('Short Code') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="code" id="code" class="form-control text-uppercase @error('code') is-invalid @enderror" value="{{ old('code', $journal->code) }}" maxlength="5" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold" for="name_en">
                                {{ __('Journal Name (English)') }}
                            </label>
                            <input type="text" name="name_en" id="name_en" class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en', $journal->translate('en')?->name) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Odoo Dynamic Accounting & Accounts Configuration Card -->
            <div class="card shadow-sm border-0 mb-4" id="accountsCard">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="icon-base bx bx-credit-card text-primary font-20"></i>
                        <h6 class="fw-bold mb-0" id="accountsCardTitle">{{ __('Default & Control Accounts') }}</h6>
                    </div>
                    <span class="badge bg-label-primary px-3 py-1" id="journalTypeBadge">{{ __('Sales') }}</span>
                </div>
                <div class="card-body pt-4">
                    <!-- Helper context alert banner -->
                    <div class="alert alert-light-primary border-primary border-start border-4 mb-4 py-2 px-3 small text-muted d-flex align-items-center gap-2" id="typeContextNotice">
                        <i class="icon-base bx bx-bulb text-primary font-18 flex-shrink-0"></i>
                        <span id="typeNoticeText">{{ __('Configure default accounts specific to this journal type according to Odoo accounting standards.') }}</span>
                    </div>

                    <div class="row g-3">
                        <!-- Primary Default Account -->
                        <div class="col-12" id="defaultAccountContainer">
                            <label class="form-label fw-semibold d-flex align-items-center" for="default_account_id">
                                <span id="defaultAccountLabel">{{ __('Default Income Account') }}</span>
                                <span class="text-danger ms-1" id="defaultAccountRequiredStar">*</span>
                                <i class="icon-base bx bx-help-circle text-muted font-16 ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Default revenue account used when creating customer invoices.') }}" id="defaultAccountTooltip"></i>
                            </label>
                            <select name="default_account_id" id="default_account_id" class="form-select select2 account-select" data-control="select2" data-placeholder="{{ __('Select Account') }}" data-allow-clear="true">
                                <option value="">{{ __('Select Account') }}</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}" data-entity="{{ $account->entity_id }}" data-type="{{ $account->account_type?->value }}" data-code="{{ $account->code }}" @selected(old('default_account_id', $journal->default_account_id) == $account->id)>
                                        {{ $account->code }} - {{ $account->translate(app()->getLocale())?->name ?? $account->translate('ar')?->name ?? $account->code }} ({{ $account->account_type?->label() ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Suspense Account -->
                        <div class="col-12 col-md-6 dynamic-account-field" id="suspenseAccountContainer">
                            <label class="form-label fw-semibold d-flex align-items-center" for="suspense_account_id">
                                <span id="suspenseAccountLabel">{{ __('Suspense Account') }}</span>
                                <i class="icon-base bx bx-help-circle text-muted font-16 ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Temporary holding account for unreconciled entries.') }}" id="suspenseAccountTooltip"></i>
                            </label>
                            <select name="suspense_account_id" id="suspense_account_id" class="form-select select2 account-select" data-control="select2" data-placeholder="{{ __('Select Suspense Account') }}" data-allow-clear="true">
                                <option value="">{{ __('Select Suspense Account') }}</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}" data-entity="{{ $account->entity_id }}" data-type="{{ $account->account_type?->value }}" data-code="{{ $account->code }}" @selected(old('suspense_account_id', $journal->suspense_account_id) == $account->id)>
                                        {{ $account->code }} - {{ $account->translate(app()->getLocale())?->name ?? $account->translate('ar')?->name ?? $account->code }} ({{ $account->account_type?->label() ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Profit Account -->
                        <div class="col-12 col-md-6 dynamic-account-field" id="profitAccountContainer">
                            <label class="form-label fw-semibold d-flex align-items-center" for="profit_account_id">
                                <span id="profitAccountLabel">{{ __('Profit Account') }}</span>
                                <i class="icon-base bx bx-help-circle text-muted font-16 ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Account for cash or foreign currency gain.') }}" id="profitAccountTooltip"></i>
                            </label>
                            <select name="profit_account_id" id="profit_account_id" class="form-select select2 account-select" data-control="select2" data-placeholder="{{ __('Select Profit Account') }}" data-allow-clear="true">
                                <option value="">{{ __('Select Profit Account') }}</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}" data-entity="{{ $account->entity_id }}" data-type="{{ $account->account_type?->value }}" data-code="{{ $account->code }}" @selected(old('profit_account_id', $journal->profit_account_id) == $account->id)>
                                        {{ $account->code }} - {{ $account->translate(app()->getLocale())?->name ?? $account->translate('ar')?->name ?? $account->code }} ({{ $account->account_type?->label() ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Loss Account -->
                        <div class="col-12 col-md-6 dynamic-account-field" id="lossAccountContainer">
                            <label class="form-label fw-semibold d-flex align-items-center" for="loss_account_id">
                                <span id="lossAccountLabel">{{ __('Loss Account') }}</span>
                                <i class="icon-base bx bx-help-circle text-muted font-16 ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Account for cash shortage or bank fees/losses.') }}" id="lossAccountTooltip"></i>
                            </label>
                            <select name="loss_account_id" id="loss_account_id" class="form-select select2 account-select" data-control="select2" data-placeholder="{{ __('Select Loss Account') }}" data-allow-clear="true">
                                <option value="">{{ __('Select Loss Account') }}</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}" data-entity="{{ $account->entity_id }}" data-type="{{ $account->account_type?->value }}" data-code="{{ $account->code }}" @selected(old('loss_account_id', $journal->loss_account_id) == $account->id)>
                                        {{ $account->code }} - {{ $account->translate(app()->getLocale())?->name ?? $account->translate('ar')?->name ?? $account->code }} ({{ $account->account_type?->label() ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Notes & Descriptions Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-comment-detail text-primary font-20"></i>
                    <h6 class="fw-bold mb-0">{{ __('Description & Notes') }}</h6>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="description_ar">{{ __('Description (Arabic)') }}</label>
                            <textarea name="description_ar" id="description_ar" class="form-control" rows="3">{{ old('description_ar', $journal->translate('ar')?->description) }}</textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="description_en">{{ __('Description (English)') }}</label>
                            <textarea name="description_en" id="description_en" class="form-control" rows="3">{{ old('description_en', $journal->translate('en')?->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Form Column -->
        <div class="col-12 col-lg-4">
            <!-- 4. Numbering & Sequences Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-hash text-primary font-20"></i>
                    <h6 class="fw-bold mb-0">{{ __('Sequence & Numbering') }}</h6>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold" for="currency">{{ __('Currency') }}</label>
                            <input type="text" name="currency" id="currency" class="form-control text-uppercase" maxlength="10" value="{{ old('currency', $journal->currency) }}" placeholder="{{ __('e.g. KWD, SAR, USD') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold d-flex align-items-center" for="sequence_prefix">
                                <span>{{ __('Sequence Prefix') }}</span>
                                <i class="icon-base bx bx-help-circle text-muted font-16 ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Supports %Y for year, %m for month.') }}"></i>
                            </label>
                            <input type="text" name="sequence_prefix" id="sequence_prefix" class="form-control" maxlength="50" value="{{ old('sequence_prefix', $journal->sequence_prefix) }}" placeholder="{{ __('e.g. SAL/%Y/') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold" for="sequence_number_next">{{ __('Next Sequence Number') }} <span class="text-danger">*</span></label>
                            <input type="number" name="sequence_number_next" id="sequence_number_next" class="form-control" min="1" value="{{ old('sequence_number_next', $journal->sequence_number_next) }}" required>
                        </div>

                        <div class="col-12 pt-2">
                            <!-- Refund sequence switch -->
                            <div class="form-check form-switch mb-3" id="refundSequenceContainer">
                                <input class="form-check-input" type="checkbox" name="refund_sequence" value="1" id="refund_sequence" @checked(old('refund_sequence', $journal->refund_sequence))>
                                <label class="form-check-label fw-semibold d-inline-flex align-items-center" for="refund_sequence">
                                    <span>{{ __('Dedicated Refund Sequence') }}</span>
                                    <i class="icon-base bx bx-help-circle text-muted font-16 ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Creates a dedicated sequence for Credit / Debit notes.') }}"></i>
                                </label>
                            </div>

                            <!-- Payment sequence switch -->
                            <div class="form-check form-switch" id="paymentSequenceContainer">
                                <input class="form-check-input" type="checkbox" name="payment_sequence" value="1" id="payment_sequence" @checked(old('payment_sequence', $journal->payment_sequence))>
                                <label class="form-check-label fw-semibold d-inline-flex align-items-center" for="payment_sequence">
                                    <span>{{ __('Dedicated Payment Sequence') }}</span>
                                    <i class="icon-base bx bx-help-circle text-muted font-16 ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Creates a dedicated sequence for outgoing payments.') }}"></i>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Options & Security Rules Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-slider-alt text-primary font-20"></i>
                    <h6 class="fw-bold mb-0">{{ __('Options & Security') }}</h6>
                </div>
                <div class="card-body pt-4">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="allow_manual_entries" value="1" id="allow_manual_entries" @checked(old('allow_manual_entries', $journal->allow_manual_entries))>
                        <label class="form-check-label fw-semibold" for="allow_manual_entries">{{ __('Allow Manual Entries') }}</label>
                    </div>

                    <div class="form-check form-switch mb-3" id="reconcileContainer">
                        <input class="form-check-input" type="checkbox" name="reconcile" value="1" id="reconcile" @checked(old('reconcile', $journal->reconcile))>
                        <label class="form-check-label fw-semibold" for="reconcile">{{ __('Enable Reconciliation') }}</label>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="restrict_mode_hash_table" value="1" id="restrict_mode_hash_table" @checked(old('restrict_mode_hash_table', $journal->restrict_mode_hash_table))>
                        <label class="form-check-label fw-semibold d-inline-flex align-items-center" for="restrict_mode_hash_table">
                            <span>{{ __('Secure Posted Entries with Hash') }}</span>
                            <i class="icon-base bx bx-help-circle text-muted font-16 ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Secure Posted Entries with Hash') }}"></i>
                        </label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $journal->is_active))>
                        <label class="form-check-label fw-semibold" for="is_active">{{ __('Active Status') }}</label>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex flex-column gap-2">
                    <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2" id="btnSubmitJournal">
                        <i class="icon-base bx bx-save font-18"></i>
                        <span class="fw-semibold">{{ __('Save Changes') }}</span>
                    </button>
                    <a href="{{ route('accounting.journals.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2">
                        <i class="icon-base bx bx-x font-18"></i>
                        <span>{{ __('Cancel') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;

    function initTooltips() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return bootstrap.Tooltip.getOrCreateInstance(tooltipTriggerEl);
            });
        }
    }

    function setTooltipTitle(elementId, newTitle) {
        const el = document.getElementById(elementId);
        if (!el) return;
        el.setAttribute('data-bs-original-title', newTitle);
        el.setAttribute('title', newTitle);
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const instance = bootstrap.Tooltip.getInstance(el);
            if (instance) instance.dispose();
            new bootstrap.Tooltip(el);
        }
    }

    // Odoo-style Type Mapping
    const typeConfigs = {
        sale: {
            title: "{{ __('Sales Journal Accounts') }}",
            badge: "{{ __('Sales') }}",
            badgeClass: "bg-label-primary",
            defaultLabel: "{{ __('Default Income Account') }}",
            defaultHint: "{{ __('Default revenue account used when creating customer invoices.') }}",
            defaultRequired: true,
            showSuspense: false,
            showProfitLoss: false,
            showRefundSeq: true,
            showPaymentSeq: false,
            notice: "{{ __('Sales Journal records customer invoices and credit notes. Requires a default income account.') }}"
        },
        purchase: {
            title: "{{ __('Purchase Journal Accounts') }}",
            badge: "{{ __('Purchase') }}",
            badgeClass: "bg-label-warning",
            defaultLabel: "{{ __('Default Expense Account') }}",
            defaultHint: "{{ __('Default cost / expense account used when registering vendor bills.') }}",
            defaultRequired: true,
            showSuspense: false,
            showProfitLoss: false,
            showRefundSeq: true,
            showPaymentSeq: false,
            notice: "{{ __('Purchase Journal records vendor bills and debit notes. Requires a default expense account.') }}"
        },
        bank: {
            title: "{{ __('Bank Account & Suspense Settings') }}",
            badge: "{{ __('Bank') }}",
            badgeClass: "bg-label-info",
            defaultLabel: "{{ __('Bank Account') }}",
            defaultHint: "{{ __('Main bank asset account holding the cash balance.') }}",
            defaultRequired: true,
            showSuspense: true,
            suspenseLabel: "{{ __('Bank Suspense Account') }}",
            suspenseHint: "{{ __('Temporary holding account for unreconciled bank transactions.') }}",
            showProfitLoss: true,
            profitLabel: "{{ __('Foreign Currency Gain Account') }}",
            profitHint: "{{ __('Account for cash or foreign currency gain.') }}",
            lossLabel: "{{ __('Foreign Currency Loss / Bank Fees Account') }}",
            lossHint: "{{ __('Account for cash shortage or bank fees/losses.') }}",
            showRefundSeq: false,
            showPaymentSeq: true,
            notice: "{{ __('Bank Journal records bank statements and transactions. Requires bank account and suspense account.') }}"
        },
        cash: {
            title: "{{ __('Cash & Safe Settings') }}",
            badge: "{{ __('Cash') }}",
            badgeClass: "bg-label-success",
            defaultLabel: "{{ __('Cash Account') }}",
            defaultHint: "{{ __('Main petty cash / safe account.') }}",
            defaultRequired: true,
            showSuspense: true,
            suspenseLabel: "{{ __('Cash Suspense Account') }}",
            suspenseHint: "{{ __('Temporary holding account for cash settlements and transfers.') }}",
            showProfitLoss: true,
            profitLabel: "{{ __('Cash Difference Gain Account') }}",
            profitHint: "{{ __('Account for cash or foreign currency gain.') }}",
            lossLabel: "{{ __('Cash Difference Loss Account') }}",
            lossHint: "{{ __('Account for cash shortage or bank fees/losses.') }}",
            showRefundSeq: false,
            showPaymentSeq: true,
            notice: "{{ __('Cash Journal records daily safe transactions, cash receipts, and cash difference accounts.') }}"
        },
        credit_card: {
            title: "{{ __('Credit Card Accounts') }}",
            badge: "{{ __('Credit Card') }}",
            badgeClass: "bg-label-secondary",
            defaultLabel: "{{ __('Credit Card Account') }}",
            defaultHint: "{{ __('Liability / Clearing account for credit card operations.') }}",
            defaultRequired: true,
            showSuspense: true,
            suspenseLabel: "{{ __('Suspense Account') }}",
            suspenseHint: "{{ __('Temporary holding account for credit card transactions.') }}",
            showProfitLoss: true,
            profitLabel: "{{ __('Profit Account') }}",
            profitHint: "{{ __('Account for cash or foreign currency gain.') }}",
            lossLabel: "{{ __('Loss Account') }}",
            lossHint: "{{ __('Account for cash shortage or bank fees/losses.') }}",
            showRefundSeq: false,
            showPaymentSeq: true,
            notice: "{{ __('Credit Card Journal records credit card merchant statements and disbursements.') }}"
        },
        general: {
            title: "{{ __('General Journal Accounts') }}",
            badge: "{{ __('Miscellaneous') }}",
            badgeClass: "bg-label-dark",
            defaultLabel: "{{ __('Default Counterpart Account') }}",
            defaultHint: "{{ __('Optional default account used to balance miscellaneous entries.') }}",
            defaultRequired: false,
            showSuspense: false,
            showProfitLoss: false,
            showRefundSeq: false,
            showPaymentSeq: false,
            notice: "{{ __('General Journal records opening balances, depreciations, and adjustments without specific bank/sales accounts.') }}"
        }
    };

    function updateTypeUI() {
        const selectedType = $('#type').val() || 'general';
        const config = typeConfigs[selectedType] || typeConfigs.general;

        $('#accountsCardTitle').text(config.title);
        $('#journalTypeBadge').text(config.badge).attr('class', 'badge ' + config.badgeClass + ' px-3 py-1');
        $('#typeNoticeText').text(config.notice);

        // Primary Default Account
        $('#defaultAccountLabel').text(config.defaultLabel);
        setTooltipTitle('defaultAccountTooltip', config.defaultHint);
        if (config.defaultRequired) {
            $('#defaultAccountRequiredStar').show();
        } else {
            $('#defaultAccountRequiredStar').hide();
        }

        // Suspense Account
        if (config.showSuspense) {
            $('#suspenseAccountContainer').show();
            $('#suspenseAccountLabel').text(config.suspenseLabel || "{{ __('Suspense Account') }}");
            setTooltipTitle('suspenseAccountTooltip', config.suspenseHint || "{{ __('Temporary holding account for unreconciled entries.') }}");
        } else {
            $('#suspenseAccountContainer').hide();
        }

        // Profit / Loss Accounts
        if (config.showProfitLoss) {
            $('#profitAccountContainer').show();
            $('#lossAccountContainer').show();
            $('#profitAccountLabel').text(config.profitLabel || "{{ __('Profit Account') }}");
            setTooltipTitle('profitAccountTooltip', config.profitHint || "{{ __('Account for cash or foreign currency gain.') }}");
            $('#lossAccountLabel').text(config.lossLabel || "{{ __('Loss Account') }}");
            setTooltipTitle('lossAccountTooltip', config.lossHint || "{{ __('Account for cash shortage or bank fees/losses.') }}");
        } else {
            $('#profitAccountContainer').hide();
            $('#lossAccountContainer').hide();
        }

        // Sequences
        if (config.showRefundSeq) {
            $('#refundSequenceContainer').show();
        } else {
            $('#refundSequenceContainer').hide();
        }

        if (config.showPaymentSeq) {
            $('#paymentSequenceContainer').show();
        } else {
            $('#paymentSequenceContainer').hide();
        }
    }

    // Cache raw options on load
    const rawOptions = {};
    $('.account-select').each(function () {
        const id = $(this).attr('id');
        rawOptions[id] = [];
        $(this).find('option').each(function () {
            if ($(this).val()) {
                rawOptions[id].push({
                    value: String($(this).val()),
                    text: $(this).text(),
                    entity: String($(this).data('entity') || ''),
                    type: String($(this).data('type') || ''),
                    code: String($(this).data('code') || '')
                });
            }
        });
    });

    function isAccountAllowedForField(opt, fieldId, journalType) {
        const code = opt.code || '';
        const type = opt.type || '';
        const text = opt.text || '';

        if (fieldId === 'default_account_id') {
            switch (journalType) {
                case 'bank':
                    return code.startsWith('1101') || text.includes('بنك') || text.toLowerCase().includes('bank');
                case 'cash':
                    return code.startsWith('1101') || text.includes('صندوق') || text.includes('خزينة') || text.includes('عهدة');
                case 'sale':
                    return type === 'income' || code.startsWith('4');
                case 'purchase':
                    return type === 'expense' || code.startsWith('5');
                case 'credit_card':
                    return code.startsWith('1101') || code.startsWith('21') || type === 'liability' || type === 'asset';
                case 'general':
                default:
                    return true;
            }
        } else if (fieldId === 'profit_account_id') {
            return type === 'income' || code.startsWith('4');
        } else if (fieldId === 'loss_account_id') {
            return type === 'expense' || code.startsWith('5');
        } else if (fieldId === 'suspense_account_id') {
            return code.startsWith('11') || code.startsWith('21') || type === 'asset' || type === 'liability';
        }
        return true;
    }

    function getBestSuggestedAccount(journalType, entityId) {
        const options = (rawOptions['default_account_id'] || []).filter(opt => {
            return (!entityId || !opt.entity || opt.entity === String(entityId));
        });

        if (journalType === 'bank') {
            const bankAcc = options.find(opt => opt.code === '110102' || opt.text.includes('بنك') || opt.text.toLowerCase().includes('bank'));
            if (bankAcc) return bankAcc.value;
            const any1101 = options.find(opt => opt.code.startsWith('1101'));
            if (any1101) return any1101.value;
        } else if (journalType === 'cash') {
            const cashAcc = options.find(opt => opt.code === '110101' || opt.text.includes('الصندوق الرئيسي') || opt.text.includes('خزينة'));
            if (cashAcc) return cashAcc.value;
            const any1101 = options.find(opt => opt.code.startsWith('1101'));
            if (any1101) return any1101.value;
        } else if (journalType === 'sale') {
            const saleAcc = options.find(opt => opt.code === '41' || (opt.type === 'income' && !opt.text.includes('أخرى')));
            if (saleAcc) return saleAcc.value;
            const anyIncome = options.find(opt => opt.type === 'income' || opt.code.startsWith('4'));
            if (anyIncome) return anyIncome.value;
        } else if (journalType === 'purchase') {
            const purchAcc = options.find(opt => opt.code === '5101' || opt.type === 'expense');
            if (purchAcc) return purchAcc.value;
        }
        return '';
    }

    function filterAccounts(autoSelect = false) {
        const entityId = $('#entity_id').val();
        const journalType = $('#type').val() || 'general';

        $('.account-select').each(function () {
            const selectEl = $(this);
            const selectId = selectEl.attr('id');
            let currentVal = String(selectEl.val() || '');

            const placeholder = selectId === 'default_account_id' ? "{{ __('Select Account') }}" : (selectId === 'suspense_account_id' ? "{{ __('Select Suspense Account') }}" : (selectId === 'profit_account_id' ? "{{ __('Select Profit Account') }}" : "{{ __('Select Loss Account') }}"));

            let newHtml = `<option value="">${placeholder}</option>`;
            let matchingOptions = [];

            (rawOptions[selectId] || []).forEach(function (opt) {
                if (entityId && opt.entity && opt.entity !== String(entityId)) {
                    return;
                }
                if (isAccountAllowedForField(opt, selectId, journalType)) {
                    matchingOptions.push(opt);
                }
            });

            let selectedValue = currentVal;
            const hasCurrentVal = matchingOptions.some(opt => opt.value === currentVal);

            if (selectId === 'default_account_id') {
                if (autoSelect || !hasCurrentVal) {
                    const suggested = getBestSuggestedAccount(journalType, entityId);
                    if (suggested) {
                        selectedValue = suggested;
                    } else if (matchingOptions.length > 0) {
                        selectedValue = matchingOptions[0].value;
                    } else {
                        selectedValue = '';
                    }
                }
            } else if (!hasCurrentVal) {
                selectedValue = '';
            }

            matchingOptions.forEach(function (opt) {
                const isSelected = (selectedValue && String(selectedValue) === opt.value);
                newHtml += `<option value="${opt.value}" data-entity="${opt.entity}" data-type="${opt.type}" data-code="${opt.code}" ${isSelected ? 'selected' : ''}>${opt.text}</option>`;
            });

            selectEl.html(newHtml);
            selectEl.val(selectedValue).trigger('change');
            if (selectEl.data('select2')) {
                selectEl.trigger('change.select2');
            }
        });
    }

    $('#type').on('change', function () {
        updateTypeUI();
        filterAccounts(true);
    });

    $('#entity_id').on('change', function () {
        filterAccounts(true);
    });

    // Initial load
    initTooltips();
    updateTypeUI();
    filterAccounts(false);
});
</script>
@endsection
