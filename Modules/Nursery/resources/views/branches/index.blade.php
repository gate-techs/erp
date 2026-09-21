@extends('layouts/contentNavbarLayout')

@section('title', __('Nursery Branches') . ' - ' . config('variables.templateName'))

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="icon-base bx bx-git-branch text-primary font-24"></i>
            <span>{{ __('Nursery Branches') }}</span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('nursery.index') }}">{{ __('Nurseries') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Nursery Branches') }}</li>
            </ol>
        </nav>
    </div>

    <div>
        <!-- Button that opens the Side Offcanvas matching the theme design -->
        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddBranch" aria-controls="offcanvasAddBranch">
            <i class="icon-base bx bx-plus font-18"></i>
            <span>{{ __('Add New Branch') }}</span>
        </button>
    </div>
</div>

<!-- Filters Card (الحضانة - المسؤول - المحافظة - الحالة) -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-end">
            <!-- 1. Nursery Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Nursery') }}</label>
                <select id="filter_nursery_id" class="select2 form-select" data-placeholder="{{ __('All Nurseries') }}" data-allow-clear="true">
                    <option value="">{{ __('All Nurseries') }}</option>
                    @foreach ($nurseries as $nursery)
                        <option value="{{ $nursery->id }}">{{ $nursery->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Employee Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Responsible Employee') }}</label>
                <select id="filter_employee_id" class="select2 form-select" data-placeholder="{{ __('All Employees') }}" data-allow-clear="true">
                    <option value="">{{ __('All Employees') }}</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Government Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label fw-semibold text-heading small mb-1">{{ __('Government') }}</label>
                <select id="filter_government_id" class="select2 form-select" data-placeholder="{{ __('All Governments') }}" data-allow-clear="true">
                    <option value="">{{ __('All Governments') }}</option>
                    @foreach ($governments as $gov)
                        <option value="{{ $gov->id }}">{{ $gov->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Status Filter -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <label class="form-label fw-semibold text-heading small mb-0">{{ __('Status') }}</label>
                    <button type="button" id="btnResetFilters" class="btn btn-link btn-xs text-muted p-0 text-decoration-none" title="{{ __('Reset Filters') }}">
                        <i class="icon-base bx bx-reset me-1"></i>{{ __('Reset') }}
                    </button>
                </div>
                <select id="filter_status" class="select2 form-select" data-placeholder="{{ __('All Statuses') }}" data-allow-clear="true">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Branches DataTable Card -->
<div class="card shadow-sm border-0">
    <div class="card-datatable table-responsive">
        <table id="branchesTable" class="table table-hover border-top w-100 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 60px;">#</th>
                    <th>{{ __('Branch Name') }}</th>
                    <th>{{ __('Nursery') }}</th>
                    <th>{{ __('Location') }}</th>
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
<!-- 1. Offcanvas: Add New Branch                                              -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasAddBranch" aria-labelledby="offcanvasAddBranchLabel" style="width: 500px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasAddBranchLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-git-branch text-primary"></i>
            <span>{{ __('Add New Branch') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="addBranchForm" action="{{ route('nursery.branches.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- 1. Image Upload Box with Camera Icon -->
            <div class="mb-4">
                <label class="form-label fw-semibold mb-2 text-heading d-flex align-items-center justify-content-between">
                    <span>{{ __('Branch Image / Logo') }}</span>
                    <small class="text-muted">PNG, JPG, WEBP</small>
                </label>
                <div class="image-upload-wrapper border rounded-3 p-3 text-center position-relative bg-lighter d-flex flex-column align-items-center justify-content-center" 
                     style="border-style: dashed !important; border-width: 2px !important; cursor: pointer; min-height: 110px;"
                     onclick="document.getElementById('add_branch_image').click();">
                    
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

                    <input type="file" id="add_branch_image" name="image" class="d-none" accept="image/*" onchange="previewAddImage(this)">
                </div>
            </div>

            <!-- 2. Parent Nursery Dropdown -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Nursery') }} <span class="text-danger">*</span></label>
                <select name="nursery_id" class="select2 form-select" data-placeholder="{{ __('Select Nursery') }}" required>
                    <option value="" selected disabled>{{ __('Select Nursery') }}</option>
                    @foreach ($nurseries as $nursery)
                        <option value="{{ $nursery->id }}">{{ $nursery->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Responsible Employee Dropdown -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Responsible Employee') }}</label>
                <select name="employee_id" class="select2 form-select" data-placeholder="{{ __('Select Responsible Employee') }}" data-allow-clear="true">
                    <option value="" selected>{{ __('Select Responsible Employee') }}</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->email }})</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Location: Government & City Row -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">{{ __('Government') }}</label>
                    <select name="government_id" id="add_government_id" class="select2 form-select" data-placeholder="{{ __('Select Government') }}" data-allow-clear="true" onchange="loadCities(this.value, '#add_city_id')">
                        <option value="" selected>{{ __('Select Government') }}</option>
                        @foreach ($governments as $gov)
                            <option value="{{ $gov->id }}">{{ $gov->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">{{ __('City / Area') }}</label>
                    <select name="city_id" id="add_city_id" class="select2 form-select" data-placeholder="{{ __('Select City / Area') }}" data-allow-clear="true">
                        <option value="" selected>{{ __('Select City / Area') }}</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}" data-gov="{{ $city->parent_id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 5. Branch Name [English] -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Branch Name [English]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_en" class="form-control" placeholder="Example: Hawally Main Branch" required dir="ltr">
            </div>

            <!-- 6. Branch Name [Arabic] -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Branch Name [Arabic]') }} <span class="text-danger">*</span></label>
                <input type="text" name="name_ar" class="form-control" placeholder="مثال: فرع حولي الرئيسي" required dir="rtl">
            </div>

            <!-- 7. Address [English] -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Branch Address [English]') }}</label>
                <input type="text" name="address_en" class="form-control" placeholder="Block 4, Street 12, Building 5..." dir="ltr">
            </div>

            <!-- 8. Address [Arabic] -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Branch Address [Arabic]') }}</label>
                <input type="text" name="address_ar" class="form-control" placeholder="قطعة 4، شارع 12، مبنى 5..." dir="rtl">
            </div>

            <!-- 9. Branch Details [English] -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Branch Details [English]') }}</label>
                <textarea name="description_en" class="form-control" rows="2" placeholder="Details and vision..." dir="ltr"></textarea>
            </div>

            <!-- 10. Branch Details [Arabic] -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Branch Details [Arabic]') }}</label>
                <textarea name="description_ar" class="form-control" rows="2" placeholder="تفاصيل ورؤية الفرع..." dir="rtl"></textarea>
            </div>

            <!-- 11. Contacts Row (WhatsApp & Email) -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">{{ __('WhatsApp Number') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-lighter text-success"><i class="icon-base bx bxl-whatsapp font-18"></i></span>
                        <input type="text" name="whatsapp" class="form-control" placeholder="+965..." dir="ltr">
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">{{ __('Email Address') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-lighter text-primary"><i class="icon-base bx bx-envelope font-18"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="branch@..." dir="ltr">
                    </div>
                </div>
            </div>

            <!-- 12. Coordinates (Lat / Lng) -->
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

            <!-- 13. Status Switch -->
            <div class="mb-4">
                <label class="form-label fw-semibold d-block">{{ __('Status') }}</label>
                <div class="d-flex align-items-center gap-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="statusActiveAdd" value="active" checked>
                        <label class="form-check-label text-success fw-semibold" for="statusActiveAdd">
                            <i class="icon-base bx bx-check-circle me-1"></i>{{ __('Active') }}
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="statusInactiveAdd" value="inactive">
                        <label class="form-check-label text-danger fw-semibold" for="statusInactiveAdd">
                            <i class="icon-base bx bx-x-circle me-1"></i>{{ __('Inactive') }}
                        </label>
                    </div>
                </div>
            </div>

            <!-- 14. Weekly Work Days & Working Hours Accordion -->
            <div class="mb-4">
                <div class="accordion border rounded-3 overflow-hidden" id="workScheduleAccordionAdd">
                    <div class="accordion-item border-0">
                        <h2 class="accordion-header" id="headingScheduleAdd">
                            <button class="accordion-button collapsed bg-lighter fw-bold text-heading" type="button" data-bs-toggle="collapse" data-bs-target="#collapseScheduleAdd" aria-expanded="false" aria-controls="collapseScheduleAdd">
                                <i class="icon-base bx bx-time-five me-2 font-18 text-primary"></i>
                                <span>{{ __('Weekly Working Schedule') }}</span>
                            </button>
                        </h2>
                        <div id="collapseScheduleAdd" class="accordion-collapse collapse" aria-labelledby="headingScheduleAdd">
                            <div class="accordion-body p-2">
                                <table class="table table-sm table-borderless mb-0">
                                    <thead>
                                        <tr class="text-muted small border-bottom">
                                            <th style="width: 110px;">{{ __('Day') }}</th>
                                            <th>{{ __('From') }}</th>
                                            <th>{{ __('To') }}</th>
                                            <th class="text-center" style="width: 70px;">{{ __('Holiday') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $defaultDays = [
                                                ['day' => 'sunday', 'from' => '07:00', 'to' => '14:00', 'off' => false],
                                                ['day' => 'monday', 'from' => '07:00', 'to' => '14:00', 'off' => false],
                                                ['day' => 'tuesday', 'from' => '07:00', 'to' => '14:00', 'off' => false],
                                                ['day' => 'wednesday', 'from' => '07:00', 'to' => '14:00', 'off' => false],
                                                ['day' => 'thursday', 'from' => '07:00', 'to' => '14:00', 'off' => false],
                                                ['day' => 'friday', 'from' => null, 'to' => null, 'off' => true],
                                                ['day' => 'saturday', 'from' => null, 'to' => null, 'off' => true],
                                            ];
                                            $idx = 0;
                                        @endphp
                                        @foreach ($defaultDays as $d)
                                            <tr class="border-bottom">
                                                <td class="align-middle py-2">
                                                    <span class="fw-semibold small">{{ __(ucfirst($d['day'])) }}</span>
                                                    <input type="hidden" name="work_days[{{ $idx }}][day]" value="{{ $d['day'] }}">
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
                    <span>{{ __('Save Branch') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. Offcanvas: Edit Branch                                                 -->
<!-- ========================================================================= -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} shadow-lg" tabindex="-1" id="offcanvasEditBranch" aria-labelledby="offcanvasEditBranchLabel" style="width: 500px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 id="offcanvasEditBranchLabel" class="offcanvas-title fw-bold text-heading d-flex align-items-center gap-2 mb-0">
            <i class="icon-base bx bx-edit text-primary"></i>
            <span>{{ __('Edit Branch') }}</span>
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="editBranchForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Image preview & update box -->
            <div class="mb-4">
                <label class="form-label fw-semibold mb-2 text-heading d-flex align-items-center justify-content-between">
                    <span>{{ __('Branch Image / Logo') }}</span>
                    <small class="text-muted">PNG, JPG, WEBP</small>
                </label>
                <div class="border rounded-3 p-3 text-center position-relative bg-lighter d-flex flex-column align-items-center justify-content-center" 
                     style="border-style: dashed !important; border-width: 2px !important; cursor: pointer; min-height: 110px;"
                     onclick="document.getElementById('edit_branch_image').click();">
                    
                    <div id="edit_image_preview_box" class="d-flex align-items-center gap-2 w-100">
                        <img id="edit_image_preview" src="" alt="Preview" class="rounded object-fit-cover border" width="56" height="56">
                        <div class="text-start">
                            <span id="edit_image_name" class="fw-semibold small d-block text-truncate" style="max-width: 220px;"></span>
                            <small class="text-primary">{{ __('Click to change image') }}</small>
                        </div>
                    </div>

                    <input type="file" id="edit_branch_image" name="image" class="d-none" accept="image/*" onchange="previewEditImage(this)">
                </div>
            </div>

            <!-- Parent Nursery Dropdown -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Nursery') }} <span class="text-danger">*</span></label>
                <select name="nursery_id" id="edit_nursery_id" class="select2 form-select" data-placeholder="{{ __('Select Nursery') }}" required>
                    <option value="" disabled>{{ __('Select Nursery') }}</option>
                    @foreach ($nurseries as $nursery)
                        <option value="{{ $nursery->id }}">{{ $nursery->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Responsible Employee Dropdown -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Responsible Employee') }}</label>
                <select name="employee_id" id="edit_employee_id" class="select2 form-select" data-placeholder="{{ __('Select Responsible Employee') }}" data-allow-clear="true">
                    <option value="">{{ __('Select Responsible Employee') }}</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->email }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Location: Government & City Row -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">{{ __('Government') }}</label>
                    <select name="government_id" id="edit_government_id" class="select2 form-select" data-placeholder="{{ __('Select Government') }}" data-allow-clear="true" onchange="loadCities(this.value, '#edit_city_id')">
                        <option value="">{{ __('Select Government') }}</option>
                        @foreach ($governments as $gov)
                            <option value="{{ $gov->id }}">{{ $gov->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">{{ __('City / Area') }}</label>
                    <select name="city_id" id="edit_city_id" class="select2 form-select" data-placeholder="{{ __('Select City / Area') }}" data-allow-clear="true">
                        <option value="">{{ __('Select City / Area') }}</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}" data-gov="{{ $city->parent_id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Branch Name [English] -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Branch Name [English]') }} <span class="text-danger">*</span></label>
                <input type="text" id="edit_name_en" name="name_en" class="form-control" required dir="ltr">
            </div>

            <!-- Branch Name [Arabic] -->
            <div class="mb-3">
                <label class="form-label required fw-semibold">{{ __('Branch Name [Arabic]') }} <span class="text-danger">*</span></label>
                <input type="text" id="edit_name_ar" name="name_ar" class="form-control" required dir="rtl">
            </div>

            <!-- Address [English] -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Branch Address [English]') }}</label>
                <input type="text" id="edit_address_en" name="address_en" class="form-control" dir="ltr">
            </div>

            <!-- Address [Arabic] -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Branch Address [Arabic]') }}</label>
                <input type="text" id="edit_address_ar" name="address_ar" class="form-control" dir="rtl">
            </div>

            <!-- Branch Details [English] -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Branch Details [English]') }}</label>
                <textarea id="edit_description_en" name="description_en" class="form-control" rows="2" dir="ltr"></textarea>
            </div>

            <!-- Branch Details [Arabic] -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('Branch Details [Arabic]') }}</label>
                <textarea id="edit_description_ar" name="description_ar" class="form-control" rows="2" dir="rtl"></textarea>
            </div>

            <!-- Contacts Row -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label fw-semibold">{{ __('WhatsApp Number') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-lighter text-success"><i class="icon-base bx bxl-whatsapp font-18"></i></span>
                        <input type="text" id="edit_whatsapp" name="whatsapp" class="form-control" dir="ltr">
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

            <!-- Status Switch -->
            <div class="mb-4">
                <label class="form-label fw-semibold d-block">{{ __('Status') }}</label>
                <div class="d-flex align-items-center gap-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="statusActiveEdit" value="active">
                        <label class="form-check-label text-success fw-semibold" for="statusActiveEdit">
                            <i class="icon-base bx bx-check-circle me-1"></i>{{ __('Active') }}
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="statusInactiveEdit" value="inactive">
                        <label class="form-check-label text-danger fw-semibold" for="statusInactiveEdit">
                            <i class="icon-base bx bx-x-circle me-1"></i>{{ __('Inactive') }}
                        </label>
                    </div>
                </div>
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

<!-- ========================================================================= -->
<!-- 3. Modal: Delete Branch Confirmation                                      -->
<!-- ========================================================================= -->
<div class="modal fade" id="deleteBranchModal" tabindex="-1" aria-labelledby="deleteBranchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form id="deleteBranchForm" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-body text-center py-4">
                <div class="avatar avatar-xl bg-label-danger mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="icon-base bx bx-trash font-32 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('Confirm Delete') }}</h5>
                <p class="text-muted mb-0">
                    {{ __('Are you sure you want to delete the branch:') }}
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
@endsection

@section('page-script')
<script>
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

// Dynamic City Filter based on Government
function loadCities(governmentId, citySelectSelector, selectedCityId = null) {
    const $citySelect = $(citySelectSelector);
    $citySelect.empty();
    $citySelect.append(`<option value="">{{ __('Select City / Area') }}</option>`);

    if (!governmentId) {
        return;
    }

    const citiesUrl = "{{ url('nursery-branches/cities') }}/" + governmentId;
    $.getJSON(citiesUrl, function (res) {
        if (res.success && res.data) {
            res.data.forEach(city => {
                const isSelected = selectedCityId && (selectedCityId == city.id) ? 'selected' : '';
                $citySelect.append(`<option value="${city.id}" ${isSelected}>${city.name}</option>`);
            });
        }
    });
}

window.addEventListener('load', function () {
    const $ = window.jQuery || window.$;
    if (!$ || !$.fn.DataTable) {
        console.error('jQuery or DataTables not loaded');
        return;
    }

    const dataUrl = "{{ route('nursery.branches.data') }}";
    const baseUrl = "{{ url('nursery-branches') }}";

    // Initialize Server-Side DataTable matching the theme style
    const table = $('#branchesTable').DataTable({
        processing: true,
        serverSide: true,
        pagingType: 'simple_numbers',
        ajax: {
            url: dataUrl,
            data: function (d) {
                d.nursery_id = $('#filter_nursery_id').val();
                d.employee_id = $('#filter_employee_id').val();
                d.government_id = $('#filter_government_id').val();
                d.status = $('#filter_status').val();
            }
        },
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
                        : `<div class="avatar avatar-sm bg-label-primary rounded d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;"><i class="icon-base bx bx-git-branch font-22"></i></div>`;
                    
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
                data: 'nursery_name',
                name: 'nursery_id',
                render: data => data && data !== '-'
                    ? `<span class="badge bg-label-info"><i class="icon-base bx bx-buildings me-1"></i>${data}</span>`
                    : `<span class="text-muted">-</span>`
            },
            {
                data: 'government_name',
                name: 'government_id',
                render: (data, type, row) => {
                    const gov = data && data !== '-' ? data : '';
                    const city = row.city_name && row.city_name !== '-' ? row.city_name : '';
                    if (!gov && !city) return '<span class="text-muted">-</span>';
                    return `
                        <div>
                            <span class="fw-semibold text-heading d-block"><i class="icon-base bx bx-map-pin text-primary me-1"></i>${gov}</span>
                            ${city ? `<small class="text-muted">${city}</small>` : ''}
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
                render: (data, type, row) => `
                    <button type="button" class="btn btn-sm btn-toggle-status ${row.status_badge || 'bg-label-success'} border-0" data-id="${row.id}" title="{{ __('Click to toggle') }}">
                        ${row.status_label || data}
                    </button>`
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
                render: (data, type, row) => `
                    <div class="d-inline-flex gap-1">
                        <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-branch"
                                title="{{ __('Edit') }}"
                                data-id="${row.id}"
                                data-nursery-id="${row.nursery_id || ''}"
                                data-name-ar="${row.name_ar || ''}"
                                data-name-en="${row.name_en || ''}"
                                data-desc-ar="${row.description_ar || ''}"
                                data-desc-en="${row.description_en || ''}"
                                data-address-ar="${row.address_ar || ''}"
                                data-address-en="${row.address_en || ''}"
                                data-employee-id="${row.employee_id || ''}"
                                data-gov-id="${row.government_id || ''}"
                                data-city-id="${row.city_id || ''}"
                                data-whatsapp="${row.whatsapp || ''}"
                                data-email="${row.email || ''}"
                                data-lat="${row.lat || ''}"
                                data-lng="${row.lng || ''}"
                                data-status="${row.status || 'active'}"
                                data-image="${row.image_url || ''}">
                            <i class="icon-base bx bx-edit-alt"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-branch"
                                title="{{ __('Delete') }}"
                                data-id="${row.id}"
                                data-name="${row.name_ar}">
                            <i class="icon-base bx bx-trash"></i>
                        </button>
                    </div>`
            }
        ],
        order: [[0, 'asc']]
    });

    // Custom Filters Trigger Reload
    $('#filter_nursery_id, #filter_employee_id, #filter_government_id, #filter_status').on('change', function () {
        table.ajax.reload();
    });

    // Reset Filters Handler
    $('#btnResetFilters').on('click', function () {
        $('#filter_nursery_id').val('');
        $('#filter_employee_id').val('');
        $('#filter_government_id').val('');
        $('#filter_status').val('');
        table.ajax.reload();
    });

    // Handle Edit Drawer Population
    const editOffcanvasEl = document.getElementById('offcanvasEditBranch');
    const editForm = document.getElementById('editBranchForm');

    $(document).on('click', '.btn-edit-branch', function () {
        const id = $(this).data('id');
        editForm.action = baseUrl + '/' + id;

        document.getElementById('edit_nursery_id').value = $(this).data('nursery-id') || '';
        document.getElementById('edit_name_ar').value = $(this).data('name-ar') || '';
        document.getElementById('edit_name_en').value = $(this).data('name-en') || '';
        document.getElementById('edit_description_ar').value = $(this).data('desc-ar') || '';
        document.getElementById('edit_description_en').value = $(this).data('desc-en') || '';
        document.getElementById('edit_address_ar').value = $(this).data('address-ar') || '';
        document.getElementById('edit_address_en').value = $(this).data('address-en') || '';
        document.getElementById('edit_employee_id').value = $(this).data('employee-id') || '';
        
        const govId = $(this).data('gov-id') || '';
        const cityId = $(this).data('city-id') || '';
        document.getElementById('edit_government_id').value = govId;
        loadCities(govId, '#edit_city_id', cityId);

        document.getElementById('edit_whatsapp').value = $(this).data('whatsapp') || '';
        document.getElementById('edit_email').value = $(this).data('email') || '';
        document.getElementById('edit_lat').value = $(this).data('lat') || '';
        document.getElementById('edit_lng').value = $(this).data('lng') || '';

        const status = $(this).data('status') || 'active';
        if (status === 'active') {
            document.getElementById('statusActiveEdit').checked = true;
        } else {
            document.getElementById('statusInactiveEdit').checked = true;
        }

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
    const deleteModalEl = document.getElementById('deleteBranchModal');
    const deleteForm = document.getElementById('deleteBranchForm');
    const deleteTargetName = document.getElementById('delete_target_name');

    $(document).on('click', '.btn-delete-branch', function () {
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
});
</script>
@endsection
