@extends('layouts/contentNavbarLayout')

@section('title', __('Lock Dates') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-lock-alt text-primary font-24"></i>
            <span>{{ __('Lock Dates') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('Accounting') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Lock Dates') }}</li>
            </ol>
        </nav>
    </div>

    <!-- Entity Select2 Selector -->
    @if($entities->count() > 1)
        <div style="min-width: 260px;">
            <form method="GET" action="{{ route('accounting.lock-dates.index') }}" id="entityFilterForm" class="m-0">
                <select name="entity_id" id="header_entity_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Accounting Entity') }}" data-allow-clear="false">
                    @foreach($entities as $entity)
                        <option value="{{ $entity->id }}" @selected($selectedEntity?->id === $entity->id)>
                            {{ $entity->translate(app()->getLocale())?->name ?? $entity->translate('ar')?->name ?? $entity->code }} ({{ $entity->code }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    @endif
</div>

<!-- Alert Notifications -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible d-flex align-items-center mb-4" role="alert">
        <i class="icon-base bx bx-check-circle me-2 font-20"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible d-flex align-items-center mb-4" role="alert">
        <i class="icon-base bx bx-error-circle me-2 font-20"></i>
        <div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-12">

        <!-- 1. Lock Dates Form Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                    <i class="icon-base bx bx-calendar-event text-primary font-20"></i>
                    <span>{{ __('Lock Dates') }}</span>
                </h5>
            </div>

            @if($selectedEntity)
                @php
                    $fmt = fn($d) => $d ? $d->format('Y-m-d') : '';
                @endphp
                <form method="POST" action="{{ route('accounting.lock-dates.update', $selectedEntity->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Journal Entries Lock Date -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-heading mb-1">
                                    {{ __('Journal Entries Lock Date') }}
                                    <span class="text-muted fw-normal small">({{ __('For Non-Advisers') }})</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="icon-base bx bx-calendar"></i></span>
                                    <input type="text"
                                           name="lock_all_date"
                                           class="form-control js-flatpickr-date"
                                           placeholder="YYYY-MM-DD"
                                           value="{{ $fmt($lockDates?->lock_all_date) }}"
                                           autocomplete="off" />
                                </div>
                                <small class="text-muted d-block mt-1">{{ __('Entries on or before this date cannot be modified by regular users.') }}</small>
                            </div>

                            <!-- Tax Return Lock Date -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-heading mb-1">
                                    {{ __('Tax Return Lock Date') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="icon-base bx bx-calendar"></i></span>
                                    <input type="text"
                                           name="tax_lock_date"
                                           class="form-control js-flatpickr-date"
                                           placeholder="YYYY-MM-DD"
                                           value="{{ $fmt($lockDates?->tax_lock_date) }}"
                                           autocomplete="off" />
                                </div>
                                <small class="text-muted d-block mt-1">{{ __('Tax-affecting entries on or before this date are strictly locked.') }}</small>
                            </div>

                            <!-- Fiscal Year Lock Date -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-heading mb-1">
                                    {{ __('Fiscal Year Lock Date') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="icon-base bx bx-calendar"></i></span>
                                    <input type="text"
                                           name="fiscal_year_lock_date"
                                           class="form-control js-flatpickr-date"
                                           placeholder="YYYY-MM-DD"
                                           value="{{ $fmt($lockDates?->fiscal_year_lock_date) }}"
                                           autocomplete="off" />
                                </div>
                                <small class="text-muted d-block mt-1">{{ __('Year-end financial records are protected after final audit.') }}</small>
                            </div>

                            <!-- All Users Lock Date -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-danger mb-1">
                                    {{ __('All Users Lock Date') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text text-danger"><i class="icon-base bx bx-calendar"></i></span>
                                    <input type="text"
                                           name="hard_lock_date"
                                           class="form-control js-flatpickr-date text-danger"
                                           placeholder="YYYY-MM-DD"
                                           value="{{ $fmt($lockDates?->hard_lock_date) }}"
                                           autocomplete="off" />
                                </div>
                                <small class="text-muted d-block mt-1">{{ __('Permanent lock for all users including administrators. Cannot be bypassed.') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer border-top py-3 px-4 d-flex align-items-center justify-content-between bg-transparent">
                        <span class="text-muted small">
                            @if($lockDates?->updatedBy)
                                {{ __('Last updated by') }}: <strong>{{ $lockDates->updatedBy->name }}</strong> · {{ $lockDates->updated_at?->format('Y-m-d') }}
                            @endif
                        </span>
                        <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-1 shadow-sm">
                            <i class="icon-base bx bx-save font-18"></i>
                            <span>{{ __('Save Changes') }}</span>
                        </button>
                    </div>
                </form>
            @else
                <div class="card-body text-center py-5 text-muted">
                    <i class="icon-base bx bx-building-house font-40 text-muted mb-2 d-block"></i>
                    <p class="mb-0">{{ __('No entity selected.') }}</p>
                </div>
            @endif
        </div>

        <!-- 2. Lock Exceptions Card -->
        @if($selectedEntity)
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="icon-base bx bx-user-check text-primary font-20"></i>
                        <span>{{ __('Lock Exceptions') }}</span>
                    </h5>
                    <!-- Button to open Standard Offcanvas -->
                    <button type="button" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddException" aria-controls="offcanvasAddException">
                        <i class="icon-base bx bx-plus font-16"></i>
                        <span>{{ __('Add Exception') }}</span>
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Lock Type') }}</th>
                                <th>{{ __('Valid From') }}</th>
                                <th>{{ __('Valid Until') }}</th>
                                <th>{{ __('Reason') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exceptions as $exception)
                                @php
                                    $active = !$exception->revoked_at && $exception->valid_from->isPast() && $exception->valid_until->isFuture();
                                @endphp
                                <tr>
                                    <td class="fw-semibold text-heading">{{ $exception->user?->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-label-primary">{{ $lockTypes[$exception->lock_type] ?? $exception->lock_type }}</span>
                                    </td>
                                    <td>{{ $exception->valid_from->format('Y-m-d H:i') }}</td>
                                    <td>{{ $exception->valid_until->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 250px;" title="{{ $exception->reason }}">
                                            {{ $exception->reason }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($exception->revoked_at)
                                            <span class="badge bg-label-danger">{{ __('Revoked') }}</span>
                                        @elseif($active)
                                            <span class="badge bg-label-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge bg-label-secondary">{{ __('Expired') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(!$exception->revoked_at && $exception->valid_until->isFuture())
                                            <form method="POST" action="{{ route('accounting.lock-dates.exceptions.revoke', $exception->id) }}" class="d-inline" onsubmit="return confirm('{{ __('Revoke this exception?') }}')">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-outline-danger">{{ __('Revoke') }}</button>
                                            </form>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted small">
                                        {{ __('No lock exceptions found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Standard Offcanvas: Add Lock Exception -->
            <div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddException" aria-labelledby="offcanvasAddExceptionLabel" style="width: 500px; max-width: 95vw;">
                <div class="offcanvas-header border-bottom py-3 px-4">
                    <h5 id="offcanvasAddExceptionLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
                        <i class="icon-base bx bx-user-plus text-primary font-22"></i>
                        <span>{{ __('Add Lock Exception') }}</span>
                    </h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>

                <div class="offcanvas-body p-4">
                    <form action="{{ route('accounting.lock-dates.exceptions.store') }}" method="POST" id="addExceptionForm">
                        @csrf
                        <input type="hidden" name="entity_id" value="{{ $selectedEntity->id }}">

                        <!-- User Selection (Bootstrap-5 Select2) -->
                        <div class="mb-3">
                            <label class="form-label required fw-semibold text-heading">{{ __('User') }} <span class="text-danger">*</span></label>
                            <select name="user_id" id="exception_user_id" class="form-select select2" data-control="select2" data-placeholder="{{ __('Select User') }}" required>
                                <option value=""></option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Lock Type Selection (Bootstrap-5 Select2) -->
                        <div class="mb-3">
                            <label class="form-label required fw-semibold text-heading">{{ __('Lock Type') }} <span class="text-danger">*</span></label>
                            <select name="lock_type" id="exception_lock_type" class="form-select select2" data-control="select2" data-placeholder="{{ __('Lock Type') }}" required>
                                <option value=""></option>
                                @foreach($lockTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range (Valid From & Valid Until) -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label required fw-semibold text-heading">{{ __('Valid From') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="icon-base bx bx-calendar"></i></span>
                                    <input type="text"
                                           name="valid_from"
                                           class="form-control js-flatpickr-datetime"
                                           value="{{ now()->format('Y-m-d H:i') }}"
                                           required />
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label required fw-semibold text-heading">{{ __('Valid Until') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="icon-base bx bx-calendar"></i></span>
                                    <input type="text"
                                           name="valid_until"
                                           class="form-control js-flatpickr-datetime"
                                           value="{{ now()->addDays(7)->format('Y-m-d H:i') }}"
                                           required />
                                </div>
                            </div>
                        </div>

                        <!-- Reason -->
                        <div class="mb-4">
                            <label class="form-label required fw-semibold text-heading">{{ __('Reason') }} <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="3" required maxlength="2000" placeholder="{{ __('Reason for granting temporary access...') }}"></textarea>
                        </div>

                        <!-- Footer Buttons -->
                        <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-4">
                                <i class="icon-base bx bx-save font-18"></i>
                                <span>{{ __('Save') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;

    // 1. Initialize Flatpickr inputs
    function initFlatpickrs() {
        if (typeof flatpickr !== 'undefined') {
            const isRtl = document.documentElement.dir === 'rtl' || (document.documentElement.lang || '').startsWith('ar');
            const locale = isRtl && flatpickr.l10ns && flatpickr.l10ns.ar ? flatpickr.l10ns.ar : 'default';

            document.querySelectorAll('.js-flatpickr-date:not(.flatpickr-input)').forEach(function (el) {
                flatpickr(el, {
                    dateFormat: 'Y-m-d',
                    allowInput: true,
                    disableMobile: true,
                    locale: locale
                });
            });

            document.querySelectorAll('.js-flatpickr-datetime:not(.flatpickr-input)').forEach(function (el) {
                flatpickr(el, {
                    dateFormat: 'Y-m-d H:i',
                    enableTime: true,
                    time_24hr: true,
                    allowInput: true,
                    disableMobile: true,
                    locale: locale
                });
            });
        }
    }

    // 2. Universal Sneat / Bootstrap-5 Select2 Initializer
    function setupSelect2(container = document) {
        if (!$) return;

        const isRtl = $('html').attr('dir') === 'rtl' || $('html').attr('lang') === 'ar';
        const noResultsText = isRtl ? 'لا توجد نتائج مطابقة' : 'No results found';
        const searchingText = isRtl ? 'جاري البحث...' : 'Searching...';

        if (typeof window.initSelect2 === 'function') {
            window.initSelect2(container);
        } else if ($.fn.select2) {
            $(container).find('.select2, [data-control="select2"]').each(function () {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2({
                        theme: 'bootstrap-5',
                        placeholder: $(this).data('placeholder') || '',
                        allowClear: $(this).data('allow-clear') !== false,
                        dir: isRtl ? 'rtl' : 'ltr',
                        width: '100%',
                        dropdownParent: $(this).closest('.modal, .offcanvas').length ? $(this).closest('.modal, .offcanvas') : null,
                        language: {
                            noResults: () => noResultsText,
                            searching: () => searchingText
                        }
                    });
                }
            });
        }
    }

    initFlatpickrs();
    setupSelect2();

    // Auto-submit entity filter when Select2 changes
    $(document).on('change', '#header_entity_id', function () {
        $('#entityFilterForm').submit();
    });

    // Re-initialize on offcanvas open
    const offcanvasEl = document.getElementById('offcanvasAddException');
    if (offcanvasEl) {
        offcanvasEl.addEventListener('shown.bs.offcanvas', function () {
            setupSelect2(offcanvasEl);
            initFlatpickrs();
        });
    }
});
</script>
@endsection
