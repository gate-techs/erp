@extends('layouts/contentNavbarLayout')

@section('title', __('App Calendar') . ' - ' . config('variables.templateName'))

@section('page-style')
<style>
    /* FullCalendar Sneat Theme Customization */
    .fc-theme-standard th {
        background-color: #fbfbfb;
        color: #566a7f;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 10px 0;
        text-align: center;
        border-color: #e7e7e8 !important;
    }

    .fc-theme-standard td,
    .fc-theme-standard .fc-scrollgrid {
        border-color: #eceef1 !important;
    }

    .fc .fc-toolbar {
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        margin-bottom: 1.5rem !important;
    }

    .fc .fc-toolbar-title {
        font-size: 1.35rem;
        font-weight: 600;
        color: #566a7f;
        margin: 0 12px !important;
    }

    /* Primary Add Event Button */
    .fc .fc-addEventButton-button {
        background-color: #0b3252 !important;
        border-color: #0b3252 !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        padding: 0.45rem 1.25rem !important;
        border-radius: 0.375rem !important;
        font-size: 0.9rem !important;
        box-shadow: 0 2px 4px rgba(11, 50, 82, 0.2);
        transition: all 0.2s ease;
        margin-inline-end: 10px !important;
    }

    .fc .fc-addEventButton-button:hover {
        background-color: #08253d !important;
        border-color: #08253d !important;
    }

    /* Views Buttons Group (Month, Week, Day, List) */
    .fc .fc-button-group {
        border-radius: 0.375rem;
        overflow: hidden;
        border: 1px solid #d9dee3;
    }

    .fc .fc-button-group > .fc-button {
        background-color: #b5c7d6 !important;
        border: none !important;
        color: #2b435b !important;
        font-weight: 500 !important;
        font-size: 0.85rem !important;
        padding: 0.45rem 0.9rem !important;
        text-transform: capitalize;
        border-radius: 0 !important;
        box-shadow: none !important;
        transition: background-color 0.2s;
    }

    .fc .fc-button-group > .fc-button:not(:last-child) {
        border-inline-end: 1px solid #9eb3c4 !important;
    }

    .fc .fc-button-group > .fc-button.fc-button-active,
    .fc .fc-button-group > .fc-button:hover {
        background-color: #9ab4c7 !important;
        color: #172d42 !important;
        font-weight: 600 !important;
    }

    /* Prev & Next Navigation Buttons */
    .fc .fc-prev-button,
    .fc .fc-next-button {
        background: transparent !important;
        border: none !important;
        color: #566a7f !important;
        font-size: 1.1rem !important;
        padding: 0.25rem 0.5rem !important;
        box-shadow: none !important;
    }

    .fc .fc-prev-button:hover,
    .fc .fc-next-button:hover {
        color: #0b3252 !important;
    }

    .fc .fc-daygrid-day-number {
        font-size: 0.85rem;
        color: #8592a3;
        padding: 6px 10px;
        font-weight: 500;
        text-decoration: none !important;
    }

    .fc .fc-day-today {
        background-color: rgba(105, 108, 255, 0.04) !important;
    }

    .fc .fc-daygrid-day-top {
        flex-direction: row-reverse;
    }

    /* Event Badges & Spanned Bars */
    .fc-event {
        cursor: pointer;
        border-radius: 4px;
        padding: 2px 6px;
        font-size: 0.8rem;
        border: none !important;
        font-weight: 500;
        margin-bottom: 2px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .fc-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
    }

    .fc-h-event {
        border-radius: 4px;
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
                <li class="breadcrumb-item text-muted fw-medium small">{{ __('Schedules & Events') }}</li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('App Calendar') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-calendar-event text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('App Calendar') }}</h4>
            <span class="badge bg-label-primary ms-2">{{ __('Events') }}</span>
        </div>
    </div>
</div>

<!-- Calendar Card -->
<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <div id="calendar"></div>
    </div>
</div>

<!-- ============================================================== -->
<!-- Add / Edit Event (Modal جانبي - Offcanvas Drawer) -->
<!-- ============================================================== -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEvent" aria-labelledby="offcanvasEventLabel" style="width: 480px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 class="offcanvas-title fw-bold" id="offcanvasEventLabel">
            <i class="icon-base bx bx-calendar-plus text-primary me-2"></i>
            <span id="offcanvasEventTitleText">{{ __('Add Event') }}</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="formEvent">
            @csrf
            <input type="hidden" id="event_id" name="id" value="">

            <div class="row g-3">
                <!-- Event Name AR -->
                <div class="col-12">
                    <label class="form-label required fw-medium">{{ __('Event Name (Arabic)') }}</label>
                    <input type="text" class="form-control" id="title_ar" name="title_ar" placeholder="مثال: أسبوع التشجير" required>
                </div>

                <!-- Event Name EN -->
                <div class="col-12">
                    <label class="form-label required fw-medium">{{ __('Event Name (English)') }}</label>
                    <input type="text" class="form-control" id="title_en" name="title_en" placeholder="e.g. Greening Week" required>
                </div>

                <!-- Start Date -->
                <div class="col-12 col-md-6">
                    <label class="form-label required fw-medium">{{ __('Date') }}</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                </div>

                <!-- End Date -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-medium">{{ __('End Date') }}</label>
                    <input type="date" class="form-control" id="end_date" name="end_date">
                    <small class="text-muted d-block mt-1">{{ __('Optional') }}</small>
                </div>

                <!-- Institution -->
                <div class="col-12">
                    <label class="form-label required fw-medium">{{ __('Educational Institution / Nursery') }}</label>
                    <select class="form-select select2-modal" id="modal_institution_id" name="institution_id" required>
                        <option value="">{{ __('Select Institution') }}</option>
                        @foreach ($institutions as $inst)
                            <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div class="col-12">
                    <label class="form-label fw-medium">{{ __('Status') }}</label>
                    <select class="form-select" id="modal_status" name="status">
                        <option value="active">{{ __('Active') }}</option>
                        <option value="inactive">{{ __('Inactive') }}</option>
                    </select>
                </div>

                <!-- Description AR -->
                <div class="col-12">
                    <label class="form-label fw-medium">{{ __('Description (Arabic)') }}</label>
                    <textarea class="form-control" id="description_ar" name="description_ar" rows="3" placeholder="وصف تفصيلي للحدث..."></textarea>
                </div>

                <!-- Description EN -->
                <div class="col-12">
                    <label class="form-label fw-medium">{{ __('Description (English)') }}</label>
                    <textarea class="form-control" id="description_en" name="description_en" rows="3" placeholder="Detailed event description..."></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-4 mt-4 border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary" id="btnSaveEvent">
                    <span class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                    <span>{{ __('Save') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- View Event Details Modal -->
<!-- ============================================================== -->
<div class="modal fade" id="modalViewEvent" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span style="width: 12px; height: 12px; border-radius: 50%; background-color: #ef4444; display: inline-block;"></span>
                    <h5 class="modal-title fw-bold mb-0" id="viewEventTitle"></h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-center gap-2 text-muted">
                        <i class="icon-base bx bx-calendar font-18 text-primary"></i>
                        <span id="viewEventDates" class="fw-semibold text-heading"></span>
                    </div>

                    <div id="viewEventInstitutionRow" class="d-flex align-items-center gap-2 text-muted">
                        <i class="icon-base bx bx-buildings font-18 text-primary"></i>
                        <span id="viewEventInstitution"></span>
                    </div>

                    <div class="border-top pt-3">
                        <label class="fw-semibold text-heading small mb-1">{{ __('Description') }}:</label>
                        <p id="viewEventDescription" class="mb-0 text-muted" style="white-space: pre-wrap;"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between border-top">
                <button type="button" class="btn btn-outline-danger btn-sm" id="btnDeleteEventFromView">
                    <i class="icon-base bx bx-trash font-16 me-1"></i>
                    <span>{{ __('Delete') }}</span>
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-primary btn-sm" id="btnEditEventFromView">
                        <i class="icon-base bx bx-edit font-16 me-1"></i>
                        <span>{{ __('Edit') }}</span>
                    </button>
                </div>
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
    let calendar = null;
    let currentEventData = null;
    let offcanvasEventInstance = null;
    const currentLocale = '{{ app()->getLocale() }}';

    document.addEventListener('DOMContentLoaded', function () {
        const offcanvasEl = document.getElementById('offcanvasEvent');
        if (offcanvasEl) {
            offcanvasEventInstance = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
        }

        initSelect2();
        initCalendar();
        initEventForm();
    });

    //------------------------------
    function initSelect2() {
        if ($.fn.select2) {
            $('.select2-modal').select2({
                dropdownParent: $('#offcanvasEvent'),
                width: '100%'
            });
        }
    }

    //------------------------------
    function initCalendar() {
        const calendarEl = document.getElementById('calendar');

        calendar = new FullCalendar.Calendar(calendarEl, {
            locale: currentLocale === 'ar' ? 'ar' : 'en',
            direction: currentLocale === 'ar' ? 'rtl' : 'ltr',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next title',
                center: '',
                right: 'addEventButton dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            customButtons: {
                addEventButton: {
                    text: '+ ' + (currentLocale === 'ar' ? 'اضف حدث' : 'Add Event'),
                    click: function () {
                        openAddEventOffcanvas();
                    }
                }
            },
            buttonText: {
                today: currentLocale === 'ar' ? 'اليوم' : 'Today',
                month: 'Month',
                week: 'Week',
                day: 'Day',
                list: 'List'
            },
            height: 'auto',
            navLinks: true,
            dayMaxEvents: 4,
            editable: false,
            selectable: true,
            selectMirror: true,

            // Fetch Events via AJAX
            events: function (fetchInfo, successCallback, failureCallback) {
                $.ajax({
                    url: '{{ route("academic.events.calendar-data") }}',
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

            // Click on date -> open side modal
            select: function (selectionInfo) {
                openAddEventOffcanvas(selectionInfo.startStr, selectionInfo.endStr);
                calendar.unselect();
            },

            // Click on event -> view details modal
            eventClick: function (info) {
                info.jsEvent.preventDefault();
                showEventDetails(info.event);
            },

            // Customize event render
            eventContent: function (arg) {
                const titleText = arg.event.title;

                const container = document.createElement('div');
                container.className = 'd-flex align-items-center gap-1 text-truncate w-100 px-1 py-1 rounded';
                container.style.backgroundColor = '#fee2e2';
                container.style.color = '#dc2626';
                container.style.fontSize = '0.78rem';
                container.style.border = '1px solid #fecaca';

                container.innerHTML = `
                    <span style="width: 6px; height: 6px; border-radius: 50%; background-color: #ef4444; display: inline-block; flex-shrink: 0;"></span>
                    <span class="text-truncate fw-semibold">${titleText}</span>
                `;

                return { domNodes: [container] };
            }
        });

        calendar.render();
    }

    //------------------------------
    function openAddEventOffcanvas(startDate = '', endDate = '') {
        $('#formEvent')[0].reset();
        $('#event_id').val('');
        $('#offcanvasEventTitleText').text('{{ __("Add Event") }}');

        if (startDate) {
            $('#start_date').val(startDate);
        } else {
            $('#start_date').val(new Date().toISOString().slice(0, 10));
        }

        if (endDate) {
            const startD = new Date(startDate);
            const endD = new Date(endDate);
            const diffDays = Math.round((endD - startD) / (1000 * 60 * 60 * 24));
            if (diffDays > 1) {
                const adjEnd = new Date(endD);
                adjEnd.setDate(adjEnd.getDate() - 1);
                $('#end_date').val(adjEnd.toISOString().slice(0, 10));
            } else {
                $('#end_date').val('');
            }
        } else {
            $('#end_date').val('');
        }

        const firstInst = $('#modal_institution_id option:nth-child(2)').val();
        if (firstInst) {
            $('#modal_institution_id').val(firstInst).trigger('change');
        }

        if (offcanvasEventInstance) {
            offcanvasEventInstance.show();
        }
    }

    //------------------------------
    function showEventDetails(eventObj) {
        currentEventData = eventObj;
        const props = eventObj.extendedProps;

        $('#viewEventTitle').text(eventObj.title);

        let dateStr = props.start_date;
        if (props.end_date && props.end_date !== props.start_date) {
            dateStr += ' {{ __("to") }} ' + props.end_date;
        }
        $('#viewEventDates').text(dateStr);

        let instText = props.institution_name || '';
        if (instText) {
            $('#viewEventInstitution').text(instText);
            $('#viewEventInstitutionRow').removeClass('d-none');
        } else {
            $('#viewEventInstitutionRow').addClass('d-none');
        }

        const desc = (currentLocale === 'ar' ? (props.description_ar || props.description_en) : (props.description_en || props.description_ar)) || '-';
        $('#viewEventDescription').text(desc);

        $('#modalViewEvent').modal('show');
    }

    //------------------------------
    $('#btnEditEventFromView').on('click', function () {
        if (!currentEventData) return;
        $('#modalViewEvent').modal('hide');
        editEvent(currentEventData.id);
    });

    //------------------------------
    $('#btnDeleteEventFromView').on('click', function () {
        if (!currentEventData) return;
        deleteEvent(currentEventData.id);
    });

    //------------------------------
    function editEvent(id) {
        $.ajax({
            url: `{{ url("academic/events") }}/${id}`,
            type: 'GET',
            success: function (res) {
                if (res.success && res.data) {
                    const data = res.data;
                    $('#event_id').val(data.id);
                    $('#offcanvasEventTitleText').text('{{ __("Edit Event") }}');

                    $('#title_ar').val(data.title_ar);
                    $('#title_en').val(data.title_en);
                    $('#description_ar').val(data.description_ar);
                    $('#description_en').val(data.description_en);
                    $('#start_date').val(data.start_date);
                    $('#end_date').val(data.end_date || '');
                    $('#modal_status').val(data.status || 'active');

                    $('#modal_institution_id').val(data.institution_id).trigger('change');

                    if (offcanvasEventInstance) {
                        offcanvasEventInstance.show();
                    }
                }
            },
            error: function () {
                alert('{{ __("Failed to load event data.") }}');
            }
        });
    }

    //------------------------------
    function deleteEvent(id) {
        if (!confirm('{{ __("Are you sure you want to delete this event?") }}')) {
            return;
        }

        $.ajax({
            url: `{{ url("academic/events") }}/${id}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            },
            success: function (res) {
                $('#modalViewEvent').modal('hide');
                calendar.refetchEvents();
            },
            error: function () {
                alert('{{ __("Failed to delete event.") }}');
            }
        });
    }

    //------------------------------
    function initEventForm() {
        $('#formEvent').on('submit', function (e) {
            e.preventDefault();

            const form = $(this);
            const id = $('#event_id').val();
            const url = id ? `{{ url("academic/events") }}/${id}` : '{{ route("academic.events.store") }}';

            const btnSave = $('#btnSaveEvent');
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
                        if (offcanvasEventInstance) {
                            offcanvasEventInstance.hide();
                        }
                        calendar.refetchEvents();
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
                        msg = xhr.responseJSON?.message || '{{ __("Error saving event.") }}';
                    }
                    alert(msg);
                }
            });
        });
    }
</script>
@endsection
