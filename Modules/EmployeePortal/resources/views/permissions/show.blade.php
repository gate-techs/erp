@extends('employeeportal::layouts.master')

@section('title', __('Permission Details'))

@section('content')
<div class="exact-perm-page">
    <!-- Top Navigation Header -->
    <div class="exact-perm-header">
        <a href="{{ route('portal.permissions.index') }}" class="exact-perm-back-btn" title="{{ __('Back') }}">
            <i class="bx {{ app()->getLocale() === 'ar' ? 'bx-chevron-right' : 'bx-chevron-left' }}"></i>
        </a>
        <h1 class="exact-perm-title">{{ __('Permission Details') }}</h1>
    </div>

    <!-- 1. Card: بيانات الإذن (Permission Information) -->
    <div class="exact-card">
        <div class="exact-card-header mb-3">
            <h2 class="exact-card-title">{{ __('Permission Information') }}</h2>
            <span class="exact-card-badge">{{ $requestCode }}</span>
        </div>

        <!-- Row 1: التوقيت والمدة -->
        <div class="exact-info-pill">
            <span class="exact-info-label">{{ __('Timing and Duration') }}</span>
            <span class="exact-info-value">{{ $timeAndDuration }}</span>
        </div>

        <!-- Row 2: نوع الاستئذان -->
        <div class="exact-info-pill">
            <span class="exact-info-label">{{ __('Permission Type') }}</span>
            <span class="exact-info-value">
                <span>{{ $permTypeLabel }}</span>
                @if($isEmergency)
                    <span class="badge bg-danger text-white rounded-pill px-2 py-1" style="font-size: 10px;">{{ __('Emergency') }}</span>
                @endif
            </span>
        </div>

        <!-- Row 3: المبرر (Only if present in database) -->
        @if($hasReason)
            <div class="exact-reason-box">
                <div class="exact-reason-header">
                    <i class="bx bx-menu-alt-right fs-6"></i>
                    <span>{{ __('Justification') }}</span>
                </div>
                <p class="exact-reason-text">{{ $reasonText }}</p>
            </div>
        @endif

        <!-- Row 4: المرفق (Only if file actually attached) -->
        @if($hasAttachment)
            <div class="exact-attachment-pill">
                <div class="exact-attachment-info">
                    <i class="bx bxs-file-pdf text-primary fs-5"></i>
                    <span class="exact-attachment-name">{{ $attachmentName }}</span>
                </div>
                <a href="{{ $attachmentUrl }}" target="_blank" class="exact-preview-link">{{ __('Preview') }}</a>
            </div>
        @endif
    </div>

    <!-- 2. Card: مسار الاعتماد (Approval Workflow Stepper) -->
    <div class="exact-card">
        <div class="exact-card-header">
            <div class="d-flex align-items-center gap-2">
                <i class="bx bx-pulse" style="font-size: 22px; color: #002A44;"></i>
                <h2 class="exact-card-title">{{ __('Approval Journey') }}</h2>
            </div>
            <span class="exact-card-badge">{{ $stageText }}</span>
        </div>

        <div class="exact-timeline">
            @foreach($steps as $step)
                <div class="exact-timeline-item {{ $step['line_active'] ? 'line-active' : '' }}">
                    <div class="exact-timeline-badge {{ $step['state'] }}">
                        <i class="bx {{ $step['icon'] }}"></i>
                    </div>
                    <div class="exact-timeline-content">
                        <h3 class="exact-timeline-title {{ $step['state'] === 'locked' ? 'muted' : '' }}">{{ $step['title'] }}</h3>
                        <p class="exact-timeline-desc {{ $step['state'] === 'locked' ? 'muted-light' : '' }}">{{ $step['subtitle'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
