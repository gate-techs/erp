@extends('layouts/contentNavbarLayout')

@section('title', __('Interview Evaluation Scorecard') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
.score-group .btn-check:checked + .btn-outline-primary {
    background-color: #696cff !important;
    color: #ffffff !important;
    border-color: #696cff !important;
    box-shadow: 0 3px 8px rgba(105, 108, 255, 0.4) !important;
    font-weight: 700 !important;
}
.score-group .btn-check:checked + .btn-outline-info {
    background-color: #03c3ec !important;
    color: #ffffff !important;
    border-color: #03c3ec !important;
    box-shadow: 0 3px 8px rgba(3, 195, 236, 0.4) !important;
    font-weight: 700 !important;
}
.score-group .btn-check:checked + .btn-outline-warning {
    background-color: #ffab00 !important;
    color: #ffffff !important;
    border-color: #ffab00 !important;
    box-shadow: 0 3px 8px rgba(255, 171, 0, 0.4) !important;
    font-weight: 700 !important;
}
.score-group .btn-check:checked + .btn-outline-success {
    background-color: #71dd37 !important;
    color: #ffffff !important;
    border-color: #71dd37 !important;
    box-shadow: 0 3px 8px rgba(113, 221, 55, 0.4) !important;
    font-weight: 700 !important;
}
.score-group .btn-check:checked + .btn-outline-secondary {
    background-color: #8592a3 !important;
    color: #ffffff !important;
    border-color: #8592a3 !important;
    box-shadow: 0 3px 8px rgba(133, 146, 163, 0.4) !important;
    font-weight: 700 !important;
}
.score-group .btn {
    cursor: pointer;
    transition: all 0.15s ease-in-out;
}
.score-group .btn:hover {
    transform: translateY(-1px);
}
</style>
@endsection

@section('content')
<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <a href="{{ route('hr.interviews.show', $interview->id) }}" class="btn btn-icon btn-sm btn-outline-secondary rounded-circle">
                <i class="bx bx-arrow-back font-18"></i>
            </a>
            <span>{{ __('Candidate Evaluation Scorecard') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.interviews.index') }}" class="text-muted">{{ __('Interviews') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.interviews.show', $interview->id) }}" class="text-muted">#{{ $interview->id }}</a></li>
                <li class="breadcrumb-item active text-primary">{{ __('Scorecard') }}</li>
            </ol>
        </nav>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm rounded-3" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@php
    $c = $interview->candidate;
    $op = $interview->jobOpening;
    $opTitle = $op?->translate(app()->getLocale())?->title ?? $op?->title;
@endphp

<!-- Candidate Header Info Strip -->
<div class="card border-0 shadow-sm rounded-3 mb-4 bg-light" style="border: 1px solid #edf0f6 !important;">
    <div class="card-body p-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar avatar-md bg-label-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5">
                {{ $c?->initials ?? 'CA' }}
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-heading">{{ $c?->full_name }}</h5>
                <span class="text-muted small">{{ $c?->current_job_title ?? __('Applicant') }} &bull; {{ $opTitle }}</span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge {{ $interview->type->badgeClass() }} rounded-pill px-3 py-1.5 font-11 fw-semibold">
                {{ $interview->type->label() }} (Round #{{ $interview->interview_round }})
            </span>
            <span class="badge {{ $interview->mode->badgeClass() }} rounded-pill px-3 py-1.5 font-11 fw-semibold">
                <i class="{{ $interview->mode->icon() }} me-1"></i>{{ $interview->mode->label() }}
            </span>
        </div>
    </div>
</div>

<form action="{{ route('hr.interviews.store-evaluation', $interview->id) }}" method="POST" id="evaluationForm">
    @csrf

    <div class="row g-4">
        <!-- Main Form: 5 Rubric Criteria -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 mb-4" style="border: 1px solid #edf0f6 !important;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 font-16 fw-bold d-flex align-items-center gap-2">
                        <i class="bx bx-check-circle text-primary font-20"></i>
                        <span>{{ __('Evaluation Criteria (1 = Poor, 5 = Excellent)') }}</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Evaluator Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-heading small required">{{ __('Evaluating Interviewer (Staff Member)') }}</label>
                        <select name="interviewer_id" class="form-select" required>
                            <option value="">{{ __('-- Select Interviewer --') }}</option>
                            @foreach ($employees as $emp)
                                @php
                                    $empName = $emp->translate(app()->getLocale())?->name ?? $emp->name;
                                @endphp
                                <option value="{{ $emp->id }}" {{ old('interviewer_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $empName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <hr class="my-4 text-muted opacity-25">

                    <!-- Criterion 1: Technical Score -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fw-bold text-heading mb-0 required">
                                1. {{ __('Technical & Functional Competency') }}
                            </label>
                            <span class="badge bg-label-primary font-12" id="val_technical">{{ old('technical_score', 3) }} / 5</span>
                        </div>
                        <p class="text-muted font-11 mb-2">{{ __('Demonstrates required domain knowledge, practical skills, and tooling proficiency.') }}</p>
                        <div class="d-flex gap-2 score-group">
                            @for ($i = 1; $i <= 5; $i++)
                                <input type="radio" class="btn-check score-radio" name="technical_score" id="tech_{{ $i }}" value="{{ $i }}" autocomplete="off" {{ old('technical_score', 3) == $i ? 'checked' : '' }} data-badge="val_technical">
                                <label class="btn btn-outline-primary flex-fill text-center py-2 rounded-2" for="tech_{{ $i }}">
                                    <span class="d-block fw-bold font-14">{{ $i }}</span>
                                    <span class="font-10 d-none d-sm-inline">
                                        @if($i==1) {{ __('Poor') }} @elseif($i==3) {{ __('Good') }} @elseif($i==5) {{ __('Expert') }} @endif
                                    </span>
                                </label>
                            @endfor
                        </div>
                    </div>

                    <!-- Criterion 2: Communication Score -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fw-bold text-heading mb-0 required">
                                2. {{ __('Communication & Presentation') }}
                            </label>
                            <span class="badge bg-label-info font-12" id="val_comm">{{ old('communication_score', 4) }} / 5</span>
                        </div>
                        <p class="text-muted font-11 mb-2">{{ __('Articulates thoughts clearly, listens actively, and displays professional composure.') }}</p>
                        <div class="d-flex gap-2 score-group">
                            @for ($i = 1; $i <= 5; $i++)
                                <input type="radio" class="btn-check score-radio" name="communication_score" id="comm_{{ $i }}" value="{{ $i }}" autocomplete="off" {{ old('communication_score', 4) == $i ? 'checked' : '' }} data-badge="val_comm">
                                <label class="btn btn-outline-info flex-fill text-center py-2 rounded-2" for="comm_{{ $i }}">
                                    <span class="d-block fw-bold font-14">{{ $i }}</span>
                                </label>
                            @endfor
                        </div>
                    </div>

                    <!-- Criterion 3: Problem Solving -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fw-bold text-heading mb-0 required">
                                3. {{ __('Problem Solving & Analytical Thinking') }}
                            </label>
                            <span class="badge bg-label-warning font-12" id="val_prob">{{ old('problem_solving_score', 3) }} / 5</span>
                        </div>
                        <p class="text-muted font-11 mb-2">{{ __('Approaches challenges logically, thinks creatively, and handles real-life workplace scenarios.') }}</p>
                        <div class="d-flex gap-2 score-group">
                            @for ($i = 1; $i <= 5; $i++)
                                <input type="radio" class="btn-check score-radio" name="problem_solving_score" id="prob_{{ $i }}" value="{{ $i }}" autocomplete="off" {{ old('problem_solving_score', 3) == $i ? 'checked' : '' }} data-badge="val_prob">
                                <label class="btn btn-outline-warning flex-fill text-center py-2 rounded-2" for="prob_{{ $i }}">
                                    <span class="d-block fw-bold font-14">{{ $i }}</span>
                                </label>
                            @endfor
                        </div>
                    </div>

                    <!-- Criterion 4: Cultural Fit -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fw-bold text-heading mb-0 required">
                                4. {{ __('Cultural & Values Fit') }}
                            </label>
                            <span class="badge bg-label-success font-12" id="val_cult">{{ old('cultural_fit_score', 4) }} / 5</span>
                        </div>
                        <p class="text-muted font-11 mb-2">{{ __('Aligns with organization values, work ethic, and positive team collaboration.') }}</p>
                        <div class="d-flex gap-2 score-group">
                            @for ($i = 1; $i <= 5; $i++)
                                <input type="radio" class="btn-check score-radio" name="cultural_fit_score" id="cult_{{ $i }}" value="{{ $i }}" autocomplete="off" {{ old('cultural_fit_score', 4) == $i ? 'checked' : '' }} data-badge="val_cult">
                                <label class="btn btn-outline-success flex-fill text-center py-2 rounded-2" for="cult_{{ $i }}">
                                    <span class="d-block fw-bold font-14">{{ $i }}</span>
                                </label>
                            @endfor
                        </div>
                    </div>

                    <!-- Criterion 5: Experience Relevance -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fw-bold text-heading mb-0 required">
                                5. {{ __('Relevant Past Experience') }}
                            </label>
                            <span class="badge bg-label-dark font-12" id="val_exp">{{ old('experience_score', 3) }} / 5</span>
                        </div>
                        <p class="text-muted font-11 mb-2">{{ __('Relevance and depth of previous roles and measurable achievements.') }}</p>
                        <div class="d-flex gap-2 score-group">
                            @for ($i = 1; $i <= 5; $i++)
                                <input type="radio" class="btn-check score-radio" name="experience_score" id="exp_{{ $i }}" value="{{ $i }}" autocomplete="off" {{ old('experience_score', 3) == $i ? 'checked' : '' }} data-badge="val_exp">
                                <label class="btn btn-outline-secondary flex-fill text-center py-2 rounded-2" for="exp_{{ $i }}">
                                    <span class="d-block fw-bold font-14">{{ $i }}</span>
                                </label>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar: Final Recommendation & Notes -->
        <div class="col-12 col-lg-4">
            <!-- Live Calculated Score Banner -->
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-label-primary p-3 text-center" style="border: 1px solid rgba(105, 108, 255, 0.2) !important;">
                <small class="text-muted fw-bold d-block mb-1">{{ __('Calculated Average Score') }}</small>
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <h2 class="fw-bold mb-0 text-primary" id="live_overall_score">3.4</h2>
                    <span class="text-muted font-14">/ 5.0</span>
                </div>
                <div class="d-flex align-items-center justify-content-center gap-1 mt-1 text-warning fs-5" id="live_stars">
                    <i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bx-star text-muted"></i><i class="bx bx-star text-muted"></i>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3 mb-4" style="border: 1px solid #edf0f6 !important;">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 font-16 fw-bold d-flex align-items-center gap-2">
                        <i class="bx bx-award text-primary font-20"></i>
                        <span>{{ __('Final Recommendation') }}</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-heading small required">{{ __('Hiring Recommendation') }}</label>
                        <select name="recommendation" class="form-select" required>
                            @foreach ($recommendations as $rec)
                                <option value="{{ $rec->value }}" {{ old('recommendation', 'hire') === $rec->value ? 'selected' : '' }}>
                                    {{ $rec->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-heading small">{{ __('Key Strengths Observed') }}</label>
                        <textarea name="strengths" rows="3" class="form-control" placeholder="{{ __('What stood out positively?') }}">{{ old('strengths') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-heading small">{{ __('Areas of Concern / Weaknesses') }}</label>
                        <textarea name="weaknesses" rows="3" class="form-control" placeholder="{{ __('Any flags or points for further checking?') }}">{{ old('weaknesses') }}</textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold text-heading small">{{ __('Internal Feedback / Summary') }}</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="{{ __('General notes for hiring manager...') }}">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Action Bar (Save Button at Bottom) -->
    <div class="card border-0 shadow-sm rounded-3 mt-2" style="border: 1px solid #edf0f6 !important;">
        <div class="card-body p-3 d-flex align-items-center justify-content-between">
            <a href="{{ route('hr.interviews.show', $interview->id) }}" class="btn btn-outline-secondary px-4">
                {{ __('Cancel') }}
            </a>

            <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-2">
                <i class="bx bx-check-double font-18"></i>
                <span class="fw-semibold">{{ __('Submit Evaluation Scorecard') }}</span>
            </button>
        </div>
    </div>
</form>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const radios = document.querySelectorAll('.score-radio');

    function updateLiveScores() {
        let total = 0;
        let count = 0;

        const criteria = ['technical_score', 'communication_score', 'problem_solving_score', 'cultural_fit_score', 'experience_score'];
        criteria.forEach(function (name) {
            const checked = document.querySelector('input[name="' + name + '"]:checked');
            if (checked) {
                const val = parseInt(checked.value, 10);
                total += val;
                count++;
                const badgeId = checked.getAttribute('data-badge');
                if (badgeId) {
                    const badge = document.getElementById(badgeId);
                    if (badge) {
                        badge.textContent = val + ' / 5';
                    }
                }
            }
        });

        if (count > 0) {
            const avg = (total / count).toFixed(1);
            const scoreEl = document.getElementById('live_overall_score');
            if (scoreEl) {
                scoreEl.textContent = avg;
            }

            const starsEl = document.getElementById('live_stars');
            if (starsEl) {
                const rounded = Math.round(parseFloat(avg));
                let starsHtml = '';
                for (let i = 1; i <= 5; i++) {
                    starsHtml += i <= rounded
                        ? '<i class="bx bxs-star text-warning"></i>'
                        : '<i class="bx bx-star text-muted"></i>';
                }
                starsEl.innerHTML = starsHtml;
            }
        }
    }

    radios.forEach(function (radio) {
        radio.addEventListener('change', updateLiveScores);
    });

    updateLiveScores();
});
</script>
@endsection
