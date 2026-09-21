@extends('layouts/contentNavbarLayout')

@section('title', __('Edit Interview Session') . ' — ' . ($interview->title ?? $interview->interview_code) . ' - ' . config('variables.templateName'))
@section('content')

@php
    $c = $interview->candidate;
    $op = $interview->jobOpening;
    $opTitle = $op?->translate(app()->getLocale())?->title ?? $op?->translate('ar')?->title ?? $op?->translate('en')?->title ?? __('N/A');
    $currentInterviewerIds = $interview->interviewers->pluck('id')->toArray();
    $siblingInterviews = \Modules\HR\Models\Interview::where('job_application_id', $interview->job_application_id)
        ->orderBy('interview_round')->get();

    $typeColorMap = [
        'phone_screen' => ['bg'=>'#e3f2fd','border'=>'#1976d2','icon'=>'#1976d2','txt'=>'#1565c0'],
        'technical'    => ['bg'=>'#e8eaf6','border'=>'#3f51b5','icon'=>'#3f51b5','txt'=>'#283593'],
        'hr'           => ['bg'=>'#e0f7fa','border'=>'#00897b','icon'=>'#00897b','txt'=>'#00695c'],
        'managerial'   => ['bg'=>'#fff8e1','border'=>'#f9a825','icon'=>'#f9a825','txt'=>'#e65100'],
        'assessment'   => ['bg'=>'#fce4ec','border'=>'#c62828','icon'=>'#c62828','txt'=>'#b71c1c'],
        'final'        => ['bg'=>'#e8f5e9','border'=>'#2e7d32','icon'=>'#2e7d32','txt'=>'#1b5e20'],
    ];
    $modeColorMap = [
        'in_person' => ['bg'=>'#e8eaf6','border'=>'#3949ab','icon'=>'text-primary'],
        'online'    => ['bg'=>'#e8f5e9','border'=>'#2e7d32','icon'=>'text-success'],
        'phone'     => ['bg'=>'#e3f2fd','border'=>'#1565c0','icon'=>'text-info'],
    ];
@endphp

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <a href="{{ route('hr.interviews.show', $interview->id) }}"
               class="btn btn-icon btn-sm btn-outline-secondary rounded-circle">
                <i class="bx bx-arrow-back font-18"></i>
            </a>
            <span>{{ __('Edit Interview Session') }}</span>
            <span class="badge bg-label-primary rounded-pill font-12">{{ $interview->interview_code }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.interviews.index') }}" class="text-muted">{{ __('Interviews') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.interviews.show', $interview->id) }}" class="text-muted">{{ $interview->interview_code }}</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold">{{ __('Edit') }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge {{ $interview->status->badgeClass() }} rounded-pill px-3 py-2 font-12 fw-semibold">
            {{ $interview->status->label() }}
        </span>
        <a href="{{ route('hr.interviews.show', $interview->id) }}" class="btn btn-outline-secondary btn-sm px-3 rounded-3">
            <i class="bx bx-x me-1"></i>{{ __('Cancel') }}
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm rounded-3" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Candidate Strip with Round Pills --}}
<div class="card border-0 shadow-sm rounded-3 mb-4"
     style="background:linear-gradient(135deg,#f8f9ff 0%,#eef2ff 100%);border:1px solid #e3e8f8!important;">
    <div class="card-body p-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar avatar-md bg-label-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5 flex-shrink-0">
                {{ $c?->initials ?? 'CA' }}
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-heading">
                    <a href="{{ route('hr.job-applications.show', $interview->job_application_id) }}"
                       class="text-heading text-hover-primary">{{ $c?->full_name }}</a>
                </h5>
                <small class="text-muted">
                    {{ $c?->current_job_title ?? __('Applicant') }} &bull;
                    <span class="text-primary fw-semibold">{{ $opTitle }}</span>
                </small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @foreach ($siblingInterviews as $sibling)
                @php $isNow = ($sibling->id === $interview->id); @endphp
                <a href="{{ $isNow ? '#' : route('hr.interviews.edit', $sibling->id) }}"
                   class="badge {{ $isNow ? 'bg-primary text-white shadow-sm' : 'bg-label-secondary text-heading' }} rounded-pill px-3 py-1.5 font-11 fw-bold text-decoration-none d-flex align-items-center gap-1">
                    @if($isNow)<i class="bx bx-edit font-11"></i>@endif
                    R{{ $sibling->interview_round }}
                    @if($isNow)<span class="opacity-75 ms-1">({{ __('editing') }})</span>@endif
                </a>
            @endforeach
            <a href="{{ route('hr.interviews.create', ['job_application_id' => $interview->job_application_id]) }}"
               class="badge bg-label-success rounded-pill px-3 py-1.5 font-11 fw-bold text-decoration-none d-flex align-items-center gap-1">
                <i class="bx bx-plus font-11"></i>{{ __('New Round') }}
            </a>
        </div>
    </div>
</div>

{{-- FORM --}}
<form action="{{ route('hr.interviews.update', $interview->id) }}" method="POST" id="editInterviewForm">
    @csrf
    @method('PUT')

    <div class="row g-4">

        {{-- ═══ MAIN COLUMN ═══ --}}
        <div class="col-12 col-lg-8 d-flex flex-column gap-4">

            {{-- 1. Basic Info --}}
            <div class="card border-0 shadow-sm rounded-3" style="border:1px solid #edf0f6!important;">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                    <i class="bx bx-info-circle text-primary font-20"></i>
                    <h5 class="card-title mb-0 fw-bold font-15">{{ __('Basic Information') }}</h5>
                </div>
                <div class="card-body p-4">
                    <label class="form-label fw-semibold small text-heading">{{ __('Session Title / Round Name') }}</label>
                    <input type="text" name="title" class="form-control"
                           placeholder="{{ __('e.g. Technical Interview – Round 2') }}"
                           value="{{ old('title', $interview->title) }}">
                </div>
            </div>

            {{-- 2. Interview TYPE – visual cards --}}
            <div class="card border-0 shadow-sm rounded-3" style="border:1px solid #edf0f6!important;">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                    <i class="bx bx-category text-primary font-20"></i>
                    <h5 class="card-title mb-0 fw-bold font-15">{{ __('Interview Type') }}</h5>
                    <span class="ms-auto badge bg-label-info rounded-pill font-11">{{ __('Click to change') }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        @foreach ($types as $tp)
                            @php
                                $isTypeActive = old('type', $interview->type->value) === $tp->value;
                                $tc = $typeColorMap[$tp->value] ?? ['bg'=>'#f5f5f5','border'=>'#757575','icon'=>'#757575','txt'=>'#424242'];
                            @endphp
                            <div class="col-6 col-sm-4">
                                <label class="type-card card text-center p-3 rounded-3 h-100"
                                       data-color-bg="{{ $tc['bg'] }}"
                                       data-color-border="{{ $tc['border'] }}"
                                       style="cursor:pointer;border:2px solid {{ $isTypeActive ? $tc['border'] : '#dee2e6' }}!important;
                                              background:{{ $isTypeActive ? $tc['bg'] : '#fff' }};transition:all .22s ease;">
                                    <input type="radio" name="type" value="{{ $tp->value }}"
                                           class="type-radio d-none" {{ $isTypeActive ? 'checked' : '' }} required>
                                    <i class="{{ $tp->icon() }} font-28 mb-2 d-block" style="color:{{ $tc['icon'] }}"></i>
                                    <span class="fw-bold font-12 d-block" style="color:{{ $isTypeActive ? $tc['txt'] : '#495057' }}">
                                        {{ $tp->label() }}
                                    </span>
                                    <span class="type-badge badge {{ $isTypeActive ? 'bg-primary text-white' : 'bg-light text-muted' }} rounded-pill font-10 mt-1 px-2 py-0.5">
                                        @if($isTypeActive)<i class="bx bx-check font-11"></i> {{ __('Selected') }}@else{{ __('Select') }}@endif
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 3. Interview MODE – visual cards --}}
            <div class="card border-0 shadow-sm rounded-3" style="border:1px solid #edf0f6!important;">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                    <i class="bx bx-broadcast text-primary font-20"></i>
                    <h5 class="card-title mb-0 fw-bold font-15">{{ __('Interview Mode') }}</h5>
                    <span class="ms-auto badge bg-label-warning rounded-pill font-11">{{ __('How will it be conducted?') }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        @foreach ($modes as $md)
                            @php
                                $isModeActive = old('mode', $interview->mode->value) === $md->value;
                                $mc = $modeColorMap[$md->value] ?? ['bg'=>'#f5f5f5','border'=>'#757575','icon'=>'text-secondary'];
                            @endphp
                            <div class="col-12 col-sm-4">
                                <label class="mode-card card text-center p-3 rounded-3 h-100"
                                       data-mode="{{ $md->value }}"
                                       data-bg="{{ $mc['bg'] }}"
                                       data-border="{{ $mc['border'] }}"
                                       style="cursor:pointer;border:2px solid {{ $isModeActive ? $mc['border'] : '#dee2e6' }}!important;
                                              background:{{ $isModeActive ? $mc['bg'] : '#fff' }};transition:all .22s ease;">
                                    <input type="radio" name="mode" value="{{ $md->value }}"
                                           class="mode-radio d-none" {{ $isModeActive ? 'checked' : '' }} required>
                                    <i class="{{ $md->icon() }} {{ $mc['icon'] }} font-32 mb-2 d-block"></i>
                                    <span class="fw-bold font-13 d-block">{{ $md->label() }}</span>
                                    <span class="mode-badge badge {{ $isModeActive ? 'bg-success text-white' : 'bg-light text-muted' }} rounded-pill font-10 mt-1 px-2 py-0.5">
                                        @if($isModeActive)<i class="bx bx-check font-11"></i> {{ __('Active') }}@else{{ __('Switch') }}@endif
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    {{-- Logistics --}}
                    <div class="row g-3">
                        <div class="col-12 col-md-6" id="field_meeting_link">
                            <label class="form-label fw-semibold small text-heading">
                                <i class="bx bx-video text-success me-1"></i>{{ __('Meeting Link (Online)') }}
                            </label>
                            <input type="url" name="meeting_link" class="form-control"
                                   placeholder="https://meet.google.com/..."
                                   value="{{ old('meeting_link', $interview->meeting_link) }}">
                        </div>
                        <div class="col-12 col-md-6" id="field_location">
                            <label class="form-label fw-semibold small text-heading">
                                <i class="bx bx-map text-primary me-1"></i>{{ __('Office / Room Location') }}
                            </label>
                            <input type="text" name="location" class="form-control"
                                   placeholder="{{ __('e.g. Headquarters – Meeting Room A') }}"
                                   value="{{ old('location', $interview->location) }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Schedule --}}
            <div class="card border-0 shadow-sm rounded-3" style="border:1px solid #edf0f6!important;">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                    <i class="bx bx-calendar-edit text-primary font-20"></i>
                    <h5 class="card-title mb-0 fw-bold font-15">{{ __('Schedule') }}</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-heading required">{{ __('Date & Start Time') }}</label>
                            <input type="datetime-local" name="scheduled_at" class="form-control"
                                   value="{{ old('scheduled_at', $interview->scheduled_at?->format('Y-m-d\TH:i')) }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-heading required">{{ __('Estimated Duration') }}</label>
                            <div class="input-group">
                                <input type="number" name="duration_minutes" class="form-control" id="durationInput"
                                       min="15" max="240" step="15"
                                       value="{{ old('duration_minutes', $interview->duration_minutes) }}" required>
                                <span class="input-group-text font-12">{{ __('Minutes') }}</span>
                            </div>
                            <div class="d-flex gap-1 mt-2 flex-wrap">
                                @foreach ([30,45,60,90] as $preset)
                                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-0 font-11 duration-preset"
                                            data-value="{{ $preset }}">{{ $preset }} {{ __('min') }}</button>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-heading">{{ __('Notes & Instructions') }}</label>
                            <textarea name="notes" rows="3" class="form-control"
                                      placeholder="{{ __('Any guidelines, test instructions, or notes for the interviewers...') }}">{{ old('notes', $interview->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /main col --}}

        {{-- ═══ SIDEBAR COLUMN ═══ --}}
        <div class="col-12 col-lg-4 d-flex flex-column gap-4">

            {{-- STATUS – visual cards --}}
            <div class="card border-0 shadow-sm rounded-3" style="border:1px solid #edf0f6!important;">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                    <i class="bx bx-check-shield text-primary font-20"></i>
                    <h5 class="card-title mb-0 fw-bold font-15">{{ __('Interview Status') }}</h5>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex flex-column gap-2">
                        @foreach ($statuses as $st)
                            @php $isStatActive = old('status', $interview->status->value) === $st->value; @endphp
                            <label class="status-card d-flex align-items-center gap-3 p-3 rounded-3"
                                   style="border:2px solid {{ $isStatActive ? 'var(--bs-primary)' : '#dee2e6' }}!important;
                                          background:{{ $isStatActive ? '#f0f4ff' : '#fff' }};
                                          cursor:pointer;transition:all .2s ease;">
                                <input type="radio" name="status" value="{{ $st->value }}"
                                       class="status-radio d-none" {{ $isStatActive ? 'checked' : '' }} required>
                                <span class="badge {{ $st->badgeClass() }} rounded-circle p-2 flex-shrink-0"
                                      style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;">
                                    <i class="{{ $st->icon() }} font-14"></i>
                                </span>
                                <span class="fw-semibold font-13 flex-grow-1 text-heading">{{ $st->label() }}</span>
                                <i class="bx {{ $isStatActive ? 'bx-check-circle text-primary' : 'bx-circle text-muted' }} font-20 flex-shrink-0 status-check-icon"></i>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Committee (Interviewers) --}}
            <div class="card border-0 shadow-sm rounded-3" style="border:1px solid #edf0f6!important;">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                    <i class="bx bx-group text-primary font-20"></i>
                    <h5 class="card-title mb-0 fw-bold font-15">{{ __('Interview Committee') }}</h5>
                </div>
                <div class="card-body p-4">
                    <label class="form-label fw-semibold small text-heading required">{{ __('Select Interviewers (Staff)') }}</label>
                    <p class="text-muted font-11 mb-2">{{ __('Choose one or more employees to conduct the interview.') }}</p>
                    <select name="interviewer_ids[]" class="select2 form-select" multiple required
                            data-placeholder="{{ __('Choose interviewers...') }}">
                        @foreach ($employees as $emp)
                            @php
                                $empName  = $emp->translate(app()->getLocale())?->name ?? $emp->name;
                                $jobTitle = $emp->jobTitle?->translate(app()->getLocale())?->name ?? $emp->jobTitle?->name;
                                $isSel    = in_array($emp->id, old('interviewer_ids', $currentInterviewerIds));
                            @endphp
                            <option value="{{ $emp->id }}" {{ $isSel ? 'selected' : '' }}>
                                {{ $empName }}@if($jobTitle) – {{ $jobTitle }}@endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Round Timeline --}}
            <div class="card border-0 shadow-sm rounded-3" style="border:1px solid #edf0f6!important;">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
                    <i class="bx bx-history text-primary font-20"></i>
                    <h5 class="card-title mb-0 fw-bold font-15">{{ __('Session Timeline') }}</h5>
                </div>
                <div class="card-body p-3">
                    <ul class="list-unstyled mb-0">
                        @foreach ($siblingInterviews as $sib)
                            @php $isCurrent = ($sib->id === $interview->id); @endphp
                            <li class="d-flex gap-3 {{ $loop->last ? '' : 'mb-3' }}">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="rounded-circle d-flex align-items-center justify-content-center fw-bold font-11 flex-shrink-0"
                                          style="width:28px;height:28px;background:{{ $isCurrent ? '#6366f1' : '#e2e8f0' }};color:{{ $isCurrent ? '#fff' : '#64748b' }};">
                                        {{ $sib->interview_round }}
                                    </span>
                                    @if (!$loop->last)
                                        <div style="width:2px;flex:1;background:#e2e8f0;min-height:18px;margin-top:3px;"></div>
                                    @endif
                                </div>
                                <div class="flex-grow-1 pb-2">
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="fw-semibold font-12 {{ $isCurrent ? 'text-primary' : 'text-heading' }}">
                                            {{ $sib->title ?? (__('Round') . ' #' . $sib->interview_round) }}
                                        </span>
                                        @if ($isCurrent)
                                            <span class="badge bg-label-primary rounded-pill font-10 px-2">{{ __('Editing now') }}</span>
                                        @endif
                                    </div>
                                    <small class="text-muted d-block">
                                        <i class="bx bx-calendar font-11 me-1"></i>{{ $sib->scheduled_at?->format('d M Y, h:i A') }}
                                    </small>
                                    <span class="badge {{ $sib->status->badgeClass() }} rounded-pill font-10 px-2 py-0.5 mt-1">{{ $sib->status->label() }}</span>
                                    @if (!$isCurrent)
                                        <div class="mt-1 d-flex gap-1">
                                            <a href="{{ route('hr.interviews.show', $sib->id) }}"
                                               class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-0 font-10">{{ __('View') }}</a>
                                            <a href="{{ route('hr.interviews.edit', $sib->id) }}"
                                               class="btn btn-xs btn-outline-warning rounded-pill px-2 py-0 font-10">{{ __('Edit') }}</a>
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Danger Zone --}}
            @if ($interview->status === \Modules\HR\Enums\InterviewStatus::SCHEDULED)
                <div class="card shadow-sm rounded-3" style="border:2px solid rgba(220,53,69,.25)!important;">
                    <div class="card-header bg-transparent py-3 d-flex align-items-center gap-2"
                         style="border-bottom:1px solid rgba(220,53,69,.2)!important;">
                        <i class="bx bx-error-alt text-danger font-20"></i>
                        <h5 class="card-title mb-0 fw-bold font-15 text-danger">{{ __('Danger Zone') }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">{{ __('Cancelling will notify the interviewers and update the candidate pipeline accordingly.') }}</p>
                        <button type="button" id="cancelInterviewBtn"
                                class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                            <i class="bx bx-x-circle font-16"></i>{{ __('Cancel This Interview') }}
                        </button>
                    </div>
                </div>
            @endif

        </div>{{-- /sidebar --}}
    </div>

    {{-- Sticky Save Bar --}}
    <div class="card border-0 shadow mt-4" style="border:1px solid #edf0f6!important;">
        <div class="card-body p-3 d-flex align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2 small text-muted">
                <i class="bx bx-info-circle text-primary font-16"></i>
                <span>{{ __('Last updated') }}: {{ $interview->updated_at?->diffForHumans() }}</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('hr.interviews.show', $interview->id) }}" class="btn btn-outline-secondary px-4">{{ __('Discard') }}</a>
                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-2 shadow-sm" id="saveBtn">
                    <i class="bx bx-save font-18"></i>
                    <span class="fw-semibold">{{ __('Save Changes') }}</span>
                </button>
            </div>
        </div>
    </div>

</form>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Select2 ── */
    if (window.jQuery && $.fn.select2) {
        $('.select2').select2({ width: '100%' });
    }

    /* ── Helper: force border color bypassing !important ── */
    function styleCard(el, border, bg) {
        el.style.setProperty('border-color', border, 'important');
        el.style.background = bg;
    }

    /* ══════════════════════════════════
       TYPE CARDS
    ══════════════════════════════════ */
    document.querySelectorAll('.type-card').forEach(function (card) {
        card.addEventListener('click', function () {
            const radio  = this.querySelector('.type-radio');
            if (!radio) return;
            radio.checked = true;
            const bg     = this.dataset.colorBg     || '#e8eaf6';
            const border = this.dataset.colorBorder  || '#3f51b5';

            document.querySelectorAll('.type-card').forEach(function (c) {
                const isThis = (c === card);
                styleCard(c, isThis ? border : '#dee2e6', isThis ? bg : '#fff');
                const badge = c.querySelector('.type-badge');
                if (!badge) return;
                if (isThis) {
                    badge.className = 'type-badge badge bg-primary text-white rounded-pill font-10 mt-1 px-2 py-0.5';
                    badge.innerHTML = '<i class="bx bx-check font-11"></i> {{ __("Selected") }}';
                } else {
                    badge.className = 'type-badge badge bg-light text-muted rounded-pill font-10 mt-1 px-2 py-0.5';
                    badge.textContent = '{{ __("Select") }}';
                }
            });
        });
    });

    /* ══════════════════════════════════
       MODE CARDS + Logistics show/hide
    ══════════════════════════════════ */
    const fieldLink = document.getElementById('field_meeting_link');
    const fieldLoc  = document.getElementById('field_location');

    function applyModeUI(val) {
        document.querySelectorAll('.mode-card').forEach(function (card) {
            const radio  = card.querySelector('.mode-radio');
            const isThis = radio && radio.value === val;
            styleCard(card,
                isThis ? (card.dataset.border || '#3949ab') : '#dee2e6',
                isThis ? (card.dataset.bg     || '#e8eaf6') : '#fff');
            const badge = card.querySelector('.mode-badge');
            if (!badge) return;
            if (isThis) {
                badge.className = 'mode-badge badge bg-success text-white rounded-pill font-10 mt-1 px-2 py-0.5';
                badge.innerHTML = '<i class="bx bx-check font-11"></i> {{ __("Active") }}';
            } else {
                badge.className = 'mode-badge badge bg-light text-muted rounded-pill font-10 mt-1 px-2 py-0.5';
                badge.textContent = '{{ __("Switch") }}';
            }
        });

        if (val === 'online')         { fieldLink?.classList.remove('d-none'); fieldLoc?.classList.add('d-none'); }
        else if (val === 'in_person') { fieldLink?.classList.add('d-none');    fieldLoc?.classList.remove('d-none'); }
        else                          { fieldLink?.classList.add('d-none');    fieldLoc?.classList.add('d-none'); }
    }

    document.querySelectorAll('.mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            const radio = this.querySelector('.mode-radio');
            if (!radio) return;
            radio.checked = true;
            applyModeUI(radio.value);
        });
    });

    const checkedMode = document.querySelector('.mode-radio:checked');
    applyModeUI(checkedMode ? checkedMode.value : '');

    /* ══════════════════════════════════
       STATUS CARDS
    ══════════════════════════════════ */
    document.querySelectorAll('.status-card').forEach(function (card) {
        card.addEventListener('click', function () {
            const radio = this.querySelector('.status-radio');
            if (!radio) return;
            radio.checked = true;

            document.querySelectorAll('.status-card').forEach(function (c) {
                const isThis = (c === card);
                styleCard(c, isThis ? 'var(--bs-primary)' : '#dee2e6', isThis ? '#f0f4ff' : '#fff');
                const icon = c.querySelector('.status-check-icon');
                if (icon) {
                    icon.className = 'bx font-20 flex-shrink-0 status-check-icon ' +
                        (isThis ? 'bx-check-circle text-primary' : 'bx-circle text-muted');
                }
            });
        });
    });

    /* ══════════════════════════════════
       DURATION PRESETS
    ══════════════════════════════════ */
    const durInput = document.getElementById('durationInput');
    document.querySelectorAll('.duration-preset').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (durInput) durInput.value = this.dataset.value;
            document.querySelectorAll('.duration-preset').forEach(function (b) {
                b.classList.remove('btn-secondary', 'text-white');
                b.classList.add('btn-outline-secondary');
            });
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-secondary', 'text-white');
        });
    });

    /* ══════════════════════════════════
       SAVE – loading spinner on submit
    ══════════════════════════════════ */
    document.getElementById('editInterviewForm')?.addEventListener('submit', function () {
        const btn = document.getElementById('saveBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ __("Saving...") }}';
        }
    });

    /* ══════════════════════════════════
       CANCEL INTERVIEW (AJAX)
    ══════════════════════════════════ */
    document.getElementById('cancelInterviewBtn')?.addEventListener('click', function () {
        if (!confirm('{{ __("Are you sure you want to cancel this interview session?") }}')) return;
        fetch('/hr/interviews/{{ $interview->id }}/cancel', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ reason: '' })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) window.location.href = '{{ route("hr.interviews.show", $interview->id) }}';
        });
    });

});
</script>
@endsection
