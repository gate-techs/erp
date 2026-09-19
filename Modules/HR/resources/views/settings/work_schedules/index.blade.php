@extends('layouts/contentNavbarLayout')

@section('title', __('Work Schedules') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Header -->
<div class="mb-4">
    <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
        <i class="icon-base bx bx-slider-alt text-primary font-22"></i>
        {{ __('HR Settings') }}
    </h4>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-3" role="alert">
        <i class="icon-base bx bx-check-circle me-2 font-20"></i>
        <div class="flex-grow-1">{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-3" role="alert">
        <i class="icon-base bx bx-error me-2 font-20"></i>
        <div class="flex-grow-1">{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- 2-Column Layout with Sub-Sidebar -->
<div class="row g-4">
    <!-- Sub-Sidebar Column -->
    <div class="col-12 col-md-4 col-lg-3">
        @include('hr::settings.partials.sidebar')
    </div>

    <!-- Main Content Column -->
    <div class="col-12 col-md-8 col-lg-9">
        <!-- Action Bar: Title & Add Button -->
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-3">
            <div>
                <h5 class="fw-bold mb-1 d-flex align-items-center gap-2 text-heading">
                    <i class="icon-base bx bx-calendar-event text-primary"></i>
                    {{ __('Work Schedules') }}
                </h5>
                <p class="text-muted mb-0 small">{{ __('Configure weekly shift plans, weekend off-days, and department schedules.') }}</p>
            </div>

            <div>
                <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm w-100 w-sm-auto" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddWorkSchedule" aria-controls="offcanvasAddWorkSchedule">
                    <i class="icon-base bx bx-plus font-18"></i>
                    <span>{{ __('Add New Work Schedule') }}</span>
                </button>
            </div>
        </div>

        <!-- Work Schedules Server-Side DataTable Card -->
        <div class="card shadow-sm border-0">
            <div class="card-datatable">
                <table id="workSchedulesTable" class="table table-hover border-top w-100 text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Arabic Name') }}</th>
                            <th>{{ __('English Name') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Working Days & Hours') }}</th>
                            <th>{{ __('Scope / Dept') }}</th>
                            <th>{{ __('Weekend Off-Days') }}</th>
                            <th>{{ __('Color') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Created Date') }}</th>
                            <th class="text-center" style="width: 100px;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loaded via DataTables Server-Side -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 1. Offcanvas: Add New Work Schedule                                       -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddWorkSchedule" aria-labelledby="offcanvasAddWorkScheduleLabel" style="width: 650px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddWorkScheduleLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-plus text-primary"></i>
            <span>{{ __('Add New Work Schedule') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form action="{{ route('hr.settings.work-schedules.store') }}" method="POST" id="addWorkScheduleForm">
            @csrf

            <!-- Code & Color -->
            <div class="row g-3 mb-3">
                <div class="col-sm-7">
                    <label class="form-label fw-semibold">{{ __('Schedule Code') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                    <input type="text" name="code" class="form-control" placeholder="{{ __('e.g. SCH-ADMIN-5D') }}">
                </div>
                <div class="col-sm-5">
                    <label class="form-label fw-semibold">{{ __('Badge Color') }}</label>
                    <div class="input-group">
                        <input type="color" name="color" class="form-control form-control-color w-100" value="#7367f0" title="{{ __('Choose schedule color') }}">
                    </div>
                </div>
            </div>

            <!-- Arabic Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Schedule Name [Arabic]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" class="form-control" placeholder="{{ __('e.g. دوام الإدارة العامة - 5 أيام') }}" required dir="rtl">
            </div>

            <!-- English Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Schedule Name [English]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" class="form-control" placeholder="{{ __('e.g. General Admin Schedule - 5 Days') }}" required dir="ltr">
            </div>

            <!-- Type, Department, Branch -->
            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <label class="form-label required fw-semibold">{{ __('Schedule Type') }} <span class="text-danger">*</span></label>
                    <select class="form-select select2" name="type" data-placeholder="{{ __('Select Type') }}" required>
                        @foreach ($types as $typeCase)
                            <option value="{{ $typeCase->value }}" {{ $typeCase->value === 'fixed' ? 'selected' : '' }}>
                                {{ $typeCase->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-semibold">{{ __('Target Department') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                    <select class="form-select select2" name="department_id" data-placeholder="{{ __('All Departments (Central)') }}" data-allow-clear="true">
                        <option value="">{{ __('All Departments (Central)') }}</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if ($branches->isNotEmpty())
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Branch') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                <select class="form-select select2" name="branch_id" data-placeholder="{{ __('All Branches') }}" data-allow-clear="true">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Defaults & Status -->
            <div class="row g-3 mb-3 align-items-center">
                <div class="col-sm-6">
                    <label class="form-label required fw-semibold">{{ __('Status') }} <span class="text-danger">*</span></label>
                    <select class="form-select select2" name="status" data-placeholder="{{ __('Select Status') }}" required>
                        @foreach ($statuses as $statusCase)
                            <option value="{{ $statusCase->value }}" {{ $statusCase->value === 'active' ? 'selected' : '' }}>
                                {{ $statusCase->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 pt-sm-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="add_is_default">
                        <label class="form-check-label fw-semibold" for="add_is_default">
                            {{ __('Set as Default Company Schedule') }}
                        </label>
                    </div>
                </div>
            </div>

            <!-- ============================================================= -->
            <!-- Days of the Week Configuration (Saturday to Friday)           -->
            <!-- ============================================================= -->
            <div class="mb-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="fw-bold text-heading mb-0 d-flex align-items-center gap-1">
                        <i class="icon-base bx bx-calendar-week text-primary"></i>
                        <span>{{ __('Weekly Days & Shifts Timetable') }}</span>
                    </h6>
                    <small class="text-muted">{{ __('Toggle weekend off-days and select shifts') }}</small>
                </div>

                <div class="border rounded p-2 bg-light-subtle">
                    @foreach ($daysList as $dayNum => $dayName)
                        @php
                            // By default, Friday (5) and Saturday (6) can be off-days or just Friday
                            $isDefaultOff = ($dayNum === 5); // Friday off by default
                        @endphp
                        <div class="card mb-2 shadow-none border schedule-day-row" data-day="{{ $dayNum }}">
                            <div class="card-body p-2 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
                                <!-- Day Name & Weekend Switch -->
                                <div class="d-flex align-items-center gap-3" style="min-width: 170px;">
                                    <span class="fw-bold text-heading font-14">{{ $dayName }}</span>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input day-off-switch" type="checkbox" name="days[{{ $dayNum }}][is_off_day]" value="1" id="add_off_{{ $dayNum }}" {{ $isDefaultOff ? 'checked' : '' }}>
                                        <label class="form-check-label small {{ $isDefaultOff ? 'text-warning fw-semibold' : 'text-muted' }}" for="add_off_{{ $dayNum }}">
                                            {{ __('Weekend / Off') }}
                                        </label>
                                    </div>
                                </div>

                                <!-- Shift Selection -->
                                <div class="flex-grow-1 day-shifts-container {{ $isDefaultOff ? 'd-none' : '' }}">
                                    <select class="form-select form-select-sm select2 day-shift-select" name="days[{{ $dayNum }}][shift_id]" data-placeholder="{{ __('Select Shift...') }}" {{ $isDefaultOff ? 'disabled' : '' }}>
                                        <option value="">{{ __('Select Shift...') }}</option>
                                        @foreach ($shifts as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }} ({{ \Carbon\Carbon::parse($s->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($s->end_time)->format('h:i A') }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Description Arabic -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Description [Arabic]') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                <textarea name="description_ar" class="form-control" rows="2" placeholder="{{ __('ملاحظات حول جدول ومواعيد الدوام...') }}" dir="rtl"></textarea>
            </div>

            <!-- Description English -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Description [English]') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                <textarea name="description_en" class="form-control" rows="2" placeholder="{{ __('Schedule notes or working conditions...') }}" dir="ltr"></textarea>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-check"></i>
                    <span>{{ __('Save Schedule') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit Work Schedule                                          -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditWorkSchedule" aria-labelledby="offcanvasEditWorkScheduleLabel" style="width: 650px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasEditWorkScheduleLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit-alt text-primary"></i>
            <span>{{ __('Edit Work Schedule') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form action="" method="POST" id="editWorkScheduleForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit_id">

            <!-- Code & Color -->
            <div class="row g-3 mb-3">
                <div class="col-sm-7">
                    <label class="form-label fw-semibold">{{ __('Schedule Code') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                    <input type="text" name="code" id="edit_code" class="form-control">
                </div>
                <div class="col-sm-5">
                    <label class="form-label fw-semibold">{{ __('Badge Color') }}</label>
                    <div class="input-group">
                        <input type="color" name="color" id="edit_color" class="form-control form-control-color w-100">
                    </div>
                </div>
            </div>

            <!-- Arabic Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Schedule Name [Arabic]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="edit_name_ar" class="form-control" required dir="rtl">
            </div>

            <!-- English Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Schedule Name [English]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" id="edit_name_en" class="form-control" required dir="ltr">
            </div>

            <!-- Type, Department, Branch -->
            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <label class="form-label required fw-semibold">{{ __('Schedule Type') }} <span class="text-danger">*</span></label>
                    <select class="form-select select2" name="type" id="edit_type" data-placeholder="{{ __('Select Type') }}" required>
                        @foreach ($types as $typeCase)
                            <option value="{{ $typeCase->value }}">{{ $typeCase->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-semibold">{{ __('Target Department') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                    <select class="form-select select2" name="department_id" id="edit_department_id" data-placeholder="{{ __('All Departments (Central)') }}" data-allow-clear="true">
                        <option value="">{{ __('All Departments (Central)') }}</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if ($branches->isNotEmpty())
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Branch') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                <select class="form-select select2" name="branch_id" id="edit_branch_id" data-placeholder="{{ __('All Branches') }}" data-allow-clear="true">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Defaults & Status -->
            <div class="row g-3 mb-3 align-items-center">
                <div class="col-sm-6">
                    <label class="form-label required fw-semibold">{{ __('Status') }} <span class="text-danger">*</span></label>
                    <select class="form-select select2" name="status" id="edit_status" data-placeholder="{{ __('Select Status') }}" required>
                        @foreach ($statuses as $statusCase)
                            <option value="{{ $statusCase->value }}">{{ $statusCase->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 pt-sm-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="edit_is_default">
                        <label class="form-check-label fw-semibold" for="edit_is_default">
                            {{ __('Set as Default Company Schedule') }}
                        </label>
                    </div>
                </div>
            </div>

            <!-- Days of the Week Configuration (Saturday to Friday) -->
            <div class="mb-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="fw-bold text-heading mb-0 d-flex align-items-center gap-1">
                        <i class="icon-base bx bx-calendar-week text-primary"></i>
                        <span>{{ __('Weekly Days & Shifts Timetable') }}</span>
                    </h6>
                    <small class="text-muted">{{ __('Toggle weekend off-days and select shifts') }}</small>
                </div>

                <div class="border rounded p-2 bg-light-subtle">
                    @foreach ($daysList as $dayNum => $dayName)
                        <div class="card mb-2 shadow-none border schedule-day-row" data-day="{{ $dayNum }}">
                            <div class="card-body p-2 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
                                <!-- Day Name & Weekend Switch -->
                                <div class="d-flex align-items-center gap-3" style="min-width: 170px;">
                                    <span class="fw-bold text-heading font-14">{{ $dayName }}</span>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input day-off-switch" type="checkbox" name="days[{{ $dayNum }}][is_off_day]" value="1" id="edit_off_{{ $dayNum }}">
                                        <label class="form-check-label small text-muted" for="edit_off_{{ $dayNum }}">
                                            {{ __('Weekend / Off') }}
                                        </label>
                                    </div>
                                </div>

                                <!-- Shift Selection -->
                                <div class="flex-grow-1 day-shifts-container">
                                    <select class="form-select form-select-sm select2 day-shift-select" name="days[{{ $dayNum }}][shift_id]" id="edit_shift_{{ $dayNum }}" data-placeholder="{{ __('Select Shift...') }}">
                                        <option value="">{{ __('Select Shift...') }}</option>
                                        @foreach ($shifts as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }} ({{ \Carbon\Carbon::parse($s->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($s->end_time)->format('h:i A') }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Description Arabic -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Description [Arabic]') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                <textarea name="description_ar" id="edit_description_ar" class="form-control" rows="2" dir="rtl"></textarea>
            </div>

            <!-- Description English -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Description [English]') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                <textarea name="description_en" id="edit_description_en" class="form-control" rows="2" dir="ltr"></textarea>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-check"></i>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 3. Modal: Delete Confirmation                                             -->
<!-- ========================================================================= -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="" method="POST" id="deleteWorkScheduleForm" class="modal-content border-0 shadow">
            @csrf
            @method('DELETE')
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title text-danger fw-bold d-flex align-items-center gap-2" id="deleteConfirmModalLabel">
                    <i class="icon-base bx bx-trash font-20"></i>
                    {{ __('Delete Work Schedule') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="avatar avatar-lg bg-label-danger mx-auto mb-3">
                    <i class="icon-base bx bx-error-circle fs-2"></i>
                </div>
                <h5 class="mb-2 fw-semibold">{{ __('Are you sure you want to delete this work schedule?') }}</h5>
                <p class="text-muted mb-0">
                    <span id="deleteItemName" class="fw-bold text-heading"></span>
                </p>
                <p class="text-muted small mt-1">{{ __('This action will remove the schedule and unlink its daily timetable configuration.') }}</p>
            </div>
            <div class="modal-footer border-top justify-content-center gap-2">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-danger">{{ __('Yes, Delete') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-script')
<script>
window.addEventListener('load', function () {
    const $ = window.jQuery || window.$;
    if (!$ || !$.fn.DataTable) {
        console.error('jQuery or DataTables not loaded');
        return;
    }

    const dataUrl = "{{ route('hr.settings.work-schedules.data') }}";
    const baseUrl = "{{ url('hr/settings/work-schedules') }}";

    // 1. Initialize Server-Side DataTable
    const table = $('#workSchedulesTable').DataTable({
        processing: true,
        serverSide: true,
        pagingType: 'simple_numbers',
        ajax: dataUrl,
        columns: [
            { 
                data: 'DT_RowIndex', 
                name: 'DT_RowIndex',
                orderable: false, 
                searchable: false,
                render: (data, type, row, meta) => `<strong class="text-muted">#${data || (meta.row + 1 + (meta.settings._iDisplayStart || 0))}</strong>`
            },
            { 
                data: 'code', 
                name: 'code',
                render: data => `<span class="badge bg-label-info font-monospace">${data || '-'}</span>`
            },
            { 
                data: 'name_ar', 
                name: 'name_ar',
                render: data => `<span class="fw-semibold text-heading">${data || '-'}</span>`
            },
            { 
                data: 'name_en', 
                name: 'name_en',
                render: data => `<span class="text-body">${data || '-'}</span>`
            },
            { 
                data: 'type_badge', 
                name: 'type',
                className: 'text-center'
            },
            { 
                data: 'working_days_info', 
                name: 'working_days_count',
                orderable: true
            },
            { 
                data: 'scope_info', 
                name: 'department_id',
                orderable: false
            },
            { 
                data: 'off_days_badge', 
                name: 'off_days',
                orderable: false,
                searchable: false
            },
            { 
                data: 'color_badge', 
                name: 'color',
                orderable: false,
                searchable: false
            },
            { 
                data: 'status', 
                name: 'status',
                className: 'text-center'
            },
            { 
                data: 'created_at', 
                name: 'created_at',
                render: data => `<small class="text-muted">${data || '-'}</small>`
            },
            {
                data: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-center'
            }
        ],
        order: [[10, 'desc']]
    });

    // Handle Weekend switch toggling in both Add and Edit offcanvases
    $(document).on('change', '.day-off-switch', function () {
        const isOff = $(this).is(':checked');
        const container = $(this).closest('.schedule-day-row').find('.day-shifts-container');
        const select = container.find('.day-shift-select');
        const label = $(this).siblings('label');

        if (isOff) {
            container.addClass('d-none');
            select.prop('disabled', true).val('').trigger('change');
            label.addClass('text-warning fw-semibold').removeClass('text-muted');
        } else {
            container.removeClass('d-none');
            select.prop('disabled', false);
            label.removeClass('text-warning fw-semibold').addClass('text-muted');
        }
    });

    // 2. Edit Action: Populate & Open Offcanvas
    const editOffcanvasEl = document.getElementById('offcanvasEditWorkSchedule');
    const editForm = document.getElementById('editWorkScheduleForm');

    $(document).on('click', '.btn-edit-schedule', function () {
        const raw = $(this).attr('data-row');
        if (!raw) return;

        const row = JSON.parse(raw);

        editForm.action = baseUrl + '/' + row.id;
        $('#edit_id').val(row.id);
        $('#edit_code').val(row.code || '');
        $('#edit_color').val(row.color || '#7367f0');
        $('#edit_name_ar').val(row.name_ar || '');
        $('#edit_name_en').val(row.name_en || '');
        $('#edit_description_ar').val(row.description_ar || '');
        $('#edit_description_en').val(row.description_en || '');
        $('#edit_is_default').prop('checked', !!row.is_default);

        if ($('#edit_type').length) {
            $('#edit_type').val(row.type || 'fixed').trigger('change');
        }
        if ($('#edit_department_id').length) {
            $('#edit_department_id').val(row.department_id || '').trigger('change');
        }
        if ($('#edit_branch_id').length) {
            $('#edit_branch_id').val(row.branch_id || '').trigger('change');
        }
        if ($('#edit_status').length) {
            $('#edit_status').val(row.status || 'active').trigger('change');
        }

        // Populate days
        const days = row.days || {};
        const allDayNums = [6, 0, 1, 2, 3, 4, 5];

        allDayNums.forEach(function (dayNum) {
            const dayData = days[dayNum];
            const switchEl = $('#edit_off_' + dayNum);
            const selectEl = $('#edit_shift_' + dayNum);
            const container = selectEl.closest('.day-shifts-container');
            const label = switchEl.siblings('label');

            if (dayData && dayData.is_off_day) {
                switchEl.prop('checked', true);
                container.addClass('d-none');
                selectEl.prop('disabled', true).val('').trigger('change');
                label.addClass('text-warning fw-semibold').removeClass('text-muted');
            } else {
                switchEl.prop('checked', false);
                container.removeClass('d-none');
                selectEl.prop('disabled', false);
                label.removeClass('text-warning fw-semibold').addClass('text-muted');
                if (dayData && dayData.shift_id) {
                    selectEl.val(dayData.shift_id).trigger('change');
                } else {
                    selectEl.val('').trigger('change');
                }
            }
        });

        if (editOffcanvasEl) {
            const editOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(editOffcanvasEl);
            editOffcanvas.show();
        }
    });

    // 3. Delete Action: Confirmation Modal
    const deleteModalEl = document.getElementById('deleteConfirmModal');
    const deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;
    const deleteForm = document.getElementById('deleteWorkScheduleForm');
    const deleteTargetName = document.getElementById('deleteItemName');

    $(document).on('click', '.btn-delete-schedule', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');

        deleteForm.action = baseUrl + '/' + id;
        deleteTargetName.textContent = name || '';

        if (deleteModal) {
            deleteModal.show();
        }
    });

    // Initialize Select2 in offcanvases
    function setupSelect2(container = document) {
        if (typeof window.initSelect2 === 'function') {
            window.initSelect2(container);
        } else if ($.fn.select2) {
            $(container).find('.select2').each(function () {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2({
                        placeholder: $(this).data('placeholder') || '',
                        allowClear: $(this).data('allow-clear') !== false,
                        width: '100%',
                        dropdownParent: $(this).closest('.offcanvas').length ? $(this).closest('.offcanvas') : $(document.body)
                    });
                }
            });
        }
    }

    setupSelect2();

    $(document).on('shown.bs.offcanvas', function (e) {
        setupSelect2(e.target);
    });
});
</script>
@endsection
