@extends('layouts/contentNavbarLayout')

@section('title', __('Official Holidays') . ' - ' . config('variables.templateName'))

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
        <!-- Action Bar: Title, Year Filter & Add Button -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
            <div>
                <h5 class="fw-bold mb-1 d-flex align-items-center gap-2 text-heading">
                    <i class="icon-base bx bx-sun text-primary"></i>
                    {{ __('Official Holidays') }}
                </h5>
                <p class="text-muted mb-0 small">{{ __('Manage national holidays, religious feasts, and company official days off.') }}</p>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Year Filter -->
                <select id="filter_year" class="form-select select2 form-select" style="min-width: 120px;" data-placeholder="{{ __('All Years') }}" data-allow-clear="true">
                    <option value="">{{ __('All Years') }}</option>
                    @foreach ($years as $yr)
                        <option value="{{ $yr }}" {{ $yr == date('Y') ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>

                <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm text-nowrap" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddHoliday" aria-controls="offcanvasAddHoliday">
                    <i class="icon-base bx bx-plus font-18"></i>
                    <span>{{ __('Add New Holiday') }}</span>
                </button>
            </div>
        </div>

        <!-- Official Holidays Server-Side DataTable Card -->
        <div class="card shadow-sm border-0">
            <div class="card-datatable">
                <table id="officialHolidaysTable" class="table table-hover border-top w-100 text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>{{ __('Arabic Name') }}</th>
                            <th>{{ __('English Name') }}</th>
                            <th>{{ __('Holiday Dates & Duration') }}</th>
                            <th>{{ __('Year') }}</th>
                            <th>{{ __('Recurring') }}</th>
                            <th>{{ __('Branch') }}</th>
                            <th>{{ __('Color') }}</th>
                            <th>{{ __('Status') }}</th>
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
<!-- 1. Offcanvas: Add New Official Holiday                                    -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddHoliday" aria-labelledby="offcanvasAddHolidayLabel" style="width: 520px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddHolidayLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-plus text-primary"></i>
            <span>{{ __('Add New Holiday') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form action="{{ route('hr.settings.official-holidays.store') }}" method="POST" id="addHolidayForm">
            @csrf

            <!-- Arabic Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Holiday Name [Arabic]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" class="form-control" placeholder="{{ __('e.g. عطلة عيد الفطر المبارك / اليوم الوطني') }}" required dir="rtl">
            </div>

            <!-- English Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Holiday Name [English]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" class="form-control" placeholder="{{ __('e.g. Eid Al-Fitr Holiday / National Day') }}" required dir="ltr">
            </div>

            <!-- Date Range (Start Date & End Date) -->
            <div class="p-3 bg-label-secondary rounded mb-3">
                <h6 class="fw-bold mb-2 text-heading d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-calendar text-primary"></i>
                    <span>{{ __('Holiday Dates & Duration') }}</span>
                </h6>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label required fw-semibold">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="add_start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label required fw-semibold">{{ __('End Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" id="add_end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="mt-2 text-end">
                    <span class="badge bg-primary fs-7" id="add_days_counter">1 {{ __('day') }}</span>
                </div>
            </div>

            <!-- Branch & Color -->
            <div class="row g-3 mb-3">
                @if ($branches->isNotEmpty())
                <div class="col-sm-7">
                    <label class="form-label fw-semibold">{{ __('Branch') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                    <select class="form-select select2" name="branch_id" data-placeholder="{{ __('All Branches (Central)') }}" data-allow-clear="true">
                        <option value="">{{ __('All Branches (Central)') }}</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-5">
                @else
                <div class="col-12">
                @endif
                    <label class="form-label fw-semibold">{{ __('Badge Color') }}</label>
                    <div class="input-group">
                        <input type="color" name="color" class="form-control form-control-color w-100" value="#e83e8c" title="{{ __('Choose holiday color') }}">
                    </div>
                </div>
            </div>

            <!-- Recurring Annually & Status -->
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
                        <input class="form-check-input" type="checkbox" name="is_recurring" value="1" id="add_is_recurring">
                        <label class="form-check-label fw-semibold" for="add_is_recurring">
                            {{ __('Repeats Annually') }}
                        </label>
                    </div>
                </div>
            </div>

            <!-- Description Arabic -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Description [Arabic]') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                <textarea name="description_ar" class="form-control" rows="2" placeholder="{{ __('تفاصيل وملاحظات العطلة الرسمية...') }}" dir="rtl"></textarea>
            </div>

            <!-- Description English -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Description [English]') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                <textarea name="description_en" class="form-control" rows="2" placeholder="{{ __('Holiday details and notes...') }}" dir="ltr"></textarea>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-check"></i>
                    <span>{{ __('Save Holiday') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit Official Holiday                                       -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditHoliday" aria-labelledby="offcanvasEditHolidayLabel" style="width: 520px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasEditHolidayLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit-alt text-primary"></i>
            <span>{{ __('Edit Holiday') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form action="" method="POST" id="editHolidayForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit_id">

            <!-- Arabic Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Holiday Name [Arabic]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" id="edit_name_ar" class="form-control" required dir="rtl">
            </div>

            <!-- English Name -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Holiday Name [English]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" id="edit_name_en" class="form-control" required dir="ltr">
            </div>

            <!-- Date Range -->
            <div class="p-3 bg-label-secondary rounded mb-3">
                <h6 class="fw-bold mb-2 text-heading d-flex align-items-center gap-1">
                    <i class="icon-base bx bx-calendar text-primary"></i>
                    <span>{{ __('Holiday Dates & Duration') }}</span>
                </h6>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label required fw-semibold">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="edit_start_date" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label required fw-semibold">{{ __('End Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" id="edit_end_date" class="form-control" required>
                    </div>
                </div>
                <div class="mt-2 text-end">
                    <span class="badge bg-primary fs-7" id="edit_days_counter">1 {{ __('day') }}</span>
                </div>
            </div>

            <!-- Branch & Color -->
            <div class="row g-3 mb-3">
                @if ($branches->isNotEmpty())
                <div class="col-sm-7">
                    <label class="form-label fw-semibold">{{ __('Branch') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                    <select class="form-select select2" name="branch_id" id="edit_branch_id" data-placeholder="{{ __('All Branches (Central)') }}" data-allow-clear="true">
                        <option value="">{{ __('All Branches (Central)') }}</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-5">
                @else
                <div class="col-12">
                @endif
                    <label class="form-label fw-semibold">{{ __('Badge Color') }}</label>
                    <div class="input-group">
                        <input type="color" name="color" id="edit_color" class="form-control form-control-color w-100">
                    </div>
                </div>
            </div>

            <!-- Recurring Annually & Status -->
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
                        <input class="form-check-input" type="checkbox" name="is_recurring" value="1" id="edit_is_recurring">
                        <label class="form-check-label fw-semibold" for="edit_is_recurring">
                            {{ __('Repeats Annually') }}
                        </label>
                    </div>
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
        <form action="" method="POST" id="deleteHolidayForm" class="modal-content border-0 shadow">
            @csrf
            @method('DELETE')
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title text-danger fw-bold d-flex align-items-center gap-2" id="deleteConfirmModalLabel">
                    <i class="icon-base bx bx-trash font-20"></i>
                    {{ __('Delete Holiday') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="avatar avatar-lg bg-label-danger mx-auto mb-3">
                    <i class="icon-base bx bx-error-circle fs-2"></i>
                </div>
                <h5 class="mb-2 fw-semibold">{{ __('Are you sure you want to delete this holiday?') }}</h5>
                <p class="text-muted mb-0">
                    <span id="deleteItemName" class="fw-bold text-heading"></span>
                </p>
                <p class="text-muted small mt-1">{{ __('This action will remove the official holiday from the company calendar.') }}</p>
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

    const dataUrl = "{{ route('hr.settings.official-holidays.data') }}";
    const baseUrl = "{{ url('hr/settings/official-holidays') }}";

    // Days counter calculation helper
    function updateDaysCounter(startEl, endEl, counterEl) {
        const startVal = $(startEl).val();
        const endVal = $(endEl).val();

        if (startVal && endVal) {
            const startDate = new Date(startVal);
            const endDate = new Date(endVal);
            if (endDate >= startDate) {
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                $(counterEl).text(diffDays + ' ' + (diffDays === 1 ? "{{ __('day') }}" : "{{ __('days') }}"));
                return;
            }
        }
        $(counterEl).text('1 {{ __("day") }}');
    }

    $('#add_start_date, #add_end_date').on('change', function () {
        if ($('#add_end_date').val() < $('#add_start_date').val()) {
            $('#add_end_date').val($('#add_start_date').val());
        }
        updateDaysCounter('#add_start_date', '#add_end_date', '#add_days_counter');
    });

    $('#edit_start_date, #edit_end_date').on('change', function () {
        if ($('#edit_end_date').val() < $('#edit_start_date').val()) {
            $('#edit_end_date').val($('#edit_start_date').val());
        }
        updateDaysCounter('#edit_start_date', '#edit_end_date', '#edit_days_counter');
    });

    // 1. Initialize Server-Side DataTable
    const table = $('#officialHolidaysTable').DataTable({
        processing: true,
        serverSide: true,
        pagingType: 'simple_numbers',
        ajax: {
            url: dataUrl,
            data: function (d) {
                d.year = $('#filter_year').val();
            }
        },
        columns: [
            { 
                data: 'DT_RowIndex', 
                name: 'DT_RowIndex',
                orderable: false, 
                searchable: false,
                render: (data, type, row, meta) => `<strong class="text-muted">#${data || (meta.row + 1 + (meta.settings._iDisplayStart || 0))}</strong>`
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
                data: 'date_range', 
                name: 'start_date',
                orderable: true,
                searchable: false
            },
            { 
                data: 'year', 
                name: 'year',
                className: 'text-center'
            },
            { 
                data: 'is_recurring', 
                name: 'is_recurring',
                className: 'text-center'
            },
            { 
                data: 'scope_info', 
                name: 'branch_id',
                orderable: false
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
                data: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-center'
            }
        ],
        order: [[3, 'desc']]
    });

    // Filter by year
    $('#filter_year').on('change', function () {
        table.ajax.reload();
    });

    // 2. Edit Action: Populate & Open Offcanvas
    const editOffcanvasEl = document.getElementById('offcanvasEditHoliday');
    const editForm = document.getElementById('editHolidayForm');

    $(document).on('click', '.btn-edit-holiday', function () {
        const raw = $(this).attr('data-row');
        if (!raw) return;

        const row = JSON.parse(raw);

        editForm.action = baseUrl + '/' + row.id;
        $('#edit_id').val(row.id);
        $('#edit_name_ar').val(row.name_ar || '');
        $('#edit_name_en').val(row.name_en || '');
        $('#edit_description_ar').val(row.description_ar || '');
        $('#edit_description_en').val(row.description_en || '');
        $('#edit_start_date').val(row.start_date || '');
        $('#edit_end_date').val(row.end_date || '');
        $('#edit_color').val(row.color || '#e83e8c');
        $('#edit_is_recurring').prop('checked', !!row.is_recurring);

        if ($('#edit_branch_id').length) {
            $('#edit_branch_id').val(row.branch_id || '').trigger('change');
        }
        if ($('#edit_status').length) {
            $('#edit_status').val(row.status || 'active').trigger('change');
        }

        updateDaysCounter('#edit_start_date', '#edit_end_date', '#edit_days_counter');

        if (editOffcanvasEl) {
            const editOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(editOffcanvasEl);
            editOffcanvas.show();
        }
    });

    // 3. Delete Action: Confirmation Modal
    const deleteModalEl = document.getElementById('deleteConfirmModal');
    const deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;
    const deleteForm = document.getElementById('deleteHolidayForm');
    const deleteTargetName = document.getElementById('deleteItemName');

    $(document).on('click', '.btn-delete-holiday', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');

        deleteForm.action = baseUrl + '/' + id;
        deleteTargetName.textContent = name || '';

        if (deleteModal) {
            deleteModal.show();
        }
    });

    // Initialize Select2 in offcanvases and filters
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
