@extends('layouts/blankLayout')

@section('title', __('Submission Complete') . ' - ' . config('app.name'))

@section('content')
<div class="container-xxl py-5" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6 text-center">
            <div class="card shadow-sm border-0 rounded-4 p-4 p-md-5">
                <div class="card-body">
                    <div class="avatar avatar-xl rounded-circle bg-label-success mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 40px;">
                        <i class="bx bx-check-circle text-success"></i>
                    </div>

                    <h3 class="fw-bold text-heading mb-2">{{ __('Thank You!') }}</h3>
                    <h5 class="text-primary mb-3">{{ $employee->translate('ar')?->name ?? $employee->translate('en')?->name }}</h5>

                    <p class="text-muted mb-4 lead fs-6">
                        {{ __('Your employment information and documents have been submitted and successfully registered in the ERP system. Our HR department will review and finalize your onboarding.') }}
                    </p>

                    <div class="p-3 bg-light rounded-3 d-inline-block text-start mb-4">
                        <div class="d-flex align-items-center gap-2 text-muted small mb-1">
                            <i class="bx bx-id-card text-primary font-18"></i>
                            <span>{{ __('Employee Code:') }} <strong>{{ $employee->employee_code }}</strong></span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <i class="bx bx-envelope text-primary font-18"></i>
                            <span>{{ __('Email:') }} <strong>{{ $employee->email }}</strong></span>
                        </div>
                    </div>

                    <div class="mt-2">
                        <span class="badge bg-label-secondary p-2 px-3">
                            <i class="bx bx-check me-1"></i> {{ __('Data Submission Completed') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

