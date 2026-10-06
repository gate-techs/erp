@extends('layouts/contentNavbarLayout')

@section('title', __('Subjects & Stages Board') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
.board-container {
    display: flex;
    gap: 0.875rem;
    overflow-x: auto;
    padding-bottom: 1.25rem;
    min-height: calc(100vh - 270px);
}

.board-column {
    min-width: 240px;
    max-width: 260px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    background-color: #f8f9fa;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
}

.board-column.drag-over {
    background-color: #e0f2fe !important;
    border: 2px dashed #0284c7 !important;
}

.board-column-header {
    padding: 0.75rem 0.875rem;
    border-bottom: 1px solid #e2e8f0;
    background-color: #ffffff;
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
}

.board-column-body {
    padding: 0.625rem;
    flex-grow: 1;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    min-height: 220px;
}

.subject-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
    padding: 0.625rem 0.75rem;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    cursor: grab;
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    user-select: none;
}

.subject-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 3px 8px rgba(0,0,0,0.06);
    border-color: #696cff;
}

.subject-card.is-dragging {
    opacity: 0.4;
    cursor: grabbing;
    transform: scale(0.96);
}

.subject-avatar {
    width: 34px;
    height: 34px;
    border-radius: 0.25rem;
    object-fit: cover;
    flex-shrink: 0;
}

.subject-avatar-placeholder {
    width: 34px;
    height: 34px;
    border-radius: 0.25rem;
    background: rgba(105, 108, 255, 0.1);
    color: #696cff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.search-box-input {
    border-radius: 0.375rem;
    padding-left: 2rem;
    font-size: 0.85rem;
}

.search-box-wrapper {
    position: relative;
}

.search-box-wrapper i {
    position: absolute;
    left: 0.65rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

[dir="rtl"] .search-box-input {
    padding-left: 0.65rem;
    padding-right: 2rem;
}

[dir="rtl"] .search-box-wrapper i {
    left: auto;
    right: 0.65rem;
}
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Academic Management') }}</li>
                <li class="breadcrumb-item"><a href="{{ route('academic.subjects.index') }}" class="text-muted fw-medium small">{{ __('Academic Subjects') }}</a></li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Stages Board (Drag & Drop)') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-grid-alt text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Subjects & Stages Board') }}</h4>
            <span class="badge bg-label-primary rounded-pill ms-2">{{ __('Drag & Drop') }}</span>
        </div>
    </div>

    <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
        <button type="button" id="btn_refresh_board" class="btn btn-label-primary btn-sm d-flex align-items-center gap-1 shadow-sm" title="{{ __('Refresh Board') }}">
            <i class="icon-base bx bx-refresh font-18"></i>
            <span>{{ __('Refresh') }}</span>
        </button>
        <a href="{{ route('academic.subjects.index') }}" class="btn btn-label-secondary btn-sm d-flex align-items-center gap-2 shadow-sm">
            <i class="icon-base bx bx-table font-18"></i>
            <span>{{ __('Table View') }}</span>
        </a>
    </div>
</div>

<!-- Filters Bar -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('academic.subjects.board') }}" id="boardFilterForm">
            <div class="row g-3 align-items-end">
                <!-- 1. Institution Filter -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <label class="form-label fw-semibold text-heading small mb-1">{{ __('Educational Institution / Nursery') }}</label>
                    <select name="institution_id" id="board_institution_id" class="form-select select2" onchange="document.getElementById('boardFilterForm').submit();">
                        @foreach ($institutions as $inst)
                            <option value="{{ $inst->id }}" {{ $selectedInstitutionId == $inst->id ? 'selected' : '' }}>
                                {{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Academic Year Filter -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <label class="form-label fw-semibold text-heading small mb-1">{{ __('Academic Year / Term') }}</label>
                    <select name="academic_year_id" id="board_academic_year_id" class="form-select select2" onchange="document.getElementById('boardFilterForm').submit();">
                        <option value="">{{ __('All Academic Years') }}</option>
                        @foreach ($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                                {{ $year->translate(app()->getLocale())?->name ?? $year->translate('ar')?->name ?? $year->name ?? $year->code }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Quick Search in Cards -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <label class="form-label fw-semibold text-heading small mb-1">{{ __('Quick Search') }}</label>
                    <div class="search-box-wrapper">
                        <i class="icon-base bx bx-search"></i>
                        <input type="text" id="quick_card_search" class="form-control search-box-input" placeholder="{{ __('Search subjects...') }}">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Alert Box for Ajax feedback -->
<div id="ajaxAlertContainer"></div>

<!-- Instructions Banner -->
<div class="alert alert-primary d-flex align-items-center mb-4 border-0 shadow-sm" role="alert">
    <i class="icon-base bx bx-info-circle me-2 font-22"></i>
    <div class="small">
        <strong>{{ __('Tip:') }}</strong> {{ __('Drag any subject card and drop it into a stage column to assign it instantly. You can also drag from one stage column to another or remove it.') }}
    </div>
</div>

<!-- Master Subjects Library (Horizontal Bar / Deck) -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-label-primary py-2 px-3 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="icon-base bx bx-book-open text-primary font-20"></i>
            <h6 class="mb-0 fw-bold text-primary">{{ __('All Subjects Library (Drag from here)') }}</h6>
        </div>
        <span class="badge bg-primary rounded-pill">{{ $subjects->count() }} {{ __('subjects') }}</span>
    </div>
    <div class="card-body p-3">
        <div class="d-flex gap-2 overflow-x-auto pb-2" id="master_subjects_list" style="min-height: 75px;">
            @forelse ($subjects as $subject)
                @php
                    $subjectName = $subject->translate(app()->getLocale())?->name ?? $subject->translate('ar')?->name ?? $subject->translate('en')?->name ?? '-';
                    $stagesCount = $subject->academicStages->count();
                @endphp
                <div class="subject-card flex-shrink-0" draggable="true" data-subject-id="{{ $subject->id }}" data-subject-name="{{ strtolower($subjectName) }}" style="width: 210px;">
                    <div class="d-flex align-items-center gap-2">
                        @if ($subject->image_url)
                            <img src="{{ $subject->image_url }}" alt="{{ $subjectName }}" class="subject-avatar border">
                        @else
                            <div class="subject-avatar-placeholder">
                                <i class="icon-base bx bx-book"></i>
                            </div>
                        @endif
                        <div class="flex-grow-1 overflow-hidden">
                            <h6 class="fw-bold mb-0 text-heading text-truncate fs-7" title="{{ $subjectName }}">{{ $subjectName }}</h6>
                            <div class="d-flex align-items-center gap-2 text-muted small mt-1">
                                <span><i class="icon-base bx bx-layer font-12 me-1"></i>{{ $stagesCount }} {{ __('stages') }}</span>
                                @if($subject->attachments->count() > 0)
                                    <span><i class="icon-base bx bx-paperclip font-12 me-1"></i>{{ $subject->attachments->count() }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="w-100 text-center py-3 text-muted small">
                    <i class="icon-base bx bx-info-circle font-20 me-1 text-secondary"></i>
                    {{ __('No subjects found for the selected filter.') }}
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Board Stages Columns (Horizontal Scrollable Grid) -->
<div class="board-container" id="boardContainer">
    <!-- Dynamic Columns for each Stage -->
    @foreach ($stages as $stage)
        @php
            $stageName = $stage->translate(app()->getLocale())?->name ?? $stage->translate('ar')?->name ?? $stage->name ?? $stage->code;
            $stageSubjects = $subjects->filter(fn($s) => $s->academicStages->contains('id', $stage->id));
        @endphp
        <div class="board-column" data-stage-id="{{ $stage->id }}" data-column-type="stage">
            <div class="board-column-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <span class="badge badge-dot bg-primary flex-shrink-0"></span>
                    <h6 class="mb-0 fw-bold text-heading text-truncate" title="{{ $stageName }}">{{ $stageName }}</h6>
                </div>
                <span class="badge bg-label-secondary rounded-pill stage-count" id="count_stage_{{ $stage->id }}">{{ $stageSubjects->count() }}</span>
            </div>
            
            <div class="board-column-body stage-drop-zone" id="stage_zone_{{ $stage->id }}" data-stage-id="{{ $stage->id }}">
                @forelse ($stageSubjects as $subject)
                    @php
                        $subjectName = $subject->translate(app()->getLocale())?->name ?? $subject->translate('ar')?->name ?? $subject->translate('en')?->name ?? '-';
                    @endphp
                    <div class="subject-card position-relative" draggable="true" data-subject-id="{{ $subject->id }}" data-stage-id="{{ $stage->id }}" data-subject-name="{{ strtolower($subjectName) }}">
                        <div class="d-flex align-items-center gap-2">
                            @if ($subject->image_url)
                                <img src="{{ $subject->image_url }}" alt="{{ $subjectName }}" class="subject-avatar border">
                            @else
                                <div class="subject-avatar-placeholder">
                                    <i class="icon-base bx bx-book"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1 overflow-hidden">
                                <h6 class="fw-bold mb-0 text-heading text-truncate fs-7" title="{{ $subjectName }}">{{ $subjectName }}</h6>
                                <div class="d-flex align-items-center gap-2 text-muted small mt-1">
                                    @if($subject->attachments->count() > 0)
                                        <span><i class="icon-base bx bx-paperclip font-12 me-1"></i>{{ $subject->attachments->count() }}</span>
                                    @endif
                                </div>
                            </div>
                            <button type="button" class="btn btn-icon btn-xs btn-label-danger btn-remove-from-stage" title="{{ __('Remove from this stage') }}" data-subject-id="{{ $subject->id }}" data-stage-id="{{ $stage->id }}">
                                <i class="icon-base bx bx-x"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="empty-placeholder text-center py-4 text-muted small">
                        <i class="icon-base bx bx-plus-circle font-24 mb-1 d-block text-muted opacity-50"></i>
                        {{ __('Drop subjects here') }}
                    </div>
                @endforelse
            </div>
        </div>
    @endforeach
</div>
@endsection

@section('page-script')
<script>
(function() {
    function initBoard() {
        const syncUrl = "{{ route('academic.subjects.sync-stage') }}";

        // Setup CSRF header for jQuery AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        function showAlert(type, message) {
            const icon = type === 'success' ? 'bx-check-circle' : 'bx-error-circle';
            const html = `
                <div class="alert alert-${type} alert-dismissible d-flex align-items-center mb-4 fade show shadow-sm" role="alert">
                    <i class="icon-base bx ${icon} me-2 font-20"></i>
                    <div>${message}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            $('#ajaxAlertContainer').html(html);
            setTimeout(() => {
                $('#ajaxAlertContainer .alert').alert('close');
            }, 4000);
        }

        // Search Filter inside cards
        $('#quick_card_search').on('input', function() {
            const term = $(this).val().toLowerCase().trim();
            $('.subject-card').each(function() {
                const name = $(this).data('subject-name') || '';
                if (name.includes(term) || term === '') {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // HTML5 Drag and Drop Implementation
        let draggedCard = null;
        let draggedSubjectId = null;

        $(document).on('dragstart', '.subject-card', function(e) {
            draggedCard = $(this);
            draggedSubjectId = $(this).data('subject-id');
            $(this).addClass('is-dragging');
            e.originalEvent.dataTransfer.setData('text/plain', draggedSubjectId);
            e.originalEvent.dataTransfer.effectAllowed = 'copyMove';
        });

        $(document).on('dragend', '.subject-card', function() {
            $('.subject-card').removeClass('is-dragging');
            $('.board-column').removeClass('drag-over');
            draggedCard = null;
            draggedSubjectId = null;
        });

        // Drag Over Column
        $('.stage-drop-zone').on('dragover', function(e) {
            e.preventDefault();
            e.originalEvent.dataTransfer.dropEffect = 'copy';
            $(this).closest('.board-column').addClass('drag-over');
        });

        $('.stage-drop-zone').on('dragleave', function() {
            $(this).closest('.board-column').removeClass('drag-over');
        });

        // Refresh Button click
        $('#btn_refresh_board').on('click', function() {
            window.location.reload();
        });

        // Drop in Stage Column
        $('.stage-drop-zone').on('drop', function(e) {
            e.preventDefault();
            const zone = $(this);
            const stageId = zone.data('stage-id');
            const column = zone.closest('.board-column');
            column.removeClass('drag-over');

            if (!draggedSubjectId || !stageId) return;

            // Check if already in this stage
            if (zone.find(`[data-subject-id="${draggedSubjectId}"]`).length > 0) {
                showAlert('info', '{{ __("Subject is already assigned to this stage.") }}');
                return;
            }

            // Perform AJAX Sync
            $.ajax({
                url: syncUrl,
                type: 'POST',
                data: {
                    subject_id: draggedSubjectId,
                    stage_id: stageId,
                    action: 'attach'
                },
                success: function(res) {
                    showAlert('success', res.message);
                    // Fast auto-refresh to update all counters and positions cleanly
                    setTimeout(() => {
                        window.location.reload();
                    }, 400);
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || '{{ __("Failed to assign subject to stage.") }}';
                    showAlert('danger', msg);
                }
            });
        });

        // Remove from stage action
        $(document).on('click', '.btn-remove-from-stage', function() {
            const btn = $(this);
            const subjectId = btn.data('subject-id');
            const stageId = btn.data('stage-id');
            const card = btn.closest('.subject-card');

            btn.prop('disabled', true);

            $.ajax({
                url: syncUrl,
                type: 'POST',
                data: {
                    subject_id: subjectId,
                    stage_id: stageId,
                    action: 'detach'
                },
                success: function(res) {
                    showAlert('success', res.message);
                    card.fadeOut(200, function() {
                        setTimeout(() => {
                            window.location.reload();
                        }, 200);
                    });
                },
                error: function(xhr) {
                    btn.prop('disabled', false);
                    const msg = xhr.responseJSON?.message || '{{ __("Failed to remove subject from stage.") }}';
                    showAlert('danger', msg);
                }
            });
        });
    }

    if (document.readyState === 'complete') {
        initBoard();
    } else {
        window.addEventListener('load', initBoard);
    }
})();
</script>
@endsection
