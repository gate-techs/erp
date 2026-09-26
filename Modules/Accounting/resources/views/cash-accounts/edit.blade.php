@extends('layouts/contentNavbarLayout')

@section('title', __('Edit Cash or Bank Account') . ' - ' . config('variables.templateName'))

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-wallet text-primary font-24"></i>
            <span>{{ __('Edit Cash or Bank Account') }}: {{ $cashAccount->translate(app()->getLocale())?->name ?? $cashAccount->name ?? $cashAccount->code }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('accounting.cash-accounts.index') }}">{{ __('Cash & Bank Accounts') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Edit') }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('accounting.cash-accounts.show', $cashAccount->id) }}" class="btn btn-label-info d-flex align-items-center gap-2 shadow-xs">
            <i class="icon-base bx bx-show font-18"></i>
            <span>{{ __('View Details') }}</span>
        </a>
        <a href="{{ route('accounting.cash-accounts.index') }}" class="btn btn-label-secondary d-flex align-items-center gap-2 shadow-xs">
            <i class="icon-base bx bx-arrow-back font-18"></i>
            <span>{{ __('Back to List') }}</span>
        </a>
    </div>
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

<form action="{{ route('accounting.cash-accounts.update', $cashAccount->id) }}" method="POST" id="cashAccountForm">
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
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="entity_id">
                                {{ __('Accounting Entity') }} <span class="text-danger">*</span>
                            </label>
                            <select name="entity_id" id="entity_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Entity') }}" required>
                                <option value="">{{ __('Select Entity') }}</option>
                                @foreach ($entities as $entity)
                                    <option value="{{ $entity->id }}" data-currency="{{ $entity->currency ?? 'SAR' }}" @selected(old('entity_id', $cashAccount->entity_id) == $entity->id)>
                                        {{ $entity->translate(app()->getLocale())?->name ?? $entity->translate('ar')?->name ?? $entity->code }} ({{ $entity->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="type">
                                {{ __('Account Type') }} <span class="text-danger">*</span>
                            </label>
                            <select name="type" id="type" class="form-select select2" data-control="select2" required>
                                @foreach ($types as $type)
                                    <option value="{{ $type->value }}" @selected(old('type', $cashAccount->type?->value ?? $cashAccount->type) == $type->value)>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold" for="code">
                                {{ __('Account Code') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="code" id="code" class="form-control text-uppercase @error('code') is-invalid @enderror" value="{{ old('code', $cashAccount->code) }}" placeholder="{{ __('e.g. CS-01, BNK-01') }}" required>
                        </div>

                        <div class="col-12 col-md-8">
                            <label class="form-label fw-semibold" for="name_ar">
                                {{ __('Account Name (Arabic)') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name_ar" id="name_ar" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $cashAccount->translate('ar')?->name) }}" placeholder="{{ __('e.g. Main Cash Vault / Al Rajhi Bank') }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold" for="name_en">
                                {{ __('Account Name (English)') }}
                            </label>
                            <input type="text" name="name_en" id="name_en" class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en', $cashAccount->translate('en')?->name) }}" placeholder="{{ __('e.g. Main Safe / Al Rajhi Bank') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Bank Details Card (Shown conditionally if Bank) -->
            <div class="card shadow-sm border-0 mb-4" id="bankDetailsCard" style="display: none;">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-building text-primary font-20"></i>
                    <h6 class="fw-bold mb-0">{{ __('Bank Details') }}</h6>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="bank_name">
                                {{ __('Bank Name') }}
                            </label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control" value="{{ old('bank_name', $cashAccount->bank_name) }}" placeholder="{{ __('e.g. Al Rajhi Bank / Riyad Bank') }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="account_number">
                                {{ __('Bank Account Number') }}
                            </label>
                            <input type="text" name="account_number" id="account_number" class="form-control" value="{{ old('account_number', $cashAccount->account_number) }}" placeholder="{{ __('e.g. 1234567890') }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="iban">
                                {{ __('IBAN') }}
                            </label>
                            <input type="text" name="iban" id="iban" class="form-control text-uppercase" value="{{ old('iban', $cashAccount->iban) }}" placeholder="{{ __('SA0000000000000000000000') }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="swift_code">
                                {{ __('SWIFT / BIC Code') }}
                            </label>
                            <input type="text" name="swift_code" id="swift_code" class="form-control text-uppercase" value="{{ old('swift_code', $cashAccount->swift_code) }}" placeholder="{{ __('e.g. RJHISARI') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Accounting & Linking Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-link-alt text-primary font-20"></i>
                    <h6 class="fw-bold mb-0">{{ __('Accounting Link & Journal') }}</h6>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="account_id">
                                {{ __('Linked General Ledger Account') }}
                            </label>
                            <select name="account_id" id="account_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select GL Account') }}">
                                <option value="">{{ __('None / Select Later') }}</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}" data-entity="{{ $account->entity_id }}" @selected(old('account_id', $cashAccount->account_id) == $account->id)>
                                        {{ $account->code }} - {{ $account->translate(app()->getLocale())?->name ?? $account->translate('ar')?->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('The GL asset account mapped to this register in the Chart of Accounts.') }}</small>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="journal_id">
                                {{ __('Linked Journal') }}
                            </label>
                            <select name="journal_id" id="journal_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select Journal') }}">
                                <option value="">{{ __('None') }}</option>
                                @foreach ($journals as $journal)
                                    <option value="{{ $journal->id }}" data-entity="{{ $journal->entity_id }}" @selected(old('journal_id', $cashAccount->journal_id) == $journal->id)>
                                        [{{ $journal->code }}] {{ $journal->translate(app()->getLocale())?->name ?? $journal->translate('ar')?->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('Optional: Default journal for transactions through this account.') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Description & Notes Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-notepad text-primary font-20"></i>
                    <h6 class="fw-bold mb-0">{{ __('Notes & Description') }}</h6>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="description_ar">
                                {{ __('Description (Arabic)') }}
                            </label>
                            <textarea name="description_ar" id="description_ar" rows="3" class="form-control" placeholder="{{ __('Additional description or notes in Arabic...') }}">{{ old('description_ar', $cashAccount->translate('ar')?->description) }}</textarea>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="description_en">
                                {{ __('Description (English)') }}
                            </label>
                            <textarea name="description_en" id="description_en" rows="3" class="form-control" placeholder="{{ __('Additional description or notes...') }}">{{ old('description_en', $cashAccount->translate('en')?->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / Settings Column -->
        <div class="col-12 col-lg-4">
            <!-- Settings Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-cog text-primary font-20"></i>
                    <h6 class="fw-bold mb-0">{{ __('Account Settings') }}</h6>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold d-flex align-items-center justify-content-between" for="currency">
                                <span>{{ __('Currency') }}</span>
                                <span class="badge bg-label-info font-11"><i class="icon-base bx bx-lock-alt me-1"></i>{{ __('From Entity') }}</span>
                            </label>
                            <input type="text" name="currency" id="currency" class="form-control text-uppercase bg-light fw-bold" value="{{ old('currency', $cashAccount->currency ?? 'SAR') }}" readonly>
                            <small class="text-muted d-block mt-1">{{ __('The currency is inherited directly from the accounting entity and cannot be modified.') }}</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold" for="opening_balance">
                                {{ __('Opening Balance') }}
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.0001" name="opening_balance" id="opening_balance" class="form-control text-end" value="{{ old('opening_balance', $cashAccount->opening_balance) }}">
                                <span class="input-group-text font-12" id="currencyLabel">{{ $cashAccount->currency ?? 'SAR' }}</span>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold" for="responsible_user_id">
                                {{ __('Responsible Person / Custodian') }}
                            </label>
                            <select name="responsible_user_id" id="responsible_user_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select User') }}">
                                <option value="">{{ __('None') }}</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('responsible_user_id', $cashAccount->responsible_user_id) == $user->id)>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('Employee in charge of this safe or bank account.') }}</small>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $cashAccount->is_active))>
                                <label class="form-check-label fw-semibold" for="is_active">{{ __('Active Account') }}</label>
                            </div>
                            <small class="text-muted d-block mt-1">{{ __('Inactive accounts cannot be used in new transactions.') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2 mb-2">
                        <i class="icon-base bx bx-save font-18"></i>
                        <span>{{ __('Update Account') }}</span>
                    </button>
                    <a href="{{ route('accounting.cash-accounts.index') }}" class="btn btn-label-secondary w-100">
                        {{ __('Cancel') }}
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

    function toggleBankFields() {
        const type = $('#type').val();
        if (type === 'bank') {
            $('#bankDetailsCard').slideDown(200);
        } else {
            $('#bankDetailsCard').slideUp(200);
        }
    }

    $('#type').on('change', toggleBankFields);
    toggleBankFields();

    // Filter GL Accounts & Journals and update Currency by selected Entity
    $('#entity_id').on('change', function () {
        const selectedOption = $(this).find(':selected');
        const entityId = $(this).val();
        const currency = selectedOption.data('currency') || 'SAR';

        $('#currency').val(currency);
        $('#currencyLabel').text(currency);
        
        $('#account_id option').each(function () {
            const accEntity = $(this).data('entity');
            if (!accEntity || !entityId || accEntity == entityId) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        $('#journal_id option').each(function () {
            const jEntity = $(this).data('entity');
            if (!jEntity || !entityId || jEntity == entityId) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    if ($('#entity_id').val()) {
        $('#entity_id').trigger('change');
    }
});
</script>
@endsection
