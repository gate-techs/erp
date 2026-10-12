@extends('layouts/contentNavbarLayout')

@section('title', ($event->translate(app()->getLocale())?->name ?? $event->translate('ar')?->name) . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('academic.events.index') }}" class="text-muted fw-medium small">{{ __('Events') }}</a></li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Event Details') }}</li>
            </ol>
        </nav>
        <h4 class="mb-0 fw-bold text-heading d-flex align-items-center gap-2">
            <i class="icon-base bx bx-party text-primary font-22"></i>
            {{ $event->translate(app()->getLocale())?->name ?? $event->translate('ar')?->name }}
        </h4>
    </div>
    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
        <a href="{{ route('academic.events.edit', $event->id) }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm">
            <i class="icon-base bx bx-edit-alt font-18"></i>
            <span>{{ __('Edit Event') }}</span>
        </a>
        <a href="{{ route('academic.events.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="icon-base bx bx-arrow-back font-18"></i>
            <span>{{ __('Back to Events') }}</span>
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Main Column: Details -->
    <div class="col-12 col-lg-8">
        <!-- Event Banner / Hero Card -->
        <div class="card shadow-sm border-0 mb-4 overflow-hidden">
            @if ($event->image_url)
                <div class="w-100 bg-light position-relative" style="max-height: 320px; overflow: hidden;">
                    <img src="{{ $event->image_url }}" alt="{{ $event->translate('ar')?->name }}" class="w-100 object-fit-cover" style="max-height: 320px;">
                </div>
            @endif
            <div class="card-body p-4">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="badge bg-label-info font-13 py-1 px-3">
                        <i class="bx bx-tag-alt me-1"></i>
                        {{ $event->eventType?->translate(app()->getLocale())?->name ?? $event->eventType?->translate('ar')?->name ?? __('Event') }}
                    </span>

                    @if ($event->status === 'active')
                        <span class="badge bg-label-success font-13 py-1 px-3">{{ __('Active') }}</span>
                    @elseif ($event->status === 'upcoming')
                        <span class="badge bg-label-warning font-13 py-1 px-3">{{ __('Upcoming') }}</span>
                    @elseif ($event->status === 'completed')
                        <span class="badge bg-label-secondary font-13 py-1 px-3">{{ __('Completed') }}</span>
                    @else
                        <span class="badge bg-label-danger font-13 py-1 px-3">{{ __('Inactive') }}</span>
                    @endif

                    @if ((float) $event->price > 0)
                        <span class="badge bg-label-primary font-13 py-1 px-3">
                            <i class="bx bx-wallet me-1"></i>
                            {{ number_format((float) $event->price, 3) }} {{ __('KWD') }}
                        </span>
                    @else
                        <span class="badge bg-label-success font-13 py-1 px-3">{{ __('Free Event') }}</span>
                    @endif
                </div>

                <h3 class="fw-bold text-heading mb-1">{{ $event->translate('ar')?->name }}</h3>
                @if ($event->translate('en')?->name)
                    <h5 class="text-muted fw-normal mb-3" dir="ltr">{{ $event->translate('en')?->name }}</h5>
                @endif

                <hr class="my-4">

                <!-- Descriptions -->
                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <h6 class="fw-bold text-heading mb-2 d-flex align-items-center gap-2">
                            <i class="bx bx-file-blank text-primary font-18"></i>
                            <span>{{ __('Event Details (Arabic)') }}</span>
                        </h6>
                        <div class="p-3 bg-light rounded text-body lh-base">
                            {{ $event->translate('ar')?->description ?? __('No Arabic description provided.') }}
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <h6 class="fw-bold text-heading mb-2 d-flex align-items-center gap-2">
                            <i class="bx bx-file-blank text-primary font-18"></i>
                            <span>{{ __('Event Details (English)') }}</span>
                        </h6>
                        <div class="p-3 bg-light rounded text-body lh-base" dir="ltr">
                            {{ $event->translate('en')?->description ?? __('No English description provided.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Column: Summary & Meta -->
    <div class="col-12 col-lg-4">
        <!-- Target Info Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-heading fw-bold">
                    <i class="icon-base bx bx-map-pin text-primary font-20"></i>
                    {{ __('Location & Target Group') }}
                </h5>
            </div>
            <div class="card-body py-3">
                <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                    <li class="d-flex align-items-center justify-content-between">
                        <span class="text-muted small">{{ __('Nursery / Institution') }}</span>
                        <span class="fw-semibold text-heading">{{ $event->institution?->translate(app()->getLocale())?->name ?? $event->institution?->translate('ar')?->name ?? '-' }}</span>
                    </li>
                    <li class="d-flex align-items-center justify-content-between">
                        <span class="text-muted small">{{ __('Branch') }}</span>
                        <span class="fw-semibold text-heading">{{ $event->branch?->translate(app()->getLocale())?->name ?? $event->branch?->translate('ar')?->name ?? '-' }}</span>
                    </li>
                    <li class="d-flex align-items-center justify-content-between">
                        <span class="text-muted small">{{ __('Academic Class') }}</span>
                        @if ($event->classroom)
                            <span class="badge bg-label-primary">{{ $event->classroom->translate(app()->getLocale())?->name ?? $event->classroom->translate('ar')?->name }}</span>
                        @else
                            <span class="badge bg-label-secondary">{{ __('All Classes in Branch') }}</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>

        <!-- Schedule & Timing Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2 text-heading fw-bold">
                    <i class="icon-base bx bx-calendar text-primary font-20"></i>
                    {{ __('Schedule & Timing') }}
                </h5>
            </div>
            <div class="card-body py-3">
                <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                    <li class="d-flex align-items-center justify-content-between">
                        <span class="text-muted small">{{ __('Start Date') }}</span>
                        <span class="fw-bold text-heading font-monospace">{{ $event->start_date ? $event->start_date->format('Y-m-d') : '-' }}</span>
                    </li>
                    <li class="d-flex align-items-center justify-content-between">
                        <span class="text-muted small">{{ __('End Date') }}</span>
                        <span class="fw-bold text-heading font-monospace">{{ $event->end_date ? $event->end_date->format('Y-m-d') : ($event->start_date ? $event->start_date->format('Y-m-d') : '-') }}</span>
                    </li>
                    @if ($event->start_time || $event->end_time)
                        <li class="d-flex align-items-center justify-content-between">
                            <span class="text-muted small">{{ __('Timing') }}</span>
                            <span class="fw-medium text-body">
                                {{ $event->start_time ? substr($event->start_time, 0, 5) : '' }}
                                {{ ($event->start_time && $event->end_time) ? '-' : '' }}
                                {{ $event->end_time ? substr($event->end_time, 0, 5) : '' }}
                            </span>
                        </li>
                    @endif
                    <li class="d-flex align-items-center justify-content-between">
                        <span class="text-muted small">{{ __('Created At') }}</span>
                        <span class="text-muted small">{{ $event->created_at ? $event->created_at->format('Y-m-d H:i') : '-' }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Danger Zone Card -->
        <div class="card shadow-sm border-0 border-top border-danger border-3">
            <div class="card-body p-3">
                <form action="{{ route('academic.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this event?') }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-1">
                        <i class="bx bx-trash"></i>
                        <span>{{ __('Delete Event') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
