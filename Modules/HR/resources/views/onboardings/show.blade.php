@extends('layouts/contentNavbarLayout')

@section('title', __('Onboarding Journey') . ' ' . $onboarding->onboarding_code . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
.task-item {
    transition: all 0.2s ease-in-out;
    border-radius: 8px;
    padding: 12px 16px;
    background: #fff;
    border: 1px solid #edf0f6;
    margin-bottom: 8px;
}
.task-item:hover {
    background: #f8f9fc;
    border-color: #dbe2ef;
}
.task-item.completed {
    background: #f6fdf9;
    border-color: #bbf7d0;
}
.task-item.completed .task-title {
    text-decoration: line-through;
    color: #8592a3;
}
</style>
@endsection

@section('content')
<!-- Action Bar & Breadcrumbs -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="fw-bold mb-0">{{ $onboarding->onboarding_code }}</h4>
            <span class="badge {{ $onboarding->status->badgeClass() }} rounded-pill px-3 py-1 font-12" id="headerStatusBadge">
                <i class="{{ $onboarding->status->icon() }} me-1"></i><span id="headerStatusText">{{ $onboarding->status->label() }}</span>
            </span>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.onboardings.index') }}" class="text-muted">{{ __('Onboarding') }}</a></li>
                <li class="breadcrumb-item active text-primary">{{ $onboarding->onboarding_code }}</li>
            </ol>
        </nav>
    </div>

    <!-- Actions -->
    <div class="d-flex flex-wrap align-items-center gap-2">
        @can('hr.onboardings.tasks')
        <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-1 shadow-sm px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#addTaskModal">
            <i class="bx bx-plus-circle font-18"></i>
            <span class="fw-semibold">{{ __('Add Task') }}</span>
        </button>
        @endcan

        @if($onboarding->employee_id && $onboarding->employee)
            @can('hr.employees.view')
            <a href="{{ route('hr.employees.show', $onboarding->employee_id) }}" class="btn btn-success d-flex align-items-center gap-1 shadow-sm px-3 py-2 rounded-3">
                <i class="bx bx-user-check font-18"></i>
                <span class="fw-semibold">{{ __('View Employee Profile') }} ({{ $onboarding->employee->employee_code }})</span>
            </a>
            @endcan
        @else
            @can('hr.onboardings.convert')
            <button type="button" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-3 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#convertEmployeeModal">
                <i class="bx bx-user-plus font-18"></i>
                <span class="fw-semibold">{{ __('Convert to Official Employee') }}</span>
            </button>
            @endcan
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Tasks Checklist by Category -->
    <div class="col-12 col-lg-8">
        <!-- Progress Summary Bar -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <h6 class="fw-bold mb-0 text-heading">{{ __('Onboarding Checklist Completion') }}</h6>
                        <small class="text-muted" id="taskCountText">
                            {{ $onboarding->tasks->where('is_completed', true)->count() }} {{ __('of') }} {{ $onboarding->tasks->count() }} {{ __('tasks completed') }}
                        </small>
                    </div>
                    <h3 class="fw-bolder text-primary mb-0" id="progressPercentageText">{{ $onboarding->progress_percentage }}%</h3>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-primary" id="progressBar" role="progressbar" style="width: {{ $onboarding->progress_percentage }}%;" aria-valuenow="{{ $onboarding->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>

        <!-- Tasks Grouped by Category -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                    <i class="bx bx-check-square text-primary font-20"></i>
                    <span>{{ __('Onboarding Checklist & Milestones') }}</span>
                </h5>
                <small class="text-muted">{{ __('Check items to mark completed in real-time') }}</small>
            </div>
            <div class="card-body p-4">
                @foreach($taskCategories as $cat)
                    @php
                        $catTasks = $onboarding->tasks->where('category', $cat);
                    @endphp
                    @if($catTasks->isNotEmpty())
                        <div class="mb-4">
                            <h6 class="fw-bold d-flex align-items-center gap-2 mb-2 text-secondary font-13 text-uppercase">
                                <i class="{{ $cat->icon() }} font-16 text-primary"></i>
                                <span>{{ $cat->label() }}</span>
                                <span class="badge bg-label-secondary font-10 px-2 py-0.5 rounded-pill">{{ $catTasks->count() }}</span>
                            </h6>

                            @foreach($catTasks as $task)
                                <div class="task-item d-flex align-items-center justify-content-between {{ $task->is_completed ? 'completed' : '' }}" id="task-row-{{ $task->id }}">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="form-check m-0">
                                            <input class="form-check-input task-toggle-checkbox" type="checkbox" data-id="{{ $task->id }}" {{ $task->is_completed ? 'checked' : '' }} style="cursor: pointer; transform: scale(1.15);">
                                        </div>
                                        <div>
                                            <span class="fw-semibold text-heading font-14 task-title d-block">{{ $task->title }}</span>
                                            @if($task->notes)
                                                <small class="text-muted font-11 d-block">{{ $task->notes }}</small>
                                            @endif
                                            @if($task->assignee)
                                                <small class="badge bg-label-secondary font-10 px-1.5 py-0 mt-0.5">
                                                    <i class="bx bx-user me-1"></i>{{ $task->assignee->translate(app()->getLocale())?->name ?? $task->assignee->name }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-end font-11 text-muted" id="task-time-{{ $task->id }}">
                                        @if($task->is_completed && $task->completed_at)
                                            <span class="text-success"><i class="bx bx-check me-0.5"></i>{{ $task->completed_at->format('M d, H:i') }}</span>
                                        @else
                                            <span>{{ __('Pending') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Right Column: Candidate & Position Dossier -->
    <div class="col-12 col-lg-4">
        <!-- Candidate Card -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                    <i class="bx bx-user text-primary font-20"></i>
                    <span>{{ __('New Joiner Profile') }}</span>
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="avatar avatar-xl bg-label-primary rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 70px;">
                        <span class="fw-bold font-24">{{ $onboarding->candidate?->initials }}</span>
                    </div>
                    <h5 class="fw-bold text-heading mb-0">{{ $onboarding->candidate?->full_name }}</h5>
                    <p class="text-muted font-13 mb-0">{{ $onboarding->candidate?->email }}</p>
                    <small class="text-muted">{{ $onboarding->candidate?->phone }}</small>
                </div>

                <ul class="list-group list-group-flush font-13">
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">{{ __('Department') }}</span>
                        <span class="fw-semibold text-heading">{{ $onboarding->department?->translate(app()->getLocale())?->name ?? '-' }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">{{ __('Designation') }}</span>
                        <span class="fw-semibold text-heading">{{ $onboarding->jobTitle?->translate(app()->getLocale())?->title ?? '-' }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">{{ __('Joining Date') }}</span>
                        <span class="fw-bold text-primary">{{ $onboarding->start_date->format('Y-m-d') }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">{{ __('Assigned Buddy') }}</span>
                        <span class="fw-semibold text-heading">{{ $onboarding->buddy?->translate(app()->getLocale())?->name ?? __('Not Assigned') }}</span>
                    </li>
                    @if($onboarding->jobOffer)
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">{{ __('Linked Offer') }}</span>
                            <a href="{{ route('hr.job-offers.show', $onboarding->jobOffer->id) }}" class="fw-semibold text-primary">
                                {{ $onboarding->jobOffer->offer_code }}
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Conversion Status Card -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4 text-center">
                @if($onboarding->employee_id && $onboarding->employee)
                    <div class="avatar avatar-md bg-label-success rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-check-double font-24"></i>
                    </div>
                    <h6 class="fw-bold text-success mb-1">{{ __('Staff Member Active') }}</h6>
                    <p class="text-muted font-12 mb-3">{{ __('Employee record has been created with code') }}: <strong>{{ $onboarding->employee->employee_code }}</strong></p>
                    <a href="{{ route('hr.employees.show', $onboarding->employee_id) }}" class="btn btn-outline-success w-100 py-2">
                        <i class="bx bx-id-card me-1"></i>{{ __('Open Employee File') }}
                    </a>
                @else
                    <div class="avatar avatar-md bg-label-warning rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                        <i class="bx bx-user-plus font-24"></i>
                    </div>
                    <h6 class="fw-bold text-heading mb-1">{{ __('Ready for Official Conversion') }}</h6>
                    <p class="text-muted font-12 mb-3">{{ __('Click below to migrate candidate into an official employee profile with active staff ID.') }}</p>
                    <button type="button" class="btn btn-primary w-100 py-2" data-bs-toggle="modal" data-bs-target="#convertEmployeeModal">
                        <i class="bx bx-user-check me-1"></i>{{ __('Convert to Employee Now') }}
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Custom Task -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="addTaskForm" action="{{ route('hr.onboardings.tasks.store', $onboarding->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">{{ __('Add Onboarding Task') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="task_title">{{ __('Task Title') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="task_title" class="form-control" placeholder="{{ __('e.g. Issue Security Access Card') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="task_category">{{ __('Category') }} <span class="text-danger">*</span></label>
                        <select name="category" id="task_category" class="form-select" required>
                            @foreach($taskCategories as $cat)
                                <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="task_assigned_to">{{ __('Assignee (Optional)') }}</label>
                        <select name="assigned_to" id="task_assigned_to" class="form-select">
                            <option value="">{{ __('Unassigned') }}</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->translate(app()->getLocale())?->name ?? $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label fw-semibold" for="task_notes">{{ __('Instructions / Notes') }}</label>
                        <textarea name="notes" id="task_notes" rows="2" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Add Task') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Convert Candidate to Official Employee -->
<div class="modal fade" id="convertEmployeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="convertEmployeeForm" action="{{ route('hr.onboardings.convert', $onboarding->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i class="bx bx-user-check text-success font-22"></i>
                        <span>{{ __('Convert Candidate to Official Employee') }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info border-0 p-3 rounded-3 font-13 mb-4">
                        <i class="bx bx-info-circle me-1"></i>
                        {{ __('This action will create an active employee profile, transfer job compensation and assignment, and mark the hiring pipeline as HIRED.') }}
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="conv_name_ar">{{ __('Full Name (Arabic)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name_ar" id="conv_name_ar" class="form-control" value="{{ $onboarding->candidate?->full_name }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="conv_name_en">{{ __('Full Name (English)') }}</label>
                            <input type="text" name="name_en" id="conv_name_en" class="form-control" value="{{ $onboarding->candidate?->full_name }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="conv_employee_code">{{ __('Employee Code (Auto-generated if empty)') }}</label>
                            <input type="text" name="employee_code" id="conv_employee_code" class="form-control" placeholder="EMP-00XX">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="conv_department_id">{{ __('Department') }} <span class="text-danger">*</span></label>
                            <select name="department_id" id="conv_department_id" class="form-select" required>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ $onboarding->department_id == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->translate(app()->getLocale())?->name ?? $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="conv_job_title_id">{{ __('Job Title') }} <span class="text-danger">*</span></label>
                            <select name="job_title_id" id="conv_job_title_id" class="form-select" required>
                                @foreach($jobTitles as $title)
                                    <option value="{{ $title->id }}" {{ $onboarding->job_title_id == $title->id ? 'selected' : '' }}>
                                        {{ $title->translate(app()->getLocale())?->title ?? $title->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="conv_basic_salary">{{ __('Basic Salary') }} ({{ \Modules\Settings\Models\Setting::instance()->currency ?? 'KWD' }}) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="basic_salary" id="conv_basic_salary" class="form-control fw-bold" value="{{ $onboarding->jobOffer?->basic_salary ?? 0 }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="conv_hire_date">{{ __('Official Hire Date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="hire_date" id="conv_hire_date" class="form-control js-flatpickr-date" value="{{ $onboarding->start_date->format('Y-m-d') }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="conv_employment_type">{{ __('Employment Type') }} <span class="text-danger">*</span></label>
                            <select name="employment_type" id="conv_employment_type" class="form-select" required>
                                @foreach($employmentTypes as $type)
                                    <option value="{{ $type->value }}" {{ ($onboarding->jobOffer?->employment_type == $type->value) ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="conv_gender">{{ __('Gender') }} <span class="text-danger">*</span></label>
                            <select name="gender" id="conv_gender" class="form-select" required>
                                @foreach($genders as $gender)
                                    <option value="{{ $gender->value }}">{{ $gender->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="conv_national_id">{{ __('National ID / Iqama') }}</label>
                            <input type="text" name="national_id" id="conv_national_id" class="form-control">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="conv_email">{{ __('Email') }}</label>
                            <input type="email" name="email" id="conv_email" class="form-control" value="{{ $onboarding->candidate?->email }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold" for="conv_phone">{{ __('Phone') }}</label>
                            <input type="text" name="phone" id="conv_phone" class="form-control js-phone-input" value="{{ $onboarding->candidate?->phone }}" dir="ltr">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success fw-bold px-4" id="btnSubmitConvert">
                        <i class="bx bx-check-circle me-1"></i>{{ __('Confirm & Create Employee Profile') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
$(function () {
    // Check if URL has #convert hash
    if (window.location.hash === '#convert') {
        const convertModal = new bootstrap.Modal(document.getElementById('convertEmployeeModal'));
        convertModal.show();
    }

    // Toggle Task Checkbox via AJAX
    $('.task-toggle-checkbox').on('change', function () {
        const checkbox = $(this);
        const taskId = checkbox.data('id');
        const row = $('#task-row-' + taskId);
        const timeCell = $('#task-time-' + taskId);

        $.post("{{ url('hr/onboardings/tasks') }}/" + taskId + "/toggle", {
            _token: '{{ csrf_token() }}'
        }, function (response) {
            if (response.success) {
                const data = response.data;
                if (data.is_completed) {
                    row.addClass('completed');
                    timeCell.html('<span class="text-success"><i class="bx bx-check me-0.5"></i>' + data.completed_at + '</span>');
                } else {
                    row.removeClass('completed');
                    timeCell.html('<span>{{ __("Pending") }}</span>');
                }

                // Update Progress Bar
                $('#progressPercentageText').text(data.progress + '%');
                $('#progressBar').css('width', data.progress + '%').attr('aria-valuenow', data.progress);
                $('#headerStatusText').text(data.status_label);
                $('#headerStatusBadge').attr('class', 'badge ' + data.status_badge + ' rounded-pill px-3 py-1 font-12');
            }
        }).fail(function () {
            checkbox.prop('checked', !checkbox.prop('checked'));
            Swal.fire({
                title: "{{ __('Error') }}",
                text: "{{ __('Could not update task state!') }}",
                icon: 'error'
            });
        });
    });

    // Add Task Form submission via AJAX
    $('#addTaskForm').on('submit', function (e) {
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

    // Convert Candidate to Employee Form submission
    $('#convertEmployeeForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const btn = $('#btnSubmitConvert');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> {{ __("Creating...") }}');

        $.post(form.attr('action'), form.serialize(), function (response) {
            if (response.success) {
                Swal.fire({
                    title: "{{ __('Congratulations!') }}",
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: "{{ __('Go to Employee Profile') }}",
                    customClass: { confirmButton: 'btn btn-success' }
                }).then(() => {
                    window.location.href = response.redirect;
                });
            }
        }).fail(function (xhr) {
            btn.prop('disabled', false).html('<i class="bx bx-check-circle me-1"></i> {{ __("Confirm & Create Employee Profile") }}');
            Swal.fire({
                title: "{{ __('Error') }}",
                text: xhr.responseJSON ? xhr.responseJSON.message : "{{ __('Could not convert candidate!') }}",
                icon: 'error'
            });
        });
    });
});
</script>
@endsection
