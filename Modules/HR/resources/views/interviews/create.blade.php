@extends('layouts/contentNavbarLayout')

@section('title', __('Schedule Interview') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <a href="{{ route('hr.interviews.index') }}" class="btn btn-icon btn-sm btn-outline-secondary rounded-circle">
                <i class="bx bx-arrow-back font-18"></i>
            </a>
            <span>{{ __('Schedule New Interview') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.interviews.index') }}" class="text-muted">{{ __('Interviews') }}</a></li>
                <li class="breadcrumb-item active text-primary">{{ __('Schedule') }}</li>
            </ol>
        </nav>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('hr.interviews.store') }}" method="POST">
    @csrf

    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 mb-4" style="border: 1px solid #edf0f6 !important;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2 font-16 fw-bold">
                        <i class="bx bx-calendar-edit text-primary font-20"></i>
                        <span>{{ __('Interview Session Details') }}</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Candidate & Job Application Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-heading small required">{{ __('Select Candidate / Application') }}</label>
                        <select name="job_application_id" id="job_application_id" class="select2 form-select" required>
                            <option value="">{{ __('-- Select Candidate --') }}</option>
                            @foreach ($applications as $app)
                                @php
                                    $cand = $app->candidate;
                                    $op = $app->jobOpening;
                                    $opTitle = $op?->translate(app()->getLocale())?->title ?? $op?->title;
                                    $isSelected = (old('job_application_id', $selectedApplication?->id) == $app->id);
                                @endphp
                                <option value="{{ $app->id }}" {{ $isSelected ? 'selected' : '' }}>
                                    {{ $app->application_code }} &bull; {{ $cand?->full_name }} ({{ $opTitle }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading small">{{ __('Session Title / Round Name') }}</label>
                            <input type="text" name="title" class="form-control" placeholder="{{ __('e.g. First Technical Interview') }}" value="{{ old('title') }}">
                            <small class="text-muted font-11">{{ __('Leave blank to auto-generate Round #') }}</small>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading small required">{{ __('Interview Type') }}</label>
                            <select name="type" class="form-select" required>
                                @foreach ($types as $type)
                                    <option value="{{ $type->value }}" {{ old('type', 'technical') === $type->value ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading small required">{{ __('Interview Mode') }}</label>
                            <select name="mode" id="interview_mode" class="form-select" required>
                                @foreach ($modes as $mode)
                                    <option value="{{ $mode->value }}" {{ old('mode', 'online') === $mode->value ? 'selected' : '' }}>
                                        {{ $mode->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading small required">{{ __('Estimated Duration') }}</label>
                            <div class="input-group">
                                <input type="number" name="duration_minutes" class="form-control" min="15" max="240" step="15" value="{{ old('duration_minutes', 45) }}" required>
                                <span class="input-group-text font-12">{{ __('Minutes') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-heading small required">{{ __('Date & Start Time') }}</label>
                            <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at', now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i')) }}" required>
                        </div>

                        <div class="col-12 col-md-6" id="field_meeting_link">
                            <label class="form-label fw-semibold text-heading small">{{ __('Meeting Link (Online)') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-video"></i></span>
                                <input type="url" name="meeting_link" class="form-control" placeholder="https://meet.google.com/..." value="{{ old('meeting_link') }}">
                            </div>
                        </div>

                        <div class="col-12 col-md-6 d-none" id="field_location">
                            <label class="form-label fw-semibold text-heading small">{{ __('Office / Room Location') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-map"></i></span>
                                <input type="text" name="location" class="form-control" placeholder="{{ __('Headquarters - Meeting Room A') }}" value="{{ old('location') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold text-heading small">{{ __('Notes & Instructions (Internal / Candidate)') }}</label>
                        <textarea name="notes" rows="3" class="form-control" placeholder="{{ __('Provide any guidelines, test instructions, or notes for the interviewers...') }}">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column: Interviewers Selection -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4" style="border: 1px solid #edf0f6 !important;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2 font-16 fw-bold">
                        <i class="bx bx-group text-primary font-20"></i>
                        <span>{{ __('Interview Committee') }}</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <label class="form-label fw-semibold text-heading small required">{{ __('Select Interviewers (Staff)') }}</label>
                    <p class="text-muted font-11 mb-2">{{ __('Choose one or more employees to conduct the interview and submit scorecards.') }}</p>

                    <select name="interviewer_ids[]" class="select2 form-select" multiple required data-placeholder="{{ __('Choose interviewers...') }}">
                        @foreach ($employees as $emp)
                            @php
                                $empName = $emp->translate(app()->getLocale())?->name ?? $emp->name;
                                $jobTitle = $emp->jobTitle?->translate(app()->getLocale())?->name ?? $emp->jobTitle?->name;
                            @endphp
                            <option value="{{ $emp->id }}" {{ in_array($emp->id, old('interviewer_ids', [])) ? 'selected' : '' }}>
                                {{ $empName }} @if($jobTitle) ({{ $jobTitle }}) @endif
                            </option>
                        @endforeach
                    </select>

                    <div class="alert alert-info border-0 shadow-none mt-4 p-3 rounded-3 small">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bx bx-info-circle font-18 flex-shrink-0 mt-0.5"></i>
                            <div>
                                <strong>{{ __('Automatic Pipeline Update:') }}</strong>
                                <span class="d-block mt-1">{{ __('Scheduling this interview will automatically move the candidate to the "Interviews" stage in the Kanban Pipeline.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Action Bar (Save at Bottom) -->
    <div class="card border-0 shadow-sm rounded-3 mt-2" style="border: 1px solid #edf0f6 !important;">
        <div class="card-body p-3 d-flex align-items-center justify-content-between">
            <a href="{{ route('hr.interviews.index') }}" class="btn btn-outline-secondary px-4">
                {{ __('Cancel') }}
            </a>

            <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-2">
                <i class="bx bx-calendar-check font-18"></i>
                <span class="fw-semibold">{{ __('Schedule Interview Session') }}</span>
            </button>
        </div>
    </div>
</form>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = window.jQuery || window.$;

    if ($.fn.select2) {
        $('.select2').select2({ width: '100%' });
    }

    const modeSelect = document.getElementById('interview_mode');
    const fieldLink = document.getElementById('field_meeting_link');
    const fieldLocation = document.getElementById('field_location');

    function toggleModeFields() {
        const val = modeSelect.value;
        if (val === 'online') {
            fieldLink.classList.remove('d-none');
            fieldLocation.classList.add('d-none');
        } else if (val === 'in_person') {
            fieldLink.classList.add('d-none');
            fieldLocation.classList.remove('d-none');
        } else {
            fieldLink.classList.add('d-none');
            fieldLocation.classList.add('d-none');
        }
    }

    modeSelect.addEventListener('change', toggleModeFields);
    toggleModeFields();
});
</script>
@endsection
