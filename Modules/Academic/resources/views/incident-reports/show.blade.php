@extends('layouts/contentNavbarLayout')

@section('title', __('Incident Report') . ' #' . $report->id . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('academic.incident-reports.index') }}" class="text-muted fw-medium small">{{ __('Incident Reports') }}</a></li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Incident Report Details') }} #{{ $report->id }}</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2 mt-1">
            <span class="badge bg-label-danger font-14 px-3 py-2">
                <i class="bx bx-first-aid me-1"></i>{{ $report->incident_type_name }}
            </span>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Incident Report') }} #{{ $report->id }}</h4>
        </div>
    </div>

    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
        <a href="{{ route('academic.incident-reports.print', $report->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 shadow-sm">
            <i class="bx bx-printer font-16"></i>
            <span>{{ __('Print Report') }}</span>
        </a>
        <a href="{{ route('academic.incident-reports.edit', $report->id) }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm">
            <i class="bx bx-edit font-16"></i>
            <span>{{ __('Edit') }}</span>
        </a>
        <a href="{{ route('academic.incident-reports.index') }}" class="btn btn-label-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bx bx-arrow-back font-16"></i>
            <span>{{ __('Back to List') }}</span>
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="bx bx-check-circle me-2 font-20"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    <!-- Main Report Body -->
    <div class="col-12 col-lg-8">
        <!-- 1. Key Incident Facts Bar -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar avatar-sm bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
                                <i class="bx bx-calendar text-primary font-20"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">{{ __('Incident Date') }}</small>
                                <span class="fw-bold text-heading">{{ $report->date ? $report->date->format('Y-m-d') : '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar avatar-sm bg-label-info rounded p-1 d-flex align-items-center justify-content-center">
                                <i class="bx bx-time text-info font-20"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">{{ __('Time') }}</small>
                                <span class="fw-bold text-heading">{{ $report->time ?: '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar avatar-sm bg-label-warning rounded p-1 d-flex align-items-center justify-content-center">
                                <i class="bx bx-map-pin text-warning font-20"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">{{ __('Location') }}</small>
                                <span class="fw-bold text-heading">{{ $report->location ?: '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar avatar-sm bg-label-danger rounded p-1 d-flex align-items-center justify-content-center">
                                <i class="bx bx-error-alt text-danger font-20"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">{{ __('Incident Type') }}</small>
                                <span class="fw-bold text-heading">{{ $report->incident_type_name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Student Description / Narrative -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                <i class="bx bx-chat text-primary font-20"></i>
                <h5 class="card-title mb-0 fw-bold text-heading">{{ __('Student Description') }}</h5>
            </div>
            <div class="card-body py-4">
                @if ($report->student_description)
                    <div class="p-3 rounded bg-light border-start border-3 border-primary">
                        <p class="mb-0 text-dark lh-base" style="white-space: pre-line;">{{ $report->student_description }}</p>
                    </div>
                @else
                    <p class="text-muted mb-0 italic">{{ __('No description recorded.') }}</p>
                @endif
            </div>
        </div>

        <!-- 3. Procedures Taken -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                <i class="bx bx-check-shield text-success font-20"></i>
                <h5 class="card-title mb-0 fw-bold text-heading">{{ __('Procedures Taken') }}</h5>
            </div>
            <div class="card-body py-4">
                @if ($report->procedures)
                    <div class="p-3 rounded bg-light border-start border-3 border-success">
                        <p class="mb-0 text-dark lh-base" style="white-space: pre-line;">{{ $report->procedures }}</p>
                    </div>
                @else
                    <p class="text-muted mb-0 italic">{{ __('No procedures recorded.') }}</p>
                @endif
            </div>
        </div>

        <!-- 4. Nurse & Medical Report -->
        <div class="card shadow-sm border-0 border-top border-4 border-info mb-4">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                <i class="bx bx-plus-medical text-info font-20"></i>
                <h5 class="card-title mb-0 fw-bold text-heading">{{ __('Nurse Report') }}</h5>
            </div>
            <div class="card-body py-4">
                @if ($report->nurse_report)
                    <div class="p-3 rounded bg-label-info border border-info">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bx bx-pulse text-info font-18"></i>
                            <span class="fw-bold text-info">{{ __('Medical & Nursing Notes:') }}</span>
                        </div>
                        <p class="mb-0 text-heading lh-base" style="white-space: pre-line;">{{ $report->nurse_report }}</p>
                    </div>
                @else
                    <p class="text-muted mb-0 italic">{{ __('No nurse report recorded.') }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar Column -->
    <div class="col-12 col-lg-4">
        <!-- Student Info Card -->
        @php
            $student = $report->student;
            $sName = $student ? ($student->translate(app()->getLocale())?->name ?? $student->translate('ar')?->name ?? $student->name) : '-';
        @endphp
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                <i class="bx bx-user text-primary font-20"></i>
                <h5 class="card-title mb-0 fw-bold text-heading">{{ __('Student Information') }}</h5>
            </div>
            <div class="card-body py-4 text-center">
                @if ($student && $student->photo_url)
                    <img src="{{ $student->photo_url }}" alt="{{ $sName }}" class="rounded-circle shadow-sm mb-3" width="80" height="80" style="object-fit: cover;">
                @else
                    <div class="avatar avatar-xl bg-label-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center fw-bold font-24">
                        {{ mb_substr($sName, 0, 1) }}
                    </div>
                @endif

                @if ($student)
                    <h5 class="fw-bold text-heading mb-1">
                        <a href="{{ route('academic.students.show', $student->id) }}" class="text-heading text-decoration-none">
                            {{ $sName }}
                        </a>
                    </h5>
                    <p class="text-muted small mb-3">{{ $student->student_code ? __('Student Code:') . ' ' . $student->student_code : '' }}</p>

                    <div class="text-start bg-light rounded p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">{{ __('Class:') }}</span>
                            <span class="fw-semibold text-heading small">{{ $student->class?->translate(app()->getLocale())?->name ?? $student->class?->name ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">{{ __('Stage:') }}</span>
                            <span class="fw-semibold text-heading small">{{ $student->stage?->translate(app()->getLocale())?->name ?? $student->stage?->name ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">{{ __('Branch:') }}</span>
                            <span class="fw-semibold text-heading small">{{ $student->branch?->translate(app()->getLocale())?->name ?? $student->branch?->name ?? '-' }}</span>
                        </div>
                        @if ($student->guardian)
                            <div class="d-flex justify-content-between mb-0 pt-2 border-top">
                                <span class="text-muted small">{{ __('Guardian:') }}</span>
                                <span class="fw-semibold text-heading small">
                                    {{ $student->guardian->translate(app()->getLocale())?->name ?? $student->guardian->name }}
                                </span>
                            </div>
                            @if ($student->guardian->phone)
                                <div class="d-flex justify-content-between mt-1">
                                    <span class="text-muted small">{{ __('Emergency Phone:') }}</span>
                                    <a href="tel:{{ $student->guardian->phone }}" class="text-primary fw-bold small dir-ltr">
                                        <i class="bx bx-phone me-1 font-12"></i>{{ $student->guardian->phone }}
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                @else
                    <p class="text-muted mb-0">-</p>
                @endif
            </div>
        </div>

        <!-- Responsible & Witnesses Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                <i class="bx bx-group text-secondary font-20"></i>
                <h5 class="card-title mb-0 fw-bold text-heading">{{ __('Responsible & Witnesses') }}</h5>
            </div>
            <div class="card-body py-4">
                <!-- Responsible Officer -->
                <div class="mb-4">
                    <small class="text-muted d-block mb-1">{{ __('Responsible Officer:') }}</small>
                    @if ($report->user)
                        <div class="d-flex align-items-center gap-2 p-2 rounded bg-light">
                            <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center fw-bold">
                                {{ mb_substr($report->user->name, 0, 1) }}
                            </div>
                            <div>
                                <span class="fw-bold text-heading d-block">{{ $report->user->name }}</span>
                                <small class="text-muted">{{ $report->user->email }}</small>
                            </div>
                        </div>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </div>

                <!-- Witnesses (Persons JSON) -->
                <div>
                    <small class="text-muted d-block mb-2">{{ __('Witnesses / Present Staff:') }}</small>
                    @php $witnesses = $report->witness_users; @endphp
                    @if ($witnesses->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($witnesses as $witness)
                                <span class="badge bg-label-secondary px-3 py-2 d-flex align-items-center gap-1 font-13">
                                    <i class="bx bx-user font-14"></i>
                                    {{ $witness->name }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted small">- {{ __('No witnesses recorded') }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Metadata Card -->
        <div class="card shadow-sm border-0">
            <div class="card-body py-3 text-muted small">
                <div class="d-flex justify-content-between mb-1">
                    <span>{{ __('Created At:') }}</span>
                    <span>{{ $report->created_at ? $report->created_at->format('Y-m-d H:i') : '-' }}</span>
                </div>
                @if ($report->creator)
                    <div class="d-flex justify-content-between mb-1">
                        <span>{{ __('Created By:') }}</span>
                        <span>{{ $report->creator->name }}</span>
                    </div>
                @endif
                @if ($report->updated_at && $report->updated_at != $report->created_at)
                    <div class="d-flex justify-content-between">
                        <span>{{ __('Last Updated:') }}</span>
                        <span>{{ $report->updated_at->format('Y-m-d H:i') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
