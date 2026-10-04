@extends('layouts/contentNavbarLayout')

@section('title', $task->title . ' - ' . __('Task Details'))

@section('page-style')
    <style>
        .timeline {
            position: relative;
            padding-left: 1.5rem;
        }
        [dir="rtl"] .timeline {
            padding-left: 0;
            padding-right: 1.5rem;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
            border-left: 2px solid #e7e7e8;
        }
        [dir="rtl"] .timeline-item {
            border-left: none;
            border-right: 2px solid #e7e7e8;
        }
        .timeline-item:last-child {
            border-color: transparent;
            padding-bottom: 0;
        }
        .timeline-point {
            position: absolute;
            left: -0.45rem;
            top: 0;
            width: 0.8rem;
            height: 0.8rem;
            border-radius: 50%;
            background-color: var(--bs-primary);
        }
        [dir="rtl"] .timeline-point {
            left: auto;
            right: -0.45rem;
        }
        .timeline-event {
            padding-left: 1rem;
        }
        [dir="rtl"] .timeline-event {
            padding-left: 0;
            padding-right: 1rem;
        }
        .note-card {
            transition: all 0.2s ease;
        }
        .note-card:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.02);
        }
    </style>
@endsection

@section('content')
    <!-- Header -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('tasks.tasks.index') }}">{{ __('Tasks') }}</a></li>
                    @if ($task->taskGroup)
                        <li class="breadcrumb-item"><a href="{{ route('tasks.task-groups.show', $task->taskGroup->id) }}">{{ $task->taskGroup->name }}</a></li>
                    @endif
                    <li class="breadcrumb-item active text-truncate" style="max-width: 250px;">{{ $task->title }}</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 d-flex align-items-center gap-2 text-heading">
                <i class="icon-base bx bx-task text-primary font-24"></i>
                <span>{{ $task->title }}</span>
            </h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if ($task->taskGroup)
                <a href="{{ route('tasks.task-groups.show', $task->taskGroup->id) }}" class="btn btn-outline-secondary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-folder"></i>
                    <span>{{ __('Group Tasks') }}</span>
                </a>
            @else
                <a href="{{ route('tasks.tasks.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-arrow-back"></i>
                    <span>{{ __('Back to Tasks') }}</span>
                </a>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Column (Description, Notes, Attachments) -->
        <div class="col-12 col-lg-8">
            <!-- Task Description Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                        <i class="icon-base bx bx-detail text-primary font-18"></i>
                        <span>{{ __('Task Description & Content') }}</span>
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        @php
                            $priority = $task->priority instanceof \Modules\Tasks\Enums\TaskPriority ? $task->priority : \Modules\Tasks\Enums\TaskPriority::tryFrom($task->priority);
                            $status = $task->task_status instanceof \Modules\Tasks\Enums\TaskStatus ? $task->task_status : \Modules\Tasks\Enums\TaskStatus::tryFrom($task->task_status);
                        @endphp
                        @if ($priority)
                            <span class="badge {{ $priority->badgeClass() }}">{{ $priority->label() }}</span>
                        @endif
                        @if ($status)
                            <span class="badge {{ $status->badgeClass() }}" id="taskStatusBadge">{{ $status->label() }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body pt-4">
                    <div class="mb-4">
                        <h6 class="fw-semibold text-heading mb-2">{{ __('Arabic Title & Body') }}</h6>
                        <h5 class="text-primary mb-2">{{ $task->translateOrNew('ar')->title ?: '-' }}</h5>
                        <p class="text-body mb-0" style="white-space: pre-line;">{{ $task->translateOrNew('ar')->body ?: __('No description in Arabic.') }}</p>
                    </div>

                    @if ($task->translateOrNew('en')->title || $task->translateOrNew('en')->body)
                        <hr class="my-4">
                        <div class="mb-2">
                            <h6 class="fw-semibold text-heading mb-2">{{ __('English Title & Body') }}</h6>
                            <h5 class="text-primary mb-2">{{ $task->translateOrNew('en')->title ?: '-' }}</h5>
                            <p class="text-body mb-0" style="white-space: pre-line;">{{ $task->translateOrNew('en')->body ?: __('No description in English.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Attachments Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header border-bottom py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                        <i class="icon-base bx bx-paperclip text-primary font-18"></i>
                        <span>{{ __('Attachments') }} ({{ $task->attachments->count() }})</span>
                    </h5>
                </div>
                <div class="card-body pt-3">
                    @if ($task->attachments->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="icon-base bx bx-file font-32 mb-2 d-block text-secondary"></i>
                            <p class="mb-0">{{ __('No attachments uploaded for this task.') }}</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush border rounded">
                            @foreach ($task->attachments as $attachment)
                                <div class="list-group-item d-flex align-items-center justify-content-between py-2 px-3">
                                    <div class="d-flex align-items-center gap-2 text-truncate">
                                        <i class="icon-base bx {{ $attachment->is_image ? 'bx-image' : ($attachment->is_pdf ? 'bxs-file-pdf' : 'bx-file') }} font-22 text-primary"></i>
                                        <div>
                                            <a href="{{ $attachment->url }}" target="_blank" class="fw-semibold text-heading d-block text-truncate">
                                                {{ $attachment->original_name }}
                                            </a>
                                            <small class="text-muted">{{ $attachment->formatted_size }}</small>
                                        </div>
                                    </div>
                                    <a href="{{ $attachment->url }}" download="{{ $attachment->original_name }}" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                                        <i class="icon-base bx bx-download"></i>
                                        <span>{{ __('Download') }}</span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Notes & Discussion Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                        <i class="icon-base bx bx-comment-detail text-primary font-18"></i>
                        <span>{{ __('Notes & Comments') }}</span>
                        <span class="badge bg-label-primary rounded-pill font-12" id="notesCount">{{ $task->notes->count() }}</span>
                    </h5>
                </div>
                <div class="card-body pt-3">
                    <!-- Notes Feed -->
                    <div class="mb-4" id="notesContainer">
                        @if ($task->notes->isEmpty())
                            <div class="text-center py-4 text-muted" id="noNotesPlaceholder">
                                <i class="icon-base bx bx-message-square-dots font-32 mb-2 d-block text-secondary"></i>
                                <p class="mb-0">{{ __('No comments yet. Start the conversation below.') }}</p>
                            </div>
                        @else
                            @foreach ($task->notes as $note)
                                <div class="card border mb-3 note-card shadow-none">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar avatar-sm">
                                                    <img src="{{ $note->user?->avatar_url }}" alt="avatar" class="rounded-circle">
                                                </div>
                                                <span class="fw-semibold text-heading font-14">{{ $note->author_name }}</span>
                                            </div>
                                            <small class="text-muted">{{ $note->created_at?->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-0 text-body font-14" style="white-space: pre-line;">{{ $note->note }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- Add Note Form -->
                    <form action="{{ route('tasks.tasks.notes.store', $task->id) }}" method="POST" id="addNoteForm" class="border-top pt-3">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="noteContent">{{ __('Add a Note or Comment') }}</label>
                            <textarea class="form-control" id="noteContent" name="note" rows="3" required placeholder="{{ __('Write your notes, feedback or updates regarding this task...') }}"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary d-flex align-items-center gap-1" id="btnSubmitNote">
                                <i class="icon-base bx bx-send"></i>
                                <span>{{ __('Post Note') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Details & Status History -->
        <div class="col-12 col-lg-4">
            <!-- Status Update Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header border-bottom py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                        <i class="icon-base bx bx-sync text-primary font-18"></i>
                        <span>{{ __('Quick Status Update') }}</span>
                    </h5>
                </div>
                <div class="card-body pt-3">
                    <form action="{{ route('tasks.tasks.update-status', $task->id) }}" method="POST" id="quickStatusForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="quick_status">{{ __('New Status') }}</label>
                            <select class="form-select" id="quick_status" name="status">
                                @foreach ($statuses as $st)
                                    <option value="{{ $st->value }}" {{ $task->task_status instanceof \Modules\Tasks\Enums\TaskStatus ? ($task->task_status === $st ? 'selected' : '') : ($task->task_status === $st->value ? 'selected' : '') }}>
                                        {{ $st->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="status_comment">{{ __('Reason / Comment') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                            <input type="text" class="form-control" id="status_comment" name="comment" placeholder="{{ __('e.g. Completed QA review') }}">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-1" id="btnUpdateQuickStatus">
                            <i class="icon-base bx bx-check font-18"></i>
                            <span>{{ __('Update Status') }}</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Task Meta Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header border-bottom py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                        <i class="icon-base bx bx-info-circle text-primary font-18"></i>
                        <span>{{ __('Task Meta') }}</span>
                    </h5>
                </div>
                <div class="card-body pt-3">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted">{{ __('Task Group') }}</span>
                            <span class="fw-semibold">
                                @if ($task->taskGroup)
                                    <a href="{{ route('tasks.task-groups.show', $task->taskGroup->id) }}" class="text-primary">{{ $task->taskGroup->name }}</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted">{{ __('Assigned Employee') }}</span>
                            <span class="fw-semibold">{{ $task->employee?->name ?: __('Unassigned') }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted">{{ __('Created By') }}</span>
                            <span class="fw-semibold">{{ $task->creator?->name ?: '-' }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted">{{ __('Start Date') }}</span>
                            <span class="fw-semibold">{{ $task->start_date?->format('Y-m-d') ?: '-' }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted">{{ __('Due Date & Remaining') }}</span>
                            <div class="text-end">
                                <span class="fw-semibold d-block {{ $task->completion_date && $task->completion_date->isPast() && $task->task_status !== \Modules\Tasks\Enums\TaskStatus::COMPLETED ? 'text-danger' : '' }}">
                                    {{ $task->completion_date?->format('Y-m-d') ?: '-' }}
                                </span>
                                {!! $task->remaining_days_badge !!}
                            </div>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2">
                            <span class="text-muted">{{ __('Completed At') }}</span>
                            <span class="fw-semibold">{{ $task->completed_at?->format('Y-m-d H:i') ?: '-' }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Task Status History Card (سجل تتبع الحالات) -->
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                        <i class="icon-base bx bx-history text-primary font-18"></i>
                        <span>{{ __('Status History') }}</span>
                    </h5>
                </div>
                <div class="card-body pt-3">
                    @if ($task->statusHistories->isEmpty())
                        <p class="text-muted text-center py-3 mb-0">{{ __('No history records yet.') }}</p>
                    @else
                        <div class="timeline" id="statusHistoryTimeline">
                            @foreach ($task->statusHistories as $history)
                                <div class="timeline-item">
                                    <span class="timeline-point"></span>
                                    <div class="timeline-event">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div>
                                                @if ($history->old_status)
                                                    <span class="badge {{ $history->old_status->badgeClass() }} font-11">{{ $history->old_status->label() }}</span>
                                                    <i class="icon-base bx bx-right-arrow-alt text-muted mx-1"></i>
                                                @endif
                                                <span class="badge {{ $history->new_status->badgeClass() }} font-11">{{ $history->new_status->label() }}</span>
                                            </div>
                                            <small class="text-muted font-11">{{ $history->created_at?->diffForHumans() }}</small>
                                        </div>
                                        <div class="small text-muted mb-1">
                                            <i class="icon-base bx bx-user font-12"></i>
                                            <span>{{ $history->changer_name }}</span>
                                        </div>
                                        @if ($history->comment)
                                            <p class="small text-body mb-0 bg-lighter p-1 rounded font-12">{{ $history->comment }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const $ = window.jQuery || window.$;
            if (!$) return;

            // CSRF Setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Handle Quick Status Update Form
            $('#quickStatusForm').on('submit', function (e) {
                e.preventDefault();
                const $form = $(this);
                const $btn = $('#btnUpdateQuickStatus');

                $btn.prop('disabled', true).prepend('<span class="spinner-border spinner-border-sm me-1" role="status"></span>');

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    success: function (res) {
                        $btn.prop('disabled', false).find('.spinner-border').remove();
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __('Success!') }}',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false,
                                customClass: { confirmButton: 'btn btn-primary' }
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).find('.spinner-border').remove();
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Error!') }}',
                            text: xhr.responseJSON?.message || '{{ __('Failed to update status.') }}',
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                    }
                });
            });

            // Handle Add Note Form Submission
            $('#addNoteForm').on('submit', function (e) {
                e.preventDefault();
                const form = this;
                const $form = $(form);
                const $btn = $('#btnSubmitNote');
                const noteVal = $('#noteContent').val().trim();

                if (!noteVal) return;

                $btn.prop('disabled', true).prepend('<span class="spinner-border spinner-border-sm me-1" role="status"></span>');

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    success: function (response) {
                        $btn.prop('disabled', false).find('.spinner-border').remove();
                        if (response.success) {
                            $('#noNotesPlaceholder').remove();
                            form.reset();

                            const data = response.data;
                            const html = `
                                <div class="card border mb-3 note-card shadow-none animate__animated animate__fadeIn">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar avatar-sm">
                                                    <img src="${data.avatar_url}" alt="avatar" class="rounded-circle">
                                                </div>
                                                <span class="fw-semibold text-heading font-14">${data.author_name}</span>
                                            </div>
                                            <small class="text-muted">${data.created_at}</small>
                                        </div>
                                        <p class="mb-0 text-body font-14" style="white-space: pre-line;">${data.note}</p>
                                    </div>
                                </div>
                            `;

                            $('#notesContainer').prepend(html);

                            const $countBadge = $('#notesCount');
                            $countBadge.text(parseInt($countBadge.text() || '0') + 1);

                            Swal.fire({
                                icon: 'success',
                                title: '{{ __('Success!') }}',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false,
                                customClass: { confirmButton: 'btn btn-primary' }
                            });
                        }
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).find('.spinner-border').remove();
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('Error!') }}',
                            text: xhr.responseJSON?.message || '{{ __('Failed to add note.') }}',
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                    }
                });
            });
        });
    </script>
@endsection
