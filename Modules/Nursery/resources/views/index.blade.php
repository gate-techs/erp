@extends('layouts/contentNavbarLayout')

@section('title', __('Nurseries') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 nursery-page-header">
    <div class="d-flex flex-column justify-content-center">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted fw-medium small">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item active text-primary fw-medium small" aria-current="page">{{ __('Nurseries List') }}</li>
            </ol>
        </nav>
        <!-- Title -->
        <div class="d-flex align-items-center gap-2 mt-1">
            <i class="icon-base bx bx-buildings text-primary font-24"></i>
            <h4 class="mb-0 fw-bold text-heading">{{ __('Nurseries') }}</h4>
        </div>
    </div>

    @can('nursery.create')
    <div class="mt-3 mt-md-0">
        <!-- Button that opens the Side Offcanvas matching the user's design -->
        <button type="button" class="btn btn-primary btn-sm nursery-page-btn d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddNursery" aria-controls="offcanvasAddNursery">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add New') }}</span>
        </button>
    </div>
    @endcan
</div>


<!-- @if (isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible mb-4 shadow-sm" role="alert">
        <h6 class="alert-heading fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-error-circle font-20"></i>
            <span>{{ __('Please review the following errors:') }}</span>
        </h6>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif-->

<!-- Nurseries DataTable Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="nurseriesTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 60px;">#</th>
                    <th>{{ __('Nursery Name') }}</th>
                    <th>{{ __('Responsible Employee') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Status') }}</th>
                    <th class="text-center" style="width: 130px;">{{ __('Created Date') }}</th>
                    <th class="text-center" style="width: 120px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loaded via Server-Side DataTables -->
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 1. Offcanvas: Add New Nursery (Form from the side matching the image)     -->
<!-- ========================================================================= -->
@can('nursery.create')
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddNursery" aria-labelledby="offcanvasAddNurseryLabel" style="width: 480px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddNurseryLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-buildings text-primary"></i>
            <span>{{ __('Add New') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="addNurseryForm" action="{{ route('nursery.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- 1. Image Upload Box with Camera Icon matching user image -->
            <div class="mb-4">
                <label class="form-label fw-semibold mb-2 text-heading d-flex align-items-center justify-content-between">
                    <span>{{ __('Add New Image') }}</span>
                    <small class="text-muted">PNG, JPG, WEBP</small>
                </label>
                <div class="image-upload-wrapper border rounded-3 p-3 text-center position-relative bg-lighter d-flex flex-column align-items-center justify-content-center" 
                     style="border-style: dashed !important; border-width: 2px !important; cursor: pointer; min-height: 110px;"
                     onclick="document.getElementById('add_nursery_image').click();">
                    
                    <div id="add_image_placeholder" class="d-flex align-items-center justify-content-between w-100 px-2">
                        <span class="text-muted small">{{ __('Add New Image') }}</span>
                        <i class="icon-base bx bx-camera text-primary font-24"></i>
                    </div>

                    <div id="add_image_preview_box" class="d-none align-items-center gap-2 w-100">
                        <img id="add_image_preview" src="" alt="Preview" class="rounded object-fit-cover border" width="56" height="56">
                        <div class="text-start">
                            <span id="add_image_name" class="fw-semibold small d-block text-truncate" style="max-width: 220px;"></span>
                            <small class="text-primary">{{ __('Click to change image') }}</small>
                        </div>
                    </div>

                    <input type="file" id="add_nursery_image" name="image" class="d-none" accept="image/*" onchange="previewAddImage(this)">
                </div>
            </div>

            <!-- 2. Responsible Employee Dropdown -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Responsible Employee') }} <span class="text-danger">*</span></label>
                <select name="employee_id" class="select2 form-select" data-placeholder="{{ __('Select Responsible Employee') }}" required>
                    <option value="" selected disabled>{{ __('Select Responsible Employee') }}</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->email }})</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Nursery Name [English] -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Nursery Name [English]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" class="form-control" placeholder="Example: Pioneer Model Nursery" required dir="ltr">
            </div>

            <!-- 4. Nursery Name [Arabic] -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Nursery Name [Arabic]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" class="form-control" placeholder="مثال: حضانة الرواد النموذجية" required dir="rtl">
            </div>

            <!-- 5. Government Dropdown -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Government') }}</label>
                <select name="government_id" class="select2 form-select" data-placeholder="{{ __('Select Government') }}" data-allow-clear="true">
                    <option value="" selected>{{ __('Select Government') }}</option>
                    @foreach ($governments as $gov)
                        <option value="{{ $gov->id }}">{{ $gov->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 6. Nursery Details [English] -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Nursery Details [English]') }}</label>
                <textarea name="description_en" class="form-control" rows="3" placeholder="Vision, mission and details..." dir="ltr"></textarea>
            </div>

            <!-- 7. Nursery Details [Arabic] -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Nursery Details [Arabic]') }}</label>
                <textarea name="description_ar" class="form-control" rows="3" placeholder="تفاصيل ورؤية وأهداف الحضانة..." dir="rtl"></textarea>
            </div>

            <!-- 8. Contacts Row (WhatsApp & Email) -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">{{ __('WhatsApp Number') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-lighter text-success"><i class="icon-base bx bxl-whatsapp font-18"></i></span>
                        <input type="text" name="whatsapp" class="form-control js-phone-input" placeholder="+965..." dir="ltr">
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">{{ __('Email Address') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-lighter text-primary"><i class="icon-base bx bx-envelope font-18"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="info@..." dir="ltr">
                    </div>
                </div>
            </div>

            <!-- 9. Geolocation Coordinates (Lat / Lng) -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">{{ __('Latitude') }}</label>
                    <input type="text" name="lat" class="form-control" placeholder="29.3759" dir="ltr">
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">{{ __('Longitude') }}</label>
                    <input type="text" name="lng" class="form-control" placeholder="47.9774" dir="ltr">
                </div>
            </div>

            <!-- 10. Status -->
            <div class="mb-4">
                <label class="form-label fw-semibold required">{{ __('Status') }}</label>
                <select name="status" class="select2 form-select" data-placeholder="{{ __('Select Status') }}" required>
                    <option value="active" selected>{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
            </div>

            <!-- 11. Collapsible Weekly Working Days Schedule -->
            <div class="mb-4">
                <div class="card border bg-lighter shadow-none">
                    <div class="card-header bg-transparent py-2 px-3 d-flex align-items-center justify-content-between" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#collapseWorkDaysAdd">
                        <span class="fw-semibold text-heading d-flex align-items-center gap-2">
                            <i class="icon-base bx bx-time-five text-primary"></i>
                            <span>{{ __('Weekly Work Days') }}</span>
                        </span>
                        <i class="icon-base bx bx-chevron-down font-18"></i>
                    </div>

                    <div id="collapseWorkDaysAdd" class="collapse show">
                        <div class="card-body p-3 pt-0">
                            @php
                                $days = [
                                    'sunday' => ['label' => __('Sunday'), 'from' => '07:00', 'to' => '14:00', 'off' => false],
                                    'monday' => ['label' => __('Monday'), 'from' => '07:00', 'to' => '14:00', 'off' => false],
                                    'tuesday' => ['label' => __('Tuesday'), 'from' => '07:00', 'to' => '14:00', 'off' => false],
                                    'wednesday' => ['label' => __('Wednesday'), 'from' => '07:00', 'to' => '14:00', 'off' => false],
                                    'thursday' => ['label' => __('Thursday'), 'from' => '07:00', 'to' => '14:00', 'off' => false],
                                    'friday' => ['label' => __('Friday'), 'from' => '', 'to' => '', 'off' => true],
                                    'saturday' => ['label' => __('Saturday'), 'from' => '', 'to' => '', 'off' => true],
                                ];
                                $idx = 0;
                            @endphp

                            <div class="table-responsive">
                                <table class="table table-sm table-borderless mb-0">
                                    <thead>
                                        <tr class="text-muted small border-bottom">
                                            <th>{{ __('Day') }}</th>
                                            <th style="width: 95px;">{{ __('From Time') }}</th>
                                            <th style="width: 95px;">{{ __('To Time') }}</th>
                                            <th class="text-center" style="width: 60px;">{{ __('Holiday / Off') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($days as $dayKey => $d)
                                            <tr>
                                                <td class="align-middle fw-semibold py-2">
                                                    {{ $d['label'] }}
                                                    <input type="hidden" name="work_days[{{ $idx }}][day]" value="{{ $dayKey }}">
                                                </td>
                                                <td class="py-2">
                                                    <input type="time" name="work_days[{{ $idx }}][from_time]" class="form-control" value="{{ $d['from'] }}">
                                                </td>
                                                <td class="py-2">
                                                    <input type="time" name="work_days[{{ $idx }}][to_time]" class="form-control" value="{{ $d['to'] }}">
                                                </td>
                                                <td class="text-center align-middle py-2">
                                                    <div class="form-check form-switch d-inline-block m-0">
                                                        <input class="form-check-input" type="checkbox" name="work_days[{{ $idx }}][is_off]" value="1" {{ $d['off'] ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                            </tr>
                                            @php $idx++; @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-4">
                    <i class="icon-base bx bx-save"></i>
                    <span>{{ __('Save Nursery') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit Nursery                                                -->
<!-- ========================================================================= -->
@can('nursery.edit')
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditNursery" aria-labelledby="offcanvasEditNurseryLabel" style="width: 480px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasEditNurseryLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit text-primary"></i>
            <span>{{ __('Edit Nursery') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="editNurseryForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Image preview & update box -->
            <div class="mb-4">
                <label class="form-label fw-semibold mb-2 text-heading d-flex align-items-center justify-content-between">
                    <span>{{ __('Add New Image') }}</span>
                    <small class="text-muted">PNG, JPG, WEBP</small>
                </label>
                <div class="border rounded-3 p-3 text-center position-relative bg-lighter d-flex flex-column align-items-center justify-content-center" 
                     style="border-style: dashed !important; border-width: 2px !important; cursor: pointer; min-height: 110px;"
                     onclick="document.getElementById('edit_nursery_image').click();">
                    
                    <div id="edit_image_preview_box" class="d-flex align-items-center gap-2 w-100">
                        <img id="edit_image_preview" src="" alt="Preview" class="rounded object-fit-cover border" width="56" height="56">
                        <div class="text-start">
                            <span id="edit_image_name" class="fw-semibold small d-block text-truncate" style="max-width: 220px;"></span>
                            <small class="text-primary">{{ __('Click to change image') }}</small>
                        </div>
                    </div>

                    <input type="file" id="edit_nursery_image" name="image" class="d-none" accept="image/*" onchange="previewEditImage(this)">
                </div>
            </div>

            <!-- Responsible Employee -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Responsible Employee') }} <span class="text-danger">*</span></label>
                <select id="edit_employee_id" name="employee_id" class="select2 form-select" data-placeholder="{{ __('Select Responsible Employee') }}" required>
                    <option value="" disabled>{{ __('Select Responsible Employee') }}</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->email }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Nursery Name [English] -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Nursery Name [English]') }} <span class="text-danger">*</span></label>
                <input type="text" id="edit_name_en" name="name_en" class="form-control" required dir="ltr">
            </div>

            <!-- Nursery Name [Arabic] -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Nursery Name [Arabic]') }} <span class="text-danger">*</span></label>
                <input type="text" id="edit_name_ar" name="name_ar" class="form-control" required dir="rtl">
            </div>

            <!-- Government -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Government') }}</label>
                <select id="edit_government_id" name="government_id" class="select2 form-select" data-placeholder="{{ __('Select Government') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Government') }}</option>
                    @foreach ($governments as $gov)
                        <option value="{{ $gov->id }}">{{ $gov->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Nursery Details [English] -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Nursery Details [English]') }}</label>
                <textarea id="edit_description_en" name="description_en" class="form-control" rows="3" dir="ltr"></textarea>
            </div>

            <!-- Nursery Details [Arabic] -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Nursery Details [Arabic]') }}</label>
                <textarea id="edit_description_ar" name="description_ar" class="form-control" rows="3" dir="rtl"></textarea>
            </div>

            <!-- Contacts Row -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">{{ __('WhatsApp Number') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-lighter text-success"><i class="icon-base bx bxl-whatsapp font-18"></i></span>
                        <input type="text" id="edit_whatsapp" name="whatsapp" class="form-control js-phone-input" dir="ltr">
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">{{ __('Email Address') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-lighter text-primary"><i class="icon-base bx bx-envelope font-18"></i></span>
                        <input type="email" id="edit_email" name="email" class="form-control" dir="ltr">
                    </div>
                </div>
            </div>

            <!-- Coordinates Row -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">{{ __('Latitude') }}</label>
                    <input type="text" id="edit_lat" name="lat" class="form-control" dir="ltr">
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">{{ __('Longitude') }}</label>
                    <input type="text" id="edit_lng" name="lng" class="form-control" dir="ltr">
                </div>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="form-label fw-semibold required">{{ __('Status') }}</label>
                <select id="edit_status" name="status" class="select2 form-select" data-placeholder="{{ __('Select Status') }}" required>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
            </div>

            <!-- Footer Buttons -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-4">
                    <i class="icon-base bx bx-save"></i>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

<!-- ========================================================================= -->
<!-- 3. Modal: Delete Nursery Confirmation                                     -->
<!-- ========================================================================= -->
@can('nursery.delete')
<div class="modal fade" id="deleteNurseryModal" tabindex="-1" aria-labelledby="deleteNurseryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form id="deleteNurseryForm" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-body text-center py-4">
                <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-trash font-32 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Confirm Delete') }}</h5>
                <p class="text-muted mb-0">
                    {{ __('Are you sure you want to delete the nursery:') }}
                    <br>
                    <strong id="delete_target_name" class="text-danger"></strong>؟
                </p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 pb-4">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-danger">{{ __('Yes, Delete') }}</button>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection

@section('page-script')
<script>
const canEditNursery = @json(auth()->user()->can('nursery.edit'));
const canDeleteNursery = @json(auth()->user()->can('nursery.delete'));
const canToggleNursery = @json(auth()->user()->can('nursery.toggle-status'));

// Image preview functions
function previewAddImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('add_image_preview').src = e.target.result;
            document.getElementById('add_image_name').textContent = file.name;
            document.getElementById('add_image_placeholder').classList.add('d-none');
            document.getElementById('add_image_preview_box').classList.remove('d-none');
            document.getElementById('add_image_preview_box').classList.add('d-flex');
        };
        reader.readAsDataURL(file);
    }
}

function previewEditImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('edit_image_preview').src = e.target.result;
            document.getElementById('edit_image_name').textContent = file.name;
        };
        reader.readAsDataURL(file);
    }
}

window.addEventListener('load', function () {
    const $ = window.jQuery || window.$;
    if (!$ || !$.fn.DataTable) {
        console.error('jQuery or DataTables not loaded');
        return;
    }

    const dataUrl = "{{ route('nursery.data') }}";
    const baseUrl = "{{ url('nurseries') }}";

    // Initialize Server-Side DataTable matching the theme style
    const table = $('#nurseriesTable').DataTable({
        processing: true,
        serverSide: true,
        pagingType: 'simple_numbers',
        language: {
            emptyTable: `
                <div class="text-center py-5">
                    <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                        <i class="icon-base bx bx-buildings font-32 text-secondary"></i>
                    </div>
                    <h6 class="fw-bold mb-1">{{ __('No Nurseries Found') }}</h6>
                    <p class="text-muted small mb-0">{{ __('Click "Add New" to create your first nursery.') }}</p>
                </div>
            `
        },
        ajax: dataUrl,
        columns: [
            { 
                data: 'DT_RowIndex', 
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: (data, type, row, meta) => `<strong class="text-muted">#${data || (meta.row + 1 + (meta.settings._iDisplayStart || 0))}</strong>`
            },
            { 
                data: 'name_ar', 
                name: 'name_ar',
                render: (data, type, row) => {
                    const avatar = row.image_url 
                        ? `<img src="${row.image_url}" alt="Logo" class="rounded object-fit-cover border shadow-xs flex-shrink-0" width="42" height="42">`
                        : `<div class="avatar avatar-sm bg-label-primary rounded d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;"><i class="icon-base bx bx-buildings font-22"></i></div>`;
                    
                    return `
                        <div class="d-flex align-items-center gap-3">
                            ${avatar}
                            <div>
                                <span class="fw-bold text-heading d-block">${data || '-'}</span>
                                <small class="text-muted" dir="ltr">${row.name_en || ''}</small>
                            </div>
                        </div>`;
                }
            },
            { 
                data: 'employee_name', 
                name: 'employee_id',
                render: data => data && data !== '-'
                    ? `<span class="badge bg-label-secondary"><i class="icon-base bx bx-user me-1"></i>${data}</span>`
                    : `<span class="text-muted">-</span>`
            },
            { 
                data: 'status', 
                name: 'status',
                className: 'text-center',
                render: (data, type, row) => {
                    if (canToggleNursery) {
                        return `
                            <button type="button" class="btn btn-sm btn-toggle-status ${row.status_badge || 'bg-label-success'} border-0" data-id="${row.id}" title="{{ __('Click to toggle') }}">
                                ${row.status_label || data}
                            </button>`;
                    }
                    return `<span class="badge ${row.status_badge || 'bg-label-success'}">${row.status_label || data}</span>`;
                }
            },
            { 
                data: 'created_at', 
                name: 'created_at',
                className: 'text-center',
                render: data => `<span class="text-muted">${data}</span>`
            },
            { 
                data: null, 
                orderable: false, 
                searchable: false, 
                className: 'text-center',
                render: (data, type, row) => {
                    let buttons = '';
                    if (canEditNursery) {
                        buttons += `
                            <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-nursery"
                                    title="{{ __('Edit') }}"
                                    data-id="${row.id}"
                                    data-name-ar="${row.name_ar || ''}"
                                    data-name-en="${row.name_en || ''}"
                                    data-desc-ar="${row.description_ar || ''}"
                                    data-desc-en="${row.description_en || ''}"
                                    data-employee-id="${row.employee_id || ''}"
                                    data-gov-id="${row.government_id || ''}"
                                    data-whatsapp="${row.whatsapp || ''}"
                                    data-email="${row.email || ''}"
                                    data-lat="${row.lat || ''}"
                                    data-lng="${row.lng || ''}"
                                    data-status="${row.status || 'active'}"
                                    data-image="${row.image_url || ''}">
                                <i class="icon-base bx bx-edit-alt"></i>
                            </button>`;
                    }
                    if (canDeleteNursery) {
                        buttons += `
                            <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-nursery"
                                    title="{{ __('Delete') }}"
                                    data-id="${row.id}"
                                    data-name="${row.name_ar}">
                                <i class="icon-base bx bx-trash"></i>
                            </button>`;
                    }
                    if (!buttons) {
                        return '<span class="text-muted">-</span>';
                    }
                    return `<div class="d-inline-flex gap-1">${buttons}</div>`;
                }
            }
        ],
        order: [[0, 'asc']]
    });

    // Handle Edit Drawer Population
    const editOffcanvasEl = document.getElementById('offcanvasEditNursery');
    const editForm = document.getElementById('editNurseryForm');

    $(document).on('click', '.btn-edit-nursery', function () {
        const id = $(this).data('id');
        editForm.action = baseUrl + '/' + id;

        document.getElementById('edit_name_ar').value = $(this).data('name-ar') || '';
        document.getElementById('edit_name_en').value = $(this).data('name-en') || '';
        document.getElementById('edit_description_ar').value = $(this).data('desc-ar') || '';
        document.getElementById('edit_description_en').value = $(this).data('desc-en') || '';
        $('#edit_employee_id').val($(this).data('employee-id') || '').trigger('change');
        $('#edit_government_id').val($(this).data('gov-id') || '').trigger('change');
        document.getElementById('edit_whatsapp').value = $(this).data('whatsapp') || '';
        document.getElementById('edit_email').value = $(this).data('email') || '';
        document.getElementById('edit_lat').value = $(this).data('lat') || '';
        document.getElementById('edit_lng').value = $(this).data('lng') || '';
        $('#edit_status').val($(this).data('status') || 'active').trigger('change');

        const img = $(this).data('image');
        const previewImg = document.getElementById('edit_image_preview');
        const previewName = document.getElementById('edit_image_name');
        if (img) {
            previewImg.src = img;
            previewName.textContent = '{{ __("Current Image") }}';
        } else {
            previewImg.src = '{{ asset("assets/img/illustrations/page-pricing-enterprise.png") }}';
            previewName.textContent = '{{ __("No Image Uploaded") }}';
        }

        if (editOffcanvasEl) {
            const editOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(editOffcanvasEl);
            editOffcanvas.show();
        }
    });

    // Handle Delete Modal
    const deleteModalEl = document.getElementById('deleteNurseryModal');
    const deleteForm = document.getElementById('deleteNurseryForm');
    const deleteTargetName = document.getElementById('delete_target_name');

    $(document).on('click', '.btn-delete-nursery', function () {
        const id = $(this).data('id');
        deleteForm.action = baseUrl + '/' + id;
        deleteTargetName.textContent = $(this).data('name') || '';
        if (deleteModalEl) {
            const deleteModal = bootstrap.Modal.getOrCreateInstance(deleteModalEl);
            deleteModal.show();
        }
    });

    // Handle Status Toggle via AJAX
    $(document).on('click', '.btn-toggle-status', function () {
        const btn = $(this);
        const id = btn.data('id');
        const token = $('meta[name="csrf-token"]').attr('content');

        btn.prop('disabled', true);

        $.ajax({
            url: baseUrl + '/' + id + '/toggle-status',
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': token
            },
            success: function (res) {
                if (res.success) {
                    btn.removeClass('bg-label-success bg-label-danger').addClass(res.badge);
                    btn.text(res.status === 'active' ? '{{ __("Active") }}' : '{{ __("Inactive") }}');
                }
            },
            complete: function () {
                btn.prop('disabled', false);
            }
        });
    });

    // Initialize Select2 in page and offcanvases
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
