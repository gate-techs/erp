@extends('layouts/contentNavbarLayout')

@section('title', __('Cash Account Details') . ' - ' . config('variables.templateName'))

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-wallet text-primary font-24"></i>
            <span>{{ $cashAccount->translate(app()->getLocale())?->name ?? $cashAccount->name ?? $cashAccount->code }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('accounting.cash-accounts.index') }}">{{ __('Cash & Bank Accounts') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Details') }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('accounting.cash-accounts.edit', $cashAccount->id) }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
            <i class="icon-base bx bx-edit-alt font-18"></i>
            <span>{{ __('Edit Account') }}</span>
        </a>
        <a href="{{ route('accounting.cash-accounts.index') }}" class="btn btn-label-secondary d-flex align-items-center gap-2 shadow-xs">
            <i class="icon-base bx bx-arrow-back font-18"></i>
            <span>{{ __('Back to List') }}</span>
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Main Info -->
    <div class="col-12 col-lg-8">
        <!-- Overview Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-info-circle text-primary font-20"></i>
                    <h6 class="fw-bold mb-0">{{ __('Account Overview') }}</h6>
                </div>
                <div>
                    @if($cashAccount->type === \Modules\Accounting\Enums\CashAccountType::CASH)
                        <span class="badge bg-label-success"><i class="icon-base bx bx-wallet me-1 font-14"></i>{{ __('Cash Register') }}</span>
                    @else
                        <span class="badge bg-label-primary"><i class="icon-base bx bx-building me-1 font-14"></i>{{ __('Bank Account') }}</span>
                    @endif
                </div>
            </div>
            <div class="card-body pt-4">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="text-muted small d-block">{{ __('Account Name (Arabic)') }}</label>
                        <span class="fw-semibold text-heading">{{ $cashAccount->translate('ar')?->name ?? '-' }}</span>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="text-muted small d-block">{{ __('Account Name (English)') }}</label>
                        <span class="fw-semibold text-heading">{{ $cashAccount->translate('en')?->name ?? '-' }}</span>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="text-muted small d-block">{{ __('Account Code') }}</label>
                        <span class="badge bg-label-secondary font-13">{{ $cashAccount->code }}</span>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="text-muted small d-block">{{ __('Accounting Entity') }}</label>
                        <span class="fw-semibold">{{ $cashAccount->entity?->translate(app()->getLocale())?->name ?? $cashAccount->entity?->code ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($cashAccount->type === \Modules\Accounting\Enums\CashAccountType::BANK)
        <!-- Bank Details Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                <i class="icon-base bx bx-building text-primary font-20"></i>
                <h6 class="fw-bold mb-0">{{ __('Bank Details') }}</h6>
            </div>
            <div class="card-body pt-4">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="text-muted small d-block">{{ __('Bank Name') }}</label>
                        <span class="fw-semibold text-heading">{{ $cashAccount->bank_name ?? '-' }}</span>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="text-muted small d-block">{{ __('Account Number') }}</label>
                        <span class="fw-semibold font-monospace">{{ $cashAccount->account_number ?? '-' }}</span>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="text-muted small d-block">{{ __('IBAN') }}</label>
                        <span class="fw-semibold font-monospace text-uppercase">{{ $cashAccount->iban ?? '-' }}</span>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="text-muted small d-block">{{ __('SWIFT / BIC Code') }}</label>
                        <span class="fw-semibold font-monospace text-uppercase">{{ $cashAccount->swift_code ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Linked Accounting Information -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                <i class="icon-base bx bx-link-alt text-primary font-20"></i>
                <h6 class="fw-bold mb-0">{{ __('Accounting Link & Journal') }}</h6>
            </div>
            <div class="card-body pt-4">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="text-muted small d-block">{{ __('GL Account') }}</label>
                        @if($cashAccount->account)
                            <span class="fw-semibold">{{ $cashAccount->account->code }} - {{ $cashAccount->account->translate(app()->getLocale())?->name ?? $cashAccount->account->name }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="text-muted small d-block">{{ __('Linked Journal') }}</label>
                        @if($cashAccount->journal)
                            <span class="fw-semibold">[{{ $cashAccount->journal->code }}] {{ $cashAccount->journal->translate(app()->getLocale())?->name ?? $cashAccount->journal->name }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($cashAccount->translate('ar')?->description || $cashAccount->translate('en')?->description || $cashAccount->notes)
        <!-- Notes Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                <i class="icon-base bx bx-notepad text-primary font-20"></i>
                <h6 class="fw-bold mb-0">{{ __('Notes & Description') }}</h6>
            </div>
            <div class="card-body pt-4">
                <div class="row g-3">
                    @if($cashAccount->translate('ar')?->description)
                    <div class="col-12 col-md-6">
                        <label class="text-muted small d-block">{{ __('Description (Arabic)') }}</label>
                        <p class="mb-0 text-heading">{{ $cashAccount->translate('ar')->description }}</p>
                    </div>
                    @endif
                    @if($cashAccount->translate('en')?->description)
                    <div class="col-12 col-md-6">
                        <label class="text-muted small d-block">{{ __('Description (English)') }}</label>
                        <p class="mb-0 text-heading">{{ $cashAccount->translate('en')->description }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar Info -->
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                <i class="icon-base bx bx-cog text-primary font-20"></i>
                <h6 class="fw-bold mb-0">{{ __('Settings & Custodian') }}</h6>
            </div>
            <div class="card-body pt-4">
                <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                    <li class="d-flex align-items-center justify-content-between">
                        <span class="text-muted">{{ __('Currency') }}:</span>
                        <span class="badge bg-label-secondary fw-bold">{{ $cashAccount->currency ?? 'SAR' }}</span>
                    </li>
                    <li class="d-flex align-items-center justify-content-between">
                        <span class="text-muted">{{ __('Opening Balance') }}:</span>
                        <span class="fw-bold font-monospace">{{ number_format($cashAccount->opening_balance, 2) }} {{ $cashAccount->currency ?? 'SAR' }}</span>
                    </li>
                    <li class="d-flex align-items-center justify-content-between">
                        <span class="text-muted">{{ __('Responsible Person') }}:</span>
                        <span class="fw-semibold">{{ $cashAccount->responsibleUser?->name ?? '-' }}</span>
                    </li>
                    <li class="d-flex align-items-center justify-content-between">
                        <span class="text-muted">{{ __('Status') }}:</span>
                        @if($cashAccount->is_active)
                            <span class="badge bg-label-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge bg-label-secondary">{{ __('Inactive') }}</span>
                        @endif
                    </li>
                    <li class="d-flex align-items-center justify-content-between">
                        <span class="text-muted">{{ __('Created At') }}:</span>
                        <span class="small">{{ $cashAccount->created_at?->format('Y-m-d H:i') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
