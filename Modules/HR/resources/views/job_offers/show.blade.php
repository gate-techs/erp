@extends('layouts/contentNavbarLayout')

@section('title', __('Job Offer') . ' ' . $offer->offer_code . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printableOfferLetter, #printableOfferLetter * {
        visibility: visible;
    }
    #printableOfferLetter {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding: 20px !important;
        margin: 0 !important;
        box-shadow: none !important;
        border: none !important;
        background: #fff !important;
    }
    .no-print {
        display: none !important;
    }
}
.offer-letter-container {
    background: #fff;
    border: 1px solid #e0e4eb;
    border-radius: 8px;
    padding: 40px;
}
.letterhead-border {
    border-bottom: 3px solid #696cff;
    padding-bottom: 20px;
    margin-bottom: 30px;
}
</style>
@endsection

@section('content')
<!-- Action Bar & Breadcrumbs -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 no-print">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="fw-bold mb-0">{{ $offer->offer_code }}</h4>
            <span class="badge {{ $offer->status->badgeClass() }} rounded-pill px-3 py-1 font-12">
                <i class="{{ $offer->status->icon() }} me-1"></i>{{ $offer->status->label() }}
            </span>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.job-offers.index') }}" class="text-muted">{{ __('Job Offers') }}</a></li>
                <li class="breadcrumb-item active text-primary">{{ $offer->offer_code }}</li>
            </ol>
        </nav>
    </div>

    <!-- Workflow Action Buttons -->
    <div class="d-flex flex-wrap align-items-center gap-2">
        <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-1 shadow-sm px-3 py-2 rounded-3" onclick="window.print()">
            <i class="bx bx-printer font-18"></i>
            <span class="fw-semibold">{{ __('Print Offer Letter') }}</span>
        </button>

        @if($offer->status === \Modules\HR\Enums\JobOfferStatus::DRAFT)
            @can('hr.job-offers.edit')
            <a href="{{ route('hr.job-offers.edit', $offer->id) }}" class="btn btn-outline-secondary d-flex align-items-center gap-1 px-3 py-2 rounded-3">
                <i class="bx bx-edit font-18"></i>
                <span>{{ __('Edit') }}</span>
            </a>
            @endcan

            @can('hr.job-offers.submit')
            <button type="button" class="btn btn-warning d-flex align-items-center gap-1 px-3 py-2 rounded-3 btn-offer-action" data-url="{{ route('hr.job-offers.submit', $offer->id) }}" data-action="{{ __('Submit for Approval') }}">
                <i class="bx bx-send font-18"></i>
                <span class="fw-semibold">{{ __('Submit for Approval') }}</span>
            </button>
            @endcan
        @endif

        @if($offer->status === \Modules\HR\Enums\JobOfferStatus::PENDING_APPROVAL)
            @can('hr.job-offers.approve')
            <button type="button" class="btn btn-success d-flex align-items-center gap-1 px-3 py-2 rounded-3 btn-offer-action" data-url="{{ route('hr.job-offers.approve', $offer->id) }}" data-action="{{ __('Approve Offer') }}">
                <i class="bx bx-check-shield font-18"></i>
                <span class="fw-semibold">{{ __('Approve Offer') }}</span>
            </button>
            @endcan
        @endif

        @if($offer->status === \Modules\HR\Enums\JobOfferStatus::APPROVED)
            @can('hr.job-offers.send')
            <button type="button" class="btn btn-primary d-flex align-items-center gap-1 px-3 py-2 rounded-3 btn-offer-action" data-url="{{ route('hr.job-offers.send', $offer->id) }}" data-action="{{ __('Mark as Sent') }}">
                <i class="bx bx-paper-plane font-18"></i>
                <span class="fw-semibold">{{ __('Send to Candidate') }}</span>
            </button>
            @endcan
        @endif

        @if($offer->status === \Modules\HR\Enums\JobOfferStatus::SENT)
            @can('hr.job-offers.accept')
            <button type="button" class="btn btn-success d-flex align-items-center gap-1 px-3 py-2 rounded-3 btn-offer-action" data-url="{{ route('hr.job-offers.accept', $offer->id) }}" data-action="{{ __('Record Candidate Acceptance') }}">
                <i class="bx bx-check-double font-18"></i>
                <span class="fw-semibold">{{ __('Candidate Accepted') }}</span>
            </button>
            @endcan

            @can('hr.job-offers.decline')
            <button type="button" class="btn btn-outline-danger d-flex align-items-center gap-1 px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#declineModal">
                <i class="bx bx-x-circle font-18"></i>
                <span>{{ __('Declined') }}</span>
            </button>
            @endcan
        @endif

        @if($offer->status === \Modules\HR\Enums\JobOfferStatus::ACCEPTED)
            @if($offer->onboarding)
                @can('hr.onboardings.view')
                <a href="{{ route('hr.onboardings.show', $offer->onboarding->id) }}" class="btn btn-success d-flex align-items-center gap-1 px-3 py-2 rounded-3 shadow-sm">
                    <i class="bx bx-compass font-18"></i>
                    <span class="fw-semibold">{{ __('View Onboarding Journey') }}</span>
                </a>
                @endcan
            @else
                @can('hr.job-offers.accept')
                <form action="{{ route('hr.job-offers.accept', $offer->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success d-flex align-items-center gap-1 px-3 py-2 rounded-3 shadow-sm">
                        <i class="bx bx-compass font-18"></i>
                        <span class="fw-semibold">{{ __('Start Onboarding') }}</span>
                    </button>
                </form>
                @endcan
            @endif
        @endif
    </div>
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-pills mb-4 no-print" role="tablist">
    <li class="nav-item">
        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-offer-letter">
            <i class="bx bx-file me-1"></i> {{ __('Official Offer Letter') }}
        </button>
    </li>
    <li class="nav-item">
        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-overview">
            <i class="bx bx-detail me-1"></i> {{ __('Offer Dossier & Timeline') }}
        </button>
    </li>
</ul>

<div class="tab-content p-0 border-0 shadow-none">
    <!-- TAB 1: Printable Official Offer Letter -->
    <div class="tab-pane fade show active" id="tab-offer-letter" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4 p-md-5" id="printableOfferLetter">
                <!-- Letterhead -->
                <div class="d-flex align-items-center justify-content-between letterhead-border">
                    <div>
                        <h3 class="fw-bolder text-primary mb-1">{{ config('app.name', 'GATE TECH ERP') }}</h3>
                        <p class="text-muted mb-0 font-13">{{ __('Human Resources & Talent Acquisition Department') }}</p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-label-secondary font-12 fw-semibold px-3 py-1 mb-1">{{ __('Ref') }}: {{ $offer->offer_code }}</span>
                        <div class="text-muted font-12">{{ __('Date') }}: {{ $offer->offer_date->format('Y-m-d') }}</div>
                    </div>
                </div>

                <!-- Letter Body -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-2">{{ __('Dear') }} / {{ $offer->candidate?->full_name }}،</h5>
                    <p class="text-muted font-14 leading-relaxed mb-4">
                        {{ __('Following your successful interviews and candidate evaluation, we are delighted to extend this formal offer of employment to join our team at Gate Tech. We were genuinely impressed by your skills, background, and cultural alignment with our values.') }}
                    </p>
                </div>

                <!-- Position Summary Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle font-14">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 35%;">{{ __('Designation / Position') }}</th>
                                <td class="fw-bold text-heading">{{ $offer->jobTitle?->translate(app()->getLocale())?->title ?? $offer->jobTitle?->title ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Department / Division') }}</th>
                                <td>{{ $offer->department?->translate(app()->getLocale())?->name ?? $offer->department?->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Employment Type') }}</th>
                                <td>{{ \Modules\HR\Enums\EmploymentType::tryFrom($offer->employment_type)?->label() ?? $offer->employment_type }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Expected Joining Date') }}</th>
                                <td class="fw-semibold text-primary">{{ $offer->joining_date ? $offer->joining_date->format('Y-m-d') : __('To be agreed') }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Probationary Period') }}</th>
                                <td>{{ $offer->probation_months }} {{ __('Months from joining date') }}</td>
                            </tr>
                        </thead>
                    </table>
                </div>

                <!-- Financial Package Table -->
                <div class="mb-4">
                    <h6 class="fw-bold text-heading mb-3 d-flex align-items-center gap-2">
                        <i class="bx bx-money text-success font-18"></i>
                        <span>{{ __('Monthly Financial Package Breakdown') }} ({{ $offer->currency }})</span>
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle font-14 mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th>{{ __('Basic Salary') }}</th>
                                    <th>{{ __('Housing Allowance') }}</th>
                                    <th>{{ __('Transportation Allowance') }}</th>
                                    <th>{{ __('Other Allowances') }}</th>
                                    <th class="table-success text-success fw-bold">{{ __('Total Monthly Gross') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="fw-semibold">
                                    <td>{{ number_format($offer->basic_salary, 2) }}</td>
                                    <td>{{ number_format($offer->housing_allowance, 2) }}</td>
                                    <td>{{ number_format($offer->transportation_allowance, 2) }}</td>
                                    <td>{{ number_format($offer->other_allowances, 2) }}</td>
                                    <td class="table-success text-success fw-bolder font-16">{{ number_format($offer->total_salary, 2) }} {{ $offer->currency }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Terms & Conditions -->
                @if($offer->terms_and_conditions)
                    <div class="mb-4">
                        <h6 class="fw-bold text-heading mb-2">{{ __('Special Terms & Conditions') }}:</h6>
                        <div class="p-3 bg-light rounded-3 font-13 text-secondary leading-relaxed">
                            {!! nl2br(e($offer->terms_and_conditions)) !!}
                        </div>
                    </div>
                @endif

                <div class="alert alert-warning border-0 p-3 rounded-3 font-13 mb-5">
                    <i class="bx bx-time-five me-1"></i>
                    <strong>{{ __('Offer Validity') }}:</strong>
                    {{ __('This offer is strictly valid until') }} <strong>{{ $offer->expiry_date ? $offer->expiry_date->format('Y-m-d') : __('7 days from issue date') }}</strong>.
                    {{ __('Please sign and return a copy to confirm your acceptance.') }}
                </div>

                <!-- Signatures Row -->
                <div class="row pt-4 mt-5 border-top">
                    <div class="col-6 text-center">
                        <p class="fw-bold text-heading mb-4">{{ __('For & On Behalf of the Company') }}</p>
                        <div style="height: 60px;">
                            @if($offer->approved_by)
                                <span class="badge bg-label-success rounded-pill font-11"><i class="bx bx-check-shield me-1"></i>{{ __('Digitally Approved') }}</span>
                            @endif
                        </div>
                        <div class="border-top w-75 mx-auto pt-2">
                            <span class="text-muted font-12 d-block">{{ __('HR Director / Authorized Signatory') }}</span>
                        </div>
                    </div>

                    <div class="col-6 text-center">
                        <p class="fw-bold text-heading mb-4">{{ __('Candidate Acceptance & Signature') }}</p>
                        <div style="height: 60px;">
                            @if($offer->status === \Modules\HR\Enums\JobOfferStatus::ACCEPTED)
                                <span class="badge bg-label-success rounded-pill font-11"><i class="bx bx-check-double me-1"></i>{{ __('Accepted by Candidate') }}</span>
                            @endif
                        </div>
                        <div class="border-top w-75 mx-auto pt-2">
                            <span class="text-muted font-12 d-block">{{ $offer->candidate?->full_name }} — {{ __('Date') }}: ____________</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: Management Dossier & Timeline -->
    <div class="tab-pane fade" id="tab-overview" role="tabpanel">
        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold">{{ __('Candidate Profile & Application Details') }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <span class="text-muted small d-block">{{ __('Full Name') }}</span>
                                <span class="fw-semibold text-heading font-14">{{ $offer->candidate?->full_name }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">{{ __('Email Address') }}</span>
                                <span class="fw-semibold text-heading font-14">{{ $offer->candidate?->email }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">{{ __('Phone Number') }}</span>
                                <span class="fw-semibold text-heading font-14">{{ $offer->candidate?->phone }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">{{ __('Nationality / City') }}</span>
                                <span class="fw-semibold text-heading font-14">{{ $offer->candidate?->nationality ?? '-' }} / {{ $offer->candidate?->city ?? '-' }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">{{ __('Job Application Code') }}</span>
                                <span class="fw-semibold text-primary font-14">{{ $offer->jobApplication?->application_code ?? '-' }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">{{ __('Job Opening') }}</span>
                                <span class="fw-semibold text-heading font-14">{{ $offer->jobOpening?->translate(app()->getLocale())?->title ?? $offer->jobOpening?->title ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($offer->notes)
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold">{{ __('Internal Notes') }}</h5>
                        </div>
                        <div class="card-body p-4">
                            <p class="mb-0 text-muted">{{ $offer->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold">{{ __('Offer Status & Milestones') }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <ul class="timeline mb-0">
                            <li class="timeline-item timeline-item-transparent pb-3">
                                <span class="timeline-point timeline-point-primary"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-1">
                                        <h6 class="mb-0 font-13 fw-semibold">{{ __('Created by HR') }}</h6>
                                        <small class="text-muted">{{ $offer->created_at->format('M d, H:i') }}</small>
                                    </div>
                                    <p class="mb-0 font-11 text-muted">{{ $offer->creator?->name ?? __('System') }}</p>
                                </div>
                            </li>

                            <li class="timeline-item timeline-item-transparent pb-3">
                                <span class="timeline-point {{ $offer->approved_at ? 'timeline-point-success' : 'timeline-point-secondary' }}"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-1">
                                        <h6 class="mb-0 font-13 fw-semibold">{{ __('Management Approval') }}</h6>
                                        <small class="text-muted">{{ $offer->approved_at ? $offer->approved_at->format('M d, H:i') : '-' }}</small>
                                    </div>
                                    <p class="mb-0 font-11 text-muted">{{ $offer->approver?->name ?? __('Pending') }}</p>
                                </div>
                            </li>

                            <li class="timeline-item timeline-item-transparent pb-3">
                                <span class="timeline-point {{ $offer->sent_at ? 'timeline-point-info' : 'timeline-point-secondary' }}"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-1">
                                        <h6 class="mb-0 font-13 fw-semibold">{{ __('Sent to Candidate') }}</h6>
                                        <small class="text-muted">{{ $offer->sent_at ? $offer->sent_at->format('M d, H:i') : '-' }}</small>
                                    </div>
                                </div>
                            </li>

                            <li class="timeline-item timeline-item-transparent">
                                <span class="timeline-point {{ $offer->status === \Modules\HR\Enums\JobOfferStatus::ACCEPTED ? 'timeline-point-success' : ($offer->status === \Modules\HR\Enums\JobOfferStatus::DECLINED ? 'timeline-point-danger' : 'timeline-point-secondary') }}"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-1">
                                        <h6 class="mb-0 font-13 fw-semibold">{{ __('Candidate Response') }}</h6>
                                        <small class="text-muted">{{ $offer->responded_at ? $offer->responded_at->format('M d, H:i') : '-' }}</small>
                                    </div>
                                    <p class="mb-0 font-11 text-muted">{{ $offer->status->label() }}</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Decline Modal -->
<div class="modal fade" id="declineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('hr.job-offers.decline', $offer->id) }}" method="POST" id="declineOfferForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">{{ __('Record Offer Decline') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="rejection_reason">{{ __('Reason for Candidate Decline') }}</label>
                        <textarea name="rejection_reason" id="rejection_reason" rows="3" class="form-control" placeholder="{{ __('e.g. Counter-offer from current employer, salary expectations...') }}" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Save Decline') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
$(function () {
    $('.btn-offer-action').on('click', function () {
        const url = $(this).data('url');
        const actionText = $(this).data('action');

        Swal.fire({
            title: actionText,
            text: "{{ __('Do you wish to proceed with this action?') }}",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: "{{ __('Confirm') }}",
            cancelButtonText: "{{ __('Cancel') }}",
            customClass: {
                confirmButton: 'btn btn-primary me-2',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then((res) => {
            if (res.isConfirmed) {
                $.post(url, { _token: '{{ csrf_token() }}' }, function (response) {
                    if (response.success) {
                        Swal.fire({
                            title: "{{ __('Success') }}",
                            text: response.message,
                            icon: 'success',
                            customClass: { confirmButton: 'btn btn-primary' }
                        }).then(() => location.reload());
                    }
                }).fail(function (xhr) {
                    Swal.fire({
                        title: "{{ __('Error') }}",
                        text: xhr.responseJSON ? xhr.responseJSON.message : "{{ __('Something went wrong!') }}",
                        icon: 'error'
                    });
                });
            }
        });
    });

    $('#declineOfferForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        $.post(form.attr('action'), form.serialize(), function (response) {
            if (response.success) {
                location.reload();
            }
        }).fail(function (xhr) {
            Swal.fire({
                title: "{{ __('Error') }}",
                text: xhr.responseJSON ? xhr.responseJSON.message : "{{ __('Something went wrong!') }}",
                icon: 'error'
            });
        });
    });
});
</script>
@endsection
