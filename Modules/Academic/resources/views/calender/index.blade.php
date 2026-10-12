@extends('layouts/contentNavbarLayout')

@section('title', __('App Calendar') . ' - ' . config('variables.templateName'))

@section('page-style')
{{-- Global FullCalendar styling is defined in resources/css/app.css --}}
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
                <!-- 1. Nursery -->
                <div class="col-12">
                    <label class="form-label required fw-medium">{{ __('Nursery') }}</label>
                    <select class="form-select select2 select2-modal" id="modal_institution_id" name="institution_id" required>
                        @foreach ($institutions as $inst)
                            <option value="{{ $inst->id }}">{{ $inst->translate(app()->getLocale())?->name ?? $inst->translate('ar')?->name ?? $inst->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Event Name AR -->
                <div class="col-12">
                    <label class="form-label required fw-medium">{{ __('Event Name (Arabic)') }}</label>
                    <input type="text" class="form-control" id="title_ar" name="title_ar" required>
                </div>

                <!-- 2. Event Name EN -->
                <div class="col-12">
                    <label class="form-label required fw-medium">{{ __('Event Name (English)') }}</label>
                    <input type="text" class="form-control" id="title_en" name="title_en" required>
                </div>

                <!-- 3. Start Date -->
                <div class="col-12 col-md-6">
                    <label class="form-label required fw-medium">{{ __('Start Date') }}</label>
                    <input type="text" class="form-control js-flatpickr-date flatpickr-input active" id="start_date" name="start_date" required>
                </div>

                <!-- 3. End Date -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-medium">{{ __('End Date') }}</label>
                    <input type="text" class="form-control js-flatpickr-date flatpickr-input active" id="end_date" name="end_date">
                    <small class="text-muted d-block mt-1">{{ __('Optional') }}</small>
                </div>

                <!-- 4. Description AR -->
                <div class="col-12">
                    <label class="form-label fw-medium">{{ __('Description (Arabic)') }}</label>
                    <textarea class="form-control" id="description_ar" name="description_ar" rows="3"></textarea>
                </div>

                <!-- 4. Description EN -->
                <div class="col-12">
                    <label class="form-label fw-medium">{{ __('Description (English)') }}</label>
                    <textarea class="form-control" id="description_en" name="description_en" rows="3"></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-4 mt-4 border-top">
                <button type="button" class="btn btn-outline-danger d-none" id="btnDeleteEventFromOffcanvas">
                    <i class="icon-base bx bx-trash me-1"></i>
                    <span>{{ __('Delete') }}</span>
                </button>
                <div class="d-flex gap-2 ms-auto">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveEvent">
                        <span class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                        <span>{{ __('Save') }}</span>
                    </button>
                </div>
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
            offcanvasEl.addEventListener('shown.bs.offcanvas', function () {
                initSelect2();
            });
        }

        initSelect2();
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
    function initSelect2() {
        if (window.jQuery && $.fn.select2) {
            $('#modal_institution_id').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#offcanvasEvent'),
                width: '100%',
                dir: (document.documentElement.dir === 'rtl' || $('html').attr('dir') === 'rtl') ? 'rtl' : 'ltr'
            });
        }
    }

    //------------------------------
    function initCalendar() {
        const calendarEl = document.getElementById('calendar');

        calendar = window.createAppCalendar(calendarEl, {
            customButtons: {
                addEventButton: {
                    text: '+ ' + '{{ __("Add Event") }}',
                    click: function () {
                        openAddEventOffcanvas();
                    }
                }
            },

            // Fetch Events via AJAX
            events: function (fetchInfo, successCallback, failureCallback) {
                $.ajax({
                    url: '{{ route("academic.calender.calendar-data") }}',
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

            // Click on event -> open edit modal directly
            eventClick: function (info) {
                info.jsEvent.preventDefault();
                editEvent(info.event.id);
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
            setDateVal('#start_date', startDate);
        } else {
            setDateVal('#start_date', new Date().toISOString().slice(0, 10));
        }

        if (endDate) {
            const startD = new Date(startDate);
            const endD = new Date(endDate);
            const diffDays = Math.round((endD - startD) / (1000 * 60 * 60 * 24));
            if (diffDays > 1) {
                const adjEnd = new Date(endD);
                adjEnd.setDate(adjEnd.getDate() - 1);
                setDateVal('#end_date', adjEnd.toISOString().slice(0, 10));
            } else {
                setDateVal('#end_date', '');
            }
        } else {
            setDateVal('#end_date', '');
        }

        const firstInst = $('#modal_institution_id option:first').val();
        if (firstInst) {
            $('#modal_institution_id').val(firstInst).trigger('change');
        }

        $('#btnDeleteEventFromOffcanvas').addClass('d-none');

        if (offcanvasEventInstance) {
            offcanvasEventInstance.show();
        }
        initSelect2();
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
    $('#btnDeleteEventFromOffcanvas').on('click', function () {
        const id = $('#event_id').val();
        if (id) {
            deleteEvent(id);
        }
    });

    //------------------------------
    function editEvent(id) {
        $.ajax({
            url: `{{ url("academic/calender") }}/${id}`,
            type: 'GET',
            success: function (res) {
                if (res.success && res.data) {
                    const data = res.data;
                    $('#event_id').val(data.id);
                    $('#offcanvasEventTitleText').text('{{ __("Edit Event") }}');
                    $('#btnDeleteEventFromOffcanvas').removeClass('d-none');

                    $('#modal_institution_id').val(data.institution_id).trigger('change');
                    $('#title_ar').val(data.title_ar);
                    $('#title_en').val(data.title_en);
                    setDateVal('#start_date', data.start_date);
                    setDateVal('#end_date', data.end_date || '');
                    $('#description_ar').val(data.description_ar);
                    $('#description_en').val(data.description_en);

                    if (offcanvasEventInstance) {
                        offcanvasEventInstance.show();
                    }
                    initSelect2();
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
            url: `{{ url("academic/calender") }}/${id}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            },
            success: function (res) {
                if (offcanvasEventInstance) {
                    offcanvasEventInstance.hide();
                }
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
            const url = id ? `{{ url("academic/calender") }}/${id}` : '{{ route("academic.calender.store") }}';

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
