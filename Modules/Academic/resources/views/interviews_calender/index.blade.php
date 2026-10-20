@extends('layouts/contentNavbarLayout')

@section('title', __('Interviews Calendar') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
    /* Color picker radio */
    .color-radio {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: inline-block;
        cursor: pointer;
        position: relative;
        border: 2px solid transparent;
        transition: transform 0.15s ease;
    }
    .color-radio:hover {
        transform: scale(1.15);
    }
    .color-radio.selected {
        border-color: #0b3252;
        box-shadow: 0 0 0 2px rgba(11, 50, 82, 0.35);
    }

    /* Interview Sidebar Styles */
    .interview-sidebar {
        background-color: #ffffff;
        min-height: 680px;
        display: flex;
        flex-direction: column;
        border-inline-start: 1px solid #ebeef2;
    }

    .interview-cal-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background-color: #eaf0f6;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
    }

    .interview-tabs-nav {
        display: flex;
        border-bottom: 1px solid #ebeef2;
        padding: 0 1.25rem;
        gap: 1.5rem;
    }

    .interview-tab-btn {
        background: transparent;
        border: none;
        padding: 0.85rem 0.25rem;
        font-size: 0.92rem;
        font-weight: 500;
        color: #7e8c9d;
        cursor: pointer;
        position: relative;
        border-bottom: 3px solid transparent;
        transition: all 0.2s ease;
    }

    .interview-tab-btn:hover {
        color: #0b3252;
    }

    .interview-tab-btn.active {
        color: #0b3252;
        font-weight: 700;
        border-bottom: 3px solid #0b3252;
    }

    .interview-tab-body {
        flex: 1;
        overflow-y: auto;
        padding: 1.25rem 1.25rem;
        max-height: 640px;
    }

    /* Date Group Header */
    .interview-day-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background-color: #eaf0f6;
        color: #1e3a58;
        font-weight: 700;
        font-size: 1.15rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Interview Card Item */
    .interview-card-item {
        border: 1px solid #e7ecf2;
        border-radius: 14px;
        padding: 12px 16px;
        margin-bottom: 10px;
        background-color: #ffffff;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .interview-card-item:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transform: translateY(-1px);
    }

    /* Empty state */
    .interview-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 3rem 1rem;
        min-height: 380px;
    }

    .interview-empty-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background-color: #eaf0f6;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.25rem;
    }

    /* Button style for Add New Interview in FullCalendar */
    .fc-addEventButton-button {
        background-color: #0b3252 !important;
        border-color: #0b3252 !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        padding: 0.45rem 1.15rem !important;
        border-radius: 6px !important;
    }

    /* Search status pill */
    .civil-status-badge {
        font-size: 0.8rem;
        padding: 4px 10px;
        border-radius: 20px;
    }
</style>
@endsection

@section('content')
<!-- Page Header / Breadcrumb -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Schedules & Events') }}</li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Interviews Calendar') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-calendar-star text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Interviews Calendar') }}</h4>
            <span class="badge bg-label-primary ms-2">{{ __('Guardian Interviews') }}</span>
        </div>
    </div>
</div>

<!-- Main Calendar Card -->
<div class="card shadow-sm border-0 overflow-hidden">
    <div class="d-flex flex-column flex-lg-row">
        <!-- ============================================================== -->
        <!-- Right Panel (in RTL): Full Calendar Area -->
        <!-- ============================================================== -->
        <div class="flex-grow-1 p-3 p-md-4" style="min-width: 0;">
            <div id="calendar"></div>
        </div>

        <!-- ============================================================== -->
        <!-- Left Panel (in RTL): Interviews Sidebar (This Month & Upcoming) -->
        <!-- ============================================================== -->
        <div class="interview-sidebar border-start" style="width: 380px; max-width: 100%; flex-shrink: 0;">
            <!-- Sidebar Header -->
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                <div>
                    <h5 class="fw-bold mb-0 text-heading" id="sidebarHeaderTitle">
                        {{ $sidebarData['month_label'] ?? 'أكتوبر 2026' }}
                    </h5>
                    <span class="text-muted small" id="sidebarHeaderCount">
                        {{ $sidebarData['this_month_count'] ?? 0 }} {{ app()->getLocale() === 'ar' ? 'مواعيد' : 'Interview(s)' }}
                    </span>
                </div>
                <div class="interview-cal-box shadow-none">
                    <i class="icon-base bx bx-calendar fs-3" style="color: #24496d;"></i>
                </div>
            </div>

            <!-- Tabs: This Month & Upcoming -->
            <div class="interview-tabs-nav">
                <button type="button" class="interview-tab-btn active" id="tabBtnThisMonth" onclick="switchInterviewTab('this_month')">
                    {{ app()->getLocale() === 'ar' ? 'هذا الشهر' : 'This Month' }}
                </button>
                <button type="button" class="interview-tab-btn" id="tabBtnUpcoming" onclick="switchInterviewTab('upcoming')">
                    {{ app()->getLocale() === 'ar' ? 'القادمة' : 'Upcoming' }}
                </button>
            </div>

            <!-- Tab Body -->
            <div class="interview-tab-body">
                <!-- Pane: This Month -->
                <div id="paneThisMonthInterviews">
                    <div id="thisMonthInterviewsList">
                        <!-- Populated by JS -->
                    </div>
                    <!-- Empty State for This Month -->
                    <div id="thisMonthInterviewsEmpty" class="interview-empty-state d-none">
                        <div class="interview-empty-icon">
                            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#255177" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                <line x1="3" y1="3" x2="21" y2="21"></line>
                            </svg>
                        </div>
                        <h6 class="fw-bold mb-1 text-secondary">No Interviews this month</h6>
                        <p class="text-muted small mb-0">Interviews will appear here</p>
                    </div>
                </div>

                <!-- Pane: Upcoming -->
                <div id="paneUpcomingInterviews" class="d-none">
                    <div id="upcomingInterviewsList">
                        <!-- Populated by JS -->
                    </div>
                    <!-- Empty State for Upcoming -->
                    <div id="upcomingInterviewsEmpty" class="interview-empty-state d-none">
                        <div class="interview-empty-icon">
                            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#255177" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                <line x1="3" y1="3" x2="21" y2="21"></line>
                            </svg>
                        </div>
                        <h6 class="fw-bold mb-1 text-secondary">{{ __('No Upcoming Interviews') }}</h6>
                        <p class="text-muted small mb-0">{{ __('Interviews will appear here') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- Add / Edit Interview Offcanvas Drawer -->
<!-- ============================================================== -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasInterview" aria-labelledby="offcanvasInterviewLabel" style="width: 520px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3">
        <h5 class="offcanvas-title fw-bold" id="offcanvasInterviewLabel">
            <i class="icon-base bx bx-calendar-star me-2 text-primary"></i>
            <span id="offcanvasInterviewTitleText">{{ __('Add New Interview') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="formInterview" autocomplete="off">
            <input type="hidden" name="interview_id" id="interview_id" value="">
            <input type="hidden" name="academic_guardian_id" id="academic_guardian_id" value="">

            <!-- Guardian Lookup & Civil ID -->
            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('Civil ID') }}</label>
                <input type="text" class="form-control" name="guardian_civil_id" id="guardian_civil_id" maxlength="30">
                <!-- Status Badge Indicator -->
                <div id="civilIdStatusContainer" class="mt-2 d-none">
                    <div id="civilIdStatusBadge" class="alert py-2 px-3 mb-0 small d-flex align-items-center gap-2">
                        <!-- Injected via JS -->
                    </div>
                </div>
            </div>

            <!-- Guardian Name -->
            <div class="mb-3">
                <label class="form-label required fw-medium">{{ __('Guardian Name') }}</label>
                <input type="text" class="form-control" name="guardian_name" id="guardian_name" required>
                <div class="invalid-feedback d-block" id="error_guardian_name"></div>
            </div>

            <!-- Phone -->
            <div class="mb-3">
                <label class="form-label required fw-medium">{{ __('Phone Number') }}</label>
                <input type="text" class="form-control" name="guardian_phone" id="guardian_phone" required>
                <div class="invalid-feedback d-block" id="error_guardian_phone"></div>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label fw-medium">{{ __('Email') }}</label>
                <input type="email" class="form-control" name="guardian_email" id="guardian_email">
            </div>

            <!-- Institution -->
            <div class="mb-3">
                <label class="form-label required fw-medium">{{ app()->getLocale() === 'ar' ? 'الحضانة / المؤسسة' : __('Institution / Nursery') }}</label>
                <select class="form-select" name="institution_id" id="institution_id" required>
                    <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الحضانة...' : __('Select Nursery...') }}</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}">
                            {{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" id="error_institution_id"></div>
            </div>

            <!-- Branch -->
            <div class="mb-3">
                <label class="form-label fw-medium">{{ app()->getLocale() === 'ar' ? 'الفرع' : __('Branch') }}</label>
                <select class="form-select" name="branch_id" id="branch_id">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الفرع...' : __('Select Branch...') }}</option>
                </select>
            </div>

            <!-- Interview Date -->
            <div class="mb-3">
                <label class="form-label required fw-medium">{{ app()->getLocale() === 'ar' ? 'تاريخ المقابلة' : __('Interview Date') }}</label>
                <input type="text" class="form-control js-flatpickr-date flatpickr-input active" name="interview_date" id="interview_date" required value="{{ date('Y-m-d') }}">
                <div class="invalid-feedback d-block" id="error_interview_date"></div>
            </div>

            <!-- Start Time -->
            <div class="mb-3">
                <label class="form-label required fw-medium">{{ app()->getLocale() === 'ar' ? 'وقت البدء' : __('Start Time') }}</label>
                <input type="time" class="form-control" name="start_time" id="start_time" required value="09:00">
                <div class="invalid-feedback d-block" id="error_start_time"></div>
            </div>

            <!-- End Time -->
            <div class="mb-3">
                <label class="form-label fw-medium">{{ app()->getLocale() === 'ar' ? 'وقت الانتهاء' : __('End Time') }}</label>
                <input type="time" class="form-control" name="end_time" id="end_time" value="09:30">
            </div>

            <!-- Notes -->
            <div class="mb-3">
                <label class="form-label fw-medium">{{ app()->getLocale() === 'ar' ? 'الملاحظات' : __('Notes') }}</label>
                <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
            </div>

            <!-- Status -->
            <div class="mb-3">
                <label class="form-label fw-medium">{{ app()->getLocale() === 'ar' ? 'الحالة' : __('Status') }}</label>
                <select class="form-select" name="status" id="status">
                    @foreach ($statuses as $val => $lbl)
                        <option value="{{ $val }}" {{ $val === 'planned' ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Color Palette -->
            <div class="mb-4">
                <label class="form-label fw-medium d-block mb-2">{{ app()->getLocale() === 'ar' ? 'اللون' : __('Color') }}</label>
                <div class="d-flex align-items-center gap-2">
                    <span class="color-radio selected" data-color="#0b3252" style="background-color: #0b3252;" title="{{ __('Navy') }}"></span>
                    <span class="color-radio" data-color="#28c76f" style="background-color: #28c76f;" title="{{ __('Green') }}"></span>
                    <span class="color-radio" data-color="#7367f0" style="background-color: #7367f0;" title="{{ __('Purple') }}"></span>
                    <span class="color-radio" data-color="#ff9f43" style="background-color: #ff9f43;" title="{{ __('Orange') }}"></span>
                    <span class="color-radio" data-color="#ea5455" style="background-color: #ea5455;" title="{{ __('Red') }}"></span>
                    <span class="color-radio" data-color="#00cfe8" style="background-color: #00cfe8;" title="{{ __('Cyan') }}"></span>
                    <span class="color-radio" data-color="#5a8dee" style="background-color: #5a8dee;" title="{{ __('Blue') }}"></span>
                </div>
                <input type="hidden" name="color" id="interview_color" value="#0b3252">
            </div>

            <!-- Form Action Buttons -->
            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                @can('academic.interviews_calender.delete')
                <button type="button" class="btn btn-outline-danger d-none" id="btnDeleteInterview">
                    <i class="icon-base bx bx-trash me-1"></i> {{ __('Delete') }}
                </button>
                @endcan
                <div class="d-flex gap-2 ms-auto">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                    @canany(['academic.interviews_calender.create', 'academic.interviews_calender.edit'])
                    <button type="submit" class="btn text-white" id="btnSaveInterview" style="background-color: #0b3252;">
                        <i class="icon-base bx bx-check me-1"></i> {{ __('Save') }}
                    </button>
                    @endcanany
                </div>
            </div>
        </form>
    </div>
</div>

<!-- View Interview Modal -->
<div class="modal fade" id="modalViewInterview" tabindex="-1" aria-labelledby="modalViewInterviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold" id="modalViewInterviewLabel">
                    <i class="icon-base bx bx-calendar-star me-2 text-primary"></i>
                    {{ __('Interview Details') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <h4 class="fw-bold mb-1 text-heading" id="viewInterviewGuardianName"></h4>
                        <div class="text-muted small" id="viewInterviewGuardianMeta"></div>
                    </div>
                    <span class="badge" id="viewInterviewStatusBadge" style="font-size: 0.85rem; padding: 6px 12px;"></span>
                </div>

                <div class="p-3 bg-light rounded-3 mb-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <span class="text-muted small d-block">{{ __('Interview Date') }}</span>
                            <strong class="text-heading" id="viewInterviewDate"></strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small d-block">{{ __('Time') }}</span>
                            <strong class="text-heading" id="viewInterviewTime"></strong>
                        </div>
                    </div>
                </div>

                <div class="mb-3" id="viewInterviewInstitutionWrap">
                    <span class="text-muted small d-block">{{ __('Institution / Branch') }}</span>
                    <strong class="text-heading" id="viewInterviewInstitution"></strong>
                </div>

                <div class="mb-3" id="viewInterviewNotesWrap">
                    <span class="text-muted small d-block">{{ __('Notes') }}</span>
                    <p class="mb-0 text-secondary" id="viewInterviewNotes" style="white-space: pre-line;"></p>
                </div>
            </div>
            <div class="modal-footer border-top py-2">
                @can('academic.interviews_calender.delete')
                <button type="button" class="btn btn-outline-danger me-auto" id="btnDeleteFromViewModal">
                    <i class="icon-base bx bx-trash me-1"></i> {{ __('Delete') }}
                </button>
                @endcan
                @can('academic.interviews_calender.edit')
                <button type="button" class="btn btn-outline-primary" id="btnEditFromViewModal">
                    <i class="icon-base bx bx-edit me-1"></i> {{ __('Edit') }}
                </button>
                @endcan
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-script')
<!-- FullCalendar v6 CDN -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales-all.global.min.js"></script>

<script>
    let calendar;
    let sidebarData = @json($sidebarData);
    let activeInterviewTab = 'this_month';
    let currentViewingInterviewId = null;
    let offcanvasInterviewInstance = null;
    let modalViewInterviewInstance = null;

    document.addEventListener('DOMContentLoaded', function () {
        const offcanvasEl = document.getElementById('offcanvasInterview');
        if (offcanvasEl) {
            offcanvasInterviewInstance = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
        }
        const modalViewEl = document.getElementById('modalViewInterview');
        if (modalViewEl) {
            modalViewInterviewInstance = bootstrap.Modal.getOrCreateInstance(modalViewEl);
        }

        // Initialize flatpickr on date fields
        initDatePickers();

        // Render initial sidebar
        renderSidebar(sidebarData);

        // Initialize FullCalendar
        const calendarEl = document.getElementById('calendar');

        const canCreate = {{ auth()->user()->can('academic.interviews_calender.create') ? 'true' : 'false' }};
        const canEdit = {{ auth()->user()->can('academic.interviews_calender.edit') ? 'true' : 'false' }};

        const customButtonsConfig = canCreate ? {
            addEventButton: {
                text: '{{ __("Add New Interview") }}',
                click: function () {
                    openAddInterviewDrawer();
                }
            }
        } : {};

        if (window.createAppCalendar && calendarEl) {
            calendar = window.createAppCalendar(calendarEl, {
                customButtons: customButtonsConfig,
                buttonText: {
                    month: 'Month',
                    week: 'Week',
                    day: 'Day',
                    list: 'List'
                },
                events: function (fetchInfo, successCallback, failureCallback) {
                    $.ajax({
                        url: '{{ route("academic.interviews_calender.calendar-data") }}',
                        type: 'GET',
                        data: {
                            start: fetchInfo.startStr,
                            end: fetchInfo.endStr,
                        },
                        success: function (events) {
                            successCallback(events);
                        },
                        error: function () {
                            failureCallback();
                        }
                    });
                },
                select: function (selectionInfo) {
                    if (canCreate) {
                        openAddInterviewDrawer(selectionInfo.startStr);
                    }
                    if (calendar && calendar.unselect) calendar.unselect();
                },
                eventClick: function (info) {
                    info.jsEvent.preventDefault();
                    const id = info.event.id || (info.event.extendedProps ? info.event.extendedProps.id : null);
                    if (canEdit) {
                        openEditInterviewDrawer(id);
                    } else {
                        window.openViewInterviewModal(id);
                    }
                },
                dateClick: function (info) {
                    if (canCreate) {
                        openAddInterviewDrawer(info.dateStr);
                    }
                },
                eventContent: function (arg) {
                    const props = arg.event.extendedProps;
                    const timeText = props.start_time ? `<span class="badge bg-white text-dark py-0 px-1 font-11 me-1">${props.start_time}</span>` : '';
                    const container = document.createElement('div');
                    container.className = 'd-flex align-items-center gap-1 text-truncate w-100 px-1 py-1 rounded text-white';
                    container.style.backgroundColor = arg.event.backgroundColor || '#0b3252';
                    container.style.fontSize = '0.78rem';
                    container.style.cursor = 'pointer';

                    container.innerHTML = `
                        <span style="width: 6px; height: 6px; border-radius: 50%; background-color: #ffffff; display: inline-block; flex-shrink: 0;"></span>
                        ${timeText}
                        <span class="text-truncate fw-semibold">${arg.event.title}</span>
                    `;

                    return { domNodes: [container] };
                }
            });
        }

        // Color Picker Click
        $('.color-radio').on('click', function () {
            $('.color-radio').removeClass('selected');
            $(this).addClass('selected');
            $('#interview_color').val($(this).data('color'));
        });

        // Institution changed -> Load Branches
        $('#institution_id').on('change', function () {
            const institutionId = $(this).val();
            loadBranches(institutionId, '#branch_id');
        });

        function loadBranches(institutionId, branchSelectSelector, selectedBranchId = null) {
            const selectEl = $(branchSelectSelector);
            selectEl.html('<option value="">{{ __("Select Branch...") }}</option>');

            if (!institutionId) return;

            $.ajax({
                url: `{{ url("academic/interviews_calender/branches") }}/${institutionId}`,
                type: 'GET',
                success: function (res) {
                    if (res.success && res.data) {
                        res.data.forEach(function (branch) {
                            const isSelected = selectedBranchId && String(selectedBranchId) === String(branch.id) ? 'selected' : '';
                            selectEl.append(`<option value="${branch.id}" ${isSelected}>${branch.name}</option>`);
                        });
                    }
                }
            });
        }

        // Civil ID Instant Verification
        let civilTimeout = null;
        $('#guardian_civil_id').on('input change blur', function () {
            clearTimeout(civilTimeout);
            const val = $(this).val().trim();
            if (val.length >= 3) {
                civilTimeout = setTimeout(function () {
                    checkCivilId(val);
                }, 300);
            } else if (val.length === 0) {
                resetCivilStatus();
            }
        });

        function resetCivilStatus() {
            $('#civilIdStatusContainer').addClass('d-none');
            $('#civilIdStatusBadge').html('');
            $('#academic_guardian_id').val('');
        }

        function checkCivilId(civilId) {
            $('#civilIdStatusContainer').removeClass('d-none');
            $('#civilIdStatusBadge')
                .removeClass('alert-success alert-info alert-danger')
                .addClass('alert-secondary')
                .html('<div class="spinner-border spinner-border-sm text-primary" role="status"></div> <span>{{ __("Checking Civil ID...") }}</span>');

            $.ajax({
                url: '{{ route("academic.interviews_calender.check-guardian") }}',
                type: 'GET',
                data: { civil_id: civilId },
                success: function (res) {
                    if (res.success && res.data.found) {
                        const g = res.data.guardian;
                        $('#academic_guardian_id').val(g.id);
                        $('#guardian_name').val(g.name);
                        $('#guardian_phone').val(g.phone || '');
                        $('#guardian_email').val(g.email || '');

                        if (g.institution_id) {
                            $('#institution_id').val(g.institution_id);
                            loadBranches(g.institution_id, '#branch_id', g.branch_id);
                        }

                        $('#civilIdStatusBadge')
                            .removeClass('alert-secondary alert-info')
                            .addClass('alert-success')
                            .html('<i class="icon-base bx bx-check-circle fs-5 text-success"></i> <div><strong>{{ __("Registered Guardian") }}:</strong> ' + g.name + '</div>');
                    } else {
                        $('#academic_guardian_id').val('');
                        $('#civilIdStatusBadge')
                            .removeClass('alert-secondary alert-success')
                            .addClass('alert-info')
                            .html('<i class="icon-base bx bx-user-plus fs-5 text-primary"></i> <div><strong>{{ __("New Guardian (will be created automatically)") }}</strong></div>');
                    }
                },
                error: function () {
                    $('#civilIdStatusBadge')
                        .removeClass('alert-secondary alert-success alert-info')
                        .addClass('alert-danger')
                        .html('<i class="icon-base bx bx-error-circle fs-5 text-danger"></i> <span>{{ __("Failed to verify civil ID.") }}</span>');
                }
            });
        }

        function initDatePickers() {
            if (window.flatpickr) {
                $('.js-flatpickr-date').each(function () {
                    if (!this._flatpickr) {
                        flatpickr(this, {
                            locale: appLocale === 'ar' ? 'ar' : 'en',
                            dateFormat: 'Y-m-d',
                            allowInput: true,
                            altInput: false,
                            disableMobile: true
                        });
                    }
                });
            }
        }

        function setDateVal(selector, val) {
            const el = document.querySelector(selector);
            if (!el) return;
            if (el._flatpickr) {
                el._flatpickr.setDate(val || '', true);
            } else {
                $(selector).val(val || '');
            }
        }

        // Drawer open helpers
        window.openAddInterviewDrawer = function (prefilledDate = null) {
            $('#formInterview')[0].reset();
            $('#interview_id').val('');
            $('#academic_guardian_id').val('');
            resetCivilStatus();
            $('.invalid-feedback').text('');
            $('#btnDeleteInterview').addClass('d-none');
            $('#offcanvasInterviewTitleText').text('{{ __("Add New Interview") }}');

            if (prefilledDate) {
                setDateVal('#interview_date', prefilledDate);
            } else {
                setDateVal('#interview_date', new Date().toISOString().split('T')[0]);
            }

            $('#start_time').val('09:00');
            $('#end_time').val('09:30');
            $('#status').val('planned');

            // Select default color
            $('.color-radio').removeClass('selected');
            $('.color-radio[data-color="#0b3252"]').addClass('selected');
            $('#interview_color').val('#0b3252');

            if (offcanvasInterviewInstance) {
                offcanvasInterviewInstance.show();
            }
        };

        window.openEditInterviewDrawer = function (id) {
            $('.invalid-feedback').text('');
            resetCivilStatus();

            $.ajax({
                url: `{{ url("academic/interviews_calender") }}/${id}`,
                type: 'GET',
                success: function (res) {
                    if (res.success && res.data) {
                        const d = res.data;
                        $('#interview_id').val(d.id);
                        $('#academic_guardian_id').val(d.academic_guardian_id || '');
                        $('#guardian_civil_id').val(d.guardian_civil_id || '');
                        $('#guardian_name').val(d.guardian_name || '');
                        $('#guardian_phone').val(d.guardian_phone || '');
                        $('#guardian_email').val(d.guardian_email || '');
                        setDateVal('#interview_date', d.interview_date || '');
                        $('#start_time').val(d.start_time || '');
                        $('#end_time').val(d.end_time || '');
                        $('#notes').val(d.notes || '');
                        $('#status').val(d.status || 'planned');
                        $('#institution_id').val(d.institution_id || '');

                        if (d.institution_id) {
                            loadBranches(d.institution_id, '#branch_id', d.branch_id);
                        } else {
                            $('#branch_id').html('<option value="">{{ __("Select Branch...") }}</option>');
                        }

                        // Color
                        const color = d.color || '#0b3252';
                        $('#interview_color').val(color);
                        $('.color-radio').removeClass('selected');
                        $(`.color-radio[data-color="${color}"]`).addClass('selected');

                        if (d.academic_guardian_id) {
                            $('#civilIdStatusContainer').removeClass('d-none');
                            $('#civilIdStatusBadge')
                                .removeClass('alert-secondary alert-info')
                                .addClass('alert-success')
                                .html('<i class="icon-base bx bx-check-circle fs-5 text-success"></i> <div><strong>{{ __("Registered Guardian") }}:</strong> ' + d.guardian_name + '</div>');
                        }

                        $('#offcanvasInterviewTitleText').text('{{ __("Edit Interview") }}');
                        $('#btnDeleteInterview').removeClass('d-none');

                        if (offcanvasInterviewInstance) {
                            offcanvasInterviewInstance.show();
                        }
                    }
                }
            });
        };

        window.openViewInterviewModal = function (id) {
            currentViewingInterviewId = id;
            $.ajax({
                url: `{{ url("academic/interviews_calender") }}/${id}`,
                type: 'GET',
                success: function (res) {
                    if (res.success && res.data) {
                        const d = res.data;
                        $('#viewInterviewGuardianName').text(d.guardian_name);

                        let metaText = '';
                        if (d.guardian_civil_id) metaText += '{{ __("Civil ID") }}: ' + d.guardian_civil_id + ' | ';
                        if (d.guardian_phone) metaText += '{{ __("Phone") }}: ' + d.guardian_phone;
                        $('#viewInterviewGuardianMeta').text(metaText);

                        // Status badge
                        let statusClass = 'bg-label-primary';
                        if (d.status === 'completed') statusClass = 'bg-label-success';
                        else if (d.status === 'cancelled') statusClass = 'bg-label-danger';
                        else if (d.status === 'absent') statusClass = 'bg-label-warning';

                        $('#viewInterviewStatusBadge')
                            .attr('class', 'badge ' + statusClass)
                            .text(d.status_label || d.status);

                        $('#viewInterviewDate').text(d.interview_date);
                        $('#viewInterviewTime').text(d.start_time + (d.end_time ? ' - ' + d.end_time : ''));

                        let instText = d.institution_name || '-';
                        if (d.branch_name) instText += ' (' + d.branch_name + ')';
                        $('#viewInterviewInstitution').text(instText);

                        if (d.notes) {
                            $('#viewInterviewNotesWrap').removeClass('d-none');
                            $('#viewInterviewNotes').text(d.notes);
                        } else {
                            $('#viewInterviewNotesWrap').addClass('d-none');
                        }

                        if (modalViewInterviewInstance) {
                            modalViewInterviewInstance.show();
                        }
                    }
                }
            });
        };

        $('#btnEditFromViewModal').on('click', function () {
            if (modalViewInterviewInstance) {
                modalViewInterviewInstance.hide();
            }
            if (currentViewingInterviewId) {
                openEditInterviewDrawer(currentViewingInterviewId);
            }
        });

        $('#btnDeleteFromViewModal').on('click', function () {
            if (currentViewingInterviewId) {
                deleteInterview(currentViewingInterviewId, function () {
                    if (modalViewInterviewInstance) {
                        modalViewInterviewInstance.hide();
                    }
                });
            }
        });

        $('#btnDeleteInterview').on('click', function () {
            const id = $('#interview_id').val();
            if (id) {
                deleteInterview(id, function () {
                    if (offcanvasInterviewInstance) {
                        offcanvasInterviewInstance.hide();
                    }
                });
            }
        });

        function deleteInterview(id, callback) {
            if (!confirm('{{ __("Are you sure you want to delete this interview?") }}')) {
                return;
            }

            $.ajax({
                url: `{{ url("academic/interviews_calender") }}/${id}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    if (res.success) {
                        if (callback) callback();
                        refreshData();
                    } else {
                        alert(res.message || '{{ __("Failed to delete interview.") }}');
                    }
                },
                error: function () {
                    alert('{{ __("Failed to delete interview.") }}');
                }
            });
        }

        // Save Form (Create / Update)
        $('#formInterview').on('submit', function (e) {
            e.preventDefault();
            $('.invalid-feedback').text('');

            const id = $('#interview_id').val();
            const isEdit = !!id;
            const url = isEdit ? `{{ url("academic/interviews_calender") }}/${id}` : '{{ route("academic.interviews_calender.store") }}';
            const method = isEdit ? 'PUT' : 'POST';

            const payload = {
                _token: '{{ csrf_token() }}',
                academic_guardian_id: $('#academic_guardian_id').val() || null,
                guardian_civil_id: $('#guardian_civil_id').val().trim() || null,
                guardian_name: $('#guardian_name').val().trim(),
                guardian_phone: $('#guardian_phone').val().trim(),
                guardian_email: $('#guardian_email').val().trim() || null,
                institution_id: $('#institution_id').val(),
                branch_id: $('#branch_id').val() || null,
                interview_date: $('#interview_date').val(),
                start_time: $('#start_time').val(),
                end_time: $('#end_time').val() || null,
                notes: $('#notes').val() || null,
                status: $('#status').val(),
                color: $('#interview_color').val() || '#0b3252',
            };

            const btnSave = $('#btnSaveInterview');
            btnSave.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> {{ __("Saving...") }}');

            $.ajax({
                url: url,
                type: method,
                data: payload,
                success: function (res) {
                    btnSave.prop('disabled', false).html('<i class="icon-base bx bx-check me-1"></i> {{ __("Save") }}');
                    if (res.success) {
                        if (offcanvasInterviewInstance) {
                            offcanvasInterviewInstance.hide();
                        }
                        refreshData();
                    } else {
                        alert(res.message || '{{ __("Failed to save interview.") }}');
                    }
                },
                error: function (xhr) {
                    btnSave.prop('disabled', false).html('<i class="icon-base bx bx-check me-1"></i> {{ __("Save") }}');
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errs = xhr.responseJSON.errors;
                        for (const key in errs) {
                            $(`#error_${key}`).text(errs[key][0]);
                        }
                    } else {
                        alert(xhr.responseJSON?.message || '{{ __("An error occurred while saving.") }}');
                    }
                }
            });
        });

        function refreshData() {
            if (calendar) {
                calendar.refetchEvents();
            }
            $.ajax({
                url: '{{ route("academic.interviews_calender.sidebar-data") }}',
                type: 'GET',
                success: function (res) {
                    if (res.success && res.data) {
                        sidebarData = res.data;
                        renderSidebar(sidebarData);
                    }
                }
            });
        }
    });

    // Switch sidebar tab
    window.switchInterviewTab = function (tab) {
        activeInterviewTab = tab;
        if (tab === 'this_month') {
            $('#tabBtnThisMonth').addClass('active');
            $('#tabBtnUpcoming').removeClass('active');
            $('#paneThisMonthInterviews').removeClass('d-none');
            $('#paneUpcomingInterviews').addClass('d-none');
            $('#sidebarHeaderTitle').text(sidebarData.month_label || '{{ __("This Month") }}');
            $('#sidebarHeaderCount').text((sidebarData.this_month_count || 0) + ' ' + (appLocale === 'ar' ? 'مواعيد' : 'Interview(s)'));
        } else {
            $('#tabBtnUpcoming').addClass('active');
            $('#tabBtnThisMonth').removeClass('active');
            $('#paneUpcomingInterviews').removeClass('d-none');
            $('#paneThisMonthInterviews').addClass('d-none');
            $('#sidebarHeaderTitle').text('{{ __("Upcoming") }}');
            $('#sidebarHeaderCount').text((sidebarData.upcoming_count || 0) + ' ' + (appLocale === 'ar' ? 'مواعيد' : 'Interview(s)'));
        }
    };

    const appLocale = '{{ app()->getLocale() }}';

    function renderSidebar(data) {
        // Update Header
        if (activeInterviewTab === 'this_month') {
            $('#sidebarHeaderTitle').text(data.month_label || '{{ __("This Month") }}');
            $('#sidebarHeaderCount').text((data.this_month_count || 0) + ' ' + (appLocale === 'ar' ? 'مواعيد' : 'Interview(s)'));
        } else {
            $('#sidebarHeaderTitle').text('{{ __("Upcoming") }}');
            $('#sidebarHeaderCount').text((data.upcoming_count || 0) + ' ' + (appLocale === 'ar' ? 'مواعيد' : 'Interview(s)'));
        }

        // Render This Month
        const thisMonthList = $('#thisMonthInterviewsList');
        const thisMonthEmpty = $('#thisMonthInterviewsEmpty');
        thisMonthList.empty();

        if (!data.this_month_groups || data.this_month_groups.length === 0) {
            thisMonthEmpty.removeClass('d-none');
        } else {
            thisMonthEmpty.addClass('d-none');
            data.this_month_groups.forEach(function (group) {
                thisMonthList.append(renderGroupHtml(group));
            });
        }

        // Render Upcoming
        const upcomingList = $('#upcomingMeetingsList');
        const upcomingEmpty = $('#upcomingInterviewsEmpty');
        $('#upcomingInterviewsList').empty();

        if (!data.upcoming_groups || data.upcoming_groups.length === 0) {
            upcomingEmpty.removeClass('d-none');
        } else {
            upcomingEmpty.addClass('d-none');
            data.upcoming_groups.forEach(function (group) {
                $('#upcomingInterviewsList').append(renderGroupHtml(group));
            });
        }
    }

    function renderGroupHtml(group) {
        let cardsHtml = '';
        if (group.interviews && group.interviews.length > 0) {
            group.interviews.forEach(function (interview) {
                cardsHtml += buildInterviewCardHtml(interview);
            });
        }

        const countText = `${group.count} ` + (appLocale === 'ar' ? 'مقابلة' : 'Interview(s)');

        return `
            <div class="interview-date-group mb-3">
                <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                    <div class="d-flex align-items-center gap-2">
                        <div class="interview-day-box shadow-none">
                            ${group.day_num || ''}
                        </div>
                        <div>
                            <div class="fw-bold text-heading font-14">${group.date_formatted || ''}</div>
                            <div class="text-muted font-12">${countText}</div>
                        </div>
                    </div>
                    <div>
                        <i class="icon-base bx ${appLocale === 'ar' ? 'bx-chevron-left' : 'bx-chevron-right'} text-muted fs-4"></i>
                    </div>
                </div>
                ${cardsHtml}
            </div>
        `;
    }

    function buildInterviewCardHtml(interview) {
        const canEdit = {{ auth()->user()->can('academic.interviews_calender.edit') ? 'true' : 'false' }};
        const subtitleText = interview.notes || interview.guardian_phone || '';
        const clickAttr = canEdit ? `onclick="openEditInterviewDrawer(${interview.id})"` : `onclick="openViewInterviewModal(${interview.id})"`;
        const editBtnHtml = canEdit ? `
            <div>
                <button type="button" class="btn btn-icon btn-sm text-muted p-0" title="{{ __('Edit') }}" onclick="event.stopPropagation(); openEditInterviewDrawer(${interview.id})">
                    <i class="icon-base bx bx-edit-alt fs-5"></i>
                </button>
            </div>` : '';

        return `
            <div class="interview-card-item" ${clickAttr}>
                <div class="d-flex align-items-center justify-content-between">
                    <!-- Right in RTL: Time with Clock Icon -->
                    <div class="d-flex align-items-center gap-1" style="min-width: 130px;">
                        <i class="icon-base bx bx-time fs-5 text-primary"></i>
                        <span class="fw-bold font-13" style="color: #1e3a58;">
                            ${interview.time_range || interview.start_time}
                        </span>
                    </div>

                    <!-- Center in RTL: Guardian Name & Notes/Phone -->
                    <div class="text-end flex-grow-1 px-3">
                        <div class="fw-bold text-heading font-14 text-truncate">${interview.guardian_name}</div>
                        ${subtitleText ? `<div class="text-muted font-12 text-truncate mt-1">${subtitleText}</div>` : ''}
                    </div>

                    <!-- Left in RTL: Edit Pencil Button -->
                    ${editBtnHtml}
                </div>
            </div>
        `;
    }
</script>
@endsection
