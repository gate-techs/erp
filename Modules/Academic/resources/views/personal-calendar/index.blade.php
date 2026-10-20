@extends('layouts/contentNavbarLayout')

@section('title', __('Personal Calendar') . ' - ' . config('variables.templateName'))

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
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Personal Calendar') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-calendar-star text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Personal Calendar') }}</h4>
            <span class="badge bg-label-info ms-2">{{ __('My Schedule') }}</span>
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
<!-- Add / Edit Personal Event Offcanvas Drawer -->
<!-- ============================================================== -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasPersonalEvent" aria-labelledby="offcanvasPersonalEventLabel" style="width: 480px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 class="offcanvas-title fw-bold" id="offcanvasPersonalEventLabel">
            <i class="icon-base bx bx-calendar-plus text-primary me-2"></i>
            <span id="offcanvasPersonalEventTitleText">{{ __('Add Personal Event') }}</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="formPersonalEvent">
            @csrf
            <input type="hidden" id="event_id" name="id" value="">

            <div class="row g-3">
                <!-- 1. Event Title -->
                <div class="col-12">
                    <label class="form-label required fw-medium">{{ __('Personal Event Title') }}</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>

                <!-- 3. Date -->
                <div class="col-12">
                    <label class="form-label required fw-medium">{{ __('Date') }}</label>
                    <input type="text" class="form-control js-flatpickr-date flatpickr-input active" id="event_date" name="event_date" required>
                </div>

                <!-- 4. All Day Switch -->
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_all_day" name="is_all_day" value="1">
                        <label class="form-check-label fw-medium" for="is_all_day">{{ __('All Day') }}</label>
                    </div>
                </div>

                <!-- 5. Time Range (Start Time & End Time) -->
                <div class="col-12 col-md-6 time-fields-row">
                    <label class="form-label fw-medium">{{ __('Start Time') }}</label>
                    <input type="time" class="form-control" id="start_time" name="start_time">
                </div>

                <div class="col-12 col-md-6 time-fields-row">
                    <label class="form-label fw-medium">{{ __('End Time') }}</label>
                    <input type="time" class="form-control" id="end_time" name="end_time">
                </div>

                <!-- 6. Status -->
                <div class="col-12">
                    <label class="form-label fw-medium">{{ __('Status') }}</label>
                    <select class="form-select" id="status" name="status">
                        @foreach ($statuses as $val => $lbl)
                            <option value="{{ $val }}">{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 7. Color Picker -->
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

                <!-- 8. Description -->
                <div class="col-12">
                    <label class="form-label fw-medium">{{ __('Description') }}</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-4 mt-4 border-top">
                @can('academic.personal-calendar.delete')
                <button type="button" class="btn btn-outline-danger d-none" id="btnDeleteEventFromOffcanvas">
                    <i class="icon-base bx bx-trash me-1"></i>
                    <span>{{ __('Delete') }}</span>
                </button>
                @endcan
                <div class="d-flex gap-2 ms-auto">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                    @canany(['academic.personal-calendar.create', 'academic.personal-calendar.edit'])
                    <button type="submit" class="btn btn-primary" id="btnSavePersonalEvent">
                        <span class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                        <span>{{ __('Save') }}</span>
                    </button>
                    @endcanany
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
    let offcanvasEventInstance = null;
    const currentLocale = '{{ app()->getLocale() }}';

    document.addEventListener('DOMContentLoaded', function () {
        const offcanvasEl = document.getElementById('offcanvasPersonalEvent');
        if (offcanvasEl) {
            offcanvasEventInstance = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
        }

        initColorPickers();
        initAllDayToggle();
        initDatePickers();
        initCalendar();
        initEventForm();
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
    function initAllDayToggle() {
        $('#is_all_day').on('change', function () {
            if ($(this).is(':checked')) {
                $('.time-fields-row').addClass('d-none');
                $('#start_time').val('');
                $('#end_time').val('');
            } else {
                $('.time-fields-row').removeClass('d-none');
            }
        });
    }

    //------------------------------
    function initCalendar() {
        const calendarEl = document.getElementById('calendar');

        const canCreate = {{ auth()->user()->can('academic.personal-calendar.create') ? 'true' : 'false' }};
        const canEdit = {{ auth()->user()->can('academic.personal-calendar.edit') ? 'true' : 'false' }};

        const customButtonsConfig = canCreate ? {
            addEventButton: {
                text: '+ ' + '{{ __("Add Personal Event") }}',
                click: function () {
                    openAddEventOffcanvas();
                }
            }
        } : {};

        calendar = window.createAppCalendar(calendarEl, {
            customButtons: customButtonsConfig,

            // Fetch Personal Events via AJAX
            events: function (fetchInfo, successCallback, failureCallback) {
                $.ajax({
                    url: '{{ route("academic.personal-calendar.calendar-data") }}',
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
                if (canCreate) {
                    openAddEventOffcanvas(selectionInfo.startStr);
                }
                calendar.unselect();
            },

            // Click on event -> open edit modal directly
            eventClick: function (info) {
                info.jsEvent.preventDefault();
                if (canEdit) {
                    editEvent(info.event.id);
                }
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

        calendar.render();
    }

    //------------------------------
    function openAddEventOffcanvas(eventDate = '') {
        $('#formPersonalEvent')[0].reset();
        $('#event_id').val('');
        $('#offcanvasPersonalEventTitleText').text('{{ __("Add Personal Event") }}');
        $('#btnDeleteEventFromOffcanvas').addClass('d-none');
        $('#is_all_day').prop('checked', false);
        $('.time-fields-row').removeClass('d-none');

        selectColor('#0b3252');
        setDateVal('#event_date', eventDate || new Date().toISOString().slice(0, 10));

        $('#status').val('planned');

        if (offcanvasEventInstance) {
            offcanvasEventInstance.show();
        }
    }

    //------------------------------
    function editEvent(id) {
        $.ajax({
            url: `{{ url("academic/personal-calendar") }}/${id}`,
            type: 'GET',
            success: function (res) {
                if (res.success && res.data) {
                    const data = res.data;
                    $('#event_id').val(data.id);
                    $('#offcanvasPersonalEventTitleText').text('{{ __("Edit Personal Event") }}');
                    $('#btnDeleteEventFromOffcanvas').removeClass('d-none');

                    $('#title').val(data.title);
                    setDateVal('#event_date', data.event_date);
                    $('#description').val(data.description || '');

                    $('#is_all_day').prop('checked', data.is_all_day);
                    if (data.is_all_day) {
                        $('.time-fields-row').addClass('d-none');
                        $('#start_time').val('');
                        $('#end_time').val('');
                    } else {
                        $('.time-fields-row').removeClass('d-none');
                        $('#start_time').val(data.start_time || '');
                        $('#end_time').val(data.end_time || '');
                    }

                    $('#status').val(data.status || 'planned');
                    selectColor(data.color || '#0b3252');

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
    $('#btnDeleteEventFromOffcanvas').on('click', function () {
        const id = $('#event_id').val();
        if (id) {
            deleteEvent(id);
        }
    });

    //------------------------------
    function deleteEvent(id) {
        if (!confirm('{{ __("Are you sure you want to delete this event?") }}')) {
            return;
        }

        $.ajax({
            url: `{{ url("academic/personal-calendar") }}/${id}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            },
            success: function () {
                if (offcanvasEventInstance) {
                    offcanvasEventInstance.hide();
                }
                calendar.refetchEvents();
            },
            error: function () {
                alert('{{ __("Failed to delete personal event.") }}');
            }
        });
    }

    //------------------------------
    function initEventForm() {
        $('#formPersonalEvent').on('submit', function (e) {
            e.preventDefault();

            const form = $(this);
            const id = $('#event_id').val();
            const url = id ? `{{ url("academic/personal-calendar") }}/${id}` : '{{ route("academic.personal-calendar.store") }}';

            const btnSave = $('#btnSavePersonalEvent');
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
