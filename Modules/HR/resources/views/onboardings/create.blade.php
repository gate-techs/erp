@extends('layouts/contentNavbarLayout')

@section('title', __('Initiate Onboarding') . ' - ' . config('variables.templateName'))

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="bx bx-compass text-primary font-24"></i>
            <span>{{ __('Initiate Onboarding Journey') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.onboardings.index') }}" class="text-muted">{{ __('Onboarding') }}</a></li>
                <li class="breadcrumb-item active text-primary">{{ __('New Journey') }}</li>
            </ol>
        </nav>
    </div>

    <div>
        <a href="{{ route('hr.onboardings.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-sm px-3 py-2 rounded-3">
            <i class="bx bx-arrow-back font-18"></i>
            <span class="fw-semibold">{{ __('Back to List') }}</span>
        </a>
    </div>
</div>

@if (isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm rounded-3" role="alert">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('hr.onboardings.store') }}" method="POST">
    @csrf

    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="bx bx-user-plus text-primary font-20"></i>
                        <span>{{ __('Candidate & Assignment Details') }}</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold" for="candidate_id">{{ __('Select Candidate') }} <span class="text-danger">*</span></label>
                            <select name="candidate_id" id="candidate_id" class="form-select @error('candidate_id') is-invalid @enderror" required>
                                <option value="">{{ __('Select candidate...') }}</option>
                                @foreach($candidates as $cand)
                                    <option value="{{ $cand->id }}" {{ (old('candidate_id', $candidateId) == $cand->id) ? 'selected' : '' }}>
                                        {{ $cand->full_name }} ({{ $cand->email }} | {{ $cand->phone }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="department_id">{{ __('Department') }}</label>
                            <select name="department_id" id="department_id" class="form-select">
                                <option value="">{{ __('Select department...') }}</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->translate(app()->getLocale())?->name ?? $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="job_title_id">{{ __('Job Title') }}</label>
                            <select name="job_title_id" id="job_title_id" class="form-select">
                                <option value="">{{ __('Select job title...') }}</option>
                                @foreach($jobTitles as $title)
                                    <option value="{{ $title->id }}" {{ old('job_title_id') == $title->id ? 'selected' : '' }}>
                                        {{ $title->translate(app()->getLocale())?->title ?? $title->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="buddy_employee_id">{{ __('Assigned Buddy / Mentor') }}</label>
                            <select name="buddy_employee_id" id="buddy_employee_id" class="form-select">
                                <option value="">{{ __('Select senior peer...') }}</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('buddy_employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->translate(app()->getLocale())?->name ?? $emp->name }} ({{ $emp->employee_code }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('Colleague responsible for guiding the newcomer in their first weeks.') }}</small>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="start_date">{{ __('First Day of Work (Start Date)') }} <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', now()->addDays(7)->toDateString()) }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold" for="notes">{{ __('Onboarding Notes & Instructions') }}</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="{{ __('Any special equipment required, specific badge requests, etc.') }}">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-label-primary border-primary border-opacity-25">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-primary mb-2 d-flex align-items-center gap-1">
                        <i class="bx bx-info-circle font-18"></i>
                        <span>{{ __('Standard Checklist Items') }}</span>
                    </h6>
                    <p class="font-13 text-secondary mb-3">
                        {{ __('Submitting this form will automatically generate the default 7-point onboarding checklist including:') }}
                    </p>
                    <ul class="font-12 text-secondary ps-3 mb-0">
                        <li class="mb-1">{{ __('Official documentation & credentials collection') }}</li>
                        <li class="mb-1">{{ __('Signing employment contract') }}</li>
                        <li class="mb-1">{{ __('Creating corporate email & system account') }}</li>
                        <li class="mb-1">{{ __('Workstation & laptop issuance') }}</li>
                        <li class="mb-1">{{ __('Orientation & team introduction tour') }}</li>
                        <li>{{ __('Reviewing code of conduct & HR policies') }}</li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <button type="submit" class="btn btn-primary w-100 py-2.5 font-15 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="bx bx-check font-18"></i>
                        <span>{{ __('Start Onboarding Journey') }}</span>
                    </button>
                    <a href="{{ route('hr.onboardings.index') }}" class="btn btn-label-secondary w-100 mt-2 py-2">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
