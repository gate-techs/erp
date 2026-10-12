@extends('layouts/contentNavbarLayout')

@section('title', __('Meetings Calendar') . ' - ' . config('variables.templateName'))

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

    /* Meeting Sidebar Styles */
    .meeting-sidebar {
        background-color: #ffffff;
        min-height: 680px;
        display: flex;
        flex-direction: column;
        border-inline-start: 1px solid #ebeef2;
    }

    .meeting-cal-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background-color: #eaf0f6;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
    }

    .meeting-tabs-nav {
        display: flex;
        border-bottom: 1px solid #ebeef2;
        padding: 0 1.25rem;
        gap: 1.5rem;
    }

    .meeting-tab-btn {
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

    .meeting-tab-btn:hover {
        color: #0b3252;
    }

    .meeting-tab-btn.active {
        color: #0b3252;
        font-weight: 700;
        border-bottom: 3px solid #0b3252;
    }

    .meeting-tab-body {
        flex: 1;
        overflow-y: auto;
        padding: 1.25rem 1.25rem;
        max-height: 640px;
    }

    /* Date Group Header */
    .meeting-day-box {
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

    /* Meeting Card (Matches Image 2 exactly) */
    .meeting-card-item {
        border: 1px solid #e7ecf2;
        border-radius: 14px;
        padding: 12px 16px;
        margin-bottom: 10px;
        background-color: #ffffff;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .meeting-card-item:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transform: translateY(-1px);
    }

    /* Empty state */
    .meeting-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 3rem 1rem;
        min-height: 380px;
    }

    .meeting-empty-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background-color: #eaf0f6;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.25rem;
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
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Meetings Calendar') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-calendar-check text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Meetings Calendar') }}</h4>
            <span class="badge bg-label-primary ms-2">{{ __('Meetings') }}</span>
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
        <!-- Left Panel (in RTL): Meetings Sidebar (Today & Upcoming) -->
        <!-- ============================================================== -->
        <div class="meeting-sidebar border-start" style="width: 380px; max-width: 100%; flex-shrink: 0;">
            <!-- Sidebar Header -->
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                <div>
                    <h5 class="fw-bold mb-0 text-heading" id="sidebarHeaderTitle">
                        {{ app()->getLocale() === 'ar' ? 'اجتماعات اليوم' : "Today's Meetings" }}
                    </h5>
                    <span class="text-muted small" id="sidebarHeaderCount">
                        {{ $sidebarData['today_count'] ?? 0 }} {{ app()->getLocale() === 'ar' ? 'اجتماع' : 'Meeting(s)' }}
                    </span>
                </div>
                <div class="meeting-cal-box shadow-none">
                    <i class="icon-base bx bx-calendar fs-3" style="color: #24496d;"></i>
                </div>
            </div>

            <!-- Tabs: Today & Upcoming -->
            <div class="meeting-tabs-nav">
                <button type="button" class="meeting-tab-btn active" id="tabBtnToday" onclick="switchMeetingTab('today')">
                    {{ app()->getLocale() === 'ar' ? 'اجتماعات اليوم' : "Today's Meetings" }}
                </button>
                <button type="button" class="meeting-tab-btn" id="tabBtnUpcoming" onclick="switchMeetingTab('upcoming')">
                    {{ app()->getLocale() === 'ar' ? 'الاجتماعات القادمة' : 'Upcoming Meetings' }}
                </button>
            </div>

            <!-- Tab Body -->
            <div class="meeting-tab-body">
                <!-- Pane: Today -->
                <div id="paneTodayMeetings">
                    <div id="todayMeetingsList">
                        <!-- Populated by JS -->
                    </div>
                    <!-- Empty State for Today -->
                    <div id="todayMeetingsEmpty" class="meeting-empty-state d-none">
                        <div class="meeting-empty-icon">
                            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#255177" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                <line x1="3" y1="3" x2="21" y2="21"></line>
                            </svg>
                        </div>
                        <h6 class="fw-bold mb-1 text-secondary">{{ __('No meetings today') }}</h6>
                        <p class="text-muted small mb-0">{{ __('Meetings will appear here') }}</p>
                    </div>
                </div>

                <!-- Pane: Upcoming -->
                <div id="paneUpcomingMeetings" class="d-none">
                    <div id="upcomingMeetingsList">
                        <!-- Populated by JS -->
                    </div>
                    <!-- Empty State for Upcoming -->
                    <div id="upcomingMeetingsEmpty" class="meeting-empty-state d-none">
                        <div class="meeting-empty-icon">
                            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#255177" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                <line x1="3" y1="3" x2="21" y2="21"></line>
                            </svg>
                        </div>
                        <h6 class="fw-bold mb-1 text-secondary">{{ __('No upcoming meetings') }}</h6>
                        <p class="text-muted small mb-0">{{ __('Meetings will appear here') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- Add / Edit Meeting Offcanvas Drawer -->
<!-- ============================================================== -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasMeeting" aria-labelledby="offcanvasMeetingLabel" style="width: 480px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 class="offcanvas-title fw-bold" id="offcanvasMeetingLabel">
            <i class="icon-base bx bx-calendar-plus text-primary me-2"></i>
            <span id="offcanvasMeetingTitleText">{{ __('Add New Meeting') }}</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="formMeeting">
            @csrf
            <input type="hidden" id="meeting_id" name="id" value="">

            <div class="row g-3">
                <!-- 1. Meeting Title -->
                <div class="col-12">
                    <label class="form-label required fw-medium">{{ __('Meeting Title') }}</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="{{ __('Meeting Title') }}" required>
                </div>

                <!-- 2. Date -->
                <div class="col-12">
                    <label class="form-label required fw-medium">{{ __('Date') }}</label>
                    <input type="text" class="form-control js-flatpickr-date flatpickr-input active" id="event_date" name="event_date" required>
                </div>

                <!-- 3. Time Range (Start Time & End Time - NO All-day concept) -->
                <div class="col-12 col-md-6">
                    <label class="form-label required fw-medium">{{ __('Start Time') }}</label>
                    <input type="time" class="form-control" id="start_time" name="start_time" required>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-medium">{{ __('End Time') }}</label>
                    <input type="time" class="form-control" id="end_time" name="end_time">
                </div>


                <!-- 5. Status -->
                <div class="col-12">
                    <label class="form-label fw-medium">{{ __('Status') }}</label>
                    <select class="form-select" id="status" name="status">
                        @foreach ($statuses as $val => $lbl)
                            <option value="{{ $val }}">{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 6. Color Picker -->
                <div class="col-12">
                    <label class="form-label fw-medium d-block">{{ __('Color') }}</label>
                    <input type="hidden" id="color" name="color" value="#0b3252">
                    <div class="d-flex align-items-center gap-2 pt-1" id="colorPickersContainer">
                        <span class="color-radio selected" data-color="#0b3252" style="background-color: #0b3252;"></span>
                        <span class="color-radio" data-color="#28c76f" style="background-color: #28c76f;"></span>
                        <span class="color-radio" data-color="#ff9f43" style="background-color: #ff9f43;"></span>
                        <span class="color-radio" data-color="#ea5455" style="background-color: #ea5455;"></span>
                        <span class="color-radio" data-color="#7367f0" style="background-color: #7367f0;"></span>
                        <span class="color-radio" data-color="#00bad1" style="background-color: #00bad1;"></span>
                    </div>
                </div>

                <!-- 7. Description / Notes -->
                <div class="col-12">
                    <label class="form-label fw-medium">{{ __('Description') }}</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="{{ __('Description') }}..."></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-4 mt-4 border-top">
                <button type="button" class="btn btn-outline-danger d-none" id="btnDeleteMeetingFromOffcanvas">
                    <i class="icon-base bx bx-trash me-1"></i>
                    <span>{{ __('Delete') }}</span>
                </button>
                <div class="d-flex gap-2 ms-auto">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveMeeting" style="background-color: #0b3252; border-color: #0b3252;">
                        <span class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                        <span>{{ __('Save') }}</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-script')
<!-- FullCalendar v6 CDN -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales-all.global.min.js"></script>

<script>
    let calendar = null;
    let offcanvasMeetingInstance = null;
    const currentLocale = '{{ app()->getLocale() }}';
    const isRtl = {{ app()->getLocale() === 'ar' ? 'true' : 'false' }};

    // Initial sidebar data injected from controller
    let sidebarData = @json($sidebarData);

    document.addEventListener('DOMContentLoaded', function () {
        const offcanvasEl = document.getElementById('offcanvasMeeting');
        if (offcanvasEl) {
            offcanvasMeetingInstance = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
        }

        initColorPickers();
        initDatePickers();
        initCalendar();
        initMeetingForm();
        renderSidebarMeetings(sidebarData);
    });

    //------------------------------
    function initDatePickers() {
        if (window.flatpickr) {
            $('.js-flatpickr-date').each(function () {
                if (!this._flatpickr) {
                    flatpickr(this, {
                        locale: currentLocale === 'ar' ? 'ar' : 'en',
                        dateFormat: 'Y-m-d',
                        allowInput: true,
                        altInput: false,
                        disableMobile: true
                    });
                }
            });
        }
    }

    //------------------------------
    function setDateVal(selector, val) {
        const el = document.querySelector(selector);
        if (!el) return;
        if (el._flatpickr) {
            el._flatpickr.setDate(val || '', true);
        } else {
            $(selector).val(val || '');
        }
    }

    //------------------------------
    function initColorPickers() {
        $('.color-radio').on('click', function () {
            $('.color-radio').removeClass('selected');
            $(this).addClass('selected');
            $('#color').val($(this).data('color'));
        });
    }

    function selectColor(hex) {
        $('.color-radio').removeClass('selected');
        const target = $(`.color-radio[data-color="${hex}"]`);
        if (target.length) {
            target.addClass('selected');
        } else {
            $('.color-radio:first').addClass('selected');
        }
        $('#color').val(hex || '#0b3252');
    }

    //------------------------------
    function initCalendar() {
        const calendarEl = document.getElementById('calendar');

        calendar = window.createAppCalendar(calendarEl, {
            customButtons: {
                addEventButton: {
                    text: '{{ __("Add New Meeting") }}',
                    click: function () {
                        openAddMeetingOffcanvas();
                    }
                }
            },
            buttonText: {
                month: 'Month',
                week: 'Week',
                day: 'Day',
                list: 'List'
            },

            // Fetch Meetings via AJAX
            events: function (fetchInfo, successCallback, failureCallback) {
                $.ajax({
                    url: '{{ route("academic.meeting-calendar.calendar-data") }}',
                    type: 'GET',
                    data: {
                        start: fetchInfo.startStr,
                        end: fetchInfo.endStr
                    },
                    success: function (response) {
                        successCallback(response);
                    },
                    error: function () {
                        failureCallback();
                    }
                });
            },

            // Click on date -> open add offcanvas
            select: function (selectionInfo) {
                openAddMeetingOffcanvas(selectionInfo.startStr);
                calendar.unselect();
            },

            // Click on event -> open edit drawer
            eventClick: function (info) {
                info.jsEvent.preventDefault();
                editMeeting(info.event.id);
            },

            // Custom event render
            eventContent: function (arg) {
                const props = arg.event.extendedProps;
                const timeText = props.start_time ? `<span class="badge bg-white text-dark py-0 px-1 font-11 me-1">${props.start_time}</span>` : '';
                const container = document.createElement('div');
                container.className = 'd-flex align-items-center gap-1 text-truncate w-100 px-1 py-1 rounded text-white';
                container.style.backgroundColor = arg.event.backgroundColor;
                container.style.fontSize = '0.78rem';

                container.innerHTML = `
                    <span style="width: 6px; height: 6px; border-radius: 50%; background-color: #ffffff; display: inline-block; flex-shrink: 0;"></span>
                    ${timeText}
                    <span class="text-truncate fw-semibold">${arg.event.title}</span>
                `;

                return { domNodes: [container] };
            }
        });
    }

    let currentActiveTab = 'today';

    //------------------------------
    // Tab switching in sidebar
    //------------------------------
    window.switchMeetingTab = function (tab) {
        currentActiveTab = tab;
        if (tab === 'today') {
            $('#tabBtnToday').addClass('active');
            $('#tabBtnUpcoming').removeClass('active');
            $('#paneTodayMeetings').removeClass('d-none');
            $('#paneUpcomingMeetings').addClass('d-none');

            $('#sidebarHeaderTitle').text(currentLocale === 'ar' ? 'اجتماعات اليوم' : "Today's Meetings");
            const count = (sidebarData && sidebarData.today_count !== undefined) ? sidebarData.today_count : 0;
            $('#sidebarHeaderCount').text(`${count} ` + (currentLocale === 'ar' ? 'اجتماع' : 'Meeting(s)'));
        } else {
            $('#tabBtnUpcoming').addClass('active');
            $('#tabBtnToday').removeClass('active');
            $('#paneUpcomingMeetings').removeClass('d-none');
            $('#paneTodayMeetings').addClass('d-none');

            $('#sidebarHeaderTitle').text(currentLocale === 'ar' ? 'الاجتماعات القادمة' : 'Upcoming Meetings');
            const count = (sidebarData && sidebarData.upcoming_count !== undefined) ? sidebarData.upcoming_count : 0;
            $('#sidebarHeaderCount').text(`${count} ` + (currentLocale === 'ar' ? 'اجتماع' : 'Meeting(s)'));
        }
    };

    //------------------------------
    // Render sidebar meetings
    //------------------------------
    function renderSidebarMeetings(data) {
        if (!data) return;
        sidebarData = data;

        // Update header count and active tab
        switchMeetingTab(currentActiveTab);

        // 1. Render Upcoming Meetings (Grouped by Date - matching Image 2)
        const upcomingContainer = $('#upcomingMeetingsList');
        upcomingContainer.empty();

        if (data.upcoming_groups && data.upcoming_groups.length > 0) {
            $('#upcomingMeetingsEmpty').addClass('d-none');
            data.upcoming_groups.forEach(function (group) {
                upcomingContainer.append(buildDateGroupHtml(group));
            });
        } else {
            $('#upcomingMeetingsEmpty').removeClass('d-none');
        }

        // 2. Render Today Meetings
        const todayContainer = $('#todayMeetingsList');
        todayContainer.empty();

        if (data.today_groups && data.today_groups.length > 0) {
            $('#todayMeetingsEmpty').addClass('d-none');
            data.today_groups.forEach(function (group) {
                todayContainer.append(buildDateGroupHtml(group));
            });
        } else {
            $('#todayMeetingsEmpty').removeClass('d-none');
        }
    }

    // Build Date Group with Day Box and Meeting Cards (Exact match to Image 2)
    function buildDateGroupHtml(group) {
        let cardsHtml = '';
        if (group.meetings && group.meetings.length > 0) {
            group.meetings.forEach(function (m) {
                cardsHtml += buildMeetingCardHtml(m);
            });
        }

        const countText = `${group.count} ` + (currentLocale === 'ar' ? 'اجتماع' : 'Meeting(s)');

        return `
            <div class="meeting-date-group mb-3">
                <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                    <div class="d-flex align-items-center gap-2">
                        <div class="meeting-day-box shadow-none">
                            ${group.day_num || ''}
                        </div>
                        <div>
                            <div class="fw-bold text-heading font-14">${group.date_formatted || ''}</div>
                            <div class="text-muted font-12">${countText}</div>
                        </div>
                    </div>
                    <div>
                        <i class="icon-base bx ${isRtl ? 'bx-chevron-left' : 'bx-chevron-right'} text-muted fs-4"></i>
                    </div>
                </div>
                ${cardsHtml}
            </div>
        `;
    }

    // Build Meeting card HTML (Matching Image 2)
    function buildMeetingCardHtml(m) {
        return `
            <div class="meeting-card-item" onclick="editMeeting(${m.id})">
                <div class="d-flex align-items-center justify-content-between">
                    <!-- Right in RTL: Time with Clock Icon -->
                    <div class="d-flex align-items-center gap-1" style="min-width: 130px;">
                        <i class="icon-base bx bx-time fs-5 text-primary"></i>
                        <span class="fw-bold font-13" style="color: #1e3a58;">
                            ${m.time_range || m.start_time}
                        </span>
                    </div>

                    <!-- Center in RTL: Title & Subtitle/Description -->
                    <div class="text-end flex-grow-1 px-3">
                        <div class="fw-bold text-heading font-14 text-truncate">${m.title}</div>
                        ${m.description ? `<div class="text-muted font-12 text-truncate mt-1">${m.description}</div>` : ''}
                    </div>

                    <!-- Left in RTL: Edit Pencil Action Button -->
                    <div>
                        <button type="button" class="btn btn-icon btn-sm text-muted p-0" title="{{ __('Edit') }}" onclick="event.stopPropagation(); editMeeting(${m.id})">
                            <i class="icon-base bx bx-edit-alt fs-5"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    // Refresh Sidebar data via AJAX
    function refreshSidebarData() {
        $.ajax({
            url: '{{ route("academic.meeting-calendar.sidebar-data") }}',
            type: 'GET',
            success: function (res) {
                if (res.success && res.data) {
                    renderSidebarMeetings(res.data);
                }
            }
        });
    }

    //------------------------------
    // Offcanvas Actions
    //------------------------------
    function openAddMeetingOffcanvas(eventDate = '') {
        $('#formMeeting')[0].reset();
        $('#meeting_id').val('');
        $('#offcanvasMeetingTitleText').text('{{ __("Add New Meeting") }}');
        $('#btnDeleteMeetingFromOffcanvas').addClass('d-none');

        selectColor('#0b3252');
        setDateVal('#event_date', eventDate || new Date().toISOString().slice(0, 10));

        // Default start time to next full hour
        const now = new Date();
        const nextHour = String((now.getHours() + 1) % 24).padStart(2, '0') + ':00';
        $('#start_time').val(nextHour);
        $('#end_time').val('');

        $('#status').val('planned');

        if (offcanvasMeetingInstance) {
            offcanvasMeetingInstance.show();
        }
    }

    //------------------------------
    window.editMeeting = function (id) {
        $.ajax({
            url: `{{ url("academic/meeting-calendar") }}/${id}`,
            type: 'GET',
            success: function (res) {
                if (res.success && res.data) {
                    const data = res.data;
                    $('#meeting_id').val(data.id);
                    $('#offcanvasMeetingTitleText').text('{{ __("Edit Meeting") }}');
                    $('#btnDeleteMeetingFromOffcanvas').removeClass('d-none');

                    $('#title').val(data.title);
                    setDateVal('#event_date', data.event_date);
                    $('#start_time').val(data.start_time || '');
                    $('#end_time').val(data.end_time || '');
                    $('#status').val(data.status || 'planned');
                    $('#description').val(data.description || '');

                    selectColor(data.color || '#0b3252');

                    if (offcanvasMeetingInstance) {
                        offcanvasMeetingInstance.show();
                    }
                }
            },
            error: function () {
                alert('{{ __("Failed to load meeting data.") }}');
            }
        });
    };

    //------------------------------
    $('#btnDeleteMeetingFromOffcanvas').on('click', function () {
        const id = $('#meeting_id').val();
        if (id) {
            deleteMeeting(id);
        }
    });

    //------------------------------
    function deleteMeeting(id) {
        if (!confirm('{{ __("Are you sure you want to delete this meeting?") }}')) {
            return;
        }

        $.ajax({
            url: `{{ url("academic/meeting-calendar") }}/${id}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            },
            success: function () {
                if (offcanvasMeetingInstance) {
                    offcanvasMeetingInstance.hide();
                }
                calendar.refetchEvents();
                refreshSidebarData();
            },
            error: function () {
                alert('{{ __("Failed to delete meeting.") }}');
            }
        });
    }

    //------------------------------
    function initMeetingForm() {
        $('#formMeeting').on('submit', function (e) {
            e.preventDefault();

            const form = $(this);
            const id = $('#meeting_id').val();
            const url = id ? `{{ url("academic/meeting-calendar") }}/${id}` : '{{ route("academic.meeting-calendar.store") }}';

            const btnSave = $('#btnSaveMeeting');
            const spinner = btnSave.find('.spinner-border');

            btnSave.prop('disabled', true);
            spinner.removeClass('d-none');

            const payload = form.serializeArray();
            if (id) {
                payload.push({ name: '_method', value: 'PUT' });
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: $.param(payload),
                success: function (res) {
                    btnSave.prop('disabled', false);
                    spinner.addClass('d-none');

                    if (res.success) {
                        if (offcanvasMeetingInstance) {
                            offcanvasMeetingInstance.hide();
                        }
                        calendar.refetchEvents();
                        refreshSidebarData();
                    } else {
                        alert(res.message || '{{ __("Error occurred.") }}');
                    }
                },
                error: function (xhr) {
                    btnSave.prop('disabled', false);
                    spinner.addClass('d-none');

                    let msg = '{{ __("Validation error:") }}\n';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        for (const key in xhr.responseJSON.errors) {
                            msg += xhr.responseJSON.errors[key].join(', ') + '\n';
                        }
                    } else {
                        msg = xhr.responseJSON?.message || '{{ __("Failed to save meeting.") }}';
                    }
                    alert(msg);
                }
            });
        });
    }
</script>
@endsection
